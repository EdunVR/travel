<?php

/**
 * Implementasi Quick Wins untuk Optimasi Performance
 * Script ini akan menambahkan index dan optimasi database
 */

echo "⚡ IMPLEMENTASI QUICK WINS OPTIMASI\n";
echo "==================================\n\n";

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=demo;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 1. Add missing indexes for better performance
    echo "📇 1. MENAMBAH INDEX UNTUK PERFORMA\n";
    echo "-----------------------------------\n";
    
    $indexes = [
        [
            'table' => 'piutang',
            'name' => 'idx_piutang_status_jatuh_tempo',
            'columns' => 'status, tanggal_jatuh_tempo',
            'description' => 'Untuk query piutang berdasarkan status dan jatuh tempo'
        ],
        [
            'table' => 'penjualan',
            'name' => 'idx_penjualan_created_at',
            'columns' => 'created_at',
            'description' => 'Untuk laporan penjualan berdasarkan tanggal'
        ],
        [
            'table' => 'penjualan',
            'name' => 'idx_penjualan_outlet_date',
            'columns' => 'id_outlet, created_at',
            'description' => 'Untuk laporan penjualan per outlet dan tanggal'
        ],
        [
            'table' => 'pos_sales',
            'name' => 'idx_pos_sales_outlet_status',
            'columns' => 'id_outlet, status',
            'description' => 'Untuk query POS berdasarkan outlet dan status'
        ],
        [
            'table' => 'hpp_produk',
            'name' => 'idx_hpp_produk_stok_positive',
            'columns' => 'id_produk, stok',
            'description' => 'Untuk query stok produk yang tersedia'
        ]
    ];
    
    foreach ($indexes as $index) {
        try {
            // Check if index already exists
            $stmt = $pdo->query("SHOW INDEX FROM `{$index['table']}` WHERE Key_name = '{$index['name']}'");
            $existing = $stmt->fetch();
            
            if (!$existing) {
                $sql = "ALTER TABLE `{$index['table']}` ADD INDEX `{$index['name']}` ({$index['columns']})";
                $pdo->exec($sql);
                echo "   ✅ Added index {$index['name']} on {$index['table']}\n";
                echo "      Purpose: {$index['description']}\n";
            } else {
                echo "   ✅ Index {$index['name']} already exists on {$index['table']}\n";
            }
        } catch (PDOException $e) {
            echo "   ❌ Failed to add index {$index['name']}: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // 2. Optimize table structure
    echo "🔧 2. OPTIMASI STRUKTUR TABEL\n";
    echo "-----------------------------\n";
    
    $tables = ['produk', 'penjualan', 'piutang', 'hpp_produk', 'pos_sales'];
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("OPTIMIZE TABLE `$table`");
            echo "   ✅ Optimized table: $table\n";
        } catch (PDOException $e) {
            echo "   ⚠️  Could not optimize $table: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // 3. Analyze tables for better query planning
    echo "📊 3. ANALISIS TABEL UNTUK QUERY PLANNING\n";
    echo "-----------------------------------------\n";
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("ANALYZE TABLE `$table`");
            echo "   ✅ Analyzed table: $table\n";
        } catch (PDOException $e) {
            echo "   ⚠️  Could not analyze $table: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // 4. Check and fix any table issues
    echo "🔍 4. PEMERIKSAAN INTEGRITAS TABEL\n";
    echo "----------------------------------\n";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("CHECK TABLE `$table`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['Msg_text'] === 'OK') {
                echo "   ✅ Table $table: OK\n";
            } else {
                echo "   ⚠️  Table $table: {$result['Msg_text']}\n";
            }
        } catch (PDOException $e) {
            echo "   ❌ Could not check $table: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // 5. Database configuration recommendations
    echo "⚙️  5. REKOMENDASI KONFIGURASI DATABASE\n";
    echo "--------------------------------------\n";
    
    // Get current buffer pool size
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'innodb_buffer_pool_size'");
    $bufferPool = $stmt->fetch(PDO::FETCH_ASSOC);
    $bufferPoolMB = round($bufferPool['Value'] / 1024 / 1024);
    
    echo "   Current InnoDB Buffer Pool: {$bufferPoolMB} MB\n";
    
    if ($bufferPoolMB < 64) {
        echo "   💡 Recommendation: Increase innodb_buffer_pool_size to at least 64MB\n";
        echo "      Add to my.cnf: innodb_buffer_pool_size = 64M\n";
    } else {
        echo "   ✅ InnoDB Buffer Pool size is adequate\n";
    }
    
    // Check query cache
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'query_cache_size'");
    $queryCache = $stmt->fetch(PDO::FETCH_ASSOC);
    $queryCacheMB = round($queryCache['Value'] / 1024 / 1024);
    
    echo "   Current Query Cache: {$queryCacheMB} MB\n";
    
    if ($queryCacheMB < 8) {
        echo "   💡 Recommendation: Increase query_cache_size to 8MB\n";
        echo "      Add to my.cnf: query_cache_size = 8M\n";
        echo "      Also add: query_cache_type = 1\n";
    } else {
        echo "   ✅ Query Cache size is adequate\n";
    }
    echo "\n";

    // 6. Performance monitoring setup
    echo "📈 6. SETUP MONITORING PERFORMA\n";
    echo "-------------------------------\n";
    
    // Enable slow query log (if not already enabled)
    try {
        $stmt = $pdo->query("SHOW VARIABLES LIKE 'slow_query_log'");
        $slowLog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($slowLog['Value'] === 'OFF') {
            echo "   💡 Slow Query Log is disabled\n";
            echo "      To enable, add to my.cnf:\n";
            echo "      slow_query_log = 1\n";
            echo "      slow_query_log_file = /var/log/mysql/slow.log\n";
            echo "      long_query_time = 2\n";
        } else {
            echo "   ✅ Slow Query Log is enabled\n";
        }
        
        $stmt = $pdo->query("SHOW VARIABLES LIKE 'long_query_time'");
        $longQueryTime = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   Long Query Time: {$longQueryTime['Value']} seconds\n";
        
    } catch (PDOException $e) {
        echo "   ⚠️  Could not check slow query log settings\n";
    }
    echo "\n";

    // 7. Summary and next steps
    echo "📋 7. RINGKASAN DAN LANGKAH SELANJUTNYA\n";
    echo "--------------------------------------\n";
    
    echo "✅ Optimasi database selesai!\n\n";
    
    echo "Yang telah dilakukan:\n";
    echo "• Menambah index untuk query yang sering digunakan\n";
    echo "• Optimasi struktur tabel\n";
    echo "• Analisis tabel untuk query planning\n";
    echo "• Pemeriksaan integritas tabel\n";
    echo "• Evaluasi konfigurasi database\n\n";
    
    echo "Langkah selanjutnya:\n";
    echo "1. 🚀 Implementasi caching di Laravel (Redis/Memcached)\n";
    echo "2. 📱 Optimasi query N+1 dengan eager loading\n";
    echo "3. 🗜️  Implementasi compression untuk response API\n";
    echo "4. ⚡ Setup lazy loading untuk gambar\n";
    echo "5. 📊 Monitoring performa aplikasi\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Quick optimizations completed!\n";