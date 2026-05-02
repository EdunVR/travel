@echo off
REM ============================================
REM Travel Module Activation Script (Windows)
REM ============================================
REM Script ini mengaktifkan modul Travel Management
REM dengan seed permission dan setup awal

echo.
echo ============================================
echo Travel Module Activation Script
echo ============================================
echo.

REM Step 1: Seed Travel Permissions
echo Step 1: Seeding Travel Permissions...
php artisan db:seed --class=TravelPermissionSeeder

if %errorlevel% equ 0 (
    echo [OK] Travel permissions seeded successfully!
) else (
    echo [ERROR] Failed to seed travel permissions
    exit /b 1
)

echo.

REM Step 2: Seed Workflow Stages
echo Step 2: Seeding Workflow Stages...
php artisan db:seed --class=WorkflowStageSeeder

if %errorlevel% equ 0 (
    echo [OK] Workflow stages seeded successfully!
) else (
    echo [WARNING] Workflow stages may already exist
)

echo.

REM Step 3: Seed Teams
echo Step 3: Seeding Teams...
php artisan db:seed --class=TeamSeeder

if %errorlevel% equ 0 (
    echo [OK] Teams seeded successfully!
) else (
    echo [WARNING] Teams may already exist
)

echo.

REM Step 4: Clear Cache
echo Step 4: Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo [OK] Cache cleared!

echo.

REM Step 5: Verify Installation
echo Step 5: Verifying installation...
php artisan tinker --execute="echo 'Travel Permissions: ' . App\Models\Permission::where('module', 'travel')->count();"

echo.
echo ============================================
echo Travel Module Activation Complete!
echo ============================================
echo.
echo Next Steps:
echo    1. Logout from the application
echo    2. Login again
echo    3. Check sidebar for 'Travel Management' menu
echo.
echo Documentation:
echo    - Quick Start: tests\UAT\QUICK_START.md
echo    - User Guide: docs\user-guide\README.md
echo    - Activation Guide: TRAVEL_MODULE_ACTIVATION_GUIDE.md
echo.
echo Optional: Seed test data
echo    php artisan db:seed --class=UATDataSeeder
echo.

pause
