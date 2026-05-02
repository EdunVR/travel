@extends('app')

@section('title')
    Ongkos Kirim
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Ongkos Kirim</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('service.ongkos-kirim.store') }}')" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-plus-circle"></i> Tambah
                </button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered" id="table-ongkir">
                    <thead>
                        <th width="5%">No</th>
                        <th>Daerah</th>
                        <th>Harga</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('service_management.ongkos_kirim.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('#table-ongkir').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('service.ongkos-kirim.index') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'daerah'},
                {data: 'harga', render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Ongkos Kirim');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=daerah]').focus();
    }

    function editForm(url) {
        // Extract ID from URL untuk membuat route edit
        var id = url.split('/').pop();
        var editUrl = '{{ route("service.ongkos-kirim.edit", ":id") }}'.replace(':id', id);
        
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Ongkos Kirim');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=daerah]').focus();

        // Gunakan route edit yang baru
        $.get(editUrl)
            .done((response) => {
                $('#modal-form [name=daerah]').val(response.daerah);
                $('#modal-form [name=harga]').val(response.harga);
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
</script>
@endpush
