<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja Tim</title>
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
    <h1>LAPORAN KINERJA TIM</h1>
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
                <th>Nama Tim</th>
                <th class="text-right">Total Tugas</th>
                <th class="text-right">Selesai</th>
                <th class="text-right">Pending</th>
                <th class="text-right">In Progress</th>
                <th class="text-right">Terlambat</th>
                <th class="text-right">Tingkat Penyelesaian</th>
                <th class="text-right">Rata-rata Waktu (Jam)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($data['team_name']); ?></td>
                    <td class="text-right"><?php echo e($data['total_tasks']); ?></td>
                    <td class="text-right"><?php echo e($data['completed_tasks']); ?></td>
                    <td class="text-right"><?php echo e($data['pending_tasks']); ?></td>
                    <td class="text-right"><?php echo e($data['in_progress_tasks']); ?></td>
                    <td class="text-right"><?php echo e($data['overdue_tasks']); ?></td>
                    <td class="text-right"><?php echo e(number_format($data['completion_rate'], 2)); ?>%</td>
                    <td class="text-right"><?php echo e(number_format($data['average_completion_hours'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        Dibuat pada: <?php echo e($generatedAt->format('d M Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\pdf\team-performance.blade.php ENDPATH**/ ?>