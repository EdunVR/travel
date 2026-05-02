@extends('app')

@section('title')
Supplier
@endsection

@section('breadcrumb')
@parent
<li class="active">Supplier</li>
@endsection

@section('content')
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                @if($outlets->count() > 1)
                <div class="form-group">
                    <label for="id_outlet">Pilih Outlet</label>
                    <select name="id_outlet" id="id_outlet" class="form-control">
                        <option value="">Semua Outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button onclick="addForm('{{ route('supplier.store') }}')"
                    class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Outlet</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Hutang ke Supplier</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('supplier.form')
    @endsection

    @push('scripts')
        <script>
            let table;
            $(function () {
                table = $('.table').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('supplier.data') }}',
                        data: function (d) {
                            d.id_outlet = $('#id_outlet').val();
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'nama'},
                        {data: 'nama_outlet'},
                        {data: 'telepon'},
                        {data: 'alamat'},
                        {data: 'hutang'},
                        {
                            data: 'aksi',
                            name: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]

                });

                $('#id_outlet').on('change', function () {
                    table.ajax.reload();
                });

                $('#modal-form form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                            .done((response) => {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();
                            })
                            .fail((errors) => {
                                console.log(errors);
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })
            });

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Supplier');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Supplier');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama]').val(response.nama);
                        $('#modal-form [name=telepon]').val(response.telepon);
                        $('#modal-form [name=alamat]').val(response.alamat);
                        $('#modal-form [name=email]').val(response.email);
                        $('#modal-form [name=bank]').val(response.bank);
                        $('#modal-form [name=no_rekening]').val(response.no_rekening);
                        $('#modal-form [name=atas_nama]').val(response.atas_nama);
                        $('#modal-form [name=id_outlet]').val(response.id_outlet);
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menampilkan data');
                        return;
                    })
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }
        </script>
    @endpush
