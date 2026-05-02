<style>
    .equal-height {
        display: flex;
        flex-wrap: wrap;
    }
    .equal-height .col-md-4 {
        display: flex;
        margin-bottom: 15px;
    }
    .equal-height .panel {
        flex: 1;
        display: flex;
        flex-direction: column;
    }


</style>

@extends('app')

@section('title')
    Daftar User
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar User</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('user.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Akses Outlet</th>
                        <th>Hak Akses</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@php
    $modules = [
        'Dashboard', 
        'Outlet', 'Kategori', 'Satuan', 'Produk', 'Bahan', 'Inventori', 'Gudang',
        'Investor', 'Profit Management', 'Withdrawal Management',
        'Customer', 'Prospek', 'Tipe', 
        'Transaksi', 'Kontra Bon',
        'Pengeluaran', 'Pembelian', 'Penjualan', 'Invoice Penjualan', 
        'Hutang', 'Piutang', 'Akun dan Buku', 'RAB', 'Jurnal', 'Aktiva Tetap','Buku Besar', 'Neraca Lajur', 'Laba Rugi', 'Perubahan Modal', 'Neraca', 'Arus Kas', 'SPT Tahunan', 'Pelaporan Keuangan',
        'User', 'Pengaturan', 'Rekrutmen', 'Payroll', 'Kinerja', 'Pelatihan', 'Absensi',
        'Laporan Penjualan', 'Agen', 'Halaman Agen',
        'Supplier',
        'Produksi',
        'Ongkir Service', 'Mesin Customer', 'Invoice Service', 'History Service',
        'Laporan', 'Pengaturan COA',
        'Project Management',
    ];


    $aksesOptions = [];
    foreach ($modules as $module) {
        $aksesOptions[] = $module;
        $aksesOptions[] = "$module Create";
        $aksesOptions[] = "$module Edit";
        $aksesOptions[] = "$module Delete";
        $aksesOptions[] = "$module View";
    }
    
    $aksesOptions = array_unique($aksesOptions);
    $aksesKhususOptions = ['Tampilkan Profit', 'Tampilkan Omset'];
  
@endphp

@includeIf('user.form', ['aksesOptions' => $aksesOptions, 'aksesKhususOptions' => $aksesKhususOptions, 'modules' => $modules])
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('user.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'name'},
                {data: 'email'},
                {data: 'akses_outlet', render: function(data) {
                    return Array.isArray(data) ? data.join(', ') : 'Tidak ada akses outlet';
                }},
                {data: 'akses', render: function(data) {
                    return Array.isArray(data) ? data.join(', ') : 'Tidak ada akses'; // Cek apakah data adalah array
                }},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
        });

        $('.akses-checkbox').change(function() {
            $(this).closest('.checkbox').find('.crud-access').toggle(this.checked);
        });
        
        // Show CRUD for already checked access
        $('.akses-checkbox:checked').each(function() {
            $(this).closest('.checkbox').find('.crud-access').show();
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah User');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=name]').focus();

        $('#password, #password_confirmation').attr('required', true);
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit User');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=name]').focus();

        $('#password, #password_confirmation').attr('required', false);

        $.get(url)
            .done((response) => {
                $('#modal-form [name=name]').val(response.name);
                $('#modal-form [name=email]').val(response.email);

                // Reset checkbox
                $('.akses-checkbox').prop('checked', false);
                $('.akses-khusus-checkbox').prop('checked', false);
                $('.akses-outlet-checkbox').prop('checked', false);

                if (response.akses && Array.isArray(response.akses)) {
                    response.akses.forEach(function(akses) {
                        $('.akses-checkbox[value="' + akses + '"]').prop('checked', true);
                        //console.log(akses);
                    });
                }

                if (response.akses_khusus && Array.isArray(response.akses_khusus)) {
                    response.akses_khusus.forEach(function(akses_khusus) {
                        $('.akses-khusus-checkbox[value="' + akses_khusus + '"]').prop('checked', true);
                        console.log(akses_khusus);
                    });
                }
                

                if (response.akses_outlet && Array.isArray(response.akses_outlet)) {
                    response.akses_outlet.forEach(function(akses_outlet) {
                        $('.akses-outlet-checkbox[value="' + akses_outlet + '"]').prop('checked', true);
                        //console.log(akses_outlet);
                    });
                }

                if (response.is_agen) {
                    $('#is_agen').prop('checked', true).trigger('change');
                    $('#id_agen').val(response.id_agen);
                } else {
                    $('#is_agen').prop('checked', false).trigger('change');
                }
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    document.querySelectorAll('.module-select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const module = this.dataset.module;
            document.querySelectorAll(`.${module}-checkbox`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    $('#is_agen').on('change', function() {
        if (this.checked) {
            $('#id_agen').prop('required', true);
            $('#id_agen').closest('.form-group').show();
        } else {
            $('#id_agen').prop('required', false);
            $('#id_agen').closest('.form-group').hide();
        }
    });
</script>
@endpush
