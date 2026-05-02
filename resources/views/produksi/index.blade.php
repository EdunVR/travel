<style>
<style>
/* Stats Cards Layout - Fixed 4 columns in 1 row */
#dashboard-stats {
    margin: 0 -8px 24px -8px;
}

#dashboard-stats .col-xl-3 {
    padding: 0 8px;
}

.card-dashboard {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 90px; /* More compact height */
    margin-bottom: 0;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); /* Single blue pastel color */
    border-left: 4px solid #2196f3;
}

.card-dashboard:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.15);
}

.card-dashboard .card-body {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    height: 100%;
}

.card-dashboard .flex-grow-1 {
    flex: 1;
    min-width: 0;
}

/* Title with highlight background */
.card-title-highlight {
    background: rgba(255, 255, 255, 0.7);
    padding: 4px 8px;
    border-radius: 6px;
    margin-bottom: 6px;
    display: inline-block;
}

.card-dashboard .card-title {
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 0;
    color: #1565c0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.card-dashboard h3 {
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 2px;
    line-height: 1.2;
    color: #1976d2;
}

.card-dashboard h5 {
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 2px;
    line-height: 1.2;
    color: #1976d2;
}

.card-dashboard small {
    font-size: 0.65rem;
    color: #546e7a;
    display: block;
    line-height: 1.2;
    font-weight: 500;
}

.icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 10px;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(33, 150, 243, 0.2);
}

.icon-wrapper i {
    width: 18px;
    height: 18px;
    color: #1976d2;
}

/* Main Dashboard Grid - Equal height */
.dashboard-grid {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -12px;
}

.dashboard-grid .col-lg-8,
.dashboard-grid .col-lg-4 {
    padding: 0 12px;
    display: flex;
    flex-direction: column;
}

/* Chart Card */
.card-chart {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    height: 100%;
    min-height: 350px;
}

.card-chart .card-body {
    padding: 20px;
    flex: 1;
}

/* Right Side Cards - Equal height with chart */
.right-side-cards {
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 16px;
}

.right-side-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    flex: 1;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 4px solid #2196f3;
}

.right-side-card .card-body {
    padding: 16px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Quick Actions */
.quick-actions .btn {
    margin-bottom: 8px;
    padding: 10px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(33, 150, 243, 0.2);
    color: #1976d2;
    transition: all 0.3s ease;
}

.quick-actions .btn:hover {
    background: #2196f3;
    color: white;
    transform: translateY(-1px);
}

/* System Status */
.system-status .status-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(33, 150, 243, 0.2);
}

.system-status .status-item:last-child {
    border-bottom: none;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 280px;
    width: 100%;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.5s ease forwards;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .card-dashboard {
        height: 85px;
    }
    
    .card-dashboard .card-body {
        padding: 10px 14px;
    }
    
    .card-dashboard h3 {
        font-size: 1.3rem;
    }
    
    .icon-wrapper {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 768px) {
    #dashboard-stats .col-xl-3 {
        padding: 0 6px;
        margin-bottom: 12px;
    }
    
    .card-dashboard {
        height: 80px;
    }
    
    .card-dashboard .card-body {
        padding: 8px 12px;
    }
    
    .card-dashboard h3 {
        font-size: 1.2rem;
    }
    
    .card-dashboard .card-title {
        font-size: 0.75rem;
    }
    
    .icon-wrapper {
        width: 36px;
        height: 36px;
        margin-left: 8px;
    }
}

/* Ensure proper grid layout for stats */
.row.stats-row {
    margin-right: -8px;
    margin-left: -8px;
    display: flex;
    flex-wrap: nowrap;
}

.row.stats-row > [class*="col-"] {
    padding-right: 8px;
    padding-left: 8px;
    flex: 1;
}

/* Text truncation */
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Remove performance card styles */
.performance-icon,
.performance-stats {
    display: none;
}
</style>

@extends('app')

@section('title')
Data Produksi
@endsection

@section('breadcrumb')
@parent
<li class="active">Data Produksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Stats Cards - 1 row, 4 columns -->
<div class="row stats-row mb-4" id="dashboard-stats">
    <div class="col-xl-3 col-md-6">
        <div class="card card-dashboard animate-fade-in">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <div class="card-title-highlight">
                            <h6 class="card-title mb-0">PRODUKSI HARI INI</h6>
                        </div>
                        <h2 class="mb-0" id="stat-hari-ini">0</h2>
                        <small class="text-muted">
                            <span>transaksi</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-dashboard animate-fade-in" style="animation-delay: 0.1s">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <div class="card-title-highlight">
                            <h6 class="card-title mb-0">PRODUKSI BULAN INI</h6>
                        </div>
                        <h3 class="mb-0" id="stat-bulan-ini">0</h3>
                        <small class="text-muted" id="stat-unit-bulan">0 unit</small>
                    </div>
                    <div class="icon-wrapper">
                        <i data-feather="calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-dashboard animate-fade-in" style="animation-delay: 0.2s">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <div class="card-title-highlight">
                            <h6 class="card-title mb-0">RATA-RATA HPP</h6>
                        </div>
                        <h3 class="mb-0" id="stat-hpp">Rp 0</h3>
                        <small class="text-muted" id="stat-total-biaya">Total: Rp 0</small>
                    </div>
                    <div class="icon-wrapper">
                        <i data-feather="dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-dashboard animate-fade-in" style="animation-delay: 0.3s">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <div class="card-title-highlight">
                            <h6 class="card-title mb-0">PRODUK TERPOPULER</h6>
                        </div>
                        <h5 class="mb-0 text-truncate" id="stat-produk-terbanyak">-</h5>
                        <small class="text-muted text-truncate" id="stat-bahan-terbanyak">Bahan: -</small>
                    </div>
                    <div class="icon-wrapper">
                        <i data-feather="award"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="row dashboard-grid mb-4">
    <!-- Left Column - Chart -->
    <div class="col-lg-8">
        <div class="card card-chart">
            <div class="card-header bg-pastel-primary d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 font-weight-bold text-dark">
                    <i data-feather="bar-chart-2" class="icon-sm mr-2"></i>Trend Produksi 7 Hari Terakhir
                </h6>
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-color bg-primary"></span>
                        <small class="text-muted">Unit Diproduksi</small>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Info Cards -->
    <div class="col-lg-4">
        <div class="right-side-cards">
            <!-- Quick Actions -->
            <div class="card right-side-card">
                <div class="card-body">
                    <h6 class="card-title font-weight-bold mb-3" style="color: #1565c0;">
                        <i data-feather="clock" class="icon-sm mr-2"></i>Aksi Cepat
                    </h6>
                    <div class="quick-actions">
                        <button class="btn btn-block mb-2 btn-hover-grow" onclick="addForm()">
                            <i data-feather="plus" class="icon-xs mr-2"></i>Tambah Produksi
                        </button>
                        <button class="btn btn-block mb-2 btn-hover-grow" onclick="showLaporanForm()">
                            <i data-feather="printer" class="icon-xs mr-2"></i>Cetak Laporan
                        </button>
                        <button class="btn btn-block btn-hover-grow" onclick="refreshDashboard()">
                            <i data-feather="refresh-cw" class="icon-xs mr-2"></i>Refresh Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card right-side-card">
                <div class="card-body">
                    <h6 class="card-title font-weight-bold mb-3" style="color: #1565c0;">
                        <i data-feather="server" class="icon-sm mr-2"></i>Status Sistem
                    </h6>
                    <div class="system-status">
                        <div class="status-item d-flex align-items-center mb-3">
                            <div class="status-indicator bg-success"></div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Database</small>
                                <span class="font-weight-bold" style="color: #1976d2;">Online</span>
                            </div>
                        </div>
                        <div class="status-item d-flex align-items-center mb-3">
                            <div class="status-indicator bg-success"></div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Aplikasi</small>
                                <span class="font-weight-bold" style="color: #1976d2;">Aktif</span>
                            </div>
                        </div>
                        <div class="status-item d-flex align-items-center">
                            <div class="status-indicator bg-warning"></div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Update Terakhir</small>
                                <span class="font-weight-bold" style="color: #1976d2;" id="last-update">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>



        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label text-gradient">
                        <i data-feather="package" class="icon-md mr-2"></i>
                        Data Produksi
                    </h3>
                </div>
                <div class="card-toolbar">
                    @if($outlets->count() > 1)
                    <div class="input-group input-group-sm mr-3" style="width: 200px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0">
                                <i data-feather="map-pin" class="icon-xs"></i>
                            </span>
                        </div>
                        <select name="id_outlet" id="id_outlet" class="form-control form-control-sm border-0 bg-light">
                            <option value="">Semua Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}" {{ $id_outlet == $outlet->id_outlet ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <button onclick="addForm()" class="btn btn-success btn-sm btn-pastel-success btn-hover-grow">
                        <i data-feather="plus" class="icon-xs mr-1"></i> Produksi Baru
                    </button>
                    <button onclick="showLaporanForm()" class="btn btn-pastel-info btn-sm mr-2 btn-hover-glow">
                        <i data-feather="printer" class="icon-xs mr-1"></i> Cetak Laporan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-produksi" style="width: 100%">
                        <thead class="bg-gradient-pastel">
                            <tr>
                                <th width="3%" class="text-center">No</th>
                                <th class="text-center">
                                    <i data-feather="calendar" class="icon-xs mr-1"></i>Tanggal
                                </th>
                                <th class="text-center">
                                    <i data-feather="map-pin" class="icon-xs mr-1"></i>Outlet
                                </th>
                                <th>
                                    <i data-feather="package" class="icon-xs mr-1"></i>Produk
                                </th>
                                <th class="text-center">
                                    <i data-feather="hash" class="icon-xs mr-1"></i>Jumlah
                                </th>
                                <th class="text-center">
                                    <i data-feather="dollar-sign" class="icon-xs mr-1"></i>HPP/Unit
                                </th>
                                <th width="10%" class="text-center">
                                    <i data-feather="settings" class="icon-xs mr-1"></i>Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('produksi.create')
@includeIf('produksi.detail-modal')
@includeIf('produksi.laporan-form')
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let table;
    let trendChart;

    $(function () {
        // Initialize Feather Icons
        feather.replace();
        
        // Load initial dashboard data
        updateDashboardData();

        // Initialize DataTable
        table = $('.table-produksi').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('produksi.data') }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    className: 'text-center'
                },
                {
                    data: 'tanggal',
                    className: 'text-center'
                },
                { 
                    data: 'nama_outlet',
                    className: 'text-center'
                },
                {
                    data: 'produk'
                },
                {
                    data: 'jumlah',
                    className: 'text-center'
                },
                {
                    data: 'hpp_unit',
                    className: 'text-right font-weight-bold text-success'
                },
                {
                    data: 'aksi',
                    searchable: false,
                    sortable: false,
                    className: 'text-center'
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            },
            createdRow: function(row, data, dataIndex) {
                $(row).addClass('fade-in-row');
                $(row).css('animation-delay', (dataIndex * 0.1) + 's');
            }
        });

        // Event when outlet changes
        $('#id_outlet').on('change', function () {
            table.ajax.reload();
            updateDashboardData();
            
            var id_outlet = $(this).val();
            $.ajax({
                url: '{{ route('produksi.index') }}',
                type: 'GET',
                data: {
                    id_outlet: id_outlet
                },
                success: function(response) {
                    $('#modal-produk').html($(response).find('#modal-produk').html());
                    feather.replace();
                    initializeProduksiModal();
                },
                error: function(xhr, status, error) {
                    console.log("Gagal memuat data:", error);
                }
            });
        });
    });

    function updateDashboardData() {
        const outletId = $('#id_outlet').val();
        
        $.ajax({
            url: '{{ route("produksi.getDashboardData") }}',
            type: 'GET',
            data: {
                id_outlet: outletId
            },
            success: function(response) {
                // Update stats
                $('#stat-hari-ini').text(response.produksi_hari_ini);
                $('#stat-bulan-ini').text(response.produksi_bulan_ini);
                $('#stat-unit-bulan').text(response.total_unit_bulan_ini + ' unit diproduksi');
                $('#stat-hpp').text('Rp ' + response.rata_hpp_bulan_ini.toLocaleString());
                $('#stat-total-biaya').text('Total biaya: Rp ' + response.total_hpp_bulan_ini.toLocaleString());
                
                // Update produk terbanyak
                if (response.produk_terbanyak && response.produk_terbanyak.produk) {
                    $('#stat-produk-terbanyak').text(response.produk_terbanyak.produk.nama_produk);
                } else {
                    $('#stat-produk-terbanyak').text('-');
                }
                
                // Update bahan terbanyak - dengan pengecekan yang lebih aman
                if (response.bahan_terbanyak && response.bahan_terbanyak.bahan) {
                    $('#stat-bahan-terbanyak').text('Bahan terbanyak: ' + response.bahan_terbanyak.bahan.nama_bahan);
                } else {
                    $('#stat-bahan-terbanyak').text('Bahan terbanyak: -');
                }
                
                // Update trend chart
                updateTrendChart(response.trend_harian);
            },
            error: function(xhr, status, error) {
                console.log("Gagal memuat data dashboard:", error);
                // Fallback values jika error
                $('#stat-hari-ini').text('0');
                $('#stat-bulan-ini').text('0');
                $('#stat-unit-bulan').text('0 unit diproduksi');
                $('#stat-hpp').text('Rp 0');
                $('#stat-total-biaya').text('Total biaya: Rp 0');
                $('#stat-produk-terbanyak').text('-');
                $('#stat-bahan-terbanyak').text('Bahan terbanyak: -');
                
                // Show basic trend chart dengan data kosong
                updateTrendChart([
                    { date: '01 Jan', count: 0 },
                    { date: '02 Jan', count: 0 },
                    { date: '03 Jan', count: 0 },
                    { date: '04 Jan', count: 0 },
                    { date: '05 Jan', count: 0 },
                    { date: '06 Jan', count: 0 },
                    { date: '07 Jan', count: 0 }
                ]);
            }
        });
    }

    function updateTrendChart(trendData) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        // Destroy existing chart if exists
        if (trendChart) {
            trendChart.destroy();
        }
        
        const labels = trendData.map(item => item.date);
        const data = trendData.map(item => item.count);
        
        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Produksi',
                    data: data,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }

    function initializeProduksiModal() {
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Produk'
            });
        }
        feather.replace();
    }

    function addForm() {
        $('#modal-produk').modal('show');
        feather.replace();
    }

    function showDetail(url) {
        $('#modal-detail-produksi .modal-body').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="text-muted">Memuat detail produksi...</p>
            </div>
        `);
        $('#modal-detail-produksi').modal('show');
        
        $.get(url)
            .done((response) => {
                $('#modal-detail-produksi .modal-body').html(response.html);
                feather.replace();
            })
            .fail((errors) => {
                console.log(errors);
                $('#modal-detail-produksi .modal-body').html(`
                    <div class="text-center py-5 text-danger">
                        <i data-feather="alert-triangle" class="icon-lg mb-3"></i>
                        <p class="mt-2">Gagal memuat detail data</p>
                    </div>
                `);
                feather.replace();
            });
    }
    
    function deleteData(url) {
        Swal.fire({
            title: 'Hapus Data Produksi?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
            borderRadius: '15px',
            customClass: {
                confirmButton: 'btn-pastel-danger',
                cancelButton: 'btn-pastel-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.ajax.reload();
                        updateDashboardData(); // Refresh dashboard data
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data produksi berhasil dihapus.',
                            icon: 'success',
                            background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
                            borderRadius: '15px',
                            customClass: {
                                confirmButton: 'btn-pastel-success'
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Tidak dapat menghapus data.',
                            icon: 'error',
                            background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
                            borderRadius: '15px',
                            customClass: {
                                confirmButton: 'btn-pastel-danger'
                            }
                        });
                    }
                });
            }
        });
    }

    // Auto-refresh dashboard every 2 minutes
    setInterval(updateDashboardData, 120000);

    // Reinitialize feather icons when modal is shown
    $(document).on('shown.bs.modal', function () {
        feather.replace();
    });
</script>
@endpush
