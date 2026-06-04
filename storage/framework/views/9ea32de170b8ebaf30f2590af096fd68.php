

<?php $__env->startSection('title', 'Detail History Bagi Hasil - ' . $history->group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i data-feather="clock"></i> Detail History Bagi Hasil
                </h6>
                <a href="<?php echo e(route('irp.profit-management.index', ['tab' => 'history'])); ?>" 
                   class="btn btn-sm btn-light">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-left-primary h-100">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-primary"><?php echo e($history->group->name); ?></h5>
                            <?php if($history->group->product): ?>
                                <p class="mb-1"><strong>Produk:</strong> <?php echo e($history->group->product->nama_produk); ?></p>
                            <?php endif; ?>
                            <p class="mb-1"><strong>Periode:</strong> <?php echo e($history->period); ?></p>
                            <p class="mb-1"><strong>Tanggal Distribusi:</strong> 
                                <?php echo e($history->distribution_date->format('d F Y')); ?>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Keuntungan:</span>
                                <span class="font-weight-bold text-primary">
                                    Rp <?php echo e(number_format($history->total_profit, 0, ',', '.')); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Dibagikan:</span>
                                <span class="font-weight-bold text-success">
                                    Rp <?php echo e(number_format($history->total_profit - $history->remaining_profit, 0, ',', '.')); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sisa Keuntungan:</span>
                                <span class="font-weight-bold <?php echo e($history->remaining_profit > 0 ? 'text-danger' : 'text-success'); ?>">
                                    Rp <?php echo e(number_format($history->remaining_profit, 0, ',', '.')); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                <?php if($history->status == 'paid'): ?>
                                    <span class="badge badge-success">Dibayar</span>
                                <?php elseif($history->status == 'processed'): ?>
                                    <span class="badge badge-warning">Diproses</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($history->proof_file): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white py-2">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i data-feather="file-text"></i> Bukti Transfer
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <a href="<?php echo e(asset('storage/'.$history->proof_file)); ?>" target="_blank" 
                               class="btn btn-primary">
                                <i data-feather="download"></i> Download Bukti Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i data-feather="users"></i> Detail Pembagian Investor
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Investor</th>
                                    <th>Rekening</th>
                                    <th class="text-right">Investasi</th>
                                    <th class="text-right">Persentase</th>
                                    <th class="text-right">Bagi Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $distributions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index+1); ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?php echo e($distribution->investor->name); ?></div>
                                        <small class="text-muted">
                                            <span class="badge badge-<?php echo e($distribution->investor->category == 'internal' ? 'primary' : 'success'); ?>">
                                                <?php echo e(ucfirst($distribution->investor->category)); ?>

                                            </span>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if($distribution->account): ?>
                                        <div class="font-weight-bold"><?php echo e($distribution->account->bank_name); ?></div>
                                        <small class="text-muted"><?php echo e($distribution->account->account_number); ?></small>
                                        <?php else: ?>
                                        <span class="text-danger">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        Rp <?php echo e(number_format($distribution->investment_amount, 0, ',', '.')); ?>

                                    </td>
                                    <td class="text-right">
                                        <div class="font-weight-bold"><?php echo e(number_format($distribution->profit_percentage)); ?>%</div>
                                    </td>
                                    <td class="text-right font-weight-bold text-success">
                                        Rp <?php echo e(number_format($distribution->profit_share, 0, ',', '.')); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        feather.replace();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\profit_management\show_group_history.blade.php ENDPATH**/ ?>