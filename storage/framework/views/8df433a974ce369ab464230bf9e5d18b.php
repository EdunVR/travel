

<?php $__env->startSection('title'); ?> Buat RAB Template <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('rab_template.index')); ?>">RAB Template</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Buat RAB Template Baru</h3>
            </div>
            <div class="box-body">
                <?php echo $__env->make('rab_template._form', [
                    'action' => route('rab_template.store'),
                    'template' => new App\Models\RabTemplate(),
                    'products' => $products
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function() {

    // Fungsi untuk menghitung total
    $(document).on('keyup', '.rab-item-cost', function() {
        calculateTotal();
    });

    // Inisialisasi Feather Icons
    feather.replace();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\rab_template\create.blade.php ENDPATH**/ ?>