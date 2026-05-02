<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔧 ADDING closeOutletDropdown() METHODS\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Files that need the closeOutletDropdown method
$filesToUpdate = [
    'resources/views/admin/service/index.blade.php' => 'Service Dashboard',
    'resources/views/admin/service/history/index.blade.php' => 'Service History',
    'resources/views/admin/sdm/index.blade.php' => 'SDM Dashboard',
    'resources/views/admin/sdm/attendance/index.blade.php' => 'SDM Attendance',
    'resources/views/admin/penjualan/kontrabon/index.blade.php' => 'Kontrabon',
    'resources/views/admin/penjualan/invoice/index.blade.php' => 'Sales Invoice',
    'resources/views/admin/penjualan/index.blade.php' => 'Sales Dashboard',
    'resources/views/admin/inventaris/produk/index.blade.php' => 'Product Management',
    'resources/views/admin/finance/index.blade.php' => 'Finance Dashboard',
    'resources/views/admin/finance/jurnal/index.blade.php' => 'Finance Journal',
    'resources/views/admin/crm/tipe/index.blade.php' => 'CRM Tipe',
];

$methodToAdd = '
        closeOutletDropdown() {
          // Only close dropdown when clicking outside, not when interacting with checkboxes
          this.showOutletDropdown = false;
        },';

$addedCount = 0;

foreach ($filesToUpdate as $filePath => $description) {
    echo "🔧 Processing: $description\n";
    
    if (!File::exists($filePath)) {
        echo "   ⚠️  File not found, skipping\n\n";
        continue;
    }
    
    $content = File::get($filePath);
    
    // Check if method already exists
    if (strpos($content, 'closeOutletDropdown') !== false) {
        echo "   ℹ️  Method already exists, skipping\n\n";
        continue;
    }
    
    // Find a good place to add the method
    $inserted = false;
    
    // Strategy 1: Look for outlet-related methods
    $insertPatterns = [
        'clearAllOutlets() {',
        'selectAllOutlets() {',
        'onOutletSelectionChange() {',
        'getSelectedOutletsText() {'
    ];
    
    foreach ($insertPatterns as $pattern) {
        if (strpos($content, $pattern) !== false && !$inserted) {
            // Find the end of the method
            $pos = strpos($content, $pattern);
            $braceCount = 0;
            $i = $pos;
            
            // Find the opening brace
            while ($i < strlen($content) && $content[$i] !== '{') {
                $i++;
            }
            $i++; // Skip the opening brace
            $braceCount = 1;
            
            // Find the closing brace
            while ($i < strlen($content) && $braceCount > 0) {
                if ($content[$i] === '{') {
                    $braceCount++;
                } elseif ($content[$i] === '}') {
                    $braceCount--;
                }
                $i++;
            }
            
            // Insert the new method after the closing brace
            if ($braceCount === 0) {
                $content = substr_replace($content, $methodToAdd, $i, 0);
                $inserted = true;
                echo "   ✅ Added method after $pattern\n";
                break;
            }
        }
    }
    
    // Strategy 2: If no outlet methods found, look for any method in the Alpine component
    if (!$inserted) {
        // Look for common method patterns
        $fallbackPatterns = [
            'init() {',
            'loadData() {',
            'fetchData() {',
            'getData() {'
        ];
        
        foreach ($fallbackPatterns as $pattern) {
            if (strpos($content, $pattern) !== false && !$inserted) {
                // Find the end of the method
                $pos = strpos($content, $pattern);
                $braceCount = 0;
                $i = $pos;
                
                // Find the opening brace
                while ($i < strlen($content) && $content[$i] !== '{') {
                    $i++;
                }
                $i++; // Skip the opening brace
                $braceCount = 1;
                
                // Find the closing brace
                while ($i < strlen($content) && $braceCount > 0) {
                    if ($content[$i] === '{') {
                        $braceCount++;
                    } elseif ($content[$i] === '}') {
                        $braceCount--;
                    }
                    $i++;
                }
                
                // Insert the new method after the closing brace
                if ($braceCount === 0) {
                    $content = substr_replace($content, $methodToAdd, $i, 0);
                    $inserted = true;
                    echo "   ✅ Added method after $pattern (fallback)\n";
                    break;
                }
            }
        }
    }
    
    // Strategy 3: Last resort - add before the closing of the Alpine component
    if (!$inserted) {
        // Find the last method in the component and add before the final closing brace
        $lastBracePos = strrpos($content, '}');
        if ($lastBracePos !== false) {
            // Look backwards for the second-to-last brace (end of last method)
            $secondLastBracePos = strrpos($content, '}', $lastBracePos - strlen($content) - 1);
            if ($secondLastBracePos !== false) {
                $content = substr_replace($content, $methodToAdd, $secondLastBracePos + 1, 0);
                $inserted = true;
                echo "   ✅ Added method at end of component (last resort)\n";
            }
        }
    }
    
    if ($inserted) {
        File::put($filePath, $content);
        $addedCount++;
        echo "   💾 Saved changes\n";
    } else {
        echo "   ❌ Could not find suitable location to add method\n";
    }
    
    echo "\n";
}

echo "📊 SUMMARY\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "✅ Methods Added: $addedCount\n";
echo "📁 Files Processed: " . count($filesToUpdate) . "\n\n";

echo "🎯 WHAT WAS ADDED:\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "Method: closeOutletDropdown()\n";
echo "Purpose: Close dropdown only when clicking outside\n";
echo "Behavior: Prevents auto-close when interacting with checkboxes\n\n";

echo "🧪 NEXT STEPS:\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "1. Test each page with outlet dropdown\n";
echo "2. Verify dropdown stays open during checkbox interaction\n";
echo "3. Verify dropdown closes when clicking outside\n";
echo "4. Check console for any JavaScript errors\n\n";

echo "🎉 closeOutletDropdown() METHODS: ADDED!\n";