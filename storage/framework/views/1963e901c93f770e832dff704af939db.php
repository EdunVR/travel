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
                <h1 class="text-2xl font-bold text-gray-800">Surat Peringatan (SP)</h1>
                <p class="text-gray-600 mt-1">Kelola SP1, SP2, SP3 karyawan</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('sdm.kontrak.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="<?php echo e(route('sdm.kontrak.export.sp.pdf')); ?>" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="<?php echo e(route('sdm.kontrak.sp.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah SP
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="recruitment_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Karyawan</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>" <?php echo e(request('recruitment_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?> - <?php echo e($emp->position); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis SP</label>
                    <select name="jenis_sp" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Jenis</option>
                        <option value="SP1" <?php echo e(request('jenis_sp') == 'SP1' ? 'selected' : ''); ?>>SP1</option>
                        <option value="SP2" <?php echo e(request('jenis_sp') == 'SP2' ? 'selected' : ''); ?>>SP2</option>
                        <option value="SP3" <?php echo e(request('jenis_sp') == 'SP3' ? 'selected' : ''); ?>>SP3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?php echo e(request('status') == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                        <option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. SP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal SP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masa Berlaku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $sp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($item->nomor_sp); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->recruitment->name ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold"><?php echo e($item->jenis_sp); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($item->tanggal_sp->format('d/m/Y')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item->tanggal_berlaku->format('d/m/Y')); ?> - <?php echo e($item->tanggal_berakhir ? $item->tanggal_berakhir->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate"><?php echo e($item->alasan); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if($item->status === 'aktif'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                                <?php elseif($item->status === 'selesai'): ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Selesai</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2 items-center">
                                    <!-- Print PDF Button -->
                                    <button onclick="openPrintModal(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->nomor_sp)); ?>')" 
                                            class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition text-xs" 
                                            title="Generate & Print PDF SP">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    
                                    <?php if($item->file_path): ?>
                                        <a href="<?php echo e(Storage::url($item->file_path)); ?>" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat File"><i class="fas fa-file-pdf"></i></a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('sdm.kontrak.sp.edit', $item->id)); ?>" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('sdm.kontrak.sp.destroy', $item->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data surat peringatan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4"><?php echo e($sp->links()); ?></div>
    </div>

    <!-- Modal Print PDF -->
    <div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Preview Surat Peringatan - <span id="modalSpNumber"></span></h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="mb-4 flex gap-2">
                <a id="downloadPdfBtn" href="#" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
        function openPrintModal(spId, nomorSp) {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            const downloadBtn = document.getElementById('downloadPdfBtn');
            const modalNumber = document.getElementById('modalSpNumber');
            
            const pdfUrl = '<?php echo e(route("sdm.kontrak.sp.print", ":id")); ?>'.replace(':id', spId);
            
            iframe.src = pdfUrl;
            downloadBtn.href = pdfUrl;
            modalNumber.textContent = nomorSp;
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\sp-index.blade.php ENDPATH**/ ?>