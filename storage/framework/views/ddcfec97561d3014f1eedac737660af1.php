<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th style="width: 15%;">Nama</th>
                <th style="width: 15%;">Posisi</th>
                <th style="width: 15%;">Department</th>
                <th style="width: 20%;">Jobdesk</th>
                <th style="width: 10%;">Fingerprint ID</th>
                <th style="width: 10%;">Status Sidik Jari</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $recruitments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recruitment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($recruitment->name); ?></td>
                <td><?php echo e($recruitment->position); ?></td>
                <td><?php echo e($recruitment->department); ?></td>
                <td>
                    <ul>
                        <?php if($recruitment->jobdesk): ?>
                            <?php $__currentLoopData = json_decode($recruitment->jobdesk); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($job); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <li>Tidak ada jobdesk.</li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td><?php echo e($recruitment->fingerprint_id ?? 'Belum terdaftar'); ?></td>
                <td><?php echo e($recruitment->is_registered_fingerprint ? 'Terdaftar' : 'Belum terdaftar'); ?></td>
                <td>
                    <span class="badge 
                        <?php if($recruitment->status == 'menunggu'): ?> badge-warning
                        <?php elseif($recruitment->status == 'diterima'): ?> badge-success
                        <?php else: ?> badge-danger
                        <?php endif; ?>">
                        <?php echo e(ucfirst($recruitment->status)); ?>

                    </span>
                </td>
                <td>
                    <a href="<?php echo e(route('hrm.recruitment.edit', $recruitment->id)); ?>" class="btn btn-icon btn-warning" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="<?php echo e(route('hrm.recruitment.destroy', $recruitment->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-icon btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    <?php if($recruitment->status == 'diterima'): ?>
                        <button class="btn btn-icon btn-info mt-1" title="Cetak Kontrak" onclick="openPrintContractModal(<?php echo e($recruitment->id); ?>)">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\recruitment\partials\table.blade.php ENDPATH**/ ?>