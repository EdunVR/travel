@extends('affiliate.layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Leaderboard Mitra</h1>
        <p class="text-sm text-gray-500 mt-1">Peringkat perolehan tertinggi dari PPC dan Referral</p>
    </div>

    <!-- Filter Period -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex gap-3 items-center">
            <select name="period" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
            <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                <option value="total" {{ request('type') == 'total' ? 'selected' : '' }}>Total Earnings</option>
                <option value="ppc" {{ request('type') == 'ppc' ? 'selected' : '' }}>PPC (Klik)</option>
                <option value="referral" {{ request('type') == 'referral' ? 'selected' : '' }}>Referral (Penjualan)</option>
            </select>
        </form>
    </div>

    <!-- Top 3 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($topThree as $index => $aff)
        <div class="bg-white rounded-xl shadow-sm border-2 {{ $index == 0 ? 'border-yellow-400' : ($index == 1 ? 'border-gray-300' : 'border-amber-600') }} p-6 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 {{ $index == 0 ? 'bg-yellow-100' : ($index == 1 ? 'bg-gray-100' : 'bg-amber-100') }} rounded-bl-full opacity-50"></div>
            <div class="relative">
                <div class="w-20 h-20 rounded-full {{ $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : 'bg-amber-600') }} flex items-center justify-center text-white font-bold text-2xl mx-auto mb-3 overflow-hidden border-4 border-white shadow-lg">
                    @if($aff->photo)
                        <img src="{{ $aff->photo_url }}" alt="{{ $aff->full_name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($aff->full_name, 0, 1)) }}
                    @endif
                </div>
                <div class="text-3xl font-bold {{ $index == 0 ? 'text-yellow-600' : ($index == 1 ? 'text-gray-600' : 'text-amber-600') }} mb-1">
                    #{{ $index + 1 }}
                </div>
                <div class="font-bold text-gray-900">{{ $aff->full_name }}</div>
                <div class="text-xs text-gray-500 mb-3">{{ '@' . $aff->username }}</div>
                
                <div class="bg-gray-50 rounded-lg p-3 mt-3">
                    <div class="text-xs text-gray-500 mb-1">Total Earnings</div>
                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($aff->total_earnings, 0, ',', '.') }}</div>
                    <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                        <div>
                            <div class="text-gray-500">PPC</div>
                            <div class="font-semibold text-blue-600">{{ number_format($aff->total_clicks) }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Referral</div>
                            <div class="font-semibold text-green-600">{{ number_format($aff->total_sales) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Full Leaderboard -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Semua Peringkat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 w-16">Rank</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Mitra</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Total Klik</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Total Penjualan</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Komisi PPC</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Komisi Referral</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Total Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaderboard as $index => $aff)
                    <tr class="hover:bg-gray-50 {{ $aff->id == $currentAffiliator->id ? 'bg-green-50' : '' }}">
                        <td class="px-4 py-3 text-center">
                            <div class="w-8 h-8 rounded-full {{ $index < 3 ? ($index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : 'bg-amber-600')) : 'bg-gray-200' }} flex items-center justify-center text-white font-bold text-sm mx-auto">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 overflow-hidden border-2 border-gray-100">
                                    @if($aff->photo)
                                        <img src="{{ $aff->photo_url }}" alt="{{ $aff->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($aff->full_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $aff->full_name }}
                                        @if($aff->id == $currentAffiliator->id)
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full ml-1">Anda</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">{{ '@' . $aff->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">
                                {{ number_format($aff->total_clicks) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">
                                {{ number_format($aff->total_sales) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-blue-600 font-semibold">
                            Rp {{ number_format($aff->ppc_earnings, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold">
                            Rp {{ number_format($aff->referral_earnings, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($aff->total_earnings, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Your Position -->
    @if($yourPosition > 3)
    <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90 mb-1">Posisi Anda</div>
                <div class="text-3xl font-bold">#{{ $yourPosition }}</div>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90 mb-1">Total Earnings</div>
                <div class="text-2xl font-bold">Rp {{ number_format($currentAffiliator->total_earnings, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
