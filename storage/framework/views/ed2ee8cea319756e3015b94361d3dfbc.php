<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Surat Peringatan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-sp { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($outlet->nama ?? 'ERP System'); ?></h2>
        <h3>Daftar Surat Peringatan</h3>
        <p>Tanggal Cetak: <?php echo e(date('d/m/Y H:i')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. SP</th>
                <th width="20%">Karyawan</th>
                <th width="10%">Jenis</th>
                <th width="12%">Tgl SP</th>
                <th width="20%">Masa Berlaku</th>
                <th width="28%">Alasan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->nomor_sp); ?></td>
                    <td><?php echo e($item->recruitment->name ?? '-'); ?></td>
                    <td class="text-center">
                        <span class="badge badge-sp"><?php echo e($item->jenis_sp); ?></span>
                    </td>
                    <td><?php echo e($item->tanggal_sp->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->tanggal_berlaku->format('d/m/Y')); ?> - <?php echo e($item->tanggal_berakhir ? $item->tanggal_berakhir->format('d/m/Y') : '-'); ?></td>
                    <td><?php echo e(Str::limit($item->alasan, 50)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\pdf\sp.blade.php ENDPATH**/ ?>