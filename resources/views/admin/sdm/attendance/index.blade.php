<x-layouts.admin :title="'SDM / Absensi'">
  <div x-data="attendanceCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Manajemen Absensi</h1>
        <p class="text-slate-600 text-sm">Kelola data kehadiran karyawan</p>
      </div>
      <div class="flex flex-wrap gap-2">
        {{-- Checkbox Outlet Filter --}}
        <div class="relative">
          <button x-on:click="showOutletDropdown = !showOutletDropdown" 
                  class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-primary-200 flex items-center justify-between min-w-[200px]">
            <span x-text="getSelectedOutletsText()"></span>
            <i class='bx bx-chevron-down ml-2' :class="showOutletDropdown ? 'rotate-180' : ''"></i>
          </button>
          
          <div x-show="showOutletDropdown" x-on:click.away="closeOutletDropdown()"
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="transform opacity-100 scale-100"
               x-transition:leave-end="transform opacity-0 scale-95"
               class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
            <div class="p-3 border-b border-slate-100">
              <button x-on:click="selectAllOutlets()" class="text-xs text-primary-600 hover:text-primary-800 mr-3">Pilih Semua</button>
              <button x-on:click="clearAllOutlets()" class="text-xs text-slate-600 hover:text-slate-800">Hapus Semua</button>
            </div>
            <div class="p-2">
              <template x-for="outlet in outlets" :key="outlet.id_outlet">
                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg cursor-pointer" x-on:click.stop>
                  <input type="checkbox" :value="outlet.id_outlet" x-model="selectedOutlets" x-on:change="onOutletSelectionChange()" 
                         class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                  <span class="text-sm" x-text="outlet.nama_outlet"></span>
                </label>
              </template>
            </div>
          </div>
        </div>
        <button x-on:click="openTimeSettings()" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 text-white px-4 py-2 hover:bg-purple-700">
          <i class='bx bx-cog text-lg'></i> Pengaturan Waktu
        </button>
        <button x-on:click="openSetWorkHours()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700">
          <i class='bx bx-time-five text-lg'></i> Set Jam Kerja
        </button>
        @hasPermission('hrm.absensi.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Absensi
        </button>
        @endhasPermission
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
            <div class="text-2xl font-bold text-slate-800" x-text="Math.abs(statistics.avg_hours || 0).toFixed(1) + ' jam'"></div>
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
            @hasPermission('hrm.absensi.export')
            <button 
              x-on:click="exportPdf()" 
              class="flex-1 rounded-xl border border-red-600 text-red-700 px-4 py-2 hover:bg-red-50 inline-flex items-center justify-center gap-2">
              <i class='bx bxs-file-pdf'></i> Export PDF
            </button>
            <button 
              x-show="currentTab === 'daily'"
              x-on:click="exportExcel()" 
              class="flex-1 rounded-xl border border-emerald-600 text-emerald-700 px-4 py-2 hover:bg-emerald-50 inline-flex items-center justify-center gap-2">
              <i class='bx bxs-file'></i> Export Excel
            </button>
            @endhasPermission
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
                <th class="text-center px-4 py-3"><i class='bx bx-camera'></i> Foto Masuk</th>
                <th class="text-center px-4 py-3"><i class='bx bx-log-out'></i> Keluar</th>
                <th class="text-center px-4 py-3"><i class='bx bx-camera'></i> Foto Keluar</th>
                <th class="text-center px-4 py-3">Terlambat</th>
                <th class="text-center px-4 py-3">Pulang Cepat</th>
                <th class="text-center px-4 py-3">Total Jam</th>
                <th class="text-center px-4 py-3 w-32">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(item, index) in attendances" :key="item.id || 'emp-' + item.employee_id + '-' + index">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-4 py-3" x-text="index + 1"></td>
                  <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded bg-indigo-600 text-white text-xs font-mono" x-text="item.fingerprint_id"></span>
                  </td>
                  <td class="px-4 py-3 font-medium" x-text="item.employee_name"></td>
                  <td class="px-4 py-3 text-slate-600" x-text="item.position"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="formatScheduleTime(item.schedule_in)"></td>
                  <td class="px-4 py-3 text-center text-slate-600" x-text="formatScheduleTime(item.schedule_out)"></td>
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
                  <!-- Foto/Lokasi Masuk -->
                  <td class="px-4 py-3 text-center">
                    <div class="flex flex-col items-center gap-1">
                      <!-- Tombol foto masuk (hanya jika ada foto) -->
                      <template x-if="item.clock_in_photo">
                        <button x-on:click="showPhotoModal(item.clock_in_photo, 'Foto Masuk - ' + item.employee_name)" 
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 px-2 py-1 hover:bg-blue-100">
                          <i class='bx bx-camera text-sm'></i>
                          <span class="text-xs">Foto</span>
                        </button>
                      </template>
                      <!-- Tombol lokasi masuk (hanya jika ada GPS dari Flutter) -->
                      <template x-if="item.source === 'online' && item.latitude && item.longitude">
                        <button x-on:click="viewLocation(item, 'in')"
                                class="inline-flex items-center gap-1 rounded-lg bg-green-50 border border-green-200 text-green-700 px-2 py-1 hover:bg-green-100"
                                title="Lihat Lokasi Masuk">
                          <i class='bx bx-map-pin text-sm'></i>
                          <span class="text-xs">Lokasi</span>
                        </button>
                      </template>
                      <!-- Fallback jika tidak ada foto maupun GPS -->
                      <template x-if="!item.clock_in_photo && !(item.source === 'online' && item.latitude && item.longitude)">
                        <span class="text-slate-400 text-xs">-</span>
                      </template>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-center font-medium" x-text="item.clock_out || '-'"></td>
                  <!-- Foto/Lokasi Keluar -->
                  <td class="px-4 py-3 text-center">
                    <div class="flex flex-col items-center gap-1">
                      <!-- Tombol foto keluar (hanya jika ada foto) -->
                      <template x-if="item.clock_out_photo">
                        <button x-on:click="showPhotoModal(item.clock_out_photo, 'Foto Keluar - ' + item.employee_name)" 
                                class="inline-flex items-center gap-1 rounded-lg bg-green-50 border border-green-200 text-green-700 px-2 py-1 hover:bg-green-100">
                          <i class='bx bx-camera text-sm'></i>
                          <span class="text-xs">Foto</span>
                        </button>
                      </template>
                      <!-- Tombol lokasi keluar (hanya jika ada GPS dari Flutter) -->
                      <template x-if="item.source === 'online' && (item.clock_out_latitude || item.latitude)">
                        <button x-on:click="viewLocation(item, 'out')"
                                class="inline-flex items-center gap-1 rounded-lg bg-orange-50 border border-orange-200 text-orange-700 px-2 py-1 hover:bg-orange-100"
                                title="Lihat Lokasi Keluar">
                          <i class='bx bx-map-pin text-sm'></i>
                          <span class="text-xs">Lokasi</span>
                        </button>
                      </template>
                      <!-- Fallback -->
                      <template x-if="!item.clock_out_photo && !(item.source === 'online' && (item.clock_out_latitude || item.latitude))">
                        <span class="text-slate-400 text-xs">-</span>
                      </template>
                    </div>
                  </td>
                  
                  <!-- Terlambat -->
                  <td class="px-4 py-3 text-center" 
                      x-data="{ late: calculateLateMinutes(item) }"
                      :class="late > 0 ? 'text-red-600 font-medium' : 'text-slate-400'" 
                      x-text="late > 0 ? late + ' mnt' : '-'"></td>
                  
                  <!-- Pulang Cepat -->
                  <td class="px-4 py-3 text-center" 
                      x-data="{ early: calculateEarlyMinutes(item) }"
                      :class="early > 0 ? 'text-orange-600 font-medium' : 'text-slate-400'" 
                      x-text="early > 0 ? early + ' mnt' : '-'"></td>

                  <!-- Total Jam -->
                  <td class="px-4 py-3 text-center font-medium text-blue-600" 
                      x-text="calculateHoursWorked(item)"></td>
                      
                  <td class="px-4 py-3">
                    <div class="flex gap-2 justify-center">
                      @hasPermission('hrm.absensi.edit')
                      <button 
                        x-on:click="item.id ? openEdit(item.id, item.employee_id, filterDate) : openCreateWithEmployee(item.employee_id, filterDate)" 
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                        <i class='bx bx-edit-alt'></i>
                      </button>
                      @endhasPermission
                      @hasPermission('hrm.absensi.delete')
                      <button 
                        x-on:click="confirmDelete(item.id)" 
                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                        <i class='bx bx-trash'></i>
                      </button>
                      @endhasPermission
                    </div>
                  </td>
                </tr>
              </template>
              <tr x-show="attendances.length === 0">
                <td colspan="16" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
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
                <th class="text-center px-3 py-2">Total Jam</th>
                <th class="text-center px-3 py-2">Terlambat</th>
                <th class="text-center px-3 py-2">Pulang Cepat</th>
                <th class="text-center px-3 py-2">Lembur (jam)</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(row, index) in monthlyData" :key="row.employee_id || index">
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                  <td class="px-3 py-2 border-r border-slate-200" x-text="index + 1"></td>
                  <td class="px-3 py-2 border-r border-slate-200 font-medium" x-text="row.employee_name"></td>
                  <td class="px-3 py-2 border-r border-slate-200 text-slate-600" x-text="row.position"></td>
                  <template x-for="day in daysInMonth" :key="'day-' + row.employee_id + '-' + day">
                    <td class="text-center px-2 py-2 border-r border-slate-200">
                      <template x-if="row['day_' + day]">
                        <span 
                          :class="{
                            'bg-emerald-50 text-emerald-700 border border-emerald-200': getStatusCode(row['day_' + day]) === 'H',
                            'bg-amber-50 text-amber-700 border border-amber-200': getStatusCode(row['day_' + day]) === 'T',
                            'bg-blue-50 text-blue-700 border border-blue-200': getStatusCode(row['day_' + day]) === 'I',
                            'bg-orange-50 text-orange-700 border border-orange-200': getStatusCode(row['day_' + day]) === 'S',
                            'bg-red-50 text-red-700 border border-red-200': getStatusCode(row['day_' + day]) === 'A',
                            'bg-purple-50 text-purple-700 border border-purple-200': getStatusCode(row['day_' + day]) === 'P'
                          }"
                          class="inline-block w-6 h-6 rounded leading-6 text-xs font-medium"
                          x-text="getStatusCode(row['day_' + day])">
                        </span>
                      </template>
                      <template x-if="!row['day_' + day]">
                        <span class="text-slate-300">-</span>
                      </template>
                    </td>
                  </template>
                  <td class="px-3 py-2 text-center font-bold text-emerald-600 border-l-2 border-slate-300" x-text="row.total_present || 0"></td>
                  <td class="px-3 py-2 text-center font-bold text-red-600" x-text="row.total_absent || 0"></td>
                  <td class="px-3 py-2 text-center font-bold text-blue-600" x-text="row.total_hours ? row.total_hours.toFixed(2) : '0.00'"></td>
                  <td class="px-3 py-2 text-center text-amber-600" x-text="row.total_late > 0 ? row.total_late + ' mnt' : '-'"></td>
                  <td class="px-3 py-2 text-center text-orange-600" x-text="row.total_early > 0 ? row.total_early + ' mnt' : '-'"></td>
                  <td class="px-3 py-2 text-center text-blue-600" x-text="row.total_overtime > 0 ? row.total_overtime + ' jam' : '-'"></td>
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
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto" x-cloak style="display: none;">
      <div x-on:click.outside="closeForm()" class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
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
              <label class="text-sm text-slate-600">Jam Masuk (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.clock_in" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
              <div x-show="errors.clock_in" class="text-red-500 text-xs mt-1" x-text="errors.clock_in"></div>
            </div>

            <!-- Clock Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Keluar (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.clock_out" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
              <div x-show="errors.clock_out" class="text-red-500 text-xs mt-1" x-text="errors.clock_out"></div>
            </div>

            <!-- Break In -->
            <div>
              <label class="text-sm text-slate-600">Jam Mulai Istirahat (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.break_in" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
              <div x-show="errors.break_in" class="text-red-500 text-xs mt-1" x-text="errors.break_in"></div>
            </div>

            <!-- Break Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Selesai Istirahat (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.break_out" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
              <div x-show="errors.break_out" class="text-red-500 text-xs mt-1" x-text="errors.break_out"></div>
            </div>

            <!-- Overtime In -->
            <div>
              <label class="text-sm text-slate-600">Jam Lembur Masuk (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.overtime_in" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
              <div x-show="errors.overtime_in" class="text-red-500 text-xs mt-1" x-text="errors.overtime_in"></div>
            </div>

            <!-- Overtime Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Lembur Keluar (HH:MM atau HH:MM:SS)</label>
              <input type="time" 
                     x-model="form.overtime_out" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
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
    <div x-show="showWorkHoursModal" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20" x-cloak style="display: none;">
      <div x-on:click.outside="showWorkHoursModal = false" class="w-full max-w-2xl bg-white rounded-2xl shadow-float overflow-hidden">
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
              <label class="text-sm text-slate-600">Jam Masuk (HH:MM atau HH:MM:SS) <span class="text-red-500">*</span></label>
              <input type="time" 
                     x-model="workHoursForm.clock_in" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
            </div>

            <!-- Clock Out -->
            <div>
              <label class="text-sm text-slate-600">Jam Pulang (HH:MM atau HH:MM:SS) <span class="text-red-500">*</span></label>
              <input type="time" 
                     x-model="workHoursForm.clock_out" 
                      
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-indigo-200" 
                     placeholder="HH:MM atau HH:MM:SS (24 jam)" 
                     pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"
                     x-on:change="ensureTimeFormat($event.target)"
                     x-on:blur="ensureTimeFormat($event.target)">
            </div>

            <!-- Apply to All -->
            <div>
              <label class="inline-flex items-center gap-2 cursor-pointer" x-on:click.stop>
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

    <!-- Time Settings Modal -->
    <div x-show="showTimeSettingsModal" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto" x-cloak style="display: none;">
      <div x-on:click.outside="showTimeSettingsModal = false" class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4 flex flex-col max-h-[calc(100vh-2rem)]">
        <!-- Header - Fixed -->
        <div class="px-5 py-3 bg-purple-600 text-white flex items-center justify-between rounded-t-2xl flex-shrink-0">
          <div class="font-semibold flex items-center gap-2">
            <i class='bx bx-cog text-xl'></i>
            <span>Pengaturan Waktu RFID</span>
          </div>
          <button class="p-2 -m-2 hover:bg-purple-700 rounded-lg" x-on:click="showTimeSettingsModal = false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <!-- Content - Scrollable -->
        <div class="flex-1 overflow-y-auto">
          <div class="px-5 py-4">
            <div class="space-y-5">
              <!-- Info -->
              <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                <div class="flex items-start gap-2 text-sm text-blue-700">
                  <i class='bx bx-info-circle text-lg shrink-0'></i>
                  <div>
                    <div class="font-medium">Pengaturan Jam Tap RFID</div>
                    <div class="mt-1">Tentukan rentang waktu kapan karyawan boleh tap untuk masuk dan pulang. Format 24 jam (HH:MM).</div>
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div x-show="loadingTimeSettings" class="text-center py-8">
                <div class="inline-flex items-center gap-2 text-slate-600">
                  <i class='bx bx-loader-alt bx-spin text-xl'></i>
                  <span>Memuat pengaturan...</span>
                </div>
              </div>

              <!-- Jam Masuk -->
              <template x-if="!loadingTimeSettings">
                <div class="space-y-4">
                  <div class="border border-emerald-200 rounded-xl p-4 bg-emerald-50/40">
                    <div class="flex items-center gap-2 mb-3">
                      <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                      <h4 class="font-semibold text-emerald-800"><i class='bx bx-log-in mr-1'></i>Jam Masuk</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Karyawan hanya bisa tap masuk dalam rentang jam ini.</p>
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="text-sm font-medium text-slate-700">Mulai</label>
                        <div class="relative mt-1">
                          <input type="text" id="rfid_ci_start" readonly
                                 class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 cursor-pointer bg-white"
                                 placeholder="HH:MM">
                          <i class='bx bx-time-five absolute right-3 top-2.5 text-slate-400 pointer-events-none'></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Default: 06:00</p>
                      </div>
                      <div>
                        <label class="text-sm font-medium text-slate-700">Sampai</label>
                        <div class="relative mt-1">
                          <input type="text" id="rfid_ci_end" readonly
                                 class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 cursor-pointer bg-white"
                                 placeholder="HH:MM">
                          <i class='bx bx-time-five absolute right-3 top-2.5 text-slate-400 pointer-events-none'></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Default: 08:30</p>
                      </div>
                    </div>
                  </div>

                  <!-- Jam Pulang -->
                  <div class="border border-blue-200 rounded-xl p-4 bg-blue-50/40">
                    <div class="flex items-center gap-2 mb-3">
                      <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                      <h4 class="font-semibold text-blue-800"><i class='bx bx-log-out mr-1'></i>Jam Pulang</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Karyawan hanya bisa tap pulang dalam rentang jam ini.</p>
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="text-sm font-medium text-slate-700">Mulai</label>
                        <div class="relative mt-1">
                          <input type="text" id="rfid_co_start" readonly
                                 class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 cursor-pointer bg-white"
                                 placeholder="HH:MM">
                          <i class='bx bx-time-five absolute right-3 top-2.5 text-slate-400 pointer-events-none'></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Default: 17:00</p>
                      </div>
                      <div>
                        <label class="text-sm font-medium text-slate-700">Sampai</label>
                        <div class="relative mt-1">
                          <input type="text" id="rfid_co_end" readonly
                                 class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 cursor-pointer bg-white"
                                 placeholder="HH:MM">
                          <i class='bx bx-time-five absolute right-3 top-2.5 text-slate-400 pointer-events-none'></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Default: 23:59</p>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Footer - Fixed -->
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2 rounded-b-2xl flex-shrink-0 bg-white">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="showTimeSettingsModal = false">Batal</button>
          <button x-on:click="saveTimeSettings()" :disabled="savingTimeSettings" class="rounded-xl bg-purple-600 text-white px-4 py-2 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
            <i class='bx bx-save' x-show="!savingTimeSettings"></i>
            <i class='bx bx-loader-alt bx-spin' x-show="savingTimeSettings"></i>
            <span x-text="savingTimeSettings ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 pt-20" x-cloak style="display: none;">
      <div x-on:click.outside="showDeleteModal = false" class="w-full max-w-lg rounded-2xl bg-white shadow-float overflow-hidden">
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

    <!-- Photo Modal -->
    <div x-show="showPhotoModalDialog" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-3" x-cloak style="display: none;">
      <div x-on:click.outside="closePhotoModal()" class="w-full max-w-2xl bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-3 bg-slate-600 text-white flex items-center justify-between">
          <div class="font-semibold flex items-center gap-2">
            <i class='bx bx-camera text-xl'></i>
            <span x-text="photoModalTitle"></span>
          </div>
          <button class="p-2 -m-2 hover:bg-slate-700 rounded-lg" x-on:click="closePhotoModal()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="p-5">
          <div class="text-center">
            <img :src="photoModalSrc" :alt="photoModalTitle" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg" 
                 x-on:error="handlePhotoError($event)">
            <div class="mt-3 text-sm text-slate-600" x-text="photoModalTitle"></div>
          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end">
          <button class="rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700" x-on:click="closePhotoModal()">Tutup</button>
        </div>
      </div>
    </div>

    <!-- Location Modal -->
    <div x-show="showLocationModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/60 p-3 overflow-y-auto" x-cloak style="display: none;">
      <div x-on:click.outside="showLocationModal = false" class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4">
        <!-- Header -->
        <div class="px-5 py-3 text-white flex items-center justify-between rounded-t-2xl"
             :class="locationData.clock_label === 'Keluar/Pulang' ? 'bg-orange-500' : 'bg-green-600'">
          <div class="font-semibold flex items-center gap-2">
            <i class='bx bx-map-pin text-xl'></i>
            <span>Lokasi Absen <span x-text="locationData.clock_label || 'Masuk'"></span></span>
          </div>
          <button class="p-2 -m-2 hover:bg-black/20 rounded-lg" x-on:click="showLocationModal = false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="p-5">
          <!-- Info baris atas -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            <div>
              <div class="text-slate-500 text-xs uppercase font-medium">Karyawan</div>
              <div class="font-semibold text-slate-800 text-sm" x-text="locationData.employee_name || '-'"></div>
            </div>
            <div>
              <div class="text-slate-500 text-xs uppercase font-medium">Tanggal</div>
              <div class="font-semibold text-slate-800 text-sm" x-text="locationData.date || '-'"></div>
            </div>
            <div>
              <div class="text-slate-500 text-xs uppercase font-medium">Waktu</div>
              <div class="font-semibold text-sm"
                   :class="locationData.clock_label === 'Keluar/Pulang' ? 'text-orange-600' : 'text-green-600'"
                   x-text="(locationData.clock_label || 'Masuk') + ': ' + (locationData.clock_in || '-')"></div>
            </div>
            <div>
              <div class="text-slate-500 text-xs uppercase font-medium">Perangkat</div>
              <div class="text-slate-600 text-xs" x-text="locationData.device_info || '-'"></div>
            </div>
          </div>

          <!-- Koordinat & Alamat -->
          <div class="mb-4 space-y-2">
            <div class="flex items-start gap-2 text-sm">
              <i class='bx bx-current-location text-blue-600 text-lg shrink-0 mt-0.5'></i>
              <div>
                <div class="font-medium text-slate-700">Koordinat GPS</div>
                <div class="text-slate-600 font-mono text-xs"
                     x-text="locationData.latitude && locationData.longitude ? locationData.latitude + ', ' + locationData.longitude : 'Tidak tersedia'"></div>
              </div>
            </div>
            <div class="flex items-start gap-2 text-sm" x-show="locationData.location_address">
              <i class='bx bx-map text-blue-600 text-lg shrink-0 mt-0.5'></i>
              <div>
                <div class="font-medium text-slate-700">Alamat</div>
                <div class="text-slate-600 text-sm" x-text="locationData.location_address || '-'"></div>
              </div>
            </div>
          </div>

          <!-- Map embed via OpenStreetMap (tidak butuh API key, render konsisten) -->
          <div class="rounded-xl overflow-hidden border border-slate-200">
            <div x-show="locationData.latitude && locationData.longitude">
              <iframe
                :src="locationData.latitude && locationData.longitude
                  ? 'https://www.openstreetmap.org/export/embed.html?bbox=' + (parseFloat(locationData.longitude)-0.005) + '%2C' + (parseFloat(locationData.latitude)-0.005) + '%2C' + (parseFloat(locationData.longitude)+0.005) + '%2C' + (parseFloat(locationData.latitude)+0.005) + '&layer=mapnik&marker=' + locationData.latitude + '%2C' + locationData.longitude
                  : 'about:blank'"
                style="border:0; display:block; width:100%; height:450px;"
                loading="lazy"
                allowfullscreen>
              </iframe>
            </div>
            <div x-show="!locationData.latitude || !locationData.longitude"
                 style="height:450px;"
                 class="flex items-center justify-center text-slate-400 bg-slate-100">
              <div class="text-center">
                <i class='bx bx-map-alt text-4xl block mb-2'></i>
                <span class="text-sm">Koordinat GPS tidak tersedia</span>
              </div>
            </div>
          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between rounded-b-2xl">
          <a :href="'https://www.google.com/maps?q=' + locationData.latitude + ',' + locationData.longitude"
             target="_blank"
             class="inline-flex items-center gap-2 rounded-xl border border-blue-200 text-blue-700 px-4 py-2 hover:bg-blue-50 text-sm"
             x-show="locationData.latitude && locationData.longitude">
            <i class='bx bx-link-external'></i> Buka di Google Maps
          </a>
          <button class="rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700 text-sm ml-auto" x-on:click="showLocationModal = false">Tutup</button>
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
      const today = new Date();
      const currentYear = today.getFullYear();
      
      // Format tanggal hari ini dengan timezone lokal
      const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      };
      
      return {
        // State
        attendances: [],
        monthlyData: [],
        employees: [],
        outlets: [],
        selectedOutlets: [],
        showOutletDropdown: false,
        statistics: {},
        loading: false,
        saving: false,
        savingWorkHours: false,
        deleting: false,
        
        // Current tab
        currentTab: 'daily',
        
        // Filters
        filterDate: formatLocalDate(today),
        filterMonth: today.getMonth() + 1,
        filterYear: currentYear,
        search: '',
        
        // Year options - Initialize immediately
        yearOptions: [currentYear - 2, currentYear - 1, currentYear, currentYear + 1],
        
        // Monthly calendar
        daysInMonth: 31,
        
        // Modals
        showForm: false,
        showWorkHoursModal: false,
        showTimeSettingsModal: false,
        showDeleteModal: false,
        showPhotoModalDialog: false,
        
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
        
        // Time settings (simplified: only check_in and check_out windows)
        timeSettings: [],
        loadingTimeSettings: false,
        savingTimeSettings: false,
        // check_in window
        rfidCheckIn: { start: '06:00', end: '08:30' },
        // check_out window
        rfidCheckOut: { start: '17:00', end: '23:59' },
        testTime: '',
        testResult: null,
        testingTime: false,
        
        // Delete
        deleteId: null,
        
        // Photo Modal
        photoModalSrc: '',
        photoModalTitle: '',

        // Location Modal
        showLocationModal: false,
        locationData: {},
        
        // Toast
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
          await this.loadOutlets();
          // Panggil fetchEmployees setelah outlets dimuat dan selected
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.fetchEmployees(),
              this.fetchStatistics(),
              this.fetchData()
            ]);
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch('{{ route("finance.outlets.data") }}');
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
              // Set default to first outlet if available
              if (this.outlets.length > 0 && this.selectedOutlets.length === 0) {
                this.selectedOutlets = [this.outlets[0].id_outlet];
                // Langsung panggil fetchEmployees setelah outlet default diset
                await this.fetchEmployees();
                console.log('✅ Default outlet set and employees loaded');
              }
              console.log('✅ Loaded outlets:', this.outlets.length);
            }
          } catch (error) {
            console.error('❌ Error loading outlets:', error);
          }
        },

        // Checkbox Management Functions
        getSelectedOutletsText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet === this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Outlet Terpilih';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Terpilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        async onOutletSelectionChange() {
          console.log('🔄 Outlet selection changed:', this.selectedOutlets);
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.fetchEmployees(),
              this.fetchStatistics(),
              this.fetchData()
            ]);
          }
        },

        // Computed calculation functions
        calculateHoursWorked(item) {
          if (!item.clock_in || !item.clock_out) return '-';
          
          try {
            const clockIn = new Date(`2000-01-01T${item.clock_in}`);
            const clockOut = new Date(`2000-01-01T${item.clock_out}`);
            
            // Handle overnight shifts
            if (clockOut < clockIn) {
              clockOut.setDate(clockOut.getDate() + 1);
            }
            
            let totalMinutes = 0;
            
            // Rumus: total_jam_kerja = [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)
            if (item.break_in && item.break_out) {
              // Ada waktu istirahat
              const breakIn = new Date(`2000-01-01T${item.break_in}`);   // mulai istirahat
              const breakOut = new Date(`2000-01-01T${item.break_out}`); // selesai istirahat
              
              // Handle overnight break
              if (breakOut < breakIn) {
                breakOut.setDate(breakOut.getDate() + 1);
              }
              
              // Waktu kerja sebelum istirahat: break_in - clock_in
              const beforeBreakMinutes = (breakIn - clockIn) / 1000 / 60;
              
              // Waktu kerja setelah istirahat: clock_out - break_out
              const afterBreakMinutes = (clockOut - breakOut) / 1000 / 60;
              
              // Total jam kerja normal (tanpa lembur)
              totalMinutes = beforeBreakMinutes + afterBreakMinutes;
            } else {
              // Tidak ada waktu istirahat: clock_out - clock_in
              totalMinutes = (clockOut - clockIn) / 1000 / 60;
            }
            
            // Tambahkan jam lembur jika ada: overtime_out - overtime_in
            if (item.overtime_in && item.overtime_out) {
              const overtimeIn = new Date(`2000-01-01T${item.overtime_in}`);
              const overtimeOut = new Date(`2000-01-01T${item.overtime_out}`);
              
              // Handle overnight overtime
              if (overtimeOut < overtimeIn) {
                overtimeOut.setDate(overtimeOut.getDate() + 1);
              }
              
              const overtimeMinutes = (overtimeOut - overtimeIn) / 1000 / 60;
              if (overtimeMinutes > 0) {
                totalMinutes += overtimeMinutes;
              }
            }
            
            if (totalMinutes <= 0) return '-';
            
            const hours = Math.floor(totalMinutes / 60);
            const minutes = Math.round(totalMinutes % 60);
            
            if (hours > 0 && minutes > 0) {
              return `${hours}j ${minutes}m`;
            } else if (hours > 0) {
              return `${hours} jam`;
            } else if (minutes > 0) {
              return `${minutes} mnt`;
            } else {
              return '-';
            }
          } catch (error) {
            console.error('Error calculating hours worked:', error);
            return '-';
          }
        },

        calculateLateMinutes(item) {
          if (!item.clock_in || !item.schedule_in) return 0;
          
          const clockIn = new Date(`2000-01-01 ${item.clock_in}`);
          const scheduleIn = new Date(`2000-01-01 ${item.schedule_in}`);
          
          if (clockIn > scheduleIn) {
            return Math.round((clockIn - scheduleIn) / 1000 / 60);
          }
          return 0;
        },

        calculateEarlyMinutes(item) {
          if (!item.clock_out || !item.schedule_out) return 0;
          
          const clockOut = new Date(`2000-01-01 ${item.clock_out}`);
          const scheduleOut = new Date(`2000-01-01 ${item.schedule_out}`);
          
          if (clockOut < scheduleOut) {
            return Math.round((scheduleOut - clockOut) / 1000 / 60);
          }
          return 0;
        },

        calculateOvertimeMinutes(item) {
          if (!item.overtime_in || !item.overtime_out) return 0;
          
          try {
            const overtimeIn = new Date(`2000-01-01T${item.overtime_in}`);
            const overtimeOut = new Date(`2000-01-01T${item.overtime_out}`);
            
            // Handle overnight overtime
            if (overtimeOut < overtimeIn) {
              overtimeOut.setDate(overtimeOut.getDate() + 1);
            }
            
            if (overtimeOut > overtimeIn) {
              return Math.round((overtimeOut - overtimeIn) / 1000 / 60);
            }
          } catch (error) {
            console.error('Error calculating overtime:', error);
          }
          return 0;
        },

        // Fungsi untuk menampilkan jam lembur dalam format yang mudah dibaca
        calculateOvertimeHours(item) {
          const overtimeMinutes = this.calculateOvertimeMinutes(item);
          if (overtimeMinutes <= 0) return '-';
          
          const hours = Math.floor(overtimeMinutes / 60);
          const minutes = overtimeMinutes % 60;
          
          if (hours > 0 && minutes > 0) {
            return `${hours}j ${minutes}m`;
          } else if (hours > 0) {
            return `${hours} jam`;
          } else if (minutes > 0) {
            return `${minutes} mnt`;
          } else {
            return '-';
          }
        },

        switchTab(tab) {
          this.currentTab = tab;
          // Fetch both data and statistics when switching tabs
          Promise.all([
            this.fetchData(),
            this.fetchStatistics()
          ]);
        },

        async fetchData() {
          this.loading = true;
          try {
            if (this.currentTab === 'daily') {
              await this.fetchDailyData();
            } else {
              await this.fetchMonthlyData();
            }
            // Update statistics setiap kali data berubah
            await this.fetchStatistics();
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchDailyData() {
          if (this.selectedOutlets.length === 0) {
            this.attendances = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('date', this.filterDate);
          params.append('search', this.search);

          const response = await fetch(`{{ route('sdm.attendance.daily.table') }}?${params}`);
          const data = await response.json();
          
          this.attendances = data.data || [];
          
          // Debug: Log first attendance to check data structure
          if (this.attendances.length > 0) {
            console.log('=== ATTENDANCE DATA DEBUG ===');
            console.log('Daily attendance sample:', this.attendances[0]);
            console.log('Available fields:', Object.keys(this.attendances[0]));
            console.log('--- CALCULATED FIELDS ---');
            console.log('hours_worked:', this.attendances[0].hours_worked, '(Type:', typeof this.attendances[0].hours_worked, ')');
            console.log('late_minutes:', this.attendances[0].late_minutes, '(Type:', typeof this.attendances[0].late_minutes, ')');
            console.log('early_minutes:', this.attendances[0].early_minutes, '(Type:', typeof this.attendances[0].early_minutes, ')');
            console.log('overtime_minutes:', this.attendances[0].overtime_minutes, '(Type:', typeof this.attendances[0].overtime_minutes, ')');
            console.log('========================');
          }
        },

        async fetchMonthlyData() {
          if (this.selectedOutlets.length === 0) {
            this.monthlyData = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('month', this.filterMonth);
          params.append('year', this.filterYear);
          params.append('search', this.search);

          const response = await fetch(`{{ route('sdm.attendance.monthly.table') }}?${params}`);
          const data = await response.json();
          
          this.monthlyData = data.data || [];
          this.daysInMonth = data.days_in_month || 31;
          
          // Debug: Log first row to see data structure
          if (this.monthlyData.length > 0) {
            console.log('Monthly data sample:', this.monthlyData[0]);
          }
        },

        async fetchEmployees() {
          try {
            if (this.selectedOutlets.length === 0) {
              console.log('⚠️ No outlets selected, clearing employees');
              this.employees = [];
              return;
            }

            console.log('🔄 Fetching employees for outlets:', this.selectedOutlets);

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });

            const response = await fetch(`{{ route("sdm.attendance.employees") }}?${params}`);
            const data = await response.json();
            this.employees = data;
            
            console.log('✅ Loaded', this.employees.length, 'employees');
          } catch (error) {
            console.error('❌ Error fetching employees:', error);
            this.employees = [];
          }
        },

        async fetchStatistics() {
          try {
            if (this.selectedOutlets.length === 0) {
              this.statistics = {};
              return;
            }

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });
            
            if (this.currentTab === 'daily') {
              // Untuk tab harian, gunakan tanggal yang dipilih
              params.append('start_date', this.filterDate);
              params.append('end_date', this.filterDate);
            } else {
              // Untuk tab bulanan, gunakan range bulan yang dipilih
              const startDate = `${this.filterYear}-${String(this.filterMonth).padStart(2, '0')}-01`;
              const endDate = new Date(this.filterYear, this.filterMonth, 0).toISOString().split('T')[0];
              
              params.append('start_date', startDate);
              params.append('end_date', endDate);
            }
            
            params.append('search', this.search);

            const response = await fetch(`{{ route('sdm.attendance.statistics') }}?${params}`);
            const data = await response.json();
            this.statistics = data;
          } catch (error) {
            console.error('Error fetching statistics:', error);
          }
        },

        async openCreate() {
          // Pastikan menggunakan tanggal hari ini
          const today = new Date();
          const todayFormatted = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
          
          // Pastikan employees sudah dimuat
          if (this.employees.length === 0 && this.selectedOutlets.length > 0) {
            console.log('🔄 Loading employees for modal...');
            await this.fetchEmployees();
          }
          
          this.form = {
            id: null,
            employee_id: '',
            date: todayFormatted,
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
          
          console.log('✅ Modal opened with', this.employees.length, 'employees available');
        },

        // Helper function to format schedule time (handles various formats)
        formatScheduleTime(time) {
          if (!time) return '-';
          
          // If time is a datetime string (ISO format), extract just the time part
          if (time.includes('T') && time.includes('Z')) {
            try {
              const dateObj = new Date(time);
              return dateObj.toLocaleTimeString('en-GB', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
              });
            } catch (e) {
              console.warn('Failed to parse datetime:', time);
              return '-';
            }
          }
          
          // If time has seconds (HH:MM:SS), remove them
          if (time.match(/^\d{2}:\d{2}:\d{2}$/)) {
            return time.substring(0, 5);
          }
          
          // If already HH:MM format, return as is
          if (time.match(/^\d{2}:\d{2}$/)) {
            return time;
          }
          
          return time || '-';
        },

        async openEdit(id, employeeId = null, date = null) {
          try {
            // Ensure employees are loaded first
            if (this.employees.length === 0 && this.selectedOutlets.length > 0) {
              console.log('🔄 Loading employees for edit modal...');
              await this.fetchEmployees();
            }

            const response = await fetch(`{{ route('sdm.attendance.show', '') }}/${id}`);
            const data = await response.json();
            
            // Helper function to format time (remove seconds if present)
            const formatTime = (time) => {
              if (!time) return '';
              // If time has seconds (HH:MM:SS), remove them
              return time.substring(0, 5);
            };
            
            // Format date properly for date input (YYYY-MM-DD)
            let formattedDate = data.date;
            if (formattedDate && !formattedDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
              // If date is not in YYYY-MM-DD format, try to parse and format it
              try {
                const dateObj = new Date(formattedDate);
                if (!isNaN(dateObj.getTime())) {
                  formattedDate = dateObj.toISOString().split('T')[0];
                }
              } catch (e) {
                formattedDate = this.filterDate;
              }
            }
            
            this.form = {
              id: data.id,
              employee_id: data.employee_id,
              date: formattedDate,
              clock_in: formatTime(data.clock_in),
              clock_out: formatTime(data.clock_out),
              break_out: formatTime(data.break_out),
              break_in: formatTime(data.break_in),
              overtime_in: formatTime(data.overtime_in),
              overtime_out: formatTime(data.overtime_out),
              status: data.status,
              notes: data.notes || ''
            };
            this.errors = {};
            this.showForm = true;
            
            console.log('✅ Edit modal opened with data:', this.form);
          } catch (error) {
            console.error('Error fetching attendance:', error);
            this.showToastMessage('Gagal memuat data absensi', 'error');
          }
        },

        async openCreateWithEmployee(employeeId, date) {
          // Ensure employees are loaded first
          if (this.employees.length === 0 && this.selectedOutlets.length > 0) {
            console.log('🔄 Loading employees for create modal...');
            await this.fetchEmployees();
          }
          
          // Format date properly for date input (YYYY-MM-DD)
          let formattedDate = date || this.filterDate;
          if (formattedDate && !formattedDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
            // If date is not in YYYY-MM-DD format, try to parse and format it
            try {
              const dateObj = new Date(formattedDate);
              if (!isNaN(dateObj.getTime())) {
                formattedDate = dateObj.toISOString().split('T')[0];
              }
            } catch (e) {
              formattedDate = this.filterDate;
            }
          }
          
          this.form = {
            id: null,
            employee_id: employeeId || '',
            date: formattedDate,
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
          
          console.log('✅ Create modal opened with pre-filled employee:', employeeId, 'and formatted date:', formattedDate);
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

        // Time Settings Functions
        async openTimeSettings() {
          this.showTimeSettingsModal = true;
          await this.loadTimeSettings();
          // Setelah data dimuat dan x-if sudah render, init flatpickr
          await this.$nextTick();
          setTimeout(() => initRfidFlatpickrs(this), 50);
        },

        async loadTimeSettings() {
          this.loadingTimeSettings = true;
          try {
            const response = await fetch('{{ route("sdm.attendance.time.settings") }}');
            const result = await response.json();

            if (response.ok) {
              // Map only check_in and check_out from server into the two simplified objects
              this.timeSettings = result.settings; // keep raw for save
              const checkIn  = result.settings.find(s => s.name === 'check_in');
              const checkOut = result.settings.find(s => s.name === 'check_out');
              if (checkIn) {
                this.rfidCheckIn.start = checkIn.start_time.substring(0, 5);
                this.rfidCheckIn.end   = checkIn.end_time.substring(0, 5);
              }
              if (checkOut) {
                this.rfidCheckOut.start = checkOut.start_time.substring(0, 5);
                this.rfidCheckOut.end   = checkOut.end_time.substring(0, 5);
              }
            } else {
              this.showToastMessage(result.message || 'Gagal memuat pengaturan waktu', 'error');
            }
          } catch (error) {
            console.error('Error loading time settings:', error);
            this.showToastMessage('Gagal memuat pengaturan waktu', 'error');
          } finally {
            this.loadingTimeSettings = false;
          }
        },

        async saveTimeSettings() {
          this.savingTimeSettings = true;
          try {
            // Validate format
            const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!timeRegex.test(this.rfidCheckIn.start) || !timeRegex.test(this.rfidCheckIn.end)) {
              throw new Error('Format jam masuk tidak valid (HH:MM)');
            }
            if (!timeRegex.test(this.rfidCheckOut.start) || !timeRegex.test(this.rfidCheckOut.end)) {
              throw new Error('Format jam pulang tidak valid (HH:MM)');
            }

            // Build payload: only update check_in and check_out rows; keep break/overtime untouched
            const checkInRow  = this.timeSettings.find(s => s.name === 'check_in');
            const checkOutRow = this.timeSettings.find(s => s.name === 'check_out');
            const breakRow    = this.timeSettings.find(s => s.name === 'break');
            const overtimeRow = this.timeSettings.find(s => s.name === 'overtime');

            if (!checkInRow || !checkOutRow) {
              throw new Error('Data pengaturan tidak lengkap, coba muat ulang halaman');
            }

            // Deactivate break and overtime rows — no longer used
            const settings = [
              { id: checkInRow.id,  start_time: this.rfidCheckIn.start,  end_time: this.rfidCheckIn.end,  is_active: true },
              { id: checkOutRow.id, start_time: this.rfidCheckOut.start, end_time: this.rfidCheckOut.end, is_active: true },
            ];
            if (breakRow)    settings.push({ id: breakRow.id,    start_time: breakRow.start_time,    end_time: breakRow.end_time,    is_active: false });
            if (overtimeRow) settings.push({ id: overtimeRow.id, start_time: overtimeRow.start_time, end_time: overtimeRow.end_time, is_active: false });

            const response = await fetch('{{ route("sdm.attendance.time.settings.update") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ settings })
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Pengaturan waktu berhasil disimpan', 'success');
              this.showTimeSettingsModal = false;
            } else {
              console.error('❌ Save failed:', result);
              this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
            }
          } catch (error) {
            console.error('❌ Error saving time settings:', error);
            this.showToastMessage(error.message || 'Gagal menyimpan pengaturan waktu', 'error');
          } finally {
            this.savingTimeSettings = false;
          }
        },

        getTimeSettingTitle(name) {
          const titles = {
            'check_in':  'Jam Masuk',
            'check_out': 'Jam Pulang',
          };
          return titles[name] || name;
        },

        // Function to ensure time format is always 24-hour (HH:MM)
        ensureTimeFormat(input) {
          if (!input || !input.value) return;
          
          let value = input.value;
          console.log('🕐 Original time value:', value);
          
          // If value is in 12-hour format, convert to 24-hour
          if (value.includes('AM') || value.includes('PM')) {
            console.log('🔄 Converting 12-hour to 24-hour format');
            
            // Parse 12-hour format
            const time12h = value.replace(/\s/g, '');
            const [time, period] = time12h.split(/(AM|PM)/i);
            const [hours, minutes] = time.split(':');
            
            let hours24 = parseInt(hours);
            
            if (period.toUpperCase() === 'PM' && hours24 !== 12) {
              hours24 += 12;
            } else if (period.toUpperCase() === 'AM' && hours24 === 12) {
              hours24 = 0;
            }
            
            value = `${hours24.toString().padStart(2, '0')}:${minutes}`;
            console.log('✅ Converted to 24-hour:', value);
          }
          
          // Ensure format is HH:MM or HH:MM:SS (pad single digits)
          if (value.match(/^\d{1,2}:\d{2}$/)) {
            const [hours, minutes] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}`;
          } else if (value.match(/^\d{1,2}:\d{2}:\d{2}$/)) {
            const [hours, minutes, seconds] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}:${seconds}`;
          }
          
          // Validate 24-hour format (with or without seconds)
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/)) {
            input.value = value;
            console.log('✅ Final time value:', value);
            
            // Trigger Alpine.js update
            input.dispatchEvent(new Event('input', { bubbles: true }));
          } else {
            console.warn('⚠️ Invalid time format:', value);
          }
        },

        // Helper function to format time (preserves HH:MM:SS if present, otherwise HH:MM)
        formatTimeToHHMM(timeValue) {
          if (!timeValue) return '';
          
          let value = timeValue.toString().trim();
          console.log('🕐 Formatting time value:', value);
          
          // If value is in 12-hour format, convert to 24-hour
          if (value.includes('AM') || value.includes('PM')) {
            console.log('🔄 Converting 12-hour to 24-hour format');
            
            // Parse 12-hour format
            const time12h = value.replace(/\s/g, '');
            const [time, period] = time12h.split(/(AM|PM)/i);
            const [hours, minutes, seconds] = time.split(':');
            
            let hours24 = parseInt(hours);
            
            if (period.toUpperCase() === 'PM' && hours24 !== 12) {
              hours24 += 12;
            } else if (period.toUpperCase() === 'AM' && hours24 === 12) {
              hours24 = 0;
            }
            
            value = `${hours24.toString().padStart(2, '0')}:${minutes}`;
            if (seconds) {
              value += `:${seconds}`;
            }
            console.log('✅ Converted to 24-hour:', value);
          }
          
          // Ensure format is HH:MM or HH:MM:SS (pad single digits)
          if (value.match(/^\d{1,2}:\d{2}$/)) {
            const [hours, minutes] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}`;
          } else if (value.match(/^\d{1,2}:\d{2}:\d{2}$/)) {
            const [hours, minutes, seconds] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}:${seconds}`;
          }
          
          // Remove seconds if present (HH:MM:SS -> HH:MM) for display
          if (value.match(/^\d{2}:\d{2}:\d{2}$/)) {
            value = value.substring(0, 5);
          }
          
          // Validate 24-hour format
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            console.log('✅ Final formatted time:', value);
            return value;
          } else {
            console.warn('⚠️ Invalid time format, returning original:', value);
            return timeValue; // Return original if can't format
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

        exportPdf() {
          if (this.selectedOutlets.length === 0) {
            this.showToastMessage('Pilih minimal satu outlet', 'error');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          if (this.currentTab === 'daily') {
            params.append('date', this.filterDate);
            window.open(`{{ route('sdm.attendance.export.daily.pdf') }}?${params}`, '_blank');
          } else {
            params.append('month', this.filterMonth);
            params.append('year', this.filterYear);
            window.open(`{{ route('sdm.attendance.export.monthly.pdf') }}?${params}`, '_blank');
          }
        },
        
        exportExcel() {
          if (this.selectedOutlets.length === 0) {
            this.showToastMessage('Pilih minimal satu outlet', 'error');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('start_date', this.filterDate);
          params.append('end_date', this.filterDate);
          
          window.open(`{{ route('sdm.attendance.export.excel') }}?${params}`, '_blank');
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

        getStatusCode(value) {
          if (!value) return '';
          
          // If value is HTML string, strip all HTML tags
          if (typeof value === 'string' && (value.includes('<') || value.includes('>'))) {
            // Create a temporary div to parse HTML
            const temp = document.createElement('div');
            temp.innerHTML = value;
            // Get text content (this strips all HTML)
            const text = temp.textContent || temp.innerText || '';
            // Return first letter (should be H, T, I, S, A, or P)
            return text.trim().charAt(0).toUpperCase();
          }
          
          // If already just the code, return it
          return value.toString().trim().charAt(0).toUpperCase();
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        showPhotoModal(photoPath, title) {
          if (!photoPath) {
            this.showToastMessage('Foto tidak tersedia', 'error');
            return;
          }
          
          this.photoModalSrc = '{{ asset("storage") }}/' + photoPath;
          this.photoModalTitle = title;
          this.showPhotoModalDialog = true;
        },

        viewLocation(item, type) {
          const isOut = type === 'out';
          const lat  = isOut ? (item.clock_out_latitude  || item.latitude)  : item.latitude;
          const lng  = isOut ? (item.clock_out_longitude || item.longitude) : item.longitude;
          const addr = isOut ? (item.clock_out_address   || item.location_address) : item.location_address;

          if (!lat || !lng) {
            this.showToastMessage('Koordinat GPS tidak tersedia', 'error');
            return;
          }

          this.locationData = {
            employee_name: item.employee_name,
            date:          item.date || this.filterDate,
            clock_label:   isOut ? 'Keluar/Pulang' : 'Masuk',
            clock_in:      isOut ? (item.clock_out || '-') : (item.clock_in || '-'),
            latitude:      lat,
            longitude:     lng,
            location_address: addr || null,
            device_info:   item.device_info || null,
          };
          this.showLocationModal = true;
        },

        closePhotoModal() {
          this.showPhotoModalDialog = false;
          this.photoModalSrc = '';
          this.photoModalTitle = '';
        },

        handlePhotoError(event) {
          event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f1f5f9" width="200" height="200"/%3E%3Ctext fill="%2394a3b8" font-family="sans-serif" font-size="14" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3EFoto tidak tersedia%3C/text%3E%3C/svg%3E';
        }
      };
    }
    
    // AGGRESSIVE 24-HOUR FORMAT ENFORCEMENT
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🕐 Enforcing 24-hour format for time inputs...');
      
      // Function to force 24-hour format
      function enforce24HourFormat() {
        const timeInputs = document.querySelectorAll('input[type="time"]');
        console.log(`Found ${timeInputs.length} time inputs to process`);
        
        timeInputs.forEach((input, index) => {
          console.log(`Processing time input ${index + 1}:`, input);
          
          // Force attributes
          input.setAttribute('step', '1');
          input.setAttribute('pattern', '[0-9]{2}:[0-9]{2}');
          input.setAttribute('data-format', '24');
          
          // Remove any existing AM/PM related attributes
          input.removeAttribute('data-12hour');
          input.removeAttribute('data-ampm');
          
          // Force style to hide AM/PM
          input.style.setProperty('-webkit-appearance', 'none', 'important');
          input.style.setProperty('-moz-appearance', 'textfield', 'important');
          input.style.setProperty('appearance', 'none', 'important');
          
          // Add validation
          input.addEventListener('input', function() {
            const value = this.value;
            console.log('Time input changed:', value);
            
            if (value && !value.match(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
              this.setCustomValidity('Format harus HH:MM (24 jam)');
              console.log('Invalid time format:', value);
            } else {
              this.setCustomValidity('');
              console.log('Valid time format:', value);
            }
          });
          
          // Force focus behavior
          input.addEventListener('focus', function() {
            console.log('Time input focused, ensuring 24-hour format');
            // Try to force 24-hour mode
            this.setAttribute('data-format', '24');
          });
          
          // Additional enforcement on change
          input.addEventListener('change', function() {
            console.log('Time input changed, value:', this.value);
          });
        });
      }
      
      // Run immediately
      enforce24HourFormat();
      
      // Run again after a short delay to catch dynamically added inputs
      setTimeout(enforce24HourFormat, 500);
      setTimeout(enforce24HourFormat, 1000);
      
      // Watch for new time inputs being added to DOM
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
              if (node.nodeType === 1) { // Element node
                const timeInputs = node.querySelectorAll ? node.querySelectorAll('input[type="time"]') : [];
                if (timeInputs.length > 0) {
                  console.log('New time inputs detected, enforcing 24-hour format');
                  enforce24HourFormat();
                }
              }
            });
          }
        });
      });
      
      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
      
      console.log('✅ 24-hour format enforcement initialized');
    });
  </script>
  
  <style>
    /* FORCE 24-HOUR FORMAT - AGGRESSIVE APPROACH */
    
    /* Hide AM/PM in all WebKit browsers (Chrome, Safari, Edge) */
    input[type="time"]::-webkit-datetime-edit-ampm-field {
      display: none !important;
      visibility: hidden !important;
      width: 0 !important;
      height: 0 !important;
      opacity: 0 !important;
    }
    
    /* Hide AM/PM in Firefox */
    input[type="time"]::-moz-time-picker-ampm {
      display: none !important;
      visibility: hidden !important;
    }
    
    /* Force 24-hour format attributes */
    input[type="time"] {
      -webkit-appearance: none !important;
      -moz-appearance: textfield !important;
      appearance: none !important;
    }
    
    /* Additional WebKit selectors to hide AM/PM */
    input[type="time"]::-webkit-datetime-edit-meridiem-field {
      display: none !important;
      visibility: hidden !important;
    }
    
    input[type="time"]::-webkit-datetime-edit-text {
      display: none !important;
    }
    
    /* Style the visible parts */
    input[type="time"]::-webkit-datetime-edit-hour-field {
      color: #1f2937 !important;
    }
    
    input[type="time"]::-webkit-datetime-edit-minute-field {
      color: #1f2937 !important;
    }
    
    /* Force container to not show AM/PM */
    input[type="time"]::-webkit-datetime-edit {
      padding: 0 !important;
    }
    
    /* Additional browser-specific hiding */
    input[type="time"]::-ms-clear {
      display: none !important;
    }
    
    /* Ensure no AM/PM text appears */
    input[type="time"]::after {
      content: "" !important;
    }
    
    input[type="time"]::before {
      content: "" !important;
    }
  </style>

  {{-- Flatpickr: time-only picker 24 jam untuk modal Pengaturan Waktu RFID --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    // Init 4 flatpickr time pickers setelah modal terbuka dan data sudah dimuat
    // Dipanggil dari openTimeSettings() Alpine setelah loadTimeSettings() selesai
    function initRfidFlatpickrs(alpineData) {
      const fields = [
        { id: 'rfid_ci_start', get: () => alpineData.rfidCheckIn.start,  set: v => alpineData.rfidCheckIn.start  = v },
        { id: 'rfid_ci_end',   get: () => alpineData.rfidCheckIn.end,    set: v => alpineData.rfidCheckIn.end    = v },
        { id: 'rfid_co_start', get: () => alpineData.rfidCheckOut.start, set: v => alpineData.rfidCheckOut.start = v },
        { id: 'rfid_co_end',   get: () => alpineData.rfidCheckOut.end,   set: v => alpineData.rfidCheckOut.end   = v },
      ];

      fields.forEach(f => {
        const el = document.getElementById(f.id);
        if (!el) return;

        // Destroy dulu jika sudah pernah di-init (modal dibuka ulang)
        if (el._flatpickr) {
          el._flatpickr.destroy();
        }

        flatpickr(el, {
          enableTime:   true,
          noCalendar:   true,
          dateFormat:   'H:i',
          time_24hr:    true,
          allowInput:   false,
          defaultDate:  f.get() || undefined,
          onChange(selectedDates, dateStr) {
            f.set(dateStr); // sync ke Alpine state
          }
        });
      });
    }
  </script>
</x-layouts.admin>
