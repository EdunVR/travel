{{-- resources/views/admin/finance/hutang/index.blade.php --}}
<x-layouts.admin :title="'Hutang'">
  <div x-data="hutangManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Hutang</h1>
        <p class="text-slate-600 text-sm">Kelola dan monitor hutang supplier</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="refreshData()" :disabled="isLoading" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Total Hutang</p>
            <p class="text-2xl font-bold text-slate-800 mt-1" x-text="formatCurrency(summary.total_hutang)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-red-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Sudah Dibayar</p>
            <p class="text-2xl font-bold text-green-600 mt-1" x-text="formatCurrency(summary.total_dibayar)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-green-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Sisa Hutang</p>
            <p class="text-2xl font-bold text-orange-600 mt-1" x-text="formatCurrency(summary.total_sisa)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
            <i class='bx bx-time-five text-2xl text-orange-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Jatuh Tempo</p>
            <p class="text-2xl font-bold text-red-600 mt-1" x-text="summary.count_overdue"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
            <i class='bx bx-error text-2xl text-red-600'></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadHutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select x-model="filters.status" @change="loadHutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Status</option>
            <option value="belum_lunas">Belum Lunas</option>
            <option value="lunas">Lunas</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadHutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadHutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Cari</label>
          <input type="text" x-model="filters.search" @input.debounce.500ms="loadHutangData()" placeholder="Nama supplier..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data hutang...</p>
    </div>

    {{-- Hutang Table --}}
    <div x-show="!isLoading && hutangData.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">No PO</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Tanggal</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Supplier</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Outlet</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Jumlah Hutang</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Dibayar</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Sisa</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Jatuh Tempo</th>
              <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="(hutang, index) in hutangData" :key="hutang.id_hutang">
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-3">
                  <button @click="showPOPDF(hutang.id_purchase_order)" class="font-medium text-blue-600 hover:text-blue-800 hover:underline" x-text="hutang.po_number"></button>
                </td>
                <td class="px-4 py-3 text-slate-600" x-text="formatDate(hutang.tanggal)"></td>
                <td class="px-4 py-3">
                  <div class="font-medium text-slate-800" x-text="hutang.nama_supplier"></div>
                </td>
                <td class="px-4 py-3 text-slate-600" x-text="hutang.outlet"></td>
                <td class="px-4 py-3 text-right font-medium" x-text="formatCurrency(hutang.jumlah_hutang)"></td>
                <td class="px-4 py-3 text-right text-green-600" x-text="formatCurrency(hutang.jumlah_dibayar)"></td>
                <td class="px-4 py-3 text-right font-semibold" :class="hutang.sisa_hutang > 0 ? 'text-orange-600' : 'text-slate-600'" x-text="formatCurrency(hutang.sisa_hutang)"></td>
                <td class="px-4 py-3">
                  <span x-show="hutang.tanggal_jatuh_tempo" :class="hutang.is_overdue ? 'text-red-600 font-medium' : 'text-slate-600'" x-text="formatDate(hutang.tanggal_jatuh_tempo)"></span>
                  <span x-show="!hutang.tanggal_jatuh_tempo" class="text-slate-400">-</span>
                  <div x-show="hutang.is_overdue" class="text-xs text-red-600 mt-1">
                    Terlambat <span x-text="hutang.days_overdue"></span> hari
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span x-show="hutang.status === 'lunas'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Lunas
                  </span>
                  <span x-show="hutang.status === 'dibayar_sebagian'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Dibayar Sebagian
                  </span>
                  <span x-show="hutang.status === 'belum_lunas' && !hutang.is_overdue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    Belum Lunas
                  </span>
                  <span x-show="hutang.status === 'belum_lunas' && hutang.is_overdue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Jatuh Tempo
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button x-show="hutang.status !== 'lunas'" @click="redirectToPOPayment(hutang.id_purchase_order)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 text-xs font-medium">
                      <i class='bx bx-credit-card'></i> Bayar
                    </button>
                    <span x-show="hutang.status === 'lunas'" class="text-xs text-slate-500">-</span>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && hutangData.length === 0" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-receipt text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Tidak ada data hutang</h3>
      <p class="text-slate-600 mb-4">Belum ada hutang yang tercatat untuk filter yang dipilih.</p>
    </div>

    {{-- Modal Print PDF --}}
    <div x-show="showPrintModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showPrintModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closePrintModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showPrintModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-6xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle">
          
          {{-- Modal Header --}}
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Purchase Order</h3>
            <button @click="closePrintModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Modal Body --}}
          <div class="p-0">
            <iframe x-show="printPdfUrl" :src="printPdfUrl" class="w-full h-[80vh]" frameborder="0"></iframe>
            <div x-show="!printPdfUrl" class="p-8 text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-4 text-slate-600">Memuat purchase order...</p>
            </div>
          </div>

          {{-- Modal Footer --}}
          <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200 bg-slate-50">
            <button @click="closePrintModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function hutangManagement() {
      return {
        routes: {
          outletsData: '{{ route("finance.outlets.data") }}',
          hutangData: '{{ route("finance.hutang.data") }}',
          hutangDetail: '{{ route("finance.hutang.detail", ":id") }}',
          poIndex: '{{ route("pembelian.purchase-order.index") }}',
          poPrint: '{{ route("pembelian.purchase-order.print", ":id") }}'
        },
        filters: {
          outlet_id: '',
          status: 'all',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          search: ''
        },
        outlets: [],
        hutangData: [],
        summary: {
          total_hutang: 0,
          total_dibayar: 0,
          total_sisa: 0,
          count_belum_lunas: 0,
          count_lunas: 0,
          count_overdue: 0
        },
        isLoading: false,
        showPrintModal: false,
        printPdfUrl: '',

        async init() {
          await this.loadOutlets();
          await this.loadHutangData();
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const data = await response.json();
            if (data.success) {
              this.outlets = data.data;
              if (this.outlets.length > 0 && !this.filters.outlet_id) {
                this.filters.outlet_id = this.outlets[0].id_outlet;
              }
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
            this.showNotification('error', 'Gagal memuat data outlet');
          }
        },

        async loadHutangData() {
          this.isLoading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id || '',
              status: this.filters.status,
              start_date: this.filters.start_date,
              end_date: this.filters.end_date,
              search: this.filters.search
            });

            const response = await fetch(`${this.routes.hutangData}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.hutangData = data.data;
              this.summary = data.summary;
            } else {
              this.showNotification('error', data.message || 'Gagal memuat data hutang');
            }
          } catch (error) {
            console.error('Error loading hutang data:', error);
            this.showNotification('error', 'Gagal memuat data hutang');
          } finally {
            this.isLoading = false;
          }
        },

        closePrintModal() {
          this.showPrintModal = false;
          this.printPdfUrl = '';
        },

        async showPOPDF(poId) {
          this.showPrintModal = true;
          const printUrl = this.routes.poPrint.replace(':id', poId);
          this.printPdfUrl = printUrl;
        },

        async redirectToPOPayment(poId) {
          if (!poId) {
            this.showNotification('error', 'Data purchase order tidak tersedia');
            return;
          }
          
          // Redirect ke halaman PO dengan parameter untuk auto-open modal pembayaran
          window.location.href = `${this.routes.poIndex}?po_id=${poId}&open_payment=1`;
        },

        async refreshData() {
          await this.loadHutangData();
          this.showNotification('success', 'Data berhasil dimuat ulang');
        },

        formatCurrency(value) {
          if (!value && value !== 0) return 'Rp 0';
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(value);
        },

        formatDate(dateString) {
          if (!dateString) return '-';
          const date = new Date(dateString);
          return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          }).format(date);
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
