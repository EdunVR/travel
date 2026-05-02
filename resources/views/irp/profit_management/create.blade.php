@extends('app')

@section('title', 'Buat Pembagian Keuntungan Baru')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Form Pembagian Keuntungan</h6>
            <a href="{{ route('irp.profit-management.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('irp.profit-management.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Periode Pembagian*</label>
                            <input type="text" name="period" class="form-control" placeholder="Contoh: 2023-Q3" required>
                            <small class="form-text text-muted">Format: Tahun-Kuartal (2023-Q3) atau Tahun-Bulan (2023-08)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Distribusi*</label>
                            <input type="date" name="distribution_date" class="form-control" required 
                                   value="{{ old('distribution_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Total Keuntungan (Rp)*</label>
                    <input type="number" name="total_profit" class="form-control" required step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Bukti Transfer (Optional)</label>
                    <input type="file" name="proof_file" class="form-control-file">
                    <small class="form-text text-muted">Format: PDF, JPG, PNG (Maks. 2MB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator"></i> Hitung Pembagian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
