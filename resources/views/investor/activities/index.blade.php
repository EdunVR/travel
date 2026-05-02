@extends('investor.layouts.app')

@section('title', 'Aktivitas Investasi')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    </style>
@endpush

@section('content')
    <main class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">Semua Aktivitas</h2>
                <p class="text-gray-500">Riwayat lengkap investasi dan pencairan</p>
            </div>
            <a href="{{ route('investor.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="divide-y">
            @forelse($activities as $activity)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="p-2 rounded-full mr-3 
                            @if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit') bg-green-100 text-green-600
                            @elseif($activity instanceof \App\Models\InvestorWithdrawal) bg-blue-100 text-blue-600
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit')
                                <i class="fas fa-hand-holding-usd"></i>
                            @elseif($activity instanceof \App\Models\InvestorWithdrawal)
                                <i class="fas fa-money-bill-wave"></i>
                            @else
                                <i class="fas fa-info-circle"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h4 class="font-medium">
                                    @if($activity instanceof \App\Models\AccountInvestment)
                                        {{ $activity->description }}
                                    @else
                                        Pencairan Dana
                                    @endif
                                </h4>
                                <span class="text-sm text-gray-500">
                                    {{-- Gunakan date untuk investment, requested_at untuk withdrawal --}}
                                    @if($activity instanceof \App\Models\AccountInvestment)
                                        {{ $activity->date->format('d M Y') }}
                                    @else
                                        {{ $activity->requested_at->format('d M Y') }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($activity->amount)
                                    Rp{{ number_format($activity->amount, 0, ',', '.') }} • 
                                @endif
                                {{ $activity->account->bank_name }} ({{ $activity->account->account_number }})
                                @if($activity instanceof \App\Models\InvestorWithdrawal)
                                    • Status: {{ ucfirst($activity->status) }}
                                @endif
                            </p>
                            @if($activity->notes)
                            <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i> {{ $activity->notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Tidak ada aktivitas
                </div>
                @endforelse
            </div>

            @if($activities->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $activities->links() }}
            </div>
            @endif
        </div>
    </main>
@endsection
