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
            <h1 class="text-2xl font-bold text-gray-800"><?php echo e(isset($dokumen) ? 'Edit' : 'Tambah'); ?> Dokumen HR</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="<?php echo e(isset($dokumen) ? route('sdm.kontrak.dokumen.update', $dokumen->id) : route('sdm.kontrak.dokumen.store')); ?>" method="POST" enctype="multipart/form-data" x-data="{ hasMasaBerlaku: <?php echo e(isset($dokumen) && $dokumen->memiliki_masa_berlaku ? 'true' : 'false'); ?> }">
                <?php echo csrf_field(); ?>
                <?php if(isset($dokumen)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan (Opsional)</label>
                        <select name="recruitment_id" class="w-full border-gray-300 rounded-lg">
                            <option value="">Dokumen Umum</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php echo e((isset($dokumen) && $dokumen->recruitment_id == $emp->id) ? 'selected' : ''); ?>><?php echo e($emp->name); ?> - <?php echo e($emp->position); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika dokumen bersifat umum</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Dokumen *</label>
                        <input type="text" name="nomor_dokumen" value="<?php echo e($dokumen->nomor_dokumen ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen *</label>
                        <input type="text" name="jenis_dokumen" value="<?php echo e($dokumen->jenis_dokumen ?? ''); ?>" required class="w-full border-gray-300 rounded-lg" placeholder="SK Jabatan, Surat Tugas, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Dokumen *</label>
                        <input type="text" name="judul_dokumen" value="<?php echo e($dokumen->judul_dokumen ?? ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit *</label>
                        <input type="date" name="tanggal_terbit" value="<?php echo e(isset($dokumen) ? $dokumen->tanggal_terbit->format('Y-m-d') : ''); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="memiliki_masa_berlaku" value="1" <?php echo e((isset($dokumen) && $dokumen->memiliki_masa_berlaku) ? 'checked' : ''); ?> class="rounded border-gray-300" x-model="hasMasaBerlaku">
                            <span class="text-sm font-medium text-gray-700">Dokumen Memiliki Masa Berlaku</span>
                        </label>
                    </div>

                    <div x-show="hasMasaBerlaku">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlaku</label>
                        <input type="date" name="tanggal_berlaku" value="<?php echo e(isset($dokumen) && $dokumen->tanggal_berlaku ? $dokumen->tanggal_berlaku->format('Y-m-d') : ''); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div x-show="hasMasaBerlaku">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" value="<?php echo e(isset($dokumen) && $dokumen->tanggal_berakhir ? $dokumen->tanggal_berakhir->format('Y-m-d') : ''); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <?php if(isset($dokumen)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="aktif" <?php echo e($dokumen->status == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="habis" <?php echo e($dokumen->status == 'habis' ? 'selected' : ''); ?>>Habis</option>
                            <option value="dibatalkan" <?php echo e($dokumen->status == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                        <?php if(isset($dokumen) && $dokumen->file_path): ?>
                            <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="<?php echo e(Storage::url($dokumen->file_path)); ?>" target="_blank" class="text-blue-600">Lihat</a></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e($dokumen->deskripsi ?? ''); ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e($dokumen->catatan ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="<?php echo e(route('sdm.kontrak.dokumen.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</a>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\dokumen-form.blade.php ENDPATH**/ ?>