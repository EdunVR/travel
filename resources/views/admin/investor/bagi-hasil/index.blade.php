<x-layouts.admin :title="'Investor / Bagi Hasil'">
  <div x-data="investorBagiHasil()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Bagi Hasil Investor</h1>
        <p class="text-slate-600 text-sm">Kelola perhitungan dan distribusi bagi hasil investor</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @can('investor.bagi-hasil.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Bagi Hasil
        </button>
        @endcan
        
        @can('investor.bagi-hasil.export')
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        @endcan
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari investor, periode…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Outlet -->
        <div class="lg:col-span-3">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Outlet: Semua</option>
            @foreach($outlets as $outlet)
              <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
            @endforeach
          </select>
        </div>
        <!-- Filter Investor -->
        <div class="lg:col-span-3">
          <select x-model="investorFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Investor: Semua</option>
            @foreach($investors as $investor)
              <option value="{{ $investor->id }}">{{ $investor->name }}</option>
            @endforeach
          </select>
        </div>
        <!-- Filter Status -->
        <div class="lg:col-span-2">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="draft">Draft</option>
            <option value="approved">Disetujui</option>
            <option value="paid">Dibayar</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <!-- Sort -->
        <div class="grid grid-cols-2 gap-2 lg:col-span-6">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="period_month">Periode</option>
            <option value="investor_name">Investor</option>
            <option value="net_profit">Laba Bersih</option>
            <option value="investor_share_amount">Bagian Investor</option>
            <option value="status">Status</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
        </div>

        <!-- Toggle View -->
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
        <template x-for="item in bagiHasil" :key="item.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-chart-pie text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="item.investor_name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="getStatusClass(item.status)"
                        x-text="getStatusLabel(item.status)"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span x-text="item.outlet_name"></span> • <span x-text="item.period_formatted"></span>
                </div>
                <div class="mt-2 text-sm">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 border border-emerald-200">
                      <i class='bx bx-wallet'></i><span x-text="formatCurrency(item.net_profit)"></span>
                    </span>
                  </div>
                  <div class="text-slate-600 text-xs">
                    Investor: <span class="font-medium text-blue-600" x-text="formatCurrency(item.investor_share_amount)"></span> • 
                    Pengelola: <span class="font-medium text-green-600" x-text="formatCurrency(item.management_share_amount)"></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click.prevent="viewBagiHasil(item)" class="flex-1 rounded-lg bg-emerald-600 text-white px-3 py-2 hover:bg-emerald-700 text-sm">
                <i class='bx bx-show'></i> Detail
              </button>
              @can('investor.bagi-hasil.update')
              <button x-on:click="openEdit(item)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-edit-alt'></i> Edit
              </button>
              @endcan
              @can('investor.bagi-hasil.delete')
              <button x-on:click="confirmDelete(item)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm">
                <i class='bx bx-trash'></i> Hapus
              </button>
              @endcan
            </div>
          </div>
        </template>
      </div>
      <div x-show="bagiHasil.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3 w-12">No</th>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Investor</th>
              <th class="text-left px-4 py-3">Periode</th>
              <th class="text-left px-4 py-3">Laba Bersih</th>
              <th class="text-left px-4 py-3">Bagian Investor</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="text-left px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(item,i) in bagiHasil" :key="item.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="i+1"></td>
                <td class="px-4 py-3" x-text="item.outlet_name"></td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="font-medium" x-text="item.investor_name"></span>
                  </div>
                </td>
                <td class="px-4 py-3" x-text="item.period_formatted"></td>
                <td class="px-4 py-3">
                  <span class="text-green-600 font-medium" x-text="formatCurrency(item.net_profit)"></span>
                </td>
                <td class="px-4 py-3">
                  <span class="text-blue-600 font-medium" x-text="formatCurrency(item.investor_share_amount)"></span>
                </td>
                <td class="px-4 py-3">
                  <span :class="getStatusClass(item.status)" 
                        class="px-2 py-0.5 rounded-full text-xs border" 
                        x-text="getStatusLabel(item.status)"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    <button x-on:click.prevent="viewBagiHasil(item)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700 text-sm">
                      <i class='bx bx-show'></i> Detail
                    </button>
                    @can('investor.bagi-hasil.update')
                    <button x-on:click="openEdit(item)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                      <i class='bx bx-edit-alt'></i>
                    </button>
                    @endcan
                    @can('investor.bagi-hasil.delete')
                    <button x-on:click="confirmDelete(item)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                    @endcan
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="bagiHasil.length===0"><td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="closeForm()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Bagi Hasil' : 'Tambah Bagi Hasil'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Outlet —</option>
                @foreach($outlets as $outlet)
                  <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                @endforeach
              </select>
              <div x-show="errors.outlet_id" class="text-red-500 text-xs mt-1" x-text="errors.outlet_id"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Investor <span class="text-red-500">*</span></label>
              <select x-model="form.investor_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Investor —</option>
                @foreach($investors as $investor)
                  <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                @endforeach
              </select>
              <div x-show="errors.investor_id" class="text-red-500 text-xs mt-1" x-text="errors.investor_id"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Periode <span class="text-red-500">*</span></label>
              <input type="month" x-model="form.period_month" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.period_month" class="text-red-500 text-xs mt-1" x-text="errors.period_month"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Persentase Investor (%) <span class="text-red-500">*</span></label>
              <input type="number" x-model="form.investor_share_percentage" x-on:input="calculateShares()" min="0" max="100" step="0.01" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.investor_share_percentage" class="text-red-500 text-xs mt-1" x-text="errors.investor_share_percentage"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Total Pendapatan <span class="text-red-500">*</span></label>
              <input type="number" x-model="form.total_revenue" x-on:input="calculateShares()" min="0" step="0.01" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.total_revenue" class="text-red-500 text-xs mt-1" x-text="errors.total_revenue"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Total Pengeluaran <span class="text-red-500">*</span></label>
              <input type="number" x-model="form.total_expense" x-on:input="calculateShares()" min="0" step="0.01" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.total_expense" class="text-red-500 text-xs mt-1" x-text="errors.total_expense"></div>
            </div>
            
            <!-- Calculation Summary -->
            <div class="sm:col-span-2">
              <div class="bg-slate-50 rounded-xl p-4">
                <h4 class="text-sm font-medium text-slate-900 mb-3">Ringkasan Perhitungan</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div class="bg-white rounded-lg p-3 border">
                    <div class="text-slate-500">Laba Bersih</div>
                    <div class="font-medium text-lg" x-text="formatCurrency(calculatedNetProfit)"></div>
                  </div>
                  <div class="bg-white rounded-lg p-3 border">
                    <div class="text-slate-500">Bagian Investor</div>
                    <div class="font-medium text-lg text-blue-600" x-text="formatCurrency(calculatedInvestorShare)"></div>
                  </div>
                  <div class="bg-white rounded-lg p-3 border">
                    <div class="text-slate-500">Bagian Pengelola</div>
                    <div class="font-medium text-lg text-green-600" x-text="formatCurrency(calculatedManagementShare)"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Catatan</label>
              <textarea x-model="form.notes" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
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

    <!-- Modal Detail -->
    <div x-show="showDetailModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="showDetailModal=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate">Detail Bagi Hasil</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click.stop="showDetailModal=false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>
        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1" x-show="selectedBagiHasil">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-slate-500">Outlet</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedBagiHasil?.outlet_name || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Investor</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedBagiHasil?.investor_name || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Periode</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedBagiHasil?.period_formatted || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Status</label>
              <p class="mt-1 text-sm text-slate-900" x-text="getStatusLabel(selectedBagiHasil?.status)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Total Pendapatan</label>
              <p class="mt-1 text-sm text-slate-900" x-text="formatCurrency(selectedBagiHasil?.total_revenue || 0)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Total Pengeluaran</label>
              <p class="mt-1 text-sm text-slate-900" x-text="formatCurrency(selectedBagiHasil?.total_expense || 0)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Laba Bersih</label>
              <p class="mt-1 text-sm text-slate-900 font-medium" x-text="formatCurrency(selectedBagiHasil?.net_profit || 0)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Persentase Investor</label>
              <p class="mt-1 text-sm text-slate-900" x-text="(selectedBagiHasil?.investor_share_percentage || 0) + '%'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Bagian Investor</label>
              <p class="mt-1 text-sm text-blue-600 font-medium" x-text="formatCurrency(selectedBagiHasil?.investor_share_amount || 0)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Bagian Pengelola</label>
              <p class="mt-1 text-sm text-green-600 font-medium" x-text="formatCurrency(selectedBagiHasil?.management_share_amount || 0)"></p>
            </div>
          </div>
          
          <div class="mt-4" x-show="selectedBagiHasil?.notes">
            <label class="text-sm font-medium text-slate-500">Catatan</label>
            <p class="mt-1 text-sm text-slate-900" x-text="selectedBagiHasil?.notes"></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Bagi Hasil?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.investor_name"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="'Periode: ' + (toDelete?.period_formatted || '-') + ' • Laba: ' + formatCurrency(toDelete?.net_profit || 0)"></div>
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
    function investorBagiHasil(){
      return {
        // State management
        bagiHasil: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        outletFilter: 'ALL',
        investorFilter: 'ALL',
        statusFilter: 'ALL',
        sortKey: 'period_month',
        sortDir: 'desc',
        view: 'table',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          outlet_id: '', 
          investor_id: '', 
          period_month: new Date().toISOString().slice(0, 7),
          total_revenue: 0,
          total_expense: 0,
          investor_share_percentage: 50,
          notes: ''
        },
        errors: {},
        
        // Delete confirmation
        toDelete: null,

        // Detail modal
        showDetailModal: false,
        selectedBagiHasil: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        // Calculation
        calculatedNetProfit: 0,
        calculatedInvestorShare: 0,
        calculatedManagementShare: 0,

        async init(){
          try {
            await this.fetchData();
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              investor_filter: this.investorFilter,
              status_filter: this.statusFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`{{ route('admin.investor.bagi-hasil.index') }}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.bagiHasil = data.data.map(item => ({
                id: item.id,
                outlet_id: item.outlet_id,
                outlet_name: item.outlet_name || item.outlet?.nama_outlet || '-',
                investor_id: item.investor_id,
                investor_name: item.investor_name || item.investor?.name || '-',
                period_month: item.period_month,
                period_formatted: item.period_formatted || this.formatPeriod(item.period_month),
                total_revenue: item.total_revenue || 0,
                total_expense: item.total_expense || 0,
                net_profit: item.net_profit || 0,
                investor_share_percentage: item.investor_share_percentage || 0,
                investor_share_amount: item.investor_share_amount || 0,
                management_share_amount: item.management_share_amount || 0,
                status: item.status || 'draft',
                notes: item.notes || ''
              }));
            }
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        openCreate(){ 
          this.form = { 
            id: null, 
            outlet_id: '', 
            investor_id: '', 
            period_month: new Date().toISOString().slice(0, 7),
            total_revenue: 0,
            total_expense: 0,
            investor_share_percentage: 50,
            notes: ''
          }; 
          this.errors = {};
          this.calculateShares();
          this.showForm = true; 
        },

        openEdit(item){ 
          this.form = { 
            id: item.id,
            outlet_id: item.outlet_id,
            investor_id: item.investor_id,
            period_month: item.period_month,
            total_revenue: item.total_revenue,
            total_expense: item.total_expense,
            investor_share_percentage: item.investor_share_percentage,
            notes: item.notes
          }; 
          this.errors = {};
          this.calculateShares();
          this.showForm = true; 
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        calculateShares() {
          const revenue = parseFloat(this.form.total_revenue) || 0;
          const expense = parseFloat(this.form.total_expense) || 0;
          const percentage = parseFloat(this.form.investor_share_percentage) || 0;
          
          this.calculatedNetProfit = revenue - expense;
          this.calculatedInvestorShare = (this.calculatedNetProfit * percentage) / 100;
          this.calculatedManagementShare = this.calculatedNetProfit - this.calculatedInvestorShare;
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('admin.investor.bagi-hasil.index') }}/${this.form.id}`
              : '{{ route("admin.investor.bagi-hasil.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(item){ 
          this.toDelete = item; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.investor.bagi-hasil.index') }}/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
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

        viewBagiHasil(item) {
          this.selectedBagiHasil = item;
          this.showDetailModal = true;
        },

        getStatusClass(status) {
          switch(status) {
            case 'approved': return 'bg-green-50 text-green-700 border-green-200';
            case 'paid': return 'bg-blue-50 text-blue-700 border-blue-200';
            case 'draft': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
            default: return 'bg-slate-50 text-slate-600 border-slate-200';
          }
        },

        getStatusLabel(status) {
          switch(status) {
            case 'approved': return 'Disetujui';
            case 'paid': return 'Dibayar';
            case 'draft': return 'Draft';
            default: return 'Unknown';
          }
        },

        formatPeriod(periodMonth) {
          if (!periodMonth) return '-';
          const [year, month] = periodMonth.split('-');
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          return `${monthNames[parseInt(month) - 1]} ${year}`;
        },

        formatCurrency(amount) {
          const num = parseFloat(amount || 0);
          return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        },

        exportPdf(){
          const params = new URLSearchParams({
            outlet_filter: this.outletFilter,
            investor_filter: this.investorFilter,
            status_filter: this.statusFilter
          });
          window.open(`{{ route('admin.investor.bagi-hasil.export') }}?${params}&format=pdf`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet_filter: this.outletFilter,
            investor_filter: this.investorFilter,
            status_filter: this.statusFilter
          });
          window.open(`{{ route('admin.investor.bagi-hasil.export') }}?${params}&format=excel`, '_blank');
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
                        const result = await response.json();
                        
                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorBagiHasil()"]'));
                            component.modalTitle = 'Edit Bagi Hasil';
                            component.form = result.data;
                            component.calculateShares();
                            component.showModal = true;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                },

                approveProfitShare(id) {
                    const component = Alpine.$data(document.querySelector('[x-data="investorBagiHasil()"]'));
                    component.approvalId = id;
                    component.showApprovalModal = true;
                },

                async markAsPaid(id) {
                    if (!confirm('Apakah Anda yakin ingin menandai bagi hasil ini sebagai dibayar?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route("admin.investor.bagi-hasil.index") }}/${id}/mark-as-paid`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorBagiHasil()"]'));
                            component.loadData();
                            alert(result.message);
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                },

                async deleteProfitShare(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus bagi hasil ini?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route("admin.investor.bagi-hasil.index") }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorBagiHasil()"]'));
                            component.loadData();
                            alert(result.message);
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                }
            });
        });
    </script>
</x-layouts.admin>