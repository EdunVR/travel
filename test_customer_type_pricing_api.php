<?php
/**
 * Test Customer Type Pricing API
 * Simulasi request ke endpoint customer-type-prices
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProdukTipe;

echo "🧪 Test Customer Type Pricing API\n";
echo "=================================\n\n";

// Cari tipe customer yang memiliki produk tofu spesial
echo "📋 1. Mencari tipe customer dengan produk tofu spesial...\n";
$tipeWithTofu = DB::table('produk_tipe as pt')
    ->join('tipe as t', 'pt.id_tipe', '=', 't.id_tipe')
    ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
    ->where('p.nama_produk', 'like', '%tofu spesial%')
    ->select('pt.id_tipe', 't.nama_tipe')
    ->distinct()
    ->get();

foreach ($tipeWithTofu as $tipe) {
    echo "   - ID: {$tipe->id_tipe}, Nama: {$tipe->nama_tipe}\n";
}

// Test dengan tipe pertama
if ($tipeWithTofu->count() > 0) {
    $testTipe = $tipeWithTofu->first();
    echo "\n📋 2. Test dengan tipe: {$testTipe->nama_tipe} (ID: {$testTipe->id_tipe})\n";
    
    // Simulasi method getCustomerTypePrices
    try {
        $idTipe = $testTipe->id_tipe;
        
        echo "   Query: ProdukTipe::where('id_tipe', {$idTipe})->with('produk')...\n";
        
        $produkTipe = ProdukTipe::where('id_tipe', $idTipe)
            ->with('produk:id_produk,kode_produk,harga_jual,nama_produk')
            ->get()
            ->map(function($pt) {
                $hargaFinal = $pt->harga_jual; // Harga jual khusus
                
                // Jika tidak ada harga jual khusus, hitung dari diskon
                if (!$hargaFinal || $hargaFinal == 0) {
                    $hargaNormal = $pt->produk->harga_jual;
                    $diskon = $pt->diskon ?? 0;
                    $hargaFinal = $hargaNormal * (1 - $diskon / 100);
                }
                
                return [
                    'id_produk' => $pt->id_produk,
                    'sku' => $pt->produk->kode_produk,
                    'nama_produk' => $pt->produk->nama_produk,
                    'harga_normal' => $pt->produk->harga_jual,
                    'diskon' => $pt->diskon ?? 0,
                    'harga_khusus' => $pt->harga_jual,
                    'harga_final' => $hargaFinal
                ];
            })
            ->keyBy('id_produk')
            ->toArray();

        echo "\n   Hasil API (total: " . count($produkTipe) . " produk):\n";
        
        $tofuCount = 0;
        foreach ($produkTipe as $idProduk => $data) {
            if (stripos($data['nama_produk'], 'tofu spesial') !== false) {
                $tofuCount++;
                echo "   ✅ {$data['nama_produk']} (ID: {$idProduk})\n";
                echo "      SKU: {$data['sku']}\n";
                echo "      Harga Normal: Rp " . number_format($data['harga_normal'], 0, ',', '.') . "\n";
                echo "      Harga Khusus: Rp " . number_format($data['harga_khusus'], 0, ',', '.') . "\n";
                echo "      Diskon: {$data['diskon']}%\n";
                echo "      Harga Final: Rp " . number_format($data['harga_final'], 0, ',', '.') . "\n\n";
            }
        }
        
        echo "   📊 Total produk tofu spesial dalam response: {$tofuCount}\n";
        
        // Format JSON seperti yang dikirim ke frontend
        echo "\n📋 3. Format JSON untuk frontend:\n";
        echo json_encode($produkTipe, JSON_PRETTY_PRINT) . "\n\n";
        
        // Cek apakah ada produk tofu spesial yang tidak muncul
        echo "📋 4. Verifikasi kelengkapan produk tofu spesial:\n";
        $allTofuProducts = DB::table('produk')
            ->where('nama_produk', 'like', '%tofu spesial%')
            ->select('id_produk', 'nama_produk', 'kode_produk')
            ->get();
        
        echo "   Total produk tofu spesial di database: " . $allTofuProducts->count() . "\n";
        echo "   Yang muncul di API response: {$tofuCount}\n\n";
        
        foreach ($allTofuProducts as $product) {
            if (isset($produkTipe[$product->id_produk])) {
                echo "   ✅ {$product->nama_produk} - Ada di response\n";
            } else {
                echo "   ❌ {$product->nama_produk} - TIDAK ada di response\n";
                
                // Cek apakah ada pricing untuk tipe ini
                $hasPricing = DB::table('produk_tipe')
                    ->where('id_produk', $product->id_produk)
                    ->where('id_tipe', $idTipe)
                    ->first();
                
                if (!$hasPricing) {
                    echo "      Alasan: Tidak ada pricing untuk tipe customer ini\n";
                } else {
                    echo "      Alasan: Ada pricing tapi tidak muncul (bug?)\n";
                    echo "      Data: " . json_encode($hasPricing) . "\n";
                }
            }
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        echo "   Stack trace: " . $e->getTraceAsString() . "\n";
    }
}

echo "\n🎯 Kesimpulan:\n";
echo "=============\n";
if (isset($tofuCount) && isset($allTofuProducts)) {
    if ($tofuCount < $allTofuProducts->count()) {
        echo "❌ Masalah: Tidak semua produk tofu spesial memiliki pricing untuk tipe customer yang dipilih\n";
        echo "💡 Solusi:\n";
        echo "   1. Buka menu CRM > Tipe Customer\n";
        echo "   2. Pilih tipe customer yang bermasalah\n";
        echo "   3. Pastikan semua produk tofu spesial sudah diatur harga khususnya\n";
        echo "   4. Simpan perubahan\n";
    } else {
        echo "✅ Semua produk tofu spesial memiliki pricing\n";
        echo "❌ Jika masih hanya 1 yang berubah di frontend, masalah ada di JavaScript\n";
        echo "💡 Cek console browser untuk melihat data yang diterima JavaScript\n";
    }
}

echo "\n";
?>