<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Member</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-member">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $member; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                            <tr>
                                <td width="5%"><?php echo e($key+1); ?></td>
                                <td><?php echo e($item->nama); ?></td>
                                <td><?php echo e($item->tipeX->nama_tipe ?? '-'); ?></td>
                                <td><?php echo e($item->telepon); ?></td>
                                <td><?php echo e($item->alamat); ?></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihMember('<?php echo e($item->id_member); ?>', '<?php echo e($item->nama); ?>', '<?php echo e($item->piutang); ?>', '<?php echo e($item->id_tipe); ?>') ">
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\penjualan_detail\member.blade.php ENDPATH**/ ?>