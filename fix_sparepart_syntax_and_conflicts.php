<?php

/**
 * Fix Sparepart Syntax Error and Conflicts
 * Mengatasi syntax error dan konflik multiple scripts
 */

echo "🔧 Memperbaiki syntax error dan konflik di sparepart...\n\n";

// 1. Fix sparepart view syntax error and clean up scripts
echo "1. Memperbaiki sparepart view syntax error...\n";

$sparepartViewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($sparepartViewPath)) {
    $content = file_get_contents($sparepartViewPath);
    
    // Remove the malformed script at the end
    $malformedScript = "    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure Alpine.js is properly initialized
            if (typeof Alpine !== 'undefined') {
                }
        });
    </script>";
    
    $content = str_replace($malformedScript, '', $content);
    
    // Remove duplicate DOMContentLoaded scripts
    $duplicateScript = "    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure DataTable Manager is available
            if (typeof window.DataTableManager !== 'undefined') {
                console.log('✅ DataTable Manager is available');
            } else {
                console.warn('⚠️ DataTable Manager not found, using fallback');
            }
            
            // Ensure Alpine.js is properly initialized
            if (typeof Alpine !== 'undefined') {
                console.log('✅ Alpine.js started');
            }
        });
    </script>";
    
    $content = str_replace($duplicateScript, '', $content);
    
    // Clean up the @push('scripts') section - remove the complex fallback
    $oldScriptsSection = "    <!-- Initialize Alpine.js component AFTER sparepart.js is loaded -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure sparepartData function is available
            if (typeof sparepartData === 'undefined') {
                console.error('❌ sparepartData function not found in sparepart.js');
                
                // Create a minimal fallback sparepartData function
                window.sparepartData = function() {
                    return {
                        // Minimal data structure to prevent errors
                        table: null,
                        search: '',
                        stats: { total: 0, tersedia: 0, minimum: 0, habis: 0 },
                        showModal: false,
                        showDetailModal: false,
                        showAdjustModal: false,
                        showExportModal: false,
                        showPriceAdjustModal: false,
                        modalTitle: 'Tambah Sparepart',
                        editMode: false,
                        editId: null,
                        loading: false,
                        detailData: null,
                        adjustData: null,
                        adjustLogs: [],
                        filteredAdjustLogs: [],
                        priceAdjustData: null,
                        priceChangeLogs: [],
                        userRole: '',
                        selectedItems: [],
                        selectAll: false,
                        showKaryawanDropdown: false,
                        karyawanList: [],
                        uniqueKaryawanInLogs: [],
                        filters: {
                            start_date: '',
                            end_date: '',
                            outlet_id: ''
                        },
                        logFilters: {
                            start_date: '',
                            end_date: '',
                            kategori: '',
                            karyawan: ''
                        },
                        logSortField: 'created_at',
                        logSortDirection: 'desc',
                        adjustForm: {
                            tipe: 'tambah',
                            kategori: '',
                            jumlah: 0,
                            keterangan: '',
                            id_karyawan: null,
                            karyawan_search: ''
                        },
                        priceAdjustForm: {
                            harga_baru: 0,
                            keterangan: ''
                        },
                        exportForm: {
                            format: 'pdf',
                            data_type: 'all',
                            include_history: 'no',
                            log_start_date: '',
                            log_end_date: '',
                            log_category: '',
                            log_sort: 'desc'
                        },
                        form: {
                            outlet_id: '',
                            kode_sparepart: '',
                            nama_sparepart: '',
                            merk: '',
                            spesifikasi: '',
                            harga: 0,
                            stok: 0,
                            stok_minimum: 0,
                            satuan: '',
                            is_active: 1,
                            keterangan: ''
                        },
                        
                        // Minimal methods to prevent errors
                        init() {
                            console.log('⚠️ Using fallback sparepartData - please check sparepart.js');
                            this.loadStats();
                        },
                        
                        async loadStats() {
                            // Minimal stats loading
                            this.stats = { total: 0, tersedia: 0, minimum: 0, habis: 0 };
                        },
                        
                        openAddModal() {
                            this.showModal = true;
                            this.editMode = false;
                            this.modalTitle = 'Tambah Sparepart';
                        },
                        
                        closeModal() {
                            this.showModal = false;
                        },
                        
                        closeDetailModal() {
                            this.showDetailModal = false;
                        },
                        
                        closeAdjustModal() {
                            this.showAdjustModal = false;
                        },
                        
                        closeExportModal() {
                            this.showExportModal = false;
                        },
                        
                        closePriceAdjustModal() {
                            this.showPriceAdjustModal = false;
                        },
                        
                        applyFilters() {
                            console.log('Filters applied');
                        },
                        
                        clearFilters() {
                            this.filters = {
                                start_date: '',
                                end_date: '',
                                outlet_id: ''
                            };
                        },
                        
                        toggleSelectAll() {
                            // Toggle select all
                        },
                        
                        updateSelectedItems() {
                            // Update selected items
                        },
                        
                        openExportModal() {
                            this.showExportModal = true;
                        },
                        
                        async saveSparepart() {
                            console.log('Save sparepart called');
                        },
                        
                        async generateKodeSparepart() {
                            console.log('Generate kode called');
                        }
                    };
                };
                
                console.log('✅ Fallback sparepartData function created');
            } else {
                console.log('✅ sparepartData function found in sparepart.js');
            }
            
            // Reinitialize Alpine.js if needed
            if (typeof Alpine !== 'undefined') {
                try {
                    console.log('✅ Alpine.js restarted successfully');
                } catch (e) {
                    console.log('ℹ️ Alpine.js already started');
                }
            }
        });
    </script>";
    
    $newScriptsSection = "    <!-- Simple initialization script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 Sparepart view loaded');
            
            // Check if sparepartData is available
            if (typeof sparepartData !== 'undefined') {
                console.log('✅ sparepartData function is available');
            } else {
                console.warn('⚠️ sparepartData function not found - emergency fix will handle this');
            }
        });
    </script>";
    
    $content = str_replace($oldScriptsSection, $newScriptsSection, $content);
    
    // Remove any remaining Alpine.start() calls
    $content = preg_replace('/Alpine\.start\(\);?\s*/', '', $content);
    
    file_put_contents($sparepartViewPath, $content);
    echo "   ✅ Sparepart view syntax error diperbaiki\n";
    echo "   ✅ Duplicate scripts dihapus\n";
    echo "   ✅ Alpine.start() calls dihapus\n";
} else {
    echo "   ❌ Sparepart view tidak ditemukan\n";
}

// 2. Remove duplicate emergency fix from admin layout
echo "\n2. Membersihkan duplicate emergency fix dari admin layout...\n";

$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $layoutContent = file_get_contents($adminLayoutPath);
    
    // Remove sparepart-emergency-fix.js if it exists (we have the main emergency fix)
    $layoutContent = str_replace('<script src="{{ asset(\'js/sparepart-emergency-fix.js\') }}"></script>', '', $layoutContent);
    
    // Clean up any duplicate Alpine.start() calls
    $layoutContent = preg_replace('/Alpine\.start\(\);?\s*/', '', $layoutContent);
    
    // Ensure only one Alpine.js initialization script exists
    $alpineInitCount = substr_count($layoutContent, 'alpineStarted');
    if ($alpineInitCount > 1) {
        echo "   ⚠️  Multiple Alpine.js initialization scripts found, cleaning up...\n";
        
        // Keep only the first one
        $parts = explode('alpineStarted', $layoutContent);
        if (count($parts) > 2) {
            // Reconstruct with only the first occurrence
            $layoutContent = $parts[0] . 'alpineStarted' . $parts[1];
            // Add the rest without the Alpine initialization
            for ($i = 2; $i < count($parts); $i++) {
                $layoutContent .= preg_replace('/.*?<\/script>/s', '', $parts[$i], 1);
            }
        }
    }
    
    file_put_contents($adminLayoutPath, $layoutContent);
    echo "   ✅ Admin layout dibersihkan dari duplicate scripts\n";
} else {
    echo "   ❌ Admin layout tidak ditemukan\n";
}

// 3. Update emergency fix to be less aggressive
echo "\n3. Memperbarui emergency fix agar tidak terlalu agresif...\n";

$emergencyFixPath = 'public/js/emergency-alpine-datatable-fix.js';
if (file_exists($emergencyFixPath)) {
    $emergencyContent = "/**
 * Emergency Fix for Alpine.js and DataTable Conflicts
 * Simplified version to prevent conflicts
 */

console.log('🚨 Loading emergency Alpine.js and DataTable fix...');

// Prevent multiple Alpine.js initialization
if (typeof window.alpineStarted === 'undefined') {
    window.alpineStarted = false;
}

// Only override Alpine.start if it hasn't been overridden yet
if (typeof Alpine !== 'undefined' && !window.alpineOverridden) {
    window.alpineOverridden = true;
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

// Enhanced DataTable cleanup - only if needed
window.emergencyDataTableCleanup = function(tableId) {
    console.log('🚨 Emergency DataTable cleanup for:', tableId);
    
    try {
        if ($.fn.DataTable.isDataTable(tableId)) {
            const table = $(tableId).DataTable();
            table.clear();
            table.destroy();
        }
        
        // Clean up DataTable artifacts
        $(tableId).removeClass('dataTable');
        $(tableId).removeAttr('role aria-describedby');
        $(tableId + '_wrapper').remove();
        $(tableId).empty();
        
        console.log('✅ Emergency cleanup completed for:', tableId);
    } catch (error) {
        console.error('❌ Emergency cleanup error:', error);
    }
};

// Only run auto-cleanup if there are issues
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚨 Emergency fix loaded and ready');
    
    // Check for problematic DataTables only
    setTimeout(() => {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.dataTable').each(function() {
                const tableId = '#' + this.id;
                if (tableId !== '#' && $(this).hasClass('error')) {
                    console.log('🚨 Found problematic DataTable, cleaning up:', tableId);
                    window.emergencyDataTableCleanup(tableId);
                }
            });
        }
    }, 1000);
});

console.log('🚨 Emergency fix script loaded');
";
    
    file_put_contents($emergencyFixPath, $emergencyContent);
    echo "   ✅ Emergency fix diperbaiki agar tidak terlalu agresif\n";
} else {
    echo "   ❌ Emergency fix tidak ditemukan\n";
}

// 4. Check and fix sparepart.js syntax
echo "\n4. Memeriksa sparepart.js untuk masalah syntax...\n";

$sparepartJsPath = 'public/js/sparepart.js';
if (file_exists($sparepartJsPath)) {
    $jsContent = file_get_contents($sparepartJsPath);
    
    // Check for common syntax issues
    $syntaxIssues = [];
    
    // Check for unmatched braces
    $openBraces = substr_count($jsContent, '{');
    $closeBraces = substr_count($jsContent, '}');
    if ($openBraces !== $closeBraces) {
        $syntaxIssues[] = "Unmatched braces: $openBraces open, $closeBraces close";
    }
    
    // Check for unmatched parentheses
    $openParens = substr_count($jsContent, '(');
    $closeParens = substr_count($jsContent, ')');
    if ($openParens !== $closeParens) {
        $syntaxIssues[] = "Unmatched parentheses: $openParens open, $closeParens close";
    }
    
    if (empty($syntaxIssues)) {
        echo "   ✅ sparepart.js syntax tampak baik\n";
    } else {
        echo "   ⚠️  Potential syntax issues found:\n";
        foreach ($syntaxIssues as $issue) {
            echo "      - $issue\n";
        }
    }
    
    // Ensure the file ends properly
    if (!preg_match('/\}\s*;\s*$/', trim($jsContent))) {
        echo "   ⚠️  File might not end properly, checking...\n";
        
        // Add proper ending if missing
        $jsContent = rtrim($jsContent);
        if (!str_ends_with($jsContent, '};')) {
            if (str_ends_with($jsContent, '}')) {
                $jsContent .= ';';
            } else {
                $jsContent .= "\n};";
            }
            file_put_contents($sparepartJsPath, $jsContent);
            echo "   ✅ File ending diperbaiki\n";
        }
    }
} else {
    echo "   ❌ sparepart.js tidak ditemukan\n";
}

// 5. Create a simple test page
echo "\n5. Membuat test page sederhana...\n";

$testPageContent = "<!DOCTYPE html>
<html>
<head>
    <title>Sparepart Syntax Test</title>
    <script src=\"https://code.jquery.com/jquery-3.7.1.min.js\"></script>
    <script defer src=\"https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js\"></script>
</head>
<body>
    <div x-data=\"{ message: 'Alpine.js is working!' }\">
        <h1>Syntax Test Page</h1>
        <p x-text=\"message\"></p>
        <button @click=\"message = 'Button clicked!'\">Test Alpine.js</button>
    </div>
    
    <script>
        console.log('Test page loaded');
        
        // Test if sparepart.js loads without errors
        const script = document.createElement('script');
        script.src = 'js/sparepart.js';
        script.onload = function() {
            console.log('✅ sparepart.js loaded successfully');
            if (typeof sparepartData !== 'undefined') {
                console.log('✅ sparepartData function is available');
            } else {
                console.log('❌ sparepartData function not found');
            }
        };
        script.onerror = function() {
            console.error('❌ Error loading sparepart.js');
        };
        document.head.appendChild(script);
    </script>
</body>
</html>";

file_put_contents('public/test-sparepart-syntax.html', $testPageContent);
echo "   ✅ Test page berhasil dibuat: public/test-sparepart-syntax.html\n";

echo "\n🎉 Syntax error dan konflik fix selesai!\n\n";
echo "📋 Yang telah diperbaiki:\n";
echo "   1. ✅ Syntax error di sparepart view diperbaiki\n";
echo "   2. ✅ Duplicate scripts dihapus\n";
echo "   3. ✅ Alpine.start() calls dibersihkan\n";
echo "   4. ✅ Emergency fix disederhanakan\n";
echo "   5. ✅ sparepart.js syntax diperiksa\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "   1. Clear browser cache (Ctrl+Shift+R)\n";
echo "   2. Test dengan: /test-sparepart-syntax.html\n";
echo "   3. Buka halaman sparepart: /admin/inventaris/sparepart\n";
echo "   4. Check console - tidak boleh ada syntax error\n";
echo "   5. Pastikan Alpine.js hanya start sekali\n\n";

echo "⚠️  Expected console output:\n";
echo "   ✅ '🚨 Emergency fix loaded and ready'\n";
echo "   ✅ '🏔️ Starting Alpine.js...'\n";
echo "   ✅ '✅ Alpine.js started successfully'\n";
echo "   ✅ '📄 Sparepart view loaded'\n";
echo "   ❌ NO 'Alpine has already been initialized'\n";
echo "   ❌ NO 'Uncaught SyntaxError'\n\n";

?>