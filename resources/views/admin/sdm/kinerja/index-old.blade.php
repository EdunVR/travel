<x-layouts.admin :title="'SDM / Manajemen Kinerja'">
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Kinerja</h1>
            <p class="text-gray-600 mt-1">Kelola penilaian kinerja karyawan</p>
        </div>
        <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Penilaian</span>
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Penilaian</p>
                    <p class="text-2xl font-bold text-gray-800" id="stat-total">0</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Rata-rata Skor</p>
                    <p class="text-2xl font-bold text-gray-800" id="stat-avg">0</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Grade A</p>
                    <p class="text-2xl font-bold text-gray-800" id="stat-grade-a">0</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Grade B</p>
                    <p class="text-2xl font-bold text-gray-800" id="stat-grade-b">0</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-award text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @if(count($outlets) > 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Outlet</label>
                <select id="outlet-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <input type="month" id="period-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ date('Y-m') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                <select id="employee-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Karyawan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="final">Final</option>
                </select>
            </div>

            <div class="flex items-end">
                <button onclick="loadData()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button onclick="exportPdf()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table id="kinerja-table" class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluator</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="form-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800" id="modal-title">Tambah Penilaian Kinerja</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="appraisal-form" class="space-y-4">
            <input type="hidden" id="appraisal-id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(count($outlets) > 1)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                    <select id="outlet-id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Outlet</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="outlet-id" value="{{ $outlets[0]->id_outlet }}">
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan <span class="text-red-500">*</span></label>
                    <select id="employee-id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Karyawan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode <span class="text-red-500">*</span></label>
                    <input type="month" id="period" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ date('Y-m') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Penilaian <span class="text-red-500">*</span></label>
                    <input type="date" id="appraisal-date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-700 mb-3">Parameter Penilaian (Skala 0-100)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disiplin <span class="text-red-500">*</span></label>
                        <input type="number" id="discipline-score" required min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kerjasama <span class="text-red-500">*</span></label>
                        <input type="number" id="teamwork-score" required min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Kerja <span class="text-red-500">*</span></label>
                        <input type="number" id="work-result-score" required min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Inisiatif <span class="text-red-500">*</span></label>
                        <input type="number" id="initiative-score" required min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target KPI <span class="text-red-500">*</span></label>
                        <input type="number" id="kpi-score" required min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="0">
                    </div>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Evaluator</label>
                        <textarea id="evaluator-notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Karyawan</label>
                        <textarea id="employee-notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rencana Perbaikan</label>
                        <textarea id="improvement-plan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select id="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="draft">Draft</option>
                            <option value="final">Final</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let dataTable;
const baseUrl = '{{ route("sdm.kinerja.index") }}';

document.addEventListener('DOMContentLoaded', function() {
    initDataTable();
    loadEmployees();
    loadData();
    loadStatistics();

    // Form submit
    document.getElementById('appraisal-form').addEventListener('submit', handleSubmit);

    // Outlet change
    document.getElementById('outlet-id').addEventListener('change', function() {
        loadEmployees(this.value);
    });
});

function initDataTable() {
    dataTable = $('#kinerja-table').DataTable({
        processing: true,
        serverSide: false,
        data: [],
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'outlet_name' },
            { data: 'employee_name' },
            { data: 'employee_position' },
            { data: 'period' },
            { data: 'appraisal_date' },
            { data: 'average_score' },
            { 
                data: null,
                render: function(data) {
                    return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-${data.grade_color}-100 text-${data.grade_color}-800">${data.grade} - ${data.grade_label}</span>`;
                }
            },
            { data: 'evaluator_name' },
            { 
                data: null,
                render: function(data) {
                    const color = data.status === 'final' ? 'green' : 'yellow';
                    return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-${color}-100 text-${color}-800">${data.status_label}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="flex gap-1">
                            <button onclick="viewDetail(${data.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${data.status === 'draft' ? `
                            <button onclick="editAppraisal(${data.id})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteAppraisal(${data.id})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            ` : ''}
                            <button onclick="exportSinglePdf(${data.id})" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs" title="Export PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
}

function loadData() {
    const outletFilter = document.getElementById('outlet-filter')?.value || 'all';
    const periodFilter = document.getElementById('period-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    const employeeFilter = document.getElementById('employee-filter').value;

    fetch(`{{ route('sdm.kinerja.data') }}?outlet_filter=${outletFilter}&period_filter=${periodFilter}&status_filter=${statusFilter}&employee_filter=${employeeFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                dataTable.clear();
                dataTable.rows.add(data.data);
                dataTable.draw();
            }
        })
        .catch(error => console.error('Error:', error));
}

function loadStatistics() {
    const periodFilter = document.getElementById('period-filter').value;

    fetch(`{{ route('sdm.kinerja.statistics') }}?period_filter=${periodFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('stat-total').textContent = data.data.total;
                document.getElementById('stat-avg').textContent = data.data.average_score;
                document.getElementById('stat-grade-a').textContent = data.data.grade_a;
                document.getElementById('stat-grade-b').textContent = data.data.grade_b;
            }
        })
        .catch(error => console.error('Error:', error));
}

function loadEmployees(outletId = null) {
    const url = outletId 
        ? `{{ route('sdm.kinerja.employees') }}?outlet_id=${outletId}`
        : `{{ route('sdm.kinerja.employees') }}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selects = ['employee-id', 'employee-filter'];
                selects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    const currentValue = select.value;
                    select.innerHTML = selectId === 'employee-filter' 
                        ? '<option value="">Semua Karyawan</option>' 
                        : '<option value="">Pilih Karyawan</option>';
                    
                    data.data.forEach(emp => {
                        const option = new Option(`${emp.name} - ${emp.position}`, emp.id);
                        select.add(option);
                    });
                    
                    if (currentValue) select.value = currentValue;
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

function openAddModal() {
    document.getElementById('modal-title').textContent = 'Tambah Penilaian Kinerja';
    document.getElementById('appraisal-form').reset();
    document.getElementById('appraisal-id').value = '';
    document.getElementById('appraisal-date').value = '{{ date("Y-m-d") }}';
    document.getElementById('period').value = '{{ date("Y-m") }}';
    document.getElementById('form-modal').classList.remove('hidden');
}

function editAppraisal(id) {
    fetch(`{{ route('sdm.kinerja.index') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const appraisal = data.data;
                document.getElementById('modal-title').textContent = 'Edit Penilaian Kinerja';
                document.getElementById('appraisal-id').value = appraisal.id;
                document.getElementById('outlet-id').value = appraisal.outlet_id;
                
                loadEmployees(appraisal.outlet_id);
                setTimeout(() => {
                    document.getElementById('employee-id').value = appraisal.recruitment_id;
                }, 300);
                
                document.getElementById('period').value = appraisal.period;
                document.getElementById('appraisal-date').value = appraisal.appraisal_date;
                document.getElementById('discipline-score').value = appraisal.discipline_score;
                document.getElementById('teamwork-score').value = appraisal.teamwork_score;
                document.getElementById('work-result-score').value = appraisal.work_result_score;
                document.getElementById('initiative-score').value = appraisal.initiative_score;
                document.getElementById('kpi-score').value = appraisal.kpi_score;
                document.getElementById('evaluator-notes').value = appraisal.evaluator_notes || '';
                document.getElementById('employee-notes').value = appraisal.employee_notes || '';
                document.getElementById('improvement-plan').value = appraisal.improvement_plan || '';
                document.getElementById('status').value = appraisal.status;
                
                document.getElementById('form-modal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data');
        });
}

function viewDetail(id) {
    exportSinglePdf(id);
}

function closeModal() {
    document.getElementById('form-modal').classList.add('hidden');
}

function handleSubmit(e) {
    e.preventDefault();

    const id = document.getElementById('appraisal-id').value;
    const url = id 
        ? `{{ route('sdm.kinerja.index') }}/${id}`
        : `{{ route('sdm.kinerja.store') }}`;
    const method = id ? 'PUT' : 'POST';

    const formData = {
        outlet_id: document.getElementById('outlet-id').value,
        recruitment_id: document.getElementById('employee-id').value,
        period: document.getElementById('period').value,
        appraisal_date: document.getElementById('appraisal-date').value,
        discipline_score: document.getElementById('discipline-score').value,
        teamwork_score: document.getElementById('teamwork-score').value,
        work_result_score: document.getElementById('work-result-score').value,
        initiative_score: document.getElementById('initiative-score').value,
        kpi_score: document.getElementById('kpi-score').value,
        evaluator_notes: document.getElementById('evaluator-notes').value,
        employee_notes: document.getElementById('employee-notes').value,
        improvement_plan: document.getElementById('improvement-plan').value,
        status: document.getElementById('status').value,
    };

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeModal();
            loadData();
            loadStatistics();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    });
}

function deleteAppraisal(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus penilaian ini?')) return;

    fetch(`{{ route('sdm.kinerja.index') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadData();
            loadStatistics();
        } else {
            alert(data.message || 'Gagal menghapus data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus data');
    });
}

function exportPdf() {
    const outletFilter = document.getElementById('outlet-filter')?.value || 'all';
    const periodFilter = document.getElementById('period-filter').value;
    const statusFilter = document.getElementById('status-filter').value;

    window.open(`{{ route('sdm.kinerja.export.pdf') }}?outlet_filter=${outletFilter}&period_filter=${periodFilter}&status_filter=${statusFilter}`, '_blank');
}

function exportSinglePdf(id) {
    window.open(`{{ route('sdm.kinerja.export.pdf') }}?id=${id}`, '_blank');
}
</script>
@endpush
</x-layouts.admin>
