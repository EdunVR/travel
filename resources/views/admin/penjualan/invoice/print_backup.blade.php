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
        @elseif($template === 'modern')
        /* Modern Template */
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.5;
            color: #2c3e50;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .print-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 28px;
            font-weight: bold;
            text-align: right;
            color: #fff;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .bank-accounts {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .bank-section-title {
            color: #667eea;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .print-footer {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        @media print {
            body {
                background: white !important;
            }
            .print-header, table, .bank-accounts, .print-footer {
                box-shadow: none !important;
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
            font-weight: bold;
            color: #2c3e50;
        }
        
        .account-number {
            font-family: monospace;
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
                    <td class="text-right">{{ $item->kuantitas }}</td>
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
                    <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d/m/Y') }}
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
                <td class="text-center">{{ number_format($item->kuantitas, 2) }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($item->diskon > 0)
                    {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
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

    <!-- Informasi Pembayaran Bank -->
    @if($companySettings['bank_name'] || $companySettings['bank_account_number'])
    <div class="bank-accounts">
        <div class="bank-section-title">INFORMASI PEMBAYARAN</div>
        <div style="padding: 10px; background: #f8f9fa; border-radius: 5px;">
            @if($companySettings['bank_name'])
            <span style="font-weight: bold; color: #667eea;">{{ $companySettings['bank_name'] }}</span><br>
            @endif
            
            @if($companySettings['bank_account_number'])
            <span style="font-family: monospace; font-size: 14px;">No. Rek: {{ $companySettings['bank_account_number'] }}</span><br>
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
            @if($companySettings['logo_url'])
            <img src="{{ $companySettings['logo_url'] }}" 
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
                    <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d/m/Y') }}
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <strong>Kepada:</strong><br>
                    @if($invoice->member)
                        {{ $invoice->member->nama }}<br>
                        {{ $invoice->member->alamat }}<br>
                    @elseif($invoice->prospek)
                        {{ $invoice->prospek->nama }}<br>
                        {{ $invoice->prospek->alamat }}<br>
                        /* Telp: {{ $invoice->prospek->telepon }} */
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
                <td class="text-center">{{ number_format($item->kuantitas, 2) }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($item->diskon > 0)
                    {{ number_format($item->diskon * $item->kuantitas, 0, ',', '.') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
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
        <!-- Legal Information -->
        @if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup'] || $companySettings['tdp'])
        <div style="margin-bottom: 20px; font-size: 10px; color: #6c757d; text-align: center;">
            @if($companySettings['npwp'])
                NPWP: {{ $companySettings['npwp'] }}
            @endif
            
            @if($companySettings['nib'])
                @if($companySettings['npwp']) | @endif
                NIB: {{ $companySettings['nib'] }}
            @endif
            
            @if($companySettings['siup'])
                @if($companySettings['npwp'] || $companySettings['nib']) | @endif
                SIUP: {{ $companySettings['siup'] }}
            @endif
            
            @if($companySettings['tdp'])
                @if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup']) | @endif
                TDP: {{ $companySettings['tdp'] }}
            @endif
        </div>
        @endif
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-left"></div>
            <div class="signature-right">
                <div style="margin-bottom: 10px;">
                    {{ $companySettings['company_address'] ? explode(',', $companySettings['company_address'])[0] : 'Jakarta' }}, 
                    {{ $invoice->tanggal->format('d F Y') }}
                </div>
                <div style="margin-bottom: 60px;">Hormat Kami,</div>
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
