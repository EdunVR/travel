<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        :root {
            --primary-color: #2e7d32; /* Hijau utama */
            --primary-light: #4caf50; /* Hijau muda */
            --primary-dark: #1b5e20; /* Hijau tua */
            --secondary-color: #757575;
            --success-color: #388e3c;
            --info-color: #00796b;
            --light-color: #f8f9fa;
            --dark-color: #212121;
        }
        
        body {
            background-color: #ffffff;
            font-family: 'Figtree', sans-serif;
            padding-bottom: 80px; /* Untuk bottom nav di mobile */
        }
        
        /* Desktop Layout */
        .desktop-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            background: white;
            width: 250px;
            min-height: 100vh;
            transition: all 0.3s;
            position: fixed;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-right: 1px solid #e0e0e0;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }
        
        /* Sidebar Menu Items */
        .nav-link {
            color: var(--dark-color);
            padding: 12px 15px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 18px; /* Changed from text-2xl */
        }
        
        .nav-link:hover {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--primary-dark);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: var(--primary-color);
            color: white !important;
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Mobile Bottom Navigation */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100vw; /* Gunakan viewport width */
            max-width: 100vw;
            height: 80px;
            padding: 0;
            margin: 0;
            z-index: 1000;
            overflow: visible;
        }

        .nav-container {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: visible;
        }
        
        /* Background utama (emas) */
        .nav-background {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #FFD700; /* Warna emas */
            border-radius: 30px 30px 0 0;
            z-index: 1;
        }
        
        /* Dekorasi hijau (untuk item navigasi) */
        .nav-decoration {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            height: 80px;
            background-color: #2E7D32; /* Warna hijau */
            border-radius: 50px;
            z-index: 2;
            margin: 0 15px;
        }
        
        .nav-items {
            width: 100%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            z-index: 3;
            padding: 0 5%;
            gap: 10px;
            margin: 0;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            position: relative;
            z-index: 4;
            flex: 0 1 auto;
            min-width: 0;
            width: 20%;
            height: 100%;
            
        }
        
        .nav-item.active {
            color: #FFD700; /* Warna emas untuk item aktif */
        }
        
        /* Tombol home di tengah */
        .nav-item-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 20px;
            width: 60px;
            height: 60px;
            background-color: #2E7D32;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .nav-item:nth-child(2) { /* Bagi Hasil */
            margin-right: 30px; /* Tambahkan jarak ke home */
        }

        .nav-item:nth-child(4) { /* Pencairan */
            margin-left: 30px; /* Tambahkan jarak dari home */
        }

        /* Item di ujung (Investasi dan Dokumen) */
        .nav-item:nth-child(1),
        .nav-item:nth-child(5) {
            flex: 1; /* Mengisi sisa space */
        }
        
        .nav-item-center.active {
            background-color: #1B5E20; /* Hijau lebih gelap saat aktif */
        }
        
        .nav-item-center .nav-icon {
            font-size: 24px;
            color: white;
        }
        
        .nav-icon {
            font-size: 20px;
            margin-bottom: 4px;
        }
        
        .nav-label {
            font-size: 20px; /* Changed from 1.3rem */
            font-family: 'Dongle', sans-serif;
            font-weight: 400;
        }
        
        .nav-item-center .nav-label {
            position: absolute;
            bottom: -15px;
            color: #2E7D32; /* Warna hijau */
            font-weight: 700;
            font-size: 18px; /* Added specific size */
        }
        
        .nav-item-center.active .nav-label {
            color: #1B5E20; /* Hijau lebih gelap saat aktif */
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            
            .bottom-nav {
                display: block;
                align-items: center;
            }
            body {
                padding-bottom: 80px;
            }

            .top-nav, .top-nav-home {
                border-radius: 0 0 30px 30px;
                
            }
            
            .balance-actions {
                flex-wrap: wrap;
            }
            
            .balance-btn {
                flex: 1 1 45%;
                margin: 2px;
                min-width: auto;
            }
            
            .nav-item {
                font-size: 14px;
            }
            
            .nav-icon {
                font-size: 16px;
            }
            
            .nav-label {
                font-size: 16px; /* Changed from 1.1rem */
            }
            .logo_syirkah {
                height: 20px;
                max-height: 20px;
                width: auto;
            }
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 18px; /* Added */
        }
        
        /* Table Styling */
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-dark);
            font-size: 16px; /* Added */
        }
        
        /* Button Styling */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-size: 16px; /* Added */
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        /* Hover Effects */
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 5px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 0 0 50px 50px;
        }
        
        .user-greeting {
            font-family: 'Dongle', sans-serif;
            font-size: 18px; /* Changed from 1.8rem */
            color: #333;
            text-align: right;
        }
        
        .user-name {
            font-family: 'Dongle', sans-serif;
            font-size: 22px; /* Changed from 2.0rem */
            font-weight: 700;
            color: #2E7D32;
            margin-top: -18px;
            text-align: right;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #FFD700;
        }

        .top-nav, .top-nav-home {
            width: 100%;
            min-width: 100%;
            max-width: 100vw; /* Gunakan viewport width */
            margin: 0;
            box-sizing: border-box;
            position: relative;
            z-index: 100;
        }

        .logo_syirkah {
            height: 22px;
            max-height: 22px;
            width: auto;
        }

        /* Special Home Page Top Navigation */
        .top-nav-home {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 5px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 0 0 50px 50px;
        }
        
        .home-user-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .balance-container {
            width: 100%;
            text-align: center;
            margin-top: -15px;
        }
        
        .balance-label {
            font-family: 'Dongle', sans-serif;
            font-size: 20px; /* Changed from 1.5rem */
            color: #666;
        }
        
        .balance-amount {
            font-family: 'Dongle', sans-serif;
            font-size: 32px; /* Changed from 2.5rem */
            font-weight: 700;
            color: #2E7D32;
            margin-top: -15px;
        }
        
        .balance-actions {
            display: flex;
            justify-content: center;
            gap: 10px; /* Reduced gap between buttons */
        }
        
        .balance-btn {
            font-family: 'Dongle', sans-serif;
            font-size: 15px; /* Changed from 1.5rem */
            font-weight: 700;
            padding: 5px 15px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px; /* Fixed minimum width */
        }
        
        .withdraw-btn {
            background-color: #2E7D32;
            color: white;
        }
        
        .deposit-btn {
            background-color: #FFD700;
            color: #333;
        }
        
        .balance-btn i {
            margin-right: 5px;
            font-size: 15px; /* Changed from 1.2rem */
        }

        /* Modal styles */
        #withdrawalModal {
            font-size: 16px; /* Base font size for modal */
        }
        
        #withdrawalModal h3 {
            font-size: 20px; /* Modal title */
        }
        
        #withdrawalModal label {
            font-size: 14px; /* Form labels */
        }
        
        #withdrawalModal input,
        #withdrawalModal select,
        #withdrawalModal textarea {
            font-size: 16px; /* Form inputs */
        }
        
        #withdrawalModal button {
            font-size: 16px; /* Buttons */
        }
        
        #availableBalance {
            font-size: 24px; /* Balance display */
        }

        .nav-item.loading,
        .nav-item-center.loading {
            opacity: 0.6;
            position: relative;
        }

        .nav-item.loading::after,
        .nav-item-center.loading::after {
            content: "";
            position: absolute;
            top: 10px;
            right: 10px;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-top: 2px solid #FFD700;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .nav-icon.spinner {
            border: 2px solid white;
            border-top: 2px solid #FFD700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin-home 1s linear infinite;
        }

        @keyframes spin-home {
            to { transform: rotate(360deg); }
        }

        
    </style>
    @stack('styles')
</head>
<body>
    <!-- Desktop Layout -->
    <div class="desktop-layout">
        <!-- Sidebar (Desktop) -->
        <div class="sidebar d-none d-lg-block">
            <div class="sidebar-heading text-center py-4">
                <h4 style="color: var(--primary-color); font-size: 20px;">Investor Portal</h4>
            </div>
            <div class="nav flex-column px-2">
                <a href="{{ route('investor.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('investor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('investor.investments') }}" 
                   class="nav-link {{ request()->routeIs('investor.investments') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Rekening</span>
                </a>
                <a href="{{ route('investor.profits') }}" 
                   class="nav-link {{ request()->routeIs('investor.profits') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Bagi Hasil</span>
                </a>
                <a href="{{ route('investor.withdrawals') }}" 
                   class="nav-link {{ request()->routeIs('investor.withdrawals') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Status Pencairan</span>
                </a>
                <a href="{{ route('investor.documents') }}" 
                   class="nav-link {{ request()->routeIs('investor.documents') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Dokumen</span>
                </a>
                <a href="{{ route('investor.profile') }}" 
                   class="nav-link {{ request()->routeIs('investor.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation - Conditional Rendering -->
            @if(request()->routeIs('investor.dashboard'))
                <!-- Special Home Page Top Navigation -->
                <nav class="top-nav-home" id="topNavHome">
                    <div class="home-user-info">
                        <div>
                            <img src="{{ asset('img/logo_2.png') }}" alt="SUN" class="logo_syirkah">
                        </div>
                        <div>
                            <div class="user-greeting">Assalamualaikum!</div>
                            <div class="user-name">{{ Auth::guard('investor')->user()->name }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-info">
                                        @php
                                            $user = Auth::guard('investor')->user();
                                            $photo = $user->photo 
                                                        ? asset('storage/' . $user->photo) 
                                                        : ($user->jenis_kelamin === 'Perempuan' 
                                                            ? asset('img/investor_user_perempuan.png') 
                                                            : asset('img/investor_user.png'));
                                        @endphp

                                        <img src="{{ $photo }}" alt="User Avatar" class="user-avatar">
                                    </div>

                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="{{ route('investor.profile') }}" style="font-size: 16px;">
                                        <i class="fas fa-user me-2"></i>
                                        Profil
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('investor.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item" style="font-size: 16px;">
                                                <i class="fas fa-sign-out-alt me-2"></i>
                                                Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="balance-container">
                        <div class="balance-label">Saldo Total saat ini</div>
                        <div class="balance-amount">Rp{{ number_format($totalSaldo_semua, 0, ',', '.') }},-</div>
                        @if($totalSaldoTertahan != 0)
                            <div class="text-sm text-gray-500" style="margin-top: -15px;">
                                Terdapat saldo tertahan sebesar: <strong>Rp{{ number_format($totalSaldoTertahan, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                        <div class="balance-actions">
                            <button onclick="showWithdrawalModal()" class="balance-btn withdraw-btn">
                                <i class="fas fa-money-bill-wave"></i> Pencairan
                            </button>
                            <!-- <a href="https://wa.me/6289699497272?text=Assalamualaikum%20warohmatullahi%20wabarokatuh%2C%20Saya%20atas%20nama%20{{ urlencode(Auth::guard('investor')->user()->name) }}%20ingin%20menambah%20modal%20syirkah%2C%20mohon%20untuk%20di%20proses" 
                                class="balance-btn deposit-btn" 
                                target="_blank">
                                    <i class="fas fa-plus-circle"></i> Tambah Modal
                                </a> -->
                        </div>
                    </div>
                </nav>
                
                <nav class="top-nav d-none" id="topNavGeneral">
                    <div>
                        <img src="{{ asset('img/logo_2.png') }}" alt="SUN" class="logo_syirkah">
                    </div>
                    <div>
                        <div class="user-greeting">Assalamualaikum!</div>
                        <div class="user-name">{{ Auth::guard('investor')->user()->name }}</div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-info">
                                        @php
                                            $user = Auth::guard('investor')->user();
                                            $photo = $user->photo 
                                                        ? asset('storage/' . $user->photo) 
                                                        : ($user->jenis_kelamin === 'Perempuan' 
                                                            ? asset('img/investor_user_perempuan.png') 
                                                            : asset('img/investor_user.png'));
                                        @endphp

                                        <img src="{{ $photo }}" alt="User Avatar" class="user-avatar">
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('investor.profile') }}" style="font-size: 16px;">
                                    <i class="fas fa-user me-2"></i>
                                    Profil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('investor.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item" style="font-size: 16px;">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            @else
                <!-- General Top Navigation for other pages -->
                <nav class="top-nav" id="topNavGeneral">
                    <div>
                        <img src="{{ asset('img/logo_2.png') }}" alt="SUN" class="logo_syirkah">
                    </div>
                    <div>
                        <div class="user-greeting">Assalamualaikum!</div>
                        <div class="user-name">{{ Auth::guard('investor')->user()->name }}</div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-info">
                                        @php
                                            $user = Auth::guard('investor')->user();
                                            $photo = $user->photo 
                                                        ? asset('storage/' . $user->photo) 
                                                        : ($user->jenis_kelamin === 'Perempuan' 
                                                            ? asset('img/investor_user_perempuan.png') 
                                                            : asset('img/investor_user.png'));
                                        @endphp

                                        <img src="{{ $photo }}" alt="User Avatar" class="user-avatar">
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('investor.profile') }}" style="font-size: 16px;">
                                    <i class="fas fa-user me-2"></i>
                                    Profil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('investor.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item" style="font-size: 16px;">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            @endif


            <!-- Page Content -->
            <div class="container-fluid px-0">
                @include('partials.alert')
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="bottom-nav d-lg-none">
        <div class="nav-container">
            <!-- Background emas di lapisan paling belakang -->
            <div class="nav-background"></div>
            
            <!-- Dekorasi hijau untuk item navigasi -->
            <div class="nav-decoration"></div>
            
            <!-- Item navigasi -->
            <div class="nav-items">
                <a href="{{ route('investor.investments') }}" class="nav-item {{ request()->routeIs('investor.investments') ? 'active' : '' }}">
                    <i class="fas fa-wallet nav-icon"></i>
                    <span class="nav-label">Rekening</span>
                </a>
                
                <a href="{{ route('investor.profits') }}" class="nav-item {{ request()->routeIs('investor.profits') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <span class="nav-label">Bagi Hasil</span>
                </a>
                
                <!-- Tombol home di tengah -->
                <div class="nav-item-center {{ request()->routeIs('investor.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('investor.dashboard') }}" class="flex flex-col items-center">
                        <i class="fas fa-home nav-icon"></i>
                    </a>
                </div>
                
                <a href="{{ route('investor.withdrawals') }}" class="nav-item {{ request()->routeIs('investor.withdrawals') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave nav-icon"></i>
                    <span class="nav-label">Pencairan</span>
                </a>
                
                <a href="{{ route('investor.documents') }}" class="nav-item {{ request()->routeIs('investor.documents') ? 'active' : '' }}">
                    <i class="fas fa-file-alt nav-icon"></i>
                    <span class="nav-label">Dokumen</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Pencairan -->
    <div id="withdrawalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Ajukan Pencairan</h3>
                    <button onclick="hideWithdrawalModal()" class="text-gray-500 hover:text-gray-700">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form action="{{ route('investor.withdrawals.store') }}" method="POST" id="withdrawalForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="account_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Rekening</label>
                        <select id="account_id" name="account_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" onchange="updateBalance()"
                            style="font-family: 'Dongle', sans-serif; font-size: 18px;">
                            <option value="all">Semua Rekening (Total)</option>
                            @foreach($investor->accounts as $account)
                                <option value="{{ $account->id }}"
                                        data-balance="{{ $account->profit_balance - $account->saldo_tertahan }}"
                                        data-tertahan="{{ $account->saldo_tertahan }}">
                                    {{ $account->bank_name }} - {{ $account->account_number }} (Rp{{ number_format($account->profit_balance - $account->saldo_tertahan, 0, ',', '.') }})
                                </option>
                            @endforeach

                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Saldo Tersedia</label>
                        <div id="availableBalance" class="text-xl font-semibold">
                            Rp{{ number_format($investor->accounts->sum('profit_balance') - $investor->accounts->sum('saldo_tertahan'), 0, ',', '.') }}
                        </div>
                        
                        @if ($investor->accounts->sum('saldo_tertahan') > 0)
                            <div id="saldoTertahan" class="text-sm text-gray-500 mt-1" style="font-family: 'Dongle', sans-serif; font-size: 16px;">
                                Terdapat saldo tertahan Rp{{ number_format($investor->accounts->sum('saldo_tertahan'), 0, ',', '.') }}
                            </div>
                        @endif

                        <input type="hidden" id="maxAmount" value="{{ $investor->accounts->sum('profit_balance') }}">
                    </div>

                    
                    <div class="mb-4">
                        <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Jumlah Pencairan</label>
                        <input type="text" id="amount" name="amount_display" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            onkeyup="formatCurrency(this)"
                            inputmode="decimal"
                            placeholder="Contoh: 300000">
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                        <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideWithdrawalModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
                            <i data-feather="send" class="mr-2 w-4 h-4"></i> Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sidebar') && !e.target.closest('#sidebarToggle')) {
                document.querySelector('.sidebar').classList.remove('show');
            }
        });

        function showWithdrawalModal() {
            document.getElementById('withdrawalModal').classList.remove('hidden');
            const homeNav = document.getElementById('topNavHome');
            const generalNav = document.getElementById('topNavGeneral');

            if (homeNav) homeNav.classList.add('d-none');
            if (generalNav) generalNav.classList.remove('d-none');
            feather.replace();
        }

        function hideWithdrawalModal() {
            document.getElementById('withdrawalModal').classList.add('hidden');
            const homeNav = document.getElementById('topNavHome');
            const generalNav = document.getElementById('topNavGeneral');

            if (homeNav && window.location.pathname.includes('dashboard')) homeNav.classList.remove('d-none');
            if (generalNav && window.location.pathname.includes('dashboard')) generalNav.classList.add('d-none');
        }

        function updateBalance() {
            const accountSelect = document.getElementById('account_id');
            const balanceDisplay = document.getElementById('availableBalance');
            const saldoTertahanDisplay = document.getElementById('saldoTertahan');
            const maxAmountInput = document.getElementById('maxAmount');
            
            if (accountSelect.value === 'all') {
                const totalBalance = {{ $investor->accounts->sum('profit_balance') - $investor->accounts->sum('saldo_tertahan') }};
                balanceDisplay.textContent = 'Rp' + formatNumber(totalBalance);
                saldoTertahanDisplay.textContent = 'Terdapat saldo tertahan Rp' + formatNumber({{ $investor->accounts->sum('saldo_tertahan') }});
                maxAmountInput.value = totalBalance;
            } else {
                const selectedOption = accountSelect.options[accountSelect.selectedIndex];
                const balance = selectedOption.getAttribute('data-balance');
                const saldoTertahan = selectedOption.getAttribute('data-tertahan');
                balanceDisplay.textContent = 'Rp' + formatNumber(balance);
                saldoTertahanDisplay.textContent = 'Terdapat saldo tertahan Rp' + formatNumber(saldoTertahan);
                maxAmountInput.value = balance;
            }
        }

        function formatCurrency(input) {
            // Simpan posisi cursor
            const start = input.selectionStart;
            const end = input.selectionEnd;
            
            // Hapus semua karakter selain angka
            let value = input.value.replace(/[^\d]/g, '');
            
            // Konversi ke number
            const numericValue = parseInt(value, 10) || 0;
            
            // Format angka dengan titik sebagai pemisah ribuan
            if (value.length > 0) {
                value = numericValue.toLocaleString('id-ID');
            }
            
            // Set nilai kembali ke input
            input.value = value;
            
            // Kembalikan posisi cursor
            //input.setSelectionRange(start, end);
            
            // Periksa apakah nilai melebihi saldo maksimum
            const maxAmount = parseFloat(document.getElementById('maxAmount').value);
            
            if (numericValue > maxAmount) {
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
            const amountInput = document.getElementById('amount');
            
            // Konversi nilai format ke number murni
            const numericAmount = parseFloat(amountInput.value.replace(/[^\d]/g, '')) || 0;
            const maxAmount = parseFloat(document.getElementById('maxAmount').value);
            
            if (numericAmount > maxAmount) {
                e.preventDefault();
                alert('Jumlah pencairan tidak boleh melebihi saldo tersedia');
                amountInput.focus();
                return;
            }
            
            // Buat input hidden untuk nilai numerik
            const hiddenAmount = document.createElement('input');
            hiddenAmount.type = 'hidden';
            hiddenAmount.name = 'amount';
            hiddenAmount.value = numericAmount;
            
            // Hapus input hidden sebelumnya jika ada
            const existingHidden = this.querySelector('input[name="amount"][type="hidden"]');
            if (existingHidden) {
                this.removeChild(existingHidden);
            }
            
            this.appendChild(hiddenAmount);
        });

        // Inisialisasi saat modal dibuka
        document.getElementById('withdrawalModal').addEventListener('shown', function() {
            updateBalance();
            feather.replace();
        });
    </script>
    <script>
        // Untuk semua item nav biasa
        document.querySelectorAll('.bottom-nav .nav-item').forEach(function(navItem) {
            navItem.addEventListener('click', function(e) {
                const parent = navItem.closest('.nav-item');
                if (!parent.classList.contains('active')) {
                    parent.classList.add('loading');
                    setTimeout(() => {
                        parent.classList.remove('loading');
                    }, 4000);
                }
            });
        });

        // Untuk tombol Home di tengah
        const homeLink = document.querySelector('.nav-item-center a');
        const homeIcon = homeLink.querySelector('.nav-icon');

        homeLink.addEventListener('click', function (e) {
            if (!homeLink.closest('.nav-item-center').classList.contains('active')) {
                // Ganti icon menjadi spinner
                homeIcon.classList.remove('fa-home');
                homeIcon.classList.add('spinner');

                // Optional: auto-revert spinner setelah 4 detik
                setTimeout(() => {
                    homeIcon.classList.remove('spinner');
                    homeIcon.classList.add('fa-home');
                }, 4000);
            }
        });
    </script>


    @stack('scripts')
</body>
</html>
