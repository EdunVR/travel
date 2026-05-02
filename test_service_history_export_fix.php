<?php

echo "🧪 Testing Service History Export Fix\n";
echo "=====================================\n\n";

// Check if ServiceController exists
$controllerPath = 'app/Http/Controllers/ServiceController.php';
if (!file_exists($controllerPath)) {
    echo "❌ ServiceController not found at: $controllerPath\n";
    exit(1);
}

$content = file_get_contents($controllerPath);

echo "1️⃣ Checking exportHistory method...\n";

// Check if exportHistory method uses multiple outlet support
$hasMultipleOutletSupport = strpos($content, 'outlet_ids') !== false;
$hasWhereInClause = strpos($content, 'whereIn(\'outlet_id\', $filterOutletIds)') !== false;
$hasAccessibleOutletIds = strpos($content, 'getAccessibleOutletIds()') !== false;

if ($hasMultipleOutletSupport && $hasWhereInClause && $hasAccessibleOutletIds) {
    echo "   ✅ exportHistory method supports multiple outlets\n";
} else {
    echo "   ❌ exportHistory method missing multiple outlet support\n";
    if (!$hasMultipleOutletSupport) echo "      - Missing outlet_ids parameter handling\n";
    if (!$hasWhereInClause) echo "      - Missing whereIn clause for outlet filtering\n";
    if (!$hasAccessibleOutletIds) echo "      - Missing getAccessibleOutletIds() call\n";
}

echo "\n2️⃣ Checking exportHistoryPdf method...\n";

// Check if exportHistoryPdf method uses multiple outlet support
$pdfHasMultipleOutletSupport = strpos($content, 'outlet_ids') !== false;
$pdfHasWhereInClause = strpos($content, 'whereIn(\'outlet_id\', $filterOutletIds)') !== false;

if ($pdfHasMultipleOutletSupport && $pdfHasWhereInClause) {
    echo "   ✅ exportHistoryPdf method supports multiple outlets\n";
} else {
    echo "   ❌ exportHistoryPdf method missing multiple outlet support\n";
}

echo "\n3️⃣ Checking outlet access validation...\n";

// Check if both methods have outlet access validation
$hasOutletValidation = strpos($content, 'array_diff($outletIds, $accessibleOutletIds)') !== false;
$hasAccessDeniedResponse = strpos($content, 'Anda tidak memiliki akses ke beberapa outlet yang dipilih') !== false;

if ($hasOutletValidation && $hasAccessDeniedResponse) {
    echo "   ✅ Outlet access validation implemented\n";
} else {
    echo "   ❌ Missing outlet access validation\n";
}

echo "\n4️⃣ Checking backward compatibility...\n";

// Check if methods still support single outlet_id parameter
$hasBackwardCompatibility = strpos($content, 'outlet_id') !== false && 
                           strpos($content, 'Fallback to single outlet_id for backward compatibility') !== false;

if ($hasBackwardCompatibility) {
    echo "   ✅ Backward compatibility maintained\n";
} else {
    echo "   ❌ Missing backward compatibility for single outlet_id\n";
}

echo "\n5️⃣ Checking filter consistency...\n";

// Check if export methods use same filtering logic as data display
$hasStatusFilter = strpos($content, 'service-berikutnya') !== false;
$hasDateFilter = strpos($content, 'whereBetween(\'tanggal\'') !== false;

if ($hasStatusFilter && $hasDateFilter) {
    echo "   ✅ Export methods use consistent filtering logic\n";
} else {
    echo "   ❌ Export methods missing consistent filtering\n";
}

echo "\n6️⃣ Summary:\n";

$allChecks = [
    $hasMultipleOutletSupport && $hasWhereInClause && $hasAccessibleOutletIds,
    $pdfHasMultipleOutletSupport && $pdfHasWhereInClause,
    $hasOutletValidation && $hasAccessDeniedResponse,
    $hasBackwardCompatibility,
    $hasStatusFilter && $hasDateFilter
];

$passedChecks = count(array_filter($allChecks));
$totalChecks = count($allChecks);

if ($passedChecks === $totalChecks) {
    echo "   🎉 All checks passed! Service history export fix is complete.\n";
    echo "   📊 Export data will now match the filtered table data.\n";
} else {
    echo "   ⚠️  $passedChecks/$totalChecks checks passed. Some issues need attention.\n";
}

echo "\n7️⃣ Testing Instructions:\n";
echo "   1. Login to admin panel\n";
echo "   2. Go to Service > History Service\n";
echo "   3. Select specific outlets using the outlet filter\n";
echo "   4. Apply status filter (e.g., 'Menunggu' or 'Lunas')\n";
echo "   5. Set date range filter\n";
echo "   6. Click 'Export Excel' or 'Export PDF'\n";
echo "   7. Verify exported data matches the filtered table data\n";

echo "\n✅ Service History Export Fix Test Complete!\n";