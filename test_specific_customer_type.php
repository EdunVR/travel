<?php
/**
 * Test Specific Customer Type - 3800 Jawa 2025
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProdukTipe;

echo "🧪 Test Customer Type: 3800 Jawa 2025\n";
echo "====================================\n\n";

// Cari tipe customer "3800 Jawa 2025"
$tipe3800 = \Illuminate\Support\Facades\DB::table('tipe')
    ->where('nama_tipe', 'like', '%3800%')
    ->first();

if (!$tipe3800) {
    echo "❌ Tipe customer '3800 Jawa 2025' tidak ditemukan\n";
    exit;
}

echo "📋 Tipe Customer: {$tipe3800->nama_tipe} (ID: {$tipe3800->id_tipe})\n\n";

// Test API untuk tipe ini
echo "📋 Simulasi API getCustomerTypePrices untuk tipe ID: {$tipe3800->id_tipe}\n";

try {
    $produkTipe = ProdukTipe::where('id_tipe', $tipe3800->id_tipe)
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
                'harga_normal' => floatval($pt->produk->harga_jual),
                'diskon' => floatval($pt->diskon ?? 0),
                'harga_khusus' => floatval($pt->harga_jual),
                'harga_final' => floatval($hargaFinal)
            ];
        })
        ->keyBy('id_produk')
        ->toArray();

    echo "Hasil API (total: " . count($produkTipe) . " produk):\n\n";
    
    $tofuProducts = [];
    foreach ($produkTipe as $idProduk => $data) {
        if (stripos($data['nama_produk'], 'tofu spesial') !== false) {
            $tofuProducts[] = $data;
            echo "✅ {$data['nama_produk']} (ID: {$idProduk})\n";
            echo "   SKU: {$data['sku']}\n";
            echo "   Harga Normal: Rp " . number_format($data['harga_normal'], 0, ',', '.') . "\n";
            echo "   Harga Khusus: Rp " . number_format($data['harga_khusus'], 0, ',', '.') . "\n";
            echo "   Diskon: {$data['diskon']}%\n";
            echo "   Harga Final: Rp " . number_format($data['harga_final'], 0, ',', '.') . "\n\n";
        }
    }
    
    echo "📊 Total produk tofu spesial: " . count($tofuProducts) . "\n";
    
    // Cek apakah ada yang harga finalnya 3800
    $harga3800Count = 0;
    foreach ($tofuProducts as $product) {
        if ($product['harga_final'] == 3800) {
            $harga3800Count++;
            echo "💰 Produk dengan harga final 3800: {$product['nama_produk']}\n";
        }
    }
    
    echo "📊 Produk dengan harga final 3800: {$harga3800Count}\n\n";
    
    // Format JSON untuk frontend
    echo "📋 Format JSON yang dikirim ke frontend:\n";
    echo json_encode($produkTipe, JSON_PRETTY_PRINT) . "\n\n";
    
    // Cek apakah user benar bahwa seharusnya ada 2 produk dengan harga 3800
    echo "📋 Verifikasi klaim user:\n";
    echo "User mengatakan seharusnya ada:\n";
    echo "1. Tofu spesial ayam - harga final 3800\n";
    echo "2. Tofu spesial udang 120 gram - harga final 3800\n\n";
    
    $ayamFound = false;
    $udang120Found = false;
    
    foreach ($tofuProducts as $product) {
        if (stripos($product['nama_produk'], 'ayam') !== false && $product['harga_final'] == 3800) {
            $ayamFound = true;
            echo "✅ Tofu spesial ayam dengan harga 3800 - DITEMUKAN\n";
        }
        if (stripos($product['nama_produk'], 'udang') !== false && 
            stripos($product['nama_produk'], '120') !== false && 
            $product['harga_final'] == 3800) {
            $udang120Found = true;
            echo "✅ Tofu spesial udang 120g dengan harga 3800 - DITEMUKAN\n";
        }
    }
    
    if (!$ayamFound) {
        echo "❌ Tofu spesial ayam dengan harga 3800 - TIDAK DITEMUKAN\n";
    }
    if (!$udang120Found) {
        echo "❌ Tofu spesial udang 120g dengan harga 3800 - TIDAK DITEMUKAN\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Kesimpulan:\n";
echo "=============\n";
if (isset($tofuProducts)) {
    if (count($tofuProducts) >= 2 && $harga3800Count >= 2) {
        echo "✅ API mengembalikan data yang benar\n";
        echo "❌ Masalah kemungkinan di frontend JavaScript\n";
        echo "💡 Cek console browser untuk melihat apakah semua data diterima dengan benar\n";
    } else {
        echo "❌ Data di database belum sesuai dengan yang diharapkan user\n";
        echo "💡 Pastikan pricing sudah diatur dengan benar di CRM > Tipe Customer\n";
    }
}

echo "\n";
?>