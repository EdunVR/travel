<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Master / Penerbangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Master / Penerbangan')]); ?>
  <div x-data="flightCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Data Penerbangan</h1>
        <p class="text-slate-600 text-sm">Kelola data penerbangan dan group penerbangan untuk paket travel.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <template x-if="activeTab === 'groups'">
          <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.create')): ?>
          <button x-on:click="openCreateGroup()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
            <i class='bx bx-plus-circle text-lg'></i> Buat Group Penerbangan
          </button>
          <?php endif; ?>
        </template>
        <template x-if="activeTab === 'flights'">
          <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.create')): ?>
          <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
            <i class='bx bx-plus-circle text-lg'></i> Tambah Penerbangan
          </button>
          <?php endif; ?>
        </template>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 border-b border-slate-200">
      <button x-on:click="activeTab = 'groups'; fetchGroups()" 
              :class="activeTab === 'groups' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-600 hover:text-slate-900'"
              class="px-4 py-2 border-b-2 font-medium transition-colors">
        Group Penerbangan
      </button>
      <button x-on:click="activeTab = 'flights'; fetchData()" 
              :class="activeTab === 'flights' ? 'border-primary-600 text-primary-600' : 'border-transparent text-slate-600 hover:text-slate-900'"
              class="px-4 py-2 border-b-2 font-medium transition-colors">
        Penerbangan Individual
      </button>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-5">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari maskapai, nomor penerbangan, rute…"
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-4">
          <select x-model="airlineFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Maskapai</option>
            <template x-for="a in airlines" :key="a"><option :value="a" x-text="a"></option></template>
          </select>
        </div>
        <div class="lg:col-span-3">
          <input x-model="routeFilter" x-on:input.debounce.500ms="fetchData()" placeholder="Filter rute…"
                 class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <div class="grid grid-cols-2 gap-2 lg:col-span-4">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="airline">Maskapai</option>
            <option value="flight_number">Nomor Penerbangan</option>
            <option value="departure">Waktu Keberangkatan</option>
            <option value="capacity">Kapasitas</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
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

    <!-- FLIGHT GROUPS TABLE -->
    <div x-show="!loading && activeTab === 'groups'">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Nama Group</th>
              <th class="text-left px-4 py-3">Penerbangan Keberangkatan</th>
              <th class="text-left px-4 py-3">Penerbangan Kepulangan</th>
              <th class="text-left px-4 py-3">Rute</th>
              <th class="px-4 py-3 text-right w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="g in flightGroups" :key="g.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3">
                  <div class="font-medium" x-text="g.group_name"></div>
                  <div class="text-xs text-slate-500" x-text="g.description"></div>
                </td>
                <td class="px-4 py-3">
                  <template x-if="g.departure_flight">
                    <div>
                      <div class="font-medium" x-text="g.departure_flight.airline + ' ' + g.departure_flight.flight_number"></div>
                      <div class="text-xs text-slate-500" x-text="g.departure_flight.departure_time"></div>
                    </div>
                  </template>
                  <template x-if="!g.departure_flight">
                    <span class="text-slate-400">-</span>
                  </template>
                </td>
                <td class="px-4 py-3">
                  <template x-if="g.return_flight">
                    <div>
                      <div class="font-medium" x-text="g.return_flight.airline + ' ' + g.return_flight.flight_number"></div>
                      <div class="text-xs text-slate-500" x-text="g.return_flight.departure_time"></div>
                    </div>
                  </template>
                  <template x-if="!g.return_flight">
                    <span class="text-slate-400">-</span>
                  </template>
                </td>
                <td class="px-4 py-3 text-sm" x-text="g.route"></td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.edit')): ?>
                    <button x-on:click="openEditGroup(g)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    <?php endif; ?>
                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.delete')): ?>
                    <button x-on:click="confirmDeleteGroup(g)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="flightGroups.length===0"><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada group penerbangan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list for groups -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="g in flightGroups" :key="g.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="font-semibold mb-2" x-text="g.group_name"></div>
            <div class="text-xs text-slate-500 mb-3" x-text="g.description"></div>
            <div class="space-y-2 text-sm">
              <div>
                <span class="text-slate-600">Keberangkatan:</span>
                <template x-if="g.departure_flight">
                  <div class="font-medium" x-text="g.departure_flight.airline + ' ' + g.departure_flight.flight_number"></div>
                </template>
                <template x-if="!g.departure_flight">
                  <span class="text-slate-400">-</span>
                </template>
              </div>
              <div>
                <span class="text-slate-600">Kepulangan:</span>
                <template x-if="g.return_flight">
                  <div class="font-medium" x-text="g.return_flight.airline + ' ' + g.return_flight.flight_number"></div>
                </template>
                <template x-if="!g.return_flight">
                  <span class="text-slate-400">-</span>
                </template>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.edit')): ?>
              <button x-on:click="openEditGroup(g)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Edit</button>
              <?php endif; ?>
              
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.delete')): ?>
              <button x-on:click="confirmDeleteGroup(g)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50">Hapus</button>
              <?php endif; ?>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- INDIVIDUAL FLIGHTS TABLE -->
    <div x-show="!loading && activeTab === 'flights'">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Maskapai</th>
              <th class="text-left px-4 py-3">Nomor Penerbangan</th>
              <th class="text-left px-4 py-3">Rute</th>
              <th class="text-left px-4 py-3">Keberangkatan</th>
              <th class="text-left px-4 py-3">Kedatangan</th>
              <th class="text-left px-4 py-3">Biaya/Orang</th>
              <th class="px-4 py-3 text-right w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="f in flights" :key="f.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-medium" x-text="f.airline_name"></td>
                <td class="px-4 py-3 font-mono text-slate-600" x-text="f.flight_number"></td>
                <td class="px-4 py-3" x-text="f.route"></td>
                <td class="px-4 py-3 text-sm" x-text="f.departure_time"></td>
                <td class="px-4 py-3 text-sm" x-text="f.arrival_time"></td>
                <td class="px-4 py-3 font-semibold text-primary-700" x-text="f.price_per_person_formatted"></td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.edit')): ?>
                    <button x-on:click="openEdit(f)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50"><i class='bx bx-edit-alt'></i> Edit</button>
                    <?php endif; ?>
                    
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.delete')): ?>
                    <button x-on:click="confirmDelete(f)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50"><i class='bx bx-trash'></i> Hapus</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="flights.length===0"><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="f in flights" :key="f.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100">
                <i class='bx bx-plane-alt'></i>
              </div>
              <div class="flex-1">
                <div class="font-semibold" x-text="f.airline_name"></div>
                <div class="text-[11px] text-slate-500 font-mono" x-text="f.flight_number"></div>
                <div class="text-sm text-slate-600 mt-1" x-text="f.route"></div>
                <div class="text-xs text-slate-500 mt-1">
                  <span x-text="f.departure_time"></span> - <span x-text="f.arrival_time"></span>
                </div>
                <div class="mt-1 text-[11px]">
                  <span class="px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-200">
                    Kapasitas: <span x-text="f.capacity"></span>
                  </span>
                  <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 ml-1">
                    Tersedia: <span x-text="f.available_seats"></span>
                  </span>
                </div>
                <div class="mt-2 text-sm font-semibold text-primary-700" x-text="f.price_per_person_formatted"></div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.edit')): ?>
              <button x-on:click="openEdit(f)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Edit</button>
              <?php endif; ?>
              
              <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'master.flight.delete')): ?>
              <button x-on:click="confirmDelete(f)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50">Hapus</button>
              <?php endif; ?>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float flex flex-col overflow-hidden my-4">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Penerbangan' : 'Tambah Penerbangan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Nama Maskapai <span class="text-red-500">*</span></label>
              <select x-model="form.airline_name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">-- Pilih Maskapai --</option>
                <template x-for="a in airlineList" :key="a.id">
                  <option :value="a.name" x-text="a.name"></option>
                </template>
              </select>
              <div x-show="errors.airline_name" class="text-red-500 text-xs mt-1" x-text="errors.airline_name"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nomor Penerbangan <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.flight_number" placeholder="GA-123" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.flight_number" class="text-red-500 text-xs mt-1" x-text="errors.flight_number"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Bandara Keberangkatan <span class="text-red-500">*</span></label>
              <select x-model="form.departure_airport" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">-- Pilih Bandara --</option>
                <template x-for="ap in airportList" :key="ap.id">
                  <option :value="ap.iata_code + ' - ' + ap.city" x-text="ap.iata_code + ' - ' + ap.city"></option>
                </template>
              </select>
              <div x-show="errors.departure_airport" class="text-red-500 text-xs mt-1" x-text="errors.departure_airport"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Bandara Kedatangan <span class="text-red-500">*</span></label>
              <select x-model="form.arrival_airport" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">-- Pilih Bandara --</option>
                <template x-for="ap in airportList" :key="ap.id">
                  <option :value="ap.iata_code + ' - ' + ap.city" x-text="ap.iata_code + ' - ' + ap.city"></option>
                </template>
              </select>
              <div x-show="errors.arrival_airport" class="text-red-500 text-xs mt-1" x-text="errors.arrival_airport"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Waktu Keberangkatan <span class="text-red-500">*</span></label>
              <input type="datetime-local" x-model="form.departure_time" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.departure_time" class="text-red-500 text-xs mt-1" x-text="errors.departure_time"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Waktu Kedatangan <span class="text-red-500">*</span></label>
              <input type="datetime-local" x-model="form.arrival_time" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.arrival_time" class="text-red-500 text-xs mt-1" x-text="errors.arrival_time"></div>
            </div>

            <!-- Transit Section (Optional) -->
            <div class="sm:col-span-2">
              <div class="flex items-center justify-between mb-2">
                <label class="text-sm text-slate-600">Transit (Opsional)</label>
                <button type="button" x-on:click="addTransit()" class="text-xs text-primary-600 hover:text-primary-700 flex items-center gap-1">
                  <i class='bx bx-plus-circle'></i> Tambah Transit
                </button>
              </div>
              
              <template x-if="form.transit_info && form.transit_info.length > 0">
                <div class="space-y-2">
                  <template x-for="(transit, index) in form.transit_info" :key="index">
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                      <div class="flex items-start justify-between mb-2">
                        <div class="text-xs font-medium text-slate-700">Transit <span x-text="index + 1"></span></div>
                        <button type="button" x-on:click="removeTransit(index)" class="text-red-600 hover:text-red-700">
                          <i class='bx bx-trash text-sm'></i>
                        </button>
                      </div>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="sm:col-span-2">
                          <label class="text-xs text-slate-600">Bandara Transit</label>
                          <select x-model="transit.airport" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                            <option value="">-- Pilih Bandara --</option>
                            <template x-for="ap in airportList" :key="ap.id">
                              <option :value="ap.iata_code + ' - ' + ap.city" x-text="ap.iata_code + ' - ' + ap.city + ' (' + ap.country + ')'"></option>
                            </template>
                          </select>
                        </div>
                        <div>
                          <label class="text-xs text-slate-600">Waktu Tiba</label>
                          <input type="time" x-model="transit.arrival_time" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                          <label class="text-xs text-slate-600">Waktu Berangkat</label>
                          <input type="time" x-model="transit.departure_time" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                          <label class="text-xs text-slate-600">Durasi Transit (menit)</label>
                          <input type="number" x-model.number="transit.duration_minutes" min="0" placeholder="120" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                          <div class="text-xs text-slate-500 mt-0.5">
                            <span x-show="transit.duration_minutes > 0" x-text="Math.floor(transit.duration_minutes / 60) + ' jam ' + (transit.duration_minutes % 60) + ' menit'"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </template>
              
              <template x-if="!form.transit_info || form.transit_info.length === 0">
                <div class="text-xs text-slate-500 italic p-3 rounded-xl border border-dashed border-slate-200 text-center">
                  Tidak ada transit (Direct Flight)
                </div>
              </template>
            </div>

            <div>
              <label class="text-sm text-slate-600">Biaya per Orang <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="form.price_per_person" placeholder="15000000" min="0" step="0.01" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.price_per_person" class="text-red-500 text-xs mt-1" x-text="errors.price_per_person"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Tipe Pesawat</label>
              <input type="text" x-model.trim="form.aircraft_type" placeholder="Boeing 777" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.aircraft_type" class="text-red-500 text-xs mt-1" x-text="errors.aircraft_type"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Host Seller Tiket</label>
              <input type="text" x-model.trim="form.seller_name" placeholder="Nama agen/seller tiket" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Telepon Host Seller</label>
              <input type="text" x-model.trim="form.seller_phone" placeholder="08xxxxxxxxxx" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.id_outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Outlet</option>
                <template x-for="outlet in outlets" :key="outlet.id_outlet">
                  <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
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

    <!-- Modal Hapus -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Penerbangan?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm"><span class="font-medium" x-text="toDelete?.airline_name"></span> • <span class="font-mono text-slate-600" x-text="toDelete?.flight_number"></span></div>
            <div class="text-xs text-slate-500 mt-1" x-text="toDelete?.route"></div>
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

    <!-- Modal: Flight Group Create/Edit -->
    <div x-show="showGroupForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeGroupForm()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float flex flex-col overflow-hidden my-4">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="groupForm.id ? 'Edit Group Penerbangan' : 'Buat Group Penerbangan'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeGroupForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="space-y-4">
            <div>
              <label class="text-sm text-slate-600">Nama Group <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="groupForm.group_name" placeholder="Contoh: Umroh Ramadhan 2026 - Jakarta" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="groupErrors.group_name" class="text-red-500 text-xs mt-1" x-text="groupErrors.group_name"></div>
            </div>
            
            <div>
              <label class="text-sm text-slate-600">Deskripsi</label>
              <textarea x-model="groupForm.description" rows="2" placeholder="Deskripsi group penerbangan..." class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
            </div>
            
            <div>
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="groupForm.id_outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Outlet</option>
                <template x-for="outlet in outlets" :key="outlet.id_outlet">
                  <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                </template>
              </select>
              <div x-show="groupErrors.id_outlet" class="text-red-500 text-xs mt-1" x-text="groupErrors.id_outlet"></div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="text-sm text-slate-600 font-medium">Penerbangan dalam Group <span class="text-red-500">*</span></label>
                <button type="button" x-on:click="addFlightToGroup()" class="text-xs text-primary-600 hover:text-primary-700 flex items-center gap-1">
                  <i class='bx bx-plus-circle'></i> Tambah Penerbangan
                </button>
              </div>
              
              <div class="space-y-2">
                <template x-for="(gf, index) in groupForm.flights" :key="index">
                  <div class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div class="flex items-start justify-between mb-2">
                      <div class="text-xs font-medium text-slate-700">Penerbangan <span x-text="index + 1"></span></div>
                      <button type="button" x-on:click="removeFlightFromGroup(index)" class="text-red-600 hover:text-red-700">
                        <i class='bx bx-trash text-sm'></i>
                      </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                      <div>
                        <label class="text-xs text-slate-600">Pilih Penerbangan</label>
                        <select x-model="gf.id_flight" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                          <option value="">-- Pilih Penerbangan --</option>
                          <template x-for="f in flights" :key="f.id">
                            <option :value="f.id" x-text="f.airline_name + ' ' + f.flight_number + ' - ' + f.route"></option>
                          </template>
                        </select>
                      </div>
                      <div>
                        <label class="text-xs text-slate-600">Tipe</label>
                        <select x-model="gf.flight_type" class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                          <option value="departure">Keberangkatan</option>
                          <option value="return">Kepulangan</option>
                          <option value="transit">Transit</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </template>
                
                <div x-show="groupForm.flights.length === 0" class="text-xs text-slate-500 italic p-3 rounded-xl border border-dashed border-slate-200 text-center">
                  Belum ada penerbangan. Klik "Tambah Penerbangan" untuk menambahkan.
                </div>
              </div>
              <div x-show="groupErrors.flights" class="text-red-500 text-xs mt-1" x-text="groupErrors.flights"></div>
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeGroupForm()">Batal</button>
          <button x-on:click="submitGroupForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="saving" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Delete Group Confirmation -->
    <div x-show="toDeleteGroup" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDeleteGroup=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Group Penerbangan?</div>
          <p class="text-slate-600 mt-1">Group akan dihapus secara permanen. Penerbangan individual tidak akan terhapus.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="toDeleteGroup?.group_name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="toDeleteGroup?.description"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDeleteGroup=null">Batal</button>
          <button x-on:click="deleteGroupNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
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
    function flightCrud(){
      return {
        // State management
        activeTab: 'groups', // Default to groups
        flights: [],
        flightGroups: [],
        airlines: [],
        airlineList: [],
        airportList: [],
        outlets: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        airlineFilter: 'ALL',
        routeFilter: '',
        sortKey: 'airline',
        sortDir: 'asc',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          airline_name: '', 
          flight_number: '', 
          departure_airport: '', 
          arrival_airport: '', 
          departure_time: '',
          arrival_time: '',
          capacity: '',
          aircraft_type: '',
          price_per_person: 0,
          id_outlet: ''
        },
        errors: {},
        
        // Group form state
        showGroupForm: false,
        groupForm: {
          id: null,
          group_name: '',
          description: '',
          id_outlet: '',
          flights: []
        },
        groupErrors: {},
        
        // Delete confirmation
        toDelete: null,
        toDeleteGroup: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          try {
            await Promise.all([
              this.fetchGroups(),
              this.fetchData(),
              this.fetchAirlines(),
              this.fetchOutlets(),
              this.fetchAirlineList(),
              this.fetchAirportList()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchGroups(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search
            });

            const response = await fetch(`<?php echo e(route('admin.inventaris.flight-group.data')); ?>?${params}`);
            const data = await response.json();
            
            this.flightGroups = data.data;
          } catch (error) {
            console.error('Error fetching groups:', error);
            this.showToastMessage('Gagal memuat data group', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              airline_filter: this.airlineFilter,
              route_filter: this.routeFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`<?php echo e(route('admin.inventaris.flight.data')); ?>?${params}`);
            const data = await response.json();
            
            this.flights = data.data.map(item => ({
              id: item.id,
              airline_name: item.airline || item.airline_name,
              flight_number: item.flight_number,
              route: item.route,
              departure_time: item.departure_time,
              arrival_time: item.arrival_time,
              capacity: item.capacity,
              available_seats: item.available_seats,
              aircraft_type: item.aircraft_type,
              price_per_person: item.price_per_person_raw || 0,
              price_per_person_formatted: item.price_per_person || 'Rp 0',
              id_outlet: item.id_outlet
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchAirlines(){
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.flight.airlines")); ?>');
            const data = await response.json();
            this.airlines = data;
          } catch (error) {
            console.error('Error fetching airlines:', error);
          }
        },

        async fetchOutlets(){
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.outlet.data")); ?>');
            const data = await response.json();
            this.outlets = data.data.map(item => ({
              id_outlet: item.id_outlet || item.id,
              nama_outlet: item.name || item.nama_outlet
            }));
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async fetchAirlineList(){
          try {
            const res = await fetch('<?php echo e(route("admin.inventaris.airline.list")); ?>');
            this.airlineList = await res.json();
          } catch(e) { console.error('Error fetching airline list:', e); }
        },

        async fetchAirportList(){
          try {
            const res = await fetch('<?php echo e(route("admin.inventaris.airport.list")); ?>');
            this.airportList = await res.json();
          } catch(e) { console.error('Error fetching airport list:', e); }
        },

        openCreate(){ 
          this.form = { 
            id: null, 
            airline_name: '', 
            flight_number: '', 
            departure_airport: '', 
            arrival_airport: '', 
            departure_time: '',
            arrival_time: '',
            transit_info: [],
            aircraft_type: '',
            seller_name: '',
            seller_phone: '',
            id_outlet: ''
          }; 
          this.errors = {};
          this.showForm = true; 
        },

        addTransit() {
          if (!this.form.transit_info) {
            this.form.transit_info = [];
          }
          this.form.transit_info.push({
            airport: '',
            arrival_time: '',
            departure_time: '',
            duration_minutes: 0
          });
        },

        removeTransit(index) {
          this.form.transit_info.splice(index, 1);
        },

        async openEdit(f){ 
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.flight.show', '')); ?>/${f.id}`);
            const data = await response.json();
            
            this.form = { 
              id: data.id,
              airline_name: data.airline_name, 
              flight_number: data.flight_number, 
              departure_airport: data.departure_airport, 
              arrival_airport: data.arrival_airport, 
              departure_time: data.departure_time,
              arrival_time: data.arrival_time,
              transit_info: data.transit_info || [],
              aircraft_type: data.aircraft_type,
              price_per_person: data.price_per_person || 0,
              seller_name: data.seller_name || '',
              seller_phone: data.seller_phone || '',
              id_outlet: data.id_outlet
            }; 
            this.errors = {};
            this.showForm = true;
          } catch (error) {
            console.error('Error loading flight data:', error);
            this.showToastMessage('Gagal memuat data penerbangan', 'error');
          }
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
              ? `<?php echo e(route('admin.inventaris.flight.update', '')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.inventaris.flight.store")); ?>';

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

        confirmDelete(f){ 
          this.toDelete = f; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.flight.destroy', '')); ?>/${this.toDelete.id}`, {
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
              this.showToastMessage(result.message || result.error || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        // Flight Group Methods
        openCreateGroup() {
          this.groupForm = {
            id: null,
            group_name: '',
            description: '',
            id_outlet: '',
            flights: []
          };
          this.groupErrors = {};
          this.showGroupForm = true;
        },

        async openEditGroup(g) {
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.flight-group.show', '')); ?>/${g.id}`);
            const data = await response.json();
            
            this.groupForm = {
              id: data.id,
              group_name: data.group_name,
              description: data.description,
              id_outlet: data.id_outlet,
              flights: data.flights.map(f => ({
                id_flight: f.id_flight,
                flight_type: f.flight_type
              }))
            };
            this.groupErrors = {};
            this.showGroupForm = true;
          } catch (error) {
            console.error('Error loading group data:', error);
            this.showToastMessage('Gagal memuat data group', 'error');
          }
        },

        closeGroupForm() {
          this.showGroupForm = false;
          this.groupErrors = {};
        },

        addFlightToGroup() {
          this.groupForm.flights.push({
            id_flight: '',
            flight_type: 'departure'
          });
        },

        removeFlightFromGroup(index) {
          this.groupForm.flights.splice(index, 1);
        },

        async submitGroupForm() {
          this.saving = true;
          this.groupErrors = {};

          try {
            const url = this.groupForm.id 
              ? `<?php echo e(route('admin.inventaris.flight-group.update', '')); ?>/${this.groupForm.id}`
              : '<?php echo e(route("admin.inventaris.flight-group.store")); ?>';

            const method = this.groupForm.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(this.groupForm)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Group berhasil disimpan', 'success');
              this.closeGroupForm();
              await this.fetchGroups();
            } else {
              if (result.errors) {
                this.groupErrors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving group:', error);
            this.showToastMessage('Gagal menyimpan group', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDeleteGroup(g) {
          this.toDeleteGroup = g;
        },

        async deleteGroupNow() {
          if(!this.toDeleteGroup) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`<?php echo e(route('admin.inventaris.flight-group.destroy', '')); ?>/${this.toDeleteGroup.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Group berhasil dihapus', 'success');
              this.toDeleteGroup = null;
              await this.fetchGroups();
            } else {
              this.showToastMessage(result.message || result.error || 'Gagal menghapus group', 'error');
            }
          } catch (error) {
            console.error('Error deleting group:', error);
            this.showToastMessage('Gagal menghapus group', 'error');
          } finally {
            this.deleting = false;
          }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\inventaris\flight\index.blade.php ENDPATH**/ ?>