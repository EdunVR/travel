<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <?php $__currentLoopData = $dataproduk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td class="text-center" style="border: 1px solid #333;">
                    <p><?php echo e($produk->nama_produk); ?> - Rp. <?php echo e(format_uang($produk->harga_jual)); ?></p>
                    <img src="data:image/png;base64,<?php echo e(DNS1D::getBarcodePNG($produk->kode_produk, 'C39')); ?>" 
                        alt="<?php echo e($produk->kode_produk); ?>"
                        width="180"
                        height="60">
                    <br>
                    <?php echo e($produk->kode_produk); ?>

                </td>
                <?php if($no++ % 3 == 0): ?>
                    </tr><tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\produk\barcode.blade.php ENDPATH**/ ?>