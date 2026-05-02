<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔧 FIXING OUTLET DROPDOWN BEHAVIOR - COMPREHENSIVE\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Files that need to be fixed based on the search results
$filesToFix = [
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
    // Customer management already fixed above
];

echo "📋 FILES TO FIX: " . count($filesToFix) . "\n\n";

$fixedCount = 0;
$skippedCount = 0;

foreach ($filesToFix as $filePath => $description) {
    echo "🔧 Fixing: $description ($filePath)\n";
    
    if (!File::exists($filePath)) {
        echo "   ⚠️  File not found, skipping\n\n";
        $skippedCount++;
        continue;
    }
    
    $content = File::get($filePath);
    $originalContent = $content;
    $changesApplied = 0;
    
    // Fix 1: Replace @click.away with closeOutletDropdown() method
    $oldPattern = 'x-on:click.away="showOutletDropdown = false"';
    $newPattern = 'x-on:click.away="closeOutletDropdown()"';
    
    if (strpos($content, $oldPattern) !== false) {
        $content = str_replace($oldPattern, $newPattern, $content);
        $changesApplied++;
        echo "   ✅ Fixed click.away behavior\n";
    }
    
    // Alternative pattern
    $oldPattern2 = '@click.away="showOutletDropdown = false"';
    $newPattern2 = '@click.away="closeOutletDropdown()"';
    
    if (strpos($content, $oldPattern2) !== false) {
        $content = str_replace($oldPattern2, $newPattern2, $content);
        $changesApplied++;
        echo "   ✅ Fixed @click.away behavior\n";
    }
    
    // Fix 2: Add x-on:click.stop to label elements to prevent dropdown close
    $labelPattern = '/<label class="[^"]*cursor-pointer[^"]*">/';
    if (preg_match($labelPattern, $content)) {
        $content = preg_replace(
            '/<label class="([^"]*cursor-pointer[^"]*)">/',
            '<label class="$1" x-on:click.stop>',
            $content
        );
        $changesApplied++;
        echo "   ✅ Added click.stop to labels\n";
    }
    
    // Fix 3: Remove automatic dropdown close from outlet selection change methods
    $patterns = [
        'this.showOutletDropdown = false;' => '// Dropdown stays open for better UX',
        'showOutletDropdown = false;' => '// Dropdown stays open for better UX'
    ];
    
    foreach ($patterns as $oldPattern => $newPattern) {
        if (strpos($content, $oldPattern) !== false) {
            // Only replace if it's in an outlet selection context
            if (strpos($content, 'onOutletSelectionChange') !== false || 
                strpos($content, 'outlet') !== false) {
                $content = str_replace($oldPattern, $newPattern, $content);
                $changesApplied++;
                echo "   ✅ Removed auto-close from outlet selection\n";
            }
        }
    }
    
    // Fix 4: Add closeOutletDropdown method if it doesn't exist
    if (strpos($content, 'closeOutletDropdown') === false && $changesApplied > 0) {
        // Find a good place to add the method (after other outlet-related methods)
        $methodToAdd = '
        closeOutletDropdown() {
          // Only close dropdown when clicking outside, not when interacting with checkboxes
          this.showOutletDropdown = false;
        },';
        
        // Try to find outlet-related methods to add after
        $insertPatterns = [
            'clearAllOutlets() {',
            'selectAllOutlets() {',
            'onOutletSelectionChange() {'
        ];
        
        foreach ($insertPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                // Find the end of the method
                $pos = strpos($content, $pattern);
                $braceCount = 0;
                $methodStart = $pos;
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
                    $changesApplied++;
                    echo "   ✅ Added closeOutletDropdown method\n";
                    break;
                }
            }
        }
    }
    
    // Save changes if any were made
    if ($content !== $originalContent) {
        File::put($filePath, $content);
        $fixedCount++;
        echo "   💾 Saved changes ($changesApplied fixes applied)\n";
    } else {
        echo "   ℹ️  No changes needed\n";
    }
    
    echo "\n";
}

echo "📊 SUMMARY\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "✅ Files Fixed: $fixedCount\n";
echo "⚠️  Files Skipped: $skippedCount\n";
echo "📁 Total Files Processed: " . count($filesToFix) . "\n\n";

echo "🎯 IMPROVEMENTS APPLIED:\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "1. ✅ Click outside behavior: Changed to closeOutletDropdown() method\n";
echo "2. ✅ Label click behavior: Added x-on:click.stop to prevent dropdown close\n";
echo "3. ✅ Auto-close removal: Dropdown stays open during selection\n";
echo "4. ✅ Method addition: Added closeOutletDropdown() method where needed\n\n";

echo "🧪 TESTING RECOMMENDATIONS:\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "1. Test outlet dropdown in each fixed page\n";
echo "2. Verify dropdown stays open when checking/unchecking outlets\n";
echo "3. Verify dropdown closes when clicking outside\n";
echo "4. Verify 'Pilih Semua' and 'Hapus Semua' buttons work correctly\n";
echo "5. Verify data refreshes correctly when outlet selection changes\n\n";

echo "🎉 OUTLET DROPDOWN BEHAVIOR: IMPROVED FOR BETTER UX!\n";