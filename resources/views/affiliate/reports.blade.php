@extends('affiliate.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Laporan Performa</h1>
        <p class="text-sm text-gray-500 mt-1">Analisis performa bulanan Anda</p>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Performa 12 Bulan Terakhir</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Bulan</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Klik</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Penjualan</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($monthlyStats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $stat['month'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                {{ number_format($stat['clicks']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                {{ number_format($stat['sales']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            Rp {{ number_format($stat['earnings'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="text-sm text-gray-500 mb-1">Total Klik (12 Bulan)</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format(collect($monthlyStats)->sum('clicks')) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="text-sm text-gray-500 mb-1">Total Penjualan (12 Bulan)</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format(collect($monthlyStats)->sum('sales')) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="text-sm text-gray-500 mb-1">Total Earnings (12 Bulan)</div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format(collect($monthlyStats)->sum('earnings'), 0, ',', '.') }}</div>
        </div>
    </div>
</div>
@endsection
