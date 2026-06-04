

<?php $__env->startSection('title', 'Edit Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Absensi</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('hrm.attendance.update', $attendance->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        <?php $__currentLoopData = $recruitments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recruitment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($recruitment->id); ?>" <?php echo e($attendance->recruitment_id == $recruitment->id ? 'selected' : ''); ?>>
                                <?php echo e($recruitment->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo e($attendance->date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="clock_in">Jam Masuk</label>
                    <input type="time" class="form-control" id="clock_in" name="clock_in" value="<?php echo e($attendance->clock_in); ?>" required>
                </div>
                <div class="form-group">
                    <label for="clock_out">Jam Keluar</label>
                    <input type="time" class="form-control" id="clock_out" name="clock_out" value="<?php echo e($attendance->clock_out); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?php echo e(route('hrm.attendance.index')); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\attendance\edit.blade.php ENDPATH**/ ?>