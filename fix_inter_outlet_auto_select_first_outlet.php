<?php

/**
 * Fix Inter Outlet Auto Select First Outlet
 * Mengubah implementasi untuk langsung mengarah ke outlet pertama yang tersedia
 */

echo "🔧 Mengubah implementasi untuk auto-select outlet pertama...\n\n";

// 1. Update Controller untuk menggunakan outlet pertama sebagai default
echo "1. Memperbaiki controller InterOutletSaleController...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Update method index untuk menggunakan outlet pertama sebagai default
    $oldIndexMethod = '/public function index\(Request \$request\)\s*\{[^}]*\}/s';
    
    $newIndexMethod = 'public function index(Request $request)
    {
        // Get user\'s accessible outlets only
        $outlets = $this->getUserOutlets();
        
        // Get selected outlet from request
        $selectedOutlet = $request->get(\'outlet_id\');
        
        // If no accessible outlets, redirect with error
        if ($outlets->isEmpty()) {
            return redirect()->back()->with(\'error\', \'Anda tidak memiliki akses ke outlet manapun.\');
        }
        
        // Validate selected outlet access if provided
        if ($selectedOutlet && $selectedOutlet !== \'ALL\' && is_numeric($selectedOutlet) && !$this->hasOutletAccess((int)$selectedOutlet)) {
            $selectedOutlet = null; // Reset invalid outlet
        }
        
        // Default to first accessible outlet if none selected
        if (!$selectedOutlet || $selectedOutlet === \'ALL\') {
            $selectedOutlet = $outlets->first()->id_outlet;
        }
        
        return view(\'admin.penjualan.inter-outlet.index\', compact(\'selectedOutlet\', \'outlets\'));
    }';
    
    if (preg_match($oldIndexMethod, $content)) {
        $content = preg_replace($oldIndexMethod, $newIndexMethod, $content);
        file_put_contents($controllerFile, $content);
        echo "   ✅ Method index berhasil diupdate untuk auto-select outlet pertama\n";
    } else {
        echo "   ⚠️  Pattern method index tidak ditemukan, melakukan update manual...\n";
        
        // Fallback: replace specific parts
        $content = str_replace(
            '// Don\'t set default outlet - let frontend handle empty state
        if (!$selectedOutlet || $selectedOutlet === \'ALL\') {
            $selectedOutlet = null;
        }',
            '// Default to first accessible outlet if none selected
        if (!$selectedOutlet || $selectedOutlet === \'ALL\') {
            $selectedOutlet = $outlets->first()->id_outlet;
        }',
            $content
        );
        
        file_put_contents($controllerFile, $content);
        echo "   ✅ Controller berhasil diupdate (fallback)\n";
    }
} else {
    echo "   ❌ File controller tidak ditemukan\n";
}

// 2. Update JavaScript untuk selalu load data saat init
echo "\n2. Memperbaiki JavaScript inter-outlet.js...\n";

$jsFile = 'public/js/inter-outlet.js';

if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Update init method untuk selalu load data
    $oldInit = '/init\(\)\s*\{[^}]*\}/s';
    
    $newInit = 'init() {
            console.log(\'🚀 Initializing Inter Outlet Sale App...\');
            
            // Always load data since we have a selected outlet
            this.loadProducts();
            this.loadOutlets();
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
            'init() {
            console.log(\'🚀 Initializing Inter Outlet Sale App...\');
            
            // Always load data since we have a selected outlet
            this.loadProducts();
            this.loadOutlets();
        }',
            $content
        );
        echo "   ✅ JavaScript init berhasil diupdate (fallback)\n";
    }
    
    // Update changeOutlet method untuk selalu load data
    $oldChangeOutlet = '/changeOutlet\(\)\s*\{[^}]*\}/s';
    
    $newChangeOutlet = 'changeOutlet() {
            this.clearCart();
            
            // Always load data when outlet changes
            this.loadProducts();
            this.loadOutlets();
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
            'changeOutlet() {
            this.clearCart();
            
            // Always load data when outlet changes
            this.loadProducts();
            this.loadOutlets();
        }',
            $content
        );
        echo "   ✅ JavaScript changeOutlet berhasil diupdate (fallback)\n";
    }
    
    file_put_contents($jsFile, $content);
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 3. Update View untuk menghilangkan opsi "Pilih Outlet"
echo "\n3. Memperbaiki view inter-outlet/index.blade.php...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Remove "Pilih Outlet" option
    $content = str_replace(
        '<option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)',
        '@foreach($outlets as $outlet)',
        $content
    );
    
    echo "   ✅ Opsi 'Pilih Outlet' berhasil dihilangkan\n";
    
    // Update empty state message back to original
    $content = str_replace(
        '<p class="mt-2 text-slate-600" x-text="!selectedOutlet || selectedOutlet === \'ALL\' ? \'Pilih outlet terlebih dahulu untuk melihat produk\' : \'Tidak ada produk ditemukan\'"></p>',
        '<p class="mt-2 text-slate-600">Tidak ada produk ditemukan</p>',
        $content
    );
    
    echo "   ✅ Empty state message berhasil dikembalikan ke original\n";
    
    file_put_contents($viewFile, $content);
} else {
    echo "   ❌ File view tidak ditemukan\n";
}

// 4. Update window.selectedOutlet di view untuk handle numeric value
echo "\n4. Memperbaiki window.selectedOutlet di view...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Update window.selectedOutlet untuk handle numeric value properly
    $content = str_replace(
        'window.selectedOutlet = {{ $selectedOutlet ? $selectedOutlet : "null" }};',
        'window.selectedOutlet = {{ $selectedOutlet }};',
        $content
    );
    
    file_put_contents($viewFile, $content);
    echo "   ✅ window.selectedOutlet berhasil diupdate\n";
}

echo "\n✅ Perbaikan selesai!\n\n";

echo "📋 Ringkasan perubahan:\n";
echo "   1. ✅ Controller menggunakan outlet pertama sebagai default\n";
echo "   2. ✅ JavaScript selalu load data saat init dan changeOutlet\n";
echo "   3. ✅ View menghilangkan opsi 'Pilih Outlet'\n";
echo "   4. ✅ Empty state message dikembalikan ke original\n";
echo "   5. ✅ Auto-select outlet pertama yang tersedia\n\n";

echo "🧪 Untuk testing:\n";
echo "   1. Buka halaman penjualan antar outlet\n";
echo "   2. Pastikan outlet pertama otomatis terpilih\n";
echo "   3. Pastikan produk langsung dimuat sesuai outlet pertama\n";
echo "   4. Ganti outlet dan pastikan data berubah sesuai outlet baru\n";
echo "   5. Pastikan tidak ada opsi 'Pilih Outlet' di dropdown\n\n";

echo "🔧 Jika masih ada masalah:\n";
echo "   1. Clear browser cache\n";
echo "   2. Periksa console browser untuk error JavaScript\n";
echo "   3. Pastikan user memiliki akses ke outlet yang dipilih\n\n";

?>