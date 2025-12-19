<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan & Operasional - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #111;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            display: table-cell;
            width: 50%;
            padding: 15px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }
        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
            color: #111;
            margin: 10px 0 0 0;
        }
        .summary-card.electricity .amount {
            color: #d97706;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        table tr.highlight {
            background-color: #fef3c7;
        }
        table tfoot td {
            background-color: #f9fafb;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan & Operasional</h1>
        <p>Periode: {{ $monthName }} {{ $year }}</p>
        <p>Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Pengeluaran Operasional</h3>
            <p style="font-size: 10px; color: #888; margin: 3px 0;">Gaji, Kebersihan, dan lainnya</p>
            <p class="amount">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card electricity">
            <h3>Estimasi Listrik</h3>
            <p style="font-size: 10px; color: #888; margin: 3px 0;">Berdasarkan durasi booking aktif</p>
            <p class="amount">Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expensesByCategory as $category => $amount)
            <tr>
                <td>{{ $category }}</td>
                <td style="text-align: right;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="highlight">
                <td><strong>Listrik (Estimasi)</strong></td>
                <td style="text-align: right;"><strong>Rp {{ number_format($totalElectricityCost, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL ESTIMASI</td>
                <td style="text-align: right;">Rp {{ number_format($totalExpenses + $totalElectricityCost, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Self-Storage Bali</p>
    </div>
</body>
</html>
