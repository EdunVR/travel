<style>
    .badge-warning {
        background-color: #f6c23e;
        color: #1f2d3d;
    }
    .badge-success {
        background-color: #1cc88a;
    }
    .badge-danger {
        background-color: #e74a3b;
    }
    .badge-info {
        background-color: #36b9cc;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .gap-1 {
        gap: 0.25rem;
    }
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

@section('title', 'Manajemen Pencairan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen Pencairan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Sumber</th>
                            <th>Investor</th>
                            <th>Rekening</th>
                            <th class="text-right">Jumlah</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Catatan/Deskripsi</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $index => $withdrawal)
                        <tr>
                            <td>{{ $index + $withdrawals->firstItem() }}</td>
                            <td>
                                @if($withdrawal->source_table == 'investor_withdrawal')
                                    <span class="badge badge-primary">Pengajuan Investor</span>
                                @else
                                    <span class="badge badge-info">Pencairan Sistem</span>
                                @endif
                            </td>
                            <td>
                                @if($withdrawal->investor_id)
                                    {{ $withdrawal->investor->name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">{{ $withdrawal->investor->email ?? '' }}</small>
                                @elseif($withdrawal->account && $withdrawal->account->investor)
                                    {{ $withdrawal->account->investor->name }}
                                    <br>
                                    <small class="text-muted">{{ $withdrawal->account->investor->email }}</small>
                                @else
                                    <span class="text-muted">Data investor tidak tersedia</span>
                                @endif
                            </td>
                            <td>
                                @if($withdrawal->account)
                                    {{ $withdrawal->account->bank_name }}
                                    <br>
                                    <small class="text-muted">{{ $withdrawal->account->account_number }}</small>
                                @else
                                    <span class="text-muted">Data rekening tidak tersedia</span>
                                @endif
                            </td>
                            <td class="text-right">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($withdrawal->requested_at)
                                    {{ \Carbon\Carbon::parse($withdrawal->requested_at)->format('d/m/Y H:i') }}
                                @elseif($withdrawal->date)
                                    {{ \Carbon\Carbon::parse($withdrawal->date)->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $withdrawal->notes ?? $withdrawal->description ?? '-' }}
                            </td>
                            <td>
                                @if($withdrawal->status == 'pending')
                                    @if($withdrawal->source_table == 'investor_withdrawal')
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('irp.withdrawal-management.approve', $withdrawal->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('irp.withdrawal-management.reject', $withdrawal->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('irp.withdrawal-management.approve-investment', $withdrawal->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Proses Pencairan">
                                                <i class="fas fa-check"></i> Berhasil
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($withdrawal->status == 'pending' && $withdrawal->source_table == 'investor_withdrawal')
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('irp.withdrawal-management.approve', $withdrawal->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('irp.withdrawal-management.reject', $withdrawal->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                @if($withdrawals->hasPages())
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $withdrawals->firstItem() }} sampai {{ $withdrawals->lastItem() }} dari {{ $withdrawals->total() }} entri
                    </div>
                    <nav>
                        {{ $withdrawals->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
