<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Manajemen Bagi Hasil Kelompok</h5>
    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createGroupModal">
        <i class="fas fa-plus"></i> Tambah Kelompok
    </button>
</div>

<div class="row">
    <?php if($groups->isEmpty()): ?>
        <div class="col-md-4">
            <div class="card-placeholder" data-toggle="modal" data-target="#createGroupModal">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    <?php else: ?>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card group-card">
                    <div class="card-header group-card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo e($group->name); ?></h5>
                    </div>
                    <div class="card-body group-card-body">
                        <p><?php echo e($group->description ?? 'Tidak ada deskripsi'); ?></p>
                        
                        <?php if($group->product): ?>
                            <p><strong>Produk:</strong> <?php echo e($group->product->nama_produk); ?></p>
                        <?php endif; ?>
                        
                        <?php if($group->total_quota): ?>
                            <p><strong>Total Kuota:</strong> <?php echo e(format_uang($group->total_quota)); ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Total Investasi:</strong> <?php echo e(format_uang($group->total_investment)); ?></p>
                        
                        <div class="mt-3">
                            <button class="btn btn-sm btn-info">Detail</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\profit_management\partials\group_tab.blade.php ENDPATH**/ ?>