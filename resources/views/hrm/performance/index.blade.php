<style>
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

@section('title', 'Manajemen Kinerja Karyawan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen Kinerja Karyawan</h6>
            <div>
                <form action="{{ route('hrm.performance.index') }}" method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="month" class="mr-2">Pilih Periode:</label>
                        <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2 mb-2">Filter</button>
                </form>
                <a href="{{ route('hrm.performance.create') }}" class="btn btn-primary ml-2">
                    <i class="fas fa-plus"></i> Tambah Kinerja
                </a>
                <a href="{{ route('hrm.performance.export_pdf', ['month' => $month]) }}" class="btn btn-success ml-2">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Posisi</th>
                            <th>Tanggal Penilaian</th>
                            <th>Kriteria</th>
                            <th>Nilai</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performances as $index => $performance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $performance->recruitment->name }}</td>
                            <td>{{ $performance->recruitment->position }}</td>
                            <td>{{ $performance->evaluation_date }}</td>
                            <td>{{ $performance->criteria }}</td>
                            <td>{{ $performance->score }}</td>
                            <td>{{ $performance->remarks }}</td>
                            <td>
                                <a href="{{ route('hrm.performance.edit', $performance->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('hrm.performance.destroy', $performance->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table thead th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Inisialisasi DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endpush
