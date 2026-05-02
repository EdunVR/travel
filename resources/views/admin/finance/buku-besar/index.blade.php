{{-- resources/views/admin/finance/buku-besar/index.blade.php --}}
<x-layouts.admin :title="'Buku Besar'">
  <div x-data="generalLedgerManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Buku Besar</h1>
        <p class="text-slate-600 text-sm">Laporan detail transaksi per akun dalam periode tertentu</p>
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

        <button @click="printLedger()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-printer'></i> Print
        </button>
        <button @click="refreshData()" :disabled="isLoading" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
          <input type="date" x-model="filters.start_date" @change="loadLedgerData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
          <input type="date" x-model="filters.end_date" @change="loadLedgerData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Akun</label>
          <select x-model="filters.account_id" @change="loadLedgerData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Akun</option>
            <template x-for="account in availableAccounts" :key="account.id">
              <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
            </template>
          </select>
        </div>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data buku besar...</p>
    </div>

    {{-- Odoo Style Ledger --}}
    <div x-show="!isLoading && ledgerData.ledger_entries.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      
      {{-- Header --}}
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Buku Besar</h2>
          <p class="text-sm text-slate-600">
            Periode: <span x-text="formatDate(filters.start_date)"></span> s/d <span x-text="formatDate(filters.end_date)"></span>
            <span x-show="filters.account_id !== 'all'">- Akun Terpilih</span>
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <div class="text-sm text-slate-600">Total Debit</div>
            <div class="text-lg font-bold text-green-600" x-text="formatCurrency(ledgerData.summary.total_debit)"></div>
          </div>
          <div class="text-right">
            <div class="text-sm text-slate-600">Total Kredit</div>
            <div class="text-lg font-bold text-red-600" x-text="formatCurrency(ledgerData.summary.total_credit)"></div>
          </div>
        </div>
      </div>

      {{-- Ledger Content --}}
      <div class="overflow-x-auto">
        <table class="w-full text-sm ledger-table" x-html="renderLedgerTable()"></table>
      </div>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && (!ledgerData.ledger_entries || ledgerData.ledger_entries.length === 0)" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-book text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Tidak ada data</h3>
      <p class="text-slate-600 mb-4">Tidak ditemukan transaksi untuk periode yang dipilih.</p>
      <button @click="refreshData()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
        <i class='bx bx-refresh'></i> Muat Ulang Data
      </button>
    </div>

  </div>

  <script>
    function generalLedgerManagement() {
      return {
        // Routes - Define all route URLs
        routes: {
            outletsData: '{{ route("finance.outlets.data") }}',
            chartOfAccountsData: '{{ route("finance.chart-of-accounts.data") }}',
            generalLedgerData: '{{ route("finance.general-ledger.data") }}',
            jurnalIndex: '{{ route("finance.jurnal.index") }}',
            exportXLSX: '{{ route("finance.general-ledger.export.xlsx") }}',
            exportPDF: '{{ route("finance.general-ledger.export.pdf") }}'
        },
        filters: {
          outlet_id: '',
          period: 'monthly',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0],
          account_id: 'all'
        },
        outlets: [],
        availableAccounts: [],
        ledgerData: {
          ledger_entries: [],
          summary: {
            total_debit: 0,
            total_credit: 0,
            balance: 0
          }
        },
        isLoading: false,

        async init() {
          await this.loadOutlets();
          await this.setDefaultOutlet();
          await this.loadAccounts();
          await this.loadLedgerData();
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

        async loadAccounts() {
          if (!this.filters.outlet_id) return;

          try {
            const url = `${this.routes.chartOfAccountsData}?outlet_id=${this.filters.outlet_id}&status=active`;
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
              this.availableAccounts = result.data;
            }
          } catch (error) {
            console.error('Error loading accounts:', error);
          }
        },

        async loadLedgerData() {
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          try {
            this.isLoading = true;
            
            // Build URL with parameters
            let url = `${this.routes.generalLedgerData}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}&level=odoo-style`;
            
            if (this.filters.account_id !== 'all') {
              url += `&account_id=${this.filters.account_id}`;
            }

            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
              this.ledgerData = result.data;
            } else {
              this.showNotification(result.message || 'Gagal memuat data buku besar', 'error');
            }
          } catch (error) {
            console.error('Error loading ledger data:', error);
            this.showNotification('Gagal memuat data buku besar', 'error');
          } finally {
            this.isLoading = false;
          }
        },

        onOutletChange() {
          this.loadAccounts();
          this.loadLedgerData();
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
          this.loadLedgerData();
        },

        getJournalUrl(journalId, outletId) {
          return `${this.routes.jurnalIndex}?journal_id=${journalId}&outlet_id=${outletId}`;
        },

        getTypeName(type) {
          const names = {
            asset: 'Aset',
            liability: 'Kewajiban', 
            equity: 'Ekuitas',
            revenue: 'Pendapatan',
            expense: 'Beban',
            otherrevenue: 'Pendapatan Lain',
            otherexpense: 'Beban Lain'
          };
          return names[type] || type;
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
          if (amount === null || amount === undefined) return 'Rp 0';
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(amount);
        },

        async exportToXLSX() {
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          try {
            // Build URL with parameters
            let url = `${this.routes.exportXLSX}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            if (this.filters.account_id !== 'all') {
              url += `&account_id=${this.filters.account_id}`;
            }

            // Trigger download
            window.location.href = url;
            this.showNotification('Export XLSX berhasil dimulai', 'success');
          } catch (error) {
            console.error('Error exporting to XLSX:', error);
            this.showNotification('Gagal mengekspor data ke XLSX', 'error');
          }
        },

        async exportToPDF() {
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          try {
            // Build URL with parameters
            let url = `${this.routes.exportPDF}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
            
            if (this.filters.account_id !== 'all') {
              url += `&account_id=${this.filters.account_id}`;
            }

            // Trigger download
            window.location.href = url;
            this.showNotification('Export PDF berhasil dimulai', 'success');
          } catch (error) {
            console.error('Error exporting to PDF:', error);
            this.showNotification('Gagal mengekspor data ke PDF', 'error');
          }
        },

        printLedger() {
          if (!this.filters.outlet_id) {
            this.showNotification('Pilih outlet terlebih dahulu', 'warning');
            return;
          }

          // Build URL with parameters for PDF print
          let url = `${this.routes.exportPDF}?outlet_id=${this.filters.outlet_id}&start_date=${this.filters.start_date}&end_date=${this.filters.end_date}`;
          
          if (this.filters.account_id !== 'all') {
            url += `&account_id=${this.filters.account_id}`;
          }

          // Open PDF in new window for printing
          window.open(url, '_blank');
        },

        async refreshData() {
          await this.loadLedgerData();
        },

        showNotification(message, type = 'info') {
          // Implement notification system
          const toast = document.createElement('div');
          toast.className = `fixed top-4 right-4 p-4 rounded-lg text-white ${
            type === 'error' ? 'bg-red-500' : 
            type === 'success' ? 'bg-green-500' : 
            type === 'warning' ? 'bg-orange-500' : 'bg-blue-500'
          }`;
          toast.textContent = message;
          document.body.appendChild(toast);
          
          setTimeout(() => {
            document.body.removeChild(toast);
          }, 3000);
        },

        renderLedgerTable() {
          if (!this.ledgerData.ledger_entries || this.ledgerData.ledger_entries.length === 0) {
            return `
              <tbody>
                <tr>
                  <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                    Tidak ada data transaksi
                  </td>
                </tr>
              </tbody>
            `;
          }

          let html = `
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12 border-r border-slate-200">#</th>
                <th class="px-4 py-3 text-left border-r border-slate-200">Tanggal</th>
                <th class="px-4 py-3 text-left border-r border-slate-200">Jurnal</th>
                <th class="px-4 py-3 text-left border-r border-slate-200">Keterangan</th>
                <th class="px-4 py-3 text-right border-r border-slate-200">Debit</th>
                <th class="px-4 py-3 text-right border-r border-slate-200">Kredit</th>
                <th class="px-4 py-3 text-right">Saldo</th>
              </tr>
            </thead>
            <tbody>
          `;

          this.ledgerData.ledger_entries.forEach((accountEntry, accountIndex) => {
            // Account Header
            html += `
              <tr class="bg-slate-50 border-t border-slate-300">
                <td colspan="7" class="px-4 py-3 font-semibold">
                  <div class="flex items-center justify-between">
                    <div>
                      <span class="text-blue-600 font-mono">${accountEntry.account_code}</span>
                      <span class="text-slate-700 ml-2">${accountEntry.account_name}</span>
                      <span class="text-xs text-slate-500 ml-2">${this.getTypeName(accountEntry.account_type)}</span>
                    </div>
                    <div class="text-sm text-slate-500">${accountEntry.transaction_count} transaksi</div>
                  </div>
                </td>
              </tr>
            `;

            // Opening Balance
            html += `
              <tr class="border-t border-slate-100 bg-blue-50">
                <td class="px-4 py-2 border-r border-slate-100 text-center text-slate-400">-</td>
                <td class="px-4 py-2 border-r border-slate-100">${this.formatDate(this.filters.start_date)}</td>
                <td class="px-4 py-2 border-r border-slate-100">
                  <span class="font-mono text-xs text-slate-500">SALDO-AWAL</span>
                </td>
                <td class="px-4 py-2 border-r border-slate-100 text-slate-600">Saldo Awal Periode</td>
                <td class="px-4 py-2 border-r border-slate-100 text-right">
                  ${accountEntry.opening_balance > 0 ? 
                    `<span class="font-semibold text-green-600">${this.formatCurrency(accountEntry.opening_balance)}</span>` : 
                    `<span class="text-slate-400">-</span>`
                  }
                </td>
                <td class="px-4 py-2 border-r border-slate-100 text-right">
                  ${accountEntry.opening_balance < 0 ? 
                    `<span class="font-semibold text-red-600">${this.formatCurrency(Math.abs(accountEntry.opening_balance))}</span>` : 
                    `<span class="text-slate-400">-</span>`
                  }
                </td>
                <td class="px-4 py-2 text-right">
                  <span class="${accountEntry.opening_balance >= 0 ? 'text-blue-600' : 'text-orange-600'} font-semibold">
                    ${this.formatCurrency(accountEntry.opening_balance)}
                  </span>
                </td>
              </tr>
            `;

            // Transactions
            accountEntry.transactions.forEach((transaction, transIndex) => {
              html += `
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-2 border-r border-slate-100 text-center text-slate-500">${transIndex + 1}</td>
                  <td class="px-4 py-2 border-r border-slate-100">
                    <span class="text-slate-600 text-xs">${transaction.date_formatted}</span>
                  </td>
                  <td class="px-4 py-2 border-r border-slate-100">
                    <a href="${this.getJournalUrl(transaction.journal_id, this.filters.outlet_id)}" 
                      class="font-mono text-blue-600 hover:text-blue-800 hover:underline text-xs block"
                      title="Lihat detail jurnal">${transaction.reference}</a>
                    <div class="text-xs text-slate-500 mt-1">${transaction.book_name}</div>
                  </td>
                  <td class="px-4 py-2 border-r border-slate-100">
                    <div class="text-slate-800 text-sm">${transaction.description}</div>
                  </td>
                  <td class="px-4 py-2 border-r border-slate-100 text-right">
                    ${transaction.debit > 0 ? 
                      `<span class="font-semibold text-green-600 text-sm">${this.formatCurrency(transaction.debit)}</span>` : 
                      `<span class="text-slate-400">-</span>`
                    }
                  </td>
                  <td class="px-4 py-2 border-r border-slate-100 text-right">
                    ${transaction.credit > 0 ? 
                      `<span class="font-semibold text-red-600 text-sm">${this.formatCurrency(transaction.credit)}</span>` : 
                      `<span class="text-slate-400">-</span>`
                    }
                  </td>
                  <td class="px-4 py-2 text-right">
                    <span class="${transaction.balance >= 0 ? 'text-blue-600' : 'text-orange-600'} font-semibold text-sm">
                      ${this.formatCurrency(transaction.balance)}
                    </span>
                  </td>
                </tr>
              `;
            });

            // Account Total
            html += `
              <tr class="border-t-2 border-slate-300 bg-slate-100 font-semibold">
                <td colspan="4" class="px-4 py-2 text-right border-r border-slate-200">
                  Total <span class="font-mono">${accountEntry.account_code}</span>
                </td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-green-600">${this.formatCurrency(accountEntry.total_debit)}</span>
                </td>
                <td class="px-4 py-2 text-right border-r border-slate-200">
                  <span class="text-red-600">${this.formatCurrency(accountEntry.total_credit)}</span>
                </td>
                <td class="px-4 py-2 text-right">
                  <span class="${accountEntry.ending_balance >= 0 ? 'text-blue-600' : 'text-orange-600'}">
                    ${this.formatCurrency(accountEntry.ending_balance)}
                  </span>
                </td>
              </tr>
            `;

            // Spacer
            html += `<tr class="h-4"><td colspan="7" class="bg-slate-50"></td></tr>`;
          });

          // Grand Total
          html += `
            <tr class="border-t-2 border-slate-400 bg-slate-200 font-bold">
              <td colspan="4" class="px-4 py-3 text-right border-r border-slate-300">TOTAL BUKU BESAR</td>
              <td class="px-4 py-3 text-right border-r border-slate-300">
                <span class="text-green-600">${this.formatCurrency(this.ledgerData.summary.total_debit)}</span>
              </td>
              <td class="px-4 py-3 text-right border-r border-slate-300">
                <span class="text-red-600">${this.formatCurrency(this.ledgerData.summary.total_credit)}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <span class="${this.ledgerData.summary.balance >= 0 ? 'text-blue-600' : 'text-orange-600'}">
                  ${this.formatCurrency(this.ledgerData.summary.balance)}
                </span>
              </td>
            </tr>
          `;

          html += '</tbody>';
          return html;
        }
      };
    }
  </script>

  <style>
    .ledger-table {
      border-collapse: collapse;
    }
    
    .ledger-table th,
    .ledger-table td {
      border: 1px solid #e2e8f0;
      vertical-align: top;
    }
    
    .ledger-table th {
      background-color: #f8fafc;
      font-weight: 600;
      color: #374151;
      position: sticky;
      top: 0;
    }
    
    .ledger-table th:first-child {
      width: 50px;
    }
    
    .ledger-table th:nth-child(2) {
      width: 100px;
    }
    
    .ledger-table th:nth-child(3) {
      width: 120px;
    }
    
    .ledger-table th:nth-child(4) {
      width: auto;
      min-width: 200px;
    }
    
    .ledger-table th:nth-child(5),
    .ledger-table th:nth-child(6),
    .ledger-table th:nth-child(7) {
      width: 120px;
    }
    
    .bg-blue-50 {
      background-color: #eff6ff;
    }
    
    .bg-slate-100 {
      background-color: #f1f5f9;
    }
    
    .bg-slate-200 {
      background-color: #e2e8f0;
    }
    
    /* Ensure proper alignment */
    .ledger-table td {
      padding: 8px 12px;
      line-height: 1.4;
    }
    
    /* Right align for numeric columns */
    .ledger-table td:nth-child(5),
    .ledger-table td:nth-child(6),
    .ledger-table td:nth-child(7) {
      text-align: right;
    }
    
    /* Center align for first column */
    .ledger-table td:first-child {
      text-align: center;
    }
  </style>
</x-layouts.admin>
