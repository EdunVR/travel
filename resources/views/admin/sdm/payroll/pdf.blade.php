<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        h1 { text-align: center; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-right { text-align: right; }
        .status { padding: 2px 8px; border-radius: 4px; font-size: 9px; }
        .status-draft { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-paid { background-color: #e9d5ff; color: #6b21a8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($period . '-01')->format('F Y') }}</p>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Outlet</th>
                <th>Karyawan</th>
                <th>Posisi</th>
                <th class="text-right">Gaji Pokok</th>
                <th class="text-right">Gaji Bersih</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNet = 0; @endphp
            @foreach($payrolls as $index => $p)
            @php $totalNet += $p->net_salary; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->outlet ? $p->outlet->nama_outlet : '-' }}</td>
                <td>{{ $p->employee->name }}</td>
                <td>{{ $p->employee->position }}</td>
                <td class="text-right">Rp {{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($p->net_salary, 0, ',', '.') }}</td>
                <td>
                    @if($p->status === 'draft')
                        <span class="status status-draft">Draft</span>
                    @elseif($p->status === 'approved')
                        <span class="status status-approved">Approved</span>
                    @else
                        <span class="status status-paid">Dibayar</span>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr style="background-color: #f9fafb; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Total Karyawan: {{ count($payrolls) }}</p>
</body>
</html>
