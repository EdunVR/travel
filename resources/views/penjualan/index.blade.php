@extends('app')

@section('title')
    Daftar Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    @if($outlets->count() > 1)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_outlet">Pilih Outlet</label>
                            <select name="id_outlet" id="id_outlet" class="form-control">
                                <option value="">Semua Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="visibility: hidden;">Aksi</label><br>
                            <button id="print-btn" class="btn btn-success btn-flat">
                                <i class="fa fa-print"></i> Cetak Detail
                            </button>
                            <button id="print-simple-btn" class="btn btn-info btn-flat">
                                <i class="fa fa-file"></i> Cetak Sederhana
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Nama Customer</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Diskon</th>
                        <th>Total Bayar</th>
                         <th>Cash/Bon</th>
                        <th>Kasir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan.detail')
@endsection

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('admin.penjualan.data') }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                { data: 'nama_outlet' },
                {data: 'nama'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'diskon'},
                {data: 'bayar'},
                {data: 'payment_type', searchable: false, sortable: false},
                {data: 'kasir'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })

        $('#id_outlet, #start_date, #end_date').on('change', function () {
            table.ajax.reload();
        });

        $('#filter-btn').on('click', function () {
            table.ajax.reload();
        });

        // Fix: Gunakan event listener yang lebih spesifik
        $('#print-btn').on('click', function () {
            let id_outlet = $('#id_outlet').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            
            // Buat form sementara dengan POST method
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.penjualan.cetak.post") }}';
            form.target = '_blank';
            
            // Tambahkan CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Tambahkan input fields
            function addInput(name, value) {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }
            
            addInput('start_date', start_date);
            addInput('end_date', end_date);
            if (id_outlet) {
                addInput('id_outlet', id_outlet);
            }
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    });

    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
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

    $('#print-simple-btn').on('click', function () {
        let id_outlet = $('#id_outlet').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        
        // Buat form sementara dengan POST method
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.penjualan.cetak.sederhana.post") }}';
        form.target = '_blank';
        
        // Tambahkan CSRF token
        let csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Tambahkan input fields
        function addInput(name, value) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }
        
        addInput('start_date', start_date);
        addInput('end_date', end_date);
        if (id_outlet) {
            addInput('id_outlet', id_outlet);
        }
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
</script>
@endpush
