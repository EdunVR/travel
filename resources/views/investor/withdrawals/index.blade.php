@extends('investor.layouts.app')

@section('title', 'Daftar Pencairan - Portal Investor')

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
                <h2 class="text-xl font-semibold">Riwayat Pencairan Dana</h2>
                <p class="text-gray-500">Total pencairan disetujui: Rp{{ number_format($totalApprovedWithdrawals, 0, ',', '.') }}</p>
            </div>
            <a href="{{ route('investor.investments') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Investasi
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h3 class="font-semibold">Semua Pencairan</h3>
                    <div class="relative">
                        <select class="appearance-none bg-gray-100 border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option>Semua Status</option>
                            <option>Disetujui</option>
                            <option>Menunggu</option>
                            <option>Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <span class="text-sm text-gray-500">
                    Menampilkan {{ $withdrawals->count() }} dari {{ $withdrawals->total() }} pencairan
                </span>
            </div>
            <div class="divide-y">
                @forelse($withdrawals as $withdrawal)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium">Pencairan Dana</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $withdrawal->requested_at->format('d M Y H:i') }}
                                • {{ $withdrawal->account->bank_name }} ({{ $withdrawal->account->account_number }})
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-red-600">
                                -Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}
                            </span>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($withdrawal->status === 'approved')
                                    <span class="text-green-600">Disetujui pada {{ $withdrawal->approved_at->format('d M Y') }}</span>
                                @elseif($withdrawal->status === 'rejected')
                                    <span class="text-red-600">Ditolak</span>
                                @else
                                    <span class="text-yellow-600">Menunggu Persetujuan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($withdrawal->notes)
                    <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> {{ $withdrawal->notes }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Belum ada riwayat pencairan
                </div>
                @endforelse
            </div>
            @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </div>
    </main>
@endsection
