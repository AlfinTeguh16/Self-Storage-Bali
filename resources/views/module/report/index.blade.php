@extends('layouts.master')
@section('title', 'Reports Dashboard')

@section('content')
<section>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold mb-2">Reports Dashboard</h1>
            <p class="text-gray-600">Laporan pendapatan, penyewaan storage, dan pemesanan.</p>
        </div>
        
        <!-- Filter Form -->
        <form action="{{ route('report.index') }}" method="GET" class="flex flex-wrap gap-3 items-end mt-4 md:mt-0">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                <select name="month" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach(range(date('Y')-2, date('Y')+1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <i class="ph-bold ph-funnel mr-2"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- Export Buttons -->
    <div class="flex gap-2 mb-6">
        <a href="{{ route('report.export-excel', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-sm">
            <i class="ph-bold ph-file-xls mr-2"></i>
            Export Excel
        </a>
        <a href="{{ route('report.export-pdf', ['month' => $month, 'year' => $year]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition text-sm">
            <i class="ph-bold ph-file-pdf mr-2"></i>
            Export PDF
        </a>
    </div>

    <!-- ==================== SECTION 1: PENDAPATAN & PENGELUARAN ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="ph-bold ph-chart-line-up text-emerald-600 mr-2"></i>
                Pendapatan & Pengeluaran
            </h2>
            <p class="text-sm text-gray-500">Ringkasan keuangan bulan {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</p>
        </header>
        
        <div class="p-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Revenue -->
                <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                    <p class="text-sm text-emerald-700 font-medium">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-600 mt-1">Dari {{ $successBookings }} booking sukses</p>
                </div>
                
                <!-- Total Expenses -->
                <div class="bg-rose-50 rounded-lg p-4 border border-rose-100">
                    <p class="text-sm text-rose-700 font-medium">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-rose-600">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                    <p class="text-xs text-rose-600 mt-1">Operasional bulanan</p>
                </div>
                
                <!-- Electricity -->
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                    <p class="text-sm text-amber-700 font-medium">Estimasi Listrik</p>
                    <p class="text-2xl font-bold text-amber-600">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</p>
                    <p class="text-xs text-amber-600 mt-1">Berdasarkan durasi booking</p>
                </div>
                
                <!-- Net Profit -->
                @php
                    $netProfit = $totalRevenue - $totalExpenses - $totalElectricityCost;
                    $profitColor = $netProfit >= 0 ? 'emerald' : 'rose';
                @endphp
                <div class="bg-{{ $profitColor }}-50 rounded-lg p-4 border border-{{ $profitColor }}-100">
                    <p class="text-sm text-{{ $profitColor }}-700 font-medium">Laba Bersih</p>
                    <p class="text-2xl font-bold text-{{ $profitColor }}-600">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                    <p class="text-xs text-{{ $profitColor }}-600 mt-1">Pendapatan - Pengeluaran</p>
                </div>
            </div>
            
            <!-- Expense Breakdown Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-medium">Kategori Pengeluaran</th>
                            <th class="px-4 py-3 text-right font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($expensesByCategory as $cat => $amount)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $cat }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada pengeluaran tercatat.</td>
                        </tr>
                        @endforelse
                        <tr class="bg-amber-50">
                            <td class="px-4 py-3 text-sm font-medium text-amber-700">Listrik (Estimasi)</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-amber-700">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold text-gray-800 border-t-2 border-gray-300">
                        <tr>
                            <td class="px-4 py-3">Total Pengeluaran</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($totalExpenses + $totalElectricityCost, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== SECTION 2: LAPORAN PENYEWAAN STORAGE ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="ph-bold ph-warehouse text-indigo-600 mr-2"></i>
                Laporan Penyewaan Storage Room
            </h2>
            <p class="text-sm text-gray-500">Status dan ketersediaan unit storage saat ini</p>
        </header>
        
        <div class="p-6">
            <!-- Occupancy Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                    <p class="text-sm text-indigo-700 font-medium">Total Unit</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $totalStorages }}</p>
                </div>
                <div class="bg-rose-50 rounded-lg p-4 border border-rose-100">
                    <p class="text-sm text-rose-700 font-medium">Terisi</p>
                    <p class="text-2xl font-bold text-rose-600">{{ $occupiedStorages }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                    <p class="text-sm text-emerald-700 font-medium">Tersedia</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $availableStorages }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                    <p class="text-sm text-purple-700 font-medium">Tingkat Hunian</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $occupancyRate }}%</p>
                </div>
            </div>
            
            <!-- Storage List -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-medium">Unit Storage</th>
                            <th class="px-4 py-3 text-right font-medium">Harga/Hari</th>
                            <th class="px-4 py-3 text-left font-medium">Penyewa Saat Ini</th>
                            <th class="px-4 py-3 text-left font-medium">Berakhir</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($storageList as $storage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $storage['size'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">Rp {{ number_format($storage['price'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $storage['customer_name'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($storage['booking_end_date'])
                                    {{ \Carbon\Carbon::parse($storage['booking_end_date'])->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($storage['is_occupied'])
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-700">Terisi</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== SECTION 3: PEMESANAN ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <header class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="ph-bold ph-calendar-check text-blue-600 mr-2"></i>
                    Pemesanan
                </h2>
                <p class="text-sm text-gray-500">Daftar booking bulan {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</p>
            </div>
            <a href="{{ route('data-booking.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat Semua →</a>
        </header>
        
        <div class="p-6">
            <!-- Booking Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <p class="text-sm text-blue-700 font-medium">Total Booking</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalBookings }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                    <p class="text-sm text-emerald-700 font-medium">Sukses</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $successBookings }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                    <p class="text-sm text-amber-700 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingBookings }}</p>
                </div>
                <div class="bg-rose-50 rounded-lg p-4 border border-rose-100">
                    <p class="text-sm text-rose-700 font-medium">Gagal</p>
                    <p class="text-2xl font-bold text-rose-600">{{ $failedBookings }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                    <p class="text-sm text-purple-700 font-medium">Customer Baru</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $newCustomers }}</p>
                </div>
            </div>
            
            <!-- Recent Bookings Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-medium">Booking Ref</th>
                            <th class="px-4 py-3 text-left font-medium">Customer</th>
                            <th class="px-4 py-3 text-left font-medium">Storage</th>
                            <th class="px-4 py-3 text-left font-medium">Periode</th>
                            <th class="px-4 py-3 text-right font-medium">Total</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $booking->booking_ref }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->customer?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $booking->storage?->size ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $booking->start_date?->format('d/m/Y') }} - {{ $booking->end_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'success' => 'bg-emerald-100 text-emerald-700',
                                        'failed' => 'bg-rose-100 text-rose-700',
                                        'cancelled' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $color = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada booking ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection


