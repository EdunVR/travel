<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING OPENING BALANCE TABLES ===\n\n";

try {
    // Check if account_opening_balances table exists
    echo "1. CHECKING account_opening_balances TABLE...\n";
    if (Schema::hasTable('account_opening_balances')) {
        echo "   ✅ account_opening_balances table exists\n";
        
        // Show table structure
        $columns = DB::select("SHOW COLUMNS FROM account_opening_balances");
        echo "   Table structure:\n";
        foreach ($columns as $column) {
            echo "      - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "   ❌ account_opening_balances table does NOT exist\n";
    }

    // Look for similar tables
    echo "\n2. LOOKING FOR SIMILAR TABLES...\n";
    $tables = DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . env('DB_DATABASE');

    echo "   Tables containing 'opening' or 'balance':\n";
    $foundSimilar = false;
    foreach ($tables as $table) {
        $tableName = $table->{$tableColumn};
        if (stripos($tableName, 'opening') !== false || stripos($tableName, 'balance') !== false) {
            echo "      - {$tableName}\n";
            $foundSimilar = true;
        }
    }
    
    if (!$foundSimilar) {
        echo "      No similar tables found\n";
    }

    // Look for saldo awal related tables
    echo "\n3. LOOKING FOR SALDO AWAL RELATED TABLES...\n";
    echo "   Tables containing 'saldo':\n";
    $foundSaldo = false;
    foreach ($tables as $table) {
        $tableName = $table->{$tableColumn};
        if (stripos($tableName, 'saldo') !== false) {
            echo "      - {$tableName}\n";
            $foundSaldo = true;
        }
    }
    
    if (!$foundSaldo) {
        echo "      No saldo related tables found\n";
    }

    // Check journal entries for opening balance patterns
    echo "\n4. CHECKING JOURNAL ENTRIES FOR OPENING BALANCE PATTERNS...\n";
    
    if (Schema::hasTable('journal_entries')) {
        echo "   ✅ journal_entries table exists\n";
        
        // Look for opening balance journals
        $openingJournals = DB::table('journal_entries')
            ->where(function($query) {
                $query->where('description', 'like', '%saldo awal%')
                      ->orWhere('description', 'like', '%opening balance%')
                      ->orWhere('reference_type', 'like', '%opening%');
            })
            ->limit(5)
            ->get();
        
        echo "   Found " . $openingJournals->count() . " potential opening balance journals:\n";
        foreach ($openingJournals as $journal) {
            echo "      - ID: {$journal->id}, Desc: {$journal->description}\n";
        }
    } else {
        echo "   ❌ journal_entries table does NOT exist\n";
    }

    // Show all finance-related tables
    echo "\n5. ALL FINANCE-RELATED TABLES...\n";
    $financeKeywords = ['journal', 'account', 'chart', 'finance', 'book', 'entry'];
    
    foreach ($tables as $table) {
        $tableName = $table->{$tableColumn};
        foreach ($financeKeywords as $keyword) {
            if (stripos($tableName, $keyword) !== false) {
                echo "   - {$tableName}\n";
                break;
            }
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "\n=== CHECK COMPLETED ===\n";