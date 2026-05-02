<style>
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }

    .custom-file-label::after {
        content: "Browse";
    }
    
    /* Style untuk baris bulan - dipertegas dengan biru muda */
    .month-divider {
        background-color: #e3f2fd !important;
        font-weight: bold;
        color: #0d47a1;
        border-top: 2px solid #bbdefb;
        border-bottom: 2px solid #bbdefb;
    }
    
    /* Style untuk tombol show/hide */
    .toggle-info {
        cursor: pointer;
        color: #007bff;
        font-size: 0.8rem;
    }
    .toggle-info:hover {
        text-decoration: underline;
    }
    .full-info {
        display: none;
    }
    
    /* Style untuk toggle view */
    .view-toggle {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .view-toggle label {
        margin-right: 15px;
        margin-bottom: 0;
        font-weight: normal;
    }
    .view-toggle .form-check {
        display: flex;
        align-items: center;
    }
    .view-toggle .form-check-input {
        margin-right: 5px;
    }
    /* Timeline styles */
.timeline-container {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.timeline-container::-webkit-scrollbar {
    width: 6px;
}

.timeline-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.timeline-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.timeline-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Badge colors */
.badge-secondary { background-color: #6c757d; }
.badge-info { background-color: #17a2b8; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-success { background-color: #28a745; }
.badge-primary { background-color: #007bff; }
.badge-danger { background-color: #dc3545; }
</style>

@extends('app')

@section('title', 'Manajemen Prospek & Lead')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Daftar Prospek & Lead</h6>
                    <div>
                        <a href="{{ route('prospek.create') }}" class="btn btn-sm btn-primary">
                            <i data-feather="plus"></i> Tambah Prospek
                        </a>
                        <div class="btn-group" role="group">
                            <a href="{{ route('prospek.export-template') }}" class="btn btn-sm btn-success mr-2">
                                Download Template
                            </a>
                            <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#importModal">
                                Import Excel
                            </button>
                        </div>
                        <a href="{{ route('prospek.map') }}" class="btn btn-sm btn-info mr-2">
                            Lihat Peta Keseluruhan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Hapus toggle view karena sekarang hanya satu tampilan dengan semua fitur -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="enhancedTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Status</th>
                                    <th>Lokasi</th>
                                    <th>Nama</th>
                                    <th>Perusahaan</th>
                                    <th>Telepon</th>
                                    <th>Jenis</th>
                                    <th>Alamat</th>
                                    <th>Kapasitas</th>
                                    <th>Sistem Produksi</th>
                                    <th>Bahan Bakar</th>
                                    <th>Informasi Perusahaan</th>
                                    <th>Menggunakan Boiler?</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $counters = [];
                                $currentMonthYear = null;
                            @endphp
                            @foreach($prospeks as $prospek)
                            @php
                                // Cek apakah bulan/tahun berbeda untuk menambahkan pembatas
                                $prospekDate = \Carbon\Carbon::parse($prospek->tanggal);
                                $monthYear = $prospekDate->format('F Y');
                                
                                // Generate kode
                                $jenisParts = explode(' ', $prospek->jenis ?? '');
                                $jenisCode = '';
                                foreach ($jenisParts as $part) {
                                    $jenisCode .= strtoupper(substr($part, 0, 1));
                                }
                                
                                $statusCode = strtoupper(substr($prospek->current_status, 0, 1));
                                
                                $key = $jenisCode . '-' . $statusCode;
                                if (!isset($counters[$key])) {
                                    $counters[$key] = 1;
                                } else {
                                    $counters[$key]++;
                                }
                                
                                $kode = $jenisCode . '-' . $statusCode . '-' . str_pad($counters[$key], 3, '0', STR_PAD_LEFT);
                                
                                // Data atribut untuk grouping
                                $monthYearAttr = $prospekDate->format('Y-m');
                            @endphp
                                
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ tanggal_indonesia($prospek->tanggal) }}</td>
                                    <td>{{ $kode }}</td>
                                    <td>
                                        @php
                                            $badgeClass = [
                                                'prospek' => 'badge-prospek',
                                                'followup' => 'badge-followup',
                                                'negosiasi' => 'badge-negosiasi',
                                                'closing' => 'badge-closing',
                                                'deposit' => 'badge-deposit',
                                                'gagal' => 'badge-gagal'
                                            ][$prospek->current_status] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ App\Models\Prospek::STATUSES[$prospek->current_status] ?? $prospek->current_status }}
                                        </span>
                                        <br>
                                        <button class="btn btn-sm btn-outline-primary mt-1 btn-timeline" 
                                                data-id="{{ $prospek->id_prospek }}"
                                                data-toggle="modal" 
                                                data-target="#timelineModal">
                                            <i class="fas fa-history"></i> Timeline & Update
                                        </button>
                                    </td>
                                    <td>
                                        @if($prospek->latitude && $prospek->longitude)
                                            <a href="#" class="btn-map btn btn-sm btn-info" data-lat="{{ $prospek->latitude }}" data-lng="{{ $prospek->longitude }}">
                                                <i class="fas fa-map-marker-alt"></i> Lihat
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $prospek->nama }}</td>
                                    <td>{{ $prospek->nama_perusahaan }}</td>
                                    <td>{{ $prospek->telepon }}</td>
                                    <td>{{ $prospek->jenis ?? '-' }}</td>
                                    <td>{{ Str::limit($prospek->alamat, 30) }}</td>
                                    <td>{{ $prospek->kapasitas_produksi ?? '-' }}</td>
                                    <td>{{ $prospek->sistem_produksi ?? '-' }}</td>
                                    <td>{{ $prospek->bahan_bakar ?? '-' }}</td>
                                    <td>
                                    @if($prospek->informasi_perusahaan)
                                        <div class="short-info">
                                            {{ Str::limit($prospek->informasi_perusahaan, 50) }}
                                            @if(strlen($prospek->informasi_perusahaan) > 50)
                                                <span class="toggle-info">Selengkapnya</span>
                                            @endif
                                        </div>
                                        <div class="full-info">
                                            {{ $prospek->informasi_perusahaan }}
                                            <span class="toggle-info">Sembunyikan</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $prospek->menggunakan_boiler ? 'success' : 'danger' }}">
                                            {{ $prospek->menggunakan_boiler ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td>{{ $prospek->recruitment->name ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('prospek.edit', $prospek->id_prospek) }}" class="btn btn-sm btn-warning mr-2" title="Edit">
                                                <i data-feather="edit"></i>
                                            </a>
                                            <form action="{{ route('prospek.destroy', $prospek->id_prospek) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus prospek ini?')" title="Hapus">
                                                    <i data-feather="trash-2"></i>
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
        </div>
    </div>
</div>

<!-- Modal for Map -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Lokasi Prospek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="prospekMap" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnNavigate" class="btn btn-primary" target="_blank">
                    <i data-feather="navigation"></i> Buka Navigasi
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Prospek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('prospek.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fileInput" name="file" accept=".xlsx, .xls" required>
                            <label class="custom-file-label" for="fileInput">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format file harus sesuai dengan template. <a href="{{ route('prospek.export-template') }}">Download template</a>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Timeline -->
<div class="modal fade" id="timelineModal" tabindex="-1" role="dialog" aria-labelledby="timelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timelineModalLabel">Timeline Prospek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="timelineForm" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    @foreach(App\Models\Prospek::STATUSES as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Timeline</button>
                        </form>

                        <hr>

                        <h6>Riwayat Timeline</h6>
                        <div class="timeline-container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Timeline items will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&libraries=places"></script>
<script>
    feather.replace();

    const baseUrl = window.baseUrl;
    
    // Handle timeline button click
    $(document).on('click', '.btn-timeline', function() {
        var prospekId = $(this).data('id');
        var modal = $('#timelineModal');
        
        // Set active button
        $('.btn-timeline').removeClass('active');
        $(this).addClass('active');
        
        // Set form action URL menggunakan route name
        $('#timelineForm').attr('action', `${baseUrl}/crm/prospek/${prospekId}/timeline`);
        
        // Set current datetime as default
        var now = new Date();
        var formattedDateTime = now.toISOString().slice(0, 16);
        $('#tanggal').val(formattedDateTime);
        
        // Load timeline history
        loadTimeline(prospekId);
    });

    function loadTimeline(prospekId) {
        $.get(`${baseUrl}/crm/prospek/${prospekId}/timeline`, function(data) {
            var timelineHtml = '';
            
            data.forEach(function(timeline) {
                var badgeClass = {
                    'prospek': 'secondary',
                    'followup': 'info',
                    'negosiasi': 'warning',
                    'closing': 'success',
                    'deposit': 'primary',
                    'gagal': 'danger'
                }[timeline.status] || 'secondary';
                
                var date = new Date(timeline.tanggal);
                var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + 
                                ('0' + (date.getMonth()+1)).slice(-2) + '/' + 
                                date.getFullYear() + ' ' +
                                ('0' + date.getHours()).slice(-2) + ':' + 
                                ('0' + date.getMinutes()).slice(-2);
                
                timelineHtml += `
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">
                                    <span class="badge badge-${badgeClass}">
                                        ${timeline.status}
                                    </span>
                                </h6>
                                <small>${formattedDate}</small>
                            </div>
                            <p class="mb-1">${timeline.deskripsi || '-'}</p>
                            <form class="delete-timeline-form text-right" data-timeline-id="${timeline.id}">
                                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            });
            
            $('.timeline-container').html(timelineHtml || '<p>Belum ada timeline</p>');
        });
    }

    // Handle form submission
    $('#timelineForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var prospekId = $('.btn-timeline.active').data('id');
        
        $.ajax({
            type: 'POST',
            url: `${baseUrl}/crm/prospek/${prospekId}/timeline`,
            data: form.serialize(),
            success: function(response) {
                // Refresh timeline
                loadTimeline(prospekId);
                
                // Reset form
                form[0].reset();
                
                // Set current datetime as default
                var now = new Date();
                var formattedDateTime = now.toISOString().slice(0, 16);
                $('#tanggal').val(formattedDateTime);
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Timeline berhasil ditambahkan',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Terjadi kesalahan saat menyimpan timeline';
                    
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMsg
                });
            }
        });
    });

    // Handle delete timeline
    $(document).on('submit', '.delete-timeline-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var timelineId = form.data('timeline-id');
        var prospekId = $('.btn-timeline.active').data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Timeline ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: `${baseUrl}/crm/prospek/timeline/${timelineId}`,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        loadTimeline(prospekId);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Timeline berhasil dihapus',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menghapus timeline'
                        });
                    }
                });
            }
        });
    });
    
    $(document).ready(function() {
        // Mapping nama bulan Indonesia ke angka bulan
        const bulanToAngka = {
            'Januari': '01', 'Februari': '02', 'Maret': '03', 'April': '04',
            'Mei': '05', 'Juni': '06', 'Juli': '07', 'Agustus': '08',
            'September': '09', 'Oktober': '10', 'November': '11', 'Desember': '12'
        };

        var table = $('#enhancedTable').DataTable({
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({page:'current'}).nodes();
                var lastMonthYear = null;
                
                // Hapus semua baris pembatas yang ada sebelumnya
                $('.month-divider').remove();
                
                $(rows).each(function(index, row) {
                    if ($(row).hasClass('month-divider') || $(row).hasClass('dataTables_empty')) {
                        return;
                    }
                    
                    var dateText = $(row).find('td:eq(1)').text().trim();
                    
                    // Tangani format "Selasa, 11 Maret 2025"
                    var dateParts = dateText.split(', ');
                    if (dateParts.length === 2) {
                        var datePart = dateParts[1]; // "11 Maret 2025"
                        var dateComponents = datePart.split(' ');
                        
                        if (dateComponents.length === 3) {
                            var day = dateComponents[0];
                            var monthIndo = dateComponents[1];
                            var year = dateComponents[2];
                            
                            // Dapatkan angka bulan
                            var monthNum = bulanToAngka[monthIndo] || '01';
                            
                            // Format YYYY-MM untuk grouping
                            var monthYearKey = year + '-' + monthNum;
                            var monthYearDisplay = monthIndo + ' ' + year;
                            
                            if (monthYearKey !== lastMonthYear) {
                                // Tambahkan baris pembatas
                                var dividerRow = '<tr class="month-divider">' +
                                    '<td colspan="17" class="text-center font-weight-bold">' +
                                    monthYearDisplay.toUpperCase() + '</td></tr>';
                                
                                $(dividerRow).insertBefore(row);
                                lastMonthYear = monthYearKey;
                            }
                        }
                    }
                });
            },
            "createdRow": function(row, data, dataIndex) {
                // Tambahkan atribut data untuk memudahkan debugging
                var dateText = $(row).find('td:eq(1)').text().trim();
                var dateParts = dateText.split(', ');
                if (dateParts.length === 2) {
                    $(row).attr('data-month-year', dateParts[1]);
                }
            }
        });

        // Fungsi untuk toggle informasi perusahaan
        $('#enhancedTable').on('click', '.toggle-info', function() {
            var row = $(this).closest('tr');
            row.find('.short-info, .full-info').toggle();
        });

        var currentLat, currentLng;
        
        // Map modal handler
        $('.btn-map').click(function(e) {
            e.preventDefault();
            currentLat = $(this).data('lat');
            currentLng = $(this).data('lng');
            
            $('#mapModal').modal('show');
            
            // Update tombol navigasi
            $('#btnNavigate').attr('href', 
                `https://www.google.com/maps/dir/?api=1&destination=${currentLat},${currentLng}&travelmode=driving`);
            
            // Initialize map after modal is shown
            $('#mapModal').on('shown.bs.modal', function() {
                var map = new google.maps.Map(document.getElementById('prospekMap'), {
                    center: {lat: parseFloat(currentLat), lng: parseFloat(currentLng)},
                    zoom: 15,
                    mapTypeId: 'roadmap'
                });
                
                new google.maps.Marker({
                    position: {lat: parseFloat(currentLat), lng: parseFloat(currentLng)},
                    map: map,
                    title: 'Lokasi Prospek'
                });
                
                // Tambahkan tombol untuk mendapatkan lokasi saat ini
                var locationButton = document.createElement("button");
                locationButton.textContent = "Gunakan Lokasi Saya";
                locationButton.classList.add("btn", "btn-sm", "btn-info");
                locationButton.style.margin = "10px";
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
                
                locationButton.addEventListener("click", () => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const pos = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                                
                                // Update tombol navigasi dengan rute dari lokasi saat ini
                                $('#btnNavigate').attr('href', 
                                    `https://www.google.com/maps/dir/?api=1&origin=${pos.lat},${pos.lng}&destination=${currentLat},${currentLng}&travelmode=driving`);
                                
                                // Tambahkan marker lokasi saat ini
                                new google.maps.Marker({
                                    position: pos,
                                    map: map,
                                    title: 'Lokasi Anda',
                                    icon: {
                                        url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                                    }
                                });
                                
                                // Gambar rute antara dua titik
                                const directionsService = new google.maps.DirectionsService();
                                const directionsRenderer = new google.maps.DirectionsRenderer({
                                    map: map,
                                    suppressMarkers: true
                                });
                                
                                directionsService.route({
                                    origin: pos,
                                    destination: {lat: parseFloat(currentLat), lng: parseFloat(currentLng)},
                                    travelMode: 'DRIVING'
                                }, (response, status) => {
                                    if (status === 'OK') {
                                        directionsRenderer.setDirections(response);
                                    }
                                });
                            },
                            (error) => {
                                alert('Error getting location: ' + error.message);
                            }
                        );
                    } else {
                        alert("Browser tidak mendukung geolocation");
                    }
                });
            });
        });

        // Tangani perubahan file input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Tangani submit form
        $('#importModal form').on('submit', function(e) {
            $('.modal-footer button').prop('disabled', true);
            $('#importModal').find('.modal-content').LoadingOverlay("show");
        });
    });
</script>
@endpush
