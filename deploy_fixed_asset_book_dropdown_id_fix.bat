@echo off
echo ========================================
echo DEPLOYING FIXED ASSET BOOK DROPDOWN ID FIX
echo ========================================
echo.

echo Step 1: Testing the dropdown ID conflict fix...
php test_book_dropdown_fix.php
if %errorlevel% neq 0 (
    echo ERROR: Test failed
    pause
    exit /b 1
)
echo.

echo Step 2: Clearing application cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo Cache cleared successfully.
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. Fixed HTML ID conflict between filter and modal dropdowns
echo 2. Changed modal dropdown ID from 'book_id' to 'modal_book_id'
echo 3. Updated JavaScript edit function to use correct modal ID
echo 4. setDefaultBookId() already used correct name selector
echo.
echo ROOT CAUSE:
echo - Filter dropdown: id="book_id" (uses $books)
echo - Modal dropdown: id="book_id" (uses $booksActive) 
echo - JavaScript $('#book_id').val() targeted filter, not modal
echo - User saw correct data in console but modal showed wrong state
echo.
echo SOLUTION:
echo - Filter dropdown: id="book_id" (unchanged)
echo - Modal dropdown: id="modal_book_id" (changed)
echo - Edit function: $('#modal_book_id').val() (updated)
echo - setDefaultBookId: uses name selector (already correct)
echo.
echo TESTING INSTRUCTIONS:
echo 1. Clear browser cache (Ctrl+F5)
echo 2. Switch to Dahana outlet
echo 3. Open "Tambah Aktiva Tetap" modal
echo 4. Should show "BUKU DAHANA 2026" selected
echo 5. Should NOT show "Pilih Tahun Buku"
echo 6. Test PBU outlet with multiple books
echo 7. Verify edit functionality works correctly
echo.
pause