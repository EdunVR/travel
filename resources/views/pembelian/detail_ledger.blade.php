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

@section('title', 'Detail Pembelian')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembelian #{{ $pembelian->id_pembelian }}</h6>
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
                            <td>{{ $pembelian->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>{{ $pembelian->supplier->nama }}</td>
                        </tr>
                        <tr>
                            <th>Outlet</th>
                            <td>{{ $pembelian->outlet->nama_outlet }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Total Item</th>
                            <td>{{ $pembelian->total_item }}</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp {{ number_format($pembelian->total_harga, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td>
                                @if($pembelian->bayar >= $pembelian->total_harga)
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($pembelian->bayar > 0)
                                    <span class="badge badge-warning">Sebagian</span>
                                @else
                                    <span class="badge badge-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th>Harga Beli</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->details as $detail)
                        <tr>
                            <td>{{ $detail->bahan->nama_bahan }}</td>
                            <td>Rp {{ number_format($detail->harga_beli, 0) }}</td>
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
