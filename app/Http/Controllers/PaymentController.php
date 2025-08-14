<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Transaction;
use Illuminate\Support\Facades\DB;


class PaymentController extends Controller
{
    public function index()
    {
        // Subquery: ambil id booking terakhir per customer
        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
            FROM tb_bookings
            WHERE is_deleted = 0
            GROUP BY customer_id) lb
        ");

        $payments = DB::table('tb_payments as p')
            ->leftJoin('tb_customers as c', 'c.id', '=', 'p.customer_id')
            ->leftJoin($latestBookingSub, 'lb.customer_id', '=', 'p.customer_id')
            ->leftJoin('tb_bookings as b', 'b.id', '=', 'lb.last_booking_id')
            ->where('p.is_deleted', 0)
            ->orderByDesc('p.id')
            ->selectRaw("
                p.id,
                p.method,
                p.created_at,
                c.id   as customer_id,
                c.name as customer_name,
                c.email as customer_email,
                b.id   as booking_id,
                b.booking_ref,
                b.start_date,
                b.end_date,
                b.status as status
            ")
            ->get();

            // return response()->json([
            //     'data' => $payments,
            // ]);

        // NB: di Blade, akses ->customer_name (bukan ->customer->name), dan ->status dari join booking
        return view('module.payment.index', compact('payments'));
    }



    /**
     * Show detail payment (berbasis Payment ID), tampilkan status + receipt dari Midtrans.
     */
    public function show($id)
    {
        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
            FROM tb_bookings
            WHERE is_deleted = 0
            GROUP BY customer_id) lb
        ");

        $row = DB::table('tb_payments as p')
            ->leftJoin('tb_customers as c', 'c.id', '=', 'p.customer_id')
            ->leftJoin($latestBookingSub, 'lb.customer_id', '=', 'p.customer_id')
            ->leftJoin('tb_bookings as b', 'b.id', '=', 'lb.last_booking_id')
            ->where('p.is_deleted', 0)
            ->where('p.id', $id)
            ->selectRaw("
                p.id as payment_id,
                p.method,
                p.created_at,
                c.id as customer_id,
                c.name as customer_name,
                c.email as customer_email,
                b.id as booking_id,
                b.booking_ref,
                b.status as status,
                b.start_date,
                b.end_date
            ")
            ->first();

        if (!$row) {
            return back()->withErrors('Payment tidak ditemukan.');
        }

        // Konfigurasi Midtrans
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $midtrans = null;
        $receiptUrl = null;

        if (!empty($row->booking_ref)) {
            try {
                $midtrans = Transaction::status($row->booking_ref);

                // Map status Midtrans -> status booking kita
                $map = [
                    'capture'    => 'success',
                    'settlement' => 'success',
                    'pending'    => 'pending',
                    'deny'       => 'failed',
                    'cancel'     => 'failed',
                    'expire'     => 'failed',
                    'failure'    => 'failed',
                ];
                $new = $map[$midtrans->transaction_status ?? ''] ?? $row->status;

                // Sinkron ke DB jika berubah
                if (!empty($row->booking_id) && $new && $new !== $row->status) {
                    DB::table('tb_bookings')
                        ->where('id', $row->booking_id)
                        ->update(['status' => $new, 'updated_at' => now()]);
                    $row->status = $new; // update di objek untuk tampilan
                }

                // receipt (kalau tersedia)
                $receiptUrl = $midtrans->pdf_url ?? null;

            } catch (\Throwable $e) {
                Log::warning('Midtrans status fetch failed', [
                    'booking_ref' => $row->booking_ref ?? null,
                    'error'       => $e->getMessage()
                ]);
            }
        }

        // Kirim satu objek $payment (row) + midtrans + receiptUrl ke view show
        return view('module.payment.show', [
            'payment'    => $row,
            'midtrans'   => $midtrans,
            'receiptUrl' => $receiptUrl,
        ]);
    }


    /**
     * Alias opsional: jika ada route yang memanggil dengan Booking ID
     * /bookings/{id}/payment -> arahkan ke show() berbasis Payment ID jika ada.
     */
    public function showPayment($bookingId)
    {
        $booking = Booking::with('customer')->where('is_deleted', false)->findOrFail($bookingId);

        // Cari payment yang terkait booking ini (butuh booking_id di payments)
        $payment = Payment::with('customer')
            ->where('is_deleted', false)
            ->where('booking_id', $booking->id)
            ->latest('id')
            ->first();

        // Kalau belum ada relasi langsung, masih bisa tampil status dari booking
        // tapi tombol "Receipt" mungkin tidak ada.
        // Lanjut panggil logika yang sama seperti di show():

        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized  = true; Config::$is3ds = true;

        $midtrans = null;
        try {
            $midtrans = Transaction::status($booking->booking_ref);
            $map = [
                'capture'=>'success','settlement'=>'success','pending'=>'pending',
                'deny'=>'failed','cancel'=>'failed','expire'=>'failed','failure'=>'failed',
            ];
            $new = $map[$midtrans->transaction_status ?? ''] ?? $booking->status;
            if ($booking->status !== $new) $booking->update(['status' => $new]);
        } catch (\Throwable $e) {
            Log::warning('Midtrans status fetch failed', ['booking_ref'=>$booking->booking_ref, 'error'=>$e->getMessage()]);
        }

        return view('module.payment.show', compact('payment', 'booking', 'midtrans'));
    }

    /**
     * Refresh status (berbasis Payment ID) — sinkron dari Midtrans.
     */
    public function refreshStatus($id)
    {
        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
            FROM tb_bookings
            WHERE is_deleted = 0
            GROUP BY customer_id) lb
        ");

        $row = DB::table('tb_payments as p')
            ->leftJoin('tb_customers as c', 'c.id', '=', 'p.customer_id')
            ->leftJoin($latestBookingSub, 'lb.customer_id', '=', 'p.customer_id')
            ->leftJoin('tb_bookings as b', 'b.id', '=', 'lb.last_booking_id')
            ->where('p.is_deleted', 0)
            ->where('p.id', $id)
            ->selectRaw("p.id as payment_id, b.id as booking_id, b.booking_ref, b.status")
            ->first();

        if (!$row || empty($row->booking_ref)) {
            return back()->withErrors('Booking terkait payment ini tidak ditemukan.');
        }

        // Midtrans config
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized  = true; Config::$is3ds = true;

        try {
            $resp = Transaction::status($row->booking_ref);

            $map = [
                'capture'=>'success','settlement'=>'success','pending'=>'pending',
                'deny'=>'failed','cancel'=>'failed','expire'=>'failed','failure'=>'failed',
            ];
            $new = $map[$resp->transaction_status ?? ''] ?? $row->status;

            if (!empty($row->booking_id) && $new && $new !== $row->status) {
                DB::table('tb_bookings')
                    ->where('id', $row->booking_id)
                    ->update(['status' => $new, 'updated_at' => now()]);
            }

            return back()->with('success', "Status diperbarui: {$new}");
        } catch (\Throwable $e) {
            Log::error('Refresh status failed', [
                'payment_id'  => $id,
                'booking_ref' => $row->booking_ref,
                'error'       => $e->getMessage()
            ]);
            return back()->withErrors('Gagal mengambil status dari Midtrans.');
        }
    }

}
