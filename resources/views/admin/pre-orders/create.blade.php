<x-layouts.admin>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buat Pre Order Baru</h1>
                <p class="text-gray-600">Buat penawaran atau pre order untuk pelanggan</p>
            </div>
            <a href="{{ route('admin.penjualan.preorders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form id="preOrderForm" class="space-y-6">
            @csrf
            
            <!-- Customer & Date Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                        <select name="customer_id" required 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id_member }}">{{ $customer->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Item Pre Order</h3>
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
                            <input type="number" name="diskon" step="0.01" min="0" value="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pajak</label>
                            <input type="number" name="pajak" step="0.01" min="0" value="0"
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
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                <textarea name="catatan" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan..."></textarea>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.penjualan.preorders.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Pre Order
                </button>
            </div>
        </form>
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
                    <div class="mt-3">
                        <div class="text-right">
                            <span class="text-sm text-gray-600">Subtotal: </span>
                            <span class="font-medium" id="subtotal_${itemIndex}">Rp 0</span>
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
                calculateItemSubtotal(currentItemIndex);
            }
            closeProductModal();
        }

        function calculateItemSubtotal(index) {
            const qty = parseFloat(document.querySelector(`input[name="items[${index}][qty]"]`).value) || 0;
            const harga = parseFloat(document.querySelector(`input[name="items[${index}][harga]"]`).value) || 0;
            const subtotal = qty * harga;
            
            document.getElementById(`subtotal_${index}`).textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            
            // Calculate subtotal from all items
            document.querySelectorAll('[id^="subtotal_"]').forEach(element => {
                const value = element.textContent.replace(/[^\d]/g, '');
                subtotal += parseFloat(value) || 0;
            });
            
            const diskon = parseFloat(document.querySelector('input[name="diskon"]').value) || 0;
            const pajak = parseFloat(document.querySelector('input[name="pajak"]').value) || 0;
            const total = subtotal - diskon + pajak;
            
            document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        // Form submission
        document.getElementById('preOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                if (key.includes('[')) {
                    // Handle array fields
                    const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                    if (matches) {
                        const [, arrayName, index, field] = matches;
                        if (!data[arrayName]) data[arrayName] = {};
                        if (!data[arrayName][index]) data[arrayName][index] = {};
                        data[arrayName][index][field] = value;
                    }
                } else {
                    data[key] = value;
                }
            }
            
            // Convert items object to array
            if (data.items) {
                data.items = Object.values(data.items);
            }
            
            fetch('{{ route("admin.penjualan.preorders.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '{{ route("admin.penjualan.preorders.index") }}';
                    }
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
    </script>
</x-layouts.admin>