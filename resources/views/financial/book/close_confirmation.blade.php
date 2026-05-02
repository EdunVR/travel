@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4>Konfirmasi Tutup Buku</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Perhatian!</strong> Proses ini tidak dapat dibatalkan. Pastikan semua transaksi telah dicatat dengan benar sebelum menutup buku.
            </div>
            
            <div class="mb-4">
                <h5>Detail Buku</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Nama Buku</th>
                        <td>{{ $book->name }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ $book->start_date->format('d/m/Y') }} - {{ $book->end_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Mata Uang</th>
                        <td>{{ $book->currency }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-success">{{ ucfirst($book->status) }}</span></td>
                    </tr>
                </table>
            </div>
            
            <div class="mb-4">
                <h5>Aksi yang akan dilakukan:</h5>
                <ul>
                    <li>Generate laporan akhir tahun (Laba Rugi, Neraca, Arus Kas)</li>
                    <li>Mengubah status buku menjadi "Closed"</li>
                    <li>Membuat buku baru untuk periode berikutnya (jika belum ada)</li>
                    <li>Memindahkan saldo akhir sebagai saldo awal periode berikutnya</li>
                </ul>
            </div>
            
            <form action="{{ route('financial.book.close', $book->id) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="password">Konfirmasi Password</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Masukkan password Anda">
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('financial.book.list') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-lock"></i> Konfirmasi Tutup Buku
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
