<x-layouts.admin :title="'Tipe & Diskon Customer'">
  {{-- Load Axios --}}
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  
  <div x-data="customerTypeManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Tipe & Diskon Customer</h1>
        <p class="text-slate-600 text-sm">Kelola tipe pelanggan dan pengaturan diskon</p>
      </div>

      <div class="flex flex-wrap gap-2">
        @hasPermission('crm.tipe.create')
        <button @click="openCreateModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
          <i class='bx bx-plus'></i> Tambah Tipe
        </button>
        @endhasPermission
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-category text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_types">0</div>
            <div class="text-sm text-slate-600">Total Tipe</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-user text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_members">0</div>
            <div class="text-sm text-slate-600">Total Pelanggan</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-store text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold text-green-600">{{ $outlets->count() }}</div>
            <div class="text-sm text-slate-600">Outlet Aktif</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        {{-- Checkbox Outlet Filter --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Filter Outlet</label>
          <div class="relative">
            <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 flex items-center justify-between bg-white">
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
                 class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
              <div class="p-3 border-b border-slate-100">
                <button x-on:click="selectAllOutlets()" class="text-xs text-primary-600 hover:text-primary-800 mr-3">Pilih Semua</button>
                <button x-on:click="clearAllOutlets()" class="text-xs text-slate-600 hover:text-slate-800">Hapus Semua</button>
              </div>
              <div class="p-2">
                @foreach($outlets as $outlet)
                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                  <input type="checkbox" value="{{ $outlet->id_outlet }}" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                         class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                  <span class="text-sm">{{ $outlet->nama_outlet }}</span>
                </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        {{-- Search --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Cari Tipe</label>
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input type="text" 
                   x-model="filters.search" 
                   @input.debounce.500ms="loadData()" 
                   placeholder="Cari nama tipe atau keterangan..."
                   class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
          </div>
        </div>
      </div>
    </div>

    {{-- Grid View --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <template x-for="type in types" :key="type.id_tipe">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <h3 class="font-semibold text-lg mb-1" x-text="type.nama_tipe"></h3>
              <p class="text-sm text-slate-600 line-clamp-2" x-text="type.keterangan || 'Tidak ada keterangan'"></p>
            </div>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
              <i class='bx bx-user text-sm mr-1'></i>
              <span x-text="type.member_count"></span>
            </span>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-store text-slate-400'></i>
              <span x-text="type.outlet_name"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-calendar text-slate-400'></i>
              <span x-text="type.created_at"></span>
            </div>
          </div>

          <div class="space-y-2 pt-3 border-t border-slate-100">
            <button @click="openProductModal(type.id_tipe, type.nama_tipe)" 
                    class="w-full px-3 py-1.5 text-sm rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 flex items-center justify-center gap-1">
              <i class='bx bx-package'></i> Kelola Produk Diskon
              <span x-show="type.produk_count > 0" class="ml-1 px-1.5 py-0.5 bg-blue-100 rounded-full text-xs" x-text="'(' + type.produk_count + ')'"></span>
            </button>
            <div class="flex gap-2">
              <button @click="editType(type.id_tipe)" 
                      class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50 flex items-center justify-center gap-1">
                <i class='bx bx-edit'></i> Edit
              </button>
              <button @click="deleteType(type.id_tipe)" 
                      class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                <i class='bx bx-trash'></i>
              </button>
            </div>
          </div>
        </div>
      </template>

      <div x-show="types.length === 0" class="col-span-full text-center py-12 text-slate-500">
        <i class='bx bx-category text-5xl mb-2'></i>
        <p>Belum ada tipe customer</p>
      </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-20">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full p-6 z-10 my-4">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900" x-text="modalTitle">Tambah Tipe</h3>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <form @submit.prevent="submitForm()">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
                <select x-model="formData.id_outlet"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                  <option value="">Semua Outlet</option>
                  @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                  @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">Kosongkan jika tipe berlaku untuk semua outlet</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Tipe *</label>
                <input type="text" x-model="formData.nama_tipe" required
                       placeholder="Contoh: Member Gold, Member Silver, Reseller"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan</label>
                <textarea x-model="formData.keterangan" rows="3"
                          placeholder="Deskripsi tipe customer, benefit, atau catatan lainnya..."
                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button type="button" @click="closeModal()" 
                      class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="loading"
                      class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!loading">Simpan</span>
                <span x-show="loading">Menyimpan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Product Modal --}}
    <div x-show="showProductModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-10">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeProductModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-6xl w-full p-6 z-10 my-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-xl font-bold text-slate-900">Kelola Produk Diskon</h3>
              <p class="text-sm text-slate-600 mt-1">
                <span x-text="'Tipe: ' + selectedTypeName"></span>
                <span class="mx-2">•</span>
                <span x-text="'Outlet: ' + getSelectedOutletName()"></span>
              </p>
            </div>
            <button @click="closeProductModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Search & Add Product --}}
          <div class="mb-4 space-y-3">
            <div class="flex gap-2">
              <div class="flex-1 relative">
                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                <input type="text" 
                       x-model="productSearch" 
                       @input.debounce.300ms="searchProducts()"
                       placeholder="Cari produk untuk ditambahkan..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>
            </div>

            {{-- Search Results --}}
            <div x-show="productSearchResults.length > 0" class="border border-slate-200 rounded-lg max-h-48 overflow-y-auto">
              <template x-for="product in productSearchResults" :key="product.id_produk">
                <div class="p-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0 flex items-center justify-between">
                  <div class="flex-1">
                    <div class="font-medium" x-text="product.nama_produk"></div>
                    <div class="text-sm text-slate-600" x-text="'Kode: ' + product.kode_produk + ' | Harga: Rp ' + formatNumber(product.harga_jual)"></div>
                  </div>
                  <button @click="openAddProductForm(product)" 
                          class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class='bx bx-plus'></i> Tambah
                  </button>
                </div>
              </template>
            </div>
          </div>

          {{-- Selected Products List --}}
          <div class="border border-slate-200 rounded-lg">
            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
              <h4 class="font-semibold text-slate-900">Produk yang Mendapat Diskon</h4>
              <p class="text-sm text-slate-600 mt-1" x-text="selectedProducts.length + ' produk'"></p>
            </div>
            
            <div class="divide-y divide-slate-100">
              <template x-for="product in selectedProducts" :key="product.id">
                <div class="p-4 hover:bg-slate-50">
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                      <div class="font-medium" x-text="product.produk.nama_produk"></div>
                      <div class="text-sm text-slate-600">
                        <span x-text="'Kode: ' + product.produk.kode_produk"></span>
                        <span class="mx-2">|</span>
                        <span x-text="'Harga Normal: Rp ' + formatNumber(product.produk.harga_jual)"></span>
                      </div>
                    </div>
                    <button @click="removeProductFromType(product.id)" 
                            class="px-3 py-1.5 text-sm border border-red-200 text-red-700 rounded-lg hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-slate-700 mb-1">Diskon (%)</label>
                      <input type="number" 
                             :value="product.diskon" 
                             @change="updateProductDiscount(product.id, $event.target.value, product.harga_jual)"
                             min="0" 
                             max="100" 
                             step="0.01"
                             class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-700 mb-1">Harga Jual Khusus (Rp)</label>
                      <input type="number" 
                             :value="product.harga_jual" 
                             @change="updateProductDiscount(product.id, product.diskon, $event.target.value)"
                             min="0" 
                             step="0.01"
                             placeholder="Kosongkan jika pakai diskon"
                             class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                  </div>
                  
                  <div x-show="product.diskon > 0 || product.harga_jual > 0" class="mt-2 p-2 bg-green-50 rounded-lg">
                    <div class="text-sm font-medium text-green-700">
                      <template x-if="product.harga_jual > 0">
                        <span x-text="'Harga Final: Rp ' + formatNumber(product.harga_jual)"></span>
                      </template>
                      <template x-if="product.harga_jual == 0 && product.diskon > 0">
                        <span x-text="'Harga Final: Rp ' + formatNumber(product.produk.harga_jual * (1 - product.diskon / 100))"></span>
                      </template>
                    </div>
                  </div>
                </div>
              </template>

              <div x-show="selectedProducts.length === 0" class="p-8 text-center text-slate-500">
                <i class='bx bx-package text-4xl mb-2'></i>
                <p>Belum ada produk yang ditambahkan</p>
                <p class="text-sm mt-1">Gunakan pencarian di atas untuk menambah produk</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="closeProductModal()" 
                    class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Add Product Form Modal --}}
    <div x-show="showAddProductForm" 
         x-transition.opacity
         class="fixed inset-0 z-[60] overflow-y-auto" 
         style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-20">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeAddProductForm()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 z-10 my-4">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900">Tambah Produk</h3>
            <button @click="closeAddProductForm()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <div class="mb-4">
            <div class="font-medium" x-text="addProductFormData.nama_produk"></div>
            <div class="text-sm text-slate-600" x-text="'Kode: ' + addProductFormData.kode_produk"></div>
            <div class="text-sm text-slate-600" x-text="'Harga Normal: Rp ' + formatNumber(addProductFormData.harga_jual)"></div>
          </div>

          <form @submit.prevent="submitAddProduct()">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Diskon (%)</label>
                <input type="number" 
                       x-model="addProductFormData.diskon" 
                       min="0" 
                       max="100" 
                       step="0.01"
                       placeholder="0"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-slate-500 mt-1">Masukkan persentase diskon (0-100)</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Harga Jual Khusus (Rp)</label>
                <input type="number" 
                       x-model="addProductFormData.harga_jual_khusus" 
                       min="0" 
                       step="0.01"
                       placeholder="Kosongkan jika menggunakan diskon"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-slate-500 mt-1">Atau set harga jual khusus (akan override diskon)</p>
              </div>

              <div x-show="addProductFormData.diskon > 0 || addProductFormData.harga_jual_khusus > 0" 
                   class="p-3 bg-green-50 rounded-lg">
                <div class="text-sm font-medium text-green-700">
                  <template x-if="addProductFormData.harga_jual_khusus > 0">
                    <span x-text="'Harga Final: Rp ' + formatNumber(addProductFormData.harga_jual_khusus)"></span>
                  </template>
                  <template x-if="!addProductFormData.harga_jual_khusus && addProductFormData.diskon > 0">
                    <span x-text="'Harga Final: Rp ' + formatNumber(addProductFormData.harga_jual * (1 - addProductFormData.diskon / 100))"></span>
                  </template>
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button type="button" @click="closeAddProductForm()" 
                      class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="loading"
                      class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!loading">Tambah Produk</span>
                <span x-show="loading">Menambahkan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Configure Axios with CSRF token
    if (typeof axios !== 'undefined') {
      // Set default headers
      axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
      axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
      axios.defaults.headers.common['Accept'] = 'application/json';
      axios.defaults.withCredentials = true; // Important for CORS/subdomain
      
      // Add interceptor to refresh token on 419
      axios.interceptors.response.use(
        response => response,
        error => {
          if (error.response && error.response.status === 419) {
            alert('Session expired. Halaman akan di-refresh.');
            window.location.reload();
          }
          return Promise.reject(error);
        }
      );
    }

    function customerTypeManagement() {
      return {
        types: [],
        outlets: @json($outlets),
        selectedOutlets: [{{ $outlets->first()->id_outlet ?? '' }}],
        showOutletDropdown: false,
        showModal: false,
        showProductModal: false,
        showAddProductForm: false,
        modalTitle: 'Tambah Tipe',
        loading: false,
        editMode: false,
        editId: null,
        selectedTypeId: null,
        selectedTypeName: '',
        selectedProducts: [],
        productSearch: '',
        productSearchResults: [],
        addProductFormData: {
          id_produk: null,
          nama_produk: '',
          kode_produk: '',
          harga_jual: 0,
          diskon: 0,
          harga_jual_khusus: null
        },
        filters: {
          search: ''
        },
        formData: {
          nama_tipe: '',
          keterangan: '',
          id_outlet: ''
        },
        statistics: {
          total_types: 0,
          total_members: 0,
          type_usage: []
        },

        init() {
          console.log('🚀 Initializing Customer Type Management with outlets:', this.selectedOutlets);
          
          // Ensure outlet selection is properly set
          if (this.selectedOutlets.length === 0 && this.outlets.length > 0) {
            this.selectedOutlets = [this.outlets[0].id_outlet];
            console.log('🔧 Setting default outlet selection:', this.selectedOutlets);
          }
          
          this.loadData();
          this.loadStatistics();
        },

        // Checkbox Management Functions
        getSelectedOutletsText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet === this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Outlet Terpilih';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Terpilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        async onOutletSelectionChange() {
          console.log('🔄 Outlet selection changed:', this.selectedOutlets);
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.loadData(),
              this.loadStatistics()
            ]);
          }
        },

        loadData() {
          if (this.selectedOutlets.length === 0) {
            this.types = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('search', this.filters.search);

          console.log('📊 Fetching customer type data with outlets:', this.selectedOutlets);

          fetch(`{{ route("admin.crm.tipe.data") }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.data) {
                this.types = data.data;
                console.log('📈 Customer type data loaded:', this.types.length, 'types');
              }
            })
            .catch(err => console.error('Error loading data:', err));
        },

        loadStatistics() {
          if (this.selectedOutlets.length === 0) {
            this.statistics = {
              total_types: 0,
              total_members: 0,
              type_usage: []
            };
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          console.log('📊 Loading statistics for outlets:', this.selectedOutlets);

          fetch(`{{ route('admin.crm.tipe.statistics') }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.statistics = data.data;
                console.log('📈 Statistics loaded:', this.statistics);
              }
            })
            .catch(err => console.error('Error loading statistics:', err));
        },

        openCreateModal() {
          this.editMode = false;
          this.modalTitle = 'Tambah Tipe Customer';
          this.resetForm();
          this.showModal = true;
        },

        editType(id) {
          this.editMode = true;
          this.editId = id;
          this.modalTitle = 'Edit Tipe Customer';
          this.loadTypeData(id);
          this.showModal = true;
        },

        loadTypeData(id) {
          fetch(`{{ url('admin/crm/tipe') }}/${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.formData = {
                  nama_tipe: data.data.nama_tipe,
                  keterangan: data.data.keterangan,
                  id_outlet: data.data.id_outlet || ''
                };
              }
            })
            .catch(err => console.error('Error loading type:', err));
        },

        async submitForm() {
          this.loading = true;
          
          try {
            const url = this.editMode 
              ? `{{ url('admin/crm/tipe') }}/${this.editId}`
              : '{{ route("admin.crm.tipe.store") }}';
            
            const method = this.editMode ? 'put' : 'post';
            
            const response = await axios[method](url, this.formData);
            
            this.loading = false;
            if (response.data.success) {
              alert(response.data.message);
              this.closeModal();
              this.loadData();
              this.loadStatistics();
            } else {
              alert(response.data.message || 'Terjadi kesalahan');
            }
          } catch (error) {
            this.loading = false;
            console.error('Error:', error);
            if (error.response) {
              alert('Error: ' + (error.response.data.message || error.response.statusText));
            } else {
              alert('Terjadi kesalahan: ' + error.message);
            }
          }
        },

        async deleteType(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus tipe customer ini?')) return;

          try {
            const response = await axios.delete(`{{ url('admin/crm/tipe') }}/${id}`);
            
            if (response.data.success) {
              alert(response.data.message);
              this.loadData();
              this.loadStatistics();
            } else {
              alert(response.data.message || 'Gagal menghapus tipe customer');
            }
          } catch (error) {
            console.error('Error:', error);
            if (error.response) {
              alert('Error: ' + (error.response.data.message || error.response.statusText));
            } else {
              alert('Terjadi kesalahan: ' + error.message);
            }
          }
        },

        closeModal() {
          this.showModal = false;
          this.resetForm();
        },

        resetForm() {
          this.formData = {
            nama_tipe: '',
            keterangan: '',
            id_outlet: ''
          };
        },

        // Product Management Methods
        openProductModal(typeId, typeName) {
          this.selectedTypeId = typeId;
          this.selectedTypeName = typeName;
          this.showProductModal = true;
          this.loadTypeProducts(typeId);
          this.productSearch = '';
          this.productSearchResults = [];
        },

        closeProductModal() {
          this.showProductModal = false;
          this.selectedTypeId = null;
          this.selectedTypeName = '';
          this.selectedProducts = [];
          this.productSearch = '';
          this.productSearchResults = [];
          this.loadData(); // Reload to update product counts
        },

        loadTypeProducts(typeId) {
          fetch(`{{ url('admin/crm/tipe') }}/${typeId}/products`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.selectedProducts = data.data;
              }
            })
            .catch(err => console.error('Error loading products:', err));
        },

        searchProducts() {
          if (this.productSearch.length < 2) {
            this.productSearchResults = [];
            return;
          }

          const params = new URLSearchParams();
          params.append('search', this.productSearch);
          params.append('type_id', this.selectedTypeId);
          
          // Add multiple outlet IDs for product search
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          fetch(`{{ route('admin.crm.tipe.search-products') }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.productSearchResults = data.data;
              }
            })
            .catch(err => console.error('Error searching products:', err));
        },

        openAddProductForm(product) {
          this.addProductFormData = {
            id_produk: product.id_produk,
            nama_produk: product.nama_produk,
            kode_produk: product.kode_produk,
            harga_jual: product.harga_jual,
            diskon: 0,
            harga_jual_khusus: null
          };
          this.showAddProductForm = true;
        },

        closeAddProductForm() {
          this.showAddProductForm = false;
          this.addProductFormData = {
            id_produk: null,
            nama_produk: '',
            kode_produk: '',
            harga_jual: 0,
            diskon: 0,
            harga_jual_khusus: null
          };
        },

        async submitAddProduct() {
          this.loading = true;
          
          try {
            const response = await axios.post(
              `{{ url('admin/crm/tipe') }}/${this.selectedTypeId}/products`,
              { 
                id_produk: this.addProductFormData.id_produk,
                diskon: this.addProductFormData.diskon || 0,
                harga_jual: this.addProductFormData.harga_jual_khusus || null
              }
            );
            
            this.loading = false;
            
            if (response.data.success) {
              this.loadTypeProducts(this.selectedTypeId);
              this.productSearch = '';
              this.productSearchResults = [];
              this.closeAddProductForm();
              alert(response.data.message);
            } else {
              alert(response.data.message || 'Gagal menambahkan produk');
            }
          } catch (error) {
            this.loading = false;
            console.error('Error:', error);
            if (error.response) {
              alert('Error: ' + (error.response.data.message || error.response.statusText));
            } else {
              alert('Terjadi kesalahan: ' + error.message);
            }
          }
        },

        async updateProductDiscount(produkTipeId, diskon, hargaJual) {
          try {
            const response = await axios.put(
              `{{ url('admin/crm/tipe/products') }}/${produkTipeId}`,
              { 
                diskon: diskon || 0,
                harga_jual: hargaJual || null
              }
            );
            
            if (response.data.success) {
              this.loadTypeProducts(this.selectedTypeId);
            } else {
              alert(response.data.message || 'Gagal mengupdate diskon');
            }
          } catch (error) {
            console.error('Error:', error);
            if (error.response) {
              alert('Error: ' + (error.response.data.message || error.response.statusText));
            } else {
              alert('Terjadi kesalahan: ' + error.message);
            }
          }
        },

        async removeProductFromType(produkTipeId) {
          if (!confirm('Hapus produk dari tipe ini?')) return;

          try {
            const response = await axios.delete(`{{ url('admin/crm/tipe/products') }}/${produkTipeId}`);
            
            if (response.data.success) {
              this.loadTypeProducts(this.selectedTypeId);
              alert(response.data.message);
            } else {
              alert(response.data.message || 'Gagal menghapus produk');
            }
          } catch (error) {
            console.error('Error:', error);
            if (error.response) {
              alert('Error: ' + (error.response.data.message || error.response.statusText));
            } else {
              alert('Terjadi kesalahan: ' + error.message);
            }
          }
        },

        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num);
        },

        getSelectedOutletName() {
          if (this.selectedOutlets.length === 0) {
            return 'Tidak ada outlet dipilih';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet === this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Outlet Terpilih';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Terpilih`;
          }
        }
      }
    }
  </script>
</x-layouts.admin>
