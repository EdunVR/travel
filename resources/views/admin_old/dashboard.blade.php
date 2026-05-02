<style>
/* Gaya dasar untuk small-boxx */
.small-boxx {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
    background: #A2DDF0; /* Warna utama */
    color: #2c3e50; /* Warna teks gelap */
    margin-bottom: 20px;
}

.small-boxx:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.small-boxx .innerx {
    padding: 20px;
}

.small-boxx .iconx {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 60px;
    color: rgba(44, 62, 80, 0.1); /* Warna ikon pastel */
    transition: all 0.3s ease-in-out;
}

.small-boxx:hover .iconx {
    transform: scale(1.1);
    color: rgba(44, 62, 80, 0.2); /* Warna ikon saat hover */
}

.small-boxx .small-box-footerx {
    background: rgba(44, 62, 80, 0.05); /* Warna footer pastel */
    padding: 10px;
    text-align: center;
    color: #2c3e50; /* Warna teks gelap */
    text-decoration: none;
    display: block;
    transition: all 0.3s ease-in-out;
}

.small-boxx .small-box-footerx:hover {
    background: rgba(44, 62, 80, 0.1); /* Warna footer saat hover */
}

/* Warna alternatif untuk small-boxx */
.small-boxx.bg-secondaryx {
    background: #B39EB5; /* Warna sekunder */
}

/* Gaya untuk boxx grafik */
.boxx {
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
    background: #ffffff; /* Warna latar pastel */
    margin-bottom: 20px;
}

.boxx:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.boxx-headerx {
    background: #A2DDF0; /* Warna utama */
    color: #2c3e50; /* Warna teks gelap */
    padding: 15px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    text-align: center;
}

.boxx-bodyx {
    padding: 20px;
}

/* Gaya untuk form dan tombol */
.form-inlinex {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.form-inlinex .form-groupx {
    margin: 0;
}

.form-inlinex .form-controlx {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 8px 12px;
    background: #ffffff; /* Warna latar pastel */
    color: #2c3e50; /* Warna teks gelap */
}

.form-inlinex .btn-primaryx {
    background: #A2DDF0; /* Warna utama */
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    color: #2c3e50; /* Warna teks gelap */
    transition: all 0.3s ease-in-out;
}

.form-inlinex .btn-primaryx:hover {
    background: #8CC7E0; /* Warna utama lebih gelap */
}

/* Gaya untuk infografis tambahan */
.infographicx {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
}

.infographicx-itemx {
    flex: 1;
    background: #6A7BA2; /* Warna sekunder */
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    color: #ffffff; /* Warna teks gelap */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}

.infographicx-itemx:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.infographicx-itemx h4 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.infographicx-itemx p {
    margin: 10px 0 0;
    font-size: 14px;
}
</style>

@extends('app')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-boxx">
            <div class="innerx">
                <h3>{{ $bahan }}</h3>
                <p>Total Bahan</p>
            </div>
            <div class="iconx">
                <i class="fa fa-cube"></i>
            </div>
            <a href="{{ route('bahan.index') }}" class="small-box-footerx">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-boxx">
            <div class="innerx">
                <h3>{{ $produk }}</h3>
                <p>Total Produk</p>
            </div>
            <div class="iconx">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('produk.index') }}" class="small-box-footerx">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-boxx">
            <div class="innerx">
                <h3>{{ $member }}</h3>
                <p>Total Customer</p>
            </div>
            <div class="iconx">
                <i class="fa fa-id-card"></i>
            </div>
            <a href="{{ route('member.index') }}" class="small-box-footerx">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-boxx">
            <div class="innerx">
                <h3>{{ $supplier }}</h3>
                <p>Total Supplier</p>
            </div>
            <div class="iconx">
                <i class="fa fa-truck"></i>
            </div>
            <a href="{{ route('supplier.index') }}" class="small-box-footerx">Lihat <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Infografis Tambahan -->
<div class="infographicx">
    <div class="infographicx-itemx">
        <h4>Total Omset Hari Ini</h4>
        <p>Rp {{ number_format($total_omset, 0, ',', '.') }}</p>
    </div>
    <div class="infographicx-itemx">
        <h4>Total Profit Hari Ini</h4>
        <p>Rp {{ number_format($total_profit, 0, ',', '.') }}</p>
    </div>
    <div class="infographicx-itemx">
        <h4>Total BON Hari Ini</h4>
        <p>Rp {{ number_format($total_BON_harian, 0, ',', '.') }}</p>
    </div>
    <div class="infographicx-itemx">
        <h4>Total CASH Hari Ini</h4>
        <p>Rp {{ number_format($total_CASH_harian, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Form Ubah Periode -->
<form action="{{ route('dashboard') }}" method="GET" class="form-inlinex text-center mb-3">
    <div class="form-groupx">
        <input type="date" name="tanggal_awal" class="form-controlx" value="{{ $tanggal_awal }}">
    </div>
    <div class="form-groupx">
        <input type="date" name="tanggal_akhir" class="form-controlx" value="{{ $tanggal_akhir }}">
    </div>
    <button type="submit" class="btn btn-primaryx">Ubah Periode</button>
</form>

<!-- Grafik -->
<div class="row">
    <div class="col-lg-6">
        <div class="boxx">
            <div class="boxx-headerx">
                <p class="text-center">Grafik Penjualan & Profit</p>
                <p class="text-center">
                    <strong>{{ tanggal_indonesia($tanggal_awal, false) }} s/d {{ tanggal_indonesia($tanggal_akhir, false) }}</strong>
                </p>
            </div>
            <div class="boxx-bodyx">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="boxx">
            <div class="boxx-headerx">
                <p class="text-center">Grafik Pengeluaran & Pembelian</p>
                <p class="text-center">
                    <strong>{{ tanggal_indonesia($tanggal_awal, false) }} s/d {{ tanggal_indonesia($tanggal_akhir, false) }}</strong>
                </p>
            </div>
            <div class="boxx-bodyx">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {

    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    var ctxSales = $('#salesChart').get(0).getContext('2d');
    var ctxExpense = $('#expenseChart').get(0).getContext('2d');

    var salesChartData = {
        labels: {{ json_encode($data_tanggal) }},
        datasets: [
            {
                label: 'Omset Penjualan',
                backgroundColor: 'rgba(135, 206, 235, 0.9)',
                borderColor: 'rgba(135, 206, 235, 1)',
                pointBackgroundColor: '#87CEEB',
                pointBorderColor: 'rgba(135, 206, 235, 1)',
                data: {{ json_encode($data_penjualan) }},
                fill: true
            },
            {
                label: 'BON Customer',
                backgroundColor: 'rgba(255, 111, 97, 0.9)',
                borderColor: 'rgba(255, 111, 97, 1)',
                pointBackgroundColor: '#FF6F61',
                pointBorderColor: 'rgba(255, 111, 97, 1)',
                data: {{ json_encode($data_BON) }},
                fill: true
            },
            {
                label: 'Margin Profit',
                backgroundColor: 'rgba(179, 158, 181, 0.9)',
                borderColor: 'rgba(179, 158, 181, 1)',
                pointBackgroundColor: '#B39EB5',
                pointBorderColor: 'rgba(179, 158, 181, 1)',
                data: {{ json_encode($data_profit) }},
                fill: true
            }
        ]
    };

    var expenseChartData = {
        labels: {{ json_encode($data_tanggal) }},
        datasets: [
            {
                label: 'Pembelian Bahan',
                backgroundColor: 'rgba(119, 221, 119, 0.9)',
                borderColor: 'rgba(119, 221, 119, 1)',
                pointBackgroundColor: '#77DD77',
                pointBorderColor: 'rgba(119, 221, 119, 1)',
                data: {{ json_encode($data_pembelian) }},
                fill: true
            },
            {
                label: 'Pengeluaran Umum',
                backgroundColor: 'rgba(255, 215, 0, 0.9)',
                borderColor: 'rgba(255, 215, 0, 1)',
                pointBackgroundColor: '#FFD700',
                pointBorderColor: 'rgba(255, 215, 0, 1)',
                data: {{ json_encode($data_pengeluaran) }},
                fill: true
            }
        ]
    };

    var salesChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                ticks: {
                    callback: function(value, index, values) {
                        return formatRupiah(value);
                    }
                }
            }
        }
    };

    new Chart(ctxSales, {
        type: 'line',
        data: salesChartData,
        options: salesChartOptions
    });

    new Chart(ctxExpense, {
        type: 'bar',
        data: expenseChartData,
        options: salesChartOptions
    });

});
</script>
@endpush
