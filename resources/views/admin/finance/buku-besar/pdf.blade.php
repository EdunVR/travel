<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
            margin: 20mm 15mm 20mm 15mm;
        }
        
        /* Professional Letterhead */
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

        /* Report Header */
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
        
        .report-outlet {
            font-size: 12pt;
            color: #475569;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .filter-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 6px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }

        .filter-info-row {
            display: table-row;
        }

        .filter-info-label {
            display: table-cell;
            font-weight: 600;
            width: 120px;
            padding: 4px 0;
            color: #374151;
        }

        .filter-info-value {
            display: table-cell;
            padding: 4px 0;
            color: #1f2937;
        }
        
        .account-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 12px 15px;
            margin-top: 20px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 11pt;
            border-radius: 6px 6px 0 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .account-code {
            color: #fbbf24;
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }
        
        .account-type {
            font-size: 9pt;
            color: #e5e7eb;
            font-weight: normal;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 6px 6px;
            overflow: hidden;
        }
        
        table thead {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        table th:last-child {
            border-right: none;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            font-size: 9pt;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .opening-balance-row {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%) !important;
            font-weight: bold;
            color: #1e40af;
        }
        
        .account-total-row {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            font-weight: bold;
            border-top: 2px solid #94a3b8 !important;
        }
        
        .grand-total-row {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
            font-weight: bold;
            border-top: 3px solid #1e40af !important;
            font-size: 10pt;
            color: #1e40af;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .debit {
            color: #059669;
        }
        
        .credit {
            color: #DC2626;
        }
        
        .balance-positive {
            color: #2563eb;
        }
        
        .balance-negative {
            color: #ea580c;
        }
        
        .reference {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            color: #1e40af;
            background: #eff6ff;
            padding: 1px 4px;
            border-radius: 3px;
        }
        
        .book-name {
            font-size: 7pt;
            color: #64748b;
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
        
        .summary-box {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 8px 0;
            font-weight: bold;
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-row:last-child {
            border-bottom: none;
            border-top: 2px solid #cbd5e1;
            margin-top: 10px;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .spacer {
            height: 15px;
        }

        @media print {
            body {
                margin: 10mm 8mm 10mm 8mm;
            }
            
            table tbody tr:hover {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>
    <!-- Professional Letterhead -->
    <div class="letterhead">
        @if(isset($companySettings['logo_url']) && $companySettings['logo_url'])
        <div class="logo-section">
            <img src="{{ $companySettings['logo_url'] }}" alt="Company Logo">
        </div>
        @endif
        <div class="company-info">
            <div class="company-name">{{ $companySettings['company_name'] ?? $filters['company_name'] ?? 'Nama Perusahaan' }}</div>
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
        <div class="report-title">Buku Besar (General Ledger)</div>
        @if(isset($filters['outlet_name']))
        <div class="report-outlet">{{ $filters['outlet_name'] }}</div>
        @endif
    </div>

    @if(!empty($filters))
    <div class="filter-info">
        <div class="filter-info-row">
            <div class="filter-info-label">Informasi Laporan:</div>
            <div class="filter-info-value"></div>
        </div>
        @if(isset($filters['start_date']) && isset($filters['end_date']))
        <div class="filter-info-row">
            <div class="filter-info-label">Periode:</div>
            <div class="filter-info-value">{{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</div>
        </div>
        @endif
        @if(isset($filters['account_name']))
        <div class="filter-info-row">
            <div class="filter-info-label">Akun:</div>
            <div class="filter-info-value">{{ $filters['account_name'] }}</div>
        </div>
        @endif
        <div class="filter-info-row">
            <div class="filter-info-label">Tanggal Cetak:</div>
            <div class="filter-info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    @endif

    @php
        $grandTotalDebit = 0;
        $grandTotalCredit = 0;
        $grandBalance = 0;
    @endphp

    @if(isset($data['ledger_entries']) && count($data['ledger_entries']) > 0)
        @foreach($data['ledger_entries'] as $accountEntry)
            {{-- Account Header --}}
            <div class="account-header">
                <span class="account-code">{{ $accountEntry['account_code'] }}</span>
                <span>{{ $accountEntry['account_name'] }}</span>
                <span class="account-type">
                    ({{ ucfirst($accountEntry['account_type']) }} - {{ $accountEntry['transaction_count'] }} transaksi)
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 12%;">Referensi</th>
                        <th style="width: 28%;">Keterangan</th>
                        <th style="width: 15%;" class="text-right">Debit</th>
                        <th style="width: 15%;" class="text-right">Kredit</th>
                        <th style="width: 15%;" class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Opening Balance --}}
                    <tr class="opening-balance-row">
                        <td class="text-center">-</td>
                        <td>{{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</td>
                        <td><span class="reference">SALDO-AWAL</span></td>
                        <td>Saldo Awal Periode</td>
                        <td class="text-right amount">
                            @if($accountEntry['opening_balance'] > 0)
                                <span class="debit">{{ number_format($accountEntry['opening_balance'], 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right amount">
                            @if($accountEntry['opening_balance'] < 0)
                                <span class="credit">{{ number_format(abs($accountEntry['opening_balance']), 0, ',', '.') }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td class="text-right amount {{ $accountEntry['opening_balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($accountEntry['opening_balance'], 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Transactions --}}
                    @foreach($accountEntry['transactions'] as $index => $transaction)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                            <td>
                                <span class="reference">{{ $transaction['reference'] }}</span>
                                @if(isset($transaction['book_name']))
                                    <br><span class="book-name">{{ $transaction['book_name'] }}</span>
                                @endif
                            </td>
                            <td style="font-size: 8pt;">{{ $transaction['description'] }}</td>
                            <td class="text-right amount">
                                @if($transaction['debit'] > 0)
                                    <span class="debit">{{ number_format($transaction['debit'], 0, ',', '.') }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-right amount">
                                @if($transaction['credit'] > 0)
                                    <span class="credit">{{ number_format($transaction['credit'], 0, ',', '.') }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-right amount {{ $transaction['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                {{ number_format($transaction['balance'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Account Total --}}
                    <tr class="account-total-row">
                        <td colspan="4" class="text-right">
                            Total <span class="account-code">{{ $accountEntry['account_code'] }}</span>
                        </td>
                        <td class="text-right amount debit">
                            {{ number_format($accountEntry['total_debit'], 0, ',', '.') }}
                        </td>
                        <td class="text-right amount credit">
                            {{ number_format($accountEntry['total_credit'], 0, ',', '.') }}
                        </td>
                        <td class="text-right amount {{ $accountEntry['ending_balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format($accountEntry['ending_balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="spacer"></div>

            @php
                $grandTotalDebit += $accountEntry['total_debit'];
                $grandTotalCredit += $accountEntry['total_credit'];
            @endphp
        @endforeach

        {{-- Grand Total --}}
        <table>
            <tbody>
                <tr class="grand-total-row">
                    <td colspan="4" class="text-right" style="padding: 8px;">TOTAL BUKU BESAR</td>
                    <td class="text-right amount debit" style="padding: 8px;">
                        {{ number_format($data['summary']['total_debit'], 0, ',', '.') }}
                    </td>
                    <td class="text-right amount credit" style="padding: 8px;">
                        {{ number_format($data['summary']['total_credit'], 0, ',', '.') }}
                    </td>
                    <td class="text-right amount {{ $data['summary']['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}" style="padding: 8px;">
                        {{ number_format($data['summary']['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Summary Box --}}
        <div class="summary-box">
            <div class="summary-row">
                <span>Total Debit:</span>
                <span class="amount debit">Rp {{ number_format($data['summary']['total_debit'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Total Kredit:</span>
                <span class="amount credit">Rp {{ number_format($data['summary']['total_credit'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-row" style="border-top: 1px solid #999; padding-top: 5px; margin-top: 5px;">
                <span>Saldo:</span>
                <span class="amount {{ $data['summary']['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    Rp {{ number_format($data['summary']['balance'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Tidak ada data transaksi untuk periode yang dipilih</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>{{ $companySettings['company_name'] ?? $filters['company_name'] ?? 'Nama Perusahaan' }}</strong> - Buku Besar</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Halaman ini merupakan dokumen resmi dan sah untuk keperluan akuntansi</p>
    </div>
</body>
</html>
