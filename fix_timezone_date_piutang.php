<?php

/**
 * Script untuk memperbaiki masalah timezone, format tanggal, dan piutang double
 * Jalankan dengan: php fix_timezone_date_piutang.php
 */

echo "🔧 Memulai perbaikan timezone, format tanggal, dan piutang double...\n\n";

// 1. Cek dan perbaiki duplikasi piutang yang sudah ada
echo "1. 🔍 Mengecek duplikasi piutang yang sudah ada...\n";

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=demo;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Cari duplikasi piutang berdasarkan id_penjualan
    $stmt = $pdo->query("
        SELECT id_penjualan, COUNT(*) as count 
        FROM piutang 
        WHERE id_penjualan IS NOT NULL 
        GROUP BY id_penjualan 
        HAVING COUNT(*) > 1
    ");
    
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($duplicates) > 0) {
        echo "   ⚠️ Ditemukan " . count($duplicates) . " duplikasi piutang:\n";
        
        foreach ($duplicates as $dup) {
            echo "   - ID Penjualan: {$dup['id_penjualan']} ({$dup['count']} duplikat)\n";
            
            // Hapus duplikat, sisakan yang pertama (ID terkecil)
            $deleteStmt = $pdo->prepare("
                DELETE FROM piutang 
                WHERE id_penjualan = ? 
                AND id_piutang NOT IN (
                    SELECT min_id FROM (
                        SELECT MIN(id_piutang) as min_id 
                        FROM piutang 
                        WHERE id_penjualan = ?
                    ) as temp
                )
            ");
            
            $deleteStmt->execute([$dup['id_penjualan'], $dup['id_penjualan']]);
            $deleted = $deleteStmt->rowCount();
            echo "     ✅ Dihapus {$deleted} duplikat\n";
        }
    } else {
        echo "   ✅ Tidak ada duplikasi piutang ditemukan\n";
    }

    // 2. Tambah unique constraint jika belum ada
    echo "\n2. 🔒 Menambah unique constraint untuk mencegah duplikasi...\n";
    
    try {
        // Cek apakah constraint sudah ada
        $stmt = $pdo->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = 'demo' 
            AND TABLE_NAME = 'piutang' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME LIKE '%penjualan%'
        ");
        
        $existingConstraint = $stmt->fetch();
        
        if (!$existingConstraint) {
            $pdo->exec("
                ALTER TABLE piutang 
                ADD CONSTRAINT unique_piutang_penjualan 
                UNIQUE (id_penjualan)
            ");
            echo "   ✅ Unique constraint berhasil ditambahkan\n";
        } else {
            echo "   ✅ Unique constraint sudah ada\n";
        }
        
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "   ⚠️ Masih ada duplikat yang perlu dibersihkan manual\n";
        } else {
            echo "   ⚠️ Error menambah constraint: " . $e->getMessage() . "\n";
        }
    }

    // 3. Cek timezone setting
    echo "\n3. 🌍 Mengecek timezone setting...\n";
    
    $stmt = $pdo->query("SELECT @@time_zone as mysql_timezone, NOW() as current_mysql_time");
    $timezone = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   MySQL Timezone: {$timezone['mysql_timezone']}\n";
    echo "   Current MySQL Time: {$timezone['current_mysql_time']}\n";
    echo "   PHP Timezone: " . date_default_timezone_get() . "\n";
    echo "   Current PHP Time: " . date('Y-m-d H:i:s') . "\n";
    
    // Set timezone untuk session ini
    $pdo->exec("SET time_zone = '+07:00'");
    echo "   ✅ MySQL timezone diset ke +07:00 (WIB)\n";

    echo "\n✅ Perbaikan database selesai!\n\n";

} catch (PDOException $e) {
    echo "❌ Error database: " . $e->getMessage() . "\n";
    exit(1);
}

echo "📝 Langkah selanjutnya:\n";
echo "1. Update POS Controller untuk validasi piutang\n";
echo "2. Buat helper untuk format tanggal DD/MM/YYYY\n";
echo "3. Update semua view dengan format tanggal konsisten\n";
echo "4. Test transaksi POS untuk memastikan tidak ada duplikasi\n\n";

echo "🎉 Script selesai dijalankan!\n";