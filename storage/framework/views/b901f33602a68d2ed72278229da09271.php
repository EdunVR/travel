

<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e($title); ?></h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php if(isset($investor)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="join_date">Tanggal Bergabung*</label>
                            <input type="date" class="form-control" id="join_date" name="join_date" 
                                   value="<?php echo e(old('join_date', isset($investor) ? $investor->join_date->format('Y-m-d') : date('Y-m-d'))); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Lengkap*</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo e(old('name', $investor->name ?? '')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">Pilih</option>
                                <option value="Laki-laki" <?php echo e(old('jenis_kelamin', $investor->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo e(old('jenis_kelamin', $investor->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : ''); ?>>Perempuan</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo e(old('email', $investor->email ?? '')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon*</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?php echo e(old('phone', $investor->phone ?? '')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori Investor*</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="syirkah" <?php echo e(old('category', $investor->category ?? '') == 'syirkah' ? 'selected' : ''); ?>>Syirkah</option>
                                <option value="investama" <?php echo e(old('category', $investor->category ?? '') == 'investama' ? 'selected' : ''); ?>>Investama</option>
                                <option value="sukuk" <?php echo e(old('category', $investor->category ?? '') == 'sukuk' ? 'selected' : ''); ?>>Sukuk</option>
                                <option value="internal" <?php echo e(old('category', $investor->category ?? '') == 'internal' ? 'selected' : ''); ?>>Internal</option>
                                <option value="eksternal" <?php echo e(old('category', $investor->category ?? '') == 'eksternal' ? 'selected' : ''); ?>>Eksternal</option>
                                <option value="aktif" <?php echo e(old('category', $investor->category ?? '') == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                                <option value="pasif" <?php echo e(old('category', $investor->category ?? '') == 'pasif' ? 'selected' : ''); ?>>Pasif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status*</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active" <?php echo e(old('status', $investor->status ?? '') == 'active' ? 'selected' : ''); ?>>Aktif</option>
                                <option value="inactive" <?php echo e(old('status', $investor->status ?? '') == 'inactive' ? 'selected' : ''); ?>>Non-Aktif</option>
                            </select>
                        </div>
                        
                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="initial_investment">Investasi Awal (Rp)*</label>
                            <input type="number" class="form-control" id="initial_investment" name="initial_investment" 
                                   value="<?php echo e(old('initial_investment', $investor->initial_investment ?? 0)); ?>" required>
                        </div>

                        
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tempo">Tanggal Jatuh Tempo</label>
                            <input type="date" class="form-control" id="tempo" name="tempo" 
                                value="<?php echo e(old('tempo', $investor->tempo ?? '')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="bank">Nama Bank</label>
                            <input type="text" class="form-control" id="bank" name="bank" 
                                value="<?php echo e(old('bank', $investor->bank ?? 0)); ?>">
                        </div>

                        <div class="form-group">
                            <label for="rekening">No. Rekening</label>
                            <input type="number" class="form-control" id="rekening" name="rekening" 
                                value="<?php echo e(old('rekening', $investor->rekening ?? 0)); ?>">
                        </div>

                        <div class="form-group">
                            <label for="atas_nama">Atas Nama</label>
                            <input type="text" class="form-control" id="atas_nama" name="atas_nama" 
                                value="<?php echo e(old('atas_nama', $investor->atas_nama ?? '')); ?>"> 
                        </div>

                        <div class="form-group">
                            <label for="profit_percentage">Persen Keuntungan (%)</label>
                            <input type="number" class="form-control" id="profit_percentage" name="profit_percentage" 
                                value="<?php echo e(old('profit_percentage', $investor->profit_percentage ?? 0)); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="photo">Foto Profil</label>
                            <input type="file" class="form-control-file" id="photo" name="photo">
                            <?php if(isset($investor) && $investor->photo): ?>
                                <small class="form-text text-muted">
                                    Foto saat ini: <a href="<?php echo e(asset('storage/'.$investor->photo)); ?>" target="_blank">Lihat</a>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo e(old('address', $investor->address ?? '')); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2"><?php echo e(old('notes', $investor->notes ?? '')); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo e(route('irp.investor.index')); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\create.blade.php ENDPATH**/ ?>