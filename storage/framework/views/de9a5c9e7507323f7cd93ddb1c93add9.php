<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Piutang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-belum {
            color: orange;
            font-weight: bold;
        }
        .status-overdue {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA PIUTANG</h2>
        <p>Tanggal: <?php echo e(date('d/m/Y H:i')); ?></p>
        <?php if(isset($filters['start_date']) && isset($filters['end_date'])): ?>
        <p>Periode: <?php echo e(date('d/m/Y', strtotime($filters['start_date']))); ?> - <?php echo e(date('d/m/Y', strtotime($filters['end_date']))); ?></p>
        <?php endif; ?>
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Total Piutang:</strong> Rp <?php echo e(number_format($summary['total_piutang'], 0, ',', '.')); ?>

        </div>
        <div class="summary-item">
            <strong>Sudah Dibayar:</strong> Rp <?php echo e(number_format($summary['total_dibayar'], 0, ',', '.')); ?>

        </div>
        <div class="summary-item">
            <strong>Sisa Piutang:</strong> Rp <?php echo e(number_format($summary['total_sisa'], 0, ',', '.')); ?>

        </div>
        <div class="summary-item">
            <strong>Jatuh Tempo:</strong> <?php echo e($summary['count_overdue']); ?> transaksi
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Source</th>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Outlet</th>
                <th class="text-right">Jumlah Piutang</th>
                <th class="text-right">Dibayar</th>
                <th class="text-right">Sisa</th>
                <th>Jatuh Tempo</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $piutangData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $piutang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e(strtoupper($piutang['source'])); ?></td>
                <td><?php echo e($piutang['invoice_number']); ?></td>
                <td><?php echo e(date('d/m/Y', strtotime($piutang['tanggal']))); ?></td>
                <td><?php echo e($piutang['nama_customer']); ?></td>
                <td><?php echo e($piutang['outlet']); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($piutang['jumlah_piutang'], 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($piutang['jumlah_dibayar'], 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($piutang['sisa_piutang'], 0, ',', '.')); ?></td>
                <td>
                    <?php if($piutang['tanggal_jatuh_tempo']): ?>
                        <?php echo e(date('d/m/Y', strtotime($piutang['tanggal_jatuh_tempo']))); ?>

                        <?php if($piutang['is_overdue']): ?>
                            <br><small class="status-overdue">(Terlambat <?php echo e($piutang['days_overdue']); ?> hari)</small>
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($piutang['status'] === 'lunas'): ?>
                        <span class="status-lunas">Lunas</span>
                    <?php elseif($piutang['status'] === 'dibayar_sebagian'): ?>
                        <span class="status-belum">Dibayar Sebagian</span>
                    <?php else: ?>
                        <span class="status-belum">Belum Lunas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f4f4f4; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right">Rp <?php echo e(number_format($summary['total_piutang'], 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($summary['total_dibayar'], 0, ',', '.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($summary['total_sisa'], 0, ',', '.')); ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\piutang\pdf.blade.php ENDPATH**/ ?>