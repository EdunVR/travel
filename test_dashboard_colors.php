<?php
/**
 * Test Dashboard Colors - Verify Tailwind CSS Classes
 * 
 * This script helps verify that all dashboard colors are properly compiled
 * Run this after rebuilding Tailwind CSS
 */

echo "=== DASHBOARD COLOR TEST ===\n\n";

// Test colors that should be available
$testColors = [
    // Background gradients
    'from-blue-500', 'to-blue-600', 'from-indigo-500', 'to-indigo-600',
    'from-purple-500', 'to-purple-600', 'from-emerald-500', 'to-emerald-600',
    'from-orange-500', 'to-orange-600', 'from-red-500', 'to-red-600',
    'from-teal-500', 'to-teal-600', 'from-pink-500', 'to-pink-600',
    'from-cyan-500', 'to-cyan-600', 'from-amber-500', 'to-amber-600',
    'from-slate-500', 'to-slate-600',
    
    // Icon backgrounds
    'bg-blue-100', 'bg-indigo-100', 'bg-purple-100', 'bg-emerald-100',
    'bg-orange-100', 'bg-red-100', 'bg-teal-100', 'bg-pink-100',
    'bg-cyan-100', 'bg-amber-100', 'bg-slate-100',
    
    // Icon colors
    'text-blue-600', 'text-indigo-600', 'text-purple-600', 'text-emerald-600',
    'text-orange-600', 'text-red-600', 'text-teal-600', 'text-pink-600',
    'text-cyan-600', 'text-amber-600', 'text-slate-600',
    
    // Borders
    'border-blue-200', 'border-indigo-200', 'border-purple-200', 'border-emerald-200',
    'border-orange-200', 'border-red-200', 'border-teal-200', 'border-pink-200',
    'border-cyan-200', 'border-amber-200', 'border-slate-200'
];

// Check if CSS file exists
$cssPath = __DIR__ . '/public/build/assets';
$cssFiles = [];

if (is_dir($cssPath)) {
    $files = scandir($cssPath);
    foreach ($files as $file) {
        if (strpos($file, '.css') !== false) {
            $cssFiles[] = $file;
        }
    }
}

echo "CSS Files found:\n";
foreach ($cssFiles as $file) {
    echo "- $file\n";
}

if (empty($cssFiles)) {
    echo "❌ No CSS files found! Please run 'npm run build'\n\n";
    echo "Steps to fix:\n";
    echo "1. Run: npm install\n";
    echo "2. Run: npm run build\n";
    echo "3. Run this test again\n\n";
    exit;
}

// Check if the main CSS file contains our colors
$mainCssFile = $cssPath . '/' . $cssFiles[0];
if (file_exists($mainCssFile)) {
    $cssContent = file_get_contents($mainCssFile);
    
    echo "\nTesting color classes:\n";
    $missingColors = [];
    
    foreach ($testColors as $color) {
        // Convert Tailwind class to CSS selector
        $cssClass = '.' . str_replace(':', '\\:', $color);
        
        if (strpos($cssContent, $color) !== false) {
            echo "✅ $color - Found\n";
        } else {
            echo "❌ $color - Missing\n";
            $missingColors[] = $color;
        }
    }
    
    if (empty($missingColors)) {
        echo "\n🎉 ALL COLORS FOUND! Dashboard should work correctly.\n";
    } else {
        echo "\n⚠️  Missing colors found. Please:\n";
        echo "1. Check tailwind.config.js safelist\n";
        echo "2. Run 'npm run build' again\n";
        echo "3. Clear browser cache\n";
        
        echo "\nMissing colors:\n";
        foreach ($missingColors as $color) {
            echo "- $color\n";
        }
    }
} else {
    echo "❌ Could not read CSS file\n";
}

echo "\n=== TEST COMPLETE ===\n";

// Generate HTML test file
$htmlTest = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Color Test</title>
    <link rel="stylesheet" href="/build/assets/' . $cssFiles[0] . '">
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-3xl font-bold mb-8">Dashboard Color Test</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Blue Test -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl text-white">
            <div class="bg-blue-100 border-4 border-blue-200 p-4 rounded-2xl inline-block">
                <i class="text-blue-600 text-4xl">📦</i>
            </div>
            <h3 class="text-xl font-bold mt-4">Blue Theme</h3>
        </div>
        
        <!-- Indigo Test -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-2xl text-white">
            <div class="bg-indigo-100 border-4 border-indigo-200 p-4 rounded-2xl inline-block">
                <i class="text-indigo-600 text-4xl">🚚</i>
            </div>
            <h3 class="text-xl font-bold mt-4">Indigo Theme</h3>
        </div>
        
        <!-- Purple Test -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-2xl text-white">
            <div class="bg-purple-100 border-4 border-purple-200 p-4 rounded-2xl inline-block">
                <i class="text-purple-600 text-4xl">👥</i>
            </div>
            <h3 class="text-xl font-bold mt-4">Purple Theme</h3>
        </div>
    </div>
    
    <div class="mt-8 p-4 bg-white rounded-lg">
        <h2 class="text-xl font-bold mb-4">Test Results:</h2>
        <p>If you can see colored backgrounds and icons above, Tailwind CSS is working correctly!</p>
        <p class="mt-2">If colors are missing, please run the rebuild script.</p>
    </div>
</body>
</html>';

file_put_contents('dashboard_color_test.html', $htmlTest);
echo "\nHTML test file created: dashboard_color_test.html\n";
echo "Open this file in your browser to visually test colors.\n";
?>