<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekening Koran - {{ $account->account_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 5px 0; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; }
        .transaction-table { width: 100%; border-collapse: collapse; }
        .transaction-table th, 
        .transaction-table td { border: 1px solid #ddd; padding: 5px; }
        .transaction-table th { background-color: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 9pt; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKENING KORAN INVESTASI</h2>
        <h3>{{ $account->bank_name }} - {{ $account->account_number }}</h3>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Investor</td>
            <td width="30%">: {{ $investor->name }}</td>
            <td width="20%">Total Investasi</td>
            <td width="30%" class="text-right">: Rp{{ number_format($totalInvestment, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: {{ $account->account_number }}</td>
            <td>Saldo Akhir</td>
            <td class="text-right">: Rp{{ number_format($closingBalance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Nama Rekening</td>
            <td>: {{ $account->account_name }}</td>
            <td>Total Transaksi</td>
            <td class="text-right">: {{ count($transactions) }}</td>
        </tr>
    </table>

    <table class="transaction-table">
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="15%">Jenis</th>
                <th width="25%">Keterangan</th>
                <th width="15%" class="text-right">Debet</th>
                <th width="15%" class="text-right">Kredit</th>
                <th width="20%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td class="text-center">{{ $transaction['date'] }}</td>
                <td>{{ $transaction['type'] }}</td>
                <td>{{ $transaction['description'] }}</td>
                <td class="text-right">{{ $transaction['debit'] > 0 ? 'Rp'.number_format($transaction['debit'], 0, ',', '.') : '' }}</td>
                <td class="text-right">{{ $transaction['credit'] > 0 ? 'Rp'.number_format($transaction['credit'], 0, ',', '.') : '' }}</td>
                <td class="text-right">Rp{{ number_format($transaction['balance'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
