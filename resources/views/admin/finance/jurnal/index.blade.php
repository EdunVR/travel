<x-layouts.admin :title="'Jurnal Umum'">
  <div x-data="journalsManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Jurnal Umum</h1>
        <p class="text-slate-600 text-sm">Kelola pencatatan transaksi keuangan dalam jurnal umum</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- Checkbox Outlet Filter --}}
        <div class="relative">
          <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                  class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-primary-200 flex items-center justify-between min-w-[200px]">
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

        @hasPermission('finance.jurnal.create')
        <button @click="openCreateJournal()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700">
          <i class='bx bx-plus'></i> Buat Jurnal
        </button>
        @endhasPermission
        
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

        {{-- Import Button --}}
        <button @click="openImportModal()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
          <i class='bx bx-import'></i> Import
        </button>
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

      {{-- Quick Stats --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <i class='bx bx-edit text-2xl text-blue-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="journalStats.totalJournals"></div>
              <div class="text-sm text-slate-600">Total Jurnal</div>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx bx-plus-circle text-green-500'></i>
            <span class="text-green-600" x-text="journalStats.thisMonth"></span>
            <span class="text-slate-500">bulan ini</span>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
              <i class='bx bx-check-circle text-2xl text-green-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(journalStats.totalDebit)"></div>
              <div class="text-sm text-slate-600">Total Debit</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            Seluruh periode
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
              <i class='bx bx-x-circle text-2xl text-red-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(journalStats.totalCredit)"></div>
              <div class="text-sm text-slate-600">Total Kredit</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            Seluruh periode
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
              <i class='bx bx-line-chart text-2xl text-purple-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="journalStats.balancedJournals"></div>
              <div class="text-sm text-slate-600">Jurnal Seimbang</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="journalStats.unbalancedJournals"></span> perlu penyesuaian
          </div>
        </div>
      </div>

      {{-- Recent Unbalanced Journals --}}
      <div x-show="unbalancedJournals.length > 0" class="rounded-2xl border border-orange-200 bg-orange-50 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <i class='bx bx-error-circle text-orange-600 text-xl'></i>
            <h3 class="text-lg font-semibold text-orange-800">Jurnal Tidak Seimbang</h3>
          </div>
          <span class="text-orange-700 font-medium" x-text="unbalancedJournals.length + ' jurnal'"></span>
        </div>
        <div class="space-y-3">
          <template x-for="journal in unbalancedJournals" :key="journal.id">
            <div class="flex items-center justify-between p-3 rounded-lg border border-orange-200 bg-white">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                  <i class='bx bx-error text-orange-600'></i>
                </div>
                <div>
                  <div class="font-medium text-slate-800" x-text="journal.reference + ' - ' + journal.description"></div>
                  <div class="text-xs text-slate-500" x-text="journal.date_formatted"></div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-semibold text-orange-600" x-text="'Selisih: ' + formatCurrency(Math.abs(journal.balance))"></div>
                <div class="text-xs text-slate-500">Debit: <span x-text="formatCurrency(journal.total_debit)"></span> | Kredit: <span x-text="formatCurrency(journal.total_credit)"></span></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      {{-- Journal Table --}}
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
              <h2 class="text-lg font-semibold text-slate-800">Daftar Jurnal Umum</h2>
              <div class="flex flex-wrap gap-2">
                  <select x-model="filters.book_id" @change="loadJournals()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="all">Semua Buku</option>
                      <template x-for="book in availableBooks" :key="book.id">
                          <option :value="book.id" x-text="book.name"></option>
                      </template>
                  </select>
                  <select x-model="filters.status" @change="loadJournals()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="all">Semua Status</option>
                      <option value="draft">Draft</option>
                      <option value="posted">Diposting</option>
                      <option value="void">Dibatalkan</option>
                  </select>
                  <input type="date" x-model="filters.date_from" @change="loadJournals()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <input type="date" x-model="filters.date_to" @change="loadJournals()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  <input type="text" x-model="filters.search" @input.debounce.500ms="loadJournals()" 
                        placeholder="Cari jurnal..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
              </div>
          </div>

          <div class="overflow-x-auto">
              <table class="w-full text-sm">
                  <thead class="bg-slate-50">
                      <tr>
                          <th class="px-4 py-3 text-left w-12">No</th>
                          <th class="px-4 py-3 text-left">Tanggal</th>
                          <th class="px-4 py-3 text-left">No. Transaksi</th>
                          <th class="px-4 py-3 text-left">Keterangan</th>
                          <th class="px-4 py-3 text-left">Buku</th>
                          <th class="px-4 py-3 text-right">Total Debit</th>
                          <th class="px-4 py-3 text-right">Total Kredit</th>
                          <th class="px-4 py-3 text-right">Saldo</th>
                          <th class="px-4 py-3 text-left">Status</th>
                          <th class="px-4 py-3 text-left w-40">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                      <template x-if="journalsData.length === 0 && !loading">
                          <tr>
                              <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                                  <div class="flex flex-col items-center gap-2">
                                      <i class='bx bx-file-blank text-3xl text-slate-300'></i>
                                      <span>Tidak ada data jurnal</span>
                                  </div>
                              </td>
                          </tr>
                      </template>
                      <template x-for="(journal, index) in journalsData" :key="journal.id">
                          <tr class="border-t border-slate-100 hover:bg-slate-50 group" 
                              :class="journal.balance !== 0 ? 'bg-red-25' : ''">
                              <td class="px-4 py-3" x-text="index + 1"></td>
                              <td class="px-4 py-3">
                                  <div class="font-medium" x-text="journal.date_formatted"></div>
                              </td>
                              <td class="px-4 py-3">
                                  <div class="font-mono text-sm" x-text="journal.reference"></div>
                              </td>
                              <td class="px-4 py-3">
                                  <div class="font-medium text-slate-800" x-text="journal.description"></div>
                                  <div class="text-xs text-slate-500" x-text="journal.entries_count + ' entri'"></div>
                                  
                                  {{-- Expandable Account Details --}}
                                  <div class="mt-2">
                                      <button @click="toggleJournalDetails(journal.id)" 
                                              class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                          <i class='bx' :class="journal.showDetails ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                                          <span x-text="journal.showDetails ? 'Sembunyikan' : 'Lihat Detail Akun'"></span>
                                      </button>
                                      
                                      <div x-show="journal.showDetails" class="mt-2 p-2 bg-slate-50 rounded-lg">
                                          <div class="text-xs font-medium text-slate-700 mb-1">Rincian Akun:</div>
                                          <div class="space-y-1">
                                              <template x-for="entry in journal.entries" :key="entry.id">
                                                  <div class="flex justify-between items-center text-xs">
                                                      <div class="flex items-center gap-2">
                                                          <span class="font-mono" x-text="entry.account_code"></span>
                                                          <span x-text="entry.account_name"></span>
                                                      </div>
                                                      <div class="flex gap-4">
                                                          <span class="font-mono text-green-600" 
                                                                x-text="entry.debit > 0 ? formatCurrency(entry.debit) : ''"></span>
                                                          <span class="font-mono text-red-600" 
                                                                x-text="entry.credit > 0 ? formatCurrency(entry.credit) : ''"></span>
                                                      </div>
                                                  </div>
                                              </template>
                                          </div>
                                      </div>
                                  </div>
                              </td>
                              <td class="px-4 py-3">
                                  <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800" 
                                        x-text="journal.book_name"></span>
                              </td>
                              <td class="px-4 py-3 text-right">
                                  <div class="font-semibold text-green-600" 
                                      x-text="formatCurrency(journal.total_debit)"></div>
                              </td>
                              <td class="px-4 py-3 text-right">
                                  <div class="font-semibold text-red-600" 
                                      x-text="formatCurrency(journal.total_credit)"></div>
                              </td>
                              <td class="px-4 py-3 text-right">
                                  <div class="font-semibold" 
                                      :class="journal.balance === 0 ? 'text-blue-600' : 'text-orange-600'" 
                                      x-text="formatCurrency(journal.balance)"></div>
                              </td>
                              <td class="px-4 py-3">
                                  <span :class="getStatusBadgeClass(journal.status)" 
                                        x-text="getStatusName(journal.status)" 
                                        class="px-2 py-1 rounded-full text-xs"></span>
                              </td>
                              <td class="px-4 py-3">
                                  <div class="flex items-center gap-2">
                                      <button @click="viewJournal(journal)" 
                                              class="text-blue-600 hover:text-blue-800 p-1 rounded" 
                                              title="Lihat Detail">
                                          <i class="bx bx-show text-lg"></i>
                                      </button>
                                      @hasPermission('finance.jurnal.edit')
                                      <button @click="editJournal(journal)" 
                                              :disabled="journal.status !== 'draft'"
                                              :class="journal.status !== 'draft' ? 'text-slate-400 cursor-not-allowed' : 'text-green-600 hover:text-green-800'"
                                              class="p-1 rounded"
                                              title="Edit">
                                          <i class="bx bx-edit text-lg"></i>
                                      </button>
                                      @endhasPermission
                                      <button @click="postJournal(journal.id)" 
                                              x-show="journal.status === 'draft' && journal.balance === 0"
                                              class="text-purple-600 hover:text-purple-800 p-1 rounded" 
                                              title="Posting">
                                          <i class="bx bx-check text-lg"></i>
                                      </button>
                                      @hasPermission('finance.jurnal.delete')
                                      <button @click="deleteJournal(journal.id)" 
                                              :disabled="journal.status !== 'draft'"
                                              :class="journal.status !== 'draft' ? 'text-slate-400 cursor-not-allowed' : 'text-red-600 hover:text-red-800'"
                                              class="p-1 rounded"
                                              title="Hapus">
                                          <i class="bx bx-trash text-lg"></i>
                                      </button>
                                      @endhasPermission
                                      
                                      {{-- Tombol Hapus Khusus Superadmin untuk Jurnal Posted --}}
                                      @if(auth()->user()->role->name === 'super_admin')
                                      <button @click="deleteSuperadminJournal(journal.id)" 
                                              x-show="journal.status === 'posted'"
                                              class="text-orange-600 hover:text-orange-800 p-1 rounded"
                                              title="Hapus (Superadmin Only)">
                                          <i class="bx bx-trash-alt text-lg"></i>
                                      </button>
                                      @endif
                                  </div>
                              </td>
                          </tr>
                      </template>
                  </tbody>
              </table>
          </div>
      </div>

    </div>

    {{-- Modal Create/Edit Journal --}}
    <div x-show="showJournalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-10 z-50 overflow-y-auto">
      <div class="bg-white rounded-2xl w-full max-w-6xl my-4">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800" x-text="editingJournal ? 'Edit Jurnal' : 'Buat Jurnal Baru'"></h3>
        </div>
        
        <form @submit.prevent="saveJournal()">
          <div class="p-6 space-y-4">
            {{-- Error Validation --}}
            <div x-show="formErrors.length > 0" class="rounded-xl bg-red-50 border border-red-200 p-4">
              <ul class="list-disc list-inside text-red-800 text-sm">
                <template x-for="error in formErrors" :key="error">
                  <li x-text="error"></li>
                </template>
              </ul>
            </div>

            {{-- Journal Header --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Jurnal *</label>
                <input type="date" x-model="journalForm.transaction_date" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       :class="formErrors.transaction_date ? 'border-red-300' : ''"
                       required>
                <p x-show="formErrors.transaction_date" class="mt-1 text-red-600 text-xs" x-text="formErrors.transaction_date"></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">No. Transaksi</label>
                <input type="text" x-model="journalForm.transaction_number" 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50"
                       readonly>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Buku *</label>
                <select x-model="journalForm.book_id" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        :class="formErrors.book_id ? 'border-red-300' : ''"
                        required>
                  <option value="">Pilih Buku</option>
                  <template x-for="book in availableBooks" :key="book.id">
                    <option :value="book.id" x-text="book.name + ' (' + book.code + ')'"></option>
                  </template>
                </select>
                <p x-show="formErrors.book_id" class="mt-1 text-red-600 text-xs" x-text="formErrors.book_id"></p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan *</label>
              <textarea x-model="journalForm.description" rows="2" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        :class="formErrors.description ? 'border-red-300' : ''"
                        placeholder="Deskripsi transaksi..."
                        required></textarea>
              <p x-show="formErrors.description" class="mt-1 text-red-600 text-xs" x-text="formErrors.description"></p>
            </div>

            {{-- Journal Entries --}}
            <div>
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-slate-800">Entri Jurnal *</h4>
                <button type="button" @click="addEntry()" 
                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                  <i class='bx bx-plus'></i> Tambah Entri
                </button>
              </div>

              <div class="space-y-3">
                <template x-for="(entry, index) in journalForm.entries" :key="index">
                  <div class="grid grid-cols-12 gap-3 items-start p-3 rounded-lg border border-slate-200">
                    <div class="col-span-5 relative">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Akun *</label>
                      <input type="text" 
                             x-model="entry.account_name"
                             @input="searchAccountsFunc($event.target.value, index)"
                             @focus="searchAccountsFunc(entry.account_name, index)"
                             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                             :class="formErrors['entries.' + index + '.account_id'] ? 'border-red-300' : ''"
                             placeholder="Ketik untuk mencari akun..."
                             required>
                      
                      {{-- Search Results Dropdown --}}
                      <div x-show="searchAccounts.length > 0 && currentSearchIndex === index" 
                           class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="account in searchAccounts" :key="account.id">
                          <div @click="selectAccount(account, index)"
                               class="px-3 py-2 hover:bg-slate-100 cursor-pointer text-sm border-b border-slate-100">
                            <div class="font-mono text-xs text-slate-500" x-text="account.code"></div>
                            <div class="text-slate-800" x-text="account.name"></div>
                            <div class="text-xs text-slate-500" x-text="account.type_name"></div>
                          </div>
                        </template>
                      </div>
                      
                      <input type="hidden" x-model="entry.account_id">
                    </div>
                    <div class="col-span-3">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan Entri</label>
                      <input type="text" x-model="entry.description" 
                             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                             placeholder="Keterangan entri">
                    </div>
                    <div class="col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Debit</label>
                      <input type="number" x-model="entry.debit" 
                             @input="calculateTotals()"
                             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-right" 
                             placeholder="0"
                             step="0.01"
                             min="0">
                    </div>
                    <div class="col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Kredit</label>
                      <input type="number" x-model="entry.credit" 
                             @input="calculateTotals()"
                             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-right" 
                             placeholder="0"
                             step="0.01"
                             min="0">
                    </div>
                    <div class="col-span-1 flex items-end">
                      <button type="button" @click="removeEntry(index)" 
                              class="w-full text-red-600 hover:text-red-800 p-2"
                              x-show="journalForm.entries.length > 2">
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>
                  </div>
                </template>
              </div>

              {{-- Totals --}}
              <div class="mt-4 p-4 rounded-lg border border-slate-200 bg-slate-50">
                <div class="grid grid-cols-2 gap-4">
                  <div class="text-center">
                    <div class="text-sm text-slate-600">Total Debit</div>
                    <div class="text-lg font-bold text-green-600" x-text="formatCurrency(journalForm.total_debit)"></div>
                  </div>
                  <div class="text-center">
                    <div class="text-sm text-slate-600">Total Kredit</div>
                    <div class="text-lg font-bold text-red-600" x-text="formatCurrency(journalForm.total_credit)"></div>
                  </div>
                </div>
                <div class="mt-2 text-center">
                  <div class="text-sm font-medium" 
                        :class="journalForm.balance === 0 ? 'text-green-600' : 'text-orange-600'"
                        x-text="journalForm.balance === 0 ? '✓ Jurnal Seimbang' : '✗ Tidak Seimbang - Selisih: ' + formatCurrency(Math.abs(journalForm.balance))"></div>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
              <textarea x-model="journalForm.notes" rows="2" 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-between items-center">
            <div class="text-sm text-slate-500">
              <span x-text="journalForm.entries.length"></span> entri
            </div>
            <div class="flex gap-3">
              <button type="button" @click="showJournalModal = false" 
                      class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
                Batal
              </button>
              <button type="button" @click="saveAsDraft()"
                      :disabled="saving"
                      class="px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Draft'"></span>
                <i x-show="saving" class='bx bx-loader-alt animate-spin'></i>
              </button>
              <button type="submit" 
                      :disabled="saving || journalForm.balance !== 0" 
                      :class="journalForm.balance !== 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'" 
                      class="px-4 py-2 text-white text-sm rounded-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <span x-text="saving ? 'Menyimpan...' : 'Simpan & Posting'"></span>
                <i x-show="saving" class='bx bx-loader-alt animate-spin'></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- Import Modal --}}
    <div x-show="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50">
      <div class="bg-white rounded-2xl w-full max-w-3xl" @click.away="closeImportModal()">
        <div class="p-6 border-b border-slate-200">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-lg font-semibold text-slate-800">Import Jurnal dari Excel</h3>
              <p class="text-sm text-slate-600 mt-1">Upload file Excel untuk mengimpor data jurnal</p>
            </div>
            <a href="{{ route('finance.journals.template') }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-blue-200 transition-colors">
              <i class='bx bx-download'></i>
              Download Template
            </a>
          </div>
        </div>
        
        <div class="p-6 space-y-4">
          {{-- File Upload Area --}}
          <div x-show="!importFile" 
               @drop.prevent="handleFileDrop($event)" 
               @dragover.prevent 
               @dragenter.prevent="isDragging = true"
               @dragleave.prevent="isDragging = false"
               :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300'"
               class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors"
               @click="$refs.fileInput.click()">
            <div class="flex flex-col items-center gap-3">
              <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">
                <i class='bx bx-cloud-upload text-3xl text-blue-600'></i>
              </div>
              <div>
                <p class="text-slate-700 font-medium">Klik untuk memilih file atau drag & drop</p>
                <p class="text-sm text-slate-500 mt-1">File Excel (.xlsx, .xls) maksimal 5MB</p>
              </div>
            </div>
            <input type="file" 
                   x-ref="fileInput" 
                   @change="handleFileSelect($event)" 
                   accept=".xlsx,.xls" 
                   class="hidden">
          </div>

          {{-- Selected File Info --}}
          <div x-show="importFile" class="border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                  <i class='bx bx-file text-xl text-green-600'></i>
                </div>
                <div>
                  <p class="font-medium text-slate-800" x-text="importFile?.name"></p>
                  <p class="text-sm text-slate-500" x-text="formatFileSize(importFile?.size)"></p>
                </div>
              </div>
              <button @click="clearImportFile()" class="text-red-600 hover:text-red-800 p-2">
                <i class='bx bx-x text-xl'></i>
              </button>
            </div>
          </div>

          {{-- Upload Progress --}}
          <div x-show="isUploading" class="space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="text-slate-600">Mengupload...</span>
              <span class="font-medium text-blue-600" x-text="uploadProgress + '%'"></span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
              <div class="bg-blue-600 h-full transition-all duration-300" 
                   :style="`width: ${uploadProgress}%`"></div>
            </div>
          </div>

          {{-- Import Results --}}
          <div x-show="importResults" class="space-y-3">
            <div x-show="importResults?.success" class="rounded-xl bg-green-50 border border-green-200 p-4">
              <div class="flex items-start gap-3">
                <i class='bx bx-check-circle text-2xl text-green-600'></i>
                <div class="flex-1">
                  <h4 class="font-semibold text-green-800">Import Berhasil!</h4>
                  <p class="text-sm text-green-700 mt-1" x-text="importResults?.message"></p>
                  <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-white rounded-lg p-2">
                      <span class="text-slate-600">Berhasil diimpor:</span>
                      <span class="font-bold text-green-600 ml-1" x-text="importResults?.imported_count"></span>
                    </div>
                    <div class="bg-white rounded-lg p-2">
                      <span class="text-slate-600">Dilewati:</span>
                      <span class="font-bold text-orange-600 ml-1" x-text="importResults?.skipped_count"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div x-show="!importResults?.success" class="rounded-xl bg-red-50 border border-red-200 p-4">
              <div class="flex items-start gap-3">
                <i class='bx bx-error-circle text-2xl text-red-600'></i>
                <div class="flex-1">
                  <h4 class="font-semibold text-red-800">Import Gagal</h4>
                  <p class="text-sm text-red-700 mt-1" x-text="importResults?.message"></p>
                </div>
              </div>
            </div>

            {{-- Error Details --}}
            <div x-show="importResults && importResults.errors && Array.isArray(importResults.errors) && importResults.errors.length > 0" 
                 class="rounded-xl border border-orange-200 bg-orange-50 p-4 max-h-60 overflow-y-auto">
              <h5 class="font-semibold text-orange-800 mb-2">Detail Error:</h5>
              <ul class="space-y-1 text-sm text-orange-700">
                <template x-for="error in (importResults?.errors || [])" :key="error">
                  <li class="flex items-start gap-2">
                    <i class='bx bx-error text-orange-600 mt-0.5'></i>
                    <span x-text="error"></span>
                  </li>
                </template>
              </ul>
            </div>
          </div>

          {{-- Download Template Link --}}
          <div class="border-t border-slate-200 pt-4">
            <a href="{{ route('finance.journals.template') }}" 
               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800">
              <i class='bx bx-download'></i>
              <span>Download Template Excel</span>
            </a>
          </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="closeImportModal()" 
                  :disabled="isUploading"
                  class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-text="importResults ? 'Tutup' : 'Batal'"></span>
          </button>
          <button @click="uploadImportFile()" 
                  x-show="importFile && !importResults"
                  :disabled="isUploading || !importFile"
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <span x-text="isUploading ? 'Mengupload...' : 'Upload & Import'"></span>
            <i x-show="isUploading" class='bx bx-loader-alt animate-spin'></i>
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    function journalsManagement() {
      return {
        // State
        loading: false,
        saving: false,
        error: null,
        showJournalModal: false,
        showImportModal: false,
        editingJournal: null,
        selectedOutlets: [],
        showOutletDropdown: false,
        outlets: [],
    
        currentSearchIndex: null,
        isExporting: false,
        isUploading: false,
        isDragging: false,
        importFile: null,
        uploadProgress: 0,
        importResults: null,
        
        // Data
        journalForm: {
          book_id: '',
          transaction_date: '',
          transaction_number: '',
          description: '',
          entries: [],
          notes: '',
          total_debit: 0,
          total_credit: 0,
          balance: 0,
          status: 'posted'
        },
        filters: {
          book_id: 'all',
          status: 'all',
          date_from: '',
          date_to: '',
          search: ''
        },
        journalStats: {
          totalJournals: 0,
          thisMonth: 0,
          totalDebit: 0,
          totalCredit: 0,
          balancedJournals: 0,
          unbalancedJournals: 0
        },
        journalsData: [],
        unbalancedJournals: [],
        availableAccounts: [],
        availableBooks: [],
        formErrors: [],
        searchAccounts: [],
        searchTimeout: null,

        // Routes
        routes: {
          journalsData: '{{ route("finance.journals.data") }}',
          journalStats: '{{ route("finance.journals.stats") }}',
          showJournal: '{{ route("finance.journals.show", ["id" => ":id"]) }}',
          storeJournal: '{{ route("finance.journals.store") }}',
          updateJournal: '{{ route("finance.journals.update", ["id" => ":id"]) }}',
          postJournal: '{{ route("finance.journals.post", ["id" => ":id"]) }}',
          deleteJournal: '{{ route("finance.journals.delete", ["id" => ":id"]) }}',
          deleteSuperadminJournal: '{{ route("finance.journals.delete-superadmin", ["id" => ":id"]) }}',
          outletsData: '{{ route("finance.outlets.data") }}',
          accountingBooksData: '{{ route("finance.accounting-books.data") }}',
          chartOfAccountsData: '{{ route("finance.chart-of-accounts.data") }}',
          bookActivityData: '{{ route("finance.book-activity.data") }}',
          exportJournalXLSX: '{{ route("finance.journals.export.xlsx") }}',
          exportJournalPDF: '{{ route("finance.journals.export.pdf") }}',
          importJournals: '{{ route("finance.journals.import") }}'
        },

        async init() {
          console.log('🚀 Initializing journals management...');
          
          // Get URL parameters
          const urlParams = new URLSearchParams(window.location.search);
          const journalId = urlParams.get('journal_id');
          const outletId = urlParams.get('outlet_id');
          
          await this.loadOutlets();
          
          // Set outlets from parameter or default to first outlet
          if (outletId && this.outlets.some(outlet => outlet.id_outlet == outletId)) {
            this.selectedOutlets = [parseInt(outletId)];
          } else if (this.outlets.length > 0) {
            this.selectedOutlets = [this.outlets[0].id_outlet];
          }
          
          await this.loadAvailableBooks();
          await this.loadJournals();
          await this.loadStats();
          
          // If journal_id parameter exists, open journal detail
          if (journalId) {
            await this.openJournalFromParameter(journalId);
          }
          
        },

        async openJournalFromParameter(journalId) {
          try {
            console.log('📖 Opening journal from parameter:', journalId);
            
            const response = await fetch(this.routes.showJournal.replace(':id', journalId));
            const result = await response.json();

            if (result.success) {
              const journalData = result.data;
              
              // Find journal in current data or create view data
              let journal = this.journalsData.find(j => j.id == journalId);
              if (!journal) {
                journal = {
                  id: journalData.id,
                  reference: journalData.transaction_number,
                  description: journalData.description,
                  date_formatted: new Date(journalData.transaction_date).toLocaleDateString('id-ID'),
                  book_name: journalData.book?.name || '-',
                  total_debit: parseFloat(journalData.total_debit) || 0,
                  total_credit: parseFloat(journalData.total_credit) || 0,
                  balance: parseFloat(journalData.total_debit) - parseFloat(journalData.total_credit),
                  status: journalData.status,
                  entries: journalData.journal_entry_details?.map(detail => ({
                    id: detail.id,
                    account_code: detail.account?.code || '-',
                    account_name: detail.account?.name || '-',
                    debit: parseFloat(detail.debit) || 0,
                    credit: parseFloat(detail.credit) || 0,
                    description: detail.description || ''
                  })) || [],
                  notes: journalData.notes || '',
                  posted_at: journalData.posted_at,
                  showDetails: true
                };
              }
              
              this.viewJournal(journal);
              
              // Remove parameters from URL without reload
              const newUrl = window.location.pathname;
              window.history.replaceState({}, document.title, newUrl);
              
            } else {
              console.error('❌ Journal not found:', journalId);
              this.showNotification('Jurnal tidak ditemukan', 'error');
            }
          } catch (error) {
            console.error('❌ Error opening journal from parameter:', error);
            this.showNotification('Gagal memuat detail jurnal', 'error');
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
              console.log('✅ Loaded outlets:', this.outlets.length);
            }
          } catch (error) {
            console.error('❌ Error loading outlets:', error);
          }
        },

        // Checkbox Management Functions
        getSelectedOutletsText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet === this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Outlet Terpilih';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Terpilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        async onOutletSelectionChange() {
          console.log('🔄 Outlet selection changed:', this.selectedOutlets);
          if (this.selectedOutlets.length > 0) {
            await this.loadAvailableBooks();
            await this.loadJournals();
            await this.loadStats();
          }
        },

        async loadAvailableBooks() {
          try {
            if (this.selectedOutlets.length === 0) {
              this.availableBooks = [];
              return;
            }

            // Use first selected outlet for books (books are outlet-specific)
            const outletId = this.selectedOutlets[0];
            
            const params = new URLSearchParams({
              outlet_id: outletId,
              status: 'active'
            });

            const response = await fetch(`${this.routes.accountingBooksData}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.availableBooks = result.data.filter(book => 
                book.status === 'active' && !book.is_locked
              );
              console.log('✅ Loaded available books:', this.availableBooks.length);
            } else {
              console.error('❌ Failed to load books:', result.message);
              this.availableBooks = [];
            }
          } catch (error) {
            console.error('❌ Error loading books:', error);
            this.availableBooks = [];
          }
        },

        async loadJournals() {
          this.loading = true;
          this.error = null;

          try {
            if (this.selectedOutlets.length === 0) {
              this.journalsData = [];
              this.unbalancedJournals = [];
              this.loading = false;
              return;
            }

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });
            
            // Add other filters
            params.append('book_id', this.filters.book_id);
            params.append('status', this.filters.status);
            if (this.filters.date_from) params.append('date_from', this.filters.date_from);
            if (this.filters.date_to) params.append('date_to', this.filters.date_to);
            if (this.filters.search) params.append('search', this.filters.search);

            const url = `${this.routes.journalsData}?${params}`;
            console.log('📊 Loading journals from:', url);
            
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
              this.journalsData = result.data;
              this.unbalancedJournals = result.unbalanced_journals || [];
              console.log('✅ Loaded journals:', this.journalsData.length);
            } else {
              this.error = result.message;
              console.error('❌ Error loading journals:', result.message);
            }
          } catch (error) {
            this.error = 'Gagal memuat data jurnal: ' + error.message;
            console.error('❌ Error loading journals:', error);
          } finally {
            this.loading = false;
          }
        },

        async loadStats() {
          try {
            if (this.selectedOutlets.length === 0) {
              this.journalStats = {
                totalJournals: 0,
                thisMonth: 0,
                totalDebit: 0,
                totalCredit: 0,
                balancedJournals: 0,
                unbalancedJournals: 0
              };
              return;
            }

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });

            const response = await fetch(`${this.routes.journalStats}?${params}`);
            const result = await response.json();

            if (result.success) {
              this.journalStats = result.data;
              console.log('✅ Loaded journal stats:', this.journalStats);
            } else {
              console.error('❌ Error loading stats:', result.message);
            }
          } catch (error) {
            console.error('❌ Error loading stats:', error);
          }
        },

        async searchAccountsFunc(searchTerm, index) {
          this.currentSearchIndex = index;
          
          if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
          }

          this.searchTimeout = setTimeout(async () => {
            if (!searchTerm || searchTerm.length < 2) {
              this.searchAccounts = [];
              return;
            }

            if (this.selectedOutlets.length === 0) {
              this.searchAccounts = [];
              return;
            }

            try {
              // Use first selected outlet for account search
              const outletId = this.selectedOutlets[0];
              
              const params = new URLSearchParams({
                outlet_id: outletId,
                search: searchTerm,
                status: 'active'
              });

              const response = await fetch(`${this.routes.chartOfAccountsData}?${params}`);
              const result = await response.json();

              if (result.success) {
                this.searchAccounts = result.data.slice(0, 10);
                console.log('🔍 Search results:', this.searchAccounts.length);
              }
            } catch (error) {
              console.error('❌ Error searching accounts:', error);
              this.searchAccounts = [];
            }
          }, 300);
        },

        selectAccount(account, index) {
          this.journalForm.entries[index].account_id = account.id;
          this.journalForm.entries[index].account_name = account.code + ' - ' + account.name;
          this.searchAccounts = [];
          this.currentSearchIndex = null;
        },

        openCreateJournal() {
          this.editingJournal = null;
          this.formErrors = [];
          this.searchAccounts = [];
          this.currentSearchIndex = null;
          this.journalForm = {
            book_id: this.availableBooks[0]?.id || '',
            transaction_date: new Date().toISOString().split('T')[0],
            transaction_number: '',
            description: '',
            entries: [
              { account_id: '', account_name: '', description: '', debit: 0, credit: 0 },
              { account_id: '', account_name: '', description: '', debit: 0, credit: 0 }
            ],
            notes: '',
            total_debit: 0,
            total_credit: 0,
            balance: 0,
            status: 'posted'
          };
          this.generateTransactionNumber();
          this.showJournalModal = true;
        },

        async generateTransactionNumber() {
          const now = new Date();
          const timestamp = now.getTime().toString().slice(-6);
          this.journalForm.transaction_number = `JNL-${timestamp}`;
        },

        addEntry() {
          this.journalForm.entries.push({ 
            account_id: '', 
            account_name: '',
            description: '', 
            debit: 0, 
            credit: 0 
          });
        },

        removeEntry(index) {
          if (this.journalForm.entries.length > 2) {
            this.journalForm.entries.splice(index, 1);
            this.calculateTotals();
          }
        },

        calculateTotals() {
          this.journalForm.total_debit = this.journalForm.entries.reduce((sum, entry) => 
            sum + (parseFloat(entry.debit) || 0), 0);
          this.journalForm.total_credit = this.journalForm.entries.reduce((sum, entry) => 
            sum + (parseFloat(entry.credit) || 0), 0);
          this.journalForm.balance = this.journalForm.total_debit - this.journalForm.total_credit;
        },

        async editJournal(journal) {
          try {
            const response = await fetch(this.routes.showJournal.replace(':id', journal.id));
            const result = await response.json();

            if (result.success) {
              this.editingJournal = journal.id;
              const journalData = result.data;
              
              this.journalForm = {
                book_id: journalData.book_id,
                transaction_date: journalData.transaction_date,
                transaction_number: journalData.transaction_number,
                description: journalData.description,
                entries: journalData.journal_entry_details.map(detail => ({
                  account_id: detail.account_id,
                  account_name: (detail.account ? detail.account.code + ' - ' + detail.account.name : ''),
                  description: detail.description || '',
                  debit: parseFloat(detail.debit) || 0,
                  credit: parseFloat(detail.credit) || 0
                })),
                notes: journalData.notes || '',
                total_debit: parseFloat(journalData.total_debit) || 0,
                total_credit: parseFloat(journalData.total_credit) || 0,
                balance: parseFloat(journalData.total_debit) - parseFloat(journalData.total_credit),
                status: journalData.status
              };
              
              this.formErrors = [];
              this.searchAccounts = [];
              this.showJournalModal = true;
            } else {
              this.showNotification(result.message, 'error');
            }
          } catch (error) {
            this.showNotification('Gagal memuat data jurnal', 'error');
          }
        },

        async saveJournal() {
          await this.saveJournalWithStatus('posted');
        },

        async saveAsDraft() {
          await this.saveJournalWithStatus('draft');
        },

        async saveJournalWithStatus(status) {
          this.saving = true;
          this.formErrors = [];

          // Validate required fields
          if (!this.journalForm.book_id) {
            this.formErrors = ['Buku harus dipilih'];
            this.saving = false;
            return;
          }

          if (!this.journalForm.transaction_date) {
            this.formErrors = ['Tanggal jurnal harus diisi'];
            this.saving = false;
            return;
          }

          if (!this.journalForm.description) {
            this.formErrors = ['Keterangan harus diisi'];
            this.saving = false;
            return;
          }

          // Validate entries
          const hasEmptyAccounts = this.journalForm.entries.some(entry => !entry.account_id);
          if (hasEmptyAccounts) {
            this.formErrors = ['Semua entri harus memiliki akun yang dipilih'];
            this.saving = false;
            return;
          }

          // Validate at least one debit and one credit
          const hasDebit = this.journalForm.entries.some(entry => (parseFloat(entry.debit) || 0) > 0);
          const hasCredit = this.journalForm.entries.some(entry => (parseFloat(entry.credit) || 0) > 0);
          
          if (!hasDebit || !hasCredit) {
            this.formErrors = ['Jurnal harus memiliki minimal satu entri debit dan satu entri kredit'];
            this.saving = false;
            return;
          }

          // Validate balance for posted journals
          if (status === 'posted' && this.journalForm.balance !== 0) {
            this.formErrors = ['Jurnal harus seimbang untuk dapat diposting'];
            this.saving = false;
            return;
          }

          try {
            const url = this.editingJournal 
              ? this.routes.updateJournal.replace(':id', this.editingJournal)
              : this.routes.storeJournal;

            const method = this.editingJournal ? 'PUT' : 'POST';

            const payload = {
              book_id: this.journalForm.book_id,
              transaction_date: this.journalForm.transaction_date,
              description: this.journalForm.description,
              entries: this.journalForm.entries.map(entry => ({
                account_id: entry.account_id,
                description: entry.description,
                debit: parseFloat(entry.debit) || 0,
                credit: parseFloat(entry.credit) || 0
              })),
              notes: this.journalForm.notes,
              status: status,
              outlet_id: this.selectedOutlets.length > 0 ? this.selectedOutlets[0] : null
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

            if (result && result.success) {
              this.showJournalModal = false;
              await this.loadJournals();
              await this.loadStats();
              this.showNotification(result.message || 'Jurnal berhasil disimpan', 'success');
             
            } else {
              if (result && result.errors) {
                this.formErrors = Object.values(result.errors).flat();
              } else if (result && result.message) {
                this.formErrors = [result.message];
              } else {
                this.formErrors = ['Gagal menyimpan jurnal'];
              }
            }
          } catch (error) {
            console.error('❌ Save error:', error);
            this.formErrors = ['Terjadi kesalahan saat menyimpan jurnal'];
          } finally {
            this.saving = false;
          }
        },

        async postJournal(id) {
          if (!confirm('Apakah Anda yakin ingin memposting jurnal ini?')) {
            return;
          }

          try {
            const response = await fetch(this.routes.postJournal.replace(':id', id), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result && result.success) {
              await this.loadJournals();
              await this.loadStats();
              this.showNotification(result.message || 'Jurnal berhasil diposting', 'success');
             
            } else {
              this.showNotification(result && result.message ? result.message : 'Gagal memposting jurnal', 'error');
            }
          } catch (error) {
            console.error('❌ Post journal error:', error);
            this.showNotification('Gagal memposting jurnal', 'error');
          }
        },

        async deleteJournal(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus jurnal ini?')) {
            return;
          }

          try {
            const response = await fetch(this.routes.deleteJournal.replace(':id', id), {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result && result.success) {
              await this.loadJournals();
              await this.loadStats();
              this.showNotification(result.message || 'Jurnal berhasil dihapus', 'success');
            
            } else {
              this.showNotification(result && result.message ? result.message : 'Gagal menghapus jurnal', 'error');
            }
          } catch (error) {
            console.error('❌ Delete journal error:', error);
            this.showNotification('Gagal menghapus jurnal', 'error');
          }
        },

        async deleteSuperadminJournal(id) {
          if (!confirm('PERINGATAN: Anda akan menghapus jurnal yang sudah diposting!\n\nJika jurnal ini berasal dari saldo awal, data saldo awal juga akan ikut terhapus.\n\nApakah Anda yakin ingin melanjutkan?')) {
            return;
          }

          // Konfirmasi kedua untuk keamanan
          if (!confirm('Konfirmasi sekali lagi: Hapus jurnal yang sudah diposting beserta sumber datanya?')) {
            return;
          }

          try {
            const response = await fetch(this.routes.deleteSuperadminJournal.replace(':id', id), {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (result && result.success) {
              await this.loadJournals();
              await this.loadStats();
              this.showNotification(result.message || 'Jurnal berhasil dihapus', 'success');
            
            } else {
              this.showNotification(result && result.message ? result.message : 'Gagal menghapus jurnal', 'error');
            }
          } catch (error) {
            console.error('❌ Delete superadmin journal error:', error);
            this.showNotification('Gagal menghapus jurnal', 'error');
          }
        },

        toggleJournalDetails(journalId) {
          const journal = this.journalsData.find(j => j.id === journalId);
          if (journal) {
            journal.showDetails = !journal.showDetails;
            this.journalsData = [...this.journalsData];
          }
        },

        viewJournal(journal) {
          const modal = document.createElement('div');
          modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
          modal.innerHTML = `
            <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
              <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-slate-800">Detail Jurnal - ${journal.reference}</h3>
                  <button onclick="this.closest('.fixed').remove()" class="text-slate-400 hover:text-slate-600">
                    <i class='bx bx-x text-xl'></i>
                  </button>
                </div>
                <div class="mt-2 text-sm text-slate-600">
                  Outlet: <span class="font-medium">${this.getSelectedOutletsText()}</span>
                </div>
              </div>
              <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No. Transaksi</label>
                    <div class="font-mono text-slate-800 text-sm">${journal.reference}</div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <div class="text-slate-800 text-sm">${journal.date_formatted}</div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Buku</label>
                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">${journal.book_name}</span>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <span class="px-2 py-1 rounded-full text-xs ${this.getStatusBadgeClass(journal.status)}">
                      ${this.getStatusName(journal.status)}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                  <div class="text-slate-800 bg-slate-50 p-3 rounded-lg">${journal.description}</div>
                </div>

                <div>
                  <h4 class="font-semibold text-slate-800 mb-4">Detail Entri Jurnal</h4>
                  <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-slate-200">
                      <thead class="bg-slate-50">
                        <tr>
                          <th class="px-4 py-3 text-left border border-slate-200">Akun</th>
                          <th class="px-4 py-3 text-left border border-slate-200">Nama Akun</th>
                          <th class="px-4 py-3 text-right border border-slate-200">Debit</th>
                          <th class="px-4 py-3 text-right border border-slate-200">Kredit</th>
                          <th class="px-4 py-3 text-left border border-slate-200">Keterangan</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${journal.entries.map(entry => `
                          <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-2 border border-slate-100">
                              <div class="font-mono text-xs">${entry.account_code}</div>
                            </td>
                            <td class="px-4 py-2 border border-slate-100 text-sm">${entry.account_name}</td>
                            <td class="px-4 py-2 border border-slate-100 text-right">
                              ${entry.debit > 0 ? 
                                `<div class="font-mono text-green-600 font-semibold">${this.formatCurrency(entry.debit)}</div>` : 
                                `<div class="font-mono text-slate-400">-</div>`
                              }
                            </td>
                            <td class="px-4 py-2 border border-slate-100 text-right">
                              ${entry.credit > 0 ? 
                                `<div class="font-mono text-red-600 font-semibold">${this.formatCurrency(entry.credit)}</div>` : 
                                `<div class="font-mono text-slate-400">-</div>`
                              }
                            </td>
                            <td class="px-4 py-2 border border-slate-100 text-slate-600 text-sm">${entry.description || '-'}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                      <tfoot class="bg-slate-100 border-t-2 border-slate-300">
                        <tr>
                          <td colspan="2" class="px-4 py-3 text-right font-semibold border border-slate-200">Total:</td>
                          <td class="px-4 py-3 text-right border border-slate-200">
                            <div class="font-mono font-semibold text-green-600">${this.formatCurrency(journal.total_debit)}</div>
                          </td>
                          <td class="px-4 py-3 text-right border border-slate-200">
                            <div class="font-mono font-semibold text-red-600">${this.formatCurrency(journal.total_credit)}</div>
                          </td>
                          <td class="px-4 py-3 border border-slate-200">
                            <div class="flex items-center gap-2 ${journal.balance === 0 ? 'text-green-600' : 'text-orange-600'}">
                              <i class='bx ${journal.balance === 0 ? 'bx-check-circle' : 'bx-error-circle'}'></i>
                              <span class="text-sm font-medium">${journal.balance === 0 ? 'Seimbang' : 'Tidak Seimbang'}</span>
                              ${journal.balance !== 0 ? 
                                `<span class="text-xs">(Selisih: ${this.formatCurrency(Math.abs(journal.balance))})</span>` : 
                                ''}
                            </div>
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                ${journal.notes ? `
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                  <div class="text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">${journal.notes}</div>
                </div>
                ` : ''}

                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                  <div class="text-sm text-slate-500">
                    ${journal.posted_at ? `Diposting: ${new Date(journal.posted_at).toLocaleDateString('id-ID', { 
                      day: '2-digit', 
                      month: 'long', 
                      year: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}` : 'Status: Draft'}
                  </div>
                  <div class="flex gap-2">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
                      Tutup
                    </button>
                    <button onclick="window.print()" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-2">
                      <i class='bx bx-printer'></i> Print
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `;

          document.body.appendChild(modal);
          modal.addEventListener('click', (e) => {
            if (e.target === modal) {
              modal.remove();
            }
          });
        },

        getOutletName(outletId) {
          const outlet = this.outlets.find(o => o.id_outlet == outletId);
          return outlet ? outlet.nama_outlet : 'Unknown Outlet';
        },

        openQuickJournal() {
          this.showNotification('Fitur jurnal cepat akan segera tersedia', 'info');
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

        getStatusBadgeClass(status) {
          const classes = {
            draft: 'bg-yellow-100 text-yellow-800',
            posted: 'bg-green-100 text-green-800',
            void: 'bg-red-100 text-red-800'
          };
          return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusName(status) {
          const names = {
            draft: 'Draft',
            posted: 'Diposting',
            void: 'Dibatalkan'
          };
          return names[status] || status;
        },

        formatCurrency(amount) {
          return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
          }).format(amount);
        },

        getFilterParams() {
          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          if (this.selectedOutlets.length > 0) {
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });
          }
          
          // Add other filters
          params.append('book_id', this.filters.book_id);
          params.append('status', this.filters.status);
          if (this.filters.date_from) params.append('date_from', this.filters.date_from);
          if (this.filters.date_to) params.append('date_to', this.filters.date_to);
          if (this.filters.search) params.append('search', this.filters.search);
          
          return params.toString();
        },

        async exportToXLSX() {
          if (this.isExporting) return;
          
          this.isExporting = true;
          console.log('📥 Exporting journals to XLSX...');

          try {
            const url = `${this.routes.exportJournalXLSX}?${this.getFilterParams()}`;
            const response = await fetch(url, {
              method: 'GET',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            
            const timestamp = new Date().toISOString().slice(0, 10);
            link.download = `jurnal_${timestamp}.xlsx`;
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);

            this.showNotification('Data berhasil diekspor ke XLSX', 'success');
            console.log('✅ Export to XLSX successful');
          } catch (error) {
            console.error('❌ Export to XLSX error:', error);
            this.showNotification('Gagal mengekspor data: ' + error.message, 'error');
          } finally {
            this.isExporting = false;
          }
        },

        async exportToPDF() {
          if (this.isExporting) return;
          
          this.isExporting = true;
          console.log('📥 Exporting journals to PDF...');

          try {
            const url = `${this.routes.exportJournalPDF}?${this.getFilterParams()}`;
            const response = await fetch(url, {
              method: 'GET',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            
            const timestamp = new Date().toISOString().slice(0, 10);
            link.download = `jurnal_${timestamp}.pdf`;
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);

            this.showNotification('Data berhasil diekspor ke PDF', 'success');
            console.log('✅ Export to PDF successful');
          } catch (error) {
            console.error('❌ Export to PDF error:', error);
            this.showNotification('Gagal mengekspor data: ' + error.message, 'error');
          } finally {
            this.isExporting = false;
          }
        },

        // Import Functions
        openImportModal() {
          this.showImportModal = true;
          this.importFile = null;
          this.importResults = null;
          this.uploadProgress = 0;
          console.log('📂 Opening import modal');
        },

        closeImportModal() {
          this.showImportModal = false;
          this.importFile = null;
          this.importResults = null;
          this.uploadProgress = 0;
          this.isDragging = false;
          
          // Reload journals if import was successful
          if (this.importResults?.success) {
            this.loadJournals();
            this.loadStats();
          }
        },

        handleFileSelect(event) {
          const file = event.target.files[0];
          this.validateAndSetFile(file);
        },

        handleFileDrop(event) {
          this.isDragging = false;
          const file = event.dataTransfer.files[0];
          this.validateAndSetFile(file);
        },

        validateAndSetFile(file) {
          if (!file) return;

          // Validate file type
          const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
          if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/i)) {
            this.showNotification('File harus berformat Excel (.xlsx atau .xls)', 'error');
            return;
          }

          // Validate file size (max 5MB)
          const maxSize = 5 * 1024 * 1024;
          if (file.size > maxSize) {
            this.showNotification('Ukuran file maksimal 5MB', 'error');
            return;
          }

          this.importFile = file;
          this.importResults = null;
          console.log('📄 File selected:', file.name);
        },

        clearImportFile() {
          this.importFile = null;
          this.importResults = null;
          this.uploadProgress = 0;
          if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
          }
        },

        async uploadImportFile() {
          if (!this.importFile) {
            this.showNotification('Pilih file terlebih dahulu', 'error');
            return;
          }

          this.isUploading = true;
          this.uploadProgress = 0;
          this.importResults = null;
          console.log('📤 Uploading import file...');

          try {
            const formData = new FormData();
            formData.append('file', this.importFile);
            formData.append('outlet_id', this.selectedOutlets.length > 0 ? this.selectedOutlets[0] : null);

            // Simulate progress
            const progressInterval = setInterval(() => {
              if (this.uploadProgress < 90) {
                this.uploadProgress += 10;
              }
            }, 200);

            const response = await fetch(this.routes.importJournals, {
              method: 'POST',
              body: formData,
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            clearInterval(progressInterval);
            this.uploadProgress = 100;

            const result = await response.json();

            if (result && result.success) {
              this.importResults = {
                success: true,
                message: result.message || 'Import berhasil',
                imported_count: result.imported_count || 0,
                skipped_count: result.skipped_count || 0,
                errors: result.errors || []
              };
              this.showNotification('Import berhasil!', 'success');
              console.log('✅ Import successful:', result);
            } else {
              this.importResults = {
                success: false,
                message: result && result.message ? result.message : 'Import gagal',
                errors: result && result.errors ? result.errors : []
              };
              this.showNotification('Import gagal: ' + (result && result.message ? result.message : 'Unknown error'), 'error');
              console.error('❌ Import failed:', result);
            }
          } catch (error) {
            console.error('❌ Import error:', error);
            this.importResults = {
              success: false,
              message: 'Terjadi kesalahan saat mengimpor data',
              errors: [error.message]
            };
            this.showNotification('Gagal mengimpor data: ' + error.message, 'error');
          } finally {
            this.isUploading = false;
          }
        },

        formatFileSize(bytes) {
          if (!bytes) return '0 Bytes';
          const k = 1024;
          const sizes = ['Bytes', 'KB', 'MB', 'GB'];
          const i = Math.floor(Math.log(bytes) / Math.log(k));
          return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
      };
    }
  </script>

  <style>
    .bg-red-25 {
      background-color: #fef2f2;
    }
  
  </style>
</x-layouts.admin>
