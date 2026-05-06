<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HM Tour & Travel | Umroh & Haji Sesuai Sunnah</title>
    <meta name="description" content="HM Tour & Travel - Penyelenggara Umroh dan Haji terpercaya, berizin resmi Kemenag, Akreditasi A.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-brand':  '#2E7D32', // hijau tua brand
                        'green-mid':    '#4CAF50', // hijau tengah
                        'green-light':  '#81C784', // hijau muda
                        'green-pale':   '#E8F5E9', // hijau sangat muda (bg)
                        'green-accent': '#00C853', // hijau aksen
                    }
                }
            }
        }
    </script>
    <!-- Favicon - Using HM Logo from header -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('favicon.png')); ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Aref+Ruqaa:wght@400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Nunito', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-arabic { font-family: 'Aref Ruqaa', 'Playfair Display', serif; }

        /* FORCE Hero title font - CRITICAL */
        h1.hero-title,
        h1.hero-title *,
        .hero-title,
        .hero-title * {
            font-family: 'Aref Ruqaa', 'Playfair Display', serif !important;
            font-weight: 900 !important;
        }

        /* ===== DEBRIS CANVAS ===== */
        #debris-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        /* ===== BRAND GRADIENTS ===== */
        .text-green-gradient {
            background: linear-gradient(135deg, #2E7D32, #4CAF50, #81C784);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-green-gradient {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
        }
        .bg-green-gradient-light {
            background: linear-gradient(135deg, #4CAF50, #81C784);
        }
        .border-green-brand { border-color: #2E7D32; }

        /* ===== NAVBAR ===== */
        #navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(46,125,50,0.1);
            transition: all 0.3s ease;
        }
        #navbar.scrolled {
            box-shadow: 0 4px 20px rgba(46,125,50,0.15);
        }

        /* ===== HERO (WordPress Style - Light) ===== */
        #hero-section {
            position: relative;
            min-height: 520px;
            overflow: hidden;
            background: #f5f5f5;
        }
        /* Subtle texture background */
        #hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 50%, rgba(46,125,50,0.06) 0%, transparent 70%),
                radial-gradient(ellipse 60% 80% at 80% 30%, rgba(200,230,201,0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        /* Paper/cloud wave at bottom */
        .hero-wave {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            z-index: 3;
            line-height: 0;
        }
        .hero-wave img { width: 100%; display: block; }

        /* Illustration (Ka'bah) */
        #hero-illustration {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 88%;
            max-height: 460px;
            object-fit: contain;
            object-position: bottom left;
            z-index: 2;
            animation: heroIllustFloat 7s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.12));
        }
        @keyframes heroIllustFloat {
            0%,100% { transform: translateY(0px); }
            50%      { transform: translateY(-10px); }
        }

        /* Air ticket - REMOVED */

        /* ===== RIGHT SIDE DECORATIVE ICONS ===== */
        .hero-right-icons {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 22%;
            z-index: 2;
            pointer-events: none;
        }
        /* Satu gambar 5 pasti, seukuran ilustrasi kiri */
        #hero-right-5pasti {
            position: absolute;
            right: 0;
            bottom: 0;
            height: 88%;
            max-height: 400px;
            object-fit: contain;
            object-position: bottom right;
            z-index: 2;
            animation: heroIllustFloat 7s ease-in-out 0.8s infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.10));
        }

        /* Paper plane animation - terbang dari kiri ke kanan, hidung menghadap kanan */
        #paper-plane {
            position: absolute;
            z-index: 4;
            width: 72px;
            pointer-events: none;
            filter: drop-shadow(2px 4px 8px rgba(0,0,0,0.12));
            /* scaleX(-1) agar hidung pesawat menghadap ke kanan (arah terbang) */
            animation: paperPlaneFly 9s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
        }
        @keyframes paperPlaneFly {
            0%   { left: -100px; top: 58%; transform: scaleX(-1) rotate(8deg); opacity: 0; }
            4%   { opacity: 1; }
            40%  { left: 48%; top: 38%; transform: scaleX(-1) rotate(12deg); opacity: 1; }
            96%  { left: 108%; top: 22%; transform: scaleX(-1) rotate(15deg); opacity: 0.2; }
            100% { left: 112%; top: 20%; transform: scaleX(-1) rotate(15deg); opacity: 0; }
        }

        /* Birds */
        .hero-bird {
            position: absolute;
            z-index: 3;
            font-size: 18px;
            color: #555;
            opacity: 0.6;
        }
        .hero-bird:nth-child(1) { animation: birdFly1 12s linear 0s infinite; }
        .hero-bird:nth-child(2) { animation: birdFly1 12s linear 2s infinite; font-size: 12px; }
        .hero-bird:nth-child(3) { animation: birdFly2 15s linear 4s infinite; font-size: 10px; }
        @keyframes birdFly1 {
            0%   { left: -40px; top: 30%; transform: scaleX(1); opacity: 0; }
            5%   { opacity: 0.6; }
            95%  { opacity: 0.4; }
            100% { left: 110%; top: 25%; transform: scaleX(1); opacity: 0; }
        }
        @keyframes birdFly2 {
            0%   { left: -40px; top: 45%; transform: scaleX(1); opacity: 0; }
            5%   { opacity: 0.5; }
            100% { left: 110%; top: 38%; transform: scaleX(1); opacity: 0; }
        }

        /* Text animations */
        .hero-badge   { opacity: 0; animation: ovaMoveUp 0.6s ease 0.3s forwards; }
        .hero-title   { opacity: 0; animation: ovaMoveUp 0.7s ease 0.5s forwards; }
        .hero-sub     { opacity: 0; animation: ovaMoveUp 0.7s ease 0.8s forwards; }
        .hero-cta-btn { opacity: 0; animation: ovaMoveUp 0.7s ease 1.0s forwards; }
        @keyframes ovaMoveUp {
            from { transform: translateY(50px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* Hero word highlight */
        .hero-word-highlight {
            color: #2E7D32;
            position: relative;
            display: inline-block;
        }
        .hero-word-highlight::after {
            content: '';
            position: absolute;
            left: 0; bottom: -6px;
            width: 100%; height: 7px;
            background: linear-gradient(90deg, #4CAF50, #81C784, #4CAF50);
            border-radius: 4px;
            animation: highlightGlow 2.5s ease-in-out infinite;
        }
        @keyframes highlightGlow {
            0%,100% { opacity: 0.4; transform: scaleX(0.95); }
            50%      { opacity: 1;   transform: scaleX(1.03); }
        }
        .hero-word-plain { color: #1a1a1a; }

        /* Pulse on "Sunnah" */
        .hero-word-pulse {
            display: inline-block;
            animation: wordPulse 2s ease-in-out infinite;
        }
        @keyframes wordPulse {
            0%,100% { text-shadow: none; transform: scale(1); }
            50%      { text-shadow: 0 0 40px rgba(46,125,50,0.4), 0 0 80px rgba(76,175,80,0.2); transform: scale(1.04); }
        }

        /* CTA button pulse */
        .hero-btn-pulse {
            animation: btnPulse 2.5s ease-in-out infinite;
        }
        @keyframes btnPulse {
            0%,100% { box-shadow: 0 8px 30px rgba(46,125,50,0.35); transform: scale(1); }
            50%      { box-shadow: 0 12px 50px rgba(46,125,50,0.6), 0 0 0 10px rgba(76,175,80,0.1); transform: scale(1.04); }
        }

        /* Search bar */
        .hero-search {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            border: 1px solid rgba(46,125,50,0.1);
        }
        .hero-search-item {
            border-right: 1px solid #e5e7eb;
        }
        .hero-search-item:last-of-type { border-right: none; }

        /* Typing cursor */
        .typing-cursor::after {
            content: '|';
            animation: blink 1s step-end infinite;
            color: #2E7D32;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

        /* ===== CARDS ===== */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(46,125,50,0.15);
        }

        /* ===== SECTION DIVIDER ===== */
        .divider-green {
            background: linear-gradient(90deg, transparent, #4CAF50, transparent);
            height: 2px;
        }

        /* ===== BADGE ===== */
        .badge-hot { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .badge-new { background: linear-gradient(135deg, #2E7D32, #4CAF50); }
        .badge-popular { background: linear-gradient(135deg, #f59e0b, #d97706); }

        /* ===== WHATSAPP FLOAT ===== */
        .wa-float {
            position: fixed;
            bottom: 28px; right: 28px;
            z-index: 9999;
        }
        .wa-float a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px; height: 56px;
            background: #25D366;
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(37,211,102,0.5);
            animation: wa-pulse 2.5s infinite;
        }
        @keyframes wa-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 20px rgba(37,211,102,0.5); }
            50% { transform: scale(1.08); box-shadow: 0 6px 28px rgba(37,211,102,0.7); }
        }

        /* ===== MARQUEE ===== */
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .marquee-track { animation: marquee 22s linear infinite; }
        .marquee-track:hover { animation-play-state: paused; }

        /* ===== SMOOTH SCROLL ===== */
        html { scroll-behavior: smooth; }

        /* ===== SECTION BG ALTERNATING ===== */
        .section-white { background: #ffffff; }
        .section-pale  { background: #f0fdf4; }

        /* ===== TESTIMONIAL ===== */
        .testimonial-card {
            background: #ffffff;
            border: 1px solid #e8f5e9;
            box-shadow: 0 2px 16px rgba(46,125,50,0.07);
        }

        /* ===== STAT CARD ===== */
        .stat-card {
            background: linear-gradient(135deg, rgba(46,125,50,0.08), rgba(76,175,80,0.05));
            border: 1px solid rgba(46,125,50,0.2);
        }

        /* ===== FADE IN ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeInUp 0.7s ease forwards; }

        /* ===== SHIMMER ANIMATION ===== */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* ===== MOBILE MENU ===== */
        #mobile-menu { transition: all 0.3s ease; }

        /* ===== RELATIVE Z ===== */
        .z-content { position: relative; z-index: 1; }
    </style>
</head>
<body class="bg-white text-gray-800 overflow-x-hidden">

<!-- ===== DEBRIS CANVAS ===== -->
<canvas id="debris-canvas"></canvas>

<!-- ===== NAVBAR ===== -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 z-content">
                <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>"
                     alt="HM Tour and Travel"
                     class="h-12 w-auto object-contain"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none" class="items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-green-brand flex items-center justify-center font-bold text-white text-lg">HM</div>
                    <span class="font-bold text-xl text-green-brand">HM Tour</span>
                </div>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center gap-7 z-content">
                <a href="#beranda" class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium">Beranda</a>
                <a href="#paket"   class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium">Paket</a>
                <a href="#keunggulan" class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium">Keunggulan</a>
                
                <!-- Dropdown Tentang Kami -->
                <div class="relative" x-data="{ open: false }" @mouseleave="open = false">
                    <button @mouseenter="open = true" 
                            class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium flex items-center gap-1">
                        Tentang Kami
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" 
                         @mouseenter="open = true"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-green-100 py-2 z-50"
                         style="display: none;">
                        <a href="#hm-tour" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-brand transition-colors">
                            <i class="fas fa-building text-green-600 mr-2 text-xs"></i>HM Tour
                        </a>
                        <a href="#hm-team" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-brand transition-colors">
                            <i class="fas fa-users text-green-600 mr-2 text-xs"></i>HM Team
                        </a>
                        <a href="#sejarah" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-brand transition-colors">
                            <i class="fas fa-history text-green-600 mr-2 text-xs"></i>Sejarah HM
                        </a>
                    </div>
                </div>
                
                <a href="#testimoni" class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium">Testimoni</a>
                <a href="#kontak"  class="text-gray-600 hover:text-green-brand transition-colors text-sm font-medium">Kontak</a>
            </div>

            <!-- CTA -->
            <div class="hidden lg:flex items-center gap-3 z-content">
                <a href="https://wa.me/628976688800?text=Assalamu'alaikum, saya ingin info paket umroh HM Tour"
                   target="_blank"
                   class="bg-green-gradient text-white font-semibold px-5 py-2.5 rounded-full text-sm transition-all hover:opacity-90 shadow-md shadow-green-200">
                    <i class="fab fa-whatsapp mr-1.5"></i> Konsultasi
                </a>

                <!-- Dropdown Masuk -->
                <div class="relative" id="login-dropdown-wrapper">
                    <button onclick="toggleLoginDropdown()"
                            class="border-2 border-green-brand text-green-brand hover:bg-green-pale font-semibold px-5 py-2 rounded-full text-sm transition-all flex items-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                        <i class="fas fa-chevron-down text-xs transition-transform" id="login-chevron"></i>
                    </button>
                    <div id="login-dropdown"
                         class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-green-100 overflow-hidden z-50">
                        <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                            <p class="text-xs text-green-700 font-semibold uppercase tracking-wider">Masuk Sebagai</p>
                        </div>
                        <a href="admin"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors group">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-shield text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900 group-hover:text-green-brand">Admin</div>
                                <div class="text-xs text-gray-500">Panel manajemen</div>
                            </div>
                        </a>
                        <a href="<?php echo e(route('affiliate.login')); ?>"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors group border-t border-gray-50">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-handshake text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900 group-hover:text-green-brand">Mitra</div>
                                <div class="text-xs text-gray-500">Dashboard komisi</div>
                            </div>
                        </a>
                        <div class="flex items-center gap-3 px-4 py-3 border-t border-gray-50 opacity-50 cursor-not-allowed">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Jemaah</div>
                                <div class="text-xs text-gray-400">Segera hadir</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile btn -->
            <button id="menu-btn" class="lg:hidden text-green-brand p-2 z-content">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="lg:hidden hidden mt-3 pb-4 border-t border-green-100">
            <div class="flex flex-col gap-2 pt-3" x-data="{ tentangOpen: false }">
                <a href="#beranda"    class="text-gray-600 hover:text-green-brand py-2 text-sm px-2">Beranda</a>
                <a href="#paket"      class="text-gray-600 hover:text-green-brand py-2 text-sm px-2">Paket Umroh & Haji</a>
                <a href="#keunggulan" class="text-gray-600 hover:text-green-brand py-2 text-sm px-2">Keunggulan</a>
                
                <!-- Mobile Dropdown Tentang Kami -->
                <div class="relative">
                    <button @click="tentangOpen = !tentangOpen" class="w-full text-left text-gray-600 hover:text-green-brand py-2 text-sm px-2 flex items-center justify-between">
                        <span>Tentang Kami</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': tentangOpen }"></i>
                    </button>
                    <div x-show="tentangOpen" x-collapse class="pl-4 space-y-1">
                        <a href="#hm-tour" class="block py-2 text-sm text-gray-600 hover:text-green-brand">HM Tour</a>
                        <a href="#hm-team" class="block py-2 text-sm text-gray-600 hover:text-green-brand">HM Team</a>
                        <a href="#sejarah" class="block py-2 text-sm text-gray-600 hover:text-green-brand">Sejarah HM</a>
                    </div>
                </div>
                
                <a href="#testimoni"  class="text-gray-600 hover:text-green-brand py-2 text-sm px-2">Testimoni</a>
                <a href="#kontak"     class="text-gray-600 hover:text-green-brand py-2 text-sm px-2">Kontak</a>
                <div class="flex gap-3 pt-2 px-2">
                    <a href="https://wa.me/628976688800" target="_blank"
                       class="flex-1 bg-green-gradient text-white font-semibold py-2.5 rounded-full text-sm text-center">
                        <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                    </a>
                    <!-- Mobile Dropdown Masuk -->
                    <div class="flex-1 relative" id="mobile-login-wrapper">
                        <button onclick="toggleMobileLogin()"
                                class="w-full border-2 border-green-brand text-green-brand py-2 rounded-full text-sm text-center font-semibold flex items-center justify-center gap-1">
                            <i class="fas fa-sign-in-alt"></i> Masuk <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="mobile-login-dropdown"
                             class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-green-100 overflow-hidden z-50">
                            <a href="admin" class="flex items-center gap-3 px-4 py-3 hover:bg-green-50">
                                <i class="fas fa-user-shield text-blue-600 w-5"></i>
                                <span class="text-sm font-semibold text-gray-900">Admin</span>
                            </a>
                            <a href="<?php echo e(route('affiliate.login')); ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 border-t border-gray-50">
                                <i class="fas fa-handshake text-green-600 w-5"></i>
                                <span class="text-sm font-semibold text-gray-900">Mitra</span>
                            </a>
                            <div class="flex items-center gap-3 px-4 py-3 border-t border-gray-50 opacity-50">
                                <i class="fas fa-user text-yellow-600 w-5"></i>
                                <span class="text-sm text-gray-500">Jemaah (segera)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ===== HERO (WordPress Style - Light) ===== -->
<section id="beranda">
<div id="hero-section">

    <!-- Birds flying -->
    <div class="hero-bird" style="top:28%">🕊</div>
    <div class="hero-bird" style="top:22%">🕊</div>
    <div class="hero-bird" style="top:40%">🕊</div>

    <!-- Paper plane SVG -->
    <svg id="paper-plane" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <polygon points="0,30 100,0 70,30 100,60" fill="#4CAF50" opacity="0.9"/>
        <polygon points="0,30 70,30 55,50" fill="#2E7D32" opacity="0.8"/>
        <line x1="70" y1="30" x2="55" y2="50" stroke="#1B5E20" stroke-width="1"/>
    </svg>

    <!-- Ka'bah illustration (left-center) -->
    <img id="hero-illustration"
         src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/07/Umroh-sesuai-sunnah.png')); ?>"
         alt="Ka'bah & Masjid Nabawi"
         onerror="this.style.display='none'">

    <!-- Air ticket (right) - REMOVED -->

    <!-- RIGHT SIDE: gambar 5 pasti seukuran ilustrasi kiri (desktop only) -->
    <img id="hero-right-5pasti"
         class="hidden lg:block"
         src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/5_pasti-removebg-preview.png')); ?>"
         alt="5 Pasti HM Tour"
         onerror="this.style.display='none'">

    <!-- Content CENTER full-width -->
    <div class="relative z-content w-full px-4 sm:px-6 lg:px-8" style="min-height:480px; display:flex; align-items:center; justify-content:center;">
        <div class="w-full text-center py-20 lg:py-24">

            <!-- Title -->
            <h1 class="hero-title font-arabic font-black leading-none mb-8 text-gray-900"
                style="font-size: clamp(3rem, 8vw, 6.5rem); line-height: 1.05; font-weight: 900;">
                <span class="hero-word-highlight">Umroh</span>
                <span class="hero-word-plain"> And </span>
                <span class="hero-word-highlight">Hajj</span><br>
                <span class="hero-word-plain">With </span>
                <span class="hero-word-highlight hero-word-pulse">Sunnah</span>
                <span class="hero-word-plain"> Ways</span>
            </h1>

            <!-- CTA Button -->
            <div class="hero-cta-btn">
                <a href="#paket"
                   class="hero-btn-pulse inline-flex items-center gap-2 bg-green-gradient text-white font-bold px-10 py-4 rounded-full text-base shadow-xl shadow-green-300/50 hover:opacity-90 transform transition-all">
                    <i class="fas fa-search"></i> LIHAT PROGRAM
                </a>
            </div>
        </div>
    </div>

    <!-- Search bar -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-8" style="margin-top:-20px;">
        <form action="<?php echo e(url('/')); ?>" method="GET" class="hero-search p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-0">
                <!-- Perusahaan (Outlet) -->
                <div class="hero-search-item flex-1 px-4 py-2">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-building text-green-brand text-xs"></i>
                        <label class="font-bold text-gray-800 text-sm cursor-pointer" for="s_outlet">Perusahaan</label>
                    </div>
                    <select id="s_outlet" name="outlet_id"
                            class="w-full text-xs text-gray-500 bg-transparent border-none outline-none cursor-pointer appearance-none">
                        <option value="">Tour & Travel</option>
                        <?php $__currentLoopData = $outlets ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($outlet->id_outlet); ?>"
                            <?php echo e(request('outlet_id') == $outlet->id_outlet ? 'selected' : ''); ?>>
                            <?php echo e($outlet->nama_outlet); ?><?php echo e($outlet->kota ? ' - '.$outlet->kota : ''); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <!-- Waktu -->
                <div class="hero-search-item flex-1 px-4 py-2">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-calendar text-green-brand text-xs"></i>
                        <label class="font-bold text-gray-800 text-sm cursor-pointer" for="s_bulan">Waktu</label>
                    </div>
                    <select id="s_bulan" name="bulan"
                            class="w-full text-xs text-gray-500 bg-transparent border-none outline-none cursor-pointer appearance-none">
                        <option value="">Bulan</option>
                        <?php $__currentLoopData = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $bln): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($i+1); ?>" <?php echo e(request('bulan') == $i+1 ? 'selected' : ''); ?>><?php echo e($bln); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <!-- Program (Package Type) -->
                <div class="hero-search-item flex-1 px-4 py-2">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-search text-green-brand text-xs"></i>
                        <label class="font-bold text-gray-800 text-sm cursor-pointer" for="s_program">Program</label>
                    </div>
                    <select id="s_program" name="package_type"
                            class="w-full text-xs text-gray-500 bg-transparent border-none outline-none cursor-pointer appearance-none">
                        <option value="">Pilih Tipe</option>
                        <?php $__currentLoopData = $packageTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php echo e(request('package_type') == $type ? 'selected' : ''); ?>>
                            <?php echo e(ucwords(str_replace('_', ' ', $type))); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <!-- Jemaah -->
                <div class="flex-1 px-4 py-2">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-user text-green-brand text-xs"></i>
                        <label class="font-bold text-gray-800 text-sm cursor-pointer" for="s_jemaah">Jemaah</label>
                    </div>
                    <select id="s_jemaah" name="jemaah"
                            class="w-full text-xs text-gray-500 bg-transparent border-none outline-none cursor-pointer appearance-none">
                        <?php for($j = 1; $j <= 10; $j++): ?>
                        <option value="<?php echo e($j); ?>" <?php echo e(request('jemaah', 1) == $j ? 'selected' : ''); ?>><?php echo e($j); ?> Orang</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <!-- Search button -->
                <div class="px-2">
                    <button type="submit"
                            class="flex items-center gap-2 bg-green-gradient text-white font-bold px-6 py-3 rounded-xl text-sm hover:opacity-90 transition-all whitespace-nowrap">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
</section>

<!-- ===== AIRLINE MARQUEE ===== -->
<div class="section-white py-6 overflow-hidden border-y border-green-100">
    <div class="flex items-center gap-3 mb-4 justify-center">
        <div class="divider-green w-12"></div>
        <span class="text-gray-400 text-xs uppercase tracking-widest font-medium">Maskapai Partner</span>
        <div class="divider-green w-12"></div>
    </div>
    <div class="overflow-hidden">
        <div class="marquee-track flex gap-14 items-center" style="width: max-content;">
            <?php
            $airlines = [
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-saudi-airlines.png'),    'alt' => 'Saudi Airlines'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-garuda-indonesia.png'), 'alt' => 'Garuda Indonesia'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-emirates.png'),         'alt' => 'Emirates'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-turkish-airlines.png'), 'alt' => 'Turkish Airlines'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-etihad.png'),           'alt' => 'Etihad'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-Qatar-airlines.png'),   'alt' => 'Qatar Airways'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-oman-air.png'),         'alt' => 'Oman Air'],
                ['src' => url('WEB_HMTour/wp-content/uploads/2025/08/partner-Egyptair.png'),         'alt' => 'EgyptAir'],
            ];
            ?>
            <?php $__currentLoopData = array_merge($airlines, $airlines); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex-shrink-0 opacity-50 hover:opacity-100 transition-opacity">
                <img src="<?php echo e($airline['src']); ?>" alt="<?php echo e($airline['alt']); ?>"
                     class="h-8 w-auto object-contain grayscale hover:grayscale-0 transition-all"
                     onerror="this.parentElement.style.display='none'">
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<!-- ===== HASIL PENCARIAN (jika ada filter aktif) ===== -->
<?php if(isset($searchResults)): ?>
<section class="section-white py-12" id="hasil-cari">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-playfair text-2xl font-bold text-gray-900">
                    Hasil Pencarian
                    <span class="text-green-brand">(<?php echo e($searchResults->count()); ?> paket)</span>
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    <?php if(request('outlet_id') && ($selectedOutlet = $outlets->firstWhere('id_outlet', request('outlet_id')))): ?>
                        Perusahaan: <strong><?php echo e($selectedOutlet->nama_outlet); ?></strong>
                    <?php endif; ?>
                    <?php if(request('bulan')): ?>
                        · Bulan: <strong><?php echo e(['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][request('bulan')]); ?></strong>
                    <?php endif; ?>
                    <?php if(request('package_type')): ?>
                        · Program: <strong><?php echo e(ucwords(str_replace('_', ' ', request('package_type')))); ?></strong>
                    <?php endif; ?>
                </p>
            </div>
            <a href="/" class="text-green-brand text-sm hover:underline font-medium">
                <i class="fas fa-times mr-1"></i> Reset Filter
            </a>
        </div>

        <?php if($searchResults->isEmpty()): ?>
        <div class="text-center py-16 bg-green-pale rounded-2xl border border-green-100">
            <i class="fas fa-search text-green-200 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium">Tidak ada paket yang sesuai dengan filter Anda.</p>
            <a href="/" class="mt-4 inline-block text-green-brand text-sm hover:underline">Lihat semua paket</a>
        </div>
        <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginalbb3566070790fe8fe5c4e22b7076e036 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.package-card','data' => ['package' => $pkg]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('package-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['package' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pkg)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $attributes = $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $component = $__componentOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===== TAGLINE ===== -->
<div class="section-white py-8 border-b border-green-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-gray-900">
            Penuhi Panggilan Allah Dari Sini!
        </h2>
    </div>
</div>

<!-- ===== PAKET UMROH (dari database) ===== -->
<section id="paket" class="section-pale py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-4 shadow-sm">
                <i class="fas fa-kaaba text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Paket Pilihan</span>
            </div>
            <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Paket <span class="text-green-gradient">Umroh & Haji</span>
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm">
                Pilih paket yang sesuai kebutuhan Anda. Semua paket sudah termasuk visa, tiket, hotel, dan pembimbing berpengalaman.
            </p>
        </div>

        <?php if(isset($featuredPackages) && $featuredPackages->count() > 0): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $featuredPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginalbb3566070790fe8fe5c4e22b7076e036 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.package-card','data' => ['package' => $pkg]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('package-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['package' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pkg)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $attributes = $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $component = $__componentOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <!-- Fallback jika belum ada data -->
        <div class="text-center py-16 bg-white rounded-2xl border border-green-100">
            <i class="fas fa-kaaba text-green-200 text-5xl mb-4"></i>
            <p class="text-gray-500">Paket akan segera tersedia. Hubungi kami untuk informasi lebih lanjut.</p>
        </div>
        <?php endif; ?>

        <div class="text-center mt-10">
            <a href="https://wa.me/628976688800?text=Assalamu'alaikum, saya ingin melihat semua paket umroh HM Tour"
               target="_blank"
               class="inline-flex items-center gap-2 border-2 border-green-brand text-green-brand hover:bg-green-pale px-8 py-3 rounded-full text-sm font-semibold transition-all">
                Lihat Semua Paket <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- ===== KEUNGGULAN ===== -->
<section id="keunggulan" class="section-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-green-pale border border-green-200 rounded-full px-4 py-2 mb-4">
                <i class="fas fa-award text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Mengapa HM Tour</span>
            </div>
            <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Keunggulan <span class="text-green-gradient">Kami</span>
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $keunggulan = [
                ['icon' => 'fas fa-book-open', 'img' => url('WEB_HMTour/wp-content/uploads/2025/08/sunnah.png'),
                 'title' => 'Sesuai Sunnah', 'desc' => 'Bimbingan ibadah sesuai tuntunan Rasulullah ﷺ oleh ustadz berpengalaman'],
                ['icon' => 'fas fa-shield-alt', 'img' => url('WEB_HMTour/wp-content/uploads/2025/08/dependable.png'),
                 'title' => 'Terpercaya & Resmi', 'desc' => 'Berizin resmi Kemenag RI dengan Akreditasi A, beroperasi sejak 2015'],
                ['icon' => 'fas fa-tag', 'img' => url('WEB_HMTour/wp-content/uploads/2025/08/low-price.png'),
                 'title' => 'Harga Terjangkau', 'desc' => 'Harga kompetitif tanpa mengorbankan kualitas layanan dan fasilitas'],
                ['icon' => 'fas fa-headset', 'img' => url('WEB_HMTour/wp-content/uploads/2025/08/ramah.png'),
                 'title' => 'Pelayanan Ramah', 'desc' => 'Tim profesional siap melayani 24/7 sebelum, selama, dan setelah perjalanan'],
            ];
            ?>
            <?php $__currentLoopData = $keunggulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hover bg-white rounded-2xl p-6 text-center border border-green-100 shadow-sm hover:border-green-300">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-pale flex items-center justify-center overflow-hidden">
                    <img src="<?php echo e($item['img']); ?>" alt="<?php echo e($item['title']); ?>"
                         class="w-10 h-10 object-contain"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    <i class="<?php echo e($item['icon']); ?> text-green-brand text-2xl" style="display:none"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2"><?php echo e($item['title']); ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed"><?php echo e($item['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Akreditasi Banner -->
        <div class="mt-12 rounded-3xl overflow-hidden border border-green-200 shadow-sm">
            <div class="relative">
                <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Akreditasi-A-HM-Tour-Oleh-Kemenag.jpeg')); ?>"
                     alt="Akreditasi A Kemenag"
                     class="w-full h-64 object-cover object-center"
                     onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-transparent flex items-center">
                    <div class="p-8 lg:p-12">
                        <div class="text-green-brand text-sm font-semibold uppercase tracking-wider mb-2">Resmi & Terpercaya</div>
                        <h3 class="font-playfair text-2xl lg:text-3xl font-bold text-gray-900 mb-3">
                            Akreditasi A dari<br>Kementerian Agama RI
                        </h3>
                        <p class="text-gray-600 text-sm max-w-md">
                            HM Tour telah mendapatkan akreditasi tertinggi dari Kemenag RI, menjamin keamanan dan kualitas perjalanan ibadah Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== TENTANG KAMI ===== -->
<section id="tentang" class="section-pale py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Images -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-4">
                    <div class="rounded-2xl overflow-hidden border-2 border-white shadow-md h-48">
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Jemaah-HM-Tour-plus-turki.jpeg')); ?>"
                             alt="Jamaah HM Tour" class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                    <div class="rounded-2xl overflow-hidden border-2 border-white shadow-md h-32">
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Mitra-Stakeholder-HM-Tour-Travel-umroh-sunnah.jpeg')); ?>"
                             alt="Mitra HM Tour" class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                </div>
                <div class="space-y-4 mt-8">
                    <div class="rounded-2xl overflow-hidden border-2 border-white shadow-md h-32">
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Masjidil-Haram-Makkah-Umroh-Sesuai-Sunnah.jpeg')); ?>"
                             alt="Masjidil Haram" class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                    <div class="rounded-2xl overflow-hidden border-2 border-white shadow-md h-48">
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Masjid-Nabawi-Madinah-Umroh-Sesuai-Sunnah.jpeg')); ?>"
                             alt="Masjid Nabawi" class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-6 shadow-sm">
                    <i class="fas fa-mosque text-green-brand text-xs"></i>
                    <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Tentang HM Tour</span>
                </div>
                <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    Perjalanan Ibadah yang<br><span class="text-green-gradient">Bermakna & Terpercaya</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Berdiri sejak tahun 2012, HM Tour & Travel (PT Hikami Mandiri Indonesia) telah tumbuh dan berkembang menjadi salah satu travel yang berawal dari travel pariwisata nusantara. Dibawah pimpinan Bapak H. Ilham Mochamad Hikami, tahun 2022 HM Tour & Travel mengembangkan usaha sebagai salah satu penyelenggaraan Ibadah Umrah baik itu paket reguler maupun umrah plus.
                </p>
                <p class="text-gray-600 leading-relaxed mb-8">
                    Untuk memperkuat bisnis ini kami telah menjadi anggota dari Serikat Penyelenggara Umrah dan Haji Indonesia (SAPUHI). Selain itu sebagai komitmen legalitas perusahaan dalam melayani customer serta jemaah secara profesional, kami telah memiliki izin resmi sebagai Biro Perjalanan Wisata, izin sebagai penyelenggara ibadah umrah dan haji khusus dari Kementerian Agama RI.
                </p>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="stat-card rounded-xl p-4">
                        <div class="text-2xl font-bold text-green-brand mb-1">10K+</div>
                        <div class="text-gray-500 text-xs">Jamaah Berangkat</div>
                    </div>
                    <div class="stat-card rounded-xl p-4">
                        <div class="text-2xl font-bold text-green-brand mb-1">10+</div>
                        <div class="text-gray-500 text-xs">Tahun Pengalaman</div>
                    </div>
                    <div class="stat-card rounded-xl p-4">
                        <div class="text-2xl font-bold text-green-brand mb-1">50+</div>
                        <div class="text-gray-500 text-xs">Kota di Indonesia</div>
                    </div>
                    <div class="stat-card rounded-xl p-4">
                        <div class="text-2xl font-bold text-green-brand mb-1">100%</div>
                        <div class="text-gray-500 text-xs">Visa Terjamin</div>
                    </div>
                </div>

                <!-- GM Profile -->
                <div class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-green-100 shadow-sm">
                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-green-200 flex-shrink-0">
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Husni-Mubarok-General-Manager-HM-Tour.jpeg')); ?>"
                             alt="Husni Mubarok"
                             class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Husni Mubarok</div>
                        <div class="text-green-brand text-xs font-medium">General Manager HM Tour</div>
                        <div class="text-gray-500 text-xs mt-1 italic">"Kami hadir untuk mewujudkan impian ibadah Anda"</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== HM TOUR SECTION ===== -->
<section id="hm-tour" class="section-pale py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-4 shadow-sm">
                <i class="fas fa-building text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Profil Perusahaan</span>
            </div>
            <h2 class="font-arabic text-3xl sm:text-4xl font-bold text-gray-900 mb-4">HM Tour & Travel</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Penyelenggara Perjalanan Ibadah Umroh dan Haji Terpercaya
            </p>
        </div>

        <!-- Visi, Misi, Moto & Tagline -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl p-8 border border-green-100 shadow-sm">
                <div class="w-12 h-12 bg-green-gradient rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-eye text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Visi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Memberikan layanan berkualitas, profesional dan amanah untuk para tamu Allah, dengan landasan nilai ibadah yang kuat dan sesuai tuntunan Qur'an & Sunnah sehingga setiap perjalanan mulia menuju baitullah memiliki makna dan value di hati Duyufurrahman.
                </p>
            </div>

            <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl p-8 border border-green-100 shadow-sm">
                <div class="w-12 h-12 bg-green-gradient rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-bullseye text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Misi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Menyediakan layanan perjalanan ibadah haji dan umroh dengan kualitas prima dan pelayanan ramah yang didukung oleh mitra & stakeholder berpengalaman dengan mengintegrasikan teknologi berbasis digital untuk kemudahan pelayanan yang lebih luas.
                </p>
            </div>
        </div>

        <!-- Moto & Tagline -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 border border-green-100 shadow-sm">
                <div class="w-12 h-12 bg-green-gradient rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-quote-right text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Moto</h3>
                <p class="text-gray-600 leading-relaxed">
                    Travel Amanah, Sesuai Sunnah, Pelayanan Ramah, Harga Murah, Proses Mudah, Fasilitas Mewah, Semoga Berkah
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 border border-green-100 shadow-sm">
                <div class="w-12 h-12 bg-green-gradient rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-tag text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tagline</h3>
                <p class="text-gray-600 leading-relaxed font-semibold text-green-brand text-lg">
                    Hajj & Umroh With Sunnah Ways
                </p>
            </div>
        </div>

        <!-- Legalitas Perusahaan -->
        <div class="bg-white rounded-2xl p-8 border border-green-100 shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Legalitas Perusahaan</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Akta Pendirian</p>
                    <p class="text-xs text-gray-600 mt-1">100 / 25 Oktober 2023</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-receipt text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">NPWP</p>
                    <p class="text-xs text-gray-600 mt-1">65.256.531.8-429.000</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-certificate text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Izin PPIU KEMENAG RI</p>
                    <p class="text-xs text-gray-600 mt-1">27042200404460002</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-handshake text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">Anggota SAPUHI</p>
                    <p class="text-xs text-gray-600 mt-1">340/DPP/SAPUHI/2022</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-id-card text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">NIB</p>
                    <p class="text-xs text-gray-600 mt-1">27042200404460002</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-gavel text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-gray-900 text-sm">SK Kehakiman</p>
                    <p class="text-xs text-gray-600 mt-1">C-453.HT.03.01-TH.2005</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== HM TEAM SECTION ===== -->
<!-- <section id="hm-team" class="section-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-green-pale border border-green-200 rounded-full px-4 py-2 mb-4">
                <i class="fas fa-users text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Tim Kami</span>
            </div>
            <h2 class="font-arabic text-3xl sm:text-4xl font-bold text-gray-900 mb-4">HM Team</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Tim profesional dan berpengalaman siap melayani perjalanan ibadah Anda
            </p>
        </div> -->

        <!-- Team Image -->
        <!-- <div class="max-w-5xl mx-auto">
            <img src="<?php echo e(asset('img/hm_team.png')); ?>" 
                 alt="HM Team" 
                 class="w-full h-auto rounded-2xl shadow-lg border border-green-100"
                 onerror="this.src='<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>'; this.classList.add('p-20', 'bg-green-50')">
        </div>
    </div>
</section> -->

<!-- ===== SEJARAH HM SECTION ===== -->
<section id="sejarah" class="section-pale py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-4 shadow-sm">
                <i class="fas fa-history text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Perjalanan Kami</span>
            </div>
            <h2 class="font-arabic text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Sejarah HM Tour</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Lebih Dari 13+ Tahun Kami Berpengalaman
            </p>
        </div>

        <!-- Timeline -->
        <div class="relative max-w-4xl mx-auto">
            <!-- Vertical line -->
            <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-green-200 hidden md:block"></div>

            <!-- Timeline items -->
            <div class="space-y-12">
                <!-- 2012 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8 md:text-right mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2012</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">HM Tour & Travel Berdiri</h3>
                            <p class="text-sm text-gray-600">Pada tahun 2012 kami mendirikan Biro perjalanan lokal HM Tour & Travel dan melayani berbagai pengguna yang ingin liburan di Indonesia.</p>
                        </div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8"></div>
                </div>

                <!-- 2013 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8"></div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8 mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2013</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">Pendaftaran Perusahaan</h3>
                            <p class="text-sm text-gray-600">Agar dapat melayani instansi resmi seperti kantor pemerintahan, sekolah, universitas, dan berbagai kantor swasta. Kami mendaftarkan perusahaan agar memiliki legalitas dengan izin resmi.</p>
                        </div>
                    </div>
                </div>

                <!-- 2020 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8 md:text-right mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2020</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">Pindah Kantor</h3>
                            <p class="text-sm text-gray-600">Kami melakukan pemindahan kantor untuk dapat melayani para pengguna lebih baik lagi. Kantor kami sekarang berada di: Jl. A.H. Nasution No.98, Sukamiskin, Kec. Arcamanik, Kota Bandung, Jawa Barat 40293.</p>
                        </div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8"></div>
                </div>

                <!-- 2021 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8"></div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8 mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2021</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">Mengubah Citra</h3>
                            <p class="text-sm text-gray-600">Pada awalnya, perusahaan kami berdiri dengan nama CV. HM Tour & Travel. Kemudian kami meningkatkan perusahaan kami dengan nama: PT Hikami Mandiri Indonesia.</p>
                        </div>
                    </div>
                </div>

                <!-- 2022 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8 md:text-right mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2022</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">Penawaran Umum Perdana</h3>
                            <p class="text-sm text-gray-600">Pada tahun 2022 kami menawarkan saham perusahaan kami kepada beberapa investor. Dan mendapatkan beberapa investor yang berminat untuk memiliki saham perusahaan kami.</p>
                        </div>
                    </div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8"></div>
                </div>

                <!-- 2024 -->
                <div class="relative flex flex-col md:flex-row items-center">
                    <div class="w-full md:w-1/2 md:pr-8"></div>
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-600 rounded-full border-4 border-white hidden md:block"></div>
                    <div class="w-full md:w-1/2 md:pl-8 mb-4 md:mb-0">
                        <div class="bg-white rounded-xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-green-600 font-bold text-lg">2024</span>
                            <h3 class="font-bold text-gray-900 mt-2 mb-2">Akreditasi A dari KEMENAG</h3>
                            <p class="text-sm text-gray-600">Mendapatkan predikat Akreditasi A dari KEMENAG dalam pelayanan jemaah umroh & Haji salah satu yang terbaik.</p>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pencapaian HM Tour -->
        <div class="mt-16">
            <h3 class="text-2xl font-bold text-gray-900 text-center mb-8">Pencapaian HM Tour</h3>
            <p class="text-gray-600 text-center mb-10 max-w-3xl mx-auto">
                Selama 13 tahun lebih perjalanan HM Tour di industri travel Haji & Umroh ini telah banyak pencapaian yang diterima, yang tentu ini menjadi motivasi untuk menjadi perusahaan yang jauh lebih baik untuk menjadi pelayan tamu Allah
            </p>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-8 border border-green-100 shadow-sm hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-award text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-3">Akreditasi A Oleh KEMENAG</h4>
                    <p class="text-sm text-gray-600">Dengan melalui proses penilaian yang sangat ketat oleh kemenag dari segala sisi management, HM Tour mendapat Akreditasi A yang merupakan nilai tertinggi dalam pelayanan.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 border border-green-100 shadow-sm hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-3">Trusted & Friendly Environment</h4>
                    <p class="text-sm text-gray-600">Pelayanan profesional dengan lingkungan yang ramah dan terpercaya, memberikan kenyamanan maksimal bagi setiap jamaah dalam perjalanan ibadah mereka.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 border border-green-100 shadow-sm hover:shadow-md transition-shadow text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-3">Low Cost & Quality Service</h4>
                    <p class="text-sm text-gray-600">Harga kompetitif dengan kualitas terbaik. Kami berkomitmen memberikan layanan berkualitas tinggi dengan harga yang terjangkau untuk semua kalangan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOTEL PARTNER ===== -->
<section class="section-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-green-pale border border-green-200 rounded-full px-4 py-2 mb-4">
                <i class="fas fa-hotel text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Hotel Partner</span>
            </div>
            <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Hotel <span class="text-green-gradient">Bintang 4 & 5</span>
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $hotels = [
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Al-Shohada-Mekkah-1.jpg'),                    'name' => 'Hotel Al-Shohada',          'city' => 'Makkah',  'stars' => 5],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/gOLDEN-tULIP-aLZAHABI-1.jpg'),                      'name' => 'Golden Tulip Al-Zahabi',    'city' => 'Madinah', 'stars' => 5],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Movenpick-Mekkah-Umroh-Sesuai-Sunnah.jpeg'),  'name' => 'Movenpick',                 'city' => 'Makkah',  'stars' => 5],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Swissotel-Al-Maqam-Makkah-Umroh-Sesuai-Sunnah.jpeg'), 'name' => 'Swissotel Al-Maqam', 'city' => 'Makkah',  'stars' => 5],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Pullman-Zamzam-Mekkah-Umroh-Sesuai-Sunnah.jpeg'), 'name' => 'Pullman Zamzam',        'city' => 'Makkah',  'stars' => 5],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/Hotel-Grand-Plaza-Madinah-Umroh-Sesuai-Sunnah.jpeg'), 'name' => 'Grand Plaza',             'city' => 'Madinah', 'stars' => 4],
            ];
            ?>
            <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="https://www.google.com/search?q=<?php echo e(urlencode($hotel['name'] . ' ' . $hotel['city'] . ' Saudi Arabia')); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               class="block card-hover rounded-2xl overflow-hidden border border-green-100 shadow-sm group bg-white">
                <div class="relative h-44 overflow-hidden">
                    <img src="<?php echo e($hotel['img']); ?>" alt="<?php echo e($hotel['name']); ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 flex gap-0.5">
                        <?php for($i = 0; $i < $hotel['stars']; $i++): ?>
                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm"><?php echo e($hotel['name']); ?></h3>
                    <p class="text-green-brand text-xs mt-1 font-medium"><i class="fas fa-map-marker-alt mr-1"></i><?php echo e($hotel['city']); ?></p>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<!-- ===== TESTIMONI ===== -->
<section id="testimoni" class="section-pale py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-4 shadow-sm">
                <i class="fas fa-heart text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Testimoni Jamaah</span>
            </div>
            <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Kata Mereka yang Telah <span class="text-green-gradient">Berangkat</span>
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $testimonials = [
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.23.00.jpeg'),
                 'name' => 'Bapak Ahmad', 'package' => 'Umroh Reguler 2024', 'rating' => 5,
                 'text' => 'Alhamdulillah, perjalanan umroh bersama HM Tour sangat berkesan. Pembimbing sangat sabar dan berpengalaman. Hotel dekat Masjidil Haram, sangat nyaman.'],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.24.15.jpeg'),
                 'name' => 'Ibu Siti Rahayu', 'package' => 'Umroh Plus Turki 2024', 'rating' => 5,
                 'text' => 'Subhanallah, paket plus Turki benar-benar luar biasa. Bisa umroh sekaligus wisata halal di Istanbul. Pelayanan HM Tour sangat profesional dan ramah.'],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.25.52.jpeg'),
                 'name' => 'Keluarga Budi Santoso', 'package' => 'Umroh Keluarga 2024', 'rating' => 5,
                 'text' => 'Kami sekeluarga sangat puas dengan layanan HM Tour. Anak-anak pun nyaman karena pembimbing sangat perhatian. Insya Allah akan umroh lagi bersama HM Tour.'],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.27.36.jpeg'),
                 'name' => 'Pak Hendra', 'package' => 'Haji Plus 2024', 'rating' => 5,
                 'text' => 'Haji Plus bersama HM Tour sangat memuaskan. Hotel sangat dekat dengan Masjidil Haram, makanan enak, dan pembimbing sangat berpengalaman. Jazakallah khairan.'],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.30.25.jpeg'),
                 'name' => 'Ibu Dewi Lestari', 'package' => 'Umroh Plus Dubai 2024', 'rating' => 5,
                 'text' => 'Paket umroh plus Dubai sangat worth it! Bisa ibadah di Tanah Suci sekaligus melihat keajaiban Dubai. HM Tour benar-benar terpercaya dan profesional.'],
                ['img' => url('WEB_HMTour/wp-content/uploads/2025/08/WhatsApp-Image-2024-01-29-at-23.34.40.jpeg'),
                 'name' => 'Pak Ridwan', 'package' => 'Umroh Reguler 2025', 'rating' => 5,
                 'text' => 'Ini umroh ketiga saya bersama HM Tour. Selalu memuaskan! Pelayanan konsisten, pembimbing berpengalaman, dan harga sangat terjangkau. Highly recommended!'],
            ];
            ?>
            <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="testimonial-card rounded-2xl p-6">
                <div class="flex gap-1 mb-4">
                    <?php for($i = 0; $i < $t['rating']; $i++): ?>
                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">"<?php echo e($t['text']); ?>"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-green-200 flex-shrink-0">
                        <img src="<?php echo e($t['img']); ?>" alt="<?php echo e($t['name']); ?>"
                             class="w-full h-full object-cover"
                             onerror="this.parentElement.style.background='#e8f5e9';this.remove()">
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm"><?php echo e($t['name']); ?></div>
                        <div class="text-green-brand text-xs font-medium"><?php echo e($t['package']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<!-- ===== PROGRAM KEMITRAAN ===== -->
<?php if(!isset($affiliator) || !$affiliator): ?>
<section class="section-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-green-pale border border-green-200 rounded-full px-4 py-2 mb-4">
                <i class="fas fa-handshake text-green-brand text-xs"></i>
                <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Program Kemitraan</span>
            </div>
            <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Bergabung Bersama <span class="text-green-gradient">HM Tour</span>
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Pilih program kemitraan yang sesuai dengan kebutuhan Anda dan mulai dapatkan penghasilan
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $programs = \App\Models\PartnershipProgram::active()->ordered()->get();
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-green-100 shadow-sm group">
                <div class="bg-gradient-to-br from-green-50 to-white p-5 border-b border-green-100">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 transition-colors">
                        <i class="fas fa-handshake text-green-600 group-hover:text-white text-xl transition-colors"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 text-center text-base mb-2"><?php echo e($program->name); ?></h3>
                    <div class="text-center mb-3">
                        <?php if($program->registration_fee == 0): ?>
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">GRATIS</span>
                        <?php else: ?>
                            <span class="inline-block bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo e($program->formatted_fee); ?></span>
                        <?php endif; ?>
                        <?php if($program->requires_previous_booking): ?>
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full ml-1" title="Memerlukan booking sebelumnya">
                            <i class="fas fa-star"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-600 text-center line-clamp-2 min-h-[32px]"><?php echo e($program->description); ?></p>
                </div>
                <div class="p-4">
                    <!-- Target Audience -->
                    <div class="mb-3 pb-3 border-b border-gray-100">
                        <div class="text-xs text-gray-500 mb-1">Target:</div>
                        <div class="text-xs font-semibold text-gray-700"><?php echo e($program->target_audience); ?></div>
                    </div>
                    
                    <!-- Commission Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Komisi Per Closing:</span>
                            <span class="font-semibold text-green-600"><?php echo e($program->formatted_commission); ?></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Komisi Per Click:</span>
                            <span class="font-semibold text-blue-600">Rp <?php echo e(number_format($program->default_ppc_commission, 0, ',', '.')); ?></span>
                        </div>
                        <?php if($program->max_commission_per_sale > 0): ?>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Max Per Sale:</span>
                            <span class="font-semibold text-amber-600">Rp <?php echo e(number_format($program->max_commission_per_sale, 0, ',', '.')); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Benefits Preview -->
                    <?php
                        $benefits = $program->benefits;
                        if (is_string($benefits)) {
                            $benefits = json_decode($benefits, true);
                        }
                    ?>
                    <?php if($benefits && is_array($benefits) && count($benefits) > 0): ?>
                    <div class="mb-4">
                        <div class="text-xs text-gray-500 mb-2">Benefit:</div>
                        <ul class="space-y-1">
                            <?php $__currentLoopData = array_slice($benefits, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="text-xs text-gray-700 flex items-start gap-1">
                                <i class="fas fa-check text-green-500 text-[10px] mt-0.5"></i>
                                <span class="line-clamp-1"><?php echo e($benefit); ?></span>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('affiliate.register')); ?>"
                       class="block text-center bg-green-gradient text-white font-semibold px-4 py-2.5 rounded-lg text-xs hover:opacity-90 transition">
                        Daftar Sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-12 text-gray-400">
                <i class="fas fa-info-circle text-3xl mb-3 block"></i>
                Program kemitraan akan segera hadir
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else: ?>
<!-- ===== MITRA INFO CARD ===== -->
<section class="section-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 z-content relative">
        <div class="bg-gradient-to-br from-green-50 to-white rounded-3xl border-2 border-green-200 shadow-2xl overflow-hidden">
            <div class="p-8 lg:p-12">
                <!-- Badge -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center gap-2 bg-green-600 text-white rounded-full px-6 py-2.5 shadow-lg">
                        <i class="fas fa-check-circle text-lg"></i>
                        <span class="font-bold text-sm uppercase tracking-wider">Agen Resmi HM Tour</span>
                    </div>
                </div>

                <!-- Mitra Info -->
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-green-500 shadow-xl mb-4">
                        <?php if($affiliator->photo): ?>
                            <img src="<?php echo e($affiliator->photo_url); ?>" alt="<?php echo e($affiliator->full_name); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-green-500 flex items-center justify-center text-white font-bold text-4xl">
                                <?php echo e(strtoupper(substr($affiliator->full_name, 0, 1))); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h2 class="font-playfair text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                        <?php echo e($affiliator->full_name); ?>

                    </h2>
                    
                    <p class="text-green-600 font-semibold mb-6">
                        <i class="fas fa-user-tie mr-2"></i>Agen Terpercaya HM Tour
                    </p>
                </div>

                <!-- Contact Cards -->
                <div class="grid sm:grid-cols-2 gap-4 mb-8">
                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $affiliator->phone_number)); ?>" target="_blank"
                       class="bg-white border-2 border-green-200 hover:border-green-500 rounded-2xl p-6 transition-all hover:shadow-lg group">
                        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 transition-colors">
                            <i class="fab fa-whatsapp text-green-600 group-hover:text-white text-2xl transition-colors"></i>
                        </div>
                        <div class="font-bold text-gray-900 text-sm mb-1">WhatsApp</div>
                        <div class="text-gray-600 text-sm"><?php echo e($affiliator->phone_number); ?></div>
                    </a>
                    
                    <a href="mailto:<?php echo e($affiliator->email); ?>"
                       class="bg-white border-2 border-green-200 hover:border-green-500 rounded-2xl p-6 transition-all hover:shadow-lg group">
                        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 transition-colors">
                            <i class="fas fa-envelope text-green-600 group-hover:text-white text-2xl transition-colors"></i>
                        </div>
                        <div class="font-bold text-gray-900 text-sm mb-1">Email</div>
                        <div class="text-gray-600 text-sm break-all"><?php echo e($affiliator->email); ?></div>
                    </a>
                </div>

                <!-- CTA Button -->
                <div class="text-center">
                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $affiliator->phone_number)); ?>?text=Assalamu'alaikum <?php echo e(urlencode($affiliator->full_name)); ?>, saya ingin konsultasi paket umroh HM Tour"
                       target="_blank"
                       class="inline-flex items-center gap-3 bg-green-gradient text-white font-bold px-10 py-4 rounded-full text-base transition-all shadow-xl shadow-green-300 hover:opacity-90">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        Konsultasi dengan <?php echo e(explode(' ', $affiliator->full_name)[0]); ?>

                    </a>
                    <p class="text-gray-500 text-xs mt-4">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Agen resmi dan terpercaya HM Tour
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== KONTAK / CTA ===== -->
<section id="kontak" class="section-pale py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/08/Masjidil-Haram-Makkah-Umroh-Sesuai-Sunnah.jpeg')); ?>"
             alt="" class="w-full h-full object-cover"
             onerror="this.remove()">
    </div>
    <div class="relative z-content max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white border border-green-200 rounded-full px-4 py-2 mb-6 shadow-sm">
            <i class="fas fa-phone text-green-brand text-xs"></i>
            <span class="text-green-brand text-xs font-semibold uppercase tracking-wider">Hubungi Kami</span>
        </div>
        <h2 class="font-playfair text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            Siap Berangkat ke<br><span class="text-green-gradient">Tanah Suci?</span>
        </h2>
        <p class="text-gray-600 text-lg mb-10 max-w-2xl mx-auto">
            Konsultasikan kebutuhan ibadah Anda dengan tim kami. Kami siap membantu merencanakan perjalanan umroh dan haji yang sempurna.
        </p>

        <?php if(!isset($affiliator) || !$affiliator): ?>
        
        <div class="grid sm:grid-cols-3 gap-4 mb-10">
            <a href="https://wa.me/628976688800" target="_blank"
               class="bg-white border border-green-200 hover:border-green-brand rounded-2xl p-6 transition-all hover:shadow-md group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fab fa-whatsapp text-green-500 text-2xl"></i>
                </div>
                <div class="font-semibold text-gray-900 text-sm mb-1">WhatsApp</div>
                <div class="text-gray-500 text-xs">+62 812-3456-7890</div>
            </a>
            <a href="tel:+62211234567"
               class="bg-white border border-green-200 hover:border-green-brand rounded-2xl p-6 transition-all hover:shadow-md group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-phone text-green-brand text-xl"></i>
                </div>
                <div class="font-semibold text-gray-900 text-sm mb-1">Telepon</div>
                <div class="text-gray-500 text-xs">+62 21-1234-567</div>
            </a>
            <a href="mailto:info@hmtour.co.id"
               class="bg-white border border-green-200 hover:border-green-brand rounded-2xl p-6 transition-all hover:shadow-md group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-envelope text-green-brand text-xl"></i>
                </div>
                <div class="font-semibold text-gray-900 text-sm mb-1">Email</div>
                <div class="text-gray-500 text-xs">info@hmtour.co.id</div>
            </a>
        </div>

        <a href="https://wa.me/628976688800?text=Assalamu'alaikum, saya ingin konsultasi paket umroh HM Tour"
           target="_blank"
           class="inline-flex items-center gap-3 bg-green-gradient text-white font-bold px-10 py-4 rounded-full text-base transition-all shadow-lg shadow-green-200 hover:opacity-90">
            <i class="fab fa-whatsapp text-xl"></i>
            Konsultasi Gratis Sekarang
        </a>
        <?php else: ?>
        
        <div class="grid sm:grid-cols-2 gap-4 mb-10 max-w-2xl mx-auto">
            <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $affiliator->phone_number)); ?>" target="_blank"
               class="bg-white border-2 border-green-200 hover:border-green-brand rounded-2xl p-6 transition-all hover:shadow-md group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 transition-colors">
                    <i class="fab fa-whatsapp text-green-500 group-hover:text-white text-2xl transition-colors"></i>
                </div>
                <div class="font-semibold text-gray-900 text-sm mb-1">WhatsApp <?php echo e(explode(' ', $affiliator->full_name)[0]); ?></div>
                <div class="text-gray-600 text-xs"><?php echo e($affiliator->phone_number); ?></div>
            </a>
            <a href="mailto:<?php echo e($affiliator->email); ?>"
               class="bg-white border-2 border-green-200 hover:border-green-brand rounded-2xl p-6 transition-all hover:shadow-md group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 transition-colors">
                    <i class="fas fa-envelope text-green-brand group-hover:text-white text-xl transition-colors"></i>
                </div>
                <div class="font-semibold text-gray-900 text-sm mb-1">Email <?php echo e(explode(' ', $affiliator->full_name)[0]); ?></div>
                <div class="text-gray-600 text-xs break-all"><?php echo e($affiliator->email); ?></div>
            </a>
        </div>

        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $affiliator->phone_number)); ?>?text=Assalamu'alaikum <?php echo e(urlencode($affiliator->full_name)); ?>, saya ingin konsultasi paket umroh HM Tour"
           target="_blank"
           class="inline-flex items-center gap-3 bg-green-gradient text-white font-bold px-10 py-4 rounded-full text-base transition-all shadow-lg shadow-green-200 hover:opacity-90">
            <i class="fab fa-whatsapp text-xl"></i>
            Chat <?php echo e(explode(' ', $affiliator->full_name)[0]); ?> Sekarang
        </a>
        <?php endif; ?>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="bg-green-brand text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Brand -->
            <div class="lg:col-span-2">
                <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2025/10/Logo-HM_UMRAH-3-WHITE.png')); ?>"
                     alt="HM Tour Logo" class="h-12 w-auto mb-4 object-contain"
                     onerror="this.style.display='none'">
                <p class="text-green-100 text-sm leading-relaxed mb-6 max-w-sm">
                    HM Tour & Travel — Penyelenggara Umroh dan Haji terpercaya, berizin resmi Kemenag RI dengan Akreditasi A. Melayani jamaah dari seluruh Indonesia sejak 2015.
                </p>
                <div class="flex gap-3">
                    <?php $__currentLoopData = ['instagram' => 'fab fa-instagram', 'facebook' => 'fab fa-facebook', 'youtube' => 'fab fa-youtube', 'tiktok' => 'fab fa-tiktok']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="#" class="w-9 h-9 bg-green-700 hover:bg-green-600 rounded-lg flex items-center justify-center text-green-200 hover:text-white transition-all">
                        <i class="<?php echo e($icon); ?> text-sm"></i>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Paket -->
            <div>
                <h4 class="font-semibold text-white mb-4 text-sm">Paket Umroh</h4>
                <ul class="space-y-2">
                    <?php $__currentLoopData = ['Umroh Reguler', 'Umroh Plus Turki', 'Umroh Plus Dubai', 'Umroh Plus Mesir', 'Umroh Plus Al-Ula', 'Haji Plus (ONH+)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="#paket" class="text-green-200 hover:text-white text-xs transition-colors">
                            <i class="fas fa-chevron-right text-green-400 mr-1 text-xs"></i><?php echo e($item); ?>

                        </a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <!-- Info -->
            <div>
                <h4 class="font-semibold text-white mb-4 text-sm">Informasi</h4>
                <ul class="space-y-2">
                    <?php $__currentLoopData = ['Tentang Kami', 'Program Kemitraan', 'Syarat & Ketentuan', 'Kebijakan Privasi', 'FAQ', 'Kontak Kami']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="#" class="text-green-200 hover:text-white text-xs transition-colors">
                            <i class="fas fa-chevron-right text-green-400 mr-1 text-xs"></i><?php echo e($item); ?>

                        </a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <div class="mt-6">
                    <h4 class="font-semibold text-white mb-3 text-sm">Kantor Pusat</h4>
                    <p class="text-green-200 text-xs leading-relaxed">
                        <i class="fas fa-map-marker-alt text-green-300 mr-2"></i>Jakarta, Indonesia
                    </p>
                    <p class="text-green-200 text-xs mt-2">
                        <i class="fas fa-clock text-green-300 mr-2"></i>Senin - Sabtu: 08.00 - 17.00 WIB
                    </p>
                </div>
            </div>
        </div>

        <div class="divider-green mb-6 opacity-30"></div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-green-200 text-xs">&copy; <?php echo e(date('Y')); ?> HM Tour & Travel. All rights reserved. Berizin Kemenag RI.</p>
            <div class="flex items-center gap-2">
                <span class="text-green-300 text-xs">Powered by</span>
                <a href="admin" class="text-green-100 hover:text-white text-xs transition-colors font-medium">HM System</a>
            </div>
        </div>
    </div>
</footer>

<!-- ===== WHATSAPP FLOAT ===== -->
<div class="wa-float">
    <?php if(!isset($affiliator) || !$affiliator): ?>
    <a href="https://wa.me/628976688800?text=Assalamu'alaikum, saya ingin info paket umroh HM Tour"
       target="_blank" title="Chat WhatsApp">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>
    <?php else: ?>
    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $affiliator->phone_number)); ?>?text=Assalamu'alaikum <?php echo e(urlencode($affiliator->full_name)); ?>, saya ingin info paket umroh HM Tour"
       target="_blank" title="Chat <?php echo e($affiliator->full_name); ?>">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>
    <?php endif; ?>
</div>

<!-- ===== SCRIPTS ===== -->
<script>
// ===== NAVBAR SCROLL =====
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
});

// ===== MOBILE MENU =====
document.getElementById('menu-btn').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});
document.querySelectorAll('#mobile-menu a').forEach(a => {
    a.addEventListener('click', () => document.getElementById('mobile-menu').classList.add('hidden'));
});

// ===== LOGIN DROPDOWN =====
function toggleLoginDropdown() {
    const dd = document.getElementById('login-dropdown');
    const chevron = document.getElementById('login-chevron');
    dd.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}
function toggleMobileLogin() {
    document.getElementById('mobile-login-dropdown').classList.toggle('hidden');
}
// Tutup dropdown saat klik di luar
document.addEventListener('click', (e) => {
    const wrapper = document.getElementById('login-dropdown-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('login-dropdown').classList.add('hidden');
        document.getElementById('login-chevron').classList.remove('rotate-180');
    }
    const mWrapper = document.getElementById('mobile-login-wrapper');
    if (mWrapper && !mWrapper.contains(e.target)) {
        const mdd = document.getElementById('mobile-login-dropdown');
        if (mdd) mdd.classList.add('hidden');
    }
});

// ===== AUTO SCROLL ke hasil pencarian =====
<?php if(isset($searchResults)): ?>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('hasil-cari');
    if (el) setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300);
});
<?php endif; ?>

// ===== PAPER PLANE restart after each cycle =====
(function() {
    const plane = document.getElementById('paper-plane');
    if (!plane) return;
    // Randomize vertical start slightly each cycle
    plane.addEventListener('animationiteration', () => {
        const topVariants = ['50%','45%','55%','48%','52%'];
        plane.style.setProperty('--start-top', topVariants[Math.floor(Math.random() * topVariants.length)]);
    });
})();

// ===== COUNTER ANIMATION =====
(function() {
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.dataset.target);
            const suffix = target >= 1000 ? 'K+' : '+';
            const displayTarget = target >= 1000 ? target / 1000 : target;
            let current = 0;
            const step = displayTarget / 60;
            const timer = setInterval(() => {
                current = Math.min(current + step, displayTarget);
                el.textContent = (current >= displayTarget ? displayTarget : Math.floor(current)) + suffix;
                if (current >= displayTarget) clearInterval(timer);
            }, 25);
            observer.unobserve(el);
        });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
})();

// ===== DEBRIS PARTICLE EFFECT =====
(function() {
    const canvas = document.getElementById('debris-canvas');
    const ctx = canvas.getContext('2d');
    let W, H, mouse = { x: -999, y: -999 };
    const PARTICLE_COUNT = 60;
    const particles = [];

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Track mouse
    window.addEventListener('mousemove', e => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });
    window.addEventListener('mouseleave', () => { mouse.x = -999; mouse.y = -999; });

    // Particle class
    class Particle {
        constructor() { this.reset(true); }
        reset(init) {
            this.x  = Math.random() * (W || window.innerWidth);
            this.y  = Math.random() * (H || window.innerHeight);
            this.vx = (Math.random() - 0.5) * 0.4;
            this.vy = (Math.random() - 0.5) * 0.4;
            this.r  = Math.random() * 4 + 2;
            // Green palette
            const greens = ['rgba(46,125,50,', 'rgba(76,175,80,', 'rgba(129,199,132,', 'rgba(200,230,201,'];
            this.color = greens[Math.floor(Math.random() * greens.length)];
            this.alpha = Math.random() * 0.35 + 0.08;
            this.shape = Math.random() > 0.5 ? 'circle' : 'rect';
            this.angle = Math.random() * Math.PI * 2;
            this.spin  = (Math.random() - 0.5) * 0.02;
        }
        update() {
            // Repel from mouse
            const dx = this.x - mouse.x;
            const dy = this.y - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const REPEL_RADIUS = 120;
            if (dist < REPEL_RADIUS && dist > 0) {
                const force = (REPEL_RADIUS - dist) / REPEL_RADIUS;
                this.vx += (dx / dist) * force * 1.8;
                this.vy += (dy / dist) * force * 1.8;
            }
            // Damping
            this.vx *= 0.97;
            this.vy *= 0.97;
            // Clamp speed
            const speed = Math.sqrt(this.vx * this.vx + this.vy * this.vy);
            if (speed > 3) { this.vx = (this.vx / speed) * 3; this.vy = (this.vy / speed) * 3; }

            this.x += this.vx;
            this.y += this.vy;
            this.angle += this.spin;

            // Wrap around
            if (this.x < -20) this.x = W + 20;
            if (this.x > W + 20) this.x = -20;
            if (this.y < -20) this.y = H + 20;
            if (this.y > H + 20) this.y = -20;
        }
        draw() {
            ctx.save();
            ctx.globalAlpha = this.alpha;
            ctx.fillStyle = this.color + this.alpha + ')';
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle);
            if (this.shape === 'circle') {
                ctx.beginPath();
                ctx.arc(0, 0, this.r, 0, Math.PI * 2);
                ctx.fill();
            } else {
                ctx.fillRect(-this.r, -this.r, this.r * 2, this.r * 2);
            }
            ctx.restore();
        }
    }

    // Init particles
    for (let i = 0; i < PARTICLE_COUNT; i++) particles.push(new Particle());

    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, W, H);
        // Draw connecting lines between nearby particles
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const d  = Math.sqrt(dx * dx + dy * dy);
                if (d < 100) {
                    ctx.save();
                    ctx.globalAlpha = (1 - d / 100) * 0.08;
                    ctx.strokeStyle = '#4CAF50';
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        }
        particles.forEach(p => { p.update(); p.draw(); });
        requestAnimationFrame(animate);
    }
    animate();
})();
</script>

<!-- Crop Display Helper -->
<script src="<?php echo e(asset('js/crop-display.js')); ?>"></script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/homepage.blade.php ENDPATH**/ ?>