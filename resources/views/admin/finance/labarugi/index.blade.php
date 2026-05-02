{{-- resources/views/admin/finance/labarugi/index.blade.php --}}
<x-layouts.admin :title="'Laporan Laba Rugi'">
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
        <h1 class="text-2xl font-bold tracking-tight">Laporan Laba Rugi</h1>
        <p class="text-slate-600 text-sm">Laporan kinerja keuangan perusahaan dalam periode tertentu</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
            <i class='bx bx-chevron-down text-sm'></i>
          </button>
          
          <div x-show="exportOpen" @click.away="exportOpen = false" x-transition class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportToXLSX(); exportOpen = false" class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i> Export ke XLSX
            </button>
            <button @click="exportToPDF(); exportOpen = false" class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl">
              <i class='bx bxs-file-pdf text-red-600'></i> Export ke PDF
            </button>
          </div>
        </div>

        <button @click="printReport()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-printer'></i> Print
        </button>
        <button @click="refreshData()" :disabled="isLoading" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data laporan laba rugi...</p>
    </div>

    {{-- Error Message --}}
    <div x-show="error" x-transition class="rounded-xl bg-red-50 border border-red-200 p-4 shadow-sm">
      <div class="flex items-start gap-3">
        <i class='bx bx-error-circle text-2xl text-red-600'></i>
        <div class="flex-1">
          <h4 class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</h4>
          <p class="text-sm text-red-700" x-text="error"></p>
        </div>
        <button @click="error = null" class="text-red-400 hover:text-red-600">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
    </div>

    {{-- Profit & Loss Table --}}
    <div x-show="!isLoading && !error" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      
      {{-- Header --}}
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Laporan Laba Rugi</h2>
          <p class="text-sm text-slate-600">
            Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
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

      {{-- Table Content --}}
      <div class="overflow-x-auto">
        <table class="w-full text-sm profit-loss-table" x-html="renderProfitLossTable()"></table>
      </div>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && !error && isDataEmpty()" x-transition class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-line-chart text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Tidak ada data</h3>
      <p class="text-slate-600 mb-4">Tidak ditemukan transaksi untuk periode yang dipilih.</p>
      <button @click="refreshData()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
        <i class='bx bx-refresh'></i> Muat Ulang Data
      </button>
    </div>

  </div>

  <script>
    function profitLossManagement() {
      return {
        routes: {
          outletsData: '{{ route("finance.outlets.data") }}',
          profitLossData: '{{ route("finance.profit-loss.data") }}',
          accountDetails: '{{ route("finance.profit-loss.account-details") }}',
          exportXLSX: '{{ route("finance.profit-loss.export.xlsx") }}',
          exportPDF: '{{ route("finance.profit-loss.export.pdf") }}'
        },
        filters: {
          outlet_id: '',
          period: 'monthly',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0]
        },
        outlets: [],
        profitLossData: {
          revenue: { accounts: [], total: 0 },
          other_revenue: { accounts: [], total: 0 },
          expense: { accounts: [], total: 0 },
          other_expense: { accounts: [], total: 0 },
          summary: {
            total_revenue: 0,
            total_expense: 0,
            net_income: 0,
            gross_profit_margin: null,
            net_profit_margin: null
          }
        },
        isLoading: false,
        error: null,
        showAccountModal: false,
        isLoadingAccountDetails: false,
        accountDetails: {
          account: null,
          transactions: [],
          summary: null
        },
        accountDetailsError: null,

        async init() {
          window.profitLossApp = this;
          await this.loadOutlets();
          await this.setDefaultOutlet();
          await this.loadProfitLossData();
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
          }
        },

        async setDefaultOutlet() {
          if (this.outlets.length > 0 && !this.filters.outlet_id) {
            this.filters.outlet_id = this.outlets[0].id_outlet;
          }
        },

        async loadProfitLossData() {
          if (!this.filters.outlet_id) {
            this.error = 'Pilih outlet terlebih dahulu';
            return;
          }

          try {
            this.isLoading = true;
            this.error = null;
            
            const url = `${this.routes.profitLossData}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            const response = await fetch(url);
            
            if (!response.ok) {
              const result = await response.json();
              this.error = result.message || 'Gagal memuat data';
              return;
            }
            
            const result = await response.json();
            if (result.success) {
              this.profitLossData = result.data;
            } else {
              this.error = result.message || 'Gagal memuat data laporan laba rugi';
            }
          } catch (error) {
            console.error('Error loading profit loss data:', error);
            this.error = 'Terjadi kesalahan saat memuat data';
          } finally {
            this.isLoading = false;
          }
        },

        onOutletChange() {
          this.loadProfitLossData();
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
          this.loadProfitLossData();
        },

        renderAccountRow(account, colorClass = 'text-green-600', isChild = false) {
          const hasChildren = account.children && account.children.length > 0;
          const isClickable = !hasChildren || isChild;
          const nameClass = isClickable ? 'text-blue-600 hover:text-blue-800 hover:underline cursor-pointer' : 'text-slate-800';
          const paddingClass = isChild ? 'pl-8' : '';
          const sizeClass = isChild ? 'text-sm' : '';
          
          return `
            <tr class="border-t ${isChild ? 'border-slate-50 bg-slate-25' : 'border-slate-100'} hover:bg-slate-50">
              <td class="px-4 py-2 ${paddingClass} border-r border-slate-100">
                <span class="font-mono text-xs ${isChild ? 'text-slate-500' : 'text-blue-600 font-semibold'}">${account.code || '-'}</span>
              </td>
              <td class="px-4 py-2 border-r border-slate-100">
                ${isClickable ? 
                  `<button onclick="window.profitLossApp.showAccountTransactions(${account.id}, '${account.code}', '${account.name}')" class="${nameClass} ${sizeClass}">${account.name || 'Unnamed Account'}</button>` :
                  `<span class="${nameClass} ${sizeClass}">${account.name || 'Unnamed Account'}</span>`
                }
              </td>
              <td class="px-4 py-2 text-right">
                <span class="${colorClass} ${isChild ? sizeClass : 'font-semibold'}">${this.formatCurrency(account.amount)}</span>
              </td>
            </tr>
          `;
        },

        renderProfitLossTable() {
          if (!this.profitLossData || this.isDataEmpty()) {
            return `
              <tbody>
                <tr>
                  <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                    Tidak ada data transaksi
                  </td>
                </tr>
              </tbody>
            `;
          }

          let html = `
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-32 border-r border-slate-200">Kode</th>
                <th class="px-4 py-3 text-left border-r border-slate-200">Nama Akun</th>
                <th class="px-4 py-3 text-right w-48">Jumlah</th>
              </tr>
            </thead>
            <tbody>
          `;

          // PENDAPATAN
          html += `
            <tr class="bg-slate-50 border-t border-slate-300">
              <td colspan="3" class="px-4 py-3 font-semibold text-slate-700">PENDAPATAN</td>
            </tr>
          `;

          if (this.profitLossData.revenue && this.profitLossData.revenue.accounts) {
            this.profitLossData.revenue.accounts.forEach((account) => {
              html += this.renderAccountRow(account, 'text-green-600');
              
              if (account.children && account.children.length > 0) {
                account.children.forEach((child) => {
                  html += this.renderAccountRow(child, 'text-green-600', true);
                });
              }
            });
          }

          html += `
            <tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
              <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Pendapatan</td>
              <td class="px-4 py-2 text-right">
                <span class="text-green-600">${this.formatCurrency(this.profitLossData.revenue.total)}</span>
              </td>
            </tr>
            <tr class="h-4"><td colspan="3" class="bg-slate-50"></td></tr>
          `;

          // PENDAPATAN LAIN-LAIN
          html += `
            <tr class="bg-slate-50 border-t border-slate-300">
              <td colspan="3" class="px-4 py-3 font-semibold text-slate-700">PENDAPATAN LAIN-LAIN</td>
            </tr>
          `;

          if (this.profitLossData.other_revenue && this.profitLossData.other_revenue.accounts) {
            this.profitLossData.other_revenue.accounts.forEach((account) => {
              html += this.renderAccountRow(account, 'text-green-600');
              
              if (account.children && account.children.length > 0) {
                account.children.forEach((child) => {
                  html += this.renderAccountRow(child, 'text-green-600', true);
                });
              }
            });
          }

          html += `
            <tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
              <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Pendapatan Lain-Lain</td>
              <td class="px-4 py-2 text-right">
                <span class="text-green-600">${this.formatCurrency(this.profitLossData.other_revenue.total)}</span>
              </td>
            </tr>
            <tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
              <td colspan="2" class="px-4 py-3 text-right border-r border-slate-300">TOTAL PENDAPATAN</td>
              <td class="px-4 py-3 text-right">
                <span class="text-green-700">${this.formatCurrency(this.profitLossData.summary.total_revenue)}</span>
              </td>
            </tr>
            <tr class="h-4"><td colspan="3" class="bg-slate-50"></td></tr>
          `;

          // BEBAN OPERASIONAL
          html += `
            <tr class="bg-slate-50 border-t border-slate-300">
              <td colspan="3" class="px-4 py-3 font-semibold text-slate-700">BEBAN OPERASIONAL</td>
            </tr>
          `;

          if (this.profitLossData.expense && this.profitLossData.expense.accounts) {
            this.profitLossData.expense.accounts.forEach((account) => {
              html += this.renderAccountRow(account, 'text-red-600');
              
              if (account.children && account.children.length > 0) {
                account.children.forEach((child) => {
                  html += this.renderAccountRow(child, 'text-red-600', true);
                });
              }
            });
          }

          html += `
            <tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
              <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Beban Operasional</td>
              <td class="px-4 py-2 text-right">
                <span class="text-red-600">${this.formatCurrency(this.profitLossData.expense.total)}</span>
              </td>
            </tr>
            <tr class="h-4"><td colspan="3" class="bg-slate-50"></td></tr>
          `;

          // BEBAN LAIN-LAIN
          html += `
            <tr class="bg-slate-50 border-t border-slate-300">
              <td colspan="3" class="px-4 py-3 font-semibold text-slate-700">BEBAN LAIN-LAIN</td>
            </tr>
          `;

          if (this.profitLossData.other_expense && this.profitLossData.other_expense.accounts) {
            this.profitLossData.other_expense.accounts.forEach((account) => {
              html += this.renderAccountRow(account, 'text-red-600');
              
              if (account.children && account.children.length > 0) {
                account.children.forEach((child) => {
                  html += this.renderAccountRow(child, 'text-red-600', true);
                });
              }
            });
          }

          html += `
            <tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
              <td colspan="2" class="px-4 py-2 text-right border-r border-slate-200">Total Beban Lain-Lain</td>
              <td class="px-4 py-2 text-right">
                <span class="text-red-600">${this.formatCurrency(this.profitLossData.other_expense.total)}</span>
              </td>
            </tr>
            <tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
              <td colspan="2" class="px-4 py-3 text-right border-r border-slate-300">TOTAL BEBAN</td>
              <td class="px-4 py-3 text-right">
                <span class="text-red-700">${this.formatCurrency(this.profitLossData.summary.total_expense)}</span>
              </td>
            </tr>
            <tr class="h-4"><td colspan="3" class="bg-slate-50"></td></tr>
          `;

          // LABA/RUGI BERSIH
          const netIncomeClass = this.profitLossData.summary.net_income >= 0 ? 'text-blue-600' : 'text-orange-600';
          html += `
            <tr class="border-t-2 border-slate-500 bg-blue-50 font-bold">
              <td colspan="2" class="px-4 py-4 text-right border-r border-slate-400">LABA/RUGI BERSIH</td>
              <td class="px-4 py-4 text-right">
                <span class="${netIncomeClass} text-lg">${this.formatCurrency(this.profitLossData.summary.net_income)}</span>
              </td>
            </tr>
          `;

          html += '</tbody>';
          return html;
        },

        isDataEmpty() {
          return (!this.profitLossData.revenue || this.profitLossData.revenue.accounts.length === 0) && 
                 (!this.profitLossData.expense || this.profitLossData.expense.accounts.length === 0) &&
                 (!this.profitLossData.other_revenue || this.profitLossData.other_revenue.accounts.length === 0) &&
                 (!this.profitLossData.other_expense || this.profitLossData.other_expense.accounts.length === 0);
        },

        async exportToXLSX() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }
          const url = `${this.routes.exportXLSX}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
          window.location.href = url;
        },

        async exportToPDF() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }
          const url = `${this.routes.exportPDF}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
          window.location.href = url;
        },

        printReport() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }
          window.print();
        },

        refreshData() {
          this.loadProfitLossData();
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
            const url = `${this.routes.accountDetails}?outlet_id=${this.filters.outlet_id}&account_id=${accountId}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
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
        }
      };
    }

    // Make it globally accessible for onclick handlers
    document.addEventListener('alpine:init', () => {
      window.profitLossApp = null;
    });
  </script>

  <style>
    .profit-loss-table {
      border-collapse: collapse;
    }
    
    .profit-loss-table th,
    .profit-loss-table td {
      border: 1px solid #e2e8f0;
    }
    
    .profit-loss-table th {
      background-color: #f8fafc;
      font-weight: 600;
      color: #374151;
    }
    
    .bg-slate-25 {
      background-color: #f9fafb;
    }
  </style>
</x-layouts.admin>
