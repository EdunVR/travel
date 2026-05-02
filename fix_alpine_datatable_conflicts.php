<?php

/**
 * Fix Alpine.js and DataTable Conflicts
 * Mengatasi masalah Alpine.js double initialization dan DataTable reinitialization
 */

echo "🔧 Memperbaiki konflik Alpine.js dan DataTable...\n\n";

// 1. Fix Alpine.js multiple initialization
echo "1. Memperbaiki Alpine.js multiple initialization...\n";

// Check admin layout for Alpine.js initialization issues
$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $layoutContent = file_get_contents($adminLayoutPath);
    
    // Remove any Alpine.start() calls from the layout
    $layoutContent = preg_replace('/Alpine\.start\(\);?\s*/', '', $layoutContent);
    
    // Remove any duplicate Alpine.js initialization
    $layoutContent = preg_replace('/document\.addEventListener\([\'"]alpine:init[\'"].*?\}\);?\s*/s', '', $layoutContent);
    
    // Add proper Alpine.js initialization at the end
    $alpineInitScript = "
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Alpine.js is only started once
    if (typeof Alpine !== 'undefined' && !window.alpineStarted) {
        console.log('🏔️ Starting Alpine.js...');
        Alpine.start();
        window.alpineStarted = true;
        console.log('✅ Alpine.js started successfully');
    } else if (window.alpineStarted) {
        console.log('ℹ️ Alpine.js already started, skipping...');
    } else {
        console.warn('⚠️ Alpine.js not found');
    }
});
</script>";
    
    // Add the script before closing body tag
    if (strpos($layoutContent, 'alpineStarted') === false) {
        $layoutContent = str_replace('</body>', $alpineInitScript . "\n</body>", $layoutContent);
        echo "   ✅ Alpine.js initialization script ditambahkan\n";
    } else {
        echo "   ✅ Alpine.js initialization script sudah ada\n";
    }
    
    file_put_contents($adminLayoutPath, $layoutContent);
} else {
    echo "   ❌ Admin layout tidak ditemukan\n";
}

// 2. Fix sparepart view Alpine.js calls
echo "\n2. Memperbaiki sparepart view Alpine.js calls...\n";

$sparepartViewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($sparepartViewPath)) {
    $viewContent = file_get_contents($sparepartViewPath);
    
    // Remove any Alpine.start() calls from the view
    $viewContent = preg_replace('/Alpine\.start\(\);?\s*/', '', $viewContent);
    
    // Update the DOMContentLoaded script to not start Alpine
    $oldScript = "document.addEventListener('DOMContentLoaded', function() {
            // Ensure Alpine.js is properly initialized
            if (typeof Alpine !== 'undefined') {
                Alpine.start();
            }
        });";
    
    $newScript = "document.addEventListener('DOMContentLoaded', function() {
            // Alpine.js will be started by the main layout
            console.log('📄 Sparepart view loaded');
            
            // Ensure DataTable Manager is available
            if (typeof window.DataTableManager !== 'undefined') {
                console.log('✅ DataTable Manager is available');
            } else {
                console.warn('⚠️ DataTable Manager not found');
            }
        });";
    
    if (strpos($viewContent, $oldScript) !== false) {
        $viewContent = str_replace($oldScript, $newScript, $viewContent);
        echo "   ✅ Alpine.js start call dihapus dari sparepart view\n";
    } else {
        // Look for any Alpine.start() and remove it
        $viewContent = preg_replace('/Alpine\.start\(\);?\s*/', '', $viewContent);
        echo "   ✅ Alpine.js calls dibersihkan dari sparepart view\n";
    }
    
    file_put_contents($sparepartViewPath, $viewContent);
} else {
    echo "   ❌ Sparepart view tidak ditemukan\n";
}

// 3. Fix DataTable initialization in sparepart.js
echo "\n3. Memperbaiki DataTable initialization di sparepart.js...\n";

$sparepartJsPath = 'public/js/sparepart.js';
if (file_exists($sparepartJsPath)) {
    $jsContent = file_get_contents($sparepartJsPath);
    
    // Update the initDataTable function with better cleanup
    $oldInitFunction = 'initDataTable() {
            console.log(\'Initializing sparepart DataTable...\');
            
            // Use DataTable Manager if available, otherwise fallback to manual cleanup
            if (typeof window.DataTableManager !== \'undefined\') {
                console.log(\'Using DataTable Manager for initialization\');
                this.table = window.DataTableManager.init("#sparepart-table", this.getDataTableOptions());
                return;
            }
            
            // Fallback: Manual cleanup
            console.log(\'Using manual DataTable cleanup\');
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                const existingTable = $("#sparepart-table").DataTable();
                existingTable.clear();
                existingTable.destroy();
                $("#sparepart-table").empty();
            }';
    
    $newInitFunction = 'initDataTable() {
            console.log(\'🔄 Initializing sparepart DataTable...\');
            
            // Always do thorough cleanup first
            this.destroyExistingTable();
            
            // Wait a moment for cleanup to complete
            setTimeout(() => {
                try {
                    // Use DataTable Manager if available
                    if (typeof window.DataTableManager !== \'undefined\') {
                        console.log(\'📊 Using DataTable Manager for initialization\');
                        this.table = window.DataTableManager.init("#sparepart-table", this.getDataTableOptions());
                    } else {
                        console.log(\'📊 Using direct DataTable initialization\');
                        this.table = $("#sparepart-table").DataTable(this.getDataTableOptions());
                    }
                    
                    if (this.table) {
                        console.log(\'✅ DataTable initialized successfully\');
                    } else {
                        console.error(\'❌ DataTable initialization failed\');
                    }
                } catch (error) {
                    console.error(\'❌ DataTable initialization error:\', error);
                }
            }, 100);
        },
        
        destroyExistingTable() {
            try {
                if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                    console.log(\'🗑️ Destroying existing DataTable...\');
                    const existingTable = $("#sparepart-table").DataTable();
                    existingTable.clear();
                    existingTable.destroy();
                    $("#sparepart-table").empty();
                    console.log(\'✅ Existing DataTable destroyed\');
                }
                
                // Restore table structure
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
                `);
            } catch (error) {
                console.error(\'❌ Error destroying existing table:\', error);
            }';
    
    if (strpos($jsContent, $oldInitFunction) !== false) {
        $jsContent = str_replace($oldInitFunction, $newInitFunction, $jsContent);
        echo "   ✅ DataTable initialization function diperbaiki\n";
    } else {
        echo "   ⚠️  Tidak dapat menemukan exact function, mencoba pattern replacement...\n";
        
        // Try pattern-based replacement
        $pattern = '/initDataTable\(\)\s*\{[^}]*console\.log\([^}]*\}/s';
        if (preg_match($pattern, $jsContent)) {
            $jsContent = preg_replace($pattern, 'initDataTable() {
            console.log(\'🔄 Initializing sparepart DataTable...\');
            
            // Always do thorough cleanup first
            this.destroyExistingTable();
            
            // Wait a moment for cleanup to complete
            setTimeout(() => {
                try {
                    // Use DataTable Manager if available
                    if (typeof window.DataTableManager !== \'undefined\') {
                        console.log(\'📊 Using DataTable Manager for initialization\');
                        this.table = window.DataTableManager.init("#sparepart-table", this.getDataTableOptions());
                    } else {
                        console.log(\'📊 Using direct DataTable initialization\');
                        this.table = $("#sparepart-table").DataTable(this.getDataTableOptions());
                    }
                    
                    if (this.table) {
                        console.log(\'✅ DataTable initialized successfully\');
                    } else {
                        console.error(\'❌ DataTable initialization failed\');
                    }
                } catch (error) {
                    console.error(\'❌ DataTable initialization error:\', error);
                }
            }, 100);
        },
        
        destroyExistingTable() {
            try {
                if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                    console.log(\'🗑️ Destroying existing DataTable...\');
                    const existingTable = $("#sparepart-table").DataTable();
                    existingTable.clear();
                    existingTable.destroy();
                    $("#sparepart-table").empty();
                    console.log(\'✅ Existing DataTable destroyed\');
                }
                
                // Restore table structure
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
                `);
            } catch (error) {
                console.error(\'❌ Error destroying existing table:\', error);
            }', $jsContent);
            echo "   ✅ DataTable function diperbaiki dengan pattern matching\n";
        } else {
            echo "   ⚠️  Pattern tidak ditemukan, menambahkan function baru...\n";
            
            // Add the new functions at the beginning of sparepartData function
            $addFunction = "
        destroyExistingTable() {
            try {
                if ($.fn.DataTable.isDataTable(\"#sparepart-table\")) {
                    console.log('🗑️ Destroying existing DataTable...');
                    const existingTable = $(\"#sparepart-table\").DataTable();
                    existingTable.clear();
                    existingTable.destroy();
                    $(\"#sparepart-table\").empty();
                    console.log('✅ Existing DataTable destroyed');
                }
                
                // Restore table structure
                $(\"#sparepart-table\").html(`
                    <thead>
                        <tr>
                            <th class=\"text-center\">
                                <input type=\"checkbox\" class=\"rounded border-slate-300\">
                            </th>
                            <th class=\"text-center\">No</th>
                            <th>Kode</th>
                            <th>Nama Sparepart</th>
                            <th>Merk</th>
                            <th class=\"text-right\">Harga</th>
                            <th class=\"text-center\">Stok</th>
                            <th class=\"text-center\">Min</th>
                            <th class=\"text-center\">Status Stok</th>
                            <th class=\"text-center\">Status</th>
                            <th class=\"text-center\">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `);
            } catch (error) {
                console.error('❌ Error destroying existing table:', error);
            }
        },
";
            
            // Add after the form object
            $jsContent = str_replace('        },

        async init() {', '        },' . $addFunction . '
        async init() {', $jsContent);
            echo "   ✅ destroyExistingTable function ditambahkan\n";
        }
    }
    
    file_put_contents($sparepartJsPath, $jsContent);
} else {
    echo "   ❌ sparepart.js tidak ditemukan\n";
}

// 4. Update DataTable helper to be more robust
echo "\n4. Memperbarui DataTable helper...\n";

$datatableHelperPath = 'public/js/datatable-helper.js';
if (file_exists($datatableHelperPath)) {
    $helperContent = file_get_contents($datatableHelperPath);
    
    // Add more robust initialization
    $robustInit = "
    // Initialize DataTable with proper cleanup and error handling
    init: function(tableId, options = {}) {
        console.log('📊 DataTableManager: Initializing table:', tableId);
        
        // Force cleanup first
        this.destroy(tableId);
        
        // Wait for cleanup to complete
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                try {
                    // Ensure table element exists
                    if ($(tableId).length === 0) {
                        console.error('❌ Table element not found:', tableId);
                        reject(new Error('Table element not found'));
                        return;
                    }
                    
                    // Default options
                    const defaultOptions = {
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true, // Allow reinitialization
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
                    
                    // Initialize DataTable
                    const table = $(tableId).DataTable(finalOptions);
                    
                    // Store reference
                    this.tables.set(tableId, table);
                    
                    console.log('✅ DataTable initialized successfully for:', tableId);
                    resolve(table);
                } catch (error) {
                    console.error('❌ Error initializing DataTable for:', tableId, error);
                    reject(error);
                }
            }, 150); // Increased timeout for better reliability
        });
    },";
    
    // Replace the init function
    $helperContent = preg_replace('/init:\s*function\([^}]*\{[^}]*\}/s', $robustInit, $helperContent);
    
    file_put_contents($datatableHelperPath, $helperContent);
    echo "   ✅ DataTable helper diperbaiki dengan error handling yang lebih baik\n";
} else {
    echo "   ❌ DataTable helper tidak ditemukan\n";
}

// 5. Create emergency fix script
echo "\n5. Membuat emergency fix script...\n";

$emergencyFixContent = "/**
 * Emergency Fix for Alpine.js and DataTable Conflicts
 * Load this script if there are still conflicts
 */

console.log('🚨 Loading emergency Alpine.js and DataTable fix...');

// Prevent multiple Alpine.js initialization
if (typeof window.alpineStarted === 'undefined') {
    window.alpineStarted = false;
}

// Override Alpine.start to prevent multiple calls
if (typeof Alpine !== 'undefined' && !window.alpineOriginalStart) {
    window.alpineOriginalStart = Alpine.start;
    Alpine.start = function() {
        if (!window.alpineStarted) {
            console.log('🏔️ Starting Alpine.js (emergency override)...');
            window.alpineOriginalStart.call(Alpine);
            window.alpineStarted = true;
            console.log('✅ Alpine.js started successfully');
        } else {
            console.log('ℹ️ Alpine.js already started, ignoring duplicate call');
        }
    };
}

// Enhanced DataTable cleanup
window.emergencyDataTableCleanup = function(tableId) {
    console.log('🚨 Emergency DataTable cleanup for:', tableId);
    
    try {
        // Multiple cleanup attempts
        if ($.fn.DataTable.isDataTable(tableId)) {
            const table = $(tableId).DataTable();
            table.clear();
            table.destroy();
        }
        
        // Force remove DataTable classes and data
        $(tableId).removeClass('dataTable');
        $(tableId).removeAttr('role');
        $(tableId).removeAttr('aria-describedby');
        $(tableId).find('*').removeAttr('role');
        
        // Clear wrapper
        $(tableId + '_wrapper').remove();
        
        // Reset table HTML
        $(tableId).empty();
        
        console.log('✅ Emergency cleanup completed for:', tableId);
    } catch (error) {
        console.error('❌ Emergency cleanup error:', error);
    }
};

// Auto-fix on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚨 Emergency fix loaded and ready');
    
    // Clean up any existing DataTables
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('.dataTable').each(function() {
            const tableId = '#' + this.id;
            if (tableId !== '#') {
                window.emergencyDataTableCleanup(tableId);
            }
        });
    }
});

console.log('🚨 Emergency fix script loaded');
";

file_put_contents('public/js/emergency-alpine-datatable-fix.js', $emergencyFixContent);
echo "   ✅ Emergency fix script berhasil dibuat\n";

// 6. Update admin layout to include emergency fix
echo "\n6. Menambahkan emergency fix ke admin layout...\n";

if (file_exists($adminLayoutPath)) {
    $layoutContent = file_get_contents($adminLayoutPath);
    
    if (strpos($layoutContent, 'emergency-alpine-datatable-fix.js') === false) {
        // Add emergency fix before Alpine.js
        $layoutContent = str_replace(
            '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>',
            '<script src="{{ asset(\'js/emergency-alpine-datatable-fix.js\') }}"></script>' . "\n    " . '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>',
            $layoutContent
        );
        
        file_put_contents($adminLayoutPath, $layoutContent);
        echo "   ✅ Emergency fix script ditambahkan ke admin layout\n";
    } else {
        echo "   ✅ Emergency fix script sudah ada di admin layout\n";
    }
}

echo "\n🎉 Alpine.js dan DataTable conflict fix selesai!\n\n";
echo "📋 Yang telah diperbaiki:\n";
echo "   1. ✅ Alpine.js multiple initialization dicegah\n";
echo "   2. ✅ DataTable reinitialization diperbaiki dengan cleanup yang lebih baik\n";
echo "   3. ✅ Error handling ditingkatkan\n";
echo "   4. ✅ Emergency fix script dibuat untuk fallback\n";
echo "   5. ✅ Timing issues diatasi dengan setTimeout\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "   1. Clear browser cache (Ctrl+Shift+R)\n";
echo "   2. Buka halaman sparepart\n";
echo "   3. Check browser console:\n";
echo "      - Tidak boleh ada 'Alpine has already been initialized'\n";
echo "      - Tidak boleh ada 'Cannot reinitialise DataTable'\n";
echo "      - Harus ada pesan '✅ Alpine.js started successfully'\n";
echo "      - Harus ada pesan '✅ DataTable initialized successfully'\n\n";

echo "⚠️  Jika masih ada masalah:\n";
echo "   1. Check console untuk error details\n";
echo "   2. Emergency fix akan otomatis menangani konflik\n";
echo "   3. Pastikan semua script dimuat dengan urutan yang benar\n\n";

?>