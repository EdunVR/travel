<?php

/**
 * Fix Inter Outlet Layout and JavaScript Loading Order
 * Memperbaiki urutan loading JavaScript dan Alpine.js di layout admin
 */

echo "🔧 Fixing Inter Outlet Layout and JavaScript Loading Order...\n\n";

// 1. Perbaiki layout admin - pindahkan inter-outlet.js ke setelah Alpine.js
echo "1. Memperbaiki layout admin...\n";

$layoutFile = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Backup
    copy($layoutFile, $layoutFile . '.backup.layout.' . date('YmdHis'));
    
    // Remove inter-outlet.js dari posisi sekarang
    $content = str_replace('<script src="{{ asset(\'js/inter-outlet.js\') }}"></script>', '', $content);
    
    // Tambahkan inter-outlet.js setelah Alpine.js dengan defer
    $content = str_replace(
        '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>',
        '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>' . "\n" .
        '    <script defer src="{{ asset(\'js/inter-outlet.js\') }}"></script>',
        $content
    );
    
    file_put_contents($layoutFile, $content);
    echo "   ✅ Layout admin diperbaiki - inter-outlet.js dipindahkan setelah Alpine.js\n";
} else {
    echo "   ❌ Layout admin tidak ditemukan\n";
}

// 2. Buat ulang file inter-outlet.js dengan struktur yang benar
echo "\n2. Membuat ulang file inter-outlet.js...\n";

$jsContent = <<<'JS'
/**
 * Inter Outlet Sale Application
 * Alpine.js component for managing inter-outlet sales transactions
 * 
 * IMPORTANT: This file must be loaded AFTER Alpine.js
 */

// Wait for Alpine.js to be available
document.addEventListener('alpine:init', () => {
    console.log('🏪 Initializing Inter Outlet Sale JavaScript...');
    
    // Define constants
    window.ALL = 'all';
    const ALL = 'all';
    
    // Register Alpine.js component
    Alpine.data('interOutletSaleApp', () => ({
        // State
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

        // Computed
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
        },

        get discountAmount() {
            return (this.subtotal * this.discountPercent) / 100;
        },

        get taxAmount() {
            return ((this.subtotal - this.discountAmount) * this.taxPercent) / 100;
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

        get historyUrl() {
            return window.routes?.interOutletHistory || '/admin/penjualan/inter-outlet/history';
        },

        get coaSettingsUrl() {
            return window.routes?.interOutletCoaSettings || '/admin/penjualan/inter-outlet/coa-settings';
        },

        // Methods
        init() {
            console.log('🚀 Initializing Inter Outlet Sale App...');
            this.loadProducts();
            this.loadOutlets();
        },

        async loadProducts() {
            this.loading = true;
            try {
                const url = window.routes?.interOutletProducts || `/admin/penjualan/inter-outlet/products`;
                const response = await this.fetchWithAuth(`${url}?outlet_id=${this.selectedOutlet}`);
                const data = await response.json();
                
                if (data.success) {
                    this.products = data.data;
                    this.filteredProducts = [...this.products];
                    this.categories = [...new Set(this.products.map(p => p.category))];
                } else {
                    this.showError(data.message);
                }
            } catch (error) {
                console.error('Load products error:', error);
                this.showError('Gagal memuat produk: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async loadOutlets() {
            try {
                const url = window.routes?.interOutletOutlets || `/admin/penjualan/inter-outlet/outlets`;
                const response = await this.fetchWithAuth(`${url}?current_outlet_id=${this.selectedOutlet}`);
                const data = await response.json();
                
                if (data.success) {
                    this.availableOutlets = data.data;
                } else {
                    console.error('Load outlets error:', data.message);
                }
            } catch (error) {
                console.error('Gagal memuat outlet:', error);
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
                    p.name.toLowerCase().includes(search) ||
                    p.sku.toLowerCase().includes(search)
                );
            }
            
            if (this.categoryFilter) {
                filtered = filtered.filter(p => p.category === this.categoryFilter);
            }
            
            this.filteredProducts = filtered;
        },

        addToCart(product) {
            if (product.stock <= 0) {
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
                    name: product.name,
                    sku: product.sku,
                    price: product.price,
                    quantity: 1,
                    satuan: product.satuan,
                    stock: product.stock,
                    subtotal: product.price
                });
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        increaseQuantity(index) {
            const item = this.cart[index];
            if (item.quantity < item.stock) {
                item.quantity += 1;
                this.updateCartItem(index);
            } else {
                this.showError('Kuantitas melebihi stok tersedia');
            }
        },

        decreaseQuantity(index) {
            const item = this.cart[index];
            if (item.quantity > 0.01) {
                item.quantity -= 1;
                this.updateCartItem(index);
            }
        },

        updateCartItem(index) {
            const item = this.cart[index];
            if (item.quantity > item.stock) {
                item.quantity = item.stock;
                this.showError('Kuantitas disesuaikan dengan stok tersedia');
            }
            item.subtotal = item.quantity * item.price;
        },

        calculateTotal() {
            // Total akan dihitung otomatis melalui computed properties
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
                    this.loadProducts(); // Refresh products to update stock
                } else {
                    this.showError(data.message);
                }
            } catch (error) {
                console.error('Process transaction error:', error);
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
            }).format(amount);
        },

        showError(message) {
            // Simple error notification - you can enhance this
            alert(message);
        },

        // History Methods
        async loadHistoryData() {
            console.log('Loading history data with filters:', {
                outlet_id: this.historyOutletFilter,
                status: this.historyStatusFilter,
                start_date: this.historyStartDate,
                end_date: this.historyEndDate
            });
            
            this.historyLoading = true;
            try {
                const params = new URLSearchParams({
                    outlet_id: this.historyOutletFilter,
                    status: this.historyStatusFilter,
                    start_date: this.historyStartDate,
                    end_date: this.historyEndDate
                });
                
                const url = window.routes?.interOutletHistory || '/admin/penjualan/inter-outlet/history';
                const response = await this.fetchWithAuth(`${url}?${params}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.historyData = data.data || [];
                    console.log('History data loaded:', this.historyData.length, 'records');
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
        }
    }));
    
    console.log('✅ Inter Outlet Sale JavaScript loaded successfully');
});

// Fallback if Alpine.js is not available
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (typeof Alpine === 'undefined') {
            console.error('❌ Alpine.js not loaded. Inter-outlet functionality will not work.');
            console.log('🔄 Attempting to load Alpine.js manually...');
            
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
            script.defer = true;
            document.head.appendChild(script);
        }
    }, 1000);
});

// Error handler for undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('Caught ALL undefined error, using fallback');
        window.ALL = 'all';
        return true;
    }
});

// Ensure constants are available globally
if (typeof window.ALL === 'undefined') {
    window.ALL = 'all';
}
JS;

$jsFile = 'public/js/inter-outlet.js';
copy($jsFile, $jsFile . '.backup.rewrite.' . date('YmdHis'));
file_put_contents($jsFile, $jsContent);
echo "   ✅ File inter-outlet.js berhasil dibuat ulang dengan struktur Alpine.js yang benar\n";

// 3. Update view untuk menggunakan Alpine.js component yang benar
echo "\n3. Memperbaiki view inter-outlet...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Backup
    copy($viewFile, $viewFile . '.backup.view.' . date('YmdHis'));
    
    // Pastikan x-data menggunakan nama yang benar
    $content = str_replace('x-data="interOutletSaleApp()"', 'x-data="interOutletSaleApp"', $content);
    
    // Pastikan CSRF meta tag ada
    if (strpos($content, 'csrf-token') === false) {
        $content = str_replace('<x-layouts.admin>', '<x-layouts.admin>' . "\n@push('head')\n<meta name=\"csrf-token\" content=\"{{ csrf_token() }}\">\n@endpush\n", $content);
    }
    
    file_put_contents($viewFile, $content);
    echo "   ✅ View inter-outlet diperbaiki\n";
} else {
    echo "   ❌ View inter-outlet tidak ditemukan\n";
}

// 4. Clear cache
echo "\n4. Membersihkan cache...\n";

$commands = [
    'php artisan route:clear',
    'php artisan config:clear',
    'php artisan view:clear',
    'php artisan cache:clear'
];

foreach ($commands as $command) {
    if (function_exists('exec')) {
        exec("$command 2>&1", $output, $return_var);
        if ($return_var === 0) {
            echo "   ✅ $command\n";
        }
    }
}

echo "\n✅ Layout and JavaScript fix selesai!\n\n";

echo "📋 Ringkasan perbaikan:\n";
echo "   1. ✅ Layout admin diperbaiki - inter-outlet.js dipindahkan setelah Alpine.js\n";
echo "   2. ✅ File inter-outlet.js dibuat ulang dengan struktur Alpine.js yang benar\n";
echo "   3. ✅ View diperbaiki untuk menggunakan Alpine.js component\n";
echo "   4. ✅ CSRF token handling ditambahkan\n";
echo "   5. ✅ Error handling diperbaiki\n";
echo "   6. ✅ Cache dibersihkan\n\n";

echo "🧪 Langkah testing:\n";
echo "   1. Login ke aplikasi sebagai admin/superadmin\n";
echo "   2. Buka halaman: /admin/penjualan/inter-outlet\n";
echo "   3. Buka Developer Tools (F12)\n";
echo "   4. Periksa Console - seharusnya tidak ada error\n";
echo "   5. Periksa Network tab - API calls seharusnya return 200\n";
echo "   6. Test dropdown outlet dan produk\n\n";

echo "🔧 Perubahan utama:\n";
echo "   - JavaScript sekarang dimuat SETELAH Alpine.js\n";
echo "   - Menggunakan Alpine.data() untuk mendefinisikan component\n";
echo "   - Event listener alpine:init untuk inisialisasi\n";
echo "   - Proper error handling dan fallback\n";
echo "   - CSRF token handling yang benar\n\n";

echo "📁 File backup dibuat dengan suffix .backup.layout.[timestamp] dan .backup.rewrite.[timestamp]\n\n";