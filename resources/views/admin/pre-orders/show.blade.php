<x-layouts.admin>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pre Order {{ $preorder->kode_preorder }}</h1>
                <p class="text-gray-600">Detail pre order dan penawaran</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openCoaModal()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    Pengaturan COA
                </button>
                <a href="{{ route('admin.penjualan.preorders.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pre Order Info -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Pre Order</h3>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $preorder->status_badge }}">
                            {{ ucfirst($preorder->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Pre Order</label>
                            <p class="text-gray-900 font-medium">{{ $preorder->kode_preorder }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <p class="text-gray-900">{{ $preorder->tanggal->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <p class="text-gray-900">{{ $preorder->customer->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total</label>
                            <p class="text-gray-900 font-bold text-lg">Rp {{ number_format($preorder->total, 0, ',', '.') }}</p>
                        </div>
                        @if($preorder->dp_amount)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">DP Amount</label>
                            <p class="text-gray-900">Rp {{ number_format($preorder->dp_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sisa Pembayaran</label>
                            <p class="text-gray-900">Rp {{ number_format($preorder->remaining_payment, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($preorder->catatan)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <p class="text-gray-900">{{ $preorder->catatan }}</p>
                    </div>
                    @endif
                </div>

                <!-- Items -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Item Pre Order</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($preorder->items as $item)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product_image)
                                            <img src="{{ $item->product_image }}" 
                                                 alt="{{ $item->deskripsi }}" 
                                                 class="w-12 h-12 object-cover rounded">
                                            @else
                                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">No Img</span>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $item->deskripsi }}</p>
                                                @if($item->product)
                                                <p class="text-xs text-gray-500">{{ $item->product->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ number_format($item->qty, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Rp {{ number_format($preorder->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if($preorder->diskon > 0)
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-900">Diskon:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-red-600">-Rp {{ number_format($preorder->diskon, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($preorder->pajak > 0)
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-900">Pajak:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Rp {{ number_format($preorder->pajak, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900">Rp {{ number_format($preorder->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Actions -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Status</h3>
                    
                    @if($preorder->status === 'penawaran')
                    <button onclick="updateStatus('invoice')" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mb-2">
                        Ubah ke Invoice
                    </button>
                    @elseif($preorder->status === 'invoice')
                    <button onclick="updateStatus('lunas')" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg mb-2">
                        Tandai Lunas
                    </button>
                    @endif
                </div>

                <!-- Print Actions -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cetak Dokumen</h3>
                    
                    <div class="space-y-2">
                        <button onclick="printDocument('penawaran')" 
                                class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                            Cetak Penawaran
                        </button>
                        
                        @if($preorder->status !== 'penawaran')
                        <button onclick="printDocument('invoice')" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Cetak Invoice
                        </button>
                        @endif
                        
                        @if($preorder->status === 'lunas')
                        <button onclick="printDocument('kwitansi')" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            Cetak Kwitansi
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
                <form id="statusForm">
                    @csrf
                    <input type="hidden" id="newStatus" name="status">
                    
                    <div id="dpAmountField" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah DP</label>
                        <input type="number" id="dpAmount" name="dp_amount" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               onchange="showDpCurrencyFormat(this)">
                        <p class="text-xs text-blue-600 mt-1 dp-currency-display" style="display: none;"></p>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeStatusModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- COA Settings Modal -->
    <div id="coaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Chart of Accounts</h3>
                <form id="coaForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">COA Penjualan</label>
                            <select name="coa_penjualan" id="coa_penjualan" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih COA</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">COA Piutang</label>
                            <select name="coa_piutang" id="coa_piutang" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih COA</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">COA Uang Muka</label>
                            <select name="coa_uang_muka" id="coa_uang_muka" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih COA</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">COA Kas/Bank</label>
                            <select name="coa_kas_bank" id="coa_kas_bank" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih COA</option>
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
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Preview Dokumen</h3>
                        <div class="flex gap-2">
                            <button onclick="downloadPdf()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Download PDF
                            </button>
                            <button onclick="closePrintModal()" 
                                    class="text-gray-700 bg-gray-200 rounded-lg px-4 py-2 hover:bg-gray-300">
                                Tutup
                            </button>
                        </div>
                    </div>
                    <div class="border rounded-lg">
                        <iframe id="printFrame" class="w-full h-96"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPrintType = null;

        function updateStatus(status) {
            document.getElementById('newStatus').value = status;
            
            // Show DP amount field if changing to invoice
            const dpField = document.getElementById('dpAmountField');
            if (status === 'invoice') {
                dpField.classList.remove('hidden');
            } else {
                dpField.classList.add('hidden');
            }
            
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusForm').reset();
        }

        function openCoaModal() {
            // Load COAs
            fetch('{{ route("admin.penjualan.preorders.settings.getCoas") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const selects = ['coa_penjualan', 'coa_piutang', 'coa_uang_muka', 'coa_kas_bank'];
                        
                        selects.forEach(selectName => {
                            const select = document.getElementById(selectName);
                            select.innerHTML = '<option value="">Pilih COA</option>';
                            
                            data.coas.forEach(coa => {
                                const option = document.createElement('option');
                                option.value = coa.id;
                                option.textContent = `${coa.kode_akun} - ${coa.nama_akun}`;
                                
                                if (data.settings[selectName] == coa.id) {
                                    option.selected = true;
                                }
                                
                                select.appendChild(option);
                            });
                        });
                    }
                });
            
            document.getElementById('coaModal').classList.remove('hidden');
        }

        function closeCoaModal() {
            document.getElementById('coaModal').classList.add('hidden');
        }

        function printDocument(type) {
            currentPrintType = type;
            const url = `{{ route('admin.penjualan.preorders.print.modal', ['preorder' => $preorder->id, 'type' => 'TYPE']) }}`.replace('TYPE', type);
            
            document.getElementById('printFrame').src = url;
            document.getElementById('printModal').classList.remove('hidden');
        }

        function closePrintModal() {
            document.getElementById('printModal').classList.add('hidden');
            currentPrintType = null;
        }

        function downloadPdf() {
            if (currentPrintType) {
                const url = `{{ route('admin.penjualan.preorders.print.download', ['preorder' => $preorder->id, 'type' => 'TYPE']) }}`.replace('TYPE', currentPrintType);
                window.open(url, '_blank');
            }
        }

        // Form submissions
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("admin.penjualan.preorders.updateStatus", $preorder->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status: formData.get('status'),
                    dp_amount: formData.get('dp_amount')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate status');
            });
        });

        document.getElementById('coaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            fetch('{{ route("admin.penjualan.preorders.settings.store") }}', {
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
                    closeCoaModal();
                    alert('Pengaturan COA berhasil disimpan');
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan pengaturan');
            });
        });

        // Currency format function
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
    </script>
</x-layouts.admin>