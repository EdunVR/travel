<style>
    .badge-warning {
        background-color: #f6c23e;
        color: #1f2d3d;
    }
    .badge-success {
        background-color: #1cc88a;
    }
    .badge-danger {
        background-color: #e74a3b;
    }
    .badge-info {
        background-color: #36b9cc;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .gap-1 {
        gap: 0.25rem;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>



<?php $__env->startSection('title', 'Manajemen Pencairan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen Pencairan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Sumber</th>
                            <th>Investor</th>
                            <th>Rekening</th>
                            <th class="text-right">Jumlah</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Catatan/Deskripsi</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + $withdrawals->firstItem()); ?></td>
                            <td>
                                <?php if($withdrawal->source_table == 'investor_withdrawal'): ?>
                                    <span class="badge badge-primary">Pengajuan Investor</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Pencairan Sistem</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($withdrawal->investor_id): ?>
                                    <?php echo e($withdrawal->investor->name ?? 'N/A'); ?>

                                    <br>
                                    <small class="text-muted"><?php echo e($withdrawal->investor->email ?? ''); ?></small>
                                <?php elseif($withdrawal->account && $withdrawal->account->investor): ?>
                                    <?php echo e($withdrawal->account->investor->name); ?>

                                    <br>
                                    <small class="text-muted"><?php echo e($withdrawal->account->investor->email); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Data investor tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($withdrawal->account): ?>
                                    <?php echo e($withdrawal->account->bank_name); ?>

                                    <br>
                                    <small class="text-muted"><?php echo e($withdrawal->account->account_number); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Data rekening tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">Rp <?php echo e(number_format($withdrawal->amount, 0, ',', '.')); ?></td>
                            <td>
                                <?php if($withdrawal->requested_at): ?>
                                    <?php echo e(\Carbon\Carbon::parse($withdrawal->requested_at)->format('d/m/Y H:i')); ?>

                                <?php elseif($withdrawal->date): ?>
                                    <?php echo e(\Carbon\Carbon::parse($withdrawal->date)->format('d/m/Y H:i')); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($withdrawal->notes ?? $withdrawal->description ?? '-'); ?>

                            </td>
                            <td>
                                <?php if($withdrawal->status == 'pending'): ?>
                                    <?php if($withdrawal->source_table == 'investor_withdrawal'): ?>
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="<?php echo e(route('irp.withdrawal-management.approve', $withdrawal->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('irp.withdrawal-management.reject', $withdrawal->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="<?php echo e(route('irp.withdrawal-management.approve-investment', $withdrawal->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Proses Pencairan">
                                                <i class="fas fa-check"></i> Berhasil
                                            </button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($withdrawal->status == 'pending' && $withdrawal->source_table == 'investor_withdrawal'): ?>
                                <div class="d-flex gap-1">
                                    <form method="POST" action="<?php echo e(route('irp.withdrawal-management.approve', $withdrawal->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('irp.withdrawal-management.reject', $withdrawal->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <?php if($withdrawals->hasPages()): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan <?php echo e($withdrawals->firstItem()); ?> sampai <?php echo e($withdrawals->lastItem()); ?> dari <?php echo e($withdrawals->total()); ?> entri
                    </div>
                    <nav>
                        <?php echo e($withdrawals->onEachSide(1)->links('pagination::bootstrap-4')); ?>

                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\withdrawal_management\index.blade.php ENDPATH**/ ?>