<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi <?php echo e($preorder->kode_preorder); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 11px;
            line-height: 1.4;
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
        .kwitansi-number {
            text-align: center;
            font-size: 14px;
            margin-bottom: 40px;
        }
        .content {
            margin: 40px 0;
            font-size: 16px;
            line-height: 2;
        }
        .content-row {
            margin-bottom: 20px;
            display: flex;
            align-items: baseline;
        }
        .content-label {
            width: 200px;
            display: inline-block;
        }
        .content-colon {
            width: 20px;
            display: inline-block;
        }
        .content-value {
            flex: 1;
            font-weight: bold;
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
            min-height: 20px;
        }
        .amount-words {
            font-style: italic;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 80px;
            margin-bottom: 10px;
        }
        .date-location {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .stamp-area {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .payment-details {
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #000;
            background-color: #f5f5f5;
        }
        .payment-details h4 {
            margin: 0 0 15px 0;
            text-align: center;
            font-size: 16px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-table td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }
        .payment-table .label {
            width: 40%;
            font-weight: bold;
        }
        .payment-table .amount {
            text-align: right;
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
    <div class="title">KWITANSI</div>
    
    <!-- Kwitansi Number -->
    <div class="kwitansi-number">
        No. <?php echo e($preorder->kode_preorder); ?>/KWIT
    </div>

    <!-- Date and Location -->
    <div class="date-location">
        Bandung, <?php echo e(now()->format('d F Y')); ?>

    </div>

    <!-- Content -->
    <div class="content">
        <div class="content-row">
            <span class="content-label">Sudah terima dari</span>
            <span class="content-colon">:</span>
            <span class="content-value"><?php echo e($preorder->customer->nama ?? '-'); ?></span>
        </div>

        <div class="content-row">
            <span class="content-label">Uang sejumlah</span>
            <span class="content-colon">:</span>
            <span class="content-value">Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></span>
        </div>

        <div class="amount-words">
            <strong>Terbilang:</strong> <?php echo e($this->terbilang($preorder->total)); ?> Rupiah
        </div>

        <div class="content-row">
            <span class="content-label">Untuk pembayaran</span>
            <span class="content-colon">:</span>
            <span class="content-value">Pelunasan Pre Order <?php echo e($preorder->kode_preorder); ?></span>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="payment-details">
        <h4>Rincian Pembayaran</h4>
        <table class="payment-table">
            <tr>
                <td class="label">Total Invoice:</td>
                <td class="amount">Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></td>
            </tr>
            <?php if($preorder->dp_amount): ?>
            <tr>
                <td class="label">Down Payment (DP):</td>
                <td class="amount">Rp <?php echo e(number_format($preorder->dp_amount, 0, ',', '.')); ?></td>
            </tr>
            <tr>
                <td class="label">Pelunasan:</td>
                <td class="amount">Rp <?php echo e(number_format($preorder->remaining_payment, 0, ',', '.')); ?></td>
            </tr>
            <?php endif; ?>
            <tr style="border-top: 2px solid #000;">
                <td class="label"><strong>Total Diterima:</strong></td>
                <td class="amount"><strong>Rp <?php echo e(number_format($preorder->total, 0, ',', '.')); ?></strong></td>
            </tr>
        </table>
    </div>

    <?php if($preorder->catatan): ?>
    <div style="margin: 20px 0; padding: 15px; border-left: 4px solid #2196f3;">
        <strong>Catatan:</strong><br>
        <?php echo e($preorder->catatan); ?>

    </div>
    <?php endif; ?>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Yang Menerima,</div>
            <div class="signature-line"></div>
            <div><strong><?php echo e($preorder->customer->nama ?? '-'); ?></strong></div>
            <div style="font-size: 12px;">Customer</div>
        </div>

        <div class="signature-box">
            <div>Yang Menyerahkan,</div>
            <div class="signature-line"></div>
            <div><strong>Egie Helmi Fauzi</strong></div>
            <div style="font-size: 12px;">Direktur</div>
        </div>
    </div>

    <!-- Stamp Area -->
    <div class="stamp-area">
        <div style="border: 2px dashed #ccc; padding: 20px; margin: 20px auto; width: 150px; height: 80px; display: flex; align-items: center; justify-content: center;">
            MATERAI<br>
            Rp 10.000
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 11px; color: #666; border-top: 1px solid #ccc; padding-top: 20px;">
        Kwitansi ini dicetak pada <?php echo e(now()->format('d/m/Y H:i:s')); ?><br>
        <strong>Terima kasih atas kepercayaan Anda kepada PT. Dahana Rekayasa Nusantara</strong>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\pre-orders\pdf\kwitansi.blade.php ENDPATH**/ ?>