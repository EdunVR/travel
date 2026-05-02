<?php

/**
 * Debug controller data mapping issue
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Production;

echo "========================================\n";
echo "DEBUG CONTROLLER DATA MAPPING\n";
echo "========================================\n\n";

try {
    // Get sample production with relations
    $production = Production::with([
        'outlet',
        'hppRecords.produk'
    ])->where('business_type', 'tofu')
      ->whereNotNull('tofu_data')
      ->first();
    
    if (!$production) {
        echo "❌ No tofu production found\n";
        exit(1);
    }
    
    echo "[DEBUG 1] Production details:\n";
    echo "  - ID: {$production->id}\n";
    echo "  - Code: {$production->production_code}\n";
    echo "  - Business Type: {$production->business_type}\n";
    echo "  - Start Date: {$production->start_date}\n";
    echo "  - Target Quantity: {$production->target_quantity}\n";
    echo "  - Realized Quantity: {$production->realized_quantity}\n";
    echo "\n";
    
    echo "[DEBUG 2] Raw tofu_data:\n";
    echo "  - Type: " . gettype($production->tofu_data) . "\n";
    echo "  - Length: " . strlen($production->tofu_data ?? '') . "\n";
    echo "  - Content: ";
    var_dump($production->tofu_data);
    echo "\n";
    
    echo "[DEBUG 3] JSON decode test:\n";
    $tofuData = [];
    if ($production->tofu_data) {
        $tofuData = json_decode($production->tofu_data, true) ?: [];
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "  - JSON Error: " . json_last_error_msg() . "\n";
        } else {
            echo "  - JSON decode SUCCESS\n";
            echo "  - Fields count: " . count($tofuData) . "\n";
            echo "  - Fields:\n";
            foreach ($tofuData as $key => $value) {
                echo "    * {$key}: " . ($value ?? 'NULL') . "\n";
            }
        }
    } else {
        echo "  - tofu_data is empty or null\n";
    }
    echo "\n";
    
    echo "[DEBUG 4] Product code generation:\n";
    $productCode = $production->production_code;
    if ($production->hppRecords && $production->hppRecords->isNotEmpty()) {
        echo "  - HPP Records found: " . $production->hppRecords->count() . "\n";
        $firstProduct = $production->hppRecords->first()->produk;
        if ($firstProduct && $firstProduct->kode_produk) {
            $productCode = $firstProduct->kode_produk;
            echo "  - Using product code: {$productCode}\n";
        } else {
            echo "  - No product code found, using production code\n";
        }
    } else {
        echo "  - No HPP records found, using production code\n";
    }
    echo "\n";
    
    echo "[DEBUG 5] Data mapping simulation:\n";
    $mappedData = [
        'no' => 1,
        'tanggal_produksi' => $production->start_date ? $production->start_date->format('d/m/Y') : '-',
        'kode_produk' => $productCode,
        
        // Perendaman Kacang Kedelai
        'perendaman_waktu' => $tofuData['perendaman_waktu'] ?? '-',
        'perendaman_kuantitas' => $tofuData['perendaman_qty'] ?? ($tofuData['perendaman_kuantitas'] ?? '-'),
        
        // Jumlah Reject Telur
        'reject_telur_kuantitas' => $tofuData['rijek_telur'] ?? ($tofuData['reject_telur_kuantitas'] ?? '-'),
        
        // Pasteurisasi
        'pasteurisasi_waktu' => $tofuData['pasteurisasi_waktu'] ?? '-',
        'pasteurisasi_suhu' => $tofuData['pasteurisasi_suhu'] ?? '-',
        
        // Berat Akhir Sari Kedelai
        'berat_akhir_sari_kedelai' => $tofuData['berat_sari_kedelai'] ?? ($tofuData['berat_akhir_sari_kedelai'] ?? '-'),
        
        // Pencampuran
        'pencampuran_waktu' => $tofuData['waktu_pencampuran'] ?? ($tofuData['pencampuran_waktu'] ?? '-'),
        
        // Filling & Pengemasan
        'filling_waktu' => $tofuData['filling_waktu'] ?? '-',
        'filling_mesin_1' => $tofuData['filling_mesin1'] ?? ($tofuData['mesin_1'] ?? '-'),
        'filling_mesin_2' => $tofuData['filling_mesin2'] ?? ($tofuData['mesin_2'] ?? '-'),
        
        // Total & Reject
        'total_kuantitas' => $tofuData['filling_total'] ?? ($tofuData['total_kuantitas'] ?? ($production->realized_quantity ?? '-')),
        'jumlah_reject_mentah' => $tofuData['rijek_mentah'] ?? ($tofuData['jumlah_reject_mentah'] ?? ($production->rejected_quantity ?? '-')),
    ];
    
    echo "Final mapped data:\n";
    foreach ($mappedData as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
    
    // Count non-empty values
    $nonEmptyCount = 0;
    foreach ($mappedData as $key => $value) {
        if ($key !== 'no' && $key !== 'tanggal_produksi' && $key !== 'kode_produk' && 
            $value !== '-' && $value !== null && $value !== '') {
            $nonEmptyCount++;
        }
    }
    
    echo "\nData completeness: {$nonEmptyCount}/12 QC fields have values\n";
    
    if ($nonEmptyCount > 0) {
        echo "✅ Data mapping working - {$nonEmptyCount} fields populated\n";
    } else {
        echo "❌ No data mapped - all fields empty\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during debugging: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "DEBUG COMPLETE\n";
echo "========================================\n";

?>