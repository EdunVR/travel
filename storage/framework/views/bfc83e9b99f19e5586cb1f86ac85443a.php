

<?php $__env->startSection('title', 'Kelola Voucher Affiliator'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" x-data="voucherManager()">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-ticket-alt text-warning mr-2"></i>
                Kelola Voucher Affiliator
            </h1>
            <p class="text-muted mb-0">Buat dan kelola voucher diskon untuk affiliator</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal()">
            <i class="fas fa-plus mr-2"></i>Buat Voucher Baru
        </button>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Affiliator</label>
                    <select class="form-control" x-model="filters.affiliator_id" @change="loadVouchers()">
                        <option value="">Semua Affiliator</option>
                        <?php $__currentLoopData = $affiliators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($aff->id); ?>"><?php echo e($aff->full_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select class="form-control" x-model="filters.status" @change="loadVouchers()">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="expired">Kadaluarsa</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Pencarian</label>
                    <input type="text" class="form-control" placeholder="Cari kode voucher..." 
                           x-model="filters.search" @input.debounce.500ms="loadVouchers()">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button class="btn btn-secondary btn-block" @click="resetFilters()">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Affiliator</th>
                            <th>Tipe Diskon</th>
                            <th>Nilai</th>
                            <th>Penggunaan</th>
                            <th>Berlaku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="!loading && vouchers.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada voucher</p>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-for="voucher in vouchers" :key="voucher.id">
                            <tr>
                                <td>
                                    <strong x-text="voucher.code"></strong>
                                    <br>
                                    <small class="text-muted" x-text="voucher.description"></small>
                                </td>
                                <td x-text="voucher.affiliator?.full_name || '-'"></td>
                                <td>
                                    <span x-text="voucher.discount_type === 'percentage' ? 'Persentase' : 'Nominal Tetap'"></span>
                                </td>
                                <td>
                                    <template x-if="voucher.discount_type === 'percentage'">
                                        <span x-text="voucher.discount_value + '%'"></span>
                                    </template>
                                    <template x-if="voucher.discount_type === 'fixed'">
                                        <span x-text="'Rp ' + formatNumber(voucher.discount_value)"></span>
                                    </template>
                                    <template x-if="voucher.max_discount">
                                        <br><small class="text-muted" x-text="'Max: Rp ' + formatNumber(voucher.max_discount)"></small>
                                    </template>
                                </td>
                                <td>
                                    <span x-text="voucher.usage_count"></span> / 
                                    <span x-text="voucher.usage_limit || '∞'"></span>
                                </td>
                                <td>
                                    <small x-text="formatDate(voucher.valid_from)"></small> - 
                                    <small x-text="formatDate(voucher.valid_until)"></small>
                                </td>
                                <td>
                                    <span class="badge" 
                                          :class="{
                                              'badge-success': voucher.is_active && isValidDate(voucher),
                                              'badge-danger': !voucher.is_active,
                                              'badge-warning': voucher.is_active && !isValidDate(voucher)
                                          }"
                                          x-text="getStatusText(voucher)">
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" @click="openEditModal(voucher)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="deleteVoucher(voucher.id)" title="Hapus"
                                            :disabled="voucher.usage_count > 0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="voucherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span x-show="!editMode">Buat Voucher Baru</span>
                        <span x-show="editMode">Edit Voucher</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveVoucher()">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Affiliator <span class="text-danger">*</span></label>
                                    <select class="form-control" x-model="form.id_affiliator" required>
                                        <option value="">Pilih Affiliator</option>
                                        <?php $__currentLoopData = $affiliators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($aff->id); ?>"><?php echo e($aff->full_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Voucher <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" x-model="form.code" 
                                               required maxlength="50" style="text-transform: uppercase;"
                                               :readonly="editMode">
                                        <div class="input-group-append" x-show="!editMode">
                                            <button class="btn btn-secondary" type="button" @click="generateCode()">
                                                <i class="fas fa-random"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipe Diskon <span class="text-danger">*</span></label>
                                    <select class="form-control" x-model="form.discount_type" required>
                                        <option value="percentage">Persentase (%)</option>
                                        <option value="fixed">Nominal Tetap (Rp)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nilai Diskon <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" x-model="form.discount_value" 
                                           required min="0" step="0.01">
                                    <small class="text-muted" x-show="form.discount_type === 'percentage'">
                                        Masukkan nilai persentase (contoh: 10 untuk 10%)
                                    </small>
                                    <small class="text-muted" x-show="form.discount_type === 'fixed'">
                                        Masukkan nominal dalam Rupiah
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Maksimal Diskon (Opsional)</label>
                                    <input type="number" class="form-control" x-model="form.max_discount" 
                                           min="0" step="1000">
                                    <small class="text-muted">Untuk tipe persentase, batasi maksimal diskon</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Minimal Transaksi</label>
                                    <input type="number" class="form-control" x-model="form.min_transaction" 
                                           min="0" step="1000">
                                    <small class="text-muted">Minimal nilai transaksi untuk menggunakan voucher</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batas Penggunaan</label>
                                    <input type="number" class="form-control" x-model="form.usage_limit" 
                                           min="1">
                                    <small class="text-muted">Kosongkan untuk unlimited</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Berlaku Dari</label>
                                    <input type="date" class="form-control" x-model="form.valid_from">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Berlaku Sampai</label>
                                    <input type="date" class="form-control" x-model="form.valid_until">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea class="form-control" x-model="form.description" rows="2"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="isActive" 
                                       x-model="form.is_active">
                                <label class="custom-control-label" for="isActive">Aktif</label>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span x-show="!saving">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </span>
                                <span x-show="saving">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function voucherManager() {
    return {
        vouchers: [],
        affiliators: <?php echo json_encode($affiliators, 15, 512) ?>,
        loading: false,
        saving: false,
        editMode: false,
        filters: {
            affiliator_id: '',
            status: '',
            search: ''
        },
        form: {
            id: null,
            id_affiliator: '',
            code: '',
            discount_type: 'percentage',
            discount_value: '',
            max_discount: '',
            min_transaction: 0,
            usage_limit: '',
            valid_from: '',
            valid_until: '',
            description: '',
            is_active: true
        },

        init() {
            this.loadVouchers();
        },

        async loadVouchers() {
            this.loading = true;
            try {
                const params = new URLSearchParams(this.filters);
                const response = await fetch(`/admin/affiliate/vouchers/list?${params}`);
                const data = await response.json();
                this.vouchers = data.data || [];
            } catch (error) {
                console.error('Error loading vouchers:', error);
                alert('Gagal memuat data voucher');
            } finally {
                this.loading = false;
            }
        },

        openCreateModal() {
            this.editMode = false;
            this.resetForm();
            $('#voucherModal').modal('show');
        },

        openEditModal(voucher) {
            this.editMode = true;
            this.form = { ...voucher };
            $('#voucherModal').modal('show');
        },

        async generateCode() {
            try {
                const response = await fetch('/admin/affiliate/vouchers/generate-code');
                const data = await response.json();
                if (data.success) {
                    this.form.code = data.code;
                }
            } catch (error) {
                console.error('Error generating code:', error);
            }
        },

        async saveVoucher() {
            this.saving = true;
            try {
                const url = this.editMode 
                    ? `/admin/affiliate/vouchers/${this.form.id}`
                    : '/admin/affiliate/vouchers';
                
                const method = this.editMode ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    $('#voucherModal').modal('hide');
                    this.loadVouchers();
                } else {
                    alert(data.message || 'Gagal menyimpan voucher');
                }
            } catch (error) {
                console.error('Error saving voucher:', error);
                alert('Terjadi kesalahan saat menyimpan voucher');
            } finally {
                this.saving = false;
            }
        },

        async deleteVoucher(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus voucher ini?')) return;

            try {
                const response = await fetch(`/admin/affiliate/vouchers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.loadVouchers();
                } else {
                    alert(data.message || 'Gagal menghapus voucher');
                }
            } catch (error) {
                console.error('Error deleting voucher:', error);
                alert('Terjadi kesalahan saat menghapus voucher');
            }
        },

        resetForm() {
            this.form = {
                id: null,
                id_affiliator: '',
                code: '',
                discount_type: 'percentage',
                discount_value: '',
                max_discount: '',
                min_transaction: 0,
                usage_limit: '',
                valid_from: '',
                valid_until: '',
                description: '',
                is_active: true
            };
        },

        resetFilters() {
            this.filters = {
                affiliator_id: '',
                status: '',
                search: ''
            };
            this.loadVouchers();
        },

        formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID');
        },

        isValidDate(voucher) {
            const now = new Date();
            const validFrom = voucher.valid_from ? new Date(voucher.valid_from) : null;
            const validUntil = voucher.valid_until ? new Date(voucher.valid_until) : null;
            
            if (validFrom && now < validFrom) return false;
            if (validUntil && now > validUntil) return false;
            
            return true;
        },

        getStatusText(voucher) {
            if (!voucher.is_active) return 'Tidak Aktif';
            if (!this.isValidDate(voucher)) return 'Kadaluarsa';
            if (voucher.usage_limit && voucher.usage_count >= voucher.usage_limit) return 'Habis';
            return 'Aktif';
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\affiliate\vouchers.blade.php ENDPATH**/ ?>