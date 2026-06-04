<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrak Kerja - <?php echo e($recruitment->name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .contract { margin: 20px; padding: 20px; border: 1px solid #000; }
        .header { text-align: center; }
        .content { margin-top: 20px; }
        .footer { margin-top: 40px; text-align: right; }
        .jobdesk-list { margin-left: 20px; }
    </style>
</head>
<body>
    <div class="contract">
        <div class="header">
            <h2>PERJANJIAN KONTRAK KERJA</h2>
            <p>Nomor: <?php echo e($recruitment->id); ?>/PK/HRD/<?php echo e(date('Y')); ?></p>
        </div>
        <div class="content">
            <p>Pada hari ini, <?php echo e(date('d F Y')); ?>, bertempat di <?php echo e(config('app.name')); ?>, telah dibuat perjanjian kontrak kerja antara:</p>
            <p><strong>Pihak Pertama:</strong> <?php echo e($manager->name); ?> - <?php echo e($manager->position); ?> (<?php echo e($manager->department); ?>)</p>
            <p><strong>Pihak Kedua:</strong> <?php echo e($recruitment->name); ?></p>
            <p>Dengan posisi sebagai <strong><?php echo e($recruitment->position); ?></strong> di departemen <strong><?php echo e($recruitment->department); ?></strong>.</p>
            <p>Jobdesk Pihak Kedua:</p>
            <ul class="jobdesk-list">
                <?php if($recruitment->jobdesk): ?>
                    <?php $__currentLoopData = json_decode($recruitment->jobdesk); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($job); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <li>Tidak ada jobdesk.</li>
                <?php endif; ?>
            </ul>
            <p>Perjanjian ini berlaku sejak tanggal <?php echo e(date('d F Y')); ?> hingga waktu yang ditentukan kemudian.</p>
        </div>
        <div class="footer">
            <p>Mengetahui,</p>
            <p><strong>Pihak Pertama</strong></p>
            <p>_________________________</p>
            <p><strong>Pihak Kedua</strong></p>
            <p>_________________________</p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\recruitment\contract.blade.php ENDPATH**/ ?>