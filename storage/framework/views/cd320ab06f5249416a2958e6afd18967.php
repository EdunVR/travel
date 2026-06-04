<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Satuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Satuan</h2>
        <p>Tanggal: <?php echo e(date('d/m/Y H:i')); ?></p>
        <?php if(isset($filterStatus) && $filterStatus !== 'ALL'): ?>
            <p>Filter Status: <?php echo e($filterStatus); ?></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="20%">Nama Satuan</th>
                <th width="10%">Simbol</th>
                <th width="25%">Konversi</th>
                <th width="15%">Deskripsi</th>
                <th class="text-center" width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->kode_satuan); ?></td>
                    <td><?php echo e($item->nama_satuan); ?></td>
                    <td><?php echo e($item->simbol ?? '-'); ?></td>
                    <td>
                        <?php if($item->nilai_konversi && $item->satuanUtama): ?>
                            1 <?php echo e($item->simbol); ?> = <?php echo e($item->nilai_konversi); ?> <?php echo e($item->satuanUtama->simbol); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($item->deskripsi ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($item->is_active ? 'Aktif' : 'Nonaktif'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(date('d/m/Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\satuan\export_pdf.blade.php ENDPATH**/ ?>