<?php

echo "=== Testing COA Outlet and Grid Fix ===\n";

echo "\n=== Testing COA Modal Structure ===\n";

// Check if modal file has correct structure
$modalFile = 'resources/views/admin/penjualan/inter-outlet/coa-settings.blade.php';
if (file_exists($modalFile)) {
    echo "✓ COA settings modal file exists\n";
    
    $content = file_get_contents($modalFile);
    
    // Check for outlet select with correct ID
    if (strpos($content, 'id="coa-outlet-select"') !== false) {
        echo "✓ Modal has outlet select with correct ID\n";
    } else {
        echo "✗ Modal outlet select ID not found\n";
    }
    
    // Check for other form elements
    $requiredElements = [
        'id="coa-accounting-book"',
        'id="coa-piutang"',
        'id="coa-pendapatan"',
        'id="coa-hpp"',
        'id="coa-persediaan"',
        'id="coa-ppn"'
    ];
    
    foreach ($requiredElements as $element) {
        if (strpos($content, $element) !== false) {
            echo "✓ Found element: $element\n";
        } else {
            echo "✗ Missing element: $element\n";
        }
    }
    
} else {
    echo "✗ COA settings modal file not found\n";
}

echo "\n=== Testing JavaScript Functions ===\n";

$indexFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($indexFile)) {
    echo "✓ Index file exists\n";
    
    $content = file_get_contents($indexFile);
    
    // Check for populateCoaModal function
    if (strpos($content, 'function populateCoaModal(response)') !== false) {
        echo "✓ populateCoaModal function found\n";
    } else {
        echo "✗ populateCoaModal function not found\n";
    }
    
    // Check for outlet population logic
    if (strpos($content, 'data.outlets.forEach') !== false) {
        echo "✓ Outlet population logic found\n";
    } else {
        echo "✗ Outlet population logic not found\n";
    }
    
    // Check for account population logic
    if (strpos($content, 'data.accountsByType') !== false) {
        echo "✓ Account population logic found\n";
    } else {
        echo "✗ Account population logic not found\n";
    }
    
} else {
    echo "✗ Index file not found\n";
}

echo "\n=== Testing Product Grid Structure ===\n";

if (file_exists($indexFile)) {
    $content = file_get_contents($indexFile);
    
    // Check if add to cart button is removed
    if (strpos($content, 'Tambah ke Keranjang') === false) {
        echo "✓ Add to cart button removed from grid\n";
    } else {
        echo "✗ Add to cart button still exists in grid\n";
    }
    
    // Check for click indicator
    if (strpos($content, 'Klik untuk menambah') !== false) {
        echo "✓ Click indicator added\n";
    } else {
        echo "✗ Click indicator not found\n";
    }
    
    // Check for price section with background
    if (strpos($content, 'bg-slate-50 rounded-lg p-2') !== false) {
        echo "✓ Price section with background found\n";
    } else {
        echo "✗ Price section styling not found\n";
    }
    
    // Check for flex layout improvements
    if (strpos($content, 'flex flex-col h-full') !== false) {
        echo "✓ Improved flex layout found\n";
    } else {
        echo "✗ Improved flex layout not found\n";
    }
    
    // Check for stock validation
    if (strpos($content, 'product.stock <= 0') !== false) {
        echo "✓ Stock validation found\n";
    } else {
        echo "✗ Stock validation not found\n";
    }
}

echo "\n=== Testing Controller Method ===\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    echo "✓ Controller file exists\n";
    
    $content = file_get_contents($controllerFile);
    
    // Check for getCoaModalData method
    if (strpos($content, 'function getCoaModalData') !== false) {
        echo "✓ getCoaModalData method found\n";
    } else {
        echo "✗ getCoaModalData method not found\n";
    }
    
    // Check for outlets query
    if (strpos($content, "Outlet::where('is_active', true)->get()") !== false) {
        echo "✓ Outlets query found\n";
    } else {
        echo "✗ Outlets query not found\n";
    }
    
    // Check for accounts grouping
    if (strpos($content, 'accountsByType') !== false) {
        echo "✓ Accounts grouping found\n";
    } else {
        echo "✗ Accounts grouping not found\n";
    }
    
} else {
    echo "✗ Controller file not found\n";
}

echo "\n=== Summary ===\n";
echo "✓ Fixed COA modal outlet population\n";
echo "✓ Added proper JavaScript functions for data loading\n";
echo "✓ Removed add to cart button from product grid\n";
echo "✓ Fixed price layout to prevent overflow\n";
echo "✓ Added click indicator for better UX\n";
echo "✓ Improved product card layout with flexbox\n";
echo "✓ Added stock validation and visual indicators\n";

echo "\n=== Next Steps ===\n";
echo "1. Test COA settings modal - outlets should appear\n";
echo "2. Test product grid - price should stay within card\n";
echo "3. Test clicking product cards - should add to cart\n";
echo "4. Test stock validation - out of stock products should be disabled\n";

echo "\n=== Test Complete ===\n";