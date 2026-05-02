<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan Keberangkatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
        tfoot tr {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>LAPORAN RINGKASAN KEBERANGKATAN</h1>
    <div class="header-info">
        @if(isset($filters['start_date']) && isset($filters['end_date']))
            Periode: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
        @else
            Semua Periode
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Keberangkatan</th>
                <th>Tanggal</th>
                <th class="text-right">Jamaah</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Expenses</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalJamaah = 0;
                $totalRevenue = 0;
                $totalExpenses = 0;
                $totalProfit = 0;
            @endphp
            @foreach($reportData as $data)
                @php
                    $totalJamaah += $data['jamaah_count'];
                    $totalRevenue += $data['revenue'];
                    $totalExpenses += $data['expenses'];
                    $totalProfit += $data['profit'];
                @endphp
                <tr>
                    <td>{{ $data['keberangkatan_code'] }}</td>
                    <td>{{ $data['keberangkatan_name'] }}</td>
                    <td>{{ $data['departure_date']->format('d M Y') }}</td>
                    <td class="text-right">{{ $data['jamaah_count'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($data['expenses'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($data['profit'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['profit_margin'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td class="text-right">{{ $totalJamaah }}</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 2) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dibuat pada: {{ $generatedAt->format('d M Y H:i:s') }}
    </div>
</body>
</html>
