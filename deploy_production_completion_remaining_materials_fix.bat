@echo off
echo ========================================
echo Production Completion - Remaining Materials Fix
echo ========================================
echo.

echo [1/3] Testing current production data...
php test_production_completion_remaining_materials.php
if errorlevel 1 (
    echo.
    echo Error testing production data!
    pause
    exit /b 1
)

echo.
echo [2/3] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [3/3] Deployment Summary
echo ========================================
echo.
echo Fixed Files:
echo   - app/Http/Controllers/ProductionController.php
echo     * Updated reduceMaterialStock() to track quantity_used
echo.
echo What Changed:
echo   - When realization is added, quantity_used is now updated
echo   - When completing with "consume remaining" checked:
echo     * System calculates: remaining = planned - used
echo     * Only remaining materials are consumed
echo.
echo Example:
echo   - Planned: 30 kg bahan A
echo   - After 80%% realization: 24 kg used
echo   - When completing: 6 kg remaining consumed
echo   - Total: 30 kg (correct!)
echo.
echo ========================================
echo.
echo Next Steps:
echo 1. Test by creating a new production
echo 2. Add realization (e.g., 80%%)
echo 3. Check production_materials.quantity_used is updated
echo 4. Complete with checkbox checked
echo 5. Verify only remaining materials are consumed
echo.
echo ========================================
echo Deployment Complete!
echo ========================================
pause
