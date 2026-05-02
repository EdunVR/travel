@extends('app')

@section('title')
    Laporan Umum {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<style>
    .total-info {
        font-size: 16px;
        font-weight: bold;
        padding: 8px 12px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        display: inline-block;
        margin-left: 10px;
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Laporan Umum</li>
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
                <button onclick="updatePeriode()" class="btn btn-info btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Ubah Periode</button>
                <a id="exportPdf" href="#" target="_blank" class="btn btn-success btn-xs btn-flat"><i class="fa fa-file-excel-o"></i> Export PDF</a>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Penjualan</th>
                        <th>Pembelian</th>
                        <th>Pengeluaran</th>
                        <th>Profit</th>
                        <th>Hutang</th>
                        <th>Piutang</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">TOTAL :</th>
                        <th><span class="badge bg-primary" style="font-size: 14px; padding: 8px;" id="totalPenjualan">Rp 0</span></th>
                        <th><span class="badge bg-warning" style="font-size: 14px; padding: 8px;" id="totalPembelian">Rp 0</span></th>
                        <th><span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalPengeluaran">Rp 0</span></th>
                        <th><span class="badge bg-success" style="font-size: 14px; padding: 8px;" id="totalPendapatan">Rp 0</span></th>
                        <th><span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalHutang">Rp 0</span></th>
                        <th><span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalPiutang">Rp 0</span></th>
                    </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('laporan.form')
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('laporan.data', [$tanggalAwal, $tanggalAkhir]) }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                },
                dataSrc: function(json) {
                    let totalPenjualan = 0;
                    let totalPembelian = 0;
                    let totalPengeluaran = 0;
                    let totalPendapatan = 0;
                    let totalHutang = 0;
                    let totalPiutang = 0;

                    json.data.forEach(item => {
                        totalPenjualan += parseInt(item.penjualan.replace(/\D/g, '')) || 0;
                        totalPembelian += parseInt(item.pembelian.replace(/\D/g, '')) || 0;
                        totalPengeluaran += parseInt(item.pengeluaran.replace(/\D/g, '')) || 0;
                        totalPendapatan = totalPenjualan - totalPembelian - totalPengeluaran || 0; 
                        if (item.hutang.includes('-')) {
                            totalHutang -= parseInt(item.hutang.replace(/\D/g, '')) || 0;
                        } else {
                            totalHutang += parseInt(item.hutang.replace(/\D/g, '')) || 0;
                        }

                        if (item.piutang.includes('-')) {
                            totalPiutang -= parseInt(item.piutang.replace(/\D/g, '')) || 0;
                        } else {
                            totalPiutang += parseInt(item.piutang.replace(/\D/g, '')) || 0;
                        }
                    
                    });

                    $('#totalPenjualan').text(formatRupiah(totalPenjualan));
                    $('#totalPembelian').text(formatRupiah(totalPembelian));
                    $('#totalPengeluaran').text(formatRupiah(totalPengeluaran));
                    $('#totalPendapatan').text(formatRupiah(totalPendapatan));
                    $('#totalHutang').text(formatRupiah(totalHutang));
                    $('#totalPiutang').text(formatRupiah(totalPiutang));

                    $('#exportPdf').attr('href', '{{ route('laporan.export_pdf', ['awal' => ':awal', 'akhir' => ':akhir', 'totalPenjualan' => ':totalPenjualan', 'totalPembelian' => ':totalPembelian', 'totalPengeluaran' => ':totalPengeluaran', 'totalPendapatan' => ':totalPendapatan', 'totalHutang' => ':totalHutang', 'totalPiutang' => ':totalPiutang']) }}'
                        .replace(':awal', '{{ $tanggalAwal }}')
                        .replace(':akhir', '{{ $tanggalAkhir }}')
                        .replace(':totalPenjualan', totalPenjualan)
                        .replace(':totalPembelian', totalPembelian)
                        .replace(':totalPengeluaran', totalPengeluaran)
                        .replace(':totalPendapatan', totalPendapatan)
                        .replace(':totalHutang', totalHutang)
                        .replace(':totalPiutang', totalPiutang))


                    return json.data;
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'penjualan'},
                {data: 'pembelian'},
                {data: 'pengeluaran'},
                {data: 'pendapatan'},
                {data: 'hutang'},
                {data: 'piutang'}
            ],
            dom: 'Brt',
            bSort: false,
            bPaginate: false,
        });

        $('#id_outlet').on('change', function () {
            table.ajax.reload();
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(angka);
    }
</script>
@endpush
