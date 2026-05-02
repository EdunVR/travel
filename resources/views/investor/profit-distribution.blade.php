@extends('investor.layouts.app')

@section('title', 'Bagi Hasil')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Bagi Hasil</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Rekening</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $distribution)
                        <tr>
                            <td>{{ $distribution->date->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($distribution->amount, 0, ',', '.') }}</td>
                            <td>{{ $distribution->account->account_number ?? '-' }}</td>
                            <td>{{ $distribution->description ?? 'Bagi hasil' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data bagi hasil</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $distributions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
