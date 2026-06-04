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
        <p>Periode: <?php echo e(\Carbon\Carbon::parse($month)->format('F Y')); ?></p>
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
            <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
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
            ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($payroll->employee->name); ?></td>
                <td><?php echo e($payroll->employee->position); ?></td>
                <td class="text-right"><?php echo e(format_uang($payroll->salary)); ?></td>
                <td>
                    <?php if($payroll->additional_salary || $payroll->deductions): ?>
                        <strong>Tambahan Gaji:</strong><br>
                        <?php if($payroll->additional_salary): ?>
                            <?php $__currentLoopData = json_decode($payroll->additional_salary, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                - <?php echo e($additional['description'] ?? 'Tanpa Deskripsi'); ?>: <?php echo e(format_uang($additional['amount'])); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            Tidak ada tambahan gaji.<br>
                        <?php endif; ?>

                        <strong>Potongan Gaji:</strong><br>
                        <?php if($payroll->deductions): ?>
                            <?php $__currentLoopData = json_decode($payroll->deductions, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                - <?php echo e($deduction['description'] ?? 'Tanpa Deskripsi'); ?>: <?php echo e(format_uang($deduction['amount'])); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            Tidak ada potongan gaji.<br>
                        <?php endif; ?>
                    <?php else: ?>
                        Tidak ada tambahan atau potongan gaji.
                    <?php endif; ?>
                </td>
                <td class="text-right"><?php echo e($payroll->total_hours_worked); ?> Jam</td>
                <td><?php echo e(format_uang($totalSalary, 0)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\payroll\export_pdf.blade.php ENDPATH**/ ?>