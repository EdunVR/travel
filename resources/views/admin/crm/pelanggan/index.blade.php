<x-layouts.admin :title="'Manajemen Pelanggan'">
  <div x-data="customerManagement()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Manajemen Pelanggan</h1>
        <p class="text-slate-600 text-sm">Kelola data pelanggan dan informasi kontak</p>
      </div>

      <div class="flex flex-wrap gap-2">
        {{-- View Toggle --}}
        <div class="inline-flex rounded-xl border border-slate-200 bg-white p-1">
          <button @click="viewMode = 'grid'" 
                  :class="viewMode === 'grid' ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-colors">
            <i class='bx bx-grid-alt'></i>
          </button>
          <button @click="viewMode = 'table'" 
                  :class="viewMode === 'table' ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-slate-50'"
                  class="px-3 py-1.5 rounded-lg transition-colors">
            <i class='bx bx-list-ul'></i>
          </button>
        </div>

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
              @foreach($outlets as $outlet)
              <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                <input type="checkbox" value="{{ $outlet->id_outlet }}" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                       class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                <span class="text-sm">{{ $outlet->nama_outlet }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Filter Tipe --}}
        <select x-model="filters.tipe" @change="loadData()" 
                class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <option value="all">Semua Tipe</option>
          <template x-for="tipe in tipes" :key="tipe.id_tipe">
            <option :value="tipe.id_tipe" x-text="tipe.nama_tipe"></option>
          </template>
        </select>

        @hasPermission('crm.pelanggan.create')
        <button @click="openCreateModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
          <i class='bx bx-plus'></i> Tambah Pelanggan
        </button>
        @endhasPermission

        @hasPermission('crm.pelanggan.import')
        {{-- Import Button --}}
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import'></i>
          <span>Import</span>
          <input type="file" class="hidden" accept=".xlsx,.xls" @change="importExcel($event)">
        </label>
        @endhasPermission
        
        @hasPermission('crm.pelanggan.export')
        {{-- Export Dropdown --}}
        <div x-data="{ exportOpen: false }" class="relative">
          <button @click="exportOpen = !exportOpen" 
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 h-10 hover:bg-slate-50">
            <i class='bx bx-export'></i>
            <span>Export</span>
            <i class='bx bx-chevron-down text-sm'></i>
          </button>

          <div x-show="exportOpen" 
               @click.away="exportOpen = false"
               x-transition
               class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg z-10">
            <button @click="exportExcel(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-t-xl">
              <i class='bx bx-file text-green-600'></i>
              <span>Export ke XLSX</span>
            </button>
            <button @click="exportPdf(); exportOpen = false" 
                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center gap-2 rounded-b-xl border-t border-slate-100">
              <i class='bx bxs-file-pdf text-red-600'></i>
              <span>Export ke PDF</span>
            </button>
          </div>
        </div>
        @endhasPermission
      </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class='bx bx-user text-2xl text-blue-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold" x-text="statistics.total_customers">0</div>
            <div class="text-sm text-slate-600">Total Pelanggan</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class='bx bx-money text-2xl text-red-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold text-red-600" x-text="formatRupiah(statistics.total_piutang)">Rp 0</div>
            <div class="text-sm text-slate-600">Total Piutang</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
            <i class='bx bx-store text-2xl text-green-600'></i>
          </div>
          <div>
            <div class="text-2xl font-bold text-green-600">{{ $outlets->count() }}</div>
            <div class="text-sm text-slate-600">Outlet Aktif</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Search Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="relative">
        <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
        <input type="text" 
               x-model="filters.search" 
               @input.debounce.500ms="loadData()" 
               placeholder="Cari nama, telepon, alamat, atau kode member..."
               class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
      </div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <template x-for="customer in customers" :key="customer.id_member">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-primary-50 text-primary-700" 
                      x-text="customer.kode_display"></span>
              </div>
              <h3 class="font-semibold text-lg" x-text="customer.nama"></h3>
              <p class="text-sm text-slate-600" x-text="customer.tipe_nama"></p>
            </div>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm" x-show="customer.nama_perusahaan">
              <i class='bx bx-buildings text-slate-400'></i>
              <span x-text="customer.nama_perusahaan"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-phone text-slate-400'></i>
              <span x-text="customer.telepon"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-map text-slate-400'></i>
              <span class="line-clamp-1" x-text="customer.alamat || '-'"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-store text-slate-400'></i>
              <span x-text="customer.outlet_nama"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <i class='bx bx-money text-red-400'></i>
              <span class="font-medium text-red-600" x-text="customer.total_piutang_formatted"></span>
            </div>
          </div>

          <div class="flex gap-2 pt-3 border-t border-slate-100">
            <button @click="viewCustomer(customer.id_member)" 
                    class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-1">
              <i class='bx bx-show'></i> Detail
            </button>
            @hasPermission('crm.pelanggan.update')
            <button @click="editCustomer(customer.id_member)" 
                    class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50 flex items-center justify-center gap-1">
              <i class='bx bx-edit'></i> Edit
            </button>
            @endhasPermission
            @hasPermission('crm.pelanggan.delete')
            <button @click="deleteCustomer(customer.id_member)" 
                    class="px-3 py-1.5 text-sm rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
              <i class='bx bx-trash'></i>
            </button>
            @endhasPermission
          </div>
        </div>
      </template>

      <div x-show="customers.length === 0" class="col-span-full text-center py-12 text-slate-500">
        <i class='bx bx-user-x text-5xl mb-2'></i>
        <p>Belum ada data pelanggan</p>
      </div>
    </div>

    {{-- Table View --}}
    <div x-show="viewMode === 'table'" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">No</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Kode</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Perusahaan</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Telepon</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Alamat</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Tipe</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Outlet</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Piutang</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(customer, index) in customers" :key="customer.id_member">
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3" x-text="index + 1"></td>
                <td class="px-4 py-3">
                  <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-primary-50 text-primary-700" 
                        x-text="customer.kode_display"></span>
                </td>
                <td class="px-4 py-3 font-medium" x-text="customer.nama"></td>
                <td class="px-4 py-3" x-text="customer.nama_perusahaan || '-'"></td>
                <td class="px-4 py-3" x-text="customer.telepon"></td>
                <td class="px-4 py-3 max-w-xs truncate" x-text="customer.alamat || '-'"></td>
                <td class="px-4 py-3" x-text="customer.tipe_nama"></td>
                <td class="px-4 py-3" x-text="customer.outlet_nama"></td>
                <td class="px-4 py-3 font-medium text-red-600" x-text="customer.total_piutang_formatted"></td>
                <td class="px-4 py-3">
                  <div class="flex gap-1">
                    <button @click="viewCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-slate-200 hover:bg-slate-50">
                      <i class='bx bx-show'></i>
                    </button>
                    @hasPermission('crm.pelanggan.update')
                    <button @click="editCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-50">
                      <i class='bx bx-edit'></i>
                    </button>
                    @endhasPermission
                    @hasPermission('crm.pelanggan.delete')
                    <button @click="deleteCustomer(customer.id_member)" 
                            class="px-2 py-1 text-xs rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>

            <tr x-show="customers.length === 0">
              <td colspan="10" class="px-4 py-12 text-center text-slate-500">
                <i class='bx bx-user-x text-5xl mb-2'></i>
                <p>Belum ada data pelanggan</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    {{-- Create/Edit Modal --}}
    <div x-show="showModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-20">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-6 z-10 my-4">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900" x-text="modalTitle">Tambah Pelanggan</h3>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          {{-- Tab Navigation --}}
          <div x-show="editMode" class="flex border-b border-slate-200 mb-6">
            <button type="button" @click="activeTab = 'info'" 
                    :class="activeTab === 'info' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
              <i class='bx bx-user'></i> Informasi
            </button>
            <button type="button" @click="activeTab = 'manifest'" 
                    :class="activeTab === 'manifest' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
              <i class='bx bx-id-card'></i> Manifest
            </button>
          </div>

          <form @submit.prevent="submitForm()">
            {{-- Tab Content: Informasi --}}
            <div x-show="activeTab === 'info'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet *</label>
                <input type="text" :value="getOutletName(formData.id_outlet)" readonly
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-600 cursor-not-allowed">
                <p class="text-xs text-slate-500 mt-1">Outlet disesuaikan dengan filter yang dipilih di halaman</p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Pelanggan *</label>
                <input type="text" x-model="formData.nama" required
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Perusahaan</label>
                <input type="text" x-model="formData.nama_perusahaan"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon *</label>
                <input type="text" x-model="formData.telepon" required
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Customer</label>
                <select x-model="formData.id_tipe"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                  <option value="">Pilih Tipe</option>
                  <template x-for="tipe in modalTipes" :key="tipe.id_tipe">
                    <option :value="tipe.id_tipe" x-text="tipe.nama_tipe"></option>
                  </template>
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                <textarea x-model="formData.alamat" rows="3"
                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
              </div>
            </div>

            {{-- Tab Content: Manifest --}}
            <div x-show="activeTab === 'manifest'" class="space-y-6">
              {{-- Pas Foto --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-user-circle'></i> Pas Foto
                </label>
                <div class="flex items-start gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-32 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                      <template x-if="formData.pas_foto_preview">
                        <img :src="formData.pas_foto_preview" class="w-full h-full object-cover">
                      </template>
                      <template x-if="!formData.pas_foto_preview">
                        <i class='bx bx-image text-4xl text-slate-400'></i>
                      </template>
                    </div>
                  </div>
                  <div class="flex-1">
                    <input type="file" @change="handlePasFotoUpload($event)" accept="image/*" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Max 2MB</p>
                  </div>
                </div>
              </div>

              {{-- Foto KTP --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-id-card'></i> Foto KTP
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.ktp_foto_preview">
                          <img :src="formData.ktp_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.ktp_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleKtpUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Max 2MB. Data akan diekstrak otomatis.</p>
                      <div x-show="ktpProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                  </div>
                  
                  {{-- KTP Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">NIK</label>
                      <input type="text" x-model="formData.ktp_nik" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nama</label>
                      <input type="text" x-model="formData.ktp_nama" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tempat Lahir</label>
                      <input type="text" x-model="formData.ktp_tempat_lahir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Lahir</label>
                      <input type="date" x-model="formData.ktp_tanggal_lahir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Alamat</label>
                      <textarea x-model="formData.ktp_alamat" rows="2"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Foto Passport --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-book'></i> Foto Passport
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.passport_foto_preview">
                          <img :src="formData.passport_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.passport_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handlePassportUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Max 2MB. Data akan diekstrak otomatis.</p>
                      <div x-show="passportProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                  </div>
                  
                  {{-- Passport Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Passport</label>
                      <input type="text" x-model="formData.passport_nomor" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nama</label>
                      <input type="text" x-model="formData.passport_nama" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Lahir</label>
                      <input type="date" x-model="formData.passport_tanggal_lahir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Kadaluarsa</label>
                      <input type="date" x-model="formData.passport_tanggal_kadaluarsa" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Kewarganegaraan</label>
                      <input type="text" x-model="formData.passport_kewarganegaraan" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Foto Visa --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-file-blank'></i> Foto Visa
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.visa_foto_preview">
                          <img :src="formData.visa_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.visa_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleVisaUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Max 2MB. Data akan diekstrak otomatis.</p>
                      <div x-show="visaProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                  </div>
                  
                  {{-- Visa Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Visa</label>
                      <input type="text" x-model="formData.visa_nomor" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tipe Visa</label>
                      <input type="text" x-model="formData.visa_tipe" 
                             placeholder="Tourist, Business, Umrah, dll"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Terbit</label>
                      <input type="date" x-model="formData.visa_tanggal_terbit" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Kadaluarsa</label>
                      <input type="date" x-model="formData.visa_tanggal_kadaluarsa" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Negara</label>
                      <input type="text" x-model="formData.visa_negara" 
                             placeholder="Saudi Arabia, UAE, dll"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Foto Tiket --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-plane-alt'></i> Foto Tiket Pesawat
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.tiket_foto_preview">
                          <img :src="formData.tiket_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.tiket_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleTiketUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, PDF. Max 2MB.</p>
                    </div>
                  </div>
                  
                  {{-- Tiket Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Tiket</label>
                      <input type="text" x-model="formData.tiket_nomor" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Maskapai</label>
                      <input type="text" x-model="formData.tiket_maskapai" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Rute</label>
                      <input type="text" x-model="formData.tiket_rute" 
                             placeholder="CGK - JED - CGK"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Berangkat</label>
                      <input type="datetime-local" x-model="formData.tiket_tanggal_berangkat" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Pulang</label>
                      <input type="datetime-local" x-model="formData.tiket_tanggal_pulang" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Foto Asuransi --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-shield'></i> Foto Asuransi Perjalanan
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.asuransi_foto_preview">
                          <img :src="formData.asuransi_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.asuransi_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleAsuransiUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, PDF. Max 2MB.</p>
                    </div>
                  </div>
                  
                  {{-- Asuransi Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Polis</label>
                      <input type="text" x-model="formData.asuransi_nomor_polis" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Provider</label>
                      <input type="text" x-model="formData.asuransi_provider" 
                             placeholder="Allianz, AXA, dll"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai</label>
                      <input type="date" x-model="formData.asuransi_tanggal_mulai" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Akhir</label>
                      <input type="date" x-model="formData.asuransi_tanggal_akhir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Foto Sertifikat Kesehatan --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-plus-medical'></i> Foto Sertifikat Kesehatan
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.sertifikat_kesehatan_foto_preview">
                          <img :src="formData.sertifikat_kesehatan_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!formData.sertifikat_kesehatan_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleSertifikatKesehatanUpload($event)" accept="image/*" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG. Max 2MB. Data akan diekstrak otomatis.</p>
                      <div x-show="sertifikatKesehatanProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                  </div>
                  
                  {{-- Sertifikat Kesehatan Form Fields --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Sertifikat</label>
                      <input type="text" x-model="formData.sertifikat_kesehatan_nomor" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Jenis</label>
                      <input type="text" x-model="formData.sertifikat_kesehatan_jenis" 
                             placeholder="Vaksinasi, Medical Check-up, dll"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Terbit</label>
                      <input type="date" x-model="formData.sertifikat_kesehatan_tanggal_terbit" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Kadaluarsa</label>
                      <input type="date" x-model="formData.sertifikat_kesehatan_tanggal_kadaluarsa" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Penerbit</label>
                      <input type="text" x-model="formData.sertifikat_kesehatan_penerbit" 
                             placeholder="Rumah Sakit, Klinik, dll"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Anggota Keluarga --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-slate-700">
                    <i class='bx bx-group'></i> Anggota Keluarga
                  </label>
                  <button type="button" @click="addFamilyMember()"
                          class="inline-flex items-center gap-1 text-xs rounded-lg bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1.5 hover:bg-primary-100">
                    <i class="bx bx-plus"></i> Tambah Anggota
                  </button>
                </div>

                <div x-show="formData.family_members.length === 0" class="text-center py-4 text-slate-400 text-sm">
                  Belum ada anggota keluarga. Klik "Tambah Anggota" untuk menambahkan.
                </div>

                <div class="space-y-3">
                  <template x-for="(member, idx) in formData.family_members" :key="idx">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                          <span class="text-xs font-semibold text-slate-600" x-text="'Anggota ' + (idx + 1)"></span>
                          <!-- Badge usia/kategori otomatis -->
                          <template x-if="member.tanggal_lahir">
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  :class="getFamilyMemberAgeCategory(member.tanggal_lahir).color"
                                  x-text="getFamilyMemberAgeCategory(member.tanggal_lahir).label"></span>
                          </template>
                        </div>
                        <button type="button" @click="removeFamilyMember(idx)"
                                class="p-1 rounded-lg text-red-500 hover:bg-red-50">
                          <i class="bx bx-trash text-sm"></i>
                        </button>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Nama <span class="text-red-500">*</span></label>
                          <input type="text" x-model="member.nama" required placeholder="Nama lengkap"
                                 class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Hubungan</label>
                          <select x-model="member.hubungan"
                                  class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Pilih Hubungan</option>
                            <option value="Suami">Suami</option>
                            <option value="Istri">Istri</option>
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Anak">Anak</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Lainnya">Lainnya</option>
                          </select>
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">NIK</label>
                          <input type="text" x-model="member.nik" maxlength="16" placeholder="16 digit (opsional)"
                                 class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Lahir</label>
                          <input type="date" x-model="member.tanggal_lahir" @change="$forceUpdate()"
                                 class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">No. Passport</label>
                          <input type="text" x-model="member.no_passport" placeholder="Opsional"
                                 class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Telepon</label>
                          <input type="text" x-model="member.telepon" placeholder="Opsional"
                                 class="w-full px-3 py-1.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                      </div>
                      <!-- Info harga otomatis -->
                      <template x-if="member.tanggal_lahir">
                        <div class="mt-2 text-xs text-slate-500 bg-white rounded-lg px-2 py-1 border border-slate-200">
                          <i class="bx bx-info-circle"></i>
                          <span x-text="getFamilyMemberAgeCategory(member.tanggal_lahir).priceInfo"></span>
                        </div>
                      </template>
                    </div>
                  </template>
                </div>
              </div>

              {{-- Jamaah Information --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-slate-700">
                    <i class='bx bx-mosque'></i> Data Jamaah
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="formData.is_jamaah" 
                           class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                    <span class="text-sm text-slate-600">Tandai sebagai Jamaah</span>
                  </label>
                </div>

                <div x-show="formData.is_jamaah" x-transition class="space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tipe Jamaah</label>
                      <select x-model="formData.jamaah_type" 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Tipe</option>
                        <option value="hajj">Hajj</option>
                        <option value="umrah">Umrah</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Jenis Kelamin</label>
                      <select x-model="formData.gender" 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Preferensi Kamar</label>
                      <select x-model="formData.room_preference" 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Preferensi</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="triple">Triple</option>
                        <option value="quad">Quad</option>
                      </select>
                    </div>
                  </div>

                  {{-- Mahram Information --}}
                  <div class="border-t border-slate-200 pt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                      <i class='bx bx-user-check'></i> Informasi Mahram
                      <span class="text-xs text-slate-500 font-normal">(Wajib untuk jamaah perempuan di bawah 45 tahun)</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nama Mahram</label>
                        <input type="text" x-model="formData.mahram_name" 
                               class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Hubungan</label>
                        <input type="text" x-model="formData.mahram_relationship" 
                               placeholder="Contoh: Suami, Ayah, Saudara"
                               class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Telepon Mahram</label>
                        <input type="text" x-model="formData.mahram_phone" 
                               class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">NIK Mahram</label>
                        <input type="text" x-model="formData.mahram_ktp_nik" 
                               maxlength="16"
                               placeholder="16 digit"
                               class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                      </div>
                    </div>
                  </div>

                  {{-- Health and Emergency Contact --}}
                  <div class="border-t border-slate-200 pt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                      <i class='bx bx-health'></i> Kesehatan & Kontak Darurat
                    </label>
                    <div class="space-y-3">
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Kondisi Kesehatan</label>
                        <textarea x-model="formData.health_conditions" rows="2"
                                  placeholder="Riwayat penyakit, alergi, atau kondisi kesehatan khusus"
                                  class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Nama Kontak Darurat</label>
                          <input type="text" x-model="formData.emergency_contact_name" 
                                 class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Telepon Kontak Darurat</label>
                          <input type="text" x-model="formData.emergency_contact_phone" 
                                 class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-slate-600 mb-1">Hubungan</label>
                          <input type="text" x-model="formData.emergency_contact_relationship" 
                                 placeholder="Contoh: Istri, Anak"
                                 class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Permintaan Khusus</label>
                        <textarea x-model="formData.special_requests" rows="2"
                                  placeholder="Permintaan khusus terkait perjalanan"
                                  class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button type="button" @click="closeModal()" 
                      class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="loading"
                      class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!loading">Simpan</span>
                <span x-show="loading">Menyimpan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showDetailModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-20">
        <div class="fixed inset-0 bg-black opacity-50" @click="closeDetailModal()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full p-6 z-10 my-4">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-900">Detail Pelanggan</h3>
            <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <div x-show="detailData" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-slate-600">Kode Member</p>
                <p class="font-semibold" x-text="detailData?.kode_member || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Nama</p>
                <p class="font-semibold" x-text="detailData?.nama || '-'"></p>
              </div>
              <div class="col-span-2">
                <p class="text-sm text-slate-600">Nama Perusahaan</p>
                <p class="font-semibold" x-text="detailData?.nama_perusahaan || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Telepon</p>
                <p class="font-semibold" x-text="detailData?.telepon || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Tipe Customer</p>
                <p class="font-semibold" x-text="detailData?.tipe?.nama_tipe || '-'"></p>
              </div>
              <div class="col-span-2">
                <p class="text-sm text-slate-600">Alamat</p>
                <p class="font-semibold" x-text="detailData?.alamat || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Outlet</p>
                <p class="font-semibold" x-text="detailData?.outlet?.nama || '-'"></p>
              </div>
              <div>
                <p class="text-sm text-slate-600">Total Piutang</p>
                <p class="font-semibold text-red-600" x-text="formatRupiah(detailData?.total_piutang || 0)"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function customerManagement() {
      return {
        viewMode: 'grid', // default grid
        customers: [],
        outlets: @json($outlets),
        tipes: [], // Dynamic tipes based on selected outlets
        modalTipes: [], // Dynamic tipes for modal based on selected outlet
        selectedOutlets: [{{ $outlets->first()->id_outlet ?? '' }}],
        showOutletDropdown: false,
        showModal: false,
        showDetailModal: false,
        modalTitle: 'Tambah Pelanggan',
        loading: false,
        editMode: false,
        editId: null,
        activeTab: 'info',
        ktpProcessing: false,
        passportProcessing: false,
        visaProcessing: false,
        sertifikatKesehatanProcessing: false,
        filters: {
          tipe: 'all',
          search: ''
        },
        formData: {
          nama: '',
          nama_perusahaan: '',
          telepon: '',
          alamat: '',
          id_tipe: '',
          id_outlet: '',
          pas_foto: null,
          pas_foto_preview: null,
          ktp_foto: null,
          ktp_foto_preview: null,
          ktp_nik: '',
          ktp_nama: '',
          ktp_tempat_lahir: '',
          ktp_tanggal_lahir: '',
          ktp_alamat: '',
          passport_foto: null,
          passport_foto_preview: null,
          passport_nomor: '',
          passport_nama: '',
          passport_tanggal_lahir: '',
          passport_tanggal_kadaluarsa: '',
          passport_kewarganegaraan: '',
          // Jamaah-specific fields
          is_jamaah: false,
          jamaah_type: '',
          mahram_name: '',
          mahram_relationship: '',
          mahram_phone: '',
          mahram_ktp_nik: '',
          health_conditions: '',
          emergency_contact_name: '',
          emergency_contact_phone: '',
          emergency_contact_relationship: '',
          room_preference: '',
          special_requests: '',
          gender: '',
          family_members: []
        },
        detailData: null,
        statistics: {
          total_customers: 0,
          total_piutang: 0,
          customers_by_tipe: []
        },

        init() {
          console.log('🚀 Initializing Customer Management with outlets:', this.selectedOutlets);
          
          // Ensure outlet selection is properly set
          if (this.selectedOutlets.length === 0 && this.outlets.length > 0) {
            this.selectedOutlets = [this.outlets[0].id_outlet];
            console.log('🔧 Setting default outlet selection:', this.selectedOutlets);
          }
          
          this.loadTipes();
          this.loadData();
          this.loadStatistics();
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

        closeOutletDropdown() {
          // Only close dropdown when clicking outside, not when interacting with checkboxes
          this.showOutletDropdown = false;
        },

        async onOutletSelectionChange() {
          console.log('🔄 Outlet selection changed:', this.selectedOutlets);
          // Don't close dropdown automatically - let user continue selecting
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.loadTipes(),
              this.loadData(),
              this.loadStatistics()
            ]);
          }
        },

        loadTipes() {
          if (this.selectedOutlets.length === 0) {
            this.tipes = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          console.log('📋 Loading tipes for outlets:', this.selectedOutlets);

          fetch(`{{ route('admin.crm.pelanggan.tipes-by-outlets') }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.tipes = data.data;
                console.log('📈 Tipes loaded:', this.tipes.length, 'tipes');
                
                // Reset tipe filter if current selection is not available
                if (this.filters.tipe !== 'all') {
                  const tipeExists = this.tipes.some(tipe => tipe.id_tipe == this.filters.tipe);
                  if (!tipeExists) {
                    this.filters.tipe = 'all';
                    console.log('🔄 Reset tipe filter to "all" - selected tipe not available in current outlets');
                  }
                }
                
                // Reset form tipe if current selection is not available
                if (this.formData.id_tipe) {
                  const formTipeExists = this.tipes.some(tipe => tipe.id_tipe == this.formData.id_tipe);
                  if (!formTipeExists) {
                    this.formData.id_tipe = '';
                    console.log('🔄 Reset form tipe - selected tipe not available in current outlets');
                  }
                }
              }
            })
            .catch(err => {
              console.error('Error loading tipes:', err);
              this.tipes = [];
            });
        },

        loadData() {
          if (this.selectedOutlets.length === 0) {
            this.customers = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('tipe_filter', this.filters.tipe);
          params.append('search', this.filters.search);

          console.log('📊 Fetching customer data with outlets:', this.selectedOutlets);

          fetch(`{{ route("admin.crm.pelanggan.data") }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.data) {
                this.customers = data.data;
                console.log('📈 Customer data loaded:', this.customers.length, 'customers');
              }
            })
            .catch(err => console.error('Error loading data:', err));
        },

        loadStatistics() {
          if (this.selectedOutlets.length === 0) {
            this.statistics = {
              total_customers: 0,
              total_piutang: 0,
              customers_by_tipe: []
            };
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          console.log('📊 Loading statistics for outlets:', this.selectedOutlets);

          fetch(`{{ route('admin.crm.pelanggan.statistics') }}?${params}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.statistics = data.data;
                console.log('📈 Statistics loaded:', this.statistics);
              }
            })
            .catch(err => console.error('Error loading statistics:', err));
        },

        openCreateModal() {
          this.editMode = false;
          this.modalTitle = 'Tambah Pelanggan';
          this.activeTab = 'info';
          this.resetForm();
          
          // Set outlet from first selected outlet
          if (this.selectedOutlets.length > 0) {
            this.formData.id_outlet = this.selectedOutlets[0];
            this.onModalOutletChange();
          }
          
          this.showModal = true;
        },

        editCustomer(id) {
          this.editMode = true;
          this.editId = id;
          this.modalTitle = 'Edit Pelanggan';
          this.activeTab = 'info';
          this.loadCustomerData(id);
          this.showModal = true;
        },

        async onModalOutletChange() {
          console.log('🔄 Modal outlet changed to:', this.formData.id_outlet);
          
          if (!this.formData.id_outlet) {
            this.modalTipes = [];
            this.formData.id_tipe = ''; // Reset tipe selection
            return;
          }

          try {
            const params = new URLSearchParams();
            params.append('outlet_ids[]', this.formData.id_outlet);

            const response = await fetch(`{{ route('admin.crm.pelanggan.tipes-by-outlets') }}?${params}`);
            const data = await response.json();

            if (data.success) {
              this.modalTipes = data.data;
              console.log('📋 Modal tipes loaded:', this.modalTipes.length, 'tipes for outlet', this.formData.id_outlet);
              
              // Reset tipe selection if current selection is not available
              if (this.formData.id_tipe) {
                const tipeExists = this.modalTipes.some(tipe => tipe.id_tipe == this.formData.id_tipe);
                if (!tipeExists) {
                  this.formData.id_tipe = '';
                  console.log('🔄 Reset modal tipe selection - not available in selected outlet');
                }
              }
            } else {
              this.modalTipes = [];
              this.formData.id_tipe = '';
              console.error('Failed to load modal tipes:', data.message);
            }
          } catch (error) {
            console.error('Error loading modal tipes:', error);
            this.modalTipes = [];
            this.formData.id_tipe = '';
          }
        },

        async loadCustomerData(id) {
          try {
            const response = await fetch(`{{ url('admin/crm/pelanggan') }}/${id}`);
            const data = await response.json();
            
            if (data.success) {
              const customerIdTipe = data.data.id_tipe || '';
              
              this.formData = {
                nama: data.data.nama,
                nama_perusahaan: data.data.nama_perusahaan || '',
                telepon: data.data.telepon,
                alamat: data.data.alamat,
                id_tipe: '',
                id_outlet: data.data.id_outlet,
                pas_foto: null,
                pas_foto_preview: data.data.pas_foto ? `{{ url('storage') }}/${data.data.pas_foto}` : null,
                ktp_foto: null,
                ktp_foto_preview: data.data.ktp_foto ? `{{ url('storage') }}/${data.data.ktp_foto}` : null,
                ktp_nik: data.data.ktp_nik || '',
                ktp_nama: data.data.ktp_nama || '',
                ktp_tempat_lahir: data.data.ktp_tempat_lahir || '',
                ktp_tanggal_lahir: data.data.ktp_tanggal_lahir || '',
                ktp_alamat: data.data.ktp_alamat || '',
                passport_foto: null,
                passport_foto_preview: data.data.passport_foto ? `{{ url('storage') }}/${data.data.passport_foto}` : null,
                passport_nomor: data.data.passport_nomor || '',
                passport_nama: data.data.passport_nama || '',
                passport_tanggal_lahir: data.data.passport_tanggal_lahir || '',
                passport_tanggal_kadaluarsa: data.data.passport_tanggal_kadaluarsa || '',
                passport_kewarganegaraan: data.data.passport_kewarganegaraan || '',
                // Jamaah-specific fields
                is_jamaah: data.data.is_jamaah || false,
                jamaah_type: data.data.jamaah_type || '',
                mahram_name: data.data.mahram_name || '',
                mahram_relationship: data.data.mahram_relationship || '',
                mahram_phone: data.data.mahram_phone || '',
                mahram_ktp_nik: data.data.mahram_ktp_nik || '',
                health_conditions: data.data.health_conditions || '',
                emergency_contact_name: data.data.emergency_contact_name || '',
                emergency_contact_phone: data.data.emergency_contact_phone || '',
                emergency_contact_relationship: data.data.emergency_contact_relationship || '',
                room_preference: data.data.room_preference || '',
                special_requests: data.data.special_requests || '',
                gender: data.data.gender || '',
                family_members: Array.isArray(data.data.family_members) ? data.data.family_members : (data.data.family_members ? JSON.parse(data.data.family_members) : [])
              };
              
              // Load tipes for the selected outlet first
              await this.onModalOutletChange();
              
              // Set id_tipe after tipes are loaded
              this.$nextTick(() => {
                this.formData.id_tipe = customerIdTipe;
                console.log('✅ Tipe customer loaded:', customerIdTipe);
              });
            }
          } catch (err) {
            console.error('Error loading customer:', err);
          }
        },

        submitForm() {
          this.loading = true;
          const url = this.editMode 
            ? `{{ url('admin/crm/pelanggan') }}/${this.editId}`
            : '{{ route("admin.crm.pelanggan.store") }}';
          
          const method = this.editMode ? 'PUT' : 'POST';

          // Prepare FormData for file uploads
          const formData = new FormData();
          
          // Add basic fields
          Object.keys(this.formData).forEach(key => {
            if (key.includes('_preview')) return; // Skip preview fields
            
            // Handle boolean fields specially
            if (key === 'is_jamaah') {
              formData.append(key, this.formData[key] ? '1' : '0');
              return;
            }
            
            // Handle family_members as JSON
            if (key === 'family_members') {
              formData.append(key, JSON.stringify(this.formData[key] || []));
              return;
            }
            
            if (this.formData[key] !== null && this.formData[key] !== '') {
              if (key === 'pas_foto' || key === 'ktp_foto' || key === 'passport_foto') {
                if (this.formData[key] instanceof File) {
                  formData.append(key, this.formData[key]);
                }
              } else {
                formData.append(key, this.formData[key]);
              }
            }
          });

          // Add method for PUT requests
          if (method === 'PUT') {
            formData.append('_method', 'PUT');
          }

          fetch(url, {
            method: 'POST', // Always POST for FormData
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            this.loading = false;
            if (data.success) {
              alert(data.message);
              this.closeModal();
              this.loadData();
              this.loadStatistics();
            } else {
              // Show detailed validation errors
              if (data.errors) {
                let errorMessage = data.message + '\n\nDetail Error:\n';
                Object.keys(data.errors).forEach(field => {
                  errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                });
                alert(errorMessage);
              } else {
                alert(data.message || 'Terjadi kesalahan');
              }
              console.error('Validation errors:', data.errors);
            }
          })
          .catch(error => {
            this.loading = false;
            alert('Terjadi kesalahan saat mengirim data');
            console.error('Error:', error);
          });
        },

        handlePasFotoUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          // Validate file size (max 2MB)
          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          // Validate file type
          if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            event.target.value = '';
            return;
          }

          this.formData.pas_foto = file;
          
          // Create preview
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.pas_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);
        },

        async handleKtpUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          // Validate file size (max 2MB)
          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          // Validate file type
          if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            event.target.value = '';
            return;
          }

          this.formData.ktp_foto = file;
          
          // Create preview
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.ktp_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);

          // Process OCR
          await this.processKtpOcr(file);
        },

        async processKtpOcr(file) {
          this.ktpProcessing = true;

          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await fetch('{{ route("admin.crm.pelanggan.ocr.ktp") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const data = await response.json();

            if (data.success) {
              // Fill form with OCR data
              if (data.data.nik) this.formData.ktp_nik = data.data.nik;
              if (data.data.nama) this.formData.ktp_nama = data.data.nama;
              if (data.data.tempat_lahir) this.formData.ktp_tempat_lahir = data.data.tempat_lahir;
              if (data.data.tanggal_lahir) this.formData.ktp_tanggal_lahir = data.data.tanggal_lahir;
              if (data.data.alamat) this.formData.ktp_alamat = data.data.alamat;
              
              console.log('✅ KTP OCR berhasil:', data.data);
              
              // Show warning if OCR returned empty data
              if (!data.data.nik && !data.data.nama && !data.data.tempat_lahir) {
                if (data.data.error) {
                  alert('⚠️ ' + data.data.error);
                } else {
                  alert('⚠️ OCR tidak dapat mengekstrak data dari gambar. Silakan isi data secara manual.\n\nTips:\n- Pastikan foto KTP jelas dan tidak blur\n- Pastikan pencahayaan cukup\n- Foto harus tegak lurus (tidak miring)');
                }
              }
            } else {
              console.warn('⚠️ KTP OCR gagal:', data.message);
              alert('⚠️ ' + (data.message || 'Gagal memproses OCR'));
            }
          } catch (error) {
            console.error('Error processing KTP OCR:', error);
            alert('⚠️ Terjadi kesalahan saat memproses OCR. Silakan isi data secara manual.');
          } finally {
            this.ktpProcessing = false;
          }
        },

        async handlePassportUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          // Validate file size (max 2MB)
          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          // Validate file type
          if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            event.target.value = '';
            return;
          }

          this.formData.passport_foto = file;
          
          // Create preview
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.passport_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);

          // Process OCR
          await this.processPassportOcr(file);
        },

        async processPassportOcr(file) {
          this.passportProcessing = true;

          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await fetch('{{ route("admin.crm.pelanggan.ocr.passport") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const data = await response.json();

            if (data.success) {
              // Fill form with OCR data
              if (data.data.nomor) this.formData.passport_nomor = data.data.nomor;
              if (data.data.nama) this.formData.passport_nama = data.data.nama;
              if (data.data.tanggal_lahir) this.formData.passport_tanggal_lahir = data.data.tanggal_lahir;
              if (data.data.tanggal_kadaluarsa) this.formData.passport_tanggal_kadaluarsa = data.data.tanggal_kadaluarsa;
              if (data.data.kewarganegaraan) this.formData.passport_kewarganegaraan = data.data.kewarganegaraan;
              
              console.log('✅ Passport OCR berhasil:', data.data);
            } else {
              console.warn('⚠️ Passport OCR gagal:', data.message);
            }
          } catch (error) {
            console.error('Error processing Passport OCR:', error);
          } finally {
            this.passportProcessing = false;
          }
        },

        // Visa Upload Handler
        async handleVisaUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            event.target.value = '';
            return;
          }

          this.formData.visa_foto = file;
          
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.visa_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);

          await this.processVisaOcr(file);
        },

        async processVisaOcr(file) {
          this.visaProcessing = true;

          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await fetch('{{ route("admin.crm.pelanggan.ocr.visa") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const data = await response.json();

            if (data.success) {
              if (data.data.visa_nomor) this.formData.visa_nomor = data.data.visa_nomor;
              if (data.data.visa_tipe) this.formData.visa_tipe = data.data.visa_tipe;
              if (data.data.visa_tanggal_terbit) this.formData.visa_tanggal_terbit = data.data.visa_tanggal_terbit;
              if (data.data.visa_tanggal_kadaluarsa) this.formData.visa_tanggal_kadaluarsa = data.data.visa_tanggal_kadaluarsa;
              if (data.data.visa_negara) this.formData.visa_negara = data.data.visa_negara;
              
              console.log('✅ Visa OCR berhasil:', data.data);
            }
          } catch (error) {
            console.error('Error processing Visa OCR:', error);
          } finally {
            this.visaProcessing = false;
          }
        },

        // Tiket Upload Handler
        async handleTiketUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          this.formData.tiket_foto = file;
          
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.tiket_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);
        },

        // Asuransi Upload Handler
        async handleAsuransiUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          this.formData.asuransi_foto = file;
          
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.asuransi_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);
        },

        // Sertifikat Kesehatan Upload Handler
        async handleSertifikatKesehatanUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
          }

          if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            event.target.value = '';
            return;
          }

          this.formData.sertifikat_kesehatan_foto = file;
          
          const reader = new FileReader();
          reader.onload = (e) => {
            this.formData.sertifikat_kesehatan_foto_preview = e.target.result;
          };
          reader.readAsDataURL(file);

          await this.processSertifikatKesehatanOcr(file);
        },

        async processSertifikatKesehatanOcr(file) {
          this.sertifikatKesehatanProcessing = true;

          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await fetch('{{ route("admin.crm.pelanggan.ocr.sertifikat-kesehatan") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const data = await response.json();

            if (data.success) {
              if (data.data.nomor) this.formData.sertifikat_kesehatan_nomor = data.data.nomor;
              if (data.data.jenis) this.formData.sertifikat_kesehatan_jenis = data.data.jenis;
              if (data.data.tanggal_terbit) this.formData.sertifikat_kesehatan_tanggal_terbit = data.data.tanggal_terbit;
              if (data.data.tanggal_kadaluarsa) this.formData.sertifikat_kesehatan_tanggal_kadaluarsa = data.data.tanggal_kadaluarsa;
              if (data.data.penerbit) this.formData.sertifikat_kesehatan_penerbit = data.data.penerbit;
              
              console.log('✅ Sertifikat Kesehatan OCR berhasil:', data.data);
            }
          } catch (error) {
            console.error('Error processing Sertifikat Kesehatan OCR:', error);
          } finally {
            this.sertifikatKesehatanProcessing = false;
          }
        },

        viewCustomer(id) {
          fetch(`{{ url('admin/crm/pelanggan') }}/${id}`)
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                this.detailData = data.data;
                this.showDetailModal = true;
              }
            })
            .catch(err => console.error('Error viewing customer:', err));
        },

        deleteCustomer(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) return;

          fetch(`{{ url('admin/crm/pelanggan') }}/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              this.loadData();
              this.loadStatistics();
            } else {
              alert(data.message || 'Gagal menghapus pelanggan');
            }
          })
          .catch(err => {
            alert('Terjadi kesalahan');
            console.error('Error:', err);
          });
        },

        closeModal() {
          this.showModal = false;
          this.resetForm();
        },

        closeDetailModal() {
          this.showDetailModal = false;
          this.detailData = null;
        },

        resetForm() {
          this.formData = {
            nama: '',
            nama_perusahaan: '',
            telepon: '',
            alamat: '',
            id_tipe: '',
            id_outlet: '',
            pas_foto: null,
            pas_foto_preview: null,
            ktp_foto: null,
            ktp_foto_preview: null,
            ktp_nik: '',
            ktp_nama: '',
            ktp_tempat_lahir: '',
            ktp_tanggal_lahir: '',
            ktp_alamat: '',
            passport_foto: null,
            passport_foto_preview: null,
            passport_nomor: '',
            passport_nama: '',
            passport_tanggal_lahir: '',
            passport_tanggal_kadaluarsa: '',
            passport_kewarganegaraan: '',
            // Jamaah-specific fields
            is_jamaah: false,
            jamaah_type: '',
            mahram_name: '',
            mahram_relationship: '',
            mahram_phone: '',
            mahram_ktp_nik: '',
            health_conditions: '',
            emergency_contact_name: '',
            emergency_contact_phone: '',
            emergency_contact_relationship: '',
            room_preference: '',
            special_requests: '',
            gender: '',
            family_members: []
          };
          this.modalTipes = []; // Reset modal tipes
          this.activeTab = 'info';
        },

        addFamilyMember() {
          this.formData.family_members.push({
            nama: '', hubungan: '', nik: '', tanggal_lahir: '', no_passport: '', telepon: ''
          });
        },

        removeFamilyMember(idx) {
          this.formData.family_members.splice(idx, 1);
        },

        getFamilyMemberAgeCategory(tanggalLahir) {
          if (!tanggalLahir) return { label: '', color: '', priceInfo: '' };
          const birth = new Date(tanggalLahir);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          const m = today.getMonth() - birth.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
          
          if (age < 2) {
            return {
              label: 'Infant (' + age + ' th)',
              color: 'bg-pink-100 text-pink-700',
              priceInfo: 'Infant 0-2th: Flat Rp 18.000.000'
            };
          } else if (age <= 8) {
            return {
              label: 'Anak (' + age + ' th)',
              color: 'bg-yellow-100 text-yellow-700',
              priceInfo: 'Anak 2-8th: Diskon 15% dari harga paket yang dipilih'
            };
          } else {
            return {
              label: 'Dewasa (' + age + ' th)',
              color: 'bg-green-100 text-green-700',
              priceInfo: 'Dewasa: Harga penuh sesuai paket yang dipilih'
            };
          }
        },

        exportExcel() {
          if (this.selectedOutlets.length === 0) {
            alert('Pilih minimal satu outlet');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('tipe_filter', this.filters.tipe);
          
          window.location.href = `{{ route('admin.crm.pelanggan.export.excel') }}?${params}`;
        },

        exportPdf() {
          if (this.selectedOutlets.length === 0) {
            alert('Pilih minimal satu outlet');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('tipe_filter', this.filters.tipe);
          
          window.location.href = `{{ route('admin.crm.pelanggan.export.pdf') }}?${params}`;
        },

        importExcel(event) {
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          // Show loading
          const originalText = event.target.parentElement.querySelector('span').textContent;
          event.target.parentElement.querySelector('span').textContent = 'Mengimport...';
          event.target.parentElement.style.pointerEvents = 'none';

          fetch('{{ route("admin.crm.pelanggan.import.excel") }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            event.target.parentElement.querySelector('span').textContent = originalText;
            event.target.parentElement.style.pointerEvents = 'auto';
            event.target.value = ''; // Reset file input

            if (data.success) {
              let message = data.message;
              if (data.errors && data.errors.length > 0) {
                message += '\n\nError:\n' + data.errors.slice(0, 5).join('\n');
                if (data.errors.length > 5) {
                  message += '\n... dan ' + (data.errors.length - 5) + ' error lainnya';
                }
              }
              alert(message);
              this.loadData();
              this.loadStatistics();
            } else {
              alert(data.message || 'Gagal import data');
            }
          })
          .catch(error => {
            event.target.parentElement.querySelector('span').textContent = originalText;
            event.target.parentElement.style.pointerEvents = 'auto';
            event.target.value = '';
            alert('Terjadi kesalahan saat import');
            console.error('Error:', error);
          });
        },

        formatRupiah(amount) {
          return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },

        getOutletName(outletId) {
          const outlet = this.outlets.find(o => o.id_outlet === outletId);
          return outlet ? outlet.nama_outlet : '-';
        }
      }
    }
  </script>
</x-layouts.admin>
