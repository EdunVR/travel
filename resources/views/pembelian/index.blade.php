@extends('app')

@section('title')
Pembelian Bahan
@endsection

@section('breadcrumb')
@parent
<li class="active">Pembelian Bahan</li>
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
                <button onclick="addForm()" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i>Transaksi Baru</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-pembelian">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Supplier</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Diskon</th>
                        <th>Total Bayar</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('pembelian.supplier')
    @includeIf('pembelian.detail')
        @endsection

        @push('scripts')
            <script>
                let table, table1;
                $(function () {
                    table = $('.table-pembelian').DataTable({
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        autoWidth: false,
                        ajax: {
                            url: '{{ route('pembelian.data') }}',
                            data: function (d) {
                                d.id_outlet = $('#id_outlet').val();
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                searchable: false,
                                sortable: false
                            },
                            {
                                data: 'tanggal'
                            },
                            { data: 'nama_outlet' }, 
                            {
                                data: 'supplier'
                            },
                            {
                                data: 'total_item'
                            },
                            {
                                data: 'total_harga'
                            },
                            {
                                data: 'diskon'
                            },
                            {
                                data: 'bayar'
                            },
                            {
                                data: 'aksi',
                                searchable: false,
                                sortable: false
                            },
                        ]
                    });

                    $('.table-supplier').DataTable();
                    table1 = $('.table-detail').DataTable({
                        processing: true,
                        bSort: false,
                        dom: 'Brt',
                        columns: [{
                                data: 'DT_RowIndex',
                                searchable: false,
                                sortable: false
                            },
                            {
                                data: 'nama_bahan'
                            },
                            {
                                data: 'harga_beli'
                            },
                            {
                                data: 'jumlah'
                            },
                            {
                                data: 'subtotal'
                            },
                        ]
                    })

                    $('#id_outlet').on('change', function () {
                        table.ajax.reload();
                        var id_outlet = $(this).val();
                        $.ajax({
                            url: '{{ route('pembelian.index') }}',
                            type: 'GET',
                            data: {
                                id_outlet: id_outlet
                            },
                            success: function(response) {
                                $('#modal-supplier').html($(response).find('#modal-supplier').html());
                            },
                            error: function(xhr, status, error) {
                                console.log("Gagal memuat data member:", error);
                            }
                        });
                    });


                });

                function addForm() {
                    $('#modal-supplier').modal('show');
                }

                function showDetail(url) {
                    $('#modal-detail').modal('show');

                    table1.ajax.url(url);
                    table1.ajax.reload();
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
                                console.log(errors);
                                alert('Tidak dapat menghapus data');
                                return;
                            })
                    }
                }
            </script>
        @endpush
