<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Arus Kas - {{ $accountingBook->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 8pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8pt;
            page-break-inside: auto;
        }
        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 3px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .section-header {
            background-color: #e0e0e0 !important;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #333 !important;
            border-bottom: 1px solid #333 !important;
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
        .indent-1 {
            padding-left: 10px;
        }
        .indent-2 {
            padding-left: 20px;
        }
        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 7pt;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ARUS KAS</h1>
        <p>{{ config('app.name') }}</p>
        <p>Periode: {{ $dateFrom ? $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y') : 'Semua Periode' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Tahun Buku</strong></td>
            <td width="30%">: {{ $accountingBook->name }}</td>
            <td width="20%"><strong>Dicetak oleh</strong></td>
            <td width="30%">: {{ $preparedBy }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $exportDate }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- ARUS KAS AKTIVITAS OPERASIONAL -->
    <table>
        <tr class="section-header">
            <td colspan="3">ARUS KAS AKTIVITAS OPERASIONAL</td>
        </tr>
        
        @foreach($cashFlowData['operating_activities']['revenues'] as $revenue)
        <tr>
            <td>{{ $revenue['code'] }} - {{ $revenue['name'] }}</td>
            <td></td>
            <td class="text-right positive-amount">
                {{ number_format($revenue['amount'], 0) }}
            </td>
        </tr>
        @endforeach

        @foreach($cashFlowData['operating_activities']['cogs'] as $cogs)
        <tr>
            <td>{{ $cogs['code'] }} - {{ $cogs['name'] }}</td>
            <td></td>
            <td class="text-right negative-amount">
                ({{ number_format(abs($cogs['amount']), 0) }})
            </td>
        </tr>
        @endforeach

        @foreach($cashFlowData['operating_activities']['expenses'] as $expense)
        <tr>
            <td>{{ $expense['code'] }} - {{ $expense['name'] }}</td>
            <td></td>
            <td class="text-right negative-amount">
                ({{ number_format(abs($expense['amount']), 0) }})
            </td>
        </tr>
        @endforeach

        @foreach($cashFlowData['operating_activities']['other_expenses'] as $expense)
        <tr>
            <td>{{ $expense['code'] }} - {{ $expense['name'] }}</td>
            <td></td>
            <td class="text-right negative-amount">
                ({{ number_format(abs($expense['amount']), 0) }})
            </td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td><strong>SUBTOTAL</strong></td>
            <td></td>
            <td class="text-right {{ $cashFlowData['operating_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                <strong>{{ number_format($cashFlowData['operating_activities']['subtotal'], 0) }}</strong>
            </td>
        </tr>
    </table>

    <!-- ARUS KAS AKTIVITAS INVESTASI -->
    <table>
        <tr class="section-header">
            <td colspan="3">ARUS KAS AKTIVITAS INVESTASI</td>
        </tr>

        @foreach($cashFlowData['investing_activities']['items'] as $item)
        <tr>
            <td>{{ $item['code'] }} - {{ $item['name'] }}</td>
            <td></td>
            <td class="text-right {{ $item['amount'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                @if($item['amount'] >= 0)
                    {{ number_format($item['amount'], 0) }}
                @else
                    ({{ number_format(abs($item['amount']), 0) }})
                @endif
            </td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td><strong>SUBTOTAL</strong></td>
            <td></td>
            <td class="text-right {{ $cashFlowData['investing_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                <strong>{{ number_format($cashFlowData['investing_activities']['subtotal'], 0) }}</strong>
            </td>
        </tr>
    </table>

    <!-- ARUS KAS AKTIVITAS PENDANAAN -->
    <table>
        <tr class="section-header">
            <td colspan="3">ARUS KAS AKTIVITAS PENDANAAN</td>
        </tr>

        @foreach($cashFlowData['financing_activities']['items'] as $item)
        <tr>
            <td>{{ $item['code'] }} - {{ $item['name'] }}</td>
            <td></td>
            <td class="text-right {{ $item['amount'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                @if($item['amount'] >= 0)
                    {{ number_format($item['amount'], 0) }}
                @else
                    ({{ number_format(abs($item['amount']), 0) }})
                @endif
            </td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td><strong>SUBTOTAL</strong></td>
            <td></td>
            <td class="text-right {{ $cashFlowData['financing_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                <strong>{{ number_format($cashFlowData['financing_activities']['subtotal'], 0) }}</strong>
            </td>
        </tr>
    </table>

    <!-- TOTAL KENAIKAN/PENURUNAN KAS -->
    <table>
        <tr class="final-row">
            <td colspan="2"><strong>TOTAL KENAIKAN (karena +)</strong></td>
            <td class="text-right positive-amount">
                <strong>{{ number_format($cashFlowData['total_increase'], 0) }}</strong>
            </td>
        </tr>
        <tr class="final-row">
            <td colspan="2"><strong>TOTAL PENURUNAN (karena -)</strong></td>
            <td class="text-right negative-amount">
                <strong>({{ number_format($cashFlowData['total_decrease'], 0) }})</strong>
            </td>
        </tr>
    </table>

    <!-- SALDO KAS AWAL DAN AKHIR -->
    <table>
        <tr>
            <td colspan="2">Saldo Kas Awal Periode</td>
            <td class="text-right positive-amount">
                {{ number_format($cashFlowData['beginning_cash'], 0) }}
            </td>
        </tr>
        <tr class="final-row">
            <td colspan="2"><strong>Saldo Kas Akhir Periode</strong></td>
            <td class="text-right positive-amount">
                <strong>{{ number_format($cashFlowData['ending_cash'], 0) }}</strong>
            </td>
        </tr>
    </table>

    <!-- DETAIL SALDO KAS -->
    <div style="margin-top: 15px;">
        <table>
            <thead>
                <tr>
                    <th colspan="4" class="text-center">DETAIL SALDO KAS DAN BANK</th>
                </tr>
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th class="text-right">Saldo Awal</th>
                    <th class="text-right">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cashFlowData['cash_details'] as $account)
                <tr>
                    <td>{{ $account['code'] }}</td>
                    <td>{{ $account['name'] }}</td>
                    <td class="text-right {{ $account['beginning_balance'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                        {{ number_format($account['beginning_balance'], 0) }}
                    </td>
                    <td class="text-right {{ $account['ending_balance'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                        {{ number_format($account['ending_balance'], 0) }}
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>TOTAL</strong></td>
                    <td class="text-right {{ $cashFlowData['beginning_cash'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                        <strong>{{ number_format($cashFlowData['beginning_cash'], 0) }}</strong>
                    </td>
                    <td class="text-right {{ $cashFlowData['ending_cash'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                        <strong>{{ number_format($cashFlowData['ending_cash'], 0) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak oleh {{ $preparedBy }} pada {{ $exportDate }}
    </div>
</body>
</html>
