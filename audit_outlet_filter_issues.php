<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 COMPREHENSIVE OUTLET FILTER AUDIT\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// List of controllers to check
$controllersToCheck = [
    'app/Http/Controllers/CustomerManagementController.php',
    'app/Http/Controllers/CustomerTypeController.php',
    'app/Http/Controllers/SalesManagementController.php',
    'app/Http/Controllers/PurchaseManagementController.php',
    'app/Http/Controllers/InventoriController.php',
    'app/Http/Controllers/BahanController.php',
    'app/Http/Controllers/ProdukController.php',
    'app/Http/Controllers/ServiceController.php',
    'app/Http/Controllers/ServiceManagementController.php',
    'app/Http/Controllers/FinanceAccountantController.php',
    'app/Http/Controllers/ProductionController.php',
    'app/Http/Controllers/InterOutletSaleController.php',
    'app/Http/Controllers/PosController.php',
    'app/Http/Controllers/MarginReportController.php',
];

$issues = [];

foreach ($controllersToCheck as $controllerPath) {
    if (!file_exists($controllerPath)) {
        continue;
    }
    
    echo "🔍 Checking: " . basename($controllerPath) . "\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check if uses HasOutletFilter trait
    $usesOutletFilter = strpos($content, 'use HasOutletFilter') !== false || 
                       strpos($content, 'use \App\Traits\HasOutletFilter') !== false;
    
    // Check for potential issues
    $potentialIssues = [];
    
    // Issue 1: Getting all records without outlet filter
    if (preg_match_all('/(\w+)::all\(\)/', $content, $matches)) {
        foreach ($matches[1] as $model) {
            if (in_array($model, ['Tipe', 'Outlet', 'Member', 'Produk', 'Bahan', 'Supplier'])) {
                $potentialIssues[] = "Uses {$model}::all() - may need outlet filtering";
            }
        }
    }
    
    // Issue 2: Direct model queries without outlet filter
    if (preg_match_all('/(\w+)::(?:where|select|get|find)/', $content, $matches)) {
        foreach ($matches[1] as $model) {
            if (in_array($model, ['Tipe', 'Member', 'Produk', 'Bahan', 'Supplier']) && 
                !$usesOutletFilter) {
                $potentialIssues[] = "Direct {$model} queries without HasOutletFilter trait";
            }
        }
    }
    
    // Issue 3: Missing outlet access validation
    $hasOutletValidation = strpos($content, 'validateOutletAccess') !== false ||
                          strpos($content, 'getAccessibleOutletIds') !== false ||
                          strpos($content, 'applyOutletFilter') !== false;
    
    if (!$hasOutletValidation && !$usesOutletFilter) {
        $potentialIssues[] = "No outlet access validation found";
    }
    
    // Issue 4: Check for specific problematic patterns
    $problematicPatterns = [
        'Tipe::all()' => 'Should filter tipe by accessible outlets',
        'Member::all()' => 'Should filter members by accessible outlets',
        'Produk::all()' => 'Should filter products by accessible outlets',
        'Bahan::all()' => 'Should filter materials by accessible outlets',
        'Supplier::all()' => 'Should filter suppliers by accessible outlets',
    ];
    
    foreach ($problematicPatterns as $pattern => $issue) {
        if (strpos($content, $pattern) !== false) {
            $potentialIssues[] = $issue;
        }
    }
    
    if (!empty($potentialIssues)) {
        $issues[$controllerPath] = [
            'uses_outlet_filter' => $usesOutletFilter,
            'has_outlet_validation' => $hasOutletValidation,
            'issues' => $potentialIssues
        ];
        
        echo "   ❌ Found " . count($potentialIssues) . " potential issues\n";
        foreach ($potentialIssues as $issue) {
            echo "      - $issue\n";
        }
    } else {
        echo "   ✅ No obvious issues found\n";
    }
    
    echo "\n";
}

echo "\n📊 AUDIT SUMMARY\n";
echo "=" . str_repeat("=", 60) . "\n";

if (empty($issues)) {
    echo "✅ No outlet filter issues found!\n";
} else {
    echo "❌ Found issues in " . count($issues) . " controllers:\n\n";
    
    foreach ($issues as $controller => $data) {
        echo "🔧 " . basename($controller) . ":\n";
        echo "   Uses HasOutletFilter: " . ($data['uses_outlet_filter'] ? 'Yes' : 'No') . "\n";
        echo "   Has Outlet Validation: " . ($data['has_outlet_validation'] ? 'Yes' : 'No') . "\n";
        echo "   Issues:\n";
        foreach ($data['issues'] as $issue) {
            echo "      - $issue\n";
        }
        echo "\n";
    }
}

// Check specific models that should have outlet relationships
echo "\n🏢 CHECKING MODEL OUTLET RELATIONSHIPS\n";
echo "=" . str_repeat("=", 60) . "\n";

$modelsToCheck = [
    'App\Models\Tipe' => 'id_outlet',
    'App\Models\Member' => 'id_outlet', 
    'App\Models\Produk' => 'id_outlet',
    'App\Models\Bahan' => 'id_outlet',
    'App\Models\Supplier' => 'id_outlet',
];

foreach ($modelsToCheck as $modelClass => $outletColumn) {
    try {
        if (class_exists($modelClass)) {
            $model = new $modelClass();
            $table = $model->getTable();
            
            // Check if outlet column exists
            $hasOutletColumn = \Schema::hasColumn($table, $outletColumn);
            
            echo "📋 " . class_basename($modelClass) . " (table: $table):\n";
            echo "   Has $outletColumn column: " . ($hasOutletColumn ? 'Yes' : 'No') . "\n";
            
            if ($hasOutletColumn) {
                // Count records per outlet
                $outletCounts = $model->select($outletColumn, \DB::raw('count(*) as total'))
                    ->groupBy($outletColumn)
                    ->get();
                
                echo "   Records per outlet:\n";
                foreach ($outletCounts as $count) {
                    $outlet = \App\Models\Outlet::find($count->$outletColumn);
                    $outletName = $outlet ? $outlet->nama_outlet : "Unknown ({$count->$outletColumn})";
                    echo "      - $outletName: {$count->total} records\n";
                }
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "❌ Error checking $modelClass: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Audit Complete!\n";