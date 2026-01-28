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
        // Validasi untuk pelanggan baru atau existing
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
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

        // Wrap EVERYTHING in a single transaction - including Midtrans call
        // If Midtrans fails, all will be rolled back
        DB::beginTransaction();
        
        try {
            // Check if customer already exists by email
            $customer = Customer::where('email', $validated['email'])->first();
            $isNewCustomer = false;
            
            if (!$customer) {
                // Buat customer baru
                $customer = Customer::create([
                    'name'        => $validated['name'],
                    'address'     => $validated['address'] ?? null,
                    'email'       => $validated['email'],
                    'phone'       => $validated['phone'],
                    'is_deleted'  => false,
                ]);
                $isNewCustomer = true;
            } else {
                // Update customer data jika sudah ada
                $customer->update([
                    'name'    => $validated['name'],
                    'phone'   => $validated['phone'],
                    'address' => $validated['address'] ?? $customer->address,
                ]);
            }

            // Generate unique booking ref dengan timestamp microseconds
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

            // Konfigurasi Midtrans
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $bookingRef,
                    'gross_amount' => $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email'      => $customer->email,
                    'phone'      => $customer->phone ?? null,
                ],
            ];

            // Call Midtrans - if this fails, entire transaction will be rolled back
            $tx = Snap::createTransaction($params);
            $paymentUrl = $tx->redirect_url ?? null;

            if (!$paymentUrl) {
                throw new \Exception('Midtrans did not return redirect_url.');
            }

            // Simpan row di tb_payments
            $paymentData = [
                'customer_id' => $customer->id,
                'method'      => 'midtrans',
                'is_deleted'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

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
                $paymentData['midtrans_order_id'] = $bookingRef;
            }

            DB::table('tb_payments')->insert($paymentData);
            Log::info('Payment row created (onlineBooking)', ['booking_id' => $booking->id, 'customer_id' => $customer->id]);

            // COMMIT transaction - only if Midtrans was successful
            DB::commit();

            // Kirim email berisi link pembayaran ke halaman mock payment
            try {
                $mockPaymentUrl = route('payment.page', ['bookingId' => $booking->id]);
                Mail::to($customer->email)->send(new PaymentEmail($mockPaymentUrl));
            } catch (\Throwable $mailEx) {
                Log::error('Failed to send payment email (onlineBooking): '.$mailEx->getMessage());
            }

            // Redirect ke halaman sukses
            return redirect()->route('booking.success', ['bookingId' => $booking->id]);

        } catch (\Throwable $e) {
            // ROLLBACK - hapus semua data yang sudah dibuat
            DB::rollBack();
            
            Log::error('Error in onlineBooking: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            // Return error message yang lebih user-friendly
            $errorMessage = 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.';
            
            if (str_contains($e->getMessage(), 'order_id sudah digunakan')) {
                $errorMessage = 'Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.';
            } elseif (str_contains($e->getMessage(), 'Midtrans')) {
                $errorMessage = 'Gagal menghubungi layanan pembayaran. Silakan coba beberapa saat lagi.';
            }
            
            return back()->withErrors(['booking' => $errorMessage])->withInput();
        }
    }



    private function generateBookingRef()
    {
        // Use microseconds to ensure uniqueness even for rapid requests
        $timestamp = now()->format('YmdHis') . substr(microtime(), 2, 4);
        $random = strtoupper(substr(uniqid(), -4));
        
        return 'BK-' . $timestamp . '-' . $random;
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

    /**
     * Show payment selection page (mock Midtrans-like page)
     */
    public function showPaymentPage($bookingId)
    {
        $booking = Booking::with(['customer', 'storage'])
            ->where('id', $bookingId)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('pages.payment', compact('booking'));
    }

    /**
     * Show payment receipt page
     */
    public function showReceipt($bookingId)
    {
        $booking = Booking::with(['customer', 'storage'])
            ->where('id', $bookingId)
            ->firstOrFail();

        return view('pages.receipt', compact('booking'));
    }
}