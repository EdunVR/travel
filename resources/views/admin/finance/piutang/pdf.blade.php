<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Piutang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-belum {
            color: orange;
            font-weight: bold;
        }
        .status-overdue {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA PIUTANG</h2>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
        @if(isset($filters['start_date']) && isset($filters['end_date']))
        <p>Periode: {{ date('d/m/Y', strtotime($filters['start_date'])) }} - {{ date('d/m/Y', strtotime($filters['end_date'])) }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Total Piutang:</strong> Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}
        </div>
        <div class="summary-item">
            <strong>Sudah Dibayar:</strong> Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}
        </div>
        <div class="summary-item">
            <strong>Sisa Piutang:</strong> Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}
        </div>
        <div class="summary-item">
            <strong>Jatuh Tempo:</strong> {{ $summary['count_overdue'] }} transaksi
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Source</th>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Outlet</th>
                <th class="text-right">Jumlah Piutang</th>
                <th class="text-right">Dibayar</th>
                <th class="text-right">Sisa</th>
                <th>Jatuh Tempo</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($piutangData as $index => $piutang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ strtoupper($piutang['source']) }}</td>
                <td>{{ $piutang['invoice_number'] }}</td>
                <td>{{ date('d/m/Y', strtotime($piutang['tanggal'])) }}</td>
                <td>{{ $piutang['nama_customer'] }}</td>
                <td>{{ $piutang['outlet'] }}</td>
                <td class="text-right">Rp {{ number_format($piutang['jumlah_piutang'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($piutang['jumlah_dibayar'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($piutang['sisa_piutang'], 0, ',', '.') }}</td>
                <td>
                    @if($piutang['tanggal_jatuh_tempo'])
                        {{ date('d/m/Y', strtotime($piutang['tanggal_jatuh_tempo'])) }}
                        @if($piutang['is_overdue'])
                            <br><small class="status-overdue">(Terlambat {{ $piutang['days_overdue'] }} hari)</small>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($piutang['status'] === 'lunas')
                        <span class="status-lunas">Lunas</span>
                    @elseif($piutang['status'] === 'dibayar_sebagian')
                        <span class="status-belum">Dibayar Sebagian</span>
                    @else
                        <span class="status-belum">Belum Lunas</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f4f4f4; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
