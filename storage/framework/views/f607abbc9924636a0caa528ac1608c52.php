<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
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
        <h1><?php echo e($title); ?></h1>
        <p>Tanggal: <?php echo e(date('d/m/Y H:i')); ?></p>
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
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($emp->outlet ? $emp->outlet->nama_outlet : '-'); ?></td>
                <td><?php echo e($emp->name); ?></td>
                <td><?php echo e($emp->position); ?></td>
                <td><?php echo e($emp->department ?? '-'); ?></td>
                <td>
                    <?php if($emp->status === 'active'): ?>
                        <span class="status status-active">Aktif</span>
                    <?php elseif($emp->status === 'inactive'): ?>
                        <span class="status status-inactive">Tidak Aktif</span>
                    <?php else: ?>
                        <span class="status status-resigned">Resign</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($emp->phone ?? '-'); ?></td>
                <td>Rp <?php echo e(number_format($emp->salary ?? 0, 0, ',', '.')); ?></td>
                <td><?php echo e($emp->join_date ? date('d/m/Y', strtotime($emp->join_date)) : '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Total Karyawan: <?php echo e(count($employees)); ?></p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kepegawaian\pdf.blade.php ENDPATH**/ ?>