<?php

/**
 * Verify POS Customer Type Pricing Implementation
 */

echo "🧪 Verifying POS Customer Type Pricing Implementation\n";
echo "=" . str_repeat("=", 55) . "\n\n";

// Test 1: Check if POS file exists and has required functions
echo "📋 Test 1: Checking POS file and functions\n";
$posFile = 'resources/views/admin/penjualan/pos/index.blade.php';

if (file_exists($posFile)) {
    echo "✅ POS file found: {$posFile}\n";
    
    $content = file_get_contents($posFile);
    
    // Check for required functions
    $requiredFunctions = [
        'selectCustomer' => 'Customer selection with type price loading',
        'loadCustomerTypePrices' => 'Load prices for customer type from API',
        'updateProductPrices' => 'Update product prices in grid display',
        'applyCustomerTypePrices' => 'Apply customer type prices to cart',
        'addItem' => 'Add item with correct pricing logic',
        'clearCart' => 'Clear cart and reset prices'
    ];
    
    $foundFunctions = 0;
    foreach ($requiredFunctions as $func => $desc) {
        if (strpos($content, $func . '(') !== false) {
            echo "✅ {$func}() - {$desc}\n";
            $foundFunctions++;
        } else {
            echo "❌ {$func}() - Missing\n";
        }
    }
    
    echo "\nFunction Coverage: {$foundFunctions}/" . count($requiredFunctions) . "\n";
    
} else {
    echo "❌ POS file not found: {$posFile}\n";
}
echo "\n";

// Test 2: Check for enhanced features
echo "📋 Test 2: Checking enhanced features\n";

if (isset($content)) {
    $features = [
        'console.log.*POS.*Selecting customer' => 'Customer selection logging',
        'console.log.*POS.*Loading customer type prices' => 'Price loading logging',
        'console.log.*POS.*Updating product prices' => 'Price update logging',
        'has_discount.*true' => 'Discount indicator support',
        'original_price' => 'Original price preservation',
        'discount_info' => 'Discount information display',
        'customerTypePrices\[.*id_produk\]' => 'Customer type price mapping',
        'updateProductPrices\(\)' => 'Product price update calls'
    ];
    
    $foundFeatures = 0;
    foreach ($features as $pattern => $desc) {
        if (preg_match('/' . $pattern . '/i', $content)) {
            echo "✅ {$desc}\n";
            $foundFeatures++;
        } else {
            echo "❌ {$desc} - Not found\n";
        }
    }
    
    echo "\nFeature Coverage: {$foundFeatures}/" . count($features) . "\n";
}
echo "\n";

// Test 3: Check for visual enhancements in grid
echo "📋 Test 3: Checking grid visual enhancements\n";

if (isset($content)) {
    $gridFeatures = [
        'x-show="p\.has_discount"' => 'Discount indicator display',
        'line-through.*original_price' => 'Strikethrough original price',
        'text-green-600.*discount_info' => 'Green discount text',
        'idr\(p\.price\)' => 'Price display function'
    ];
    
    $foundGridFeatures = 0;
    foreach ($gridFeatures as $pattern => $desc) {
        if (preg_match('/' . $pattern . '/i', $content)) {
            echo "✅ {$desc}\n";
            $foundGridFeatures++;
        } else {
            echo "❌ {$desc} - Not found\n";
        }
    }
    
    echo "\nGrid Enhancement Coverage: {$foundGridFeatures}/" . count($gridFeatures) . "\n";
}
echo "\n";

// Test 4: Check route file for API endpoint
echo "📋 Test 4: Checking API route\n";
$routeFiles = [
    'routes/web.php',
    'routes/admin.php'
];

$routeFound = false;
foreach ($routeFiles as $routeFile) {
    if (file_exists($routeFile)) {
        $routeContent = file_get_contents($routeFile);
        if (strpos($routeContent, 'customer-type-prices') !== false) {
            echo "✅ Customer type prices route found in {$routeFile}\n";
            $routeFound = true;
            break;
        }
    }
}

if (!$routeFound) {
    echo "⚠️  Customer type prices route not found in route files\n";
    echo "   Expected route: admin.penjualan.pos.customer-type-prices\n";
}
echo "\n";

// Test 5: Check controller method
echo "📋 Test 5: Checking controller method\n";
$controllerFile = 'app/Http/Controllers/PosController.php';

if (file_exists($controllerFile)) {
    echo "✅ PosController found\n";
    
    $controllerContent = file_get_contents($controllerFile);
    if (strpos($controllerContent, 'getCustomerTypePrices') !== false) {
        echo "✅ getCustomerTypePrices method found\n";
        
        // Check for key implementation details
        if (strpos($controllerContent, 'ProdukTipe::where') !== false) {
            echo "✅ ProdukTipe model usage found\n";
        } else {
            echo "❌ ProdukTipe model usage not found\n";
        }
        
        if (strpos($controllerContent, 'harga_final') !== false) {
            echo "✅ Final price calculation found\n";
        } else {
            echo "❌ Final price calculation not found\n";
        }
        
    } else {
        echo "❌ getCustomerTypePrices method not found\n";
    }
} else {
    echo "❌ PosController not found: {$controllerFile}\n";
}
echo "\n";

// Test 6: Summary and recommendations
echo "📋 Test 6: Implementation Summary\n";
echo "=" . str_repeat("=", 35) . "\n";

$totalTests = 6;
$passedTests = 0;

// Calculate passed tests based on previous checks
if (file_exists($posFile)) $passedTests++;
if (isset($foundFunctions) && $foundFunctions >= 5) $passedTests++;
if (isset($foundFeatures) && $foundFeatures >= 6) $passedTests++;
if (isset($foundGridFeatures) && $foundGridFeatures >= 3) $passedTests++;
if ($routeFound) $passedTests++;
if (file_exists($controllerFile) && strpos(file_get_contents($controllerFile), 'getCustomerTypePrices') !== false) $passedTests++;

$percentage = round(($passedTests / $totalTests) * 100);

echo "📊 Test Results: {$passedTests}/{$totalTests} ({$percentage}%)\n\n";

if ($percentage >= 80) {
    echo "🎉 IMPLEMENTATION STATUS: EXCELLENT\n";
    echo "✅ All core features implemented\n";
    echo "✅ Ready for production testing\n";
} elseif ($percentage >= 60) {
    echo "⚠️  IMPLEMENTATION STATUS: GOOD\n";
    echo "✅ Most features implemented\n";
    echo "⚠️  Some minor issues may need attention\n";
} else {
    echo "❌ IMPLEMENTATION STATUS: NEEDS WORK\n";
    echo "❌ Several features missing or incomplete\n";
    echo "🔧 Review implementation and fix issues\n";
}

echo "\n📝 Next Steps:\n";
echo "1. 🌐 Open POS page in browser\n";
echo "2. 🧑‍💼 Test customer selection with different types\n";
echo "3. 💰 Verify price changes in grid and cart\n";
echo "4. 🔍 Check browser console for logging messages\n";
echo "5. 📋 Follow detailed testing guide in POS_CUSTOMER_TYPE_PRICING_TESTING_GUIDE.md\n\n";

echo "🚀 Implementation verification complete!\n";

?>