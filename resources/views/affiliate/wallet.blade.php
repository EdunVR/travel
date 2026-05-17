@extends('affiliate.layouts.app')

@section('title', 'Wallet')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Wallet</h1>
        <p class="text-sm text-gray-500 mt-1">Riwayat transaksi wallet Anda</p>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-1">Saldo Tersedia</div>
            <div class="text-3xl font-bold">Rp {{ number_format($affiliator->available_balance, 0, ',', '.') }}</div>
            <div class="text-xs opacity-75 mt-2">Bisa ditarik kapan saja</div>
        </div>

        <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-1">Saldo Pending</div>
            <div class="text-3xl font-bold">Rp {{ number_format($affiliator->pending_balance, 0, ',', '.') }}</div>
            <div class="text-xs opacity-75 mt-2">
                @if($pendingBreakdown['waiting_payment'] > 0)
                    Menunggu Pelunasan: Rp {{ number_format($pendingBreakdown['waiting_payment'], 0, ',', '.') }}<br>
                @endif
                @if($pendingBreakdown['waiting_departure'] > 0)
                    Menunggu Keberangkatan: Rp {{ number_format($pendingBreakdown['waiting_departure'], 0, ',', '.') }}
                @endif
                @if($pendingBreakdown['waiting_payment'] == 0 && $pendingBreakdown['waiting_departure'] == 0)
                    Menunggu verifikasi
                @endif
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">
                            {{ $trx['date']->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($trx['type'] == 'click')
                                    <i class="fas fa-mouse-pointer text-blue-500"></i>
                                @elseif($trx['type'] == 'referral')
                                    <i class="fas fa-users text-green-500"></i>
                                @else
                                    <i class="fas fa-arrow-down text-red-500"></i>
                                @endif
                                <span class="text-gray-900">{{ $trx['description'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold {{ $trx['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $trx['amount'] >= 0 ? '+' : '' }}Rp {{ number_format(abs($trx['amount']), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($trx['status'] === 'completed' || $trx['status'] === 'verified')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Selesai</span>
                            @elseif($trx['status'] === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending</span>
                            @elseif($trx['status'] === 'rejected' || $trx['status'] === 'failed')
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Gagal</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ ucfirst($trx['status']) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Belum ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
