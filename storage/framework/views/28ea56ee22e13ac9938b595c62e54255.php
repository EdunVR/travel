<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Bulanan - <?php echo e($monthName); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            font-size: 7px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .danger {
            color: red;
        }
        .text-left {
            text-align: left;
            padding-left: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
        }
        .footer p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN ABSENSI BULANAN</h2>
        <h3><?php echo e($monthName); ?></h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" style="width: 100px;">Nama</th>
                <th rowspan="2" style="width: 60px;">Jabatan</th>
                <th colspan="<?php echo e($daysInMonth); ?>">Tanggal</th>
                <th colspan="6">Summary</th>
            </tr>
            <tr>
                <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                    <th style="width: 12px;"><?php echo e($day); ?></th>
                <?php endfor; ?>
                <th style="width: 25px;">H</th>
                <th style="width: 25px;">A</th>
                <th style="width: 30px;">Jam</th>
                <th style="width: 30px;">T</th>
                <th style="width: 30px;">PC</th>
                <th style="width: 30px;">L</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td class="text-left"><?php echo e($row['employee_name']); ?></td>
                <td><?php echo e($row['position']); ?></td>
                <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                    <td class="<?php echo e($row['days'][$day]['symbol'] === 'H' ? 'success' : 'danger'); ?>">
                        <?php echo e($row['days'][$day]['symbol']); ?>

                    </td>
                <?php endfor; ?>
                <td><strong><?php echo e($row['summary']['present']); ?></strong></td>
                <td><?php echo e($row['summary']['absent']); ?></td>
                <td><strong><?php echo e($row['summary']['hours']); ?></strong></td>
                <td><?php echo e($row['summary']['late']); ?></td>
                <td><?php echo e($row['summary']['early']); ?></td>
                <td><?php echo e($row['summary']['overtime']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Keterangan:</strong></p>
        <p>H = Hadir | - = Tidak Hadir/Izin/Sakit/Alpha</p>
        <p><strong>Summary:</strong> H = Hadir | A = Absen | Jam = Total Jam Kerja | T = Terlambat (menit) | PC = Pulang Cepat (menit) | L = Lembur (jam)</p>
        <p>Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\attendance\monthly-pdf.blade.php ENDPATH**/ ?>