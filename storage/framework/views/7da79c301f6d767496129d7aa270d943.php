<!DOCTYPE html>
<html>
<head>
    <title>Laporan History Invoice Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-weight: bold; font-size: 16px; }
        .report-title { font-size: 14px; margin: 10px 0; }
        .filter-info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT. GHAVA SHANKARA NUSANTARA</div>
        <div class="report-title">LAPORAN HISTORY INVOICE PENJUALAN</div>
        <div class="filter-info">
            Status: <?php echo e(ucfirst($status)); ?> | 
            Periode: <?php echo e($start_date ? tanggal_indonesia($start_date) : 'Semua'); ?> - <?php echo e($end_date ? tanggal_indonesia($end_date) : 'Semua'); ?> |
            Tanggal Cetak: <?php echo e(date('d/m/Y H:i')); ?>

        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">No Invoice</th>
                <th width="8%">Tanggal</th>
                <th width="15%">Customer</th>
                <th width="10%">Total</th>
                <th width="8%">Status</th>
                <?php if($status != 'lunas'): ?>
                <th width="8%">Jatuh Tempo</th>
                <th width="8%">Sisa Hari</th>
                <?php else: ?>
                <th width="10%">Tanggal Bayar</th>
                <th width="8%">Jenis Bayar</th>
                <?php endif; ?>
                <th width="20%">Items</th>
                <th width="10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td><?php echo e($invoice->no_invoice); ?></td>
                <td><?php echo e(tanggal_indonesia($invoice->tanggal)); ?></td>
                <td><?php echo e($invoice->member->nama); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($invoice->total, 0, ',', '.')); ?></td>
                <td class="text-center"><?php echo e(ucfirst($invoice->status)); ?></td>
                
                <?php if($status != 'lunas'): ?>
                <td><?php echo e($invoice->due_date ? tanggal_indonesia($invoice->due_date) : '-'); ?></td>
                <td class="text-center">
                    <?php if($invoice->due_date): ?>
                        <?php
                            $now = now();
                            $dueDate = $invoice->due_date;
                            if ($dueDate < $now) {
                                $totalJamTerlambat = $dueDate->diffInHours($now);
                                if ($totalJamTerlambat < 24) {
                                    echo 'Terlambat ' . $totalJamTerlambat . ' jam';
                                } else {
                                    $hariTerlambat = floor($totalJamTerlambat / 24);
                                    $jamTerlambat = $totalJamTerlambat % 24;
                                    echo 'Terlambat ' . $hariTerlambat . ' hari ' . $jamTerlambat . ' jam';
                                }
                            } else {
                                $totalSisaJam = $now->diffInHours($dueDate, false);
                                if ($totalSisaJam < 24) {
                                    echo 'Sisa ' . $totalSisaJam . ' jam';
                                } else {
                                    $sisaHari = floor($totalSisaJam / 24);
                                    $sisaJam = $totalSisaJam % 24;
                                    echo 'Sisa ' . $sisaHari . ' hari ' . $sisaJam . ' jam';
                                }
                            }
                        ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <?php else: ?>
                <td><?php echo e($invoice->tanggal_pembayaran ? tanggal_indonesia($invoice->tanggal_pembayaran) : '-'); ?></td>
                <td class="text-center"><?php echo e($invoice->jenis_pembayaran ? ucfirst($invoice->jenis_pembayaran) : '-'); ?></td>
                <?php endif; ?>
                
                <td>
                    <?php
                        $items = $invoice->items->take(3);
                    ?>
                    <?php if($items->count() > 0): ?>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            • <?php echo e($item->deskripsi); ?> - Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?>

                            <?php if(!$loop->last): ?><br><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($invoice->items->count() > 3): ?>
                            <br>... dan <?php echo e($invoice->items->count() - 3); ?> item lainnya
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo e($invoice->user ? $invoice->user->name : '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: System | <?php echo e(date('d/m/Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\sales_management\invoice\export.blade.php ENDPATH**/ ?>