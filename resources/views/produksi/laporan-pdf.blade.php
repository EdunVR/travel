<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi - {{ config('app.name') }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4f81bd;
            position: relative;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #34495e;
            margin: 5px 0;
            font-size: 18px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 120px;
            color: #2c3e50;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th {
            background: linear-gradient(135deg, #a8c0ff 0%, #b6fbff 100%);
            color: #2c3e50;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }
        .main-table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }
        .main-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .summary {
            background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        .summary-item h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 14px;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #bdc3c7;
            text-align: right;
            color: #7f8c8d;
            font-size: 10px;
        }
        .badge {
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .total-row {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%) !important;
            font-weight: bold;
        }
        .subtotal {
            background: #ecf0f1;
            font-weight: bold;
        }
        .download-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 10px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PRODUKSI</h1>
        <h2>{{ config('app.name') }}</h2>
        <p>Periode: 
            @if($startDate && $endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
        </p>
        
        <div class="download-note">
            <strong>Preview Mode</strong> - Gunakan tombol download di browser untuk menyimpan file PDF
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Outlet:</td>
            <td>{{ $outlet ? $outlet->nama_outlet : 'Semua Outlet' }}</td>
            <td class="label">Tanggal Cetak:</td>
            <td>{{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td class="label">Total Data:</td>
            <td>{{ $produksi->count() }} produksi</td>
            <td class="label">Halaman:</td>
            <td><span class="page-number"></span></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Outlet</th>
                <th width="20%">Produk</th>
                <th width="8%" class="text-center">Jumlah</th>
                <th width="12%" class="text-center">Total HPP</th>
                <th width="12%" class="text-center">HPP/Unit</th>
                <th width="20%">Bahan Digunakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produksi as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->outlet->nama_outlet ?? '-' }}</td>
                <td>{{ $item->produk->nama_produk ?? 'Produk Telah Dihapus' }}</td>
                <td class="text-center">{{ number_format($item->jumlah) }}</td>
                <td class="text-right">Rp {{ number_format($item->total_hpp) }}</td>
                <td class="text-right">Rp {{ number_format($item->hpp_unit) }}</td>
                <td>
                    @foreach($item->detail as $detail)
                    • {{ $detail->bahan->nama_bahan ?? 'Bahan Dihapus' }} 
                    ({{ number_format($detail->jumlah) }} {{ $detail->bahan->satuan->nama_satuan ?? '-' }})<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3>TOTAL PRODUKSI</h3>
                <div class="value">{{ number_format($totalProduksi) }} Unit</div>
            </div>
            <div class="summary-item">
                <h3>TOTAL BIAYA</h3>
                <div class="value">Rp {{ number_format($totalBiaya) }}</div>
            </div>
            <div class="summary-item">
                <h3>RATA-RATA HPP/UNIT</h3>
                <div class="value">Rp {{ number_format($rataHPP) }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} | {{ config('app.name') }} | Halaman <span class="page-number"></span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
            
            $text = "Halaman {PAGE_NUM}";
            $x = $pdf->get_width() - 50;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
