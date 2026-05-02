<style>
    /* Gaya untuk tabel akun */
    .table-accounts {
        
        width: 100%;
    }
    
    /* Header tabel */
    .table-accounts thead th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* Kolom kode akun */
    .account-code {
        text-align: left;
        padding-left: 15px;
        white-space: nowrap;
        vertical-align: middle;
        
    }
    
    /* Kolom nama akun */
    .account-name {
        text-align: left;
        padding-left: 10px;
       vertical-align: middle;
    }
    
    /* Indentasi hierarki */
    .level-0 .account-name { padding-left: 15px; }
    .level-1 .account-name { padding-left: 35px; }
    .level-2 .account-name { padding-left: 55px; }
    .level-3 .account-name { padding-left: 75px; }
    .level-4 .account-name { padding-left: 95px; }
    .level-5 .account-name { padding-left: 115px; }
    .level-6 .account-name { padding-left: 135px; }

    /* Indentasi hierarki */
    .level-0 .account-code { padding-left: 15px; }
    .level-1 .account-code { padding-left: 35px; }
    .level-2 .account-code { padding-left: 55px; }
    .level-3 .account-code { padding-left: 75px; }
    .level-4 .account-code { padding-left: 95px; }
    .level-5 .account-code { padding-left: 115px; }
    .level-6 .account-code { padding-left: 135px; }
    
    /* Warna background untuk level berbeda */
    .level-0 { background-color: white; }
    .level-1 { background-color: rgba(0,0,0,0.02); }
    .level-2 { background-color: rgba(0,0,0,0.04); }
    .level-3 { background-color: rgba(0,0,0,0.06); }
    .level-4 { background-color: rgba(0,0,0,0.08); }
    .level-5 { background-color: rgba(0,0,0,0.10); }
    .level-6 { background-color: rgba(0,0,0,0.12); }
    
    /* Gaya untuk badge */
    .badge {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-weight: 500;
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

@section('title', 'Daftar Akun Buku')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i data-feather="book-open" class="mr-2"></i>Daftar Akun Buku
            </h6>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addAccountModal">
                <i data-feather="plus"></i> Tambah Akun
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-accounts table-bordered table-hover" id="accountsTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 15%; text-align: left">Kode</th>
                            <th style="width: 30%; text-align: left">Nama Akun</th>
                            <th style="width: 15%; text-align: center">Tipe</th>
                            <th style="width: 15%; text-align: center">Status</th>
                            <th style="width: 25%; text-align: center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            @include('financial.book.account_row', [
                                'account' => $account, 
                                'level' => 0,
                                'parentAccounts' => $parentAccounts
                            ])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Child Account Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1" role="dialog" aria-labelledby="addChildModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChildModalLabel">Tambah Child Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addChildForm">
                <input type="hidden" id="parent_code" name="parent_code" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="child_code">Kode Akun</label>
                        <input type="text" class="form-control" id="child_code" name="code" required>
                        <small class="form-text text-muted">Kode akan otomatis ditambahkan setelah kode parent</small>
                    </div>
                    <div class="form-group">
                        <label for="child_name">Nama Akun</label>
                        <input type="text" class="form-control" id="child_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="child_type">Tipe Akun</label>
                        <select class="form-control" id="child_type" name="type" required>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    feather.replace();

    function formatAccountCode(code) {
        const parts = code.split('.');
        if (parts.length === 1) return code;
        
        return parts.map((part, index) => {
            if (index === parts.length - 1) {
                return part.padStart(2, '0');
            }
            return part;
        }).join('.');
    }

    // Terapkan format ke semua kode akun
    $('.account-code span').each(function() {
        const originalCode = $(this).text();
        const formattedCode = formatAccountCode(originalCode);
        $(this).text(formattedCode);
    });
    
    // Handle form submission
    $('#addAccountForm').submit(function(e) {
        e.preventDefault();
        submitAccountForm($(this).serialize(), "{{ route('financial.book.store_account') }}");
    });
    
    // Handle form submission for child account
    $('#addChildForm').submit(function(e) {
        e.preventDefault();
        submitAccountForm($(this).serialize(), "{{ route('financial.book.store_account') }}");
    });
    
    // Handle add child button click
    $(document).on('click', '.add-child-btn', function() {
        const parentCode = $(this).data('parent-code');
        const parentName = $(this).data('parent-name');
        
        $('#addChildModal #parent_code').val(parentCode);
        $('#addChildModal .modal-title').text(`Tambah Child untuk ${parentCode} - ${parentName}`);
        
        // Generate kode otomatis
        generateAccountCode(parentCode, '#addChildModal #child_code');
        
        $('#addChildModal').modal('show');
    });
    
    // Generic function to submit account form
    function submitAccountForm(formData, url) {
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menyimpan akun';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    }
    
    // Handle delete account
    $(document).on('click', '.delete-account-btn', function(e) {
        e.preventDefault();
        const code = $(this).data('code');
        const hasChildren = $(this).closest('tr').next('tr').hasClass('child-row');
        
        if (hasChildren) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak dapat menghapus',
                text: 'Akun ini memiliki child account. Hapus semua child terlebih dahulu.'
            });
            return;
        }
        
        Swal.fire({
            title: 'Hapus Akun?',
            text: "Anda yakin ingin menghapus akun ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('financial.book.delete_account', '') }}/" + code,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus akun'
                        });
                    }
                });
            }
        });
    });

    function generateAccountCode(parentCode, targetSelector) {
        if (!parentCode) {
            // Generate kode untuk parent (level 1)
            $.get("{{ route('financial.book.generate_code') }}", function(response) {
                $(targetSelector).val(response.code);
            }).fail(function() {
                $(targetSelector).val('');
            });
        } else {
            // Generate kode untuk child
            $.get("{{ route('financial.book.generate_code') }}", { 
                parent_code: parentCode 
            }, function(response) {
                $(targetSelector).val(response.code);
            }).fail(function() {
                $(targetSelector).val('');
            });
        }
    }

    $('#addAccountModal #parent_code').change(function() {
        const parentCode = $(this).val();
        generateAccountCode(parentCode, '#addAccountModal #code');
    });
});
</script>
@endpush
