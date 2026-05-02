<?php
/**
 * Debug HPP Request
 * 
 * Add this temporarily to PackageController::updateHpp() to see what's being sent
 */

// Add this at the beginning of updateHpp() method, after validation:

Log::info('=== HPP UPDATE REQUEST DEBUG ===');
Log::info('All request data:', $request->all());
Log::info('Custom components:', [
    'exists' => $request->has('custom_components'),
    'value' => $request->input('custom_components'),
    'type' => gettype($request->input('custom_components')),
]);

// Check for old format (custom_ keys)
$customKeys = [];
foreach ($request->all() as $key => $value) {
    if (strpos($key, 'custom_') === 0) {
        $customKeys[$key] = $value;
    }
}
Log::info('Custom keys found (old format):', $customKeys);

Log::info('=== END DEBUG ===');
