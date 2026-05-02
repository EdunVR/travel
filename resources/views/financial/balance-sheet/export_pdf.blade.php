<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Neraca - {{ $accountingBook->name }}</title>
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
        .balance-sheet {
            display: flex;
            width: 100%;
        }
        .balance-column {
            flex: 1;
            min-width: 45%;
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
        .asset-account { background-color: #e8f5e9; }
        .liability-account { background-color: #ffebee; }
        .equity-account { background-color: #e3f2fd; }
        .summary-row {
            font-weight: bold;
            background-color: #f5f5f5 !important;
        }
        .total-row {
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
        <h1>NERACA</h1>
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

    <div class="balance-sheet">
        <!-- Aktiva Column -->
        <div class="balance-column">
            <table>
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">AKTIVA</th>
                    </tr>
                </thead>
                
                <!-- Current Assets -->
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">AKTIVA LANCAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['assets']['current'] as $asset)
                    <tr class="asset-account">
                        <td>{{ $asset['code'] }} {{ $asset['name'] }}</td>
                        <td class="text-right">{{ number_format($asset['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL AKTIVA LANCAR</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['assets']['total_current'], 2) }}</td>
                    </tr>
                </tbody>
                
                <!-- Fixed Assets -->
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">AKTIVA TETAP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['assets']['fixed'] as $asset)
                    <tr class="asset-account">
                        <td>{{ $asset['code'] }} {{ $asset['name'] }}</td>
                        <td class="text-right">{{ number_format($asset['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL AKTIVA TETAP</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['assets']['total_fixed'], 2) }}</td>
                    </tr>
                </tbody>
                
                <!-- Other Assets -->
                @if(!empty($balanceSheetData['assets']['other']))
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">AKTIVA LAINNYA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['assets']['other'] as $asset)
                    <tr class="asset-account">
                        <td>{{ $asset['code'] }} {{ $asset['name'] }}</td>
                        <td class="text-right">{{ number_format($asset['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL AKTIVA LAINNYA</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['assets']['total_other'], 2) }}</td>
                    </tr>
                </tbody>
                @endif
                
                <!-- Total Assets -->
                <tfoot>
                    <tr class="total-row">
                        <td class="text-center"><strong>TOTAL AKTIVA</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['assets']['total_assets'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Pasiva Column -->
        <div class="balance-column">
            <table>
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">PASIVA</th>
                    </tr>
                </thead>
                
                <!-- Current Liabilities -->
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">KEWAJIBAN LANCAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['liabilities']['current'] as $liability)
                    <tr class="liability-account">
                        <td>{{ $liability['code'] }} {{ $liability['name'] }}</td>
                        <td class="text-right">{{ number_format($liability['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL KEWAJIBAN LANCAR</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['liabilities']['total_current'], 2) }}</td>
                    </tr>
                </tbody>
                
                <!-- Long-term Liabilities -->
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">KEWAJIBAN JANGKA PANJANG</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['liabilities']['long_term'] as $liability)
                    <tr class="liability-account">
                        <td>{{ $liability['code'] }} {{ $liability['name'] }}</td>
                        <td class="text-right">{{ number_format($liability['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL KEWAJIBAN JANGKA PANJANG</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['liabilities']['total_long_term'], 2) }}</td>
                    </tr>
                </tbody>
                
                <!-- Total Liabilities -->
                <tr class="summary-row">
                    <td class="text-center"><strong>TOTAL KEWAJIBAN</strong></td>
                    <td class="text-right">{{ number_format($balanceSheetData['liabilities']['total_liabilities'], 2) }}</td>
                </tr>
                
                <!-- Equities -->
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">MODAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceSheetData['equities']['items'] as $equity)
                    <tr class="equity-account">
                        <td>{{ $equity['code'] }} {{ $equity['name'] }}</td>
                        <td class="text-right">{{ number_format($equity['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <td class="text-center"><strong>TOTAL MODAL</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['equities']['total_equities'], 2) }}</td>
                    </tr>
                </tbody>
                
                <!-- Total Liabilities & Equities -->
                <tfoot>
                    <tr class="total-row">
                        <td class="text-center"><strong>TOTAL KEWAJIBAN & MODAL</strong></td>
                        <td class="text-right">{{ number_format($balanceSheetData['total_liabilities_equities'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="footer">
        Dicetak oleh {{ $preparedBy }} pada {{ $exportDate }}
    </div>
</body>
</html>
