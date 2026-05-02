<?php

/**
 * Test QC data reading pattern comparison between individual and bulk export
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Production;

echo "========================================\n";
echo "TESTING QC DATA READING PATTERN\n";
echo "========================================\n\n";

try {
    // Get a sample tofu production
    $production = Production::where('business_type', 'tofu')
        ->whereNotNull('tofu_data')
        ->first();
    
    if (!$production) {
        echo "❌ No tofu production with QC data found\n";
        exit(1);
    }
    
    echo "[TEST 1] Sample production: {$production->production_code}\n";
    echo "Raw tofu_data: ";
    var_dump($production->tofu_data);
    echo "\n";
    
    // Test individual QC PDF pattern (simple decode)
    echo "[TEST 2] Individual QC PDF pattern (simple decode)...\n";
    $individualPattern = json_decode($production->tofu_data, true) ?: [];
    
    if (!empty($individualPattern)) {
        echo "✓ Individual pattern SUCCESS\n";
        echo "Fields found:\n";
        foreach ($individualPattern as $key => $value) {
            echo "  - {$key}: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "❌ Individual pattern FAILED\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
    
    echo "\n";
    
    // Test bulk export pattern (double decode handling)
    echo "[TEST 3] Bulk export pattern (double decode handling)...\n";
    $bulkPattern = [];
    if ($production->tofu_data) {
        $firstDecode = json_decode($production->tofu_data, true);
        if (is_string($firstDecode)) {
            $bulkPattern = json_decode($firstDecode, true) ?: [];
            echo "✓ Double-encoded data detected and handled\n";
        } else {
            $bulkPattern = $firstDecode ?: [];
            echo "✓ Single-encoded data handled\n";
        }
    }
    
    if (!empty($bulkPattern)) {
        echo "✓ Bulk pattern SUCCESS\n";
        echo "Fields found:\n";
        foreach ($bulkPattern as $key => $value) {
            echo "  - {$key}: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "❌ Bulk pattern FAILED\n";
    }
    
    echo "\n";
    
    // Compare patterns
    echo "[TEST 4] Comparing patterns...\n";
    if ($individualPattern === $bulkPattern) {
        echo "✅ PATTERNS MATCH - Both methods return same data\n";
    } else {
        echo "⚠️ PATTERNS DIFFER\n";
        echo "Individual fields: " . count($individualPattern) . "\n";
        echo "Bulk fields: " . count($bulkPattern) . "\n";
        
        $individualKeys = array_keys($individualPattern);
        $bulkKeys = array_keys($bulkPattern);
        
        $onlyInIndividual = array_diff($individualKeys, $bulkKeys);
        $onlyInBulk = array_diff($bulkKeys, $individualKeys);
        
        if (!empty($onlyInIndividual)) {
            echo "Only in individual: " . implode(', ', $onlyInIndividual) . "\n";
        }
        if (!empty($onlyInBulk)) {
            echo "Only in bulk: " . implode(', ', $onlyInBulk) . "\n";
        }
    }
    
    echo "\n";
    
    // Test field mapping for bulk export
    echo "[TEST 5] Testing field mapping for bulk export...\n";
    $mappedData = [
        'perendaman_waktu' => $bulkPattern['perendaman_waktu'] ?? '-',
        'perendaman_kuantitas' => $bulkPattern['perendaman_qty'] ?? '-',
        'reject_telur_kuantitas' => $bulkPattern['rijek_telur'] ?? '-',
        'pasteurisasi_waktu' => $bulkPattern['pasteurisasi_waktu'] ?? '-',
        'pasteurisasi_suhu' => $bulkPattern['pasteurisasi_suhu'] ?? '-',
        'berat_akhir_sari_kedelai' => $bulkPattern['berat_sari_kedelai'] ?? '-',
        'pencampuran_waktu' => $bulkPattern['waktu_pencampuran'] ?? '-',
        'filling_waktu' => $bulkPattern['filling_waktu'] ?? '-',
        'filling_mesin_1' => $bulkPattern['filling_mesin1'] ?? '-',
        'filling_mesin_2' => $bulkPattern['filling_mesin2'] ?? '-',
        'total_kuantitas' => $bulkPattern['filling_total'] ?? '-',
        'jumlah_reject_mentah' => $bulkPattern['rijek_mentah'] ?? '-',
    ];
    
    echo "Mapped data for bulk export:\n";
    foreach ($mappedData as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
    
    // Count non-empty values
    $nonEmptyCount = count(array_filter($mappedData, function($value) {
        return $value !== '-' && $value !== null && $value !== '';
    }));
    
    echo "\nData completeness: {$nonEmptyCount}/12 fields have values\n";
    
    if ($nonEmptyCount > 0) {
        echo "✅ Data mapping successful - {$nonEmptyCount} fields populated\n";
    } else {
        echo "❌ No data mapped - check field names\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during testing: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "QC DATA READING PATTERN TEST COMPLETE\n";
echo "========================================\n";

?>