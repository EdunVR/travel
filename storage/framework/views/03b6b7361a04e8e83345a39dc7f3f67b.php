

<?php $__env->startSection('title', 'Edit Pelatihan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Pelatihan</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('hrm.training.update', $training->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        <?php $__currentLoopData = $recruitments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recruitment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($recruitment->id); ?>" <?php echo e($training->recruitment_id == $recruitment->id ? 'selected' : ''); ?>>
                                <?php echo e($recruitment->name); ?> - <?php echo e($recruitment->position); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="training_name">Nama Pelatihan</label>
                    <input type="text" class="form-control" id="training_name" name="training_name" value="<?php echo e($training->training_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($training->start_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($training->end_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="trainer">Pelatih</label>
                    <input type="text" class="form-control" id="trainer" name="trainer" value="<?php echo e($training->trainer); ?>" required>
                </div>
                <div class="form-group">
                    <label for="location">Lokasi</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo e($training->location); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo e($training->description); ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?php echo e(route('hrm.training.index')); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\training\edit.blade.php ENDPATH**/ ?>