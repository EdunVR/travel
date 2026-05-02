<?php

/**
 * Test HPP calculation consistency after fix
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use Illuminate\Support\Facades\DB;

echo "🧪 TESTING HPP CALCULATION CONSISTENCY\n";
echo "======================================\n\n";

// Test beberapa produksi
$productions = Production::with(['materials', 'laborCosts', 'operationalCosts', 'hppRecords'])
    ->whereHas('hppRecords')
    ->limit(3)
    ->get();

foreach ($productions as $production) {
    echo "📋 TESTING: {$production->production_code}\n";
    
    // Hitung HPP menggunakan FIFO (sama seperti di grid)
    $materialCost = $production->materials->sum(function($material) {
        return $material->quantity_required * getFifoPrice($material->material_id, $material->material_type);
    });
    
    $laborCost = $production->laborCosts->sum(function($labor) {
        return $labor->worker_count * $labor->cost_per_worker;
    });
    
    $operationalCost = $production->operationalCosts->sum('amount');
    $totalCost = $materialCost + $laborCost + $operationalCost;
    $calculatedHpp = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
    
    // HPP dari database
    $databaseHpp = $production->hppRecords->first()->hpp ?? 0;
    
    echo "   Calculated HPP: Rp " . number_format($calculatedHpp) . "\n";
    echo "   Database HPP: Rp " . number_format($databaseHpp) . "\n";
    
    $difference = abs($calculatedHpp - $databaseHpp);
    if ($difference < 1) {
        echo "   ✅ KONSISTEN (selisih: Rp " . number_format($difference) . ")\n";
    } else {
        echo "   ⚠️ TIDAK KONSISTEN (selisih: Rp " . number_format($difference) . ")\n";
    }
    
    echo "\n";
}

echo "✅ Test selesai!\n";

// Helper function
function getFifoPrice($materialId, $materialType = 'bahan')
{
    if ($materialType === 'bahan') {
        $hargaBahan = DB::table('harga_bahan')
            ->where('id_bahan', $materialId)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->first();
        
        if ($hargaBahan) {
            return $hargaBahan->harga_beli;
        }
        
        $bahanData = \App\Models\Bahan::find($materialId);
        return $bahanData ? $bahanData->harga_beli : 0;
    } else {
        $produk = \App\Models\Produk::find($materialId);
        if ($produk && method_exists($produk, 'calculateHpp')) {
            return $produk->calculateHpp() ?? 0;
        }
        return 0;
    }
}
