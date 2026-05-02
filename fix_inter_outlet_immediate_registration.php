<?php

/**
 * Immediate Fix for Inter Outlet Component Registration
 * Register component immediately when Alpine.js is available
 */

echo "🚨 IMMEDIATE FIX: Inter Outlet Component Registration\n\n";

$jsContent = <<<'JS'
/**
 * Inter Outlet Sale Application - IMMEDIATE REGISTRATION
 * This version registers the component immediately when Alpine.js is detected
 */

console.log('📦 [INTER-OUTLET] Starting immediate registration...');

// Function to register the component immediately
function registerInterOutletComponent() {
    if (typeof Alpine === 'undefined') {
        console.error('❌ [INTER-OUTLET] Alpine.js not available for registration');
        return false;
    }
    
    console.log('🏪 [INTER-OUTLET] Registering component immediately...');
    
    try {
        // Define constants globally first
        window.ALL = 'all';
        
        // Register the component
        Alpine.data('interOutletSaleApp', () => ({
            // Initialize ALL properties with default values
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
                return this.cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            },

            get discountAmount() {
                return (this.subtotal * (this.discountPercent || 0)) / 100;
            },

            get taxAmount() {
                return ((this.subtotal - this.discountAmount) * (this.taxPercent || 0)) / 100;
            },

            get total() {
                return this.subtotal - this.discountAmount + this.taxAmount;
            },

            get canProcess() {
                return this.cart.length > 0 && 
                       this.destinationOutlet && 
                       this.transactionDate && 
                       !this.processing;
            },

            // Methods
            init() {
                console.log('🚀 [INTER-OUTLET] Component initialized');
                this.loadProducts();
                this.loadOutlets();
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
                this.clearCart();
                this.loadProducts();
                this.loadOutlets();
            },

            searchProducts() {
                this.filterProducts();
            },

            filterProducts() {
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
            },

            addToCart(product) {
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
            },

            removeFromCart(index) {
                if (index >= 0 && index < this.cart.length) {
                    this.cart.splice(index, 1);
                }
            },

            increaseQuantity(index) {
                const item = this.cart[index];
                if (item && item.quantity < item.stock) {
                    item.quantity += 1;
                    this.updateCartItem(index);
                } else {
                    this.showError('Kuantitas melebihi stok tersedia');
                }
            },

            decreaseQuantity(index) {
                const item = this.cart[index];
                if (item && item.quantity > 0.01) {
                    item.quantity -= 1;
                    this.updateCartItem(index);
                }
            },

            updateCartItem(index) {
                const item = this.cart[index];
                if (!item) return;
                
                if (item.quantity > item.stock) {
                    item.quantity = item.stock;
                    this.showError('Kuantitas disesuaikan dengan stok tersedia');
                }
                item.subtotal = (item.quantity || 0) * (item.price || 0);
            },

            calculateTotal() {
                // Total calculated automatically via computed properties
            },

            clearCart() {
                this.cart = [];
                this.discountPercent = 0;
                this.taxPercent = 0;
                this.notes = '';
                this.destinationOutlet = '';
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
                        this.loadProducts();
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
                if (this.lastTransactionId) {
                    const url = window.routes?.interOutletPrint || `/admin/penjualan/inter-outlet/${this.lastTransactionId}/print`;
                    window.open(url, '_blank');
                }
                this.showSuccessModal = false;
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount || 0);
            },

            showError(message) {
                console.error('[INTER-OUTLET] Error:', message);
                alert(message);
            },

            // History Methods
            async loadHistoryData() {
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
                const url = window.routes?.interOutletPrint || `/admin/penjualan/inter-outlet/${transactionId}/print`;
                window.open(url, '_blank');
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
                        this.loadHistoryData();
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
                        this.loadHistoryData();
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
            },

            // Price Methods (placeholder)  
            async loadPriceProducts() {
                console.log('[INTER-OUTLET] Loading price products...');
            }
        }));
        
        console.log('✅ [INTER-OUTLET] Component registered successfully');
        return true;
        
    } catch (error) {
        console.error('❌ [INTER-OUTLET] Error registering component:', error);
        return false;
    }
}

// Try to register immediately if Alpine.js is already available
if (typeof Alpine !== 'undefined') {
    console.log('🎯 [INTER-OUTLET] Alpine.js already available, registering immediately');
    registerInterOutletComponent();
} else {
    console.log('⏳ [INTER-OUTLET] Waiting for Alpine.js...');
    
    // Multiple registration attempts
    let attempts = 0;
    const maxAttempts = 50;
    
    function tryRegister() {
        attempts++;
        
        if (typeof Alpine !== 'undefined') {
            console.log(`✅ [INTER-OUTLET] Alpine.js found after ${attempts} attempts`);
            registerInterOutletComponent();
        } else if (attempts < maxAttempts) {
            setTimeout(tryRegister, 100);
        } else {
            console.error('❌ [INTER-OUTLET] Alpine.js not found after maximum attempts');
        }
    }
    
    // Start trying immediately
    tryRegister();
    
    // Also listen for events
    document.addEventListener('alpine:init', () => {
        console.log('🎉 [INTER-OUTLET] Alpine:init event received');
        registerInterOutletComponent();
    });
    
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🎉 [INTER-OUTLET] DOMContentLoaded event received');
        setTimeout(tryRegister, 100);
    });
    
    window.addEventListener('load', () => {
        console.log('🎉 [INTER-OUTLET] Window load event received');
        setTimeout(tryRegister, 100);
    });
}

// Global error handler
window.addEventListener('error', function(e) {
    if (e.message.includes('interOutletSaleApp is not defined')) {
        console.warn('⚠️ [INTER-OUTLET] Component not found, attempting re-registration...');
        setTimeout(() => {
            if (typeof Alpine !== 'undefined') {
                registerInterOutletComponent();
            }
        }, 500);
        return true;
    }
});

// Ensure constants are available
if (typeof window.ALL === 'undefined') {
    window.ALL = 'all';
}

console.log('📦 [INTER-OUTLET] Script loaded');
JS;

// Write the JavaScript file
if (file_put_contents('public/js/inter-outlet.js', $jsContent)) {
    echo "   ✅ Inter-outlet.js updated with immediate registration\n";
} else {
    echo "   ❌ Failed to update inter-outlet.js\n";
    exit(1);
}

echo "\n✅ IMMEDIATE REGISTRATION FIX APPLIED\n";
echo "The component will now register as soon as Alpine.js is available.\n\n";

echo "🎯 TEST STEPS:\n";
echo "1. Clear browser cache completely (Ctrl+F5)\n";
echo "2. Open Developer Tools (F12)\n";
echo "3. Go to /admin/penjualan/inter-outlet\n";
echo "4. Check Console for:\n";
echo "   ✅ '📦 [INTER-OUTLET] Starting immediate registration...'\n";
echo "   ✅ '🎯 [INTER-OUTLET] Alpine.js already available, registering immediately'\n";
echo "   ✅ '🏪 [INTER-OUTLET] Registering component immediately...'\n";
echo "   ✅ '✅ [INTER-OUTLET] Component registered successfully'\n";
echo "5. Verify NO 'interOutletSaleApp is not defined' errors\n\n";

echo "If this still doesn't work, the issue might be with Alpine.js itself not loading properly.\n";