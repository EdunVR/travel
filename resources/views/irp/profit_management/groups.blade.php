@extends('app')

@section('title', 'Manajemen Bagi Hasil Kelompok')

@section('content')
<style>
    .card-placeholder {
        height: 200px;
        border: 2px dashed #ccc;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .card-placeholder:hover {
        border-color: #4e73df;
        background-color: #f8f9fa;
    }
    .card-placeholder i {
        font-size: 3rem;
        color: #6c757d;
    }
    .group-card {
        border-radius: 25px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
        margin-bottom: 20px;
    }
    .group-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .group-card-header {
        border-top-left-radius: 25px !important;
        border-top-right-radius: 25px !important;
        padding: 15px 20px;
    }
    .group-card-body {
        padding: 20px;
    }
    .investor-table th {
        background-color: #f8f9fa;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen Bagi Hasil Kelompok</h6>
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createGroupModal">
                <i class="fas fa-plus"></i> Tambah Kelompok
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                @if($groups->isEmpty())
                    <div class="col-md-4">
                        <div class="card-placeholder" data-toggle="modal" data-target="#createGroupModal">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                @else
                    @foreach($groups as $group)
                        <div class="col-md-4">
                            <div class="card group-card">
                                <div class="card-header group-card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ $group->name }}</h5>
                                </div>
                                <div class="card-body group-card-body">
                                    <p>{{ $group->description ?? 'Tidak ada deskripsi' }}</p>
                                    
                                    @if($group->product)
                                        <p><strong>Produk:</strong> {{ $group->product->nama_produk }}</p>
                                    @endif
                                    
                                    @if($group->total_quota)
                                        <p><strong>Total Kuota:</strong> {{ format_uang($group->total_quota) }}</p>
                                    @endif
                                    
                                    <p><strong>Total Investasi:</strong> {{ format_uang($group->total_investment) }}</p>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-info">Detail</button>
                                        <button class="btn btn-sm btn-warning">Edit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Group -->
<div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('irp.profit-management.store-group') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">Tambah Kelompok Bagi Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelompok*</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Pilih Produk (Opsional)</label>
                        <select name="product_id" class="form-control" id="productSelect">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id_produk }}" data-price="{{ $product->harga_jual }}">
                                    {{ $product->nama_produk }} ({{ format_uang($product->harga_jual) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Kuota (Opsional)</label>
                        <input type="number" name="total_quota" class="form-control" id="totalQuota">
                    </div>
                    
                    <div class="form-group">
                        <label>Tambahkan Investor (Opsional)</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="investorTable">
                                <thead>
                                    <tr>
                                        <th>Nama Investor</th>
                                        <th>Jumlah Investasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="investorTableBody">
                                    <!-- Rows will be added dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <button type="button" class="btn btn-sm btn-primary" id="addInvestorBtn">
                                                <i class="fas fa-plus"></i> Tambah Investor
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Investor Select Template -->
<template id="investorRowTemplate">
    <tr>
        <td>
            <select name="investors[][id]" class="form-control investor-select" required>
                <option value="">-- Pilih Investor --</option>
                @foreach($investors as $investor)
                    <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="investors[][amount]" class="form-control investment-amount" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-investor-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto fill total quota when product is selected
    $('#productSelect').change(function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        if (price) {
            $('#totalQuota').val(price);
        }
    });

    // Add investor row
    $('#addInvestorBtn').click(function() {
        const template = $('#investorRowTemplate').html();
        const newRow = $(template);
        $('#investorTableBody').append(newRow);
    });

    // Remove investor row
    $(document).on('click', '.remove-investor-btn', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endpush
