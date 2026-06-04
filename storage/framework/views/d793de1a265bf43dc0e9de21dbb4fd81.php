<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800"><?php echo e(isset($sp) ? 'Edit' : 'Tambah'); ?> Surat Peringatan</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="<?php echo e(isset($sp) ? route('sdm.kontrak.sp.update', $sp->id) : route('sdm.kontrak.sp.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php if(isset($sp)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan *</label>
                        <select name="recruitment_id" required class="w-full border-gray-300 rounded-lg">
                            <option value="">Pilih Karyawan</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php echo e((isset($sp) && $sp->recruitment_id == $emp->id) ? 'selected' : ''); ?>><?php echo e($emp->name); ?> - <?php echo e($emp->position); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor SP *</label>
                        <input type="text" name="nomor_sp" value="<?php echo e($sp->nomor_sp ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis SP *</label>
                        <select name="jenis_sp" required class="w-full border-gray-300 rounded-lg">
                            <option value="SP1" <?php echo e((isset($sp) && $sp->jenis_sp == 'SP1') ? 'selected' : ''); ?>>SP1</option>
                            <option value="SP2" <?php echo e((isset($sp) && $sp->jenis_sp == 'SP2') ? 'selected' : ''); ?>>SP2</option>
                            <option value="SP3" <?php echo e((isset($sp) && $sp->jenis_sp == 'SP3') ? 'selected' : ''); ?>>SP3</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal SP *</label>
                        <input type="date" name="tanggal_sp" value="<?php echo e(isset($sp) ? $sp->tanggal_sp->format('Y-m-d') : ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlaku *</label>
                        <input type="date" name="tanggal_berlaku" value="<?php echo e(isset($sp) ? $sp->tanggal_berlaku->format('Y-m-d') : ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" value="<?php echo e(isset($sp) && $sp->tanggal_berakhir ? $sp->tanggal_berakhir->format('Y-m-d') : ''); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <?php if(isset($sp)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="aktif" <?php echo e($sp->status == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="selesai" <?php echo e($sp->status == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                            <option value="dibatalkan" <?php echo e($sp->status == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen SP</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                        <?php if(isset($sp) && $sp->file_path): ?>
                            <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="<?php echo e(Storage::url($sp->file_path)); ?>" target="_blank" class="text-blue-600">Lihat</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan *</label>
                        <textarea name="alasan" rows="3" required class="w-full border-gray-300 rounded-lg"><?php echo e($sp->alasan ?? ''); ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e($sp->catatan ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="<?php echo e(route('sdm.kontrak.sp.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\sp-form.blade.php ENDPATH**/ ?>