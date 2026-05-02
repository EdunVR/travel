<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Bandara']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Bandara')]); ?>
  <div x-data="airportCrud()" x-init="init()" class="space-y-4 self-start w-full">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">Data Bandara</h1>
        <p class="text-slate-600 text-sm">Kelola daftar bandara keberangkatan dan kedatangan.</p>
      </div>
      <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 text-sm">
        <i class='bx bx-plus-circle'></i> Tambah Bandara
      </button>
    </div>

    <div class="relative max-w-sm">
      <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
      <input x-model="search" x-on:input.debounce.400ms="fetchData()" placeholder="Cari bandara, kode, kota…"
             class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200 text-sm">
    </div>

    <div x-show="loading" class="text-center py-8 text-slate-500"><i class='bx bx-loader-alt bx-spin text-xl'></i></div>

    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Kode IATA</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nama Bandara</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Kota</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Negara</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-600 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <template x-for="a in airports" :key="a.id">
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-mono font-semibold text-primary-700" x-text="a.iata_code"></td>
              <td class="px-4 py-3" x-text="a.name"></td>
              <td class="px-4 py-3 text-slate-600" x-text="a.city"></td>
              <td class="px-4 py-3 text-slate-600" x-text="a.country || '-'"></td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="a.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                      x-text="a.is_active ? 'Aktif' : 'Nonaktif'"></span>
              </td>
              <td class="px-4 py-3">
                <div class="flex justify-end gap-1">
                  <button x-on:click="openEdit(a)" class="p-1.5 rounded-lg hover:bg-slate-100" title="Edit">
                    <i class='bx bx-edit text-blue-600'></i>
                  </button>
                  <button x-on:click="confirmDelete(a)" class="p-1.5 rounded-lg hover:bg-red-50" title="Hapus">
                    <i class='bx bx-trash text-red-600'></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>
          <tr x-show="airports.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data bandara</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-16 overflow-y-auto">
      <div x-on:click.outside="showForm=false" class="w-full max-w-md bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="form.id ? 'Edit Bandara' : 'Tambah Bandara'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showForm=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode IATA <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.iata_code" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm uppercase" placeholder="CGK" maxlength="10">
              <div x-show="errors.iata_code" class="text-red-500 text-xs mt-1" x-text="errors.iata_code"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Kota <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.city" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Jakarta">
              <div x-show="errors.city" class="text-red-500 text-xs mt-1" x-text="errors.city"></div>
            </div>
          </div>
          <div>
            <label class="text-sm text-slate-600">Nama Bandara <span class="text-red-500">*</span></label>
            <input type="text" x-model.trim="form.name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Soekarno-Hatta International Airport">
            <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
          </div>
          <div>
            <label class="text-sm text-slate-600">Negara</label>
            <input type="text" x-model.trim="form.country" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Indonesia">
          </div>
          <div x-show="form.id">
            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
              <input type="checkbox" x-model="form.is_active" class="rounded">
              <span>Aktif</span>
            </label>
          </div>
        </div>
        <div class="px-5 pb-4 pt-2 border-t border-slate-100 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50" x-on:click="showForm=false">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving"
                  class="rounded-xl bg-primary-600 text-white px-4 py-2 text-sm hover:bg-primary-700 disabled:opacity-50">
            <span x-show="saving"><i class='bx bx-loader-alt bx-spin'></i></span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-16">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-sm bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Bandara?</div>
          <p class="text-slate-600 mt-1 text-sm" x-text="toDelete?.iata_code + ' - ' + toDelete?.name"></p>
        </div>
        <div class="px-5 pb-4 border-t border-slate-100 pt-3 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting"
                  class="rounded-xl bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700 disabled:opacity-50">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <?php $__env->startPush('scripts'); ?>
  <script>
  function airportCrud() {
    return {
      airports: [], loading: false, saving: false, deleting: false,
      search: '', showForm: false, toDelete: null,
      form: { id: null, iata_code: '', name: '', city: '', country: '', is_active: true },
      errors: {},

      async init() { await this.fetchData(); },

      async fetchData() {
        this.loading = true;
        try {
          const res = await fetch(`<?php echo e(route('admin.inventaris.airport.data')); ?>?search=${encodeURIComponent(this.search)}`);
          const data = await res.json();
          this.airports = data.data || [];
        } finally { this.loading = false; }
      },

      openCreate() {
        this.form = { id: null, iata_code: '', name: '', city: '', country: '', is_active: true };
        this.errors = {}; this.showForm = true;
      },

      openEdit(a) {
        this.form = { id: a.id, iata_code: a.iata_code, name: a.name, city: a.city, country: a.country || '', is_active: a.is_active };
        this.errors = {}; this.showForm = true;
      },

      async submitForm() {
        this.saving = true; this.errors = {};
        try {
          const url = this.form.id
            ? `<?php echo e(url('admin/inventaris/airport')); ?>/${this.form.id}`
            : `<?php echo e(route('admin.inventaris.airport.store')); ?>`;
          const res = await fetch(url, {
            method: this.form.id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify(this.form)
          });
          const data = await res.json();
          if (res.ok) { this.showForm = false; await this.fetchData(); }
          else { this.errors = data.errors || {}; }
        } finally { this.saving = false; }
      },

      confirmDelete(a) { this.toDelete = a; },

      async deleteNow() {
        this.deleting = true;
        try {
          await fetch(`<?php echo e(url('admin/inventaris/airport')); ?>/${this.toDelete.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
          });
          this.toDelete = null; await this.fetchData();
        } finally { this.deleting = false; }
      }
    };
  }
  </script>
  <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/inventaris/airport/index.blade.php ENDPATH**/ ?>