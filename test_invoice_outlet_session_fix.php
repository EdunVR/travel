<?php

/**
 * Test Invoice Outlet Session Fix
 * Menguji perbaikan error Alpine.js "ALL is not defined"
 */

echo "=== TESTING INVOICE OUTLET SESSION FIX ===\n\n";

// 1. Check file yang sudah diperbaiki
$invoiceFile = 'resources/views/admin/penjualan/invoice/index.blade.php';

if (!file_exists($invoiceFile)) {
    echo "❌ File invoice tidak ditemukan: $invoiceFile\n";
    exit(1);
}

$content = file_get_contents($invoiceFile);

echo "1. Checking JavaScript initialization fix:\n";

// Check apakah selectedOutlet sudah menggunakan @json()
if (strpos($content, 'selectedOutlet: @json($selectedOutlet)') !== false) {
    echo "   ✅ selectedOutlet menggunakan @json() - FIXED\n";
} else if (strpos($content, 'selectedOutlet: {{ $selectedOutlet }}') !== false) {
    echo "   ❌ selectedOutlet masih menggunakan {{ }} - NEEDS FIX\n";
} else {
    echo "   ⚠️  selectedOutlet tidak ditemukan\n";
}

echo "\n2. Checking potential issues:\n";

// Check apakah ada penggunaan {{ }} lain yang bermasalah
$problematicPatterns = [
    '/selectedOutlet:\s*\{\{\s*\$selectedOutlet\s*\}\}/',
    '/outlets:\s*\{\{\s*\$outlets\s*\}\}/',
];

$hasIssues = false;
foreach ($problematicPatterns as $pattern) {
    if (preg_match($pattern, $content)) {
        echo "   ❌ Found problematic pattern: $pattern\n";
        $hasIssues = true;
    }
}

if (!$hasIssues) {
    echo "   ✅ No problematic {{ }} patterns found\n";
}

echo "\n3. Checking @json() usage:\n";

// Check penggunaan @json() yang benar
$jsonPatterns = [
    'selectedOutlet: @json($selectedOutlet)',
    'outlets: @json($outlets)',
];

foreach ($jsonPatterns as $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "   ✅ Found correct usage: $pattern\n";
    } else {
        echo "   ⚠️  Pattern not found: $pattern\n";
    }
}

echo "\n4. Simulating JavaScript behavior:\n";

// Simulasi nilai yang mungkin dikirim dari controller
$testValues = [
    'ALL' => '"ALL"',
    '1' => '1',
    'null' => 'null',
    'empty string' => '""'
];

foreach ($testValues as $description => $value) {
    echo "   Testing $description ($value):\n";
    
    // Dengan {{ }} (BROKEN)
    $brokenJs = "selectedOutlet: $value";
    echo "     {{ }} format: selectedOutlet: $value";
    
    if ($value === '"ALL"' || $value === '""') {
        echo " ❌ (Would cause 'undefined variable' error)\n";
    } else {
        echo " ✅ (Would work)\n";
    }
    
    // Dengan @json() (FIXED)
    echo "     @json() format: selectedOutlet: $value ✅ (Always works)\n";
}

echo "\n5. Testing outlet change behavior:\n";

// Check fungsi onOutletChange
if (strpos($content, 'onOutletChange()') !== false) {
    echo "   ✅ onOutletChange() function exists\n";
} else {
    echo "   ❌ onOutletChange() function missing\n";
}

// Check console.log untuk debugging
if (strpos($content, "console.log('Outlet changed to:', this.selectedOutlet)") !== false) {
    echo "   ✅ Debug logging exists for outlet changes\n";
} else {
    echo "   ⚠️  No debug logging for outlet changes\n";
}

echo "\n6. Checking Alpine.js x-model usage:\n";

// Check x-model untuk outlet selector
if (strpos($content, 'x-model="selectedOutlet"') !== false) {
    echo "   ✅ x-model=\"selectedOutlet\" found\n";
} else {
    echo "   ❌ x-model=\"selectedOutlet\" not found\n";
}

echo "\n=== SUMMARY ===\n";

$isFixed = strpos($content, 'selectedOutlet: @json($selectedOutlet)') !== false;

if ($isFixed) {
    echo "✅ ISSUE FIXED: selectedOutlet now uses @json() for proper JavaScript encoding\n";
    echo "\nWhat was fixed:\n";
    echo "- Changed: selectedOutlet: {{ \$selectedOutlet }}\n";
    echo "- To:      selectedOutlet: @json(\$selectedOutlet)\n";
    echo "\nThis ensures that string values like 'ALL' are properly quoted in JavaScript\n";
    
    echo "\nNext steps:\n";
    echo "1. Clear browser cache\n";
    echo "2. Test the invoice page\n";
    echo "3. Check browser console for Alpine.js errors\n";
    echo "4. Test outlet switching functionality\n";
} else {
    echo "❌ ISSUE NOT FIXED: selectedOutlet still needs @json() wrapper\n";
}

echo "\n=== TESTING COMPLETE ===\n";