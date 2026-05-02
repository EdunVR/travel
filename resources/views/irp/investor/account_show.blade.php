@extends('app')

@section('title', 'Detail Rekening Investor')

@section('content')

<!-- Tambahkan di bagian atas -->
@include('irp.investor.partials.investment_modal')

<!-- Tambahkan tabel riwayat investasi -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Transaksi</h5>
        <button class="btn btn-sm btn-primary" data-toggle="modal" 
                data-target="#addInvestmentModal"
                data-account-id="{{ $account->id }}">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Investasi</h5>
                        <p class="card-text h4">
                            {{ format_uang($account->total_investment) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Bagi Hasil</h5>
                        <p class="card-text h4">
                            {{ format_uang($account->total_profit) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Bagi Hasil</h5>
                        <p class="card-text h4">
                            {{ format_uang($account->profit_balance) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Saldo Investasi</th>
                        <th>Saldo Bagi Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $investmentBalance = 0;
                        $profitBalance = 0;
                        $transactions = $account->investments()->orderBy('date')->get();
                    @endphp
                    
                    @foreach($transactions as $transaction)
                        @php
                            if ($transaction->type == 'investment') {
                                $investmentBalance += $transaction->amount;
                            } elseif ($transaction->type == 'deposit') {
                                $profitBalance += $transaction->amount;
                            } elseif ($transaction->type == 'withdrawal') {
                                $profitBalance -= $transaction->amount;
                            }
                        @endphp
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>
                                @if($transaction->type == 'investment')
                                    <span class="badge badge-info">Investasi</span>
                                @elseif($transaction->type == 'deposit')
                                    <span class="badge badge-success">Bagi Hasil</span>
                                @else
                                    <span class="badge badge-warning">Penarikan</span>
                                @endif
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td class="text-right">
                                @if($transaction->type != 'withdrawal')
                                    {{ format_uang($transaction->amount) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($transaction->type == 'withdrawal')
                                    {{ format_uang($transaction->amount) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($transaction->type == 'investment')
                                    {{ format_uang($investmentBalance) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($transaction->type != 'investment')
                                    {{ format_uang($profitBalance) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
