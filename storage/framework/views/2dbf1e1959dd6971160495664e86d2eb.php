<div class="modal fade" id="modal-supplier" tabindex="-1" aria-labelledby="modal-supplier" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h1 class="modal-title">Pilih Supplier</h1>
            </div>
            <div class="modal-body">
                <table class="table table-stiped table-bordered table-supplier">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Hutang</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td width="5%"><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($item->nama); ?></td>
                                <td><?php echo e($item->telepon); ?></td>
                                <td><?php echo e($item->alamat); ?></td>
                                <td><?php echo e(format_uang($item->hutang)); ?></td>
                                <td>
                                    <a href="<?php echo e(route('pembelian.create', [$item->id_supplier, $item->id_outlet ?? auth()->user()->akses_outlet[0]])); ?>"
                                        class="btn btn-primary btn-xs btn-flat">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\pembelian\supplier.blade.php ENDPATH**/ ?>