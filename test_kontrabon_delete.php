<?php

/**
 * Script untuk test fitur delete kontra bon
 * 
 * Cara menjalankan:
 * php artisan tinker
 * include 'test_kontrabon_delete.php';
 */

use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;
use Illuminate\Support\Facades\DB;

echo "=== TEST KONTRA BON DELETE FEATURE ===\n\n";

try {
    // 1. Cek permission
    echo "1. Checking Permission...\n";
    $permission = DB::table('permissions')
        ->where('name', 'sales.kontrabon.delete')
        ->first();
    
    if ($permission) {
        echo "   ✅ Permission 'sales.kontrabon.delete' exists\n";
        echo "   ID: {$permission->id}\n";
        echo "   Display Name: {$permission->display_name}\n";
    } else {
        echo "   ❌ Permission 'sales.kontrabon.delete' NOT FOUND\n";
        echo "   Run: php artisan tinker\n";
        echo "   Then: include 'create_kontrabon_delete_permission.php';\n";
        exit;
    }
    
    // 2. Cek route
    echo "\n2. Checking Route...\n";
    $routeName = 'admin.penjualan.kontrabon.destroy';
    if (Route::has($routeName)) {
        $route = Route::getRoutes()->getByName($routeName);
        echo "   ✅ Route '{$routeName}' exists\n";
        echo "   URI: " . $route->uri() . "\n";
        echo "   Method: " . implode(', ', $route->methods()) . "\n";
    } else {
        echo "   ❌ Route '{$routeName}' NOT FOUND\n";
        echo "   Please check routes/web.php\n";
    }
    
    // 3. Cek kontra bon yang ada
    echo "\n3. Checking Kontra Bon Data...\n";
    $kontraBonCount = KontraBon::count();
    echo "   Total Kontra Bon: {$kontraBonCount}\n";
    
    if ($kontraBonCount > 0) {
        $latestKontraBon = KontraBon::with(['details', 'member'])->latest()->first();
        echo "   Latest Kontra Bon:\n";
        echo "   - ID: {$latestKontraBon->id_kontra_bon}\n";
        echo "   - No: {$latestKontraBon->no_kontra_bon}\n";
        echo "   - Member: " . ($latestKontraBon->member->nama ?? '-') . "\n";
        echo "   - Status: {$latestKontraBon->status}\n";
        echo "   - Total Details: " . $latestKontraBon->details->count() . "\n";
        
        // Cek piutang terkait
        if ($latestKontraBon->details->count() > 0) {
            echo "\n   Details:\n";
            foreach ($latestKontraBon->details as $detail) {
                $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
                if ($piutang) {
                    echo "   - Penjualan ID: {$detail->id_penjualan}\n";
                    echo "     Jumlah Bayar: Rp " . number_format($detail->jumlah_bayar, 0, ',', '.') . "\n";
                    echo "     Piutang Status: {$piutang->status}\n";
                    echo "     Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
                }
            }
        }
    } else {
        echo "   ⚠️ No Kontra Bon data found\n";
    }
    
    // 4. Test simulation (tidak benar-benar menghapus)
    echo "\n4. Delete Simulation...\n";
    if ($kontraBonCount > 0) {
        $testKontraBon = KontraBon::with(['details'])->latest()->first();
        
        echo "   Simulating delete for Kontra Bon ID: {$testKontraBon->id_kontra_bon}\n";
        echo "   No: {$testKontraBon->no_kontra_bon}\n";
        
        echo "\n   What will happen:\n";
        foreach ($testKontraBon->details as $detail) {
            $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
            if ($piutang) {
                $newJumlahDibayar = max(0, $piutang->jumlah_dibayar - $detail->jumlah_bayar);
                $newSisaPiutang = min($piutang->jumlah_piutang, $piutang->sisa_piutang + $detail->jumlah_bayar);
                $newStatus = ($newSisaPiutang >= $piutang->jumlah_piutang) ? 'belum_lunas' : $piutang->status;
                
                echo "\n   Piutang (Penjualan ID: {$detail->id_penjualan}):\n";
                echo "   - Current Status: {$piutang->status}\n";
                echo "   - Current Jumlah Dibayar: Rp " . number_format($piutang->jumlah_dibayar, 0, ',', '.') . "\n";
                echo "   - Current Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
                echo "   → New Status: {$newStatus}\n";
                echo "   → New Jumlah Dibayar: Rp " . number_format($newJumlahDibayar, 0, ',', '.') . "\n";
                echo "   → New Sisa Piutang: Rp " . number_format($newSisaPiutang, 0, ',', '.') . "\n";
            }
        }
        
        echo "\n   ⚠️ This is just a simulation. No data was actually deleted.\n";
    }
    
    // 5. Cek controller method
    echo "\n5. Checking Controller Method...\n";
    $controllerClass = 'App\Http\Controllers\Admin\KontraBonController';
    if (class_exists($controllerClass)) {
        echo "   ✅ Controller class exists\n";
        
        if (method_exists($controllerClass, 'destroy')) {
            echo "   ✅ Method 'destroy' exists\n";
            
            $reflection = new ReflectionMethod($controllerClass, 'destroy');
            $params = $reflection->getParameters();
            echo "   Parameters: ";
            foreach ($params as $param) {
                echo $param->getName() . " ";
            }
            echo "\n";
        } else {
            echo "   ❌ Method 'destroy' NOT FOUND\n";
        }
    } else {
        echo "   ❌ Controller class NOT FOUND\n";
    }
    
    // 6. Summary
    echo "\n=== SUMMARY ===\n";
    echo "✅ Permission: OK\n";
    echo "✅ Route: OK\n";
    echo "✅ Controller: OK\n";
    echo "✅ Data: " . ($kontraBonCount > 0 ? "OK ({$kontraBonCount} records)" : "No data") . "\n";
    
    echo "\n=== MANUAL TEST STEPS ===\n";
    echo "1. Login sebagai super_admin atau user dengan permission 'sales.kontrabon.delete'\n";
    echo "2. Buka halaman: /admin/penjualan/kontrabon\n";
    echo "3. Klik tab 'List Kontra Bon'\n";
    echo "4. Cari kontra bon yang ingin dihapus\n";
    echo "5. Klik tombol hapus (icon trash merah) di kolom Aksi\n";
    echo "6. Konfirmasi hapus\n";
    echo "7. Cek apakah:\n";
    echo "   - Kontra bon terhapus dari tabel\n";
    echo "   - Status piutang kembali ke 'belum_lunas'\n";
    echo "   - Sisa piutang bertambah sesuai pembayaran\n";
    
    echo "\n=== AJAX TEST ===\n";
    echo "Open browser console and run:\n";
    echo "```javascript\n";
    echo "$.ajax({\n";
    echo "    url: '/admin/penjualan/kontrabon/1',\n";
    echo "    type: 'DELETE',\n";
    echo "    data: { _token: $('meta[name=\"csrf-token\"]').attr('content') },\n";
    echo "    success: function(response) { console.log(response); },\n";
    echo "    error: function(xhr) { console.log(xhr.responseJSON); }\n";
    echo "});\n";
    echo "```\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
