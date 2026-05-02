<x-layouts.admin :title="'SDM / Absensi'">
  <div x-data="attendanceCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Manajemen Absensi</h1>
        <p class="text-slate-600 text-sm">Kelola data kehadiran karyawan</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button x-on:click="openSetWorkHours()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700">
          <i class='bx bx-time-five text-lg'></i> Set Jam Kerja
        </button>
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Absensi
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <!-- Hadir -->
      <div class="rounded-2xl border border-emerald-200 bg-white shadow-card p-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-700 border border-emerald-100 shrink-0">
            <i class='bx bx-check-circle text-2xl'></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs text-emerald-600 font-medium uppercase">Hadir Hari Ini</div>
            <div class="text-2xl font-bold text-slate-800" x-text="statistics.hadir || 0"></div>
          </div>
        </div>
      </div>

      <!-- Terlambat -->
      <div class="rounded-2xl border border-amber-200 bg-white shadow-card p-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-100 shrink-0">
            <i class='bx bx-time text-2xl'></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs text-amber-600 font-medium uppercase">Terlambat</div>
            <div class="text-2xl font-bold text-slate-800" x-text="statistics.terlambat || 0"></div>
          </div>
        </div>
      </div>

      <!-- Tidak Hadir -->
      <div class="rounded-2xl border border-red-200 bg-white shadow-card p-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-red-50 text-red-700 border border-red-100 shrink-0">
            <i class='bx bx-x-circle text-2xl'></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs text-red-600 font-medium uppercase">Tidak Hadir</div>
            <div class="text-2xl font-bold text-slate-800" x-text="statistics.tidak_hadir || 0"></div>
          </div>
        </div>
      </div>

      <!-- Rata-rata Jam Kerja -->
      <div class="rounded-2xl border border-blue-200 bg-white shadow-card p-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-700 border border-blue-100 shrink-0">
            <i class='bx bx-briefcase text-2xl'></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs text-blue-600 font-medium uppercase">Rata-rata Jam Kerja</div>
            <div class="text-2xl font-bold text-slate-800" x-text="(statistics.avg_hours || 0).toFixed(1) + ' jam'"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs & Filters Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <!-- Tabs -->
      <div class="border-b border-slate-200">
        <div class="flex">
          <button 
            x-on:click="switchTab('daily')" 
            :class="currentTab === 'daily' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-slate-600 hover:text-slate-800'"
            class="px-6 py-3 font-medium text-sm transition">
            <i class='bx bx-calendar-alt'></i> Harian
          </button>
          <button 
            x-on:click="switchTab('monthly')" 
            :class="currentTab === 'monthly' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-slate-600 hover:text-slate-800'"
            class="px-6 py-3 font-medium text-sm transition">
            <i class='bx bx-calendar'></i> Bulanan
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
          <!-- Date Filter (Daily) -->
          <div x-show="currentTab === 'daily'" class="md:col-span-3">
            <label class="text-sm text-slate-600 mb-1 block">Tanggal</label>
            <input 
              type="date" 
              x-model="filterDate" 
              class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
          </div>

          <!-- Month Filter (Monthly) -->
          <div x-show="currentTab === 'monthly'" class="md:col-span-3">
            <label class="text-sm text-slate-600 mb-1 block">Bulan</label>
            <select x-model="filterMonth" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="1">Januari</option>
              <option value="2">Februari</option>
              <option value="3">Maret</option>
              <option value="4">April</option>
              <option value="5">Mei</option>
              <option value="6">Juni</option>
              <option value="7">Juli</option>
              <option value="8">Agustus</option>
              <option value="9">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>

          <!-- Year Filter (Monthly) -->
          <div x-show="currentTab === 'monthly'" class="md:col-span-2">
            <label class="text-sm text-slate-600 mb-1 block">Tahun</label>
            <select x-model="filterYear" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <template x-for="year in yearOptions" :key="year">
                <option :value="year" x-text="year"></option>
              </template>
            </select>
          </div>

          <!-- Search -->
          <div class="md:col-span-4">
            <label class="text-sm text-slate-600 mb-1 block">Cari</label>
            <div class="relative">
              <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
              <input 
                x-model="search" 
                x-on:input.debounce.500ms="fetchData()" 
                placeholder="Cari nama, jabatan..." 
                class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="md:col-span-3 flex items-end gap-2">
            <button 
              x-on:click="fetchData()" 
              class="flex-1 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 inline-flex items-center justify-center gap-2">
              <i class='bx bx-search'></i> Filter
            </button>
            <button 
              x-on:click="exportData()" 
              class="flex-1 rounded-xl border border-emerald-600 text-emerald-700 px-4 py-2 hover:bg-emerald-50 inline-flex items-center justify-center gap-2">
              <i class='bx bx-export'></i> Export
            </button>
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

    <!-- Daily Table -->
    <div x-show="currentTab === 'daily' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th class="text-left px-4 py-3 w-12">No</th>
                <th class="text-left px-4 py-3">ID</th>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Jabatan</th>
                <th class="text-center px-4 py-3">Jadwal Masuk</th>
                <th class="text-center px-4 py-3">Jadwal Pulang</th>
                <th class="text-center px-4 py-3">Status</th>
                <th class="text-center px-4 py-3"><i class='bx bx-log-in'></i> Masuk</th>
                <th class="text-center px-4 py-3"><i class='bx bx-log-out'></i> Keluar</th>
                <th class="text-center px-4 py-3"><i class='bx bx-coffee'></i> Istirahat Keluar</th>
                <th class="text-center px-4 py-3"><i class='bx bx-coffee'></i> Istirahat Masuk</th>
                <th class="text-center px-4 py-3"><i class='bx bx-time'></i> Lembur Masuk</th>
                <th class="text-center px-4 py-3"><i class='bx bx-time'></i> Lembur Keluar</th>
                <th class="text-center px-4 py-3">Total Jam</th>
                <th class="text-center px-4 py-3">Terlambat</th>
                <th class="text-center px-4 py-3">Pulang Cepat</th>
                <th class="text-center px-4 py-3">Lembur</th>
                <th class="text-center px-4 py-3 w-32">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(item, index) in attendances" :key="item.id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded bg-indigo-600 text-white text-xs font-mono" x-text="item.fingerprint_id"></span>
                  </td>
                  <td class="px-4 py-3 font-medium" x-text="item.employee_name"></td>
                  <td class="px-4 py-3 text-slate-600" x-text="item.position"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.schedule_in || '-'"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.schedule_out || '-'"></td>
                  <td class="px-4 py-3 text-center">
                    <span 
                      :class="{
                        'bg-emerald-50 text-emerald-700 border-emerald-200': item.status === 'present',
                        'bg-amber-50 text-amber-700 border-amber-200': item.status === 'late',
                        'bg-blue-50 text-blue-700 border-blue-200': item.status === 'leave',
                        'bg-orange-50 text-orange-700 border-orange-200': item.status === 'sick',
                        'bg-red-50 text-red-700 border-red-200': item.status === 'absent',
                        'bg-purple-50 text-purple-700 border-purple-200': item.status === 'permission'
                      }"
                      class="inline-block px-2 py-0.5 rounded-full text-xs border"
                      x-text="getStatusLabel(item.status)">
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center font-medium" x-text="item.clock_in || '-'"></td>
                  <td class="px-4 py-3 text-center font-medium" x-text="item.clock_out || '-'"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.break_out || '-'"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.break_in || '-'"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.overtime_in || '-'"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="item.overtime_out || '-'"></td>
                  <td class="px-4 py-3 text-center font-medium text-blue-600" x-text="item.hours_worked || '-'"></td>
                  <td class="px-4 py-3 text-center" :class="item.late_minutes > 0 ? 'text-red-600 font-medium' : 'text-slate-400'" x-text="item.late_minutes ? item.late_minutes + ' mnt' : '-'"></td>
                  <td class="px-4 py-3 text-center" :class="item.early_minutes > 0 ? 'text-orange-600 font-medium' : 'text-slate-400'" x-text="item.early_minutes ? item.early_minutes + ' mnt' : '-'"></td>
                  <td class="px-4 py-3 text-center" :class="item.overtime_minutes > 0 ? 'text-emerald-600 font-medium' : 'text-slate-400'" x-text="item.overtime_minutes ? item.overtime_minutes + ' mnt' : '-'"></td>
                  <td class="px-4 py-3">
                    <div class="flex gap-2 justify-center">
                      <button 
                        x-on:click="openEdit(item.id)" 
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                        <i class='bx bx-edit-alt'></i>
                      </button>
                      <button 
                        x-on:click="confirmDelete(item.id)" 
                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
              <tr x-show="attendances.length === 0">
                <td colspan="18" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Monthly Table -->
    <div x-show="currentTab === 'monthly' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-700">
              <tr>
                <th rowspan="2" class="text-left px-3 py-2 border-r border-slate-200">No</th>
                <th rowspan="2" class="text-left px-3 py-2 border-r border-slate-200">Nama</th>
                <th rowspan="2" class="text-left px-3 py-2 border-r border-slate-200">Jabatan</th>
                <th colspan="31" class="text-center px-3 py-2 border-r border-slate-200">Tanggal</th>
                <th colspan="5" class="text-center px-3 py-2">Summary</th>
              </tr>
              <tr>
                <template x-for="day in daysInMonth" :key="day">
                  <th class="text-center px-2 py-2 border-r border-slate-200 min-w-[30px]" x-text="day"></th>
                </template>
                <th class="text-center px-3 py-2 border-l-2 border-slate-300">Hadir</th>
                <th class="text-center px-3 py-2">Absen</th>
                <th class="text-center px-3 py-2">Terlambat</th>
                <th class="text-center px-3 py-2">Pulang Cepat</th>
                <th class="text-center px-3 py-2">Lembur (jam)</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(row, index) in monthlyData" :key="row.employee_id">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-3 py-2 border-r border-slate-200" x-text="index + 1"></td>
                  <td class="px-3 py-2 border-r border-slate-200 font-medium" x-text="row.employee_name"></td>
                  <td class="px-3 py-2 border-r border-slate-200 text-slate-600" x-text="row.position"></td>
                  <template x-for="day in daysInMonth" :key="day">
                    <td class="text-center px-2 py-2 border-r border-slate-200">
                      <span 
                        x-show="row['day_' + day]"
                        :class="{
                          'bg-emerald-50 text-emerald-700': row['day_' + day] === 'H',
                          'bg-amber-50 text-amber-700': row['day_' + day] === 'T',
                          'bg-blue-50 text-blue-700': row['day_' + day] === 'I',
                          'bg-orange-50 text-orange-700': row['day_' + day] === 'S',
                          'bg-red-50 text-red-700': row['day_' + day] === 'A',
                          'bg-purple-50 text-purple-700': row['day_' + day] === 'P'
                        }"
                        class="inline-block w-6 h-6 rounded leading-6 text-xs font-medium"
                        x-text="row['day_' + day]">
                      </span>
                      <span x-show="!row['day_' + day]" class="text-slate-300">-</span>
                    </td>
                  </template>
                  <td class="px-3 py-2 text-center font-bold text-emerald-600 border-l-2 border-slate-300" x-text="row.total_present || 0"></td>
                  <td class="px-3 py-2 text-center font-bold text-red-600" x-text="row.total_absent || 0"></td>
                  <td class="px-3 py-2 text-center text-amber-600" x-text="row.total_late || 0"></td>
                  <td class="px-3 py-2 text-center text-orange-600" x-text="row.total_early || 0"></td>
                  <td class="px-3 py-2 text-center text-blue-600" x-text="row.total_overtime || 0"></td>
                </tr>
              </template>
              <tr x-show="monthlyData.length === 0">
                <td colspan="39" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Legend -->
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
          <div class="flex flex-wrap gap-4 text-xs">
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-center leading-5 font-medium">H</span>
              <span class="text-slate-600">Hadir</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-center leading-5 font-medium">T</span>
              <span class="text-slate-600">Terlambat</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-center leading-5 font-medium">I</span>
              <span class="text-slate-600">Izin</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-center leading-5 font-medium">S</span>
              <span class="text-slate-600">Sakit</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-red-50 text-red-700 border border-red-200 text-center leading-5 font-medium">A</span>
              <span class="text-slate-600">Alpha</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="inline-block w-5 h-5 rounded bg-purple-50 text-purple-700 border border-purple-200 text-center leading-5 font-medium">P</span>
              <span class="text-slate-600">Izin Khusus</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3" x-cloak style="display: none;">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Absensi' : 'Tambah Absensi'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Employee -->
            <div>
              <label class="text-sm text-slate-600">Karyawan <span class="text-red-500">*</span></label>
              <select x-model="form.employee_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option value="">Pilih Karyawan</option>
                <template x-for="emp in employees" :key="emp.id">
                  <option :value="emp.id" x-text="emp.nama + ' - ' + emp.jabatan"></option>
                </template>
              </select>
              <div x-show="errors.employee_id" class="text-red-500 text-xs mt-1" x-text="errors.employee_id"></div>
            </div>

            <!-- Date -->
            <div>
              <label class="text-sm text-slate-600">Tanggal <span class="text-red-500">*</span></label>
              <input type="date" x-model="form.date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.date" class="text-red-500 text-xs mt-1" x-text="errors.date"></div>
            </div>

            <!-- Clock In -->
            <div>
              <label class="text-sm text-slate-600">Jam Masuk</label>
              <input type="time" x-model="form.clock_in" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.clock_in" class="text-red-500 text-xs mt-1" x-text="errors.clock_in"></div>
            </div>

            <!-- Clock Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Keluar</label>
              <input type="time" x-model="form.clock_out" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.clock_out" class="text-red-500 text-xs mt-1" x-text="errors.clock_out"></div>
            </div>

            <!-- Break Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Keluar Istirahat</label>
              <input type="time" x-model="form.break_out" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.break_out" class="text-red-500 text-xs mt-1" x-text="errors.break_out"></div>
            </div>

            <!-- Break In -->
            <div>
              <label class="text-sm text-slate-600">Jam Masuk Istirahat</label>
              <input type="time" x-model="form.break_in" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.break_in" class="text-red-500 text-xs mt-1" x-text="errors.break_in"></div>
            </div>

            <!-- Overtime In -->
            <div>
              <label class="text-sm text-slate-600">Jam Lembur Masuk</label>
              <input type="time" x-model="form.overtime_in" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.overtime_in" class="text-red-500 text-xs mt-1" x-text="errors.overtime_in"></div>
            </div>

            <!-- Overtime Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Lembur Keluar</label>
              <input type="time" x-model="form.overtime_out" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <div x-show="errors.overtime_out" class="text-red-500 text-xs mt-1" x-text="errors.overtime_out"></div>
            </div>

            <!-- Status -->
            <div>
              <label class="text-sm text-slate-600">Status <span class="text-red-500">*</span></label>
              <select x-model="form.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option value="present">Hadir</option>
                <option value="late">Terlambat</option>
                <option value="leave">Izin</option>
                <option value="sick">Sakit</option>
                <option value="absent">Alpha</option>
                <option value="permission">Izin Khusus</option>
              </select>
              <div x-show="errors.status" class="text-red-500 text-xs mt-1" x-text="errors.status"></div>
            </div>

            <!-- Notes -->
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Keterangan</label>
              <textarea x-model="form.notes" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"></textarea>
              <div x-show="errors.notes" class="text-red-500 text-xs mt-1" x-text="errors.notes"></div>
            </div>
          </div>

          <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-200">
            <div class="flex items-start gap-2 text-sm text-blue-700">
              <i class='bx bx-info-circle text-lg shrink-0'></i>
              <span>Terlambat, pulang cepat, lembur, dan total jam kerja akan dihitung otomatis.</span>
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

    <!-- Set Work Hours Modal -->
    <div x-show="showWorkHoursModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3" x-cloak style="display: none;">
      <div x-on:click.outside="showWorkHoursModal = false" class="w-full max-w-md bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-3 bg-indigo-600 text-white flex items-center justify-between">
          <div class="font-semibold flex items-center gap-2">
            <i class='bx bx-time-five text-xl'></i>
            <span>Set Jam Kerja</span>
          </div>
          <button class="p-2 -m-2 hover:bg-indigo-700 rounded-lg" x-on:click="showWorkHoursModal = false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-5 py-4">
          <div class="space-y-3">
            <!-- Employee Selection -->
            <div>
              <label class="text-sm text-slate-600">Karyawan</label>
              <select x-model="workHoursForm.employee_id" :disabled="workHoursForm.apply_to_all" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-200 disabled:bg-slate-50 disabled:text-slate-400">
                <option value="">Semua Karyawan</option>
                <template x-for="emp in employees" :key="emp.id">
                  <option :value="emp.id" x-text="emp.nama + ' - ' + emp.jabatan"></option>
                </template>
              </select>
              <div class="text-xs text-slate-500 mt-1">Kosongkan untuk set jadwal semua karyawan</div>
            </div>

            <!-- Clock In -->
            <div>
              <label class="text-sm text-slate-600">Jam Masuk <span class="text-red-500">*</span></label>
              <input type="time" x-model="workHoursForm.clock_in" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- Clock Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Pulang <span class="text-red-500">*</span></label>
              <input type="time" x-model="workHoursForm.clock_out" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- Apply to All -->
            <div>
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="workHoursForm.apply_to_all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-200">
                <span class="text-sm text-slate-700">Terapkan ke semua karyawan aktif</span>
              </label>
            </div>

            <!-- Warning -->
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
              <div class="flex items-start gap-2 text-sm text-amber-700">
                <i class='bx bx-error text-lg shrink-0'></i>
                <span>Jadwal kerja akan digunakan untuk menghitung keterlambatan dan lembur.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="showWorkHoursModal = false">Batal</button>
          <button x-on:click="submitWorkHours()" :disabled="savingWorkHours" class="rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
            <i class='bx bx-save' x-show="!savingWorkHours"></i>
            <i class='bx bx-loader-alt bx-spin' x-show="savingWorkHours"></i>
            <span x-text="savingWorkHours ? 'Menyimpan...' : 'Simpan Jadwal'"></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3" x-cloak style="display: none;">
      <div x-on:click.outside="showDeleteModal = false" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold text-lg">Hapus Data Absensi?</div>
          <p class="text-slate-600 mt-1">Data absensi akan dihapus secara permanen dari database.</p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="showDeleteModal = false">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="deleting" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
            </span>
            <span x-show="!deleting">Ya, Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50" x-cloak style="display: none;">
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
    function attendanceCrud() {
      return {
        // State
        attendances: [],
        monthlyData: [],
        employees: [],
        statistics: {},
        loading: false,
        saving: false,
        savingWorkHours: false,
        deleting: false,
        
        // Current tab
        currentTab: 'daily',
        
        // Filters
        filterDate: new Date().toISOString().split('T')[0],
        filterMonth: new Date().getMonth() + 1,
        filterYear: new Date().getFullYear(),
        search: '',
        
        // Year options
        yearOptions: [],
        
        // Monthly calendar
        daysInMonth: 31,
        
        // Modals
        showForm: false,
        showWorkHoursModal: false,
        showDeleteModal: false,
        
        // Form data
        form: {
          id: null,
          employee_id: '',
          date: '',
          clock_in: '',
          clock_out: '',
          break_out: '',
          break_in: '',
          overtime_in: '',
          overtime_out: '',
          status: 'present',
          notes: ''
        },
        errors: {},
        
        // Work hours form
        workHoursForm: {
          employee_id: '',
          clock_in: '08:00',
          clock_out: '17:00',
          apply_to_all: false
        },
        
        // Delete
        deleteId: null,
        
        // Toast
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
          this.generateYearOptions();
          await Promise.all([
            this.fetchEmployees(),
            this.fetchStatistics(),
            this.fetchData()
          ]);
        },

        generateYearOptions() {
          const currentYear = new Date().getFullYear();
          for (let year = currentYear - 2; year <= currentYear + 1; year++) {
            this.yearOptions.push(year);
          }
        },

        switchTab(tab) {
          this.currentTab = tab;
          this.fetchData();
        },

        async fetchData() {
          this.loading = true;
          try {
            if (this.currentTab === 'daily') {
              await this.fetchDailyData();
            } else {
              await this.fetchMonthlyData();
            }
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchDailyData() {
          const params = new URLSearchParams({
            date: this.filterDate,
            search: this.search
          });

          const response = await fetch(`{{ route('sdm.attendance.daily.table') }}?${params}`);
          const data = await response.json();
          
          this.attendances = data.data || [];
        },

        async fetchMonthlyData() {
          const params = new URLSearchParams({
            month: this.filterMonth,
            year: this.filterYear,
            search: this.search
          });

          const response = await fetch(`{{ route('sdm.attendance.monthly.table') }}?${params}`);
          const data = await response.json();
          
          this.monthlyData = data.data || [];
          this.daysInMonth = data.days_in_month || 31;
        },

        async fetchEmployees() {
          try {
            const response = await fetch('{{ route("sdm.attendance.employees") }}');
            const data = await response.json();
            this.employees = data;
          } catch (error) {
            console.error('Error fetching employees:', error);
          }
        },

        async fetchStatistics() {
          try {
            const params = new URLSearchParams({
              start_date: this.filterDate,
              end_date: this.filterDate
            });

            const response = await fetch(`{{ route('sdm.attendance.statistics') }}?${params}`);
            const data = await response.json();
            this.statistics = data;
          } catch (error) {
            console.error('Error fetching statistics:', error);
          }
        },

        openCreate() {
          this.form = {
            id: null,
            employee_id: '',
            date: this.filterDate,
            clock_in: '',
            clock_out: '',
            break_out: '',
            break_in: '',
            overtime_in: '',
            overtime_out: '',
            status: 'present',
            notes: ''
          };
          this.errors = {};
          this.showForm = true;
        },

        async openEdit(id) {
          try {
            const response = await fetch(`{{ route('sdm.attendance.show', '') }}/${id}`);
            const data = await response.json();
            
            this.form = {
              id: data.id,
              employee_id: data.employee_id,
              date: data.date,
              clock_in: data.clock_in || '',
              clock_out: data.clock_out || '',
              break_out: data.break_out || '',
              break_in: data.break_in || '',
              overtime_in: data.overtime_in || '',
              overtime_out: data.overtime_out || '',
              status: data.status,
              notes: data.notes || ''
            };
            this.errors = {};
            this.showForm = true;
          } catch (error) {
            console.error('Error fetching attendance:', error);
            this.showToastMessage('Gagal memuat data absensi', 'error');
          }
        },

        closeForm() {
          this.showForm = false;
          this.errors = {};
        },

        async submitForm() {
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('sdm.attendance.update', '') }}/${this.form.id}`
              : '{{ route("sdm.attendance.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
              await this.fetchStatistics();
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

        openSetWorkHours() {
          this.workHoursForm = {
            employee_id: '',
            clock_in: '08:00',
            clock_out: '17:00',
            apply_to_all: false
          };
          this.showWorkHoursModal = true;
        },

        async submitWorkHours() {
          this.savingWorkHours = true;

          try {
            const response = await fetch('{{ route("sdm.attendance.set.work.hours") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                clock_in: this.workHoursForm.clock_in,
                clock_out: this.workHoursForm.clock_out,
                employee_id: this.workHoursForm.employee_id || null,
                apply_to_all: this.workHoursForm.apply_to_all ? 1 : 0
              })
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Jadwal kerja berhasil disimpan', 'success');
              this.showWorkHoursModal = false;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
            }
          } catch (error) {
            console.error('Error saving work hours:', error);
            this.showToastMessage('Gagal menyimpan jadwal kerja', 'error');
          } finally {
            this.savingWorkHours = false;
          }
        },

        confirmDelete(id) {
          this.deleteId = id;
          this.showDeleteModal = true;
        },

        async deleteNow() {
          if (!this.deleteId) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('sdm.attendance.destroy', '') }}/${this.deleteId}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.showDeleteModal = false;
              this.deleteId = null;
              await this.fetchData();
              await this.fetchStatistics();
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

        exportData() {
          if (this.currentTab === 'daily') {
            window.open(`{{ route('sdm.attendance.export.excel') }}?start_date=${this.filterDate}&end_date=${this.filterDate}`, '_blank');
          } else {
            window.open(`{{ route('sdm.attendance.export.monthly.pdf') }}?month=${this.filterMonth}&year=${this.filterYear}`, '_blank');
          }
        },

        getStatusLabel(status) {
          const labels = {
            'present': 'Hadir',
            'late': 'Terlambat',
            'leave': 'Izin',
            'sick': 'Sakit',
            'absent': 'Alpha',
            'permission': 'Izin Khusus'
          };
          return labels[status] || status;
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
</x-layouts.admin>
