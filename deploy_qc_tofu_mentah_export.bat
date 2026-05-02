@echo off
echo ========================================
echo DEPLOYING QC EGG TOFU MENTAH EXPORT
echo ========================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/5] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [3/5] Checking created files...
if exist "resources\views\admin\produksi\produksi\qc-tofu-mentah-pdf.blade.php" (
    echo ✓ QC Tofu Mentah PDF template created
) else (
    echo ✗ QC Tofu Mentah PDF template missing
)

if exist "test_qc_tofu_mentah_export.php" (
    echo ✓ Test script created
) else (
    echo ✗ Test script missing
)

echo.
echo [4/5] Testing routes...
php artisan route:list --name=qc-tofu-mentah

echo.
echo [5/5] Running functionality test...
php test_qc_tofu_mentah_export.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo FITUR QC EGG TOFU MENTAH EXPORT:
echo ✓ Header profesional dengan logo perusahaan
echo ✓ Informasi dokumen (PNI/FSOP/QC/01-2, Revisi 00)
echo ✓ Struktur tabel sesuai form QC industri
echo ✓ Data diambil dari kolom tofu_data (JSON)
echo ✓ Header perusahaan dari tabel CompanySetting
echo ✓ Periode laporan otomatis
echo ✓ Format landscape A4
echo ✓ Font Times New Roman formal
echo ✓ Filename: qc_tofu_mentah_{bulan}_{tahun}.pdf
echo.
echo STRUKTUR TABEL:
echo - No, Tanggal Produksi, Kode Produk
echo - Perendaman Kacang Kedelai (Waktu, Kuantitas)
echo - Jumlah Reject Telur (Kuantitas)
echo - Pasteurisasi (Waktu, Suhu °C)
echo - Berat Akhir Sari Kedelai (Kuantitas)
echo - Pencampuran (Waktu)
echo - Filling & Pengemasan (Waktu, Kuantitas, Mesin 1, Mesin 2)
echo - Total Kuantitas, Jumlah Reject Mentah
echo.
echo MAPPING DATA JSON:
echo - perendaman_waktu → Perendaman Waktu
echo - perendaman_kuantitas → Perendaman Kuantitas
echo - reject_telur_kuantitas → Reject Telur
echo - pasteurisasi_waktu → Pasteurisasi Waktu
echo - pasteurisasi_suhu → Pasteurisasi Suhu
echo - berat_akhir_sari_kedelai → Berat Akhir Sari Kedelai
echo - pencampuran_waktu → Pencampuran Waktu
echo - filling_waktu → Filling Waktu
echo - filling_kuantitas → Filling Kuantitas
echo - filling_mesin_1 → Mesin 1
echo - filling_mesin_2 → Mesin 2
echo - total_kuantitas → Total Kuantitas
echo - jumlah_reject_mentah → Jumlah Reject Mentah
echo.
echo TESTING:
echo 1. Buka halaman Produksi
echo 2. Klik dropdown "Export PDF"
echo 3. Pilih "QC Egg Tofu Mentah"
echo 4. Verifikasi format PDF sesuai form QC
echo 5. Periksa header perusahaan dan data mapping
echo.
pause