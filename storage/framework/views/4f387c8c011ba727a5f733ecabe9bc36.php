

<?php $__env->startSection('title', 'Detail Rekening Investor'); ?>

<?php $__env->startSection('content'); ?>

<!-- Tambahkan di bagian atas -->
<?php echo $__env->make('irp.investor.partials.investment_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Tambahkan tabel riwayat investasi -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Transaksi</h5>
        <button class="btn btn-sm btn-primary" data-toggle="modal" 
                data-target="#addInvestmentModal"
                data-account-id="<?php echo e($account->id); ?>">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Investasi</h5>
                        <p class="card-text h4">
                            <?php echo e(format_uang($account->total_investment)); ?>

                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Bagi Hasil</h5>
                        <p class="card-text h4">
                            <?php echo e(format_uang($account->total_profit)); ?>

                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Bagi Hasil</h5>
                        <p class="card-text h4">
                            <?php echo e(format_uang($account->profit_balance)); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Saldo Investasi</th>
                        <th>Saldo Bagi Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $investmentBalance = 0;
                        $profitBalance = 0;
                        $transactions = $account->investments()->orderBy('date')->get();
                    ?>
                    
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            if ($transaction->type == 'investment') {
                                $investmentBalance += $transaction->amount;
                            } elseif ($transaction->type == 'deposit') {
                                $profitBalance += $transaction->amount;
                            } elseif ($transaction->type == 'withdrawal') {
                                $profitBalance -= $transaction->amount;
                            }
                        ?>
                        <tr>
                            <td><?php echo e($transaction->date->format('d/m/Y')); ?></td>
                            <td>
                                <?php if($transaction->type == 'investment'): ?>
                                    <span class="badge badge-info">Investasi</span>
                                <?php elseif($transaction->type == 'deposit'): ?>
                                    <span class="badge badge-success">Bagi Hasil</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Penarikan</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($transaction->description); ?></td>
                            <td class="text-right">
                                <?php if($transaction->type != 'withdrawal'): ?>
                                    <?php echo e(format_uang($transaction->amount)); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if($transaction->type == 'withdrawal'): ?>
                                    <?php echo e(format_uang($transaction->amount)); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if($transaction->type == 'investment'): ?>
                                    <?php echo e(format_uang($investmentBalance)); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if($transaction->type != 'investment'): ?>
                                    <?php echo e(format_uang($profitBalance)); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\account_show.blade.php ENDPATH**/ ?>