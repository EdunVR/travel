<x-layouts.admin :title="'Kelola Room Position'">
  <div x-data="roomManager()" x-init="init()" class="space-y-4 self-start w-full">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div class="flex items-center gap-2">
        <button onclick="
          if (window.self !== window.top) {
            // Berada di dalam iframe — tutup modal di parent
            window.parent.postMessage('closeRoomlistModal', '*');
          } else {
            window.history.back();
          }
        " class="p-2 hover:bg-slate-100 rounded-lg">
          <i class='bx bx-arrow-back text-xl'></i>
        </button>
        <div>
          <h1 class="text-xl font-bold">Kelola Room Position</h1>
          <p class="text-slate-500 text-sm" x-text="keberangkatan.keberangkatan_name + ' — ' + keberangkatan.departure_date_formatted"></p>
        </div>
      </div>
      <div class="flex gap-2 flex-wrap">
        <button x-on:click="autoAssign(activeCityType)" :disabled="loading"
                class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white text-slate-700 px-3 py-2 hover:bg-slate-50 text-sm disabled:opacity-50">
          <i class='bx bx-magic-wand'></i> Auto Assign
        </button>
        <button x-on:click="addRoom()"
                class="inline-flex items-center gap-1 rounded-xl border border-blue-300 text-blue-700 bg-blue-50 px-3 py-2 hover:bg-blue-100 text-sm">
          <i class='bx bx-plus'></i> Tambah Kamar
        </button>
        <button x-on:click="saveAll()" :disabled="saving"
                class="inline-flex items-center gap-1 rounded-xl bg-green-600 text-white px-3 py-2 hover:bg-green-700 text-sm disabled:opacity-50">
          <i class='bx bx-save'></i>
          <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
        </button>
        <button x-on:click="exportPdf()"
                class="inline-flex items-center gap-1 rounded-xl bg-red-600 text-white px-3 py-2 hover:bg-red-700 text-sm">
          <i class='bx bx-file-pdf'></i> Export PDF
        </button>
      </div>
    </div>

    <!-- City Type Tabs -->
    <div class="flex gap-1 border-b border-slate-200">
      <template x-for="ct in cityTypes" :key="ct.value">
        <button x-on:click="activeCityType = ct.value; renderRooms()"
                :class="activeCityType === ct.value ? 'border-b-2 border-blue-600 text-blue-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm transition-colors" x-text="ct.label"></button>
      </template>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="text-center py-8 text-slate-500">
      <i class='bx bx-loader-alt bx-spin text-2xl'></i>
      <p class="mt-2 text-sm">Memuat data...</p>
    </div>

    <div x-show="!loading" class="grid grid-cols-1 lg:grid-cols-3 gap-4">

      <!-- LEFT: Unassigned Persons Pool -->
      <div class="lg:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-sm text-slate-700">
              Belum Ditempatkan
              <span class="ml-1 px-1.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs" x-text="unassignedForCity.length"></span>
            </h3>
            <span class="text-xs text-slate-400">Drag ke kamar</span>
          </div>
          <div id="unassigned-pool" class="space-y-1.5 min-h-[60px]">
            <template x-for="person in unassignedForCity" :key="person.id">
              <div class="person-card flex items-center gap-2 p-2 rounded-lg border border-slate-200 bg-slate-50 cursor-grab"
                   :data-person-id="person.id"
                   :data-person-json="JSON.stringify(person)">
                <i class='bx bx-grid-vertical text-slate-400 flex-shrink-0'></i>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-medium truncate" x-text="person.person_name"></div>
                  <div class="text-xs text-slate-400 flex items-center gap-1">
                    <span x-text="person.booking_code"></span>
                    <span class="px-1 rounded text-xs"
                          :class="person.person_type === 'family' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'"
                          x-text="person.person_type === 'family' ? 'Keluarga' : 'Jamaah'"></span>
                  </div>
                </div>
              </div>
            </template>
            <div x-show="unassignedForCity.length === 0" class="text-center py-4 text-slate-400 text-xs">
              Semua sudah ditempatkan
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Rooms -->
      <div class="lg:col-span-2 space-y-3">
        <template x-for="(room, ri) in roomsForCity" :key="room.room_number + '_' + activeCityType">
          <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
            <!-- Room Header -->
            <div class="flex items-center gap-3 mb-3 flex-wrap">
              <div class="flex items-center gap-2">
                <span class="font-semibold text-sm">Kamar</span>
                <input type="text" :value="room.room_number"
                     @change="room.room_number = $event.target.value"
                     @keydown.stop
                       class="w-16 px-2 py-1 border border-slate-300 rounded-lg text-sm font-mono text-center focus:ring-2 focus:ring-blue-400">
              </div>
              <select :value="room.room_type" @change="room.room_type = $event.target.value"
                      class="px-2 py-1 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-400">
                <option value="single">Single (1)</option>
                <option value="double">Double (2)</option>
                <option value="triple">Triple (3)</option>
                <option value="quad">Quad (4)</option>
              </select>
              <!-- Capacity indicator -->
              <div class="flex items-center gap-1">
                <template x-for="i in getCapacity(room.room_type)" :key="i">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-xs"
                       :class="i <= room.persons.length ? 'bg-green-500 border-green-500 text-white' : 'border-slate-300 text-slate-300'">
                    <i class='bx bx-user text-xs'></i>
                  </div>
                </template>
                <span class="text-xs text-slate-500 ml-1"
                      :class="room.persons.length > getCapacity(room.room_type) ? 'text-red-600 font-semibold' : ''"
                      x-text="room.persons.length + '/' + getCapacity(room.room_type)"></span>
              </div>
              <!-- Room position note -->
              <input type="text" :value="room.room_position"
                     @change="room.room_position = $event.target.value"
                     @keydown.stop
                     placeholder="Berdekatan dengan kamar..."
                     class="flex-1 min-w-[140px] px-2 py-1 border border-orange-200 bg-orange-50 rounded-lg text-xs focus:ring-2 focus:ring-orange-300">
              <button x-on:click="removeRoom(ri)" class="p-1 rounded hover:bg-red-50 text-red-400 hover:text-red-600 ml-auto">
                <i class='bx bx-trash text-sm'></i>
              </button>
            </div>

            <!-- Persons in room (droppable) -->
            <div :id="'room-' + ri + '-' + activeCityType"
                 :data-room-index="ri"
                 class="room-drop-zone space-y-1.5 min-h-[50px] rounded-xl border-2 border-dashed p-2 transition-colors"
                 :class="room.persons.length === 0 ? 'border-slate-200 bg-slate-50' : 'border-transparent bg-slate-50'">
              <template x-for="(person, pi) in room.persons" :key="person.id">
                <div class="person-card flex items-center gap-2 p-2 rounded-lg border bg-white cursor-grab"
                     :class="person.person_type === 'family' ? 'border-yellow-200' : 'border-blue-200'"
                     :data-person-id="person.id"
                     :data-person-json="JSON.stringify(person)">
                  <i class='bx bx-grid-vertical text-slate-400 flex-shrink-0'></i>
                  <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium truncate" x-text="person.person_name"></div>
                    <div class="text-xs text-slate-400 flex items-center gap-1">
                      <span x-text="person.booking_code"></span>
                      <span class="px-1 rounded text-xs"
                            :class="person.person_type === 'family' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'"
                            x-text="person.person_type === 'family' ? 'Keluarga' : 'Jamaah'"></span>
                    </div>
                  </div>
                  <button x-on:click="removePersonFromRoom(ri, pi)"
                          class="p-1 rounded hover:bg-red-50 text-slate-400 hover:text-red-500 flex-shrink-0">
                    <i class='bx bx-x text-sm'></i>
                  </button>
                </div>
              </template>
              <div x-show="room.persons.length === 0" class="text-center py-2 text-slate-400 text-xs">
                Drag jamaah ke sini
              </div>
            </div>
          </div>
        </template>

        <div x-show="roomsForCity.length === 0 && !loading" class="text-center py-8 text-slate-400">
          <i class='bx bx-hotel text-4xl mb-2'></i>
          <p class="text-sm">Belum ada kamar. Klik "Auto Assign" atau "Tambah Kamar".</p>
        </div>
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
  function roomManager() {
    return {
      keberangkatanId: null,
      keberangkatan: {},
      allPersons: [],
      allRooms: [],   // [{room_number, city_type, room_type, room_position, persons:[]}]
      activeCityType: 'makkah',
      cityTypes: [
        { value: 'makkah', label: 'Hotel Mekkah' },
        { value: 'madinah', label: 'Hotel Madinah' },
      ],
      loading: false,
      saving: false,
      toast: { show: false, message: '', type: 'success' },
      sortableInstances: [],

      get roomsForCity() {
        return this.allRooms.filter(r => r.city_type === this.activeCityType);
      },
      get unassignedForCity() {
        const assigned = new Set(
          this.allRooms.filter(r => r.city_type === this.activeCityType)
            .flatMap(r => r.persons.map(p => p.id))
        );
        return this.allPersons.filter(p => p.city_type === this.activeCityType && !assigned.has(p.id));
      },

      async init() {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const idx = parts.indexOf('document');
        if (idx !== -1 && parts[idx + 1]) this.keberangkatanId = parts[idx + 1];
        if (!this.keberangkatanId) return;
        await this.loadData();
      },

      async loadData() {
        this.loading = true;
        try {
          const res = await fetch(`{{ url('') }}/admin/inventaris/travel/document/${this.keberangkatanId}/room-assignments`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          });
          const data = await res.json();
          if (data.success) {
            this.keberangkatan = data.keberangkatan;
            this.allPersons = data.persons;
            this.allRooms = data.rooms;
          }
        } catch(e) { this.showToast('Gagal memuat data', 'error'); }
        finally { this.loading = false; this.$nextTick(() => this.initSortable()); }
      },

      initSortable() {
        // Destroy old instances
        this.sortableInstances.forEach(s => s.destroy());
        this.sortableInstances = [];

        const self = this;

        // Unassigned pool
        const pool = document.getElementById('unassigned-pool');
        if (pool) {
          this.sortableInstances.push(Sortable.create(pool, {
            group: 'persons-' + this.activeCityType,
            animation: 150,
            ghostClass: 'opacity-40',
            onAdd(evt) {
              const personId = evt.item.dataset.personId;
              // Remove from room
              self.allRooms.forEach(r => {
                if (r.city_type !== self.activeCityType) return;
                r.persons = r.persons.filter(p => p.id !== personId);
              });
              evt.item.remove(); // Alpine will re-render
            }
          }));
        }

        // Room drop zones
        const zones = document.querySelectorAll('.room-drop-zone');
        zones.forEach(zone => {
          const ri = parseInt(zone.dataset.roomIndex);
          this.sortableInstances.push(Sortable.create(zone, {
            group: 'persons-' + this.activeCityType,
            animation: 150,
            ghostClass: 'opacity-40',
            onAdd(evt) {
              const personId = evt.item.dataset.personId;
              const personData = JSON.parse(evt.item.dataset.personJson || '{}');
              // Remove from other rooms
              self.allRooms.forEach((r, i) => {
                if (r.city_type !== self.activeCityType) return;
                if (i !== ri) r.persons = r.persons.filter(p => p.id !== personId);
              });
              // Add to this room if not already there
              const room = self.allRooms.filter(r => r.city_type === self.activeCityType)[ri];
              if (room && !room.persons.find(p => p.id === personId)) {
                room.persons.push(personData);
              }
              evt.item.remove(); // Alpine re-renders
            },
            onEnd(evt) {
              // Reorder within same room
              const room = self.allRooms.filter(r => r.city_type === self.activeCityType)[ri];
              if (room && evt.oldIndex !== evt.newIndex) {
                const moved = room.persons.splice(evt.oldIndex, 1)[0];
                room.persons.splice(evt.newIndex, 0, moved);
              }
            }
          }));
        });
      },

      getCapacity(roomType) {
        const map = { single: 1, double: 2, triple: 3, quad: 4 };
        return map[roomType?.toLowerCase()] ?? 2;
      },

      updateRoomCapacity(room) {
        // No action needed — capacity indicator updates reactively
      },

      addRoom() {
        const existing = this.allRooms.filter(r => r.city_type === this.activeCityType);
        const maxNum = existing.reduce((m, r) => Math.max(m, parseInt(r.room_number) || 0), 100);
        this.allRooms.push({
          room_number: String(maxNum + 1),
          city_type: this.activeCityType,
          room_type: 'double',
          room_position: '',
          persons: [],
        });
        this.$nextTick(() => this.initSortable());
      },

      removeRoom(ri) {
        const rooms = this.allRooms.filter(r => r.city_type === this.activeCityType);
        const room = rooms[ri];
        // Persons go back to unassigned (they'll appear automatically)
        const globalIdx = this.allRooms.indexOf(room);
        if (globalIdx !== -1) this.allRooms.splice(globalIdx, 1);
        this.$nextTick(() => this.initSortable());
      },

      removePersonFromRoom(ri, pi) {
        const rooms = this.allRooms.filter(r => r.city_type === this.activeCityType);
        rooms[ri].persons.splice(pi, 1);
        this.$nextTick(() => this.initSortable());
      },

      async autoAssign(cityType) {
        this.loading = true;
        try {
          const res = await fetch(`{{ url('') }}/admin/inventaris/travel/document/${this.keberangkatanId}/room-assignments/auto`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ city_type: cityType })
          });
          const data = await res.json();
          if (data.success) {
            // Remove existing rooms for this city type and add new ones
            this.allRooms = this.allRooms.filter(r => r.city_type !== cityType);
            data.rooms.forEach(r => {
              this.allRooms.push({
                room_number: r.room_number,
                city_type: r.city_type,
                room_type: r.room_type,
                room_position: r.room_position || '',
                persons: r.persons.map(p => ({
                  id: (p.person_type === 'jamaah' ? 'j_' : 'f_') + p.booking_id + '_' + cityType + (p.family_index != null ? '_' + p.family_index : ''),
                  booking_id: p.booking_id,
                  booking_code: this.allPersons.find(ap => ap.booking_id === p.booking_id)?.booking_code ?? '',
                  person_type: p.person_type,
                  person_name: p.person_name,
                  family_index: p.family_index,
                  city_type: cityType,
                })),
              });
            });
            this.showToast('Auto-assign berhasil', 'success');
            this.$nextTick(() => this.initSortable());
          } else {
            this.showToast(data.message || 'Gagal', 'error');
          }
        } catch(e) { this.showToast('Error: ' + e.message, 'error'); }
        finally { this.loading = false; }
      },

      async saveAll() {
        this.saving = true;
        try {
          const res = await fetch(`{{ url('') }}/admin/inventaris/travel/document/${this.keberangkatanId}/room-assignments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
              rooms: this.allRooms.map(r => ({
                ...r,
                room_number: String(r.room_number || '')
              }))
            })
          });
          const data = await res.json();
          if (data.success) { this.showToast('Berhasil disimpan', 'success'); }
          else { this.showToast(data.message || 'Gagal', 'error'); }
        } catch(e) { this.showToast('Error', 'error'); }
        finally { this.saving = false; }
      },

      exportPdf() {
        window.open(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/roomlist-stream`, '_blank');
      },

      showToast(msg, type = 'success') {
        this.toast = { show: true, message: msg, type };
        setTimeout(() => this.toast.show = false, 3000);
      },

      renderRooms() {
        this.$nextTick(() => this.initSortable());
      }
    };
  }
  </script>
  @endpush
</x-layouts.admin>
