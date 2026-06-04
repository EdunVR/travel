<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Investor / Pencairan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Investor / Pencairan')]); ?>
  <div x-data="investorPencairan()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Pencairan Investor</h1>
        <p class="text-slate-600 text-sm">Kelola permintaan pencairan dana investor</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.create')): ?>
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Pencairan
        </button>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.export')): ?>
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari investor, nomor pencairan…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Outlet -->
        <div class="lg:col-span-2">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Outlet: Semua</option>
            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($outlet->id); ?>"><?php echo e($outlet->nama_outlet); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <!-- Filter Investor -->
        <div class="lg:col-span-2">
          <select x-model="investorFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Investor: Semua</option>
            <?php $__currentLoopData = $investors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $investor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($investor->id); ?>"><?php echo e($investor->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <!-- Filter Status -->
        <div class="lg:col-span-2">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="pending">Menunggu</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
            <option value="paid">Dibayar</option>
          </select>
        </div>
        <!-- Filter Tipe -->
        <div class="lg:col-span-2">
          <select x-model="typeFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Tipe: Semua</option>
            <option value="profit_share">Bagi Hasil</option>
            <option value="investment">Investasi</option>
            <option value="both">Keduanya</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <!-- Sort -->
        <div class="grid grid-cols-2 gap-2 lg:col-span-6">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="request_date">Tanggal</option>
            <option value="investor_name">Investor</option>
            <option value="amount">Jumlah</option>
            <option value="type">Tipe</option>
            <option value="status">Status</option>
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
        <template x-for="item in pencairan" :key="item.id">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-money-withdraw text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold truncate" x-text="item.investor_name"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="getStatusClass(item.status)"
                        x-text="getStatusLabel(item.status)"></span>
                </div>
                <div class="text-[12px] text-slate-500 mt-0.5">
                  <span x-text="item.outlet_name"></span> • <span class="font-mono" x-text="item.withdrawal_number"></span>
                </div>
                <div class="mt-2 text-sm">
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 border border-emerald-200">
                    <i class='bx bx-wallet'></i><span x-text="formatCurrency(item.amount)"></span>
                  </span>
                  <div class="mt-1 text-slate-600">
                    Tipe: <span class="font-medium capitalize" x-text="item.type_label || '-'"></span> • 
                    <span x-text="formatDate(item.request_date)"></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <button x-on:click.prevent="viewPencairan(item)" class="flex-1 rounded-lg bg-emerald-600 text-white px-3 py-2 hover:bg-emerald-700 text-sm">
                <i class='bx bx-show'></i> Detail
              </button>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.update')): ?>
              <button x-on:click="openEdit(item)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-edit-alt'></i> Edit
              </button>
              <?php endif; ?>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.delete')): ?>
              <button x-on:click="confirmDelete(item)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-sm">
                <i class='bx bx-trash'></i> Hapus
              </button>
              <?php endif; ?>
            </div>
          </div>
        </template>
      </div>
      <div x-show="pencairan.length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3 w-12">No</th>
              <th class="text-left px-4 py-3">No. Pencairan</th>
              <th class="text-left px-4 py-3">Outlet</th>
              <th class="text-left px-4 py-3">Investor</th>
              <th class="text-left px-4 py-3">Tanggal</th>
              <th class="text-left px-4 py-3">Jumlah</th>
              <th class="text-left px-4 py-3">Tipe</th>
              <th class="text-left px-4 py-3">Status</th>
              <th class="text-left px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(item,i) in pencairan" :key="item.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="i+1"></td>
                <td class="px-4 py-3">
                  <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded" x-text="item.withdrawal_number"></span>
                </td>
                <td class="px-4 py-3" x-text="item.outlet_name"></td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="font-medium" x-text="item.investor_name"></span>
                  </div>
                </td>
                <td class="px-4 py-3" x-text="formatDate(item.request_date)"></td>
                <td class="px-4 py-3">
                  <span class="text-green-600 font-medium" x-text="formatCurrency(item.amount)"></span>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 rounded bg-primary-600 text-white text-xs capitalize" x-text="item.type_label"></span>
                </td>
                <td class="px-4 py-3">
                  <span :class="getStatusClass(item.status)" 
                        class="px-2 py-0.5 rounded-full text-xs border" 
                        x-text="getStatusLabel(item.status)"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    <button x-on:click.prevent="viewPencairan(item)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700 text-sm">
                      <i class='bx bx-show'></i> Detail
                    </button>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.update')): ?>
                    <button x-on:click="openEdit(item)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                      <i class='bx bx-edit-alt'></i>
                    </button>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('investor.pencairan.delete')): ?>
                    <button x-on:click="confirmDelete(item)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="pencairan.length===0"><td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Pencairan' : 'Tambah Pencairan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.outlet_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Outlet —</option>
                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($outlet->id); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div x-show="errors.outlet_id" class="text-red-500 text-xs mt-1" x-text="errors.outlet_id"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Investor <span class="text-red-500">*</span></label>
              <select x-model="form.investor_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Investor —</option>
                <?php $__currentLoopData = $investors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $investor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($investor->id); ?>"><?php echo e($investor->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div x-show="errors.investor_id" class="text-red-500 text-xs mt-1" x-text="errors.investor_id"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Tanggal Permintaan <span class="text-red-500">*</span></label>
              <input type="date" x-model="form.request_date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.request_date" class="text-red-500 text-xs mt-1" x-text="errors.request_date"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Jumlah <span class="text-red-500">*</span></label>
              <input type="number" min="1" step="0.01" x-model="form.amount" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.amount" class="text-red-500 text-xs mt-1" x-text="errors.amount"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Tipe Pencairan <span class="text-red-500">*</span></label>
              <select x-model="form.type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">— Pilih Tipe —</option>
                <option value="profit_share">Bagi Hasil</option>
                <option value="investment">Investasi</option>
                <option value="both">Keduanya</option>
              </select>
              <div x-show="errors.type" class="text-red-500 text-xs mt-1" x-text="errors.type"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Alasan Pencairan</label>
              <textarea x-model="form.reason" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Jelaskan alasan pencairan..."></textarea>
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

    <!-- Modal Detail -->
    <div x-show="showDetailModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="showDetailModal=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate">Detail Pencairan</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click.stop="showDetailModal=false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>
        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1" x-show="selectedPencairan">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-slate-500">No. Pencairan</label>
              <p class="mt-1 text-sm text-slate-900 font-mono" x-text="selectedPencairan?.withdrawal_number || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Status</label>
              <p class="mt-1 text-sm text-slate-900" x-text="getStatusLabel(selectedPencairan?.status)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Outlet</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.outlet_name || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Investor</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.investor_name || '-'"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Tanggal Permintaan</label>
              <p class="mt-1 text-sm text-slate-900" x-text="formatDate(selectedPencairan?.request_date)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Jumlah</label>
              <p class="mt-1 text-sm text-slate-900 font-medium" x-text="formatCurrency(selectedPencairan?.amount || 0)"></p>
            </div>
            <div>
              <label class="text-sm font-medium text-slate-500">Tipe</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.type_label || '-'"></p>
            </div>
            <div x-show="selectedPencairan?.payment_method">
              <label class="text-sm font-medium text-slate-500">Metode Pembayaran</label>
              <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.payment_method || '-'"></p>
            </div>
          </div>
          
          <div class="mt-4" x-show="selectedPencairan?.reason">
            <label class="text-sm font-medium text-slate-500">Alasan</label>
            <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.reason"></p>
          </div>
          
          <div class="mt-4" x-show="selectedPencairan?.notes">
            <label class="text-sm font-medium text-slate-500">Catatan</label>
            <p class="mt-1 text-sm text-slate-900" x-text="selectedPencairan?.notes"></p>
          </div>
          
          <div class="mt-4" x-show="selectedPencairan?.rejection_reason">
            <label class="text-sm font-medium text-slate-500">Alasan Penolakan</label>
            <p class="mt-1 text-sm text-red-600" x-text="selectedPencairan?.rejection_reason"></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Pencairan?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.investor_name"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="'No: ' + (toDelete?.withdrawal_number || '-') + ' • Jumlah: ' + formatCurrency(toDelete?.amount || 0)"></div>
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
    function investorPencairan(){
      return {
        // State management
        pencairan: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        outletFilter: 'ALL',
        investorFilter: 'ALL',
        statusFilter: 'ALL',
        typeFilter: 'ALL',
        sortKey: 'request_date',
        sortDir: 'desc',
        view: 'table',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          outlet_id: '', 
          investor_id: '', 
          request_date: new Date().toISOString().split('T')[0],
          amount: 0,
          type: '',
          reason: ''
        },
        errors: {},
        
        // Delete confirmation
        toDelete: null,

        // Detail modal
        showDetailModal: false,
        selectedPencairan: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          try {
            await this.fetchData();
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              investor_filter: this.investorFilter,
              status_filter: this.statusFilter,
              type_filter: this.typeFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`<?php echo e(route('admin.investor.pencairan.index')); ?>?${params}`);
            const data = await response.json();
            
            if (data.success) {
              this.pencairan = data.data.map(item => ({
                id: item.id,
                outlet_id: item.outlet_id,
                outlet_name: item.outlet_name || item.outlet?.nama_outlet || '-',
                investor_id: item.investor_id,
                investor_name: item.investor_name || item.investor?.name || '-',
                withdrawal_number: item.withdrawal_number || '-',
                request_date: item.request_date,
                amount: item.amount || 0,
                type: item.type || '',
                type_label: item.type_label || this.getTypeLabel(item.type),
                status: item.status || 'pending',
                reason: item.reason || '',
                notes: item.notes || '',
                payment_method: item.payment_method || '',
                rejection_reason: item.rejection_reason || ''
              }));
            }
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        openCreate(){ 
          this.form = { 
            id: null, 
            outlet_id: '', 
            investor_id: '', 
            request_date: new Date().toISOString().split('T')[0],
            amount: 0,
            type: '',
            reason: ''
          }; 
          this.errors = {};
          this.showForm = true; 
        },

        openEdit(item){ 
          this.form = { 
            id: item.id,
            outlet_id: item.outlet_id,
            investor_id: item.investor_id,
            request_date: item.request_date,
            amount: item.amount,
            type: item.type,
            reason: item.reason
          }; 
          this.errors = {};
          this.showForm = true; 
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `<?php echo e(route('admin.investor.pencairan.index')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.investor.pencairan.store")); ?>';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
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
            const response = await fetch(`<?php echo e(route('admin.investor.pencairan.index')); ?>/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        viewPencairan(item) {
          this.selectedPencairan = item;
          this.showDetailModal = true;
        },

        getStatusClass(status) {
          switch(status) {
            case 'approved': return 'bg-green-50 text-green-700 border-green-200';
            case 'paid': return 'bg-blue-50 text-blue-700 border-blue-200';
            case 'rejected': return 'bg-red-50 text-red-700 border-red-200';
            case 'pending': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
            default: return 'bg-slate-50 text-slate-600 border-slate-200';
          }
        },

        getStatusLabel(status) {
          switch(status) {
            case 'approved': return 'Disetujui';
            case 'paid': return 'Dibayar';
            case 'rejected': return 'Ditolak';
            case 'pending': return 'Menunggu';
            default: return 'Unknown';
          }
        },

        getTypeLabel(type) {
          switch(type) {
            case 'profit_share': return 'Bagi Hasil';
            case 'investment': return 'Investasi';
            case 'both': return 'Keduanya';
            default: return '-';
          }
        },

        formatDate(dateString) {
          if (!dateString) return '-';
          const date = new Date(dateString);
          const day = String(date.getDate()).padStart(2, '0');
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const year = date.getFullYear();
          return `${day}/${month}/${year}`;
        },

        formatCurrency(amount) {
          const num = parseFloat(amount || 0);
          return 'Rp ' + Math.round(num).toLocaleString('id-ID');
        },

        exportPdf(){
          const params = new URLSearchParams({
            outlet_filter: this.outletFilter,
            investor_filter: this.investorFilter,
            status_filter: this.statusFilter,
            type_filter: this.typeFilter
          });
          window.open(`<?php echo e(route('admin.investor.pencairan.export')); ?>?${params}&format=pdf`, '_blank');
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet_filter: this.outletFilter,
            investor_filter: this.investorFilter,
            status_filter: this.statusFilter,
            type_filter: this.typeFilter
          });
          window.open(`<?php echo e(route('admin.investor.pencairan.export')); ?>?${params}&format=excel`, '_blank');
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
                            body: JSON.stringify({ notes: this.approvalNotes })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.closeApprovalModal();
                            this.loadData();
                            this.showNotification(result.message, 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Terjadi kesalahan', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async confirmRejection() {
                    this.loading = true;
                    try {
                        const response = await fetch(`<?php echo e(route("admin.investor.pencairan.index")); ?>/${this.approvalId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ rejection_reason: this.rejectionReason })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.closeRejectionModal();
                            this.loadData();
                            this.showNotification(result.message, 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Terjadi kesalahan', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async confirmPayment() {
                    this.loading = true;
                    try {
                        const response = await fetch(`<?php echo e(route("admin.investor.pencairan.index")); ?>/${this.approvalId}/mark-as-paid`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ 
                                payment_method: this.paymentMethod,
                                payment_reference: this.paymentReference
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.closePaymentModal();
                            this.loadData();
                            this.showNotification(result.message, 'success');
                        } else {
                            this.showNotification(result.message, 'error');
                        }
                    } catch (error) {
                        this.showNotification('Terjadi kesalahan', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async exportData() {
                    const params = new URLSearchParams();
                    if (this.selectedOutlet) params.append('outlet_id', this.selectedOutlet);
                    if (this.selectedInvestor) params.append('investor_id', this.selectedInvestor);
                    if (this.selectedStatus) params.append('status', this.selectedStatus);
                    if (this.selectedType) params.append('type', this.selectedType);
                    
                    window.open(`<?php echo e(route("admin.investor.pencairan.export")); ?>?${params.toString()}`);
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    return new Date(dateString).toLocaleDateString('id-ID');
                },

                showNotification(message, type = 'info') {
                    alert(message);
                }
            }
        }

        // Global functions for DataTable actions
        function viewWithdrawal(id) {
            Alpine.store('investorPencairan').viewWithdrawal(id);
        }

        function editWithdrawal(id) {
            Alpine.store('investorPencairan').editWithdrawal(id);
        }

        function approveWithdrawal(id) {
            Alpine.store('investorPencairan').approveWithdrawal(id);
        }

        function rejectWithdrawal(id) {
            Alpine.store('investorPencairan').rejectWithdrawal(id);
        }

        function markAsPaid(id) {
            Alpine.store('investorPencairan').markAsPaid(id);
        }

        function deleteWithdrawal(id) {
            Alpine.store('investorPencairan').deleteWithdrawal(id);
        }

        // Add to Alpine store for global access
        document.addEventListener('alpine:init', () => {
            Alpine.store('investorPencairan', {
                async viewWithdrawal(id) {
                    try {
                        const response = await fetch(`<?php echo e(route("admin.investor.pencairan.index")); ?>/${id}`);
                        const result = await response.json();
                        
                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                            component.viewData = result.data;
                            component.showViewModal = true;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                },

                async editWithdrawal(id) {
                    try {
                        const response = await fetch(`<?php echo e(route("admin.investor.pencairan.index")); ?>/${id}/edit`);
                        const result = await response.json();
                        
                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                            component.modalTitle = 'Edit Pencairan';
                            component.form = result.data;
                            component.showModal = true;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                },

                approveWithdrawal(id) {
                    const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                    component.approvalId = id;
                    component.showApprovalModal = true;
                },

                rejectWithdrawal(id) {
                    const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                    component.approvalId = id;
                    component.showRejectionModal = true;
                },

                markAsPaid(id) {
                    const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                    component.approvalId = id;
                    component.showPaymentModal = true;
                },

                async deleteWithdrawal(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus pencairan ini?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`<?php echo e(route("admin.investor.pencairan.index")); ?>/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            const component = Alpine.$data(document.querySelector('[x-data="investorPencairan()"]'));
                            component.loadData();
                            alert(result.message);
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan');
                    }
                }
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal)): ?>
<?php $attributes = $__attributesOriginal; ?>
<?php unset($__attributesOriginal); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal)): ?>
<?php $component = $__componentOriginal; ?>
<?php unset($__componentOriginal); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\investor\pencairan\index.blade.php ENDPATH**/ ?>