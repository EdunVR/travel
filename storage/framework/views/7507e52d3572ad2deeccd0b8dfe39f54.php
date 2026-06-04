<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Kontra Bon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kontra Bon']); ?>
    <div x-data="kontraBonManagement()" x-init="init()" class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kontra Bon</h1>
                <p class="text-gray-600">Kelola kontra bon pelanggan</p>
            </div>
            <?php if(auth()->user()->hasRole('super_admin') || auth()->user()->hasPermission('sales.kontrabon.create')): ?>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2" data-toggle="modal" data-target="#modal-create-kontrabon">
                <i class="bx bx-plus"></i>
                <span>Tambah Kontra Bon</span>
            </button>
            <?php endif; ?>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="switchTab('piutang')" :class="currentTab === 'piutang' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Piutang
                    </button>
                    <button @click="switchTab('kontrabon')" :class="currentTab === 'kontrabon' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        List Kontra Bon
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Tab Piutang -->
                <div x-show="currentTab === 'piutang'" class="tab-content">
                    <div class="mb-4 flex flex-wrap gap-4">
                        <?php if(count($outlets) > 1): ?>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Filter Outlet</label>
                            <div class="relative">
                                <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                                        class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between min-w-[200px] bg-white">
                                    <span x-text="getSelectedOutletsText()"></span>
                                    <i class='bx bx-chevron-down ml-2' :class="showOutletDropdown ? 'rotate-180' : ''"></i>
                                </button>
                                
                                <div x-show="showOutletDropdown" x-on:click.away="closeOutletDropdown()"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div class="p-3 border-b border-gray-100">
                                        <button x-on:click="selectAllOutlets()" class="text-xs text-blue-600 hover:text-blue-800 mr-3">Pilih Semua</button>
                                        <button x-on:click="clearAllOutlets()" class="text-xs text-gray-600 hover:text-gray-800">Hapus Semua</button>
                                    </div>
                                    <div class="p-2">
                                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" x-on:click.stop>
                                            <input type="checkbox" value="<?php echo e($outlet->id_outlet); ?>" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                                            <span class="text-sm"><?php echo e($outlet->nama_outlet); ?></span>
                                        </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="flex flex-col">
                            <label for="status_piutang" class="text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select x-model="statusPiutang" @change="reloadPiutangTable()" id="status_piutang" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="belum_lunas">Belum Lunas</option>
                                <option value="lunas">Lunas</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="table-piutang" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all-piutang" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Nota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <?php if(count($outlets) > 1): ?>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                                    <?php endif; ?>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibayar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Kontra Bon -->
                <div x-show="currentTab === 'kontrabon'" class="tab-content">
                    <div class="mb-4 flex flex-wrap gap-4">
                        <?php if(count($outlets) > 1): ?>
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">Filter Outlet</label>
                            <div class="relative">
                                <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                                        class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between min-w-[200px] bg-white">
                                    <span x-text="getSelectedOutletsText()"></span>
                                    <i class='bx bx-chevron-down ml-2' :class="showOutletDropdown ? 'rotate-180' : ''"></i>
                                </button>
                                
                                <div x-show="showOutletDropdown" x-on:click.away="closeOutletDropdown()"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div class="p-3 border-b border-gray-100">
                                        <button x-on:click="selectAllOutlets()" class="text-xs text-blue-600 hover:text-blue-800 mr-3">Pilih Semua</button>
                                        <button x-on:click="clearAllOutlets()" class="text-xs text-gray-600 hover:text-gray-800">Hapus Semua</button>
                                    </div>
                                    <div class="p-2">
                                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" x-on:click.stop>
                                            <input type="checkbox" value="<?php echo e($outlet->id_outlet); ?>" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                                            <span class="text-sm"><?php echo e($outlet->nama_outlet); ?></span>
                                        </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="table-kontrabon" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontra Bon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <?php if(count($outlets) > 1): ?>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                                    <?php endif; ?>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modal Create -->
    <?php echo $__env->make('admin.penjualan.kontrabon.modals.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Include Print Modal -->
    <?php echo $__env->make('admin.penjualan.kontrabon.modals.print', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Detail Modal -->
    <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail-label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="modal-detail-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-detail-label">Detail Kontra Bon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-detail-body">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat detail...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        let tablePiutang, tableKontraBon;
        let isInitialized = false;

        function kontraBonManagement() {
            return {
                currentTab: 'kontrabon',
                selectedOutlets: <?php echo json_encode($outlets->pluck('id_outlet')->toArray(), 15, 512) ?>, // Default: semua outlet terceklis
                showOutletDropdown: false,
                statusPiutang: 'belum_lunas',

                init() {
                    // Only initialize once
                    if (isInitialized) return;
                    
                    this.$nextTick(() => {
                        // Wait for DOM to be fully ready
                        setTimeout(() => {
                            this.initDataTables();
                            isInitialized = true;
                        }, 300);
                    });
                },

                initDataTables() {
                    // Initialize tables only once - never destroy them
                    if (!tablePiutang) {
                        this.initPiutangTable();
                    }
                    
                    if (!tableKontraBon) {
                        this.initKontraBonTable();
                    }

                    // Select all checkbox handler
                    $('#select-all-piutang').off('change').on('change', function() {
                        $('.piutang-checkbox').prop('checked', this.checked);
                    });
                },

                switchTab(tab) {
                    this.currentTab = tab;
                    
                    // Load data when switching to a tab
                    this.$nextTick(() => {
                        if (tab === 'piutang' && tablePiutang) {
                            tablePiutang.ajax.reload(null, false);
                        } else if (tab === 'kontrabon' && tableKontraBon) {
                            tableKontraBon.ajax.reload(null, false);
                        }
                    });
                },

                getSelectedOutletsText() {
                    if (this.selectedOutlets.length === 0) {
                        return 'Pilih Outlet';
                    } else if (this.selectedOutlets.length === 1) {
                        const outlet = <?php echo json_encode($outlets, 15, 512) ?>.find(o => o.id_outlet == this.selectedOutlets[0]);
                        return outlet ? outlet.nama_outlet : 'Pilih Outlet';
                    } else {
                        return `${this.selectedOutlets.length} outlet dipilih`;
                    }
                },

                selectAllOutlets() {
                    this.selectedOutlets = <?php echo json_encode($outlets->pluck('id_outlet')->toArray(), 15, 512) ?>;
                    this.onOutletSelectionChange();
                },

                clearAllOutlets() {
                    this.selectedOutlets = [];
                    this.onOutletSelectionChange();
                },

                onOutletSelectionChange() {
                    // Close dropdown
                    // Dropdown stays open for better UX
                    
                    // Reload tables with new outlet selection
                    this.reloadTables();
                },

                reloadTables() {
                    // Only reload data, never destroy tables
                    if (tablePiutang) {
                        tablePiutang.ajax.reload(null, false);
                    }
                    
                    if (tableKontraBon) {
                        tableKontraBon.ajax.reload(null, false);
                    }
                },

                reloadPiutangTable() {
                    // Reload piutang table when status changes
                    if (tablePiutang) {
                        tablePiutang.ajax.reload(null, false);
                    }
                },

                reloadPiutangTable() {
                    // Reload piutang table when status changes
                    if (tablePiutang) {
                        tablePiutang.ajax.reload(null, false);
                    }
                },

                initPiutangTable() {
                    const self = this;
                    
                    tablePiutang = $('#table-piutang').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: false, // Never destroy
                        ajax: {
                            url: '<?php echo e(route("admin.penjualan.kontrabon.data")); ?>',
                            type: 'GET',
                            data: function(d) {
                                d.status = self.statusPiutang;
                                // Send outlet_ids as individual parameters for GET request
                                if (self.selectedOutlets && self.selectedOutlets.length > 0) {
                                    self.selectedOutlets.forEach(function(outletId, index) {
                                        d['outlet_ids[' + index + ']'] = outletId;
                                    });
                                }
                                console.log('Piutang AJAX data:', d);
                            },
                            error: function(xhr, error, thrown) {
                                console.error('Piutang AJAX error:', xhr.responseText);
                            }
                        },
                        columns: [
                            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                            {data: 'tanggal', name: 'tanggal'},
                            {data: 'no_nota', name: 'no_nota'},
                            {data: 'nama_member', name: 'nama_member'},
                            <?php if(count($outlets) > 1): ?>
                            {data: 'outlet', name: 'outlet'},
                            <?php endif; ?>
                            {data: 'total_formatted', name: 'total'},
                            {data: 'dibayar_formatted', name: 'dibayar'},
                            {data: 'sisa_formatted', name: 'sisa'},
                            {data: 'status_badge', name: 'status', orderable: false, searchable: false}
                        ],
                        order: [[1, 'desc']],
                        pageLength: 25,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                        },
                        drawCallback: function() {
                            // Re-bind checkbox handler after each draw
                            $('#select-all-piutang').off('change').on('change', function() {
                                $('.piutang-checkbox').prop('checked', this.checked);
                            });
                        }
                    });
                    
                    console.log('Piutang table initialized successfully');
                },

                initKontraBonTable() {
                    const self = this;
                    
                    tableKontraBon = $('#table-kontrabon').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: false, // Never destroy
                        ajax: {
                            url: '<?php echo e(route("admin.penjualan.kontrabon.data-kontrabon")); ?>',
                            type: 'GET',
                            data: function(d) {
                                // Send outlet_ids as individual parameters for GET request
                                if (self.selectedOutlets && self.selectedOutlets.length > 0) {
                                    self.selectedOutlets.forEach(function(outletId, index) {
                                        d['outlet_ids[' + index + ']'] = outletId;
                                    });
                                }
                                console.log('KontraBon AJAX data:', d);
                            },
                            error: function(xhr, error, thrown) {
                                console.error('KontraBon AJAX error:', xhr.responseText);
                            }
                        },
                        columns: [
                            {data: 'tanggal', name: 'tanggal'},
                            {data: 'no_kontra_bon', name: 'no_kontra_bon'},
                            {data: 'nama_member', name: 'nama_member'},
                            <?php if(count($outlets) > 1): ?>
                            {data: 'outlet', name: 'outlet'},
                            <?php endif; ?>
                            {data: 'total_formatted', name: 'total'},
                            {data: 'jatuh_tempo', name: 'jatuh_tempo'},
                            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                            {data: 'action', name: 'action', orderable: false, searchable: false}
                        ],
                        order: [[0, 'desc']],
                        pageLength: 25,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                        }
                    });
                    
                    console.log('KontraBon table initialized successfully');
                }
            }
        }

        function showDetail(id) {
            // Show loading state
            $('#modal-detail-body').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail...</p>
                </div>
            `);
            
            // Show modal
            $('#modal-detail').modal('show');
            
            // Load content
            $.get('<?php echo e(route("admin.penjualan.kontrabon.show", ":id")); ?>'.replace(':id', id))
                .done(function(data) {
                    $('#modal-detail-content').html(data);
                })
                .fail(function(xhr) {
                    let errorMessage = 'Gagal memuat detail kontra bon';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    $('#modal-detail-body').html(`
                        <div class="alert alert-danger text-center">
                            <i class="bx bx-error-circle bx-lg mb-2"></i><br>
                            <strong>Error!</strong><br>
                            ${errorMessage}
                        </div>
                    `);
                });
        }

        function showPrintModal(id) {
            // Redirect to print page
            window.open('<?php echo e(route("admin.penjualan.kontrabon.print", ":id")); ?>'.replace(':id', id), '_blank');
        }

        function lunasi(id) {
            if (confirm('Apakah Anda yakin ingin melunasi kontra bon ini? Semua piutang yang terlibat akan diubah statusnya menjadi lunas.')) {
                $.ajax({
                    url: '<?php echo e(route("admin.penjualan.kontrabon.lunasi", ":id")); ?>'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // Reload tables safely
                            if (tableKontraBon && $.fn.DataTable.isDataTable('#table-kontrabon')) {
                                tableKontraBon.ajax.reload();
                            }
                            if (tablePiutang && $.fn.DataTable.isDataTable('#table-piutang')) {
                                tablePiutang.ajax.reload();
                            }
                        } else {
                            alert('Gagal melunasi kontra bon: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat melunasi kontra bon';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            }
        }

        function deleteKontraBon(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kontra bon ini? Status piutang yang terkait akan dikembalikan ke belum lunas.')) {
                $.ajax({
                    url: '<?php echo e(route("admin.penjualan.kontrabon.destroy", ":id")); ?>'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // Reload tables safely
                            if (tableKontraBon && $.fn.DataTable.isDataTable('#table-kontrabon')) {
                                tableKontraBon.ajax.reload();
                            }
                            if (tablePiutang && $.fn.DataTable.isDataTable('#table-piutang')) {
                                tablePiutang.ajax.reload();
                            }
                        } else {
                            alert('Gagal menghapus kontra bon: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menghapus kontra bon';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            }
        }
    </script>
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\kontrabon\index.blade.php ENDPATH**/ ?>