<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Dashboard Penjualan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dashboard Penjualan')]); ?>
  <div x-data="salesDashboard()" x-init="init()" class="space-y-6 overflow-x-hidden">

    
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Dashboard Penjualan</h1>
          <p class="text-slate-600 text-sm">Ringkasan real-time dari Invoice, POS, Margin & Piutang</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
            <div class="relative">
              <button @click="showOutletDropdown = !showOutletDropdown" 
                      class="h-10 w-full rounded-xl border border-slate-200 px-3 text-left flex items-center justify-between bg-white hover:border-slate-300">
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
                             x-model="filter.selectedOutlets" 
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
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Dari Tanggal</label>
            <input type="date" x-model="filter.from" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Sampai Tanggal</label>
            <input type="date" x-model="filter.to" @change="loadData()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div class="flex items-end">
            <button @click="resetFilter()" :disabled="isLoading"
                    class="h-10 w-full rounded-xl border border-slate-200 px-3 hover:bg-slate-50 disabled:opacity-50">
              <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </section>

    
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Total Transaksi</div>
          <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
            <i class='bx bx-receipt text-blue-600'></i>
          </div>
        </div>
        <div class="mt-2 text-3xl font-bold" x-text="kpi.total_transaksi || 0"></div>
        <div class="mt-2 flex items-center gap-2">
          <span class="text-xs px-2 py-0.5 rounded-full" 
                :class="kpi.growth_percent >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
            <i class='bx' :class="kpi.growth_percent >= 0 ? 'bx-trending-up' : 'bx-trending-down'"></i>
            <span x-text="Math.abs(kpi.growth_percent || 0).toFixed(1) + '%'"></span>
          </span>
          <span class="text-xs text-slate-500">vs periode lalu</span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Total Item Terjual</div>
          <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
            <i class='bx bx-package text-emerald-600'></i>
          </div>
        </div>
        <div class="mt-2 text-3xl font-bold" x-text="formatNumber(kpi.total_item || 0)"></div>
        <div class="mt-2">
          <div class="text-xs text-slate-500">
            Rata-rata: <span class="font-semibold" x-text="formatNumber(kpi.total_transaksi > 0 ? Math.round(kpi.total_item / kpi.total_transaksi) : 0)"></span> item/transaksi
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Total Penjualan</div>
          <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
            <i class='bx bx-money text-indigo-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="idr(kpi.total_penjualan || 0)"></div>
        <div class="mt-2">
          <div class="text-xs text-slate-500">
            Rata-rata: <span class="font-semibold" x-text="idr(kpi.avg_transaksi || 0)"></span>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Piutang Belum Lunas</div>
          <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center">
            <i class='bx bx-time-five text-rose-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold text-rose-600" x-text="idr(kpi.total_piutang || 0)"></div>
        <div class="mt-2">
          <div class="text-xs text-slate-500">
            Dibayar: <span class="font-semibold text-emerald-600" x-text="idr(kpi.total_dibayar || 0)"></span>
          </div>
        </div>
      </div>
    </section>

    
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Penjualan per Outlet</div>
            <div class="text-xs text-slate-500" x-text="periodeLabel"></div>
          </div>
        </div>
        <div class="h-48 flex items-end gap-3">
          <template x-if="outletSummary.length === 0">
            <div class="w-full h-full flex items-center justify-center text-slate-400">
              <div class="text-center">
                <i class='bx bx-bar-chart text-4xl'></i>
                <p class="text-sm mt-2">Tidak ada data</p>
              </div>
            </div>
          </template>
          <template x-for="outlet in outletSummary" :key="outlet.name">
            <div class="flex-1 flex flex-col items-center group">
              <div class="text-xs font-medium text-slate-600 mb-1" x-text="idr(outlet.total)"></div>
              <div class="w-full rounded-t-lg bg-gradient-to-t from-primary-500 to-primary-400 hover:from-primary-600 hover:to-primary-500 transition cursor-pointer relative"
                   :style="`height:${outlet.height}px`"
                   :title="`${outlet.name}: ${idr(outlet.total)} (${outlet.count} transaksi)`">
                <div class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition" x-text="outlet.count">
                </div>
              </div>
              <div class="mt-2 text-xs text-slate-600 font-medium" x-text="outlet.name"></div>
            </div>
          </template>
        </div>
      </div>

      
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-sm font-semibold mb-4">Status Pembayaran</div>

        <div class="grid grid-cols-3 gap-3 mb-4">
          <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-3 text-center">
            <div class="text-xs text-emerald-600 font-medium">Lunas</div>
            <div class="text-2xl font-bold text-emerald-700" x-text="statusCount.lunas || 0"></div>
          </div>
          <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-3 text-center">
            <div class="text-xs text-amber-600 font-medium">Sebagian</div>
            <div class="text-2xl font-bold text-amber-700" x-text="statusCount.dibayar_sebagian || 0"></div>
          </div>
          <div class="rounded-xl border-2 border-rose-200 bg-rose-50 p-3 text-center">
            <div class="text-xs text-rose-600 font-medium">Belum Lunas</div>
            <div class="text-2xl font-bold text-rose-700" x-text="statusCount.belum_lunas || 0"></div>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-200">
          <div class="text-xs text-slate-500 mb-2">Tren 30 Hari Terakhir</div>
          <svg viewBox="0 0 300 60" class="w-full h-16">
            <path :d="trendPath" fill="rgba(99,102,241,0.1)" stroke="rgb(99,102,241)" stroke-width="2"></path>
          </svg>
        </div>
      </div>
    </section>

    
    <section class="rounded-2xl border border-slate-200 bg-white p-0 shadow-card">
      <div class="p-4 flex items-center justify-between border-b border-slate-200">
        <div>
          <div class="text-sm font-semibold">Transaksi Terbaru</div>
          <div class="text-xs text-slate-500">Menampilkan 10 transaksi terakhir</div>
        </div>
        <div class="flex gap-2">
          <a :href="`<?php echo e(route('admin.penjualan.laporan.index')); ?>`" 
             class="h-9 px-3 rounded-lg border border-slate-200 text-sm hover:bg-slate-50 flex items-center gap-1">
            <i class='bx bx-file'></i> Laporan Lengkap
          </a>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-[1000px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left w-12">No</th>
              <th class="px-3 py-2 text-left">Source</th>
              <th class="px-3 py-2 text-left">No Transaksi</th>
              <th class="px-3 py-2 text-left">Tanggal</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Customer</th>
              <th class="px-3 py-2 text-right">Item</th>
              <th class="px-3 py-2 text-right">Total</th>
              <th class="px-3 py-2 text-center">Status</th>
              <th class="px-3 py-2 text-right">Sisa</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="isLoading">
              <tr><td colspan="10" class="px-3 py-8 text-center">
                <div class="flex items-center justify-center gap-2">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                  <span class="text-slate-500">Memuat data...</span>
                </div>
              </td></tr>
            </template>

            <template x-if="!isLoading && salesData.length === 0">
              <tr><td colspan="10" class="px-3 py-8 text-center">
                <div class="text-slate-400">
                  <i class='bx bx-receipt text-4xl'></i>
                  <p class="mt-2">Tidak ada transaksi</p>
                </div>
              </td></tr>
            </template>

            <template x-for="(t,i) in salesData.slice(0, 10)" :key="t.id">
              <tr class="border-t hover:bg-slate-50">
                <td class="px-3 py-2" x-text="i+1"></td>
                <td class="px-3 py-2">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                        :class="t.source === 'invoice' ? 'bg-blue-100 text-blue-800' : 'bg-cyan-100 text-cyan-800'">
                    <i class='bx text-sm mr-1' :class="t.source === 'invoice' ? 'bx-file' : 'bx-store'"></i>
                    <span x-text="t.source === 'invoice' ? 'INV' : 'POS'"></span>
                  </span>
                </td>
                <td class="px-3 py-2 font-medium" x-text="t.no_transaksi"></td>
                <td class="px-3 py-2 text-slate-600" x-text="fmtd(t.tanggal)"></td>
                <td class="px-3 py-2" x-text="t.outlet"></td>
                <td class="px-3 py-2" x-text="t.customer"></td>
                <td class="px-3 py-2 text-right" x-text="t.total_item"></td>
                <td class="px-3 py-2 text-right font-semibold" x-text="idr(t.total)"></td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="badgeClass(t.status)"
                        x-text="t.status"></span>
                </td>
                <td class="px-3 py-2 text-right">
                  <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="t.sisa > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'"
                        x-text="idr(t.sisa)"></span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  
  <script>
    function salesDashboard(){
      return {
        isLoading: false,
        showOutletDropdown: false,
        outlets: <?php echo json_encode($outlets ?? [], 15, 512) ?>,
        salesData: [],
        kpi: {},
        outletSummary: [],
        statusCount: {},
        dailyTrend: [],
        filter: {
          selectedOutlets: [],
          from: '',
          to: ''
        },
        periodeLabel: '',
        trendPath: '',

        async init(){
          // Set default date range (30 days)
          const now = new Date();
          const from = new Date(now);
          from.setDate(now.getDate() - 30);
          this.filter.from = from.toISOString().slice(0, 10);
          this.filter.to = now.toISOString().slice(0, 10);
          
          // Initialize with first outlet selected
          if (this.outlets.length > 0) {
            this.filter.selectedOutlets = [this.outlets[0].id_outlet];
          }
          
          await this.loadData();
        },

        async loadData(){
          this.isLoading = true;
          try {
            const params = new URLSearchParams({
              start_date: this.filter.from,
              end_date: this.filter.to
            });

            // Add multiple outlet IDs
            if (this.filter.selectedOutlets.length > 0) {
              this.filter.selectedOutlets.forEach(outletId => {
                params.append('outlet_ids[]', outletId);
              });
            }

            const response = await fetch(`<?php echo e(route('admin.penjualan.dashboard.data')); ?>?${params}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();
            
            if (result.success) {
              this.salesData = result.data.sales || [];
              this.kpi = result.data.kpi || {};
              this.outletSummary = this.processOutletSummary(result.data.outlet_summary || []);
              this.statusCount = result.data.status_count || {};
              this.dailyTrend = result.data.daily_trend || [];
              this.updatePeriodeLabel();
              this.updateTrendPath();
            } else {
              this.showNotification('error', result.message || 'Gagal memuat data');
            }
          } catch (error) {
            console.error('Error loading dashboard:', error);
            this.showNotification('error', 'Terjadi kesalahan saat memuat data');
          } finally {
            this.isLoading = false;
          }
        },

        processOutletSummary(summary){
          if (summary.length === 0) return [];
          const max = Math.max(...summary.map(s => s.total), 1);
          return summary.map(s => ({
            ...s,
            height: Math.max(20, Math.round((s.total / max) * 160))
          }));
        },

        updatePeriodeLabel(){
          const from = this.fmtd(this.filter.from);
          const to = this.fmtd(this.filter.to);
          this.periodeLabel = `${from} — ${to}`;
        },

        updateTrendPath(){
          if (this.dailyTrend.length === 0) {
            this.trendPath = 'M0,60 L300,60';
            return;
          }

          const w = 300, h = 60;
          const max = Math.max(...this.dailyTrend.map(d => d.total), 1);
          const step = w / (this.dailyTrend.length - 1 || 1);

          let path = `M0,${h}`;
          this.dailyTrend.forEach((d, i) => {
            const x = i * step;
            const y = h - ((d.total / max) * (h - 10));
            path += ` L${x},${y}`;
          });
          path += ` L${w},${h} Z`;
          this.trendPath = path;
        },

        resetFilter(){
          this.filter.selectedOutlets = this.outlets.length > 0 ? [this.outlets[0].id_outlet] : [];
          this.filter.from = new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().slice(0,10);
          this.filter.to = new Date().toISOString().slice(0,10);
          this.loadData();
        },

        idr(n){ 
          const num = parseFloat(n) || 0;
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(num);
        },

        formatNumber(n){
          return (Number(n) || 0).toLocaleString('id-ID');
        },

        fmtd(s){ 
          const d = new Date(s); 
          return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          });
        },

        badgeClass(status){
          const s = (status || '').toLowerCase();
          if (s.includes('lunas')) return 'bg-emerald-100 text-emerald-700';
          if (s.includes('sebagian')) return 'bg-amber-100 text-amber-700';
          return 'bg-rose-100 text-rose-700';
        },

        getSelectedOutletText() {
          if (this.filter.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.filter.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet == this.filter.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Unknown';
          } else if (this.filter.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.filter.selectedOutlets.length} Outlet Dipilih`;
          }
        },

        selectAllOutlets() {
          this.filter.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.loadData();
        },

        clearAllOutlets() {
          this.filter.selectedOutlets = [];
          this.loadData();
        },

        showNotification(type, message) {
          const event = new CustomEvent('notify', {
            detail: { type, message }
          });
          window.dispatchEvent(event);
        }
      }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\index.blade.php ENDPATH**/ ?>