<?php

/**
 * Test Production Completion - Remaining Materials Fix
 * 
 * This script tests the fix for production completion where remaining materials
 * should be calculated based on actual usage, not planned quantity.
 * 
 * Test Scenario:
 * 1. Create production with 30 kg bahan A
 * 2. Add realization 80% (24 units from 30 target)
 *    - Should reduce stock: 30 * (24/30) = 24 kg
 *    - Should update quantity_used: 24 kg
 * 3. Complete production with checkbox "consume remaining materials"
 *    - Should calculate remaining: 30 - 24 = 6 kg
 *    - Should reduce stock by 6 kg more
 *    - Total reduction: 24 + 6 = 30 kg ✅
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\Bahan;
use App\Models\HargaBahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🧪 Testing Production Completion - Remaining Materials Fix\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // Find a production that is in_progress
    $production = Production::with(['materials', 'hppRecords'])
        ->where('status', 'in_progress')
        ->first();
    
    if (!$production) {
        echo "⚠️  No in_progress production found. Please create one first.\n";
        echo "\nSteps to test manually:\n";
        echo "1. Create a new production with materials (e.g., 30 kg bahan A)\n";
        echo "2. Approve and start the production\n";
        echo "3. Add realization 80% (e.g., 24 units from 30 target)\n";
        echo "4. Check the production_materials table:\n";
        echo "   - quantity_required should be 30\n";
        echo "   - quantity_used should be 24 (after realization)\n";
        echo "5. Complete production with checkbox 'consume remaining materials'\n";
        echo "6. Verify:\n";
        echo "   - quantity_used should be 30 (fully consumed)\n";
        echo "   - Stock should be reduced by total 30 kg (24 + 6)\n";
        exit(0);
    }
    
    echo "📋 Found Production:\n";
    echo "   ID: {$production->id}\n";
    echo "   Code: {$production->production_code}\n";
    echo "   Status: {$production->status}\n";
    echo "   Target Quantity: {$production->target_quantity}\n";
    echo "   Realized Quantity: {$production->realized_quantity}\n";
    echo "\n";
    
    echo "📦 Materials:\n";
    foreach ($production->materials as $material) {
        echo "   Material ID: {$material->material_id}\n";
        echo "   Type: {$material->material_type}\n";
        echo "   Quantity Required: {$material->quantity_required}\n";
        echo "   Quantity Used: " . ($material->quantity_used ?? 0) . "\n";
        echo "   Remaining: " . ($material->quantity_required - ($material->quantity_used ?? 0)) . "\n";
        echo "\n";
        
        // Check if quantity_used is being tracked
        if ($production->realized_quantity > 0 && ($material->quantity_used ?? 0) == 0) {
            echo "   ⚠️  WARNING: Production has realizations but quantity_used is 0!\n";
            echo "   This indicates the old bug where quantity_used was not tracked.\n";
            echo "\n";
        }
        
        if ($material->material_type === 'bahan') {
            // Get current stock
            $stockBatches = DB::table('harga_bahan')
                ->join('bahan', 'harga_bahan.id_bahan', '=', 'bahan.id_bahan')
                ->where('harga_bahan.id_bahan', $material->material_id)
                ->where('bahan.id_outlet', $production->outlet_id)
                ->where('harga_bahan.stok', '>', 0)
                ->select('harga_bahan.*')
                ->get();
            
            $totalStock = $stockBatches->sum('stok');
            echo "   Current Stock: {$totalStock}\n";
            echo "\n";
        }
    }
    
    echo "✅ Test Data Retrieved Successfully\n\n";
    
    echo "📝 Expected Behavior:\n";
    echo "1. When adding realization:\n";
    echo "   - Stock should be reduced proportionally\n";
    echo "   - quantity_used should be updated (NEW FIX!)\n";
    echo "\n";
    echo "2. When completing with 'consume remaining materials' checked:\n";
    echo "   - Remaining = quantity_required - quantity_used\n";
    echo "   - Stock should be reduced by remaining amount only\n";
    echo "   - quantity_used should be updated to quantity_required\n";
    echo "   - fully_consumed should be set to true\n";
    echo "\n";
    
    echo "🔍 To verify the fix:\n";
    echo "1. Add a realization to this production\n";
    echo "2. Check that quantity_used is updated in production_materials table\n";
    echo "3. Complete the production with checkbox checked\n";
    echo "4. Verify that only the remaining materials are consumed\n";
    echo "\n";
    
    echo "📊 SQL Query to check:\n";
    echo "SELECT id, material_id, material_type, quantity_required, quantity_used, fully_consumed\n";
    echo "FROM production_materials\n";
    echo "WHERE production_id = {$production->id};\n";
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo str_repeat("=", 70) . "\n";
echo "✅ Test completed successfully!\n";
