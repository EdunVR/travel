<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode Sparepart</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .barcode-container {
            display: inline-block;
            margin: 5mm;
            padding: 3mm;
            border: 1px solid #ddd;
            text-align: center;
            width: 60mm;
            height: 25mm;
            page-break-inside: avoid;
        }
        
        .barcode-title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1mm;
            line-height: 1.2;
        }
        
        .barcode-kode {
            font-size: 8px;
            color: #666;
            margin-bottom: 1mm;
        }
        
        .barcode-image {
            margin: 1mm 0;
            font-family: 'Libre Barcode 128', cursive;
            font-size: 20px;
            letter-spacing: 2px;
        }
        
        .no-print {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            .barcode-container {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Barcode</button>
        <button onclick="window.close()" class="btn btn-default">Tutup</button>
        <hr>
    </div>
    
    <div style="text-align: center;">
        <?php $__currentLoopData = $spareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sparepart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="barcode-container">
            <div class="barcode-title"><?php echo e(Str::limit($sparepart->nama_sparepart, 30)); ?></div>
            <div class="barcode-kode"><?php echo e($sparepart->kode_sparepart); ?></div>
            <div class="barcode-image">*<?php echo e($sparepart->kode_sparepart); ?>*</div>
            <div style="font-size: 7px; margin-top: 1mm;">
                <?php echo e($sparepart->merk ?: '-'); ?> | Stok: <?php echo e($sparepart->stok); ?>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\sparepart\print-barcode.blade.php ENDPATH**/ ?>