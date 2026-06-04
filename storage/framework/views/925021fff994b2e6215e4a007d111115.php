

<?php $__env->startSection('title', 'Edit Penggajian'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Penggajian</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('hrm.payroll.update', $payroll->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label for="recruitment_id">Karyawan</label>
                    <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($employee->id); ?>" <?php echo e($payroll->recruitment_id == $employee->id ? 'selected' : ''); ?>>
                                <?php echo e($employee->name); ?> - <?php echo e($employee->position); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji Pokok</label>
                    <input type="number" class="form-control" id="salary" name="salary" value="<?php echo e($payroll->salary); ?>" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Harga per Jam</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo e($payroll->hourly_rate); ?>" step="0.01">
                </div>
                <div class="form-group">
                    <label for="additional_salary">Tambahan Gaji</label>
                    <div id="additional-salary-container">
                        <?php if($payroll->additional_salary): ?>
                            <?php
                                $additionalSalaries = json_decode($payroll->additional_salary, true); // Decode sebagai array
                            ?>
                            <?php if(is_array($additionalSalaries)): ?> <!-- Pastikan data adalah array -->
                                <?php $__currentLoopData = $additionalSalaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="additional_salary[]" value="<?php echo e($additional['amount'] ?? ''); ?>" placeholder="Jumlah">
                                        <input type="text" class="form-control" name="additional_salary_description[]" value="<?php echo e($additional['description'] ?? ''); ?>" placeholder="Deskripsi">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" onclick="removeAdditionalSalary(this)">Hapus</button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addAdditionalSalary()">Tambah Tambahan Gaji</button>
                </div>

                <div class="form-group">
                    <label for="deductions">Potongan Gaji</label>
                    <div id="deductions-container">
                        <?php if($payroll->deductions): ?>
                            <?php
                                $deductions = json_decode($payroll->deductions, true); // Decode sebagai array
                            ?>
                            <?php if(is_array($deductions)): ?> <!-- Pastikan data adalah array -->
                                <?php $__currentLoopData = $deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="deductions[]" value="<?php echo e($deduction['amount'] ?? ''); ?>" placeholder="Jumlah">
                                        <input type="text" class="form-control" name="deductions_description[]" value="<?php echo e($deduction['description'] ?? ''); ?>" placeholder="Deskripsi">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" onclick="removeDeduction(this)">Hapus</button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="addDeduction()">Tambah Potongan Gaji</button>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="<?php echo e(route('hrm.payroll.index')); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function addAdditionalSalary() {
        const container = document.getElementById('additional-salary-container');
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control" name="additional_salary[]" placeholder="Jumlah">
            <input type="text" class="form-control" name="additional_salary_description[]" placeholder="Deskripsi">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger" onclick="removeAdditionalSalary(this)">Hapus</button>
            </div>
        `;
        container.appendChild(div);
    }

    function removeAdditionalSalary(button) {
        button.closest('.input-group').remove();
    }

    function addDeduction() {
        const container = document.getElementById('deductions-container');
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control" name="deductions[]" placeholder="Jumlah">
            <input type="text" class="form-control" name="deductions_description[]" placeholder="Deskripsi">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger" onclick="removeDeduction(this)">Hapus</button>
            </div>
        `;
        container.appendChild(div);
    }

    function removeDeduction(button) {
        button.closest('.input-group').remove();
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\payroll\edit.blade.php ENDPATH**/ ?>