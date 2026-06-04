<div class="mb-3">
    <h4>Total Investasi: <?php echo e(format_uang($investor->total_investment)); ?></h4>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rekening</th>
                <th>Bank</th>
                <th>Investasi</th>
                <th>Persentase</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $investor->accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($account->account_number); ?></td>
                <td><?php echo e($account->bank_name); ?></td>
                <td class="text-right"><?php echo e(format_uang($account->total_investment)); ?></td>
                <td class="text-right"><?php echo e($account->profit_percentage); ?>%</td>
                <td>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" 
                            data-target="#addInvestmentModal" 
                            data-account-id="<?php echo e($account->id); ?>">
                        <i class="fas fa-plus"></i> Tambah Investasi
                    </button>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<?php echo $__env->make('irp.investor.partials.investment_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\partials\investments.blade.php ENDPATH**/ ?>