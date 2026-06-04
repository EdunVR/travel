<!DOCTYPE html>
<html>
<head>
    <title>Struk POS - <?php echo e($posSale->no_transaksi); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            padding-bottom: 10px;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 2px 0;
        }
        .right {
            text-align: right;
        }
        .item-row td {
            padding: 4px 0;
        }
        .company-logo {
            width: 40px;
            height: auto;
            margin-bottom: 5px;
        }
        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>
<body>
    <?php if($companySettings['logo_url']): ?>
    <div class="center">
        <img src="<?php echo e($companySettings['logo_url']); ?>" class="company-logo" alt="Logo">
    </div>
    <?php endif; ?>
    
    <div class="center bold" style="font-size: 14px;">
        <?php echo e($companySettings['company_name']); ?>

    </div>
    
    <?php if($companySettings['company_address']): ?>
    <div class="center" style="font-size: 10px;">
        <?php echo e(strip_tags($companySettings['formatted_address'])); ?>

    </div>
    <?php endif; ?>
    
    <?php if($companySettings['company_phone'] || $companySettings['company_email']): ?>
    <div class="center" style="font-size: 10px;">
        <?php if($companySettings['company_phone']): ?>
            Telp: <?php echo e($companySettings['company_phone']); ?>

        <?php endif; ?>
        <?php if($companySettings['company_email']): ?>
            <?php if($companySettings['company_phone']): ?> | <?php endif; ?>
            <?php echo e($companySettings['company_email']); ?>

        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="center" style="font-size: 10px;">
        Point of Sales
    </div>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>No. Transaksi</td>
            <td class="right"><?php echo e($posSale->no_transaksi); ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="right"><?php echo e($posSale->tanggal->format('d/m/Y H:i')); ?></td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="right"><?php echo e($posSale->user->name ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Customer</td>
            <td class="right"><?php echo e($posSale->member->nama ?? 'Pelanggan Umum'); ?></td>
        </tr>
        <?php if($posSale->outlet): ?>
        <tr>
            <td>Outlet</td>
            <td class="right"><?php echo e($posSale->outlet->nama_outlet); ?></td>
        </tr>
        <?php endif; ?>
    </table>
    
    <div class="line"></div>
    
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $posSale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="item-row">
                <td><?php echo e($item->nama_produk); ?></td>
                <td style="text-align: center;"><?php echo e($item->kuantitas); ?></td>
                <td class="right"><?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                <td class="right"><?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right bold">Rp <?php echo e(number_format($posSale->subtotal, 0, ',', '.')); ?></td>
        </tr>
        <?php if($posSale->total_diskon > 0): ?>
        <tr>
            <td>Diskon</td>
            <td class="right">Rp <?php echo e(number_format($posSale->total_diskon, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($posSale->ppn > 0): ?>
        <tr>
            <td>PPN <?php echo e($companySettings['tax_rate'] ?? 10); ?>%</td>
            <td class="right">Rp <?php echo e(number_format($posSale->ppn, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <tr style="font-size: 14px;">
            <td class="bold">TOTAL</td>
            <td class="right bold">Rp <?php echo e(number_format($posSale->total, 0, ',', '.')); ?></td>
        </tr>
    </table>
    
    <div class="line"></div>
    
    <?php if($posSale->is_bon): ?>
    <table>
        <tr>
            <td class="bold">PIUTANG</td>
            <td class="right bold">Rp <?php echo e(number_format($posSale->total, 0, ',', '.')); ?></td>
        </tr>
    </table>
    <?php else: ?>
    <table>
        <tr>
            <td>Bayar (<?php echo e(strtoupper($posSale->jenis_pembayaran)); ?>)</td>
            <td class="right">Rp <?php echo e(number_format($posSale->jumlah_bayar, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <td class="bold">Kembali</td>
            <td class="right bold">Rp <?php echo e(number_format($posSale->kembalian, 0, ',', '.')); ?></td>
        </tr>
    </table>
    <?php endif; ?>
    
    <div class="line"></div>
    
    <?php if($posSale->catatan): ?>
    <div style="margin: 8px 0;">
        <div class="bold">Catatan:</div>
        <div><?php echo e($posSale->catatan); ?></div>
    </div>
    <div class="line"></div>
    <?php endif; ?>
    
    <!-- Legal Information (if available) -->
    <?php if($companySettings['npwp']): ?>
    <div class="center" style="font-size: 9px; margin: 5px 0;">
        NPWP: <?php echo e($companySettings['npwp']); ?>

    </div>
    <?php endif; ?>
    
    <div class="center" style="margin-top: 10px;">
        Terima Kasih 🙏
    </div>
    <div class="center" style="font-size: 10px; margin-top: 5px;">
        Powered by <?php echo e($companySettings['company_name']); ?>

    </div>
    
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\pos\print.blade.php ENDPATH**/ ?>