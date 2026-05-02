@extends('app')

@section('title', 'Ledger Account')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Buku Besar: {{ $account->code }} - {{ $account->name }}
            </h6>
            <span class="badge badge-primary">
                Saldo Akhir: Rp {{ number_format($current_balance, 2) }}
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Transaksi</th>
                            <th>Keterangan</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>
                                @if($transaction->transaction)
                                    @if($transaction->transaction_type === 'journal')
                                        <a href="{{ route('financial.journals.show', $transaction->transaction_id) }}">
                                            {{ $transaction->transaction->reference }}
                                        </a>
                                    @elseif($transaction->transaction_type === 'payment')
                                        <a href="{{ route('financial.payments.show', $transaction->transaction_id) }}">
                                            {{ $transaction->transaction->reference }}
                                        </a>
                                    @else
                                        {{ $transaction->transaction->reference }}
                                    @endif
                                @endif
                            </td>
                            <td>{{ $transaction->description ?? $transaction->transaction?->description }}</td>
                            <td class="text-right">{{ number_format($transaction->debit, 2) }}</td>
                            <td class="text-right">{{ number_format($transaction->credit, 2) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($transaction->balance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
