

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Detail Laporan Penjualan</h2>
    <table class="table">
        <tr>
            <th>Tanggal</th>
            <td><?php echo e(\Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y')); ?></td>
        </tr>
        <tr>
            <th>Nama Produk</th>
            <td><?php echo e($laporan->nama_produk); ?></td>
        </tr>
        <tr>
            <th>HPP</th>
            <td>Rp<?php echo e(number_format($laporan->hpp, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <th>Harga Jual</th>
            <td>Rp<?php echo e(number_format($laporan->harga_jual, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td><?php echo e($laporan->jumlah); ?></td>
        </tr>
        <tr>
            <th>Profit</th>
            <td>Rp<?php echo e(number_format(($laporan->harga_jual - $laporan->hpp) * $laporan->jumlah, 0, ',', '.')); ?></td>
        </tr>
    </table>
    <a href="<?php echo e(route('laporan_penjualan.index')); ?>" class="btn btn-primary">Kembali</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\laporan_penjualan\detail.blade.php ENDPATH**/ ?>