<x-layouts.admin :title="'Accounting Books'">
  <div x-data="booksManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Accounting Books</h1>
        <p class="text-slate-600 text-sm">Kelola buku akuntansi dan pembukuan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Pilih Outlet --}}
        <select x-model="selectedOutlet" @change="loadBooks()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <template x-for="outlet in outlets" :key="outlet.id_outlet">
            <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
          </template>
        </select>

        @hasPermission('finance.buku.create')
        <button @click="openCreateBook()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700">
          <i class='bx bx-plus'></i> Buat Buku Baru
        </button>
        @endhasPermission
        
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" 
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
            <i class='bx bx-chevron-down text-sm'></i>
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
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl">
              <i class='bx bxs-file-pdf text-red-600'></i> 
              <span>Export ke PDF</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    {{-- Error Message --}}
    <div x-show="error" class="rounded-xl bg-red-50 border border-red-200 p-4">
      <div class="flex items-center gap-2 text-red-800">
        <i class='bx bx-error-circle text-lg'></i>
        <span x-text="error"></span>
      </div>
    </div>

    {{-- Main Content --}}
    <div x-show="!loading && !error" class="space-y-6">

      {{-- Infografis Buku --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Book Activity --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Aktivitas Buku</h3>
                <div class="flex items-center gap-2">
                    <span x-show="loading" class="text-sm text-slate-500">
                        <i class='bx bx-loader-alt animate-spin'></i> Loading...
                    </span>
                    <select x-model="chartPeriod" @change="handleChartPeriodChange()" 
                            :disabled="loading"
                            class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
                        <option value="monthly">Bulanan</option>
                        <option value="quarterly">Triwulan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
            </div>
            <div class="h-64 chart-container">
                <canvas id="bookActivityChart" x-ref="bookActivityChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Book Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-800">Status Buku</h3>
            <span class="text-sm text-slate-500">Total: <span x-text="bookStats.totalBooks"></span> buku</span>
          </div>
          <div class="h-64">
            <canvas id="bookStatusChart" x-ref="bookStatusChart"></canvas>
          </div>
        </div>
      </div>

      {{-- Quick Stats --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <i class='bx bx-book text-2xl text-blue-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="bookStats.totalBooks"></div>
              <div class="text-sm text-slate-600">Total Buku</div>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx bx-check-circle text-green-500'></i>
            <span class="text-green-600" x-text="bookStats.activeBooks"></span>
            <span class="text-slate-500">aktif</span>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
              <i class='bx bx-edit text-2xl text-green-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="bookStats.totalEntries"></div>
              <div class="text-sm text-slate-600">Total Entri</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="bookStats.entriesThisMonth"></span> bulan ini
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
              <i class='bx bx-wallet text-2xl text-purple-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(bookStats.totalBalance)"></div>
              <div class="text-sm text-slate-600">Total Saldo</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            Seluruh buku
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
              <i class='bx bx-line-chart text-2xl text-orange-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="bookStats.avgEntries"></div>
              <div class="text-sm text-slate-600">Rata-rata Entri</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            Per buku per bulan
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-lg font-semibold text-slate-800">Daftar Buku Akuntansi</h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filters.type" @change="loadBooks()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all">Semua Tipe</option>
              <option value="general">Umum</option>
              <option value="cash">Kas</option>
              <option value="bank">Bank</option>
              <option value="sales">Penjualan</option>
              <option value="purchase">Pembelian</option>
              <option value="inventory">Persediaan</option>
              <option value="payroll">Penggajian</option>
            </select>
            <select x-model="filters.status" @change="loadBooks()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
              <option value="draft">Draft</option>
              <option value="closed">Ditutup</option>
            </select>
            <input type="text" x-model="filters.search" @input.debounce.500ms="loadBooks()" 
                   placeholder="Cari kode atau nama buku..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
          </div>
        </div>
      </div>

      {{-- Books Grid --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-if="booksData.length === 0">
          <div class="col-span-3 rounded-2xl border border-slate-200 bg-white p-12 text-center">
            <div class="flex flex-col items-center gap-3">
              <i class='bx bx-book-open text-4xl text-slate-300'></i>
              <div class="text-slate-500">Tidak ada data buku akuntansi</div>
              <button @click="openCreateBook()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700 mt-2">
                <i class='bx bx-plus'></i> Buat Buku Pertama
              </button>
            </div>
          </div>
        </template>

        <template x-for="book in booksData" :key="book.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card hover:shadow-lg transition-shadow" 
               :class="book.is_locked ? 'border-orange-300 bg-orange-25' : ''">
            <div class="flex items-start justify-between mb-4">
              <div>
                <div class="font-mono text-sm text-slate-600" x-text="book.code"></div>
                <div class="font-semibold text-slate-800 text-lg" x-text="book.name"></div>
              </div>
              <div class="flex flex-col items-end gap-1">
                <span :class="getStatusBadgeClass(book.status)" 
                      class="px-2 py-1 rounded-full text-xs" x-text="getStatusName(book.status)"></span>
                <span x-show="book.is_locked" class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                  <i class='bx bx-lock-alt'></i> Terkunci
                </span>
              </div>
            </div>
            
            <p class="text-sm text-slate-600 mb-4" x-text="book.description || 'Tidak ada deskripsi'"></p>
            
            <div class="space-y-3 mb-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Tipe</span>
                <span class="text-sm font-medium" x-text="getTypeName(book.type)"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Periode</span>
                <span class="text-sm font-medium" x-text="book.period"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Mata Uang</span>
                <span class="text-sm font-medium" x-text="book.currency"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-slate-500">Total Entri</span>
                <span class="text-sm font-medium" x-text="book.total_entries"></span>
              </div>
            </div>

            <div class="border-t border-slate-200 pt-4">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-slate-500">Saldo</span>
                <span class="text-lg font-bold" :class="book.closing_balance >= 0 ? 'text-green-600' : 'text-red-600'" 
                       x-text="formatCurrency(book.closing_balance)"></span>
              </div>
            </div>

            <div class="flex justify-between items-center mt-4">
              <div class="text-xs text-slate-500">
                Dibuat: <span x-text="formatDate(book.created_at)"></span>
              </div>
              <div class="flex items-center gap-2">
                <button @click="viewBook(book.id)" class="text-blue-600 hover:text-blue-800 p-1 rounded" title="Lihat">
                  <i class="bx bx-show text-lg"></i>
                </button>
                <button @click="editBook(book)" 
                        :disabled="book.is_locked || !book.can_edit"
                        :class="book.is_locked || !book.can_edit ? 'text-slate-400 cursor-not-allowed' : 'text-green-600 hover:text-green-800'"
                        class="p-1 rounded"
                        :title="book.is_locked ? 'Buku terkunci' : !book.can_edit ? 'Tidak dapat diedit' : 'Edit'">
                  <i class="bx bx-edit text-lg"></i>
                </button>
                <button @click="toggleBook(book.id, book.status)" 
                        :disabled="book.is_locked"
                        :class="book.is_locked ? 'text-slate-400 cursor-not-allowed' : book.status === 'active' ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'"
                        class="p-1 rounded"
                        :title="book.is_locked ? 'Buku terkunci' : book.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'">
                  <i :class="book.status === 'active' ? 'bx bx-power-off text-lg' : 'bx bx-check-circle text-lg'"></i>
                </button>
                <button @click="closeBook(book)" 
                        :disabled="book.status !== 'active' || book.is_locked"
                        :class="book.status !== 'active' || book.is_locked ? 'text-slate-400 cursor-not-allowed' : 'text-purple-600 hover:text-purple-800'"
                        class="p-1 rounded"
                        :title="book.status !== 'active' ? 'Buku harus aktif untuk ditutup' : book.is_locked ? 'Buku sudah terkunci' : 'Tutup Buku'">
                  <i class="bx bx-lock-alt text-lg"></i>
                </button>
                <button @click="deleteBook(book)" 
                        :disabled="!book.can_delete"
                        :class="!book.can_delete ? 'text-slate-400 cursor-not-allowed' : 'text-red-600 hover:text-red-800'"
                        class="p-1 rounded"
                        :title="!book.can_delete ? 'Tidak dapat dihapus' : 'Hapus'">
                  <i class="bx bx-trash text-lg"></i>
                </button>
              </div>
            </div>
          </div>
        </template>
      </div>

    </div>

    {{-- Modal Create/Edit Book --}}
    <div x-show="showBookModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50 overflow-y-auto">
      <div class="bg-white rounded-2xl w-full max-w-4xl my-4">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800" x-text="editingBook ? 'Edit Buku' : 'Buat Buku Baru'"></h3>
        </div>
        <form @submit.prevent="saveBook()">
          <div class="p-6 space-y-4">
            {{-- Error Validation --}}
            <div x-show="formErrors.length > 0" class="rounded-xl bg-red-50 border border-red-200 p-4">
              <ul class="list-disc list-inside text-red-800 text-sm">
                <template x-for="error in formErrors" :key="error">
                  <li x-text="error"></li>
                </template>
              </ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Buku *</label>
                <div class="flex gap-2">
                  <input type="text" x-model="bookForm.code" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                         :class="formErrors.code ? 'border-red-300' : ''"
                         required>
                  <button type="button" @click="generateBookCode()" 
                          class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50 whitespace-nowrap">
                    Generate
                  </button>
                </div>
                <p x-show="formErrors.code" class="mt-1 text-red-600 text-xs" x-text="formErrors.code"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Buku *</label>
                <input type="text" x-model="bookForm.name" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       :class="formErrors.name ? 'border-red-300' : ''"
                       required>
                <p x-show="formErrors.name" class="mt-1 text-red-600 text-xs" x-text="formErrors.name"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Buku *</label>
                <select x-model="bookForm.type" @change="onTypeChange()"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        :class="formErrors.type ? 'border-red-300' : ''"
                        required>
                  <option value="general">Umum</option>
                  <option value="cash">Kas</option>
                  <option value="bank">Bank</option>
                  <option value="sales">Penjualan</option>
                  <option value="purchase">Pembelian</option>
                  <option value="inventory">Persediaan</option>
                  <option value="payroll">Penggajian</option>
                </select>
                <p x-show="formErrors.type" class="mt-1 text-red-600 text-xs" x-text="formErrors.type"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mata Uang *</label>
                <select x-model="bookForm.currency" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        :class="formErrors.currency ? 'border-red-300' : ''"
                        required>
                  <option value="IDR">IDR - Rupiah</option>
                  <option value="USD">USD - Dolar AS</option>
                  <option value="EUR">EUR - Euro</option>
                  <option value="SGD">SGD - Dolar Singapura</option>
                </select>
                <p x-show="formErrors.currency" class="mt-1 text-red-600 text-xs" x-text="formErrors.currency"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode Mulai *</label>
                <input type="date" x-model="bookForm.start_date" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       :class="formErrors.start_date ? 'border-red-300' : ''"
                       required>
                <p x-show="formErrors.start_date" class="mt-1 text-red-600 text-xs" x-text="formErrors.start_date"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode Berakhir *</label>
                <input type="date" x-model="bookForm.end_date" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       :class="formErrors.end_date ? 'border-red-300' : ''"
                       required>
                <p x-show="formErrors.end_date" class="mt-1 text-red-600 text-xs" x-text="formErrors.end_date"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Awal</label>
                <input type="number" x-model="bookForm.opening_balance" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       step="0.01"
                       placeholder="0">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status *</label>
                <select x-model="bookForm.status" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        :class="formErrors.status ? 'border-red-300' : ''"
                        required>
                  <option value="draft">Draft</option>
                  <option value="active">Aktif</option>
                  <option value="inactive">Nonaktif</option>
                  <option value="closed">Ditutup</option>
                </select>
                <p x-show="formErrors.status" class="mt-1 text-red-600 text-xs" x-text="formErrors.status"></p>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
              <textarea x-model="bookForm.description" rows="3" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        placeholder="Deskripsi buku akuntansi..."></textarea>
            </div>
          </div>
          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button type="button" @click="showBookModal = false" 
                    class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" 
                    :disabled="saving"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
              <i x-show="saving" class='bx bx-loader-alt animate-spin'></i>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <script>
    function booksManagement() {
      return {
        // State
        loading: false,
        saving: false,
        error: null,
        chartPeriod: 'monthly',
        showBookModal: false,
        editingBook: null,
        selectedOutlet: 1,
        outlets: [],
        
        // Data
        bookForm: {
          outlet_id: '',
          code: '',
          name: '',
          type: 'general',
          description: '',
          currency: 'IDR',
          start_date: '',
          end_date: '',
          opening_balance: 0,
          status: 'draft'
        },
        filters: {
          type: 'all',
          status: 'all',
          search: ''
        },
        bookStats: {
          totalBooks: 0,
          activeBooks: 0,
          draftBooks: 0,
          closedBooks: 0,
          totalEntries: 0,
          entriesThisMonth: 0,
          totalBalance: 0,
          avgEntries: 0
        },
        booksData: [],
        formErrors: [],

        // Routes
        routes: {
          accountingBooksData: '{{ route("finance.accounting-books.data") }}',
          generateCode: '{{ route("finance.accounting-books.generate-code") }}',
          storeBook: '{{ route("finance.accounting-books.store") }}',
          updateBook: '{{ route("finance.accounting-books.update", ["id" => ":id"]) }}',
          toggleBook: '{{ route("finance.accounting-books.toggle", ["id" => ":id"]) }}',
          deleteBook: '{{ route("finance.accounting-books.delete", ["id" => ":id"]) }}',
          closeBook: '{{ route("finance.accounting-books.close", ["id" => ":id"]) }}',
          showBook: '{{ route("finance.accounting-books.show", ["id" => ":id"]) }}',
          outletsData: '{{ route("finance.outlets.data") }}'
        },

        async init() {
            try {
                await this.loadOutlets();
                if (this.outlets.length > 0) {
                    this.selectedOutlet = this.outlets[0].id_outlet;
                    this.bookForm.outlet_id = this.outlets[0].id_outlet;
                }
                await this.loadBooks();
                
                // Initialize charts setelah data terload
                this.$nextTick(() => {
                    this.initCharts();
                });
            } catch (error) {
                console.error('Initialization error:', error);
                this.error = 'Gagal memuat aplikasi';
            }
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
            } else {
              console.error('Error loading outlets:', result.message);
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
            this.outlets = [
              { id_outlet: 1, nama_outlet: 'Outlet Pusat' },
              { id_outlet: 2, nama_outlet: 'Outlet Cabang 1' },
              { id_outlet: 3, nama_outlet: 'Outlet Cabang 2' }
            ];
          }
        },

        async loadBooks() {
            this.loading = true;
            this.error = null;

            try {
                this.bookForm.outlet_id = this.selectedOutlet;

                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet,
                    type: this.filters.type,
                    status: this.filters.status,
                    search: this.filters.search
                });

                const url = `${this.routes.accountingBooksData}?${params}`;
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    this.booksData = result.data;
                    this.bookStats = result.stats;
                    
                    // Update status chart saja
                    this.updateStatusChart();
                } else {
                    this.error = result.message;
                }
            } catch (error) {
                this.error = 'Gagal memuat data buku: ' + error.message;
            } finally {
                this.loading = false;
            }
        },

        async generateBookCode() {
            try {
                console.log('Generating book code...');
                console.log('Outlet ID:', this.bookForm.outlet_id);
                console.log('Type:', this.bookForm.type);

                if (!this.bookForm.outlet_id) {
                    this.showNotification('Pilih outlet terlebih dahulu', 'error');
                    return;
                }

                const params = new URLSearchParams({
                    outlet_id: this.bookForm.outlet_id.toString(),
                    type: this.bookForm.type
                });

                const url = `{{ route('finance.accounting-books.generate-code') }}?${params}`;
                console.log('Request URL:', url);

                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    this.bookForm.code = result.data.code;
                    this.showNotification('Kode buku berhasil digenerate', 'success');
                } else {
                    this.showNotification('Gagal generate kode: ' + result.message, 'error');
                    this.generateFallbackCode();
                }
            } catch (error) {
                console.error('Generate code error:', error);
                this.showNotification('Error generating code: ' + error.message, 'error');
                this.generateFallbackCode();
            }
        },

        // Fallback method jika API error
        generateFallbackCode() {
            const typePrefix = this.getTypePrefix(this.bookForm.type);
            const outletPrefix = '001'; // Default fallback
            const randomNum = Math.floor(Math.random() * 900) + 100; // Random 100-999
            
            this.bookForm.code = `${typePrefix}-${outletPrefix}-${randomNum}`;
            this.showNotification('Kode digenerate secara manual', 'info');
        },

        getTypePrefix(type) {
            const prefixes = {
                'general': 'BB',
                'cash': 'BK',
                'bank': 'BBK',
                'sales': 'BP',
                'purchase': 'BPM',
                'inventory': 'BPS',
                'payroll': 'BGA'
            };
            return prefixes[type] || 'BUK';
        },

        onTypeChange() {
            if (!this.editingBook) {
                // Delay sedikit untuk memastikan form sudah update
                setTimeout(() => {
                    this.generateBookCode();
                }, 100);
            }
        },

        openCreateBook() {
            this.editingBook = null;
            const today = new Date().toISOString().split('T')[0];
            const nextYear = new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0];
            
            this.bookForm = {
                outlet_id: this.selectedOutlet,
                code: '',
                name: '',
                type: 'general',
                description: '',
                currency: 'IDR',
                start_date: today,
                end_date: nextYear,
                opening_balance: 0,
                status: 'draft'
            };
            this.formErrors = [];
            this.showBookModal = true;
            
            // Auto-generate code setelah modal terbuka
            this.$nextTick(() => {
                this.generateBookCode();
            });
        },

        editBook(book) {
            this.editingBook = book.id;
            
            // Format dates untuk input type="date" (YYYY-MM-DD)
            const startDate = book.start_date ? new Date(book.start_date).toISOString().split('T')[0] : '';
            const endDate = book.end_date ? new Date(book.end_date).toISOString().split('T')[0] : '';
            
            this.bookForm = {
                outlet_id: book.outlet_id,
                code: book.code,
                name: book.name,
                type: book.type,
                description: book.description || '',
                currency: book.currency,
                start_date: startDate,
                end_date: endDate,
                opening_balance: parseFloat(book.opening_balance),
                status: book.status
            };
            this.formErrors = [];
            this.showBookModal = true;
        },

        async saveBook() {
            this.saving = true;
            this.formErrors = [];

            try {
                const url = this.editingBook 
                    ? this.routes.updateBook.replace(':id', this.editingBook)
                    : this.routes.storeBook;

                const method = this.editingBook ? 'POST' : 'POST';
                
                const formData = new FormData();
                
                if (this.editingBook) {
                    formData.append('_method', 'PUT');
                }
                
                // Tambahkan CSRF token secara manual
                const csrfToken = this.getCsrfToken();
                formData.append('_token', csrfToken);
                
                formData.append('outlet_id', this.bookForm.outlet_id.toString());
                formData.append('code', this.bookForm.code);
                formData.append('name', this.bookForm.name);
                formData.append('type', this.bookForm.type);
                formData.append('description', this.bookForm.description || '');
                formData.append('currency', this.bookForm.currency);
                formData.append('start_date', this.bookForm.start_date);
                formData.append('end_date', this.bookForm.end_date);
                formData.append('opening_balance', this.bookForm.opening_balance.toString());
                formData.append('status', this.bookForm.status);

                console.log('Sending form data:', Object.fromEntries(formData));

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.showBookModal = false;
                    await this.loadBooks();
                    this.showNotification(result.message, 'success');
                } else {
                    if (result.errors) {
                        this.formErrors = Object.values(result.errors).flat();
                    } else {
                        this.formErrors = [result.message];
                    }
                }
            } catch (error) {
                console.error('Save error:', error);
                this.formErrors = ['Terjadi kesalahan saat menyimpan data'];
            } finally {
                this.saving = false;
            }
        },

        // Tambahkan method getCsrfToken
        getCsrfToken() {
            // Coba dari meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.getAttribute('content');
            }
            
            // Fallback: coba dari cookie
            const cookieValue = document.cookie
                .split('; ')
                .find(row => row.startsWith('XSRF-TOKEN='))
                ?.split('=')[1];
            
            if (cookieValue) {
                return decodeURIComponent(cookieValue);
            }
            
            console.error('CSRF token not found');
            return '';
        },

        async toggleBook(id, currentStatus) {
          const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
          const action = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';

          if (!confirm(`Apakah Anda yakin ingin ${action} buku ini?`)) {
            return;
          }

          try {
            const url = this.routes.toggleBook.replace(':id', id);
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result.success) {
              await this.loadBooks();
              this.showNotification(result.message, 'success');
            } else {
              this.showNotification(result.message, 'error');
            }
          } catch (error) {
            this.showNotification('Gagal mengubah status buku', 'error');
          }
        },

        async deleteBook(book) {
          if (!book.can_delete) {
            this.showNotification('Buku tidak dapat dihapus', 'error');
            return;
          }

          const confirmation = confirm(`Apakah Anda yakin ingin menghapus buku "${book.name}" (${book.code})?`);
          
          if (!confirmation) return;

          try {
            const url = this.routes.deleteBook.replace(':id', book.id);
            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });

            const result = await response.json();

            if (result.success) {
              await this.loadBooks();
              this.showNotification(result.message, 'success');
            } else {
              this.showNotification(result.message, 'error');
            }
          } catch (error) {
            console.error('Delete error:', error);
            this.showNotification('Gagal menghapus buku', 'error');
          }
        },

        async closeBook(book) {
          if (book.status !== 'active' || book.is_locked) {
            this.showNotification('Buku harus aktif dan tidak terkunci untuk ditutup', 'error');
            return;
          }

          const confirmation = confirm(`Apakah Anda yakin ingin menutup buku "${book.name}" (${book.code})?\n\nSetelah ditutup, buku tidak dapat diubah lagi dan akan terkunci secara permanen.`);
          
          if (!confirmation) return;

          try {
            const url = this.routes.closeBook.replace(':id', book.id);
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });

            const result = await response.json();

            if (result.success) {
              await this.loadBooks();
              this.showNotification(result.message, 'success');
            } else {
              this.showNotification(result.message, 'error');
            }
          } catch (error) {
            console.error('Close book error:', error);
            this.showNotification('Gagal menutup buku', 'error');
          }
        },

        viewBook(id) {
            const url = '{{ route("finance.buku.detail", ["id" => ":id"]) }}'.replace(':id', id);
            window.location.href = url;
        },

        initCharts() {
            if (typeof Chart === 'undefined') return;
            
            // Tunggu DOM benar-benar ready
            setTimeout(() => {
                this.initializeChartsSafely();
            }, 300);
        },

        initializeChartsSafely() {
            try {
                // Clear existing charts
                this.clearExistingCharts();
                
                // Initialize charts
                this.initializeActivityChart();
                this.initializeStatusChart();
                
            } catch (error) {
                console.error('Chart initialization failed:', error);
            }
        },

        clearExistingCharts() {
            // Clear activity chart
            if (this.activityChart) {
                try {
                    this.activityChart.destroy();
                } catch (e) {
                    console.warn('Error clearing activity chart:', e);
                }
                this.activityChart = null;
            }
            
            // Clear status chart
            if (this.statusChart) {
                try {
                    this.statusChart.destroy();
                } catch (e) {
                    console.warn('Error clearing status chart:', e);
                }
                this.statusChart = null;
            }
        },

        async loadRealChartData() {
            // Cegah recursive call
            if (this.loading || this.reinitializingChart) {
                return;
            }
            
            this.loading = true;

            try {
                const activityData = await this.loadActivityData();
                
                if (this.activityChart && activityData) {
                    await this.safeUpdateActivityChart(activityData);
                } else if (!this.activityChart) {
                    // Jika chart belum ada, initialize
                    this.initializeActivityChartWithData(activityData);
                }
            } catch (error) {
                console.error('Error loading real chart data:', error);
            } finally {
                this.loading = false;
            }
        },

        updateActivityChart(activityData) {
            if (!this.activityChart || !activityData) return;

            try {
                this.activityChart.data.labels = activityData.labels || [];
                this.activityChart.data.datasets[0].data = activityData.datasets?.[0]?.data || [];
                this.activityChart.update('none');
            } catch (error) {
                console.warn('Activity chart update failed:', error);
            }
        },

        async safeUpdateActivityChart(activityData) {
            if (!this.activityChart || !this.activityChart.data) {
                return;
            }

            try {
                // Update data secara langsung tanpa kompleksitas
                this.activityChart.data.labels = activityData.labels || [];
                
                if (activityData.datasets && activityData.datasets[0]) {
                    this.activityChart.data.datasets[0].data = activityData.datasets[0].data || [];
                }
                
                // Update chart dengan timeout untuk hindari race condition
                setTimeout(() => {
                    if (this.activityChart) {
                        this.activityChart.update('none');
                    }
                }, 50);
                
            } catch (updateError) {
                console.warn('Activity chart update failed:', updateError);
                // Jangan reinitialize otomatis, biarkan user refresh manual
            }
        },

        initializeActivityChartWithData(activityData = null) {
            const activityCanvas = this.$refs.bookActivityChart;
            if (!activityCanvas || !activityCanvas.getContext) return;

            const activityCtx = activityCanvas.getContext('2d');
            if (!activityCtx) return;

            // Data default jika tidak ada activityData
            const defaultData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Entri Jurnal',
                    data: [0, 0, 0, 0, 0, 0],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            };

            this.activityChart = new Chart(activityCtx, {
                type: 'line',
                data: activityData || defaultData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    animation: false
                }
            });
        },

        safeChartInitialization() {
            try {
                this.destroyChartsSafely();
                
                // Tunggu sebentar sebelum initialize baru
                setTimeout(() => {
                    this.initializeActivityChart();
                    this.initializeStatusChart();
                }, 150);
                
            } catch (error) {
                console.error('Chart initialization error:', error);
            }
        },

        destroyChartsSafely() {
            // Set flag reinitializing
            this.reinitializingChart = true;
            
            // Destroy activity chart
            if (this.activityChart) {
                try {
                    if (typeof this.activityChart.destroy === 'function') {
                        this.activityChart.destroy();
                    }
                } catch (e) {
                    console.warn('Error destroying activity chart:', e);
                }
                this.activityChart = null;
            }
            
            // Destroy status chart
            if (this.statusChart) {
                try {
                    if (typeof this.statusChart.destroy === 'function') {
                        this.statusChart.destroy();
                    }
                } catch (e) {
                    console.warn('Error destroying status chart:', e);
                }
                this.statusChart = null;
            }
            
            // Reset flag setelah destroy selesai
            setTimeout(() => {
                this.reinitializingChart = false;
            }, 100);
        },

        initializeActivityChart() {
            const canvas = this.$refs.bookActivityChart;
            if (!canvas || !canvas.getContext) {
                console.warn('Activity chart canvas not found');
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            this.activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Entri Jurnal',
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        },

        initializeStatusChart() {
            const canvas = this.$refs.bookStatusChart;
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            this.statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Nonaktif', 'Draft', 'Ditutup'],
                    datasets: [{
                        data: [
                            this.bookStats.activeBooks || 0,
                            this.bookStats.inactiveBooks || 0,
                            this.bookStats.draftBooks || 0,
                            this.bookStats.closedBooks || 0
                        ],
                        backgroundColor: ['#10b981', '#ef4444', '#6b7280', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        },


        hideChartContainers() {
            // Fallback: sembunyikan chart containers jika error
            const chartContainers = document.querySelectorAll('[x-ref*="Chart"]');
            chartContainers.forEach(container => {
                if (container) {
                    container.style.display = 'none';
                }
            });
        },

        async loadActivityData() {
            try {
                const params = new URLSearchParams({
                    outlet_id: this.selectedOutlet,
                    period: this.chartPeriod
                });

                const response = await fetch(`{{ route('finance.book-activity.data') }}?${params}`);
                const result = await response.json();

                if (result.success) {
                    return result.data;
                }
                throw new Error(result.message);
            } catch (error) {
                console.error('Error loading activity data:', error);
                return this.getFallbackActivityData();
            }
        },

        getFallbackActivityData() {
            const periodCount = this.chartPeriod === 'yearly' ? 5 : 
                              this.chartPeriod === 'quarterly' ? 4 : 6;
            
            let labels = [];
            if (this.chartPeriod === 'monthly') {
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'].slice(0, periodCount);
            } else if (this.chartPeriod === 'quarterly') {
                labels = ['Q1', 'Q2', 'Q3', 'Q4'].slice(0, periodCount);
            } else {
                const currentYear = new Date().getFullYear();
                labels = Array.from({length: periodCount}, (_, i) => (currentYear - periodCount + i + 1).toString());
            }
            
            const data = labels.map(() => Math.floor(Math.random() * 50) + 10);

            return {
                labels: labels,
                datasets: [{
                    label: 'Entri Jurnal',
                    data: data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            };
        },

        updateCharts() {
            // Update status chart dengan data terbaru
            if (this.statusChart) {
                this.updateStatusChart();
            }
            
            // Update activity chart dengan data terbaru
            this.loadActivityData().then(activityData => {
                if (activityData && this.activityChart) {
                    this.updateActivityChart(activityData);
                }
            });
        },

        updateStatusChart() {
            if (!this.statusChart || !this.statusChart.data || !this.statusChart.data.datasets) {
                return;
            }

            try {
                this.statusChart.data.datasets[0].data = [
                    this.bookStats.activeBooks || 0,
                    this.bookStats.inactiveBooks || 0,
                    this.bookStats.draftBooks || 0,
                    this.bookStats.closedBooks || 0
                ];
                this.statusChart.update('none');
            } catch (error) {
                console.warn('Status chart update error:', error);
            }
        },

        showNotification(message, type = 'info') {
          const notification = document.createElement('div');
          const bgColor = type === 'success' ? 'bg-green-500' : 
                         type === 'error' ? 'bg-red-500' : 'bg-blue-500';
          
          notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
          notification.textContent = message;
          document.body.appendChild(notification);

          setTimeout(() => {
            notification.classList.add('opacity-100');
          }, 10);

          setTimeout(() => {
            notification.classList.remove('opacity-100');
            setTimeout(() => {
              if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
              }
            }, 300);
          }, 3000);
        },

        getTypeName(type) {
          const names = {
            'general': 'Umum',
            'cash': 'Kas',
            'bank': 'Bank',
            'sales': 'Penjualan',
            'purchase': 'Pembelian',
            'inventory': 'Persediaan',
            'payroll': 'Penggajian'
          };
          return names[type] || type;
        },

        getStatusName(status) {
          const names = {
            'active': 'Aktif',
            'inactive': 'Nonaktif',
            'draft': 'Draft',
            'closed': 'Ditutup'
          };
          return names[status] || status;
        },

        getStatusBadgeClass(status) {
          const classes = {
            'active': 'bg-green-100 text-green-800',
            'inactive': 'bg-red-100 text-red-800',
            'draft': 'bg-slate-100 text-slate-800',
            'closed': 'bg-purple-100 text-purple-800'
          };
          return classes[status] || 'bg-gray-100 text-gray-800';
        },

        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(amount);
        },

        formatDate(dateString) {
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
          });
        },

        async exportToXLSX() {
          try {
            this.loading = true;
            
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              type: this.filters.type,
              status: this.filters.status,
              search: this.filters.search
            });

            const url = `{{ route('finance.accounting-books.export.xlsx') }}?${params}`;
            
            // Create a temporary link to trigger download
            const link = document.createElement('a');
            link.href = url;
            link.download = `accounting_books_${Date.now()}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            this.showNotification('Data berhasil diekspor ke XLSX', 'success');
          } catch (error) {
            console.error('Export XLSX error:', error);
            this.showNotification('Gagal mengekspor data: ' + error.message, 'error');
          } finally {
            this.loading = false;
          }
        },

        async exportToPDF() {
          try {
            this.loading = true;
            
            const params = new URLSearchParams({
              outlet_id: this.selectedOutlet,
              type: this.filters.type,
              status: this.filters.status,
              search: this.filters.search
            });

            const url = `{{ route('finance.accounting-books.export.pdf') }}?${params}`;
            
            // Open PDF in new tab
            window.open(url, '_blank');
            
            this.showNotification('PDF berhasil digenerate', 'success');
          } catch (error) {
            console.error('Export PDF error:', error);
            this.showNotification('Gagal mengekspor data: ' + error.message, 'error');
          } finally {
            this.loading = false;
          }
        },

        handleChartPeriodChange() {
            // Reinitialize charts untuk periode baru
            this.initializeChartsSafely();
        },
      };
    }
  </script>

  <style>
    .bg-orange-25 {
      background-color: #fff7ed;
    }
  </style>
</x-layouts.admin>
