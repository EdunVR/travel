<x-layouts.admin :title="'Kelola Manifest'">
  <div x-data="manifestManager()" x-init="init()" class="space-y-4 self-start w-full">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div class="flex items-center gap-2">
        <button onclick="window.history.back()" class="p-2 hover:bg-slate-100 rounded-lg">
          <i class='bx bx-arrow-back text-xl'></i>
        </button>
        <div>
          <h1 class="text-xl font-bold">Kelola Manifest</h1>
          <p class="text-slate-500 text-sm" x-text="keberangkatan.keberangkatan_name + ' — ' + (keberangkatan.departure_date || '')"></p>
        </div>
      </div>
      <div class="flex gap-2 flex-wrap">
        <button x-on:click="markBerdekatan()" :disabled="selectedRows.length < 2"
                class="inline-flex items-center gap-1 rounded-xl border border-green-300 text-green-700 bg-green-50 px-3 py-2 hover:bg-green-100 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
          <i class='bx bx-link'></i> Tandai Berdekatan
        </button>
        <button x-on:click="clearBerdekatan()" :disabled="selectedRows.length === 0"
                class="inline-flex items-center gap-1 rounded-xl border border-slate-300 text-slate-700 bg-white px-3 py-2 hover:bg-slate-50 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
          <i class='bx bx-unlink'></i> Hapus Label
        </button>
        <button x-on:click="saveOrder()" :disabled="saving"
                class="inline-flex items-center gap-1 rounded-xl bg-green-600 text-white px-3 py-2 hover:bg-green-700 text-sm disabled:opacity-50">
          <i class='bx bx-save'></i>
          <span x-text="saving ? 'Menyimpan...' : 'Simpan Urutan'"></span>
        </button>
        <button x-on:click="downloadPdf()"
                class="inline-flex items-center gap-1 rounded-xl bg-red-600 text-white px-3 py-2 hover:bg-red-700 text-sm">
          <i class='bx bx-file-pdf'></i> Download PDF
        </button>
        <button x-on:click="downloadExcel()"
                class="inline-flex items-center gap-1 rounded-xl bg-green-700 text-white px-3 py-2 hover:bg-green-800 text-sm">
          <i class='bx bx-spreadsheet'></i> Download Excel
        </button>
      </div>
    </div>

    <!-- Info -->
    <div class="flex items-center gap-4 text-sm text-slate-600 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
      <i class='bx bx-info-circle text-blue-500 text-lg'></i>
      <span>Drag & drop baris untuk mengatur urutan manifest. Pilih beberapa baris lalu klik "Tandai Berdekatan" untuk mengelompokkan.</span>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="text-center py-12 text-slate-500">
      <i class='bx bx-loader-alt bx-spin text-3xl'></i>
      <p class="mt-2 text-sm">Memuat data jamaah...</p>
    </div>

    <!-- Manifest Table -->
    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-100 text-slate-700 text-xs uppercase tracking-wide">
              <th class="px-2 py-3 text-center w-8">
                <input type="checkbox" x-on:change="toggleSelectAll($event)" class="rounded">
              </th>
              <th class="px-2 py-3 text-center w-8"></th>
              <th class="px-3 py-3 text-center w-12">NO</th>
              <th class="px-3 py-3 text-left w-16">TITLE</th>
              <th class="px-3 py-3 text-left">FULL NAME</th>
              <th class="px-3 py-3 text-center w-16">GENDER</th>
              <th class="px-3 py-3 text-left w-36">NO PASSPORT</th>
              <th class="px-3 py-3 text-left w-40">RELATION</th>
              <th class="px-3 py-3 text-center w-24">GROUP</th>
            </tr>
          </thead>
          <tbody id="manifest-sortable">
            <template x-for="(row, idx) in manifestRows" :key="row.id">
              <tr class="border-t border-slate-100 hover:bg-slate-50 transition-colors cursor-grab"
                  :class="{
                    'border-l-4 border-l-green-500 bg-green-50/50': row.group_label === 'BERDEKATAN',
                    'bg-blue-50/30': selectedRows.includes(row.id)
                  }"
                  :data-row-id="row.id"
                  x-on:click="toggleSelect(row.id, $event)">
                <!-- Checkbox -->
                <td class="px-2 py-2.5 text-center" x-on:click.stop>
                  <input type="checkbox" :checked="selectedRows.includes(row.id)"
                         x-on:change="toggleSelect(row.id, $event)" class="rounded">
                </td>
                <!-- Drag Handle -->
                <td class="px-2 py-2.5 text-center text-slate-400 cursor-grab drag-handle">
                  <span class="text-lg">≡</span>
                </td>
                <!-- NO -->
                <td class="px-3 py-2.5 text-center font-mono text-slate-600" x-text="idx + 1"></td>
                <!-- TITLE -->
                <td class="px-3 py-2.5 font-medium" x-text="row.title"></td>
                <!-- FULL NAME -->
                <td class="px-3 py-2.5 font-medium" x-text="row.full_name"></td>
                <!-- GENDER -->
                <td class="px-3 py-2.5 text-center">
                  <span class="px-1.5 py-0.5 rounded text-xs font-medium"
                        :class="row.gender === 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'"
                        x-text="row.gender === 'Male' ? 'L' : 'P'"></span>
                </td>
                <!-- NO PASSPORT -->
                <td class="px-3 py-2.5 font-mono text-xs" x-text="row.passport_no || '-'"></td>
                <!-- RELATION -->
                <td class="px-3 py-2.5 text-xs">
                  <span x-show="row.type === 'main'" class="text-slate-400 italic">Jamaah Utama</span>
                  <span x-show="row.type === 'family'" class="text-orange-600 font-medium" x-text="row.relation"></span>
                </td>
                <!-- GROUP -->
                <td class="px-3 py-2.5 text-center">
                  <span x-show="row.group_label === 'BERDEKATAN'"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                    <i class='bx bx-link text-xs'></i> BERDEKATAN
                  </span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div x-show="manifestRows.length === 0 && !loading" class="text-center py-12 text-slate-400">
        <i class='bx bx-user-x text-4xl mb-2'></i>
        <p class="text-sm">Belum ada jamaah di keberangkatan ini.</p>
      </div>

      <!-- Summary -->
      <div x-show="manifestRows.length > 0" class="border-t border-slate-200 bg-slate-50 px-4 py-3 flex items-center justify-between text-sm text-slate-600">
        <span>Total: <strong x-text="manifestRows.length"></strong> orang</span>
        <span x-show="getGroupCount() > 0">
          <i class='bx bx-link text-green-600'></i>
          <span x-text="getGroupCount()"></span> grup berdekatan
        </span>
      </div>
    </div>

    <!-- Toast -->
    <div x-show="toast.show" x-transition.opacity class="fixed bottom-4 right-4 z-50">
      <div class="rounded-xl shadow-lg px-5 py-3 text-white text-sm"
           :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
           x-text="toast.message"></div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script>
  function manifestManager() {
    return {
      keberangkatanId: {{ $keberangkatan->id ?? 'null' }},
      keberangkatan: @json($keberangkatan ?? new stdClass),
      manifestRows: @json($manifestRows ?? []),
      selectedRows: [],
      loading: false,
      saving: false,
      sortableInstance: null,
      toast: { show: false, message: '', type: 'success' },

      async init() {
        this.$nextTick(() => this.initSortable());
      },

      // Data already loaded from server via @json($manifestRows)

      initSortable() {
        if (this.sortableInstance) {
          this.sortableInstance.destroy();
          this.sortableInstance = null;
        }

        const tbody = document.getElementById('manifest-sortable');
        if (!tbody) return;

        const self = this;
        this.sortableInstance = Sortable.create(tbody, {
          animation: 150,
          handle: '.drag-handle',
          ghostClass: 'opacity-40',
          chosenClass: 'bg-blue-100',
          dragClass: 'shadow-lg',
          onEnd(evt) {
            if (evt.oldIndex === evt.newIndex) return;
            const moved = self.manifestRows.splice(evt.oldIndex, 1)[0];
            self.manifestRows.splice(evt.newIndex, 0, moved);
          }
        });
      },

      toggleSelect(rowId, event) {
        if (event && event.target.type === 'checkbox') {
          // Handled by checkbox change
        }
        const idx = this.selectedRows.indexOf(rowId);
        if (idx === -1) {
          this.selectedRows.push(rowId);
        } else {
          this.selectedRows.splice(idx, 1);
        }
      },

      toggleSelectAll(event) {
        if (event.target.checked) {
          this.selectedRows = this.manifestRows.map(r => r.id);
        } else {
          this.selectedRows = [];
        }
      },

      markBerdekatan() {
        if (this.selectedRows.length < 2) return;

        // Find indices of selected rows and check if they are adjacent
        const indices = this.selectedRows
          .map(id => this.manifestRows.findIndex(r => r.id === id))
          .filter(i => i !== -1)
          .sort((a, b) => a - b);

        // Mark all selected rows as BERDEKATAN
        this.selectedRows.forEach(id => {
          const row = this.manifestRows.find(r => r.id === id);
          if (row) row.group_label = 'BERDEKATAN';
        });

        this.selectedRows = [];
        this.showToast('Grup berdekatan ditandai', 'success');
      },

      clearBerdekatan() {
        this.selectedRows.forEach(id => {
          const row = this.manifestRows.find(r => r.id === id);
          if (row) row.group_label = '';
        });
        this.selectedRows = [];
        this.showToast('Label berdekatan dihapus', 'success');
      },

      getGroupCount() {
        // Count distinct groups of consecutive BERDEKATAN rows
        let groups = 0;
        let inGroup = false;
        this.manifestRows.forEach(row => {
          if (row.group_label === 'BERDEKATAN') {
            if (!inGroup) { groups++; inGroup = true; }
          } else {
            inGroup = false;
          }
        });
        return groups;
      },

      async saveOrder() {
        this.saving = true;
        try {
          const orderData = this.manifestRows.map((row, idx) => ({
            id: row.id,
            type: row.type,
            booking_id: row.booking_id,
            family_idx: row.family_idx,
            order: idx,
            group_label: row.group_label || ''
          }));

          const res = await fetch(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/save-manifest-order`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ manifest_order: orderData })
          });

          const data = await res.json();
          if (data.success) {
            this.showToast('Urutan manifest berhasil disimpan', 'success');
          } else {
            this.showToast(data.message || 'Gagal menyimpan', 'error');
          }
        } catch(e) {
          console.error('Save error:', e);
          this.showToast('Error: ' + e.message, 'error');
        } finally {
          this.saving = false;
        }
      },

      downloadPdf() {
        window.open(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/manifest-pdf`, '_blank');
      },

      downloadExcel() {
        window.open(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/manifest-excel`, '_blank');
      },

      showToast(msg, type = 'success') {
        this.toast = { show: true, message: msg, type };
        setTimeout(() => this.toast.show = false, 3000);
      }
    };
  }
  </script>
  @endpush
</x-layouts.admin>
