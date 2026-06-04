<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-produk">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Jumlah</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $produk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td width="5%"><?php echo e($key+1); ?></td>
                                <td><span class="label label-success"><?php echo e($item->kode_produk); ?></span></td>
                                <td><?php echo e($item->nama_produk); ?></td>
                                <td><?php echo e(format_uang($item->hargaJual_FIX)); ?></td>
                                <td><?php echo e($item->hpp_produk_sum_stok ?? 0); ?></td>
                                <td>
                                    <input type="number" class="form-control input-sm jumlah" 
                                           data-id="<?php echo e($item->id_produk); ?>" 
                                           placeholder="0"
                                           min="0" 
                                           max="<?php echo e($item->hpp_produk_sum_stok ?? 1); ?>">
                                </td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihProduk('<?php echo e($item->id_produk); ?>', '<?php echo e($item->kode_produk); ?>', '<?php echo e($item->hpp_produk_sum_stok); ?>')">
                                         <!-- onclick="pilihHarga('<?php echo e(route('getHPP', $item->id_produk)); ?>', '<?php echo e($item->id_produk); ?>')"> -->
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\penjualan_detail\produk.blade.php ENDPATH**/ ?>