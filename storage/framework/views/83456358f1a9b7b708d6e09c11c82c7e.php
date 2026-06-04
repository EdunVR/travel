

<?php $__env->startSection('title'); ?>
    Setting Chart of Accounts
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Setting COA</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Menu Setting</h3>
            </div>
            <div class="box-body">
                <div class="list-group">
                    <a href="<?php echo e(route('settings.coa.po-penjualan')); ?>" class="list-group-item <?php echo e(request()->is('settings/coa/po-penjualan') ? 'active' : ''); ?>">
                        <i class="fa fa-shopping-cart"></i> PO Penjualan
                    </a>
                    <a href="<?php echo e(route('settings.coa.pembelian')); ?>" class="list-group-item <?php echo e(request()->is('settings/coa/pembelian') ? 'active' : ''); ?>">
                        <i class="fa fa-truck"></i> Pembelian
                    </a>
                    <a href="<?php echo e(route('settings.coa.produksi')); ?>" class="list-group-item <?php echo e(request()->is('settings/coa/produksi') ? 'active' : ''); ?>">
                        <i class="fa fa-industry"></i> Produksi
                    </a>
                    <a href="<?php echo e(route('settings.coa.retur')); ?>" class="list-group-item <?php echo e(request()->is('settings/coa/retur') ? 'active' : ''); ?>">
                        <i class="fa fa-exchange"></i> Retur
                    </a>
                </div>
            </div>
        </div>
        
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Info COA</h3>
            </div>
            <div class="box-body">
                <div class="small">
                    <?php $__currentLoopData = $accountTypesLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-2">
                            <strong><?php echo e($label); ?>:</strong> 
                            <span class="badge bg-<?php echo e($type == 'asset' ? 'primary' : ($type == 'liability' ? 'warning' : ($type == 'equity' ? 'success' : ($type == 'revenue' ? 'info' : 'danger')))); ?>">
                                <?php echo e($accountCounts[$type] ?? 0); ?> akun
                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9">
        <?php echo $__env->yieldContent('coa-content'); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\settings\coa\index.blade.php ENDPATH**/ ?>