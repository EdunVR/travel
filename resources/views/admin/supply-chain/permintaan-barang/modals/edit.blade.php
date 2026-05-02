{{-- Edit Modal --}}
<div x-show="showEditModal" 
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
     @click.self="showEditModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-4 flex flex-col"
         x-data="editPermintaanApp()"
         x-init="init()"
         @modal-opened="handleModalOpened($event.detail)">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Edit Permintaan Barang</h2>
                <p class="text-sm text-slate-600 mt-1" x-text="form.nomor_permintaan || 'Loading...'"></p>
            </div>
            <button @click="closeModal()" 
                    class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class='bx bx-x text-xl text-slate-500'></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            <div x-show="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary-500 border-t-transparent"></div>
            </div>
            
            <form @submit.prevent="submitForm()" x-show="!loading" class="p-6 space-y-6">
                
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
                            <option value="">Pilih Prioritas</option>
                            <option value="rendah">Rendah</option>
                            <option value="normal">Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Dibutuhkan</label>
                        <input type="date" 
                               x-model="form.tanggal_dibutuhkan" 
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                    <textarea x-model="form.deskripsi" 
                              rows="3"
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Masukkan deskripsi permintaan"></textarea>
                </div>

                {{-- Items Section --}}
                <div class="border-t border-slate-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-slate-900">Item Permintaan</h3>
                        <button type="button" 
                                @click="addItem()"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            <i class='bx bx-plus'></i>
                            <span>Tambah Item</span>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <template x-for="(item, index) in form.items" :key="index">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Item *</label>
                                        <select x-model="item.tipe_item" 
                                                required
                                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                            <option value="custom">Custom</option>
                                            <option value="produk">Produk</option>
                                            <option value="bahan">Bahan</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Item *</label>
                                        <input type="text" 
                                               x-model="item.nama_item" 
                                               required
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Nama item">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah *</label>
                                        <input type="number" 
                                               x-model="item.qty" 
                                               required
                                               min="0.01"
                                               step="0.01"
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Satuan *</label>
                                        <input type="text" 
                                               x-model="item.satuan" 
                                               required
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="pcs, kg, dll">
                                    </div>
                                    
                                    <div class="flex items-end">
                                        <button type="button" 
                                                @click="removeItem(index)"
                                                class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Spesifikasi</label>
                                        <input type="text" 
                                               x-model="item.spesifikasi" 
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Spesifikasi item">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Estimasi Harga</label>
                                        <input type="number" 
                                               x-model="item.estimasi_harga" 
                                               min="0"
                                               step="0.01"
                                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="0">
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan</label>
                                    <textarea x-model="item.catatan" 
                                              rows="2"
                                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="Keterangan tambahan"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 p-6 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button type="button" 
                    @click="closeModal()" 
                    class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    @click="submitForm()"
                    :disabled="submitting"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50">
                <span x-show="!submitting">Simpan Perubahan</span>
                <span x-show="submitting">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>

<script>
function editPermintaanApp() {
    return {
        loading: false,
        submitting: false,
        outlets: [],
        form: {
            id: null,
            nomor_permintaan: '',
            judul: '',
            outlet_id: '',
            prioritas: '',
            tanggal_dibutuhkan: '',
            deskripsi: '',
            items: []
        },

        init() {
            // Watch for modal opening using Alpine store or global events
            this.$watch('$store.permintaanBarang.showEditModal', (isOpen) => {
                if (isOpen && this.$store.permintaanBarang.selectedItem) {
                    this.handleModalOpened(this.$store.permintaanBarang.selectedItem);
                }
            });
        },

        async handleModalOpened(selectedItem) {
            console.log('Edit modal opened with item:', selectedItem);
            if (!selectedItem?.id) {
                console.error('No item ID provided to edit modal');
                return;
            }
            
            this.loading = true;
            try {
                // Load outlets first
                await this.loadOutlets();
                
                // Load item detail
                const response = await fetch(`{{ route('admin.supply-chain.permintaan-barang.show', ':id') }}`.replace(':id', selectedItem.id));
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const detail = await response.json();
                console.log('Edit data loaded:', detail);
                
                // Populate form
                this.form = {
                    id: detail.id,
                    nomor_permintaan: detail.nomor_permintaan,
                    judul: detail.judul || '',
                    outlet_id: detail.outlet_id || '',
                    prioritas: detail.prioritas || 'normal',
                    tanggal_dibutuhkan: detail.tanggal_dibutuhkan ? detail.tanggal_dibutuhkan.split('T')[0] : '',
                    deskripsi: detail.deskripsi || '',
                    items: (detail.items || []).map(item => ({
                        id: item.id,
                        tipe_item: item.tipe_item || 'custom', // Required field
                        produk_id: item.produk_id || null,
                        bahan_id: item.bahan_id || null,
                        nama_item: item.nama_item || '',
                        spesifikasi: item.spesifikasi || '',
                        qty: item.qty || 1,
                        satuan: item.satuan || '',
                        estimasi_harga: item.estimasi_harga || 0,
                        catatan: item.catatan || ''
                    }))
                };
                
                console.log('Form populated:', this.form);
            } catch (error) {
                console.error('Error loading detail:', error);
                alert('Gagal memuat detail permintaan barang: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async loadOutlets() {
            try {
                const response = await fetch('{{ route('admin.supply-chain.permintaan-barang.outlets') }}');
                this.outlets = await response.json();
            } catch (error) {
                console.error('Error loading outlets:', error);
            }
        },

        closeModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('close-edit-modal');
        },

        addItem() {
            this.form.items.push({
                tipe_item: 'custom', // Required field
                produk_id: null,
                bahan_id: null,
                nama_item: '',
                spesifikasi: '',
                qty: 1,
                satuan: '',
                estimasi_harga: 0,
                catatan: ''
            });
        },

        removeItem(index) {
            this.form.items.splice(index, 1);
        },

        async submitForm() {
            if (this.submitting) return;
            
            console.log('Form data before submit:', this.form);
            console.log('Form ID:', this.form.id);
            
            if (!this.form.id) {
                alert('Error: ID permintaan tidak ditemukan. Silakan tutup modal dan coba lagi.');
                return;
            }
            
            this.submitting = true;
            
            try {
                // Use FormData with method spoofing for Laravel
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Append form data
                Object.keys(this.form).forEach(key => {
                    if (key === 'items') {
                        formData.append('items', JSON.stringify(this.form.items));
                    } else {
                        formData.append(key, this.form[key] || '');
                    }
                });
                
                // Generate URL with proper ID
                const updateUrl = `{{ route('admin.supply-chain.permintaan-barang.index') }}/${this.form.id}`;
                console.log('Update URL:', updateUrl);
                
                const response = await fetch(updateUrl, {
                    method: 'POST', // Use POST with _method spoofing
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('Response status:', response.status);
                console.log('Response URL:', response.url);
                console.log('Response headers:', response.headers.get('content-type'));
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('Error response:', errorText);
                    
                    // Handle validation errors (422)
                    if (response.status === 422) {
                        try {
                            const errorData = JSON.parse(errorText);
                            if (errorData.errors) {
                                let errorMessage = 'Validation errors:\n';
                                Object.keys(errorData.errors).forEach(field => {
                                    errorMessage += `- ${field}: ${errorData.errors[field].join(', ')}\n`;
                                });
                                alert(errorMessage);
                                return;
                            }
                        } catch (e) {
                            // If JSON parsing fails, show generic error
                        }
                    }
                    
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const responseText = await response.text();
                    console.log('Non-JSON response:', responseText.substring(0, 500));
                    throw new Error('Server returned HTML instead of JSON. Check server logs for errors.');
                }

                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    this.$dispatch('close-edit-modal');
                    this.$dispatch('refresh-data');
                    this.$dispatch('show-notification', { message: 'Permintaan barang berhasil diperbarui', type: 'success' });
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>