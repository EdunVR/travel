<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penilaian Kinerja - <?php echo e($appraisal->employee_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .score-table th,
        .score-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .score-table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        .score-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .score-value {
            font-weight: bold;
            font-size: 14px;
        }
        .grade-box {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .grade-a { background-color: #10b981; color: white; }
        .grade-b { background-color: #3b82f6; color: white; }
        .grade-c { background-color: #f59e0b; color: white; }
        .grade-d { background-color: #ef4444; color: white; }
        .grade-e { background-color: #6b7280; color: white; }
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
        }
        .notes-section h3 {
            margin-top: 0;
            color: #2563eb;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FORMULIR PENILAIAN KINERJA KARYAWAN</h1>
        <p><?php echo e($appraisal->outlet ? $appraisal->outlet->nama_outlet : '-'); ?></p>
        <p>Periode: <?php echo e($appraisal->period); ?></p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Nama Karyawan</td>
                <td><?php echo e($appraisal->employee_name); ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td><?php echo e($appraisal->employee ? $appraisal->employee->position : '-'); ?></td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td><?php echo e($appraisal->employee ? $appraisal->employee->department : '-'); ?></td>
            </tr>
            <tr>
                <td>Tanggal Penilaian</td>
                <td><?php echo e($appraisal->appraisal_date->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td>Evaluator</td>
                <td><?php echo e($appraisal->evaluator ? $appraisal->evaluator->name : '-'); ?></td>
            </tr>
        </table>
    </div>

    <h3 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 5px;">Parameter Penilaian</h3>
    <table class="score-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Parameter</th>
                <th>Skor (0-100)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Disiplin</td>
                <td class="score-value"><?php echo e($appraisal->discipline_score); ?></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Kerjasama</td>
                <td class="score-value"><?php echo e($appraisal->teamwork_score); ?></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Hasil Kerja</td>
                <td class="score-value"><?php echo e($appraisal->work_result_score); ?></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Inisiatif</td>
                <td class="score-value"><?php echo e($appraisal->initiative_score); ?></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Target KPI</td>
                <td class="score-value"><?php echo e($appraisal->kpi_score); ?></td>
            </tr>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="2">Total Skor</td>
                <td class="score-value"><?php echo e(number_format($appraisal->total_score, 2)); ?></td>
            </tr>
            <tr style="background-color: #dbeafe; font-weight: bold;">
                <td colspan="2">Rata-rata Skor</td>
                <td class="score-value"><?php echo e(number_format($appraisal->average_score, 2)); ?></td>
            </tr>
            <tr style="background-color: #bfdbfe; font-weight: bold;">
                <td colspan="2">Grade</td>
                <td>
                    <?php
                        $gradeInfo = $appraisal->getGradeLabel();
                        $gradeClass = 'grade-' . strtolower($appraisal->grade);
                    ?>
                    <span class="grade-box <?php echo e($gradeClass); ?>"><?php echo e($appraisal->grade); ?> - <?php echo e($gradeInfo['text']); ?></span>
                </td>
            </tr>
        </tbody>
    </table>

    <?php if($appraisal->evaluator_notes): ?>
    <div class="notes-section">
        <h3>Catatan Evaluator</h3>
        <p><?php echo e($appraisal->evaluator_notes); ?></p>
    </div>
    <?php endif; ?>

    <?php if($appraisal->employee_notes): ?>
    <div class="notes-section">
        <h3>Catatan Karyawan</h3>
        <p><?php echo e($appraisal->employee_notes); ?></p>
    </div>
    <?php endif; ?>

    <?php if($appraisal->improvement_plan): ?>
    <div class="notes-section">
        <h3>Rencana Perbaikan</h3>
        <p><?php echo e($appraisal->improvement_plan); ?></p>
    </div>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Evaluator</strong></p>
            <div class="signature-line">
                <?php echo e($appraisal->evaluator ? $appraisal->evaluator->name : '-'); ?>

            </div>
        </div>
        <div class="signature-box">
            <p><strong>Karyawan</strong></p>
            <div class="signature-line">
                <?php echo e($appraisal->employee_name); ?>

            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada <?php echo e(now()->format('d F Y H:i')); ?></p>
        <p>Status: <?php echo e($appraisal->status === 'final' ? 'Final' : 'Draft'); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kinerja\pdf-single.blade.php ENDPATH**/ ?>