{{-- resources/views/admin/penjualan/margin/index.blade.php --}}
<x-layouts.admin :title="'Laporan Margin'">
  <div x-data="marginReportApp()" x-init="init()" class="space-y-6 overflow-x-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Laporan Margin & Profit</h1>
        <p class="text-sm text-slate-600 mt-1">Analisis margin dan keuntungan per produk</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="exportPdf()" :disabled="isLoading || marginData.length === 0" 
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700 disabled:opacity-50">
          <i class='bx bx-download'></i> Export PDF
        </button>
        <button @click="refreshData()" :disabled="isLoading" 
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-600">Total Item</p>
            <p class="text-2xl font-bold mt-1" x-text="summary.total_items"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
            <i class='bx bx-package text-2xl text-blue-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-600">Total HPP</p>
            <p class="text-xl font-bold mt-1" x-text="formatRupiah(summary.total_hpp)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-orange-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-600">Total Penjualan</p>
            <p class="text-xl font-bold mt-1" x-text="formatRupiah(summary.total_penjualan)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center">
            <i class='bx bx-cart text-2xl text-cyan-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-600">Total Profit</p>
            <p class="text-xl font-bold mt-1 text-emerald-600" x-text="formatRupiah(summary.total_profit)"></p>
            <p class="text-xs text-slate-500 mt-1">
              Avg Margin: <span class="font-semibold" x-text="summary.avg_margin.toFixed(2) + '%'"></span>
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
            <i class='bx bx-trending-up text-2xl text-emerald-600'></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter Section --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        
        {{-- Outlet --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadData()" class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>

        {{-- Tanggal Mulai --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadData()" 
                 class="w-full h-10 rounded-xl border border-slate-200 px-3">
        </div>

        {{-- Tanggal Akhir --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadData()" 
                 class="w-full h-10 rounded-xl border border-slate-200 px-3">
        </div>

        {{-- Search --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Cari Produk</label>
          <input type="text" x-model="filters.search" @input.debounce.500ms="filterData()" 
                 placeholder="Nama produk..." 
                 class="w-full h-10 rounded-xl border border-slate-200 px-3">
        </div>
      </div>
    </section>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data margin...</p>
    </div>

    {{-- Table --}}
    <section x-show="!isLoading" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="overflow-x-auto">
        <table class="min-w-[1200px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">No</th>
              <th class="px-3 py-2 text-left">Source</th>
              <th class="px-3 py-2 text-left">Tanggal</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Produk</th>
              <th class="px-3 py-2 text-right">Qty</th>
              <th class="px-3 py-2 text-right">HPP</th>
              <th class="px-3 py-2 text-right">Harga Jual</th>
              <th class="px-3 py-2 text-right">Subtotal</th>
              <th class="px-3 py-2 text-right">Profit</th>
              <th class="px-3 py-2 text-right">Margin %</th>
              <th class="px-3 py-2 text-center">Status HPP</th>
              <th class="px-3 py-2 text-center">Pembayaran</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="(item, index) in filteredData" :key="item.id">
              <tr class="hover:bg-slate-50">
                <td class="px-3 py-2" x-text="index + 1"></td>
                <td class="px-3 py-2">
                  <span x-show="item.source === 'invoice'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    <i class='bx bx-file text-sm mr-1'></i> Invoice
                  </span>
                  <span x-show="item.source === 'pos'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-cyan-100 text-cyan-800">
                    <i class='bx bx-store text-sm mr-1'></i> POS
                  </span>
                  <span x-show="item.source === 'inter_outlet'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                    <i class='bx bx-transfer text-sm mr-1'></i> Inter Outlet
                  </span>
                </td>
                <td class="px-3 py-2 text-slate-600" x-text="formatDate(item.tanggal)"></td>
                <td class="px-3 py-2" x-text="item.outlet"></td>
                <td class="px-3 py-2 font-medium" x-text="item.produk"></td>
                <td class="px-3 py-2 text-right" x-text="item.qty"></td>
                <td class="px-3 py-2 text-right" x-text="formatRupiah(item.hpp)"></td>
                <td class="px-3 py-2 text-right" x-text="formatRupiah(item.harga_jual)"></td>
                <td class="px-3 py-2 text-right font-semibold" x-text="formatRupiah(item.subtotal)"></td>
                <td class="px-3 py-2 text-right font-semibold">
                  <span x-show="item.profit !== null" 
                        :class="item.profit >= 0 ? 'text-emerald-600' : 'text-red-600'"
                        x-text="formatRupiah(item.profit)"></span>
                  <span x-show="item.profit === null" class="text-slate-400 text-xs">Perlu Diperiksa</span>
                </td>
                <td class="px-3 py-2 text-right">
                  <span x-show="item.profit !== null && item.margin_pct !== null" class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold"
                        :class="{
                          'bg-emerald-100 text-emerald-800': item.margin_pct !== null && item.margin_pct >= 30,
                          'bg-blue-100 text-blue-800': item.margin_pct !== null && item.margin_pct >= 15 && item.margin_pct < 30,
                          'bg-orange-100 text-orange-800': item.margin_pct !== null && item.margin_pct >= 5 && item.margin_pct < 15,
                          'bg-red-100 text-red-800': item.margin_pct !== null && item.margin_pct < 5
                        }"
                        x-text="item.margin_pct.toFixed(2) + '%'"></span>
                  <span x-show="item.profit === null || item.margin_pct === null" class="text-slate-400 text-xs">-</span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span x-show="item.source === 'inter_outlet'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-800': item.hpp_status === 'complete',
                          'bg-blue-100 text-blue-800': item.hpp_status === 'fallback',
                          'bg-red-100 text-red-800': item.hpp_status === 'insufficient',
                          'bg-orange-100 text-orange-800': item.hpp_status === 'error'
                        }"
                        :title="item.hpp_message">
                    <i class='bx text-sm mr-1'
                       :class="{
                         'bx-check-circle': item.hpp_status === 'complete',
                         'bx-info-circle': item.hpp_status === 'fallback',
                         'bx-error-circle': item.hpp_status === 'insufficient',
                         'bx-x-circle': item.hpp_status === 'error'
                       }"></i>
                    <span x-text="{
                      'complete': 'OK',
                      'fallback': 'Fallback',
                      'insufficient': 'Tidak Cukup',
                      'error': 'Error'
                    }[item.hpp_status]"></span>
                  </span>
                  <span x-show="item.source !== 'inter_outlet'" class="text-slate-400 text-xs">-</span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-800': item.payment_type === 'Cash' || item.payment_type === 'cash',
                          'bg-blue-100 text-blue-800': item.payment_type === 'QRIS' || item.payment_type === 'qris',
                          'bg-orange-100 text-orange-800': item.payment_type === 'BON'
                        }"
                        x-text="item.payment_type"></span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>

        {{-- Empty State --}}
        <div x-show="filteredData.length === 0" class="text-center py-12">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
            <i class='bx bx-chart text-3xl text-slate-400'></i>
          </div>
          <p class="text-slate-600 font-medium">Tidak ada data margin</p>
          <p class="text-sm text-slate-500 mt-1">Belum ada transaksi untuk filter yang dipilih</p>
        </div>
      </div>
    </section>

    {{-- Modal PDF Preview --}}
    <div x-show="showPdfModal" x-cloak class="fixed inset-0 z-50" style="display: none;">
      <div x-show="showPdfModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" 
           x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" 
           x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
           class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closePdfModal()"></div>

      <div class="fixed inset-0 flex items-start justify-center pt-2 pb-2 pointer-events-none overflow-y-auto">
        <div x-show="showPdfModal" x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 translate-y-4" 
             class="relative w-full max-w-[98vw] overflow-hidden text-left transition-all transform bg-white rounded-2xl shadow-xl flex flex-col pointer-events-auto my-2" 
             style="height: calc(100vh - 1rem);">
          
          {{-- Modal Header --}}
          <div class="flex items-center justify-between px-4 py-2 border-b border-slate-200 flex-shrink-0">
            <h3 class="text-lg font-semibold text-slate-800">Laporan Margin PDF</h3>
            <button @click="closePdfModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Modal Body --}}
          <div class="flex-1 overflow-auto min-h-0">
            <iframe x-show="pdfUrl" :src="pdfUrl" class="w-full h-full" frameborder="0" data-no-auto-resize="true" style="min-height: 100%; display: block;"></iframe>
            <div x-show="!pdfUrl" class="flex items-center justify-center h-full min-h-[400px]">
              <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-slate-600">Memuat laporan...</p>
              </div>
            </div>
          </div>

          {{-- Modal Footer --}}
          <div class="flex items-center justify-end gap-2 px-4 py-2 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button @click="closePdfModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function marginReportApp() {
      return {
        isLoading: false,
        outlets: @json($outlets),
        marginData: [],
        filteredData: [],
        filters: {
          outlet_id: '',
          start_date: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          search: ''
        },
        summary: {
          total_items: 0,
          total_hpp: 0,
          total_penjualan: 0,
          total_profit: 0,
          avg_margin: 0
        },
        showPdfModal: false,
        pdfUrl: '',

        async init() {
          await this.loadData();
        },

        async loadData() {
          this.isLoading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id || '',
              start_date: this.filters.start_date,
              end_date: this.filters.end_date
            });

            const response = await fetch(`{{ route('admin.penjualan.margin.data') }}?${params}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const data = await response.json();
            
            if (data.success) {
              this.marginData = data.data;
              this.filterData();
              this.calculateSummary();
            } else {
              this.showNotification('error', data.message || 'Gagal memuat data');
            }
          } catch (error) {
            console.error('Error loading data:', error);
            this.showNotification('error', 'Terjadi kesalahan saat memuat data');
          } finally {
            this.isLoading = false;
          }
        },

        filterData() {
          if (!this.filters.search) {
            this.filteredData = this.marginData;
          } else {
            const search = this.filters.search.toLowerCase();
            this.filteredData = this.marginData.filter(item => 
              item.produk && item.produk.toLowerCase().includes(search)
            );
          }
          this.calculateSummary();
        },

        calculateSummary() {
          const data = this.filteredData || [];
          
          if (data.length === 0) {
            this.summary = {
              total_items: 0,
              total_hpp: 0,
              total_penjualan: 0,
              total_profit: 0,
              avg_margin: 0
            };
            return;
          }

          this.summary = {
            total_items: data.length,
            total_hpp: data.reduce((sum, item) => {
              const hpp = parseFloat(item.hpp) || 0;
              const qty = parseFloat(item.qty) || 0;
              return sum + (hpp * qty);
            }, 0),
            total_penjualan: data.reduce((sum, item) => {
              const subtotal = parseFloat(item.subtotal) || 0;
              return sum + subtotal;
            }, 0),
            total_profit: data.reduce((sum, item) => {
              const profit = parseFloat(item.profit) || 0;
              return sum + profit;
            }, 0),
            avg_margin: data.reduce((sum, item) => {
              const margin = parseFloat(item.margin_pct) || 0;
              return sum + margin;
            }, 0) / data.length
          };
        },

        async refreshData() {
          await this.loadData();
          this.showNotification('success', 'Data berhasil dimuat ulang');
        },

        formatDate(dateStr) {
          if (!dateStr) return '-';
          const date = new Date(dateStr);
          return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric'
          });
        },

        formatRupiah(value) {
          const numValue = parseFloat(value);
          if (isNaN(numValue)) return 'Rp 0';
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(numValue);
        },

        exportPdf() {
          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id || '',
            start_date: this.filters.start_date,
            end_date: this.filters.end_date
          });

          const url = `{{ route('admin.penjualan.margin.export-pdf') }}?${params}`;
          this.pdfUrl = url;
          this.showPdfModal = true;
        },

        closePdfModal() {
          this.showPdfModal = false;
          this.pdfUrl = '';
        },

        showNotification(type, message) {
          const event = new CustomEvent('notify', {
            detail: { type, message }
          });
          window.dispatchEvent(event);
        }
      };
    }
  </script>

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
</x-layouts.admin>
