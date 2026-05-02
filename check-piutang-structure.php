<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PIUTANG TABLE STRUCTURE ===\n";
$columns = DB::select('DESCRIBE piutang');
foreach ($columns as $col) {
    echo $col->Field . ' - ' . $col->Type . ' - ' . $col->Null . ' - ' . $col->Key . "\n";
}

echo "\n=== SAMPLE PIUTANG DATA ===\n";
$piutang = DB::table('piutang')->where('source_type', 'travel')->first();
if ($piutang) {
    print_r($piutang);
}
