@extends('affiliate.layouts.app')

@section('title', 'Payments')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola semua penarikan dana Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Dibayar</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['pending'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Pending</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['failed'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Gagal</div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Semua Penarikan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Referensi</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Metode</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payouts as $payout)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $payout->payout_reference }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ ucwords(str_replace('_', ' ', $payout->payment_method)) }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $payout->requested_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($payout->status === 'completed')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Selesai</span>
                            @elseif($payout->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending</span>
                            @elseif($payout->status === 'failed')
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Gagal</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ ucfirst($payout->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Belum ada riwayat penarikan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payouts->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $payouts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
