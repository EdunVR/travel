@echo off
echo ========================================
echo DEPLOY KONTRABON SELECT ALL FIX
echo ========================================
echo.

echo [1/4] Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo [2/4] Running test script...
php test_kontrabon_select_all_fix.php
echo.

echo [3/4] Deployment summary...
echo.
echo CHANGES MADE:
echo - Fixed checkbox field name: selected_penjualan[] to piutang_ids[]
echo - Fixed checkbox value: id_penjualan to id_piutang
echo - Fixed no_transaksi display: hardcode to database value
echo - Updated Select All function
echo - Updated Auto Pilih Hutang function
echo.

echo [4/4] Next steps...
echo.
echo Please test manually:
echo 1. Open: /admin/penjualan/kontrabon/create
echo 2. Select customer with piutang
echo 3. Set date range
echo 4. Click "Select All"
echo 5. Submit and print
echo 6. Verify printed data matches selected items
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Read: QUICK_TEST_KONTRABON_SELECT_ALL_FIX.md for detailed testing guide
echo.

pause
