<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th>Kode Kontra Bon</th>
                <td><?php echo e($kontraBon->kode_kontra_bon); ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?php echo e(tanggal_indonesia($kontraBon->tanggal)); ?></td>
            </tr>
            <tr>
                <th>Customer</th>
                <td><?php echo e($kontraBon->member->nama); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th>Total Pembayaran</th>
                <td><?php echo e(format_uang($kontraBon->total_pembayaran)); ?></td>
            </tr>
            <tr>
                <th>Sisa Hutang</th>
                <td><?php echo e(format_uang($kontraBon->sisa_hutang)); ?></td>
            </tr>
            <tr>
                <th>Outlet</th>
                <td><?php echo e($kontraBon->outlet->nama_outlet); ?></td>
            </tr>
        </table>
    </div>
</div>

<h4>Detail Pembayaran</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>TrxID</th>
            <th>Tanggal</th>
            <th>Total Hutang</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $kontraBon->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($loop->iteration); ?></td>
            <td>TRX00<?php echo e($detail->penjualan->id_penjualan); ?></td>
            <td><?php echo e(tanggal_indonesia($detail->penjualan->created_at)); ?></td>
            <td>Rp <?php echo e(format_uang($kontraBon->details->sum('nominal'))); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\kontra_bon\detail.blade.php ENDPATH**/ ?>