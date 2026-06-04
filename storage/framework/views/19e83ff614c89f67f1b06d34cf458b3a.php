

<?php $__env->startSection('title', 'Kelola Customer Investor'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Customer Investor: <?php echo e($investor->name); ?></h6>
            <a href="<?php echo e(route('irp.investor.index')); ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="<?php echo e(route('irp.investor.customer.store', $investor->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-row">
                            <div class="col-md-6">
                                <select name="id_member" class="form-control" required>
                                    <option value="">Pilih Customer</option>
                                    <?php $__currentLoopData = $availableMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($member->id_member); ?>"><?php echo e($member->nama); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="biaya" class="form-control" placeholder="Biaya" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <div class="alert alert-info">
                        <strong>Status:</strong> 
                        <?php echo e($investor->customers->where('status', 'paid')->count()); ?>/<?php echo e($investor->kuota); ?> kursi terisi
                    </div>
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
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                        <i class="fas fa-check"></i> Verifikasi
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form action="<?php echo e(route('irp.investor.customer.destroy', [$investor->id, $customer->id])); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus customer ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\customers.blade.php ENDPATH**/ ?>