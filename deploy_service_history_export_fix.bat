@echo off
echo ========================================
echo 🚀 Deploying Service History Export Fix
echo ========================================
echo.

echo 📋 Fix Summary:
echo - Updated exportHistory method to support multiple outlets
echo - Updated exportHistoryPdf method to support multiple outlets  
echo - Added outlet access validation
echo - Maintained backward compatibility
echo - Export data now matches filtered table data
echo.

echo 🧪 Running tests...
php test_service_history_export_fix.php
echo.

if %ERRORLEVEL% EQU 0 (
    echo ✅ All tests passed!
    echo.
    echo 🔄 Clearing cache...
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo.
    echo 🎉 Service History Export Fix deployed successfully!
    echo.
    echo 📝 What's Fixed:
    echo - Export Excel now respects outlet filter selection
    echo - Export PDF now respects outlet filter selection
    echo - Export data matches exactly what's shown in the table
    echo - All status filters (Semua, Menunggu, Lunas, etc.) work in export
    echo - Date range filters work in export
    echo - Multiple outlet selection supported
    echo - Single outlet backward compatibility maintained
    echo.
    echo 🧪 Test Instructions:
    echo 1. Go to Service ^> History Service
    echo 2. Select specific outlets using outlet dropdown
    echo 3. Apply status filter (e.g., Menunggu, Lunas)
    echo 4. Set date range if needed
    echo 5. Click Export Excel or Export PDF
    echo 6. Verify exported data matches table data exactly
    echo.
) else (
    echo ❌ Tests failed! Please check the issues above.
    pause
    exit /b 1
)

echo ✅ Deployment Complete!
pause