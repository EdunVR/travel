<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PembelianDetailController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProduksiDetailController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TipeController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\InventoriController;
use App\Http\Controllers\SaudiTransportController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageCatalogController;
use App\Http\Controllers\KeberangkatanController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\DesignMaterialController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\KontraBonController;
use App\Http\Controllers\PermintaanPengirimanController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ProspekController;
use App\Http\Controllers\ProspekSettingController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\InvestorAccountController;
use App\Http\Controllers\ProfitDistributionController;
use App\Http\Controllers\InvestorDocumentController;
use App\Http\Controllers\AccountInvestmentController;
use App\Http\Controllers\ProfitManagementController;
use App\Http\Controllers\InvestorCustomerController;
use App\Models\Prospek;
use App\Http\Controllers\Auth\InvestorLoginController;
use App\Http\Controllers\InvestorDashboardController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\AnnualTaxReportController;
use App\Http\Controllers\WorksheetController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\EquityChangeController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\RabTemplateController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\JemaahController;
use App\Http\Controllers\InvestorAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WithdrawalManagementController;
use App\Http\Controllers\AgenGerobakController;
use App\Http\Controllers\GerobakController;
use App\Http\Controllers\AgenLaporanController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\SalesManagementController;
use App\Http\Controllers\SalesCustomerPriceController;
use App\Http\Controllers\SalesOngkirController;
use App\Http\Controllers\PoPenjualanController;
use App\Http\Controllers\SettingCOAController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\TransferGudangController;
use App\Http\Controllers\DashboardInventarisController;
use App\Http\Controllers\PurchaseManagementController;
use App\Http\Controllers\FinanceAccountantController;
use App\Http\Controllers\CompanyBankAccountController;
use App\Http\Controllers\SistemController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\MarginReportController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\CrmDashboardController;
use App\Http\Controllers\RecruitmentManagementController;
use App\Http\Controllers\PayrollManagementController;
use App\Http\Controllers\PayrollCoaSettingController;
use App\Http\Controllers\AttendanceManagementController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\PreOrderPrintController;
use App\Http\Controllers\PreOrderSettingController;
use App\Http\Controllers\AffiliateController;
use App\Services\AffiliateTrackingService;

// Root URL - tampilkan homepage langsung dengan affiliate tracking
Route::get('/', function () {
    $outlets = \App\Models\Outlet::active()->orderBy('nama_outlet')->get(['id_outlet', 'nama_outlet', 'kota']);
    $packageTypes = \App\Models\TravelPackage::active()
        ->select('package_type')
        ->distinct()
        ->orderBy('package_type')
        ->pluck('package_type');

    // Paket katalog untuk ditampilkan di homepage (6 dengan keberangkatan terdekat)
    $featuredPackages = \App\Models\TravelPackage::active()
        ->with(['outlet', 'flightDeparture', 'hotelMakkah', 'hotelMadinah'])
        ->where('departure_date', '>=', now())
        ->orderBy('departure_date', 'asc')
        ->limit(6)
        ->get();

    // Handle search dari hero search bar
    $searchResults = null;
    if (request()->hasAny(['outlet_id', 'bulan', 'package_type'])) {
        $query = \App\Models\TravelPackage::active()->with(['outlet', 'flightDeparture', 'hotelMakkah', 'hotelMadinah']);

        if (request('outlet_id')) {
            $query->where('id_outlet', request('outlet_id'));
        }
        if (request('bulan')) {
            $query->whereMonth('departure_date', request('bulan'));
        }
        if (request('package_type')) {
            $query->where('package_type', request('package_type'));
        }

        $searchResults = $query->orderBy('departure_date')->get();
    }

    // ===== AFFILIATE REFERRAL DETECTION =====
    $affiliator = null;
    
    // Check for referral parameter in URL
    if (request()->has('ref')) {
        $username = request('ref');
        
        // Load affiliator data
        $affiliator = \App\Models\Affiliator::where('username', $username)
            ->where('status', 'active')
            ->first();
        
        // Set cookie with custom lifetime if affiliator found
        if ($affiliator) {
            $minutes = ($affiliator->cookie_lifetime ?? 30) * 24 * 60; // Convert days to minutes
            cookie()->queue('affiliate_ref', $affiliator->username, $minutes);
        }
    }
    // Check for existing cookie if no ref parameter
    elseif (request()->cookie('affiliate_ref')) {
        $username = request()->cookie('affiliate_ref');
        
        $affiliator = \App\Models\Affiliator::where('username', $username)
            ->where('status', 'active')
            ->first();
    }

    return view('homepage', compact('outlets', 'packageTypes', 'featuredPackages', 'searchResults', 'affiliator'));
})->middleware(['web', 'affiliate.tracking'])->name('homepage');

// ===== PUBLIC PACKAGE DETAIL ROUTE =====
Route::get('/paket/{id}', [App\Http\Controllers\PublicPackageController::class, 'show'])
    ->where('id', '[0-9]+')
    ->middleware(['web', 'affiliate.tracking'])
    ->name('public.paket.show');

// ===== PUBLIC ALL PACKAGES PAGE =====
Route::get('/paket', [App\Http\Controllers\PublicPackageController::class, 'index'])
    ->middleware(['web', 'affiliate.tracking'])
    ->name('public.paket.index');

// ===== PUBLIC INFORMATION PAGES =====
Route::get('/sejarah', [App\Http\Controllers\PublicDocumentController::class, 'sejarah'])
    ->middleware(['web'])
    ->name('public.sejarah');

Route::get('/legalitas', [App\Http\Controllers\PublicDocumentController::class, 'legalitas'])
    ->middleware(['web'])
    ->name('public.legalitas');

Route::get('/hotel-partner', [App\Http\Controllers\PublicDocumentController::class, 'hotelPartner'])
    ->middleware(['web'])
    ->name('public.hotel-partner');

// ===== AFFILIATE ROUTES =====
// Pendaftaran Affiliator
Route::get('/affiliate/register', [AffiliateController::class, 'register'])->name('affiliate.register');
Route::post('/affiliate/register', [AffiliateController::class, 'storeRegistration'])->name('affiliate.register.store');

// Halaman Pembayaran Affiliator
Route::get('/affiliate/payment/{token}', [AffiliateController::class, 'showPaymentPage'])->name('affiliate.payment');
Route::post('/affiliate/payment/{token}', [AffiliateController::class, 'processPayment'])->name('affiliate.payment.process');

// QRIS Payment untuk Affiliator
Route::post('/affiliate/payment/{token}/qris/generate', [\App\Http\Controllers\QrisPaymentController::class, 'affiliateGenerateQris'])->name('affiliate.payment.qris.generate');
Route::get('/affiliate/qris/{trxNumber}/check-status', [\App\Http\Controllers\QrisPaymentController::class, 'affiliateCheckStatus'])->name('affiliate.qris.check-status');

// Login Affiliator
Route::get('/affiliate/login', [AffiliateController::class, 'login'])->name('affiliate.login');
Route::post('/affiliate/login', [AffiliateController::class, 'processLogin'])->name('affiliate.login.process');

// Forgot Password Affiliator
Route::get('/affiliate/forgot-password', [AffiliateController::class, 'forgotPassword'])->name('affiliate.forgot-password');
Route::post('/affiliate/forgot-password', [AffiliateController::class, 'sendResetLink'])->name('affiliate.forgot-password.send');
Route::get('/affiliate/reset-password', [AffiliateController::class, 'resetPassword'])->name('affiliate.reset-password');
Route::post('/affiliate/reset-password', [AffiliateController::class, 'updatePassword'])->name('affiliate.reset-password.update');

// Dashboard Affiliator (protected)
Route::middleware(['web'])->group(function () {
    Route::get('/affiliate/dashboard', [AffiliateController::class, 'dashboard'])->name('affiliate.dashboard');
    Route::get('/affiliate/referrals', [AffiliateController::class, 'referrals'])->name('affiliate.referrals');
    Route::get('/affiliate/payments', [AffiliateController::class, 'payments'])->name('affiliate.payments');
    Route::get('/affiliate/wallet', [AffiliateController::class, 'wallet'])->name('affiliate.wallet');
    Route::get('/affiliate/profile', [AffiliateController::class, 'profile'])->name('affiliate.profile');
    Route::post('/affiliate/profile', [AffiliateController::class, 'updateProfile'])->name('affiliate.profile.update');
    Route::get('/affiliate/marketing', [AffiliateController::class, 'marketing'])->name('affiliate.marketing');
    Route::get('/affiliate/reports', [AffiliateController::class, 'reports'])->name('affiliate.reports');
    Route::get('/affiliate/leaderboard', [AffiliateController::class, 'leaderboard'])->name('affiliate.leaderboard');
    Route::post('/affiliate/payout/request', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout.request');
    Route::get('/affiliate/hierarchy', [AffiliateController::class, 'hierarchy'])->name('affiliate.hierarchy');
    Route::get('/affiliate/logout', [AffiliateController::class, 'logout'])->name('affiliate.logout');
});

// ===== VOUCHER API ROUTES (accessible by both admin and affiliates) =====
Route::prefix('admin/affiliate/vouchers')->group(function () {
    Route::get('/list', [\App\Http\Controllers\AdminVoucherController::class, 'list']);
    Route::get('/generate-code', [\App\Http\Controllers\AdminVoucherController::class, 'generateCode']);
    Route::post('/', [\App\Http\Controllers\AdminVoucherController::class, 'store']);
    Route::put('/{id}', [\App\Http\Controllers\AdminVoucherController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\AdminVoucherController::class, 'destroy']);
});

// Submit pemesanan paket publik
Route::post('/paket/{id}/pesan', [\App\Http\Controllers\PublicPackageController::class, 'order'])
    ->middleware(['web'])
    ->name('public.paket.order');

// Invoice booking publik
Route::get('/paket/{packageId}/invoice/{bookingId}', [\App\Http\Controllers\PublicPackageController::class, 'invoice'])
    ->middleware(['web'])
    ->name('public.paket.invoice');

// Submit pembayaran dari invoice booking
Route::post('/paket/{packageId}/invoice/{bookingId}/bayar', [\App\Http\Controllers\PublicPackageController::class, 'pay'])
    ->middleware(['web'])
    ->name('public.paket.pay');

// Payment pending verification page (Task 9)
Route::get('/paket/{packageId}/booking/{bookingId}/pending', 
    [\App\Http\Controllers\PublicPackageController::class, 'paymentPending'])
    ->middleware(['web'])
    ->name('public.payment.pending');

// Booking fully paid page
Route::get('/paket/{packageId}/booking/{bookingId}/paid', 
    [\App\Http\Controllers\PublicPackageController::class, 'bookingPaid'])
    ->middleware(['web'])
    ->name('public.booking.paid');

// Check payment status API (Task 9)
Route::get('/payment/{paymentId}/check-status', 
    [\App\Http\Controllers\PublicPackageController::class, 'checkPaymentStatus'])
    ->middleware(['web'])
    ->name('public.payment.check-status');

// Public manifest form (Task 10)
Route::get('/booking/{bookingId}/manifest', 
    [\App\Http\Controllers\PublicDocumentController::class, 'manifestForm'])
    ->middleware(['web'])
    ->name('public.booking.manifest');

Route::post('/booking/{bookingId}/manifest', 
    [\App\Http\Controllers\PublicDocumentController::class, 'submitManifest'])
    ->middleware(['web'])
    ->name('public.booking.manifest.submit');

// OCR Passport for public manifest form (Task 14)
Route::post('/manifest/ocr-passport', 
    [\App\Http\Controllers\PublicDocumentController::class, 'ocrPassport'])
    ->middleware(['web'])
    ->name('public.manifest.ocr-passport');

// OCR KTP for public manifest form
Route::post('/manifest/ocr-ktp', 
    [\App\Http\Controllers\PublicDocumentController::class, 'ocrKtp'])
    ->middleware(['web'])
    ->name('public.manifest.ocr-ktp');

// Save manifest data (text fields only, no file upload) — AJAX endpoint
Route::post('/booking/{bookingId}/manifest/save-data',
    [\App\Http\Controllers\PublicDocumentController::class, 'saveManifestData'])
    ->middleware(['web'])
    ->name('public.booking.manifest.save-data');

// Upload dokumen manifest per-file — AJAX endpoint
Route::post('/booking/{bookingId}/manifest/upload-doc',
    [\App\Http\Controllers\PublicDocumentController::class, 'uploadManifestDocument'])
    ->middleware(['web'])
    ->name('public.booking.manifest.upload-doc');

// Hapus dokumen manifest per-file — AJAX endpoint
Route::delete('/booking/{bookingId}/manifest/delete-doc',
    [\App\Http\Controllers\PublicDocumentController::class, 'deleteManifestDocument'])
    ->middleware(['web'])
    ->name('public.booking.manifest.delete-doc');

// Add family member to booking (Task 10)
Route::post('/booking/{bookingId}/add-family-member', 
    [\App\Http\Controllers\PublicPackageController::class, 'addFamilyMember'])
    ->middleware(['web'])
    ->name('public.booking.add-family-member');

// NEW: Public Booking WhatsApp Flow Routes
Route::post('/booking/submit', [\App\Http\Controllers\PublicPackageController::class, 'submitBooking'])
    ->middleware(['web'])
    ->name('public.booking.submit');

Route::get('/booking/payment/{token}', [\App\Http\Controllers\PublicPackageController::class, 'showPaymentPage'])
    ->middleware(['web'])
    ->name('public.booking.payment');

Route::post('/booking/payment/{token}', [\App\Http\Controllers\PublicPackageController::class, 'processPayment'])
    ->middleware(['web'])
    ->name('public.booking.payment.process');

// QRIS Payment Routes (InterActive QRIS)
Route::post('/paket/{packageId}/invoice/{bookingId}/qris/generate', 
    [\App\Http\Controllers\QrisPaymentController::class, 'generateQris'])
    ->middleware(['web'])
    ->name('public.qris.generate');

Route::get('/qris/{trxNumber}/check-status', 
    [\App\Http\Controllers\QrisPaymentController::class, 'checkStatus'])
    ->middleware(['web'])
    ->name('public.qris.check-status');

// Voucher routes
Route::post('/voucher/validate', [\App\Http\Controllers\VoucherController::class, 'validateVoucher'])
    ->middleware(['web'])
    ->name('voucher.validate');

// AJAX: Get products for equipment selection
Route::get('/api/products', [\App\Http\Controllers\PublicPackageController::class, 'getProducts'])
    ->middleware(['web'])
    ->name('api.products');

// Serve semua file statis dari WEB_HMTour (gambar, logo, dll)
Route::get('/WEB_HMTour/{path}', function ($path) {
    $fullPath = base_path('WEB_HMTour/' . $path);
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }
    // Hanya izinkan file gambar dan font
    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp','svg','ico','woff','woff2','ttf','eot'];
    if (!in_array($ext, $allowed)) {
        abort(403);
    }
    $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->middleware(['web']);

// Health check endpoint to prevent 405 errors
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
        'app' => config('app.name'),
        'version' => '1.0.0'
    ]);
})->middleware('web');

// Handle any other methods on root URL
Route::match(['POST', 'PUT', 'PATCH', 'DELETE'], '/', function () {
    Log::warning('❌ [ROOT ACCESS] Invalid method attempted on root URL', [
        'method' => request()->method(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent()
    ]);
    
    return response()->json([
        'error' => 'Method not allowed',
        'message' => 'Only GET method is allowed for root URL',
        'allowed_methods' => ['GET'],
        'redirect_url' => route('login')
    ], 405)->header('Allow', 'GET');
})->middleware('web')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Handle /admin URL - this is the main entry point that maintains consistent URL structure
Route::get('/admin', function () {
    try {
        // Log the request for debugging
        Log::info('🔐 [ADMIN ACCESS] Admin URL accessed', [
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString()
        ]);

        // Check if user is authenticated
        if (Auth::check()) {
            Log::info('✅ [ADMIN ACCESS] Authenticated user, showing dashboard', [
                'user_id' => Auth::id()
            ]);
            // Return the admin dashboard view directly to maintain /admin URL
            return app(\App\Http\Controllers\AdminDashboardController::class)->index();
        }

        // Redirect to login for unauthenticated users
        Log::info('🔄 [ADMIN ACCESS] Unauthenticated user, redirecting to login');
        return redirect()->route('login');
        
    } catch (\Exception $e) {
        Log::error('💥 [ADMIN ACCESS] Error in admin access', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Fallback to login page
        return redirect()->route('login');
    }
})->middleware(['web']);

// Removed catch-all route from here - moved to end of file after all other routes

// Apply admin URL redirect middleware to web routes for authenticated users
Route::middleware(['web', 'admin.url.redirect'])->group(function () {
    // This will catch any non-admin routes and redirect authenticated users to admin
});

// ================== PUBLIC DOCUMENTS (no auth required) ==================
Route::prefix('doc')->name('public.')->middleware(['web'])->group(function () {
    // Invoice PDF — QR code dari invoice mengarah ke sini
    Route::get('invoice/{bookingId}/{token}', [\App\Http\Controllers\PublicDocumentController::class, 'invoice'])
        ->name('invoice');
    // Kwitansi PDF — QR code dari kwitansi mengarah ke sini
    Route::get('receipt/{paymentId}/{token}', [\App\Http\Controllers\PublicDocumentController::class, 'receipt'])
        ->name('receipt');
});

// ================== ADMIN ==================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Dashboard - this handles /admin/ (with trailing slash) - redirect to main admin
    Route::get('/', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API Endpoints
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/overview', [App\Http\Controllers\AdminDashboardController::class, 'getOverviewStats'])->name('overview');
        Route::get('/sales-trend', [App\Http\Controllers\AdminDashboardController::class, 'getSalesTrend'])->name('sales-trend');
        Route::get('/inventory-status', [App\Http\Controllers\AdminDashboardController::class, 'getInventoryStatus'])->name('inventory-status');
        Route::get('/production-efficiency', [App\Http\Controllers\AdminDashboardController::class, 'getProductionEfficiency'])->name('production-efficiency');
        Route::get('/employee-performance', [App\Http\Controllers\AdminDashboardController::class, 'getEmployeePerformance'])->name('employee-performance');
        Route::get('/insights', [App\Http\Controllers\AdminDashboardController::class, 'getInsights'])->name('insights');
    });

    // ===== Alias lama (modul utama) agar menu lama tetap hidup =====
    Route::view('/inventaris', 'admin.inventaris.index')->name('inventaris'); // <— ALIAS penting
    Route::view('/investor',   'admin.investor.index')->name('investor');
    Route::view('/pelanggan',  'admin.pelanggan.index')->name('pelanggan');
    Route::view('/pos',        'admin.pos.index')->name('pos');
    Route::view('/keuangan',   'admin.keuangan.index')->name('keuangan');
    Route::view('/keuangan/aktiva',   'admin.keuangan.aktiva.index')->name('keuangan.aktiva.index');
    Route::view('/keuangan/arus-kas',   'admin.keuangan.arus-kas.index')->name('keuangan.arus-kas.index');
    Route::view('/keuangan/buku',   'admin.keuangan.buku.index')->name('keuangan.buku.index');
    Route::view('/keuangan/buku/akun',   'admin.keuangan.buku.akun')->name('keuangan.buku.akun');
    Route::view('/keuangan/bukubesar',   'admin.keuangan.bukubesar.index')->name('keuangan.bukubesar.index');
    Route::view('/keuangan/jurnal',   'admin.keuangan.jurnal.index')->name('keuangan.jurnal.index');
    Route::view('/keuangan/labarugi',   'admin.keuangan.labarugi.index')->name('keuangan.labarugi.index');
    Route::view('/keuangan/neraca',   'admin.keuangan.neraca.index')->name('keuangan.neraca.index');
    Route::view('/keuangan/perubahan_modal',   'admin.keuangan.perubahan_modal.index')->name('keuangan.perubahan_modal.index');
    Route::view('/keuangan/rab',   'admin.keuangan.rab.index')->name('keuangan.rab.index');
    Route::view('/keuangan/spt',   'admin.keuangan.spt.index')->name('keuangan.spt.index');
    Route::get('/sdm', [App\Http\Controllers\SdmDashboardController::class, 'index'])->name('sdm');
    Route::get('/sdm/dashboard/data', [App\Http\Controllers\SdmDashboardController::class, 'getData'])->name('sdm.dashboard.data');
    Route::view('/penjualan',  'admin.penjualan.index')->name('penjualan.index');
    Route::view('/penjualan/invoice',  'admin.penjualan.invoice.index')->name('penjualan.invoice.index');
    Route::view('/pembelian/supplier',  'admin.pembelian.supplier.index')->name('pembelian.supplier.index');
    // Route produksi.produksi.index akan dihandle oleh controller di bawah
    Route::view('/rantai-pasok','admin.rantai-pasok.index')->name('rantai-pasok');
    Route::get('/service', [ServiceController::class, 'index'])->name('service');
    Route::get('/service/data', [ServiceController::class, 'getData'])->name('service.data');
    Route::view('/analisis',   'admin.analisis.index')->name('analisis');
    
    // ====== PRODUKSI ADMIN ROUTES ======
    Route::prefix('produksi')->name('produksi.produksi.')->group(function () {
        Route::get('/produksi', [ProductionController::class, 'index'])->name('index');
        Route::get('/produksi/data', [ProductionController::class, 'getData'])->name('data');
        Route::get('/produksi/statistics', [ProductionController::class, 'getStatistics'])->name('statistics');
        Route::get('/produksi/products', [ProductionController::class, 'getProducts'])->name('products');
        Route::get('/produksi/materials', [ProductionController::class, 'getMaterials'])->name('materials');
        Route::get('/produksi/materials/{id}/fifo', [ProductionController::class, 'getMaterialFifo'])->name('materials.fifo');
        Route::get('/produksi/attendance/count', [ProductionController::class, 'getAttendanceCount'])->name('attendance.count');
        
        // Monthly Production Costs Routes - MUST BE BEFORE {id} routes
        Route::get('/produksi/monthly-costs', [ProductionController::class, 'getMonthlyCosts'])->name('monthly-costs.get');
        Route::post('/produksi/monthly-costs', [ProductionController::class, 'storeMonthlyCost'])->name('monthly-costs.store');
        Route::delete('/produksi/monthly-costs/{id}', [ProductionController::class, 'deleteMonthlyCost'])->name('monthly-costs.delete');
        
        // Monthly Production Costs Data Route
        Route::get('/monthly-production-costs/data', [App\Http\Controllers\MonthlyProductionCostController::class, 'data'])->name('monthly-production-costs.data');
        Route::post('/monthly-production-costs/store', [App\Http\Controllers\MonthlyProductionCostController::class, 'store'])->name('monthly-production-costs.store');
        
        Route::get('/produksi/export/pdf', [ProductionController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/produksi/export/excel', [ProductionController::class, 'exportExcel'])->name('export.excel');
        Route::get('/produksi/export/bulk-production-pdf', [ProductionController::class, 'exportBulkProductionPdf'])->name('export.bulk-production-pdf');
        Route::get('/produksi/export/bulk-qc-tofu-pdf', [ProductionController::class, 'exportBulkQcTofuPdf'])->name('export.bulk-qc-tofu-pdf');
        Route::get('/produksi/export/qc-tofu-mentah-pdf', [ProductionController::class, 'exportQcTofuMentahPdf'])->name('export.qc-tofu-mentah-pdf');
        
        Route::post('/produksi/hpp/preview', [ProductionController::class, 'calculateHppPreview'])->name('hpp.preview');
        Route::post('/produksi', [ProductionController::class, 'store'])->name('store');
        
        // Routes with {id} parameter - MUST BE AFTER specific routes
        Route::get('/produksi/{id}', [ProductionController::class, 'show'])->name('show');
        Route::get('/produksi/{id}/edit', [ProductionController::class, 'edit'])->name('edit');
        Route::put('/produksi/{id}', [ProductionController::class, 'update'])->name('update');
        Route::delete('/produksi/{id}', [ProductionController::class, 'destroy'])->name('destroy');
        Route::post('/produksi/{id}/approve', [ProductionController::class, 'approve'])->name('approve');
        Route::post('/produksi/{id}/start', [ProductionController::class, 'start'])->name('start');
        Route::post('/produksi/{id}/complete', [ProductionController::class, 'complete'])->name('complete');
        Route::post('/produksi/{id}/cancel', [ProductionController::class, 'cancel'])->name('cancel');
        Route::post('/produksi/{id}/realization', [ProductionController::class, 'addRealization'])->name('realization');
        Route::get('/produksi/{id}/pdf', [ProductionController::class, 'generatePdf'])->name('pdf');
        Route::get('/produksi/{id}/qc-tofu-pdf', [ProductionController::class, 'generateQcTofuPdf'])->name('qc-tofu-pdf');
        
        // Temporary test route to debug the issue
        Route::get('/produksi/{id}/test-pdf', function($id) {
            Log::info('🧪 [TEST] Testing PDF generation route', ['id' => $id]);
            
            $controller = new \App\Http\Controllers\ProductionController();
            
            if (method_exists($controller, 'generatePdf')) {
                Log::info('✅ [TEST] Method exists, calling it');
                return $controller->generatePdf($id);
            } else {
                Log::error('❌ [TEST] Method does not exist');
                return response()->json(['error' => 'Method not found'], 404);
            }
        })->name('test-pdf');
    });
    
    // ====== SUPPLY CHAIN MANAGEMENT ======
    Route::prefix('supply-chain')->name('supply-chain.')->group(function () {
        // Permintaan Barang Routes
        Route::prefix('permintaan-barang')->name('permintaan-barang.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PermintaanBarangController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\PermintaanBarangController::class, 'getData'])->name('data');
            Route::get('/stats', [\App\Http\Controllers\PermintaanBarangController::class, 'getStats'])->name('stats');
            Route::get('/create', [\App\Http\Controllers\PermintaanBarangController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PermintaanBarangController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\PermintaanBarangController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\PermintaanBarangController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\PermintaanBarangController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\PermintaanBarangController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/approve', [\App\Http\Controllers\PermintaanBarangController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\PermintaanBarangController::class, 'reject'])->name('reject');
            Route::get('/{id}/pdf', [\App\Http\Controllers\PermintaanBarangController::class, 'generatePdf'])->name('pdf');
            
            // Helper routes
            Route::get('/search/products', [\App\Http\Controllers\PermintaanBarangController::class, 'searchProducts'])->name('search.products');
            Route::get('/search/materials', [\App\Http\Controllers\PermintaanBarangController::class, 'searchMaterials'])->name('search.materials');
            Route::get('/outlets/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getOutlets'])->name('outlets');
            Route::get('/suppliers/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getSuppliers'])->name('suppliers');
            Route::get('/books/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getBooks'])->name('books');
            Route::get('/asset-accounts/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getAssetAccounts'])->name('asset-accounts');
            Route::get('/expense-accounts/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getExpenseAccounts'])->name('expense-accounts');
            Route::get('/accumulated-depreciation-accounts/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getAccumulatedDepreciationAccounts'])->name('accumulated-depreciation-accounts');
            Route::get('/payment-accounts/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getPaymentAccounts'])->name('payment-accounts');
            Route::get('/journal-accounts/list', [\App\Http\Controllers\PermintaanBarangController::class, 'getJournalAccounts'])->name('journal-accounts');
        });
    });
    
    // ====== SISTEM & PENGATURAN ======
    Route::prefix('sistem')->name('sistem.')->group(function () {
        Route::get('/', [SistemController::class, 'index'])->name('index');
        Route::get('system-info', [SistemController::class, 'getSystemInfo'])->name('system-info');
        Route::post('clear-cache', [SistemController::class, 'clearCache'])->name('clear-cache');
        Route::post('run-migration', [SistemController::class, 'runMigration'])->name('run-migration');
        Route::post('optimize-database', [SistemController::class, 'optimizeDatabase'])->name('optimize-database');
        Route::post('create-backup', [SistemController::class, 'createBackup'])->name('create-backup');
        Route::get('backups', [SistemController::class, 'getBackups'])->name('backups');
        Route::get('backups/{filename}/download', [SistemController::class, 'downloadBackup'])->name('backups.download');
        Route::delete('backups/{filename}', [SistemController::class, 'deleteBackup'])->name('backups.delete');
        Route::post('restore-backup', [SistemController::class, 'restoreBackup'])->name('restore-backup');
        Route::get('app-settings', [SistemController::class, 'getAppSettings'])->name('app-settings');
        
        // Pengaturan Perusahaan
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::get('/', [CompanySettingController::class, 'index'])->name('index');
            Route::get('edit', [CompanySettingController::class, 'edit'])->name('edit');
            Route::put('update', [CompanySettingController::class, 'update'])->name('update');
            Route::post('remove-file', [CompanySettingController::class, 'removeFile'])->name('remove-file');
            Route::get('settings', [CompanySettingController::class, 'getSettings'])->name('settings');
            Route::post('reset', [CompanySettingController::class, 'reset'])->name('reset');
        });
    });
    
    // ====== INVENTARIS (rute baru yang rapi) ======
    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        //Dashboard
        Route::get('/', [DashboardInventarisController::class, 'index'])->name('index');
        Route::get('stats', [DashboardInventarisController::class, 'getStats'])->name('stats');
        Route::get('outlets-summary', [DashboardInventarisController::class, 'getOutletsSummary'])->name('outlets-summary');
        Route::get('low-stock-items', [DashboardInventarisController::class, 'getLowStockItems'])->name('low-stock-items');
        Route::get('recent-activities', [DashboardInventarisController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('search', [DashboardInventarisController::class, 'search'])->name('search');

        //Outlet
        Route::get('outlet-data', [OutletController::class, 'data'])->name('outlet.data');
        Route::get('outlet/export/pdf', [OutletController::class, 'exportPdf'])->name('outlet.export.pdf');
        Route::get('outlet/export/excel', [OutletController::class, 'exportExcel'])->name('outlet.export.excel');
        Route::post('outlet/import/excel', [OutletController::class, 'importExcel'])->name('outlet.import.excel');
        Route::get('outlet/download-template', [OutletController::class, 'downloadTemplate'])->name('outlet.download-template');
        Route::get('outlet/generate-kode', [OutletController::class, 'getNewKode'])->name('outlet.generate-kode');
        Route::get('outlet/cities', [OutletController::class, 'getCities'])->name('outlet.cities');
        Route::resource('outlet', OutletController::class);

        // Airline
        Route::get('airline/data', [\App\Http\Controllers\AirlineController::class, 'getData'])->name('airline.data');
        Route::get('airline/list', [\App\Http\Controllers\AirlineController::class, 'list'])->name('airline.list');
        Route::resource('airline', \App\Http\Controllers\AirlineController::class)->except(['create', 'edit']);

        // Airport
        Route::get('airport/data', [\App\Http\Controllers\AirportController::class, 'getData'])->name('airport.data');
        Route::get('airport/list', [\App\Http\Controllers\AirportController::class, 'list'])->name('airport.list');
        Route::resource('airport', \App\Http\Controllers\AirportController::class)->except(['create', 'edit']);

        // Flight
        Route::get('flight-data', [FlightController::class, 'getData'])->name('flight.data');
        Route::get('flight/airlines', [FlightController::class, 'getAirlines'])->name('flight.airlines');
        Route::resource('flight', FlightController::class);
        
        // Flight Groups
        Route::get('flight-group/data', [\App\Http\Controllers\FlightGroupController::class, 'getData'])->name('flight-group.data');
        Route::resource('flight-group', \App\Http\Controllers\FlightGroupController::class)->except(['create', 'edit']);
        
        // Hotel
        Route::get('hotel-data', [HotelController::class, 'getData'])->name('hotel.data');
        Route::get('hotel/user-outlets', [HotelController::class, 'getUserOutlets'])->name('hotel.user-outlets');
        Route::get('hotel/cities', [HotelController::class, 'getCities'])->name('hotel.cities');
        Route::get('hotel/{hotel}/room-types', [HotelController::class, 'getRoomTypes'])->name('hotel.room-types');
        Route::post('hotel/{hotel}/room-types', [HotelController::class, 'storeRoomType'])->name('hotel.room-types.store');
        Route::put('hotel/{hotel}/room-types/{roomType}', [HotelController::class, 'updateRoomType'])->name('hotel.room-types.update');
        Route::delete('hotel/{hotel}/room-types/{roomType}', [HotelController::class, 'destroyRoomType'])->name('hotel.room-types.destroy');
        Route::resource('hotel', HotelController::class);
        
        // Transportasi Saudi (Kereta Cepat / Bus)
        Route::get('transport/data', [SaudiTransportController::class, 'getData'])->name('transport.data');
        Route::resource('transport', SaudiTransportController::class);
        
        // Travel Package
        Route::get('travel/package', [PackageController::class, 'index'])->name('travel.package.index');
        Route::get('travel/package/data', [PackageController::class, 'getData'])->name('travel.package.data');
        Route::get('travel/package/{id}/detail', [PackageController::class, 'showPage'])->name('travel.package.detail');
        Route::get('travel/package/{id}/export-pdf', [PackageController::class, 'exportPdf'])->name('travel.package.export.pdf');
        Route::get('travel/package/{id}/hpp', [PackageController::class, 'getHpp'])->name('travel.package.hpp');
        Route::put('travel/package/{id}/hpp', [PackageController::class, 'updateHpp'])->name('travel.package.hpp.update');
        Route::post('travel/package/{id}/hpp/lock', [PackageController::class, 'lockHpp'])->name('travel.package.hpp.lock');
        Route::get('travel/package/hpp/flights', [PackageController::class, 'getFlights'])->name('travel.package.hpp.flights');
        Route::get('travel/package/hpp/hotels', [PackageController::class, 'getHotels'])->name('travel.package.hpp.hotels');
        Route::get('travel/package/hpp/saudi-transports', [PackageController::class, 'getSaudiTransports'])->name('travel.package.hpp.saudi-transports');
        Route::get('travel/package/flight-groups', [PackageController::class, 'getFlightGroups'])->name('travel.package.flight-groups');
        Route::get('travel/package/{id}/tour-plans', [PackageController::class, 'getTourPlans'])->name('travel.package.tour-plans.get');
        Route::post('travel/package/{id}/tour-plans', [PackageController::class, 'saveTourPlans'])->name('travel.package.tour-plans.save');
        Route::get('travel/package/types', [PackageController::class, 'getPackageTypes'])->name('travel.package.types');
        Route::get('travel/package/stages', [PackageController::class, 'getWorkflowStages'])->name('travel.package.stages');
        Route::get('travel/package/{id}/workflow/progress', [PackageController::class, 'getWorkflowProgress'])->name('travel.package.workflow.progress');
        Route::post('travel/package/{id}/workflow/transition', [PackageController::class, 'transitionWorkflow'])->name('travel.package.workflow.transition');
        Route::get('travel/package/{id}/workflow/tasks', [PackageController::class, 'getWorkflowTasks'])->name('travel.package.workflow.tasks');
        Route::put('travel/package/{id}/handling-fee', [PackageController::class, 'updateHandlingFee'])->name('travel.package.handling-fee.update');
        Route::resource('travel/package', PackageController::class)->except(['index'])->names([
            'create' => 'travel.package.create',
            'store' => 'travel.package.store',
            'show' => 'travel.package.show',
            'edit' => 'travel.package.edit',
            'update' => 'travel.package.update',
            'destroy' => 'travel.package.destroy'
        ]);
        
        // Package Catalog (Public)
        Route::get('travel/catalog', [PackageCatalogController::class, 'index'])->name('travel.catalog.index');
        Route::get('travel/catalog/{id}', [PackageCatalogController::class, 'show'])->name('travel.catalog.show');
        Route::get('travel/catalog/data/packages', [PackageCatalogController::class, 'getData'])->name('travel.catalog.data');
        
        // Design Materials
        Route::get('travel/package/{packageId}/materials', [DesignMaterialController::class, 'index'])->name('travel.design-materials.index');
        Route::get('travel/package/{packageId}/materials/data', [DesignMaterialController::class, 'getMaterials'])->name('travel.design-materials.data');
        Route::post('travel/package/{packageId}/materials/upload', [DesignMaterialController::class, 'upload'])->name('travel.design-materials.upload');
        Route::put('travel/materials/{id}', [DesignMaterialController::class, 'update'])->name('travel.design-materials.update');
        Route::post('travel/materials/{id}/complete', [DesignMaterialController::class, 'markComplete'])->name('travel.design-materials.complete');
        Route::post('travel/materials/{id}/incomplete', [DesignMaterialController::class, 'markIncomplete'])->name('travel.design-materials.incomplete');
        Route::delete('travel/materials/{id}', [DesignMaterialController::class, 'destroy'])->name('travel.design-materials.destroy');
        Route::get('travel/materials/{id}/download', [DesignMaterialController::class, 'download'])->name('travel.design-materials.download');
        
        // Keberangkatan
        Route::get('travel/keberangkatan/data', [KeberangkatanController::class, 'getData'])->name('travel.keberangkatan.data');
        Route::get('travel/keberangkatan/packages', [KeberangkatanController::class, 'getAvailablePackages'])->name('travel.keberangkatan.packages');
        Route::post('travel/keberangkatan/generate-code', [KeberangkatanController::class, 'generateCode'])->name('travel.keberangkatan.generate-code');
        Route::get('travel/keberangkatan/{id}/capacity', [KeberangkatanController::class, 'getCapacityTracking'])->name('travel.keberangkatan.capacity');
        Route::post('travel/keberangkatan/{id}/create-rab', [KeberangkatanController::class, 'createRab'])->name('travel.keberangkatan.create-rab');
        Route::get('travel/keberangkatan/{id}/rab-data', [KeberangkatanController::class, 'getRabData'])->name('travel.keberangkatan.rab-data');
        Route::get('travel/keberangkatan/{id}/budget-variance', [KeberangkatanController::class, 'getBudgetVariance'])->name('travel.keberangkatan.budget-variance');
        Route::post('travel/keberangkatan/{id}/rab-realisasi', [KeberangkatanController::class, 'updateRabRealisasi'])->name('travel.keberangkatan.rab-realisasi');
        Route::get('travel/keberangkatan/{id}/rab-modal', [KeberangkatanController::class, 'getKeberangkatanRabModal'])->name('travel.keberangkatan.rab-modal');
        Route::post('travel/keberangkatan/{id}/rab-modal-update', [KeberangkatanController::class, 'updateKeberangkatanRabItem'])->name('travel.keberangkatan.rab-modal-update');
        Route::post('travel/keberangkatan/{id}/sesuaikan-laporan', [KeberangkatanController::class, 'sesuaikanLaporan'])->name('travel.keberangkatan.sesuaikan-laporan');
        Route::post('travel/keberangkatan/{id}/reset-penyesuaian', [KeberangkatanController::class, 'resetPenyesuaianLaporan'])->name('travel.keberangkatan.reset-penyesuaian');
        Route::get('travel/keberangkatan/{id}/financial-summary', [KeberangkatanController::class, 'getFinancialSummary'])->name('travel.keberangkatan.financial-summary');
        Route::get('travel/keberangkatan/{id}/available-jamaah', [KeberangkatanController::class, 'getAvailableJamaah'])->name('travel.keberangkatan.available-jamaah');
        Route::post('travel/keberangkatan/{id}/add-jamaah', [KeberangkatanController::class, 'addJamaah'])->name('travel.keberangkatan.add-jamaah');
        Route::post('travel/keberangkatan/{id}/remove-jamaah', [KeberangkatanController::class, 'removeJamaah'])->name('travel.keberangkatan.remove-jamaah');
        Route::get('travel/keberangkatan/{id}/total-cost', [KeberangkatanController::class, 'getTotalCost'])->name('travel.keberangkatan.total-cost');
        Route::post('travel/keberangkatan/{id}/auto-assign', [KeberangkatanController::class, 'autoAssignJamaah'])->name('travel.keberangkatan.auto-assign');
        Route::get('travel/keberangkatan/{id}/unassigned-bookings', [KeberangkatanController::class, 'getUnassignedBookings'])->name('travel.keberangkatan.unassigned-bookings');
        Route::resource('travel/keberangkatan', KeberangkatanController::class)->names([
            'index' => 'travel.keberangkatan.index',
            'store' => 'travel.keberangkatan.store',
            'show' => 'travel.keberangkatan.show',
            'update' => 'travel.keberangkatan.update',
            'destroy' => 'travel.keberangkatan.destroy'
        ]);

        // Keberangkatan Visa Management
        Route::get('travel/keberangkatan/{id}/visas', [KeberangkatanController::class, 'getVisas'])->name('travel.keberangkatan.visas.index');
        Route::post('travel/keberangkatan/{id}/visas', [KeberangkatanController::class, 'storeVisa'])->name('travel.keberangkatan.visas.store');
        Route::put('travel/keberangkatan/{id}/visas/{visaId}', [KeberangkatanController::class, 'updateVisa'])->name('travel.keberangkatan.visas.update');
        Route::delete('travel/keberangkatan/{id}/visas/{visaId}', [KeberangkatanController::class, 'destroyVisa'])->name('travel.keberangkatan.visas.destroy');
        Route::post('travel/keberangkatan/{id}/send-reminders', [KeberangkatanController::class, 'sendReminders'])->name('travel.keberangkatan.send-reminders');
        
        // Manifest management (drag & drop + PDF + Excel)
        Route::get('travel/keberangkatan/{id}/manage-manifest', [KeberangkatanController::class, 'manageManifest'])->name('travel.keberangkatan.manage-manifest');
        Route::post('travel/keberangkatan/{id}/save-manifest-order', [KeberangkatanController::class, 'saveManifestOrder'])->name('travel.keberangkatan.save-manifest-order');
        Route::get('travel/keberangkatan/{id}/manifest-pdf', [KeberangkatanController::class, 'manifestPdf'])->name('travel.keberangkatan.manifest-pdf');
        Route::get('travel/keberangkatan/{id}/manifest-excel', [KeberangkatanController::class, 'manifestExcel'])->name('travel.keberangkatan.manifest-excel');
        
        // Task Management
        Route::get('travel/tasks', [TaskController::class, 'index'])->name('travel.tasks.index');
        Route::get('travel/tasks/data', [TaskController::class, 'getData'])->name('travel.tasks.data');
        Route::get('travel/tasks/my-tasks', [TaskController::class, 'myTasks'])->name('travel.tasks.my-tasks');
        Route::post('travel/tasks/{id}/complete', [TaskController::class, 'complete'])->name('travel.tasks.complete');

        // Jamaah Booking
        Route::get('travel/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::get('travel/booking/data', [BookingController::class, 'getData'])->name('booking.getData');
        Route::post('travel/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('travel/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
        Route::get('travel/booking/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
        Route::put('travel/booking/{id}', [BookingController::class, 'update'])->name('booking.update');
        Route::post('travel/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        Route::delete('travel/booking/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
        Route::post('travel/booking/{id}/set-payment-amount', [BookingController::class, 'setPaymentAmount'])->name('booking.setPaymentAmount');
        Route::post('travel/booking/{id}/clear-payment-amount', [BookingController::class, 'clearPaymentAmount'])->name('booking.clearPaymentAmount');
        Route::get('travel/booking/packages/available', [BookingController::class, 'getAvailablePackages'])->name('booking.getAvailablePackages');
        Route::get('travel/booking/keberangkatan/{packageId}', [BookingController::class, 'getAvailableKeberangkatan'])->name('booking.getAvailableKeberangkatan');
        Route::get('travel/booking/jamaah/members', [BookingController::class, 'getJamaahMembers'])->name('booking.getJamaahMembers');

        // Booking Add-ons
        Route::get('travel/booking/{id}/addons', [BookingController::class, 'getAddons'])->name('booking.addons.index');
        Route::get('travel/booking/{id}/addons/create', [BookingController::class, 'createAddon'])->name('booking.addons.create');
        Route::get('travel/booking/{id}/addons/{addonId}/edit', [BookingController::class, 'editAddon'])->name('booking.addons.edit');
        Route::post('travel/booking/{id}/addons', [BookingController::class, 'storeAddon'])->name('booking.addons.store');
        Route::put('travel/booking/{id}/addons/{addonId}', [BookingController::class, 'updateAddon'])->name('booking.addons.update');
        Route::delete('travel/booking/{id}/addons/{addonId}', [BookingController::class, 'destroyAddon'])->name('booking.addons.destroy');

        // Jamaah Hotel Bookings
        Route::get('travel/booking/{id}/hotel-bookings', [BookingController::class, 'getHotelBookings'])->name('booking.hotel-bookings.index');
        Route::get('travel/booking/{id}/hotel-bookings/create', [BookingController::class, 'createHotelBooking'])->name('booking.hotel-bookings.create');
        Route::get('travel/booking/{id}/hotel-bookings/{hbId}/edit', [BookingController::class, 'editHotelBooking'])->name('booking.hotel-bookings.edit');
        Route::post('travel/booking/{id}/hotel-bookings', [BookingController::class, 'storeHotelBooking'])->name('booking.hotel-bookings.store');
        Route::put('travel/booking/{id}/hotel-bookings/{hbId}', [BookingController::class, 'updateHotelBooking'])->name('booking.hotel-bookings.update');
        Route::delete('travel/booking/{id}/hotel-bookings/{hbId}', [BookingController::class, 'destroyHotelBooking'])->name('booking.hotel-bookings.destroy');

        // Flight Booking
        Route::post('travel/flight-booking', [FlightBookingController::class, 'store'])->name('flight-booking.store');
        Route::put('travel/flight-booking/{id}/status', [FlightBookingController::class, 'updateStatus'])->name('flight-booking.updateStatus');
        Route::post('travel/flight-booking/{id}/upload-ticket', [FlightBookingController::class, 'uploadTicket'])->name('flight-booking.uploadTicket');
        Route::get('travel/flight-booking/keberangkatan/{keberangkatanId}', [FlightBookingController::class, 'getByKeberangkatan'])->name('flight-booking.getByKeberangkatan');
        Route::delete('travel/flight-booking/{id}', [FlightBookingController::class, 'destroy'])->name('flight-booking.destroy');
        Route::get('travel/flight-booking/flights/available', [FlightBookingController::class, 'getAvailableFlights'])->name('flight-booking.getAvailableFlights');

        // Hotel Booking
        Route::post('travel/hotel-booking', [HotelBookingController::class, 'store'])->name('hotel-booking.store');
        Route::put('travel/hotel-booking/{id}/status', [HotelBookingController::class, 'updateStatus'])->name('hotel-booking.updateStatus');
        Route::post('travel/hotel-booking/{id}/assign-rooms', [HotelBookingController::class, 'assignRooms'])->name('hotel-booking.assignRooms');
        Route::delete('travel/hotel-booking/{bookingId}/assignment/{assignmentId}', [HotelBookingController::class, 'removeAssignment'])->name('hotel-booking.removeAssignment');
        Route::get('travel/hotel-booking/{id}/roomlist', [HotelBookingController::class, 'generateRoomlist'])->name('hotel-booking.generateRoomlist');
        Route::get('travel/hotel-booking/{id}/assignments', [HotelBookingController::class, 'getRoomAssignments'])->name('hotel-booking.getRoomAssignments');
        Route::get('travel/hotel-booking/keberangkatan/{keberangkatanId}', [HotelBookingController::class, 'getByKeberangkatan'])->name('hotel-booking.getByKeberangkatan');
        Route::delete('travel/hotel-booking/{id}', [HotelBookingController::class, 'destroy'])->name('hotel-booking.destroy');
        Route::get('travel/hotel-booking/hotels/available', [HotelBookingController::class, 'getAvailableHotels'])->name('hotel-booking.getAvailableHotels');

        // Payment Management
        Route::get('travel/booking/{booking}/payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('travel/booking/{booking}/payment', [PaymentController::class, 'store'])->name('payment.store');
        Route::get('travel/booking/{booking}/payments', [PaymentController::class, 'index'])->name('payment.index');
        Route::delete('travel/payment/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');
        Route::get('travel/payment/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('payment.receipt');
        Route::get('travel/payment/{payment}/receipt/download', [PaymentController::class, 'downloadReceipt'])->name('payment.receipt.download');
        Route::get('travel/payments/overdue', [PaymentController::class, 'overduePayments'])->name('payment.overdue');
        Route::post('travel/booking/{booking}/reminder', [PaymentController::class, 'sendReminder'])->name('payment.reminder');
        Route::get('travel/booking/{booking}/departure-eligibility', [PaymentController::class, 'checkDepartureEligibility'])->name('payment.departure-eligibility');
        Route::post('travel/booking/{booking}/invoice', [PaymentController::class, 'createInvoice'])->name('payment.create-invoice');
        Route::post('travel/booking/{booking}/invoice/update', [PaymentController::class, 'updateInvoice'])->name('payment.update-invoice');
        Route::get('travel/booking/{booking}/invoice/preview', [PaymentController::class, 'previewInvoice'])->name('payment.preview-invoice');
        Route::delete('travel/booking/{booking}/invoice', [PaymentController::class, 'deleteInvoice'])->name('payment.delete-invoice');
        Route::get('travel/booking/{booking}/invoice/pdf', [PaymentController::class, 'generateJamaahInvoice'])->name('payment.jamaah-invoice-pdf');
        
        // QRIS Payment (Admin)
        Route::post('travel/booking/{booking}/qris/generate', [\App\Http\Controllers\QrisPaymentController::class, 'adminGenerateQris'])->name('payment.qris.generate');
        Route::get('travel/booking/{booking}/qris/history', [\App\Http\Controllers\QrisPaymentController::class, 'adminQrisHistory'])->name('payment.qris.history');

        // Payment Verification (Task 9)
        Route::get('travel/payment/verify', [PaymentController::class, 'verifyIndex'])->name('travel.payment.verify');
        Route::post('travel/payment/{paymentId}/verify', [PaymentController::class, 'verifyPayment'])->name('travel.payment.verify.action');
        Route::post('travel/payment/{paymentId}/reject', [PaymentController::class, 'rejectPayment'])->name('travel.payment.reject');

        // Document Management
        Route::get('travel/booking/{booking}/documents', [DocumentController::class, 'index'])->name('document.index');
        Route::post('travel/booking/{booking}/documents/upload', [DocumentController::class, 'upload'])->name('document.upload');
        Route::post('travel/document/{document}/verify', [DocumentController::class, 'verify'])->name('document.verify');
        Route::delete('travel/document/{document}', [DocumentController::class, 'destroy'])->name('document.destroy');
        Route::get('travel/document/{document}/download', [DocumentController::class, 'download'])->name('document.download');
        Route::get('travel/document/{document}/preview', [DocumentController::class, 'preview'])->name('document.preview');
        Route::get('travel/documents/expiring', [DocumentController::class, 'getExpiringDocuments'])->name('document.expiring');
        Route::get('travel/keberangkatan/{keberangkatan}/manifest', [DocumentController::class, 'generateManifest'])->name('document.manifest');
        Route::get('travel/keberangkatan/{keberangkatan}/siskopatuh', [DocumentController::class, 'generateSiskopatuh'])->name('document.siskopatuh');
        Route::get('travel/keberangkatan/{keberangkatan}/roomlist', [DocumentController::class, 'generateRoomlist'])->name('document.roomlist');
        Route::get('travel/keberangkatan/{keberangkatan}/roomlist-stream', [DocumentController::class, 'generateRoomlistStream'])->name('document.roomlist-stream');
        Route::get('travel/keberangkatan/{keberangkatan}/stiker-koper', [DocumentController::class, 'generateStikerKoper'])->name('document.stiker-koper');
        Route::get('travel/keberangkatan/{keberangkatan}/documents/status', [DocumentController::class, 'checkCompletionStatus'])->name('document.completion-status');
        
        // Room Position Management
        Route::get('travel/document/{keberangkatan}/manage-room-position', [DocumentController::class, 'manageRoomPosition'])->name('document.manage-room-position');
        Route::get('travel/document/{keberangkatan}/room-positions', [DocumentController::class, 'getRoomPositions'])->name('document.get-room-positions');
        Route::post('travel/document/{keberangkatan}/room-positions', [DocumentController::class, 'saveRoomPositions'])->name('document.save-room-positions');
        // New room assignment endpoints
        Route::get('travel/document/{keberangkatan}/room-assignments', [DocumentController::class, 'getRoomAssignments'])->name('document.get-room-assignments');
        Route::post('travel/document/{keberangkatan}/room-assignments', [DocumentController::class, 'saveRoomAssignments'])->name('document.save-room-assignments');
        Route::post('travel/document/{keberangkatan}/room-assignments/auto', [DocumentController::class, 'autoAssignRooms'])->name('document.auto-assign-rooms');

        // Logistics
        Route::get('travel/keberangkatan/{keberangkatan}/logistics', [LogisticsController::class, 'index'])->name('logistics.index');
        Route::post('travel/keberangkatan/{keberangkatan}/logistics', [LogisticsController::class, 'store'])->name('logistics.store');
        Route::put('travel/logistics/{id}', [LogisticsController::class, 'update'])->name('logistics.update');
        Route::put('travel/logistics/{id}/status', [LogisticsController::class, 'updateStatus'])->name('logistics.updateStatus');
        Route::delete('travel/logistics/{id}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');
        Route::post('travel/keberangkatan/{keberangkatan}/logistics/initialize', [LogisticsController::class, 'initializeDefaults'])->name('logistics.initialize');
        Route::get('travel/keberangkatan/{keberangkatan}/logistics/packing-list', [LogisticsController::class, 'generatePackingList'])->name('logistics.packing-list');

        // Customer Communication
        Route::get('travel/communication', [CommunicationController::class, 'index'])->name('travel.communication.index');
        Route::get('travel/communication/data', [CommunicationController::class, 'getData'])->name('travel.communication.data');
        Route::post('travel/communication', [CommunicationController::class, 'store'])->name('travel.communication.store');
        Route::get('travel/communication/{id}', [CommunicationController::class, 'show'])->name('travel.communication.show');
        Route::put('travel/communication/{id}', [CommunicationController::class, 'update'])->name('travel.communication.update');
        Route::delete('travel/communication/{id}', [CommunicationController::class, 'destroy'])->name('travel.communication.destroy');
        Route::get('travel/communication/member/{memberId}/history', [CommunicationController::class, 'getMemberHistory'])->name('travel.communication.member-history');
        Route::get('travel/communication/pending-followups', [CommunicationController::class, 'getPendingFollowUps'])->name('travel.communication.pending-followups');
        Route::get('travel/communication/member/{memberId}/metrics', [CommunicationController::class, 'getResponseMetrics'])->name('travel.communication.metrics');
        Route::post('travel/communication/schedule-followup', [CommunicationController::class, 'scheduleFollowUp'])->name('travel.communication.schedule-followup');

        // Reports
        Route::get('travel/report', [ReportController::class, 'index'])->name('travel.report.index');
        Route::get('travel/report/dashboard', [ReportController::class, 'dashboard'])->name('travel.report.dashboard');
        Route::get('travel/report/departure-summary', [ReportController::class, 'departureSummary'])->name('travel.report.departure-summary');
        Route::get('travel/report/departure-summary/pdf', [ReportController::class, 'exportDepartureSummaryPdf'])->name('travel.report.departure-summary.pdf');
        Route::get('travel/report/departure-summary/excel', [ReportController::class, 'exportDepartureSummaryExcel'])->name('travel.report.departure-summary.excel');
        Route::get('travel/report/financial', [ReportController::class, 'financial'])->name('travel.report.financial');
        Route::get('travel/report/financial/pdf', [ReportController::class, 'exportFinancialPdf'])->name('travel.report.financial.pdf');
        Route::get('travel/report/financial/excel', [ReportController::class, 'exportFinancialExcel'])->name('travel.report.financial.excel');
        Route::get('travel/report/operational', [ReportController::class, 'operational'])->name('travel.report.operational');
        Route::get('travel/report/operational/pdf', [ReportController::class, 'exportOperationalPdf'])->name('travel.report.operational.pdf');
        Route::get('travel/report/operational/excel', [ReportController::class, 'exportOperationalExcel'])->name('travel.report.operational.excel');
        Route::get('travel/report/team-performance', [ReportController::class, 'teamPerformance'])->name('travel.report.team-performance');
        Route::get('travel/report/team-performance/pdf', [ReportController::class, 'exportTeamPerformancePdf'])->name('travel.report.team-performance.pdf');
        Route::get('travel/report/team-performance/excel', [ReportController::class, 'exportTeamPerformanceExcel'])->name('travel.report.team-performance.excel');

        Route::post('travel/tasks/{id}/reassign', [TaskController::class, 'reassign'])->name('travel.tasks.reassign');
        Route::put('travel/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('travel.tasks.update-status');
        Route::get('travel/tasks/team/{teamCode}/members', [TaskController::class, 'getTeamMembers'])->name('travel.tasks.team-members');
        Route::get('travel/tasks/overdue-count', [TaskController::class, 'overdueCount'])->name('travel.tasks.overdue-count');
        
        // Affiliate Management (Admin)
        Route::prefix('affiliate')->name('affiliate.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'getData'])->name('data');
            Route::get('/hierarchy/tree', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'hierarchyTree'])->name('hierarchy.tree');
            Route::post('/hierarchy/fee/save', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'saveLineFee'])->name('hierarchy.fee.save');
            Route::patch('/{affiliator}/update-hierarchy', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'updateHierarchy'])->name('update-hierarchy');
            Route::get('/leaderboard', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'leaderboard'])->name('leaderboard');
            Route::get('/payouts', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'payouts'])->name('payouts');
            Route::get('/payouts/data', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'getPayoutsData'])->name('payouts.data');
            Route::post('/payouts/{payout}/approve', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'approvePayout'])->name('payouts.approve');
            Route::post('/payouts/{payout}/reject', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'rejectPayout'])->name('payouts.reject');
            Route::get('/settings', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'settings'])->name('settings');
            Route::post('/settings', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'updateSettings'])->name('settings.update');
            Route::get('/{affiliator}', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'show'])->name('show');
            Route::patch('/{affiliator}/approve', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'approve'])->name('approve');
            Route::patch('/{affiliator}/suspend', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'suspend'])->name('suspend');
            Route::patch('/{affiliator}/update-commission', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'updateCommission'])->name('update-commission');
            Route::patch('/referral/{referral}/verify', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'verifyReferral'])->name('referral.verify');
            Route::patch('/referral/{referral}/release-termin1', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'releaseTermin1'])->name('referral.release-termin1');
            Route::patch('/referral/{referral}/release-termin2', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'releaseTermin2'])->name('referral.release-termin2');
            Route::patch('/referral/{referral}/force-release', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'forceRelease'])->name('referral.force-release');
            Route::delete('/{affiliator}', [\App\Http\Controllers\Admin\AffiliateAdminController::class, 'destroy'])->name('destroy');
        });
        
        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

        // Search and Filtering
        Route::get('search', [SearchController::class, 'search'])->name('search');
        Route::get('search/advanced', function() {
            return view('admin.search.advanced');
        })->name('search.advanced');
        Route::get('search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');
        Route::get('search/filter/packages', [SearchController::class, 'filterPackages'])->name('search.filter.packages');
        Route::get('search/filter/jamaah', [SearchController::class, 'filterJamaah'])->name('search.filter.jamaah');
        Route::get('search/filter/tasks', [SearchController::class, 'filterTasks'])->name('search.filter.tasks');
        Route::post('search/filter/save', [SearchController::class, 'saveFilter'])->name('search.filter.save');
        Route::get('search/filter/saved', [SearchController::class, 'getSavedFilters'])->name('search.filter.saved');
        Route::delete('search/filter/{id}', [SearchController::class, 'deleteSavedFilter'])->name('search.filter.delete');
        
        // Audit Logs
        Route::get('audit', [AuditLogController::class, 'index'])->name('admin.audit.index');
        Route::get('audit/export', [AuditLogController::class, 'export'])->name('admin.audit.export');
        
        // Kategori
        Route::get('kategori/generate-kode', [KategoriController::class, 'getNewKode'])->name('kategori.generate-kode');
        Route::get('kategori-data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::get('kategori/export/pdf', [KategoriController::class, 'exportPdf'])->name('kategori.export.pdf');
        Route::get('kategori/export/excel', [KategoriController::class, 'exportExcel'])->name('kategori.export.excel');
        Route::post('kategori/import/excel', [KategoriController::class, 'importExcel'])->name('kategori.import.excel');
        Route::get('kategori/download-template', [KategoriController::class, 'downloadTemplate'])->name('kategori.download-template');
        Route::get('kategori/groups', [KategoriController::class, 'getGroups'])->name('kategori.groups');
        Route::get('kategori/outlets', [KategoriController::class, 'getOutlets'])->name('kategori.outlets');
        Route::resource('kategori', KategoriController::class);

        // Satuan Routes
        Route::get('satuan/generate-kode', [SatuanController::class, 'getNewKode'])->name('satuan.generate-kode');
        Route::get('satuan-data', [SatuanController::class, 'data'])->name('satuan.data');
        Route::get('satuan/export/pdf', [SatuanController::class, 'exportPdf'])->name('satuan.export.pdf');
        Route::get('satuan/export/excel', [SatuanController::class, 'exportExcel'])->name('satuan.export.excel');
        Route::post('satuan/import/excel', [SatuanController::class, 'importExcel'])->name('satuan.import.excel');
        Route::get('satuan/download-template', [SatuanController::class, 'downloadTemplate'])->name('satuan.download-template');
        Route::get('satuan/satuan-utama', [SatuanController::class, 'getSatuanUtama'])->name('satuan.satuan-utama');
        Route::resource('satuan', SatuanController::class);

        // Produk Routes
        Route::get('produk/generate-sku', [ProdukController::class, 'getNewSku'])->name('produk.generate-sku');
        Route::get('produk-data', [ProdukController::class, 'data'])->name('produk.data');
        Route::get('produk/export/pdf', [ProdukController::class, 'exportPdf'])->name('produk.export.pdf');
        Route::get('produk/export/excel', [ProdukController::class, 'exportExcel'])->name('produk.export.excel');
        Route::post('produk/import/excel', [ProdukController::class, 'importExcel'])->name('produk.import.excel');
        Route::get('produk/download-template', [ProdukController::class, 'downloadTemplate'])->name('produk.download-template');
        Route::get('produk/outlets', [ProdukController::class, 'getOutlets'])->name('produk.outlets');
        Route::get('produk/categories', [ProdukController::class, 'getCategories'])->name('produk.categories');
        Route::get('produk/units', [ProdukController::class, 'getUnits'])->name('produk.units');
        Route::get('produk/id-mappings', [ProdukController::class, 'getIdMappings'])->name('produk.id-mappings');
        Route::post('produk/{productId}/images/set-primary', [ProdukController::class, 'setPrimaryImage'])->name('produk.set-primary-image');
        Route::delete('produk/{productId}/images/remove', [ProdukController::class, 'removeImage'])->name('produk.remove-image');
        Route::post('produk/{productId}/add-stock', [ProdukController::class, 'addStock'])->name('produk.add-stock');
        Route::get('produk/{productId}/hpp-data', [ProdukController::class, 'getHppData'])->name('produk.hpp-data');
        Route::post('produk/{productId}/hpp', [ProdukController::class, 'storeHpp'])->name('produk.hpp.store');
        Route::put('produk/{productId}/hpp/{hppId}', [ProdukController::class, 'updateHpp'])->name('produk.hpp.update');
        Route::delete('produk/{productId}/hpp/{hppId}', [ProdukController::class, 'destroyHpp'])->name('produk.hpp.destroy');
        Route::resource('produk', ProdukController::class);

        // Bahan Routes
        Route::get('bahan/generate-kode', [BahanController::class, 'getNewKode'])->name('bahan.generate-kode');
        Route::get('bahan-data', [BahanController::class, 'data'])->name('bahan.data');
        Route::get('bahan/export/pdf', [BahanController::class, 'exportPdf'])->name('bahan.export.pdf');
        Route::get('bahan/export/excel', [BahanController::class, 'exportExcel'])->name('bahan.export.excel');
        Route::post('bahan/import/excel', [BahanController::class, 'importExcel'])->name('bahan.import.excel');
        Route::get('bahan/download-template', [BahanController::class, 'downloadTemplate'])->name('bahan.download-template');
        Route::get('bahan/outlets', [BahanController::class, 'getOutlets'])->name('bahan.outlets');
        Route::get('bahan/satuan', [BahanController::class, 'getSatuan'])->name('bahan.satuan');
        Route::get('bahan/{id}/edit-harga', [BahanController::class, 'editHarga'])->name('bahan.edit_harga');
        Route::put('bahan/harga/{id}', [BahanController::class, 'updateHarga'])->name('bahan.update_harga');
        Route::delete('bahan/{id}/destroy-harga', [BahanController::class, 'destroyHarga'])->name('bahan.destroy_harga');
        Route::post('bahan/store-stock', [BahanController::class, 'storeStock'])->name('bahan.store-stock');
        Route::put('bahan/stock/{id}', [BahanController::class, 'updateStock'])->name('bahan.update-stock');
        Route::put('bahan/price/{id}', [BahanController::class, 'updateHargaBeli'])->name('bahan.update-price');
        Route::resource('bahan', BahanController::class);

        // Sparepart Routes
        Route::get('sparepart', [SparepartController::class, 'index'])->name('sparepart.index');
        Route::get('sparepart/data', [SparepartController::class, 'getData'])->name('sparepart.data');
        Route::get('sparepart/generate-kode', [SparepartController::class, 'generateKode'])->name('sparepart.generate-kode');
        Route::get('sparepart/search', [SparepartController::class, 'search'])->name('sparepart.search');
        Route::get('sparepart/search-karyawan', [SparepartController::class, 'searchKaryawan'])->name('sparepart.search-karyawan');
        Route::post('sparepart', [SparepartController::class, 'store'])->name('sparepart.store');
        Route::get('sparepart/{id}', [SparepartController::class, 'show'])->name('sparepart.show');
        Route::put('sparepart/{id}', [SparepartController::class, 'update'])->name('sparepart.update');
        Route::delete('sparepart/{id}', [SparepartController::class, 'destroy'])->name('sparepart.destroy');
        Route::post('sparepart/{id}/adjust', [SparepartController::class, 'adjustStok'])->name('sparepart.adjust');
        Route::post('sparepart/{id}/adjust-price', [SparepartController::class, 'adjustPrice'])->name('sparepart.adjust-price');
        Route::get('sparepart/{id}/logs', [SparepartController::class, 'getLogs'])->name('sparepart.logs');
        Route::post('sparepart/export', [SparepartController::class, 'export'])->name('sparepart.export');
        Route::post('sparepart/bulk-delete', [SparepartController::class, 'bulkDelete'])->name('sparepart.bulk-delete');

        // Inventori Routes
        Route::get('inventori/generate-kode', [InventoriController::class, 'getNewKode'])->name('inventori.generate-kode');
        Route::get('inventori-data', [InventoriController::class, 'data'])->name('inventori.data');
        Route::get('inventori/export/pdf', [InventoriController::class, 'exportPdf'])->name('inventori.export.pdf');
        Route::get('inventori/export/excel', [InventoriController::class, 'exportExcel'])->name('inventori.export.excel');
        Route::post('inventori/import/excel', [InventoriController::class, 'importExcel'])->name('inventori.import.excel');
        Route::get('inventori/download-template', [InventoriController::class, 'downloadTemplate'])->name('inventori.download-template');
        Route::get('inventori/outlets', [InventoriController::class, 'getOutlets'])->name('inventori.outlets');
        Route::get('inventori/categories', [InventoriController::class, 'getCategories'])->name('inventori.categories');
        Route::resource('inventori', InventoriController::class);

        // Transfer Gudang Routes
        Route::get('transfer-gudang/outlets', [TransferGudangController::class, 'getOutlets'])->name('transfer-gudang.outlets');
        Route::get('transfer-gudang/items', [TransferGudangController::class, 'getItems'])->name('transfer-gudang.items');
        Route::get('transfer-gudang/data', [TransferGudangController::class, 'data'])->name('transfer-gudang.data');
        Route::post('transfer-gudang/{id}/approve', [TransferGudangController::class, 'approve'])->name('transfer-gudang.approve');
        Route::post('transfer-gudang/{id}/reject', [TransferGudangController::class, 'reject'])->name('transfer-gudang.reject');
        Route::get('transfer-gudang/export/pdf', [TransferGudangController::class, 'exportPdf'])->name('transfer-gudang.export.pdf');
        Route::get('transfer-gudang/export/excel', [TransferGudangController::class, 'exportExcel'])->name('transfer-gudang.export.excel');
        Route::resource('transfer-gudang', TransferGudangController::class);
        
    });
    
    // ====== CRM (Customer Relationship Management) ======
    Route::prefix('crm')->name('crm.')->group(function () {
        // CRM Dashboard
        Route::get('/', [App\Http\Controllers\CrmDashboardController::class, 'index'])->name('index');
        Route::get('/dashboard/analytics', [App\Http\Controllers\CrmDashboardController::class, 'getAnalytics'])->name('dashboard.analytics');
        Route::get('/dashboard/predictions', [App\Http\Controllers\CrmDashboardController::class, 'getPredictions'])->name('dashboard.predictions');
        
        // Tipe Customer Routes
        Route::middleware('permission:crm.tipe.view')->group(function () {
            Route::get('tipe', [App\Http\Controllers\CustomerTypeController::class, 'index'])->name('tipe.index');
            Route::get('tipe/statistics', [App\Http\Controllers\CustomerTypeController::class, 'getStatistics'])->name('tipe.statistics');
            Route::get('tipe/data', [App\Http\Controllers\CustomerTypeController::class, 'getData'])->name('tipe.data');
            Route::get('tipe/search-products', [App\Http\Controllers\CustomerTypeController::class, 'searchProducts'])->name('tipe.search-products');
            Route::get('tipe/{id}', [App\Http\Controllers\CustomerTypeController::class, 'show'])->name('tipe.show');
            Route::get('tipe/{id}/products', [App\Http\Controllers\CustomerTypeController::class, 'getTypeProducts'])->name('tipe.products');
        });
        Route::post('tipe', [App\Http\Controllers\CustomerTypeController::class, 'store'])
            ->middleware('permission:crm.tipe.create')->name('tipe.store');
        Route::put('tipe/{id}', [App\Http\Controllers\CustomerTypeController::class, 'update'])
            ->middleware('permission:crm.tipe.update')->name('tipe.update');
        Route::delete('tipe/{id}', [App\Http\Controllers\CustomerTypeController::class, 'destroy'])
            ->middleware('permission:crm.tipe.delete')->name('tipe.destroy');
        Route::post('tipe/{id}/products', [App\Http\Controllers\CustomerTypeController::class, 'addProduct'])
            ->middleware('permission:crm.tipe.update')->name('tipe.add-product');
        Route::put('tipe/products/{id}', [App\Http\Controllers\CustomerTypeController::class, 'updateProduct'])
            ->middleware('permission:crm.tipe.update')->name('tipe.update-product');
        Route::delete('tipe/products/{id}', [App\Http\Controllers\CustomerTypeController::class, 'removeProduct'])
            ->middleware('permission:crm.tipe.update')->name('tipe.remove-product');
        
        // Pelanggan Routes
        Route::middleware('permission:crm.pelanggan.view')->group(function () {
            Route::get('pelanggan', [CustomerManagementController::class, 'index'])->name('pelanggan.index');
            Route::get('pelanggan/statistics', [CustomerManagementController::class, 'getStatistics'])->name('pelanggan.statistics');
            Route::get('pelanggan/data', [CustomerManagementController::class, 'getData'])->name('pelanggan.data');
            Route::get('pelanggan/tipes-by-outlets', [CustomerManagementController::class, 'getTipesByOutlets'])->name('pelanggan.tipes-by-outlets');
            Route::get('pelanggan/{id}', [CustomerManagementController::class, 'show'])->name('pelanggan.show');
        });
        Route::get('pelanggan/export/excel', [CustomerManagementController::class, 'exportExcel'])
            ->middleware('permission:crm.pelanggan.export')->name('pelanggan.export.excel');
        Route::get('pelanggan/export/pdf', [CustomerManagementController::class, 'exportPdf'])
            ->middleware('permission:crm.pelanggan.export')->name('pelanggan.export.pdf');
        Route::post('pelanggan/import/excel', [CustomerManagementController::class, 'importExcel'])
            ->middleware('permission:crm.pelanggan.import')->name('pelanggan.import.excel');
        Route::post('pelanggan/ocr/ktp', [CustomerManagementController::class, 'ocrKtp'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.ocr.ktp');
        Route::post('pelanggan/ocr/passport', [CustomerManagementController::class, 'ocrPassport'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.ocr.passport');
        Route::post('pelanggan/ocr/visa', [CustomerManagementController::class, 'ocrVisa'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.ocr.visa');
        Route::post('pelanggan/ocr/sertifikat-kesehatan', [CustomerManagementController::class, 'ocrSertifikatKesehatan'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.ocr.sertifikat-kesehatan');
        // Upload & hapus dokumen member (manifest)
        Route::post('pelanggan/{id}/upload-doc', [CustomerManagementController::class, 'uploadMemberDocument'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.upload-doc');
        Route::delete('pelanggan/{id}/delete-doc', [CustomerManagementController::class, 'deleteMemberDocument'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.delete-doc');
        Route::post('pelanggan', [CustomerManagementController::class, 'store'])
            ->middleware('permission:crm.pelanggan.create')->name('pelanggan.store');
        Route::put('pelanggan/{id}', [CustomerManagementController::class, 'update'])
            ->middleware('permission:crm.pelanggan.update')->name('pelanggan.update');
        Route::delete('pelanggan/{id}', [CustomerManagementController::class, 'destroy'])
            ->middleware('permission:crm.pelanggan.delete')->name('pelanggan.destroy');
    });

    // ====== FINANCE & ACCOUNTING ======
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('outlets', [FinanceAccountantController::class, 'getOutlets'])->name('outlets.data');

        // RAB Routes
        Route::get('rab', [FinanceAccountantController::class, 'rabIndex'])->name('rab.index');
        Route::get('rab/data', [FinanceAccountantController::class, 'rabData'])->name('rab.data');
        Route::post('rab', [FinanceAccountantController::class, 'storeRab'])->name('rab.store');
        Route::put('rab/{id}', [FinanceAccountantController::class, 'updateRab'])->name('rab.update');
        Route::delete('rab/{id}', [FinanceAccountantController::class, 'deleteRab'])->name('rab.delete');
        Route::post('rab/{id}/realisasi', [FinanceAccountantController::class, 'saveRealisasi'])->name('rab.realisasi');
        Route::post('rab/{id}/realisasi-simple', [FinanceAccountantController::class, 'saveRealisasiSimple'])->name('rab.realisasi-simple');
        Route::post('rab/{id}/realisasi-per-item', [FinanceAccountantController::class, 'saveRealisasiPerItem'])->name('rab.realisasi-per-item');
        Route::get('rab/{id}/history', [FinanceAccountantController::class, 'getRealisasiHistory'])->name('rab.history');

        // Expense Routes
        Route::get('biaya', [FinanceAccountantController::class, 'biayaIndex'])->name('biaya.index');
        Route::get('biaya/data', [FinanceAccountantController::class, 'expensesData'])->name('expenses.data');
        Route::get('biaya/stats', [FinanceAccountantController::class, 'expensesStats'])->name('expenses.stats');
        Route::get('biaya/chart-data', [FinanceAccountantController::class, 'expensesChartData'])->name('expenses.chart-data');
        Route::post('biaya', [FinanceAccountantController::class, 'storeExpense'])->name('expenses.store');
        Route::put('biaya/{id}', [FinanceAccountantController::class, 'updateExpense'])->name('expenses.update');
        Route::delete('biaya/{id}', [FinanceAccountantController::class, 'deleteExpense'])->name('expenses.delete');
        Route::post('biaya/{id}/approve', [FinanceAccountantController::class, 'approveExpense'])->name('expenses.approve');
        Route::post('biaya/{id}/reject', [FinanceAccountantController::class, 'rejectExpense'])->name('expenses.reject');
        Route::post('biaya/from-realisasi', [FinanceAccountantController::class, 'createExpenseFromRealisasi'])->name('expenses.from-realisasi');
        Route::get('biaya/export/xlsx', [FinanceAccountantController::class, 'exportExpensesXLSX'])->name('expenses.export.xlsx');
        Route::get('biaya/export/pdf', [FinanceAccountantController::class, 'exportExpensesPDF'])->name('expenses.export.pdf');
    });

    Route::prefix('penjualan')->name('penjualan.')->group(function () {
    
        // Dashboard
        Route::get('/', [App\Http\Controllers\SalesDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/data', [App\Http\Controllers\SalesDashboardController::class, 'getData'])->name('dashboard.data');
    
        // POS Routes
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');
        Route::get('/pos/customers', [PosController::class, 'getCustomers'])->name('pos.customers');
        Route::get('/pos/customer-type-prices', [PosController::class, 'getCustomerTypePrices'])->name('pos.customer-type-prices');
        Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
        Route::get('/pos/history-data', [PosController::class, 'historyData'])->name('pos.history.data');
        Route::get('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings');
        Route::post('/pos/coa-settings', [PosController::class, 'coaSettings'])->name('pos.coa.settings.update');
        Route::get('/pos/{id}', [PosController::class, 'show'])->name('pos.show');
        Route::get('/pos/{id}/print', [PosController::class, 'print'])->name('pos.print');
        
        // POS Cache Management Routes
        Route::post('/pos/cache/clear', [PosController::class, 'clearProductsCache'])->name('pos.cache.clear');
        Route::post('/pos/cache/clear-customers', [PosController::class, 'clearCustomerCache'])->name('pos.cache.clear-customers');
        Route::post('/pos/cache/warm', [PosController::class, 'warmProductsCache'])->name('pos.cache.warm');
        
        // Tambahkan route untuk submenu penjualan lainnya
        Route::get('/laporan-penjualan', [SalesReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan-penjualan/data', [SalesReportController::class, 'getData'])->name('laporan.data');
        Route::get('/laporan-penjualan/export-pdf', [SalesReportController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::delete('/laporan-penjualan/{source}/{id}', [SalesReportController::class, 'delete'])->name('laporan.delete');
        
        // Margin Report Routes
        Route::get('/laporan-margin', [MarginReportController::class, 'index'])->name('margin.index');
        Route::get('/laporan-margin/data', [MarginReportController::class, 'getData'])->name('margin.data');
        Route::get('/laporan-margin/export-pdf', [MarginReportController::class, 'exportPdf'])->name('margin.export-pdf');
        Route::view('/agen-gerobakan', 'admin.penjualan.agen_gerobak.index')->name('agen_gerobak.index');
        Route::view('/halaman-agen', 'admin.penjualan.agen.index')->name('agen.index');
        
        // Inter Outlet Sale Routes
        Route::get('/inter-outlet', [App\Http\Controllers\InterOutletSaleController::class, 'index'])->name('inter-outlet.index');
        Route::get('/inter-outlet/products', [App\Http\Controllers\InterOutletSaleController::class, 'getProducts'])->name('inter-outlet.products');
        Route::get('/inter-outlet/outlets', [App\Http\Controllers\InterOutletSaleController::class, 'getOutlets'])->name('inter-outlet.outlets');
        Route::post('/inter-outlet/store', [App\Http\Controllers\InterOutletSaleController::class, 'store'])->name('inter-outlet.store');
        Route::get('/inter-outlet/history', [App\Http\Controllers\InterOutletSaleController::class, 'history'])->name('inter-outlet.history');
        Route::get('/inter-outlet/history-data', [App\Http\Controllers\InterOutletSaleController::class, 'historyData'])->name('inter-outlet.history.data');
        Route::get('/inter-outlet/coa-modal-data', [App\Http\Controllers\InterOutletSaleController::class, 'getCoaModalData'])->name('inter-outlet.coa-modal-data');
        Route::get('/inter-outlet/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet.coa-settings');
        Route::post('/inter-outlet/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet.coa-settings.update');
        
        // Price Settings Routes (must be before parameterized routes)
        Route::get('/inter-outlet/price-products', [App\Http\Controllers\InterOutletSaleController::class, 'getPriceProducts'])->name('inter-outlet.price-products');
        Route::post('/inter-outlet/update-price', [App\Http\Controllers\InterOutletSaleController::class, 'updatePrice'])->name('inter-outlet.update-price');
        Route::post('/inter-outlet/bulk-update-prices', [App\Http\Controllers\InterOutletSaleController::class, 'bulkUpdatePrices'])->name('inter-outlet.bulk-update-prices');
        
        // Parameterized routes (must be last)
        Route::get('/inter-outlet/{id}', [App\Http\Controllers\InterOutletSaleController::class, 'show'])->name('inter-outlet.show');
        Route::get('/inter-outlet/{id}/print', [App\Http\Controllers\InterOutletSaleController::class, 'print'])->name('inter-outlet.print');
        Route::post('/inter-outlet/{id}/approve', [App\Http\Controllers\InterOutletSaleController::class, 'approve'])->name('inter-outlet.approve');
        Route::delete('/inter-outlet/{id}', [App\Http\Controllers\InterOutletSaleController::class, 'destroy'])->name('inter-outlet.destroy');

        // Route aliases for compatibility
        Route::get('/inter-outlet-sale', [App\Http\Controllers\InterOutletSaleController::class, 'index'])->name('inter-outlet-sale.index');
        Route::get('/inter-outlet-sale/products', [App\Http\Controllers\InterOutletSaleController::class, 'getProducts'])->name('inter-outlet-sale.products');
        Route::get('/inter-outlet-sale/outlets', [App\Http\Controllers\InterOutletSaleController::class, 'getOutlets'])->name('inter-outlet-sale.outlets');
        Route::post('/inter-outlet-sale/store', [App\Http\Controllers\InterOutletSaleController::class, 'store'])->name('inter-outlet-sale.store');
        Route::get('/inter-outlet-sale/history', [App\Http\Controllers\InterOutletSaleController::class, 'history'])->name('inter-outlet-sale.history');
        Route::get('/inter-outlet-sale/history-data', [App\Http\Controllers\InterOutletSaleController::class, 'historyData'])->name('inter-outlet-sale.history.data');
        Route::get('/inter-outlet-sale/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet-sale.coa-settings');
        Route::post('/inter-outlet-sale/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet-sale.coa-settings.update');
        Route::get('/inter-outlet-sale/{id}', [App\Http\Controllers\InterOutletSaleController::class, 'show'])->name('inter-outlet-sale.show');
        Route::get('/inter-outlet-sale/{id}/print', [App\Http\Controllers\InterOutletSaleController::class, 'print'])->name('inter-outlet-sale.print');
        Route::post('/inter-outlet-sale/{id}/approve', [App\Http\Controllers\InterOutletSaleController::class, 'approve'])->name('inter-outlet-sale.approve');

        // Kontra Bon Routes
        Route::prefix('kontrabon')->name('kontrabon.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\KontraBonController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\KontraBonController::class, 'store'])->name('store');
            Route::get('/data', [App\Http\Controllers\Admin\KontraBonController::class, 'data'])->name('data');
            Route::get('/data-kontrabon', [App\Http\Controllers\Admin\KontraBonController::class, 'dataKontraBon'])->name('data-kontrabon');
            Route::get('/get-piutang/{id_member}', [App\Http\Controllers\Admin\KontraBonController::class, 'getPiutang'])->name('get-piutang');
            Route::get('/{id}/print', [App\Http\Controllers\Admin\KontraBonController::class, 'print'])->name('print');
            Route::post('/{id}/lunasi', [App\Http\Controllers\Admin\KontraBonController::class, 'lunasi'])->name('lunasi');
            Route::delete('/{id}', [App\Http\Controllers\Admin\KontraBonController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [App\Http\Controllers\Admin\KontraBonController::class, 'show'])->name('show');
        });

        // Tambahkan route ini dalam group penjualan
        Route::get('outlets', [SalesManagementController::class, 'getOutlets'])->name('outlets');
        // Invoice Routes - HARUS didefinisikan SEBELUM resource
        Route::get('invoice/generate-kode', [SalesManagementController::class, 'generateInvoiceCode'])->name('invoice.generate-kode');
        Route::get('invoice/data', [SalesManagementController::class, 'invoiceData'])->name('invoice.data');
        Route::get('invoice/export/pdf', [SalesManagementController::class, 'invoiceExportPdf'])->name('invoice.export.pdf');
        Route::get('invoice/export/excel', [SalesManagementController::class, 'invoiceExportExcel'])->name('invoice.export.excel');
        Route::post('invoice/import/excel', [SalesManagementController::class, 'invoiceImportExcel'])->name('invoice.import.excel');
        Route::get('invoice/download-template', [SalesManagementController::class, 'invoiceDownloadTemplate'])->name('invoice.download-template');
        Route::get('invoice/counts', [SalesManagementController::class, 'invoiceStatusCounts'])->name('invoice.counts');
        Route::get('invoice/due-soon', [SalesManagementController::class, 'invoiceDueSoon'])->name('invoice.due-soon');
        Route::get('invoice/{id}/print', [SalesManagementController::class, 'invoicePrint'])->name('invoice.print');
        Route::get('invoice/{id}/preview', [SalesManagementController::class, 'invoicePreview'])->name('invoice.preview');
        Route::post('invoice/{id}/update-status', [SalesManagementController::class, 'updateStatus'])->name('invoice.update-status');
        Route::post('invoice/{id}/cancel', [SalesManagementController::class, 'cancelInvoice'])->name('invoice.cancel');
        Route::get('invoice/setting', [SalesManagementController::class, 'invoiceSetting'])->name('invoice.setting');
        Route::post('invoice/setting/update', [SalesManagementController::class, 'updateInvoiceSetting'])->name('invoice.setting.update');
        Route::get('invoice/{id}/view-bukti-transfer', [SalesManagementController::class, 'viewBuktiTransfer'])->name('invoice.view-bukti-transfer');
        Route::get('invoice/{id}/download-bukti-transfer', [SalesManagementController::class, 'downloadBuktiTransfer'])->name('invoice.download-bukti-transfer');
        
        // Invoice Payment Routes (Installment Support)
        Route::post('invoice/payment', [SalesManagementController::class, 'processInvoicePayment'])->name('invoice.payment.process');
        Route::get('invoice/{id}/payment-history', [SalesManagementController::class, 'getPaymentHistory'])->name('invoice.payment.history');
        
        // Invoice Confirm Route (Draft to Menunggu)
        Route::post('invoice/{id}/confirm', [SalesManagementController::class, 'confirmInvoice'])->name('invoice.confirm');
        
        // Customer & Product Routes
        Route::get('customers', [SalesManagementController::class, 'getCustomers'])->name('customers');
        Route::get('customer/{type}/{id}', [SalesManagementController::class, 'getCustomerDetail'])->name('customer.detail');
        Route::get('customer-prices/{customerId}/{customerType}', [SalesManagementController::class, 'getCustomerPricesByCustomer'])->name('customer-prices.by-customer');
        Route::get('produk/harga-normal', [SalesManagementController::class, 'getProdukHargaNormal'])->name('produk.harga-normal');
        Route::get('produk/harga-khusus', [SalesManagementController::class, 'getProdukHargaKhusus'])->name('produk.harga-khusus');
        
        // COA Setting Routes
        Route::get('coa-setting', [SalesManagementController::class, 'coaSetting'])->name('coa-setting');
        Route::post('coa-setting/update', [SalesManagementController::class, 'coaSettingUpdate'])->name('coa-setting.update');
        Route::get('coa-setting/preview', [SalesManagementController::class, 'coaSettingPreview'])->name('coa-setting.preview');
        Route::get('accounts/by-type', [SalesManagementController::class, 'getAccountsByType'])->name('accounts.by-type');
        Route::get('coa-setting/preview-multi-status', [SalesManagementController::class, 'coaSettingPreviewMultiStatus'])->name('coa-setting.preview-multi-status');
        
        Route::get('/ongkos-kirim/data', [SalesManagementController::class, 'ongkirData'])->name('ongkir.data');
        Route::post('/ongkos-kirim/store', [SalesManagementController::class, 'ongkirStore'])->name('ongkir.store');
        Route::put('/ongkos-kirim/update/{id}', [SalesManagementController::class, 'ongkirUpdate'])->name('ongkir.update');
        Route::delete('/ongkos-kirim/destroy/{id}', [SalesManagementController::class, 'ongkirDestroy'])->name('ongkir.destroy');
        
        // Inter Outlet Sale Routes
        Route::get('/inter-outlet', [App\Http\Controllers\InterOutletSaleController::class, 'index'])->name('inter-outlet.index');
        Route::get('/inter-outlet/products', [App\Http\Controllers\InterOutletSaleController::class, 'getProducts'])->name('inter-outlet.products');
        Route::get('/inter-outlet/outlets', [App\Http\Controllers\InterOutletSaleController::class, 'getOutlets'])->name('inter-outlet.outlets');
        Route::post('/inter-outlet/store', [App\Http\Controllers\InterOutletSaleController::class, 'store'])->name('inter-outlet.store');
        Route::get('/inter-outlet/history', [App\Http\Controllers\InterOutletSaleController::class, 'history'])->name('inter-outlet.history');
        Route::get('/inter-outlet/history-data', [App\Http\Controllers\InterOutletSaleController::class, 'historyData'])->name('inter-outlet.history.data');
        Route::get('/inter-outlet/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet.coa-settings');
        Route::post('/inter-outlet/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet.coa-settings.update');
        Route::get('/inter-outlet/{id}', [App\Http\Controllers\InterOutletSaleController::class, 'show'])->name('inter-outlet.show');
        Route::get('/inter-outlet/{id}/print', [App\Http\Controllers\InterOutletSaleController::class, 'print'])->name('inter-outlet.print');
        Route::post('/inter-outlet/{id}/approve', [App\Http\Controllers\InterOutletSaleController::class, 'approve'])->name('inter-outlet.approve');

        // Route aliases for compatibility
        Route::get('/inter-outlet-sale', [App\Http\Controllers\InterOutletSaleController::class, 'index'])->name('inter-outlet-sale.index');
        Route::get('/inter-outlet-sale/products', [App\Http\Controllers\InterOutletSaleController::class, 'getProducts'])->name('inter-outlet-sale.products');
        Route::get('/inter-outlet-sale/outlets', [App\Http\Controllers\InterOutletSaleController::class, 'getOutlets'])->name('inter-outlet-sale.outlets');
        Route::post('/inter-outlet-sale/store', [App\Http\Controllers\InterOutletSaleController::class, 'store'])->name('inter-outlet-sale.store');
        Route::get('/inter-outlet-sale/history', [App\Http\Controllers\InterOutletSaleController::class, 'history'])->name('inter-outlet-sale.history');
        Route::get('/inter-outlet-sale/history-data', [App\Http\Controllers\InterOutletSaleController::class, 'historyData'])->name('inter-outlet-sale.history.data');
        Route::get('/inter-outlet-sale/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet-sale.coa-settings');
        Route::post('/inter-outlet-sale/coa-settings', [App\Http\Controllers\InterOutletSaleController::class, 'coaSettings'])->name('inter-outlet-sale.coa-settings.update');
        Route::get('/inter-outlet-sale/{id}', [App\Http\Controllers\InterOutletSaleController::class, 'show'])->name('inter-outlet-sale.show');
        Route::get('/inter-outlet-sale/{id}/print', [App\Http\Controllers\InterOutletSaleController::class, 'print'])->name('inter-outlet-sale.print');
        Route::post('/inter-outlet-sale/{id}/approve', [App\Http\Controllers\InterOutletSaleController::class, 'approve'])->name('inter-outlet-sale.approve');

        // Customer Price Routes
        Route::get('customer-price/data', [SalesManagementController::class, 'customerPriceData'])->name('customer-price.data');
        Route::post('customer-price', [SalesManagementController::class, 'customerPriceStore'])->name('customer-price.store');
        Route::get('customer-price/{id}', [SalesManagementController::class, 'customerPriceEdit'])->name('customer-price.edit');
        Route::put('customer-price/{id}', [SalesManagementController::class, 'customerPriceUpdate'])->name('customer-price.update');
        Route::delete('customer-price/{id}', [SalesManagementController::class, 'customerPriceDestroy'])->name('customer-price.destroy');

        // Di routes/web.php dalam group penjualan
        Route::get('company-bank-accounts', [CompanyBankAccountController::class, 'index'])->name('company-bank-accounts.index');
        Route::post('company-bank-accounts', [CompanyBankAccountController::class, 'store'])->name('company-bank-accounts.store');
        Route::put('company-bank-accounts/{id}', [CompanyBankAccountController::class, 'update'])->name('company-bank-accounts.update');
        Route::delete('company-bank-accounts/{id}', [CompanyBankAccountController::class, 'destroy'])->name('company-bank-accounts.destroy');
        
        // Pre Order Routes
        Route::get('preorders', [App\Http\Controllers\PreOrderController::class, 'index'])->name('preorders.index');
        Route::get('preorders/create', [App\Http\Controllers\PreOrderController::class, 'create'])->name('preorders.create');
        Route::post('preorders', [App\Http\Controllers\PreOrderController::class, 'store'])->name('preorders.store');
        Route::get('preorders/{preorder}', [App\Http\Controllers\PreOrderController::class, 'show'])->name('preorders.show');
        Route::put('preorders/{preorder}', [App\Http\Controllers\PreOrderController::class, 'update'])->name('preorders.update');
        Route::post('preorders/{preorder}/update-status', [App\Http\Controllers\PreOrderController::class, 'updateStatus'])->name('preorders.updateStatus');
        
        // Pre Order Data Routes
        Route::get('preorders/customers-by-outlet', [App\Http\Controllers\PreOrderController::class, 'getCustomersByOutlet'])->name('preorders.customers-by-outlet');
        Route::get('preorders/products-by-outlet', [App\Http\Controllers\PreOrderController::class, 'getProductsByOutlet'])->name('preorders.products-by-outlet');
        
        // Pre Order Print Routes
        Route::get('preorders/{preorder}/print/penawaran', [App\Http\Controllers\PreOrderPrintController::class, 'penawaran'])->name('preorders.print.penawaran');
        Route::get('preorders/{preorder}/print/invoice', [App\Http\Controllers\PreOrderPrintController::class, 'invoice'])->name('preorders.print.invoice');
        Route::get('preorders/{preorder}/print/kwitansi', [App\Http\Controllers\PreOrderPrintController::class, 'kwitansi'])->name('preorders.print.kwitansi');
        Route::get('preorders/{preorder}/modal/{type}', [App\Http\Controllers\PreOrderPrintController::class, 'penawaranModal'])->name('preorders.print.modal');
        Route::get('preorders/{preorder}/download/{type}', [App\Http\Controllers\PreOrderPrintController::class, 'penawaran'])->name('preorders.print.download');
        
        // Pre Order Settings Routes
        Route::post('preorders/settings', [App\Http\Controllers\PreOrderSettingController::class, 'store'])->name('preorders.settings.store');
        Route::get('preorders/settings/coas', [App\Http\Controllers\PreOrderSettingController::class, 'getCoas'])->name('preorders.settings.getCoas');

        // Resource Routes (diletakkan di akhir)
        Route::resource('invoice', SalesManagementController::class);
    });

    // ====== SERVICE MANAGEMENT ======
    Route::prefix('service')->name('service.')->group(function () {
        // Invoice Service
        Route::get('/invoice', [ServiceController::class, 'invoiceIndex'])->name('invoice.index');
        Route::post('/invoice', [ServiceController::class, 'storeInvoice'])->name('invoice.store');
        Route::get('/invoice/settings', [ServiceController::class, 'getInvoiceSettings'])->name('invoice.settings.get');
        Route::post('/invoice/settings', [ServiceController::class, 'saveInvoiceSettings'])->name('invoice.settings.save');
        Route::get('/invoice/{id}/print', [ServiceController::class, 'printInvoice'])->name('invoice.print');
        Route::post('/invoice/status/{id}', [ServiceController::class, 'updateStatus'])->name('invoice.status');
        Route::delete('/invoice/{id}', [ServiceController::class, 'deleteInvoice'])->name('invoice.delete');
        Route::post('/invoice/schedule/{id}', [ServiceController::class, 'scheduleNextService'])->name('invoice.schedule');
        
        // History Service
        Route::get('/history', [ServiceController::class, 'historyIndex'])->name('history.index');
        Route::get('/history/data', [ServiceController::class, 'getHistoryData'])->name('history.data');
        Route::get('/history/export', [ServiceController::class, 'exportHistory'])->name('history.export');
        Route::get('/history/export-pdf', [ServiceController::class, 'exportHistoryPdf'])->name('history.export-pdf');
        Route::get('/status-counts', [ServiceController::class, 'getStatusCounts'])->name('status-counts');
        Route::get('/invoice/due-soon', [ServiceController::class, 'getDueSoonInvoices'])->name('invoice.due-soon');
        
        // Ongkir Service
        Route::get('/ongkir', [ServiceController::class, 'ongkirIndex'])->name('ongkir.index');
        Route::get('/ongkir/data', [ServiceController::class, 'getOngkirData'])->name('ongkir.data');
        Route::post('/ongkir', [ServiceController::class, 'storeOngkir'])->name('ongkir.store');
        Route::get('/ongkir/{id}', [ServiceController::class, 'getOngkir'])->name('ongkir.show');
        Route::put('/ongkir/{id}', [ServiceController::class, 'updateOngkir'])->name('ongkir.update');
        Route::delete('/ongkir/{id}', [ServiceController::class, 'deleteOngkir'])->name('ongkir.delete');
        
        // Mesin Customer
        Route::get('/mesin', [ServiceController::class, 'mesinIndex'])->name('mesin.index');
        Route::get('/mesin/data', [ServiceController::class, 'getMesinData'])->name('mesin.data');
        Route::post('/mesin', [ServiceController::class, 'storeMesin'])->name('mesin.store');
        Route::get('/mesin/{id}', [ServiceController::class, 'getMesin'])->name('mesin.show');
        Route::put('/mesin/{id}', [ServiceController::class, 'updateMesin'])->name('mesin.update');
        Route::delete('/mesin/{id}', [ServiceController::class, 'deleteMesin'])->name('mesin.delete');
        Route::get('/mesin/by-member/{id_member}', [ServiceController::class, 'getMesinByMember'])->name('mesin.by-member');
        Route::get('/search-customers', [ServiceController::class, 'searchCustomers'])->name('search-customers');
        Route::get('/mesin/produk/list', [ServiceController::class, 'getProdukList'])->name('mesin.produk');
    });

});

// ====== PENJUALAN (rute baru yang rapi) ======
    

    // ====== PEMBELIAN ======
    Route::prefix('pembelian')->name('pembelian.')->group(function () {
        
        // Purchase Order Routes
        Route::get('purchase-order/data', [PurchaseManagementController::class, 'purchaseOrderData'])->name('purchase-order.data');
        Route::get('purchase-order/generate-kode', [PurchaseManagementController::class, 'generatePOCode'])->name('purchase-order.generate-kode');
        Route::get('purchase-order/counts', [PurchaseManagementController::class, 'purchaseOrderStatusCounts'])->name('purchase-order.counts');
        Route::get('purchase-order/{id}/print', [PurchaseManagementController::class, 'purchaseOrderPrint'])->name('purchase-order.print');
        Route::get('purchase-order/{id}/print-document', [PurchaseManagementController::class, 'printDocument'])->name('purchase-order.print-document');
        Route::post('purchase-order/update-status/{id}', [PurchaseManagementController::class, 'updateStatus'])->name('purchase-order.update-status');
        Route::get('purchase-order/setting', [PurchaseManagementController::class, 'purchaseOrderSetting'])->name('purchase-order.setting');
        Route::post('purchase-order/setting/update', [PurchaseManagementController::class, 'updatePurchaseOrderSetting'])->name('purchase-order.setting.update');
        Route::get('purchase-order/export/pdf', [PurchaseManagementController::class, 'purchaseOrderExportPdf'])->name('purchase-order.export.pdf');
        Route::get('purchase-order/export/excel', [PurchaseManagementController::class, 'purchaseOrderExportExcel'])->name('purchase-order.export.excel');
        
        // Data Routes
        Route::get('suppliers', [PurchaseManagementController::class, 'getSuppliers'])->name('suppliers');
        Route::get('outlets', [PurchaseManagementController::class, 'getOutlets'])->name('outlets');
        Route::get('produk/pembelian', [PurchaseManagementController::class, 'getProdukPembelian'])->name('produk.pembelian');
        Route::get('bahan/pembelian', [PurchaseManagementController::class, 'getBahanPembelian'])->name('bahan.pembelian');
        
        // COA Setting Routes
        Route::get('coa-setting', [PurchaseManagementController::class, 'coaSettingPurchase'])->name('coa-setting');
        Route::post('coa-setting/update', [PurchaseManagementController::class, 'coaSettingPurchaseUpdate'])->name('coa-setting.update');
        Route::get('coa-setting/preview-journal', [PurchaseManagementController::class, 'previewCoaJournal'])->name('coa-setting.preview-journal');
        
        // Resource Routes
        Route::resource('purchase-order', PurchaseManagementController::class);
        
        // Halaman utama
        Route::view('/purchase-order', 'admin.pembelian.purchase-order.index')->name('purchase-order.index');

        // Supplier
        Route::get('supplier-management', [PurchaseManagementController::class, 'supplierManagement'])->name('supplier-management');
        Route::post('supplier/store', [PurchaseManagementController::class, 'supplierStore'])->name('supplier.store');
        Route::delete('supplier/{id}', [PurchaseManagementController::class, 'supplierDestroy'])->name('supplier.destroy');
        Route::get('supplier/{id}', [PurchaseManagementController::class, 'supplierShow'])->name('supplier.show');

        // Invoice Management Routes
        Route::get('purchase-invoice', [PurchaseManagementController::class, 'purchaseInvoiceIndex'])->name('purchase-invoice.index');
        Route::get('purchase-invoice/data', [PurchaseManagementController::class, 'purchaseInvoiceData'])->name('purchase-invoice.data');
        Route::get('purchase-order/invoice-data/{id}', [PurchaseManagementController::class, 'getPOForInvoice'])->name('purchase-order.invoice-data');
        Route::post('purchase-invoice/create', [PurchaseManagementController::class, 'createPurchaseInvoice'])->name('purchase-invoice.create');
        Route::get('purchase-invoice/preview', [PurchaseManagementController::class, 'invoicePreview'])->name('purchase-order.invoice-preview');
        Route::post('payment/upload-proof/{id}', [PurchaseManagementController::class, 'uploadPaymentProof'])->name('payment.upload-proof');
        Route::get('purchase-invoice/data', [PurchaseManagementController::class, 'purchaseInvoiceData'])->name('purchase-invoice.data');

        // PO Installment Payment Routes
        Route::post('purchase-order/payment', [PurchaseManagementController::class, 'processPayment'])->name('purchase-order.payment');
        Route::get('purchase-order/{id}/payment-history', [PurchaseManagementController::class, 'getPaymentHistory'])->name('purchase-order.payment-history');
        Route::get('purchase-order/payment/{id}/download-bukti', [PurchaseManagementController::class, 'downloadBuktiTransfer'])->name('purchase-order.download-bukti');

        // Dashboard Pembelian Routes
        Route::get('dashboard/data', [PurchaseManagementController::class, 'dashboard'])->name('dashboard.data');
        Route::get('dashboard/outlets', [PurchaseManagementController::class, 'getDashboardOutlets'])->name('dashboard.outlets');
        Route::view('/pembelian/dashboard', 'admin.pembelian.index')->name('dashboard');

        Route::post('payment/upload-proof/{id}', [PurchaseManagementController::class, 'uploadPaymentProof'])->name('payment.upload-proof');
        Route::get('payment/get-proof/{id}', [PurchaseManagementController::class, 'getPaymentProof'])->name('payment.get-proof');
        Route::get('payment/check-proof/{id}', [PurchaseManagementController::class, 'checkPaymentProof'])->name('payment.check-proof');
    });

// ====== FINANCE & ACCOUNTING (Outside Admin) ======
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('outlets', [FinanceAccountantController::class, 'getOutlets'])->name('outlets.data');
    
    // Existing chart of accounts routes...
    Route::get('chart-of-accounts/data', [FinanceAccountantController::class, 'chartOfAccountsData'])->name('chart-of-accounts.data');
    Route::get('chart-of-accounts/parents', [FinanceAccountantController::class, 'getParentAccounts'])->name('chart-of-accounts.parents');
    Route::get('chart-of-accounts/generate-code', [FinanceAccountantController::class, 'generateAccountCode'])->name('chart-of-accounts.generate-code');
    Route::post('chart-of-accounts', [FinanceAccountantController::class, 'storeAccount'])->name('chart-of-accounts.store');
    Route::put('chart-of-accounts/{id}', [FinanceAccountantController::class, 'updateAccount'])->name('chart-of-accounts.update');
    Route::post('chart-of-accounts/{id}/toggle', [FinanceAccountantController::class, 'toggleAccount'])->name('chart-of-accounts.toggle');
    Route::delete('chart-of-accounts/{id}', [FinanceAccountantController::class, 'deleteAccount'])->name('chart-of-accounts.delete');
    Route::get('chart-of-accounts/export', [FinanceAccountantController::class, 'exportAccounts'])->name('chart-of-accounts.export');
    Route::post('chart-of-accounts/import', [FinanceAccountantController::class, 'importAccounts'])->name('chart-of-accounts.import');
    Route::get('chart-of-accounts/{id}/balance-details', [FinanceAccountantController::class, 'getAccountBalanceDetails'])->name('chart-of-accounts.balance-details');
    
    Route::get('book-activity/data', [FinanceAccountantController::class, 'getBookActivityData'])->name('book-activity.data');
    Route::get('accounting-books/generate-code', [FinanceAccountantController::class, 'generateBookCode'])->name('accounting-books.generate-code');
    Route::get('accounting-books/data', [FinanceAccountantController::class, 'accountingBooksData'])->name('accounting-books.data');
    Route::get('accounting-books/{id}', [FinanceAccountantController::class, 'showBook'])->name('accounting-books.show');
    Route::get('buku/{id}', [FinanceAccountantController::class, 'bookDetailPage'])->name('buku.detail');
    Route::post('accounting-books/{id}/close', [FinanceAccountantController::class, 'closeBook'])->name('accounting-books.close');
    Route::get('accounting-books/{id}/report', [FinanceAccountantController::class, 'generateBookReport'])->name('accounting-books.report');
    Route::post('accounting-books', [FinanceAccountantController::class, 'storeBook'])->name('accounting-books.store');
    Route::put('accounting-books/{id}', [FinanceAccountantController::class, 'updateBook'])->name('accounting-books.update');
    Route::post('accounting-books/{id}/toggle', [FinanceAccountantController::class, 'toggleBook'])->name('accounting-books.toggle');
    Route::delete('accounting-books/{id}', [FinanceAccountantController::class, 'deleteBook'])->name('accounting-books.delete');
    
    // Journal Entries di Buku
    Route::get('journal-entries/{id}', [FinanceAccountantController::class, 'showJournalEntry'])->name('journal-entries.show');
    Route::get('journal-entries/{id}/edit', [FinanceAccountantController::class, 'editJournalEntry'])->name('journal-entries.edit');
    Route::put('journal-entries/{id}', [FinanceAccountantController::class, 'updateJournalEntry'])->name('journal-entries.update');
    Route::delete('journal-entries/{id}', [FinanceAccountantController::class, 'deleteJournalEntry'])->name('journal-entries.delete');
    Route::post('journal-entries', [FinanceAccountantController::class, 'storeJournalEntry'])->name('journal-entries.store');
    // Journal Routes
    Route::get('journals/data', [FinanceAccountantController::class, 'journalsData'])->name('journals.data');
    Route::get('journals/stats', [FinanceAccountantController::class, 'journalStats'])->name('journals.stats');
    Route::get('journals/{id}', [FinanceAccountantController::class, 'showJournal'])->name('journals.show');
    Route::post('journals', [FinanceAccountantController::class, 'storeJournal'])->name('journals.store');
    Route::put('journals/{id}', [FinanceAccountantController::class, 'updateJournal'])->name('journals.update');
    Route::post('journals/{id}/post', [FinanceAccountantController::class, 'postJournal'])->name('journals.post');
    Route::delete('journals/{id}', [FinanceAccountantController::class, 'deleteJournal'])->name('journals.delete');
    Route::delete('journals/{id}/superadmin', [FinanceAccountantController::class, 'deleteSuperadminJournal'])->name('journals.delete-superadmin');
    Route::get('active-books', [FinanceAccountantController::class, 'getActiveBooks'])->name('active-books.data');
    Route::get('search-accounts', [FinanceAccountantController::class, 'searchAccounts'])->name('accounts.search');
    Route::get('accounts/active', [FinanceAccountantController::class, 'getActiveAccounts'])->name('accounts.active');

    // General Ledger Routes
    Route::get('general-ledger/data', [FinanceAccountantController::class, 'generalLedgerData'])->name('general-ledger.data');
    Route::get('general-ledger/stats', [FinanceAccountantController::class, 'generalLedgerStats'])->name('general-ledger.stats');
    Route::get('general-ledger/accounts', [FinanceAccountantController::class, 'getActiveAccounts'])->name('general-ledger.accounts');
    Route::get('general-ledger/account-details', [FinanceAccountantController::class, 'getAccountTransactionDetailsForModal'])->name('general-ledger.account-details');
    
    // Trial Balance (Neraca Saldo)
    Route::get('trial-balance/data', [FinanceAccountantController::class, 'trialBalanceData'])->name('trial-balance.data');
    Route::get('trial-balance/export/pdf', [FinanceAccountantController::class, 'exportTrialBalancePDF'])->name('trial-balance.export.pdf');
    Route::get('trial-balance/export/excel', [FinanceAccountantController::class, 'exportTrialBalanceXLSX'])->name('trial-balance.export.excel');
    
    // Balance Sheet
    Route::get('balance-sheet/data', [FinanceAccountantController::class, 'balanceSheetData'])->name('balance-sheet.data');
    
    // Income Statement
    Route::get('income-statement/data', [FinanceAccountantController::class, 'incomeStatementData'])->name('income-statement.data');
    
    // Opening Balance Routes
    Route::get('opening-balance/data', [FinanceAccountantController::class, 'openingBalanceData'])->name('opening-balance.data');
    Route::get('opening-balance/accounts', [FinanceAccountantController::class, 'getAccountsForOpeningBalance'])->name('opening-balance.accounts');
    Route::post('opening-balance', [FinanceAccountantController::class, 'storeOpeningBalance'])->name('opening-balance.store');
    Route::put('opening-balance/{id}', [FinanceAccountantController::class, 'updateOpeningBalance'])->name('opening-balance.update');
    Route::delete('opening-balance/{id}', [FinanceAccountantController::class, 'deleteOpeningBalance'])->name('opening-balance.delete');
    Route::post('opening-balance/validate', [FinanceAccountantController::class, 'validateOpeningBalances'])->name('opening-balance.validate');
    Route::post('opening-balance/post', [FinanceAccountantController::class, 'postOpeningBalances'])->name('opening-balance.post');
    
    // Fixed Assets Routes
    Route::prefix('fixed-assets')->name('fixed-assets.')->group(function () {
        // Asset Management Routes
        Route::get('data', [FinanceAccountantController::class, 'fixedAssetsData'])->name('data');
        Route::post('/', [FinanceAccountantController::class, 'storeFixedAsset'])->name('store');
        
        // Export/Import Routes (must be before {id} routes)
        Route::get('export/xlsx', [FinanceAccountantController::class, 'exportFixedAssetsXLSX'])->name('export.xlsx');
        Route::get('export/pdf', [FinanceAccountantController::class, 'exportFixedAssetsPDF'])->name('export.pdf');
        Route::get('export', [FinanceAccountantController::class, 'exportFixedAssets'])->name('export');
        Route::post('import', [FinanceAccountantController::class, 'importFixedAssets'])->name('import');
        Route::get('template', [FinanceAccountantController::class, 'downloadFixedAssetsTemplate'])->name('template');
        
        // Statistics & Reporting Routes
        Route::get('stats', [FinanceAccountantController::class, 'fixedAssetsStats'])->name('stats');
        Route::get('chart/value', [FinanceAccountantController::class, 'assetValueChartData'])->name('chart.value');
        Route::get('chart/distribution', [FinanceAccountantController::class, 'assetDistributionData'])->name('chart.distribution');
        Route::get('generate/code', [FinanceAccountantController::class, 'generateAssetCode'])->name('generate-code');
        
        // Depreciation Routes
        Route::post('depreciation/calculate', [FinanceAccountantController::class, 'calculateDepreciation'])->name('depreciation.calculate');
        Route::post('depreciation/batch', [FinanceAccountantController::class, 'batchDepreciation'])->name('depreciation.batch');
        Route::post('depreciation/bulk-post', [FinanceAccountantController::class, 'bulkPostDepreciations'])->name('depreciation.bulk-post');
        Route::delete('depreciation/bulk-delete', [FinanceAccountantController::class, 'bulkDeleteDepreciations'])->name('depreciation.bulk-delete');
        Route::post('depreciation/{id}/post', [FinanceAccountantController::class, 'postDepreciation'])->name('depreciation.post');
        Route::post('depreciation/{id}/reverse', [FinanceAccountantController::class, 'reverseDepreciation'])->name('depreciation.reverse');
        Route::get('depreciation/history', [FinanceAccountantController::class, 'depreciationHistoryData'])->name('depreciation.history');
        Route::get('assets/all', [FinanceAccountantController::class, 'getAllFixedAssets'])->name('assets.all');
        
        // Asset CRUD Routes (must be last because of {id} parameter)
        Route::put('{id}', [FinanceAccountantController::class, 'updateFixedAsset'])->name('update');
        Route::delete('{id}', [FinanceAccountantController::class, 'deleteFixedAsset'])->name('delete');
        Route::patch('{id}/toggle', [FinanceAccountantController::class, 'toggleFixedAsset'])->name('toggle');
        Route::post('{id}/dispose', [FinanceAccountantController::class, 'disposeAsset'])->name('dispose');
        Route::post('{id}/approve', [FinanceAccountantController::class, 'approveFixedAsset'])->name('approve');
        Route::get('{id}', [FinanceAccountantController::class, 'showFixedAsset'])->name('show');
    });
    
    // Journal Export/Import/Print Routes
    Route::prefix('journals')->name('journals.')->group(function () {
        Route::get('export/xlsx', [FinanceAccountantController::class, 'exportJournalsXLSX'])->name('export.xlsx');
        Route::get('export/pdf', [FinanceAccountantController::class, 'exportJournalsPDF'])->name('export.pdf');
        Route::post('import', [FinanceAccountantController::class, 'importJournals'])->name('import');
        Route::get('template', [FinanceAccountantController::class, 'downloadJournalsTemplate'])->name('template');
    });
    
    // Accounting Book Export/Print Routes
    Route::prefix('accounting-books')->name('accounting-books.')->group(function () {
        Route::get('export/xlsx', [FinanceAccountantController::class, 'exportAccountingBooksXLSX'])->name('export.xlsx');
        Route::get('export/pdf', [FinanceAccountantController::class, 'exportAccountingBooksPDF'])->name('export.pdf');
    });
    
    // General Ledger Export/Print Routes
    Route::prefix('general-ledger')->name('general-ledger.')->group(function () {
        Route::get('export/xlsx', [FinanceAccountantController::class, 'exportGeneralLedgerXLSX'])->name('export.xlsx');
        Route::get('export/pdf', [FinanceAccountantController::class, 'exportGeneralLedgerPDF'])->name('export.pdf');
    });
    
    // Profit & Loss Routes (Laporan Laba Rugi)
    Route::get('profit-loss', [FinanceAccountantController::class, 'profitLossIndex'])->name('profit-loss.index');
    Route::get('laba-rugi', [FinanceAccountantController::class, 'profitLossIndex'])->name('laba-rugi.index'); // Alias
    Route::get('profit-loss/data', [FinanceAccountantController::class, 'profitLossData'])->name('profit-loss.data');
    Route::get('profit-loss/stats', [FinanceAccountantController::class, 'profitLossStats'])->name('profit-loss.stats');
    Route::get('profit-loss/account-details', [FinanceAccountantController::class, 'profitLossAccountDetails'])->name('profit-loss.account-details');
    Route::get('profit-loss/export/xlsx', [FinanceAccountantController::class, 'exportProfitLossXLSX'])->name('profit-loss.export.xlsx');
    Route::get('profit-loss/export/pdf', [FinanceAccountantController::class, 'exportProfitLossPDF'])->name('profit-loss.export.pdf');
    
    // Neraca (Balance Sheet) Routes
    Route::get('neraca/data', [FinanceAccountantController::class, 'neracaData'])->name('neraca.data');
    Route::get('neraca/account-details/{id}', [FinanceAccountantController::class, 'getNeracaAccountDetails'])->name('neraca.account-details');
    Route::get('neraca/export/pdf', [FinanceAccountantController::class, 'exportNeracaPDF'])->name('neraca.export.pdf');
    Route::get('neraca/export/xlsx', [FinanceAccountantController::class, 'exportNeracaXLSX'])->name('neraca.export.xlsx');
    
    // Dashboard Finance
    Route::get('/', [\App\Http\Controllers\FinanceDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [\App\Http\Controllers\FinanceDashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/export-pdf', [\App\Http\Controllers\FinanceDashboardController::class, 'exportPdf'])->name('dashboard.export-pdf');

    // Main Page
    Route::view('/accounting', 'admin.finance.accounting.index')->name('accounting.index');
    Route::view('/biaya', 'admin.finance.biaya.index')->name('biaya.index');
    Route::view('/akun', 'admin.finance.akun.index')->name('akun.index');
    Route::view('/buku', 'admin.finance.buku.index')->name('buku.index');
    Route::view('/saldo-awal', 'admin.finance.saldo-awal.index')->name('saldo-awal.index');
    Route::get('jurnal', [FinanceAccountantController::class, 'jurnalIndex'])->name('jurnal.index');
    Route::view('/aktiva-tetap', 'admin.finance.aktiva-tetap.index')->name('aktiva.index');
    Route::view('/buku-besar', 'admin.finance.buku-besar.index')->name('buku-besar.index');
    Route::get('cashflow', [\App\Http\Controllers\CashFlowController::class, 'index'])->name('cashflow.index');
    Route::get('arus-kas', [\App\Http\Controllers\CashFlowController::class, 'index'])->name('arus-kas.index'); // Alias
    Route::get('cashflow/data', [\App\Http\Controllers\CashFlowController::class, 'getData'])->name('cashflow.data');
    Route::get('cashflow/account-details/{id}', [\App\Http\Controllers\CashFlowController::class, 'getAccountDetails'])->name('cashflow.account-details');
    Route::get('cashflow/item-details', [\App\Http\Controllers\CashFlowController::class, 'getItemDetails'])->name('cashflow.item-details');
    Route::get('cashflow/fixed-asset-purchases', [\App\Http\Controllers\CashFlowController::class, 'getFixedAssetPurchases'])->name('cashflow.fixed-asset-purchases');
    Route::get('cashflow/export/pdf', [\App\Http\Controllers\CashFlowController::class, 'exportPDF'])->name('cashflow.export.pdf');
    Route::get('cashflow/export/xlsx', [\App\Http\Controllers\CashFlowController::class, 'exportXLSX'])->name('cashflow.export.xlsx');
    Route::view('/labarugi', 'admin.finance.labarugi.index')->name('labarugi.index');
    Route::get('neraca', [FinanceAccountantController::class, 'neracaIndex'])->name('neraca.index');
    Route::view('/neraca-saldo', 'admin.finance.neraca-saldo.index')->name('neraca-saldo.index');
    
    // Piutang Routes
    Route::get('piutang', [FinanceAccountantController::class, 'piutangIndex'])->name('piutang.index');
    Route::get('piutang/data', [FinanceAccountantController::class, 'getPiutangData'])->name('piutang.data');
    Route::get('piutang/{id}/detail', [FinanceAccountantController::class, 'getPiutangDetail'])->name('piutang.detail');
    Route::post('piutang/{id}/mark-paid', [FinanceAccountantController::class, 'markPiutangAsPaid'])->name('piutang.mark-paid');
    Route::post('piutang/pos/{id}/bayar', [FinanceAccountantController::class, 'payPosPiutang'])->name('piutang.pos.bayar');
    Route::get('piutang/get-sales-invoice-id/{penjualanId}', [FinanceAccountantController::class, 'getSalesInvoiceId'])->name('piutang.get-sales-invoice-id');
    Route::get('piutang/export/excel', [FinanceAccountantController::class, 'exportPiutangExcel'])->name('piutang.export.excel');
    Route::get('piutang/export/pdf', [FinanceAccountantController::class, 'exportPiutangPdf'])->name('piutang.export.pdf');
    
    // Hutang Routes
    Route::get('hutang', [FinanceAccountantController::class, 'hutangIndex'])->name('hutang.index');
    Route::get('hutang/data', [FinanceAccountantController::class, 'getHutangData'])->name('hutang.data');
    Route::get('hutang/{id}/detail', [FinanceAccountantController::class, 'getHutangDetail'])->name('hutang.detail');
    
    // Bank Reconciliation Routes
    Route::get('rekonsiliasi', [\App\Http\Controllers\BankReconciliationController::class, 'index'])->name('rekonsiliasi.index');
    Route::get('rekonsiliasi/data', [\App\Http\Controllers\BankReconciliationController::class, 'getData'])->name('rekonsiliasi.data');
    Route::get('rekonsiliasi/statistics', [\App\Http\Controllers\BankReconciliationController::class, 'getStatistics'])->name('rekonsiliasi.statistics');
    Route::get('rekonsiliasi/bank-accounts', [\App\Http\Controllers\BankReconciliationController::class, 'getBankAccounts'])->name('rekonsiliasi.bank-accounts');
    Route::get('rekonsiliasi/unreconciled-transactions', [\App\Http\Controllers\BankReconciliationController::class, 'getUnreconciledTransactions'])->name('rekonsiliasi.unreconciled-transactions');
    Route::post('rekonsiliasi', [\App\Http\Controllers\BankReconciliationController::class, 'store'])->name('rekonsiliasi.store');
    Route::get('rekonsiliasi/{id}', [\App\Http\Controllers\BankReconciliationController::class, 'show'])->name('rekonsiliasi.show');
    Route::put('rekonsiliasi/{id}', [\App\Http\Controllers\BankReconciliationController::class, 'update'])->name('rekonsiliasi.update');
    Route::post('rekonsiliasi/{id}/complete', [\App\Http\Controllers\BankReconciliationController::class, 'complete'])->name('rekonsiliasi.complete');
    Route::post('rekonsiliasi/{id}/approve', [\App\Http\Controllers\BankReconciliationController::class, 'approve'])->name('rekonsiliasi.approve');
    Route::delete('rekonsiliasi/{id}', [\App\Http\Controllers\BankReconciliationController::class, 'destroy'])->name('rekonsiliasi.destroy');
    Route::get('rekonsiliasi/{id}/export-pdf', [\App\Http\Controllers\BankReconciliationController::class, 'exportPdf'])->name('rekonsiliasi.export-pdf');
});

Route::prefix('investor')->name('investor.')->middleware('web')->group(function () {
    Route::get('/login', [InvestorAuthController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [InvestorAuthController::class, 'login'])->name('login.submit');
            Route::get('/register', [InvestorAuthController::class, 'showRegistrationForm'])->name('register');
            Route::post('/register', [InvestorAuthController::class, 'register'])->name('register.submit');
            Route::get('/forgot-password', [InvestorAuthController::class, 'showForgotPasswordForm'])->name('password.request');
            Route::post('/forgot-password', [InvestorAuthController::class, 'sendResetLinkEmail'])->name('password.email');
            Route::get('/reset-password/{token}', [InvestorAuthController::class, 'showResetPasswordForm'])->name('password.reset');
            Route::post('/reset-password', [InvestorAuthController::class, 'resetPassword'])->name('password.update');

        // Auth Routes
        Route::post('/logout', [InvestorAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [InvestorAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/activities', [InvestorAuthController::class, 'activities'])->name('activities');

        // Investasi
        Route::get('/investasi', [InvestorAuthController::class, 'investments'])->name('investments');
        Route::get('/investasi/{id}', [InvestorAuthController::class, 'investmentDetail'])->name('investments.show');
        Route::get('/investasi/tambah', [InvestorAuthController::class, 'showAddInvestmentForm'])->name('investments.create');
        Route::post('/investasi', [InvestorAuthController::class, 'addInvestment'])->name('investments.store');

        // Bagi Hasil
        Route::get('/bagi-hasil/{id}', [InvestorAuthController::class, 'profitShareDetail'])->name('profits.show');
        Route::get('/bagi-hasil', [InvestorAuthController::class, 'profits'])->name('profits');

        // Dokumen
        Route::get('/dokumen', [InvestorAuthController::class, 'documents'])->name('documents');
        Route::get('/dokumen/{id}', [InvestorAuthController::class, 'downloadDocument'])->name('documents.download');
        Route::get('documents/view/{id}', [InvestorAuthController::class, 'viewDocument'])->name('documents.view');

        // Profil
        Route::get('/profil/edit', [InvestorAuthController::class, 'showEditProfileForm'])->name('profile.edit');
        Route::get('/profil/ganti-password', [InvestorAuthController::class, 'showChangePasswordForm'])->name('password.change');
        Route::post('/profil/ganti-password', [InvestorAuthController::class, 'changePassword'])->name('password.change.submit');
        Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profil/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');

        Route::post('/withdrawals', [InvestorAuthController::class, 'storeWithdrawal'])->name('withdrawals.store');
        Route::get('/investasi/{id}/history', [InvestorAuthController::class, 'downloadHistory'])->name('accounts.history');
        Route::get('/withdrawals', [InvestorAuthController::class, 'withdrawals'])->name('withdrawals');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['web', 'auth'])->group(function() {
    

    Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::resource('/supplier', SupplierController::class);

    Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
    Route::get('/pembelian/{id}/{id_outlet_selected}/create', [PembelianController::class, 'create'])->name('pembelian.create');
    Route::get('/pembelian/selesai', [PembelianController::class, 'selesai'])->name('pembelian.selesai');
    Route::get('/pembelian/nota-pembelian', [PembelianController::class, 'notaPembelian'])->name('pembelian.nota_pembelian');
    Route::resource('/pembelian', PembelianController::class)
        ->except(['create']);

    Route::get('/pembelian_detail/{id}/data', [PembelianDetailController::class, 'data'])->name('pembelian_detail.data');
    Route::get('/pembelian_detail/{id}', [PembelianDetailController::class, 'getHargaBeli'])->name('getHargaBeli');
    Route::post('/pembelian_detail/simpan-harga-bahan', [PembelianDetailController::class, 'simpanHargaBahan'])->name('simpanHargaBahan');
    Route::get('/pembelian_detail/loadform/{diskon}/{total}/{isChecked}/{isBayarHutang}/{hutang}', [PembelianDetailController::class, 'loadForm'])->name('pembelian_detail.load_form');
    Route::post('/pembelian_detail/update-hutang', [PembelianDetailController::class, 'updateHutang'])->name('pembelian_detail.updateHutang');
    Route::post('/pembelian_detail/update-jumlah', [PembelianDetailController::class, 'updateJumlah'])->name('pembelian_detail.updateJumlah');
    Route::put('/pembelian_detail/bahan_harga/{id}', [PembelianDetailController::class, 'updateHarga'])->name('pembelian_detail.update_harga');
    Route::get('/pembelian_detail/bahan/{id}/edit-harga', [PembelianDetailController::class, 'editHarga'])->name('pembelian_detail.edit_harga');
    Route::delete('/pembelian_detail/bahan/{id}/destroy-harga', [PembelianDetailController::class, 'destroyHarga'])->name('pembelian_detail.destroy_harga');
    Route::resource('/pembelian_detail', PembelianDetailController::class)
        ->except('create', 'show', 'edit');

    

    Route::post('rab_template/{id}/set-default', [RabTemplateController::class, 'setDefault'])->name('rab_template.set_default');
    Route::post('rab_template/{id}/update-approval', [RabTemplateController::class, 'updateApproval'])->name('rab_template.update_approval');
    Route::get('rab_template/{id}/approval', [RabTemplateController::class, 'showApproval']) ->name('rab_template.show_approval');
    Route::get('/rab_template/list', [RabTemplateController::class, 'list'])->name('rab_template.list');
    Route::resource('rab_template', RabTemplateController::class);
    Route::get('rab_template/history/{id}', [RabTemplateController::class, 'getHistory'])->name('rab_template.history');
    Route::post('rab_template/{id}/add-realisasi', [RabTemplateController::class, 'addRealisasi'])->name('rab_template.add_realisasi');
    Route::delete('rab_template/history/{id}', [RabTemplateController::class, 'deleteHistory'])->name('rab_template.delete_history');
    Route::delete('rab_template/history/reset/{detailId}', [RabTemplateController::class, 'resetHistory'])->name('rab_template.reset_history');
    Route::get('rab_template/history/sum/{detailId}', [RabTemplateController::class, 'getHistorySum'])->name('rab_template.history_sum');

    // Route untuk Produksi
    Route::get('/produksi/data', [ProduksiController::class, 'data'])->name('produksi.data');
    Route::get('/produksi/create', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::post('/produksi/get-harga-fifo', [ProduksiController::class, 'getHargaFifo'])->name('produksi.getHargaFifo');
    Route::get('/produksi/laporan', [ProduksiController::class, 'showLaporanForm'])->name('produksi.laporan.form');
    Route::get('/produksi/generate-laporan', [ProduksiController::class, 'generateLaporan'])->name('produksi.generateLaporan');
    Route::get('/produksi/download-laporan', [ProduksiController::class, 'downloadLaporan'])->name('produksi.downloadLaporan');
    Route::get('/produksi/dashboard-data', [ProduksiController::class, 'getDashboardDataAjax'])->name('produksi.getDashboardData');
    Route::resource('/produksi', ProduksiController::class)->except(['create']);

    // Route untuk ProduksiDetail
    Route::get('/produksi_detail/{id}/data', [ProduksiDetailController::class, 'data'])->name('produksi_detail.data');
    Route::resource('/produksi_detail', ProduksiDetailController::class)->except(['create', 'show', 'edit']);
    
    Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
    Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
    Route::delete('member/delete-selected', [MemberController::class, 'deleteSelected'])->name('member.delete_selected');
    Route::resource('/member', MemberController::class);

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
//---
        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar/{isChecked}', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');
       Route::post('penjualan/cetak', [PenjualanController::class, 'cetakPost'])->name('penjualan.cetak.post');
       Route::get('penjualan/cetak-sederhana', [PenjualanController::class, 'cetakSederhana'])->name('penjualan.cetak.sederhana');
Route::post('penjualan/cetak-sederhana', [PenjualanController::class, 'cetakSederhanaPost'])->name('penjualan.cetak.sederhana.post');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}/{piutang}/{isChecked}/{isCheckedIngatkan}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::post('/transaksi/update-piutang', [PenjualanDetailController::class, 'updatePiutang'])->name('transaksi.updatePiutang');
        Route::get('/getDiscount', [PenjualanDetailController::class, 'getDiscount'])->name('getDiscount');
        Route::post('/hapus-produk-terpilih', [PenjualanDetailController::class, 'hapusProdukTerpilih'])->name('hapus.produk');
        Route::get('/transaksi/{id}', [PenjualanDetailController::class, 'getHPP'])->name('getHPP');
        Route::get('/transaksi/get-hpp-fifo/{id_produk}/{jumlah}', [PenjualanDetailController::class, 'getHppFifo'])->name('transaksi.getHppFifo');
        Route::post('/transaksi/kembalikan-stok', [PenjualanDetailController::class, 'kembalikanStok'])->name('transaksi.kembalikanStok');
        Route::post('/transaksi/update-jumlah', [PenjualanDetailController::class, 'updateJumlah'])->name('transaksi.updateJumlah');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}/{totalPenjualan}/{totalPembelian}/{totalPengeluaran}/{totalPendapatan}/{totalHutang}/{totalPiutang}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        // Route::get('/laporan-hutang-piutang', [LaporanHutangPiutangController::class, 'index'])->name('laporan_hutang_piutang.index');
        // Route::get('/laporan-hutang-piutang/data/{awal}/{akhir}', [LaporanHutangPiutangController::class, 'data'])->name('laporan_hutang_piutang.data');
        // Route::get('/laporan-hutang-piutang/pdf/{awal}/{akhir}/{totalHutang}/{totalPiutang}', [LaporanHutangPiutangController::class, 'exportPDF'])->name('laporan_hutang_piutang.export_pdf');

        Route::get('/hutang', [HutangController::class, 'index'])->name('hutang.index');
        Route::get('/hutang/data', [HutangController::class, 'data'])->name('hutang.data');
        Route::delete('/hutang/{id}', [HutangController::class, 'destroy'])->name('hutang.destroy');

        Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
        Route::get('/piutang/data', [PiutangController::class, 'data'])->name('piutang.data');
        Route::delete('/piutang/{id}', [PiutangController::class, 'destroy'])->name('piutang.destroy');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route untuk menampilkan halaman tipe
// Route::get('/tipe', [TipeController::class, 'index'])->name('tipe.index');
// Route::post('/tipe', [TipeController::class, 'store'])->name('tipe.store');
// Route::get('/tipe/{id}/edit', [TipeController::class, 'edit'])->name('tipe.edit');
// Route::put('/tipe/{id}', [TipeController::class, 'update'])->name('tipe.update');
// Route::delete('/tipe/{id}', [TipeController::class, 'destroy'])->name('tipe.destroy');
Route::get('/tipe/data', [TipeController::class, 'data'])->name('tipe.data');
Route::resource('tipe', TipeController::class);

    Route::get('/laporan_penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan_penjualan.index');
    Route::get('/laporan_penjualan/data/{awal}/{akhir}', [LaporanPenjualanController::class, 'data'])->name('laporan_penjualan.data');
    Route::get('/laporan_penjualan/export_pdf/{awal}/{akhir}/{totalHPP}/{totalHargaJual}/{totalJumlah}/{totalProfit}', [LaporanPenjualanController::class, 'exportPDF'])->name('laporan_penjualan.export_pdf');
    Route::delete('laporan_penjualan/delete-selected', [LaporanPenjualanController::class, 'deleteSelected'])->name('laporan_penjualan.delete_selected');

    Route::prefix('inventori')->group(function () {
        Route::get('/', [InventoriController::class, 'index'])->name('inventori.index');
        Route::get('/data', [InventoriController::class, 'data'])->name('inventori.data');
        Route::post('/', [InventoriController::class, 'store'])->name('inventori.store');
        Route::get('/{id}', [InventoriController::class, 'show'])->name('inventori.show');
        Route::get('/{id}/edit', [InventoriController::class, 'edit'])->name('inventori.edit');
        Route::put('/{id}', [InventoriController::class, 'update'])->name('inventori.update');
        Route::delete('/{id}', [InventoriController::class, 'destroy'])->name('inventori.destroy');
        Route::get('/cetak-laporan', [InventoriController::class, 'cetakLaporan'])->name('inventori.cetak_laporan');
    });
    Route::get('/inventori/{id}/detail', [InventoriController::class, 'getDetail'])->name('inventori.detail');
    Route::post('/inventori/{id}/pinjam', [InventoriController::class, 'pinjamBarang'])->name('pinjam_barang');

    Route::get('/kontra_bon/selesai/{id}', [KontraBonController::class, 'selesai'])->name('kontra_bon.selesai');
    Route::get('/kontra_bon/nota-besar/{id}', [KontraBonController::class, 'notaBesar'])->name('kontra_bon.nota_besar');
    Route::get('/kontra_bon/data', [KontraBonController::class, 'data'])->name('kontra_bon.data');
    Route::get('/kontra_bon/data_kontra_bon', [KontraBonController::class, 'dataKontraBon'])->name('kontra_bon.data_kontra_bon');
    Route::get('/get-piutang/{id_member}', [KontraBonController::class, 'getPiutang']);
    Route::post('/kontra_bon/store', [KontraBonController::class, 'store'])->name('kontra_bon.store');
    Route::resource('kontra_bon', KontraBonController::class)
        ->except('store');
    
    Route::prefix('manajemen-gudang')->group(function () {
        Route::get('/', [PermintaanPengirimanController::class, 'index'])->name('manajemen-gudang.index');
        Route::get('/get-items', [PermintaanPengirimanController::class, 'getItems'])->name('manajemen-gudang.get-items');
        Route::post('/buat-permintaan', [PermintaanPengirimanController::class, 'buatPermintaan'])->name('manajemen-gudang.buat-permintaan');
        Route::post('/setujui-permintaan/{id}', [PermintaanPengirimanController::class, 'setujuiPermintaan'])->name('manajemen-gudang.setujui-permintaan');
        Route::post('/tolak-permintaan/{id}', [PermintaanPengirimanController::class, 'tolakPermintaan'])->name('manajemen-gudang.tolak-permintaan');
        Route::get('/daftar-permintaan', [PermintaanPengirimanController::class, 'daftarPermintaan'])->name('manajemen-gudang.daftar-permintaan');
    });

    // Route::get('/partials/sidebar/{menu}', function ($menu) {
    //     return view("partials.sidebar.{$menu}");
    // })->name('sidebar.submenu');

    // routes/web.php
Route::get('/partials/sidebar/{menu}', function ($menu) {
    return view("partials.sidebar.{$menu}");
})->where('menu', '[a-z-]+');

    Route::prefix('hrm')->name('hrm.')->group(function () {
        Route::resource('recruitment', RecruitmentController::class);
        Route::resource('payroll', PayrollController::class);
        Route::resource('performance', PerformanceController::class);
        Route::resource('training', TrainingController::class);
        Route::resource('attendance', AttendanceController::class);
    });
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/hrm/recruitment/{id}/print-contract', [RecruitmentController::class, 'printContract'])->name('hrm.recruitment.print-contract');
    Route::get('/hrm/payroll/{id}/print', [PayrollController::class, 'print'])->name('hrm.payroll.print');
    Route::get('/payroll/export-pdf', [PayrollController::class, 'exportPdf'])->name('hrm.payroll.export_pdf');
    Route::post('/hrm/payroll/{payroll}/finalize', [PayrollController::class, 'finalize'])->name('hrm.payroll.finalize');
    Route::get('/performance/export-pdf', [PerformanceController::class, 'exportPdf'])->name('hrm.performance.export_pdf');
    Route::get('/hrm/training/print/{id}', [TrainingController::class, 'print'])->name('hrm.training.print');

    Route::get('/hrm/attendance/get-weeks-in-month', [AttendanceController::class, 'getWeeksInMonth'])
    ->name('hrm.attendance.getWeeksInMonth');
    Route::post('/attendance/set-work-hours', [AttendanceController::class, 'setWorkHours'])
    ->name('hrm.attendance.set-work-hours');
    Route::get('/work-schedule/{employeeId}', function ($employeeId) {
        $schedule = App\Models\WorkSchedule::where('recruitment_id', $employeeId)->first();
        return response()->json($schedule);
    });

    Route::prefix('financial/book')->name('financial.book.')->group(function() {
        Route::get('/create', [BookController::class, 'createBook'])->name('create');
        Route::post('/store', [BookController::class, 'storeBook'])->name('store');
        Route::get('/list', [BookController::class, 'listBooks'])->name('list');
        Route::delete('/delete/{id}', [BookController::class, 'deleteBook'])->name('delete');
        Route::get('/opening-balances/{bookId}', [BookController::class, 'openingBalances'])->name('opening_balances');
        Route::post('/update-balances/{bookId}', [BookController::class, 'updateOpeningBalances'])->name('update_balances');
        Route::get('/sub-classes', [BookController::class, 'subClasses'])->name('sub_classes');
    Route::get('/sub-classes/create', [BookController::class, 'createSubClass'])->name('create_sub_class');
    Route::post('/sub-classes/store', [BookController::class, 'storeSubClass'])->name('store_sub_class');
    Route::get('/sub-classes/edit/{id}', [BookController::class, 'editSubClass'])->name('edit_sub_class');
    Route::put('/sub-classes/update/{id}', [BookController::class, 'updateSubClass'])->name('update_sub_class');
    Route::delete('/sub-classes/delete/{id}', [BookController::class, 'deleteSubClass'])->name('delete_sub_class');
        Route::get('/report-formats', [BookController::class, 'reportFormats'])->name('report_formats');
        Route::post('/close/{bookId}', [BookController::class, 'closeBook'])->name('close');
        Route::get('/backup/{bookId}', [BookController::class, 'backupData'])->name('backup');
        Route::get('/close-confirmation/{bookId}', [BookController::class, 'closeConfirmation'])->name('close_confirmation');
        Route::get('/edit/{id}', [BookController::class, 'editBook'])->name('edit');
        Route::put('/update/{id}', [BookController::class, 'updateBook'])->name('update');
        Route::get('/accounts', [BookController::class, 'accountList'])->name('accounts');
        Route::post('/accounts/store', [BookController::class, 'storeAccount'])->name('store_account');
        Route::delete('/accounts/delete/{code}', [BookController::class, 'deleteAccount'])->name('delete_account');
        Route::get('/generate-code', [BookController::class, 'generateCode'])->name('generate_code');
    });

    Route::prefix('financial/journal')->name('financial.journal.')->group(function() {
        Route::get('/', [JournalController::class, 'index'])->name('index');
        Route::get('/{journal}/edit', [JournalController::class, 'edit'])->name('edit');
        Route::post('/', [JournalController::class, 'store'])->name('store');
        Route::put('/{journal}', [JournalController::class, 'update'])->name('update');
        Route::post('/{journal}/validate', [JournalController::class, 'validateJournal'])
            ->name('validate')
            ->middleware('auth');
        Route::delete('/{journal}', [JournalController::class, 'destroy'])->name('destroy');
        Route::delete('/', [JournalController::class, 'destroyAll'])->name('destroy.all');
        Route::post('/delete-selected', [JournalController::class, 'deleteSelected'])->name('delete-selected');
        Route::get('/account-balance', [JournalController::class, 'getAccountBalance'])->name('account.balance');
        Route::get('/generate-reference', [JournalController::class, 'generateReference'])->name('generate-reference');
        Route::get('/search-accounts', [JournalController::class, 'searchAccounts'])->name('search-accounts');
        Route::post('/validate-selected', [JournalController::class, 'validateSelected'])
            ->name('validate-selected');
    });

    Route::prefix('financial/fixed-asset')->name('financial.fixed-asset.')->group(function() {
        Route::get('/', [FixedAssetController::class, 'index'])->name('index');
        Route::get('/{fixedAsset}/edit', [FixedAssetController::class, 'edit'])->name('edit');
        Route::post('/', [FixedAssetController::class, 'store'])->name('store');
        Route::put('/{fixedAsset}', [FixedAssetController::class, 'update'])->name('update');
        Route::delete('/{fixedAsset}', [FixedAssetController::class, 'destroy'])->name('destroy');
        Route::post('/delete-selected', [FixedAssetController::class, 'deleteSelected'])->name('delete-selected');
        Route::post('/{fixedAsset}/generate-depreciation', [FixedAssetController::class, 'generateDepreciation'])->name('generate-depreciation');
        Route::get('/generate-code', [FixedAssetController::class, 'generateCode'])->name('generate-code');
        Route::post('/delete-selected', [FixedAssetController::class, 'deleteSelected'])->name('delete-selected');
    });

    Route::prefix('financial/annual-tax-report')->name('financial.annual-tax-report.')->group(function() {
        Route::get('/', [AnnualTaxReportController::class, 'index'])->name('index');
        Route::get('/create', [AnnualTaxReportController::class, 'create'])->name('create');
        Route::post('/', [AnnualTaxReportController::class, 'store'])->name('store');
        Route::get('/{annualTaxReport}', [AnnualTaxReportController::class, 'show'])->name('show');
        Route::get('/{annualTaxReport}/edit', [AnnualTaxReportController::class, 'edit'])->name('edit');
        Route::put('/{annualTaxReport}', [AnnualTaxReportController::class, 'update'])->name('update');
        Route::delete('/{annualTaxReport}', [AnnualTaxReportController::class, 'destroy'])->name('destroy');
        
        // Additional routes
        Route::get('/{annualTaxReport}/download', [AnnualTaxReportController::class, 'download'])->name('download');
        Route::post('/{annualTaxReport}/submit', [AnnualTaxReportController::class, 'submit'])->name('submit');
    });

    Route::prefix('financial/ledger')->name('financial.ledger.')->group(function() {
        Route::get('/', [LedgerController::class, 'index'])->name('index');
        Route::post('/export', [LedgerController::class, 'export'])->name('export');
        Route::post('/export-all', [LedgerController::class, 'exportAll'])->name('export.all');
    });

    Route::prefix('financial/worksheet')->name('financial.worksheet.')->group(function() {
        Route::get('/', [WorksheetController::class, 'index'])->name('index');
        Route::post('/export', [WorksheetController::class, 'export'])->name('export');
    });

    Route::prefix('financial/profit-loss')->name('financial.profit-loss.')->group(function() {
        Route::get('/', [ProfitLossController::class, 'index'])->name('index');
        Route::post('/export', [ProfitLossController::class, 'export'])->name('export');
    });

    Route::prefix('financial/equity-change')->name('financial.equity-change.')->group(function() {
        Route::get('/', [EquityChangeController::class, 'index'])->name('index');
        Route::post('/export', [EquityChangeController::class, 'export'])->name('export');
    });

    Route::prefix('financial/balance-sheet')->name('financial.balance-sheet.')->group(function() {
        Route::get('/', [BalanceSheetController::class, 'index'])->name('index');
        Route::post('/export', [BalanceSheetController::class, 'export'])->name('export');
    });

    Route::prefix('financial/cash-flow')->name('financial.cash-flow.')->group(function() {
        Route::get('/', [CashFlowController::class, 'index'])->name('index');
        Route::post('/export', [CashFlowController::class, 'export'])->name('export');
    });

    // Route untuk tampilan detail
    Route::get('/penjualan/detail_ledger/{id}', [PenjualanController::class, 'showDetailLedger'])->name('penjualan.detail_ledger');
    Route::get('/pembelian/detail_ledger/{id}', [PembelianController::class, 'showDetailLedger'])->name('pembelian.detail_ledger');
    Route::get('/hrm/payroll/detail_ledger/{id}', [PayrollController::class, 'showDetailLedger'])->name('hrm.payroll.detail_ledger');
    Route::get('/financial/journal/detail_ledger/{id}', [AccountingController::class, 'showJournalDetail'])->name('financial.journal.detail_ledger');
    
    Route::prefix('crm')->group(function () {
        // Prospek Management
        Route::get('/prospek', [ProspekController::class, 'index'])->name('prospek.index');
        Route::get('/prospek/create', [ProspekController::class, 'create'])->name('prospek.create');
        Route::post('/prospek', [ProspekController::class, 'store'])->name('prospek.store');
        Route::get('/prospek/{id}/edit', [ProspekController::class, 'edit'])->name('prospek.edit');
        Route::put('/prospek/{id}', [ProspekController::class, 'update'])->name('prospek.update');
        Route::delete('/prospek/{id}', [ProspekController::class, 'destroy'])->name('prospek.destroy');
        Route::get('/prospek/map', [ProspekController::class, 'showMap'])->name('prospek.map');
        
        // Timeline Management
        Route::post('/prospek/{id}/timeline', [ProspekController::class, 'addTimeline'])->name('prospek.timeline.store');
        Route::delete('/prospek/timeline/{id}', [ProspekController::class, 'removeTimeline'])->name('prospek.timeline.destroy');
        Route::get('/prospek/{id}/timeline', function($id) {
            $prospek = Prospek::findOrFail($id);
            $timelines = $prospek->timeline()->orderBy('tanggal', 'desc')->get();
            
            return response()->json($timelines);
        })->name('prospek.timeline.json');
        
        // Settings
        Route::get('/prospek/settings', [ProspekSettingController::class, 'index'])->name('prospek.settings.index');
        Route::post('/prospek/settings', [ProspekSettingController::class, 'update'])->name('prospek.settings.update');

        Route::get('/prospek/export-template', [ProspekController::class, 'exportTemplate'])->name('prospek.export-template');
        Route::post('/prospek/import', [ProspekController::class, 'import'])->name('prospek.import');
        Route::post('/prospek/{id}/upload-photo', [ProspekController::class, 'uploadPhoto'])->name('prospek.uploadPhoto');
        Route::delete('/prospek/{id}/delete-photo', [ProspekController::class, 'deletePhoto'])->name('prospek.deletePhoto');
    });

    Route::prefix('irp/investor')->name('irp.investor.')->group(function() {
        Route::get('/', [InvestorController::class, 'index'])->name('index');
        Route::get('/create', [InvestorController::class, 'create'])->name('create');
        Route::post('/', [InvestorController::class, 'store'])->name('store');
        Route::get('/{investor}', [InvestorController::class, 'show'])->name('show');
        Route::get('/{investor}/edit', [InvestorController::class, 'edit'])->name('edit');
        Route::put('/{investor}', [InvestorController::class, 'update'])->name('update');
        Route::delete('/{investor}', [InvestorController::class, 'destroy'])->name('destroy');

        Route::prefix('{investor}/accounts')->name('account.')->group(function() {
            Route::get('/', [InvestorAccountController::class, 'index'])->name('index');
            Route::post('/', [InvestorAccountController::class, 'store'])->name('store');
            Route::get('/{account}', [InvestorAccountController::class, 'show'])->name('show');
            Route::get('/{account}/edit', [InvestorAccountController::class, 'edit'])->name('edit');
            Route::put('/{account}', [InvestorAccountController::class, 'update'])->name('update');
            Route::delete('/{account}', [InvestorAccountController::class, 'destroy'])->name('destroy');
        });
        
    });

    Route::prefix('irp/investor/{investor}/documents')->name('irp.investor.document.')->group(function() {
        Route::post('/', [InvestorDocumentController::class, 'store'])->name('store');
        Route::post('/custom', [InvestorDocumentController::class, 'createCustom'])->name('create.custom');
        Route::get('/{document}', [InvestorDocumentController::class, 'show'])->name('show');
        Route::delete('/{document}', [InvestorDocumentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('irp/investor/{investor}/accounts/{account}/investments')->name('irp.investor.account.investment.')->group(function() {
        Route::get('/{investment}/edit', [AccountInvestmentController::class, 'edit'])->name('edit');
        Route::post('/', [AccountInvestmentController::class, 'store'])->name('store');
        Route::put('/{investment}', [AccountInvestmentController::class, 'update'])->name('update');
        Route::delete('/{investment}', [AccountInvestmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('irp/investor/{investor}/customers')->name('irp.investor.customer.')->group(function() {
        Route::get('/', [InvestorCustomerController::class, 'index'])->name('index');
        Route::post('/', [InvestorCustomerController::class, 'store'])->name('store');
        Route::post('/{customer}/verify', [InvestorCustomerController::class, 'verifyPayment'])->name('verify');
        Route::delete('/{customer}', [InvestorCustomerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('irp/profit-management')->name('irp.profit-management.')->group(function() {
        Route::get('/', [ProfitManagementController::class, 'index'])->name('index');
        Route::get('/create', [ProfitManagementController::class, 'create'])->name('create');
        Route::post('/', [ProfitManagementController::class, 'store'])->name('store');
        Route::get('/{profit}', [ProfitManagementController::class, 'show'])->name('show');
        Route::post('/{profit}/confirm-payment', [ProfitManagementController::class, 'confirmPayment'])
            ->name('confirm-payment');
        Route::put('/{profit}/update-distribution', [ProfitManagementController::class, 'updateDistribution'])
            ->name('update-distribution');
        Route::post('/groups', [ProfitManagementController::class, 'storeGroup'])->name('store-group');
        Route::get('/groups/{group}/edit', [ProfitManagementController::class, 'edit'])->name('edit-group');
        Route::put('/groups/{id}', [ProfitManagementController::class, 'updateGroup'])->name('update-group');
        Route::delete('/groups/{group}', [ProfitManagementController::class, 'destroy'])->name('destroy-group');
        // Route untuk kelompok bagi hasil
        Route::get('/groups/{group}', [ProfitManagementController::class, 'showGroup'])->name('show-group');
        Route::put('/groups/{group}/update-distribution', [ProfitManagementController::class, 'updateDistributionGroup'])->name('update-distribution-group');
        Route::post('/groups/{group}/confirm-payment', [ProfitManagementController::class, 'confirmPaymentGroup'])->name('confirm-payment-group');
        // History routes
        Route::get('/history/{id}', [ProfitManagementController::class, 'showGroupHistory'])->name('show-group-history');
        Route::delete('/history/{id}/cancel', [ProfitManagementController::class, 'cancelPaymentGroup'])->name('cancel-payment-group');
    });
    Route::post('/investor/{investor}/profitsss', [ProfitManagementController::class, 'storeForInvestor'])->name('irp.investor.profit.store');
    Route::put('/profit-management/{profit}/update-category', [ProfitManagementController::class, 'updateCategory'])->name('irp.profit-management.update-category');
    Route::delete('/profit-management/history/{id}', [ProfitManagementController::class, 'deleteHistory'])->name('irp.profit-management.delete-history');

    Route::prefix('irp/manajemen-pencairan')->group(function() {
        Route::get('/', [WithdrawalManagementController::class, 'index'])->name('irp.withdrawal-management.index');
        Route::post('/approve/{id}', [WithdrawalManagementController::class, 'approve'])->name('irp.withdrawal-management.approve');
        Route::post('/reject/{id}', [WithdrawalManagementController::class, 'reject'])->name('irp.withdrawal-management.reject');
    });
   
    Route::post('withdrawal-management/approve-investment/{id}', [WithdrawalManagementController::class, 'approveInvestment'])
    ->name('irp.withdrawal-management.approve-investment');
});

// Untuk mengakses file storage
Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where('filename', '.*');

Route::post('/prospek/search-and-save', [ProspekController::class, 'searchAndSaveLocation'])->name('prospek.searchAndSave');
Route::get('/prospek/getLocations', [ProspekController::class, 'getLocations'])->name('prospek.getLocations');
Route::delete('/map-umum/{id}', [ProspekController::class, 'deleteMapUmumLocation']);

Route::get('/jemaah/{id}', [JemaahController::class, 'show'])->name('jemaah.show');
// Removed duplicate: Route::get('/api/products', [ProdukController::class, 'apiIndex'])->name('api.products');
// The correct route is defined earlier in the file for public booking equipment selection
Route::get('/api/products/{id}', [ProdukController::class, 'apiShow'])->name('api.products.show');
Route::post('/jemaah/checkout', [JemaahController::class, 'checkout'])->name('jemaah.checkout');
Route::put('/jemaah/{id}/identitas', [JemaahController::class, 'updateIdentitas'])->name('jemaah.updateIdentitas');
Route::post('/jemaah/process-ktp', [JemaahController::class, 'processKtp'])->name('jemaah.processKtp');
Route::put('/jemaah/{id}/keluarga', [JemaahController::class, 'updateKeluarga'])->name('jemaah.updateKeluarga');

Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
    Route::resource('/kategori', KategoriController::class); 

    Route::get('/satuan/data', [SatuanController::class, 'data'])->name('satuan.data');
    Route::resource('/satuan', SatuanController::class); 



    Route::get('outlet/data', [OutletController::class, 'data'])->name('outlet.data');
    Route::resource('outlet', OutletController::class);

Route::get('agen_gerobak/data', [AgenGerobakController::class, 'data'])->name('agen_gerobak.data');
Route::get('agen/get-by-tipe', [AgenGerobakController::class, 'getAgenByTipe'])->name('agen.get_by_tipe');
Route::resource('agen_gerobak', AgenGerobakController::class);
Route::post('agen_gerobak/delete_selected', [AgenGerobakController::class, 'deleteSelected'])->name('agen_gerobak.delete_selected');
Route::get('agen_gerobak/{id}/laporan-penjualan', [AgenGerobakController::class, 'laporanPenjualan'])->name('agen_gerobak.laporan_penjualan');
Route::get('agen_gerobak/{id}/inventory', [AgenGerobakController::class, 'inventory'])->name('agen_gerobak.inventory');
Route::post('agen_gerobak/{id}/sync-stok', [AgenGerobakController::class, 'syncStok'])->name('agen_gerobak.sync_stok');
Route::get('agen_gerobak/{id}/penjualan-gerobak/{produkIdentifier}', [AgenGerobakController::class, 'penjualanPerGerobak'])->name('agen_gerobak.penjualan_gerobak');

// Routes untuk Gerobak management
Route::get('agen_gerobak/{agenId}/gerobak/data', [GerobakController::class, 'data'])->name('agen_gerobak.gerobak.data');
Route::post('gerobak/{id}/update-location', [GerobakController::class, 'updateLocation'])->name('gerobak.update_location');
Route::post('gerobak/{id}/update-stok', [GerobakController::class, 'updateStok'])->name('gerobak.update_stok');
Route::post('agen_gerobak/{agenId}/gerobak/delete-selected', [GerobakController::class, 'deleteSelected'])->name('agen_gerobak.gerobak.destroy-selected');
Route::get('agen_gerobak/{agenId}/gerobak', [GerobakController::class, 'index'])->name('agen_gerobak.gerobak.index');
Route::get('agen_gerobak/{agenId}/gerobak/create', [GerobakController::class, 'create'])->name('agen_gerobak.gerobak.create');
Route::post('agen_gerobak/{agenId}/gerobak', [GerobakController::class, 'store'])->name('agen_gerobak.gerobak.store');
Route::get('agen_gerobak/{agenId}/gerobak/{id}', [GerobakController::class, 'show'])->name('agen_gerobak.gerobak.show');
Route::get('agen_gerobak/{agenId}/gerobak/{id}/edit', [GerobakController::class, 'edit'])->name('agen_gerobak.gerobak.edit');
Route::put('agen_gerobak/{agenId}/gerobak/{id}', [GerobakController::class, 'update'])->name('agen_gerobak.gerobak.update');
Route::delete('agen_gerobak/{agenId}/gerobak/{id}', [GerobakController::class, 'destroy'])->name('agen_gerobak.gerobak.destroy');
// Routes untuk manage produk gerobak
Route::get('agen_gerobak/{gerobakId}/produk', [GerobakController::class, 'getProdukGerobak'])->name('agen_gerobak.gerobak.get-produk');
Route::post('agen_gerobak/{gerobakId}/update-produk', [GerobakController::class, 'updateProduk'])->name('agen_gerobak.gerobak.update-produk');
// Routes untuk laporan agen
Route::get('agen_gerobak/{id_agen}/laporan', [AgenLaporanController::class, 'index'])->name('agen_laporan.index');
Route::get('agen_gerobak/{id_agen}/laporan/data', [AgenLaporanController::class, 'data'])->name('agen_laporan.data');
Route::get('agen_gerobak/{id_agen}/laporan/create', [AgenLaporanController::class, 'create'])->name('agen_laporan.create');
Route::post('agen_gerobak/{id_agen}/laporan', [AgenLaporanController::class, 'store'])->name('agen_laporan.store');
Route::get('agen_laporan/get-produk/{id_gerobak}', [AgenLaporanController::class, 'getProdukByGerobak'])->name('agen_laporan.get_produk');
// Route khusus untuk halaman agen (tanpa parameter id_agen)
Route::get('agen/laporan', [AgenLaporanController::class, 'indexAgen'])->name('agen.laporan.index');

// Service Management Routes
Route::prefix('service-management')->group(function () {
    // Ongkos Kirim
    Route::get('/ongkos-kirim', [ServiceManagementController::class, 'ongkosKirimIndex'])->name('service.ongkos-kirim.index');
    Route::post('/ongkos-kirim', [ServiceManagementController::class, 'ongkosKirimStore'])->name('service.ongkos-kirim.store');
    Route::get('/ongkos-kirim/{id}/edit', [ServiceManagementController::class, 'ongkosKirimEdit'])->name('service.ongkos-kirim.edit');
    Route::put('/ongkos-kirim/{id}', [ServiceManagementController::class, 'ongkosKirimUpdate'])->name('service.ongkos-kirim.update');
    Route::delete('/ongkos-kirim/{id}', [ServiceManagementController::class, 'ongkosKirimDestroy'])->name('service.ongkos-kirim.destroy');
    
    // Mesin Customer
    Route::get('/mesin-customer', [ServiceManagementController::class, 'mesinCustomerIndex'])->name('service.mesin-customer.index');
    Route::post('/mesin-customer', [ServiceManagementController::class, 'mesinCustomerStore'])->name('service.mesin-customer.store');
    Route::get('/mesin-customer/{id}/edit', [ServiceManagementController::class, 'mesinCustomerEdit'])->name('service.mesin-customer.edit');
    Route::put('/mesin-customer/{id}', [ServiceManagementController::class, 'mesinCustomerUpdate'])->name('service.mesin-customer.update');
    Route::delete('/mesin-customer/{id}', [ServiceManagementController::class, 'mesinCustomerDestroy'])->name('service.mesin-customer.destroy');
    
    // Invoice
    Route::get('/invoice', [ServiceManagementController::class, 'invoiceIndex'])->name('service.invoice.index');
    Route::get('/invoice/history', [ServiceManagementController::class, 'invoiceHistory'])->name('service.invoice.history');
    Route::get('/invoice/print/{id}', [ServiceManagementController::class, 'invoicePrint'])->name('service.invoice.print');
    Route::get('/invoice/preview/{id}', [ServiceManagementController::class, 'invoicePreview'])->name('service.invoice.preview');
    Route::get('/get-mesin-customer/{id_member}', [ServiceManagementController::class, 'getMesinCustomer'])->name('service.get-mesin-customer');
    Route::post('/invoice', [ServiceManagementController::class, 'invoiceStore'])->name('service.invoice.store');
    Route::delete('/invoice/{id}', [ServiceManagementController::class, 'invoiceDestroy'])->name('service.invoice.destroy');
    Route::get('/mesin-customer-detail/{id}', [ServiceManagementController::class, 'getMesinCustomerDetail'])->name('service.mesin-customer.detail');
    Route::get('/service-management/invoice/preview/temp', [ServiceManagementController::class, 'invoicePreviewTemp'])->name('service.invoice.preview.temp');
    Route::get('/get-mesin-customer-grouped/{id_member}', [ServiceManagementController::class, 'getMesinCustomerGrouped'])->name('service.get-mesin-customer-grouped');
    Route::get('/get-mesin-customer-by-type/{id_member}/{closing_type}', [ServiceManagementController::class, 'getMesinCustomerByType'])->name('service.get-mesin-customer-by-type');
    Route::get('/invoice/status-counts', [ServiceManagementController::class, 'getStatusCounts'])->name('service.invoice.status-counts');
    Route::get('/invoice/status-counts', [ServiceManagementController::class, 'getStatusCounts'])->name('service.invoice.status-counts');
    Route::post('/invoice/{id}/status', [ServiceManagementController::class, 'updateStatus'])->name('service.invoice.update-status');
    Route::get('/invoice/export-pdf', [ServiceManagementController::class, 'exportPdf'])->name('service.invoice.export-pdf');
    // Alarm invoices - update nama route
    Route::get('/invoice/due-soon', [ServiceManagementController::class, 'getDueSoonInvoices'])->name('service.invoice.due-soon');
    // Customer search for invoice
    Route::get('/invoice/search-customers', [ServiceManagementController::class, 'searchCustomers'])->name('service.invoice.search-customers');
    Route::get('/invoice/upcoming-services', [ServiceManagementController::class, 'getUpcomingServiceInvoices']);
    Route::get('/invoice/service-berikutnya-counts', [ServiceManagementController::class, 'getServiceBerikutnyaCounts']);
    Route::post('/invoice/{id}/service-berikutnya', [ServiceManagementController::class, 'updateServiceBerikutnya'])->name('service.invoice.service-berikutnya');
});

// Routes untuk Sales Management
Route::prefix('sales-management')->group(function () {
    // Ongkos Kirim
    Route::get('/ongkos-kirim', [SalesOngkirController::class, 'index'])->name('sales.ongkos-kirim.index');
    Route::post('/ongkos-kirim', [SalesOngkirController::class, 'store'])->name('sales.ongkos-kirim.store');
    Route::get('/ongkos-kirim/{id}/edit', [SalesOngkirController::class, 'edit'])->name('sales.ongkos-kirim.edit');
    Route::put('/ongkos-kirim/{id}', [SalesOngkirController::class, 'update'])->name('sales.ongkos-kirim.update');
    Route::delete('/ongkos-kirim/{id}', [SalesOngkirController::class, 'destroy'])->name('sales.ongkos-kirim.destroy');
    
    // Customer Price
    Route::get('/customer-price', [SalesCustomerPriceController::class, 'index'])->name('sales.customer-price.index');
    Route::post('/customer-price', [SalesCustomerPriceController::class, 'store'])->name('sales.customer-price.store');
    Route::get('/customer-price/{id}/edit', [SalesCustomerPriceController::class, 'edit'])->name('sales.customer-price.edit');
    Route::put('/customer-price/{id}', [SalesCustomerPriceController::class, 'update'])->name('sales.customer-price.update');
    Route::delete('/customer-price/{id}', [SalesCustomerPriceController::class, 'destroy'])->name('sales.customer-price.destroy');
    Route::get('/customer-price/get-customers', [SalesCustomerPriceController::class, 'getCustomers'])->name('sales.customer-price.get-customers');
    
    // Invoice
    Route::get('/invoice', [SalesManagementController::class, 'invoiceIndex'])->name('sales.invoice.index');
    Route::post('/invoice', [SalesManagementController::class, 'invoiceStore'])->name('sales.invoice.store');
    Route::get('/invoice/{id}/print', [SalesManagementController::class, 'invoicePrint'])->name('sales.invoice.print');
    Route::delete('/invoice/{id}', [SalesManagementController::class, 'invoiceDestroy'])->name('sales.invoice.destroy');
    Route::post('/invoice/{id}/status', [SalesManagementController::class, 'updateStatus'])->name('sales.invoice.status.update');
    // Routes untuk produk
    Route::get('sales-management/get-produk-harga-khusus', [SalesManagementController::class, 'getProdukHargaKhusus'])->name('sales.get-produk-harga-khusus');
    Route::get('sales-management/get-produk-harga-normal', [SalesManagementController::class, 'getProdukHargaNormal'])->name('sales.get-produk-harga-normal');
    // Routes untuk preview
    Route::get('sales-management/invoice/{id}/preview', [SalesManagementController::class, 'invoicePreview'])->name('sales.invoice.preview');
    Route::get('sales-management/invoice/{id}/journal-preview', [SalesManagementController::class, 'journalPreview'])->name('sales.invoice.journal-preview');
    Route::post('sales-management/preview-journal-before-save', [SalesManagementController::class, 'previewJournalBeforeSave'])->name('sales.invoice.preview-journal-before-save');
    // Routes untuk history invoice sales
    Route::get('sales-management/invoice/history', [SalesManagementController::class, 'invoiceHistory'])->name('sales.invoice.history');
    Route::get('sales-management/invoice/status-counts', [SalesManagementController::class, 'invoiceStatusCounts'])->name('sales.invoice.status-counts');
    Route::get('sales-management/invoice/due-soon', [SalesManagementController::class, 'invoiceDueSoon'])->name('sales.invoice.due-soon');
    Route::get('sales-management/invoice/export-pdf', [SalesManagementController::class, 'invoiceExportPdf'])->name('sales.invoice.export-pdf');
    // Routes untuk setting nomor invoice sales
    Route::get('sales-management/invoice/setting', [SalesManagementController::class, 'invoiceSetting'])->name('sales.invoice.setting');
    Route::post('sales-management/invoice/update-setting', [SalesManagementController::class, 'updateInvoiceSetting'])->name('sales.invoice.update-setting');
    
    // API Routes
    Route::get('/get-customers', [SalesManagementController::class, 'getCustomers'])->name('sales.get-customers');
    Route::get('/get-customer-detail/{type}/{id}', [SalesManagementController::class, 'getCustomerDetail'])->name('sales.get-customer-detail');
    Route::get('/get-customer-price/{id_member}', [SalesManagementController::class, 'getCustomerPrice'])->name('sales.get-customer-price');
    Route::get('/get-customer-price-detail/{id}', [SalesManagementController::class, 'getCustomerPriceDetail'])->name('sales.get-customer-price-detail');
    Route::get('/get-customer-prices/{customerId}/{customerType}', [SalesManagementController::class, 'getCustomerPricesByCustomer'])->name('sales.get-customer-prices');
    
    // COA Setting
    Route::get('/coa-setting', [SalesManagementController::class, 'coaSetting'])->name('sales.coa.setting');
    Route::post('/coa-setting', [SalesManagementController::class, 'coaSettingUpdate'])->name('sales.coa.setting.update');
    Route::get('/coa-setting/preview', [SalesManagementController::class, 'coaSettingPreview'])->name('sales.coa.setting.preview');
    Route::get('/coa-setting/get-accounts', [SalesManagementController::class, 'getAccountsByType'])->name('sales.coa.get-accounts');
});


// Route untuk pencarian produk dan member
Route::get('/produk/cari', [ProdukController::class, 'cari'])->name('produk.cari');
Route::get('/member/cari', [MemberController::class, 'cari'])->name('member.cari');

// PO Penjualan Routes
Route::prefix('po-penjualan')->group(function () {
    Route::get('/', [PoPenjualanController::class, 'index'])->name('po-penjualan.index');
    Route::get('/data', [PoPenjualanController::class, 'data'])->name('po-penjualan.data');
    Route::get('/create', [PoPenjualanController::class, 'create'])->name('po-penjualan.create');
    Route::post('/', [PoPenjualanController::class, 'store'])->name('po-penjualan.store');
    Route::get('/{id}', [PoPenjualanController::class, 'show'])->name('po-penjualan.show');
    Route::get('/{id}/edit', [PoPenjualanController::class, 'edit'])->name('po-penjualan.edit');
    Route::put('/{id}', [PoPenjualanController::class, 'update'])->name('po-penjualan.update');
    Route::delete('/{id}', [PoPenjualanController::class, 'destroy'])->name('po-penjualan.destroy');
    Route::post('/{id}/status', [PoPenjualanController::class, 'updateStatus'])->name('po-penjualan.update-status');
    Route::get('/{id}/print', [PoPenjualanController::class, 'print'])->name('po-penjualan.print');
    Route::get('/product/{id}/price', [PoPenjualanController::class, 'getProductPrice'])->name('po-penjualan.product-price');
    Route::get('/laporan/cetak', [PoPenjualanController::class, 'printReport'])->name('po-penjualan.print-report');
    Route::get('/{id}/print', [PoPenjualanController::class, 'print'])->name('po-penjualan.print');
});

// Setting Routes
Route::prefix('settings')->group(function () {
    Route::get('/po-penjualan-accounts', [SettingCOAController::class, 'poPenjualanAccounts'])->name('settings.po-penjualan-accounts');
    Route::post('/po-penjualan-accounts', [SettingCOAController::class, 'updatePoPenjualanAccounts'])->name('settings.update-po-penjualan-accounts');
});

// Setting nomor invoice
Route::get('/invoice/setting', [ServiceManagementController::class, 'invoiceSetting'])->name('service.invoice.setting');
Route::post('/invoice/update-setting', [ServiceManagementController::class, 'updateInvoiceSetting'])->name('service.invoice.update-setting');


// Setting COA Routes
Route::prefix('settings/coa')->group(function () {
    Route::get('/', [SettingCOAController::class, 'index'])->name('settings.coa.index');
    Route::get('/po-penjualan', [SettingCOAController::class, 'poPenjualan'])->name('settings.coa.po-penjualan');
    Route::post('/po-penjualan', [SettingCOAController::class, 'updatePoPenjualan'])->name('settings.coa.update-po-penjualan');
    
    Route::get('/pembelian', [SettingCOAController::class, 'pembelian'])->name('settings.coa.pembelian');
    Route::post('/pembelian', [SettingCOAController::class, 'updatePembelian'])->name('settings.coa.update-pembelian');
    
    Route::get('/produksi', [SettingCOAController::class, 'produksi'])->name('settings.coa.produksi');
    Route::post('/produksi', [SettingCOAController::class, 'updateProduksi'])->name('settings.coa.update-produksi');
    
    Route::get('/retur', [SettingCOAController::class, 'retur'])->name('settings.coa.retur');
    Route::post('/retur', [SettingCOAController::class, 'updateRetur'])->name('settings.coa.update-retur');
    
    // API Routes
    Route::get('/accounts/{type}', [SettingCOAController::class, 'getAccountOptions'])->name('settings.coa.account-options');
    Route::post('/validate', [SettingCOAController::class, 'validateAccounts'])->name('settings.coa.validate-accounts');
});

Route::get('/kamera', function () {
    return view('kamera');
});

Route::get('/pbu', function () {
    return view('pbu');
});

// Sparepart Routes
Route::get('/sparepart', [SparepartController::class, 'index'])->name('sparepart.index');
Route::get('/sparepart/data', [SparepartController::class, 'index'])->name('sparepart.data');
Route::post('/sparepart', [SparepartController::class, 'store'])->name('sparepart.store');
Route::get('/sparepart/{id}/edit', [SparepartController::class, 'edit'])->name('sparepart.edit');
Route::put('/sparepart/{id}', [SparepartController::class, 'update'])->name('sparepart.update');
Route::delete('/sparepart/{id}', [SparepartController::class, 'destroy'])->name('sparepart.destroy');
Route::get('/sparepart/search', [SparepartController::class, 'search'])->name('sparepart.search');
Route::get('/sparepart/{id}/detail', [SparepartController::class, 'getDetail'])->name('sparepart.detail');
Route::get('/sparepart/{id}/edit', [SparepartController::class, 'edit'])->name('sparepart.edit');
Route::post('/sparepart/{id}/tambah-stok', [SparepartController::class, 'tambahStok'])->name('sparepart.tambah-stok');
Route::post('/sparepart/{id}/update-harga', [SparepartController::class, 'updateHarga'])->name('sparepart.update-harga');
Route::get('/sparepart/{id}/logs', [SparepartController::class, 'getLogs'])->name('sparepart.logs');
Route::get('/sparepart/export-log-pdf', [SparepartController::class, 'exportLogPdf'])->name('sparepart.export-log-pdf');
Route::get('/sparepart/get-for-filter', [SparepartController::class, 'getSparepartsForFilter'])->name('sparepart.get-for-filter');

    

// ====== USER MANAGEMENT & AUTHENTICATION ======
use App\Http\Controllers\AuthController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // User Management Routes
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
    });
    
    // Role Management Routes
    Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('index');
        Route::get('/{role}', [RoleManagementController::class, 'show'])->name('show');
        Route::post('/', [RoleManagementController::class, 'store'])->name('store');
        Route::put('/{role}', [RoleManagementController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleManagementController::class, 'destroy'])->name('destroy');
    });

    // Resolution Settings Routes
    Route::prefix('sistem/resolusi')->name('admin.sistem.resolusi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ResolutionSettingController::class, 'index'])->name('index');
        Route::post('/save', [\App\Http\Controllers\Admin\ResolutionSettingController::class, 'store'])->name('store');
        Route::post('/reset', [\App\Http\Controllers\Admin\ResolutionSettingController::class, 'reset'])->name('reset');
        Route::get('/get', [\App\Http\Controllers\Admin\ResolutionSettingController::class, 'get'])->name('get');
    });

    // ====== SDM / HRM ======
    Route::prefix('sdm')->name('sdm.')->group(function () {
        // Kepegawaian & Rekrutmen
        Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
            Route::get('/', [RecruitmentManagementController::class, 'index'])->name('index');
            Route::get('/data', [RecruitmentManagementController::class, 'getData'])->name('data');
            Route::get('/departments', [RecruitmentManagementController::class, 'getDepartments'])->name('departments');
            Route::post('/store', [RecruitmentManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [RecruitmentManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [RecruitmentManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [RecruitmentManagementController::class, 'destroy'])->name('destroy');
            Route::get('/export/pdf', [RecruitmentManagementController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [RecruitmentManagementController::class, 'exportExcel'])->name('export.excel');
        });

        // Payroll
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollManagementController::class, 'index'])->name('index');
            Route::get('/data', [PayrollManagementController::class, 'getData'])->name('data');
            Route::get('/employees', [PayrollManagementController::class, 'getEmployees'])->name('employees');
            Route::get('/attendance-summary', [PayrollManagementController::class, 'getAttendanceSummary'])->name('attendance.summary');
            
            // COA Settings - HARUS SEBELUM /{id}
            Route::get('/coa-settings', [PayrollCoaSettingController::class, 'index'])->name('coa.index');
            Route::get('/coa-settings/get', [PayrollCoaSettingController::class, 'getSettings'])->name('coa.settings');
            Route::get('/coa-settings/accounts', [PayrollCoaSettingController::class, 'getAccounts'])->name('coa.accounts');
            Route::post('/coa-settings/store', [PayrollCoaSettingController::class, 'store'])->name('coa.store');
            
            // Export routes - SEBELUM /{id}
            Route::get('/export/pdf', [PayrollManagementController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [PayrollManagementController::class, 'exportExcel'])->name('export.excel');
            
            // CRUD routes dengan {id} parameter - HARUS PALING AKHIR
            Route::post('/store', [PayrollManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [PayrollManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [PayrollManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [PayrollManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/approve', [PayrollManagementController::class, 'approve'])->name('approve');
            Route::post('/{id}/pay', [PayrollManagementController::class, 'pay'])->name('pay');
            Route::get('/{id}/slip', [PayrollManagementController::class, 'printSlip'])->name('slip');
        });

        // Attendance Management
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceManagementController::class, 'index'])->name('index');
            Route::get('/data', [AttendanceManagementController::class, 'getData'])->name('data');
            Route::get('/statistics', [AttendanceManagementController::class, 'getStatistics'])->name('statistics');
            Route::get('/employees', [AttendanceManagementController::class, 'getEmployees'])->name('employees');
            
            // New routes for daily/monthly table
            Route::get('/daily-table', [AttendanceManagementController::class, 'getDailyTable'])->name('daily.table');
            Route::get('/monthly-table', [AttendanceManagementController::class, 'getMonthlyTable'])->name('monthly.table');
            
            // Work schedule routes
            Route::get('/work-schedules', [AttendanceManagementController::class, 'getWorkSchedules'])->name('work.schedules');
            Route::post('/set-work-hours', [AttendanceManagementController::class, 'setWorkHours'])->name('set.work.hours');
            Route::get('/employee-schedule/{employeeId}', [AttendanceManagementController::class, 'getEmployeeSchedule'])->name('employee.schedule');
            
            // Time Settings routes
            Route::get('/time-settings', [AttendanceManagementController::class, 'getTimeSettings'])->name('time.settings');
            Route::post('/time-settings', [AttendanceManagementController::class, 'updateTimeSettings'])->name('time.settings.update');
            Route::post('/test-time-period', [AttendanceManagementController::class, 'testTimePeriod'])->name('test.time.period');
            
            // Export routes - SEBELUM /{id}
            Route::get('/export/pdf', [AttendanceManagementController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [AttendanceManagementController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/daily-pdf', [AttendanceManagementController::class, 'exportDailyPdf'])->name('export.daily.pdf');
            Route::get('/export/monthly-pdf', [AttendanceManagementController::class, 'exportMonthlyPdf'])->name('export.monthly.pdf');
            
            // CRUD routes dengan {id} parameter - HARUS PALING AKHIR
            Route::post('/store', [AttendanceManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [AttendanceManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [AttendanceManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [AttendanceManagementController::class, 'destroy'])->name('destroy');
        });

        // Performance Appraisal / Manajemen Kinerja
        Route::prefix('kinerja')->name('kinerja.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PerformanceAppraisalController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\PerformanceAppraisalController::class, 'getData'])->name('data');
            Route::get('/statistics', [\App\Http\Controllers\PerformanceAppraisalController::class, 'getStatistics'])->name('statistics');
            Route::get('/employees', [\App\Http\Controllers\PerformanceAppraisalController::class, 'getEmployees'])->name('employees');
            
            // Export routes - SEBELUM /{id}
            Route::get('/export/pdf', [\App\Http\Controllers\PerformanceAppraisalController::class, 'exportPdf'])->name('export.pdf');
            
            // CRUD routes dengan {id} parameter - HARUS PALING AKHIR
            Route::post('/store', [\App\Http\Controllers\PerformanceAppraisalController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\PerformanceAppraisalController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\PerformanceAppraisalController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\PerformanceAppraisalController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Kontrak & Dokumen HR
        Route::prefix('kontrak')->name('kontrak.')->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\KontrakDokumenController::class, 'index'])->name('index');
            
            // Kontrak Kerja
            Route::prefix('kontrak')->name('kontrak.')->group(function () {
                Route::get('/', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakIndex'])->name('index');
                Route::get('/create', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakCreate'])->name('create');
                Route::post('/store', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakStore'])->name('store');
                Route::get('/{id}/edit', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakEdit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakUpdate'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'kontrakDestroy'])->name('destroy');
                Route::get('/{id}/print', [\App\Http\Controllers\KontrakDokumenController::class, 'printKontrakPdf'])->name('print');
            });
            
            // Perpanjangan Kontrak
            Route::prefix('perpanjangan')->name('perpanjangan.')->group(function () {
                Route::get('/', [\App\Http\Controllers\KontrakDokumenController::class, 'perpanjanganIndex'])->name('index');
                Route::get('/create', [\App\Http\Controllers\KontrakDokumenController::class, 'perpanjanganCreate'])->name('create');
                Route::post('/store', [\App\Http\Controllers\KontrakDokumenController::class, 'perpanjanganStore'])->name('store');
                Route::get('/{id}/print', [\App\Http\Controllers\KontrakDokumenController::class, 'printPerpanjanganPdf'])->name('print');
            });
            
            // Surat Peringatan
            Route::prefix('sp')->name('sp.')->group(function () {
                Route::get('/', [\App\Http\Controllers\KontrakDokumenController::class, 'spIndex'])->name('index');
                Route::get('/create', [\App\Http\Controllers\KontrakDokumenController::class, 'spCreate'])->name('create');
                Route::post('/store', [\App\Http\Controllers\KontrakDokumenController::class, 'spStore'])->name('store');
                Route::get('/{id}/edit', [\App\Http\Controllers\KontrakDokumenController::class, 'spEdit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'spUpdate'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'spDestroy'])->name('destroy');
                Route::get('/{id}/print', [\App\Http\Controllers\KontrakDokumenController::class, 'printSpPdf'])->name('print');
            });
            
            // Dokumen HR
            Route::prefix('dokumen')->name('dokumen.')->group(function () {
                Route::get('/', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenIndex'])->name('index');
                Route::get('/create', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenCreate'])->name('create');
                Route::post('/store', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenStore'])->name('store');
                Route::get('/{id}/edit', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenEdit'])->name('edit');
                Route::get('/{id}/print', [\App\Http\Controllers\KontrakDokumenController::class, 'printDokumenPdf'])->name('print');
                Route::put('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenUpdate'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\KontrakDokumenController::class, 'dokumenDestroy'])->name('destroy');
            });
            
            // Monitoring
            Route::get('/monitoring', [\App\Http\Controllers\KontrakDokumenController::class, 'monitoring'])->name('monitoring');
            
            // Export PDF
            Route::get('/export/kontrak-pdf', [\App\Http\Controllers\KontrakDokumenController::class, 'exportKontrakPdf'])->name('export.kontrak.pdf');
            Route::get('/export/sp-pdf', [\App\Http\Controllers\KontrakDokumenController::class, 'exportSpPdf'])->name('export.sp.pdf');
            Route::get('/export/monitoring-pdf', [\App\Http\Controllers\KontrakDokumenController::class, 'exportMonitoringPdf'])->name('export.monitoring.pdf');
        });
    });

    // ====== INVESTOR ADMIN (rute baru yang rapi) ======
    Route::prefix('investor-admin')->name('admin.investor.')->group(function () {
        // Profil Investor
        Route::get('profil/export', [App\Http\Controllers\Admin\InvestorProfilController::class, 'export'])->name('profil.export');
        Route::get('profil', [App\Http\Controllers\Admin\InvestorProfilController::class, 'index'])->name('profil.index');
        Route::post('profil', [App\Http\Controllers\Admin\InvestorProfilController::class, 'store'])->name('profil.store');
        Route::get('profil/{id}', [App\Http\Controllers\Admin\InvestorProfilController::class, 'show'])->name('profil.show');
        Route::get('profil/{id}/edit', [App\Http\Controllers\Admin\InvestorProfilController::class, 'edit'])->name('profil.edit');
        Route::put('profil/{id}', [App\Http\Controllers\Admin\InvestorProfilController::class, 'update'])->name('profil.update');
        Route::delete('profil/{id}', [App\Http\Controllers\Admin\InvestorProfilController::class, 'destroy'])->name('profil.destroy');
        
        // Bagi Hasil
        Route::get('bagi-hasil', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'index'])->name('bagi-hasil.index');
        Route::post('bagi-hasil', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'store'])->name('bagi-hasil.store');
        Route::get('bagi-hasil/{id}', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'show'])->name('bagi-hasil.show');
        Route::get('bagi-hasil/{id}/edit', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'edit'])->name('bagi-hasil.edit');
        Route::put('bagi-hasil/{id}', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'update'])->name('bagi-hasil.update');
        Route::delete('bagi-hasil/{id}', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'destroy'])->name('bagi-hasil.destroy');
        Route::post('bagi-hasil/{id}/approve', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'approve'])->name('bagi-hasil.approve');
        Route::post('bagi-hasil/{id}/mark-as-paid', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'markAsPaid'])->name('bagi-hasil.mark-as-paid');
        Route::get('bagi-hasil/export', [App\Http\Controllers\Admin\InvestorBagiHasilController::class, 'export'])->name('bagi-hasil.export');
        
        // Pencairan
        Route::get('pencairan', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'index'])->name('pencairan.index');
        Route::post('pencairan', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'store'])->name('pencairan.store');
        Route::get('pencairan/{id}', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'show'])->name('pencairan.show');
        Route::get('pencairan/{id}/edit', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'edit'])->name('pencairan.edit');
        Route::put('pencairan/{id}', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'update'])->name('pencairan.update');
        Route::delete('pencairan/{id}', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'destroy'])->name('pencairan.destroy');
        Route::post('pencairan/{id}/approve', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'approve'])->name('pencairan.approve');
        Route::post('pencairan/{id}/reject', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'reject'])->name('pencairan.reject');
        Route::post('pencairan/{id}/mark-as-paid', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'markAsPaid'])->name('pencairan.mark-as-paid');
        Route::get('pencairan/export', [App\Http\Controllers\Admin\InvestorPencairanController::class, 'export'])->name('pencairan.export');
    });

    // ====== CHAT SYSTEM ======
    Route::prefix('admin/chat')->name('admin.chat.')->middleware(['throttle:60,1'])->group(function () {
        Route::get('/panel', [\App\Http\Controllers\ChatController::class, 'panel'])->name('panel');
        Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
        Route::post('/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send')->middleware('throttle:10,1');
        Route::get('/users', [\App\Http\Controllers\ChatController::class, 'getUserList'])->name('users');
        Route::post('/mark-read', [\App\Http\Controllers\ChatController::class, 'markAsRead'])->name('mark-read');
        Route::get('/unread-count', [\App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('unread-count');
    });
    
}); // End of admin group
// Test route for permission debugging
Route::get('/test-permission', function () {
    return view('test-permission');
})->middleware('auth');
// Test route for debugging user permissions
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    $permissions = [];
    
    $testPermissions = ['service.mesin.create', 'service.ongkir.create', 'service.invoice.create'];
    foreach ($testPermissions as $perm) {
        $permissions[$perm] = $user->hasPermission($perm);
    }
    
    return [
        'user' => $user->name,
        'email' => $user->email,
        'role' => $user->role->name,
        'permissions' => $permissions,
        'is_super_admin' => $user->hasRole('super_admin')
    ];
})->middleware('auth');

// End of routes

// ================== CATCH-ALL ADMIN REDIRECT ==================
// CRITICAL: This MUST be at the END of the file after ALL other routes
// Intercept any remaining admin sub-routes and redirect to main admin page
// This ensures browser reload always goes to /admin with full tab system
Route::get('/admin/{any}', function ($any) {
    try {
        Log::info('🔄 [ADMIN REDIRECT] Sub-route accessed, redirecting to main admin', [
            'requested_path' => '/admin/' . $any,
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip()
        ]);

        // Check if user is authenticated
        if (Auth::check()) {
            Log::info('✅ [ADMIN REDIRECT] Authenticated user, redirecting to main admin');
            // Always redirect to main admin page to maintain tab system
            return redirect('/admin');
        }

        // Redirect to login for unauthenticated users
        Log::info('🔄 [ADMIN REDIRECT] Unauthenticated user, redirecting to login');
        return redirect()->route('login');
        
    } catch (\Exception $e) {
        Log::error('💥 [ADMIN REDIRECT] Error in admin redirect', [
            'error' => $e->getMessage(),
            'requested_path' => '/admin/' . $any
        ]);
        
        // Fallback to main admin page
        return redirect('/admin');
    }
})->where('any', '.+')->middleware(['web']); // .+ means one or more characters (excludes empty string)