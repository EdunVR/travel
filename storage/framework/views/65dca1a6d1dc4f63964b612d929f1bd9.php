<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Bahan</title>
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
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA BAHAN</h1>
        <p>Periode: <?php echo e(date('d/m/Y')); ?></p>
        <?php if(request('outlet') !== 'ALL' || request('unit') !== 'ALL'): ?>
        <p>
            Filter: 
            <?php echo e(request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : ''); ?>

            <?php echo e(request('unit') !== 'ALL' ? 'Satuan: ' . request('unit') : ''); ?>

        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Bahan</th>
                <th>Outlet</th>
                <th>Merk</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $bahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->kode_bahan); ?></td>
                <td><?php echo e($item->nama_bahan); ?></td>
                <td><?php echo e($item->outlet ? $item->outlet->nama_outlet : '-'); ?></td>
                <td><?php echo e($item->merk ?: '-'); ?></td>
                <td class="text-center"><?php echo e($item->harga_bahan_sum_stok ?? 0); ?></td>
                <td class="text-center"><?php echo e($item->satuan ? $item->satuan->nama_satuan : '-'); ?></td>
                <td class="text-center <?php echo e($item->is_active ? 'status-aktif' : 'status-nonaktif'); ?>">
                    <?php echo e($item->is_active ? 'AKTIF' : 'NONAKTIF'); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($bahan->count() === 0): ?>
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?php echo e(date('d/m/Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\bahan\export_pdf.blade.php ENDPATH**/ ?>