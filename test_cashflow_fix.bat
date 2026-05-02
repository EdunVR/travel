@echo off
echo Testing Cash Flow PDF Export Fix...
echo.

echo Testing Cash Flow PDF with company settings integration...
curl -X GET "http://localhost/MORRA/finance/cashflow/export/pdf?outlet_id=1&start_date=2024-12-01&end_date=2024-12-31&method=direct" ^
     -H "Accept: application/pdf" ^
     -o "test_cashflow_export.pdf"

if exist "test_cashflow_export.pdf" (
    echo ✓ Cash Flow PDF exported successfully
    echo File size: 
    dir "test_cashflow_export.pdf" | find "test_cashflow_export.pdf"
    del "test_cashflow_export.pdf"
) else (
    echo ✗ Cash Flow PDF export failed
)

echo.
echo Testing complete!
pause