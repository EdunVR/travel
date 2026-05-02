@extends('app')

@section('title')
    Laporan Penjualan {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
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
    <li class="active">Laporan Penjualan</li>
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
                <button onclick="deleteSelected('{{ route('laporan_penjualan.delete_selected') }}')"
                        class="btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i>Hapus Terpilih</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                <thead>
                    <tr>
                        <!-- <th width="5%">
                            <input type="checkbox" name="select-all" id="select-all">
                        </th> -->
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Nama Produk</th>
                        <th>HPP</th>
                        <th>Harga Jual</th>
                        <th>Jumlah</th>
                        <th>Cash/Bon</th>
                        @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                        <th>Profit</th>
                        @endif
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">TOTAL :</th>
                        <th><span class="badge bg-primary" style="font-size: 14px; padding: 8px;" id="totalHPP">Rp 0</span></th>
                        <th><span class="badge bg-warning" style="font-size: 14px; padding: 8px;" id="totalHargaJual">Rp 0</span></th>
                        <th><span class="badge bg-success" style="font-size: 14px; padding: 8px;" id="totalJumlah">Rp 0</span></th>
                        <th>
                            <span class="badge bg-info" style="font-size: 14px; padding: 8px;" id="totalCash">Cash: Rp 0</span>
                            <span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalBon">Bon: Rp 0</span>
                        </th>
                        @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                        <th><span class="badge bg-danger" style="font-size: 14px; padding: 8px;" id="totalProfit">Rp 0</span></th>
                        @endif
                    </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('laporan_penjualan.form')
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
                url: '{{ route('laporan_penjualan.data', [$tanggalAwal, $tanggalAkhir]) }}',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                },
                dataSrc: function(json) {
                    let totalHPP = 0;
                    let totalHargaJual = 0;
                    let totalJumlah = 0;
                    let totalProfit = 0;
                    let totalCash = 0;
                    let totalBon = 0;

                    json.data.forEach(item => {
                        const hargaJual = parseInt(item.harga_jual.replace(/\D/g, '')) || 0;
                        totalHPP += parseInt(item.hpp.replace(/\D/g, '')) || 0;
                        totalHargaJual += hargaJual;
                        totalJumlah += parseInt(item.jumlah) || 0;
                        totalProfit += parseInt(item.profit.replace(/\D/g, '')) || 0;
                        
                        if (item.payment_type === 'Cash') {
                            totalCash += hargaJual;
                        } else {
                            totalBon += hargaJual;
                        }
                    });

                    $('#totalHPP').text(formatRupiah(totalHPP));
                    $('#totalHargaJual').text(formatRupiah(totalHargaJual));
                    $('#totalJumlah').text(totalJumlah);
                    $('#totalProfit').text(formatRupiah(totalProfit));
                    $('#totalCash').text('Cash: ' + formatRupiah(totalCash));
                    $('#totalBon').text('Bon: ' + formatRupiah(totalBon));
                    
                    $('#exportPdf').attr('href', '{{ route('laporan_penjualan.export_pdf', ['awal' => ':awal', 'akhir' => ':akhir', 'totalHPP' => ':totalHPP', 'totalHargaJual' => ':totalHargaJual', 'totalJumlah' => ':totalJumlah', 'totalProfit' => ':totalProfit', 'totalCash' => ':totalCash', 'totalBon' => ':totalBon']) }}'
                        .replace(':awal', '{{ $tanggalAwal }}')
                        .replace(':akhir', '{{ $tanggalAkhir }}')
                        .replace(':totalHPP', totalHPP)
                        .replace(':totalHargaJual', totalHargaJual)
                        .replace(':totalJumlah', totalJumlah)
                        .replace(':totalProfit', totalProfit)
                        .replace(':totalCash', totalCash)
                        .replace(':totalBon', totalBon));

                    return json.data;
                }
            },
            columns: [
                // {data: 'select-all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'nama_produk'},
                {data: 'hpp'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'payment_type'},
                @if(in_array('Tampilkan Profit', Auth::user()->akses_khusus ?? []))
                {data: 'profit'}
                @endif
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

        $('#select-all').on('click', function (e) {
                    if ($(this).is(':checked')) {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', true);
                    } else {
                        $('.table').find('tbody').find('input[type=checkbox]').prop('checked', false);
                    }
                })
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
                            'id_laporan': selectedIds
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
