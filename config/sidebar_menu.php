<?php

return [
    'Master/Inventaris' => [
        'module' => 'inventaris',
        'route' => 'admin.inventaris.index',
        'icon' => 'boxes',
        'items' => [
            ['name' => 'Outlet', 'route' => 'admin.inventaris.outlet.index', 'permissions' => ['inventaris.outlet.view']],
            ['name' => 'Penerbangan', 'route' => 'admin.inventaris.flight.index', 'permissions' => ['master.flight.view']],
            ['name' => 'Hotel', 'route' => 'admin.inventaris.hotel.index', 'permissions' => ['master.hotel.view']],
            ['name' => 'Kategori Umum', 'route' => 'admin.inventaris.kategori.index', 'permissions' => ['inventaris.kategori.view']],
            ['name' => 'Satuan', 'route' => 'admin.inventaris.satuan.index', 'permissions' => ['inventaris.satuan.view']],
            ['name' => 'Produk', 'route' => 'admin.inventaris.produk.index', 'permissions' => ['inventaris.produk.view']],
            ['name' => 'Bahan', 'route' => 'admin.inventaris.bahan.index', 'permissions' => ['inventaris.bahan.view']],
            ['name' => 'Sparepart', 'route' => 'admin.inventaris.sparepart.index', 'permissions' => ['inventaris.sparepart.view']],
            ['name' => 'Inventori', 'route' => 'admin.inventaris.inventori.index', 'permissions' => ['inventaris.inventori.view']],
            ['name' => 'Transfer Gudang', 'route' => 'admin.inventaris.transfer-gudang.index', 'permissions' => ['inventaris.transfer-gudang.view']],
        ]
    ],

    'Travel Management' => [
        'module' => 'travel',
        'route' => 'admin.inventaris.travel.package.index',
        'icon' => 'plane-departure',
        'items' => [
            // Master Data
            ['name' => 'Maskapai', 'route' => 'admin.inventaris.airline.index', 'permissions' => ['travel.airline.view']],
            ['name' => 'Bandara', 'route' => 'admin.inventaris.airport.index', 'permissions' => ['travel.airport.view']],
            ['name' => 'Penerbangan', 'route' => 'admin.inventaris.flight.index', 'permissions' => ['travel.flight.view']],
            ['name' => 'Hotel', 'route' => 'admin.inventaris.hotel.index', 'permissions' => ['travel.hotel.view']],
            ['name' => 'Transportasi Saudi', 'route' => 'admin.inventaris.transport.index', 'permissions' => ['travel.transport.view']],
            
            // Package Management
            ['name' => 'Paket Perjalanan', 'route' => 'admin.inventaris.travel.package.index', 'permissions' => ['travel.package.view']],
            ['name' => 'Katalog Paket', 'route' => 'admin.inventaris.travel.catalog.index', 'permissions' => ['travel.catalog.view']],
            
            // Operations
            ['name' => 'Booking Jamaah', 'route' => 'admin.inventaris.booking.index', 'permissions' => ['travel.booking.view']],
            
            // Task Management — Task Management hanya untuk superadmin, My Tasks untuk semua role
            ['name' => 'Task Management', 'route' => 'admin.inventaris.tasks.index', 'permissions' => ['travel.task.view'], 'superadmin_only' => true],
            ['name' => 'My Tasks', 'route' => 'admin.inventaris.tasks.my-tasks', 'permissions' => ['travel.task.view']],
            
            // Communication & Reporting — Komunikasi dan Laporan disembunyikan
            // ['name' => 'Komunikasi', 'route' => 'admin.inventaris.travel.communication.index', 'permissions' => ['travel.communication.view']],
            // ['name' => 'Laporan', 'route' => 'admin.inventaris.travel.report.index', 'permissions' => ['travel.report.view']],
            ['name' => 'Pengingat Pembayaran', 'route' => 'admin.inventaris.travel.payment-reminder.index', 'permissions' => ['travel.payment-reminder.view']],

            // Affiliate
            ['name' => 'Mitra', 'route' => 'admin.inventaris.affiliate.index', 'permissions' => ['travel.affiliate.view']],
            ['name' => 'Withdraw Mitra', 'route' => 'admin.inventaris.affiliate.payouts', 'permissions' => ['travel.affiliate.payout']],
            ['name' => 'Pengaturan Affiliate', 'route' => 'admin.inventaris.affiliate.settings', 'permissions' => ['travel.affiliate.settings']],
        ]
    ],

    'Jemaah (CRM)' => [
        'module' => 'crm',
        'route' => 'admin.crm.index',
        'icon' => 'users',
        'items' => [
            ['name' => 'Jemaah dan Manifest', 'route' => 'admin.crm.pelanggan.index', 'permissions' => ['crm.pelanggan.view']],          
        ]
    ],

    'Keuangan (F&A)' => [
        'module' => 'finance',
        'route' => 'finance.dashboard.index',
        'icon' => 'wallet',
        'items' => [
            ['name' => 'Daftar Akun', 'route' => 'finance.akun.index', 'permissions' => ['finance.akun.view']],
            ['name' => 'Manajemen RAB', 'route' => 'admin.finance.rab.index', 'permissions' => ['finance.rab.view']],
            ['name' => 'Biaya', 'route' => 'finance.biaya.index', 'permissions' => ['finance.biaya.view']],
            ['name' => 'Hutang', 'route' => 'finance.hutang.index', 'permissions' => ['finance.hutang.view']],
            ['name' => 'Piutang', 'route' => 'finance.piutang.index', 'permissions' => ['finance.piutang.view']],
            ['name' => 'Rekonsiliasi Bank', 'route' => 'finance.rekonsiliasi.index', 'permissions' => ['finance.rekonsiliasi.view']],
            ['name' => 'Buku Akuntansi', 'route' => 'finance.buku.index', 'permissions' => ['finance.buku.view']],
            ['name' => 'Saldo Awal', 'route' => 'finance.saldo-awal.index', 'permissions' => ['finance.buku.view']],
            ['name' => 'Jurnal', 'route' => 'finance.jurnal.index', 'permissions' => ['finance.jurnal.view']],
            ['name' => 'Aktiva Tetap', 'route' => 'finance.aktiva.index', 'permissions' => ['finance.aktiva.view']],
            ['name' => 'Buku Besar', 'route' => 'finance.buku-besar.index', 'permissions' => ['finance.buku-besar.view']],
            ['name' => 'Neraca', 'route' => 'finance.neraca.index', 'permissions' => ['finance.neraca.view']],
            ['name' => 'Neraca Saldo', 'route' => 'finance.neraca-saldo.index', 'permissions' => ['finance.neraca-saldo.view']],
            ['name' => 'Laporan Laba Rugi', 'route' => 'finance.laba-rugi.index', 'permissions' => ['finance.laba-rugi.view']],
            ['name' => 'Arus Kas', 'route' => 'finance.arus-kas.index', 'permissions' => ['finance.arus-kas.view']],
        ]
    ],

    'Penjualan (S&M)' => [
        'module' => 'sales',
        'route' => 'admin.penjualan.dashboard.index',
        'icon' => 'receipt-text',
        'items' => [
            ['name' => 'Penawaran dan Pre Order', 'route' => 'admin.penjualan.preorders.index', 'permissions' => ['sales.preorder.view']],
            ['name' => 'Point of Sales', 'route' => 'admin.penjualan.pos.index', 'permissions' => ['sales.pos.view']],
            ['name' => 'Penjualan Antar Outlet', 'route' => 'admin.penjualan.inter-outlet.index', 'permissions' => ['sales.inter-outlet.view']],
            ['name' => 'Invoice Penjualan', 'route' => 'admin.penjualan.invoice.index', 'permissions' => ['sales.invoice.view']],
            ['name' => 'Kontra Bon', 'route' => 'admin.penjualan.kontrabon.index', 'permissions' => ['sales.kontrabon.view']],
            ['name' => 'Laporan Penjualan', 'route' => 'admin.penjualan.laporan.index', 'permissions' => ['sales.laporan.view']],
            ['name' => 'Laporan Margin', 'route' => 'admin.penjualan.margin.index', 'permissions' => ['sales.margin.view']],
            ['name' => 'Agen & Gerobak', 'route' => 'admin.penjualan.agen_gerobak.index', 'permissions' => ['sales.agen.view']],
            ['name' => 'Halaman Agen', 'route' => 'admin.penjualan.agen.index', 'permissions' => ['sales.agen.view']],
        ]
    ],

    'Pembelian (PM)' => [
        'module' => 'procurement',
        'route' => 'pembelian.purchase-order.index',
        'icon' => 'truck',
        'items' => [
            ['name' => 'Purchase Order', 'route' => 'pembelian.purchase-order.index', 'permissions' => ['procurement.purchase-order.view']],
        ]
    ],

    'Produksi (MRP)' => [
        'module' => 'production',
        'route' => 'admin.produksi.produksi.index',
        'icon' => 'factory',
        'items' => [
            ['name' => 'Data Produksi', 'route' => 'admin.produksi.produksi.index', 'permissions' => ['production.produksi.view']],
        ]
    ],

    'Rantai Pasok (SCM)' => [
        'module' => 'supply-chain',
        'route' => 'admin.rantai-pasok',
        'icon' => 'git-branch',
        'items' => [
            ['name' => 'Permintaan Barang', 'route' => 'admin.supply-chain.permintaan-barang.index', 'permissions' => ['supply-chain.permintaan-barang.view']],
            ['name' => 'Transfer Gudang', 'route' => 'admin.inventaris.transfer-gudang.index', 'permissions' => ['inventaris.transfer-gudang.view']],
        ]
    ],

    'SDM' => [
        'module' => 'hrm',
        'route' => 'admin.sdm',
        'icon' => 'id-card',
        'hasPermission' => true,
        'items' => [
            ['name' => 'Kepegawaian & Rekrutmen', 'route' => 'sdm.kepegawaian.index', 'permissions' => ['hrm.karyawan.view']],
            ['name' => 'Penggajian / Payroll', 'route' => 'sdm.payroll.index', 'permissions' => ['hrm.payroll.view']],
            ['name' => 'Manajemen Absensi', 'route' => 'sdm.attendance.index', 'permissions' => ['hrm.absensi.view']],
            ['name' => 'Manajemen Kinerja', 'route' => 'sdm.kinerja.index', 'permissions' => ['hrm.kinerja.view']],
            ['name' => 'Kontrak & Dokumen HR', 'route' => 'sdm.kontrak.index', 'permissions' => ['hrm.kontrak.view']],
            ['name' => 'Pelatihan & Pengembangan', 'route' => '#', 'permissions' => ['hrm.karyawan.view']],
        ]
    ],

    'Service Management' => [
        'module' => 'service',
        'route' => 'admin.service',
        'icon' => 'wrench',
        'hasPermission' => true,
        'items' => [
            ['name' => 'Invoice Service', 'route' => 'admin.service.invoice.index', 'permissions' => ['service.invoice.view']],
            ['name' => 'History Service', 'route' => 'admin.service.history.index', 'permissions' => ['service.history.view']],
            ['name' => 'Ongkir Service', 'route' => 'admin.service.ongkir.index', 'permissions' => ['service.ongkir.view']],
            ['name' => 'Mesin Customer', 'route' => 'admin.service.mesin.index', 'permissions' => ['service.mesin.view']],
        ]
    ],

    'Investor' => [
        'module' => 'investor',
        'route' => 'admin.investor',
        'icon' => 'hand-coins',
        'items' => [
            ['name' => 'Profil Investor', 'route' => 'admin.investor.profil.index', 'permissions' => ['investor.profil.view']],
            ['name' => 'Bagi Hasil', 'route' => 'admin.investor.bagi-hasil.index', 'permissions' => ['investor.bagi-hasil.view']],
            ['name' => 'List Pencairan', 'route' => 'admin.investor.pencairan.index', 'permissions' => ['investor.pencairan.view']],
        ]
    ],

    'Sistem' => [
        'module' => 'sistem',
        'route' => 'admin.sistem.index',
        'icon' => 'settings',
        'permissions' => ['sistem.view'],
        'items' => [
            ['name' => 'Pengaturan Perusahaan', 'route' => 'admin.sistem.pengaturan.index', 'permissions' => ['sistem.settings.view']],
            ['name' => 'Setting Resolusi', 'route' => 'admin.sistem.resolusi.index', 'permissions' => []],
            ['name' => 'User Management', 'route' => 'admin.users.index', 'permissions' => ['users.view']],
            ['name' => 'Role & Permission', 'route' => 'admin.roles.index', 'permissions' => ['roles.view']],
        ]
    ],
];
