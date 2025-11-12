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
        // Gunakan timezone eksplisit agar konsisten
        $now = Carbon::now(config('app.timezone')); // e.g., 'Asia/Jakarta'
        $today = $now->copy()->startOfDay();

        // ========== KPIs (Cards) ==========
        $totalStorages = DB::table('tb_storages')->where('is_deleted', 0)->count();
        $bookedStorages = DB::table('tb_storage_management')
            ->where('is_deleted', 0)
            ->where('status', 'booked')
            ->count();
        $occupancyRate = $totalStorages ? round($bookedStorages / $totalStorages * 100, 1) : 0;

        $activeToday = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $new7d = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay())
            ->count();

        $availableStorages = DB::table('tb_storage_management as sm')
            ->join('tb_storages as s', 's.id', '=', 'sm.storage_id')
            ->where('sm.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('sm.status', 'available')
            ->whereNull('sm.booking_id')
            ->count();

        $endingSoonCount = DB::table('tb_bookings')
            ->where('is_deleted', 0)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(3))
            ->count();

        $revenueMonth = DB::table('tb_bookings as b')
            ->join('tb_storages as s', 's.id', '=', 'b.storage_id')
            ->where('b.is_deleted', 0)
            ->where('s.is_deleted', 0)
            ->where('b.status', 'success')
            ->whereYear('b.created_at', $now->year)
            ->whereMonth('b.created_at', $now->month)
            ->sum('s.price');

        // ========== Charts ==========
        // 🔧 Perbaikan: pastikan batas waktu eksplisit + gunakan DATE() yang aman
        $startDate = $now->copy()->subDays(30)->startOfDay(); // inklusif hari ke-30
        $endDate = $now->copy()->endOfDay(); // inklusif hari ini

        $trendRaw = DB::table('tb_bookings')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('is_deleted', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // Normalisasi ke 31 hari (hari ke-30 s.d. hari ini)
        $trendLabels = [];
        $trendCounts = [];

        for ($i = 30; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString(); // 'Y-m-d'
            $trendLabels[] = Carbon::parse($date)->format('d M');

            // 🔧 Perbaikan: pastikan perbandingan string Y-m-d eksak
            $found = $trendRaw->first(function ($item) use ($date) {
                // $item itu stdClass, cek apakah punya properti 'stdClass'
                if (isset($item->stdClass)) {
                    return $item->stdClass->d === $date;
                }
                // fallback: normal object
                return $item->d === $date;
            });
            
            if ($found) {
                $count = isset($found->stdClass) ? (int) $found->stdClass->c : (int) $found->c;
                $trendCounts[] = $count;
            } else {
                $trendCounts[] = 0;
            }
        }

        // Optional: log untuk debugging (hapus di production)
        Log::info('Trend Raw Data:', $trendRaw->toArray());
        Log::info('Trend Labels:', $trendLabels);
        Log::info('Trend Counts:', $trendCounts);

        // ========== Tables ==========
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

        $paymentSnapshotRaw = DB::table('tb_bookings')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->where('is_deleted', 0)
            ->groupBy('status')
            ->pluck('c', 'status');

        $paymentSnapshot = [
            'success' => (int) ($paymentSnapshotRaw['success'] ?? 0),
            'pending' => (int) ($paymentSnapshotRaw['pending'] ?? 0),
            'failed'  => (int) ($paymentSnapshotRaw['failed'] ?? 0),
        ];

        return view('dashboard.index', compact(
            'occupancyRate', 'totalStorages', 'bookedStorages', 'activeToday',
            'new7d', 'availableStorages', 'endingSoonCount', 'revenueMonth',
            'trendLabels', 'trendCounts', 'latestPayments', 'currentStays',
            'endingSoonList', 'paymentSnapshot'
        ));
    }

    public function admin(){
        return $this->index();
    }
}
