<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    asset-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    /* Style untuk tombol hapus selected */
    #deleteSelectedBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

@extends('app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="package"></i> Daftar Aktiva Tetap
            </h5>
            <div>
                <button id="openCreateModal" class="btn btn-success btn-sm">
                    <i data-feather="plus"></i> Tambah Aktiva Tetap
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <div class="bg-light p-3 rounded mb-3">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="book_id" class="form-label">Tahun Buku</label>
                            <select name="book_id" id="book_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }} ({{ $book->start_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="asset_type" class="form-label">Jenis Aktiva</label>
                            <select name="asset_type" id="asset_type" class="form-select">
                                <option value="">Semua</option>
                                <option value="tangible" {{ request('asset_type') == 'tangible' ? 'selected' : '' }}>Berwujud</option>
                                <option value="intangible" {{ request('asset_type') == 'intangible' ? 'selected' : '' }}>Tidak Berwujud</option>
                                <option value="building" {{ request('asset_type') == 'building' ? 'selected' : '' }}>Bangunan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Pencarian</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nama/Kode Aktiva">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" id="resetFilter" class="btn btn-sm btn-secondary">
                                <i data-feather="refresh-ccw"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th width="50">Foto</th>
                            <th width="50">No</th>
                            <th>Kode Aktiva</th>
                            <th>Nama Aktiva</th>
                            <th>Jenis</th>
                            <th>Kelompok</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Nilai Residu</th>
                            <th>Tanggal Perolehan</th>
                            <th>Umur</th>
                            <th>Penyusutan/Bulan</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($assets as $asset)
                        @php
                            $residualValue = $asset->total_cost * 0.1;
                            $monthlyDepreciation = ($asset->total_cost - $residualValue) / $asset->useful_life / 12;
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}">
                            </td>
                            <td>
                                @if($asset->photo_path)
                                <img src="{{ asset('storage/' . $asset->photo_path) }}" alt="{{ $asset->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                <div class="text-center text-muted" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i data-feather="image"></i>
                                </div>
                                @endif
                            </td>
                            <td>{{ $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() }}</td>
                            <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>
                                @if($asset->asset_type == 'tangible')
                                    <span class="badge bg-primary">Berwujud</span>
                                @elseif($asset->asset_type == 'intangible')
                                    <span class="badge bg-info">Tidak Berwujud</span>
                                @else
                                    <span class="badge bg-secondary">Bangunan</span>
                                @endif
                            </td>
                            <td>{{ $asset->asset_group }}</td>
                            <td>{{ $asset->quantity }} {{ $asset->unit }}</td>
                            <td class="text-end">{{ number_format($asset->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($asset->total_cost, 2) }}</td>
                            <td class="text-end">{{ number_format($residualValue, 2) }}</td>
                            <td>{{ $asset->acquisition_date->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $asset->useful_life }} tahun</td>
                            <td class="text-end">{{ number_format($monthlyDepreciation, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-primary edit-btn" data-id="{{ $asset->id }}" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                    <button class="btn btn-info generate-btn" data-id="{{ $asset->id }}" title="Generate Depresiasi">
                                        <i data-feather="refresh-cw"></i>
                                    </button>
                                    <button class="btn btn-danger delete-btn" data-id="{{ $asset->id }}" title="Hapus">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between mt-3">
                <div>
                    <button id="deleteSelectedBtn" class="btn btn-danger btn-sm" disabled>
                        <i data-feather="trash-2"></i> Hapus yang Dipilih
                    </button>
                </div>
                <div>
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="assetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i data-feather="plus-circle"></i> <span id="modalAction">Tambah</span> Aktiva Tetap
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assetForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="assetId" name="id">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="accounting_book_id" class="form-label">Tahun Buku *</label>
                            <select name="accounting_book_id" id="accounting_book_id" class="form-select" required>
                                <option value="">Pilih Tahun Buku</option>
                                @foreach($booksActive as $book)
                                    <option value="{{ $book->id }}">{{ $book->name }} ({{ $book->start_date->format('Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="asset_code" class="form-label">Kode Aktiva *</label>
                            <input type="text" name="asset_code" id="asset_code" class="form-control" readonly required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="name" class="form-label">Nama Aktiva *</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="asset_type" class="form-label">Jenis Aktiva *</label>
                            <select name="asset_type" id="asset_type" class="form-select" required>
                                <option value="tangible">Berwujud</option>
                                <option value="intangible">Tidak Berwujud</option>
                                <option value="building">Bangunan</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="asset_group" class="form-label">Kelompok Aktiva *</label>
                            <input type="text" name="asset_group" id="asset_group" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="photo" class="form-label">Foto Aktiva</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                            <div id="photoPreview" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="quantity" class="form-label">Jumlah *</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" step="1" value="1" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="unit" class="form-label">Satuan *</label>
                            <input type="text" name="unit" id="unit" class="form-control" value="Unit" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="unit_price" class="form-label">Harga Satuan *</label>
                            <input type="number" name="unit_price" id="unit_price" class="form-control" min="0" step="0.01" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="acquisition_date" class="form-label">Tanggal Perolehan *</label>
                            <input type="date" name="acquisition_date" id="acquisition_date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="total_cost" class="form-label">Total Biaya</label>
                            <input type="text" name="total_cost" id="total_cost" class="form-control" readonly>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="useful_life" class="form-label">Umur Ekonomis (tahun) *</label>
                            <input type="number" name="useful_life" id="useful_life" class="form-control" min="1" step="1" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="salvage_value" class="form-label">Nilai Residu (10% dari Total)</label>
                            <input type="text" name="salvage_value" id="salvage_value" class="form-control" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i data-feather="x"></i> Batal
                </button>
                <button type="button" id="saveAssetBtn" class="btn btn-success">
                    <i data-feather="save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    feather.replace();
    
    let isEditMode = false;
    let currentAssetId = null;
    baseUrl = window.baseUrl;

    $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
        $('#assetModal').modal('hide');
    });

    // Open create modal
    $('#openCreateModal').click(function() {
        isEditMode = false;
        currentAssetId = null;
        
        $('#modalTitle span').text('Tambah');
        $('#assetForm')[0].reset();
        $('#assetId').val('');
        $('#photoPreview').html('');
        calculateCosts();
        
        $('#assetModal').modal('show');
    });

    // Open edit modal
    $(document).on('click', '.edit-btn', function() {
        const assetId = $(this).data('id');
        isEditMode = true;
        currentAssetId = assetId;
        
        $('#modalTitle span').text('Edit');
        $('#assetForm')[0].reset();
        $('#photoPreview').html('');
        
        $.get(`${baseUrl}/financial/fixed-asset/${assetId}/edit`, function(response) {
            console.log('[DEBUG] Edit response:', response);
            console.log('[DEBUG] jenis aset:', response.asset_type);
            
            // Isi form
            $('#assetId').val(response.id);
            $('#accounting_book_id').val(response.accounting_book_id);
            $('#asset_code').val(response.asset_code);
            $('#name').val(response.name);
            $('#asset_type').val(response.asset_type)
            console.log('Set asset_type ke:', $('#asset_type').val());

            $('#asset_group').val(response.asset_group);
            $('#quantity').val(response.quantity);
            $('#unit').val(response.unit);
            $('#unit_price').val(response.unit_price);
            $('#acquisition_date').val(response.acquisition_date);
            $('#useful_life').val(response.useful_life);
            calculateCosts();
            
            // Tampilkan foto jika ada
            if (response.photo_path) {
                $('#photoPreview').html(`
                    <img src="${baseUrl}/storage/${response.photo_path}" class="img-thumbnail" style="max-width: 200px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removePhoto">
                        <i data-feather="x"></i> Hapus Foto
                    </button>
                `);
                feather.replace();
            }
            
            $('#assetModal').modal('show');
        }).fail(function(xhr) {
            console.error('Failed to load asset data:', xhr);
            Swal.fire('Error!', 'Gagal memuat data aktiva', 'error');
        });
    });

    $('#saveAssetBtn').click(function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();

        const url = isEditMode 
            ? `${baseUrl}/financial/fixed-asset/${currentAssetId}`
            : `${baseUrl}/financial/fixed-asset`;
        
        const method = isEditMode ? 'PUT' : 'POST';

        // Buat FormData
        const formData = new FormData(document.getElementById('assetForm'));
        
        // Untuk method PUT, tambahkan _method
        if (isEditMode) {
            formData.append('_method', 'PUT');
        }

        console.log('Submitting to:', url);
        console.log('Method:', method);
        console.log('FormData:', Object.fromEntries(formData.entries()));

        $.ajax({
            url: url,
            type: 'POST', // Tetap POST karena kita menggunakan _method untuk PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Save successful:', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.redirect;
                });
            },
            error: function(xhr) {
                console.error('Save failed:', xhr.responseJSON);
                let message = 'Terjadi kesalahan saat menyimpan aktiva';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    // Tampilkan error di masing-masing field
                    Object.keys(errors).forEach(field => {
                        const $field = $(`#${field}`);
                        $field.addClass('is-invalid');
                        const $feedback = $field.next('.invalid-feedback');
                        if ($feedback.length) {
                            $feedback.text(errors[field][0]).show();
                        } else {
                            $field.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                        }
                    });
                    
                    message = 'Harap perbaiki error di form';
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: `${message}<br><small>Lihat error di form</small>`,
                    scrollbarPadding: false
                });
            }
        });
    });


    // Generate depreciation
    $(document).on('click', '.generate-btn', function() {
        const assetId = $(this).data('id');
        
        Swal.fire({
            title: 'Generate Depresiasi?',
            text: "Anda yakin ingin generate depresiasi untuk aktiva ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`${baseUrl}/financial/fixed-asset/${assetId}/generate-depreciation`, {
                    _token: '{{ csrf_token() }}',
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    }
                }).fail(function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                });
            }
        });
    });

    // Delete asset
    $('#deleteSelectedBtn').click(function() {
        const selectedIds = $('.asset-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu aktiva', 'info');
            return;
        }

        Swal.fire({
            title: 'Hapus Aktiva Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} aktiva tetap`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("financial.fixed-asset.delete-selected") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan saat menghapus';
                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });

    // Select All Functionality
    $('#selectAllCheckbox').change(function() {
        $('.asset-checkbox').prop('checked', $(this).prop('checked'));
        toggleDeleteSelectedBtn();
    });

    // Toggle checkbox individual
    $(document).on('change', '.asset-checkbox', function() {
        if ($('.asset-checkbox:checked').length === $('.asset-checkbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
        toggleDeleteSelectedBtn();
    });

    // Fungsi toggle tombol delete selected
    function toggleDeleteSelectedBtn() {
        const checkedCount = $('.asset-checkbox:checked').length;
        const $deleteBtn = $('#deleteSelectedBtn');
        
        $deleteBtn.prop('disabled', checkedCount === 0);
        $deleteBtn.html(`<i data-feather="trash-2"></i> Hapus Dipilih (${checkedCount})`);
        feather.replace();
    }

    // Handle delete selected
    $('#deleteSelectedBtn').click(function() {
        const selectedIds = $('.asset-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu aktiva', 'info');
            return;
        }

        Swal.fire({
            title: 'Hapus Aktiva Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} aktiva`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("financial.fixed-asset.delete-selected") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const assetId = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data aktiva tetap akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/financial/fixed-asset/${assetId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Terhapus!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload(); // Refresh halaman setelah penghapusan
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus',
                            'error'
                        );
                    }
                });
            }
        });
    });
    
    // Generate kode aktiva otomatis
    function generateAssetCode() {
        const month = new Date().getMonth() + 1;
        const monthStr = month.toString().padStart(2, '0');
        
        $.get("{{ route('financial.fixed-asset.generate-code') }}", function(response) {
            $('#asset_code').val(`${monthStr}-${response.nextNumber.toString().padStart(4, '0')}`);
        });
    }

    function calculateCosts() {
        const quantity = parseFloat($('#quantity').val()) || 0;
        const unitPrice = parseFloat($('#unit_price').val()) || 0;
        const totalCost = quantity * unitPrice;
        const residualValue = totalCost * 0.1;
        
        $('#total_cost').val(formatNumber(totalCost));
        $('#salvage_value').val(formatNumber(residualValue));
    }

    // Saat modal dibuka
    $('#assetModal').on('shown.bs.modal', function() {
        if (!isEditMode) {
            $.get("{{ route('financial.fixed-asset.generate-code') }}", function(response) {
                $('#asset_code').val(response.asset_code);
            });
        }
    });

    // Quantity atau unit price berubah
    $('#quantity, #unit_price').on('input', calculateCosts);

    // Photo preview
    $('#photo').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreview').html(`
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removePhoto">
                        <i data-feather="x"></i> Hapus Foto
                    </button>
                `);
                feather.replace();
            }
            reader.readAsDataURL(file);
        }
    });

    $(document).on('click', '#removePhoto', function() {
        $('#photo').val('');
        $('#photoPreview').html('');
    });

    function formatNumber(number) {
        if (typeof number === 'string') {
            // Hapus semua karakter non-digit kecuali titik desimal
            number = number.replace(/[^\d.]/g, '');
            // Konversi ke float
            number = parseFloat(number) || 0;
        }
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi untuk mengembalikan ke format number asli (tanpa separator)
    function unformatNumber(formattedNumber) {
        return parseFloat(formattedNumber.toString().replace(/\./g, ''));
    }

    // Reset filter
    $('#resetFilter').click(function() {
        // Reset form filter
        $('#filterForm')[0].reset();
        
        // Redirect ke URL tanpa parameter filter
        const baseUrl = window.location.href.split('?')[0];
        window.location.href = baseUrl;
    });

    // Select All Functionality
    $('#selectAllCheckbox').change(function() {
        $('.asset-checkbox').prop('checked', $(this).prop('checked'));
        toggleDeleteSelectedBtn();
    });

    // Toggle checkbox individual
    $(document).on('change', '.asset-checkbox', function() {
        if ($('.asset-checkbox:checked').length === $('.asset-checkbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
        toggleDeleteSelectedBtn();
    });

    // Fungsi toggle tombol delete selected
    function toggleDeleteSelectedBtn() {
        const checkedCount = $('.asset-checkbox:checked').length;
        const $deleteBtn = $('#deleteSelectedBtn');
        
        $deleteBtn.prop('disabled', checkedCount === 0);
        $deleteBtn.html(`<i data-feather="trash-2"></i> Hapus Dipilih (${checkedCount})`);
        feather.replace();
    }

    // Handle delete selected
    $('#deleteSelectedBtn').click(function() {
        const selectedIds = $('.asset-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu aktiva', 'info');
            return;
        }

        Swal.fire({
            title: 'Hapus Aktiva Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} aktiva`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("financial.fixed-asset.delete-selected") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
