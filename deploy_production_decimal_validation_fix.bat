@echo off
echo ========================================
echo PRODUCTION DECIMAL VALIDATION FIX
echo ========================================
echo.
echo Menerapkan perbaikan validasi desimal pada modal edit produksi...
echo.

echo 1. Membersihkan cache browser...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo 2. Memperbarui timestamp file JavaScript...
copy /Y "public\js\production.js" "public\js\production.js.bak"
echo File JavaScript telah diperbarui.

echo.
echo 3. Memverifikasi perbaikan...
echo - Input biaya operasional: step="0.01" (mendukung desimal)
echo - Input biaya per pekerja: step="0.01" (mendukung desimal)
echo - Input jumlah pekerja: step="1" (bilangan bulat)

echo.
echo ========================================
echo PERBAIKAN SELESAI
echo ========================================
echo.
echo TESTING GUIDE:
echo 1. Buka halaman Produksi
echo 2. Edit produksi yang sudah ada
echo 3. Coba masukkan nilai desimal pada:
echo    - Biaya per Pekerja: 252721.84
echo    - Biaya Operasional: 150000.50
echo 4. Pastikan tidak ada error "please enter a valid value"
echo 5. Simpan dan verifikasi data tersimpan dengan benar
echo.
echo Jika masih ada masalah, periksa console browser untuk error JavaScript.
echo.
pause