<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Invoice Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; font-size: 16px; }
        .header p { margin: 3px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .status-badge { padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .status-menunggu { background: #fff3cd; color: #856404; }
        .status-lunas { background: #d1edff; color: #0c5460; }
        .status-gagal { background: #f8d7da; color: #721c24; }
        .item-details { font-size: 9px; line-height: 1.2; }
        .footer { margin-top: 15px; text-align: right; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INVOICE PENJUALAN</h1>
        <p>Periode: <?php echo e($startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-'); ?> s/d <?php echo e($endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-'); ?></p>
        <p>Status: <?php echo e(ucfirst($status)); ?> | Dicetak: <?php echo e(date('d/m/Y H:i:s')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">No Invoice</th>
                <th width="8%">Tanggal</th>
                <th width="15%">Customer</th>
                <th width="10%">Outlet</th>
                <th width="8%" class="text-right">Total</th>
                <th width="8%">Status</th>
                <th width="8%">Jatuh Tempo</th>
                <th width="26%">Rincian Items</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td><?php echo e($invoice->no_invoice); ?></td>
                <td><?php echo e($invoice->tanggal->format('d/m/Y')); ?></td>
                <td><?php echo e($invoice->member ? $invoice->member->nama : 'Customer Tidak Ditemukan'); ?></td>
                <td><?php echo e($invoice->outlet ? $invoice->outlet->nama_outlet : '-'); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($invoice->total, 0, ',', '.')); ?></td>
                <td>
                    <span class="status-badge status-<?php echo e($invoice->status); ?>">
                        <?php echo e(ucfirst($invoice->status)); ?>

                    </span>
                </td>
                <td><?php echo e($invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-'); ?></td>
                <td class="item-details">
                    <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($itemIndex + 1); ?>. <?php echo e($item->deskripsi); ?></div>
                    <div style="margin-left: 10px;">
                        Qty: <?php echo e($item->kuantitas); ?> <?php echo e($item->satuan ?? 'Unit'); ?> | 
                        Harga: Rp <?php echo e(number_format($item->harga_normal, 0, ',', '.')); ?>

                        <?php if($item->diskon > 0): ?>
                        | <span style="color: #28a745;">Diskon: -Rp <?php echo e(number_format($item->diskon, 0, ',', '.')); ?></span>
                        <?php endif; ?>
                        | Subtotal: Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?>

                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($invoices->count() === 0): ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data invoice</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: <?php echo e(auth()->user()->name ?? '-'); ?> | Total: <?php echo e($invoices->count()); ?> invoice
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\invoice\export_pdf.blade.php ENDPATH**/ ?>