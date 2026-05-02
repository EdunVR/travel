{{-- resources/views/admin/penjualan/laporan/index.blade.php --}}
<x-layouts.admin :title="'Laporan Penjualan'">
  <div x-data="salesReportApp()" x-init="init()" class="space-y-6 overflow-x-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Laporan Penjualan</h1>
        <p class="text-sm text-slate-600 mt-1">Gabungan data dari Invoice, POS, dan Penjualan Antar Outlet</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="exportPdf()" :disabled="isLoading || salesData.length === 0" 
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700 disabled:opacity-50">
          <i class='bx bx-download'></i> Export PDF
        </button>
        <button @click="refreshData()" :disabled="isLoading" 
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Filter Section --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
        
        {{-- Outlet --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadData()" class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Semua Outlet (Yang Dapat Diakses)</option>
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
          <label class="block text-xs font-medium text-slate-700 mb-1">Cari</label>
          <input type="text" x-model="filters.search" @input.debounce.500ms="loadData()" 
                 placeholder="Customer / No Invoice..." 
                 class="w-full h-10 rounded-xl border border-slate-200 px-3">
        </div>

        {{-- Summary --}}
        <div class="flex items-end">
          <div class="text-sm">
            <div class="text-slate-600">Total Transaksi:</div>
            <div class="font-bold text-lg" x-text="salesData.length"></div>
          </div>
        </div>
      </div>
    </section>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data penjualan...</p>
    </div>

    {{-- Table --}}
    <section x-show="!isLoading" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="overflow-x-auto">
        <table class="min-w-[1000px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">No</th>
              <th class="px-3 py-2 text-left">Source</th>
              <th class="px-3 py-2 text-left">No Invoice</th>
              <th class="px-3 py-2 text-left">Tanggal</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Customer</th>
              <th class="px-3 py-2 text-right">Total Item</th>
              <th class="px-3 py-2 text-right">Total Harga</th>
              <th class="px-3 py-2 text-right">Diskon</th>
              <th class="px-3 py-2 text-right">Total Bayar</th>
              <th class="px-3 py-2 text-center">Pembayaran</th>
              <th class="px-3 py-2 text-left">Kasir</th>
              <th class="px-3 py-2 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="(item, index) in salesData" :key="item.id">
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
                <td class="px-3 py-2">
                  <button @click="showInvoicePreview(item)" 
                          class="font-medium text-primary-600 hover:text-primary-800 hover:underline"
                          x-text="item.invoice_number"></button>
                </td>
                <td class="px-3 py-2 text-slate-600" x-text="formatDate(item.tanggal)"></td>
                <td class="px-3 py-2" x-text="item.outlet"></td>
                <td class="px-3 py-2" x-text="item.customer"></td>
                <td class="px-3 py-2 text-right" x-text="item.total_item"></td>
                <td class="px-3 py-2 text-right" x-text="formatRupiah(item.total_harga)"></td>
                <td class="px-3 py-2 text-right" x-text="formatRupiah(item.diskon)"></td>
                <td class="px-3 py-2 text-right font-semibold" x-text="formatRupiah(item.total_bayar)"></td>
                <td class="px-3 py-2 text-center">
                  <div class="flex flex-col items-center gap-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                          :class="{
                            'bg-green-100 text-green-800': item.payment_status === 'Lunas',
                            'bg-orange-100 text-orange-800': item.payment_status === 'Dibayar Sebagian',
                            'bg-red-100 text-red-800': item.payment_status === 'Belum Lunas'
                          }"
                          x-text="item.payment_status"></span>
                    <span x-show="item.payment_method && item.source === 'pos'" 
                          class="text-xs text-slate-500"
                          x-text="item.payment_method"></span>
                  </div>
                </td>
                <td class="px-3 py-2" x-text="item.kasir"></td>
                <td class="px-3 py-2 text-center">
                  @hasPermission('sales.laporan.delete')
                  <button @click="confirmDelete(item)" 
                          class="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs">
                    <i class='bx bx-trash'></i> Hapus
                  </button>
                  @endhasPermission
                </td>
              </tr>
            </template>
          </tbody>
        </table>

        {{-- Empty State --}}
        <div x-show="salesData.length === 0" class="text-center py-12">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
            <i class='bx bx-receipt text-3xl text-slate-400'></i>
          </div>
          <p class="text-slate-600 font-medium">Tidak ada data penjualan</p>
          <p class="text-sm text-slate-500 mt-1">Belum ada transaksi untuk filter yang dipilih</p>
        </div>
      </div>
    </section>

    {{-- Modal PDF Preview - Mobile Optimized --}}
    <div x-show="showPdfModal" 
         x-cloak 
         class="fixed inset-0 z-50" 
         style="display: none;"
         x-init="$watch('showPdfModal', value => {
           if (value) {
             document.body.classList.add('modal-open');
           } else {
             document.body.classList.remove('modal-open');
           }
         })">
      {{-- Backdrop --}}
      <div x-show="showPdfModal" 
           x-transition:enter="ease-out duration-300" 
           x-transition:enter-start="opacity-0" 
           x-transition:enter-end="opacity-100" 
           x-transition:leave="ease-in duration-200" 
           x-transition:leave-start="opacity-100" 
           x-transition:leave-end="opacity-0" 
           class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" 
           @click="closePdfModal()"></div>

      {{-- Modal Container --}}
      <div class="fixed inset-0 flex items-start justify-center pt-2 pb-2 pointer-events-none overflow-y-auto">
        {{-- Modal Content --}}
        <div x-show="showPdfModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 translate-y-4" 
             class="relative w-full max-w-[98vw] overflow-hidden text-left transition-all transform bg-white rounded-2xl shadow-xl flex flex-col pointer-events-auto my-2" 
             style="height: calc(100vh - 1rem);">
          
          {{-- Modal Header --}}
          <div class="flex items-center justify-between px-4 py-2 border-b border-slate-200 bg-white flex-shrink-0">
            <h3 class="text-lg font-semibold text-slate-800">Preview Invoice</h3>
            <button @click="closePdfModal()" 
                    class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Modal Body --}}
          <div class="flex-1 overflow-auto min-h-0">
            {{-- PDF Iframe --}}
            <iframe x-show="pdfUrl" 
                    :src="pdfUrl" 
                    class="w-full h-full" 
                    frameborder="0"
                    data-no-auto-resize="true"
                    style="min-height: 100%; display: block;"
                    loading="lazy"
                    allowfullscreen></iframe>
            
            {{-- Loading State --}}
            <div x-show="!pdfUrl" class="flex items-center justify-center h-full min-h-[400px]">
              <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-slate-600">Memuat invoice...</p>
              </div>
            </div>
          </div>

          {{-- Modal Footer --}}
          <div class="flex items-center justify-end gap-2 px-4 py-2 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button @click="closePdfModal()" 
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function salesReportApp() {
      return {
        isLoading: false,
        outlets: @json($outlets),
        salesData: [],
        filters: {
          outlet_id: '',
          start_date: new Date(new Date().setDate(new Date().getDate() - 7)).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          search: ''
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
              end_date: this.filters.end_date,
              search: this.filters.search
            });

            const response = await fetch(`{{ route('admin.penjualan.laporan.data') }}?${params}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const data = await response.json();
            
            if (data.success) {
              this.salesData = data.data;
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

        async refreshData() {
          await this.loadData();
          this.showNotification('success', 'Data berhasil dimuat ulang');
        },

        confirmDelete(item) {
          if (confirm(`Hapus transaksi ${item.invoice_number}?\n\nPeringatan: Ini akan menghapus:\n- Transaksi ${item.source === 'invoice' ? 'Invoice' : 'POS'}\n- Jurnal terkait\n- Piutang terkait (jika ada)\n\nTindakan ini tidak dapat dibatalkan!`)) {
            this.deleteTransaction(item);
          }
        },

        async deleteTransaction(item) {
          try {
            const response = await fetch(`{{ route('admin.penjualan.laporan.delete', ['source' => ':source', 'id' => ':id']) }}`.replace(':source', item.source).replace(':id', item.source_id), {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const data = await response.json();
            
            if (data.success) {
              this.showNotification('success', data.message);
              await this.loadData();
            } else {
              this.showNotification('error', data.message || 'Gagal menghapus transaksi');
            }
          } catch (error) {
            console.error('Error deleting transaction:', error);
            this.showNotification('error', 'Terjadi kesalahan saat menghapus transaksi');
          }
        },

        formatDate(dateStr) {
          if (!dateStr) return '-';
          const date = new Date(dateStr);
          return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
        },

        formatRupiah(value) {
          if (!value && value !== 0) return 'Rp 0';
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(value);
        },

        async showInvoicePreview(item) {
          if (item.source === 'pos') {
            // Show POS nota PDF
            const url = `{{ route('admin.penjualan.pos.print', ':id') }}`.replace(':id', item.source_id) + '?type=besar';
            this.pdfUrl = url;
            this.showPdfModal = true;
          } else if (item.source === 'inter_outlet') {
            // Show Inter Outlet PDF
            const url = `{{ route('admin.penjualan.inter-outlet.print', ':id') }}`.replace(':id', item.source_id);
            this.pdfUrl = url;
            this.showPdfModal = true;
          } else {
            // For invoice source, we need to find the corresponding SalesInvoice
            // For now, show a message that invoice preview is not available
            this.showNotification('info', 'Preview invoice belum tersedia untuk transaksi lama. Silakan gunakan menu Invoice untuk melihat detail.');
          }
        },

        closePdfModal() {
          this.showPdfModal = false;
          this.pdfUrl = '';
          // Ensure body scroll is restored
          document.body.classList.remove('modal-open');
        },

        exportPdf() {
          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id || '',
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            search: this.filters.search
          });

          const url = `{{ route('admin.penjualan.laporan.export-pdf') }}?${params}`;
          this.pdfUrl = url;
          this.showPdfModal = true;
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
    
    /* Mobile PDF Modal Optimizations */
    @media (max-width: 640px) {
      /* Ensure modal takes full screen on mobile */
      .pdf-modal-mobile {
        height: 100vh !important;
        height: 100dvh !important; /* Dynamic viewport height for mobile browsers */
        border-radius: 0 !important;
      }
      
      /* Optimize iframe for mobile */
      .pdf-modal-mobile iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
      }
      
      /* Prevent body scroll when modal is open */
      body.modal-open {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
      }
    }
    
    /* Ensure proper z-index stacking */
    .pdf-modal {
      z-index: 9999 !important;
    }
    
    /* Smooth transitions */
    .modal-transition {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
  </style>
</x-layouts.admin>
