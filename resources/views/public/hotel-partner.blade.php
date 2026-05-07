<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Partner - HM Tour & Travel</title>
    <meta name="description" content="Hotel partner bintang 4 dan 5 di Makkah dan Madinah yang bekerja sama dengan HM Tour & Travel.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-brand':  '#2E7D32',
                        'green-mid':    '#4CAF50',
                        'green-light':  '#81C784',
                        'green-pale':   '#E8F5E9',
                    }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Nunito', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        html { scroll-behavior: smooth; }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(46,125,50,0.15);
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png') }}"
                     alt="HM Tour" class="h-12 w-auto object-contain"
                     onerror="this.style.display='none'">
            </a>
            <a href="{{ url('/') }}" class="text-green-brand hover:text-green-mid text-sm font-semibold">
                <i class="fas fa-home mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</nav>

<!-- Header -->
<section class="bg-gradient-to-br from-green-50 to-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-playfair text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
            Hotel <span class="text-green-brand">Partner</span>
        </h1>
        <p class="text-gray-600 text-lg">
            Hotel bintang 4 dan 5 pilihan terbaik di Makkah dan Madinah
        </p>
    </div>
</section>

<!-- Hotels Grid -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Makkah Hotels -->
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-kaaba text-green-brand"></i>
                Hotel di Makkah
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $makkahHotels = [
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'), 'name' => 'Hotel Al-Shohada', 'stars' => 5, 'distance' => '100m dari Masjidil Haram'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Movenpick-Mekkah-Umroh-Sesuai-Sunnah.jpeg'), 'name' => 'Movenpick Hotel', 'stars' => 5, 'distance' => '200m dari Masjidil Haram'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'), 'name' => 'Elaf Kinda Hotel', 'stars' => 4, 'distance' => '300m dari Masjidil Haram'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'), 'name' => 'Dar Al Eiman Royal', 'stars' => 5, 'distance' => '150m dari Masjidil Haram'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'), 'name' => 'Anjum Hotel Makkah', 'stars' => 5, 'distance' => '250m dari Masjidil Haram'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'), 'name' => 'Swissotel Makkah', 'stars' => 5, 'distance' => '100m dari Masjidil Haram'],
                ];
                @endphp
                @foreach($makkahHotels as $hotel)
                <a href="https://www.google.com/search?q={{ urlencode($hotel['name'] . ' Makkah Saudi Arabia') }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="block card-hover rounded-2xl overflow-hidden border border-green-100 shadow-sm group bg-white">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $hotel['img'] }}" alt="{{ $hotel['name'] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 flex gap-0.5">
                            @for($i = 0; $i < $hotel['stars']; $i++)
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 text-base mb-2 break-words">{{ $hotel['name'] }}</h3>
                        <p class="text-green-brand text-sm font-medium mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>Makkah
                        </p>
                        <p class="text-gray-500 text-xs">
                            <i class="fas fa-walking mr-1"></i>{{ $hotel['distance'] }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Madinah Hotels -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-mosque text-green-brand"></i>
                Hotel di Madinah
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $madinahHotels = [
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Golden Tulip Al-Zahabi', 'stars' => 5, 'distance' => '150m dari Masjid Nabawi'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Pullman Zamzam Madina', 'stars' => 5, 'distance' => '100m dari Masjid Nabawi'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Anwar Al Madinah Movenpick', 'stars' => 5, 'distance' => '200m dari Masjid Nabawi'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Dar Al Iman Intercontinental', 'stars' => 5, 'distance' => '250m dari Masjid Nabawi'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Elaf Taiba Hotel', 'stars' => 4, 'distance' => '300m dari Masjid Nabawi'],
                    ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'), 'name' => 'Shaza Al Madina', 'stars' => 5, 'distance' => '180m dari Masjid Nabawi'],
                ];
                @endphp
                @foreach($madinahHotels as $hotel)
                <a href="https://www.google.com/search?q={{ urlencode($hotel['name'] . ' Madinah Saudi Arabia') }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="block card-hover rounded-2xl overflow-hidden border border-green-100 shadow-sm group bg-white">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $hotel['img'] }}" alt="{{ $hotel['name'] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 flex gap-0.5">
                            @for($i = 0; $i < $hotel['stars']; $i++)
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 text-base mb-2 break-words">{{ $hotel['name'] }}</h3>
                        <p class="text-green-brand text-sm font-medium mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>Madinah
                        </p>
                        <p class="text-gray-500 text-xs">
                            <i class="fas fa-walking mr-1"></i>{{ $hotel['distance'] }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-12 bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-8 border border-green-200">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-brand rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Hotel Pilihan Terbaik</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Semua hotel partner kami dipilih dengan cermat berdasarkan kualitas, lokasi strategis dekat Masjidil Haram dan Masjid Nabawi, serta fasilitas yang nyaman untuk jemaah.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Kami bekerja sama dengan hotel bintang 4 dan 5 untuk memastikan kenyamanan maksimal selama perjalanan ibadah Anda. Jarak tempuh ke masjid yang dekat memudahkan Anda untuk beribadah dengan khusyuk.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-green-brand py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-white mb-4">
            Nikmati Kenyamanan Hotel Terbaik
        </h2>
        <p class="text-green-100 mb-6">
            Pilih paket umroh kami dan rasakan pengalaman menginap di hotel pilihan
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white text-green-brand font-bold px-8 py-3 rounded-full hover:bg-green-50 transition-all">
            <i class="fas fa-kaaba"></i> Lihat Paket Umroh
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} HM Tour & Travel. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
