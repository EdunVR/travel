<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Keberangkatan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Keberangkatan')]); ?>
  <div x-data="keberangkatanCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Keberangkatan</h1>
        <p class="text-slate-600 text-sm">Kelola batch keberangkatan jamaah untuk setiap paket.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.keberangkatan.create')): ?>
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Keberangkatan
        </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-5">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari kode, nama keberangkatan…"
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-4">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Status</option>
            <option value="planning">Planning</option>
            <option value="confirmed">Confirmed</option>
            <option value="departed">Departed</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="lg:col-span-3">
          <select x-model="packageFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Paket</option>
            <template x-for="pkg in packages" :key="pkg.id">
              <option :value="pkg.id" x-text="pkg.package_name"></option>
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
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Kode</th>
              <th class="text-left px-4 py-3">Nama Keberangkatan</th>
              <th class="text-left px-4 py-3">Paket</th>
              <th class="text-left px-4 py-3">Tanggal</th>
              <th class="text-left px-4 py-3">Jamaah</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right w-48">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="k in keberangkatans" :key="k.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-mono text-slate-600" x-text="k.code"></td>
                <td class="px-4 py-3 font-medium" x-text="k.name"></td>
                <td class="px-4 py-3 text-sm" x-text="k.package"></td>
                <td class="px-4 py-3 text-sm">
                  <div x-text="k.departure_date"></div>
                  <div class="text-xs text-slate-500" x-text="'s/d ' + k.return_date"></div>
                </td>
                <td class="px-4 py-3" x-html="k.jamaah_count"></td>
                <td class="px-4 py-3" x-html="k.status"></td>
                <td class="px-4 py-3" x-html="k.aksi"></td>
              </tr>
            </template>
            <tr x-show="keberangkatans.length===0"><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="k in keberangkatans" :key="k.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100">
                <i class='bx bx-plane-departure'></i>
              </div>
              <div class="flex-1">
                <div class="font-semibold" x-text="k.name"></div>
                <div class="text-[11px] text-slate-500 font-mono" x-text="k.code"></div>
                <div class="text-sm text-slate-600 mt-1" x-text="k.package"></div>
                <div class="text-xs text-slate-500 mt-1">
                  <span x-text="k.departure_date"></span> - <span x-text="k.return_date"></span>
                </div>
                <div class="mt-1 text-[11px]" x-html="k.jamaah_count"></div>
                <div class="mt-1" x-html="k.status"></div>
              </div>
            </div>
            <div class="mt-3 flex gap-2 flex-wrap">
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.keberangkatan.view')): ?>
              <button x-on:click="showDetail(k)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">Detail</button>
              <?php endif; ?>
              
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.keberangkatan.edit')): ?>
              <button x-on:click="openEdit(k)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">Edit</button>
              <?php endif; ?>
              
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.keberangkatan.delete')): ?>
              <button x-on:click="confirmDelete(k)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm">Hapus</button>
              <?php endif; ?>
            </div>
          </div>
        </template>
      </div>
    </div>


    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[85vh] flex flex-col overflow-hidden my-4">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Keberangkatan' : 'Tambah Keberangkatan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <!-- Error Summary -->
          <div x-show="Object.keys(errors).length > 0" class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
            <div class="text-sm font-medium text-red-700 mb-1"><i class="bx bx-error-circle"></i> Terdapat kesalahan:</div>
            <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
              <template x-for="(msg, field) in errors" :key="field">
                <li x-text="msg"></li>
              </template>
            </ul>
          </div>
          <div class="grid grid-cols-1 gap-4">
            <!-- Pilih Paket Perjalanan -->
            <div>
              <label class="text-sm font-medium text-slate-700">Paket Perjalanan <span class="text-red-500">*</span></label>
              <select x-model="form.id_travel_package" x-on:change="onPackageChange()" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-primary-200">
                <option value="">-- Pilih Paket Perjalanan --</option>
                <template x-for="pkg in packages" :key="pkg.id">
                  <option :value="pkg.id" x-text="pkg.package_name + ' (' + pkg.package_type.toUpperCase() + ') - ' + pkg.duration_days + ' hari'"></option>
                </template>
              </select>
              <div x-show="errors.id_travel_package" class="text-red-500 text-xs mt-1" x-text="errors.id_travel_package"></div>
            </div>

            <!-- Info Paket (Auto-filled) -->
            <div x-show="selectedPackage" class="p-4 rounded-xl bg-blue-50 border border-blue-200">
              <div class="text-sm font-medium text-blue-900 mb-2">Informasi Paket</div>
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <span class="text-blue-600">Tipe:</span>
                  <span class="font-medium ml-1" x-text="selectedPackage?.package_type?.toUpperCase()"></span>
                </div>
                <div>
                  <span class="text-blue-600">Durasi:</span>
                  <span class="font-medium ml-1" x-text="selectedPackage?.duration_days + ' hari'"></span>
                </div>
                <div>
                  <span class="text-blue-600">Kapasitas:</span>
                  <span class="font-medium ml-1" x-text="selectedPackage?.capacity + ' jamaah'"></span>
                </div>
                <div>
                  <span class="text-blue-600">Harga:</span>
                  <span class="font-medium ml-1" x-text="'Rp ' + (selectedPackage?.price || 0).toLocaleString('id-ID')"></span>
                </div>
              </div>
            </div>

            <!-- Kode & Nama Keberangkatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="text-sm font-medium text-slate-700">Kode Keberangkatan (Otomatis)</label>
                <div class="relative">
                  <input type="text" x-model.trim="form.keberangkatan_code" readonly class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 bg-slate-50 cursor-not-allowed pr-10">
                  <div class="absolute right-3 top-1/2 -translate-y-1/2 mt-0.5">
                    <i class='bx bx-lock-alt text-slate-400'></i>
                  </div>
                </div>
                <div x-show="form.keberangkatan_code" class="text-xs text-green-600 mt-1">
                  <i class='bx bx-check-circle'></i> Kode dibuat otomatis dari paket
                </div>
                <div x-show="errors.keberangkatan_code" class="text-red-500 text-xs mt-1" x-text="errors.keberangkatan_code"></div>
              </div>
              <div>
                <label class="text-sm font-medium text-slate-700">Nama Keberangkatan <span class="text-red-500">*</span></label>
                <input type="text" x-model.trim="form.keberangkatan_name" placeholder="Batch 1 - Januari 2026" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-primary-200">
                <div x-show="errors.keberangkatan_name" class="text-red-500 text-xs mt-1" x-text="errors.keberangkatan_name"></div>
              </div>
            </div>

            <!-- Tanggal Keberangkatan & Kepulangan (Auto-filled from package) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="text-sm font-medium text-slate-700">Tanggal Keberangkatan (Dari Paket)</label>
                <input type="date" x-model="form.departure_date" readonly class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 bg-slate-50 cursor-not-allowed">
                <div x-show="form.departure_date" class="text-xs text-green-600 mt-1">
                  <i class='bx bx-check-circle'></i> Diambil dari jadwal paket
                </div>
                <div x-show="errors.departure_date" class="text-red-500 text-xs mt-1" x-text="errors.departure_date"></div>
              </div>
              <div>
                <label class="text-sm font-medium text-slate-700">Tanggal Kepulangan (Dari Paket)</label>
                <input type="date" x-model="form.return_date" readonly class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 bg-slate-50 cursor-not-allowed">
                <div x-show="form.return_date" class="text-xs text-green-600 mt-1">
                  <i class='bx bx-check-circle'></i> Diambil dari jadwal paket
                </div>
              </div>
            </div>

            <!-- Total Jamaah & Outlet -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="text-sm font-medium text-slate-700">Kapasitas Jamaah <span class="text-red-500">*</span></label>
                <input type="number" x-model.number="form.total_jamaah" placeholder="40" min="1" :max="selectedPackage?.capacity" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-primary-200">
                <div x-show="errors.total_jamaah" class="text-red-500 text-xs mt-1" x-text="errors.total_jamaah"></div>
                <div x-show="selectedPackage" class="text-xs text-slate-500 mt-1">
                  Maksimal: <span x-text="selectedPackage?.capacity"></span> jamaah
                </div>
              </div>
              <div>
                <label class="text-sm font-medium text-slate-700">Outlet <span class="text-red-500">*</span></label>
                <select x-model="form.id_outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-primary-200">
                  <option value="">Pilih Outlet</option>
                  <template x-for="outlet in outlets" :key="outlet.id_outlet">
                    <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                  </template>
                </select>
                <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
              </div>
            </div>

            <!-- Status (Only for Edit) -->
            <div x-show="form.id">
              <label class="text-sm font-medium text-slate-700">Status</label>
              <select x-model="form.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-primary-200">
                <option value="planning">Planning</option>
                <option value="confirmed">Confirmed</option>
                <option value="departed">Departed</option>
                <option value="completed">Completed</option>
              </select>
              <div x-show="errors.status" class="text-red-500 text-xs mt-1" x-text="errors.status"></div>
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
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Keberangkatan?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="toDelete?.name"></div>
            <div class="text-xs text-slate-500 mt-1"><span class="font-mono" x-text="toDelete?.code"></span> • <span x-text="toDelete?.package"></span></div>
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
    function keberangkatanCrud(){
      return {
        // State management
        keberangkatans: [],
        packages: [],
        outlets: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Selected package info
        selectedPackage: null,
        
        // Filters and search
        search: '',
        statusFilter: 'ALL',
        packageFilter: 'ALL',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          keberangkatan_code: '', 
          keberangkatan_name: '', 
          id_travel_package: '', 
          departure_date: '', 
          return_date: '',
          total_jamaah: '',
          status: 'planning',
          id_outlet: ''
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
              this.fetchPackages(),
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
              status_filter: this.statusFilter,
              package_filter: this.packageFilter
            });

            const response = await fetch(`<?php echo e(route('admin.inventaris.travel.keberangkatan.data')); ?>?${params}`);
            const data = await response.json();
            
            this.keberangkatans = data.data.map(item => ({
              id: item.id,
              code: item.code,
              name: item.name,
              package: item.package,
              departure_date: item.departure_date,
              return_date: item.return_date,
              jamaah_count: item.jamaah_count,
              status: item.status,
              aksi: item.aksi,
              id_travel_package: item.id_travel_package,
              total_jamaah: item.total_jamaah,
              id_outlet: item.id_outlet
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchPackages(){
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.travel.keberangkatan.packages")); ?>');
            const data = await response.json();
            this.packages = data;
          } catch (error) {
            console.error('Error fetching packages:', error);
          }
        },

        async fetchOutlets(){
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.outlet.data")); ?>');
            const data = await response.json();
            this.outlets = data.data.map(item => ({
              id_outlet: item.id_outlet || item.id,
              nama_outlet: item.name || item.nama_outlet
            }));
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        openCreate(){ 
          this.form = { 
            id: null, 
            keberangkatan_code: '', 
            keberangkatan_name: '', 
            id_travel_package: '', 
            departure_date: '', 
            return_date: '',
            total_jamaah: '',
            status: 'planning',
            id_outlet: ''
          }; 
          this.selectedPackage = null;
          this.errors = {};
          this.showForm = true; 
        },

        onPackageChange() {
          if (!this.form.id_travel_package) {
            this.selectedPackage = null;
            this.form.total_jamaah = '';
            this.form.departure_date = '';
            this.form.return_date = '';
            this.form.keberangkatan_code = '';
            return;
          }

          // Find selected package
          this.selectedPackage = this.packages.find(p => p.id == this.form.id_travel_package);
          
          if (this.selectedPackage) {
            // Auto-fill capacity
            this.form.total_jamaah = this.selectedPackage.capacity;
            
            // Auto-fill dates from package (convert to yyyy-MM-dd format)
            if (this.selectedPackage.departure_date) {
              this.form.departure_date = this.formatDateForInput(this.selectedPackage.departure_date);
            }
            if (this.selectedPackage.return_date) {
              this.form.return_date = this.formatDateForInput(this.selectedPackage.return_date);
            }
            
            // Generate keberangkatan code
            this.generateKeberangkatanCode();
          }
        },

        formatDateForInput(dateString) {
          if (!dateString) return '';
          
          // Jika sudah format yyyy-MM-dd, langsung return
          if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return dateString;
          
          // Handle ISO format (2026-02-23T17:00:00.000000Z) - ambil bagian tanggal saja
          if (dateString.includes('T')) {
            return dateString.split('T')[0];
          }
          
          // Fallback: parse dengan Date tapi gunakan UTC
          const date = new Date(dateString);
          if (isNaN(date.getTime())) return '';
          
          const year = date.getUTCFullYear();
          const month = String(date.getUTCMonth() + 1).padStart(2, '0');
          const day = String(date.getUTCDate()).padStart(2, '0');
          
          return `${year}-${month}-${day}`;
        },

        async generateKeberangkatanCode() {
          if (!this.form.id_travel_package) return;
          
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.travel.keberangkatan.generate-code")); ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify({
                package_id: this.form.id_travel_package
              })
            });
            
            const result = await response.json();
            if (response.ok && result.code) {
              this.form.keberangkatan_code = result.code;
            }
          } catch (error) {
            console.error('Error generating code:', error);
          }
        },

        calculateReturnDate() {
          // This method is now deprecated since we use dates from package
          // Kept for backward compatibility
          if (!this.form.departure_date || !this.selectedPackage || !this.selectedPackage.duration_days) {
            return;
          }

          // Only calculate if package doesn't have return_date
          if (!this.selectedPackage.return_date) {
            const departureDate = new Date(this.form.departure_date);
            const returnDate = new Date(departureDate);
            returnDate.setDate(returnDate.getDate() + parseInt(this.selectedPackage.duration_days));
            this.form.return_date = returnDate.toISOString().split('T')[0];
          }
        },

        async openEdit(k){ 
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.travel.keberangkatan.show', '')); ?>/${k.id}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const data = await response.json();
            
            this.form = { 
              id: data.id,
              keberangkatan_code: data.keberangkatan_code, 
              keberangkatan_name: data.keberangkatan_name, 
              id_travel_package: data.id_travel_package, 
              departure_date: data.departure_date, 
              return_date: data.return_date,
              total_jamaah: data.total_jamaah,
              status: data.status,
              id_outlet: data.id_outlet
            };
            
            // Set selected package
            this.selectedPackage = this.packages.find(p => p.id == data.id_travel_package);
            
            this.errors = {};
            this.showForm = true;
          } catch (error) {
            console.error('Error loading keberangkatan data:', error);
            this.showToastMessage('Gagal memuat data keberangkatan', 'error');
          }
        },

        async showDetail(k){
          window.location.href = `<?php echo e(route('admin.inventaris.travel.keberangkatan.show', '')); ?>/${k.id}`;
        },

        closeForm(){ 
          this.showForm = false; 
          this.selectedPackage = null;
          this.errors = {};
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          // Debug: log data yang akan dikirim
          console.log('Submitting keberangkatan form:', JSON.stringify(this.form));
          
          // Validasi frontend sebelum kirim
          const frontErrors = {};
          if (!this.form.keberangkatan_name) frontErrors.keberangkatan_name = 'Nama keberangkatan wajib diisi';
          if (!this.form.id_travel_package) frontErrors.id_travel_package = 'Paket perjalanan wajib dipilih';
          if (!this.form.departure_date) frontErrors.departure_date = 'Tanggal keberangkatan wajib diisi';
          if (!this.form.return_date) frontErrors.return_date = 'Tanggal kepulangan wajib diisi';
          if (!this.form.total_jamaah) frontErrors.total_jamaah = 'Kapasitas jamaah wajib diisi';
          if (!this.form.id_outlet) frontErrors.id_outlet = 'Outlet wajib dipilih';
          
          if (Object.keys(frontErrors).length > 0) {
            this.errors = frontErrors;
            this.saving = false;
            return;
          }

          try {
            const url = this.form.id 
              ? `<?php echo e(route('admin.inventaris.travel.keberangkatan.update', '')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.inventaris.travel.keberangkatan.store")); ?>';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                // Normalize errors: convert array values to string
                const normalized = {};
                Object.keys(result.errors).forEach(k => {
                  const v = result.errors[k];
                  normalized[k] = Array.isArray(v) ? v[0] : v;
                });
                this.errors = normalized;
                const errorMessages = Object.values(normalized).join('\n');
                alert('Validasi gagal:\n' + errorMessages);
              } else {
                const msg = result.message || result.error || 'Terjadi kesalahan';
                this.showToastMessage(msg, 'error');
                alert(msg);
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(k){ 
          this.toDelete = k; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.travel.keberangkatan.destroy', '')); ?>/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || result.error || 'Gagal menghapus data', 'error');
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

<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\keberangkatan\index.blade.php ENDPATH**/ ?>