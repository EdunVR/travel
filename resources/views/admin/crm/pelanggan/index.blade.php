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
            {{-- Sesuai dengan form manifest publik: Passport, KTP/Akta Lahir (usia), dokumen tambahan, anggota keluarga --}}
            <div x-show="activeTab === 'manifest'" class="space-y-6">

              {{-- Tandai sebagai Jamaah (paling atas) --}}
              <div class="border border-primary-200 bg-primary-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div>
                    <label class="block text-sm font-semibold text-primary-800">
                      <i class='bx bx-mosque'></i> Status Jamaah
                    </label>
                    <p class="text-xs text-primary-600 mt-0.5">Aktifkan untuk mengisi data manifest perjalanan</p>
                  </div>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="formData.is_jamaah" 
                           class="rounded border-slate-300 text-primary-600 focus:ring-primary-200 w-5 h-5">
                    <span class="text-sm font-semibold text-primary-700">Tandai sebagai Jamaah</span>
                  </label>
                </div>
              </div>

              {{-- Info usia jamaah --}}
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                <i class='bx bx-info-circle'></i>
                <strong>Catatan:</strong> Form KTP/Akta Lahir ditampilkan berdasarkan usia jamaah.
                Usia dihitung dari <strong>Tanggal Lahir KTP</strong> di bawah.
                Anak &lt; 17 tahun: KTP disembunyikan, Akta Lahir wajib.
              </div>

              {{-- Pas Foto (Opsional) --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-user-circle'></i> Pas Foto 4x6 <span class="text-xs text-slate-400">(opsional)</span>
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

              {{-- Foto KTP (hanya untuk dewasa >= 17 tahun) --}}
              <div class="border border-slate-200 rounded-lg p-4" x-show="getAgeFromKtp() === null || getAgeFromKtp() >= 17">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-id-card'></i> KTP <span class="text-red-500 text-xs">*</span>
                  <span class="text-xs text-slate-400 font-normal">(dewasa ≥ 17 tahun)</span>
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.ktp_foto_preview">
                          <img :src="formData.ktp_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="formData.ktp_foto_is_pdf">
                          <div class="text-center p-2">
                            <i class='bx bxs-file-pdf text-4xl text-red-500'></i>
                            <p class="text-xs text-slate-500 mt-1">PDF terupload</p>
                          </div>
                        </template>
                        <template x-if="!formData.ktp_foto_preview && !formData.ktp_foto_is_pdf">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                      <template x-if="(formData.ktp_foto_preview || formData.ktp_foto_is_pdf) && editMode">
                        <div class="flex gap-1 mt-2">
                          <a :href="formData.ktp_foto_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                          <button type="button" @click="deleteAdminDocument('ktp_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                        </div>
                      </template>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handleKtpUpload($event)" accept="image/*,.pdf" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, PDF. Max 5MB.</p>
                      <div x-show="ktpProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nama di KTP</label>
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
                      <input type="date" x-model="formData.ktp_tanggal_lahir" @change="$forceUpdate()"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Alamat KTP</label>
                      <textarea x-model="formData.ktp_alamat" rows="2"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Akta Lahir (hanya untuk anak < 17 tahun) --}}
              <div class="border border-orange-200 bg-orange-50 rounded-lg p-4" x-show="getAgeFromKtp() !== null && getAgeFromKtp() < 17">
                <div class="flex items-center gap-2 mb-3">
                  <i class='bx bx-file text-orange-600'></i>
                  <label class="block text-sm font-medium text-orange-800">
                    Akta Lahir <span class="text-red-500 text-xs">*</span>
                    <span class="text-xs text-orange-600 font-normal">(anak &lt; 17 tahun — KTP tidak diperlukan)</span>
                  </label>
                </div>
                <div class="bg-orange-100 border border-orange-300 rounded-lg p-2 mb-3 text-xs text-orange-800">
                  <i class='bx bx-info-circle'></i>
                  Jamaah ini berusia <strong x-text="getAgeFromKtp() + ' tahun'"></strong>. KTP tidak diperlukan, upload Akta Lahir sebagai gantinya.
                </div>
                <div class="flex items-start gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-48 h-32 border-2 border-dashed border-orange-300 rounded-lg overflow-hidden bg-white flex items-center justify-center">
                      <template x-if="formData.akta_lahir_preview">
                        <img :src="formData.akta_lahir_preview" class="w-full h-full object-cover">
                      </template>
                      <template x-if="!formData.akta_lahir_preview">
                        <i class='bx bx-file text-4xl text-orange-300'></i>
                      </template>
                    </div>
                  </div>
                  <div class="flex-1">
                    <input type="file" @change="handleAktaLahirUpload($event)" accept="image/*,.pdf" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, PDF. Max 5MB.</p>
                    <div class="mt-3">
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Lahir (dari Akta)</label>
                      <input type="date" x-model="formData.ktp_tanggal_lahir" @change="$forceUpdate()"
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Passport --}}
              <div class="border border-slate-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                  <i class='bx bx-book'></i> Passport <span class="text-red-500 text-xs">*</span>
                </label>
                <div class="space-y-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-48 h-32 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center">
                        <template x-if="formData.passport_foto_preview && !formData.passport_foto_preview.endsWith('.pdf')">
                          <img :src="formData.passport_foto_preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="formData.passport_foto_preview && formData.passport_foto_preview.endsWith('.pdf')">
                          <div class="text-center p-2">
                            <i class='bx bxs-file-pdf text-4xl text-red-500'></i>
                            <p class="text-xs text-slate-500 mt-1">PDF tersimpan</p>
                          </div>
                        </template>
                        <template x-if="!formData.passport_foto_preview">
                          <i class='bx bx-image text-4xl text-slate-400'></i>
                        </template>
                      </div>
                      <template x-if="formData.passport_foto_preview && editMode">
                        <div class="flex gap-1 mt-2">
                          <a :href="formData.passport_foto_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                          <button type="button" @click="deleteAdminDocument('passport_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                        </div>
                      </template>
                    </div>
                    <div class="flex-1">
                      <input type="file" @change="handlePassportUpload($event)" accept="image/*,.pdf" 
                             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                      <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, PDF. Max 5MB.</p>
                      <div x-show="passportProcessing" class="mt-2 text-sm text-primary-600">
                        <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Passport</label>
                      <input type="text" x-model="formData.passport_nomor" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Nama Lengkap (Full Name)</label>
                      <input type="text" x-model="formData.passport_nama" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                      <select x-model="formData.passport_title" 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Pilih --</option>
                        <option value="MR">MR</option>
                        <option value="MRS">MRS</option>
                        <option value="MS">MS</option>
                        <option value="MSTR">MSTR</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Gender</label>
                      <select x-model="formData.passport_gender" 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">-- Pilih --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tempat Lahir (Birth City)</label>
                      <input type="text" x-model="formData.passport_tempat_lahir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Lahir</label>
                      <input type="date" x-model="formData.passport_tanggal_lahir" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Terbit (Issued Date)</label>
                      <input type="date" x-model="formData.passport_tanggal_terbit" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Kadaluarsa <span class="text-red-500">*</span></label>
                      <input type="date" x-model="formData.passport_tanggal_kadaluarsa" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Kewarganegaraan</label>
                      <input type="text" x-model="formData.passport_kewarganegaraan" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600 mb-1">Kantor Penerbit (Office Issued)</label>
                      <input type="text" x-model="formData.passport_kantor_terbit" 
                             class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                  </div>
                </div>
              </div>

              {{-- Dokumen Tambahan (grid 2 kolom) --}}
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Kartu Keluarga --}}
                <div class="border border-slate-200 rounded-lg p-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    <i class='bx bx-group'></i> Kartu Keluarga <span class="text-red-500 text-xs">*</span>
                  </label>
                  <div class="w-full h-24 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center mb-2">
                    <template x-if="formData.kartu_keluarga_preview && !formData.kartu_keluarga_preview.endsWith('.pdf')">
                      <img :src="formData.kartu_keluarga_preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="formData.kartu_keluarga_preview && formData.kartu_keluarga_preview.endsWith('.pdf')">
                      <div class="text-center p-2"><i class='bx bxs-file-pdf text-3xl text-red-500'></i><p class="text-xs text-slate-500">PDF</p></div>
                    </template>
                    <template x-if="!formData.kartu_keluarga_preview">
                      <i class='bx bx-image text-3xl text-slate-400'></i>
                    </template>
                  </div>
                  <template x-if="formData.kartu_keluarga_preview && editMode">
                    <div class="flex gap-1 mb-2">
                      <a :href="formData.kartu_keluarga_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                      <button type="button" @click="deleteAdminDocument('kartu_keluarga_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                    </div>
                  </template>
                  <input type="file" @change="handleKartuKeluargaUpload($event); if(editMode) uploadAdminDocument('kartu_keluarga_foto', $event.target.files[0])" accept="image/*,.pdf" 
                         class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                {{-- Buku Nikah --}}
                <div class="border border-slate-200 rounded-lg p-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    <i class='bx bx-book-heart'></i> Buku Nikah <span class="text-xs text-slate-400">(jika menikah)</span>
                  </label>
                  <div class="w-full h-24 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center mb-2">
                    <template x-if="formData.buku_nikah_preview && !formData.buku_nikah_preview.endsWith('.pdf')">
                      <img :src="formData.buku_nikah_preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="formData.buku_nikah_preview && formData.buku_nikah_preview.endsWith('.pdf')">
                      <div class="text-center p-2"><i class='bx bxs-file-pdf text-3xl text-red-500'></i><p class="text-xs text-slate-500">PDF</p></div>
                    </template>
                    <template x-if="!formData.buku_nikah_preview">
                      <i class='bx bx-image text-3xl text-slate-400'></i>
                    </template>
                  </div>
                  <template x-if="formData.buku_nikah_preview && editMode">
                    <div class="flex gap-1 mb-2">
                      <a :href="formData.buku_nikah_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                      <button type="button" @click="deleteAdminDocument('buku_nikah_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                    </div>
                  </template>
                  <input type="file" @change="handleBukuNikahUpload($event); if(editMode) uploadAdminDocument('buku_nikah_foto', $event.target.files[0])" accept="image/*,.pdf" 
                         class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                {{-- Vaksin Meningitis --}}
                <div class="border border-slate-200 rounded-lg p-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    <i class='bx bx-injection'></i> Buku Kuning Vaksin <span class="text-xs text-slate-400">(opsional)</span>
                  </label>
                  <div class="w-full h-24 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center mb-2">
                    <template x-if="formData.vaksin_preview && !formData.vaksin_preview.endsWith('.pdf')">
                      <img :src="formData.vaksin_preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="formData.vaksin_preview && formData.vaksin_preview.endsWith('.pdf')">
                      <div class="text-center p-2"><i class='bx bxs-file-pdf text-3xl text-red-500'></i><p class="text-xs text-slate-500">PDF</p></div>
                    </template>
                    <template x-if="!formData.vaksin_preview">
                      <i class='bx bx-image text-3xl text-slate-400'></i>
                    </template>
                  </div>
                  <template x-if="formData.vaksin_preview && editMode">
                    <div class="flex gap-1 mb-2">
                      <a :href="formData.vaksin_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                      <button type="button" @click="deleteAdminDocument('vaksin_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                    </div>
                  </template>
                  <input type="file" @change="handleVaksinUpload($event); if(editMode) uploadAdminDocument('vaksin_foto', $event.target.files[0])" accept="image/*,.pdf" 
                         class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                {{-- BPJS --}}
                <div class="border border-slate-200 rounded-lg p-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    <i class='bx bx-card'></i> BPJS/KIS/ASKES <span class="text-xs text-slate-400">(opsional)</span>
                  </label>
                  <div class="w-full h-24 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden bg-slate-50 flex items-center justify-center mb-2">
                    <template x-if="formData.bpjs_preview && !formData.bpjs_preview.endsWith('.pdf')">
                      <img :src="formData.bpjs_preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="formData.bpjs_preview && formData.bpjs_preview.endsWith('.pdf')">
                      <div class="text-center p-2"><i class='bx bxs-file-pdf text-3xl text-red-500'></i><p class="text-xs text-slate-500">PDF</p></div>
                    </template>
                    <template x-if="!formData.bpjs_preview">
                      <i class='bx bx-image text-3xl text-slate-400'></i>
                    </template>
                  </div>
                  <template x-if="formData.bpjs_preview && editMode">
                    <div class="flex gap-1 mb-2">
                      <a :href="formData.bpjs_preview" target="_blank" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"><i class='bx bx-download'></i> Unduh</a>
                      <button type="button" @click="deleteAdminDocument('bpjs_foto')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"><i class='bx bx-trash'></i> Hapus</button>
                    </div>
                  </template>
                  <input type="file" @change="handleBpjsUpload($event); if(editMode) uploadAdminDocument('bpjs_foto', $event.target.files[0])" accept="image/*,.pdf" 
                         class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
              </div>
              {{-- Anggota Keluarga dengan Manifest --}}
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

                      {{-- Form Manifest Anggota Keluarga --}}
                      <div class="mt-3 border-t border-slate-200 pt-3">
                        <button type="button" @click="member.showManifest = !member.showManifest"
                                class="flex items-center gap-2 text-xs font-semibold text-primary-700 hover:text-primary-900">
                          <i class="bx" :class="member.showManifest ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                          <i class='bx bx-id-card'></i>
                          <span x-text="member.showManifest ? 'Sembunyikan Manifest' : 'Isi Manifest Dokumen'"></span>
                        </button>

                        <div x-show="member.showManifest" x-transition class="mt-3 space-y-3">
                          <!-- Passport Anggota -->
                          <div class="bg-white border border-slate-200 rounded-lg p-3">
                            <p class="text-xs font-semibold text-slate-700 mb-2"><i class='bx bx-book'></i> Passport</p>
                            <!-- Upload Passport -->
                            <div class="border-2 border-dashed border-slate-300 rounded-lg p-3 text-center mb-2 hover:border-primary-400 transition-all">
                              <input type="file" :id="'member_passport_foto_' + idx" accept="image/*,.pdf" class="hidden"
                                     @change="handleMemberPassportUpload($event, idx)">
                              <label :for="'member_passport_foto_' + idx" class="cursor-pointer">
                                <template x-if="member.passport_foto_preview">
                                  <div>
                                    <img :src="member.passport_foto_preview" class="h-16 mx-auto rounded object-cover mb-1">
                                    <div class="text-xs text-green-600 font-semibold">✅ Klik untuk ganti</div>
                                  </div>
                                </template>
                                <template x-if="!member.passport_foto_preview">
                                  <div>
                                    <i class='bx bx-cloud-upload text-2xl text-slate-400'></i>
                                    <div class="text-xs text-slate-500 mt-1">Upload Passport (OCR)</div>
                                  </div>
                                </template>
                              </label>
                              <div x-show="member.passportProcessing" class="text-xs text-primary-600 mt-1">
                                <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                              </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Nomor Passport</label>
                                <input type="text" x-model="member.passport_nomor" placeholder="A1234567"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Nama Lengkap</label>
                                <input type="text" x-model="member.passport_nama"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Title</label>
                                <select x-model="member.passport_title"
                                        class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                  <option value="">-- Pilih --</option>
                                  <option value="MR">MR</option>
                                  <option value="MRS">MRS</option>
                                  <option value="MS">MS</option>
                                  <option value="MSTR">MSTR</option>
                                </select>
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Gender</label>
                                <select x-model="member.passport_gender"
                                        class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                  <option value="">-- Pilih --</option>
                                  <option value="Male">Male</option>
                                  <option value="Female">Female</option>
                                </select>
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Tempat Lahir</label>
                                <input type="text" x-model="member.passport_tempat_lahir" placeholder="Kota"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Tgl Lahir</label>
                                <input type="date" x-model="member.passport_tanggal_lahir"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Tgl Terbit</label>
                                <input type="date" x-model="member.passport_tanggal_terbit"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Tgl Kadaluarsa <span class="text-red-500">*</span></label>
                                <input type="date" x-model="member.passport_tanggal_kadaluarsa"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Kewarganegaraan</label>
                                <input type="text" x-model="member.passport_kewarganegaraan" placeholder="Indonesia"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Kantor Penerbit</label>
                                <input type="text" x-model="member.passport_kantor_terbit" placeholder="Kantor Imigrasi"
                                       class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                              </div>
                            </div>
                          </div>

                          <!-- KTP atau Akta Lahir berdasarkan usia -->
                          <template x-if="!member.tanggal_lahir || getMemberAge(member.tanggal_lahir) >= 17">
                            <div class="bg-white border border-slate-200 rounded-lg p-3">
                              <p class="text-xs font-semibold text-slate-700 mb-2"><i class='bx bx-id-card'></i> KTP</p>
                              <!-- Upload KTP -->
                              <div class="border-2 border-dashed border-slate-300 rounded-lg p-3 text-center mb-2 hover:border-primary-400 transition-all">
                                <input type="file" :id="'member_ktp_foto_' + idx" accept="image/*,.pdf" class="hidden"
                                       @change="handleMemberKtpUpload($event, idx)">
                                <label :for="'member_ktp_foto_' + idx" class="cursor-pointer">
                                  <template x-if="member.ktp_foto_preview">
                                    <div>
                                      <img :src="member.ktp_foto_preview" class="h-16 mx-auto rounded object-cover mb-1">
                                      <div class="text-xs text-green-600 font-semibold">✅ Klik untuk ganti</div>
                                    </div>
                                  </template>
                                  <template x-if="!member.ktp_foto_preview">
                                    <div>
                                      <i class='bx bx-cloud-upload text-2xl text-slate-400'></i>
                                      <div class="text-xs text-slate-500 mt-1">Upload KTP (OCR)</div>
                                    </div>
                                  </template>
                                </label>
                                <div x-show="member.ktpProcessing" class="text-xs text-primary-600 mt-1">
                                  <i class='bx bx-loader-alt bx-spin'></i> Memproses OCR...
                                </div>
                              </div>
                              <div class="grid grid-cols-2 gap-2">
                                <div>
                                  <label class="block text-xs text-slate-500 mb-1">NIK (16 digit)</label>
                                  <input type="text" x-model="member.ktp_nik" maxlength="16" placeholder="16 digit"
                                         class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                </div>
                                <div>
                                  <label class="block text-xs text-slate-500 mb-1">Nama di KTP</label>
                                  <input type="text" x-model="member.ktp_nama"
                                         class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                </div>
                                <div>
                                  <label class="block text-xs text-slate-500 mb-1">Tempat Lahir</label>
                                  <input type="text" x-model="member.ktp_tempat_lahir"
                                         class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                </div>
                                <div>
                                  <label class="block text-xs text-slate-500 mb-1">Alamat KTP</label>
                                  <input type="text" x-model="member.ktp_alamat"
                                         class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-primary-500">
                                </div>
                              </div>
                            </div>
                          </template>

                          <template x-if="member.tanggal_lahir && getMemberAge(member.tanggal_lahir) < 17">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                              <p class="text-xs font-semibold text-orange-800 mb-2">
                                <i class='bx bx-file'></i> Akta Lahir
                                <span class="text-red-500">*</span>
                                <span class="font-normal text-orange-600">(anak &lt; 17 tahun)</span>
                              </p>
                              <div class="text-xs text-orange-700 mb-2">
                                Usia: <strong x-text="getMemberAge(member.tanggal_lahir) + ' tahun'"></strong> — KTP tidak diperlukan
                              </div>
                              <input type="file" @change="handleMemberAktaLahir($event, idx)" accept="image/*,.pdf"
                                     class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-orange-100 file:text-orange-700">
                              <template x-if="member.akta_lahir_preview">
                                <img :src="member.akta_lahir_preview" class="mt-2 h-16 rounded border border-orange-200 object-cover">
                              </template>
                            </div>
                          </template>

                          <!-- Dokumen tambahan anggota -->
                          <div class="bg-white border border-slate-200 rounded-lg p-3">
                            <p class="text-xs font-semibold text-slate-700 mb-2"><i class='bx bx-file-blank'></i> Dokumen Lainnya</p>
                            <div class="grid grid-cols-2 gap-2">
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Kartu Keluarga <span class="text-red-500">*</span></label>
                                <input type="file" @change="handleMemberKartuKeluarga($event, idx)" accept="image/*,.pdf"
                                       class="block w-full text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-primary-50 file:text-primary-700">
                                <template x-if="member.kartu_keluarga_preview">
                                  <img :src="member.kartu_keluarga_preview" class="mt-1 h-12 rounded border object-cover">
                                </template>
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Buku Nikah <span class="text-slate-400">(jika menikah)</span></label>
                                <input type="file" @change="handleMemberBukuNikah($event, idx)" accept="image/*,.pdf"
                                       class="block w-full text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-primary-50 file:text-primary-700">
                                <template x-if="member.buku_nikah_preview">
                                  <img :src="member.buku_nikah_preview" class="mt-1 h-12 rounded border object-cover">
                                </template>
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">Vaksin Meningitis <span class="text-slate-400">(opsional)</span></label>
                                <input type="file" @change="handleMemberVaksin($event, idx)" accept="image/*,.pdf"
                                       class="block w-full text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-primary-50 file:text-primary-700">
                                <template x-if="member.vaksin_preview">
                                  <img :src="member.vaksin_preview" class="mt-1 h-12 rounded border object-cover">
                                </template>
                              </div>
                              <div>
                                <label class="block text-xs text-slate-500 mb-1">BPJS/KIS <span class="text-slate-400">(opsional)</span></label>
                                <input type="file" @change="handleMemberBpjs($event, idx)" accept="image/*,.pdf"
                                       class="block w-full text-xs text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-primary-50 file:text-primary-700">
                                <template x-if="member.bpjs_preview">
                                  <img :src="member.bpjs_preview" class="mt-1 h-12 rounded border object-cover">
                                </template>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>

              {{-- Data Jamaah (tipe, gender, mahram, dll) - REMOVED per user request --}}
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
          passport_title: '',
          passport_gender: '',
          passport_tanggal_terbit: '',
          passport_kantor_terbit: '',
          passport_tempat_lahir: '',
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
          family_members: [],
          // Dokumen tambahan manifest
          akta_lahir: null,
          akta_lahir_preview: null,
          kartu_keluarga: null,
          kartu_keluarga_preview: null,
          buku_nikah: null,
          buku_nikah_preview: null,
          vaksin: null,
          vaksin_preview: null,
          bpjs: null,
          bpjs_preview: null,
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
                ktp_tanggal_lahir: data.data.ktp_tanggal_lahir ? data.data.ktp_tanggal_lahir.substring(0, 10) : '',
                ktp_alamat: data.data.ktp_alamat || '',
                passport_foto: null,
                passport_foto_preview: data.data.passport_foto ? `{{ url('storage') }}/${data.data.passport_foto}` : null,
                passport_nomor: data.data.passport_nomor || '',
                passport_nama: data.data.passport_nama || '',
                passport_tanggal_lahir: data.data.passport_tanggal_lahir || '',
                passport_tanggal_kadaluarsa: data.data.passport_tanggal_kadaluarsa || '',
                passport_kewarganegaraan: data.data.passport_kewarganegaraan || '',
                passport_title: data.data.passport_title || '',
                passport_gender: data.data.passport_gender || '',
                passport_tanggal_terbit: data.data.passport_tanggal_terbit || '',
                passport_kantor_terbit: data.data.passport_kantor_terbit || '',
                passport_tempat_lahir: data.data.passport_tempat_lahir || '',
                // Dokumen manifest tambahan
                akta_lahir: null,
                akta_lahir_preview: data.data.akta_lahir_foto ? `{{ url('storage') }}/${data.data.akta_lahir_foto}` : null,
                akta_lahir_foto_path: data.data.akta_lahir_foto || null,
                kartu_keluarga: null,
                kartu_keluarga_preview: data.data.kartu_keluarga_foto ? `{{ url('storage') }}/${data.data.kartu_keluarga_foto}` : null,
                kartu_keluarga_foto_path: data.data.kartu_keluarga_foto || null,
                buku_nikah: null,
                buku_nikah_preview: data.data.buku_nikah_foto ? `{{ url('storage') }}/${data.data.buku_nikah_foto}` : null,
                buku_nikah_foto_path: data.data.buku_nikah_foto || null,
                vaksin: null,
                vaksin_preview: data.data.vaksin_foto ? `{{ url('storage') }}/${data.data.vaksin_foto}` : null,
                vaksin_foto_path: data.data.vaksin_foto || null,
                bpjs: null,
                bpjs_preview: data.data.bpjs_foto ? `{{ url('storage') }}/${data.data.bpjs_foto}` : null,
                bpjs_foto_path: data.data.bpjs_foto || null,
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
                family_members: (function(fm) {
                  if (!fm) return [];
                  if (Array.isArray(fm)) return fm;
                  // Handle string (possibly double-encoded)
                  try {
                    var parsed = JSON.parse(fm);
                    if (Array.isArray(parsed)) return parsed;
                    // Double-encoded: parse again
                    if (typeof parsed === 'string') {
                      var parsed2 = JSON.parse(parsed);
                      return Array.isArray(parsed2) ? parsed2 : [];
                    }
                    return [];
                  } catch(e) { return []; }
                })(data.data.family_members)
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
              if (key === 'pas_foto' || key === 'ktp_foto' || key === 'passport_foto' || key === 'akta_lahir' || key === 'kartu_keluarga' || key === 'buku_nikah' || key === 'vaksin' || key === 'bpjs') {
                if (this.formData[key] instanceof File) {
                  // Map local field names to server field names
                  const fileFieldMap = {
                    'akta_lahir': 'akta_lahir_foto',
                    'kartu_keluarga': 'kartu_keluarga_foto',
                    'buku_nikah': 'buku_nikah_foto',
                    'vaksin': 'vaksin_foto',
                    'bpjs': 'bpjs_foto',
                  };
                  const serverKey = fileFieldMap[key] || key;
                  formData.append(serverKey, this.formData[key]);
                }
              } else if (!key.includes('_path')) {
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

          // Validate file size (max 5MB)
          if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB');
            event.target.value = '';
            return;
          }

          // Validate file type
          const isImage = file.type.startsWith('image/');
          const isPdf = file.type === 'application/pdf';
          if (!isImage && !isPdf) {
            alert('File harus berupa gambar (JPG/PNG) atau PDF');
            event.target.value = '';
            return;
          }

          this.formData.ktp_foto = file;
          
          // Create preview
          if (isPdf) {
            this.formData.ktp_foto_preview = null; // No image preview for PDF
            this.formData.ktp_foto_is_pdf = true;
          } else {
            this.formData.ktp_foto_is_pdf = false;
            const reader = new FileReader();
            reader.onload = (e) => {
              this.formData.ktp_foto_preview = e.target.result;
            };
            reader.readAsDataURL(file);
          }

          // Process OCR untuk gambar dan PDF (Google Vision support keduanya)
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

          // Validate file size (max 5MB)
          if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB');
            event.target.value = '';
            return;
          }

          // Validate file type
          const isImage = file.type.startsWith('image/');
          const isPdf = file.type === 'application/pdf';
          if (!isImage && !isPdf) {
            alert('File harus berupa gambar (JPG/PNG) atau PDF');
            event.target.value = '';
            return;
          }

          this.formData.passport_foto = file;
          
          // Create preview
          if (isPdf) {
            this.formData.passport_foto_preview = null; // No image preview for PDF
            this.formData.passport_foto_is_pdf = true;
          } else {
            this.formData.passport_foto_is_pdf = false;
            const reader = new FileReader();
            reader.onload = (e) => {
              this.formData.passport_foto_preview = e.target.result;
            };
            reader.readAsDataURL(file);
          }

          // Process OCR untuk gambar dan PDF (Google Vision support keduanya)
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
              if (data.data.title) this.formData.passport_title = data.data.title;
              if (data.data.gender) this.formData.passport_gender = data.data.gender;
              if (data.data.tanggal_terbit) this.formData.passport_tanggal_terbit = data.data.tanggal_terbit;
              if (data.data.kantor_terbit) this.formData.passport_kantor_terbit = data.data.kantor_terbit;
              if (data.data.tempat_lahir) this.formData.passport_tempat_lahir = data.data.tempat_lahir;
              
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
            // Data dasar
            nama: '', hubungan: '', nik: '', tanggal_lahir: '', no_passport: '', telepon: '',
            // Toggle manifest form
            showManifest: false,
            // Passport (lengkap)
            passport_nomor: '', passport_nama: '', passport_tanggal_lahir: '', passport_tanggal_kadaluarsa: '',
            passport_title: '', passport_gender: '', passport_tanggal_terbit: '',
            passport_kantor_terbit: '', passport_tempat_lahir: '', passport_kewarganegaraan: '',
            passport_foto: null, passport_foto_preview: null, passportProcessing: false,
            // KTP
            ktp_nik: '', ktp_nama: '', ktp_tempat_lahir: '', ktp_tanggal_lahir: '', ktp_alamat: '',
            ktp_foto: null, ktp_foto_preview: null, ktpProcessing: false,
            // Dokumen upload (file objects + previews)
            akta_lahir: null, akta_lahir_preview: null,
            kartu_keluarga: null, kartu_keluarga_preview: null,
            buku_nikah: null, buku_nikah_preview: null,
            vaksin: null, vaksin_preview: null,
            bpjs: null, bpjs_preview: null,
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

        // Hitung usia dari ktp_tanggal_lahir — digunakan untuk show/hide KTP vs Akta Lahir
        getAgeFromKtp() {
          const tgl = this.formData.ktp_tanggal_lahir;
          if (!tgl) return null;
          const birth = new Date(tgl);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          const m = today.getMonth() - birth.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
          return age;
        },

        // Hitung usia anggota keluarga
        getMemberAge(tanggalLahir) {
          if (!tanggalLahir) return null;
          const birth = new Date(tanggalLahir);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          const m = today.getMonth() - birth.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
          return age;
        },

        handleMemberAktaLahir(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].akta_lahir_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].akta_lahir = file;
        },

        async handleMemberPassportUpload(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].passport_foto_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].passport_foto = file;
          this.formData.family_members[idx].passportProcessing = true;
          const fd = new FormData();
          fd.append('image', file);
          try {
            const res = await fetch('{{ route("admin.crm.pelanggan.ocr.passport") }}', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
              body: fd
            });
            const data = await res.json();
            if (data.success) {
              const m = this.formData.family_members[idx];
              if (data.data.nomor) m.passport_nomor = data.data.nomor;
              if (data.data.nama) m.passport_nama = data.data.nama;
              if (data.data.tanggal_lahir) m.passport_tanggal_lahir = data.data.tanggal_lahir;
              if (data.data.tanggal_kadaluarsa) m.passport_tanggal_kadaluarsa = data.data.tanggal_kadaluarsa;
              if (data.data.kewarganegaraan) m.passport_kewarganegaraan = data.data.kewarganegaraan;
              if (data.data.title) m.passport_title = data.data.title;
              if (data.data.gender) m.passport_gender = data.data.gender;
              if (data.data.tanggal_terbit) m.passport_tanggal_terbit = data.data.tanggal_terbit;
              if (data.data.kantor_terbit) m.passport_kantor_terbit = data.data.kantor_terbit;
              if (data.data.tempat_lahir) m.passport_tempat_lahir = data.data.tempat_lahir;
            }
          } catch(e) {}
          this.formData.family_members[idx].passportProcessing = false;
        },

        async handleMemberKtpUpload(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].ktp_foto_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].ktp_foto = file;
          this.formData.family_members[idx].ktpProcessing = true;
          const fd = new FormData();
          fd.append('image', file);
          try {
            const res = await fetch('{{ route("admin.crm.pelanggan.ocr.ktp") }}', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
              body: fd
            });
            const data = await res.json();
            if (data.success) {
              const m = this.formData.family_members[idx];
              if (data.data.nik) m.ktp_nik = data.data.nik;
              if (data.data.nama) m.ktp_nama = data.data.nama;
              if (data.data.tempat_lahir) m.ktp_tempat_lahir = data.data.tempat_lahir;
              if (data.data.tanggal_lahir) m.ktp_tanggal_lahir = data.data.tanggal_lahir;
              if (data.data.alamat) m.ktp_alamat = data.data.alamat;
            }
          } catch(e) {}
          this.formData.family_members[idx].ktpProcessing = false;
        },

        handleMemberKartuKeluarga(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].kartu_keluarga_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].kartu_keluarga = file;
        },

        handleMemberBukuNikah(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].buku_nikah_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].buku_nikah = file;
        },

        handleMemberVaksin(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].vaksin_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].vaksin = file;
        },

        handleMemberBpjs(event, idx) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.family_members[idx].bpjs_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.family_members[idx].bpjs = file;
        },

        handleAktaLahirUpload(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.akta_lahir_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.akta_lahir = file;
        },

        handleKartuKeluargaUpload(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.kartu_keluarga_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.kartu_keluarga = file;
        },

        handleBukuNikahUpload(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.buku_nikah_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.buku_nikah = file;
        },

        handleVaksinUpload(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.vaksin_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.vaksin = file;
        },

        handleBpjsUpload(event) {
          const file = event.target.files[0];
          if (!file) return;
          const reader = new FileReader();
          reader.onload = (e) => { this.formData.bpjs_preview = e.target.result; };
          reader.readAsDataURL(file);
          this.formData.bpjs = file;
        },

        /**
         * Upload dokumen langsung ke server (admin)
         * docType: passport_foto, ktp_foto, akta_lahir_foto, kartu_keluarga_foto, buku_nikah_foto, vaksin_foto, bpjs_foto, pas_foto
         */
        async uploadAdminDocument(docType, file) {
          if (!this.editId || !file) return;
          const formData = new FormData();
          formData.append('file', file);
          formData.append('doc_type', docType);

          try {
            const res = await fetch(`{{ url('admin/crm/pelanggan') }}/${this.editId}/upload-doc`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              body: formData
            });
            const data = await res.json();
            if (data.success) {
              // Update preview based on docType
              const previewMap = {
                'passport_foto': 'passport_foto_preview',
                'ktp_foto': 'ktp_foto_preview',
                'pas_foto': 'pas_foto_preview',
                'akta_lahir_foto': 'akta_lahir_preview',
                'kartu_keluarga_foto': 'kartu_keluarga_preview',
                'buku_nikah_foto': 'buku_nikah_preview',
                'vaksin_foto': 'vaksin_preview',
                'bpjs_foto': 'bpjs_preview',
              };
              const pathMap = {
                'akta_lahir_foto': 'akta_lahir_foto_path',
                'kartu_keluarga_foto': 'kartu_keluarga_foto_path',
                'buku_nikah_foto': 'buku_nikah_foto_path',
                'vaksin_foto': 'vaksin_foto_path',
                'bpjs_foto': 'bpjs_foto_path',
              };
              if (previewMap[docType]) {
                this.formData[previewMap[docType]] = data.file_url;
              }
              if (pathMap[docType]) {
                this.formData[pathMap[docType]] = data.file_path;
              }
            } else {
              alert('Gagal upload: ' + (data.message || 'Error'));
            }
          } catch(e) {
            alert('Error jaringan saat upload');
          }
        },

        /**
         * Hapus dokumen dari server (admin)
         */
        async deleteAdminDocument(docType) {
          if (!this.editId) return;
          if (!confirm('Hapus dokumen ini?')) return;

          try {
            const res = await fetch(`{{ url('admin/crm/pelanggan') }}/${this.editId}/delete-doc`, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ doc_type: docType })
            });
            const data = await res.json();
            if (data.success) {
              const previewMap = {
                'passport_foto': 'passport_foto_preview',
                'ktp_foto': 'ktp_foto_preview',
                'pas_foto': 'pas_foto_preview',
                'akta_lahir_foto': 'akta_lahir_preview',
                'kartu_keluarga_foto': 'kartu_keluarga_preview',
                'buku_nikah_foto': 'buku_nikah_preview',
                'vaksin_foto': 'vaksin_preview',
                'bpjs_foto': 'bpjs_preview',
              };
              const pathMap = {
                'akta_lahir_foto': 'akta_lahir_foto_path',
                'kartu_keluarga_foto': 'kartu_keluarga_foto_path',
                'buku_nikah_foto': 'buku_nikah_foto_path',
                'vaksin_foto': 'vaksin_foto_path',
                'bpjs_foto': 'bpjs_foto_path',
              };
              if (previewMap[docType]) this.formData[previewMap[docType]] = null;
              if (pathMap[docType]) this.formData[pathMap[docType]] = null;
            } else {
              alert('Gagal hapus: ' + (data.message || 'Error'));
            }
          } catch(e) {
            alert('Error jaringan saat hapus');
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
