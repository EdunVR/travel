<x-layouts.admin>
    <x-slot name="title">Invoice Service</x-slot>

    <div class="container px-6 py-8 mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Invoice Service</h1>
                <p class="mt-1 text-sm text-gray-600">Buat invoice service untuk customer</p>
            </div>
            
            <!-- Outlet Selector & Settings -->
            <div class="flex items-center gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Outlet</label>
                    <select id="outlet-selector" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $selectedOutlet ? 'selected' : '' }}>
                                {{ $outlet->nama_outlet }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6">
                    <button type="button" id="btn-invoice-settings" class="px-4 py-2 text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                        <i class="mr-2 fas fa-cog"></i>Setting Nomor Invoice
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Invoice -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <form id="invoice-form">
                @csrf
                <input type="hidden" name="outlet_id" id="outlet_id" value="{{ $selectedOutlet }}">
                
                <!-- Customer & Mesin Selection -->
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Customer *</label>
                        <div class="relative">
                            <i class="absolute text-gray-400 transform -translate-y-1/2 fas fa-search left-3 top-1/2"></i>
                            <input type="text" 
                                   id="customer-search" 
                                   class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Ketik nama customer untuk mencari..."
                                   autocomplete="off">
                            <input type="hidden" name="id_member" id="id_member" required>
                        </div>
                        <div id="customer-dropdown" class="absolute z-50 hidden w-full mt-1 overflow-hidden bg-white border border-gray-300 rounded-lg shadow-lg max-h-60">
                            <div id="customer-loading" class="hidden px-4 py-3 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin"></i> Mencari...
                            </div>
                            <div id="customer-results" class="overflow-y-auto max-h-60">
                                <!-- Results will be populated here -->
                            </div>
                            <div id="customer-empty" class="hidden px-4 py-3 text-center text-gray-500">
                                Tidak ada customer ditemukan
                            </div>
                        </div>
                        <small class="text-gray-500">Ketik minimal 2 karakter untuk mencari</small>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Mesin Customer (Opsional)</label>
                        <select name="id_mesin_customer" id="id_mesin_customer" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" disabled>
                            <option value="">Pilih Customer terlebih dahulu</option>
                        </select>
                        <small class="text-gray-500">Opsional - Kosongkan jika hanya service umum</small>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Invoice *</label>
                        <input type="date" name="tanggal" id="tanggal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Mulai Service *</label>
                        <input type="date" name="tanggal_mulai_service" id="tanggal_mulai_service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Selesai Service *</label>
                        <input type="date" name="tanggal_selesai_service" id="tanggal_selesai_service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Jenis Service *</label>
                        <select name="jenis_service" id="jenis_service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Jenis Service</option>
                            <option value="Service">Service</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Pembelian Sparepart">Pembelian Sparepart</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Keterangan Service</label>
                        <input type="text" name="keterangan_service" id="keterangan_service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Keterangan tambahan">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal Service Berikutnya (Opsional)</label>
                        <input type="date" name="tanggal_service_berikutnya" id="tanggal_service_berikutnya" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <small class="text-gray-500">Isi jika ada jadwal service berikutnya</small>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Item Service</h3>
                        <div class="flex gap-2">
                            <button type="button" id="add-sparepart" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                                <i class="mr-2 fas fa-cog"></i>Tambah Sparepart
                            </button>
                            <button type="button" id="add-item" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                <i class="mr-2 fas fa-plus"></i>Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300" id="invoice-items">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left border" style="width: 5%;">No</th>
                                    <th class="px-4 py-2 text-left border" style="width: 20%;">Deskripsi</th>
                                    <th class="px-4 py-2 text-left border" style="width: 15%;">Keterangan</th>
                                    <th class="px-4 py-2 text-center border" style="width: 8%;">Qty</th>
                                    <th class="px-4 py-2 text-left border" style="width: 8%;">Satuan</th>
                                    <th class="px-4 py-2 text-right border" style="width: 12%;">Harga</th>
                                    <th class="px-4 py-2 text-right border" style="width: 10%;">Diskon</th>
                                    <th class="px-4 py-2 text-right border" style="width: 12%;">Subtotal</th>
                                    <th class="px-4 py-2 text-center border" style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items-container">
                                <!-- Items will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Teknisi Info (Only for "Service" type) -->
                <div id="teknisi-section" class="hidden grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Jumlah Teknisi</label>
                        <input type="number" name="jumlah_teknisi" id="jumlah_teknisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" min="0">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Jumlah Jam</label>
                        <input type="number" name="jumlah_jam" id="jumlah_jam" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" min="0" step="0.5">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Biaya Teknisi (Auto)</label>
                        <input type="text" name="biaya_teknisi" id="biaya_teknisi" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500" value="0" readonly>
                        <small class="text-gray-500">Rp 25.000 per jam</small>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-700">Subtotal:</span>
                        <span id="subtotal-display" class="font-semibold text-gray-900">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-700">Diskon:</span>
                        <input type="text" name="diskon" id="diskon" class="w-48 px-4 py-2 text-right border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0">
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t">
                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                        <span id="total-display" class="text-2xl font-bold text-blue-600">Rp 0</span>
                    </div>
                    <input type="hidden" name="total_setelah_diskon" id="total_setelah_diskon" value="0">
                </div>

                <!-- Additional Options -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_garansi" id="is_garansi" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Service Garansi (Gratis)</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.service.history.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fas fa-save"></i>Simpan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pilih Sparepart -->
    <div id="modal-pilih-sparepart" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="document.getElementById('modal-pilih-sparepart').classList.add('hidden')"></div>
            
            <div class="relative z-10 w-full max-w-4xl p-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="mr-2 fas fa-cog"></i>Pilih Sparepart
                    </h3>
                    <button type="button" onclick="document.getElementById('modal-pilih-sparepart').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="text-2xl fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <input type="text" id="search-sparepart" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Cari sparepart berdasarkan kode, nama, atau merk...">
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left border" style="width: 5%;">#</th>
                                <th class="px-4 py-2 text-left border">Kode</th>
                                <th class="px-4 py-2 text-left border">Nama Sparepart</th>
                                <th class="px-4 py-2 text-left border">Merk</th>
                                <th class="px-4 py-2 text-right border">Harga</th>
                                <th class="px-4 py-2 text-center border">Stok</th>
                                <th class="px-4 py-2 text-center border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sparepart-search-results">
                            <!-- Results will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Setting Nomor Invoice -->
    <div id="modal-invoice-settings" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="document.getElementById('modal-invoice-settings').classList.add('hidden')"></div>
            
            <div class="relative z-10 w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="mr-2 fas fa-cog"></i>Setting Nomor Invoice
                    </h3>
                    <button type="button" onclick="document.getElementById('modal-invoice-settings').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="text-2xl fas fa-times"></i>
                    </button>
                </div>
                
                <form id="form-invoice-settings">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Prefix Invoice</label>
                        <input type="text" id="invoice_prefix" name="prefix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="BBN.INV" required>
                        <small class="text-gray-500">Contoh: BBN.INV, SRV.INV, SERVICE</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nomor Terakhir</label>
                        <input type="number" id="last_number" name="last_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" min="0" required>
                        <small class="text-gray-500">Nomor invoice berikutnya akan dimulai dari nomor ini + 1</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" id="counter_year" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" min="2020" max="2099" required>
                        <small class="text-gray-500">Counter akan reset otomatis setiap tahun baru</small>
                    </div>
                    
                    <div class="p-4 mb-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="mr-2 fas fa-info-circle"></i>
                            <strong>Preview:</strong> <span id="invoice-preview">001/BBN.INV/XII/2025</span>
                        </p>
                    </div>
                    
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="document.getElementById('modal-invoice-settings').classList.add('hidden')" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            <i class="mr-2 fas fa-save"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Pass Laravel routes to JavaScript
        window.serviceRoutes = {
            searchCustomers: '{{ route("admin.service.search-customers") }}',
            getMesinByMember: '{{ route("service.get-mesin-customer-grouped", ":id") }}',
            storeInvoice: '{{ route("admin.service.invoice.store") }}',
            searchSparepart: '{{ route("admin.inventaris.sparepart.search") }}',
            sparepartDetail: '{{ route("admin.inventaris.sparepart.show", ":id") }}',
            getInvoiceSettings: '{{ route("admin.service.invoice.settings.get") }}',
            saveInvoiceSettings: '{{ route("admin.service.invoice.settings.save") }}'
        };
    </script>
    <script src="{{ asset('js/service-invoice-autocomplete-fixed.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/service-invoice.js') }}?v={{ time() }}"></script>
    @endpush
</x-layouts.admin>
