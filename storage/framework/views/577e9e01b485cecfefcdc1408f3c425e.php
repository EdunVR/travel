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



<?php $__env->startSection('title', 'Detail Pembelian'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembelian #<?php echo e($pembelian->id_pembelian); ?></h6>
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tanggal</th>
                            <td><?php echo e($pembelian->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td><?php echo e($pembelian->supplier->nama); ?></td>
                        </tr>
                        <tr>
                            <th>Outlet</th>
                            <td><?php echo e($pembelian->outlet->nama_outlet); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Total Item</th>
                            <td><?php echo e($pembelian->total_item); ?></td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp <?php echo e(number_format($pembelian->total_harga, 0)); ?></td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td>
                                <?php if($pembelian->bayar >= $pembelian->total_harga): ?>
                                    <span class="badge badge-success">Lunas</span>
                                <?php elseif($pembelian->bayar > 0): ?>
                                    <span class="badge badge-warning">Sebagian</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th>Harga Beli</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pembelian->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($detail->bahan->nama_bahan); ?></td>
                            <td>Rp <?php echo e(number_format($detail->harga_beli, 0)); ?></td>
                            <td><?php echo e($detail->jumlah); ?></td>
                            <td>Rp <?php echo e(number_format($detail->subtotal, 0)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\pembelian\detail_ledger.blade.php ENDPATH**/ ?>