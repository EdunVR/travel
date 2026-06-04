<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventori</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Laporan Inventori</h1>
    <?php if($inventori->isEmpty()): ?>
        <p>Tidak ada data inventori untuk ditampilkan.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $inventori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($key + 1); ?></td>
                    <td><?php echo e($item->nama_barang); ?></td>
                    <td><?php echo e($item->kategori->nama_kategori); ?></td>
                    <td><?php echo e($item->jumlah); ?></td>
                    <td><?php echo e($item->stok); ?></td>
                    <td><?php echo e($item->status); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\inventori\laporan.blade.php ENDPATH**/ ?>