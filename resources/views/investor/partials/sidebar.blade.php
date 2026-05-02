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
        <a href="{{ route('investor.dashboard') }}" class="flex items-center px-4 py-3 text-sm text-green-100 hover:bg-green-600">
            <i class="fas fa-chart-line w-6 text-center mr-3"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('investor.investments') }}" class="flex items-center px-4 py-3 text-sm bg-green-800 text-white">
            <i class="fas fa-wallet w-6 text-center mr-3"></i>
            <span>Investasi Saya</span>
            <span class="ml-auto w-1 h-6 bg-white rounded-l"></span>
        </a>
        <a href="{{ route('investor.profits') }}" class="flex items-center px-4 py-3 text-sm text-green-100 hover:bg-green-600">
            <i class="fas fa-hand-holding-usd w-6 text-center mr-3"></i>
            <span>Bagi Hasil</span>
        </a>
        <a href="{{ route('investor.withdrawals') }}" class="flex items-center px-4 py-3 text-sm text-green-100 hover:bg-green-600">
            <i class="fas fa-money-bill-wave w-6 text-center mr-3"></i>
            <span>Pencairan</span>
        </a>
        <a href="{{ route('investor.documents') }}" class="flex items-center px-4 py-3 text-sm text-green-100 hover:bg-green-600">
            <i class="fas fa-file-alt w-6 text-center mr-3"></i>
            <span>Dokumen</span>
        </a>
        <a href="{{ route('investor.profile') }}" class="flex items-center px-4 py-3 text-sm text-green-100 hover:bg-green-600">
            <i class="fas fa-user w-6 text-center mr-3"></i>
            <span>Profil Saya</span>
        </a>
    </nav>
</div>
