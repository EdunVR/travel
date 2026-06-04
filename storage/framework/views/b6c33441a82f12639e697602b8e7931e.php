
<div x-show="showDetailModal" 
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
     @click.self="showDetailModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-4 flex flex-col"
         x-data="detailPermintaanApp()"
         x-init="init()"
         @modal-opened="handleModalOpened($event.detail)">
        
        
        <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Detail Permintaan Barang</h2>
                <p class="text-sm text-slate-600 mt-1" x-text="detail.nomor_permintaan || 'Loading...'"></p>
            </div>
            <button @click="closeModal()" 
                    class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class='bx bx-x text-xl text-slate-500'></i>
            </button>
        </div>

        
        <div class="flex-1 overflow-y-auto">
            <div x-show="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary-500 border-t-transparent"></div>
            </div>
            
            <div x-show="!loading && detail.id" class="p-6 space-y-6">
                
                
                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">Informasi Umum</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Permintaan</label>
                            <p class="text-sm text-slate-900" x-text="detail.nomor_permintaan"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <span :class="getStatusBadge(detail.status)" 
                                  class="inline-flex px-2 py-1 rounded-full text-xs font-medium" 
                                  x-text="detail.status"></span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                            <p class="text-sm text-slate-900" x-text="detail.judul"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Prioritas</label>
                            <span :class="getPrioritasBadge(detail.prioritas)" 
                                  class="inline-flex px-2 py-1 rounded-full text-xs font-medium" 
                                  x-text="detail.prioritas"></span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Outlet</label>
                            <p class="text-sm text-slate-900" x-text="detail.outlet?.nama || '-'"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pemohon</label>
                            <p class="text-sm text-slate-900" x-text="detail.user?.name || '-'"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-slate-900" x-text="formatDate(detail.created_at)"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Dibutuhkan</label>
                            <p class="text-sm text-slate-900" x-text="detail.tanggal_dibutuhkan ? formatDate(detail.tanggal_dibutuhkan) : '-'"></p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                            <p class="text-sm text-slate-900" x-text="detail.deskripsi || '-'"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Total Estimasi Budget</label>
                            <p class="text-lg font-bold text-primary-600" x-text="formatCurrency(detail.estimasi_budget || 0)"></p>
                        </div>
                    </div>
                </div>

                
                <div x-show="detail.status === 'disetujui' || detail.status === 'ditolak'" class="bg-slate-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">Informasi Persetujuan</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Disetujui/Ditolak Oleh</label>
                            <p class="text-sm text-slate-900" x-text="detail.approver?.name || '-'"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Persetujuan</label>
                            <p class="text-sm text-slate-900" x-text="detail.approved_at ? formatDate(detail.approved_at) : '-'"></p>
                        </div>
                        
                        <div x-show="detail.catatan_approval" class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Persetujuan</label>
                            <p class="text-sm text-slate-900" x-text="detail.catatan_approval"></p>
                        </div>
                        
                        <div x-show="detail.alasan_penolakan" class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penolakan</label>
                            <p class="text-sm text-red-600" x-text="detail.alasan_penolakan"></p>
                        </div>
                    </div>
                </div>

                
                <div>
                    <h3 class="text-lg font-medium text-slate-900 mb-4">Daftar Barang</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border border-slate-200 rounded-lg">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Spesifikasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <template x-for="(item, index) in detail.items" :key="item.id">
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-slate-900" x-text="index + 1"></td>
                                        <td class="px-4 py-3">
                                            <span :class="getTipeItemBadge(item.tipe_item)" 
                                                  class="px-2 py-1 rounded-full text-xs font-medium" 
                                                  x-text="item.tipe_item"></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-slate-900" x-text="item.nama_item"></div>
                                            <div x-show="item.produk" class="text-xs text-slate-500">
                                                SKU: <span x-text="item.produk?.sku"></span>
                                            </div>
                                            <div x-show="item.bahan" class="text-xs text-slate-500">
                                                Kode: <span x-text="item.bahan?.kode"></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-900" x-text="item.spesifikasi || '-'"></td>
                                        <td class="px-4 py-3 text-sm text-slate-900" x-text="formatNumber(item.qty)"></td>
                                        <td class="px-4 py-3 text-sm text-slate-900" x-text="item.satuan"></td>
                                        <td class="px-4 py-3 text-sm text-slate-900" x-text="formatCurrency(item.estimasi_harga || 0)"></td>
                                        <td class="px-4 py-3 text-sm font-medium text-slate-900" x-text="formatCurrency(item.total_estimasi || 0)"></td>
                                    </tr>
                                    <tr x-show="item.catatan">
                                        <td colspan="8" class="px-4 py-2 bg-slate-50">
                                            <div class="text-xs text-slate-600">
                                                <strong>Catatan:</strong> <span x-text="item.catatan"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-sm font-medium text-slate-900 text-right">Total Estimasi:</td>
                                    <td class="px-4 py-3 text-sm font-bold text-primary-600" x-text="formatCurrency(detail.estimasi_budget || 0)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button type="button" 
                    @click="closeModal()" 
                    class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Tutup
            </button>
            
            <div class="flex items-center gap-3">
                <button type="button" 
                        @click="generatePdf()" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    <i class='bx bx-download'></i>
                    <span>Download PDF</span>
                </button>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('supply-chain.permintaan-barang.update')): ?>
                <button x-show="canEdit()" 
                        @click="editItem()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class='bx bx-edit'></i>
                    <span>Edit</span>
                </button>
                <?php endif; ?>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('supply-chain.permintaan-barang.approve')): ?>
                <button x-show="canApprove()" 
                        @click="showApprovalModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class='bx bx-check'></i>
                    <span>Setujui</span>
                </button>
                
                <button x-show="canApprove()" 
                        @click="showRejectModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class='bx bx-x'></i>
                    <span>Tolak</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function detailPermintaanApp() {
    return {
        detail: {},
        loading: false,

        init() {
            // Watch for modal opening using Alpine store or global events
            this.$watch('$store.permintaanBarang.showDetailModal', (isOpen) => {
                if (isOpen && this.$store.permintaanBarang.selectedItem) {
                    this.handleModalOpened(this.$store.permintaanBarang.selectedItem);
                }
            });
        },

        async handleModalOpened(selectedItem) {
            console.log('Detail modal opened with item:', selectedItem);
            if (!selectedItem?.id) {
                console.error('No item ID provided to detail modal');
                return;
            }
            
            this.loading = true;
            try {
                const response = await fetch(`<?php echo e(route('admin.supply-chain.permintaan-barang.show', ':id')); ?>`.replace(':id', selectedItem.id));
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Detail data loaded:', data);
                this.detail = data;
            } catch (error) {
                console.error('Error loading detail:', error);
                alert('Gagal memuat detail permintaan barang: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('close-detail-modal');
        },

        canEdit() {
            return ['draft', 'aktif'].includes(this.detail.status);
        },

        canApprove() {
            return this.detail.status === 'aktif';
        },

        editItem() {
            // Use $dispatch to communicate with parent
            this.$dispatch('open-edit-modal', this.detail);
        },

        showApprovalModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('open-approval-modal', this.detail);
        },

        showRejectModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('open-reject-modal', this.detail);
        },

        generatePdf() {
            window.open(`<?php echo e(route('admin.supply-chain.permintaan-barang.pdf', ':id')); ?>`.replace(':id', this.detail.id), '_blank');
        },

        getStatusBadge(status) {
            const badges = {
                'draft': 'bg-gray-100 text-gray-800',
                'aktif': 'bg-blue-100 text-blue-800',
                'disetujui': 'bg-green-100 text-green-800',
                'ditolak': 'bg-red-100 text-red-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        },

        getPrioritasBadge(prioritas) {
            const badges = {
                'rendah': 'bg-gray-100 text-gray-800',
                'normal': 'bg-blue-100 text-blue-800',
                'tinggi': 'bg-yellow-100 text-yellow-800',
                'urgent': 'bg-red-100 text-red-800'
            };
            return badges[prioritas] || 'bg-gray-100 text-gray-800';
        },

        getTipeItemBadge(tipe) {
            const badges = {
                'produk': 'bg-blue-100 text-blue-800',
                'bahan': 'bg-green-100 text-green-800',
                'custom': 'bg-purple-100 text-purple-800'
            };
            return badges[tipe] || 'bg-gray-100 text-gray-800';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\supply-chain\permintaan-barang\modals\detail.blade.php ENDPATH**/ ?>