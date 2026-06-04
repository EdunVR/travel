<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> Penjualan Antar Outlet <?php $__env->endSlot(); ?>
    
    <?php $__env->startPush('styles'); ?>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-card {
            transition: all 0.2s ease-in-out;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
        }
        
        .product-image {
            transition: transform 0.2s ease-in-out;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        /* Custom scrollbar for modal */
        .modal-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .modal-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <?php $__env->stopPush(); ?>
    
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-4" x-data="interOutletSaleApp()">
        <script>
            // Pass data to JavaScript
            window.selectedOutlet = <?php echo e($selectedOutlet); ?>;
            window.routes = {
                interOutletProducts: '<?php echo e(route('admin.penjualan.inter-outlet.products')); ?>',
                interOutletOutlets: '<?php echo e(route('admin.penjualan.inter-outlet.outlets')); ?>',
                interOutletStore: '<?php echo e(route('admin.penjualan.inter-outlet.store')); ?>',
                interOutletHistory: '<?php echo e(route('admin.penjualan.inter-outlet.history')); ?>',
                interOutletPrint: '<?php echo e(route('admin.penjualan.inter-outlet-sale.print', 0)); ?>',
                interOutletPriceProducts: '<?php echo e(route('admin.penjualan.inter-outlet.price-products')); ?>',
                interOutletUpdatePrice: '<?php echo e(route('admin.penjualan.inter-outlet.update-price')); ?>',
                interOutletBulkUpdatePrices: '<?php echo e(route('admin.penjualan.inter-outlet.bulk-update-prices')); ?>',
                interOutletCoaModalData: '<?php echo e(route('admin.penjualan.inter-outlet.coa-modal-data')); ?>',
                interOutletCoaSettings: '<?php echo e(route('admin.penjualan.inter-outlet.coa-settings')); ?>',
                interOutletCoaSettingsUpdate: '<?php echo e(route('admin.penjualan.inter-outlet.coa-settings')); ?>'
            };
        </script>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Penjualan Antar Outlet</h1>
                    <p class="text-slate-600 mt-1">Kelola transaksi penjualan antar outlet dengan mudah</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Outlet Selector -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-700">Outlet:</label>
                        <select x-model="selectedOutlet" @change="changeOutlet()" 
                                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e($outlet->id_outlet == $selectedOutlet ? 'selected' : ''); ?>>
                                    <?php echo e($outlet->nama_outlet); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <button @click="showHistory = true; loadHistoryData()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors">
                        <i class="bx bx-history text-sm"></i>
                        <span>Riwayat</span>
                    </button>
                    
                    <button @click="showPriceSettings = true; loadPriceProducts()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="bx bx-dollar text-sm"></i>
                        <span>Setting Harga</span>
                    </button>
                    
                    <button @click="showCoaSettings = true; coaSelectedOutlet = selectedOutlet; loadCoaData()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="bx bx-cog text-sm"></i>
                        <span>Setting COA</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Selection -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Pilih Produk</h2>
                        
                        <!-- Search and Filter -->
                        <div class="mt-4 flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <input type="text" x-model="searchProduct" @input="searchProducts()" 
                                       placeholder="Cari produk..." 
                                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <select x-model="categoryFilter" @change="filterProducts()" 
                                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Semua Kategori</option>
                                <template x-for="category in categories" :key="category">
                                    <option :value="category" x-text="category"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="p-6">
                        <div x-show="loading" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="mt-2 text-slate-600">Memuat produk...</p>
                        </div>
                        
                        <div x-show="!loading && filteredProducts.length === 0" class="text-center py-8">
                            <i class="bx bx-package text-4xl text-slate-400"></i>
                            <p class="mt-2 text-slate-600">Tidak ada produk ditemukan</p>
                        </div>
                        
                        <div x-show="!loading && filteredProducts.length > 0" 
                             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <template x-for="product in filteredProducts" :key="product.id_produk">
                                <div class="product-card bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-primary-300 transition-all duration-200 cursor-pointer group relative"
                                     @click="addToCart(product)"
                                     :class="product.stock <= 0 ? 'opacity-60 cursor-not-allowed' : ''">
                                    
                                    <!-- Product Image -->
                                    <div class="relative">
                                        <img :src="product.image || '/img/no-image.png'" 
                                             :alt="product.name"
                                             class="product-image w-full h-32 object-cover bg-slate-100">
                                        
                                        <!-- Status Badge - Top Right -->
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                  :class="product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                <span x-text="product.stock > 0 ? 'Tersedia' : 'Habis'"></span>
                                            </span>
                                        </div>
                                        
                                        <!-- Stock Info - Top Left -->
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                <i class="bx bx-package text-xs mr-1"></i>
                                                <span x-text="product.stock"></span>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <!-- Product Name and SKU -->
                                        <div class="mb-2">
                                            <h3 class="font-semibold text-slate-900 text-sm leading-tight line-clamp-2 group-hover:text-primary-600 transition-colors mb-1" 
                                                x-text="product.name" :title="product.name"></h3>
                                            <p class="text-xs text-slate-500" x-text="product.sku"></p>
                                        </div>
                                        
                                        <!-- Category -->
                                        <div class="mb-2">
                                            <span class="text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded" x-text="product.category"></span>
                                        </div>
                                        
                                        <!-- Price Section - Directly below image info -->
                                        <div class="bg-primary-50 rounded-lg p-2 text-center">
                                            <div class="text-lg font-bold text-primary-600" x-text="formatCurrency(product.price)"></div>
                                            <div class="text-xs text-slate-500">per <span x-text="product.satuan"></span></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart and Transaction -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 sticky top-4">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Keranjang Transaksi</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Transaction Info -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Transaksi</label>
                                <input type="date" x-model="transactionDate" 
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet Tujuan</label>
                                <select x-model="destinationOutlet" 
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Outlet Tujuan</option>
                                    <template x-for="outlet in availableOutlets" :key="outlet.id">
                                        <option :value="outlet.id" x-text="outlet.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Cart Items -->
                        <div class="space-y-3 mb-6">
                            <div x-show="cart.length === 0" class="text-center py-8">
                                <i class="bx bx-cart text-4xl text-slate-400"></i>
                                <p class="mt-2 text-slate-600">Keranjang kosong</p>
                            </div>
                            
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="border border-slate-200 rounded-lg p-3">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-slate-900 text-sm truncate" x-text="item.name"></h4>
                                            <p class="text-xs text-slate-500" x-text="item.sku"></p>
                                        </div>
                                        <button @click="removeFromCart(index)" 
                                                class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="bx bx-trash text-sm"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center border border-slate-300 rounded">
                                            <button @click="decreaseQuantity(index)" 
                                                    class="px-2 py-1 hover:bg-slate-100">-</button>
                                            <input type="number" x-model="item.quantity" @input="updateCartItem(index)"
                                                   class="w-16 px-2 py-1 text-center border-0 focus:ring-0" 
                                                   min="0.01" step="0.01">
                                            <button @click="increaseQuantity(index)" 
                                                    class="px-2 py-1 hover:bg-slate-100">+</button>
                                        </div>
                                        <span class="text-xs text-slate-500" x-text="item.satuan"></span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-sm text-slate-600" x-text="formatCurrency(item.price)"></span>
                                        <span class="font-medium text-slate-900" x-text="formatCurrency(item.subtotal)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Discount and Tax -->
                        <div class="space-y-3 mb-6 border-t border-slate-200 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Subtotal</span>
                                <span class="font-medium" x-text="formatCurrency(subtotal)"></span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-slate-600">Diskon (%)</label>
                                <input type="number" x-model="discountPercent" @input="calculateTotal()"
                                       class="flex-1 px-2 py-1 border border-slate-300 rounded text-sm" 
                                       min="0" max="100" step="0.01">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-slate-600">PPN (%)</label>
                                <input type="number" x-model="taxPercent" @input="calculateTotal()"
                                       class="flex-1 px-2 py-1 border border-slate-300 rounded text-sm" 
                                       min="0" max="100" step="0.01">
                            </div>
                            
                            <div class="flex items-center justify-between font-semibold text-lg border-t border-slate-200 pt-2">
                                <span>Total</span>
                                <span class="text-primary-600" x-text="formatCurrency(total)"></span>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                            <textarea x-model="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Catatan transaksi (opsional)"></textarea>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button @click="processTransaction()" :disabled="!canProcess" 
                                    class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:bg-slate-300 disabled:cursor-not-allowed font-medium transition-colors">
                                <span x-show="!processing">Proses Transaksi</span>
                                <span x-show="processing" class="flex items-center justify-center gap-2">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                    Memproses...
                                </span>
                            </button>
                            
                            <button @click="clearCart()" 
                                    class="w-full px-4 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-700 font-medium transition-colors">
                                Bersihkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div x-show="showHistory" x-cloak 
             class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
             @click.self="showHistory = false">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl my-4">
                <div class="flex items-center justify-between p-6 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Riwayat Transaksi</h2>
                    <button @click="showHistory = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <!-- Filters -->
                    <div class="bg-slate-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Outlet</label>
                                <select x-model="historyOutletFilter" @change="loadHistoryData()" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="all">Semua Outlet</option>
                                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
                                <select x-model="historyStatusFilter" @change="loadHistoryData()" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="all">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                                <input type="date" x-model="historyStartDate" @change="loadHistoryData()" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                                <input type="date" x-model="historyEndDate" @change="loadHistoryData()" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="history-table" class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No. Transaksi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Outlet Asal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Outlet Tujuan</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <template x-for="transaction in historyData" :key="transaction.id">
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-4 text-sm font-medium text-slate-900" x-text="transaction.no_transaksi"></td>
                                            <td class="px-4 py-4 text-sm text-slate-900" x-text="transaction.tanggal_formatted"></td>
                                            <td class="px-4 py-4 text-sm text-slate-900" x-text="transaction.outlet_asal_name"></td>
                                            <td class="px-4 py-4 text-sm text-slate-900" x-text="transaction.outlet_tujuan_name"></td>
                                            <td class="px-4 py-4 text-sm text-slate-900 text-right" x-text="formatCurrency(transaction.total)"></td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                      :class="{
                                                          'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                                          'bg-green-100 text-green-800': transaction.status === 'approved',
                                                          'bg-red-100 text-red-800': transaction.status === 'rejected'
                                                      }"
                                                      x-text="transaction.status"></span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <!-- Print Button -->
                                                    <button @click="printHistoryInvoice(transaction.id)" 
                                                            class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                                            title="Print Invoice">
                                                        <i class="bx bx-printer text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Loading State -->
                        <div x-show="historyLoading" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="mt-2 text-slate-600">Memuat data riwayat...</p>
                        </div>
                        
                        <!-- Empty State -->
                        <div x-show="!historyLoading && historyData.length === 0" class="text-center py-8">
                            <i class="bx bx-receipt text-4xl text-slate-400"></i>
                            <p class="mt-2 text-slate-600">Tidak ada transaksi ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COA Settings Modal -->
        <div x-show="showCoaSettings" x-cloak 
             class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
             @click.self="showCoaSettings = false">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl my-4 flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Setting COA Penjualan Antar Outlet</h2>
                        <p class="text-sm text-slate-600 mt-1">Konfigurasi akun untuk pencatatan transaksi penjualan antar outlet</p>
                    </div>
                    <button @click="showCoaSettings = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                
                <!-- Modal Body - Scrollable -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Loading State -->
                    <div x-show="coaLoading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        <p class="mt-2 text-slate-600">Memuat data...</p>
                    </div>

                    <!-- Form Content -->
                    <div x-show="!coaLoading">
                        <form @submit.prevent="saveCoaSettings()">
                            <!-- Outlet Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
                                <select x-model="coaSelectedOutlet" @change="loadCoaData()" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Outlet</option>
                                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Accounting Book -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Buku Akuntansi <span class="text-red-500">*</span></label>
                                <select x-model="coaData.accounting_book_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Buku Akuntansi</option>
                                    <template x-for="book in coaBooks" :key="book.id">
                                        <option :value="book.id" x-text="book.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Akun Piutang Antar Outlet -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Akun Piutang Antar Outlet <span class="text-red-500">*</span></label>
                                    <select x-model="coaData.akun_piutang_antar_outlet" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Akun</option>
                                        <template x-for="account in coaAccounts" :key="account.id">
                                            <option :value="account.id" x-text="`${account.code} - ${account.name}`"></option>
                                        </template>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat piutang dari outlet tujuan</p>
                                </div>

                                <!-- Akun Pendapatan Antar Outlet -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Akun Pendapatan Antar Outlet <span class="text-red-500">*</span></label>
                                    <select x-model="coaData.akun_pendapatan_antar_outlet" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Akun</option>
                                        <template x-for="account in coaAccounts" :key="account.id">
                                            <option :value="account.id" x-text="`${account.code} - ${account.name}`"></option>
                                        </template>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat pendapatan dari penjualan antar outlet</p>
                                </div>

                                <!-- Akun HPP -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Akun HPP (Harga Pokok Penjualan)</label>
                                    <select x-model="coaData.akun_hpp" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Akun (Opsional)</option>
                                        <template x-for="account in coaAccounts" :key="account.id">
                                            <option :value="account.id" x-text="`${account.code} - ${account.name}`"></option>
                                        </template>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat harga pokok penjualan</p>
                                </div>

                                <!-- Akun Persediaan -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Akun Persediaan</label>
                                    <select x-model="coaData.akun_persediaan" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Akun (Opsional)</option>
                                        <template x-for="account in coaAccounts" :key="account.id">
                                            <option :value="account.id" x-text="`${account.code} - ${account.name}`"></option>
                                        </template>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat pengurangan persediaan</p>
                                </div>

                                <!-- Akun PPN -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Akun PPN</label>
                                    <select x-model="coaData.akun_ppn" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Pilih Akun (Opsional)</option>
                                        <template x-for="account in coaAccounts" :key="account.id">
                                            <option :value="account.id" x-text="`${account.code} - ${account.name}`"></option>
                                        </template>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Akun untuk mencatat PPN keluaran</p>
                                </div>
                            </div>

                            <!-- Information Panel -->
                            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="bx bx-info-circle text-blue-600 text-lg mt-0.5"></i>
                                    <div>
                                        <h4 class="font-medium text-blue-900 mb-2">Informasi Jurnal Otomatis</h4>
                                        <div class="text-sm text-blue-800 space-y-1">
                                            <p><strong>Jurnal yang akan dibuat:</strong></p>
                                            <ul class="list-disc list-inside space-y-1 ml-4">
                                                <li>Piutang Antar Outlet (Debit) - Pendapatan Antar Outlet (Kredit)</li>
                                                <li>HPP (Debit) - Persediaan (Kredit) <em>(jika diatur)</em></li>
                                                <li>PPN Keluaran (Kredit) <em>(jika ada PPN)</em></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Footer - Fixed at bottom -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200 bg-white flex-shrink-0">
                    <button type="button" @click="showCoaSettings = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="resetCoaForm()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors">
                        Reset
                    </button>
                    <button type="button" @click="saveCoaSettings()" :disabled="coaSaving" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50">
                        <span x-show="!coaSaving">Simpan Pengaturan</span>
                        <span x-show="coaSaving">
                            <i class="bx bx-loader-alt animate-spin mr-2"></i>Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Price Settings Modal -->
        <div x-show="showPriceSettings" x-cloak 
             class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
             @click.self="showPriceSettings = false">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl my-4">
                <div class="flex items-center justify-between p-6 border-b border-slate-200">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Setting Harga Produk Inter Outlet</h2>
                        <p class="text-sm text-slate-600 mt-1">Harga khusus untuk penjualan antar outlet (tidak mempengaruhi harga produk umum)</p>
                    </div>
                    <button @click="showPriceSettings = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-6 flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" x-model="priceSearchProduct" @input="filterPriceProducts()" 
                                   placeholder="Cari produk..." 
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <select x-model="priceCategoryFilter" @change="filterPriceProducts()" 
                                class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Kategori</option>
                            <template x-for="category in categories" :key="category">
                                <option :value="category" x-text="category"></option>
                            </template>
                        </select>
                        <button @click="savePriceSettings()" 
                                class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            <i class="bx bx-save mr-2"></i>Simpan Semua
                        </button>
                    </div>
                    
                    <!-- Products Table -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Harga Regular</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">HPP</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Markup (%)</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Harga Inter Outlet</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <template x-for="(product, index) in filteredPriceProducts" :key="product.id_produk">
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <img :src="product.image || '/img/no-image.png'" 
                                                         :alt="product.name"
                                                         class="w-10 h-10 rounded-lg object-cover mr-3">
                                                    <div>
                                                        <div class="text-sm font-medium text-slate-900" x-text="product.name"></div>
                                                        <div class="text-sm text-slate-500" x-text="product.satuan"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-900" x-text="product.sku"></td>
                                            <td class="px-4 py-4 text-sm text-slate-900" x-text="product.category"></td>
                                            <td class="px-4 py-4 text-sm text-slate-500 text-right">
                                                <span x-text="formatCurrency(product.regular_price || 0)"></span>
                                                <div class="text-xs text-slate-400">Harga umum</div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-900 text-right">
                                                <span class="font-medium" x-text="formatCurrency(product.hpp || 0)"></span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" 
                                                       x-model="product.markup_percent" 
                                                       @input="calculateFinalPrice(product)"
                                                       class="w-20 px-2 py-1 text-center border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                       min="0" max="99999999.99" step="0.1" placeholder="0">
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <input type="number" 
                                                       x-model="product.final_price" 
                                                       @input="calculateMarkupFromPrice(product)"
                                                       class="w-32 px-2 py-1 text-right border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                       min="0" max="999999999999.99" step="0.01" placeholder="Harga khusus inter outlet">
                                                <div class="text-xs text-slate-400 mt-1">Khusus inter outlet</div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <button @click="updateSinglePrice(product)" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                                    <i class="bx bx-check text-sm mr-1"></i>Update
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        
                        <div x-show="filteredPriceProducts.length === 0" class="text-center py-8">
                            <i class="bx bx-package text-4xl text-slate-400"></i>
                            <p class="mt-2 text-slate-600">Tidak ada produk ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" x-cloak 
             class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 pt-20"
             @click.self="showSuccessModal = false">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bx bx-check text-3xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Transaksi Berhasil!</h3>
                <p class="text-slate-600 mb-4" x-text="successMessage"></p>
                <div class="flex gap-3 justify-center">
                    <button @click="showSuccessModal = false" 
                            class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">
                        Tutup
                    </button>
                    <button @click="printInvoice()" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Print Invoice
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal PDF -->
        <div x-show="showPdfModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40" x-on:click="showPdfModal = false"></div>
            <div class="relative z-50 w-full max-w-6xl h-[90vh] bg-white rounded-2xl shadow-float overflow-hidden flex flex-col">
                <div class="flex items-center justify-between p-4 border-b border-slate-200">
                    <h3 class="font-semibold">Preview Invoice Inter Outlet</h3>
                    <button x-on:click="showPdfModal = false" class="p-2 hover:bg-slate-100 rounded-lg">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <iframe :src="pdfUrl" class="w-full flex-1"></iframe>
            </div>
        </div>
    </div>
    
    <!-- Include COA Settings Modal -->
    <?php echo $__env->make('admin.penjualan.inter-outlet.coa-settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/inter-outlet.js?v=1769180416')); ?>"></script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\inter-outlet\index.blade.php ENDPATH**/ ?>