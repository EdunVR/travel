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
            <h1 class="text-2xl font-bold text-gray-800">Perpanjang Kontrak</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="<?php echo e(route('sdm.kontrak.perpanjangan.store')); ?>" method="POST" enctype="multipart/form-data" x-data="perpanjanganForm()">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kontrak yang Akan Diperpanjang *</label>
                        <select name="kontrak_lama_id" required class="w-full border-gray-300 rounded-lg" x-model="kontrakLamaId" @change="loadKontrakData()">
                            <option value="">Pilih Kontrak</option>
                            <?php $__currentLoopData = $kontrakAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k->id); ?>" 
                                        data-nomor="<?php echo e($k->nomor_kontrak); ?>"
                                        data-employee="<?php echo e($k->recruitment->name); ?>"
                                        data-jabatan="<?php echo e($k->jabatan); ?>"
                                        data-unit="<?php echo e($k->unit_kerja); ?>"
                                        data-jenis="<?php echo e($k->jenis_kontrak); ?>"
                                        data-gaji="<?php echo e($k->gaji_pokok); ?>">
                                    <?php echo e($k->nomor_kontrak); ?> - <?php echo e($k->recruitment->name); ?> (<?php echo e($k->jabatan); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Info Kontrak Lama -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg" x-show="kontrakLamaId">
                        <h3 class="font-semibold text-gray-700 mb-2">Informasi Kontrak Lama:</h3>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-600">Karyawan:</span> <span x-text="employeeName"></span></div>
                            <div><span class="text-gray-600">Jabatan:</span> <span x-text="jabatan"></span></div>
                            <div><span class="text-gray-600">Unit Kerja:</span> <span x-text="unitKerja"></span></div>
                            <div><span class="text-gray-600">Jenis:</span> <span x-text="jenisKontrak"></span></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontrak Baru *</label>
                        <input type="text" name="nomor_kontrak_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Baru *</label>
                        <input type="date" name="tanggal_mulai_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Baru *</label>
                        <input type="date" name="tanggal_selesai_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Perpanjangan (Opsional)</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Perpanjangan</label>
                        <textarea name="alasan" rows="3" class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="<?php echo e(route('sdm.kontrak.perpanjangan.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan Perpanjangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function perpanjanganForm() {
            return {
                kontrakLamaId: '',
                employeeName: '',
                jabatan: '',
                unitKerja: '',
                jenisKontrak: '',
                loadKontrakData() {
                    const select = document.querySelector('select[name="kontrak_lama_id"]');
                    const option = select.options[select.selectedIndex];
                    if (option.value) {
                        this.employeeName = option.dataset.employee;
                        this.jabatan = option.dataset.jabatan;
                        this.unitKerja = option.dataset.unit;
                        this.jenisKontrak = option.dataset.jenis;
                    }
                }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\perpanjangan-form.blade.php ENDPATH**/ ?>