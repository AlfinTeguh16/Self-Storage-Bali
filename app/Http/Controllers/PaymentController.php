<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Transaction;

/**
 * PaymentController
 *
 * Mengelola tampilan dan sinkronisasi data pembayaran:
 * - Daftar pembayaran dengan join ke customer & booking terbaru
 * - Penampilan detail pembayaran + status Midtrans
 * - Refresh status pembayaran dari Midtrans API
 * - Dukungan akses via Payment ID maupun Booking ID
 *
 * Catatan penting:
 * - Kolom `booking_id` di tabel `tb_payments` bersifat opsional (belum semua record terisi)
 * - Status booking disinkronkan dari Midtrans hanya jika `booking_ref` tersedia
 * - Midtrans SDK bisa mengembalikan `array` atau `object` tergantung konfigurasi — ditangani secara eksplisit
 */
class PaymentController extends Controller
{
    /**
     * Menampilkan daftar semua pembayaran aktif (tidak dihapus).
     *
     * Menggunakan subquery untuk mengambil hanya booking terakhir per customer,
     * sehingga kolom status booking yang ditampilkan adalah yang terkini.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Subquery: ambil ID booking terakhir per customer (hanya yang aktif)
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

        return view('module.payment.index', compact('payments'));
    }

    /**
     * Menampilkan detail pembayaran berdasarkan Payment ID.
     *
     * Melakukan:
     * 1. Query data pembayaran + customer + booking terakhir
     * 2. Sinkronisasi status booking dari Midtrans (jika booking_ref tersedia)
     * 3. Ekstraksi URL receipt PDF (jika tersedia)
     *
     * @param int $id ID pembayaran (`tb_payments.id`)
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
             FROM tb_bookings
             WHERE is_deleted = 0
             GROUP BY customer_id) lb
        ");

        $paymentData = DB::table('tb_payments as p')
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

        if (!$paymentData) {
            return back()->withErrors('Payment tidak ditemukan.');
        }

        // Inisialisasi Midtrans
        $this->configureMidtrans();

        $midtransResponse = null;
        $receiptUrl = null;

        // Hanya proses Midtrans jika booking_ref tersedia
        if (!empty($paymentData->booking_ref)) {
            try {
                $midtransResponse = Transaction::status($paymentData->booking_ref);

                // Normalisasi respons Midtrans ke array asosiatif (aman untuk array/object)
                $transactionData = $this->normalizeMidtransResponse($midtransResponse);

                // Mapping status Midtrans ke status internal sistem
                $statusMap = [
                    'capture'    => 'success',
                    'settlement' => 'success',
                    'pending'    => 'pending',
                    'deny'       => 'failed',
                    'cancel'     => 'failed',
                    'expire'     => 'failed',
                    'failure'    => 'failed',
                ];

                $midtransStatus = $transactionData['transaction_status'] ?? '';
                $newStatus = $statusMap[$midtransStatus] ?? $paymentData->status;

                // Update status booking di database jika berubah
                if (!empty($paymentData->booking_id) && $newStatus && $newStatus !== $paymentData->status) {
                    DB::table('tb_bookings')
                        ->where('id', $paymentData->booking_id)
                        ->update(['status' => $newStatus, 'updated_at' => now()]);

                    // Perbarui nilai di objek untuk tampilan
                    $paymentData->status = $newStatus;
                }

                // Ambil URL receipt jika tersedia
                $receiptUrl = $transactionData['pdf_url'] ?? null;

            } catch (\Throwable $e) {
                Log::warning('Gagal mengambil status dari Midtrans', [
                    'booking_ref' => $paymentData->booking_ref,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return view('module.payment.show', [
            'payment'      => $paymentData,
            'midtransData' => $midtransResponse, // untuk keperluan debug di view
            'receiptUrl'   => $receiptUrl,
        ]);
    }

    /**
     * Alias: Menampilkan detail pembayaran berdasarkan Booking ID.
     *
     * Digunakan ketika rute seperti `/bookings/{id}/payment` dipanggil.
     * Mencari pembayaran terkait (jika ada), lalu menampilkan status dari Midtrans.
     *
     * @param int $bookingId ID booking
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika booking tidak ditemukan
     */
    public function showPayment($bookingId)
    {
        $booking = Booking::with('customer')
            ->where('is_deleted', false)
            ->findOrFail($bookingId);

        // Cari pembayaran terkait booking (bisa null jika belum ada relasi eksplisit)
        $payment = Payment::where('is_deleted', false)
            ->where('booking_id', $booking->id)
            ->latest('id')
            ->first();

        $this->configureMidtrans();

        $midtransResponse = null;
        $transactionData = [];

        try {
            $midtransResponse = Transaction::status($booking->booking_ref);
            $transactionData = $this->normalizeMidtransResponse($midtransResponse);

            $statusMap = [
                'capture'    => 'success',
                'settlement' => 'success',
                'pending'    => 'pending',
                'deny'       => 'failed',
                'cancel'     => 'failed',
                'expire'     => 'failed',
                'failure'    => 'failed',
            ];

            $midtransStatus = $transactionData['transaction_status'] ?? '';
            $newStatus = $statusMap[$midtransStatus] ?? $booking->status;

            // Sinkronisasi status jika berubah
            if ($booking->status !== $newStatus) {
                $booking->update(['status' => $newStatus]);
            }

        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil status Midtrans via booking ID', [
                'booking_id'   => $bookingId,
                'booking_ref'  => $booking->booking_ref,
                'error'        => $e->getMessage(),
            ]);
        }

        return view('module.payment.show', compact('payment', 'booking', 'midtransResponse', 'transactionData'));
    }

    /**
     * Memperbarui status pembayaran dari Midtrans secara manual (action refresh).
     *
     * Hanya memperbarui status booking di database — tidak mengubah data pembayaran.
     *
     * @param int $id ID pembayaran
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshStatus($id)
    {
        $paymentData = DB::table('tb_payments as p')
            ->leftJoin('tb_bookings as b', function ($join) {
                $join->on('b.customer_id', '=', 'p.customer_id')
                     ->where('b.is_deleted', 0);
            })
            ->where('p.is_deleted', 0)
            ->where('p.id', $id)
            ->whereNotNull('b.booking_ref') // pastikan ada booking_ref
            ->selectRaw("p.id as payment_id, b.id as booking_id, b.booking_ref, b.status")
            ->orderByDesc('b.id')
            ->first();

        if (!$paymentData || empty($paymentData->booking_ref)) {
            return back()->withErrors('Booking terkait pembayaran ini tidak ditemukan atau tidak memiliki booking reference.');
        }

        $this->configureMidtrans();

        try {
            $response = Transaction::status($paymentData->booking_ref);
            $transactionData = $this->normalizeMidtransResponse($response);

            $statusMap = [
                'capture'    => 'success',
                'settlement' => 'success',
                'pending'    => 'pending',
                'deny'       => 'failed',
                'cancel'     => 'failed',
                'expire'     => 'failed',
                'failure'    => 'failed',
            ];

            $midtransStatus = $transactionData['transaction_status'] ?? '';
            $newStatus = $statusMap[$midtransStatus] ?? $paymentData->status;

            // Update hanya jika status berubah
            if (!empty($paymentData->booking_id) && $newStatus && $newStatus !== $paymentData->status) {
                DB::table('tb_bookings')
                    ->where('id', $paymentData->booking_id)
                    ->update(['status' => $newStatus, 'updated_at' => now()]);
            }

            return back()->with('success', "Status berhasil diperbarui menjadi: {$newStatus}");

        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui status dari Midtrans', [
                'payment_id'   => $id,
                'booking_ref'  => $paymentData->booking_ref,
                'error'        => $e->getMessage(),
            ]);

            return back()->withErrors('Gagal mengambil status terbaru dari Midtrans. Silakan coba lagi.');
        }
    }

    // ┌──────────────────────────────────────────────────────────────────────┐
    // │ Private Helper Methods                                               │
    // └──────────────────────────────────────────────────────────────────────┘

    /**
     * Mengonfigurasi Midtrans SDK sesuai environment.
     *
     * Menggunakan nilai dari .env:
     * - MIDTRANS_SERVER_KEY
     * - MIDTRANS_IS_PRODUCTION (default: false)
     */
    private function configureMidtrans(): void
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Menormalisasi respons Midtrans ke bentuk array asosiatif.
     *
     * Midtrans SDK versi lama mengembalikan object stdClass, versi baru bisa array.
     * Fungsi ini menjamin output selalu array untuk akses yang konsisten.
     *
     * @param mixed $response Respons dari Transaction::status()
     * @return array Data transaksi dalam format array
     */
    private function normalizeMidtransResponse($response): array
    {
        if (is_array($response)) {
            return $response;
        }

        if (is_object($response)) {
            return json_decode(json_encode($response), true);
        }

        // Fallback: return array kosong agar tidak crash
        Log::warning('Midtrans response tidak dalam format array/object', [
            'type' => gettype($response),
            'value' => $response,
        ]);

        return [];
    }
}