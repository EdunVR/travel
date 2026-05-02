<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Kelola Gerobak - {{ $agen->nama }}</h3>
                <div class="btn-group">
                    <button onclick="addForm('{{ route('agen_gerobak.gerobak.store', $agen->id_member) }}')" 
                            class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus-circle"></i> Tambah Gerobak
                    </button>
                    <button onclick="deleteSelected('{{ route('agen_gerobak.gerobak.destroy-selected', $agen->id_member) }}')" 
                            class="btn btn-danger btn-xs btn-flat">
                        <i class="fa fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-gerobak">
                    <thead>
                        <th width="5%">
                            <input type="checkbox" name="select_all" id="select_all">
                        </th>
                        <th width="5%">No</th>
                        <th>Kode Gerobak</th>
                        <th>Nama Gerobak</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th>Total Produk</th>
                        <th>Total Stok</th>
                        <th>Lokasi</th>
                        <th width="15%"><i class="fa fa-cog"></i> Aksi</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('gerobak.form')
@includeIf('gerobak.manage_produk')
<script>
    
    var currentAgenId = {{ $agen->id_member }};

    $(function () {
        tableGerobak = $('.table-gerobak').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('agen_gerobak.gerobak.data', $agen->id_member) }}',
                type: 'get'
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_gerobak'},
                {data: 'nama_gerobak'},
                {data: 'nama_outlet'},
                {data: 'status_badge'},
                {data: 'total_produk'},
                {data: 'total_stok'},
                {data: 'lokasi', searchable: false, sortable: false},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        // HAPUS: $('[name=select_all]').on('click', function () { ... });
        $('#select_all').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form-gerobak').modal('show');
        $('#modal-form-gerobak .modal-title').text('Tambah Gerobak');
        $('#modal-form-gerobak form')[0].reset();
        $('#modal-form-gerobak form').attr('action', url);
        $('#modal-form-gerobak [name=_method]').val('post');
        $('#modal-form-gerobak [name=nama_gerobak]').focus();
    }

    function editForm(url) {
        $('#modal-form-gerobak').modal('show');
        $('#modal-form-gerobak .modal-title').text('Edit Gerobak');

        const updateUrl = url.replace('/edit', '');

        $('#modal-form-gerobak form')[0].reset();
        $('#modal-form-gerobak form').attr('action', updateUrl); // Gunakan URL tanpa /edit
        $('#modal-form-gerobak [name=_method]').val('put'); // Method PUT untuk update

        // Ambil data untuk form edit
        $.get(url) // Tetap gunakan URL dengan /edit untuk get data
            .done((response) => {
                $('#modal-form-gerobak [name=id_outletz]').val(response.gerobak.id_outlet);
                $('#modal-form-gerobak [name=nama_gerobak]').val(response.gerobak.nama_gerobak);
                $('#modal-form-gerobak [name=status]').val(response.gerobak.status);
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
                    tableGerobak.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        const selectedIds = [];
        $('.table-gerobak tbody input:checked').each(function () {
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
                    'id_gerobak': selectedIds
                })
                .done((response) => {
                    tableGerobak.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                });
        }
    }

</script>
