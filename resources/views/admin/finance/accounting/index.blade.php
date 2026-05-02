{{-- resources/views/admin/finance/accounting/index.blade.php --}}
<x-layouts.admin :title="'Finance & Accounting'">
  <div x-data="financeAccounting()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight" x-text="getTranslation('financeAccounting')"></h1>
        <p class="text-slate-600 text-sm" x-text="getTranslation('manageAccounts')"></p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Language Switch --}}
        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
          <i class='bx bx-globe text-slate-600'></i>
          <select x-model="currentLanguage" @change="changeLanguage()" class="bg-transparent text-sm focus:outline-none">
            <option value="id">🇮🇩 Indonesia</option>
            <option value="en">🇺🇸 English</option>
          </select>
        </div>

        <button @click="openCreateAccount()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700">
          <i class='bx bx-plus'></i> <span x-text="getTranslation('addAccount')"></span>
        </button>
        <button @click="openCreateBook()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-book'></i> <span x-text="getTranslation('createNewBook')"></span>
        </button>
        <button @click="openCreateJournal()" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 text-white px-4 h-10 hover:bg-purple-700">
          <i class='bx bx-edit'></i> <span x-text="getTranslation('createJournal')"></span>
        </button>
        <button @click="generateReport()" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 text-white px-4 h-10 hover:bg-orange-700">
          <i class='bx bx-file'></i> <span x-text="getTranslation('generateReport')"></span>
        </button>
      </div>
    </div>



    {{-- Infografis Dashboard --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Financial Health Chart --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800" x-text="getTranslation('financialHealth')"></h3>
          <select x-model="chartPeriod" @change="updateCharts()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
            <option value="monthly">Bulanan</option>
            <option value="quarterly">Triwulan</option>
            <option value="yearly">Tahunan</option>
          </select>
        </div>
        <div class="h-64">
          <canvas id="financialHealthChart"></canvas>
        </div>
      </div>

      {{-- Expense Distribution --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800" x-text="getTranslation('expenseDistribution')"></h3>
          <a href="{{ route('finance.biaya.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Lihat Detail <i class='bx bx-chevron-right'></i>
          </a>
        </div>
        <div class="h-64">
          <canvas id="expenseDistributionChart"></canvas>
        </div>
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-wallet text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(stats.totalAssets)"></div>
            <div class="text-sm text-slate-600" x-text="getTranslation('totalAssets')"></div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-trending-up text-green-500'></i>
          <span class="text-green-600">+12.5%</span>
          <span class="text-slate-500">dari bulan lalu</span>
        </div>
      </div>
      
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-trending-down text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(stats.totalLiabilities)"></div>
            <div class="text-sm text-slate-600" x-text="getTranslation('totalLiabilities')"></div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-trending-down text-red-500'></i>
          <span class="text-red-600">-3.2%</span>
          <span class="text-slate-500">dari bulan lalu</span>
        </div>
      </div>
      
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-trending-up text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(stats.totalEquity)"></div>
            <div class="text-sm text-slate-600" x-text="getTranslation('totalEquity')"></div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-trending-up text-green-500'></i>
          <span class="text-green-600">+8.7%</span>
          <span class="text-slate-500">dari bulan lalu</span>
        </div>
      </div>
      
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-line-chart text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(stats.netIncome)"></div>
            <div class="text-sm text-slate-600" x-text="getTranslation('netIncome')"></div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-trending-up text-green-500'></i>
          <span class="text-green-600">+15.3%</span>
          <span class="text-slate-500">dari bulan lalu</span>
        </div>
      </div>
    </div>

    {{-- Recent Activity & Key Metrics --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Recent Journal Entries --}}
      <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800" x-text="getTranslation('recentJournals')"></h3>
          <a href="#journal-entries" @click="activeTab = 'journal-entries'" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Lihat Semua <i class='bx bx-chevron-right'></i>
          </a>
        </div>
        <div class="space-y-3">
          <template x-for="journal in recentJournals" :key="journal.id">
            <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                  <i class='bx bx-edit text-blue-600'></i>
                </div>
                <div>
                  <div class="font-medium text-slate-800" x-text="journal.description"></div>
                  <div class="text-xs text-slate-500" x-text="journal.date_formatted"></div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-semibold" x-text="formatCurrency(journal.amount)"></div>
                <div class="text-xs" :class="journal.type === 'debit' ? 'text-green-600' : 'text-red-600'" 
                      x-text="journal.type === 'debit' ? 'Debit' : 'Kredit'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      {{-- Key Metrics --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('keyMetrics')"></h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class='bx bx-check-circle text-green-600'></i>
              </div>
              <span class="text-sm text-slate-600">ROI</span>
            </div>
            <span class="font-semibold text-green-600">18.5%</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class='bx bx-trending-up text-blue-600'></i>
              </div>
              <span class="text-sm text-slate-600">Current Ratio</span>
            </div>
            <span class="font-semibold text-blue-600">2.3</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class='bx bx-time text-purple-600'></i>
              </div>
              <span class="text-sm text-slate-600">Debt to Equity</span>
            </div>
            <span class="font-semibold text-purple-600">0.45</span>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                <i class='bx bx-dollar-circle text-orange-600'></i>
              </div>
              <span class="text-sm text-slate-600">Profit Margin</span>
            </div>
            <span class="font-semibold text-orange-600">22.8%</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-slate-200">
      <nav class="-mb-px flex space-x-8 overflow-x-auto">
        <button @click="activeTab = 'chart-of-accounts'" 
                :class="activeTab === 'chart-of-accounts' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-list-ul mr-2'></i><span x-text="getTranslation('chartOfAccounts')"></span>
        </button>
        <button @click="activeTab = 'accounting-books'" 
                :class="activeTab === 'accounting-books' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-book mr-2'></i><span x-text="getTranslation('accountingBooks')"></span>
        </button>
        <button @click="activeTab = 'journal-entries'" 
                :class="activeTab === 'journal-entries' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-edit-alt mr-2'></i><span x-text="getTranslation('journalEntries')"></span>
        </button>
        <button @click="activeTab = 'expenses'" 
                :class="activeTab === 'expenses' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-money mr-2'></i><span x-text="getTranslation('expenses')"></span>
        </button>
        <button @click="activeTab = 'general-ledger'" 
                :class="activeTab === 'general-ledger' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-book-open mr-2'></i><span x-text="getTranslation('generalLedger')"></span>
        </button>
        <button @click="activeTab = 'cash-flow'" 
                :class="activeTab === 'cash-flow' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-transfer mr-2'></i><span x-text="getTranslation('cashFlow')"></span>
        </button>
        <button @click="activeTab = 'fixed-assets'" 
                :class="activeTab === 'fixed-assets' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-building-house mr-2'></i><span x-text="getTranslation('fixedAssets')"></span>
        </button>
        <button @click="activeTab = 'trial-balance'" 
                :class="activeTab === 'trial-balance' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-balance mr-2'></i><span x-text="getTranslation('trialBalance')"></span>
        </button>
        <button @click="activeTab = 'balance-sheet'" 
                :class="activeTab === 'balance-sheet' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-spreadsheet mr-2'></i><span x-text="getTranslation('balanceSheet')"></span>
        </button>
        <button @click="activeTab = 'income-statement'" 
                :class="activeTab === 'income-statement' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-line-chart mr-2'></i><span x-text="getTranslation('incomeStatement')"></span>
        </button>
        <button @click="activeTab = 'opening-balance'" 
                :class="activeTab === 'opening-balance' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
          <i class='bx bx-transfer-alt mr-2'></i><span x-text="getTranslation('openingBalance')"></span>
        </button>
      </nav>
    </div>

    {{-- Tab Content --}}
    <div class="space-y-6">

      {{-- Chart of Accounts Tab --}}
      <div x-show="activeTab === 'chart-of-accounts'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('chartOfAccounts')"></h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filtersCoA.type" @change="loadChartOfAccounts()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allTypes')"></option>
              <option value="asset" x-text="getTranslation('asset')"></option>
              <option value="liability" x-text="getTranslation('liability')"></option>
              <option value="equity" x-text="getTranslation('equity')"></option>
              <option value="revenue" x-text="getTranslation('revenue')"></option>
              <option value="expense" x-text="getTranslation('expense')"></option>
            </select>
            <input type="text" x-model="filtersCoA.search" @input.debounce.500ms="loadChartOfAccounts()" 
                   :placeholder="getTranslation('searchCodeOrName')" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
            <button @click="exportCoA()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-export'></i> <span x-text="getTranslation('export')"></span>
            </button>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('code')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('accountName')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('type')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('balance')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('status')"></th>
                <th class="px-4 py-3 text-left w-32" x-text="getTranslation('actions')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(account, index) in coaData" :key="account.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <div x-html="account.code_formatted"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="account.name_with_indent"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="account.type_badge"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="account.balance_formatted"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="account.status_badge"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="account.actions"></div>
                  </td>
                </tr>
              </template>
              <tr x-show="coaData.length === 0 && !loadingCoA">
                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                  <i class='bx bx-wallet text-3xl mb-2 text-slate-300'></i>
                  <div x-text="getTranslation('noAccountData')"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Accounting Books Tab --}}
      <div x-show="activeTab === 'accounting-books'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('accountingBooks')"></h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filtersBooks.status" @change="loadAccountingBooks()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allStatus')"></option>
              <option value="active" x-text="getTranslation('active')"></option>
              <option value="inactive" x-text="getTranslation('inactive')"></option>
            </select>
            <input type="text" x-model="filtersBooks.search" @input.debounce.500ms="loadAccountingBooks()" 
                   :placeholder="getTranslation('searchBooks')" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <template x-for="book in booksData" :key="book.id">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition-shadow">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <div class="font-mono text-sm font-semibold text-slate-600" x-text="book.code"></div>
                  <div class="font-semibold text-slate-800" x-text="book.name"></div>
                </div>
                <div x-html="book.status_badge"></div>
              </div>
              <p class="text-sm text-slate-600 mb-4" x-text="book.description"></p>
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs text-slate-500" x-text="getTranslation('balance')"></div>
                  <div x-html="book.balance_formatted" class="font-semibold"></div>
                </div>
                <div x-html="book.actions"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      {{-- Journal Entries Tab --}}
      <div x-show="activeTab === 'journal-entries'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('journalEntries')"></h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filtersJournals.book_id" @change="loadJournalEntries()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allBooks')"></option>
              <option value="1" x-text="getTranslation('mainLedger')"></option>
              <option value="2" x-text="getTranslation('cashBook')"></option>
              <option value="3" x-text="getTranslation('bankBook')"></option>
            </select>
            <input type="date" x-model="filtersJournals.date_from" @change="loadJournalEntries()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" x-model="filtersJournals.date_to" @change="loadJournalEntries()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="text" x-model="filtersJournals.search" @input.debounce.500ms="loadJournalEntries()" 
                   :placeholder="getTranslation('searchJournals')" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('date')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('reference')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('description')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('debit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('credit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('balance')"></th>
                <th class="px-4 py-3 text-left w-24" x-text="getTranslation('actions')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(entry, index) in journalsData" :key="entry.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3" x-text="entry.date_formatted"></td>
                  <td class="px-4 py-3 font-mono text-sm" x-text="entry.reference"></td>
                  <td class="px-4 py-3" x-text="entry.description"></td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.debit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.credit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.balance_formatted"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="entry.actions"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Expenses Tab --}}
      <div x-show="activeTab === 'expenses'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('expenses')"></h2>
          <div class="flex flex-wrap gap-2">
            <button @click="openCreateExpense()" class="inline-flex items-center gap-2 rounded-xl bg-red-600 text-white px-4 h-10 hover:bg-red-700">
              <i class='bx bx-plus'></i> <span x-text="getTranslation('addExpense')"></span>
            </button>
            <select x-model="filtersExpenses.category" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allCategories')"></option>
              <option value="operational" x-text="getTranslation('operational')"></option>
              <option value="administrative" x-text="getTranslation('administrative')"></option>
              <option value="marketing" x-text="getTranslation('marketing')"></option>
              <option value="maintenance" x-text="getTranslation('maintenance')"></option>
            </select>
            <input type="date" x-model="filtersExpenses.date_from" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" x-model="filtersExpenses.date_to" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('date')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('reference')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('category')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('description')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('account')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('amount')"></th>
                <th class="px-4 py-3 text-left w-24" x-text="getTranslation('actions')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(expense, index) in expensesData" :key="expense.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3" x-text="expense.date_formatted"></td>
                  <td class="px-4 py-3 font-mono text-sm" x-text="expense.reference"></td>
                  <td class="px-4 py-3">
                    <div x-html="expense.category_badge"></div>
                  </td>
                  <td class="px-4 py-3" x-text="expense.description"></td>
                  <td class="px-4 py-3" x-text="expense.account_name"></td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="expense.amount_formatted"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="expense.actions"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      {{-- General Ledger Tab --}}
      <div x-show="activeTab === 'general-ledger'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('generalLedger')"></h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filtersLedger.account_id" @change="loadGeneralLedger()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allAccounts')"></option>
              <option value="1">1-1000 Kas</option>
              <option value="2">1-1100 Bank</option>
              <option value="3">1-1200 Piutang Usaha</option>
            </select>
            <input type="date" x-model="filtersLedger.date_from" @change="loadGeneralLedger()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" x-model="filtersLedger.date_to" @change="loadGeneralLedger()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button @click="exportLedger()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-export'></i> <span x-text="getTranslation('export')"></span>
            </button>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('date')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('reference')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('description')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('debit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('credit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('balance')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(entry, index) in ledgerData" :key="entry.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3" x-text="entry.date_formatted"></td>
                  <td class="px-4 py-3 font-mono text-sm" x-text="entry.reference"></td>
                  <td class="px-4 py-3" x-text="entry.description"></td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.debit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.credit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="entry.balance_formatted"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Cash Flow Tab --}}
      <div x-show="activeTab === 'cash-flow'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('cashFlow')"></h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filtersCashFlow.method" @change="loadCashFlow()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="direct" x-text="getTranslation('directMethod')"></option>
              <option value="indirect" x-text="getTranslation('indirectMethod')"></option>
            </select>
            <select x-model="filtersCashFlow.activity" @change="loadCashFlow()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allActivities')"></option>
              <option value="operating" x-text="getTranslation('operating')"></option>
              <option value="investing" x-text="getTranslation('investing')"></option>
              <option value="financing" x-text="getTranslation('financing')"></option>
            </select>
            <input type="date" x-model="filtersCashFlow.start_date" @change="loadCashFlow()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" x-model="filtersCashFlow.end_date" @change="loadCashFlow()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button @click="printCashFlow()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-printer'></i> <span x-text="getTranslation('print')"></span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('cashFlowStatement')"></h3>
            <div class="space-y-4">
              <template x-if="cashFlowData.operating">
                <div>
                  <h4 class="font-semibold text-slate-700 mb-2" x-text="getTranslation('operatingActivities')"></h4>
                  <div class="space-y-2">
                    <template x-for="(item, index) in cashFlowData.operating" :key="index">
                      <div class="flex justify-between items-center">
                        <span class="text-slate-600" x-text="item.description"></span>
                        <span :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold" x-text="formatCurrency(item.amount)"></span>
                      </div>
                    </template>
                  </div>
                  <div class="border-t border-slate-200 mt-2 pt-2">
                    <div class="flex justify-between items-center font-semibold">
                      <span x-text="getTranslation('netCashOperating')"></span>
                      <span x-text="formatCurrency(cashFlowData.net_operating)" class="text-green-600"></span>
                    </div>
                  </div>
                </div>
              </template>

              <template x-if="cashFlowData.investing">
                <div>
                  <h4 class="font-semibold text-slate-700 mb-2" x-text="getTranslation('investingActivities')"></h4>
                  <div class="space-y-2">
                    <template x-for="(item, index) in cashFlowData.investing" :key="index">
                      <div class="flex justify-between items-center">
                        <span class="text-slate-600" x-text="item.description"></span>
                        <span :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold" x-text="formatCurrency(item.amount)"></span>
                      </div>
                    </template>
                  </div>
                  <div class="border-t border-slate-200 mt-2 pt-2">
                    <div class="flex justify-between items-center font-semibold">
                      <span x-text="getTranslation('netCashInvesting')"></span>
                      <span x-text="formatCurrency(cashFlowData.net_investing)" class="text-red-600"></span>
                    </div>
                  </div>
                </div>
              </template>

              <template x-if="cashFlowData.financing">
                <div>
                  <h4 class="font-semibold text-slate-700 mb-2" x-text="getTranslation('financingActivities')"></h4>
                  <div class="space-y-2">
                    <template x-for="(item, index) in cashFlowData.financing" :key="index">
                      <div class="flex justify-between items-center">
                        <span class="text-slate-600" x-text="item.description"></span>
                        <span :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'" class="font-semibold" x-text="formatCurrency(item.amount)"></span>
                      </div>
                    </template>
                  </div>
                  <div class="border-t border-slate-200 mt-2 pt-2">
                    <div class="flex justify-between items-center font-semibold">
                      <span x-text="getTranslation('netCashFinancing')"></span>
                      <span x-text="formatCurrency(cashFlowData.net_financing)" class="text-green-600"></span>
                    </div>
                  </div>
                </div>
              </template>

              <div class="border-t-2 border-slate-300 mt-4 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                  <span x-text="getTranslation('netCashFlow')"></span>
                  <span x-text="formatCurrency(cashFlowData.net_cash_flow)" class="text-blue-600"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Fixed Assets Tab --}}
      <div x-show="activeTab === 'fixed-assets'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('fixedAssets')"></h2>
          <div class="flex flex-wrap gap-2">
            <button @click="openCreateFixedAsset()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
              <i class='bx bx-plus'></i> <span x-text="getTranslation('addFixedAsset')"></span>
            </button>
            <select x-model="filtersFixedAssets.category" @change="loadFixedAssets()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allCategories')"></option>
              <option value="land" x-text="getTranslation('land')"></option>
              <option value="building" x-text="getTranslation('building')"></option>
              <option value="vehicle" x-text="getTranslation('vehicle')"></option>
              <option value="equipment" x-text="getTranslation('equipment')"></option>
            </select>
            <select x-model="filtersFixedAssets.status" @change="loadFixedAssets()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allStatus')"></option>
              <option value="active" x-text="getTranslation('active')"></option>
              <option value="sold" x-text="getTranslation('sold')"></option>
              <option value="disposed" x-text="getTranslation('disposed')"></option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <template x-for="asset in fixedAssetsData" :key="asset.id">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card hover:shadow-lg transition-shadow">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <div class="font-semibold text-slate-800" x-text="asset.name"></div>
                  <div class="text-sm text-slate-600" x-text="asset.category"></div>
                </div>
                <div x-html="asset.status_badge"></div>
              </div>
              <div class="space-y-2 text-sm mb-4">
                <div class="flex justify-between">
                  <span class="text-slate-500" x-text="getTranslation('acquisitionDate')"></span>
                  <span x-text="asset.acquisition_date"></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-500" x-text="getTranslation('acquisitionCost')"></span>
                  <span x-html="asset.acquisition_cost_formatted"></span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-500" x-text="getTranslation('depreciation')"></span>
                  <span x-html="asset.depreciation_formatted"></span>
                </div>
                <div class="flex justify-between font-semibold">
                  <span class="text-slate-700" x-text="getTranslation('bookValue')"></span>
                  <span x-html="asset.book_value_formatted"></span>
                </div>
              </div>
              <div class="flex justify-between">
                <div>
                  <div class="text-xs text-slate-500" x-text="getTranslation('usefulLife')"></div>
                  <div class="font-semibold" x-text="asset.useful_life + ' ' + getTranslation('years')"></div>
                </div>
                <div x-html="asset.actions"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      {{-- Trial Balance Tab --}}
      <div x-show="activeTab === 'trial-balance'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('trialBalance')"></h2>
          <div class="flex flex-wrap gap-2">
            <input type="date" x-model="filtersTrialBalance.date" @change="loadTrialBalance()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <select x-model="filtersTrialBalance.book_id" @change="loadTrialBalance()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all" x-text="getTranslation('allBooks')"></option>
              <option value="1" x-text="getTranslation('mainLedger')"></option>
            </select>
            <button @click="exportTrialBalance()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-export'></i> <span x-text="getTranslation('export')"></span>
            </button>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('accountCode')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('accountName')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('debit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('credit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('balance')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(item, index) in trialBalanceData" :key="index">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <div x-html="item.account_code"></div>
                  </td>
                  <td class="px-4 py-3" x-text="item.account_name"></td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="item.debit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="item.credit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="item.balance_formatted"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      {{-- Balance Sheet Tab --}}
      <div x-show="activeTab === 'balance-sheet'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('balanceSheet')"></h2>
          <div class="flex flex-wrap gap-2">
            <input type="date" x-model="filtersBalanceSheet.date" @change="loadBalanceSheet()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button @click="printBalanceSheet()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-printer'></i> <span x-text="getTranslation('print')"></span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {{-- Assets --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('assets')"></h3>
            <div class="space-y-3">
              <template x-if="balanceSheetData.assets">
                <template x-for="(section, sectionName) in balanceSheetData.assets">
                  <div>
                    <h4 class="font-semibold text-slate-700 mb-2" x-text="formatSectionName(sectionName)"></h4>
                    <div class="space-y-2">
                      <template x-for="(amount, accountName) in section">
                        <div class="flex justify-between items-center" x-show="!accountName.includes('Total')">
                          <span class="text-slate-600" x-text="accountName"></span>
                          <span class="font-semibold text-green-600" x-text="formatCurrency(amount)"></span>
                        </div>
                      </template>
                    </div>
                    <div class="border-t border-slate-200 mt-2 pt-2" x-show="sectionName.includes('current_assets') || sectionName.includes('fixed_assets')">
                      <div class="flex justify-between items-center font-semibold">
                        <span x-text="'Total ' + formatSectionName(sectionName)"></span>
                        <span x-text="formatCurrency(section['Total ' + formatSectionName(sectionName)])" class="text-green-600"></span>
                      </div>
                    </div>
                  </div>
                </template>
              </template>
              <div class="border-t-2 border-slate-300 mt-4 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                  <span x-text="getTranslation('totalAssets')"></span>
                  <span x-text="formatCurrency(balanceSheetData.total_assets)" class="text-green-600"></span>
                </div>
              </div>
            </div>
          </div>

          {{-- Liabilities & Equity --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('liabilitiesEquity')"></h3>
            <div class="space-y-4">
              {{-- Liabilities --}}
              <div>
                <h4 class="font-semibold text-slate-700 mb-2" x-text="getTranslation('liabilities')"></h4>
                <template x-if="balanceSheetData.liabilities">
                  <template x-for="(section, sectionName) in balanceSheetData.liabilities">
                    <div>
                      <div class="space-y-2">
                        <template x-for="(amount, accountName) in section">
                          <div class="flex justify-between items-center" x-show="!accountName.includes('Total')">
                            <span class="text-slate-600" x-text="accountName"></span>
                            <span class="font-semibold text-red-600" x-text="formatCurrency(amount)"></span>
                          </div>
                        </template>
                      </div>
                      <div class="border-t border-slate-200 mt-2 pt-2" x-show="sectionName.includes('current_liabilities') || sectionName.includes('long_term_liabilities')">
                        <div class="flex justify-between items-center font-semibold">
                          <span x-text="'Total ' + formatSectionName(sectionName)"></span>
                          <span x-text="formatCurrency(section['Total ' + formatSectionName(sectionName)])" class="text-red-600"></span>
                        </div>
                      </div>
                    </div>
                  </template>
                </template>
                <div class="border-t border-slate-300 mt-2 pt-2">
                  <div class="flex justify-between items-center font-bold">
                    <span x-text="getTranslation('totalLiabilities')"></span>
                    <span x-text="formatCurrency(balanceSheetData.total_liabilities)" class="text-red-600"></span>
                  </div>
                </div>
              </div>

              {{-- Equity --}}
              <div>
                <h4 class="font-semibold text-slate-700 mb-2" x-text="getTranslation('equity')"></h4>
                <template x-if="balanceSheetData.equity">
                  <div class="space-y-2">
                    <template x-for="(amount, accountName) in balanceSheetData.equity">
                      <div class="flex justify-between items-center" x-show="!accountName.includes('Total')">
                        <span class="text-slate-600" x-text="accountName"></span>
                        <span class="font-semibold text-blue-600" x-text="formatCurrency(amount)"></span>
                      </div>
                    </template>
                  </div>
                </template>
                <div class="border-t border-slate-300 mt-2 pt-2">
                  <div class="flex justify-between items-center font-bold">
                    <span x-text="getTranslation('totalEquity')"></span>
                    <span x-text="formatCurrency(balanceSheetData.total_equity)" class="text-blue-600"></span>
                  </div>
                </div>
              </div>

              <div class="border-t-2 border-slate-300 mt-4 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                  <span x-text="getTranslation('totalLiabilitiesEquity')"></span>
                  <span x-text="formatCurrency(balanceSheetData.total_liabilities_equity)" class="text-purple-600"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Income Statement Tab --}}
      <div x-show="activeTab === 'income-statement'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('incomeStatement')"></h2>
          <div class="flex flex-wrap gap-2">
            <input type="date" x-model="filtersIncomeStatement.start_date" @change="loadIncomeStatement()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" x-model="filtersIncomeStatement.end_date" @change="loadIncomeStatement()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button @click="printIncomeStatement()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-printer'></i> <span x-text="getTranslation('print')"></span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {{-- Revenues --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('revenues')"></h3>
            <div class="space-y-3">
              <template x-if="incomeStatementData.revenues">
                <template x-for="(amount, accountName) in incomeStatementData.revenues">
                  <div class="flex justify-between items-center" x-show="!accountName.includes('Total')">
                    <span class="text-slate-600" x-text="accountName"></span>
                    <span class="font-semibold text-green-600" x-text="formatCurrency(amount)"></span>
                  </div>
                </template>
              </template>
              <div class="border-t-2 border-slate-300 mt-4 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                  <span x-text="getTranslation('totalRevenue')"></span>
                  <span x-text="formatCurrency(incomeStatementData.revenues?.Total_Pendapatan || 0)" class="text-green-600"></span>
                </div>
              </div>
            </div>
          </div>

          {{-- Expenses --}}
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <h3 class="text-lg font-semibold text-slate-800 mb-4" x-text="getTranslation('expenses')"></h3>
            <div class="space-y-3">
              <template x-if="incomeStatementData.expenses">
                <template x-for="(amount, accountName) in incomeStatementData.expenses">
                  <div class="flex justify-between items-center" x-show="!accountName.includes('Total')">
                    <span class="text-slate-600" x-text="accountName"></span>
                    <span class="font-semibold text-red-600" x-text="formatCurrency(amount)"></span>
                  </div>
                </template>
              </template>
              <div class="border-t-2 border-slate-300 mt-4 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                  <span x-text="getTranslation('totalExpenses')"></span>
                  <span x-text="formatCurrency(incomeStatementData.expenses?.Total_Beban || 0)" class="text-red-600"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Net Income --}}
        <div class="rounded-2xl border-2 border-green-200 bg-green-50 p-6">
          <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-green-800" x-text="getTranslation('netIncome')"></span>
            <span class="text-2xl font-bold text-green-600" x-text="formatCurrency(incomeStatementData.net_income || 0)"></span>
          </div>
        </div>
      </div>

      {{-- Opening Balance Tab --}}
      <div x-show="activeTab === 'opening-balance'" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800" x-text="getTranslation('openingBalance')"></h2>
          <div class="flex flex-wrap gap-2">
            <button @click="importOpeningBalance()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-import'></i> <span x-text="getTranslation('import')"></span>
            </button>
            <button @click="exportOpeningBalance()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
              <i class='bx bx-export'></i> <span x-text="getTranslation('export')"></span>
            </button>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12" x-text="getTranslation('no')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('accountCode')"></th>
                <th class="px-4 py-3 text-left" x-text="getTranslation('accountName')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('debit')"></th>
                <th class="px-4 py-3 text-right" x-text="getTranslation('credit')"></th>
                <th class="px-4 py-3 text-left w-24" x-text="getTranslation('actions')"></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(item, index) in openingBalanceData" :key="item.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <div x-html="item.account_code"></div>
                  </td>
                  <td class="px-4 py-3" x-text="item.account_name"></td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="item.debit_formatted"></div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div x-html="item.credit_formatted"></div>
                  </td>
                  <td class="px-4 py-3">
                    <div x-html="item.actions"></div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span x-text="getTranslation('loadingData')"></span>
      </div>
    </div>

    {{-- Toast Notification --}}
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
    function financeAccounting() {
      return {
        activeTab: 'chart-of-accounts',
        loading: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        currentLanguage: 'id',

        // Translations
        translations: {
          // Indonesian (default)
          id: {
            financeAccounting: 'Finance & Accounting',
            manageAccounts: 'Kelola akun, buku, jurnal, dan laporan keuangan.',
            addAccount: 'Tambah Akun',
            createNewBook: 'Buat Buku Baru',
            createJournal: 'Buat Jurnal',
            generateReport: 'Generate Report',
            totalAssets: 'Total Aset',
            totalLiabilities: 'Total Kewajiban',
            totalEquity: 'Total Ekuitas',
            netIncome: 'Laba Bersih',
            chartOfAccounts: 'Chart of Accounts',
            accountingBooks: 'Accounting Books',
            journalEntries: 'Journal Entries',
            expenses: 'Biaya',
            generalLedger: 'Buku Besar',
            cashFlow: 'Arus Kas',
            fixedAssets: 'Aktiva Tetap',
            trialBalance: 'Trial Balance',
            balanceSheet: 'Balance Sheet',
            incomeStatement: 'Income Statement',
            openingBalance: 'Opening Balance',
            allTypes: 'Semua Tipe',
            asset: 'Aset',
            liability: 'Kewajiban',
            equity: 'Ekuitas',
            revenue: 'Pendapatan',
            expense: 'Beban',
            searchCodeOrName: 'Cari kode atau nama akun...',
            export: 'Export',
            no: 'No',
            code: 'Kode',
            accountName: 'Nama Akun',
            type: 'Tipe',
            balance: 'Saldo',
            status: 'Status',
            actions: 'Aksi',
            noAccountData: 'Tidak ada data akun',
            allStatus: 'Semua Status',
            active: 'Aktif',
            inactive: 'Nonaktif',
            searchBooks: 'Cari buku...',
            allBooks: 'Semua Buku',
            mainLedger: 'Buku Besar Utama',
            cashBook: 'Buku Kas',
            bankBook: 'Buku Bank',
            date: 'Tanggal',
            reference: 'Referensi',
            description: 'Keterangan',
            debit: 'Debit',
            credit: 'Kredit',
            searchJournals: 'Cari jurnal...',
            addExpense: 'Tambah Biaya',
            allCategories: 'Semua Kategori',
            operational: 'Operasional',
            administrative: 'Administratif',
            marketing: 'Pemasaran',
            maintenance: 'Pemeliharaan',
            category: 'Kategori',
            amount: 'Jumlah',
            allAccounts: 'Semua Akun',
            directMethod: 'Metode Langsung',
            indirectMethod: 'Metode Tidak Langsung',
            allActivities: 'Semua Aktivitas',
            operating: 'Operasional',
            investing: 'Investasi',
            financing: 'Pendanaan',
            print: 'Print',
            cashFlowStatement: 'Laporan Arus Kas',
            operatingActivities: 'Aktivitas Operasi',
            netCashOperating: 'Kas Bersih dari Operasi',
            investingActivities: 'Aktivitas Investasi',
            netCashInvesting: 'Kas Bersih dari Investasi',
            financingActivities: 'Aktivitas Pendanaan',
            netCashFinancing: 'Kas Bersih dari Pendanaan',
            netCashFlow: 'Kenaikan/Penurunan Kas Bersih',
            addFixedAsset: 'Tambah Aktiva Tetap',
            land: 'Tanah',
            building: 'Bangunan',
            vehicle: 'Kendaraan',
            equipment: 'Peralatan',
            sold: 'Terjual',
            disposed: 'Dibuang',
            acquisitionDate: 'Tanggal Perolehan',
            acquisitionCost: 'Biaya Perolehan',
            depreciation: 'Penyusutan',
            bookValue: 'Nilai Buku',
            usefulLife: 'Masa Manfaat',
            years: 'tahun',
            accountCode: 'Kode Akun',
            assets: 'ASSETS',
            liabilitiesEquity: 'LIABILITIES & EQUITY',
            liabilities: 'LIABILITIES',
            totalLiabilitiesEquity: 'TOTAL LIABILITIES & EQUITY',
            revenues: 'REVENUES',
            totalRevenue: 'TOTAL REVENUE',
            totalExpenses: 'TOTAL EXPENSES',
            import: 'Import',
            loadingData: 'Memuat data...'
          },
          // English
          en: {
            financeAccounting: 'Finance & Accounting',
            manageAccounts: 'Manage accounts, books, journals, and financial reports.',
            addAccount: 'Add Account',
            createNewBook: 'Create New Book',
            createJournal: 'Create Journal',
            generateReport: 'Generate Report',
            totalAssets: 'Total Assets',
            totalLiabilities: 'Total Liabilities',
            totalEquity: 'Total Equity',
            netIncome: 'Net Income',
            chartOfAccounts: 'Chart of Accounts',
            accountingBooks: 'Accounting Books',
            journalEntries: 'Journal Entries',
            expenses: 'Expenses',
            generalLedger: 'General Ledger',
            cashFlow: 'Cash Flow',
            fixedAssets: 'Fixed Assets',
            trialBalance: 'Trial Balance',
            balanceSheet: 'Balance Sheet',
            incomeStatement: 'Income Statement',
            openingBalance: 'Opening Balance',
            allTypes: 'All Types',
            asset: 'Asset',
            liability: 'Liability',
            equity: 'Equity',
            revenue: 'Revenue',
            expense: 'Expense',
            searchCodeOrName: 'Search code or account name...',
            export: 'Export',
            no: 'No',
            code: 'Code',
            accountName: 'Account Name',
            type: 'Type',
            balance: 'Balance',
            status: 'Status',
            actions: 'Actions',
            noAccountData: 'No account data',
            allStatus: 'All Status',
            active: 'Active',
            inactive: 'Inactive',
            searchBooks: 'Search books...',
            allBooks: 'All Books',
            mainLedger: 'Main Ledger',
            cashBook: 'Cash Book',
            bankBook: 'Bank Book',
            date: 'Date',
            reference: 'Reference',
            description: 'Description',
            debit: 'Debit',
            credit: 'Credit',
            searchJournals: 'Search journals...',
            addExpense: 'Add Expense',
            allCategories: 'All Categories',
            operational: 'Operational',
            administrative: 'Administrative',
            marketing: 'Marketing',
            maintenance: 'Maintenance',
            category: 'Category',
            amount: 'Amount',
            allAccounts: 'All Accounts',
            directMethod: 'Direct Method',
            indirectMethod: 'Indirect Method',
            allActivities: 'All Activities',
            operating: 'Operating',
            investing: 'Investing',
            financing: 'Financing',
            print: 'Print',
            cashFlowStatement: 'Cash Flow Statement',
            operatingActivities: 'Operating Activities',
            netCashOperating: 'Net Cash from Operating',
            investingActivities: 'Investing Activities',
            netCashInvesting: 'Net Cash from Investing',
            financingActivities: 'Financing Activities',
            netCashFinancing: 'Net Cash from Financing',
            netCashFlow: 'Net Increase/Decrease in Cash',
            addFixedAsset: 'Add Fixed Asset',
            land: 'Land',
            building: 'Building',
            vehicle: 'Vehicle',
            equipment: 'Equipment',
            sold: 'Sold',
            disposed: 'Disposed',
            acquisitionDate: 'Acquisition Date',
            acquisitionCost: 'Acquisition Cost',
            depreciation: 'Depreciation',
            bookValue: 'Book Value',
            usefulLife: 'Useful Life',
            years: 'years',
            accountCode: 'Account Code',
            assets: 'ASSETS',
            liabilitiesEquity: 'LIABILITIES & EQUITY',
            liabilities: 'LIABILITIES',
            totalLiabilitiesEquity: 'TOTAL LIABILITIES & EQUITY',
            revenues: 'REVENUES',
            totalRevenue: 'TOTAL REVENUE',
            totalExpenses: 'TOTAL EXPENSES',
            import: 'Import',
            loadingData: 'Loading data...'
          }
        },

        // Stats
        stats: {
          totalAssets: 250000000,
          totalLiabilities: 120000000,
          totalEquity: 130000000,
          netIncome: 35000000
        },

        // Data
        coaData: [],
        booksData: [],
        journalsData: [],
        expensesData: [],
        ledgerData: [],
        cashFlowData: {},
        fixedAssetsData: [],
        trialBalanceData: [],
        balanceSheetData: {},
        incomeStatementData: {},
        openingBalanceData: [],

        // Filters
        filtersCoA: {
          type: 'all',
          search: ''
        },
        filtersBooks: {
          status: 'all',
          search: ''
        },
        filtersJournals: {
          book_id: 'all',
          date_from: '',
          date_to: '',
          search: ''
        },
        filtersExpenses: {
          category: 'all',
          date_from: '',
          date_to: ''
        },
        filtersLedger: {
          account_id: 'all',
          date_from: '',
          date_to: ''
        },
        filtersCashFlow: {
          method: 'direct',
          activity: 'all',
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0]
        },
        filtersFixedAssets: {
          category: 'all',
          status: 'all'
        },
        filtersTrialBalance: {
          date: new Date().toISOString().split('T')[0],
          book_id: 'all'
        },
        filtersBalanceSheet: {
          date: new Date().toISOString().split('T')[0]
        },
        filtersIncomeStatement: {
          start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
          end_date: new Date().toISOString().split('T')[0]
        },

        // Loading states
        loadingCoA: false,
        loadingBooks: false,
        loadingJournals: false,
        loadingExpenses: false,
        loadingLedger: false,
        loadingCashFlow: false,
        loadingFixedAssets: false,
        loadingTrialBalance: false,
        loadingBalanceSheet: false,
        loadingIncomeStatement: false,
        loadingOpeningBalance: false,

        async init() {
          await this.loadChartOfAccounts();
          await this.loadAccountingBooks();
          await this.loadJournalEntries();
          await this.loadExpenses();
          await this.loadGeneralLedger();
          await this.loadCashFlow();
          await this.loadFixedAssets();
          await this.loadTrialBalance();
          await this.loadBalanceSheet();
          await this.loadIncomeStatement();
          await this.loadOpeningBalance();
        },

        // Language methods
        getTranslation(key) {
          return this.translations[this.currentLanguage][key] || key;
        },

        changeLanguage() {
          // No need to do anything else, Alpine.js will automatically update all x-text bindings
        },

        // Data loading methods
        async loadChartOfAccounts() {
          this.loadingCoA = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Chart of Accounts
            this.coaData = [
              {
                id: 1,
                code_formatted: '<span class="font-mono">1-1000</span>',
                name_with_indent: 'Kas',
                type_badge: '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Aset</span>',
                balance_formatted: this.formatCurrency(50000000),
                status_badge: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Aktif</span>',
                actions: '<button @click="editAccount(1)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              },
              {
                id: 2,
                code_formatted: '<span class="font-mono">1-1100</span>',
                name_with_indent: 'Bank',
                type_badge: '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Aset</span>',
                balance_formatted: this.formatCurrency(150000000),
                status_badge: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Aktif</span>',
                actions: '<button @click="editAccount(2)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading chart of accounts:', error);
            this.showToastMessage('Gagal memuat data akun', 'error');
          } finally {
            this.loadingCoA = false;
          }
        },

        async loadAccountingBooks() {
          this.loadingBooks = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Accounting Books
            this.booksData = [
              {
                id: 1,
                code: 'BB-001',
                name: 'Buku Besar Utama',
                description: 'Buku besar utama perusahaan',
                balance_formatted: this.formatCurrency(200000000),
                status_badge: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Aktif</span>',
                actions: '<button @click="viewBook(1)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-show"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading accounting books:', error);
            this.showToastMessage('Gagal memuat data buku', 'error');
          } finally {
            this.loadingBooks = false;
          }
        },

        async loadJournalEntries() {
          this.loadingJournals = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Journal Entries
            this.journalsData = [
              {
                id: 1,
                date_formatted: '15 Mar 2024',
                reference: 'JU-001',
                description: 'Pembayaran gaji karyawan',
                debit_formatted: this.formatCurrency(0),
                credit_formatted: this.formatCurrency(25000000),
                balance_formatted: this.formatCurrency(-25000000),
                actions: '<button @click="viewJournal(1)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bx bx-show"></i></button><button @click="printJournal(1)" class="text-green-600 hover:text-green-800"><i class="bx bx-printer"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading journal entries:', error);
            this.showToastMessage('Gagal memuat data jurnal', 'error');
          } finally {
            this.loadingJournals = false;
          }
        },

        async loadExpenses() {
          this.loadingExpenses = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Expenses
            this.expensesData = [
              {
                id: 1,
                date_formatted: '15 Mar 2024',
                reference: 'EXP-001',
                category_badge: '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Operasional</span>',
                description: 'Pembayaran listrik bulan Maret',
                account_name: 'Beban Listrik',
                amount_formatted: this.formatCurrency(5000000),
                actions: '<button @click="editExpense(1)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              },
              {
                id: 2,
                date_formatted: '18 Mar 2024',
                reference: 'EXP-002',
                category_badge: '<span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">Administratif</span>',
                description: 'Pembelian alat tulis kantor',
                account_name: 'Beban Administrasi',
                amount_formatted: this.formatCurrency(1500000),
                actions: '<button @click="editExpense(2)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading expenses:', error);
            this.showToastMessage('Gagal memuat data biaya', 'error');
          } finally {
            this.loadingExpenses = false;
          }
        },

        async loadGeneralLedger() {
          this.loadingLedger = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for General Ledger
            this.ledgerData = [
              {
                id: 1,
                date_formatted: '01 Mar 2024',
                reference: 'BB-001',
                description: 'Saldo awal',
                debit_formatted: this.formatCurrency(50000000),
                credit_formatted: this.formatCurrency(0),
                balance_formatted: this.formatCurrency(50000000)
              },
              {
                id: 2,
                date_formatted: '15 Mar 2024',
                reference: 'JU-001',
                description: 'Penerimaan dari pelanggan',
                debit_formatted: this.formatCurrency(25000000),
                credit_formatted: this.formatCurrency(0),
                balance_formatted: this.formatCurrency(75000000)
              }
            ];
          } catch (error) {
            console.error('Error loading general ledger:', error);
            this.showToastMessage('Gagal memuat data buku besar', 'error');
          } finally {
            this.loadingLedger = false;
          }
        },

        async loadCashFlow() {
          this.loadingCashFlow = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Cash Flow
            this.cashFlowData = {
              operating: [
                { description: 'Penerimaan dari pelanggan', amount: 75000000 },
                { description: 'Pembayaran kepada supplier', amount: -45000000 },
                { description: 'Pembayaran gaji karyawan', amount: -25000000 }
              ],
              net_operating: 5000000,
              investing: [
                { description: 'Pembelian peralatan', amount: -15000000 },
                { description: 'Penjualan kendaraan', amount: 10000000 }
              ],
              net_investing: -5000000,
              financing: [
                { description: 'Penerimaan pinjaman bank', amount: 30000000 },
                { description: 'Pembayaran dividen', amount: -10000000 }
              ],
              net_financing: 20000000,
              net_cash_flow: 20000000
            };
          } catch (error) {
            console.error('Error loading cash flow:', error);
            this.showToastMessage('Gagal memuat data arus kas', 'error');
          } finally {
            this.loadingCashFlow = false;
          }
        },

        async loadFixedAssets() {
          this.loadingFixedAssets = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Fixed Assets
            this.fixedAssetsData = [
              {
                id: 1,
                name: 'Gedung Kantor Pusat',
                category: 'Bangunan',
                acquisition_date: '15 Jan 2020',
                acquisition_cost_formatted: this.formatCurrency(500000000),
                depreciation_formatted: this.formatCurrency(50000000),
                book_value_formatted: this.formatCurrency(450000000),
                useful_life: 20,
                status_badge: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Aktif</span>',
                actions: '<button @click="viewFixedAsset(1)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-show"></i></button>'
              },
              {
                id: 2,
                name: 'Mobil Operasional',
                category: 'Kendaraan',
                acquisition_date: '20 Mar 2022',
                acquisition_cost_formatted: this.formatCurrency(250000000),
                depreciation_formatted: this.formatCurrency(50000000),
                book_value_formatted: this.formatCurrency(200000000),
                useful_life: 5,
                status_badge: '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Aktif</span>',
                actions: '<button @click="viewFixedAsset(2)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-show"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading fixed assets:', error);
            this.showToastMessage('Gagal memuat data aktiva tetap', 'error');
          } finally {
            this.loadingFixedAssets = false;
          }
        },

        async loadTrialBalance() {
          this.loadingTrialBalance = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Trial Balance
            this.trialBalanceData = [
              {
                account_code: '1-1000',
                account_name: 'Kas',
                debit_formatted: this.formatCurrency(75000000),
                credit_formatted: this.formatCurrency(0),
                balance_formatted: this.formatCurrency(75000000)
              },
              {
                account_code: '1-1100',
                account_name: 'Bank',
                debit_formatted: this.formatCurrency(150000000),
                credit_formatted: this.formatCurrency(0),
                balance_formatted: this.formatCurrency(150000000)
              }
            ];
          } catch (error) {
            console.error('Error loading trial balance:', error);
            this.showToastMessage('Gagal memuat data trial balance', 'error');
          } finally {
            this.loadingTrialBalance = false;
          }
        },

        async loadBalanceSheet() {
          this.loadingBalanceSheet = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Balance Sheet
            this.balanceSheetData = {
              assets: {
                current_assets: {
                  'Kas': 50000000,
                  'Bank': 150000000,
                  'Piutang Usaha': 75000000,
                  'Persediaan': 100000000,
                  'Total Aktiva Lancar': 375000000
                },
                fixed_assets: {
                  'Tanah': 200000000,
                  'Gedung': 450000000,
                  'Kendaraan': 200000000,
                  'Peralatan': 50000000,
                  'Total Aktiva Tetap': 900000000
                }
              },
              total_assets: 1275000000,
              liabilities: {
                current_liabilities: {
                  'Hutang Usaha': 75000000,
                  'Hutang Bank Jangka Pendek': 100000000,
                  'Total Kewajiban Lancar': 175000000
                },
                long_term_liabilities: {
                  'Hutang Bank Jangka Panjang': 300000000,
                  'Total Kewajiban Jangka Panjang': 300000000
                }
              },
              total_liabilities: 475000000,
              equity: {
                'Modal Saham': 500000000,
                'Laba Ditahan': 250000000,
                'Laba Tahun Berjalan': 50000000
              },
              total_equity: 800000000,
              total_liabilities_equity: 1275000000
            };
          } catch (error) {
            console.error('Error loading balance sheet:', error);
            this.showToastMessage('Gagal memuat data balance sheet', 'error');
          } finally {
            this.loadingBalanceSheet = false;
          }
        },

        async loadIncomeStatement() {
          this.loadingIncomeStatement = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Income Statement
            this.incomeStatementData = {
              revenues: {
                'Penjualan': 250000000,
                'Pendapatan Jasa': 50000000,
                'Total_Pendapatan': 300000000
              },
              expenses: {
                'Harga Pokok Penjualan': 120000000,
                'Beban Gaji': 50000000,
                'Beban Sewa': 25000000,
                'Beban Listrik': 10000000,
                'Beban Administrasi': 15000000,
                'Total_Beban': 220000000
              },
              net_income: 80000000
            };
          } catch (error) {
            console.error('Error loading income statement:', error);
            this.showToastMessage('Gagal memuat data income statement', 'error');
          } finally {
            this.loadingIncomeStatement = false;
          }
        },

        async loadOpeningBalance() {
          this.loadingOpeningBalance = true;
          try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Dummy data for Opening Balance
            this.openingBalanceData = [
              {
                id: 1,
                account_code: '1-1000',
                account_name: 'Kas',
                debit_formatted: this.formatCurrency(50000000),
                credit_formatted: this.formatCurrency(0),
                actions: '<button @click="editOpeningBalance(1)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              },
              {
                id: 2,
                account_code: '1-1100',
                account_name: 'Bank',
                debit_formatted: this.formatCurrency(150000000),
                credit_formatted: this.formatCurrency(0),
                actions: '<button @click="editOpeningBalance(2)" class="text-blue-600 hover:text-blue-800"><i class="bx bx-edit"></i></button>'
              }
            ];
          } catch (error) {
            console.error('Error loading opening balance:', error);
            this.showToastMessage('Gagal memuat data saldo awal', 'error');
          } finally {
            this.loadingOpeningBalance = false;
          }
        },

        // Utility methods
        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(amount);
        },

        formatSectionName(sectionName) {
          const names = {
            'current_assets': 'Aktiva Lancar',
            'fixed_assets': 'Aktiva Tetap',
            'current_liabilities': 'Kewajiban Lancar',
            'long_term_liabilities': 'Kewajiban Jangka Panjang'
          };
          return names[sectionName] || sectionName;
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        // Demo CRUD operations
        openCreateAccount() {
          this.showToastMessage('Fitur tambah akun akan segera tersedia', 'info');
        },

        openCreateBook() {
          this.showToastMessage('Fitur buat buku baru akan segera tersedia', 'info');
        },

        openCreateJournal() {
          this.showToastMessage('Fitur buat jurnal akan segera tersedia', 'info');
        },

        openCreateExpense() {
          this.showToastMessage('Fitur tambah biaya akan segera tersedia', 'info');
        },

        openCreateFixedAsset() {
          this.showToastMessage('Fitur tambah aktiva tetap akan segera tersedia', 'info');
        },

        generateReport() {
          this.showToastMessage('Fitur generate report akan segera tersedia', 'info');
        },

        editAccount(id) {
          this.showToastMessage(`Edit akun ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        viewAccount(id) {
          this.showToastMessage(`View akun ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        toggleAccount(id, status) {
          this.showToastMessage(`Toggle akun ID: ${id} ke status: ${status} - Fitur akan segera tersedia`, 'info');
        },

        viewBook(id) {
          this.showToastMessage(`View buku ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        editBook(id) {
          this.showToastMessage(`Edit buku ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        toggleBook(id, status) {
          this.showToastMessage(`Toggle buku ID: ${id} ke status: ${status} - Fitur akan segera tersedia`, 'info');
        },

        viewJournal(id) {
          this.showToastMessage(`View jurnal ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        printJournal(id) {
          this.showToastMessage(`Print jurnal ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        editExpense(id) {
          this.showToastMessage(`Edit biaya ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        viewFixedAsset(id) {
          this.showToastMessage(`View aktiva tetap ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        editOpeningBalance(id) {
          this.showToastMessage(`Edit saldo awal ID: ${id} - Fitur akan segera tersedia`, 'info');
        },

        exportCoA() {
          this.showToastMessage('Export Chart of Accounts - Fitur akan segera tersedia', 'info');
        },

        exportLedger() {
          this.showToastMessage('Export General Ledger - Fitur akan segera tersedia', 'info');
        },

        exportTrialBalance() {
          this.showToastMessage('Export Trial Balance - Fitur akan segera tersedia', 'info');
        },

        printBalanceSheet() {
          this.showToastMessage('Print Balance Sheet - Fitur akan segera tersedia', 'info');
        },

        printIncomeStatement() {
          this.showToastMessage('Print Income Statement - Fitur akan segera tersedia', 'info');
        },

        printCashFlow() {
          this.showToastMessage('Print Cash Flow - Fitur akan segera tersedia', 'info');
        },

        importOpeningBalance() {
          this.showToastMessage('Import Opening Balance - Fitur akan segera tersedia', 'info');
        },

        exportOpeningBalance() {
          this.showToastMessage('Export Opening Balance - Fitur akan segera tersedia', 'info');
        },

        chartPeriod: 'monthly',
        recentJournals: [
          {
            id: 1,
            description: 'Pembayaran gaji karyawan',
            date_formatted: '15 Mar 2024',
            amount: 25000000,
            type: 'credit'
          },
          {
            id: 2,
            description: 'Penerimaan dari pelanggan',
            date_formatted: '14 Mar 2024',
            amount: 75000000,
            type: 'debit'
          },
          {
            id: 3,
            description: 'Pembayaran supplier',
            date_formatted: '12 Mar 2024',
            amount: 45000000,
            type: 'credit'
          }
        ],

        async init() {
          await this.loadChartOfAccounts();
          await this.loadAccountingBooks();
          await this.loadJournalEntries();
          await this.loadGeneralLedger();
          await this.loadCashFlow();
          await this.loadFixedAssets();
          await this.loadTrialBalance();
          await this.loadBalanceSheet();
          await this.loadIncomeStatement();
          await this.loadOpeningBalance();
          this.initCharts();
        },

        initCharts() {
          // Financial Health Chart
          const financialCtx = document.getElementById('financialHealthChart').getContext('2d');
          this.financialChart = new Chart(financialCtx, {
            type: 'line',
            data: {
              labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
              datasets: [
                {
                  label: 'Pendapatan',
                  data: [120000000, 150000000, 180000000, 160000000, 200000000, 220000000],
                  borderColor: '#10b981',
                  backgroundColor: 'rgba(16, 185, 129, 0.1)',
                  tension: 0.4,
                  fill: true
                },
                {
                  label: 'Pengeluaran',
                  data: [80000000, 90000000, 100000000, 95000000, 110000000, 120000000],
                  borderColor: '#ef4444',
                  backgroundColor: 'rgba(239, 68, 68, 0.1)',
                  tension: 0.4,
                  fill: true
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'top',
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return 'Rp ' + (value / 1000000) + 'Jt';
                    }
                  }
                }
              }
            }
          });

          // Expense Distribution Chart
          const expenseCtx = document.getElementById('expenseDistributionChart').getContext('2d');
          this.expenseChart = new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
              labels: ['Operasional', 'Gaji', 'Pemasaran', 'Administrasi', 'Pemeliharaan'],
              datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                  '#3b82f6',
                  '#10b981',
                  '#f59e0b',
                  '#8b5cf6',
                  '#ef4444'
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
                  position: 'bottom'
                }
              }
            }
          });
        },

        updateCharts() {
          // Update charts based on selected period
          if (this.financialChart) {
            // Update chart data based on period
            this.financialChart.update();
          }
        },

      };
    }
  </script>
</x-layouts.admin>
