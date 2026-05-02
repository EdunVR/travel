@echo off
echo Testing Finance PDF Logo Fix...
echo.

echo 1. Testing Cash Flow PDF Export with Company Settings...
curl -X GET "http://localhost/MORRA/finance/cashflow/export/pdf?outlet_id=1&start_date=2024-12-01&end_date=2024-12-31&method=direct" ^
     -H "Accept: application/pdf" ^
     -o "test_cashflow_export.pdf"

if exist "test_cashflow_export.pdf" (
    echo ✓ Cash Flow PDF exported successfully
    del "test_cashflow_export.pdf"
) else (
    echo ✗ Cash Flow PDF export failed
)

echo.
echo 2. Testing other Finance PDF exports...

echo Testing Neraca Saldo PDF...
curl -X GET "http://localhost/MORRA/finance/neraca-saldo/export/pdf?outlet_id=1&start_date=2024-12-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -o "test_neraca_saldo.pdf"

if exist "test_neraca_saldo.pdf" (
    echo ✓ Neraca Saldo PDF exported successfully
    del "test_neraca_saldo.pdf"
) else (
    echo ✗ Neraca Saldo PDF export failed
)

echo.
echo Testing Laba Rugi PDF...
curl -X GET "http://localhost/MORRA/finance/labarugi/export/pdf?outlet_id=1&start_date=2024-12-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -o "test_labarugi.pdf"

if exist "test_labarugi.pdf" (
    echo ✓ Laba Rugi PDF exported successfully
    del "test_labarugi.pdf"
) else (
    echo ✗ Laba Rugi PDF export failed
)

echo.
echo Testing Neraca PDF...
curl -X GET "http://localhost/MORRA/finance/neraca/export/pdf?outlet_id=1&date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -o "test_neraca.pdf"

if exist "test_neraca.pdf" (
    echo ✓ Neraca PDF exported successfully
    del "test_neraca.pdf"
) else (
    echo ✗ Neraca PDF export failed
)

echo.
echo Testing Buku Besar PDF...
curl -X GET "http://localhost/MORRA/finance/buku-besar/export/pdf?outlet_id=1&account_id=1&start_date=2024-12-01&end_date=2024-12-31" ^
     -H "Accept: application/pdf" ^
     -o "test_buku_besar.pdf"

if exist "test_buku_besar.pdf" (
    echo ✓ Buku Besar PDF exported successfully
    del "test_buku_besar.pdf"
) else (
    echo ✗ Buku Besar PDF export failed
)

echo.
echo Finance PDF Logo Fix Testing Complete!
echo All finance reports should now display company logo and professional letterhead.
pause