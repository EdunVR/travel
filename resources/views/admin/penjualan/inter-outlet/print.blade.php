<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan Antar Outlet - {{ $interOutletSale->no_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            margin: 20px;
            padding: 10px;
        }
        
        /* Header Styles - Same as QC Tofu Mentah */
        .header-container {
            width: 100%;
            border: 2px solid #000;
            margin-bottom: 15px;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f5f5f5;
            line-height: 60px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        
        .company-info {
            width: 55%;
            text-align: center;
            vertical-align: middle;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .form-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .document-info {
            width: 30%;
            vertical-align: top;
        }
        
        .doc-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .doc-info-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9px;
        }
        
        .doc-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 40%;
        }

        /* Transaction Info Section */
        .transaction-section {
            margin: 15px 0;
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .transaction-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: top;
        }
        
        .transaction-header {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            width: 33.33%;
        }
        
        .transaction-content {
            width: 33.33%;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }
        
        .items-table td {
            font-size: 9px;
        }
        
        /* Column widths */
        .col-no { width: 5%; }
        .col-code { width: 15%; }
        .col-name { width: 35%; }
        .col-qty { width: 10%; }
        .col-price { width: 15%; }
        .col-subtotal { width: 20%; }
        
        /* Text alignment */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Summary Section */
        .summary-section {
            margin-top: 15px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
        }
        
        .summary-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 70%;
            text-align: right;
        }
        
        .summary-value {
            width: 30%;
            text-align: right;
            font-weight: bold;
        }
        
        .summary-total {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 11px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Notes Section */
        .notes-section {
            margin-top: 15px;
        }
        
        .notes-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .notes-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 10px;
        }
        
        .notes-header {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 15%;
            vertical-align: top;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        /* Print optimizations */
        @media print {
            body { 
                font-size: 9px; 
                margin: 15px;
                padding: 5px;
            }
            .items-table { font-size: 8px; }
            .items-table th, .items-table td { padding: 2px 4px; }
        }
    </style>
</head>
<body>
    <!-- Header Section - Same structure as QC Tofu Mentah -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                        <img src="{{ $companySettings['logo_url'] }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                    @else
                        <div class="company-logo">LOGO</div>
                    @endif
                </td>
                <td class="company-info">
                    <div class="company-name">{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</div>
                    <div class="form-title">INVOICE PENJUALAN ANTAR OUTLET</div>
                </td>
                <td class="document-info">
                    <table class="doc-info-table">
                        <tr>
                            <td class="doc-label">No. Transaksi</td>
                            <td>{{ $interOutletSale->no_transaksi }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Status</td>
                            <td>
                                <span class="status-badge status-{{ $interOutletSale->status }}">
                                    {{ ucfirst($interOutletSale->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="doc-label">Tanggal</td>
                            <td>{{ $interOutletSale->tanggal->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Waktu</td>
                            <td>{{ $interOutletSale->tanggal->format('H:i') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Transaction Information -->
    <div class="transaction-section">
        <table class="transaction-table">
            <tr>
                <td class="transaction-header">OUTLET ASAL</td>
                <td class="transaction-header">OUTLET TUJUAN</td>
                <td class="transaction-header">INFORMASI TRANSAKSI</td>
            </tr>
            <tr>
                <td class="transaction-content">
                    <strong>{{ $interOutletSale->outletAsal->nama_outlet ?? '-' }}</strong><br>
                    {{ $interOutletSale->outletAsal->alamat ?? '-' }}<br>
                    @if($interOutletSale->outletAsal->telepon ?? false)
                        Telp: {{ $interOutletSale->outletAsal->telepon }}<br>
                    @endif
                </td>
                <td class="transaction-content">
                    <strong>{{ $interOutletSale->outletTujuan->nama_outlet ?? '-' }}</strong><br>
                    {{ $interOutletSale->outletTujuan->alamat ?? '-' }}<br>
                    @if($interOutletSale->outletTujuan->telepon ?? false)
                        Telp: {{ $interOutletSale->outletTujuan->telepon }}<br>
                    @endif
                </td>
                <td class="transaction-content">
                    <strong>User:</strong> {{ $interOutletSale->user->name ?? '-' }}<br>
                    <strong>Tanggal:</strong> {{ $interOutletSale->tanggal->format('d/m/Y H:i') }}<br>
                    @if($interOutletSale->approved_by)
                        <strong>Disetujui:</strong> {{ $interOutletSale->approvedBy->name ?? '-' }}<br>
                        <strong>Tgl Persetujuan:</strong> {{ $interOutletSale->approved_at ? $interOutletSale->approved_at->format('d/m/Y H:i') : '-' }}<br>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-code">Kode Produk</th>
                <th class="col-name">Nama Produk</th>
                <th class="col-qty">Qty</th>
                <th class="col-price">Harga Satuan</th>
                <th class="col-subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interOutletSale->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->produk->kode_produk ?? '-' }}</td>
                <td class="text-left">{{ $item->produk->nama_produk ?? 'Produk tidak ditemukan' }}</td>
                <td class="text-center">
                    {{ number_format($item->kuantitas, 2) }} 
                    {{ $item->produk->satuan->nama_satuan ?? 'pcs' }}
                </td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Subtotal</td>
                <td class="summary-value">Rp {{ number_format($interOutletSale->subtotal, 0, ',', '.') }}</td>
            </tr>
            
            @if($interOutletSale->total_diskon > 0)
            <tr>
                <td class="summary-label">
                    Diskon 
                    @if($interOutletSale->diskon_persen > 0)
                        ({{ number_format($interOutletSale->diskon_persen, 1) }}%)
                    @endif
                </td>
                <td class="summary-value" style="color: #dc2626;">-Rp {{ number_format($interOutletSale->total_diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            @if($interOutletSale->ppn > 0)
            <tr>
                <td class="summary-label">PPN</td>
                <td class="summary-value">Rp {{ number_format($interOutletSale->ppn, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <tr class="summary-total">
                <td class="summary-label">TOTAL</td>
                <td class="summary-value">Rp {{ number_format($interOutletSale->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Notes Section -->
    @if($interOutletSale->catatan)
    <div class="notes-section">
        <table class="notes-table">
            <tr>
                <td class="notes-header">Catatan:</td>
                <td>{{ $interOutletSale->catatan }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }} - Sistem ERP Terintegrasi</p>
    </div>
</body>
</html>