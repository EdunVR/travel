<?php

/**
 * Debug script untuk melacak HPP inter-outlet pada kasus spesifik
 * 
 * Kasus: Tanggal 23 Jan 2026, Produk "Tofu Spesial Udang 120g", Qty 8000, HPP Rp 1.333
 * 
 * Tujuan: Melacak dari mana nilai HPP Rp 1.333 berasal
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== DEBUG INTER-OUTLET HPP TRACKING ===\n\n";

try {
    // Step 1: Cari data inter-outlet sale pada tanggal 23 Jan 2026
    echo "1. MENCARI DATA INTER-OUTLET SALE...\n";
    echo "   Tanggal: 23 Jan 2026 (2026-01-23)\n";
    echo "   Produk: Tofu Spesial Udang 120g\n";
    echo "   Qty: 8000\n\n";
    
    // Query untuk mencari inter-outlet sale
    $interOutletQuery = "
    SELECT 
        ios.id,
        ios.no_transaksi,
        ios.tanggal,
        ios.outlet_asal,
        ios.outlet_tujuan,
        ios.status,
        iosi.id as item_id,
        iosi.id_produk,
        iosi.kuantitas,
        iosi.harga,
        iosi.subtotal,
        p.nama_produk,
        oa.nama_outlet as outlet_asal_nama,
        ot.nama_outlet as outlet_tujuan_nama
    FROM inter_outlet_sales ios
    JOIN inter_outlet_sale_items iosi ON ios.id = iosi.inter_outlet_sale_id
    JOIN produk p ON iosi.id_produk = p.id_produk
    JOIN outlets oa ON ios.outlet_asal = oa.id_outlet
    JOIN outlets ot ON ios.outlet_tujuan = ot.id_outlet
    WHERE DATE(ios.tanggal) = '2026-01-23'
    AND p.nama_produk LIKE '%Tofu Spesial Udang 120g%'
    AND iosi.kuantitas = 8000
    ORDER BY ios.created_at DESC
    ";
    
    echo "Query Inter-Outlet Sale:\n";
    echo $interOutletQuery . "\n\n";
    
    // Step 2: Cari data produk untuk mendapatkan id_produk
    echo "2. MENCARI DATA PRODUK...\n";
    
    $produkQuery = "
    SELECT 
        id_produk,
        nama_produk,
        kode_produk,
        id_outlet,
        tipe_produk,
        harga_jual,
        created_at,
        updated_at
    FROM produk 
    WHERE nama_produk LIKE '%Tofu Spesial Udang 120g%'
    ORDER BY created_at DESC
    ";
    
    echo "Query Produk:\n";
    echo $produkQuery . "\n\n";
    
    // Step 3: Cari data HPP produk (tabel hpp_produk)
    echo "3. MENCARI DATA HPP PRODUK...\n";
    echo "   Ini adalah data yang digunakan untuk perhitungan FIFO\n\n";
    
    $hppQuery = "
    SELECT 
        id_hpp,
        id_produk,
        hpp,
        stok,
        created_at,
        updated_at
    FROM hpp_produk 
    WHERE id_produk IN (
        SELECT id_produk FROM produk 
        WHERE nama_produk LIKE '%Tofu Spesial Udang 120g%'
    )
    AND stok > 0
    ORDER BY created_at ASC
    ";
    
    echo "Query HPP Produk (FIFO Order):\n";
    echo $hppQuery . "\n\n";
    
    // Step 4: Simulasi perhitungan FIFO
    echo "4. SIMULASI PERHITUNGAN FIFO...\n";
    echo "   Berdasarkan method calculateHppFifo di MarginReportController\n\n";
    
    // Contoh data HPP (akan diganti dengan data real dari database)
    $sampleHppData = [
        ['id_hpp' => 1, 'hpp' => 1200, 'stok' => 5000, 'created_at' => '2026-01-20'],
        ['id_hpp' => 2, 'hpp' => 1400, 'stok' => 4000, 'created_at' => '2026-01-21'],
        ['id_hpp' => 3, 'hpp' => 1500, 'stok' => 2000, 'created_at' => '2026-01-22'],
    ];
    
    echo "Sample HPP Data (akan diganti dengan data real):\n";
    foreach ($sampleHppData as $i => $hpp) {
        echo "   Batch " . ($i + 1) . ": HPP Rp " . number_format($hpp['hpp'], 0, ',', '.') . 
             ", Stok " . $hpp['stok'] . ", Tanggal " . $hpp['created_at'] . "\n";
    }
    echo "\n";
    
    // Simulasi FIFO calculation
    $qty = 8000;
    $totalHppFifo = 0;
    $remainingQty = $qty;
    $usedBatches = [];
    
    echo "Perhitungan FIFO untuk qty $qty:\n";
    
    foreach ($sampleHppData as $i => $hpp) {
        if ($remainingQty <= 0) break;
        
        $usedQty = min($hpp['stok'], $remainingQty);
        $batchTotal = $hpp['hpp'] * $usedQty;
        $totalHppFifo += $batchTotal;
        $remainingQty -= $usedQty;
        
        $usedBatches[] = [
            'batch' => $i + 1,
            'hpp' => $hpp['hpp'],
            'used_qty' => $usedQty,
            'batch_total' => $batchTotal
        ];
        
        echo "   Batch " . ($i + 1) . ": Ambil $usedQty unit @ Rp " . 
             number_format($hpp['hpp'], 0, ',', '.') . " = Rp " . 
             number_format($batchTotal, 0, ',', '.') . "\n";
    }
    
    $hppPerUnit = $qty > 0 ? $totalHppFifo / $qty : 0;
    
    echo "\n";
    echo "Total HPP FIFO: Rp " . number_format($totalHppFifo, 0, ',', '.') . "\n";
    echo "HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
    echo "Expected HPP: Rp 1.333\n";
    echo "Difference: Rp " . number_format(abs($hppPerUnit - 1333), 0, ',', '.') . "\n\n";
    
    // Step 5: Cek method calculateHppFifo di MarginReportController
    echo "5. ANALISIS METHOD calculateHppFifo...\n";
    
    $controllerFile = 'app/Http/Controllers/MarginReportController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        // Extract method calculateHppFifo
        $pattern = '/private function calculateHppFifo.*?(?=private function|\}$)/s';
        if (preg_match($pattern, $content, $matches)) {
            echo "Method calculateHppFifo ditemukan:\n";
            echo "```php\n";
            echo trim($matches[0]) . "\n";
            echo "```\n\n";
        } else {
            echo "Method calculateHppFifo tidak ditemukan dalam controller\n\n";
        }
    }
    
    // Step 6: Kemungkinan sumber HPP yang berbeda
    echo "6. KEMUNGKINAN SUMBER HPP YANG BERBEDA...\n\n";
    
    echo "Kemungkinan 1: Data HPP di database berbeda dengan simulasi\n";
    echo "   - Cek tabel hpp_produk untuk produk Tofu Spesial Udang 120g\n";
    echo "   - Pastikan urutan FIFO (ORDER BY created_at ASC)\n";
    echo "   - Pastikan stok > 0\n\n";
    
    echo "Kemungkinan 2: Perhitungan FIFO tidak sesuai\n";
    echo "   - Method calculateHppFifo mungkin ada bug\n";
    echo "   - Pembagian total HPP dengan quantity tidak tepat\n\n";
    
    echo "Kemungkinan 3: Data produk salah\n";
    echo "   - id_produk yang digunakan tidak sesuai\n";
    echo "   - Nama produk tidak exact match\n\n";
    
    echo "Kemungkinan 4: Cache atau data lama\n";
    echo "   - Data HPP di cache belum terupdate\n";
    echo "   - Transaksi lain sudah mengubah stok HPP\n\n";
    
    // Step 7: Query untuk debugging
    echo "7. QUERY DEBUGGING YANG PERLU DIJALANKAN...\n\n";
    
    echo "A. Cek data inter-outlet sale:\n";
    echo "```sql\n";
    echo $interOutletQuery;
    echo "```\n\n";
    
    echo "B. Cek data produk:\n";
    echo "```sql\n";
    echo $produkQuery;
    echo "```\n\n";
    
    echo "C. Cek data HPP produk:\n";
    echo "```sql\n";
    echo $hppQuery;
    echo "```\n\n";
    
    echo "D. Cek perhitungan manual:\n";
    echo "```sql\n";
    echo "-- Hitung total HPP FIFO manual\n";
    echo "SELECT \n";
    echo "    id_hpp,\n";
    echo "    hpp,\n";
    echo "    stok,\n";
    echo "    created_at,\n";
    echo "    -- Simulasi pengambilan untuk qty 8000\n";
    echo "    CASE \n";
    echo "        WHEN @remaining_qty := 8000 THEN 0\n";
    echo "        WHEN @remaining_qty > stok THEN stok\n";
    echo "        ELSE @remaining_qty\n";
    echo "    END as used_qty,\n";
    echo "    hpp * used_qty as batch_total\n";
    echo "FROM hpp_produk \n";
    echo "WHERE id_produk = [ID_PRODUK_TOFU]\n";
    echo "AND stok > 0\n";
    echo "ORDER BY created_at ASC;\n";
    echo "```\n\n";
    
    // Step 8: Instruksi untuk debugging manual
    echo "8. INSTRUKSI DEBUGGING MANUAL...\n\n";
    
    echo "Langkah 1: Jalankan query A untuk mendapatkan data inter-outlet sale\n";
    echo "Langkah 2: Catat id_produk dari hasil query A\n";
    echo "Langkah 3: Jalankan query C dengan id_produk yang didapat\n";
    echo "Langkah 4: Hitung manual FIFO berdasarkan data HPP\n";
    echo "Langkah 5: Bandingkan dengan HPP yang muncul di laporan (Rp 1.333)\n";
    echo "Langkah 6: Jika berbeda, cek method calculateHppFifo\n";
    echo "Langkah 7: Jika masih berbeda, cek apakah ada transaksi lain yang mengubah stok\n\n";
    
    echo "=== KESIMPULAN ===\n";
    echo "Untuk melacak HPP Rp 1.333, perlu:\n";
    echo "1. ✅ Jalankan query database untuk data real\n";
    echo "2. ✅ Hitung manual FIFO berdasarkan data hpp_produk\n";
    echo "3. ✅ Bandingkan dengan hasil method calculateHppFifo\n";
    echo "4. ✅ Identifikasi perbedaan dan sumbernya\n\n";
    
    echo "File ini hanya simulasi. Untuk debugging real:\n";
    echo "1. Akses database dan jalankan query di atas\n";
    echo "2. Catat hasil dan bandingkan dengan perhitungan manual\n";
    echo "3. Cek log Laravel untuk error atau warning\n";
    echo "4. Verifikasi method calculateHppFifo\n\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== DEBUG SELESAI ===\n";