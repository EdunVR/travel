<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Akuntansi - {{ $book->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .book-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .summary-section {
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .balance-positive {
            color: #059669;
            font-weight: bold;
        }
        
        .balance-negative {
            color: #dc2626;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ $companySettings['company_name'] ?? config('app.name') }}</div>
        <div>{{ $companySettings['company_address'] ?? '' }}</div>
        @if(isset($companySettings['company_phone']))
            <div>Telp: {{ $companySettings['company_phone'] }}</div>
        @endif
        <div class="report-title">LAPORAN BUKU AKUNTANSI</div>
    </div>

    {{-- Book Information --}}
    <div class="book-info">
        <div class="info-row">
            <span class="info-label">Kode Buku:</span>
            <span>{{ $book->code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama Buku:</span>
            <span>{{ $book->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipe:</span>
            <span>{{ ucfirst($book->type) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Outlet:</span>
            <span>{{ $book->outlet->nama_outlet }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ \Carbon\Carbon::parse($book->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($book->end_date)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span>{{ ucfirst($book->status) }}{{ $book->is_locked ? ' (Terkunci)' : '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mata Uang:</span>
            <span>{{ $book->currency }}</span>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="summary-section">
        <div class="summary-title">RINGKASAN</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($totalEntries) }}</div>
                <div class="summary-label">Total Entri Jurnal</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($totalDebit, 0, ',', '.') }}</div>
                <div class="summary-label">Total Debit</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($totalCredit, 0, ',', '.') }}</div>
                <div class="summary-label">Total Kredit</div>
            </div>
        </div>
    </div>

    {{-- Account Balances --}}
    <div class="summary-section">
        <div class="summary-title">SALDO AKUN</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15%">Kode Akun</th>
                    <th style="width: 35%">Nama Akun</th>
                    <th style="width: 10%">Tipe</th>
                    <th style="width: 15%">Total Debit</th>
                    <th style="width: 15%">Total Kredit</th>
                    <th style="width: 15%">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accountBalances as $balance)
                <tr>
                    <td class="text-center">{{ $balance['account']->code }}</td>
                    <td>{{ $balance['account']->name }}</td>
                    <td class="text-center">{{ ucfirst($balance['account']->type) }}</td>
                    <td class="text-right">{{ number_format($balance['debit'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($balance['credit'], 0, ',', '.') }}</td>
                    <td class="text-right {{ $balance['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                        {{ number_format($balance['balance'], 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data saldo akun</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Journal Entries Summary --}}
    @if($book->journalEntries->count() > 0)
    <div class="page-break"></div>
    <div class="summary-section">
        <div class="summary-title">DAFTAR ENTRI JURNAL</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15%">No. Transaksi</th>
                    <th style="width: 12%">Tanggal</th>
                    <th style="width: 35%">Deskripsi</th>
                    <th style="width: 15%">Total Debit</th>
                    <th style="width: 15%">Total Kredit</th>
                    <th style="width: 8%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($book->journalEntries->sortBy('transaction_date') as $entry)
                <tr>
                    <td class="text-center">{{ $entry->transaction_number }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($entry->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right">{{ number_format($entry->total_debit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($entry->total_credit, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($entry->status) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL:</td>
                    <td class="text-right">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalCredit, 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div>Laporan digenerate pada: {{ $generatedAt->format('d/m/Y H:i:s') }}</div>
        <div>Sistem Akuntansi - {{ config('app.name') }}</div>
    </div>
</body>
</html>