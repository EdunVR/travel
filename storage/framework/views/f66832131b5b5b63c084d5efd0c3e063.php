<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .grade-a { background-color: #10b981; color: white; }
        .grade-b { background-color: #3b82f6; color: white; }
        .grade-c { background-color: #f59e0b; color: white; }
        .grade-d { background-color: #ef4444; color: white; }
        .grade-e { background-color: #6b7280; color: white; }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-final { background-color: #10b981; color: white; }
        .status-draft { background-color: #f59e0b; color: white; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo e($title); ?></h1>
        <?php if($period): ?>
        <p>Periode: <?php echo e($period); ?></p>
        <?php endif; ?>
        <p>Dicetak pada: <?php echo e(now()->format('d F Y H:i')); ?></p>
    </div>

    <?php if($appraisals->count() > 0): ?>
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        Total Penilaian: <?php echo e($appraisals->count()); ?><br>
        Rata-rata Skor: <?php echo e(number_format($appraisals->avg('average_score'), 2)); ?><br>
        Grade A: <?php echo e($appraisals->where('grade', 'A')->count()); ?> | 
        Grade B: <?php echo e($appraisals->where('grade', 'B')->count()); ?> | 
        Grade C: <?php echo e($appraisals->where('grade', 'C')->count()); ?> | 
        Grade D: <?php echo e($appraisals->where('grade', 'D')->count()); ?> | 
        Grade E: <?php echo e($appraisals->where('grade', 'E')->count()); ?>

    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Outlet</th>
                <th style="width: 20%;">Karyawan</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 10%;">Periode</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 8%;">Skor</th>
                <th style="width: 10%;">Grade</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $appraisals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $appraisal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($appraisal->outlet ? $appraisal->outlet->nama_outlet : '-'); ?></td>
                <td><?php echo e($appraisal->employee_name); ?></td>
                <td><?php echo e($appraisal->employee ? $appraisal->employee->position : '-'); ?></td>
                <td><?php echo e($appraisal->period); ?></td>
                <td><?php echo e($appraisal->appraisal_date->format('d/m/Y')); ?></td>
                <td style="text-align: center; font-weight: bold;"><?php echo e(number_format($appraisal->average_score, 2)); ?></td>
                <td style="text-align: center;">
                    <?php
                        $gradeInfo = $appraisal->getGradeLabel();
                        $gradeClass = 'grade-' . strtolower($appraisal->grade);
                    ?>
                    <span class="grade-badge <?php echo e($gradeClass); ?>"><?php echo e($appraisal->grade); ?></span>
                </td>
                <td style="text-align: center;">
                    <span class="status-badge status-<?php echo e($appraisal->status); ?>">
                        <?php echo e($appraisal->status === 'final' ? 'Final' : 'Draft'); ?>

                    </span>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; padding: 20px; color: #666;">Tidak ada data penilaian kinerja</p>
    <?php endif; ?>

    <div class="footer">
        <p>Laporan Penilaian Kinerja - <?php echo e(now()->format('d F Y H:i')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kinerja\pdf-list.blade.php ENDPATH**/ ?>