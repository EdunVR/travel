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
            <h1 class="text-2xl font-bold text-gray-800"><?php echo e(isset($kontrak) ? 'Edit' : 'Tambah'); ?> Kontrak Kerja</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="<?php echo e(isset($kontrak) ? route('sdm.kontrak.kontrak.update', $kontrak->id) : route('sdm.kontrak.kontrak.store')); ?>" 
                  method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php if(isset($kontrak)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan *</label>
                        <select name="recruitment_id" required class="w-full border-gray-300 rounded-lg">
                            <option value="">Pilih Karyawan (<?php echo e(count($employees)); ?> tersedia)</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php echo e((isset($kontrak) && $kontrak->recruitment_id == $emp->id) ? 'selected' : ''); ?>>
                                    <?php echo e($emp->name); ?> - <?php echo e($emp->position); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if(count($employees) == 0): ?>
                            <p class="text-xs text-red-600 mt-1">⚠️ Tidak ada karyawan aktif. Periksa data recruitment.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontrak *</label>
                        <input type="text" name="nomor_kontrak" value="<?php echo e($kontrak->nomor_kontrak ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kontrak *</label>
                        <select name="jenis_kontrak" required class="w-full border-gray-300 rounded-lg">
                            <option value="PKWT" <?php echo e((isset($kontrak) && $kontrak->jenis_kontrak == 'PKWT') ? 'selected' : ''); ?>>PKWT</option>
                            <option value="PKWTT" <?php echo e((isset($kontrak) && $kontrak->jenis_kontrak == 'PKWTT') ? 'selected' : ''); ?>>PKWTT</option>
                            <option value="Freelance" <?php echo e((isset($kontrak) && $kontrak->jenis_kontrak == 'Freelance') ? 'selected' : ''); ?>>Freelance</option>
                            <option value="Magang" <?php echo e((isset($kontrak) && $kontrak->jenis_kontrak == 'Magang') ? 'selected' : ''); ?>>Magang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan *</label>
                        <input type="text" name="jabatan" value="<?php echo e($kontrak->jabatan ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja *</label>
                        <input type="text" name="unit_kerja" value="<?php echo e($kontrak->unit_kerja ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" value="<?php echo e(isset($kontrak) ? $kontrak->tanggal_mulai->format('Y-m-d') : ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="<?php echo e(isset($kontrak) && $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('Y-m-d') : ''); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gaji Pokok</label>
                        <input type="number" name="gaji_pokok" value="<?php echo e($kontrak->gaji_pokok ?? ''); ?>" 
                               class="w-full border-gray-300 rounded-lg"
                               onchange="showCurrencyFormat(this)">
                        <p class="text-xs text-blue-600 mt-1 currency-display" style="display: none;"></p>
                    </div>

                    <?php if(isset($kontrak)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="aktif" <?php echo e($kontrak->status == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="habis" <?php echo e($kontrak->status == 'habis' ? 'selected' : ''); ?>>Habis</option>
                            <option value="diperpanjang" <?php echo e($kontrak->status == 'diperpanjang' ? 'selected' : ''); ?>>Diperpanjang</option>
                            <option value="dibatalkan" <?php echo e($kontrak->status == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen (PDF/IMG, Max 5MB)</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                        <?php if(isset($kontrak) && $kontrak->file_path): ?>
                            <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="<?php echo e(Storage::url($kontrak->file_path)); ?>" target="_blank" class="text-blue-600">Lihat</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border-gray-300 rounded-lg"><?php echo e($kontrak->deskripsi ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="<?php echo e(route('sdm.kontrak.kontrak.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCurrencyFormat(input) {
            const value = parseFloat(input.value);
            const displayElement = input.parentNode.querySelector('.currency-display');
            
            if (value > 0) {
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
                
                displayElement.textContent = '≈ ' + formatted;
                displayElement.style.display = 'block';
            } else {
                displayElement.style.display = 'none';
            }
        }
    </script>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\kontrak-form.blade.php ENDPATH**/ ?>