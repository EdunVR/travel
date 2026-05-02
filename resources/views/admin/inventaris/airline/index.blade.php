<x-layouts.admin :title="'Travel / Maskapai'">
  <div x-data="airlineCrud()" x-init="init()" class="space-y-4 self-start w-full">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">Data Maskapai</h1>
        <p class="text-slate-600 text-sm">Kelola daftar maskapai penerbangan.</p>
      </div>
      <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 text-sm">
        <i class='bx bx-plus-circle'></i> Tambah Maskapai
      </button>
    </div>

    <!-- Search -->
    <div class="relative max-w-sm">
      <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
      <input x-model="search" x-on:input.debounce.400ms="fetchData()" placeholder="Cari maskapai…"
             class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200 text-sm">
    </div>

    <!-- Loading -->
    <div x-show="loading" class="text-center py-8 text-slate-500"><i class='bx bx-loader-alt bx-spin text-xl'></i></div>

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nama Maskapai</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Kode IATA</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Negara</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-600 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <template x-for="a in airlines" :key="a.id">
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-medium" x-text="a.name"></td>
              <td class="px-4 py-3 font-mono text-slate-600" x-text="a.iata_code || '-'"></td>
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
          <tr x-show="airlines.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data maskapai</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-16 overflow-y-auto">
      <div x-on:click.outside="showForm=false" class="w-full max-w-md bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="form.id ? 'Edit Maskapai' : 'Tambah Maskapai'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="showForm=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 space-y-3">
          <div>
            <label class="text-sm text-slate-600">Nama Maskapai <span class="text-red-500">*</span></label>
            <input type="text" x-model.trim="form.name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Garuda Airlines">
            <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Kode IATA</label>
              <input type="text" x-model.trim="form.iata_code" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm uppercase" placeholder="GA" maxlength="10">
            </div>
            <div>
              <label class="text-sm text-slate-600">Negara</label>
              <input type="text" x-model.trim="form.country" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Indonesia">
            </div>
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
          <div class="font-semibold">Hapus Maskapai?</div>
          <p class="text-slate-600 mt-1 text-sm" x-text="toDelete?.name"></p>
        </div>
        <div class="px-5 pb-4 border-t border-slate-100 pt-3 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting"
                  class="rounded-xl bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700 disabled:opacity-50">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  function airlineCrud() {
    return {
      airlines: [], loading: false, saving: false, deleting: false,
      search: '', showForm: false, toDelete: null,
      form: { id: null, name: '', iata_code: '', country: '', is_active: true },
      errors: {},

      async init() { await this.fetchData(); },

      async fetchData() {
        this.loading = true;
        try {
          const res = await fetch(`{{ route('admin.inventaris.airline.data') }}?search=${encodeURIComponent(this.search)}`);
          const data = await res.json();
          this.airlines = data.data || [];
        } finally { this.loading = false; }
      },

      openCreate() {
        this.form = { id: null, name: '', iata_code: '', country: '', is_active: true };
        this.errors = {}; this.showForm = true;
      },

      openEdit(a) {
        this.form = { id: a.id, name: a.name, iata_code: a.iata_code || '', country: a.country || '', is_active: a.is_active };
        this.errors = {}; this.showForm = true;
      },

      async submitForm() {
        this.saving = true; this.errors = {};
        try {
          const url = this.form.id
            ? `{{ url('admin/inventaris/airline') }}/${this.form.id}`
            : `{{ route('admin.inventaris.airline.store') }}`;
          const res = await fetch(url, {
            method: this.form.id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
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
          await fetch(`{{ url('admin/inventaris/airline') }}/${this.toDelete.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
          });
          this.toDelete = null; await this.fetchData();
        } finally { this.deleting = false; }
      }
    };
  }
  </script>
  @endpush
</x-layouts.admin>
