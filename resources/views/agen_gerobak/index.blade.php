<style>
.table-inventory tbody tr.danger {
    background-color: #f2dede !important;
}
.table-inventory tbody tr.warning {
    background-color: #fcf8e3 !important;
}
.table-inventory thead tr {
    background-color: #3c8dbc;
    color: white;
}
.small {
    font-size: 12px;
    line-height: 1.2;
}
</style>

@extends('app')

@section('title')
    Manajemen Agen dan Gerobak
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Manajemen Agen dan Gerobak</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    @if($outlets->count() > 1)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_outlet">Pilih Outlet</label>
                            <select name="id_outlet" id="id_outlet" class="form-control">
                                <option value="">Semua Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="btn-group">
                    <button onclick="addForm('{{ route('agen_gerobak.store') }}')" class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus-circle"></i> Tambah Agen
                    </button>
                    <button onclick="deleteSelected('{{ route('agen_gerobak.delete_selected') }}')" 
                            class="btn btn-danger btn-xs btn-flat">
                        <i class="fa fa-trash"></i> Hapus Terpilih
                    </button>
                    <button onclick="refreshLocations()" class="btn btn-info btn-xs btn-flat">
                        <i class="fa fa-refresh"></i> Refresh Lokasi
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-agen">
                    @csrf
                    <table class="table table-stiped table-bordered table-agen">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">No</th>
                            <th>Kode Agen</th>
                            <th>Nama Agen</th>
                            <th>Outlet</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Jumlah Gerobak</th>
                            <th>Total Produk</th>
                            <th>Jumlah Pembelian</th>
                            <th>Total Pembelian (Rp)</th>
                            <th>Total Penjualan (Rp)</th>
                            <th>Lokasi</th>
                            <th width="15%"><i class="fa fa-cog"></i> Aksi</th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('agen_gerobak.form')
@includeIf('agen_gerobak.detail')
@endsection

@push('scripts')
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&callback=initMap" async defer></script> -->
<script>
    let tableAgen, tableDetailAgen, tablePenjualanAgen, tableInventoryAgen;
    let map, markers = [];
    let isMapLoaded = false;

    function loadGoogleMaps() {
        if (typeof google === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&callback=initMap';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        } else {
            initMap();
        }
    }

    $(function () {
        feather.replace();
        tableAgen = $('.table-agen').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('agen_gerobak.data') }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                }
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_member'},
                {data: 'nama'},
                {data: 'nama_outlet'},
                {data: 'telepon'},
                {data: 'alamat'},
                {data: 'total_gerobak', className: 'text-center'},
                {data: 'total_produk', className: 'text-center'},
                {data: 'jumlah_pembelian', className: 'text-center'},
                {data: 'total_pembelian', className: 'text-right'},
                {data: 'total_penjualan', className: 'text-right'},
                {data: 'lokasi', searchable: false, sortable: false},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#id_outlet').on('change', function () {
            tableAgen.ajax.reload();
        });
    });

    function initMap() {
        if (typeof google === 'undefined') {
            console.warn('Google Maps API belum loaded');
            return;
        }
        
        try {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: {lat: -6.2088, lng: 106.8456} // Default to Jakarta
            });
            isMapLoaded = true;
            console.log('Google Maps initialized successfully');
        } catch (error) {
            console.error('Error initializing Google Maps:', error);
        }
    }

    $(document).on('shown.bs.modal', '#modal-form', function () {
        if (!isMapLoaded) {
            loadGoogleMaps();
        }
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Agen');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama]').focus();
        
        // Initialize location picker
        initLocationPicker();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Agen');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');

        $.get(url)
            .done((response) => {
                $('#modal-form [name=id_outlet]').val(response.agen.id_outlet);
                $('#modal-form [name=nama]').val(response.agen.nama);
                $('#modal-form [name=telepon]').val(response.agen.telepon);
                $('#modal-form [name=alamat]').val(response.agen.alamat);
                $('#modal-form [name=latitude]').val(response.agen.latitude);
                $('#modal-form [name=longitude]').val(response.agen.longitude);
                
                // Update map location
                if (response.agen.latitude && response.agen.longitude) {
                    updateMapLocation(parseFloat(response.agen.latitude), parseFloat(response.agen.longitude));
                }
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
            
        // Initialize location picker
        initLocationPicker();
    }

    function initLocationPicker() {
        if (typeof google === 'undefined' || !map) {
            console.warn('Google Maps not ready for location picker');
            setTimeout(initLocationPicker, 500); // Retry after 500ms
            return;
        }
        
        const latitudeInput = $('#latitude');
        const longitudeInput = $('#longitude');
        
        // Jika ada existing values, set the map
        if (latitudeInput.val() && longitudeInput.val()) {
            updateMapLocation(parseFloat(latitudeInput.val()), parseFloat(longitudeInput.val()));
        }
        
        // Add click listener to map
        map.addListener('click', (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            
            latitudeInput.val(lat);
            longitudeInput.val(lng);
            
            updateMapLocation(lat, lng);
        });
    }

    function updateMapLocation(lat, lng) {
        if (typeof google === 'undefined') {
            console.warn('Google Maps not available for update');
            return;
        }
        
        // Clear existing markers
        clearMarkers();
        
        // Add new marker
        const marker = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            draggable: true
        });
        
        markers.push(marker);
        
        // Center map on marker
        map.setCenter({lat: lat, lng: lng});
        
        // Update inputs when marker is dragged
        marker.addListener('dragend', () => {
            $('#latitude').val(marker.getPosition().lat());
            $('#longitude').val(marker.getPosition().lng());
        });
    }

    function clearMarkers() {
        markers.forEach(marker => {
            if (marker && typeof marker.setMap === 'function') {
                marker.setMap(null);
            }
        });
        markers = [];
    }

    function showDetail(url) {
        const id = url.split('/').pop();
        console.log('Showing detail for agen ID:', id);
        
        $('#modal-detail').modal('show');
        
        $.get(url)
            .done((response) => {
                // Display agen info
                $('#detail-nama').text(response.agen.nama);
                $('#detail-telepon').text(response.agen.telepon);
                $('#detail-alamat').text(response.agen.alamat);
                $('#detail-outlet').text(response.agen.outlet ? response.agen.outlet.nama_outlet : '-');
                
                // Check if URL has hash for specific tab
                const hash = window.location.hash;
                 window.currentAgenId = id;
                if (hash === '#tab-gerobak') {
                    // Aktifkan tab gerobak
                    $('.nav-tabs a[href="#tab-gerobak"]').tab('show');
                    loadGerobak(response.agen.id_member);
                } else {
                    // Load data untuk tabs
                    loadPenjualan(response.agen.id_member);
                    loadInventory(response.agen.id_member);
                    loadGerobak(response.agen.id_member);
                }
            })
            .fail((errors) => {
                console.error('Error loading detail:', errors);
                alert('Tidak dapat memuat detail agen');
            });
    }

    function initDetailMap(lat, lng) {
        const detailMap = new google.maps.Map(document.getElementById('detail-map'), {
            zoom: 15,
            center: {lat: parseFloat(lat), lng: parseFloat(lng)}
        });
        
        new google.maps.Marker({
            position: {lat: parseFloat(lat), lng: parseFloat(lng)},
            map: detailMap,
            title: 'Lokasi Agen'
        });
    }

    function refreshLocations() {
        // This would typically make an API call to update all gerobak locations
        alert('Fitur refresh lokasi akan mengupdate semua lokasi gerobak');
        // Implementasi actual akan memanggil API untuk update lokasi
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    tableAgen.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        const selectedIds = [];
        $('.table-agen tbody input:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Tidak ada data yang dipilih!');
            return;
        }

        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete',
                    'id_agen': selectedIds
                })
                .done((response) => {
                    tableAgen.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                });
        }
    }

    function loadPenjualan(agenId) {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if ($.fn.DataTable.isDataTable('.table-penjualan')) {
            tablePenjualanAgen.destroy();
        }
        
        tablePenjualanAgen = $('.table-penjualan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `{{ url('agen_gerobak') }}/${agenId}/laporan-penjualan`,
                data: function (d) {
                    d.start_date = startDate;
                    d.end_date = endDate;
                }
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'tanggal',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                },
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {
                    data: 'tipe',
                    render: function(data) {
                        if (data === 'pembelian') {
                            return '<span class="label label-success">Pembelian</span>';
                        } else {
                            return '<span class="label label-danger">Penjualan</span>';
                        }
                    }
                },
                {
                    data: 'stok_awal',
                    className: 'text-right'
                },
                {
                    data: 'pembelian',
                    className: 'text-right',
                    render: function(data) {
                        return data > 0 ? data : '-';
                    }
                },
                {
                    data: 'penjualan',
                    className: 'text-right',
                    render: function(data) {
                        return data > 0 ? data : '-';
                    }
                },
                {
                    data: 'stok_akhir',
                    className: 'text-right',
                    render: function(data, type, row) {
                        const stokAkhir = parseInt(data);
                        if (stokAkhir < 0) {
                            return '<span class="text-danger">' + stokAkhir + ' (Kurang)</span>';
                        }
                        return stokAkhir;
                    }
                },
                {
                    data: 'omset',
                    className: 'text-right',
                    render: function(data) {
                        return data > 0 ? 'Rp ' + formatRupiah(data) : '-';
                    }
                },
                {
                    data: 'gerobak',
                    render: function(data, type, row) {
                        return row.tipe === 'penjualan' ? 
                            (data + (row.kode_gerobak ? ' (' + row.kode_gerobak + ')' : '')) : 
                            '-';
                    }
                }
            ],
            order: [[1, 'desc']], // Sort by tanggal descending
            drawCallback: function (settings) {
                // Update omset info
                const omset = settings.json.omset;
                if (omset) {
                    $('#total-pembelian').text('Rp ' + formatRupiah(omset.total_pembelian || 0));
                    $('#total-omset').text('Rp ' + formatRupiah(omset.total_omset || 0));
                    $('#total-transaksi').text(omset.total_transaksi || 0);
                    $('#periode').text(omset.start_date + ' s/d ' + omset.end_date);
                    $('#omset-info').show();
                }
            },
            error: function (xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                alert('Terjadi kesalahan saat memuat data. Silakan refresh halaman.');
            }
        });
    }

    // Fungsi untuk menampilkan penjualan per gerobak
    function showPenjualanPerGerobak(agenId, produkId, startDate, endDate) {
        console.log('Loading penjualan per gerobak:', {
            agenId: agenId,
            produkId: produkId,
            startDate: startDate,
            endDate: endDate
        });
        
        // Ambil data penjualan per gerobak dari server
        $.get(`{{ url('agen_gerobak') }}/${agenId}/penjualan-gerobak/${produkId}`, {
            start_date: startDate,
            end_date: endDate
        })
        .done(function(response) {
            // Tampilkan modal dengan detail penjualan per gerobak
            $('#modal-penjualan-gerobak .modal-body').html(response);
            $('#modal-penjualan-gerobak').modal('show');
        })
        .fail(function(error) {
            console.error('Error loading penjualan per gerobak:', error);
            alert('Gagal memuat detail penjualan per gerobak. Lihat console untuk detail.');
        });
    }

    function loadInventory(agenId) {
        const url = `{{ url('agen_gerobak') }}/${agenId}/inventory`;
        
        if ($.fn.DataTable.isDataTable('.table-inventory')) {
            tableInventoryAgen.destroy();
        }
        
        tableInventoryAgen = $('.table-inventory').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, thrown) {
                    console.error('Error loading inventory:', error);
                    alert('Gagal memuat data inventory. Silakan refresh halaman.');
                }
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false,
                    sortable: false,
                    className: 'text-center'
                },
                {
                    data: 'kode_produk',
                    className: 'text-center'
                },
                {
                    data: 'nama_produk'
                },
                {
                    data: 'stok_agen',
                    className: 'text-center',
                    render: function(data) {
                        return '<strong>' + data + '</strong>';
                    }
                },
                {
                    data: 'total_stok_gerobak',
                    className: 'text-center',
                    render: function(data) {
                        return '<strong>' + data + '</strong>';
                    }
                },
                {
                    data: 'stok_tersedia',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'detail_gerobak',
                    render: function(data, type, row) {
                        return data;
                    }
                }
            ],
            order: [[2, 'asc']],
            createdRow: function(row, data, dataIndex) {
                // Gunakan stok_tersedia_raw untuk styling
                if (data.stok_tersedia_raw < 0) {
                    $(row).addClass('danger');
                } else if (data.stok_tersedia_raw == 0) {
                    $(row).addClass('warning');
                }
            },
            language: {
                emptyTable: "Tidak ada data inventory",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ produk",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 produk",
                loadingRecords: "Memuat data...",
                processing: "Memproses...",
                search: "Cari:",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            }
        });
    }

    function refreshInventory() {
        const agenId = window.currentAgenId;
        if (agenId && typeof tableInventoryAgen !== 'undefined') {
            tableInventoryAgen.ajax.reload();
        }
    }

    function loadGerobak(agenId) {
        const url = `{{ url('agen_gerobak') }}/${agenId}/gerobak`;
        
        $.get(url)
            .done((response) => {
                $('#gerobak-list').html(response);
                
                // Re-initialize DataTables setelah content loaded
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('.table-gerobak')) {
                        $('.table-gerobak').DataTable().ajax.reload();
                    }
                }, 500);
            })
            .fail((errors) => {
                console.error('Error loading gerobak:', errors);
                $('#gerobak-list').html('<p class="text-muted">Gagal memuat data gerobak</p>');
            });
    }

    if (window.location.hash) {
        const tab = window.location.hash;
        $(`a[href="${tab}"]`).tab('show');
    }

    function filterPenjualan() {
        const agenId = window.currentAgenId;
        if (agenId && typeof tablePenjualanAgen !== 'undefined') {
            // Destroy dan reload datatable
            tablePenjualanAgen.destroy();
            loadPenjualan(agenId);
        }
    }

    function tambahLaporan() {
        const agenId = window.currentAgenId;
        if (agenId) {
            window.open(`{{ url('agen_gerobak') }}/${agenId}/laporan/create`, '_blank');
        }
    }

    function formatRupiah(angka) {
        if (!angka) return '0';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function syncStok() {
        const agenId = window.currentAgenId;
        if (!agenId) return;
        
        if (confirm('Sync stok dari data penjualan? Proses ini akan update stok agen berdasarkan transaksi POS.')) {
            $.post(`{{ url('agen_gerobak') }}/${agenId}/sync-stok`, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                alert('Sync berhasil: ' + response.message);
                loadInventory(agenId);
                if (typeof tablePenjualanAgen !== 'undefined') {
                    tablePenjualanAgen.ajax.reload();
                }
            })
            .fail(function(xhr) {
                alert('Gagal sync stok');
                console.error(xhr.responseText);
            });
        }
    }
</script>
@endpush
