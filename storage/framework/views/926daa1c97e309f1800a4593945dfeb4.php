

<?php $__env->startSection('title', 'Edit Rekening Investor'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Rekening</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('irp.investor.account.update', ['investor' => $investor->id, 'account' => $account->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="form-group">
                    <label for="account_number">Nomor Rekening*</label>
                    <input type="text" class="form-control" id="account_number" name="account_number" 
                           value="<?php echo e(old('account_number', $account->account_number)); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="bank_name">Nama Bank*</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" 
                           value="<?php echo e(old('bank_name', $account->bank_name)); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="account_name">Atas Nama*</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" 
                           value="<?php echo e(old('account_name', $account->account_name)); ?>" required>
                </div>

                <div class="form-group">
                        <label for="date">Tanggal*</label>
                        <input type="date" class="form-control" id="date" name="date"
                               value="<?php echo e(old('date', $account->date)); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tempo">Jatuh Tempo</label>
                        <input type="date" class="form-control" id="tempo" name="tempo"
                               value="<?php echo e(old('tempo', $account->tempo)); ?>">
                    </div>
                
                <div class="form-group">
                    <label for="initial_balance">Modal Rekening*</label>
                    <input type="number" class="form-control" id="initial_balance" name="initial_balance" 
                           value="<?php echo e(old('initial_balance', $account->initial_balance)); ?>" required>
                </div>
                <div class="form-group">
                    <label for="saldo_tertahan">Saldo Tertahan*</label>
                    <input type="number" class="form-control" id="saldo_tertahan" name="saldo_tertahan" 
                           value="<?php echo e(old('saldo_tertahan', $account->saldo_tertahan)); ?>">
                </div>
                <div class="form-group">
                    <label for="profit_percentage">Persentase Bagi Hasil (%)*</label>
                    <input type="number" step="0.01" class="form-control" id="profit_percentage" name="profit_percentage" 
                           value="<?php echo e(old('profit_percentage', $account->profit_percentage)); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status*</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" <?php echo e(old('status', $account->status) == 'active' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="inactive" <?php echo e(old('status', $account->status) == 'inactive' ? 'selected' : ''); ?>>Non-Aktif</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?php echo e(route('irp.investor.show', ['investor' => $investor->id, 'account' => $account->id])); ?>" 
                   class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\account_edit.blade.php ENDPATH**/ ?>