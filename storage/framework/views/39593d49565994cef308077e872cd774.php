<?php if($images->isEmpty()): ?>
    <div class="col-md-12 text-center">
        <i data-feather="image"></i> Belum ada gambar untuk produk ini
    </div>
<?php else: ?>
    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-3 mb-3">
        <div class="card">
            <img src="<?php echo e(asset('storage/'.$image->path)); ?>" class="card-img-top" alt="Product Image" style="height: 120px; object-fit: cover;">
            <div class="card-body text-center">
                <?php if($image->is_primary): ?>
                    <span class="badge badge-success mb-2">Utama</span>
                <?php else: ?>
                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="setPrimaryImage(<?php echo e($image->id_image); ?>)">
                        <i data-feather="star"></i> Jadikan Utama
                    </button>
                <?php endif; ?>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteImage(<?php echo e($image->id_image); ?>)">
                    <i data-feather="trash-2"></i> Hapus
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\produk\_images.blade.php ENDPATH**/ ?>