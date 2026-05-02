<x-layouts.admin :title="'Inventaris / Satuan'">
  <div x-data="satuanCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Satuan</h1>
        <p class="text-slate-600 text-sm">Kelola daftar satuan untuk produk/bahan dengan sistem konversi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('inventaris.satuan.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Satuan
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.satuan.export')
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
        
        @hasPermission('inventaris.satuan.import')
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
        <!-- Search -->
        <div class="lg:col-span-6">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama, kode, simbol…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Status -->
        <div class="lg:col-span-3">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="ACTIVE">Aktif</option>
            <option value="INACTIVE">Nonaktif</option>
          </select>
        </div>
        <!-- Sort -->
        <div class="lg:col-span-3">
          <div class="grid grid-cols-2 gap-2">
            <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="name">Nama</option>
              <option value="code">Kode</option>
              <option value="symbol">Simbol</option>
            </select>
            <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="asc">Naik</option><option value="desc">Turun</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Toggle View -->
      <div class="flex justify-end">
        <div class="flex rounded-xl border border-slate-200 overflow-hidden">
          <button x-on:click="view='grid'"  :class="view==='grid'  ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="px-3 py-2 text-sm">Grid</button>
          <button x-on:click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="px-3 py-2 text-sm">Tabel</button>
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
        <template x-for="u in satuan" :key="u.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-ruler text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="u.name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="u.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="u.is_active ? 'Aktif' : 'Nonaktif'"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span class="font-mono" x-text="u.code"></span>
                  <template x-if="u.symbol"><span> • <span x-text="u.symbol"></span></template>
                </div>
                <div class="text-sm text-slate-600 mt-2 line-clamp-2" x-text="u.desc || 'Tidak ada deskripsi'"></div>
                
                <!-- Tampilkan konversi -->
                <div x-show="u.nilai_konversi && u.satuan_utama_name" class="text-xs text-primary-600 mt-2 p-2 bg-blue-50 rounded-lg">
                  <div class="font-medium">Konversi:</div>
                  <div>1 <span x-text="u.symbol"></span> = <span x-text="u.nilai_konversi"></span> <span x-text="u.satuan_utama_simbol || u.satuan_utama_name"></span></div>
                </div>
                <div x-show="!u.nilai_konversi || !u.satuan_utama_name" class="text-xs text-slate-400 mt-2">
                  Tidak ada konversi
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              @hasPermission('inventaris.satuan.edit')
              <button x-on:click="openEdit(u)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm"><i class='bx bx-edit-alt'></i> Edit</button>
              @endhasPermission
              
              @hasPermission('inventaris.satuan.delete')
              <button x-on:click="confirmDelete(u)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i> Hapus</button>
              @endhasPermission
            </div>
          </div>
        </template>
      </div>
      <div x-show="satuan.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3 w-12">No</th>
              <th class="text-left px-4 py-3">Kode</th>
              <th class="text-left px-4 py-3">Satuan</th>
              <th class="text-left px-4 py-3">Simbol</th>
              <th class="text-left px-4 py-3">Konversi</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="text-left px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(u,i) in satuan" :key="u.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="i+1"></td>
                <td class="px-4 py-3 font-mono text-slate-600" x-text="u.code"></td>
                <td class="px-4 py-3 font-medium" x-text="u.name"></td>
                <td class="px-4 py-3" x-text="u.symbol || '-'"></td>
                <td class="px-4 py-3">
                  <template x-if="u.nilai_konversi && u.satuan_utama_name">
                    <div class="text-xs">
                      <div class="font-medium text-slate-700">1 <span x-text="u.symbol"></span> =</div>
                      <div class="text-primary-600" x-text="u.nilai_konversi + ' ' + (u.satuan_utama_simbol || u.satuan_utama_name)"></div>
                    </div>
                  </template>
                  <template x-if="!u.nilai_konversi || !u.satuan_utama_name">
                    <span class="text-slate-400 text-xs">-</span>
                  </template>
                </td>
                <td class="px-4 py-3">
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="u.is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="u.is_active ? 'Aktif' : 'Nonaktif'"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    @hasPermission('inventaris.satuan.edit')
                    <button x-on:click="openEdit(u)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    @endhasPermission
                    
                    @hasPermission('inventaris.satuan.delete')
                    <button x-on:click="confirmDelete(u)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="satuan.length===0"><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="closeForm()" class="w-full max-w-2xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Satuan' : 'Tambah Satuan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode Satuan</label>
              <input type="text" x-model.trim="form.code" placeholder="SAT-001" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
              <div class="text-xs text-slate-500 mt-1">Kode satuan digenerate otomatis</div>
              <div x-show="errors.code" class="text-red-500 text-xs mt-1" x-text="errors.code"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Satuan <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" placeholder="Pcs, gram, Kg…" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Simbol (opsional)</label>
              <input type="text" x-model.trim="form.symbol" placeholder="pcs, g, kg…" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.symbol" class="text-red-500 text-xs mt-1" x-text="errors.symbol"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Deskripsi (opsional)</label>
              <textarea x-model.trim="form.desc" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              <div x-show="errors.desc" class="text-red-500 text-xs mt-1" x-text="errors.desc"></div>
            </div>
            
            <!-- Section Konversi Satuan -->
            <div class="sm:col-span-2 border-t border-slate-100 pt-4">
              <label class="text-sm font-medium text-slate-700">Konversi Satuan (Opsional)</label>
              <p class="text-xs text-slate-500 mb-3">Atur konversi jika satuan ini merupakan turunan dari satuan utama</p>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="text-sm text-slate-600">Satuan Utama</label>
                  <select x-model="form.satuan_utama_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                    <option value="">Pilih Satuan Utama</option>
                    <template x-for="su in satuanUtama" :key="su.id_satuan">
                      <option :value="su.id_satuan" x-text="su.nama_satuan + ' (' + su.simbol + ')'"></option>
                    </template>
                  </select>
                  <div x-show="errors.satuan_utama_id" class="text-red-500 text-xs mt-1" x-text="errors.satuan_utama_id"></div>
                </div>
                <div>
                  <label class="text-sm text-slate-600">Nilai Konversi</label>
                  <input type="number" x-model.number="form.nilai_konversi" placeholder="0.00" step="0.001" min="0"
                         class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                  <div class="text-xs text-slate-500 mt-1" x-show="form.satuan_utama_id && form.nilai_konversi">
                    Konversi: 1 <span x-text="form.symbol || 'satuan'"></span> = <span x-text="form.nilai_konversi"></span> 
                    <template x-for="su in satuanUtama" :key="su.id_satuan">
                      <span x-show="su.id_satuan == form.satuan_utama_id" x-text="su.simbol"></span>
                    </template>
                  </div>
                  <div x-show="errors.nilai_konversi" class="text-red-500 text-xs mt-1" x-text="errors.nilai_konversi"></div>
                </div>
              </div>
            </div>

            <div class="sm:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" class="rounded border-slate-300">
                <span class="text-sm text-slate-700">Satuan aktif</span>
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
          <div class="font-semibold">Hapus Satuan?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.code"></span></div>
            <div x-show="toDelete?.nilai_konversi" class="text-xs text-slate-500 mt-1">
              Konversi: 1 <span x-text="toDelete?.symbol"></span> = <span x-text="toDelete?.nilai_konversi"></span> <span x-text="toDelete?.satuan_utama_name"></span>
            </div>
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
    function satuanCrud(){
      return {
        // State management
        satuan: [],
        satuanUtama: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
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
          symbol: '', 
          desc: '', 
          is_active: true,
          nilai_konversi: null,
          satuan_utama_id: null
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
              this.fetchSatuanUtama()
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
              status_filter: this.statusFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`{{ route('admin.inventaris.satuan.data') }}?${params}`);
            const data = await response.json();
            
            this.satuan = data.data.map(item => ({
              id: item.id_satuan || item.id,
              code: item.code || item.kode_satuan,
              name: item.name || item.nama_satuan,
              symbol: item.symbol || item.simbol,
              desc: item.desc || item.deskripsi,
              is_active: item.is_active !== undefined ? item.is_active : true,
              nilai_konversi: item.nilai_konversi ? parseFloat(item.nilai_konversi) : null,
              satuan_utama_id: item.satuan_utama_id || null,
              satuan_utama_name: item.satuan_utama_name || null,
              satuan_utama_simbol: item.satuan_utama_simbol || null,
              konversi_display: item.konversi_display || null
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchSatuanUtama(){
          try {
            const response = await fetch('{{ route("admin.inventaris.satuan.satuan-utama") }}');
            const data = await response.json();
            this.satuanUtama = data;
          } catch (error) {
            console.error('Error fetching satuan utama:', error);
          }
        },

        async openCreate(){ 
          try {
            // Fetch satuan utama dan kode baru secara bersamaan
            const [kodeResponse, satuanUtamaResponse] = await Promise.all([
              fetch('{{ route("admin.inventaris.satuan.generate-kode") }}'),
              fetch('{{ route("admin.inventaris.satuan.satuan-utama") }}')
            ]);
            
            const kodeData = await kodeResponse.json();
            const satuanUtamaData = await satuanUtamaResponse.json();
            
            this.satuanUtama = satuanUtamaData;
            
            this.form = { 
              id: null, 
              code: kodeData.kode_satuan,
              name: '', 
              symbol: '', 
              desc: '', 
              is_active: true,
              nilai_konversi: null,
              satuan_utama_id: null
            }; 
          } catch (error) {
            console.error('Error generating code:', error);
            this.form = { 
              id: null, 
              code: '', 
              name: '', 
              symbol: '', 
              desc: '', 
              is_active: true,
              nilai_konversi: null,
              satuan_utama_id: null
            }; 
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        async openEdit(u){ 
          try {
            // Fetch data satuan dan satuan utama secara bersamaan
            const [detailResponse, satuanUtamaResponse] = await Promise.all([
              fetch(`{{ route('admin.inventaris.satuan.show', '') }}/${u.id}`),
              fetch('{{ route("admin.inventaris.satuan.satuan-utama") }}')
            ]);
            
            const data = await detailResponse.json();
            const satuanUtamaData = await satuanUtamaResponse.json();
            
            this.satuanUtama = satuanUtamaData;
            
            this.form = { 
              id: data.id,
              code: data.code, 
              name: data.name, 
              symbol: data.symbol, 
              desc: data.desc, 
              is_active: data.is_active,
              nilai_konversi: data.nilai_konversi,
              satuan_utama_id: data.satuan_utama_id
            }; 
          } catch (error) {
            console.error('Error fetching satuan detail:', error);
            // Fallback ke data lokal jika gagal
            this.form = { 
              id: u.id,
              code: u.code, 
              name: u.name, 
              symbol: u.symbol, 
              desc: u.desc, 
              is_active: u.is_active,
              nilai_konversi: u.nilai_konversi,
              satuan_utama_id: u.satuan_utama_id
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
              ? `{{ route('admin.inventaris.satuan.update', '') }}/${this.form.id}`
              : '{{ route("admin.inventaris.satuan.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const formData = {
              nama_satuan: this.form.name,
              simbol: this.form.symbol,
              deskripsi: this.form.desc,
              is_active: this.form.is_active,
              nilai_konversi: this.form.nilai_konversi,
              satuan_utama_id: this.form.satuan_utama_id
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

        confirmDelete(u){ 
          this.toDelete = u; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.satuan.destroy', '') }}/${this.toDelete.id}`, {
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
            status: this.statusFilter
          });
          window.open(`{{ route('admin.inventaris.satuan.export.pdf') }}?${params}`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            status: this.statusFilter
          });
          window.open(`{{ route('admin.inventaris.satuan.export.excel') }}?${params}`, '_blank');
        },

        async importExcel(event){
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          try {
            const response = await fetch('{{ route("admin.inventaris.satuan.import.excel") }}', {
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
          window.open('{{ route("admin.inventaris.satuan.download-template") }}', '_blank');
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
