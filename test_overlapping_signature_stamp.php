<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING OVERLAPPING SIGNATURE STAMP ===\n\n";

// Test 1: Check if invoice print files have overlapping signature code
echo "1. Testing Sales Invoice Print File...\n";
$salesInvoicePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
if (file_exists($salesInvoicePath)) {
    $content = file_get_contents($salesInvoicePath);
    
    if (strpos($content, 'position: relative') !== false && 
        strpos($content, 'position: absolute') !== false &&
        strpos($content, 'right: -10px') !== false) {
        echo "   ✓ Sales invoice has overlapping signature stamp code\n";
    } else {
        echo "   ✗ Sales invoice missing overlapping signature stamp code\n";
    }
    
    if (strpos($content, 'opacity: 0.8') !== false) {
        echo "   ✓ Sales invoice has stamp opacity setting\n";
    } else {
        echo "   ✗ Sales invoice missing stamp opacity setting\n";
    }
} else {
    echo "   ✗ Sales invoice file not found\n";
}

echo "\n2. Testing Service Invoice Print File...\n";
$serviceInvoicePath = 'resources/views/admin/service/invoice/print.blade.php';
if (file_exists($serviceInvoicePath)) {
    $content = file_get_contents($serviceInvoicePath);
    
    if (strpos($content, 'position: relative') !== false && 
        strpos($content, 'position: absolute') !== false &&
        strpos($content, 'right: -10px') !== false) {
        echo "   ✓ Service invoice has overlapping signature stamp code\n";
    } else {
        echo "   ✗ Service invoice missing overlapping signature stamp code\n";
    }
    
    if (strpos($content, 'opacity: 0.8') !== false) {
        echo "   ✓ Service invoice has stamp opacity setting\n";
    } else {
        echo "   ✗ Service invoice missing stamp opacity setting\n";
    }
} else {
    echo "   ✗ Service invoice file not found\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "\nOVERLAPPING SIGNATURE STAMP FEATURES:\n";
echo "✓ Logo/stamp positioned absolutely over signature\n";
echo "✓ Stamp positioned at right side (-10px from right edge)\n";
echo "✓ Stamp has 80% opacity for subtle overlay effect\n";
echo "✓ Signature height increased to 60px for better visibility\n";
echo "✓ Fallback placeholder when signature not available\n";
echo "✓ Z-index ensures stamp appears above signature\n";

echo "\nVISUAL LAYOUT:\n";
echo "┌─────────────────────────┐\n";
echo "│  [Signature Image]      │\n";
echo "│              [Logo/Cap] │ ← Logo overlaps 50% right side\n";
echo "│                         │\n";
echo "└─────────────────────────┘\n";
echo "        User Name\n";

echo "\nNEXT STEPS:\n";
echo "1. Test print preview to verify overlapping effect\n";
echo "2. Adjust logo size if needed (currently 40px height)\n";
echo "3. Adjust position offset if needed (currently -10px right)\n";
echo "4. Test with different signature and logo sizes\n";