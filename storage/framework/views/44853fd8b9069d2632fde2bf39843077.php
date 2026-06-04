<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Operasional</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
    <h1>LAPORAN OPERASIONAL</h1>
    <h2 style="text-align: center; font-size: 14px; margin-top: 0;">Waktu Penyelesaian Workflow Stage</h2>
    <div class="header-info">
        <?php if(isset($filters['start_date']) && isset($filters['end_date'])): ?>
            Periode: <?php echo e(\Carbon\Carbon::parse($filters['start_date'])->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($filters['end_date'])->format('d M Y')); ?>

        <?php else: ?>
            Semua Periode
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Workflow Stage</th>
                <th class="text-right">Rata-rata Durasi (Jam)</th>
                <th class="text-right">Rata-rata Durasi (Hari)</th>
                <th class="text-right">Jumlah Paket</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(ucwords(str_replace('_', ' ', $data['stage_name']))); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['average_duration_hours'], 2)); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['average_duration_hours'] / 24, 2)); ?></td>
                    <td class="text-right"><?php echo e($data['package_count']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dibuat pada: <?php echo e($generatedAt->format('d M Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\pdf\operational.blade.php ENDPATH**/ ?>