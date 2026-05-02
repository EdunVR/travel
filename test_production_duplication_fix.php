<?php

/**
 * Test Production Duplication Fix
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use Illuminate\Support\Facades\DB;

echo "🧪 TESTING PRODUCTION DUPLICATION FIX\n";
echo "====================================\n\n";

// Test 1: Check for remaining duplicates
echo "1️⃣ Checking for remaining duplicates...\n";
$duplicates = DB::table('productions')
    ->select('production_code', DB::raw('COUNT(*) as count'))
    ->groupBy('production_code')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "   ✅ No duplicates found\n";
} else {
    echo "   ⚠️ Still have duplicates:\n";
    foreach ($duplicates as $duplicate) {
        echo "      - {$duplicate->production_code}: {$duplicate->count} records\n";
    }
}

// Test 2: Test unique constraint
echo "\n2️⃣ Testing unique constraint...\n";
try {
    // Try to create duplicate production code
    $testCode = 'TEST-DUPLICATE-' . time();
    
    Production::create([
        'outlet_id' => 1,
        'production_code' => $testCode,
        'production_line' => 'Test Line',
        'target_quantity' => 100,
        'start_date' => now(),
        'end_date' => now()->addDay(),
        'status' => 'draft',
        'created_by' => 1,
    ]);
    
    // Try to create the same code again
    Production::create([
        'outlet_id' => 1,
        'production_code' => $testCode,
        'production_line' => 'Test Line 2',
        'target_quantity' => 200,
        'start_date' => now(),
        'end_date' => now()->addDay(),
        'status' => 'draft',
        'created_by' => 1,
    ]);
    
    echo "   ❌ Unique constraint not working - duplicate created\n";
    
    // Cleanup
    Production::where('production_code', $testCode)->delete();
    
} catch (Exception $e) {
    echo "   ✅ Unique constraint working - duplicate prevented\n";
    echo "      Error: " . $e->getMessage() . "\n";
    
    // Cleanup any created record
    Production::where('production_code', $testCode)->delete();
}

echo "\n✅ Test completed!\n";
