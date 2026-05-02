<?php

require_once 'vendor/autoload.php';

// Test script untuk memverifikasi perbaikan export PDF margin report
echo "=== TESTING MARGIN REPORT EXPORT FILTER FIX ===\n\n";

// Test 1: Verify MarginReportController exportPdf method
echo "1. Testing MarginReportController exportPdf method...\n";
$controllerFile = 'app/Http/Controllers/MarginReportController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check if getData is used for consistency
    if (strpos($content, '$response = $this->getData($request)') !== false) {
        echo "   ✓ Export uses getData method for filter consistency\n";
    } else {
        echo "   ✗ Export does not use getData method\n";
    }
    
    // Check if company settings are included
    if (strpos($content, 'companySettings') !== false) {
        echo "   ✓ Company settings integration added\n";
    } else {
        echo "   ✗ Company settings not found\n";
    }
    
    // Check if filters are passed to view
    if (strpos($content, "'filters' =>") !== false) {
        echo "   ✓ Filter information passed to PDF view\n";
    } else {
        echo "   ✗ Filter information not passed to view\n";
    }
    
} else {
    echo "   ✗ MarginReportController not found\n";
}

echo "\n";

// Test 2: Verify PDF template structure
echo "2. Testing PDF template structure...\n";
$pdfTemplate = 'resources/views/admin/penjualan/margin/pdf.blade.php';
if (file_exists($pdfTemplate)) {
    $content = file_get_contents($pdfTemplate);
    
    // Check for header structure like inter-outlet
    if (strpos($content, 'header-container') !== false) {
        echo "   ✓ Inter-outlet style header structure found\n";
    } else {
        echo "   ✗ Header structure not updated\n";
    }
    
    // Check for company logo integration
    if (strpos($content, 'companySettings[\'logo_url\']') !== false) {
        echo "   ✓ Company logo integration added\n";
    } else {
        echo "   ✗ Company logo integration not found\n";
    }
    
    // Check for filter information section
    if (strpos($content, 'filter-section') !== false) {
        echo "   ✓ Filter information section added\n";
    } else {
        echo "   ✗ Filter information section not found\n";
    }
    
    // Check for inter-outlet source handling
    if (strpos($content, 'badge-inter') !== false) {
        echo "   ✓ Inter-outlet source badge added\n";
    } else {
        echo "   ✗ Inter-outlet source badge not found\n";
    }
    
} else {
    echo "   ✗ PDF template not found\n";
}

echo "\n";

// Test 3: Verify CompanySetting model getValue method
echo "3. Testing CompanySetting model...\n";
$modelFile = 'app/Models/CompanySetting.php';
if (file_exists($modelFile)) {
    $content = file_get_contents($modelFile);
    
    // Check if getValue method exists
    if (strpos($content, 'public static function getValue') !== false) {
        echo "   ✓ getValue method found\n";
    } else {
        echo "   ✗ getValue method not found\n";
    }
    
    // Check if outlet parameter is supported
    if (strpos($content, 'int $outletId = null') !== false) {
        echo "   ✓ Outlet-specific settings support added\n";
    } else {
        echo "   ✗ Outlet-specific settings not supported\n";
    }
    
} else {
    echo "   ✗ CompanySetting model not found\n";
}

echo "\n";

// Test 4: Check route exists
echo "4. Testing route configuration...\n";
$routeFiles = ['routes/web.php', 'routes/admin.php'];
$routeFound = false;

foreach ($routeFiles as $routeFile) {
    if (file_exists($routeFile)) {
        $content = file_get_contents($routeFile);
        if (strpos($content, 'margin') !== false && strpos($content, 'export-pdf') !== false) {
            echo "   ✓ Margin export PDF route found in $routeFile\n";
            $routeFound = true;
            break;
        }
    }
}

if (!$routeFound) {
    echo "   ⚠ Margin export PDF route not found - may need to be added\n";
}

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "The margin report export filter fix includes:\n";
echo "1. Export PDF now uses the same getData method as the view\n";
echo "2. PDF template updated with inter-outlet print header style\n";
echo "3. Company settings integration for logo and company info\n";
echo "4. Filter information displayed in PDF\n";
echo "5. Enhanced summary calculations with null handling\n";
echo "6. Support for inter-outlet transactions in export\n\n";

echo "TESTING INSTRUCTIONS:\n";
echo "1. Navigate to Laporan Margin page\n";
echo "2. Apply various filters (outlet, date range, search)\n";
echo "3. Click Export PDF button\n";
echo "4. Verify PDF contains only filtered data\n";
echo "5. Check PDF header matches inter-outlet print style\n";
echo "6. Confirm company logo and info are displayed\n\n";

echo "Test completed!\n";