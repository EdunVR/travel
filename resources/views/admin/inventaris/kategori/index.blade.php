<x-layouts.admin :title="'Inventaris / Kategori Umum'">
  <div x-data="kategoriCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Kategori Umum</h1>
        <p class="text-slate-600 text-sm">Kelompokkan produk/bahan/asset.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('inventaris.kategori.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Kategori
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.kategori.export')
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        <button x-on:click="downloadTemplate()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-download text-lg'></i> Template
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.kategori.import')
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import text-lg'></i><span>Import Excel</span>
          <input type="file" class="hidden" accept=".xlsx,.xls,.csv" x-on:change="importExcel($event)">
        </label>
        @endhasPermission
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-5">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama, kode, outlet, kelompok…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-4">
          <select x-model="groupFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Kelompok: Semua</option>
            <template x-for="g in groups" :key="g"><option :value="g" x-text="g"></option></template>
          </select>
        </div>
        <div class="lg:col-span-3">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="ACTIVE">Aktif</option>
            <option value="INACTIVE">Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <div class="grid grid-cols-2 gap-2 lg:col-span-4">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="name">Nama</option>
            <option value="code">Kode</option>
            <option value="group">Kelompok</option>
            <option value="outlet">Outlet</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
        </div>
        <div class="lg:col-span-2 lg:col-start-11">
          <div class="flex rounded-xl border border-slate-200 overflow-hidden">
            <button x-on:click="view='grid'"  :class="view==='grid'  ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Grid</button>
            <button x-on:click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Tabel</button>
          </div>
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

    <!-- GRID -->
    <div x-show="view==='grid' && !loading">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="k in kategori" :key="k.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-purchase-tag-alt text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="k.name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="k.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="k.is_active ? 'Aktif' : 'Nonaktif'"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span class="font-mono" x-text="k.code"></span> • <span x-text="k.group"></span> • <span x-text="k.outlet || '-'"></span>
                </div>
                <div class="text-sm text-slate-600 mt-2 line-clamp-2" x-text="k.desc || 'Tidak ada deskripsi'"></div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              @hasPermission('inventaris.kategori.edit')
              <button x-on:click="openEdit(k)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm"><i class='bx bx-edit-alt'></i> Edit</button>
              @endhasPermission
              
              @hasPermission('inventaris.kategori.delete')
              <button x-on:click="confirmDelete(k)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i> Hapus</button>
              @endhasPermission
            </div>
          </div>
        </template>
      </div>
      <div x-show="kategori.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Kode</th>
              <th class="text-left px-4 py-3">Nama Kategori</th>
              <th class="text-left px-4 py-3">Kelompok</th>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="k in kategori" :key="k.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-mono text-slate-600" x-text="k.code"></td>
                <td class="px-4 py-3 font-medium" x-text="k.name"></td>
                <td class="px-4 py-3" x-text="k.group"></td>
                <td class="px-4 py-3" x-text="k.outlet || '-'"></td>
                <td class="px-4 py-3">
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="k.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="k.is_active ? 'Aktif' : 'Nonaktif'"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    @hasPermission('inventaris.kategori.edit')
                    <button x-on:click="openEdit(k)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    @endhasPermission
                    
                    @hasPermission('inventaris.kategori.delete')
                    <button x-on:click="confirmDelete(k)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="kategori.length===0"><td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="k in kategori" :key="k.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="font-semibold" x-text="k.name"></div>
            <div class="text-[12px] text-slate-500 mt-0.5">
              <span class="font-mono" x-text="k.code"></span> • <span x-text="k.group"></span> • <span x-text="k.outlet || '-'"></span>
            </div>
            <div class="mt-2 flex gap-2">
              <button x-on:click="openEdit(k)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Edit</button>
              <button x-on:click="confirmDelete(k)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50">Hapus</button>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- MODAL -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Kategori' : 'Tambah Kategori'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode Kategori</label>
              <input type="text" x-model.trim="form.code" placeholder="CTG-001" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
              <div class="text-xs text-slate-500 mt-1">Kode kategori digenerate otomatis</div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Kategori <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" placeholder="Nama kategori" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.nama_kategori" class="text-red-500 text-xs mt-1" x-text="errors.nama_kategori"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Kelompok <span class="text-red-500">*</span></label>
              <select x-model="form.group" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <template x-for="g in groups" :key="g"><option :value="g" x-text="g"></option></template>
              </select>
              <div x-show="errors.kelompok" class="text-red-500 text-xs mt-1" x-text="errors.kelompok"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Outlet</label>
              <select x-model="form.outlet_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Outlet —</option>
                <template x-for="o in outletOptions" :key="o.id_outlet">
                  <option :value="o.id_outlet" x-text="o.nama_outlet"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Deskripsi (opsional)</label>
              <textarea x-model.trim="form.desc" rows="3" placeholder="Deskripsi kategori" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              <div x-show="errors.deskripsi" class="text-red-500 text-xs mt-1" x-text="errors.deskripsi"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" class="rounded border-slate-300">
                <span class="text-sm text-slate-700">Kategori aktif</span>
              </label>
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
          <div class="font-semibold">Hapus Kategori?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.code"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="'Outlet: ' + (toDelete?.outlet || '-')"></div>
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

  <script>
    function kategoriCrud(){
      return {
        // State management
        kategori: [],
        groups: ['Produk', 'Bahan', 'Aset', 'Lainnya'],
        outletOptions: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        groupFilter: 'ALL',
        statusFilter: 'ALL',
        sortKey: 'name',
        sortDir: 'asc',
        view: 'table',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          code: '', 
          name: '', 
          group: 'Produk', 
          outlet_id: '', 
          desc: '', 
          is_active: true 
        },
        errors: {},
        
        // Delete confirmation
        toDelete: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          try {
            await Promise.all([
              this.fetchData(),
              this.fetchOutlets()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              kelompok_filter: this.groupFilter,
              status_filter: this.statusFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`{{ route('admin.inventaris.kategori.data') }}?${params}`);
            const data = await response.json();
            
            this.kategori = data.data.map(item => ({
              id: item.id_kategori || item.id,
              code: item.code || item.kode_kategori,
              name: item.name || item.nama_kategori,
              group: item.group || item.kelompok,
              outlet: item.outlet || item.nama_outlet,
              outlet_id: item.outlet_id || item.id_outlet,
              desc: item.desc || item.deskripsi || '',
              is_active: item.is_active !== undefined ? item.is_active : true
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchOutlets(){
          try {
            const response = await fetch('{{ route("admin.inventaris.kategori.outlets") }}');
            const data = await response.json();
            this.outletOptions = data;
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async fetchGroups(){
          try {
            const response = await fetch('{{ route("admin.inventaris.kategori.groups") }}');
            const data = await response.json();
            if (data && data.length > 0) {
              this.groups = data;
            }
          } catch (error) {
            console.error('Error fetching groups:', error);
          }
        },

        async openCreate(){ 
          try {
            ModalLoader.show();
            const response = await fetch('{{ route("admin.inventaris.kategori.generate-kode") }}');
            const data = await response.json();
            
            this.form = { 
              id: null, 
              code: data.kode_kategori,
              name: '', 
              group: 'Produk', 
              outlet_id: '', 
              desc: '', 
              is_active: true 
            }; 
          } catch (error) {
            console.error('Error generating code:', error);
            this.form = { 
              id: null, 
              code: '', 
              name: '', 
              group: 'Produk', 
              outlet_id: '', 
              desc: '', 
              is_active: true 
            }; 
          } finally {
              ModalLoader.hide();
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        async openEdit(k){ 
          // Fetch detail data dari API untuk mendapatkan outlet_id
          try {
              const response = await fetch(`{{ route('admin.inventaris.kategori.show', '') }}/${k.id}`);
              const detail = await response.json();
              
              if (response.ok) {
                  this.form = { 
                      id: detail.id,
                      code: detail.code, 
                      name: detail.name, 
                      group: detail.group, 
                      outlet_id: detail.outlet_id || '', // ← GUNAKAN outlet_id dari API
                      desc: detail.desc, 
                      is_active: detail.is_active 
                  }; 
              } else {
                  // Fallback ke data existing jika API error
                  this.form = { 
                      id: k.id,
                      code: k.code, 
                      name: k.name, 
                      group: k.group, 
                      outlet_id: k.outlet_id || '', 
                      desc: k.desc, 
                      is_active: k.is_active 
                  }; 
              }
          } catch (error) {
              console.error('Error fetching detail:', error);
              // Fallback ke data existing
              this.form = { 
                  id: k.id,
                  code: k.code, 
                  name: k.name, 
                  group: k.group, 
                  outlet_id: k.outlet_id || '', 
                  desc: k.desc, 
                  is_active: k.is_active 
              }; 
          }
          
          this.errors = {};
          this.showForm = true; 
      },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('admin.inventaris.kategori.update', '') }}/${this.form.id}`
              : '{{ route("admin.inventaris.kategori.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const formData = {
              nama_kategori: this.form.name,
              kelompok: this.form.group,
              id_outlet: this.form.outlet_id || null,
              deskripsi: this.form.desc,
              is_active: this.form.is_active
            };

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(o){ 
          this.toDelete = o; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.kategori.destroy', '') }}/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        exportPdf(){
          const params = new URLSearchParams({
            kelompok: this.groupFilter,
            status: this.statusFilter
          });
          window.open(`{{ route('admin.inventaris.kategori.export.pdf') }}?${params}`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            kelompok: this.groupFilter,
            status: this.statusFilter
          });
          window.open(`{{ route('admin.inventaris.kategori.export.excel') }}?${params}`, '_blank');
        },

        async importExcel(event){
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          try {
            const response = await fetch('{{ route("admin.inventaris.kategori.import.excel") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil diimport', 'success');
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal mengimport data', 'error');
            }
          } catch (error) {
            console.error('Error importing data:', error);
            this.showToastMessage('Gagal mengimport data', 'error');
          } finally {
            event.target.value = '';
          }
        },

        downloadTemplate(){
          window.open('{{ route("admin.inventaris.kategori.download-template") }}', '_blank');
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
</x-layouts.admin>
