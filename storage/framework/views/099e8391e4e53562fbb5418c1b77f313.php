<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Gaji - <?php echo e($payroll->employee->name); ?></title>
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
            <p>Periode: <?php echo e(date('F Y')); ?></p>
        </div>
        <div class="content">
            <p><strong>Nama Karyawan:</strong> <?php echo e($payroll->employee->name); ?></p>
            <p><strong>Posisi:</strong> <?php echo e($payroll->employee->position); ?></p>
            <p><strong>Gaji Pokok:</strong> <?php echo e(format_uang($payroll->salary)); ?></p>
            <p><strong>Total Jam Kerja:</strong> <?php echo e($payroll->total_hours_worked); ?> Jam</p>
            <p><strong>Harga per Jam:</strong> <?php echo e(format_uang($payroll->hourly_rate)); ?></p>
            <p><strong>Total Gaji:</strong> <?php echo e(format_uang($totalSalary)); ?></p>

            <h4>Rincian Harian</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $payroll->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($attendance->date); ?></td>
                        <td><?php echo e($attendance->hours_worked); ?> Jam</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php if($payroll->additional_salary): ?>
                        <?php $__currentLoopData = json_decode($payroll->additional_salary); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($additional->description); ?></td>
                            <td><?php echo e(format_uang($additional->amount)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Tidak ada tambahan gaji.</td>
                        </tr>
                    <?php endif; ?>
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
                    <?php if($payroll->deductions): ?>
                        <?php $__currentLoopData = json_decode($payroll->deductions); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($deduction->description); ?></td>
                            <td><?php echo e(format_uang($deduction->amount)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Tidak ada potongan gaji.</td>
                        </tr>
                    <?php endif; ?>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\payroll\print.blade.php ENDPATH**/ ?>