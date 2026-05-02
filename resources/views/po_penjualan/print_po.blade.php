<!DOCTYPE html>
<html>
<head>
    <title>Surat Purchase Order - {{ $poPenjualan->no_po }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 12px;
            color: #666;
        }
        .po-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 30%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .total-table .label {
            font-weight: bold;
            text-align: right;
            width: 60%;
        }
        .total-table .amount {
            text-align: right;
            width: 40%;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333 !important;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .signature-section {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            float: left;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin: 40px 0 5px 0;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $setting->nama_perusahaan ?? 'PT. SUN TRAVEL' }}</div>
        <div class="company-address">
            {{ $setting->alamat ?? 'Jl. Contoh No. 123, Jakarta' }} | 
            Telp: {{ $setting->telepon ?? '(021) 123-4567' }} | 
            Email: {{ $setting->email ?? 'info@sun-travel.com' }}
        </div>
    </div>

    <div class="po-title">SURAT PURCHASE ORDER (PO)</div>

    <!-- Informasi PO -->
    <table class="info-table">
        <tr>
            <td class="label">No. PO</td>
            <td>: {{ $poPenjualan->no_po }}</td>
            <td class="label">Tanggal PO</td>
            <td>: {{ tanggal_indonesia($poPenjualan->tanggal, false) }}</td>
        </tr>
        <tr>
            <td class="label">Customer</td>
            <td>: {{ $poPenjualan->member->nama ?? 'Customer Umum' }}</td>
            <td class="label">Telepon</td>
            <td>: {{ $poPenjualan->member->telepon ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Outlet</td>
            <td>: {{ $poPenjualan->outlet->nama_outlet ?? '-' }}</td>
            <td class="label">Tanggal Tempo</td>
            <td>: {{ $poPenjualan->tanggal_tempo ? tanggal_indonesia($poPenjualan->tanggal_tempo, false) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td colspan="3">: {{ $poPenjualan->member->alamat ?? '-' }}</td>
        </tr>
    </table>

    <!-- Items -->
    <div class="section">
        <div class="section-title">Detail Items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Produk / Jasa</th>
                    <th width="10%">Harga</th>
                    <th width="8%">Jumlah</th>
                    <th width="10%">Diskon</th>
                    <th width="15%">Subtotal</th>
                    <th width="17%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counter = 1;
                    $totalProduk = 0;
                    $totalOngkir = 0;
                @endphp
                
                @foreach($poPenjualan->details as $detail)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>
                        @if($detail->tipe_item == 'ongkir')
                            <strong>ONGKOS KIRIM</strong><br>
                            {{ $detail->keterangan ?? 'Biaya pengiriman' }}
                        @else
                            <strong>{{ $detail->produk->kode_produk ?? '' }}</strong><br>
                            {{ $detail->produk->nama_produk ?? 'Produk' }}
                        @endif
                    </td>
                    <td class="text-right">{{ format_uang($detail->harga_jual) }}</td>
                    <td class="text-center">{{ $detail->jumlah }}</td>
                    <td class="text-center">{{ $detail->diskon }}%</td>
                    <td class="text-right">{{ format_uang($detail->subtotal) }}</td>
                    <td>{{ $detail->keterangan }}</td>
                </tr>
                
                @if($detail->tipe_item == 'ongkir')
                    @php $totalOngkir += $detail->subtotal; @endphp
                @else
                    @php $totalProduk += $detail->subtotal; @endphp
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Total -->
    <div class="total-section">
        <table class="total-table">
            <tr>
                <td class="label">Total Produk:</td>
                <td class="amount">{{ format_uang($totalProduk) }}</td>
            </tr>
            <tr>
                <td class="label">Diskon ({{ $poPenjualan->diskon }}%):</td>
                <td class="amount">- {{ format_uang($totalProduk * $poPenjualan->diskon / 100) }}</td>
            </tr>
            <tr>
                <td class="label">Total Setelah Diskon:</td>
                <td class="amount">{{ format_uang($totalProduk - ($totalProduk * $poPenjualan->diskon / 100)) }}</td>
            </tr>
            <tr>
                <td class="label">Ongkos Kirim:</td>
                <td class="amount">{{ format_uang($totalOngkir) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL BAYAR:</td>
                <td class="amount">{{ format_uang($poPenjualan->bayar) }}</td>
            </tr>
            <tr>
                <td class="label">Diterima:</td>
                <td class="amount">{{ format_uang($poPenjualan->diterima) }}</td>
            </tr>
            <tr>
                <td class="label">Sisa:</td>
                <td class="amount">{{ format_uang($poPenjualan->bayar - $poPenjualan->diterima) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <!-- Footer dan Tanda Tangan -->
    <div class="footer">
        <div class="section">
            <div class="section-title">Keterangan</div>
            <p>
                Status: <strong>{{ strtoupper($poPenjualan->status) }}</strong><br>
                PO dibuat oleh: {{ $poPenjualan->user->name ?? '-' }}<br>
                Tanggal dibuat: {{ tanggal_indonesia($poPenjualan->created_at, true) }}
            </p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Customer</div>
            </div>
            <div class="signature-box" style="float: right;">
                <div class="signature-line"></div>
                <div>Hormat Kami,<br>{{ $setting->nama_perusahaan ?? 'PT. SUN TRAVEL' }}</div>
            </div>
        </div>
    </div>

    <!-- Term and Conditions -->
    <div class="page-break"></div>
    <div class="section">
        <div class="section-title">Syarat dan Ketentuan</div>
        <ol>
            <li>Purchase Order ini sah dan mengikat setelah ditandatangani oleh kedua belah pihak</li>
            <li>Pembayaran dilakukan sesuai dengan ketentuan yang disepakati</li>
            <li>Pengiriman barang/jasa dilakukan setelah konfirmasi pembayaran</li>
            <li>Komplain dapat diajukan maksimal 7 hari setelah barang/jasa diterima</li>
            <li>Perubahan PO harus disetujui secara tertulis oleh kedua belah pihak</li>
            <li>Force majeure: Pihak yang terkena dampak force majeure tidak bertanggung jawab atas keterlambatan</li>
        </ol>
    </div>
</body>
</html>
