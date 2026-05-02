<?php

/**
 * Update existing HPP records to use consistent FIFO calculation
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use App\Models\HppProduk;
use Illuminate\Support\Facades\DB;

echo "🔄 UPDATING EXISTING HPP RECORDS\n";
echo "================================\n\n";

// Get all productions with HPP records
$productions = Production::with(['materials', 'laborCosts', 'operationalCosts', 'hppRecords'])
    ->whereHas('hppRecords')
    ->get();

$updated = 0;
$total = $productions->count();

foreach ($productions as $production) {
    echo "Processing: {$production->production_code}...";
    
    try {
        // Calculate HPP using FIFO method
        $materialCost = $production->materials->sum(function($material) {
            return $material->quantity_required * getFifoPrice($material->material_id, $material->material_type);
        });
        
        $laborCost = $production->laborCosts->sum(function($labor) {
            return $labor->worker_count * $labor->cost_per_worker;
        });
        
        $operationalCost = $production->operationalCosts->sum('amount');
        $totalCost = $materialCost + $laborCost + $operationalCost;
        $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
        
        // Update all HPP records for this production
        foreach ($production->hppRecords as $hppRecord) {
            $oldHpp = $hppRecord->hpp;
            $hppRecord->hpp = $hppPerUnit;
            $hppRecord->save();
            
            echo " Updated HPP: Rp " . number_format($oldHpp) . " → Rp " . number_format($hppPerUnit);
        }
        
        echo " ✅\n";
        $updated++;
        
    } catch (Exception $e) {
        echo " ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n📊 SUMMARY:\n";
echo "Total productions: {$total}\n";
echo "Updated successfully: {$updated}\n";
echo "✅ Update completed!\n";

// Helper function
function getFifoPrice($materialId, $materialType = 'bahan')
{
    if ($materialType === 'bahan') {
        // Get FIFO price from harga_bahan table (oldest first)
        $hargaBahan = DB::table('harga_bahan')
            ->where('id_bahan', $materialId)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO order
            ->first();
        
        if ($hargaBahan) {
            return $hargaBahan->harga_beli;
        }
        
        // Fallback to base price from bahan table
        $bahanData = \App\Models\Bahan::find($materialId);
        return $bahanData ? $bahanData->harga_beli : 0;
    } else {
        // For produk type materials
        $produk = \App\Models\Produk::find($materialId);
        if ($produk && method_exists($produk, 'calculateHpp')) {
            return $produk->calculateHpp() ?? 0;
        }
        return 0;
    }
}
