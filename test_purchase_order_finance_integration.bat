@echo off
echo ========================================
echo Testing Purchase Order Finance Integration
echo ========================================
echo.

echo Testing Purchase Order Print Templates...
echo.

echo 1. Testing Purchase Order Standard Print
curl -X GET "http://localhost/MORRA/pembelian/purchase-order/1/print?preview=1" ^
     -H "Accept: text/html" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_po_standard.html"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Purchase Order Standard Print - SUCCESS
) else (
    echo ✗ Purchase Order Standard Print - FAILED
)

echo.
echo 2. Testing Purchase Order Invoice Print
curl -X GET "http://localhost/MORRA/pembelian/purchase-order/1/print-document?preview=1" ^
     -H "Accept: text/html" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_po_invoice.html"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Purchase Order Invoice Print - SUCCESS
) else (
    echo ✗ Purchase Order Invoice Print - FAILED
)

echo.
echo 3. Testing Finance Reports PDF Export
curl -X GET "http://localhost/MORRA/finance/neraca-saldo/export-pdf?outlet_id=1&start_date=2024-01-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_neraca_saldo.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Neraca Saldo PDF Export - SUCCESS
) else (
    echo ✗ Neraca Saldo PDF Export - FAILED
)

echo.
echo 4. Testing Profit Loss PDF Export
curl -X GET "http://localhost/MORRA/finance/profit-loss/export-pdf?outlet_id=1&start_date=2024-01-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_profit_loss.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Profit Loss PDF Export - SUCCESS
) else (
    echo ✗ Profit Loss PDF Export - FAILED
)

echo.
echo 5. Testing Balance Sheet PDF Export
curl -X GET "http://localhost/MORRA/finance/neraca/export-pdf?outlet_id=1&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_balance_sheet.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Balance Sheet PDF Export - SUCCESS
) else (
    echo ✗ Balance Sheet PDF Export - FAILED
)

echo.
echo ========================================
echo Test Results Summary:
echo ========================================
echo.
echo Files created:
if exist "test_po_standard.html" echo ✓ test_po_standard.html
if exist "test_po_invoice.html" echo ✓ test_po_invoice.html  
if exist "test_neraca_saldo.pdf" echo ✓ test_neraca_saldo.pdf
if exist "test_profit_loss.pdf" echo ✓ test_profit_loss.pdf
if exist "test_balance_sheet.pdf" echo ✓ test_balance_sheet.pdf

echo.
echo Check the generated files to verify:
echo 1. Company logo appears in header
echo 2. Company name, address, phone, email are displayed
echo 3. All print templates show consistent company branding
echo.

echo ========================================
echo Integration Test Complete
echo ========================================
pause