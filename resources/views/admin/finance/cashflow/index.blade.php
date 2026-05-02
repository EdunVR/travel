{{-- resources/views/admin/finance/cashflow/index.blade.php --}}
<x-layouts.admin :title="'Laporan Arus Kas'">
  <div x-data="cashFlowManagement()" x-init="init()" class="space-y-6">

    {{-- Account Transaction Details Modal --}}
    <div x-show="showAccountModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeAccountModal()"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                  <i class='bx bx-detail text-2xl text-white'></i>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-white">Detail Transaksi Akun</h3>
                  <p class="text-sm text-blue-100" x-show="accountDetails.account">
                    <span x-text="accountDetails.account?.code"></span> - <span x-text="accountDetails.account?.name"></span>
                  </p>
                </div>
              </div>
              <button @click="closeAccountModal()" class="text-white hover:text-blue-100 transition-colors">
                <i class='bx bx-x text-3xl'></i>
              </button>
            </div>
          </div>

          <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            <div x-show="isLoadingAccountDetails" class="text-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p class="text-slate-600">Memuat detail transaksi...</p>
            </div>

            <div x-show="!isLoadingAccountDetails && accountDetails.transactions">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4">
                  <div class="text-sm text-blue-600 mb-1">Total Transaksi</div>
                  <div class="text-2xl font-bold text-blue-700" x-text="accountDetails.summary?.total_transactions || 0"></div>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                  <div class="text-sm text-green-600 mb-1">Total Debit</div>
                  <div class="text-lg font-bold text-green-700" x-text="formatCurrency(accountDetails.summary?.total_debit || 0)"></div>
                </div>
                <div class="bg-red-50 rounded-xl p-4">
                  <div class="text-sm text-red-600 mb-1">Total Kredit</div>
                  <div class="text-lg font-bold text-red-700" x-text="formatCurrency(accountDetails.summary?.total_credit || 0)"></div>
                </div>
                <div class="bg-purple-50 rounded-xl p-4">
                  <div class="text-sm text-purple-600 mb-1">Arus Kas Bersih</div>
                  <div class="text-lg font-bold text-purple-700" x-text="formatCurrency(accountDetails.summary?.net_cash_flow || 0)"></div>
                </div>
              </div>

              <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                      <th class="px-4 py-3 text-left font-semibold text-slate-700">Tanggal</th>
                      <th class="px-4 py-3 text-left font-semibold text-slate-700">No. Transaksi</th>
                      <th class="px-4 py-3 text-left font-semibold text-slate-700">Deskripsi</th>
                      <th class="px-4 py-3 text-left font-semibold text-slate-700">Buku</th>
                      <th class="px-4 py-3 text-right font-semibold text-slate-700">Debit</th>
                      <th class="px-4 py-3 text-right font-semibold text-slate-700">Kredit</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template x-if="accountDetails.transactions && accountDetails.transactions.length === 0">
                      <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                          <i class='bx bx-info-circle text-3xl mb-2'></i>
                          <p>Tidak ada transaksi untuk akun ini dalam periode yang dipilih</p>
                        </td>
                      </tr>
                    </template>
                    <template x-for="transaction in accountDetails.transactions" :key="transaction.id">
                      <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700" x-text="formatDate(transaction.transaction_date)"></td>
                        <td class="px-4 py-3">
                          <span class="font-mono text-xs text-slate-600" x-text="transaction.transaction_number"></span>
                        </td>
                        <td class="px-4 py-3 text-slate-700" x-text="transaction.description"></td>
                        <td class="px-4 py-3">
                          <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded" x-text="transaction.book_name"></span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-green-600 font-semibold" x-show="transaction.debit > 0" x-text="formatCurrency(transaction.debit)"></span>
                          <span class="text-slate-400" x-show="transaction.debit === 0">-</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                          <span class="text-red-600 font-semibold" x-show="transaction.credit > 0" x-text="formatCurrency(transaction.credit)"></span>
                          <span class="text-slate-400" x-show="transaction.credit === 0">-</span>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                  <tfoot x-show="accountDetails.transactions && accountDetails.transactions.length > 0" class="bg-slate-50 border-t-2 border-slate-300">
                    <tr class="font-semibold">
                      <td colspan="4" class="px-4 py-3 text-right text-slate-700">Total:</td>
                      <td class="px-4 py-3 text-right text-green-600" x-text="formatCurrency(accountDetails.summary?.total_debit || 0)"></td>
                      <td class="px-4 py-3 text-right text-red-600" x-text="formatCurrency(accountDetails.summary?.total_credit || 0)"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div x-show="accountDetailsError" class="text-center py-12">
              <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-error-circle text-3xl text-red-600'></i>
              </div>
              <p class="text-slate-700 font-semibold mb-2">Gagal Memuat Data</p>
              <p class="text-slate-600 text-sm" x-text="accountDetailsError"></p>
            </div>
          </div>

          <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
            <button @click="closeAccountModal()" 
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Laporan Arus Kas</h1>
        <p class="text-slate-600 text-sm">Analisis arus kas masuk dan keluar dari aktivitas operasi, investasi, dan pendanaan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Export Dropdown --}}
        <div x-data="{ showExportMenu: false }" class="relative">
          <button @click="showExportMenu = !showExportMenu" 
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
            <i class='bx bx-download'></i> Export
            <i class='bx bx-chevron-down text-sm'></i>
          </button>
          <div x-show="showExportMenu" 
               @click.away="showExportMenu = false"
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="transform opacity-100 scale-100"
               x-transition:leave-end="transform opacity-0 scale-95"
               class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg border border-slate-200 py-1 z-10"
               style="display: none;">
            <button @click="exportPDF(); showExportMenu = false" 
                    class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
              <i class='bx bx-file-blank text-red-600'></i>
              <span>Export PDF</span>
            </button>
            <button @click="exportExcel(); showExportMenu = false" 
                    class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2">
              <i class='bx bx-spreadsheet text-green-600'></i>
              <span>Export Excel</span>
            </button>
          </div>
        </div>
        
        <button @click="refreshData()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
          <i class='bx bx-refresh'></i> Refresh
        </button>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="onOutletChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Pilih Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Buku Akuntansi</label>
          <select x-model="filters.book_id" @change="loadCashFlowData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Buku</option>
            <template x-for="book in books" :key="book.id">
              <option :value="book.id" x-text="book.name"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Metode Laporan</label>
          <select x-model="filters.method" @change="loadCashFlowData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="direct">Langsung (Direct)</option>
            <option value="indirect">Tidak Langsung (Indirect)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
          <select x-model="filters.period" @change="updateDateRange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="monthly">Bulanan</option>
            <option value="quarterly">Triwulan</option>
            <option value="yearly">Tahunan</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadCashFlowData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadCashFlowData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    {{-- Cash Flow Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-trending-up text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" :class="cashFlowStats.netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="formatCurrency(cashFlowStats.netCashFlow)"></div>
            <div class="text-sm text-slate-600">Arus Kas Bersih</div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs" :class="cashFlowStats.netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'">
          <i :class="cashFlowStats.netCashFlow >= 0 ? 'bx bx-up-arrow-alt' : 'bx bx-down-arrow-alt'"></i>
          <span x-text="cashFlowStats.netCashFlow >= 0 ? 'Positif' : 'Negatif'"></span>
          <span class="text-slate-500">periode ini</span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-building-house text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" :class="cashFlowStats.operatingCash >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="formatCurrency(cashFlowStats.operatingCash)"></div>
            <div class="text-sm text-slate-600">Kas dari Operasi</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Aktivitas operasional
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-line-chart text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" :class="cashFlowStats.investingCash >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="formatCurrency(cashFlowStats.investingCash)"></div>
            <div class="text-sm text-slate-600">Kas dari Investasi</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Aktivitas investasi
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-orange-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" :class="cashFlowStats.financingCash >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="formatCurrency(cashFlowStats.financingCash)"></div>
            <div class="text-sm text-slate-600">Kas dari Pendanaan</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Aktivitas pendanaan
        </div>
      </div>
    </div>

    {{-- Cash Flow Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Cash Flow Trend --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Trend Arus Kas</h3>
          <select x-model="trendPeriod" @change="updateTrendChart()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
            <option value="6">6 Bulan</option>
            <option value="12">1 Tahun</option>
          </select>
        </div>
        <div class="h-64 relative">
          <canvas id="cashFlowTrendChart" x-ref="cashFlowTrendChart" style="max-height: 256px;"></canvas>
        </div>
      </div>

      {{-- Cash Flow Composition --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Komposisi Arus Kas</h3>
          <span class="text-sm text-slate-500" x-text="filters.period"></span>
        </div>
        <div class="h-64 relative">
          <canvas id="cashFlowCompositionChart" x-ref="cashFlowCompositionChart" style="max-height: 256px;"></canvas>
        </div>
      </div>
    </div>

    {{-- Cash Flow Statement --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Laporan Arus Kas</h2>
          <p class="text-sm text-slate-600">
            Periode: <span x-text="filters.start_date"></span> s/d <span x-text="filters.end_date"></span> 
            - Metode: <span x-text="filters.method === 'direct' ? 'Langsung' : 'Tidak Langsung'"></span>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-slate-500">Dalam Rupiah</span>
        </div>
      </div>

      {{-- Operating Activities - Direct -- FIXED VERSION --}}
<div x-show="filters.method === 'direct'">
    {{-- Operating Activities --}}
    <div class="border-b border-slate-200">
        <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">A. Arus Kas dari Aktivitas Operasi</h3>
        </div>
        <div class="px-6 py-4 space-y-2">
            <template x-for="item in directCashFlow.operating" :key="item.id">
                <div>
                    {{-- Header item (seperti Penerimaan Kas dari Pelanggan) --}}
                    <template x-if="item.is_header && item.children && item.children.length > 0">
                        <div class="py-2">
                            <div class="font-semibold text-slate-800 mb-2" x-text="item.name"></div>
                            {{-- Render children langsung tanpa parent --}}
                            <div class="space-y-1 ml-4">
                                <template x-for="child in item.children" :key="child.id">
                                    <div class="flex justify-between items-center py-1 hover:bg-slate-50 rounded px-2"
                                         :style="'padding-left: ' + ((child.level - 1) * 20) + 'px'">
                                        <div class="flex items-center gap-2">
                                            <button x-show="child.account_id" 
                                                    @click="showAccountTransactions(child.account_id, child.code, child.name)"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer text-left"
                                                    x-text="child.name"></button>
                                            <span x-show="!child.account_id" 
                                                  class="text-slate-700"
                                                  x-text="child.name"></span>
                                            <span x-show="child.code" 
                                                  class="text-xs text-slate-400 font-mono"
                                                  x-text="child.code"></span>
                                        </div>
                                        <div class="font-medium" 
                                             :class="child.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                                             x-text="formatCurrency(child.amount)"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Item tanpa children (leaf node) --}}
                    <template x-if="(!item.children || item.children.length === 0) && !item.is_header">
                        <div class="flex justify-between items-center py-2 hover:bg-slate-50 rounded px-2">
                            <div class="flex items-center gap-2">
                                <button x-show="item.account_id" 
                                        @click="showAccountTransactions(item.account_id, item.code, item.name)"
                                        class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer text-left"
                                        x-text="item.name"></button>
                                <span x-show="!item.account_id" 
                                      class="text-slate-700"
                                      x-text="item.name"></span>
                                <span x-show="item.code" 
                                      class="text-xs text-slate-400 font-mono"
                                      x-text="item.code"></span>
                            </div>
                            <div class="font-medium" 
                                 :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                                 x-text="formatCurrency(item.amount)"></div>
                        </div>
                    </template>
                </div>
            </template>
            
            <div class="border-t border-slate-200 pt-4 mt-4">
                <div class="flex justify-between items-center font-semibold">
                    <span>Kas Bersih yang Dihasilkan dari Aktivitas Operasi</span>
                    <span :class="directCashFlow.netOperating >= 0 ? 'text-green-600' : 'text-red-600'" 
                          x-text="formatCurrency(directCashFlow.netOperating)"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Investing Activities --}}
    <div class="border-b border-slate-200">
        <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">B. Arus Kas dari Aktivitas Investasi</h3>
        </div>
        <div class="px-6 py-4 space-y-2">
            <template x-for="item in directCashFlow.investing" :key="item.id">
                <div class="flex justify-between items-center py-2 hover:bg-slate-50 rounded px-2">
                    <div class="flex items-center gap-2">
                        <button x-show="item.account_id" 
                                @click="showAccountTransactions(item.account_id, item.code, item.name)"
                                class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer text-left"
                                x-text="item.name || item.description"></button>
                        <span x-show="!item.account_id" 
                              class="text-slate-700"
                              x-text="item.name || item.description"></span>
                        <span x-show="item.code" 
                              class="text-xs text-slate-400 font-mono"
                              x-text="item.code"></span>
                    </div>
                    <div class="font-medium" 
                         :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                         x-text="formatCurrency(item.amount)"></div>
                </div>
            </template>
            <div class="border-t border-slate-200 pt-4 mt-4">
                <div class="flex justify-between items-center font-semibold">
                    <span>Kas Bersih yang Digunakan untuk Aktivitas Investasi</span>
                    <span :class="directCashFlow.netInvesting >= 0 ? 'text-green-600' : 'text-red-600'" 
                          x-text="formatCurrency(directCashFlow.netInvesting)"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Financing Activities --}}
    <div class="border-b border-slate-200">
        <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">C. Arus Kas dari Aktivitas Pendanaan</h3>
        </div>
        <div class="px-6 py-4 space-y-2">
            <template x-for="item in directCashFlow.financing" :key="item.id">
                <div class="flex justify-between items-center py-2 hover:bg-slate-50 rounded px-2">
                    <div class="flex items-center gap-2">
                        <button x-show="item.account_id" 
                                @click="showAccountTransactions(item.account_id, item.code, item.name)"
                                class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer text-left"
                                x-text="item.name || item.description"></button>
                        <span x-show="!item.account_id" 
                              class="text-slate-700"
                              x-text="item.name || item.description"></span>
                        <span x-show="item.code" 
                              class="text-xs text-slate-400 font-mono"
                              x-text="item.code"></span>
                    </div>
                    <div class="font-medium" 
                         :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                         x-text="formatCurrency(item.amount)"></div>
                </div>
            </template>
            <div class="border-t border-slate-200 pt-4 mt-4">
                <div class="flex justify-between items-center font-semibold">
                    <span>Kas Bersih yang Diperoleh dari Aktivitas Pendanaan</span>
                    <span :class="directCashFlow.netFinancing >= 0 ? 'text-green-600' : 'text-red-600'" 
                          x-text="formatCurrency(directCashFlow.netFinancing)"></span>
                </div>
            </div>
        </div>
    </div>
</div>

      {{-- Indirect Method --}}
      <div x-show="filters.method === 'indirect'">
        {{-- Operating Activities - Indirect --}}
        <div class="border-b border-slate-200">
          <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">A. Arus Kas dari Aktivitas Operasi</h3>
          </div>
          <div class="px-6 py-4 space-y-3">
            <div class="flex justify-between items-center font-semibold">
              <span>Laba Bersih</span>
              <span class="text-green-600" x-text="formatCurrency(indirectCashFlow.netIncome)"></span>
            </div>
            
            <div class="pl-4 space-y-2">
              <div class="text-sm font-medium text-slate-700">Penyesuaian untuk merekonsiliasi laba bersih menjadi kas bersih dari aktivitas operasi:</div>
              
              <template x-for="item in indirectCashFlow.adjustments" :key="item.id">
                <div class="flex justify-between items-center hover:bg-slate-50">
                  <div class="flex items-center gap-2">
                    <button x-show="item.account_id" 
                            @click="showAccountTransactions(item.account_id, item.code, item.description)"
                            class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                            x-text="item.description"></button>
                    <span x-show="!item.account_id" class="text-slate-600" x-text="item.description"></span>
                    <span x-show="item.note" class="text-xs text-slate-400" x-text="'(' + item.note + ')'"></span>
                  </div>
                  <div class="font-semibold" :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                        x-text="formatCurrency(item.amount)"></div>
                </div>
              </template>
            </div>

            <div class="border-t border-slate-200 pt-3">
              <div class="flex justify-between items-center font-semibold">
                <span>Kas Bersih yang Dihasilkan dari Aktivitas Operasi</span>
                <span :class="indirectCashFlow.netOperating >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(indirectCashFlow.netOperating)"></span>
              </div>
            </div>
          </div>
        </div>

        {{-- Investing Activities (Same as Direct) --}}
        <div class="border-b border-slate-200">
          <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">B. Arus Kas dari Aktivitas Investasi</h3>
          </div>
          <div class="px-6 py-4 space-y-3">
            <template x-for="item in indirectCashFlow.investing" :key="item.id">
              <div class="flex justify-between items-center hover:bg-slate-50">
                <div class="flex items-center gap-2">
                  <button x-show="item.account_id" 
                          @click="showAccountTransactions(item.account_id, item.code, item.name || item.description)"
                          class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                          x-text="item.name || item.description"></button>
                  <span x-show="!item.account_id" class="text-slate-600" x-text="item.name || item.description"></span>
                  <span x-show="item.note" class="text-xs text-slate-400" x-text="'(' + item.note + ')'"></span>
                </div>
                <div class="font-semibold" :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(item.amount)"></div>
              </div>
            </template>
            <div class="border-t border-slate-200 pt-3">
              <div class="flex justify-between items-center font-semibold">
                <span>Kas Bersih yang Digunakan untuk Aktivitas Investasi</span>
                <span :class="indirectCashFlow.netInvesting >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(indirectCashFlow.netInvesting)"></span>
              </div>
            </div>
          </div>
        </div>

        {{-- Financing Activities (Same as Direct) --}}
        <div class="border-b border-slate-200">
          <div class="bg-slate-50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">C. Arus Kas dari Aktivitas Pendanaan</h3>
          </div>
          <div class="px-6 py-4 space-y-3">
            <template x-for="item in indirectCashFlow.financing" :key="item.id">
              <div class="flex justify-between items-center hover:bg-slate-50">
                <div class="flex items-center gap-2">
                  <button x-show="item.account_id" 
                          @click="showAccountTransactions(item.account_id, item.code, item.name || item.description)"
                          class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                          x-text="item.name || item.description"></button>
                  <span x-show="!item.account_id" class="text-slate-600" x-text="item.name || item.description"></span>
                  <span x-show="item.note" class="text-xs text-slate-400" x-text="'(' + item.note + ')'"></span>
                </div>
                <div class="font-semibold" :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(item.amount)"></div>
              </div>
            </template>
            <div class="border-t border-slate-200 pt-3">
              <div class="flex justify-between items-center font-semibold">
                <span>Kas Bersih yang Diperoleh dari Aktivitas Pendanaan</span>
                <span :class="indirectCashFlow.netFinancing >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(indirectCashFlow.netFinancing)"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Net Cash Flow --}}
      <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
        <div class="flex justify-between items-center">
          <span class="font-semibold text-blue-800">Kenaikan (Penurunan) Bersih dalam Kas dan Setara Kas</span>
          <span class="text-xl font-bold" :class="cashFlowStats.netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'" 
                x-text="formatCurrency(cashFlowStats.netCashFlow)"></span>
        </div>
      </div>

      {{-- Cash at Beginning and End --}}
      <div class="px-6 py-4">
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-slate-600">Kas dan Setara Kas pada Awal Periode</span>
            <span class="font-semibold text-blue-600" x-text="formatCurrency(cashFlowStats.cashAtBeginning)"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-slate-600">Kas dan Setara Kas pada Akhir Periode</span>
            <span class="font-semibold text-blue-600" x-text="formatCurrency(cashFlowStats.cashAtEnd)"></span>
          </div>
        </div>
      </div>
    </div>

    {{-- Cash Flow Analysis --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Cash Flow Ratios --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Rasio Arus Kas</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                <i class='bx bx-line-chart text-green-600'></i>
              </div>
              <div>
                <div class="font-medium text-slate-800">Operating Cash Flow Ratio</div>
                <div class="text-xs text-slate-500">Kas dari operasi / Kewajiban lancar</div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-semibold text-green-600" x-text="cashFlowRatios.operatingRatio"></div>
              <div class="text-xs text-slate-500">Healthy: >1.0</div>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class='bx bx-money text-blue-600'></i>
              </div>
              <div>
                <div class="font-medium text-slate-800">Cash Flow Margin</div>
                <div class="text-xs text-slate-500">Kas dari operasi / Pendapatan</div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-semibold text-blue-600" x-text="cashFlowRatios.cashFlowMargin + '%'"></div>
              <div class="text-xs text-slate-500">Industry avg: 15%</div>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class='bx bx-trending-up text-purple-600'></i>
              </div>
              <div>
                <div class="font-medium text-slate-800">Free Cash Flow</div>
                <div class="text-xs text-slate-500">Kas dari operasi - Capex</div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-semibold text-purple-600" x-text="formatCurrency(cashFlowRatios.freeCashFlow)"></div>
              <div class="text-xs" :class="cashFlowRatios.freeCashFlow >= 0 ? 'text-green-600' : 'text-red-600'">
                <span x-text="cashFlowRatios.freeCashFlow >= 0 ? 'Positive' : 'Negative'"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Cash Flow Forecast --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Proyeksi Arus Kas</h3>
          <span class="text-sm text-slate-500">3 Bulan Mendatang</span>
        </div>
        <div class="space-y-4">
          <template x-for="forecast in cashFlowForecast" :key="forecast.month">
            <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                  <i class='bx bx-calendar text-orange-600'></i>
                </div>
                <div>
                  <div class="font-medium text-slate-800" x-text="forecast.month"></div>
                  <div class="text-xs text-slate-500" x-text="forecast.period"></div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-semibold" :class="forecast.amount >= 0 ? 'text-green-600' : 'text-red-600'" 
                      x-text="formatCurrency(forecast.amount)"></div>
                <div class="text-xs text-slate-500" x-text="forecast.trend + ' vs previous'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

  </div>

  <script>
    function cashFlowManagement() {
      return {
        isLoading: false,
        error: null,
        outlets: [],
        books: [],
        filters: {
          outlet_id: '',
          book_id: '',
          method: 'direct',
          period: 'monthly',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0]
        },
        trendPeriod: '6',
        cashFlowData: null,
        cashFlowStats: {
          netCashFlow: 0,
          operatingCash: 0,
          investingCash: 0,
          financingCash: 0,
          cashAtBeginning: 0,
          cashAtEnd: 0
        },
        directCashFlow: {
          operating: [],
          investing: [],
          financing: [],
          netOperating: 0,
          netInvesting: 0,
          netFinancing: 0
        },
        indirectCashFlow: {
          netIncome: 0,
          adjustments: [],
          operating: [],
          investing: [],
          financing: [],
          netOperating: 0,
          netInvesting: 0,
          netFinancing: 0
        },
        cashFlowRatios: {
          operatingRatio: '0.00',
          cashFlowMargin: '0.0',
          freeCashFlow: 0
        },
        cashFlowForecast: [],
        expandedItems: {},
        trendChart: null,
        compositionChart: null,
        showAccountModal: false,
        isLoadingAccountDetails: false,
        accountDetails: {
          account: null,
          transactions: [],
          summary: null
        },
        accountDetailsError: null,

        async init() {
          window.cashFlowApp = this; // Make globally accessible
          await this.loadOutlets();
          if (this.outlets.length > 0) {
            this.filters.outlet_id = this.outlets[0].id_outlet;
            await this.loadBooks();
            await this.loadCashFlowData();
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch('{{ route('finance.outlets.data') }}');
            const result = await response.json();
            if (result.success) {
              this.outlets = result.data;
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },

        async loadBooks() {
          if (!this.filters.outlet_id) return;
          
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id
            });
            const response = await fetch(`{{ route('finance.active-books.data') }}?${params}`);
            const result = await response.json();
            if (result.success) {
              this.books = result.data;
            }
          } catch (error) {
            console.error('Error loading books:', error);
          }
        },

        async onOutletChange() {
          this.filters.book_id = '';
          await this.loadBooks();
          await this.loadCashFlowData();
        },

        async loadCashFlowData() {
            if (!this.filters.outlet_id) {
                this.error = 'Pilih outlet terlebih dahulu';
                return;
            }
            
            this.isLoading = true;
            this.error = null;

            try {
                const params = new URLSearchParams({
                    outlet_id: this.filters.outlet_id,
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                    method: this.filters.method
                });
                
                if (this.filters.book_id) {
                    params.append('book_id', this.filters.book_id);
                }

                const response = await fetch(`{{ route('finance.cashflow.data') }}?${params}`);
                const result = await response.json();

                if (result.success) {
                    this.cashFlowData = result.data;
                    this.cashFlowStats = result.data.stats;
                    
                    // Update direct cash flow - menggunakan data asli tanpa flatten
                    this.directCashFlow.operating = result.data.operating.items || [];
                    this.directCashFlow.investing = result.data.investing.items || [];
                    this.directCashFlow.financing = result.data.financing.items || [];
                    this.directCashFlow.netOperating = result.data.operating.total || 0;
                    this.directCashFlow.netInvesting = result.data.investing.total || 0;
                    this.directCashFlow.netFinancing = result.data.financing.total || 0;
                    
                    // Update indirect cash flow
                    if (result.data.operating.net_income !== undefined) {
                        this.indirectCashFlow.netIncome = result.data.operating.net_income || 0;
                        this.indirectCashFlow.adjustments = result.data.operating.adjustments || [];
                        this.indirectCashFlow.netOperating = result.data.operating.total || 0;
                        this.indirectCashFlow.investing = result.data.investing.items || [];
                        this.indirectCashFlow.financing = result.data.financing.items || [];
                        this.indirectCashFlow.netInvesting = result.data.investing.total || 0;
                        this.indirectCashFlow.netFinancing = result.data.financing.total || 0;
                    }
                    
                    // Update ratios
                    if (result.data.ratios) {
                        this.cashFlowRatios = result.data.ratios;
                    }
                    
                    // Update forecast
                    if (result.data.forecast) {
                        this.cashFlowForecast = result.data.forecast;
                    }
                    
                    // Update charts dengan delay untuk memastikan DOM ready
                    this.$nextTick(() => {
                        setTimeout(() => {
                            const trendData = result.data.trend || {
                                labels: [],
                                operating: [],
                                investing: [],
                                financing: []
                            };
                            this.initCharts(trendData);
                        }, 100);
                    });
                } else {
                    this.error = result.message || 'Gagal memuat data arus kas';
                }
            } catch (error) {
                console.error('Error loading cash flow:', error);
                this.error = 'Terjadi kesalahan saat memuat data';
            } finally {
                this.isLoading = false;
            }
        },

        flattenItems(items, parentLevel = 0) {
          let flattened = [];
          items.forEach(item => {
            flattened.push(item);
            if (item.children && item.children.length > 0) {
              flattened = flattened.concat(this.flattenItems(item.children, item.level));
            }
          });
          return flattened;
        },

        toggleItem(itemId) {
          this.expandedItems[itemId] = !this.expandedItems[itemId];
        },

        isExpanded(itemId) {
          return this.expandedItems[itemId] === true;
        },

        renderChildren(children) {
          let html = '';
          children.forEach(child => {
            const paddingLeft = child.level * 20;
            const amountClass = child.amount >= 0 ? 'text-green-600' : 'text-red-600';
            const hasChildren = child.children && child.children.length > 0;
            const isClickable = child.account_id;
            
            // If has children, show parent name only (no amount)
            if (hasChildren) {
              html += `<div class="py-1" style="padding-left: ${paddingLeft}px">`;
              html += `<span class="font-semibold text-slate-700">${child.name}</span>`;
              html += `</div>`;
              // Recursively render children
              html += this.renderChildren(child.children);
            } else {
              // Leaf node: show with amount and clickable
              html += `<div class="flex justify-between items-center py-1 hover:bg-slate-50" style="padding-left: ${paddingLeft}px">`;
              html += `<div class="flex items-center gap-2">`;
              
              if (isClickable) {
                html += `<button onclick="window.cashFlowApp.showAccountTransactions(${child.account_id}, '${child.code || ''}', '${child.name}')" class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">${child.name}</button>`;
              } else {
                html += `<span class="text-slate-600">${child.name}</span>`;
              }
              
              if (child.code) {
                html += `<span class="text-xs text-slate-400">(${child.code})</span>`;
              }
              html += `</div>`;
              html += `<div class="${amountClass}">${this.formatCurrency(child.amount)}</div>`;
              html += `</div>`;
            }
          });
          return html;
        },



        updateDateRange() {
          const now = new Date();
          let startDate, endDate;

          switch (this.filters.period) {
            case 'monthly':
              startDate = new Date(now.getFullYear(), now.getMonth(), 1);
              endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0);
              break;
            case 'quarterly':
              const quarter = Math.floor(now.getMonth() / 3);
              startDate = new Date(now.getFullYear(), quarter * 3, 1);
              endDate = new Date(now.getFullYear(), (quarter + 1) * 3, 0);
              break;
            case 'yearly':
              startDate = new Date(now.getFullYear(), 0, 1);
              endDate = new Date(now.getFullYear(), 11, 31);
              break;
            default:
              return;
          }

          this.filters.start_date = startDate.toISOString().split('T')[0];
          this.filters.end_date = endDate.toISOString().split('T')[0];
          this.loadCashFlowData();
        },

        initCharts(trendData) {
          // Check if Chart.js is loaded
          if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded');
            return;
          }

          // Destroy existing charts safely
          try {
            if (this.trendChart && typeof this.trendChart.destroy === 'function') {
              this.trendChart.destroy();
              this.trendChart = null;
            }
            if (this.compositionChart && typeof this.compositionChart.destroy === 'function') {
              this.compositionChart.destroy();
              this.compositionChart = null;
            }
          } catch (e) {
            console.warn('Error destroying charts:', e);
          }

          // Cash Flow Trend Chart
          try {
            const trendCtx = this.$refs.cashFlowTrendChart;
            if (trendCtx && trendData && trendData.labels && trendData.labels.length > 0) {
              const ctx = trendCtx.getContext('2d');
              if (ctx) {
                this.trendChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                    labels: trendData.labels || [],
                    datasets: [
                      {
                        label: 'Operasi',
                        data: trendData.operating || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                      },
                      {
                        label: 'Investasi',
                        data: trendData.investing || [],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                      },
                      {
                        label: 'Pendanaan',
                        data: trendData.financing || [],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                      }
                    ]
                  },
                  options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false, // Disable animation to prevent errors
                    plugins: {
                      legend: {
                        position: 'top',
                      }
                    },
                    scales: {
                      y: {
                        beginAtZero: false,
                        ticks: {
                          callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                          }
                        }
                      }
                    }
                  }
                });
              }
            }
          } catch (e) {
            console.error('Error creating trend chart:', e);
          }

          // Cash Flow Composition Chart
          try {
            const compositionCtx = this.$refs.cashFlowCompositionChart;
            if (compositionCtx) {
              const operatingAbs = Math.abs(this.cashFlowStats.operatingCash || 0);
              const investingAbs = Math.abs(this.cashFlowStats.investingCash || 0);
              const financingAbs = Math.abs(this.cashFlowStats.financingCash || 0);
              
              // Only create chart if there's data
              if (operatingAbs > 0 || investingAbs > 0 || financingAbs > 0) {
                const ctx = compositionCtx.getContext('2d');
                if (ctx) {
                  this.compositionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                      labels: ['Operasi', 'Investasi', 'Pendanaan'],
                      datasets: [{
                        data: [operatingAbs, investingAbs, financingAbs],
                        backgroundColor: [
                          '#10b981',
                          '#8b5cf6',
                          '#f59e0b'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                      }]
                    },
                    options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      animation: false, // Disable animation to prevent errors
                      plugins: {
                        legend: {
                          position: 'bottom'
                        },
                        tooltip: {
                          callbacks: {
                            label: (context) => {
                              const value = context.raw;
                              const actualValue = context.dataIndex === 0 ? this.cashFlowStats.operatingCash :
                                                context.dataIndex === 1 ? this.cashFlowStats.investingCash :
                                                this.cashFlowStats.financingCash;
                              const sign = actualValue >= 0 ? '+' : '-';
                              return `${context.label}: ${sign}${this.formatCurrency(Math.abs(actualValue))}`;
                            }
                          }
                        }
                      }
                    }
                  });
                }
              }
            }
          } catch (e) {
            console.error('Error creating composition chart:', e);
          }
        },

        updateTrendChart() {
          // Reload data with new trend period
          this.loadCashFlowData();
        },

        formatDate(dateString) {
          if (!dateString) return '-';
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        async showAccountTransactions(accountId, accountCode, accountName) {
          this.showAccountModal = true;
          this.isLoadingAccountDetails = true;
          this.accountDetailsError = null;
          this.accountDetails = {
            account: { code: accountCode, name: accountName },
            transactions: [],
            summary: null
          };

          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id,
              start_date: this.filters.start_date,
              end_date: this.filters.end_date
            });
            
            if (this.filters.book_id) {
              params.append('book_id', this.filters.book_id);
            }

            // Check if this is "Pembelian Aset Tetap" - use special endpoint
            let url;
            if (accountName && accountName.toLowerCase().includes('pembelian aset tetap')) {
              url = `{{ route('finance.cashflow.fixed-asset-purchases') }}?${params}`;
            } else {
              url = `{{ route('finance.cashflow.account-details', '') }}/${accountId}?${params}`;
            }
            
            const response = await fetch(url);
            
            if (!response.ok) {
              const result = await response.json();
              this.accountDetailsError = result.message || 'Gagal memuat detail transaksi';
              return;
            }
            
            const result = await response.json();
            if (result.success) {
              this.accountDetails = result.data;
            } else {
              this.accountDetailsError = result.message || 'Gagal memuat detail transaksi';
            }
          } catch (error) {
            console.error('Error loading account details:', error);
            this.accountDetailsError = 'Terjadi kesalahan saat memuat detail transaksi';
          } finally {
            this.isLoadingAccountDetails = false;
          }
        },

        closeAccountModal() {
          this.showAccountModal = false;
          this.accountDetails = {
            account: null,
            transactions: [],
            summary: null
          };
          this.accountDetailsError = null;
        },

        exportPDF() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }

          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            method: this.filters.method
          });
          
          if (this.filters.book_id) {
            params.append('book_id', this.filters.book_id);
          }

          window.open(`{{ route('finance.cashflow.export.pdf') }}?${params}`, '_blank');
        },

        exportExcel() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }

          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            start_date: this.filters.start_date,
            end_date: this.filters.end_date,
            method: this.filters.method
          });
          
          if (this.filters.book_id) {
            params.append('book_id', this.filters.book_id);
          }

          window.location.href = `{{ route('finance.cashflow.export.xlsx') }}?${params}`;
        },

        refreshData() {
          this.loadCashFlowData();
        },

        formatCurrency(amount) {
          const absAmount = Math.abs(amount);
          const formatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(absAmount);
          
          // Show negative values in parentheses (accounting format)
          return amount < 0 ? `(${formatted})` : formatted;
        }
      };
    }
  </script>
</x-layouts.admin>
