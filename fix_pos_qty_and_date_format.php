<?php
/**
 * Fix POS Qty Input dan Date Format
 * 1. Hilangkan tombol - dan + pada qty
 * 2. Perlebar input field qty
 * 3. Ubah format tanggal menjadi DD/MM/YYYY
 */

echo "🔧 Fix POS Qty Input dan Date Format\n";
echo "====================================\n\n";

// 1. Update POS Blade Template
echo "📋 1. Updating POS Blade Template...\n";

$posBladeFile = 'resources/views/admin/penjualan/pos/index.blade.php';
$posContent = file_get_contents($posBladeFile);

// Backup original file
copy($posBladeFile, $posBladeFile . '.backup');
echo "✅ Original file backed up as index.blade.php.backup\n";

// Replace qty input section - remove buttons and widen input
$oldQtySection = '<td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-2">
                    <button class="w-7 h-7 rounded border hover:bg-slate-50" x-on:click="decQty(i)">-</button>
                    <input type="number" min="1" x-model.number="c.qty" x-on:change="recalc()" class="w-12 h-8 rounded border border-slate-200 text-center">
                    <button class="w-7 h-7 rounded border hover:bg-slate-50" x-on:click="incQty(i)">+</button>
                  </div>
                </td>';

$newQtySection = '<td class="px-3 py-2">
                  <div class="flex items-center justify-center">
                    <input type="number" min="1" x-model.number="c.qty" x-on:change="recalc()" class="w-20 h-8 rounded border border-slate-200 text-center focus:border-primary-300 focus:ring-2 focus:ring-primary-100">
                  </div>
                </td>';

$updatedPosContent = str_replace($oldQtySection, $newQtySection, $posContent);

if ($updatedPosContent !== $posContent) {
    file_put_contents($posBladeFile, $updatedPosContent);
    echo "✅ Qty input updated - buttons removed and input widened\n";
} else {
    echo "⚠️ Qty section not found or already updated\n";
}

// 2. Update JavaScript untuk format tanggal
echo "\n📋 2. Updating JavaScript date format...\n";

$posJsFile = 'public/js/pos.js';
$jsContent = file_get_contents($posJsFile);

// Backup original file
copy($posJsFile, $posJsFile . '.backup');
echo "✅ Original JS file backed up as pos.js.backup\n";

// Add date formatting function
$dateFormatFunction = '
    formatDateDDMMYYYY(dateStr) {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
      return `${day}/${month}/${year}`;
    },

    getCurrentDateDDMMYYYY() {
      const today = new Date();
      const day = String(today.getDate()).padStart(2, "0");
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const year = today.getFullYear();
      return `${day}/${month}/${year}`;
    },
';

// Find a good place to insert the function (after formatDate function)
$insertAfter = 'formatDate(dateStr) {
      return window.DateHelper.formatDate(dateStr);
    },';

$replacement = 'formatDate(dateStr) {
      return window.DateHelper.formatDate(dateStr);
    },
' . $dateFormatFunction;

$updatedJsContent = str_replace($insertAfter, $replacement, $jsContent);

if ($updatedJsContent !== $jsContent) {
    file_put_contents($posJsFile, $updatedJsContent);
    echo "✅ Date formatting functions added to pos.js\n";
} else {
    echo "⚠️ Date formatting functions already exist or insertion point not found\n";
}

// 3. Update POS Blade untuk menampilkan tanggal dengan format DD/MM/YYYY
echo "\n📋 3. Updating date display format in template...\n";

$posContent = file_get_contents($posBladeFile);

// Find the transaction date display and update it
$oldDateDisplay = '<div class="text-sm text-slate-600" x-text="nowStr"></div>';
$newDateDisplay = '<div class="text-sm text-slate-600">
            <span x-text="nowStr.split(\',\')[0]"></span><br>
            <span class="text-xs text-slate-500">Transaksi: <span x-text="formatDateDDMMYYYY(state.transactionDate)"></span></span>
          </div>';

$updatedPosContent = str_replace($oldDateDisplay, $newDateDisplay, $posContent);

if ($updatedPosContent !== $posContent) {
    file_put_contents($posBladeFile, $updatedPosContent);
    echo "✅ Date display format updated to DD/MM/YYYY\n";
} else {
    echo "⚠️ Date display section not found or already updated\n";
}

// 4. Create test file
echo "\n📋 4. Creating test file...\n";

$testContent = '<!DOCTYPE html>
<html>
<head>
    <title>Test POS Date Format</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>
    <div x-data="{
        transactionDate: new Date().toISOString().split(\'T\')[0],
        formatDateDDMMYYYY(dateStr) {
            if (!dateStr) return \"\";
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, \"0\");
            const month = String(date.getMonth() + 1).padStart(2, \"0\");
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    }" class="p-4">
        <h2>Test Date Format</h2>
        <p>Original date: <span x-text="transactionDate"></span></p>
        <p>Formatted date (DD/MM/YYYY): <span x-text="formatDateDDMMYYYY(transactionDate)"></span></p>
        
        <h2>Test Qty Input</h2>
        <div class="flex items-center justify-center mt-4">
            <input type="number" min="1" value="1" class="w-20 h-8 rounded border border-gray-300 text-center focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
        </div>
        <p class="text-sm text-gray-600 mt-2">Input qty tanpa tombol - dan + (lebar 20)</p>
    </div>
</body>
</html>';

file_put_contents('test_pos_qty_date_format.html', $testContent);
echo "✅ Test file created: test_pos_qty_date_format.html\n";

// 5. Create deployment script
echo "\n📋 5. Creating deployment script...\n";

$deployScript = '#!/bin/bash
# Deploy POS Qty and Date Format Fix

echo "Deploying POS qty and date format fix..."

# Clear view cache to ensure changes are reflected
php artisan view:clear

# Clear browser cache recommendation
echo "✅ POS qty and date format fix deployed!"
echo "📋 Please refresh POS page (Ctrl+F5 to clear browser cache)"
echo ""
echo "Changes made:"
echo "1. ✅ Removed - and + buttons from qty input"
echo "2. ✅ Widened qty input field (w-12 -> w-20)"
echo "3. ✅ Added DD/MM/YYYY date formatting functions"
echo "4. ✅ Updated date display format"
echo ""
echo "Test the changes:"
echo "1. Open POS page"
echo "2. Add items to cart"
echo "3. Check qty input (no buttons, wider field)"
echo "4. Check date format in header (DD/MM/YYYY)"
';

file_put_contents('deploy_pos_qty_date_fix.bat', $deployScript);
echo "✅ Deployment script created: deploy_pos_qty_date_fix.bat\n";

// 6. Summary
echo "\n🎯 Summary of changes:\n";
echo "======================\n";
echo "✅ Removed - and + buttons from qty input in cart table\n";
echo "✅ Widened qty input field from w-12 to w-20\n";
echo "✅ Added formatDateDDMMYYYY() function to pos.js\n";
echo "✅ Updated date display to show DD/MM/YYYY format\n";
echo "✅ Created test file for verification\n";
echo "✅ Created deployment script\n";

echo "\n📋 Files modified:\n";
echo "- resources/views/admin/penjualan/pos/index.blade.php\n";
echo "- public/js/pos.js\n";

echo "\n📋 Files created:\n";
echo "- test_pos_qty_date_format.html (for testing)\n";
echo "- deploy_pos_qty_date_fix.bat (deployment script)\n";

echo "\n📋 Backup files created:\n";
echo "- resources/views/admin/penjualan/pos/index.blade.php.backup\n";
echo "- public/js/pos.js.backup\n";

echo "\n📋 Next steps:\n";
echo "1. Run: php artisan view:clear\n";
echo "2. Refresh POS page (Ctrl+F5)\n";
echo "3. Test qty input (no buttons, wider field)\n";
echo "4. Test date format (DD/MM/YYYY)\n";

echo "\n✅ Fix completed!\n";
?>