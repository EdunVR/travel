<?php
/**
 * Test Mesin Customer & Ongkir Service Alpine.js Fix
 */

echo "=== MESIN CUSTOMER & ONGKIR SERVICE ALPINE.JS FIX TEST ===\n\n";

// Check mesin.js
$mesinJsPath = __DIR__ . '/public/js/mesin.js';
if (file_exists($mesinJsPath)) {
    echo "✅ mesin.js exists\n";
    
    $content = file_get_contents($mesinJsPath);
    if (strpos($content, 'window.mesinCrud') !== false) {
        echo "✅ mesinCrud function defined in mesin.js\n";
    }
    if (strpos($content, 'console.log(\'✅ mesin.js loaded successfully\')') !== false) {
        echo "✅ Success logging present in mesin.js\n";
    }
} else {
    echo "❌ mesin.js missing\n";
}

// Check ongkir.js
$ongkirJsPath = __DIR__ . '/public/js/ongkir.js';
if (file_exists($ongkirJsPath)) {
    echo "✅ ongkir.js exists\n";
    
    $content = file_get_contents($ongkirJsPath);
    if (strpos($content, 'window.ongkirCrud') !== false) {
        echo "✅ ongkirCrud function defined in ongkir.js\n";
    }
    if (strpos($content, 'console.log(\'✅ ongkir.js loaded successfully\')') !== false) {
        echo "✅ Success logging present in ongkir.js\n";
    }
} else {
    echo "❌ ongkir.js missing\n";
}

// Check mesin view
$mesinViewPath = __DIR__ . '/resources/views/admin/service/mesin/index.blade.php';
if (file_exists($mesinViewPath)) {
    echo "✅ mesin view exists\n";
    
    $content = file_get_contents($mesinViewPath);
    
    if (strpos($content, 'mesin.js') !== false) {
        echo "✅ mesin.js script reference found\n";
    }
    if (strpos($content, '?v={{ time() }}') !== false) {
        echo "✅ Cache busting parameter added to mesin view\n";
    }
    if (strpos($content, 'fallback mesinCrud') !== false) {
        echo "✅ Fallback function included in mesin view\n";
    }
    if (strpos($content, 'function mesinCrud()') === false) {
        echo "✅ Inline function removed from mesin view\n";
    } else {
        echo "❌ Inline function still exists in mesin view\n";
    }
} else {
    echo "❌ mesin view missing\n";
}

// Check ongkir view
$ongkirViewPath = __DIR__ . '/resources/views/admin/service/ongkir/index.blade.php';
if (file_exists($ongkirViewPath)) {
    echo "✅ ongkir view exists\n";
    
    $content = file_get_contents($ongkirViewPath);
    
    if (strpos($content, 'ongkir.js') !== false) {
        echo "✅ ongkir.js script reference found\n";
    }
    if (strpos($content, '?v={{ time() }}') !== false) {
        echo "✅ Cache busting parameter added to ongkir view\n";
    }
    if (strpos($content, 'fallback ongkirCrud') !== false) {
        echo "✅ Fallback function included in ongkir view\n";
    }
    if (strpos($content, 'function ongkirCrud()') === false) {
        echo "✅ Inline function removed from ongkir view\n";
    } else {
        echo "❌ Inline function still exists in ongkir view\n";
    }
} else {
    echo "❌ ongkir view missing\n";
}

echo "\n=== EXPECTED CONSOLE OUTPUT ===\n";
echo "MESIN CUSTOMER PAGE:\n";
echo "✅ mesin.js loaded successfully\n";
echo "✅ mesinCrud function found in mesin.js\n";
echo "✅ Mesin CRUD initialized\n\n";

echo "ONGKIR SERVICE PAGE:\n";
echo "✅ ongkir.js loaded successfully\n";
echo "✅ ongkirCrud function found in ongkir.js\n";
echo "✅ Ongkir CRUD initialized\n\n";

echo "=== MANUAL TESTING CHECKLIST ===\n";
echo "MESIN CUSTOMER (/admin/service/mesin):\n";
echo "□ Page loads without Alpine.js errors\n";
echo "□ Data table displays correctly\n";
echo "□ 'Tambah Mesin' button works\n";
echo "□ Customer search works\n";
echo "□ Product selection works\n";
echo "□ Form submission works\n";
echo "□ Edit mesin works\n";
echo "□ Delete mesin works\n\n";

echo "ONGKIR SERVICE (/admin/service/ongkir):\n";
echo "□ Page loads without Alpine.js errors\n";
echo "□ Data table displays correctly\n";
echo "□ 'Tambah Ongkir' button works\n";
echo "□ Form submission works\n";
echo "□ Edit ongkir works\n";
echo "□ Delete ongkir works\n";
echo "□ Outlet filter works\n\n";

echo "Status: Ready for testing\n";
?>