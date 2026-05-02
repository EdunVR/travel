<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pelanggan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA PELANGGAN</h2>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Tipe</th>
                <th>Outlet</th>
                <th>Piutang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $customer->getMemberCodeWithPrefix() ?? $customer->kode_member ?? '-' }}</td>
                <td>{{ $customer->nama }}</td>
                <td>{{ $customer->telepon }}</td>
                <td>{{ $customer->alamat }}</td>
                <td>{{ $customer->tipe ? $customer->tipe->nama_tipe : '-' }}</td>
                <td>{{ $customer->outlet ? $customer->outlet->nama_outlet : '-' }}</td>
                <td>Rp {{ number_format($customer->total_piutang ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
