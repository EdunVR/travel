@echo off
echo ========================================
echo  TEST: Company Settings Print Integration
echo ========================================
echo.
echo Testing the "Ikuti POS" template feature...
echo.

echo [1/3] Checking if template option exists in invoice index...
findstr /C:"pos_style" resources\views\admin\penjualan\invoice\index.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Template option "pos_style" found in invoice index
) else (
    echo ✗ Template option "pos_style" NOT found in invoice index
)

echo.
echo [2/3] Checking if POS template implemented in print view...
findstr /C:"pos_style" resources\views\admin\penjualan\invoice\print.blade.php >nul
if %errorlevel%==0 (
    echo ✓ POS template implementation found in print view
) else (
    echo ✗ POS template implementation NOT found in print view
)

echo.
echo [3/3] Checking if company phone integration exists...
findstr /C:"company_phone" resources\views\admin\penjualan\invoice\print.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Company phone integration found in print view
) else (
    echo ✗ Company phone integration NOT found in print view
)

echo.
echo ========================================
echo  TEST RESULTS
echo ========================================
echo.
echo Manual testing steps:
echo 1. Login to admin panel
echo 2. Go to Penjualan ^> Invoice Penjualan
echo 3. Click Print button on any invoice
echo 4. Look for "Ikuti POS" option in template dropdown
echo 5. Select "Ikuti POS" and verify preview shows POS-style layout
echo 6. Check that company phone appears in header
echo 7. Verify print/download works with POS template
echo.
pause