

<?php $__env->startSection('title', isset($subClass) ? 'Edit Subclass' : 'Tambah Subclass'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i data-feather="layers"></i> <?php echo e(isset($subClass) ? 'Edit' : 'Tambah'); ?> Subclass
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" 
                  action="<?php echo e(isset($subClass) ? route('financial.book.update_sub_class', $subClass->id) : route('financial.book.store_sub_class')); ?>">
                <?php echo csrf_field(); ?>
                <?php if(isset($subClass)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="form-group">
                    <label for="accounting_book_id">Buku Akuntansi <span class="text-danger">*</span></label>
                    <select class="form-control" id="accounting_book_id" name="accounting_book_id" required>
                        <option value="">- Pilih Buku -</option>
                        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($book->id); ?>" 
                                <?php echo e((isset($subClass) && $subClass->accounting_book_id == $book->id) ? 'selected' : ''); ?>>
                                <?php echo e($book->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="code">Kode Subclass <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" 
                           value="<?php echo e($subClass->code ?? old('code')); ?>" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="name">Nama Subclass <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo e($subClass->name ?? old('name')); ?>" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo e($subClass->description ?? old('description')); ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?php echo e(route('financial.book.sub_classes')); ?>" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\book\sub_class_form.blade.php ENDPATH**/ ?>