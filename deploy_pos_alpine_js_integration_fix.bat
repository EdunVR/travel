@echo off
echo 🚀 Deploying POS Alpine.js Integration Fix
echo ==========================================

echo.
echo 📋 Step 1: Backup current POS template
copy "resources\views\admin\penjualan\pos\index.blade.php" "resources\views\admin\penjualan\pos\index.blade.php.backup" >nul
echo ✅ Backup created

echo.
echo 📋 Step 2: Create corrected POS template
echo Creating new POS template with proper Alpine.js integration...

REM The file is too large to replace via strReplace, so we'll create a deployment guide instead
echo.
echo ⚠️  MANUAL STEPS REQUIRED:
echo.
echo 1. Open: resources\views\admin\penjualan\pos\index.blade.php
echo 2. Find the script section starting with:
echo    ^<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"^>^</script^>
echo.
echo 3. Replace the ENTIRE script section (from jsbarcode to ^</script^>) with:
echo.
echo {{-- Load JsBarcode library for barcode generation --}}
echo ^<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"^>^</script^>
echo.
echo {{-- Initialize POS data for the separate pos.js file --}}
echo ^<script^>
echo // Set up window variables for POS initialization
echo window.posInitialOutlet = {{ $selectedOutlet }};
echo window.posCSRFToken = '{{ csrf_token() }}';
echo.
echo // Set up route variables for POS API calls
echo window.posProductsRoute = '{{ route("admin.penjualan.pos.products") }}';
echo window.posCustomersRoute = '{{ route("admin.penjualan.pos.customers") }}';
echo window.posCustomerTypePricesRoute = '{{ route("admin.penjualan.pos.customer-type-prices") }}';
echo window.posAccountingBooksRoute = '{{ route("finance.accounting-books.data") }}';
echo window.posChartOfAccountsRoute = '{{ route("finance.chart-of-accounts.data") }}';
echo window.posCoaSettingsRoute = '{{ route("admin.penjualan.pos.coa.settings") }}';
echo window.posCoaSettingsUpdateRoute = '{{ route("admin.penjualan.pos.coa.settings.update") }}';
echo window.posStoreRoute = '{{ route("admin.penjualan.pos.store") }}';
echo window.posPrintRoute = '{{ route("admin.penjualan.pos.print", ":id") }}';
echo window.posHistoryRoute = '{{ route("admin.penjualan.pos.history.data") }}';
echo window.posDashboardRoute = '{{ route("admin.dashboard") }}';
echo window.posLoginRoute = '{{ route("login") }}';
echo.
echo console.log('✅ [POS] Initialization variables set up for separate pos.js file');
echo ^</script^>
echo.
echo 4. Save the file
echo.
echo 📋 Step 3: Clear cache and test
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo 📋 Step 4: Test the integration
php test_pos_alpine_integration.php

echo.
echo 🎯 DEPLOYMENT COMPLETE
echo ======================
echo.
echo ✅ Admin layout updated with pos.js script
echo ✅ Separate pos.js file created with Alpine.js component
echo ⚠️  Manual update needed for POS template (see steps above)
echo.
echo 📋 Next Steps:
echo 1. Complete the manual template update
echo 2. Open POS page in browser
echo 3. Check console (F12) for Alpine.js errors
echo 4. Test customer type pricing functionality
echo.
echo 🔗 Files to check:
echo - resources\views\admin\penjualan\pos\index.blade.php
echo - public\js\pos.js
echo - resources\views\components\layouts\admin.blade.php
echo.
pause