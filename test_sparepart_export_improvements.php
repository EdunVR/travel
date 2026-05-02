<?php

/**
 * Test Sparepart Export Improvements
 * 
 * Testing:
 * 1. Export PDF tanpa history (simple table)
 * 2. Export PDF dengan history (detailed view)
 * 3. Stream PDF functionality
 */

echo "=== TESTING SPAREPART EXPORT IMPROVEMENTS ===\n\n";

// Test 1: Check if export route exists
echo "1. Testing Export Route...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $exportRoute = null;
    
    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'sparepart.export')) {
            $exportRoute = $route;
            break;
        }
    }
    
    if ($exportRoute) {
        echo "   ✓ Export route found: " . $exportRoute->getName() . "\n";
    } else {
        echo "   ✗ Export route not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 2: Check if PDF template exists
echo "\n2. Testing PDF Template...\n";
$templatePath = resource_path('views/admin/inventaris/sparepart/export-pdf.blade.php');
if (file_exists($templatePath)) {
    echo "   ✓ PDF template exists\n";
    
    // Check if template has both simple and detailed views
    $content = file_get_contents($templatePath);
    if (strpos($content, 'simple-table') !== false) {
        echo "   ✓ Simple table view found\n";
    } else {
        echo "   ✗ Simple table view not found\n";
    }
    
    if (strpos($content, 'sparepart-section') !== false) {
        echo "   ✓ Detailed section view found\n";
    } else {
        echo "   ✗ Detailed section view not found\n";
    }
    
    if (strpos($content, 'include_history') !== false) {
        echo "   ✓ Include history condition found\n";
    } else {
        echo "   ✗ Include history condition not found\n";
    }
} else {
    echo "   ✗ PDF template not found\n";
}

// Test 3: Check Controller Method
echo "\n3. Testing Controller Method...\n";
try {
    $controller = new \App\Http\Controllers\SparepartController();
    $reflection = new ReflectionClass($controller);
    
    if ($reflection->hasMethod('export')) {
        echo "   ✓ Export method exists\n";
        
        $method = $reflection->getMethod('export');
        $methodContent = file_get_contents($reflection->getFileName());
        
        if (strpos($methodContent, 'include_history') !== false) {
            echo "   ✓ Include history parameter handling found\n";
        } else {
            echo "   ✗ Include history parameter handling not found\n";
        }
        
        if (strpos($methodContent, 'stream(') !== false) {
            echo "   ✓ PDF stream method found\n";
        } else {
            echo "   ✗ PDF stream method not found\n";
        }
    } else {
        echo "   ✗ Export method not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error checking controller: " . $e->getMessage() . "\n";
}

// Test 4: Check JavaScript Updates
echo "\n4. Testing JavaScript Updates...\n";
$jsPath = public_path('js/sparepart.js');
if (file_exists($jsPath)) {
    echo "   ✓ JavaScript file exists\n";
    
    $jsContent = file_get_contents($jsPath);
    
    if (strpos($jsContent, 'include_history') !== false) {
        echo "   ✓ Include history parameter found in JS\n";
    } else {
        echo "   ✗ Include history parameter not found in JS\n";
    }
    
    if (strpos($jsContent, 'form.target = \'_blank\'') !== false) {
        echo "   ✓ PDF stream form submission found\n";
    } else {
        echo "   ✗ PDF stream form submission not found\n";
    }
    
    if (strpos($jsContent, 'toggleHistoryFilters') !== false) {
        echo "   ✓ Toggle history filters function found\n";
    } else {
        echo "   ✗ Toggle history filters function not found\n";
    }
} else {
    echo "   ✗ JavaScript file not found\n";
}

// Test 5: Check Sparepart Model
echo "\n5. Testing Sparepart Model...\n";
try {
    if (class_exists('\App\Models\Sparepart')) {
        echo "   ✓ Sparepart model exists\n";
        
        $model = new \App\Models\Sparepart();
        if (method_exists($model, 'logs')) {
            echo "   ✓ Logs relationship exists\n";
        } else {
            echo "   ✗ Logs relationship not found\n";
        }
    } else {
        echo "   ✗ Sparepart model not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error checking model: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING COMPLETE ===\n\n";

echo "MANUAL TESTING CHECKLIST:\n";
echo "[ ] Open Master Sparepart page\n";
echo "[ ] Click Export button\n";
echo "[ ] Select 'Tanpa History Detail Log' - should show simple table in PDF\n";
echo "[ ] Select 'Include History Detail Log' - should show detailed view with logs\n";
echo "[ ] Check if PDF opens in new tab (stream)\n";
echo "[ ] Test with selected items vs all data\n";
echo "[ ] Test Excel export (should still download file)\n";
echo "\nEXPECTED RESULTS:\n";
echo "- PDF without history: Simple table like DataTable view\n";
echo "- PDF with history: Detailed sections with log tables\n";
echo "- All PDFs should stream (open in browser)\n";
echo "- Excel should download as file\n";