{{-- Create Modal --}}
<div x-show="showCreateModal" 
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
     @click.self="showCreateModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-4 flex flex-col"
         x-data="createPermintaanApp()"
         x-init="init()">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Buat Permintaan Barang</h2>
                <p class="text-sm text-slate-600 mt-1">Isi form untuk membuat permintaan barang baru</p>
            </div>
            <button @click="showCreateModal = false" 
                    class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class='bx bx-x text-xl text-slate-500'></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            <form @submit.prevent="submitForm()" class="p-6 space-y-6">
                
                {{-- Basic Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Judul Permintaan *</label>
                        <input type="text" 
                               x-model="form.judul" 
                               required
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Masukkan judul permintaan">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Outlet *</label>
                        <select x-model="form.outlet_id" 
                                required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Outlet</option>
                            <template x-for="outlet in outlets" :key="outlet.id">
                                <option :value="outlet.id" x-text="outlet.nama"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas *</label>
                        <select x-model="form.prioritas" 
                                required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="rendah">Rendah</option>
                            <option value="normal" selected>Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Dibutuhkan</label>
                        <input type="date" 
                               x-model="form.tanggal_dibutuhkan" 
                               :min="new Date().toISOString().split('T')[0]"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                    <textarea x-model="form.deskripsi" 
                              rows="3"
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Jelaskan detail permintaan barang"></textarea>
                </div>

                {{-- Items Section --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-slate-900">Daftar Barang</h3>
                        <button type="button" 
                                @click="addItem()" 
                                class="inline-flex items-center gap-2 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            <i class='bx bx-plus'></i>
                            <span>Tambah Item</span>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <template x-for="(item, index) in form.items" :key="index">
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <div class="flex items-start justify-between mb-4">
                                    <h4 class="font-medium text-slate-900" x-text="`Item ${index + 1}`"></h4>
                                    <button type="button" 
                                            @click="removeItem(index)" 
                                            class="p-1 text-red-600 hover:bg-red-100 rounded transition-colors">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Item *</label>
                                        <select x-model="item.tipe_item" 
                                                @change="onItemTypeChange(index)"
                                                required
                                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            <option value="">Pilih Tipe</option>
                                            <option value="produk">Produk</option>
                                            <option value="bahan">Bahan</option>
                                            <option value="custom">Input Manual</option>
                                        </select>
                                    </div>
                                    
                                    <div x-show="item.tipe_item === 'produk'">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Produk</label>
                                        <div class="relative">
                                            <input type="text" 
                                                   x-model="item.search_produk"
                                                   @input="searchProducts(index, $event.target.value)"
                                                   placeholder="Cari produk..."
                                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            
                                            <div x-show="item.produk_results && item.produk_results.length > 0" 
                                                 class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                <template x-for="produk in item.produk_results" :key="produk.id">
                                                    <button type="button"
                                                            @click="selectProduk(index, produk)"
                                                            class="w-full px-3 py-2 text-left hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                                                        <div class="font-medium" x-text="produk.nama"></div>
                                                        <div class="text-sm text-slate-500" x-text="produk.sku"></div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div x-show="item.tipe_item === 'bahan'">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Bahan</label>
                                        <div class="relative">
                                            <input type="text" 
                                                   x-model="item.search_bahan"
                                                   @input="searchMaterials(index, $event.target.value)"
                                                   placeholder="Cari bahan..."
                                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            
                                            <div x-show="item.bahan_results && item.bahan_results.length > 0" 
                                                 class="absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                <template x-for="bahan in item.bahan_results" :key="bahan.id">
                                                    <button type="button"
                                                            @click="selectBahan(index, bahan)"
                                                            class="w-full px-3 py-2 text-left hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                                                        <div class="font-medium" x-text="bahan.nama"></div>
                                                        <div class="text-sm text-slate-500" x-text="bahan.kode"></div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div x-show="item.tipe_item === 'custom' || item.nama_item">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Item *</label>
                                        <input type="text" 
                                               x-model="item.nama_item" 
                                               :required="item.tipe_item"
                                               :readonly="item.tipe_item !== 'custom'"
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Masukkan nama item">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Spesifikasi</label>
                                        <input type="text" 
                                               x-model="item.spesifikasi" 
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Spesifikasi tambahan">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Qty *</label>
                                        <input type="number" 
                                               x-model="item.qty" 
                                               @input="calculateItemTotal(index)"
                                               step="0.01"
                                               min="0.01"
                                               required
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Satuan *</label>
                                        <input type="text" 
                                               x-model="item.satuan" 
                                               required
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="pcs, kg, liter, dll">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Estimasi Harga</label>
                                        <input type="number" 
                                               x-model="item.estimasi_harga" 
                                               @input="calculateItemTotal(index)"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Total Estimasi</label>
                                        <input type="text" 
                                               :value="formatCurrency(item.total_estimasi || 0)"
                                               readonly
                                               class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                                    <textarea x-model="item.catatan" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="Catatan tambahan untuk item ini"></textarea>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="form.items.length === 0" class="text-center py-8 text-slate-500">
                            <i class='bx bx-package text-4xl mb-2'></i>
                            <p>Belum ada item. Klik "Tambah Item" untuk menambahkan.</p>
                        </div>
                    </div>
                </div>

                {{-- Total Budget --}}
                <div x-show="form.items.length > 0" class="bg-primary-50 rounded-lg p-4 border border-primary-200">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-primary-900">Total Estimasi Budget:</span>
                        <span class="text-xl font-bold text-primary-900" x-text="formatCurrency(getTotalBudget())"></span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button type="button" 
                    @click="showCreateModal = false" 
                    class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Batal
            </button>
            
            <div class="flex items-center gap-3">
                <button type="button" 
                        @click="submitForm('draft')" 
                        :disabled="submitting"
                        class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors disabled:opacity-50">
                    <span x-show="!submitting">Simpan Draft</span>
                    <span x-show="submitting">Menyimpan...</span>
                </button>
                
                <button type="button" 
                        @click="submitForm('aktif')" 
                        :disabled="submitting"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50">
                    <span x-show="!submitting">Ajukan Permintaan</span>
                    <span x-show="submitting">Mengajukan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function createPermintaanApp() {
    return {
        form: {
            judul: '',
            deskripsi: '',
            prioritas: 'normal',
            tanggal_dibutuhkan: '',
            outlet_id: '',
            items: []
        },
        outlets: [],
        submitting: false,
        searchTimeout: null,

        async init() {
            await this.loadOutlets();
            this.addItem(); // Add first item by default
        },

        async loadOutlets() {
            try {
                const response = await fetch('{{ route('admin.supply-chain.permintaan-barang.outlets') }}');
                this.outlets = await response.json();
            } catch (error) {
                console.error('Error loading outlets:', error);
            }
        },

        addItem() {
            this.form.items.push({
                tipe_item: '',
                produk_id: null,
                bahan_id: null,
                nama_item: '',
                spesifikasi: '',
                qty: 1,
                satuan: '',
                estimasi_harga: 0,
                total_estimasi: 0,
                catatan: '',
                search_produk: '',
                search_bahan: '',
                produk_results: [],
                bahan_results: []
            });
        },

        removeItem(index) {
            this.form.items.splice(index, 1);
        },

        onItemTypeChange(index) {
            const item = this.form.items[index];
            // Reset related fields when type changes
            item.produk_id = null;
            item.bahan_id = null;
            item.nama_item = '';
            item.satuan = '';
            item.search_produk = '';
            item.search_bahan = '';
            item.produk_results = [];
            item.bahan_results = [];
        },

        async searchProducts(index, query) {
            if (!query || query.length < 2) {
                this.form.items[index].produk_results = [];
                return;
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({
                        q: query,
                        outlet_id: this.form.outlet_id
                    });
                    
                    const response = await fetch(`{{ route('admin.supply-chain.permintaan-barang.search.products') }}?${params}`);
                    const results = await response.json();
                    this.form.items[index].produk_results = results;
                } catch (error) {
                    console.error('Error searching products:', error);
                }
            }, 300);
        },

        async searchMaterials(index, query) {
            if (!query || query.length < 2) {
                this.form.items[index].bahan_results = [];
                return;
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({
                        q: query,
                        outlet_id: this.form.outlet_id
                    });
                    
                    const response = await fetch(`{{ route('admin.supply-chain.permintaan-barang.search.materials') }}?${params}`);
                    const results = await response.json();
                    this.form.items[index].bahan_results = results;
                } catch (error) {
                    console.error('Error searching materials:', error);
                }
            }, 300);
        },

        selectProduk(index, produk) {
            const item = this.form.items[index];
            item.produk_id = produk.id;
            item.nama_item = produk.nama;
            item.satuan = produk.satuan?.nama || '';
            item.search_produk = produk.nama;
            item.produk_results = [];
        },

        selectBahan(index, bahan) {
            const item = this.form.items[index];
            item.bahan_id = bahan.id;
            item.nama_item = bahan.nama;
            item.satuan = bahan.satuan?.nama || '';
            item.search_bahan = bahan.nama;
            item.bahan_results = [];
        },

        calculateItemTotal(index) {
            const item = this.form.items[index];
            item.total_estimasi = (parseFloat(item.qty) || 0) * (parseFloat(item.estimasi_harga) || 0);
        },

        getTotalBudget() {
            return this.form.items.reduce((total, item) => {
                return total + (parseFloat(item.total_estimasi) || 0);
            }, 0);
        },

        async submitForm(status = 'draft') {
            if (this.submitting) return;
            
            // Validation
            if (!this.form.judul || !this.form.outlet_id || this.form.items.length === 0) {
                alert('Mohon lengkapi semua field yang wajib diisi');
                return;
            }

            // Validate items
            for (let i = 0; i < this.form.items.length; i++) {
                const item = this.form.items[i];
                if (!item.tipe_item || !item.nama_item || !item.qty || !item.satuan) {
                    alert(`Item ${i + 1}: Mohon lengkapi semua field yang wajib diisi`);
                    return;
                }
            }

            this.submitting = true;
            
            try {
                const formData = new FormData();
                
                // Add basic fields
                formData.append('judul', this.form.judul);
                formData.append('deskripsi', this.form.deskripsi || '');
                formData.append('prioritas', this.form.prioritas);
                formData.append('tanggal_dibutuhkan', this.form.tanggal_dibutuhkan || '');
                formData.append('outlet_id', this.form.outlet_id);
                formData.append('status', status);
                
                // Add items
                this.form.items.forEach((item, index) => {
                    formData.append(`items[${index}][tipe_item]`, item.tipe_item);
                    formData.append(`items[${index}][produk_id]`, item.produk_id || '');
                    formData.append(`items[${index}][bahan_id]`, item.bahan_id || '');
                    formData.append(`items[${index}][nama_item]`, item.nama_item);
                    formData.append(`items[${index}][spesifikasi]`, item.spesifikasi || '');
                    formData.append(`items[${index}][qty]`, item.qty);
                    formData.append(`items[${index}][satuan]`, item.satuan);
                    formData.append(`items[${index}][estimasi_harga]`, item.estimasi_harga || 0);
                    formData.append(`items[${index}][catatan]`, item.catatan || '');
                });

                const response = await fetch('{{ route('admin.supply-chain.permintaan-barang.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!response.ok) {
                    // Try to get error message from response
                    let errorMessage = `HTTP error! status: ${response.status}`;
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        } else if (errorData.errors) {
                            // Handle validation errors
                            const validationErrors = Object.values(errorData.errors).flat();
                            errorMessage = validationErrors.join(', ');
                        }
                    } catch (e) {
                        // If response is not JSON, use default error message
                    }
                    throw new Error(errorMessage);
                }

                const result = await response.json();

                if (result.success) {
                    this.showCreateModal = false;
                    this.loadData();
                    this.loadStats();
                    this.showNotification(result.message, 'success');
                    
                    // Reset form
                    this.form = {
                        judul: '',
                        deskripsi: '',
                        prioritas: 'normal',
                        tanggal_dibutuhkan: '',
                        outlet_id: '',
                        items: []
                    };
                    this.addItem();
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                
                // Try to get more details about the error
                if (error.message.includes('Unexpected token')) {
                    console.error('Server returned HTML instead of JSON. Check server logs for errors.');
                    alert('Terjadi kesalahan server. Silakan periksa log untuk detail lebih lanjut.');
                } else {
                    alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
                }
            } finally {
                this.submitting = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>