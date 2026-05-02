<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        h1 { text-align: center; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 20px; }
        .status { padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-inactive { background-color: #fef3c7; color: #92400e; }
        .status-resigned { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Outlet</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Departemen</th>
                <th>Status</th>
                <th>Telepon</th>
                <th>Gaji</th>
                <th>Tgl Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $emp->outlet ? $emp->outlet->nama_outlet : '-' }}</td>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->position }}</td>
                <td>{{ $emp->department ?? '-' }}</td>
                <td>
                    @if($emp->status === 'active')
                        <span class="status status-active">Aktif</span>
                    @elseif($emp->status === 'inactive')
                        <span class="status status-inactive">Tidak Aktif</span>
                    @else
                        <span class="status status-resigned">Resign</span>
                    @endif
                </td>
                <td>{{ $emp->phone ?? '-' }}</td>
                <td>Rp {{ number_format($emp->salary ?? 0, 0, ',', '.') }}</td>
                <td>{{ $emp->join_date ? date('d/m/Y', strtotime($emp->join_date)) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;">Total Karyawan: {{ count($employees) }}</p>
</body>
</html>
