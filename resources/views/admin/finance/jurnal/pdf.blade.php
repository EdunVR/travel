<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Jurnal Umum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16pt;
            margin: 5px 0;
        }
        .header h2 {
            font-size: 13pt;
            margin: 5px 0;
            color: #4F46E5;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #4F46E5;
        }
        .filter-info p {
            margin: 3px 0;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead tr {
            background-color: #4F46E5;
            color: white;
        }
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8pt;
            border: 1px solid #4F46E5;
        }
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .debit { color: #059669; }
        .credit { color: #DC2626; }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .status-draft { background-color: #FEF3C7; color: #92400E; }
        .status-posted { background-color: #D1FAE5; color: #065F46; }
        .status-void { background-color: #FEE2E2; color: #991B1B; }
        tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }
        tfoot td {
            padding: 10px 5px;
            border: 2px solid #4F46E5;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #4F46E5;
        }
        .summary-item {
            margin: 5px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $filters['company_name'] ?? config('app.name', 'Nama Perusahaan') }}</h1>
        <h2>DAFTAR JURNAL UMUM</h2>
        @if(isset($filters['outlet_name']))
            <p style="font-size: 9pt;">{{ $filters['outlet_name'] }}</p>
        @endif
    </div>

    @if(!empty($filters) && (isset($filters['date_from']) || isset($filters['status']) || isset($filters['book_name'])))
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        @if(isset($filters['date_from']) && isset($filters['date_to']))
            <p>Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</p>
        @endif
        @if(isset($filters['status']) && $filters['status'] !== 'all')
            <p>Status: {{ ucfirst($filters['status']) }}</p>
        @endif
        @if(isset($filters['book_name']))
            <p>Buku: {{ $filters['book_name'] }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 11%;">No. Transaksi</th>
                <th style="width: 9%;">Kode Akun</th>
                <th style="width: 17%;">Nama Akun</th>
                <th style="width: 20%;">Deskripsi</th>
                <th style="width: 12%;" class="text-right">Debit</th>
                <th style="width: 12%;" class="text-right">Kredit</th>
                <th style="width: 6%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @forelse($data as $index => $journal)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($journal->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $journal->transaction_number }}</td>
                    <td>{{ $journal->account_code }}</td>
                    <td>{{ $journal->account_name }}</td>
                    <td>{{ $journal->description }}</td>
                    <td class="text-right amount debit">
                        {{ $journal->debit > 0 ? 'Rp ' . number_format($journal->debit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right amount credit">
                        {{ $journal->credit > 0 ? 'Rp ' . number_format($journal->credit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $journal->status }}">
                            @if($journal->status === 'draft')
                                Draft
                            @elseif($journal->status === 'posted')
                                Posted
                            @elseif($journal->status === 'void')
                                Void
                            @else
                                {{ ucfirst($journal->status) }}
                            @endif
                        </span>
                    </td>
                </tr>
                @php
                    $totalDebit += $journal->debit;
                    $totalCredit += $journal->credit;
                @endphp
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Tidak ada data jurnal
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right amount debit">
                    <strong>Rp {{ number_format($totalDebit, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right amount credit">
                    <strong>Rp {{ number_format($totalCredit, 0, ',', '.') }}</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($data) > 0)
    <div class="summary">
        <div class="summary-item">
            Total Debit: <span class="amount debit">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            Total Kredit: <span class="amount credit">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item" style="border-top: 1px solid #999; padding-top: 5px; margin-top: 5px;">
            Selisih: 
            <span class="amount" style="color: {{ abs($totalDebit - $totalCredit) < 0.01 ? '#059669' : '#DC2626' }};">
                Rp {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }}
                @if(abs($totalDebit - $totalCredit) < 0.01)
                    (Seimbang)
                @else
                    (Tidak Seimbang)
                @endif
            </span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>
