<?php
/**
 * Check for potential Alpine.js conflicts in admin pages
 * Similar to the roles page issue that was just fixed
 */

echo "=== CHECKING ALPINE.JS CONFLICTS IN ADMIN PAGES ===\n\n";

// List of admin pages that use Alpine.js with @push('scripts')
$adminPagesWithAlpine = [
    'resources/views/admin/supply-chain/permintaan-barang/index.blade.php' => 'permintaanBarangApp',
    'resources/views/admin/service/ongkir/index.blade.php' => 'ongkirCrud',
    'resources/views/admin/service/mesin/index.blade.php' => 'mesinCrud',
    'resources/views/admin/service/history/index.blade.php' => 'historyData',
    'resources/views/admin/sdm/kinerja/index.blade.php' => 'kinerjaCrud',
    'resources/views/admin/sdm/attendance/index.blade.php' => 'attendanceCrud',
    'resources/views/admin/crm/tipe/index.blade.php' => 'customerTypeManagement',
    'resources/views/admin/produksi/produksi/index.blade.php' => 'productionCrud',
    'resources/views/admin/sistem/pengaturan/edit.blade.php' => 'companySettingsForm',
];

$potentialIssues = [];
$workingPages = [];

foreach ($adminPagesWithAlpine as $filePath => $functionName) {
    echo "Checking: " . basename($filePath) . " (function: {$functionName})\n";
    
    if (!file_exists($filePath)) {
        echo "   ❌ File not found\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Check if it uses x-data with the function
    $hasXData = strpos($content, "x-data=\"{$functionName}()\"") !== false;
    
    // Check if it uses @push('scripts')
    $hasPushScripts = strpos($content, "@push('scripts')") !== false;
    
    // Check if the function is defined inline
    $hasFunctionDefinition = strpos($content, "function {$functionName}") !== false;
    
    // Check if it has external JS file
    $hasExternalJS = strpos($content, "asset('js/{$functionName}.js')") !== false || 
                     strpos($content, "asset('js/" . strtolower(str_replace(['Crud', 'App', 'Management', 'Form', 'Data'], '', $functionName)) . ".js')") !== false;
    
    echo "   x-data: " . ($hasXData ? "✅" : "❌") . "\n";
    echo "   @push('scripts'): " . ($hasPushScripts ? "✅" : "❌") . "\n";
    echo "   Inline function: " . ($hasFunctionDefinition ? "✅" : "❌") . "\n";
    echo "   External JS: " . ($hasExternalJS ? "✅" : "❌") . "\n";
    
    // Determine if this could be problematic
    if ($hasXData && $hasPushScripts && $hasFunctionDefinition && !$hasExternalJS) {
        echo "   🚨 POTENTIAL ISSUE: Uses inline function in @push('scripts') - same pattern as roles page\n";
        $potentialIssues[] = [
            'file' => $filePath,
            'function' => $functionName,
            'issue' => 'Inline function in @push(\'scripts\') may cause timing conflicts'
        ];
    } else if ($hasXData && $hasExternalJS) {
        echo "   ✅ LOOKS GOOD: Uses external JS file\n";
        $workingPages[] = $filePath;
    } else if ($hasXData && !$hasPushScripts) {
        echo "   ✅ LOOKS GOOD: No @push('scripts') dependency\n";
        $workingPages[] = $filePath;
    } else {
        echo "   ⚠️  UNCLEAR: Mixed pattern\n";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n\n";

if (count($potentialIssues) > 0) {
    echo "🚨 POTENTIAL ISSUES FOUND: " . count($potentialIssues) . "\n\n";
    
    foreach ($potentialIssues as $issue) {
        echo "❌ " . basename($issue['file']) . "\n";
        echo "   Function: " . $issue['function'] . "\n";
        echo "   Issue: " . $issue['issue'] . "\n\n";
    }
    
    echo "RECOMMENDED ACTIONS:\n";
    echo "1. Test these pages for Alpine.js 'function not defined' errors\n";
    echo "2. If errors found, apply the same fix as roles page:\n";
    echo "   - Create external JS file (e.g., public/js/ongkir.js)\n";
    echo "   - Move function to external file with window.functionName\n";
    echo "   - Load external file with cache busting\n";
    echo "   - Add fallback function for error recovery\n\n";
} else {
    echo "✅ NO OBVIOUS ISSUES FOUND\n\n";
}

echo "✅ WORKING PAGES: " . count($workingPages) . "\n";
foreach ($workingPages as $page) {
    echo "   ✅ " . basename($page) . "\n";
}

echo "\n=== TESTING RECOMMENDATIONS ===\n";
echo "1. Navigate to each page listed above\n";
echo "2. Check browser console for Alpine.js errors\n";
echo "3. Look for 'function not defined' errors\n";
echo "4. Test page functionality (buttons, modals, forms)\n";
echo "5. If errors found, apply the same pattern as roles page fix\n\n";

echo "=== PREVENTION ===\n";
echo "For future pages, use this pattern:\n";
echo "1. Create external JS file: public/js/module.js\n";
echo "2. Define: window.moduleFunction = function() { ... }\n";
echo "3. Load with: <script src=\"{{ asset('js/module.js') }}?v={{ time() }}\"></script>\n";
echo "4. Add fallback function for error recovery\n";
?>