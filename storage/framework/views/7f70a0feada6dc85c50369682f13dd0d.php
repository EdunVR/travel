

<?php $__env->startSection('title', 'Edit Jurnal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Jurnal #<?php echo e($journal->reference); ?></h6>
            <a href="<?php echo e(route('financial.accounting.index')); ?>" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <form id="journalForm" method="POST" action="<?php echo e(route('financial.journals.update_journal', $journal->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="date" class="form-control" 
                                value="<?php echo e($journal->date->format('Y-m-d')); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="description" class="form-control" 
                                value="<?php echo e($journal->description); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="journal-entries">
                    <?php $__currentLoopData = $journal->entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="entry row mb-3">
                        <div class="col-md-4">
                            <select name="entries[<?php echo e($index); ?>][account_id]" class="form-control account-select" required>
                                <option value="">Pilih Akun</option>
                                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($account->id); ?>" 
                                    <?php echo e($entry->account_id == $account->id ? 'selected' : ''); ?>>
                                    <?php echo e($account->code); ?> - <?php echo e($account->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="entries[<?php echo e($index); ?>][debit]" 
                                class="form-control debit" placeholder="Debit" min="0" step="0.01"
                                value="<?php echo e($entry->debit); ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="entries[<?php echo e($index); ?>][credit]" 
                                class="form-control credit" placeholder="Credit" min="0" step="0.01"
                                value="<?php echo e($entry->credit); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="entries[<?php echo e($index); ?>][memo]" 
                                class="form-control" placeholder="Memo"
                                value="<?php echo e($entry->memo); ?>">
                        </div>
                        <div class="col-md-1">
                            <?php if($index > 1): ?>
                            <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="addEntry" class="btn btn-sm btn-primary">
                            Tambah Entri
                        </button>
                        <button type="submit" class="btn btn-success float-right">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let entryCount = <?php echo e(count($journal->entries)); ?>;
    
    // Add journal entry
    $('#addEntry').click(function() {
        const newEntry = $(`<div class="entry row mb-3">
            <div class="col-md-4">
                <select name="entries[${entryCount}][account_id]" class="form-control account-select" required>
                    <option value="">Pilih Akun</option>
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($account->id); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="entries[${entryCount}][debit]" class="form-control debit" placeholder="Debit" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <input type="number" name="entries[${entryCount}][credit]" class="form-control credit" placeholder="Credit" min="0" step="0.01">
            </div>
            <div class="col-md-3">
                <input type="text" name="entries[${entryCount}][memo]" class="form-control" placeholder="Memo">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
            </div>
        </div>`);
        
        $('.journal-entries').append(newEntry);
        entryCount++;
    });

    // Remove journal entry
    $(document).on('click', '.remove-entry', function() {
        if($('.entry').length > 2) {
            $(this).closest('.entry').remove();
        } else {
            alert('Minimal harus ada 2 entri jurnal');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\accounting\edit_journal.blade.php ENDPATH**/ ?>