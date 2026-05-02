<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InterOutletSale;
use App\Models\SettingCOAInterOutletSale;
use App\Models\ChartOfAccount;
use App\Models\AccountingBook;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Inter Outlet Journal Creation ===\n\n";

try {
    // Test 1: Check if COA settings exist
    echo "1. Checking COA Settings...\n";
    $outlets = DB::table('outlets')->where('is_active', true)->get();
    
    foreach ($outlets as $outlet) {
        $setting = SettingCOAInterOutletSale::getByOutlet($outlet->id_outlet);
        
        if ($setting) {
            echo "   ✓ Outlet {$outlet->nama_outlet} (ID: {$outlet->id_outlet}) has COA settings\n";
            echo "     - Accounting Book ID: {$setting->accounting_book_id}\n";
            echo "     - Piutang Account: {$setting->akun_piutang_antar_outlet}\n";
            echo "     - Pendapatan Account: {$setting->akun_pendapatan_antar_outlet}\n";
            echo "     - HPP Account: " . ($setting->akun_hpp ?: 'Not set') . "\n";
            echo "     - Persediaan Account: " . ($setting->akun_persediaan ?: 'Not set') . "\n";
            echo "     - PPN Account: " . ($setting->akun_ppn ?: 'Not set') . "\n";
            
            // Validate accounts exist
            $accountsToCheck = [
                'Piutang' => $setting->akun_piutang_antar_outlet,
                'Pendapatan' => $setting->akun_pendapatan_antar_outlet,
            ];
            
            if ($setting->akun_hpp) $accountsToCheck['HPP'] = $setting->akun_hpp;
            if ($setting->akun_persediaan) $accountsToCheck['Persediaan'] = $setting->akun_persediaan;
            if ($setting->akun_ppn) $accountsToCheck['PPN'] = $setting->akun_ppn;
            
            foreach ($accountsToCheck as $type => $accountIdOrCode) {
                // Check if it's numeric (ID) or string (code)
                if (is_numeric($accountIdOrCode)) {
                    $account = ChartOfAccount::where('id', $accountIdOrCode)
                        ->where('outlet_id', $outlet->id_outlet)
                        ->first();
                } else {
                    $account = ChartOfAccount::where('code', $accountIdOrCode)
                        ->where('outlet_id', $outlet->id_outlet)
                        ->first();
                }
                
                if ($account) {
                    echo "     ✓ {$type} Account found: {$account->code} - {$account->name}\n";
                } else {
                    echo "     ✗ {$type} Account NOT FOUND: {$accountIdOrCode}\n";
                }
            }
            
            // Check accounting book
            $book = AccountingBook::find($setting->accounting_book_id);
            if ($book) {
                echo "     ✓ Accounting Book found: {$book->name}\n";
            } else {
                echo "     ✗ Accounting Book NOT FOUND: {$setting->accounting_book_id}\n";
            }
            
        } else {
            echo "   ✗ Outlet {$outlet->nama_outlet} (ID: {$outlet->id_outlet}) has NO COA settings\n";
        }
        echo "\n";
    }
    
    // Test 2: Check recent inter outlet sales
    echo "2. Checking Recent Inter Outlet Sales...\n";
    $recentSales = InterOutletSale::with(['outletAsal', 'outletTujuan'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    if ($recentSales->count() > 0) {
        foreach ($recentSales as $sale) {
            echo "   Transaction: {$sale->no_transaksi}\n";
            echo "   - Date: {$sale->tanggal}\n";
            echo "   - From: {$sale->outletAsal->nama_outlet} (ID: {$sale->outlet_asal})\n";
            echo "   - To: {$sale->outletTujuan->nama_outlet} (ID: {$sale->outlet_tujuan})\n";
            echo "   - Total: Rp " . number_format($sale->total, 0, ',', '.') . "\n";
            echo "   - Status: {$sale->status}\n";
            
            // Check if journal exists
            $journal = DB::table('journal_entries')
                ->where('reference_type', 'inter_outlet_sale')
                ->where('reference_number', 'LIKE', '%' . $sale->id)
                ->first();
            
            if ($journal) {
                echo "   ✓ Journal Entry exists: {$journal->transaction_number}\n";
            } else {
                echo "   ✗ No Journal Entry found\n";
            }
            echo "\n";
        }
    } else {
        echo "   No inter outlet sales found\n\n";
    }
    
    // Test 3: Test account lookup function
    echo "3. Testing Account Lookup Function...\n";
    
    // Get a sample outlet with COA settings
    $sampleOutlet = null;
    foreach ($outlets as $outlet) {
        $setting = SettingCOAInterOutletSale::getByOutlet($outlet->id_outlet);
        if ($setting && $setting->akun_piutang_antar_outlet) {
            $sampleOutlet = $outlet;
            $sampleSetting = $setting;
            break;
        }
    }
    
    if ($sampleOutlet) {
        echo "   Testing with outlet: {$sampleOutlet->nama_outlet}\n";
        
        // Test the getAccountIdByCode function logic
        $accountIdOrCode = $sampleSetting->akun_piutang_antar_outlet;
        echo "   Testing account lookup for: {$accountIdOrCode}\n";
        
        if (is_numeric($accountIdOrCode)) {
            echo "   - Detected as ID (numeric)\n";
            $account = ChartOfAccount::where('id', $accountIdOrCode)
                ->where('outlet_id', $sampleOutlet->id_outlet)
                ->first();
        } else {
            echo "   - Detected as Code (string)\n";
            $account = ChartOfAccount::where('code', $accountIdOrCode)
                ->where('outlet_id', $sampleOutlet->id_outlet)
                ->first();
        }
        
        if ($account) {
            echo "   ✓ Account found: ID={$account->id}, Code={$account->code}, Name={$account->name}\n";
        } else {
            echo "   ✗ Account NOT FOUND\n";
        }
    } else {
        echo "   No outlet with COA settings found for testing\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}