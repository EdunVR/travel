<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Perubahan Modal - {{ $accountingBook->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 14pt;
        }
        .header p {
            margin: 3px 0;
            font-size: 9pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .equity-account { background-color: #e8f5e9; }
        .withdrawal-account { background-color: #ffebee; }
        .summary-row {
            font-weight: bold;
            background-color: #f5f5f5 !important;
        }
        .final-row {
            font-weight: bold;
            background-color: #e8f5e9 !important;
        }
        .positive-amount {
            color: #2e7d32;
        }
        .negative-amount {
            color: #c62828;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
        }
        .detail-row {
            font-size: 8pt;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERUBAHAN MODAL</h1>
        <p>{{ config('app.name') }}</p>
        <p>Tanggal Export: {{ $exportDate }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Tahun Buku</strong></td>
            <td width="35%">: {{ $accountingBook->name }}</td>
            <td width="15%"><strong>Dicetak oleh</strong></td>
            <td width="35%">: {{ $preparedBy }}</td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>: {{ $dateFrom ? $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y') : 'Semua Periode' }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $exportDate }}</td>
        </tr>
    </table>

    <!-- Beginning Equity -->
    <table>
        <thead>
            <tr>
                <th colspan="3" class="text-center">MODAL AWAL (Kode 3.01.xx)</th>
            </tr>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equityData['equity_items'] as $item)
                @if($item['is_beginning'])
                <tr class="equity-account">
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right positive-amount">{{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="summary-row">
                <td colspan="2" class="text-center"><strong>TOTAL MODAL AWAL</strong></td>
                <td class="text-right positive-amount">{{ number_format($equityData['beginning_equity'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Additional Investment -->
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="3" class="text-center">TAMBAHAN MODAL (Kode 3.02.xx)</th>
            </tr>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equityData['equity_items'] as $item)
                @if($item['is_additional'])
                <tr class="equity-account">
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right positive-amount">{{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="summary-row">
                <td colspan="2" class="text-center"><strong>TOTAL TAMBAHAN MODAL</strong></td>
                <td class="text-right positive-amount">{{ number_format($equityData['additional_investment'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Profit/Loss -->
    <table style="margin-top: 20px;">
        <tbody>
            <tr class="summary-row">
                <td colspan="2" class="text-center">
                    <strong>LABA/RUGI BERSIH</strong>
                    <div class="detail-row">
                        Pendapatan: {{ number_format($equityData['profit_loss_detail']['totals']['revenue'], 2) }} - 
                        HPP: {{ number_format($equityData['profit_loss_detail']['totals']['cogs'], 2) }} = 
                        Laba Kotor: {{ number_format($equityData['profit_loss_detail']['gross_profit'], 2) }}<br>
                        Beban Operasional: {{ number_format($equityData['profit_loss_detail']['totals']['operating_expenses'], 2) }} | 
                        Pendapatan Lain: {{ number_format($equityData['profit_loss_detail']['totals']['other_income'], 2) }} | 
                        Beban Lain: {{ number_format($equityData['profit_loss_detail']['totals']['other_expenses'], 2) }}<br>
                        Pajak: {{ number_format($equityData['profit_loss_detail']['tax_expense'], 2) }}
                    </div>
                </td>
                <td class="text-right {{ $equityData['profit_loss'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                    @if($equityData['profit_loss'] >= 0)
                        {{ number_format($equityData['profit_loss'], 2) }}
                    @else
                        ({{ number_format(abs($equityData['profit_loss']), 2) }})
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Owner Withdrawal -->
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="3" class="text-center">PENGAMBILAN PRIVE (Kode 3.03.xx)</th>
            </tr>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equityData['equity_items'] as $item)
                @if($item['is_withdrawal'])
                <tr class="withdrawal-account">
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right negative-amount">({{ number_format(abs($item['amount']), 2) }})</td>
                </tr>
                @endif
            @endforeach
            <tr class="summary-row">
                <td colspan="2" class="text-center"><strong>TOTAL PENGAMBILAN PRIVE</strong></td>
                <td class="text-right negative-amount">({{ number_format($equityData['owner_withdrawal'], 2) }})</td>
            </tr>
        </tbody>
    </table>

    <!-- Ending Equity Calculation -->
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="3" class="text-center">PERHITUNGAN MODAL AKHIR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">Modal Awal</td>
                <td class="text-right positive-amount">{{ number_format($equityData['beginning_equity'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">Tambahan Modal</td>
                <td class="text-right positive-amount">{{ number_format($equityData['additional_investment'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">Laba/Rugi Bersih</td>
                <td class="text-right {{ $equityData['profit_loss'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                    @if($equityData['profit_loss'] >= 0)
                        {{ number_format($equityData['profit_loss'], 2) }}
                    @else
                        ({{ number_format(abs($equityData['profit_loss']), 2) }})
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">Pengambilan Prive</td>
                <td class="text-right negative-amount">({{ number_format($equityData['owner_withdrawal'], 2) }})</td>
            </tr>
            <tr class="final-row">
                <td colspan="2"><strong>MODAL AKHIR</strong></td>
                <td class="text-right positive-amount"><strong>{{ number_format($equityData['ending_equity'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh {{ $preparedBy }} pada {{ $exportDate }}
    </div>
</body>
</html>
