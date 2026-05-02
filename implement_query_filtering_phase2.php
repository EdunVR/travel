<?php

/**
 * Phase 2: Query Filtering Implementation
 * Memastikan semua queries di controller terfilter sesuai outlet access
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\File;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHASE 2: QUERY FILTERING IMPLEMENTATION ===\n\n";

// Controllers yang sudah menggunakan HasOutletFilter trait
$controllers = [
    // Finance Module
    'app/Http/Controllers/FinanceAccountantController.php',
    'app/Http/Controllers/FinanceDashboardController.php',
    
    // Dashboard
    'app/Http/Controllers/AdminDashboardController.php',
    'app/Http/Controllers/CrmDashboardController.php',
    'app/Http/Controllers/SalesDashboardController.php',
    
    // Sales & Purchase
    'app/Http/Controllers/SalesManagementController.php',
    'app/Http/Controllers/SalesReportController.php',
    'app/Http/Controllers/MarginReportController.php',
    'app/Http/Controllers/PosController.php',
    'app/Http/Controllers/PurchaseOrderController.php',
    
    // Inventory
    'app/Http/Controllers/ProdukController.php',
    'app/Http/Controllers/BahanController.php',
    'app/Http/Controllers/InventoriController.php',
    'app/Http/Controllers/SparepartController.php',
    'app/Http/Controllers/TransferGudangController.php',
    
    // Service & CRM
    'app/Http/Controllers/ServiceController.php',
    'app/Http/Controllers/CustomerTypeController.php',
    
    // Production
    'app/Http/Controllers/ProductionController.php',
    
    // SDM
    'app/Http/Controllers/RecruitmentManagementController.php',
    'app/Http/Controllers/PayrollManagementController.php',
];

$report = [
    'analyzed' => 0,
    'needs_update' => [],
    'already_filtered' => [],
    'errors' => []
];

foreach ($controllers as $controllerPath) {
    if (!file_exists($controllerPath)) {
        $report['errors'][] = "File not found: $controllerPath";
        continue;
    }
    
    $report['analyzed']++;
    $content = file_get_contents($controllerPath);
    $controllerName = basename($controllerPath);
    
    echo "Analyzing: $controllerName\n";
    
    // Patterns yang perlu difilter
    $patterns = [
        // Model::where() tanpa outlet filter
        '/(\$\w+|\w+)::where\([^)]+\)(?!.*whereIn\([\'"]outlet_id[\'"]\)|.*where\([\'"]outlet_id[\'"]\))/',
        
        // Model::all() atau Model::get() tanpa filter
        '/(\$\w+|\w+)::(all|get)\(\)/',
        
        // $this->model->where() tanpa outlet filter
        '/\$this->\w+->where\([^)]+\)(?!.*whereIn\([\'"]outlet_id[\'"]\)|.*where\([\'"]outlet_id[\'"]\))/',
        
        // Query builder tanpa outlet filter
        '/DB::table\([^)]+\)(?!.*whereIn\([\'"]outlet_id[\'"]\)|.*where\([\'"]outlet_id[\'"]\))/',
    ];
    
    $needsUpdate = false;
    $issues = [];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            $needsUpdate = true;
            $issues[] = "Found unfiltered query pattern";
        }
    }
    
    // Check if already using outlet filtering methods
    $hasOutletFiltering = false;
    if (strpos($content, 'getAccessibleOutletIds()') !== false ||
        strpos($content, 'whereIn(\'outlet_id\'') !== false ||
        strpos($content, 'applyOutletFilter') !== false) {
        $hasOutletFiltering = true;
    }
    
    if ($needsUpdate && !$hasOutletFiltering) {
        $report['needs_update'][] = [
            'file' => $controllerName,
            'path' => $controllerPath,
            'issues' => $issues
        ];
        echo "  ⚠️  Needs outlet filtering\n";
    } else if ($hasOutletFiltering) {
        $report['already_filtered'][] = $controllerName;
        echo "  ✅ Already has outlet filtering\n";
    } else {
        echo "  ℹ️  No queries found or already filtered\n";
    }
    
    echo "\n";
}

// Generate report
echo "\n=== ANALYSIS REPORT ===\n\n";
echo "Total Controllers Analyzed: {$report['analyzed']}\n";
echo "Already Filtered: " . count($report['already_filtered']) . "\n";
echo "Needs Update: " . count($report['needs_update']) . "\n";
echo "Errors: " . count($report['errors']) . "\n\n";

if (!empty($report['needs_update'])) {
    echo "=== CONTROLLERS NEEDING OUTLET FILTERING ===\n\n";
    foreach ($report['needs_update'] as $item) {
        echo "📄 {$item['file']}\n";
        echo "   Path: {$item['path']}\n";
        foreach ($item['issues'] as $issue) {
            echo "   - $issue\n";
        }
        echo "\n";
    }
}

if (!empty($report['already_filtered'])) {
    echo "=== CONTROLLERS WITH OUTLET FILTERING ===\n\n";
    foreach ($report['already_filtered'] as $controller) {
        echo "✅ $controller\n";
    }
    echo "\n";
}

if (!empty($report['errors'])) {
    echo "=== ERRORS ===\n\n";
    foreach ($report['errors'] as $error) {
        echo "❌ $error\n";
    }
    echo "\n";
}

// Generate implementation guide
$guide = "# Phase 2: Query Filtering Implementation Guide

## Overview
This guide provides patterns and examples for implementing outlet filtering in all queries.

## Standard Patterns

### 1. Basic Query Filtering
```php
// Before
\$data = Model::where('status', 'active')->get();

// After
\$outletIds = \$this->getAccessibleOutletIds();
\$data = Model::whereIn('outlet_id', \$outletIds)
    ->where('status', 'active')
    ->get();
```

### 2. Index Method Pattern
```php
public function index(Request \$request)
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$query = Model::whereIn('outlet_id', \$outletIds);
    
    // Apply additional filters
    if (\$request->filled('search')) {
        \$query->where('name', 'like', '%' . \$request->search . '%');
    }
    
    \$data = \$query->paginate(10);
    
    return view('index', compact('data'));
}
```

### 3. Dashboard/Statistics Pattern
```php
public function dashboard()
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$stats = [
        'total' => Model::whereIn('outlet_id', \$outletIds)->count(),
        'active' => Model::whereIn('outlet_id', \$outletIds)
            ->where('status', 'active')
            ->count(),
        'revenue' => Model::whereIn('outlet_id', \$outletIds)
            ->sum('amount'),
    ];
    
    return view('dashboard', compact('stats'));
}
```

### 4. Store Method Pattern
```php
public function store(Request \$request)
{
    \$validated = \$request->validate([...]);
    
    // Ensure outlet_id is set
    \$validated['outlet_id'] = session('outlet_id');
    
    // Validate outlet access
    \$this->validateOutletAccess(\$validated['outlet_id']);
    
    \$model = Model::create(\$validated);
    
    return redirect()->back()->with('success', 'Created successfully');
}
```

### 5. Update Method Pattern
```php
public function update(Request \$request, \$id)
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$model = Model::whereIn('outlet_id', \$outletIds)
        ->findOrFail(\$id);
    
    \$validated = \$request->validate([...]);
    
    \$model->update(\$validated);
    
    return redirect()->back()->with('success', 'Updated successfully');
}
```

### 6. Delete Method Pattern
```php
public function destroy(\$id)
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$model = Model::whereIn('outlet_id', \$outletIds)
        ->findOrFail(\$id);
    
    \$model->delete();
    
    return redirect()->back()->with('success', 'Deleted successfully');
}
```

### 7. Relationship Filtering Pattern
```php
public function show(\$id)
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$model = Model::with(['relation' => function(\$query) use (\$outletIds) {
        \$query->whereIn('outlet_id', \$outletIds);
    }])
    ->whereIn('outlet_id', \$outletIds)
    ->findOrFail(\$id);
    
    return view('show', compact('model'));
}
```

### 8. API Response Pattern
```php
public function getData(Request \$request)
{
    \$outletIds = \$this->getAccessibleOutletIds();
    
    \$data = Model::whereIn('outlet_id', \$outletIds)
        ->select('id', 'name', 'value')
        ->get();
    
    return response()->json(\$data);
}
```

## Controllers Priority List

### High Priority (Dashboard & Reports)
1. FinanceDashboardController
2. AdminDashboardController
3. CrmDashboardController
4. SalesDashboardController
5. SalesReportController
6. MarginReportController

### Medium Priority (Core Operations)
1. FinanceAccountantController
2. SalesManagementController
3. PosController
4. PurchaseOrderController
5. ProductionController

### Standard Priority (CRUD Operations)
1. ProdukController
2. BahanController
3. InventoriController
4. SparepartController
5. TransferGudangController
6. ServiceController
7. CustomerTypeController
8. RecruitmentManagementController
9. PayrollManagementController

## Testing Checklist

For each controller:
- [ ] All index/list queries filtered by outlet
- [ ] All show/detail queries filtered by outlet
- [ ] All store operations set outlet_id
- [ ] All update operations validate outlet access
- [ ] All delete operations validate outlet access
- [ ] All statistics/counts filtered by outlet
- [ ] All API endpoints filtered by outlet
- [ ] All exports filtered by outlet

## Next Steps

1. Start with High Priority controllers
2. Test each controller after implementation
3. Verify with different user roles
4. Check edge cases (superadmin, multi-outlet users)
5. Update documentation
";

file_put_contents('OUTLET_ACCESS_PHASE2_IMPLEMENTATION_GUIDE.md', $guide);
echo "✅ Implementation guide created: OUTLET_ACCESS_PHASE2_IMPLEMENTATION_GUIDE.md\n\n";

echo "=== PHASE 2 ANALYSIS COMPLETE ===\n";
