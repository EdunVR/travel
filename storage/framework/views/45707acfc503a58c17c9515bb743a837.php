

<?php $__env->startSection('title', 'Edit Kinerja Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Kinerja Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('hrm.performance.update', $performance->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        <?php $__currentLoopData = $recruitments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recruitment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($recruitment->id); ?>" <?php echo e($performance->recruitment_id == $recruitment->id ? 'selected' : ''); ?>>
                                <?php echo e($recruitment->name); ?> - <?php echo e($recruitment->position); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="evaluation_date">Tanggal Penilaian</label>
                    <input type="date" class="form-control" id="evaluation_date" name="evaluation_date" value="<?php echo e($performance->evaluation_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="criteria">Kriteria</label>
                    <input type="text" class="form-control" id="criteria" name="criteria" value="<?php echo e($performance->criteria); ?>" required>
                </div>
                <div class="form-group">
                    <label for="score">Nilai (0 - 10)</label>
                    <input type="number" class="form-control" id="score" name="score" value="<?php echo e($performance->score); ?>" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="remarks">Keterangan</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo e($performance->remarks); ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?php echo e(route('hrm.performance.index')); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\performance\edit.blade.php ENDPATH**/ ?>