<?php
/**
 * Debug Customer Type Pricing - Accurate Version
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Debug Customer Type Pricing - Accurate\n";
echo "========================================\n\n";

// 1. Cek tipe customer
echo "📋 1. Tipe Customer yang tersedia:\n";
$tipes = DB::table('tipe')->get();
foreach ($tipes as $tipe) {
    echo "   - ID: {$tipe->id_tipe}, Nama: {$tipe->nama_tipe}, Outlet: {$tipe->id_outlet}\n";
}

// 2. Cek produk yang memiliki pricing khusus
echo "\n📋 2. Produk dengan pricing khusus:\n";
$produkTipes = DB::table('produk_tipe as pt')
    ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
    ->join('tipe as t', 'pt.id_tipe', '=', 't.id_tipe')
    ->select('pt.*', 'p.nama_produk', 'p.sku', 'p.harga_jual as harga_normal', 't.nama_tipe')
    ->get();

echo "   Total: " . count($produkTipes) . " kombinasi produk-tipe\n\n";

// Group by tipe
$groupedByTipe = [];
foreach ($produkTipes as $pt) {
    if (!isset($groupedByTipe[$pt->id_tipe])) {
        $groupedByTipe[$pt->id_tipe] = [
            'nama_tipe' => $pt->nama_tipe,
            'produk' => []
        ];
    }
    $groupedByTipe[$pt->id_tipe]['produk'][] = $pt;
}

foreach ($groupedByTipe as $tipeId => $data) {
    echo "   🏷️ Tipe: {$data['nama_tipe']} (ID: {$tipeId})\n";
    foreach ($data['produk'] as $pt) {
        echo "      - {$pt->nama_produk} (SKU: {$pt->sku})\n";
        echo "        ID Produk: {$pt->id_produk}\n";
        echo "        Harga Normal: Rp " . number_format($pt->harga_normal, 0, ',', '.') . "\n";
        echo "        Harga Khusus: Rp " . number_format($pt->harga_jual, 0, ',', '.') . "\n";
        echo "        Diskon: {$pt->diskon}%\n";
        
        // Hitung harga final
        $hargaFinal = $pt->harga_jual;
        if ($pt->diskon > 0) {
            $hargaFinal = $pt->harga_normal * (1 - $pt->diskon / 100);
        }
        echo "        Harga Final: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n\n";
    }
}

// 3. Cek produk tofu spesial khusus
echo "📋 3. Produk Tofu Spesial:\n";
$tofuProducts = DB::table('produk')
    ->where('nama_produk', 'like', '%tofu spesial%')
    ->orWhere('nama_produk', 'like', '%tofu special%')
    ->get();

foreach ($tofuProducts as $product) {
    echo "   - ID: {$product->id_produk}, Nama: {$product->nama_produk}\n";
    echo "     SKU: {$product->sku}, Harga Normal: Rp " . number_format($product->harga_jual, 0, ',', '.') . "\n";
    
    // Cek pricing untuk produk ini
    $pricings = DB::table('produk_tipe as pt')
        ->join('tipe as t', 'pt.id_tipe', '=', 't.id_tipe')
        ->where('pt.id_produk', $product->id_produk)
        ->select('pt.*', 't.nama_tipe')
        ->get();
    
    if (count($pricings) > 0) {
        echo "     Pricing khusus:\n";
        foreach ($pricings as $pricing) {
            $hargaFinal = $pricing->harga_jual;
            if ($pricing->diskon > 0) {
                $hargaFinal = $product->harga_jual * (1 - $pricing->diskon / 100);
            }
            echo "       - Tipe: {$pricing->nama_tipe}\n";
            echo "         Harga Khusus: Rp " . number_format($pricing->harga_jual, 0, ',', '.') . "\n";
            echo "         Diskon: {$pricing->diskon}%\n";
            echo "         Harga Final: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n";
        }
    } else {
        echo "     ❌ Tidak ada pricing khusus\n";
    }
    echo "\n";
}

// 4. Simulasi endpoint customer type prices
echo "📋 4. Simulasi endpoint customer-type-prices:\n";
$testTipeId = null;
$testOutletId = 1;

// Cari tipe yang memiliki produk tofu spesial
foreach ($groupedByTipe as $tipeId => $data) {
    foreach ($data['produk'] as $pt) {
        if (stripos($pt->nama_produk, 'tofu spesial') !== false) {
            $testTipeId = $tipeId;
            echo "   Menggunakan tipe: {$data['nama_tipe']} (ID: {$tipeId})\n";
            break 2;
        }
    }
}

if ($testTipeId) {
    echo "   Query untuk tipe ID: {$testTipeId}, outlet ID: {$testOutletId}\n\n";
    
    // Query sesuai dengan yang ada di controller
    $customerTypePrices = DB::table('produk_tipe as pt')
        ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
        ->join('produk_outlet as po', function($join) use ($testOutletId) {
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
            'p.sku'
        )
        ->get();

    echo "   Hasil query:\n";
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
        echo "     Harga Normal: Rp " . number_format($price->harga_normal, 0, ',', '.') . "\n";
        echo "     Harga Khusus: Rp " . number_format($price->harga_khusus, 0, ',', '.') . "\n";
        echo "     Diskon: {$price->diskon}%\n";
        echo "     Harga Final: Rp " . number_format($hargaFinal, 0, ',', '.') . "\n\n";
    }
    
    echo "   Format JSON untuk frontend:\n";
    echo json_encode($pricingArray, JSON_PRETTY_PRINT) . "\n\n";
    
    // Cek apakah semua produk tofu spesial ada di hasil
    echo "   Verifikasi produk tofu spesial:\n";
    foreach ($tofuProducts as $product) {
        if (isset($pricingArray[$product->id_produk])) {
            echo "   ✅ {$product->nama_produk} - Ada di hasil\n";
        } else {
            echo "   ❌ {$product->nama_produk} - TIDAK ada di hasil\n";
            
            // Cek mengapa tidak ada
            $checkProdukTipe = DB::table('produk_tipe')
                ->where('id_produk', $product->id_produk)
                ->where('id_tipe', $testTipeId)
                ->first();
            
            if (!$checkProdukTipe) {
                echo "      Alasan: Tidak ada di tabel produk_tipe\n";
            } else {
                $checkProdukOutlet = DB::table('produk_outlet')
                    ->where('id_produk', $product->id_produk)
                    ->where('id_outlet', $testOutletId)
                    ->first();
                
                if (!$checkProdukOutlet) {
                    echo "      Alasan: Tidak ada di tabel produk_outlet untuk outlet {$testOutletId}\n";
                } else {
                    echo "      Alasan: Unknown - ada di kedua tabel\n";
                }
            }
        }
    }
}

echo "\n🎯 Kesimpulan:\n";
echo "=============\n";
if (count($tofuProducts) > 1) {
    $foundInPricing = 0;
    foreach ($tofuProducts as $product) {
        if (isset($pricingArray[$product->id_produk])) {
            $foundInPricing++;
        }
    }
    
    echo "📊 Produk tofu spesial: " . count($tofuProducts) . "\n";
    echo "📊 Yang ada pricing: {$foundInPricing}\n";
    
    if ($foundInPricing < count($tofuProducts)) {
        echo "❌ Masalah: Tidak semua produk tofu spesial memiliki pricing\n";
        echo "💡 Solusi: Pastikan semua produk sudah diatur di menu CRM > Tipe Customer\n";
    } else {
        echo "✅ Semua produk tofu spesial memiliki pricing\n";
        echo "❌ Masalah mungkin di frontend JavaScript atau controller\n";
    }
}

echo "\n";
?>