<?php

/**
 * Debug Production Completion Issue
 * 
 * This script helps debug why stock reduction is still wrong
 * when completing production with "consume remaining materials" checkbox
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use App\Models\ProductionMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 Debug Production Completion Issue\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // Find the most recent production
    $production = Production::with(['materials', 'realizations'])
        ->orderBy('id', 'desc')
        ->first();
    
    if (!$production) {
        echo "❌ No production found in database\n";
        exit(1);
    }
    
    echo "📋 Production Details:\n";
    echo "   ID: {$production->id}\n";
    echo "   Code: {$production->production_code}\n";
    echo "   Status: {$production->status}\n";
    echo "   Target Quantity: {$production->target_quantity}\n";
    echo "   Realized Quantity: {$production->realized_quantity}\n";
    echo "   Outlet ID: {$production->outlet_id}\n";
    echo "\n";
    
    // Check realizations
    echo "📊 Realizations:\n";
    if ($production->realizations->count() > 0) {
        foreach ($production->realizations as $realization) {
            echo "   - Quantity Produced: {$realization->quantity_produced}\n";
            echo "     Date: {$realization->realization_date}\n";
        }
    } else {
        echo "   No realizations yet\n";
    }
    echo "\n";
    
    // Check materials
    echo "📦 Materials:\n";
    if ($production->materials->count() > 0) {
        foreach ($production->materials as $material) {
            echo "   Material ID: {$material->material_id}\n";
            echo "   Type: {$material->material_type}\n";
            echo "   Quantity Required: {$material->quantity_required}\n";
            echo "   Quantity Used: " . ($material->quantity_used ?? 'NULL') . "\n";
            echo "   Fully Consumed: " . ($material->fully_consumed ? 'true' : 'false') . "\n";
            
            // Calculate what should happen
            $plannedQty = $material->quantity_required;
            $usedQty = $material->quantity_used ?? 0;
            $remainingQty = $plannedQty - $usedQty;
            
            echo "   📊 Calculation:\n";
            echo "      Planned: {$plannedQty}\n";
            echo "      Used: {$usedQty}\n";
            echo "      Remaining: {$remainingQty}\n";
            
            if ($material->material_type === 'bahan') {
                // Check current stock
                $stockBatches = DB::table('harga_bahan')
                    ->join('bahan', 'harga_bahan.id_bahan', '=', 'bahan.id_bahan')
                    ->where('harga_bahan.id_bahan', $material->material_id)
                    ->where('bahan.id_outlet', $production->outlet_id)
                    ->select('harga_bahan.*', 'bahan.nama_bahan')
                    ->get();
                
                $totalStock = $stockBatches->sum('stok');
                echo "   📦 Current Stock: {$totalStock}\n";
                
                if ($stockBatches->count() > 0) {
                    echo "   Stock Batches:\n";
                    foreach ($stockBatches as $batch) {
                        echo "      - Batch ID: {$batch->id}, Stock: {$batch->stok}, Date: {$batch->created_at}\n";
                    }
                }
            }
            
            echo "\n";
        }
    } else {
        echo "   No materials found\n";
    }
    echo "\n";
    
    // Check if quantity_used is being tracked
    echo "🔍 Analysis:\n";
    $hasRealizations = $production->realizations->count() > 0;
    $hasQuantityUsed = false;
    
    foreach ($production->materials as $material) {
        if (($material->quantity_used ?? 0) > 0) {
            $hasQuantityUsed = true;
            break;
        }
    }
    
    if ($hasRealizations && !$hasQuantityUsed) {
        echo "   ⚠️  WARNING: Production has realizations but quantity_used is 0!\n";
        echo "   This means the fix is NOT working properly.\n";
        echo "\n";
        echo "   Possible causes:\n";
        echo "   1. Cache not cleared after code update\n";
        echo "   2. Code not deployed properly\n";
        echo "   3. Realization was added before the fix\n";
        echo "\n";
        echo "   Solutions:\n";
        echo "   1. Clear cache: php artisan cache:clear\n";
        echo "   2. Add a new realization to test the fix\n";
        echo "   3. Check ProductionController.php has the updated code\n";
    } elseif ($hasRealizations && $hasQuantityUsed) {
        echo "   ✅ quantity_used is being tracked correctly!\n";
        echo "\n";
        echo "   When completing with checkbox:\n";
        foreach ($production->materials as $material) {
            $remaining = $material->quantity_required - ($material->quantity_used ?? 0);
            echo "   - Material {$material->material_id}: Will reduce {$remaining} more\n";
        }
    } else {
        echo "   ℹ️  No realizations yet. Add a realization to test.\n";
    }
    echo "\n";
    
    // Check the controller code
    echo "🔍 Checking Controller Code:\n";
    $controllerPath = __DIR__ . '/app/Http/Controllers/ProductionController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, "material->update(['quantity_used' => \$newQuantityUsed])") !== false ||
        strpos($controllerContent, 'material->update([\'quantity_used\' => $newQuantityUsed])') !== false) {
        echo "   ✅ Fix code found in ProductionController.php\n";
    } else {
        echo "   ❌ Fix code NOT found in ProductionController.php\n";
        echo "   The controller needs to be updated!\n";
    }
    echo "\n";
    
    echo str_repeat("=", 70) . "\n";
    echo "✅ Debug completed\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
