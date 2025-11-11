<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\PaymentEmail;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class NonAuthController extends Controller
{

  

    public function showBookingForm(Request $request)
    {
        // Parse & validasi tanggal dengan fallback aman
        try {
            $startDate = Carbon::parse($request->input('start_date', now()))->toDateString();
            $endDate   = Carbon::parse($request->input('end_date', now()->addDays(7)))->toDateString();
        } catch (\Exception $e) {
            $startDate = now()->toDateString();
            $endDate   = now()->addDays(7)->toDateString();
        }

        // Validasi opsional
        $request->validate([
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        // Ambil semua storage aktif
        $storages = Storage::availableForDisplay()
            ->get()
            ->map(function ($storage) use ($startDate, $endDate) {
                try {
                    $isAvailable = $storage->isAvailableBetween($startDate, $endDate);
                } catch (\Exception $e) {
                    Log::warning('Gagal cek ketersediaan storage', [
                        'storage_id' => $storage->id,
                        'error' => $e->getMessage()
                    ]);
                    $isAvailable = false;
                }

                $totalDays = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
                $totalPrice = $isAvailable ? $totalDays * $storage->price : null;

                return [
                    'id' => $storage->id,
                    'size' => $storage->size,
                    'price' => (int) $storage->price,
                    'description' => $storage->description ?? 'Storage siap pakai',
                    'is_available' => $isAvailable,
                    'total_days' => $totalDays,
                    'estimated_total' => $totalPrice,
                ];

               
            });

        return view('pages.customer-booking', compact('storages', 'startDate', 'endDate'));
    }

    /**
     * Menangani submit booking (POST /online-booking)
     */
    public function onlineBooking(Request $request)
    {
        // Validasi untuk pelanggan baru
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:tb_customers,email',
            'phone'       => 'required|string|max:20',
            'address'     => 'nullable|string',
            'storage_id'  => [
                'required',
                'exists:tb_storages,id',
                function ($attribute, $value, $fail) use ($request) {
                    $storage = Storage::find($value);
                    if (!$storage) {
                        return $fail('Storage tidak ditemukan.');
                    }

                    $start = $request->input('start_date');
                    $end   = $request->input('end_date');

                    try {
                        if (!$storage->isAvailableBetween($start, $end)) {
                            $fail('Storage ini tidak tersedia pada periode yang dipilih.');
                        }
                    } catch (\Throwable $e) {
                        $fail('Gagal memverifikasi ketersediaan storage.');
                    }
                }
            ],
            'start_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'required|date|after:start_date',
            'notes'       => 'nullable|string|max:500',
        ]);

        // Hitung durasi & harga
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDate = $start->diffInDays($end) + 1; // inclusif
        $storage = Storage::findOrFail($validated['storage_id']);
        $totalPrice = $totalDate * $storage->price;

        // Buat customer + booking dalam transaksi lalu commit sebelum panggil Midtrans
        try {
            $booking = DB::transaction(function () use ($validated, $totalDate, $totalPrice) {
                // Buat customer
                $customer = Customer::create([
                    'name'        => $validated['name'],
                    'address'     => $validated['address'] ?? null,
                    'email'       => $validated['email'],
                    'phone'       => $validated['phone'],
                    'is_deleted'  => false,
                ]);

                // Generate booking ref (pastikan trait/service sudah tersedia)
                $bookingRef = $this->generateBookingRef();

                // Buat booking
                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'storage_id'  => $validated['storage_id'],
                    'booking_ref' => $bookingRef,
                    'start_date'  => $validated['start_date'],
                    'end_date'    => $validated['end_date'],
                    'total_date'  => $totalDate,
                    'total_price' => $totalPrice,
                    'status'      => 'pending',
                    'is_deleted'  => false,
                ]);

                // return array with booking and customer for use after commit
                return [
                    'booking'  => $booking,
                    'customer' => $customer,
                ];
            });

            // Setelah commit: panggil Midtrans
            $bookingModel = $booking['booking'];
            $customerModel = $booking['customer'];
            $amount = $totalPrice; // sudah dihitung sebelumnya

            // Konfigurasi Midtrans
            Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $bookingModel->booking_ref,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => $customerModel->name,
                    'email'      => $customerModel->email,
                    'phone'      => $customerModel->phone ?? null,
                ],
            ];

            try {
                $tx = Snap::createTransaction($params);
                $paymentUrl = $tx->redirect_url ?? null;

                if (!$paymentUrl) {
                    throw new \Exception('Midtrans did not return redirect_url.');
                }

                // Simpan row di tb_payments (flexible dengan Schema::hasColumn checks)
                $paymentData = [
                    'customer_id' => $customerModel->id,
                    'method'      => 'midtrans',
                    'is_deleted'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                if (Schema::hasColumn('tb_payments', 'booking_id')) {
                    $paymentData['booking_id'] = $bookingModel->id;
                }
                if (Schema::hasColumn('tb_payments', 'status')) {
                    $paymentData['status'] = 'pending';
                }
                if (Schema::hasColumn('tb_payments', 'payment_url')) {
                    $paymentData['payment_url'] = $paymentUrl;
                }
                if (Schema::hasColumn('tb_payments', 'midtrans_order_id')) {
                    $paymentData['midtrans_order_id'] = $bookingModel->booking_ref;
                }

                DB::table('tb_payments')->insert($paymentData);
                Log::info('Payment row created (onlineBooking)', ['booking_id' => $bookingModel->id, 'customer_id' => $customerModel->id]);

                // Kirim email berisi link pembayaran (tidak blocking flow)
                try {
                    Mail::to($customerModel->email)->send(new PaymentEmail($paymentUrl));
                } catch (\Throwable $mailEx) {
                    Log::error('Failed to send payment email (onlineBooking): '.$mailEx->getMessage());
                }

                // Kembalikan JSON sukses dengan link pembayaran
                return response()->json([
                    'message' => 'Booking berhasil dibuat. Silakan lanjut ke pembayaran.',
                    'booking_id' => $bookingModel->id,
                    'booking_ref' => $bookingModel->booking_ref,
                    'payment_url' => $paymentUrl,
                ], 201);

            } catch (\Throwable $midEx) {
                Log::error('Midtrans createTransaction failed (onlineBooking): '.$midEx->getMessage());

                // Jika Midtrans gagal, tandai booking 'failed' dan beri notifikasi, lepas resource jika perlu
                try {
                    DB::transaction(function () use ($bookingModel) {
                        $bookingModel->update(['status' => 'failed']);
                        // jika kamu menggunakan StorageManagement atau resource allocation,
                        // lakukan revert di sini (contoh jika ada StorageManagement table)
                        if (class_exists(\App\Models\StorageManagement::class)) {
                            \App\Models\StorageManagement::where('storage_id', $bookingModel->storage_id)
                                ->where('booking_id', $bookingModel->id)
                                ->update(['booking_id' => null, 'status' => 'available']);
                        }
                    });
                    Log::info('Reverted booking state after Midtrans failure (onlineBooking)', ['booking_id' => $bookingModel->id]);
                } catch (\Throwable $revertEx) {
                    Log::error('Failed to revert booking after Midtrans failure: '.$revertEx->getMessage());
                }

                return response()->json([
                    'message' => 'Gagal membuat transaksi pembayaran: ' . $midEx->getMessage()
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error('Error in onlineBooking: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


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

    /**
     * Endpoint untuk sukses (opsional)
     */
    public function bookingSuccess($bookingId)
    {
        $booking = Booking::with(['customer', 'storage'])
            ->where('id', $bookingId)
            ->firstOrFail();

        return view('pages.booking-success', compact('booking'));
    }

    /**
     * Alias route (opsional)
     */
    public function showAvailableStorage(Request $request)
    {
        return $this->showBookingForm($request);
    }
}