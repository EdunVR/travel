<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
    <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#addCustomerModal">
        <i data-feather="plus" class="icon-sm"></i> Tambah Customer
    </button>
    <div class="alert alert-info mb-2 ms-auto">
        <strong>Status:</strong> 
        <?php echo e($investor->customers->where('status', 'paid')->count()); ?>/<?php echo e($investor->kuota); ?> kursi terisi
        <?php if($investor->customers->count() >= $investor->kuota): ?>
            <span class="badge badge-danger ml-2">Kuota Penuh</span>
        <?php else: ?>
            <span class="badge badge-success ml-2">Tersedia</span>
        <?php endif; ?>
    </div>
</div>


<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Customer</th>
                <th>Telepon</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $investor->customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($customer->member->nama); ?></td>
                <td><?php echo e($customer->member->telepon); ?></td>
                <td class="text-right"><?php echo e(format_uang($customer->biaya)); ?></td>
                <td>
                    <span class="badge badge-<?php echo e($customer->status == 'paid' ? 'success' : 'warning'); ?>">
                        <?php echo e(ucfirst($customer->status)); ?>

                    </span>
                </td>
                <td><?php echo e($customer->payment_date ? tanggal_indonesia($customer->payment_date) : '-'); ?></td>
                <td>
                    <?php if($customer->status != 'paid'): ?>
                    <form action="<?php echo e(route('irp.investor.customer.verify', [$investor->id, $customer->id])); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi pembayaran?')">
                            <i data-feather="check" class="icon-sm"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <form action="<?php echo e(route('irp.investor.customer.destroy', [$investor->id, $customer->id])); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus customer ini?')">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('irp.investor.customer.store', $investor->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="id_member" class="form-control" required>
                            <option value="">Pilih Customer</option>
                            <?php $__currentLoopData = $availableMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($member->id_member); ?>"><?php echo e($member->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Biaya</label>
                        <input type="number" name="biaya" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i data-feather="x" class="icon-sm"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="icon-sm"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Inisialisasi Feather Icons di modal setelah dibuka
    $('#addCustomerModal').on('shown.bs.modal', function () {
        feather.replace();
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\partials\customers.blade.php ENDPATH**/ ?>