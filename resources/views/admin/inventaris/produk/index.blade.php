<x-layouts.admin :title="'Inventaris / Produk'">
  <div x-data="produkCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header & Actions -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Produk</h1>
        <p class="text-slate-600 text-sm">Kelola data produk dengan filter Outlet, Tipe, Status Stok & tampilan Grid/Tabel.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('inventaris.produk.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Produk
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.produk.export')
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        @endhasPermission
        
        @hasPermission('inventaris.produk.import')
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer" x-on:click.stop>
          <i class='bx bx-import text-lg'></i><span>Import Excel</span>
          <input type="file" class="hidden" accept=".xlsx,.xls,.csv" x-on:change="importExcel($event)">
        </label>
        <button x-on:click="downloadTemplate()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-download text-lg'></i> Template
        </button>
        @endhasPermission
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama, SKU, kategori…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-3">
          <div class="relative">
            <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-left focus:ring-2 focus:ring-primary-200 flex items-center justify-between">
              <span x-text="getSelectedOutletsText()"></span>
              <i class='bx bx-chevron-down' :class="showOutletDropdown ? 'rotate-180' : ''"></i>
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
                <template x-for="outlet in outletObjects" :key="outlet.id">
                  <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                    <input type="checkbox" :value="outlet.id" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                           class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                    <span class="text-sm" x-text="outlet.name"></span>
                  </label>
                </template>
              </div>
            </div>
          </div>
        </div>
        <div class="lg:col-span-3">
          <select x-model="typeFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Tipe</option>
            <option value="barang_dagang">Barang Dagang</option>
            <option value="jasa">Jasa</option>
            <option value="paket_travel">Paket Travel</option>
            <option value="produk_kustom">Produk Kustom</option>
          </select>
        </div>
        <div class="lg:col-span-2">
          <select x-model="stockFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Stok: Semua</option>
            <option value="READY">Tersedia (&gt; 0)</option>
            <option value="EMPTY">Habis (0)</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <div class="grid grid-cols-2 gap-2 lg:col-span-4">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="name">Nama</option>
            <option value="sku">SKU</option>
            <option value="stock">Stok</option>
            <option value="price">Harga</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option>
            <option value="desc">Turun</option>
          </select>
        </div>
        <div class="lg:col-span-2 lg:col-start-11">
          <div class="flex rounded-xl border border-slate-200 overflow-hidden">
            <button x-on:click="view='grid'"  :class="view==='grid'  ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Grid</button>
            <button x-on:click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Tabel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    <!-- GRID -->
    <div x-show="view==='grid' && !loading">
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="p in products" :key="p.id">
          <div class="group relative rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition overflow-hidden">
            <div class="absolute left-3 top-3 text-[10px] px-2 py-0.5 rounded-full bg-primary-50 text-primary-700" x-text="p.outlet"></div>
            <div class="absolute right-3 top-3 text-[10px] px-2 py-0.5 rounded-full" :class="p.stock>0?'bg-green-50 text-green-700':'bg-red-50 text-red-700'">
              <span x-text="p.stock>0?'Stok: '+p.stock:'Habis'"></span>
            </div>
            <div class="aspect-square w-full bg-slate-50 flex items-center justify-center overflow-hidden">
              <img :src="imgSrc(p)" x-on:error="$event.target.src = placeholder" class="w-3/4 h-3/4 object-contain" alt="">
            </div>
            <div class="p-4">
              <div class="text-[11px] text-slate-500 flex items-center justify-between">
                <span x-text="p.sku"></span>
                <span class="px-2 py-0.5 rounded-full bg-slate-50 border border-slate-200" x-text="p.type"></span>
              </div>
              <div class="font-semibold truncate mt-0.5" x-text="p.name"></div>
              <div class="mt-1 flex items-center justify-between">
                <div class="text-primary-700 font-semibold" x-text="formatCurrency(p.price)"></div>
                <div class="text-xs text-slate-500" x-text="p.unit"></div>
              </div>
              <div class="mt-3 flex gap-2">
                @hasPermission('inventaris.produk.update')
                <button x-on:click="openEdit(p)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm"><i class='bx bx-edit-alt'></i> Edit</button>
                @endhasPermission
                @hasPermission('inventaris.produk.hpp')
                <button x-on:click="openHppModal(p)" class="flex-1 rounded-lg border border-blue-200 text-blue-700 px-3 py-2 hover:bg-blue-50 text-sm"><i class='bx bx-line-chart'></i> HPP</button>
                @endhasPermission
                @hasPermission('inventaris.produk.delete')
                <button x-on:click="confirmDelete(p)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i> Hapus</button>
                @endhasPermission
              </div>
            </div>
          </div>
        </template>
      </div>
      <div x-show="products.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Gambar</th>
              <th class="text-left px-4 py-3">SKU</th>
              <th class="text-left px-4 py-3">Nama</th>
              <th class="text-left px-4 py-3">Tipe</th>
              <th class="text-left px-4 py-3">Kategori</th>
              <th class="text-left px-4 py-3">Satuan</th>
              <th class="text-right px-4 py-3">Stok</th>
              <th class="text-right px-4 py-3">Harga</th>
              <th class="px-4 py-3 text-right w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="p in products" :key="p.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="p.outlet"></td>
                <td class="px-4 py-3">
                  <img :src="imgSrc(p)" x-on:error="$event.target.src = placeholder" class="w-12 h-12 object-contain bg-slate-50 rounded-lg" alt="">
                </td>
                <td class="px-4 py-3 font-mono text-slate-600" x-text="p.sku"></td>
                <td class="px-4 py-3 font-medium" x-text="p.name"></td>
                <td class="px-4 py-3" x-text="p.type"></td>
                <td class="px-4 py-3" x-text="p.category"></td>
                <td class="px-4 py-3" x-text="p.unit"></td>
                <td class="px-4 py-3 text-right" x-text="p.stock"></td>
                <td class="px-4 py-3 text-right" x-text="formatCurrency(p.price)"></td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    @hasPermission('inventaris.produk.update')
                    <button x-on:click="openEdit(p)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    @endhasPermission
                    @hasPermission('inventaris.produk.hpp')
                    <button x-on:click="openHppModal(p)" class="inline-flex items-center gap-1 rounded-lg border border-blue-200 text-blue-700 px-3 py-1.5 hover:bg-blue-50"><i class='bx bx-line-chart'></i> HPP</button>
                    @endhasPermission
                    @hasPermission('inventaris.produk.delete')
                    <button x-on:click="confirmDelete(p)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="products.length===0"><td colspan="10" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="p in products" :key="p.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-start gap-3">
              <img :src="imgSrc(p)" x-on:error="$event.target.src = placeholder" class="w-16 h-16 object-contain bg-slate-50 rounded-lg">
              <div class="flex-1">
                <div class="text-[11px] text-slate-500" x-text="p.outlet + ' • ' + p.sku"></div>
                <div class="font-semibold" x-text="p.name"></div>
                <div class="text-sm text-slate-600" x-text="p.type + ' • ' + p.category + ' • ' + p.unit"></div>
                <div class="mt-1 font-semibold text-primary-700" x-text="formatCurrency(p.price)"></div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click="openEdit(p)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Edit</button>
              @hasPermission('inventaris.produk.hpp')
              <button x-on:click="openHppModal(p)" class="flex-1 rounded-lg border border-blue-200 text-blue-700 px-3 py-2 hover:bg-blue-50">HPP</button>
              @endhasPermission
              <button x-on:click="confirmDelete(p)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50">Hapus</button>
            </div>
          </div>
        </template>
        <div x-show="products.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
      </div>
    </div>

    <!-- MODAL WIZARD (RESPONSIVE) -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">

        <!-- Header -->
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Produk' : 'Tambah Produk'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <!-- Stepper -->
        <div class="px-4 sm:px-5 pt-3">
          <div class="grid grid-cols-4 gap-2 sm:gap-4">
            <template x-for="(s, i) in steps" :key="i">
              <div class="flex flex-col items-center text-center">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center border text-sm sm:text-base"
                     :class="currentStep === i ? 'bg-primary-600 text-white border-primary-600' : (i < currentStep ? 'bg-green-600 text-white border-green-600' : 'bg-white text-slate-500 border-slate-300')"
                     x-text="i+1"></div>
                <div class="mt-1 text-[11px] sm:text-xs leading-tight"
                     :class="currentStep === i ? 'text-primary-700 font-medium' : 'text-slate-500'"
                     x-text="s.title"></div>
              </div>
            </template>
          </div>
        </div>

        <!-- Body (scrollable) -->
        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <!-- STEP 1 - Informasi Produk -->
          <div x-show="currentStep === 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet" x-on:change="onFormOutletChange()" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Outlet</option>
                <template x-for="o in outlets" :key="o">
                  <option :value="o" x-text="o"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Tipe Produk <span class="text-red-500">*</span></label>
              <select x-model="form.type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Tipe</option>
                <option value="barang_dagang">Barang Dagang</option>
                <option value="jasa">Jasa</option>
                <option value="paket_travel">Paket Travel</option>
                <option value="produk_kustom">Produk Kustom</option>
              </select>
              <div x-show="errors.tipe_produk" class="text-red-500 text-xs mt-1" x-text="errors.tipe_produk"></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Kategori <span class="text-red-500">*</span></label>
              <select x-model="form.category" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Kategori</option>
                <template x-for="c in categories" :key="c">
                  <option :value="c" x-text="c"></option>
                </template>
              </select>
              <div x-show="errors.id_kategori" class="text-red-500 text-xs mt-1" x-text="errors.id_kategori"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Satuan <span class="text-red-500">*</span></label>
              <select x-model="form.unit" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Satuan</option>
                <template x-for="u in units" :key="u">
                  <option :value="u" x-text="u"></option>
                </template>
              </select>
              <div x-show="errors.id_satuan" class="text-red-500 text-xs mt-1" x-text="errors.id_satuan"></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">SKU <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.sku" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
              <div class="text-xs text-slate-500 mt-1">Kode produk digenerate otomatis</div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Produk <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" placeholder="Nama produk" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.nama_produk" class="text-red-500 text-xs mt-1" x-text="errors.nama_produk"></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Merk</label>
              <input type="text" x-model.trim="form.brand" placeholder="(opsional)" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Harga Jual <span class="text-red-500">*</span></label>
              <input type="number" min="0" step="1" x-model.number="form.price" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <p class="text-xs text-blue-600 mt-1" x-show="form.price > 0" 
                 x-text="'≈ ' + formatCurrency(form.price)"></p>
              <div x-show="errors.harga_jual" class="text-red-500 text-xs mt-1" x-text="errors.harga_jual"></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Diskon (%)</label>
              <input type="number" min="0" step="1" x-model.number="form.discount" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Stok</label>
              <div class="flex gap-2">
                <input type="number" min="0" x-model.number="form.stock" class="mt-1 flex-1 rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
                
                <button type="button" x-on:click="openAddStock()" class="mt-1 inline-flex items-center gap-1 rounded-xl bg-green-600 text-white px-3 py-2 hover:bg-green-700">
                  <i class='bx bx-plus'></i> Tambah
                </button>
              
              </div>
              <div class="text-xs text-slate-500 mt-1">Stok saat ini (read only). Gunakan tombol Tambah untuk menambah stok.</div>
            </div>

            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Spesifikasi/Deskripsi</label>
              <textarea x-model.trim="form.desc" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
            </div>

            <div class="sm:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" class="rounded border-slate-300">
                <span class="text-sm text-slate-700">Produk aktif</span>
              </label>
            </div>
          </div>

          <!-- STEP 2 - Gambar -->
          <div x-show="currentStep === 1" class="space-y-4">
            <div class="text-sm text-slate-600">Unggah sampai 4 gambar (JPG/PNG). Klik bintang untuk set gambar cover.</div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <template x-for="i in 4" :key="i">
                <div class="relative group">
                  <!-- Image Container -->
                  <label class="block aspect-square rounded-xl border border-dashed border-slate-300 hover:border-primary-400 cursor-pointer overflow-hidden bg-slate-50"
                         :class="form.images[i-1] ? 'border-solid' : ''">
                    <input type="file" class="hidden" accept="image/*" x-on:change="onPickImage($event, i-1)">
                    <template x-if="form.images[i-1]">
                      <img :src="typeof form.images[i-1] === 'string' ? form.images[i-1] : (form.images[i-1].url || form.images[i-1])" 
                           class="w-full h-full object-cover" 
                           x-on:error="console.error('Image load error:', form.images[i-1])"
                           x-on:load="console.log('Image loaded:', form.images[i-1])" />
                    </template>
                    <template x-if="!form.images[i-1]">
                      <div class="w-full h-full flex items-center justify-center text-slate-400 group-hover:text-primary-500">
                        <div class="text-center">
                          <i class='bx bx-image-alt text-3xl'></i>
                          <div class="text-xs mt-1">Klik untuk pilih</div>
                        </div>
                      </div>
                    </template>
                  </label>
                  
                  <!-- Action Buttons (only show when image exists) -->
                  <template x-if="form.images[i-1]">
                    <div class="absolute top-2 right-2 flex gap-1">
                      <!-- Set as Primary/Cover -->
                      <button type="button"
                              x-on:click.stop="setPrimaryImage(i-1)"
                              :class="form.primaryImageIndex === (i-1) ? 'bg-yellow-500 text-white' : 'bg-white/90 text-slate-600 hover:bg-yellow-500 hover:text-white'"
                              class="w-8 h-8 rounded-lg shadow-lg flex items-center justify-center transition"
                              :title="form.primaryImageIndex === (i-1) ? 'Gambar Cover' : 'Set sebagai Cover'">
                        <i class='bx bxs-star text-lg'></i>
                      </button>
                      
                      <!-- Delete Image -->
                      <button type="button"
                              x-on:click.stop="removeImage(i-1)"
                              class="w-8 h-8 rounded-lg bg-red-500 text-white shadow-lg hover:bg-red-600 flex items-center justify-center transition"
                              title="Hapus Gambar">
                        <i class='bx bx-trash text-lg'></i>
                      </button>
                    </div>
                  </template>
                  
                  <!-- Primary Badge -->
                  <template x-if="form.images[i-1] && form.primaryImageIndex === (i-1)">
                    <div class="absolute bottom-2 left-2 px-2 py-1 rounded-lg bg-yellow-500 text-white text-xs font-medium shadow-lg">
                      <i class='bx bxs-star'></i> Cover
                    </div>
                  </template>
                </div>
              </template>
            </div>
            <div class="text-xs text-slate-500">
              <i class='bx bx-info-circle'></i> Gambar pertama atau yang diberi bintang akan menjadi gambar cover produk.
            </div>
          </div>

          <!-- STEP 3 - Varian -->
          <div x-show="currentStep === 2" class="space-y-3">
            <div class="flex items-center gap-2">
              <input id="varianCheck" type="checkbox" x-model="form.hasVariant" class="rounded border-slate-300">
              <label for="varianCheck" class="text-sm text-slate-700">Produk memiliki varian (warna/ukuran/dll)</label>
            </div>

            <div x-show="form.hasVariant" class="rounded-xl border border-slate-200 overflow-hidden">
              <div class="bg-slate-50 px-3 sm:px-4 py-2 text-sm text-slate-600">Daftar Varian</div>
              <div class="p-3 sm:p-4 space-y-2">
                <!-- Header Tabel -->
                <div class="hidden md:grid grid-cols-6 gap-2 text-xs font-medium text-slate-600 pb-2 border-b border-slate-200">
                  <div class="col-span-2">Nama Varian</div>
                  <div>SKU Varian</div>
                  <div>Stok</div>
                  <div>Harga</div>
                  <div>Aksi</div>
                </div>
                
                <template x-for="(v, idx) in form.variants" :key="idx">
                  <div class="grid grid-cols-1 md:grid-cols-6 gap-2 items-center">
                    <input type="text" x-model.trim="v.name" placeholder="Nama Varian (Merah / L)" class="rounded-lg border border-slate-200 px-3 py-2 md:col-span-2">
                    <input type="text" x-model.trim="v.sku" placeholder="SKU Varian" class="rounded-lg border border-slate-200 px-3 py-2">
                    <input type="number" min="0" x-model.number="v.stock" placeholder="Stok" class="rounded-lg border border-slate-200 px-3 py-2">
                    <input type="number" min="0" step="1" x-model.number="v.price" placeholder="Harga" class="rounded-lg border border-slate-200 px-3 py-2">
                    <button class="rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50" x-on:click="removeVariant(idx)">
                      <i class='bx bx-trash'></i>
                    </button>
                  </div>
                </template>
                <button class="mt-1 inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50"
                        x-on:click="addVariant()">
                  <i class='bx bx-plus'></i> Tambah Varian
                </button>
              </div>
            </div>
            <div x-show="!form.hasVariant" class="text-sm text-slate-500">Centang di atas jika produk memiliki ukuran/warna/tipe berbeda.</div>
          </div>

          <!-- STEP 4 - Konfirmasi -->
          <div x-show="currentStep === 3" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="rounded-xl border border-slate-200 bg-white">
                <div class="aspect-square bg-slate-50 rounded-t-xl overflow-hidden flex items-center justify-center">
                  <!-- Display primary/cover image -->
                  <template x-if="form.images[form.primaryImageIndex || 0]">
                    <img :src="typeof form.images[form.primaryImageIndex || 0] === 'string' ? form.images[form.primaryImageIndex || 0] : (form.images[form.primaryImageIndex || 0].url || form.images[form.primaryImageIndex || 0])" 
                         class="w-full h-full object-cover"
                         x-on:error="console.error('Cover image load error')">
                  </template>
                  <template x-if="!form.images[form.primaryImageIndex || 0]">
                    <div class="text-slate-300">
                      <i class='bx bx-image-alt text-6xl'></i>
                      <div class="text-xs mt-2">Tidak ada gambar</div>
                    </div>
                  </template>
                </div>
                <div class="p-3">
                  <div class="font-semibold" x-text="form.name || '—'"></div>
                  <div class="text-sm text-slate-500" x-text="(form.type || '—') + ' • ' + (form.category || '—')"></div>
                  <div class="mt-1 text-primary-700 font-semibold" x-text="formatCurrency(form.price || 0)"></div>
                  <!-- Show cover badge if has images -->
                  <div x-show="form.images.length > 0" class="mt-2 inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-xs">
                    <i class='bx bxs-star'></i> Gambar Cover
                  </div>
                </div>
              </div>
              <div class="md:col-span-2 rounded-xl border border-slate-200 p-4">
                <div class="grid grid-cols-2 gap-2 text-sm">
                  <div class="text-slate-500">Outlet</div><div class="font-medium" x-text="form.outlet || '—'"></div>
                  <div class="text-slate-500">SKU</div><div class="font-mono text-slate-700" x-text="form.sku || '—'"></div>
                  <div class="text-slate-500">Satuan</div><div class="font-medium" x-text="form.unit || '—'"></div>
                  <div class="text-slate-500">Stok</div><div class="font-medium" x-text="form.stock || 0"></div>
                  <div class="text-slate-500">Diskon</div><div class="font-medium" x-text="(form.discount||0)+' %'"></div>
                  <div class="text-slate-500">Merk</div><div class="font-medium" x-text="form.brand || '—'"></div>
                </div>
                <div class="mt-3 text-sm">
                  <div class="text-slate-500">Spesifikasi</div>
                  <div class="mt-1" x-text="form.desc || '—'"></div>
                </div>
                <div class="mt-3" x-show="form.hasVariant">
                  <div class="text-slate-500 text-sm">Varian</div>
                  <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <template x-for="(v, idx) in form.variants" :key="idx">
                      <div class="rounded-lg border border-slate-200 p-2 text-sm">
                        <div class="font-medium" x-text="v.name"></div>
                        <div class="text-slate-500 font-mono" x-text="v.sku"></div>
                        <div class="flex items-center justify-between mt-1">
                          <span x-text="'Stok: '+(v.stock||0)"></span>
                          <span class="font-semibold text-primary-700" x-text="formatCurrency(v.price||0)"></span>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-between">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50"
                  x-on:click="prevStep()" x-bind:disabled="currentStep===0"
                  :class="currentStep===0 ? 'opacity-50 cursor-not-allowed' : ''">
            Kembali
          </button>

          <div class="flex items-center gap-2">
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>

            <button x-show="currentStep < steps.length-1"
                    class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700"
                    x-on:click="nextStep()">
              Lanjut
            </button>

            <button x-show="currentStep === steps.length-1"
                    x-on:click="submitWizard()" :disabled="saving"
                    class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
              <span x-show="saving" class="inline-flex items-center gap-2">
                <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
              </span>
              <span x-show="!saving">Simpan</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-lg rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Produk?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.sku"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="toDelete?.category + ' • ' + toDelete?.unit"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deleting" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
            </span>
            <span x-show="!deleting">Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
      <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
           class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
        <div class="flex items-center gap-2">
          <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
          <span x-text="toastMessage"></span>
        </div>
      </div>
    </div>

    <!-- Include HPP Modal -->
    @include('admin.inventaris.produk.hpp-modal')

    <!-- MODAL TAMBAH STOK -->
    <div x-show="showAddStockModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-start justify-center bg-black/40 p-3 pt-20 overflow-y-auto">
      <div x-on:click.outside="closeAddStock()" class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Tambah Stok</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeAddStock()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <!-- Body -->
        <div class="px-5 py-4 space-y-4">
          <div class="text-sm text-slate-600">
            Tambah stok untuk produk: <span class="font-medium" x-text="form.name"></span>
          </div>
          
          <div>
            <label class="text-sm text-slate-600">Jumlah Stok <span class="text-red-500">*</span></label>
            <input type="number" min="1" x-model.number="addStockForm.jumlah" 
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
                   placeholder="Masukkan jumlah stok">
            <div x-show="addStockErrors.jumlah" class="text-red-500 text-xs mt-1" x-text="addStockErrors.jumlah"></div>
          </div>

          <div>
            <label class="text-sm text-slate-600">HPP (Harga Pokok Penjualan) <span class="text-red-500">*</span></label>
            <input type="number" min="0" step="0.01" x-model.number="addStockForm.hpp" 
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" 
                   placeholder="Masukkan HPP per unit">
            <div class="text-xs text-slate-500 mt-1">HPP adalah harga beli atau biaya produksi per unit</div>
            <div x-show="addStockErrors.hpp" class="text-red-500 text-xs mt-1" x-text="addStockErrors.hpp"></div>
          </div>

          <div class="bg-slate-50 rounded-xl p-3">
            <div class="text-sm text-slate-600">Ringkasan:</div>
            <div class="mt-1 space-y-1 text-sm">
              <div class="flex justify-between">
                <span>Jumlah:</span>
                <span x-text="addStockForm.jumlah || 0"></span>
              </div>
              <div class="flex justify-between">
                <span>HPP per unit:</span>
                <span x-text="formatCurrency(addStockForm.hpp || 0)"></span>
              </div>
              <div class="flex justify-between font-medium border-t border-slate-200 pt-1">
                <span>Total Nilai:</span>
                <span x-text="formatCurrency((addStockForm.jumlah || 0) * (addStockForm.hpp || 0))"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-4 border-t border-slate-100 flex gap-2 justify-end">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" 
                  x-on:click="closeAddStock()">
            Batal
          </button>
          <button class="rounded-xl bg-green-600 text-white px-4 py-2 hover:bg-green-700" 
                  x-on:click="submitAddStock()" 
                  :disabled="addingStock"
                  :class="addingStock ? 'opacity-50 cursor-not-allowed' : ''">
            <span x-show="!addingStock">Tambah Stok</span>
            <span x-show="addingStock" class="flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function produkCrud(){
      return {
        // State management
        products: [],
        outlets: [],
        categories: [],
        units: [],
        loading: false,
        saving: false,
        deleting: false,

        // Add Stock Modal State
        showAddStockModal: false,
        addingStock: false,
        addStockForm: {
          jumlah: 0,
          hpp: 0
        },
        addStockErrors: {},

        idMappings: {
            outlets: {},
            categories: {},
            units: {},
            loaded: false,
            loading: false
        },
        
        // Filters and search
        search: '',
        outletFilter: 'ALL', // Keep for backward compatibility
        selectedOutlets: [],
        showOutletDropdown: false,
        outletObjects: [],
        typeFilter: 'ALL',
        stockFilter: 'ALL',
        sortKey: 'name',
        sortDir: 'asc',
        view: 'grid',
        
        // Form state
        showForm: false,
        currentStep: 0,
        steps: [
          { title: 'Informasi' },
          { title: 'Gambar' }, 
          { title: 'Varian' },
          { title: 'Konfirmasi' }
        ],
        form: { 
          id: null,
          outlet: '',
          type: '',
          sku: '',
          name: '',
          category: '',
          unit: '',
          stock: 0,
          price: 0,
          discount: 0,
          brand: '',
          desc: '',
          is_active: true,
          images: [],
          imageFiles: [],
          primaryImageIndex: 0,
          hasVariant: false,
          variants: []
        },
        errors: {},
        
        // Delete confirmation
        toDelete: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        placeholder: 'data:image/svg+xml;utf8,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512"><rect width="100%" height="100%" fill="#f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="Arial" font-size="28">No Image</text></svg>`),

        // Getter for cover image URL
        get coverImageUrl() {
          const primaryIndex = this.form.primaryImageIndex || 0;
          const image = this.form.images[primaryIndex];
          
          if (!image) return this.placeholder;
          
          // Handle both string and object format
          if (typeof image === 'string') {
            return image;
          } else {
            return image.url || image;
          }
        },

        async init(){
          // Parallel loading semua data termasuk ID mappings
          try {
            await Promise.all([
              this.loadIdMappings(),
              this.fetchData(),
              this.fetchOutlets(),
              this.fetchCategories(), 
              this.fetchUnits()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              type_filter: this.typeFilter,
              stock_filter: this.stockFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            // Add selected outlets as array
            if (this.selectedOutlets.length > 0) {
              this.selectedOutlets.forEach(outletId => {
                params.append('outlet_ids[]', outletId);
              });
            }

            const response = await fetch(`{{ route('admin.inventaris.produk.data') }}?${params}`);
            const data = await response.json();
            
            this.products = data.data.map(item => ({
              id: item.id_produk || item.id,
              outlet: item.outlet,
              image: item.image || this.placeholder,
              sku: item.sku || item.kode_produk,
              name: item.name || item.nama_produk,
              type: item.type || item.tipe_produk,
              category: item.category || item.nama_kategori,
              unit: item.unit || item.nama_satuan,
              stock: item.stock || item.hpp_produk_sum_stok || 0,
              price: item.price || item.harga_jual || 0,
              discount: item.discount || item.diskon || 0,
              brand: item.brand || item.merk || '',
              desc: item.desc || item.spesifikasi || '',
              is_active: item.is_active !== undefined ? item.is_active : true
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchOutlets(){
          try {
            const response = await fetch('{{ route("admin.inventaris.produk.outlets") }}');
            const data = await response.json();
            this.outlets = data; // Keep for backward compatibility
            
            // Create outlet objects for checkbox system
            this.outletObjects = data.map(outletName => {
              // Assuming outlet names are unique identifiers
              // You may need to adjust this based on your actual data structure
              return {
                id: outletName,
                name: outletName
              };
            });
            
            // Initialize with all outlets selected
            this.selectedOutlets = this.outletObjects.map(o => o.id);
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async fetchCategories(){
          try {
            const response = await fetch('{{ route("admin.inventaris.produk.categories") }}');
            const data = await response.json();
            this.categories = data;
          } catch (error) {
            console.error('Error fetching categories:', error);
          }
        },

        async fetchUnits(){
          try {
            const response = await fetch('{{ route("admin.inventaris.produk.units") }}');
            const data = await response.json();
            this.units = data;
          } catch (error) {
            console.error('Error fetching units:', error);
          }
        },

        async onOutletChange() {
          // Reset type filter saat outlet berubah
          this.typeFilter = 'ALL';
          
          // Fetch data produk
          await this.fetchData();
        },

        // Checkbox outlet management functions
        getSelectedOutletsText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outletObjects.find(o => o.id === this.selectedOutlets[0]);
            return outlet ? outlet.name : 'Unknown';
          } else if (this.selectedOutlets.length === this.outletObjects.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Dipilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outletObjects.map(o => o.id);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        onOutletSelectionChange() {
          // Reset type filter when outlet selection changes
          this.typeFilter = 'ALL';
          // Dropdown stays open for better UX
          this.fetchData();
        },

        async onFormOutletChange() {
          // Reset form type saat outlet berubah
          this.form.type = '';
        },

        formatCurrency(n){ 
          return 'Rp '+Number(n||0).toLocaleString('id-ID'); 
        },

        imgSrc(p){ 
          // Handle both base64 images and URL images
          if (p.image && p.image.startsWith('data:')) {
            return p.image;
          }
          if (p.image && p.image.includes('<img')) {
            // Extract src from HTML img tag
            const match = p.image.match(/src="([^"]*)"/);
            return match ? match[1] : this.placeholder;
          }
          return p.image || this.placeholder; 
        },

        async openCreate(){ 
            try {
                ModalLoader.show();
                
                // Pastikan mappings sudah terload sebelum buka form
                if (!this.idMappings.loaded) {
                    await this.loadIdMappings();
                }
                
                const response = await fetch('{{ route("admin.inventaris.produk.generate-sku") }}');
                const data = await response.json();
                
                this.form = { 
                    id: null,
                    outlet: '',
                    type: '',
                    sku: data.sku,
                    name: '',
                    category: '',
                    unit: '',
                    stock: 0,
                    price: 0,
                    discount: 0,
                    brand: '',
                    desc: '',
                    is_active: true,
                    images: [],
                    imageFiles: [],
                    primaryImageIndex: 0,
                    hasVariant: false,
                    variants: []
                }; 
            } catch (error) {
                console.error('Error generating SKU:', error);
                this.form.sku = '';
            } finally {
                ModalLoader.hide();
            }
            this.errors = {};
            this.currentStep = 0;
            this.showForm = true; 
        },

        async openEdit(p){ 
          try {
            // Extract product ID safely
            const productId = typeof p.id === 'object' ? p.id.id : p.id;
            
            console.log('openEdit - fetching product:', productId);
            
            // Retry mechanism for session failures
            let response;
            let retryCount = 0;
            const maxRetries = 2;
            
            while (retryCount <= maxRetries) {
              try {
                response = await fetch(`{{ route('admin.inventaris.produk.show', '') }}/${productId}`);
                
                if (response.ok) {
                  break; // Success, exit retry loop
                }
                
                // If 401/403/500, retry
                if ([401, 403, 500].includes(response.status) && retryCount < maxRetries) {
                  console.warn(`Retry ${retryCount + 1}/${maxRetries} due to status ${response.status}`);
                  retryCount++;
                  await new Promise(resolve => setTimeout(resolve, 500)); // Wait 500ms before retry
                  continue;
                }
                
                throw new Error(`HTTP error! status: ${response.status}`);
              } catch (fetchError) {
                if (retryCount < maxRetries) {
                  console.warn(`Retry ${retryCount + 1}/${maxRetries} due to error:`, fetchError);
                  retryCount++;
                  await new Promise(resolve => setTimeout(resolve, 500));
                  continue;
                }
                throw fetchError;
              }
            }
            
            const data = await response.json();
            
            console.log('openEdit - received data:', data);
            
            // Transform images to match expected format
            const images = (data.images || []).map(img => {
              if (typeof img === 'string') {
                // Old format: just URL string
                return img;
              } else {
                // New format: object with id, url, is_primary
                return {
                  id: img.id,
                  url: img.url,
                  is_primary: img.is_primary
                };
              }
            });
            
            // Ensure id is a number, not an object
            const finalProductId = typeof data.id === 'object' ? data.id.id : data.id;
            
            console.log('openEdit - processed images:', images);
            
            this.form = { 
              id: parseInt(finalProductId),
              outlet: data.outlet,
              type: data.type,
              sku: data.sku,
              name: data.name,
              category: data.category,
              unit: data.unit,
              stock: data.stock,
              price: data.price,
              discount: data.discount,
              brand: data.brand,
              desc: data.desc,
              is_active: data.is_active,
              images: images,
              imageFiles: [],
              primaryImageIndex: data.primaryImageIndex || 0,
              hasVariant: data.variants && data.variants.length > 0,
              variants: data.variants || []
            }; 
            
            console.log('openEdit - form set:', this.form);
          } catch (error) {
            console.error('Error fetching product details:', error);
            this.showToastMessage('Gagal memuat data produk: ' + error.message, 'error');
            return;
          }
          
          this.errors = {};
          this.currentStep = 0;
          this.showForm = true; 
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
          this.currentStep = 0;
        },

        // Add Stock Modal Methods
        openAddStock() {
          if (!this.form.id) {
            this.showToastMessage('Simpan produk terlebih dahulu sebelum menambah stok', 'error');
            return;
          }
          
          this.addStockForm = {
            jumlah: 0,
            hpp: 0
          };
          this.addStockErrors = {};
          this.showAddStockModal = true;
        },

        closeAddStock() {
          this.showAddStockModal = false;
          this.addStockForm = {
            jumlah: 0,
            hpp: 0
          };
          this.addStockErrors = {};
        },

        async submitAddStock() {
          this.addingStock = true;
          this.addStockErrors = {};

          try {
            // Validasi
            if (!this.addStockForm.jumlah || this.addStockForm.jumlah <= 0) {
              this.addStockErrors.jumlah = 'Jumlah stok harus lebih dari 0';
              this.addingStock = false;
              return;
            }

            if (!this.addStockForm.hpp || this.addStockForm.hpp <= 0) {
              this.addStockErrors.hpp = 'HPP harus lebih dari 0';
              this.addingStock = false;
              return;
            }

            const response = await fetch(`{{ url('admin/inventaris/produk') }}/${this.form.id}/add-stock`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                jumlah: this.addStockForm.jumlah,
                hpp: this.addStockForm.hpp
              })
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Stok berhasil ditambahkan', 'success');
              this.closeAddStock();
              
              // Update stok di form dan refresh data
              this.form.stock = result.new_stock || (this.form.stock + this.addStockForm.jumlah);
              await this.fetchData();
            } else {
              if (result.errors) {
                this.addStockErrors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Gagal menambah stok', 'error');
              }
            }
          } catch (error) {
            console.error('Error adding stock:', error);
            this.showToastMessage('Terjadi kesalahan saat menambah stok', 'error');
          } finally {
            this.addingStock = false;
          }
        },

        nextStep(){ 
          if (this.currentStep === 0) {
            // Validate required fields in step 1
            if (!this.form.outlet || !this.form.type || !this.form.name || !this.form.category || !this.form.unit || !this.form.price) {
              this.showToastMessage('Harap lengkapi semua field yang wajib diisi', 'error');
              return;
            }
          }
          if (this.currentStep < this.steps.length - 1) {
            this.currentStep++; 
          }
        },

        prevStep(){ 
          if (this.currentStep > 0) {
            this.currentStep--; 
          }
        },

        async submitWizard(){
            this.saving = true;
            this.errors = {};

            try {
                // Validasi: pastikan mappings sudah terload
                if (!this.idMappings.loaded) {
                    this.showToastMessage('Sedang memuat data sistem, silakan coba lagi dalam beberapa detik', 'error');
                    this.saving = false;
                    return;
                }

                // Validasi: pastikan ID yang diperlukan ada
                const outletId = this.getOutletId(this.form.outlet);
                const categoryId = this.getCategoryId(this.form.category);
                const unitId = this.getUnitId(this.form.unit);

                console.log('Form data:', this.form);
                console.log('Resolved IDs:', { outletId, categoryId, unitId });

                if (!outletId) {
                    this.showToastMessage('Outlet tidak valid atau tidak ditemukan. Silakan pilih outlet dari dropdown.', 'error');
                    this.saving = false;
                    return;
                }

                if (!categoryId) {
                    this.showToastMessage('Kategori tidak valid atau tidak ditemukan. Silakan pilih kategori dari dropdown.', 'error');
                    this.saving = false;
                    return;
                }

                if (!unitId) {
                    this.showToastMessage('Satuan tidak valid atau tidak ditemukan. Silakan pilih satuan dari dropdown.', 'error');
                    this.saving = false;
                    return;
                }

                const url = this.form.id 
                    ? `{{ route('admin.inventaris.produk.update', '') }}/${this.form.id}`
                    : '{{ route("admin.inventaris.produk.store") }}';

                // Prepare form data
                const formData = new FormData();
                
                // Add _method for PUT request (Laravel method spoofing)
                if (this.form.id) {
                    formData.append('_method', 'PUT');
                }
                
                // Basic product info - pastikan tipe data benar
                formData.append('nama_produk', this.form.name);
                formData.append('id_outlet', outletId.toString());
                formData.append('tipe_produk', this.form.type);
                formData.append('id_kategori', categoryId.toString());
                formData.append('id_satuan', unitId.toString());
                formData.append('harga_jual', this.form.price.toString());
                formData.append('diskon', (this.form.discount || 0).toString());
                formData.append('merk', this.form.brand || '');
                formData.append('spesifikasi', this.form.desc || '');
                formData.append('stok', (this.form.stock || 0).toString());
                formData.append('is_active', this.form.is_active ? '1' : '0');

                // Debug: log formData contents
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }

                // Handle images - use imageFiles array which contains File objects
                if (this.form.imageFiles && this.form.imageFiles.length > 0) {
                    this.form.imageFiles.forEach((file, index) => {
                        if (file instanceof File) {
                            formData.append(`images[${index}]`, file);
                        }
                    });
                }

                // Handle variants
                if (this.form.hasVariant && this.form.variants.length > 0) {
                    this.form.variants.forEach((variant, index) => {
                        formData.append(`variants[${index}][name]`, variant.name);
                        formData.append(`variants[${index}][sku]`, variant.sku);
                        formData.append(`variants[${index}][price]`, variant.price.toString());
                        formData.append(`variants[${index}][stock]`, (variant.stock || 0).toString());
                    });
                }

                const response = await fetch(url, {
                    method: 'POST', // Always use POST, Laravel will handle _method
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                console.log('Server response:', response.status, result);

                if (response.ok) {
                    this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
                    this.closeForm();
                    await this.fetchData();
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                        console.log('Validation errors:', result.errors);
                        this.showToastMessage('Terdapat kesalahan dalam pengisian form', 'error');
                    } else {
                        console.log('Server error:', result);
                        this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
                    }
                }
            } catch (error) {
                console.error('Error saving data:', error);
                this.showToastMessage('Gagal menyimpan data: ' + error.message, 'error');
            } finally {
                this.saving = false;
            }
        },

        async loadIdMappings() {
            if (this.idMappings.loading) return;
            
            this.idMappings.loading = true;
            try {
                const response = await fetch('{{ route("admin.inventaris.produk.id-mappings") }}');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.idMappings.outlets = data.outlets || {};
                        this.idMappings.categories = data.categories || {};
                        this.idMappings.units = data.units || {};
                        this.idMappings.loaded = true;
                    }
                }
            } catch (error) {
                console.error('Error loading ID mappings:', error);
                // Set default empty objects to prevent undefined errors
                this.idMappings.outlets = {};
                this.idMappings.categories = {};
                this.idMappings.units = {};
            } finally {
                this.idMappings.loading = false;
            }
        },

        getOutletId(outletName) {
            if (!this.idMappings.outlets || typeof this.idMappings.outlets !== 'object') {
                console.warn('Outlet mappings not loaded properly');
                return null;
            }
            const entry = Object.entries(this.idMappings.outlets).find(([name, id]) => name === outletName);
            return entry ? entry[1] : null;
        },

        getCategoryId(categoryName) {
            if (!this.idMappings.categories || typeof this.idMappings.categories !== 'object') {
                console.warn('Category mappings not loaded properly');
                return null;
            }
            const entry = Object.entries(this.idMappings.categories).find(([name, id]) => name === categoryName);
            return entry ? entry[1] : null;
        },

        getUnitId(unitName) {
            if (!this.idMappings.units || typeof this.idMappings.units !== 'object') {
                console.warn('Unit mappings not loaded properly');
                return null;
            }
            const entry = Object.entries(this.idMappings.units).find(([name, id]) => name === unitName);
            return entry ? entry[1] : null;
        },

        confirmDelete(p){ 
          this.toDelete = p; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.produk.destroy', '') }}/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        async onPickImage(ev, slot){
          const file = ev.target.files[0];
          if (!file) return;
          
          // Validate file type
          if (!file.type.startsWith('image/')) {
            this.showToastMessage('File harus berupa gambar', 'error');
            return;
          }

          // Validate file size (max 5MB before compression)
          if (file.size > 5 * 1024 * 1024) {
            this.showToastMessage('Ukuran file maksimal 5MB', 'error');
            return;
          }

          try {
            // Compress image
            const compressedFile = await this.compressImage(file);
            
            // Create preview URL
            const reader = new FileReader();
            reader.onload = () => {
              // Store both preview URL and File object
              if (!this.form.imageFiles) {
                this.form.imageFiles = [];
              }
              this.form.images.splice(slot, 1, reader.result);
              this.form.imageFiles.splice(slot, 1, compressedFile);
              
              // Jika ini gambar pertama yang diupload dan belum ada primary image, set sebagai primary
              const hasExistingImages = this.form.images.filter(img => img).length > 1;
              if (!hasExistingImages || this.form.primaryImageIndex === null || this.form.primaryImageIndex === undefined) {
                this.form.primaryImageIndex = slot;
              }
              
              // Ensure we only keep 4 images
              this.form.images = this.form.images.slice(0, 4);
              this.form.imageFiles = this.form.imageFiles.slice(0, 4);
            };
            reader.readAsDataURL(compressedFile);
          } catch (error) {
            console.error('Error processing image:', error);
            this.showToastMessage('Gagal memproses gambar', 'error');
          }
          
          ev.target.value = '';
        },

        async compressImage(file) {
          return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
              const img = new Image();
              img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                
                // Resize if image is too large
                const maxDimension = 1200;
                if (width > maxDimension || height > maxDimension) {
                  if (width > height) {
                    height = (height / width) * maxDimension;
                    width = maxDimension;
                  } else {
                    width = (width / height) * maxDimension;
                    height = maxDimension;
                  }
                }
                
                canvas.width = width;
                canvas.height = height;
                
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                // Convert to blob with quality 0.8
                canvas.toBlob((blob) => {
                  if (blob) {
                    // Create new File from blob
                    const compressedFile = new File([blob], file.name, {
                      type: 'image/jpeg',
                      lastModified: Date.now()
                    });
                    resolve(compressedFile);
                  } else {
                    reject(new Error('Failed to compress image'));
                  }
                }, 'image/jpeg', 0.8);
              };
              img.onerror = reject;
              img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
          });
        },

        async removeImage(index) {
          if (!confirm('Hapus gambar ini?')) return;
          
          try {
            // If editing existing product and image has ID, delete from server
            if (this.form.id && this.form.images[index] && this.form.images[index].id) {
              const imageId = this.form.images[index].id;
              const productId = typeof this.form.id === 'object' ? this.form.id.id : this.form.id;
              
              console.log('removeImage debug:', {
                formId: this.form.id,
                productId: productId,
                imageId: imageId,
                index: index
              });
              
              // Use route helper to generate correct URL
              const url = '{{ route("admin.inventaris.produk.remove-image", ":productId") }}'.replace(':productId', productId);
              console.log('Fetching URL:', url);
              
              const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Content-Type': 'application/json',
                  'Accept': 'application/json'
                },
                body: JSON.stringify({ image_id: imageId })
              });
              
              if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error('Gagal menghapus gambar dari server');
              }
            }
            
            // Remove image from array
            this.form.images.splice(index, 1);
            if (this.form.imageFiles && this.form.imageFiles[index]) {
              this.form.imageFiles.splice(index, 1);
            }
            
            // Adjust primary index if needed
            if (this.form.primaryImageIndex === index) {
              // If we deleted the primary image, set first available image as primary
              const remainingImages = this.form.images.filter(img => img);
              if (remainingImages.length > 0) {
                // Find first non-empty slot
                for (let i = 0; i < this.form.images.length; i++) {
                  if (this.form.images[i]) {
                    this.form.primaryImageIndex = i;
                    break;
                  }
                }
              } else {
                this.form.primaryImageIndex = 0; // Reset to 0 if no images left
              }
            } else if (this.form.primaryImageIndex > index) {
              // Adjust index if primary was after deleted image
              this.form.primaryImageIndex--;
            }
            
            this.showToastMessage('Gambar berhasil dihapus', 'success');
          } catch (error) {
            console.error('Error removing image:', error);
            this.showToastMessage('Gagal menghapus gambar: ' + error.message, 'error');
          }
        },

        async setPrimaryImage(index) {
          try {
            // If editing existing product and image has ID, update on server
            if (this.form.id && this.form.images[index] && this.form.images[index].id) {
              const imageId = this.form.images[index].id;
              const productId = typeof this.form.id === 'object' ? this.form.id.id : this.form.id;
              
              console.log('setPrimaryImage debug:', {
                formId: this.form.id,
                productId: productId,
                imageId: imageId,
                index: index
              });
              
              // Use route helper to generate correct URL
              const url = '{{ route("admin.inventaris.produk.set-primary-image", ":productId") }}'.replace(':productId', productId);
              console.log('Fetching URL:', url);
              
              const response = await fetch(url, {
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Content-Type': 'application/json',
                  'Accept': 'application/json'
                },
                body: JSON.stringify({ image_id: imageId })
              });
              
              if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error('Gagal mengatur gambar cover di server');
              }
            }
            
            this.form.primaryImageIndex = index;
            this.showToastMessage('Gambar cover berhasil diset', 'success');
          } catch (error) {
            console.error('Error setting primary image:', error);
            this.showToastMessage('Gagal mengatur gambar cover: ' + error.message, 'error');
          }
        },

        addVariant(){ 
          this.form.variants.push({
            id: null,
            name: '',
            sku: '',
            stock: 0,
            price: 0
          }); 
        },

        removeVariant(i){ 
          this.form.variants.splice(i, 1); 
        },

        exportPdf(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            type: this.typeFilter
          });
          window.open(`{{ route('admin.inventaris.produk.export.pdf') }}?${params}`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            type: this.typeFilter
          });
          window.open(`{{ route('admin.inventaris.produk.export.excel') }}?${params}`, '_blank');
        },

        async importExcel(event){
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          try {
            const response = await fetch('{{ route("admin.inventaris.produk.import.excel") }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil diimport', 'success');
              await this.fetchData();
            } else {
              this.showToastMessage(result.error || 'Gagal mengimport data', 'error');
            }
          } catch (error) {
            console.error('Error importing data:', error);
            this.showToastMessage('Gagal mengimport data', 'error');
          } finally {
            event.target.value = '';
          }
        },

        downloadTemplate(){
          window.open('{{ route("admin.inventaris.produk.download-template") }}', '_blank');
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        // HPP Modal Functions
        showHppModal: false,
        showAddHppModal: false,
        showEditHppModal: false,
        selectedProduct: null,
        hppData: [],
        hppSummary: {
          total_in: 0,
          total_out: 0,
          avg_hpp: 0
        },
        loadingHppData: false,
        savingHpp: false,
        updatingHpp: false,
        deletingHpp: false,
        hppToDelete: null,
        editingHpp: null,
        hppForm: {
          type: 'in',
          quantity: 0,
          hpp_per_unit: 0,
          notes: ''
        },
        editHppForm: {
          id: null,
          type: 'in',
          quantity: 0,
          hpp_per_unit: 0,
          notes: ''
        },
        hppErrors: {},
        editHppErrors: {},

        async openHppModal(product) {
          console.log('openHppModal called with product:', product);
          console.log('Product ID:', product.id);
          console.log('Product id_produk:', product.id_produk);
          
          // Ensure product has id property (use id_produk as fallback)
          if (!product.id && product.id_produk) {
            product.id = product.id_produk;
            console.log('Set product.id from id_produk:', product.id);
          }
          
          if (!product.id) {
            this.showToastMessage('Data produk tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
            console.error('Product missing both id and id_produk:', product);
            return;
          }
          
          this.selectedProduct = product;
          this.showHppModal = true;
          await this.fetchHppData();
        },

        closeHppModal() {
          this.showHppModal = false;
          // Don't clear selectedProduct immediately - keep it for edit operations
          // this.selectedProduct = null;
          this.hppData = [];
          
          // Clear selectedProduct after a delay to allow edit operations to complete
          setTimeout(() => {
            if (!this.showEditHppModal) {
              this.selectedProduct = null;
            }
          }, 100);
        },

        openAddHppStock() {
          this.hppForm = {
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: '',
            // Store product data as backup
            productId: this.selectedProduct?.id || this.selectedProduct?.id_produk,
            productData: this.selectedProduct ? {...this.selectedProduct} : null
          };
          
          console.log('openAddHppStock: Backup data stored:', {
            productId: this.hppForm.productId,
            productData: this.hppForm.productData
          });
          this.hppErrors = {};
          this.showAddHppModal = true;
        },

        closeAddHppModal() {
          this.showAddHppModal = false;
          this.hppForm = {
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: '',
            // Store product data as backup
            productId: this.selectedProduct?.id || this.selectedProduct?.id_produk,
            productData: this.selectedProduct ? {...this.selectedProduct} : null
          };
          
          console.log('openAddHppStock: Backup data stored:', {
            productId: this.hppForm.productId,
            productData: this.hppForm.productData
          });
          this.hppErrors = {};
        },

        openEditHpp(hpp) {
          console.log('openEditHpp called with:', hpp);
          
          // Validate hpp object and its id
          if (!hpp || !hpp.id) {
            this.showToastMessage('Data HPP tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
            console.error('Invalid HPP data:', hpp);
            return;
          }

          console.log('HPP data is valid, setting up edit form...');
          
          this.editingHpp = hpp;
          this.editHppForm = {
            id: hpp.id,
            type: hpp.type || 'in',
            quantity: hpp.quantity || 0,
            hpp_per_unit: hpp.hpp_per_unit || 0,
            notes: hpp.notes || '',
            // Store product data as backup
            productId: this.selectedProduct?.id || this.selectedProduct?.id_produk,
            productData: this.selectedProduct ? {...this.selectedProduct} : null
          };
          
          console.log('Edit form data:', this.editHppForm);
          
          this.editHppErrors = {};
          this.showEditHppModal = true;
        },

        closeEditHppModal() {
          this.showEditHppModal = false;
          this.editingHpp = null;
          this.editHppForm = {
            id: null,
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: '',
            productId: null,
            productData: null
          };
          this.editHppErrors = {};
        },

        async fetchHppData() {
          if (!this.selectedProduct) return;
          
          // Get product ID with fallback
          const productId = this.selectedProduct.id || this.selectedProduct.id_produk;
          if (!productId) {
            console.error('selectedProduct missing both id and id_produk:', this.selectedProduct);
            this.showToastMessage('ID Produk tidak valid untuk memuat data HPP', 'error');
            return;
          }
          
          console.log('Fetching HPP data for product ID:', productId);
          
          this.loadingHppData = true;
          try {
            const response = await fetch(`{{ url('admin/inventaris/produk') }}/${productId}/hpp-data`);
            const data = await response.json();
            
            if (response.ok) {
              this.hppData = data.data || [];
              this.hppSummary = data.summary || {
                total_in: 0,
                total_out: 0,
                avg_hpp: 0
              };
              console.log('HPP data loaded successfully:', this.hppData.length, 'records');
            } else {
              this.showToastMessage(data.error || 'Gagal memuat data HPP', 'error');
            }
          } catch (error) {
            console.error('Error fetching HPP data:', error);
            this.showToastMessage('Gagal memuat data HPP', 'error');
          } finally {
            this.loadingHppData = false;
          }
        },

        async submitHppForm() {
          this.savingHpp = true;
          this.hppErrors = {};

          try {
            // Validasi
            if (!this.hppForm.quantity || this.hppForm.quantity <= 0) {
              this.hppErrors.quantity = 'Jumlah harus lebih dari 0';
              this.savingHpp = false;
              return;
            }

            if (this.hppForm.type === 'in' && (!this.hppForm.hpp_per_unit || this.hppForm.hpp_per_unit <= 0)) {
              this.hppErrors.hpp_per_unit = 'HPP per unit harus lebih dari 0';
              this.savingHpp = false;
              return;
            }

            // Enhanced validation for selectedProduct with multiple fallbacks (same as editHppForm)
            let productId = null;
            let productData = null;
            
            // Try to get product data from multiple sources
            if (this.selectedProduct) {
              productId = this.selectedProduct.id || this.selectedProduct.id_produk;
              productData = this.selectedProduct;
              console.log('submitHppForm: Using selectedProduct:', productData);
            } else if (this.hppForm.productId) {
              productId = this.hppForm.productId;
              productData = this.hppForm.productData;
              console.log('submitHppForm: Using backup product data from hppForm:', productData);
            }
            
            if (!productId) {
              console.error('submitHppForm: No valid product ID found');
              console.error('selectedProduct:', this.selectedProduct);
              console.error('hppForm.productId:', this.hppForm.productId);
              
              this.hppErrors.general = 'ID Produk tidak valid. Silakan tutup modal dan coba lagi.';
              this.showToastMessage('ID Produk tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
              this.savingHpp = false;
              return;
            }

            console.log('submitHppForm: All validations passed, sending request...');
            console.log('submitHppForm: Using product ID:', productId);
            
            const response = await fetch(`{{ url('admin/inventaris/produk') }}/${productId}/hpp`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              },
              body: JSON.stringify(this.hppForm)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data HPP berhasil disimpan', 'success');
              this.closeAddHppModal();
              await this.fetchHppData();
              await this.fetchData(); // Refresh product list to update stock
            } else {
              if (result.errors) {
                this.hppErrors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Gagal menyimpan data HPP', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving HPP data:', error);
            this.showToastMessage('Terjadi kesalahan saat menyimpan data HPP', 'error');
          } finally {
            this.savingHpp = false;
          }
        },

        async submitEditHppForm() {
          console.log('submitEditHppForm called');
          console.log('editHppForm:', this.editHppForm);
          console.log('selectedProduct:', this.selectedProduct);
          console.log('selectedProduct.id:', this.selectedProduct?.id);
          console.log('selectedProduct.id_produk:', this.selectedProduct?.id_produk);
          
          this.updatingHpp = true;
          this.editHppErrors = {};

          try {
            // Enhanced validation for ID
            if (!this.editHppForm || !this.editHppForm.id) {
              console.error('Invalid editHppForm or missing ID:', this.editHppForm);
              this.editHppErrors.general = 'ID HPP tidak valid. Silakan tutup modal dan coba lagi.';
              this.showToastMessage('Data HPP tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
              this.updatingHpp = false;
              return;
            }

            // Validasi
            if (!this.editHppForm.quantity || this.editHppForm.quantity <= 0) {
              this.editHppErrors.quantity = 'Jumlah harus lebih dari 0';
              this.updatingHpp = false;
              return;
            }

            if (this.editHppForm.type === 'in' && (!this.editHppForm.hpp_per_unit || this.editHppForm.hpp_per_unit <= 0)) {
              this.editHppErrors.hpp_per_unit = 'HPP per unit harus lebih dari 0';
              this.updatingHpp = false;
              return;
            }

            // Enhanced validation for selectedProduct with multiple fallbacks
            let productId = null;
            let productData = null;
            
            // Try to get product data from multiple sources
            if (this.selectedProduct) {
              productId = this.selectedProduct.id || this.selectedProduct.id_produk;
              productData = this.selectedProduct;
              console.log('Using selectedProduct:', productData);
            } else if (this.editHppForm.productId) {
              productId = this.editHppForm.productId;
              productData = this.editHppForm.productData;
              console.log('Using backup product data from editHppForm:', productData);
            }
            
            if (!productId) {
              console.error('No valid product ID found');
              console.error('selectedProduct:', this.selectedProduct);
              console.error('editHppForm.productId:', this.editHppForm.productId);
              console.error('editHppForm.productData:', this.editHppForm.productData);
              
              this.editHppErrors.general = 'ID Produk tidak valid. Silakan tutup modal dan coba lagi.';
              this.showToastMessage('ID Produk tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
              this.updatingHpp = false;
              return;
            }

            console.log('All validations passed, sending request...');
            console.log('Using product ID:', productId);
            console.log('Using HPP ID:', this.editHppForm.id);
            
            const response = await fetch(`{{ url('admin/inventaris/produk') }}/${productId}/hpp/${this.editHppForm.id}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                type: this.editHppForm.type,
                quantity: this.editHppForm.quantity,
                hpp_per_unit: this.editHppForm.hpp_per_unit,
                notes: this.editHppForm.notes
              })
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data HPP berhasil diupdate', 'success');
              this.closeEditHppModal();
              await this.fetchHppData();
              await this.fetchData(); // Refresh product list to update stock
            } else {
              if (result.errors) {
                this.editHppErrors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Gagal mengupdate data HPP', 'error');
              }
            }
          } catch (error) {
            console.error('Error updating HPP data:', error);
            this.showToastMessage('Terjadi kesalahan saat mengupdate data HPP', 'error');
          } finally {
            this.updatingHpp = false;
          }
        },

        confirmDeleteHpp(hpp) {
          this.hppToDelete = hpp;
        },

        async deleteHppNow() {
          if (!this.hppToDelete) return;
          
          this.deletingHpp = true;
          try {
            const response = await fetch(`{{ url('admin/inventaris/produk') }}/${this.selectedProduct.id}/hpp/${this.hppToDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data HPP berhasil dihapus', 'success');
              this.hppToDelete = null;
              await this.fetchHppData();
              await this.fetchData(); // Refresh product list to update stock
            } else {
              this.showToastMessage(result.error || 'Gagal menghapus data HPP', 'error');
            }
          } catch (error) {
            console.error('Error deleting HPP data:', error);
            this.showToastMessage('Gagal menghapus data HPP', 'error');
          } finally {
            this.deletingHpp = false;
          }
        },

        formatDate(dateString) {
          if (!dateString) return '';
          const date = new Date(dateString);
          return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
        }
      };
    }
  </script>
</x-layouts.admin>
