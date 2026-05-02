@echo off
echo ========================================
echo DEPLOY SPAREPART IMPROVEMENTS FINAL
echo ========================================
echo.

echo [INFO] Deploying Sparepart improvements...
echo - Modal penyesuaian stok improvements
echo - Export data improvements  
echo - Auto-fill keterangan for tambah stok
echo - Optional keterangan field
echo - Wider adjust stock modal
echo.

echo [INFO] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [INFO] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [SUCCESS] Sparepart improvements deployed successfully!
echo.
echo IMPROVEMENTS IMPLEMENTED:
echo 1. ✓ Auto-fill keterangan for tambah stok (Service/Produksi)
echo 2. ✓ Keterangan field made optional
echo 3. ✓ Wider modal for better table display
echo 4. ✓ Auto-select "Data Terpilih" when items selected
echo 5. ✓ Export respects current sorting and filtering
echo 6. ✓ Include/exclude history detail log option
echo.
echo TESTING CHECKLIST:
echo [ ] Test modal penyesuaian stok - auto keterangan
echo [ ] Test export with selected items
echo [ ] Test export with/without history logs
echo [ ] Test wider modal display
echo [ ] Test optional keterangan field
echo.
pause