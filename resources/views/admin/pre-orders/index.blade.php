<x-layouts.admin>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pre Order Management</h1>
                <p class="text-gray-600">Kelola penawaran dan pre order pelanggan</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openCoaModal()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Setting COA
                </button>
                <button onclick="openCreateModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pre Order
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Outlet</label>
                    <select id="outletFilter" onchange="changeOutlet()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($accessibleOutlets as $outlet)
                        <option value="{{ $outlet->id_outlet }}" {{ $selectedOutletId == $outlet->id_outlet ? 'selected' : '' }}>
                            {{ $outlet->nama_outlet }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" id="searchInput" placeholder="Cari kode pre order atau nama customer..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="penawaran">Penawaran</option>
                        <option value="invoice">Invoice</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
                <button onclick="filterData()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
                <button onclick="resetFilter()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Reset
                </button>
            </div>
        </div>

        <!-- Grid View Toggle -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex gap-2">
                    <button onclick="switchView('grid')" id="gridViewBtn" 
                            class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">
                        Grid View
                    </button>
                    <button onclick="switchView('table')" id="tableViewBtn" 
                            class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">
                        Table View
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <span id="totalCount">{{ $preOrders->total() }}</span> Pre Order
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($preOrders as $preOrder)
            <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $preOrder->kode_preorder }}</h3>
                        <p class="text-sm text-gray-600">{{ $preOrder->customer->nama ?? 'N/A' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $preOrder->status === 'penawaran' ? 'bg-yellow-100 text-yellow-800' : 
                           ($preOrder->status === 'invoice' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($preOrder->status) }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tanggal:</span>
                        <span>{{ $preOrder->tanggal->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-medium">Rp {{ number_format($preOrder->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Items:</span>
                        <span>{{ $preOrder->items->count() }} item</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button onclick="viewPreOrder({{ $preOrder->id }})" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm">
                        Detail
                    </button>
                    @if($preOrder->status === 'penawaran')
                    <button onclick="updateStatus({{ $preOrder->id }}, 'invoice')" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                        → Invoice
                    </button>
                    @elseif($preOrder->status === 'invoice')
                    <button onclick="updateStatus({{ $preOrder->id }}, 'lunas')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                        → Lunas
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Table View -->
        <div id="tableView" class="bg-white rounded-lg shadow-sm border overflow-hidden hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Pre Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($preOrders as $preOrder)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $preOrder->kode_preorder }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $preOrder->customer->nama ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $preOrder->tanggal->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $preOrder->status_badge }}">
                                    {{ ucfirst($preOrder->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($preOrder->total, 0, ',', '.') }}</div>
                                @if($preOrder->dp_amount)
                                <div class="text-xs text-gray-500">DP: Rp {{ number_format($preOrder->dp_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $preOrder->items->count() }} item(s)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewPreOrder({{ $preOrder->id }})" 
                                            class="text-blue-600 hover:text-blue-900">Detail</button>
                                    
                                    @if($preOrder->status === 'penawaran')
                                    <button onclick="updateStatus({{ $preOrder->id }}, 'invoice')" 
                                            class="text-green-600 hover:text-green-900">Invoice</button>
                                    @elseif($preOrder->status === 'invoice')
                                    <button onclick="updateStatus({{ $preOrder->id }}, 'lunas')" 
                                            class="text-green-600 hover:text-green-900">Lunas</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data pre order
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($preOrders->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $preOrders->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
                <form id="statusForm">
                    @csrf
                    <input type="hidden" id="preOrderId" name="preorder_id">
                    <input type="hidden" id="newStatus" name="status">
                    
                    <div id="dpAmountField" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah DP</label>
                        <input type="number" id="dpAmount" name="dp_amount" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               onchange="showDpCurrencyFormat(this)">
                        <p class="text-xs text-blue-600 mt-1 dp-currency-display" style="display: none;"></p>
                    </div>
                    
                    <div class="mb-4">
                        <p id="statusMessage" class="text-sm text-gray-600"></p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeStatusModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="preOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Buat Pre Order Baru</h3>
                        <button onclick="closePreOrderModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="preOrderForm" class="space-y-6">
                        @csrf
                        <input type="hidden" id="preOrderIdEdit" name="id">
                        
                        <!-- Customer & Date Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                                <select name="customer_id" id="customerId" required 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Customer</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id_member }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                                <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Items -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium text-gray-900">Item Pre Order</h4>
                                <button type="button" onclick="addItem()" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Tambah Item
                                </button>
                            </div>
                            
                            <div id="itemsContainer">
                                <!-- Items will be added here -->
                            </div>
                            
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Diskon</label>
                                        <input type="number" name="diskon" id="diskon" step="0.01" min="0" value="0"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="calculateTotal()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pajak</label>
                                        <input type="number" name="pajak" id="pajak" step="0.01" min="0" value="0"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="calculateTotal()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                                        <div class="text-lg font-bold text-gray-900 py-2" id="totalAmount">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closePreOrderModal()" 
                                    class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Simpan Pre Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Detail Pre Order</h3>
                        <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="detailContent">
                        <!-- Detail content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Selection Modal -->
    <div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Produk</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($products as $product)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                             onclick="selectProduct({{ $product->id_produk }}, '{{ $product->nama_produk }}', {{ $product->harga_jual ?? 0 }})">
                            @if($product->gambar)
                            <img src="{{ asset('storage/' . $product->gambar) }}" 
                                 alt="{{ $product->nama_produk }}" 
                                 class="w-full h-32 object-cover rounded mb-2">
                            @else
                            <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                            @endif
                            <h4 class="font-medium text-gray-900">{{ $product->nama_produk }}</h4>
                            <p class="text-sm text-gray-600">Rp {{ number_format($product->harga_jual ?? 0, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeProductModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        let currentItemIndex = null;
        let currentView = 'grid';

        // View switching
        function switchView(view) {
            currentView = view;
            const gridView = document.getElementById('gridView');
            const tableView = document.getElementById('tableView');
            const gridBtn = document.getElementById('gridViewBtn');
            const tableBtn = document.getElementById('tableViewBtn');

            if (view === 'grid') {
                gridView.classList.remove('hidden');
                tableView.classList.add('hidden');
                gridBtn.classList.add('bg-blue-600', 'text-white');
                gridBtn.classList.remove('bg-gray-200', 'text-gray-700');
                tableBtn.classList.add('bg-gray-200', 'text-gray-700');
                tableBtn.classList.remove('bg-blue-600', 'text-white');
            } else {
                gridView.classList.add('hidden');
                tableView.classList.remove('hidden');
                tableBtn.classList.add('bg-blue-600', 'text-white');
                tableBtn.classList.remove('bg-gray-200', 'text-gray-700');
                gridBtn.classList.add('bg-gray-200', 'text-gray-700');
                gridBtn.classList.remove('bg-blue-600', 'text-white');
            }
        }

        // Modal functions
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Buat Pre Order Baru';
            document.getElementById('preOrderForm').reset();
            document.getElementById('preOrderIdEdit').value = '';
            document.getElementById('itemsContainer').innerHTML = '';
            itemIndex = 0;
            addItem();
            loadCustomersAndProducts(); // Load data for current outlet
            document.getElementById('preOrderModal').classList.remove('hidden');
        }

        // Load customers and products based on selected outlet
        function loadCustomersAndProducts() {
            const outletId = document.getElementById('outletFilter').value;
            
            if (!outletId) return;
            
            // Load customers
            fetch(`{{ route('admin.penjualan.preorders.customers-by-outlet') }}?outlet_id=${outletId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const customerSelect = document.getElementById('customerId');
                        customerSelect.innerHTML = '<option value="">Pilih Customer</option>';
                        data.customers.forEach(customer => {
                            customerSelect.innerHTML += `<option value="${customer.id_member}">${customer.nama}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error loading customers:', error));
            
            // Load products for product modal
            fetch(`{{ route('admin.penjualan.preorders.products-by-outlet') }}?outlet_id=${outletId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateProductModal(data.products);
                    }
                })
                .catch(error => console.error('Error loading products:', error));
        }

        function updateProductModal(products) {
            const productGrid = document.querySelector('#productModal .grid');
            if (!productGrid) return;
            
            productGrid.innerHTML = '';
            products.forEach(product => {
                const productHtml = `
                    <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                         onclick="selectProduct(${product.id_produk}, '${product.nama_produk}', ${product.harga_jual || 0})">
                        ${product.gambar ? 
                            `<img src="{{ asset('storage/') }}/${product.gambar}" 
                                 alt="${product.nama_produk}" 
                                 class="w-full h-32 object-cover rounded mb-2">` :
                            `<div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>`
                        }
                        <h4 class="font-medium text-gray-900">${product.nama_produk}</h4>
                        <p class="text-sm text-gray-600">Rp ${(product.harga_jual || 0).toLocaleString('id-ID')}</p>
                    </div>
                `;
                productGrid.innerHTML += productHtml;
            });
        }

        function closePreOrderModal() {
            document.getElementById('preOrderModal').classList.add('hidden');
        }

        function openStatusModal(preOrderId, newStatus) {
            document.getElementById('preOrderId').value = preOrderId;
            document.getElementById('newStatus').value = newStatus;
            
            const dpField = document.getElementById('dpAmountField');
            const message = document.getElementById('statusMessage');
            
            if (newStatus === 'invoice') {
                dpField.classList.remove('hidden');
                message.textContent = 'Mengubah status ke Invoice. Anda dapat menambahkan jumlah DP jika diperlukan.';
            } else {
                dpField.classList.add('hidden');
                message.textContent = `Mengubah status ke ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}.`;
            }
            
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        function updateStatus(preOrderId, newStatus) {
            openStatusModal(preOrderId, newStatus);
        }

        function viewPreOrder(preOrderId) {
            // Load detail via AJAX
            fetch(`{{ url('admin/penjualan/preorders') }}/${preOrderId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('detailContent').innerHTML = html;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail pre order');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Item management
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
                <div class="border rounded-lg p-4 mb-4" id="item-${itemIndex}">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Item ${itemIndex + 1}</h4>
                        <button type="button" onclick="removeItem(${itemIndex})" 
                                class="text-red-600 hover:text-red-800 text-sm">
                            Hapus
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                            <div class="flex gap-2">
                                <input type="hidden" name="items[${itemIndex}][produk_id]" id="produk_id_${itemIndex}">
                                <input type="text" id="produk_name_${itemIndex}" readonly 
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50"
                                       placeholder="Pilih produk...">
                                <button type="button" onclick="openProductModal(${itemIndex})" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Pilih
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi *</label>
                            <input type="text" name="items[${itemIndex}][deskripsi]" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty *</label>
                            <input type="number" name="items[${itemIndex}][qty]" step="0.01" min="0.01" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateItemSubtotal(${itemIndex})">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga *</label>
                            <input type="number" name="items[${itemIndex}][harga]" step="0.01" min="0" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateItemSubtotal(${itemIndex})">
                        </div>
                    </div>
                    
                    <!-- Additional Costs Section (Hidden until product selected) -->
                    <div id="additional_costs_${itemIndex}" class="mt-4 hidden">
                        <div class="border-t pt-4">
                            <h5 class="font-medium text-gray-900 mb-3">Biaya Tambahan</h5>
                            
                            <!-- Material Instalasi -->
                            <div class="bg-blue-50 p-4 rounded-lg mb-3">
                                <h6 class="font-medium text-gray-900 mb-2">Material Instalasi</h6>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya</label>
                                        <input type="number" name="items[${itemIndex}][material_instalasi_biaya]" 
                                               step="0.01" min="0" value="0"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="calculateItemSubtotal(${itemIndex})"
                                               oninput="showCurrencyFormat(this)"
                                               placeholder="0">
                                        <p class="text-xs text-blue-600 mt-1 currency-display" style="display: none;"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                        <input type="text" name="items[${itemIndex}][material_instalasi_satuan]" 
                                               value="lot"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                        <input type="text" name="items[${itemIndex}][material_instalasi_keterangan]" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Keterangan...">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Biaya Pemasangan dan Pelatihan -->
                            <div class="bg-green-50 p-4 rounded-lg mb-3">
                                <h6 class="font-medium text-gray-900 mb-2">Biaya Pemasangan dan Pelatihan</h6>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya</label>
                                        <input type="number" name="items[${itemIndex}][pemasangan_pelatihan_biaya]" 
                                               step="0.01" min="0" value="0"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="calculateItemSubtotal(${itemIndex})"
                                               oninput="showCurrencyFormat(this)"
                                               placeholder="0">
                                        <p class="text-xs text-blue-600 mt-1 currency-display" style="display: none;"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                        <input type="text" name="items[${itemIndex}][pemasangan_pelatihan_satuan]" 
                                               value="orang"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                        <input type="text" name="items[${itemIndex}][pemasangan_pelatihan_keterangan]" 
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Keterangan...">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ongkos Kirim -->
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <h6 class="font-medium text-gray-900">Ongkos Kirim</h6>
                                    <button type="button" onclick="addOngkirKomponen(${itemIndex})" 
                                            class="text-sm bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded">
                                        + Komponen
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Biaya</label>
                                        <input type="number" name="items[${itemIndex}][ongkos_kirim_biaya]" 
                                               id="ongkir_total_${itemIndex}"
                                               step="0.01" min="0" value="0"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="calculateItemSubtotal(${itemIndex})"
                                               oninput="showCurrencyFormat(this)"
                                               placeholder="0">
                                        <p class="text-xs text-blue-600 mt-1 currency-display" style="display: none;"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                                        <input type="text" name="items[${itemIndex}][ongkos_kirim_satuan]" 
                                               value="unit"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <!-- Komponen Ongkir -->
                                <div id="ongkir_komponen_${itemIndex}" class="space-y-2">
                                    <!-- Komponen will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="text-right space-y-1">
                            <div>
                                <span class="text-sm text-gray-600">Subtotal Produk: </span>
                                <span class="font-medium" id="subtotal_produk_${itemIndex}">Rp 0</span>
                            </div>
                            <div id="biaya_tambahan_display_${itemIndex}" class="hidden">
                                <span class="text-sm text-gray-600">Biaya Tambahan: </span>
                                <span class="font-medium text-blue-600" id="biaya_tambahan_${itemIndex}">Rp 0</span>
                            </div>
                            <div class="border-t pt-1">
                                <span class="text-sm text-gray-600">Total Item: </span>
                                <span class="font-bold text-lg" id="subtotal_${itemIndex}">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
        }

        function removeItem(index) {
            document.getElementById(`item-${index}`).remove();
            calculateTotal();
        }

        function openProductModal(index) {
            currentItemIndex = index;
            document.getElementById('productModal').classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
            currentItemIndex = null;
        }

        function selectProduct(productId, productName, price) {
            if (currentItemIndex !== null) {
                document.getElementById(`produk_id_${currentItemIndex}`).value = productId;
                document.getElementById(`produk_name_${currentItemIndex}`).value = productName;
                document.querySelector(`input[name="items[${currentItemIndex}][deskripsi]"]`).value = productName;
                document.querySelector(`input[name="items[${currentItemIndex}][harga]"]`).value = price;
                
                // Show additional costs section
                const additionalCostsSection = document.getElementById(`additional_costs_${currentItemIndex}`);
                if (additionalCostsSection) {
                    additionalCostsSection.classList.remove('hidden');
                }
                
                calculateItemSubtotal(currentItemIndex);
            }
            closeProductModal();
        }

        function calculateItemSubtotal(index) {
            const qty = parseFloat(document.querySelector(`input[name="items[${index}][qty]"]`).value) || 0;
            const harga = parseFloat(document.querySelector(`input[name="items[${index}][harga]"]`).value) || 0;
            const subtotalProduk = qty * harga;
            
            // Calculate additional costs
            const materialInstalasi = parseFloat(document.querySelector(`input[name="items[${index}][material_instalasi_biaya]"]`)?.value) || 0;
            const pemasanganPelatihan = parseFloat(document.querySelector(`input[name="items[${index}][pemasangan_pelatihan_biaya]"]`)?.value) || 0;
            const ongkosKirim = parseFloat(document.querySelector(`input[name="items[${index}][ongkos_kirim_biaya]"]`)?.value) || 0;
            
            const totalBiayaTambahan = materialInstalasi + pemasanganPelatihan + ongkosKirim;
            const totalItem = subtotalProduk + totalBiayaTambahan;
            
            // Update displays
            const subtotalProdukEl = document.getElementById(`subtotal_produk_${index}`);
            const biayaTambahanEl = document.getElementById(`biaya_tambahan_${index}`);
            const biayaTambahanDisplayEl = document.getElementById(`biaya_tambahan_display_${index}`);
            const subtotalEl = document.getElementById(`subtotal_${index}`);
            
            if (subtotalProdukEl) {
                subtotalProdukEl.textContent = 'Rp ' + subtotalProduk.toLocaleString('id-ID');
            }
            
            if (biayaTambahanEl && biayaTambahanDisplayEl) {
                if (totalBiayaTambahan > 0) {
                    biayaTambahanEl.textContent = 'Rp ' + totalBiayaTambahan.toLocaleString('id-ID');
                    biayaTambahanDisplayEl.classList.remove('hidden');
                } else {
                    biayaTambahanDisplayEl.classList.add('hidden');
                }
            }
            
            if (subtotalEl) {
                subtotalEl.textContent = 'Rp ' + totalItem.toLocaleString('id-ID');
            }
            
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            
            // Calculate subtotal from all items (including additional costs)
            document.querySelectorAll('[id^="subtotal_"]:not([id*="produk"]):not([id*="tambahan"])').forEach(element => {
                const value = element.textContent.replace(/[^\d]/g, '');
                subtotal += parseFloat(value) || 0;
            });
            
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const pajak = parseFloat(document.getElementById('pajak').value) || 0;
            const total = subtotal - diskon + pajak;
            
            document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Form submissions
        document.getElementById('preOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                if (key.includes('[')) {
                    // Handle nested array fields (like ongkir_komponen)
                    const nestedMatches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]\[(\d+)\]\[(\w+)\]/);
                    if (nestedMatches) {
                        const [, arrayName, itemIndex, fieldName, komponenIndex, komponenField] = nestedMatches;
                        if (!data[arrayName]) data[arrayName] = {};
                        if (!data[arrayName][itemIndex]) data[arrayName][itemIndex] = {};
                        if (!data[arrayName][itemIndex][fieldName]) data[arrayName][itemIndex][fieldName] = {};
                        if (!data[arrayName][itemIndex][fieldName][komponenIndex]) data[arrayName][itemIndex][fieldName][komponenIndex] = {};
                        data[arrayName][itemIndex][fieldName][komponenIndex][komponenField] = value;
                    } else {
                        // Handle regular array fields
                        const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                        if (matches) {
                            const [, arrayName, index, field] = matches;
                            if (!data[arrayName]) data[arrayName] = {};
                            if (!data[arrayName][index]) data[arrayName][index] = {};
                            data[arrayName][index][field] = value;
                        }
                    }
                } else {
                    data[key] = value;
                }
            }
            
            // Convert items object to array and process ongkir_komponen
            if (data.items) {
                data.items = Object.values(data.items).map(item => {
                    if (item.ongkir_komponen) {
                        // Convert ongkir_komponen object to array
                        item.ongkir_komponen = Object.values(item.ongkir_komponen);
                    }
                    return item;
                });
            }
            
            // Add current outlet_id
            data.outlet_id = document.getElementById('outletFilter').value;
            
            const url = data.id ? `{{ url('admin/penjualan/preorders') }}/${data.id}` : '{{ route("admin.penjualan.preorders.store") }}';
            const method = data.id ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closePreOrderModal();
                    location.reload(); // Refresh page to show updated data
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    if (data.errors) {
                        console.log('Validation errors:', data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            });
        });

        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const preOrderId = formData.get('preorder_id');
            
            fetch(`{{ url('admin/penjualan/preorders') }}/${preOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeStatusModal();
                    location.reload(); // Refresh page to show updated status
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate status');
            });
        });

        // Outlet change function
        function changeOutlet() {
            const outletId = document.getElementById('outletFilter').value;
            const params = new URLSearchParams(window.location.search);
            params.set('outlet_id', outletId);
            window.location.href = '{{ route("admin.penjualan.preorders.index") }}' + '?' + params.toString();
        }

        // COA Modal functions
        function openCoaModal() {
            // Load COA settings
            fetch('{{ route("admin.penjualan.preorders.settings.getCoas") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCoaModal(data.coas, data.settings);
                    } else {
                        alert('Gagal memuat data COA');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat COA');
                });
        }

        function showCoaModal(coas, settings) {
            const modalHtml = `
                <div id="coaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Setting Chart of Accounts</h3>
                                <button onclick="closeCoaModal()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <form id="coaForm">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Piutang</label>
                                        <select name="coa_piutang" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Akun Piutang</option>
                                            ${coas.map(coa => `<option value="${coa.id}" ${settings?.coa_piutang == coa.id ? 'selected' : ''}>${coa.code} - ${coa.name}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Pendapatan</label>
                                        <select name="coa_penjualan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Akun Pendapatan</option>
                                            ${coas.map(coa => `<option value="${coa.id}" ${settings?.coa_penjualan == coa.id ? 'selected' : ''}>${coa.code} - ${coa.name}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Kas/Bank</label>
                                        <select name="coa_kas_bank" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Akun Kas/Bank</option>
                                            ${coas.map(coa => `<option value="${coa.id}" ${settings?.coa_kas_bank == coa.id ? 'selected' : ''}>${coa.code} - ${coa.name}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Uang Muka</label>
                                        <select name="coa_uang_muka" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Akun Uang Muka</option>
                                            ${coas.map(coa => `<option value="${coa.id}" ${settings?.coa_uang_muka == coa.id ? 'selected' : ''}>${coa.code} - ${coa.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" onclick="closeCoaModal()" 
                                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                        Batal
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Simpan Setting
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Add form submit handler
            document.getElementById('coaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route("admin.penjualan.preorders.settings.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeCoaModal();
                        alert('Setting COA berhasil disimpan');
                    } else {
                        alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan setting');
                });
            });
        }

        function closeCoaModal() {
            const modal = document.getElementById('coaModal');
            if (modal) {
                modal.remove();
            }
        }

        // Filter functions
        function filterData() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const outletId = document.getElementById('outletFilter').value;
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (status) params.append('status', status);
            if (outletId) params.append('outlet_id', outletId);
            
            window.location.href = '{{ route("admin.penjualan.preorders.index") }}' + (params.toString() ? '?' + params.toString() : '');
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            // Keep outlet filter as it's based on user access
            window.location.href = '{{ route("admin.penjualan.preorders.index") }}';
        }

        // Print document function
        function printDocument(type, preOrderId) {
            const url = `{{ url('admin/penjualan/preorders') }}/${preOrderId}/print/${type}`;
            window.open(url, '_blank');
        }

        // Ongkir komponen management
        let ongkirKomponenIndex = {};
        
        function addOngkirKomponen(itemIndex) {
            if (!ongkirKomponenIndex[itemIndex]) {
                ongkirKomponenIndex[itemIndex] = 0;
            }
            
            const container = document.getElementById(`ongkir_komponen_${itemIndex}`);
            const komponenIndex = ongkirKomponenIndex[itemIndex];
            
            const komponenHtml = `
                <div class="flex gap-2 items-end" id="ongkir_komponen_${itemIndex}_${komponenIndex}">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Komponen</label>
                        <input type="text" name="items[${itemIndex}][ongkir_komponen][${komponenIndex}][nama]" 
                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                               placeholder="Contoh: Fuso, Forklift">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Biaya</label>
                        <input type="number" name="items[${itemIndex}][ongkir_komponen][${komponenIndex}][biaya]" 
                               step="0.01" min="0" value="0"
                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                               onchange="updateOngkirTotal(${itemIndex})"
                               oninput="showCurrencyFormat(this)"
                               placeholder="0">
                    </div>
                    <button type="button" onclick="removeOngkirKomponen(${itemIndex}, ${komponenIndex})" 
                            class="text-red-600 hover:text-red-800 text-sm px-2 py-1">
                        Hapus
                    </button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', komponenHtml);
            ongkirKomponenIndex[itemIndex]++;
        }
        
        function removeOngkirKomponen(itemIndex, komponenIndex) {
            const element = document.getElementById(`ongkir_komponen_${itemIndex}_${komponenIndex}`);
            if (element) {
                element.remove();
                updateOngkirTotal(itemIndex);
            }
        }
        
        function updateOngkirTotal(itemIndex) {
            let total = 0;
            const komponenInputs = document.querySelectorAll(`input[name^="items[${itemIndex}][ongkir_komponen]"][name$="[biaya]"]`);
            
            komponenInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            const totalInput = document.getElementById(`ongkir_total_${itemIndex}`);
            if (totalInput) {
                totalInput.value = total;
                calculateItemSubtotal(itemIndex);
            }
        }

        // Currency format functions
        function showCurrencyFormat(input) {
            const value = parseFloat(input.value);
            const displayElement = input.parentNode.querySelector('.currency-display');
            
            if (displayElement) {
                if (value > 0) {
                    const formatted = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                    
                    displayElement.textContent = '≈ ' + formatted;
                    displayElement.style.display = 'block';
                } else {
                    displayElement.style.display = 'none';
                }
            }
        }

        function showDpCurrencyFormat(input) {
            const value = parseFloat(input.value);
            const displayElement = input.parentNode.querySelector('.dp-currency-display');
            
            if (value > 0) {
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
                
                displayElement.textContent = '≈ ' + formatted;
                displayElement.style.display = 'block';
            } else {
                displayElement.style.display = 'none';
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial view
            switchView('grid');
        });
    </script>
</x-layouts.admin>