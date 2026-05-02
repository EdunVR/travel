<x-layouts.admin :title="'Dashboard Finance'">
  <style>
    [x-cloak] { display: none !important; }
  </style>
  
  <div x-data="financeDashboard()" x-init="init()" class="space-y-6 overflow-x-hidden">

    {{-- Header & Filter --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Dashboard Finance</h1>
          <p class="text-slate-600 text-sm">Ringkasan Keuangan Real-time dari Semua Modul Finance</p>
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
                  @foreach($outlets as $outlet)
                  <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                    <input type="checkbox" 
                           x-model="filter.selectedOutlets" 
                           value="{{ $outlet->id_outlet }}"
                           @change="loadDataDebounced()"
                           class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                    <span class="text-sm text-slate-700">{{ $outlet->nama_outlet }}</span>
                  </label>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Dari Tanggal</label>
            <input type="date" x-model="filter.from" @change="loadDataDebounced()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Sampai Tanggal</label>
            <input type="date" x-model="filter.to" @change="loadDataDebounced()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div class="flex items-end">
            <button @click="exportPdf()" :disabled="isLoading"
                    class="h-10 w-full rounded-xl bg-primary-600 text-white px-3 hover:bg-primary-700 disabled:opacity-50">
              <i class='bx bxs-file-pdf'></i> Export PDF
            </button>
          </div>
        </div>
      </div>
    </section>


    {{-- KPI Cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Total Revenue</div>
          <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
            <i class='bx bx-trending-up text-emerald-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold" x-text="idr(kpi.total_revenue || 0)"></div>
        <div class="mt-2 text-xs text-slate-500">
          Margin: <span class="font-semibold" x-text="(kpi.profit_margin || 0).toFixed(1) + '%'"></span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Total Expense</div>
          <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center">
            <i class='bx bx-trending-down text-rose-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold text-rose-600" x-text="idr(kpi.total_expense || 0)"></div>
        <div class="mt-2 text-xs text-slate-500">
          Transaksi: <span class="font-semibold" x-text="kpi.total_transactions || 0"></span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Net Profit</div>
          <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
            <i class='bx bx-dollar-circle text-indigo-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold" 
             :class="(kpi.net_profit || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
             x-text="idr(kpi.net_profit || 0)"></div>
        <div class="mt-2 text-xs text-slate-500">
          Margin: <span class="font-semibold" x-text="(kpi.profit_margin || 0).toFixed(1) + '%'"></span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-xs text-slate-500 font-medium">Kas & Bank</div>
          <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
            <i class='bx bx-wallet text-blue-600'></i>
          </div>
        </div>
        <div class="mt-2 text-2xl font-bold text-blue-600" x-text="idr(kpi.cash_bank_balance || 0)"></div>
        <div class="mt-2 text-xs text-slate-500">
          Working Capital: <span class="font-semibold" x-text="idr(kpi.working_capital || 0)"></span>
        </div>
      </div>
    </section>

    {{-- Line Chart (Kurva) --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-sm font-semibold">Tren Revenue, Expense & Profit</h3>
          <p class="text-xs text-slate-500 mt-1" x-text="periodeLabel"></p>
        </div>
        <button @click="showPdfPreview = true" 
                class="h-9 px-4 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm font-medium flex items-center gap-2">
          <i class='bx bx-show'></i> Preview PDF
        </button>
      </div>

      {{-- Legend --}}
      <div class="flex items-center gap-6 mb-4">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
          <span class="text-xs font-medium text-slate-700">Revenue</span>
          <span class="text-xs font-bold text-emerald-600" x-text="idr(kpi.total_revenue || 0)"></span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full bg-rose-500"></div>
          <span class="text-xs font-medium text-slate-700">Expense</span>
          <span class="text-xs font-bold text-rose-600" x-text="idr(kpi.total_expense || 0)"></span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full" 
               :class="(kpi.net_profit || 0) >= 0 ? 'bg-indigo-500' : 'bg-amber-500'"></div>
          <span class="text-xs font-medium text-slate-700">Net Profit</span>
          <span class="text-xs font-bold" 
                :class="(kpi.net_profit || 0) >= 0 ? 'text-indigo-600' : 'text-amber-600'"
                x-text="idr(kpi.net_profit || 0)"></span>
        </div>
      </div>

      {{-- Chart Container --}}
      <div class="relative w-full h-64 bg-slate-50 rounded-lg p-4">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          {{-- Grid Lines --}}
          <line x1="0" y1="25" x2="100" y2="25" stroke="#e2e8f0" stroke-width="0.2" />
          <line x1="0" y1="50" x2="100" y2="50" stroke="#e2e8f0" stroke-width="0.2" />
          <line x1="0" y1="75" x2="100" y2="75" stroke="#e2e8f0" stroke-width="0.2" />
          
          {{-- Revenue Line (Emerald) --}}
          <path :d="getLinePath([
            {x: 10, y: getChartY(kpi.total_revenue * 0.7)},
            {x: 30, y: getChartY(kpi.total_revenue * 0.85)},
            {x: 50, y: getChartY(kpi.total_revenue * 0.9)},
            {x: 70, y: getChartY(kpi.total_revenue * 0.95)},
            {x: 90, y: getChartY(kpi.total_revenue)}
          ])"
                fill="none"
                stroke="#10b981"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="transition-all duration-700 ease-out"
                style="vector-effect: non-scaling-stroke;" />
          
          {{-- Revenue Area Fill --}}
          <path :d="getAreaPath([
            {x: 10, y: getChartY(kpi.total_revenue * 0.7)},
            {x: 30, y: getChartY(kpi.total_revenue * 0.85)},
            {x: 50, y: getChartY(kpi.total_revenue * 0.9)},
            {x: 70, y: getChartY(kpi.total_revenue * 0.95)},
            {x: 90, y: getChartY(kpi.total_revenue)}
          ])"
                fill="url(#emeraldGradient)"
                opacity="0.2"
                class="transition-all duration-700 ease-out" />
          
          {{-- Expense Line (Rose) --}}
          <path :d="getLinePath([
            {x: 10, y: getChartY(kpi.total_expense * 0.8)},
            {x: 30, y: getChartY(kpi.total_expense * 0.9)},
            {x: 50, y: getChartY(kpi.total_expense * 0.85)},
            {x: 70, y: getChartY(kpi.total_expense * 0.95)},
            {x: 90, y: getChartY(kpi.total_expense)}
          ])"
                fill="none"
                stroke="#ef4444"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="transition-all duration-700 ease-out"
                style="vector-effect: non-scaling-stroke;" />
          
          {{-- Expense Area Fill --}}
          <path :d="getAreaPath([
            {x: 10, y: getChartY(kpi.total_expense * 0.8)},
            {x: 30, y: getChartY(kpi.total_expense * 0.9)},
            {x: 50, y: getChartY(kpi.total_expense * 0.85)},
            {x: 70, y: getChartY(kpi.total_expense * 0.95)},
            {x: 90, y: getChartY(kpi.total_expense)}
          ])"
                fill="url(#roseGradient)"
                opacity="0.2"
                class="transition-all duration-700 ease-out" />
          
          {{-- Profit Line (Indigo/Amber) --}}
          <path :d="getLinePath([
            {x: 10, y: getChartY(Math.abs(kpi.net_profit) * 0.6)},
            {x: 30, y: getChartY(Math.abs(kpi.net_profit) * 0.75)},
            {x: 50, y: getChartY(Math.abs(kpi.net_profit) * 0.85)},
            {x: 70, y: getChartY(Math.abs(kpi.net_profit) * 0.9)},
            {x: 90, y: getChartY(Math.abs(kpi.net_profit))}
          ])"
                fill="none"
                :stroke="(kpi.net_profit || 0) >= 0 ? '#6366f1' : '#f59e0b'"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="transition-all duration-700 ease-out"
                style="vector-effect: non-scaling-stroke;" />
          
          {{-- Profit Area Fill --}}
          <path :d="getAreaPath([
            {x: 10, y: getChartY(Math.abs(kpi.net_profit) * 0.6)},
            {x: 30, y: getChartY(Math.abs(kpi.net_profit) * 0.75)},
            {x: 50, y: getChartY(Math.abs(kpi.net_profit) * 0.85)},
            {x: 70, y: getChartY(Math.abs(kpi.net_profit) * 0.9)},
            {x: 90, y: getChartY(Math.abs(kpi.net_profit))}
          ])"
                :fill="(kpi.net_profit || 0) >= 0 ? 'url(#indigoGradient)' : 'url(#amberGradient)'"
                opacity="0.2"
                class="transition-all duration-700 ease-out" />
          
          {{-- Gradients --}}
          <defs>
            <linearGradient id="emeraldGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#10b981;stop-opacity:0" />
            </linearGradient>
            <linearGradient id="roseGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#ef4444;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#ef4444;stop-opacity:0" />
            </linearGradient>
            <linearGradient id="indigoGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#6366f1;stop-opacity:0" />
            </linearGradient>
            <linearGradient id="amberGradient" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#f59e0b;stop-opacity:0" />
            </linearGradient>
          </defs>
        </svg>
        
        {{-- Y-Axis Labels --}}
        <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-slate-500 pr-2">
          <span x-text="idr(getMaxValue())"></span>
          <span x-text="idr(getMaxValue() * 0.75)"></span>
          <span x-text="idr(getMaxValue() * 0.5)"></span>
          <span x-text="idr(getMaxValue() * 0.25)"></span>
          <span>Rp 0</span>
        </div>
      </div>
    </section>

    {{-- Piutang & Hutang --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Piutang (Aging)</div>
            <div class="text-xs text-slate-500">Total: <span class="font-bold" x-text="idr(kpi.total_piutang || 0)"></span></div>
          </div>
          <a :href="`{{ route('finance.piutang.index') }}`" class="text-xs text-primary-600 hover:underline">
            Lihat Detail →
          </a>
        </div>
        <div class="space-y-2">
          <div class="flex items-center justify-between p-2 bg-emerald-50 rounded-lg">
            <span class="text-xs font-medium text-emerald-700">Current (0-30 hari)</span>
            <span class="text-sm font-bold text-emerald-700" x-text="idr(piutangAging.current || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-amber-50 rounded-lg">
            <span class="text-xs font-medium text-amber-700">31-60 hari</span>
            <span class="text-sm font-bold text-amber-700" x-text="idr(piutangAging.overdue_30 || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-orange-50 rounded-lg">
            <span class="text-xs font-medium text-orange-700">61-90 hari</span>
            <span class="text-sm font-bold text-orange-700" x-text="idr(piutangAging.overdue_60 || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-rose-50 rounded-lg">
            <span class="text-xs font-medium text-rose-700">> 90 hari</span>
            <span class="text-sm font-bold text-rose-700" x-text="idr(piutangAging.overdue_90 || 0)"></span>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Hutang (Aging)</div>
            <div class="text-xs text-slate-500">Total: <span class="font-bold" x-text="idr(kpi.total_hutang || 0)"></span></div>
          </div>
          <a :href="`{{ route('finance.hutang.index') }}`" class="text-xs text-primary-600 hover:underline">
            Lihat Detail →
          </a>
        </div>
        <div class="space-y-2">
          <div class="flex items-center justify-between p-2 bg-emerald-50 rounded-lg">
            <span class="text-xs font-medium text-emerald-700">Current (0-30 hari)</span>
            <span class="text-sm font-bold text-emerald-700" x-text="idr(hutangAging.current || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-amber-50 rounded-lg">
            <span class="text-xs font-medium text-amber-700">31-60 hari</span>
            <span class="text-sm font-bold text-amber-700" x-text="idr(hutangAging.overdue_30 || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-orange-50 rounded-lg">
            <span class="text-xs font-medium text-orange-700">61-90 hari</span>
            <span class="text-sm font-bold text-orange-700" x-text="idr(hutangAging.overdue_60 || 0)"></span>
          </div>
          <div class="flex items-center justify-between p-2 bg-rose-50 rounded-lg">
            <span class="text-xs font-medium text-rose-700">> 90 hari</span>
            <span class="text-sm font-bold text-rose-700" x-text="idr(hutangAging.overdue_90 || 0)"></span>
          </div>
        </div>
      </div>
    </section>


    {{-- Profit Loss & Cashflow Summary --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Laba Rugi</div>
            <div class="text-xs text-slate-500" x-text="periodeLabel"></div>
          </div>
          <a :href="`{{ route('finance.labarugi.index') }}`" class="text-xs text-primary-600 hover:underline">
            Lihat Detail →
          </a>
        </div>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-600">Pendapatan</span>
            <span class="text-sm font-bold text-emerald-600" x-text="idr(profitLoss.revenue || 0)"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-600">HPP</span>
            <span class="text-sm font-bold text-rose-600" x-text="idr(profitLoss.cogs || 0)"></span>
          </div>
          <div class="flex justify-between items-center pt-2 border-t">
            <span class="text-xs font-medium text-slate-700">Laba Kotor</span>
            <span class="text-sm font-bold" x-text="idr(profitLoss.gross_profit || 0)"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-600">Biaya Operasional</span>
            <span class="text-sm font-bold text-rose-600" x-text="idr(profitLoss.operating_expense || 0)"></span>
          </div>
          <div class="flex justify-between items-center pt-2 border-t">
            <span class="text-xs font-medium text-slate-700">Laba Operasional</span>
            <span class="text-sm font-bold" x-text="idr(profitLoss.operating_profit || 0)"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-600">Pendapatan Lain</span>
            <span class="text-sm font-bold text-emerald-600" x-text="idr(profitLoss.other_revenue || 0)"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xs text-slate-600">Biaya Lain</span>
            <span class="text-sm font-bold text-rose-600" x-text="idr(profitLoss.other_expense || 0)"></span>
          </div>
          <div class="flex justify-between items-center pt-2 border-t-2 border-slate-300">
            <span class="text-sm font-bold text-slate-900">Laba Bersih</span>
            <span class="text-lg font-bold" 
                  :class="(profitLoss.net_profit || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                  x-text="idr(profitLoss.net_profit || 0)"></span>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Arus Kas</div>
            <div class="text-xs text-slate-500" x-text="periodeLabel"></div>
          </div>
          <a :href="`{{ route('finance.cashflow.index') }}`" class="text-xs text-primary-600 hover:underline">
            Lihat Detail →
          </a>
        </div>
        <div class="space-y-3">
          <div class="p-3 bg-blue-50 rounded-lg">
            <div class="text-xs font-medium text-blue-700 mb-2">Aktivitas Operasional</div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Masuk:</span>
              <span class="font-semibold" x-text="idr(cashflow.operating.inflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Keluar:</span>
              <span class="font-semibold" x-text="idr(cashflow.operating.outflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-blue-200">
              <span>Net:</span>
              <span :class="(cashflow.operating.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                    x-text="idr(cashflow.operating.net || 0)"></span>
            </div>
          </div>

          <div class="p-3 bg-purple-50 rounded-lg">
            <div class="text-xs font-medium text-purple-700 mb-2">Aktivitas Investasi</div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Masuk:</span>
              <span class="font-semibold" x-text="idr(cashflow.investing.inflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Keluar:</span>
              <span class="font-semibold" x-text="idr(cashflow.investing.outflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-purple-200">
              <span>Net:</span>
              <span :class="(cashflow.investing.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                    x-text="idr(cashflow.investing.net || 0)"></span>
            </div>
          </div>

          <div class="p-3 bg-amber-50 rounded-lg">
            <div class="text-xs font-medium text-amber-700 mb-2">Aktivitas Pendanaan</div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Masuk:</span>
              <span class="font-semibold" x-text="idr(cashflow.financing.inflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Keluar:</span>
              <span class="font-semibold" x-text="idr(cashflow.financing.outflow || 0)"></span>
            </div>
            <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-amber-200">
              <span>Net:</span>
              <span :class="(cashflow.financing.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                    x-text="idr(cashflow.financing.net || 0)"></span>
            </div>
          </div>

          <div class="flex justify-between items-center pt-2 border-t-2 border-slate-300">
            <span class="text-sm font-bold text-slate-900">Net Cashflow</span>
            <span class="text-lg font-bold" 
                  :class="(cashflow.net_cashflow || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                  x-text="idr(cashflow.net_cashflow || 0)"></span>
          </div>
        </div>
      </div>
    </section>


    {{-- Balance Sheet & Charts --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-sm font-semibold">Neraca</div>
            <div class="text-xs text-slate-500">Per <span x-text="fmtd(filter.to)"></span></div>
          </div>
          <a :href="`{{ route('finance.neraca.index') }}`" class="text-xs text-primary-600 hover:underline">
            Lihat Detail →
          </a>
        </div>
        <div class="space-y-3">
          <div class="p-3 bg-blue-50 rounded-lg">
            <div class="text-xs font-medium text-blue-700 mb-2">ASET</div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Aset Lancar:</span>
              <span class="font-semibold" x-text="idr(balanceSheet.assets.current || 0)"></span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Aset Tetap:</span>
              <span class="font-semibold" x-text="idr(balanceSheet.assets.fixed || 0)"></span>
            </div>
            <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-blue-200">
              <span>Total Aset:</span>
              <span class="text-blue-700" x-text="idr(balanceSheet.assets.total || 0)"></span>
            </div>
          </div>

          <div class="p-3 bg-rose-50 rounded-lg">
            <div class="text-xs font-medium text-rose-700 mb-2">KEWAJIBAN</div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Kewajiban Lancar:</span>
              <span class="font-semibold" x-text="idr(balanceSheet.liabilities.current || 0)"></span>
            </div>
            <div class="flex justify-between text-xs">
              <span class="text-slate-600">Kewajiban Jangka Panjang:</span>
              <span class="font-semibold" x-text="idr(balanceSheet.liabilities.long_term || 0)"></span>
            </div>
            <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-rose-200">
              <span>Total Kewajiban:</span>
              <span class="text-rose-700" x-text="idr(balanceSheet.liabilities.total || 0)"></span>
            </div>
          </div>

          <div class="p-3 bg-emerald-50 rounded-lg">
            <div class="text-xs font-medium text-emerald-700 mb-2">EKUITAS</div>
            <div class="flex justify-between text-xs font-bold">
              <span>Total Ekuitas:</span>
              <span class="text-emerald-700" x-text="idr(balanceSheet.equity || 0)"></span>
            </div>
          </div>

          <div class="flex justify-between items-center pt-2 border-t-2 border-slate-300">
            <span class="text-sm font-bold text-slate-900">Total Kewajiban + Ekuitas</span>
            <span class="text-sm font-bold text-slate-900" x-text="idr(balanceSheet.total_liabilities_equity || 0)"></span>
          </div>

          <div class="grid grid-cols-2 gap-2 mt-3">
            <div class="p-2 bg-slate-50 rounded text-center">
              <div class="text-xs text-slate-500">Current Ratio</div>
              <div class="text-sm font-bold" x-text="(balanceSheet.current_ratio || 0).toFixed(2)"></div>
            </div>
            <div class="p-2 bg-slate-50 rounded text-center">
              <div class="text-xs text-slate-500">Debt to Equity</div>
              <div class="text-sm font-bold" x-text="(balanceSheet.debt_to_equity || 0).toFixed(2)"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-sm font-semibold mb-4">Tren Bulanan</div>
        
        <!-- Table instead of Chart to prevent infinite loop -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-3 py-2 text-left font-medium text-slate-600">Bulan</th>
                <th class="px-3 py-2 text-right font-medium text-slate-600">Revenue</th>
                <th class="px-3 py-2 text-right font-medium text-slate-600">Expense</th>
                <th class="px-3 py-2 text-right font-medium text-slate-600">Profit</th>
              </tr>
            </thead>
            <tbody>
              <template x-if="!monthlyTrend || monthlyTrend.length === 0">
                <tr><td colspan="4" class="px-3 py-8 text-center text-slate-400">
                  <i class='bx bx-line-chart text-4xl'></i>
                  <p class="mt-2">Tidak ada data tren</p>
                </td></tr>
              </template>
              
              <template x-for="(trend, i) in monthlyTrend" :key="i">
                <tr class="border-t hover:bg-slate-50">
                  <td class="px-3 py-2 font-medium" x-text="trend.month"></td>
                  <td class="px-3 py-2 text-right text-emerald-600 font-semibold" x-text="idr(trend.revenue)"></td>
                  <td class="px-3 py-2 text-right text-rose-600 font-semibold" x-text="idr(trend.expense)"></td>
                  <td class="px-3 py-2 text-right font-bold" 
                      :class="trend.profit >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                      x-text="idr(trend.profit)"></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    {{-- Recent Transactions --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-0 shadow-card">
      <div class="p-4 flex items-center justify-between border-b border-slate-200">
        <div>
          <div class="text-sm font-semibold">Transaksi Terbaru</div>
          <div class="text-xs text-slate-500">10 transaksi jurnal terakhir</div>
        </div>
        <a :href="`{{ route('finance.jurnal.index') }}`" 
           class="h-9 px-3 rounded-lg border border-slate-200 text-sm hover:bg-slate-50 flex items-center gap-1">
          <i class='bx bx-file'></i> Lihat Semua
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">Tanggal</th>
              <th class="px-3 py-2 text-left">Referensi</th>
              <th class="px-3 py-2 text-left">Deskripsi</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-right">Debit</th>
              <th class="px-3 py-2 text-right">Kredit</th>
              <th class="px-3 py-2 text-center">Source</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="isLoading">
              <tr><td colspan="7" class="px-3 py-8 text-center">
                <div class="flex items-center justify-center gap-2">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                  <span class="text-slate-500">Memuat data...</span>
                </div>
              </td></tr>
            </template>

            <template x-if="!isLoading && recentTransactions.length === 0">
              <tr><td colspan="7" class="px-3 py-8 text-center">
                <div class="text-slate-400">
                  <i class='bx bx-receipt text-4xl'></i>
                  <p class="mt-2">Tidak ada transaksi</p>
                </div>
              </td></tr>
            </template>

            <template x-for="(t, i) in recentTransactions" :key="t.id">
              <tr class="border-t hover:bg-slate-50">
                <td class="px-3 py-2" x-text="fmtd(t.date)"></td>
                <td class="px-3 py-2 font-medium" x-text="t.reference"></td>
                <td class="px-3 py-2 text-slate-600" x-text="t.description"></td>
                <td class="px-3 py-2" x-text="t.outlet"></td>
                <td class="px-3 py-2 text-right font-semibold" x-text="idr(t.debit)"></td>
                <td class="px-3 py-2 text-right font-semibold" x-text="idr(t.credit)"></td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-700"
                        x-text="t.source"></span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </section>

    {{-- PDF Preview Modal --}}
    <template x-if="showPdfPreview">
  <div @keydown.escape.window="showPdfPreview = false"
       class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      {{-- Backdrop --}}
      <div x-transition:enter="ease-out duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="ease-in duration-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           @click="showPdfPreview = false"
           class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      {{-- Modal Panel --}}
      <div x-transition:enter="ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
           x-transition:leave="ease-in duration-200"
           x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
           x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
           class="inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
          <div>
            <h3 class="text-lg font-bold text-slate-900">Preview PDF Dashboard Finance</h3>
            <p class="text-sm text-slate-600 mt-0.5" x-text="periodeLabel"></p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="exportPdf()" 
                    class="h-10 px-4 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-medium flex items-center gap-2">
              <i class='bx bxs-download'></i> Download PDF
            </button>
            <button @click="showPdfPreview = false" 
                    class="h-10 w-10 rounded-lg hover:bg-slate-200 flex items-center justify-center">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>
        </div>

        {{-- Modal Body - PDF Preview Content --}}
        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto bg-slate-50">
          <div class="bg-white shadow-lg rounded-lg p-8 max-w-4xl mx-auto">
            
            {{-- Preview Header --}}
            <div class="text-center mb-8 pb-6 border-b-2 border-slate-200">
              <h1 class="text-3xl font-bold text-slate-900">Dashboard Finance</h1>
              <p class="text-slate-600 mt-2" x-text="periodeLabel || '-'"></p>
              <p class="text-sm text-slate-500 mt-1">
                Outlet: <span x-text="getSelectedOutletText()"></span>
              </p>
            </div>

            {{-- KPI Summary --}}
            <div class="mb-8">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Key Performance Indicators</h2>
              <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                  <div class="text-xs text-emerald-700 font-medium mb-1">Total Revenue</div>
                  <div class="text-xl font-bold text-emerald-700" x-text="idr(kpi.total_revenue || 0)"></div>
                </div>
                <div class="p-4 bg-rose-50 rounded-lg border border-rose-200">
                  <div class="text-xs text-rose-700 font-medium mb-1">Total Expense</div>
                  <div class="text-xl font-bold text-rose-700" x-text="idr(kpi.total_expense || 0)"></div>
                </div>
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                  <div class="text-xs text-indigo-700 font-medium mb-1">Net Profit</div>
                  <div class="text-xl font-bold text-indigo-700" x-text="idr(kpi.net_profit || 0)"></div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="text-xs text-blue-700 font-medium mb-1">Kas & Bank</div>
                  <div class="text-xl font-bold text-blue-700" x-text="idr(kpi.cash_bank_balance || 0)"></div>
                </div>
              </div>
            </div>

            {{-- Horizontal Bar Chart Preview --}}
            <div class="mb-8">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Perbandingan Revenue, Expense & Profit</h2>
              <div class="space-y-4">
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Revenue</span>
                    <span class="text-sm font-bold text-emerald-600" x-text="idr(kpi.total_revenue || 0)"></span>
                  </div>
                  <div class="w-full h-6 bg-slate-100 rounded overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded" :style="`width: ${getBarWidth(kpi.total_revenue)}%`"></div>
                  </div>
                </div>
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Expense</span>
                    <span class="text-sm font-bold text-rose-600" x-text="idr(kpi.total_expense || 0)"></span>
                  </div>
                  <div class="w-full h-6 bg-slate-100 rounded overflow-hidden">
                    <div class="h-full bg-rose-500 rounded" :style="`width: ${getBarWidth(kpi.total_expense)}%`"></div>
                  </div>
                </div>
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Net Profit</span>
                    <span class="text-sm font-bold" 
                          :class="(kpi.net_profit || 0) >= 0 ? 'text-indigo-600' : 'text-amber-600'"
                          x-text="idr(kpi.net_profit || 0)"></span>
                  </div>
                  <div class="w-full h-6 bg-slate-100 rounded overflow-hidden">
                    <div class="h-full rounded" 
                         :class="(kpi.net_profit || 0) >= 0 ? 'bg-indigo-500' : 'bg-amber-500'"
                         :style="`width: ${getBarWidth(Math.abs(kpi.net_profit))}%`"></div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Profit Loss Summary --}}
            <div class="mb-8">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Ringkasan Laba Rugi</h2>
              <table class="w-full text-sm">
                <tr class="border-b">
                  <td class="py-2">Pendapatan</td>
                  <td class="py-2 text-right font-semibold text-emerald-600" x-text="idr(profitLoss.revenue || 0)"></td>
                </tr>
                <tr class="border-b">
                  <td class="py-2">HPP</td>
                  <td class="py-2 text-right font-semibold text-rose-600" x-text="idr(profitLoss.cogs || 0)"></td>
                </tr>
                <tr class="border-b bg-slate-50">
                  <td class="py-2 font-medium">Laba Kotor</td>
                  <td class="py-2 text-right font-bold" x-text="idr(profitLoss.gross_profit || 0)"></td>
                </tr>
                <tr class="border-b">
                  <td class="py-2">Biaya Operasional</td>
                  <td class="py-2 text-right font-semibold text-rose-600" x-text="idr(profitLoss.operating_expense || 0)"></td>
                </tr>
                <tr class="border-b bg-slate-50">
                  <td class="py-2 font-medium">Laba Operasional</td>
                  <td class="py-2 text-right font-bold" x-text="idr(profitLoss.operating_profit || 0)"></td>
                </tr>
                <tr class="border-b">
                  <td class="py-2">Pendapatan Lain</td>
                  <td class="py-2 text-right font-semibold text-emerald-600" x-text="idr(profitLoss.other_revenue || 0)"></td>
                </tr>
                <tr class="border-b">
                  <td class="py-2">Biaya Lain</td>
                  <td class="py-2 text-right font-semibold text-rose-600" x-text="idr(profitLoss.other_expense || 0)"></td>
                </tr>
                <tr class="border-t-2 border-slate-300 bg-slate-100">
                  <td class="py-3 font-bold text-base">Laba Bersih</td>
                  <td class="py-3 text-right font-bold text-lg" 
                      :class="(profitLoss.net_profit || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                      x-text="idr(profitLoss.net_profit || 0)"></td>
                </tr>
              </table>
            </div>

            {{-- Cashflow Summary --}}
            <div class="mb-8">
              <h2 class="text-lg font-bold text-slate-900 mb-4">Ringkasan Arus Kas</h2>
              <div class="space-y-3">
                <div class="p-3 bg-blue-50 rounded border border-blue-200">
                  <div class="font-medium text-sm mb-2">Aktivitas Operasional</div>
                  <div class="text-sm font-bold" 
                       :class="(cashflow.operating.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                       x-text="idr(cashflow.operating.net || 0)"></div>
                </div>
                <div class="p-3 bg-purple-50 rounded border border-purple-200">
                  <div class="font-medium text-sm mb-2">Aktivitas Investasi</div>
                  <div class="text-sm font-bold" 
                       :class="(cashflow.investing.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                       x-text="idr(cashflow.investing.net || 0)"></div>
                </div>
                <div class="p-3 bg-amber-50 rounded border border-amber-200">
                  <div class="font-medium text-sm mb-2">Aktivitas Pendanaan</div>
                  <div class="text-sm font-bold" 
                       :class="(cashflow.financing.net || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                       x-text="idr(cashflow.financing.net || 0)"></div>
                </div>
                <div class="p-4 bg-slate-100 rounded border-2 border-slate-300">
                  <div class="flex justify-between items-center">
                    <span class="font-bold">Net Cashflow</span>
                    <span class="text-lg font-bold" 
                          :class="(cashflow.net_cashflow || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                          x-text="idr(cashflow.net_cashflow || 0)"></span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Footer --}}
            <div class="text-center text-xs text-slate-500 mt-8 pt-6 border-t">
              <p>Dicetak pada: <span x-text="new Date().toLocaleString('id-ID')"></span></p>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
    </template>

  </div>
  {{-- End of Alpine Component Scope --}}


  {{-- Alpine Component --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function financeDashboard(){
      return {
        isLoading: false,
        isRendering: false,
        loadTimeout: null,
        showPdfPreview: false,
        showOutletDropdown: false,
        filter: {
          selectedOutlets: [],
          from: '',
          to: ''
        },
        kpi: {},
        cashflow: {
          operating: {},
          investing: {},
          financing: {},
          net_cashflow: 0
        },
        profitLoss: {},
        balanceSheet: {
          assets: {},
          liabilities: {},
          equity: 0
        },
        piutangAging: {},
        hutangAging: {},
        monthlyTrend: [],
        recentTransactions: [],
        periodeLabel: '',
        chart: null,
        outlets: @json($outlets),

        async init(){
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

        loadDataDebounced(){
          // Clear previous timeout
          if (this.loadTimeout) {
            clearTimeout(this.loadTimeout);
          }
          
          // Set new timeout
          this.loadTimeout = setTimeout(() => {
            this.loadData();
          }, 500);
        },

        async loadData(){
          if (this.isLoading) {
            console.log('Load already in progress, skipping...');
            return;
          }
          
          this.isLoading = true;
          console.log('Loading dashboard data...');
          
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

            const response = await fetch(`{{ route('finance.dashboard.data') }}?${params}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();
            
            if (result.success) {
              this.kpi = result.data.kpi || {};
              this.cashflow = result.data.cashflow_summary || {};
              this.profitLoss = result.data.profit_loss_summary || {};
              this.balanceSheet = result.data.balance_sheet_summary || {};
              this.piutangAging = result.data.piutang_aging || {};
              this.hutangAging = result.data.hutang_aging || {};
              this.monthlyTrend = result.data.monthly_trend || [];
              this.recentTransactions = result.data.recent_transactions || [];
              
              console.log('Data loaded, monthly trend items:', this.monthlyTrend.length);
              
              this.updatePeriodeLabel();
              
              // Chart replaced with table - no rendering needed
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

        renderChart(){
          // Prevent multiple simultaneous renders
          if (this.isRendering) {
            console.log('Chart render already in progress, skipping...');
            return;
          }
          
          this.isRendering = true;
          console.log('Starting chart render...');
          
          const canvas = document.getElementById('monthlyTrendChart');
          if (!canvas) {
            console.log('Canvas not found');
            this.isRendering = false;
            return;
          }

          // Destroy existing chart
          if (this.chart) {
            console.log('Destroying existing chart');
            this.chart.destroy();
            this.chart = null;
          }

          // Check if we have data
          if (!this.monthlyTrend || this.monthlyTrend.length === 0) {
            console.log('No monthly trend data, skipping chart render');
            this.isRendering = false;
            return;
          }

          console.log('Creating new chart with', this.monthlyTrend.length, 'data points');
          
          const ctx = canvas.getContext('2d');
          this.chart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: this.monthlyTrend.map(m => m.month),
              datasets: [
                {
                  label: 'Revenue',
                  data: this.monthlyTrend.map(m => m.revenue),
                  borderColor: 'rgb(16, 185, 129)',
                  backgroundColor: 'rgba(16, 185, 129, 0.1)',
                  tension: 0.4,
                  fill: true
                },
                {
                  label: 'Expense',
                  data: this.monthlyTrend.map(m => m.expense),
                  borderColor: 'rgb(239, 68, 68)',
                  backgroundColor: 'rgba(239, 68, 68, 0.1)',
                  tension: 0.4,
                  fill: true
                },
                {
                  label: 'Profit',
                  data: this.monthlyTrend.map(m => m.profit),
                  borderColor: 'rgb(99, 102, 241)',
                  backgroundColor: 'rgba(99, 102, 241, 0.1)',
                  tension: 0.4,
                  fill: true
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: true,
                  position: 'top'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      let label = context.dataset.label || '';
                      if (label) {
                        label += ': ';
                      }
                      label += new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                      }).format(context.parsed.y);
                      return label;
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                      }).format(value);
                    }
                  }
                }
              }
            }
          });
          
          console.log('Chart created successfully');
          this.isRendering = false;
        },

        updatePeriodeLabel(){
          const from = this.fmtd(this.filter.from);
          const to = this.fmtd(this.filter.to);
          this.periodeLabel = `${from} — ${to}`;
        },

        exportPdf(){
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
          
          window.open(`{{ route('finance.dashboard.export-pdf') }}?${params}`, '_blank');
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

        fmtd(s){ 
          if (!s) return '-';
          const d = new Date(s); 
          return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          });
        },

        getBarWidth(value) {
          const maxValue = Math.max(
            Math.abs(this.kpi.total_revenue || 0),
            Math.abs(this.kpi.total_expense || 0),
            Math.abs(this.kpi.net_profit || 0)
          );
          
          if (maxValue === 0) return 0;
          
          const percentage = (Math.abs(value) / maxValue) * 100;
          return Math.min(percentage, 100);
        },

        getMaxValue() {
          return Math.max(
            Math.abs(this.kpi.total_revenue || 0),
            Math.abs(this.kpi.total_expense || 0),
            Math.abs(this.kpi.net_profit || 0)
          );
        },

        getChartY(value) {
          const maxValue = this.getMaxValue();
          if (maxValue === 0) return 100;
          
          // Invert Y axis (0 at bottom, 100 at top)
          // Scale value to 0-100 range
          const percentage = (Math.abs(value) / maxValue) * 100;
          return 100 - percentage; // Invert for SVG coordinates
        },

        getLinePath(points) {
          if (!points || points.length === 0) return '';
          
          let path = `M ${points[0].x} ${points[0].y}`;
          
          // Create smooth curve using quadratic bezier
          for (let i = 1; i < points.length; i++) {
            const prev = points[i - 1];
            const curr = points[i];
            const cpX = (prev.x + curr.x) / 2;
            
            path += ` Q ${cpX} ${prev.y}, ${curr.x} ${curr.y}`;
          }
          
          return path;
        },

        getAreaPath(points) {
          if (!points || points.length === 0) return '';
          
          let path = this.getLinePath(points);
          
          // Close the path to create area
          const lastPoint = points[points.length - 1];
          const firstPoint = points[0];
          
          path += ` L ${lastPoint.x} 100`; // Line to bottom right
          path += ` L ${firstPoint.x} 100`; // Line to bottom left
          path += ` Z`; // Close path
          
          return path;
        },

        getOutletName(outletId) {
          if (outletId === 'all') return 'Semua Outlet';
          const outlet = this.outlets.find(o => o.id_outlet == outletId);
          return outlet ? outlet.nama_outlet : 'Unknown';
        },

        getSelectedOutletText() {
          if (this.filter.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.filter.selectedOutlets.length === 1) {
            return this.getOutletName(this.filter.selectedOutlets[0]);
          } else if (this.filter.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.filter.selectedOutlets.length} Outlet Dipilih`;
          }
        },

        selectAllOutlets() {
          this.filter.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.loadDataDebounced();
        },

        clearAllOutlets() {
          this.filter.selectedOutlets = [];
          this.loadDataDebounced();
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
</x-layouts.admin>
