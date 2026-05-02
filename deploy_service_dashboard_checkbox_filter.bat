@echo off
echo ========================================
echo Deploying Service Management Dashboard Checkbox Filter
echo ========================================

echo.
echo [1/3] Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo [2/3] Testing Service Management Dashboard...
php test_service_dashboard_checkbox_filter.php

echo.
echo [3/3] Deployment Summary:
echo ✓ Service Management Dashboard updated with checkbox filter system
echo ✓ Multiple outlet selection support added
echo ✓ ServiceController updated to support multiple outlet IDs
echo ✓ Dynamic URL generation for filtered navigation
echo ✓ Real-time data loading with Alpine.js

echo.
echo ========================================
echo Service Management Dashboard Deployment Complete!
echo ========================================
echo.
echo Next Steps:
echo 1. Test the Service Management dashboard at /admin/service
echo 2. Verify outlet filtering works correctly
echo 3. Check that status counts update when outlets are selected
echo 4. Ensure navigation links include outlet filters
echo.

pause