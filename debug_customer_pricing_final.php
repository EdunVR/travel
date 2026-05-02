<?php
/**
 * Debug Customer Type Pricing - Final Version
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Debug Customer Type Pricing - Final\n";
echo "=====================================\n\n";

// 1. Cek produk tofu spesial
echo "📋 1. Produk Tofu Spesial:\n";
$tofuProducts = DB::table('produk')
    ->where('nama_produk', 'like', '%tofu spesial%')
    ->orWhere('nama_produk', 'like', '%tofu special%')
    ->select('id_produk', 'nama_produk', 'kode_produk', 'harga_jual')
    ->get();

echo "   Ditemukan " . count($tofuProducts) . " produk:\n";
foreach ($tofuProducts as $product) {
    echo "   - ID: {$product->id_produk}, Nama: {$product->nama_produk}\n";
    echo "     Kode: {$product->kode_produk}, Harga: Rp " . number_format($product->harga_jual, 0, ',', '.') . "\n\n";
}

// 2. Cek tipe customer yang memiliki pricing untuk produk tofu spesial
echo "📋 2. Tipe Customer dengan pricing tofu spesial:\n";
$tofuProductIds = $tofuProducts->pluck('id_produk')->toArray();

if (!empty($tofuProductIds)) {
    $tipeWithTofu = DB::table('produk_tipe as pt')
        ->join('tipe as t', 'pt.id_tipe', '=', 't.id_tipe')
        ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
        ->whereIn('pt.id_produk', $tofuProductIds)
        ->select('pt.*', 't.nama_tipe', 'p.nama_produk', 'p.kode_produk', 'p.harga_jual as harga_normal')
        ->get();

    $groupedByTipe = [];
    foreach ($tipeWithTofu as $item) {
        if (!isset($groupedByTipe[$item->id_tipe])) {
            $groupedByTipe[$item->id_tipe] = [
                'nama_tipe' => $item->nama_tipe,
                'produk' => []
            ];
        }
        $groupedByTipe[$item->id_tipe]['produk'][] = $item;
    }

    foreach ($groupedByTipe as $tipeId => $data) {
        echo "   🏷️ Tipe: {$data['nama_tipe']} (ID: {$tipeId})\n";
        foreach ($data['produk'] as $item) {
            $hargaFinal = $item->harga_jual; // harga_jual di produk_tipe adalah harga khusus
            if ($item->diskon > 0) {
                $hargaFinal = $item->harga_normal * (1 - $item->diskon / 100);
            }
            
            echo "      - {$item->nama_produk}\n";
            echo "        Harga Normal: Rp " . number_format($item->harga_normal, 0, ',', '.') . "\n";
            echo "        Harga Khusus: Rp " . number_format($item->harga_jual, 0, ',', '.') . "\n";
            echo "        Diskon: {$item->diskon}%\n";
            echo "        Harga Final: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n\n";
        }
    }

    // 3. Test endpoint untuk tipe tertentu
    if (!empty($groupedByTipe)) {
        $testTipeId = array_keys($groupedByTipe)[0]; // Ambil tipe pertama
        $testOutletId = 1;
        
        echo "📋 3. Test endpoint untuk tipe ID: {$testTipeId} (outlet: {$testOutletId}):\n";
        
        // Query sesuai controller POS
        $customerTypePrices = DB::table('produk_tipe as pt')
            ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
            ->leftJoin('produk_outlet as po', function($join) use ($testOutletId) {
                $join->on('p.id_produk', '=', 'po.id_produk')
                     ->where('po.id_outlet', '=', $testOutletId);
            })
            ->where('pt.id_tipe', $testTipeId)
            ->select(
                'pt.id_produk',
                'pt.harga_jual as harga_khusus',
                'pt.diskon',
                'p.harga_jual as harga_normal',
                'p.nama_produk',
                'p.kode_produk',
                'po.id_outlet'
            )
            ->get();

        echo "   Query hasil (total: " . count($customerTypePrices) . "):\n";
        $pricingArray = [];
        
        foreach ($customerTypePrices as $price) {
            // Hitung harga final
            $hargaFinal = $price->harga_khusus;
            if ($price->diskon > 0) {
                $hargaFinal = $price->harga_normal * (1 - $price->diskon / 100);
            }
            
            $pricingArray[$price->id_produk] = [
                'harga_khusus' => floatval($price->harga_khusus),
                'diskon' => floatval($price->diskon),
                'harga_final' => floatval($hargaFinal),
                'harga_normal' => floatval($price->harga_normal)
            ];
            
            echo "   - {$price->nama_produk} (ID: {$price->id_produk})\n";
            echo "     Outlet: " . ($price->id_outlet ? $price->id_outlet : 'NULL') . "\n";
            echo "     Harga Normal: Rp " . number_format($price->harga_normal, 0, ',', '.') . "\n";
            echo "     Harga Khusus: Rp " . number_format($price->harga_khusus, 0, ',', '.') . "\n";
            echo "     Diskon: {$price->diskon}%\n";
            echo "     Harga Final: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n\n";
        }
        
        echo "   Format JSON untuk frontend:\n";
        echo json_encode($pricingArray, JSON_PRETTY_PRINT) . "\n\n";
        
        // 4. Cek mengapa produk tidak muncul
        echo "📋 4. Analisis produk yang tidak muncul:\n";
        foreach ($tofuProducts as $product) {
            if (!isset($pricingArray[$product->id_produk])) {
                echo "   ❌ {$product->nama_produk} (ID: {$product->id_produk}) tidak muncul\n";
                
                // Cek apakah ada di produk_tipe
                $checkProdukTipe = DB::table('produk_tipe')
                    ->where('id_produk', $product->id_produk)
                    ->where('id_tipe', $testTipeId)
                    ->first();
                
                if (!$checkProdukTipe) {
                    echo "      Alasan: Tidak ada pricing untuk tipe customer ini\n";
                    echo "      💡 Solusi: Tambahkan pricing di CRM > Tipe Customer\n";
                } else {
                    echo "      Ada di produk_tipe, tapi tidak muncul di query\n";
                    
                    // Cek produk_outlet
                    $checkProdukOutlet = DB::table('produk_outlet')
                        ->where('id_produk', $product->id_produk)
                        ->where('id_outlet', $testOutletId)
                        ->first();
                    
                    if (!$checkProdukOutlet) {
                        echo "      Kemungkinan: Produk tidak ada di outlet {$testOutletId}\n";
                        echo "      💡 Solusi: Pastikan produk aktif di outlet yang dipilih\n";
                    }
                }
            } else {
                echo "   ✅ {$product->nama_produk} - Muncul dengan benar\n";
            }
        }
    }
}

// 5. Cek controller POS
echo "\n📋 5. Cek PosController customer-type-prices method:\n";
$controllerFile = 'app/Http/Controllers/PosController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    if (strpos($content, 'customer-type-prices') !== false) {
        echo "   ✅ Method customer-type-prices ditemukan\n";
        
        // Cari method getCustomerTypePrices
        if (preg_match('/function\s+getCustomerTypePrices.*?\{(.*?)\}/s', $content, $matches)) {
            echo "   📄 Method content preview:\n";
            $methodContent = substr($matches[1], 0, 500);
            echo "   " . str_replace("\n", "\n   ", trim($methodContent)) . "...\n";
        }
    } else {
        echo "   ❌ Method customer-type-prices tidak ditemukan\n";
    }
} else {
    echo "   ❌ File PosController.php tidak ditemukan\n";
}

echo "\n🎯 Kesimpulan:\n";
echo "=============\n";
if (count($tofuProducts) > 1) {
    echo "📊 Total produk tofu spesial: " . count($tofuProducts) . "\n";
    
    if (isset($pricingArray)) {
        $foundCount = 0;
        foreach ($tofuProducts as $product) {
            if (isset($pricingArray[$product->id_produk])) {
                $foundCount++;
            }
        }
        
        echo "📊 Yang muncul di pricing: {$foundCount}\n";
        
        if ($foundCount < count($tofuProducts)) {
            echo "❌ Masalah: Tidak semua produk memiliki pricing atau tidak muncul di query\n";
            echo "💡 Langkah perbaikan:\n";
            echo "   1. Pastikan semua produk tofu spesial sudah diatur pricing di CRM > Tipe Customer\n";
            echo "   2. Pastikan produk aktif di outlet yang dipilih\n";
            echo "   3. Cek query di PosController method getCustomerTypePrices\n";
        } else {
            echo "✅ Semua produk memiliki pricing\n";
            echo "❌ Masalah mungkin di frontend JavaScript\n";
        }
    }
} else {
    echo "❌ Hanya ditemukan " . count($tofuProducts) . " produk tofu spesial\n";
}

echo "\n";
?>