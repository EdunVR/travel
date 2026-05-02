<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa; /* Warna latar belakang */
    }

    

    .main-sidebar.collapsed {
        width: 60px; /* Lebar sidebar saat collapse */
    }

    .scroll-indicator {
        position: absolute;
        left: 0;
        right: 0;
        height: 20px;
        z-index: 1;
    }

    .scroll-indicator-bottom {
        bottom: 0;
        transform: rotate(180deg); /* Flip the gradient for the bottom */
    }

    /* User panel */
    .user-panel {
        background: #34495e; /* Warna latar belakang yang lebih gelap */
        padding: 15px 1px;
        text-align: center;
        align-items: center;
        flex-direction: column;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        height: 170px;
    }
    .user-panel img {
        margin-top: 20px;
        width: 200px;
        height: 150px;
        scale: 1.5;
        transition: transform 0.3s ease-in-out;
    }
    .user-panel img:hover {
        transform: scale(1.5);
    }

    .user-panel .infox {
        margin-top: 30px;
        text-align: center;
    }

    .user-panel .infox p {
        margin: 5px 0px;
        font-size: 14px;
        font-weight: 600;
        color: #ecf0f1;
        text-align: center;
    }

    .user-panel .infox a {
        color: #ecf0f1;
        font-size: 12px;
        text-align: center;
    }

   /* Styling untuk card menu */
.sidebar-menu {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 kolom dengan lebar yang sama */
    gap: 10px; /* Jarak antar card */
    padding: 10px;
}

.menu-card {
    width: 100%; /* Lebar card menyesuaikan dengan kolom */
    height: 150px; /* Tinggi card */
    background: #ffffff; /* Background putih */
    border-radius: 10px; /* Sudut rounded */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Shadow untuk efek card */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    position: relative;
    overflow: hidden;
    padding: 10px; /* Padding untuk card */
}

/* Saat sidebar collapse */
.main-sidebar.collapsed .sidebar-menu {
    grid-template-columns: repeat(1, 1fr); /* 1 kolom saat sidebar collapse */
}

.main-sidebar.collapsed .menu-card {
    width: 60px; /* Lebar card lebih kecil */
    height: 60px; /* Tinggi card lebih kecil */
    padding: 5px; /* Padding lebih kecil */
}

.main-sidebar.collapsed .menu-card .icon-container i {
    font-size: 24px; /* Ukuran icon lebih kecil */
}

.menu-card:hover {
    transform: translateY(-5px); /* Efek hover */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    background-color: #2ecc71;
}

/* Hover effects for menu cards */
.menu-card:hover .icon-container {
    color: #ffffff !important; /* White icon on hover */
}

.menu-card:hover .icon-container i {
    color: #ffffff !important; /* White icon on hover */
}

.menu-card:hover .abbreviation {
    color: #ffffff !important; /* White text on hover */
}

.menu-card:hover .title {
    color: rgba(255, 255, 255, 0.8) !important; /* Slightly transparent white on hover */
}

/* Special hover effect for active cards */
.menu-card.active:hover {
    background-color: #27ae60 !important; /* Slightly darker green for active hover */
}

/* Transition effects for smooth color changes */
.icon-container, 
.icon-container i,
.abbreviation,
.title {
    transition: color 0.3s ease-in-out;
}

/* Ensure the toggle icon also changes color on hover */
.menu-card:hover .toggle-icon {
    color: #ffffff !important;
}

/* For collapsed state hover effects */
.main-sidebar.collapsed .menu-card:hover .icon-container i {
    color: #ffffff !important;
}

/* Icon container (70% bagian atas) */
.menu-card .icon-container {
    height: 70%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(20, 120, 60, 1); /* hijau gelap */
}

.menu-card .icon-container i {
    font-size: 32px; /* Ukuran icon */
    color: #3498db; /* Warna icon hitam saat idle */
}

/* Singkatan (20% bagian tengah) */
.menu-card .abbreviation {
    height: 20%;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50; /* Warna teks */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px; /* Jarak dari icon */
}

/* Judul menu (10% bagian bawah) */
.menu-card .title {
    height: 10%;
    font-size: 12px;
    color: #7f8c8d; /* Warna teks */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px; /* Jarak dari singkatan */
}

/* Icon toggle */
.menu-card .toggle-icon {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 14px;
    color: #2c3e50; /* Warna icon */
    transition: transform 0.3s ease-in-out;
}

/* Rotasi icon saat sub-menu terbuka */
.menu-card.active .toggle-icon {
    transform: rotate(180deg);
}

/* Animasi untuk menu aktif */
.menu-card.active {
    background: #2ecc71;
    color: #ffffff; /* Warna teks putih untuk kontras */
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.menu-card.active .icon-container,
.menu-card.active .abbreviation,
.menu-card.active .title {
    color: #ffffff; /* Warna teks putih saat aktif */
}

.menu-card.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(46, 204, 113, 0.1); /* Warna fill hijau transparan */
    border-radius: 10px; /* Sudut melengkung */
    animation: fill-breathing 2s infinite ease-in-out; /* Animasi breathing */
    z-index: -1;
}

/* Animasi breathing effect */
@keyframes fill-breathing {
    0% {
        opacity: 0.5; /* Opacity rendah */
    }
    50% {
        opacity: 1; /* Opacity tinggi */
    }
    100% {
        opacity: 0.5; /* Kembali ke opacity rendah */
    }
}

/* Outer glow effect untuk menu aktif */
.menu-card.active::after {
    content: '';
    position: absolute;
    left: -5px;
    top: -5px;
    right: -5px;
    bottom: -5px;
    border: 2px solid rgba(52, 152, 219, 0.5); /* Warna border glow */
    border-radius: 15px; /* Sudut melengkung lebih besar */
    opacity: 0;
    animation: glow-pulse 2s infinite ease-in-out; /* Animasi glow */
    z-index: -1;
}

@keyframes glow-pulse {
    0% {
        opacity: 0;
        box-shadow: 0 0 0 rgba(52, 152, 219, 0.3);
    }
    50% {
        opacity: 1;
        box-shadow: 0 0 10px rgba(52, 152, 219, 0.6); /* Efek glow */
    }
    100% {
        opacity: 0;
        box-shadow: 0 0 0 rgba(52, 152, 219, 0.3);
    }
}

/* Saat sidebar collapse */
.main-sidebar.collapsed .menu-card .abbreviation,
.main-sidebar.collapsed .menu-card .title {
    display: none; /* Sembunyikan abbreviation dan title */
}

/* Sesuaikan ukuran card saat sidebar collapse */
.main-sidebar.collapsed .menu-card {
    width: 60px; /* Lebar card lebih kecil */
    height: 60px; /* Tinggi card lebih kecil */
    padding: 5px; /* Padding lebih kecil */
}

/* Sesuaikan ukuran icon saat sidebar collapse */
.main-sidebar.collapsed .menu-card .icon-container i {
    font-size: 24px; /* Ukuran icon lebih kecil */
}

.sidebar-toggle i {
    font-size: 16px;
    color: #2c3e50;
}

/* Rotasi icon saat sidebar collapse */
.main-sidebar.collapsed .sidebar-toggle i {
    transform: rotate(0); /* Tidak perlu rotasi untuk icon garis tiga */
}

/* Gaya hover untuk menu non-aktif */
.sidebar-menu li:not(.active) a:hover {
    background: rgba(52, 152, 219, 0.1); /* Warna biru transparan 10% saat hover */
    color: #3498db; /* Warna biru untuk teks saat hover */
}

/* Fill animation untuk menu non-aktif saat hover */
.sidebar-menu li:not(.active) a:hover::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(52, 152, 219, 0.3); /* Warna fill biru transparan */
    border-radius: 8px; /* Sudut melengkung */
    animation: fill-breathing 2s infinite ease-in-out; /* Animasi breathing */
    z-index: -1;
}

/* Outer glow effect untuk menu non-aktif saat hover */
.sidebar-menu li:not(.active) a:hover::after {
    content: '';
    position: absolute;
    left: -5px;
    top: -5px;
    right: -5px;
    bottom: -5px;
    border: 2px solid rgba(52, 152, 219, 0.5); /* Warna border glow */
    border-radius: 12px; /* Sudut melengkung lebih besar */
    opacity: 0;
    animation: glow-pulse 2s infinite ease-in-out; /* Animasi glow */
    z-index: -1;
}

    /* Section headers */
    .sidebar-menu .header {
        padding: 8px 15px;
        font-size: 11px;
        text-transform: uppercase;
        color: rgba(236, 240, 241, 0.7); /* Warna teks terang dengan transparansi */
        border-bottom: 1px solid rgba(236, 240, 241, 0.2);
        margin-top: 10px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-menu .header .toggle-icon {
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-menu .header.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    /* Sub-menu styling */
    .sidebar-menu .sub-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        position: absolute;
        left: 100%; /* Muncul di sebelah kanan main-sidebar */
        top: 0;
        width: 220px; /* Lebar sub-menu */
        background: #34495e; /* Warna latar belakang sub-menu */
        border-radius: 0 8px 8px 0; /* Ujung rounded di sebelah kanan */
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-in-out, opacity 0.3s ease-in-out;
        opacity: 0;
        z-index: 1001; /* Pastikan sub-menu di atas sidebar */
    }

    .sidebar-menu .sub-menu.open {
        max-height: 500px; /* Sesuaikan dengan tinggi maksimal sub-menu */
        opacity: 1;
    }

    .sidebar-menu .sub-menu li a {
        padding: 8px 15px;
        font-size: 12px;
        color: #ecf0f1;
        display: block;
        transition: all 0.3s ease-in-out;
    }

    .sidebar-menu .sub-menu li a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #3498db;
    }

    /* Animasi untuk sub-menu */
    @keyframes slideRight {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .sidebar-menu .sub-menu.open li {
        animation: slideRight 0.3s ease-in-out;
    }

    /* Container untuk sub-menu */
.sub-menu-container {
    position: fixed;
    left: 230px; /* Sesuaikan dengan lebar sidebar */
    top: 110px; /* Mulai dari bawah menu Dashboard (sesuaikan dengan tinggi user-panel) */
    width: 250px; /* Lebar sub-menu */
    height: calc(100vh - 110px); /* Tinggi sub-menu (sesuaikan dengan tinggi user-panel) */
    background: rgba(52, 73, 94, 0.95); /* Warna latar belakang sub-menu dengan transparansi */
    border-radius: 0 8px 8px 0; /* Ujung rounded di sebelah kanan */
    box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 1001; /* Pastikan sub-menu di atas sidebar */
    display: none; /* Sembunyikan secara default */
    overflow-y: auto; /* Tambahkan scroll jika konten terlalu panjang */
    padding: 10px 0; /* Padding untuk sub-menu */
    transition: all 0.3s ease;
}

.sub-menu-container.open {
    display: block; /* Tampilkan saat dibuka */
}

/* Styling untuk sub-menu */
.sub-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sub-menu li {
    margin: 5px 10px; /* Margin antar item menu */
}

.sub-menu li a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #ecf0f1;
    padding: 10px 15px;
    font-size: 13px;
    transition: all 0.3s ease-in-out;
    background: rgba(255, 255, 255, 0.1); /* Background item menu */
    border-radius: 8px; /* Ujung rounded untuk item menu */
}

/* Feather Icons */
.sidebar-menu li a svg {
        width: 16px; /* Ukuran ikon */
        height: 16px; /* Ukuran ikon */
        margin-right: 0px; /* Jarak antara ikon dan teks */
    }

    .sidebar-menu li a span {
        font-size: 11px; /* Ukuran ikon */
        min-width: 11px;
        margin-left: 10px;
    }

.sub-menu li a:hover {
    background: rgba(255, 255, 255, 0.2); /* Background saat hover */
    color: #3498db;
    transform: translateX(5px); /* Animasi geser ke kanan */
}

.sub-menu li a i {
    font-size: 16px;
    margin-right: 10px;
}

.sub-menu .unavailable a {
        color: #999; /* Warna abu-abu */
        
    }

    .sub-menu .unavailable a .unavailable-icon {
        width: 16px; /* Ukuran ikon */
        height: 16px; /* Ukuran ikon */
        margin-left: 8px; /* Jarak dari teks */
        color: #ff0000; /* Warna merah */
    }

    /* Hilangkan animasi hover pada sub-menu unavailable */
    .sub-menu .unavailable a:hover {
        background-color: transparent; /* Hilangkan background hover */
        color: #999; /* Tetap abu-abu saat hover */
    }

    .sub-menu li:not(.unavailable) a:hover {
        background-color: #f0f0f0; /* Contoh animasi hover */
        transition: background-color 0.3s ease;
    }

    /* Responsive styles for mobile */
    @media (max-width: 768px) {
        .main-sidebar {
            width: 230px; /* Sidebar expanded by default on mobile */
            transition: all 0.3s ease;
        }
        
        .main-sidebar.collapsed {
            width: 0; /* Sidebar hidden completely when collapsed */
            overflow: hidden;
        }
        
        .sub-menu-container {
            left: 0; /* Submenu takes full width on mobile */
            width: 100%;
            display: none;
        }
        
        .sub-menu-container.open {
            display: block;
        }
        
        /* Back button in submenu */
        .sub-menu-back {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            color: #ecf0f1;
            font-size: 14px;
        }
        
        .sub-menu-back i {
            margin-right: 10px;
        }
        
        .sub-menu-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    }

    /* Tambahkan style untuk hover behavior di desktop */
@media (min-width: 769px) {
    .menu-card {
        position: relative;
    }
    
    .menu-card:hover .sub-menu-container {
        display: block !important;
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }
    
    /* Delay untuk mencegah submenu langsung hilang saat cursor berpindah */
    .sub-menu-container {
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
    }
    
    /* Style untuk submenu container di desktop */
    .sub-menu-container {
        position: fixed;
        left: 230px;
        top: 110px;
        width: 250px;
        height: calc(100vh - 110px);
        background: rgba(52, 73, 94, 0.95);
        border-radius: 0 8px 8px 0;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        display: none;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-10px);
        overflow-y: auto;
        padding: 10px 0;
    }
    
    .sub-menu-container.open {
        display: block;
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }
    
    /* Hover effect untuk menu card di desktop */
    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        background-color: #2ecc71;
        z-index: 1002; /* Pastikan di atas elemen lain saat hover */
    }
    .menu-card.active-hover {
        background-color: #2ecc71 !important;
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 1002;
    }
    
    .menu-card.active-hover .icon-container i,
    .menu-card.active-hover .abbreviation,
    .menu-card.active-hover .title,
    .menu-card.active-hover .toggle-icon {
        color: #ffffff !important;
    }
}
</style>

<aside class="main-sidebar">
    <div class="scroll-indicator"></div>
    <div class="user-panel">
        <div class="image">
            <img src="{{ url($setting->path_logo) ?? Auth::user()->foto }}" alt="User Image">
        </div>
        <div class="infox">
            <p>{{ $setting->nama_perusahaan }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
        <!-- Card untuk Dashboard -->
        @if(in_array('Dashboard', Auth::user()->akses ?? []))
        <div class="menu-card {{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="window.location.href='{{ route('dashboard') }}'">
            <div class="icon-container">
                <i data-feather="home"></i>
            </div>
            <div class="abbreviation">DASH</div>
            <div class="title">Dashboard</div>
        </div>
        @endif

        @if(hasAnyAccess(['Outlet', 'Kategori', 'Satuan', 'Produk', 'Bahan', 'Inventori', 'Gudang']))
        <div class="menu-card {{ request()->routeIs('inventory*') ? 'active' : '' }}" onclick="handleMenuClick('inventory')">
            <div class="icon-container">
                <i data-feather="package"></i>
            </div>
            <div class="abbreviation">IM</div>
            <div class="title">Inventaris</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Investor', 'Profit Management', 'Withdrawal Management']))
        <div class="menu-card {{ request()->routeIs('irp*') ? 'active' : '' }}" onclick="handleMenuClick('irp')">
            <div class="icon-container">
                <i data-feather="user-check"></i>
            </div>
            <div class="abbreviation">IRP</div>
            <div class="title">Investor</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Customer', 'Prospek', 'Tipe']))
        <div class="menu-card {{ request()->routeIs('customer-service*') ? 'active' : '' }}" onclick="handleMenuClick('customer-service')">
            <div class="icon-container">
                <i data-feather="user-check"></i>
            </div>
            <div class="abbreviation">CRM</div>
            <div class="title">Pelanggan</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        <!-- Card untuk POS -->
        @if(hasAnyAccess(['Transaksi', 'Kontra Bon']))
        <div class="menu-card {{ request()->routeIs('pos*') ? 'active' : '' }}" onclick="handleMenuClick('pos')">
            <div class="icon-container">
                <i data-feather="credit-card"></i>
            </div>
            <div class="abbreviation">PoS</div>
            <div class="title">Point of Sales</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        <!-- Card untuk Keuangan (Finance) -->
        @if(hasAnyAccess(['Pengeluaran', 'Pembelian', 'Penjualan', 'Hutang', 'Piutang', 'RAB', 'Akun dan Buku', 'Jurnal', 'Aktiva Tetap', 'Buku Besar', 'Neraca Lajur', 'Laba Rugi', 'Perubahan Modal', 'Neraca', 'Arus Kas', 'SPT Tahunan', 'Pelaporan Keuangan']))
        <div class="menu-card {{ request()->routeIs('finance*') ? 'active' : '' }}" onclick="handleMenuClick('finance')">
            <div class="icon-container">
                <i data-feather="dollar-sign"></i>
            </div>
            <div class="abbreviation">F&A</div>
            <div class="title">Keuangan</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        <!-- Card untuk Sumber Daya Manusia (HRM) -->
        @if(hasAnyAccess(['User', 'Pengaturan', 'Rekrutmen', 'Payroll', 'Kinerja', 'Pelatihan', 'Absensi']))
        <div class="menu-card {{ request()->routeIs('hrm*') ? 'active' : '' }}" onclick="handleMenuClick('hrm')">
            <div class="icon-container">
                <i data-feather="users"></i>
            </div>
            <div class="abbreviation">HRM</div>
            <div class="title">SDM</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        <!-- Card untuk Penjualan & Pemasaran (Sales & Marketing) -->
        @if(hasAnyAccess(['Penjualan', 'Laporan Penjualan', 'Agen', 'Halaman Agen', 'Invoice Penjualan']))
        <div class="menu-card {{ request()->routeIs('sales*') ? 'active' : '' }}" onclick="handleMenuClick('sales')">
            <div class="icon-container">
                <i data-feather="shopping-cart"></i>
            </div>
            <div class="abbreviation">S&M</div>
            <div class="title">Penjualan</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif
    
        @if(hasAnyAccess(['Pembelian', 'Supplier']))
        <div class="menu-card {{ request()->routeIs('procurement*') ? 'active' : '' }}" onclick="handleMenuClick('procurement')">
            <div class="icon-container">
                <i data-feather="shopping-bag"></i>
            </div>
            <div class="abbreviation">PM</div>
            <div class="title">Pembelian</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Produksi']))
        <div class="menu-card {{ request()->routeIs('production*') ? 'active' : '' }}" onclick="handleMenuClick('production')">
            <div class="icon-container">
                <i data-feather="grid"></i>
            </div>
            <div class="abbreviation">MRP</div>
            <div class="title">Produksi</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif
        
        @if(hasAnyAccess(['Gudang']) || hasPermission('supply-chain.permintaan-barang.view'))
        <div class="menu-card {{ request()->routeIs('supply-chain*') ? 'active' : '' }}" onclick="handleMenuClick('supply-chain')">
            <div class="icon-container">
                <i data-feather="truck"></i>
            </div>
            <div class="abbreviation">SCM</div>
            <div class="title">Rantai Pasok</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Ongkir Service', 'Mesin Customer', 'Invoice Service', 'History Service']))
        <div class="menu-card {{ request()->routeIs('services*') ? 'active' : '' }}" onclick="handleMenuClick('services')">
            <div class="icon-container">
                <i data-feather="tool"></i>
            </div>
            <div class="abbreviation">SM</div>
            <div class="title">Service</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Project Management']))
        <div class="menu-card {{ request()->routeIs('project-management*') ? 'active' : '' }}" onclick="handleMenuClick('project-management')">
            <div class="icon-container">
                <i data-feather="layers"></i>
            </div>
            <div class="abbreviation">PM</div>
            <div class="title">Manajemen Proyek</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        {{-- Travel Management --}}
        @if(hasAnyAccess(['Flight', 'Hotel', 'Travel Package', 'Keberangkatan', 'Task Management']))
        <div class="menu-card {{ request()->routeIs('travel*') || request()->routeIs('admin.inventaris.flight.*') || request()->routeIs('admin.inventaris.hotel.*') ? 'active' : '' }}" onclick="handleMenuClick('travel')">
            <div class="icon-container">
                <i data-feather="map"></i>
            </div>
            <div class="abbreviation">TRV</div>
            <div class="title">Travel</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['Laporan', 'Laporan Penjualan']))
        <div class="menu-card {{ request()->routeIs('analytics*') ? 'active' : '' }}" onclick="handleMenuClick('analytics')">
            <div class="icon-container">
                <i data-feather="pie-chart"></i>
            </div>
            <div class="abbreviation">A&R</div>
            <div class="title">Analisis & Pelaporan</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif

        @if(hasAnyAccess(['User', 'Pengaturan']))
        <div class="menu-card {{ request()->routeIs('system*') ? 'active' : '' }}" onclick="handleMenuClick('system')">
            <div class="icon-container">
                <i data-feather="settings"></i>
            </div>
            <div class="abbreviation">SYS</div>
            <div class="title">Sistem</div>
            <i class="toggle-icon" data-feather="chevron-down"></i>
        </div>
        @endif
    </ul>
    <div class="scroll-indicator scroll-indicator-bottom"></div>
</aside>

<!-- Container untuk sub-menu -->
<div id="sub-menu-container" class="sub-menu-container">
    <!-- Sub-menu akan dimuat di sini -->
</div>

<!-- Modal untuk fitur terbatas -->
<div id="unavailableFeatureModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content animate__animated animate__fadeInDown"> <!-- Tambahkan animasi -->
            <div class="modal-header">
                <h5 class="modal-title">Fitur Terbatas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Ilustrasi dari Undraw -->
                <img src="https://cdn.undraw.co/illustration/in-the-office_e7pg.svg" alt="Fitur Terbatas" style="width: 100%; max-width: 200px; margin: 0 auto; display: block;">

                <!-- Pesan -->
                <p class="mt-3">
                    Maaf, fitur ini sedang dalam pengembangan. 
                    Untuk mendapatkan akses penuh ke fitur ini, silakan hubungi developer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="contactDeveloper()">Hubungi Developer</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();

    let isMobile = window.innerWidth <= 768;
    let activeMenu = null;
    let hoverTimeout = null;

    function handleMenuClick(menu) {
        // Untuk mobile, selalu gunakan behavior klik
        if (isMobile) {
            toggleSubMenu(menu);
        } else {
            toggleSubMenu(menu);
        }
    }

    function toggleSubMenu(menu) {
        const baseUrl = window.baseUrl;
        const menuElement = document.querySelector(`[onclick="handleMenuClick('${menu}')"]`);
        const subMenuContainer = document.getElementById('sub-menu-container');

        // Jika menu yang sama diklik lagi, tutup submenu
        if (activeMenu === menu && subMenuContainer.classList.contains('open')) {
            closeSubMenu();
            return;
        }

        // Deactivate all other menu cards first
        document.querySelectorAll('.menu-card.active').forEach(activeCard => {
            if (activeCard !== menuElement) {
                activeCard.classList.remove('active');
            }
        });

        // Toggle the clicked menu card
        menuElement.classList.toggle('active');
        activeMenu = menu;

        // Muat konten sub-menu
        fetch(`${baseUrl}/partials/sidebar/${menu}`)
            .then(response => response.text())
            .then(data => {
                // Tambahkan tombol back untuk mobile
                if (isMobile) {
                    data = `<div class="sub-menu-back" onclick="backToMenu()">
                                <i data-feather="arrow-left"></i> Kembali ke Menu
                            </div>` + data;
                }
                
                subMenuContainer.innerHTML = data;
                
                // Toggle visibility berdasarkan device
                if (isMobile) {
                    const sidebar = document.querySelector('.main-sidebar');
                    sidebar.classList.add('collapsed');
                    subMenuContainer.classList.add('open');
                } else {
                    // Desktop behavior
                    subMenuContainer.classList.add('open');
                }
                
                feather.replace();
                addUnavailableFeatureListeners();
            })
            .catch(error => console.error('Error loading sub-menu:', error));
    }

    function closeSubMenu() {
        const subMenuContainer = document.getElementById('sub-menu-container');
        subMenuContainer.classList.remove('open');
        subMenuContainer.innerHTML = '';
        
        document.querySelectorAll('.menu-card.active').forEach(menu => {
            menu.classList.remove('active');
        });
        
        activeMenu = null;
    }

    // Fungsi untuk kembali ke menu utama di mobile
    function backToMenu() {
        const subMenuContainer = document.getElementById('sub-menu-container');
        const sidebar = document.querySelector('.main-sidebar');
        
        subMenuContainer.classList.remove('open');
        sidebar.classList.remove('collapsed');
        
        // Reset active state
        document.querySelectorAll('.menu-card.active').forEach(menu => {
            menu.classList.remove('active');
        });
    }

    function initializeHoverBehavior() {
        if (isMobile) return;

        const menuCards = document.querySelectorAll('.menu-card');
        const subMenuContainer = document.getElementById('sub-menu-container');

        menuCards.forEach(card => {
            if (!card.getAttribute('onclick')?.includes('handleMenuClick')) return;

            // Hover enter
            card.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                
                const menuType = this.getAttribute('onclick').match(/handleMenuClick\('([^']+)'\)/)[1];
                
                hoverTimeout = setTimeout(() => {
                    // Close previous submenu first
                    closeSubMenu();
                    
                    // Load and show new submenu
                    loadSubMenuOnHover(menuType, this);
                }, 200); // Delay 200ms untuk mencegah trigger tidak sengaja
            });

            // Hover leave untuk menu card
            card.addEventListener('mouseleave', function(e) {
                clearTimeout(hoverTimeout);
                
                // Delay menutup submenu untuk memberi waktu berpindah ke submenu
                hoverTimeout = setTimeout(() => {
                    if (!isMouseOverSubMenu) {
                        closeSubMenu();
                    }
                }, 300);
            });
        });

        // Track mouse position untuk submenu container
        let isMouseOverSubMenu = false;

        subMenuContainer.addEventListener('mouseenter', function() {
            isMouseOverSubMenu = true;
            clearTimeout(hoverTimeout);
        });

        subMenuContainer.addEventListener('mouseleave', function() {
            isMouseOverSubMenu = false;
            hoverTimeout = setTimeout(() => {
                closeSubMenu();
            }, 300);
        });
    }

    // Fungsi untuk memuat submenu pada hover
    function loadSubMenuOnHover(menu, menuElement) {
        const baseUrl = window.baseUrl;
        const subMenuContainer = document.getElementById('sub-menu-container');

        fetch(`${baseUrl}/partials/sidebar/${menu}`)
            .then(response => response.text())
            .then(data => {
                subMenuContainer.innerHTML = data;
                subMenuContainer.classList.add('open');
                
                // Highlight menu card yang sedang dihover
                document.querySelectorAll('.menu-card').forEach(card => {
                    card.classList.remove('active-hover');
                });
                menuElement.classList.add('active-hover');
                
                feather.replace();
                addUnavailableFeatureListeners();
            })
            .catch(error => console.error('Error loading sub-menu on hover:', error));
    }

    // Fungsi untuk menambahkan event listener ke sub-menu yang belum tersedia
    function addUnavailableFeatureListeners() {
        document.querySelectorAll('.sub-menu a[href="#"]').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); // Mencegah perilaku default link
                showUnavailableFeatureModal(); // Tampilkan modal
            });
        });
    }

    // Fungsi untuk menampilkan modal
    function showUnavailableFeatureModal() {
        const modal = $('#unavailableFeatureModal');
        modal.find('.modal-content').removeClass('animate__fadeOutUp').addClass('animate__fadeInDown');
        modal.modal('show');
    }

    $('#unavailableFeatureModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').removeClass('animate__fadeInDown').addClass('animate__fadeOutUp');
    });

    // Fungsi untuk menghubungi developer
    function contactDeveloper() {
        const phoneNumber = "+6285795483498"; // Nomor WhatsApp Anda
        const message = "Halo, saya ingin mendapatkan akses penuh ke fitur terbatas di demo aplikasi ERP."; // Pesan default
        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank'); // Buka di tab baru
    }

    document.addEventListener('click', function(event) {
        if (isMobile) return;
        
        const subMenuContainer = document.getElementById('sub-menu-container');
        
        if (!event.target.closest('.sidebar-menu') && !event.target.closest('.sub-menu-container')) {
            closeSubMenu();
        }
    });

    window.addEventListener('resize', function() {
        const oldIsMobile = isMobile;
        isMobile = window.innerWidth <= 768;
        
        // Jika berpindah dari desktop ke mobile atau sebaliknya, reset state
        if (oldIsMobile !== isMobile) {
            closeSubMenu();
            
            if (isMobile) {
                // Behavior mobile
                document.querySelector('.main-sidebar').classList.remove('expanded');
            } else {
                // Behavior desktop - initialize hover
                initializeHoverBehavior();
            }
        }
    });

    if (!isMobile) {
        initializeHoverBehavior();
    } else {
        document.querySelector('.main-sidebar').classList.remove('expanded');
    }
</script>
