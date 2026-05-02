@extends('investor.layouts.app')

@section('title', 'Investasi Saya - Portal Investor')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap');
        
        body {
            font-family: 'Dongle', sans-serif;
        }
        
        /* Text Sizes - Dipertahankan */
        .text-xs { font-size: 1.0rem; }
        .text-sm { font-size: 1.2rem; }
        .text-base { font-size: 1.4rem; }
        .text-lg { font-size: 1.6rem; }
        .text-xl { font-size: 1.8rem; }
        .text-2xl { font-size: 2.0rem; }
        .text-3xl { font-size: 2.2rem; }
        
        /* Investasi Card Styles - Diperbaiki spacing */
        .account-card {
            background-color: white;
            border-radius: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .account-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .account-header {
            display: flex;
            align-items: center;
            gap: 8px; /* dikurangi dari 10px */
            margin-left: 0.8rem; /* dikurangi dari 1.0rem */
            margin-right: 0.8rem;
            padding-top: 0.6rem; /* dikurangi dari 0.8rem */
            padding-bottom: 0.6rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .account-icon {
            width: 36px; /* dikurangi dari 40px */
            height: 36px;
            background-color: #FFD700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem; /* dikurangi dari 1.2rem */
        }
        
        .account-info {
            flex: 1;
        }
        
        .account-title {
            font-weight: 700;
            font-size: 1.5rem; /* dikurangi dari 1.6rem */
            line-height: 0.8;
        }
        
        .account-number {
            font-size: 1.1rem; /* dikurangi dari 1.2rem */
            color: #666;
        }
        
        .account-status {
            padding: 1px 30px; /* dikurangi dari 40px */
            border-radius: 40px;
            font-size: 1.0rem; /* dikurangi dari 1.1rem */
            font-weight: 700;
        }
        
        .status-active {
            background-color: #E8F5E9;
            color: #2E7D32;
        }
        
        .status-inactive {
            background-color: #EEEEEE;
            color: #757575;
        }
        
        .account-details {
            padding: 0.8rem; /* dikurangi dari 1.0rem */
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem; /* dikurangi dari 1.2rem */
            margin-bottom: 0.3rem; /* dikurangi dari 0.5rem */
            line-height: 1.1; /* ditambahkan untuk mengurangi jarak vertikal */
        }
        
        .detail-label {
            color: #666;
        }
        
        .detail-value {
            font-weight: 700;
        }
        
        .account-footer {
            padding: 0.6rem 0.8rem; /* dikurangi dari 0.8rem 1.0rem */
            margin-left: 0.8rem; /* dikurangi dari 1.0rem */
            margin-right: 0.8rem;
            border-top: 1px solid #e0e0e0;
            text-align: right;
        }
        
        .detail-link {
            color: green;
            font-weight: 700;
            font-size: 1.1rem; /* dikurangi dari 1.2rem */
            text-decoration: none;
        }
        
        .detail-link:hover {
            color: #FBC02D;
        }
        
        .add-account-btn {
            background-color: #2E7D32;
            color: white;
            border-radius: 40px;
            padding: 6px 16px; /* dikurangi dari 8px 20px */
            font-size: 1.3rem; /* dikurangi dari 1.4rem */
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px; /* dikurangi dari 6px */
            transition: all 0.3s ease;
        }
        
        .add-account-btn:hover {
            background-color: #1B5E20;
            transform: translateY(-2px);
        }

        /* Header Section - Diperbaiki spacing */
        .header-container {
            padding: 0.8rem; /* dikurangi dari 1.0rem */
        }

        .total-investment {
            font-size: 2rem; /* dikurangi dari 2.5rem/3xl */
            line-height: 1;
        }

        .grid-accounts {
            gap: 0.8rem; /* dikurangi dari 1.0rem */
        }
    </style>
@endpush

@section('content')
    <main class="p-3"> <!-- dikurangi dari p-4 -->
       <!-- Header Section -->
        <div class="mb-4"> <!-- dikurangi dari mb-6 -->
            <div class="bg-white rounded-[40px] shadow-md p-3"> <!-- dikurangi dari p-4 -->
                <!-- Flex Container untuk Bagian Atas -->
                <div class="flex justify-between items-center">
                    <!-- Bagian Kiri - Icon dan Judul -->
                    <div class="flex items-center gap-2"> <!-- dikurangi dari gap-3 -->
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600"> <!-- dikurangi dari w-12 h-12 -->
                            <i class="fas fa-university text-lg"></i> <!-- dikurangi dari text-xl -->
                        </div>
                        <div>
                            <h1 class="text-lg font-bold leading-[0.8]"> <!-- dikurangi dari text-xl -->
                                Akun<br>Investasi<br>Saya
                            </h1>
                        </div>
                    </div>
                    
                    <!-- Bagian Kanan - Total Investasi -->
                    <div class="flex flex-col justify-center">
                        <p class="text-base text-gray-500">Total Investasi:</p> <!-- dikurangi dari text-lg -->
                        <p class="total-investment font-bold text-green-600">
                            Rp{{ number_format($investor->total_investment, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                
                <!-- Tombol Ajukan (Paling Bawah) -->
                <!-- <div class="mt-3 text-center"> 
                    <a href="https://wa.me/6289699497272?text=Assalamualaikum%20warohmatullahi%20wabarokatuh%2C%20Saya%20atas%20nama%20{{ urlencode(Auth::guard('investor')->user()->name) }}%20ingin%20mengajukan%20akun%20investasi%20baru%2C%20mohon%20untuk%20di%20proses"
                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-base font-bold rounded-[40px] hover:bg-green-700 transition-colors shadow-md"
                    target="_blank">
                        <i class="fas fa-plus-circle mr-1 text-xs"></i> Ajukan Tambah Akun
                    </a>
                </div> -->
            </div>
        </div>
        
        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"> <!-- dikurangi dari gap-4 -->
            @forelse($accounts as $account)
                <div class="account-card">
                    <!-- Account Header -->
                    <div class="account-header">
                        <div class="account-icon {{ $account->status === 'active' ? 'bg-green-500' : 'bg-gray-500' }}">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="account-info">
                            <h3 class="account-title">{{ $account->bank_name }}</h3>
                            <p class="account-number">{{ $account->account_number }}</p>
                        </div>
                        <span class="account-status {{ $account->status === 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ $account->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    <!-- Account Details -->
                    <div class="account-details">
                        <div class="detail-row">
                            <span class="detail-label">Sub-Total Investasi</span>
                            <span class="detail-value">Rp{{ number_format($account->total_investment, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Sub-Total Keuntungan</span>
                            <span class="detail-value">Rp{{ number_format($account->total_profit, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Sub-Total Dicairkan</span>
                            <span class="detail-value">Rp{{ number_format($account->total_withdrawals, 0, ',', '.') }}</span>
                        </div>
                        @if($account->saldo_tertahan != 0)
                            <div class="detail-row">
                                <span class="detail-label">Sub-Saldo Tertahan</span>
                                <span class="detail-value">Rp{{ number_format($account->saldo_tertahan, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Sub-Saldo Saat Ini</span>
                            <span class="detail-value">Rp{{ number_format($account->profit_balance - $account->saldo_tertahan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Account Footer -->
                    <div class="account-footer">
                        <a href="{{ route('investor.investments.show', $account->id) }}" class="detail-link">
                            Lihat Detail <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full account-card text-center py-4"> 
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-university text-2xl"></i> 
                    </div>
                    <h3 class="text-base font-bold text-gray-700">Belum ada akun investasi</h3>
                    <p class="text-gray-500 mt-1 text-xs">Mulai dengan menambahkan akun investasi Anda</p> 
                    <a href="https://wa.me/6289699497272?text=Assalamualaikum%20warohmatullahi%20wabarokatuh%2C%20Saya%20atas%20nama%20{{ urlencode(Auth::guard('investor')->user()->name) }}%20ingin%20mengajukan%20akun%20investasi%20baru%2C%20mohon%20untuk%20di%20proses"
                       class="add-account-btn mt-2 mx-auto"
                       target="_blank">
                        <i class="fab fa-whatsapp text-xs"></i> Ajukan Tambah Akun
                    </a>
                </div>
            @endforelse
        </div>
    </main>
@endsection
