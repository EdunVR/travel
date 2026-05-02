@extends('app')

@section('title')
    Laporan Penjualan - {{ $agen->nama }}
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Laporan Penjualan - {{ $agen->nama }}</h3>
                <div class="btn-group">
                    <button onclick="tambahLaporan()" class="btn btn-success btn-xs btn-flat">
                        <i class="fa fa-plus"></i> Input Laporan Manual
                    </button>
                    <a href="{{ route('agen_gerobak.index') }}" class="btn btn-default btn-xs btn-flat">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Dari Tanggal</label>
                            <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Sampai Tanggal</label>
                            <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="visibility: hidden;">Filter</label><br>
                            <button onclick="filterLaporan()" class="btn btn-primary">Filter</button>
                            <button onclick="refreshLaporan()" class="btn btn-default">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Keterangan:</strong><br>
                    • <strong>Stok Awal:</strong> Stok yang tersedia di agen pada awal periode<br>
                    • <strong>Detail Gerobak:</strong> Menampilkan stok awal dan penjualan per gerobak
                </div>

                <div id="omset-info" class="alert alert-info" style="display: none;">
                    <strong>Periode: </strong> <span id="periode"></span> | 
                    <strong>Total Pembelian: </strong> <span id="total-pembelian"></span> | 
                    <strong>Total Penjualan: </strong> <span id="total-penjualan"></span> | 
                    <strong>Total Transaksi: </strong> <span id="total-transaksi"></span>
                </div>

                <table class="table table-striped table-bordered table-laporan" style="width: 100%;">
                    <thead>
                        <tr class="bg-primary">
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Kode Produk</th>
                            <th width="15%">Nama Produk</th>
                            <th width="8%">Tipe</th>
                            <th width="8%">Stok Awal</th>
                            <th width="8%">Pembelian</th>
                            <th width="8%">Penjualan</th>
                            <th width="8%">Stok Akhir</th>
                            <th width="10%">Nominal</th>
                            <th width="10%">Gerobak</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-laporan tbody tr.danger {
    background-color: #f2dede !important;
}
.table-laporan tbody tr.warning {
    background-color: #fcf8e3 !important;
}
.small {
    font-size: 12px;
    line-height: 1.2;
}
.gerobak-detail {
    border-left: 3px solid #3c8dbc;
    padding-left: 10px;
    margin-bottom: 5px;
    background-color: #f8f9fa;
    padding: 5px;
    border-radius: 3px;
}
</style>
@endpush

@push('scripts')
<script>
let tableLaporan;

function filterLaporan() {
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    tableLaporan.ajax.url('{{ route("agen_laporan.data", $agen->id_member) }}?start_date=' + startDate + '&end_date=' + endDate).load();
}

function refreshLaporan() {
    tableLaporan.ajax.reload();
}

function tambahLaporan() {
    window.location.href = '{{ route("agen_laporan.create", $agen->id_member) }}';
}

$(function () {
    tableLaporan = $('.table-laporan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("agen_laporan.data", $agen->id_member) }}',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: 'text-center'
            },
            {
                data: 'tanggal',
                render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                },
                className: 'text-center'
            },
            {
                data: 'kode_produk',
                className: 'text-center'
            },
            {
                data: 'nama_produk'
            },
            {
                data: 'tipe_badge',
                className: 'text-center',
                orderable: false
            },
            {
                data: 'stok_awal',
                className: 'text-center'
            },
            {
                data: 'pembelian',
                className: 'text-center'
            },
            {
                data: 'penjualan',
                className: 'text-center'
            },
            {
                data: 'stok_akhir',
                className: 'text-center'
            },
            {
                data: 'total',
                className: 'text-right'
            },
            {
                data: 'detail_gerobak',
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'desc']], // Sort by tanggal descending
        createdRow: function(row, data, dataIndex) {
            if (data.stok_akhir < 0) {
                $(row).addClass('danger');
            } else if (data.stok_akhir == 0) {
                $(row).addClass('warning');
            }
        },
        drawCallback: function (settings) {
            const omset = settings.json.omset;
            if (omset) {
                $('#total-pembelian').text('Rp ' + formatRupiah(omset.total_pembelian || 0));
                $('#total-penjualan').text('Rp ' + formatRupiah(omset.total_penjualan || 0));
                $('#total-transaksi').text(omset.total_transaksi || 0);
                $('#periode').text(omset.start_date + ' s/d ' + omset.end_date);
                $('#omset-info').show();
            }
        },
        language: {
            emptyTable: "Tidak ada data penjualan untuk periode yang dipilih",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 transaksi",
            loadingRecords: "Memuat data...",
            processing: "Memproses...",
            search: "Cari:",
            zeroRecords: "Tidak ditemukan data yang sesuai"
        }
    });

    // Auto filter ketika tanggal berubah
    $('#start_date, #end_date').on('change', function() {
        filterLaporan();
    });
});

function formatRupiah(angka) {
    if (!angka) return '0';
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
@endpush
