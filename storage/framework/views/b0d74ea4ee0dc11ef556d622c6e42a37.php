<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .signature { margin-top: 100px; float: right; }
        .footer { margin-top: 50px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo e($title); ?></h1>
        <p>Tanggal: <?php echo e($date); ?></p>
    </div>
    
    <div class="content">
        <?php echo nl2br(e($content)); ?>

    </div>
    
    <?php if($signature): ?>
    <div class="signature">
        <p>Hormat kami,</p>
        <br><br>
        <p><?php echo e($signature); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem <?php echo e(config('app.name')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\documents\custom.blade.php ENDPATH**/ ?>