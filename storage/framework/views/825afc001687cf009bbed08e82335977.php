<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
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
        <h1><?php echo e($title); ?></h1>
        <p>Periode: <?php echo e(\Carbon\Carbon::parse($period . '-01')->format('F Y')); ?></p>
        <p>Tanggal: <?php echo e(date('d/m/Y H:i')); ?></p>
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
            <?php $totalNet = 0; ?>
            <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $totalNet += $p->net_salary; ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($p->outlet ? $p->outlet->nama_outlet : '-'); ?></td>
                <td><?php echo e($p->employee->name); ?></td>
                <td><?php echo e($p->employee->position); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($p->basic_salary, 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($p->net_salary, 0, ',', '.')); ?></td>
                <td>
                    <?php if($p->status === 'draft'): ?>
                        <span class="status status-draft">Draft</span>
                    <?php elseif($p->status === 'approved'): ?>
                        <span class="status status-approved">Approved</span>
                    <?php else: ?>
                        <span class="status status-paid">Dibayar</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr style="background-color: #f9fafb; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp <?php echo e(number_format($totalNet, 0, ',', '.')); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Total Karyawan: <?php echo e(count($payrolls)); ?></p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\payroll\pdf.blade.php ENDPATH**/ ?>