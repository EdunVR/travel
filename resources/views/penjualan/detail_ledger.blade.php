<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>

@extends('app')

@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Penjualan #{{ $penjualan->id_penjualan }}</h6>
            <a href="{{ url()->previous() }}" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tanggal</th>
                            <td>{{ $penjualan->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Outlet</th>
                            <td>{{ $penjualan->outlet->nama_outlet }}</td>
                        </tr>
                        <tr>
                            <th>Kasir</th>
                            <td>{{ $penjualan->user->name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Pelanggan</th>
                            <td>{{ $penjualan->member->nama ?? 'Customer Umum' }}</td>
                        </tr>
                        <tr>
                            <th>Total Item</th>
                            <td>{{ $penjualan->total_item }}</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp {{ number_format($penjualan->total_harga, 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->details as $detail)
                        <tr>
                            <td>{{ $detail->produk->nama_produk }}</td>
                            <td>Rp {{ number_format($detail->harga_jual, 0) }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
