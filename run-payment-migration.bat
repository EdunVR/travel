@echo off
cd C:\xampp\htdocs\hm
php artisan migrate --path=database/migrations/2026_04_11_000001_add_bukti_transfer_to_jamaah_payments.php --force
pause
