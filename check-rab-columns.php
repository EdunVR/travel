<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking rab_template columns ===\n\n";

$columns = Schema::getColumnListing('rab_template');

echo "Columns in rab_template table:\n";
foreach ($columns as $column) {
    echo "  - {$column}\n";
}

echo "\n";

// Get sample data
$sample = DB::table('rab_template')->first();
if ($sample) {
    echo "Sample RAB data:\n";
    foreach ($sample as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
}

echo "\n✓ Done!\n";
