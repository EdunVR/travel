<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== OPENING BALANCES TABLE STRUCTURE ===\n\n";

try {
    $columns = DB::select('SHOW COLUMNS FROM opening_balances');
    echo "opening_balances table structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nSample data (first 5 records):\n";
    $samples = DB::table('opening_balances')->limit(5)->get();
    foreach ($samples as $sample) {
        echo "  ID: {$sample->id}";
        if (isset($sample->account_code)) echo ", Account: {$sample->account_code}";
        if (isset($sample->account_id)) echo ", Account ID: {$sample->account_id}";
        if (isset($sample->book_id)) echo ", Book: {$sample->book_id}";
        if (isset($sample->accounting_book_id)) echo ", Accounting Book: {$sample->accounting_book_id}";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "\n=== CHECK COMPLETED ===\n";