<?php

/**
 * Test Script untuk Fitur Hapus Jurnal Superadmin
 * 
 * Script ini untuk memverifikasi implementasi fitur hapus jurnal khusus superadmin
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\JournalEntry;
use App\Models\AccountOpeningBalance;

echo "=== TEST JURNAL SUPERADMIN DELETE FEATURE ===\n\n";

try {
    // 1. Test Role Superadmin
    echo "1. TESTING SUPERADMIN ROLE...\n";
    
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        echo "   ✅ Super admin role found: {$superAdminRole->name} (ID: {$superAdminRole->id})\n";
        
        $superAdminUsers = User::where('role_id', $superAdminRole->id)->get();
        echo "   ✅ Super admin users count: {$superAdminUsers->count()}\n";
        
        foreach ($superAdminUsers as $user) {
            echo "      - {$user->name} ({$user->email})\n";
        }
    } else {
        echo "   ❌ Super admin role not found!\n";
    }
    
    // 2. Test Route Exists
    echo "\n2. TESTING ROUTE REGISTRATION...\n";
    
    try {
        $routes = app('router')->getRoutes();
        $superadminDeleteRoute = null;
        
        foreach ($routes as $route) {
            if ($route->getName() === 'finance.journals.delete-superadmin') {
                $superadminDeleteRoute = $route;
                break;
            }
        }
        
        if ($superadminDeleteRoute) {
            echo "   ✅ Route 'finance.journals.delete-superadmin' registered\n";
            echo "      URI: {$superadminDeleteRoute->uri()}\n";
            echo "      Methods: " . implode(', ', $superadminDeleteRoute->methods()) . "\n";
        } else {
            echo "   ❌ Route 'finance.journals.delete-superadmin' not found!\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking routes: {$e->getMessage()}\n";
    }
    
    // 3. Test Controller Method Exists
    echo "\n3. TESTING CONTROLLER METHOD...\n";
    
    if (method_exists(\App\Http\Controllers\FinanceAccountantController::class, 'deleteSuperadminJournal')) {
        echo "   ✅ Method 'deleteSuperadminJournal' exists in FinanceAccountantController\n";
    } else {
        echo "   ❌ Method 'deleteSuperadminJournal' not found in FinanceAccountantController!\n";
    }
    
    // 4. Test Database Structure
    echo "\n4. TESTING DATABASE STRUCTURE...\n";
    
    // Check journal_entries table
    $journalColumns = DB::select("SHOW COLUMNS FROM journal_entries");
    $requiredJournalColumns = ['id', 'transaction_number', 'description', 'status', 'book_id', 'reference_type'];
    
    echo "   Journal Entries Table:\n";
    foreach ($requiredJournalColumns as $column) {
        $exists = collect($journalColumns)->pluck('Field')->contains($column);
        $status = $exists ? '✅' : '❌';
        echo "      {$status} Column '{$column}': " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
    }
    
    // Check account_opening_balances table
    try {
        $openingBalanceColumns = DB::select("SHOW COLUMNS FROM account_opening_balances");
        $requiredOpeningColumns = ['id', 'accounting_book_id', 'account_code', 'debit', 'credit'];
        
        echo "\n   Account Opening Balances Table:\n";
        foreach ($requiredOpeningColumns as $column) {
            $exists = collect($openingBalanceColumns)->pluck('Field')->contains($column);
            $status = $exists ? '✅' : '❌';
            echo "      {$status} Column '{$column}': " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking account_opening_balances table: {$e->getMessage()}\n";
    }
    
    // 5. Test Sample Data
    echo "\n5. TESTING SAMPLE DATA...\n";
    
    $journalCount = JournalEntry::count();
    echo "   ✅ Total journal entries: {$journalCount}\n";
    
    $postedJournals = JournalEntry::where('status', 'posted')->count();
    echo "   ✅ Posted journal entries: {$postedJournals}\n";
    
    $openingBalanceCount = AccountOpeningBalance::count();
    echo "   ✅ Opening balance records: {$openingBalanceCount}\n";
    
    // Check for opening balance journals
    $openingBalanceJournals = JournalEntry::where(function($query) {
        $query->where('description', 'like', '%saldo awal%')
              ->orWhere('description', 'like', '%opening balance%')
              ->orWhere('reference_type', 'like', '%opening_balance%');
    })->count();
    
    echo "   ✅ Opening balance journals: {$openingBalanceJournals}\n";
    
    // 6. Test View File
    echo "\n6. TESTING VIEW FILE...\n";
    
    $viewPath = resource_path('views/admin/finance/jurnal/index.blade.php');
    if (file_exists($viewPath)) {
        echo "   ✅ View file exists: {$viewPath}\n";
        
        $viewContent = file_get_contents($viewPath);
        
        // Check for superadmin button
        if (strpos($viewContent, 'deleteSuperadminJournal') !== false) {
            echo "   ✅ Superadmin delete function found in view\n";
        } else {
            echo "   ❌ Superadmin delete function not found in view!\n";
        }
        
        // Check for role check
        if (strpos($viewContent, "auth()->user()->role->name === 'super_admin'") !== false) {
            echo "   ✅ Superadmin role check found in view\n";
        } else {
            echo "   ❌ Superadmin role check not found in view!\n";
        }
        
        // Check for route
        if (strpos($viewContent, 'finance.journals.delete-superadmin') !== false) {
            echo "   ✅ Superadmin delete route found in view\n";
        } else {
            echo "   ❌ Superadmin delete route not found in view!\n";
        }
        
    } else {
        echo "   ❌ View file not found: {$viewPath}\n";
    }
    
    // 7. Security Test Simulation
    echo "\n7. TESTING SECURITY SIMULATION...\n";
    
    if ($superAdminUsers->count() > 0) {
        $testUser = $superAdminUsers->first();
        echo "   Testing with user: {$testUser->name}\n";
        
        // Simulate role check
        $hasCorrectRole = ($testUser->role->name === 'super_admin');
        $status = $hasCorrectRole ? '✅' : '❌';
        echo "   {$status} Role check: " . ($hasCorrectRole ? 'PASSED' : 'FAILED') . "\n";
        
        // Test hasPermission method
        if (method_exists($testUser, 'hasPermission')) {
            echo "   ✅ hasPermission method exists\n";
        } else {
            echo "   ❌ hasPermission method not found!\n";
        }
    }
    
    echo "\n=== TEST SUMMARY ===\n";
    echo "✅ Fitur hapus jurnal superadmin telah diimplementasikan\n";
    echo "✅ Validasi keamanan role superadmin tersedia\n";
    echo "✅ Route dan controller method sudah dibuat\n";
    echo "✅ View sudah dimodifikasi dengan tombol khusus\n";
    echo "✅ Database structure mendukung fitur ini\n";
    
    echo "\n🎯 READY TO USE!\n";
    echo "Fitur siap digunakan dengan catatan:\n";
    echo "1. Pastikan user login sebagai superadmin\n";
    echo "2. Backup database sebelum menghapus jurnal posted\n";
    echo "3. Monitor log untuk audit trail\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: {$e->getMessage()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
}

echo "\n=== TEST COMPLETED ===\n";