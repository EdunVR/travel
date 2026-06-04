
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Saldo Awal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Saldo Awal')]); ?>
  <div x-data="openingBalanceManagement()" x-init="init()" class="space-y-6">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Saldo Awal</h1>
        <p class="text-slate-600 text-sm">Kelola saldo awal akun untuk periode berjalan</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.saldo-awal.create')): ?>
        <button @click="openCreateBalance()" 
                :disabled="!filters.outlet || !filters.book"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed">
          <i class='bx bx-plus'></i> Tambah Saldo
        </button>
        <?php endif; ?>
        <button @click="postBalances()" 
                :disabled="!filters.outlet || !filters.book"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 h-10 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed">
          <i class='bx bx-check-circle'></i> Posting ke Jurnal
        </button>
        <button @click="validateBalance()" 
                class="inline-flex items-center gap-2 rounded-xl bg-orange-600 text-white px-4 h-10 hover:bg-orange-700">
          <i class='bx bx-check-double'></i> Validasi
        </button>
      </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
                <select x-model="filters.outlet" @change="onOutletChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">Pilih Outlet</option>
                    <template x-for="outlet in outlets" :key="outlet.id_outlet">
                        <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                    </template>
                </select>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Buku Akuntansi</label>
                <select x-model="filters.book" @change="loadOpeningBalance()" 
                        :disabled="!filters.outlet || accountingBooks.length === 0"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm disabled:bg-slate-50 disabled:cursor-not-allowed">
                    <option value="">Pilih Buku</option>
                    <template x-for="book in accountingBooks" :key="book.id">
                        <option :value="book.id" x-text="book.code + ' - ' + book.name"></option>
                    </template>
                </select>
                <div x-show="!filters.outlet" class="text-xs text-orange-600 mt-1">
                    Pilih outlet terlebih dahulu
                </div>
                <div x-show="filters.outlet && accountingBooks.length === 0" class="text-xs text-slate-500 mt-1">
                    Tidak ada buku akuntansi aktif untuk outlet ini
                </div>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Akun</label>
                <select x-model="filters.type" @change="loadOpeningBalance()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="all">Semua Tipe</option>
                    <option value="asset">Aset</option>
                    <option value="liability">Kewajiban</option>
                    <option value="equity">Ekuitas</option>
                    <option value="revenue">Pendapatan</option>
                    <option value="expense">Beban</option>
                </select>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status Saldo</label>
                <select x-model="filters.status" @change="loadOpeningBalance()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="posted">Diposting</option>
                    <option value="draft">Draft</option>
                    <option value="balanced">Seimbang</option>
                    <option value="unbalanced">Tidak Seimbang</option>
                </select>
            </div>
        </div>

        
        <div class="mt-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pencarian</label>
            <div class="flex gap-2">
                <input type="text" x-model="filters.search" @input.debounce.500ms="loadOpeningBalance()" 
                      placeholder="Cari kode atau nama akun..." 
                      class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button @click="loadOpeningBalance()" class="rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700">
                    <i class='bx bx-search'></i>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="isLoading" class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
      <div class="flex justify-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      <p class="text-slate-600 mt-2">Memuat data saldo awal...</p>
    </div>

    
    <div x-show="!isLoading && openingBalanceData.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-800">Ringkasan Saldo Awal</h3>
          <span class="text-sm text-slate-500" x-text="`${balanceStats.accounts_with_balance} dari ${balanceStats.total_accounts} akun`"></span>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
          <div class="text-center p-4 rounded-lg bg-green-50">
            <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(balanceStats.total_debit)"></div>
            <div class="text-sm text-green-800">Total Debit</div>
          </div>
          <div class="text-center p-4 rounded-lg bg-red-50">
            <div class="text-2xl font-bold text-red-600" x-text="formatCurrency(balanceStats.total_credit)"></div>
            <div class="text-sm text-red-800">Total Kredit</div>
          </div>
        </div>
        <div class="text-center p-4 rounded-lg" :class="balanceStats.balance === 0 ? 'bg-blue-50' : 'bg-orange-50'">
          <div class="text-lg font-bold" :class="balanceStats.balance === 0 ? 'text-blue-600' : 'text-orange-600'" 
                x-text="balanceStats.balance === 0 ? 'BALANCE' : 'UNBALANCE'"></div>
          <div class="text-sm" :class="balanceStats.balance === 0 ? 'text-blue-800' : 'text-orange-800'"
                x-text="balanceStats.balance === 0 ? 'Saldo seimbang' : 'Selisih: ' + formatCurrency(Math.abs(balanceStats.balance))"></div>
        </div>
      </div>

      
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
          <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-slate-800">Saldo per Tipe Akun</h3>
              <span class="text-sm text-slate-500" x-text="balanceStats.period + ' Periode'"></span>
          </div>
          <div class="h-64">
              <canvas id="balanceTypeChart" x-ref="balanceTypeChart" 
                      x-show="openingBalanceData.length > 0"></canvas>
              <div x-show="openingBalanceData.length === 0" class="h-full flex items-center justify-center text-slate-500">
                  <div class="text-center">
                      <i class='bx bx-bar-chart-alt-2 text-4xl mb-2'></i>
                      <p>Data tidak tersedia untuk chart</p>
                      <p class="text-sm">Pilih outlet dan buku akuntansi untuk melihat chart</p>
                  </div>
              </div>
          </div>
      </div>
    </div>

    
    <div x-show="!isLoading && openingBalanceData.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-wallet text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="balanceStats.total_accounts"></div>
            <div class="text-sm text-slate-600">Total Akun</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          <span x-text="balanceStats.accounts_with_balance"></span> memiliki saldo
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="balanceStats.balanced_accounts"></div>
            <div class="text-sm text-slate-600">Akun Seimbang</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Debit = Kredit
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
            <i class='bx bx-error-circle text-2xl text-orange-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="balanceStats.unbalanced_accounts"></div>
            <div class="text-sm text-slate-600">Akun Tidak Seimbang</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Perlu penyesuaian
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
            <i class='bx bx-calendar text-2xl text-purple-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="balanceStats.period"></div>
            <div class="text-sm text-slate-600">Periode</div>
          </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
          Tahun berjalan
        </div>
      </div>
    </div>

    
    <div x-show="!isLoading && openingBalanceData.length === 0" class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <i class='bx bx-wallet text-2xl text-slate-400'></i>
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum ada saldo awal</h3>
      <p class="text-slate-600 mb-6">Mulai dengan menambahkan saldo awal untuk akun-akun Anda.</p>
      <button @click="openCreateBalance()" 
              :disabled="!filters.outlet || !filters.book"
              class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-6 py-3 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed">
        <i class='bx bx-plus'></i> Tambah Saldo Awal Pertama
      </button>
    </div>

    
    <div x-show="!isLoading && openingBalanceData.length > 0" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Daftar Saldo Awal</h2>
        <div class="flex items-center gap-4">
          <div class="text-sm text-slate-600">
            Menampilkan <span x-text="openingBalanceData.length"></span> dari <span x-text="totalItems"></span> entri
          </div>
          <select x-model="perPage" @change="loadOpeningBalance()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="10">10 per halaman</option>
            <option value="25">25 per halaman</option>
            <option value="50">50 per halaman</option>
            <option value="100">100 per halaman</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left w-12">No</th>
              <th class="px-4 py-3 text-left">Kode Akun</th>
              <th class="px-4 py-3 text-left">Nama Akun</th>
              <th class="px-4 py-3 text-left">Tipe</th>
              <th class="px-4 py-3 text-right">Debit</th>
              <th class="px-4 py-3 text-right">Kredit</th>
              <th class="px-4 py-3 text-right">Saldo</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Tanggal Efektif</th>
              <th class="px-4 py-3 text-left w-24">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(item, index) in openingBalanceData" :key="item.id">
              <tr class="border-t border-slate-100 hover:bg-slate-50"
                  :style="item.account.level > 1 ? 'background-color: rgba(248, 250, 252, 0.5)' : ''">
                <td class="px-4 py-3" x-text="(currentPage - 1) * perPage + index + 1"></td>
                <td class="px-4 py-3">
                  <div class="font-mono text-sm flex items-center gap-1">
                    <!-- Indentasi untuk akun anak -->
                    <template x-if="item.account.level > 1">
                      <span class="text-slate-300" x-text="'└─'.repeat(item.account.level - 1)"></span>
                    </template>
                    <span x-text="item.account.code" 
                          :class="item.account.level > 1 ? 'text-slate-600' : 'font-semibold'"></span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1">
                    <!-- Indentasi visual untuk hierarki -->
                    <template x-if="item.account.level > 1">
                      <span class="text-slate-300 text-xs" x-text="'└─'.repeat(item.account.level - 1)"></span>
                    </template>
                    <div>
                      <div :class="item.account.level > 1 ? 'text-slate-600' : 'font-semibold text-slate-800'" 
                           x-text="item.account.name"></div>
                      <div class="text-xs text-slate-500" x-text="item.description"></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span :class="getTypeBadgeClass(item.account.type)" x-text="getTypeName(item.account.type)" 
                        class="px-2 py-1 rounded-full text-xs"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <template x-if="item.account.level > 1">
                      <span class="text-slate-300 text-xs mr-2">└─</span>
                    </template>
                    <div :class="[
                           'font-semibold text-right',
                           item.account.level > 1 ? 'text-sm' : 'text-base',
                           item.debit > 0 ? 'text-green-600' : 'text-slate-400'
                         ]" 
                         x-text="formatCurrency(item.debit)"></div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <template x-if="item.account.level > 1">
                      <span class="text-slate-300 text-xs mr-2">└─</span>
                    </template>
                    <div :class="[
                           'font-semibold text-right',
                           item.account.level > 1 ? 'text-sm' : 'text-base',
                           item.credit > 0 ? 'text-red-600' : 'text-slate-400'
                         ]" 
                         x-text="formatCurrency(item.credit)"></div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <template x-if="item.account.level > 1">
                      <span class="text-slate-300 text-xs mr-2">└─</span>
                    </template>
                    <div :class="[
                           'font-semibold text-right',
                           item.account.level > 1 ? 'text-sm' : 'text-base',
                           getBalanceClass(item.debit - item.credit)
                         ]" 
                         x-text="formatCurrency(item.debit - item.credit)"></div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span :class="getStatusBadgeClass(item.status)" 
                        class="px-2 py-1 rounded-full text-xs" 
                        x-text="getStatusName(item.status)"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm" x-text="formatDate(item.effective_date)"></div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <button x-show="item.status !== 'posted'" 
                            @click="editBalance(item)" 
                            class="text-blue-600 hover:text-blue-800" 
                            title="Edit">
                      <i class="bx bx-edit"></i>
                    </button>
                    <button x-show="item.status !== 'posted'" 
                            @click="deleteBalance(item.id)" 
                            class="text-red-600 hover:text-red-800" 
                            title="Hapus">
                      <i class="bx bx-trash"></i>
                    </button>
                    <button x-show="item.status === 'posted'" 
                            class="text-green-600" 
                            title="Sudah Diposting">
                      <i class="bx bx-check"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      
      <div x-show="lastPage > 1" class="flex items-center justify-between p-4 border-t border-slate-200">
        <div class="text-sm text-slate-600">
          Menampilkan halaman <span x-text="currentPage"></span> dari <span x-text="lastPage"></span>
        </div>
        <div class="flex gap-1">
          <button @click="changePage(1)" :disabled="currentPage === 1"
                  class="px-3 py-1 rounded border border-slate-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-chevrons-left'></i>
          </button>
          <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                  class="px-3 py-1 rounded border border-slate-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-chevron-left'></i>
          </button>
          
          <template x-for="page in getPaginationPages()" :key="page">
            <button @click="changePage(page)"
                    :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-slate-700'"
                    class="px-3 py-1 rounded border border-slate-200 text-sm">
              <span x-text="page"></span>
            </button>
          </template>

          <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
                  class="px-3 py-1 rounded border border-slate-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-chevron-right'></i>
          </button>
          <button @click="changePage(lastPage)" :disabled="currentPage === lastPage"
                  class="px-3 py-1 rounded border border-slate-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            <i class='bx bx-chevrons-right'></i>
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="showBalanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50 overflow-y-auto">
      <div class="bg-white rounded-2xl w-full max-w-4xl my-4">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800" x-text="editingBalance ? 'Edit Saldo Awal' : 'Tambah Saldo Awal'"></h3>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-1">Akun <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="text" 
                       x-model="accountSearch" 
                       @input.debounce.300ms="loadAvailableAccounts()"
                       @focus="showAccountDropdown = true"
                       placeholder="Cari kode atau nama akun..."
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm pr-10">
                <i class='bx bx-search absolute right-3 top-2 text-slate-400'></i>
              </div>
              
              
              <div x-show="showAccountDropdown && availableAccounts.length > 0" 
                   class="absolute z-10 mt-1 w-full max-w-md bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                <template x-for="account in availableAccounts" :key="account.id">
                  <button @click="selectAccount(account)" 
                          class="w-full text-left px-4 py-2 hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                    <div class="font-mono text-sm" x-text="account.code"></div>
                    <div class="text-sm text-slate-600" x-text="account.name"></div>
                    <div class="text-xs text-slate-500" x-text="getTypeName(account.type)"></div>
                  </button>
                </template>
              </div>
              
              
              <div x-show="balanceForm.account_id" class="mt-2 p-3 bg-slate-50 rounded-lg">
                <div class="flex justify-between items-center">
                  <div>
                    <div class="font-mono text-sm font-semibold" x-text="getSelectedAccountCode()"></div>
                    <div class="text-sm text-slate-600" x-text="getSelectedAccountName()"></div>
                  </div>
                  <button @click="clearAccountSelection()" class="text-red-500 hover:text-red-700">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Debit</label>
              <input type="number" 
                     x-model="balanceForm.debit" 
                     @input="calculateBalance()"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" 
                     placeholder="0" 
                     step="0.01" 
                     min="0">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Kredit</label>
              <input type="number" 
                     x-model="balanceForm.credit" 
                     @input="calculateBalance()"
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" 
                     placeholder="0" 
                     step="0.01" 
                     min="0">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Netto</label>
              <div class="p-3 bg-slate-50 rounded-lg">
                <div class="text-lg font-semibold text-center" 
                     :class="getBalanceClass(netBalance)"
                     x-text="formatCurrency(netBalance)"></div>
              </div>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Efektif <span class="text-red-500">*</span></label>
              <input type="date" 
                     x-model="balanceForm.effective_date" 
                     class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
            <textarea x-model="balanceForm.description" 
                      rows="3" 
                      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" 
                      placeholder="Keterangan saldo awal..."></textarea>
          </div>
        </div>
        <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
          <button @click="showBalanceModal = false; showAccountDropdown = false;" 
                  class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 border border-slate-200 rounded-lg">
            Batal
          </button>
          <button @click="saveBalance()" 
                  :disabled="!isFormValid()"
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed">
            Simpan
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="showValidationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50">
      <div class="bg-white rounded-2xl w-full max-w-lg">
        <div class="p-6 border-b border-slate-200">
          <h3 class="text-lg font-semibold text-slate-800">Validasi Saldo Awal</h3>
        </div>
        <div class="p-6">
          <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                 :class="validationResult.is_balanced ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600'">
              <i class='bx text-2xl' :class="validationResult.is_balanced ? 'bx-check-circle' : 'bx-error-circle'"></i>
            </div>
            <h4 class="text-lg font-semibold mb-2" 
                x-text="validationResult.is_balanced ? 'Saldo Seimbang!' : 'Saldo Tidak Seimbang'"></h4>
            <p class="text-slate-600" x-text="validationMessage"></p>
          </div>
          
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="text-center p-3 rounded-lg bg-green-50">
              <div class="text-lg font-bold text-green-600" x-text="formatCurrency(validationResult.total_debit)"></div>
              <div class="text-sm text-green-800">Total Debit</div>
            </div>
            <div class="text-center p-3 rounded-lg bg-red-50">
              <div class="text-lg font-bold text-red-600" x-text="formatCurrency(validationResult.total_credit)"></div>
              <div class="text-sm text-red-800">Total Kredit</div>
            </div>
          </div>
        </div>
        <div class="p-6 border-t border-slate-200 flex justify-end">
          <button @click="showValidationModal = false" 
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Tutup
          </button>
        </div>
      </div>
    </div>

  </div>

  <script>
    function openingBalanceManagement() {
      return {
        // State
        isLoading: false,
        showBalanceModal: false,
        showValidationModal: false,
        showAccountDropdown: false,
        editingBalance: null,
        currentPage: 1,
        perPage: 10,
        lastPage: 1,
        totalItems: 0,
        accountSearch: '',
        netBalance: 0,

        // Data
        outlets: [],
        accountingBooks: [],
        openingBalanceData: [],
        availableAccounts: [],
        selectedAccount: null, // ✅ Added to store selected account
        balanceStats: {
          total_debit: 0,
          total_credit: 0,
          balance: 0,
          total_accounts: 0,
          accounts_with_balance: 0,
          balanced_accounts: 0,
          unbalanced_accounts: 0,
          period: new Date().getFullYear().toString()
        },
        validationResult: {
          is_balanced: false,
          total_debit: 0,
          total_credit: 0,
          balance: 0,
          difference: 0
        },

        // Filters
        filters: {
          outlet: '',
          book: '',
          type: 'all',
          status: 'all',
          search: ''
        },

        // Forms
        balanceForm: {
          account_id: '',
          debit: 0,
          credit: 0,
          effective_date: new Date().toISOString().split('T')[0],
          description: ''
        },

        validationMessage: '',
        typeChart: null,
        chartInitialized: false,

        async init() {
          await this.loadOutlets();
          await this.loadAccountingBooks();
        },

        async loadOutlets() {
            try {
                const response = await fetch('<?php echo e(route("finance.outlets.data")); ?>', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.outlets = data.data;
                }
            } catch (error) {
                console.error('Error loading outlets:', error);
            }
        },

         async loadAccountingBooks() {
            if (!this.filters.outlet) {
                this.accountingBooks = [];
                return;
            }

            try {
                const params = new URLSearchParams({
                    outlet_id: this.filters.outlet,
                    status: 'active'
                });

                const response = await fetch(`<?php echo e(route("finance.accounting-books.data")); ?>?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.accountingBooks = data.data;
                } else {
                    this.accountingBooks = [];
                }
            } catch (error) {
                console.error('Error loading accounting books:', error);
                this.accountingBooks = [];
            }
        },

        async onOutletChange() {
            this.filters.book = '';
            this.accountingBooks = [];
            this.openingBalanceData = []; // Clear data existing
            this.destroyChart(); // Destroy chart lama
            
            if (this.filters.outlet) {
                await this.loadAccountingBooks();
            }
        },

        async loadOpeningBalance() {
            if (!this.filters.outlet || !this.filters.book) {
                this.openingBalanceData = [];
                this.destroyChart();
                return;
            }

            try {
                this.isLoading = true;
                
                const params = new URLSearchParams({
                    page: this.currentPage,
                    per_page: this.perPage,
                    search: this.filters.search,
                    outlet_id: this.filters.outlet,
                    book_id: this.filters.book,
                    type: this.filters.type,
                    status: this.filters.status
                    // HAPUS effective_date dari params
                });

                const response = await fetch(`<?php echo e(route("finance.opening-balance.data")); ?>?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.openingBalanceData = data.data;
                    this.balanceStats = data.stats;
                    this.totalItems = data.meta.total;
                    this.lastPage = data.meta.last_page;
                    
                    // ✅ Init or update chart after data loaded
                    if (this.openingBalanceData.length > 0) {
                        this.$nextTick(() => {
                            if (!this.typeChart) {
                                this.initChart();
                            }
                            this.updateChartData();
                        });
                    }
                } else {
                    this.showAlert('error', 'Gagal memuat data saldo awal: ' + data.message);
                }
            } catch (error) {
                console.error('Error loading opening balance:', error);
                this.showAlert('error', 'Terjadi kesalahan saat memuat data saldo awal');
            } finally {
                this.isLoading = false;
            }
        },


        async loadAvailableAccounts() {
            if (!this.accountSearch.trim()) {
                this.availableAccounts = [];
                return;
            }

            try {
                const params = new URLSearchParams({
                    outlet_id: this.filters.outlet,
                    search: this.accountSearch
                });

                const response = await fetch(`<?php echo e(route("finance.opening-balance.accounts")); ?>?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.availableAccounts = data.data;
                }
            } catch (error) {
                console.error('Error loading accounts:', error);
            }
        },

        selectAccount(account) {
            this.balanceForm.account_id = account.id;
            this.selectedAccount = account; // ✅ Store selected account
            this.showAccountDropdown = false;
            this.accountSearch = '';
            this.availableAccounts = [];
        },

        clearAccountSelection() {
            this.balanceForm.account_id = '';
            this.selectedAccount = null; // ✅ Clear selected account
            this.balanceForm.debit = 0;
            this.balanceForm.credit = 0;
            this.netBalance = 0;
        },

        getSelectedAccountCode() {
          // ✅ Check selectedAccount first, then availableAccounts
          if (this.selectedAccount && this.selectedAccount.id === this.balanceForm.account_id) {
            return this.selectedAccount.code;
          }
          const account = this.availableAccounts.find(acc => acc.id === this.balanceForm.account_id);
          return account ? account.code : '';
        },

        getSelectedAccountName() {
          // ✅ Check selectedAccount first, then availableAccounts
          if (this.selectedAccount && this.selectedAccount.id === this.balanceForm.account_id) {
            return this.selectedAccount.name;
          }
          const account = this.availableAccounts.find(acc => acc.id === this.balanceForm.account_id);
          return account ? account.name : '';
        },

        calculateBalance() {
            this.netBalance = parseFloat(this.balanceForm.debit || 0) - parseFloat(this.balanceForm.credit || 0);
        },

        openCreateBalance() {
            if (!this.filters.outlet || !this.filters.book) {
                this.showAlert('warning', 'Pilih outlet dan buku akuntansi terlebih dahulu');
                return;
            }

            this.editingBalance = null;
            this.balanceForm = {
                account_id: '',
                debit: 0,
                credit: 0,
                effective_date: this.filters.effective_date,
                description: ''
            };
            this.netBalance = 0;
            this.accountSearch = '';
            this.availableAccounts = [];
            this.showBalanceModal = true;
        },

        editBalance(item) {
            this.editingBalance = item.id;
            this.balanceForm = {
                account_id: item.account_id,
                debit: parseFloat(item.debit),
                credit: parseFloat(item.credit),
                effective_date: item.effective_date,
                description: item.description || ''
            };
            this.selectedAccount = item.account; // ✅ Store selected account from item
            this.netBalance = parseFloat(item.debit) - parseFloat(item.credit);
            this.accountSearch = item.account.code + ' - ' + item.account.name;
            this.showBalanceModal = true;
        },

        async saveBalance() {
            if (!this.isFormValid()) {
                return;
            }

            try {
                const url = this.editingBalance ? 
                    `<?php echo e(route("finance.opening-balance.update", "")); ?>/${this.editingBalance}` : 
                    '<?php echo e(route("finance.opening-balance.store")); ?>';
                
                const method = this.editingBalance ? 'PUT' : 'POST';

                const payload = {
                    ...this.balanceForm,
                    outlet_id: this.filters.outlet,
                    book_id: this.filters.book
                };

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    this.showBalanceModal = false;
                    this.showAccountDropdown = false;
                    await this.loadOpeningBalance();
                } else {
                    this.showAlert('error', 'Gagal menyimpan saldo: ' + (data.message || 'Terjadi kesalahan'));
                    if (data.errors) {
                        console.error('Validation errors:', data.errors);
                    }
                }
            } catch (error) {
                console.error('Error saving balance:', error);
                this.showAlert('error', 'Terjadi kesalahan saat menyimpan saldo');
            }
        },

        async deleteBalance(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus saldo awal ini?')) {
                return;
            }

            try {
                const response = await fetch(`<?php echo e(route("finance.opening-balance.delete", "")); ?>/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    await this.loadOpeningBalance();
                } else {
                    this.showAlert('error', 'Gagal menghapus saldo: ' + data.message);
                }
            } catch (error) {
                console.error('Error deleting balance:', error);
                this.showAlert('error', 'Terjadi kesalahan saat menghapus saldo');
            }
        },

        async validateBalance() {
            if (!this.filters.outlet || !this.filters.book) {
                this.showAlert('warning', 'Pilih outlet dan buku akuntansi terlebih dahulu');
                return;
            }

            // ✅ Calculate from current table data
            const totalDebit = this.openingBalanceData.reduce((sum, item) => sum + parseFloat(item.debit || 0), 0);
            const totalCredit = this.openingBalanceData.reduce((sum, item) => sum + parseFloat(item.credit || 0), 0);
            const difference = totalDebit - totalCredit;

            this.validationResult = {
                is_balanced: Math.abs(difference) < 0.01, // Allow small rounding difference
                total_debit: totalDebit,
                total_credit: totalCredit,
                balance: difference,
                difference: Math.abs(difference)
            };

            this.validationMessage = this.validationResult.is_balanced 
                ? 'Saldo awal sudah seimbang!' 
                : `Saldo tidak seimbang. Selisih: ${this.formatCurrency(this.validationResult.difference)}`;

            this.showValidationModal = true;
        },

        async postBalances() {
            if (!this.filters.outlet || !this.filters.book) {
                this.showAlert('warning', 'Pilih outlet dan buku akuntansi terlebih dahulu');
                return;
            }

            // ✅ Calculate and show totals before posting
            const totalDebit = this.openingBalanceData.reduce((sum, item) => sum + parseFloat(item.debit || 0), 0);
            const totalCredit = this.openingBalanceData.reduce((sum, item) => sum + parseFloat(item.credit || 0), 0);
            const difference = Math.abs(totalDebit - totalCredit);

            const confirmMessage = `Posting saldo awal akan membuat entri jurnal permanen.\n\n` +
                `Total Debit: ${this.formatCurrency(totalDebit)}\n` +
                `Total Kredit: ${this.formatCurrency(totalCredit)}\n` +
                `Selisih: ${this.formatCurrency(difference)}\n\n` +
                `Lanjutkan?`;

            if (!confirm(confirmMessage)) {
                return;
            }

            try {
                const response = await fetch('<?php echo e(route("finance.opening-balance.post")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        outlet_id: this.filters.outlet,
                        book_id: this.filters.book,
                        effective_date: this.filters.effective_date
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    await this.loadOpeningBalance();
                } else {
                    this.showAlert('error', data.message);
                    if (data.errors) {
                        console.error('Posting errors:', data.errors);
                    }
                }
            } catch (error) {
                console.error('Error posting balances:', error);
                this.showAlert('error', 'Terjadi kesalahan saat posting saldo');
            }
        },

        // Utility Methods
        isFormValid() {
            return this.balanceForm.account_id && 
                   this.balanceForm.effective_date && 
                   (this.balanceForm.debit > 0 || this.balanceForm.credit > 0) &&
                   !(this.balanceForm.debit > 0 && this.balanceForm.credit > 0);
        },

        changePage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.loadOpeningBalance();
            }
        },

        getPaginationPages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.lastPage, start + 4);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },

        getTypeBadgeClass(type) {
            const classes = {
                asset: 'bg-blue-100 text-blue-800',
                liability: 'bg-red-100 text-red-800',
                equity: 'bg-purple-100 text-purple-800',
                revenue: 'bg-green-100 text-green-800',
                expense: 'bg-orange-100 text-orange-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getTypeName(type) {
            const names = {
                asset: 'Aset',
                liability: 'Kewajiban',
                equity: 'Ekuitas',
                revenue: 'Pendapatan',
                expense: 'Beban'
            };
            return names[type] || type;
        },

        getStatusBadgeClass(status) {
            const classes = {
                active: 'bg-green-100 text-green-800',
                posted: 'bg-blue-100 text-blue-800',
                draft: 'bg-slate-100 text-slate-800',
                cancelled: 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusName(status) {
            const names = {
                active: 'Aktif',
                posted: 'Diposting',
                draft: 'Draft',
                cancelled: 'Dibatalkan'
            };
            return names[status] || status;
        },

        getBalanceClass(balance) {
            if (balance > 0) return 'text-green-600';
            if (balance < 0) return 'text-red-600';
            return 'text-slate-600';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(amount);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        showAlert(type, message) {
            // Simple alert untuk sementara
            const alertType = type === 'error' ? 'error' : 
                            type === 'warning' ? 'warning' : 'success';
            alert(`[${alertType.toUpperCase()}] ${message}`);
        },

        // Cleanup saat component di-destroy
        destroy() {
            this.destroyChart();
        },

        initChart() {
            if (typeof Chart === 'undefined') return;
            
            const ctx = this.$refs.balanceTypeChart;
            if (!ctx) return;

            // Destroy existing chart
            this.destroyChart();

            try {
                this.typeChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'],
                        datasets: [{
                            label: 'Total Saldo',
                            data: [0, 0, 0, 0, 0],
                            backgroundColor: [
                                '#3b82f6', '#ef4444', '#8b5cf6', '#10b981', '#f59e0b'
                            ],
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        return `Saldo: ${this.formatCurrency(context.parsed.y)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => {
                                        if (value >= 1000000) return `Rp ${(value/1000000).toFixed(0)}Jt`;
                                        if (value >= 1000) return `Rp ${(value/1000).toFixed(0)}Rb`;
                                        return `Rp ${value}`;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Chart initialization error:', error);
                this.typeChart = null;
            }
        },

        updateChartData() {
            // ✅ More robust checks
            if (!this.typeChart) return;
            if (!this.openingBalanceData || !this.openingBalanceData.length) return;
            if (!this.typeChart.data || !this.typeChart.data.datasets || !this.typeChart.data.datasets[0]) return;

            try {
                // Reset data
                const typeData = [0, 0, 0, 0, 0];
                const typeIndex = {
                    'asset': 0,
                    'liability': 1,
                    'equity': 2,
                    'revenue': 3,
                    'expense': 4
                };

                // Hitung total per tipe
                this.openingBalanceData.forEach(item => {
                    if (!item || !item.account) return;
                    const balance = Math.abs(parseFloat(item.debit || 0) - parseFloat(item.credit || 0));
                    const index = typeIndex[item.account.type];
                    if (index !== undefined) {
                        typeData[index] += balance;
                    }
                });

                // Update chart data safely
                this.typeChart.data.datasets[0].data = typeData;
                
                // Use requestAnimationFrame to prevent stack overflow
                requestAnimationFrame(() => {
                    if (this.typeChart && this.typeChart.update) {
                        this.typeChart.update('none');
                    }
                });
            } catch (error) {
                console.error('Chart update error:', error);
                // If error, destroy and recreate chart
                this.destroyChart();
            }
        },

        destroyChart() {
            if (this.typeChart) {
                try {
                    this.typeChart.destroy();
                } catch (error) {
                    console.error('Chart destruction error:', error);
                } finally {
                    this.typeChart = null;
                }
            }
        },

        // Method untuk inisialisasi chart saat data tersedia (dipanggil di template)
        initChartIfNeeded() {
            if (this.openingBalanceData.length > 0 && !this.typeChart) {
                this.$nextTick(() => {
                    this.initChart();
                    this.updateChartData();
                });
            }
        },

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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\saldo-awal\index.blade.php ENDPATH**/ ?>