<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Produk:</strong> <?php echo e($produk->kode_produk); ?> - <?php echo e($produk->nama_produk); ?><br>
            <strong>Agen:</strong> <?php echo e($agen->nama); ?><br>
            <strong>Periode:</strong> <?php echo e($start_date); ?> s/d <?php echo e($end_date); ?>

        </div>
        
        <?php if($penjualanPerGerobak->count() > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="bg-primary">
                    <th>Tanggal</th>
                    <th>Kode Gerobak</th>
                    <th>Nama Gerobak</th>
                    <th class="text-right">Jumlah Terjual</th>
                    <th class="text-right">Omset</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $penjualanPerGerobak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penjualan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(date('d/m/Y', strtotime($penjualan->tanggal))); ?></td>
                    <td>
                        <?php if($penjualan->kode_gerobak): ?>
                            <?php echo e($penjualan->kode_gerobak); ?>

                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($penjualan->nama_gerobak): ?>
                            <?php echo e($penjualan->nama_gerobak); ?>

                        <?php else: ?>
                            <span class="text-muted">Tidak Ada Gerobak</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><?php echo e($penjualan->jumlah_terjual); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($penjualan->total_omset, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr class="bg-success">
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right"><?php echo e($penjualanPerGerobak->sum('jumlah_terjual')); ?></th>
                    <th class="text-right">Rp <?php echo e(number_format($penjualanPerGerobak->sum('total_omset'), 0, ',', '.')); ?></th>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <div class="alert alert-warning">
            Tidak ada penjualan untuk produk "<?php echo e($produk->kode_produk); ?> - <?php echo e($produk->nama_produk); ?>" 
            pada periode <?php echo e($start_date); ?> s/d <?php echo e($end_date); ?>.
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\agen_gerobak\partials\penjualan_gerobak.blade.php ENDPATH**/ ?>