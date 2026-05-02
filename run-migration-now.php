<?php
// Quick migration runner
chdir('C:/xampp/htdocs/hm');
echo "Running migration...\n";
passthru('php artisan migrate --path=database/migrations/2026_04_11_000001_add_bukti_transfer_to_jamaah_payments.php --force');
echo "\nDone!\n";
