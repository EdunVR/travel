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
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Monitoring Masa Berlaku Dokumen</h1>
                <p class="text-gray-600 mt-1">Pantau dokumen yang akan/sudah habis masa berlaku</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('sdm.kontrak.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button onclick="openPrintModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-print mr-2"></i>Generate & Print PDF
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                    <select name="filter_status" class="w-full border-gray-300 rounded-lg" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="green" <?php echo e(request('filter_status') == 'green' ? 'selected' : ''); ?>>Aktif (Hijau)</option>
                        <option value="yellow" <?php echo e(request('filter_status') == 'yellow' ? 'selected' : ''); ?>>Akan Habis (Kuning)</option>
                        <option value="red" <?php echo e(request('filter_status') == 'red' ? 'selected' : ''); ?>>Sudah Habis (Merah)</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h3 class="font-semibold text-gray-700 mb-3">Keterangan Warna:</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-sm text-gray-700">Aktif (Masih Lama)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                    <span class="text-sm text-gray-700">Akan Habis (≤ 30 hari)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span class="text-sm text-gray-700">Sudah Habis</span>
                </div>
            </div>
        </div>

        <!-- Kontrak Kerja -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Kontrak Kerja</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Kontrak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $kontrak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_selesai, false);
                                $statusClass = $item->status_warna === 'green' ? 'bg-green-100 text-green-800' : 
                                              ($item->status_warna === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->recruitment->name ?? '-'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->nomor_kontrak); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->jabatan); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Sudah Habis'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 rounded text-xs <?php echo e($statusClass); ?>">
                                        <?php if($item->status_warna === 'green'): ?> Aktif
                                        <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                                        <?php else: ?> Sudah Habis
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada kontrak dengan masa berlaku</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Surat Peringatan -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Surat Peringatan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. SP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $sp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                                $statusClass = $item->status_warna === 'green' ? 'bg-green-100 text-green-800' : 
                                              ($item->status_warna === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->recruitment->name ?? '-'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->nomor_sp); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs"><?php echo e($item->jenis_sp); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->tanggal_berakhir->format('d/m/Y')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Sudah Habis'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 rounded text-xs <?php echo e($statusClass); ?>">
                                        <?php if($item->status_warna === 'green'): ?> Aktif
                                        <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                                        <?php else: ?> Sudah Habis
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada SP dengan masa berlaku</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dokumen HR -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Dokumen HR</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Berakhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $dokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $sisaHari = \Carbon\Carbon::now()->diffInDays($item->tanggal_berakhir, false);
                                $statusClass = $item->status_warna === 'green' ? 'bg-green-100 text-green-800' : 
                                              ($item->status_warna === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->recruitment->name ?? 'Umum'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->nomor_dokumen); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs"><?php echo e($item->jenis_dokumen); ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate"><?php echo e($item->judul_dokumen); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->tanggal_berakhir->format('d/m/Y')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($sisaHari > 0 ? $sisaHari . ' hari' : 'Sudah Habis'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 rounded text-xs <?php echo e($statusClass); ?>">
                                        <?php if($item->status_warna === 'green'): ?> Aktif
                                        <?php elseif($item->status_warna === 'yellow'): ?> Akan Habis
                                        <?php else: ?> Sudah Habis
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada dokumen HR dengan masa berlaku</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Print PDF -->
    <div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Preview Monitoring Masa Berlaku Dokumen</h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="mb-4 flex gap-2">
                <a id="downloadPdfBtn" href="<?php echo e(route('sdm.kontrak.export.monitoring.pdf')); ?>" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <button onclick="printPdf()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
            
            <div class="border rounded-lg overflow-hidden" style="height: 70vh;">
                <iframe id="pdfFrame" src="" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openPrintModal() {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            const pdfUrl = '<?php echo e(route("sdm.kontrak.export.monitoring.pdf")); ?>';
            
            iframe.src = pdfUrl;
            modal.classList.remove('hidden');
        }

        function closePrintModal() {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            
            modal.classList.add('hidden');
            iframe.src = '';
        }

        function printPdf() {
            const iframe = document.getElementById('pdfFrame');
            iframe.contentWindow.print();
        }

        // Close modal when clicking outside
        document.getElementById('printModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrintModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePrintModal();
            }
        });
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\monitoring.blade.php ENDPATH**/ ?>