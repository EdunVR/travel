<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transfer Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .filter-info { margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-menunggu { color: #f39c12; }
        .status-disetujui { color: #27ae60; }
        .status-ditolak { color: #e74c3c; }
        .footer { margin-top: 20px; text-align: right; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSFER GUDANG</h1>
        <p>Periode: {{ date('d/m/Y') }}</p>
        @if(isset($filterStatus) && $filterStatus !== 'ALL')
        <p>Filter Status: {{ strtoupper($filterStatus) }}</p>
        @endif
    </div>

    @if(isset($filterStatus) && $filterStatus !== 'ALL')
    <div class="filter-info">
        <strong>Filter:</strong> Menampilkan data dengan status <strong>{{ strtoupper($filterStatus) }}</strong>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Outlet Asal</th>
                <th width="15%">Outlet Tujuan</th>
                <th width="10%">Jenis</th>
                <th width="20%">Nama Item</th>
                <th width="8%">Jumlah</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfers as $index => $transfer)
            @php
                $itemName = '';
                if ($transfer->id_produk) {
                    $itemName = $transfer->produk->nama_produk ?? $transfer->nama_produk;
                } elseif ($transfer->id_bahan) {
                    $itemName = $transfer->bahan->nama_bahan ?? $transfer->nama_bahan;
                } elseif ($transfer->id_inventori) {
                    $itemName = $transfer->inventori->nama_barang ?? $transfer->nama_barang;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transfer->outletAsal->nama_outlet ?? '-' }}</td>
                <td>{{ $transfer->outletTujuan->nama_outlet ?? '-' }}</td>
                <td>
                    @if($transfer->id_produk) Produk
                    @elseif($transfer->id_bahan) Bahan
                    @elseif($transfer->id_inventori) Inventori
                    @else - @endif
                </td>
                <td>{{ $itemName }}</td>
                <td class="text-center">{{ $transfer->jumlah }}</td>
                <td class="status-{{ $transfer->status }}">
                    <strong>{{ strtoupper($transfer->status) }}</strong>
                </td>
            </tr>
            @endforeach
            @if($transfers->count() === 0)
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">
                    Tidak ada data transfer gudang
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }} | 
        Total Data: {{ $transfers->count() }}
    </div>
</body>
</html>
