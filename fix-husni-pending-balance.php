<?php

/**
 * Fix husni pending balance - remove excess Rp 950.000
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use Illuminate\Support\Facades\DB;

echo "=== FIX HUSNI PENDING BALANCE ===\n\n";

try {
    $husni = Affiliator::find(8);
    
    echo "=== BEFORE ===\n";
    echo "Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n\n";
    
    // Should be Rp 1.000.000 (Rp 500.000 + Rp 500.000)
    // Currently Rp 1.950.000
    // Excess: Rp 950.000
    
    $correctBalance = 1000000;
    $excess = $husni->pending_balance - $correctBalance;
    
    echo "Correct Balance: Rp " . number_format($correctBalance, 0, ',', '.') . "\n";
    echo "Excess: Rp " . number_format($excess, 0, ',', '.') . "\n\n";
    
    DB::beginTransaction();
    
    // Set to correct balance
    $husni->pending_balance = $correctBalance;
    $husni->save();
    
    DB::commit();
    
    echo "=== AFTER ===\n";
    $husni->refresh();
    echo "Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n";
    
    echo "\n✅ DONE!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
