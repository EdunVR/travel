@extends('app')

@section('title')
    Kontra Bon
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Kontra Bon</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm()" class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus-circle"></i> Tambah Kontra Bon
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs">
                    <li><a href="#tab-piutang" data-toggle="tab">Piutang</a></li>
                    <li class="active"><a href="#tab-kontrabon" data-toggle="tab">List Kontra Bon</a></li> <!-- UBAH MENJADI ACTIVE -->
                </ul>
                
                <div class="tab-content">
                    <!-- Tab Piutang -->
                    <div class="tab-pane" id="tab-piutang"> <!-- HAPUS CLASS ACTIVE -->
                        <form class="form-inline">
                            @if(count($userOutlets) > 1)
                            <div class="form-group">
                                <label for="id_outlet_piutang">Outlet</label>
                                <select name="id_outlet_piutang" id="id_outlet_piutang" class="form-control">
                                    <option value="">Semua Outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="status_piutang">Status</label>
                                <select name="status_piutang" id="status_piutang" class="form-control">
                                    <option value="belum_lunas">Belum Lunas</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                        </form>
                        <table class="table table-striped table-bordered table-piutang">
                            <thead>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>TrxID</th>
                                <th>Nominal</th>
                            </thead>
                        </table>
                    </div>

                    <!-- Tab List Kontra Bon -->
                    <div class="tab-pane active" id="tab-kontrabon"> <!-- TAMBAHKAN CLASS ACTIVE -->
                        <form class="form-inline">
                            @if(count($userOutlets) > 1)
                            <div class="form-group">
                                <label for="id_outlet_kontrabon">Outlet</label>
                                <select name="id_outlet_kontrabon" id="id_outlet_kontrabon" class="form-control">
                                    <option value="">Semua Outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </form>
                        <table class="table table-striped table-bordered table-kontrabon">
                            <thead>
                                <th width="5%">No</th>
                                <th>Kode Kontra Bon</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Total Pembayaran</th>
                                <!-- <th>Total Hutang</th> -->
                                <th width="15%">Aksi</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('kontra_bon.form')

<!-- Modal Detail -->
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detailLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal-detailLabel">Detail Kontra Bon</h4>
            </div>
            <div class="modal-body">
                <div id="detail-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let tablePiutang, tableKontraBon;

    $(function () {
        // Inisialisasi DataTables
        initializeDataTables();
        
        // Event listeners
        $('#status_piutang').on('change', function() {
            tablePiutang.ajax.reload();
        });

        $('#id_outlet_piutang').on('change', function() {
            tablePiutang.ajax.reload();
        });

        $('#id_outlet_kontrabon').on('change', function() {
            tableKontraBon.ajax.reload();
        });
    });

    function initializeDataTables() {
        tablePiutang = $('.table-piutang').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('kontra_bon.data') }}',
                data: function (d) {
                    d.status = $('#status_piutang').val();
                    d.id_outlet = $('#id_outlet_piutang').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'member'},
                {data: 'trx_id'},
                {data: 'nominal'}
            ]
        });

        tableKontraBon = $('.table-kontrabon').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('kontra_bon.data_kontra_bon') }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet_kontrabon').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_kontra_bon'},
                {data: 'tanggal'},
                {data: 'customer'},
                {data: 'total_pembayaran'},
                // {data: 'total_hutang'}, // UBAH DARI 'sisa_hutang' MENJADI 'total_hutang'
                {data: 'aksi', searchable: false, sortable: false}
            ]
        });
    }

    function addForm() {
        window.location.href = '{{ route('kontra_bon.create') }}';
    }

    function cetakNota(url) {
        popupCenter(url, 'Nota Kontra Bon', 900, 675);
    }

    function showDetail(url) {
        console.log('Show detail URL:', url);
        $('#modal-detail').modal('show');
        $('#detail-content').html('Loading...');
        
        $.get(url)
            .done(response => {
                console.log('Detail loaded successfully');
                $('#detail-content').html(response);
            })
            .fail(errors => {
                console.log('Error loading detail:', errors);
                $('#detail-content').html('Gagal memuat data. Silakan coba lagi.');
            });
    }

    function popupCenter(url, title, w, h) {
        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left = (width - w) / 2 / systemZoom + dualScreenLeft;
        const top = (height - h) / 2 / systemZoom + dualScreenTop;
        const newWindow = window.open(url, title, 
        `
            scrollbars=yes,
            width=${w / systemZoom}, 
            height=${h / systemZoom}, 
            top=${top}, 
            left=${left}
        `);

        if (window.focus) newWindow.focus();
    }
</script>
@endpush
