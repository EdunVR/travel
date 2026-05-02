<!DOCTYPE html>
<html>
<head>
    <title>PREVIEW INVOICE SERVICE - {{ $invoice->no_invoice }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(255, 0, 0, 0.1);
            z-index: -1;
            font-weight: bold;
            pointer-events: none;
        }
        
        .preview-notice {
            background-color: #fffacd;
            border: 2px solid #ffeb3b;
            padding: 8px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            color: #d35400;
            font-size: 11px;
            border-radius: 4px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .logo {
            width: 60px;
            height: auto;
            float: left;
            margin-right: 15px;
        }
        
        .company-info {
            overflow: hidden;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .invoice-detail {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .customer-info {
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 8px 6px;
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
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .bank-info {
            border: 1px solid #000;
            padding: 10px;
            background-color: #f8f9fa;
            font-size: 11px;
            margin-bottom: 20px;
        }
        
        .signature {
            display: table;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px 0;
            width: 80%;
            display: inline-block;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
        
        .avoid-break {
            page-break-before: avoid;
        }
        
        .preview-header {
            background-color: #e74c3c;
            color: white;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    @if($invoice->is_preview ?? false)
    <div class="watermark">PREVIEW</div>
    <div class="preview-header">
        INVOICE PREVIEW - BELUM DISIMPAN
    </div>
    @endif
    
    <div class="header keep-together">
        <div class="header-left">
            <img src="{{ public_path('img/logo-ghava.png') }}" class="logo" alt="Logo">
            <div class="company-info">
                <div class="company-name">PT. GHAVA SHANKARA NUSANTARA</div>
                <div class="company-address">
                    {{ $setting->alamat ?? 'Komplek LIK Blok B2, Kec. Panyileukan, Kota Bandung' }}<br>
                    Telp: {{ $setting->telepon ?? '0812-220-033' }} | 
                    Email: {{ $setting->email ?? 'marketing@dahana-boiler.com' }}
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="invoice-info">
                <div class="invoice-title">INVOICE SERVICE</div>
                <div class="invoice-detail">
                    <strong>No:</strong> {{ $invoice->no_invoice }}<br>
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') }}<br>
                    @if($invoice->is_preview ?? false)
                    <strong>Status:</strong> <span style="color: #e74c3c;">PREVIEW</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="customer-info keep-together">
        <div><strong>Kepada:</strong></div>
        <div><strong>{{ $invoice->member->nama }}</strong></div>
        <div>{{ $invoice->member->alamat }}</div>
        <div>Telp: {{ $invoice->member->telepon }}</div>
    </div>

    <div class="service-info keep-together">
        <div><strong>Keperluan Service:</strong> {{ $invoice->jenis_service }}</div>
        @if(isset($invoice->mesinCustomer) && $invoice->mesinCustomer && $invoice->mesinCustomer->produk->count() > 0)
            <div><strong>Service Mesin:</strong> 
                {{ $invoice->mesinCustomer->produk->pluck('nama_produk')->implode(', ') }}
            </div>
        @elseif($invoice->jenis_service === 'Service' && isset($invoice->mesinCustomer) && $invoice->mesinCustomer)
            <div><strong>Service Mesin:</strong> 
                {{ $invoice->mesinCustomer->produk->pluck('nama_produk')->implode(', ') }}
            </div>
        @endif
        @if($invoice->is_preview ?? false)
        <div style="color: #e74c3c; font-weight: bold; margin-top: 5px;">
            ⓘ Dokumen ini adalah preview. Invoice akan resmi setelah disimpan.
        </div>
        @endif
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Deskripsi</th>
                    <th width="20%">Keterangan</th>
                    <th width="8%" class="text-center">Qty</th>
                    <th width="10%" class="text-center">Satuan</th>
                    <th width="12%" class="text-right">Harga (Rp)</th>
                    <th width="15%" class="text-right">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr class="avoid-break">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td>
                        @if(isset($item->jenis_kendaraan) && $item->jenis_kendaraan && $item->tipe == 'ongkir')
                            Menggunakan {{ $item->jenis_kendaraan }}
                        @else
                            {{ $item->keterangan }}
                        @endif
                        @if(isset($item->is_sparepart) && $item->is_sparepart && isset($item->kode_sparepart))
                            <br><small class="text-muted">Kode: {{ $item->kode_sparepart }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->kuantitas }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row avoid-break">
                    <td colspan="6" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer keep-together">
        <div class="payment-deadline" style="margin-bottom: 15px; padding: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
            <small style="font-size: 10px;">
                <strong>* Catatan:</strong> Batas terakhir pembayaran adalah 7 hari setelah selesai service. 
                Invoice ini harus dilunasi paling lambat tanggal 
                <strong>{{ \Carbon\Carbon::parse($invoice->tanggal_selesai_service)->addDays(7)->format('d/m/Y') }}</strong>.
            </small>
        </div>
        <div class="signature">
            <div class="signature-box">
                <div>Hormat Kami</div>
                <div style="margin-bottom: 0px;">admin</div>
                <div style="margin-bottom: 0px;">
                    <img src="{{ public_path('img/tiktik.png') }}" alt="Tanda Tangan" style="height: 80px; width: auto;">
                </div>
                <div>Tiktik Atikasari</div>
            </div>
        </div>
        
        <div class="bank-info">
            <strong>TRANSFER REKENING KE:</strong><br>
            <strong>Atas nama:</strong> PT. Ghava Shankara Nusantara<br>
            <strong>Bank:</strong> Mandiri<br>
            <strong>No Rekening:</strong> 1300027168247
        </div>
        
        @if($invoice->is_preview ?? false)
        <div class="preview-notice">
            <strong>PERHATIAN:</strong> Invoice ini masih dalam tahap preview dan belum disimpan ke sistem. 
            Simpan invoice terlebih dahulu untuk mendapatkan dokumen resmi.
        </div>
        @endif
    </div>
</body>
</html>
