<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Laba Rugi - <?php echo e($accountingBook->name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
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
            font-size: 9pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
            font-weight: bold;
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
        .revenue-account { background-color: #e8f5e9; }
        .expense-account { background-color: #ffebee; }
        .profit-row {
            font-weight: bold;
            background-color: #f5f5f5 !important;
        }
        .net-profit-row {
            font-weight: bold;
            background-color: #e8f5e9 !important;
        }
        .positive-amount {
            color: #2e7d32;
        }
        .negative-amount {
            color: #c62828;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <p><?php echo e(config('app.name')); ?></p>
        <p>Tanggal Export: <?php echo e($exportDate); ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Tahun Buku</strong></td>
            <td width="35%">: <?php echo e($accountingBook->name); ?></td>
            <td width="15%"><strong>Dicetak oleh</strong></td>
            <td width="35%">: <?php echo e($preparedBy); ?></td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>: <?php echo e($dateFrom ? $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y') : 'Semua Periode'); ?></td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: <?php echo e($exportDate); ?></td>
        </tr>
    </table>

    <!-- Revenue Section -->
    <div class="section-title">PENDAPATAN USAHA</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profitLossData['revenues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revenue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="revenue-account">
                <td><?php echo e($revenue['code']); ?></td>
                <td><?php echo e($revenue['name']); ?></td>
                <td class="text-right <?php echo e($revenue['amount'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($revenue['amount'] >= 0): ?>
                        <?php echo e(number_format($revenue['amount'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($revenue['amount']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>TOTAL PENDAPATAN USAHA</strong></td>
                <td class="text-right <?php echo e($profitLossData['totals']['revenue'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($profitLossData['totals']['revenue'] >= 0): ?>
                        <?php echo e(number_format($profitLossData['totals']['revenue'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($profitLossData['totals']['revenue']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- COGS Section -->
    <div class="section-title">HARGA POKOK PENJUALAN</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profitLossData['cogs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cogs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="expense-account">
                <td><?php echo e($cogs['code']); ?></td>
                <td><?php echo e($cogs['name']); ?></td>
                <td class="text-right negative-amount">(<?php echo e(number_format($cogs['amount'], 2)); ?>)</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>TOTAL HPP</strong></td>
                <td class="text-right negative-amount">(<?php echo e(number_format($profitLossData['totals']['cogs'], 2)); ?>)</td>
            </tr>
        </tbody>
    </table>

    <!-- Gross Profit -->
    <table>
        <tbody>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>LABA KOTOR</strong></td>
                <td class="text-right <?php echo e($profitLossData['gross_profit'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($profitLossData['gross_profit'] >= 0): ?>
                        <?php echo e(number_format($profitLossData['gross_profit'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($profitLossData['gross_profit']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Operating Expenses Section -->
    <div class="section-title">BEBAN OPERASIONAL</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profitLossData['operating_expenses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="expense-account">
                <td><?php echo e($expense['code']); ?></td>
                <td><?php echo e($expense['name']); ?></td>
                <td class="text-right negative-amount">(<?php echo e(number_format($expense['amount'], 2)); ?>)</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>TOTAL BEBAN OPERASIONAL</strong></td>
                <td class="text-right negative-amount">(<?php echo e(number_format($profitLossData['totals']['operating_expenses'], 2)); ?>)</td>
            </tr>
        </tbody>
    </table>

    <!-- Operating Profit -->
    <table>
        <tbody>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>LABA OPERASI</strong></td>
                <td class="text-right <?php echo e($profitLossData['operating_profit'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($profitLossData['operating_profit'] >= 0): ?>
                        <?php echo e(number_format($profitLossData['operating_profit'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($profitLossData['operating_profit']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Other Income/Expenses -->
    <?php if(count($profitLossData['other_income']) > 0 || count($profitLossData['other_expenses']) > 0): ?>
    <div class="section-title">PENDAPATAN/BEBAN LAIN-LAIN</div>
    <table>
        <thead>
            <tr>
                <th width="10%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="25%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profitLossData['other_income']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="revenue-account">
                <td><?php echo e($income['code']); ?></td>
                <td><?php echo e($income['name']); ?></td>
                <td class="text-right positive-amount">
                    <?php echo e(number_format($income['amount'], 2)); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php $__currentLoopData = $profitLossData['other_expenses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="expense-account">
                <td><?php echo e($expense['code']); ?></td>
                <td><?php echo e($expense['name']); ?></td>
                <td class="text-right negative-amount">
                    (<?php echo e(number_format($expense['amount'], 2)); ?>)
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(count($profitLossData['other_income']) > 0): ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>TOTAL PENDAPATAN LAIN</strong></td>
                <td class="text-right positive-amount">
                    <?php echo e(number_format($profitLossData['totals']['other_income'], 2)); ?>

                </td>
            </tr>
            <?php endif; ?>
            
            <?php if(count($profitLossData['other_expenses']) > 0): ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>TOTAL BEBAN LAIN</strong></td>
                <td class="text-right negative-amount">
                    (<?php echo e(number_format($profitLossData['totals']['other_expenses'], 2)); ?>)
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Profit Before Tax -->
    <table>
        <tbody>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>LABA SEBELUM PAJAK</strong></td>
                <td class="text-right <?php echo e($profitLossData['profit_before_tax'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($profitLossData['profit_before_tax'] >= 0): ?>
                        <?php echo e(number_format($profitLossData['profit_before_tax'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($profitLossData['profit_before_tax']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
            
            <?php if($profitLossData['tax_expense'] > 0): ?>
            <tr class="profit-row">
                <td colspan="2" class="text-center"><strong>PAJAK PENGHASILAN (10%)</strong></td>
                <td class="text-right negative-amount">
                    (<?php echo e(number_format($profitLossData['tax_expense'], 2)); ?>)
                </td>
            </tr>
            <?php endif; ?>
            
            <tr class="net-profit-row">
                <td colspan="2" class="text-center"><strong>LABA BERSIH</strong></td>
                <td class="text-right <?php echo e($profitLossData['net_profit'] >= 0 ? 'positive-amount' : 'negative-amount'); ?>">
                    <?php if($profitLossData['net_profit'] >= 0): ?>
                        <?php echo e(number_format($profitLossData['net_profit'], 2)); ?>

                    <?php else: ?>
                        (<?php echo e(number_format(abs($profitLossData['net_profit']), 2)); ?>)
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh <?php echo e($preparedBy); ?> pada <?php echo e($exportDate); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\profit-loss\export_pdf.blade.php ENDPATH**/ ?>