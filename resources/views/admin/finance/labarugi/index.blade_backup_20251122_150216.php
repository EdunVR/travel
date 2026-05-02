{{-- resources/views/admin/finance/labarugi/index.blade.php --}}
<x-layouts.admin :title="'Laporan Laba Rugi YY'">
  <div x-data="profitLossManagement()" x-init="init()" class="space-y-6">

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
        {{-- Background overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeAccountModal()"></div>

        {{-- Modal panel --}}
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          {{-- Modal Header --}}
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

          {{-- Modal Body --}}
          <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
            {{-- Loading State --}}
            <div x-show="isLoadingAccountDetails" class="text-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p class="text-slate-600">Memuat detail transaksi...</p>
            </div>

            {{-- Content --}}
            <div x-show="!isLoadingAccountDetails && accountDetails.transactions">
              {{-- Summary Cards --}}
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4">
                  <div class="text-sm text-blue-600 mb-1">Total Transaksi</div>
                  <div class="text-2xl font-bold text-blue-700" x-text="accountDetails.summary?.transaction_count || 0"></div>
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
                  <div class="text-sm text-purple-600 mb-1">Saldo</div>
                  <div class="text-lg font-bold text-purple-700" x-text="formatCurrency(accountDetails.summary?.total_amount || 0)"></div>
                </div>
              </div>

              {{-- Transactions Table --}}
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
                      <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template x-if="accountDetails.transactions && accountDetails.transactions.length === 0">
                      <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                          <i class='bx bx-info-circle text-3xl mb-2'></i>
                          <p>Tidak ada transaksi untuk akun ini dalam periode yang dipilih</p>
                        </td>
                      </tr>
                    </template>
                    <template x-for="(transaction, index) in accountDetails.transactions" :key="transaction.id">
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
                        <td class="px-4 py-3 text-center">
                          <a :href="`{{ route('finance.jurnal.index') }}?search=${transaction.transaction_number}`" 
                             target="_blank"
                             class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-xs">
                            <i class='bx bx-link-external'></i>
                            <span>Lihat Jurnal</span>
                          </a>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                  <tfoot x-show="accountDetails.transactions && accountDetails.transactions.length > 0" class="bg-slate-50 border-t-2 border-slate-300">
                    <tr class="font-semibold">
                      <td colspan="4" class="px-4 py-3 text-right text-slate-700">Total:</td>
                      <td class="px-4 py-3 text-right text-green-600" x-text="formatCurrency(accountDetails.summary?.total_debit || 0)"></td>
                      <td class="px-4 py-3 text-right text-red-600" x-text="formatCurrency(accountDetails.summary?.total_credit || 0)"></td>
                      <td class="px-4 py-3"></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            {{-- Error State --}}
            <div x-show="accountDetailsError" class="text-center py-12">
              <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-error-circle text-3xl text-red-600'></i>
              </div>
              <p class="text-slate-700 font-semibold mb-2">Gagal Memuat Data</p>
              <p class="text-slate-600 text-sm" x-text="accountDetailsError"></p>
            </div>
          </div>

          {{-- Modal Footer --}}
          <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3">
            <button @click="closeAccountModal()" 
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Print Only Header --}}
    <div class="print-only-header">
      <div style="text-align: center;">
        <h1 style="font-size: 18pt; font-weight: bold; margin: 0 0 5px 0;">LAPORAN LABA RUGI</h1>
        <h2 style="font-size: 14pt; font-weight: normal; margin: 0 0 10px 0;" x-text="profitLossData.period?.outlet_name || 'Semua Outlet'"></h2>
        <p style="font-size: 11pt; margin: 0;">
          Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
        </p>
        <p x-show="filters.comparison && profitLossData.comparison.enabled" style="font-size: 10pt; margin: 5px 0 0 0; font-style: italic;">
          Perbandingan dengan periode: <span x-text="formatDate(filters.comparison_start_date)"></span> s/d <span x-text="formatDate(filters.comparison_end_date)"></span>
        </p>
        <p style="font-size: 9pt; margin: 10px 0 0 0; color: #666;">
          Dicetak pada: <span x-text="new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })"></span>
        </p>
      </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Laporan Laba Rugi</h1>
        <p class="text-slate-600 text-sm">Laporan kinerja keuangan perusahaan dalam periode tertentu</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" 
                  :disabled="isExporting"
                  :class="isExporting ? 'opacity-50 cursor-not-allowed' : ''"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx' :class="isExporting ? 'bx-loader-alt animate-spin' : 'bx-export'"></i>
            <span x-text="isExporting ? 'Mengekspor...' : 'Export'"></span>
            <i class='bx bx-chevron-down text-sm' x-show="!isExporting"></i>
          </button>
          
          <div x-show="exportOpen" 
               @click.away="exportOpen = false"
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="transform opacity-100 scale-100"
               x-transition:leave-end="transform opacity-0 scale-95"
               class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportToXLSX(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i>
              <span>Export ke XLSX</span>
            </button>
            <button @click="exportToPDF(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl border-t border-slate-100">
              <i class='bx bxs-file-pdf text-red-600'></i>
              <span>Export ke PDF</span>
            </button>
          </div>
        </div>

        <button @click="printReport()" 
                :disabled="isLoading"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700 disabled:opacity-50">
          <i class='bx bx-printer'></i> Print
        </button>
        
        <button @click="refreshData()" 
                :disabled="isLoading" 
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Outlet *</label>
          <select x-model="filters.outlet_id" @change="onOutletChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Pilih Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
          <select x-model="filters.period" @change="onPeriodChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="monthly">Bulan Ini</option>
            <option value="last_month">Bulan Lalu</option>
            <option value="quarterly">Kuartal Ini</option>
            <option value="yearly">Tahun Ini</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadProfitLossData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadProfitLossData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        
        <div class="flex items-end">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" x-model="filters.comparison" @change="toggleComparison()" class="rounded border-slate-300">
            <span class="text-sm text-slate-700">Mode Perbandingan</span>
          </label>
        </div>
      </div>
      
      {{-- Comparison Date Range --}}
      <div x-show="filters.comparison" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-200">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai Pembanding</label>
          <input type="date" x-model="filters.comparison_start_date" @change="loadProfitLossData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir Pembanding</label>
          <input type="date" x-model="filters.comparison_end_date" @change="loadProfitLossData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data laporan laba rugi...</p>
    </div>

    {{-- Error Message --}}
    <div x-show="error" 
         x-transition
         class="rounded-xl bg-red-50 border border-red-200 p-4 shadow-sm">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <i class='bx bx-error-circle text-2xl text-red-600'></i>
        </div>
        <div class="flex-1">
          <h4 class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</h4>
          <p class="text-sm text-red-700" x-text="error"></p>
        </div>
        <button @click="error = null" class="flex-shrink-0 text-red-400 hover:text-red-600">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
    </div>

    {{-- Main Content --}}
    <div x-show="!isLoading && !error" class="space-y-6">

      {{-- Summary Cards --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Revenue Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
              <i class='bx bx-trending-up text-2xl text-green-600'></i>
            </div>
            <div class="flex-1">
              <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(profitLossData.summary?.total_revenue || 0)"></div>
              <div class="text-sm text-slate-600">Total Pendapatan</div>
            </div>
          </div>
          <div x-show="filters.comparison && profitLossData.comparison?.enabled" class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx' :class="(profitLossData.comparison?.changes?.revenue_change || 0) >= 0 ? 'bx-up-arrow-alt text-green-500' : 'bx-down-arrow-alt text-red-500'"></i>
            <span :class="(profitLossData.comparison?.changes?.revenue_change || 0) >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="Math.abs(profitLossData.comparison?.changes?.revenue_change_percent || 0).toFixed(2) + '%'"></span>
            <span class="text-slate-500">vs periode sebelumnya</span>
          </div>
        </div>

        {{-- Total Expense Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
              <i class='bx bx-trending-down text-2xl text-red-600'></i>
            </div>
            <div class="flex-1">
              <div class="text-2xl font-bold text-red-600" x-text="formatCurrency(profitLossData.summary?.total_expense || 0)"></div>
              <div class="text-sm text-slate-600">Total Beban</div>
            </div>
          </div>
          <div x-show="filters.comparison && profitLossData.comparison?.enabled" class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx' :class="(profitLossData.comparison?.changes?.expense_change || 0) >= 0 ? 'bx-up-arrow-alt text-red-500' : 'bx-down-arrow-alt text-green-500'"></i>
            <span :class="(profitLossData.comparison?.changes?.expense_change || 0) >= 0 ? 'text-red-600' : 'text-green-600'" 
                  x-text="Math.abs(profitLossData.comparison?.changes?.expense_change_percent || 0).toFixed(2) + '%'"></span>
            <span class="text-slate-500">vs periode sebelumnya</span>
          </div>
        </div>

        {{-- Net Income Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <i class='bx bx-line-chart text-2xl text-blue-600'></i>
            </div>
            <div class="flex-1">
              <div class="text-2xl font-bold" 
                   :class="(profitLossData.summary?.net_income || 0) >= 0 ? 'text-blue-600' : 'text-orange-600'" 
                   x-text="formatCurrency(profitLossData.summary?.net_income || 0)"></div>
              <div class="text-sm text-slate-600">Laba/Rugi Bersih</div>
            </div>
          </div>
          <div x-show="filters.comparison && profitLossData.comparison?.enabled" class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx' :class="(profitLossData.comparison?.changes?.net_income_change || 0) >= 0 ? 'bx-up-arrow-alt text-green-500' : 'bx-down-arrow-alt text-red-500'"></i>
            <span :class="(profitLossData.comparison?.changes?.net_income_change || 0) >= 0 ? 'text-green-600' : 'text-red-600'" 
                  x-text="Math.abs(profitLossData.comparison?.changes?.net_income_change_percent || 0).toFixed(2) + '%'"></span>
            <span class="text-slate-500">vs periode sebelumnya</span>
          </div>
        </div>

        {{-- Profit Margin Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
              <i class='bx bx-pie-chart-alt-2 text-2xl text-purple-600'></i>
            </div>
            <div class="flex-1">
              <div class="text-2xl font-bold text-purple-600" 
                   x-text="profitLossData.summary?.net_profit_margin !== null && profitLossData.summary?.net_profit_margin !== undefined ? profitLossData.summary.net_profit_margin.toFixed(2) + '%' : 'N/A'"></div>
              <div class="text-sm text-slate-600">Net Profit Margin</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="profitLossData.summary?.gross_profit_margin !== null && profitLossData.summary?.gross_profit_margin !== undefined ? 'GPM: ' + profitLossData.summary.gross_profit_margin.toFixed(2) + '%' : 'GPM: N/A'"></span>
          </div>
        </div>
      </div>


      {{-- Charts Section --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Pie Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-800">Komposisi Pendapatan</h3>
            <span class="text-sm text-slate-500" x-text="formatCurrency(profitLossData.summary.total_revenue)"></span>
          </div>
          <div class="h-64 chart-container">
            <template x-if="!chartsLoaded">
              <div class="chart-loading">
                <div class="text-center text-slate-500">
                  <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                  <div class="text-sm">Memuat grafik...</div>
                </div>
              </div>
            </template>
            <canvas id="revenuePieChart" x-ref="revenuePieChart" x-show="chartsLoaded"></canvas>
          </div>
        </div>

        {{-- Expense Pie Chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-800">Komposisi Beban</h3>
            <span class="text-sm text-slate-500" x-text="formatCurrency(profitLossData.summary.total_expense)"></span>
          </div>
          <div class="h-64 chart-container">
            <template x-if="!chartsLoaded">
              <div class="chart-loading">
                <div class="text-center text-slate-500">
                  <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                  <div class="text-sm">Memuat grafik...</div>
                </div>
              </div>
            </template>
            <canvas id="expensePieChart" x-ref="expensePieChart" x-show="chartsLoaded"></canvas>
          </div>
        </div>
      </div>

      {{-- Revenue vs Expense Bar Chart --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Perbandingan Pendapatan vs Beban</h3>
        </div>
        <div class="h-64 chart-container">
          <template x-if="!chartsLoaded">
            <div class="chart-loading">
              <div class="text-center text-slate-500">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <div class="text-sm">Memuat grafik...</div>
              </div>
            </div>
          </template>
          <canvas id="comparisonBarChart" x-ref="comparisonBarChart" x-show="chartsLoaded"></canvas>
        </div>
      </div>

      {{-- Trend Line Chart (comparison mode) --}}
      <div x-show="filters.comparison && profitLossData.comparison.enabled" x-transition class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Tren Laba/Rugi Bersih</h3>
        </div>
        <div class="h-64 chart-container">
          <canvas id="trendLineChart" x-ref="trendLineChart"></canvas>
        </div>
      </div>

      {{-- Profit & Loss Statement Table --}}
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden print-section">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200 print-header">
          <div>
            <h2 class="text-lg font-semibold text-slate-800">Laporan Laba Rugi</h2>
            <p class="text-sm text-slate-600">
              Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
            </p>
            <p class="text-sm text-slate-600" x-show="profitLossData.period?.outlet_name">
              Outlet: <span x-text="profitLossData.period?.outlet_name"></span>
            </p>
          </div>
          <div class="flex items-center gap-4">
            <div class="text-right">
              <div class="text-sm text-slate-600">Total Pendapatan</div>
              <div class="text-lg font-bold text-green-600" x-text="formatCurrency(profitLossData.summary.total_revenue)"></div>
            </div>
            <div class="text-right">
              <div class="text-sm text-slate-600">Total Beban</div>
              <div class="text-lg font-bold text-red-600" x-text="formatCurrency(profitLossData.summary.total_expense)"></div>
            </div>
            <div class="text-right">
              <div class="text-sm text-slate-600">Laba/Rugi Bersih</div>
              <div class="text-lg font-bold" :class="profitLossData.summary.net_income >= 0 ? 'text-blue-600' : 'text-orange-600'" x-text="formatCurrency(profitLossData.summary.net_income)"></div>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm profit-loss-table" x-html="renderProfitLossTable()"></table>
        </div>
      </div>

    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && !error && isDataEmpty()" 
         x-transition
         class="rounded-2xl border border-slate-200 bg-white p-12 shadow-card text-center">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-line-chart text-4xl text-slate-400'></i>
      </div>
      <h3 class="text-xl font-semibold text-slate-800 mb-2">Tidak Ada Data</h3>
      <p class="text-slate-600 mb-1">Tidak ditemukan transaksi untuk periode yang dipilih.</p>
      <p class="text-sm text-slate-500 mb-6">
        Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button @click="refreshData()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-6 py-2.5 hover:bg-blue-700">
          <i class='bx bx-refresh'></i> Muat Ulang Data
        </button>
        <button @click="filters.period = 'monthly'; onPeriodChange()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-2.5 hover:bg-slate-50">
          <i class='bx bx-calendar'></i> Coba Periode Lain
        </button>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  
  <script>
    function profitLossManagement() {
      return {
        // CONTINUE WITH EXISTING JAVASCRIPT CODE
        {{-- PENDAPATAN --}}
              <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                  <td colspan="6" class="px-4 py-3">PENDAPATAN</td>
              </tr>
              <template x-for="account in (profitLossData.revenue?.accounts || [])" :key="account.id">
                  <tr class="border-t border-slate-100 hover:bg-slate-50">
                      <td class="px-4 py-2 border-r border-slate-100">
                          <span class="font-mono text-xs font-semibold text-slate-700" x-text="account.code || '-'"></span>
                      </td>
                      <td class="px-4 py-2 border-r border-slate-100">
                          <div class="flex items-center gap-2">
                              <button x-show="account.children && account.children.length > 0" 
                                      @click="toggleAccountDetails(account.id)"
                                      class="text-slate-400 hover:text-slate-600">
                                  <i class='bx text-sm' :class="expandedAccounts.includes(account.id) ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
                              </button>
                              <button @click="showAccountTransactions(account)" 
                                      class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1"
                                      :title="'Klik untuk melihat detail transaksi'">
                                  <span class="font-semibold text-slate-800" x-text="account.name || 'Unnamed Account'"></span>
                                  <i class='bx bx-info-circle text-xs opacity-50'></i>
                              </button>
                          </div>
                      </td>
                          <td class="px-4 py-2 text-right border-r border-slate-100">
                              <span class="font-semibold text-green-600" x-text="formatCurrency(account.amount)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                              <span x-text="formatCurrency(getComparisonAmount(account, 'revenue'))"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                              <span :class="(account.amount - getComparisonAmount(account, 'revenue')) >= 0 ? 'text-green-600' : 'text-red-600'" 
                                    x-text="formatCurrency(account.amount - getComparisonAmount(account, 'revenue'))"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                              <span x-text="calculateChange(account.amount, getComparisonAmount(account, 'revenue'))"></span>
                          </td>
                      </tr>
                      
                      {{-- Child Accounts --}}
                      <template x-if="expandedAccounts.includes(account.id) && account.children && account.children.length > 0">
                          <template x-for="child in account.children" :key="child.id">
                              <tr class="border-t border-slate-50 bg-slate-25 hover:bg-slate-50">
                                  <td class="px-4 py-2 pl-8 border-r border-slate-100">
                                      <span class="font-mono text-xs text-slate-500" x-text="child.code || '-'"></span>
                                  </td>
                                  <td class="px-4 py-2 border-r border-slate-100">
                                      <button @click="showAccountTransactions(child)" 
                                              class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1 text-sm"
                                              :title="'Klik untuk melihat detail transaksi'">
                                          <span class="text-slate-600" x-text="child.name || 'Unnamed Account'"></span>
                                          <i class='bx bx-info-circle text-xs opacity-50'></i>
                                      </button>
                                  </td>
                                  <td class="px-4 py-2 text-right border-r border-slate-100">
                                      <span class="text-sm text-green-600" x-text="formatCurrency(child.amount)"></span>
                                  </td>
                                  <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                                      <span class="text-sm" x-text="formatCurrency(getComparisonAmount(child, 'revenue'))"></span>
                                  </td>
                                  <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                                      <span class="text-sm" :class="(child.amount - getComparisonAmount(child, 'revenue')) >= 0 ? 'text-green-600' : 'text-red-600'" 
                                            x-text="formatCurrency(child.amount - getComparisonAmount(child, 'revenue'))"></span>
                                  </td>
                                  <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                                      <span class="text-sm" x-text="calculateChange(child.amount, getComparisonAmount(child, 'revenue'))"></span>
                                  </td>
                              </tr>
                          </template>
                      </template>
                  </template>
              </template>

              <template x-if="!profitLossData.revenue?.accounts || profitLossData.revenue.accounts.length === 0">
                  <tr class="border-t border-slate-100">
                      <td colspan="6" class="px-4 py-4 text-center text-slate-500">
                          <i class='bx bx-info-circle text-lg mb-1'></i>
                          <p>Tidak ada data pendapatan</p>
                      </td>
                  </tr>
              </template>
              <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Pendapatan</td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-green-600" x-text="formatCurrency(profitLossData.revenue?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency(profitLossData.comparison?.revenue?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span :class="(profitLossData.comparison?.changes?.revenue_change || 0) >= 0 ? 'text-green-600' : 'text-red-600'" 
                        x-text="formatCurrency(profitLossData.comparison?.changes?.revenue_change || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right">
                  <span x-text="calculateChange(profitLossData.revenue?.total || 0, profitLossData.comparison?.revenue?.total || 0)"></span>
                </td>
              </tr>

              {{-- PENDAPATAN LAIN-LAIN --}}
              <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                <td colspan="6" class="px-4 py-3">PENDAPATAN LAIN-LAIN</td>
              </tr>
              <template x-for="account in (profitLossData.other_revenue?.accounts || [])" :key="account.id">
                  <template>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                      <td class="px-4 py-2 border-r border-slate-100">
                        <span class="font-mono text-xs font-semibold text-slate-700" x-text="account.code || '-'"></span>
                      </td>
                      <td class="px-4 py-2 border-r border-slate-100">
                        <div class="flex items-center gap-2">
                          <button x-show="account.children && account.children.length > 0" 
                                  @click="toggleAccountDetails(account.id)"
                                  class="text-slate-400 hover:text-slate-600">
                            <i class='bx text-sm' :class="expandedAccounts.includes(account.id) ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
                          </button>
                          <button @click="showAccountTransactions(account)" 
                                  class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1"
                                  :title="'Klik untuk melihat detail transaksi'">
                            <span class="font-semibold text-slate-800" x-text="account.name || 'Unnamed Account'"></span>
                            <i class='bx bx-info-circle text-xs opacity-50'></i>
                          </button>
                        </div>
                      </td>
                      <td class="px-4 py-2 text-right border-r border-slate-100">
                        <span class="font-semibold text-green-600" x-text="formatCurrency(account.amount)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span x-text="formatCurrency(account.comparison_amount || 0)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span :class="(account.amount - (account.comparison_amount || 0)) >= 0 ? 'text-green-600' : 'text-red-600'" 
                              x-text="formatCurrency(account.amount - (account.comparison_amount || 0))"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                        <span x-text="calculateChange(account.amount, account.comparison_amount || 0)"></span>
                      </td>
                    </tr>
                    {{-- Child Accounts --}}
                    <template x-if="expandedAccounts.includes(account.id) && account.children && account.children.length > 0">
                      <template x-for="child in account.children" :key="child.id">
                        <tr class="border-t border-slate-50 bg-slate-25 hover:bg-slate-50">
                          <td class="px-4 py-2 pl-8 border-r border-slate-100">
                            <span class="font-mono text-xs text-slate-500" x-text="child.code || '-'"></span>
                          </td>
                          <td class="px-4 py-2 border-r border-slate-100">
                            <button @click="showAccountTransactions(child)" 
                                    class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1 text-sm"
                                    :title="'Klik untuk melihat detail transaksi'">
                              <span class="text-slate-600" x-text="child.name || 'Unnamed Account'"></span>
                              <i class='bx bx-info-circle text-xs opacity-50'></i>
                            </button>
                          </td>
                          <td class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm text-green-600" x-text="formatCurrency(child.amount)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" x-text="formatCurrency(child.comparison_amount || 0)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" :class="(child.amount - (child.comparison_amount || 0)) >= 0 ? 'text-green-600' : 'text-red-600'" 
                                  x-text="formatCurrency(child.amount - (child.comparison_amount || 0))"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                            <span class="text-sm" x-text="calculateChange(child.amount, child.comparison_amount || 0)"></span>
                          </td>
                        </tr>
                      </template>
                    </template>
                  </template>
                </template>
              </template>
              <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Pendapatan Lain-Lain</td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-green-600" x-text="formatCurrency(profitLossData.other_revenue?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency(profitLossData.comparison?.other_revenue?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency((profitLossData.other_revenue?.total || 0) - (profitLossData.comparison?.other_revenue?.total || 0))"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right">
                  <span x-text="calculateChange(profitLossData.other_revenue?.total || 0, profitLossData.comparison?.other_revenue?.total || 0)"></span>
                </td>
              </tr>

              {{-- TOTAL PENDAPATAN --}}
              <tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
                <td colspan="2" class="px-4 py-3 text-right border-r border-slate-300">TOTAL PENDAPATAN</td>
                <td class="px-4 py-3 text-right border-r border-slate-300">
                  <span class="text-green-600" x-text="formatCurrency(profitLossData.summary.total_revenue)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right border-r border-slate-300">
                  <span x-text="formatCurrency((profitLossData.comparison.revenue?.total || 0) + (profitLossData.comparison.other_revenue?.total || 0))"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right border-r border-slate-300">
                  <span :class="profitLossData.comparison.changes?.revenue_change >= 0 ? 'text-green-600' : 'text-red-600'" 
                        x-text="formatCurrency(profitLossData.comparison.changes?.revenue_change || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right">
                  <span x-text="calculateChange(profitLossData.summary.total_revenue, (profitLossData.comparison.revenue?.total || 0) + (profitLossData.comparison.other_revenue?.total || 0))"></span>
                </td>
              </tr>

              {{-- BEBAN OPERASIONAL --}}
              <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                <td colspan="6" class="px-4 py-3">BEBAN OPERASIONAL</td>
              </tr>
              <template x-for="account in (profitLossData.expense?.accounts || [])" :key="account.id">
                  <template>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                      <td class="px-4 py-2 border-r border-slate-100">
                        <span class="font-mono text-xs font-semibold text-slate-700" x-text="account.code || '-'"></span>
                      </td>
                      <td class="px-4 py-2 border-r border-slate-100">
                        <div class="flex items-center gap-2">
                          <button x-show="account.children && account.children.length > 0" 
                                  @click="toggleAccountDetails(account.id)"
                                  class="text-slate-400 hover:text-slate-600">
                            <i class='bx text-sm' :class="expandedAccounts.includes(account.id) ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
                          </button>
                          <button @click="showAccountTransactions(account)" 
                                  class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1"
                                  :title="'Klik untuk melihat detail transaksi'">
                            <span class="font-semibold text-slate-800" x-text="account.name || 'Unnamed Account'"></span>
                            <i class='bx bx-info-circle text-xs opacity-50'></i>
                          </button>
                        </div>
                      </td>
                      <td class="px-4 py-2 text-right border-r border-slate-100">
                        <span class="font-semibold text-red-600" x-text="formatCurrency(account.amount)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span x-text="formatCurrency(account.comparison_amount || 0)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span :class="(account.amount - (account.comparison_amount || 0)) >= 0 ? 'text-red-600' : 'text-green-600'" 
                              x-text="formatCurrency(account.amount - (account.comparison_amount || 0))"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                        <span x-text="calculateChange(account.amount, account.comparison_amount || 0)"></span>
                      </td>
                    </tr>
                    {{-- Child Accounts --}}
                    <template x-if="expandedAccounts.includes(account.id) && account.children && account.children.length > 0">
                      <template x-for="child in account.children" :key="child.id">
                        <tr class="border-t border-slate-50 bg-slate-25 hover:bg-slate-50">
                          <td class="px-4 py-2 pl-8 border-r border-slate-100">
                            <span class="font-mono text-xs text-slate-500" x-text="child.code || '-'"></span>
                          </td>
                          <td class="px-4 py-2 border-r border-slate-100">
                            <button @click="showAccountTransactions(child)" 
                                    class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1 text-sm"
                                    :title="'Klik untuk melihat detail transaksi'">
                              <span class="text-slate-600" x-text="child.name || 'Unnamed Account'"></span>
                              <i class='bx bx-info-circle text-xs opacity-50'></i>
                            </button>
                          </td>
                          <td class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm text-red-600" x-text="formatCurrency(child.amount)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" x-text="formatCurrency(child.comparison_amount || 0)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" :class="(child.amount - (child.comparison_amount || 0)) >= 0 ? 'text-red-600' : 'text-green-600'" 
                                  x-text="formatCurrency(child.amount - (child.comparison_amount || 0))"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                            <span class="text-sm" x-text="calculateChange(child.amount, child.comparison_amount || 0)"></span>
                          </td>
                        </tr>
                      </template>
                    </template>
                  </template>
                </template>
              </template>
              <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Beban Operasional</td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-red-600" x-text="formatCurrency(profitLossData.expense?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency(profitLossData.comparison?.expense?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span :class="(profitLossData.comparison?.changes?.expense_change || 0) >= 0 ? 'text-red-600' : 'text-green-600'" 
                        x-text="formatCurrency(profitLossData.comparison?.changes?.expense_change || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right">
                  <span x-text="calculateChange(profitLossData.expense?.total || 0, profitLossData.comparison?.expense?.total || 0)"></span>
                </td>
              </tr>

              {{-- BEBAN LAIN-LAIN --}}
              <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                <td colspan="6" class="px-4 py-3">BEBAN LAIN-LAIN</td>
              </tr>
              <template x-for="account in (profitLossData.other_expense?.accounts || [])" :key="account.id">
                  <template>
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                      <td class="px-4 py-2 border-r border-slate-100">
                        <span class="font-mono text-xs font-semibold text-slate-700" x-text="account.code || '-'"></span>
                      </td>
                      <td class="px-4 py-2 border-r border-slate-100">
                        <div class="flex items-center gap-2">
                          <button x-show="account.children && account.children.length > 0" 
                                  @click="toggleAccountDetails(account.id)"
                                  class="text-slate-400 hover:text-slate-600">
                            <i class='bx text-sm' :class="expandedAccounts.includes(account.id) ? 'bx-chevron-down' : 'bx-chevron-right'"></i>
                          </button>
                          <button @click="showAccountTransactions(account)" 
                                  class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1"
                                  :title="'Klik untuk melihat detail transaksi'">
                            <span class="font-semibold text-slate-800" x-text="account.name || 'Unnamed Account'"></span>
                            <i class='bx bx-info-circle text-xs opacity-50'></i>
                          </button>
                        </div>
                      </td>
                      <td class="px-4 py-2 text-right border-r border-slate-100">
                        <span class="font-semibold text-red-600" x-text="formatCurrency(account.amount)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span x-text="formatCurrency(account.comparison_amount || 0)"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                        <span :class="(account.amount - (account.comparison_amount || 0)) >= 0 ? 'text-red-600' : 'text-green-600'" 
                              x-text="formatCurrency(account.amount - (account.comparison_amount || 0))"></span>
                      </td>
                      <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                        <span x-text="calculateChange(account.amount, account.comparison_amount || 0)"></span>
                      </td>
                    </tr>
                    {{-- Child Accounts --}}
                    <template x-if="expandedAccounts.includes(account.id) && account.children && account.children.length > 0">
                      <template x-for="child in account.children" :key="child.id">
                        <tr class="border-t border-slate-50 bg-slate-25 hover:bg-slate-50">
                          <td class="px-4 py-2 pl-8 border-r border-slate-100">
                            <span class="font-mono text-xs text-slate-500" x-text="child.code || '-'"></span>
                          </td>
                          <td class="px-4 py-2 border-r border-slate-100">
                            <button @click="showAccountTransactions(child)" 
                                    class="text-left hover:text-blue-600 hover:underline transition-colors flex items-center gap-1 text-sm"
                                    :title="'Klik untuk melihat detail transaksi'">
                              <span class="text-slate-600" x-text="child.name || 'Unnamed Account'"></span>
                              <i class='bx bx-info-circle text-xs opacity-50'></i>
                            </button>
                          </td>
                          <td class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm text-red-600" x-text="formatCurrency(child.amount)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" x-text="formatCurrency(child.comparison_amount || 0)"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                            <span class="text-sm" :class="(child.amount - (child.comparison_amount || 0)) >= 0 ? 'text-red-600' : 'text-green-600'" 
                                  x-text="formatCurrency(child.amount - (child.comparison_amount || 0))"></span>
                          </td>
                          <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right">
                            <span class="text-sm" x-text="calculateChange(child.amount, child.comparison_amount || 0)"></span>
                          </td>
                        </tr>
                      </template>
                    </template>
                  </template>
                </template>
              </template>
              <tr class="border-t-2 border-slate-200 bg-slate-50 font-semibold">
                <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Beban Lain-Lain</td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-red-600" x-text="formatCurrency(profitLossData.other_expense?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency(profitLossData.comparison?.other_expense?.total || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right border-r border-slate-200">
                  <span x-text="formatCurrency((profitLossData.other_expense?.total || 0) - (profitLossData.comparison?.other_expense?.total || 0))"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison?.enabled" class="px-4 py-2 text-right">
                  <span x-text="calculateChange(profitLossData.other_expense?.total || 0, profitLossData.comparison?.other_expense?.total || 0)"></span>
                </td>
              </tr>

              {{-- TOTAL BEBAN --}}
              <tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
                <td colspan="2" class="px-4 py-3 text-right border-r border-slate-300">TOTAL BEBAN</td>
                <td class="px-4 py-3 text-right border-r border-slate-300">
                  <span class="text-red-600" x-text="formatCurrency(profitLossData.summary.total_expense)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right border-r border-slate-300">
                  <span x-text="formatCurrency((profitLossData.comparison.expense?.total || 0) + (profitLossData.comparison.other_expense?.total || 0))"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right border-r border-slate-300">
                  <span :class="profitLossData.comparison.changes?.expense_change >= 0 ? 'text-red-600' : 'text-green-600'" 
                        x-text="formatCurrency(profitLossData.comparison.changes?.expense_change || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-3 text-right">
                  <span x-text="calculateChange(profitLossData.summary.total_expense, (profitLossData.comparison.expense?.total || 0) + (profitLossData.comparison.other_expense?.total || 0))"></span>
                </td>
              </tr>

              {{-- LABA/RUGI BERSIH --}}
              <tr class="border-t-2 border-slate-500 bg-blue-50 font-bold text-lg">
                <td colspan="2" class="px-4 py-4 text-right border-r border-slate-400">LABA/RUGI BERSIH</td>
                <td class="px-4 py-4 text-right border-r border-slate-400">
                  <span :class="profitLossData.summary.net_income >= 0 ? 'text-blue-600' : 'text-orange-600'" 
                        x-text="formatCurrency(profitLossData.summary.net_income)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-4 text-right border-r border-slate-400">
                  <span x-text="formatCurrency(profitLossData.comparison.summary?.net_income || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-4 text-right border-r border-slate-400">
                  <span :class="profitLossData.comparison.changes?.net_income_change >= 0 ? 'text-green-600' : 'text-red-600'" 
                        x-text="formatCurrency(profitLossData.comparison.changes?.net_income_change || 0)"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-4 text-right">
                  <span x-text="calculateChange(profitLossData.summary.net_income, profitLossData.comparison.summary?.net_income || 0)"></span>
                </td>
              </tr>

              {{-- RASIO KEUANGAN --}}
              <tr class="bg-slate-100 font-semibold border-t-2 border-slate-300">
                <td colspan="6" class="px-4 py-3">RASIO KEUANGAN</td>
              </tr>
              <tr class="border-t border-slate-100">
                <td colspan="2" class="px-4 py-2 border-r border-slate-100">Gross Profit Margin</td>
                <td class="px-4 py-2 text-right border-r border-slate-100">
                  <span class="font-semibold" x-text="profitLossData.summary.gross_profit_margin !== null ? profitLossData.summary.gross_profit_margin.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                  <span x-text="(profitLossData.comparison.summary && profitLossData.comparison.summary.gross_profit_margin !== null) ? profitLossData.comparison.summary.gross_profit_margin.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" colspan="2" class="px-4 py-2 text-right">
                  <span x-text="calculateMarginChange(profitLossData.summary.gross_profit_margin, profitLossData.comparison.summary ? profitLossData.comparison.summary.gross_profit_margin : null)"></span>
                </td>
              </tr>
              <tr class="border-t border-slate-100">
                <td colspan="2" class="px-4 py-2 border-r border-slate-100">Net Profit Margin</td>
                <td class="px-4 py-2 text-right border-r border-slate-100">
                  <span class="font-semibold" x-text="profitLossData.summary.net_profit_margin !== null ? profitLossData.summary.net_profit_margin.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                  <span x-text="(profitLossData.comparison.summary && profitLossData.comparison.summary.net_profit_margin !== null) ? profitLossData.comparison.summary.net_profit_margin.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" colspan="2" class="px-4 py-2 text-right">
                  <span x-text="calculateMarginChange(profitLossData.summary.net_profit_margin, profitLossData.comparison.summary ? profitLossData.comparison.summary.net_profit_margin : null)"></span>
                </td>
              </tr>
              <tr class="border-t border-slate-100">
                <td colspan="2" class="px-4 py-2 border-r border-slate-100">Operating Expense Ratio</td>
                <td class="px-4 py-2 text-right border-r border-slate-100">
                  <span class="font-semibold" x-text="profitLossData.summary.operating_expense_ratio !== null ? profitLossData.summary.operating_expense_ratio.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" class="px-4 py-2 text-right border-r border-slate-100">
                  <span x-text="(profitLossData.comparison.summary && profitLossData.comparison.summary.operating_expense_ratio !== null) ? profitLossData.comparison.summary.operating_expense_ratio.toFixed(2) + '%' : 'N/A'"></span>
                </td>
                <td x-show="filters.comparison && profitLossData.comparison.enabled" colspan="2" class="px-4 py-2 text-right">
                  <span x-text="calculateMarginChange(profitLossData.summary.operating_expense_ratio, profitLossData.comparison.summary ? profitLossData.comparison.summary.operating_expense_ratio : null)"></span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && !error && isDataEmpty()" 
         x-transition
         class="rounded-2xl border border-slate-200 bg-white p-12 shadow-card text-center">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-line-chart text-4xl text-slate-400'></i>
      </div>
      <h3 class="text-xl font-semibold text-slate-800 mb-2">Tidak Ada Data</h3>
      <p class="text-slate-600 mb-1">Tidak ditemukan transaksi untuk periode yang dipilih.</p>
      <p class="text-sm text-slate-500 mb-6">
        Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button @click="refreshData()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-6 py-2.5 hover:bg-blue-700">
          <i class='bx bx-refresh'></i> Muat Ulang Data
        </button>
        <button @click="filters.period = 'monthly'; onPeriodChange()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-2.5 hover:bg-slate-50">
          <i class='bx bx-calendar'></i> Coba Periode Lain
        </button>
      </div>
    </div>

    {{-- Print Footer - Only visible when printing --}}
    <div class="print-footer" style="display: none;">
      <p style="margin: 0; font-size: 8pt; color: #666;">
        Laporan ini digenerate secara otomatis oleh sistem ERP | 
        Halaman <span class="page-number"></span> dari <span class="page-total"></span>
      </p>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  
  <script>
    function profitLossManagement() {
      return {
        // Routes
        routes: {
          outletsData: '{{ route("finance.outlets.data") }}',
          profitLossData: '{{ route("finance.profit-loss.data") }}',
          profitLossStats: '{{ route("finance.profit-loss.stats") }}',
          accountDetails: '{{ route("finance.profit-loss.account-details") }}',
          exportXLSX: '{{ route("finance.profit-loss.export.xlsx") }}',
          exportPDF: '{{ route("finance.profit-loss.export.pdf") }}'
        },

        // Filters
        filters: {
          outlet_id: '',
          period: 'monthly',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          comparison: false,
          comparison_start_date: '',
          comparison_end_date: ''
        },

        // Data
        outlets: [],
        profitLossData: {
          period: {},
          revenue: { accounts: [], total: 0 },
          other_revenue: { accounts: [], total: 0 },
          expense: { accounts: [], total: 0 },
          other_expense: { accounts: [], total: 0 },
          summary: {
            total_revenue: 0,
            total_expense: 0,
            gross_profit: 0,
            operating_profit: 0,
            net_income: 0,
            gross_profit_margin: null,
            net_profit_margin: null,
            operating_expense_ratio: null
          },
          comparison: {
            enabled: false,
            period: null,
            revenue: null,
            expense: null,
            summary: null,
            changes: null
          }
        },
        stats: {},

        // UI State
        isLoading: false,
        isExporting: false,
        error: null,
        expandedAccounts: [],

        // Charts
        revenueChart: null,
        expenseChart: null,
        comparisonChart: null,
        trendChart: null,
        chartsLoaded: false,

        async init() {
          await this.loadOutlets();
          await this.setDefaultOutlet();
          if (this.filters.outlet_id) {
            await this.loadProfitLossData();
            await this.loadStats();
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const result = await response.json();
            
            if (result.success) {
              this.outlets = result.data;
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
            this.showNotification('Gagal memuat data outlet', 'error');
          }
        },

        async setDefaultOutlet() {
          if (this.outlets.length > 0 && !this.filters.outlet_id) {
            this.filters.outlet_id = this.outlets[0].id_outlet;
          }
        },

        async loadProfitLossData() {
          // Validate outlet_id
          if (!this.filters.outlet_id) {
            this.error = 'Outlet wajib dipilih';
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          // Validate date range
          if (!this.filters.start_date || !this.filters.end_date) {
            this.error = 'Tanggal mulai dan tanggal akhir wajib diisi';
            this.showNotification('Tanggal mulai dan tanggal akhir wajib diisi', 'warning');
            return;
          }

          // Validate end_date >= start_date
          if (new Date(this.filters.end_date) < new Date(this.filters.start_date)) {
            this.error = 'Tanggal akhir harus sama atau setelah tanggal mulai';
            this.showNotification('Tanggal akhir harus sama atau setelah tanggal mulai', 'warning');
            return;
          }

          // Validate comparison date range if comparison is enabled
          if (this.filters.comparison) {
            if (!this.filters.comparison_start_date || !this.filters.comparison_end_date) {
              this.error = 'Tanggal pembanding wajib diisi saat mode perbandingan aktif';
              this.showNotification('Tanggal pembanding wajib diisi saat mode perbandingan aktif', 'warning');
              return;
            }

            if (new Date(this.filters.comparison_end_date) < new Date(this.filters.comparison_start_date)) {
              this.error = 'Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding';
              this.showNotification('Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding', 'warning');
              return;
            }
          }

          try {
            this.isLoading = true;
            this.error = null;
            
            // Build URL with parameters
            let url = `${this.routes.profitLossData}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            if (this.filters.comparison) {
              url += `&comparison=true&comparison_start_date=${this.filters.comparison_start_date}&comparison_end_date=${this.filters.comparison_end_date}`;
            }

            const response = await fetch(url);
            
            // Handle HTTP errors
            if (!response.ok) {
              const result = await response.json();
              
              // Handle validation errors (422)
              if (response.status === 422 && result.errors) {
                const errorMessages = Object.values(result.errors).flat();
                this.error = errorMessages.join(', ');
                this.showNotification(this.error, 'error');
                return;
              }
              
              // Handle other errors
              this.error = result.message || `Error ${response.status}: Gagal memuat data`;
              this.showNotification(this.error, 'error');
              return;
            }
            
            const result = await response.json();
            
            if (result.success) {
              this.profitLossData = result.data;
              
              // Debug: Log data
              console.log('=== PROFIT LOSS DATA LOADED ===');
              console.log('Full Data:', this.profitLossData);
              console.log('Revenue Accounts:', this.profitLossData.revenue?.accounts);
              console.log('Other Revenue Accounts:', this.profitLossData.other_revenue?.accounts);
              console.log('Expense Accounts:', this.profitLossData.expense?.accounts);
              console.log('Other Expense Accounts:', this.profitLossData.other_expense?.accounts);
              
              // Log each account details
              if (this.profitLossData.revenue?.accounts) {
                this.profitLossData.revenue.accounts.forEach((acc, idx) => {
                  console.log(`Revenue Account ${idx}:`, {
                    id: acc.id,
                    code: acc.code,
                    name: acc.name,
                    amount: acc.amount,
                    children: acc.children?.length || 0
                  });
                });
              }
              
              // Auto-expand all accounts with children
              this.expandedAccounts = [];
              const allAccounts = [
                ...(this.profitLossData.revenue?.accounts || []),
                ...(this.profitLossData.other_revenue?.accounts || []),
                ...(this.profitLossData.expense?.accounts || []),
                ...(this.profitLossData.other_expense?.accounts || [])
              ];
              
              allAccounts.forEach(account => {
                if (account.children && account.children.length > 0) {
                  this.expandedAccounts.push(account.id);
                  console.log(`Auto-expanding account: ${account.code} - ${account.name}`);
                }
              });
              
              console.log('Auto-expanded accounts:', this.expandedAccounts);
              console.log('=== END PROFIT LOSS DATA ===');
              
              // Check if data is empty
              if (this.isDataEmpty()) {
                this.showNotification('Tidak ada data untuk periode yang dipilih', 'info');
              }
              
              await this.initCharts();
            } else {
              // Handle validation errors
              if (result.errors) {
                const errorMessages = Object.values(result.errors).flat();
                this.error = errorMessages.join(', ');
                this.showNotification(this.error, 'error');
              } else {
                this.error = result.message || 'Gagal memuat data laporan laba rugi';
                this.showNotification(this.error, 'error');
              }
            }
          } catch (error) {
            console.error('Error loading profit loss data:', error);
            this.error = 'Terjadi kesalahan saat memuat data. Silakan coba lagi.';
            this.showNotification(this.error, 'error');
          } finally {
            this.isLoading = false;
          }
        },

        isDataEmpty() {
          return this.profitLossData.summary.total_revenue === 0 && 
                 this.profitLossData.summary.total_expense === 0;
        },

        async loadStats() {
          if (!this.filters.outlet_id) return;

          try {
            const url = `${this.routes.profitLossStats}?outlet_id=${this.filters.outlet_id}`;
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
              this.stats = result.data;
            }
          } catch (error) {
            console.error('Error loading stats:', error);
          }
        },

        onOutletChange() {
          this.loadProfitLossData();
          this.loadStats();
        },

        onPeriodChange() {
          const today = new Date();
          let startDate, endDate;

          switch (this.filters.period) {
            case 'monthly':
              startDate = new Date(today.getFullYear(), today.getMonth(), 1);
              endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
              break;
            case 'last_month':
              startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
              endDate = new Date(today.getFullYear(), today.getMonth(), 0);
              break;
            case 'quarterly':
              const quarter = Math.floor(today.getMonth() / 3);
              startDate = new Date(today.getFullYear(), quarter * 3, 1);
              endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
              break;
            case 'yearly':
              startDate = new Date(today.getFullYear(), 0, 1);
              endDate = new Date(today.getFullYear(), 11, 31);
              break;
            default:
              return;
          }

          this.filters.start_date = startDate.toISOString().split('T')[0];
          this.filters.end_date = endDate.toISOString().split('T')[0];
          
          // Set comparison dates to previous period
          if (this.filters.comparison) {
            const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const compEndDate = new Date(startDate);
            compEndDate.setDate(compEndDate.getDate() - 1);
            const compStartDate = new Date(compEndDate);
            compStartDate.setDate(compStartDate.getDate() - daysDiff);
            
            this.filters.comparison_start_date = compStartDate.toISOString().split('T')[0];
            this.filters.comparison_end_date = compEndDate.toISOString().split('T')[0];
          }
          
          this.loadProfitLossData();
        },

        toggleComparison() {
          if (this.filters.comparison) {
            // Set comparison dates to previous period
            const startDate = new Date(this.filters.start_date);
            const endDate = new Date(this.filters.end_date);
            const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            
            const compEndDate = new Date(startDate);
            compEndDate.setDate(compEndDate.getDate() - 1);
            const compStartDate = new Date(compEndDate);
            compStartDate.setDate(compStartDate.getDate() - daysDiff);
            
            this.filters.comparison_start_date = compStartDate.toISOString().split('T')[0];
            this.filters.comparison_end_date = compEndDate.toISOString().split('T')[0];
          }
          this.loadProfitLossData();
        },

        toggleAccountDetails(accountId) {
          const index = this.expandedAccounts.indexOf(accountId);
          if (index > -1) {
            this.expandedAccounts.splice(index, 1);
          } else {
            this.expandedAccounts.push(accountId);
          }
        },

        async exportToXLSX() {
          // Validate outlet_id
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          // Validate date range
          if (!this.filters.start_date || !this.filters.end_date) {
            this.showNotification('Tanggal mulai dan tanggal akhir wajib diisi', 'warning');
            return;
          }

          if (new Date(this.filters.end_date) < new Date(this.filters.start_date)) {
            this.showNotification('Tanggal akhir harus sama atau setelah tanggal mulai', 'warning');
            return;
          }

          // Validate comparison date range if comparison is enabled
          if (this.filters.comparison) {
            if (!this.filters.comparison_start_date || !this.filters.comparison_end_date) {
              this.showNotification('Tanggal pembanding wajib diisi saat mode perbandingan aktif', 'warning');
              return;
            }

            if (new Date(this.filters.comparison_end_date) < new Date(this.filters.comparison_start_date)) {
              this.showNotification('Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding', 'warning');
              return;
            }
          }

          try {
            this.isExporting = true;
            
            // Build URL with parameters
            let url = `${this.routes.exportXLSX}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            if (this.filters.comparison) {
              url += `&comparison=true&comparison_start_date=${this.filters.comparison_start_date}&comparison_end_date=${this.filters.comparison_end_date}`;
            }

            // Trigger download
            window.location.href = url;
            this.showNotification('Export XLSX berhasil dimulai', 'success');
          } catch (error) {
            console.error('Error exporting to XLSX:', error);
            this.showNotification('Gagal mengekspor data ke XLSX', 'error');
          } finally {
            setTimeout(() => {
              this.isExporting = false;
            }, 2000);
          }
        },

        async exportToPDF() {
          // Validate outlet_id
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          // Validate date range
          if (!this.filters.start_date || !this.filters.end_date) {
            this.showNotification('Tanggal mulai dan tanggal akhir wajib diisi', 'warning');
            return;
          }

          if (new Date(this.filters.end_date) < new Date(this.filters.start_date)) {
            this.showNotification('Tanggal akhir harus sama atau setelah tanggal mulai', 'warning');
            return;
          }

          // Validate comparison date range if comparison is enabled
          if (this.filters.comparison) {
            if (!this.filters.comparison_start_date || !this.filters.comparison_end_date) {
              this.showNotification('Tanggal pembanding wajib diisi saat mode perbandingan aktif', 'warning');
              return;
            }

            if (new Date(this.filters.comparison_end_date) < new Date(this.filters.comparison_start_date)) {
              this.showNotification('Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding', 'warning');
              return;
            }
          }

          try {
            this.isExporting = true;
            
            // Build URL with parameters
            let url = `${this.routes.exportPDF}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            if (this.filters.comparison) {
              url += `&comparison=true&comparison_start_date=${this.filters.comparison_start_date}&comparison_end_date=${this.filters.comparison_end_date}`;
            }

            // Trigger download
            window.location.href = url;
            this.showNotification('Export PDF berhasil dimulai', 'success');
          } catch (error) {
            console.error('Error exporting to PDF:', error);
            this.showNotification('Gagal mengekspor data ke PDF', 'error');
          } finally {
            setTimeout(() => {
              this.isExporting = false;
            }, 2000);
          }
        },

        printReport() {
          // Validate outlet_id
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          // Validate date range
          if (!this.filters.start_date || !this.filters.end_date) {
            this.showNotification('Tanggal mulai dan tanggal akhir wajib diisi', 'warning');
            return;
          }

          // Check if data is loaded
          if (!this.profitLossData || this.profitLossData.summary.total_revenue === undefined) {
            this.showNotification('Data belum dimuat. Silakan tunggu sebentar.', 'warning');
            return;
          }

          // Use browser's native print dialog
          try {
            // Give a small delay to ensure all data is rendered
            setTimeout(() => {
              window.print();
            }, 100);
          } catch (error) {
            console.error('Error printing report:', error);
            this.showNotification('Gagal mencetak laporan', 'error');
          }
        },

        async refreshData() {
          await this.loadProfitLossData();
          await this.loadStats();
        },

        async initCharts() {
          this.chartsLoaded = false;
          
          // Wait for next tick to ensure canvas elements are rendered
          await this.$nextTick();
          
          // Additional delay to ensure DOM is fully ready
          await new Promise(resolve => setTimeout(resolve, 100));
          
          // Destroy existing charts
          this.destroyCharts();

          // Create all charts with error handling
          try {
            this.createRevenuePieChart();
          } catch (error) {
            console.error('Error creating revenue chart:', error);
          }
          
          try {
            this.createExpensePieChart();
          } catch (error) {
            console.error('Error creating expense chart:', error);
          }
          
          try {
            this.createComparisonBarChart();
          } catch (error) {
            console.error('Error creating comparison chart:', error);
          }
          
          // Create trend chart only in comparison mode
          if (this.filters.comparison && this.profitLossData.comparison?.enabled) {
            try {
              this.createTrendLineChart();
            } catch (error) {
              console.error('Error creating trend chart:', error);
            }
          }

          this.chartsLoaded = true;
        },

        destroyCharts() {
          if (this.revenueChart) {
            this.revenueChart.destroy();
            this.revenueChart = null;
          }
          if (this.expenseChart) {
            this.expenseChart.destroy();
            this.expenseChart = null;
          }
          if (this.comparisonChart) {
            this.comparisonChart.destroy();
            this.comparisonChart = null;
          }
          if (this.trendChart) {
            this.trendChart.destroy();
            this.trendChart = null;
          }
        },

        createRevenuePieChart() {
          const canvas = this.$refs.revenuePieChart;
          if (!canvas) {
            console.warn('Revenue chart canvas not found');
            return;
          }
          
          const revenueCtx = canvas.getContext('2d');
          if (!revenueCtx) {
            console.warn('Revenue chart context not available');
            return;
          }

          // Combine revenue and other_revenue accounts
          const allRevenueAccounts = [
            ...this.profitLossData.revenue.accounts,
            ...this.profitLossData.other_revenue.accounts
          ];

          const revenueData = allRevenueAccounts
            .filter(acc => acc.amount > 0)
            .map(acc => ({ label: acc.name, value: acc.amount }));
          
          // Show empty state if no data
          if (revenueData.length === 0) {
            revenueData.push({ label: 'Tidak ada data', value: 1 });
          }
          
          this.revenueChart = new Chart(revenueCtx, {
            type: 'pie',
            data: {
              labels: revenueData.map(d => d.label),
              datasets: [{
                data: revenueData.map(d => d.value),
                backgroundColor: [
                  '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444',
                  '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    padding: 10,
                    font: {
                      size: 11
                    }
                  }
                },
                tooltip: {
                  callbacks: {
                    label: (context) => {
                      const label = context.label || '';
                      const value = this.formatCurrency(context.parsed);
                      const total = context.dataset.data.reduce((a, b) => a + b, 0);
                      const percentage = ((context.parsed / total) * 100).toFixed(1);
                      return `${label}: ${value} (${percentage}%)`;
                    }
                  }
                }
              },
              onClick: (event, elements) => {
                if (elements.length > 0) {
                  const index = elements[0].index;
                  const account = allRevenueAccounts.filter(acc => acc.amount > 0)[index];
                  if (account) {
                    this.showAccountDetail(account);
                  }
                }
              }
            }
          });
        },

        createExpensePieChart() {
          const canvas = this.$refs.expensePieChart;
          if (!canvas) {
            console.warn('Expense chart canvas not found');
            return;
          }
          
          const expenseCtx = canvas.getContext('2d');
          if (!expenseCtx) {
            console.warn('Expense chart context not available');
            return;
          }

          // Combine expense and other_expense accounts
          const allExpenseAccounts = [
            ...this.profitLossData.expense.accounts,
            ...this.profitLossData.other_expense.accounts
          ];

          const expenseData = allExpenseAccounts
            .filter(acc => acc.amount > 0)
            .map(acc => ({ label: acc.name, value: acc.amount }));
          
          // Show empty state if no data
          if (expenseData.length === 0) {
            expenseData.push({ label: 'Tidak ada data', value: 1 });
          }
          
          this.expenseChart = new Chart(expenseCtx, {
            type: 'pie',
            data: {
              labels: expenseData.map(d => d.label),
              datasets: [{
                data: expenseData.map(d => d.value),
                backgroundColor: [
                  '#ef4444', '#f59e0b', '#f97316', '#dc2626', '#ea580c',
                  '#fb923c', '#fbbf24', '#f87171', '#fdba74', '#fcd34d'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom',
                  labels: {
                    padding: 10,
                    font: {
                      size: 11
                    }
                  }
                },
                tooltip: {
                  callbacks: {
                    label: (context) => {
                      const label = context.label || '';
                      const value = this.formatCurrency(context.parsed);
                      const total = context.dataset.data.reduce((a, b) => a + b, 0);
                      const percentage = ((context.parsed / total) * 100).toFixed(1);
                      return `${label}: ${value} (${percentage}%)`;
                    }
                  }
                }
              },
              onClick: (event, elements) => {
                if (elements.length > 0) {
                  const index = elements[0].index;
                  const account = allExpenseAccounts.filter(acc => acc.amount > 0)[index];
                  if (account) {
                    this.showAccountDetail(account);
                  }
                }
              }
            }
          });
        },

        createComparisonBarChart() {
          const canvas = this.$refs.comparisonBarChart;
          if (!canvas) {
            console.warn('Comparison chart canvas not found');
            return;
          }
          
          const comparisonCtx = canvas.getContext('2d');
          if (!comparisonCtx) {
            console.warn('Comparison chart context not available');
            return;
          }

          this.comparisonChart = new Chart(comparisonCtx, {
            type: 'bar',
            data: {
              labels: ['Pendapatan', 'Beban', 'Laba/Rugi Bersih'],
              datasets: [{
                label: 'Jumlah',
                data: [
                  this.profitLossData.summary.total_revenue,
                  this.profitLossData.summary.total_expense,
                  this.profitLossData.summary.net_income
                ],
                backgroundColor: ['#10b981', '#ef4444', '#3b82f6'],
                borderRadius: 8,
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  callbacks: {
                    label: (context) => {
                      return context.dataset.label + ': ' + this.formatCurrency(context.parsed.y);
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: (value) => {
                      if (value >= 1000000) {
                        return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                      } else if (value >= 1000) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                      }
                      return 'Rp ' + value;
                    }
                  },
                  grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                  }
                },
                x: {
                  grid: {
                    display: false
                  }
                }
              }
            }
          });
        },

        createTrendLineChart() {
          const canvas = this.$refs.trendLineChart;
          if (!canvas) {
            console.warn('Trend chart canvas not found');
            return;
          }
          
          const trendCtx = canvas.getContext('2d');
          if (!trendCtx) {
            console.warn('Trend chart context not available');
            return;
          }

          this.trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
              labels: ['Periode Pembanding', 'Periode Saat Ini'],
              datasets: [{
                label: 'Laba/Rugi Bersih',
                data: [
                  this.profitLossData.comparison.summary?.net_income || 0,
                  this.profitLossData.summary.net_income
                ],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
              }]
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
                    label: (context) => {
                      return context.dataset.label + ': ' + this.formatCurrency(context.parsed.y);
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: (value) => {
                      if (value >= 1000000) {
                        return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                      } else if (value >= 1000) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                      }
                      return 'Rp ' + value;
                    }
                  },
                  grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                  }
                },
                x: {
                  grid: {
                    display: false
                  }
                }
              }
            }
          });
        },

        updateCharts() {
          // Update existing charts with new data
          if (this.revenueChart) {
            const allRevenueAccounts = [
              ...this.profitLossData.revenue.accounts,
              ...this.profitLossData.other_revenue.accounts
            ];
            const revenueData = allRevenueAccounts
              .filter(acc => acc.amount > 0)
              .map(acc => ({ label: acc.name, value: acc.amount }));
            
            this.revenueChart.data.labels = revenueData.map(d => d.label);
            this.revenueChart.data.datasets[0].data = revenueData.map(d => d.value);
            this.revenueChart.update();
          }

          if (this.expenseChart) {
            const allExpenseAccounts = [
              ...this.profitLossData.expense.accounts,
              ...this.profitLossData.other_expense.accounts
            ];
            const expenseData = allExpenseAccounts
              .filter(acc => acc.amount > 0)
              .map(acc => ({ label: acc.name, value: acc.amount }));
            
            this.expenseChart.data.labels = expenseData.map(d => d.label);
            this.expenseChart.data.datasets[0].data = expenseData.map(d => d.value);
            this.expenseChart.update();
          }

          if (this.comparisonChart) {
            this.comparisonChart.data.datasets[0].data = [
              this.profitLossData.summary.total_revenue,
              this.profitLossData.summary.total_expense,
              this.profitLossData.summary.net_income
            ];
            this.comparisonChart.update();
          }

          if (this.trendChart && this.filters.comparison && this.profitLossData.comparison.enabled) {
            this.trendChart.data.datasets[0].data = [
              this.profitLossData.comparison.summary?.net_income || 0,
              this.profitLossData.summary.net_income
            ];
            this.trendChart.update();
          }
        },

        showAccountDetail(account) {
          // Show notification with account details
          this.showNotification(`Detail akun: ${account.code} - ${account.name}\nJumlah: ${this.formatCurrency(account.amount)}`, 'info');
          
          // Optionally expand the account in the table
          if (!this.expandedAccounts.includes(account.id)) {
            this.expandedAccounts.push(account.id);
          }
          
          // Scroll to the account in the table
          setTimeout(() => {
            const tableSection = document.querySelector('.profit-loss-table');
            if (tableSection) {
              tableSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          }, 100);
        },

        formatCurrency(amount) {
          if (amount === null || amount === undefined) return 'Rp 0';
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(amount);
        },

        formatDate(dateString) {
          if (!dateString) return '-';
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
          });
        },

        calculateChange(current, previous) {
          if (!previous || previous === 0) return 'N/A';
          const change = ((current - previous) / Math.abs(previous)) * 100;
          const sign = change >= 0 ? '+' : '';
          return sign + change.toFixed(2) + '%';
        },

        calculateMarginChange(current, previous) {
          if (current === null || previous === null) return 'N/A';
          const change = current - previous;
          const sign = change >= 0 ? '+' : '';
          return sign + change.toFixed(2) + ' pp';
        },

        // Account Transaction Details Modal
        showAccountModal: false,
        isLoadingAccountDetails: false,
        accountDetails: {
          account: null,
          period: null,
          transactions: [],
          summary: null
        },
        accountDetailsError: null,

        async showAccountTransactions(account) {
          this.showAccountModal = true;
          this.isLoadingAccountDetails = true;
          this.accountDetailsError = null;
          this.accountDetails = {
            account: null,
            period: null,
            transactions: [],
            summary: null
          };

          try {
            const url = `{{ route('finance.profit-loss.account-details') }}?outlet_id=${this.filters.outlet_id}&account_id=${account.id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            const response = await fetch(url);
            
            if (!response.ok) {
              const result = await response.json();
              this.accountDetailsError = result.message || 'Gagal memuat detail transaksi';
              this.showNotification(this.accountDetailsError, 'error');
              return;
            }
            
            const result = await response.json();
            
            if (result.success) {
              this.accountDetails = result.data;
            } else {
              this.accountDetailsError = result.message || 'Gagal memuat detail transaksi';
              this.showNotification(this.accountDetailsError, 'error');
            }
          } catch (error) {
            console.error('Error loading account details:', error);
            this.accountDetailsError = 'Terjadi kesalahan saat memuat detail transaksi';
            this.showNotification(this.accountDetailsError, 'error');
          } finally {
            this.isLoadingAccountDetails = false;
          }
        },

        closeAccountModal() {
          this.showAccountModal = false;
          this.accountDetails = {
            account: null,
            period: null,
            transactions: [],
            summary: null
          };
          this.accountDetailsError = null;
        },

        // Method untuk mendapatkan amount pembanding
        getComparisonAmount(account, type) {
            if (!this.profitLossData.comparison.enabled || !this.profitLossData.comparison[type]) {
                return 0;
            }
            
            // Cari account yang sesuai di data pembanding
            const comparisonAccount = this.profitLossData.comparison[type].accounts.find(
                acc => acc.id === account.id || acc.code === account.code
            );
            
            return comparisonAccount ? comparisonAccount.amount : 0;
        },

        // Method untuk debug data
        debugData() {
            console.log('Revenue Accounts:', this.profitLossData.revenue?.accounts);
            console.log('Expense Accounts:', this.profitLossData.expense?.accounts);
            console.log('Other Revenue Accounts:', this.profitLossData.other_revenue?.accounts);
            console.log('Other Expense Accounts:', this.profitLossData.other_expense?.accounts);
        },

        showNotification(message, type = 'info') {
          // Implement notification system with better styling
          const toast = document.createElement('div');
          
          // Icon based on type
          const icons = {
            error: 'bx-error-circle',
            success: 'bx-check-circle',
            warning: 'bx-error',
            info: 'bx-info-circle'
          };
          
          // Colors based on type
          const colors = {
            error: 'bg-red-500',
            success: 'bg-green-500',
            warning: 'bg-orange-500',
            info: 'bg-blue-500'
          };
          
          toast.className = `fixed top-4 right-4 p-4 rounded-xl text-white z-50 shadow-lg flex items-center gap-3 min-w-[300px] max-w-md animate-slide-in ${colors[type] || colors.info}`;
          toast.innerHTML = `
            <i class='bx ${icons[type] || icons.info} text-2xl'></i>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
              <i class='bx bx-x text-xl'></i>
            </button>
          `;
          
          document.body.appendChild(toast);
          
          // Auto dismiss after 5 seconds
          setTimeout(() => {
            if (toast.parentElement) {
              toast.style.opacity = '0';
              toast.style.transform = 'translateX(100%)';
              toast.style.transition = 'all 0.3s ease-out';
              setTimeout(() => {
                if (toast.parentElement) {
                  document.body.removeChild(toast);
                }
              }, 300);
            }
          }, 5000);
        }
      };
    }
  </script>

  <style>
    .profit-loss-table {
      border-collapse: collapse;
    }
    
    .profit-loss-table th,
    .profit-loss-table td {
      border: 1px solid #e2e8f0;
      vertical-align: top;
    }
    
    .profit-loss-table th {
      background-color: #f8fafc;
      font-weight: 600;
      color: #374151;
    }
    
    .chart-container {
      position: relative;
      height: 16rem;
    }
    
    .chart-loading {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    
    .bg-slate-25 {
      background-color: #f9fafb;
    }
    
    /* Notification Animation */
    @keyframes slide-in {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    .animate-slide-in {
      animation: slide-in 0.3s ease-out;
    }
    
    /* Print Header - Hidden by default, shown only when printing */
    .print-only-header {
      display: none;
    }
    
    /* Print Styles */
    @media print {
      /* Page setup */
      @page {
        size: A4 portrait;
        margin: 1.5cm 1cm;
      }
      
      body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      }
      
      /* Hide unnecessary elements */
      button,
      .no-print,
      nav,
      aside,
      header,
      footer,
      .sidebar,
      [x-data] > div:first-child:has(h1),
      .flex.flex-col.gap-3.sm\\:flex-row,
      .rounded-2xl.border.border-slate-200.bg-white.p-6.shadow-card:has(select),
      .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4.gap-4,
      .grid.grid-cols-1.lg\\:grid-cols-2.gap-6,
      .rounded-2xl.border.border-slate-200.bg-white.p-6.shadow-card:has(canvas),
      .chart-container,
      canvas {
        display: none !important;
      }
      
      /* Show print header */
      .print-only-header {
        display: block !important;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #000;
      }
      
      /* Optimize table section for print */
      .print-section {
        page-break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #000 !important;
        border-radius: 0 !important;
        margin: 0 !important;
      }
      
      .print-header {
        background-color: white !important;
        border-bottom: 2px solid #000 !important;
        padding: 10px !important;
      }
      
      /* Table styling for print */
      .profit-loss-table {
        width: 100%;
        font-size: 10pt;
        border-collapse: collapse;
      }
      
      .profit-loss-table th,
      .profit-loss-table td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
        vertical-align: top;
      }
      
      .profit-loss-table th {
        background-color: #f0f0f0 !important;
        font-weight: bold;
        color: #000 !important;
      }
      
      .profit-loss-table tbody tr:nth-child(even) {
        background-color: #fafafa !important;
      }
      
      /* Ensure colors are visible in print */
      .text-green-600 {
        color: #059669 !important;
      }
      
      .text-red-600 {
        color: #dc2626 !important;
      }
      
      .text-blue-600 {
        color: #2563eb !important;
      }
      
      .text-orange-600 {
        color: #ea580c !important;
      }
      
      .bg-slate-100,
      .bg-slate-50,
      .bg-slate-200,
      .bg-blue-50 {
        background-color: #f5f5f5 !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      }
      
      /* Font weights for print */
      .font-semibold,
      .font-bold {
        font-weight: bold !important;
      }
      
      /* Avoid page breaks inside important sections */
      tr {
        page-break-inside: avoid;
      }
      
      /* Hide comparison columns if not needed */
      .overflow-x-auto {
        overflow: visible !important;
      }
      
      /* Ensure proper spacing */
      .space-y-6 > * {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
      }
      
      /* Print footer */
      .print-footer {
        display: block !important;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        padding: 10px;
        border-top: 1px solid #000;
        background-color: white;
      }
    }
  </style>
</x-layouts.admin>
