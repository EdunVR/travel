<x-layouts.admin>
    <x-slot name="title">Service Management</x-slot>

    <div x-data="serviceDashboard()" x-init="init()" class="container px-6 py-8 mx-auto">
        <!-- Header & Filter -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Service Management</h1>
                    <p class="mt-2 text-gray-600">Kelola invoice service, history, ongkir, dan mesin customer</p>
                </div>

                <div class="w-full lg:w-auto">
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
                    <div class="relative">
                        <button @click="showOutletDropdown = !showOutletDropdown" 
                                class="h-10 w-full lg:w-64 rounded-xl border border-slate-200 px-3 text-left flex items-center justify-between bg-white hover:border-slate-300">
                            <span class="text-sm" x-text="getSelectedOutletText()"></span>
                            <i class='bx bx-chevron-down text-slate-400'></i>
                        </button>
                        
                        <div x-show="showOutletDropdown" 
                             @click.away="closeOutletDropdown()"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                          
                          <div class="p-2 border-b border-slate-100">
                            <div class="flex gap-2">
                              <button @click="selectAllOutlets()" 
                                      class="flex-1 px-3 py-1.5 text-xs bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100">
                                Pilih Semua
                              </button>
                              <button @click="clearAllOutlets()" 
                                      class="flex-1 px-3 py-1.5 text-xs bg-slate-50 text-slate-700 rounded-lg hover:bg-slate-100">
                                Hapus Semua
                              </button>
                            </div>
                          </div>
                          
                          <div class="p-1">
                            <template x-for="outlet in outlets" :key="outlet.id_outlet">
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                                  <input type="checkbox" 
                                         x-model="selectedOutlets" 
                                         :value="outlet.id_outlet"
                                         @change="loadData()"
                                         class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                                  <span class="text-sm text-slate-700" x-text="outlet.nama_outlet"></span>
                                </label>
                            </template>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
            <!-- Invoice Menunggu -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Invoice Menunggu</p>
                        <p class="mt-2 text-3xl font-bold text-yellow-600" x-text="counts.menunggu || 0"></p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="text-2xl text-yellow-600 fas fa-clock"></i>
                    </div>
                </div>
                <a :href="getHistoryUrl('menunggu')" class="inline-block mt-4 text-sm text-yellow-600 hover:text-yellow-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Invoice Lunas -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Invoice Lunas</p>
                        <p class="mt-2 text-3xl font-bold text-green-600" x-text="counts.lunas || 0"></p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="text-2xl text-green-600 fas fa-check-circle"></i>
                    </div>
                </div>
                <a :href="getHistoryUrl('lunas')" class="inline-block mt-4 text-sm text-green-600 hover:text-green-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Service Berikutnya -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Service Berikutnya</p>
                        <p class="mt-2 text-3xl font-bold text-blue-600" x-text="counts.service_berikutnya || 0"></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="text-2xl text-blue-600 fas fa-calendar-alt"></i>
                    </div>
                </div>
                <a :href="getHistoryUrl('service-berikutnya')" class="inline-block mt-4 text-sm text-blue-600 hover:text-blue-700">
                    Lihat Detail →
                </a>
            </div>

            <!-- Total Invoice -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Invoice</p>
                        <p class="mt-2 text-3xl font-bold text-gray-800" x-text="getTotalCount()"></p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="text-2xl text-gray-600 fas fa-file-invoice"></i>
                    </div>
                </div>
                <a :href="getHistoryUrl('terkini')" class="inline-block mt-4 text-sm text-gray-600 hover:text-gray-700">
                    Lihat Detail →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="p-6 mb-8 bg-white rounded-lg shadow-md">
            <h2 class="mb-4 text-xl font-semibold text-gray-800">Quick Actions</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <a :href="getInvoiceUrl()" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-blue-100 rounded-lg">
                        <i class="text-xl text-blue-600 fas fa-plus"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Buat Invoice</p>
                        <p class="text-sm text-gray-600">Invoice baru</p>
                    </div>
                </a>

                <a :href="getHistoryUrl('terkini')" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-green-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-green-100 rounded-lg">
                        <i class="text-xl text-green-600 fas fa-history"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">History</p>
                        <p class="text-sm text-gray-600">Riwayat invoice</p>
                    </div>
                </a>

                <a :href="getOngkirUrl()" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-yellow-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-truck"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Ongkir</p>
                        <p class="text-sm text-gray-600">Kelola ongkir</p>
                    </div>
                </a>

                <a :href="getMesinUrl()" class="flex items-center p-4 transition border border-gray-200 rounded-lg hover:border-purple-500 hover:shadow-md">
                    <div class="flex items-center justify-center w-12 h-12 mr-4 bg-purple-100 rounded-lg">
                        <i class="text-xl text-purple-600 fas fa-cog"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Mesin Customer</p>
                        <p class="text-sm text-gray-600">Kelola mesin</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="p-6 bg-blue-50 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="text-2xl text-blue-600 fas fa-info-circle"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900">Tentang Service Management</h3>
                    <p class="mt-2 text-blue-800">
                        Modul Service Management membantu Anda mengelola invoice service, riwayat service, tarif ongkos kirim, dan data mesin customer. 
                        Semua data terintegrasi dengan sistem outlet untuk memudahkan pengelolaan per cabang.
                    </p>
                    <div class="mt-4">
                        <h4 class="font-semibold text-blue-900">Fitur Utama:</h4>
                        <ul class="mt-2 space-y-1 text-blue-800 list-disc list-inside">
                            <li>Buat invoice service dengan multiple items</li>
                            <li>Kelola riwayat invoice dengan filter status</li>
                            <li>Atur tarif ongkos kirim per daerah</li>
                            <li>Kelola data mesin customer dan produk</li>
                            <li>Print invoice PDF</li>
                            <li>Jadwalkan service berikutnya</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine Component --}}
    <script>
        function serviceDashboard() {
            return {
                showOutletDropdown: false,
                outlets: [],
                selectedOutlets: [],
                counts: {
                    menunggu: 0,
                    lunas: 0,
                    gagal: 0,
                    service_berikutnya: 0
                },
                isLoading: false,

                async init() {
                    await this.loadOutlets();
                    await this.loadData();
                },

                async loadOutlets() {
                    try {
                        // Use outlets data passed from controller instead of AJAX
                        const outletsData = @json($outlets ?? []);
                        console.log('Loading outlets from controller:', outletsData);
                        
                        if (outletsData && outletsData.length > 0) {
                            this.outlets = outletsData;
                            // Initialize with first outlet selected
                            this.selectedOutlets = [this.outlets[0].id_outlet];
                            console.log('Outlets loaded successfully from controller:', this.outlets.length);
                            return;
                        }
                        
                        // Fallback to API if no data from controller
                        console.log('No outlets from controller, trying API...');
                        const apiUrl = '{{ url("/api/outlets") }}';
                        console.log('Loading outlets from API:', apiUrl);
                        
                        const response = await fetch(apiUrl, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            credentials: 'same-origin'
                        });
                        
                        console.log('API Response status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        const result = await response.json();
                        console.log('API Response:', result);
                        
                        if (result.success && result.data) {
                            this.outlets = result.data;
                            // Initialize with first outlet selected
                            if (this.outlets.length > 0) {
                                this.selectedOutlets = [this.outlets[0].id_outlet];
                            }
                            console.log('Outlets loaded successfully from API:', this.outlets.length);
                        } else {
                            throw new Error('Invalid API response format');
                        }
                    } catch (error) {
                        console.error('Error loading outlets:', error);
                        console.log('Using fallback outlets due to error');
                        
                        // Enhanced fallback outlets with more realistic data
                        this.outlets = [
                            { id_outlet: 1, nama_outlet: 'Outlet Utama' },
                            { id_outlet: 2, nama_outlet: 'Outlet Cabang' },
                            { id_outlet: 3, nama_outlet: 'Outlet Pusat' }
                        ];
                        this.selectedOutlets = [1];
                        console.log('Fallback outlets loaded:', this.outlets.length);
                    }
                },

                async loadData() {
                    if (this.selectedOutlets.length === 0) return;
                    
                    this.isLoading = true;
                    try {
                        const params = new URLSearchParams();
                        
                        // Add multiple outlet IDs
                        this.selectedOutlets.forEach(outletId => {
                            params.append('outlet_ids[]', outletId);
                        });

                        const response = await fetch(`{{ route('admin.service.status-counts') }}?${params}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();
                        this.counts = result;
                        
                    } catch (error) {
                        console.error('Error loading service data:', error);
                        this.counts = {
                            menunggu: 0,
                            lunas: 0,
                            gagal: 0,
                            service_berikutnya: 0
                        };
                    } finally {
                        this.isLoading = false;
                    }
                },

                getSelectedOutletText() {
                    if (this.selectedOutlets.length === 0) {
                        return 'Pilih Outlet';
                    } else if (this.selectedOutlets.length === 1) {
                        const outlet = this.outlets.find(o => o.id_outlet == this.selectedOutlets[0]);
                        return outlet ? outlet.nama_outlet : 'Unknown';
                    } else if (this.selectedOutlets.length === this.outlets.length) {
                        return 'Semua Outlet';
                    } else {
                        return `${this.selectedOutlets.length} Outlet Dipilih`;
                    }
                },

                selectAllOutlets() {
                    this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
                    this.loadData();
                },

                clearAllOutlets() {
                    this.selectedOutlets = [];
                    this.loadData();
                },

                getTotalCount() {
                    return (this.counts.menunggu || 0) + (this.counts.lunas || 0) + (this.counts.gagal || 0);
                },

                getOutletParams() {
                    const params = new URLSearchParams();
                    this.selectedOutlets.forEach(outletId => {
                        params.append('outlet_ids[]', outletId);
                    });
                    return params.toString();
                },

                getHistoryUrl(status) {
                    const baseUrl = '{{ route("admin.service.history.index") }}';
                    const params = this.getOutletParams();
                    const statusParam = status ? `status=${status}` : '';
                    const separator = params && statusParam ? '&' : '';
                    return `${baseUrl}?${params}${separator}${statusParam}`;
                },

                getInvoiceUrl() {
                    const baseUrl = '{{ route("admin.service.invoice.index") }}';
                    const params = this.getOutletParams();
                    return params ? `${baseUrl}?${params}` : baseUrl;
                },

                getOngkirUrl() {
                    const baseUrl = '{{ route("admin.service.ongkir.index") }}';
                    const params = this.getOutletParams();
                    return params ? `${baseUrl}?${params}` : baseUrl;
                },

                getMesinUrl() {
                    const baseUrl = '{{ route("admin.service.mesin.index") }}';
                    const params = this.getOutletParams();
                    return params ? `${baseUrl}?${params}` : baseUrl;
                }
            }
        }
    </script>
</x-layouts.admin>
