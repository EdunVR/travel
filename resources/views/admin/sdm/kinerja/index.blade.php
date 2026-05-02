<x-layouts.admin :title="'SDM / Manajemen Kinerja'">
  <div x-data="kinerjaCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Manajemen Kinerja</h1>
        <p class="text-slate-600 text-sm">Kelola penilaian kinerja karyawan</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('hrm.kinerja.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Penilaian
        </button>
        @endhasPermission
        <button x-on:click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-600 text-sm">Total Penilaian</p>
            <p class="text-2xl font-bold text-slate-800" x-text="stats.total">0</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-xl">
            <i class='bx bx-clipboard text-blue-600 text-2xl'></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-600 text-sm">Rata-rata Skor</p>
            <p class="text-2xl font-bold text-slate-800" x-text="stats.average_score">0</p>
          </div>
          <div class="bg-green-100 p-3 rounded-xl">
            <i class='bx bx-line-chart text-green-600 text-2xl'></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-600 text-sm">Grade A</p>
            <p class="text-2xl font-bold text-slate-800" x-text="stats.grade_a">0</p>
          </div>
          <div class="bg-yellow-100 p-3 rounded-xl">
            <i class='bx bx-star text-yellow-600 text-2xl'></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-slate-600 text-sm">Grade B</p>
            <p class="text-2xl font-bold text-slate-800" x-text="stats.grade_b">0</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-xl">
            <i class='bx bx-award text-purple-600 text-2xl'></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama karyawan…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        
        <!-- Filter Periode -->
        <div class="lg:col-span-3">
          <input type="month" x-model="periodFilter" x-on:change="fetchData()" 
                 class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
        </div>

        <!-- Filter Karyawan -->
        <div class="lg:col-span-3">
          <select x-model="employeeFilter" x-on:change="fetchData()" 
                  class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">Karyawan: Semua</option>
            <template x-for="emp in employees" :key="emp.id">
              <option :value="emp.id" x-text="emp.name"></option>
            </template>
          </select>
        </div>

        <!-- Filter Status -->
        <div class="lg:col-span-2">
          <select x-model="statusFilter" x-on:change="fetchData()" 
                  class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="all">Status: Semua</option>
            <option value="draft">Draft</option>
            <option value="final">Final</option>
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

    <!-- Table -->
    <div x-show="!loading" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Karyawan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Jabatan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Periode</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Tanggal</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Skor</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Grade</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Evaluator</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <template x-for="(item, index) in appraisals" :key="item.id">
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-sm text-slate-600" x-text="index + 1"></td>
                <td class="px-4 py-3 text-sm font-medium text-slate-800" x-text="item.employee_name"></td>
                <td class="px-4 py-3 text-sm text-slate-600" x-text="item.employee_position"></td>
                <td class="px-4 py-3 text-sm text-slate-600" x-text="item.period"></td>
                <td class="px-4 py-3 text-sm text-slate-600" x-text="item.appraisal_date"></td>
                <td class="px-4 py-3 text-sm font-semibold text-slate-800" x-text="item.average_score"></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-800': item.grade === 'A',
                          'bg-blue-100 text-blue-800': item.grade === 'B',
                          'bg-yellow-100 text-yellow-800': item.grade === 'C',
                          'bg-orange-100 text-orange-800': item.grade === 'D',
                          'bg-red-100 text-red-800': item.grade === 'E'
                        }"
                        x-text="item.grade + ' - ' + item.grade_label">
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600" x-text="item.evaluator_name"></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="item.status === 'final' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                        x-text="item.status_label">
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1">
                    <button x-on:click="viewDetail(item.id)" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100" title="Lihat Detail">
                      <i class='bx bx-show text-lg'></i>
                    </button>
                    <template x-if="item.status === 'draft'">
                      <button x-on:click="openEdit(item.id)" class="p-1.5 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100" title="Edit">
                        <i class='bx bx-edit text-lg'></i>
                      </button>
                    </template>
                    <template x-if="item.status === 'draft'">
                      <button x-on:click="deleteItem(item.id)" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="Hapus">
                        <i class='bx bx-trash text-lg'></i>
                      </button>
                    </template>
                    <button x-on:click="viewPdf(item.id)" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="Lihat PDF">
                      <i class='bx bxs-file-pdf text-lg'></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="appraisals.length === 0">
              <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                <i class='bx bx-info-circle text-3xl mb-2'></i>
                <p>Tidak ada data penilaian kinerja</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    <!-- Modal Detail -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-slate-500/75 p-4 overflow-y-auto" style="display: none;" x-on:click.self="closeDetailModal()">
        <!-- Modal Panel -->
        <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-4xl my-4 text-left transition-all transform bg-white shadow-xl rounded-2xl">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Detail Penilaian Kinerja</h3>
            <button x-on:click="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <!-- Body -->
          <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
            <template x-if="detailData">
              <div class="space-y-4">
                <!-- Info Karyawan -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl">
                  <div>
                    <p class="text-xs text-slate-500">Karyawan</p>
                    <p class="font-semibold text-slate-800" x-text="detailData.employee_name"></p>
                  </div>
                  <div>
                    <p class="text-xs text-slate-500">Jabatan</p>
                    <p class="font-semibold text-slate-800" x-text="detailData.employee_position"></p>
                  </div>
                  <div>
                    <p class="text-xs text-slate-500">Periode</p>
                    <p class="font-semibold text-slate-800" x-text="detailData.period"></p>
                  </div>
                  <div>
                    <p class="text-xs text-slate-500">Tanggal Penilaian</p>
                    <p class="font-semibold text-slate-800" x-text="detailData.appraisal_date"></p>
                  </div>
                </div>

                <!-- Parameter Penilaian -->
                <div>
                  <h4 class="font-semibold text-slate-700 mb-3">Parameter Penilaian</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                      <span class="text-slate-700">Disiplin</span>
                      <span class="font-bold text-slate-800" x-text="detailData.discipline_score"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                      <span class="text-slate-700">Kerjasama</span>
                      <span class="font-bold text-slate-800" x-text="detailData.teamwork_score"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                      <span class="text-slate-700">Hasil Kerja</span>
                      <span class="font-bold text-slate-800" x-text="detailData.work_result_score"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                      <span class="text-slate-700">Inisiatif</span>
                      <span class="font-bold text-slate-800" x-text="detailData.initiative_score"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                      <span class="text-slate-700">Target KPI</span>
                      <span class="font-bold text-slate-800" x-text="detailData.kpi_score"></span>
                    </div>
                  </div>
                </div>

                <!-- Hasil -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-blue-50 rounded-xl">
                  <div>
                    <p class="text-xs text-blue-600">Rata-rata Skor</p>
                    <p class="text-2xl font-bold text-blue-800" x-text="detailData.average_score"></p>
                  </div>
                  <div>
                    <p class="text-xs text-blue-600">Grade</p>
                    <p class="text-2xl font-bold text-blue-800" x-text="detailData.grade + ' - ' + detailData.grade_label"></p>
                  </div>
                </div>

                <!-- Catatan -->
                <div x-show="detailData.evaluator_notes" class="p-4 bg-slate-50 rounded-xl">
                  <p class="text-xs text-slate-500 mb-1">Catatan Evaluator</p>
                  <p class="text-slate-700" x-text="detailData.evaluator_notes"></p>
                </div>

                <div x-show="detailData.employee_notes" class="p-4 bg-slate-50 rounded-xl">
                  <p class="text-xs text-slate-500 mb-1">Catatan Karyawan</p>
                  <p class="text-slate-700" x-text="detailData.employee_notes"></p>
                </div>

                <div x-show="detailData.improvement_plan" class="p-4 bg-slate-50 rounded-xl">
                  <p class="text-xs text-slate-500 mb-1">Rencana Perbaikan</p>
                  <p class="text-slate-700" x-text="detailData.improvement_plan"></p>
                </div>

                <!-- Evaluator -->
                <div class="p-4 bg-slate-50 rounded-xl">
                  <p class="text-xs text-slate-500">Evaluator</p>
                  <p class="font-semibold text-slate-800" x-text="detailData.evaluator_name"></p>
                </div>
              </div>
            </template>
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-200">
            <button x-on:click="closeDetailModal()" 
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
    </div>

    <!-- Modal PDF Viewer -->
    <div x-show="showPdfModal" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-slate-500/75 p-4 overflow-y-auto" style="display: none;" x-on:click.self="closePdfModal()">
        <!-- Modal Panel -->
        <div x-show="showPdfModal" x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-6xl my-4 text-left transition-all transform bg-white shadow-xl rounded-2xl">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Preview PDF - Penilaian Kinerja</h3>
            <button x-on:click="closePdfModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <!-- Body -->
          <div class="px-6 py-4">
            <iframe :src="pdfUrl" class="w-full h-[70vh] border border-slate-200 rounded-lg"></iframe>
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-200">
            <button x-on:click="closePdfModal()" 
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
              Tutup
            </button>
          </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-slate-500/75 p-4 overflow-y-auto" style="display: none;" x-on:click.self="closeModal()">
        <!-- Modal Panel -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-4xl my-4 text-left transition-all transform bg-white shadow-xl rounded-2xl">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800" x-text="modalTitle"></h3>
            <button x-on:click="closeModal()" class="text-slate-400 hover:text-slate-600">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>

          <!-- Body -->
          <form x-on:submit.prevent="saveData()" class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
            
            <!-- Karyawan & Periode -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                <select x-model="form.recruitment_id" required 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                  <option value="">Pilih Karyawan</option>
                  <template x-for="emp in employees" :key="emp.id">
                    <option :value="emp.id" x-text="emp.name + ' - ' + emp.position"></option>
                  </template>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode <span class="text-red-500">*</span></label>
                <input type="month" x-model="form.period" required 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Penilaian <span class="text-red-500">*</span></label>
                <input type="date" x-model="form.appraisal_date" required 
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select x-model="form.status" required 
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                  <option value="draft">Draft</option>
                  <option value="final">Final</option>
                </select>
              </div>
            </div>

            <!-- Parameter Penilaian -->
            <div class="border-t border-slate-200 pt-4">
              <h4 class="font-semibold text-slate-700 mb-3">Parameter Penilaian (Skala 0-100)</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Disiplin <span class="text-red-500">*</span></label>
                  <input type="number" x-model="form.discipline_score" required min="0" max="100" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Kerjasama <span class="text-red-500">*</span></label>
                  <input type="number" x-model="form.teamwork_score" required min="0" max="100" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Hasil Kerja <span class="text-red-500">*</span></label>
                  <input type="number" x-model="form.work_result_score" required min="0" max="100" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Inisiatif <span class="text-red-500">*</span></label>
                  <input type="number" x-model="form.initiative_score" required min="0" max="100" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-slate-700 mb-1">Target KPI <span class="text-red-500">*</span></label>
                  <input type="number" x-model="form.kpi_score" required min="0" max="100" 
                         class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>
              </div>
            </div>

            <!-- Catatan -->
            <div class="border-t border-slate-200 pt-4 space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Evaluator</label>
                <textarea x-model="form.evaluator_notes" rows="3" 
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Karyawan</label>
                <textarea x-model="form.employee_notes" rows="3" 
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rencana Perbaikan</label>
                <textarea x-model="form.improvement_plan" rows="3" 
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"></textarea>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2 pt-4 border-t border-slate-200">
              <button type="button" x-on:click="closeModal()" 
                      class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
                Batal
              </button>
              <button type="submit" :disabled="saving" 
                      class="px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50">
                <span x-show="!saving">Simpan</span>
                <span x-show="saving" class="inline-flex items-center gap-2">
                  <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                </span>
              </button>
            </div>
          </form>
        </div>
    </div>

  </div>

  @push('scripts')
  <script>
  function kinerjaCrud() {
    return {
      loading: false,
      saving: false,
      showModal: false,
      showDetailModal: false,
      showPdfModal: false,
      modalTitle: 'Tambah Penilaian',
      editId: null,
      detailData: null,
      pdfUrl: '',
      
      // Data
      appraisals: [],
      employees: [],
      stats: {
        total: 0,
        average_score: 0,
        grade_a: 0,
        grade_b: 0
      },
      
      // Filters
      search: '',
      periodFilter: new Date().toISOString().slice(0, 7),
      employeeFilter: '',
      statusFilter: 'all',
      
      // Form
      form: {
        outlet_id: {{ auth()->user()->outlets()->first()->id_outlet ?? 'null' }},
        recruitment_id: '',
        period: new Date().toISOString().slice(0, 7),
        appraisal_date: new Date().toISOString().slice(0, 10),
        discipline_score: 0,
        teamwork_score: 0,
        work_result_score: 0,
        initiative_score: 0,
        kpi_score: 0,
        evaluator_notes: '',
        employee_notes: '',
        improvement_plan: '',
        status: 'draft'
      },

      init() {
        this.loadOutlets();
        this.fetchEmployees();
        this.fetchData();
        this.fetchStatistics();
      },

      async loadOutlets() {
        try {
          const response = await fetch('{{ route("finance.outlets.data") }}');
          const result = await response.json();
          if (result.success) {
            this.outlets = result.data;
            // Set default outlet if form doesn't have one
            if (!this.form.outlet_id && this.outlets.length > 0) {
              this.form.outlet_id = this.outlets[0].id_outlet;
            }
          }
        } catch (error) {
          console.error('Error loading outlets:', error);
        }
      },

      async fetchData() {
        this.loading = true;
        try {
          const params = new URLSearchParams({
            search: this.search,
            period_filter: this.periodFilter,
            employee_filter: this.employeeFilter,
            status_filter: this.statusFilter
          });
          
          const response = await fetch(`{{ route('sdm.kinerja.data') }}?${params}`);
          const data = await response.json();
          
          if (data.success) {
            this.appraisals = data.data;
          }
        } catch (error) {
          console.error('Error:', error);
          this.showNotification('Gagal memuat data', 'error');
        } finally {
          this.loading = false;
        }
      },

      async fetchStatistics() {
        try {
          const params = new URLSearchParams({
            period_filter: this.periodFilter
          });
          
          const response = await fetch(`{{ route('sdm.kinerja.statistics') }}?${params}`);
          const data = await response.json();
          
          if (data.success) {
            this.stats = data.data;
          }
        } catch (error) {
          console.error('Error:', error);
        }
      },

      async fetchEmployees() {
        try {
          const response = await fetch(`{{ route('sdm.kinerja.employees') }}`);
          const data = await response.json();
          
          if (data.success) {
            this.employees = data.data;
          }
        } catch (error) {
          console.error('Error:', error);
        }
      },

      openCreate() {
        this.modalTitle = 'Tambah Penilaian Kinerja';
        this.editId = null;
        this.resetForm();
        this.showModal = true;
      },

      async openEdit(id) {
        this.modalTitle = 'Edit Penilaian Kinerja';
        this.editId = id;
        
        try {
          const response = await fetch(`{{ route('sdm.kinerja.index') }}/${id}`);
          const data = await response.json();
          
          if (data.success) {
            const item = data.data;
            this.form = {
              outlet_id: item.outlet_id,
              recruitment_id: item.recruitment_id,
              period: item.period,
              appraisal_date: item.appraisal_date,
              discipline_score: item.discipline_score,
              teamwork_score: item.teamwork_score,
              work_result_score: item.work_result_score,
              initiative_score: item.initiative_score,
              kpi_score: item.kpi_score,
              evaluator_notes: item.evaluator_notes || '',
              employee_notes: item.employee_notes || '',
              improvement_plan: item.improvement_plan || '',
              status: item.status
            };
            this.showModal = true;
          }
        } catch (error) {
          console.error('Error:', error);
          this.showNotification('Gagal memuat data', 'error');
        }
      },

      async saveData() {
        this.saving = true;
        
        try {
          const url = this.editId 
            ? `{{ route('sdm.kinerja.index') }}/${this.editId}`
            : `{{ route('sdm.kinerja.store') }}`;
          
          const method = this.editId ? 'PUT' : 'POST';
          
          const response = await fetch(url, {
            method: method,
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.form)
          });
          
          const data = await response.json();
          
          if (data.success) {
            this.showNotification(data.message, 'success');
            this.closeModal();
            this.fetchData();
            this.fetchStatistics();
          } else {
            this.showNotification(data.message || 'Terjadi kesalahan', 'error');
          }
        } catch (error) {
          console.error('Error:', error);
          this.showNotification('Terjadi kesalahan saat menyimpan', 'error');
        } finally {
          this.saving = false;
        }
      },

      async deleteItem(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus penilaian ini?')) return;
        
        try {
          const response = await fetch(`{{ route('sdm.kinerja.index') }}/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          });
          
          const data = await response.json();
          
          if (data.success) {
            this.showNotification(data.message, 'success');
            this.fetchData();
            this.fetchStatistics();
          } else {
            this.showNotification(data.message || 'Gagal menghapus data', 'error');
          }
        } catch (error) {
          console.error('Error:', error);
          this.showNotification('Terjadi kesalahan saat menghapus', 'error');
        }
      },

      async viewDetail(id) {
        try {
          const response = await fetch(`{{ route('sdm.kinerja.index') }}/${id}`);
          const data = await response.json();
          
          if (data.success) {
            // Fetch full data with relationships
            const item = data.data;
            
            // Find employee and evaluator names
            const employee = this.employees.find(e => e.id === item.recruitment_id);
            
            this.detailData = {
              ...item,
              employee_name: item.employee_name,
              employee_position: employee ? employee.position : '-',
              appraisal_date: new Date(item.appraisal_date).toLocaleDateString('id-ID'),
              evaluator_name: item.evaluator ? item.evaluator.name : '-',
              grade_label: this.getGradeLabel(item.grade)
            };
            
            this.showDetailModal = true;
          }
        } catch (error) {
          console.error('Error:', error);
          this.showNotification('Gagal memuat detail', 'error');
        }
      },

      viewPdf(id) {
        this.pdfUrl = `{{ route('sdm.kinerja.export.pdf') }}?id=${id}`;
        this.showPdfModal = true;
      },

      exportPdf() {
        const params = new URLSearchParams({
          period_filter: this.periodFilter,
          status_filter: this.statusFilter
        });
        this.pdfUrl = `{{ route('sdm.kinerja.export.pdf') }}?${params}`;
        this.showPdfModal = true;
      },

      closeDetailModal() {
        this.showDetailModal = false;
        this.detailData = null;
      },

      closePdfModal() {
        this.showPdfModal = false;
        this.pdfUrl = '';
      },

      getGradeLabel(grade) {
        const labels = {
          'A': 'Sangat Baik',
          'B': 'Baik',
          'C': 'Cukup',
          'D': 'Kurang',
          'E': 'Sangat Kurang'
        };
        return labels[grade] || '-';
      },

      closeModal() {
        this.showModal = false;
        this.resetForm();
      },

      resetForm() {
        this.form = {
          outlet_id: this.outlets.length > 0 ? this.outlets[0].id_outlet : null,
          recruitment_id: '',
          period: new Date().toISOString().slice(0, 7),
          appraisal_date: new Date().toISOString().slice(0, 10),
          discipline_score: 0,
          teamwork_score: 0,
          work_result_score: 0,
          initiative_score: 0,
          kpi_score: 0,
          evaluator_notes: '',
          employee_notes: '',
          improvement_plan: '',
          status: 'draft'
        };
      },

      showNotification(message, type = 'success') {
        // Simple alert for now, can be replaced with toast notification
        alert(message);
      }
    }
  }
  </script>
  @endpush
</x-layouts.admin>
