<style>
    .card-bagian {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    
    .form-control {
        border-radius: 4px;
        padding: 10px 15px;
    }
    
    .btn {
        border-radius: 4px;
        padding: 8px 20px;
        font-weight: 500;
    }
    
    .map-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 5px;
    }
    
    .badge {
        padding: 5px 10px;
        font-weight: 500;
        border-radius: 4px;
    }
    
    .badge-prospek { background-color: #6c757d; color: white; }
    .badge-followup { background-color: #17a2b8; color: white; }
    .badge-negosiasi { background-color: #ffc107; color: black; }
    .badge-closing { background-color: #28a745; color: white; }
    .badge-deposit { background-color: #007bff; color: white; }
    .badge-gagal { background-color: #dc3545; color: white; }
</style>

@extends('app')

@section('title', 'Edit Prospek')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Edit Prospek: {{ $prospek->nama }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('prospek.update', $prospek->id_prospek) }}" method="POST" id="prospekForm">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Dasar -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal" class="font-weight-bold">Tanggal <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" 
                                                value="{{ old('tanggal', \Carbon\Carbon::parse($prospek->tanggal)->format('Y-m-d\TH:i')) }}" required>
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" 
                                                   value="{{ old('nama', $prospek->nama) }}" required>
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_perusahaan" class="font-weight-bold">Nama Perusahaan</label>
                                            <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" name="nama_perusahaan" 
                                                   value="{{ old('nama_perusahaan', $prospek->nama_perusahaan) }}">
                                            @error('nama_perusahaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telepon" class="font-weight-bold">Telepon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" 
                                                   value="{{ old('telepon', $prospek->telepon) }}" required>
                                            @error('telepon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="font-weight-bold">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                                   value="{{ old('email', $prospek->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis" class="font-weight-bold">Jenis Usaha</label>
                                            <select class="form-control @error('jenis') is-invalid @enderror" id="jenis" name="jenis">
                                                <option value="">- Pilih Jenis Usaha -</option>
                                                <option value="Perusahaan" {{ old('jenis', $prospek->jenis) == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                                <option value="Perorangan" {{ old('jenis', $prospek->jenis) == 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                                                <option value="UKM" {{ old('jenis', $prospek->jenis) == 'UKM' ? 'selected' : '' }}>UKM</option>
                                                <option value="Koperasi" {{ old('jenis', $prospek->jenis) == 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                                            </select>
                                            @error('jenis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis" class="font-weight-bold">Jenis Usaha</label>
                                            <input type="text" class="form-control @error('jenis') is-invalid @enderror" id="jenis" name="jenis" 
                                                   value="{{ old('jenis', $prospek->jenis) }}">
                                            @error('jenis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pemilik_manager" class="font-weight-bold">Pemilik/Manager</label>
                                            <input type="text" class="form-control @error('pemilik_manager') is-invalid @enderror" id="pemilik_manager" name="pemilik_manager" 
                                                   value="{{ old('pemilik_manager', $prospek->pemilik_manager) }}">
                                            @error('pemilik_manager')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Perusahaan -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Perusahaan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kapasitas_produksi" class="font-weight-bold">Kapasitas Produksi</label>
                                            <input type="text" class="form-control @error('kapasitas_produksi') is-invalid @enderror" id="kapasitas_produksi" name="kapasitas_produksi" 
                                                   value="{{ old('kapasitas_produksi', $prospek->kapasitas_produksi) }}">
                                            @error('kapasitas_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sistem_produksi" class="font-weight-bold">Sistem Produksi</label>
                                            <input type="text" class="form-control @error('sistem_produksi') is-invalid @enderror" id="sistem_produksi" name="sistem_produksi" 
                                                   value="{{ old('sistem_produksi', $prospek->sistem_produksi) }}">
                                            @error('sistem_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bahan_bakar" class="font-weight-bold">Bahan Bakar</label>
                                            <input type="text" class="form-control @error('bahan_bakar') is-invalid @enderror" id="bahan_bakar" name="bahan_bakar" 
                                                   value="{{ old('bahan_bakar', $prospek->bahan_bakar) }}">
                                            @error('bahan_bakar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="menggunakan_boiler" class="font-weight-bold">Menggunakan Boiler?</label>
                                            <input type="text" class="form-control @error('menggunakan_boiler') is-invalid @enderror" id="menggunakan_boiler" name="menggunakan_boiler" 
                                                   value="{{ old('menggunakan_boiler', $prospek->menggunakan_boiler) }}">
                                            @error('menggunakan_boiler')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="informasi_perusahaan" class="font-weight-bold">Informasi Perusahaan</label>
                                    <textarea class="form-control @error('informasi_perusahaan') is-invalid @enderror" id="informasi_perusahaan" name="informasi_perusahaan" rows="3">{{ old('informasi_perusahaan', $prospek->informasi_perusahaan) }}</textarea>
                                    @error('informasi_perusahaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status dan Lokasi -->
                        <div class="card-bagian mb-4">
                            <div class="card-header bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Status dan Lokasi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="current_status" class="font-weight-bold">Status Saat Ini <span class="text-danger">*</span></label>
                                            <select class="form-control @error('current_status') is-invalid @enderror" id="current_status" name="current_status" required>
                                                @foreach(App\Models\Prospek::STATUSES as $key => $value)
                                                    <option value="{{ $key }}" {{ old('current_status', $prospek->current_status) == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('current_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="recruitment_id" class="font-weight-bold">Petugas <span class="text-danger">*</span></label>
                                            <select class="form-control @error('recruitment_id') is-invalid @enderror" id="recruitment_id" name="recruitment_id" required>
                                                <option value="">- Pilih Recruitment -</option>
                                                @foreach($recruitments as $recruitment)
                                                    <option value="{{ $recruitment->id }}" {{ old('recruitment_id', $prospek->recruitment_id) == $recruitment->id ? 'selected' : '' }}>
                                                        {{ $recruitment->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('recruitment_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_outlet" class="font-weight-bold">Outlet <span class="text-danger">*</span></label>
                                            <select class="form-control @error('id_outlet') is-invalid @enderror" id="id_outlet" name="id_outlet" required>
                                                @foreach($outlets as $outlet)
                                                    <option value="{{ $outlet->id_outlet }}" {{ old('id_outlet', $prospek->id_outlet) == $outlet->id_outlet ? 'selected' : '' }}>
                                                        {{ $outlet->nama_outlet }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_outlet')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Alamat -->
                                <div class="card-bagian mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="m-0 font-weight-bold text-primary">Informasi Alamat</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="alamat" class="font-weight-bold">Alamat Lengkap</label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat', $prospek->alamat) }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="provinsi_id" class="font-weight-bold">Provinsi <span class="text-danger"></span></label>
                                                    <select class="form-control @error('provinsi_id') is-invalid @enderror" id="provinsi_id" name="provinsi_id">
                                                        <option value="">Pilih Provinsi</option>
                                                        @foreach($provinsis as $provinsi)
                                                            <option value="{{ $provinsi->id }}" {{ old('provinsi_id', $prospek->provinsi_id) == $provinsi->id ? 'selected' : '' }}>
                                                                {{ $provinsi->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('provinsi_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kabupaten_id" class="font-weight-bold">Kabupaten/Kota <span class="text-danger"></span></label>
                                                    <select class="form-control @error('kabupaten_id') is-invalid @enderror" id="kabupaten_id" name="kabupaten_id" {{ !$prospek->provinsi_id ? 'disabled' : '' }}>
                                                        <option value="">Pilih Kabupaten/Kota</option>
                                                        @if(isset($kabupatens) && $kabupatens->count() > 0)
                                                            @foreach($kabupatens as $kabupaten)
                                                                <option value="{{ $kabupaten->id }}" {{ old('kabupaten_id', $prospek->kabupaten_id) == $kabupaten->id ? 'selected' : '' }}>
                                                                    {{ $kabupaten->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('kabupaten_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="latitude" class="font-weight-bold">Latitude</label>
                                                    <input type="text" class="form-control" id="latitude" name="latitude" readonly value="{{ old('latitude', $prospek->latitude) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kecamatan_id" class="font-weight-bold">Kecamatan <span class="text-danger"></span></label>
                                                    <select class="form-control @error('kecamatan_id') is-invalid @enderror" id="kecamatan_id" name="kecamatan_id" {{ !$prospek->kabupaten_id ? 'disabled' : '' }}>
                                                        <option value="">Pilih Kecamatan</option>
                                                        @if(isset($kecamatans) && $kecamatans->count() > 0)
                                                            @foreach($kecamatans as $kecamatan)
                                                                <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $prospek->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>
                                                                    {{ $kecamatan->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('kecamatan_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="desa_id" class="font-weight-bold">Desa/Kelurahan <span class="text-danger"></span></label>
                                                    <select class="form-control @error('desa_id') is-invalid @enderror" id="desa_id" name="desa_id" {{ !$prospek->kecamatan_id ? 'disabled' : '' }}>
                                                        <option value="">Pilih Desa/Kelurahan</option>
                                                        @if(isset($desas) && $desas->count() > 0)
                                                            @foreach($desas as $desa)
                                                                <option value="{{ $desa->id }}" {{ old('desa_id', $prospek->desa_id) == $desa->id ? 'selected' : '' }}>
                                                                    {{ $desa->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('desa_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="longitude" class="font-weight-bold">Longitude</label>
                                                    <input type="text" class="form-control" id="longitude" name="longitude" readonly value="{{ old('longitude', $prospek->longitude) }}">
                                                </div>
                                                <button type="button" class="btn btn-primary btn-block" id="btnUpdateLocation">
                                                    <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saat Ini
                                                </button>
                                                <small class="text-muted">Izinkan akses lokasi saat diminta browser</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map and Coordinates -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Lokasi Peta</label>
                                            <div id="locationMap" style="height: 400px; width: 100%; border-radius: 5px; border: 1px solid #ddd;"></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('prospek.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="col-md-4">
            <div class="card-bagian shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('prospek.timeline.store', $prospek->id_prospek) }}" method="POST">
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
                        @foreach($prospek->timeline()->orderBy('tanggal', 'desc')->get() as $timeline)
                        <div class="card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        @php
                                            $badgeClass = [
                                                'prospek' => 'secondary',
                                                'followup' => 'info',
                                                'negosiasi' => 'warning',
                                                'closing' => 'success',
                                                'deposit' => 'primary',
                                                'gagal' => 'danger'
                                            ][$timeline->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $timeline->status }}
                                        </span>
                                    </h6>
                                    <small>
                                        @if($timeline->tanggal instanceof \Illuminate\Support\Carbon)
                                            {{ $timeline->tanggal->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($timeline->tanggal)->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                </div>
                                <p class="mb-1">{{ $timeline->deskripsi }}</p>
                                <form action="{{ route('prospek.timeline.destroy', $timeline->id) }}" method="POST" class="text-right">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus timeline ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- foto -->
        <div class="col-md-4">
            <!-- Photo Upload Card -->
            <div class="card-bagian shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Prospek</h6>
                </div>
                <div class="card-body">
                    @if($prospek->photo)
                        <div class="text-center mb-3">
                            <img src="{{ asset($prospek->photo) }}" alt="Foto Prospek" 
                                class="img-fluid rounded" style="max-height: 200px;">
                            <p class="text-muted small mt-2">Foto saat ini</p>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada foto yang diupload
                        </div>
                    @endif

                    <form action="{{ route('prospek.uploadPhoto', $prospek->id_prospek) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="photo">Upload Foto Baru</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" 
                                    id="photo" name="photo" accept="image/*">
                                <label class="custom-file-label" for="photo">Pilih file...</label>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Format: JPG, PNG (Maksimal 2MB)
                            </small>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-upload"></i> Upload Foto
                            </button>
                        </div>
                    </form>

                    @if($prospek->photo)
                        <form action="{{ route('prospek.deletePhoto', $prospek->id_prospek) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" 
                                    onclick="return confirm('Hapus foto ini?')">
                                <i class="fas fa-trash"></i> Hapus Foto
                            </button>
                        </form>
                    @endif
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
    feather.replace();
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

        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = document.getElementById("photo").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
</script>
@endpush
