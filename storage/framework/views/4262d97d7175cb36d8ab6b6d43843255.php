
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Keuangan / Manajemen RAB']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Keuangan / Manajemen RAB')]); ?>
  <div x-data="rabPage()" x-init="init()" class="space-y-5 overflow-x-hidden">

    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Manajemen RAB</h1>
        <p class="text-slate-600 text-sm">Atur template RAB, anggaran, persetujuan, dan realisasi pemakaian.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <div class="relative">
          <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
          <input x-model="q" placeholder="Cari nama, deskripsi, status…"
                 class="w-64 pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
        </div>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.rab.create')): ?>
        <button @click="openForm()"
          class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah RAB
        </button>
        <?php endif; ?>
        <button @click="exportJson()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export
        </button>
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import text-lg'></i> Import
          <input type="file" class="hidden" accept="application/json" @change="importJson($event)">
        </label>
      </div>
    </div>

    <!-- Filter toolbar -->
    <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
      <select x-model="selectedOutlet" @change="loadData()" class="rounded-xl border border-slate-200 px-3 py-2">
        <template x-for="outlet in outlets" :key="outlet.id_outlet">
          <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
        </template>
      </select>
      <select x-model="selectedBook" @change="loadData()" class="rounded-xl border border-slate-200 px-3 py-2">
        <template x-for="book in books" :key="book.id">
          <option :value="book.id" x-text="book.name"></option>
        </template>
      </select>
      <select x-model="status" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="ALL">Status: Semua</option>
        <template x-for="s in statusOptions" :key="s.value">
          <option :value="s.value" x-text="s.label"></option>
        </template>
      </select>
      <select x-model="produkTerkait" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="ALL">Produk Terkait: Semua</option>
        <option value="YES">Ada</option>
        <option value="NO">Tidak</option>
      </select>
      <select x-model="sortKey" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="created_at">Tanggal Pembuatan</option>
        <option value="name">Nama Template</option>
        <option value="budget_total">Budget Total</option>
        <option value="approved_value">Nilai Disetujui</option>
      </select>
      <select x-model="sortDir" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="desc">Terbaru</option>
        <option value="asc">Terlama</option>
      </select>
      <button @click="resetFilter()" class="rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50">Reset Filter</button>
    </div>

    <!-- Keberangkatan context banner (shown when coming from package detail) -->
    <template x-if="keberangkatanId && keberangkatanInfo">
      <div class="rounded-xl border p-4"
           :class="keberangkatanInfo.has_rab ? 'bg-blue-50 border-blue-200' : 'bg-amber-50 border-amber-200'">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <i class='bx text-xl' :class="keberangkatanInfo.has_rab ? 'bx-calculator text-blue-600' : 'bx-info-circle text-amber-600'"></i>
            <div>
              <div class="font-semibold text-sm" :class="keberangkatanInfo.has_rab ? 'text-blue-800' : 'text-amber-800'">
                <template x-if="keberangkatanInfo.has_rab">
                  <span>Menampilkan RAB untuk keberangkatan: <span x-text="keberangkatanInfo.name"></span></span>
                </template>
                <template x-if="!keberangkatanInfo.has_rab">
                  <span>Belum ada RAB untuk keberangkatan: <span x-text="keberangkatanInfo.name"></span></span>
                </template>
              </div>
              <div class="text-xs mt-0.5" :class="keberangkatanInfo.has_rab ? 'text-blue-600' : 'text-amber-600'"
                   x-text="keberangkatanInfo.has_rab ? 'Kode: ' + (keberangkatanInfo.code||'-') + ' · RAB ID: ' + (keberangkatanInfo.id_rab||'-') : 'Silakan buka halaman detail paket dan klik Generate RAB dari modal RAB keberangkatan.'">
              </div>
            </div>
          </div>
          <a :href="`<?php echo e(url('admin/inventaris/travel/package')); ?>`" 
             class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg border hover:bg-white"
             :class="keberangkatanInfo.has_rab ? 'border-blue-300 text-blue-700' : 'border-amber-300 text-amber-700'">
            <i class='bx bx-arrow-back'></i> Ke Halaman Paket
          </a>
        </div>
      </div>
    </template>

    <!-- Empty state when keberangkatan has no RAB -->
    <template x-if="keberangkatanId && keberangkatanInfo && !keberangkatanInfo.has_rab">
      <div class="rounded-2xl border border-amber-200 bg-amber-50 p-12 text-center">
        <i class='bx bx-calculator text-5xl text-amber-300 mb-3'></i>
        <div class="font-semibold text-amber-800 mb-1">RAB Belum Dibuat</div>
        <div class="text-sm text-amber-700 mb-4">
          Keberangkatan <strong x-text="keberangkatanInfo.name"></strong> belum memiliki RAB.<br>
          Buka halaman detail paket, pilih keberangkatan, lalu klik tombol <strong>Generate RAB</strong> di modal RAB.
        </div>
      </div>
    </template>

    <!-- Desktop table -->
    <div x-show="!keberangkatanId || (keberangkatanInfo && keberangkatanInfo.has_rab)"
         class="hidden xl:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <table class="w-full text-sm table-auto">
        <colgroup>
          <col class="w-36" />
          <col class="w-[16%]" />
          <col class="w-[28%]" />
          <col class="w-36" />
          <col class="w-36" />
          <col class="w-[22%]" />
          <col class="w-44" />
          <col class="w-36" />
          <col class="w-40" />
        </colgroup>

        <thead class="bg-slate-50 text-slate-700">
          <tr>
            <th class="px-2 py-3 text-left whitespace-nowrap">Tanggal</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Nama Template</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Deskripsi &amp; Komponen</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Budget Total</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Nilai Disetujui</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Realisasi</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Status</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Produk?</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <template x-for="r in filtered()" :key="r.id">
            <tr class="border-t border-slate-100 align-top">
              <td class="px-2 py-3 whitespace-nowrap" x-text="formatDate(r.created_at)"></td>
              <td class="px-2 py-3 whitespace-normal break-words font-medium" x-text="r.name"></td>
              <td class="px-2 py-3 whitespace-normal break-words">
                <div class="text-slate-700" x-text="r.description || '-'"></div>
                <ul class="mt-1 list-disc pl-4 text-xs text-slate-600 space-y-0.5">
                  <template x-for="c in r.components" :key="c.uraian">
                    <li>
                      <span x-text="c.uraian"></span>
                      <span class="text-slate-500" x-show="c.biaya > 0"> - <span x-text="rupiah(c.biaya)"></span></span>
                    </li>
                  </template>
                </ul>
              </td>
              <td class="px-2 py-3 whitespace-nowrap" x-text="rupiah(r.budget_total)"></td>
              <td class="px-2 py-3 whitespace-nowrap" x-text="rupiah(r.approved_value)"></td>
              <td class="px-2 py-3 whitespace-normal break-words">
                <div class="text-xs text-slate-500 mb-1">
                  Terpakai: <span class="font-medium" x-text="rupiah(totalSpent(r))"></span>
                  <span class="mx-1">/</span>
                  Sisa: <span class="font-medium" x-text="rupiah(remaining(r))"></span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full rounded-full"
                       :style="`width:${progress(r)}%`"
                       :class="progress(r)>=90?'bg-rose-500':(progress(r)>=60?'bg-amber-500':'bg-emerald-500')"></div>
                </div>
                <div class="mt-1 text-[11px] text-slate-500" x-text="progress(r)+'%'"></div>
              </td>
              <td class="px-2 py-3">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="statusBadge(r.status)">
                  <span x-text="statusLabel(r.status)"></span>
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg border"
                      :class="r.has_product? 'border-emerald-200 text-emerald-700 bg-emerald-50' : 'border-slate-200 text-slate-600 bg-slate-50'">
                  <i class='bx' :class="r.has_product? 'bx-link' : 'bx-unlink'"></i>
                  <span x-text="r.has_product? 'Ada' : 'Tidak'"></span>
                </span>
              </td>
              <td class="px-2 py-3 whitespace-nowrap">
                <div class="flex flex-wrap gap-1.5">
                  <button @click.stop="openView(r)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 hover:bg-slate-50">
                    <i class='bx bx-show'></i><span class="text-sm">Lihat</span>
                  </button>
                  <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.rab.edit')): ?>
                  <button @click.stop="edit(r)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 hover:bg-slate-50">
                    <i class='bx bx-edit'></i><span class="text-sm">Edit</span>
                  </button>
                  <?php endif; ?>
                  <template x-if="canInputRealisasi(r)">
                    <button @click.stop="openRealisasi(r)" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 text-emerald-700 px-2 py-1.5 hover:bg-emerald-50">
                      <i class='bx bx-money'></i><span class="text-sm">Realisasi</span>
                    </button>
                  </template>
                  <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'finance.rab.delete')): ?>
                  <button @click.stop="askDelete(r)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-2 py-1.5 hover:bg-red-50">
                    <i class='bx bx-trash'></i><span class="text-sm">Hapus</span>
                  </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          </template>
          <tr x-show="filtered().length===0">
            <td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile cards -->
    <div x-show="!keberangkatanId || (keberangkatanInfo && keberangkatanInfo.has_rab)"
         class="xl:hidden grid grid-cols-1 gap-3">
      <template x-for="r in filtered()" :key="r.id">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
              <div class="font-semibold" x-text="r.name"></div>
              <div class="text-xs text-slate-500" x-text="formatDate(r.created_at)"></div>
            </div>
            <div class="flex gap-1">
              <button @click="openView(r)" class="rounded-lg border border-slate-200 px-2 py-1 hover:bg-slate-50 text-sm"><i class='bx bx-show'></i></button>
              <button @click="edit(r)" class="rounded-lg border border-slate-200 px-2 py-1 hover:bg-slate-50 text-sm"><i class='bx bx-edit'></i></button>
              <template x-if="canInputRealisasi(r)">
                <button @click="openRealisasi(r)" class="rounded-lg border border-emerald-200 text-emerald-700 px-2 py-1 hover:bg-emerald-50 text-sm"><i class='bx bx-money'></i></button>
              </template>
              <button @click="askDelete(r)" class="rounded-lg border border-red-200 text-red-700 px-2 py-1 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i></button>
            </div>
          </div>
          <div class="mt-2 text-sm">
            <div class="text-slate-700" x-text="r.description || '-'"></div>
            <ul class="mt-1 list-disc pl-5 text-xs text-slate-600">
              <template x-for="c in r.components" :key="c.uraian">
                <li>
                  <span x-text="c.uraian"></span>
                  <span class="text-slate-500" x-show="c.biaya > 0"> - <span x-text="rupiah(c.biaya)"></span></span>
                </li>
              </template>
            </ul>
          </div>
          <div class="mt-2">
            <div class="text-xs text-slate-500 mb-1">
              Terpakai: <span class="font-medium" x-text="rupiah(totalSpent(r))"></span> /
              Sisa: <span class="font-medium" x-text="rupiah(remaining(r))"></span>
            </div>
            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
              <div class="h-full rounded-full"
                   :style="`width:${progress(r)}%`"
                   :class="progress(r)>=90?'bg-rose-500':(progress(r)>=60?'bg-amber-500':'bg-emerald-500')"></div>
            </div>
            <div class="mt-1 text-[11px] text-slate-500" x-text="progress(r)+'%'"></div>
          </div>
          <div class="mt-2 flex items-center justify-between">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="statusBadge(r.status)">
              <span x-text="statusLabel(r.status)"></span>
            </span>
            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg border"
                  :class="r.has_product? 'border-emerald-200 text-emerald-700 bg-emerald-50' : 'border-slate-200 text-slate-600 bg-slate-50'">
              <i class='bx' :class="r.has_product? 'bx-link' : 'bx-unlink'"></i>
              <span x-text="r.has_product? 'Ada' : 'Tidak'"></span>
            </span>
          </div>
        </div>
      </template>
      <div x-show="filtered().length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- Modal View -->
    <div x-show="showView" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20 overflow-y-auto">
      <div @click.outside="showView=false" class="w-full max-w-5xl bg-white rounded-2xl shadow-float overflow-hidden my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Detail RAB</div>
          <button @click="showView=false" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm max-h-[70vh] overflow-y-auto">
          <template x-if="viewData">
            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Tanggal</div><div class="font-medium" x-text="formatDate(viewData.created_at)"></div></div>
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Nama Template</div><div class="font-medium" x-text="viewData.name"></div></div>
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Status</div><div class="font-medium" x-text="statusLabel(viewData.status)"></div></div>
              <div class="rounded-xl border border-slate-200 p-3 sm:col-span-3">
                <div class="text-xs text-slate-500 mb-2">Deskripsi</div>
                <div x-text="viewData.description || '-'"></div>
              </div>
              
              <div class="rounded-xl border border-slate-200 p-3 sm:col-span-3">
                <div class="text-xs text-slate-500 mb-2">Detail Komponen</div>
                <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                      <tr>
                        <th class="px-2 py-2 text-left">Komponen</th>
                        <th class="px-2 py-2 text-right">Qty</th>
                        <th class="px-2 py-2 text-left">Satuan</th>
                        <th class="px-2 py-2 text-right">Harga</th>
                        <th class="px-2 py-2 text-right">Budget</th>
                        <th class="px-2 py-2 text-right">Disetujui</th>
                        <th class="px-2 py-2 text-right">Realisasi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template x-for="d in viewData.details" :key="d.id">
                        <tr class="border-t border-slate-100">
                          <td class="px-2 py-2" x-text="d.nama_komponen"></td>
                          <td class="px-2 py-2 text-right" x-text="d.jumlah"></td>
                          <td class="px-2 py-2" x-text="d.satuan"></td>
                          <td class="px-2 py-2 text-right" x-text="rupiah(d.harga_satuan)"></td>
                          <td class="px-2 py-2 text-right" x-text="rupiah(d.budget)"></td>
                          <td class="px-2 py-2 text-right" x-text="rupiah(d.nilai_disetujui)"></td>
                          <td class="px-2 py-2 text-right" x-text="rupiah(d.realisasi_pemakaian)"></td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Budget Total</div><div class="font-medium" x-text="rupiah(viewData.budget_total)"></div></div>
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Nilai Disetujui</div><div class="font-medium" x-text="rupiah(viewData.approved_value)"></div></div>
              <div class="rounded-xl border border-slate-200 p-3"><div class="text-xs text-slate-500">Produk Terkait</div><div class="font-medium" x-text="viewData.has_product? 'Ada' : 'Tidak'"></div></div>
              <div class="rounded-xl border border-slate-200 p-3 sm:col-span-3">
                <div class="text-xs text-slate-500 mb-1">Realisasi</div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full rounded-full"
                       :style="`width:${progress(viewData)}%`"
                       :class="progress(viewData)>=90?'bg-rose-500':(progress(viewData)>=60?'bg-amber-500':'bg-emerald-500')"></div>
                </div>
                <div class="mt-1 text-xs text-slate-500">
                  Terpakai: <span class="font-medium" x-text="rupiah(totalSpent(viewData))"></span>
                  / Sisa: <span class="font-medium" x-text="rupiah(remaining(viewData))"></span>
                  (<span x-text="progress(viewData)"></span>%)
                </div>
              </div>
            </div>
          </template>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" @click="showView=false">Tutup</button>
          <template x-if="isAdmin && viewData && ['DRAFT', 'PENDING_APPROVAL'].includes(viewData.status)">
            <button class="rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700" @click="openApproval(viewData)">
              <i class='bx bx-check-circle'></i> Approve
            </button>
          </template>
          <button class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700" @click="edit(viewData)">Edit</button>
        </div>
      </div>
    </div>

    <!-- Modal Approval (Admin) -->
    <div x-show="showApproval" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-10 overflow-y-auto">
      <template x-if="approvalData">
        <div @click.outside="closeApproval()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float overflow-hidden my-4">
          <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
              <div class="font-semibold">Approval RAB - Admin</div>
              <div class="text-xs text-slate-500 mt-1" x-text="approvalData.name"></div>
            </div>
            <button @click="closeApproval()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
          </div>

          <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
              <i class='bx bx-info-circle'></i> Admin dapat mengatur qty dan nilai yang disetujui untuk setiap komponen
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-slate-50">
                  <tr>
                    <th class="px-2 py-2 text-left">Komponen</th>
                    <th class="px-2 py-2 text-right w-24">Qty</th>
                    <th class="px-2 py-2 text-left w-24">Satuan</th>
                    <th class="px-2 py-2 text-right w-32">Harga Satuan</th>
                    <th class="px-2 py-2 text-right w-32">Budget</th>
                    <th class="px-2 py-2 text-right w-40">Nilai Disetujui</th>
                    <th class="px-2 py-2 text-center w-24">Approve?</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="(d,idx) in approvalData.details" :key="d.id">
                    <tr class="border-t border-slate-100">
                      <td class="px-2 py-2" x-text="d.nama_komponen"></td>
                      <td class="px-2 py-2">
                        <input type="number" 
                               class="w-full rounded border border-slate-200 px-2 py-1 text-right text-sm"
                               x-model.number="approvalData.details[idx].jumlah"
                               @input="recalculateApprovalBudget(idx)"
                               min="0" step="0.01">
                      </td>
                      <td class="px-2 py-2">
                        <input type="text" 
                               class="w-full rounded border border-slate-200 px-2 py-1 text-sm"
                               x-model="approvalData.details[idx].satuan">
                      </td>
                      <td class="px-2 py-2">
                        <input type="text" 
                               class="w-full rounded border border-slate-200 px-2 py-1 text-right text-sm"
                               :value="formatNumber(approvalData.details[idx].harga_satuan)"
                               @input="updateApprovalHarga(idx, $event.target.value)"
                               placeholder="0">
                      </td>
                      <td class="px-2 py-2 text-right font-medium" x-text="rupiah(approvalData.details[idx].budget)"></td>
                      <td class="px-2 py-2">
                        <input type="text" 
                               class="w-full rounded border border-slate-200 px-2 py-1 text-right text-sm"
                               :value="formatNumber(approvalData.details[idx].nilai_disetujui)"
                               @input="updateApprovalNilai(idx, $event.target.value)"
                               placeholder="0">
                      </td>
                      <td class="px-2 py-2 text-center">
                        <input type="checkbox" 
                               class="rounded border-slate-300"
                               x-model="approvalData.details[idx].disetujui">
                      </td>
                    </tr>
                  </template>
                </tbody>
                <tfoot class="bg-slate-50 font-medium">
                  <tr>
                    <td colspan="4" class="px-2 py-2 text-right">Total:</td>
                    <td class="px-2 py-2 text-right" x-text="rupiah(totalApprovalBudget())"></td>
                    <td class="px-2 py-2 text-right" x-text="rupiah(totalApprovalDisetujui())"></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
              <div>
                <label class="text-sm text-slate-600">Status Approval</label>
                <select x-model="approvalData.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                  <option value="APPROVED">Disetujui</option>
                  <option value="APPROVED_WITH_REV">Disetujui dengan Revisi</option>
                  <option value="REJECTED">Ditolak</option>
                </select>
              </div>
              <div>
                <label class="text-sm text-slate-600">Catatan Admin</label>
                <textarea x-model="approvalData.admin_notes" rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Catatan untuk pegawai..."></textarea>
              </div>
            </div>
          </div>

          <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
            <button @click="closeApproval()" type="button" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
            <button @click="saveApproval()" type="button" class="rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700">
              <i class='bx bx-check-circle'></i> Simpan Approval
            </button>
          </div>
        </div>
      </template>
    </div>

    <!-- Modal Form (Add/Edit RAB - Tanpa Realisasi) -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div @click.outside="closeForm()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float overflow-hidden my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="form.id ? 'Edit RAB' : 'Tambah RAB'"></div>
          <button @click="closeForm()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Outlet</option>
                <template x-for="outlet in outlets" :key="outlet.id_outlet">
                  <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                </template>
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-600">Buku Akuntansi <span class="text-red-500">*</span></label>
              <select x-model="form.book_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Buku</option>
                <template x-for="book in books" :key="book.id">
                  <option :value="book.id" x-text="book.name"></option>
                </template>
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-600">Tanggal Pembuatan</label>
              <input type="date" x-model="form.created_at" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Template <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Deskripsi</label>
              <textarea x-model.trim="form.description" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
            </div>

            <!-- Komponen dinamis dengan Uraian, Qty, Satuan, dan Biaya -->
            <div class="sm:col-span-2">
              <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-slate-700">Komponen Biaya <span class="text-red-500">*</span></label>
                <button @click="addComponent()" type="button" class="text-sm px-3 py-1.5 rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                  <i class='bx bx-plus'></i> Tambah Komponen
                </button>
              </div>
              <div class="space-y-2">
                <template x-for="(c,idx) in form.components" :key="idx">
                  <div class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50">
                    <div class="grid grid-cols-12 gap-2 items-center">
                      <div class="col-span-5">
                        <label class="text-xs text-slate-500">Uraian</label>
                        <input type="text" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               x-model="form.components[idx].uraian" 
                               placeholder="Uraian / Deskripsi">
                      </div>
                      <div class="col-span-2">
                        <label class="text-xs text-slate-500">Qty</label>
                        <input type="number" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               x-model.number="form.components[idx].qty"
                               @input="recalculateComponentBudget(idx)"
                               min="0" step="0.01"
                               placeholder="1">
                      </div>
                      <div class="col-span-2">
                        <label class="text-xs text-slate-500">Satuan</label>
                        <input type="text" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               x-model="form.components[idx].satuan" 
                               placeholder="pcs">
                      </div>
                      <div class="col-span-2">
                        <label class="text-xs text-slate-500">Harga</label>
                        <input type="text" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               :value="formatNumber(form.components[idx].harga_satuan)"
                               @input="updateComponentHarga(idx, $event.target.value)"
                               placeholder="0">
                      </div>
                      <div class="col-span-1 flex items-end">
                        <button @click="form.components.splice(idx,1); recalculateBudget()" type="button" class="w-full p-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                          <i class='bx bx-trash'></i>
                        </button>
                      </div>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                      Budget: <span class="font-medium" x-text="rupiah(form.components[idx].biaya)"></span>
                    </div>
                  </div>
                </template>
                <div x-show="!form.components || form.components.length === 0" class="text-sm text-slate-500 p-4 bg-slate-50 rounded-lg text-center">
                  Belum ada komponen. Klik "Tambah Komponen" untuk menambahkan.
                </div>
              </div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Budget Total (Auto) <span class="text-red-500">*</span></label>
              <input type="text" 
                     :value="rupiah(form.budget_total)"
                     readonly
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-100 font-medium text-lg">
              <div class="text-xs text-slate-500 mt-1">Otomatis dihitung dari total biaya komponen</div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nilai Disetujui (Admin)</label>
              <input type="text" 
                     x-model="formattedApproved" 
                     @input="updateApproved($event.target.value)"
                     placeholder="0"
                     :disabled="!isAdmin"
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 disabled:bg-slate-100">
              <div class="text-xs text-slate-500 mt-1">Format otomatis: <span x-text="rupiah(form.approved_value)"></span></div>
            </div>

            <div>
              <label class="text-sm text-slate-600">Status</label>
              <select x-model="form.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <template x-for="s in statusOptions" :key="s.value">
                  <option :value="s.value" x-text="s.label"></option>
                </template>
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-600">Produk Terkait?</label>
              <select x-model="form.has_product" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option :value="true">Ada</option>
                <option :value="false">Tidak</option>
              </select>
            </div>

            <!-- Info workflow -->
            <div class="sm:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm">
              <div class="flex items-start gap-2">
                <i class='bx bx-info-circle text-blue-600 text-lg'></i>
                <div class="text-blue-800">
                  <strong>Workflow:</strong> Pegawai buat RAB (DRAFT) → Admin validasi & approve (APPROVED) → Baru bisa input realisasi di modal terpisah
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button @click="closeForm()" type="button" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button @click="save()" type="button" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Modal Realisasi (Terpisah) -->
    <div x-show="showRealisasi" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <template x-if="realisasiData">
        <div @click.outside="closeRealisasi()" class="w-full max-w-6xl bg-white rounded-2xl shadow-float overflow-hidden my-4">
          <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
              <div class="font-semibold">Input Realisasi Pemakaian</div>
              <div class="text-xs text-slate-500 mt-1" x-text="realisasiData.name"></div>
            </div>
            <button @click="closeRealisasi()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
          </div>

          <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
              <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Budget Disetujui</div>
                <div class="font-medium text-lg" x-text="rupiah(cap(realisasiData))"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Total Terpakai</div>
                <div class="font-medium text-lg" x-text="rupiah(totalSpent(realisasiData))"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                <div class="text-xs text-slate-500">Sisa Budget</div>
                <div class="font-medium text-lg" :class="remaining(realisasiData) < 0 ? 'text-red-600' : 'text-emerald-600'" x-text="rupiah(remaining(realisasiData))"></div>
              </div>
            </div>

            <!-- Progress bar -->
            <div class="mb-4">
              <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     :style="`width:${progress(realisasiData)}%`"
                     :class="progress(realisasiData)>=90?'bg-rose-500':(progress(realisasiData)>=60?'bg-amber-500':'bg-emerald-500')"></div>
              </div>
              <div class="mt-1 text-xs text-slate-500 text-center" x-text="progress(realisasiData)+'% terpakai'"></div>
            </div>

            <!-- History Realisasi -->
            <div x-show="realisasiData.history && realisasiData.history.length > 0" class="mb-4">
              <label class="text-sm font-medium text-slate-700 mb-2 block">History Realisasi</label>
              <div class="space-y-2 max-h-60 overflow-y-auto">
                <template x-for="h in realisasiData.history" :key="h.id">
                  <div class="p-2 rounded-lg border border-slate-200 bg-slate-50 text-sm">
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="font-medium" x-text="h.nama_komponen"></span>
                        <span class="text-slate-500 mx-1">•</span>
                        <span class="text-slate-600" x-text="h.keterangan"></span>
                      </div>
                      <div class="font-medium text-emerald-600" x-text="rupiah(h.jumlah)"></div>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">
                      <span x-text="formatDate(h.created_at)"></span>
                      <span class="mx-1">•</span>
                      <span x-text="h.user_name || 'User'"></span>
                    </div>
                  </div>
                </template>
              </div>
            </div>

            <!-- Input Realisasi Per Item -->
            <div>
              <label class="text-sm font-medium text-slate-700 mb-2 block">Input Realisasi Per Item</label>
              <div class="space-y-2">
                <template x-for="(detail, idx) in realisasiData.details" :key="detail.id">
                  <div class="p-3 rounded-lg border border-slate-200 hover:bg-slate-50">
                    <div class="flex items-start justify-between mb-2">
                      <div class="flex-1">
                        <div class="font-medium text-sm" x-text="detail.nama_komponen || detail.item"></div>
                        <div class="text-xs text-slate-500 mt-0.5" x-text="detail.deskripsi"></div>
                        <div class="text-xs text-slate-600 mt-1">
                          <span>Budget: </span>
                          <span class="font-medium" x-text="rupiah(detail.nilai_disetujui || detail.budget || 0)"></span>
                          <span class="mx-1">•</span>
                          <span>Terpakai: </span>
                          <span class="font-medium" x-text="rupiah(detail.realisasi_pemakaian || 0)"></span>
                        </div>
                      </div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 items-center">
                      <div class="col-span-7">
                        <input type="text" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               x-model="detail.keterangan_realisasi" 
                               placeholder="Keterangan realisasi (opsional)">
                      </div>
                      <div class="col-span-5">
                        <input type="text" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" 
                               :value="formatNumber(detail.jumlah_realisasi || 0)"
                               @input="updateDetailRealisasi(idx, $event.target.value)"
                               placeholder="Jumlah (Rp)">
                      </div>
                    </div>
                  </div>
                </template>
                <div x-show="!realisasiData.details || realisasiData.details.length === 0" class="text-sm text-slate-500 p-4 bg-slate-50 rounded-lg text-center">
                  Tidak ada item dalam RAB ini.
                </div>
              </div>
            </div>

          </div>

          <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
            <div class="text-sm text-slate-600">
              Total Input Baru: <span class="font-bold text-lg" x-text="rupiah(totalNewRealisasi())"></span>
            </div>
            <div class="flex gap-2">
              <button @click="closeRealisasi()" type="button" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
              <button @click="saveRealisasi()" type="button" class="rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700">Simpan Realisasi</button>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Modal Delete -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20">
      <div @click.outside="toDelete=null" class="w-full max-w-lg bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus RAB?</div>
          <p class="text-slate-600 text-sm mt-1">Data akan dihapus permanen.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="font-medium" x-text="toDelete?.name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="formatDate(toDelete?.created_at)"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" @click="toDelete=null">Batal</button>
          <button class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700" @click="doDelete()">Hapus</button>
        </div>
      </div>
    </div>

  </div>

  <script>
    function rabPage(){
      return {
        items: [],
        outlets: [],
        books: [],
        selectedOutlet: <?php echo e(auth()->user()->outlet_id ?? 'null'); ?>,
        selectedBook: null,
        q: '', status:'ALL', produkTerkait:'ALL',
        sortKey:'created_at', sortDir:'desc',
        showForm:false, form:{}, 
        showRealisasi:false, realisasiData:null,
        showApproval:false, approvalData:null,
        toDelete:null,
        showView:false, viewData:null,
        loading: false,
        isAdmin: <?php echo e(auth()->user() ? 'true' : 'true'); ?>,
        formattedBudget: '',
        formattedApproved: '',
        // Keberangkatan filter (set when coming from package detail page)
        keberangkatanId: null,
        keberangkatanInfo: null,  // {name, code, has_rab, id_rab}

        statusOptions: [
          {value:'DRAFT',            label:'Draft'},
          {value:'PENDING_APPROVAL', label:'Menunggu Persetujuan'},
          {value:'APPROVED',         label:'Disetujui'},
          {value:'APPROVED_WITH_REV',label:'Disetujui dengan Revisi'},
          {value:'REJECTED',         label:'Ditolak'},
          {value:'TRANSFERRED',      label:'Ditransfer'},
        ],

        async init(){
          // Check for keberangkatan_id in URL
          const urlParams = new URLSearchParams(window.location.search);
          const kbId = urlParams.get('keberangkatan_id');
          if (kbId) {
            this.keberangkatanId = kbId;
            // Fetch keberangkatan info to check if RAB exists
            try {
              const res = await fetch(`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${kbId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
              });
              if (res.ok) {
                const kb = await res.json();
                this.keberangkatanInfo = {
                  name: kb.keberangkatan_name,
                  code: kb.keberangkatan_code,
                  has_rab: !!kb.id_rab,
                  id_rab: kb.id_rab
                };
              }
            } catch(e) { console.error('Error fetching keberangkatan info:', e); }
          }
          await this.loadOutlets();
          await this.loadBooks();
          if(!this.selectedBook && this.books.length > 0) {
            this.selectedBook = this.books[0].id;
          }
          await this.loadData();
        },

        async loadOutlets(){
          try {
            const response = await fetch('<?php echo e(route("finance.outlets.data")); ?>');
            const result = await response.json();
            if(result.success){
              this.outlets = result.data;
              // Set default outlet if not already set
              if (!this.selectedOutlet && this.outlets.length > 0) {
                this.selectedOutlet = this.outlets[0].id_outlet;
              }
            }
          } catch(e) {
            console.error('Error loading outlets:', e);
          }
        },

        async loadBooks(){
          try {
            const response = await fetch('<?php echo e(route("finance.accounting-books.data")); ?>?outlet_id=' + this.selectedOutlet);
            const result = await response.json();
            if(result.success){
              this.books = result.data;
              if(!this.selectedBook && this.books.length > 0) {
                this.selectedBook = this.books[0].id;
              }
            }
          } catch(e) {
            console.error('Error loading books:', e);
          }
        },

        async loadData(){
          this.loading = true;
          try {
            let url = '<?php echo e(route("admin.finance.rab.data")); ?>';
            const params = new URLSearchParams();
            if(this.selectedOutlet) params.append('outlet_id', this.selectedOutlet);
            if(this.selectedBook) params.append('book_id', this.selectedBook);
            if(params.toString()) url += '?' + params.toString();
            
            const response = await fetch(url);
            const result = await response.json();
            if(result.success){
              this.items = result.data.map(r => this.normalize(r));
            } else {
              console.error('Failed to load RAB data:', result.message);
            }
          } catch(e) {
            console.error('Error loading RAB data:', e);
          } finally {
            this.loading = false;
          }
        },

        normalize(r){
          // Normalize components to new format {uraian, qty, satuan, harga_satuan, biaya}
          let components = [];
          if(Array.isArray(r.components)){
            components = r.components.map(c => {
              if(typeof c === 'string'){
                // Old format: just string
                return {uraian: c, qty: 1, satuan: 'pcs', harga_satuan: 0, biaya: 0};
              } else if(typeof c === 'object' && c !== null){
                // New format: {uraian, qty, satuan, harga_satuan, biaya}
                return {
                  uraian: String(c.uraian || ''),
                  qty: Number(c.qty || 1),
                  satuan: String(c.satuan || 'pcs'),
                  harga_satuan: Number(c.harga_satuan || c.biaya || 0),
                  biaya: Number(c.biaya || 0)
                };
              }
              return {uraian: '', qty: 1, satuan: 'pcs', harga_satuan: 0, biaya: 0};
            });
          }

          return {
            id: r.id ?? Date.now(),
            created_at: r.created_at ?? new Date().toISOString().slice(0,10),
            name: String(r.name||''),
            description: String(r.description||''),
            components: components,
            budget_total: Number(r.budget_total||0),
            approved_value: Number(r.approved_value||0),
            spends: Array.isArray(r.spends)? r.spends.map(s=>({desc:String(s.desc||''),amount:Number(s.amount||0)})) : [],
            status: r.status || 'DRAFT',
            has_product: Boolean(r.has_product),
            details: Array.isArray(r.details)? r.details : [],
            outlet_id: r.outlet_id || null,
            book_id: r.book_id || null
          }
        },

        // Number formatting helpers
        formatNumber(num){
          if(!num && num !== 0) return '';
          return new Intl.NumberFormat('id-ID').format(num);
        },
        
        parseNumber(str){
          if(!str) return 0;
          return Number(String(str).replace(/\D/g, '')) || 0;
        },

        updateBudget(val){
          this.form.budget_total = this.parseNumber(val);
          this.formattedBudget = this.formatNumber(this.form.budget_total);
        },

        updateApproved(val){
          this.form.approved_value = this.parseNumber(val);
          this.formattedApproved = this.formatNumber(this.form.approved_value);
        },

        updateRealisasiAmount(idx, val){
          this.realisasiData.spends[idx].amount = this.parseNumber(val);
        },

        // filters/sort
        filtered(){
          // If filtering by keberangkatan: only show if RAB exists, and only that RAB
          if (this.keberangkatanId && this.keberangkatanInfo) {
            if (!this.keberangkatanInfo.has_rab) return []; // no RAB yet = empty
            return this.items.filter(r => r.id == this.keberangkatanInfo.id_rab);
          }
          const q=this.q.toLowerCase();
          let list=this.items.filter(r=>{
            const matchesQ = !q || [r.name,r.description,(r.components||[]).join(' '),this.statusLabel(r.status)]
                                .join(' ').toLowerCase().includes(q);
            const matchesS = this.status==='ALL' || r.status===this.status;
            const matchesP = this.produkTerkait==='ALL' || (this.produkTerkait==='YES'? r.has_product : !r.has_product);
            return matchesQ && matchesS && matchesP;
          });
          list.sort((a,b)=>{
            let A=a[this.sortKey], B=b[this.sortKey];
            if(this.sortKey==='created_at'){ A=new Date(A).getTime(); B=new Date(B).getTime(); }
            else if(['budget_total','approved_value'].includes(this.sortKey)){ A=Number(A); B=Number(B); }
            const c = A>B?1:(A<B?-1:0); return this.sortDir==='asc'?c:-c;
          });
          return list;
        },
        resetFilter(){ this.q=''; this.status='ALL'; this.produkTerkait='ALL'; this.sortKey='created_at'; this.sortDir='desc'; },

        // calc & helpers
        rupiah(v){ try{v=Number(v||0)}catch{} return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(v); },
        formatDate(s){ if(!s) return '-'; const d=new Date(s); return d.toLocaleDateString('id-ID',{year:'numeric',month:'short',day:'2-digit'}); },
        totalSpent(r){ return (r.spends||[]).reduce((a,b)=>a+Number(b.amount||0),0); },
        cap(r){ const base = Number(r.approved_value||0) || Number(r.budget_total||0); return Math.max(0, base); },
        remaining(r){ return Math.max(0, this.cap(r) - this.totalSpent(r)); },
        progress(r){ const base=this.cap(r); if(base<=0) return 0; return Math.min(100, Math.round((this.totalSpent(r)/base)*100)); },
        statusLabel(v){
          const map={
            DRAFT:'Draft',
            PENDING_APPROVAL:'Menunggu Persetujuan',
            APPROVED:'Disetujui',
            APPROVED_WITH_REV:'Disetujui dengan Revisi',
            REJECTED:'Ditolak',
            TRANSFERRED:'Ditransfer'
          };
          return map[v]||v;
        },
        statusBadge(v){
          return {
            'DRAFT'             : 'bg-slate-50 text-slate-700 border border-slate-200',
            'PENDING_APPROVAL'  : 'bg-amber-50 text-amber-700 border border-amber-200',
            'APPROVED'          : 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'APPROVED_WITH_REV' : 'bg-blue-50 text-blue-700 border border-blue-200',
            'REJECTED'          : 'bg-rose-50 text-rose-700 border border-rose-200',
            'TRANSFERRED'       : 'bg-indigo-50 text-indigo-700 border border-indigo-200',
          }[v] || 'bg-slate-50 text-slate-700 border border-slate-200';
        },

        // Check if can input realisasi
        canInputRealisasi(r){
          return ['APPROVED', 'APPROVED_WITH_REV', 'TRANSFERRED'].includes(r.status);
        },

        // view / import export
        openView(r){ this.viewData = r; this.showView = true; },
        exportJson(){
          const blob=new Blob([JSON.stringify(this.items,null,2)],{type:'application/json'});
          const url=URL.createObjectURL(blob); const a=document.createElement('a');
          a.href=url; a.download='manajemen_rab.json'; a.click(); URL.revokeObjectURL(url);
        },
        async importJson(ev){
          const f=ev.target.files[0]; if(!f) return;
          const r=new FileReader();
          r.onload= async ()=>{ 
            try{
              const arr=JSON.parse(r.result); 
              if(Array.isArray(arr)){ 
                for(const item of arr){
                  await fetch('<?php echo e(route("admin.finance.rab.store")); ?>', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(item)
                  });
                }
                await this.loadData();
                alert('Import berhasil');
              } else {
                alert('Format JSON tidak valid.');
              }
            }catch(e){ 
              console.error('Import error:', e);
              alert('Gagal membaca file.'); 
            }
            ev.target.value='';
          };
          r.readAsText(f);
        },

        // Component management
        addComponent(){
          if(!Array.isArray(this.form.components)) {
            this.form.components = [];
          }
          this.form.components.push({
            uraian: '', 
            qty: 1, 
            satuan: 'pcs', 
            harga_satuan: 0, 
            biaya: 0
          });
        },

        updateComponentHarga(idx, val){
          this.form.components[idx].harga_satuan = this.parseNumber(val);
          this.recalculateComponentBudget(idx);
        },

        recalculateComponentBudget(idx){
          const c = this.form.components[idx];
          c.biaya = (c.qty || 0) * (c.harga_satuan || 0);
          this.recalculateBudget();
        },

        recalculateBudget(){
          if(!Array.isArray(this.form.components)) {
            this.form.components = [];
          }
          this.form.budget_total = this.form.components.reduce((sum, c) => sum + Number(c.biaya || 0), 0);
        },

        // CRUD - Form RAB
        openForm(){
          this.form = {
            id:null,
            created_at: new Date().toISOString().slice(0,10),
            name:'', 
            description:'', 
            components: [{uraian: '', qty: 1, satuan: 'pcs', harga_satuan: 0, biaya: 0}],
            budget_total:0, 
            approved_value:0, 
            spends: [], 
            status:'DRAFT', 
            has_product:false,
            outlet_id: this.selectedOutlet,
            book_id: this.selectedBook,
            details: []
          };
          this.formattedBudget = '';
          this.formattedApproved = '';
          this.showForm=true;
        },
        
        edit(r){ 
          this.form = JSON.parse(JSON.stringify(this.normalize(r))); 
          if(!Array.isArray(this.form.components)) this.form.components = [];
          if(!Array.isArray(this.form.spends)) this.form.spends = [];
          if(!Array.isArray(this.form.details)) this.form.details = [];
          this.formattedBudget = this.formatNumber(this.form.budget_total);
          this.formattedApproved = this.formatNumber(this.form.approved_value);
          this.showForm=true; 
        },
        
        closeForm(){ 
          this.showForm=false; 
          this.formattedBudget = '';
          this.formattedApproved = '';
        },
        
        async save(){
          if(!this.form.name || !this.form.created_at){ 
            alert('Nama & Tanggal wajib diisi'); 
            return; 
          }
          if(!this.form.outlet_id){ 
            alert('Outlet wajib dipilih'); 
            return; 
          }
          if(!this.form.book_id){ 
            alert('Buku Akuntansi wajib dipilih'); 
            return; 
          }
          if(!Array.isArray(this.form.components) || this.form.components.length === 0){
            alert('Minimal 1 komponen biaya harus diisi');
            return;
          }
          
          // Filter empty components
          this.form.components = this.form.components.filter(c => c && c.uraian && c.uraian.trim());
          
          if(this.form.components.length === 0){
            alert('Komponen biaya tidak boleh kosong');
            return;
          }
          
          // Recalculate budget total
          this.recalculateBudget();
          
          console.log('=== SAVING RAB ===');
          console.log('Form data:', JSON.stringify(this.form, null, 2));
          console.log('Components:', this.form.components);
          
          this.loading = true;
          try {
            const url = this.form.id 
              ? `<?php echo e(url('admin/finance/rab')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.finance.rab.store")); ?>';
            
            const method = this.form.id ? 'PUT' : 'POST';
            
            console.log('Request URL:', url);
            console.log('Request method:', method);
            
            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(this.form)
            });
            
            console.log('Response status:', response.status);
            
            const result = await response.json();
            console.log('Response data:', result);
            
            if(result.success){
              await this.loadData();
              this.showForm = false;
              alert(result.message || 'Data RAB berhasil disimpan');
            } else {
              console.error('Save failed:', result);
              alert(result.message || 'Gagal menyimpan data');
            }
          } catch(e) {
            console.error('Error saving RAB:', e);
            alert('Terjadi kesalahan saat menyimpan data');
          } finally {
            this.loading = false;
          }
        },

        // Realisasi management
        async openRealisasi(r){
          this.realisasiData = JSON.parse(JSON.stringify(this.normalize(r)));
          if(!Array.isArray(this.realisasiData.details)) {
            this.realisasiData.details = [];
          }
          
          // Initialize realisasi fields for each detail
          this.realisasiData.details = this.realisasiData.details.map(detail => ({
            ...detail,
            keterangan_realisasi: '',
            jumlah_realisasi: 0
          }));
          
          // Load history
          await this.loadRealisasiHistory(r.id);
          
          this.showRealisasi = true;
        },

        closeRealisasi(){
          this.showRealisasi = false;
          this.realisasiData = null;
        },

        async loadRealisasiHistory(rabId){
          try {
            const response = await fetch(`<?php echo e(url('admin/finance/rab')); ?>/${rabId}/history`);
            const result = await response.json();
            
            if(result.success && this.realisasiData){
              this.realisasiData.history = result.data || [];
            }
          } catch(e) {
            console.error('Error loading history:', e);
            if(this.realisasiData){
              this.realisasiData.history = [];
            }
          }
        },

        updateDetailRealisasi(idx, val){
          this.realisasiData.details[idx].jumlah_realisasi = this.parseNumber(val);
        },

        totalNewRealisasi(){
          if(!this.realisasiData || !this.realisasiData.details) return 0;
          return this.realisasiData.details.reduce((total, detail) => {
            return total + (detail.jumlah_realisasi || 0);
          }, 0);
        },

        async saveRealisasi(){
          if(!this.realisasiData) return;
          
          console.log('=== SAVING REALISASI PER ITEM ===');
          console.log('Details:', this.realisasiData.details);
          
          // Filter details yang memiliki realisasi (jumlah > 0)
          const realisasiValid = this.realisasiData.details
            .filter(d => d.jumlah_realisasi > 0)
            .map(d => ({
              id_rab_detail: d.id,
              nama_komponen: d.nama_komponen || d.item,
              keterangan: d.keterangan_realisasi || `Realisasi ${d.nama_komponen || d.item}`,
              jumlah: d.jumlah_realisasi
            }));
          
          if(realisasiValid.length === 0){
            alert('Tidak ada realisasi yang diinput');
            return;
          }
          
          console.log('Realisasi valid:', realisasiValid);
          
          this.loading = true;
          try {
            const url = '<?php echo e(route("admin.finance.rab.realisasi-per-item", ["id" => "__ID__"])); ?>'.replace('__ID__', this.realisasiData.id);
            
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify({
                realisasi: realisasiValid
              })
            });
            
            console.log('Response status:', response.status);
            
            const result = await response.json();
            console.log('Response data:', result);
            
            if(result.success){
              // Auto-create expense untuk setiap realisasi yang disimpan
              if(result.realisasi_ids && result.realisasi_ids.length > 0){
                await this.createExpensesFromRealisasi(result.realisasi_ids, realisasiValid);
              }
              
              await this.loadData();
              this.showRealisasi = false;
              this.realisasiData = null;
              alert(result.message || 'Realisasi berhasil disimpan');
            } else {
              console.error('Save failed:', result);
              alert(result.message || 'Gagal menyimpan realisasi');
            }
          } catch(e) {
            console.error('Error saving realisasi:', e);
            alert('Terjadi kesalahan saat menyimpan realisasi');
          } finally {
            this.loading = false;
          }
        },

        async createExpensesFromRealisasi(realisasiIds, realisasiData){
          console.log('=== AUTO-CREATE EXPENSES ===');
          console.log('Realisasi IDs:', realisasiIds);
          console.log('Realisasi Data:', realisasiData);
          
          try {
            // Create expense untuk setiap realisasi
            for(let i = 0; i < realisasiIds.length; i++){
              const realisasiId = realisasiIds[i];
              const realisasi = realisasiData[i];
              
              const expenseData = {
                realisasi_id: realisasiId,
                outlet_id: this.selectedOutlet,
                book_id: this.selectedBook,
                amount: realisasi.jumlah,
                description: realisasi.keterangan,
                expense_date: new Date().toISOString().split('T')[0],
                rab_id: this.realisasiData.id
              };
              
              console.log('Creating expense:', expenseData);
              
              const response = await fetch('<?php echo e(route("admin.finance.expenses.from-realisasi")); ?>', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify(expenseData)
              });
              
              const result = await response.json();
              console.log('Expense creation result:', result);
              
              if(!result.success){
                console.warn('Failed to create expense:', result.message);
              }
            }
            
            console.log('All expenses created successfully');
          } catch(e) {
            console.error('Error creating expenses:', e);
            // Don't alert here, just log - realisasi already saved
          }
        },

        // Approval management
        openApproval(r){
          this.approvalData = JSON.parse(JSON.stringify(this.normalize(r)));
          if(!Array.isArray(this.approvalData.details) || this.approvalData.details.length === 0) {
            alert('Tidak ada detail komponen untuk di-approve');
            return;
          }
          this.approvalData.admin_notes = '';
          this.showApproval = true;
        },

        closeApproval(){
          this.showApproval = false;
          this.approvalData = null;
        },

        updateApprovalHarga(idx, val){
          this.approvalData.details[idx].harga_satuan = this.parseNumber(val);
          this.recalculateApprovalBudget(idx);
        },

        updateApprovalNilai(idx, val){
          this.approvalData.details[idx].nilai_disetujui = this.parseNumber(val);
        },

        recalculateApprovalBudget(idx){
          const detail = this.approvalData.details[idx];
          detail.budget = detail.jumlah * detail.harga_satuan;
        },

        totalApprovalBudget(){
          if(!this.approvalData || !this.approvalData.details) return 0;
          return this.approvalData.details.reduce((sum, d) => sum + Number(d.budget || 0), 0);
        },

        totalApprovalDisetujui(){
          if(!this.approvalData || !this.approvalData.details) return 0;
          return this.approvalData.details.reduce((sum, d) => sum + Number(d.nilai_disetujui || 0), 0);
        },

        async saveApproval(){
          if(!this.approvalData) return;
          
          this.loading = true;
          try {
            // Update RAB dengan details yang sudah di-approve
            const url = `<?php echo e(url('admin/finance/rab')); ?>/${this.approvalData.id}`;
            
            const response = await fetch(url, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify({
                ...this.approvalData,
                approved_value: this.totalApprovalDisetujui()
              })
            });
            
            const result = await response.json();
            
            if(result.success){
              await this.loadData();
              this.showApproval = false;
              this.approvalData = null;
              alert(result.message || 'Approval berhasil disimpan');
            } else {
              alert(result.message || 'Gagal menyimpan approval');
            }
          } catch(e) {
            console.error('Error saving approval:', e);
            alert('Terjadi kesalahan saat menyimpan approval');
          } finally {
            this.loading = false;
          }
        },

        // Delete
        askDelete(r){ this.toDelete=r; },
        async doDelete(){ 
          if(!this.toDelete) return;
          
          this.loading = true;
          try {
            const response = await fetch(`<?php echo e(url('admin/finance/rab')); ?>/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              }
            });
            
            const result = await response.json();
            
            if(result.success){
              await this.loadData();
              this.toDelete = null;
              
              // Show success message with instruction to refresh keberangkatan page
              alert(result.message || 'RAB berhasil dihapus.\n\nJika Anda memiliki halaman Detail Keberangkatan yang terbuka, silakan refresh halaman tersebut (Ctrl+Shift+R) untuk melihat tombol "Buat RAB" muncul kembali.');
            } else {
              alert(result.message || 'Gagal menghapus data');
            }
          } catch(e) {
            console.error('Error deleting RAB:', e);
            alert('Terjadi kesalahan saat menghapus data');
          } finally {
            this.loading = false;
          }
        },
      }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\rab\index.blade.php ENDPATH**/ ?>