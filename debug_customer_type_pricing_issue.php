<?php
/**
 * Debug Customer Type Pricing Issue
 * Mengecek mengapa hanya satu produk yang berubah harganya
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Debug Customer Type Pricing Issue\n";
echo "====================================\n\n";

// Cari tipe customer yang dimaksud
echo "📋 1. Mencari tipe customer...\n";
$customerTypes = DB::table('tipe_customer')->get();
foreach ($customerTypes as $type) {
    echo "   - ID: {$type->id}, Nama: {$type->nama_tipe}\n";
}

// Ambil ID tipe customer (asumsi yang bermasalah)
$targetTypeId = null;
foreach ($customerTypes as $type) {
    if (stripos($type->nama_tipe, 'khusus') !== false || stripos($type->nama_tipe, 'special') !== false) {
        $targetTypeId = $type->id;
        echo "\n🎯 Target tipe customer: ID {$type->id} - {$type->nama_tipe}\n";
        break;
    }
}

if (!$targetTypeId) {
    echo "\n❌ Tidak ditemukan tipe customer khusus. Menggunakan ID 1 sebagai contoh.\n";
    $targetTypeId = 1;
}

// Cek data produk tipe untuk tipe customer ini
echo "\n📋 2. Mengecek data produk tipe untuk customer type ID: {$targetTypeId}\n";
$produkTipes = DB::table('produk_tipe')
    ->join('produk', 'produk_tipe.id_produk', '=', 'produk.id_produk')
    ->where('produk_tipe.id_tipe', $targetTypeId)
    ->select('produk_tipe.*', 'produk.nama_produk', 'produk.sku', 'produk.harga_jual')
    ->get();

echo "   Ditemukan " . count($produkTipes) . " produk dengan tipe pricing:\n";
foreach ($produkTipes as $pt) {
    echo "   - Produk: {$pt->nama_produk} (SKU: {$pt->sku})\n";
    echo "     ID Produk: {$pt->id_produk}\n";
    echo "     Harga Normal: Rp " . number_format($pt->harga_jual, 0, ',', '.') . "\n";
    echo "     Harga Khusus: Rp " . number_format($pt->harga_khusus, 0, ',', '.') . "\n";
    echo "     Diskon: {$pt->diskon}%\n";
    echo "     Harga Final: Rp " . number_format($pt->harga_final, 0, ',', '.') . "\n";
    echo "     Status: " . ($pt->status ? 'Aktif' : 'Tidak Aktif') . "\n\n";
}

// Cek apakah ada produk dengan nama yang mengandung "tofu spesial"
echo "📋 3. Mencari produk 'tofu spesial'...\n";
$tofuProducts = DB::table('produk')
    ->where('nama_produk', 'like', '%tofu spesial%')
    ->orWhere('nama_produk', 'like', '%tofu special%')
    ->get();

echo "   Ditemukan " . count($tofuProducts) . " produk tofu spesial:\n";
foreach ($tofuProducts as $product) {
    echo "   - ID: {$product->id_produk}, Nama: {$product->nama_produk}, SKU: {$product->sku}\n";
    
    // Cek apakah produk ini ada di produk_tipe
    $hasPricing = DB::table('produk_tipe')
        ->where('id_produk', $product->id_produk)
        ->where('id_tipe', $targetTypeId)
        ->first();
    
    if ($hasPricing) {
        echo "     ✅ Ada pricing: Harga Final Rp " . number_format($hasPricing->harga_final, 0, ',', '.') . "\n";
    } else {
        echo "     ❌ Tidak ada pricing untuk tipe customer ini\n";
    }
}

// Cek controller POS untuk customer type prices
echo "\n📋 4. Mengecek endpoint customer type prices...\n";
try {
    // Simulasi request ke endpoint
    $testOutletId = 1;
    $customerTypePrices = DB::table('produk_tipe as pt')
        ->join('produk as p', 'pt.id_produk', '=', 'p.id_produk')
        ->join('produk_outlet as po', function($join) use ($testOutletId) {
            $join->on('p.id_produk', '=', 'po.id_produk')
                 ->where('po.id_outlet', '=', $testOutletId);
        })
        ->where('pt.id_tipe', $targetTypeId)
        ->where('pt.status', 1)
        ->select(
            'pt.id_produk',
            'pt.harga_khusus',
            'pt.diskon',
            'pt.harga_final',
            'p.harga_jual as harga_normal',
            'p.nama_produk',
            'p.sku'
        )
        ->get();

    echo "   Query hasil untuk outlet {$testOutletId}:\n";
    echo "   Ditemukan " . count($customerTypePrices) . " produk dengan pricing:\n";
    
    $pricingArray = [];
    foreach ($customerTypePrices as $price) {
        $pricingArray[$price->id_produk] = [
            'harga_khusus' => $price->harga_khusus,
            'diskon' => $price->diskon,
            'harga_final' => $price->harga_final,
            'harga_normal' => $price->harga_normal
        ];
        
        echo "   - {$price->nama_produk} (ID: {$price->id_produk})\n";
        echo "     Harga Normal: Rp " . number_format($price->harga_normal, 0, ',', '.') . "\n";
        echo "     Harga Final: Rp " . number_format($price->harga_final, 0, ',', '.') . "\n";
    }
    
    echo "\n📋 5. Format data yang dikirim ke frontend:\n";
    echo json_encode($pricingArray, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Cek apakah ada masalah dengan produk_outlet
echo "\n📋 6. Mengecek data produk_outlet...\n";
$outletProducts = DB::table('produk_outlet')
    ->join('produk', 'produk_outlet.id_produk', '=', 'produk.id_produk')
    ->where('produk_outlet.id_outlet', 1)
    ->where('produk.nama_produk', 'like', '%tofu spesial%')
    ->select('produk_outlet.*', 'produk.nama_produk', 'produk.sku')
    ->get();

echo "   Produk tofu spesial di outlet 1:\n";
foreach ($outletProducts as $op) {
    echo "   - {$op->nama_produk} (ID: {$op->id_produk})\n";
}

echo "\n🎯 Kesimpulan:\n";
echo "=============\n";
if (count($produkTipes) > 1) {
    echo "✅ Ada " . count($produkTipes) . " produk dengan customer type pricing\n";
    if (count($customerTypePrices) < count($produkTipes)) {
        echo "❌ Masalah: Tidak semua produk muncul di query endpoint\n";
        echo "   Kemungkinan penyebab:\n";
        echo "   1. Produk tidak ada di produk_outlet untuk outlet yang dipilih\n";
        echo "   2. Status produk_tipe tidak aktif (status = 0)\n";
        echo "   3. Join query bermasalah\n";
    } else {
        echo "✅ Semua produk muncul di query endpoint\n";
        echo "❌ Masalah mungkin di frontend JavaScript\n";
    }
} else {
    echo "❌ Hanya ada " . count($produkTipes) . " produk dengan customer type pricing\n";
    echo "   Pastikan semua produk sudah diatur pricing-nya\n";
}

echo "\n";
?>