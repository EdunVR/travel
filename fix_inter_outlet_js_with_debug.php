<?php

/**
 * Fix Inter Outlet JS to work with new Alpine.js debug system
 */

echo "🔧 FIXING INTER-OUTLET.JS WITH DEBUG SYSTEM\n\n";

$jsContent = <<<'JS'
/**
 * Inter Outlet Sale Application - COMPATIBLE WITH ALPINE DEBUG SYSTEM
 * Alpine.js component for managing inter-outlet sales transactions
 */

console.log('📦 [INTER-OUTLET] Loading Inter Outlet JavaScript...');

// Wait for Alpine.js with the new debug system
function waitForAlpineWithDebug(callback, maxAttempts = 100) {
    let attempts = 0;
    
    function check() {
        attempts++;
        
        // Check if Alpine.js is available
        if (typeof Alpine !== 'undefined') {
            console.log('✅ [INTER-OUTLET] Alpine.js found, initializing component...');
            callback();
            return;
        }
        
        // Check if debug system marked Alpine as loaded
        if (window.alpineLoaded) {
            console.log('✅ [INTER-OUTLET] Alpine.js marked as loaded by debug system');
            setTimeout(callback, 100); // Small delay to ensure Alpine is fully ready
            return;
        }
        
        if (attempts < maxAttempts) {
            console.log(`⏳ [INTER-OUTLET] Waiting for Alpine.js... (${attempts}/${maxAttempts})`);
            setTimeout(check, 100);
        } else {
            console.error('❌ [INTER-OUTLET] Alpine.js not found after maximum attempts');
            console.error('🚨 [INTER-OUTLET] Component will not be registered');
        }
    }
    
    check();
}

// Listen for the alpine:loaded event from debug system
window.addEventListener('alpine:loaded', () => {
    console.log('🎉 [INTER-OUTLET] Received alpine:loaded event');
    setTimeout(initializeComponent, 50);
});

function initializeComponent() {
    if (typeof Alpine === 'undefined') {
        console.error('❌ [INTER-OUTLET] Alpine.js still not available during initialization');
        return;
    }
    
    console.log('🏪 [INTER-OUTLET] Initializing Inter Outlet Sale Component...');
    
    // Define constants globally
    window.ALL = 'all';
    const ALL = 'all';
    
    try {
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
                    console.error('[INTER-OUTLET] Error calculating subtotal:', e);
                    return 0;
                }
            },

            get discountAmount() {
                try {
                    return (this.subtotal * (this.discountPercent || 0)) / 100;
                } catch (e) {
                    console.error('[INTER-OUTLET] Error calculating discount:', e);
                    return 0;
                }
            },

            get taxAmount() {
                try {
                    return ((this.subtotal - this.discountAmount) * (this.taxPercent || 0)) / 100;
                } catch (e) {
                    console.error('[INTER-OUTLET] Error calculating tax:', e);
                    return 0;
                }
            },

            get total() {
                try {
                    return this.subtotal - this.discountAmount + this.taxAmount;
                } catch (e) {
                    console.error('[INTER-OUTLET] Error calculating total:', e);
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
                    console.error('[INTER-OUTLET] Error checking canProcess:', e);
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
                console.log('🚀 [INTER-OUTLET] Initializing Inter Outlet Sale App...');
                try {
                    this.loadProducts();
                    this.loadOutlets();
                    console.log('✅ [INTER-OUTLET] Inter Outlet Sale App initialized successfully');
                } catch (e) {
                    console.error('[INTER-OUTLET] Error initializing app:', e);
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
                        console.log(`✅ [INTER-OUTLET] Loaded ${this.products.length} products`);
                    } else {
                        throw new Error(data.message || 'Failed to load products');
                    }
                } catch (error) {
                    console.error('[INTER-OUTLET] Load products error:', error);
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
                        console.log(`✅ [INTER-OUTLET] Loaded ${this.availableOutlets.length} outlets`);
                    } else {
                        throw new Error(data.message || 'Failed to load outlets');
                    }
                } catch (error) {
                    console.error('[INTER-OUTLET] Load outlets error:', error);
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
                    console.error('[INTER-OUTLET] Error changing outlet:', e);
                }
            },

            searchProducts() {
                try {
                    this.filterProducts();
                } catch (e) {
                    console.error('[INTER-OUTLET] Error searching products:', e);
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
                    console.error('[INTER-OUTLET] Error filtering products:', e);
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
                    
                    console.log('✅ [INTER-OUTLET] Product added to cart:', product.name);
                } catch (e) {
                    console.error('[INTER-OUTLET] Error adding to cart:', e);
                    this.showError('Gagal menambahkan produk ke keranjang');
                }
            },

            removeFromCart(index) {
                try {
                    if (index >= 0 && index < this.cart.length) {
                        this.cart.splice(index, 1);
                    }
                } catch (e) {
                    console.error('[INTER-OUTLET] Error removing from cart:', e);
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
                    console.error('[INTER-OUTLET] Error increasing quantity:', e);
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
                    console.error('[INTER-OUTLET] Error decreasing quantity:', e);
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
                    console.error('[INTER-OUTLET] Error updating cart item:', e);
                }
            },

            calculateTotal() {
                // Total will be calculated automatically through computed properties
                try {
                    // Force reactivity update
                    this.$nextTick(() => {
                        console.log('[INTER-OUTLET] Total recalculated:', this.total);
                    });
                } catch (e) {
                    console.error('[INTER-OUTLET] Error calculating total:', e);
                }
            },

            clearCart() {
                try {
                    this.cart = [];
                    this.discountPercent = 0;
                    this.taxPercent = 0;
                    this.notes = '';
                    this.destinationOutlet = '';
                    console.log('✅ [INTER-OUTLET] Cart cleared');
                } catch (e) {
                    console.error('[INTER-OUTLET] Error clearing cart:', e);
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
                    console.error('[INTER-OUTLET] Process transaction error:', error);
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
                    console.error('[INTER-OUTLET] Error printing invoice:', e);
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
                    console.error('[INTER-OUTLET] Error formatting currency:', e);
                    return 'Rp 0';
                }
            },

            showError(message) {
                try {
                    console.error('[INTER-OUTLET] App Error:', message);
                    alert(message);
                } catch (e) {
                    console.error('[INTER-OUTLET] Error showing error:', e);
                }
            },

            // History Methods
            async loadHistoryData() {
                console.log('[INTER-OUTLET] Loading history data...');
                
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
                        console.log('✅ [INTER-OUTLET] History data loaded:', this.historyData.length, 'records');
                    } else {
                        this.showError(data.message || 'Gagal memuat data riwayat');
                        this.historyData = [];
                    }
                } catch (error) {
                    console.error('[INTER-OUTLET] Error loading history:', error);
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
                    console.error('[INTER-OUTLET] Error printing history invoice:', e);
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
                    console.error('[INTER-OUTLET] Error approving transaction:', error);
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
                    console.error('[INTER-OUTLET] Error deleting transaction:', error);
                    this.showError('Terjadi kesalahan saat menghapus transaksi');
                }
            },

            // COA Methods (placeholder)
            async loadCoaData() {
                console.log('[INTER-OUTLET] Loading COA data...');
                // Implementation for COA loading
            },

            // Price Methods (placeholder)  
            async loadPriceProducts() {
                console.log('[INTER-OUTLET] Loading price products...');
                // Implementation for price products loading
            }
        }));
        
        console.log('✅ [INTER-OUTLET] Inter Outlet Sale Component registered successfully');
        
    } catch (error) {
        console.error('❌ [INTER-OUTLET] Error registering component:', error);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        waitForAlpineWithDebug(initializeComponent);
    });
} else {
    waitForAlpineWithDebug(initializeComponent);
}

// Also listen for alpine:init event as backup
document.addEventListener('alpine:init', () => {
    console.log('🔄 [INTER-OUTLET] Alpine:init event detected, ensuring component is registered...');
    setTimeout(initializeComponent, 50);
});

// Error handler for undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('⚠️ [INTER-OUTLET] Caught ALL undefined error, using fallback');
        window.ALL = 'all';
        return true;
    }
    
    if (e.message.includes('interOutletSaleApp is not defined')) {
        console.warn('⚠️ [INTER-OUTLET] Component not found, attempting to reinitialize...');
        setTimeout(() => {
            waitForAlpineWithDebug(initializeComponent);
        }, 1000);
        return true;
    }
});

// Ensure constants are available globally
if (typeof window.ALL === 'undefined') {
    window.ALL = 'all';
}

console.log('📦 [INTER-OUTLET] Inter Outlet JavaScript file loaded');
JS;

// Write the updated JavaScript file
if (file_put_contents('public/js/inter-outlet.js', $jsContent)) {
    echo "   ✅ Inter-outlet.js updated with debug system compatibility\n";
} else {
    echo "   ❌ Failed to update inter-outlet.js\n";
}

echo "\n✅ INTER-OUTLET.JS UPDATED WITH DEBUG SYSTEM\n";
echo "The JavaScript file now works with the new Alpine.js debug system.\n\n";