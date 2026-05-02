@extends('app')

@section('content')
<div class="container">
    <h2>Detail Laporan Penjualan</h2>
    <table class="table">
        <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Nama Produk</th>
            <td>{{ $laporan->nama_produk }}</td>
        </tr>
        <tr>
            <th>HPP</th>
            <td>Rp{{ number_format($laporan->hpp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Harga Jual</th>
            <td>Rp{{ number_format($laporan->harga_jual, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $laporan->jumlah }}</td>
        </tr>
        <tr>
            <th>Profit</th>
            <td>Rp{{ number_format(($laporan->harga_jual - $laporan->hpp) * $laporan->jumlah, 0, ',', '.') }}</td>
        </tr>
    </table>
    <a href="{{ route('laporan_penjualan.index') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
