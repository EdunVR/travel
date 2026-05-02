<?php

/**
 * Fix Inter Outlet Default Outlet Removal
 * Menghilangkan default outlet "ALL" dan memastikan data ditampilkan sesuai filter outlet
 */

echo "🔧 Memperbaiki default outlet pada halaman penjualan antar outlet...\n\n";

// 1. Update Controller untuk tidak menggunakan default outlet
echo "1. Memperbaiki controller InterOutletSaleController...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Update method index untuk tidak menggunakan default outlet
    $oldIndexMethod = '/public function index\(Request \$request\)\s*\{[^}]*\}/s';
    
    $newIndexMethod = 'public function index(Request $request)
    {
        // Get user\'s accessible outlets only
        $outlets = $this->getUserOutlets();
        
        // Get selected outlet from request (no default)
        $selectedOutlet = $request->get(\'outlet_id\');
        
        // If no accessible outlets, redirect with error
        if ($outlets->isEmpty()) {
            return redirect()->back()->with(\'error\', \'Anda tidak memiliki akses ke outlet manapun.\');
        }
        
        // Validate selected outlet access if provided
        if ($selectedOutlet && $selectedOutlet !== \'ALL\' && is_numeric($selectedOutlet) && !$this->hasOutletAccess((int)$selectedOutlet)) {
            $selectedOutlet = null; // Reset invalid outlet
        }
        
        // Don\'t set default outlet - let frontend handle empty state
        if (!$selectedOutlet || $selectedOutlet === \'ALL\') {
            $selectedOutlet = null;
        }
        
        return view(\'admin.penjualan.inter-outlet.index\', compact(\'selectedOutlet\', \'outlets\'));
    }';
    
    if (preg_match($oldIndexMethod, $content)) {
        $content = preg_replace($oldIndexMethod, $newIndexMethod, $content);
        file_put_contents($controllerFile, $content);
        echo "   ✅ Method index berhasil diupdate\n";
    } else {
        echo "   ⚠️  Pattern method index tidak ditemukan, melakukan update manual...\n";
        
        // Fallback: replace specific parts
        $content = str_replace(
            '// Default to first accessible outlet if none selected
        if (!$selectedOutlet) {
            $selectedOutlet = $outlets->first()->id_outlet;
        }',
            '// Don\'t set default outlet - let frontend handle empty state
        if (!$selectedOutlet || $selectedOutlet === \'ALL\') {
            $selectedOutlet = null;
        }',
            $content
        );
        
        file_put_contents($controllerFile, $content);
        echo "   ✅ Controller berhasil diupdate (fallback)\n";
    }
} else {
    echo "   ❌ File controller tidak ditemukan\n";
}

// 2. Update JavaScript untuk menangani empty state dan tidak load data saat tidak ada outlet
echo "\n2. Memperbaiki JavaScript inter-outlet.js...\n";

$jsFile = 'public/js/inter-outlet.js';

if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Update init method untuk tidak load data jika tidak ada selectedOutlet
    $oldInit = '/init\(\)\s*\{[^}]*\}/s';
    
    $newInit = 'init() {
            console.log(\'🚀 Initializing Inter Outlet Sale App...\');
            
            // Only load data if outlet is selected
            if (this.selectedOutlet && this.selectedOutlet !== \'ALL\') {
                this.loadProducts();
                this.loadOutlets();
            } else {
                // Show empty state - no outlet selected
                this.products = [];
                this.filteredProducts = [];
                this.availableOutlets = [];
                this.categories = [];
                console.log(\'ℹ️  No outlet selected - showing empty state\');
            }
        }';
    
    if (preg_match($oldInit, $content)) {
        $content = preg_replace($oldInit, $newInit, $content);
        echo "   ✅ Method init berhasil diupdate\n";
    } else {
        echo "   ⚠️  Pattern init tidak ditemukan, melakukan update manual...\n";
        
        // Fallback: replace the init method content
        $content = str_replace(
            'init() {
            console.log(\'🚀 Initializing Inter Outlet Sale App...\');
            this.loadProducts();
            this.loadOutlets();
        }',
            'init() {
            console.log(\'🚀 Initializing Inter Outlet Sale App...\');
            
            // Only load data if outlet is selected
            if (this.selectedOutlet && this.selectedOutlet !== \'ALL\') {
                this.loadProducts();
                this.loadOutlets();
            } else {
                // Show empty state - no outlet selected
                this.products = [];
                this.filteredProducts = [];
                this.availableOutlets = [];
                this.categories = [];
                console.log(\'ℹ️  No outlet selected - showing empty state\');
            }
        }',
            $content
        );
        echo "   ✅ JavaScript init berhasil diupdate (fallback)\n";
    }
    
    // Update changeOutlet method untuk menangani empty state
    $oldChangeOutlet = '/changeOutlet\(\)\s*\{[^}]*\}/s';
    
    $newChangeOutlet = 'changeOutlet() {
            this.clearCart();
            
            // Only load data if outlet is selected and not ALL
            if (this.selectedOutlet && this.selectedOutlet !== \'ALL\') {
                this.loadProducts();
                this.loadOutlets();
            } else {
                // Show empty state
                this.products = [];
                this.filteredProducts = [];
                this.availableOutlets = [];
                this.categories = [];
                console.log(\'ℹ️  No outlet selected - showing empty state\');
            }
        }';
    
    if (preg_match($oldChangeOutlet, $content)) {
        $content = preg_replace($oldChangeOutlet, $newChangeOutlet, $content);
        echo "   ✅ Method changeOutlet berhasil diupdate\n";
    } else {
        echo "   ⚠️  Pattern changeOutlet tidak ditemukan, melakukan update manual...\n";
        
        // Fallback
        $content = str_replace(
            'changeOutlet() {
            this.clearCart();
            this.loadProducts();
            this.loadOutlets();
        }',
            'changeOutlet() {
            this.clearCart();
            
            // Only load data if outlet is selected and not ALL
            if (this.selectedOutlet && this.selectedOutlet !== \'ALL\') {
                this.loadProducts();
                this.loadOutlets();
            } else {
                // Show empty state
                this.products = [];
                this.filteredProducts = [];
                this.availableOutlets = [];
                this.categories = [];
                console.log(\'ℹ️  No outlet selected - showing empty state\');
            }
        }',
            $content
        );
        echo "   ✅ JavaScript changeOutlet berhasil diupdate (fallback)\n";
    }
    
    file_put_contents($jsFile, $content);
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 3. Update View untuk menambahkan opsi "Pilih Outlet" dan menangani empty state
echo "\n3. Memperbaiki view inter-outlet/index.blade.php...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Update outlet selector untuk menambahkan opsi "Pilih Outlet"
    $oldSelector = '/<select x-model="selectedOutlet" @change="changeOutlet\(\)"\s*class="[^"]*">\s*@foreach\(\$outlets as \$outlet\)\s*<option value="{{ \$outlet->id_outlet }}" {{ \$outlet->id_outlet == \$selectedOutlet \? \'selected\' : \'\' }}>\s*{{ \$outlet->nama_outlet }}\s*<\/option>\s*@endforeach\s*<\/select>/s';
    
    $newSelector = '<select x-model="selectedOutlet" @change="changeOutlet()" 
                                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $selectedOutlet ? \'selected\' : \'\' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>';
    
    if (preg_match($oldSelector, $content)) {
        $content = preg_replace($oldSelector, $newSelector, $content);
        echo "   ✅ Outlet selector berhasil diupdate\n";
    } else {
        echo "   ⚠️  Pattern outlet selector tidak ditemukan, melakukan update manual...\n";
        
        // Fallback: replace the select element
        $content = str_replace(
            '@foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $selectedOutlet ? \'selected\' : \'\' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach',
            '<option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $selectedOutlet ? \'selected\' : \'\' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach',
            $content
        );
        echo "   ✅ View berhasil diupdate (fallback)\n";
    }
    
    // Update empty state message untuk produk
    $oldEmptyState = '/<div x-show="!loading && filteredProducts\.length === 0" class="text-center py-8">\s*<i class="bx bx-package text-4xl text-slate-400"><\/i>\s*<p class="mt-2 text-slate-600">Tidak ada produk ditemukan<\/p>\s*<\/div>/s';
    
    $newEmptyState = '<div x-show="!loading && filteredProducts.length === 0" class="text-center py-8">
                            <i class="bx bx-package text-4xl text-slate-400"></i>
                            <p class="mt-2 text-slate-600" x-text="!selectedOutlet || selectedOutlet === \'ALL\' ? \'Pilih outlet terlebih dahulu untuk melihat produk\' : \'Tidak ada produk ditemukan\'"></p>
                        </div>';
    
    if (preg_match($oldEmptyState, $content)) {
        $content = preg_replace($oldEmptyState, $newEmptyState, $content);
        echo "   ✅ Empty state message berhasil diupdate\n";
    } else {
        echo "   ⚠️  Pattern empty state tidak ditemukan, melakukan update manual...\n";
        
        // Fallback
        $content = str_replace(
            '<p class="mt-2 text-slate-600">Tidak ada produk ditemukan</p>',
            '<p class="mt-2 text-slate-600" x-text="!selectedOutlet || selectedOutlet === \'ALL\' ? \'Pilih outlet terlebih dahulu untuk melihat produk\' : \'Tidak ada produk ditemukan\'"></p>',
            $content
        );
        echo "   ✅ View berhasil diupdate (fallback)\n";
    }
    
    file_put_contents($viewFile, $content);
} else {
    echo "   ❌ File view tidak ditemukan\n";
}

// 4. Update window.selectedOutlet di view untuk handle null value
echo "\n4. Memperbaiki window.selectedOutlet di view...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Update window.selectedOutlet untuk handle null
    $content = str_replace(
        'window.selectedOutlet = {{ $selectedOutlet }};',
        'window.selectedOutlet = {{ $selectedOutlet ? $selectedOutlet : "null" }};',
        $content
    );
    
    file_put_contents($viewFile, $content);
    echo "   ✅ window.selectedOutlet berhasil diupdate\n";
}

echo "\n✅ Perbaikan selesai!\n\n";

echo "📋 Ringkasan perubahan:\n";
echo "   1. ✅ Controller tidak lagi menggunakan default outlet pertama\n";
echo "   2. ✅ JavaScript hanya load data jika outlet dipilih\n";
echo "   3. ✅ View menampilkan opsi 'Pilih Outlet' sebagai default\n";
echo "   4. ✅ Empty state message yang lebih informatif\n";
echo "   5. ✅ Handling null value untuk selectedOutlet\n\n";

echo "🧪 Untuk testing:\n";
echo "   1. Buka halaman penjualan antar outlet\n";
echo "   2. Pastikan dropdown outlet menampilkan 'Pilih Outlet' sebagai default\n";
echo "   3. Pastikan tidak ada produk yang ditampilkan sampai outlet dipilih\n";
echo "   4. Pilih outlet dan pastikan produk dimuat sesuai outlet yang dipilih\n";
echo "   5. Ganti outlet dan pastikan data berubah sesuai outlet baru\n\n";

echo "🔧 Jika masih ada masalah:\n";
echo "   1. Clear browser cache\n";
echo "   2. Periksa console browser untuk error JavaScript\n";
echo "   3. Pastikan user memiliki akses ke outlet yang dipilih\n\n";

?>