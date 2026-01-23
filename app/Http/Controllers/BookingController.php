<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Booking;
use App\Models\Storage;
use App\Models\Customer;
use App\Mail\PaymentEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\StorageManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

/**
 * BookingController
 *
 * Mengelola siklus hidup pemesanan penyimpanan (booking):
 * - Penjadwalan booking dengan validasi ketersediaan storage
 * - Integrasi pembayaran Midtrans (Snap)
 * - Pembaruan status storage secara aman (menghindari race condition)
 * - Reversi otomatis saat gagal bayar
 * - Soft delete dengan pemulihan storage
 *
 * @note Semua operasi tulis ke storage_management dilindungi oleh lockForUpdate()
 *       untuk menjaga konsistensi data dalam lingkungan konkuren.
 */
class BookingController extends Controller
{
    // ┌──────────────────────────────────────────────────────────────────────┐
    // │ Public Methods (Routes)                                              │
    // └──────────────────────────────────────────────────────────────────────┘

    /**
     * Menampilkan daftar semua booking aktif (tidak dihapus).
     *
     * @return \Illuminate\View\View
     * @uses Booking::with('customer') untuk eager load relasi
     */
    public function index()
    {
        $bookings = Booking::with('customer')
            ->where('is_deleted', false)
            ->latest()
            ->get();

        return view('module.booking.index', compact('bookings'));
    }

    /**
     * Menyiapkan data untuk form pembuatan booking:
     * - Daftar customer aktif
     * - Daftar storage yang tersedia (status = 'available' & booking_id IS NULL)
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customers = DB::table('tb_customers')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Ambil storage yang benar-benar tersedia untuk booking baru
        $available = DB::table('tb_storage_management as sm')
        ->join('tb_storages as s', 's.id', '=', 'sm.storage_id')
        ->where('sm.is_deleted', 0)
        ->where('s.is_deleted', 0)
        ->where('sm.status', 'available')
        ->whereNull('sm.booking_id')
        ->orderBy('s.size')
        ->get([
            'sm.id as sm_id',
            'sm.storage_id',
            'sm.status',
            's.size',
            DB::raw('CAST(s.price AS UNSIGNED) as price'), 
            // atau pakai: DB::raw('s.price + 0 as price')
        ]);

        return view('module.booking.create', [
            'customers'         => $customers,
            'availableStorages' => $available,
        ]);
    }

    /**
     * Membuat booking baru dengan alur lengkap:
     * 1. Validasi input
     * 2. Lock & verifikasi ketersediaan storage
     * 3. Buat record booking
     * 4. Update status storage management → 'booked'
     * 5. Inisiasi pembayaran Midtrans
     * 6. Catat pembayaran ke tb_payments
     * 7. Kirim email pembayaran ke customer
     *
     * Jika gagal di tahap 5–7: rollback booking & kembalikan storage ke 'available'
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            Log::info('Start storing booking', ['request' => $request->all()]);

            $validated = $this->validateBookingRequest($request);
            Log::info('Validation passed for booking', ['validated' => $validated]);

            $booking = $this->createBookingWithStorageLock($validated);
            $paymentUrl = $this->initiateMidtransAndRecordPayment($booking);
            $this->sendPaymentEmail($booking->customer, $paymentUrl);

            return redirect()
                ->route('data-booking.index')
                ->with('success', 'Booking created successfully. Payment link has been sent to the customer.');

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors('Booking failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail booking beserta relasi: customer & storage.
     *
     * @param int $id ID booking
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika booking tidak ditemukan atau sudah dihapus
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'storage'])
            ->where('is_deleted', false)
            ->findOrFail($id);

        return view('module.booking.show', compact('booking'));
    }

    /**
     * Menyiapkan data untuk form edit booking:
     * - Booking yang akan diedit
     * - Daftar customer aktif
     * - Daftar storage yang tersedia + storage saat ini (agar tetap bisa dipilih)
     *
     * @param int $id ID booking
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        $customers = DB::table('tb_customers')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $currentSm = DB::table('tb_storage_management')
            ->where('storage_id', $booking->storage_id)
            ->where('is_deleted', 0)
            ->first();

        // Tampilkan storage available + SM saat ini (agar bisa dipilih ulang)
        $available = DB::table('tb_storage_management as sm')
            ->join('tb_storages as s', 's.id', '=', 'sm.storage_id')
            ->where('sm.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where(function ($q) use ($currentSm) {
                $q->where(function ($qq) {
                    $qq->where('sm.status', 'available')->whereNull('sm.booking_id');
                });
                if ($currentSm) {
                    $q->orWhere('sm.id', $currentSm->id);
                }
            })
            ->orderBy('s.size')
            ->get([
                'sm.id as sm_id', 'sm.status',
                's.id as storage_id', 's.size', 's.price'
            ]);

        return view('module.booking.edit', [
            'booking'           => $booking,
            'customers'         => $customers,
            'availableStorages' => $available,
            'currentSm'         => $currentSm,
        ]);
    }

    /**
     * Memperbarui booking yang sudah ada.
     * Dua skenario:
     * A. Storage tidak berubah → hanya update data booking & SM terkait
     * B. Storage berubah → lepas SM lama, kunci & alokasikan SM baru
     *
     * Semua operasi dilakukan dalam satu transaksi database untuk konsistensi.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Start updating booking', ['id' => $id, 'request' => $request->all()]);

            $validated = $request->validate([
                'customer_id' => ['required', 'exists:tb_customers,id'],
                'sm_id'       => ['required', 'exists:tb_storage_management,id'],
                'start_date'  => ['required', 'date'],
                'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
                'notes'       => ['nullable', 'string'],
            ]);
            Log::info('Validation passed for booking update', ['id' => $id, 'validated' => $validated]);

            return DB::transaction(function () use ($id, $validated) {
                // 🔒 Lock booking & SM terkait untuk hindari race condition
                $booking = Booking::lockForUpdate()->findOrFail($id);
                $currentSm = StorageManagement::where('storage_id', $booking->storage_id)
                    ->where('is_deleted', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$currentSm) {
                    throw new \Exception('Storage management saat ini tidak ditemukan.');
                }

                $totalDate = Carbon::parse($validated['start_date'])
                    ->diffInDays(Carbon::parse($validated['end_date'])) + 1;
                $storage = Storage::findOrFail($booking->storage_id);
                $totalPrice = $totalDate * $storage->price;

                // Skenario A: Storage tidak berubah
                if ((int) $validated['sm_id'] === (int) $currentSm->id) {
                    $booking->update([
                        'customer_id' => $validated['customer_id'],
                        'start_date'  => $validated['start_date'],
                        'end_date'    => $validated['end_date'],
                        'total_date'  => $totalDate,
                        'total_price' => $totalPrice,
                        'notes'       => $validated['notes'] ?? null,
                    ]);

                    $this->updateStorageManagementFields($currentSm, [
                        'customer_id' => $booking->customer_id,
                        'start_date'  => $booking->start_date,
                        'end_date'    => $booking->end_date,
                    ]);

                    Log::info('Booking & SM updated (same storage)', [
                        'booking_id' => $booking->id,
                        'total_date' => $totalDate,
                        'total_price' => $totalPrice,
                    ]);

                // Skenario B: Ganti storage
                } else {
                    $newSm = StorageManagement::where('id', $validated['sm_id'])
                        ->where('is_deleted', 0)
                        ->lockForUpdate()
                        ->first();

                    if (!$newSm || $newSm->status !== 'available' || !is_null($newSm->booking_id)) {
                        throw new \Exception('Storage baru tidak tersedia.');
                    }

                    $newStorage = Storage::findOrFail($newSm->storage_id);
                    $newTotalPrice = $totalDate * $newStorage->price;

                    // Lepaskan SM lama
                    $this->resetStorageManagement($currentSm);

                    // Kaitkan SM baru
                    $this->updateStorageManagementFields($newSm, [
                        'booking_id'  => $booking->id,
                        'status'      => 'booked',
                        'customer_id' => $validated['customer_id'],
                        'start_date'  => $validated['start_date'],
                        'end_date'    => $validated['end_date'],
                    ]);

                    // Update booking
                    $booking->update([
                        'customer_id' => $validated['customer_id'],
                        'storage_id'  => $newSm->storage_id,
                        'start_date'  => $validated['start_date'],
                        'end_date'    => $validated['end_date'],
                        'total_date'  => $totalDate,
                        'total_price' => $newTotalPrice,
                        'notes'       => $validated['notes'] ?? null,
                    ]);

                    Log::info('Booking updated & storage switched', [
                        'booking_id' => $booking->id,
                        'from_sm'    => $currentSm->id,
                        'to_sm'      => $newSm->id,
                    ]);
                }

                return redirect()
                    ->route('data-booking.index')
                    ->with('success', 'Booking updated successfully.');
            });

        } catch (\Throwable $e) {
            Log::error('Error updating booking', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors('Update failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete booking dan kembalikan semua storage terkait ke status 'available'.
     * Menggunakan transaksi untuk konsistensi.
     *
     * @param int $id ID booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $booking = Booking::where('is_deleted', false)
                    ->lockForUpdate()
                    ->findOrFail($id);

                $booking->update(['is_deleted' => true]);

                // Pastikan hanya update SM yang sedang 'booked' atau 'occupied'
                StorageManagement::where('booking_id', $booking->id)
                    ->whereIn('status', ['booked', 'occupied'])
                    ->update([
                        'status'     => 'available',
                        'booking_id' => null,
                        'customer_id' => null,
                        'start_date' => null,
                        'end_date'   => null,
                    ]);
            });

            return redirect()
                ->route('data-booking.index')
                ->with('success', 'Booking dihapus dan semua storage terkait diset available.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors('Booking tidak ditemukan atau sudah dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ┌──────────────────────────────────────────────────────────────────────┐
    // │ Private Helper Methods (Reusable Logic)                              │
    // └──────────────────────────────────────────────────────────────────────┘

    /**
     * Memvalidasi request untuk pembuatan/pembaruan booking.
     *
     * @param \Illuminate\Http\Request $request
     * @return array Data tervalidasi
     */
    private function validateBookingRequest(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:tb_customers,id',
            'sm_id'       => 'required|exists:tb_storage_management,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'notes'       => 'nullable|string',
        ]);
    }

    /**
     * Membuat booking baru dalam transaksi database, termasuk:
     * - Mengunci storage management terpilih
     - Memastikan status 'available'
     - Menghitung harga & durasi
     * - Menyimpan booking
     * - Memperbarui storage management
     *
     * @param array $validated Data tervalidasi
     * @return \App\Models\Booking Instance booking yang baru dibuat
     * @throws \Exception Jika storage tidak tersedia
     */
    private function createBookingWithStorageLock(array $validated): Booking
    {
        return DB::transaction(function () use ($validated) {
            $sm = $this->lockAndCheckStorageAvailability($validated['sm_id']);

            $bookingRef = $this->generateBookingRef();
            $totalDate  = $this->calculateTotalDate($validated['start_date'], $validated['end_date']);
            $totalPrice = $this->calculateTotalPrice($sm->storage_id, $totalDate);

            $booking = Booking::create([
                'customer_id' => $validated['customer_id'],
                'storage_id'  => $sm->storage_id,
                'booking_ref' => $bookingRef,
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'],
                'total_date'  => $totalDate,
                'total_price' => $totalPrice,
                'notes'       => $validated['notes'] ?? null,
                'status'      => 'pending',
            ]);

            $this->updateStorageManagement($sm, $booking);

            Log::info('Booking created', [
                'booking_id'   => $booking->id,
                'booking_ref'  => $booking->booking_ref,
                'sm_id'        => $sm->id,
            ]);

            return $booking;
        });
    }

    /**
     * Mengunci baris storage management dan memverifikasi ketersediaannya.
     *
     * @param int $smId ID storage management
     * @return \App\Models\StorageManagement
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika tidak ditemukan
     * @throws \Exception Jika status bukan 'available' atau sudah terbooking
     */
    private function lockAndCheckStorageAvailability(int $smId): StorageManagement
    {
        $sm = StorageManagement::where('id', $smId)
            ->where('is_deleted', 0)
            ->lockForUpdate()
            ->firstOrFail();

        if ($sm->status !== 'available' || !is_null($sm->booking_id)) {
            throw new \Exception('Storage is not available. Please choose another one.');
        }

        return $sm;
    }

    /**
     * Menghitung durasi sewa dalam hari (inklusif).
     *
     * Contoh: 2025-11-10 s/d 2025-11-12 → 3 hari
     *
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return int Jumlah hari
     */
    private function calculateTotalDate(string $startDate, string $endDate): int
    {
        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
    }

    /**
     * Menghitung total harga berdasarkan durasi dan harga storage.
     *
     * @param int $storageId ID storage
     * @param int $totalDate Jumlah hari
     * @return float|int Total harga
     */
    private function calculateTotalPrice(int $storageId, int $totalDate): float|int
    {
        $storage = Storage::findOrFail($storageId);
        return $totalDate * $storage->price;
    }

    /**
     * Memperbarui data storage management berdasarkan booking.
     * Kolom `last_clean` sengaja diabaikan (tidak boleh di-override saat booking).
     *
     * @param \App\Models\StorageManagement $sm Instance SM
     * @param \App\Models\Booking $booking Instance booking
     */
    private function updateStorageManagement(StorageManagement $sm, Booking $booking): void
    {
        $customer = Customer::findOrFail($booking->customer_id);

        $updateData = [
            'booking_id'   => $booking->id,
            'status'       => 'booked',
            'customer_id'  => $customer->id,
            'start_date'   => $booking->start_date,
            'end_date'     => $booking->end_date,
            // ❗ last_clean tidak di-update — biarkan nilai historis tetap
        ];

        $this->safeUpdateStorageManagement($sm, $updateData);
    }

    /**
     * Memperbarui kolom tertentu di storage management secara aman:
     * - Hanya update kolom yang benar-benar ada di tabel
     * - Selalu lewati kolom 'last_clean' (khusus logistik)
     *
     * @param \App\Models\StorageManagement $sm
     * @param array $data Data untuk di-update (key => value)
     */
    private function safeUpdateStorageManagement(StorageManagement $sm, array $data): void
    {
        $allowedColumns = Schema::getColumnListing('tb_storage_management');
        $filteredData = array_filter($data, function ($key) use ($allowedColumns) {
            return in_array($key, $allowedColumns) && $key !== 'last_clean';
        }, ARRAY_FILTER_USE_KEY);

        $sm->update($filteredData);
    }

    /**
     * Memperbarui hanya field-field tertentu pada storage management (tanpa booking_id/status).
     * Digunakan saat update booking tanpa ganti storage.
     *
     * @param \App\Models\StorageManagement $sm
     * @param array $fields Data parsial (misal: ['customer_id' => 5, 'start_date' => '...'])
     */
    private function updateStorageManagementFields(StorageManagement $sm, array $fields): void
    {
        $this->safeUpdateStorageManagement($sm, $fields);
    }

    /**
     * Mengembalikan storage management ke status awal ('available').
     * Digunakan saat:
     * - Ganti storage (lepas SM lama)
     * - Gagal bayar (revert)
     * - Soft delete booking
     *
     * @param \App\Models\StorageManagement $sm
     */
    private function resetStorageManagement(StorageManagement $sm): void
    {
        $this->safeUpdateStorageManagement($sm, [
            'booking_id'   => null,
            'status'       => 'available',
            'customer_id'  => null,
            'start_date'   => null,
            'end_date'     => null,
        ]);
    }

    /**
     * Menginisiasi transaksi Midtrans, menyimpan data pembayaran, dan mengembalikan URL pembayaran.
     *
     * @param \App\Models\Booking $booking
     * @return string URL redirect pembayaran
     * @throws \Throwable Jika Midtrans gagal — akan memicu rollback
     */
    private function initiateMidtransAndRecordPayment(Booking $booking): string
    {
        $customer = $booking->customer;

        // 🔑 Konfigurasi Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->booking_ref,
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $customer->name,
                'email'      => $customer->email,
            ],
        ];

        try {
            $tx = Snap::createTransaction($params);
            $paymentUrl = $tx->redirect_url ?? null;

            if (!$paymentUrl) {
                throw new \Exception('Midtrans did not return redirect_url.');
            }

            Log::info('Midtrans transaction created', [
                'order_id' => $booking->booking_ref,
                'redirect_url' => $paymentUrl,
            ]);

            $this->recordPayment($booking, $customer, $paymentUrl);
            return $paymentUrl;

        } catch (\Throwable $midEx) {
            Log::error('Midtrans failed', ['error' => $midEx->getMessage()]);
            $this->revertBookingAndStorage($booking);
            throw $midEx;
        }
    }

    /**
     * Mencatat transaksi pembayaran ke tabel `tb_payments`.
     * Dinamis: hanya isi kolom yang tersedia (aman untuk migrasi masa depan).
     *
     * @param \App\Models\Booking $booking
     * @param \App\Models\Customer $customer
     * @param string $paymentUrl
     */
    private function recordPayment(Booking $booking, Customer $customer, string $paymentUrl): void
    {
        $paymentData = [
            'customer_id' => $customer->id,
            'method'      => 'midtrans',
            'is_deleted'  => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        // Dinamis: hanya insert kolom yang benar-benar ada
        $columns = Schema::getColumnListing('tb_payments');
        $insertData = array_filter($paymentData, fn($value, $key) => in_array($key, $columns), ARRAY_FILTER_USE_BOTH);

        // Tambahkan kolom opsional jika tersedia
        if (in_array('booking_id', $columns)) {
            $insertData['booking_id'] = $booking->id;
        }
        if (in_array('status', $columns)) {
            $insertData['status'] = 'pending';
        }
        if (in_array('payment_url', $columns)) {
            $insertData['payment_url'] = $paymentUrl;
        }
        if (in_array('midtrans_order_id', $columns)) {
            $insertData['midtrans_order_id'] = $booking->booking_ref;
        }

        DB::table('tb_payments')->insert($insertData);
        Log::info('Payment row created', ['booking_id' => $booking->id]);
    }

    /**
     * Membatalkan booking & mengembalikan storage ke status 'available' saat gagal bayar.
     * Dipanggil hanya saat Midtrans gagal (bukan error validasi/input).
     *
     * @param \App\Models\Booking $booking
     */
    private function revertBookingAndStorage(Booking $booking): void
    {
        try {
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'failed']);

                $sm = StorageManagement::where('booking_id', $booking->id)
                    ->where('is_deleted', 0)
                    ->first();

                if ($sm) {
                    $this->resetStorageManagement($sm);
                }
            });

            Log::info('Reverted storage after Midtrans failure', ['booking_id' => $booking->id]);
        } catch (\Throwable $revertEx) {
            Log::error('Revert failed', ['error' => $revertEx->getMessage()]);
            // ❗ Tetap lempar error Midtrans asli — ini hanya logging error sekunder
        }
    }

    /**
     * Mengirim email pembayaran ke customer.
     * Gagal kirim email **tidak membatalkan** booking (non-blocking).
     *
     * @param \App\Models\Customer $customer
     * @param string $paymentUrl
     */
    private function sendPaymentEmail(Customer $customer, string $paymentUrl): void
    {
        try {
            Mail::to($customer->email)->send(new PaymentEmail($paymentUrl));
            Log::info('Payment email sent', ['to' => $customer->email]);
        } catch (\Throwable $mailEx) {
            Log::error('Failed to send payment email', [
                'to'    => $customer->email,
                'error' => $mailEx->getMessage(),
            ]);
        }
    }

    /**
     * Menghasilkan referensi booking unik dengan format:
     * BK-YYYYMMDD-NNNN (contoh: BK-20251117-0042)
     *
     * Nomor urut di-reset tiap hari.
     *
     * @return string Booking reference
     */
    private function generateBookingRef(): string
    {
        $prefix = 'BK-' . now()->format('Ymd');
        $last = Booking::whereDate('created_at', today())
            ->where('booking_ref', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->value('booking_ref');

        $newNumber = 1;
        if ($last) {
            $lastNum = (int) substr($last, -4);
            $newNumber = $lastNum + 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }


    /**
     * Mengakhiri booking secara manual (misalnya setelah masa sewa berakhir).
     * - Hanya untuk booking dengan status 'success'
     * - Melepas storage management: hapus booking_id, set status = 'available'
     * - Opsional: bisa tambah kolom ended_at jika diperlukan
     */
    public function endBooking($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Ambil booking dengan lock untuk hindari race condition
                $booking = Booking::where('is_deleted', false)
                    ->where('status', 'success') // hanya booking yang sudah dibayar
                    ->lockForUpdate()
                    ->findOrFail($id);

                // Cari storage management terkait
                $sm = StorageManagement::where('booking_id', $booking->id)
                    ->where('is_deleted', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$sm) {
                    throw new \Exception('Storage management terkait booking tidak ditemukan.');
                }

                // Validasi: pastikan benar-benar terikat ke booking ini
                if ((int) $sm->booking_id !== (int) $booking->id) {
                    throw new \Exception('Storage management tidak sesuai dengan booking.');
                }

                // Update storage management: lepas dari booking
                $sm->update([
                    'booking_id'   => null,
                    'status'       => 'available',
                    'customer_id'  => null,
                    'start_date'   => null,
                    'end_date'     => null,
                    // 'last_clean' tetap dipertahankan — tidak direset
                ]);

                // Opsional: tandai waktu akhir booking (jika ada kolom `ended_at`)
                // $booking->update(['ended_at' => now()]);

                Log::info('Booking ended and storage released', [
                    'booking_id' => $booking->id,
                    'sm_id'      => $sm->id,
                ]);
            });

            return redirect()
                ->back()
                ->with('success', 'Booking berhasil diakhiri dan storage dikembalikan ke status available.');

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->withErrors('Booking tidak ditemukan atau belum dibayar (status harus "success").');
        } catch (\Exception $e) {
            Log::error('Gagal mengakhiri booking', [
                'booking_id' => $id,
                'error'      => $e->getMessage(),
            ]);
            return redirect()
                ->back()
                ->withErrors('Gagal mengakhiri booking: ' . $e->getMessage());
        }
    }
}