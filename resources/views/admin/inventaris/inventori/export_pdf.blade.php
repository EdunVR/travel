<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventori</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-tersedia { color: green; }
        .status-tidak-tersedia { color: red; }
        .footer { margin-top: 20px; text-align: right; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA INVENTORI</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(request('outlet') !== 'ALL' || request('status') !== 'ALL')
        <p>
            Filter: 
            {{ request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : '' }}
            {{ request('status') !== 'ALL' ? 'Status: ' . request('status') : '' }}
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Outlet</th>
                <th>Penanggung Jawab</th>
                <th>Stok</th>
                <th>Lokasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventori as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode_inventori }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->kategori ? $item->kategori->nama_kategori : '-' }}</td>
                <td>{{ $item->outlet ? $item->outlet->nama_outlet : '-' }}</td>
                <td>{{ $item->penanggung_jawab }}</td>
                <td>{{ $item->stok }}</td>
                <td>{{ $item->lokasi_penyimpanan }}</td>
                <td class="{{ $item->status === 'tersedia' ? 'status-tersedia' : 'status-tidak-tersedia' }}">
                    {{ strtoupper($item->status) }}
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
