<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use App\Models\Storage;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $reportData = $this->getReportData($month, $year);
        
        return view('module.report.index', array_merge($reportData, [
            'month' => $month,
            'year' => $year,
        ]));
    }

    /**
     * Export report data to Excel
     */
    public function exportExcel(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $reportData = $this->getReportData($month, $year);
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        
        // Prepare data for Excel export
        $exportData = [];
        
        // ============================================
        // SECTION 1: PENDAPATAN & PENGELUARAN
        // ============================================
        $exportData[] = ['category' => '═══ 1. PENDAPATAN & PENGELUARAN ═══', 'amount' => ''];
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Total Pendapatan', 'amount' => 'Rp ' . number_format($reportData['totalRevenue'], 0, ',', '.')];
        $exportData[] = ['category' => 'Total Pengeluaran Operasional', 'amount' => 'Rp ' . number_format($reportData['totalExpenses'], 0, ',', '.')];
        $exportData[] = ['category' => 'Estimasi Listrik', 'amount' => 'Rp ' . number_format($reportData['totalElectricityCost'], 0, ',', '.')];
        
        $netProfit = $reportData['totalRevenue'] - $reportData['totalExpenses'] - $reportData['totalElectricityCost'];
        $exportData[] = ['category' => 'Laba Bersih', 'amount' => 'Rp ' . number_format($netProfit, 0, ',', '.')];
        
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Rincian Pengeluaran:', 'amount' => ''];
        foreach ($reportData['expensesByCategory'] as $category => $amount) {
            $exportData[] = [
                'category' => '  - ' . $category,
                'amount' => 'Rp ' . number_format($amount, 0, ',', '.')
            ];
        }
        $exportData[] = ['category' => '  - Listrik (Estimasi)', 'amount' => 'Rp ' . number_format($reportData['totalElectricityCost'], 0, ',', '.')];
        $exportData[] = ['category' => 'TOTAL PENGELUARAN', 'amount' => 'Rp ' . number_format($reportData['totalExpenses'] + $reportData['totalElectricityCost'], 0, ',', '.')];
        
        // ============================================
        // SECTION 2: PENYEWAAN STORAGE ROOM
        // ============================================
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => '═══ 2. PENYEWAAN STORAGE ROOM ═══', 'amount' => ''];
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Total Unit Storage', 'amount' => $reportData['totalStorages']];
        $exportData[] = ['category' => 'Unit Terisi', 'amount' => $reportData['occupiedStorages']];
        $exportData[] = ['category' => 'Unit Tersedia', 'amount' => $reportData['availableStorages']];
        $exportData[] = ['category' => 'Tingkat Hunian', 'amount' => $reportData['occupancyRate'] . '%'];
        
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Detail Storage:', 'amount' => ''];
        foreach ($reportData['storageList'] as $storage) {
            $status = $storage['is_occupied'] ? 'TERISI' : 'TERSEDIA';
            $customer = $storage['customer_name'] ?? '-';
            $exportData[] = [
                'category' => '  - ' . $storage['size'] . ' (' . $status . ')',
                'amount' => $customer
            ];
        }
        
        // ============================================
        // SECTION 3: PEMESANAN
        // ============================================
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => '═══ 3. PEMESANAN (' . $monthName . ' ' . $year . ') ═══', 'amount' => ''];
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Total Booking', 'amount' => $reportData['totalBookings']];
        $exportData[] = ['category' => 'Booking Sukses', 'amount' => $reportData['successBookings']];
        $exportData[] = ['category' => 'Booking Pending', 'amount' => $reportData['pendingBookings']];
        $exportData[] = ['category' => 'Booking Gagal', 'amount' => $reportData['failedBookings']];
        $exportData[] = ['category' => 'Customer Baru', 'amount' => $reportData['newCustomers']];
        
        $exportData[] = ['category' => '', 'amount' => ''];
        $exportData[] = ['category' => 'Booking Terbaru:', 'amount' => ''];
        foreach ($reportData['recentBookings'] as $booking) {
            $exportData[] = [
                'category' => '  ' . $booking->booking_ref . ' - ' . ($booking->customer?->name ?? '-'),
                'amount' => 'Rp ' . number_format($booking->total_price, 0, ',', '.') . ' (' . ucfirst($booking->status) . ')'
            ];
        }
        
        $filename = 'Laporan_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.xlsx';
        
        return Excel::download(new ReportsExport($exportData, $month, $year), $filename);
    }


    /**
     * Export report data to PDF
     */
    public function exportPdf(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $reportData = $this->getReportData($month, $year);
        
        $pdf = Pdf::loadView('module.report.pdf', array_merge($reportData, [
            'month' => $month,
            'year' => $year,
            'monthName' => date('F', mktime(0, 0, 0, $month, 1)),
        ]));
        
        $filename = 'Laporan_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Get complete report data for a specific month and year
     */
    private function getReportData($month, $year)
    {
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        // =====================================
        // 1. OPERATIONAL EXPENSES
        // =====================================
        $expenses = \App\Models\OperationalExpense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(fn($row) => $row->sum('amount'));

        // =====================================
        // 2. ELECTRICITY ESTIMATION
        // =====================================
        $electricityRate = \App\Models\AppSetting::find('electricity_daily_rate')?->value ?? 2000;
        
        $activeBookings = Booking::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('status', 'success')
            ->where('is_deleted', false)
            ->get();

        $totalElectricityCost = 0;
        foreach ($activeBookings as $booking) {
            $start = max(strtotime($booking->start_date), strtotime($startDate));
            $end = min(strtotime($booking->end_date), strtotime($endDate));
            
            if ($end >= $start) {
                $days = ceil(($end - $start) / 86400) + 1;
                $totalElectricityCost += $days * $electricityRate;
            }
        }

        // =====================================
        // 3. BOOKING STATISTICS
        // =====================================
        $bookingsThisMonth = Booking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('is_deleted', false);
        
        $totalBookings = (clone $bookingsThisMonth)->count();
        $pendingBookings = (clone $bookingsThisMonth)->where('status', 'pending')->count();
        $successBookings = (clone $bookingsThisMonth)->where('status', 'success')->count();
        $failedBookings = (clone $bookingsThisMonth)->whereIn('status', ['failed', 'cancelled'])->count();
        
        // Revenue from successful bookings in this month
        $totalRevenue = Booking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'success')
            ->where('is_deleted', false)
            ->sum('total_price');
        
        // New customers this month
        $newCustomers = Customer::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('is_deleted', false)
            ->count();

        // =====================================
        // 4. STORAGE OCCUPANCY (Current status)
        // =====================================
        $today = Carbon::today()->toDateString();
        
        $totalStorages = Storage::where('is_deleted', false)->count();
        
        // Count storages that have active booking today
        $occupiedStorageIds = Booking::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('status', 'success')
            ->where('is_deleted', false)
            ->pluck('storage_id')
            ->unique();
        
        $occupiedStorages = $occupiedStorageIds->count();
        $availableStorages = $totalStorages - $occupiedStorages;
        $occupancyRate = $totalStorages > 0 ? round(($occupiedStorages / $totalStorages) * 100, 1) : 0;

        // =====================================
        // 5. RECENT BOOKINGS (Last 10)
        // =====================================
        $recentBookings = Booking::with(['customer', 'storage'])
            ->where('is_deleted', false)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // =====================================
        // 6. STORAGE LIST WITH OCCUPANCY STATUS
        // =====================================
        $storageList = Storage::where('is_deleted', false)
            ->get()
            ->map(function ($storage) use ($today, $occupiedStorageIds) {
                $isOccupied = $occupiedStorageIds->contains($storage->id);
                
                // Get current booking if occupied
                $currentBooking = null;
                if ($isOccupied) {
                    $currentBooking = Booking::with('customer')
                        ->where('storage_id', $storage->id)
                        ->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today)
                        ->where('status', 'success')
                        ->where('is_deleted', false)
                        ->first();
                }
                
                return [
                    'id' => $storage->id,
                    'size' => $storage->size,
                    'price' => $storage->price,
                    'is_occupied' => $isOccupied,
                    'customer_name' => $currentBooking?->customer?->name,
                    'booking_end_date' => $currentBooking?->end_date,
                ];
            });
        
        return [
            // Expenses
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'totalElectricityCost' => $totalElectricityCost,
            
            // Booking Statistics
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'successBookings' => $successBookings,
            'failedBookings' => $failedBookings,
            'totalRevenue' => $totalRevenue,
            'newCustomers' => $newCustomers,
            
            // Storage Occupancy
            'totalStorages' => $totalStorages,
            'occupiedStorages' => $occupiedStorages,
            'availableStorages' => $availableStorages,
            'occupancyRate' => $occupancyRate,
            
            // Lists
            'recentBookings' => $recentBookings,
            'storageList' => $storageList,
        ];
    }
}

