
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Penjualan / Invoice Penjualan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Penjualan / Invoice Penjualan')]); ?>
  <div x-data="invoicePenjualan()" x-init="init()" class="space-y-5 overflow-x-hidden">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Invoice Penjualan</h1>
        <p class="text-slate-600 text-sm">Kelola invoice penjualan dengan fitur lengkap.</p>
      </div>

      <div class="flex flex-wrap gap-2">
        
        <div class="relative">
          <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                  class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 flex items-center justify-between min-w-[200px]">
            <span x-text="getSelectedOutletsText()"></span>
            <i class='bx bx-chevron-down ml-2' :class="showOutletDropdown ? 'rotate-180' : ''"></i>
          </button>
          
          <div x-show="showOutletDropdown" x-on:click.away="closeOutletDropdown()"
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="transform opacity-100 scale-100"
               x-transition:leave-end="transform opacity-0 scale-95"
               class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
            <div class="p-3 border-b border-slate-100">
              <button x-on:click="selectAllOutlets()" class="text-xs text-primary-600 hover:text-primary-800 mr-3">Pilih Semua</button>
              <button x-on:click="clearAllOutlets()" class="text-xs text-slate-600 hover:text-slate-800">Hapus Semua</button>
            </div>
            <div class="p-2">
              <template x-for="outlet in outlets" :key="outlet.id_outlet">
                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                  <input type="checkbox" :value="outlet.id_outlet" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                         class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                  <span class="text-sm" x-text="outlet.nama_outlet"></span>
                </label>
              </template>
            </div>
          </div>
        </div>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'sales.invoice.create')): ?>
        <button @click="openCreateInvoice()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700">
          <i class='bx bx-plus'></i>Invoice Baru
        </button>
        <?php endif; ?>
        <button @click="openInvoiceSetting()" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 text-white px-4 h-10 hover:bg-amber-600">
          <i class='bx bx-cog'></i> Set Nomor Invoice
        </button>
        <button @click="openCoaSetting()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-calculator'></i> Set COA
        </button>
        <button @click="openOngkirSetting()" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 text-white px-4 h-10 hover:bg-purple-700">
          <i class='bx bx-truck'></i> Set Ongkir
        </button>

      </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-receipt text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.all || 0"></div>
            <div class="text-sm text-slate-600">Total Invoice</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center">
            <i class='bx bx-edit text-2xl text-gray-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.draft || 0"></div>
            <div class="text-sm text-slate-600">Draft</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
            <i class='bx bx-time text-2xl text-amber-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.menunggu || 0"></div>
            <div class="text-sm text-slate-600">Menunggu</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-trending-up text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.dibayar_sebagian || 0"></div>
            <div class="text-sm text-slate-600">Dibayar Sebagian</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-emerald-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.lunas || 0"></div>
            <div class="text-sm text-slate-600">Lunas</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-error-circle text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="stats.gagal || 0"></div>
            <div class="text-sm text-slate-600">Gagal</div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-2">
        <label class="text-xs text-slate-500">Outlet Terpilih</label>
        <div class="mt-1 px-3 py-2 bg-slate-50 rounded-xl border border-slate-200">
            <span class="font-medium text-slate-800" x-text="getOutletName(selectedOutlet)"></span>
        </div>
        </div>
        <div class="lg:col-span-2">
        <label class="text-xs text-slate-500">Dari Tanggal</label>
        <input type="date" x-model="filters.start_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 h-10">
        </div>
        <div class="lg:col-span-2">
        <label class="text-xs text-slate-500">s/d Tanggal</label>
        <input type="date" x-model="filters.end_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 h-10">
        </div>
        <div class="lg:col-span-3">
        <label class="text-xs text-slate-500">Pencarian</label>
        <input type="text" x-model="filters.search" placeholder="Cari no invoice, customer, item…"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 h-10">
        </div>
        <div class="lg:col-span-3 flex items-end gap-2">
        <button @click="applyFilters()" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
            <i class='bx bx-search'></i> Tampilkan
        </button>
        <div class="relative">
            <button @click="exportMenuOpen = !exportMenuOpen" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
            </button>
            <div x-cloak x-show="exportMenuOpen" @click.outside="exportMenuOpen = false"
                class="absolute right-0 mt-2 w-36 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportExcel()" class="w-full text-left px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-spreadsheet mr-2'></i> Excel
            </button>
            <button @click="exportPdf()" class="w-full text-left px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-file mr-2'></i> PDF
            </button>
            </div>
        </div>
        </div>
    </div>

    
    <div class="mt-4 flex flex-wrap items-center gap-2">
        <div class="flex flex-wrap gap-2">
        <button :class="activeTab === 'all' ? 'bg-primary-100 text-primary-700 border-primary-300' : 'bg-white text-slate-700 border-slate-200'"
                @click="setActiveTab('all')" class="inline-flex items-center gap-2 rounded-xl border px-4 h-9 hover:bg-slate-50">
            <i class='bx bx-list-ul'></i> Semua
            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full" x-text="stats.total"></span>
        </button>
        <button :class="activeTab === 'menunggu' ? 'bg-amber-100 text-amber-700 border-amber-300' : 'bg-white text-slate-700 border-slate-200'"
                @click="setActiveTab('menunggu')" class="inline-flex items-center gap-2 rounded-xl border px-4 h-9 hover:bg-slate-50">
            <i class='bx bx-time-five text-amber-600'></i> Menunggu
            <span class="bg-amber-100 text-amber-600 text-xs px-2 py-0.5 rounded-full" x-text="stats.menunggu"></span>
        </button>
        <button :class="activeTab === 'dibayar_sebagian' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-white text-slate-700 border-slate-200'"
                @click="setActiveTab('dibayar_sebagian')" class="inline-flex items-center gap-2 rounded-xl border px-4 h-9 hover:bg-slate-50">
            <i class='bx bx-trending-up text-blue-600'></i> Dibayar Sebagian
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full" x-text="stats.dibayar_sebagian"></span>
        </button>
        <button :class="activeTab === 'lunas' ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-white text-slate-700 border-slate-200'"
                @click="setActiveTab('lunas')" class="inline-flex items-center gap-2 rounded-xl border px-4 h-9 hover:bg-slate-50">
            <i class='bx bx-check-circle text-emerald-600'></i> Lunas
            <span class="bg-emerald-100 text-emerald-600 text-xs px-2 py-0.5 rounded-full" x-text="stats.lunas"></span>
        </button>
        <button :class="activeTab === 'gagal' ? 'bg-red-100 text-red-700 border-red-300' : 'bg-white text-slate-700 border-slate-200'"
                @click="setActiveTab('gagal')" class="inline-flex items-center gap-2 rounded-xl border px-4 h-9 hover:bg-slate-50">
            <i class='bx bx-error-circle text-red-600'></i> Retur/Gagal
            <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full" x-text="stats.gagal"></span>
        </button>
        </div>

        
        <div class="ml-auto">
        <div class="flex rounded-xl border border-slate-200 overflow-hidden">
            <button @click="view='grid'" :class="view==='grid' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'"
                    class="flex-1 px-3 py-2 text-sm">Grid</button>
            <button @click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'"
                    class="flex-1 px-3 py-2 text-sm">Tabel</button>
        </div>
        </div>
    </div>
    </div>

    
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data invoice...</span>
      </div>
    </div>

    
    <div x-show="!loading && view==='grid'">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="invoice in invoices" :key="invoice.id_sales_invoice">
        <div class="group rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.08)] transition overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center">
                <i class='bx bx-receipt text-xl text-slate-600'></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                <div class="font-mono text-sm font-semibold truncate" x-text="invoice.status === 'draft' ? 'DRAFT' : invoice.no_invoice"></div>
                <span :class="getStatusBadgeClass(invoice.status)" class="px-2 py-0.5 rounded-full text-[11px] font-medium" x-text="getStatusText(invoice.status)"></span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5" x-text="formatDate(invoice.tanggal)"></div>
            </div>
            <div class="text-right">
                <div class="text-xs text-slate-500">Total</div>
                <div class="font-semibold" x-text="formatCurrency(invoice.total)"></div>
            </div>
            </div>

            <div class="p-4 space-y-2">
            <div class="text-sm">
                <div class="text-slate-500 text-xs">Customer</div>
                <div class="font-medium truncate" x-text="invoice.customer_name"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                <div class="text-slate-500 text-xs">Outlet</div>
                <div class="truncate" x-text="invoice.outlet_name"></div>
                </div>
                <div>
                <div class="text-slate-500 text-xs">Jatuh Tempo</div>
                <div x-text="formatDate(invoice.due_date) || '-'"></div>
                </div>
                
                
                <template x-if="invoice.status === 'lunas' || invoice.status === 'dibayar_sebagian'">
                <div class="col-span-2 mt-2">
                    <div class="border-t pt-2">
                    <div class="text-slate-500 text-xs font-medium mb-1">Pembayaran:</div>
                    <div class="text-xs">
                        <span class="font-medium" x-text="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'Bayar Lunas' : 'Bayar Cicilan'"></span>
                    </div>
                    </div>
                </div>
                </template>
                
                
                <template x-if="invoice.status === 'menunggu'">
                <div>
                    <div class="text-slate-500 text-xs">Sisa Hari</div>
                    <div :class="getRemainingDaysClass(invoice.due_date, invoice.status)" class="font-medium text-xs"
                        x-text="getRemainingDaysText(invoice.due_date, invoice.status)"></div>
                </div>
                </template>
                
                
                <template x-if="invoice.status === 'gagal'">
                <div>
                    <div class="text-slate-500 text-xs">Sisa Hari</div>
                    <div class="text-slate-400 text-xs">-</div>
                </div>
                </template>
                
                <div>
                <div class="text-slate-500 text-xs">Items</div>
                <div class="text-xs text-slate-600" x-text="(invoice.items?.length||0)+' item'"></div>
                </div>
            </div>

            <div class="mt-2">
                <template x-if="invoice.items && invoice.items.length">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                    <template x-for="item in invoice.items.slice(0,2)" :key="item.id_sales_invoice_item">
                    <div class="text-xs text-slate-600 truncate" x-text="'• ' + item.deskripsi"></div>
                    </template>
                    <div x-show="invoice.items.length > 2" class="text-[11px] text-slate-500 mt-1">
                    ... dan <span x-text="invoice.items.length - 2"></span> item lainnya
                    </div>
                </div>
                </template>
            </div>
            </div>

            <div class="p-4 border-t border-slate-100">
            <div class="flex flex-wrap gap-2">
                <button @click="printInvoice(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs border border-slate-200 hover:bg-slate-50">
                <i class='bx bx-printer text-sm'></i> Print
                </button>
                <!-- Edit button - only for draft -->
                <template x-if="invoice.status === 'draft'">
                <button @click="editInvoice(invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs border border-slate-200 hover:bg-slate-50">
                    <i class='bx bx-edit text-sm'></i> Edit
                </button>
                </template>
                <!-- Konfirmasi button - only for draft -->
                <template x-if="invoice.status === 'draft'">
                <button @click="confirmInvoice(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class='bx bx-check-circle text-sm'></i> Konfirmasi
                </button>
                </template>
                <!-- Bayar button - only for menunggu/dibayar_sebagian -->
                <template x-if="invoice.status === 'menunggu' || invoice.status === 'dibayar_sebagian'">
                <button @click="openPaymentModal(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                    <i class='bx bx-money text-sm'></i> Bayar
                </button>
                </template>
                <template x-if="invoice.total_dibayar > 0 && invoice.status !== 'draft'">
                <button @click="openPaymentHistoryModal(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs bg-purple-100 text-purple-700 hover:bg-purple-200">
                    <i :class="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'bx bx-image' : 'bx bx-history'" class="text-sm"></i>
                    <span x-text="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'Lihat Bukti' : 'Lihat Cicilan'"></span>
                </button>
                </template>
                <template x-if="invoice.status === 'menunggu'">
                <button @click="updateInvoiceStatus(invoice.id_sales_invoice, 'gagal')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs bg-red-100 text-red-700 hover:bg-red-200">
                    <i class='bx bx-x text-sm'></i> Batalkan
                </button>
                </template>
                <button @click="deleteInvoice(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs border border-red-200 text-red-700 hover:bg-red-50">
                <i class='bx bx-trash text-sm'></i> Hapus
                </button>
            </div>
            </div>
        </div>
        </template>
    </div>

    <div x-show="invoices.length === 0 && !loading" class="text-center text-slate-500 py-8">
        <i class='bx bx-file-find text-3xl mb-2 text-slate-300'></i>
        <div>Tidak ada data invoice</div>
    </div>

    
    <div x-show="!loading && view==='grid' && pagination.last_page > 1" class="mt-6 flex items-center justify-between">
        <div class="text-sm text-slate-600">
            Menampilkan <span x-text="((pagination.current_page - 1) * pagination.per_page) + 1"></span> 
            sampai <span x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span> 
            dari <span x-text="pagination.total"></span> invoice
        </div>
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="pagination.current_page <= 1" 
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class='bx bx-chevron-left'></i> Sebelumnya
            </button>
            
            <div class="flex items-center gap-1">
                <template x-for="page in getPaginationPages()" :key="page">
                    <div>
                        <button x-show="page !== '...'" @click="goToPage(page)" 
                                :class="page === pagination.current_page ? 'bg-primary-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'"
                                class="w-10 h-10 rounded-lg border border-slate-200 text-sm font-medium"
                                x-text="page"></button>
                        <span x-show="page === '...'" class="w-10 h-10 flex items-center justify-center text-slate-400">...</span>
                    </div>
                </template>
            </div>
            
            <button @click="nextPage()" :disabled="pagination.current_page >= pagination.last_page"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Berikutnya <i class='bx bx-chevron-right'></i>
            </button>
        </div>
    </div>
    </div>

    
    <div x-show="!loading && view==='table'" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-2 py-3 text-left text-xs">No</th>
                        <th class="px-2 py-3 text-left text-xs">No Invoice</th>
                        <th class="px-2 py-3 text-left text-xs">Tanggal</th>
                        <th class="px-2 py-3 text-left text-xs">Customer</th>
                        <th class="px-2 py-3 text-left text-xs">Outlet</th>
                        <th class="px-2 py-3 text-right text-xs">Total</th>
                        <th class="px-2 py-3 text-left text-xs">Status</th>
                        <th class="px-2 py-3 text-left text-xs">Tempo</th>
                        <th class="px-2 py-3 text-left text-xs">Items</th>
                        <th class="px-2 py-3 text-left text-xs">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(invoice, index) in invoices" :key="invoice.id_sales_invoice">
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                             <td class="px-2 py-3 text-xs" x-text="index + 1"></td>
                              <td class="px-2 py-3">
                                  <div class="font-mono text-xs font-semibold truncate max-w-[100px]" x-text="invoice.no_invoice"></div>
                              </td>
                              <td class="px-2 py-3 text-xs whitespace-nowrap" x-text="formatDate(invoice.tanggal)"></td>
                              <td class="px-2 py-3">
                                  <div class="font-medium text-xs truncate max-w-[120px]" x-text="invoice.customer_name"></div>
                              </td>
                              <td class="px-2 py-3 text-xs truncate max-w-[100px]" x-text="invoice.outlet_name"></td>
                              <td class="px-2 py-3 text-right font-semibold text-xs whitespace-nowrap" x-text="formatCurrency(invoice.total)"></td>
                              <td class="px-2 py-3">
                                  <span :class="getStatusBadgeClass(invoice.status)" x-text="getStatusText(invoice.status)" class="px-2 py-0.5 rounded-full text-[10px] font-medium whitespace-nowrap"></span>
                              </td>
                              <td class="px-2 py-3">
                                  <div class="text-xs whitespace-nowrap" x-text="formatDate(invoice.due_date)"></div>
                                  <span :class="getRemainingDaysClass(invoice.due_date, invoice.status)" x-text="getRemainingDaysText(invoice.due_date, invoice.status)" class="text-[10px] font-medium"></span>
                              </td>
                            <td class="px-2 py-3">
                                <div class="max-w-[150px]">
                                    <template x-for="item in invoice.items.slice(0, 1)" :key="item.id_sales_invoice_item">
                                        <div class="text-xs text-slate-600 truncate" x-text="item.deskripsi"></div>
                                    </template>
                                    <div x-show="invoice.items.length > 1" class="text-[10px] text-slate-500">
                                        +<span x-text="invoice.items.length - 1"></span> lainnya
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex gap-1 flex-wrap">
                                    <button @click="printInvoice(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-green-100 text-green-700 hover:bg-green-200" title="Print">
                                        <i class='bx bx-printer text-xs'></i>
                                    </button>
                                    <button @click="editInvoice(invoice)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700 hover:bg-blue-200" title="Edit">
                                        <i class='bx bx-edit text-xs'></i>
                                    </button>
                                    <template x-if="invoice.status === 'menunggu' || invoice.status === 'dibayar_sebagian'">
                                        <button @click="openPaymentModal(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-emerald-100 text-emerald-700 hover:bg-emerald-200" title="Bayar">
                                            <i class='bx bx-money text-xs'></i>
                                        </button>
                                    </template>
                                    <template x-if="invoice.total_dibayar > 0">
                                        <button @click="openPaymentHistoryModal(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-purple-100 text-purple-700 hover:bg-purple-200" title="History">
                                            <i :class="invoice.status === 'lunas' && (invoice.total_dibayar === invoice.total || !invoice.total_dibayar) ? 'bx bx-image' : 'bx bx-history'" class="text-xs"></i>
                                        </button>
                                    </template>
                                    <template x-if="invoice.status === 'menunggu'">
                                        <button @click="updateInvoiceStatus(invoice.id_sales_invoice, 'gagal')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-red-100 text-red-700 hover:bg-red-200" title="Batalkan">
                                            <i class='bx bx-x text-xs'></i>
                                        </button>
                                    </template>
                                    <button @click="deleteInvoice(invoice.id_sales_invoice)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] bg-red-100 text-red-700 hover:bg-red-200" title="Hapus">
                                        <i class='bx bx-trash text-xs'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="invoices.length === 0 && !loading">
                        <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                            <i class='bx bx-file-find text-3xl mb-2 text-slate-300'></i>
                            <div>Tidak ada data invoice</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="!loading && view==='table' && pagination.last_page > 1" class="mt-6 flex items-center justify-between">
        <div class="text-sm text-slate-600">
            Menampilkan <span x-text="((pagination.current_page - 1) * pagination.per_page) + 1"></span> 
            sampai <span x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span> 
            dari <span x-text="pagination.total"></span> invoice
        </div>
        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="pagination.current_page <= 1" 
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class='bx bx-chevron-left'></i> Sebelumnya
            </button>
            
            <div class="flex items-center gap-1">
                <template x-for="page in getPaginationPages()" :key="page">
                    <div>
                        <button x-show="page !== '...'" @click="goToPage(page)" 
                                :class="page === pagination.current_page ? 'bg-primary-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'"
                                class="w-10 h-10 rounded-lg border border-slate-200 text-sm font-medium"
                                x-text="page"></button>
                        <span x-show="page === '...'" class="w-10 h-10 flex items-center justify-center text-slate-400">...</span>
                    </div>
                </template>
            </div>
            
            <button @click="nextPage()" :disabled="pagination.current_page >= pagination.last_page"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Berikutnya <i class='bx bx-chevron-right'></i>
            </button>
        </div>
    </div>

    
    <div x-show="showInvoiceModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div @click.outside="closeInvoiceModal()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="editingInvoice ? 'Edit Invoice' : 'Buat Invoice Baru'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeInvoiceModal()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <div class="space-y-4">
              <h3 class="font-semibold text-slate-700">Informasi Invoice</h3>
              
              <div>
                <label class="text-sm text-slate-600">No Invoice <span class="text-red-500">*</span></label>
                <input type="text" x-model="invoiceForm.no_invoice" readonly
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50">
              </div>

              <div>
                <label class="text-sm text-slate-600">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" x-model="invoiceForm.tanggal"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              </div>

              <div>
                <label class="text-sm text-slate-600">Tanggal Jatuh Tempo <span class="text-red-500">*</span></label>
                <input type="date" x-model="invoiceForm.due_date"
                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              </div>

              <div>
                    <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
                    <select x-model="invoiceForm.id_outlet" 
                            disabled
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 cursor-not-allowed">
                        <option value="">Pilih Outlet</option>
                        <template x-for="outlet in outlets" :key="outlet.id_outlet">
                            <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                        </template>
                    </select>
                    <div class="text-xs text-slate-500 mt-1">Outlet otomatis dipilih berdasarkan hak akses Anda</div>
                </div>

              <div>
                <label class="text-sm text-slate-600">Customer <span class="text-red-500">*</span></label>
                <div class="mt-1 relative">
                  <input type="text" x-model="customerSearch" @input.debounce.500ms="searchCustomers()"
                         placeholder="Cari customer..." class="w-full rounded-xl border border-slate-200 px-3 py-2">
                  <div x-show="customerSearchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="customer in customerSearchResults" :key="customer.id">
                      <button @click="selectCustomer(customer)" class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                        <div class="font-medium" x-text="customer.nama + (customer.type === 'member' && customer.tipe?.nama_tipe ? ' (' + customer.tipe.nama_tipe + ')' : '')"></div>
                        <div class="text-xs text-slate-500" x-show="customer.nama_perusahaan" x-text="customer.nama_perusahaan"></div>
                        <div class="text-xs text-slate-500" x-text="customer.alamat || '-'"></div>
                      </button>
                    </template>
                  </div>
                </div>
                <div x-show="invoiceForm.customer_type && invoiceForm.customer_id" class="mt-2 p-2 bg-slate-50 rounded-lg">
                  <div class="text-sm font-medium" x-text="selectedCustomer?.nama"></div>
                  <div class="text-xs text-slate-600" x-show="selectedCustomer?.nama_perusahaan" x-text="selectedCustomer?.nama_perusahaan"></div>
                  <div class="text-xs text-slate-600" x-text="selectedCustomer?.alamat"></div>
                </div>
              </div>

              <div>
                <label class="text-sm text-slate-600">Keterangan</label>
                <textarea x-model="invoiceForm.keterangan" rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              </div>
            </div>

            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-700">Items Invoice</h3>
                    <button @click="addInvoiceItem()" type="button" class="inline-flex items-center gap-1 rounded-xl bg-primary-600 text-white px-3 py-1.5 text-sm hover:bg-primary-700">
                        <i class='bx bx-plus'></i> Tambah Item
                    </button>
                </div>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <template x-for="(item, index) in invoiceForm.items" :key="index">
                        <div class="p-3 border border-slate-200 rounded-xl bg-slate-50">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 grid grid-cols-1 gap-2">
                                    <select x-model="item.tipe" @change="onItemTypeChange(item, index)" class="rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                        <option value="produk">Produk</option>
                                        <option value="ongkir">Ongkos Kirim</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                    
                                    
                                    <template x-if="item.tipe === 'produk'">
                                      <div class="space-y-2">
                                          <div class="relative">
                                              <input type="text" x-model="item.product_search" 
                                                    @input.debounce.500ms="searchProducts(item, index)"
                                                    placeholder="Cari produk..." 
                                                    class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                              <div x-show="item.product_results && item.product_results.length > 0" 
                                                  class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                  <template x-for="product in item.product_results" :key="product.id_produk">
                                                      <button @click="selectProduct(product, item, index)" 
                                                              class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-100 last:border-b-0 text-sm">
                                                          <div class="font-medium" x-text="product.nama_produk"></div>
                                                          <div class="text-xs text-slate-500">
                                                              Harga: <span x-text="formatCurrency(product.harga)"></span> | 
                                                              Stok: <span x-text="product.stok"></span>
                                                          </div>
                                                      </button>
                                                  </template>
                                              </div>
                                          </div>
                                          
                                          <div x-show="item.selectedProduct !== null" class="text-xs text-slate-600 bg-white p-2 rounded border">
                                              <div class="font-medium" x-text="item.selectedProduct ? item.selectedProduct.nama_produk : 'Produk tidak ditemukan'"></div>
                                              <div>
                                                  Stok: <span x-text="item.selectedProduct ? item.selectedProduct.stok : 0"></span>
                                              </div>
                                          </div>
                                      </div>
                                  </template>

                                    <template x-if="item.tipe === 'ongkir'">
                                        <div class="space-y-2">
                                            <div class="relative">
                                                <input type="text" x-model="item.ongkir_search" 
                                                      @input.debounce.500ms="searchOngkir(item, index)"
                                                      placeholder="Cari daerah ongkos kirim..." 
                                                      class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                                <div x-show="item.ongkir_results && item.ongkir_results.length > 0" 
                                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                    <template x-for="ongkir in item.ongkir_results" :key="ongkir.id_ongkir">
                                                        <button @click="selectOngkir(ongkir, item, index)" 
                                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-100 last:border-b-0 text-sm">
                                                            <div class="font-medium" x-text="ongkir.daerah"></div>
                                                            <div class="text-xs text-slate-500" x-text="'Harga: ' + formatCurrency(ongkir.harga)"></div>
                                                        </button>
                                                    </template>
                                                    <button @click="showNewOngkirForm(item, index)" 
                                                            class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-100 text-sm text-blue-600 font-medium">
                                                        <i class='bx bx-plus mr-1'></i> Tambah Ongkir Baru
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            
                                            <div x-show="item.show_new_ongkir" class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                                <div class="text-sm font-medium text-blue-800 mb-2">Tambah Ongkos Kirim Baru</div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="text-xs text-blue-700">Daerah</label>
                                                        <input x-model="item.new_ongkir_daerah" type="text" placeholder="Nama daerah" 
                                                              class="w-full rounded-lg border border-blue-300 px-2 py-1 text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs text-blue-700">Harga</label>
                                                        <input x-model="item.new_ongkir_harga" type="number" placeholder="Harga ongkir" 
                                                              @input="onNewOngkirInput(item, index)"
                                                              class="w-full rounded-lg border border-blue-300 px-2 py-1 text-sm">
                                                        <p class="text-xs text-blue-600 mt-1" x-show="item.new_ongkir_harga > 0" 
                                                           x-text="'≈ ' + formatCurrency(item.new_ongkir_harga)"></p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex gap-2">
                                                    <button @click="saveNewOngkir(item, index)" 
                                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 text-white px-3 py-1 text-xs hover:bg-blue-700">
                                                        <i class='bx bx-save'></i> Simpan
                                                    </button>
                                                    <button @click="cancelNewOngkir(item, index)" 
                                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1 text-xs hover:bg-slate-100">
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>

                                            
                                            <div x-show="item.selectedOngkir !== null && !item.show_new_ongkir" class="text-xs text-slate-600 bg-white p-2 rounded border">
                                                <template x-if="item.selectedOngkir">
                                                    <div>
                                                        <div class="font-medium" x-text="item.selectedOngkir.daerah"></div>
                                                        <div>Harga: <span x-text="formatCurrency(item.selectedOngkir.harga)"></span></div>
                                                    </div>
                                                </template>
                                                <template x-if="!item.selectedOngkir">
                                                    <div class="text-slate-500">Ongkir belum dipilih</div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    
                                    <template x-if="item.tipe === 'lainnya'">
                                        <input type="text" x-model="item.deskripsi" placeholder="Deskripsi item..." 
                                              class="rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                    </template>

                                    
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="min-w-0">
                                            <label class="text-xs text-slate-500">Qty</label>
                                            <input type="number" x-model="item.kuantitas" @input="calculateItemSubtotal(item, index)" 
                                                  min="0.01" step="0.01" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="text-xs text-slate-500">Satuan</label>
                                            <input type="text" x-model="item.satuan" placeholder="Unit" 
                                                  class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="text-xs text-slate-500">Harga</label>
                                            <input type="number" x-model="item.harga" @input="calculateItemSubtotal(item, index)"
                                                  class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                            <div class="text-xs text-slate-500" x-show="item.harga_khusus > 0">
                                                Khusus: <span x-text="formatCurrency(item.harga_khusus)"></span>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <label class="text-xs text-slate-500">Diskon</label>
                                            <input type="number" x-model="item.diskon" @input="applyDiscount(item, index)" 
                                                  min="0" :max="item.harga" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-sm">
                                        </div>
                                    </div>

                                    
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-semibold">
                                            Subtotal: <span x-text="formatCurrency(item.subtotal)"></span>
                                            <span x-show="item.diskon > 0" class="text-green-600 ml-2">
                                                (Diskon: <span x-text="formatCurrency(item.diskon * item.kuantitas)"></span>)
                                            </span>
                                        </div>
                                        <button @click="removeInvoiceItem(index)" type="button" class="text-red-600 hover:text-red-800">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                
                <div class="border-t border-slate-200 pt-4 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span>Subtotal:</span>
                        <span x-text="formatCurrency(calculateSubtotal())"></span>
                    </div>
                    <div x-show="calculateTotalDiscount() > 0" class="flex justify-between items-center text-sm text-green-600">
                        <span>Total Diskon:</span>
                        <span x-text="'-' + formatCurrency(calculateTotalDiscount())"></span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-semibold border-t border-slate-200 pt-2">
                        <span>Total:</span>
                        <span x-text="formatCurrency(calculateGrandTotal())"></span>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button @click="closeInvoiceModal()" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button @click="submitInvoice()" :disabled="savingInvoice" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
            <span x-show="savingInvoice" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!savingInvoice" x-text="editingInvoice ? 'Update Invoice' : 'Simpan Invoice'"></span>
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="showInvoiceSettingModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-20">
      <div @click.outside="closeInvoiceSettingModal()" class="w-full max-w-lg bg-white rounded-2xl shadow-float">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Setting Nomor Invoice</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeInvoiceSettingModal()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4">
          <div class="space-y-4">
            <div class="p-3 bg-slate-50 rounded-xl">
              <div class="text-sm text-slate-600">Nomor Saat Ini:</div>
              <div class="font-mono font-semibold" x-text="invoiceSetting.current_invoice_number"></div>
            </div>

            <div class="p-3 bg-blue-50 rounded-xl">
              <div class="text-sm text-slate-600">Nomor Berikutnya:</div>
              <div class="font-mono font-semibold" x-text="invoiceSetting.next_invoice_number"></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Mulai Nomor Dari</label>
              <input type="number" x-model="invoiceSettingForm.starting_number" min="1" max="999" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>

            <div>
                <label class="text-sm text-slate-600">Prefix Invoice</label>
                <input type="text" x-model="invoiceSettingForm.invoice_prefix" 
                    placeholder="SLS.INV" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <div class="text-xs text-slate-500 mt-1">Contoh: SLS.INV, INV, SJ, dll.</div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Tahun</label>
              <input type="number" x-model="invoiceSettingForm.year" min="2020" max="2030" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button @click="closeInvoiceSettingModal()" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button @click="updateInvoiceSetting()" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">Simpan Setting</button>
        </div>
      </div>
    </div>

    
    <div x-show="showPaymentModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="!processingPayment && (showPaymentModal = false)" class="w-full max-w-3xl bg-white rounded-2xl shadow-float my-4">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Konfirmasi Pelunasan Invoice</div>
                <button 
                    :disabled="processingPayment"
                    @click="showPaymentModal = false" 
                    class="p-2 -m-2 hover:bg-slate-100 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 max-h-[70vh] overflow-y-auto">
                <div class="space-y-4">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div>
                                <div class="text-sm text-slate-600">Invoice:</div>
                                <div class="font-mono font-semibold" x-text="paymentForm.no_invoice"></div>
                            </div>
                            <div>
                                <div class="text-sm text-slate-600">Total Invoice:</div>
                                <div class="font-semibold text-blue-600" x-text="formatCurrency(paymentForm.total)"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                            <div>
                                <div class="text-sm text-slate-600">Sudah Dibayar:</div>
                                <div class="font-semibold text-green-600" x-text="formatCurrency(paymentForm.total_dibayar || 0)"></div>
                            </div>
                            <div>
                                <div class="text-sm text-slate-600">Sisa Tagihan:</div>
                                <div class="font-semibold text-red-600" x-text="formatCurrency(paymentForm.sisa_tagihan || paymentForm.total)"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Jenis Pembayaran <span class="text-red-500">*</span></label>
                        <select 
                            x-model="paymentForm.jenis_pembayaran" 
                            @change="onPaymentTypeChange()"
                            :disabled="processingPayment"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Pilih Jenis</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    
                    <div>
                        <label class="text-sm text-slate-600">Jumlah Bayar <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input 
                                type="number" 
                                x-model="paymentForm.jumlah_transfer" 
                                :disabled="processingPayment"
                                placeholder="0"
                                step="0.01"
                                min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>
                        
                        
                        <div class="flex gap-2 mt-2">
                            <button 
                                type="button"
                                @click="paymentForm.jumlah_transfer = Math.round((paymentForm.sisa_tagihan || paymentForm.total) * 0.25)"
                                :disabled="processingPayment"
                                class="flex-1 text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                25%
                            </button>
                            <button 
                                type="button"
                                @click="paymentForm.jumlah_transfer = Math.round((paymentForm.sisa_tagihan || paymentForm.total) * 0.5)"
                                :disabled="processingPayment"
                                class="flex-1 text-xs bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                50%
                            </button>
                            <button 
                                type="button"
                                @click="paymentForm.jumlah_transfer = paymentForm.sisa_tagihan || paymentForm.total"
                                :disabled="processingPayment"
                                class="flex-1 text-xs bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                                Lunas
                            </button>
                        </div>
                        
                        <div class="text-xs text-slate-500 mt-2">
                            Sisa tagihan: <span class="font-semibold text-red-600" x-text="formatCurrency(paymentForm.sisa_tagihan || paymentForm.total)"></span>
                        </div>
                    </div>

                    
                    <div x-show="paymentForm.jenis_pembayaran === 'transfer'" x-transition class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="text-sm font-semibold text-blue-800">Informasi Transfer</div>
                        
                        <div>
                            <label class="text-sm text-slate-600">Nama Bank <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="paymentForm.nama_bank" 
                                :disabled="processingPayment"
                                placeholder="Contoh: BCA, Mandiri, BNI, dll."
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>

                        <div>
                            <label class="text-sm text-slate-600">Nama Pengirim <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="paymentForm.nama_pengirim" 
                                :disabled="processingPayment"
                                placeholder="Nama sesuai rekening pengirim"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>
                    </div>

                    
                    <div>
                        <label class="text-sm text-slate-600">
                            Bukti Pembayaran <span class="text-slate-400">(Opsional)</span>
                        </label>
                        <div class="mt-1">
                            <input 
                                type="file" 
                                x-ref="buktiTransferInput"
                                accept=".jpg,.jpeg,.png,.pdf"
                                :disabled="processingPayment"
                                @change="onBuktiTransferChange"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Format: JPG, PNG, PDF (Maks. 2MB) - Gambar akan dikompres otomatis
                        </div>
                        
                        
                        <div x-show="paymentForm.bukti_transfer_preview" class="mt-3">
                            <div class="text-sm font-medium text-slate-700 mb-2">Preview:</div>
                            <div class="border border-slate-200 rounded-lg p-3 bg-white">
                                <img x-show="paymentForm.bukti_transfer_preview_type === 'image'" 
                                    :src="paymentForm.bukti_transfer_preview" 
                                    alt="Preview Bukti Pembayaran"
                                    class="max-h-40 mx-auto rounded">
                                <div x-show="paymentForm.bukti_transfer_preview_type === 'pdf'" 
                                    class="text-center py-4 bg-red-50 rounded">
                                    <i class='bx bx-file text-3xl text-red-500 mb-2'></i>
                                    <div class="text-sm font-medium text-red-700">File PDF</div>
                                    <div class="text-xs text-red-600" x-text="paymentForm.bukti_transfer_name"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Penerima <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            x-model="paymentForm.penerima" 
                            :disabled="processingPayment"
                            placeholder="Nama penerima pembayaran"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                        <input 
                            type="date" 
                            x-model="paymentForm.tanggal_pembayaran"
                            :disabled="processingPayment"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Catatan Pembayaran</label>
                        <textarea 
                            x-model="paymentForm.catatan_pembayaran" 
                            :disabled="processingPayment"
                            rows="2" 
                            placeholder="Catatan tambahan..."
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:opacity-50 disabled:cursor-not-allowed"></textarea>
                    </div>

                    
                    <div x-show="processingPayment" class="flex items-center justify-center py-2">
                        <div class="inline-flex items-center gap-2 text-blue-600">
                            <i class='bx bx-loader-alt bx-spin text-lg'></i>
                            <span class="text-sm font-medium">Memproses pembayaran...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                <button 
                    @click="showPaymentModal = false" 
                    :disabled="processingPayment"
                    class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Batal
                </button>
                <button 
                    @click="confirmPayment()" 
                    :disabled="processingPayment || !isPaymentFormValid()"
                    class="rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <span x-show="processingPayment">
                        <i class='bx bx-loader-alt bx-spin'></i>
                    </span>
                    <span x-text="processingPayment ? 'Memproses...' : 'Konfirmasi Pelunasan'"></span>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showPaymentHistoryModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="showPaymentHistoryModal = false" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Riwayat Pembayaran Cicilan</div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="showPaymentHistoryModal = false">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 max-h-[70vh] overflow-y-auto">
                
                <div class="p-4 bg-slate-50 rounded-xl mb-4">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <div class="text-sm text-slate-600">No. Invoice:</div>
                            <div class="font-mono font-semibold" x-text="paymentHistoryData.invoice?.no_invoice"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Status:</div>
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': paymentHistoryData.invoice?.status === 'menunggu',
                                          'bg-blue-100 text-blue-800': paymentHistoryData.invoice?.status === 'dibayar_sebagian',
                                          'bg-green-100 text-green-800': paymentHistoryData.invoice?.status === 'lunas',
                                          'bg-red-100 text-red-800': paymentHistoryData.invoice?.status === 'gagal'
                                      }"
                                      x-text="paymentHistoryData.invoice?.status || '-'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 pt-3 border-t border-slate-200">
                        <div>
                            <div class="text-sm text-slate-600">Total Invoice:</div>
                            <div class="font-semibold text-blue-600" x-text="formatCurrency(paymentHistoryData.invoice?.total || 0)"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Sudah Dibayar:</div>
                            <div class="font-semibold text-green-600" x-text="formatCurrency(paymentHistoryData.invoice?.total_dibayar || 0)"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Sisa Tagihan:</div>
                            <div class="font-semibold text-red-600" x-text="formatCurrency(paymentHistoryData.invoice?.sisa_tagihan || 0)"></div>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">No</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Jumlah Bayar</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Metode</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Bank/Pengirim</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Penerima</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Keterangan</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Bukti</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Dicatat Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="paymentHistoryData.payment_history && paymentHistoryData.payment_history.length > 0">
                                    <template x-for="(payment, index) in paymentHistoryData.payment_history" :key="payment.id">
                                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                                            <td class="px-4 py-3" x-text="index + 1"></td>
                                            <td class="px-4 py-3 text-xs" x-text="payment.tanggal_bayar"></td>
                                            <td class="px-4 py-3 font-semibold text-green-600 text-xs" x-text="formatCurrency(payment.jumlah_bayar)"></td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs rounded-full font-medium" 
                                                      :class="payment.jenis_pembayaran === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                                                      x-text="payment.jenis_pembayaran === 'cash' ? 'Cash' : 'Transfer'"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div x-show="payment.jenis_pembayaran === 'transfer'">
                                                    <div class="text-xs text-slate-600" x-text="payment.nama_bank || '-'"></div>
                                                    <div class="text-xs text-slate-500" x-text="payment.nama_pengirim || '-'"></div>
                                                </div>
                                                <div x-show="payment.jenis_pembayaran === 'cash'" class="text-xs text-slate-500">-</div>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600" x-text="payment.penerima || '-'"></td>
                                            <td class="px-4 py-3 text-xs text-slate-600">
                                                <div class="max-w-xs truncate" :title="payment.keterangan" x-text="payment.keterangan || '-'"></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <button x-show="payment.bukti_pembayaran" 
                                                        @click="openBuktiModal(payment.bukti_pembayaran)"
                                                        class="text-blue-600 hover:text-blue-800 text-xs underline">
                                                    Lihat
                                                </button>
                                                <span x-show="!payment.bukti_pembayaran" class="text-slate-400 text-xs">-</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600" x-text="payment.created_by"></td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!paymentHistoryData.payment_history || paymentHistoryData.payment_history.length === 0">
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                            <i class='bx bx-info-circle text-3xl mb-2'></i>
                                            <div>Belum ada riwayat pembayaran</div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-2">
                        <i class='bx bx-info-circle text-blue-600 text-lg'></i>
                        <div class="text-xs text-blue-800">
                            <div class="font-semibold mb-1">Informasi:</div>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Setiap pembayaran cicilan tercatat dengan bukti pembayaran</li>
                                <li>Klik "Lihat Bukti" untuk melihat bukti pembayaran setiap cicilan</li>
                                <li>Status invoice akan otomatis berubah menjadi "Lunas" saat sisa tagihan = 0</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                <button 
                    @click="showPaymentHistoryModal = false" 
                    class="rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showBuktiModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="showBuktiModal = false" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Bukti Pembayaran</div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="showBuktiModal = false">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-center bg-slate-100 rounded-lg p-4">
                    <img :src="currentBuktiUrl" alt="Bukti Pembayaran" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-lg">
                </div>
            </div>

            <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                <a :href="currentBuktiUrl" target="_blank" download class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">
                    <i class='bx bx-download'></i> Download
                </a>
                <button 
                    @click="showBuktiModal = false" 
                    class="rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showBuktiTransferModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="showBuktiTransferModal = false" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Bukti Transfer - <span x-text="currentBuktiTransferInvoice?.no_invoice"></span></div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="showBuktiTransferModal = false">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4">
                <div class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="text-sm text-slate-600">Nama Bank</div>
                            <div class="font-semibold" x-text="currentBuktiTransferInvoice?.nama_bank || '-'"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Nama Pengirim</div>
                            <div class="font-semibold" x-text="currentBuktiTransferInvoice?.nama_pengirim || '-'"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Jumlah Transfer</div>
                            <div class="font-semibold" x-text="formatCurrency(currentBuktiTransferInvoice?.jumlah_transfer)"></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Tanggal Transfer</div>
                            <div class="font-semibold" x-text="formatDate(currentBuktiTransferInvoice?.tanggal_pembayaran)"></div>
                        </div>
                    </div>

                    
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div x-show="loadingBuktiTransfer" class="flex items-center justify-center py-12">
                            <div class="text-center">
                                <i class='bx bx-loader-alt bx-spin text-3xl text-primary-600 mb-2'></i>
                                <div class="text-sm text-slate-600">Memuat bukti transfer...</div>
                            </div>
                        </div>
                        
                        <div x-show="!loadingBuktiTransfer && buktiTransferUrl" class="flex items-center justify-center p-4">
                            <img x-show="buktiTransferType === 'image'" 
                                :src="buktiTransferUrl" 
                                alt="Bukti Transfer"
                                class="max-w-full max-h-96 rounded-lg shadow-sm">
                            
                            <div x-show="buktiTransferType === 'pdf'" class="text-center py-8">
                                <i class='bx bx-file text-6xl text-red-500 mb-3'></i>
                                <div class="text-lg font-semibold text-slate-700">File PDF</div>
                                <div class="text-sm text-slate-500 mb-4">Bukti transfer dalam format PDF</div>
                                <button @click="downloadBuktiTransfer()" 
                                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700">
                                    <i class='bx bx-download'></i> Download PDF
                                </button>
                            </div>
                        </div>

                        <div x-show="!loadingBuktiTransfer && !buktiTransferUrl" class="text-center py-12 text-slate-500">
                            <i class='bx bx-error text-4xl mb-3'></i>
                            <div>Gagal memuat bukti transfer</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="downloadBuktiTransfer()" 
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-700 hover:bg-slate-50">
                    <i class='bx bx-download'></i> Download
                </button>
                <button @click="showBuktiTransferModal = false" 
                        class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showPrintModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="closePrintModal()" class="w-full max-w-7xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold truncate">Preview & Print Invoice - <span x-text="currentPrintInvoice?.no_invoice"></span></div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closePrintModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 bg-slate-50">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="text-sm font-medium text-slate-700">Pilih Layout Template:</div>
                    <template x-for="template in printTemplates" :key="template.value">
                        <label class="inline-flex items-center gap-2 cursor-pointer group" x-on:click.stop>
                            <input type="radio" x-model="selectedTemplate" :value="template.value" 
                                  x-on:change="refreshPreview()" class="text-primary-600 focus:ring-primary-500">
                            <span x-text="template.name" 
                                  class="text-sm px-3 py-2 rounded-lg border transition-all font-medium"
                                  :class="selectedTemplate === template.value 
                                        ? 'bg-primary-600 text-white border-primary-600 shadow-sm' 
                                        : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50 hover:border-slate-400'"></span>
                        </label>
                    </template>
                    
                    <!-- Checkbox untuk set default template -->
                    <div class="flex items-center gap-2 ml-4 pl-4 border-l border-slate-300">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm" x-on:click.stop>
                            <input type="checkbox" x-model="setAsDefault" @change="onSetAsDefaultChange()"
                                   class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-slate-600">Jadikan template ini sebagai default</span>
                        </label>
                    </div>
                    
                    <div class="ml-auto flex gap-2">
                        <button @click="downloadPDF()" 
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class='bx bx-download'></i> Download PDF
                        </button>
                        <button @click="printInvoiceDirect()" 
                                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 text-sm font-medium hover:bg-primary-700 transition-colors">
                            <i class='bx bx-printer'></i> Print Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-hidden bg-slate-100 p-4">
                <div class="bg-white rounded-lg shadow-inner h-full overflow-auto flex items-start justify-center">
                    <div x-show="loadingPreview" class="flex items-center justify-center h-full w-full">
                        <div class="text-center">
                            <i class='bx bx-loader-alt bx-spin text-3xl text-primary-600 mb-2'></i>
                            <div class="text-sm text-slate-600">Memuat preview...</div>
                        </div>
                    </div>
                    <iframe x-show="!loadingPreview" x-ref="previewFrame" :src="previewUrl" 
                            class="w-full h-full border-0 min-h-[800px]" 
                            @load="onPreviewLoad()"></iframe>
                </div>
            </div>

            <div class="px-4 sm:px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between text-sm text-slate-600">
                <div class="flex items-center gap-4">
                    <div>
                        Template: <span x-text="getTemplateName(selectedTemplate)" class="font-medium text-primary-600"></span>
                    </div>
                    <div class="text-slate-400">•</div>
                    <div>
                        Invoice: <span x-text="currentPrintInvoice?.no_invoice" class="font-mono font-medium"></span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div x-show="loadingPreview" class="inline-flex items-center gap-2 text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i>
                        <span>Loading preview...</span>
                    </div>
                    <button @click="refreshPreview()" class="inline-flex items-center gap-1 text-slate-600 hover:text-primary-600 transition-colors">
                        <i class='bx bx-refresh'></i>
                        <span class="text-sm">Refresh</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div x-show="showCoaModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="closeCoaModal()" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Setting Chart of Accounts (COA)</div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeCoaModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
                <div class="space-y-6">
                    <!-- Accounting Book -->
                    <div>
                        <label class="text-sm font-medium text-slate-700">Buku Akuntansi <span class="text-red-500">*</span></label>
                        <select x-model="coaForm.accounting_book_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200 focus:border-primary-500">
                            <option value="">Pilih Buku Akuntansi</option>
                            <template x-for="book in coaData.accounting_books" :key="book.id">
                                <option :value="book.id" x-text="book.name + ' (' + book.code + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- COA Settings Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Piutang Usaha -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun Piutang Usaha <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_piutang_usaha_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_piutang_usaha', 'asset')"
                                    placeholder="Cari akun piutang..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_piutang_usaha_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_piutang_usaha_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_piutang_usaha', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_piutang_usaha_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_piutang_usaha_display"></div>
                            </div>
                        </div>

                        <!-- Pendapatan Penjualan -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun Pendapatan Penjualan <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_pendapatan_penjualan_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_pendapatan_penjualan', 'revenue')"
                                    placeholder="Cari akun pendapatan..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_pendapatan_penjualan_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_pendapatan_penjualan_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_pendapatan_penjualan', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_pendapatan_penjualan_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_pendapatan_penjualan_display"></div>
                            </div>
                        </div>

                        <!-- Kas -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun Kas <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_kas_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_kas', 'asset')"
                                    placeholder="Cari akun kas..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_kas_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_kas_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_kas', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_kas_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_kas_display"></div>
                            </div>
                        </div>

                        <!-- Bank -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun Bank <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_bank_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_bank', 'asset')"
                                    placeholder="Cari akun bank..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_bank_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_bank_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_bank', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_bank_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_bank_display"></div>
                            </div>
                        </div>

                        <!-- HPP -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun HPP <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_hpp_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_hpp', 'expense')"
                                    placeholder="Cari akun HPP..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_hpp_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_hpp_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_hpp', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_hpp_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_hpp_display"></div>
                            </div>
                        </div>

                        <!-- Persediaan -->
                        <div>
                            <label class="text-sm font-medium text-slate-700">Akun Persediaan <span class="text-red-500">*</span></label>
                            <div class="mt-1 relative">
                                <input type="text" x-model="coaForm.akun_persediaan_search" 
                                    @input.debounce.500ms="searchCoaAccounts('akun_persediaan', 'asset')"
                                    placeholder="Cari akun persediaan..." 
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 focus:ring-2 focus:ring-primary-200">
                                <div x-show="coaForm.akun_persediaan_results.length > 0" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="account in coaForm.akun_persediaan_results" :key="account.id">
                                        <button @click="selectCoaAccount('akun_persediaan', account)" 
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                            <div class="font-medium" x-text="account.code + ' - ' + account.name"></div>
                                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="coaForm.akun_persediaan_display" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800" x-text="coaForm.akun_persediaan_display"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Jurnal Otomatis dengan Tabs -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-800">Preview Jurnal Otomatis</h3>
                            <button @click="previewCoaJournal()" type="button" 
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">
                                <i class='bx bx-refresh'></i> Refresh Preview
                            </button>
                        </div>
                        
                        <!-- Tabs untuk Status -->
                        <div class="mb-4">
                            <div class="flex space-x-1 rounded-xl bg-slate-100 p-1">
                                <button 
                                    @click="activePreviewTab = 'menunggu'; previewCoaJournalByTab('menunggu')"
                                    :class="activePreviewTab === 'menunggu' 
                                        ? 'bg-white text-slate-700 shadow-sm' 
                                        : 'text-slate-500 hover:text-slate-700'"
                                    class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-all">
                                    Menunggu
                                </button>
                                <button 
                                    @click="activePreviewTab = 'lunas'; previewCoaJournalByTab('lunas')"
                                    :class="activePreviewTab === 'lunas' 
                                        ? 'bg-white text-slate-700 shadow-sm' 
                                        : 'text-slate-500 hover:text-slate-700'"
                                    class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-all">
                                    Lunas
                                </button>
                                <button 
                                    @click="activePreviewTab = 'gagal'; previewCoaJournalByTab('gagal')"
                                    :class="activePreviewTab === 'gagal' 
                                        ? 'bg-white text-slate-700 shadow-sm' 
                                        : 'text-slate-500 hover:text-slate-700'"
                                    class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-all">
                                    Retur/Gagal
                                </button>
                            </div>
                        </div>
                        
                        <!-- Loading State -->
                        <div x-show="coaPreview.loading" class="text-center py-8">
                            <i class='bx bx-loader-alt bx-spin text-xl text-blue-600'></i>
                            <div class="text-sm text-slate-600 mt-2">Memuat preview jurnal...</div>
                        </div>
                        
                        <!-- Content untuk setiap Tab -->
                        <template x-for="tab in ['menunggu', 'lunas', 'gagal']" :key="tab">
                            <div x-show="!coaPreview.loading && activePreviewTab === tab">
                                <!-- Informasi Status -->
                                <div class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-medium text-slate-700" x-text="coaPreview[tab]?.description || 'Preview untuk status ' + tab"></div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                Total: <span class="font-semibold" x-text="formatCurrency(coaPreview[tab]?.total || 0)"></span>
                                                <span x-show="coaPreview[tab]?.hpp_amount > 0" class="ml-3">
                                                    HPP: <span class="font-semibold" x-text="formatCurrency(coaPreview[tab]?.hpp_amount || 0)"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span :class="coaPreview[tab]?.is_balanced ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                class="px-2 py-1 rounded-full text-xs font-medium" 
                                                x-text="coaPreview[tab]?.is_balanced ? '✓ Balance' : '✗ Tidak Balance'">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tabel Entries -->
                                <div x-show="coaPreview[tab]?.entries && coaPreview[tab].entries.length > 0" 
                                    class="bg-white rounded-lg border border-slate-200 overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-slate-50">
                                                <th class="px-4 py-3 text-left">Akun</th>
                                                <th class="px-4 py-3 text-left">Tipe</th>
                                                <th class="px-4 py-3 text-right">Debit</th>
                                                <th class="px-4 py-3 text-right">Kredit</th>
                                                <th class="px-4 py-3 text-center">Posisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="entry in coaPreview[tab]?.entries" :key="entry.account_code">
                                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                                    <td class="px-4 py-3">
                                                        <div class="font-medium" x-text="entry.account_code"></div>
                                                        <div class="text-xs text-slate-600" x-text="entry.account_name"></div>
                                                    </td>
                                                    <td class="px-4 py-3 text-slate-600" x-text="entry.account_type"></td>
                                                    <td class="px-4 py-3 text-right font-semibold" x-text="formatCurrency(entry.debit)"></td>
                                                    <td class="px-4 py-3 text-right font-semibold" x-text="formatCurrency(entry.credit)"></td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span :class="entry.position === 'Debit' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'" 
                                                            class="px-2 py-1 rounded-full text-xs font-medium" 
                                                            x-text="entry.position"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot class="bg-slate-50">
                                            <tr>
                                                <td colspan="2" class="px-4 py-3 text-right font-semibold">Total:</td>
                                                <td class="px-4 py-3 text-right font-semibold" x-text="formatCurrency(calculateTotalDebit(coaPreview[tab]?.entries || []))"></td>
                                                <td class="px-4 py-3 text-right font-semibold" x-text="formatCurrency(calculateTotalCredit(coaPreview[tab]?.entries || []))"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <span :class="coaPreview[tab]?.is_balanced ? 'text-green-600' : 'text-red-600'" 
                                                        class="text-xs font-medium">
                                                        <span x-text="calculateTotalDebit(coaPreview[tab]?.entries || []) - calculateTotalCredit(coaPreview[tab]?.entries || [])"></span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <!-- Empty State -->
                                <div x-show="!coaPreview[tab]?.entries || coaPreview[tab].entries.length === 0" 
                                    class="text-center py-8 text-slate-500 bg-slate-50 rounded-lg border border-slate-200">
                                    <i class='bx bx-info-circle text-3xl mb-2 text-slate-300'></i>
                                    <div class="text-sm">Tidak ada data jurnal untuk preview</div>
                                    <div class="text-xs text-slate-400 mt-1">Pastikan setting COA sudah lengkap</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="closeCoaModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Batal</button>
                <button @click="saveCoaSetting()" :disabled="savingCoa" 
                        class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
                    <span x-show="savingCoa" class="inline-flex items-center gap-2">
                        <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                    </span>
                    <span x-show="!savingCoa">Simpan Setting COA</span>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showOngkirModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="closeOngkirModal()" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Setting Ongkos Kirim</div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeOngkirModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="flex-1 flex flex-col">
                <!-- Toolbar -->
                <div class="px-4 sm:px-5 py-4 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                        <div class="flex-1">
                            <div class="relative max-w-xs">
                                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                                <input type="text" x-model="ongkirSearch" @input.debounce.500ms="loadOngkirData()" 
                                      placeholder="Cari daerah ongkos kirim..." 
                                      class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary-200">
                            </div>
                        </div>
                        <button @click="openAddOngkir()" 
                                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                            <i class='bx bx-plus'></i> Tambah Ongkos Kirim
                        </button>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left">Daerah</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="ongkir in ongkirData" :key="ongkir.id_ongkir">
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium" x-text="ongkir.daerah"></div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold" x-text="formatCurrency(ongkir.harga)"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-1">
                                            <button @click="editOngkir(ongkir)" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                <i class='bx bx-edit text-xs'></i>
                                            </button>
                                            <button @click="deleteOngkir(ongkir.id_ongkir)" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                                <i class='bx bx-trash text-xs'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="ongkirData.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                                    <i class='bx bx-package text-3xl mb-2 text-slate-300'></i>
                                    <div>Tidak ada data ongkos kirim</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit Ongkir -->
    <div x-show="showOngkirFormModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-20">
        <div @click.outside="closeOngkirFormModal()" class="w-full max-w-lg bg-white rounded-2xl shadow-float">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold" x-text="editingOngkir ? 'Edit Ongkos Kirim' : 'Tambah Ongkos Kirim'"></div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeOngkirFormModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Daerah <span class="text-red-500">*</span></label>
                        <input type="text" x-model="ongkirForm.daerah" placeholder="Nama daerah / kota"
                              class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Harga <span class="text-red-500">*</span></label>
                        <input type="number" x-model="ongkirForm.harga" placeholder="0"
                              class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <p class="text-xs text-blue-600 mt-1" x-show="ongkirForm.harga > 0" 
                           x-text="'≈ ' + formatCurrency(ongkirForm.harga)"></p>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="closeOngkirFormModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Batal</button>
                <button @click="saveOngkir()" :disabled="savingOngkir" 
                        class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
                    <span x-show="savingOngkir" class="inline-flex items-center gap-2">
                        <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                    </span>
                    <span x-show="!savingOngkir" x-text="editingOngkir ? 'Update' : 'Simpan'"></span>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showCustomerPriceModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="closeCustomerPriceModal()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Setting Harga Khusus Customer</div>
                <div class="text-xs text-slate-500">
                    Outlet: <span class="font-medium" x-text="getOutletName(selectedOutlet)"></span>
                </div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeCustomerPriceModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="flex-1 flex flex-col">
                <!-- Toolbar -->
                <div class="px-4 sm:px-5 py-4 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
                                <input type="text" x-model="customerPriceSearch" @input.debounce.500ms="loadCustomerPriceData()" 
                                      placeholder="Cari customer..." 
                                      class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary-200">
                            </div>
                        </div>
                        <button @click="openAddCustomerPrice()" 
                                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                            <i class='bx bx-plus'></i> Tambah Harga Khusus
                        </button>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left">Customer</th>
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-4 py-3 text-left">Ongkos Kirim</th>
                                <th class="px-4 py-3 text-left">Produk</th>
                                <th class="px-4 py-3 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(customerPrice, index) in customerPriceData" :key="'customer-price-' + customerPrice.id_customer_price + '-' + index">
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium" x-text="customerPrice.customer_name"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span x-text="customerPrice.customer_type === 'member' ? 'Member' : 'Prospek'" 
                                              class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div x-html="customerPrice.ongkos_kirim"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs" x-html="customerPrice.produk_list"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-1">
                                            <button @click="editCustomerPrice(customerPrice)" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                <i class='bx bx-edit text-xs'></i>
                                            </button>
                                            <button @click="deleteCustomerPrice(customerPrice.id_customer_price)" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                                <i class='bx bx-trash text-xs'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="customerPriceData.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    <i class='bx bx-dollar-circle text-3xl mb-2 text-slate-300'></i>
                                    <div>Tidak ada data harga khusus customer</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit Customer Price -->
    <div x-show="showCustomerPriceFormModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
        <div @click.outside="closeCustomerPriceFormModal()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold" x-text="editingCustomerPrice ? 'Edit Harga Khusus' : 'Tambah Harga Khusus'"></div>
                <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeCustomerPriceFormModal()">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
                <div class="space-y-4">
                    <!-- Pilih Customer -->
                    <div>
                        <label class="text-sm font-medium text-slate-700">Customer <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <input type="text" x-model="customerPriceForm.customer_search" 
                                  @input.debounce.500ms="searchCustomerPriceCustomers()"
                                  placeholder="Cari customer..." 
                                  class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                            <div x-show="customerPriceForm.customer_results.length > 0" 
                                class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                <template x-for="customer in customerPriceForm.customer_results" :key="customer.id">
                                    <button @click="selectCustomerPriceCustomer(customer)" 
                                            class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                        <div class="font-medium" x-text="customer.nama"></div>
                                        <div class="text-xs text-slate-500" x-text="customer.type === 'member' ? 'Member' : 'Prospek'"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="customerPriceForm.customer_id" class="mt-2 p-2 bg-green-50 rounded-lg border border-green-200">
                            <div class="text-sm font-medium text-green-800" x-text="customerPriceForm.customer_name"></div>
                            <div class="text-xs text-green-600" x-text="customerPriceForm.customer_type === 'member' ? 'Member' : 'Prospek'"></div>
                        </div>
                    </div>

                    <!-- Pilih Ongkos Kirim -->
                    <div>
                        <label class="text-sm font-medium text-slate-700">Ongkos Kirim <span class="text-red-500">*</span></label>
                        <select x-model="customerPriceForm.id_ongkir" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                            <option value="">Pilih Ongkos Kirim</option>
                            <template x-for="ongkir in availableOngkir" :key="ongkir.id_ongkir">
                                <option :value="ongkir.id_ongkir" x-text="ongkir.daerah + ' - ' + formatCurrency(ongkir.harga)"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Daftar Produk -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-slate-700">Produk dengan Harga Khusus</label>
                            <button @click="addCustomerPriceProduct()" type="button" 
                                    class="inline-flex items-center gap-1 rounded-xl bg-primary-600 text-white px-3 py-1.5 text-sm hover:bg-primary-700">
                                <i class='bx bx-plus'></i> Tambah Produk
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <template x-for="(product, index) in customerPriceForm.produk" :key="index">
                                <div class="p-3 border border-slate-200 rounded-lg bg-slate-50">
                                    <div class="grid grid-cols-1 gap-3">
                                        <div>
                                            <label class="text-xs text-slate-600">Produk</label>
                                            <div class="relative">
                                                <input type="text" 
                                                    x-model="product.product_search"
                                                    @input.debounce.500ms="searchCustomerPriceProducts(product, index)"
                                                    placeholder="Cari produk..." 
                                                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                                <div x-show="product.product_results && product.product_results.length > 0" 
                                                    class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                    <template x-for="p in product.product_results" :key="p.id_produk">
                                                        <button type="button"
                                                            @click="selectCustomerPriceProduct(p, product, index)"
                                                            class="w-full text-left px-3 py-2 hover:bg-slate-50 border-b border-slate-200 text-sm">
                                                            <div class="font-medium" x-text="p.nama_produk"></div>
                                                            <div class="text-xs text-slate-500">
                                                                Harga: <span x-text="formatCurrency(p.harga)"></span> | 
                                                                Stok: <span x-text="p.stok"></span>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                            <div x-show="product.id_produk" class="text-xs text-slate-500 mt-1">
                                                Harga normal: <span x-text="formatCurrency(product.harga_normal)"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-600">Harga Khusus</label>
                                            <div class="flex gap-2">
                                                <div class="flex-1">
                                                    <input type="number" 
                                                        x-model="product.harga_khusus" 
                                                        placeholder="0" 
                                                        min="0"
                                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                                    <p class="text-xs text-blue-600 mt-1" x-show="product.harga_khusus > 0" 
                                                       x-text="'≈ ' + formatCurrency(product.harga_khusus)"></p>
                                                </div>
                                                <button type="button"
                                                    @click="removeCustomerPriceProduct(index)" 
                                                    class="px-2 text-red-600 hover:text-red-800"
                                                    :disabled="savingCustomerPrice">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="customerPriceForm.produk.length === 0" class="text-center py-4 text-slate-500">
                                <i class='bx bx-package text-2xl mb-2'></i>
                                <div class="text-sm">Belum ada produk ditambahkan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="closeCustomerPriceFormModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50">Batal</button>
                <button @click="saveCustomerPrice()" :disabled="savingCustomerPrice" 
                        class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
                    <span x-show="savingCustomerPrice" class="inline-flex items-center gap-2">
                        <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                    </span>
                    <span x-show="!savingCustomerPrice" x-text="editingCustomerPrice ? 'Update' : 'Simpan'"></span>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showStockAlert" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
        <div @click.outside="showStockAlert = false" class="w-full max-w-md bg-white rounded-2xl shadow-float overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <i class='bx bx-error text-xl text-red-600'></i>
                    </div>
                    <div>
                        <div class="font-semibold text-red-700">Stok Tidak Cukup</div>
                    </div>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="showStockAlert = false" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">
                    Skip / Tutup
                </button>
                <button @click="goToPurchaseOrder()" class="rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 flex items-center gap-2">
                    <i class='bx bx-cart-add'></i>
                    Buat Purchase Order
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="showDeleteConfirm" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
      <div @click.outside="showDeleteConfirm = false" class="w-full max-w-md bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Invoice?</div>
          <p class="text-slate-600 mt-1" x-text="'Invoice ' + (invoiceToDelete?.no_invoice || '') + ' akan dihapus secara permanen.'"></p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button @click="showDeleteConfirm = false" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button @click="confirmDelete()" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700">Hapus</button>
        </div>
      </div>
    </div>

    
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
    function invoicePenjualan() {
      return {
        view: 'grid',
        selectedOutlet: <?php echo json_encode($selectedOutlet, 15, 512) ?>, // Default outlet dari controller - keep for compatibility
        selectedOutlets: [], // New checkbox system
        showOutletDropdown: false,
        outlets: <?php echo json_encode($outlets, 15, 512) ?>,

        availableOngkir: [],
        loading: false,
        savingInvoice: false,
        invoices: [],
        availableProducts: [],
        customerSearchResults: [],
        selectedCustomer: null,

        // COA Setting
        showCoaModal: false,
        coaData: {
            setting: null,
            accounting_books: [],
            accounts: [], // Pastikan ini diinisialisasi sebagai array kosong
            account_types: []
        },
        coaForm: {
            accounting_book_id: '',
            akun_piutang_usaha: '',
            akun_piutang_usaha_search: '',
            akun_piutang_usaha_results: [],
            akun_piutang_usaha_display: '',
            akun_pendapatan_penjualan: '',
            akun_pendapatan_penjualan_search: '',
            akun_pendapatan_penjualan_results: [],
            akun_pendapatan_penjualan_display: '',
            akun_kas: '',
            akun_kas_search: '',
            akun_kas_results: [],
            akun_kas_display: '',
            akun_bank: '',
            akun_bank_search: '',
            akun_bank_results: [],
            akun_bank_display: '',
            akun_hpp: '',
            akun_hpp_search: '',
            akun_hpp_results: [],
            akun_hpp_display: '',
            akun_persediaan: '',
            akun_persediaan_search: '',
            akun_persediaan_results: [],
            akun_persediaan_display: ''
        },
        activePreviewTab: 'menunggu',
        coaPreview: {
            loading: false,
            menunggu: { entries: [], description: '', total: 0, hpp_amount: 0, is_balanced: false },
            lunas: { entries: [], description: '', total: 0, hpp_amount: 0, is_balanced: false },
            gagal: { entries: [], description: '', total: 0, hpp_amount: 0, is_balanced: false }
        },
        savingCoa: false,
        
        activeTab: 'all',
        exportMenuOpen: false,
        showInvoiceModal: false,
        showInvoiceSettingModal: false,
        showDeleteConfirm: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        
        stats: {
          total: 0,
          menunggu: 0,
          lunas: 0,
          gagal: 0
        },
        
        filters: {
          start_date: '',
          end_date: '',
          outlet: 'all',
          search: '',
          status: 'all'
        },
        
        pagination: {
          current_page: 1,
          last_page: 1,
          total: 0,
          per_page: 100
        },
        
        invoiceForm: {
          id_sales_invoice: null,
          no_invoice: '',
          tanggal: new Date().toISOString().split('T')[0],
          due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // Default 30 days from today
          customer_type: '',
          customer_id: '',
          id_outlet: '',
          keterangan: '',
          items: []
        },
        editingInvoice: null,
        invoiceToDelete: null,
        
        invoiceSetting: {
          current_invoice_number: '',
          next_invoice_number: '',
          current_number: 0,
          current_year: 0
        },
        invoiceSettingForm: {
          starting_number: 1,
          year: new Date().getFullYear()
        },
        
        customerSearch: '',
        customerTypePrices: {}, // Menyimpan harga berdasarkan tipe customer
        processingPayment: false,
        showStockAlert: false,
        selectedOutOfStockProduct: null,

        showBuktiTransferModal: false,
        currentBuktiTransferInvoice: null,
        buktiTransferUrl: '',
        buktiTransferType: 'image',
        loadingBuktiTransfer: false,

        async loadOngkir() {
        try {
            const params = new URLSearchParams({
            outlet_id: this.selectedOutlet
            });

            const response = await fetch(`<?php echo e(route("admin.penjualan.ongkir.data")); ?>?${params}`);
            const data = await response.json();
            if (data.data && Array.isArray(data.data)) {
            this.availableOngkir = data.data.filter(ongkir => ongkir && ongkir.id_ongkir && ongkir.daerah);
            } else {
            this.availableOngkir = [];
            }
        } catch (error) {
            console.error('Error loading ongkir:', error);
            this.availableOngkir = [];
        }
        },

        onOngkirSelect(item, index) {
            if (item.id_ongkir === 'new') {
                item.deskripsi = 'Ongkos Kirim Baru';
                item.harga = 0;
                item.satuan = 'Trip';
                item.ongkir_daerah = '';
                item.ongkir_harga = 0;
            } else {
                const ongkir = this.availableOngkir.find(o => o.id_ongkir == item.id_ongkir);
                if (ongkir) {
                    item.deskripsi = 'Ongkos Kirim - ' + ongkir.daerah;
                    item.harga = ongkir.harga;
                    item.satuan = 'Trip';
                    this.calculateItemSubtotal(item, index);
                }
            }
        },


        async init() {
          // Load default template dari localStorage
          this.loadDefaultTemplate();
          
          // Setup watchers
          this.$watch('selectedTemplate', (newTemplate) => {
              if (this.currentPrintInvoice && this.showPrintModal) {
                  this.loadingPreview = true;
                  this.previewUrl = this.generatePreviewUrl(this.currentPrintInvoice.id_sales_invoice, newTemplate);
              }
              // Update checkbox berdasarkan apakah template yang dipilih sama dengan default
              this.setAsDefault = (newTemplate === this.defaultTemplate);
          });
          
          // Parallel loading untuk performa maksimal
          try {
            await Promise.all([
              this.loadStats(),
              this.loadOutlets(),
              this.loadInvoices(),
              this.loadProducts(),
              this.loadOngkir(),
              this.loadAvailableProducts()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }

          // Check URL parameters untuk auto-open payment modal dari halaman piutang
          const urlParams = new URLSearchParams(window.location.search);
          const invoiceId = urlParams.get('invoice_id');
          const openPayment = urlParams.get('open_payment');
          
          if (invoiceId && openPayment === '1') {
            // Wait for invoice data to load, then open payment modal
            setTimeout(async () => {
              await this.openPaymentModal(parseInt(invoiceId));
              // Clean URL
              window.history.replaceState({}, document.title, window.location.pathname);
            }, 1500);
          }
        },

        async loadStats() {
            try {
                const params = new URLSearchParams({
                outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.invoice.counts")); ?>?${params}`);
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async loadOutlets() {
          try {
            const response = await fetch('<?php echo e(route("admin.penjualan.outlets")); ?>');
            const data = await response.json();
            if (data.success) {
              this.outlets = data.outlets;
              
              // Initialize selectedOutlets with all outlets for checkbox system
              this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
              
              // Maintain backward compatibility with selectedOutlet
              if (!this.selectedOutlet && this.outlets.length > 0) {
                this.selectedOutlet = this.outlets[0].id_outlet;
              }
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },

        async loadInvoices() {
          this.loading = true;
          try {
            const params = new URLSearchParams({
              status: this.activeTab,
              start_date: this.filters.start_date,
              end_date: this.filters.end_date,
              search: this.filters.search,
              page: this.pagination.current_page
            });

            // Add selected outlets as array for new checkbox system
            if (this.selectedOutlets && this.selectedOutlets.length > 0) {
              this.selectedOutlets.forEach(outletId => {
                params.append('outlet_ids[]', outletId);
              });
            }
            
            // Maintain backward compatibility
            if (this.selectedOutlet) {
              params.append('outlet_id', this.selectedOutlet);
              params.append('outlet_filter', this.selectedOutlet);
            }

            const response = await fetch(`<?php echo e(route('admin.penjualan.invoice.data')); ?>?${params}`);
            const data = await response.json();
            
            this.invoices = data.data;
            this.pagination = {
              current_page: data.current_page,
              last_page: data.last_page,
              total: data.total,
              per_page: data.per_page
            };
          } catch (error) {
            console.error('Error loading invoices:', error);
            this.showToastMessage('Gagal memuat data invoice', 'error');
          } finally {
            this.loading = false;
          }
        },

        async loadProducts() {
            try {
                const params = new URLSearchParams({
                outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.produk.harga-normal")); ?>?${params}`);
                const data = await response.json();
                if (data.success) {
                this.availableProducts = data.produks;
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        },

        setActiveTab(tab) {
          this.activeTab = tab;
          this.pagination.current_page = 1;
          this.loadInvoices();
        },

        applyFilters() {
          this.pagination.current_page = 1;
          this.loadInvoices();
        },

        nextPage() {
          if (this.pagination.current_page < this.pagination.last_page) {
            this.pagination.current_page++;
            this.loadInvoices();
          }
        },

        prevPage() {
          if (this.pagination.current_page > 1) {
            this.pagination.current_page--;
            this.loadInvoices();
          }
        },

        goToPage(page) {
          if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
            this.pagination.current_page = page;
            this.loadInvoices();
          }
        },

        getPaginationPages() {
          const pages = [];
          const current = this.pagination.current_page;
          const last = this.pagination.last_page;
          
          // Always show first page
          if (current > 3) {
            pages.push(1);
            if (current > 4) {
              pages.push('...');
            }
          }
          
          // Show pages around current page
          for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
            pages.push(i);
          }
          
          // Always show last page
          if (current < last - 2) {
            if (current < last - 3) {
              pages.push('...');
            }
            pages.push(last);
          }
          
          return pages.filter((page, index, arr) => arr.indexOf(page) === index);
        },

        // Helper method untuk mendapatkan nama outlet
        getOutletName(outletId) {
        const outlet = this.outlets.find(o => o.id_outlet == outletId);
        return outlet ? outlet.nama_outlet : 'Sedang Memuat..';
        },

        // Method untuk buat invoice - set outlet yang dipilih
        async openCreateInvoice() {
        // No longer generate invoice number here - will be generated after save
        this.editingInvoice = null;
        this.invoiceForm.id_sales_invoice = null;
        this.invoiceForm.no_invoice = 'DRAFT-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9); // Unique draft number
        this.invoiceForm.status = 'draft'; // Set status as draft
        this.invoiceForm.tanggal = new Date().toISOString().split('T')[0];
        this.invoiceForm.due_date = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]; // Default 30 days from today
        this.invoiceForm.customer_type = '';
        this.invoiceForm.customer_id = '';
        this.invoiceForm.id_outlet = this.selectedOutlet; // Set outlet yang dipilih
        this.invoiceForm.keterangan = '';
        this.invoiceForm.items = []; // Start with empty items array - user needs to add items manually
        this.selectedCustomer = null;
        this.customerSearch = '';
        
        // Add one default item automatically
        this.addInvoiceItem();
        
        this.showInvoiceModal = true;
        },

        async onOutletChange() {
            console.log('Outlet changed to:', this.selectedOutlet);
            
            // Reset semua data
            this.invoices = [];
            this.availableProducts = [];
            this.availableOngkir = [];
            this.customerPriceData = [];
            
            // Load ulang semua data
            await this.loadStats();
            await this.loadInvoices();
            await this.loadProducts();
            await this.loadOngkir();
            await this.loadAvailableProducts();
            
            // Reset form jika sedang buat invoice
            if (this.showInvoiceModal) {
                this.closeInvoiceModal();
            }
            
            // Reset modal lainnya jika terbuka
            if (this.showOngkirModal) {
                await this.loadOngkirData();
            }
            
            if (this.showCustomerPriceModal) {
                await this.loadCustomerPriceData();
            }
        },

        // Checkbox outlet management functions
        getSelectedOutletsText() {
          if (!this.selectedOutlets || this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet == this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Unknown';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Dipilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outlets.map(o => o.id_outlet);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        async onOutletSelectionChange() {
          console.log('Outlet selection changed:', this.selectedOutlets);
          
          // Update selectedOutlet for backward compatibility (use first selected)
          this.selectedOutlet = this.selectedOutlets.length > 0 ? this.selectedOutlets[0] : null;
          
          // Close dropdown
          // Dropdown stays open for better UX
          
          // Reset and reload data
          this.invoices = [];
          this.availableProducts = [];
          this.availableOngkir = [];
          this.customerPriceData = [];
          
          // Load data with new outlet selection
          await this.loadStats();
          await this.loadInvoices();
          await this.loadProducts();
          await this.loadOngkir();
          await this.loadAvailableProducts();
          
          // Reset forms if open
          if (this.showInvoiceModal) {
            this.closeInvoiceModal();
          }
        },

        async searchCustomers() {
            if (!this.customerSearch) {
                this.customerSearchResults = [];
                return;
            }

            try {
                // TAMBAHKAN outlet_id parameter
                const params = new URLSearchParams({
                    search: this.customerSearch,
                    outlet_id: this.selectedOutlet // INI YANG DITAMBAHKAN
                });

                const response = await fetch(`<?php echo e(route('admin.penjualan.customers')); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.customerSearchResults = data.customers;
                } else {
                    this.customerSearchResults = [];
                    console.error('Search customers failed:', data.message);
                }
            } catch (error) {
                console.error('Error searching customers:', error);
                this.customerSearchResults = [];
                this.showToastMessage('Gagal mencari customer', 'error');
            }
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.invoiceForm.customer_type = customer.type;
            this.invoiceForm.customer_id = customer.id;
            this.customerSearch = customer.nama;
            this.customerSearchResults = [];
            
            // Load customer type prices untuk semua produk yang sudah dipilih
            this.loadCustomerTypePrices(customer);
        },

        // Method untuk load customer type prices
        async loadCustomerTypePrices(customer) {
            // Reset customer type prices
            this.customerTypePrices = {};
            
            // Jika customer tidak memiliki tipe, skip
            if (!customer.id_tipe) {
                this.showToastMessage(`Customer ${customer.nama} menggunakan harga normal`, 'info');
                this.applyCustomerTypePricesToItems();
                return;
            }
            
            try {
                const url = `<?php echo e(route("admin.penjualan.pos.customer-type-prices")); ?>?id_tipe=${customer.id_tipe}&outlet_id=${this.selectedOutlet}`;
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    this.customerTypePrices = result.data;
                    this.showToastMessage(`Harga tipe customer "${customer.tipe?.nama_tipe || 'Unknown'}" berhasil dimuat`, 'success');
                    this.applyCustomerTypePricesToItems();
                } else {
                    console.error('Failed to load customer type prices:', result.message);
                    this.showToastMessage('Gagal memuat harga tipe customer', 'error');
                }
            } catch (error) {
                console.error('Error loading customer type prices:', error);
                this.showToastMessage('Error loading customer type prices', 'error');
            }
        },

        // Method untuk apply customer type prices ke semua item
        applyCustomerTypePricesToItems() {
            const itemsWithProducts = this.invoiceForm.items.filter(item => 
                item.tipe === 'produk' && item.id_produk
            );
            
            for (const item of itemsWithProducts) {
                this.applyCustomerTypePriceToItem(item);
            }
            
            this.calculateTotal();
        },

        addInvoiceItem() {
            this.invoiceForm.items.push({
                tipe: 'produk',
                id_produk: '',
                id_ongkir: '',
                deskripsi: '',
                kuantitas: 1,
                satuan: 'Unit',
                harga: 0, // Harga normal (tetap)
                harga_khusus: 0, // Harga khusus customer
                diskon: 0, // Diskon = harga - harga_khusus
                subtotal: 0, // = harga * kuantitas (tanpa diskon)
                product_search: '',
                product_results: [],
                selectedProduct: null,
                ongkir_search: '',
                ongkir_results: [],
                selectedOngkir: null,
                show_new_ongkir: false,
                new_ongkir_daerah: '',
                new_ongkir_harga: 0
            });
        },

        async searchProducts(item, index) {
            if (!item.product_search) {
                item.product_results = [];
                return;
            }

            try {
                const params = new URLSearchParams({
                    search: item.product_search,
                    outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.produk.harga-normal")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    item.product_results = data.produks;
                }
            } catch (error) {
                console.error('Error searching products:', error);
                item.product_results = [];
            }
        },

        async selectProduct(product, item, index) {
            // Validasi stok
            const stokTersedia = product.stok || 0;
            const kuantitasDibutuhkan = parseFloat(item.kuantitas) || 1;
            
            if (stokTersedia < kuantitasDibutuhkan) {
                this.selectedOutOfStockProduct = {
                    ...product,
                    kuantitas_dibutuhkan: kuantitasDibutuhkan
                };
                this.showStockAlert = true;
                return;
            }
            
            item.selectedProduct = product;
            item.id_produk = product.id_produk;
            item.deskripsi = product.nama_produk;
            item.harga = product.harga; // Harga normal
            item.satuan = product.satuan || 'Unit';
            item.product_search = product.nama_produk;
            item.product_results = [];
            item.diskon = 0; // Reset diskon
            item.harga_khusus = 0; // Reset harga khusus
            
            // Apply customer type price jika ada
            this.applyCustomerTypePriceToItem(item);
            this.calculateItemSubtotal(item, index);
        },

        // Method untuk apply customer type price ke item
        applyCustomerTypePriceToItem(item) {
            const typePrice = this.customerTypePrices[item.id_produk];
            
            if (typePrice) {
                // Ada harga khusus/diskon untuk produk ini
                const finalPrice = parseFloat(typePrice.harga_final) || item.harga;
                
                if (typePrice.harga_khusus && typePrice.harga_khusus > 0) {
                    // Harga khusus - gunakan harga khusus sebagai harga final
                    item.harga_khusus = parseFloat(typePrice.harga_khusus);
                    // Diskon = selisih harga normal dengan harga khusus (bisa 0 jika harga khusus lebih tinggi)
                    item.diskon = Math.max(0, item.harga - item.harga_khusus);
                } else if (typePrice.diskon && typePrice.diskon > 0) {
                    // Diskon persentase - gunakan harga final dari perhitungan diskon
                    item.harga_khusus = finalPrice;
                    // Diskon = selisih harga normal dengan harga final
                    item.diskon = Math.max(0, item.harga - finalPrice);
                } else {
                    // Tidak ada diskon - gunakan harga normal
                    item.harga_khusus = 0;
                    item.diskon = 0;
                }
            } else {
                // Tidak ada harga khusus untuk produk ini - gunakan harga normal
                item.harga_khusus = 0;
                item.diskon = 0;
            }
            
            this.calculateItemSubtotal(item, this.invoiceForm.items.indexOf(item));
        },

        async searchOngkir(item, index) {
        if (!item.ongkir_search) {
            item.ongkir_results = [];
            return;
        }

        try {
            const params = new URLSearchParams({
            search: item.ongkir_search,
            outlet_id: this.selectedOutlet
            });

            const response = await fetch(`<?php echo e(route("admin.penjualan.ongkir.data")); ?>?${params}`);
            const data = await response.json();
            
            if (data.data && Array.isArray(data.data)) {
            item.ongkir_results = data.data.filter(ongkir => 
                ongkir && ongkir.daerah && ongkir.daerah.toLowerCase().includes(item.ongkir_search.toLowerCase())
            );
            } else {
            item.ongkir_results = [];
            }
        } catch (error) {
            console.error('Error searching ongkir:', error);
            item.ongkir_results = [];
        }
        },

        selectOngkir(ongkir, item, index) {
            if (!ongkir) {
                console.error('Ongkir data is null');
                return;
            }
            
            item.selectedOngkir = ongkir;
            item.id_ongkir = ongkir.id_ongkir;
            item.deskripsi = 'Ongkos Kirim - ' + ongkir.daerah;
            item.harga = ongkir.harga;
            item.satuan = 'Trip';
            item.ongkir_search = ongkir.daerah;
            item.ongkir_results = [];
            item.show_new_ongkir = false;
            this.calculateItemSubtotal(item, index);
        },

        showNewOngkirForm(item, index) {
            item.show_new_ongkir = true;
            item.ongkir_results = [];
            item.new_ongkir_daerah = item.ongkir_search;
            item.new_ongkir_harga = 0;
        },

        cancelNewOngkir(item, index) {
            item.show_new_ongkir = false;
            item.new_ongkir_daerah = '';
            item.new_ongkir_harga = 0;
            item.ongkir_search = '';
        },

        onNewOngkirInput(item, index) {
            item.harga = parseFloat(item.new_ongkir_harga) || 0;
            if (item.new_ongkir_daerah) {
                item.deskripsi = 'Ongkos Kirim - ' + item.new_ongkir_daerah;
            }
            this.calculateItemSubtotal(item, index);
        },

        async saveNewOngkir(item, index) {
            if (!item.new_ongkir_daerah || !item.new_ongkir_harga) {
                this.showToastMessage('Daerah dan harga ongkir harus diisi', 'error');
                return;
            }

            try {
                const requestData = {
                    daerah: item.new_ongkir_daerah,
                    harga: parseFloat(item.new_ongkir_harga),
                    outlet_id: this.selectedOutlet // Pastikan kirim selectedOutlet
                };

                console.log('Saving ongkir data:', requestData);

                const response = await fetch('<?php echo e(route("admin.penjualan.ongkir.store")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                // Handle response dengan lebih baik
                const responseText = await response.text();
                console.log('Ongkir store response:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse JSON response:', parseError);
                    throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 100));
                }

                if (response.ok && result.success) {
                    this.showToastMessage('Ongkos kirim berhasil disimpan', 'success');
                    
                    // Refresh data ongkir
                    await this.loadOngkirData();
                    await this.loadOngkir(); // Refresh untuk invoice form
                    
                    // Cari ongkir yang baru dibuat
                    const newOngkir = this.availableOngkir.find(o => 
                        o && o.daerah === item.new_ongkir_daerah && o.harga == item.new_ongkir_harga
                    );
                    
                    if (newOngkir) {
                        this.selectOngkir(newOngkir, item, index);
                    }
                    
                    // Reset form
                    item.show_new_ongkir = false;
                    item.new_ongkir_daerah = '';
                    item.new_ongkir_harga = 0;
                    
                } else {
                    throw new Error(result.message || 'Gagal menyimpan ongkos kirim');
                }
            } catch (error) {
                console.error('Error saving ongkir:', error);
                this.showToastMessage('Gagal menyimpan ongkos kirim: ' + error.message, 'error');
            }
        },

        onItemTypeChange(item, index) {
            item.product_search = '';
            item.product_results = [];
            item.selectedProduct = null;
            item.ongkir_search = '';
            item.ongkir_results = [];
            item.selectedOngkir = null; 
            item.show_new_ongkir = false;
            item.new_ongkir_daerah = '';
            item.new_ongkir_harga = 0;
            item.deskripsi = '';
            item.harga = 0;
            item.harga_normal = 0;
            item.diskon = 0;
            item.subtotal = 0;

            if (item.tipe === 'ongkir') {
                item.satuan = 'Trip';
            } else if (item.tipe === 'lainnya') {
                item.deskripsi = 'Biaya Lainnya';
                item.satuan = 'Unit';
            } else {
                item.satuan = 'Unit';
            }
        },

        removeInvoiceItem(index) {
          this.invoiceForm.items.splice(index, 1);
          this.calculateTotal();
        },


        onProductSelect(item, index) {
          const product = this.availableProducts.find(p => p.id_produk == item.id_produk);
            if (product) {
                item.selectedProduct = product;
                item.deskripsi = product.nama_produk;
                item.harga = product.harga;
                item.satuan = product.satuan || 'Unit';
                this.calculateItemSubtotal(item, index);
            } else {
                item.selectedProduct = null;
                item.deskripsi = '';
                item.harga = 0;
                item.satuan = 'Unit';
                item.subtotal = 0;
            }
        },

        goToPurchaseOrder() {
            if (this.selectedOutOfStockProduct) {
                // Redirect ke halaman purchase order
                window.location.href = `<?php echo e(route('pembelian.purchase-order.index')); ?>`;
            }
            this.showStockAlert = false;
        },

        calculateItemSubtotal(item, index) {
            const kuantitas = parseFloat(item.kuantitas) || 0;
            const hargaNormal = parseFloat(item.harga) || 0;
            
            // Gunakan harga khusus jika ada, jika tidak gunakan harga normal
            const hargaFinal = (item.harga_khusus && item.harga_khusus > 0) ? 
                parseFloat(item.harga_khusus) : hargaNormal;
            
            item.subtotal = kuantitas * hargaFinal;
            
            // Validasi stok setelah perubahan kuantitas
            if (item.selectedProduct && item.tipe === 'produk') {
                const stokTersedia = item.selectedProduct.stok || 0;
                if (stokTersedia < kuantitas) {
                    this.selectedOutOfStockProduct = {
                        ...item.selectedProduct,
                        kuantitas_dibutuhkan: kuantitas
                    };
                    this.showStockAlert = true;
                    
                    // Reset kuantitas ke stok tersedia
                    item.kuantitas = stokTersedia;
                    this.calculateItemSubtotal(item, index);
                }
            }
        },

        calculateTotal() {
            const subtotal = this.invoiceForm.items.reduce((total, item) => total + (parseFloat(item.subtotal) || 0), 0);
            const totalDiscount = this.calculateTotalDiscount();
            
            return {
                subtotal: subtotal,
                totalDiscount: totalDiscount,
                grandTotal: subtotal // Karena diskon sudah dihitung di harga per item
            };
        },

        calculateTotalDiscount() {
            return this.invoiceForm.items.reduce((total, item) => {
                return total + ((parseFloat(item.diskon) || 0) * (parseFloat(item.kuantitas) || 0));
            }, 0);
        },

        async submitInvoice() {
            // Validasi dasar
            if (!this.invoiceForm.tanggal) {
                this.showToastMessage('Tanggal harus diisi', 'error');
                return;
            }

            // Pastikan due_date terisi, jika tidak set default 30 hari dari tanggal
            if (!this.invoiceForm.due_date) {
                const tanggalInvoice = new Date(this.invoiceForm.tanggal);
                const defaultDueDate = new Date(tanggalInvoice.getTime() + 30 * 24 * 60 * 60 * 1000);
                this.invoiceForm.due_date = defaultDueDate.toISOString().split('T')[0];
                console.log('Auto-set due_date to:', this.invoiceForm.due_date);
            }

            if (!this.invoiceForm.due_date) {
                this.showToastMessage('Tanggal jatuh tempo harus diisi', 'error');
                return;
            }

            if (!this.invoiceForm.id_outlet) {
                this.showToastMessage('Outlet harus dipilih', 'error');
                return;
            }

            if (!this.invoiceForm.customer_type || !this.invoiceForm.customer_id) {
                this.showToastMessage('Customer harus dipilih', 'error');
                return;
            }

            if (this.invoiceForm.items.length === 0) {
                this.showToastMessage('Minimal satu item harus ditambahkan', 'error');
                return;
            }

            // Validasi setiap item
            for (let i = 0; i < this.invoiceForm.items.length; i++) {
                const item = this.invoiceForm.items[i];
                
                if (!item.deskripsi) {
                    this.showToastMessage(`Deskripsi item ${i + 1} harus diisi`, 'error');
                    return;
                }

                if (!item.kuantitas || item.kuantitas <= 0) {
                    this.showToastMessage(`Kuantitas item ${i + 1} harus lebih dari 0`, 'error');
                    return;
                }

                if (!item.harga || item.harga < 0) {
                    this.showToastMessage(`Harga item ${i + 1} harus diisi dengan nilai valid`, 'error');
                    return;
                }

                // Validasi khusus untuk produk
                if (item.tipe === 'produk' && !item.id_produk) {
                    this.showToastMessage(`Produk untuk item ${i + 1} harus dipilih`, 'error');
                    return;
                }
            }

            this.savingInvoice = true;
                try {
                    const url = this.editingInvoice 
                ? `<?php echo e(route('admin.penjualan.invoice.update', '')); ?>/${this.editingInvoice.id_sales_invoice}`
                : '<?php echo e(route("admin.penjualan.invoice.store")); ?>';

            const method = this.editingInvoice ? 'PUT' : 'POST';

            // Hitung totals
            const subtotal = this.calculateSubtotal();
            const totalDiskon = this.calculateTotalDiscount();
            const grandTotal = this.calculateGrandTotal();

            // Format data untuk API - include diskon dan harga_normal
            const requestData = {
                tanggal: this.invoiceForm.tanggal,
                due_date: this.invoiceForm.due_date,
                customer_type: this.invoiceForm.customer_type,
                customer_id: this.invoiceForm.customer_id,
                id_outlet: this.invoiceForm.id_outlet,
                outlet_id: this.selectedOutlet,
                keterangan: this.invoiceForm.keterangan || '',
                subtotal: subtotal,
                total_diskon: totalDiskon,
                total: grandTotal,
                items: this.invoiceForm.items.map(item => {
                    const baseItem = {
                        deskripsi: item.deskripsi,
                        keterangan: item.keterangan || '',
                        kuantitas: parseFloat(item.kuantitas) || 0,
                        satuan: item.satuan || 'Unit',
                        harga_normal: parseFloat(item.harga) || 0, // Harga normal
                        harga: parseFloat(item.harga_khusus) || parseFloat(item.harga) || 0, // Harga setelah diskon
                        diskon: parseFloat(item.diskon) || 0,
                        subtotal: parseFloat(item.subtotal) || 0,
                        tipe: item.tipe
                    };

                    // Tambahkan field spesifik berdasarkan tipe
                    if (item.tipe === 'produk') {
                        baseItem.id_produk = item.id_produk;
                    } else if (item.tipe === 'ongkir') {
                        baseItem.id_ongkir = item.id_ongkir;
                    }

                    return baseItem;
                })
            };

            // Jika edit, tambahkan ID invoice
            if (this.editingInvoice) {
                requestData.id_sales_invoice = this.editingInvoice.id_sales_invoice;
            }

            console.log('Submitting Invoice Data with Discount:', requestData);
            console.log('Due Date Debug:', {
                due_date: this.invoiceForm.due_date,
                tanggal: this.invoiceForm.tanggal,
                due_date_in_request: requestData.due_date
            });

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

                // Handle response
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.showToastMessage(result.message, 'success');
                    this.closeInvoiceModal();
                    await this.loadInvoices();
                    await this.loadStats();
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join(', ');
                        this.showToastMessage(`Validasi gagal: ${errorMessages}`, 'error');
                    } else {
                        this.showToastMessage(result.message || 'Terjadi kesalahan saat menyimpan invoice', 'error');
                    }
                }
            } catch (error) {
                console.error('Error saving invoice:', error);
                
                if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                    this.showToastMessage('Koneksi jaringan bermasalah. Periksa koneksi internet Anda.', 'error');
                } else if (error.message.includes('HTTP error')) {
                    this.showToastMessage('Terjadi kesalahan server. Silakan coba lagi.', 'error');
                } else {
                    this.showToastMessage('Gagal menyimpan invoice: ' + error.message, 'error');
                }
            } finally {
                this.savingInvoice = false;
            }
        },

        closeInvoiceModal() {
            this.showInvoiceModal = false;
            this.editingInvoice = null;
            this.invoiceForm = {
                id_sales_invoice: null,
                no_invoice: '',
                tanggal: new Date().toISOString().split('T')[0],
                customer_type: '',
                customer_id: '',
                id_outlet: '',
                keterangan: '',
                items: []
            };
            this.selectedCustomer = null;
            this.customerSearch = '';
        },

        showPrintModal: false,
        loadingPreview: false,
        selectedTemplate: 'standard',
        setAsDefault: false, // Checkbox untuk set default template
        previewUrl: '',
        currentPrintInvoice: null,
        printTemplates: [
            { value: 'standard', name: 'Standard' },
            { value: 'pos_style', name: 'Ikuti POS' },
            { value: 'invoice_delivery', name: 'Invoice+surat jalan' }
        ],
        defaultTemplate: 'standard', // Template default yang dipilih user

        refreshPreview() {
            if (this.currentPrintInvoice) {
                this.loadingPreview = true;
                this.previewUrl = `<?php echo e(route('admin.penjualan.invoice.print', ':id')); ?>?template=${this.selectedTemplate}&preview=true&t=${Date.now()}`.replace(':id', this.currentPrintInvoice.id_sales_invoice);
            }
        },

        selectedTemplate: {
            handler(newTemplate, oldTemplate) {
                if (this.currentPrintInvoice && this.showPrintModal && newTemplate !== oldTemplate) {
                    this.loadingPreview = true;
                    this.previewUrl = this.generatePreviewUrl(this.currentPrintInvoice.id_sales_invoice, newTemplate);
                }
            }
        },

        generatePreviewUrl(invoiceId, template) {
            const timestamp = new Date().getTime();
            return `<?php echo e(route('admin.penjualan.invoice.print', ':id')); ?>?template=${template}&preview=true&_=${timestamp}`.replace(':id', invoiceId);
        },

        printInvoice(invoiceId) {
            const invoice = this.invoices.find(inv => inv.id_sales_invoice === invoiceId);
            if (invoice) {
                this.currentPrintInvoice = invoice;
                // Gunakan default template jika sudah diset, atau standard sebagai fallback
                this.selectedTemplate = this.defaultTemplate || 'standard';
                // Set checkbox jika template yang dipilih sama dengan default template
                this.setAsDefault = (this.selectedTemplate === this.defaultTemplate);
                this.previewUrl = this.generatePreviewUrl(invoice.id_sales_invoice, this.selectedTemplate);
                this.showPrintModal = true;
                this.loadingPreview = true;
            }
        },

        onPreviewLoad() {
            this.loadingPreview = false;
        },

        closePrintModal() {
            this.showPrintModal = false;
            this.currentPrintInvoice = null;
            this.loadingPreview = false;
        },

        getTemplateName(templateValue) {
            const template = this.printTemplates.find(t => t.value === templateValue);
            return template ? template.name : 'Standard';
        },

        downloadPDF() {
            if (this.currentPrintInvoice) {
                const downloadUrl = `<?php echo e(route('admin.penjualan.invoice.print', ':id')); ?>?template=${this.selectedTemplate}&download=true`.replace(':id', this.currentPrintInvoice.id_sales_invoice);
                window.open(downloadUrl, '_blank');
            }
        },

        printInvoiceDirect() {
            if (this.currentPrintInvoice && this.$refs.previewFrame) {
                this.$refs.previewFrame.contentWindow.print();
            }
        },

        // Method untuk menangani default template
        loadDefaultTemplate() {
            const saved = localStorage.getItem('invoice_default_template');
            if (saved) {
                this.defaultTemplate = saved;
            }
        },

        saveDefaultTemplate() {
            if (this.setAsDefault && this.selectedTemplate) {
                localStorage.setItem('invoice_default_template', this.selectedTemplate);
                this.defaultTemplate = this.selectedTemplate;
                
                // Show notification
                this.showNotification('Template "' + this.getTemplateName(this.selectedTemplate) + '" berhasil disimpan sebagai default', 'success');
            }
        },

        // Method untuk handle checkbox change
        onSetAsDefaultChange() {
            if (this.setAsDefault) {
                this.saveDefaultTemplate();
            }
        },

        // Method untuk menampilkan notifikasi
        showNotification(message, type = 'success') {
            // Buat elemen notifikasi
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'}'></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animasi masuk
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Hapus setelah 3 detik
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        },

        async editInvoice(invoice) {
            try {
                console.log('Editing invoice:', invoice);
                ModalLoader.show();
                
                // Fetch detail invoice dari API
                const response = await fetch(`<?php echo e(route('admin.penjualan.invoice.show', '')); ?>/${invoice.id_sales_invoice}`);
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Gagal memuat data invoice');
                }

                const invoiceData = result.data;
                console.log('Invoice detail:', invoiceData);

                // Format items untuk form
                const formattedItems = invoiceData.items.map(item => {
                    const baseItem = {
                        tipe: item.tipe,
                        id_produk: item.id_produk || '',
                        id_ongkir: item.id_ongkir || '',
                        deskripsi: item.deskripsi,
                        kuantitas: parseFloat(item.kuantitas) || 1,
                        satuan: item.satuan || 'Unit',
                        harga: parseFloat(item.harga_normal) || parseFloat(item.harga) || 0,
                        harga_normal: parseFloat(item.harga_normal) || parseFloat(item.harga) || 0,
                        harga_khusus: parseFloat(item.harga) || 0,
                        diskon: parseFloat(item.diskon) || 0,
                        subtotal: parseFloat(item.subtotal) || 0,
                        keterangan: item.keterangan || '',
                        product_search: '',
                        product_results: [],
                        selectedProduct: null,
                        ongkir_search: '',
                        ongkir_results: [],
                        selectedOngkir: null,
                        show_new_ongkir: false,
                        new_ongkir_daerah: '',
                        new_ongkir_harga: 0
                    };

                    // Pre-select product jika ada
                    if (item.tipe === 'produk' && item.id_produk && item.produk) {
                        // Get current stock from availableProducts (real-time stock)
                        const currentProduct = this.availableProducts.find(p => p.id_produk == item.id_produk);
                        const currentStok = currentProduct ? currentProduct.stok : (item.produk.stok || 0);
                        
                        baseItem.selectedProduct = {
                            id_produk: item.produk.id_produk,
                            nama_produk: item.produk.nama_produk,
                            harga: parseFloat(item.harga_normal) || parseFloat(item.produk.harga_jual) || 0,
                            satuan: item.produk.satuan?.nama_satuan || 'Unit',
                            stok: currentStok // Use current stock, not historical
                        };
                        baseItem.product_search = item.produk.nama_produk;
                    }

                    // Pre-select ongkir jika item adalah ongkos kirim
                    if (item.tipe === 'ongkir' && invoiceData.ongkos_kirim) {
                        baseItem.selectedOngkir = {
                            id_ongkir: invoiceData.ongkos_kirim.id_ongkir,
                            daerah: invoiceData.ongkos_kirim.daerah,
                            harga: parseFloat(invoiceData.ongkos_kirim.harga) || 0
                        };
                        baseItem.ongkir_search = invoiceData.ongkos_kirim.daerah;
                        baseItem.id_ongkir = invoiceData.ongkos_kirim.id_ongkir;
                    }

                    return baseItem;
                });

                // Set form data
                this.invoiceForm = {
                    id_sales_invoice: invoiceData.id_sales_invoice,
                    no_invoice: invoiceData.no_invoice,
                    tanggal: new Date(invoiceData.tanggal).toISOString().split('T')[0],
                    due_date: invoiceData.due_date ? new Date(invoiceData.due_date).toISOString().split('T')[0] : new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    customer_type: invoiceData.id_member ? 'member' : 'prospek',
                    customer_id: invoiceData.id_member || invoiceData.id_prospek,
                    id_outlet: invoiceData.id_outlet,
                    id_ongkir: invoiceData.id_ongkir, // Tambahkan ini
                    keterangan: invoiceData.keterangan || '',
                    items: formattedItems
                };

                // Set customer data
                if (invoiceData.member) {
                    this.selectedCustomer = {
                        id: invoiceData.member.id_member,
                        nama: invoiceData.member.nama,
                        nama_perusahaan: invoiceData.member.nama_perusahaan,
                        telepon: invoiceData.member.telepon,
                        alamat: invoiceData.member.alamat,
                        type: 'member'
                    };
                    this.customerSearch = invoiceData.member.nama;
                } else if (invoiceData.prospek) {
                    this.selectedCustomer = {
                        id: invoiceData.prospek.id_prospek,
                        nama: invoiceData.prospek.nama,
                        nama_perusahaan: invoiceData.prospek.nama_perusahaan || null,
                        telepon: invoiceData.prospek.telepon,
                        alamat: invoiceData.prospek.alamat,
                        type: 'prospek'
                    };
                    this.customerSearch = invoiceData.prospek.nama;
                }

                console.log('Form data set for editing:', this.invoiceForm);
                this.showInvoiceModal = true;

            } catch (error) {
                console.error('Error loading invoice for edit:', error);
                this.showToastMessage('Gagal memuat data invoice: ' + error.message, 'error');
            } finally {
                ModalLoader.hide();
            }
        },

        deleteInvoice(invoiceId) {
          const invoice = this.invoices.find(inv => inv.id_sales_invoice === invoiceId);
          if (invoice) {
            this.invoiceToDelete = invoice;
            this.showDeleteConfirm = true;
          }
        },

        async confirmDelete() {
          if (!this.invoiceToDelete) return;

          try {
            const response = await fetch(`<?php echo e(route('admin.penjualan.invoice.destroy', '')); ?>/${this.invoiceToDelete.id_sales_invoice}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message, 'success');
              this.showDeleteConfirm = false;
              this.invoiceToDelete = null;
              await this.loadInvoices();
              await this.loadStats();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus invoice', 'error');
            }
          } catch (error) {
            console.error('Error deleting invoice:', error);
            this.showToastMessage('Gagal menghapus invoice', 'error');
          }
        },

        async openInvoiceSetting() {
            try {
                ModalLoader.show();
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.invoice.setting")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.invoiceSetting = data;
                    this.invoiceSettingForm.starting_number = data.current_number + 1;
                    this.invoiceSettingForm.year = data.current_year;
                    this.invoiceSettingForm.invoice_prefix = data.invoice_prefix || 'SLS.INV';
                    this.invoiceSettingForm.outlet_id = this.selectedOutlet;
                    this.showInvoiceSettingModal = true;
                }
            } catch (error) {
                console.error('Error loading invoice setting:', error);
                this.showToastMessage('Gagal memuat setting invoice', 'error');
            } finally {
                ModalLoader.hide();
            }
        },

        async updateInvoiceSetting() {
            try {
                // Update form dengan outlet_id terbaru
                this.invoiceSettingForm.outlet_id = this.selectedOutlet;

                const response = await fetch('<?php echo e(route("admin.penjualan.invoice.setting.update")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(this.invoiceSettingForm)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message, 'success');
                    this.closeInvoiceSettingModal();
                    await this.openInvoiceSetting(); // Reload setting
                } else {
                    this.showToastMessage(result.message || 'Gagal menyimpan setting', 'error');
                }
            } catch (error) {
                console.error('Error updating invoice setting:', error);
                this.showToastMessage('Gagal menyimpan setting invoice', 'error');
            }
        },

        closeInvoiceSettingModal() {
          this.showInvoiceSettingModal = false;
        },

        exportExcel() {
          const params = new URLSearchParams({
            status: this.activeTab,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            outlet_filter: this.filters.outlet
          });
          window.open(`<?php echo e(route('admin.penjualan.invoice.export.excel')); ?>?${params}`, '_blank');
          this.exportMenuOpen = false;
        },

        exportPdf() {
          const params = new URLSearchParams({
            status: this.activeTab,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            outlet_filter: this.filters.outlet
          });
          window.open(`<?php echo e(route('admin.penjualan.invoice.export.pdf')); ?>?${params}`, '_blank');
          this.exportMenuOpen = false;
        },

       
        formatDate(dateString) {
          if (!dateString) return '-';
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          });
        },

        formatCurrency(amount) {
            const safeAmount = amount || 0;
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(safeAmount);
        },

        getStatusText(status) {
          const statusMap = {
            'menunggu': 'Menunggu',
            'lunas': 'Lunas',
            'gagal': 'Retur/Gagal'
          };
          return statusMap[status] || status;
        },

        getStatusBadgeClass(status) {
          const classMap = {
            'draft': 'bg-gray-100 text-gray-800',
            'menunggu': 'bg-amber-100 text-amber-800',
            'dibayar_sebagian': 'bg-blue-100 text-blue-800',
            'lunas': 'bg-emerald-100 text-emerald-800',
            'gagal': 'bg-red-100 text-red-800'
          };
          return classMap[status] || 'bg-slate-100 text-slate-800';
        },

        getStatusText(status) {
          const textMap = {
            'draft': 'Draft',
            'menunggu': 'Menunggu',
            'dibayar_sebagian': 'Dibayar Sebagian',
            'lunas': 'Lunas',
            'gagal': 'Gagal'
          };
          return textMap[status] || status;
        },

        getRemainingDaysText(dueDate, status) {
          if (status !== 'menunggu' || !dueDate) return '-';
          
          const today = new Date();
          const due = new Date(dueDate);
          const diffTime = due - today;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          
          if (diffDays < 0) {
            return `${Math.abs(diffDays)} hari lewat`;
          } else if (diffDays === 0) {
            return 'Hari ini';
          } else {
            return `${diffDays} hari lagi`;
          }
        },

        getRemainingDaysClass(dueDate, status) {
          if (status !== 'menunggu' || !dueDate) return 'text-slate-500';
          
          const today = new Date();
          const due = new Date(dueDate);
          const diffTime = due - today;
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          
          if (diffDays < 0) {
            return 'text-red-600 font-semibold';
          } else if (diffDays <= 3) {
            return 'text-amber-600 font-semibold';
          } else {
            return 'text-green-600';
          }
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        showPaymentModal: false,
        showPaymentHistoryModal: false,
        showBuktiModal: false,
        currentBuktiUrl: '',
        paymentHistoryData: {
            invoice: null,
            payment_history: []
        },
        paymentForm: {
            invoice_id: null,
            no_invoice: '',
            total: 0,
            jenis_pembayaran: 'cash',
            penerima: '<?php echo e(auth()->user()?->name ?? ""); ?>',
            tanggal_pembayaran: new Date().toISOString().split('T')[0],
            catatan_pembayaran: '',
            // Field baru untuk transfer
            nama_bank: '',
            nama_pengirim: '',
            jumlah_transfer: 0,
            bukti_transfer_file: null,
            bukti_transfer_preview: null,
            bukti_transfer_preview_type: null,
            bukti_transfer_name: ''
        },

  
        // Open payment modal for installment payment
        async openPaymentModal(invoiceId) {
            try {
                const invoice = this.invoices.find(inv => inv.id_sales_invoice === invoiceId);
                if (invoice) {
                    const sisaTagihan = invoice.sisa_tagihan || invoice.total;
                    this.paymentForm = {
                        invoice_id: invoiceId,
                        no_invoice: invoice.no_invoice,
                        total: invoice.total,
                        total_dibayar: invoice.total_dibayar || 0,
                        sisa_tagihan: sisaTagihan,
                        jenis_pembayaran: 'cash',
                        penerima: '<?php echo e(auth()->user()?->name ?? ""); ?>',
                        tanggal_pembayaran: new Date().toISOString().split('T')[0],
                        catatan_pembayaran: '',
                        nama_bank: '',
                        nama_pengirim: '',
                        jumlah_transfer: sisaTagihan, // Default to remaining amount
                        bukti_transfer_file: null,
                        bukti_transfer_preview: null,
                        bukti_transfer_preview_type: null,
                        bukti_transfer_name: ''
                    };
                    this.showPaymentModal = true;
                    this.processingPayment = false;
                }
            } catch (error) {
                console.error('Error opening payment modal:', error);
                this.showToastMessage('Gagal membuka form pembayaran', 'error');
            }
        },

        // Open payment history modal
        async openPaymentHistoryModal(invoiceId) {
            try {
                this.showPaymentHistoryModal = true;
                this.paymentHistoryData = {
                    invoice: null,
                    payment_history: []
                };

                // Load payment history
                const response = await fetch(`<?php echo e(route('admin.penjualan.invoice.payment.history', ':id')); ?>`.replace(':id', invoiceId));
                const result = await response.json();

                if (result.success) {
                    this.paymentHistoryData = result.data;
                } else {
                    throw new Error(result.message || 'Gagal memuat riwayat pembayaran');
                }
            } catch (error) {
                console.error('Error loading payment history:', error);
                this.showToastMessage('Gagal memuat riwayat pembayaran: ' + error.message, 'error');
                this.showPaymentHistoryModal = false;
            }
        },

        // Open bukti modal
        openBuktiModal(buktiUrl) {
            if (buktiUrl) {
                this.currentBuktiUrl = buktiUrl;
                this.showBuktiModal = true;
            }
        },

        // View payment bukti (deprecated - kept for compatibility)
        viewPaymentBukti(buktiUrl) {
            this.openBuktiModal(buktiUrl);
        },

        // Confirm invoice (change from draft to menunggu)
        async confirmInvoice(invoiceId) {
            try {
                if (!confirm('Konfirmasi invoice ini? Setelah dikonfirmasi, invoice tidak bisa diedit lagi dan nomor invoice akan digenerate.')) {
                    return;
                }

                const response = await fetch(`<?php echo e(route('admin.penjualan.invoice.confirm', ':id')); ?>`.replace(':id', invoiceId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showToastMessage('Invoice berhasil dikonfirmasi dan nomor invoice telah digenerate', 'success');
                    await this.loadInvoices();
                    await this.loadStats();
                } else {
                    throw new Error(result.message || 'Gagal mengkonfirmasi invoice');
                }
            } catch (error) {
                console.error('Error confirming invoice:', error);
                this.showToastMessage('Gagal mengkonfirmasi invoice: ' + error.message, 'error');
            }
        },

        async updateInvoiceStatus(invoiceId, status) {
            if (status === 'lunas') {
           
                const invoice = this.invoices.find(inv => inv.id_sales_invoice === invoiceId);
                if (invoice) {
                    this.paymentForm = {
                        invoice_id: invoiceId,
                        no_invoice: invoice.no_invoice,
                        total: invoice.total,
                        jenis_pembayaran: 'cash',
                        penerima: '<?php echo e(auth()->user()?->name ?? ""); ?>',
                        tanggal_pembayaran: new Date().toISOString().split('T')[0],
                        catatan_pembayaran: ''
                    };
                    this.showPaymentModal = true;
                    this.processingPayment = false;
                }
            } else {
              
                if (confirm(`Ubah status invoice menjadi ${status}?`)) {
                    await this.submitStatusUpdate(invoiceId, status, {});
                }
            }
        },

       
        async submitStatusUpdate(invoiceId, status, paymentData = {}) {
            try {
                const url = `<?php echo e(route('admin.penjualan.invoice.update-status', ':id')); ?>`.replace(':id', invoiceId);
                
                const requestData = {
                    status: status,
                    ...paymentData
                };

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.showToastMessage(result.message, 'success');
                    await this.loadInvoices();
                    await this.loadStats();
                } else {
                    throw new Error(result.message || 'Gagal mengupdate status');
                }
            } catch (error) {
                console.error('Error updating invoice status:', error);
                throw new Error('Gagal mengupdate status invoice: ' + error.message);
            }
        },

    
        async confirmPayment() {
            if (this.processingPayment) {
                return;
            }

            // Validasi form
            if (!this.isPaymentFormValid()) {
                this.showToastMessage('Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }

            // Validasi jumlah bayar tidak boleh 0
            if (!this.paymentForm.jumlah_transfer || this.paymentForm.jumlah_transfer <= 0) {
                this.showToastMessage('Jumlah bayar harus lebih dari 0', 'error');
                return;
            }

            // Bukti pembayaran OPSIONAL - tidak perlu validasi

            this.processingPayment = true;

            try {
                // Create FormData untuk handle file upload
                const formData = new FormData();
                formData.append('invoice_id', this.paymentForm.invoice_id);
                formData.append('tanggal_bayar', this.paymentForm.tanggal_pembayaran);
                formData.append('jumlah_bayar', this.paymentForm.jumlah_transfer);
                formData.append('jenis_pembayaran', this.paymentForm.jenis_pembayaran);
                formData.append('nama_bank', this.paymentForm.nama_bank || '');
                formData.append('nama_pengirim', this.paymentForm.nama_pengirim || '');
                formData.append('penerima', this.paymentForm.penerima || '');
                
                // Hanya append bukti jika ada file
                if (this.paymentForm.bukti_transfer_file) {
                    formData.append('bukti_pembayaran', this.paymentForm.bukti_transfer_file);
                }
                
                formData.append('keterangan', this.paymentForm.catatan_pembayaran || '');

                const response = await fetch('<?php echo e(route("admin.penjualan.invoice.payment.process")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Update invoice in the list
                    const invoice = this.invoices.find(inv => inv.id_sales_invoice === this.paymentForm.invoice_id);
                    if (invoice) {
                        invoice.total_dibayar = result.data.total_dibayar;
                        invoice.sisa_tagihan = result.data.sisa_tagihan;
                        invoice.status = result.data.status;
                    }

                    // Reload invoice data
                    await this.loadInvoices();
                    await this.loadStats();
                    
                    // Always close modal after successful payment
                    this.showPaymentModal = false;
                    
                    // Show appropriate message
                    if (result.data.is_fully_paid) {
                        this.showToastMessage('Pembayaran berhasil! Invoice telah lunas.', 'success');
                    } else {
                        this.showToastMessage('Pembayaran cicilan berhasil dicatat. Sisa tagihan: ' + this.formatCurrency(result.data.sisa_tagihan), 'success');
                    }
                } else {
                    throw new Error(result.message || 'Gagal memproses pembayaran');
                }
            } catch (error) {
                console.error('Error confirming payment:', error);
                this.showToastMessage('Gagal memproses pembayaran: ' + error.message, 'error');
            } finally {
                this.processingPayment = false;
            }
        },

        async openCoaSetting() {
            try {
                ModalLoader.show();
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.coa-setting")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.coaData = data;
                    
                    // Load existing setting
                    if (data.setting) {
                        this.coaForm.accounting_book_id = data.setting.accounting_book_id || '';
                        
                        // Untuk field akun, langsung set kode akun (tidak perlu cari ID)
                        if (data.setting.akun_piutang_usaha) {
                            const account = this.findAccountByCode(data.setting.akun_piutang_usaha);
                            if (account) {
                                this.coaForm.akun_piutang_usaha = account.code;
                                this.coaForm.akun_piutang_usaha_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                        if (data.setting.akun_pendapatan_penjualan) {
                            const account = this.findAccountByCode(data.setting.akun_pendapatan_penjualan);
                            if (account) {
                                this.coaForm.akun_pendapatan_penjualan = account.code;
                                this.coaForm.akun_pendapatan_penjualan_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                        if (data.setting.akun_kas) {
                            const account = this.findAccountByCode(data.setting.akun_kas);
                            if (account) {
                                this.coaForm.akun_kas = account.code;
                                this.coaForm.akun_kas_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                        if (data.setting.akun_bank) {
                            const account = this.findAccountByCode(data.setting.akun_bank);
                            if (account) {
                                this.coaForm.akun_bank = account.code;
                                this.coaForm.akun_bank_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                        if (data.setting.akun_hpp) {
                            const account = this.findAccountByCode(data.setting.akun_hpp);
                            if (account) {
                                this.coaForm.akun_hpp = account.code;
                                this.coaForm.akun_hpp_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                        if (data.setting.akun_persediaan) {
                            const account = this.findAccountByCode(data.setting.akun_persediaan);
                            if (account) {
                                this.coaForm.akun_persediaan = account.code;
                                this.coaForm.akun_persediaan_display = account.full_name || `${account.code} - ${account.name}`;
                            }
                        }
                    }
                    
                    this.showCoaModal = true;
                    await this.previewCoaJournal();
                } else {
                    this.showToastMessage('Gagal memuat setting COA', 'error');
                }
            } catch (error) {
                console.error('Error loading COA setting:', error);
                this.showToastMessage('Gagal memuat setting COA', 'error');
            } finally {
                ModalLoader.hide();
            }
        },

        // Helper method untuk mencari akun berdasarkan KODE
        findAccountByCode(code) {
            return this.coaData.accounts.find(account => account.code === code);
        },

        // Helper method untuk mencari akun berdasarkan ID (jika masih diperlukan)
        findAccountById(id) {
            return this.coaData.accounts.find(account => account.id == id);
        },

        updateCoaDisplayValues() {
            if (this.coaForm.akun_piutang_usaha) {
                const account = this.findAccountByCode(this.coaForm.akun_piutang_usaha);
                this.coaForm.akun_piutang_usaha_display = account ? `${account.code} - ${account.name}` : this.coaForm.akun_piutang_usaha;
            }
            if (this.coaForm.akun_pendapatan_penjualan) {
                const account = this.findAccountByCode(this.coaForm.akun_pendapatan_penjualan);
                this.coaForm.akun_pendapatan_penjualan_display = account ? `${account.code} - ${account.name}` : this.coaForm.akun_pendapatan_penjualan;
            }
            if (this.coaForm.akun_kas) {
                const account = this.findAccountByCode(this.coaForm.akun_kas);
                this.coaForm.akun_kas_display = account ? `${account.code} - ${account.name}` : this.coaForm.akun_kas;
            }
            if (this.coaForm.akun_bank) {
                const account = this.findAccountByCode(this.coaForm.akun_bank);
                this.coaForm.akun_bank_display = account ? `${account.code} - ${account.name}` : this.coaForm.akun_bank;
            }
        },

        async searchCoaAccounts(field, type = null) {
            const searchTerm = this.coaForm[`${field}_search`].toLowerCase();
            
            if (!searchTerm) {
                this.coaForm[`${field}_results`] = [];
                return;
            }

            // Filter accounts berdasarkan search term dan type (jika ada)
            let filtered = this.coaData.accounts;
            
            if (type) {
                filtered = filtered.filter(account => account.type === type);
            }
            
            filtered = filtered.filter(account => 
                account.code.toLowerCase().includes(searchTerm) || 
                account.name.toLowerCase().includes(searchTerm) ||
                (account.full_name && account.full_name.toLowerCase().includes(searchTerm))
            );
            
            // Apply logic: jika ada akun anak, hanya tampilkan yang level 2
            // Perlu cek dari semua accounts untuk menentukan parent mana yang punya children
            const finalResults = this.filterAccountsByLevel(filtered, this.coaData.accounts);
            
            this.coaForm[`${field}_results`] = finalResults.slice(0, 10); // Limit results
        },

        /**
         * Filter accounts: jika ada anak, hanya tampilkan anak (level 2)
         * Sama seperti implementasi di purchase order
         * @param {Array} accounts - Accounts yang sudah difilter berdasarkan search
         * @param {Array} allAccounts - Semua accounts untuk menentukan parent-child relationship
         */
        filterAccountsByLevel(accounts, allAccounts = null) {
            // Jika allAccounts tidak diberikan, gunakan accounts itu sendiri
            const accountsToCheck = allAccounts || accounts;
            
            const result = [];
            const parentIdsWithChildren = new Set();
            
            // Identifikasi parent yang punya children dari SEMUA accounts
            accountsToCheck.forEach(account => {
                if (account.parent_id) {
                    parentIdsWithChildren.add(account.parent_id);
                }
            });
            
            // Filter accounts yang sudah di-search
            accounts.forEach(account => {
                if (parentIdsWithChildren.has(account.id)) {
                    // Ini parent yang punya children, skip
                    return;
                }
                
                if (account.parent_id && parentIdsWithChildren.has(account.parent_id)) {
                    // Ini child dari parent yang punya children, include
                    result.push(account);
                } else if (!account.parent_id && !parentIdsWithChildren.has(account.id)) {
                    // Ini parent yang tidak punya children, include
                    result.push(account);
                }
            });
            
            return result;
        },

        selectCoaAccount(field, account) {
            this.coaForm[field] = account.code; // Simpan KODE akun, bukan ID
            this.coaForm[`${field}_display`] = account.full_name || `${account.code} - ${account.name}`;
            this.coaForm[`${field}_search`] = '';
            this.coaForm[`${field}_results`] = [];
            
            // Auto preview after selection
            this.previewCoaJournal();
        },

        async previewCoaJournalByTab(status) {
            this.coaPreview.loading = true;
            
            try {
                const params = new URLSearchParams({
                    status: status,
                    total: '1000000', // Example amount for preview
                    outlet_id: this.selectedOutlet // Tambahkan outlet_id
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.coa-setting.preview")); ?>?${params}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.coaPreview[status] = {
                        ...data.preview,
                        loading: false
                    };
                } else {
                    this.coaPreview[status] = {
                        entries: [],
                        description: `Preview untuk status ${status}`,
                        total: 1000000,
                        hpp_amount: 300000,
                        is_balanced: false
                    };
                }
            } catch (error) {
                console.error(`Error previewing COA journal for ${status}:`, error);
                this.coaPreview[status] = {
                    entries: [],
                    description: `Error loading preview untuk ${status}`,
                    total: 1000000,
                    hpp_amount: 300000,
                    is_balanced: false
                };
            } finally {
                this.coaPreview.loading = false;
            }
        },

        // Method untuk refresh semua tab
        async previewCoaJournal() {
            this.coaPreview.loading = true;
            
            // Load preview untuk semua tab secara sequential
            const tabs = ['menunggu', 'lunas', 'gagal'];
            
            for (const tab of tabs) {
                await this.previewCoaJournalByTab(tab);
            }
            
            this.coaPreview.loading = false;
        },

        async saveCoaSetting() {
            // Validasi
            const requiredFields = [
                'accounting_book_id',
                'akun_piutang_usaha',
                'akun_pendapatan_penjualan',
                'akun_kas',
                'akun_bank',
                'akun_hpp',
                'akun_persediaan'
            ];
            
            const fieldNames = {
                'accounting_book_id': 'Buku akuntansi',
                'akun_piutang_usaha': 'Akun piutang usaha',
                'akun_pendapatan_penjualan': 'Akun pendapatan penjualan',
                'akun_kas': 'Akun kas',
                'akun_bank': 'Akun bank',
                'akun_hpp': 'Akun HPP',
                'akun_persediaan': 'Akun persediaan'
            };
            
            for (const field of requiredFields) {
                if (!this.coaForm[field]) {
                    this.showToastMessage(`${fieldNames[field]} harus dipilih`, 'error');
                    return;
                }
            }

            this.savingCoa = true;
            try {
                ModalLoader.show();
                
                // Data yang dikirim ke backend - berisi KODE akun dan outlet_id
                const requestData = {
                    outlet_id: this.selectedOutlet, // Tambahkan outlet_id
                    accounting_book_id: this.coaForm.accounting_book_id,
                    akun_piutang_usaha: this.coaForm.akun_piutang_usaha,
                    akun_pendapatan_penjualan: this.coaForm.akun_pendapatan_penjualan,
                    akun_kas: this.coaForm.akun_kas,
                    akun_bank: this.coaForm.akun_bank,
                    akun_hpp: this.coaForm.akun_hpp,
                    akun_persediaan: this.coaForm.akun_persediaan
                };

                console.log('Saving COA data for outlet:', this.selectedOutlet, requestData);

                const response = await fetch('<?php echo e(route("admin.penjualan.coa-setting.update")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message, 'success');
                    this.closeCoaModal();
                } else {
                    this.showToastMessage(result.message || 'Gagal menyimpan setting COA', 'error');
                }
            } catch (error) {
                console.error('Error saving COA setting:', error);
                this.showToastMessage('Gagal menyimpan setting COA', 'error');
            } finally {
                this.savingCoa = false;
                ModalLoader.hide();
            }
        },

        closeCoaModal() {
            this.showCoaModal = false;
            // Reset form
            this.coaForm = {
                accounting_book_id: '',
                akun_piutang_usaha: '',
                akun_piutang_usaha_search: '',
                akun_piutang_usaha_results: [],
                akun_piutang_usaha_display: '',
                akun_pendapatan_penjualan: '',
                akun_pendapatan_penjualan_search: '',
                akun_pendapatan_penjualan_results: [],
                akun_pendapatan_penjualan_display: '',
                akun_kas: '',
                akun_kas_search: '',
                akun_kas_results: [],
                akun_kas_display: '',
                akun_bank: '',
                akun_bank_search: '',
                akun_bank_results: [],
                akun_bank_display: '',
                // Reset field baru
                akun_hpp: '',
                akun_hpp_search: '',
                akun_hpp_results: [],
                akun_hpp_display: '',
                akun_persediaan: '',
                akun_persediaan_search: '',
                akun_persediaan_results: [],
                akun_persediaan_display: ''
            };
        },

        selectCoaAccount(field, account) {
            this.coaForm[field] = account.code;
            this.coaForm[`${field}_display`] = `${account.code} - ${account.name}`;
            this.coaForm[`${field}_search`] = '';
            this.coaForm[`${field}_results`] = [];
            
            // Auto preview after selection
            this.previewCoaJournal();
        },

        async previewCoaJournal() {
            this.coaPreview.loading = true;
            
            try {
                const params = new URLSearchParams({
                    status: 'menunggu',
                    total: '1000000' // Example amount for preview
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.coa-setting.preview")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.coaPreview = {
                        ...data.preview,
                        loading: false
                    };
                } else {
                    this.coaPreview.entries = [];
                    this.coaPreview.loading = false;
                }
            } catch (error) {
                console.error('Error previewing COA journal:', error);
                this.coaPreview.entries = [];
                this.coaPreview.loading = false;
            }
        },

        closeCoaModal() {
            this.showCoaModal = false;
            this.coaForm = {
                accounting_book_id: '',
                akun_piutang_usaha: '',
                akun_piutang_usaha_search: '',
                akun_piutang_usaha_results: [],
                akun_piutang_usaha_display: '',
                akun_pendapatan_penjualan: '',
                akun_pendapatan_penjualan_search: '',
                akun_pendapatan_penjualan_results: [],
                akun_pendapatan_penjualan_display: '',
                akun_kas: '',
                akun_kas_search: '',
                akun_kas_results: [],
                akun_kas_display: '',
                akun_bank: '',
                akun_bank_search: '',
                akun_bank_results: [],
                akun_bank_display: '',
                akun_hpp: '',
                akun_persediaan: ''
            };
        },

        // Ongkir Setting
        showOngkirModal: false,
        showOngkirFormModal: false,
        ongkirData: [],
        ongkirSearch: '',
        editingOngkir: null,
        ongkirForm: {
            id_ongkir: null,
            daerah: '',
            harga: 0,
            outlet_id: '' // Tambahkan ini
        },
        savingOngkir: false,

        async openOngkirSetting() {
            await this.loadOngkirData();
            this.showOngkirModal = true;
        },

        async loadOngkirData() {
            try {
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.ongkir.data")); ?>?${params}`);
                
                console.log('Ongkir response status:', response.status);
                const responseText = await response.text();
                console.log('Ongkir response text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse JSON:', parseError);
                    throw new Error('Invalid JSON response from server');
                }
                
                if (data.data && Array.isArray(data.data)) {
                    // Simpan data untuk availableOngkir (untuk dropdown)
                    this.availableOngkir = data.data.filter(ongkir => ongkir && ongkir.id_ongkir && ongkir.daerah);
                    console.log('Loaded ongkir for dropdown:', this.availableOngkir);
                    
                    // Simpan data untuk tabel ongkirData
                    this.ongkirData = data.data.filter(ongkir => ongkir && ongkir.id_ongkir && ongkir.daerah);
                    console.log('Loaded ongkir for table:', this.ongkirData);
                } else {
                    this.availableOngkir = [];
                    this.ongkirData = [];
                    console.log('No ongkir data found');
                }
            } catch (error) {
                console.error('Error loading ongkir:', error);
                this.showToastMessage('Gagal memuat data ongkos kirim: ' + error.message, 'error');
                this.availableOngkir = [];
                this.ongkirData = [];
            }
        },

        openAddOngkir() {
            this.editingOngkir = null;
            this.ongkirForm = {
                id_ongkir: null,
                daerah: '',
                harga: 0,
                outlet_id: this.selectedOutlet
            };
            this.showOngkirFormModal = true;
        },

        editOngkir(ongkir) {
            this.editingOngkir = ongkir;
            this.ongkirForm = {
                id_ongkir: ongkir.id_ongkir,
                daerah: ongkir.daerah,
                harga: ongkir.harga,
                outlet_id: this.selectedOutlet
            };
            this.showOngkirFormModal = true;
        },

        async saveOngkir() {
            if (!this.ongkirForm.daerah) {
                this.showToastMessage('Daerah harus diisi', 'error');
                return;
            }

            if (!this.ongkirForm.harga || this.ongkirForm.harga <= 0) {
                this.showToastMessage('Harga harus diisi dengan nilai positif', 'error');
                return;
            }

            this.savingOngkir = true;
            try {
                const url = this.editingOngkir 
                    ? `<?php echo e(route("admin.penjualan.ongkir.update", "")); ?>/${this.ongkirForm.id_ongkir}`
                    : '<?php echo e(route("admin.penjualan.ongkir.store")); ?>';

                const method = this.editingOngkir ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(this.ongkirForm)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message, 'success');
                    this.closeOngkirFormModal();
                    await this.loadOngkirData();
                    await this.loadOngkir(); // Reload available ongkir for invoice form
                } else {
                    this.showToastMessage(result.message || 'Gagal menyimpan ongkos kirim', 'error');
                }
            } catch (error) {
                console.error('Error saving ongkir:', error);
                this.showToastMessage('Gagal menyimpan ongkos kirim', 'error');
            } finally {
                this.savingOngkir = false;
            }
        },

        async deleteOngkir(ongkirId) {
            if (!confirm('Hapus data ongkos kirim ini?')) return;

            try {
                const response = await fetch(`<?php echo e(route("admin.penjualan.ongkir.destroy", "")); ?>/${ongkirId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message, 'success');
                    await this.loadOngkirData();
                    await this.loadOngkir(); // Reload available ongkir for invoice form
                } else {
                    this.showToastMessage(result.message || 'Gagal menghapus ongkos kirim', 'error');
                }
            } catch (error) {
                console.error('Error deleting ongkir:', error);
                this.showToastMessage('Gagal menghapus ongkos kirim', 'error');
            }
        },

        closeOngkirModal() {
            this.showOngkirModal = false;
            this.ongkirSearch = '';
        },

        closeOngkirFormModal() {
            this.showOngkirFormModal = false;
            this.editingOngkir = null;
            this.ongkirForm = {
                id_ongkir: null,
                daerah: '',
                harga: 0
            };
        },

        // Customer Price Setting
        showCustomerPriceModal: false,
        showCustomerPriceFormModal: false,
        customerPriceData: [],
        customerPriceSearch: '',
        editingCustomerPrice: null,
        customerPriceForm: {
            id_customer_price: null,
            customer_type: '',
            customer_id: '',
            customer_search: '',
            customer_results: [],
            customer_name: '',
            id_ongkir: '',
            produk: []
        },
        availableProducts: [],
        savingCustomerPrice: false,

        async openCustomerPriceSetting() {
            try {
                console.log('Opening customer price setting for outlet:', this.selectedOutlet);
                
                // Reset form
                this.customerPriceForm = {
                    id_customer_price: null,
                    customer_type: '',
                    customer_id: '',
                    customer_search: '',
                    customer_results: [],
                    customer_name: '',
                    id_ongkir: '',
                    produk: []
                };

                // Load data yang diperlukan dengan outlet_id
                await Promise.all([
                    this.loadCustomerPriceData(),
                    this.loadAvailableProducts(),
                    this.loadOngkirData() // Pastikan ini load dengan outlet_id
                ]);
                
                this.showCustomerPriceModal = true;
            } catch (error) {
                console.error('Error opening customer price setting:', error);
                this.showToastMessage('Gagal membuka setting harga khusus', 'error');
            }
        },

        async loadCustomerPriceData() {
            try {
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet
                });

                console.log('Loading customer price data for outlet:', this.selectedOutlet);

                const response = await fetch(`<?php echo e(route("admin.penjualan.customer-price.data")); ?>?${params}`);
                
                // Debug response
                const responseText = await response.text();
                console.log('Customer price response (first 500 chars):', responseText.substring(0, 500));
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse customer price JSON:', parseError);
                    console.error('Response text:', responseText);
                    throw new Error('Invalid JSON response from server');
                }
                
                console.log('Parsed customer price data:', data);
                
                if (data.data && Array.isArray(data.data)) {
                    this.customerPriceData = data.data.map(item => {
                        console.log('Processing customer price item:', item);
                        return {
                            id_customer_price: item.id_customer_price || item.DT_RowIndex,
                            customer_name: item.customer_name || 'N/A',
                            customer_type: item.customer_type || 'member',
                            ongkos_kirim: item.ongkos_kirim || item.ongkir || '-',
                            produk_list: item.produk_list || item.produk || '<span class="text-slate-500">Tidak ada produk</span>',
                            // Simpan data asli untuk edit
                            _raw: item
                        };
                    });
                    console.log('Loaded customer prices:', this.customerPriceData.length, 'items');
                    console.log('Sample data:', this.customerPriceData[0]);
                } else {
                    this.customerPriceData = [];
                    console.log('No customer price data found or invalid format');
                }
            } catch (error) {
                console.error('Error loading customer price data:', error);
                this.showToastMessage('Gagal memuat data harga khusus: ' + error.message, 'error');
                this.customerPriceData = [];
            }
        },

        async loadAvailableProducts() {
            try {
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet
                });

                const response = await fetch(`<?php echo e(route("admin.penjualan.produk.harga-normal")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.availableProducts = data.produks || [];
                    console.log('Loaded available products:', this.availableProducts.length);
                } else {
                    this.availableProducts = [];
                    console.log('No available products found');
                }
            } catch (error) {
                console.error('Error loading available products:', error);
                this.availableProducts = [];
            }
        },

        openAddCustomerPrice() {
            this.editingCustomerPrice = null;
            this.customerPriceForm = {
                id_customer_price: null,
                customer_type: '',
                customer_id: '',
                customer_search: '',
                customer_results: [],
                customer_name: '',
                id_ongkir: '',
                produk: []
            };
            this.showCustomerPriceFormModal = true;
        },

        async searchCustomerPriceCustomers() {
            if (!this.customerPriceForm.customer_search) {
                this.customerPriceForm.customer_results = [];
                return;
            }

            try {
                const params = new URLSearchParams({
                    search: this.customerPriceForm.customer_search,
                    outlet_id: this.selectedOutlet // PASTIKAN kirim selectedOutlet
                });

                console.log('Searching customers for outlet:', this.selectedOutlet);

                const response = await fetch(`<?php echo e(route('admin.penjualan.customers')); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.customerPriceForm.customer_results = data.customers || [];
                    console.log('Found customers:', this.customerPriceForm.customer_results);
                } else {
                    this.customerPriceForm.customer_results = [];
                    console.log('No customers found');
                }
            } catch (error) {
                console.error('Error searching customers:', error);
                this.customerPriceForm.customer_results = [];
            }
        },

        selectCustomerPriceCustomer(customer) {
            this.customerPriceForm.customer_type = customer.type;
            this.customerPriceForm.customer_id = customer.id;
            this.customerPriceForm.customer_name = customer.nama;
            this.customerPriceForm.customer_search = customer.nama;
            this.customerPriceForm.customer_results = [];
        },

        async searchCustomerPriceProducts(item, index) {
            if (!item.product_search) {
                item.product_results = [];
                return;
            }

            try {
                const params = new URLSearchParams({
                    search: item.product_search,
                    outlet_id: this.selectedOutlet // PASTIKAN kirim selectedOutlet
                });

                console.log('Searching products for outlet:', this.selectedOutlet);

                const response = await fetch(`<?php echo e(route("admin.penjualan.produk.harga-normal")); ?>?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    item.product_results = data.produks || [];
                    console.log('Found products:', item.product_results);
                } else {
                    item.product_results = [];
                    console.log('No products found');
                }
            } catch (error) {
                console.error('Error searching products for customer price:', error);
                item.product_results = [];
            }
        },

        // Method untuk select product di customer price
        selectCustomerPriceProduct(product, item, index) {
            item.id_produk = product.id_produk;
            item.nama_produk = product.nama_produk;
            item.harga_normal = product.harga;
            item.harga_khusus = product.harga; // Default sama dengan harga normal
            item.product_search = product.nama_produk;
            item.product_results = [];
        },

        // Method untuk add product form yang baru
        addCustomerPriceProduct() {
            this.customerPriceForm.produk.push({
                id_produk: '',
                nama_produk: '',
                harga_normal: 0,
                harga_khusus: 0,
                product_search: '',
                product_results: []
            });
        },

        onCustomerPriceProductSelect(product, index) {
            const selectedProduct = this.availableProducts.find(p => p.id_produk == product.id_produk);
            if (selectedProduct) {
                // Update product info
                product.nama_produk = selectedProduct.nama_produk;
                product.harga_normal = selectedProduct.harga;
                
                // Set default special price if not already set
                if (!product.harga_khusus || product.harga_khusus === 0) {
                    product.harga_khusus = selectedProduct.harga;
                }
            } else {
                // Reset if product not found
                product.nama_produk = '';
                product.harga_normal = 0;
                product.harga_khusus = 0;
            }
        },

        removeCustomerPriceProduct(index) {
            this.customerPriceForm.produk.splice(index, 1);
        },

        async saveCustomerPrice() {
            // Validation
            if (!this.customerPriceForm.customer_id) {
                this.showToastMessage('Customer harus dipilih', 'error');
                return;
            }

            if (!this.customerPriceForm.id_ongkir) {
                this.showToastMessage('Ongkos kirim harus dipilih', 'error');
                return;
            }

            // Filter out empty products
            const validProducts = this.customerPriceForm.produk.filter(p => p.id_produk && p.id_produk !== '');
            
            if (validProducts.length === 0) {
                this.showToastMessage('Minimal satu produk harus ditambahkan', 'error');
                return;
            }

            // Validate each product
            for (const product of validProducts) {
                if (!product.harga_khusus || product.harga_khusus <= 0) {
                    this.showToastMessage('Harga khusus harus diisi dengan nilai positif untuk semua produk', 'error');
                    return;
                }
            }

            this.savingCustomerPrice = true;
            try {
                const url = this.editingCustomerPrice 
                    ? `<?php echo e(route("admin.penjualan.customer-price.update", "")); ?>/${this.customerPriceForm.id_customer_price}`
                    : '<?php echo e(route("admin.penjualan.customer-price.store")); ?>';

                const method = this.editingCustomerPrice ? 'PUT' : 'POST';

                // Prepare data for API
                const requestData = {
                    outlet_id: this.selectedOutlet,
                    customer_type: this.customerPriceForm.customer_type,
                    customer_id: this.customerPriceForm.customer_id,
                    id_ongkir: this.customerPriceForm.id_ongkir,
                    produk: validProducts.map(p => p.id_produk),
                    harga_khusus_produk: validProducts.map(p => parseFloat(p.harga_khusus))
                };

                console.log('=== SENDING CUSTOMER PRICE DATA ===');
                console.log('URL:', url);
                console.log('Method:', method);
                console.log('Data:', requestData);
                console.log('Selected outlet:', this.selectedOutlet);

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response status:', response.status);
                
                // Get response text first for debugging
                const responseText = await response.text();
                console.log('Response text:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse JSON response:', parseError);
                    throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 200));
                }

                if (response.ok && result.success) {
                    console.log('Customer price saved successfully');
                    this.showToastMessage(result.message, 'success');
                    this.closeCustomerPriceFormModal();
                    await this.loadCustomerPriceData();
                } else {
                    console.error('Customer price save failed:', result);
                    throw new Error(result.message || result.errors || 'Gagal menyimpan harga khusus');
                }
            } catch (error) {
                console.error('Error saving customer price:', error);
                this.showToastMessage('Gagal menyimpan harga khusus: ' + error.message, 'error');
            } finally {
                this.savingCustomerPrice = false;
            }
        },

        //const response = await fetch(`<?php echo e(route('admin.penjualan.customer-price.edit', ':id')); ?>`.replace(':id', customerPrice.id_customer_price));

        async editCustomerPrice(customerPrice) {
            try {
                console.log('=== EDIT CUSTOMER PRICE ===');
                console.log('Input customerPrice:', customerPrice);
                
                const response = await fetch(`<?php echo e(route('admin.penjualan.customer-price.edit', ':id')); ?>`.replace(':id', customerPrice.id_customer_price));
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                console.log('API Response:', result);
                console.log('API Response success:', result.success);
                console.log('API Response data:', result.data);
                
                if (!result.success) {
                    throw new Error(result.message || 'Response tidak sukses');
                }

                // Gunakan data langsung dari response
                const data = result.data || result;
                
                console.log('Data to process:', data);
                console.log('Data.produk:', data.produk);
                console.log('Is produk array?', Array.isArray(data.produk));
                console.log('Produk length:', data.produk ? data.produk.length : 'undefined');
                
                this.editingCustomerPrice = customerPrice;
                
                // DAPATKAN NAMA CUSTOMER DARI BERBAGAI SUMBER
                let customerName = '';
                
                // Coba dari berbagai sumber
                if (data.customer && data.customer.nama && data.customer.nama !== 'N/A') {
                    customerName = data.customer.nama;
                } else if (data.customer_name && data.customer_name !== 'N/A') {
                    customerName = data.customer_name;
                } else if (customerPrice.customer_name && customerPrice.customer_name !== 'N/A') {
                    // Gunakan dari data tabel
                    customerName = customerPrice.customer_name;
                } else {
                    // Fallback: cari dari available data
                    customerName = 'Customer ' + data.customer_id;
                }
                
                console.log('Customer Name:', customerName);
                
                // Build form data secara eksplisit
                this.customerPriceForm = {
                    id_customer_price: customerPrice.id_customer_price,
                    customer_type: data.customer_type || '',
                    customer_id: data.customer_id || '',
                    customer_search: customerName,
                    customer_results: [],
                    customer_name: customerName,
                    id_ongkir: data.id_ongkir || '',
                    produk: []
                };

                // Handle produk secara terpisah dengan logging detail
                console.log('Processing produk data:', data.produk);
                
                if (data.produk && Array.isArray(data.produk) && data.produk.length > 0) {
                    this.customerPriceForm.produk = data.produk.map((p, index) => {
                        console.log(`Product ${index}:`, p);
                        const productForm = {
                            id_produk: p.id_produk || '',
                            nama_produk: p.nama_produk || '',
                            product_search: p.nama_produk || '', // ✅ Tambahkan ini untuk ditampilkan di input
                            product_results: [], // ✅ Tambahkan ini untuk search results
                            harga_normal: p.harga_jual || 0,
                            harga_khusus: (p.pivot && p.pivot.harga_khusus) ? p.pivot.harga_khusus : 0
                        };
                        console.log(`Mapped product ${index}:`, productForm);
                        return productForm;
                    }).filter(p => p.id_produk);
                    
                    console.log('Final produk array:', this.customerPriceForm.produk);
                } else {
                    console.log('No produk data found, adding empty form');
                    this.customerPriceForm.produk.push(this.getEmptyProductForm());
                }

                // Jika tidak ada produk setelah filter, tambahkan form kosong
                if (this.customerPriceForm.produk.length === 0) {
                    console.log('Produk array empty after filter, adding empty form');
                    this.customerPriceForm.produk.push(this.getEmptyProductForm());
                }

                console.log('=== FINAL FORM DATA ===');
                console.log('Form:', this.customerPriceForm);
                console.log('Produk count:', this.customerPriceForm.produk.length);
                
                this.showCustomerPriceFormModal = true;
                
            } catch (error) {
                console.error('Error loading customer price for edit:', error);
                console.error('Error stack:', error.stack);
                this.showToastMessage('Gagal memuat data harga khusus: ' + error.message, 'error');
            }
        },

        debugResponseStructure(data) {
            console.group('=== DEBUG RESPONSE STRUCTURE ===');
            console.log('Full response:', data);
            console.log('Success:', data.success);
            console.log('Data property:', data.data);
            console.log('Direct properties:', Object.keys(data));
            
            if (data.data) {
                console.log('Data keys:', Object.keys(data.data));
                console.log('Produk in data:', data.data.produk);
                console.log('Customer in data:', data.data.customer);
            } else {
                console.log('Direct produk:', data.produk);
                console.log('Direct customer:', data.customer);
            }
            console.groupEnd();
        },

        // Helper method untuk form produk kosong
        getEmptyProductForm() {
            return {
                id_produk: '',
                nama_produk: '',
                harga_normal: 0,
                harga_khusus: 0
            };
        },

        async deleteCustomerPrice(customerPriceId) {
            if (!confirm('Hapus data harga khusus customer ini?')) return;

            try {
                const response = await fetch(`<?php echo e(route("admin.penjualan.customer-price.destroy", "")); ?>/${customerPriceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message, 'success');
                    await this.loadCustomerPriceData();
                } else {
                    this.showToastMessage(result.message || 'Gagal menghapus harga khusus', 'error');
                }
            } catch (error) {
                console.error('Error deleting customer price:', error);
                this.showToastMessage('Gagal menghapus harga khusus', 'error');
            }
        },

        closeCustomerPriceModal() {
            this.showCustomerPriceModal = false;
            this.customerPriceSearch = '';
        },

        closeCustomerPriceFormModal() {
            this.showCustomerPriceFormModal = false;
            this.editingCustomerPrice = null;
            this.customerPriceForm = {
                id_customer_price: null,
                customer_type: '',
                customer_id: '',
                customer_search: '',
                customer_results: [],
                customer_name: '',
                id_ongkir: '',
                produk: []
            };
        },

        // Method untuk apply diskon manual
        applyDiscount(item, index) {
            const diskonAmount = parseFloat(item.diskon) || 0;
            
            // Pastikan diskon tidak melebihi harga normal
            if (diskonAmount > item.harga) {
                item.diskon = item.harga;
                this.showToastMessage('Diskon tidak boleh melebihi harga normal', 'error');
            } else if (diskonAmount < 0) {
                item.diskon = 0;
                this.showToastMessage('Diskon tidak boleh negatif', 'error');
            }
            
            // Hitung harga khusus berdasarkan diskon
            item.harga_khusus = item.harga - item.diskon;
            
            this.calculateItemSubtotal(item, index);
        },

        // Hitung total subtotal (tanpa diskon)
        calculateSubtotal() {
            return this.invoiceForm.items.reduce((total, item) => {
                return total + (parseFloat(item.subtotal) || 0);
            }, 0);
        },

        // Hitung total diskon
        calculateTotalDiscount() {
            return this.invoiceForm.items.reduce((total, item) => {
                return total + ((parseFloat(item.diskon) || 0) * (parseFloat(item.kuantitas) || 0));
            }, 0);
        },

        // Hitung grand total
        calculateGrandTotal() {
            const subtotal = this.calculateSubtotal();
            const totalDiscount = this.calculateTotalDiscount();
            return subtotal - totalDiscount;
        },

        calculateTotalDebit(entries) {
            if (!entries || !Array.isArray(entries)) return 0;
            return entries.reduce((total, entry) => total + (parseFloat(entry.debit) || 0), 0);
        },

        calculateTotalCredit(entries) {
            if (!entries || !Array.isArray(entries)) return 0;
            return entries.reduce((total, entry) => total + (parseFloat(entry.credit) || 0), 0);
        },

        async onInvoiceFormOutletChange() {
            // No longer regenerate invoice number when outlet changes
            // Invoice number will be generated after save
            if (this.invoiceForm.id_outlet && !this.editingInvoice) {
                this.invoiceForm.no_invoice = '(Akan digenerate otomatis setelah simpan)';
            }
        },

        onPaymentTypeChange() {
            if (this.paymentForm.jenis_pembayaran === 'cash') {
                // Reset form transfer untuk cash
                this.paymentForm.nama_bank = '';
                this.paymentForm.nama_pengirim = '';
                this.paymentForm.jumlah_transfer = this.paymentForm.total;
                this.paymentForm.bukti_transfer_file = null;
                this.paymentForm.bukti_transfer_preview = null;
                this.paymentForm.bukti_transfer_preview_type = null;
                this.paymentForm.bukti_transfer_name = '';
                
                if (this.$refs.buktiTransferInput) {
                    this.$refs.buktiTransferInput.value = '';
                }
            } else if (this.paymentForm.jenis_pembayaran === 'transfer') {
                // Set default untuk transfer
                this.paymentForm.jumlah_transfer = this.paymentForm.total;
            }
        },

        // Method untuk handle upload bukti transfer
        onBuktiTransferChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validasi file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.showToastMessage('Ukuran file maksimal 2MB', 'error');
                event.target.value = '';
                return;
            }

            // Validasi file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                this.showToastMessage('Format file harus JPG, PNG, atau PDF', 'error');
                event.target.value = '';
                return;
            }

            this.paymentForm.bukti_transfer_file = file;
            this.paymentForm.bukti_transfer_name = file.name;

            // Generate preview untuk image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.paymentForm.bukti_transfer_preview = e.target.result;
                    this.paymentForm.bukti_transfer_preview_type = 'image';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                this.paymentForm.bukti_transfer_preview = null;
                this.paymentForm.bukti_transfer_preview_type = 'pdf';
            }
        },

        // Method validasi form pembayaran
        isPaymentFormValid() {
            if (!this.paymentForm.jenis_pembayaran || !this.paymentForm.penerima || !this.paymentForm.tanggal_pembayaran) {
                return false;
            }

            if (this.paymentForm.jenis_pembayaran === 'transfer') {
                if (!this.paymentForm.nama_bank || !this.paymentForm.nama_pengirim || !this.paymentForm.jumlah_transfer || !this.paymentForm.bukti_transfer_file) {
                    return false;
                }
            }

            return true;
        },

        // Method untuk view bukti transfer
        async viewBuktiTransfer(invoice) {
            this.currentBuktiTransferInvoice = invoice;
            this.loadingBuktiTransfer = true;
            this.showBuktiTransferModal = true;
            
            try {
                // Determine file type dari extension
                const fileExt = invoice.bukti_transfer.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                    this.buktiTransferType = 'image';
                    this.buktiTransferUrl = `<?php echo e(route('admin.penjualan.invoice.view-bukti-transfer', ':id')); ?>?t=${Date.now()}`.replace(':id', invoice.id_sales_invoice);
                } else if (fileExt === 'pdf') {
                    this.buktiTransferType = 'pdf';
                    this.buktiTransferUrl = null;
                }
            } catch (error) {
                console.error('Error loading bukti transfer:', error);
                this.showToastMessage('Gagal memuat bukti transfer', 'error');
            } finally {
                this.loadingBuktiTransfer = false;
            }
        },

        // Method untuk download bukti transfer
        downloadBuktiTransfer() {
            if (this.currentBuktiTransferInvoice) {
                const downloadUrl = `<?php echo e(route('admin.penjualan.invoice.download-bukti-transfer', ':id')); ?>`.replace(':id', this.currentBuktiTransferInvoice.id_sales_invoice);
                window.open(downloadUrl, '_blank');
            }
        },
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\invoice\index.blade.php ENDPATH**/ ?>