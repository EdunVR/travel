

<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e($title); ?></h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e($action); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="name">Nama Pelamar</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="position">Posisi</label>
                    <input type="text" class="form-control" id="position" name="position" value="<?php echo e(old('position')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department" value="<?php echo e(old('department')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="menunggu" <?php echo e(old('status') == 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                        <option value="diterima" <?php echo e(old('status') == 'diterima' ? 'selected' : ''); ?>>Diterima</option>
                        <option value="ditolak" <?php echo e(old('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="<?php echo e(old('salary')); ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga Per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo e(old('hourly_rate')); ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="jobdesk">Jobdesk</label>
                    <div id="jobdesk-container">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="jobdesk[]" placeholder="Masukkan jobdesk" value="<?php echo e(old('jobdesk.0')); ?>">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                            </div>
                        </div>
                        <?php if(old('jobdesk')): ?>
                            <?php $__currentLoopData = old('jobdesk'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($index > 0): ?>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="jobdesk[]" value="<?php echo e($job); ?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addJobdesk()">Tambah Jobdesk</button>
                </div>
                <div class="form-group">
                    <label for="fingerprint_id">Fingerprint ID</label>
                    <input type="number" class="form-control" id="fingerprint_id" name="fingerprint_id" value="<?php echo e(old('fingerprint_id')); ?>">
                </div>
                <div class="form-group">
                    <label for="is_registered_fingerprint">Status Sidik Jari</label>
                    <select class="form-control" id="is_registered_fingerprint" name="is_registered_fingerprint">
                        <option value="0" <?php echo e(old('is_registered_fingerprint', 0) == 0 ? 'selected' : ''); ?>>Belum Terdaftar</option>
                        <option value="1" <?php echo e(old('is_registered_fingerprint') == 1 ? 'selected' : ''); ?>>Terdaftar</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('hrm.recruitment.index')); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function addJobdesk() {
        const container = document.getElementById('jobdesk-container');
        const newInput = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="jobdesk[]" placeholder="Masukkan jobdesk">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="removeJobdesk(this)">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newInput);
    }

    function removeJobdesk(button) {
        button.closest('.input-group').remove();
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\recruitment\create.blade.php ENDPATH**/ ?>