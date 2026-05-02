<x-layouts.admin title="Permintaan Barang">
    <div x-data="permintaanBarangApp()" 
         x-init="init()" 
         @close-detail-modal="closeDetailModal()"
         @close-edit-modal="closeEditModal()"
         @close-approval-modal="closeApprovalModal()"
         @close-reject-modal="closeRejectModal()"
         @open-edit-modal="openEditModalFromDetail($event.detail)"
         @open-approval-modal="openApprovalModalFromDetail($event.detail)"
         @open-reject-modal="openRejectModalFromDetail($event.detail)"
         @refresh-data="refreshData()"
         @show-notification="handleNotification($event.detail)"
         class="space-y-6">
        
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Permintaan Barang</h1>
                <p class="text-slate-600 mt-1">Kelola permintaan barang untuk kebutuhan operasional</p>
            </div>
            
             @hasPermission('supply-chain.permintaan-barang.create')
            <div class="flex items-center gap-3">
                <button @click="showCreateModal = true" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class='bx bx-plus text-lg'></i>
                    <span>Buat Permintaan</span>
                </button>
            </div>
             @endhasPermission
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Total</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="stats.total || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-list-ul text-slate-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Draft</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.draft || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-edit text-gray-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Aktif</p>
                        <p class="text-2xl font-bold text-blue-900" x-text="stats.aktif || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-time text-blue-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Disetujui</p>
                        <p class="text-2xl font-bold text-green-900" x-text="stats.disetujui || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-check text-green-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Ditolak</p>
                        <p class="text-2xl font-bold text-red-900" x-text="stats.ditolak || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-x text-red-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Urgent</p>
                        <p class="text-2xl font-bold text-orange-900" x-text="stats.urgent || 0"></p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-error text-orange-600 text-lg'></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Total Budget</p>
                        <p class="text-lg font-bold text-slate-900" x-text="formatCurrency(stats.total_budget || 0)"></p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-money text-purple-600 text-lg'></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Controls --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               x-model="filters.search" 
                               @input="debounceSearch()"
                               placeholder="Cari nomor permintaan, judul, atau pemohon..."
                               class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400'></i>
                    </div>
                </div>
                
                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select x-model="filters.status" @change="loadData()" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="aktif">Aktif</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    
                    <select x-model="filters.prioritas" @change="loadData()" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Prioritas</option>
                        <option value="rendah">Rendah</option>
                        <option value="normal">Normal</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="urgent">Urgent</option>
                    </select>
                    
                    <select x-model="filters.outlet_id" @change="loadData()" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Outlet</option>
                        <template x-for="outlet in outlets" :key="outlet.id">
                            <option :value="outlet.id" x-text="outlet.nama"></option>
                        </template>
                    </select>
                    
                    {{-- View Toggle --}}
                    <div class="flex items-center bg-slate-100 rounded-lg p-1">
                        <button @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                                class="px-3 py-1 rounded text-sm transition-all">
                            <i class='bx bx-grid-alt'></i>
                        </button>
                        <button @click="viewMode = 'table'" 
                                :class="viewMode === 'table' ? 'bg-white shadow-sm' : ''"
                                class="px-3 py-1 rounded text-sm transition-all">
                            <i class='bx bx-list-ul'></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div x-show="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary-500 border-t-transparent"></div>
        </div>

        {{-- Grid View --}}
        <div x-show="viewMode === 'grid' && !loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="item in data" :key="item.id">
                <div class="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition-shadow">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-slate-900" x-text="item.nomor_permintaan"></h3>
                            <p class="text-sm text-slate-600 mt-1" x-text="item.judul"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="getStatusBadge(item.status)" 
                                  class="px-2 py-1 rounded-full text-xs font-medium" 
                                  x-text="item.status"></span>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class='bx bx-store text-primary-500'></i>
                            <span x-text="item.outlet?.nama || '-'"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class='bx bx-user text-primary-500'></i>
                            <span x-text="item.user?.name || '-'"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class='bx bx-flag text-primary-500'></i>
                            <span :class="getPrioritasBadge(item.prioritas)" 
                                  class="px-2 py-1 rounded-full text-xs font-medium" 
                                  x-text="item.prioritas"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class='bx bx-money text-primary-500'></i>
                            <span x-text="formatCurrency(item.estimasi_budget || 0)"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class='bx bx-package text-primary-500'></i>
                            <span x-text="(item.items?.length || 0) + ' item'"></span>
                        </div>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                        <span class="text-xs text-slate-500" x-text="formatDate(item.created_at)"></span>
                        <div class="flex items-center gap-2">
                            <button @click="showDetail(item)" 
                                    class="p-2 text-slate-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                    title="Detail">
                                <i class='bx bx-show text-lg'></i>
                            </button>
                            
                             @hasPermission('supply-chain.permintaan-barang.update')
                            <button x-show="canEdit(item)" 
                                    @click="editItem(item)" 
                                    class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Edit">
                                <i class='bx bx-edit text-lg'></i>
                            </button>
                             @endhasPermission
                            
                            @hasPermission('supply-chain.permintaan-barang.approve')
                            <button x-show="canApprove(item)" 
                                    @click="openApprovalModal(item)" 
                                    class="p-2 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                    title="Setujui">
                                <i class='bx bx-check text-lg'></i>
                            </button>
                            
                            <button x-show="canApprove(item)" 
                                    @click="openRejectModal(item)" 
                                    class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Tolak">
                                <i class='bx bx-x text-lg'></i>
                            </button>
                             @endhasPermission
                            
                            <button @click="generatePdf(item.id)" 
                                    class="p-2 text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                    title="Download PDF">
                                <i class='bx bx-download text-lg'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Table View --}}
        <div x-show="viewMode === 'table' && !loading" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th @click="sort('nomor_permintaan')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>No. Permintaan</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('judul')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Judul</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('outlet')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Outlet</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('user')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Pemohon</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('prioritas')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Prioritas</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('status')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Status</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('estimasi_budget')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Budget</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th @click="sort('created_at')" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100">
                                <div class="flex items-center gap-2">
                                    <span>Tanggal</span>
                                    <i class='bx bx-sort text-slate-400'></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <template x-for="item in data" :key="item.id">
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900" x-text="item.nomor_permintaan"></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-900" x-text="item.judul"></div>
                                    <div class="text-sm text-slate-500" x-text="truncateText(item.deskripsi, 50)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900" x-text="item.outlet?.nama || '-'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900" x-text="item.user?.name || '-'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPrioritasBadge(item.prioritas)" 
                                          class="px-2 py-1 rounded-full text-xs font-medium" 
                                          x-text="item.prioritas"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadge(item.status)" 
                                          class="px-2 py-1 rounded-full text-xs font-medium" 
                                          x-text="item.status"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900" x-text="formatCurrency(item.estimasi_budget || 0)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900" x-text="formatDate(item.created_at)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="showDetail(item)" 
                                                class="p-1 text-slate-600 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                                title="Detail">
                                            <i class='bx bx-show'></i>
                                        </button>
                                        
                                         @hasPermission('supply-chain.permintaan-barang.update')
                                        <button x-show="canEdit(item)" 
                                                @click="editItem(item)" 
                                                class="p-1 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                                title="Edit">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                         @endhasPermission
                                        
                                         @hasPermission('supply-chain.permintaan-barang.approve')
                                        <button x-show="canApprove(item)" 
                                                @click="openApprovalModal(item)" 
                                                class="p-1 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors"
                                                title="Setujui">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        
                                        <button x-show="canApprove(item)" 
                                                @click="openRejectModal(item)" 
                                                class="p-1 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                                title="Tolak">
                                            <i class='bx bx-x'></i>
                                        </button>
                                         @endhasPermission
                                        
                                        <button @click="generatePdf(item.id)" 
                                                class="p-1 text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded transition-colors"
                                                title="Download PDF">
                                            <i class='bx bx-download'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && pagination.last_page > 1" class="bg-white rounded-xl border border-slate-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-600">
                    Menampilkan <span x-text="((pagination.current_page - 1) * pagination.per_page) + 1"></span> 
                    sampai <span x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span> 
                    dari <span x-text="pagination.total"></span> data
                </div>
                <div class="flex items-center gap-2">
                    <button @click="changePage(pagination.current_page - 1)" 
                            :disabled="pagination.current_page <= 1"
                            :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                            class="px-3 py-2 border border-slate-300 rounded-lg transition-colors">
                        <i class='bx bx-chevron-left'></i>
                    </button>
                    
                    <template x-for="page in getVisiblePages()" :key="page">
                        <button @click="changePage(page)" 
                                :class="page === pagination.current_page ? 'bg-primary-600 text-white' : 'hover:bg-slate-100'"
                                class="px-3 py-2 border border-slate-300 rounded-lg transition-colors"
                                x-text="page"></button>
                    </template>
                    
                    <button @click="changePage(pagination.current_page + 1)" 
                            :disabled="pagination.current_page >= pagination.last_page"
                            :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-100'"
                            class="px-3 py-2 border border-slate-300 rounded-lg transition-colors">
                        <i class='bx bx-chevron-right'></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && data.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-package text-slate-400 text-2xl'></i>
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-2">Belum ada permintaan barang</h3>
            <p class="text-slate-600 mb-6">Mulai dengan membuat permintaan barang pertama Anda</p>
            @can('supply-chain.permintaan-barang.create')
            <button @click="showCreateModal = true" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class='bx bx-plus'></i>
                <span>Buat Permintaan</span>
            </button>
            @endcan
        </div>

        {{-- Modals --}}
        @include('admin.supply-chain.permintaan-barang.modals.create')
        @include('admin.supply-chain.permintaan-barang.modals.detail')
        @include('admin.supply-chain.permintaan-barang.modals.edit')
        @include('admin.supply-chain.permintaan-barang.modals.approval')
        @include('admin.supply-chain.permintaan-barang.modals.reject')
    </div>

    @push('scripts')
    <script>
        // Initialize Alpine store for permintaan barang
        document.addEventListener('alpine:init', () => {
            Alpine.store('permintaanBarang', {
                showDetailModal: false,
                showEditModal: false,
                showApprovalModal: false,
                showRejectModal: false,
                selectedItem: null
            });
        });

        function permintaanBarangApp() {
            return {
                // Data
                data: [],
                stats: {},
                outlets: [],
                loading: false,
                viewMode: 'grid',
                
                // Pagination
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 15,
                    total: 0
                },
                
                // Filters
                filters: {
                    search: '',
                    status: '',
                    prioritas: '',
                    outlet_id: ''
                },
                
                // Sorting
                sortBy: 'created_at',
                sortDir: 'desc',
                
                // Modals
                showCreateModal: false,
                showDetailModal: false,
                showEditModal: false,
                showApprovalModal: false,
                showRejectModal: false,
                selectedItem: null,
                
                // Search debounce
                searchTimeout: null,

                // Initialize
                async init() {
                    await this.loadOutlets();
                    await this.loadStats();
                    await this.loadData();
                },

                // Load data
                async loadData(page = 1) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            page: page,
                            per_page: this.pagination.per_page,
                            search: this.filters.search,
                            status: this.filters.status,
                            prioritas: this.filters.prioritas,
                            outlet_id: this.filters.outlet_id,
                            sort_by: this.sortBy,
                            sort_dir: this.sortDir
                        });

                        const response = await fetch(`{{ route('admin.supply-chain.permintaan-barang.data') }}?${params}`);
                        const result = await response.json();
                        
                        this.data = result.data;
                        this.pagination = result.pagination;
                    } catch (error) {
                        console.error('Error loading data:', error);
                        this.showNotification('Gagal memuat data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Load statistics
                async loadStats() {
                    try {
                        const response = await fetch('{{ route('admin.supply-chain.permintaan-barang.stats') }}');
                        this.stats = await response.json();
                    } catch (error) {
                        console.error('Error loading stats:', error);
                    }
                },

                // Load outlets
                async loadOutlets() {
                    try {
                        const response = await fetch('{{ route('admin.supply-chain.permintaan-barang.outlets') }}');
                        this.outlets = await response.json();
                    } catch (error) {
                        console.error('Error loading outlets:', error);
                    }
                },

                // Search with debounce
                debounceSearch() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.loadData();
                    }, 500);
                },

                // Sorting
                sort(column) {
                    if (this.sortBy === column) {
                        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortBy = column;
                        this.sortDir = 'asc';
                    }
                    this.loadData();
                },

                // Pagination
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.loadData(page);
                    }
                },

                getVisiblePages() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const pages = [];
                    
                    let start = Math.max(1, current - 2);
                    let end = Math.min(last, current + 2);
                    
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    
                    return pages;
                },

                // Actions
                showDetail(item) {
                    console.log('Opening detail modal for item:', item);
                    this.selectedItem = item;
                    this.showDetailModal = true;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showDetailModal = true;
                },

                editItem(item) {
                    console.log('Opening edit modal for item:', item);
                    this.selectedItem = item;
                    this.showEditModal = true;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showEditModal = true;
                },

                openApprovalModal(item) {
                    this.selectedItem = item;
                    this.showApprovalModal = true;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showApprovalModal = true;
                    // Dispatch event to modal
                    this.$nextTick(() => {
                        this.$dispatch('modal-opened', item);
                    });
                },

                openRejectModal(item) {
                    this.selectedItem = item;
                    this.showRejectModal = true;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showRejectModal = true;
                    // Dispatch event to modal
                    this.$nextTick(() => {
                        this.$dispatch('modal-opened', item);
                    });
                },

                // Close modal functions
                closeModal() {
                    this.showCreateModal = false;
                    this.showDetailModal = false;
                    this.showEditModal = false;
                    this.showApprovalModal = false;
                    this.showRejectModal = false;
                    // Update store
                    this.$store.permintaanBarang.showDetailModal = false;
                    this.$store.permintaanBarang.showEditModal = false;
                    this.$store.permintaanBarang.showApprovalModal = false;
                    this.$store.permintaanBarang.showRejectModal = false;
                },

                closeCreateModal() {
                    this.showCreateModal = false;
                },

                closeDetailModal() {
                    this.showDetailModal = false;
                    this.$store.permintaanBarang.showDetailModal = false;
                },

                closeEditModal() {
                    this.showEditModal = false;
                    this.$store.permintaanBarang.showEditModal = false;
                },

                closeApprovalModal() {
                    this.showApprovalModal = false;
                    this.$store.permintaanBarang.showApprovalModal = false;
                },

                closeRejectModal() {
                    this.showRejectModal = false;
                    this.$store.permintaanBarang.showRejectModal = false;
                },

                // New event handlers for modal communication
                openEditModalFromDetail(item) {
                    this.selectedItem = item;
                    this.showEditModal = true;
                    this.showDetailModal = false;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showEditModal = true;
                    this.$store.permintaanBarang.showDetailModal = false;
                },

                openApprovalModalFromDetail(item) {
                    this.selectedItem = item;
                    this.showApprovalModal = true;
                    this.showDetailModal = false;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showApprovalModal = true;
                    this.$store.permintaanBarang.showDetailModal = false;
                    this.$nextTick(() => {
                        this.$dispatch('modal-opened', item);
                    });
                },

                openRejectModalFromDetail(item) {
                    this.selectedItem = item;
                    this.showRejectModal = true;
                    this.showDetailModal = false;
                    // Update store
                    this.$store.permintaanBarang.selectedItem = item;
                    this.$store.permintaanBarang.showRejectModal = true;
                    this.$store.permintaanBarang.showDetailModal = false;
                    this.$nextTick(() => {
                        this.$dispatch('modal-opened', item);
                    });
                },

                refreshData() {
                    this.loadData();
                    this.loadStats();
                },

                handleNotification(data) {
                    this.showNotification(data.message, data.type);
                },

                generatePdf(id) {
                    window.open(`{{ route('admin.supply-chain.permintaan-barang.pdf', ':id') }}`.replace(':id', id), '_blank');
                },

                // Permissions
                canEdit(item) {
                    return ['draft', 'aktif'].includes(item.status);
                },

                canApprove(item) {
                    return item.status === 'aktif';
                },

                // Utility functions
                getStatusBadge(status) {
                    const badges = {
                        'draft': 'bg-gray-100 text-gray-800',
                        'aktif': 'bg-blue-100 text-blue-800',
                        'disetujui': 'bg-green-100 text-green-800',
                        'ditolak': 'bg-red-100 text-red-800'
                    };
                    return badges[status] || 'bg-gray-100 text-gray-800';
                },

                getPrioritasBadge(prioritas) {
                    const badges = {
                        'rendah': 'bg-gray-100 text-gray-800',
                        'normal': 'bg-blue-100 text-blue-800',
                        'tinggi': 'bg-yellow-100 text-yellow-800',
                        'urgent': 'bg-red-100 text-red-800'
                    };
                    return badges[prioritas] || 'bg-gray-100 text-gray-800';
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                truncateText(text, length) {
                    if (!text) return '-';
                    return text.length > length ? text.substring(0, length) + '...' : text;
                },

                showNotification(message, type = 'success') {
                    // Implementation depends on your notification system
                    console.log(`${type}: ${message}`);
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>