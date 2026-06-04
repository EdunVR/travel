<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Hutang dan Piutang</title>

    <link rel="stylesheet" href="<?php echo e(asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h3, h4 {
            color: #343a40;
        }
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center">Laporan Hutang dan Piutang</h3>
        <h4 class="text-center">
            Tanggal <?php echo e(tanggal_indonesia($awal, false)); ?> s/d Tanggal <?php echo e(tanggal_indonesia($akhir, false)); ?>

        </h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Tanggal</th>
                    <th>Hutang</th>
                    <th>Piutang</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td><?php echo e($col); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">Total</td>
                    <td><?php echo e(number_format($totalHutang, 2, ',', '.')); ?></td>
                    <td><?php echo e(number_format($totalPiutang, 2, ',', '.')); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\laporan_hutang_piutang\pdf.blade.php ENDPATH**/ ?>