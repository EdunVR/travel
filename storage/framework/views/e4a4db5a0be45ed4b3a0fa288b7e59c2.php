<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekening Koran - <?php echo e($account->account_number); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 5px 0; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; }
        .transaction-table { width: 100%; border-collapse: collapse; }
        .transaction-table th, 
        .transaction-table td { border: 1px solid #ddd; padding: 5px; }
        .transaction-table th { background-color: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 9pt; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKENING KORAN INVESTASI</h2>
        <h3><?php echo e($account->bank_name); ?> - <?php echo e($account->account_number); ?></h3>
        <p>Periode: <?php echo e($startDate); ?> s/d <?php echo e($endDate); ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Investor</td>
            <td width="30%">: <?php echo e($investor->name); ?></td>
            <td width="20%">Total Investasi</td>
            <td width="30%" class="text-right">: Rp<?php echo e(number_format($totalInvestment, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo e($account->account_number); ?></td>
            <td>Saldo Akhir</td>
            <td class="text-right">: Rp<?php echo e(number_format($closingBalance, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <td>Nama Rekening</td>
            <td>: <?php echo e($account->account_name); ?></td>
            <td>Total Transaksi</td>
            <td class="text-right">: <?php echo e(count($transactions)); ?></td>
        </tr>
    </table>

    <table class="transaction-table">
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="15%">Jenis</th>
                <th width="25%">Keterangan</th>
                <th width="15%" class="text-right">Debet</th>
                <th width="15%" class="text-right">Kredit</th>
                <th width="20%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($transaction['date']); ?></td>
                <td><?php echo e($transaction['type']); ?></td>
                <td><?php echo e($transaction['description']); ?></td>
                <td class="text-right"><?php echo e($transaction['debit'] > 0 ? 'Rp'.number_format($transaction['debit'], 0, ',', '.') : ''); ?></td>
                <td class="text-right"><?php echo e($transaction['credit'] > 0 ? 'Rp'.number_format($transaction['credit'], 0, ',', '.') : ''); ?></td>
                <td class="text-right">Rp<?php echo e(number_format($transaction['balance'], 0, ',', '.')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\investor\accounts\history-pdf.blade.php ENDPATH**/ ?>