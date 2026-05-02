<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT. Pionir Briket Utama | Produsen Briket Kayu Berkualitas</title>
    <meta name="description" content="PT. Pionir Briket Utama - Produsen briket kayu berkualitas tinggi dengan nilai kalori 4200-4600 kcal/kg untuk solusi energi terbarukan.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a472a',  // Hijau tua sebagai warna utama
                        secondary: '#2e7d32', // Hijau sedang
                        accent: '#ff9800',    // Oranye sebagai aksen
                        energy: '#ff5722',    // Merah oranye untuk energi
                        dark: '#1f2937',
                        light: '#f9fafb'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-reverse': 'float-reverse 5s ease-in-out infinite',
                        'pulse-slow': 'pulse 5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'wave': 'wave 8s linear infinite',
                        'fire-flicker': 'fire-flicker 2s ease-in-out infinite alternate',
                        'energy-pulse': 'energy-pulse 3s ease-out infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'slide-in': 'slideIn 1s ease-out forwards',
                        'fade-in-up': 'fadeInUp 1s ease-out forwards'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        'float-reverse': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(15px)' }
                        },
                        wave: {
                            '0%': { transform: 'rotate(0deg)' },
                            '10%': { transform: 'rotate(-5deg)' },
                            '20%': { transform: 'rotate(10deg)' },
                            '30%': { transform: 'rotate(-5deg)' },
                            '40%': { transform: 'rotate(10deg)' },
                            '50%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(0deg)' }
                        },
                        'fire-flicker': {
                            '0%': { opacity: '0.8', transform: 'scale(1)' },
                            '100%': { opacity: '1', transform: 'scale(1.1)' }
                        },
                        'energy-pulse': {
                            '0%': { opacity: '0', transform: 'scale(0.5)' },
                            '50%': { opacity: '1', transform: 'scale(1.2)' },
                            '100%': { opacity: '0', transform: 'scale(0.5)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        },
                        fadeInUp: {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GSAP for advanced animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
            background-color: #f9fafb;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, rgba(26, 71, 42, 0.9) 0%, rgba(46, 125, 50, 0.8) 100%);
        }
        
        .hero-section {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        
        .product-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .product-card:hover::before {
            opacity: 1;
        }
        
        .card-hover-effect {
            transition: all 0.3s ease;
        }
        
        .card-hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .skema-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }
        
        .skema-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #2e7d32;
        }
        
        .skema-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #1a472a, #2e7d32);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .skema-card:hover::after {
            transform: scaleX(1);
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #2e7d32;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }

        .energy-gradient-background {
            background: linear-gradient(135deg, #1a472a, #2e7d32);
        }

        .energy-gradient-text {
            background: linear-gradient(135deg, #1a472a, #2e7d32);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a472a, #2e7d32);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        
        .btn-outline {
            transition: all 0.3s ease;
            border: 2px solid #2e7d32;
        }
        
        .btn-outline:hover {
            background-color: #2e7d32;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #1a472a, #2e7d32);
            margin: 12px auto 0;
            border-radius: 2px;
        }
        
        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }
        
        .shape-1 {
            top: 20%;
            left: 5%;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-2 {
            bottom: 15%;
            right: 8%;
            animation: float-reverse 7s ease-in-out infinite;
        }
        
        .shape-3 {
            top: 40%;
            right: 10%;
            animation: float 9s ease-in-out infinite;
        }
        
        .fire-animation {
            position: relative;
            animation: fire-flicker 2s ease-in-out infinite alternate;
        }
        
        .energy-spark {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #ff9800;
            border-radius: 50%;
            filter: blur(2px);
            animation: energy-pulse 2s ease-out infinite;
        }
        
        .masked-image {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C150,200 350,0 500,100 L500,500 L0,500 Z' fill='%23000'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C150,200 350,0 500,100 L500,500 L0,500 Z' fill='%23000'/%3E%3C/svg%3E");
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-size: cover;
            mask-size: cover;
        }
        
        .curve-divider {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }
        
        .curve-divider svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 100px;
        }
        
        .curve-divider .shape-fill {
            fill: #FFFFFF;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(26, 71, 42, 0.1) 0%, rgba(46, 125, 50, 0.1) 100%);
            border-radius: 50%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: rotateY(180deg);
            background: linear-gradient(135deg, #1a472a, #2e7d32);
            color: white;
        }
        
        .company-logo {
            transition: all 0.3s ease;
        }
        
        .company-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }
        
        .floating-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #2e7d32;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
            animation: pulse-slow 3s infinite;
        }
        
        .testimonial-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1a472a, #2e7d32);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-card:hover::before {
            transform: scaleX(1);
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 50px;
            border: 2px solid white;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .scroll-indicator::before {
            content: '';
            position: absolute;
            top: 8px;
            width: 6px;
            height: 6px;
            background-color: white;
            border-radius: 50%;
            animation: scroll-down 2s infinite;
        }
        
        @keyframes scroll-down {
            0% {
                transform: translateY(0);
                opacity: 1;
            }
            50% {
                transform: translateY(10px);
                opacity: 0.5;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        .floating-keywords {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 20px;
            z-index: 2;
        }
        
        .keyword-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.5s ease;
            cursor: default;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .keyword-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(0) scale(1.05);
        }
        
        .keyword-item i {
            margin-right: 8px;
        }
        
        .product-card {
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .product-card:hover {
            border-color: #2e7d32;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .product-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #1a472a, #2e7d32);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .product-card:hover::after {
            transform: scaleX(1);
        }

        /* Animation for product cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.3s; }
        .product-card:nth-child(3) { animation-delay: 0.5s; }

        [x-cloak] { display: none !important; }

        /* Tambahkan ini di bagian style */
        .section-with-bg {
            background-image: url('{{ asset('img/sketch.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .section-with-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.85); /* Overlay putih dengan opacity 85% */
        }
        
        .section-with-bg > * {
            position: relative;
            z-index: 1;
        }

        /* Animasi ilustrasi */
        .floating-briket {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse-energy {
            animation: pulse-slow 3s infinite;
        }
        
        .bounce-energy {
            animation: bounce-slow 2s infinite;
        }
        
        .slide-in-left {
            animation: slideIn 1s ease-out forwards;
        }
        
        .fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }
        
        .energy-wave {
            position: relative;
            overflow: hidden;
        }
        
        .energy-wave::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: wave 3s linear infinite;
        }
        
        .illustration-container {
            position: relative;
            width: 100%;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .floating-icon {
            position: absolute;
            animation: float 5s ease-in-out infinite;
        }
        
        .icon-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .icon-2 { top: 20%; right: 15%; animation-delay: 1s; }
        .icon-3 { bottom: 15%; left: 20%; animation-delay: 2s; }
        .icon-4 { bottom: 10%; right: 10%; animation-delay: 1.5s; }
        
        /* Hero Section dengan foto di samping */
        .hero-content {
            display: flex;
            align-items: center;
            min-height: 90vh;
        }
        
        .hero-text {
            flex: 1;
            padding-right: 2rem;
        }
        
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .hero-image img {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            animation: float 6s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text {
                padding-right: 0;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body class="section-with-bg" x-data="{ openModal: null }">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ open: false }">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <div class="h-10 w-10 bg-primary rounded-full flex items-center justify-center text-white font-bold mr-2">PBU</div>
                    <span class="text-xl font-bold text-gray-800">PT. Pionir Briket Utama</span>
                </a>
            </div>
            
            <div class="hidden md:flex space-x-8">
                <a href="#home" class="text-gray-800 hover:energy-gradient-text font-medium nav-link">Beranda</a>
                <a href="#products" class="text-gray-800 hover:energy-gradient-text font-medium nav-link">Produk</a>
                <a href="#about" class="text-gray-800 hover:energy-gradient-text font-medium nav-link">Tentang Kami</a>
                <a href="#vision" class="text-gray-800 hover:energy-gradient-text font-medium nav-link">Visi Misi</a>
                <a href="#contact" class="text-gray-800 hover:energy-gradient-text font-medium nav-link">Kontak</a>
            </div>
            
            <button @click="open = !open" class="md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden bg-white shadow-lg">
            <div class="px-4 py-2 space-y-2">
                <a href="#home" class="block py-2 text-gray-800 hover:energy-gradient-text">Beranda</a>
                <a href="#products" class="block py-2 text-gray-800 hover:energy-gradient-text">Produk</a>
                <a href="#about" class="block py-2 text-gray-800 hover:energy-gradient-text">Tentang Kami</a>
                <a href="#vision" class="block py-2 text-gray-800 hover:energy-gradient-text">Visi Misi</a>
                <a href="#contact" class="block py-2 text-gray-800 hover:energy-gradient-text">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section dengan Foto di Samping -->
    <section id="home" class="relative overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-90"></div>
            
            <!-- Pola energi -->
            <div class="energy-pattern absolute inset-0 opacity-10"></div>
            
            <!-- Animasi ilustrasi -->
            <div class="floating-icon icon-1">
                <i class="fas fa-leaf text-white text-2xl opacity-70"></i>
            </div>
            <div class="floating-icon icon-2">
                <i class="fas fa-fire text-accent text-2xl opacity-70"></i>
            </div>
            <div class="floating-icon icon-3">
                <i class="fas fa-bolt text-energy text-2xl opacity-70"></i>
            </div>
            <div class="floating-icon icon-4">
                <i class="fas fa-recycle text-secondary text-2xl opacity-70"></i>
            </div>
        </div>

        <!-- Content dengan layout flex -->
        <div class="container mx-auto px-4">
            <div class="hero-content">
                <!-- Teks Hero -->
                <div class="hero-text" data-aos="fade-right">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">PT. Pionir Briket Utama</h1>
                    <p class="text-xl md:text-2xl text-gray-300 mb-8">Produsen Briket Kayu Berkualitas Tinggi untuk Solusi Energi Terbarukan</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#products" class="btn-primary text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                            Lihat Produk
                        </a>
                        <a href="https://wa.me/6285880588812?text=Halo%20PT.%20Pionir%20Briket%20Utama%2C%20saya%20tertarik%20dengan%20produk%20briket%20kayu%20Anda.%20Bisa%20saya%20tahu%20lebih%20lanjut%3F" 
                            target="_blank" class="btn-outline border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp Kami
                        </a>
                    </div>
                    
                    <!-- Spesifikasi singkat -->
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-fire text-accent text-xl mr-2"></i>
                                <div>
                                    <p class="text-white font-bold">4200-4600 kcal/kg</p>
                                    <p class="text-gray-300 text-sm">Nilai Kalori Tinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-tint text-accent text-xl mr-2"></i>
                                <div>
                                    <p class="text-white font-bold">3.0-4.5%</p>
                                    <p class="text-gray-300 text-sm">Kadar Air Rendah</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gambar Hero -->
                <div class="hero-image" data-aos="fade-left">
                    <img src="https://i0.wp.com/astromesin.com/wp-content/uploads/2017/10/Briket-Arang.jpg?resize=1000%2C666&ssl=1" 
                         alt="Briket Kayu Berkualitas PT. Pionir Briket Utama">
                </div>
            </div>
        </div>

        <!-- Tech Badges -->
        <div class="absolute right-8 bottom-8 hidden lg:block">
            <div class="flex flex-col gap-3">
                <div class="energy-badge cursor-pointer">
                    <i class="fas fa-fire text-accent"></i>
                    <span>Nilai Kalori Tinggi</span>
                </div>
                <div class="energy-badge cursor-pointer">
                    <i class="fas fa-leaf text-accent"></i>
                    <span>Ramah Lingkungan</span>
                </div>
                <div class="energy-badge cursor-pointer">
                    <i class="fas fa-bolt text-accent"></i>
                    <span>Energi Terbarukan</span>
                </div>
            </div>
        </div>
    </section>

    <style>
        .energy-pattern {
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(255, 152, 0, 0.3) 1px, transparent 0),
                radial-gradient(circle at 1px 1px, rgba(255, 152, 0, 0.3) 1px, transparent 0);
            background-size: 20px 20px;
        }
        
        .energy-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 8px 15px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .energy-badge:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }
    </style>

    <!-- Tentang Kami Section (tanpa spesifikasi) -->
    <section id="about" class="py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2" data-aos="fade-right">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Tentang <span class="energy-gradient-text">PT. Pionir Briket Utama</span></h2>
                        <div class="w-20 h-1 bg-secondary mb-6"></div>

                        <p class="text-gray-600 mb-4">
                            PT. Pionir Briket Utama adalah perusahaan yang bergerak di bidang produksi dan penjualan briket kayu berkualitas tinggi. 
                            Sebagai pelopor dalam industri energi terbarukan, kami berkomitmen untuk menyediakan solusi energi yang ramah lingkungan, 
                            efisien, dan berkelanjutan bagi berbagai sektor industri dan rumah tangga.
                        </p>
                        <p class="text-gray-600 mb-4">
                            Dengan fokus pada inovasi dan kualitas, kami memproduksi briket kayu dengan spesifikasi unggul yang memenuhi standar 
                            internasional. Produk kami telah terbukti memberikan performa tinggi dengan nilai kalori yang konsisten, menjadikannya 
                            pilihan tepat untuk berbagai aplikasi yang membutuhkan sumber energi yang andal.
                        </p>
                        <p class="text-gray-600 mb-6">
                            Didukung oleh tim profesional yang berpengalaman dan fasilitas produksi modern, kami terus berupaya untuk 
                            mengembangkan produk yang tidak hanya memenuhi kebutuhan pasar tetapi juga berkontribusi positif terhadap 
                            pelestarian lingkungan.
                        </p>
                        
                        <!-- Keunggulan singkat -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <span class="text-gray-700">Bahan Baku Berkualitas</span>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <span class="text-gray-700">Proses Produksi Modern</span>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <span class="text-gray-700">Ramah Lingkungan</span>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <span class="text-gray-700">Kualitas Terjamin</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2" data-aos="fade-left">
                    <!-- Ilustrasi Briket -->
                    <div class="illustration-container">
                        <div class="floating-briket">
                            <img src="https://i0.wp.com/astromesin.com/wp-content/uploads/2017/10/Briket-Arang.jpg?resize=1000%2C666&ssl=1" 
                                 alt="Briket Kayu Berkualitas" 
                                 class="w-full max-w-md rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section id="vision" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Visi & <span class="energy-gradient-text">Misi</span> Perusahaan</h2>
                <div class="w-20 h-1 bg-secondary mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Visi -->
                <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-right">
                    <div class="flex items-center mb-6">
                        <div class="bg-primary text-white p-4 rounded-full mr-4">
                            <i class="fas fa-eye text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Visi</h3>
                    </div>
                    <p class="text-gray-600 text-lg">
                        Menjadi perusahaan produsen briket kayu terdepan yang mengedepankan inovasi, kualitas, 
                        dan keberlanjutan dalam menyediakan solusi energi terbarukan untuk mendukung pertumbuhan 
                        industri hijau di Indonesia dan pasar global.
                    </p>
                </div>

                <!-- Misi -->
                <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-left">
                    <div class="flex items-center mb-6">
                        <div class="bg-secondary text-white p-4 rounded-full mr-4">
                            <i class="fas fa-bullseye text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Misi</h3>
                    </div>
                    <ul class="text-gray-600 text-lg space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-3"></i>
                            <span>Memproduksi briket kayu berkualitas tinggi dengan nilai kalori optimal dan konsisten.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-3"></i>
                            <span>Mengembangkan produk inovatif yang ramah lingkungan dan berkelanjutan.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-3"></i>
                            <span>Memberikan pelayanan terbaik kepada pelanggan dengan solusi energi yang efisien.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-3"></i>
                            <span>Berkontribusi aktif dalam pengembangan industri energi terbarukan di Indonesia.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Unggulan -->
    <section id="products" class="py-16 bg-gradient-to-b from-white to-green-50 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Produk <span class="energy-gradient-text">Unggulan</span> Kami</h2>
                <div class="w-20 h-1 bg-secondary mx-auto"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Briket kayu berkualitas tinggi dengan nilai kalori optimal untuk berbagai kebutuhan industri dan rumah tangga</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Gambar Briket -->
                <div class="flex justify-center" data-aos="fade-right">
                    <div class="relative">
                        <img src="https://i0.wp.com/astromesin.com/wp-content/uploads/2017/10/Briket-Arang.jpg?resize=1000%2C666&ssl=1" 
                             alt="Briket Kayu Berkualitas" 
                             class="w-full max-w-lg rounded-xl shadow-2xl energy-wave">
                        <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-lg">
                            <div class="flex items-center">
                                <div class="bg-energy text-white p-2 rounded-full mr-3">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">Nilai Kalori Tinggi</p>
                                    <p class="text-sm text-gray-600">4200-4600 kcal/kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detail Produk -->
                <div data-aos="fade-left">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Briket Kayu Premium</h3>
                        <p class="text-gray-600 mb-6">Briket kayu berkualitas tinggi dengan pembakaran efisien dan nilai kalori konsisten untuk berbagai aplikasi industri dan rumah tangga.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Bahan Baku Berkualitas</h4>
                                    <p class="text-gray-600 text-sm">Dari kayu pilihan yang ramah lingkungan</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Pembakaran Optimal</h4>
                                    <p class="text-gray-600 text-sm">Panas tinggi dan waktu bakar lama</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Emisi Rendah</h4>
                                    <p class="text-gray-600 text-sm">Ramah lingkungan dengan emisi karbon minimal</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-full mr-3 mt-1">
                                    <i class="fas fa-check text-secondary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Mudah Disimpan</h4>
                                    <p class="text-gray-600 text-sm">Bentuk seragam memudahkan penyimpanan</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg mb-6">
                            <h4 class="font-bold text-gray-800 mb-2">Spesifikasi Teknis</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Nilai Kalori</p>
                                    <p class="font-bold text-gray-800">4200-4600 kcal/kg</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kadar Air</p>
                                    <p class="font-bold text-gray-800">3.0-4.5%</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Bentuk</p>
                                    <p class="font-bold text-gray-800">Seragam</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kemasan</p>
                                    <p class="font-bold text-gray-800">Karung 20-25kg</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="https://wa.me/6285880588812?text=Halo%20PT.%20Pionir%20Briket%20Utama%2C%20saya%20tertarik%20dengan%20produk%20briket%20kayu%20Anda.%20Bisa%20saya%20dapatkan%20informasi%20harga%20dan%20pemesanan%3F" 
                           target="_blank" 
                           class="w-full energy-gradient-background text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition duration-300 text-center block">
                            <i class="fab fa-whatsapp mr-2"></i> Pesan Sekarang via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aplikasi Briket -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Aplikasi <span class="energy-gradient-text">Briket Kayu</span></h2>
                <div class="w-20 h-1 bg-secondary mx-auto"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Briket kayu kami cocok untuk berbagai aplikasi industri dan rumah tangga</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Aplikasi 1 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition text-center p-6" data-aos="fade-up">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center bounce-energy">
                        <i class="fas fa-industry text-secondary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">Industri Pengolahan Makanan</h3>
                    <p class="text-gray-600 text-sm">Untuk proses pengeringan, pengasapan, dan pemasakan</p>
                </div>

                <!-- Aplikasi 2 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition text-center p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center bounce-energy">
                        <i class="fas fa-fire text-energy text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">Boiler & Pemanas</h3>
                    <p class="text-gray-600 text-sm">Sumber energi untuk sistem pemanas dan pembangkit uap</p>
                </div>

                <!-- Aplikasi 3 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition text-center p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center bounce-energy">
                        <i class="fas fa-home text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">Rumah Tangga</h3>
                    <p class="text-gray-600 text-sm">Untuk memasak, memanaskan ruangan, dan barbekyu</p>
                </div>

                <!-- Aplikasi 4 -->
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition text-center p-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center bounce-energy">
                        <i class="fas fa-utensils text-secondary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">Restoran & Hotel</h3>
                    <p class="text-gray-600 text-sm">Untuk dapur komersial dan pemanas ruangan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Perusahaan -->
    <section class="py-16 bg-gray-50 relative overflow-hidden grid-pattern">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Mengapa Memilih <span class="energy-gradient-text">Kami</span></h2>
                <div class="section-title"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-award text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Kualitas Terjamin</h3>
                    <p class="text-gray-600">Briket kayu kami melalui proses produksi yang ketat untuk memastikan kualitas dan konsistensi nilai kalori.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-fire text-2xl text-energy"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Nilai Kalori Tinggi</h3>
                    <p class="text-gray-600">Dengan nilai kalori 4200-4600 kcal/kg, briket kami memberikan performa pembakaran yang optimal.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-leaf text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Ramah Lingkungan</h3>
                    <p class="text-gray-600">Produk kami berasal dari sumber daya terbarukan dan mendukung keberlanjutan lingkungan.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pengiriman Tepat Waktu</h3>
                    <p class="text-gray-600">Kami memastikan pengiriman produk sesuai dengan jadwal yang disepakati.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-headset text-2xl text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Layanan Pelanggan</h3>
                    <p class="text-gray-600">Tim kami siap membantu dan memberikan solusi terbaik untuk kebutuhan energi Anda.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-balance-scale text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Harga Kompetitif</h3>
                    <p class="text-gray-600">Kami menawarkan harga yang kompetitif dengan kualitas terbaik di kelasnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Hubungi <span class="energy-gradient-text">Kami</span></h2>
                <div class="w-20 h-1 bg-secondary mx-auto"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Kami siap membantu Anda dengan solusi energi terbarukan yang tepat untuk kebutuhan bisnis Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Info Kontak -->
                <div data-aos="fade-right">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Informasi Kontak</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-building text-secondary"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">PT. Pionir Briket Utama</h4>
                                <p class="text-gray-600">Produsen dan Penjualan Briket Kayu Berkualitas</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fab fa-whatsapp text-secondary"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">WhatsApp</h4>
                                <a href="https://wa.me/6285880588812" target="_blank" class="text-secondary hover:text-primary transition">+62 858-8058-8812</a>
                                <p class="text-gray-600">Hubungi kami untuk informasi produk dan pemesanan</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-secondary"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Email</h4>
                                <a href="mailto:info@pionirbriketutama.com" class="text-secondary hover:text-primary transition">info@pionirbriketutama.com</a>
                                <p class="text-gray-600">Kirim pertanyaan atau permintaan penawaran</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-6 bg-green-50 rounded-lg">
                        <h4 class="font-bold text-gray-800 mb-2">Jam Operasional</h4>
                        <p class="text-gray-600">Senin - Jumat: 08.00 - 17.00 WIB</p>
                        <p class="text-gray-600">Sabtu: 08.00 - 12.00 WIB</p>
                    </div>
                </div>
                
                <!-- Form Kontak -->
                <div data-aos="fade-left">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Kirim Pesan</h3>
                    <form class="space-y-4">
                        <div>
                            <label for="name" class="block text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" id="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                        </div>
                        
                        <div>
                            <label for="message" class="block text-gray-700 mb-2">Pesan</label>
                            <textarea id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition"></textarea>
                        </div>
                        
                        <button type="button" onclick="sendMessage()" class="w-full energy-gradient-background text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition duration-300">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 energy-gradient-background text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6" data-aos="fade-up">Siap Beralih ke Energi Terbarukan?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">Hubungi kami sekarang untuk informasi produk, penawaran harga, atau konsultasi gratis mengenai solusi energi briket kayu untuk bisnis Anda.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                <a href="https://wa.me/6285880588812?text=Halo%20PT.%20Pionir%20Briket%20Utama%2C%20saya%20tertarik%20dengan%20produk%20briket%20kayu%20Anda.%20Bisa%20saya%20tahu%20lebih%20lanjut%3F" 
                   target="_blank" 
                   class="bg-white text-secondary hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg transition duration-300 inline-flex items-center justify-center">
                    <i class="fab fa-whatsapp mr-2 text-xl"></i> WhatsApp Kami
                </a>
                <a href="tel:+6285880588812" 
                   class="bg-transparent border-2 border-white hover:bg-white hover:text-secondary font-bold py-3 px-8 rounded-full text-lg transition duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-phone-alt mr-2"></i> Telepon Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 bg-primary rounded-full flex items-center justify-center text-white font-bold mr-2">PBU</div>
                        <span class="text-xl font-bold">PT. Pionir Briket Utama</span>
                    </div>
                    <p class="text-gray-400 mb-4">Produsen dan penjualan briket kayu berkualitas tinggi dengan nilai kalori optimal untuk solusi energi terbarukan.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="#products" class="text-gray-400 hover:text-white transition">Produk</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#vision" class="text-gray-400 hover:text-white transition">Visi Misi</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak Kami</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-building mt-1 mr-3 text-secondary"></i>
                            <span>PT. Pionir Briket Utama</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fab fa-whatsapp mt-1 mr-3 text-secondary"></i>
                            <a href="https://wa.me/6285880588812" target="_blank" class="hover:text-white transition">+62 858-8058-8812</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-secondary"></i>
                            <a href="mailto:info@pionirbriketutama.com" class="hover:text-white transition">info@pionirbriketutama.com</a>
                        </li>
                    </ul>
                </div>
                
                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Informasi Produk</h3>
                    <p class="text-gray-400 mb-4">Dapatkan informasi terbaru tentang produk dan penawaran spesial dari kami.</p>
                    <form class="flex">
                        <input type="email" placeholder="Alamat Email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-gray-900">
                        <button type="submit" class="bg-secondary hover:bg-primary px-4 rounded-r-lg transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2025 PT. Pionir Briket Utama. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="#home" id="back-to-top" class="fixed bottom-8 right-8 bg-secondary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg hover:bg-primary transition duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate hero elements
        gsap.from(".hero-text h1", {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: "power3.out"
        });
        
        gsap.from(".hero-text p", {
            duration: 1,
            y: 50,
            opacity: 0,
            delay: 0.2,
            ease: "power3.out"
        });
        
        gsap.from(".hero-text .btn-primary", {
            duration: 1,
            y: 50,
            opacity: 0,
            delay: 0.4,
            ease: "power3.out"
        });
        
        gsap.from(".hero-image img", {
            duration: 1,
            x: 50,
            opacity: 0,
            delay: 0.6,
            ease: "power3.out"
        });
        
        // Back to top button
        window.addEventListener('scroll', function() {
            var backToTop = document.getElementById('back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.remove('opacity-100', 'visible');
                backToTop.classList.add('opacity-0', 'invisible');
            }
        });

        // Form submission handler
        function sendMessage() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            
            if (!name || !email || !phone || !message) {
                alert('Harap lengkapi semua field sebelum mengirim pesan.');
                return;
            }
            
            // Redirect to WhatsApp with pre-filled message
            const whatsappMessage = `Halo PT. Pionir Briket Utama,\n\nSaya ${name} ingin menanyakan tentang produk briket kayu Anda.\n\nEmail: ${email}\nTelepon: ${phone}\nPesan: ${message}\n\nTerima kasih.`;
            const encodedMessage = encodeURIComponent(whatsappMessage);
            window.open(`https://wa.me/6285880588812?text=${encodedMessage}`, '_blank');
            
            // Reset form
            document.getElementById('name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('message').value = '';
            
            alert('Pesan Anda telah dikirim melalui WhatsApp. Terima kasih!');
        }

        // Animate keyword items
        document.querySelectorAll('.keyword-item').forEach((item, index) => {
            gsap.to(item, {
                scrollTrigger: {
                    trigger: item,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                opacity: 1,
                x: 0,
                duration: 0.5,
                delay: index * 0.2,
                ease: "power2.out"
            });
        });
        
        // Animate product cards on scroll
        gsap.utils.toArray(".product-card").forEach((card, i) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                y: 50,
                opacity: 0,
                duration: 0.8,
                delay: i * 0.1,
                ease: "back.out(1.7)"
            });
        });
        
        // Animasi ilustrasi floating icons
        gsap.utils.toArray(".floating-icon").forEach((icon, i) => {
            gsap.to(icon, {
                y: -20,
                duration: 2,
                repeat: -1,
                yoyo: true,
                ease: "power1.inOut",
                delay: i * 0.5
            });
        });
    </script>
</body>
</html>
