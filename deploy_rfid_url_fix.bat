@echo off
echo ========================================
echo DEPLOYING RFID URL FIX
echo ========================================

echo.
echo [1/3] Clearing view cache...
php artisan view:clear
php artisan cache:clear

echo.
echo [2/3] Testing corrected URLs...
php test_frontend_url_fix.php

echo.
echo [3/3] Verifying API endpoints...
php test_rfid_api_manual.php

echo.
echo ========================================
echo URL FIX DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo PERUBAHAN YANG DILAKUKAN:
echo ✅ JavaScript URL diperbaiki menggunakan Laravel url() helper
echo ✅ fetch('/api/morra/api/rfid/mode') → fetch('{{ url("/api/morra/api/rfid/mode") }}')
echo ✅ fetch('/api/detected-rfid-uid') → fetch('{{ url("/api/detected-rfid-uid") }}')
echo.
echo URL YANG BENAR SEKARANG:
echo - https://poshan.my.id/tofu/api/morra/api/rfid/mode
echo - https://poshan.my.id/tofu/api/detected-rfid-uid
echo.
echo TESTING STEPS:
echo 1. Clear browser cache (Ctrl+F5 atau Ctrl+Shift+R)
echo 2. Buka /admin/sdm/kepegawaian
echo 3. Klik "Tambah Karyawan"
echo 4. Klik tombol "Mulai Deteksi"
echo 5. Check browser console - tidak boleh ada error 404
echo 6. Tempelkan kartu RFID ke ESP32 CAM
echo 7. UID harus otomatis terisi di form
echo.
pause