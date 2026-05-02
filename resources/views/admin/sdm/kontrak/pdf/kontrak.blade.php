<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Kontrak Kerja</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-aktif { background-color: #d4edda; color: #155724; }
        .badge-habis { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $outlet->nama ?? 'ERP System' }}</h2>
        <h3>Daftar Kontrak Kerja</h3>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Kontrak</th>
                <th width="20%">Karyawan</th>
                <th width="15%">Jenis</th>
                <th width="15%">Jabatan</th>
                <th width="15%">Periode</th>
                <th width="10%">Durasi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kontrak as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_kontrak }}</td>
                    <td>{{ $item->recruitment->name ?? '-' }}</td>
                    <td>{{ $item->jenis_kontrak }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $item->durasi ?? '-' }} bln</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->status === 'aktif' ? 'aktif' : 'habis' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
