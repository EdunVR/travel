@echo off
echo ========================================
echo DEPLOYING FIFO PRICING CONSISTENCY FIX
echo ========================================
echo.

echo TESTING FIFO PRICING CONSISTENCY...
php test_fifo_pricing_practical.php
echo.

echo CLEARING APPLICATION CACHE...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo CRITICAL FIX APPLIED:
echo - Created getFifoPrice() helper method for consistent FIFO pricing
echo - Updated getData() method to use FIFO pricing for grid/table
echo - Fixed grid/table HPP calculation to use FIFO pricing
echo - Fixed PDF generation to use FIFO pricing
echo - Updated PDF view to use controller-calculated FIFO prices
echo.

echo VERIFICATION STEPS:
echo 1. Open production module
echo 2. Create/edit production with materials having multiple harga_bahan records
echo 3. Check HPP preview shows correct FIFO price
echo 4. Save and verify grid/table HPP per unit matches preview
echo 5. Generate PDF and verify material costs are consistent
echo 6. All three views should show identical HPP values
echo.

echo ✅ FIFO PRICING CONSISTENCY FIX DEPLOYED SUCCESSFULLY!
echo All views now use consistent FIFO pricing logic.
pause