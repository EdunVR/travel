

<?php $__env->startSection('title'); ?>
    Transaksi Penjualan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Transaksi Penjualan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="alert alert-success alert-dismissible">
                    <i class="fa fa-check icon"></i>
                    Data Transaksi telah selesai.
                </div>
            </div>
            <div class="box-footer">
                <?php if($setting->tipe_nota == 1): ?>
                <button class="btn btn-warning btn-flat" onclick="notaKecil('<?php echo e(route('transaksi.nota_kecil')); ?>', 'Nota Kecil')">Cetak Ulang Nota</button>
                <?php else: ?>
                <button class="btn btn-warning btn-flat" onclick="notaBesar('<?php echo e(route('transaksi.nota_besar', ['isChecked' => 'true'])); ?>', 'Nota PDF')">Cetak Ulang Nota</button>
                <?php endif; ?>
                <a href="<?php echo e(route('transaksi.baru')); ?>" class="btn btn-primary btn-flat">Transaksi Baru</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto print setelah transaksi selesai
    $(document).ready(function() {
        <?php if(session('auto_print') && $setting->tipe_nota == 1): ?>
            notaKecil('<?php echo e(route('transaksi.nota_kecil')); ?>', 'Nota Kecil');
        <?php elseif(session('auto_print') && $setting->tipe_nota == 2): ?>
            notaBesar('<?php echo e(route('transaksi.nota_besar', ['isChecked' => session('isChecked') ?? 'false'])); ?>', 'Nota PDF');
        <?php endif; ?>
    });

    function notaKecil(url, title) {
        popupCenter(url, title, 625, 500);
    }

    function notaBesar(url, title) {
        popupCenter(url, title, 900, 675);
    }

    function popupCenter(url, title, w, h) {
        const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
        const dualScreenTop  = window.screenTop  !==  undefined ? window.screenTop  : window.screenY;

        const width  = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left       = (width - w) / 2 / systemZoom + dualScreenLeft
        const top        = (height - h) / 2 / systemZoom + dualScreenTop
        const newWindow  = window.open(url, title, 
        `
            scrollbars=yes,
            width  = ${w / systemZoom}, 
            height = ${h / systemZoom}, 
            top    = ${top}, 
            left   = ${left}
        `
        );

        if (window.focus) newWindow.focus();
        return newWindow;
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\penjualan\selesai.blade.php ENDPATH**/ ?>