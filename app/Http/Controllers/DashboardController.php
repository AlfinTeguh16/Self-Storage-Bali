<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        $today = Carbon::today();
        $now   = Carbon::now();

        // ========== KPIs (Cards) ==========
        // Occupancy Rate
        $totalStorages  = DB::table('tb_storages')->where('is_deleted', 0)->count();
        $bookedStorages = DB::table('tb_storage_management')
            ->where('is_deleted', 0)
            ->where('status', 'booked')
            ->count();
        $occupancyRate  = $totalStorages ? round($bookedStorages / $totalStorages * 100, 1) : 0;

        // Active bookings today
        $activeToday = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        // New bookings (7 days)
        $new7d = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('created_at', '>=', $now->copy()->subDays(7))
            ->count();

        // Available storages
        $availableStorages = DB::table('tb_storage_management as sm')
            ->join('tb_storages as s', 's.id', '=', 'sm.storage_id')
            ->where('sm.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('sm.status', 'available')
            ->whereNull('sm.booking_id')
            ->count();

        // Ending soon (≤ 3 hari ke depan)
        $endingSoonCount = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(3))
            ->count();

        // Revenue (est.) bulan berjalan: sum harga storage dari booking sukses bulan ini
        $revenueMonth = DB::table('tb_bookings as b')
            ->join('tb_storages as s', 's.id', '=', 'b.storage_id')
            ->where('b.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('b.status', 'success')
            ->whereYear('b.created_at', $now->year)
            ->whereMonth('b.created_at', $now->month)
            ->sum('s.price');

        // ========== Charts ==========
        // Trend bookings (30 hari): tanggal vs jumlah booking dibuat
        $trendRaw = DB::table('tb_bookings')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('is_deleted', 0)
            ->whereDate('created_at', '>=', $now->copy()->subDays(30))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // Normalisasi jadi 31 titik (hari ke-30 s/d hari ini) agar chart rapi
        $trendLabels = [];
        $trendCounts = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $trendLabels[] = Carbon::parse($date)->format('d M');
            $found = $trendRaw->firstWhere('d', $date);
            $trendCounts[] = $found ? (int)$found->c : 0;
        }

        // ========== Tables ==========
        // Latest Payments (10 terakhir) — join customer + booking terakhir per customer
        $latestBookingSub = DB::raw("
            (SELECT customer_id, MAX(id) AS last_booking_id
             FROM tb_bookings
             WHERE is_deleted = 0
             GROUP BY customer_id) lb
        ");

        $latestPayments = DB::table('tb_payments as p')
            ->leftJoin('tb_customers as c', 'c.id', '=', 'p.customer_id')
            ->leftJoin($latestBookingSub, 'lb.customer_id', '=', 'p.customer_id')
            ->leftJoin('tb_bookings as b', 'b.id', '=', 'lb.last_booking_id')
            ->where('p.is_deleted', 0)
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

        // Current stays: booking aktif hari ini (join customer + storage)
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

        // Ending soon list (≤ 3 hari)
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

        // Payment snapshot (count per status dari tb_bookings)
        $paymentSnapshotRaw = DB::table('tb_bookings')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->where('is_deleted', 0)
            ->groupBy('status')
            ->pluck('c', 'status'); // ['success'=>x, 'pending'=>y, 'failed'=>z]

        $paymentSnapshot = [
            'success' => (int)($paymentSnapshotRaw['success'] ?? 0),
            'pending' => (int)($paymentSnapshotRaw['pending'] ?? 0),
            'failed'  => (int)($paymentSnapshotRaw['failed'] ?? 0),
        ];

        return view('dashboard.index', [
            // KPIs
            'occupancyRate'     => $occupancyRate,
            'totalStorages'     => $totalStorages,
            'bookedStorages'    => $bookedStorages,
            'activeToday'       => $activeToday,
            'new7d'             => $new7d,
            'availableStorages' => $availableStorages,
            'endingSoonCount'   => $endingSoonCount,
            'revenueMonth'      => $revenueMonth,

            // Charts
            'trendLabels' => $trendLabels,  // e.g. ['15 Jul','16 Jul',...,'14 Aug']
            'trendCounts' => $trendCounts,  // e.g. [2,0,1,...,4]

            // Tables
            'latestPayments' => $latestPayments,
            'currentStays'   => $currentStays,
            'endingSoonList' => $endingSoonList,

            // Snapshot
            'paymentSnapshot' => $paymentSnapshot,
        ]);
    }

    public function admin(){
        return $this->index();
    }
}
