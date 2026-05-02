<?php
/**
 * Clear Customer Cache dan Test Data Epan(Bogor)
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "🧹 Clear Customer Cache dan Test Data Epan(Bogor)\n";
echo "================================================\n\n";

// 1. Clear customer cache
echo "📋 1. Clearing customer cache...\n";
try {
    $cleared = CacheService::clearCustomerCache();
    if ($cleared) {
        echo "✅ Customer cache berhasil di-clear\n";
    } else {
        echo "⚠️ Customer cache mungkin sudah kosong atau gagal di-clear\n";
    }
    
    // Clear manual juga
    $manualKey = CacheService::key('customers', 'all');
    Cache::forget($manualKey);
    echo "✅ Manual cache key juga di-clear: {$manualKey}\n";
    
} catch (\Exception $e) {
    echo "❌ Error clearing cache: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Cek data customer Epan(Bogor) langsung dari database
echo "📋 2. Data customer Epan(Bogor) dari database:\n";
$customers = DB::table('pelanggan as p')
    ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
    ->where(function($query) {
        $query->where('p.nama_pelanggan', 'like', '%epan%')
              ->orWhere('p.nama_pelanggan', 'like', '%bogor%');
    })
    ->select('p.*', 't.nama_tipe')
    ->get();

if ($customers->isEmpty()) {
    echo "❌ Customer tidak ditemukan dengan nama mengandung 'epan' atau 'bogor'\n";
    
    // Cari dengan pattern yang lebih luas
    echo "\n📋 Mencari customer dengan pattern lain:\n";
    $allCustomers = DB::table('pelanggan as p')
        ->leftJoin('tipe as t', 'p.id_tipe', '=', 't.id_tipe')
        ->select('p.id_pelanggan', 'p.nama_pelanggan', 'p.id_tipe', 't.nama_tipe', 'p.updated_at')
        ->orderBy('p.updated_at', 'desc')
        ->limit(15)
        ->get();
    
    echo "15 customer terbaru (berdasarkan update):\n";
    foreach ($allCustomers as $customer) {
        echo "- ID: {$customer->id_pelanggan}, Nama: {$customer->nama_pelanggan}\n";
        echo "  Tipe: " . ($customer->nama_tipe ?: 'Tidak ada') . " (ID: {$customer->id_tipe})\n";
        echo "  Updated: {$customer->updated_at}\n\n";
    }
} else {
    echo "✅ Customer ditemukan:\n";
    foreach ($customers as $customer) {
        echo "   ID: {$customer->id_pelanggan}\n";
        echo "   Nama: {$customer->nama_pelanggan}\n";
        echo "   ID Tipe: {$customer->id_tipe}\n";
        echo "   Nama Tipe: " . ($customer->nama_tipe ?: 'Tidak ada') . "\n";
        echo "   Telepon: {$customer->telepon}\n";
        echo "   Updated: {$customer->updated_at}\n\n";
    }
}

// 3. Test API endpoint setelah cache di-clear
echo "📋 3. Test API endpoint setelah cache di-clear:\n";

try {
    // Simulasi PosController->getCustomers() tanpa cache
    $apiCustomers = \App\Models\Member::select('id_member', 'nama', 'telepon', 'id_tipe')
        ->with('tipe:id_tipe,nama_tipe')
        ->where(function($query) {
            $query->where('nama', 'like', '%epan%')
                  ->orWhere('nama', 'like', '%bogor%');
        })
        ->orderBy('nama')
        ->get()
        ->map(function($customer) {
            return [
                'id' => $customer->id_member,
                'name' => $customer->nama,
                'telepon' => $customer->telepon,
                'id_tipe' => $customer->id_tipe,
                'tipe_name' => $customer->tipe ? $customer->tipe->nama_tipe : null
            ];
        });

    if ($apiCustomers->isEmpty()) {
        echo "❌ Customer tidak ditemukan melalui API\n";
        
        // Cek apakah menggunakan tabel yang berbeda
        echo "\n📋 Cek tabel member vs pelanggan:\n";
        $memberCount = DB::table('member')->count();
        $pelangganCount = DB::table('pelanggan')->count();
        
        echo "Total records di tabel 'member': {$memberCount}\n";
        echo "Total records di tabel 'pelanggan': {$pelangganCount}\n";
        
        if ($memberCount > 0) {
            echo "\n📋 Sample data dari tabel member:\n";
            $sampleMembers = DB::table('member as m')
                ->leftJoin('tipe as t', 'm.id_tipe', '=', 't.id_tipe')
                ->select('m.id_member', 'm.nama', 'm.id_tipe', 't.nama_tipe', 'm.updated_at')
                ->orderBy('m.updated_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($sampleMembers as $member) {
                echo "- ID: {$member->id_member}, Nama: {$member->nama}\n";
                echo "  Tipe: " . ($member->nama_tipe ?: 'Tidak ada') . " (ID: {$member->id_tipe})\n";
                echo "  Updated: {$member->updated_at}\n\n";
            }
        }
        
    } else {
        echo "✅ Customer ditemukan melalui API:\n";
        foreach ($apiCustomers as $customer) {
            echo "   ID: {$customer['id']}\n";
            echo "   Nama: {$customer['name']}\n";
            echo "   ID Tipe: {$customer['id_tipe']}\n";
            echo "   Nama Tipe: " . ($customer['tipe_name'] ?: 'Tidak ada') . "\n";
            echo "   Telepon: {$customer['telepon']}\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat test API: " . $e->getMessage() . "\n";
}

// 4. Cek apakah ada Model Observer atau Event Listener untuk clear cache
echo "📋 4. Cek Model Observer untuk auto-clear cache:\n";

$memberModelFile = 'app/Models/Member.php';
if (file_exists($memberModelFile)) {
    $content = file_get_contents($memberModelFile);
    
    if (strpos($content, 'updated') !== false || strpos($content, 'saved') !== false) {
        echo "✅ Model Member memiliki event listener\n";
    } else {
        echo "❌ Model Member tidak memiliki event listener untuk clear cache\n";
        echo "💡 Perlu ditambahkan event listener untuk auto-clear cache\n";
    }
} else {
    echo "❌ File Member.php tidak ditemukan\n";
}

// 5. Saran perbaikan
echo "\n🔧 Saran perbaikan:\n";
echo "1. ✅ Cache customer sudah di-clear\n";
echo "2. 🔄 Refresh halaman POS (F5 atau Ctrl+R)\n";
echo "3. 🧹 Clear browser cache juga (Ctrl+Shift+R)\n";
echo "4. 📝 Tambahkan auto-clear cache di Model Member\n";
echo "5. ⏰ Kurangi durasi cache customer dari 10 menit ke 2-3 menit\n";

// 6. Generate command untuk clear semua cache
echo "\n📋 6. Command untuk clear semua cache Laravel:\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
echo "php artisan route:clear\n";

echo "\n✅ Selesai! Silakan refresh halaman POS dan coba lagi.\n";
?>