<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 0;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        .summary-card .label {
            font-size: 9px;
            color: #666;
        }
        .summary-card .value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background: #333;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #333;
        }
        table.data-table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-green {
            background: #d4edda;
            color: #155724;
        }
        .badge-orange {
            background: #fff3cd;
            color: #856404;
        }
        .badge-red {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-blue {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-cyan {
            background: #d1f2eb;
            color: #0c5460;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p><strong>{{ $outletName }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <p style="font-size: 9px;">Dicetak: {{ $generatedAt }}</p>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ number_format($summary['total_transaksi']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Penjualan</div>
            <div class="value">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Diskon</div>
            <div class="value">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Dibayar</div>
            <div class="value">Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <table>
            <tr>
                <td width="25%"><strong>Invoice:</strong> {{ $summary['total_invoice'] }} transaksi</td>
                <td width="25%"><strong>POS:</strong> {{ $summary['total_pos'] }} transaksi</td>
                <td width="25%"><strong>Lunas:</strong> {{ $summary['lunas'] }} transaksi</td>
                <td width="25%"><strong>Belum Lunas:</strong> {{ $summary['belum_lunas'] }} transaksi</td>
            </tr>
        </table>
    </div>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Source</th>
                <th width="12%">No Invoice</th>
                <th width="9%">Tanggal</th>
                <th width="10%">Outlet</th>
                <th width="12%">Customer</th>
                <th width="6%" class="text-right">Item</th>
                <th width="10%" class="text-right">Total</th>
                <th width="9%" class="text-right">Diskon</th>
                <th width="10%" class="text-right">Dibayar</th>
                <th width="12%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    @if($item['source'] === 'invoice')
                        <span class="badge badge-blue">Invoice</span>
                    @else
                        <span class="badge badge-cyan">POS</span>
                    @endif
                </td>
                <td>{{ $item['invoice_number'] }}</td>
                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y H:i') }}</td>
                <td>{{ $item['outlet'] }}</td>
                <td>{{ $item['customer'] }}</td>
                <td class="text-right">{{ $item['total_item'] }}</td>
                <td class="text-right">{{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['diskon'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['total_bayar'], 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($item['payment_status'] === 'Lunas')
                        <span class="badge badge-green">Lunas</span>
                    @elseif($item['payment_status'] === 'Dibayar Sebagian')
                        <span class="badge badge-orange">Dibayar Sebagian</span>
                    @else
                        <span class="badge badge-red">Belum Lunas</span>
                    @endif
                    @if(isset($item['payment_method']) && $item['source'] === 'pos')
                        <br><small style="font-size: 7px;">{{ $item['payment_method'] }}</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f0f0f0; font-weight: bold;">
                <td colspan="7" class="text-right">TOTAL:</td>
                <td class="text-right">{{ number_format($summary['total_penjualan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($summary['total_dibayar'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem | {{ config('app.name') }}</p>
    </div>
</body>
</html>
