

<?php $__env->startSection('title'); ?>
    Update Status PO Penjualan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li><a href="<?php echo e(route('po-penjualan.index')); ?>">PO Penjualan</a></li>
    <li class="active">Update Status</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Update Status PO Penjualan</h3>
            </div>
            <div class="box-body">
                <!-- Informasi PO -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Informasi PO</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <td width="40%"><strong>No. PO</strong></td>
                                <td><?php echo e($poPenjualan->no_po); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td><?php echo e(tanggal_indonesia($poPenjualan->tanggal, false)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Customer</strong></td>
                                <td><?php echo e($poPenjualan->member->nama ?? 'Customer Umum'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Bayar</strong></td>
                                <td><?php echo e(format_uang($poPenjualan->bayar)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Diterima</strong></td>
                                <td><?php echo e(format_uang($poPenjualan->diterima)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status Saat Ini</strong></td>
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

                <!-- Form Update Status -->
                <form id="statusForm" action="<?php echo e(route('po-penjualan.update-status', $poPenjualan->id_po_penjualan)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('POST'); ?>
                    
                    <div class="form-group">
                        <label for="status">Status Baru *</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="menunggu" <?php echo e($poPenjualan->status == 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                            <option value="lunas" <?php echo e($poPenjualan->status == 'lunas' ? 'selected' : ''); ?>>Lunas</option>
                            <option value="gagal" <?php echo e($poPenjualan->status == 'gagal' ? 'selected' : ''); ?>>Gagal</option>
                        </select>
                    </div>

                    <div class="form-group" id="diterimaField" style="display: none;">
                        <label for="diterima">Jumlah Diterima</label>
                        <input type="number" name="diterima" id="diterima" class="form-control" 
                               value="<?php echo e($poPenjualan->diterima); ?>" min="0" step="0.01">
                        <small class="text-muted">Kosongkan jika tidak ada perubahan</small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" 
                                  placeholder="Alasan perubahan status..."></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Status
                        </button>
                        <a href="<?php echo e(route('po-penjualan.index')); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Items -->
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Items</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $poPenjualan->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($detail->tipe_item == 'ongkir'): ?>
                                        <strong>ONGKIR</strong><br>
                                        <small><?php echo e($detail->keterangan); ?></small>
                                    <?php else: ?>
                                        <strong><?php echo e($detail->produk->kode_produk ?? ''); ?></strong><br>
                                        <small><?php echo e($detail->produk->nama_produk ?? 'Produk'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?php echo e(format_uang($detail->harga_jual)); ?></td>
                                <td class="text-center"><?php echo e($detail->jumlah); ?></td>
                                <td class="text-right"><?php echo e(format_uang($detail->subtotal)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right"><strong><?php echo e(format_uang($poPenjualan->bayar)); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Tampilkan field diterima saat status lunas
    $('#status').change(function() {
        if ($(this).val() === 'lunas') {
            $('#diterimaField').show();
        } else {
            $('#diterimaField').hide();
        }
    });

    // Trigger change saat load
    $('#status').trigger('change');

    // Form submission
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?php echo e(route('po-penjualan.index')); ?>';
                    });
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                
                let message = 'Terjadi kesalahan saat update status';
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire('Error!', message, 'error');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\po_penjualan\edit.blade.php ENDPATH**/ ?>