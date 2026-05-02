<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kinerja Karyawan</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Posisi</th>
                <th>Tanggal Penilaian</th>
                <th>Kriteria</th>
                <th>Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performances as $index => $performance)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $performance->recruitment->name }}</td>
                <td>{{ $performance->recruitment->position }}</td>
                <td>{{ $performance->evaluation_date }}</td>
                <td>{{ $performance->criteria }}</td>
                <td class="text-center">{{ $performance->score }}</td>
                <td>{{ $performance->remarks ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
