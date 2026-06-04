

<?php $__env->startSection('title'); ?>
    Daftar Permintaan Pengiriman
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Permintaan Pengiriman</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Outlet Asal</th>
                            <th>Outlet Tujuan</th>
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $permintaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($item->outletAsal->nama_outlet); ?></td>
                                <td><?php echo e($item->outletTujuan->nama_outlet); ?></td>
                                <td>
                                    <?php if($item->id_produk): ?>
                                        Produk: <?php echo e($item->produk->nama_produk); ?>

                                    <?php elseif($item->id_bahan): ?>
                                        Bahan: <?php echo e($item->bahan->nama_bahan); ?>

                                    <?php elseif($item->id_inventori): ?>
                                        Inventori: <?php echo e($item->inventori->nama_barang); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->jumlah); ?></td>
                                <td><?php echo e(ucfirst($item->status)); ?></td>
                                <td>
                                    <?php if($item->status === 'menunggu' && in_array($item->id_outlet_tujuan, Auth::user()->akses_outlet ?? [])): ?>
                                        <button class="btn btn-sm btn-success" onclick="setujuiPermintaan(<?php echo e($item->id_permintaan); ?>)">Setujui</button>
                                        <button class="btn btn-sm btn-danger" onclick="tolakPermintaan(<?php echo e($item->id_permintaan); ?>)">Tolak</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Sertakan CSRF token di header AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fungsi untuk menyetujui permintaan
        function setujuiPermintaan(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui permintaan ini?')) {
                $.ajax({
                    url: '<?php echo e(route('manajemen-gudang.setujui-permintaan', '')); ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        alert(response.message);
                        location.reload(); // Reload halaman setelah menyetujui
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                    }
                });
            }
        }

        function tolakPermintaan(id) {
                if (confirm('Apakah Anda yakin ingin menolak permintaan ini?')) {
                    $.ajax({
                        url: '<?php echo e(route('manajemen-gudang.tolak-permintaan', '')); ?>/' + id,
                        type: 'POST',
                        success: function(response) {
                            alert(response.message);
                            location.reload(); // Reload halaman setelah menolak
                        },
                        error: function(xhr, status, error) {
                            alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                        }
                    });
                }
            }

        // Pindahkan fungsi setujuiPermintaan ke scope global
        window.setujuiPermintaan = setujuiPermintaan;

        // Pindahkan fungsi tolakPermintaan ke scope global
        window.tolakPermintaan = tolakPermintaan;
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\gudang\daftar-permintaan.blade.php ENDPATH**/ ?>