<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #111;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #1f2937;
            color: #fff;
            padding: 10px 15px;
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
        }
        .section-title.green { background-color: #059669; }
        .section-title.indigo { background-color: #4f46e5; }
        .section-title.blue { background-color: #2563eb; }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            background-color: #f9fafb;
        }
        .summary-box h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #111;
        }
        .summary-box .value.green { color: #059669; }
        .summary-box .value.red { color: #dc2626; }
        .summary-box .value.amber { color: #d97706; }
        .summary-box .value.indigo { color: #4f46e5; }
        .summary-box .value.blue { color: #2563eb; }
        .summary-box .sub { font-size: 9px; color: #9ca3af; margin-top: 3px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #f3f4f6;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
        }
        table td {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table tr:nth-child(even) { background-color: #f9fafb; }
        table tr.highlight { background-color: #fef3c7; }
        table tfoot td {
            background-color: #e5e7eb;
            font-weight: bold;
            border-top: 2px solid #9ca3af;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-success { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-failed { background-color: #fee2e2; color: #991b1b; }
        .status-occupied { background-color: #fee2e2; color: #991b1b; }
        .status-available { background-color: #d1fae5; color: #065f46; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>SELF-STORAGE BALI</h1>
        <h2 style="font-size: 16px; margin: 10px 0; color: #4b5563;">Laporan Bulanan</h2>
        <p>Periode: {{ $monthName }} {{ $year }}</p>
        <p>Dicetak: {{ date('d F Y, H:i') }} WIB</p>
    </div>

    <!-- ==================== SECTION 1: PENDAPATAN & PENGELUARAN ==================== -->
    <div class="section">
        <div class="section-title green">1. Pendapatan & Pengeluaran</div>
        
        <div class="summary-row">
            <div class="summary-box">
                <h4>Total Pendapatan</h4>
                <div class="value green">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="sub">Dari {{ $successBookings }} booking sukses</div>
            </div>
            <div class="summary-box">
                <h4>Total Pengeluaran</h4>
                <div class="value red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                <div class="sub">Operasional bulanan</div>
            </div>
            <div class="summary-box">
                <h4>Estimasi Listrik</h4>
                <div class="value amber">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</div>
                <div class="sub">Berdasarkan durasi booking</div>
            </div>
            <div class="summary-box">
                @php $netProfit = $totalRevenue - $totalExpenses - $totalElectricityCost; @endphp
                <h4>Laba Bersih</h4>
                <div class="value {{ $netProfit >= 0 ? 'green' : 'red' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
                <div class="sub">Pendapatan - Pengeluaran</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kategori Pengeluaran</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expensesByCategory as $category => $amount)
                <tr>
                    <td>{{ $category }}</td>
                    <td style="text-align: right;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #9ca3af;">Tidak ada pengeluaran tercatat</td>
                </tr>
                @endforelse
                <tr class="highlight">
                    <td><strong>Listrik (Estimasi)</strong></td>
                    <td style="text-align: right;"><strong>Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL PENGELUARAN</td>
                    <td style="text-align: right;">Rp {{ number_format($totalExpenses + $totalElectricityCost, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- ==================== SECTION 2: PENYEWAAN STORAGE ROOM ==================== -->
    <div class="section">
        <div class="section-title indigo">2. Laporan Penyewaan Storage Room</div>
        
        <div class="summary-row">
            <div class="summary-box">
                <h4>Total Unit</h4>
                <div class="value indigo">{{ $totalStorages }}</div>
            </div>
            <div class="summary-box">
                <h4>Terisi</h4>
                <div class="value red">{{ $occupiedStorages }}</div>
            </div>
            <div class="summary-box">
                <h4>Tersedia</h4>
                <div class="value green">{{ $availableStorages }}</div>
            </div>
            <div class="summary-box">
                <h4>Tingkat Hunian</h4>
                <div class="value indigo">{{ $occupancyRate }}%</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Unit Storage</th>
                    <th style="text-align: right;">Harga/Hari</th>
                    <th>Penyewa Saat Ini</th>
                    <th>Berakhir</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($storageList as $storage)
                <tr>
                    <td>{{ $storage['size'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($storage['price'], 0, ',', '.') }}</td>
                    <td>{{ $storage['customer_name'] ?? '-' }}</td>
                    <td>
                        @if($storage['booking_end_date'])
                            {{ \Carbon\Carbon::parse($storage['booking_end_date'])->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($storage['is_occupied'])
                            <span class="status-badge status-occupied">Terisi</span>
                        @else
                            <span class="status-badge status-available">Tersedia</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ==================== SECTION 3: PEMESANAN ==================== -->
    <div class="section">
        <div class="section-title blue">3. Pemesanan</div>
        
        <div class="summary-row">
            <div class="summary-box">
                <h4>Total Booking</h4>
                <div class="value blue">{{ $totalBookings }}</div>
            </div>
            <div class="summary-box">
                <h4>Sukses</h4>
                <div class="value green">{{ $successBookings }}</div>
            </div>
            <div class="summary-box">
                <h4>Pending</h4>
                <div class="value amber">{{ $pendingBookings }}</div>
            </div>
            <div class="summary-box">
                <h4>Gagal</h4>
                <div class="value red">{{ $failedBookings }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>Customer</th>
                    <th>Storage</th>
                    <th>Periode</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                <tr>
                    <td style="font-family: monospace;">{{ $booking->booking_ref }}</td>
                    <td>{{ $booking->customer?->name ?? '-' }}</td>
                    <td>{{ $booking->storage?->size ?? '-' }}</td>
                    <td>{{ $booking->start_date?->format('d/m/Y') }} - {{ $booking->end_date?->format('d/m/Y') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        @php
                            $statusClass = match($booking->status) {
                                'success' => 'status-success',
                                'pending' => 'status-pending',
                                default => 'status-failed',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af;">Tidak ada booking ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Self-Storage Bali</p>
    </div>
</body>
</html>


