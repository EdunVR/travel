<?php

/**
 * Clean Production Duplicates Manually
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use Illuminate\Support\Facades\DB;

echo "🧹 CLEANING PRODUCTION DUPLICATES\n";
echo "=================================\n\n";

// Get duplicates
$duplicates = DB::table('productions')
    ->select('production_code', DB::raw('COUNT(*) as count'))
    ->groupBy('production_code')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "✅ No duplicates found\n";
    exit;
}

$cleanedCount = 0;
foreach ($duplicates as $duplicate) {
    echo "🧹 Cleaning {$duplicate->production_code} ({$duplicate->count} records)...\n";
    
    $productions = Production::where('production_code', $duplicate->production_code)
        ->orderBy('created_at')
        ->get();
    
    // Keep the first one, delete the rest
    $keepFirst = true;
    foreach ($productions as $production) {
        if ($keepFirst) {
            echo "   ✅ Keeping ID: {$production->id} (created: {$production->created_at})\n";
            $keepFirst = false;
        } else {
            echo "   🗑️ Deleting ID: {$production->id} (created: {$production->created_at})\n";
            
            try {
                // Delete related records first
                DB::table('hpp_produk')->where('production_id', $production->id)->delete();
                DB::table('production_materials')->where('production_id', $production->id)->delete();
                DB::table('production_labor_costs')->where('production_id', $production->id)->delete();
                DB::table('production_operational_costs')->where('production_id', $production->id)->delete();
                DB::table('production_realizations')->where('production_id', $production->id)->delete();
                
                // Delete the production
                $production->delete();
                $cleanedCount++;
                echo "   ✅ Deleted successfully\n";
            } catch (Exception $e) {
                echo "   ❌ Error deleting: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n";
}

echo "✅ Cleaned {$cleanedCount} duplicate records\n";

// Verify no duplicates remain
echo "\n🔍 Verifying cleanup...\n";
$remainingDuplicates = DB::table('productions')
    ->select('production_code', DB::raw('COUNT(*) as count'))
    ->groupBy('production_code')
    ->having('count', '>', 1)
    ->get();

if ($remainingDuplicates->isEmpty()) {
    echo "✅ No duplicates remaining - ready for unique constraint\n";
} else {
    echo "⚠️ Still have duplicates:\n";
    foreach ($remainingDuplicates as $duplicate) {
        echo "   - {$duplicate->production_code}: {$duplicate->count} records\n";
    }
}