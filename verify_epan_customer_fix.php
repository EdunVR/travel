<?php
/**
 * Verify Epan Customer Fix
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Verifikasi Customer Epan(Bogor) Fix\n";
echo "======================================\n\n";

// 1. Cek data customer langsung dari database
echo "📋 1. Data customer Epan(Bogor) dari database:\n";
$customer = \App\Models\Member::with('tipe')
    ->where('nama', 'like', '%epan%')
    ->orWhere('nama', 'like', '%bogor%')
    ->first();

if ($customer) {
    echo "✅ Customer ditemukan:\n";
    echo "   ID: {$customer->id_member}\n";
    echo "   Nama: {$customer->nama}\n";
    echo "   ID Tipe: {$customer->id_tipe}\n";
    echo "   Nama Tipe: " . ($customer->tipe ? $customer->tipe->nama_tipe : 'Tidak ada') . "\n";
    echo "   Telepon: {$customer->telepon}\n";
    echo "   Updated: {$customer->updated_at}\n\n";
} else {
    echo "❌ Customer tidak ditemukan\n\n";
}

// 2. Test API endpoint POS (simulasi)
echo "📋 2. Test API endpoint POS:\n";
try {
    $apiResult = \App\Models\Member::select('id_member', 'nama', 'telepon', 'id_tipe')
        ->with('tipe:id_tipe,nama_tipe')
        ->where('nama', 'like', '%epan%')
        ->orWhere('nama', 'like', '%bogor%')
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

    if ($apiResult->isNotEmpty()) {
        echo "✅ API result:\n";
        foreach ($apiResult as $customer) {
            echo "   " . json_encode($customer, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ Tidak ada hasil dari API\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// 3. Cek apakah tipe customer memiliki pricing
if (isset($customer) && $customer && $customer->id_tipe) {
    echo "\n📋 3. Cek pricing untuk tipe customer '{$customer->tipe->nama_tipe}':\n";
    
    $produkTipe = \App\Models\ProdukTipe::where('id_tipe', $customer->id_tipe)
        ->with('produk:id_produk,nama_produk,harga_jual')
        ->get();
    
    if ($produkTipe->isNotEmpty()) {
        echo "✅ Ditemukan " . count($produkTipe) . " produk dengan pricing khusus:\n";
        foreach ($produkTipe->take(5) as $pt) {
            $hargaFinal = $pt->harga_jual ?: ($pt->produk->harga_jual * (1 - ($pt->diskon ?? 0) / 100));
            echo "   - {$pt->produk->nama_produk}: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n";
        }
        if (count($produkTipe) > 5) {
            echo "   ... dan " . (count($produkTipe) - 5) . " produk lainnya\n";
        }
    } else {
        echo "⚠️ Tidak ada produk dengan pricing khusus untuk tipe ini\n";
    }
}

// 4. Instruksi untuk user
echo "\n📋 4. Instruksi untuk testing:\n";
echo "1. Buka halaman POS\n";
echo "2. Refresh halaman (F5 atau Ctrl+R)\n";
echo "3. Cari customer 'Epan' di pencarian customer\n";
echo "4. Pilih customer Epan(Bogor)\n";
echo "5. Cek apakah tipe customer sudah benar: " . ($customer && $customer->tipe ? $customer->tipe->nama_tipe : 'N/A') . "\n";
echo "6. Jika masih menunjukkan tipe lama, clear browser cache (Ctrl+Shift+R)\n";

echo "\n✅ Verifikasi selesai!\n";
?>