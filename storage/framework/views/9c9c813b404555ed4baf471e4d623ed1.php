

<?php $__env->startSection('title', 'Profil Investor'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Profil Investor</h4>
                <a href="<?php echo e(route('investor.dashboard')); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo e($investor->photo ? asset('storage/'.$investor->photo) : asset('images/default-user.png')); ?>" 
                         class="rounded-circle img-thumbnail mb-3" 
                         alt="Foto Profil" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h5 class="mb-1"><?php echo e($investor->name); ?></h5>
                    <p class="text-muted mb-1"><?php echo e($investor->email); ?></p>
                    <p class="text-muted"><?php echo e($investor->phone); ?></p>
                    
                    <div class="d-flex justify-content-center mb-3">
                        <span class="badge bg-<?php echo e($investor->status === 'active' ? 'success' : 'secondary'); ?>">
                            <?php echo e(ucfirst($investor->status)); ?>

                        </span>
                        <span class="badge bg-info ms-2">
                            <?php echo e(ucfirst($investor->category)); ?>

                        </span>
                    </div>
                    
                    <p class="text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Bergabung sejak <?php echo e($investor->join_date->format('d F Y')); ?>

                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('investor.profile.update')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo e(old('name', $investor->name)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo e(old('email', $investor->email)); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo e(old('phone', $investor->phone)); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo e(old('address', $investor->address)); ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Investasi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Tanggal Bergabung</th>
                                    <td><?php echo e($investor->join_date->format('d F Y')); ?></td>
                                </tr>
                                <tr>
                                    <th>Investasi Awal</th>
                                    <td>Rp <?php echo e(number_format($investor->initial_investment, 0, ',', '.')); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Investasi</th>
                                    <td>Rp <?php echo e(number_format($investor->accounts->sum('current_balance'), 0, ',', '.')); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Bagi Hasil</th>
                                    <td>Rp <?php echo e(number_format($investor->accounts->sum('profit_balance'), 0, ',', '.')); ?></td>
                                </tr>
                                <tr>
                                    <th>Jumlah Rekening</th>
                                    <td><?php echo e($investor->accounts->count()); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('investor.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\investor\profile.blade.php ENDPATH**/ ?>