<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monitoring Masa Berlaku Dokumen</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        .section-title { background-color: #f2f2f2; padding: 8px; margin-top: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; font-size: 10px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-green { background-color: #d4edda; color: #155724; }
        .badge-yellow { background-color: #fff3cd; color: #856404; }
        .badge-red { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $outlet->nama ?? 'ERP System' }}</h2>
        <h3>Monitoring Masa Berlaku Dokumen HR</h3>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- Kontrak Kerja -->
    <div class="section-title">KONTRAK KERJA</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Karyawan</th>
                <th width="15%">No. Kontrak</th>
                <th width="15%">Jabatan</th>
                <th width="15%">Tgl Selesai</th>
                <th width="15%">Sisa Hari</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kontrak as $index => $item)
                @php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_selesai, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->recruitment->name ?? '-' }}</td>
                    <td>{{ $item->nomor_kontrak }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>{{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $sisaHari > 0 ? $sisaHari . ' hari' : 'Habis' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            @if($item->status_warna === 'green') Aktif
                            @elseif($item->status_warna === 'yellow') Akan Habis
                            @else Sudah Habis
                            @endif
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Surat Peringatan -->
    <div class="section-title">SURAT PERINGATAN</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Karyawan</th>
                <th width="15%">No. SP</th>
                <th width="10%">Jenis</th>
                <th width="15%">Tgl Berakhir</th>
                <th width="15%">Sisa Hari</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sp as $index => $item)
                @php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->recruitment->name ?? '-' }}</td>
                    <td>{{ $item->nomor_sp }}</td>
                    <td class="text-center">{{ $item->jenis_sp }}</td>
                    <td>{{ $item->tanggal_berakhir->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $sisaHari > 0 ? $sisaHari . ' hari' : 'Habis' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            @if($item->status_warna === 'green') Aktif
                            @elseif($item->status_warna === 'yellow') Akan Habis
                            @else Sudah Habis
                            @endif
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Dokumen HR -->
    <div class="section-title">DOKUMEN HR</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Karyawan</th>
                <th width="15%">No. Dokumen</th>
                <th width="15%">Jenis</th>
                <th width="15%">Tgl Berakhir</th>
                <th width="15%">Sisa Hari</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dokumen as $index => $item)
                @php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->recruitment->name ?? 'Umum' }}</td>
                    <td>{{ $item->nomor_dokumen }}</td>
                    <td>{{ $item->jenis_dokumen }}</td>
                    <td>{{ $item->tanggal_berakhir->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $sisaHari > 0 ? $sisaHari . ' hari' : 'Habis' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">
                            @if($item->status_warna === 'green') Aktif
                            @elseif($item->status_warna === 'yellow') Akan Habis
                            @else Sudah Habis
                            @endif
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
