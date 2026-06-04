<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kategori</title>
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
        <h1>LAPORAN DATA KATEGORI</h1>
        <p>Periode: <?php echo e(date('d/m/Y')); ?></p>
        <?php if(request('kelompok') !== 'ALL' || request('status') !== 'ALL'): ?>
        <p>
            Filter: 
            <?php echo e(request('kelompok') !== 'ALL' ? 'Kelompok: ' . request('kelompok') : ''); ?>

            <?php echo e(request('status') !== 'ALL' ? 'Status: ' . request('status') : ''); ?>

        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Kelompok</th>
                <th>Outlet</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->kode_kategori); ?></td>
                <td><?php echo e($item->nama_kategori); ?></td>
                <td><?php echo e($item->kelompok); ?></td>
                <td><?php echo e($item->outlet ? $item->outlet->nama_outlet : '-'); ?></td>
                <td class="<?php echo e($item->is_active ? 'status-aktif' : 'status-nonaktif'); ?>">
                    <?php echo e($item->is_active ? 'AKTIF' : 'NONAKTIF'); ?>

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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\kategori\kategori-pdf.blade.php ENDPATH**/ ?>