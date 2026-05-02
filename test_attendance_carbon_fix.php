<?php

require_once 'vendor/autoload.php';

use Carbon\Carbon;

echo "=== Testing Carbon Parsing Fix ===\n\n";

// Test cases that were causing the error
$testCases = [
    '2025-12-12 00:00:00 05:01:00', // The problematic format
    '2025-12-12 05:01:00',          // Normal datetime
    '05:01:00',                     // Time only
    '05:01',                        // Time without seconds
];

echo "1. Testing normalizeTimeField function:\n";

function normalizeTimeField($timeValue)
{
    if (empty($timeValue)) {
        return null;
    }
    
    $timeValue = trim($timeValue);
    
    // Handle malformed strings like "2025-12-12 00:00:00 05:01:00" (double time specification)
    if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\s+(\d{2}:\d{2}:\d{2})$/', $timeValue, $matches)) {
        return $matches[1];
    }
    
    // If it contains date and time (YYYY-MM-DD HH:MM:SS), extract only time
    if (preg_match('/^\d{4}-\d{2}-\d{2}\s+(\d{2}:\d{2}:\d{2})$/', $timeValue, $matches)) {
        return $matches[1];
    }
    
    // If it's HH:MM format, add seconds
    if (preg_match('/^\d{2}:\d{2}$/', $timeValue)) {
        return $timeValue . ':00';
    }
    
    // If it's already HH:MM:SS format
    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $timeValue)) {
        return $timeValue;
    }
    
    // Handle other malformed formats - extract any valid time pattern
    if (preg_match('/(\d{2}:\d{2}:\d{2})/', $timeValue, $matches)) {
        return $matches[1];
    }
    
    // If format is not recognized, return null
    echo "Warning: Unknown time format: {$timeValue}\n";
    return null;
}

foreach ($testCases as $testCase) {
    $normalized = normalizeTimeField($testCase);
    echo "Input: '{$testCase}' -> Output: '{$normalized}'\n";
    
    if ($normalized) {
        try {
            $carbon = Carbon::parse('2000-01-01 ' . $normalized);
            echo "  ✓ Carbon parsing successful: {$carbon->format('H:i:s')}\n";
        } catch (Exception $e) {
            echo "  ✗ Carbon parsing failed: {$e->getMessage()}\n";
        }
    }
    echo "\n";
}

echo "2. Testing the original problematic case:\n";
$problematicString = '2025-12-12 00:00:00 05:01:00';
echo "Original error case: '{$problematicString}'\n";

try {
    $carbon = Carbon::parse($problematicString);
    echo "  ✗ This should have failed but didn't\n";
} catch (Exception $e) {
    echo "  ✓ Expected error: {$e->getMessage()}\n";
}

$fixed = normalizeTimeField($problematicString);
echo "After normalization: '{$fixed}'\n";

try {
    $carbon = Carbon::parse('2000-01-01 ' . $fixed);
    echo "  ✓ Fixed parsing successful: {$carbon->format('H:i:s')}\n";
} catch (Exception $e) {
    echo "  ✗ Still failing: {$e->getMessage()}\n";
}

echo "\n=== Test Complete ===\n";