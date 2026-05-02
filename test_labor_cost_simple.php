<?php

// Simple test to check labor cost integration
echo "🧪 Testing Labor Cost Integration - Simple Test\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check if the controller method handles labor costs correctly
echo "📋 Checking ProductionController::calculateHppPreview method\n\n";

// Read the controller file
$controllerFile = 'app/Http/Controllers/ProductionController.php';
if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Check for labor cost handling in calculateHppPreview method
echo "🔍 Searching for labor cost handling in calculateHppPreview...\n";

// Find the calculateHppPreview method
$methodStart = strpos($content, 'public function calculateHppPreview');
if ($methodStart === false) {
    echo "❌ calculateHppPreview method not found\n";
    exit(1);
}

// Find the end of the method (next public function or end of class)
$methodEnd = strpos($content, 'public function', $methodStart + 1);
if ($methodEnd === false) {
    $methodEnd = strpos($content, '}', strrpos($content, '}') - 1); // Find class end
}

$methodContent = substr($content, $methodStart, $methodEnd - $methodStart);

echo "✅ Found calculateHppPreview method\n\n";

// Check for labor cost variables
$laborCostChecks = [
    'laborCosts' => strpos($methodContent, '$laborCosts') !== false,
    'totalLaborCost' => strpos($methodContent, '$totalLaborCost') !== false,
    'worker_count' => strpos($methodContent, 'worker_count') !== false,
    'cost_per_worker' => strpos($methodContent, 'cost_per_worker') !== false,
    'labor_cost_response' => strpos($methodContent, "'labor_cost'") !== false,
];

echo "🔍 Labor Cost Variable Checks:\n";
foreach ($laborCostChecks as $check => $found) {
    echo "- $check: " . ($found ? "✅ Found" : "❌ Not found") . "\n";
}

// Check if labor cost is included in response
$responsePattern = '/labor_cost.*=>/';
if (preg_match($responsePattern, $methodContent)) {
    echo "✅ Labor cost is included in response\n";
} else {
    echo "❌ Labor cost might not be included in response\n";
}

echo "\n📋 Key Labor Cost Code Sections:\n";

// Extract labor cost calculation section
if (strpos($methodContent, '$laborCosts') !== false) {
    $lines = explode("\n", $methodContent);
    $inLaborSection = false;
    $laborLines = [];
    
    foreach ($lines as $line) {
        if (strpos($line, '$laborCosts') !== false || strpos($line, 'labor_costs') !== false) {
            $inLaborSection = true;
        }
        
        if ($inLaborSection) {
            $laborLines[] = trim($line);
            
            // Stop at the end of labor cost calculation
            if (strpos($line, '$totalLaborCost') !== false && strpos($line, ';') !== false) {
                $inLaborSection = false;
            }
        }
    }
    
    if (!empty($laborLines)) {
        echo "```php\n";
        foreach ($laborLines as $line) {
            if (!empty(trim($line))) {
                echo $line . "\n";
            }
        }
        echo "```\n";
    }
}

echo "\n🔍 Checking JavaScript integration...\n";

// Check JavaScript file
$jsFile = 'public/js/production.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    $jsChecks = [
        'calculateLaborCost function' => strpos($jsContent, 'calculateLaborCost') !== false,
        'labor_costs form fields' => strpos($jsContent, 'labor_costs[') !== false,
        'previewLaborCost element' => strpos($jsContent, 'previewLaborCost') !== false,
        'HPP preview includes labor' => strpos($jsContent, 'labor_cost') !== false,
    ];
    
    echo "📋 JavaScript Integration Checks:\n";
    foreach ($jsChecks as $check => $found) {
        echo "- $check: " . ($found ? "✅ Found" : "❌ Not found") . "\n";
    }
} else {
    echo "❌ JavaScript file not found: $jsFile\n";
}

echo "\n🔍 Checking view file for labor cost form fields...\n";

// Check view file
$viewFile = 'resources/views/admin/produksi/produksi/index.blade.php';
if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    
    $viewChecks = [
        'worker_count input' => strpos($viewContent, 'labor_costs[worker_count]') !== false,
        'cost_per_worker input' => strpos($viewContent, 'labor_costs[cost_per_worker]') !== false,
        'total_cost hidden input' => strpos($viewContent, 'labor_costs[total_cost]') !== false,
        'previewLaborCost display' => strpos($viewContent, 'previewLaborCost') !== false,
        'calculateLaborCost function call' => strpos($viewContent, 'calculateLaborCost()') !== false,
    ];
    
    echo "📋 View File Checks:\n";
    foreach ($viewChecks as $check => $found) {
        echo "- $check: " . ($found ? "✅ Found" : "❌ Not found") . "\n";
    }
} else {
    echo "❌ View file not found: $viewFile\n";
}

echo "\n📋 Summary:\n";
$allChecks = array_merge($laborCostChecks, $jsChecks ?? [], $viewChecks ?? []);
$foundCount = count(array_filter($allChecks));
$totalCount = count($allChecks);

echo "Found: $foundCount/$totalCount checks passed\n";

if ($foundCount == $totalCount) {
    echo "✅ All labor cost integration components are present!\n";
    echo "💡 The issue might be in the data flow or calculation logic.\n";
} else {
    echo "❌ Some labor cost integration components are missing.\n";
    echo "💡 Need to implement missing components.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";