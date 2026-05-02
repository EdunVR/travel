<!DOCTYPE html>
<html>
<head>
    <title>Laporan PO Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .report-title {
            font-size: 16px;
            margin: 10px 0;
        }
        .filter-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $setting->nama_perusahaan ?? 'PT. DAHANA REKAYASA NUSANTARA' }}</div>
        <div class="report-title">Laporan PO Penjualan</div>
        <div class="filter-info">
            Periode: {{ tanggal_indonesia($start_date) }} - {{ tanggal_indonesia($end_date) }}
            @if($outlet)
                | Outlet: {{ $outlet->nama_outlet }}
            @endif
            @if($status)
                | Status: {{ ucfirst($status) }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. PO</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Outlet</th>
                <th>Total Item</th>
                <th>Total Harga</th>
                <th>Ongkir</th>
                <th>Diskon</th>
                <th>Total Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($poPenjualan as $key => $po)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $po->no_po }}</td>
                <td>{{ tanggal_indonesia($po->tanggal, false) }}</td>
                <td>{{ $po->member->nama ?? 'Customer Umum' }}</td>
                <td>{{ $po->outlet->nama_outlet ?? '-' }}</td>
                <td class="text-right">{{ $po->total_item }}</td>
                <td class="text-right">{{ format_uang($po->total_harga) }}</td>
                <td class="text-right">{{ format_uang($po->ongkir) }}</td>
                <td class="text-right">{{ $po->diskon }}%</td>
                <td class="text-right">{{ format_uang($po->bayar) }}</td>
                <td class="text-center">
                    @if($po->status == 'menunggu')
                        <span style="color: orange;">Menunggu</span>
                    @elseif($po->status == 'lunas')
                        <span style="color: green;">Lunas</span>
                    @else
                        <span style="color: red;">Gagal</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ $poPenjualan->sum('total_item') }}</strong></td>
                <td class="text-right"><strong>{{ format_uang($poPenjualan->sum('total_harga')) }}</strong></td>
                <td class="text-right"><strong>{{ format_uang($poPenjualan->sum('ongkir')) }}</strong></td>
                <td></td>
                <td class="text-right"><strong>{{ format_uang($poPenjualan->sum('bayar')) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dicetak pada: {{ date('d/m/Y H:i:s') }}</div>
        <div>Oleh: {{ auth()->user()->name }}</div>
    </div>
</body>
</html>
