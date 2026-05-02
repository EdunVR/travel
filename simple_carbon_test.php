<?php

echo "Testing Carbon parsing issue...\n";

// Test the problematic string
$problematicString = '2025-12-12 00:00:00 05:01:00';
echo "Problematic string: {$problematicString}\n";

// Function to normalize time field
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
    
    return null;
}

$normalized = normalizeTimeField($problematicString);
echo "Normalized: {$normalized}\n";

echo "Test complete.\n";