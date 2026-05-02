<x-layouts.admin :title="'Logistics - ' . $keberangkatan->keberangkatan_name">
  <div x-data="logisticsManagement()" x-init="init()" class="space-y-4 self-start w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2">
          <a href="{{ route('admin.inventaris.travel.keberangkatan.show', $keberangkatan->id) }}" class="p-2 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-arrow-back text-xl'></i>
          </a>
          <div>
            <h1 class="text-2xl font-bold">Logistik & Perlengkapan</h1>
            <p class="text-slate-600 text-sm">{{ $keberangkatan->keberangkatan_name }}</p>
          </div>
        </div>
      </div>
      <div class="flex gap-2">
        @if($equipmentItems->isEmpty())
        <button x-on:click="initializeDefaults()" 
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
          <i class='bx bx-list-plus'></i> Inisialisasi Daftar Periksa Default
        </button>
        @endif
        <button x-on:click="openAddModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-4 py-2 hover:bg-green-700">
          <i class='bx bx-plus'></i> Tambah Perlengkapan
        </button>
        <a href="{{ route('admin.inventaris.logistics.packing-list', $keberangkatan->id) }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl bg-purple-600 text-white px-4 py-2 hover:bg-purple-700">
          <i class='bx bx-printer'></i> Daftar Packing
        </a>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Total Item</div>
        <div class="text-2xl font-bold">{{ $stats['total_items'] }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Belum Dipesan</div>
        <div class="text-2xl font-bold text-gray-600">{{ $stats['not_ordered'] }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Dipesan</div>
        <div class="text-2xl font-bold text-blue-600">{{ $stats['ordered'] }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Diterima</div>
        <div class="text-2xl font-bold text-indigo-600">{{ $stats['received'] }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Dikemas</div>
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['packed'] }}</div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-4">
        <div class="text-sm text-slate-600">Dikirim</div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['shipped'] }}</div>
      </div>
      <div class="rounded-2xl border border-red-200 bg-red-50 shadow-card p-4">
        <div class="text-sm text-red-600">Peringatan</div>
        <div class="text-2xl font-bold text-red-600">{{ $stats['approaching_deadline'] + $stats['overdue'] }}</div>
      </div>
    </div>

    <!-- Equipment List -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
      <div class="p-5 border-b border-slate-200">
        <h2 class="font-semibold text-lg">Daftar Periksa Perlengkapan</h2>
      </div>
      <div class="p-5">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-200">
                <th class="text-left py-3 px-2 text-sm font-medium text-slate-600">Kategori</th>
                <th class="text-left py-3 px-2 text-sm font-medium text-slate-600">Nama Perlengkapan</th>
                <th class="text-center py-3 px-2 text-sm font-medium text-slate-600">Jml Dibutuhkan</th>
                <th class="text-center py-3 px-2 text-sm font-medium text-slate-600">Jml Diterima</th>
                <th class="text-center py-3 px-2 text-sm font-medium text-slate-600">Status</th>
                <th class="text-left py-3 px-2 text-sm font-medium text-slate-600">Supplier</th>
                <th class="text-center py-3 px-2 text-sm font-medium text-slate-600">Tenggat</th>
                <th class="text-center py-3 px-2 text-sm font-medium text-slate-600">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($equipmentItems as $item)
              <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="py-3 px-2 text-sm">{{ $item->equipment_category ?? '-' }}</td>
                <td class="py-3 px-2 text-sm font-medium">{{ $item->equipment_name }}</td>
                <td class="py-3 px-2 text-sm text-center">{{ $item->quantity_needed }}</td>
                <td class="py-3 px-2 text-sm text-center">
                  <span class="font-medium" :class="{'text-green-600': {{ $item->quantity_received }} >= {{ $item->quantity_needed }}}">
                    {{ $item->quantity_received }}
                  </span>
                </td>
                <td class="py-3 px-2 text-center">
                  <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $item->getStatusBadgeColor() }}-100 text-{{ $item->getStatusBadgeColor() }}-800">
                    {{ $item->getStatusLabel() }}
                  </span>
                </td>
                <td class="py-3 px-2 text-sm">{{ $item->supplier_name ?? '-' }}</td>
                <td class="py-3 px-2 text-sm text-center">
                  @if($item->shipping_deadline)
                    <div class="flex flex-col items-center">
                      <span class="{{ $item->isDeadlineOverdue() ? 'text-red-600 font-medium' : ($item->isDeadlineApproaching() ? 'text-yellow-600 font-medium' : '') }}">
                        {{ $item->shipping_deadline->format('d M Y') }}
                      </span>
                      @if($item->isDeadlineOverdue())
                        <span class="text-xs text-red-600">Terlambat!</span>
                      @elseif($item->isDeadlineApproaching())
                        <span class="text-xs text-yellow-600">Segera!</span>
                      @endif
                    </div>
                  @else
                    -
                  @endif
                </td>
                <td class="py-3 px-2 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <button x-on:click="openEditModal({{ $item->id }})" 
                            class="p-1 hover:bg-slate-200 rounded">
                      <i class='bx bx-edit text-blue-600'></i>
                    </button>
                    <button x-on:click="openStatusModal({{ $item->id }})" 
                            class="p-1 hover:bg-slate-200 rounded">
                      <i class='bx bx-refresh text-green-600'></i>
                    </button>
                    <button x-on:click="deleteItem({{ $item->id }})" 
                            class="p-1 hover:bg-slate-200 rounded">
                      <i class='bx bx-trash text-red-600'></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="py-8 text-center text-slate-500">
                  <i class='bx bx-package text-4xl mb-2'></i>
                  <p>Belum ada item perlengkapan. Klik "Inisialisasi Daftar Periksa Default" atau "Tambah Perlengkapan" untuk memulai.</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add/Edit Equipment Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-8">
        <div x-show="showModal" x-on:click="showModal = false" class="fixed inset-0 bg-black opacity-50"></div>
        <div x-show="showModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 my-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold" x-text="editMode ? 'Edit Perlengkapan' : 'Tambah Perlengkapan'"></h3>
            <button x-on:click="showModal = false" class="p-2 hover:bg-slate-100 rounded-lg">
              <i class='bx bx-x text-xl'></i>
            </button>
          </div>

          <form x-on:submit.prevent="saveEquipment()">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Perlengkapan *</label>
                <input type="text" x-model="form.equipment_name" required
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <input type="text" x-model="form.equipment_category"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Dibutuhkan *</label>
                <input type="number" x-model="form.quantity_needed" required min="1"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div x-show="editMode">
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Diterima</label>
                <input type="number" x-model="form.quantity_received" min="0"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div x-show="editMode">
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select x-model="form.status" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                  <option value="not_ordered">Belum Dipesan</option>
                  <option value="ordered">Dipesan</option>
                  <option value="received">Diterima</option>
                  <option value="packed">Dikemas</option>
                  <option value="shipped">Dikirim</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Supplier</label>
                <input type="text" x-model="form.supplier_name"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pemesanan</label>
                <input type="date" x-model="form.order_date"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tenggat Pengiriman</label>
                <input type="date" x-model="form.shipping_deadline"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>

              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                <textarea x-model="form.notes" rows="3"
                          class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
              </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
              <button type="button" x-on:click="showModal = false"
                      class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="saving"
                      class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
                <span x-show="!saving">Simpan</span>
                <span x-show="saving">Menyimpan...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
      <div class="flex items-start justify-center min-h-screen px-4 pt-8">
        <div x-show="showStatusModal" x-on:click="showStatusModal = false" class="fixed inset-0 bg-black opacity-50"></div>
        <div x-show="showStatusModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 my-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Perbarui Status</h3>
            <button x-on:click="showStatusModal = false" class="p-2 hover:bg-slate-100 rounded-lg">
              <i class='bx bx-x text-xl'></i>
            </button>
          </div>

          <form x-on:submit.prevent="updateStatus()">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status *</label>
                <select x-model="statusForm.status" required
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                  <option value="not_ordered">Belum Dipesan</option>
                  <option value="ordered">Dipesan</option>
                  <option value="received">Diterima</option>
                  <option value="packed">Dikemas</option>
                  <option value="shipped">Dikirim</option>
                </select>
              </div>

              <div x-show="statusForm.status === 'received' || statusForm.status === 'packed' || statusForm.status === 'shipped'">
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Diterima</label>
                <input type="number" x-model="statusForm.quantity_received" min="0"
                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
              </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
              <button type="button" x-on:click="showStatusModal = false"
                      class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="saving"
                      class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-50">
                <span x-show="!saving">Perbarui</span>
                <span x-show="saving">Memperbarui...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function logisticsManagement() {
      return {
        keberangkatanId: {{ $keberangkatan->id }},
        showModal: false,
        showStatusModal: false,
        editMode: false,
        saving: false,
        form: {
          equipment_name: '',
          equipment_category: '',
          quantity_needed: 1,
          quantity_received: 0,
          status: 'not_ordered',
          supplier_name: '',
          order_date: '',
          shipping_deadline: '',
          notes: ''
        },
        statusForm: {
          id: null,
          status: 'not_ordered',
          quantity_received: 0
        },
        editingId: null,

        init() {
          console.log('Logistics management initialized');
        },

        openAddModal() {
          this.editMode = false;
          this.resetForm();
          this.showModal = true;
        },

        openEditModal(id) {
          this.editMode = true;
          this.editingId = id;
          this.loadEquipmentData(id);
          this.showModal = true;
        },

        openStatusModal(id) {
          this.statusForm.id = id;
          this.loadEquipmentForStatus(id);
          this.showStatusModal = true;
        },

        resetForm() {
          this.form = {
            equipment_name: '',
            equipment_category: '',
            quantity_needed: 1,
            quantity_received: 0,
            status: 'not_ordered',
            supplier_name: '',
            order_date: '',
            shipping_deadline: '',
            notes: ''
          };
        },

        async loadEquipmentData(id) {
          // In a real implementation, fetch data from server
          // For now, we'll use the data from the page
        },

        async loadEquipmentForStatus(id) {
          // Load equipment data for status update
        },

        async saveEquipment() {
          this.saving = true;
          try {
            const url = this.editMode 
              ? `{{ url('/admin/inventaris/travel/logistics') }}/${this.editingId}`
              : `{{ url('/admin/inventaris/travel/keberangkatan') }}/${this.keberangkatanId}/logistics`;
            
            const method = this.editMode ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.form)
            });

            const data = await response.json();

            if (data.success) {
              window.location.reload();
            } else {
              alert(data.message || 'Failed to save equipment');
            }
          } catch (error) {
            console.error('Error saving equipment:', error);
            alert('An error occurred while saving');
          } finally {
            this.saving = false;
          }
        },

        async updateStatus() {
          this.saving = true;
          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/logistics') }}/${this.statusForm.id}/status`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify(this.statusForm)
            });

            const data = await response.json();

            if (data.success) {
              window.location.reload();
            } else {
              alert(data.message || 'Failed to update status');
            }
          } catch (error) {
            console.error('Error updating status:', error);
            alert('An error occurred while updating status');
          } finally {
            this.saving = false;
          }
        },

        async deleteItem(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus item perlengkapan ini?')) {
            return;
          }

          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/logistics') }}/${id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const data = await response.json();

            if (data.success) {
              window.location.reload();
            } else {
              alert(data.message || 'Failed to delete equipment');
            }
          } catch (error) {
            console.error('Error deleting equipment:', error);
            alert('An error occurred while deleting');
          }
        },

        async initializeDefaults() {
          if (!confirm('Inisialisasi daftar periksa perlengkapan default? Ini akan menambahkan item standar berdasarkan jumlah jamaah.')) {
            return;
          }

          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/keberangkatan') }}/${this.keberangkatanId}/logistics/initialize`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            });

            const data = await response.json();

            if (data.success) {
              window.location.reload();
            } else {
              alert(data.message || 'Failed to initialize checklist');
            }
          } catch (error) {
            console.error('Error initializing checklist:', error);
            alert('An error occurred while initializing');
          }
        }
      }
    }
  </script>
</x-layouts.admin>
