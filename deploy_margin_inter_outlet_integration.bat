@echo off
echo ========================================
echo DEPLOYING MARGIN INTER-OUTLET INTEGRATION
echo ========================================
echo.

echo FITUR BARU: Menambahkan data penjualan antar outlet ke Laporan Margin
echo BENEFIT: Visibilitas margin lengkap dari semua channel penjualan
echo.

echo 1. Testing inter-outlet integration...
php test_margin_inter_outlet_integration.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo 3. Verifying code changes...
findstr /C:"InterOutletSaleItem" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✓ InterOutletSaleItem model imported
) else (
    echo    ✗ InterOutletSaleItem model NOT imported
)

findstr /C:"inter_outlet" app\Http\Controllers\MarginReportController.php >nul
if %errorlevel%==0 (
    echo    ✓ Inter-outlet source identifier found
) else (
    echo    ✗ Inter-outlet source identifier NOT found
)

findstr /C:"bg-purple-100" resources\views\admin\penjualan\margin\index.blade.php >nul
if %errorlevel%==0 (
    echo    ✓ Purple badge styling found in view
) else (
    echo    ✗ Purple badge styling NOT found in view
)

findstr /C:"bx-transfer" resources\views\admin\penjualan\margin\index.blade.php >nul
if %errorlevel%==0 (
    echo    ✓ Transfer icon found in view
) else (
    echo    ✗ Transfer icon NOT found in view
)
echo.

echo 4. Feature summary...
echo    NEW SOURCE TYPES:
echo    - Invoice (Blue badge)
echo    - POS (Cyan badge)  
echo    - Inter Outlet (Purple badge) ← NEW!
echo.
echo    OUTLET DISPLAY FORMAT:
echo    - Invoice/POS: "Outlet Name"
echo    - Inter Outlet: "Outlet Asal → Outlet Tujuan" ← NEW!
echo.
echo    PAYMENT TYPES:
echo    - Invoice: Cash/BON
echo    - POS: Cash/QRIS/BON
echo    - Inter Outlet: Transfer ← NEW!
echo.

echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo TESTING CHECKLIST:
echo 1. Login to admin panel
echo 2. Go to Penjualan ^> Laporan Margin
echo 3. Verify inter-outlet data appears with purple badge
echo 4. Check outlet format: "Asal → Tujuan"
echo 5. Verify HPP uses FIFO calculation
echo 6. Test outlet filter includes inter-outlet transactions
echo 7. Confirm only approved inter-outlet sales appear
echo.
echo EXPECTED RESULTS:
echo - 3 source types: Invoice, POS, Inter Outlet
echo - Purple badge for inter-outlet with transfer icon
echo - Outlet format shows "Source → Destination"
echo - Payment type shows "Transfer"
echo - HPP calculated using FIFO method
echo - Profit and margin calculated correctly
echo.
echo NEW FEATURES:
echo ✓ Inter-outlet sales integration
echo ✓ Purple badge with transfer icon
echo ✓ Outlet format "Asal → Tujuan"
echo ✓ Payment type "Transfer"
echo ✓ FIFO HPP calculation
echo ✓ Smart outlet filtering (asal OR tujuan)
echo ✓ Approved status filter
echo.
echo FILES MODIFIED:
echo - app/Http/Controllers/MarginReportController.php
echo - resources/views/admin/penjualan/margin/index.blade.php
echo - test_margin_inter_outlet_integration.php (new)
echo - MARGIN_INTER_OUTLET_INTEGRATION_COMPLETE.md (new)
echo.
pause