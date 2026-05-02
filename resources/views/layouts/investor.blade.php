<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Investor - {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-green-700 text-white shadow-lg">
            <div class="p-4 flex items-center space-x-3 border-b border-green-600">
                <img src="{{ asset('images/logo-investor.png') }}" alt="Logo" class="h-10">
                <span class="font-bold text-xl">Portal Investor</span>
            </div>
            <div class="p-4 border-b border-green-600 flex items-center space-x-3">
                <img src="{{ auth()->guard('investor')->user()->photoUrl }}" 
                     class="h-10 w-10 rounded-full object-cover border-2 border-white">
                <div>
                    <div class="font-medium">{{ auth()->guard('investor')->user()->name }}</div>
                    <div class="text-xs text-green-100">{{ auth()->guard('investor')->user()->email }}</div>
                </div>
            </div>
            <nav class="mt-4">
                <x-investor.nav-link href="{{ route('investor.dashboard') }}" icon="fas fa-chart-line" :active="request()->routeIs('investor.dashboard')">
                    Dashboard
                </x-investor.nav-link>
                <x-investor.nav-link href="{{ route('investor.accounts') }}" icon="fas fa-wallet" :active="request()->routeIs('investor.accounts')">
                    Investasi Saya
                </x-investor.nav-link>
                <x-investor.nav-link href="{{ route('investor.profits') }}" icon="fas fa-hand-holding-usd" :active="request()->routeIs('investor.profits')">
                    Bagi Hasil
                </x-investor.nav-link>
                <x-investor.nav-link href="{{ route('investor.withdrawals') }}" icon="fas fa-money-bill-wave" :active="request()->routeIs('investor.withdrawals')">
                    Pencairan
                </x-investor.nav-link>
                <x-investor.nav-link href="{{ route('investor.documents') }}" icon="fas fa-file-alt" :active="request()->routeIs('investor.documents')">
                    Dokumen
                </x-investor.nav-link>
                <x-investor.nav-link href="{{ route('investor.profile') }}" icon="fas fa-user" :active="request()->routeIs('investor.profile')">
                    Profil Saya
                </x-investor.nav-link>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 rounded-full hover:bg-gray-100">
                            <i class="fas fa-bell text-gray-600"></i>
                        </button>
                        <form method="POST" action="{{ route('investor.logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-green-700">
                                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
