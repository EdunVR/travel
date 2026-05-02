<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Neraca Lajur - {{ $accountingBook->name }}</title>
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
        .account-type-asset { background-color: #e3f2fd; }
        .account-type-liability { background-color: #e8f5e9; }
        .account-type-equity { background-color: #f1f8e9; }
        .account-type-revenue { background-color: #f3e5f5; }
        .account-type-expense { background-color: #ffebee; }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5 !important;
        }
        .net-income-row {
            font-weight: bold;
            background-color: #e8f5e9 !important;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NERACA LAJUR</h1>
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

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2" width="10%">Kode Akun</th>
                <th rowspan="2" width="20%">Nama Akun</th>
                <th colspan="2" class="text-center">Neraca Saldo</th>
                <th colspan="2" class="text-center">Laba Rugi</th>
                <th colspan="2" class="text-center">Neraca</th>
            </tr>
            <tr>
                <th width="12%" class="text-center">Debit</th>
                <th width="12%" class="text-center">Kredit</th>
                <th width="12%" class="text-center">Debit</th>
                <th width="12%" class="text-center">Kredit</th>
                <th width="12%" class="text-center">Debit</th>
                <th width="12%" class="text-center">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($worksheetData['accounts'] as $index => $account)
            <tr class="account-type-{{ $account['account_type'] }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $account['account_code'] }}</td>
                <td>{{ $account['account_name'] }}</td>
                <td class="text-right">{{ number_format($account['trial_balance']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($account['trial_balance']['credit'], 2) }}</td>
                <td class="text-right">{{ number_format($account['income_statement']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($account['income_statement']['credit'], 2) }}</td>
                <td class="text-right">{{ number_format($account['balance_sheet']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($account['balance_sheet']['credit'], 2) }}</td>
            </tr>
            @endforeach
            
            <!-- Total Neraca Saldo -->
            <tr class="total-row">
                <td colspan="3" class="text-center"><strong>TOTAL NERACA SALDO</strong></td>
                <td class="text-right">{{ number_format($worksheetData['totals']['trial_balance']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($worksheetData['totals']['trial_balance']['credit'], 2) }}</td>
                <td colspan="4"></td>
            </tr>
            
            <!-- Total Laba Rugi -->
            <tr class="total-row">
                <td colspan="5" class="text-center"><strong>TOTAL LABA/RUGI</strong></td>
                <td class="text-right">{{ number_format($worksheetData['totals']['income_statement']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($worksheetData['totals']['income_statement']['credit'], 2) }}</td>
                <td colspan="2"></td>
            </tr>
            
            <!-- Laba/Rugi Bersih -->
            <tr class="net-income-row">
                <td colspan="6" class="text-center">
                    <strong>{{ $worksheetData['net_income'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</strong>
                </td>
                <td colspan="2" class="text-center">
                    <strong>{{ number_format(abs($worksheetData['net_income']), 2) }}</strong>
                </td>
            </tr>
            
            <!-- Total Neraca (Sebelum Laba/Rugi) -->
            <tr class="total-row">
                <td colspan="7" class="text-center"><strong>TOTAL NERACA SEBELUM LABA/RUGI</strong></td>
                <td class="text-right">{{ number_format($worksheetData['totals']['balance_sheet']['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($worksheetData['totals']['balance_sheet']['credit'], 2) }}</td>
            </tr>
            
            <!-- Total Neraca (Setelah Laba/Rugi) -->
            <tr class="total-row">
                <td colspan="7" class="text-center"><strong>TOTAL NERACA SETELAH LABA/RUGI</strong></td>
                <td class="text-right">
                    {{ number_format($worksheetData['totals']['balance_sheet']['debit'] + ($worksheetData['net_income'] < 0 ? abs($worksheetData['net_income']) : 0), 2) }}
                </td>
                <td class="text-right">
                    {{ number_format($worksheetData['totals']['balance_sheet']['credit'] + ($worksheetData['net_income'] >= 0 ? $worksheetData['net_income'] : 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh {{ $preparedBy }} pada {{ $exportDate }}
    </div>
</body>
</html>
