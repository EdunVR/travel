<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Harian - <?php echo e($date); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
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
        .text-left {
            text-align: left;
            padding-left: 5px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .danger {
            color: red;
        }
        .warning {
            color: orange;
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
        <h2>LAPORAN ABSENSI HARIAN</h2>
        <h3><?php echo e($dateFormatted); ?></h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 40px;">ID</th>
                <th style="width: 100px;">Nama</th>
                <th style="width: 60px;">Jabatan</th>
                <th style="width: 35px;">Jadwal Masuk</th>
                <th style="width: 35px;">Jadwal Keluar</th>
                <th style="width: 40px;">Status</th>
                <th style="width: 35px;">Jam Masuk</th>
                <th style="width: 35px;">Jam Keluar</th>
                <th style="width: 35px;">Break Keluar</th>
                <th style="width: 35px;">Break Masuk</th>
                <th style="width: 35px;">Lembur Masuk</th>
                <th style="width: 35px;">Lembur Keluar</th>
                <th style="width: 35px;">Total Jam</th>
                <th style="width: 40px;">Terlambat</th>
                <th style="width: 40px;">Pulang Cepat</th>
                <th style="width: 40px;">Lembur</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($row['fingerprint_id']); ?></td>
                <td class="text-left"><?php echo e($row['employee_name']); ?></td>
                <td><?php echo e($row['position']); ?></td>
                <td><?php echo e($row['schedule_in']); ?></td>
                <td><?php echo e($row['schedule_out']); ?></td>
                <td class="<?php echo e($row['status_class']); ?>"><?php echo e($row['status_label']); ?></td>
                <td><?php echo e($row['clock_in']); ?></td>
                <td><?php echo e($row['clock_out']); ?></td>
                <td><?php echo e($row['break_out']); ?></td>
                <td><?php echo e($row['break_in']); ?></td>
                <td><?php echo e($row['overtime_in']); ?></td>
                <td><?php echo e($row['overtime_out']); ?></td>
                <td><strong><?php echo e($row['hours_worked']); ?></strong></td>
                <td class="<?php echo e($row['late_class']); ?>"><?php echo e($row['late_minutes']); ?></td>
                <td class="<?php echo e($row['early_class']); ?>"><?php echo e($row['early_minutes']); ?></td>
                <td><?php echo e($row['overtime_minutes']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Keterangan Status:</strong></p>
        <p>H = Hadir | T = Terlambat | I = Izin | S = Sakit | A = Alpha | P = Izin Khusus</p>
        <p>Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\attendance\daily-pdf.blade.php ENDPATH**/ ?>