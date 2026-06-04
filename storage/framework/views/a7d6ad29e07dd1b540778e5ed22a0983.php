<!DOCTYPE html>
<html>
<head>
    <title>Preview Invoice - <?php echo e($invoice->no_invoice); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-info { margin-bottom: 20px; }
        .invoice-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>INVOICE PENJUALAN</h2>
        <h3><?php echo e($setting->nama_perusahaan ?? 'Company Name'); ?></h3>
    </div>

    <div class="company-info">
        <p><strong>Alamat:</strong> <?php echo e($setting->alamat ?? '-'); ?></p>
        <p><strong>Telepon:</strong> <?php echo e($setting->telepon ?? '-'); ?></p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td width="30%"><strong>No. Invoice</strong></td>
                <td>: <?php echo e($invoice->no_invoice); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: <?php echo e(tanggal_indonesia($invoice->tanggal)); ?></td>
            </tr>
            <tr>
                <td><strong>Customer</strong></td>
                <td>: <?php echo e($invoice->member->nama); ?></td>
            </tr>
            <tr>
                <td><strong>Alamat</strong></td>
                <td>: <?php echo e($invoice->member->alamat); ?></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th>Keterangan</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->deskripsi); ?></td>
                <td><?php echo e($item->keterangan ?? '-'); ?></td>
                <td><?php echo e($item->kuantitas); ?></td>
                <td><?php echo e($item->satuan ?? '-'); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($invoice->total, 0, ',', '.')); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <?php if($invoice->keterangan): ?>
    <div class="keterangan">
        <strong>Keterangan:</strong> <?php echo e($invoice->keterangan); ?>

    </div>
    <?php endif; ?>

    <div class="footer" style="margin-top: 50px;">
        <table>
            <tr>
                <td width="50%" class="text-center">
                    <br><br>
                    <p>Dibuat oleh,</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
                <td width="50%" class="text-center">
                    <br><br>
                    <p>Disetujui oleh,</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\sales_management\invoice\preview.blade.php ENDPATH**/ ?>