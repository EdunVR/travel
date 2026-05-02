@extends('app')

@section('title', 'Pengaturan Kolom Prospek')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengaturan Kolom Prospek</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('prospek.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Informasi Dasar</h5>
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <select name="nama" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'nama')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'nama')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <select name="telepon" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'telepon')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'telepon')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email</label>
                                    <select name="email" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'email')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'email')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <select name="alamat" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'alamat')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'alamat')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Kota/Kabupaten</label>
                                    <select name="kota_kab" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'kota_kab')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'kota_kab')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select name="kecamatan" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'kecamatan')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'kecamatan')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Desa/Kelurahan</label>
                                    <select name="desa_kelurahan" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'desa_kelurahan')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'desa_kelurahan')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Informasi Perusahaan</h5>
                                <div class="form-group">
                                    <label>Nama Perusahaan</label>
                                    <select name="nama_perusahaan" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'nama_perusahaan')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'nama_perusahaan')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Jenis Usaha</label>
                                    <select name="jenis" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'jenis')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'jenis')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Pemilik/Manager</label>
                                    <select name="pemilik_manager" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'pemilik_manager')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'pemilik_manager')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Kapasitas Produksi</label>
                                    <select name="kapasitas_produksi" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'kapasitas_produksi')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'kapasitas_produksi')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Sistem Produksi</label>
                                    <select name="sistem_produksi" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'sistem_produksi')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'sistem_produksi')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Bahan Bakar</label>
                                    <select name="bahan_bakar" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'bahan_bakar')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'bahan_bakar')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Informasi Perusahaan</label>
                                    <select name="informasi_perusahaan" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'informasi_perusahaan')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'informasi_perusahaan')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <h5 class="mt-4">Lokasi</h5>
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <select name="latitude" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'latitude')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'latitude')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <select name="longitude" class="form-control">
                                        <option value="required" {{ $settings->where('field_name', 'longitude')->first()->is_required ? 'selected' : '' }}>Required</option>
                                        <option value="optional" {{ !$settings->where('field_name', 'longitude')->first()->is_required ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
