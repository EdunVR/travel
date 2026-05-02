<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Detail</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 14px;
        }
        .info {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        .penjualan-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 8px;
            page-break-inside: avoid;
        }
        .penjualan-header {
            background-color: #f5f5f5;
            padding: 5px;
            border-radius: 3px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 9px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f4ff;
        }
        .summary-table {
            margin-top: 15px;
            width: 100%;
        }
        .summary-table td {
            border: none;
            padding: 3px;
        }
        .page-break {
            page-break-before: always;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #999;
            padding: 20px;
        }
        
        /* Perbaikan untuk page break */
        .page-container {
            page-break-after: always;
        }
        .last-page {
            page-break-after: auto;
        }
        
        /* Menghitung tinggi dinamis */
        .content {
            min-height: 95vh; /* Tinggi minimum untuk satu halaman */
            position: relative;
        }
        
        .summary-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENJUALAN DETAIL</h2>
        <h3>{{ $setting->nama_perusahaan ?? 'Toko' }}</h3>
        <p>{{ $setting->alamat ?? '' }} | {{ $setting->telepon ?? '' }}</p>
    </div>

    <div class="info">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 15%;"><strong>Periode:</strong></td>
                <td style="border: none; width: 35%;">{{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</td>
                <td style="border: none; width: 15%;"><strong>Outlet:</strong></td>
                <td style="border: none; width: 35%;">{{ $outlet->nama_outlet ?? 'Semua Outlet' }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Tanggal Cetak:</strong></td>
                <td style="border: none;">{{ date('d/m/Y H:i:s') }}</td>
                <td style="border: none;"><strong>Jumlah Transaksi:</strong></td>
                <td style="border: none;">{{ $penjualan->count() }} transaksi</td>
            </tr>
        </table>
    </div>

    @if($penjualan->count() > 0)
        @php
            $grand_total_item = 0;
            $grand_total_harga = 0;
            $grand_total_bayar = 0;
            $current_page_height = 0;
            $max_page_height = 90; // dalam vh, sesuaikan dengan kebutuhan
        @endphp

        @foreach($penjualan as $index => $item)
            @php
                // Estimasi tinggi transaksi (dalam vh)
                $transaction_height = 20 + (count($item->details) * 3);
                $current_page_height += $transaction_height;
            @endphp

            {{-- Jika melebihi batas halaman, buat page break --}}
            @if($current_page_height > $max_page_height && $index > 0)
                </div><div class="page-container">
                @php $current_page_height = $transaction_height; @endphp
                
                {{-- Header untuk halaman baru --}}
                <div class="header">
                    <h2>LAPORAN PENJUALAN DETAIL (Lanjutan)</h2>
                    <h3>{{ $setting->nama_perusahaan ?? 'Toko' }}</h3>
                    <p>{{ $setting->alamat ?? '' }} | {{ $setting->telepon ?? '' }}</p>
                </div>
            @endif

            <div class="penjualan-item">
                <div class="penjualan-header">
                    <table style="border: none;">
                        <tr>
                            <td style="border: none; width: 15%;"><strong>No. Transaksi:</strong></td>
                            <td style="border: none; width: 35%;">#{{ $item->id_penjualan }}</td>
                            <td style="border: none; width: 15%;"><strong>Tanggal:</strong></td>
                            <td style="border: none; width: 35%;">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;"><strong>Outlet:</strong></td>
                            <td style="border: none;">{{ $item->outlet->nama_outlet ?? '-' }}</td>
                            <td style="border: none;"><strong>Customer:</strong></td>
                            <td style="border: none;">{{ $item->member->nama ?? 'Customer Umum' }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;"><strong>Kasir:</strong></td>
                            <td style="border: none;">{{ $item->user->name ?? '-' }}</td>
                            <td style="border: none;"><strong>Status:</strong></td>
                            <td style="border: none;">
                                @if($item->diterima >= $item->bayar)
                                    <span style="color: green;">LUNAS</span>
                                @else
                                    <span style="color: red;">KREDIT</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th width="30%">Nama Produk</th>
                            <th width="10%">Harga</th>
                            <th width="8%">Qty</th>
                            <th width="12%">Subtotal</th>
                            <th width="10%">Diskon</th>
                            <th width="10%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_item_transaksi = 0;
                            $total_harga_transaksi = 0;
                        @endphp

                        @foreach($item->details as $key => $detail)
                        @php
                            $total_item_transaksi += $detail->jumlah;
                            $total_harga_transaksi += $detail->subtotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $detail->produk->kode_produk ?? '-' }}</td>
                            <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detail->jumlah }}</td>
                            <td class="text-right">Rp {{ number_format($detail->harga_jual * $detail->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->diskon }}%</td>
                            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach

                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>TOTAL TRANSAKSI</strong></td>
                            <td class="text-center"><strong>{{ $total_item_transaksi }}</strong></td>
                            <td colspan="2" class="text-right"><strong>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
                            <td class="text-center"><strong>{{ $item->diskon }}%</strong></td>
                            <td class="text-right"><strong>Rp {{ number_format($item->bayar, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 5px; font-size: 9px;">
                    <strong>Pembayaran:</strong> 
                    Diterima: Rp {{ number_format($item->diterima, 0, ',', '.') }} | 
                    Kembali: Rp {{ number_format($item->diterima - $item->bayar, 0, ',', '.') }}
                </div>
            </div>

            @php
                $grand_total_item += $total_item_transaksi;
                $grand_total_harga += $item->total_harga;
                $grand_total_bayar += $item->bayar;
            @endphp

        @endforeach

        <!-- SUMMARY -->
        <div class="summary-section">
            <h4 style="margin: 0 0 10px 0; text-align: center;">RINGKASAN TOTAL</h4>
            <table class="summary-table">
                <tr>
                    <td width="60%" class="text-right"><strong>Total Seluruh Item Terjual:</strong></td>
                    <td width="40%" class="text-right"><strong>{{ $grand_total_item }} item</strong></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Total Harga Penjualan:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($grand_total_harga, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Total Penerimaan:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($grand_total_bayar, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Jumlah Transaksi:</strong></td>
                    <td class="text-right"><strong>{{ $penjualan->count() }} transaksi</strong></td>
                </tr>
            </table>
        </div>

    @else
        <div class="no-data">
            <p>Tidak ada data penjualan untuk periode yang dipilih.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }} | {{ date('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
