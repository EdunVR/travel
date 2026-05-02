{{-- resources/views/admin/finance/neraca-saldo/index.blade.php --}}
<x-layouts.admin :title="'Neraca Saldo (Trial Balance)'">
  <div x-data="neracaSaldoManagement()" x-init="init()" class="space-y-6">

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
                  <div class="text-lg font-bold text-purple-700" x-text="formatCurrency(accountDetails.summary?.current_balance || 0)"></div>
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
                          <p>Tidak ada transaksi untuk akun ini</p>
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
                </table>
              </div>
            </div>
          </div>

          <div class="bg-slate-50 px-6 py-4 flex justify-end">
            <button @click="closeAccountModal()" class="px-6 py-2 bg-slate-600 text-white rounded-xl hover:bg-slate-700 transition-colors">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
            <i class='bx bx-balance text-3xl'></i>
          </div>
          <div>
            <h1 class="text-2xl font-bold">Neraca Saldo</h1>
            <p class="text-blue-100 text-sm mt-1">Trial Balance - Ringkasan Saldo Debit & Kredit</p>
          </div>
        </div>
        <div class="flex gap-2">
          @hasPermission('finance.neraca-saldo.export')
          <button @click="exportPDF()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl transition-colors flex items-center gap-2">
            <i class='bx bxs-file-pdf text-xl'></i>
            <span>PDF</span>
          </button>
          <button @click="exportExcel()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl transition-colors flex items-center gap-2">
            <i class='bx bxs-file text-xl'></i>
            <span>Excel</span>
          </button>
          @endhasPermission
        </div>
      </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadData()" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Buku</label>
          <select x-model="filters.book_id" @change="loadData()" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            <option value="">Semua Buku</option>
            <template x-for="book in books" :key="book.id">
              <option :value="book.id" x-text="book.name"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Mulai</label>
          <input type="date" x-model="filters.start_date" @change="loadData()" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadData()" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
        </div>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600 mb-1">Total Debit</p>
            <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(summary.total_debit)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
            <i class='bx bx-plus-circle text-2xl text-green-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600 mb-1">Total Kredit</p>
            <p class="text-2xl font-bold text-red-600" x-text="formatCurrency(summary.total_credit)"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
            <i class='bx bx-minus-circle text-2xl text-red-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600 mb-1">Selisih</p>
            <p class="text-2xl font-bold" :class="summary.is_balanced ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(summary.difference)"></p>
            <p class="text-xs mt-1" :class="summary.is_balanced ? 'text-green-600' : 'text-red-600'">
              <span x-show="summary.is_balanced">✓ Seimbang</span>
              <span x-show="!summary.is_balanced">⚠ Tidak Seimbang</span>
            </p>
          </div>
          <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="summary.is_balanced ? 'bg-green-100' : 'bg-red-100'">
            <i class='bx text-2xl' :class="summary.is_balanced ? 'bx-check-circle text-green-600' : 'bx-error-circle text-red-600'"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Trial Balance Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Kode Akun</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">Nama Akun</th>
              <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700">Tipe</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Saldo Awal</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Debit</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Kredit</th>
              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">Saldo Akhir</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="isLoading">
              <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-slate-600">Memuat data neraca saldo...</p>
                  </div>
                </td>
              </tr>
            </template>
            <template x-if="!isLoading && trialBalanceData.length === 0">
              <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                  <i class='bx bx-info-circle text-4xl mb-2'></i>
                  <p>Tidak ada data neraca saldo untuk periode yang dipilih</p>
                </td>
              </tr>
            </template>
            <template x-if="!isLoading && trialBalanceData.length > 0">
              <template x-for="(account, index) in trialBalanceData" :key="account.id">
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors cursor-pointer" @click="viewAccountDetails(account)">
                  <td class="px-6 py-4">
                    <span class="font-mono text-sm text-slate-700" x-text="account.code"></span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-slate-700" x-text="account.name"></span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="{
                            'bg-blue-100 text-blue-800': account.type === 'asset',
                            'bg-red-100 text-red-800': account.type === 'liability',
                            'bg-purple-100 text-purple-800': account.type === 'equity',
                            'bg-green-100 text-green-800': account.type === 'revenue' || account.type === 'otherrevenue',
                            'bg-orange-100 text-orange-800': account.type === 'expense' || account.type === 'otherexpense'
                          }"
                          x-text="getAccountTypeLabel(account.type)">
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="text-sm" :class="account.opening_balance >= 0 ? 'text-slate-700' : 'text-red-600'" x-text="formatCurrency(account.opening_balance)"></span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="text-sm font-semibold text-green-600" x-text="formatCurrency(account.debit)"></span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="text-sm font-semibold text-red-600" x-text="formatCurrency(account.credit)"></span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="text-sm font-bold" :class="account.ending_balance >= 0 ? 'text-slate-700' : 'text-red-600'" x-text="formatCurrency(account.ending_balance)"></span>
                  </td>
                </tr>
              </template>
            </template>
          </tbody>
          <tfoot x-show="!isLoading && trialBalanceData.length > 0" class="bg-slate-100 border-t-2 border-slate-300">
            <tr>
              <td colspan="4" class="px-6 py-4 text-right font-bold text-slate-700">TOTAL</td>
              <td class="px-6 py-4 text-right font-bold text-green-600" x-text="formatCurrency(summary.total_debit)"></td>
              <td class="px-6 py-4 text-right font-bold text-red-600" x-text="formatCurrency(summary.total_credit)"></td>
              <td class="px-6 py-4 text-right font-bold text-slate-700" x-text="formatCurrency(summary.total_debit - summary.total_credit)"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </div>

  <script>
    function neracaSaldoManagement() {
      return {
        // Data
        trialBalanceData: [],
        outlets: [],
        books: [],
        summary: {
          total_debit: 0,
          total_credit: 0,
          difference: 0,
          is_balanced: true
        },
        
        // Filters
        filters: {
          outlet_id: '',
          book_id: '',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0]
        },
        
        // Loading states
        isLoading: false,
        isLoadingAccountDetails: false,
        
        // Modal
        showAccountModal: false,
        accountDetails: {
          account: null,
          transactions: [],
          summary: null
        },
        
        // Initialize
        async init() {
          await this.loadOutlets();
          await this.loadBooks();
          await this.loadData();
        },
        
        // Load outlets
        async loadOutlets() {
          try {
            const response = await fetch('{{ route("finance.outlets.data") }}');
            const result = await response.json();
            if (result.success) {
              this.outlets = result.data;
              if (this.outlets.length > 0 && !this.filters.outlet_id) {
                this.filters.outlet_id = this.outlets[0].id_outlet;
              }
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },
        
        // Load books
        async loadBooks() {
          try {
            const response = await fetch('{{ route("finance.accounting-books.data") }}');
            const result = await response.json();
            if (result.success) {
              this.books = result.data;
            }
          } catch (error) {
            console.error('Error loading books:', error);
          }
        },
        
        // Load trial balance data
        async loadData() {
          this.isLoading = true;
          try {
            const params = new URLSearchParams(this.filters);
            const response = await fetch(`{{ route('finance.trial-balance.data') }}?${params}`);
            const result = await response.json();
            
            if (result.success) {
              this.trialBalanceData = result.data;
              this.summary = result.summary;
            } else {
              this.showNotification('error', result.message || 'Gagal memuat data');
            }
          } catch (error) {
            console.error('Error loading trial balance data:', error);
            this.showNotification('error', 'Terjadi kesalahan saat memuat data');
          } finally {
            this.isLoading = false;
          }
        },
        
        // View account details
        async viewAccountDetails(account) {
          this.showAccountModal = true;
          this.isLoadingAccountDetails = true;
          this.accountDetails = {
            account: account,
            transactions: [],
            summary: null
          };
          
          try {
            const params = new URLSearchParams({
              account_id: account.id,
              outlet_id: this.filters.outlet_id,
              book_id: this.filters.book_id || '',
              start_date: this.filters.start_date,
              end_date: this.filters.end_date
            });
            
            const response = await fetch(`{{ route('finance.general-ledger.account-details') }}?${params}`);
            const result = await response.json();
            
            if (result.success) {
              this.accountDetails.transactions = result.data.transactions;
              this.accountDetails.summary = result.data.summary;
            }
          } catch (error) {
            console.error('Error loading account details:', error);
            this.showNotification('error', 'Gagal memuat detail transaksi');
          } finally {
            this.isLoadingAccountDetails = false;
          }
        },
        
        // Close account modal
        closeAccountModal() {
          this.showAccountModal = false;
          this.accountDetails = {
            account: null,
            transactions: [],
            summary: null
          };
        },
        
        // Export to PDF
        exportPDF() {
          const params = new URLSearchParams(this.filters);
          window.open(`{{ route('finance.trial-balance.export.pdf') }}?${params}`, '_blank');
        },
        
        // Export to Excel
        exportExcel() {
          const params = new URLSearchParams(this.filters);
          window.location.href = `{{ route('finance.trial-balance.export.excel') }}?${params}`;
        },
        
        // Get account type label
        getAccountTypeLabel(type) {
          const labels = {
            'asset': 'Aset',
            'liability': 'Kewajiban',
            'equity': 'Ekuitas',
            'revenue': 'Pendapatan',
            'expense': 'Beban',
            'otherrevenue': 'Pendapatan Lain',
            'otherexpense': 'Beban Lain'
          };
          return labels[type] || type;
        },
        
        // Format currency
        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(amount || 0);
        },
        
        // Format date
        formatDate(date) {
          if (!date) return '-';
          return new Date(date).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          });
        },
        
        // Show notification
        showNotification(type, message) {
          // Implement your notification system here
          console.log(`[${type}] ${message}`);
          alert(message);
        }
      };
    }
  </script>
</x-layouts.admin>
