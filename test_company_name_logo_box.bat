@echo off
echo ========================================
echo  TEST: Company Name in Logo Box
echo ========================================
echo.
echo Testing company name display in logo box...
echo.

echo [1/4] Checking invoice print template uses company name...
findstr /C:"companySettings\['company_name'\]" resources\views\admin\penjualan\invoice\print.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Invoice print template uses company name
) else (
    echo ✗ Invoice print template does NOT use company name
)

echo.
echo [2/4] Checking POS nota besar uses company name...
findstr /C:"companySettings\['company_name'\]" resources\views\admin\penjualan\pos\nota_besar.blade.php >nul
if %errorlevel%==0 (
    echo ✓ POS nota besar uses company name
) else (
    echo ✗ POS nota besar does NOT use company name
)

echo.
echo [3/4] Checking invoice template uses company phone...
findstr /C:"companySettings\['company_phone'\]" resources\views\admin\penjualan\invoice\print.blade.php >nul
if %errorlevel%==0 (
    echo ✓ Invoice template uses company phone
) else (
    echo ✗ Invoice template does NOT use company phone
)

echo.
echo [4/4] Checking POS nota besar uses company phone...
findstr /C:"companySettings\['company_phone'\]" resources\views\admin\penjualan\pos\nota_besar.blade.php >nul
if %errorlevel%==0 (
    echo ✓ POS nota besar uses company phone
) else (
    echo ✗ POS nota besar does NOT use company phone
)

echo.
echo ========================================
echo  MANUAL TESTING STEPS
echo ========================================
echo.
echo Test Invoice Print Template:
echo 1. Go to Penjualan ^> Invoice Penjualan
echo 2. Click Print on any invoice
echo 3. Select "Ikuti POS" template
echo 4. Verify logo box shows COMPANY NAME (not outlet name)
echo 5. Verify header shows company phone number
echo.
echo Test POS Nota Besar:
echo 1. Go to Point of Sales
echo 2. Create a transaction
echo 3. Print nota besar
echo 4. Verify logo box shows COMPANY NAME (not outlet name)
echo 5. Verify header shows company phone number
echo.
echo Expected Results:
echo - Logo box: "NAMA PERUSAHAAN" (from company settings)
echo - Phone: Company phone number (from company settings)
echo - Both templates should be consistent
echo.
pause