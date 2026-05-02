<?php

echo "=== Verifying SDM Dashboard Syntax ===\n";

// Read the file and check for basic syntax issues
$filePath = 'resources/views/admin/sdm/index.blade.php';
$content = file_get_contents($filePath);

// Count opening and closing tags
$openingTags = substr_count($content, '<x-layouts.admin');
$closingTags = substr_count($content, '</x-layouts.admin>');

echo "Opening tags found: $openingTags\n";
echo "Closing tags found: $closingTags\n";

if ($openingTags === $closingTags && $openingTags === 1) {
    echo "✓ Tag balance is correct (1 opening, 1 closing)\n";
} else {
    echo "✗ Tag balance is incorrect\n";
}

// Check for common syntax issues
$issues = [];

if (strpos($content, '<x-layouts.admin title="Dashboard SDM">
<x-layouts.admin') !== false) {
    $issues[] = "Duplicate opening tags found";
}

if (empty($issues)) {
    echo "✓ No obvious syntax issues detected\n";
} else {
    echo "✗ Issues found:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}

echo "\n=== Verification Complete ===\n";