<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Laporan Travel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Laporan Travel']); ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Dashboard -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.dashboard')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h4>Dashboard</h4>
                        <p>Ringkasan Metrik Utama</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <a href="<?php echo e(route('admin.inventaris.travel.report.dashboard')); ?>" class="small-box-footer">
                        Lihat Dashboard <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Departure Summary Report -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.view')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>Ringkasan Keberangkatan</h4>
                        <p>Jamaah, Revenue, Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <a href="<?php echo e(route('admin.inventaris.travel.report.departure-summary')); ?>" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Financial Report -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.view')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Laporan Keuangan</h4>
                        <p>Revenue, Costs, Profit Margin</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="<?php echo e(route('admin.inventaris.travel.report.financial')); ?>" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Operational Report -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.view')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Laporan Operasional</h4>
                        <p>Waktu Penyelesaian Stage</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <a href="<?php echo e(route('admin.inventaris.travel.report.operational')); ?>" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Team Performance Report -->
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.report.view')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h4>Kinerja Tim</h4>
                        <p>Tingkat Penyelesaian Tugas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="<?php echo e(route('admin.inventaris.travel.report.team-performance')); ?>" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/report/index.blade.php ENDPATH**/ ?>