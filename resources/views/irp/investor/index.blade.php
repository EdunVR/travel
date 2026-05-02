<style>
    #investors-table_filter {
        margin-bottom: 15px;
    }
    .dataTables_filter input {
        margin-left: 0.5em;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        margin-bottom: 20px;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
    }
    .table th {
        white-space: nowrap;
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 85%;
        padding: 0.35em 0.65em;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .icon-sm {
        width: 16px;
        height: 16px;
    }
    .icon-md {
        width: 20px;
        height: 20px;
    }
    .icon-lg {
        width: 24px;
        height: 24px;
    }
    
    /* Spacing untuk ikon di tombol */
    .btn i {
        margin-right: 5px;
        vertical-align: middle;
    }
    .account-info {
        max-width: 250px;
        white-space: normal;
        word-break: break-word;
    }
    .account-item {
        margin-bottom: 5px;
        padding-bottom: 5px;
        border-bottom: 1px dashed #eee;
    }
    .account-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .account-info {
        max-width: 250px;
        white-space: normal;
        word-break: break-word;
    }
    .account-item {
        margin-bottom: 5px;
        padding-bottom: 5px;
        border-bottom: 1px dashed #eee;
    }
    .account-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .toggle-accounts [data-feather] {
        width: 14px;
        height: 14px;
        margin-right: 5px;
        vertical-align: middle;
    }
    .toggle-accounts {
        padding: 0;
        font-size: 0.8rem;
        color: #4e73df;
        display: inline-flex;
        align-items: center;
    }
    .toggle-accounts:hover {
        text-decoration: none;
        color: #2e59d9;
    }
</style>

@extends('app')

@section('title', 'Daftar Investor')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Investor</h6>
            <a href="{{ route('irp.investor.create') }}" class="btn btn-sm btn-primary">
                <i data-feather="plus" class="icon-sm"></i> Tambah Investor
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="investors-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Aksi</th>
                            <th>Tanggal Bergabung</th>
                            <th>Nama Investor</th>
                            <th>Kategori</th>
                            <th>Rekening Investasi</th>
                            <th>Total Investasi</th>
                            <th>Bank</th>
                            <th>Rekening</th>
                            <th>Atas Nama</th>
                            <th>Persentase</th>
                            <th>Total Bagi Hasil</th>
                            <th>Total Keseluruhan</th>
                            <th>Transfer ke Investor</th>
                            <th>Keuntungan Pengelola</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTables secara otomatis -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
    
    // Untuk konten yang dimuat secara dinamis
    $(document).on('shown.bs.modal', function () {
        feather.replace();
    });
$(document).ready(function() {
    
    var table = $('#investors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('irp.investor.index') }}",
            type: "GET"
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                width: '5%'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '12%'
            },
            {
                data: 'join_date',
                name: 'join_date',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'category',
                name: 'category',
                render: function(data, type, row) {
                    var badgeClass = '';
                    if (data === 'internal') badgeClass = 'badge-primary';
                    else if (data === 'syirkah') badgeClass = 'badge-success';
                    else if (data === 'investama') badgeClass = 'badge-info';
                    else badgeClass = 'badge-secondary';
                    
                    return '<span class="badge ' + badgeClass + '">' + 
                        data.charAt(0).toUpperCase() + data.slice(1) +
                        '</span>';
                }
            },
            {
                data: 'accounts_info',
                name: 'accounts_info',
                orderable: false,
                searchable: false
            },
            {
                data: 'total_investment',
                name: 'total_investment',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            },
            {
                data: 'bank',
                name: 'bank',
                className: 'text-center'
            },
            {
                data: 'rekening',
                name: 'rekening',
                className: 'text-center'
            },
            {
                data: 'atas_nama',
                name: 'atas_nama',
                className: 'text-center'
            },
            {
                data: 'average_percentage',
                name: 'average_percentage',
                render: function(data) {
                    // Handle null/undefined cases
                    if (data === null || data === undefined) {
                        return '0%';
                    }
                    return parseFloat(data).toFixed(2) + '%';
                }
            },
            {
                data: 'total_profit',
                name: 'total_profit',
                render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            },
            {
                data: 'total_keseluruhan',
                name: 'total_keseluruhan',
                render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            },
            {
                data: 'total_transfer',
                name: 'total_transfer',
                render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            },
            {
                data: 'keuntungan_pengelola',
                name: 'keuntungan_pengelola',
                render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }
            
        ],
        order: [[1, 'asc']],
        language: {
            processing: "Memproses...",
            search: "_INPUT_",
            searchPlaceholder: "Cari...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            infoPostFix: "",
            loadingRecords: "Memuat data...",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data tersedia",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            },
            aria: {
                sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                sortDescending: ": aktifkan untuk mengurutkan kolom turun"
            }
        },
        initComplete: function() {
            // Tambahkan custom search input
            $('.dataTables_filter').html(`
                <div class="input-group mb-3">
                    <input type="text" id="custom-search" class="form-control" placeholder="Cari investor..." aria-label="Cari investor">
                </div>
            `);
            
            // Implementasi pencarian realtime
            $('#custom-search').keyup(function() {
                table.search($(this).val()).draw();
            });
        }
    });

    table.on('draw', function() {
        feather.replace();
    });

    // // Reload table setiap 30 detik untuk update data realtime
    // setInterval(function() {
    //     table.ajax.reload(null, false);
    // }, 30000);

    $('#investors-table').on('click', '.toggle-accounts', function() {
        const investorId = $(this).data('investor');
        const moreAccountsDiv = $(this).prev('.more-accounts');
        const icon = $(this).find('[data-feather]');
        const iconName = moreAccountsDiv.is(':visible') ? 'chevron-down' : 'chevron-up';
        
        if (moreAccountsDiv.is(':visible')) {
            moreAccountsDiv.slideUp();
            $(this).html('<i data-feather="chevron-down" class="icon-sm"></i> Lihat ' + moreAccountsDiv.find('.account-item').length + ' rekening lainnya');
        } else {
            moreAccountsDiv.slideDown();
            $(this).html('<i data-feather="chevron-up" class="icon-sm"></i> Sembunyikan');
        }
        
        // Replace feather icon
        feather.replace();
    });
});
</script>
@endpush
