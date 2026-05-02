@echo off
echo ========================================
echo DEPLOYING FIXED ASSET AUTO-SELECT BOOK FIX
echo ========================================
echo.

echo Step 1: Testing database connection and outlet-book relationships...
php debug_outlet_book_relationship.php
if %errorlevel% neq 0 (
    echo ERROR: Database connection failed
    pause
    exit /b 1
)
echo.

echo Step 2: Testing the fix logic...
php test_fixed_asset_auto_select_book.php
if %errorlevel% neq 0 (
    echo ERROR: Test failed
    pause
    exit /b 1
)
echo.

echo Step 3: Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo Cache cleared successfully.
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. FixedAssetController now uses HasOutletFilter trait
echo 2. Proper outlet detection using getSelectedOutlet() method
echo 3. Books are filtered by current outlet context
echo 4. Single-book outlets will auto-select the book
echo 5. No more "Buku tidak dipilih" error
echo.
echo TESTING INSTRUCTIONS:
echo 1. Login to the application
echo 2. Switch to PBU outlet (should see 2 books in filter)
echo 3. Open "Tambah Aktiva Tetap" modal - should show both books
echo 4. Switch to Dahana outlet (should see 1 book in filter)  
echo 5. Open "Tambah Aktiva Tetap" modal - should auto-select the book
echo 6. Verify no "Buku tidak dipilih" message appears
echo.
pause