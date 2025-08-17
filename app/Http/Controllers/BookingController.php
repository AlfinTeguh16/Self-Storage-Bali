<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
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

class BookingController extends Controller
{


    /**
     * Display a listing of the bookings.
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
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $customers = DB::table('tb_customers')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id','name']);

        // ambil storage available dari storage_management
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
                's.price',
            ]);

        return view('module.booking.create', [
            'customers' => $customers,
            'availableStorages' => $available,
        ]);
    }


    public function store(Request $request)
    {
        try {
            Log::info('Start storing booking', ['request' => $request->all()]);

            // 1) Validasi input
            $validated = $request->validate([
                'customer_id' => 'required|exists:tb_customers,id',
                'sm_id'       => 'required|exists:tb_storage_management,id',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
                'notes'       => 'nullable|string',
            ]);
            Log::info('Validation passed for booking', ['validated' => $validated]);

            // 2) Transaksi DB: kunci SM, buat booking, tandai booked
            DB::beginTransaction();

            /** @var \App\Models\StorageManagement $sm */
            $sm = StorageManagement::where('id', $validated['sm_id'])
                ->where('is_deleted', 0)
                ->lockForUpdate()
                ->firstOrFail();

            if ($sm->status !== 'available' || !is_null($sm->booking_id)) {
                DB::rollBack();
                throw new \Exception('Storage already exists, choose another one.');
            }

            $bookingRef = $this->generateBookingRef();

            /** @var \App\Models\Booking $booking */
            $booking = Booking::create([
                'customer_id' => $validated['customer_id'],
                'storage_id'  => $sm->storage_id,
                'booking_ref' => $bookingRef,
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'],
                'notes'       => $validated['notes'] ?? null,
                'status'      => 'pending',
            ]);

            // Tandai SM sebagai booked
            $sm->update([
                'booking_id' => $booking->id,
                'status'     => 'booked',
            ]);

            DB::commit();

            Log::info('Booking created', ['booking_id' => $booking->id, 'booking_ref' => $booking->booking_ref]);
            Log::info('Storage management updated to booked', ['sm_id' => $sm->id, 'storage_id' => $sm->storage_id]);

            // 3) Panggil Midtrans (setelah commit)
            $customer = Customer::findOrFail($validated['customer_id']);
            $amount   = $this->calculateTotalAmount($booking->storage_id);

            // Midtrans config
            Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $booking->booking_ref,
                    'gross_amount' => $amount,
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

                /**
                 * 4) BUAT DATA DI tb_payments
                 *    Minimal isi: customer_id, method. Sisipkan kolom lain jika tersedia.
                 *    Menggunakan raw insert supaya tidak tergantung $fillable.
                 */
                $paymentData = [
                    'customer_id' => $customer->id,
                    'method'      => 'midtrans',
                    'is_deleted'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                // Opsional: linkkan jika kolom ada
                if (Schema::hasColumn('tb_payments', 'booking_id')) {
                    $paymentData['booking_id'] = $booking->id;
                }
                if (Schema::hasColumn('tb_payments', 'status')) {
                    $paymentData['status'] = 'pending';
                }
                if (Schema::hasColumn('tb_payments', 'payment_url')) {
                    $paymentData['payment_url'] = $paymentUrl;
                }
                if (Schema::hasColumn('tb_payments', 'midtrans_order_id')) {
                    $paymentData['midtrans_order_id'] = $booking->booking_ref;
                }

                DB::table('tb_payments')->insert($paymentData);
                Log::info('Payment row created', ['booking_id' => $booking->id, 'customer_id' => $customer->id]);

                // 5) Kirim email link pembayaran
                try {
                    Log::info('Sending payment email', ['email' => $customer->email]);
                    Mail::to($customer->email)->send(new \App\Mail\PaymentEmail($paymentUrl));
                    Log::info('Payment email sent successfully');
                } catch (\Throwable $mailEx) {
                    Log::error('Failed to send payment email', ['error' => $mailEx->getMessage()]);
                }

            } catch (\Throwable $midEx) {
                Log::error('Midtrans createTransaction failed', ['error' => $midEx->getMessage()]);

                // Revert state: set booking failed & lepas storage
                try {
                    DB::transaction(function () use ($booking) {
                        $booking->update(['status' => 'failed']);
                        StorageManagement::where('storage_id', $booking->storage_id)
                            ->where('booking_id', $booking->id)
                            ->update(['booking_id' => null, 'status' => 'available']);
                    });
                    Log::info('Reverted booking & storage after Midtrans failure', ['booking_id' => $booking->id]);
                } catch (\Throwable $revertEx) {
                    Log::error('Failed to revert after Midtrans failure', ['error' => $revertEx->getMessage()]);
                }

                return back()->withErrors('Failed to create payment transaction: '.$midEx->getMessage())->withInput();
            }

            return redirect()
                ->route('data-booking.index')
                ->with('success', 'Booking created successfully. Payment link has been sent to the customer\'s email.');

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error storing booking', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors($e->getMessage())->withInput();
        }
    }




    // Fungsi untuk generate booking ref
    private function generateBookingRef()
    {
        $prefix = 'BK-' . now()->format('Ymd');
        $last = Booking::whereDate('created_at', today())
            ->orderByDesc('id')->value('booking_ref');

        $newNumber = '0001';
        if ($last && str_starts_with($last, $prefix.'-')) {
            $lastNum = (int) substr($last, -4);
            $newNumber = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '-' . $newNumber;
    }

    // Fungsi untuk menghitung total amount, bisa diubah sesuai kebutuhan
    private function calculateTotalAmount($storage_id)
    {
        $storage = Storage::findOrFail($storage_id);
        return $storage->price; // bisa tambahkan biaya lain
    }


    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['customer','storage'])
            ->where('is_deleted', false)
            ->findOrFail($id);



        return view('module.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
   public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        $customers = DB::table('tb_customers')
            ->where('is_deleted', 0)
            ->orderBy('name')
            ->get(['id','name']);

        // SM saat ini (yang terikat ke booking)
        $currentSm = DB::table('tb_storage_management')
            ->where('storage_id', $booking->storage_id)
            ->where('is_deleted', 0)
            ->first();

        // Daftar storage available + sematkan current SM agar tetap muncul
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
     * Update the specified booking.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Start updating booking', ['id' => $id, 'request' => $request->all()]);

            // Validasi input
            $validated = $request->validate([
                'customer_id' => ['required','exists:tb_customers,id'],
                'sm_id'       => ['required','exists:tb_storage_management,id'],
                'start_date'  => ['required','date'],
                'end_date'    => ['required','date','after_or_equal:start_date'],
                'notes'       => ['nullable','string'],
            ]);
            Log::info('Validation passed for booking update', ['id' => $id, 'validated' => $validated]);

            // Jalankan dalam transaksi
            return DB::transaction(function () use ($id, $validated) {

                // Ambil booking
                /** @var \App\Models\Booking $booking */
                $booking = Booking::lockForUpdate()->findOrFail($id);

                // SM yang sedang terikat ke booking saat ini (berdasarkan storage_id booking)
                $currentSm = StorageManagement::where('storage_id', $booking->storage_id)
                    ->where('is_deleted', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$currentSm) {
                    Log::warning('Current storage management not found for booking', [
                        'booking_id' => $booking->id,
                        'storage_id' => $booking->storage_id
                    ]);
                    throw new \Exception('Storage management saat ini tidak ditemukan.');
                }

                // Jika SM tidak berubah → cukup update field booking lain
                if ((int)$validated['sm_id'] === (int)$currentSm->id) {
                    $booking->update([
                        'customer_id' => $validated['customer_id'],
                        'start_date'  => $validated['start_date'],
                        'end_date'    => $validated['end_date'],
                        'notes'       => $validated['notes'] ?? null,
                    ]);

                    Log::info('Booking updated without changing storage', ['booking_id' => $booking->id]);
                } else {
                    // Pindah ke SM baru: kunci baris SM baru, pastikan available
                    $newSm = StorageManagement::where('id', $validated['sm_id'])
                        ->where('is_deleted', 0)
                        ->lockForUpdate()
                        ->first();

                    if (!$newSm) {
                        throw new \Exception('Storage management baru tidak ditemukan.');
                    }

                    // Cek ketersediaan SM baru (harus available & belum terkait booking lain)
                    if ($newSm->status !== 'available' || !is_null($newSm->booking_id)) {
                        Log::warning('New storage not available', [
                            'sm_id'     => $newSm->id,
                            'status'    => $newSm->status,
                            'booking_id'=> $newSm->booking_id,
                        ]);
                        throw new \Exception('Storage baru tidak tersedia, silakan pilih yang lain.');
                    }

                    // 1) Lepaskan SM lama
                    $currentSm->update([
                        'booking_id' => null,
                        'status'     => 'available',
                    ]);

                    // 2) Kaitkan SM baru
                    $newSm->update([
                        'booking_id' => $booking->id,
                        'status'     => 'booked',
                    ]);

                    // 3) Update booking (pindahkan storage_id + field lain)
                    $booking->update([
                        'customer_id' => $validated['customer_id'],
                        'storage_id'  => $newSm->storage_id,     // pindahkan ke storage baru
                        // booking_ref dipertahankan
                        'start_date'  => $validated['start_date'],
                        'end_date'    => $validated['end_date'],
                        'notes'       => $validated['notes'] ?? null,
                    ]);

                    Log::info('Booking updated and storage switched', [
                        'booking_id'     => $booking->id,
                        'from_sm_id'     => $currentSm->id,
                        'to_sm_id'       => $newSm->id,
                        'to_storage_id'  => $newSm->storage_id,
                    ]);
                }

                return redirect()
                    ->route('data-booking.index')
                    ->with('success', 'Booking updated successfully.');
            });

        } catch (\Throwable $e) {
            Log::error('Error updating booking', ['id' => $id, 'code' => $e->getCode(), 'error' => $e->getMessage()]);
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Soft delete the specified booking.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Kunci row booking biar aman dari race condition
                $booking = Booking::where('is_deleted', false)
                    ->lockForUpdate()
                    ->findOrFail($id);

                // Tandai booking "deleted" (pakai flag)
                $booking->update(['is_deleted' => true]);

                // Update SEMUA storage management terkait jadi available
                // (batasi hanya yang bukan 'available' supaya hemat write)
                $booking->storageManagement()
                    ->where('status', '!=', 'available')
                    ->update([
                        'status' => 'available',
                        'booking_id' => null
                    ]);
            });

            return redirect()
                ->route('data-booking.index')
                ->with('success', 'Booking dihapus dan semua storage terkait diset available.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->withErrors('Booking tidak ditemukan atau sudah dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
