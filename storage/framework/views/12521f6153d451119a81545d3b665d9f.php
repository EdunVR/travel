<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk</title>
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
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA PRODUK</h1>
        <p>Periode: <?php echo e(date('d/m/Y')); ?></p>
        <?php if(request('outlet') !== 'ALL' || request('type') !== 'ALL'): ?>
        <p>
            Filter: 
            <?php echo e(request('outlet') !== 'ALL' ? 'Outlet: ' . request('outlet') : ''); ?>

            <?php echo e(request('type') !== 'ALL' ? 'Tipe: ' . request('type') : ''); ?>

        </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Outlet</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($produk->kode_produk); ?></td>
                <td><?php echo e($produk->nama_produk); ?></td>
                <td><?php echo e($produk->outlet ? $produk->outlet->nama_outlet : '-'); ?></td>
                <td>
                    <?php
                        $types = [
                            'barang_dagang' => 'Barang Dagang',
                            'jasa' => 'Jasa',
                            'paket_travel' => 'Paket Travel',
                            'produk_kustom' => 'Kustom'
                        ];
                    ?>
                    <?php echo e($types[$produk->tipe_produk] ?? $produk->tipe_produk); ?>

                </td>
                <td><?php echo e($produk->kategori ? $produk->kategori->nama_kategori : '-'); ?></td>
                <td><?php echo e($produk->satuan ? $produk->satuan->nama_satuan : '-'); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($produk->harga_jual, 0, ',', '.')); ?></td>
                <td class="text-right"><?php echo e($produk->hpp_produk_sum_stok ?? 0); ?></td>
                <td class="<?php echo e($produk->is_active ? 'status-aktif' : 'status-nonaktif'); ?>">
                    <?php echo e($produk->is_active ? 'AKTIF' : 'NONAKTIF'); ?>

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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\produk\export_pdf.blade.php ENDPATH**/ ?>