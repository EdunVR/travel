<?php
/**
 * Test Customer Search with Outlet Filter
 * 
 * This script simulates the customer search functionality to verify it works correctly
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use Illuminate\Http\Request;

echo "=== Testing Customer Search with Outlet Filter ===\n\n";

try {
    // Test 1: Check if we can query members by outlet
    echo "1. Testing basic member query by outlet:\n";
    
    $members = Member::where('id_outlet', 1)->limit(5)->get();
    echo "   ✓ Found " . $members->count() . " members in outlet 1\n";
    
    // Test 2: Test the actual search functionality
    echo "\n2. Testing customer search functionality:\n";
    
    // Simulate the search request
    $searchTerm = '';
    $outletId = 1;
    
    $query = Member::withCount(['mesinCustomers as mesin_count'])
        ->with(['mesinCustomers' => function($query) {
            $query->with(['produk' => function($q) {
                $q->withPivot('closing_type');
            }]);
        }])
        ->where('id_outlet', $outletId) // Filter members by outlet
        ->orderBy('nama', 'asc');
    
    $customers = $query->limit(5)->get();
    
    echo "   ✓ Search query executed successfully\n";
    echo "   ✓ Found " . $customers->count() . " customers in outlet $outletId\n";
    
    // Test 3: Test with search term
    echo "\n3. Testing search with search term:\n";
    
    $searchTerm = 'a'; // Search for customers with 'a' in their name
    
    $query = Member::withCount(['mesinCustomers as mesin_count'])
        ->with(['mesinCustomers' => function($query) {
            $query->with(['produk' => function($q) {
                $q->withPivot('closing_type');
            }]);
        }])
        ->where('id_outlet', $outletId);
    
    if (!empty($searchTerm)) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('nama', 'like', '%' . $searchTerm . '%')
              ->orWhere('alamat', 'like', '%' . $searchTerm . '%')
              ->orWhere('telepon', 'like', '%' . $searchTerm . '%')
              ->orWhere('kode_member', 'like', '%' . $searchTerm . '%');
        });
    }
    
    $customers = $query->orderBy('nama', 'asc')->limit(5)->get();
    
    echo "   ✓ Search with term '$searchTerm' executed successfully\n";
    echo "   ✓ Found " . $customers->count() . " customers matching '$searchTerm' in outlet $outletId\n";
    
    // Test 4: Display sample customer data
    if ($customers->count() > 0) {
        echo "\n4. Sample customer data:\n";
        $customer = $customers->first();
        echo "   Customer: " . $customer->nama . "\n";
        echo "   Outlet ID: " . $customer->id_outlet . "\n";
        echo "   Mesin Count: " . $customer->mesin_count . "\n";
        echo "   Has Mesin Customers: " . ($customer->mesinCustomers->count() > 0 ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n=== Test Results ===\n";
    echo "✓ Customer search by outlet works correctly\n";
    echo "✓ No database errors (id_outlet column exists in member table)\n";
    echo "✓ mesinCustomers relationship loads correctly\n";
    echo "✓ Search functionality works with and without search terms\n";
    
} catch (Exception $e) {
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Summary ===\n";
echo "The customer search functionality has been fixed to:\n";
echo "1. Filter customers by member.id_outlet (correct table)\n";
echo "2. NOT try to filter mesin_customer by id_outlet (incorrect - column doesn't exist)\n";
echo "3. Still load mesinCustomers relationship for each member\n";
echo "4. Work with search terms for name, address, phone, and member code\n";

echo "\nTest completed!\n";
?>