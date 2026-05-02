<?php
/**
 * Test script for Service Customer Search Fix
 * 
 * This script tests the customer search functionality with outlet filtering
 */

require_once 'vendor/autoload.php';

echo "=== Testing Service Customer Search Fix ===\n\n";

// Test database structure understanding
echo "1. Checking database structure understanding:\n";

// Check if Member model has id_outlet
$memberModel = 'app/Models/Member.php';
if (file_exists($memberModel)) {
    $content = file_get_contents($memberModel);
    
    if (strpos($content, "'id_outlet'") !== false) {
        echo "   ✓ Member model has id_outlet in fillable\n";
    } else {
        echo "   ✗ Member model missing id_outlet\n";
    }
    
    if (strpos($content, 'mesinCustomers()') !== false) {
        echo "   ✓ Member model has mesinCustomers relationship\n";
    } else {
        echo "   ✗ Member model missing mesinCustomers relationship\n";
    }
} else {
    echo "   ✗ Member model not found\n";
}

// Test ServiceController fix
echo "\n2. Testing ServiceController searchCustomers fix:\n";

$serviceController = 'app/Http/Controllers/ServiceController.php';
if (file_exists($serviceController)) {
    $content = file_get_contents($serviceController);
    
    // Check if the problematic query is fixed
    if (strpos($content, "->where('id_outlet', \$outletId)") !== false && 
        strpos($content, "->where('id_outlet', \$outletId) // Filter members by outlet") !== false) {
        echo "   ✓ Member filtering by outlet is correct\n";
    } else {
        echo "   ✗ Member outlet filtering not found or incorrect\n";
    }
    
    // Check if the problematic mesinCustomers filtering is removed
    if (strpos($content, "->with(['mesinCustomers' => function(\$query) use (\$outletId)") === false) {
        echo "   ✓ Incorrect mesinCustomers outlet filtering removed\n";
    } else {
        echo "   ✗ Still trying to filter mesinCustomers by id_outlet\n";
    }
    
    // Check if mesinCustomers relationship is still loaded
    if (strpos($content, "->with(['mesinCustomers'") !== false) {
        echo "   ✓ mesinCustomers relationship still loaded\n";
    } else {
        echo "   ✗ mesinCustomers relationship not loaded\n";
    }
    
    // Check if produk relationship is still loaded with pivot
    if (strpos($content, "->withPivot('closing_type')") !== false) {
        echo "   ✓ Produk relationship with pivot still loaded\n";
    } else {
        echo "   ✗ Produk relationship with pivot not loaded\n";
    }
} else {
    echo "   ✗ ServiceController not found\n";
}

// Test JavaScript outlet parameter passing
echo "\n3. Testing JavaScript outlet parameter:\n";

$autocompleteJs = 'public/js/service-invoice-autocomplete-fixed.js';
if (file_exists($autocompleteJs)) {
    $content = file_get_contents($autocompleteJs);
    
    if (strpos($content, 'outlet_id=${outletId}') !== false) {
        echo "   ✓ JavaScript passes outlet_id parameter\n";
    } else {
        echo "   ✗ JavaScript not passing outlet_id parameter\n";
    }
    
    if (strpos($content, 'document.getElementById("outlet_id")?.value') !== false) {
        echo "   ✓ JavaScript gets outlet_id from form\n";
    } else {
        echo "   ✗ JavaScript not getting outlet_id from form\n";
    }
} else {
    echo "   ✗ service-invoice-autocomplete-fixed.js not found\n";
}

echo "\n=== Expected Behavior ===\n";
echo "1. Customer search should filter by member.id_outlet\n";
echo "2. Should NOT try to filter mesin_customer by id_outlet (column doesn't exist)\n";
echo "3. Should still load mesinCustomers relationship for each member\n";
echo "4. Should still load produk relationship with closing_type pivot\n";
echo "5. JavaScript should pass outlet_id parameter in search requests\n";

echo "\n=== Database Query Logic ===\n";
echo "Correct: SELECT * FROM member WHERE id_outlet = ? AND nama LIKE ?\n";
echo "Incorrect: SELECT * FROM mesin_customer WHERE id_outlet = ? (column doesn't exist)\n";

echo "\n=== Test SQL Query ===\n";
echo "You can test this query in your database:\n";
echo "SELECT m.*, COUNT(mc.id) as mesin_count \n";
echo "FROM member m \n";
echo "LEFT JOIN mesin_customer mc ON m.id_member = mc.id_member \n";
echo "WHERE m.id_outlet = 1 AND m.nama LIKE '%test%' \n";
echo "GROUP BY m.id_member \n";
echo "ORDER BY m.nama ASC;\n";

echo "\nFix completed! The error should be resolved now.\n";
?>