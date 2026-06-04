<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th width="40%">No. PO</th>
                <td><?php echo e($poPenjualan->no_po); ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?php echo e(tanggal_indonesia($poPenjualan->tanggal, false)); ?></td>
            </tr>
            <tr>
                <th>Customer</th>
                <td><?php echo e($poPenjualan->member->nama ?? 'Customer Umum'); ?></td>
            </tr>
            <tr>
                <th>Outlet</th>
                <td><?php echo e($poPenjualan->outlet->nama_outlet ?? '-'); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th width="40%">Total Item</th>
                <td><?php echo e($poPenjualan->total_item); ?></td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td><?php echo e(format_uang($poPenjualan->total_harga)); ?></td>
            </tr>
            <tr>
                <th>Diskon</th>
                <td><?php echo e($poPenjualan->diskon); ?>%</td>
            </tr>
            <tr>
                <th>Ongkir</th>
                <td><?php echo e(format_uang($poPenjualan->ongkir)); ?></td>
            </tr>
            <tr>
                <th>Total Bayar</th>
                <td><?php echo e(format_uang($poPenjualan->bayar)); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if($poPenjualan->status == 'menunggu'): ?>
                        <span class="label label-warning">Menunggu</span>
                    <?php elseif($poPenjualan->status == 'lunas'): ?>
                        <span class="label label-success">Lunas</span>
                    <?php else: ?>
                        <span class="label label-danger">Gagal</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>Detail Items</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk/Ongkir</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $poPenjualan->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td>
                            <?php if($detail->tipe_item == 'ongkir'): ?>
                                <?php echo e($detail->keterangan ?? 'Ongkos Kirim'); ?>

                            <?php else: ?>
                                <?php echo e($detail->produk->nama_produk ?? '-'); ?>

                            <?php endif; ?>
                        </td>
                        <td><?php echo e(format_uang($detail->harga_jual)); ?></td>
                        <td><?php echo e($detail->jumlah); ?></td>
                        <td><?php echo e($detail->diskon); ?>%</td>
                        <td><?php echo e(format_uang($detail->subtotal)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\po_penjualan\show.blade.php ENDPATH**/ ?>