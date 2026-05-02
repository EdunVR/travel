<style>
    .form-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .section-header h5 {
        margin: 0;
        margin-left: 10px;
        font-weight: 600;
        color: #4e73df;
    }
    
    .section-header i {
        width: 24px;
        height: 24px;
        color: #4e73df;
    }
    
    .form-control {
        border-radius: 4px;
    }
    
    .btn {
        border-radius: 4px;
        font-weight: 500;
    }
    
    #locationMap {
        border-radius: 4px;
        border: 1px solid #ddd;
    }
</style>

@extends('app')

@section('title', 'Tambah Prospek Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Form Tambah Prospek</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('prospek.store') }}" method="POST" id="prospekForm">
                        @csrf

                        <!-- Informasi Dasar -->
                        <div class="form-section">
                            <div class="section-header">
                                <i data-feather="user"></i>
                                <h5>Informasi Dasar</h5>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" 
                                                value="{{ old('tanggal', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama" 
                                                   value="{{ old('nama') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_perusahaan">Nama Perusahaan</label>
                                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" 
                                                   value="{{ old('nama_perusahaan') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telepon">Telepon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="telepon" name="telepon" 
                                                   value="{{ old('telepon') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis">Jenis Usaha</label>
                                            <select class="form-control" id="jenis" name="jenis">
                                                <option value="">- Pilih Jenis Usaha -</option>
                                                <option value="Perusahaan" {{ old('jenis') == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                                <option value="Perorangan" {{ old('jenis') == 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                                                <option value="UKM" {{ old('jenis') == 'UKM' ? 'selected' : '' }}>UKM</option>
                                                <option value="Koperasi" {{ old('jenis') == 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis">Jenis Usaha</label>
                                            <input type="text" class="form-control" id="jenis" name="jenis" 
                                                   value="{{ old('jenis') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pemilik_manager">Pemilik/Manager</label>
                                            <input type="text" class="form-control" id="pemilik_manager" name="pemilik_manager" 
                                                   value="{{ old('pemilik_manager') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Perusahaan -->
                        <div class="form-section">
                            <div class="section-header">
                                <i data-feather="briefcase"></i>
                                <h5>Informasi Perusahaan</h5>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kapasitas_produksi">Kapasitas Produksi</label>
                                            <input type="text" class="form-control" id="kapasitas_produksi" name="kapasitas_produksi" 
                                                   value="{{ old('kapasitas_produksi') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sistem_produksi">Sistem Produksi</label>
                                            <input type="text" class="form-control" id="sistem_produksi" name="sistem_produksi" 
                                                   value="{{ old('sistem_produksi') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bahan_bakar">Bahan Bakar</label>
                                            <input type="text" class="form-control" id="bahan_bakar" name="bahan_bakar" 
                                                   value="{{ old('bahan_bakar') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="menggunakan_boiler">Menggunakan Boiler?</label>
                                            <input type="text" class="form-control" id="menggunakan_boiler" name="menggunakan_boiler" 
                                                   value="{{ old('menggunakan_boiler') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="informasi_perusahaan">Informasi Perusahaan</label>
                                    <textarea class="form-control" id="informasi_perusahaan" name="informasi_perusahaan" rows="3">{{ old('informasi_perusahaan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Status dan Lokasi -->
                        <div class="form-section">
                            <div class="section-header">
                                <i data-feather="navigation"></i>
                                <h5>Status dan Lokasi</h5>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_status">Status Saat Ini <span class="text-danger">*</span></label>
                                            <select class="form-control" id="current_status" name="current_status" required>
                                                @foreach(App\Models\Prospek::STATUSES as $key => $value)
                                                    <option value="{{ $key }}" {{ old('current_status') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="recruitment_id">Petugas <span class="text-danger">*</span></label>
                                            <select class="form-control" id="recruitment_id" name="recruitment_id" required>
                                                <option value="">- Pilih Recruitment -</option>
                                                @foreach($recruitments as $recruitment)
                                                    <option value="{{ $recruitment->id }}" {{ old('recruitment_id') == $recruitment->id ? 'selected' : '' }}>
                                                        {{ $recruitment->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_outlet">Outlet <span class="text-danger">*</span></label>
                                            <select class="form-control" id="id_outlet" name="id_outlet" required>
                                                @foreach($outlets as $outlet)
                                                    <option value="{{ $outlet->id_outlet }}" {{ old('id_outlet') == $outlet->id_outlet ? 'selected' : '' }}>
                                                        {{ $outlet->nama_outlet }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Alamat -->
                                <div class="form-section">
                                    <div class="section-header">
                                        <i data-feather="map-pin"></i>
                                        <h5>Informasi Alamat</h5>
                                    </div>
                                    <div class="section-body">
                                        <div class="form-group">
                                            <label for="alamat">Alamat Lengkap</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ old('alamat') }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="provinsi_id">Provinsi <span class="text-danger"></span></label>
                                                    <select class="form-control" id="provinsi_id" name="provinsi_id">
                                                        <option value="">Pilih Provinsi</option>
                                                        @foreach($provinsis as $provinsi)
                                                            <option value="{{ $provinsi->id }}" {{ old('provinsi_id') == $provinsi->id ? 'selected' : '' }}>
                                                                {{ $provinsi->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kabupaten_id">Kabupaten/Kota <span class="text-danger"></span></label>
                                                    <select class="form-control" id="kabupaten_id" name="kabupaten_id" disabled>
                                                        <option value="">Pilih Kabupaten/Kota</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kecamatan_id">Kecamatan <span class="text-danger"></span></label>
                                                    <select class="form-control" id="kecamatan_id" name="kecamatan_id" disabled>
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="desa_id">Desa/Kelurahan <span class="text-danger"></span></label>
                                                    <select class="form-control" id="desa_id" name="desa_id" disabled>
                                                        <option value="">Pilih Desa/Kelurahan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map and Coordinates -->
                                <div class="row mt-3">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Lokasi Peta</label>
                                            <div id="locationMap" style="height: 400px; width: 100%;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="text" class="form-control" id="latitude" name="latitude" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="text" class="form-control" id="longitude" name="longitude" readonly>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-block" id="btnUpdateLocation">
                                            <i data-feather="map-pin"></i> Gunakan Lokasi Saat Ini
                                        </button>
                                        <small class="text-muted">Izinkan akses lokasi saat diminta browser</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i data-feather="save"></i> Simpan
                            </button>
                            <a href="{{ route('prospek.index') }}" class="btn btn-secondary px-4">
                                <i data-feather="x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt3PCWHRN4O2fKx9T9uOqFEBPur11DPHY&libraries=places&callback=initMap" async defer></script>
<script>
    feather.replace();
    // Buat variabel global untuk route
    window.Laravel = {
        routes: {
            kabupaten: "{{ route('api.wilayah.kabupaten', ['provinsi_id' => ':provinsi_id']) }}",
            kecamatan: "{{ route('api.wilayah.kecamatan', ['kabupaten_id' => ':kabupaten_id']) }}",
            desa: "{{ route('api.wilayah.desa', ['kecamatan_id' => ':kecamatan_id']) }}"
        }
    };
</script>
<script>
    function prepareRoute(route, params) {
        let url = route;
        for (const key in params) {
            url = url.replace(`:${key}`, params[key]);
        }
        return url;
    }

    // Variabel global untuk map dan marker
    var map, marker;

    function initMap() {
        // Initialize map
        map = new google.maps.Map(document.getElementById('locationMap'), {
            center: {lat: {{ $prospek->latitude ?? -6.2088 }}, lng: {{ $prospek->longitude ?? 106.8456 }}},
            zoom: 12
        });

        // Create marker
        marker = new google.maps.Marker({
            position: {lat: {{ $prospek->latitude ?? -6.2088 }}, lng: {{ $prospek->longitude ?? 106.8456 }}},
            map: map,
            draggable: true
        });

        // Event listener untuk marker yang digeser
        marker.addListener('dragend', function() {
            var position = marker.getPosition();
            updateCoordinateFields(position.lat(), position.lng());
        });

        // Search box for location
        var input = document.createElement('input');
        input.id = 'pac-input';
        input.className = 'controls';
        input.placeholder = 'Cari lokasi...';
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

        var searchBox = new google.maps.places.SearchBox(input);

        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();
            if (places.length == 0) {
                return;
            }

            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);

            // Set marker position
            marker.setPosition(places[0].geometry.location);
            updateCoordinateFields(places[0].geometry.location.lat(), places[0].geometry.location.lng());
        });

        // Button untuk mendapatkan lokasi saat ini
        $('#btnUpdateLocation').click(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        updateCoordinateFields(pos.lat, pos.lng);
                    },
                    function(error) {
                        alert('Error getting location: ' + error.message);
                    }
                );
            } else {
                alert("Browser tidak mendukung geolocation");
            }
        });
    }

    // Fungsi update form koordinat
    function updateCoordinateFields(lat, lng) {
        $('#latitude').val(lat);
        $('#longitude').val(lng);
    }

    // Fungsi untuk geocoding berdasarkan alamat
    function geocodeAddress(address, callback) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({address: address}, function(results, status) {
            if (status === 'OK' && results[0]) {
                var location = results[0].geometry.location;
                if (typeof callback === 'function') {
                    callback(location);
                }
            } else {
                console.error('Geocode was not successful for the following reason:', status);
            }
        });
    }

    function updateMapBySelectedRegion() {
        // Tampilkan loading indicator
        $('#locationMap').append('<div class="map-loading-overlay"><i class="fas fa-spinner fa-spin"></i> Memperbarui peta...</div>');
        
        try {
            // Dapatkan elemen yang terpilih dengan pengecekan
            const getSelectedText = (selector) => {
                const selectedOption = $(selector).find('option:selected');
                return selectedOption.length ? selectedOption.text().trim() : '';
            };

            const provinsi = getSelectedText('#provinsi_id');
            const kabupaten = getSelectedText('#kabupaten_id');
            const kecamatan = getSelectedText('#kecamatan_id');
            const desa = getSelectedText('#desa_id');
            
            // Bangun alamat berdasarkan level yang dipilih
            const addressParts = [];
            
            // Prioritas: gunakan level terendah yang dipilih
            if (desa && desa !== 'Pilih Desa/Kelurahan') {
                addressParts.push(desa);
            }
            if (kecamatan && kecamatan !== 'Pilih Kecamatan') {
                addressParts.push(kecamatan);
            }
            if (kabupaten && kabupaten !== 'Pilih Kabupaten/Kota') {
                addressParts.push(kabupaten);
            }
            if (provinsi && provinsi !== 'Pilih Provinsi') {
                addressParts.push(provinsi);
            }
            
            if (addressParts.length === 0) {
                $('.map-loading-overlay').remove();
                return;
            }
            
            const fullAddress = addressParts.join(', ');
            
            geocodeAddress(fullAddress, function(location) {
                if (!location) {
                    console.error('Location not found for address:', fullAddress);
                    $('.map-loading-overlay').remove();
                    return;
                }
                
                map.setCenter(location);
                marker.setPosition(location);
                updateCoordinateFields(location.lat(), location.lng());
                
                // Animasi zoom berdasarkan level detail
                let zoomLevel = 12; // Default untuk kecamatan/desa
                if (addressParts.length <= 2) zoomLevel = 10; // Untuk kabupaten
                if (addressParts.length === 1) zoomLevel = 8; // Untuk provinsi
                map.setZoom(zoomLevel);
                
                // Sembunyikan loading indicator
                $('.map-loading-overlay').remove();
            });
        } catch (error) {
            console.error('Error updating map:', error);
            $('.map-loading-overlay').remove();
        }
    }

    $(document).ready(function() {
        // Inisialisasi dropdown wilayah
        initWilayahDropdown();

        const API_BASE_URL = window.location.origin + '/api/v1';

        // Event ketika provinsi dipilih
        $('#provinsi_id').change(function() {
            var provinsiId = $(this).val();
            $('#kabupaten_id').html('<option value="">Pilih Kabupaten/Kota</option>');
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (provinsiId) {
                $('#kabupaten_id').prop('disabled', false);
                loadKabupaten(provinsiId);
            } else {
                $('#kabupaten_id').prop('disabled', true);
                $('#kecamatan_id').prop('disabled', true);
                $('#desa_id').prop('disabled', true);
            }
        });

        // Event ketika kabupaten dipilih
        $('#kabupaten_id').change(function() {
            var kabupatenId = $(this).val();
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>');
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (kabupatenId) {
                $('#kecamatan_id').prop('disabled', false);
                loadKecamatan(kabupatenId);
            } else {
                $('#kecamatan_id').prop('disabled', true);
                $('#desa_id').prop('disabled', true);
            }
        });

        // Event ketika kecamatan dipilih
        $('#kecamatan_id').change(function() {
            var kecamatanId = $(this).val();
            $('#desa_id').html('<option value="">Pilih Desa/Kelurahan</option>');
            
            if (kecamatanId) {
                $('#desa_id').prop('disabled', false);
                loadDesa(kecamatanId);
            } else {
                $('#desa_id').prop('disabled', true);
            }
        });

        // Fungsi inisialisasi dropdown wilayah
        function initWilayahDropdown() {
            @if(isset($prospek))
                var provinsiId = "{{ $prospek->provinsi_id }}";
                var kabupatenId = "{{ $prospek->kabupaten_id }}";
                var kecamatanId = "{{ $prospek->kecamatan_id }}";
                var desaId = "{{ $prospek->desa_id }}";
                
                if (provinsiId) {
                    $('#kabupaten_id').prop('disabled', false);
                    loadKabupaten(provinsiId, kabupatenId, function() {
                        if (kabupatenId) {
                            $('#kecamatan_id').prop('disabled', false);
                            loadKecamatan(kabupatenId, kecamatanId, function() {
                                if (kecamatanId) {
                                    $('#desa_id').prop('disabled', false);
                                    loadDesa(kecamatanId, desaId);
                                }
                            });
                        }
                    });
                }
            @endif
        }

        function loadKabupaten(provinsiId, selectedId = null, callback = null) {
            const url = prepareRoute(Laravel.routes.kabupaten, { provinsi_id: provinsiId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Kabupaten/Kota</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#kabupaten_id').html(options);
                
                if (selectedId) {
                    $('#kabupaten_id').val(selectedId).trigger('change');
                }
                
                if (callback) callback();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading kabupaten:", textStatus, errorThrown);
                $('#kabupaten_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Fungsi load kecamatan dengan callback
        function loadKecamatan(kabupatenId, selectedId = null, callback = null) {
            const url = prepareRoute(Laravel.routes.kecamatan, { kabupaten_id: kabupatenId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Kecamatan</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#kecamatan_id').html(options);
                
                if (selectedId) {
                    $('#kecamatan_id').val(selectedId).trigger('change');
                }
                
                if (callback) callback();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading kecamatan:", textStatus, errorThrown);
                $('#kecamatan_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Fungsi load desa
        function loadDesa(kecamatanId, selectedId = null) {
            const url = prepareRoute(Laravel.routes.desa, { kecamatan_id: kecamatanId });
            
            $.get(url, function(data) {
                var options = '<option value="">Pilih Desa/Kelurahan</option>';
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#desa_id').html(options);
                
                if (selectedId) {
                    $('#desa_id').val(selectedId);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading desa:", textStatus, errorThrown);
                $('#desa_id').html('<option value="">Gagal memuat data</option>');
            });
        }

        // Variabel untuk menyimpan nilai terakhir
        var lastProvinsi = '';
        var lastKabupaten = '';
        var lastKecamatan = '';
        var lastDesa = '';

        $('#provinsi_id, #kabupaten_id, #kecamatan_id, #desa_id').change(function() {
            // Aktifkan dropdown berikutnya jika ada pilihan
            if ($(this).attr('id') === 'provinsi_id' && $(this).val()) {
                $('#kabupaten_id').prop('disabled', false);
            } else if ($(this).attr('id') === 'kabupaten_id' && $(this).val()) {
                $('#kecamatan_id').prop('disabled', false);
            } else if ($(this).attr('id') === 'kecamatan_id' && $(this).val()) {
                $('#desa_id').prop('disabled', false);
            }
            
            // Cek apakah nilai benar-benar berubah
            var currentProvinsi = $('#provinsi_id option:selected').text();
            var currentKabupaten = $('#kabupaten_id option:selected').text();
            var currentKecamatan = $('#kecamatan_id option:selected').text();
            var currentDesa = $('#desa_id option:selected').text();
            
            if (currentProvinsi !== lastProvinsi || 
                currentKabupaten !== lastKabupaten ||
                currentKecamatan !== lastKecamatan ||
                currentDesa !== lastDesa) {
                
                // Update nilai terakhir
                lastProvinsi = currentProvinsi;
                lastKabupaten = currentKabupaten;
                lastKecamatan = currentKecamatan;
                lastDesa = currentDesa;
                
                // Update peta berdasarkan pilihan wilayah
                updateMapBySelectedRegion();
            }
        });
    });
</script>
@endpush
