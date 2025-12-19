<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        // 1. Operational Expenses
        $expenses = \App\Models\OperationalExpense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(fn($row) => $row->sum('amount'));

        // 2. Electricity Estimation (Logic: Active Booking Days * Daily Rate)
        // Rate from AppSetting or default 2000
        $electricityRate = \App\Models\AppSetting::find('electricity_daily_rate')?->value ?? 2000;
        
        // Find bookings active in this month
        // Start date <= End of Month AND End date >= Start of Month
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        $bookings = \App\Models\Booking::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('status', 'success') // Only active bookings
            ->get();

        $totalElectricityCost = 0;
        foreach ($bookings as $booking) {
            // Calculate overlap days
            $start = max(strtotime($booking->start_date), strtotime($startDate));
            $end = min(strtotime($booking->end_date), strtotime($endDate));
            
            if ($end >= $start) {
                $days = ceil(($end - $start) / 86400) + 1; // Inclusive
                $totalElectricityCost += $days * $electricityRate;
            }
        }

        // Add Electricity to total expenses for display if not already tracked manually??? 
        // NOTE: User asked for "Electricity per day and calculate based on booking duration".
        // If we want to show this as a "Virtual" expense or "Real" expense? 
        // For now, let's treat it as a separate reported line item in the dashboard.
        
        // 3. Payroll (Aggregated from tb_employee_payroll if exists? Or just placeholder?)
        // Assuming we just want to report what we have.
        
        return view('module.report.index', compact(
            'expenses', 
            'totalExpenses', 
            'expensesByCategory', 
            'totalElectricityCost',
            'month', 
            'year'
        ));
    }

    /**
     * Export report data to Excel
     */
    public function exportExcel(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        // Get report data
        $reportData = $this->getReportData($month, $year);
        
        // Prepare data for Excel export
        $exportData = [];
        foreach ($reportData['expensesByCategory'] as $category => $amount) {
            $exportData[] = [
                'category' => $category,
                'amount' => 'Rp ' . number_format($amount, 0, ',', '.')
            ];
        }
        
        // Add electricity estimation
        $exportData[] = [
            'category' => 'Listrik (Estimasi)',
            'amount' => 'Rp ' . number_format($reportData['totalElectricityCost'], 0, ',', '.')
        ];
        
        // Add total
        $exportData[] = [
            'category' => 'TOTAL ESTIMASI',
            'amount' => 'Rp ' . number_format($reportData['totalExpenses'] + $reportData['totalElectricityCost'], 0, ',', '.')
        ];
        
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
        
        // Get report data
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
     * Get report data for a specific month and year
     */
    private function getReportData($month, $year)
    {
        // 1. Operational Expenses
        $expenses = \App\Models\OperationalExpense::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(fn($row) => $row->sum('amount'));

        // 2. Electricity Estimation
        $electricityRate = \App\Models\AppSetting::find('electricity_daily_rate')?->value ?? 2000;
        
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        $bookings = \App\Models\Booking::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('status', 'success')
            ->get();

        $totalElectricityCost = 0;
        foreach ($bookings as $booking) {
            $start = max(strtotime($booking->start_date), strtotime($startDate));
            $end = min(strtotime($booking->end_date), strtotime($endDate));
            
            if ($end >= $start) {
                $days = ceil(($end - $start) / 86400) + 1;
                $totalElectricityCost += $days * $electricityRate;
            }
        }
        
        return [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'totalElectricityCost' => $totalElectricityCost,
        ];
    }
}
