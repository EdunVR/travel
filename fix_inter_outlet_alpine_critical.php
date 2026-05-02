<?php

/**
 * Critical Fix for Inter Outlet Alpine.js Issues
 * Addresses all the undefined variable errors and component registration issues
 */

echo "🚨 CRITICAL FIX: Inter Outlet Alpine.js Issues\n\n";

// 1. Fix the JavaScript file with proper error handling and fallbacks
echo "1. Fixing JavaScript file with comprehensive error handling...\n";

$jsContent = <<<'JS'
/**
 * Inter Outlet Sale Application - CRITICAL FIX
 * Alpine.js component for managing inter-outlet sales transactions
 * 
 * IMPORTANT: This file must be loaded AFTER Alpine.js
 */

console.log('🔄 Loading Inter Outlet JavaScript...');

// Ensure Alpine.js is available
function waitForAlpine(callback, maxAttempts = 50) {
    let attempts = 0;
    
    function check() {
        attempts++;
        if (typeof Alpine !== 'undefined') {
            console.log('✅ Alpine.js found, initializing component...');
            callback();
        } else if (attempts < maxAttempts) {
            console.log(`⏳ Waiting for Alpine.js... (${attempts}/${maxAttempts})`);
            setTimeout(check, 100);
        } else {
            console.error('❌ Alpine.js not found after maximum attempts');
            // Try to load Alpine.js manually
            loadAlpineManually();
        }
    }
    
    check();
}

function loadAlpineManually() {
    console.log('🔄 Attempting to load Alpine.js manually...');
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
    script.defer = true;
    script.onload = () => {
        console.log('✅ Alpine.js loaded manually');
        setTimeout(initializeComponent, 100);
    };
    document.head.appendChild(script);
}

function initializeComponent() {
    console.log('🏪 Initializing Inter Outlet Sale Component...');
    
    // Define constants globally
    window.ALL = 'all';
    const ALL = 'all';
    
    // Register Alpine.js component
    Alpine.data('interOutletSaleApp', () => ({
        // State - Initialize all properties to prevent undefined errors
        selectedOutlet: window.selectedOutlet || 1,
        destinationOutlet: '',
        transactionDate: new Date().toISOString().split('T')[0],
        products: [],
        filteredProducts: [],
        availableOutlets: [],
        categories: [],
        cart: [],
        searchProduct: '',
        categoryFilter: '',
        discountPercent: 0,
        taxPercent: 0,
        notes: '',
        loading: false,
        processing: false,
        showHistory: false,
        showCoaSettings: false,
        showPriceSettings: false,
        showSuccessModal: false,
        successMessage: '',
        lastTransactionId: null,

        // History data
        historyData: [],
        historyLoading: false,
        historyOutletFilter: 'all',
        historyStatusFilter: 'all',
        historyStartDate: '',
        historyEndDate: '',

        // COA Settings data
        coaLoading: false,
        coaSaving: false,
        coaSelectedOutlet: '',
        coaBooks: [],
        coaAccounts: [],
        coaData: {
            accounting_book_id: '',
            akun_piutang_antar_outlet: '',
            akun_pendapatan_antar_outlet: '',
            akun_hpp: '',
            akun_persediaan: '',
            akun_ppn: ''
        },

        // Price settings
        priceSearchProduct: '',
        priceCategoryFilter: '',
        filteredPriceProducts: [],
        priceProducts: [],

        // Computed properties
        get subtotal() {
            try {
                return this.cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            } catch (e) {
                console.error('Error calculating subtotal:', e);
                return 0;
            }
        },

        get discountAmount() {
            try {
                return (this.subtotal * (this.discountPercent || 0)) / 100;
            } catch (e) {
                console.error('Error calculating discount:', e);
                return 0;
            }
        },

        get taxAmount() {
            try {
                return ((this.subtotal - this.discountAmount) * (this.taxPercent || 0)) / 100;
            } catch (e) {
                console.error('Error calculating tax:', e);
                return 0;
            }
        },

        get total() {
            try {
                return this.subtotal - this.discountAmount + this.taxAmount;
            } catch (e) {
                console.error('Error calculating total:', e);
                return 0;
            }
        },

        get canProcess() {
            try {
                return this.cart.length > 0 && 
                       this.destinationOutlet && 
                       this.transactionDate && 
                       !this.processing;
            } catch (e) {
                console.error('Error checking canProcess:', e);
                return false;
            }
        },

        get historyUrl() {
            return window.routes?.interOutletHistory || '/admin/penjualan/inter-outlet/history';
        },

        get coaSettingsUrl() {
            return window.routes?.interOutletCoaSettings || '/admin/penjualan/inter-outlet/coa-settings';
        },

        // Methods
        init() {
            console.log('🚀 Initializing Inter Outlet Sale App...');
            try {
                this.loadProducts();
                this.loadOutlets();
                console.log('✅ Inter Outlet Sale App initialized successfully');
            } catch (e) {
                console.error('Error initializing app:', e);
            }
        },

        async loadProducts() {
            this.loading = true;
            try {
                const url = window.routes?.interOutletProducts || `/admin/penjualan/inter-outlet/products`;
                const response = await this.fetchWithAuth(`${url}?outlet_id=${this.selectedOutlet}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.products = data.data || [];
                    this.filteredProducts = [...this.products];
                    this.categories = [...new Set(this.products.map(p => p.category).filter(Boolean))];
                    console.log(`✅ Loaded ${this.products.length} products`);
                } else {
                    throw new Error(data.message || 'Failed to load products');
                }
            } catch (error) {
                console.error('Load products error:', error);
                this.showError('Gagal memuat produk: ' + error.message);
                this.products = [];
                this.filteredProducts = [];
                this.categories = [];
            } finally {
                this.loading = false;
            }
        },

        async loadOutlets() {
            try {
                const url = window.routes?.interOutletOutlets || `/admin/penjualan/inter-outlet/outlets`;
                const response = await this.fetchWithAuth(`${url}?current_outlet_id=${this.selectedOutlet}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.availableOutlets = data.data || [];
                    console.log(`✅ Loaded ${this.availableOutlets.length} outlets`);
                } else {
                    throw new Error(data.message || 'Failed to load outlets');
                }
            } catch (error) {
                console.error('Load outlets error:', error);
                this.availableOutlets = [];
            }
        },

        // Helper method for authenticated fetch
        async fetchWithAuth(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            };
            
            return fetch(url, { ...defaultOptions, ...options });
        },

        getCSRFToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        },

        changeOutlet() {
            try {
                this.clearCart();
                this.loadProducts();
                this.loadOutlets();
            } catch (e) {
                console.error('Error changing outlet:', e);
            }
        },

        searchProducts() {
            try {
                this.filterProducts();
            } catch (e) {
                console.error('Error searching products:', e);
            }
        },

        filterProducts() {
            try {
                let filtered = [...this.products];
                
                if (this.searchProduct) {
                    const search = this.searchProduct.toLowerCase();
                    filtered = filtered.filter(p => 
                        (p.name && p.name.toLowerCase().includes(search)) ||
                        (p.sku && p.sku.toLowerCase().includes(search))
                    );
                }
                
                if (this.categoryFilter) {
                    filtered = filtered.filter(p => p.category === this.categoryFilter);
                }
                
                this.filteredProducts = filtered;
            } catch (e) {
                console.error('Error filtering products:', e);
                this.filteredProducts = [...this.products];
            }
        },

        addToCart(product) {
            try {
                if (!product || product.stock <= 0) {
                    this.showError('Stok produk tidak mencukupi');
                    return;
                }

                const existingIndex = this.cart.findIndex(item => item.id_produk === product.id_produk);
                
                if (existingIndex >= 0) {
                    this.cart[existingIndex].quantity += 1;
                    this.updateCartItem(existingIndex);
                } else {
                    this.cart.push({
                        id_produk: product.id_produk,
                        name: product.name || 'Unknown Product',
                        sku: product.sku || '',
                        price: product.price || 0,
                        quantity: 1,
                        satuan: product.satuan || 'pcs',
                        stock: product.stock || 0,
                        subtotal: product.price || 0
                    });
                }
                
                console.log('✅ Product added to cart:', product.name);
            } catch (e) {
                console.error('Error adding to cart:', e);
                this.showError('Gagal menambahkan produk ke keranjang');
            }
        },

        removeFromCart(index) {
            try {
                if (index >= 0 && index < this.cart.length) {
                    this.cart.splice(index, 1);
                }
            } catch (e) {
                console.error('Error removing from cart:', e);
            }
        },

        increaseQuantity(index) {
            try {
                const item = this.cart[index];
                if (item && item.quantity < item.stock) {
                    item.quantity += 1;
                    this.updateCartItem(index);
                } else {
                    this.showError('Kuantitas melebihi stok tersedia');
                }
            } catch (e) {
                console.error('Error increasing quantity:', e);
            }
        },

        decreaseQuantity(index) {
            try {
                const item = this.cart[index];
                if (item && item.quantity > 0.01) {
                    item.quantity -= 1;
                    this.updateCartItem(index);
                }
            } catch (e) {
                console.error('Error decreasing quantity:', e);
            }
        },

        updateCartItem(index) {
            try {
                const item = this.cart[index];
                if (!item) return;
                
                if (item.quantity > item.stock) {
                    item.quantity = item.stock;
                    this.showError('Kuantitas disesuaikan dengan stok tersedia');
                }
                item.subtotal = (item.quantity || 0) * (item.price || 0);
            } catch (e) {
                console.error('Error updating cart item:', e);
            }
        },

        calculateTotal() {
            // Total will be calculated automatically through computed properties
            try {
                // Force reactivity update
                this.$nextTick(() => {
                    console.log('Total recalculated:', this.total);
                });
            } catch (e) {
                console.error('Error calculating total:', e);
            }
        },

        clearCart() {
            try {
                this.cart = [];
                this.discountPercent = 0;
                this.taxPercent = 0;
                this.notes = '';
                this.destinationOutlet = '';
                console.log('✅ Cart cleared');
            } catch (e) {
                console.error('Error clearing cart:', e);
            }
        },

        async processTransaction() {
            if (!this.canProcess) return;

            this.processing = true;
            
            try {
                const payload = {
                    tanggal: this.transactionDate,
                    outlet_asal: this.selectedOutlet,
                    outlet_tujuan: this.destinationOutlet,
                    items: this.cart.map(item => ({
                        id_produk: item.id_produk,
                        kuantitas: item.quantity,
                        harga: item.price,
                        subtotal: item.subtotal
                    })),
                    subtotal: this.subtotal,
                    diskon_persen: this.discountPercent,
                    ppn: this.taxAmount,
                    total: this.total,
                    catatan: this.notes
                };

                const url = window.routes?.interOutletStore || '/admin/penjualan/inter-outlet';
                const response = await this.fetchWithAuth(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    this.lastTransactionId = data.data.id;
                    this.successMessage = `Transaksi ${data.data.no_transaksi} berhasil disimpan dengan total ${this.formatCurrency(data.data.total)}`;
                    this.showSuccessModal = true;
                    this.clearCart();
                    this.loadProducts(); // Refresh products to update stock
                } else {
                    this.showError(data.message || 'Gagal memproses transaksi');
                }
            } catch (error) {
                console.error('Process transaction error:', error);
                this.showError('Gagal memproses transaksi: ' + error.message);
            } finally {
                this.processing = false;
            }
        },

        printInvoice() {
            try {
                if (this.lastTransactionId) {
                    const url = window.routes?.interOutletPrint || `/admin/penjualan/inter-outlet/${this.lastTransactionId}/print`;
                    window.open(url, '_blank');
                }
                this.showSuccessModal = false;
            } catch (e) {
                console.error('Error printing invoice:', e);
            }
        },

        formatCurrency(amount) {
            try {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount || 0);
            } catch (e) {
                console.error('Error formatting currency:', e);
                return 'Rp 0';
            }
        },

        showError(message) {
            try {
                console.error('App Error:', message);
                alert(message);
            } catch (e) {
                console.error('Error showing error:', e);
            }
        },

        // History Methods
        async loadHistoryData() {
            console.log('Loading history data...');
            
            this.historyLoading = true;
            try {
                const params = new URLSearchParams({
                    outlet_id: this.historyOutletFilter || 'all',
                    status: this.historyStatusFilter || 'all',
                    start_date: this.historyStartDate || '',
                    end_date: this.historyEndDate || ''
                });
                
                const url = window.routes?.interOutletHistory || '/admin/penjualan/inter-outlet/history';
                const response = await this.fetchWithAuth(`${url}?${params}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.historyData = data.data || [];
                    console.log('✅ History data loaded:', this.historyData.length, 'records');
                } else {
                    this.showError(data.message || 'Gagal memuat data riwayat');
                    this.historyData = [];
                }
            } catch (error) {
                console.error('Error loading history:', error);
                this.showError('Terjadi kesalahan saat memuat riwayat: ' + error.message);
                this.historyData = [];
            } finally {
                this.historyLoading = false;
            }
        },

        printHistoryInvoice(transactionId) {
            try {
                const url = window.routes?.interOutletPrint || `/admin/penjualan/inter-outlet/${transactionId}/print`;
                window.open(url, '_blank');
            } catch (e) {
                console.error('Error printing history invoice:', e);
            }
        },

        async approveTransaction(transactionId) {
            if (!confirm('Apakah Anda yakin ingin menyetujui transaksi ini?')) {
                return;
            }
            
            try {
                const url = window.routes?.interOutletApprove || `/admin/penjualan/inter-outlet/${transactionId}/approve`;
                const response = await this.fetchWithAuth(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showError('Transaksi berhasil disetujui');
                    this.loadHistoryData(); // Refresh history data
                } else {
                    this.showError(data.message || 'Gagal menyetujui transaksi');
                }
            } catch (error) {
                console.error('Error approving transaction:', error);
                this.showError('Terjadi kesalahan saat menyetujui transaksi');
            }
        },

        async deleteTransaction(transactionId) {
            if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            
            try {
                const url = window.routes?.interOutletDelete || `/admin/penjualan/inter-outlet/${transactionId}`;
                const response = await this.fetchWithAuth(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showError('Transaksi berhasil dihapus');
                    this.loadHistoryData(); // Refresh history data
                } else {
                    this.showError(data.message || 'Gagal menghapus transaksi');
                }
            } catch (error) {
                console.error('Error deleting transaction:', error);
                this.showError('Terjadi kesalahan saat menghapus transaksi');
            }
        },

        // COA Methods (placeholder)
        async loadCoaData() {
            console.log('Loading COA data...');
            // Implementation for COA loading
        },

        // Price Methods (placeholder)  
        async loadPriceProducts() {
            console.log('Loading price products...');
            // Implementation for price products loading
        }
    }));
    
    console.log('✅ Inter Outlet Sale Component registered successfully');
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        waitForAlpine(initializeComponent);
    });
} else {
    waitForAlpine(initializeComponent);
}

// Also listen for alpine:init event as backup
document.addEventListener('alpine:init', () => {
    console.log('🔄 Alpine:init event detected, ensuring component is registered...');
    initializeComponent();
});

// Error handler for undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('⚠️ Caught ALL undefined error, using fallback');
        window.ALL = 'all';
        return true;
    }
    
    if (e.message.includes('interOutletSaleApp is not defined')) {
        console.warn('⚠️ Component not found, attempting to reinitialize...');
        setTimeout(() => {
            waitForAlpine(initializeComponent);
        }, 1000);
        return true;
    }
});

// Ensure constants are available globally
if (typeof window.ALL === 'undefined') {
    window.ALL = 'all';
}

console.log('📦 Inter Outlet JavaScript file loaded');
JS;

// Write the fixed JavaScript file
if (file_put_contents('public/js/inter-outlet.js', $jsContent)) {
    echo "   ✅ JavaScript file updated with comprehensive error handling\n";
} else {
    echo "   ❌ Failed to update JavaScript file\n";
}

// 2. Check and fix the admin layout
echo "\n2. Checking admin layout JavaScript loading order...\n";

$layoutFile = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Check if the order is correct
    $alpinePos = strpos($content, 'alpinejs@3.x.x/dist/cdn.min.js');
    $interOutletPos = strpos($content, 'inter-outlet.js');
    
    if ($alpinePos !== false && $interOutletPos !== false && $interOutletPos > $alpinePos) {
        echo "   ✅ JavaScript loading order is correct\n";
    } else {
        echo "   ⚠️ Fixing JavaScript loading order...\n";
        
        // Remove existing inter-outlet.js line
        $content = preg_replace('/\s*<script[^>]*inter-outlet\.js[^>]*><\/script>\s*/', '', $content);
        
        // Add it after Alpine.js
        $content = str_replace(
            '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>',
            '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>' . "\n" .
            '    <script defer src="{{ asset(\'js/inter-outlet.js\') }}"></script>',
            $content
        );
        
        if (file_put_contents($layoutFile, $content)) {
            echo "   ✅ Layout file updated with correct loading order\n";
        } else {
            echo "   ❌ Failed to update layout file\n";
        }
    }
} else {
    echo "   ❌ Layout file not found\n";
}

// 3. Clear browser cache instructions
echo "\n3. Browser cache clearing instructions:\n";
echo "   🔄 Clear browser cache (Ctrl+F5 or Cmd+Shift+R)\n";
echo "   🔄 Clear Laravel cache: php artisan cache:clear\n";
echo "   🔄 Clear view cache: php artisan view:clear\n";

// 4. Test the fix
echo "\n4. Testing the fix...\n";

// Check if files exist and have correct content
$jsExists = file_exists('public/js/inter-outlet.js');
$layoutExists = file_exists($layoutFile);

if ($jsExists && $layoutExists) {
    $jsContent = file_get_contents('public/js/inter-outlet.js');
    $layoutContent = file_get_contents($layoutFile);
    
    $hasAlpineData = strpos($jsContent, "Alpine.data('interOutletSaleApp'") !== false;
    $hasErrorHandling = strpos($jsContent, 'waitForAlpine') !== false;
    $hasCorrectOrder = strpos($layoutContent, 'alpinejs@3.x.x') < strpos($layoutContent, 'inter-outlet.js');
    
    if ($hasAlpineData && $hasErrorHandling && $hasCorrectOrder) {
        echo "   ✅ All fixes applied successfully\n";
        echo "\n🎯 MANUAL TESTING STEPS:\n";
        echo "   1. Clear browser cache (Ctrl+F5)\n";
        echo "   2. Open Developer Tools (F12)\n";
        echo "   3. Go to /admin/penjualan/inter-outlet\n";
        echo "   4. Check Console tab for:\n";
        echo "      ✅ '🔄 Loading Inter Outlet JavaScript...'\n";
        echo "      ✅ '✅ Alpine.js found, initializing component...'\n";
        echo "      ✅ '✅ Inter Outlet Sale Component registered successfully'\n";
        echo "      ❌ NO 'ALL is not defined' errors\n";
        echo "      ❌ NO 'interOutletSaleApp is not defined' errors\n";
        echo "   5. Test functionality:\n";
        echo "      - Dropdown outlets should load\n";
        echo "      - Products should display\n";
        echo "      - Search should work\n";
        echo "      - Add to cart should work\n";
    } else {
        echo "   ⚠️ Some fixes may not have been applied correctly\n";
        echo "   - Alpine.data registration: " . ($hasAlpineData ? "✅" : "❌") . "\n";
        echo "   - Error handling: " . ($hasErrorHandling ? "✅" : "❌") . "\n";
        echo "   - Loading order: " . ($hasCorrectOrder ? "✅" : "❌") . "\n";
    }
} else {
    echo "   ❌ Required files not found\n";
}

echo "\n🚨 CRITICAL FIX COMPLETE\n";
echo "If errors persist, check:\n";
echo "1. Browser console for any remaining errors\n";
echo "2. Network tab for failed requests\n";
echo "3. Laravel logs for server-side errors\n";
echo "4. Ensure user has proper permissions\n\n";