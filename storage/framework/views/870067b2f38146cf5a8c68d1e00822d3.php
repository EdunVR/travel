
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Piutang']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Piutang')]); ?>
  <div x-data="piutangManagement()" x-init="init()" class="space-y-6">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Piutang</h1>
        <p class="text-slate-600 text-sm">Kelola dan monitor piutang pelanggan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" 
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i>
            <span>Export</span>
            <i class='bx bx-chevron-down text-sm'></i>
          </button>

          <div x-show="exportOpen" 
               @click.away="exportOpen = false"
               x-transition
               class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportExcel(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i>
              <span>Export ke Excel</span>
            </button>
            <button @click="exportPdf(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl border-t border-slate-100">
              <i class='bx bxs-file-pdf text-red-600'></i>
              <span>Export ke PDF</span>
            </button>
          </div>
        </div>

        <button @click="refreshData()" :disabled="isLoading" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Total Piutang</p>
            <p class="text-2xl font-bold text-slate-800 mt-1" x-text="formatCurrency(summary.total_piutang)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-blue-600'></i>
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
            <p class="text-sm text-slate-600">Sisa Piutang</p>
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

    
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadPiutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select x-model="filters.status" @change="loadPiutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Status</option>
            <option value="belum_lunas">Belum Lunas</option>
            <option value="lunas">Lunas</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadPiutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadPiutangData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Cari</label>
          <input type="text" x-model="filters.search" @input.debounce.500ms="loadPiutangData()" placeholder="Nama customer..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data piutang...</p>
    </div>

    
    <div x-show="!isLoading && piutangData.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Kode Booking</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Tanggal</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Customer</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Outlet</th>
              <th class="px-4 py-2 text-right font-semibold text-slate-700">Jumlah Piutang</th>
              <th class="px-4 py-2 text-right font-semibold text-slate-700">Dibayar</th>
              <th class="px-4 py-2 text-right font-semibold text-slate-700">Sisa</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Jatuh Tempo</th>
              <th class="px-4 py-2 text-center font-semibold text-slate-700">Status</th>
              <th class="px-4 py-2 text-center font-semibold text-slate-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="(piutang, index) in paginatedData" :key="`travel-${piutang.id_piutang}`">
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-2">
                  <span class="font-medium text-blue-600" x-text="piutang.invoice_number"></span>
                </td>
                <td class="px-4 py-2 text-slate-600" x-text="formatDate(piutang.tanggal)"></td>
                <td class="px-4 py-2">
                  <div class="font-medium text-slate-800" x-text="piutang.nama_customer"></div>
                </td>
                <td class="px-4 py-2 text-slate-600" x-text="piutang.outlet"></td>
                <td class="px-4 py-2 text-right font-medium" x-text="formatCurrency(piutang.jumlah_piutang)"></td>
                <td class="px-4 py-2 text-right text-green-600" x-text="formatCurrency(piutang.jumlah_dibayar)"></td>
                <td class="px-4 py-2 text-right font-semibold" :class="piutang.sisa_piutang > 0 ? 'text-orange-600' : 'text-slate-600'" x-text="formatCurrency(piutang.sisa_piutang)"></td>
                <td class="px-4 py-2">
                  <span x-show="piutang.tanggal_jatuh_tempo" :class="piutang.is_overdue ? 'text-red-600 font-medium' : 'text-slate-600'" x-text="formatDate(piutang.tanggal_jatuh_tempo)"></span>
                  <span x-show="!piutang.tanggal_jatuh_tempo" class="text-slate-400">-</span>
                  <div x-show="piutang.is_overdue" class="text-xs text-red-600 mt-1">
                    Terlambat <span x-text="piutang.days_overdue"></span> hari
                  </div>
                </td>
                <td class="px-4 py-2 text-center">
                  <span x-show="piutang.status === 'lunas'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Lunas
                  </span>
                  <span x-show="piutang.status === 'dibayar_sebagian'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Dibayar Sebagian
                  </span>
                  <span x-show="piutang.status === 'belum_lunas' && !piutang.is_overdue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    Belum Lunas
                  </span>
                  <span x-show="piutang.status === 'belum_lunas' && piutang.is_overdue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Jatuh Tempo
                  </span>
                </td>
                <td class="px-4 py-2 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <button x-show="piutang.status !== 'lunas'" @click="redirectToBookingPayment(piutang.source_id)" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 text-xs font-medium">
                      <i class='bx bx-credit-card text-sm'></i> Bayar
                    </button>
                    <span x-show="piutang.status === 'lunas'" class="text-xs text-slate-500">-</span>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      
      
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center gap-2 text-sm text-slate-600">
          <span>Menampilkan</span>
          <select x-model="pagination.perPage" @change="changePage(1)" class="rounded-lg border border-slate-200 px-2 py-1 text-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>dari <span x-text="piutangData.length"></span> data</span>
        </div>
        
        <div class="flex items-center gap-2">
          <button @click="changePage(pagination.currentPage - 1)" :disabled="pagination.currentPage === 1" class="px-3 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
            <i class='bx bx-chevron-left'></i>
          </button>
          
          <template x-for="page in paginationPages" :key="page">
            <button @click="changePage(page)" :class="page === pagination.currentPage ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-3 py-1 rounded-lg border border-slate-200 text-sm min-w-[2rem]" x-text="page"></button>
          </template>
          
          <button @click="changePage(pagination.currentPage + 1)" :disabled="pagination.currentPage === pagination.totalPages" class="px-3 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
            <i class='bx bx-chevron-right'></i>
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="!isLoading && piutangData.length === 0" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-receipt text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Tidak ada data piutang</h3>
      <p class="text-slate-600 mb-4">Belum ada piutang yang tercatat untuk filter yang dipilih.</p>
    </div>

    
    <div x-show="showPrintModal" x-cloak class="fixed inset-0 z-50" style="display: none;">
      <div x-show="showPrintModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closePrintModal()"></div>

      <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none overflow-y-auto">
        <div x-show="showPrintModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="relative w-full max-w-[98vw] overflow-hidden text-left transition-all transform bg-white rounded-2xl shadow-xl flex flex-col pointer-events-auto" style="height: calc(100vh - 2rem);">
          
          
          <div class="flex items-center justify-between px-4 py-2 border-b border-slate-200 flex-shrink-0">
            <h3 class="text-lg font-semibold text-slate-800">Invoice Penjualan</h3>
            <button @click="closePrintModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          
          <div class="flex-1 overflow-auto min-h-0">
            <iframe x-show="printPdfUrl" :src="printPdfUrl" class="w-full h-full" frameborder="0" data-no-auto-resize="true" style="min-height: 100%; display: block;"></iframe>
            <div x-show="!printPdfUrl" class="flex items-center justify-center h-full min-h-[400px]">
              <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-slate-600">Memuat invoice...</p>
              </div>
            </div>
          </div>

          
          <div class="flex items-center justify-end gap-2 px-4 py-2 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button @click="closePrintModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closeDetailModal()"></div>

      <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none overflow-y-auto">
        <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95" class="relative w-full max-w-5xl overflow-hidden text-left transition-all transform bg-white rounded-2xl shadow-xl pointer-events-auto my-4">
          
          
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Detail Piutang</h3>
            <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          
          <div x-show="loadingDetail" class="p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-slate-600">Memuat detail...</p>
          </div>

          <div x-show="!loadingDetail && detailData" class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
            
            
            <div class="rounded-xl border border-slate-200 p-4">
              <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <i class='bx bx-receipt'></i> Informasi Piutang
              </h4>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-slate-600">Customer</p>
                  <p class="font-medium" x-text="detailData?.piutang?.nama_customer"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Outlet</p>
                  <p class="font-medium" x-text="detailData?.piutang?.outlet"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Tanggal</p>
                  <p class="font-medium" x-text="formatDate(detailData?.piutang?.tanggal)"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Jatuh Tempo</p>
                  <p class="font-medium" :class="detailData?.piutang?.is_overdue ? 'text-red-600' : ''" x-text="detailData?.piutang?.tanggal_jatuh_tempo ? formatDate(detailData.piutang.tanggal_jatuh_tempo) : '-'"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Jumlah Piutang</p>
                  <p class="font-semibold text-lg" x-text="formatCurrency(detailData?.piutang?.jumlah_piutang)"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Sudah Dibayar</p>
                  <p class="font-semibold text-lg text-green-600" x-text="formatCurrency(detailData?.piutang?.jumlah_dibayar)"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Sisa Piutang</p>
                  <p class="font-semibold text-lg text-orange-600" x-text="formatCurrency(detailData?.piutang?.sisa_piutang)"></p>
                </div>
                <div>
                  <p class="text-sm text-slate-600">Status</p>
                  <span x-show="detailData?.piutang?.status === 'lunas'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Lunas
                  </span>
                  <span x-show="detailData?.piutang?.status === 'belum_lunas'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    Belum Lunas
                  </span>
                </div>
              </div>
            </div>

            
            <div x-show="detailData?.penjualan" class="rounded-xl border border-slate-200 p-4">
              <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <i class='bx bx-shopping-bag'></i> Detail Transaksi Penjualan
              </h4>
              <div class="mb-3">
                <p class="text-sm text-slate-600">No. Invoice</p>
                <p class="font-medium text-blue-600" x-text="detailData?.penjualan?.invoice_number"></p>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50">
                    <tr>
                      <th class="px-3 py-2 text-left font-medium text-slate-700">Produk</th>
                      <th class="px-3 py-2 text-right font-medium text-slate-700">Qty</th>
                      <th class="px-3 py-2 text-right font-medium text-slate-700">Harga</th>
                      <th class="px-3 py-2 text-right font-medium text-slate-700">Diskon</th>
                      <th class="px-3 py-2 text-right font-medium text-slate-700">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200">
                    <template x-for="item in detailData?.penjualan?.items" :key="item.nama_produk">
                      <tr>
                        <td class="px-3 py-2" x-text="item.nama_produk"></td>
                        <td class="px-3 py-2 text-right" x-text="item.jumlah"></td>
                        <td class="px-3 py-2 text-right" x-text="formatCurrency(item.harga)"></td>
                        <td class="px-3 py-2 text-right" x-text="formatCurrency(item.diskon)"></td>
                        <td class="px-3 py-2 text-right font-medium" x-text="formatCurrency(item.subtotal)"></td>
                      </tr>
                    </template>
                  </tbody>
                  <tfoot class="bg-slate-50 font-semibold">
                    <tr>
                      <td colspan="4" class="px-3 py-2 text-right">Total</td>
                      <td class="px-3 py-2 text-right" x-text="formatCurrency(detailData?.penjualan?.total_harga)"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            
            <div class="rounded-xl border border-slate-200 p-4">
              <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <i class='bx bx-history'></i> Riwayat Pembayaran
              </h4>
              <div x-show="!detailData?.payment_history || detailData.payment_history.length === 0" class="text-center py-4 text-slate-500">
                Belum ada pembayaran
              </div>
              <template x-for="(payment, index) in detailData?.payment_history" :key="payment.id">
                <div class="mb-3 last:mb-0 border border-slate-200 rounded-lg p-3 bg-slate-50">
                  <div class="flex items-center justify-between mb-2">
                    <div>
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        Pembayaran #<span x-text="index + 1"></span>
                      </span>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-slate-600" x-text="formatDate(payment.tanggal_bayar)"></p>
                      <p class="font-semibold text-green-600" x-text="formatCurrency(payment.jumlah_bayar)"></p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                      <p class="text-slate-600">Jenis Pembayaran</p>
                      <p class="font-medium capitalize" x-text="payment.jenis_pembayaran"></p>
                    </div>
                    <div x-show="payment.nama_bank">
                      <p class="text-slate-600">Bank</p>
                      <p class="font-medium" x-text="payment.nama_bank"></p>
                    </div>
                    <div x-show="payment.nama_pengirim">
                      <p class="text-slate-600">Pengirim</p>
                      <p class="font-medium" x-text="payment.nama_pengirim"></p>
                    </div>
                    <div x-show="payment.penerima">
                      <p class="text-slate-600">Penerima</p>
                      <p class="font-medium" x-text="payment.penerima"></p>
                    </div>
                  </div>
                  <div x-show="payment.keterangan" class="mt-2 text-sm">
                    <p class="text-slate-600">Keterangan</p>
                    <p class="text-slate-800" x-text="payment.keterangan"></p>
                  </div>
                  <div x-show="payment.bukti_pembayaran" class="mt-2">
                    <a :href="payment.bukti_pembayaran" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                      <i class='bx bx-image'></i> Lihat Bukti Transfer
                    </a>
                  </div>
                </div>
              </template>
            </div>

            
            <div class="rounded-xl border border-slate-200 p-4">
              <h4 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <i class='bx bx-book'></i> Jurnal Terkait
              </h4>
              <div x-show="!detailData?.journals || detailData.journals.length === 0" class="text-center py-4 text-slate-500">
                Tidak ada jurnal terkait
              </div>
              <template x-for="journal in detailData?.journals" :key="journal.id">
                <div class="mb-4 last:mb-0 border border-slate-200 rounded-lg p-3">
                  <div class="flex items-center justify-between mb-2">
                    <div>
                      <p class="font-medium text-blue-600" x-text="journal.transaction_number"></p>
                      <p class="text-sm text-slate-600" x-text="formatDate(journal.transaction_date)"></p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="journal.status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800'" x-text="journal.status"></span>
                  </div>
                  <p class="text-sm text-slate-600 mb-3" x-text="journal.description"></p>
                  <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                      <thead class="bg-slate-50">
                        <tr>
                          <th class="px-2 py-1 text-left text-xs font-medium text-slate-700">Akun</th>
                          <th class="px-2 py-1 text-left text-xs font-medium text-slate-700">Keterangan</th>
                          <th class="px-2 py-1 text-right text-xs font-medium text-slate-700">Debit</th>
                          <th class="px-2 py-1 text-right text-xs font-medium text-slate-700">Kredit</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-slate-200">
                        <template x-for="detail in journal.details" :key="detail.account_code">
                          <tr>
                            <td class="px-2 py-1">
                              <span class="text-xs font-medium" x-text="detail.account_code"></span>
                              <span class="text-xs text-slate-600" x-text="' - ' + detail.account_name"></span>
                            </td>
                            <td class="px-2 py-1 text-xs text-slate-600" x-text="detail.description"></td>
                            <td class="px-2 py-1 text-right text-xs" x-text="detail.debit > 0 ? formatCurrency(detail.debit) : '-'"></td>
                            <td class="px-2 py-1 text-right text-xs" x-text="detail.credit > 0 ? formatCurrency(detail.credit) : '-'"></td>
                          </tr>
                        </template>
                      </tbody>
                      <tfoot class="bg-slate-50 font-semibold text-xs">
                        <tr>
                          <td colspan="2" class="px-2 py-1 text-right">Total</td>
                          <td class="px-2 py-1 text-right" x-text="formatCurrency(journal.total_debit)"></td>
                          <td class="px-2 py-1 text-right" x-text="formatCurrency(journal.total_credit)"></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </template>
            </div>

          </div>

          
          <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200 bg-slate-50">
            <button @click="closeDetailModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function piutangManagement() {
      return {
        routes: {
          outletsData: '<?php echo e(route("finance.outlets.data")); ?>',
          piutangData: '<?php echo e(route("finance.piutang.data")); ?>'
        },
        filters: {
          outlet_id: '',
          status: 'all',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          search: ''
        },
        outlets: [],
        piutangData: [],
        pagination: {
          currentPage: 1,
          perPage: 25,
          totalPages: 1
        },
        summary: {
          total_piutang: 0,
          total_dibayar: 0,
          total_sisa: 0,
          count_belum_lunas: 0,
          count_lunas: 0,
          count_overdue: 0
        },
        isLoading: false,
        showDetailModal: false,
        loadingDetail: false,
        detailData: null,
        showPrintModal: false,
        printPdfUrl: '',


        async init() {
          await this.loadOutlets();
          await this.loadPiutangData();
        },
        
        // Computed properties untuk paginasi
        get paginatedData() {
          const start = (this.pagination.currentPage - 1) * this.pagination.perPage;
          const end = start + this.pagination.perPage;
          return this.piutangData.slice(start, end);
        },
        
        get paginationPages() {
          const pages = [];
          const total = this.pagination.totalPages;
          const current = this.pagination.currentPage;
          
          if (total <= 7) {
            for (let i = 1; i <= total; i++) {
              pages.push(i);
            }
          } else {
            if (current <= 3) {
              for (let i = 1; i <= 5; i++) pages.push(i);
              pages.push('...');
              pages.push(total);
            } else if (current >= total - 2) {
              pages.push(1);
              pages.push('...');
              for (let i = total - 4; i <= total; i++) pages.push(i);
            } else {
              pages.push(1);
              pages.push('...');
              for (let i = current - 1; i <= current + 1; i++) pages.push(i);
              pages.push('...');
              pages.push(total);
            }
          }
          
          return pages.filter(p => p !== '...' || pages.indexOf(p) === pages.lastIndexOf(p));
        },
        
        changePage(page) {
          if (page < 1 || page > this.pagination.totalPages || page === '...') return;
          this.pagination.currentPage = page;
        },
        
        updatePagination() {
          this.pagination.totalPages = Math.ceil(this.piutangData.length / this.pagination.perPage);
          if (this.pagination.currentPage > this.pagination.totalPages) {
            this.pagination.currentPage = 1;
          }
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

        async loadPiutangData() {
          this.isLoading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id || '',
              status: this.filters.status,
              start_date: this.filters.start_date,
              end_date: this.filters.end_date,
              search: this.filters.search
            });

            const response = await fetch(`${this.routes.piutangData}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.piutangData = data.data;
              this.summary = data.summary;
              this.updatePagination();
            } else {
              this.showNotification('error', data.message || 'Gagal memuat data piutang');
            }
          } catch (error) {
            console.error('Error loading piutang data:', error);
            this.showNotification('error', 'Gagal memuat data piutang');
          } finally {
            this.isLoading = false;
          }
        },

        async showDetail(id) {
          this.showDetailModal = true;
          this.loadingDetail = true;
          this.detailData = null;

          try {
            const url = this.routes.piutangDetail.replace(':id', id);
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
              this.detailData = data.data;
            } else {
              this.showNotification('error', data.message || 'Gagal memuat detail piutang');
              this.closeDetailModal();
            }
          } catch (error) {
            console.error('Error loading piutang detail:', error);
            this.showNotification('error', 'Gagal memuat detail piutang');
            this.closeDetailModal();
          } finally {
            this.loadingDetail = false;
          }
        },

        closeDetailModal() {
          this.showDetailModal = false;
          this.detailData = null;
        },

        closePrintModal() {
          this.showPrintModal = false;
          this.printPdfUrl = '';
        },

        async showInvoicePDF(piutangId, penjualanId) {
          if (!penjualanId) {
            this.showNotification('error', 'Invoice tidak tersedia untuk piutang ini');
            return;
          }
          
          // Get sales_invoice id from penjualan id
          try {
            const url = this.routes.getSalesInvoiceId.replace(':id', penjualanId);
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success && data.sales_invoice_id) {
              // Open modal print PDF with correct ID
              this.showPrintModal = true;
              const printUrl = this.routes.invoicePrint.replace(':id', data.sales_invoice_id);
              this.printPdfUrl = printUrl;
            } else {
              this.showNotification('error', 'Invoice tidak ditemukan');
            }
          } catch (error) {
            console.error('Error getting sales invoice ID:', error);
            this.showNotification('error', 'Gagal memuat invoice');
          }
        },

        closePDFModal() {
          this.showPDFModal = false;
          this.pdfUrl = '';
        },

        async redirectToBookingPayment(bookingId) {
          if (!bookingId) {
            this.showNotification('error', 'Data booking tidak tersedia');
            return;
          }
          
          // Redirect ke halaman booking payment
          window.location.href = `<?php echo e(route('admin.inventaris.booking.index')); ?>?booking_id=${bookingId}&open_payment=1`;
        },

        closePaymentModal() {
          this.showPaymentModal = false;
          this.paymentForm = {
            piutang_id: null,
            jumlah_pembayaran: 0,
            sisa_piutang: 0,
            tanggal_pembayaran: new Date().toISOString().split('T')[0],
            keterangan: '',
            submitting: false
          };
        },

        async submitPayment() {
          if (this.paymentForm.submitting) return;

          if (this.paymentForm.jumlah_pembayaran <= 0) {
            this.showNotification('error', 'Jumlah pembayaran harus lebih dari 0');
            return;
          }

          if (this.paymentForm.jumlah_pembayaran > this.paymentForm.sisa_piutang) {
            this.showNotification('error', 'Jumlah pembayaran tidak boleh melebihi sisa piutang');
            return;
          }

          this.paymentForm.submitting = true;

          try {
            const url = this.routes.markPaid.replace(':id', this.paymentForm.piutang_id);
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({
                jumlah_pembayaran: this.paymentForm.jumlah_pembayaran,
                tanggal_pembayaran: this.paymentForm.tanggal_pembayaran,
                keterangan: this.paymentForm.keterangan
              })
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', data.message);
              this.closePaymentModal();
              await this.loadPiutangData();
            } else {
              this.showNotification('error', data.message || 'Gagal mencatat pembayaran');
            }
          } catch (error) {
            console.error('Error submitting payment:', error);
            this.showNotification('error', 'Gagal mencatat pembayaran');
          } finally {
            this.paymentForm.submitting = false;
          }
        },

        async refreshData() {
          await this.loadPiutangData();
          this.showNotification('success', 'Data berhasil dimuat ulang');
        },

        showInvoicePreview(piutang) {
          // Show booking detail - redirect to booking page
          if (piutang.source_id) {
            window.location.href = `<?php echo e(route('admin.inventaris.booking.index')); ?>?booking_id=${piutang.source_id}`;
          } else {
            this.showNotification('error', 'Data booking tidak tersedia');
          }
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
        },

        exportExcel() {
          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id || '',
            status: this.filters.status,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            search: this.filters.search
          });
          
          window.location.href = `<?php echo e(route('finance.piutang.export.excel')); ?>?${params}`;
        },

        exportPdf() {
          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id || '',
            status: this.filters.status,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            search: this.filters.search
          });
          
          window.location.href = `<?php echo e(route('finance.piutang.export.pdf')); ?>?${params}`;
        }
      };
    }
  </script>

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\piutang\index.blade.php ENDPATH**/ ?>