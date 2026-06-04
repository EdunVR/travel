<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Outlet</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-aktif { color: green; }
        .status-nonaktif { color: red; }
        .footer { margin-top: 20px; text-align: right; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA OUTLET</h1>
        <p>Periode: <?php echo e(date('d/m/Y')); ?></p>
        <?php if(request('kota') !== 'ALL' || request('status') !== 'ALL'): ?>
        <p>
            Filter: 
            <?php echo e(request('kota') !== 'ALL' ? 'Kota: ' . request('kota') : ''); ?>

            <?php echo e(request('status') !== 'ALL' ? 'Status: ' . request('status') : ''); ?>

        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Outlet</th>
                <th>Kota</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($outlet->kode_outlet); ?></td>
                <td><?php echo e($outlet->nama_outlet); ?></td>
                <td><?php echo e($outlet->kota); ?></td>
                <td><?php echo e($outlet->alamat); ?></td>
                <td><?php echo e($outlet->telepon); ?></td>
                <td class="<?php echo e($outlet->is_active ? 'status-aktif' : 'status-nonaktif'); ?>">
                    <?php echo e($outlet->is_active ? 'AKTIF' : 'NONAKTIF'); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?php echo e(date('d/m/Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\outlet\outlet-pdf.blade.php ENDPATH**/ ?>