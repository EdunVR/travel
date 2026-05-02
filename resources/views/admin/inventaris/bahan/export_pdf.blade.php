<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Bahan</title>
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
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA BAHAN</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(request('outlet') !== 'ALL' || request('unit') !== 'ALL')
        <p>
            Filter: 
            {{ request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : '' }}
            {{ request('unit') !== 'ALL' ? 'Satuan: ' . request('unit') : '' }}
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Bahan</th>
                <th>Outlet</th>
                <th>Merk</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bahan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kode_bahan }}</td>
                <td>{{ $item->nama_bahan }}</td>
                <td>{{ $item->outlet ? $item->outlet->nama_outlet : '-' }}</td>
                <td>{{ $item->merk ?: '-' }}</td>
                <td class="text-center">{{ $item->harga_bahan_sum_stok ?? 0 }}</td>
                <td class="text-center">{{ $item->satuan ? $item->satuan->nama_satuan : '-' }}</td>
                <td class="text-center {{ $item->is_active ? 'status-aktif' : 'status-nonaktif' }}">
                    {{ $item->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </td>
            </tr>
            @endforeach
            @if($bahan->count() === 0)
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
