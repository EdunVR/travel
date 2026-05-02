@echo off
echo ========================================
echo   DEPLOY PO PAYMENT MODAL SCROLL FIX
echo ========================================
echo.

echo [1/4] Creating backup...
if exist "resources\views\admin\pembelian\purchase-order\index.blade.php.backup" (
    echo    Backup already exists, skipping...
) else (
    copy "resources\views\admin\pembelian\purchase-order\index.blade.php" "resources\views\admin\pembelian\purchase-order\index.blade.php.backup"
    echo    ✅ Backup created
)

echo.
echo [2/4] Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo    ✅ Cache cleared

echo.
echo [3/4] Running verification test...
php test_po_payment_modal_scroll_fix.php

echo.
echo [4/4] Testing modal accessibility...
echo    📋 Manual testing required:
echo    1. Open Purchase Order page in browser
echo    2. Find PO with 'vendor_bill' status
echo    3. Click 'Bayar' button
echo    4. Test modal on different screen sizes:
echo       - Desktop: 1920x1080
echo       - Laptop: 1366x768  
echo       - Tablet: 768x1024
echo       - Mobile: 375x667
echo    5. Verify:
echo       - All buttons are visible
echo       - Content can be scrolled
echo       - Modal is responsive
echo       - Payment form works correctly

echo.
echo ========================================
echo   DEPLOYMENT COMPLETE
echo ========================================
echo.
echo ✅ Modal scroll fix has been deployed
echo 📝 Check documentation: PO_PAYMENT_MODAL_SCROLL_FIX_COMPLETE.md
echo 🧪 Run manual tests as described above
echo.
echo If issues occur, restore backup:
echo copy "resources\views\admin\pembelian\purchase-order\index.blade.php.backup" "resources\views\admin\pembelian\purchase-order\index.blade.php"
echo.
pause