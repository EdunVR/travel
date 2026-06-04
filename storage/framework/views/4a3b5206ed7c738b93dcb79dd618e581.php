<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Buku Besar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0;
        }
        .account-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        table td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .balance-positive {
            color: #2e7d32;
            font-weight: bold;
        }
        .balance-negative {
            color: #c62828;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9pt;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BUKU BESAR</h2>
        <p><?php echo e(config('app.name')); ?></p>
        <p>Tanggal Export: <?php echo e($exportDate); ?></p>
    </div>

    <div class="account-info">
        <table>
            <tr>
                <td width="20%"><strong>Akun</strong></td>
                <td width="30%"><?php echo e($account['code']); ?> - <?php echo e($account['name']); ?></td>
                <td width="20%"><strong>Tahun Buku</strong></td>
                <td width="30%"><?php echo e($accountingBook->name); ?></td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>
                    <?php if($dateFrom && $dateTo): ?>
                        <?php echo e($dateFrom); ?> s/d <?php echo e($dateTo); ?>

                    <?php else: ?>
                        Semua Periode
                    <?php endif; ?>
                </td>
                <td><strong>Saldo Awal</strong></td>
                <td class="<?php echo e($initialBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                    <?php echo e(number_format($initialBalance, 2)); ?>

                </td>
            </tr>
        </table>
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
            <!-- Initial Balance -->
            <tr>
                <td class="text-center"></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>SALDO AWAL</strong></td>
                <td></td>
                <td class="text-right"><?php echo e($initialBalance > 0 && !$isCreditAccount ? number_format($initialBalance, 2) : ''); ?></td>
                <td class="text-right"><?php echo e($initialBalance > 0 && $isCreditAccount ? number_format($initialBalance, 2) : ''); ?></td>
                <td class="text-right <?php echo e($initialBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                    <?php echo e(number_format($initialBalance, 2)); ?>

                </td>
            </tr>

            <!-- Transactions -->
            <?php $__currentLoopData = $ledgerEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

            <!-- Totals -->
            <tr>
                <th colspan="6" class="text-center">TOTAL</th>
                <th class="text-right"><?php echo e(number_format($totalDebit, 2)); ?></th>
                <th class="text-right"><?php echo e(number_format($totalCredit, 2)); ?></th>
                <th class="text-right <?php echo e($endingBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                    <?php echo e(number_format($endingBalance, 2)); ?>

                </th>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: <?php echo e(auth()->user()->name); ?> pada <?php echo e($exportDate); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\ledger\export.blade.php ENDPATH**/ ?>