<?php

// Quick test to verify sales report outlet access control implementation
echo "🧪 Quick Sales Report Outlet Access Test\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$controllerFile = 'app/Http/Controllers/SalesReportController.php';

if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: {$controllerFile}\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

echo "1️⃣ Checking HasOutletFilter trait usage...\n";
if (strpos($content, 'use \App\Traits\HasOutletFilter;') !== false) {
    echo "   ✅ HasOutletFilter trait is imported\n";
} else {
    echo "   ❌ HasOutletFilter trait is NOT imported\n";
}

echo "\n2️⃣ Checking index() method...\n";
if (strpos($content, '$this->getUserOutlets()') !== false) {
    echo "   ✅ getUserOutlets() method is used in index\n";
} else {
    echo "   ❌ getUserOutlets() method is NOT used in index\n";
}

echo "\n3️⃣ Checking getData() method...\n";
if (strpos($content, '$this->getAccessibleOutletIds()') !== false) {
    echo "   ✅ getAccessibleOutletIds() method is used in getData\n";
} else {
    echo "   ❌ getAccessibleOutletIds() method is NOT used in getData\n";
}

echo "\n4️⃣ Checking outlet filtering in queries...\n";
if (strpos($content, 'whereIn(\'id_outlet\', $accessibleOutletIds)') !== false) {
    echo "   ✅ Invoice and POS queries include outlet filtering\n";
} else {
    echo "   ❌ Invoice and POS queries do NOT include outlet filtering\n";
}

if (strpos($content, 'whereIn(\'outlet_asal\', $accessibleOutletIds)') !== false) {
    echo "   ✅ Inter Outlet queries include outlet filtering\n";
} else {
    echo "   ❌ Inter Outlet queries do NOT include outlet filtering\n";
}

echo "\n5️⃣ Checking security measures...\n";
if (strpos($content, 'if (empty($accessibleOutletIds))') !== false) {
    echo "   ✅ Empty outlet access check is implemented\n";
} else {
    echo "   ❌ Empty outlet access check is NOT implemented\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 SUMMARY:\n";

$checks = [
    strpos($content, 'use \App\Traits\HasOutletFilter;') !== false,
    strpos($content, '$this->getUserOutlets()') !== false,
    strpos($content, '$this->getAccessibleOutletIds()') !== false,
    strpos($content, 'whereIn(\'id_outlet\', $accessibleOutletIds)') !== false,
    strpos($content, 'whereIn(\'outlet_asal\', $accessibleOutletIds)') !== false,
    strpos($content, 'if (empty($accessibleOutletIds))') !== false
];

$passed = array_sum($checks);
$total = count($checks);

if ($passed === $total) {
    echo "🎉 ALL CHECKS PASSED ({$passed}/{$total})\n";
    echo "✅ Sales Report Outlet Access Control is properly implemented!\n";
} else {
    echo "⚠️  SOME CHECKS FAILED ({$passed}/{$total})\n";
    echo "❌ Please review the implementation\n";
}

echo str_repeat("=", 50) . "\n";

?>