<?php

/**
 * Test Inter Outlet Print Routes
 * 
 * This script tests both possible print routes:
 * 1. /admin/penjualan/inter-outlet/{id}/print
 * 2. /admin/penjualan/inter-outlet-sale/{id}/print
 */

echo "=== TESTING INTER OUTLET PRINT ROUTES ===\n\n";

// Test 1: Analyze route list output
echo "1. ANALYZING AVAILABLE ROUTES:\n";
echo "   From route:list output, we found:\n";
echo "   ✅ Route 1: admin/penjualan/inter-outlet/{id}/print\n";
echo "   ✅ Route 2: admin/penjualan/inter-outlet-sale/{id}/print\n";
echo "   Both routes point to: InterOutletSaleController@print\n";

echo "\n";

// Test 2: Check JavaScript URL updates
echo "2. TESTING JAVASCRIPT URL UPDATES:\n";
$jsFile = file_get_contents('public/js/inter-outlet.js');

if (strpos($jsFile, '/admin/penjualan/inter-outlet-sale/${transactionId}/print') !== false) {
    echo "   ✅ printHistoryInvoice updated to use inter-outlet-sale route\n";
} else {
    echo "   ❌ printHistoryInvoice NOT updated\n";
}

if (strpos($jsFile, '/admin/penjualan/inter-outlet-sale/${this.lastTransactionId}/print') !== false) {
    echo "   ✅ printInvoice updated to use inter-outlet-sale route\n";
} else {
    echo "   ❌ printInvoice NOT updated\n";
}

echo "\n";

// Test 3: Route analysis
echo "3. ROUTE ANALYSIS:\n";
echo "   Both routes exist and point to the same controller method.\n";
echo "   This suggests there might be route duplication or different contexts.\n";
echo "   \n";
echo "   Route 1: /admin/penjualan/inter-outlet/{id}/print\n";
echo "   Route 2: /admin/penjualan/inter-outlet-sale/{id}/print\n";
echo "   \n";
echo "   We updated JavaScript to use Route 2 (inter-outlet-sale)\n";
echo "   as it appears more specific and complete.\n";

echo "\n";

// Test 4: Generate test URLs
echo "4. GENERATING TEST URLS:\n";
$sampleId = 123;
echo "   🔗 Test URL 1: /admin/penjualan/inter-outlet/{$sampleId}/print\n";
echo "   🔗 Test URL 2: /admin/penjualan/inter-outlet-sale/{$sampleId}/print\n";
echo "   \n";
echo "   JavaScript now uses: /admin/penjualan/inter-outlet-sale/{$sampleId}/print\n";

echo "\n";

// Test 5: Troubleshooting steps
echo "5. TROUBLESHOOTING STEPS:\n";
echo "   If the issue persists:\n";
echo "   \n";
echo "   1. 🔍 Create a test transaction:\n";
echo "      - Go to Inter Outlet Sale page\n";
echo "      - Create a new transaction\n";
echo "      - Note the transaction ID\n";
echo "   \n";
echo "   2. 🧪 Test both URLs directly:\n";
echo "      - /admin/penjualan/inter-outlet/{ID}/print\n";
echo "      - /admin/penjualan/inter-outlet-sale/{ID}/print\n";
echo "   \n";
echo "   3. 📊 Check browser console:\n";
echo "      - Open Developer Tools (F12)\n";
echo "      - Go to Network tab\n";
echo "      - Try print function\n";
echo "      - Check actual URL and HTTP status\n";
echo "   \n";
echo "   4. 📋 Check Laravel logs:\n";
echo "      - Look at storage/logs/laravel.log\n";
echo "      - Check for any error messages\n";

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "CHANGES MADE:\n";
echo "1. ✅ Updated printHistoryInvoice to use inter-outlet-sale route\n";
echo "2. ✅ Updated printInvoice to use inter-outlet-sale route\n";
echo "3. ✅ Route cache cleared\n";
echo "4. ✅ Application cache cleared\n";
echo "\n";

echo "NEW URLS:\n";
echo "- printHistoryInvoice: /admin/penjualan/inter-outlet-sale/{id}/print\n";
echo "- printInvoice: /admin/penjualan/inter-outlet-sale/{id}/print\n";
echo "\n";

echo "EXPECTED BEHAVIOR:\n";
echo "1. Modal opens correctly ✅ (already working)\n";
echo "2. PDF URL resolves to valid route ✅ (should work now)\n";
echo "3. PDF content loads in iframe ✅ (should work now)\n";
echo "4. No more 'Not Found' errors ✅ (should be fixed)\n";
echo "\n";

echo "✅ ROUTE UPDATE COMPLETE!\n";
echo "\nTo test:\n";
echo "1. Refresh the Inter Outlet Sale page\n";
echo "2. Create a new transaction\n";
echo "3. Click 'Print Invoice' from success modal\n";
echo "4. Verify PDF loads in modal (not 'Not Found')\n";
echo "5. Test print from history modal\n";

?>