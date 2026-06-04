<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Inventaris / Inventori']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Inventaris / Inventori')]); ?>
  <div x-data="inventoriCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Inventori</h1>
        <p class="text-slate-600 text-sm">Kelola daftar stok barang per outlet/lokasi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Item
        </button>
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import text-lg'></i><span>Import Excel</span>
          <input type="file" class="hidden" accept=".xlsx,.xls,.csv" x-on:change="importExcel($event)">
        </label>
        <button x-on:click="downloadTemplate()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-download text-lg'></i> Template
        </button>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-5">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama, kode, kategori, outlet, PJ…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Outlet -->
        <div class="lg:col-span-3">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Outlet: Semua</option>
            <template x-for="o in outlets" :key="o.id_outlet">
              <option :value="o.id_outlet" x-text="o.nama_outlet"></option>
            </template>
          </select>
        </div>
        <!-- Filter Kategori -->
        <div class="lg:col-span-2">
          <select x-model="categoryFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Kategori: Semua</option>
            <template x-for="c in categories" :key="c.id_kategori">
              <option :value="c.id_kategori" x-text="c.nama_kategori"></option>
            </template>
          </select>
        </div>
        <!-- Filter Status -->
        <div class="lg:col-span-2">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="tersedia">Tersedia</option>
            <option value="tidak tersedia">Tidak Tersedia</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <!-- Sort -->
        <div class="grid grid-cols-2 gap-2 lg:col-span-6">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="name">Nama</option>
            <option value="category">Kategori</option>
            <option value="outlet">Outlet</option>
            <option value="pic">Penanggung Jawab</option>
            <option value="stock">Stok</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
        </div>

        <!-- Toggle View -->
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
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="it in inventori" :key="it.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-archive text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="it.name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="it.stock > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="it.stock > 0 ? 'Ready' : 'Habis'"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span x-text="it.category"></span> • <span x-text="it.outlet"></span> • PJ: <span x-text="it.pic"></span>
                </div>
                <div class="mt-2 text-sm">
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 border border-emerald-200">
                    <i class='bx bx-cube'></i><span x-text="it.stock"></span>
                  </span>
                  <span class="ml-2 text-slate-600">Lokasi: <span class="font-medium" x-text="it.location"></span></span>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click="openDetail(it)" class="flex-1 rounded-lg bg-emerald-600 text-white px-3 py-2 hover:bg-emerald-700 text-sm"><i class='bx bx-show'></i> Detail</button>
              <button x-on:click="openEdit(it)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm"><i class='bx bx-edit-alt'></i> Edit</button>
              <button x-on:click="confirmDelete(it)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i> Hapus</button>
            </div>
          </div>
        </template>
      </div>
      <div x-show="inventori.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Kode</th>
              <th class="text-left px-4 py-3">Nama Barang</th>
              <th class="text-left px-4 py-3">Kategori</th>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Penanggung Jawab</th>
              <th class="text-left px-4 py-3">Stok</th>
              <th class="text-left px-4 py-3">Lokasi Penyimpanan</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="it in inventori" :key="it.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-mono text-slate-600" x-text="it.code"></td>
                <td class="px-4 py-3 font-medium" x-text="it.name"></td>
                <td class="px-4 py-3" x-text="it.category"></td>
                <td class="px-4 py-3" x-text="it.outlet"></td>
                <td class="px-4 py-3" x-text="it.pic"></td>
                <td class="px-4 py-3" x-text="it.stock"></td>
                <td class="px-4 py-3" x-text="it.location"></td>
                <td class="px-4 py-3">
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="it.status === 'tersedia' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="it.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia'"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <button x-on:click="openDetail(it)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700"><i class='bx bx-show'></i> Detail</button>
                    <button x-on:click="openEdit(it)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    <button x-on:click="confirmDelete(it)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="inventori.length===0"><td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="it in inventori" :key="it.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100">
                <i class='bx bx-archive'></i>
              </div>
              <div class="flex-1">
                <div class="font-semibold" x-text="it.name"></div>
                <div class="text-[11px] text-slate-500">
                  <span class="font-mono" x-text="it.code"></span> • 
                  <span x-text="it.category"></span> • 
                  <span x-text="it.outlet"></span>
                </div>
                <div class="text-sm text-slate-600 line-clamp-2 mt-1" x-text="'PJ: ' + it.pic"></div>
                <div class="mt-1 text-[11px]">
                  <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 mr-2">
                    <i class='bx bx-cube'></i> <span x-text="it.stock"></span>
                  </span>
                  <span class="px-2 py-0.5 rounded-full"
                        :class="it.status === 'tersedia' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-50 text-slate-600 border border-slate-200' "
                        x-text="it.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia'"></span>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click="openDetail(it)" class="flex-1 rounded-lg bg-emerald-600 text-white px-3 py-2 hover:bg-emerald-700">Detail</button>
              <button x-on:click="openEdit(it)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Edit</button>
              <button x-on:click="confirmDelete(it)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50">Hapus</button>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Inventori' : 'Tambah Inventori'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.code" placeholder="INV-001" 
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50" readonly>
              <div class="text-xs text-slate-500 mt-1">Kode inventori digenerate otomatis</div>
              <div x-show="errors.kode_inventori" class="text-red-500 text-xs mt-1" x-text="errors.kode_inventori"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Barang <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" placeholder="Nama barang" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.nama_barang" class="text-red-500 text-xs mt-1" x-text="errors.nama_barang"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Kategori <span class="text-red-500">*</span></label>
              <select x-model="form.category_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Kategori —</option>
                <template x-for="c in categories" :key="c.id_kategori">
                  <option :value="c.id_kategori" x-text="c.nama_kategori"></option>
                </template>
              </select>
              <div x-show="errors.id_kategori" class="text-red-500 text-xs mt-1" x-text="errors.id_kategori"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Outlet —</option>
                <template x-for="o in outlets" :key="o.id_outlet">
                  <option :value="o.id_outlet" x-text="o.nama_outlet"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Penanggung Jawab <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.pic" placeholder="Nama PJ" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.penanggung_jawab" class="text-red-500 text-xs mt-1" x-text="errors.penanggung_jawab"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Stok <span class="text-red-500">*</span></label>
              <input type="number" min="0" x-model.number="form.stock" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.stok" class="text-red-500 text-xs mt-1" x-text="errors.stok"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Lokasi Penyimpanan <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.location" placeholder="Gudang/Outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.lokasi_penyimpanan" class="text-red-500 text-xs mt-1" x-text="errors.lokasi_penyimpanan"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Status <span class="text-red-500">*</span></label>
              <select x-model="form.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="tersedia">Tersedia</option>
                <option value="tidak tersedia">Tidak Tersedia</option>
              </select>
              <div x-show="errors.status" class="text-red-500 text-xs mt-1" x-text="errors.status"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Catatan (opsional)</label>
              <textarea x-model.trim="form.note" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              <div x-show="errors.catatan" class="text-red-500 text-xs mt-1" x-text="errors.catatan"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" class="rounded border-slate-300">
                <span class="text-sm text-slate-700">Aktif</span>
              </label>
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="saving" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: Detail -->
    <div x-show="detail" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20 overflow-y-auto">
      <div x-on:click.outside="detail=null"
           class="w-full max-w-5xl rounded-3xl bg-white shadow-[0_20px_60px_rgba(15,23,42,.2)] overflow-hidden my-4">
        <!-- Header -->
        <div class="relative p-5 sm:p-6 bg-gradient-to-r from-primary-600 to-primary-500 text-white">
          <div class="flex items-start sm:items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center ring-1 ring-white/30">
              <i class='bx bx-archive text-2xl'></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-semibold text-lg sm:text-xl truncate" x-text="detail?.name"></h3>
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-white/15 ring-1 ring-white/30"
                      x-text="detail?.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia'"></span>
              </div>
              <div class="text-xs sm:text-sm text-white/80 mt-1 flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1"><i class='bx bx-category-alt'></i><span x-text="detail?.category"></span></span>
                <span class="inline-flex items-center gap-1"><i class='bx bx-store-alt'></i><span x-text="detail?.outlet"></span></span>
                <span class="inline-flex items-center gap-1"><i class='bx bx-user'></i> PJ: <span x-text="detail?.pic"></span></span>
              </div>
            </div>
            <button class="p-2 -m-2 hover:bg-white/10 rounded-lg" x-on:click="detail=null" aria-label="Tutup">
              <i class='bx bx-x text-xl'></i>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 space-y-5">
          <!-- Stock bar -->
          <div>
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
              <span>Stok</span>
              <span class="font-medium text-slate-700" x-text="detail?.stock + ' unit'"></span>
            </div>
            <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400"
                   :style="`width: ${Math.min((detail?.stock ?? 0) * 2, 100)}%`"></div>
            </div>
          </div>

          <!-- Info grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-2xl border border-slate-200 p-4">
              <div class="text-[11px] uppercase tracking-wide text-slate-500">Kode</div>
              <div class="font-mono text-slate-800 mt-0.5" x-text="detail?.code || '-'"></div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
              <div class="text-[11px] uppercase tracking-wide text-slate-500">Lokasi Penyimpanan</div>
              <div class="mt-0.5 font-medium text-slate-800" x-text="detail?.location || '-'"></div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
              <div class="text-[11px] uppercase tracking-wide text-slate-500">Penanggung Jawab</div>
              <div class="mt-0.5 font-medium text-slate-800" x-text="detail?.pic || '-'"></div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4">
              <div class="text-[11px] uppercase tracking-wide text-slate-500">Catatan</div>
              <div class="mt-0.5 text-slate-700" x-text="detail?.note || '-'"></div>
            </div>
          </div>

          <!-- Chips -->
          <div class="flex flex-wrap gap-2">
            <span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-700">
              Kategori: <span class="font-medium" x-text="detail?.category"></span>
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs bg-slate-100 text-slate-700">
              Outlet: <span class="font-medium" x-text="detail?.outlet"></span>
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs"
                  :class="(detail?.stock ?? 0) > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'">
              <i class='bx bx-cube'></i> <span x-text="detail?.stock"></span> unit
            </span>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-5 sm:px-6 py-3 border-t border-slate-100 flex items-center justify-end gap-2 bg-slate-50/60">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-white" x-on:click="detail=null">Tutup</button>
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-white" x-on:click="openEdit(detail)">Edit</button>
          <button class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700" x-on:click="confirmDelete(detail)">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-lg rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Item Inventori?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.code"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="'Outlet: ' + (toDelete?.outlet || '-') + ' • Stok: ' + (toDelete?.stock || 0)"></div>
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
  </div>

  <script>
    function inventoriCrud(){
      return {
        // State management
        inventori: [],
        outlets: [],
        categories: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        outletFilter: 'ALL',
        categoryFilter: 'ALL',
        statusFilter: 'ALL',
        sortKey: 'name',
        sortDir: 'asc',
        view: 'table',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          code: '', 
          name: '', 
          category_id: '', 
          outlet_id: '', 
          pic: '', 
          stock: 0, 
          location: '', 
          status: 'tersedia', 
          note: '', 
          is_active: true 
        },
        errors: {},
        
        // Detail & Delete
        detail: null,
        toDelete: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          await Promise.all([
            this.fetchData(),
            this.fetchOutlets(),
            this.fetchCategories()
          ]);
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              category_filter: this.categoryFilter,
              status_filter: this.statusFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`<?php echo e(route('admin.inventaris.inventori.data')); ?>?${params}`);
            const data = await response.json();
            
            this.inventori = data.data.map(item => ({
              id: item.id_inventori || item.id,
              code: item.code || item.kode_inventori,
              name: item.name || item.nama_barang,
              category: item.category || item.nama_kategori,
              category_id: item.category_id || item.id_kategori,
              outlet: item.outlet || item.nama_outlet,
              outlet_id: item.outlet_id || item.id_outlet,
              pic: item.pic || item.penanggung_jawab,
              stock: item.stock || item.stok,
              location: item.location || item.lokasi_penyimpanan,
              status: item.status,
              note: item.note || item.catatan || '',
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
            const response = await fetch('<?php echo e(route("admin.inventaris.inventori.outlets")); ?>');
            const data = await response.json();
            this.outlets = data;
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async fetchCategories(){
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.inventori.categories")); ?>');
            const data = await response.json();
            this.categories = data;
          } catch (error) {
            console.error('Error fetching categories:', error);
          }
        },

        async openCreate(){ 
          try {
            ModalLoader.show();
            const response = await fetch('<?php echo e(route("admin.inventaris.inventori.generate-kode")); ?>');
            const data = await response.json();
            
            this.form = { 
              id: null, 
              code: data.kode_inventori, 
              name: '', 
              category_id: '', 
              outlet_id: '', 
              pic: '', 
              stock: 0, 
              location: '', 
              status: 'tersedia', 
              note: '', 
              is_active: true 
            }; 
          } catch (error) {
            console.error('Error generating code:', error);
            this.form = { 
              id: null, 
              code: '', 
              name: '', 
              category_id: '', 
              outlet_id: '', 
              pic: '', 
              stock: 0, 
              location: '', 
              status: 'tersedia', 
              note: '', 
              is_active: true 
            }; 
          } finally {
              ModalLoader.hide();
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        async openEdit(item){ 
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.inventori.show', '')); ?>/${item.id}`);
            const data = await response.json();
            
            this.form = { 
              id: data.id,
              code: data.code, 
              name: data.name, 
              category_id: data.category_id, 
              outlet_id: data.outlet_id, 
              pic: data.pic, 
              stock: data.stock, 
              location: data.location, 
              status: data.status, 
              note: data.note, 
              is_active: data.is_active 
            }; 
          } catch (error) {
            console.error('Error fetching item details:', error);
            // Fallback to local data
            this.form = { 
              id: item.id,
              code: item.code, 
              name: item.name, 
              category_id: item.category_id, 
              outlet_id: item.outlet_id, 
              pic: item.pic, 
              stock: item.stock, 
              location: item.location, 
              status: item.status, 
              note: item.note, 
              is_active: item.is_active 
            }; 
          }
          
          this.errors = {};
          this.showForm = true; 
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        openDetail(item){ 
          this.detail = item; 
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `<?php echo e(route('admin.inventaris.inventori.update', '')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.inventaris.inventori.store")); ?>';

            const method = this.form.id ? 'PUT' : 'POST';

            const formData = {
              kode_inventori: this.form.code,
              nama_barang: this.form.name,
              id_kategori: this.form.category_id,
              id_outlet: this.form.outlet_id,
              penanggung_jawab: this.form.pic,
              stok: this.form.stock,
              lokasi_penyimpanan: this.form.location,
              status: this.form.status,
              catatan: this.form.note,
              is_active: this.form.is_active
            };

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(item){ 
          this.toDelete = item; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.inventori.destroy', '')); ?>/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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

        exportPdf(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            status: this.statusFilter
          });
          window.open(`<?php echo e(route('admin.inventaris.inventori.export.pdf')); ?>?${params}`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet: this.outletFilter,
            status: this.statusFilter
          });
          window.open(`<?php echo e(route('admin.inventaris.inventori.export.excel')); ?>?${params}`, '_blank');
        },

        async importExcel(event){
          const file = event.target.files[0];
          if (!file) return;

          const formData = new FormData();
          formData.append('file', file);

          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.inventori.import.excel")); ?>', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
          window.open('<?php echo e(route("admin.inventaris.inventori.download-template")); ?>', '_blank');
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\inventori\index.blade.php ENDPATH**/ ?>