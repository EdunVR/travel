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

@section('title', 'Detail Jurnal')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-book mr-2"></i>
                Detail Jurnal #{{ $journal->reference }}
            </h6>
            <a href="{{ url()->previous() }}" class="btn btn-danger">
                Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">No. Referensi</th>
                            <td>{{ $journal->reference }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $journal->date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tipe Transaksi</th>
                            <td>
                                <span class="badge badge-{{ 
                                    $journal->transaction_type === 'manual' ? 'primary' : 
                                    ($journal->transaction_type === 'penjualan' ? 'success' :
                                    ($journal->transaction_type === 'pembelian' ? 'warning' : 'info'))
                                }}">
                                    {{ ucfirst($journal->transaction_type ?? 'manual') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Debit</th>
                            <td>Rp {{ number_format($journal->entries->sum('debit'), 0) }}</td>
                        </tr>
                        <tr>
                            <th>Total Kredit</th>
                            <td>Rp {{ number_format($journal->entries->sum('credit'), 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="font-weight-bold">Keterangan:</h5>
                <p>{{ $journal->description ?? 'Tidak ada keterangan' }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Akun</th>
                            <th class="text-center bg-success text-white">Debit</th>
                            <th class="text-center bg-danger text-white">Kredit</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journal->entries as $entry)
                        <tr>
                            <td>
                                <strong>{{ $entry->account->code }}</strong> - {{ $entry->account->name }}
                                <br>
                                <small class="text-muted">{{ ucfirst($entry->account->type) }}</small>
                            </td>
                            <td class="text-right text-success font-weight-bold">
                                @if($entry->debit > 0)
                                Rp {{ number_format($entry->debit, 0) }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-right text-danger font-weight-bold">
                                @if($entry->credit > 0)
                                Rp {{ number_format($entry->credit, 0) }}
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $entry->memo ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
