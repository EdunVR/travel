{{-- Reject Modal --}}
<div x-show="showRejectModal" 
     x-transition.opacity.duration.300ms
     class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm p-4 pt-20"
     @click.self="showRejectModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col"
         x-data="rejectPermintaanApp()"
         @modal-opened="handleModalOpened($event.detail)">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-200 flex-shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Tolak Permintaan Barang</h2>
                <p class="text-sm text-slate-600 mt-1" x-text="selectedItem?.nomor_permintaan || ''"></p>
            </div>
            <button @click="closeModal()" 
                    class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class='bx bx-x text-xl text-slate-500'></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            <form @submit.prevent="submitRejection()" class="p-6 space-y-6">
                
                {{-- Item Summary --}}
                <div class="bg-slate-50 rounded-lg p-4">
                    <h3 class="font-medium text-slate-900 mb-3">Ringkasan Permintaan</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Judul:</span>
                            <span class="font-medium" x-text="selectedItem?.judul"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Outlet:</span>
                            <span class="font-medium" x-text="selectedItem?.outlet?.nama"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Pemohon:</span>
                            <span class="font-medium" x-text="selectedItem?.user?.name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Total Budget:</span>
                            <span class="font-medium text-primary-600" x-text="formatCurrency(selectedItem?.estimasi_budget || 0)"></span>
                        </div>
                    </div>
                </div>

                {{-- Warning Notice --}}
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class='bx bx-error-circle text-red-600 text-lg mt-0.5'></i>
                        <div class="text-sm text-red-800">
                            <p class="font-medium mb-1">Peringatan</p>
                            <p>Setelah ditolak, permintaan ini tidak dapat diproses lebih lanjut. Pastikan keputusan Anda sudah tepat.</p>
                        </div>
                    </div>
                </div>

                {{-- Rejection Reason --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan *</label>
                    <textarea x-model="form.alasan_penolakan" 
                              rows="4"
                              required
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Jelaskan alasan mengapa permintaan ini ditolak..."></textarea>
                    <p class="text-xs text-slate-500 mt-1">Alasan ini akan dikirimkan kepada pemohon sebagai feedback</p>
                </div>

                {{-- Common Rejection Reasons --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Umum (Opsional)</label>
                    <div class="space-y-2">
                        <template x-for="reason in commonReasons" :key="reason">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" 
                                       @change="toggleReason(reason)"
                                       class="text-red-600 focus:ring-red-500 rounded">
                                <span class="text-sm text-slate-700" x-text="reason"></span>
                            </label>
                        </template>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Pilih alasan yang sesuai untuk mempercepat pengisian</p>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50 flex-shrink-0">
            <button type="button" 
                    @click="closeModal()" 
                    class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Batal
            </button>
            
            <button type="button" 
                    @click="submitRejection()" 
                    :disabled="submitting || !form.alasan_penolakan.trim()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!submitting">Tolak Permintaan</span>
                <span x-show="submitting">Memproses...</span>
            </button>
        </div>
    </div>
</div>

<script>
function rejectPermintaanApp() {
    return {
        selectedItem: null,
        form: {
            alasan_penolakan: ''
        },
        submitting: false,
        commonReasons: [
            'Budget tidak tersedia',
            'Item tidak sesuai dengan kebutuhan operasional',
            'Spesifikasi tidak jelas atau tidak lengkap',
            'Prioritas tidak mendesak',
            'Sudah ada stok yang cukup',
            'Perlu persetujuan manajemen lebih tinggi',
            'Dokumen pendukung tidak lengkap',
            'Tidak sesuai dengan kebijakan perusahaan'
        ],

        handleModalOpened(selectedItem) {
            this.selectedItem = selectedItem;
        },

        closeModal() {
            // Use $dispatch to communicate with parent
            this.$dispatch('close-reject-modal');
        },

        toggleReason(reason) {
            const currentReasons = this.form.alasan_penolakan.split('\n').filter(r => r.trim());
            const reasonExists = currentReasons.some(r => r.includes(reason));
            
            if (reasonExists) {
                // Remove reason
                this.form.alasan_penolakan = currentReasons
                    .filter(r => !r.includes(reason))
                    .join('\n');
            } else {
                // Add reason
                if (this.form.alasan_penolakan.trim()) {
                    this.form.alasan_penolakan += '\n• ' + reason;
                } else {
                    this.form.alasan_penolakan = '• ' + reason;
                }
            }
        },

        async submitRejection() {
            if (this.submitting) return;
            
            // Validation
            if (!this.form.alasan_penolakan.trim()) {
                alert('Mohon isi alasan penolakan');
                return;
            }

            // Confirmation
            if (!confirm('Apakah Anda yakin ingin menolak permintaan ini?')) {
                return;
            }

            this.submitting = true;
            
            try {
                const response = await fetch(`{{ route('admin.supply-chain.permintaan-barang.reject', ':id') }}`.replace(':id', this.selectedItem.id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.success) {
                    this.$dispatch('close-reject-modal');
                    this.$dispatch('refresh-data');
                    this.$dispatch('show-notification', { message: result.message, type: 'success' });
                    
                    // Reset form
                    this.form = {
                        alasan_penolakan: ''
                    };
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error submitting rejection:', error);
                alert('Terjadi kesalahan saat memproses penolakan');
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