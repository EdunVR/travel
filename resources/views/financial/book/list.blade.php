<style>
    .badge {
        font-weight: 600;
        padding: 0.35em 0.65em;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
    }

    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
    .btn i {
        width: 16px;
        height: 16px;
        vertical-align: middle;
        margin-right: 3px;
    }
    .swal2-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .swal2-loading .swal2-spacer {
        height: 1em;
    }

    .swal2-loading .swal2-progress-steps {
        display: none;
    }
    
</style>

@extends('app')

@section('title', 'Daftar Buku Akuntansi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i data-feather="book" class="mr-2"></i>Daftar Buku Akuntansi
            </h6>
            <div>
                <a href="{{ route('financial.book.accounts') }}" class="btn btn-primary btn-sm mr-2">
                    <i data-feather="book-open"></i> Akun Buku
                </a>
                <a href="{{ route('financial.book.sub_classes') }}" class="btn btn-info btn-sm mr-2">
                    <i data-feather="layers"></i> Kelola Subclass
                </a>
                <a href="{{ route('financial.book.create') }}" class="btn btn-success btn-sm">
                    <i data-feather="plus"></i> Tambah Buku Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="booksTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Buku</th>
                            <th width="15%">Periode</th>
                            <th width="10%">Mata Uang</th>
                            <th width="10%">Status</th>
                            <th width="15%">Dibuat Oleh</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $index => $book)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $book->name }}</td>
                            <td>
                                {{ $book->start_date->format('d M Y') }} - 
                                {{ $book->end_date->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                @if($book->currency == 'IDR')
                                    <span class="badge badge-success">IDR</span>
                                @else
                                    <span class="badge badge-info">USD</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($book->status == 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($book->status == 'active')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tutup</span>
                                @endif
                            </td>
                            <td>{{ $book->creator->name }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('financial.book.opening_balances', $book->id) }}" 
                                    class="btn btn-info" title="Saldo Awal">
                                        <i data-feather="dollar-sign" width="16"></i>
                                    </a>
                                    @if($book->status == 'draft')
                                        <button class="btn btn-sm btn-primary edit-book-btn" data-book-id="{{ $book->id }}" title="Edit">
                                            <i data-feather="edit" width="16"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-book-btn" 
                                                data-book-id="{{ $book->id }}" 
                                                data-book-name="{{ $book->name }}"
                                                title="Hapus">
                                                <i data-feather="trash-2" width="16"></i>
                                        </button>
                                    @endif
                                   
                                    @if($book->status === 'active')
                                        <button class="btn btn-sm btn-danger close-book-btn" data-book-id="{{ $book->id }}">
                                        <i data-feather="lock" width="16"></i> Tutup Buku
                                        </button>
                                    @endif
                                    @if($book->status === 'closed')
                                        <a href="{{ route('financial.book.backup', $book->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-download"></i> Backup
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data buku</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
    $(document).ready(function() {
        $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
            $('#editBookModal').modal('hide');
        });
        $('.close-book-btn').click(function(e) {
            e.preventDefault();
            const bookId = $(this).data('book-id');
            console.log('Tombol tutup buku diklik untuk bookId:', bookId);
            
            Swal.fire({
                title: 'Memeriksa...',
                html: 'Sedang memverifikasi kelayakan buku untuk ditutup',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Cek apakah buku bisa ditutup
            $.ajax({
                url: "{{ route('financial.book.close_confirmation', '') }}/" + bookId,
                type: 'GET',
                success: function(response) {
                    Swal.close();
                    // Tampilkan modal konfirmasi
                    $('body').append(response);
                    $('#closeBookModal').modal('show');
                    
                    // Handle konfirmasi di modal
                    $('#confirmCloseBtn').click(function() {
                        const password = $('#password').val();
                        
                        if (!password) {
                            showErrorAlert('Password harus diisi');
                            return;
                        }
                        
                        Swal.fire({
                            title: 'Konfirmasi Akhir',
                            text: 'Anda yakin ingin menutup buku ini? Proses ini tidak dapat dibatalkan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Tutup Sekarang!',
                            cancelButtonText: 'Batalkan'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processBookClosing(bookId, password);
                            }
                        });
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    showErrorAlert(xhr.responseJSON?.error || 'Terjadi kesalahan saat memeriksa buku');
                }
            });
        });
        
        
        // Fungsi untuk proses tutup buku
        function processBookClosing(bookId, password) {
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang menutup buku dan membuat laporan akhir',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "{{ route('financial.book.close', '') }}/" + bookId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    password: password
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: response.message,
                        showConfirmButton: true,
                        confirmButtonText: 'Lihat Daftar Buku',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    showErrorAlert(xhr.responseJSON?.error || 'Terjadi kesalahan saat menutup buku');
                }
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: message,
                confirmButtonText: 'Mengerti',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
        }

        $('.edit-book-btn').click(function() {
            const bookId = $(this).data('book-id');
            
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "{{ route('financial.book.edit', '') }}/" + bookId,
                type: 'GET',
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        $('body').append(response.html);
                        $('#editBookModal').modal('show');
                        
                        // Handle form submit
                        $('#editBookForm').submit(function(e) {
                            e.preventDefault();
                            
                            Swal.fire({
                                title: 'Konfirmasi',
                                text: 'Anda yakin ingin menyimpan perubahan?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Simpan!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    updateBook(bookId);
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data buku'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memuat data buku'
                    });
                }
            });
        });
        
        // Fungsi untuk update buku
        function updateBook(bookId) {
            const formData = $('#editBookForm').serialize();
            
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "{{ route('financial.book.update', '') }}/" + bookId,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#editBookModal').modal('hide');
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan saat menyimpan perubahan';
                    
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

        $(document).on('click', '.delete-book-btn', function() {
            const bookId = $(this).data('book-id');
            const bookName = $(this).data('book-name');
            
            Swal.fire({
                title: 'Hapus Buku?',
                html: `Anda yakin ingin menghapus buku <strong>${bookName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`{{ route('financial.book.delete', '') }}/${bookId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.value.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: result.value.message
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
