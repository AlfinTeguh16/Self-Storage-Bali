@extends('layouts.master')
@section('title', 'Financial & Operational Reports')

@section('content')
<section>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold mb-2">Financial & Operational Reports</h1>
            <p class="text-gray-600">Summary of expenses and estimated operational costs.</p>
        </div>
        
        <!-- Filter Form -->
        <form action="{{ route('report.index') }}" method="GET" class="flex gap-2 items-center mt-4 md:mt-0">
            <select name="month" class="border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                @endforeach
            </select>
            <select name="year" class="border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach(range(date('Y')-2, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <x-button variant="neutral" type="submit">Filter</x-button>
        </form>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Total Operational Expenses -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Total Expenses</h2>
                    <p class="text-sm text-gray-500">This Month (Salary, Cleaning, etc.)</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <i class="ph-bold ph-money w-6 h-6 text-2xl"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
        </div>

        <!-- Electricity Estimation -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Electricity Estimate</h2>
                    <p class="text-sm text-gray-500">Estimate based on active booking duration</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <i class="ph-bold ph-lightning w-6 h-6 text-2xl"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-amber-600">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</div>
            <p class="text-xs text-gray-400 mt-2">*Calculation: Booking Duration x Daily Rate</p>
        </div>
    </div>

    <!-- Breakdown & Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Rincian Table -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
             <header class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-semibold text-gray-800">Expense Breakdown</h2>
            </header>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-100">
                            <th class="px-6 py-3 text-left font-medium">Category</th>
                            <th class="px-6 py-3 text-right font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($expensesByCategory as $cat => $amount)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $cat }}</td>
                            <td class="px-6 py-3 text-sm text-right font-medium text-gray-900">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <!-- Add Electricity to list visually -->
                        <tr class="hover:bg-amber-50 bg-amber-50/30">
                            <td class="px-6 py-3 text-sm text-amber-700 font-medium">Electricity (Estimate)</td>
                            <td class="px-6 py-3 text-sm text-right font-medium text-amber-700">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-gray-800 border-t border-gray-200">
                         <tr>
                            <td class="px-6 py-3">TOTAL ESTIMATE</td>
                            <td class="px-6 py-3 text-right">Rp {{ number_format($totalExpenses + $totalElectricityCost, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Export Actions -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 h-fit">
            <h2 class="font-semibold text-gray-800 mb-4">Export Data</h2>
            <div class="space-y-3">
                <a href="{{ route('report.export-excel', ['month' => $month, 'year' => $year]) }}" class="flex w-full justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                    <i class="ph-bold ph-microsoft-excel mr-2 text-lg"></i>
                    Export Excel
                </a>
                <a href="{{ route('report.export-pdf', ['month' => $month, 'year' => $year]) }}" target="_blank" class="flex w-full justify-center items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition">
                    <i class="ph-bold ph-file-pdf mr-2 text-lg"></i>
                    Export PDF
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
