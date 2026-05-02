<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Outlet</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-aktif { color: green; }
        .status-nonaktif { color: red; }
        .footer { margin-top: 20px; text-align: right; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA OUTLET</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(request('kota') !== 'ALL' || request('status') !== 'ALL')
        <p>
            Filter: 
            {{ request('kota') !== 'ALL' ? 'Kota: ' . request('kota') : '' }}
            {{ request('status') !== 'ALL' ? 'Status: ' . request('status') : '' }}
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Outlet</th>
                <th>Kota</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outlets as $index => $outlet)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $outlet->kode_outlet }}</td>
                <td>{{ $outlet->nama_outlet }}</td>
                <td>{{ $outlet->kota }}</td>
                <td>{{ $outlet->alamat }}</td>
                <td>{{ $outlet->telepon }}</td>
                <td class="{{ $outlet->is_active ? 'status-aktif' : 'status-nonaktif' }}">
                    {{ $outlet->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
