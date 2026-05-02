<?php

/**
 * Simple Test for Sparepart Export Improvements
 */

echo "=== TESTING SPAREPART EXPORT IMPROVEMENTS ===\n\n";

// Test 1: Check if PDF template exists and has correct structure
echo "1. Testing PDF Template Structure...\n";
$templatePath = 'resources/views/admin/inventaris/sparepart/export-pdf.blade.php';
if (file_exists($templatePath)) {
    echo "   ✓ PDF template exists\n";
    
    $content = file_get_contents($templatePath);
    
    // Check for simple table view
    if (strpos($content, 'simple-table') !== false) {
        echo "   ✓ Simple table CSS class found\n";
    } else {
        echo "   ✗ Simple table CSS class not found\n";
    }
    
    // Check for include_history condition
    if (strpos($content, "include_history") !== false && strpos($content, "=== 'no'") !== false) {
        echo "   ✓ Include history condition found\n";
    } else {
        echo "   ✗ Include history condition not found\n";
    }
    
    // Check for detailed view
    if (strpos($content, 'sparepart-section') !== false) {
        echo "   ✓ Detailed section view found\n";
    } else {
        echo "   ✗ Detailed section view not found\n";
    }
    
    // Check for landscape orientation
    if (strpos($content, 'landscape') !== false) {
        echo "   ✓ Landscape orientation mentioned\n";
    } else {
        echo "   ⚠ Landscape orientation not mentioned in template\n";
    }
    
} else {
    echo "   ✗ PDF template not found at: $templatePath\n";
}

// Test 2: Check Controller Updates
echo "\n2. Testing Controller Updates...\n";
$controllerPath = 'app/Http/Controllers/SparepartController.php';
if (file_exists($controllerPath)) {
    echo "   ✓ Controller file exists\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check for include_history validation
    if (strpos($content, "'include_history' => 'required|in:yes,no'") !== false) {
        echo "   ✓ Include history validation found\n";
    } else {
        echo "   ✗ Include history validation not found\n";
    }
    
    // Check for stream method
    if (strpos($content, '->stream(') !== false) {
        echo "   ✓ PDF stream method found\n";
    } else {
        echo "   ✗ PDF stream method not found\n";
    }
    
    // Check for landscape setting
    if (strpos($content, "setPaper('A4', 'landscape')") !== false) {
        echo "   ✓ Landscape paper setting found\n";
    } else {
        echo "   ✗ Landscape paper setting not found\n";
    }
    
    // Check for JSON parsing
    if (strpos($content, 'json_decode') !== false) {
        echo "   ✓ JSON parsing for form data found\n";
    } else {
        echo "   ✗ JSON parsing for form data not found\n";
    }
    
} else {
    echo "   ✗ Controller file not found at: $controllerPath\n";
}

// Test 3: Check JavaScript Updates
echo "\n3. Testing JavaScript Updates...\n";
$jsPath = 'public/js/sparepart.js';
if (file_exists($jsPath)) {
    echo "   ✓ JavaScript file exists\n";
    
    $content = file_get_contents($jsPath);
    
    // Check for include_history in exportForm
    if (strpos($content, 'include_history: "no"') !== false) {
        echo "   ✓ Include history default value found\n";
    } else {
        echo "   ✗ Include history default value not found\n";
    }
    
    // Check for form submission for PDF
    if (strpos($content, "form.target = '_blank'") !== false) {
        echo "   ✓ PDF form submission for new tab found\n";
    } else {
        echo "   ✗ PDF form submission for new tab not found\n";
    }
    
    // Check for toggleHistoryFilters function
    if (strpos($content, 'toggleHistoryFilters') !== false) {
        echo "   ✓ Toggle history filters function found\n";
    } else {
        echo "   ✗ Toggle history filters function not found\n";
    }
    
} else {
    echo "   ✗ JavaScript file not found at: $jsPath\n";
}

// Test 4: Check View Template Updates
echo "\n4. Testing View Template Updates...\n";
$viewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($viewPath)) {
    echo "   ✓ Main view file exists\n";
    
    $content = file_get_contents($viewPath);
    
    // Check for include history radio buttons
    if (strpos($content, 'include_history') !== false) {
        echo "   ✓ Include history radio buttons found\n";
    } else {
        echo "   ✗ Include history radio buttons not found\n";
    }
    
    // Check for toggleHistoryFilters function call
    if (strpos($content, '@change="toggleHistoryFilters()"') !== false) {
        echo "   ✓ Toggle history filters event found\n";
    } else {
        echo "   ✗ Toggle history filters event not found\n";
    }
    
    // Check for x-show condition for history filters
    if (strpos($content, 'x-show="exportForm.include_history === \'yes\'"') !== false) {
        echo "   ✓ Conditional history filters display found\n";
    } else {
        echo "   ✗ Conditional history filters display not found\n";
    }
    
} else {
    echo "   ✗ Main view file not found at: $viewPath\n";
}

echo "\n=== TESTING COMPLETE ===\n\n";

echo "IMPLEMENTATION STATUS:\n";
echo "✓ = Implemented correctly\n";
echo "✗ = Missing or incorrect\n";
echo "⚠ = Warning or minor issue\n\n";

echo "MANUAL TESTING STEPS:\n";
echo "1. Open Master Sparepart page in browser\n";
echo "2. Click 'Export' button\n";
echo "3. Test 'Tanpa History Detail Log' option:\n";
echo "   - Should show simple table format in PDF\n";
echo "   - PDF should open in new browser tab\n";
echo "4. Test 'Include History Detail Log' option:\n";
echo "   - Should show filter form\n";
echo "   - Should show detailed view with log tables\n";
echo "5. Test with selected items vs all data\n";
echo "6. Verify Excel export still downloads as file\n\n";

echo "EXPECTED RESULTS:\n";
echo "- PDF without history: Clean table like DataTable view\n";
echo "- PDF with history: Detailed sections with individual log tables\n";
echo "- All PDFs open in browser (stream), not download\n";
echo "- Excel files still download normally\n";