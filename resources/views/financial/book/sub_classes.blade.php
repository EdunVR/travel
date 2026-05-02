<style>
    .btn-group-sm .btn svg {
        width: 16px;
        height: 16px;
    }
    
    .btn-group-sm .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
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

@section('title', 'Kelola Subclass')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i data-feather="layers"></i> Daftar Subclass
            </h6>
            <a href="{{ route('financial.book.create_sub_class') }}" class="btn btn-primary btn-sm">
                <i data-feather="plus"></i> Tambah Subclass
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="subClassesTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Kode</th>
                            <th width="25%">Nama Subclass</th>
                            <th width="30%">Buku Terkait</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subClasses as $index => $subClass)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $subClass->code }}</td>
                            <td>{{ $subClass->name }}</td>
                            <td>{{ $subClass->accountingBook->name ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('financial.book.edit_sub_class', $subClass->id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form action="{{ route('financial.book.delete_sub_class', $subClass->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <a type="submit" class="btn btn-danger" 
                                                title="Hapus" onclick="return confirm('Hapus subclass ini?')">
                                            <i data-feather="trash-2"></i>
                                        </a>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#subClassesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
        
        feather.replace();
    });
</script>
@endpush
