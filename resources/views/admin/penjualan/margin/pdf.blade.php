<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Margin & Profit</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            margin: 15px;
            padding: 10px;
        }
        
        /* Header Styles - Same as Inter Outlet Print */
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

        /* Filter Info Section */
        .filter-section {
            margin: 15px 0;
        }
        
        .filter-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .filter-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: top;
        }
        
        .filter-header {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            width: 25%;
        }
        
        .filter-content {
            width: 25%;
        }

        /* Summary Section */
        .summary-section {
            margin: 15px 0;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }
        
        .data-table td {
            font-size: 8px;
        }
        
        /* Column widths */
        .col-no { width: 3%; }
        .col-source { width: 6%; }
        .col-date { width: 8%; }
        .col-outlet { width: 12%; }
        .col-product { width: 20%; }
        .col-qty { width: 5%; }
        .col-hpp { width: 8%; }
        .col-price { width: 8%; }
        .col-subtotal { width: 10%; }
        .col-profit { width: 10%; }
        .col-margin { width: 6%; }
        .col-payment { width: 4%; }
        
        /* Text alignment */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-invoice {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-pos {
            background-color: #cffafe;
            color: #0e7490;
        }
        
        .badge-inter {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        
        .badge-cash {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-qris {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-bon {
            background-color: #fed7aa;
            color: #9a3412;
        }
        
        .margin-high {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .margin-medium {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .margin-low {
            background-color: #fed7aa;
            color: #9a3412;
        }
        
        .margin-negative {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .profit-positive {
            color: #16a34a;
            font-weight: bold;
        }
        
        .profit-negative {
            color: #dc2626;
            font-weight: bold;
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
                font-size: 8px; 
                margin: 10px;
                padding: 5px;
            }
            .data-table { font-size: 7px; }
            .data-table th, .data-table td { padding: 2px 3px; }
        }
    </style>
</head>
<body>
    <!-- DEBUG INFO - Remove after testing -->
    <!-- DEBUG: Logo URL = {{ $companySettings['logo_url'] ?? 'NOT SET' }} -->
    <!-- DEBUG: Company Name = {{ $companySettings['company_name'] ?? 'NOT SET' }} -->
    <!-- DEBUG: Outlet ID = {{ session('selected_outlet_id', 'NOT SET') }} -->
    <!-- DEBUG: Company Settings Keys = {{ implode(', ', array_keys($companySettings ?? [])) }} -->
    <!-- DEBUG: Full Company Settings = {{ json_encode($companySettings ?? []) }} -->
    
    <!-- Header Section - Same structure as Inter Outlet Print -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <!-- DEBUG: Checking logo conditions -->
                    <!-- DEBUG: isset companySettings logo_url = {{ isset($companySettings['logo_url']) ? 'TRUE' : 'FALSE' }} -->
                    <!-- DEBUG: logo_url value = {{ $companySettings['logo_url'] ?? 'NULL' }} -->
                    <!-- DEBUG: logo_base64 available = {{ isset($companySettings['logo_base64']) ? 'TRUE' : 'FALSE' }} -->
                    <!-- DEBUG: logo_url not empty = {{ !empty($companySettings['logo_url']) ? 'TRUE' : 'FALSE' }} -->
                    
                    @if(isset($companySettings['logo_base64']) && $companySettings['logo_base64'])
                        <!-- Use base64 encoded logo (most reliable for PDF) -->
                        <img src="{{ $companySettings['logo_base64'] }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                        <!-- DEBUG: Base64 logo rendered -->
                    @elseif(isset($companySettings['logo_url']) && $companySettings['logo_url'])
                        @php
                            $logoUrl = $companySettings['logo_url'];
                            // Convert HTTPS to HTTP for PDF compatibility if needed
                            if (str_starts_with($logoUrl, 'https://')) {
                                $logoUrlHttp = str_replace('https://', 'http://', $logoUrl);
                            } else {
                                $logoUrlHttp = $logoUrl;
                            }
                        @endphp
                        
                        <!-- DEBUG: Original logo URL = {{ $logoUrl }} -->
                        <!-- DEBUG: HTTP logo URL = {{ $logoUrlHttp }} -->
                        
                        <!-- Try HTTP URL for better PDF compatibility -->
                        <img src="{{ $logoUrlHttp }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                        <!-- DEBUG: URL logo rendered (HTTP) -->
                    @else
                        <div class="company-logo">LOGO</div>
                        <!-- DEBUG: Logo placeholder rendered -->
                    @endif
                </td>
                <td class="company-info">
                    <div class="company-name">{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</div>
                    <div class="form-title">LAPORAN MARGIN & PROFIT</div>
                </td>
                <td class="document-info">
                    <table class="doc-info-table">
                        <tr>
                            <td class="doc-label">Outlet</td>
                            <td>{{ $outletName }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Periode</td>
                            <td>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Total Item</td>
                            <td>{{ number_format($summary['total_items']) }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Dicetak</td>
                            <td>{{ $generatedAt }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Filter Information -->
    <div class="filter-section">
        <table class="filter-table">
            <tr>
                <td class="filter-header">FILTER OUTLET</td>
                <td class="filter-header">FILTER TANGGAL</td>
                <td class="filter-header">FILTER PENCARIAN</td>
                <td class="filter-header">SUMBER DATA</td>
            </tr>
            <tr>
                <td class="filter-content">
                    {{ $outletName }}
                </td>
                <td class="filter-content">
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </td>
                <td class="filter-content">
                    {{ $search ? $search : 'Semua Data' }}
                </td>
                <td class="filter-content">
                    Invoice: {{ $summary['total_invoice'] }}<br>
                    POS: {{ $summary['total_pos'] }}<br>
                    Inter Outlet: {{ $summary['total_inter_outlet'] }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total HPP</td>
                <td class="summary-value">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Penjualan</td>
                <td class="summary-value">Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Profit</td>
                <td class="summary-value">Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-total">
                <td class="summary-label">RATA-RATA MARGIN</td>
                <td class="summary-value">{{ number_format($summary['avg_margin'] ?? 0, 2) }}%</td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-source">Source</th>
                <th class="col-date">Tanggal</th>
                <th class="col-outlet">Outlet</th>
                <th class="col-product">Produk</th>
                <th class="col-qty">Qty</th>
                <th class="col-hpp">HPP</th>
                <th class="col-price">Harga</th>
                <th class="col-subtotal">Subtotal</th>
                <th class="col-profit">Profit</th>
                <th class="col-margin">Margin</th>
                <th class="col-payment">Pay</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marginData as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                    @if($item['source'] === 'invoice')
                        <span class="badge badge-invoice">INV</span>
                    @elseif($item['source'] === 'pos')
                        <span class="badge badge-pos">POS</span>
                    @else
                        <span class="badge badge-inter">INT</span>
                    @endif
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/y') }}</td>
                <td class="text-left">{{ $item['outlet'] }}</td>
                <td class="text-left">{{ $item['produk'] }}</td>
                <td class="text-center">{{ number_format($item['qty'], 0) }}</td>
                <td class="text-right">{{ number_format($item['hpp'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['harga_jual'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right {{ ($item['profit'] ?? 0) >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ $item['profit'] !== null ? number_format($item['profit'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-center">
                    @if($item['margin_pct'] !== null)
                        @php
                            $marginClass = 'margin-negative';
                            if ($item['margin_pct'] >= 30) {
                                $marginClass = 'margin-high';
                            } elseif ($item['margin_pct'] >= 15) {
                                $marginClass = 'margin-medium';
                            } elseif ($item['margin_pct'] >= 5) {
                                $marginClass = 'margin-low';
                            }
                        @endphp
                        <span class="badge {{ $marginClass }}">{{ number_format($item['margin_pct'], 1) }}%</span>
                    @else
                        <span class="badge margin-negative">N/A</span>
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $paymentClass = 'badge-cash';
                        $paymentLabel = $item['payment_type'] ?? 'Cash';
                        if (strtolower($paymentLabel) === 'qris') {
                            $paymentClass = 'badge-qris';
                        } elseif (strtolower($paymentLabel) === 'bon') {
                            $paymentClass = 'badge-bon';
                        } elseif (strtolower($paymentLabel) === 'transfer') {
                            $paymentClass = 'badge-qris';
                        }
                    @endphp
                    <span class="badge {{ $paymentClass }}">{{ strtoupper(substr($paymentLabel, 0, 3)) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Catatan:</strong> Margin dihitung berdasarkan (Profit / Subtotal) × 100%. Data yang ditampilkan sesuai dengan filter yang diterapkan.</p>
        <p>Dokumen ini dicetak secara otomatis pada {{ $generatedAt }}</p>
        <p>{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }} - Sistem ERP Terintegrasi</p>
    </div>
</body>
</html>
