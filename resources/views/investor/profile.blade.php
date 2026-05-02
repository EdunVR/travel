@extends('investor.layouts.app')

@section('title', 'Profil Investor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Profil Investor</h4>
                <a href="{{ route('investor.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <img src="{{ $investor->photo ? asset('storage/'.$investor->photo) : asset('images/default-user.png') }}" 
                         class="rounded-circle img-thumbnail mb-3" 
                         alt="Foto Profil" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h5 class="mb-1">{{ $investor->name }}</h5>
                    <p class="text-muted mb-1">{{ $investor->email }}</p>
                    <p class="text-muted">{{ $investor->phone }}</p>
                    
                    <div class="d-flex justify-content-center mb-3">
                        <span class="badge bg-{{ $investor->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($investor->status) }}
                        </span>
                        <span class="badge bg-info ms-2">
                            {{ ucfirst($investor->category) }}
                        </span>
                    </div>
                    
                    <p class="text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Bergabung sejak {{ $investor->join_date->format('d F Y') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('investor.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $investor->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $investor->email) }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $investor->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="photo" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $investor->address) }}</textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Investasi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Tanggal Bergabung</th>
                                    <td>{{ $investor->join_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Investasi Awal</th>
                                    <td>Rp {{ number_format($investor->initial_investment, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Investasi</th>
                                    <td>Rp {{ number_format($investor->accounts->sum('current_balance'), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Bagi Hasil</th>
                                    <td>Rp {{ number_format($investor->accounts->sum('profit_balance'), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Rekening</th>
                                    <td>{{ $investor->accounts->count() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
