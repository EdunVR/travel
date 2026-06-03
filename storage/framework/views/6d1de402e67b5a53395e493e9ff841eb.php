<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Paket Perjalanan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Paket Perjalanan')]); ?>
  <div x-data="packageCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Paket Perjalanan</h1>
        <p class="text-slate-600 text-sm">Kelola paket Hajj dan Umrah dengan perhitungan HPP.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.package.create')): ?>
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Paket
        </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-3">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari kode, nama paket…"
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-2">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Outlet</option>
            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="lg:col-span-2">
          <select x-model="typeFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Tipe</option>
            <option value="hajj">Hajj</option>
            <option value="umrah">Umrah</option>
          </select>
        </div>
        <div class="lg:col-span-3">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="full">Full</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="lg:col-span-2">
          <select x-model="sortDir" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="desc">Terbaru</option>
            <option value="asc">Terlama</option>
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

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Kode</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Nama Paket</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Tipe</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Tanggal</th>
              <th class="px-2 py-2.5 text-center font-medium text-slate-600 uppercase tracking-wide">Kap.</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">HPP/org</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Harga Jual</th>
              <th class="px-2 py-2.5 text-center font-medium text-slate-600 uppercase tracking-wide">Profit</th>
              <th class="px-2 py-2.5 text-center font-medium text-slate-600 uppercase tracking-wide">Status</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="pkg in packages" :key="pkg.id">
              <tr class="hover:bg-slate-50 transition-colors"
                  :class="pkg.has_recent_booking ? 'bg-green-50 border-l-4 border-l-green-500' : ''">
                <td class="px-2 py-2 font-mono text-xs text-slate-500 whitespace-nowrap" x-text="pkg.package_code"></td>
                <td class="px-2 py-2 font-medium text-sm max-w-[180px]">
                  <div class="truncate" x-text="pkg.package_name" :title="pkg.package_name"></div>
                </td>
                <td class="px-2 py-2 whitespace-nowrap">
                  <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                        :class="pkg.package_type === 'hajj' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                        x-text="pkg.package_type === 'hajj' ? 'Hajj' : 'Umrah'"></span>
                </td>
                <td class="px-2 py-2 whitespace-nowrap">
                  <div x-text="pkg.departure_date"></div>
                  <div class="text-slate-400" x-text="pkg.return_date"></div>
                </td>
                <td class="px-2 py-2 text-center whitespace-nowrap">
                  <span x-text="pkg.booked_count"></span><span class="text-slate-400">/</span><span x-text="pkg.capacity"></span>
                </td>
                <td class="px-2 py-2 font-medium whitespace-nowrap" x-text="pkg.hpp_formatted"></td>
                <td class="px-2 py-2 font-medium text-green-600 max-w-[140px]" x-html="pkg.selling_price_formatted"></td>
                <td class="px-2 py-2 text-center whitespace-nowrap">
                  <span :class="pkg.profit_margin >= 0 ? 'text-green-600' : 'text-red-600'" x-text="pkg.profit_margin_formatted"></span>
                </td>
                <td class="px-2 py-2 text-center" x-html="pkg.status_badge"></td>
                <td class="px-2 py-2">
                  <div class="flex items-center justify-end gap-0.5">
                    <a :href="`<?php echo e(url('admin/inventaris/travel/package')); ?>/${pkg.id}/detail`" class="p-1 rounded hover:bg-slate-100" title="Detail">
                      <i class='bx bx-show text-base text-slate-600'></i>
                    </a>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.package.update')): ?>
                    <button x-on:click="openEdit(pkg)" class="p-1 rounded hover:bg-slate-100" title="Edit">
                      <i class='bx bx-edit text-base text-blue-600'></i>
                    </button>
                    <button x-on:click="openHppModal(pkg)" class="p-1 rounded hover:bg-blue-50" title="Kelola HPP">
                      <i class='bx bx-calculator text-base text-blue-600'></i>
                    </button>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.package.delete')): ?>
                    <button x-on:click="confirmDelete(pkg)" class="p-1 rounded hover:bg-slate-100" title="Hapus">
                      <i class='bx bx-trash text-base text-red-600'></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3 text-sm text-slate-600">
          <span>Tampilkan</span>
          <select x-model.number="perPage" @change="currentPage=1; fetchData()"
                  class="rounded-lg border border-slate-200 px-2 py-1 text-xs">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          <span>data per halaman &bull; Total: <strong x-text="total"></strong></span>
          <span x-show="hasRecentBooking" class="flex items-center gap-1 text-xs text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
            <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span> Hijau = ada booking baru (7 hari terakhir)
          </span>
        </div>
        <div class="flex items-center gap-1">
          <button @click="goToPage(1)" :disabled="currentPage===1"
                  class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 text-xs">«</button>
          <button @click="prevPage()" :disabled="currentPage===1"
                  class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 text-xs">‹</button>
          <template x-for="p in pageNumbers()" :key="p">
            <button @click="goToPage(p)"
                    :class="p===currentPage ? 'bg-primary-600 text-white border-primary-600' : 'border-slate-200 hover:bg-slate-50'"
                    class="px-2.5 py-1 rounded-lg border text-xs min-w-[32px]"
                    x-text="p"></button>
          </template>
          <button @click="nextPage()" :disabled="currentPage===lastPage"
                  class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 text-xs">›</button>
          <button @click="goToPage(lastPage)" :disabled="currentPage===lastPage"
                  class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-40 text-xs">»</button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && packages.length === 0" class="text-center py-12">
      <i class='bx bx-package text-6xl text-slate-300'></i>
      <p class="mt-4 text-slate-600">Belum ada paket perjalanan</p>
      <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.package.create')): ?>
      <button x-on:click="openCreate()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
        <i class='bx bx-plus-circle'></i> Tambah Paket Pertama
      </button>
      <?php endif; ?>
    </div>

    <!-- Debug: Verify Alpine scope -->
    <div x-show="false" x-init="console.log('✅ Alpine scope active, methods available:', Object.keys($data).filter(k => typeof $data[k] === 'function').slice(0, 10))"></div>
  </div>

  <?php $__env->startPush('scripts'); ?>
  <script>
    // HPP Custom Components Fix V2.0.2 - INLINE VERSION - TIMESTAMP: <?php echo e(time()); ?>

    // FORCE RELOAD: Press Ctrl+Shift+R if you don't see console message below
    
    window.hppCustomComponentsV2 = {
      version: '2.0.2-inline-<?php echo e(time()); ?>',
      
      buildPayload(hppForm, hppExtraComponents, selectedPackage) {
        const extraMap = {};
        const payStatusMap = {};
        const hutangMap = {};
        const customComponents = []; // Array untuk custom components dengan label
        
        const knownKeys = [
          'transportation_cost',
          'meal_cost',
          'visa_cost',
          'guide_cost',
          'insurance_cost',
          'operational_overhead',
          'contingency'
        ];
        
        // Initialize known keys
        knownKeys.forEach(k => extraMap[k] = 0);
        
        // Process all components
        hppExtraComponents.forEach(c => {
          if (knownKeys.includes(c.id)) {
            // Known component - add to extraMap
            extraMap[c.id] = (extraMap[c.id] || 0) + (parseFloat(c.value) || 0);
            payStatusMap[c.id] = 'hutang';
            hutangMap[c.id] = (parseFloat(c.value) || 0) * (selectedPackage?.capacity || 1);
          } else if (c.id.startsWith('custom_')) {
            // Custom component - add to customComponents array with label
            customComponents.push({
              id: c.id,
              label: c.label || 'Biaya Lainnya',
              value: parseFloat(c.value) || 0,
              payment_status: 'hutang',
              hutang_amount: (parseFloat(c.value) || 0) * (selectedPackage?.capacity || 1),
            });
          }
        });
        
        const payload = {
          flight_cost: hppForm.flight_cost || 0,
          hotel_cost: hppForm.hotel_cost || 0,
          ...extraMap,
          custom_components: customComponents, // IMPORTANT: Send as array
          component_payment_status: payStatusMap,
          component_hutang_amount: hutangMap,
        };
        
        console.log('=== HPP PAYLOAD V2.0.2 INLINE ===');
        console.log('TIMESTAMP:', '<?php echo e(time()); ?>');
        console.log('Custom components count:', customComponents.length);
        console.log('Custom components:', customComponents);
        console.log('Full payload:', payload);
        console.log('==================================');
        
        return payload;
      }
    };
    
    // IMMEDIATE CONSOLE OUTPUT TO VERIFY LOAD
    console.log('%c✓✓✓ HPP Custom Components V2.0.2 INLINE LOADED ✓✓✓', 'color: green; font-size: 16px; font-weight: bold');
    console.log('%cTIMESTAMP: <?php echo e(time()); ?>', 'color: blue; font-size: 14px');
    console.log('%cIf you see this, the fix is loaded!', 'color: green; font-size: 12px');
    
    // GLOBAL BRIDGE: Store Alpine component reference for modal access
    window.packageCrudInstance = null;
    
    // Version: 2.0.1 - Custom Components Fix (INLINE)
    function packageCrud() {
      console.log('%c🚀 packageCrud() FUNCTION CALLED', 'color: cyan; font-size: 16px; font-weight: bold');
      
      const instance = {
        loading: false,
        packages: [],
        search: '',
        outletFilter: 'ALL',
        typeFilter: 'ALL',
        statusFilter: 'ALL',
        sortDir: 'desc',
        currentPage: 1,
        lastPage: 1,
        total: 0,
        perPage: 10,
        hasRecentBooking: false,

        // HPP Modal State
        showHppModal: false,
        loadingHpp: false,
        savingHpp: false,
        lockingHpp: false,
        showLockConfirm: false,
        selectedPackage: null,
        hppLocked: false,
        availableFlights: [],
        availableHotels: [],
        availableSaudiTransports: [],
        selectedFlightId: '',
        selectedHotelId: '',
        selectedFlightPrice: 0,
        selectedHotelPrice: 0,
        // Split flight/hotel/transport state
        flightDeparture: { id: '', price: 0, manual: 0 },
        flightReturn:    { id: '', price: 0, manual: 0 },
        hotelMakkah:     { id: '', price_per_night: 0, manual: 0, nights: 0 },
        hotelMadinah:    { id: '', price_per_night: 0, manual: 0, nights: 0 },
        saudiTransportSelected: { id: '', price: 0, manual: 0 },
        hppForm: {
          flight_cost: 0,
          hotel_cost: 0,
          transportation_cost: 0,
          meal_cost: 0,
          visa_cost: 0,
          guide_cost: 0,
          insurance_cost: 0,
          operational_overhead: 0,
          contingency: 0
        },
        hppErrors: {},
        hppExtraComponents: [],

        init() {
          console.log('%c🎬 INIT() CALLED - Alpine is initializing', 'color: magenta; font-size: 14px; font-weight: bold');
          this.fetchData();
          
          // Listen for submit-hpp-form event from modal using Alpine's event system
          this.$el.addEventListener('submit-hpp-form', (event) => {
            console.log('%c📨 submit-hpp-form EVENT RECEIVED', 'color: yellow; font-size: 14px; font-weight: bold');
            console.log('Event detail:', event.detail);
            this.submitHppForm();
          });
          
          console.log('%c✅ Event listener attached to $el', 'color: lime; font-size: 12px');
        },

        async fetchData() {
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              type_filter: this.typeFilter,
              status_filter: this.statusFilter,
              sort_dir: this.sortDir,
              page: this.currentPage,
              length: this.perPage,   // DataTables uses 'length' for page size
              start: (this.currentPage - 1) * this.perPage,
            });

            const url = '<?php echo e(route('admin.inventaris.travel.package.data')); ?>';
            const response = await fetch(`${url}?${params}`);
            
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            // DataTables response format
            if (data.data) {
              this.packages = data.data.map(pkg => ({
                id: pkg.id,
                package_code: pkg.package_code,
                package_name: pkg.package_name,
                package_type: pkg.package_type,
                departure_date: pkg.departure_date,
                return_date: pkg.return_date,
                capacity: parseInt(pkg.capacity) || 0,
                booked_count: pkg.booked_count || 0,
                hpp_formatted: pkg.hpp || 'Rp 0',
                selling_price_formatted: pkg.price || 'Rp 0',
                price: parseFloat(pkg.price_raw) || 0,
                hpp: parseFloat(pkg.hpp_raw) || 0,
                profit_margin: parseFloat(pkg.profit_margin_raw) || 0,
                profit_margin_formatted: (parseFloat(pkg.profit_margin_raw) || 0).toFixed(2) + '%',
                status_badge: pkg.status || 'draft',
                id_outlet: pkg.id_outlet,
                duration_days: parseInt(pkg.duration_days) || 0,
                id_flight_departure: pkg.id_flight_departure || pkg.id_flight || null,
                id_flight_return: pkg.id_flight_return || null,
                id_hotel_makkah: pkg.id_hotel_makkah || pkg.id_hotel || null,
                id_hotel_room_type_makkah: pkg.id_hotel_room_type_makkah || pkg.id_hotel_room_type || null,
                id_hotel_madinah: pkg.id_hotel_madinah || null,
                id_hotel_room_type_madinah: pkg.id_hotel_room_type_madinah || null,
                id_flight: pkg.id_flight || pkg.id_flight_departure || null,
                id_hotel: pkg.id_hotel || pkg.id_hotel_makkah || null,
                id_hotel_room_type: pkg.id_hotel_room_type || pkg.id_hotel_room_type_makkah || null,
                price_packages: pkg.price_packages || [],
                id_saudi_transport: pkg.id_saudi_transport || null,
                saudi_transport_price: parseFloat(pkg.saudi_transport_price) || 0,
                has_recent_booking: !!pkg.has_recent_booking,
              }));
              this.total = data.recordsTotal || data.recordsFiltered || data.data.length;
              const filtered = data.recordsFiltered || this.total;
              this.lastPage = Math.max(1, Math.ceil(filtered / this.perPage));
              // Check if any package has recent booking (for legend display)
              this.hasRecentBooking = this.packages.some(p => p.has_recent_booking);
            }
          } catch (error) {
            console.error('Error fetching packages:', error);
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', 'Gagal memuat data paket', 'error');
            }
          } finally {
            this.loading = false;
          }
        },

        openCreate() {
          window.location.href = '<?php echo e(route('admin.inventaris.travel.package.create')); ?>';
        },

        openEdit(pkg) {
          const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
          window.location.href = `${baseUrl}/${pkg.id}/edit`;
        },

        async confirmDelete(pkg) {
          if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
              title: 'Hapus Paket?',
              text: `Yakin ingin menghapus paket "${pkg.package_name}"?`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Ya, Hapus!',
              cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
              await this.deletePackage(pkg.id, pkg.package_name);
            }
          } else {
            if (confirm(`Yakin ingin menghapus paket "${pkg.package_name}"?`)) {
              await this.deletePackage(pkg.id, pkg.package_name);
            }
          }
        },

        async deletePackage(id, packageName, force = false) {
          try {
            const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
            const url = force ? `${baseUrl}/${id}?force=1` : `${baseUrl}/${id}`;
            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              }
            });

            const data = await response.json();

            if (response.ok) {
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil!', 'Paket berhasil dihapus', 'success');
              } else {
                alert('Paket berhasil dihapus');
              }
              this.fetchData();
            } else if (response.status === 400 && data.code === 'ACTIVE_BOOKINGS_EXIST') {
              // Ada booking aktif — tawarkan force delete
              if (typeof Swal !== 'undefined') {
                const forceResult = await Swal.fire({
                  title: 'Ada Booking Aktif',
                  html: `Paket <strong>${packageName}</strong> memiliki booking aktif.<br><br>Hapus paksa akan membatalkan semua booking terkait. Lanjutkan?`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#6b7280',
                  confirmButtonText: 'Hapus Paksa',
                  cancelButtonText: 'Batal'
                });
                if (forceResult.isConfirmed) {
                  await this.deletePackage(id, packageName, true);
                }
              } else {
                if (confirm('Ada booking aktif. Hapus paksa dan batalkan semua booking?')) {
                  await this.deletePackage(id, packageName, true);
                }
              }
            } else {
              const msg = data.message || data.error || 'Gagal menghapus paket';
              if (typeof Swal !== 'undefined') {
                Swal.fire('Gagal', msg, 'error');
              } else {
                alert(msg);
              }
            }
          } catch (error) {
            console.error('Error deleting package:', error);
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', 'Gagal menghapus paket', 'error');
            } else {
              alert('Gagal menghapus paket');
            }
          }
        },

        prevPage() {
          if (this.currentPage > 1) {
            this.currentPage--;
            this.fetchData();
          }
        },

        nextPage() {
          if (this.currentPage < this.lastPage) {
            this.currentPage++;
            this.fetchData();
          }
        },

        goToPage(p) {
          if (p >= 1 && p <= this.lastPage && p !== this.currentPage) {
            this.currentPage = p;
            this.fetchData();
          }
        },

        pageNumbers() {
          // Show at most 5 page buttons centered around current page
          const range = 2;
          let start = Math.max(1, this.currentPage - range);
          let end   = Math.min(this.lastPage, this.currentPage + range);
          // Extend if near beginning/end
          if (end - start < range * 2) {
            if (start === 1) end = Math.min(this.lastPage, start + range * 2);
            else start = Math.max(1, end - range * 2);
          }
          const pages = [];
          for (let i = start; i <= end; i++) pages.push(i);
          return pages;
        },

        // HPP Management Functions
        async openHppModal(pkg) {
          // Ensure numeric values are properly parsed
          this.selectedPackage = {
            ...pkg,
            capacity: parseInt(pkg.capacity) || 0,
            price: parseFloat(pkg.price) || 0,
            // Support both new field names and legacy
            id_flight_departure: pkg.id_flight_departure || pkg.id_flight || null,
            id_flight_return: pkg.id_flight_return || null,
            id_hotel_makkah: pkg.id_hotel_makkah || pkg.id_hotel || null,
            id_hotel_room_type_makkah: pkg.id_hotel_room_type_makkah || pkg.id_hotel_room_type || null,
            id_hotel_madinah: pkg.id_hotel_madinah || null,
            id_hotel_room_type_madinah: pkg.id_hotel_room_type_madinah || null,
            id_flight: pkg.id_flight || pkg.id_flight_departure || null,
            id_hotel: pkg.id_hotel || pkg.id_hotel_makkah || null,
            id_hotel_room_type: pkg.id_hotel_room_type || pkg.id_hotel_room_type_makkah || null,
            duration_days: parseInt(pkg.duration_days) || 0,
            id_outlet: pkg.id_outlet || null,
            id_saudi_transport: pkg.id_saudi_transport || null,
            saudi_transport_price: parseFloat(pkg.saudi_transport_price) || 0,
          };

          // Reset split state
          this.flightDeparture = { id: '', price: 0, manual: 0 };
          this.flightReturn    = { id: '', price: 0, manual: 0 };
          this.hotelMakkah     = { id: '', price_per_night: 0, manual: 0, nights: 0 };
          this.hotelMadinah    = { id: '', price_per_night: 0, manual: 0, nights: 0 };
          this.saudiTransportSelected = { id: '', price: 0, manual: 0 };
          
          this.showHppModal = true;
          
          await Promise.all([
            this.fetchHppData(),
            this.loadFlights(),
            this.loadHotels(),
            this.loadSaudiTransports()
          ]);
          
          await this.$nextTick();
          this.autoFillFlightHotel();
          this.autoFillSaudiTransport();
        },

        autoFillFlightHotel() {
          const pkg = this.selectedPackage;
          const duration = parseInt(pkg.duration_days) || 1;

          // Flight Keberangkatan
          if (pkg.id_flight_departure) {
            const f = this.availableFlights.find(x => x.id == pkg.id_flight_departure);
            if (f) {
              this.flightDeparture.id = f.id;
              this.flightDeparture.price = parseFloat(f.price_per_person) || 0;
            }
          }
          // Flight Kepulangan
          if (pkg.id_flight_return) {
            const f = this.availableFlights.find(x => x.id == pkg.id_flight_return);
            if (f) {
              this.flightReturn.id = f.id;
              this.flightReturn.price = parseFloat(f.price_per_person) || 0;
            }
          }
          // Hotel Mekkah
          if (pkg.id_hotel_room_type_makkah) {
            const h = this.availableHotels.find(x => x.id == pkg.id_hotel_room_type_makkah);
            if (h) {
              this.hotelMakkah.id = h.id;
              this.hotelMakkah.price_per_night = parseFloat(h.price_per_night) || 0;
            }
          }
          this.hotelMakkah.nights = duration;
          // Hotel Madinah
          if (pkg.id_hotel_room_type_madinah) {
            const h = this.availableHotels.find(x => x.id == pkg.id_hotel_room_type_madinah);
            if (h) {
              this.hotelMadinah.id = h.id;
              this.hotelMadinah.price_per_night = parseFloat(h.price_per_night) || 0;
            }
          }
          this.hotelMadinah.nights = duration;

          // Sync ke hppForm jika belum ada nilai
          this.syncFlightHotelToForm();
        },

        async autoFillSaudiTransport() {
          const pkg = this.selectedPackage;
          if (!pkg?.id_saudi_transport) return;
          const price = parseFloat(pkg.saudi_transport_price) || 0;
          // Set dropdown ke transport paket jika ada di list
          const t = this.availableSaudiTransports.find(x => x.id == pkg.id_saudi_transport);
          if (t) {
            this.saudiTransportSelected.id = t.id;
            this.saudiTransportSelected.price = parseFloat(t.price_per_person) || price;
          } else if (price) {
            this.saudiTransportSelected.id = '';
            this.saudiTransportSelected.manual = price;
          }
          this.syncSaudiTransportToComp();
        },

        onSaudiTransportSelected() {
          const t = this.availableSaudiTransports.find(x => x.id == this.saudiTransportSelected.id);
          this.saudiTransportSelected.price = t ? (parseFloat(t.price_per_person) || 0) : 0;
          this.syncSaudiTransportToComp();
        },

        syncSaudiTransportToComp() {
          const val = this.saudiTransportSelected.id
            ? this.saudiTransportSelected.price
            : (parseFloat(this.saudiTransportSelected.manual) || 0);
          const comp = this.hppExtraComponents.find(c => c.id === 'transportation_cost');
          if (comp) comp.value = val;
        },

        async loadSaudiTransports() {
          try {
            const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.saudi-transports')); ?>';
            const response = await fetch(url);
            if (response.ok) {
              this.availableSaudiTransports = await response.json();
            }
          } catch (error) {
            console.error('Error loading saudi transports:', error);
          }
        },

        syncFlightHotelToForm() {
          const fd = this.flightDeparture;
          const fr = this.flightReturn;
          const hm = this.hotelMakkah;
          const hd = this.hotelMadinah;

          const flightTotal = (fd.id ? fd.price : (parseFloat(fd.manual)||0))
                            + (fr.id ? fr.price : (parseFloat(fr.manual)||0));
          const hotelTotal  = ((hm.id ? hm.price_per_night : (parseFloat(hm.manual)||0)) * (parseInt(hm.nights)||0))
                            + ((hd.id ? hd.price_per_night : (parseFloat(hd.manual)||0)) * (parseInt(hd.nights)||0));

          if (this.hppForm.flight_cost === 0) this.hppForm.flight_cost = flightTotal;
          if (this.hppForm.hotel_cost  === 0) this.hppForm.hotel_cost  = hotelTotal;
        },

        onFlightDepartureSelected() {
          const f = this.availableFlights.find(x => x.id == this.flightDeparture.id);
          this.flightDeparture.price = f ? (parseFloat(f.price_per_person)||0) : 0;
          this.recalcFlightCost();
        },
        onFlightReturnSelected() {
          const f = this.availableFlights.find(x => x.id == this.flightReturn.id);
          this.flightReturn.price = f ? (parseFloat(f.price_per_person)||0) : 0;
          this.recalcFlightCost();
        },
        recalcFlightCost() {
          const fd = this.flightDeparture;
          const fr = this.flightReturn;
          this.hppForm.flight_cost = (fd.id ? fd.price : (parseFloat(fd.manual)||0))
                                   + (fr.id ? fr.price : (parseFloat(fr.manual)||0));
        },
        calcFlightTotal() { return this.recalcFlightCost(), this.hppForm.flight_cost; },

        onHotelMakkahSelected() {
          const h = this.availableHotels.find(x => x.id == this.hotelMakkah.id);
          this.hotelMakkah.price_per_night = h ? (parseFloat(h.price_per_night)||0) : 0;
          this.recalcHotelCost();
        },
        onHotelMadinahSelected() {
          const h = this.availableHotels.find(x => x.id == this.hotelMadinah.id);
          this.hotelMadinah.price_per_night = h ? (parseFloat(h.price_per_night)||0) : 0;
          this.recalcHotelCost();
        },
        recalcHotelCost() {
          const hm = this.hotelMakkah;
          const hd = this.hotelMadinah;
          this.hppForm.hotel_cost = ((hm.id ? hm.price_per_night : (parseFloat(hm.manual)||0)) * (parseInt(hm.nights)||0))
                                  + ((hd.id ? hd.price_per_night : (parseFloat(hd.manual)||0)) * (parseInt(hd.nights)||0));
        },
        calcHotelTotal() { return this.recalcHotelCost(), this.hppForm.hotel_cost; },

        async loadFlights() {
          try {
            const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.flights')); ?>';
            const params = new URLSearchParams();
            
            // Add outlet filter if package has outlet
            if (this.selectedPackage?.id_outlet) {
              params.append('outlet_id', this.selectedPackage.id_outlet);
            }
            
            const response = await fetch(`${url}?${params}`);
            if (response.ok) {
              this.availableFlights = await response.json();
            }
          } catch (error) {
            console.error('Error loading flights:', error);
          }
        },

        async loadHotels() {
          try {
            const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.hotels')); ?>';
            const params = new URLSearchParams();
            
            // Add outlet filter if package has outlet
            if (this.selectedPackage?.id_outlet) {
              params.append('outlet_id', this.selectedPackage.id_outlet);
            }
            
            const response = await fetch(`${url}?${params}`);
            if (response.ok) {
              this.availableHotels = await response.json();
            }
          } catch (error) {
            console.error('Error loading hotels:', error);
          }
        },

        closeHppModal() {
          this.showHppModal = false;
          this.selectedPackage = null;
          this.hppLocked = false;
          this.selectedFlightId = '';
          this.selectedHotelId = '';
          this.selectedFlightPrice = 0;
          this.selectedHotelPrice = 0;
          this.resetHppForm();
        },

        resetHppForm() {
          this.hppForm = {
            flight_cost: 0,
            hotel_cost: 0,
            transportation_cost: 0,
            meal_cost: 0,
            visa_cost: 0,
            guide_cost: 0,
            insurance_cost: 0,
            operational_overhead: 0,
            contingency: 0
          };
          this.hppErrors = {};
          this.hppExtraComponents = [];
          this.flightDeparture = { id: '', price: 0, manual: 0 };
          this.flightReturn    = { id: '', price: 0, manual: 0 };
          this.hotelMakkah     = { id: '', price_per_night: 0, manual: 0, nights: 0 };
          this.hotelMadinah    = { id: '', price_per_night: 0, manual: 0, nights: 0 };
          this.saudiTransportSelected = { id: '', price: 0, manual: 0 };
        },

        async fetchHppData() {
          if (!this.selectedPackage) return;
          
          this.loadingHpp = true;
          try {
            const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
            const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp`);
            
            if (response.ok) {
              const data = await response.json();
              this.hppForm = {
                flight_cost: parseFloat(data.flight_cost) || 0,
                hotel_cost: parseFloat(data.hotel_cost) || 0,
                transportation_cost: parseFloat(data.transportation_cost) || 0,
                meal_cost: parseFloat(data.meal_cost) || 0,
                visa_cost: parseFloat(data.visa_cost) || 0,
                guide_cost: parseFloat(data.guide_cost) || 0,
                insurance_cost: parseFloat(data.insurance_cost) || 0,
                operational_overhead: parseFloat(data.operational_overhead) || 0,
                contingency: parseFloat(data.contingency) || 0
              };
              this.hppLocked = data.is_locked || false;

              // Build extra components
              this.hppExtraComponents = [];
              const extras = [
                { key: 'transportation_cost', label: 'Biaya Transportasi', hint: 'Transportasi lokal per orang' },
                { key: 'meal_cost', label: 'Biaya Makan', hint: 'Biaya makan selama perjalanan' },
                { key: 'visa_cost', label: 'Biaya Visa', hint: 'Pengurusan visa per orang' },
                { key: 'guide_cost', label: 'Biaya Pembimbing', hint: 'Pembimbing/muthawif per orang' },
                { key: 'insurance_cost', label: 'Biaya Asuransi', hint: 'Asuransi perjalanan per orang' },
                { key: 'operational_overhead', label: 'Biaya Operasional', hint: 'Operasional & administrasi' },
                { key: 'contingency', label: 'Biaya Kontingensi', hint: 'Cadangan darurat per orang' },
              ];
              const payStatus = data.component_payment_status || {};
              const hutangAmt = data.component_hutang_amount || {};
              extras.forEach(e => {
                this.hppExtraComponents.push({
                  id: e.key, label: e.label, hint: e.hint,
                  value: parseFloat(data[e.key]) || 0, isDefault: true,
                  payment_status: payStatus[e.key] || 'hutang', // Default hutang
                  hutang_amount: parseFloat(hutangAmt[e.key]) || ((parseFloat(data[e.key]) || 0) * (this.selectedPackage?.capacity || 1)),
                });
              });
              
              // Load custom components
              const customComps = data.custom_components || [];
              customComps.forEach(c => {
                this.hppExtraComponents.push({
                  id: c.id,
                  label: c.label || 'Biaya Lainnya',
                  hint: 'Biaya per orang',
                  value: parseFloat(c.value) || 0,
                  isDefault: false,
                  payment_status: 'hutang',
                  hutang_amount: parseFloat(c.hutang_amount) || ((parseFloat(c.value) || 0) * (this.selectedPackage?.capacity || 1)),
                });
              });
            } else {
              this.initDefaultExtraComponents();
            }
          } catch (error) {
            console.error('Error fetching HPP data:', error);
            this.initDefaultExtraComponents();
          } finally {
            this.loadingHpp = false;
          }
        },

        initDefaultExtraComponents() {
          // Hanya init komponen default dengan value 0, akan disembunyikan di UI jika value = 0
          this.hppExtraComponents = [
            { id: 'transportation_cost', label: 'Biaya Transportasi', hint: 'Transportasi lokal per orang', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'meal_cost', label: 'Biaya Makan', hint: 'Biaya makan selama perjalanan', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'visa_cost', label: 'Biaya Visa', hint: 'Pengurusan visa per orang', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'guide_cost', label: 'Biaya Pembimbing', hint: 'Pembimbing/muthawif per orang', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'insurance_cost', label: 'Biaya Asuransi', hint: 'Asuransi perjalanan per orang', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'operational_overhead', label: 'Biaya Operasional', hint: 'Operasional & administrasi', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
            { id: 'contingency', label: 'Biaya Kontingensi', hint: 'Cadangan darurat per orang', value: 0, isDefault: true, payment_status: 'hutang', hutang_amount: 0 },
          ];
        },

        addExtraComponent() {
          // Komponen baru selalu default hutang
          this.hppExtraComponents.push({ 
            id: 'custom_' + Date.now(), 
            label: '', 
            hint: 'Biaya per orang', 
            value: 0, 
            isDefault: false, 
            payment_status: 'hutang', 
            hutang_amount: 0 
          });
        },

        removeExtraComponent(index) {
          this.hppExtraComponents.splice(index, 1);
        },

        getTotalExtraComponents() {
          return this.hppExtraComponents.reduce((sum, c) => sum + (parseFloat(c.value) || 0), 0);
        },

        testAlpineScope() {
          console.log('%c🧪 TEST: Alpine scope is working!', 'color: lime; font-size: 18px; font-weight: bold');
          alert('Alpine scope test: SUCCESS! The button can call Alpine methods.');
          return true;
        },

        async submitHppForm() {
          try {
            console.log('%c>>> SUBMIT HPP FORM CALLED <<<', 'color: red; font-size: 20px; font-weight: bold');
            
            if (this.hppLocked) {
              alert('HPP sudah terkunci dan tidak dapat diubah');
              return;
            }

            this.hppErrors = {};
            this.savingHpp = true;

            console.log('%c>>> BEFORE BUILD PAYLOAD <<<', 'color: orange; font-size: 16px');
            console.log('hppForm:', this.hppForm);
            console.log('hppExtraComponents:', this.hppExtraComponents);
            console.log('selectedPackage:', this.selectedPackage);
            console.log('window.hppCustomComponentsV2:', window.hppCustomComponentsV2);

            // Use external function to build payload (V2)
            let payload;
            try {
              payload = window.hppCustomComponentsV2.buildPayload(
                this.hppForm,
                this.hppExtraComponents,
                this.selectedPackage
              );
              console.log('%c>>> PAYLOAD BUILT SUCCESSFULLY <<<', 'color: green; font-size: 16px');
            } catch (err) {
              console.error('%c>>> ERROR BUILDING PAYLOAD <<<', 'color: red; font-size: 16px');
              console.error(err);
              alert('Error building payload: ' + err.message);
              this.savingHpp = false;
              return;
            }

            const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
            const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil!', 'HPP calculation berhasil disimpan', 'success');
              } else {
                alert('HPP calculation berhasil disimpan');
              }
              this.closeHppModal();
              this.fetchData(); // Refresh table
            } else {
              if (data.errors) {
                this.hppErrors = data.errors;
              } else {
                throw new Error(data.message || 'Failed to save HPP');
              }
            }
          } catch (error) {
            console.error('%c>>> FATAL ERROR IN SUBMIT <<<', 'color: red; font-size: 16px');
            console.error('Error saving HPP:', error);
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', 'Gagal menyimpan HPP calculation', 'error');
            } else {
              alert('Gagal menyimpan HPP calculation');
            }
          } finally {
            this.savingHpp = false;
          }
        },

        confirmLockHpp() {
          this.showLockConfirm = true;
        },

        async lockHppNow() {
          this.lockingHpp = true;

          try {
            const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
            const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp/lock`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              }
            });

            if (response.ok) {
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil!', 'HPP calculation berhasil dikunci', 'success');
              } else {
                alert('HPP calculation berhasil dikunci');
              }
              this.showLockConfirm = false;
              this.hppLocked = true;
            } else {
              throw new Error('Failed to lock HPP');
            }
          } catch (error) {
            console.error('Error locking HPP:', error);
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', 'Gagal mengunci HPP calculation', 'error');
            } else {
              alert('Gagal mengunci HPP calculation');
            }
          } finally {
            this.lockingHpp = false;
          }
        },

        calculateTotalHpp() {
          const capacity = this.selectedPackage?.capacity || 1;
          const flightOnly = (this.hppForm.flight_cost || 0);
          const extras = this.getTotalExtraComponents();
          return (flightOnly + extras) * capacity;
        },

        calculateProfitMargin() {
          const totalHpp = this.calculateTotalHpp();
          const capacity = this.selectedPackage?.capacity || 1;
          const price = this.selectedPackage?.price || 0;
          const totalRevenue = price * capacity;
          
          if (totalRevenue === 0) return 0;
          return ((totalRevenue - totalHpp) / totalRevenue) * 100;
        },

        formatCurrency(amount) {
          return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }
      };
      
      console.log('%c🎯 About to store instance globally', 'color: orange; font-size: 14px');
      console.log('Instance object created:', instance);
      console.log('Instance has submitHppForm:', typeof instance.submitHppForm);
      
      // Store instance globally for modal access
      window.packageCrudInstance = instance;
      console.log('%c✅ packageCrudInstance stored globally', 'color: lime; font-size: 14px; font-weight: bold');
      console.log('Available methods:', Object.keys(instance).filter(k => typeof instance[k] === 'function'));
      console.log('Test calling submitHppForm:', typeof instance.submitHppForm);
      console.log('window.packageCrudInstance check:', window.packageCrudInstance);
      
      return instance;
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/index.blade.php ENDPATH**/ ?>