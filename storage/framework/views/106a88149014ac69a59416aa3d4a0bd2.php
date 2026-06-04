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



<?php $__env->startSection('title', 'Detail Jurnal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-book mr-2"></i>
                Detail Jurnal #<?php echo e($journal->reference); ?>

            </h6>
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">No. Referensi</th>
                            <td><?php echo e($journal->reference); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?php echo e($journal->date->format('d/m/Y')); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tipe Transaksi</th>
                            <td>
                                <span class="badge badge-<?php echo e($journal->transaction_type === 'manual' ? 'primary' : 
                                    ($journal->transaction_type === 'penjualan' ? 'success' :
                                    ($journal->transaction_type === 'pembelian' ? 'warning' : 'info'))); ?>">
                                    <?php echo e(ucfirst($journal->transaction_type ?? 'manual')); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Debit</th>
                            <td>Rp <?php echo e(number_format($journal->entries->sum('debit'), 0)); ?></td>
                        </tr>
                        <tr>
                            <th>Total Kredit</th>
                            <td>Rp <?php echo e(number_format($journal->entries->sum('credit'), 0)); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="font-weight-bold">Keterangan:</h5>
                <p><?php echo e($journal->description ?? 'Tidak ada keterangan'); ?></p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Akun</th>
                            <th class="text-center bg-success text-white">Debit</th>
                            <th class="text-center bg-danger text-white">Kredit</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $journal->entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($entry->account->code); ?></strong> - <?php echo e($entry->account->name); ?>

                                <br>
                                <small class="text-muted"><?php echo e(ucfirst($entry->account->type)); ?></small>
                            </td>
                            <td class="text-right text-success font-weight-bold">
                                <?php if($entry->debit > 0): ?>
                                Rp <?php echo e(number_format($entry->debit, 0)); ?>

                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td class="text-right text-danger font-weight-bold">
                                <?php if($entry->credit > 0): ?>
                                Rp <?php echo e(number_format($entry->credit, 0)); ?>

                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($entry->memo ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\accounting\journal_detail_ledger.blade.php ENDPATH**/ ?>