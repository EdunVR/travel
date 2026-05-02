<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Kolom tabel produk:\n";
$columns = DB::select('DESCRIBE produk');
foreach($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}
?>