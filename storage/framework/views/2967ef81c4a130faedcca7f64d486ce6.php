<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk POS - <?php echo e($posSale->no_transaksi); ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 5mm;
            padding-bottom: 5mm;
            width: 70mm;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        .header .outlet-name {
            font-size: 16px;
            font-weight: bold;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .items {
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        
        .item-row {
            margin: 3px 0;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            margin: 10px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin: 5px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
        }
        
        .barcode img {
            width: 60mm;
            height: 20mm;
        }
        
        @media print {
            body {
                width: 70mm;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="outlet-name"><?php echo e(strtoupper($posSale->outlet->nama_outlet ?? 'OUTLET')); ?></div>
        <div><?php echo e($posSale->outlet->alamat ?? ''); ?></div>
        <div>Telp: <?php echo e($posSale->outlet->telepon ?? '-'); ?></div>
    </div>

    <!-- Transaction Info -->
    <div style="margin: 10px 0; font-size: 11px;">
        <div class="info-row">
            <span>No:</span>
            <span class="bold"><?php echo e($posSale->no_transaksi); ?></span>
        </div>
        <div class="info-row">
            <span>Tanggal:</span>
            <span><?php echo e(\Carbon\Carbon::parse($posSale->tanggal)->format('d/m/Y H:i')); ?></span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            <span><?php echo e($posSale->user->name ?? 'System'); ?></span>
        </div>
        <div class="info-row">
            <span>Customer:</span>
            <span><?php echo e($posSale->member->nama ?? 'Umum'); ?></span>
        </div>
        <?php if($posSale->is_bon && isset($piutang)): ?>
        <div class="info-row">
            <span>Jatuh Tempo:</span>
            <span class="bold"><?php echo e(\Carbon\Carbon::parse($piutang->tanggal_jatuh_tempo)->format('d/m/Y')); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Items -->
    <div class="items">
        <?php $__currentLoopData = $posSale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item-row">
            <div class="item-name"><?php echo e($item->nama_produk); ?></div>
            <div class="item-detail">
                <span><?php echo e($item->kuantitas); ?> x Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></span>
                <span>Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp <?php echo e(number_format($posSale->subtotal, 0, ',', '.')); ?></span>
        </div>
        
        <?php if($posSale->total_diskon > 0): ?>
        <div class="total-row">
            <span>Diskon <?php if($posSale->diskon_persen > 0): ?>(<?php echo e($posSale->diskon_persen); ?>%)<?php endif; ?>:</span>
            <span>Rp <?php echo e(number_format($posSale->total_diskon, 0, ',', '.')); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if($posSale->ppn > 0): ?>
        <div class="total-row">
            <span>PPN 10%:</span>
            <span>Rp <?php echo e(number_format($posSale->ppn, 0, ',', '.')); ?></span>
        </div>
        <?php endif; ?>
        
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>Rp <?php echo e(number_format($posSale->total, 0, ',', '.')); ?></span>
        </div>
        
        <?php if(!$posSale->is_bon): ?>
        <div class="total-row">
            <span>Bayar (<?php echo e(strtoupper($posSale->jenis_pembayaran)); ?>):</span>
            <span>Rp <?php echo e(number_format($posSale->jumlah_bayar, 0, ',', '.')); ?></span>
        </div>
        <div class="total-row">
            <span>Kembali:</span>
            <span>Rp <?php echo e(number_format($posSale->kembalian, 0, ',', '.')); ?></span>
        </div>
        <?php else: ?>
        <div class="total-row bold">
            <span>Status:</span>
            <span>BON / BELUM LUNAS</span>
        </div>
        <?php if(isset($piutang)): ?>
        <div class="total-row">
            <span>Total Piutang:</span>
            <span>Rp <?php echo e(number_format($piutang->sisa_piutang ?? 0, 0, ',', '.')); ?></span>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if($posSale->catatan): ?>
    <div style="margin: 10px 0; font-size: 11px; border-top: 1px dashed #000; padding-top: 5px;">
        <div class="bold">Catatan:</div>
        <div><?php echo e($posSale->catatan); ?></div>
    </div>
    <?php endif; ?>

    <!-- Barcode -->
    <div class="barcode">
        <?php
            $barcode = 'POS' . str_pad($posSale->id, 6, '0', STR_PAD_LEFT);
        ?>
        <img src="data:image/png;base64,<?php echo e(DNS1D::getBarcodePNG($barcode, 'C39', 2, 40)); ?>" alt="barcode">
        <div style="font-size: 10px;"><?php echo e($barcode); ?></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="bold">Terima Kasih</div>
        <div>Barang yang sudah dibeli</div>
        <div>tidak dapat ditukar/dikembalikan</div>
        <div style="margin-top: 5px;"><?php echo e(now()->format('d/m/Y H:i:s')); ?></div>
    </div>

    <script>
        window.onload = function() {
            // Auto print only if autoprint parameter is present
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                window.print();
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\pos\nota_kecil.blade.php ENDPATH**/ ?>