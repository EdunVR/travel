<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi Bulk</title>
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
            margin: 20px;
        }
        
        /* Header Styles - Same as QC Tofu */
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
            max-width: 80px;
            max-height: 80px;
            width: auto;
            height: auto;
        }
        
        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f5f5f5;
            line-height: 80px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            color: #999;
        }
        
        .company-info {
            width: 55%;
            text-align: center;
            vertical-align: middle;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .form-title {
            font-size: 14px;
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
        
        /* Content Styles */
        .filters {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }
        
        .filters h3 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        
        .filter-item {
            font-size: 9px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #6b7280;
        }
        
        .filter-value {
            color: #111827;
        }
        
        .summary {
            background-color: #eff6ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #bfdbfe;
            text-align: center;
        }
        
        .summary-text {
            font-size: 11px;
            font-weight: bold;
            color: #1e40af;
        }
        
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
        }
        
        th {
            background-color: #f0f0f0;
            color: #000;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 8px;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-in_progress {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .priority {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .priority-normal {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .priority-high {
            background-color: #fed7aa;
            color: #c2410c;
        }
        
        .priority-urgent {
            background-color: #fecaca;
            color: #dc2626;
        }
        
        .progress-bar {
            width: 40px;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #2563eb;
            transition: width 0.3s ease;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
        
        /* Responsive adjustments for PDF */
        @media print {
            body {
                font-size: 9px;
            }
            
            table {
                font-size: 7px;
            }
            
            th, td {
                padding: 4px 2px;
            }
        }
    </style>
</head>
<body>
    <!-- Header with Logo and Company Info -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <!-- Logo -->
                <td class="logo-cell">
                    @if($companyLogo && file_exists(storage_path('app/public/' . $companyLogo)))
                        <img src="{{ storage_path('app/public/' . $companyLogo) }}" alt="Company Logo" class="company-logo">
                    @else
                        <div class="logo-placeholder">LOGO</div>
                    @endif
                </td>
                
                <!-- Company Info -->
                <td class="company-info">
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="form-title">Laporan Produksi Bulk</div>
                </td>
                
                <!-- Document Info -->
                <td class="document-info">
                    <table class="doc-info-table">
                        <tr>
                            <td class="doc-label">No. Dokumen</td>
                            <td>LP-PROD-001</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Revisi</td>
                            <td>00</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Tanggal</td>
                            <td>{{ $filters['export_date'] }}</td>
                        </tr>
                        <tr>
                            <td class="doc-label">Halaman</td>
                            <td>1 dari 1</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Statistics Section -->
    <div class="filters" style="background-color: #eff6ff; border-color: #bfdbfe;">
        <h3 style="color: #1e40af;">Statistik Produksi:</h3>
        <div class="filter-grid">
            <div class="filter-item">
                <span class="filter-label">Total Target:</span>
                <span class="filter-value">{{ number_format($statistics['total_target']) }} unit</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Total Realisasi:</span>
                <span class="filter-value">{{ number_format($statistics['total_realized']) }} unit</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Total Reject:</span>
                <span class="filter-value" style="color: #dc2626;">{{ number_format($statistics['total_rejected']) }} unit</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Rata-rata HPP/Unit:</span>
                <span class="filter-value" style="color: #059669; font-weight: bold;">Rp {{ number_format($statistics['avg_hpp'], 0, ',', '.') }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Rata-rata Target:</span>
                <span class="filter-value">{{ number_format($statistics['avg_target'], 0, ',', '.') }} unit</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Rata-rata Realisasi:</span>
                <span class="filter-value">{{ number_format($statistics['avg_realized'], 0, ',', '.') }} unit</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Total Biaya Produksi:</span>
                <span class="filter-value" style="color: #1e40af; font-weight: bold;">Rp {{ number_format($statistics['total_cost'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Production Data Table -->
    @if($productions->count() > 0)
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Kode Produksi</th>
                    <th style="width: 15%;">Produk</th>
                    <th style="width: 8%;">Lini</th>
                    <th style="width: 8%;">Target</th>
                    <th style="width: 8%;">Realisasi</th>
                    <th style="width: 6%;">Reject</th>
                    <th style="width: 8%;">Progress</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 6%;">Prioritas</th>
                    <th style="width: 10%;">HPP/Unit</th>
                    <th style="width: 12%;">Total Biaya</th>
                    <th style="width: 10%;">Tgl Mulai</th>
                    <th style="width: 10%;">Tgl Selesai</th>
                    <th style="width: 10%;">Kadaluarsa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productions as $index => $production)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left font-bold">{{ $production['production_code'] }}</td>
                    <td class="text-left">{{ $production['product_name'] }}</td>
                    <td class="text-center">{{ $production['production_line'] }}</td>
                    <td class="text-right">{{ number_format($production['target_quantity']) }}</td>
                    <td class="text-right">{{ number_format($production['realized_quantity']) }}</td>
                    <td class="text-right">{{ number_format($production['rejected_quantity']) }}</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(100, $production['progress']) }}%;"></div>
                        </div>
                        <div style="font-size: 7px; margin-top: 2px;">{{ $production['progress'] }}%</div>
                    </td>
                    <td class="text-center">
                        <span class="status status-{{ $production['status'] }}">
                            {{ $production['status_text'] }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="priority priority-{{ $production['priority'] }}">
                            {{ $production['priority_text'] }}
                        </span>
                    </td>
                    <td class="text-right">{{ $production['hpp_per_unit_formatted'] }}</td>
                    <td class="text-right">{{ $production['total_cost_formatted'] }}</td>
                    <td class="text-center">{{ $production['start_date'] }}</td>
                    <td class="text-center">{{ $production['end_date'] }}</td>
                    <td class="text-center">{{ $production['expiry_date'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="no-data">
        <p>Tidak ada data produksi yang sesuai dengan filter yang diterapkan.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ $filters['export_date'] }}</p>
    </div>
</body>
</html>