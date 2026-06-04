<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Service / Mesin Customer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Service / Mesin Customer']); ?>
    <div x-data="mesinCrud()" x-init="init()" class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">Mesin Customer</h1>
                <p class="text-slate-600 text-sm">Kelola data mesin customer dan produk service.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(true): ?> 
                <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                    <i class='bx bx-plus-circle text-lg'></i> Tambah Mesin
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="grid grid-cols-1 gap-3">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                <!-- Search -->
                <div class="lg:col-span-6">
                    <div class="relative">
                        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                        <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari customer, kode mesin…" 
                               class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
                    </div>
                </div>
                <!-- Filter Outlet -->
                <div class="lg:col-span-6">
                    <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="ALL">Outlet: Semua</option>
                        <template x-for="o in outlets" :key="o.id">
                            <option :value="o.id" x-text="o.name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="inline-flex items-center gap-2 text-slate-600">
                <i class='bx bx-loader-alt bx-spin text-xl'></i>
                <span>Memuat data...</span>
            </div>
        </div>

        <!-- TABLE -->
        <div x-show="!loading">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="text-left px-4 py-3 w-12">No</th>
                            <th class="text-left px-4 py-3">Kode Mesin</th>
                            <th class="text-left px-4 py-3">Customer</th>
                            <th class="text-left px-4 py-3">Daerah & Ongkir</th>
                            <th class="text-left px-4 py-3">Produk & Harga</th>
                            <th class="text-left px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(m,i) in mesinList" :key="m.id">
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-3" x-text="i+1"></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded bg-primary-600 text-white text-xs" x-text="m.kode_mesin"></span>
                                </td>
                                <td class="px-4 py-3" x-text="m.customer_name"></td>
                                <td class="px-4 py-3">
                                    <div x-text="m.daerah"></div>
                                    <div class="text-xs text-green-600 font-medium" x-show="m.ongkir_harga" x-text="'Rp ' + (m.ongkir_harga ? new Intl.NumberFormat('id-ID').format(m.ongkir_harga) : '0')"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="p in m.produk" :key="p.id">
                                            <div class="inline-flex flex-col px-2 py-1 rounded bg-emerald-50 border border-emerald-200 text-xs">
                                                <div class="flex items-center gap-1 text-emerald-700 font-medium">
                                                    <span x-text="p.nama"></span>
                                                    <span x-text="'(x' + p.jumlah + ')'"></span>
                                                </div>
                                                <div class="text-[10px] text-emerald-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(p.biaya_service)"></div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button x-on:click="openEdit(m)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                                            <i class='bx bx-edit-alt'></i>
                                        </button>
                                        <button x-on:click="confirmDelete(m)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="mesinList.length===0">
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: Tambah/Edit -->
        <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
            <div x-on:click.outside="closeForm()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                    <div class="font-semibold truncate" x-text="form.id ? 'Edit Mesin Customer' : 'Tambah Mesin Customer'"></div>
                    <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>

                <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-slate-600">Customer <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   x-model="customerSearch" 
                                   x-on:input="handleCustomerInput()"
                                   x-on:change="selectCustomerFromList()"
                                   list="customerList"
                                   placeholder="Ketik nama atau telepon customer..." 
                                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                            <datalist id="customerList">
                                <template x-for="c in customerResults" :key="c.id">
                                    <option :value="c.text" :data-id="c.id"></option>
                                </template>
                            </datalist>
                            <div class="text-xs text-slate-500 mt-1">
                                <span x-show="!form.id_member">Ketik minimal 1 karakter untuk mencari</span>
                                <span x-show="form.id_member" class="text-green-600">✓ Customer terpilih</span>
                            </div>
                            <div x-show="errors.id_member" class="text-red-500 text-xs mt-1" x-text="errors.id_member"></div>
                        </div>

                        <div>
                            <label class="text-sm text-slate-600">Daerah Ongkir <span class="text-red-500">*</span></label>
                            <select x-model="form.id_ongkir" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                                <option value="">— Pilih Daerah —</option>
                                <template x-for="o in ongkirList" :key="o.id">
                                    <option :value="o.id" x-text="o.daerah + ' - Rp ' + o.harga"></option>
                                </template>
                            </select>
                            <div x-show="errors.id_ongkir" class="text-red-500 text-xs mt-1" x-text="errors.id_ongkir"></div>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm text-slate-600">Produk Service <span class="text-red-500">*</span></label>
                                <button type="button" x-on:click="addProdukRow()" class="inline-flex items-center gap-1 rounded-lg bg-green-600 text-white px-3 py-1.5 hover:bg-green-700 text-sm">
                                    <i class='bx bx-plus'></i> Tambah Produk
                                </button>
                            </div>
                            
                            <div class="space-y-2">
                                <template x-for="(p, index) in form.produk" :key="index">
                                    <div class="grid grid-cols-12 gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="col-span-4">
                                            <label class="text-xs text-slate-600 mb-1 block">Produk</label>
                                            <select x-model="p.id_produk" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-sm">
                                                <option value="">Pilih Produk</option>
                                                <template x-for="prod in produkList" :key="prod.id">
                                                    <option :value="prod.id" x-text="prod.nama"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-xs text-slate-600 mb-1 block">Jumlah</label>
                                            <input type="number" x-model="p.jumlah" min="1" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-sm">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-xs text-slate-600 mb-1 block">Biaya Service</label>
                                            <input type="number" x-model="p.biaya_service" min="0" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-sm">
                                            <p class="text-xs text-blue-600 mt-1" x-show="p.biaya_service > 0" 
                                               x-text="'≈ ' + formatCurrency(p.biaya_service)"></p>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-xs text-slate-600 mb-1 block">Tipe</label>
                                            <select x-model="p.closing_type" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-sm">
                                                <option value="jual_putus">Jual Putus</option>
                                                <option value="deposit">Deposit</option>
                                            </select>
                                        </div>
                                        <div class="col-span-1 flex items-end">
                                            <button type="button" x-on:click="removeProdukRow(index)" class="w-full px-2 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>
                    <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="saving" class="inline-flex items-center gap-2">
                            <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                        </span>
                        <span x-show="!saving">Simpan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
            <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
                <div class="px-5 py-4">
                    <div class="font-semibold">Hapus Mesin Customer?</div>
                    <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
                    <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm"><span class="font-medium" x-text="toDelete?.kode_mesin"></span></div>
                        <div class="text-xs text-slate-500 mt-1" x-text="'Customer: ' + (toDelete?.customer_name || '-')"></div>
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDelete=null">Batal</button>
                    <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
                        <span x-show="deleting" class="inline-flex items-center gap-2">
                            <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
                        </span>
                        <span x-show="!deleting">Hapus</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
            <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
                 class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
                <div class="flex items-center gap-2">
                    <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
                    <span x-text="toastMessage"></span>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function mesinCrud() {
            return {
                mesinList: [],
                outlets: [],
                ongkirList: [],
                produkList: [],
                customerResults: [],
                searchTimeout: null,
                loading: false,
                saving: false,
                deleting: false,
                
                search: '',
                outletFilter: 'ALL',
                
                showForm: false,
                form: {
                    id: null,
                    id_member: '',
                    id_ongkir: '',
                    produk: []
                },
                customerSearch: '',
                errors: {},
                
                toDelete: null,
                
                showToast: false,
                toastMessage: '',
                toastType: 'success',

                async init() {
                    await Promise.all([
                        this.fetchData(),
                        this.fetchOutlets(),
                        this.fetchOngkir(),
                        this.fetchProduk()
                    ]);
                },

                async fetchData() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            search: this.search,
                            outlet_id: this.outletFilter !== 'ALL' ? this.outletFilter : ''
                        });

                        const response = await fetch(`<?php echo e(route('admin.service.mesin.data')); ?>?${params}`);
                        const result = await response.json();
                        
                        this.mesinList = result.data.map(item => ({
                            id: item.id,
                            kode_mesin: item.kode_mesin,
                            customer_name: item.customer_name || item.member_name || '-',
                            daerah: item.daerah || item.ongkir_daerah || '-',
                            ongkir_harga: item.ongkir_harga || 0,
                            produk: item.produk || []
                        }));
                    } catch (error) {
                        console.error('Error fetching data:', error);
                        this.showToastMessage('Gagal memuat data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchOutlets() {
                    try {
                        const response = await fetch('<?php echo e(route("admin.inventaris.bahan.outlets")); ?>');
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        
                        const data = await response.json();
                        this.outlets = Object.entries(data).map(([id, name]) => ({ id, name }));
                    } catch (error) {
                        console.error('Error fetching outlets:', error);
                        // Fallback to default outlets if API fails
                        this.outlets = [
                            { id: '1', name: 'PBU' },
                            { id: '3', name: 'Dahana' }
                        ];
                    }
                },

                async fetchOngkir() {
                    try {
                        let outletId = this.outletFilter !== 'ALL' ? this.outletFilter : null;
                        
                        // Get outlet ID with fallback logic
                        if (!outletId) {
                            if (window.OutletHelper && typeof window.OutletHelper.getFirstOutletId === 'function') {
                                outletId = window.OutletHelper.getFirstOutletId(this.outlets);
                            } else {
                                outletId = this.outlets[0]?.id || this.outlets[0]?.id_outlet || '1';
                            }
                        }
                        
                        const response = await fetch(`<?php echo e(route('admin.service.ongkir.data')); ?>?outlet_id=${outletId}`);
                        const result = await response.json();
                        this.ongkirList = result.data.map(item => ({
                            id: item.id_ongkir,
                            daerah: item.daerah,
                            harga: new Intl.NumberFormat('id-ID').format(item.harga)
                        }));
                    } catch (error) {
                        console.error('Error fetching ongkir:', error);
                    }
                },

                async fetchProduk() {
                    try {
                        let outletId = this.outletFilter !== 'ALL' ? this.outletFilter : null;
                        
                        // Get outlet ID with fallback logic
                        if (!outletId) {
                            if (window.OutletHelper && typeof window.OutletHelper.getFirstOutletId === 'function') {
                                outletId = window.OutletHelper.getFirstOutletId(this.outlets);
                            } else {
                                outletId = this.outlets[0]?.id || this.outlets[0]?.id_outlet || '1';
                            }
                        }
                        
                        const response = await fetch(`<?php echo e(route('admin.service.mesin.produk')); ?>?outlet_id=${outletId}`);
                        const data = await response.json();
                        if (data.success) {
                            this.produkList = data.data;
                        }
                    } catch (error) {
                        console.error('Error fetching produk:', error);
                    }
                },

                handleCustomerInput() {
                    // Clear customer ID when user types
                    if (this.customerSearch.length < 1) {
                        this.form.id_member = '';
                        this.customerResults = [];
                        return;
                    }
                    
                    // Debounce search
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.searchCustomers();
                    }, 300);
                },

                async searchCustomers() {
                    try {
                        const response = await fetch(`<?php echo e(route('admin.service.search-customers')); ?>?q=${encodeURIComponent(this.customerSearch)}`);
                        const data = await response.json();
                        this.customerResults = data.results || [];
                    } catch (error) {
                        console.error('Error searching customers:', error);
                    }
                },

                selectCustomerFromList() {
                    // When user selects from datalist, find and set the customer ID
                    const selected = this.customerResults.find(c => c.text === this.customerSearch);
                    if (selected) {
                        this.form.id_member = selected.id;
                    } else {
                        // If not found in results, clear the ID
                        this.form.id_member = '';
                    }
                },

                openCreate() {
                    this.form = {
                        id: null,
                        id_member: '',
                        id_ongkir: '',
                        produk: [{
                            id_produk: '',
                            jumlah: 1,
                            biaya_service: 0,
                            closing_type: 'jual_putus'
                        }]
                    };
                    this.customerSearch = '';
                    this.errors = {};
                    this.showForm = true;
                },

                async openEdit(item) {
                    try {
                        const response = await fetch(`<?php echo e(route('admin.service.mesin.index')); ?>/${item.id}`);
                        const data = await response.json();
                        
                        this.form = {
                            id: data.id,
                            id_member: data.id_member,
                            id_ongkir: data.id_ongkir,
                            produk: data.produk.map(p => ({
                                id_produk: p.id_produk,
                                jumlah: p.pivot.jumlah,
                                biaya_service: p.pivot.biaya_service,
                                closing_type: p.pivot.closing_type
                            }))
                        };
                        this.customerSearch = data.member.nama;
                        this.errors = {};
                        this.showForm = true;
                    } catch (error) {
                        console.error('Error loading mesin:', error);
                        this.showToastMessage('Gagal memuat data', 'error');
                    }
                },

                closeForm() {
                    this.showForm = false;
                    this.errors = {};
                },

                addProdukRow() {
                    this.form.produk.push({
                        id_produk: '',
                        jumlah: 1,
                        biaya_service: 0,
                        closing_type: 'jual_putus'
                    });
                },

                removeProdukRow(index) {
                    if (this.form.produk.length > 1) {
                        this.form.produk.splice(index, 1);
                    } else {
                        this.showToastMessage('Minimal harus ada 1 produk', 'error');
                    }
                },

                async submitForm() {
                    // Validate customer selection
                    if (!this.form.id_member || !this.customerSearch) {
                        this.showToastMessage('Pilih customer terlebih dahulu dari daftar', 'error');
                        return;
                    }

                    if (this.form.produk.length === 0) {
                        this.showToastMessage('Minimal harus ada 1 produk', 'error');
                        return;
                    }

                    this.saving = true;
                    this.errors = {};

                    try {
                        const url = this.form.id 
                            ? `<?php echo e(route('admin.service.mesin.index')); ?>/${this.form.id}`
                            : '<?php echo e(route('admin.service.mesin.store')); ?>';
                        
                        const method = this.form.id ? 'PUT' : 'POST';

                        const formData = {
                            id_member: this.form.id_member,
                            id_ongkir: this.form.id_ongkir,
                            produk: this.form.produk.map(p => p.id_produk),
                            jumlah_produk: this.form.produk.map(p => p.jumlah),
                            biaya_service_produk: this.form.produk.map(p => p.biaya_service),
                            closing_type_produk: this.form.produk.map(p => p.closing_type)
                        };

                        console.log('Form data to submit:', formData);
                        console.log('Form produk array:', this.form.produk);

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify(formData)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
                            this.closeForm();
                            await this.fetchData();
                        } else {
                            if (result.errors) {
                                this.errors = result.errors;
                            }
                            this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
                        }
                    } catch (error) {
                        console.error('Error saving data:', error);
                        this.showToastMessage('Gagal menyimpan data', 'error');
                    } finally {
                        this.saving = false;
                    }
                },

                confirmDelete(item) {
                    this.toDelete = item;
                },

                async deleteNow() {
                    if (!this.toDelete) return;
                    
                    this.deleting = true;
                    try {
                        const response = await fetch(`<?php echo e(route('admin.service.mesin.index')); ?>/${this.toDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            }
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
                            this.toDelete = null;
                            await this.fetchData();
                        } else {
                            this.showToastMessage(result.message || 'Gagal menghapus data', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting data:', error);
                        this.showToastMessage('Gagal menghapus data', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                showToastMessage(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                }
            };
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
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\service\mesin\index-test.blade.php ENDPATH**/ ?>