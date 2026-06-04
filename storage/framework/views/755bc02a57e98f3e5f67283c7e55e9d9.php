<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventori</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-tersedia { color: green; }
        .status-tidak-tersedia { color: red; }
        .footer { margin-top: 20px; text-align: right; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA INVENTORI</h1>
        <p>Periode: <?php echo e(date('d/m/Y')); ?></p>
        <?php if(request('outlet') !== 'ALL' || request('status') !== 'ALL'): ?>
        <p>
            Filter: 
            <?php echo e(request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : ''); ?>

            <?php echo e(request('status') !== 'ALL' ? 'Status: ' . request('status') : ''); ?>

        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Outlet</th>
                <th>Penanggung Jawab</th>
                <th>Stok</th>
                <th>Lokasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $inventori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->kode_inventori); ?></td>
                <td><?php echo e($item->nama_barang); ?></td>
                <td><?php echo e($item->kategori ? $item->kategori->nama_kategori : '-'); ?></td>
                <td><?php echo e($item->outlet ? $item->outlet->nama_outlet : '-'); ?></td>
                <td><?php echo e($item->penanggung_jawab); ?></td>
                <td><?php echo e($item->stok); ?></td>
                <td><?php echo e($item->lokasi_penyimpanan); ?></td>
                <td class="<?php echo e($item->status === 'tersedia' ? 'status-tersedia' : 'status-tidak-tersedia'); ?>">
                    <?php echo e(strtoupper($item->status)); ?>

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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\inventori\export_pdf.blade.php ENDPATH**/ ?>