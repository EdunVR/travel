<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>



<?php $__env->startSection('title', 'Detail Payroll'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Payroll #<?php echo e($payroll->id); ?></h6>
            <div>
                <!-- <a href="<?php echo e(route('hrm.payroll.print', $payroll->id)); ?>" 
                   class="btn btn-success" target="_blank">
                    Cetak PDF
                </a> -->
                <a href="<?php echo e(url()->previous()); ?>" class="btn btn-danger">
                     Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Karyawan</th>
                            <td><?php echo e($payroll->employee->name); ?></td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td><?php echo e($payroll->employee->position); ?></td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td><?php echo e($payroll->created_at->format('F Y')); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Gaji Pokok</th>
                            <td>Rp <?php echo e(number_format($payroll->salary, 0)); ?></td>
                        </tr>
                        <tr>
                            <th>Total Jam Kerja</th>
                            <td><?php echo e($payroll->total_hours_worked); ?> jam</td>
                        </tr>
                        <tr>
                            <th>Total Gaji</th>
                            <td>Rp <?php echo e(number_format($payroll->salary + ($payroll->total_hours_worked * $payroll->hourly_rate), 0)); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="m-0 font-weight-bold">Tambahan Gaji</h6>
                        </div>
                        <div class="card-body">
                            <?php
                                $additionalSalaries = json_decode($payroll->additional_salary, true) ?? [];
                                $totalAdditional = 0;
                            ?>
                            
                            <?php if(count($additionalSalaries) > 0): ?>
                                <table class="table table-bordered">
                                    <?php $__currentLoopData = $additionalSalaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($additional['description'] ?? 'Tambahan'); ?></td>
                                        <td class="text-right">Rp <?php echo e(number_format($additional['amount'], 0)); ?></td>
                                    </tr>
                                    <?php $totalAdditional += $additional['amount']; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="font-weight-bold">
                                        <td>Total Tambahan</td>
                                        <td class="text-right">Rp <?php echo e(number_format($totalAdditional, 0)); ?></td>
                                    </tr>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">Tidak ada tambahan gaji</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h6 class="m-0 font-weight-bold">Potongan Gaji</h6>
                        </div>
                        <div class="card-body">
                            <?php
                                $deductions = json_decode($payroll->deductions, true) ?? [];
                                $totalDeductions = 0;
                            ?>
                            
                            <?php if(count($deductions) > 0): ?>
                                <table class="table table-bordered">
                                    <?php $__currentLoopData = $deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($deduction['description'] ?? 'Potongan'); ?></td>
                                        <td class="text-right">Rp <?php echo e(number_format($deduction['amount'], 0)); ?></td>
                                    </tr>
                                    <?php $totalDeductions += $deduction['amount']; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="font-weight-bold">
                                        <td>Total Potongan</td>
                                        <td class="text-right">Rp <?php echo e(number_format($totalDeductions, 0)); ?></td>
                                    </tr>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">Tidak ada potongan gaji</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-primary text-black">
                <div class="card-body text-center">
                    <h4 class="font-weight-bold mb-0">
                        Gaji Bersih: Rp <?php echo e(number_format(
                            $payroll->salary + 
                            ($payroll->total_hours_worked * $payroll->hourly_rate) + 
                            $totalAdditional - 
                            $totalDeductions
                        , 0)); ?>

                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\payroll\detail_ledger.blade.php ENDPATH**/ ?>