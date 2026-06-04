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
                <h1 class="text-2xl font-bold text-gray-800">Perpanjangan Kontrak</h1>
                <p class="text-gray-600 mt-1">Riwayat perpanjangan kontrak karyawan</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('sdm.kontrak.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="<?php echo e(route('sdm.kontrak.perpanjangan.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Perpanjang Kontrak
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontrak Lama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontrak Baru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Perpanjangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $perpanjangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item->kontrakLama->recruitment->name ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item->kontrakLama->nomor_kontrak ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item->kontrakBaru->nomor_kontrak ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item->tanggal_perpanjangan->format('d/m/Y')); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo e($item->alasan ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if($item->file_path): ?>
                                    <a href="<?php echo e(Storage::url($item->file_path)); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-file-pdf"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="openPrintModal(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->kontrakBaru->nomor_kontrak)); ?>')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition" 
                                        title="Generate & Print PDF Perpanjangan">
                                    <i class="fas fa-print mr-1"></i>
                                    <span class="text-xs font-medium">Print</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data perpanjangan kontrak
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($perpanjangan->links()); ?>

        </div>
    </div>

    <!-- Modal Print PDF -->
    <div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Preview Perpanjangan Kontrak - <span id="modalKontrakNumber"></span></h3>
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
        function openPrintModal(perpanjanganId, nomorKontrak) {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            const downloadBtn = document.getElementById('downloadPdfBtn');
            const modalNumber = document.getElementById('modalKontrakNumber');
            
            const pdfUrl = '<?php echo e(route("sdm.kontrak.perpanjangan.print", ":id")); ?>'.replace(':id', perpanjanganId);
            
            iframe.src = pdfUrl;
            downloadBtn.href = pdfUrl;
            modalNumber.textContent = nomorKontrak;
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\perpanjangan-index.blade.php ENDPATH**/ ?>