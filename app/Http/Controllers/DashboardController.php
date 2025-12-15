<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Ambil tahun dari query string, default: tahun ini
        $selectedYear = (int) $request->input('year', now()->year);
        $selectedYear = max(2020, min($selectedYear, now()->year + 1));

        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $today = $now->copy()->startOfDay();
        $startOfYear = Carbon::create($selectedYear, 1, 1)->startOfDay();
        $endOfYear = Carbon::create($selectedYear, 12, 31)->endOfDay();

        // ========== KPIs (Cards) ==========

        // Total storage aktif (tidak dihapus)
        $totalStorages = DB::table('tb_storages')
            ->where('is_deleted', 0)
            ->count();

        // 🔹 OCCUPANCY RATE: storage yang pernah digunakan di tahun terpilih
        $bookedStorages = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->where(function ($query) use ($startOfYear, $endOfYear) {
                $query->whereBetween('start_date', [$startOfYear, $endOfYear])
                      ->orWhereBetween('end_date', [$startOfYear, $endOfYear])
                      ->orWhere(function ($q) use ($startOfYear, $endOfYear) {
                          $q->where('start_date', '<=', $startOfYear)
                            ->where('end_date', '>=', $endOfYear);
                      });
            })
            ->distinct('storage_id')
            ->count();

        $occupancyRate = $totalStorages ? round(($bookedStorages / $totalStorages) * 100, 1) : 0.0;

        // 🔹 Active Today (real-time — tidak tergantung tahun)
        $activeToday = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        // 🔹 New Bookings: di tahun terpilih
        $newThisYear = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->count();

        // 🔹 Available Storages (real-time — status available & tidak sedang dibooking)
        $availableStorages = DB::table('tb_storage_management as sm')
            ->join('tb_storages as s', 's.id', '=', 'sm.storage_id')
            ->where('sm.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('sm.status', 'available')
            ->whereNull('sm.booking_id')
            ->count();

        // 🔹 Ending Soon (≤ 3 hari dari hari ini — real-time)
        $endingSoonCount = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(3))
            ->count();

        // 🔹 Revenue: hanya booking 'success' di tahun terpilih
        $revenueYear = DB::table('tb_bookings as b')
            ->join('tb_storages as s', 's.id', '=', 'b.storage_id')
            ->where('b.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('b.status', 'success')
            ->whereBetween('b.created_at', [$startOfYear, $endOfYear])
            ->sum('s.price');

        // ========== Charts ==========

        // 🔹 Trend Bookings: per bulan dalam tahun terpilih
        $trendLabels = [];
        $trendCounts = [];

        for ($month = 1; $month <= 12; $month++) {
            $trendLabels[] = Carbon::create($selectedYear, $month, 1)->format('M');
            $count = DB::table('tb_bookings')
                ->where('is_deleted', 0)
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $month)
                ->count();
            $trendCounts[] = $count;
        }

        // ========== Tables ==========

        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
            FROM tb_bookings
            WHERE is_deleted = 0
            GROUP BY customer_id) lb
        ");

        // 🔹 Latest Payments: di tahun terpilih
        $latestPayments = DB::table('tb_payments as p')
            ->leftJoin('tb_customers as c', 'c.id', '=', 'p.customer_id')
            ->leftJoin($latestBookingSub, 'lb.customer_id', '=', 'p.customer_id')
            ->leftJoin('tb_bookings as b', 'b.id', '=', 'lb.last_booking_id')
            ->where('p.is_deleted', 0)
            ->whereBetween('p.created_at', [$startOfYear, $endOfYear])
            ->orderByDesc('p.id')
            ->limit(10)
            ->get([
                'p.id',
                'p.method',
                'p.created_at',
                'c.name as customer_name',
                'c.email as customer_email',
                'b.booking_ref',
                'b.status as booking_status',
            ]);

        // 🔹 Current Stays: hari ini saja (real-time)
        $currentStays = DB::table('tb_bookings as b')
            ->join('tb_customers as c', 'c.id', '=', 'b.customer_id')
            ->join('tb_storages as s', 's.id', '=', 'b.storage_id')
            ->where('b.is_deleted', 0)
            ->whereDate('b.start_date', '<=', $today)
            ->whereDate('b.end_date', '>=', $today)
            ->orderBy('b.end_date')
            ->limit(10)
            ->get([
                'b.id',
                'b.booking_ref',
                'b.start_date',
                'b.end_date',
                'c.name as customer_name',
                's.size as storage_size',
                's.price as storage_price',
            ]);

        // 🔹 Ending Soon List: ≤ 3 hari ke depan (real-time)
        $endingSoonList = DB::table('tb_bookings as b')
            ->join('tb_customers as c', 'c.id', '=', 'b.customer_id')
            ->join('tb_storages as s', 's.id', '=', 'b.storage_id')
            ->where('b.is_deleted', 0)
            ->whereDate('b.end_date', '>=', $today)
            ->whereDate('b.end_date', '<=', $today->copy()->addDays(3))
            ->orderBy('b.end_date')
            ->limit(10)
            ->get([
                'b.id',
                'b.booking_ref',
                'b.end_date',
                'c.name as customer_name',
                's.size as storage_size',
            ]);

        // 🔹 Payment Snapshot: di tahun terpilih
        $paymentSnapshotRaw = DB::table('tb_bookings')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->where('is_deleted', 0)
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->groupBy('status')
            ->pluck('c', 'status');

        $paymentSnapshot = [
            'success' => (int) ($paymentSnapshotRaw['success'] ?? 0),
            'pending' => (int) ($paymentSnapshotRaw['pending'] ?? 0),
            'failed'  => (int) ($paymentSnapshotRaw['failed'] ?? 0),
        ];

        return view('dashboard.index', compact(
            'occupancyRate',
            'totalStorages',
            'bookedStorages',
            'activeToday',
            'newThisYear',
            'availableStorages',
            'endingSoonCount',
            'revenueYear',
            'trendLabels',
            'trendCounts',
            'latestPayments',
            'currentStays',
            'endingSoonList',
            'paymentSnapshot',
            'selectedYear'
        ));
    }

    public function admin()
    {
        return $this->index(request());
    }
}