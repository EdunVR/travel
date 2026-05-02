<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan - {{ $book->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        .indent-1 { padding-left: 20px; }
        .indent-2 { padding-left: 40px; }
        .indent-3 { padding-left: 60px; }
        .footer { margin-top: 50px; text-align: right; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Tahunan</h1>
        <h2>{{ $book->name }}</h2>
        <p>Periode: {{ $book->start_date->format('d/m/Y') }} - {{ $book->end_date->format('d/m/Y') }}</p>
        <p>Dibuat pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <h3>Laporan Laba Rugi</h3>
    <table>
        <thead>
            <tr>
                <th>Akun</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profitLoss['revenues'] as $revenue)
                <tr>
                    <td>{{ $revenue['name'] }}</td>
                    <td class="text-right">{{ number_format($revenue['balance'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="text-right">{{ number_format($profitLoss['total_revenue'], 2) }}</td>
            </tr>
            
            @foreach($profitLoss['expenses'] as $expense)
                <tr>
                    <td>{{ $expense['name'] }}</td>
                    <td class="text-right">{{ number_format($expense['balance'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Biaya</td>
                <td class="text-right">{{ number_format($profitLoss['total_expense'], 2) }}</td>
            </tr>
            
            <tr class="total-row">
                <td>Laba/Rugi Bersih</td>
                <td class="text-right">{{ number_format($profitLoss['net_profit'], 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <h3>Neraca</h3>
    <table>
        <thead>
            <tr>
                <th>Aktiva</th>
                <th class="text-right">Jumlah</th>
                <th>Pasiva</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(count($balanceSheet['assets']), count($balanceSheet['liabilities']) + count($balanceSheet['equities']));
            @endphp
            
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    <td>
                        @if(isset($balanceSheet['assets'][$i]))
                            {{ $balanceSheet['assets'][$i]['name'] }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($balanceSheet['assets'][$i]))
                            {{ number_format($balanceSheet['assets'][$i]['balance'], 2) }}
                        @endif
                    </td>
                    <td>
                        @if($i < count($balanceSheet['liabilities']))
                            {{ $balanceSheet['liabilities'][$i]['name'] }}
                        @elseif($i < count($balanceSheet['liabilities']) + count($balanceSheet['equities']))
                            {{ $balanceSheet['equities'][$i - count($balanceSheet['liabilities'])]['name'] }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($i < count($balanceSheet['liabilities']))
                            {{ number_format($balanceSheet['liabilities'][$i]['balance'], 2) }}
                        @elseif($i < count($balanceSheet['liabilities']) + count($balanceSheet['equities']))
                            {{ number_format($balanceSheet['equities'][$i - count($balanceSheet['liabilities'])]['balance'], 2) }}
                        @endif
                    </td>
                </tr>
            @endfor
            
            <tr class="total-row">
                <td>Total Aktiva</td>
                <td class="text-right">{{ number_format($balanceSheet['total_assets'], 2) }}</td>
                <td>Total Pasiva</td>
                <td class="text-right">{{ number_format($balanceSheet['total_liabilities'] + $balanceSheet['total_equities'], 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
        <p>Tanggal: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
