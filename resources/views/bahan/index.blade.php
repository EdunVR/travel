@extends('app')

@section('title')
Bahan
@endsection

@section('breadcrumb')
@parent
<li class="active">Bahan</li>
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
                <div class="btn-group">
                    <button onclick="addForm('{{ route('bahan.store') }}')"
                        class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah Bahan</button>
                    <button onclick="deleteSelected('{{ route('bahan.delete_selected') }}')"
                        class="btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i>Hapus Terpilih</button>
                </div>

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-bahan">
                    <thead>
                        <th width="5%">
                            <input type="checkbox" name="select-all" id="select-all">
                        </th>
                        <th width="5%">No</th>
                        <th>Outlet</th>
                        <th>Nama Bahan</th>
                        <th>Merk</th>
                        <th>Stok Total</th>
                        <th>Satuan</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('bahan.detail')
@includeif('bahan.form')
@includeif('bahan.form_harga')
    @endsection

    @push('scripts')
        <script>
            let table, table1;
            $(function () {
                table = $('.table-bahan').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('bahan.data') }}',
                        data: function (d) {
                            d.id_outlet = $('#id_outlet').val();
                        }
                    },
                    columns: [
                        {
                            data: 'select-all',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {data: 'nama_outlet'},
                        {
                            data: 'nama_bahan'
                        },
                        {
                            data: 'merk'
                        },
                        {
                            data: 'stok'
                        },
                        {
                            data: 'nama_satuan'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]

                });

                table1 = $('.table-detail').DataTable({
                    processing: true,
                    bSort: false,
                    dom: 'Brt',
                    destroy: true,
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'harga_beli'},
                        {data: 'stok'},
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

                $('#modal-formx form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault(); 
                        $.post($('#modal-formx form').attr('action'), $('#modal-formx form').serialize())
                            .done((response) => {
                                $('#modal-formx').modal('hide');
                                table.ajax.reload();
                            })
                            .fail((errors) => {
                                console.log('Errornya adalah:', errors);
                                alert('Tidak dapat menambahkan bahan OY');
                                return;
                            });
                    }
                })

                $('#modal-form-harga form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-form-harga form').attr('action'), $('#modal-form-harga form').serialize())
                            .done((response) => {
                                $('#modal-form-harga').modal('hide');
                                table1.ajax.reload();
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })

                $('#select-all').on('click', function (e) {
                    if ($(this).is(':checked')) {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', true);
                    } else {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', false);
                    }
                })
            });

            function showDetail(url) {
                $('#modal-detail').modal('show');
                table1.ajax.url(url);
                table1.ajax.reload();
            }

            function addForm(url) {
                $('#modal-formx').modal('show');
                $('#modal-formx .modal-title').text('Tambah Bahan');
                $('#modal-formx form')[0].reset();
                $('#modal-formx form').attr('action', url);
                $('#modal-formx [name=_method]').val('post'); // Set method POST
                $('#modal-formx [name=nama_bahan]').focus();
            }

            function editForm(url) {
                $('#modal-formx').modal('show');
                $('#modal-formx .modal-title').text('Edit Bahan');
                $('#modal-formx form')[0].reset();
                $('#modal-formx form').attr('action', url);
                $('#modal-formx [name=_method]').val('put'); // Set method PUT
                $('#modal-formx [name=nama_bahan]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-formx [name=nama_bahan]').val(response.nama_bahan);
                        $('#modal-formx [name=merk]').val(response.merk);
                        $('#modal-formx [name=id_satuan]').val(response.id_satuan);
                        $('#modal-formx [name=id_outlet]').val(response.id_outlet);

                        $('#modal-formx form').attr('action', "{{ url('bahan') }}/" + response.id_bahan);
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menampilkan data');
                    });
            }

            function editForm_harga(url) {
                $('#modal-form-harga').modal('show');
                $('#modal-form-harga .modal-title').text('Edit Harga dan Stok');
                
                // Reset nilai input dalam modal
                $('#modal-form-harga input').val('');
                $('#modal-form-harga [name=_method]').val('put');  // Set method PUT
                $('#modal-form-harga [name=harga_beli]').focus();  // Fokuskan elemen harga_bahan
                
                // Ambil data dari URL dan masukkan ke input modal
                $.get(url)
                    .done((response) => {
                        // Isi input modal dengan data yang diterima dari server
                        $('#modal-form-harga [name=harga_beli]').val(response.harga_beli);
                        $('#modal-form-harga [name=stok]').val(response.stok);
                        
                        // Handle pengiriman data menggunakan AJAX, tanpa form
                        $('#modal-form-harga .btn-primary').on('click', function(e) {
                            e.preventDefault();  // Prevent default form submission
                            
                            var dataX = {
                                _method: 'PUT',  // Menyatakan request adalah PUT
                                harga_beli: $('#modal-form-harga [name=harga_beli]').val(),
                                stok: $('#modal-form-harga [name=stok]').val()
                            };
                            
                            $.ajax({
                                url: "{{ url('bahan_harga') }}/" + response.id,
                                method: 'PUT',
                                data: dataX,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Menambahkan CSRF token ke header
                                },
                                success: function(response) {
                                    alert('Data berhasil disimpan');
                                    $('#modal-form-harga').modal('hide');
                                    table.ajax.reload();
                                    table1.ajax.reload();
                                },
                                error: function(errors) {
                                    console.log(errors);
                                    alert('Tidak dapat menyimpan data');
                                }
                            });
                        });
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menampilkan data');
                    });
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

            function deleteData_harga(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                            table1.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }

            function deleteSelected(url) {
                // Ambil semua checkbox yang dipilih
                let selectedIds = [];
                $('.table tbody input:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Tidak ada data yang dipilih!');
                    return;
                }

                if (confirm('Yakin ingin menghapus data terpilih?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete',
                            'id_bahan': selectedIds
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            console.log(errors);
                            alert('Tidak dapat menghapus data');
                        });
                }
            }
        </script>
    @endpush
