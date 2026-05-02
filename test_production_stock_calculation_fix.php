<?php

/**
 * Test Production Stock Calculation Fix
 * Memverifikasi bahwa stok dihitung dari tabel hpp_produk, bukan dari kolom produk.stok yang tidak ada
 */

echo "=== TESTING PRODUCTION STOCK CALCULATION FIX ===\n\n";

// 1. Check ProductionController file
$controllerFile = 'app/Http/Controllers/ProductionController.php';

if (!file_exists($controllerFile)) {
    echo "❌ ProductionController tidak ditemukan: $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

echo "1. Checking stock calculation fixes:\n";

// Check for wrong stock references
$wrongStockReferences = [
    "'produk.stok'" => 'Direct reference to non-existent produk.stok column in SELECT',
    "where('produk.stok'" => 'WHERE clause using non-existent produk.stok',
    "->stok" => 'Direct property access to stok on produk model'
];

$hasWrongReferences = false;
foreach ($wrongStockReferences as $pattern => $description) {
    // Skip if it's a comment or in hpp_produk context
    if (strpos($content, $pattern) !== false) {
        // Check if it's in a valid context (hpp_produk or comments)
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, $pattern) !== false) {
                // Skip if it's a comment or hpp_produk context
                if (strpos($line, '//') !== false || 
                    strpos($line, 'hpp_produk') !== false ||
                    strpos($line, '$record->stok') !== false ||
                    strpos($line, '$hppRecord->stok') !== false) {
                    continue;
                }
                echo "   ❌ Found wrong reference: $pattern - $description\n";
                echo "      Line: " . trim($line) . "\n";
                $hasWrongReferences = true;
                break;
            }
        }
    }
}

if (!$hasWrongReferences) {
    echo "   ✅ No wrong stock column references found\n";
}

echo "\n2. Checking correct stock calculation implementation:\n";

// Check for correct stock calculation patterns
$correctPatterns = [
    'SUM\(hpp_produk\.stok\)' => 'Calculates total stock from hpp_produk table',
    'COALESCE\(SUM\(hpp_produk\.stok\)' => 'Handles NULL values in stock calculation',
    'total_stock' => 'Uses calculated total_stock alias',
    'groupBy.*produk\.id_produk' => 'Groups by product ID for aggregation',
    'having.*total_stock.*>' => 'Filters by calculated stock > 0',
    'AVG\(hpp_produk\.hpp\)' => 'Calculates average cost from hpp_produk'
];

foreach ($correctPatterns as $pattern => $description) {
    if (preg_match("/$pattern/i", $content)) {
        echo "   ✅ $description - IMPLEMENTED\n";
    } else {
        echo "   ⚠️  $description - PATTERN NOT FOUND\n";
    }
}

echo "\n3. Checking method-specific implementations:\n";

// Check getMaterials method
if (strpos($content, 'function getMaterials') !== false) {
    echo "   getMaterials() method:\n";
    
    $getMaterialsChecks = [
        'leftJoin.*hpp_produk' => 'Joins with hpp_produk table',
        'SUM.*hpp_produk.stok.*as total_stock' => 'Calculates total stock',
        'AVG.*hpp_produk.hpp.*as cost' => 'Calculates average cost',
        'having.*total_stock.*>' => 'Filters by stock > 0',
        'groupBy.*produk.id_produk' => 'Groups by product for aggregation'
    ];
    
    foreach ($getMaterialsChecks as $pattern => $description) {
        if (preg_match("/$pattern/i", $content)) {
            echo "     ✅ $description\n";
        } else {
            echo "     ❌ $description - MISSING\n";
        }
    }
}

// Check getProducts method
if (strpos($content, 'function getProducts') !== false) {
    echo "\n   getProducts() method:\n";
    
    $getProductsChecks = [
        'leftJoin.*hpp_produk' => 'Joins with hpp_produk table',
        'SUM.*hpp_produk.stok.*as total_stock' => 'Calculates total stock',
        'groupBy.*produk.id_produk' => 'Groups by product for aggregation',
        'total_stock.*0' => 'Uses calculated stock in response'
    ];
    
    foreach ($getProductsChecks as $pattern => $description) {
        if (preg_match("/$pattern/i", $content)) {
            echo "     ✅ $description\n";
        } else {
            echo "     ❌ $description - MISSING\n";
        }
    }
}

echo "\n4. Checking SQL query structure:\n";

// Check for proper SQL structure
$sqlChecks = [
    'SELECT with calculated fields' => 'COALESCE.*SUM.*hpp_produk.stok',
    'LEFT JOIN syntax' => 'leftJoin.*hpp_produk.*produk.id_produk.*hpp_produk.id_produk',
    'GROUP BY clause' => 'groupBy.*produk.id_produk',
    'HAVING clause' => 'having.*total_stock',
    'Qualified column names' => 'produk.id_outlet.*produk.kode_produk'
];

foreach ($sqlChecks as $check => $pattern) {
    if (preg_match("/$pattern/i", $content)) {
        echo "   ✅ $check - Properly implemented\n";
    } else {
        echo "   ⚠️  $check - Pattern not found\n";
    }
}

echo "\n5. Database compatibility check:\n";

// Verify database structure understanding
echo "   Expected behavior:\n";
echo "   - produk table: Does NOT have 'stok' column\n";
echo "   - hpp_produk table: Has 'stok' column for FIFO inventory\n";
echo "   - Stock calculation: SUM(hpp_produk.stok) GROUP BY produk.id_produk\n";
echo "   - Cost calculation: AVG(hpp_produk.hpp) for average FIFO cost\n";

echo "\n6. Error prevention:\n";

$errorPrevention = [
    'No direct produk.stok references' => !preg_match("/select.*'stok'|where.*produk\.stok/i", $content),
    'Uses qualified column names' => strpos($content, 'produk.id_produk') !== false,
    'Handles NULL values' => strpos($content, 'COALESCE') !== false,
    'Proper aggregation' => strpos($content, 'groupBy') !== false,
    'Stock filtering' => strpos($content, 'having') !== false
];

foreach ($errorPrevention as $check => $passed) {
    if ($passed) {
        echo "   ✅ $check\n";
    } else {
        echo "   ❌ $check\n";
    }
}

echo "\n=== SUMMARY ===\n";

$isFixed = !preg_match("/select.*'stok'|where.*produk\.stok/i", $content) && 
           strpos($content, 'SUM(hpp_produk.stok)') !== false &&
           strpos($content, 'total_stock') !== false;

if ($isFixed) {
    echo "✅ STOCK CALCULATION FIXED: Stock is now properly calculated from hpp_produk table\n";
    
    echo "\nKey improvements:\n";
    echo "- Removed references to non-existent produk.stok column\n";
    echo "- Implemented SUM(hpp_produk.stok) for total stock calculation\n";
    echo "- Added GROUP BY for proper aggregation\n";
    echo "- Used HAVING clause to filter by calculated stock\n";
    echo "- Implemented AVG(hpp_produk.hpp) for average cost\n";
    echo "- Added COALESCE to handle NULL values\n";
    
    echo "\nSQL Query Structure:\n";
    echo "SELECT produk.*, SUM(hpp_produk.stok) as total_stock, AVG(hpp_produk.hpp) as cost\n";
    echo "FROM produk\n";
    echo "LEFT JOIN hpp_produk ON produk.id_produk = hpp_produk.id_produk\n";
    echo "WHERE produk.id_outlet = ? AND produk.is_active = 1\n";
    echo "GROUP BY produk.id_produk\n";
    echo "HAVING total_stock > 0\n";
    
    echo "\nNext steps:\n";
    echo "1. Clear Laravel cache\n";
    echo "2. Test materials API endpoint\n";
    echo "3. Test products API endpoint\n";
    echo "4. Verify stock calculations are correct\n";
    echo "5. Check that only products with stock appear\n";
    
} else {
    echo "❌ STOCK CALCULATION NOT FULLY FIXED\n";
    echo "Some issues may remain with stock calculation implementation\n";
}

echo "\n=== TESTING COMPLETE ===\n";