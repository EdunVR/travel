<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kategori</title>
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
        <h1>LAPORAN DATA KATEGORI</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(request('kelompok') !== 'ALL' || request('status') !== 'ALL')
        <p>
            Filter: 
            {{ request('kelompok') !== 'ALL' ? 'Kelompok: ' . request('kelompok') : '' }}
            {{ request('status') !== 'ALL' ? 'Status: ' . request('status') : '' }}
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Kelompok</th>
                <th>Outlet</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode_kategori }}</td>
                <td>{{ $item->nama_kategori }}</td>
                <td>{{ $item->kelompok }}</td>
                <td>{{ $item->outlet ? $item->outlet->nama_outlet : '-' }}</td>
                <td class="{{ $item->is_active ? 'status-aktif' : 'status-nonaktif' }}">
                    {{ $item->is_active ? 'AKTIF' : 'NONAKTIF' }}
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
