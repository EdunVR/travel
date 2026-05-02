@echo off
echo ========================================
echo DEPLOY MARGIN REPORT LOGO FINAL FIX
echo ========================================
echo.

echo [1/6] Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [2/6] Setting up storage...
php artisan storage:link

echo.
echo [3/6] Creating test logo...
php create_test_logo.php

echo.
echo [4/6] Setting up company settings...
echo Please run ONE of these commands to setup company settings:
echo   Option A: php setup_company_settings.php
echo   Option B: Import setup_company_settings.sql via phpMyAdmin
echo.
pause

echo.
echo [5/6] Testing logo data...
php test_margin_logo_data.php

echo.
echo [6/6] Deployment completed!
echo.
echo CHANGES APPLIED:
echo - ✓ Added HasCompanySettings trait to MarginReportController
echo - ✓ Added debug logging to controller
echo - ✓ Enhanced debug info in PDF template
echo - ✓ Created storage directory and symlink
echo - ✓ Created test logo file
echo - ✓ Provided company settings setup scripts
echo.
echo TESTING STEPS:
echo 1. Setup company settings (if not done yet)
echo 2. Test logo URL: http://localhost/storage/logos/test-logo.png
echo 3. Go to Laporan Margin and export PDF
echo 4. Check debug info in PDF source
echo 5. Verify logo appears in header
echo.
echo TROUBLESHOOTING:
echo - Check Laravel logs: tail -f storage/logs/laravel.log
echo - View PDF source for debug comments
echo - Compare with inter-outlet PDF export
echo.
pause