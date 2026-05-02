<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Gaji - {{ $payroll->employee->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #000; }
        .header { text-align: center; }
        .content { margin-top: 20px; }
        .footer { margin-top: 40px; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>STRUK GAJI</h2>
            <p>Periode: {{ date('F Y') }}</p>
        </div>
        <div class="content">
            <p><strong>Nama Karyawan:</strong> {{ $payroll->employee->name }}</p>
            <p><strong>Posisi:</strong> {{ $payroll->employee->position }}</p>
            <p><strong>Gaji Pokok:</strong> {{ format_uang($payroll->salary) }}</p>
            <p><strong>Total Jam Kerja:</strong> {{ $payroll->total_hours_worked }} Jam</p>
            <p><strong>Harga per Jam:</strong> {{ format_uang($payroll->hourly_rate) }}</p>
            <p><strong>Total Gaji:</strong> {{ format_uang($totalSalary) }}</p>

            <h4>Rincian Harian</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payroll->attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date }}</td>
                        <td>{{ $attendance->hours_worked }} Jam</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Tambahan Gaji</h4>
            <table>
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @if($payroll->additional_salary)
                        @foreach(json_decode($payroll->additional_salary) as $additional)
                        <tr>
                            <td>{{ $additional->description }}</td>
                            <td>{{ format_uang($additional->amount) }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">Tidak ada tambahan gaji.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <h4>Potongan Gaji</h4>
            <table>
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @if($payroll->deductions)
                        @foreach(json_decode($payroll->deductions) as $deduction)
                        <tr>
                            <td>{{ $deduction->description }}</td>
                            <td>{{ format_uang($deduction->amount) }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">Tidak ada potongan gaji.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="footer">
            <p>Mengetahui,</p>
            <p><strong>HRD</strong></p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
