<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penggajian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penggajian</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($month)->format('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Posisi</th>
                <th>Gaji Pokok</th>
                <th>Tambahan dan Potongan Gaji</th>
                <th>Total Jam Kerja</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $index => $payroll)
            @php
                // Hitung total tambahan gaji
                $totalAdditionalSalary = 0;
                if ($payroll->additional_salary) {
                    $additionalSalaries = json_decode($payroll->additional_salary, true);
                    foreach ($additionalSalaries as $additional) {
                        $totalAdditionalSalary += $additional['amount'];
                    }
                }

                // Hitung total potongan gaji
                $totalDeductions = 0;
                if ($payroll->deductions) {
                    $deductions = json_decode($payroll->deductions, true);
                    foreach ($deductions as $deduction) {
                        $totalDeductions += $deduction['amount'];
                    }
                }

                // Hitung total gaji
                $totalSalary = $payroll->salary + ($payroll->total_hours_worked * $payroll->hourly_rate) + $totalAdditionalSalary - $totalDeductions;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payroll->employee->name }}</td>
                <td>{{ $payroll->employee->position }}</td>
                <td class="text-right">{{ format_uang($payroll->salary) }}</td>
                <td>
                    @if($payroll->additional_salary || $payroll->deductions)
                        <strong>Tambahan Gaji:</strong><br>
                        @if($payroll->additional_salary)
                            @foreach(json_decode($payroll->additional_salary, true) as $additional)
                                - {{ $additional['description'] ?? 'Tanpa Deskripsi' }}: {{ format_uang($additional['amount']) }}<br>
                            @endforeach
                        @else
                            Tidak ada tambahan gaji.<br>
                        @endif

                        <strong>Potongan Gaji:</strong><br>
                        @if($payroll->deductions)
                            @foreach(json_decode($payroll->deductions, true) as $deduction)
                                - {{ $deduction['description'] ?? 'Tanpa Deskripsi' }}: {{ format_uang($deduction['amount']) }}<br>
                            @endforeach
                        @else
                            Tidak ada potongan gaji.<br>
                        @endif
                    @else
                        Tidak ada tambahan atau potongan gaji.
                    @endif
                </td>
                <td class="text-right">{{ $payroll->total_hours_worked }} Jam</td>
                <td>{{ format_uang($totalSalary, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
