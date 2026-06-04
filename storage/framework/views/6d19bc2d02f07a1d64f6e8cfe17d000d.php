<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Chart of Accounts']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Chart of Accounts')]); ?>
  <div x-data="accountsManagement()" x-init="init()" class="space-y-6">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Chart of Accounts</h1>
        <p class="text-slate-600 text-sm">Kelola semua akun dalam sistem akuntansi</p>
      </div>

      <div class="flex flex-wrap gap-2">
        
        <select x-model="selectedOutlet" @change="loadAccounts()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <template x-for="outlet in outlets" :key="outlet.id_outlet">
            <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
          </template>
        </select>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.akun.create')): ?>
        <button @click="openCreateAccount()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 h-10 hover:bg-emerald-700">
          <i class='bx bx-plus'></i> Tambah Akun
        </button>
        <?php endif; ?>
        <button @click="exportAccounts()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
          <i class='bx bx-export'></i> Export
        </button>
        <button @click="importAccounts()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
          <i class='bx bx-import'></i> Import
        </button>
      </div>
    </div>

    
    <div x-show="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    
    <div x-show="error" class="rounded-xl bg-red-50 border border-red-200 p-4">
      <div class="flex items-center gap-2 text-red-800">
        <i class='bx bx-error-circle text-lg'></i>
        <span x-text="error"></span>
      </div>
    </div>

    
    <div x-show="!loading && !error" class="space-y-6">

      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-800">Distribusi Akun per Tipe</h3>
            <select x-model="chartPeriod" @change="updateCharts()" class="rounded-lg border border-slate-200 px-3 py-1 text-sm">
              <option value="all">Semua</option>
              <option value="active">Aktif</option>
            </select>
          </div>
          <div class="h-64">
            <canvas id="accountDistributionChart" x-ref="accountDistributionChart"></canvas>
          </div>
        </div>

        
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Saldo per Akun Induk</h3>
                <span class="text-sm text-slate-500">
                Total: <span x-text="formatCurrency(parentAccountsTotal)"></span>
                </span>
            </div>
            <div class="h-64">
                <canvas id="parentAccountsChart" x-ref="parentAccountsChart"></canvas>
            </div>
        </div>
      </div>

      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <i class='bx bx-list-ul text-2xl text-blue-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="accountStats.totalAccounts"></div>
              <div class="text-sm text-slate-600">Total Akun (induk + anak)</div>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-1 text-xs">
            <i class='bx bx-plus-circle text-green-500'></i>
            <span class="text-green-600" x-text="accountStats.activeAccounts"></span>
            <span class="text-slate-500">aktif</span>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
              <i class='bx bx-wallet text-2xl text-green-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.assetBalance)"></div>
              <div class="text-sm text-slate-600">Total Aset</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.assetAccounts"></span> akun induk aset
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
              <i class='bx bx-trending-down text-2xl text-red-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.liabilityBalance)"></div>
              <div class="text-sm text-slate-600">Total Kewajiban</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.liabilityAccounts"></span> akun induk kewajiban
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
              <i class='bx bx-line-chart text-2xl text-purple-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.equityBalance)"></div>
              <div class="text-sm text-slate-600">Total Ekuitas</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.equityAccounts"></span> akun induk ekuitas
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center">
              <i class='bx bx-pie-chart-alt text-2xl text-pink-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.revenueBalance)"></div>
              <div class="text-sm text-slate-600">Total Pendapatan</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.revenueAccounts"></span> akun induk pendapatan
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center">
              <i class='bx bx-pie-chart-alt text-2xl text-yellow-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.expenseBalance)"></div>
              <div class="text-sm text-slate-600">Total Beban</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.expenseAccounts"></span> akun induk beban
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <i class='bx bx-pie-chart-alt text-2xl text-blue-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.otherrevenueBalance)"></div>
              <div class="text-sm text-slate-600">Total Pendapatan Lain</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.otherrevenueAccounts"></span> akun induk pendapatan lain
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
              <i class='bx bx-pie-chart-alt text-2xl text-orange-600'></i>
            </div>
            <div>
              <div class="text-2xl font-bold" x-text="formatCurrency(accountStats.otherexpenseBalance)"></div>
              <div class="text-sm text-slate-600">Total Beban Lain</div>
            </div>
          </div>
          <div class="mt-3 text-xs text-slate-500">
            <span x-text="accountStats.otherexpenseAccounts"></span> akun induk beban lain
          </div>
        </div>
      </div>

      
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6 border-b border-slate-200">
          <h2 class="text-lg font-semibold text-slate-800">Daftar Akun</h2>
          <div class="flex flex-wrap gap-2">
            <select x-model="filters.type" @change="loadAccounts()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all">Semua Tipe</option>
              <option value="asset">Aset</option>
              <option value="liability">Kewajiban</option>
              <option value="equity">Ekuitas</option>
              <option value="revenue">Pendapatan</option>
              <option value="expense">Beban</option>
              <option value="otherrevenue">Pendapatan Lain</option>
              <option value="otherexpense">Beban Lain</option>
            </select>
            <select x-model="filters.status" @change="loadAccounts()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
              <option value="all">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
            </select>
            <input type="text" x-model="filters.search" @input.debounce.500ms="loadAccounts()" 
                   placeholder="Cari kode atau nama akun..." class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-64">
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left w-12">No</th>
                <th class="px-4 py-3 text-left">Kode</th>
                <th class="px-4 py-3 text-left">Nama Akun</th>
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-right">Saldo</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left w-40">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <template x-if="accountsData.length === 0">
                <tr>
                  <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                    <div class="flex flex-col items-center gap-2">
                      <i class='bx bx-file-blank text-3xl text-slate-300'></i>
                      <span>Tidak ada data akun</span>
                    </div>
                  </td>
                </tr>
              </template>
              <template x-for="(account, index) in accountsData" :key="account.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50" 
                    :class="account.level > 1 ? 'bg-slate-25' : ''"
                    :style="account.level > 1 ? 'background-color: rgba(248, 250, 252, 0.5)' : ''">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <div class="font-mono text-sm flex items-center gap-1">
                      <!-- Indentasi untuk akun anak -->
                      <template x-if="account.level > 1">
                        <span class="text-slate-300" x-text="'└─'.repeat(account.level - 1)"></span>
                      </template>
                      <span x-text="account.code" :class="account.level > 1 ? 'text-slate-600' : 'font-semibold'"></span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-1">
                      <!-- Indentasi visual untuk hierarki -->
                      <template x-if="account.level > 1">
                        <span class="text-slate-300 text-xs" x-text="'└─'.repeat(account.level - 1)"></span>
                      </template>
                      <span :class="account.level > 1 ? 'text-slate-600' : 'font-semibold text-slate-800'"
                            x-text="account.name"></span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="getTypeBadgeClass(account.type)" x-text="getTypeName(account.type)" 
                          class="px-2 py-1 rounded-full text-xs"></span>
                  </td>
                  <td class="px-4 py-3">
                    <span x-text="account.category" class="text-slate-600 text-xs"></span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1">
                      <!-- Indentasi untuk saldo akun anak -->
                      <template x-if="account.level > 1">
                        <span class="text-slate-300 text-xs mr-2">└─</span>
                      </template>
                      <div class="text-right">
                        <div :class="[
                               'font-semibold',
                               account.level > 1 ? 'text-sm' : 'text-base',
                               getBalanceColor(account.accumulated_balance, account.type)
                             ]"
                             x-text="formatCurrency(account.accumulated_balance)"></div>
                        <template x-if="account.children && account.children.length > 0">
                          <div class="text-xs text-slate-500">
                            <span x-text="account.children.length"></span> akun anak
                          </div>
                        </template>
                        <template x-if="!account.children || account.children.length === 0">
                          <div class="text-xs text-slate-400 italic">
                            Detail
                          </div>
                        </template>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span :class="account.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                          class="px-2 py-1 rounded-full text-xs" x-text="account.status === 'active' ? 'Aktif' : 'Nonaktif'"></span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <button @click="viewBalanceDetails(account)" 
                                class="text-blue-600 hover:text-blue-800 p-1 rounded"
                                title="Lihat Detail Saldo">
                        <i class="bx bx-show text-lg"></i>
                        </button>
                      <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.akun.edit')): ?>
                      <button @click="editAccount(account)" class="text-green-600 hover:text-green-800" title="Edit">
                        <i class="bx bx-edit"></i>
                      </button>
                      <?php endif; ?>
                      <button @click="toggleAccount(account.id, account.status)" 
                              :class="account.status === 'active' ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'"
                              :title="account.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'">
                        <i :class="account.status === 'active' ? 'bx bx-power-off' : 'bx bx-check-circle'"></i>
                      </button>
                      <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.akun.delete')): ?>
                      <button @click="deleteAccount(account)" 
                                        class="text-red-600 hover:text-red-800 p-1 rounded"
                                        :class="account.children && account.children.length > 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                        :title="account.children && account.children.length > 0 ? 'Tidak dapat dihapus karena memiliki akun anak' : 'Hapus'"
                                        :disabled="account.children && account.children.length > 0">
                                    <i class="bx bx-trash text-lg"></i>
                                </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    
    <div x-show="showAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-20 z-50 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-4xl my-4">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800" x-text="editingAccount ? 'Edit Akun' : 'Tambah Akun Baru'"></h3>
            </div>
            <form @submit.prevent="saveAccount()">
                <div class="p-6 space-y-4">
                    
                    <div x-show="formErrors.length > 0" class="rounded-xl bg-red-50 border border-red-200 p-4">
                        <ul class="list-disc list-inside text-red-800 text-sm">
                            <template x-for="error in formErrors" :key="error">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Akun *</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="accountForm.code" 
                                      class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-slate-50"
                                      :class="formErrors.code ? 'border-red-300' : ''"
                                      readonly
                                      placeholder="Kode akan otomatis tergenerate">
                                <button type="button" @click="generateAccountCode()" 
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50 whitespace-nowrap"
                                        :disabled="!accountForm.outlet_id || !accountForm.type">
                                        Generate
                                </button>
                            </div>
                            <p class="mt-1 text-slate-500 text-xs">Kode otomatis berdasarkan outlet, type, dan parent</p>
                            <p x-show="formErrors.code" class="mt-1 text-red-600 text-xs" x-text="formErrors.code"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Akun *</label>
                            <input type="text" x-model="accountForm.name" 
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                  :class="formErrors.name ? 'border-red-300' : ''"
                                  required
                                  placeholder="Contoh: Kas Kecil, Bank BCA, dll.">
                            <p x-show="formErrors.name" class="mt-1 text-red-600 text-xs" x-text="formErrors.name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Akun *</label>
                            <select x-model="accountForm.type" @change="onTypeChange()"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                    :class="formErrors.type ? 'border-red-300' : ''"
                                    required>
                                <option value="asset">Aset</option>
                                <option value="liability">Kewajiban</option>
                                <option value="equity">Ekuitas</option>
                                <option value="revenue">Pendapatan</option>
                                <option value="expense">Beban</option>
                                <option value="otherrevenue">Pendapatan Lain</option>
                                <option value="otherexpense">Beban Lain</option>
                            </select>
                            <p x-show="formErrors.type" class="mt-1 text-red-600 text-xs" x-text="formErrors.type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                            <input type="text" x-model="accountForm.category" 
                                  @input.debounce.300ms="onCategoryChange()"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                  placeholder="Contoh: Aset Lancar, Beban Operasional, Persediaan">
                            <p class="mt-1 text-slate-500 text-xs">Kategori akan mempengaruhi kode akun</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Akun Induk</label>
                            <select x-model="accountForm.parent_id" @change="onParentChange()"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                <option value="">Tidak ada (Akun Level 1)</option>
                                <template x-for="account in parentAccounts" :key="account.id">
                                    <option :value="account.id" x-text="account.code + ' - ' + account.name"></option>
                                </template>
                            </select>
                            <p class="mt-1 text-slate-500 text-xs" x-text="accountForm.parent_id ? 'Akun Level 2+' : 'Akun Level 1'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select x-model="accountForm.status" 
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea x-model="accountForm.description" rows="3" 
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                  placeholder="Deskripsi optional untuk akun"></textarea>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" @click="showAccountModal = false" 
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="saving || !accountForm.code"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                        <i x-show="saving" class='bx bx-loader-alt animate-spin'></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div x-show="showImportPreview" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-10 z-50 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-6xl my-4">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Preview Import Akun</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-slate-600">Total data: <span x-text="importPreview.total"></span></p>
                    <p class="text-sm text-green-600" x-show="importPreview.new > 0">Akun baru: <span x-text="importPreview.new"></span></p>
                    <p class="text-sm text-blue-600" x-show="importPreview.updated > 0">Akun diupdate: <span x-text="importPreview.updated"></span></p>
                    <p class="text-sm text-red-600" x-show="importPreview.errors.length > 0">Error: <span x-text="importPreview.errors.length"></span></p>
                </div>
                
                <div x-show="importPreview.errors.length > 0" class="mb-4">
                    <h4 class="font-semibold text-red-700 mb-2">Error Details:</h4>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 max-h-32 overflow-y-auto">
                        <template x-for="error in importPreview.errors" :key="error.line">
                            <p class="text-sm text-red-700" x-text="`Baris ${error.line}: ${error.message}`"></p>
                        </template>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button @click="showImportPreview = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-lg border border-slate-200 hover:bg-slate-50">
                        Tutup
                    </button>
                    <button @click="confirmImport()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Konfirmasi Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div x-show="showBalanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-10 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-6xl my-4">
        <div class="p-6 border-b border-slate-200">
        <h3 class="text-lg font-semibold text-slate-800">Detail Saldo Akun</h3>
        </div>
        
        <div class="p-6 space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl">
            <div>
            <label class="block text-sm font-medium text-slate-700">Kode Akun</label>
            <p class="text-lg font-mono font-semibold" x-text="balanceDetails.account.code"></p>
            </div>
            <div>
            <label class="block text-sm font-medium text-slate-700">Nama Akun</label>
            <p class="text-lg font-semibold" x-text="balanceDetails.account.name"></p>
            </div>
            <div>
            <label class="block text-sm font-medium text-slate-700">Tipe Akun</label>
            <p>
                <span :class="getTypeBadgeClass(balanceDetails.account.type)" 
                    x-text="getTypeName(balanceDetails.account.type)"
                    class="px-2 py-1 rounded-full text-xs"></span>
            </p>
            </div>
            <div>
            <label class="block text-sm font-medium text-slate-700">Level</label>
            <p class="text-sm" x-text="'Level ' + balanceDetails.account.level"></p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
            <div class="text-sm text-blue-700 font-medium">Saldo Akumulasi</div>
            <div class="text-2xl font-bold text-blue-800" 
                x-text="formatCurrency(balanceDetails.balances.accumulated_balance)"></div>
            </div>
            <div class="bg-green-50 p-4 rounded-xl border border-green-200">
            <div class="text-sm text-green-700 font-medium">Total Debit</div>
            <div class="text-2xl font-bold text-green-800" 
                x-text="formatCurrency(balanceDetails.summary.total_debit)"></div>
            </div>
            <div class="bg-red-50 p-4 rounded-xl border border-red-200">
            <div class="text-sm text-red-700 font-medium">Total Kredit</div>
            <div class="text-2xl font-bold text-red-800" 
                x-text="formatCurrency(balanceDetails.summary.total_credit)"></div>
            </div>
            <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
            <div class="text-sm text-purple-700 font-medium">Jumlah Entri</div>
            <div class="text-2xl font-bold text-purple-800" 
                x-text="balanceDetails.summary.entry_count"></div>
            </div>
        </div>

        
        <div>
            <h4 class="text-lg font-semibold text-slate-800 mb-4">Transaksi Jurnal</h4>
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">No. Transaksi</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-left">Buku</th>
                    <th class="px-4 py-3 text-right">Debit</th>
                    <th class="px-4 py-3 text-right">Kredit</th>
                    <th class="px-4 py-3 text-right">Perubahan</th>
                </tr>
                </thead>
                <tbody>
                <template x-if="balanceDetails.journal_entries.length === 0">
                    <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                        <i class='bx bx-file-blank text-3xl text-slate-300'></i>
                        <span>Tidak ada transaksi jurnal</span>
                        </div>
                    </td>
                    </tr>
                </template>
                <template x-for="entry in balanceDetails.journal_entries" :key="entry.transaction_number">
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3" x-text="entry.date"></td>
                    <td class="px-4 py-3 font-mono text-xs" x-text="entry.transaction_number"></td>
                    <td class="px-4 py-3" x-text="entry.description"></td>
                    <td class="px-4 py-3" x-text="entry.book_name"></td>
                    <td class="px-4 py-3 text-right text-green-600 font-medium" 
                        x-text="entry.debit > 0 ? formatCurrency(entry.debit) : '-'"></td>
                    <td class="px-4 py-3 text-right text-red-600 font-medium" 
                        x-text="entry.credit > 0 ? formatCurrency(entry.credit) : '-'"></td>
                    <td class="px-4 py-3 text-right font-medium" 
                        :class="entry.balance_change >= 0 ? 'text-green-600' : 'text-red-600'"
                        x-text="formatCurrency(entry.balance_change)"></td>
                    </tr>
                </template>
                </tbody>
            </table>
            </div>
        </div>
        </div>
        
        <div class="p-6 border-t border-slate-200 flex justify-end">
        <button @click="showBalanceModal = false" 
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Tutup
        </button>
        </div>
    </div>
    </div>

  </div>

  <script>
    function accountsManagement() {
        return {
            // State
            loading: false,
            saving: false,
            error: null,
            chartPeriod: 'all',
            showAccountModal: false,
            showImportModal: false,
            showImportPreview: false,
            editingAccount: null,
            selectedOutlet: null,
            outlets: [],
            
            // Data
            accountForm: {
                outlet_id: null,
                code: '',
                name: '',
                type: 'asset',
                category: '',
                parent_id: '',
                status: 'active',
                description: ''
            },
            filters: {
                type: 'all',
                status: 'all',
                search: ''
            },
            accountStats: {
                totalAccounts: 0,
                activeAccounts: 0,
                assetBalance: 0,
                liabilityBalance: 0,
                equityBalance: 0,
                assetAccounts: 0,
                liabilityAccounts: 0,
                equityAccounts: 0,
                totalBalance: 0,
                revenueBalance: 0,
                expenseBalance: 0,
                revenueAccounts: 0,
                expenseAccounts: 0,
                otherrevenueBalance: 0,
                otherexpenseBalance: 0,
                otherrevenueAccounts: 0,
                otherexpenseAccounts: 0,

            },
            accountsData: [],
            parentAccounts: [],
            formErrors: [],
            importPreview: { // Tambahkan ini
                total: 0,
                new: 0,
                updated: 0,
                errors: []
            },

            showBalanceModal: false,
            balanceDetails: {
                account: {},
                balances: {},
                journal_entries: [],
                summary: {}
            },

            // Routes - Define all route URLs
            routes: {
                chartOfAccountsData: '<?php echo e(route("finance.chart-of-accounts.data")); ?>',
                parentAccounts: '<?php echo e(route("finance.chart-of-accounts.parents")); ?>',
                generateCode: '<?php echo e(route("finance.chart-of-accounts.generate-code")); ?>',
                storeAccount: '<?php echo e(route("finance.chart-of-accounts.store")); ?>',
                updateAccount: '<?php echo e(route("finance.chart-of-accounts.update", ["id" => ":id"])); ?>',
                toggleAccount: '<?php echo e(route("finance.chart-of-accounts.toggle", ["id" => ":id"])); ?>',
                deleteAccount: '<?php echo e(route("finance.chart-of-accounts.delete", ["id" => ":id"])); ?>',
                exportAccounts: '<?php echo e(route("finance.chart-of-accounts.export")); ?>',
                importAccounts: '<?php echo e(route("finance.chart-of-accounts.import")); ?>',
                outletsData: '<?php echo e(route("finance.outlets.data")); ?>'
            },

            // Methods
            async init() {
                await this.loadOutlets();
                
                // Use OutletHelper to set default outlet
                let defaultOutletId = null;
                if (window.OutletHelper) {
                    defaultOutletId = window.OutletHelper.getFirstOutletId(this.outlets);
                } else if (this.outlets.length > 0) {
                    defaultOutletId = this.outlets[0].id_outlet;
                }
                
                if (defaultOutletId) {
                    this.selectedOutlet = defaultOutletId;
                    this.accountForm.outlet_id = defaultOutletId;
                    console.log('Chart of Accounts: Using outlet ID:', defaultOutletId);
                }
                
                await this.loadAccounts();
            },

            onTypeChange() {
                this.loadParentAccounts();
                if (!this.editingAccount) {
                    this.generateAccountCode();
                }
            },

            onParentChange() {
                if (!this.editingAccount) {
                    this.generateAccountCode();
                }
            },

            onCategoryChange() {
                if (!this.editingAccount) {
                    // Clear kode sementara saat kategori berubah
                    this.accountForm.code = '';
                    
                    clearTimeout(this.generateTimeout);
                    this.generateTimeout = setTimeout(() => {
                        this.generateAccountCode();
                    }, 300);
                }
            },

            async generateAccountCode() {
                if (!this.accountForm.outlet_id || !this.accountForm.type) {
                    console.log('Missing required fields for code generation');
                    return;
                }

                // Tampilkan loading state
                this.accountForm.code = 'Generating...';

                try {
                    const params = new URLSearchParams({
                        outlet_id: this.accountForm.outlet_id,
                        parent_id: this.accountForm.parent_id || '',
                        type: this.accountForm.type,
                        category: this.accountForm.category || ''
                    });

                    console.log('Generating code with params:', {
                        outlet_id: this.accountForm.outlet_id,
                        parent_id: this.accountForm.parent_id,
                        type: this.accountForm.type,
                        category: this.accountForm.category || '(empty)'
                    });

                    const url = `${this.routes.generateCode}?${params}`;
                    const response = await fetch(url);
                    const result = await response.json();

                    if (result.success) {
                        this.accountForm.code = result.data.code;
                        console.log('Successfully generated code:', this.accountForm.code);
                    } else {
                        this.accountForm.code = '';
                        console.error('Failed to generate code:', result.message);
                        this.showNotification('Gagal generate kode: ' + result.message, 'error');
                    }
                } catch (error) {
                    this.accountForm.code = '';
                    console.error('Error generating code:', error);
                    this.showNotification('Error generating code: ' + error.message, 'error');
                }
            },

            async loadOutlets() {
                try {
                    const response = await fetch(this.routes.outletsData);
                    const result = await response.json();

                    if (result.success) {
                        this.outlets = result.data;
                        // Set default outlet jika belum ada
                        if (this.outlets.length > 0 && !this.selectedOutlet) {
                            this.selectedOutlet = this.outlets[0].id_outlet;
                            this.accountForm.outlet_id = this.outlets[0].id_outlet;
                        }
                    } else {
                        console.error('Error loading outlets:', result.message);
                    }
                } catch (error) {
                    console.error('Error loading outlets:', error);
                    // Fallback data
                    this.outlets = [
                        { id_outlet: 1, kode_outlet: 'OUT-001', nama_outlet: 'Outlet Pusat' },
                        { id_outlet: 2, kode_outlet: 'OUT-002', nama_outlet: 'Outlet Cabang 1' },
                        { id_outlet: 3, kode_outlet: 'OUT-003', nama_outlet: 'Outlet Cabang 2' }
                    ];
                }
            },

            async loadAccounts() {
                this.loading = true;
                this.error = null;

                try {
                    this.accountForm.outlet_id = this.selectedOutlet;

                    const params = new URLSearchParams({
                        outlet_id: this.selectedOutlet,
                        type: this.filters.type,
                        status: this.filters.status,
                        search: this.filters.search
                    });

                    const url = `${this.routes.chartOfAccountsData}?${params}`;
                    const response = await fetch(url);
                    const result = await response.json();

                    if (result.success) {
                        this.accountsData = result.data;
                        this.accountStats = result.stats;
                        
                        // Wait for DOM update
                        await this.$nextTick();
                        
                        // Use fresh chart initialization
                        this.safeInitCharts();
                    } else {
                        this.error = result.message;
                    }
                } catch (error) {
                    this.error = 'Gagal memuat data akun: ' + error.message;
                } finally {
                    this.loading = false;
                }
            },

            async loadParentAccounts() {
                try {
                    const params = new URLSearchParams({
                        outlet_id: this.accountForm.outlet_id,
                        type: this.accountForm.type
                    });

                    const url = `${this.routes.parentAccounts}?${params}`;
                    const response = await fetch(url);
                    const result = await response.json();

                    if (result.success) {
                        this.parentAccounts = result.data;
                        
                        // Jika parent_id tidak sesuai dengan type yang baru, reset parent_id
                        if (this.accountForm.parent_id) {
                            const currentParentExists = this.parentAccounts.some(
                                parent => parent.id == this.accountForm.parent_id
                            );
                            if (!currentParentExists) {
                                this.accountForm.parent_id = '';
                                this.debouncedGenerateCode(); // Regenerate code
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error loading parent accounts:', error);
                }
            },

            openCreateAccount() {
                this.editingAccount = null;
                this.accountForm = {
                    outlet_id: this.selectedOutlet,
                    code: '',
                    name: '',
                    type: 'asset',
                    category: '',
                    parent_id: '',
                    status: 'active',
                    description: ''
                };
                this.formErrors = [];
                this.loadParentAccounts();
                this.showAccountModal = true;

                this.$nextTick(() => {
                    this.generateAccountCode();
                });
            },

            editAccount(account) {
                this.editingAccount = account.id;
                this.accountForm = {
                    outlet_id: account.outlet_id,
                    code: account.code,
                    name: account.name,
                    type: account.type,
                    category: account.category || '',
                    parent_id: account.parent_id || '',
                    status: account.status,
                    description: account.description || ''
                };
                this.formErrors = [];
                this.loadParentAccounts();
                this.showAccountModal = true;
            },

            async saveAccount() {
                this.saving = true;
                this.formErrors = [];

                try {
                    const url = this.editingAccount 
                        ? this.routes.updateAccount.replace(':id', this.editingAccount)
                        : this.routes.storeAccount;

                    const method = this.editingAccount ? 'PUT' : 'POST';

                    console.log('Sending data:', this.accountForm);

                    // Dapatkan CSRF token dengan cara yang lebih aman
                    const csrfToken = this.getCsrfToken();

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.accountForm)
                    });

                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        this.showAccountModal = false;
                        await this.loadAccounts();
                        this.showNotification(result.message, 'success');
                    } else {
                        if (result.errors) {
                            this.formErrors = Object.values(result.errors).flat();
                        } else {
                            this.formErrors = [result.message];
                        }
                        console.error('Server errors:', result.errors);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    this.formErrors = ['Terjadi kesalahan saat menyimpan data: ' + error.message];
                } finally {
                    this.saving = false;
                }
            },

            // Method untuk mendapatkan CSRF token
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

            async toggleAccount(id, currentStatus) {
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                const action = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';

                if (!confirm(`Apakah Anda yakin ingin ${action} akun ini?`)) {
                    return;
                }

                try {
                    const url = this.routes.toggleAccount.replace(':id', id);
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        await this.loadAccounts();
                        this.showNotification(result.message, 'success');
                    } else {
                        this.showNotification(result.message, 'error');
                    }
                } catch (error) {
                    this.showNotification('Gagal mengubah status akun', 'error');
                }
            },

            viewAccount(id) {
                // Redirect to detail page or show modal
                // Jika ada route detail, gunakan: window.location.href = `/finance/akun/${id}`;
                this.showNotification('Fitur detail akun akan segera tersedia', 'info');
            },

            // Chart management methods
            initCharts() {
                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js not loaded');
                    return;
                }

                // Clear any existing timeouts
                if (this.chartInitTimeout) {
                    clearTimeout(this.chartInitTimeout);
                }

                // Delay initialization to ensure DOM is ready
                this.chartInitTimeout = setTimeout(() => {
                    this.safeInitCharts();
                }, 150);
            },

            safeInitCharts() {
                // Destroy existing charts first
                this.safeDestroyCharts();
                
                // Create new charts
                this.safeCreateDistributionChart();
                this.safeCreateParentAccountsChart();
            },

            safeDestroyCharts() {
                // Destroy distribution chart
                if (this.distributionChart instanceof Chart) {
                    try {
                        this.distributionChart.destroy();
                    } catch (e) {
                        console.warn('Error destroying distribution chart:', e);
                    }
                    this.distributionChart = null;
                }

                // Destroy parent accounts chart
                if (this.parentAccountsChart instanceof Chart) {
                    try {
                        this.parentAccountsChart.destroy();
                    } catch (e) {
                        console.warn('Error destroying parent accounts chart:', e);
                    }
                    this.parentAccountsChart = null;
                }

                // Clear canvas elements
                this.clearCanvas(this.$refs.accountDistributionChart);
                this.clearCanvas(this.$refs.parentAccountsChart);
            },

            clearCanvas(canvas) {
                if (!canvas) return;
                
                try {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                } catch (e) {
                    console.warn('Error clearing canvas:', e);
                }
            },

            safeCreateDistributionChart() {
                const canvas = this.$refs.accountDistributionChart;
                if (!canvas) {
                    console.warn('Distribution chart canvas not found');
                    return;
                }

                // Check if canvas is already in use
                if (this.isCanvasInUse(canvas)) {
                    console.warn('Distribution canvas already in use, skipping');
                    return;
                }

                try {
                    this.distributionChart = new Chart(canvas, {
                        type: 'doughnut',
                        data: {
                            labels: ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban', 'Pendapatan Lain', 'Beban Lain'],
                            datasets: [{
                                data: [
                                    this.accountStats.assetAccounts || 0,
                                    this.accountStats.liabilityAccounts || 0,
                                    this.accountStats.equityAccounts || 0,
                                    this.accountStats.revenueAccounts || 0,
                                    this.accountStats.expenseAccounts || 0,
                                    this.accountStats.otherrevenueAccounts || 0,
                                    this.accountStats.otherexpenseAccounts || 0
                                ],
                                backgroundColor: [
                                    '#3b82f6', '#ef4444', '#8b5cf6', '#10b981', '#f59e0b', '#6b7280', '#6b7280'
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
                            },
                            // Disable animations for stability
                            animation: false
                        }
                    });
                    
                    console.log('Distribution chart created successfully');
                } catch (error) {
                    console.error('Error creating distribution chart:', error);
                    this.distributionChart = null;
                }
            },

            get parentAccountsTotal() {
                if (!this.accountStats.parentAccountsBalance) return 0;
                return this.accountStats.parentAccountsBalance.reduce((total, account) => {
                    return total + Math.abs(account.balance);
                }, 0);
            },

            safeCreateParentAccountsChart() {
                const canvas = this.$refs.parentAccountsChart;
                if (!canvas) {
                    console.warn('Parent accounts chart canvas not found');
                    return;
                }

                // Check if canvas is already in use
                if (this.isCanvasInUse(canvas)) {
                    console.warn('Parent accounts canvas already in use, skipping');
                    return;
                }

                try {
                    const parentAccounts = this.accountStats.parentAccountsBalance || [];
                    
                    // Sort by balance descending and take top 10
                    const sortedAccounts = parentAccounts
                        .filter(account => Math.abs(account.balance) > 0)
                        .sort((a, b) => Math.abs(b.balance) - Math.abs(a.balance))
                        .slice(0, 10);

                    const labels = sortedAccounts.map(account => {
                        const shortName = account.name.length > 20 ? 
                            account.name.substring(0, 20) + '...' : account.name;
                        return `${account.code} - ${shortName}`;
                    });

                    const balances = sortedAccounts.map(account => Math.abs(account.balance));
                    
                    // Generate colors based on account type
                    const backgroundColors = sortedAccounts.map(account => {
                        const colors = {
                            asset: 'rgba(59, 130, 246, 0.8)',
                            liability: 'rgba(239, 68, 68, 0.8)',
                            equity: 'rgba(139, 92, 246, 0.8)',
                            revenue: 'rgba(16, 185, 129, 0.8)',
                            expense: 'rgba(245, 158, 11, 0.8)',
                            otherrevenue: 'rgba(107, 114, 128, 0.8)',
                            otherexpense: 'rgba(156, 163, 175, 0.8)'
                        };
                        return colors[account.type] || 'rgba(156, 163, 175, 0.8)';
                    });

                    const borderColors = sortedAccounts.map(account => {
                        const colors = {
                            asset: 'rgb(59, 130, 246)',
                            liability: 'rgb(239, 68, 68)',
                            equity: 'rgb(139, 92, 246)',
                            revenue: 'rgb(16, 185, 129)',
                            expense: 'rgb(245, 158, 11)',
                            otherrevenue: 'rgb(107, 114, 128)',
                            otherexpense: 'rgb(156, 163, 175)'
                        };
                        return colors[account.type] || 'rgb(156, 163, 175)';
                    });

                    this.parentAccountsChart = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Saldo Akun Induk',
                                data: balances,
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            const account = sortedAccounts[context.dataIndex];
                                            const balance = account.balance;
                                            const typeName = this.getTypeName(account.type);
                                            const sign = balance >= 0 ? '+' : '-';
                                            return [
                                                `Akun: ${account.name}`,
                                                `Tipe: ${typeName}`,
                                                `Saldo: ${sign} ${this.formatCurrency(Math.abs(balance))}`
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                            }
                                            return 'Rp ' + value;
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            animation: false
                        }
                    });
                    
                    console.log('Parent accounts chart created successfully');
                } catch (error) {
                    console.error('Error creating parent accounts chart:', error);
                    this.parentAccountsChart = null;
                }
            },

            // Helper to check if canvas is already used by a chart
            isCanvasInUse(canvas) {
                if (!canvas) return false;
                
                try {
                    // Check if canvas has Chart.js instance
                    const chart = Chart.getChart(canvas);
                    return chart !== undefined;
                } catch (e) {
                    return false;
                }
            },

            updateCharts() {
                // Use a fresh approach - always recreate charts
                console.log('Updating charts...');
                this.safeInitCharts();
            },

            showNotification(message, type = 'info') {
                // Create temporary notification
                const notification = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' : 
                             type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                
                notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
                notification.textContent = message;
                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.add('opacity-100');
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('opacity-100');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            },

            getTypeBadgeClass(type) {
                const classes = {
                    asset: 'bg-blue-100 text-blue-800',
                    liability: 'bg-red-100 text-red-800',
                    equity: 'bg-purple-100 text-purple-800',
                    revenue: 'bg-green-100 text-green-800',
                    expense: 'bg-orange-100 text-orange-800',
                    otherrevenue: 'bg-green-100 text-green-800',
                    otherexpense: 'bg-orange-100 text-orange-800'
                };
                return classes[type] || 'bg-gray-100 text-gray-800';
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

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            },

            exportAccounts() {
                this.showNotification('Fitur export akan segera tersedia', 'info');
            },

            importAccounts() {
                this.showNotification('Fitur import akan segera tersedia', 'info');
            },

            // Tambahkan method deleteAccount
            async deleteAccount(account) {
                // Validasi: akun yang memiliki children tidak bisa dihapus
                if (account.children && account.children.length > 0) {
                    this.showNotification('Tidak dapat menghapus akun yang memiliki akun anak', 'error');
                    return;
                }

                // Validasi: akun dengan saldo tidak nol tidak bisa dihapus
                if (parseFloat(account.balance) !== 0) {
                    this.showNotification('Tidak dapat menghapus akun yang memiliki saldo tidak nol', 'error');
                    return;
                }

                // Validasi: akun sistem tidak bisa dihapus
                if (account.is_system_account) {
                    this.showNotification('Tidak dapat menghapus akun sistem', 'error');
                    return;
                }

                const confirmation = confirm(`Apakah Anda yakin ingin menghapus akun "${account.name}" (${account.code})?`);
                
                if (!confirmation) {
                    return;
                }

                try {
                    const url = this.routes.deleteAccount.replace(':id', account.id);
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification(result.message, 'success');
                        await this.loadAccounts(); // Reload data
                    } else {
                        this.showNotification(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    this.showNotification('Gagal menghapus akun', 'error');
                }
            },

            // Export function
            async exportAccounts() {
                if (!this.selectedOutlet) {
                    this.showNotification('Pilih outlet terlebih dahulu', 'error');
                    return;
                }

                try {
                    const params = new URLSearchParams({
                        outlet_id: this.selectedOutlet
                    });

                    const url = `${this.routes.exportAccounts}?${params}`;
                    
                    // Download file
                    const response = await fetch(url);
                    const blob = await response.blob();
                    
                    // Create download link
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    
                    // Get outlet name for filename
                    const outlet = this.outlets.find(o => o.id_outlet == this.selectedOutlet);
                    const outletName = outlet ? outlet.nama_outlet.replace(/\s+/g, '_') : 'outlet';
                    a.download = `daftar_akun_${outletName}_${new Date().toISOString().split('T')[0]}.xlsx`;
                    
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);
                    
                    this.showNotification('Export berhasil, file sedang didownload', 'success');
                } catch (error) {
                    console.error('Export error:', error);
                    this.showNotification('Gagal export data', 'error');
                }
            },

            // Import function
            importAccounts() {
                if (!this.selectedOutlet) {
                    this.showNotification('Pilih outlet terlebih dahulu', 'error');
                    return;
                }

                // Create file input
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = '.xlsx,.xls,.csv';
                
                input.onchange = async (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Validate file type
                    const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
                    if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/)) {
                        this.showNotification('Format file tidak didukung. Gunakan file Excel (.xlsx, .xls) atau CSV', 'error');
                        return;
                    }

                    if (!confirm(`Import data akun ke outlet ${this.getOutletName(this.selectedOutlet)}? Data yang sudah ada akan diupdate.`)) {
                        return;
                    }

                    await this.processImport(file);
                };
                
                input.click();
            },

            async processImport(file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('outlet_id', this.selectedOutlet);

                try {
                    const response = await fetch(this.routes.importAccounts, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification(result.message, 'success');
                        await this.loadAccounts(); // Reload data
                    } else {
                        this.showNotification(result.message, 'error');
                        if (result.errors) {
                            console.error('Import errors:', result.errors);
                        }
                    }
                } catch (error) {
                    console.error('Import error:', error);
                    this.showNotification('Gagal import data', 'error');
                }
            },

            // Helper function
            getOutletName(outletId) {
                const outlet = this.outlets.find(o => o.id_outlet == outletId);
                return outlet ? outlet.nama_outlet : 'Outlet';
            },

            async viewBalanceDetails(account) {
                try {
                    this.loading = true;
                    const url = `<?php echo e(route('finance.chart-of-accounts.balance-details', ['id' => ':id'])); ?>`.replace(':id', account.id);
                    const response = await fetch(url);
                    const result = await response.json();

                    if (result.success) {
                        this.balanceDetails = result.data;
                        this.showBalanceModal = true;
                    } else {
                        this.showNotification(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Error loading balance details:', error);
                    this.showNotification('Gagal memuat detail saldo', 'error');
                } finally {
                    this.loading = false;
                }
            },

            getBalanceColor(balance, type) {
                // Untuk semua tipe akun, tampilkan hijau jika positif, merah jika negatif
                // Tapi dengan normalisasi yang sudah dilakukan di backend,
                // semua saldo seharusnya positif untuk balance yang "normal"
                const normalizedBalance = this.getNormalizedBalance(balance, type);
                return normalizedBalance >= 0 ? 'text-green-600' : 'text-red-600';
            },

            getNormalizedBalance(balance, type) {
                switch (type) {
                    case 'asset':
                    case 'expense':
                    case 'otherexpense':
                        return balance; // Tetap seperti dari backend
                    case 'liability':
                    case 'equity':
                    case 'revenue':
                    case 'otherrevenue':
                        return balance; // Sudah dinormalisasi di backend
                    default:
                        return balance;
                }
            },

            // Format currency dengan penanda +/- yang benar
            formatCurrencyWithSign(amount, type) {
                const normalized = this.getNormalizedBalance(amount, type);
                const sign = normalized >= 0 ? '+' : '-';
                const absValue = Math.abs(normalized);
                return `${sign} ${this.formatCurrency(absValue)}`;
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\akun\index.blade.php ENDPATH**/ ?>