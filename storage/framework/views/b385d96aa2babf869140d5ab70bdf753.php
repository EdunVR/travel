<div class="modal-header">
    <h5 class="modal-title">Detail Jurnal: <?php echo e($journal->reference); ?></h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6"><strong>Tanggal:</strong> <?php echo e($journal->date->format('d/m/Y')); ?></div>
        <div class="col-md-6"><strong>Keterangan:</strong> <?php echo e($journal->description); ?></div>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Akun</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $journal->entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($entry->account->code); ?> - <?php echo e($entry->account->name); ?></td>
                <td class="text-right"><?php echo e(number_format($entry->debit, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($entry->credit, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr class="font-weight-bold">
                <td class="text-right">Total:</td>
                <td class="text-right"><?php echo e(number_format($journal->entries->sum('debit'), 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($journal->entries->sum('credit'), 2)); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\accounting\journal_detail.blade.php ENDPATH**/ ?>