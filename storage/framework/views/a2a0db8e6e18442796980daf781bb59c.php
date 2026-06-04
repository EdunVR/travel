<!DOCTYPE html>
<html>
<head>
    <title>INVOICE PENJUALAN - <?php echo e($invoice->no_invoice); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .logo {
            width: 60px;
            height: auto;
            float: left;
            margin-right: 15px;
        }
        
        .company-info {
            overflow: hidden;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .invoice-detail {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .customer-info {
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 8px 6px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .bank-info {
            border: 1px solid #000;
            padding: 10px;
            background-color: #f8f9fa;
            font-size: 11px;
        }
        
        .signature {
            display: table;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px 0;
            width: 80%;
            display: inline-block;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
        
        .avoid-break {
            page-break-before: avoid;
        }
    </style>
</head>
<body>
    <div class="header keep-together">
        <div class="header-left">
            <img src="<?php echo e(public_path('img/logo-ghava.png')); ?>" class="logo" alt="Logo">
            <div class="company-info">
                <div class="company-name">PT. GHAVA SHANKARA NUSANTARA</div>
                <div class="company-address">
                    <?php echo e($setting->alamat ?? 'Komplek LIK Blok B2, Kec. Panyileukan, Kota Bandung'); ?><br>
                    Telp: <?php echo e($setting->telepon ?? '0812-220-033'); ?> | 
                    Email: <?php echo e($setting->email ?? 'marketing@dahana-boiler.com'); ?>

                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="invoice-info">
                <div class="invoice-title">INVOICE PENJUALAN</div>
                <div class="invoice-detail">
                    <strong>No:</strong> <?php echo e($invoice->no_invoice); ?><br>
                    <strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y')); ?>

                </div>
            </div>
        </div>
    </div>

    <div class="customer-info keep-together">
        <div><strong>Kepada:</strong></div>
        <div><strong><?php echo e($invoice->member->nama); ?></strong></div>
        <div><?php echo e($invoice->member->alamat); ?></div>
        <div>Telp: <?php echo e($invoice->member->telepon); ?></div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Deskripsi</th>
                    <th width="20%">Keterangan</th>
                    <th width="8%" class="text-center">Qty</th>
                    <th width="10%" class="text-center">Satuan</th>
                    <th width="12%" class="text-right">Harga (Rp)</th>
                    <th width="15%" class="text-right">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="avoid-break">
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->deskripsi); ?></td>
                    <td><?php echo e($item->keterangan ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($item->kuantitas); ?></td>
                    <td class="text-center"><?php echo e($item->satuan ?? '-'); ?></td>
                    <td class="text-right"><?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr class="total-row avoid-break">
                    <td colspan="6" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong><?php echo e(number_format($invoice->total, 0, ',', '.')); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if($invoice->keterangan): ?>
    <div class="keterangan">
        <strong>Keterangan:</strong> <?php echo e($invoice->keterangan); ?>

    </div>
    <?php endif; ?>

    <div class="footer keep-together">
        <div class="payment-deadline" style="margin-bottom: 15px; padding: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
            <small style="font-size: 10px;">
                <strong>* Catatan:</strong> Batas terakhir pembayaran adalah 30 hari setelah tanggal invoice. 
                Invoice ini harus dilunasi paling lambat tanggal 
                <strong><?php echo e(\Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y')); ?></strong>.
            </small>
        </div>
        <div class="signature">
            <div class="signature-box">
                <div>Hormat Kami</div>
                <div style="margin-bottom: 0px;">admin</div>
                <div style="margin-bottom: 0px;">
                    <img src="<?php echo e(public_path('img/tiktik.png')); ?>" alt="Tanda Tangan" style="height: 80px; width: auto;">
                </div>
                <div>Tiktik Atikasari</div>
            </div>
        </div>
        <div class="bank-info">
            <strong>TRANSFER REKENING KE:</strong><br>
            <strong>Atas nama:</strong> PT. Ghava Shankara Nusantara<br>
            <strong>Bank:</strong> Mandiri<br>
            <strong>No Rekening:</strong> 1300027168247
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\sales_management\invoice\print.blade.php ENDPATH**/ ?>