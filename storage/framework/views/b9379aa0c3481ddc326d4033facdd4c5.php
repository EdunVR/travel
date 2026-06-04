<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .summary-box {
            display: inline-block;
            width: 23%;
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-box h3 {
            margin: 0;
            font-size: 14px;
        }
        .summary-box p {
            margin: 5px 0 0 0;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>LAPORAN KEUANGAN</h1>
    <div class="header-info">
        <?php if(isset($filters['start_date']) && isset($filters['end_date'])): ?>
            Periode: <?php echo e(\Carbon\Carbon::parse($filters['start_date'])->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($filters['end_date'])->format('d M Y')); ?>

        <?php else: ?>
            Semua Periode
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <div class="summary-box">
            <h3>Rp <?php echo e(number_format($totals['total_revenue'], 0, ',', '.')); ?></h3>
            <p>Total Revenue</p>
        </div>
        <div class="summary-box">
            <h3>Rp <?php echo e(number_format($totals['total_costs'], 0, ',', '.')); ?></h3>
            <p>Total Costs</p>
        </div>
        <div class="summary-box">
            <h3>Rp <?php echo e(number_format($totals['total_profit'], 0, ',', '.')); ?></h3>
            <p>Total Profit</p>
        </div>
        <div class="summary-box">
            <h3><?php echo e(number_format($totals['average_profit_margin'], 2)); ?>%</h3>
            <p>Avg Profit Margin</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Paket</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th class="text-right">Jamaah</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Costs</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($data['package_code']); ?></td>
                    <td><?php echo e($data['package_name']); ?></td>
                    <td><?php echo e(strtoupper($data['package_type'])); ?></td>
                    <td><?php echo e($data['departure_date']->format('d M Y')); ?></td>
                    <td class="text-right"><?php echo e($data['jamaah_count']); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['revenue'], 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['costs'], 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['profit'], 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['profit_margin'], 2)); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dibuat pada: <?php echo e($generatedAt->format('d M Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\pdf\financial.blade.php ENDPATH**/ ?>