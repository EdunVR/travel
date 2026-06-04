<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Daftar Pembagian Keuntungan Berdasarkan Kategori</h5>
    <a href="<?php echo e(route('irp.profit-management.create')); ?>" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> Tambah Pembagian
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="profitTable">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Total Keuntungan</th>
                <th>Tanggal Pembagian</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Bukti Transfer</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $profits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($profit->period); ?></td>
                <td class="text-right"><?php echo e(format_uang($profit->total_profit)); ?></td>
                <td><?php echo e($profit->distribution_date->format('d/m/Y')); ?></td>
                <td>
                    <?php echo e($profit->category ? ucfirst($profit->category) : 'Semua Kategori'); ?>

                </td>
                <td>
                    <?php if($profit->status == 'paid'): ?>
                        <span class="badge badge-success">Sudah Dibayar</span>
                    <?php elseif($profit->status == 'processed'): ?>
                        <span class="badge badge-warning">Diproses</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Draft</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($profit->proof_file): ?>
                        <a href="<?php echo e(asset('storage/'.$profit->proof_file)); ?>" target="_blank">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(route('irp.profit-management.show', $profit->id)); ?>" 
                    class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\profit_management\partials\category_tab.blade.php ENDPATH**/ ?>