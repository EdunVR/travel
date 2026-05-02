@extends('investor.layouts.app')

@section('title', 'Bagi Hasil - Portal Investor')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    </style>
@endpush

@section('content')
    <main class="p-6">
        <!-- Statistik Bagi Hasil -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Saldo Bagi Hasil</p>
                        <h3 class="text-2xl font-bold mt-1">Rp{{ number_format($totalProfit, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                </div>
                <button onclick="showWithdrawalModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-money-bill-wave mr-2"></i> Ajukan Pencairan
                    </button>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Bagi Hasil Tahun Ini</p>
                        <h3 class="text-2xl font-bold mt-1">Rp{{ number_format($yearlyProfit, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm">Bagi Hasil Bulan Ini</p>
                        <h3 class="text-2xl font-bold mt-1">Rp{{ number_format($monthlyProfit, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Bagi Hasil -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-semibold">Riwayat Distribusi Bagi Hasil</h3>
                <div class="relative">
                    <select class="appearance-none bg-gray-100 border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>Semua Periode</option>
                        <option>Tahun Ini</option>
                        <option>Bulan Ini</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="divide-y">
                @forelse($profitDistributions as $distribution)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium">Bagi Hasil {{ $distribution->description ?? 'Tidak Ada Periode' }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($distribution->date)
                                    Diterima pada {{ $distribution->date->format('d M Y') }}
                                @else
                                    Tanggal distribusi tidak tersedia
                                @endif
                                • {{ $distribution->account->bank_name }} ({{ $distribution->account->account_number }})
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-green-600">
                                +Rp{{ number_format($distribution->amount, 0, ',', '.') }}
                            </span>
                           
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Belum ada distribusi bagi hasil
                </div>
                @endforelse
            </div>
            @if($profitDistributions->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $profitDistributions->links() }}
            </div>
            @endif
        </div>
    </main>
@endsection
