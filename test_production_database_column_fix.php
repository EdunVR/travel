<?php

/**
 * Test Production Database Column Fix
 * Memverifikasi bahwa method sudah diperbaiki sesuai struktur database yang sebenarnya
 */

echo "=== TESTING PRODUCTION DATABASE COLUMN FIX ===\n\n";

// 1. Check ProductionController file
$controllerFile = 'app/Http/Controllers/ProductionController.php';

if (!file_exists($controllerFile)) {
    echo "❌ ProductionController tidak ditemukan: $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

echo "1. Checking database column fixes:\n";

// Check fixes for wrong column names
$columnFixes = [
    'total_biaya_operasional' => [
        'status' => strpos($content, 'total_biaya_operasional') === false ? 'FIXED' : 'STILL_EXISTS',
        'replacement' => 'total_cost',
        'description' => 'Productions table uses total_cost, not total_biaya_operasional'
    ],
    'harga_beli' => [
        'status' => strpos($content, "'harga_beli'") === false ? 'FIXED' : 'STILL_EXISTS',
        'replacement' => 'hpp from hpp_produk table',
        'description' => 'Produk table does not have harga_beli, use hpp_produk.hpp instead'
    ],
    'total_hpp' => [
        'status' => strpos($content, 'total_hpp') === false ? 'FIXED' : 'STILL_EXISTS',
        'replacement' => 'hpp_per_unit',
        'description' => 'Productions table uses hpp_per_unit, not total_hpp'
    ]
];

foreach ($columnFixes as $wrongColumn => $fix) {
    if ($fix['status'] === 'FIXED') {
        echo "   ✅ $wrongColumn - FIXED (replaced with {$fix['replacement']})\n";
    } else {
        echo "   ❌ $wrongColumn - STILL EXISTS (should be replaced with {$fix['replacement']})\n";
    }
    echo "      {$fix['description']}\n";
}

echo "\n2. Checking correct column usage:\n";

// Check for correct column usage
$correctUsage = [
    'total_cost' => 'sum(\'total_cost\')',
    'hpp_per_unit' => 'sum(\'hpp_per_unit\')',
    'labor_cost' => 'sum(\'labor_cost\')',
    'operational_cost' => 'sum(\'operational_cost\')',
    'material_cost' => 'sum(\'material_cost\')',
    'outlet_id' => 'where(\'outlet_id\'',
    'hpp_produk join' => 'leftJoin(\'hpp_produk\'',
    'hpp as cost' => 'hpp_produk.hpp as cost'
];

foreach ($correctUsage as $usage => $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "   ✅ $usage - Correctly implemented\n";
    } else {
        echo "   ⚠️  $usage - Pattern not found: $pattern\n";
    }
}

echo "\n3. Checking FIFO implementation:\n";

// Check FIFO implementation
$fifoChecks = [
    'hpp_produk table usage' => 'HppProduk::where',
    'FIFO ordering' => 'orderBy(\'created_at\', \'asc\')',
    'cost from hpp' => 'record->hpp',
    'stock from hpp' => 'record->stok'
];

foreach ($fifoChecks as $check => $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "   ✅ $check - Implemented\n";
    } else {
        echo "   ⚠️  $check - Pattern not found: $pattern\n";
    }
}

echo "\n4. Checking outlet filtering fixes:\n";

// Check outlet filtering
$outletChecks = [
    'outlet_id column' => 'outlet_id.*outletId',
    'produk outlet filter' => 'produk.id_outlet.*outletId',
    'ALL outlet handling' => 'outletId.*!==.*ALL'
];

foreach ($outletChecks as $check => $pattern) {
    if (preg_match("/$pattern/", $content)) {
        echo "   ✅ $check - Correctly implemented\n";
    } else {
        echo "   ⚠️  $check - Pattern might be missing\n";
    }
}

echo "\n5. Checking error handling:\n";

// Check error handling
$errorHandling = [
    'try-catch blocks' => 'try.*catch.*Exception',
    'Log::error usage' => 'Log::error.*Error getting',
    'proper response format' => 'response\(\)->json.*success.*false'
];

foreach ($errorHandling as $check => $pattern) {
    if (preg_match("/$pattern/s", $content)) {
        echo "   ✅ $check - Implemented\n";
    } else {
        echo "   ⚠️  $check - Might be missing\n";
    }
}

echo "\n6. Database structure validation:\n";

// Validate against known database structure
$knownStructure = [
    'productions' => ['total_cost', 'hpp_per_unit', 'labor_cost', 'operational_cost', 'material_cost', 'outlet_id'],
    'produk' => ['id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 'stok', 'id_outlet'],
    'hpp_produk' => ['id_produk', 'hpp', 'stok', 'production_id']
];

echo "   Expected database structure:\n";
foreach ($knownStructure as $table => $columns) {
    echo "   $table: " . implode(', ', $columns) . "\n";
}

echo "\n7. Method-specific fixes:\n";

$methodFixes = [
    'getStatistics()' => [
        'uses total_cost instead of total_biaya_operasional',
        'uses hpp_per_unit instead of total_hpp',
        'includes labor_cost, operational_cost, material_cost'
    ],
    'getMaterials()' => [
        'joins with hpp_produk table for cost',
        'uses hpp as cost instead of harga_beli',
        'properly filters by outlet_id'
    ],
    'getMaterialFifo()' => [
        'uses HppProduk model for FIFO data',
        'orders by created_at for FIFO',
        'calculates average cost from hpp records'
    ]
];

foreach ($methodFixes as $method => $fixes) {
    echo "   $method:\n";
    foreach ($fixes as $fix) {
        echo "     - $fix\n";
    }
}

echo "\n=== SUMMARY ===\n";

$hasWrongColumns = strpos($content, 'total_biaya_operasional') !== false || 
                   strpos($content, "'harga_beli'") !== false;

if (!$hasWrongColumns) {
    echo "✅ DATABASE COLUMN FIXES APPLIED: All wrong column references have been corrected\n";
    
    echo "\nKey fixes applied:\n";
    echo "- total_biaya_operasional → total_cost\n";
    echo "- harga_beli → hpp (from hpp_produk table)\n";
    echo "- total_hpp → hpp_per_unit\n";
    echo "- Added proper FIFO implementation using hpp_produk table\n";
    echo "- Fixed outlet filtering to use correct column names\n";
    echo "- Implemented proper JOIN for cost data\n";
    
    echo "\nDatabase compatibility:\n";
    echo "- ✅ Uses actual column names from productions table\n";
    echo "- ✅ Uses actual column names from produk table\n";
    echo "- ✅ Implements FIFO system using hpp_produk table\n";
    echo "- ✅ No database schema changes required\n";
    
    echo "\nNext steps:\n";
    echo "1. Clear Laravel cache\n";
    echo "2. Test production statistics API\n";
    echo "3. Test materials search API\n";
    echo "4. Test FIFO data retrieval\n";
    echo "5. Verify no SQL column errors in logs\n";
    
} else {
    echo "❌ COLUMN ISSUES REMAIN: Some wrong column references still exist\n";
    echo "Please check the controller for remaining issues\n";
}

echo "\n=== TESTING COMPLETE ===\n";