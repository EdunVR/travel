<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Kontrak Kerja</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-aktif { background-color: #d4edda; color: #155724; }
        .badge-habis { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($outlet->nama ?? 'ERP System'); ?></h2>
        <h3>Daftar Kontrak Kerja</h3>
        <p>Tanggal Cetak: <?php echo e(date('d/m/Y H:i')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Kontrak</th>
                <th width="20%">Karyawan</th>
                <th width="15%">Jenis</th>
                <th width="15%">Jabatan</th>
                <th width="15%">Periode</th>
                <th width="10%">Durasi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $kontrak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->nomor_kontrak); ?></td>
                    <td><?php echo e($item->recruitment->name ?? '-'); ?></td>
                    <td><?php echo e($item->jenis_kontrak); ?></td>
                    <td><?php echo e($item->jabatan); ?></td>
                    <td><?php echo e($item->tanggal_mulai->format('d/m/Y')); ?> - <?php echo e($item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-'); ?></td>
                    <td class="text-center"><?php echo e($item->durasi ?? '-'); ?> bln</td>
                    <td class="text-center">
                        <span class="badge badge-<?php echo e($item->status === 'aktif' ? 'aktif' : 'habis'); ?>">
                            <?php echo e(ucfirst($item->status)); ?>

                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\pdf\kontrak.blade.php ENDPATH**/ ?>