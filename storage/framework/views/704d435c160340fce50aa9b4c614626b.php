<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice <?php echo e($preorder->kode_preorder); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 10px;
            line-height: 1.3;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            color: #d32f2f;
        }
        .doc-number {
            text-align: center;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .payment-info {
            margin: 30px 0;
            padding: 15px;
            border: 2px solid #d32f2f;
            background-color: #fff3f3;
        }
        .payment-info h4 {
            margin: 0 0 10px 0;
            color: #d32f2f;
        }
        .payment-table {
            width: 100%;
            margin-top: 10px;
        }
        .payment-table td {
            padding: 5px 0;
        }
        .bank-info {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #2196f3;
            color: white;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. DAHANA REKAYASA NUSANTARA</div>
        <div class="company-address">
            Lingkungan Industri Kecil / UPTD Logam Blok. B2<br>
            Jl. Soekarno Hatta Km. 12.5 - Gedebage, Bandung, Jawa Barat, Indonesia 40296<br>
            Ph : +62 811 2121 511
        </div>
    </div>

    <!-- Title -->
    <div class="title">INVOICE - PRE ORDER</div>
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="status-badge"><?php echo e(strtoupper($preorder->status)); ?></span>
    </div>
    
    <!-- Document Number -->
    <div class="doc-number">
        No. <?php echo e($preorder->kode_preorder); ?>

    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <table>
            <tr>
                <td style="width: 15%;">Kepada</td>
                <td style="width: 2%;">:</td>
                <td style="width: 33%;"><?php echo e($preorder->customer->nama ?? '-'); ?></td>
                <td style="width: 15%;">Tanggal Invoice</td>
                <td style="width: 2%;">:</td>
                <td><?php echo e($preorder->updated_at->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?php echo e($preorder->customer->alamat ?? '-'); ?></td>
                <td>Tanggal Jatuh Tempo</td>
                <td>:</td>
                <td><?php echo e($preorder->updated_at->addDays(30)->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td>Telp</td>
                <td>:</td>
                <td><?php echo e($preorder->customer->no_hp ?? '-'); ?></td>
                <td>Status</td>
                <td>:</td>
                <td><strong><?php echo e(strtoupper($preorder->status)); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 40%;">DESKRIPSI</th>
                <th style="width: 10%;">QTY</th>
                <th style="width: 20%;">HARGA UNIT</th>
                <th style="width: 25%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $preorder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td><?php echo e($item->deskripsi); ?></td>
                <td class="text-center"><?php echo e(number_format($item->qty, 0)); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <!-- Totals -->
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->subtotal, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php if($preorder->diskon > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Diskon:</strong></td>
                <td class="text-right"><strong>-Rp <?php echo e(number_format($preorder->diskon, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php endif; ?>
            <?php if($preorder->pajak > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Pajak:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->pajak, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php endif; ?>
            <tr style="background-color: #f0f0f0;">
                <td colspan="4" class="text-right"><strong>TOTAL INVOICE:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Information -->
    <div class="payment-info">
        <h4>Informasi Pembayaran</h4>
        <table class="payment-table">
            <tr>
                <td style="width: 30%;">Total Invoice:</td>
                <td style="width: 5%;">:</td>
                <td><strong>Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php if($preorder->dp_amount): ?>
            <tr>
                <td>Down Payment (DP):</td>
                <td>:</td>
                <td><strong>Rp <?php echo e(number_format($preorder->dp_amount, 0, ',', '.')); ?></strong></td>
            </tr>
            <tr style="background-color: #ffebee;">
                <td>Sisa Pembayaran:</td>
                <td>:</td>
                <td><strong>Rp <?php echo e(number_format($preorder->remaining_payment, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php else: ?>
            <tr style="background-color: #ffebee;">
                <td>Jumlah yang harus dibayar:</td>
                <td>:</td>
                <td><strong>Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></strong></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Bank Info -->
    <div class="bank-info">
        <strong>Informasi Rekening Pembayaran:</strong><br>
        BCA PT Dahana Rekayasa Nusantara : 6395813432<br>
        <em>Mohon konfirmasi setelah melakukan pembayaran</em>
    </div>

    <?php if($preorder->catatan): ?>
    <div style="margin: 20px 0; padding: 10px; border-left: 4px solid #2196f3;">
        <strong>Catatan:</strong><br>
        <?php echo e($preorder->catatan); ?>

    </div>
    <?php endif; ?>

    <!-- Signature -->
    <div class="signature">
        <div class="signature-box">
            Hormat Kami,<br><br>
            <div class="signature-line"></div>
            <strong>Egie Helmi Fauzi</strong><br>
            Direktur
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        Invoice ini dicetak pada <?php echo e(now()->format('d/m/Y H:i:s')); ?><br>
        Terima kasih atas kepercayaan Anda kepada PT. Dahana Rekayasa Nusantara
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\pre-orders\pdf\invoice.blade.php ENDPATH**/ ?>