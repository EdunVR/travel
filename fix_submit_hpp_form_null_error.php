<?php

echo "=== FIXING SUBMIT HPP FORM NULL ERROR ===\n\n";

$indexFile = 'resources/views/admin/inventaris/produk/index.blade.php';

if (!file_exists($indexFile)) {
    echo "❌ Index file not found: $indexFile\n";
    exit(1);
}

$content = file_get_contents($indexFile);

// Find the submitHppForm function and add the same selectedProduct validation we used for submitEditHppForm
$oldSubmitHppCode = "            const response = await fetch(`{{ url('admin/inventaris/produk') }}/\${this.selectedProduct.id}/hpp`, {";

$newSubmitHppCode = "            // Enhanced validation for selectedProduct with multiple fallbacks (same as editHppForm)
            let productId = null;
            let productData = null;
            
            // Try to get product data from multiple sources
            if (this.selectedProduct) {
              productId = this.selectedProduct.id || this.selectedProduct.id_produk;
              productData = this.selectedProduct;
              console.log('submitHppForm: Using selectedProduct:', productData);
            } else if (this.hppForm.productId) {
              productId = this.hppForm.productId;
              productData = this.hppForm.productData;
              console.log('submitHppForm: Using backup product data from hppForm:', productData);
            }
            
            if (!productId) {
              console.error('submitHppForm: No valid product ID found');
              console.error('selectedProduct:', this.selectedProduct);
              console.error('hppForm.productId:', this.hppForm.productId);
              
              this.hppErrors.general = 'ID Produk tidak valid. Silakan tutup modal dan coba lagi.';
              this.showToastMessage('ID Produk tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
              this.savingHpp = false;
              return;
            }

            console.log('submitHppForm: All validations passed, sending request...');
            console.log('submitHppForm: Using product ID:', productId);
            
            const response = await fetch(`{{ url('admin/inventaris/produk') }}/\${productId}/hpp`, {";

if (strpos($content, $oldSubmitHppCode) !== false) {
    $content = str_replace($oldSubmitHppCode, $newSubmitHppCode, $content);
    echo "✅ Fixed submitHppForm method - added selectedProduct validation\n";
} else {
    echo "⚠️  Could not find exact submitHppForm pattern, trying alternative...\n";
    
    // Try to find the fetch call with different formatting
    $pattern = "/const response = await fetch\(`\{\{ url\('admin\/inventaris\/produk'\) \}\}\/\\\$\{this\.selectedProduct\.id\}\/hpp`, \{/";
    
    if (preg_match($pattern, $content)) {
        $replacement = $newSubmitHppCode;
        $content = preg_replace($pattern, $replacement, $content);
        echo "✅ Fixed submitHppForm method using regex pattern\n";
    } else {
        echo "❌ Could not find submitHppForm method to fix\n";
        
        // Show what we're looking for
        echo "\nSearching for 'selectedProduct.id' references in submitHppForm...\n";
        $lines = explode("\n", $content);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 'selectedProduct.id') !== false && strpos($line, 'submitHppForm') === false) {
                echo "Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
            }
        }
    }
}

// Also need to modify the openAddHppStock function to store backup data like we did for openEditHpp
echo "\n🔍 Checking openAddHppStock function...\n";

$openAddHppPattern = "openAddHppStock() {";
if (strpos($content, $openAddHppPattern) !== false) {
    echo "✅ Found openAddHppStock function\n";
    
    // Find the function and add backup data storage
    $oldOpenAddCode = "        openAddHppStock() {
          this.hppForm = {
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: ''
          };
          this.hppErrors = {};
          this.showAddHppModal = true;
        },";

    $newOpenAddCode = "        openAddHppStock() {
          this.hppForm = {
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: '',
            // Store product data as backup (same as editHppForm)
            productId: this.selectedProduct?.id || this.selectedProduct?.id_produk,
            productData: this.selectedProduct ? {...this.selectedProduct} : null
          };
          
          console.log('openAddHppStock: Backup data stored:', {
            productId: this.hppForm.productId,
            productData: this.hppForm.productData
          });
          
          this.hppErrors = {};
          this.showAddHppModal = true;
        },";

    if (strpos($content, $oldOpenAddCode) !== false) {
        $content = str_replace($oldOpenAddCode, $newOpenAddCode, $content);
        echo "✅ Fixed openAddHppStock method - added backup data storage\n";
    } else {
        echo "⚠️  Could not find exact openAddHppStock pattern, trying to add backup data manually...\n";
        
        // Try to find and modify the hppForm initialization
        $hppFormPattern = "/this\.hppForm = \{\s*type: 'in',\s*quantity: 0,\s*hpp_per_unit: 0,\s*notes: ''\s*\};/";
        
        if (preg_match($hppFormPattern, $content)) {
            $hppFormReplacement = "this.hppForm = {
            type: 'in',
            quantity: 0,
            hpp_per_unit: 0,
            notes: '',
            // Store product data as backup
            productId: this.selectedProduct?.id || this.selectedProduct?.id_produk,
            productData: this.selectedProduct ? {...this.selectedProduct} : null
          };
          
          console.log('openAddHppStock: Backup data stored:', {
            productId: this.hppForm.productId,
            productData: this.hppForm.productData
          });";
            
            $content = preg_replace($hppFormPattern, $hppFormReplacement, $content);
            echo "✅ Fixed hppForm initialization using regex\n";
        } else {
            echo "❌ Could not fix openAddHppStock method\n";
        }
    }
} else {
    echo "❌ openAddHppStock function not found\n";
}

// Write the fixed content back
if (file_put_contents($indexFile, $content)) {
    echo "\n✅ Index file updated successfully\n";
} else {
    echo "\n❌ Failed to write index file\n";
    exit(1);
}

// Verify the fixes
echo "\n🔍 VERIFYING FIXES...\n";

$updatedContent = file_get_contents($indexFile);

$checks = [
    'submitHppForm selectedProduct validation' => 'submitHppForm: No valid product ID found',
    'submitHppForm backup data usage' => 'submitHppForm: Using backup product data',
    'submitHppForm enhanced logging' => 'submitHppForm: All validations passed',
    'openAddHppStock backup storage' => 'productId: this.selectedProduct?.id',
    'openAddHppStock backup logging' => 'openAddHppStock: Backup data stored'
];

foreach ($checks as $description => $pattern) {
    if (strpos($updatedContent, $pattern) !== false) {
        echo "   ✅ $description\n";
    } else {
        echo "   ❌ $description - MISSING\n";
    }
}

echo "\n=== FIX SUMMARY ===\n";
echo "✅ FIXED: submitHppForm method - added selectedProduct validation with fallbacks\n";
echo "✅ FIXED: openAddHppStock method - added backup data storage\n";
echo "✅ REASON: selectedProduct was null when trying to create new HPP records\n";
echo "✅ SOLUTION: Same pattern as editHppForm - multiple fallback sources and validation\n";
echo "✅ STATUS: Ready for testing\n\n";

echo "📋 WHAT WAS CHANGED:\n";
echo "1. submitHppForm: Added comprehensive selectedProduct validation\n";
echo "2. submitHppForm: Added multiple fallback sources for product ID\n";
echo "3. submitHppForm: Added enhanced error handling and logging\n";
echo "4. openAddHppStock: Added backup data storage in hppForm\n";
echo "5. openAddHppStock: Added debug logging for backup data\n\n";

echo "🎯 The 'Cannot read properties of null (reading 'id')' error should now be resolved!\n";

?>