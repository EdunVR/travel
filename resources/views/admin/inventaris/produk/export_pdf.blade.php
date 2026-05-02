<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk</title>
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
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA PRODUK</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(request('outlet') !== 'ALL' || request('type') !== 'ALL')
        <p>
            Filter: 
            {{ request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : '' }}
            {{ request('type') !== 'ALL' ? 'Tipe: ' . request('type') : '' }}
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Outlet</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produks as $index => $produk)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $produk->kode_produk }}</td>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->outlet ? $produk->outlet->nama_outlet : '-' }}</td>
                <td>
                    @php
                        $types = [
                            'barang_dagang' => 'Barang Dagang',
                            'jasa' => 'Jasa',
                            'paket_travel' => 'Paket Travel',
                            'produk_kustom' => 'Kustom'
                        ];
                    @endphp
                    {{ $types[$produk->tipe_produk] ?? $produk->tipe_produk }}
                </td>
                <td>{{ $produk->kategori ? $produk->kategori->nama_kategori : '-' }}</td>
                <td>{{ $produk->satuan ? $produk->satuan->nama_satuan : '-' }}</td>
                <td class="text-right">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                <td class="text-right">{{ $produk->hpp_produk_sum_stok ?? 0 }}</td>
                <td class="{{ $produk->is_active ? 'status-aktif' : 'status-nonaktif' }}">
                    {{ $produk->is_active ? 'AKTIF' : 'NONAKTIF' }}
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
