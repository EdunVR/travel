<style>
    /* Improve table appearance */
    .table {
        font-size: 1.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .table thead th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .status-present {
        color: #28a745;
        font-weight: bold;
    }

    .status-absent {
        color: #dc3545;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .table tbody td {
        vertical-align: middle;
    }
    
    /* Highlight present/absent status */
    .present-cell {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .absent-cell {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    /* Small screens adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table thead th {
            white-space: nowrap;
        }
        
        .form-inline .form-control {
            margin-bottom: 0.5rem;
            width: 100%;
        }
    }
    
    /* Tooltip styling */
    .tooltip-inner {
        max-width: 300px;
        white-space: pre-line;
        text-align: left;
    }

    .fa-spinner {
        color: #4e73df;
    }

    /* Smooth transition for tab content */
    .tab-pane {
        transition: opacity 0.3s ease;
    }

    .nav-tabs {
        display: flex;
        justify-content: center;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs .nav-item {
        margin: 0 5px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem 0.25rem 0 0;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.1);
        border-bottom: 3px solid #4e73df;
    }
    

    .set-schedule-btn {
        opacity: 0.7;
        transition: opacity 0.3s;
    }
    
    .set-schedule-btn:hover {
        opacity: 1;
    }

    .schedule-time {
        font-weight: 500;
        color: #4e73df;
    }

    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }

</style>

@extends('app')

@section('title', 'Manajemen Absensi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi</h6>
            <div>
                <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#setWorkHoursModal">
                    <i data-feather="clock"></i> Set Jam Datang/Pulang
                </button>
                <a href="{{ route('hrm.attendance.create') }}" class="btn btn-primary">
                    <i data-feather="plus"></i> Tambah Absensi
                </a>
            </div>
        </div>
        <div class="card-body">
            

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'daily' ? 'active' : '' }}" id="daily-tab" data-toggle="tab" href="#daily" role="tab">Harian</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'weekly' ? 'active' : '' }}" id="weekly-tab" data-toggle="tab" href="#weekly" role="tab">Mingguan</a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'monthly' ? 'active' : '' }}" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab">Bulanan</a>
                </li>
            </ul>
            
            <div class="tab-content" id="attendanceTabsContent">
                <!-- Daily Tab - Updated -->
                <div class="tab-pane fade" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                    <div class="mt-3">
                    <form id="dailyFilterForm" onsubmit="filterAttendance(event, 'daily')">
                            <input type="hidden" name="tab" value="daily">
                            <div class="form-group">
                                <label for="date" class="mr-2">Pilih Tanggal:</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">Filter</span>
                                <span class="loading-spinner" style="display:none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </form>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-calendar-day"></i> Tanggal: {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dailyTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>ID Fingerprint</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Jadwal Masuk</th>
                                        <th>Jadwal Pulang</th>
                                        <th>Status</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Total Jam Kerja</th>
                                        <th>Terlambat (m)</th>
                                        <th>Pulang Cepat (m)</th>
                                        <th>Lembur (m)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyAttendances as $attendance)
                                    @php
                                        $workSchedule = $attendance->recruitment->workSchedule;
                                        $scheduleIn = $workSchedule ? $workSchedule->clock_in : '08:00';
                                        $scheduleOut = $workSchedule ? $workSchedule->clock_out : '17:00';

                                        $status = $attendance->clock_in ? '✓' : '-';
                                        $statusClass = $attendance->clock_in ? 'text-success font-weight-bold' : 'text-danger';
                                        $cellClass = $attendance->clock_in ? 'present-cell' : 'absent-cell';
                                        $tooltip = $attendance->clock_in ? 
                                            "Masuk: {$attendance->clock_in}\nPulang: ".($attendance->clock_out ?? '-')."\nTerlambat: ".($attendance->late_minutes ?? 0)."m\nPulang Cepat: ".($attendance->early_minutes ?? 0)."m\nLembur: ".($attendance->overtime_minutes ?? 0)."m" : 
                                            'Tidak hadir';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $attendance->recruitment->fingerprint_id }}</td>
                                        <td>{{ $attendance->recruitment->name }}</td>
                                        <td>{{ $attendance->recruitment->position ?? '-' }}</td>
                                        <td>{{ $scheduleIn }}</td>
                                        <td>{{ $scheduleOut }}</td>
                                        <td class="text-center {{ $cellClass }}" title="{{ $tooltip }}" style="cursor: help;">
                                            <span class="{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td>{{ $attendance->clock_in ?? '-' }}</td>
                                        <td>{{ $attendance->clock_out ?? '-' }}</td>
                                        <td>{{ $attendance->hours_worked }} Jam</td>
                                        <td class="text-center">{{ $attendance->late_minutes ?? 0 }}</td>
                                        <td class="text-center">{{ $attendance->early_minutes ?? 0 }}</td>
                                        <td class="text-center">{{ $attendance->overtime_minutes ?? 0 }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('hrm.attendance.edit', $attendance->id) }}" class="btn btn-icon btn-warning mr-1" title="Edit">
                                                    <!-- <i data-feather="edit"></i> -->
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('hrm.attendance.destroy', $attendance->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-danger mr-1" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                        <!-- <i data-feather="trash-2"></i> -->
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                
                <!-- Monthly Tab - Updated -->
                <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                    <div class="mt-3">
                        <form id="monthlyFilterForm" onsubmit="filterAttendance(event, 'monthly')">
                            <input type="hidden" name="tab" value="monthly">
                            <input type="hidden" name="monthly_filter" value="1">
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="monthly_month">Pilih Bulan:</label>
                                        <input type="month" name="monthly_month" id="monthly_month" class="form-control" value="{{ $monthlyMonth }}">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="alert alert-info">
                            <i class="fas fa-calendar-week"></i> Periode: 
                            {{ $monthlyData['startDate']->translatedFormat('d M Y') }} - 
                            {{ $monthlyData['endDate']->translatedFormat('d M Y') }}
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="monthlyTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th rowspan="2" class="align-middle">No</th>
                                        <th rowspan="2" class="align-middle">ID Finger</th>
                                        <th rowspan="2" class="align-middle">Nama</th>
                                        <th rowspan="2" class="align-middle">Jabatan</th>
                                        <th rowspan="2" class="align-middle">Jadwal Masuk</th>
                                        <th rowspan="2" class="align-middle">Jadwal Pulang</th>
                                        @foreach($monthlyData['dates'] as $date)
                                        <th class="text-center" style="min-width: 60px;">
                                            <div class="font-weight-bold">{{ $date->format('d') }}</div>
                                            <div class="small">{{ $dayNames[$date->dayOfWeek] }}</div>
                                        </th>
                                        @endforeach
                                        <th rowspan="2" class="align-middle">Hadir</th>
                                        <th rowspan="2" class="align-middle">Absen</th>
                                        <th rowspan="2" class="align-middle">Terlambat (m)</th>
                                        <th rowspan="2" class="align-middle">Pulang Cepat (m)</th>
                                        <th rowspan="2" class="align-middle">Lembur (m)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyData['employees'] as $employee)
                                    @php
                                        $scheduleIn = $employee['work_schedule']['clock_in'] ?? '08:00';
                                        $scheduleOut = $employee['work_schedule']['clock_out'] ?? '17:00';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $employee['fingerprint_id'] }}</td>
                                        <td>{{ $employee['name'] }}</td>
                                        <td>{{ $employee['position'] ?? '-' }}</td>
                                        <td>{{ $scheduleIn }}</td>
                                        <td>{{ $scheduleOut }}</td>
                                        @foreach($monthlyData['dates'] as $date)
                                        @php
                                            $dateKey = $date->format('Y-m-d');
                                            $attendance = $employee['attendances'][$dateKey] ?? null;
                                            
                                            // Handle both old string format and new array format
                                            if (is_array($attendance)) {
                                                $status = $attendance['status'] ?? '-';
                                                $statusClass = $status === '✓' ? 'text-success font-weight-bold' : 'text-danger';
                                                $cellClass = $status === '✓' ? 'present-cell' : 'absent-cell';
                                                $tooltip = $status === '✓' ? 
                                                    "Masuk: ".($attendance['clock_in'] ?? '-')."\nPulang: ".($attendance['clock_out'] ?? '-')."\nTerlambat: ".($attendance['late'] ?? 0)."m\nPulang Cepat: ".($attendance['early'] ?? 0)."m\nLembur: ".($attendance['overtime'] ?? 0)."m" : 
                                                    'Tidak hadir';
                                            } else {
                                                // Fallback for old string format
                                                $status = $attendance ?? '-';
                                                $statusClass = $status === '✓' ? 'text-success font-weight-bold' : 'text-danger';
                                                $cellClass = $status === '✓' ? 'present-cell' : 'absent-cell';
                                                $tooltip = $status === '✓' ? 'Hadir' : 'Tidak hadir';
                                            }
                                        @endphp
                                        <td class="text-center {{ $cellClass }}" @if(isset($tooltip)) title="{{ $tooltip }}" @endif style="cursor: help;">
                                            <span class="{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        @endforeach
                                        <td class="text-center">{{ $employee['total_present'] }}</td>
                                        <td class="text-center">{{ $employee['total_absent'] }}</td>
                                        <td class="text-center">{{ $employee['total_late'] }}</td>
                                        <td class="text-center">{{ $employee['total_early'] }}</td>
                                        <td class="text-center">{{ $employee['total_overtime'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal utama untuk set jam kerja -->
<div class="modal fade" id="setWorkHoursModal" tabindex="-1" role="dialog" aria-labelledby="setWorkHoursModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="setWorkHoursModalLabel">Atur Jadwal Datang & Pulang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('hrm.attendance.set-work-hours') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id">Karyawan</label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="clock_in">Jadwal Datang</label>
                        <input type="time" name="clock_in" id="clock_in" class="form-control" value="08:00" required>
                    </div>
                    <div class="form-group">
                        <label for="clock_out">Jadwal Pulang</label>
                        <input type="time" name="clock_out" id="clock_out" class="form-control" value="17:00" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="apply_to_all" id="apply_to_all" class="form-check-input" value="1">
                        <label class="form-check-label" for="apply_to_all">Terapkan untuk semua karyawan</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal kecil untuk set jam kerja per karyawan -->
<div class="modal fade" id="employeeScheduleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Jadwal Kerja untuk <span id="modalEmployeeName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('hrm.attendance.set-work-hours') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" id="modalEmployeeId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_clock_in">Jadwal Datang</label>
                        <input type="time" name="clock_in" id="modal_clock_in" class="form-control" value="08:00" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_clock_out">Jadwal Pulang</label>
                        <input type="time" name="clock_out" id="modal_clock_out" class="form-control" value="17:00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('daily-table')
    <!-- Isi tabel harian -->
    <table class="table table-bordered">
        <!-- ... -->
    </table>
@endsection


@section('monthly-table')
    <!-- Isi tabel bulanan -->
    <table class="table table-bordered">
        <!-- ... -->
    </table>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
// Fungsi untuk handle filter
async function filterAttendance(event, tabName) {
    event.preventDefault();
    
    // Dapatkan data form
    const form = event.target;
    const formData = new FormData(form);
    
    // Tampilkan tombol loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    submitBtn.disabled = true;
    
    try {
        // Kirim request AJAX
        const response = await fetch("{{ route('hrm.attendance.index') }}?" + new URLSearchParams(formData), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });
        
        const html = await response.text();
        
        // Parse response dan update konten
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector(`#${tabName}`).innerHTML;
        
        // Update konten dengan efek fade
        const tabContent = document.querySelector(`#${tabName}`);
        tabContent.style.opacity = 0;
        setTimeout(() => {
            tabContent.innerHTML = newContent;
            tabContent.style.opacity = 1;
            
            // Aktifkan tab yang difilter
            $(`.nav-tabs a[href="#${tabName}"]`).tab('show');
            
            // Inisialisasi ulang tooltip
            $('[data-toggle="tooltip"]').tooltip();
        }, 300);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data');
    } finally {
        // Kembalikan tombol ke state semula
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
}

// Handle saat halaman pertama kali load
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    // Set tab aktif dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'daily';
    
    // Aktifkan tab yang sesuai
    $(`.nav-tabs a[href="#${activeTab}"]`).tab('show');
    
    // Jika ada parameter filter di URL, jalankan filter otomatis
    if (urlParams.has('date') || urlParams.has('weekly_year') || urlParams.has('monthly_month')) {
        const formId = `${activeTab}FilterForm`;
        const form = document.getElementById(formId);
        if (form) {
            filterAttendance({ preventDefault: () => {} }, activeTab);
        }
    }
});

$(document).ready(function() {
        // Tangani klik tombol set schedule per karyawan
        $('.set-schedule-btn').click(function() {
            const employeeId = $(this).data('employee-id');
            const employeeName = $(this).data('employee-name');
            
            $('#modalEmployeeId').val(employeeId);
            $('#modalEmployeeName').text(employeeName);
            
            // Ambil data jam kerja yang sudah ada (jika ada)
            $.get(`/api/work-schedule/${employeeId}`, function(data) {
                if (data) {
                    $('#modal_clock_in').val(data.clock_in);
                    $('#modal_clock_out').val(data.clock_out);
                }
            });
            
            $('#employeeScheduleModal').modal('show');
        });
        
        // Validasi form utama
        $('#setWorkHoursModal form').submit(function(e) {
            if (!$('#apply_to_all').is(':checked') && !$('#employee_id').val()) {
                e.preventDefault();
                alert('Pilih karyawan atau centang "Terapkan untuk semua"');
            }
        });
    });
</script>
@endpush
