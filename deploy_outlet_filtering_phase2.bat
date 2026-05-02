@echo off
echo ========================================
echo OUTLET ACCESS CONTROL - PHASE 2 DEPLOYMENT
echo Query Filtering Implementation
echo ========================================
echo.

echo Step 1: Backup current state...
php -r "copy('.env', '.env.phase2.backup');"
echo   - Environment backed up
echo.

echo Step 2: Run analysis...
php implement_query_filtering_phase2.php
if %ERRORLEVEL% NEQ 0 (
    echo   ERROR: Analysis failed!
    pause
    exit /b 1
)
echo   - Analysis complete
echo.

echo Step 3: Run tests before changes...
php test_outlet_filtering.php
echo   - Pre-deployment test complete
echo.

echo Step 4: Clear cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo   - Cache cleared
echo.

echo ========================================
echo PHASE 2 DEPLOYMENT COMPLETE
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Review OUTLET_ACCESS_PHASE2_COMPLETE.md
echo 2. Manually update high priority controllers
echo 3. Test each controller after update
echo 4. Run: php test_outlet_filtering.php
echo 5. Deploy to production when all tests pass
echo.
echo IMPORTANT:
echo - Backup files created with .phase2.backup extension
echo - Review all changes before production deployment
echo - Test with different user roles
echo.

pause
