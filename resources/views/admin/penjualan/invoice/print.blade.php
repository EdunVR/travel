<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        @if($template === 'pos_style')
        /* POS Style Template - Matching nota_besar.blade.php */
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td {
            font-size: 14px;
            padding: 2px;
            vertical-align: top;
        }
        
        table.data td,
        table.data th {
            border: 1px solid #000;
            padding: 3px;
        }
        
        table.data {
            border-collapse: collapse;
            width: 100%;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .header {
            margin-bottom: 10px;
        }
        
        .body {
            margin: 10px 0;
        }
        
        .footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
        
        .total-section {
            margin-top: 10px;
        }
        
        .date-trxid {
            margin-top: 10px;
        }
        
        .due-date-info {
            margin-top: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            font-size: 12px;
        }
        
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                margin: 0;
                padding: 10px;
            }
            th, td {
                padding: 2px 5px;
            }
        }
        @elseif($template === 'invoice_delivery')
        /* Invoice + Surat Jalan Template */
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 5px;
        }
        
        .page-divider {
            border-top: 2px dashed #000;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .section-invoice, .section-delivery {
            page-break-inside: avoid;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td {
            font-size: 11px;
            padding: 2px;
            vertical-align: top;
        }
        
        table.data td,
        table.data th {
            border: 1px solid #000;
            padding: 3px;
        }
        
        table.data {
            border-collapse: collapse;
            width: 100%;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .header {
            margin-bottom: 8px;
        }
        
        .body {
            margin: 8px 0;
        }
        
        .footer {
            margin-top: 8px;
        }
        
        .company-logo {
            width: 60px;
            height: auto;
            float: left;
            margin-right: 10px;
        }
        
        .total-section {
            margin-top: 8px;
        }
        
        .bank-info {
            margin-top: 8px;
            padding: 5px;
            border: 1px solid #000;
            background: #f8f9fa;
        }
        
        .signature-box {
            text-align: center;
            margin-top: 30px;
        }
        
        .signature-image {
            height: 50px;
            width: auto;
        }
        
        .stamp-image {
            position: absolute;
            height: 60px;
            width: auto;
            opacity: 0.8;
            margin-left: -160px;
            margin-top: -10px;
        }
        
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                margin: 0;
                padding: 5px;
            }
            .page-divider {
                page-break-after: avoid;
            }
        }
        @else
        /* Standard styles */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        /* Header styles */
        .print-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-logo {
            width: 80px;
            height: auto;
            float: left;
            margin-right: 15px;
            margin-top: 5px;
        }
        
        .company-info {
            overflow: hidden;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .company-address {
            font-size: 12px;
            line-height: 1.4;
            color: #34495e;
            margin-bottom: 3px;
        }
        
        .company-contact {
            font-size: 12px;
            color: #34495e;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #e74c3c;
        }
        
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Bank accounts styles */
        .bank-accounts {
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .bank-account-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }
        
        .bank-account-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .bank-name {
            color: #2c3e50;
            font-weight: normal;
        }
        
        .account-number {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            color: #34495e;
        }
        
        .account-holder {
            color: #7f8c8d;
            font-style: italic;
        }
        
        .bank-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        
        .due-date-info {
            margin-top: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            font-size: 12px;
        }
        
        /* Footer styles */
        .print-footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .signature-right {
            text-align: center;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(46, 204, 113, 0.1);
            font-weight: bold;
            z-index: -1;
        }
        
        @media print {
            .print-header, .print-footer {
                page-break-inside: avoid;
            }
            
            .company-logo {
                max-width: 80px !important;
                height: auto !important;
            }
        }
        @endif
    </style>
</head>
<body>
    @if($template === 'pos_style')
    {{-- POS Style Template --}}
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo Box -->
                <td style="width: 20%;">
                    <div class="logo-box">
                        {{ strtoupper($companySettings['company_name'] ?? 'PERUSAHAAN') }}
                    </div>
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <span style="font-size: 16px; font-weight: bold;">
                        @if($companySettings['company_phone'])
                            TELP: {{ $companySettings['company_phone'] }}
                        @else
                            {{ $companySettings['company_name'] }}
                        @endif
                    </span><br>
                    <span>INVOICE PENJUALAN</span>
                </td>
                <!-- Kolom Kanan: Barcode -->
                <td style="width: 20%; text-align: right;">
                    @php
                        $barcode = 'INV' . str_pad($invoice->id_sales_invoice, 6, '0', STR_PAD_LEFT);
                        $barcodeOptions = [
                            'height' => 15,
                            'width' => 1,
                        ];
                    @endphp
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <table>
            <tr>
                <!-- Kolom Kiri -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ $invoice->tanggal->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Nama Customer</td>
                            <td>: {{ $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Pelanggan Umum') }}</td>
                        </tr>
                        <tr>
                            <td>Tempo</td>
                            <td>: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                    </table>
                </td>
                <!-- Kolom Kanan -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>No Invoice</td>
                            <td>: {{ $invoice->no_invoice }}</td>
                        </tr>
                        <tr>
                            <td>Operator</td>
                            <td>: {{ $invoice->user->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: {{ strtoupper($invoice->status) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Detail Penjualan -->
    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jumlah</th>
                <th>Nama</th>
                <th>Harga Satuan</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td class="text-right">{{ number_format($item->kuantitas, 0) }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_normal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item->diskon > 0 ? 'Rp ' . number_format($item->diskon * $item->kuantitas, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <table>
            <tr>
                <td colspan="4" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</b></td>
            </tr>
            @if($invoice->total_diskon > 0)
            <tr>
                <td colspan="4" class="text-right"><b>Diskon</b></td>
                <td class="text-right"><b>Rp {{ number_format($invoice->total_diskon, 0, ',', '.') }}</b></td>
            </tr>
            @endif
            <tr>
                <td colspan="4" class="text-right"><b>Total Bayar</b></td>
                <td class="text-right"><b>Rp {{ number_format($invoice->total, 0, ',', '.') }}</b></td>
            </tr>
            @if($invoice->status === 'lunas')
            <tr>
                <td colspan="4" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>Rp {{ number_format($invoice->total, 0, ',', '.') }}</b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>Rp 0</b></td>
            </tr>
            @endif
        </table>
        
        <!-- Jatuh Tempo Info -->
        @if($invoice->due_date)
        <div class="due-date-info">
            <strong>Jatuh Tempo:</strong> 
            @php
                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                $today = \Carbon\Carbon::now();
                $diffInDays = (int) $today->diffInDays($dueDate, false);
            @endphp
            {{ $dueDate->format('d F Y') }}
            @if($diffInDays > 0)
                ({{ $diffInDays }} hari lagi)
            @elseif($diffInDays == 0)
                (Hari ini)
            @else
                (Terlambat {{ abs($diffInDays) }} hari)
            @endif
        </div>
        @endif
    </div>

    @if($invoice->keterangan)
    <div style="margin-top: 10px;">
        <b>Catatan:</b> {{ $invoice->keterangan }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <!-- Kolom Kiri: Tanda Terima -->
                <td style="width: 33%;">
                    <b>Tanda Terima</b><br>
                    ( {{ $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Pelanggan Umum') }} )
                </td>
                <!-- Kolom Tengah: Pesan -->
                <td style="width: 34%; text-align: center;">
                    <b>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</b>
                </td>
                <!-- Kolom Kanan: Hormat Kami -->
                <td style="width: 33%; text-align: right;">
                    <b>Hormat Kami</b><br>
                    ( {{ $invoice->user->name ?? 'System' }} )
                </td>
            </tr>
        </table>
    </div>

    @elseif($template === 'invoice_delivery')
    {{-- Invoice + Surat Jalan Template --}}
    
    {{-- BAGIAN ATAS: INVOICE --}}
    <div class="section-invoice">
        <!-- Header Invoice -->
        <div class="header">
            <table>
                <tr>
                    <!-- Kolom Kiri: Logo -->
                    <td style="width: 15%;">
                        @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                        <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" 
                             alt="Logo" 
                             class="company-logo">
                        @endif
                    </td>
                    <!-- Kolom Tengah: Informasi Perusahaan -->
                    <td style="width: 60%; text-align: center;">
                        <span style="font-size: 14px; font-weight: bold;">
                            {{ strtoupper($companySettings['company_name'] ?? 'PERUSAHAAN') }}
                        </span><br>
                        <span style="font-size: 11px;">
                            @if($companySettings['company_phone'])
                                TELP: {{ $companySettings['company_phone'] }}
                            @endif
                        </span><br>
                        <span style="font-size: 12px; font-weight: bold;">INVOICE PENJUALAN</span>
                    </td>
                    <!-- Kolom Kanan: Barcode -->
                    <td style="width: 25%; text-align: right;">
                        @php
                            $barcode = 'INV' . str_pad($invoice->id_sales_invoice, 6, '0', STR_PAD_LEFT);
                            $barcodeOptions = [
                                'height' => 15,
                                'width' => 1,
                            ];
                        @endphp
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body Invoice -->
        <div class="body">
            <table>
                <tr>
                    <!-- Kolom Kiri -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>Tanggal</td>
                                <td>: {{ $invoice->tanggal->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Nama Customer</td>
                                <td>: {{ $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Pelanggan Umum') }}</td>
                            </tr>
                            <tr>
                                <td>Tempo</td>
                                <td>: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kolom Kanan -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>No Invoice</td>
                                <td>: {{ $invoice->no_invoice }}</td>
                            </tr>
                            <tr>
                                <td>Operator</td>
                                <td>: {{ $invoice->user->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: {{ strtoupper($invoice->status) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Detail Penjualan -->
        <table class="data" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jumlah</th>
                    <th>Nama</th>
                    <th>Harga Satuan</th>
                    <th>Diskon</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $key => $item)
                    <tr>
                        <td class="text-center">{{ $key+1 }}</td>
                        <td class="text-right">{{ number_format($item->kuantitas, 0) }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_normal, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $item->diskon > 0 ? 'Rp ' . number_format($item->diskon * $item->kuantitas, 0, ',', '.') : '-' }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table>
                <tr>
                    <td colspan="4" class="text-right"><b>Total Harga</b></td>
                    <td class="text-right"><b>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</b></td>
                </tr>
                @if($invoice->total_diskon > 0)
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon</b></td>
                    <td class="text-right"><b>Rp {{ number_format($invoice->total_diskon, 0, ',', '.') }}</b></td>
                </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right"><b>Total Bayar</b></td>
                    <td class="text-right"><b>Rp {{ number_format($invoice->total, 0, ',', '.') }}</b></td>
                </tr>
            </table>
        </div>

        <!-- Informasi Pembayaran -->
        @if($bankAccounts && $bankAccounts->count() > 0)
        <div class="bank-info">
            <b>INFORMASI PEMBAYARAN:</b><br>
            @foreach($bankAccounts->take(2) as $bank)
                <span style="font-size: 10px;">
                    {{ $bank->bank_name }} - {{ $bank->account_number }} a/n {{ $bank->account_holder }}
                </span>
                @if(!$loop->last) | @endif
            @endforeach
        </div>
        @endif

        @if($invoice->keterangan)
        <div style="margin-top: 5px; font-size: 10px;">
            <b>Catatan:</b> {{ $invoice->keterangan }}
        </div>
        @endif

        <!-- Footer Invoice dengan Tanda Tangan dan Cap -->
        <div class="footer">
            <table>
                <tr>
                    <!-- Kolom Kiri: Tanda Terima -->
                    <td style="width: 33%;">
                        <b>Tanda Terima</b><br><br><br><br>
                        ( {{ $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Pelanggan Umum') }} )
                    </td>
                    <!-- Kolom Tengah: Pesan -->
                    <td style="width: 34%; text-align: center; font-size: 10px;">
                        <b>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</b>
                    </td>
                    <!-- Kolom Kanan: Hormat Kami dengan Tanda Tangan & Cap -->
                    <td style="width: 33%; text-align: center;">
                        <b>Hormat Kami</b><br>
                        <div class="signature-box" style="position: relative; display: inline-block;">
                            @if(auth()->user() && auth()->user()->signature_path)
                            <img src="{{ request()->has('preview') ? asset(auth()->user()->signature_path) : public_path(auth()->user()->signature_path) }}" 
                                 alt="Tanda Tangan" 
                                 class="signature-image">
                            @endif
                            
                            @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                            <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" 
                                 alt="Cap" 
                                 class="stamp-image">
                            @endif
                        </div><br>
                        ( {{ $invoice->user->name ?? 'System' }} )
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PEMBATAS HALAMAN --}}
    <div class="page-divider"></div>

    {{-- BAGIAN BAWAH: SURAT JALAN / PENGIRIMAN BARANG --}}
    <div class="section-delivery">
        <!-- Header Surat Jalan -->
        <div class="header">
            <table>
                <tr>
                    <!-- Kolom Kiri: Logo -->
                    <td style="width: 15%;">
                        @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                        <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" 
                             alt="Logo" 
                             class="company-logo">
                        @endif
                    </td>
                    <!-- Kolom Tengah: Informasi Perusahaan -->
                    <td style="width: 60%; text-align: center;">
                        <span style="font-size: 14px; font-weight: bold;">
                            {{ strtoupper($companySettings['company_name'] ?? 'PERUSAHAAN') }}
                        </span><br>
                        <span style="font-size: 11px;">
                            @if($companySettings['company_phone'])
                                TELP: {{ $companySettings['company_phone'] }}
                            @endif
                        </span><br>
                        <span style="font-size: 12px; font-weight: bold;">SURAT JALAN / PENGIRIMAN BARANG</span>
                    </td>
                    <!-- Kolom Kanan: Barcode -->
                    <td style="width: 25%; text-align: right;">
                        @php
                            $deliveryBarcode = 'SJ' . str_pad($invoice->id_sales_invoice, 6, '0', STR_PAD_LEFT);
                        @endphp
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($deliveryBarcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height']) }}" alt="barcode">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body Surat Jalan -->
        <div class="body">
            <table>
                <tr>
                    <!-- Kolom Kiri -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>No. Surat Jalan</td>
                                <td>: SJ-{{ $invoice->no_invoice }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Kirim</td>
                                <td>: {{ $invoice->tanggal->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>Ref. Invoice</td>
                                <td>: {{ $invoice->no_invoice }}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kolom Kanan -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td colspan="2"><b>Kepada:</b></td>
                            </tr>
                            <tr>
                                <td colspan="2">{{ $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Pelanggan Umum') }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    @if($invoice->member && $invoice->member->alamat)
                                        {{ $invoice->member->alamat }}
                                    @elseif($invoice->prospek && $invoice->prospek->alamat)
                                        {{ $invoice->prospek->alamat }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Barang yang Dikirim -->
        <table class="data" width="100%">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Nama Barang</th>
                    <th style="width: 15%;">Satuan</th>
                    <th style="width: 15%;">Jumlah</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $key => $item)
                    <tr>
                        <td class="text-center">{{ $key+1 }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-center">{{ number_format($item->kuantitas, 0) }}</td>
                        <td>-</td>
                    </tr>
                @endforeach
                {{-- Tambahkan baris kosong jika item kurang dari 5 --}}
                @for($i = count($invoice->items); $i < 5; $i++)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div style="margin-top: 5px; font-size: 10px;">
            <b>Catatan:</b> Harap periksa barang saat diterima. Kerusakan/kekurangan barang harap dilaporkan segera.
        </div>

        <!-- Footer Surat Jalan -->
        <div class="footer">
            <table>
                <tr>
                    <!-- Kolom Kiri: Pengirim -->
                    <td style="width: 33%; text-align: center;">
                        <b>Pengirim</b><br><br><br><br>
                        ( _________________ )
                    </td>
                    <!-- Kolom Tengah: Sopir/Kurir -->
                    <td style="width: 34%; text-align: center;">
                        <b>Sopir/Kurir</b><br><br><br><br>
                        ( _________________ )
                    </td>
                    <!-- Kolom Kanan: Penerima -->
                    <td style="width: 33%; text-align: center;">
                        <b>Penerima</b><br><br><br><br>
                        ( _________________ )
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @elseif($template === 'modern')
    {{-- Modern Template --}}
    <!-- Header Perusahaan -->
    <div class="print-header">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 70%; vertical-align: top;">
                <div class="company-name">
                    {{ $companySettings['company_name'] }}
                </div>
                
                @if($companySettings['company_address'])
                <div style="font-size: 14px; margin-bottom: 5px;">
                    {!! $companySettings['formatted_address'] !!}
                </div>
                @endif
                
                <div style="font-size: 14px;">
                    @if($companySettings['company_phone'])
                        <span>Telp: {{ $companySettings['company_phone'] }}</span>
                    @endif
                    
                    @if($companySettings['company_email'])
                        @if($companySettings['company_phone']) | @endif
                        <span>Email: {{ $companySettings['company_email'] }}</span>
                    @endif
                </div>
            </div>
            
            <div style="display: table-cell; width: 30%; vertical-align: top; text-align: right;">
                <div class="document-title">INVOICE</div>
                <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">
                    No: {{ $invoice->no_invoice }}
                </div>
                <div style="font-size: 14px;">
                    Tanggal: {{ $invoice->tanggal->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Invoice -->
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <table style="width: 100%; margin-bottom: 20px; border: none;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <strong style="color: #667eea;">Informasi Invoice:</strong><br>
                    <strong>No. Invoice:</strong> {{ $invoice->no_invoice }}<br>
                    <strong>Tanggal:</strong> {{ $invoice->tanggal->format('d/m/Y') }}<br>
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <strong style="color: #667eea;">Kepada:</strong><br>
                    @if($invoice->member)
                        {{ $invoice->member->nama }}<br>
                        {{ $invoice->member->alamat }}<br>
                        Telp: {{ $invoice->member->telepon }}
                    @elseif($invoice->prospek)
                        {{ $invoice->prospek->nama }}<br>
                        {{ $invoice->prospek->alamat }}<br>
                        Telp: {{ $invoice->prospek->telepon }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Items -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Deskripsi</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 15%;" class="text-right">Harga</th>
                <th style="width: 10%;" class="text-right">Diskon</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td class="text-center">{{ number_format($item->kuantitas, 0) }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($item->diskon > 0)
                    Rp {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr>
            <td style="width: 70%; border: none;"></td>
            <td style="width: 30%; border: none;">
                <table style="width: 100%; background: white; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 8px; background: #f8f9fa;"><strong>Subtotal:</strong></td>
                        <td style="padding: 8px; text-align: right; background: #f8f9fa;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->total_diskon > 0)
                    <tr>
                        <td style="padding: 8px;"><strong>Total Diskon:</strong></td>
                        <td style="padding: 8px; text-align: right;">- Rp {{ number_format($invoice->total_diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <td style="padding: 12px;"><strong>TOTAL:</strong></td>
                        <td style="padding: 12px; text-align: right; font-weight: bold;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Jatuh Tempo Info -->
    @if($invoice->due_date)
    <div class="due-date-info">
        <strong>Jatuh Tempo:</strong> 
        @php
            $dueDate = \Carbon\Carbon::parse($invoice->due_date);
            $today = \Carbon\Carbon::now();
            $diffInDays = (int) $today->diffInDays($dueDate, false);
        @endphp
        {{ $dueDate->format('d F Y') }}
        @if($diffInDays > 0)
            ({{ $diffInDays }} hari lagi)
        @elseif($diffInDays == 0)
            (Hari ini)
        @else
            (Terlambat {{ abs($diffInDays) }} hari)
        @endif
    </div>
    @endif

    <!-- Informasi Pembayaran Bank -->
    @if($companySettings['bank_name'] || $companySettings['bank_account_number'])
    <div class="bank-accounts">
        <div class="bank-section-title">INFORMASI PEMBAYARAN</div>
        <div style="padding: 10px; background: #f8f9fa; border-radius: 5px;">
            @if($companySettings['bank_name'])
            <span class="bank-name">{{ $companySettings['bank_name'] }}</span><br>
            @endif
            
            @if($companySettings['bank_account_number'])
            <span class="account-number">No. Rek: {{ $companySettings['bank_account_number'] }}</span><br>
            @endif
            
            @if($companySettings['bank_account_name'])
            <span style="color: #6c757d; font-style: italic;">a/n {{ $companySettings['bank_account_name'] }}</span>
            @endif
        </div>
    </div>
    @endif

    @if($invoice->keterangan)
    <div style="background: white; padding: 15px; border-radius: 8px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <strong style="color: #667eea;">Keterangan:</strong><br>
        {{ $invoice->keterangan }}
    </div>
    @endif

    <!-- Footer -->
    <div class="print-footer">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="margin-bottom: 10px;">
                {{ $companySettings['company_address'] ? explode(',', $companySettings['company_address'])[0] : 'Jakarta' }}, 
                {{ $invoice->tanggal->format('d F Y') }}
            </div>
            <div style="margin-bottom: 60px;">Hormat Kami,</div>
            <div style="border-top: 2px solid #667eea; width: 200px; margin: 0 auto; padding-top: 10px;">
                <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
            </div>
        </div>
    </div>

    @else
    {{-- Standard Template --}}
    <!-- Header Perusahaan -->
    <div class="print-header">
        <div class="header-left">
            @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
            <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" 
                 alt="Company Logo" 
                 class="company-logo">
            @endif
            
            <div class="company-info">
                <div class="company-name">
                    {{ $companySettings['company_name'] }}
                </div>
                
                @if($companySettings['company_code'])
                <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 3px;">
                    Kode: {{ $companySettings['company_code'] }}
                </div>
                @endif
                
                @if($companySettings['company_address'])
                <div class="company-address">
                    {!! $companySettings['formatted_address'] !!}
                </div>
                @endif
                
                <div class="company-contact">
                    @if($companySettings['company_phone'])
                        <span>Telp: {{ $companySettings['company_phone'] }}</span>
                    @endif
                    
                    @if($companySettings['company_email'])
                        @if($companySettings['company_phone']) | @endif
                        <span>Email: {{ $companySettings['company_email'] }}</span>
                    @endif
                    
                    @if($companySettings['company_website'])
                        @if($companySettings['company_phone'] || $companySettings['company_email']) | @endif
                        <span>{{ $companySettings['company_website'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="document-title">INVOICE</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">
                No: {{ $invoice->no_invoice }}
            </div>
            <div style="font-size: 12px; color: #7f8c8d;">
                Tanggal: {{ $invoice->tanggal->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- Informasi Invoice -->
    <div class="invoice-info" style="margin-bottom: 20px;">
        <table style="width: 100%; margin-bottom: 20px; border: none;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <strong>Informasi Invoice:</strong><br>
                    <strong>No. Invoice:</strong> {{ $invoice->no_invoice }}<br>
                    <strong>Tanggal:</strong> {{ $invoice->tanggal->format('d/m/Y') }}<br>
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <strong>Kepada:</strong><br>
                    @if($invoice->member)
                        {{ $invoice->member->nama }}<br>
                        {{ $invoice->member->alamat }}<br>
                    @elseif($invoice->prospek)
                        {{ $invoice->prospek->nama }}<br>
                        {{ $invoice->prospek->alamat }}<br>
                        {{-- Telp: {{ $invoice->prospek->telepon }} --}}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Items -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Deskripsi</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 15%;" class="text-right">Harga</th>
                <th style="width: 10%;" class="text-right">Diskon</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td class="text-center">{{ number_format($item->kuantitas, 0) }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($item->diskon > 0)
                    Rp {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr>
            <td style="width: 70%; border: none;"></td>
            <td style="width: 30%; border: none;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 5px;"><strong>Subtotal:</strong></td>
                        <td style="padding: 5px; text-align: right;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->total_diskon > 0)
                    <tr>
                        <td style="padding: 5px;"><strong>Total Diskon:</strong></td>
                        <td style="padding: 5px; text-align: right;">- Rp {{ number_format($invoice->total_diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr style="background-color: #ecf0f1;">
                        <td style="padding: 8px;"><strong>TOTAL:</strong></td>
                        <td style="padding: 8px; text-align: right; font-weight: bold;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Jatuh Tempo Info -->
    @if($invoice->due_date)
    <div class="due-date-info">
        <strong>Jatuh Tempo:</strong> 
        @php
            $dueDate = \Carbon\Carbon::parse($invoice->due_date);
            $today = \Carbon\Carbon::now();
            $diffInDays = (int) $today->diffInDays($dueDate, false);
        @endphp
        {{ $dueDate->format('d F Y') }}
        @if($diffInDays > 0)
            ({{ $diffInDays }} hari lagi)
        @elseif($diffInDays == 0)
            (Hari ini)
        @else
            (Terlambat {{ abs($diffInDays) }} hari)
        @endif
    </div>
    @endif

    <!-- Informasi Pembayaran Bank -->
    @if($companySettings['bank_name'] || $companySettings['bank_account_number'])
    <div class="bank-accounts">
        <div class="bank-section-title">INFORMASI PEMBAYARAN</div>
        <div class="bank-account-item">
            @if($companySettings['bank_name'])
            <span class="bank-name">{{ $companySettings['bank_name'] }}</span><br>
            @endif
            
            @if($companySettings['bank_account_number'])
            <span class="account-number">No. Rek: {{ $companySettings['bank_account_number'] }}</span><br>
            @endif
            
            @if($companySettings['bank_account_name'])
            <span class="account-holder">a/n {{ $companySettings['bank_account_name'] }}</span>
            @endif
        </div>
    </div>
    @endif

    <!-- Informasi Pembayaran untuk Transfer -->
    @if($invoice->jenis_pembayaran === 'transfer' && $invoice->hasBuktiTransfer())
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #27ae60; background: #d5f4e6; border-radius: 5px;">
        <strong>Pembayaran telah diterima via Transfer</strong><br>
        Bank: {{ $invoice->nama_bank }} | Pengirim: {{ $invoice->nama_pengirim }}<br>
        Jumlah: Rp {{ number_format($invoice->jumlah_transfer, 0, ',', '.') }} | Tanggal: {{ $invoice->tanggal_pembayaran->format('d/m/Y') }}
    </div>
    @endif

    <!-- Keterangan -->
    @if($invoice->keterangan)
    <div style="margin-top: 20px;">
        <strong>Keterangan:</strong><br>
        {{ $invoice->keterangan }}
    </div>
    @endif

    <!-- Footer -->
    <div class="print-footer">
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-left"></div>
            <div class="signature-right">
                <div style="margin-bottom: 10px;">
                    {{ $companySettings['company_address'] ? explode(',', $companySettings['company_address'])[0] : 'Jakarta' }}, 
                    {{ $invoice->tanggal->format('d F Y') }}
                </div>
                <div style="margin-bottom: 10px;">Hormat Kami,</div>
                <div style="margin-bottom: 10px;">{{ $companySettings['company_name'] }}</div>
                
                <!-- Signature with overlapping company stamp -->
                <div style="position: relative; display: inline-block; margin-bottom: 10px;">
                    <!-- User Signature (base layer) -->
                    @if(auth()->user() && auth()->user()->signature_path)
                    <img src="{{ request()->has('preview') ? asset(auth()->user()->signature_path) : public_path(auth()->user()->signature_path) }}" alt="Tanda Tangan" style="height: 60px; width: auto; display: block;">
                    @else
                    <div style="height: 60px; width: 120px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">
                        Tanda Tangan
                    </div>
                    @endif
                    
                    <!-- Company Logo/Stamp (overlapping 50% right side) -->
                    @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                    <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" alt="Company Stamp" 
                         style="position: absolute; top: 0; left: -10px; height: 80px; width: auto; opacity: 0.8; z-index: 10;">
                    @endif
                </div>
                
                <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto; padding-top: 5px;">
                    <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Footer Note -->
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d; border-top: 1px dashed #dee2e6; padding-top: 10px;">
            Dokumen ini dicetak secara otomatis oleh sistem {{ $companySettings['company_name'] }}
            <br>
            Tanggal cetak: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- Watermark untuk invoice lunas -->
    @if($invoice->status === 'lunas')
    <div class="watermark">LUNAS</div>
    @endif
    @endif
</body>
</html>