

<?php $__env->startSection('title', 'Ledger Account'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Buku Besar: <?php echo e($account->code); ?> - <?php echo e($account->name); ?>

            </h6>
            <span class="badge badge-primary">
                Saldo Akhir: Rp <?php echo e(number_format($current_balance, 2)); ?>

            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Transaksi</th>
                            <th>Keterangan</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($transaction->date->format('d/m/Y')); ?></td>
                            <td>
                                <?php if($transaction->transaction): ?>
                                    <?php if($transaction->transaction_type === 'journal'): ?>
                                        <a href="<?php echo e(route('financial.journals.show', $transaction->transaction_id)); ?>">
                                            <?php echo e($transaction->transaction->reference); ?>

                                        </a>
                                    <?php elseif($transaction->transaction_type === 'payment'): ?>
                                        <a href="<?php echo e(route('financial.payments.show', $transaction->transaction_id)); ?>">
                                            <?php echo e($transaction->transaction->reference); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php echo e($transaction->transaction->reference); ?>

                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($transaction->description ?? $transaction->transaction?->description); ?></td>
                            <td class="text-right"><?php echo e(number_format($transaction->debit, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($transaction->credit, 2)); ?></td>
                            <td class="text-right font-weight-bold"><?php echo e(number_format($transaction->balance, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo e($transactions->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\ledger\show.blade.php ENDPATH**/ ?>