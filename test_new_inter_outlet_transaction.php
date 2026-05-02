<?php

/**
 * Script untuk membuat transaksi inter-outlet test dan memverifikasi perbaikan HPP
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\HppProduk;
use App\Models\Produk;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

echo "=== TEST NEW INTER-OUTLET TRANSACTION ===\n\n";

try {
    // 1. Setup data untuk transaksi test
    echo "1. MENYIAPKAN DATA TRANSAKSI TEST...\n";
    
    // Cari outlet yang tersedia
    $outlets = Outlet::where('is_active', true)->limit(3)->get();
    if ($outlets->count() < 2) {
        echo "   ❌ Minimal perlu 2 outlet untuk testing\n";
        return;
    }
    
    $outletAsal = $outlets[0];
    $outletTujuan = $outlets[1];
    
    echo "   Outlet Asal: {$outletAsal->nama_outlet} (ID: {$outletAsal->id_outlet})\n";
    echo "   Outlet Tujuan: {$outletTujuan->nama_outlet} (ID: {$outletTujuan->id_outlet})\n";
    
    // Cari produk dengan stok di outlet asal
    $produkAsal = Produk::where('id_outlet', $outletAsal->id_outlet)
        ->where('is_active', 1)
        ->whereHas('hppProduk', function($query) {
            $query->where('stok', '>', 10);
        })
        ->first();
    
    if (!$produkAsal) {
        echo "   ❌ Tidak ada produk dengan stok yang cukup di outlet asal\n";
        return;
    }
    
    echo "   Produk: {$produkAsal->nama_produk} (ID: {$produkAsal->id_produk})\n";
    echo "   Kode: {$produkAsal->kode_produk}\n";
    
    // Cek HPP di outlet asal
    $hppAsal = HppProduk::where('id_produk', $produkAsal->id_produk)
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$hppAsal) {
        echo "   ❌ Tidak ada HPP dengan stok > 0 di outlet asal\n";
        return;
    }
    
    echo "   HPP Asal: Rp " . number_format($hppAsal->hpp, 0, ',', '.') . "\n";
    echo "   Stok Tersedia: {$hppAsal->stok}\n";
    
    // Setup data transaksi
    $kuantitas = 5;
    $hargaJual = 3000; // Harga jual yang akan menjadi HPP di outlet tujuan
    $subtotal = $kuantitas * $hargaJual;
    
    echo "   Kuantitas Test: {$kuantitas}\n";
    echo "   Harga Jual: Rp " . number_format($hargaJual, 0, ',', '.') . "\n";
    echo "   Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
    
    // 2. Cek kondisi sebelum transaksi
    echo "\n2. KONDISI SEBELUM TRANSAKSI...\n";
    
    // Cek produk di outlet tujuan sebelum transaksi
    $produkTujuanBefore = Produk::where('kode_produk', $produkAsal->kode_produk)
        ->where('id_outlet', $outletTujuan->id_outlet)
        ->first();
    
    if ($produkTujuanBefore) {
        echo "   Produk sudah ada di outlet tujuan (ID: {$produkTujuanBefore->id_produk})\n";
        
        $hppTujuanBefore = HppProduk::where('id_produk', $produkTujuanBefore->id_produk)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        echo "   HPP di outlet tujuan sebelum transaksi:\n";
        foreach ($hppTujuanBefore as $i => $hpp) {
            echo "      " . ($i + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                 ", Stok: {$hpp->stok}, Tanggal: {$hpp->created_at}\n";
        }
    } else {
        echo "   Produk belum ada di outlet tujuan (akan dibuat otomatis)\n";
    }
    
    // 3. Simulasi transaksi menggunakan controller
    echo "\n3. MEMBUAT TRANSAKSI INTER-OUTLET...\n";
    
    // Login sebagai user pertama
    $user = User::first();
    if (!$user) {
        echo "   ❌ Tidak ada user untuk testing\n";
        return;
    }
    
    auth()->login($user);
    echo "   Login sebagai: {$user->name}\n";
    
    // Buat request data
    $requestData = [
        'tanggal' => now()->format('Y-m-d'),
        'outlet_asal' => $outletAsal->id_outlet,
        'outlet_tujuan' => $outletTujuan->id_outlet,
        'items' => [
            [
                'id_produk' => $produkAsal->id_produk,
                'kuantitas' => $kuantitas,
                'harga' => $hargaJual,
                'subtotal' => $subtotal
            ]
        ],
        'subtotal' => $subtotal,
        'total' => $subtotal,
        'catatan' => 'Test transaksi untuk verifikasi HPP destination fix'
    ];
    
    // Buat request object
    $request = new Request($requestData);
    
    // Panggil controller
    $controller = new \App\Http\Controllers\InterOutletSaleController(
        new \App\Services\JournalEntryService()
    );
    
    echo "   Memproses transaksi...\n";
    $response = $controller->store($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "   ✅ Transaksi berhasil dibuat!\n";
        echo "   No. Transaksi: " . ($responseData['data']['no_transaksi'] ?? 'N/A') . "\n";
        echo "   Total: Rp " . number_format($responseData['data']['total'] ?? 0, 0, ',', '.') . "\n";
    } else {
        echo "   ❌ Transaksi gagal: " . $responseData['message'] . "\n";
        if (isset($responseData['errors'])) {
            foreach ($responseData['errors'] as $field => $errors) {
                echo "      {$field}: " . implode(', ', $errors) . "\n";
            }
        }
        return;
    }
    
    // 4. Verifikasi hasil transaksi
    echo "\n4. VERIFIKASI HASIL TRANSAKSI...\n";
    
    // Cari transaksi yang baru dibuat
    $newTransaction = InterOutletSale::where('outlet_asal', $outletAsal->id_outlet)
        ->where('outlet_tujuan', $outletTujuan->id_outlet)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$newTransaction) {
        echo "   ❌ Transaksi baru tidak ditemukan\n";
        return;
    }
    
    echo "   Transaksi: {$newTransaction->no_transaksi}\n";
    echo "   Status: {$newTransaction->status}\n";
    echo "   Tanggal: {$newTransaction->created_at}\n";
    
    // Cek produk di outlet tujuan setelah transaksi
    $produkTujuanAfter = Produk::where('kode_produk', $produkAsal->kode_produk)
        ->where('id_outlet', $outletTujuan->id_outlet)
        ->first();
    
    if (!$produkTujuanAfter) {
        echo "   ❌ Produk tidak dibuat di outlet tujuan\n";
        return;
    }
    
    echo "   ✅ Produk berhasil dibuat/diupdate di outlet tujuan (ID: {$produkTujuanAfter->id_produk})\n";
    
    // Cek HPP baru di outlet tujuan
    $hppTujuanAfter = HppProduk::where('id_produk', $produkTujuanAfter->id_produk)
        ->where('created_at', '>=', $newTransaction->created_at)
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$hppTujuanAfter) {
        echo "   ❌ HPP baru tidak dibuat di outlet tujuan\n";
        return;
    }
    
    echo "   ✅ HPP baru berhasil dibuat di outlet tujuan\n";
    echo "   HPP Baru: Rp " . number_format($hppTujuanAfter->hpp, 0, ',', '.') . "\n";
    echo "   Stok: {$hppTujuanAfter->stok}\n";
    echo "   Tanggal: {$hppTujuanAfter->created_at}\n";
    
    // 5. Verifikasi HPP = harga jual
    echo "\n5. VERIFIKASI HPP = HARGA JUAL...\n";
    
    if (abs($hppTujuanAfter->hpp - $hargaJual) < 0.01) {
        echo "   ✅ BERHASIL! HPP di outlet tujuan sesuai dengan harga jual\n";
        echo "   Expected: Rp " . number_format($hargaJual, 0, ',', '.') . "\n";
        echo "   Actual: Rp " . number_format($hppTujuanAfter->hpp, 0, ',', '.') . "\n";
        echo "   Selisih: Rp " . number_format(abs($hppTujuanAfter->hpp - $hargaJual), 2, ',', '.') . "\n";
    } else {
        echo "   ❌ GAGAL! HPP di outlet tujuan tidak sesuai dengan harga jual\n";
        echo "   Expected: Rp " . number_format($hargaJual, 0, ',', '.') . "\n";
        echo "   Actual: Rp " . number_format($hppTujuanAfter->hpp, 0, ',', '.') . "\n";
        echo "   Selisih: Rp " . number_format(abs($hppTujuanAfter->hpp - $hargaJual), 2, ',', '.') . "\n";
    }
    
    // 6. Cek data HPP yang disimpan
    echo "\n6. VERIFIKASI DATA HPP TERSIMPAN...\n";
    
    $transactionItem = InterOutletSaleItem::where('inter_outlet_sale_id', $newTransaction->id)
        ->first();
    
    if ($transactionItem && $transactionItem->data_hpp) {
        echo "   ✅ Data HPP tersimpan dalam format JSON\n";
        echo "   Data HPP: " . json_encode($transactionItem->data_hpp, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ⚠️  Data HPP tidak tersimpan atau kosong\n";
    }
    
    echo "\n=== RINGKASAN TEST ===\n";
    
    $success = abs($hppTujuanAfter->hpp - $hargaJual) < 0.01;
    
    if ($success) {
        echo "✅ PERBAIKAN BERHASIL!\n";
        echo "✅ HPP di outlet tujuan = harga jual dari outlet asal\n";
        echo "✅ Tidak ada lagi HPP 0 di outlet tujuan\n";
        echo "✅ Transaksi inter-outlet berfungsi dengan benar\n";
    } else {
        echo "❌ PERBAIKAN BELUM BERHASIL\n";
        echo "❌ HPP di outlet tujuan masih tidak sesuai\n";
        echo "💡 Perlu investigasi lebih lanjut\n";
    }
    
    echo "\n=== LANGKAH SELANJUTNYA ===\n";
    echo "1. Periksa laporan margin untuk transaksi ini\n";
    echo "2. Verifikasi perhitungan profit/margin sudah benar\n";
    echo "3. Test dengan produk dan outlet lain\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";