<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            <?php echo csrf_field(); ?>
            <?php echo method_field('post'); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="name" class="col-lg-3 col-lg-offset-1 control-label">Nama</label>
                        <div class="col-lg-6">
                            <input type="text" name="name" id="name" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_agen" class="col-lg-3 col-lg-offset-1 control-label">Akses sebagai Agen</label>
                        <div class="col-lg-6">
                            <select name="id_agen" id="id_agen" class="form-control">
                                <option value="">-- Pilih Agen --</option>
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($agent->id_member); ?>"><?php echo e($agent->kode_member); ?> - <?php echo e($agent->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="help-block with-errors"></span>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_agen" id="is_agen" value="1"> User ini adalah agen
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses_outlet" class="col-lg-3 col-lg-offset-1 control-label">Akses Outlet</label>
                        <div class="col-lg-6">
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="akses_outlet[]" value="<?php echo e($outlet->id_outlet); ?>" class="akses-outlet-checkbox"> <?php echo e($outlet->nama_outlet); ?>

                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses_khusus" class="col-lg-3 col-lg-offset-1 control-label">Akses Khusus</label>
                        <div class="col-lg-6">
                            <?php $__currentLoopData = $aksesKhususOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="akses_khusus[]" value="<?php echo e($kh); ?>" class="akses-khusus-checkbox"> <?php echo e(ucfirst($kh)); ?>

                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="akses" class="col-lg-3 col-lg-offset-1 control-label">Hak Akses</label>
                        <div class="col-lg-6">
                            <div class="row equal-height">
                                <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4 d-flex">
                                    <div class="panel panel-default flex-fill">
                                        <div class="panel-heading">
                                            <label>
                                                <input type="checkbox" name="akses[]" value="<?php echo e($module); ?>" class="akses-checkbox" data-module="<?php echo e($module); ?>"> 
                                                <strong><?php echo e($module); ?></strong>
                                            </label>
                                        </div>
                                        <div class="panel-body">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="<?php echo e($module); ?> View" class="akses-checkbox <?php echo e($module); ?>-checkbox"> View
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="<?php echo e($module); ?> Create" class="akses-checkbox <?php echo e($module); ?>-checkbox"> Create
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="<?php echo e($module); ?> Edit" class="akses-checkbox <?php echo e($module); ?>-checkbox"> Edit
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="akses[]" value="<?php echo e($module); ?> Delete" class="akses-checkbox <?php echo e($module); ?>-checkbox"> Delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="email" class="col-lg-3 col-lg-offset-1 control-label">Email</label>
                        <div class="col-lg-6">
                            <input type="email" name="email" id="email" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-lg-3 col-lg-offset-1 control-label">Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password" id="password" class="form-control" 
                            required
                            minlength="6">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-lg-3 col-lg-offset-1 control-label">Konfirmasi Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                required
                                data-match="#password">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\user\form.blade.php ENDPATH**/ ?>