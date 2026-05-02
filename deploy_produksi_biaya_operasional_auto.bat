@echo off
echo ========================================
echo DEPLOY FITUR BIAYA OPERASIONAL OTOMATIS
echo ========================================
echo.

echo [1/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [2/5] Testing database connection...
php artisan migrate:status

echo.
echo [3/5] Running test script...
php test_produksi_biaya_operasional_auto.php

echo.
echo [4/5] Checking file permissions...
if exist "resources\views\admin\produksi\produksi\index.blade.php" (
    echo ✅ View file exists and accessible
) else (
    echo ❌ View file not found!
    pause
    exit /b 1
)

echo.
echo [5/5] Verifying routes...
php artisan route:list | findstr "monthly-production-costs"

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo FITUR YANG TELAH DITAMBAHKAN (v1.1):
echo ✅ Auto calculation biaya operasional dari biaya bulanan
echo ✅ Form input jumlah hari kerja
echo ✅ Form input persentase gaji office (default 30%)
echo ✅ Breakdown biaya bulanan (listrik, air, bahan bakar, gaji office)
echo ✅ Auto-generated operational cost rows dengan persentase
echo ✅ Real-time calculation dan HPP preview integration
echo ✅ Auto update HPP preview saat aktivasi/clear auto calculation
echo ✅ Error handling dan fallback ke manual input
echo.
echo CARA PENGGUNAAN:
echo 1. Buka halaman Produksi
echo 2. Klik "Buat Produksi Baru"
echo 3. Pilih outlet yang memiliki data biaya bulanan
echo 4. Di bagian "Biaya Operasional", klik "Auto dari Biaya Bulanan"
echo 5. Masukkan jumlah hari kerja
echo 6. Atur persentase gaji office (default 30%)
echo 7. Review biaya yang dihitung otomatis
echo 8. HPP preview otomatis terupdate
echo 9. Simpan produksi
echo.
echo TESTING MANUAL:
echo - Jalankan: php test_produksi_biaya_operasional_auto.php
echo - Ikuti checklist di file PRODUKSI_BIAYA_OPERASIONAL_AUTO_COMPLETE.md
echo.
echo ========================================
pause