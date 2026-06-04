<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Dokumen</th>
            </tr>
        </thead>
        <tbody>
            <?php if($investor->investments->count() > 0): ?>
                <?php $__currentLoopData = $investor->investments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $investment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(tanggal_indonesia($investment->date)); ?></td>
                    <td><?php echo e(ucfirst($investment->type)); ?></td>
                    <td class="text-right"><?php echo e(format_uang($investment->amount)); ?></td>
                    <td><?php echo e($investment->notes ?? '-'); ?></td>
                    <td>
                        <?php if($investment->document): ?>
                            <a href="<?php echo e(asset('storage/'.$investment->document)); ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-download"></i>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada riwayat investasi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\partials\investment_history.blade.php ENDPATH**/ ?>