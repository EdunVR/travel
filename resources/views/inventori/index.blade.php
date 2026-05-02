@extends('app')

@section('title', 'Inventori')

@section('breadcrumb')
@parent
<li class="active">Inventori</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
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
                    <button onclick="addForm('{{ route('inventori.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <!-- <button onclick="cetakLaporan('{{ route('inventori.cetak_laporan') }}')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-print"></i> Cetak Laporan</button> -->
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered table-inventori">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Outlet</th>
                        <th>Penanggung Jawab</th>
                        <th>Stok</th>
                        <th>Lokasi Penyimpanan</th>
                        <th>Status</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeif('inventori.form')
@includeif('inventori.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-inventori').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('inventori.data') }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_barang'},
                {data: 'nama_kategori'},
                {data: 'nama_outlet'},
                {data: 'penanggung_jawab'},
                {data: 'stok'},
                {data: 'lokasi'},
                {data: 'status'},
                {data: 'aksi', searchable: false, sortable: false}
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
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })
                $('#modal-detail form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-detail form').attr('action'), $('#modal-detail form').serialize())
                            .done((response) => {
                                table.ajax.reload();
                                table1.ajax.reload();
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })
        
    });

    function showDetail(url) {
        $('#modal-detail').modal('show');
        // Mengambil data inventori untuk menampilkan nama barang
        $.get(url, function(data) {
            if (data && data.nama_barang) {
                $('#nama-barang-title').text('Detail Barang: ' + data.nama_barang);
                $('#form-pinjam').attr('action', '{{ url('inventori') }}/' + data.id_inventori + '/pinjam');
            } else {
                console.error('Data tidak valid:', data);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
        });

        // Hancurkan DataTable jika sudah ada
        if ($.fn.DataTable.isDataTable('.table-detail')) {
            $('.table-detail').DataTable().destroy();
            console.log('DataTable dihancurkan');
        }

        // Mengatur URL untuk mengambil data detail
        table1 = $('.table-detail').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url + '/detail', // URL untuk mengambil data detail
                dataSrc: function(json) {
                    console.log('Data for DataTable:', json);
                    return json; // Pastikan ini sesuai dengan struktur data yang diterima
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return '-';
                        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                        return new Date(data).toLocaleDateString('id-ID', options);
                    }
                },
                {data: 'jumlah'},
                {data: 'status'},
                {data: 'keterangan'},
            ]
        });
    }

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Inventori');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Inventori');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_barang]').val(response.nama_barang);
                $('#modal-form [name=id_kategori]').val(response.id_kategori);
                $('#modal-form [name=jumlah]').val(response.jumlah);
                $('#modal-form [name=stok]').val(response.stok);
                $('#modal-form [name=status]').val(response.status);
                $('#modal-form [name=keterangan]').val(response.keterangan);
                $('#modal-form [name=penanggung_jawab]').val(response.penanggung_jawab);
                $('#modal-form [name=lokasi]').val(response.lokasi);
                $('#modal-form [name=id_outlet]').val(response.id_outlet);

                $('#modal-form form').attr('action', "{{ url('inventori') }}/" + response.id_inventori);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data ini?')) {
            $.post(url, {
                '_token': $('[name=csrf-token]').attr('content'),
                '_method': 'delete'
            })
            .done(() => {
                table.ajax.reload();
            })
            .fail(() => {
                alert('Tidak dapat menghapus data');
            });
        }
    }

    function cetakLaporan(url) {
        console.log('URL untuk cetak laporan:', url); // Log URL
        if (url) {
            window.open(url, '_blank');
        } else {
            alert('URL untuk mencetak laporan tidak ditemukan.');
        }
    }
</script>
@endpush
