<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 2cm; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .signature { margin-top: 100px; float: right; text-align: center; }
        .footer { margin-top: 50px; font-size: 0.8em; text-align: center; }
        .investor-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($title); ?></h2>
        <p>Nomor: <?php echo e(strtoupper(Str::random(10))); ?></p>
    </div>
    
    <div class="investor-info">
        <p>Nama Investor: <strong><?php echo e($investor->name); ?></strong></p>
        <p>Tanggal: <strong><?php echo e($date); ?></strong></p>
    </div>
    
    <div class="content">
        <?php echo nl2br(e($content)); ?>

    </div>
    
    <?php if(isset($signature)): ?>
    <div class="signature">
        <p>Hormat kami,</p>
        <br><br><br>
        <p><u><?php echo e($signature); ?></u></p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem <?php echo e(config('app.name')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\documents\custom_template.blade.php ENDPATH**/ ?>