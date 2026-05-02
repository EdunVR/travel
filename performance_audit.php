<?php

/**
 * Performance Audit Script untuk ERP System
 * Menganalisis performa database, queries, dan sistem secara keseluruhan
 */

echo "🔍 PERFORMANCE AUDIT - SISTEM ERP\n";
echo "================================\n\n";

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=demo;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 1. Database Size Analysis
    echo "📊 1. ANALISIS UKURAN DATABASE\n";
    echo "------------------------------\n";
    
    $stmt = $pdo->query("
        SELECT 
            table_name,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
            table_rows,
            ROUND((data_length / 1024 / 1024), 2) AS data_mb,
            ROUND((index_length / 1024 / 1024), 2) AS index_mb
        FROM information_schema.tables 
        WHERE table_schema = 'demo'
        ORDER BY (data_length + index_length) DESC
        LIMIT 15
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    printf("%-25s %10s %12s %10s %10s\n", "Table", "Size(MB)", "Rows", "Data(MB)", "Index(MB)");
    echo str_repeat("-", 70) . "\n";
    
    $totalSize = 0;
    foreach ($tables as $table) {
        printf("%-25s %10.2f %12s %10.2f %10.2f\n", 
            $table['table_name'], 
            $table['size_mb'], 
            number_format($table['table_rows']), 
            $table['data_mb'], 
            $table['index_mb']
        );
        $totalSize += $table['size_mb'];
    }
    
    echo str_repeat("-", 70) . "\n";
    printf("%-25s %10.2f MB\n", "TOTAL DATABASE SIZE", $totalSize);
    echo "\n";

    // 2. Slow Query Analysis (simulasi)
    echo "🐌 2. ANALISIS QUERY LAMBAT\n";
    echo "---------------------------\n";
    
    // Test beberapa query yang mungkin lambat
    $slowQueries = [
        [
            'name' => 'POS Products Query',
            'query' => "
                SELECT p.*, k.nama_kategori, s.nama_satuan,
                       COALESCE(SUM(hpp.stok), 0) as total_stock
                FROM produk p
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
                LEFT JOIN hpp_produk hpp ON p.id_produk = hpp.id_produk
                WHERE p.id_outlet = 1 AND p.is_active = 1
                GROUP BY p.id_produk
                LIMIT 50
            "
        ],
        [
            'name' => 'Piutang Summary',
            'query' => "
                SELECT p.*, m.nama as member_name, o.nama_outlet
                FROM piutang p
                LEFT JOIN member m ON p.id_member = m.id_member
                LEFT JOIN outlets o ON p.id_outlet = o.id_outlet
                WHERE p.status = 'belum_lunas'
                ORDER BY p.tanggal_jatuh_tempo ASC
                LIMIT 100
            "
        ],
        [
            'name' => 'Penjualan Report',
            'query' => "
                SELECT DATE(created_at) as tanggal, 
                       COUNT(*) as total_transaksi,
                       SUM(total_harga) as total_penjualan
                FROM penjualan 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY tanggal DESC
            "
        ]
    ];
    
    foreach ($slowQueries as $queryTest) {
        $start = microtime(true);
        $stmt = $pdo->query($queryTest['query']);
        $results = $stmt->fetchAll();
        $duration = (microtime(true) - $start) * 1000; // Convert to milliseconds
        
        printf("%-20s: %6.2f ms (%d rows)\n", 
            $queryTest['name'], 
            $duration, 
            count($results)
        );
        
        if ($duration > 100) {
            echo "   ⚠️  SLOW QUERY - Consider optimization\n";
        } elseif ($duration > 50) {
            echo "   ⚡ MODERATE - Could be improved\n";
        } else {
            echo "   ✅ FAST\n";
        }
    }
    echo "\n";

    // 3. Index Analysis
    echo "📇 3. ANALISIS INDEX\n";
    echo "-------------------\n";
    
    $criticalTables = ['produk', 'penjualan', 'piutang', 'hpp_produk', 'pos_sales'];
    
    foreach ($criticalTables as $tableName) {
        $stmt = $pdo->query("SHOW INDEX FROM `$tableName`");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table: $tableName\n";
        if (empty($indexes)) {
            echo "   ❌ No indexes found!\n";
        } else {
            $indexNames = array_unique(array_column($indexes, 'Key_name'));
            foreach ($indexNames as $indexName) {
                $columns = array_filter($indexes, function($idx) use ($indexName) {
                    return $idx['Key_name'] === $indexName;
                });
                $columnNames = array_column($columns, 'Column_name');
                
                if ($indexName === 'PRIMARY') {
                    echo "   🔑 PRIMARY: " . implode(', ', $columnNames) . "\n";
                } else {
                    $unique = $columns[0]['Non_unique'] == 0 ? 'UNIQUE' : 'INDEX';
                    echo "   📇 $unique $indexName: " . implode(', ', $columnNames) . "\n";
                }
            }
        }
        echo "\n";
    }

    // 4. Connection and Configuration Analysis
    echo "⚙️  4. KONFIGURASI DATABASE\n";
    echo "--------------------------\n";
    
    $configs = [
        'innodb_buffer_pool_size',
        'max_connections',
        'query_cache_size',
        'tmp_table_size',
        'max_heap_table_size'
    ];
    
    foreach ($configs as $config) {
        try {
            $stmt = $pdo->query("SHOW VARIABLES LIKE '$config'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                printf("%-25s: %s\n", $result['Variable_name'], $result['Value']);
            }
        } catch (Exception $e) {
            printf("%-25s: Unable to retrieve\n", $config);
        }
    }
    echo "\n";

    // 5. Recommendations
    echo "💡 5. REKOMENDASI OPTIMASI\n";
    echo "-------------------------\n";
    
    $recommendations = [];
    
    // Check for large tables without proper indexes
    foreach ($tables as $table) {
        if ($table['table_rows'] > 10000 && $table['index_mb'] < 1) {
            $recommendations[] = "🔍 Table '{$table['table_name']}' has {$table['table_rows']} rows but minimal indexes";
        }
        
        if ($table['size_mb'] > 100) {
            $recommendations[] = "📊 Table '{$table['table_name']}' is large ({$table['size_mb']} MB) - consider archiving old data";
        }
    }
    
    // General recommendations
    $recommendations[] = "🚀 Implement Redis caching for frequently accessed data";
    $recommendations[] = "📱 Add database connection pooling";
    $recommendations[] = "🔄 Implement query result caching";
    $recommendations[] = "📈 Set up database monitoring and alerting";
    $recommendations[] = "🗜️  Enable MySQL query cache if not already enabled";
    $recommendations[] = "⚡ Consider using database read replicas for reporting";
    
    foreach ($recommendations as $i => $rec) {
        echo ($i + 1) . ". $rec\n";
    }
    
    echo "\n";
    
    // 6. Quick Wins
    echo "🎯 6. QUICK WINS (Implementasi Cepat)\n";
    echo "------------------------------------\n";
    
    $quickWins = [
        "Add composite index on (id_outlet, is_active) for produk table",
        "Add index on (status, tanggal_jatuh_tempo) for piutang table", 
        "Add index on (created_at) for penjualan table",
        "Implement Laravel query caching for product listings",
        "Add database query logging to identify N+1 problems",
        "Optimize image loading with lazy loading",
        "Implement browser caching for static assets",
        "Add compression for API responses"
    ];
    
    foreach ($quickWins as $i => $win) {
        echo ($i + 1) . ". $win\n";
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Performance audit completed!\n";
echo "📝 Next: Run optimization scripts based on recommendations above.\n";