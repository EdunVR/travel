@extends('app')

@section('title', 'Detail History Bagi Hasil - ' . $history->group->name)

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i data-feather="clock"></i> Detail History Bagi Hasil
                </h6>
                <a href="{{ route('irp.profit-management.index', ['tab' => 'history']) }}" 
                   class="btn btn-sm btn-light">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-left-primary h-100">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-primary">{{ $history->group->name }}</h5>
                            @if($history->group->product)
                                <p class="mb-1"><strong>Produk:</strong> {{ $history->group->product->nama_produk }}</p>
                            @endif
                            <p class="mb-1"><strong>Periode:</strong> {{ $history->period }}</p>
                            <p class="mb-1"><strong>Tanggal Distribusi:</strong> 
                                {{ $history->distribution_date->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Keuntungan:</span>
                                <span class="font-weight-bold text-primary">
                                    Rp {{ number_format($history->total_profit, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Dibagikan:</span>
                                <span class="font-weight-bold text-success">
                                    Rp {{ number_format($history->total_profit - $history->remaining_profit, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sisa Keuntungan:</span>
                                <span class="font-weight-bold {{ $history->remaining_profit > 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($history->remaining_profit, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                @if($history->status == 'paid')
                                    <span class="badge badge-success">Dibayar</span>
                                @elseif($history->status == 'processed')
                                    <span class="badge badge-warning">Diproses</span>
                                @else
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($history->proof_file)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white py-2">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i data-feather="file-text"></i> Bukti Transfer
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <a href="{{ asset('storage/'.$history->proof_file) }}" target="_blank" 
                               class="btn btn-primary">
                                <i data-feather="download"></i> Download Bukti Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i data-feather="users"></i> Detail Pembagian Investor
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Investor</th>
                                    <th>Rekening</th>
                                    <th class="text-right">Investasi</th>
                                    <th class="text-right">Persentase</th>
                                    <th class="text-right">Bagi Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($distributions as $index => $distribution)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $distribution->investor->name }}</div>
                                        <small class="text-muted">
                                            <span class="badge badge-{{ $distribution->investor->category == 'internal' ? 'primary' : 'success' }}">
                                                {{ ucfirst($distribution->investor->category) }}
                                            </span>
                                        </small>
                                    </td>
                                    <td>
                                        @if($distribution->account)
                                        <div class="font-weight-bold">{{ $distribution->account->bank_name }}</div>
                                        <small class="text-muted">{{ $distribution->account->account_number }}</small>
                                        @else
                                        <span class="text-danger">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        Rp {{ number_format($distribution->investment_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        <div class="font-weight-bold">{{ number_format($distribution->profit_percentage) }}%</div>
                                    </td>
                                    <td class="text-right font-weight-bold text-success">
                                        Rp {{ number_format($distribution->profit_share, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        feather.replace();
    });
</script>
@endpush
