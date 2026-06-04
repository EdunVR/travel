
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Management Biaya']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Management Biaya')]); ?>
  <div x-data="expensesManagement()" x-init="init()" class="space-y-6">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Management Biaya</h1>
        <p class="text-slate-600 text-sm">Kelola dan pantau pengeluaran perusahaan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        
        <select x-model="selectedOutlet" @change="onOutletChange()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <template x-for="outlet in outlets" :key="outlet.id_outlet">
            <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
          </template>
        </select>

        
        <select x-model="selectedBook" @change="onBookChange()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <template x-for="book in books" :key="book.id">
            <option :value="book.id" x-text="book.name"></option>
          </template>
        </select>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.biaya.create')): ?>
        <button @click="openCreateExpense()" class="inline-flex items-center gap-2 rounded-xl bg-red-600 text-white px-4 h-10 hover:bg-red-700">
          <i class='bx bx-plus'></i> Tambah Biaya
        </button>
        <?php endif; ?>
        
        
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
      </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Ringkasan Biaya</h3>
          <div class="flex gap-2">
            
            <select x-model="chartRabFilter" @change="updateCharts()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
              <option value="all">Semua Anggaran</option>
              <option value="no_budget">Tanpa Anggaran</option>
              <template x-for="rab in availableRabs" :key="rab.id">
                <option :value="rab.id" x-text="rab.name"></option>
              </template>
            </select>
            <select x-model="overviewPeriod" @change="updateCharts()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
              <option value="monthly">Bulan Ini</option>
              <option value="quarterly">Kuartal Ini</option>
              <option value="yearly">Tahun Ini</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
          <div class="text-center p-4 rounded-lg bg-blue-50">
            <div class="text-2xl font-bold text-blue-600" x-text="formatCurrency(expenseStats.totalThisPeriod)"></div>
            <div class="text-sm text-blue-800">Total Biaya</div>
          </div>
          <div class="text-center p-4 rounded-lg bg-green-50">
            <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(expenseStats.budgetRemaining)"></div>
            <div class="text-sm text-green-800">Sisa Anggaran</div>
          </div>
        </div>
        <div class="h-48">
          <canvas id="expenseTrendChart"></canvas>
        </div>
      </div>

      
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Biaya per Kategori</h3>
          <a href="#expense-table" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Lihat Detail <i class='bx bx-chevron-right'></i>
          </a>
        </div>
        <div class="h-64">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="formatCurrency(expenseStats.totalMonthly)"></div>
            <div class="text-sm text-slate-600">Biaya Bulan Ini</div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs">
          <i class='bx bx-trending-up text-red-500'></i>
          <span class="text-red-600">+8.2%</span>
          <span class="text-slate-500">dari bulan lalu</span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-category text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="expenseStats.categoriesCount"></div>
            <div class="text-sm text-slate-600">Kategori Aktif</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          <span x-text="expenseStats.topCategory"></span> terbesar
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="expenseStats.approvedCount"></div>
            <div class="text-sm text-slate-600">Terealisasi</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          <span x-text="expenseStats.pendingCount"></span> menunggu approval
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-target-lock text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="expenseStats.utilization + '%'"></div>
            <div class="text-sm text-slate-600">Utilisasi Anggaran</div>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-1 text-xs" :class="expenseStats.utilization > 80 ? 'text-red-600' : 'text-green-600'">
          <i :class="expenseStats.utilization > 80 ? 'bx bx-error-circle' : 'bx bx-check-circle'"></i>
          <span x-text="expenseStats.utilization > 80 ? 'Melebihi batas' : 'Dalam batas'"></span>
        </div>
      </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Biaya</h2>
        <div class="flex flex-wrap gap-2">
          <select x-model="filters.category" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Kategori</option>
            <option value="operational">Operasional</option>
            <option value="administrative">Administratif</option>
            <option value="marketing">Pemasaran</option>
            <option value="maintenance">Pemeliharaan</option>
          </select>
          <select x-model="filters.status" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Status</option>
            <option value="approved">Disetujui</option>
            <option value="pending">Menunggu</option>
            <option value="rejected">Ditolak</option>
          </select>
          <input type="date" x-model="filters.date_from" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <input type="date" x-model="filters.date_to" @change="loadExpenses()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <input type="text" x-model="filters.search" @input.debounce.500ms="loadExpenses()" 
                 placeholder="Cari biaya..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
        </div>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left w-12">No</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Kategori</th>
            <th class="px-4 py-3 text-left">Deskripsi</th>
            <th class="px-4 py-3 text-left">Akun COA</th>
            <th class="px-4 py-3 text-right">Jumlah</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left w-24">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(expense, index) in expensesData" :key="expense.id">
            <tr class="border-t border-slate-100 hover:bg-slate-50">
              <td class="px-4 py-3" x-text="index + 1"></td>
              <td class="px-4 py-3" x-text="expense.date_formatted"></td>
              <td class="px-4 py-3">
                <div x-html="expense.category_badge"></div>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <div>
                    <div class="font-medium" x-text="expense.description"></div>
                    <div class="text-xs text-slate-500" x-text="expense.reference"></div>
                  </div>
                  <template x-if="expense.is_auto_generated">
                    <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700 whitespace-nowrap">
                      <i class='bx bx-bot'></i> Auto
                    </span>
                  </template>
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="text-sm" x-text="expense.account_name"></div>
                <div class="text-xs text-slate-500" x-text="expense.account_code"></div>
              </td>
              <td class="px-4 py-3 text-right">
                <div x-html="expense.amount_formatted" class="font-semibold"></div>
              </td>
              <td class="px-4 py-3">
                <div x-html="expense.status_badge"></div>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button @click="viewExpense(expense.id)" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                    <i class="bx bx-show"></i>
                  </button>
                  <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.biaya.edit')): ?>
                  <template x-if="expense.status === 'pending'">
                    <button @click="editExpense(expense.id)" class="text-green-600 hover:text-green-800" title="Edit">
                      <i class="bx bx-edit"></i>
                    </button>
                  </template>
                  <?php endif; ?>
                  <template x-if="expense.status === 'pending'">
                    <button @click="approveExpense(expense.id)" class="text-purple-600 hover:text-purple-800" title="Approve">
                      <i class="bx bx-check-circle"></i>
                    </button>
                  </template>
                  <template x-if="expense.status === 'pending'">
                    <button @click="rejectExpense(expense.id)" class="text-orange-600 hover:text-orange-800" title="Reject">
                      <i class="bx bx-x-circle"></i>
                    </button>
                  </template>
                  <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.biaya.delete')): ?>
                  <template x-if="expense.status === 'pending'">
                    <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-800" title="Hapus">
                      <i class="bx bx-trash"></i>
                    </button>
                  </template>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    
    <div x-show="showExpenseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50 overflow-y-auto" style="display: none;">
      <div class="bg-white rounded-2xl w-full max-w-5xl my-4">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800" x-text="editingExpense ? 'Edit Biaya' : 'Tambah Biaya Baru'"></h3>
        </div>
        
        <form @submit.prevent="saveExpense()">
          <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Outlet *</label>
                <select x-model="expenseForm.outlet_id" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                  <template x-for="outlet in outlets" :key="outlet.id_outlet">
                    <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                  </template>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Buku Akuntansi *</label>
                <select x-model="expenseForm.book_id" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                  <template x-for="book in books" :key="book.id">
                    <option :value="book.id" x-text="book.name"></option>
                  </template>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal *</label>
                <input type="date" x-model="expenseForm.expense_date" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori *</label>
                <select x-model="expenseForm.category" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                  <option value="">Pilih Kategori</option>
                  <option value="operational">Operasional</option>
                  <option value="administrative">Administratif</option>
                  <option value="marketing">Pemasaran</option>
                  <option value="maintenance">Pemeliharaan</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">RAB Template (Opsional)</label>
              <select x-model="expenseForm.rab_id" 
                      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">Tanpa Anggaran</option>
                <template x-for="rab in availableRabs" :key="rab.id">
                  <option :value="rab.id" x-text="rab.name + ' (Budget: Rp ' + rab.budget_total.toLocaleString('id-ID') + ')'"></option>
                </template>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Akun Biaya (Debit) *</label>
              <select x-model="expenseForm.account_id" 
                      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <option value="">Pilih Akun Biaya</option>
                <template x-for="account in expenseAccounts" :key="account.id">
                  <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                </template>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Akun Kas/Bank (Credit) *</label>
              <select x-model="expenseForm.cash_account_id" 
                      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                <option value="">Pilih Akun Kas/Bank</option>
                <template x-for="account in cashAccounts" :key="account.id">
                  <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                </template>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi *</label>
              <textarea x-model="expenseForm.description" rows="3" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        placeholder="Deskripsi biaya..." required></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah *</label>
              <input type="number" x-model="expenseForm.amount" 
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                     placeholder="0" step="1" min="0" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
              <textarea x-model="expenseForm.notes" rows="2" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button type="button" @click="showExpenseModal = false" 
                    class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" 
                    :disabled="saving"
                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
              <i x-show="saving" class='bx bx-loader-alt animate-spin'></i>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <script>
    function expensesManagement() {
      return {
        currentLanguage: 'id',
        overviewPeriod: 'monthly',
        selectedOutlet: 1,
        selectedBook: 1,
        selectedRab: 'all',
        chartRabFilter: 'all',
        outlets: [],
        books: [],
        availableRabs: [],
        loading: false,
        saving: false,
        isExporting: false,
        showExpenseModal: false,
        editingExpense: null,
        expenseAccounts: [],
        cashAccounts: [],
        expenseForm: {
          expense_date: '',
          category: '',
          rab_id: '',
          account_id: '',
          cash_account_id: '',
          description: '',
          amount: 0,
          notes: ''
        },
        filters: {
          category: 'all',
          status: 'all',
          date_from: '',
          date_to: '',
          search: ''
        },
        expenseStats: {
          totalThisPeriod: 0,
          budgetRemaining: 0,
          totalMonthly: 0,
          categoriesCount: 0,
          topCategory: 'Operasional',
          approvedCount: 0,
          pendingCount: 0,
          utilization: 0
        },
        expensesData: [],

        // Routes
        routes: {
          outletsData: '<?php echo e(route("admin.finance.outlets.data")); ?>',
          expensesData: '<?php echo e(route("admin.finance.expenses.data")); ?>',
          expensesStats: '<?php echo e(route("admin.finance.expenses.stats")); ?>',
          chartData: '<?php echo e(route("admin.finance.expenses.chart-data")); ?>',
          storeExpense: '<?php echo e(route("admin.finance.expenses.store")); ?>',
          updateExpense: '<?php echo e(route("admin.finance.expenses.update", ["id" => ":id"])); ?>',
          deleteExpense: '<?php echo e(route("admin.finance.expenses.delete", ["id" => ":id"])); ?>',
          approveExpense: '<?php echo e(route("admin.finance.expenses.approve", ["id" => ":id"])); ?>',
          rejectExpense: '<?php echo e(route("admin.finance.expenses.reject", ["id" => ":id"])); ?>',
          exportXLSX: '<?php echo e(route("admin.finance.expenses.export.xlsx")); ?>',
          exportPDF: '<?php echo e(route("admin.finance.expenses.export.pdf")); ?>'
        },

        async init() {
          console.log('🚀 Initializing expenses management...');
          
          await this.loadOutlets();
          console.log('📍 Outlets loaded:', this.outlets);
          
          // Set outlet from user or default to first available outlet
          const userOutletId = <?php echo e(auth()->user()->outlet_id ?? 'null'); ?>;
          console.log('👤 User outlet ID:', userOutletId);
          
          if (userOutletId && this.outlets.some(o => o.id_outlet == userOutletId)) {
            this.selectedOutlet = userOutletId;
            console.log('✅ Using user outlet:', userOutletId);
          } else if (this.outlets.length > 0) {
            this.selectedOutlet = this.outlets[0].id_outlet;
            console.log('✅ Using first outlet:', this.outlets[0].id_outlet);
          } else {
            this.selectedOutlet = window.OutletHelper ? window.OutletHelper.getFirstOutletId(this.outlets) : 2;
            console.log('⚠️ Using default outlet: 1');
          }
          
          await this.loadBooks();
          console.log('📚 Books loaded:', this.books);
          console.log('🎯 Selected book:', this.selectedBook);
          
          await this.loadAvailableRabs();
          await this.loadExpenseAccounts();
          await this.loadCashAccounts();
          
          console.log('💰 About to load expenses...');
          await this.loadExpenses();
          
          await this.loadStats();
          await this.loadChartData();
          
          console.log('✅ Initialization complete!');
        },

        async loadExpenseAccounts() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              type: 'expense',
              status: 'active'
            });

            const response = await fetch(`<?php echo e(route("finance.chart-of-accounts.data")); ?>?${params}`);
            const result = await response.json();

            if (result.success) {
              this.expenseAccounts = result.data;
            }
          } catch (error) {
            console.error('Error loading expense accounts:', error);
          }
        },

        async loadCashAccounts() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              type: 'asset',
              status: 'active'
            });

            const response = await fetch(`<?php echo e(route("finance.chart-of-accounts.data")); ?>?${params}`);
            const result = await response.json();

            if (result.success) {
              // Filter only cash/bank accounts (code starts with 1000)
              this.cashAccounts = result.data.filter(acc => acc.code.startsWith('1000'));
            }
          } catch (error) {
            console.error('Error loading cash accounts:', error);
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const result = await response.json();
            if (result.success) {
              this.outlets = result.data;
              // Set default outlet (from user or first outlet)
              if (this.outlets.length > 0) {
                this.selectedOutlet = this.outlets[0].id_outlet;
              }
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
          }
        },

        async loadBooks() {
          try {
            const response = await fetch(`<?php echo e(route("finance.accounting-books.data")); ?>?outlet_id=${this.selectedOutlet}`);
            const result = await response.json();
            if (result.success) {
              this.books = result.data;
              if (this.books.length > 0) {
                this.selectedBook = this.books[0].id;
              }
            }
          } catch (error) {
            console.error('Error loading books:', error);
          }
        },

        async onOutletChange() {
          console.log('Outlet changed to:', this.selectedOutlet);
          this.selectedRab = 'all';
          await this.loadBooks();
          await this.loadAvailableRabs();
          await this.loadExpenseAccounts();
          await this.loadCashAccounts();
          await this.loadExpenses();
          await this.loadStats();
          await this.loadChartData();
        },

        async onBookChange() {
          console.log('Book changed to:', this.selectedBook);
          this.chartRabFilter = 'all';
          await this.loadAvailableRabs();
          await this.loadExpenses();
          await this.loadStats();
          await this.loadChartData();
        },

        async onRabChange() {
          console.log('RAB changed to:', this.selectedRab);
          await this.loadExpenses();
        },

        async loadAvailableRabs() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              book_id: this.selectedBook
            });

            const response = await fetch(`<?php echo e(route("admin.finance.rab.data")); ?>?${params}`);
            const result = await response.json();

            if (result.success) {
              this.availableRabs = result.data;
            }
          } catch (error) {
            console.error('Error loading RABs:', error);
          }
        },

        async loadExpenses() {
          this.loading = true;
          console.log('🔍 Loading expenses...');
          console.log('selectedOutlet:', this.selectedOutlet);
          console.log('selectedBook:', this.selectedBook);
          
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              book_id: this.selectedBook,
              rab_id: this.chartRabFilter || 'all',
              category: this.filters.category || 'all',
              status: this.filters.status || 'all',
              date_from: this.filters.date_from || '',
              date_to: this.filters.date_to || '',
              search: this.filters.search || ''
            });

            const url = `${this.routes.expensesData}?${params}`;
            console.log('🌐 API URL:', url);
            console.log('📋 Params:', Object.fromEntries(params));

            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
              }
            });
            
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
            
            if (!response.ok) {
              const errorText = await response.text();
              console.error('❌ Response error text:', errorText);
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('📊 API Result:', result);

            if (result.success) {
              this.expensesData = result.data;
              console.log('✅ Expenses data loaded:', this.expensesData.length, 'items');
              
              if (this.expensesData.length === 0) {
                console.warn('⚠️ API berhasil tapi tidak ada data yang dikembalikan');
                console.log('🔍 Kemungkinan penyebab: filter terlalu ketat atau data tidak sesuai kriteria');
              }
            } else {
              console.error('❌ API returned error:', result.message);
              this.showNotification(result.message || 'Gagal memuat data', 'error');
            }
          } catch (error) {
            console.error('💥 Error loading expenses:', error);
            this.showNotification('Gagal memuat data biaya: ' + error.message, 'error');
          } finally {
            this.loading = false;
          }
        },

        async loadStats() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              rab_id: this.chartRabFilter
            });

            const response = await fetch(`${this.routes.expensesStats}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.expenseStats = result.data;
            }
          } catch (error) {
            console.error('Error loading stats:', error);
          }
        },

        async loadChartData() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              rab_id: this.chartRabFilter,
              period: this.overviewPeriod
            });

            const response = await fetch(`${this.routes.chartData}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.initCharts(result.data);
            }
          } catch (error) {
            console.error('Error loading chart data:', error);
            // Fallback to empty charts
            this.initCharts({
              trend: { labels: [], data: [] },
              category: { labels: [], data: [], colors: [] }
            });
          }
        },

        initCharts(chartData) {
          // Expense Trend Chart
          const trendCtx = document.getElementById('expenseTrendChart').getContext('2d');
          
          // Destroy existing chart if exists
          if (this.trendChart) {
            this.trendChart.destroy();
          }
          
          this.trendChart = new Chart(trendCtx, {
            type: 'bar',
            data: {
              labels: chartData.trend.labels,
              datasets: [{
                label: 'Total Biaya',
                data: chartData.trend.data,
                backgroundColor: '#3b82f6',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: false
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                    }
                  }
                }
              }
            }
          });

          // Category Chart
          const categoryCtx = document.getElementById('categoryChart').getContext('2d');
          
          // Destroy existing chart if exists
          if (this.categoryChart) {
            this.categoryChart.destroy();
          }
          
          this.categoryChart = new Chart(categoryCtx, {
            type: 'pie',
            data: {
              labels: chartData.category.labels,
              datasets: [{
                data: chartData.category.data,
                backgroundColor: chartData.category.colors.length > 0 ? chartData.category.colors : [
                  '#ef4444',
                  '#8b5cf6',
                  '#3b82f6',
                  '#f59e0b',
                  '#10b981',
                  '#6b7280'
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
                  position: 'right'
                }
              }
            }
          });
        },

        async updateCharts() {
          await this.loadStats();
          await this.loadChartData();
          await this.loadExpenses();
        },

        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(amount);
        },

        openCreateExpense() {
          this.editingExpense = null;
          this.expenseForm = {
            outlet_id: this.selectedOutlet,
            book_id: this.selectedBook,
            expense_date: new Date().toISOString().split('T')[0],
            category: '',
            rab_id: '',
            account_id: '',
            cash_account_id: '',
            description: '',
            amount: 0,
            notes: ''
          };
          this.showExpenseModal = true;
        },

        async saveExpense() {
          this.saving = true;

          try {
            const url = this.editingExpense 
              ? this.routes.updateExpense.replace(':id', this.editingExpense)
              : this.routes.storeExpense;

            const method = this.editingExpense ? 'PUT' : 'POST';

            const payload = {
              ...this.expenseForm
            };

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
              this.showExpenseModal = false;
              await this.loadExpenses();
              await this.loadStats();
              await this.loadChartData();
              this.showNotification(result.message || 'Biaya berhasil disimpan', 'success');
            } else {
              this.showNotification(result.message || 'Gagal menyimpan biaya', 'error');
            }
          } catch (error) {
            console.error('Error saving expense:', error);
            this.showNotification('Gagal menyimpan biaya', 'error');
          } finally {
            this.saving = false;
          }
        },

        viewExpense(id) {
          const expense = this.expensesData.find(e => e.id === id);
          if (expense) {
            alert(`Detail Biaya:\n\nReferensi: ${expense.reference}\nTanggal: ${expense.date_formatted}\nKategori: ${expense.category}\nDeskripsi: ${expense.description}\nJumlah: ${expense.amount_formatted}\nStatus: ${expense.status}`);
          }
        },

        async editExpense(id) {
          const expense = this.expensesData.find(e => e.id === id);
          if (expense && expense.status === 'pending') {
            this.editingExpense = id;
            this.expenseForm = {
              outlet_id: expense.outlet_id || this.selectedOutlet,
              book_id: expense.book_id || this.selectedBook,
              expense_date: expense.expense_date,
              category: expense.category,
              rab_id: expense.rab_id || '',
              account_id: expense.account_id,
              cash_account_id: expense.cash_account_id || '',
              description: expense.description,
              amount: expense.amount,
              notes: expense.notes || ''
            };
            this.showExpenseModal = true;
          } else {
            this.showNotification('Hanya biaya dengan status pending yang dapat diedit', 'error');
          }
        },

        async approveExpense(id) {
          if (!confirm('Apakah Anda yakin ingin menyetujui biaya ini? Jurnal entry akan dibuat otomatis.')) {
            return;
          }

          try {
            const response = await fetch(this.routes.approveExpense.replace(':id', id), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result.success) {
              this.showNotification(result.message || 'Biaya berhasil disetujui dan jurnal telah dibuat', 'success');
              await this.loadExpenses();
              await this.loadStats();
              await this.loadChartData();
            } else {
              this.showNotification(result.message || 'Gagal menyetujui biaya', 'error');
            }
          } catch (error) {
            console.error('Error approving expense:', error);
            this.showNotification('Gagal menyetujui biaya', 'error');
          }
        },

        async rejectExpense(id) {
          if (!confirm('Apakah Anda yakin ingin menolak biaya ini?')) {
            return;
          }

          try {
            const response = await fetch(this.routes.rejectExpense.replace(':id', id), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result.success) {
              this.showNotification(result.message || 'Biaya berhasil ditolak', 'success');
              await this.loadExpenses();
              await this.loadStats();
            } else {
              this.showNotification(result.message || 'Gagal menolak biaya', 'error');
            }
          } catch (error) {
            console.error('Error rejecting expense:', error);
            this.showNotification('Gagal menolak biaya', 'error');
          }
        },

        async deleteExpense(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus biaya ini?')) {
            return;
          }

          try {
            const response = await fetch(this.routes.deleteExpense.replace(':id', id), {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result.success) {
              this.showNotification(result.message || 'Biaya berhasil dihapus', 'success');
              await this.loadExpenses();
              await this.loadStats();
            } else {
              this.showNotification(result.message || 'Gagal menghapus biaya', 'error');
            }
          } catch (error) {
            console.error('Error deleting expense:', error);
            this.showNotification('Gagal menghapus biaya', 'error');
          }
        },

        async exportToXLSX() {
          if (this.isExporting) return;
          
          this.isExporting = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              book_id: this.selectedBook,
              category: this.filters.category !== 'all' ? this.filters.category : '',
              status: this.filters.status !== 'all' ? this.filters.status : '',
              date_from: this.filters.date_from,
              date_to: this.filters.date_to,
              search: this.filters.search
            });

            const response = await fetch(`${this.routes.exportXLSX}?${params}`);
            
            if (!response.ok) {
              throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `biaya_${new Date().toISOString().slice(0, 10)}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            this.showNotification('Data berhasil diekspor ke Excel', 'success');
          } catch (error) {
            console.error('Error exporting to XLSX:', error);
            this.showNotification('Gagal mengekspor data', 'error');
          } finally {
            this.isExporting = false;
          }
        },

        async exportToPDF() {
          if (this.isExporting) return;
          
          this.isExporting = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              book_id: this.selectedBook,
              category: this.filters.category !== 'all' ? this.filters.category : '',
              status: this.filters.status !== 'all' ? this.filters.status : '',
              date_from: this.filters.date_from,
              date_to: this.filters.date_to,
              search: this.filters.search
            });

            const response = await fetch(`${this.routes.exportPDF}?${params}`);
            
            if (!response.ok) {
              throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `biaya_${new Date().toISOString().slice(0, 10)}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            this.showNotification('Data berhasil diekspor ke PDF', 'success');
          } catch (error) {
            console.error('Error exporting to PDF:', error);
            this.showNotification('Gagal mengekspor data', 'error');
          } finally {
            this.isExporting = false;
          }
        },

        exportExpenses() {
          // Show export options
          if (confirm('Export ke Excel?')) {
            this.exportToXLSX();
          } else {
            this.exportToPDF();
          }
        },

        showNotification(message, type = 'info') {
          // Simple notification using alert for now
          // You can replace this with a better notification system
          if (type === 'success') {
            alert('✓ ' + message);
          } else if (type === 'error') {
            alert('✗ ' + message);
          } else {
            alert(message);
          }
        },

        changeLanguage() {
          // Language change logic
        }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\biaya\index.blade.php ENDPATH**/ ?>