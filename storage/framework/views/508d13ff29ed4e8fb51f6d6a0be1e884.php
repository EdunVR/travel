<div class="modal fade" id="modal-bahan" tabindex="-1" aria-labelledby="modal-bahan" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h1 class="modal-title">Pilih Bahan</h1>
            </div>
            <div class="modal-body">
                <table class="table table-stiped table-bordered table-bahan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Stok</th>
                        <th>Jumlah</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $bahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td width="5%"><?php echo e($key+1); ?></td>
                            <td><span class="label label-success"><?php echo e($item->nama_bahan); ?></span></td>
                            <td><?php echo e($item->merk); ?></td>
                            <td><?php echo e($item->stok ?? 0); ?></td>
                            <td>
                                    <input type="number" class="form-control input-sm jumlah" 
                                           data-id="<?php echo e($item->id_bahan); ?>" 
                                           placeholder="0" 
                                           min="0" 
                                           >
                                </td>
                            <td>
                                <a href="#" class="btn btn-xs btn-info"
                                    onclick="pilihHarga('<?php echo e(route('getHargaBeli', $item->id_bahan)); ?>', '<?php echo e($item->id_bahan); ?>')">
                                    <i class="fa fa-check-circle"></i> Pilih
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\pembelian_detail\bahan.blade.php ENDPATH**/ ?>