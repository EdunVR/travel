@extends('investor.layouts.app')

@section('title', 'Detail Akun Investasi - Portal Investor')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap');

        /* Base Styles */
        body {
            font-family: 'Dongle', sans-serif;
        }
        
        /* Text Sizes - Reduced from original */
        .text-xs { font-size: 1rem; line-height: 1.1; }
        .text-sm { font-size: 1.2rem; line-height: 1.1; }
        .text-base { font-size: 1.4rem; line-height: 1.1; }
        .text-lg { font-size: 1.6rem; line-height: 1.1; }
        .text-xl { font-size: 1.8rem; line-height: 1.1; }
        .text-2xl { font-size: 2.2rem; line-height: 1.1; }
        
        /* Card Styles with reduced padding */
        .account-card {
            background-color: white;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 1rem;
        }

        .account-card div {
            line-height: 1.1;
        }

        /* Perbaikan untuk flex items */
        .flex-between-items > * {
            margin-bottom: 0;
            line-height: 1.1;
        }
        
        .header-section {
            border-bottom: 2px solid black;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        
        /* Button Styles */
        .action-btn {
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .withdrawal-btn {
            background-color: #2E7D32;
            color: white;
        }
        
        .download-btn {
            background-color: #3B82F6;
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* Filter Styles */
        .filter-select {
            border-radius: 50px;
            padding: 0.4rem 0.8rem;
            font-size: 1.2rem;
            border: 1px solid #D1D5DB;
            background-color: #F3F4F6;
        }

        /* Transaction item with reduced padding */
        .transaction-item {
            padding: 0.75rem 0;
        }
    </style>
@endpush

@section('content')
    <main class="p-4">
        <!-- Header with Underline -->
        <div class="header-section">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">Detail Akun Investasi</h2>
                    <p class="text-base text-gray-500">{{ $account->bank_name }} - {{ $account->account_number }}</p>
                </div>
            </div>
        </div>

        <!-- Account Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <!-- Account Information -->
            <div class="account-card">
                <h3 class="text-xl font-bold mb-3">Informasi Akun</h3>
                <div class="space-y-1 text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bank</span>
                        <span class="font-bold">{{ $account->bank_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nomor Rekening</span>
                        <span class="font-bold">{{ $account->account_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama Rekening</span>
                        <span class="font-bold">{{ $account->account_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="font-bold capitalize">{{ $account->status }}</span>
                    </div>
                </div>
            </div>

            <!-- Balance Information -->
            <div class="account-card">
                <h3 class="text-xl font-bold mb-3">Informasi Saldo</h3>
                <div class="space-y-1 text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Investasi</span>
                        <span class="font-bold">Rp{{ number_format($account->total_investment, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Keuntungan</span>
                        <span class="font-bold">Rp{{ number_format($account->total_profit, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Pencairan</span>
                        <span class="font-bold">Rp{{ number_format($account->total_withdrawals, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Saldo Saat Ini</span>
                        <span class="font-bold">Rp{{ number_format($account->profit_balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="account-card">
                <h3 class="text-xl font-bold mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button onclick="showWithdrawalModal()" class="action-btn withdrawal-btn w-full">
                        <i class="fas fa-money-bill-wave mr-2"></i> Ajukan Pencairan
                    </button>
                    <button onclick="handleDownload()" class="action-btn download-btn w-full">
                        <i class="fas fa-file-pdf mr-2"></i> Download History
                    </button>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="account-card">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-3">
                <h3 class="text-xl font-bold">Histori Transaksi</h3>
                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <select id="type-filter" onchange="updateFilters()" class="filter-select">
                        <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                        <option value="investment" {{ $filterType === 'investment' ? 'selected' : '' }}>Setoran Modal</option>
                        <option value="deposit" {{ $filterType === 'deposit' ? 'selected' : '' }}>Bagi Hasil</option>
                        <option value="penarikan" {{ $filterType === 'penarikan' ? 'selected' : '' }}>Penarikan Modal</option>
                        <option value="withdrawal" {{ $filterType === 'withdrawal' ? 'selected' : '' }}>Pencairan Bagi Hasil</option>
                    </select>
                    
                    <select id="period-filter" onchange="updateFilters()" class="filter-select">
                        <option value="all" {{ $filterPeriod === 'all' ? 'selected' : '' }}>Semua Periode</option>
                        <option value="today" {{ $filterPeriod === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $filterPeriod === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $filterPeriod === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ $filterPeriod === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
            </div>
            
            <div class="divide-y">
                @forelse($transactions as $transaction)
                <div class="transaction-item">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-bold">
                                @if($transaction instanceof \App\Models\AccountInvestment)
                                    {{ $transaction->description }}
                                @else
                                    Pencairan Dana
                                @endif
                            </h4>
                            <p class="text-base text-gray-500">
                                @if($transaction instanceof \App\Models\AccountInvestment)
                                    {{ $transaction->date->format('d M Y') }}
                                @else
                                    {{ $transaction->requested_at->format('d M Y') }}
                                @endif
                                @if($transaction instanceof \App\Models\InvestorWithdrawal)
                                    • {{ ucfirst($transaction->status) }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right text-base">
                            @if($transaction instanceof \App\Models\AccountInvestment)
                                @if($transaction->type === 'investment')
                                    <span class="font-bold text-purple-600">
                                        +Rp{{ number_format($transaction->amount, 0, ',', '.') }} (Investasi)
                                    </span>
                                    <p class="text-sm text-gray-500">
                                        Modal: Rp{{ number_format($transaction->total_investasi, 0, ',', '.') }}
                                    </p>
                                @elseif($transaction->type === 'penarikan')
                                    <span class="font-bold text-orange-600">
                                        -Rp{{ number_format($transaction->amount, 0, ',', '.') }} (Penarikan)
                                    </span>
                                    <p class="text-sm text-gray-500">
                                        Modal: Rp{{ number_format($transaction->total_investasi, 0, ',', '.') }}
                                    </p>
                                @elseif($transaction->type === 'deposit')
                                    <span class="font-bold text-green-600">
                                        +Rp{{ number_format($transaction->amount, 0, ',', '.') }} (Bagi Hasil)
                                    </span>
                                    <p class="text-sm text-gray-500">
                                        Saldo: Rp{{ number_format($transaction->calculated_balance, 0, ',', '.') }}
                                    </p>
                                @endif
                            @else
                                <span class="font-bold {{ $transaction->status === 'approved' ? 'text-red-600' : 'text-yellow-600' }}">
                                    -Rp{{ number_format($transaction->amount, 0, ',', '.') }} (Pencairan)
                                </span>
                                <p class="text-sm text-gray-500">
                                    @if($transaction->status === 'approved')
                                        Saldo: Rp{{ number_format($transaction->calculated_balance, 0, ',', '.') }}
                                    @else
                                        Status: {{ ucfirst($transaction->status) }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    @if($transaction->notes)
                    <div class="mt-1 p-1 bg-gray-50 rounded text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> {{ $transaction->notes }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="py-4 text-center text-gray-500">
                    Belum ada transaksi
                </div>
                @endforelse
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        function updateFilters() {
            const type = document.getElementById('type-filter').value;
            const period = document.getElementById('period-filter').value;
            
            let url = new URL(window.location.href);
            url.searchParams.set('type', type);
            url.searchParams.set('period', period);
            url.searchParams.delete('page'); // Reset to first page
            
            window.location.href = url.toString();
        }

        function handleDownload() {
            // Cek apakah di WebView Android
            const isAndroidWebView = /Android/i.test(navigator.userAgent) && /wv/i.test(navigator.userAgent);
            
            if (isAndroidWebView) {
                // Jika di WebView Android, buka URL download dalam tab baru
                const downloadUrl = "{{ route('investor.accounts.history', $account->id) }}";
                window.open(downloadUrl, '_blank');
            } else {
                // Jika di browser biasa, lakukan download seperti biasa
                window.location.href = "{{ route('investor.accounts.history', $account->id) }}";
            }
        }

        // Ganti link download dengan fungsi handleDownload
        document.addEventListener('DOMContentLoaded', function() {
            const downloadBtn = document.querySelector('.download-btn');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleDownload();
                });
            }
        });
    </script>
@endpush
