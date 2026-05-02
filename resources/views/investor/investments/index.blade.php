@extends('investor.layouts.app')

@section('title', 'Daftar Investasi Saya')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Investasi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="investments-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Rekening</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            @foreach($account->investments as $investment)
                            <tr>
                                <td>{{ $investment->date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $investment->type === 'investment' ? 'success' : 'info' }}">
                                        {{ $investment->type === 'investment' ? 'Investasi' : 'Penarikan' }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($investment->amount, 0, ',', '.') }}</td>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ $investment->status ?? 'Completed' }}</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data investasi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#investments-table').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endpush
