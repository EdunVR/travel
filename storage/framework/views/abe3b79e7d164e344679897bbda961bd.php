<style>
    .card-bagian {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    
    .form-control {
        border-radius: 4px;
        padding: 10px 15px;
    }
    
    .btn {
        border-radius: 4px;
        padding: 8px 20px;
        font-weight: 500;
    }
    
    .map-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 5px;
    }
    
    .badge {
        padding: 5px 10px;
        font-weight: 500;
        border-radius: 4px;
    }
    
    .badge-prospek { background-color: #6c757d; color: white; }
    .badge-followup { background-color: #17a2b8; color: white; }
    .badge-negosiasi { background-color: #ffc107; color: black; }
    .badge-closing { background-color: #28a745; color: white; }
    .badge-deposit { background-color: #007bff; color: white; }
    .badge-gagal { background-color: #dc3545; color: white; }
</style>



<?php $__env->startSection('title', 'Edit Prospek'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Edit Prospek: <?php echo e($prospek->nama); ?></h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('prospek.update', $prospek->id_prospek)); ?>" method="POST" id="prospekForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Informasi Dasar -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tanggal" name="tanggal" 
                                                value="<?php echo e(old('tanggal', \Carbon\Carbon::parse($prospek->tanggal)->format('Y-m-d\TH:i'))); ?>" required>
                                            <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nama" name="nama" 
                                                   value="<?php echo e(old('nama', $prospek->nama)); ?>" required>
                                            <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_perusahaan" class="font-weight-bold">Nama Perusahaan</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nama_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nama_perusahaan" name="nama_perusahaan" 
                                                   value="<?php echo e(old('nama_perusahaan', $prospek->nama_perusahaan)); ?>">
                                            <?php $__errorArgs = ['nama_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telepon" class="font-weight-bold">Telepon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="telepon" name="telepon" 
                                                   value="<?php echo e(old('telepon', $prospek->telepon)); ?>" required>
                                            <?php $__errorArgs = ['telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="font-weight-bold">Email</label>
                                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" 
                                                   value="<?php echo e(old('email', $prospek->email)); ?>">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis" class="font-weight-bold">Jenis Usaha</label>
                                            <select class="form-control <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="jenis" name="jenis">
                                                <option value="">- Pilih Jenis Usaha -</option>
                                                <option value="Perusahaan" <?php echo e(old('jenis', $prospek->jenis) == 'Perusahaan' ? 'selected' : ''); ?>>Perusahaan</option>
                                                <option value="Perorangan" <?php echo e(old('jenis', $prospek->jenis) == 'Perorangan' ? 'selected' : ''); ?>>Perorangan</option>
                                                <option value="UKM" <?php echo e(old('jenis', $prospek->jenis) == 'UKM' ? 'selected' : ''); ?>>UKM</option>
                                                <option value="Koperasi" <?php echo e(old('jenis', $prospek->jenis) == 'Koperasi' ? 'selected' : ''); ?>>Koperasi</option>
                                            </select>
                                            <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis" class="font-weight-bold">Jenis Usaha</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="jenis" name="jenis" 
                                                   value="<?php echo e(old('jenis', $prospek->jenis)); ?>">
                                            <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pemilik_manager" class="font-weight-bold">Pemilik/Manager</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['pemilik_manager'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="pemilik_manager" name="pemilik_manager" 
                                                   value="<?php echo e(old('pemilik_manager', $prospek->pemilik_manager)); ?>">
                                            <?php $__errorArgs = ['pemilik_manager'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Perusahaan -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Perusahaan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kapasitas_produksi" class="font-weight-bold">Kapasitas Produksi</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['kapasitas_produksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="kapasitas_produksi" name="kapasitas_produksi" 
                                                   value="<?php echo e(old('kapasitas_produksi', $prospek->kapasitas_produksi)); ?>">
                                            <?php $__errorArgs = ['kapasitas_produksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sistem_produksi" class="font-weight-bold">Sistem Produksi</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['sistem_produksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="sistem_produksi" name="sistem_produksi" 
                                                   value="<?php echo e(old('sistem_produksi', $prospek->sistem_produksi)); ?>">
                                            <?php $__errorArgs = ['sistem_produksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bahan_bakar" class="font-weight-bold">Bahan Bakar</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['bahan_bakar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="bahan_bakar" name="bahan_bakar" 
                                                   value="<?php echo e(old('bahan_bakar', $prospek->bahan_bakar)); ?>">
                                            <?php $__errorArgs = ['bahan_bakar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="menggunakan_boiler" class="font-weight-bold">Menggunakan Boiler?</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['menggunakan_boiler'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="menggunakan_boiler" name="menggunakan_boiler" 
                                                   value="<?php echo e(old('menggunakan_boiler', $prospek->menggunakan_boiler)); ?>">
                                            <?php $__errorArgs = ['menggunakan_boiler'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="informasi_perusahaan" class="font-weight-bold">Informasi Perusahaan</label>
                                    <textarea class="form-control <?php $__errorArgs = ['informasi_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="informasi_perusahaan" name="informasi_perusahaan" rows="3"><?php echo e(old('informasi_perusahaan', $prospek->informasi_perusahaan)); ?></textarea>
                                    <?php $__errorArgs = ['informasi_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Status dan Lokasi -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Status dan Lokasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_status" class="font-weight-bold">Status Saat Ini <span class="text-danger">*</span></label>
                                            <select class="form-control <?php $__errorArgs = ['current_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="current_status" name="current_status" required>
                                                <?php $__currentLoopData = App\Models\Prospek::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>" <?php echo e(old('current_status', $prospek->current_status) == $key ? 'selected' : ''); ?>>
                                                        <?php echo e($value); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['current_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="recruitment_id" class="font-weight-bold">Petugas <span class="text-danger">*</span></label>
                                            <select class="form-control <?php $__errorArgs = ['recruitment_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="recruitment_id" name="recruitment_id" required>
                                                <option value="">- Pilih Recruitment -</option>
                                                <?php $__currentLoopData = $recruitments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recruitment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($recruitment->id); ?>" <?php echo e(old('recruitment_id', $prospek->recruitment_id) == $recruitment->id ? 'selected' : ''); ?>>
                                                        <?php echo e($recruitment->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['recruitment_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_outlet" class="font-weight-bold">Outlet <span class="text-danger">*</span></label>
                                            <select class="form-control <?php $__errorArgs = ['id_outlet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="id_outlet" name="id_outlet" required>
                                                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e(old('id_outlet', $prospek->id_outlet) == $outlet->id_outlet ? 'selected' : ''); ?>>
                                                        <?php echo e($outlet->nama_outlet); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['id_outlet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Alamat -->
                                <div class="card-bagian mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="m-0 font-weight-bold text-primary">Informasi Alamat</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="alamat" class="font-weight-bold">Alamat Lengkap</label>
                                            <textarea class="form-control <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="alamat" name="alamat" rows="2"><?php echo e(old('alamat', $prospek->alamat)); ?></textarea>
                                            <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="provinsi_id" class="font-weight-bold">Provinsi <span class="text-danger"></span></label>
                                                    <select class="form-control <?php $__errorArgs = ['provinsi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="provinsi_id" name="provinsi_id">
                                                        <option value="">Pilih Provinsi</option>
                                                        <?php $__currentLoopData = $provinsis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provinsi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($provinsi->id); ?>" <?php echo e(old('provinsi_id', $prospek->provinsi_id) == $provinsi->id ? 'selected' : ''); ?>>
                                                                <?php echo e($provinsi->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <?php $__errorArgs = ['provinsi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kabupaten_id" class="font-weight-bold">Kabupaten/Kota <span class="text-danger"></span></label>
                                                    <select class="form-control <?php $__errorArgs = ['kabupaten_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="kabupaten_id" name="kabupaten_id" <?php echo e(!$prospek->provinsi_id ? 'disabled' : ''); ?>>
                                                        <option value="">Pilih Kabupaten/Kota</option>
                                                        <?php if(isset($kabupatens) && $kabupatens->count() > 0): ?>
                                                            <?php $__currentLoopData = $kabupatens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kabupaten): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($kabupaten->id); ?>" <?php echo e(old('kabupaten_id', $prospek->kabupaten_id) == $kabupaten->id ? 'selected' : ''); ?>>
                                                                    <?php echo e($kabupaten->name); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php $__errorArgs = ['kabupaten_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="latitude" class="font-weight-bold">Latitude</label>
                                                    <input type="text" class="form-control" id="latitude" name="latitude" readonly value="<?php echo e(old('latitude', $prospek->latitude)); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kecamatan_id" class="font-weight-bold">Kecamatan <span class="text-danger"></span></label>
                                                    <select class="form-control <?php $__errorArgs = ['kecamatan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="kecamatan_id" name="kecamatan_id" <?php echo e(!$prospek->kabupaten_id ? 'disabled' : ''); ?>>
                                                        <option value="">Pilih Kecamatan</option>
                                                        <?php if(isset($kecamatans) && $kecamatans->count() > 0): ?>
                                                            <?php $__currentLoopData = $kecamatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kecamatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($kecamatan->id); ?>" <?php echo e(old('kecamatan_id', $prospek->kecamatan_id) == $kecamatan->id ? 'selected' : ''); ?>>
                                                                    <?php echo e($kecamatan->name); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php $__errorArgs = ['kecamatan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="desa_id" class="font-weight-bold">Desa/Kelurahan <span class="text-danger"></span></label>
                                                    <select class="form-control <?php $__errorArgs = ['desa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="desa_id" name="desa_id" <?php echo e(!$prospek->kecamatan_id ? 'disabled' : ''); ?>>
                                                        <option value="">Pilih Desa/Kelurahan</option>
                                                        <?php if(isset($desas) && $desas->count() > 0): ?>
                                                            <?php $__currentLoopData = $desas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $desa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($desa->id); ?>" <?php echo e(old('desa_id', $prospek->desa_id) == $desa->id ? 'selected' : ''); ?>>
                                                                    <?php echo e($desa->name); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php $__errorArgs = ['desa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="longitude" class="font-weight-bold">Longitude</label>
                                                    <input type="text" class="form-control" id="longitude" name="longitude" readonly value="<?php echo e(old('longitude', $prospek->longitude)); ?>">
                                                </div>
                                                <button type="button" class="btn btn-primary btn-block" id="btnUpdateLocation">
                                                    <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini
                                                </button>
                                                <small class="text-muted">Izinkan akses lokasi saat diminta browser</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map and Coordinates -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Lokasi Peta</label>
                                            <div id="locationMap" style="height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ddd;"></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?php echo e(route('prospek.index')); ?>" class="btn btn-secondary px-4">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="col-md-4">
            <div class="card-bagian shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Status</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('prospek.timeline.store', $prospek->id_prospek)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <?php $__currentLoopData = App\Models\Prospek::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Timeline</button>
                    </form>

                    <hr>

                    <h6>Riwayat Timeline</h6>
                    <div class="timeline-container" style="max-height: 400px; overflow-y: auto;">
                        <?php $__currentLoopData = $prospek->timeline()->orderBy('tanggal', 'desc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timeline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        <?php
                                            $badgeClass = [
                                                'prospek' => 'secondary',
                                                'followup' => 'info',
                                                'negosiasi' => 'warning',
                                                'closing' => 'success',
                                                'deposit' => 'primary',
                                                'gagal' => 'danger'
                                            ][$timeline->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?php echo e($badgeClass); ?>">
                                            <?php echo e($timeline->status); ?>

                                        </span>
                                    </h6>
                                    <small>
                                        <?php if($timeline->tanggal instanceof \Illuminate\Support\Carbon): ?>
                                            <?php echo e($timeline->tanggal->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <?php echo e(\Carbon\Carbon::parse($timeline->tanggal)->format('d/m/Y H:i')); ?>

                                        <?php endif; ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?php echo e($timeline->deskripsi); ?></p>
                                <form action="<?php echo e(route('prospek.timeline.destroy', $timeline->id)); ?>" method="POST" class="text-right">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus timeline ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- foto -->
        <div class="col-md-4">
            <!-- Photo Upload Card -->
            <div class="card-bagian shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Prospek</h6>
                </div>
                <div class="card-body">
                    <?php if($prospek->photo): ?>
                        <div class="text-center mb-3">
                            <img src="<?php echo e(asset($prospek->photo)); ?>" alt="Foto Prospek" 
                                class="img-fluid rounded" style="max-height: 200px;">
                            <p class="text-muted small mt-2">Foto saat ini</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada foto yang diupload
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('prospek.uploadPhoto', $prospek->id_prospek)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="photo">Upload Foto Baru</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="photo" name="photo" accept="image/*">
                                <label class="custom-file-label" for="photo">Pilih file...</label>
                                <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <small class="form-text text-muted">
                                Format: JPG, PNG (Maksimal 2MB)
                            </small>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-upload"></i> Upload Foto
                            </button>
                        </div>
                    </form>

                    <?php if($prospek->photo): ?>
                        <form action="<?php echo e(route('prospek.deletePhoto', $prospek->id_prospek)); ?>" method="POST" class="mt-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-block" 
                                    onclick="return confirm('Hapus foto ini?')">
                                <i class="fas fa-trash"></i> Hapus Foto
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&libraries=places&callback=initMap" async defer></script>
<script>
    // Buat variabel global untuk route
    window.Laravel = {
        routes: {
            kabupaten: "<?php echo e(route('api.wilayah.kabupaten', ['provinsi_id' => ':provinsi_id'])); ?>",
            kecamatan: "<?php echo e(route('api.wilayah.kecamatan', ['kabupaten_id' => ':kabupaten_id'])); ?>",
            desa: "<?php echo e(route('api.wilayah.desa', ['kecamatan_id' => ':kecamatan_id'])); ?>"
        }
    };
</script>
<script>
    feather.replace();
    function prepareRoute(route, params) {
        let url = route;
        for (const key in params) {
            url = url.replace(`:${key}`, params[key]);
        }
        return url;
    }

    // Variabel global untuk map dan marker
    var map, marker;

    function initMap() {
        // Initialize map
        map = new google.maps.Map(document.getElementById('locationMap'), {
            center: {lat: <?php echo e($prospek->latitude ?? -6.2088); ?>, lng: <?php echo e($prospek->longitude ?? 106.8456); ?>},
            zoom: 12
        });

        // Create marker
        marker = new google.maps.Marker({
            position: {lat: <?php echo e($prospek->latitude ?? -6.2088); ?>, lng: <?php echo e($prospek->longitude ?? 106.8456); ?>},
            map: map,
            draggable: true
        });

        // Event listener untuk marker yang digeser
        marker.addListener('dragend', function() {
            var position = marker.getPosition();
            updateCoordinateFields(position.lat(), position.lng());
        });

        // Search box for location
        var input = document.createElement('input');
        input.id = 'pac-input';
        input.className = 'controls';
        input.placeholder = 'Cari lokasi...';
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

        var searchBox = new google.maps.places.SearchBox(input);

        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();
            if (places.length == 0) {
                return;
            }

            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);

            // Set marker position
            marker.setPosition(places[0].geometry.location);
            updateCoordinateFields(places[0].geometry.location.lat(), places[0].geometry.location.lng());
        });

        // Button untuk mendapatkan lokasi saat ini
        $('#btnUpdateLocation').click(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        updateCoordinateFields(pos.lat, pos.lng);
                    },
                    function(error) {
                        alert('Error getting location: ' + error.message);
                    }
                );
            } else {
                alert("Browser tidak mendukung geolocation");
            }
        });
    }

    // Fungsi update form koordinat
    function updateCoordinateFields(lat, lng) {
        $('#latitude').val(lat);
        $('#longitude').val(lng);
    }

    // Fungsi untuk geocoding berdasarkan alamat
    function geocodeAddress(address, callback) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({address: address}, function(results, status) {
            if (status === 'OK' && results[0]) {
                var location = results[0].geometry.location;
                if (typeof callback === 'function') {
                    callback(location);
                }
            } else {
                console.error('Geocode was not successful for the following reason:', status);
            }
        });
    }

    function updateMapBySelectedRegion() {
        // Tampilkan loading indicator
        $('#locationMap').append('<div class="map-loading-overlay"><i class="fas fa-spinner fa-spin"></i> Memperbarui peta...</div>');
        
        try {
            // Dapatkan elemen yang terpilih dengan pengecekan
            const getSelectedText = (selector) => {
                const selectedOption = $(selector).find('option:selected');
                return selectedOption.length ? selectedOption.text().trim() : '';
            };

            const provinsi = getSelectedText('#provinsi_id');
            const kabupaten = getSelectedText('#kabupaten_id');
            const kecamatan = getSelectedText('#kecamatan_id');
            const desa = getSelectedText('#desa_id');
            
            // Bangun alamat berdasarkan level yang dipilih
            const addressParts = [];
            
            // Prioritas: gunakan level terendah yang dipilih
            if (desa && desa !== 'Pilih Desa/Kelurahan') {
                addressParts.push(desa);
            }
            if (kecamatan && kecamatan !== 'Pilih Kecamatan') {
                addressParts.push(kecamatan);
            }
            if (kabupaten && kabupaten !== 'Pilih Kabupaten/Kota') {
                addressParts.push(kabupaten);
            }
            if (provinsi && provinsi !== 'Pilih Provinsi') {
                addressParts.push(provinsi);
            }
            
            if (addressParts.length === 0) {
                $('.map-loading-overlay').remove();
                return;
            }
            
            const fullAddress = addressParts.join(', ');
            
            geocodeAddress(fullAddress, function(location) {
                if (!location) {
                    console.error('Location not found for address:', fullAddress);
                    $('.map-loading-overlay').remove();
                    return;
                }
                
                map.setCenter(location);
                marker.setPosition(location);
                updateCoordinateFields(location.lat(), location.lng());
                
                // Animasi zoom berdasarkan level detail
                let zoomLevel = 12; // Default untuk kecamatan/desa
                if (addressParts.length <= 2) zoomLevel = 10; // Untuk kabupaten
                if (addressParts.length === 1) zoomLevel = 8; // Untuk provinsi
                map.setZoom(zoomLevel);
                
                // Sembunyikan loading indicator
                $('.map-loading-overlay').remove();
            });
        } catch (error) {
            console.error('Error updating map:', error);
            $('.map-loading-overlay').remove();
        }
    }

    $(document).ready(function() {
        // Inisialisasi dropdown wilayah
        initWilayahDropdown();

        const API_BASE_URL = window.location.origin + '/api/v1';

        // Event ketika provinsi dipilih
        $('#provinsi_id').change(function() {
            var provinsiId = $(this).val();
            $('#kabupaten_id').html('<option value="">Pilih Kabupaten/Kota</option>');
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (provinsiId) {
                $('#kabupaten_id').prop('disabled', false);
                loadKabupaten(provinsiId);
            } else {
                $('#kabupaten_id').prop('disabled', true);
                $('#kecamatan_id').prop('disabled', true);
                $('#desa_id').prop('disabled', true);
            }
        });

        // Event ketika kabupaten dipilih
        $('#kabupaten_id').change(function() {
            var kabupatenId = $(this).val();
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (kabupatenId) {
                $('#kecamatan_id').prop('disabled', false);
                loadKecamatan(kabupatenId);
            } else {
                $('#kecamatan_id').prop('disabled', true);
                $('#desa_id').prop('disabled', true);
            }
        });

        // Event ketika kecamatan dipilih
        $('#kecamatan_id').change(function() {
            var kecamatanId = $(this).val();
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (kecamatanId) {
                $('#desa_id').prop('disabled', false);
                loadDesa(kecamatanId);
            } else {
                $('#desa_id').prop('disabled', true);
            }
        });

        // Fungsi inisialisasi dropdown wilayah
        function initWilayahDropdown() {
            <?php if(isset($prospek)): ?>
                var provinsiId = "<?php echo e($prospek->provinsi_id); ?>";
                var kabupatenId = "<?php echo e($prospek->kabupaten_id); ?>";
                var kecamatanId = "<?php echo e($prospek->kecamatan_id); ?>";
                var desaId = "<?php echo e($prospek->desa_id); ?>";
                
                if (provinsiId) {
                    $('#kabupaten_id').prop('disabled', false);
                    loadKabupaten(provinsiId, kabupatenId, function() {
                        if (kabupatenId) {
                            $('#kecamatan_id').prop('disabled', false);
                            loadKecamatan(kabupatenId, kecamatanId, function() {
                                if (kecamatanId) {
                                    $('#desa_id').prop('disabled', false);
                                    loadDesa(kecamatanId, desaId);
                                }
                            });
                        }
                    });
                }
            <?php endif; ?>
        }

        function loadKabupaten(provinsiId, selectedId = null, callback = null) {
            const url = prepareRoute(Laravel.routes.kabupaten, { provinsi_id: provinsiId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Kabupaten/Kota</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#kabupaten_id').html(options);
                
                if (selectedId) {
                    $('#kabupaten_id').val(selectedId).trigger('change');
                }
                
                if (callback) callback();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading kabupaten:", textStatus, errorThrown);
                $('#kabupaten_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Fungsi load kecamatan dengan callback
        function loadKecamatan(kabupatenId, selectedId = null, callback = null) {
            const url = prepareRoute(Laravel.routes.kecamatan, { kabupaten_id: kabupatenId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Kecamatan</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#kecamatan_id').html(options);
                
                if (selectedId) {
                    $('#kecamatan_id').val(selectedId).trigger('change');
                }
                
                if (callback) callback();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading kecamatan:", textStatus, errorThrown);
                $('#kecamatan_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Fungsi load desa
        function loadDesa(kecamatanId, selectedId = null) {
            const url = prepareRoute(Laravel.routes.desa, { kecamatan_id: kecamatanId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Desa/Kelurahan</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#desa_id').html(options);
                
                if (selectedId) {
                    $('#desa_id').val(selectedId);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading desa:", textStatus, errorThrown);
                $('#desa_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Variabel untuk menyimpan nilai terakhir
        var lastProvinsi = '';
        var lastKabupaten = '';
        var lastKecamatan = '';
        var lastDesa = '';

        $('#provinsi_id, #kabupaten_id, #kecamatan_id, #desa_id').change(function() {
            // Aktifkan dropdown berikutnya jika ada pilihan
            if ($(this).attr('id') === 'provinsi_id' && $(this).val()) {
                $('#kabupaten_id').prop('disabled', false);
            } else if ($(this).attr('id') === 'kabupaten_id' && $(this).val()) {
                $('#kecamatan_id').prop('disabled', false);
            } else if ($(this).attr('id') === 'kecamatan_id' && $(this).val()) {
                $('#desa_id').prop('disabled', false);
            }
            
            // Cek apakah nilai benar-benar berubah
            var currentProvinsi = $('#provinsi_id option:selected').text();
            var currentKabupaten = $('#kabupaten_id option:selected').text();
            var currentKecamatan = $('#kecamatan_id option:selected').text();
            var currentDesa = $('#desa_id option:selected').text();
            
            if (currentProvinsi !== lastProvinsi || 
                currentKabupaten !== lastKabupaten ||
                currentKecamatan !== lastKecamatan ||
                currentDesa !== lastDesa) {
                
                // Update nilai terakhir
                lastProvinsi = currentProvinsi;
                lastKabupaten = currentKabupaten;
                lastKecamatan = currentKecamatan;
                lastDesa = currentDesa;
                
                // Update peta berdasarkan pilihan wilayah
                updateMapBySelectedRegion();
            }
        });

        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = document.getElementById("photo").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\crm\prospek\edit.blade.php ENDPATH**/ ?>