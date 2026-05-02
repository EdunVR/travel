<x-layouts.admin :title="'Master / Transportasi Saudi'">
  <div x-data="transportCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Transportasi Saudi</h1>
        <p class="text-slate-600 text-sm">Kelola data kereta cepat & bus untuk paket travel.</p>
      </div>
      <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
        <i class='bx bx-plus-circle text-lg'></i> Tambah Transportasi
      </button>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
      <div class="lg:col-span-4">
        <div class="relative">
          <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
          <input x-model="search" x-on:input.debounce.400ms="fetchData()" placeholder="Cari nama, kode, operator…"
                 class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
        </div>
      </div>
      <div class="lg:col-span-3">
        <select x-model="typeFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2">
          <option value="ALL">Semua Tipe</option>
          <option value="kereta_cepat">Kereta Cepat (Haramain)</option>
          <option value="bus">Bus</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="text-center py-8">
      <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
    </div>

    <!-- Table -->
    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Kode</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Tipe</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Rute</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Operator</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Harga/Orang</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase">Host Seller</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-slate-700 uppercase w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="t in transports" :key="t.id">
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-mono text-xs" x-text="t.transport_code"></td>
                <td class="px-4 py-3 font-medium" x-text="t.transport_name"></td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 text-xs rounded-full"
                        :class="t.transport_type === 'kereta_cepat' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
                        x-text="t.type_label"></span>
                </td>
                <td class="px-4 py-3 text-sm" x-text="t.route || '-'"></td>
                <td class="px-4 py-3 text-sm" x-text="t.operator"></td>
                <td class="px-4 py-3 font-semibold text-primary-700" x-text="t.price_formatted"></td>
                <td class="px-4 py-3 text-xs">
                  <div x-text="t.seller_name"></div>
                  <div class="text-slate-400" x-text="t.seller_phone"></div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-1">
                    <button x-on:click="openEdit(t)" class="p-1.5 rounded-lg hover:bg-slate-100" title="Edit">
                      <i class='bx bx-edit text-lg text-blue-600'></i>
                    </button>
                    <button x-on:click="confirmDelete(t)" class="p-1.5 rounded-lg hover:bg-slate-100" title="Hapus">
                      <i class='bx bx-trash text-lg text-red-600'></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="transports.length === 0">
              <td colspan="8" class="px-4 py-10 text-center text-slate-400">Belum ada data transportasi</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="form.id ? 'Edit Transportasi' : 'Tambah Transportasi'"></div>
          <button class="p-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Nama Transportasi <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.transport_name" placeholder="Haramain Express / Bus Makkah-Madinah" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.transport_name" class="text-red-500 text-xs mt-1" x-text="errors.transport_name"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Tipe <span class="text-red-500">*</span></label>
              <select x-model="form.transport_type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="kereta_cepat">Kereta Cepat (Haramain)</option>
                <option value="bus">Bus</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-600">Operator</label>
              <input type="text" x-model.trim="form.operator" placeholder="Saudi Railways / SAPTCO" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Dari</label>
              <input type="text" x-model.trim="form.route_from" placeholder="Makkah" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Ke</label>
              <input type="text" x-model.trim="form.route_to" placeholder="Madinah" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Harga per Orang <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="form.price_per_person" min="0" placeholder="500000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.price_per_person" class="text-red-500 text-xs mt-1" x-text="errors.price_per_person"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Outlet</label>
              <select x-model="form.id_outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Host Seller</label>
              <input type="text" x-model.trim="form.seller_name" placeholder="Nama agen" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Telepon Host Seller</label>
              <input type="text" x-model.trim="form.seller_phone" placeholder="08xxxxxxxxxx" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Catatan</label>
              <textarea x-model.trim="form.notes" rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Keterangan tambahan"></textarea>
            </div>
          </div>
        </div>
        <div class="px-5 pb-4 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
            <span x-show="saving"><i class='bx bx-loader-alt bx-spin'></i> Menyimpan...</span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Transportasi?</div>
          <p class="text-slate-600 mt-1 text-sm" x-text="'Yakin hapus: ' + (toDelete?.transport_name ?? '') + '?'"></p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deleting"><i class='bx bx-loader-alt bx-spin'></i></span>
            <span x-show="!deleting">Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
      <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
           class="px-4 py-3 rounded-xl border shadow-lg">
        <span x-text="toastMessage"></span>
      </div>
    </div>
  </div>

  <script>
    function transportCrud() {
      return {
        transports: [], loading: false, saving: false, deleting: false,
        search: '', typeFilter: 'ALL',
        showForm: false, toDelete: null,
        showToast: false, toastMessage: '', toastType: 'success',
        form: { id: null, transport_name: '', transport_type: 'bus', route_from: '', route_to: '', operator: '', price_per_person: 0, seller_name: '', seller_phone: '', notes: '', id_outlet: '' },
        errors: {},

        async init() { await this.fetchData(); },

        async fetchData() {
          this.loading = true;
          try {
            const params = new URLSearchParams({ search: this.search, type_filter: this.typeFilter });
            const res = await fetch(`{{ route('admin.inventaris.transport.data') }}?${params}`);
            const data = await res.json();
            this.transports = data.data;
          } catch(e) { this.toast('Gagal memuat data', 'error'); }
          finally { this.loading = false; }
        },

        openCreate() {
          this.form = { id: null, transport_name: '', transport_type: 'bus', route_from: '', route_to: '', operator: '', price_per_person: 0, seller_name: '', seller_phone: '', notes: '', id_outlet: '' };
          this.errors = {}; this.showForm = true;
        },

        async openEdit(t) {
          const res = await fetch(`{{ route('admin.inventaris.transport.show', '') }}/${t.id}`);
          const data = await res.json();
          this.form = { id: data.id, transport_name: data.transport_name, transport_type: data.transport_type, route_from: data.route_from || '', route_to: data.route_to || '', operator: data.operator || '', price_per_person: data.price_per_person, seller_name: data.seller_name || '', seller_phone: data.seller_phone || '', notes: data.notes || '', id_outlet: data.id_outlet || '' };
          this.errors = {}; this.showForm = true;
        },

        closeForm() { this.showForm = false; this.errors = {}; },

        async submitForm() {
          this.saving = true; this.errors = {};
          try {
            const url = this.form.id ? `{{ route('admin.inventaris.transport.update', '') }}/${this.form.id}` : '{{ route("admin.inventaris.transport.store") }}';
            const method = this.form.id ? 'PUT' : 'POST';
            const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(this.form) });
            const result = await res.json();
            if (res.ok) { this.toast(result.message, 'success'); this.closeForm(); await this.fetchData(); }
            else { if (result.errors) this.errors = Object.fromEntries(Object.entries(result.errors).map(([k,v]) => [k, Array.isArray(v) ? v[0] : v])); }
          } catch(e) { this.toast('Gagal menyimpan', 'error'); }
          finally { this.saving = false; }
        },

        confirmDelete(t) { this.toDelete = t; },

        async deleteNow() {
          this.deleting = true;
          try {
            const res = await fetch(`{{ route('admin.inventaris.transport.destroy', '') }}/${this.toDelete.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            const result = await res.json();
            if (res.ok) { this.toast(result.message, 'success'); this.toDelete = null; await this.fetchData(); }
            else { this.toast(result.message || 'Gagal menghapus', 'error'); }
          } catch(e) { this.toast('Gagal menghapus', 'error'); }
          finally { this.deleting = false; }
        },

        toast(msg, type = 'success') {
          this.toastMessage = msg; this.toastType = type; this.showToast = true;
          setTimeout(() => this.showToast = false, 3000);
        }
      };
    }
  </script>
</x-layouts.admin>
