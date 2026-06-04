<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monitoring Masa Berlaku Dokumen</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        .section-title { background-color: #f2f2f2; padding: 8px; margin-top: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; font-size: 10px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .badge-green { background-color: #d4edda; color: #155724; }
        .badge-yellow { background-color: #fff3cd; color: #856404; }
        .badge-red { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($outlet->nama ?? 'ERP System'); ?></h2>
        <h3>Monitoring Masa Berlaku Dokumen HR</h3>
        <p>Tanggal Cetak: <?php echo e(date('d/m/Y H:i')); ?></p>
    </div>

    <!-- Kontrak Kerja -->
    <div class="section-title">KONTRAK KERJA</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Karyawan</th>
                <th width="15%">No. Kontrak</th>
                <th width="15%">Jabatan</th>
                <th width="15%">Tgl Selesai</th>
                <th width="15%">Sisa Hari</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $kontrak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_selesai, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->recruitment->name ?? '-'); ?></td>
                    <td><?php echo e($item->nomor_kontrak); ?></td>
                    <td><?php echo e($item->jabatan); ?></td>
                    <td><?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                    <td class="text-center"><?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Habis'); ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo e($badgeClass); ?>">
                            <?php if($item->status_warna === 'green'): ?> Aktif
                            <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                            <?php else: ?> Sudah Habis
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Surat Peringatan -->
    <div class="section-title">SURAT PERINGATAN</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Karyawan</th>
                <th width="15%">No. SP</th>
                <th width="10%">Jenis</th>
                <th width="15%">Tgl Berakhir</th>
                <th width="15%">Sisa Hari</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $sp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->recruitment->name ?? '-'); ?></td>
                    <td><?php echo e($item->nomor_sp); ?></td>
                    <td class="text-center"><?php echo e($item->jenis_sp); ?></td>
                    <td><?php echo e($item->tanggal_berakhir->format('d/m/Y')); ?></td>
                    <td class="text-center"><?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Habis'); ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo e($badgeClass); ?>">
                            <?php if($item->status_warna === 'green'): ?> Aktif
                            <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                            <?php else: ?> Sudah Habis
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Dokumen HR -->
    <div class="section-title">DOKUMEN HR</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Karyawan</th>
                <th width="15%">No. Dokumen</th>
                <th width="15%">Jenis</th>
                <th width="15%">Tgl Berakhir</th>
                <th width="15%">Sisa Hari</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $dokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                    $badgeClass = $item->status_warna === 'green' ? 'badge-green' : ($item->status_warna === 'yellow' ? 'badge-yellow' : 'badge-red');
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->recruitment->name ?? 'Umum'); ?></td>
                    <td><?php echo e($item->nomor_dokumen); ?></td>
                    <td><?php echo e($item->jenis_dokumen); ?></td>
                    <td><?php echo e($item->tanggal_berakhir->format('d/m/Y')); ?></td>
                    <td class="text-center"><?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Habis'); ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo e($badgeClass); ?>">
                            <?php if($item->status_warna === 'green'): ?> Aktif
                            <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                            <?php else: ?> Sudah Habis
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\pdf\monitoring.blade.php ENDPATH**/ ?>