@echo off
echo ============================================================
echo DEPLOYING: PDF Rupiah Format Fix
echo ============================================================
echo.

echo CHANGE BEING DEPLOYED:
echo - HPP/Unit column now uses Rupiah format
echo - Total Biaya column now uses Rupiah format
echo.

echo ============================================================
echo Step 1: Testing PDF Format
echo ============================================================
php test_pdf_rupiah_format.php
if errorlevel 1 (
    echo ERROR: Test failed!
    pause
    exit /b 1
)
echo.

echo ============================================================
echo Step 2: Clear View Cache
echo ============================================================
php artisan view:clear
echo View cache cleared successfully!
echo.

echo ============================================================
echo DEPLOYMENT SUMMARY
echo ============================================================
echo.
echo FILE MODIFIED:
echo - resources/views/admin/produksi/produksi/bulk-production-pdf.blade.php
echo.
echo CHANGES:
echo Line 377: {{ $production['hpp_per_unit'] }}
echo       to: {{ $production['hpp_per_unit_formatted'] }}
echo.
echo Line 378: {{ $production['total_cost'] }}
echo       to: {{ $production['total_cost_formatted'] }}
echo.
echo RESULT:
echo - HPP/Unit: Rp 1.870 (with thousand separator)
echo - Total Biaya: Rp 145.000.000 (with thousand separator)
echo.

echo ============================================================
echo TESTING INSTRUCTIONS
echo ============================================================
echo.
echo 1. Open browser and navigate to Production page
echo 2. Click "Export PDF" -^> "Laporan Produksi"
echo 3. Verify in PDF table:
echo    - HPP/Unit column shows: Rp X.XXX
echo    - Total Biaya column shows: Rp XXX.XXX.XXX
echo 4. Both columns should have Rupiah format with thousand separator
echo.

echo ============================================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ============================================================
echo.
echo HPP/Unit and Total Biaya columns now display in Rupiah format.
echo Please test the PDF export in the browser.
echo.
pause
