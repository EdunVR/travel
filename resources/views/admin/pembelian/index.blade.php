<x-layouts.admin :title="'Dashboard Pembelian'">
  <div x-data="purchasesDashboard()" x-init="init()" class="space-y-6 overflow-x-hidden">

    {{-- ====== Header & Filter ====== --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Dashboard Pembelian</h1>
          <p class="text-slate-600 text-sm">Ringkasan pembelian, performa item, dan tren per outlet.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
            <select x-model="filter.outlet" @change="applyFilters()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
              <option value="all">Semua Outlet</option>
              <template x-for="outlet in outlets" :key="outlet.id_outlet">
                <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
              </template>
            </select>
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Dari Tanggal</label>
            <input type="date" x-model="filter.from" @change="applyFilters()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500 mb-1 block">Sampai Tanggal</label>
            <input type="date" x-model="filter.to" @change="applyFilters()" class="h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div class="flex items-end">
            <button @click="resetFilter()"
                    class="h-10 w-full rounded-xl border border-slate-200 px-3 hover:bg-slate-50">
              Reset Filter
            </button>
          </div>
        </div>
      </div>
    </section>

    {{-- Loading State --}}
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data dashboard...</span>
      </div>
    </div>

    {{-- ====== KPI ====== --}}
    <section x-show="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      {{-- Total PO --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-purchase-tag text-2xl text-blue-600'></i>
          </div>
          <div class="flex-1">
            <div class="text-xs text-slate-500">Total PO</div>
            <div class="mt-1 text-2xl font-bold" x-text="kpi.po_count"></div>
            <div class="text-xs text-slate-500 mt-1">
              <span x-text="kpi.paid_po_count" class="text-green-600 font-medium"></span> sudah dibayar
            </div>
          </div>
        </div>
        <div class="mt-2">
          <svg viewBox="0 0 210 38" class="w-full h-10">
            <path :d="sparkPath(spark_data.po_spark)" fill="rgba(47,134,255,0.12)"></path>
            <path :d="sparkStroke(spark_data.po_spark)" stroke="rgb(47,134,255)" stroke-width="2" fill="none"></path>
          </svg>
        </div>
      </div>

      {{-- Total Item --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-package text-2xl text-green-600'></i>
          </div>
          <div class="flex-1">
            <div class="text-xs text-slate-500">Total Item Dibeli</div>
            <div class="mt-1 text-2xl font-bold" x-text="kpi.total_items"></div>
            <div class="text-xs text-slate-500 mt-1">Qty semua item</div>
          </div>
        </div>
        <div class="mt-2">
          <svg viewBox="0 0 210 38" class="w-full h-10">
            <path :d="sparkPath(spark_data.item_spark)" fill="rgba(16,185,129,0.12)"></path>
            <path :d="sparkStroke(spark_data.item_spark)" stroke="rgb(16,185,129)" stroke-width="2" fill="none"></path>
          </svg>
        </div>
      </div>

      {{-- Total Pembelian --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-indigo-600'></i>
          </div>
          <div class="flex-1">
            <div class="text-xs text-slate-500">Total Pembelian</div>
            <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.total_amount)"></div>
            <div class="text-xs text-slate-500 mt-1">
              <span x-text="idr(kpi.paid_amount)" class="text-green-600 font-medium"></span> sudah dibayar
            </div>
          </div>
        </div>
        <div class="mt-2">
          <svg viewBox="0 0 210 38" class="w-full h-10">
            <path :d="sparkPath(spark_data.amount_spark)" fill="rgba(99,102,241,0.12)"></path>
            <path :d="sparkStroke(spark_data.amount_spark)" stroke="rgb(99,102,241)" stroke-width="2" fill="none"></path>
          </svg>
        </div>
      </div>

      {{-- Outstanding --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
            <i class='bx bx-time-five text-2xl text-amber-600'></i>
          </div>
          <div class="flex-1">
            <div class="text-xs text-slate-500">Belum Dibayar</div>
            <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.outstanding)"></div>
            <div class="text-xs text-slate-500 mt-1">Status draft</div>
          </div>
        </div>
        <div class="mt-2">
          <svg viewBox="0 0 210 38" class="w-full h-10">
            <path :d="sparkPath(spark_data.paid_spark)" fill="rgba(245,158,11,0.12)"></path>
            <path :d="sparkStroke(spark_data.paid_spark)" stroke="rgb(245,158,11)" stroke-width="2" fill="none"></path>
          </svg>
        </div>
      </div>
    </section>

    {{-- ====== Grafik Per Outlet & Top Items ====== --}}
    <section x-show="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      {{-- Bar Chart Per Outlet --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold">Total Pembelian per Outlet</div>
            <div class="text-xs text-slate-500" x-text="periode_label"></div>
          </div>
          <div class="text-xs text-slate-500">IDR</div>
        </div>
        <div class="mt-3 h-40 flex items-end gap-2 overflow-hidden">
          <template x-for="b in outlet_data" :key="b.name">
            <div class="flex-1 flex flex-col items-center">
              <div class="w-full rounded-md bg-primary-200 hover:bg-primary-300 transition cursor-pointer"
                   :style="`height:${b.height}px`"
                   :title="`${b.name}: ${idr(b.value)} - ${b.po_count} PO`"
                   @click="showOutletDetail(b)"></div>
              <div class="mt-1 text-xs text-slate-600 truncate w-full text-center" x-text="b.name"></div>
              <div class="text-xs text-slate-500" x-text="idrCompact(b.value)"></div>
            </div>
          </template>
          <div x-show="outlet_data.length === 0" class="flex-1 flex items-center justify-center text-slate-500 text-sm">
            Tidak ada data
          </div>
        </div>
      </div>

      {{-- Top Items yang Sering Dibeli --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold">Item Paling Sering Dibeli</div>
            <div class="text-xs text-slate-500" x-text="periode_label"></div>
          </div>
          <div class="text-xs text-slate-500" x-text="`${top_items.length} item`"></div>
        </div>

        <div class="mt-3 space-y-3">
          <template x-for="item in top_items" :key="item.id">
            <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3 hover:bg-slate-50 cursor-pointer"
                 @click="showItemDetail(item)">
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="font-medium truncate" x-text="item.nama"></span>
                  <span :class="getItemTypeBadgeClass(item.tipe)" 
                        class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                        x-text="item.tipe"></span>
                </div>
                <div class="text-xs text-slate-500">
                  Kode: <span x-text="item.kode" class="font-mono"></span> | 
                  Satuan: <span x-text="item.satuan"></span>
                </div>
                <div class="flex items-center gap-4 mt-2 text-xs">
                  <div class="text-blue-600">
                    <i class='bx bx-purchase-tag-alt'></i>
                    <span x-text="formatNumber(item.total_dibeli)"></span> dibeli
                  </div>
                  <div class="text-green-600">
                    <i class='bx bx-package'></i>
                    <span x-text="formatNumber(item.stok_sekarang)"></span> stok
                  </div>
                  <div class="text-amber-600" x-show="item.stok_terakhir > 0">
                    <i class='bx bx-plus-circle'></i>
                    +<span x-text="formatNumber(item.stok_terakhir)"></span> terakhir
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-xs text-slate-500">Transaksi</div>
                <div class="font-semibold" x-text="item.total_transaksi"></div>
              </div>
            </div>
          </template>
          <div x-show="top_items.length === 0" class="text-center py-4 text-slate-500 text-sm">
            <i class='bx bx-package text-2xl mb-2 text-slate-300'></i>
            <div>Tidak ada data item</div>
          </div>
        </div>
      </div>
    </section>

    {{-- ====== Tabel Transaksi Terakhir ====== --}}
    <section x-show="!loading" class="rounded-2xl border border-slate-200 bg-white p-0 shadow-card">
      <div class="p-4 flex items-center justify-between">
        <div>
          <div class="text-sm font-semibold">10 Transaksi Terakhir</div>
          <div class="text-xs text-slate-500" x-text="periode_label"></div>
        </div>
        <div class="flex gap-2">
          <button @click="exportData('csv')" class="h-9 px-3 rounded-lg border border-slate-200 text-sm hover:bg-slate-50 flex items-center gap-2">
            <i class='bx bx-spreadsheet'></i> Export CSV
          </button>
          <button @click="exportData('pdf')" class="h-9 px-3 rounded-lg border border-slate-200 text-sm hover:bg-slate-50 flex items-center gap-2">
            <i class='bx bx-file'></i> Export PDF
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-[960px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left w-12">No</th>
              <th class="px-3 py-2 text-left">No PO</th>
              <th class="px-3 py-2 text-left">Tanggal</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Supplier</th>
              <th class="px-3 py-2 text-right">Total Item</th>
              <th class="px-3 py-2 text-right">Total Harga</th>
              <th class="px-3 py-2 text-right">Status</th>
              <th class="px-3 py-2 text-center w-24">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="last_transactions.length === 0">
              <tr><td colspan="9" class="px-3 py-6 text-center text-slate-500">Tidak ada data transaksi</td></tr>
            </template>

            <template x-for="(t, i) in last_transactions" :key="t.id">
              <tr class="border-t border-slate-100 hover:bg-slate-50">
                <td class="px-3 py-2" x-text="i + 1"></td>
                <td class="px-3 py-2 font-mono text-sm font-medium" x-text="t.no_po"></td>
                <td class="px-3 py-2" x-text="formatDate(t.tanggal)"></td>
                <td class="px-3 py-2" x-text="t.outlet"></td>
                <td class="px-3 py-2" x-text="t.supplier"></td>
                <td class="px-3 py-2 text-right" x-text="t.total_item"></td>
                <td class="px-3 py-2 text-right font-medium" x-text="idr(t.total_harga)"></td>
                <td class="px-3 py-2 text-center">
                  <span :class="getStatusBadgeClass(t.status)" 
                        class="px-2 py-1 rounded-full text-xs font-medium"
                        x-text="getStatusText(t.status)"></span>
                  <div x-show="t.is_paid" class="text-[10px] text-green-600 mt-1">
                    <i class='bx bx-check'></i> Lunas
                  </div>
                  <div x-show="!t.is_paid" class="text-[10px] text-amber-600 mt-1">
                    <i class='bx bx-time'></i> Belum Bayar
                  </div>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-1">
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50" 
                            @click="showTransactionDetail(t)"
                            title="Detail Transaksi">
                      <i class='bx bx-show-alt'></i>
                    </button>
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50"
                            @click="printPO(t.id)"
                            title="Print PO">
                      <i class='bx bx-printer'></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  {{-- ====== Alpine Store ====== --}}
  <script>
    function purchasesDashboard(){
      return {
        loading: true,
        outlets: [],
        kpi: { 
          po_count: 0,
          paid_po_count: 0, 
          total_items: 0, 
          total_amount: 0,
          paid_amount: 0,
          outstanding: 0 
        },
        spark_data: {
          po_spark: [],
          item_spark: [],
          amount_spark: [],
          paid_spark: []
        },
        outlet_data: [],
        top_items: [],
        last_transactions: [],
        periode_label: '',
        filter: { 
          outlet: 'all', 
          from: '', 
          to: '' 
        },

        async init() {
          await this.loadOutlets();
          await this.applyFilters();
        },

        async loadOutlets() {
          try {
            const response = await fetch('{{ route("pembelian.dashboard.outlets") }}');
            const result = await response.json();
            
            if (result.success) {
              this.outlets = result.outlets;
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },

        async applyFilters() {
          this.loading = true;
          
          try {
            const params = new URLSearchParams();
            if (this.filter.outlet !== 'all') params.append('outlet', this.filter.outlet);
            if (this.filter.from) params.append('from', this.filter.from);
            if (this.filter.to) params.append('to', this.filter.to);

            const response = await fetch(`{{ route('pembelian.dashboard.data') }}?${params}`);
            const result = await response.json();

            if (result.success) {
              const data = result.data;
              this.kpi = data.kpi;
              this.spark_data = data.spark_data;
              this.outlet_data = this.prepareOutletData(data.outlet_data);
              this.top_items = data.top_items;
              this.last_transactions = data.last_transactions;
              this.periode_label = data.periode_label;
            } else {
              console.error('Failed to load dashboard data:', result.message);
            }
          } catch (error) {
            console.error('Error applying filters:', error);
          } finally {
            this.loading = false;
          }
        },

        prepareOutletData(outletData) {
          if (!outletData || outletData.length === 0) return [];
          
          const maxValue = Math.max(...outletData.map(item => item.value));
          const maxHeight = 140; // Maximum height for bars
          
          return outletData.map(item => ({
            ...item,
            height: Math.max(6, Math.round((item.value / maxValue) * maxHeight))
          }));
        },

        resetFilter() {
          this.filter = {
            outlet: 'all',
            from: '',
            to: ''
          };
          this.applyFilters();
        },

        // Formatting helpers
        idr(n) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(n || 0);
        },

        idrCompact(n) {
          if (n >= 1000000000) {
            return 'Rp' + (n / 1000000000).toFixed(1) + 'M';
          } else if (n >= 1000000) {
            return 'Rp' + (n / 1000000).toFixed(1) + 'Jt';
          } else if (n >= 1000) {
            return 'Rp' + (n / 1000).toFixed(0) + 'Rb';
          }
          return this.idr(n);
        },

        formatNumber(n) {
          return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        formatDate(dateString) {
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          });
        },

        getStatusText(status) {
          const statusMap = {
            'draft': 'Draft',
            'dibayar': 'Dibayar',
            'diproses': 'Diproses',
            'dikirim': 'Dikirim',
            'diterima': 'Diterima',
            'dibatalkan': 'Dibatalkan'
          };
          return statusMap[status] || status;
        },

        getStatusBadgeClass(status) {
          const classMap = {
            'draft': 'bg-amber-100 text-amber-800',
            'dibayar': 'bg-green-100 text-green-800',
            'diproses': 'bg-blue-100 text-blue-800',
            'dikirim': 'bg-orange-100 text-orange-800',
            'diterima': 'bg-purple-100 text-purple-800',
            'dibatalkan': 'bg-red-100 text-red-800'
          };
          return classMap[status] || 'bg-slate-100 text-slate-800';
        },

        getItemTypeBadgeClass(type) {
          const classMap = {
            'produk': 'bg-blue-100 text-blue-800',
            'bahan': 'bg-green-100 text-green-800',
            'manual': 'bg-slate-100 text-slate-800'
          };
          return classMap[type] || 'bg-slate-100 text-slate-800';
        },

        // Sparkline helpers
        sparkPath(arr) {
          const w = 210, h = 38, base = h - 2;
          if (!arr || arr.length === 0) return `M0,${base} L${w},${base} L${w},${h} L0,${h}Z`;
          
          const max = Math.max(1, ...arr);
          const step = w / (arr.length - 1);
          
          let d = `M0,${base}`;
          arr.forEach((v, i) => {
            const y = base - (v / max) * (h - 8);
            const x = i * step;
            d += ` L${x},${y}`;
          });
          d += ` L${w},${h} L0,${h} Z`;
          return d;
        },

        sparkStroke(arr) {
          const w = 210, h = 38, base = h - 2;
          if (!arr || arr.length === 0) return `M0,${base} L${w},${base}`;
          
          const max = Math.max(1, ...arr);
          const step = w / (arr.length - 1);
          
          let d = `M0,${base - (arr[0] / max) * (h - 8)}`;
          arr.forEach((v, i) => {
            const y = base - (v / max) * (h - 8);
            const x = i * step;
            d += ` L${x},${y}`;
          });
          return d;
        },

        // Action methods
        showOutletDetail(outlet) {
          alert(`Detail Outlet: ${outlet.name}\nTotal Pembelian: ${this.idr(outlet.value)}\nJumlah PO: ${outlet.po_count}\nTotal Item: ${outlet.item_count}`);
        },

        showItemDetail(item) {
          alert(
            `Detail Item: ${item.nama}
Tipe       : ${item.tipe}
Kode       : ${item.kode}
Satuan     : ${item.satuan}
Total Dibeli: ${this.formatNumber(item.total_dibeli)} ${item.satuan}
Transaksi  : ${item.total_transaksi} kali
Stok Sekarang: ${this.formatNumber(item.stok_sekarang)} ${item.satuan}
Penambahan Terakhir: +${this.formatNumber(item.stok_terakhir)} ${item.satuan}`
          );
        },

        showTransactionDetail(transaction) {
          const statusInfo = transaction.is_pid ? 
            'Status: Lunas (Sudah Dibayar)' : 
            'Status: Belum Dibayar (Draft)';
            
          alert(
            `Detail Pembelian:
No PO      : ${transaction.no_po}
Tanggal    : ${this.formatDate(transaction.tanggal)}
Outlet     : ${transaction.outlet}
Supplier   : ${transaction.supplier}
Total Item : ${transaction.total_item}
Total Harga: ${this.idr(transaction.total_harga)}
${statusInfo}`
          );
        },

        printPO(poId) {
          window.open(`{{ route('pembelian.purchase-order.print', '') }}/${poId}`, '_blank');
        },

        exportData(format) {
          // Simple export implementation
          const data = {
            kpi: this.kpi,
            outlet_data: this.outlet_data,
            top_items: this.top_items,
            last_transactions: this.last_transactions,
            periode: this.periode_label,
            filter: this.filter
          };
          
          if (format === 'csv') {
            this.exportToCSV(data);
          } else {
            this.exportToPDF(data);
          }
        },

        exportToCSV(data) {
          // Simple CSV export for transactions
          const headers = ['No PO', 'Tanggal', 'Outlet', 'Supplier', 'Total Item', 'Total Harga', 'Status'];
          const csvContent = [
            headers.join(','),
            ...data.last_transactions.map(t => [
              t.no_po,
              t.tanggal,
              t.outlet,
              t.supplier,
              t.total_item,
              t.total_harga,
              t.status
            ].join(','))
          ].join('\n');

          const blob = new Blob([csvContent], { type: 'text/csv' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `laporan-pembelian-${new Date().toISOString().split('T')[0]}.csv`;
          a.click();
          window.URL.revokeObjectURL(url);
        },

        exportToPDF(data) {
          // Redirect to PDF export route
          const params = new URLSearchParams();
          if (this.filter.outlet !== 'all') params.append('outlet', this.filter.outlet);
          if (this.filter.from) params.append('from', this.filter.from);
          if (this.filter.to) params.append('to', this.filter.to);

          window.open(`{{ route('pembelian.purchase-order.export.pdf') }}?${params}`, '_blank');
        }
      }
    }
  </script>
</x-layouts.admin>
