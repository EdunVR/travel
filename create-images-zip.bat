@echo off
echo ========================================
echo MEMBUAT ZIP FILE GAMBAR PAKET
echo ========================================
echo.

cd storage\app\public

echo Membuat zip file...
powershell Compress-Archive -Path travel-packages -DestinationPath ..\..\..\travel-packages-upload.zip -Force

cd ..\..\..

echo.
echo ========================================
echo SELESAI!
echo ========================================
echo.
echo File zip telah dibuat: travel-packages-upload.zip
echo.
echo Langkah selanjutnya:
echo 1. Upload file travel-packages-upload.zip ke server via cPanel File Manager
echo 2. Extract di folder: /home/username/public_html/storage/app/public/
echo 3. Set permissions: chmod -R 755 travel-packages/
echo.
echo Atau baca panduan lengkap di: CARA_UPLOAD_GAMBAR_PAKET.md
echo.
pause
