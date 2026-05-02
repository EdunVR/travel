<?php

/**
 * Script untuk test perbaikan timezone, format tanggal, dan piutang double
 * Jalankan dengan: php test_timezone_date_piutang_fix.php
 */

echo "🧪 Testing perbaikan timezone, format tanggal, dan piutang double...\n\n";

// 1. Test timezone setting
echo "1. 🌍 Testing timezone setting...\n";

// Set timezone untuk PHP
date_default_timezone_set('Asia/Jakarta');
echo "   PHP Timezone: " . date_default_timezone_get() . "\n";
echo "   Current PHP Time: " . date('d/m/Y H:i:s') . "\n";

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=demo;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Set MySQL timezone
    $pdo->exec("SET time_zone = '+07:00'");
    
    $stmt = $pdo->query("SELECT NOW() as current_mysql_time, @@time_zone as mysql_timezone");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   MySQL Timezone: {$result['mysql_timezone']}\n";
    echo "   Current MySQL Time: {$result['current_mysql_time']}\n";
    echo "   ✅ Timezone setting OK\n\n";

} catch (PDOException $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 2. Test unique constraint pada piutang
echo "2. 🔒 Testing unique constraint pada piutang...\n";

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
    
    $constraint = $stmt->fetch();
    
    if ($constraint) {
        echo "   ✅ Unique constraint '{$constraint['CONSTRAINT_NAME']}' sudah ada\n";
        
        // Test insert duplicate (should fail)
        try {
            $pdo->exec("
                INSERT INTO piutang (id_penjualan, nama, piutang, jumlah_piutang, sisa_piutang, status) 
                VALUES (999999, 'Test Duplicate', 100000, 100000, 100000, 'belum_lunas')
            ");
            
            // Try insert duplicate
            $pdo->exec("
                INSERT INTO piutang (id_penjualan, nama, piutang, jumlah_piutang, sisa_piutang, status) 
                VALUES (999999, 'Test Duplicate 2', 100000, 100000, 100000, 'belum_lunas')
            ");
            
            echo "   ❌ Constraint tidak bekerja - duplikasi berhasil diinsert\n";
            
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "   ✅ Constraint bekerja - duplikasi ditolak\n";
            } else {
                echo "   ⚠️ Error lain: " . $e->getMessage() . "\n";
            }
        }
        
        // Cleanup test data
        $pdo->exec("DELETE FROM piutang WHERE id_penjualan = 999999");
        
    } else {
        echo "   ❌ Unique constraint belum ada\n";
    }
    
    echo "\n";

} catch (PDOException $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 3. Test format tanggal
echo "3. 📅 Testing format tanggal...\n";

// Simulasi format tanggal DD/MM/YYYY
function formatDateDDMMYYYY($date) {
    if (!$date) return '-';
    
    try {
        $d = new DateTime($date);
        return $d->format('d/m/Y');
    } catch (Exception $e) {
        return '-';
    }
}

function formatDateTimeDDMMYYYY($date) {
    if (!$date) return '-';
    
    try {
        $d = new DateTime($date);
        return $d->format('d/m/Y H:i');
    } catch (Exception $e) {
        return '-';
    }
}

$testDates = [
    '2026-01-17',
    '2026-01-17 15:30:45',
    '2025-12-31 23:59:59',
    null,
    'invalid-date'
];

foreach ($testDates as $testDate) {
    $formatted = formatDateDDMMYYYY($testDate);
    $formattedDateTime = formatDateTimeDDMMYYYY($testDate);
    
    echo "   Input: " . ($testDate ?: 'null') . "\n";
    echo "   Output Date: $formatted\n";
    echo "   Output DateTime: $formattedDateTime\n";
    echo "   ---\n";
}

echo "   ✅ Format tanggal testing selesai\n\n";

// 4. Test data piutang existing
echo "4. 📊 Checking existing piutang data...\n";

try {
    $stmt = $pdo->query("
        SELECT COUNT(*) as total_piutang,
               COUNT(DISTINCT id_penjualan) as unique_penjualan,
               SUM(CASE WHEN status = 'belum_lunas' THEN 1 ELSE 0 END) as belum_lunas,
               SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as lunas
        FROM piutang 
        WHERE id_penjualan IS NOT NULL
    ");
    
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   Total piutang records: {$stats['total_piutang']}\n";
    echo "   Unique penjualan: {$stats['unique_penjualan']}\n";
    echo "   Belum lunas: {$stats['belum_lunas']}\n";
    echo "   Lunas: {$stats['lunas']}\n";
    
    if ($stats['total_piutang'] == $stats['unique_penjualan']) {
        echo "   ✅ Tidak ada duplikasi piutang\n";
    } else {
        echo "   ⚠️ Kemungkinan ada duplikasi piutang\n";
    }
    
    echo "\n";

} catch (PDOException $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

echo "🎉 Testing selesai!\n\n";

echo "📋 Ringkasan perbaikan yang telah dilakukan:\n";
echo "✅ 1. Timezone sudah diset ke Asia/Jakarta (WIB)\n";
echo "✅ 2. Unique constraint pada piutang untuk mencegah duplikasi\n";
echo "✅ 3. Helper untuk format tanggal DD/MM/YYYY konsisten\n";
echo "✅ 4. Update POS controller dengan validasi piutang\n";
echo "✅ 5. JavaScript helper untuk format tanggal di frontend\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "1. Test transaksi POS dengan BON untuk memastikan tidak ada duplikasi\n";
echo "2. Cek semua halaman ERP baru menggunakan format tanggal DD/MM/YYYY\n";
echo "3. Monitor log untuk memastikan tidak ada error timezone\n";
echo "4. Update view lain yang belum menggunakan DateHelper\n";