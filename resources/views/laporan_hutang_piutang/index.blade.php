@extends('app')

@section('title')
    Laporan Hutang dan Piutang {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
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
    <li class="active">Laporan Hutang dan Piutang</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="updatePeriode()" class="btn btn-info btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Ubah Periode</button>
                <a id="exportPdf" href="#" target="_blank" class="btn btn-success btn-xs btn-flat"><i class="fa fa-file-excel-o"></i> Export PDF</a>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Hutang</th>
                        <th>Piutang</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">TOTAL :</th>
                        <th><span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalHutang">Rp 0</span></th>
                        <th><span class="badge bg-success" style="font-size: 14px; padding: 8px;" id="totalPiutang">Rp 0</span></th>
                    </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('laporan_hutang_piutang.form')
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
                url: '{{ route('laporan_hutang_piutang.data', [$tanggalAwal, $tanggalAkhir]) }}',
                dataSrc: function(json) {
                    let totalHutang = 0;
                    let totalPiutang = 0;

                    json.data.forEach(item => {
                        totalHutang += parseInt(item.hutang.replace(/\D/g, '')) || 0;
                        totalPiutang += parseInt(item.piutang.replace(/\D/g, '')) || 0;
                    });

                    $('#totalHutang').text(formatRupiah(totalHutang));
                    $('#totalPiutang').text(formatRupiah(totalPiutang));

                    $('#exportPdf').attr('href', '{{ route('laporan_hutang_piutang.export_pdf', ['awal' => ':awal', 'akhir' => ':akhir', 'totalHutang' => ':totalHutang', 'totalPiutang' => ':totalPiutang']) }}'
                        .replace(':awal', '{{ $tanggalAwal }}')
                        .replace(':akhir', '{{ $tanggalAkhir }}')
                        .replace(':totalHutang', totalHutang)
                        .replace(':totalPiutang', totalPiutang));

                    return json.data;
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'hutang'},
                {data: 'piutang'}
            ],
            dom: 'Brt',
            bSort: false,
            bPaginate: false,
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
