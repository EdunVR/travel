{{-- resources/views/admin/finance/rekonsiliasi/index.blade.php --}}
<x-layouts.admin :title="'Rekonsiliasi Bank'">
  <div x-data="bankReconciliationManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Rekonsiliasi Bank</h1>
        <p class="text-slate-600 text-sm">Kelola rekonsiliasi bank dan monitor selisih saldo</p>
      </div>

      <div class="flex flex-wrap gap-2">
        @hasPermission('finance.rekonsiliasi.create')
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 h-10 text-white hover:bg-primary-700">
          <i class='bx bx-plus'></i> Buat Rekonsiliasi
        </button>
        @endhasPermission
        <button @click="refreshData()" :disabled="isLoading" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 disabled:opacity-50">
          <i class='bx bx-refresh' :class="{'animate-spin': isLoading}"></i> Refresh
        </button>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Total Rekonsiliasi</p>
            <p class="text-2xl font-bold text-slate-800 mt-1" x-text="statistics.total_reconciliations"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
            <i class='bx bx-file text-2xl text-blue-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Draft</p>
            <p class="text-2xl font-bold text-orange-600 mt-1" x-text="statistics.draft"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
            <i class='bx bx-edit text-2xl text-orange-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1" x-text="statistics.completed"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-green-600'></i>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Disetujui</p>
            <p class="text-2xl font-bold text-blue-600 mt-1" x-text="statistics.approved"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
            <i class='bx bx-badge-check text-2xl text-blue-600'></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter Section --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
          <select x-model="filters.outlet_id" @change="loadData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select x-model="filters.status" @change="loadData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="all">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="completed">Selesai</option>
            <option value="approved">Disetujui</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
          <input type="month" x-model="filters.period_month" @change="loadData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Rekening Bank</label>
          <select x-model="filters.bank_account_id" @change="loadData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Rekening</option>
            <template x-for="account in bankAccounts" :key="account.id">
              <option :value="account.id" x-text="account.bank_name + ' - ' + account.account_number"></option>
            </template>
          </select>
        </div>
      </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-slate-600">Memuat data rekonsiliasi...</p>
    </div>

    {{-- Reconciliation Table --}}
    <div x-show="!isLoading && reconciliations.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Periode</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Tanggal</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Outlet</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Bank</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Saldo Bank</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Saldo Buku</th>
              <th class="px-4 py-3 text-right font-semibold text-slate-700">Selisih</th>
              <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="recon in reconciliations" :key="recon.id">
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-3 font-medium" x-text="recon.period_month"></td>
                <td class="px-4 py-3 text-slate-600" x-text="formatDate(recon.reconciliation_date)"></td>
                <td class="px-4 py-3 text-slate-600" x-text="recon.outlet_name"></td>
                <td class="px-4 py-3">
                  <div class="font-medium text-slate-800" x-text="recon.bank_name"></div>
                  <div class="text-xs text-slate-500" x-text="recon.account_number"></div>
                </td>
                <td class="px-4 py-3 text-right font-medium" x-text="formatCurrency(recon.bank_statement_balance)"></td>
                <td class="px-4 py-3 text-right font-medium" x-text="formatCurrency(recon.book_balance)"></td>
                <td class="px-4 py-3 text-right font-semibold" :class="Math.abs(recon.difference) > 0 ? 'text-red-600' : 'text-green-600'" x-text="formatCurrency(recon.difference)"></td>
                <td class="px-4 py-3 text-center">
                  <span x-show="recon.status === 'draft'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    Draft
                  </span>
                  <span x-show="recon.status === 'completed'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Selesai
                  </span>
                  <span x-show="recon.status === 'approved'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Disetujui
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="viewDetail(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-medium">
                      <i class='bx bx-show'></i> Detail
                    </button>
                    @hasPermission('finance.rekonsiliasi.edit')
                    <button x-show="recon.status === 'draft'" @click="editReconciliation(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 text-xs font-medium">
                      <i class='bx bx-edit'></i> Edit
                    </button>
                    @endhasPermission
                    <button x-show="recon.status === 'draft'" @click="completeReconciliation(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 text-xs font-medium">
                      <i class='bx bx-check'></i> Selesai
                    </button>
                    <button x-show="recon.status === 'completed'" @click="approveReconciliation(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-medium">
                      <i class='bx bx-badge-check'></i> Setujui
                    </button>
                    <button @click="exportPdf(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium">
                      <i class='bx bxs-file-pdf'></i> PDF
                    </button>
                    @hasPermission('finance.rekonsiliasi.delete')
                    <button x-show="recon.status !== 'approved'" @click="deleteReconciliation(recon.id)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium">
                      <i class='bx bx-trash'></i>
                    </button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && reconciliations.length === 0" class="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-file text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Tidak ada data rekonsiliasi</h3>
      <p class="text-slate-600 mb-4">Belum ada rekonsiliasi bank yang tercatat.</p>
      <button @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-white hover:bg-primary-700">
        <i class='bx bx-plus'></i> Buat Rekonsiliasi Pertama
      </button>
    </div>

    {{-- Reconciliation Wizard Modal (MYOB Style) --}}
    <div x-show="showReconcileModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showReconcileModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closeReconcileModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showReconcileModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-7xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle">
          
          {{-- Modal Header --}}
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-blue-50 to-blue-100">
            <div>
              <h3 class="text-lg font-semibold text-slate-800">Rekonsiliasi Bank</h3>
              <p class="text-sm text-slate-600 mt-1">Centang transaksi yang cocok antara bank statement dan buku perusahaan</p>
            </div>
            <button @click="closeReconcileModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Step Indicator --}}
          <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
              <div class="flex items-center" :class="reconcileStep >= 1 ? 'text-blue-600' : 'text-slate-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="reconcileStep >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600'">1</div>
                <span class="ml-2 font-medium">Setup</span>
              </div>
              <div class="flex-1 h-1 mx-4" :class="reconcileStep >= 2 ? 'bg-blue-600' : 'bg-slate-300'"></div>
              <div class="flex items-center" :class="reconcileStep >= 2 ? 'text-blue-600' : 'text-slate-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="reconcileStep >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600'">2</div>
                <span class="ml-2 font-medium">Matching</span>
              </div>
              <div class="flex-1 h-1 mx-4" :class="reconcileStep >= 3 ? 'bg-blue-600' : 'bg-slate-300'"></div>
              <div class="flex items-center" :class="reconcileStep >= 3 ? 'text-blue-600' : 'text-slate-400'">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="reconcileStep >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-600'">3</div>
                <span class="ml-2 font-medium">Review</span>
              </div>
            </div>
          </div>

          {{-- Modal Body --}}
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            
            {{-- Step 1: Setup --}}
            <div x-show="reconcileStep === 1">
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Outlet <span class="text-red-500">*</span></label>
                    <select x-model="reconcileData.outlet_id" @change="loadBankAccountsForReconcile()" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="">Pilih Outlet</option>
                      <template x-for="outlet in outlets" :key="outlet.id_outlet">
                        <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                      </template>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Rekening Bank <span class="text-red-500">*</span></label>
                    <select x-model="reconcileData.bank_account_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="">Pilih Rekening</option>
                      <template x-for="account in filteredBankAccounts" :key="account.id">
                        <option :value="account.id" x-text="account.full_info"></option>
                      </template>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Periode <span class="text-red-500">*</span></label>
                    <input type="month" x-model="reconcileData.period_month" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Akhir Bank Statement <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" x-model="reconcileData.bank_statement_balance" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="0">
                  </div>
                </div>

                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                  <div class="flex items-start gap-3">
                    <i class='bx bx-info-circle text-2xl text-blue-600'></i>
                    <div class="text-sm text-blue-800">
                      <p class="font-medium mb-1">Tips Rekonsiliasi:</p>
                      <ul class="list-disc list-inside space-y-1">
                        <li>Pastikan periode yang dipilih sesuai dengan rekening koran</li>
                        <li>Masukkan saldo akhir sesuai yang tertera di rekening koran</li>
                        <li>Sistem akan menampilkan transaksi yang perlu dicocokkan</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Step 2: Matching Transactions (MYOB Style) --}}
            <div x-show="reconcileStep === 2">
              <div class="space-y-4">
                {{-- Summary Bar --}}
                <div class="grid grid-cols-3 gap-4">
                  <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="text-sm text-slate-600">Saldo Bank Statement</div>
                    <div class="text-xl font-bold text-blue-600" x-text="formatCurrency(reconcileData.bank_statement_balance)"></div>
                  </div>
                  <div class="p-4 rounded-xl bg-green-50 border border-green-200">
                    <div class="text-sm text-slate-600">Total Tercentang</div>
                    <div class="text-xl font-bold text-green-600" x-text="formatCurrency(calculateCheckedTotal())"></div>
                  </div>
                  <div class="p-4 rounded-xl border-2" :class="calculateDifferenceReconcile() === 0 ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500'">
                    <div class="text-sm text-slate-600">Selisih</div>
                    <div class="text-xl font-bold" :class="calculateDifferenceReconcile() === 0 ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(calculateDifferenceReconcile())"></div>
                  </div>
                </div>

                {{-- Transactions List --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                  <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                      <h4 class="font-semibold text-slate-800">Transaksi Buku (Centang yang Cocok)</h4>
                      <div class="flex items-center gap-2">
                        <button @click="checkAllTransactions()" class="text-sm text-blue-600 hover:text-blue-800">
                          <i class='bx bx-check-square'></i> Centang Semua
                        </button>
                        <button @click="uncheckAllTransactions()" class="text-sm text-slate-600 hover:text-slate-800">
                          <i class='bx bx-square'></i> Hapus Semua
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="max-h-96 overflow-y-auto">
                    <table class="w-full text-sm">
                      <thead class="bg-slate-100 sticky top-0">
                        <tr>
                          <th class="px-4 py-2 text-center w-12">
                            <input type="checkbox" @change="toggleAllTransactions($event)" class="rounded">
                          </th>
                          <th class="px-4 py-2 text-left">Tanggal</th>
                          <th class="px-4 py-2 text-left">No. Transaksi</th>
                          <th class="px-4 py-2 text-left">Keterangan</th>
                          <th class="px-4 py-2 text-right">Debit</th>
                          <th class="px-4 py-2 text-right">Kredit</th>
                          <th class="px-4 py-2 text-right">Saldo</th>
                        </tr>
                      </thead>
                      <tbody>
                        <template x-for="(trx, index) in bookTransactions" :key="index">
                          <tr class="border-b border-slate-100 hover:bg-slate-50" :class="trx.checked ? 'bg-green-50' : ''">
                            <td class="px-4 py-2 text-center">
                              <input type="checkbox" x-model="trx.checked" @change="updateRunningBalance()" class="rounded">
                            </td>
                            <td class="px-4 py-2" x-text="formatDate(trx.transaction_date)"></td>
                            <td class="px-4 py-2 font-mono text-xs" x-text="trx.transaction_number"></td>
                            <td class="px-4 py-2" x-text="trx.description"></td>
                            <td class="px-4 py-2 text-right" x-text="trx.type === 'debit' ? formatCurrency(trx.amount) : '-'"></td>
                            <td class="px-4 py-2 text-right" x-text="trx.type === 'credit' ? formatCurrency(trx.amount) : '-'"></td>
                            <td class="px-4 py-2 text-right font-medium" x-text="formatCurrency(trx.running_balance)"></td>
                          </tr>
                        </template>
                        <tr x-show="bookTransactions.length === 0">
                          <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            <i class='bx bx-info-circle text-3xl mb-2'></i>
                            <p>Tidak ada transaksi untuk periode ini</p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                  <div class="flex items-start gap-3">
                    <i class='bx bx-bulb text-2xl text-amber-600'></i>
                    <div class="text-sm text-amber-800">
                      <p class="font-medium mb-1">Cara Matching:</p>
                      <ul class="list-disc list-inside space-y-1">
                        <li>Centang transaksi yang sudah muncul di rekening koran</li>
                        <li>Transaksi yang dicentang akan berwarna hijau</li>
                        <li>Pastikan selisih menjadi Rp 0 sebelum melanjutkan</li>
                        <li>Jika ada selisih, cek transaksi yang belum tercatat</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Step 3: Review --}}
            <div x-show="reconcileStep === 3">
              <div class="space-y-4">
                <div class="p-6 rounded-xl bg-gradient-to-br from-blue-50 to-green-50 border-2 border-blue-200">
                  <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" :class="calculateDifferenceReconcile() === 0 ? 'bg-green-500' : 'bg-amber-500'">
                      <i class='bx text-3xl text-white' :class="calculateDifferenceReconcile() === 0 ? 'bx-check' : 'bx-error'"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2" x-text="calculateDifferenceReconcile() === 0 ? 'Rekonsiliasi Seimbang!' : 'Masih Ada Selisih'"></h3>
                    <p class="text-slate-600" x-text="calculateDifferenceReconcile() === 0 ? 'Semua transaksi sudah cocok' : 'Periksa kembali transaksi yang belum dicentang'"></p>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-lg">
                      <div class="text-sm text-slate-600 mb-1">Saldo Bank Statement</div>
                      <div class="text-2xl font-bold text-slate-800" x-text="formatCurrency(reconcileData.bank_statement_balance)"></div>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                      <div class="text-sm text-slate-600 mb-1">Total Tercentang</div>
                      <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(calculateCheckedTotal())"></div>
                    </div>
                  </div>

                  <div class="mt-4 p-4 rounded-lg" :class="calculateDifferenceReconcile() === 0 ? 'bg-green-100' : 'bg-red-100'">
                    <div class="flex items-center justify-between">
                      <span class="font-semibold" :class="calculateDifferenceReconcile() === 0 ? 'text-green-800' : 'text-red-800'">Selisih:</span>
                      <span class="text-2xl font-bold" :class="calculateDifferenceReconcile() === 0 ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(calculateDifferenceReconcile())"></span>
                    </div>
                  </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                  <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
                    <h4 class="font-semibold text-slate-800">Ringkasan Transaksi</h4>
                  </div>
                  <div class="p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                      <div>
                        <div class="text-2xl font-bold text-blue-600" x-text="bookTransactions.length"></div>
                        <div class="text-sm text-slate-600">Total Transaksi</div>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-green-600" x-text="bookTransactions.filter(t => t.checked).length"></div>
                        <div class="text-sm text-slate-600">Tercentang</div>
                      </div>
                      <div>
                        <div class="text-2xl font-bold text-amber-600" x-text="bookTransactions.filter(t => !t.checked).length"></div>
                        <div class="text-sm text-slate-600">Belum Dicentang</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                  <textarea x-model="reconcileData.notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
              </div>
            </div>

          </div>

          {{-- Modal Footer --}}
          <div class="flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-slate-50">
            <button x-show="reconcileStep > 1" @click="reconcileStep--" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              <i class='bx bx-chevron-left'></i> Kembali
            </button>
            <div x-show="reconcileStep === 1"></div>
            
            <div class="flex gap-2">
              <button @click="closeReconcileModal()" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                Batal
              </button>
              <button x-show="reconcileStep < 3" @click="nextReconcileStep()" type="button" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                Lanjut <i class='bx bx-chevron-right'></i>
              </button>
              <button x-show="reconcileStep === 3" @click="saveReconciliation()" :disabled="isSaving" type="button" class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 disabled:opacity-50">
                <span x-show="!isSaving"><i class='bx bx-check'></i> Simpan Rekonsiliasi</span>
                <span x-show="isSaving">Menyimpan...</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Create/Edit Modal (Simple) --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle">
          
          {{-- Modal Header --}}
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800" x-text="modalMode === 'create' ? 'Buat Rekonsiliasi Bank' : 'Edit Rekonsiliasi Bank'"></h3>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Modal Body --}}
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            <form @submit.prevent="saveReconciliation()">
              <div class="space-y-4">
                {{-- Basic Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Outlet <span class="text-red-500">*</span></label>
                    <select x-model="formData.outlet_id" @change="loadBankAccountsByOutlet()" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="">Pilih Outlet</option>
                      <template x-for="outlet in outlets" :key="outlet.id_outlet">
                        <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                      </template>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Rekening Bank <span class="text-red-500">*</span></label>
                    <select x-model="formData.bank_account_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                      <option value="">Pilih Rekening</option>
                      <template x-for="account in filteredBankAccounts" :key="account.id">
                        <option :value="account.id" x-text="account.full_info"></option>
                      </template>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Rekonsiliasi <span class="text-red-500">*</span></label>
                    <input type="date" x-model="formData.reconciliation_date" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Periode <span class="text-red-500">*</span></label>
                    <input type="month" x-model="formData.period_month" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Bank Statement <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" x-model="formData.bank_statement_balance" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Buku <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" x-model="formData.book_balance" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                  <textarea x-model="formData.notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                </div>

                {{-- Selisih Display --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-700">Selisih:</span>
                    <span class="text-lg font-bold" :class="calculateDifference() !== 0 ? 'text-red-600' : 'text-green-600'" x-text="formatCurrency(calculateDifference())"></span>
                  </div>
                </div>
              </div>
            </form>
          </div>

          {{-- Modal Footer --}}
          <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200 bg-slate-50">
            <button @click="closeModal()" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
              Batal
            </button>
            <button @click="saveReconciliation()" :disabled="isSaving" type="button" class="px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
              <span x-show="!isSaving">Simpan</span>
              <span x-show="isSaving">Menyimpan...</span>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function bankReconciliationManagement() {
      return {
        routes: {
          outletsData: '{{ route("finance.outlets.data") }}',
          reconciliationData: '{{ route("finance.rekonsiliasi.data") }}',
          reconciliationStore: '{{ route("finance.rekonsiliasi.store") }}',
          reconciliationUpdate: '{{ route("finance.rekonsiliasi.update", ":id") }}',
          reconciliationShow: '{{ route("finance.rekonsiliasi.show", ":id") }}',
          reconciliationComplete: '{{ route("finance.rekonsiliasi.complete", ":id") }}',
          reconciliationApprove: '{{ route("finance.rekonsiliasi.approve", ":id") }}',
          reconciliationDelete: '{{ route("finance.rekonsiliasi.destroy", ":id") }}',
          reconciliationExportPdf: '{{ route("finance.rekonsiliasi.export-pdf", ":id") }}',
          bankAccountsData: '{{ route("finance.rekonsiliasi.bank-accounts") }}',
          statisticsData: '{{ route("finance.rekonsiliasi.statistics") }}',
          unreconciledTransactions: '{{ route("finance.rekonsiliasi.unreconciled-transactions") }}'
        },
        filters: {
          outlet_id: '',
          status: 'all',
          period_month: '',
          bank_account_id: ''
        },
        outlets: [],
        bankAccounts: [],
        filteredBankAccounts: [],
        reconciliations: [],
        statistics: {
          total_reconciliations: 0,
          draft: 0,
          completed: 0,
          approved: 0,
          total_difference: 0
        },
        isLoading: false,
        isSaving: false,
        showModal: false,
        showReconcileModal: false,
        modalMode: 'create',
        editingId: null,
        reconcileStep: 1,
        bookTransactions: [],
        formData: {
          outlet_id: '',
          bank_account_id: '',
          reconciliation_date: new Date().toISOString().split('T')[0],
          period_month: new Date().toISOString().slice(0, 7),
          bank_statement_balance: 0,
          book_balance: 0,
          notes: ''
        },
        reconcileData: {
          outlet_id: '',
          bank_account_id: '',
          period_month: new Date().toISOString().slice(0, 7),
          bank_statement_balance: 0,
          notes: ''
        },

        async init() {
          await this.loadOutlets();
          await this.loadBankAccounts();
          await this.loadStatistics();
          await this.loadData();
          
          // Initialize filtered bank accounts
          if (this.outlets.length > 0 && this.formData.outlet_id) {
            this.loadBankAccountsByOutlet();
          } else {
            this.filteredBankAccounts = this.bankAccounts;
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch(this.routes.outletsData);
            const data = await response.json();
            if (data.success) {
              this.outlets = data.data;
              if (this.outlets.length > 0 && !this.filters.outlet_id) {
                this.filters.outlet_id = this.outlets[0].id_outlet;
                this.formData.outlet_id = this.outlets[0].id_outlet;
              }
            }
          } catch (error) {
            console.error('Error loading outlets:', error);
            this.showNotification('error', 'Gagal memuat data outlet');
          }
        },

        async loadBankAccounts() {
          try {
            const response = await fetch(this.routes.bankAccountsData);
            const data = await response.json();
            if (data.success) {
              this.bankAccounts = data.data;
            }
          } catch (error) {
            console.error('Error loading bank accounts:', error);
            this.showNotification('error', 'Gagal memuat data bank');
          }
        },

        loadBankAccountsByOutlet() {
          if (this.formData.outlet_id) {
            this.filteredBankAccounts = this.bankAccounts.filter(
              account => account.outlet_id == this.formData.outlet_id
            );
          } else {
            this.filteredBankAccounts = this.bankAccounts;
          }
          this.formData.bank_account_id = '';
        },

        async loadStatistics() {
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id || ''
            });

            const response = await fetch(`${this.routes.statisticsData}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.statistics = data.data;
            }
          } catch (error) {
            console.error('Error loading statistics:', error);
          }
        },

        async loadData() {
          this.isLoading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet_id || '',
              status: this.filters.status,
              period_month: this.filters.period_month || '',
              bank_account_id: this.filters.bank_account_id || ''
            });

            const response = await fetch(`${this.routes.reconciliationData}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.reconciliations = data.data;
            } else {
              this.showNotification('error', data.message || 'Gagal memuat data');
            }
          } catch (error) {
            console.error('Error loading data:', error);
            this.showNotification('error', 'Gagal memuat data rekonsiliasi');
          } finally {
            this.isLoading = false;
          }
        },

        openCreateModal() {
          // Open MYOB-style reconciliation wizard
          this.reconcileStep = 1;
          this.resetReconcileData();
          this.showReconcileModal = true;
        },

        openSimpleModal() {
          // Open simple create modal (old style)
          this.modalMode = 'create';
          this.editingId = null;
          this.resetForm();
          this.loadBankAccountsByOutlet();
          this.showModal = true;
        },

        async editReconciliation(id) {
          this.modalMode = 'edit';
          this.editingId = id;
          
          try {
            const response = await fetch(this.routes.reconciliationShow.replace(':id', id));
            const data = await response.json();
            
            if (data.success) {
              this.formData = {
                outlet_id: data.data.outlet_id,
                bank_account_id: data.data.bank_account_id,
                reconciliation_date: data.data.reconciliation_date,
                period_month: data.data.period_month,
                bank_statement_balance: data.data.bank_statement_balance,
                book_balance: data.data.book_balance,
                notes: data.data.notes || ''
              };
              this.loadBankAccountsByOutlet();
              this.showModal = true;
            }
          } catch (error) {
            console.error('Error loading reconciliation:', error);
            this.showNotification('error', 'Gagal memuat data rekonsiliasi');
          }
        },

        async saveReconciliation() {
          this.isSaving = true;
          try {
            const url = this.modalMode === 'create' 
              ? this.routes.reconciliationStore 
              : this.routes.reconciliationUpdate.replace(':id', this.editingId);
            
            const method = this.modalMode === 'create' ? 'POST' : 'PUT';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.formData)
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', data.message);
              this.closeModal();
              await this.loadData();
              await this.loadStatistics();
            } else {
              this.showNotification('error', data.message || 'Gagal menyimpan data');
            }
          } catch (error) {
            console.error('Error saving reconciliation:', error);
            this.showNotification('error', 'Gagal menyimpan rekonsiliasi');
          } finally {
            this.isSaving = false;
          }
        },

        async completeReconciliation(id) {
          if (!confirm('Yakin ingin menyelesaikan rekonsiliasi ini?')) return;

          try {
            const response = await fetch(this.routes.reconciliationComplete.replace(':id', id), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', data.message);
              await this.loadData();
              await this.loadStatistics();
            } else {
              this.showNotification('error', data.message);
            }
          } catch (error) {
            console.error('Error completing reconciliation:', error);
            this.showNotification('error', 'Gagal menyelesaikan rekonsiliasi');
          }
        },

        async approveReconciliation(id) {
          if (!confirm('Yakin ingin menyetujui rekonsiliasi ini?')) return;

          try {
            const response = await fetch(this.routes.reconciliationApprove.replace(':id', id), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', data.message);
              await this.loadData();
              await this.loadStatistics();
            } else {
              this.showNotification('error', data.message);
            }
          } catch (error) {
            console.error('Error approving reconciliation:', error);
            this.showNotification('error', 'Gagal menyetujui rekonsiliasi');
          }
        },

        async deleteReconciliation(id) {
          if (!confirm('Yakin ingin menghapus rekonsiliasi ini?')) return;

          try {
            const response = await fetch(this.routes.reconciliationDelete.replace(':id', id), {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', data.message);
              await this.loadData();
              await this.loadStatistics();
            } else {
              this.showNotification('error', data.message);
            }
          } catch (error) {
            console.error('Error deleting reconciliation:', error);
            this.showNotification('error', 'Gagal menghapus rekonsiliasi');
          }
        },

        exportPdf(id) {
          // Open PDF in new tab for streaming
          const pdfUrl = this.routes.reconciliationExportPdf.replace(':id', id);
          window.open(pdfUrl, '_blank');
        },

        async viewDetail(id) {
          try {
            const response = await fetch(this.routes.reconciliationShow.replace(':id', id));
            const data = await response.json();
            
            if (data.success) {
              // Show detail in modal
              this.showDetailModal(data.data);
            } else {
              this.showNotification('error', data.message || 'Gagal memuat detail');
            }
          } catch (error) {
            console.error('Error loading detail:', error);
            this.showNotification('error', 'Gagal memuat detail rekonsiliasi');
          }
        },

        showDetailModal(recon) {
          // Create detail modal content
          const modal = `
            <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" style="display: none;" x-cloak>
              <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-500 bg-opacity-75" @click="show = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle">
                  <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800">Detail Rekonsiliasi Bank</h3>
                    <button @click="show = false; $el.closest('.fixed').remove()" class="text-slate-400 hover:text-slate-600">
                      <i class='bx bx-x text-2xl'></i>
                    </button>
                  </div>
                  <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                      <div>
                        <label class="text-sm text-slate-600">Outlet</label>
                        <div class="font-medium">${recon.outlet_name}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Akun Bank</label>
                        <div class="font-medium">${recon.bank_name}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Periode</label>
                        <div class="font-medium">${recon.period_month}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Tanggal Rekonsiliasi</label>
                        <div class="font-medium">${this.formatDate(recon.reconciliation_date)}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Saldo Bank Statement</label>
                        <div class="font-medium text-blue-600">${this.formatCurrency(recon.bank_statement_balance)}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Saldo Buku</label>
                        <div class="font-medium text-green-600">${this.formatCurrency(recon.book_balance)}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Selisih</label>
                        <div class="font-medium ${Math.abs(recon.difference) > 0 ? 'text-red-600' : 'text-green-600'}">${this.formatCurrency(recon.difference)}</div>
                      </div>
                      <div>
                        <label class="text-sm text-slate-600">Status</label>
                        <div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            recon.status === 'draft' ? 'bg-orange-100 text-orange-800' :
                            recon.status === 'completed' ? 'bg-green-100 text-green-800' :
                            'bg-blue-100 text-blue-800'
                          }">
                            ${recon.status === 'draft' ? 'Draft' : recon.status === 'completed' ? 'Selesai' : 'Disetujui'}
                          </span>
                        </div>
                      </div>
                    </div>
                    ${recon.items && recon.items.length > 0 ? `
                      <div class="border-t border-slate-200 pt-4">
                        <h4 class="font-semibold mb-3">Detail Transaksi (${recon.items.length})</h4>
                        <div class="overflow-x-auto">
                          <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                              <tr>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-left">Keterangan</th>
                                <th class="px-3 py-2 text-right">Debit</th>
                                <th class="px-3 py-2 text-right">Kredit</th>
                                <th class="px-3 py-2 text-center">Status</th>
                              </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                              ${recon.items.map(item => `
                                <tr>
                                  <td class="px-3 py-2">${this.formatDate(item.transaction_date)}</td>
                                  <td class="px-3 py-2">${item.description}</td>
                                  <td class="px-3 py-2 text-right">${item.type === 'debit' ? this.formatCurrency(item.amount) : '-'}</td>
                                  <td class="px-3 py-2 text-right">${item.type === 'credit' ? this.formatCurrency(item.amount) : '-'}</td>
                                  <td class="px-3 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs ${
                                      item.status === 'reconciled' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'
                                    }">
                                      ${item.status === 'reconciled' ? 'Sesuai' : 'Belum Sesuai'}
                                    </span>
                                  </td>
                                </tr>
                              `).join('')}
                            </tbody>
                          </table>
                        </div>
                      </div>
                    ` : ''}
                    ${recon.notes ? `
                      <div class="border-t border-slate-200 pt-4 mt-4">
                        <label class="text-sm text-slate-600">Catatan</label>
                        <div class="mt-1 text-slate-800">${recon.notes}</div>
                      </div>
                    ` : ''}
                  </div>
                  <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <button @click="show = false; $el.closest('.fixed').remove()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                      Tutup
                    </button>
                  </div>
                </div>
              </div>
            </div>
          `;
          
          // Append modal to body
          const div = document.createElement('div');
          div.innerHTML = modal;
          document.body.appendChild(div.firstElementChild);
          
          // Initialize Alpine
          Alpine.initTree(div.firstElementChild);
        },

        calculateDifference() {
          return parseFloat(this.formData.bank_statement_balance || 0) - parseFloat(this.formData.book_balance || 0);
        },

        closeModal() {
          this.showModal = false;
          this.resetForm();
        },

        resetForm() {
          this.formData = {
            outlet_id: this.outlets.length > 0 ? this.outlets[0].id_outlet : '',
            bank_account_id: '',
            reconciliation_date: new Date().toISOString().split('T')[0],
            period_month: new Date().toISOString().slice(0, 7),
            bank_statement_balance: 0,
            book_balance: 0,
            notes: ''
          };
        },

        async refreshData() {
          await this.loadData();
          await this.loadStatistics();
          this.showNotification('success', 'Data berhasil dimuat ulang');
        },

        // ===== MYOB-Style Reconciliation Functions =====
        
        async nextReconcileStep() {
          if (this.reconcileStep === 1) {
            // Validate step 1
            if (!this.reconcileData.outlet_id || !this.reconcileData.bank_account_id || 
                !this.reconcileData.period_month || !this.reconcileData.bank_statement_balance) {
              this.showNotification('error', 'Mohon lengkapi semua field yang wajib diisi');
              return;
            }
            
            // Load transactions for matching
            await this.loadBookTransactions();
            this.reconcileStep = 2;
          } else if (this.reconcileStep === 2) {
            this.reconcileStep = 3;
          }
        },

        async loadBookTransactions() {
          try {
            const [year, month] = this.reconcileData.period_month.split('-');
            const startDate = `${year}-${month}-01`;
            const lastDay = new Date(year, month, 0).getDate();
            const endDate = `${year}-${month}-${lastDay}`;

            const params = new URLSearchParams({
              outlet_id: this.reconcileData.outlet_id,
              bank_account_id: this.reconcileData.bank_account_id,
              start_date: startDate,
              end_date: endDate
            });

            const response = await fetch(`${this.routes.unreconciledTransactions}?${params}`);
            const data = await response.json();
            
            if (data.success) {
              // Add checked property and running balance
              let runningBalance = 0;
              this.bookTransactions = data.data.map(trx => {
                if (trx.type === 'debit') {
                  runningBalance += parseFloat(trx.amount);
                } else {
                  runningBalance -= parseFloat(trx.amount);
                }
                return {
                  ...trx,
                  checked: false,
                  running_balance: runningBalance
                };
              });
            } else {
              this.showNotification('error', data.message || 'Gagal memuat transaksi');
            }
          } catch (error) {
            console.error('Error loading transactions:', error);
            this.showNotification('error', 'Gagal memuat transaksi');
          }
        },

        loadBankAccountsForReconcile() {
          if (this.reconcileData.outlet_id) {
            this.filteredBankAccounts = this.bankAccounts.filter(
              account => account.outlet_id == this.reconcileData.outlet_id
            );
          } else {
            this.filteredBankAccounts = this.bankAccounts;
          }
          this.reconcileData.bank_account_id = '';
        },

        calculateCheckedTotal() {
          let total = 0;
          this.bookTransactions.forEach(trx => {
            if (trx.checked) {
              if (trx.type === 'debit') {
                total += parseFloat(trx.amount);
              } else {
                total -= parseFloat(trx.amount);
              }
            }
          });
          return total;
        },

        calculateDifferenceReconcile() {
          const bankBalance = parseFloat(this.reconcileData.bank_statement_balance || 0);
          const checkedTotal = this.calculateCheckedTotal();
          return bankBalance - checkedTotal;
        },

        updateRunningBalance() {
          // Recalculate running balance based on checked items
          let runningBalance = 0;
          this.bookTransactions.forEach(trx => {
            if (trx.checked) {
              if (trx.type === 'debit') {
                runningBalance += parseFloat(trx.amount);
              } else {
                runningBalance -= parseFloat(trx.amount);
              }
            }
            trx.running_balance = runningBalance;
          });
        },

        checkAllTransactions() {
          this.bookTransactions.forEach(trx => {
            trx.checked = true;
          });
          this.updateRunningBalance();
        },

        uncheckAllTransactions() {
          this.bookTransactions.forEach(trx => {
            trx.checked = false;
          });
          this.updateRunningBalance();
        },

        toggleAllTransactions(event) {
          const checked = event.target.checked;
          this.bookTransactions.forEach(trx => {
            trx.checked = checked;
          });
          this.updateRunningBalance();
        },

        async saveReconciliation() {
          this.isSaving = true;
          try {
            // Prepare data
            const checkedTransactions = this.bookTransactions.filter(trx => trx.checked);
            const bookBalance = this.calculateCheckedTotal();
            
            const payload = {
              outlet_id: this.reconcileData.outlet_id,
              bank_account_id: this.reconcileData.bank_account_id,
              reconciliation_date: new Date().toISOString().split('T')[0],
              period_month: this.reconcileData.period_month,
              bank_statement_balance: this.reconcileData.bank_statement_balance,
              book_balance: bookBalance,
              notes: this.reconcileData.notes,
              items: checkedTransactions.map(trx => ({
                journal_entry_id: trx.journal_entry_id,
                transaction_date: trx.transaction_date,
                transaction_number: trx.transaction_number,
                description: trx.description,
                amount: trx.amount,
                type: trx.type,
                status: 'reconciled',
                category: 'other'
              }))
            };

            const response = await fetch(this.routes.reconciliationStore, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
              this.showNotification('success', 'Rekonsiliasi berhasil disimpan');
              this.closeReconcileModal();
              await this.loadData();
              await this.loadStatistics();
            } else {
              this.showNotification('error', data.message || 'Gagal menyimpan rekonsiliasi');
            }
          } catch (error) {
            console.error('Error saving reconciliation:', error);
            this.showNotification('error', 'Gagal menyimpan rekonsiliasi');
          } finally {
            this.isSaving = false;
          }
        },

        closeReconcileModal() {
          this.showReconcileModal = false;
          this.reconcileStep = 1;
          this.resetReconcileData();
        },

        resetReconcileData() {
          this.reconcileData = {
            outlet_id: this.outlets.length > 0 ? this.outlets[0].id_outlet : '',
            bank_account_id: '',
            period_month: new Date().toISOString().slice(0, 7),
            bank_statement_balance: 0,
            notes: ''
          };
          this.bookTransactions = [];
        },

        formatCurrency(value) {
          if (!value && value !== 0) return 'Rp 0';
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
          return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          }).format(date);
        },

        showNotification(type, message) {
          const event = new CustomEvent('notify', {
            detail: { type, message }
          });
          window.dispatchEvent(event);
        }
      };
    }
  </script>

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
</x-layouts.admin>
