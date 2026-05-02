{{-- resources/views/admin/finance/neraca/index.blade.php --}}
<x-layouts.admin :title="'Neraca (Balance Sheet)'">
  <div x-data="neracaManagement()" x-init="init()" class="space-y-6">

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
        <h1 class="text-2xl font-bold tracking-tight">Neraca (Balance Sheet)</h1>
        <p class="text-slate-600 text-sm">Laporan posisi keuangan perusahaan pada tanggal tertentu</p>
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
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
          <select x-model="filters.book_id" @change="loadNeracaData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Buku</option>
            <template x-for="book in books" :key="book.id">
              <option :value="book.id" x-text="book.name"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Neraca</label>
          <input type="date" x-model="filters.end_date" @change="loadNeracaData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
      </div>
    </div>


    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-slate-600">Memuat data neraca...</p>
    </div>

    {{-- Error State --}}
    <div x-show="error && !isLoading" class="rounded-2xl border border-red-200 bg-red-50 p-6">
      <div class="flex items-center gap-3">
        <i class='bx bx-error-circle text-3xl text-red-600'></i>
        <div>
          <h3 class="font-semibold text-red-900">Gagal Memuat Data</h3>
          <p class="text-sm text-red-700" x-text="error"></p>
        </div>
      </div>
    </div>

    {{-- Neraca Content --}}
    <div x-show="!isLoading && !error" class="space-y-6">
      
      {{-- Balance Check Alert --}}
      <div x-show="neracaData.totals && !neracaData.totals.is_balanced" 
           class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4">
        <div class="flex items-center gap-3">
          <i class='bx bx-error text-2xl text-yellow-600'></i>
          <div>
            <h3 class="font-semibold text-yellow-900">Neraca Tidak Balance</h3>
            <p class="text-sm text-yellow-700">
              Selisih: <span x-text="formatCurrency(Math.abs(neracaData.totals?.difference || 0))"></span>
            </p>
          </div>
        </div>
      </div>

      {{-- Neraca Table --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- ASET (Assets) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl">
            <h2 class="text-lg font-semibold text-white">ASET</h2>
          </div>
          <div class="p-6">
            <template x-if="neracaData.assets && neracaData.assets.length > 0">
              <div class="space-y-2">
                <template x-for="asset in neracaData.assets" :key="asset.id">
                  <div>
                    <div @click="viewAccountDetails(asset.id)" 
                         class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors"
                         :class="{'font-semibold': asset.level === 1}">
                      <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500" x-text="asset.code"></span>
                        <span class="text-slate-700" x-text="asset.name"></span>
                        <i x-show="asset.has_children" class='bx bx-chevron-down text-xs text-slate-400'></i>
                      </div>
                      <span class="text-slate-900 font-medium" x-text="formatCurrency(asset.balance)"></span>
                    </div>
                    
                    {{-- Children Accounts --}}
                    <template x-if="asset.children && asset.children.length > 0">
                      <div class="ml-6 space-y-1">
                        <template x-for="child in asset.children" :key="child.id">
                          <div @click="viewAccountDetails(child.id)"
                               class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors text-sm">
                            <div class="flex items-center gap-2">
                              <span class="text-xs text-slate-400" x-text="child.code"></span>
                              <span class="text-slate-600" x-text="child.name"></span>
                            </div>
                            <span class="text-slate-700" x-text="formatCurrency(child.balance)"></span>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </template>
            
            <template x-if="!neracaData.assets || neracaData.assets.length === 0">
              <div class="text-center py-8 text-slate-500">
                <i class='bx bx-info-circle text-3xl mb-2'></i>
                <p>Tidak ada data aset</p>
              </div>
            </template>
            
            <div class="mt-4 pt-4 border-t-2 border-slate-300">
              <div class="flex justify-between items-center font-bold text-lg">
                <span class="text-slate-700">TOTAL ASET</span>
                <span class="text-blue-700" x-text="formatCurrency(neracaData.totals?.total_assets || 0)"></span>
              </div>
            </div>
          </div>
        </div>

        {{-- KEWAJIBAN & EKUITAS (Liabilities & Equity) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
          <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4 rounded-t-2xl">
            <h2 class="text-lg font-semibold text-white">KEWAJIBAN & EKUITAS</h2>
          </div>
          <div class="p-6 space-y-6">
            
            {{-- KEWAJIBAN (Liabilities) --}}
            <div>
              <h3 class="font-semibold text-slate-700 mb-3">KEWAJIBAN</h3>
              <template x-if="neracaData.liabilities && neracaData.liabilities.length > 0">
                <div class="space-y-2">
                  <template x-for="liability in neracaData.liabilities" :key="liability.id">
                    <div>
                      <div @click="viewAccountDetails(liability.id)"
                           class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors"
                           :class="{'font-semibold': liability.level === 1}">
                        <div class="flex items-center gap-2">
                          <span class="text-xs text-slate-500" x-text="liability.code"></span>
                          <span class="text-slate-700" x-text="liability.name"></span>
                          <i x-show="liability.has_children" class='bx bx-chevron-down text-xs text-slate-400'></i>
                        </div>
                        <span class="text-slate-900 font-medium" x-text="formatCurrency(liability.balance)"></span>
                      </div>
                      
                      {{-- Children Accounts --}}
                      <template x-if="liability.children && liability.children.length > 0">
                        <div class="ml-6 space-y-1">
                          <template x-for="child in liability.children" :key="child.id">
                            <div @click="viewAccountDetails(child.id)"
                                 class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors text-sm">
                              <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400" x-text="child.code"></span>
                                <span class="text-slate-600" x-text="child.name"></span>
                              </div>
                              <span class="text-slate-700" x-text="formatCurrency(child.balance)"></span>
                            </div>
                          </template>
                        </div>
                      </template>
                    </div>
                  </template>
                </div>
              </template>
              
              <template x-if="!neracaData.liabilities || neracaData.liabilities.length === 0">
                <div class="text-center py-4 text-slate-500 text-sm">
                  <p>Tidak ada data kewajiban</p>
                </div>
              </template>
              
              <div class="mt-3 pt-3 border-t border-slate-200">
                <div class="flex justify-between items-center font-semibold">
                  <span class="text-slate-700">Total Kewajiban</span>
                  <span class="text-slate-900" x-text="formatCurrency(neracaData.totals?.total_liabilities || 0)"></span>
                </div>
              </div>
            </div>

            {{-- EKUITAS (Equity) --}}
            <div>
              <h3 class="font-semibold text-slate-700 mb-3">EKUITAS</h3>
              <template x-if="neracaData.equity && neracaData.equity.length > 0">
                <div class="space-y-2">
                  <template x-for="eq in neracaData.equity" :key="eq.id">
                    <div>
                      <div @click="viewAccountDetails(eq.id)"
                           class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors"
                           :class="{'font-semibold': eq.level === 1}">
                        <div class="flex items-center gap-2">
                          <span class="text-xs text-slate-500" x-text="eq.code"></span>
                          <span class="text-slate-700" x-text="eq.name"></span>
                          <i x-show="eq.has_children" class='bx bx-chevron-down text-xs text-slate-400'></i>
                        </div>
                        <span class="text-slate-900 font-medium" x-text="formatCurrency(eq.balance)"></span>
                      </div>
                      
                      {{-- Children Accounts --}}
                      <template x-if="eq.children && eq.children.length > 0">
                        <div class="ml-6 space-y-1">
                          <template x-for="child in eq.children" :key="child.id">
                            <div @click="viewAccountDetails(child.id)"
                                 class="flex justify-between items-center py-2 px-3 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors text-sm">
                              <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400" x-text="child.code"></span>
                                <span class="text-slate-600" x-text="child.name"></span>
                              </div>
                              <span class="text-slate-700" x-text="formatCurrency(child.balance)"></span>
                            </div>
                          </template>
                        </div>
                      </template>
                    </div>
                  </template>
                  
                  {{-- Laba Ditahan --}}
                  <div class="flex justify-between items-center py-2 px-3 rounded-lg">
                    <div class="flex items-center gap-2">
                      <span class="text-xs text-slate-500">-</span>
                      <span class="text-slate-700">Laba Ditahan</span>
                    </div>
                    <span class="text-slate-900 font-medium" x-text="formatCurrency(neracaData.retained_earnings || 0)"></span>
                  </div>
                </div>
              </template>
              
              <template x-if="!neracaData.equity || neracaData.equity.length === 0">
                <div class="text-center py-4 text-slate-500 text-sm">
                  <p>Tidak ada data ekuitas</p>
                </div>
              </template>
              
              <div class="mt-3 pt-3 border-t border-slate-200">
                <div class="flex justify-between items-center font-semibold">
                  <span class="text-slate-700">Total Ekuitas</span>
                  <span class="text-slate-900" x-text="formatCurrency(neracaData.totals?.total_equity || 0)"></span>
                </div>
              </div>
            </div>

            {{-- TOTAL KEWAJIBAN & EKUITAS --}}
            <div class="pt-4 border-t-2 border-slate-300">
              <div class="flex justify-between items-center font-bold text-lg">
                <span class="text-slate-700">TOTAL KEWAJIBAN & EKUITAS</span>
                <span class="text-purple-700" x-text="formatCurrency(neracaData.totals?.total_liabilities_and_equity || 0)"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>


  <script>
    function neracaManagement() {
      return {
        isLoading: false,
        error: null,
        outlets: [],
        books: [],
        neracaData: {
          assets: [],
          liabilities: [],
          equity: [],
          retained_earnings: 0,
          totals: null
        },
        filters: {
          outlet_id: '',
          book_id: '',
          end_date: new Date().toISOString().split('T')[0]
        },
        showAccountModal: false,
        isLoadingAccountDetails: false,
        accountDetails: {
          account: null,
          transactions: [],
          summary: null
        },
        accountDetailsError: null,

        async init() {
          await this.loadOutlets();
          if (this.outlets.length > 0) {
            this.filters.outlet_id = this.outlets[0].id_outlet;
            await this.loadBooks();
            await this.loadNeracaData();
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

        async loadNeracaData() {
          if (!this.filters.outlet_id) {
            this.error = 'Pilih outlet terlebih dahulu';
            return;
          }

          this.isLoading = true;
          this.error = null;

          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id,
              end_date: this.filters.end_date
            });
            
            if (this.filters.book_id) {
              params.append('book_id', this.filters.book_id);
            }

            const response = await fetch(`{{ route('finance.neraca.data') }}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.neracaData = result.data;
            } else {
              this.error = result.message || 'Gagal memuat data neraca';
            }
          } catch (error) {
            console.error('Error loading neraca data:', error);
            this.error = 'Terjadi kesalahan saat memuat data';
          } finally {
            this.isLoading = false;
          }
        },

        async onOutletChange() {
          this.filters.book_id = ''; // Reset book filter
          await this.loadBooks();
          await this.loadNeracaData();
        },

        async refreshData() {
          await this.loadNeracaData();
        },

        async viewAccountDetails(accountId) {
          this.showAccountModal = true;
          this.isLoadingAccountDetails = true;
          this.accountDetailsError = null;
          this.accountDetails = {
            account: null,
            transactions: [],
            summary: null
          };

          try {
            const params = new URLSearchParams({
              end_date: this.filters.end_date
            });
            
            if (this.filters.book_id) {
              params.append('book_id', this.filters.book_id);
            }

            const response = await fetch(`{{ url('finance/neraca/account-details') }}/${accountId}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.accountDetails = result.data;
            } else {
              this.accountDetailsError = result.message || 'Gagal memuat detail akun';
            }
          } catch (error) {
            console.error('Error loading account details:', error);
            this.accountDetailsError = 'Terjadi kesalahan saat memuat detail akun';
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

        async exportToXLSX() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }

          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            end_date: this.filters.end_date
          });
          
          if (this.filters.book_id) {
            params.append('book_id', this.filters.book_id);
          }

          window.location.href = `{{ route('finance.neraca.export.xlsx') }}?${params}`;
        },

        async exportToPDF() {
          if (!this.filters.outlet_id) {
            alert('Pilih outlet terlebih dahulu');
            return;
          }

          const params = new URLSearchParams({
            outlet_id: this.filters.outlet_id,
            end_date: this.filters.end_date
          });
          
          if (this.filters.book_id) {
            params.append('book_id', this.filters.book_id);
          }

          window.location.href = `{{ route('finance.neraca.export.pdf') }}?${params}`;
        },

        printReport() {
          window.print();
        },

        formatCurrency(value) {
          if (value === null || value === undefined) return 'Rp 0';
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
          return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
          });
        }
      };
    }
  </script>

  <style>
    @media print {
      .no-print {
        display: none !important;
      }
      
      body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      }
    }
  </style>
</x-layouts.admin>
