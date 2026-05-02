<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca Saldo - {{ $outlet_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            margin: 20mm 15mm 20mm 15mm;
        }
        
        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e40af;
            position: relative;
        }
        
        .logo-section {
            display: table-cell;
            width: 80px;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .logo-section img {
            width: 70px;
            height: auto;
            max-height: 70px;
            object-fit: contain;
        }
        
        .company-info {
            display: table-cell;
            vertical-align: top;
            text-align: center;
            width: auto;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-address {
            font-size: 10pt;
            color: #555;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .company-contact {
            font-size: 9pt;
            color: #666;
            margin-bottom: 15px;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }
        
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-period {
            font-size: 11pt;
            color: #475569;
            font-weight: 600;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 6px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 120px;
            padding: 4px 0;
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            display: table-cell;
            padding: 4px 0;
            color: #1f2937;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .main-table thead {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }
        
        .main-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .main-table th:last-child {
            border-right: none;
        }
        
        .main-table th.text-right {
            text-align: right;
        }
        
        .main-table th.text-center {
            text-align: center;
        }
        
        .main-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .main-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .main-table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .main-table td {
            padding: 10px;
            font-size: 9pt;
            vertical-align: middle;
        }
        
        .main-table td.text-right {
            text-align: right;
        }
        
        .main-table td.text-center {
            text-align: center;
        }
        
        .account-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1e40af;
            background: #eff6ff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
        }
        
        .account-name {
            color: #374151;
            font-weight: 500;
        }
        
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .type-asset {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        
        .type-liability {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .type-equity {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #6b21a8;
            border: 1px solid #c4b5fd;
        }
        
        .type-revenue {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid #86efac;
        }
        
        .type-expense {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
            border: 1px solid #fb923c;
        }
        
        .amount-debit {
            color: #059669;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        .amount-credit {
            color: #dc2626;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        .amount-balance {
            color: #1e293b;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }
        
        .amount-negative {
            color: #dc2626;
        }
        
        .main-table tfoot {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            color: white;
        }
        
        .main-table tfoot td {
            padding: 12px 10px;
            font-weight: 700;
            font-size: 10pt;
            border-top: 2px solid #6b7280;
        }
        
        .summary-section {
            display: table;
            width: 100%;
            margin-top: 25px;
        }
        
        .summary-left {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }
        
        .summary-right {
            display: table-cell;
            width: 50%;
            padding-left: 15px;
            vertical-align: top;
        }
        
        .summary-box {
            padding: 15px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .summary-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 12px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 10pt;
            margin-top: 5px;
            padding-top: 10px;
            border-top: 2px solid #cbd5e1;
        }
        
        .summary-label {
            font-weight: 600;
            color: #475569;
        }
        
        .summary-value {
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        .balanced {
            color: #059669;
        }
        
        .unbalanced {
            color: #dc2626;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
        }
        
        .footer p {
            margin-bottom: 3px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 15mm 10mm 15mm 10mm;
            }
            
            .main-table tbody tr:hover {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>
    <!-- Letterhead Header -->
    <div class="letterhead">
        @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
        <div class="logo-section">
            <img src="{{ $companySettings['logo_url'] }}" alt="Company Logo">
        </div>
        @endif
        <div class="company-info">
            <div class="company-name">{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</div>
            @if(isset($companySettings['company_address']) && $companySettings['company_address'])
            <div class="company-address">{{ $companySettings['company_address'] }}</div>
            @endif
            <div class="company-contact">
                @if(isset($companySettings['company_phone']) && $companySettings['company_phone'])
                    Telp: {{ $companySettings['company_phone'] }}
                @endif
                @if(isset($companySettings['company_email']) && $companySettings['company_email'])
                    @if(isset($companySettings['company_phone']) && $companySettings['company_phone']) | @endif
                    Email: {{ $companySettings['company_email'] }}
                @endif
                @if(isset($companySettings['company_website']) && $companySettings['company_website'])
                    @if((isset($companySettings['company_phone']) && $companySettings['company_phone']) || (isset($companySettings['company_email']) && $companySettings['company_email'])) | @endif
                    {{ $companySettings['company_website'] }}
                @endif
            </div>
        </div>
    </div>

    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">Neraca Saldo (Trial Balance)</div>
        <div class="report-period">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        </div>
    </div>
    
    <!-- Report Information -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Outlet:</div>
            <div class="info-value">{{ $outlet_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Buku:</div>
            <div class="info-value">{{ $book_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ $print_date }}</div>
        </div>
    </div>
    
    <!-- Main Data Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;">Kode</th>
                <th style="width: 30%;">Nama Akun</th>
                <th class="text-center" style="width: 10%;">Tipe</th>
                <th class="text-right" style="width: 12.5%;">Saldo Awal</th>
                <th class="text-right" style="width: 12.5%;">Debit</th>
                <th class="text-right" style="width: 12.5%;">Kredit</th>
                <th class="text-right" style="width: 12.5%;">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trialBalanceData as $account)
            <tr>
                <td><span class="account-code">{{ $account['code'] }}</span></td>
                <td class="account-name">{{ $account['name'] }}</td>
                <td class="text-center">
                    @php
                        $typeClass = 'type-asset';
                        $typeLabel = 'Aset';
                        
                        switch($account['type']) {
                            case 'liability':
                                $typeClass = 'type-liability';
                                $typeLabel = 'Kewajiban';
                                break;
                            case 'equity':
                                $typeClass = 'type-equity';
                                $typeLabel = 'Ekuitas';
                                break;
                            case 'revenue':
                            case 'otherrevenue':
                                $typeClass = 'type-revenue';
                                $typeLabel = 'Pendapatan';
                                break;
                            case 'expense':
                            case 'otherexpense':
                                $typeClass = 'type-expense';
                                $typeLabel = 'Beban';
                                break;
                        }
                    @endphp
                    <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                </td>
                <td class="text-right {{ $account['opening_balance'] < 0 ? 'amount-negative' : '' }}">
                    {{ number_format($account['opening_balance'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-debit">
                    {{ number_format($account['debit'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-credit">
                    {{ number_format($account['credit'], 0, ',', '.') }}
                </td>
                <td class="text-right amount-balance {{ $account['ending_balance'] < 0 ? 'amount-negative' : '' }}">
                    {{ number_format($account['ending_balance'], 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280; font-style: italic;">
                    Tidak ada data neraca saldo untuk periode yang dipilih
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($trialBalanceData) > 0)
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: 700;">TOTAL</td>
                <td class="text-right">{{ number_format($summary['total_debit'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($summary['total_credit'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($summary['total_debit'] - $summary['total_credit'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    @if(count($trialBalanceData) > 0)
    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-left">
            <div class="summary-box">
                <div class="summary-title">Ringkasan Saldo</div>
                <div class="summary-item">
                    <span class="summary-label">Total Debit:</span>
                    <span class="summary-value amount-debit">Rp {{ number_format($summary['total_debit'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Kredit:</span>
                    <span class="summary-value amount-credit">Rp {{ number_format($summary['total_credit'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Selisih:</span>
                    <span class="summary-value {{ $summary['is_balanced'] ? 'balanced' : 'unbalanced' }}">
                        Rp {{ number_format($summary['difference'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="summary-right">
            <div class="summary-box">
                <div class="summary-title">Status Neraca</div>
                <div class="summary-item">
                    <span class="summary-label">Kondisi:</span>
                    <span class="summary-value {{ $summary['is_balanced'] ? 'balanced' : 'unbalanced' }}">
                        {{ $summary['is_balanced'] ? '✓ Seimbang' : '⚠ Tidak Seimbang' }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Akun:</span>
                    <span class="summary-value">{{ count($trialBalanceData) }} Akun</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Periode:</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($start_date)->diffInDays(\Carbon\Carbon::parse($end_date)) + 1 }} Hari</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p><strong>{{ $companySettings['company_name'] ?? 'Nama Perusahaan' }}</strong> - Neraca Saldo</p>
        <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ $print_date }}</p>
        <p>Halaman ini merupakan dokumen resmi dan sah untuk keperluan akuntansi</p>
    </div>
</body>
</html>
