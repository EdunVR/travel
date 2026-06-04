<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Buku Besar Semua Akun</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 14pt;
        }
        .header p {
            margin: 3px 0;
        }
        .account-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .account-header {
            background-color: #f2f2f2;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
        }
        .account-title {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table th {
            background-color: #e8f5e9;
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
            font-size: 8pt;
        }
        table td {
            border: 1px solid #ddd;
            padding: 3px;
            font-size: 8pt;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .balance-positive {
            color: #2e7d32;
        }
        .balance-negative {
            color: #c62828;
        }
        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 8pt;
        }
        .summary-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKU BESAR SEMUA AKUN</h1>
        <p><?php echo e(config('app.name')); ?></p>
        <p>Periode: <?php echo e($dateFrom ?? 'Awal'); ?> s/d <?php echo e($dateTo ?? 'Akhir'); ?></p>
        <p>Tahun Buku: <?php echo e($accountingBook->name); ?></p>
        <p>Tanggal Export: <?php echo e($exportDate); ?></p>
    </div>

    <?php $__currentLoopData = $allLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="account-section">
        <div class="account-header">
            <div class="account-title">
                <?php echo e($ledger['account']['code']); ?> - <?php echo e($ledger['account']['name']); ?>

            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="12%">No. Jurnal</th>
                    <th width="12%">No. Bukti</th>
                    <th width="25%">Keterangan</th>
                    <th width="10%">Sub Kelas</th>
                    <th width="8%">Debit</th>
                    <th width="8%">Kredit</th>
                    <th width="10%">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>SALDO AWAL</strong></td>
                    <td></td>
                    <td class="text-right"><?php echo e($ledger['initialBalance'] > 0 && !$ledger['isCreditAccount'] ? number_format($ledger['initialBalance'], 2) : ''); ?></td>
                    <td class="text-right"><?php echo e($ledger['initialBalance'] > 0 && $ledger['isCreditAccount'] ? number_format($ledger['initialBalance'], 2) : ''); ?></td>
                    <td class="text-right <?php echo e($ledger['initialBalance'] >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                        <?php echo e(number_format($ledger['initialBalance'], 2)); ?>

                    </td>
                </tr>
                
                <?php $__currentLoopData = $ledger['entries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($entry['date']); ?></td>
                    <td><?php echo e($entry['journal_number']); ?></td>
                    <td><?php echo e($entry['reference_number']); ?></td>
                    <td><?php echo e($entry['description']); ?></td>
                    <td><?php echo e($entry['sub_class']); ?></td>
                    <td class="text-right"><?php echo e($entry['debit'] > 0 ? number_format($entry['debit'], 2) : ''); ?></td>
                    <td class="text-right"><?php echo e($entry['credit'] > 0 ? number_format($entry['credit'], 2) : ''); ?></td>
                    <td class="text-right <?php echo e($entry['balance'] >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                        <?php echo e(number_format($entry['balance'], 2)); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <tr class="summary-row">
                    <td colspan="6" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-right"><?php echo e(number_format($ledger['totalDebit'], 2)); ?></td>
                    <td class="text-right"><?php echo e(number_format($ledger['totalCredit'], 2)); ?></td>
                    <td class="text-right <?php echo e($ledger['endingBalance'] >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                        <?php echo e(number_format($ledger['endingBalance'], 2)); ?>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="footer">
        Dicetak pada <?php echo e($exportDate); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\ledger\export_all.blade.php ENDPATH**/ ?>