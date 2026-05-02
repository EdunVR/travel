@echo off
echo ========================================
echo DEPLOYING INTER OUTLET SEPARATE PRICING
echo ========================================
echo.

echo 1. Testing separate pricing implementation...
php test_inter_outlet_separate_pricing.php
if %errorlevel% neq 0 (
    echo ERROR: Separate pricing test failed
    pause
    exit /b 1
)

echo.
echo ========================================
echo DEPLOYMENT SUMMARY
echo ========================================
echo ✓ Created inter_outlet_product_prices table
echo ✓ Created InterOutletProductPrice model
echo ✓ Modified InterOutletSaleController for separate pricing
echo ✓ Updated frontend to show regular vs inter outlet prices
echo ✓ Modified getProducts to use inter outlet prices
echo ✓ Updated price settings modal with clear labeling
echo.
echo FEATURES:
echo - Harga inter outlet terpisah dari harga produk umum
echo - Fallback ke harga regular jika belum ada harga inter outlet
echo - Markup percentage tracking per outlet
echo - UI yang jelas membedakan harga regular dan inter outlet
echo - Tidak mempengaruhi harga produk di modul lain
echo.
echo NEXT STEPS:
echo 1. Test setting harga di browser
echo 2. Verify transaksi menggunakan harga inter outlet
echo 3. Confirm harga produk umum tidak berubah
echo.
echo Deployment completed successfully!
pause