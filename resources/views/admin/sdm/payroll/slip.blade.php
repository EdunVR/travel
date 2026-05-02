<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $payroll->employee->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 5px; }
        .info-label { width: 150px; font-weight: bold; }
        .info-value { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { background-color: #f9fafb; font-weight: bold; }
        .net-salary { background-color: #dcfce7; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 40px; }
        .signature { display: inline-block; width: 45%; text-align: center; margin-top: 60px; }
        .signature-line { border-top: 1px solid #000; padding-top: 5px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SLIP GAJI KARYAWAN</h2>
        <p>{{ $payroll->outlet ? $payroll->outlet->nama_outlet : 'MORRA ERP' }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($payroll->period . '-01')->format('F Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama Karyawan</div>
            <div class="info-value">: {{ $payroll->employee->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Posisi</div>
            <div class="info-value">: {{ $payroll->employee->position }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Departemen</div>
            <div class="info-value">: {{ $payroll->employee->department ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Pembayaran</div>
            <div class="info-value">: {{ $payroll->payment_date->format('d F Y') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" style="background-color: #e5e7eb; font-weight: bold;">PENDAPATAN</td>
            </tr>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
            </tr>
            @if($payroll->overtime_pay > 0)
            <tr>
                <td>Lembur ({{ $payroll->overtime_hours }} jam)</td>
                <td class="text-right">{{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->bonus > 0)
            <tr>
                <td>Bonus</td>
                <td class="text-right">{{ number_format($payroll->bonus, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->allowance > 0)
            <tr>
                <td>Tunjangan</td>
                <td class="text-right">{{ number_format($payroll->allowance, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="text-right">{{ number_format($payroll->gross_salary, 0, ',', '.') }}</td>
            </tr>
            
            <tr>
                <td colspan="2" style="background-color: #e5e7eb; font-weight: bold;">POTONGAN</td>
            </tr>
            @if($payroll->absent_penalty > 0)
            <tr>
                <td>Denda Tidak Hadir ({{ $payroll->absent_days }} hari)</td>
                <td class="text-right">{{ number_format($payroll->absent_penalty, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->late_penalty > 0)
            <tr>
                <td>Denda Terlambat ({{ $payroll->late_days }} hari)</td>
                <td class="text-right">{{ number_format($payroll->late_penalty, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->loan_deduction > 0)
            <tr>
                <td>Potongan Pinjaman</td>
                <td class="text-right">{{ number_format($payroll->loan_deduction, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->deduction > 0)
            <tr>
                <td>Potongan Lain-lain</td>
                <td class="text-right">{{ number_format($payroll->deduction, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->tax > 0)
            <tr>
                <td>Pajak</td>
                <td class="text-right">{{ number_format($payroll->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Potongan</td>
                <td class="text-right">{{ number_format($payroll->calculateTotalDeductions(), 0, ',', '.') }}</td>
            </tr>
            
            <tr class="net-salary">
                <td>GAJI BERSIH</td>
                <td class="text-right">{{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($payroll->notes)
    <div style="margin-top: 20px;">
        <strong>Catatan:</strong>
        <p>{{ $payroll->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature" style="float: left;">
            <div>Karyawan</div>
            <div class="signature-line">{{ $payroll->employee->name }}</div>
        </div>
        <div class="signature" style="float: right;">
            <div>HRD / Finance</div>
            <div class="signature-line">
                @if($payroll->paid_by)
                    {{ $payroll->paidBy->name }}
                @else
                    ___________________
                @endif
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>Slip gaji ini dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>Dokumen ini sah tanpa tanda tangan basah</p>
    </div>
</body>
</html>
