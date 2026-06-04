

<?php $__env->startSection('title', 'Search Results'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Search Results</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Search Results</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Search results for: <strong><?php echo e($query); ?></strong></h3>
                        <div class="card-tools">
                            <span class="badge badge-info"><?php echo e($results->count()); ?> results found</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($results->isEmpty()): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No results found for "<?php echo e($query); ?>"
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($result['url']); ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <i class="<?php echo e($result['icon']); ?> mr-2"></i>
                                                <?php echo e($result['title']); ?>

                                            </h5>
                                            <small>
                                                <span class="badge badge-secondary"><?php echo e(ucfirst($result['type'])); ?></span>
                                            </small>
                                        </div>
                                        <p class="mb-1 text-muted"><?php echo e($result['subtitle']); ?></p>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\search\results.blade.php ENDPATH**/ ?>