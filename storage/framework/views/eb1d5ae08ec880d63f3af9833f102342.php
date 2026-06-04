<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan - <?php echo e($book->name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        .indent-1 { padding-left: 20px; }
        .indent-2 { padding-left: 40px; }
        .indent-3 { padding-left: 60px; }
        .footer { margin-top: 50px; text-align: right; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Tahunan</h1>
        <h2><?php echo e($book->name); ?></h2>
        <p>Periode: <?php echo e($book->start_date->format('d/m/Y')); ?> - <?php echo e($book->end_date->format('d/m/Y')); ?></p>
        <p>Dibuat pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>
    
    <h3>Laporan Laba Rugi</h3>
    <table>
        <thead>
            <tr>
                <th>Akun</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profitLoss['revenues']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revenue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($revenue['name']); ?></td>
                    <td class="text-right"><?php echo e(number_format($revenue['balance'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="text-right"><?php echo e(number_format($profitLoss['total_revenue'], 2)); ?></td>
            </tr>
            
            <?php $__currentLoopData = $profitLoss['expenses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($expense['name']); ?></td>
                    <td class="text-right"><?php echo e(number_format($expense['balance'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td>Total Biaya</td>
                <td class="text-right"><?php echo e(number_format($profitLoss['total_expense'], 2)); ?></td>
            </tr>
            
            <tr class="total-row">
                <td>Laba/Rugi Bersih</td>
                <td class="text-right"><?php echo e(number_format($profitLoss['net_profit'], 2)); ?></td>
            </tr>
        </tbody>
    </table>
    
    <h3>Neraca</h3>
    <table>
        <thead>
            <tr>
                <th>Aktiva</th>
                <th class="text-right">Jumlah</th>
                <th>Pasiva</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $maxRows = max(count($balanceSheet['assets']), count($balanceSheet['liabilities']) + count($balanceSheet['equities']));
            ?>
            
            <?php for($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                    <td>
                        <?php if(isset($balanceSheet['assets'][$i])): ?>
                            <?php echo e($balanceSheet['assets'][$i]['name']); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if(isset($balanceSheet['assets'][$i])): ?>
                            <?php echo e(number_format($balanceSheet['assets'][$i]['balance'], 2)); ?>

                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($i < count($balanceSheet['liabilities'])): ?>
                            <?php echo e($balanceSheet['liabilities'][$i]['name']); ?>

                        <?php elseif($i < count($balanceSheet['liabilities']) + count($balanceSheet['equities'])): ?>
                            <?php echo e($balanceSheet['equities'][$i - count($balanceSheet['liabilities'])]['name']); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if($i < count($balanceSheet['liabilities'])): ?>
                            <?php echo e(number_format($balanceSheet['liabilities'][$i]['balance'], 2)); ?>

                        <?php elseif($i < count($balanceSheet['liabilities']) + count($balanceSheet['equities'])): ?>
                            <?php echo e(number_format($balanceSheet['equities'][$i - count($balanceSheet['liabilities'])]['balance'], 2)); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
            
            <tr class="total-row">
                <td>Total Aktiva</td>
                <td class="text-right"><?php echo e(number_format($balanceSheet['total_assets'], 2)); ?></td>
                <td>Total Pasiva</td>
                <td class="text-right"><?php echo e(number_format($balanceSheet['total_liabilities'] + $balanceSheet['total_equities'], 2)); ?></td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: <?php echo e(auth()->user()->name); ?></p>
        <p>Tanggal: <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\book\reports\full_report.blade.php ENDPATH**/ ?>