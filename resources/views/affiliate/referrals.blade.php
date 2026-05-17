@extends('affiliate.layouts.app')

@section('title', 'Referrals')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Referrals</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola semua referral Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Pending Referrals</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Verified Referrals</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Rejected Referrals</div>
        </div>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Semua Referrals</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Paket</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Customer</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Order Amount</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Komisi</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($referrals as $ref)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $ref->package->package_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-700">{{ $ref->booking->member->nama ?? $ref->booking->member->full_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-400">{{ $ref->booking->member->telepon ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            Rp {{ number_format($ref->order_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            Rp {{ number_format($ref->commission_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $ref->order_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($ref->status === 'verified')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Verified</span>
                            @elseif($ref->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending</span>
                            @elseif($ref->status === 'rejected')
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Rejected</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">Paid</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$ref->termin_1_released)
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full">⏳ Menunggu Pelunasan</span>
                            @elseif($ref->termin_1_released && !$ref->termin_2_released)
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">⏳ Menunggu Keberangkatan</span>
                            @else
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">✅ Bisa Ditarik</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Belum ada referral
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
