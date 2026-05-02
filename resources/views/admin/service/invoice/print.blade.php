<!DOCTYPE html>
<html>
<head>
    <title>INVOICE SERVICE - {{ $invoice->no_invoice }}</title>
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

        .garansi-badge {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        del {
            color: #6c757d;
            text-decoration: line-through;
        }

        .total-garansi {
            color: #28a745;
            font-weight: bold;
        }

        tr.has-discount {
            background-color: #f8fff8 !important;
        }

        tr.has-discount td {
            border-left: 3px solid #28a745 !important;
        }

        .discount-text {
            color: #dc3545;
            font-weight: bold;
        }

        .strikethrough {
            text-decoration: line-through;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header keep-together">
        <div class="header-left">
            @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
            <img src="{{ request()->has('preview') ? $companySettings['logo_url'] : public_path(str_replace(url('/'), '', $companySettings['logo_url'])) }}" class="logo" alt="Company Logo">
            @endif
            <div class="company-info">
                <div class="company-name">{{ $companySettings['company_name'] }}</div>
                <div class="company-address">
                    @if($companySettings['company_address'])
                        {!! $companySettings['formatted_address'] !!}<br>
                    @endif
                    @if($companySettings['company_phone'])
                        Telp: {{ $companySettings['company_phone'] }}
                    @endif
                    @if($companySettings['company_email'])
                        @if($companySettings['company_phone']) | @endif
                        Email: {{ $companySettings['company_email'] }}
                    @endif
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="invoice-info">
                <div class="invoice-title">INVOICE SERVICE</div>
                <div class="invoice-detail">
                    <strong>No:</strong> {{ $invoice->no_invoice }}<br>
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="customer-info keep-together">
        <div><strong>Kepada:</strong></div>
        <div>
            <strong>
                @if(isset($invoice->member->kode_member) && $invoice->member->kode_member)
                    {{ $invoice->member->nama }} ({{ $invoice->getMemberCodeWithPrefix() }})
                @else
                    {{ $invoice->member->nama }}
                @endif
            </strong>
        </div>
        <div>{{ $invoice->member->alamat }}</div>
    </div>

    <div class="service-info keep-together">
        <div><strong>Keperluan Service:</strong> 
            {{ $invoice->jenis_service }}
            @if($invoice->service_lanjutan_ke > 0)
                <strong>(Service lanjutan ke-{{ $invoice->service_lanjutan_ke }})</strong>
            @endif
        </div>
        @if($invoice->id_invoice_sebelumnya && $invoice->invoiceSebelumnya)
            <div><strong>Invoice Sebelumnya:</strong> 
                {{ $invoice->invoiceSebelumnya->no_invoice }} 
                ({{ \Carbon\Carbon::parse($invoice->invoiceSebelumnya->tanggal)->format('d/m/Y') }})
            </div>
        @endif
        @if($invoice->keterangan_service)
            <div><strong>Keterangan:</strong> {{ $invoice->keterangan_service }}</div>
        @endif
        @if($invoice->tanggal_mulai_service && $invoice->tanggal_selesai_service)
        <div><strong>Periode Service:</strong> 
            {{ \Carbon\Carbon::parse($invoice->tanggal_mulai_service)->format('d/m/Y') }} - 
            {{ \Carbon\Carbon::parse($invoice->tanggal_selesai_service)->format('d/m/Y') }}
        </div>
        @endif
        @if(isset($invoice->mesinCustomer) && $invoice->mesinCustomer->produk->count() > 0)
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
                    <th width="12%" class="text-right">Diskon (Rp)</th>
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
                    <td class="text-right">
                        @if($item->diskon > 0)
                            <small style="text-decoration: line-through; color: #999;">
                                {{ number_format($item->harga, 0, ',', '.') }}
                            </small><br>
                            {{ number_format($item->harga_setelah_diskon, 0, ',', '.') }}
                        @else
                            {{ number_format($item->harga, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($item->diskon > 0)
                            -{{ number_format($item->diskon, 0, ',', '.') }}
                        @else
                            0
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @if($invoice->diskon > 0)
                <tr>
                    <td colspan="7" class="text-right"><strong>Total Sebelum Diskon</strong></td>
                    <td class="text-right">
                        <del>{{ number_format($invoice->total + $invoice->diskon, 0, ',', '.') }}</del>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><strong>Diskon</strong></td>
                    <td class="text-right">{{ number_format($invoice->diskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="7" class="text-right">
                        <strong>
                            @if($invoice->is_garansi)
                            TOTAL (GARANSI)
                            @else
                            TOTAL
                            @endif
                        </strong>
                    </td>
                    <td class="text-right">
                        <strong>
                            @if($invoice->is_garansi)
                            <span style="color: #28a745;">
                                <del>{{ number_format($invoice->total, 0, ',', '.') }}</del>
                            </span>
                            @else
                            {{ number_format($invoice->total, 0, ',', '.') }}
                            @endif
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer keep-together">
        @if(!$invoice->is_garansi)
        <div class="payment-deadline" style="margin-bottom: 15px; padding: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
            <small style="font-size: 10px;">
                <strong>* Catatan:</strong> Batas terakhir pembayaran adalah 7 hari setelah selesai service. 
                Invoice ini harus dilunasi paling lambat tanggal 
                <strong>{{ \Carbon\Carbon::parse($invoice->tanggal_selesai_service)->addDays(7)->format('d/m/Y') }}</strong>.
            </small>
        </div>
        @endif
        @if($invoice->tanggal_service_berikutnya)
        <div class="service-berikutnya-info" style="margin-top: 10px; padding: 8px; background-color: #e8f4fd; border: 1px solid #b8daff; border-radius: 4px;">
            <strong>Service Berikutnya Dijadwalkan:</strong> 
            {{ \Carbon\Carbon::parse($invoice->tanggal_service_berikutnya)->format('d F Y') }}
            @if($invoice->keterangan_service_berikutnya)
            <br><small>{{ $invoice->keterangan_service_berikutnya }}</small>
            @endif
        </div>
        @endif
        <div class="signature">
            <div class="signature-box">
                <div>Hormat Kami</div>
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
                
                <div>{{ auth()->user()->name ?? 'Admin' }}</div>
            </div>
        </div>
        @if(!$invoice->is_garansi && ($companySettings['bank_name'] || $companySettings['bank_account_number']))
        <div class="bank-info">
            <strong>TRANSFER REKENING KE:</strong><br>
            @if($companySettings['bank_account_name'])
            <strong>Atas nama:</strong> {{ $companySettings['bank_account_name'] }}<br>
            @endif
            @if($companySettings['bank_name'])
            <strong>Bank:</strong> {{ $companySettings['bank_name'] }}<br>
            @endif
            @if($companySettings['bank_account_number'])
            <strong>No Rekening:</strong> {{ $companySettings['bank_account_number'] }}
            @endif
        </div>
        @endif
        
        
    </div>
</body>
</html>
