<x-layouts.admin :title="'Inventaris'">
  @php
    $user = auth()->user();
    $userOutlets = $user->outlets ?? collect();
    $defaultOutlet = session('outlet_id') ?? $userOutlets->first()->id_outlet ?? null;
  @endphp

  <div x-data="inventarisDashboard({{ json_encode($userOutlets->pluck('id_outlet')->toArray()) }}, {{ $defaultOutlet }})" x-init="init()" class="space-y-6 overflow-x-hidden">

    {{-- HEADER + SEARCH --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Inventaris</h1>
        <p class="text-slate-600 text-sm">Ringkasan stok, pintasan modul, dan aktivitas terbaru.</p>
      </div>
      <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
        {{-- Outlet Filter with Checkbox --}}
        @if($userOutlets->count() > 0)
        <div class="relative" x-data="{ open: false }">
          <button @click="open = !open" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 flex items-center gap-2 bg-white min-w-[200px] justify-between">
            <span x-text="getSelectedOutletText()">Pilih Outlet</span>
            <i class='bx bx-chevron-down transition-transform' :class="{'rotate-180': open}"></i>
          </button>
          
          <div x-show="open" @click.away="open = false" x-transition class="absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
            <div class="p-2">
              @foreach($userOutlets as $outlet)
              <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                <input type="checkbox" 
                       :checked="selectedOutlets.includes({{ $outlet->id_outlet }})"
                       @change="toggleOutlet({{ $outlet->id_outlet }})"
                       class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm">{{ $outlet->nama_outlet }}</span>
              </label>
              @endforeach
            </div>
            <div class="border-t border-slate-200 p-2">
              <button @click="selectAllOutlets()" class="text-xs text-primary-600 hover:text-primary-700 mr-3">
                Pilih Semua
              </button>
              <button @click="clearAllOutlets()" class="text-xs text-slate-500 hover:text-slate-700">
                Hapus Semua
              </button>
            </div>
          </div>
        </div>
        @endif

        <div class="relative">
          <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
          <input x-model="searchTerm" x-on:input.debounce.500ms="performSearch()" 
                 placeholder="Cari produk/bahan/inventori/outlet…"
                 class="w-full sm:w-72 pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200" />
          
          {{-- Search Results Dropdown --}}
          <div x-show="searchResults.length > 0" class="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-10 max-h-60 overflow-y-auto">
            <template x-for="result in searchResults" :key="result.code">
              <a :href="result.url" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                <div :class="{
                  'bg-blue-100 text-blue-600': result.type === 'produk',
                  'bg-green-100 text-green-600': result.type === 'bahan', 
                  'bg-purple-100 text-purple-600': result.type === 'inventori',
                  'bg-orange-100 text-orange-600': result.type === 'outlet'
                }" class="p-2 rounded-lg">
                  <i :class="{
                    'bx bx-cube': result.type === 'produk',
                    'bx bx-leaf': result.type === 'bahan',
                    'bx bx-archive': result.type === 'inventori',
                    'bx bx-building': result.type === 'outlet'
                  }" class="text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-medium truncate" x-text="result.name"></div>
                  <div class="text-xs text-slate-500 truncate">
                    <span x-text="result.code"></span> • 
                    <span x-text="result.outlet"></span> • 
                    <span x-text="result.category"></span>
                  </div>
                </div>
              </a>
            </template>
          </div>
        </div>
        <a href="{{ route('admin.inventaris.produk.index') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Produk
        </a>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data inventaris...</span>
      </div>
    </div>

    {{-- KPI CARDS --}}
    <section x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 flex items-center gap-2">
            <i class='bx bx-cube text-primary-600 text-lg'></i> Total SKU
          </div>
          <span class="text-[11px] px-2 py-0.5 rounded-full bg-primary-50 text-primary-700">Produk</span>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="stats.totalSku"></div>
        <p class="text-slate-500 text-xs mt-1">Gabungan Produk & Inventori</p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 flex items-center gap-2">
            <i class='bx bx-store-alt text-primary-600 text-lg'></i> Outlet Aktif
          </div>
          <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Aktif</span>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="stats.outlets"></div>
        <p class="text-slate-500 text-xs mt-1">Titik penyimpanan</p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 flex items-center gap-2">
            <i class='bx bx-package text-primary-600 text-lg'></i> Stok Total
          </div>
          <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Unit</span>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="stats.totalStock.toLocaleString()"></div>
        <p class="text-slate-500 text-xs mt-1">Semua kategori</p>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 flex items-center gap-2">
            <i class='bx bx-error-circle text-primary-600 text-lg'></i> Stok Rendah
          </div>
          <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-50 text-red-700">Alert</span>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="stats.lowStock"></div>
        <p class="text-slate-500 text-xs mt-1">Butuh restock</p>
      </div>
    </section>

    {{-- QUICK LINKS --}}
    <section x-show="!loading">
      <h2 class="text-lg font-semibold mb-3">Pintasan Modul</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="{{ route('admin.inventaris.outlet.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-buildings text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Outlet</div>
              <p class="text-sm text-slate-600">Kelola cabang/gudang</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Lihat daftar outlet</div>
        </a>

        <a href="{{ route('admin.inventaris.kategori.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-category-alt text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Kategori</div>
              <p class="text-sm text-slate-600">Kelompokkan produk</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Tambah/ubah kategori</div>
        </a>

        <a href="{{ route('admin.inventaris.satuan.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-ruler text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Satuan</div>
              <p class="text-sm text-slate-600">Pcs, Kg, gram, …</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Kelola satuan</div>
        </a>

        <a href="{{ route('admin.inventaris.produk.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-cuboid text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Produk</div>
              <p class="text-sm text-slate-600">CRUD + galeri</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Kelola master produk</div>
        </a>

        <a href="{{ route('admin.inventaris.bahan.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-leaf text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Bahan</div>
              <p class="text-sm text-slate-600">Raw material</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Manajemen bahan</div>
        </a>

        <a href="{{ route('admin.inventaris.inventori.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-archive text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Inventori</div>
              <p class="text-sm text-slate-600">Barang non-produk</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Stok inventori</div>
        </a>

        <a href="{{ route('admin.inventaris.transfer-gudang.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-xl/10 hover:-translate-y-0.5 transition">
          <div class="flex items-start gap-3">
            <div class="rounded-xl bg-primary-50 p-3 ring-1 ring-primary-100"><i class='bx bx-transfer-alt text-primary-700 text-2xl'></i></div>
            <div class="min-w-0">
              <div class="font-semibold">Transfer Gudang</div>
              <p class="text-sm text-slate-600">Mutasi antar outlet</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">Buat permintaan</div>
        </a>
      </div>
    </section>

    {{-- TWO COLUMNS: Low Stock + Activity --}}
    <section x-show="!loading" class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      {{-- Low Stock --}}
      <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold flex items-center gap-2">
            <i class='bx bx-error-circle text-amber-600'></i> Stok Rendah
          </div>
          <div class="text-xs text-slate-500 hidden sm:block">
            Total: <span class="font-medium" x-text="lowStockItems.length"></span> item
          </div>
        </div>

        <div class="p-4 sm:p-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <template x-for="item in lowStockItems" :key="item.id">
              <div class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex-1 min-w-0">
                    <div class="font-medium truncate" x-text="item.name"></div>
                    <div class="text-xs text-slate-500 mt-0.5">
                      <span x-text="item.category"></span> • <span x-text="item.outlet"></span>
                    </div>
                    <div class="text-xs text-amber-600 mt-1">
                      Min. stok: <span x-text="item.min_stock"></span>
                    </div>
                  </div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                    <i class='bx bx-cube'></i><span x-text="item.stock"></span>
                  </span>
                </div>
                <div class="mt-2 flex items-center gap-2">
                  <a :href="item.manage_url"
                     class="text-xs rounded-lg border border-slate-200 px-2 py-1 hover:bg-slate-50">Kelola</a>
                  <a href="{{ route('admin.inventaris.transfer-gudang.index') }}"
                     class="text-xs rounded-lg bg-primary-600 text-white px-2 py-1 hover:bg-primary-700">Restock</a>
                </div>
              </div>
            </template>
          </div>
          <div x-show="lowStockItems.length === 0" class="text-center text-slate-500 text-sm py-6">
            <i class='bx bx-check-circle text-2xl mb-2 text-green-500'></i>
            <div>Tidak ada item yang mendekati batas stok minimum.</div>
          </div>
        </div>
      </div>

      {{-- Activity --}}
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 font-semibold flex items-center gap-2">
          <i class='bx bx-time-five text-primary-600'></i> Aktivitas Terbaru
        </div>
        <div class="p-4 sm:p-5 space-y-3">
          <template x-for="activity in recentActivities" :key="activity.id">
            <div class="flex items-start gap-3">
              <div class="mt-0.5">
                <i :class="activity.icon" class="text-lg text-primary-600"></i>
              </div>
              <div class="min-w-0 flex-1">
                <div class="text-sm">
                  <span class="font-medium" x-text="activity.title"></span>
                  <span class="text-slate-600" x-text="activity.desc"></span>
                </div>
                <div class="text-xs text-slate-500" x-text="activity.time"></div>
              </div>
            </div>
          </template>
          <div x-show="recentActivities.length === 0" class="text-center text-slate-500 text-sm py-4">
            <i class='bx bx-info-circle text-2xl mb-2'></i>
            <div>Belum ada aktivitas transfer.</div>
          </div>
        </div>
      </div>
    </section>

    {{-- OUTLET OVERVIEW --}}
    <section x-show="!loading">
      <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-semibold">Ringkasan Outlet</h2>
        <a href="{{ route('admin.inventaris.outlet.index') }}" class="text-sm text-primary-700 hover:underline">Kelola Outlet</a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="outlet in outletsSummary" :key="outlet.name">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between">
              <div class="font-semibold" x-text="outlet.name"></div>
              <span class="text-xs text-slate-500" x-text="outlet.city"></span>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
              <div class="rounded-lg border border-slate-200 p-2">
                <div class="text-xs text-slate-500">Produk</div>
                <div class="font-bold" x-text="outlet.products"></div>
              </div>
              <div class="rounded-lg border border-slate-200 p-2">
                <div class="text-xs text-slate-500">Bahan</div>
                <div class="font-bold" x-text="outlet.materials"></div>
              </div>
              <div class="rounded-lg border border-slate-200 p-2">
                <div class="text-xs text-slate-500">Inventori</div>
                <div class="font-bold" x-text="outlet.inventory"></div>
              </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
              <div>Total Stok</div>
              <div class="font-medium" x-text="outlet.stock.toLocaleString() + ' unit'"></div>
            </div>
          </div>
        </template>
      </div>
    </section>
  </div>

  <script>
    function inventarisDashboard(availableOutlets, defaultOutlet){
      return {
        loading: true,
        availableOutlets: availableOutlets || [],
        selectedOutlets: defaultOutlet ? [defaultOutlet] : [],
        searchTerm: '',
        searchResults: [],
        stats: { totalSku: 0, outlets: 0, totalStock: 0, lowStock: 0 },
        outletsSummary: [],
        lowStockItems: [],
        recentActivities: [],

        getSelectedOutletText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = @json($userOutlets->keyBy('id_outlet'));
            return outlet[this.selectedOutlets[0]]?.nama_outlet || 'Outlet';
          } else if (this.selectedOutlets.length === this.availableOutlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Dipilih`;
          }
        },

        toggleOutlet(outletId) {
          const index = this.selectedOutlets.indexOf(outletId);
          if (index > -1) {
            this.selectedOutlets.splice(index, 1);
          } else {
            this.selectedOutlets.push(outletId);
          }
          this.loadAllData();
        },

        selectAllOutlets() {
          this.selectedOutlets = [...this.availableOutlets];
          this.loadAllData();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.loadAllData();
        },

        async init(){
          try {
            await this.loadAllData();
          } catch (error) {
            console.error('Error initializing dashboard:', error);
            this.showToast('Gagal memuat data dashboard', 'error');
          } finally {
            this.loading = false;
          }
        },

        async loadAllData() {
          if (this.loading && this.selectedOutlets.length === 0) return;
          
          this.loading = true;
          try {
            await Promise.all([
              this.loadStats(),
              this.loadOutletsSummary(),
              this.loadLowStockItems(),
              this.loadRecentActivities()
            ]);
          } catch (error) {
            console.error('Error loading data:', error);
            this.showToast('Gagal memuat data dashboard', 'error');
          } finally {
            this.loading = false;
          }
        },

        getOutletParams() {
          if (this.selectedOutlets.length === 0) {
            return '';
          }
          const params = new URLSearchParams();
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          return '?' + params.toString();
        },

        async loadStats(){
          try {
            const response = await fetch('{{ route("admin.inventaris.stats") }}' + this.getOutletParams());
            const data = await response.json();
            this.stats = data;
          } catch (error) {
            console.error('Error loading stats:', error);
          }
        },

        async loadOutletsSummary(){
          try {
            const response = await fetch('{{ route("admin.inventaris.outlets-summary") }}' + this.getOutletParams());
            const data = await response.json();
            this.outletsSummary = data;
          } catch (error) {
            console.error('Error loading outlets summary:', error);
          }
        },

        async loadLowStockItems(){
          try {
            const response = await fetch('{{ route("admin.inventaris.low-stock-items") }}' + this.getOutletParams());
            const data = await response.json();
            this.lowStockItems = data;
          } catch (error) {
            console.error('Error loading low stock items:', error);
          }
        },

        async loadRecentActivities(){
          try {
            const response = await fetch('{{ route("admin.inventaris.recent-activities") }}' + this.getOutletParams());
            const data = await response.json();
            this.recentActivities = data;
          } catch (error) {
            console.error('Error loading recent activities:', error);
          }
        },

        async performSearch(){
          if (!this.searchTerm.trim()) {
            this.searchResults = [];
            return;
          }

          try {
            const response = await fetch(`{{ route("admin.inventaris.search") }}?q=${encodeURIComponent(this.searchTerm)}` + (this.getOutletParams() ? '&' + this.getOutletParams().substring(1) : ''));
            const data = await response.json();
            this.searchResults = data;
          } catch (error) {
            console.error('Error performing search:', error);
            this.searchResults = [];
          }
        },

        showToast(message, type = 'success') {
          // Simple toast notification
          const toast = document.createElement('div');
          toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl border ${
            type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'
          } shadow-lg`;
          toast.innerHTML = `
            <div class="flex items-center gap-2">
              <i class='bx ${type === 'success' ? 'bx-check-circle text-green-600' : 'bx-error-circle text-red-600'}'></i>
              <span>${message}</span>
            </div>
          `;
          document.body.appendChild(toast);
          
          setTimeout(() => {
            document.body.removeChild(toast);
          }, 4000);
        }
      }
    }
  </script>
</x-layouts.admin>
