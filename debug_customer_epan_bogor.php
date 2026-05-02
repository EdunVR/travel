<?php
/**
 * Debug Customer Epan(Bogor) - Cek Tipe Customer
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Debug Customer Epan(Bogor) - Tipe Customer Issue\n";
echo "==================================================\n\n";

// 1. Cari customer Epan(Bogor)
echo "📋 1. Mencari customer Epan(Bogor):\n";
$epanCustomers = DB::table('pelanggan')
    ->where('nama_pelanggan', 'like', '%epan%')
    ->orWhere('nama_pelanggan', 'like', '%bogor%')
    ->get();

if ($epanCustomers->isEmpty()) {
    echo "❌ Customer dengan nama mengandung 'epan' atau 'bogor' tidak ditemukan\n";
    
    // Coba cari dengan pattern yang lebih luas
    echo "\n📋 Mencari dengan pattern yang lebih luas:\n";
    $allCustomers = DB::table('pelanggan')
        ->select('id_pelanggan', 'nama_pelanggan', 'id_tipe', 'created_at', 'updated_at')
        ->orderBy('updated_at', 'desc')
        ->limit(20)
        ->get();
    
    echo "20 customer terbaru (berdasarkan update):\n";
    foreach ($allCustomers as $customer) {
        echo "- ID: {$customer->id_pelanggan}, Nama: {$customer->nama_pelanggan}, Tipe: {$customer->id_tipe}\n";
        echo "  Updated: {$customer->updated_at}\n\n";
    }
} else {
    echo "Ditemukan " . count($epanCustomers) . " customer:\n";
    foreach ($epanCustomers as $customer) {
        echo "✅ Customer ditemukan:\n";
        echo "   ID: {$customer->id_pelanggan}\n";
        echo "   Nama: {$customer->nama_pelanggan}\n";
        echo "   ID Tipe: {$customer->id_tipe}\n";
        echo "   Telepon: {$customer->telepon}\n";
        echo "   Alamat: {$customer->alamat}\n";
        echo "   Created: {$customer->created_at}\n";
        echo "   Updated: {$customer->updated_at}\n\n";
        
        // Cek nama tipe customer
        if ($customer->id_tipe) {
            $tipeCustomer = DB::table('tipe')
                ->where('id_tipe', $customer->id_tipe)
                ->first();
            
            if ($tipeCustomer) {
                echo "   📋 Tipe Customer: {$tipeCustomer->nama_tipe}\n";
                echo "   📋 Tipe Created: {$tipeCustomer->created_at}\n";
                echo "   📋 Tipe Updated: {$tipeCustomer->updated_at}\n\n";
            } else {
                echo "   ❌ Tipe customer dengan ID {$customer->id_tipe} tidak ditemukan\n\n";
            }
        } else {
            echo "   ⚠️ Customer tidak memiliki tipe (id_tipe = NULL)\n\n";
        }
    }
}

// 2. Cek API endpoint customers yang digunakan POS
echo "📋 2. Test API endpoint customers (simulasi PosController):\n";

try {
    // Simulasi query dari PosController->getCustomers()
    $customers = DB::table('pelanggan as p')
        ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
        ->select(
            'p.id_pelanggan as id',
            'p.nama_pelanggan as name',
            'p.telepon',
            'p.alamat',
            'p.id_tipe',
            't.nama_tipe'
        )
        ->orderBy('p.nama_pelanggan')
        ->get();

    echo "Total customers dari API: " . count($customers) . "\n\n";
    
    // Cari Epan(Bogor) dalam hasil API
    $epanFromApi = $customers->filter(function($customer) {
        return stripos($customer->name, 'epan') !== false || 
               stripos($customer->name, 'bogor') !== false;
    });
    
    if ($epanFromApi->isEmpty()) {
        echo "❌ Customer Epan(Bogor) tidak ditemukan dalam hasil API\n";
        
        // Tampilkan beberapa customer untuk referensi
        echo "\nBeberapa customer dari API (10 pertama):\n";
        foreach ($customers->take(10) as $customer) {
            echo "- {$customer->name} (ID: {$customer->id}, Tipe: {$customer->nama_tipe})\n";
        }
    } else {
        echo "✅ Customer Epan(Bogor) ditemukan dalam API:\n";
        foreach ($epanFromApi as $customer) {
            echo "   ID: {$customer->id}\n";
            echo "   Nama: {$customer->name}\n";
            echo "   Telepon: {$customer->telepon}\n";
            echo "   ID Tipe: {$customer->id_tipe}\n";
            echo "   Nama Tipe: {$customer->nama_tipe}\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat test API: " . $e->getMessage() . "\n";
}

// 3. Cek history perubahan tipe customer (jika ada log)
echo "📋 3. Cek riwayat perubahan tipe customer:\n";

// Cek apakah ada tabel audit atau log
$tables = DB::select("SHOW TABLES LIKE '%audit%' OR SHOW TABLES LIKE '%log%'");
if (!empty($tables)) {
    echo "Ditemukan tabel audit/log:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- {$tableName}\n";
    }
} else {
    echo "Tidak ada tabel audit/log ditemukan\n";
}

// Cek updated_at untuk melihat kapan terakhir customer diupdate
if (!empty($epanCustomers)) {
    $customer = $epanCustomers->first();
    echo "\nCustomer terakhir diupdate: {$customer->updated_at}\n";
    
    // Cek apakah ada perubahan dalam 24 jam terakhir
    $lastUpdate = new DateTime($customer->updated_at);
    $now = new DateTime();
    $diff = $now->diff($lastUpdate);
    
    echo "Selisih waktu dari sekarang: {$diff->days} hari, {$diff->h} jam, {$diff->i} menit\n";
}

// 4. Cek cache atau session yang mungkin menyimpan data lama
echo "\n📋 4. Kemungkinan penyebab data lama:\n";
echo "✓ Browser cache - User perlu clear cache browser\n";
echo "✓ Laravel cache - Perlu clear cache aplikasi\n";
echo "✓ Session data - Mungkin ada session yang menyimpan data customer lama\n";
echo "✓ JavaScript cache - File pos.js mungkin di-cache browser\n";

// 5. Saran perbaikan
echo "\n🔧 Saran perbaikan:\n";
echo "1. Clear browser cache (Ctrl+F5 atau Ctrl+Shift+R)\n";
echo "2. Clear Laravel cache: php artisan cache:clear\n";
echo "3. Clear config cache: php artisan config:clear\n";
echo "4. Clear view cache: php artisan view:clear\n";
echo "5. Restart browser atau gunakan incognito mode\n";

// 6. Generate script untuk clear cache
echo "\n📋 6. Script untuk clear cache:\n";
echo "Jalankan command berikut:\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
echo "php artisan route:clear\n";

echo "\n";
?>