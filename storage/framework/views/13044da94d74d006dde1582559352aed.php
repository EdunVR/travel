
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Data Produksi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Data Produksi')]); ?>
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <?php $__env->stopPush(); ?>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Data Produksi</h1>
                <p class="text-slate-600">Kelola rencana & realisasi produksi</p>
            </div>
            <div class="flex items-center gap-3">
                <select id="outletSelect" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e($outlet->id_outlet == $selectedOutlet ? 'selected' : ''); ?>>
                            <?php echo e($outlet->nama_outlet); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                
                <button id="createProductionBtn" 
                        onclick="openProductionModal()"
                        class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-lg transition">
                    <i class='bx bx-plus'></i>
                    <span>Buat Produksi Baru</span>
                </button>
            </div>
        </div>
    </div>

    
    <section class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            
            <div class="lg:col-span-3">
                <div class="flex flex-wrap gap-3">
                    <select id="filterStatus" class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option value="all">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Disetujui</option>
                        <option value="in_progress">Berjalan</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <select id="filterLine" class="border border-slate-200 rounded-lg px-3 py-2 text-sm min-w-32">
                        <option value="all">Semua Lini</option>
                        <option value="Lini A">Lini A</option>
                        <option value="Lini B">Lini B</option>
                        <option value="Lini C">Lini C</option>
                        <option value="Lini D">Lini D</option>
                    </select>
                    <input type="date" id="filterStartDate" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Dari Tanggal">
                    <input type="date" id="filterEndDate" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="Sampai Tanggal">
                </div>
            </div>

            
            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                <div class="text-center">
                    <div id="activeCount" class="text-2xl font-bold text-primary-700">0</div>
                    <div class="text-sm text-primary-600">Produksi Aktif</div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="productionTable" class="w-full display">
                    <thead>
                        <tr>
                            <th>ID Produksi</th>
                            <th>Produk</th>
                            <th>Lini</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>

    
    <div id="createModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeCreateModal()"></div>
        
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-4xl transform rounded-2xl bg-white shadow-xl transition-all">
                    
                    <div class="flex items-center justify-between p-6 border-b border-slate-200">
                        <div>
                            <h3 class="text-xl font-semibold">Buat Produksi Baru</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                <i class='bx bx-store-alt'></i>
                                <span id="modalOutletName">Loading...</span>
                            </p>
                        </div>
                        <button onclick="closeCreateModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    
                    <form id="productionForm" class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            
                            <div class="relative">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Produk *</label>
                                <input type="hidden" name="product_id" id="product_id" required>
                                <input type="text" id="product_search" 
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Ketik untuk mencari produk..." 
                                       autocomplete="off" required>
                                <div id="product_results" class="hidden absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                                <p class="text-xs text-slate-500 mt-1">Ketik minimal 2 karakter untuk mencari</p>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Lini Produksi *</label>
                                <select name="production_line" required
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Lini</option>
                                    <option value="Lini A">Lini A</option>
                                    <option value="Lini B">Lini B</option>
                                    <option value="Lini C">Lini C</option>
                                    <option value="Lini D">Lini D</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Target Produksi *</label>
                                <input type="number" name="target_quantity" required min="1"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0" onchange="calculateHppPreview()">
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Mulai *</label>
                                <input type="date" name="start_date" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Selesai *</label>
                                <input type="date" name="end_date" required
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-slate-700">Kebutuhan Material</label>
                            </div>
                            <div class="space-y-3" id="materialRequirements">
                                
                                <div class="flex items-center gap-3 material-row">
                                    <input type="hidden" name="materials[0][material_type]" value="">
                                    <select name="materials[0][material_id]" 
                                            class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Material</option>
                                    </select>
                                    <input type="number" name="materials[0][quantity]" min="1" step="0.01"
                                           class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Qty" onchange="calculateHppPreview()">
                                    <select name="materials[0][unit]"
                                            class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="kg">kg</option>
                                        <option value="pcs">pcs</option>
                                        <option value="roll">roll</option>
                                        <option value="unit">unit</option>
                                    </select>
                                    <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addMaterial()" 
                                    class="mt-3 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm">
                                <i class='bx bx-plus'></i>
                                Tambah Material
                            </button>
                        </div>

                        
                        <div class="border border-slate-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-slate-700 mb-4">Biaya Tenaga Kerja (Opsional)</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Tenaga Kerja</label>
                                    <input type="number" name="labor_costs[worker_count]" min="0"
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="0" onchange="calculateLaborCost()">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Biaya per Tenaga Kerja</label>
                                    <input type="number" name="labor_costs[cost_per_worker]" min="0" step="1000"
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="0" onchange="calculateLaborCost()" oninput="formatCurrencyInput(this)">
                                    <small class="text-xs text-slate-500" id="costPerWorkerFormatted">Rp 0</small>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Total Biaya</label>
                                    <input type="text" id="totalLaborCost" readonly
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 text-slate-600"
                                           value="Rp 0">
                                </div>
                            </div>
                            
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="fromAttendance" name="labor_costs[from_attendance]" 
                                           class="rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                           onchange="toggleAttendanceDate()">
                                    <label for="fromAttendance" class="text-sm text-slate-700">Ambil dari data absensi</label>
                                </div>
                                
                                <div id="attendanceDateSection" class="hidden">
                                    <div class="flex items-center gap-3">
                                        <input type="date" name="labor_costs[attendance_date]" id="attendanceDate"
                                               class="border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               onchange="getAttendanceCount()">
                                        <button type="button" onclick="getAttendanceCount()" 
                                                class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                            Ambil Data
                                        </button>
                                    </div>
                                    <div id="attendanceResult" class="mt-2 text-sm text-slate-600"></div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                                    <textarea name="labor_costs[notes]" rows="2"
                                              class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="Catatan untuk biaya tenaga kerja..."></textarea>
                                </div>
                            </div>
                        </div>

                        
                        <div class="border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-slate-700">Biaya Operasional (Opsional)</h4>
                                <button type="button" onclick="addOperationalCost()" 
                                        class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm">
                                    <i class='bx bx-plus'></i>
                                    Tambah Biaya
                                </button>
                            </div>
                            
                            <div class="space-y-3" id="operationalCosts">
                                
                                <div class="flex items-center gap-3 operational-cost-row">
                                    <select name="operational_costs[0][cost_type]"
                                            class="w-40 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Jenis</option>
                                        <option value="listrik">Listrik</option>
                                        <option value="air">Air</option>
                                        <option value="gas">Gas</option>
                                        <option value="bahan_bakar">Bahan Bakar</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                    <input type="number" name="operational_costs[0][amount]" min="0" step="1000"
                                           class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Jumlah biaya" onchange="calculateHppPreview()" oninput="formatCurrencyInput(this)">
                                    <input type="text" name="operational_costs[0][description]"
                                           class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Deskripsi (opsional)">
                                    <button type="button" onclick="removeOperationalCost(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Kadaluarsa (Opsional)</label>
                                <input type="date" name="expiry_date"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-xs text-slate-500 mt-1">Tanggal kadaluarsa produk hasil produksi</p>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Lokasi Gudang (Opsional)</label>
                                <input type="text" name="warehouse_location" maxlength="255"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Contoh: Gudang A - Rak 1">
                                <p class="text-xs text-slate-500 mt-1">Lokasi penyimpanan produk hasil produksi</p>
                            </div>
                        </div>

                        
                        <div class="border border-primary-200 rounded-lg p-4 bg-primary-50">
                            <h4 class="text-sm font-medium text-primary-700 mb-3">Preview HPP Produk</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-600">Biaya Material:</span>
                                    <div id="previewMaterialCost" class="font-medium text-slate-800">Rp 0</div>
                                </div>
                                <div>
                                    <span class="text-slate-600">Biaya Tenaga Kerja:</span>
                                    <div id="previewLaborCost" class="font-medium text-slate-800">Rp 0</div>
                                </div>
                                <div>
                                    <span class="text-slate-600">Biaya Operasional:</span>
                                    <div id="previewOperationalCost" class="font-medium text-slate-800">Rp 0</div>
                                </div>
                                <div>
                                    <span class="text-slate-600">HPP per Unit:</span>
                                    <div id="previewHppPerUnit" class="font-medium text-primary-700 text-lg">Rp 0</div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-primary-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Total HPP:</span>
                                    <div id="previewTotalCost" class="font-bold text-primary-700 text-xl">Rp 0</div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas</label>
                                <select name="priority"
                                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="normal">Normal</option>
                                    <option value="high">Tinggi</option>
                                    <option value="urgent">Mendesak</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                                <textarea name="notes" rows="3"
                                          class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="Catatan tambahan untuk produksi..."></textarea>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-3">Standar Kualitas</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <span class="text-sm text-slate-700">Tingkat Reject Maksimal</span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="max_reject_rate" min="0" max="100" step="0.1"
                                               class="w-20 border border-slate-200 rounded px-2 py-1 text-sm"
                                               value="3.0">
                                        <span class="text-sm text-slate-500">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                                    <span class="text-sm text-slate-700">Efisiensi Minimal</span>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="min_efficiency" min="0" max="100" step="0.1"
                                               class="w-20 border border-slate-200 rounded px-2 py-1 text-sm"
                                               value="85.0">
                                        <span class="text-sm text-slate-500">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    
                    <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200">
                        <button onclick="closeCreateModal()" 
                                class="px-4 py-2.5 text-slate-700 hover:bg-slate-100 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" form="productionForm"
                                class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                            Simpan Produksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        // Define URLs for JavaScript
        const productionDataUrl = "<?php echo e(route('admin.produksi.produksi.data')); ?>";
        const storeUrl = "<?php echo e(route('admin.produksi.produksi.store')); ?>";
        const showUrl = "<?php echo e(route('admin.produksi.produksi.show', ':id')); ?>";
        const deleteUrl = "<?php echo e(route('admin.produksi.produksi.destroy', ':id')); ?>";
        const approveUrl = "<?php echo e(route('admin.produksi.produksi.approve', ':id')); ?>";
        const startUrl = "<?php echo e(route('admin.produksi.produksi.start', ':id')); ?>";
        const productsUrl = "<?php echo e(route('admin.produksi.produksi.products')); ?>";
        const materialsUrl = "<?php echo e(route('admin.produksi.produksi.materials')); ?>";
        const statisticsUrl = "<?php echo e(route('admin.produksi.produksi.statistics')); ?>";
        const addRealizationUrl = "<?php echo e(route('admin.produksi.produksi.realization', ':id')); ?>";
        const attendanceCountUrl = "<?php echo e(route('admin.produksi.produksi.attendance.count')); ?>";
        const hppPreviewUrl = "<?php echo e(route('admin.produksi.produksi.hpp.preview')); ?>";
        
        console.log('URLs loaded:', {
            materialsUrl,
            productsUrl,
            storeUrl,
            attendanceCountUrl,
            hppPreviewUrl
        });

        // Define global functions first
        window.loadMaterials = function() {
            const outletSelect = document.getElementById('outletSelect');
            const outletId = outletSelect ? outletSelect.value : null;
            
            console.log('loadMaterials called, outlet:', outletId);
            
            if (!outletId || !materialsUrl) {
                console.error('Missing outlet ID or materials URL');
                return;
            }
            
            const url = materialsUrl + '?outlet_id=' + outletId;
            console.log('Fetching materials from:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Materials response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Materials response data:', data);
                    if (data.success && data.data) {
                        window.populateMaterialSelects(data.data);
                    } else {
                        console.error('Materials loading failed:', data.message || 'No data');
                    }
                })
                .catch(error => {
                    console.error('Error loading materials:', error);
                });
        };
        
        window.populateMaterialSelects = function(materials) {
            console.log('populateMaterialSelects called with', materials ? materials.length : 0, 'materials');
            
            if (!materials || materials.length === 0) {
                console.warn('No materials to populate');
                return;
            }
            
            setTimeout(() => {
                const selects = document.querySelectorAll('select[name*="material_id"]');
                console.log('Found', selects.length, 'material select elements');
                
                selects.forEach((select, index) => {
                    // Clear existing options except first
                    while (select.children.length > 1) {
                        select.removeChild(select.lastChild);
                    }
                    
                    // Add material options
                    materials.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id;
                        option.textContent = `${material.name} (Stok: ${material.stock} ${material.unit})`;
                        option.dataset.type = material.type;
                        option.dataset.unit = material.unit;
                        select.appendChild(option);
                    });
                    
                    console.log(`Select ${index} now has`, select.children.length, 'options');
                });
            }, 100);
        };

        window.openProductionModal = function() {
            console.log('openProductionModal called');
            const modal = document.getElementById('createModal');
            
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Update outlet name
                const outletSelect = document.getElementById('outletSelect');
                const modalOutletName = document.getElementById('modalOutletName');
                
                if (outletSelect && modalOutletName) {
                    const selectedOption = outletSelect.options[outletSelect.selectedIndex];
                    if (selectedOption) {
                        modalOutletName.textContent = selectedOption.text;
                    }
                }
                
                // Load materials
                window.loadMaterials();
                
                // Set default dates
                const today = new Date().toISOString().split('T')[0];
                const startDateInput = document.querySelector('input[name="start_date"]');
                const endDateInput = document.querySelector('input[name="end_date"]');
                
                if (startDateInput && !startDateInput.value) {
                    startDateInput.value = today;
                }
                
                if (endDateInput && !endDateInput.value && startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    startDate.setDate(startDate.getDate() + 7);
                    endDateInput.value = startDate.toISOString().split('T')[0];
                }
            }
        };

        window.closeCreateModal = function() {
            const modal = document.getElementById('createModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                const form = document.getElementById('productionForm');
                if (form) {
                    form.reset();
                }
                
                // Reset HPP preview
                document.getElementById('previewMaterialCost').textContent = 'Rp 0';
                document.getElementById('previewLaborCost').textContent = 'Rp 0';
                document.getElementById('previewOperationalCost').textContent = 'Rp 0';
                document.getElementById('previewTotalCost').textContent = 'Rp 0';
                document.getElementById('previewHppPerUnit').textContent = 'Rp 0';
            }
        };

        // Material management functions
        window.addMaterial = function() {
            const container = document.getElementById('materialRequirements');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 material-row';
            row.innerHTML = `
                <input type="hidden" name="materials[${index}][material_type]" value="">
                <select name="materials[${index}][material_id]" 
                        class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Pilih Material</option>
                </select>
                <input type="number" name="materials[${index}][quantity]" min="1" step="0.01"
                       class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Qty" onchange="calculateHppPreview()">
                <select name="materials[${index}][unit]"
                        class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="kg">kg</option>
                    <option value="pcs">pcs</option>
                    <option value="roll">roll</option>
                    <option value="unit">unit</option>
                </select>
                <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                    <i class='bx bx-trash'></i>
                </button>
            `;
            container.appendChild(row);
            
            // Populate new select with materials if available
            setTimeout(() => {
                const newSelect = row.querySelector('select[name*="material_id"]');
                if (newSelect && window.lastMaterials) {
                    window.lastMaterials.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id;
                        option.textContent = `${material.name} (Stok: ${material.stock} ${material.unit})`;
                        option.dataset.type = material.type;
                        option.dataset.unit = material.unit;
                        newSelect.appendChild(option);
                    });
                }
            }, 50);
        };

        window.removeMaterial = function(button) {
            const row = button.closest('.material-row');
            const container = document.getElementById('materialRequirements');
            if (container.children.length > 1) {
                row.remove();
                calculateHppPreview();
            }
        };

        // Operational cost management
        let operationalCostIndex = 1;
        
        window.addOperationalCost = function() {
            const container = document.getElementById('operationalCosts');
            const index = operationalCostIndex++;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 operational-cost-row';
            row.innerHTML = `
                <select name="operational_costs[${index}][cost_type]"
                        class="w-40 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Pilih Jenis</option>
                    <option value="listrik">Listrik</option>
                    <option value="air">Air</option>
                    <option value="gas">Gas</option>
                    <option value="bahan_bakar">Bahan Bakar</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                <input type="number" name="operational_costs[${index}][amount]" min="0" step="1000"
                       class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Jumlah biaya" onchange="calculateHppPreview()" oninput="formatCurrencyInput(this)">
                <input type="text" name="operational_costs[${index}][description]"
                       class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Deskripsi (opsional)">
                <button type="button" onclick="removeOperationalCost(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                    <i class='bx bx-trash'></i>
                </button>
            `;
            container.appendChild(row);
        };

        window.removeOperationalCost = function(button) {
            const row = button.closest('.operational-cost-row');
            if (row) {
                row.remove();
                calculateHppPreview();
            }
        };

        // Labor cost functions
        window.calculateLaborCost = function() {
            const workerCount = parseFloat(document.querySelector('input[name="labor_costs[worker_count]"]').value) || 0;
            const costPerWorker = parseFloat(document.querySelector('input[name="labor_costs[cost_per_worker]"]').value) || 0;
            const totalCost = workerCount * costPerWorker;
            
            document.getElementById('totalLaborCost').value = formatCurrency(totalCost);
            calculateHppPreview();
        };

        window.toggleAttendanceDate = function() {
            const checkbox = document.getElementById('fromAttendance');
            const section = document.getElementById('attendanceDateSection');
            
            if (checkbox.checked) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
                document.querySelector('input[name="labor_costs[worker_count]"]').value = '';
                calculateLaborCost();
            }
        };

        window.getAttendanceCount = function() {
            const dateInput = document.getElementById('attendanceDate');
            const resultDiv = document.getElementById('attendanceResult');
            const workerCountInput = document.querySelector('input[name="labor_costs[worker_count]"]');
            
            if (!dateInput.value) {
                alert('Pilih tanggal terlebih dahulu');
                return;
            }
            
            resultDiv.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mengambil data absensi...';
            
            const outletSelect = document.getElementById('outletSelect');
            const outletId = outletSelect ? outletSelect.value : null;
            
            fetch(attendanceCountUrl + '?' + new URLSearchParams({
                date: dateInput.value,
                outlet_id: outletId
            }))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = data.data.count;
                    resultDiv.innerHTML = `<i class="bx bx-check text-green-600"></i> Ditemukan ${count} karyawan hadir`;
                    workerCountInput.value = count;
                    calculateLaborCost();
                } else {
                    resultDiv.innerHTML = `<i class="bx bx-x text-red-600"></i> ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<i class="bx bx-x text-red-600"></i> Gagal mengambil data absensi';
            });
        };

        // HPP calculation
        window.calculateHppPreview = function() {
            const formData = new FormData(document.getElementById('productionForm'));
            
            // Convert FormData to regular object
            const data = {};
            for (let [key, value] of formData.entries()) {
                if (key.includes('[') && key.includes(']')) {
                    const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                    if (matches) {
                        const [, arrayName, index, fieldName] = matches;
                        if (!data[arrayName]) data[arrayName] = [];
                        if (!data[arrayName][index]) data[arrayName][index] = {};
                        data[arrayName][index][fieldName] = value;
                    } else {
                        const matches2 = key.match(/(\w+)\[(\w+)\]/);
                        if (matches2) {
                            const [, objectName, fieldName] = matches2;
                            if (!data[objectName]) data[objectName] = {};
                            data[objectName][fieldName] = value;
                        }
                    }
                } else {
                    data[key] = value;
                }
            }
            
            if (!hppPreviewUrl) return;
            
            fetch(hppPreviewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formatted = data.data.formatted;
                    document.getElementById('previewMaterialCost').textContent = formatted.material_cost;
                    document.getElementById('previewLaborCost').textContent = formatted.labor_cost;
                    document.getElementById('previewOperationalCost').textContent = formatted.operational_cost;
                    document.getElementById('previewTotalCost').textContent = formatted.total_cost;
                    document.getElementById('previewHppPerUnit').textContent = formatted.hpp_per_unit;
                }
            })
            .catch(error => {
                console.error('Error calculating HPP preview:', error);
            });
        };

        // Currency formatting
        window.formatCurrencyInput = function(input) {
            const value = parseFloat(input.value) || 0;
            const formatted = formatCurrency(value);
            
            const smallText = input.nextElementSibling;
            if (smallText && smallText.tagName === 'SMALL') {
                smallText.textContent = formatted;
            }
        };

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Store materials for new rows
        const originalPopulate = window.populateMaterialSelects;
        window.populateMaterialSelects = function(materials) {
            window.lastMaterials = materials;
            return originalPopulate(materials);
        };

        // Test function
        window.testMaterialsLoading = function() {
            console.log('=== TESTING MATERIALS LOADING ===');
            const outletSelect = document.getElementById('outletSelect');
            const outletId = outletSelect ? outletSelect.value : null;
            console.log('Current outlet ID:', outletId);
            
            if (!outletId) {
                console.error('No outlet selected');
                return;
            }
            
            window.loadMaterials();
        };
    </script>
    <script src="<?php echo e(asset('js/production.js')); ?>?v=<?php echo e(time()); ?>&debug=1"></script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\index_complete.blade.php ENDPATH**/ ?>