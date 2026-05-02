<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekonsiliasi Bank - {{ $reconciliation->period_month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            text-align: right;
        }
        .difference {
            font-size: 14px;
            font-weight: bold;
            color: {{ abs($reconciliation->difference) > 0 ? '#dc2626' : '#16a34a' }};
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKONSILIASI BANK</h1>
        <p>{{ $reconciliation->outlet->nama_outlet ?? '-' }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($reconciliation->period_month)->format('F Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Akun Bank:</div>
            <div class="info-value">{{ $reconciliation->account->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kode Akun:</div>
            <div class="info-value">{{ $reconciliation->account->code ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kategori:</div>
            <div class="info-value">{{ $reconciliation->account->category ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Rekonsiliasi:</div>
            <div class="info-value">{{ $reconciliation->reconciliation_date->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                @if($reconciliation->status === 'draft')
                    Draft
                @elseif($reconciliation->status === 'completed')
                    Selesai
                @elseif($reconciliation->status === 'approved')
                    Disetujui
                @endif
            </div>
        </div>
    </div>

    @if($reconciliation->items->count() > 0)
    <h3>Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Transaksi</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reconciliation->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $item->transaction_number ?? '-' }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->category ?? '-')) }}</td>
                <td class="text-right">{{ $item->type === 'debit' ? 'Rp ' . number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $item->type === 'credit' ? 'Rp ' . number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td class="text-center">
                    @if($item->status === 'reconciled')
                        Sesuai
                    @elseif($item->status === 'unreconciled')
                        Belum Sesuai
                    @else
                        Pending
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="summary-box">
        <h3 style="margin-top: 0;">Ringkasan Rekonsiliasi</h3>
        <div class="summary-row">
            <div class="summary-label">Saldo Bank Statement:</div>
            <div class="summary-value">Rp {{ number_format($reconciliation->bank_statement_balance, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Saldo Buku:</div>
            <div class="summary-value">Rp {{ number_format($reconciliation->book_balance, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Saldo Disesuaikan:</div>
            <div class="summary-value">Rp {{ number_format($reconciliation->adjusted_balance, 0, ',', '.') }}</div>
        </div>
        <hr style="margin: 10px 0;">
        <div class="summary-row">
            <div class="summary-label">Selisih:</div>
            <div class="summary-value difference">Rp {{ number_format($reconciliation->difference, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($reconciliation->notes)
    <div style="margin-top: 20px;">
        <strong>Catatan:</strong>
        <p>{{ $reconciliation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <div>Dibuat Oleh,</div>
            <div class="signature-line">{{ $reconciliation->reconciled_by ?? '-' }}</div>
        </div>
        @if($reconciliation->approved_by)
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="signature-line">{{ $reconciliation->approved_by }}</div>
            <div style="font-size: 10px; margin-top: 5px;">
                {{ $reconciliation->approved_at ? $reconciliation->approved_at->format('d/m/Y H:i') : '' }}
            </div>
        </div>
        @endif
    </div>
</body>
</html>
