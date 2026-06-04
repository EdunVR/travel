<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?> - <?php echo e($title ?? 'Investor Portal'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

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
            font-size: 18px;
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
            width: 100vw;
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
            background-color: #FFD700;
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
            background-color: #2E7D32;
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
            color: #FFD700;
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

        .nav-item:nth-child(2) {
            margin-right: 30px;
        }

        .nav-item:nth-child(4) {
            margin-left: 30px;
        }

        .nav-item:nth-child(1),
        .nav-item:nth-child(5) {
            flex: 1;
        }
        
        .nav-item-center.active {
            background-color: #1B5E20;
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
            font-size: 20px;
            font-family: 'Dongle', sans-serif;
            font-weight: 400;
        }
        
        .nav-item-center .nav-label {
            position: absolute;
            bottom: -15px;
            color: #2E7D32;
            font-weight: 700;
            font-size: 18px;
        }
        
        .nav-item-center.active .nav-label {
            color: #1B5E20;
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
                font-size: 16px;
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
            font-size: 18px;
        }
        
        /* Table Styling */
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-dark);
            font-size: 16px;
        }
        
        /* Button Styling */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-size: 16px;
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
            font-size: 18px;
            color: #333;
            text-align: right;
        }
        
        .user-name {
            font-family: 'Dongle', sans-serif;
            font-size: 22px;
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
            max-width: 100vw;
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
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
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
                <a href="<?php echo e(route('investor.dashboard')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('investor.investments')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.investments') ? 'active' : ''); ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Rekening</span>
                </a>
                <a href="<?php echo e(route('investor.profits')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.profits') ? 'active' : ''); ?>">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Bagi Hasil</span>
                </a>
                <a href="<?php echo e(route('investor.withdrawals')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.withdrawals') ? 'active' : ''); ?>">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Status Pencairan</span>
                </a>
                <a href="<?php echo e(route('investor.documents')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.documents') ? 'active' : ''); ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Dokumen</span>
                </a>
                <a href="<?php echo e(route('investor.profile')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('investor.profile') ? 'active' : ''); ?>">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <nav class="top-nav">
                <div>
                    <img src="<?php echo e(asset('img/logo_2.png')); ?>" alt="SUN" class="logo_syirkah">
                </div>
                <div>
                    <div class="user-greeting">Assalamualaikum!</div>
                    <div class="user-name"><?php echo e(Auth::guard('investor')->user()->name); ?></div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-info">
                                <?php
                                    $user = Auth::guard('investor')->user();
                                    $photo = $user->photo 
                                                ? asset('storage/' . $user->photo) 
                                                : ($user->jenis_kelamin === 'Perempuan' 
                                                    ? asset('img/investor_user_perempuan.png') 
                                                    : asset('img/investor_user.png'));
                                ?>

                                <img src="<?php echo e($photo); ?>" alt="User Avatar" class="user-avatar">
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="<?php echo e(route('investor.profile')); ?>" style="font-size: 16px;">
                                <i class="fas fa-user me-2"></i>
                                Profil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('investor.logout')); ?>">
                                    <?php echo csrf_field(); ?>
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

            <!-- Page Content -->
            <div class="container-fluid px-0">
                <?php echo $__env->make('partials.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo e($slot); ?>

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
                <a href="<?php echo e(route('investor.investments')); ?>" class="nav-item <?php echo e(request()->routeIs('investor.investments') ? 'active' : ''); ?>">
                    <i class="fas fa-wallet nav-icon"></i>
                    <span class="nav-label">Rekening</span>
                </a>
                
                <a href="<?php echo e(route('investor.profits')); ?>" class="nav-item <?php echo e(request()->routeIs('investor.profits') ? 'active' : ''); ?>">
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <span class="nav-label">Bagi Hasil</span>
                </a>
                
                <!-- Tombol home di tengah -->
                <div class="nav-item-center <?php echo e(request()->routeIs('investor.dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('investor.dashboard')); ?>" class="flex flex-col items-center">
                        <i class="fas fa-home nav-icon"></i>
                    </a>
                </div>
                
                <a href="<?php echo e(route('investor.withdrawals')); ?>" class="nav-item <?php echo e(request()->routeIs('investor.withdrawals') ? 'active' : ''); ?>">
                    <i class="fas fa-money-bill-wave nav-icon"></i>
                    <span class="nav-label">Pencairan</span>
                </a>
                
                <a href="<?php echo e(route('investor.documents')); ?>" class="nav-item <?php echo e(request()->routeIs('investor.documents') ? 'active' : ''); ?>">
                    <i class="fas fa-file-alt nav-icon"></i>
                    <span class="nav-label">Dokumen</span>
                </a>
            </div>
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
        if (homeLink) {
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
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\components\investor\layout.blade.php ENDPATH**/ ?>