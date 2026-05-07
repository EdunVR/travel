<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Paket Umroh & Haji - HM Tour & Travel</title>
    <meta name="description" content="Lihat semua paket umroh dan haji dari HM Tour & Travel. Pilih paket yang sesuai dengan kebutuhan Anda.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-brand':  '#2E7D32',
                        'green-mid':    '#4CAF50',
                        'green-light':  '#81C784',
                        'green-pale':   '#E8F5E9',
                        'green-accent': '#00C853',
                    }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Nunito', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        
        .text-green-gradient {
            background: linear-gradient(135deg, #2E7D32, #4CAF50, #81C784);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-green-gradient {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(46,125,50,0.15);
        }
        
        html { scroll-behavior: smooth; }
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
<section class="bg-gradient-to-br from-green-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Semua Paket <span class="text-green-gradient">Umroh & Haji</span>
            </h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Pilih paket yang sesuai dengan kebutuhan dan budget Anda. Semua paket sudah termasuk visa, tiket, hotel, dan pembimbing berpengalaman.
            </p>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
            <form action="{{ route('public.paket.index') }}" method="GET" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Perusahaan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-green-brand mr-1"></i> Perusahaan
                    </label>
                    <select name="outlet_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-brand focus:border-transparent">
                        <option value="">Semua Perusahaan</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id_outlet }}" {{ request('outlet_id') == $outlet->id_outlet ? 'selected' : '' }}>
                            {{ $outlet->nama_outlet }}{{ $outlet->kota ? ' - '.$outlet->kota : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar text-green-brand mr-1"></i> Bulan Keberangkatan
                    </label>
                    <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-brand focus:border-transparent">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Paket -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-green-brand mr-1"></i> Tipe Paket
                    </label>
                    <select name="package_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-brand focus:border-transparent">
                        <option value="">Semua Tipe</option>
                        @foreach($packageTypes as $type)
                        <option value="{{ $type }}" {{ request('package_type') == $type ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-gradient text-white font-semibold px-6 py-2.5 rounded-lg hover:opacity-90 transition-all">
                        <i class="fas fa-search mr-2"></i> Cari Paket
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['outlet_id', 'bulan', 'package_type']))
            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-filter mr-1"></i> Filter aktif
                </div>
                <a href="{{ route('public.paket.index') }}" class="text-sm text-green-brand hover:text-green-mid font-semibold">
                    <i class="fas fa-times mr-1"></i> Reset Filter
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Packages Grid -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($packages->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border border-green-100">
            <i class="fas fa-search text-green-200 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium mb-4">Tidak ada paket yang sesuai dengan filter Anda.</p>
            <a href="{{ route('public.paket.index') }}" class="inline-block text-green-brand hover:underline">
                Lihat semua paket
            </a>
        </div>
        @else
        <div class="mb-6 flex items-center justify-between">
            <p class="text-gray-600">
                Menampilkan <strong>{{ $packages->count() }}</strong> dari <strong>{{ $packages->total() }}</strong> paket
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($packages as $package)
            <x-package-card :package="$package" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $packages->links() }}
        </div>
        @endif
    </div>
</section>

<!-- CTA -->
<section class="bg-green-brand py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-white mb-4">
            Butuh Bantuan Memilih Paket?
        </h2>
        <p class="text-green-100 mb-6">
            Tim kami siap membantu Anda menemukan paket yang paling sesuai dengan kebutuhan dan budget Anda.
        </p>
        <a href="https://wa.me/628976688800?text=Assalamu'alaikum, saya ingin konsultasi paket umroh HM Tour"
           target="_blank"
           class="inline-flex items-center gap-2 bg-white text-green-brand font-bold px-8 py-3 rounded-full hover:bg-green-50 transition-all">
            <i class="fab fa-whatsapp text-xl"></i> Konsultasi Gratis
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} HM Tour & Travel. All rights reserved.</p>
    </div>
</footer>

<!-- Crop Display Helper -->
<script src="{{ asset('js/crop-display.js') }}"></script>

</body>
</html>
