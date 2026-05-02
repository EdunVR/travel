@echo off
echo ========================================
echo Testing Finance PDF Design Improvements
echo ========================================
echo.

echo Testing improved PDF designs for all finance reports...
echo.

echo 1. Testing Neraca Saldo (Trial Balance) PDF
curl -X GET "http://localhost/MORRA/finance/neraca-saldo/export-pdf?outlet_id=1&start_date=2024-01-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_neraca_saldo_improved.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Neraca Saldo PDF - SUCCESS
) else (
    echo ✗ Neraca Saldo PDF - FAILED
)

echo.
echo 2. Testing Profit Loss (Laba Rugi) PDF
curl -X GET "http://localhost/MORRA/finance/profit-loss/export-pdf?outlet_id=1&start_date=2024-01-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_profit_loss_improved.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Profit Loss PDF - SUCCESS
) else (
    echo ✗ Profit Loss PDF - FAILED
)

echo.
echo 3. Testing Balance Sheet (Neraca) PDF
curl -X GET "http://localhost/MORRA/finance/neraca/export-pdf?outlet_id=1&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_balance_sheet_improved.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Balance Sheet PDF - SUCCESS
) else (
    echo ✗ Balance Sheet PDF - FAILED
)

echo.
echo 4. Testing Fixed Assets PDF
curl -X GET "http://localhost/MORRA/finance/aktiva-tetap/export-pdf?outlet_id=1" ^
     -H "Accept: application/pdf" ^
     -b "laravel_session=your_session_cookie" ^
     -o "test_fixed_assets_improved.pdf"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Fixed Assets PDF - SUCCESS
) else (
    echo ✗ Fixed Assets PDF - FAILED
)

echo.
echo ========================================
echo Design Improvement Test Results:
echo ========================================
echo.
echo Files created:
if exist "test_neraca_saldo_improved.pdf" echo ✓ test_neraca_saldo_improved.pdf
if exist "test_profit_loss_improved.pdf" echo ✓ test_profit_loss_improved.pdf  
if exist "test_balance_sheet_improved.pdf" echo ✓ test_balance_sheet_improved.pdf
if exist "test_fixed_assets_improved.pdf" echo ✓ test_fixed_assets_improved.pdf

echo.
echo Check the generated PDFs to verify:
echo 1. Professional letterhead with logo on left, company info centered
echo 2. Improved margins and spacing
echo 3. Enhanced table designs with gradients and shadows
echo 4. Better typography and color schemes
echo 5. Professional footer with company branding
echo 6. Consistent design across all reports
echo.

echo ========================================
echo PDF Design Test Complete
echo ========================================
pause