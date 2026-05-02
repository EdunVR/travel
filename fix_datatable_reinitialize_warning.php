<?php

/**
 * Fix DataTables Reinitialization Warning
 * Mengatasi warning: Cannot reinitialise DataTable
 */

echo "🔧 Memperbaiki DataTables reinitialization warning...\n\n";

// 1. Fix sparepart.js DataTable initialization
echo "1. Memperbaiki inisialisasi DataTable di sparepart.js...\n";

$sparepartJsPath = 'public/js/sparepart.js';
if (file_exists($sparepartJsPath)) {
    $content = file_get_contents($sparepartJsPath);
    
    // Find the initDataTable function and improve it
    $oldInitFunction = 'initDataTable() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                $("#sparepart-table").DataTable().destroy();
            }';
    
    $newInitFunction = 'initDataTable() {
            // Properly destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                const existingTable = $("#sparepart-table").DataTable();
                existingTable.clear();
                existingTable.destroy();
                $("#sparepart-table").empty();
            }
            
            // Clear any existing table HTML structure
            $("#sparepart-table").html(`
                <thead>
                    <tr>
                        <th class="text-center">
                            <input type="checkbox" class="rounded border-slate-300">
                        </th>
                        <th class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Sparepart</th>
                        <th>Merk</th>
                        <th class="text-right">Harga</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">Status Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);';
    
    if (strpos($content, $oldInitFunction) !== false) {
        $content = str_replace($oldInitFunction, $newInitFunction, $content);
        echo "   ✅ DataTable initialization function diperbaiki\n";
    } else {
        // If exact match not found, look for the function and replace it
        $pattern = '/initDataTable\(\)\s*\{[^}]*if\s*\(\$\.fn\.DataTable\.isDataTable[^}]*\}/s';
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, 'initDataTable() {
            // Properly destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                const existingTable = $("#sparepart-table").DataTable();
                existingTable.clear();
                existingTable.destroy();
                $("#sparepart-table").empty();
            }
            
            // Clear any existing table HTML structure
            $("#sparepart-table").html(`
                <thead>
                    <tr>
                        <th class="text-center">
                            <input type="checkbox" class="rounded border-slate-300">
                        </th>
                        <th class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Sparepart</th>
                        <th>Merk</th>
                        <th class="text-right">Harga</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">Status Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);', $content);
            echo "   ✅ DataTable initialization function diperbaiki (pattern match)\n";
        } else {
            echo "   ⚠️  Tidak dapat menemukan initDataTable function untuk diperbaiki\n";
        }
    }
    
    // Also add a global DataTable cleanup function
    $globalCleanupFunction = "
// Global DataTable cleanup function
window.cleanupDataTable = function(tableId) {
    if ($.fn.DataTable.isDataTable(tableId)) {
        const table = $(tableId).DataTable();
        table.clear();
        table.destroy();
        $(tableId).empty();
    }
};

// Enhanced DataTable initialization with proper cleanup
window.initDataTableSafe = function(tableId, options) {
    // Cleanup first
    window.cleanupDataTable(tableId);
    
    // Wait a bit for cleanup to complete
    setTimeout(() => {
        return $(tableId).DataTable(options);
    }, 100);
};

";
    
    // Add the global functions at the beginning of the file
    if (strpos($content, 'window.cleanupDataTable') === false) {
        $content = $globalCleanupFunction . $content;
        echo "   ✅ Global DataTable cleanup functions ditambahkan\n";
    }
    
    file_put_contents($sparepartJsPath, $content);
    echo "   ✅ sparepart.js berhasil diperbaiki\n";
} else {
    echo "   ❌ File sparepart.js tidak ditemukan\n";
}

// 2. Create a general DataTable helper
echo "\n2. Membuat DataTable helper umum...\n";

$datatableHelperContent = "/**
 * DataTable Helper Functions
 * Mengatasi masalah reinitialization dan cleanup
 */

// Global DataTable management
window.DataTableManager = {
    tables: new Map(),
    
    // Initialize DataTable with proper cleanup
    init: function(tableId, options = {}) {
        console.log('Initializing DataTable for:', tableId);
        
        // Cleanup existing table
        this.destroy(tableId);
        
        // Default options
        const defaultOptions = {
            processing: true,
            serverSide: true,
            responsive: true,
            language: {
                processing: 'Memuat...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(disaring dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang ditemukan',
                emptyTable: 'Tidak ada data tersedia',
                paginate: {
                    first: 'Pertama',
                    previous: 'Sebelumnya',
                    next: 'Selanjutnya',
                    last: 'Terakhir'
                }
            },
            dom: '<\"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3\"lf>rt<\"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3\"ip>'
        };
        
        // Merge options
        const finalOptions = Object.assign({}, defaultOptions, options);
        
        try {
            // Initialize DataTable
            const table = $(tableId).DataTable(finalOptions);
            
            // Store reference
            this.tables.set(tableId, table);
            
            console.log('✅ DataTable initialized successfully for:', tableId);
            return table;
        } catch (error) {
            console.error('❌ Error initializing DataTable for:', tableId, error);
            return null;
        }
    },
    
    // Destroy DataTable properly
    destroy: function(tableId) {
        try {
            if ($.fn.DataTable.isDataTable(tableId)) {
                console.log('Destroying existing DataTable for:', tableId);
                
                const table = $(tableId).DataTable();
                table.clear();
                table.destroy();
                
                // Remove from our tracking
                this.tables.delete(tableId);
                
                // Clear the table HTML
                $(tableId).empty();
                
                console.log('✅ DataTable destroyed successfully for:', tableId);
            }
        } catch (error) {
            console.error('❌ Error destroying DataTable for:', tableId, error);
        }
    },
    
    // Get DataTable instance
    get: function(tableId) {
        return this.tables.get(tableId);
    },
    
    // Reload DataTable data
    reload: function(tableId, resetPaging = false) {
        const table = this.get(tableId);
        if (table) {
            table.ajax.reload(null, resetPaging);
        }
    },
    
    // Clear all DataTables
    destroyAll: function() {
        console.log('Destroying all DataTables...');
        this.tables.forEach((table, tableId) => {
            this.destroy(tableId);
        });
        this.tables.clear();
    }
};

// Auto cleanup on page unload
window.addEventListener('beforeunload', function() {
    window.DataTableManager.destroyAll();
});

// jQuery ready function to ensure proper initialization
$(document).ready(function() {
    console.log('✅ DataTable Manager initialized');
});

console.log('📊 DataTable Helper loaded successfully');
";

file_put_contents('public/js/datatable-helper.js', $datatableHelperContent);
echo "   ✅ DataTable helper berhasil dibuat\n";

// 3. Update admin layout to include DataTable helper
echo "\n3. Menambahkan DataTable helper ke admin layout...\n";

$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $layoutContent = file_get_contents($adminLayoutPath);
    
    // Add DataTable helper before other scripts
    if (strpos($layoutContent, 'datatable-helper.js') === false) {
        $layoutContent = str_replace(
            '<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>',
            '<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>' . "\n    " . '<script src="{{ asset(\'js/datatable-helper.js\') }}"></script>',
            $layoutContent
        );
        
        file_put_contents($adminLayoutPath, $layoutContent);
        echo "   ✅ DataTable helper ditambahkan ke admin layout\n";
    } else {
        echo "   ✅ DataTable helper sudah ada di admin layout\n";
    }
} else {
    echo "   ❌ Admin layout tidak ditemukan\n";
}

// 4. Update sparepart view to use new DataTable manager
echo "\n4. Memperbarui sparepart view untuk menggunakan DataTable manager...\n";

$sparepartViewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($sparepartViewPath)) {
    $viewContent = file_get_contents($sparepartViewPath);
    
    // Add initialization script at the end
    $initScript = "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure DataTable Manager is available
            if (typeof window.DataTableManager !== 'undefined') {
                console.log('✅ DataTable Manager is available');
            } else {
                console.warn('⚠️ DataTable Manager not found, using fallback');
            }
            
            // Ensure Alpine.js is properly initialized
            if (typeof Alpine !== 'undefined') {
                Alpine.start();
                console.log('✅ Alpine.js started');
            }
        });
    </script>";
    
    // Add the script before the closing </x-layouts.admin> tag
    if (strpos($viewContent, 'DataTable Manager is available') === false) {
        $viewContent = str_replace('</x-layouts.admin>', $initScript . "\n</x-layouts.admin>", $viewContent);
        file_put_contents($sparepartViewPath, $viewContent);
        echo "   ✅ Sparepart view berhasil diperbarui\n";
    } else {
        echo "   ✅ Sparepart view sudah memiliki DataTable manager initialization\n";
    }
} else {
    echo "   ❌ Sparepart view tidak ditemukan\n";
}

// 5. Create a test script for DataTable functionality
echo "\n5. Membuat test script untuk DataTable...\n";

$testScript = "
<!DOCTYPE html>
<html>
<head>
    <title>DataTable Test</title>
    <script src=\"https://code.jquery.com/jquery-3.7.1.min.js\"></script>
    <link rel=\"stylesheet\" href=\"https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css\">
    <script src=\"https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js\"></script>
    <script src=\"datatable-helper.js\"></script>
</head>
<body>
    <h1>DataTable Reinitialization Test</h1>
    
    <button onclick=\"initTable()\">Initialize Table</button>
    <button onclick=\"destroyTable()\">Destroy Table</button>
    <button onclick=\"reinitTable()\">Reinitialize Table</button>
    
    <table id=\"test-table\" class=\"display\">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tiger Nixon</td>
                <td>System Architect</td>
                <td>Edinburgh</td>
                <td>61</td>
                <td>2011/04/25</td>
                <td>\$320,800</td>
            </tr>
        </tbody>
    </table>
    
    <script>
        function initTable() {
            console.log('Initializing table...');
            window.DataTableManager.init('#test-table', {
                serverSide: false,
                data: [
                    ['Tiger Nixon', 'System Architect', 'Edinburgh', '61', '2011/04/25', '\$320,800'],
                    ['Garrett Winters', 'Accountant', 'Tokyo', '63', '2011/07/25', '\$170,750']
                ]
            });
        }
        
        function destroyTable() {
            console.log('Destroying table...');
            window.DataTableManager.destroy('#test-table');
        }
        
        function reinitTable() {
            console.log('Reinitializing table...');
            destroyTable();
            setTimeout(initTable, 200);
        }
        
        // Auto test
        $(document).ready(function() {
            console.log('Page ready, testing DataTable...');
            initTable();
        });
    </script>
</body>
</html>
";

file_put_contents('public/test-datatable-reinit.html', $testScript);
echo "   ✅ Test script berhasil dibuat: public/test-datatable-reinit.html\n";

echo "\n🎉 DataTables reinitialization warning fix selesai!\n\n";
echo "📋 Yang telah diperbaiki:\n";
echo "   1. ✅ Improved DataTable destruction in sparepart.js\n";
echo "   2. ✅ Created DataTable helper with proper cleanup\n";
echo "   3. ✅ Added global DataTable management system\n";
echo "   4. ✅ Enhanced error handling for DataTable operations\n";
echo "   5. ✅ Created test page for verification\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "   1. Clear browser cache (Ctrl+Shift+R)\n";
echo "   2. Buka halaman sparepart dan check console\n";
echo "   3. Test dengan: /test-datatable-reinit.html\n";
echo "   4. Pastikan tidak ada warning 'Cannot reinitialise DataTable'\n\n";

echo "⚠️  Jika masih ada warning:\n";
echo "   1. Check browser console untuk error details\n";
echo "   2. Pastikan jQuery dan DataTables dimuat dengan benar\n";
echo "   3. Test dengan halaman test yang disediakan\n\n";

?>