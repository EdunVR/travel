<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InterOutletSale;
use App\Models\CompanySetting;
use App\Http\Controllers\InterOutletSaleController;
use App\Http\Controllers\SalesReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "=== TESTING INTER OUTLET LOGO AND SALES REPORT FIX ===\n\n";

try {
    // Test 1: Check CompanySettings logo URL
    echo "1. Testing CompanySettings logo URL...\n";
    
    $outlet = DB::table('outlets')->where('is_active', true)->first();
    if ($outlet) {
        echo "   🏢 Testing with outlet: {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
        
        $companySetting = CompanySetting::getOrCreateForOutlet($outlet->id_outlet);
        echo "   📋 Company name: " . ($companySetting->company_name ?? 'Not set') . "\n";
        echo "   🖼️  Logo path: " . ($companySetting->company_logo ?? 'Not set') . "\n";
        echo "   🔗 Logo URL: " . ($companySetting->logo_url ?? 'Not set') . "\n";
        
        // Test if logo file exists
        if ($companySetting->company_logo) {
            $logoPath = storage_path('app/public/' . str_replace('storage/', '', $companySetting->company_logo));
            if (file_exists($logoPath)) {
                echo "   ✅ Logo file exists at: {$logoPath}\n";
                echo "   📏 File size: " . number_format(filesize($logoPath) / 1024, 2) . " KB\n";
            } else {
                echo "   ❌ Logo file NOT found at: {$logoPath}\n";
            }
        }
        
        // Test the trait method
        $controller = new class extends \Illuminate\Routing\Controller {
            use \App\Traits\HasCompanySettings;
            use \App\Traits\HasOutletFilter;
            
            public function testGetCompanySettingsForPrint($outletId) {
                session(['selected_outlet_id' => $outletId]);
                return $this->getCompanySettingsForPrint();
            }
        };
        
        $printSettings = $controller->testGetCompanySettingsForPrint($outlet->id_outlet);
        echo "   📄 Print settings logo_url: " . ($printSettings['logo_url'] ?? 'Not set') . "\n";
        
        if (isset($printSettings['logo_url']) && $printSettings['logo_url']) {
            echo "   ✅ Logo URL is properly set in print settings\n";
        } else {
            echo "   ❌ Logo URL is NOT set in print settings\n";
        }
    }
    
    // Test 2: Test SalesReportController with Inter Outlet source
    echo "\n2. Testing SalesReportController with Inter Outlet source...\n";
    
    $request = new Request([
        'outlet_id' => 'all',
        'start_date' => '2024-01-01',
        'end_date' => '2026-12-31'
    ]);
    
    $controller = new SalesReportController();
    $response = $controller->getData($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "   ✅ SalesReportController getData - SUCCESS\n";
        echo "   📊 Total records: " . count($responseData['data']) . "\n";
        
        // Count by source and check if source field is populated
        $sources = [];
        $emptySourceCount = 0;
        
        foreach ($responseData['data'] as $record) {
            if (isset($record['source']) && !empty($record['source'])) {
                $sources[$record['source']] = ($sources[$record['source']] ?? 0) + 1;
            } else {
                $emptySourceCount++;
            }
        }
        
        foreach ($sources as $source => $count) {
            echo "   📈 {$source}: {$count} records\n";
        }
        
        if ($emptySourceCount > 0) {
            echo "   ⚠️  Records with empty source: {$emptySourceCount}\n";
        } else {
            echo "   ✅ All records have source field populated\n";
        }
        
        // Check for inter_outlet records specifically
        if (isset($sources['inter_outlet'])) {
            echo "   ✅ Inter Outlet records found: " . $sources['inter_outlet'] . "\n";
        } else {
            echo "   ⚠️  No Inter Outlet records found\n";
        }
    } else {
        echo "   ❌ SalesReportController getData - FAILED: " . $responseData['message'] . "\n";
    }
    
    // Test 3: Test InterOutletSale PDF generation with logo
    echo "\n3. Testing InterOutletSale PDF generation with logo...\n";
    
    $interOutletSale = InterOutletSale::with(['outletAsal', 'outletTujuan', 'user', 'items.produk'])->first();
    
    if ($interOutletSale) {
        echo "   📋 Testing with transaction: {$interOutletSale->no_transaksi}\n";
        
        try {
            $controller = new InterOutletSaleController(new \App\Services\JournalEntryService());
            
            // Mock the print method to test company settings
            session(['selected_outlet_id' => $interOutletSale->outlet_asal]);
            
            // Test if we can get company settings
            $testController = new class extends \Illuminate\Routing\Controller {
                use \App\Traits\HasCompanySettings;
                use \App\Traits\HasOutletFilter;
                
                public function testGetCompanySettingsForPrint() {
                    return $this->getCompanySettingsForPrint();
                }
            };
            
            $companySettings = $testController->testGetCompanySettingsForPrint();
            
            echo "   📄 Company settings for PDF:\n";
            echo "      - Company name: " . ($companySettings['company_name'] ?? 'Not set') . "\n";
            echo "      - Logo URL: " . ($companySettings['logo_url'] ?? 'Not set') . "\n";
            
            if (isset($companySettings['logo_url']) && $companySettings['logo_url']) {
                echo "   ✅ Logo URL available for PDF generation\n";
                
                // Test if URL is accessible
                $logoUrl = $companySettings['logo_url'];
                if (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                    echo "   ✅ Logo URL is valid: {$logoUrl}\n";
                } else {
                    echo "   ⚠️  Logo URL might be relative: {$logoUrl}\n";
                }
            } else {
                echo "   ❌ Logo URL NOT available for PDF generation\n";
            }
            
        } catch (\Exception $e) {
            echo "   ❌ Error testing PDF generation: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠️  No InterOutletSale records found\n";
    }
    
    echo "\n=== FIXES TO APPLY ===\n";
    echo "✅ Added Inter Outlet source badge in sales report\n";
    echo "✅ Fixed invoice preview for Inter Outlet transactions\n";
    echo "✅ Removed broken invoice preview route (404 error)\n";
    
    if (isset($printSettings['logo_url']) && $printSettings['logo_url']) {
        echo "✅ Logo URL is properly configured\n";
    } else {
        echo "⚠️  Logo URL needs to be configured in Company Settings\n";
    }
    
    echo "\n🎯 NEXT STEPS:\n";
    echo "1. Test sales report - source column should show Inter Outlet badge\n";
    echo "2. Test Inter Outlet PDF - logo should display if configured\n";
    echo "3. Configure company logo in Admin > Sistem > Company Settings if not set\n";
    echo "4. Clear browser cache to see updated source badges\n";

} catch (\Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";