<!-- Updated Navigation -->
<nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <img src="{{ asset('img/logo-auto.png') }}" alt="ManufakturPro" class="h-10 object-contain">
                </a>
            </div>
            
            <div class="hidden md:flex space-x-8">
                <a href="{{ url('/') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Beranda</a>
                <a href="{{ url('/#products') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Produk</a>
                <a href="{{ url('/#features') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Keunggulan</a>
                <a href="{{ url('/#about') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Tentang Kami</a>
                <a href="{{ url('/login') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Masuk_Syirkah</a>
            </div>
            
            <button class="md:hidden focus:outline-none" @click="open = !open">
                <a href="{{ url('/login') }}" class="text-gray-800 hover:text-indigo-600 font-medium">Masuk_Syirkah</a>
            </button>
        </div>

        <!-- New Bottom Navigation (Mobile Only) -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-lg z-40 border-t border-gray-200">
            <div class="flex justify-around">
                <a href="{{ url('/') }}" class="flex flex-col items-center justify-center py-3 px-4 text-xs text-gray-600 hover:text-indigo-600">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Beranda
                </a>
                <a href="{{ url('/#products') }}" class="flex flex-col items-center justify-center py-3 px-4 text-xs text-gray-600 hover:text-indigo-600">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Produk
                </a>
                <a href="{{ url('/#features') }}" class="flex flex-col items-center justify-center py-3 px-4 text-xs text-gray-600 hover:text-indigo-600">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Keunggulan
                </a>
                <a href="{{ url('/#about') }}" class="flex flex-col items-center justify-center py-3 px-4 text-xs text-gray-600 hover:text-indigo-600">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Tentang Kami
                </a>
            </div>
        </div>

        <!-- Floating Syirkah Button (Mobile Only) -->
        <div class="md:hidden fixed right-4 bottom-20 z-50">
            <a href="#syirkah" class="flex items-center justify-center w-14 h-14 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </a>
        </div>
    </nav>
