<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Bulanan - {{ $monthName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            font-size: 7px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .danger {
            color: red;
        }
        .text-left {
            text-align: left;
            padding-left: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
        }
        .footer p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN ABSENSI BULANAN</h2>
        <h3>{{ $monthName }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" style="width: 100px;">Nama</th>
                <th rowspan="2" style="width: 60px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}">Tanggal</th>
                <th colspan="6">Summary</th>
            </tr>
            <tr>
                @for($day = 1; $day <= $daysInMonth; $day++)
                    <th style="width: 12px;">{{ $day }}</th>
                @endfor
                <th style="width: 25px;">H</th>
                <th style="width: 25px;">A</th>
                <th style="width: 30px;">Jam</th>
                <th style="width: 30px;">T</th>
                <th style="width: 30px;">PC</th>
                <th style="width: 30px;">L</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $row['employee_name'] }}</td>
                <td>{{ $row['position'] }}</td>
                @for($day = 1; $day <= $daysInMonth; $day++)
                    <td class="{{ $row['days'][$day]['symbol'] === 'H' ? 'success' : 'danger' }}">
                        {{ $row['days'][$day]['symbol'] }}
                    </td>
                @endfor
                <td><strong>{{ $row['summary']['present'] }}</strong></td>
                <td>{{ $row['summary']['absent'] }}</td>
                <td><strong>{{ $row['summary']['hours'] }}</strong></td>
                <td>{{ $row['summary']['late'] }}</td>
                <td>{{ $row['summary']['early'] }}</td>
                <td>{{ $row['summary']['overtime'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Keterangan:</strong></p>
        <p>H = Hadir | - = Tidak Hadir/Izin/Sakit/Alpha</p>
        <p><strong>Summary:</strong> H = Hadir | A = Absen | Jam = Total Jam Kerja | T = Terlambat (menit) | PC = Pulang Cepat (menit) | L = Lembur (jam)</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
