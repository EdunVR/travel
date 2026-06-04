<style>
    .badge-primary { background-color: #4e73df; }
    .badge-success { background-color: #1cc88a; }
    .badge-secondary { background-color: #858796; }
    .badge-warning { background-color: #f6c23e; }
    .badge-danger { background-color: #e74a3b; }
    
    .info-box {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 0.35rem;
        background-color: #fff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        color: white;
        border-radius: 0.35rem;
        margin-right: 1rem;
    }
    .info-box-content {
        flex: 1;
    }
    .info-box-text {
        display: block;
        font-size: 0.875rem;
        color: #858796;
    }
    .info-box-number {
        font-size: 1.25rem;
        font-weight: 700;
    }
    .form-inline .form-group {
    margin-right: 0.5rem;
}

.flex-form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.flex-form-row .form-group {
    flex: 1;
    min-width: 200px;
}

.koreksi-card .btn-submit-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-top: 1rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.4em 0.6em;
    border-radius: 0.35rem;
}

.custom-switch label {
    margin-left: 0.5rem;
}

.card-body {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #e0e0e0;
}

.koreksi-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #e0e0e0;
}

</style>



<?php $__env->startSection('title', 'Detail Pembagian Keuntungan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Detail Pembagian Keuntungan Periode <?php echo e($profit->period); ?>

                <span class="badge badge-<?php echo e($profit->status == 'draft' ? 'secondary' : ($profit->status == 'processed' ? 'warning' : 'success')); ?>">
                    <?php echo e(ucfirst($profit->status)); ?>

                </span>
            </h6>
            <a href="<?php echo e(route('irp.profit-management.index')); ?>" class="btn btn-sm btn-danger">
                <i data-feather="arrow-left" width="16" height="16"></i>  Kembali
            </a>
        </div>
        <?php if($profit->status == 'draft'): ?>
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <div class="row">
                        
                        <div class="col-md-3 mb-4">
                            <div class="koreksi-card border p-3 rounded shadow-sm bg-white h-100">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i data-feather="filter"></i> Filter Investor
                                </h6>
                                <div class="form-inline">
                                    <div class="form-group mr-2 mb-2">
                                        <select id="categoryFilter" class="form-control form-control-sm" 
                                            <?php echo e($profit->status != 'draft' ? 'disabled' : ''); ?>>
                                            <option value="">Semua Kategori</option>
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($category); ?>" 
                                                    <?php echo e(($selectedCategory ?? $profit->category) == $category ? 'selected' : ''); ?>>
                                                    <?php echo e(ucfirst($category)); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <?php if($profit->status == 'draft'): ?>
                                        <button id="applyFilter" class="btn btn-sm btn-primary mb-2">
                                            <i data-feather="search"></i> Filter
                                        </button>
                                        <?php if($selectedCategory || $profit->category): ?>
                                            <a href="<?php echo e(route('irp.profit-management.show', $profit->id)); ?>" 
                                            class="btn btn-sm btn-secondary mb-2 ml-1">
                                                <i data-feather="x"></i> Reset
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if($profit->status != 'draft'): ?>
                                    <small class="text-muted">Filter tidak dapat diubah karena status sudah <?php echo e($profit->status); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-9 mb-4">
                            <div class="koreksi-card border p-3 rounded shadow-sm bg-white h-100">
                                <h6 class="font-weight-bold text-dark mb-3">
                                    <i data-feather="edit"></i> Koreksi Pembagian
                                </h6>
                                <form action="<?php echo e(route('irp.profit-management.update-distribution', $profit->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="form-row align-items-end">
                                        <div class="form-group col-md-4">
                                            <label>Total Keuntungan Baru (Rp)*</label>
                                            <input type="number" name="new_total_profit" class="form-control" 
                                                value="<?php echo e(old('new_total_profit', $profit->total_profit)); ?>" step="0.01" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Gunakan Persentase Custom?</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" class="custom-control-input" id="useCustomPercentage" 
                                                    name="use_custom_percentage" value="1" <?php echo e($profit->use_custom_percentage ? 'checked' : ''); ?>>
                                                <label class="custom-control-label" for="useCustomPercentage">Aktifkan</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4" id="customPercentageField" style="<?php echo e(!$profit->use_custom_percentage ? 'display:none;' : ''); ?>">
                                            <label>Persentase Custom (%)</label>
                                            <input type="number" name="custom_percentage" class="form-control" 
                                                value="<?php echo e(old('custom_percentage', $profit->custom_percentage)); ?>" step="0.01">
                                        </div>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i data-feather="save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Keuntungan</span>
                            <span class="info-box-number"><?php echo e(format_uang($profit->total_profit)); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tanggal Pembagian</span>
                            <span class="info-box-number"><?php echo e($profit->distribution_date->format('d F Y')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Investasi</span>
                            <span class="info-box-number"><?php echo e(format_uang($totalInvestment)); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview Pembagian ke Investor</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Investor</th>
                                    <th>Kategori</th>
                                    <th>Rekening</th>
                                    <th class="text-right">Total Investasi</th>
                                    <th class="text-right">Persentase</th>
                                    <th class="text-right">Bagi Hasil</th>
                                    <th class="text-right">Estimasi ROI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalProfitShare = 0;
                                    $totalInvestmentDisplay = $totalInvestment > 0 ? $totalInvestment : 1; // Hindari division by zero
                                    $useCustomPercentage = $profit->use_custom_percentage;
                                    $customPercentage = $profit->custom_percentage;
                                    $totalDistributed = 0;
                                ?>
                                
                                <?php $__currentLoopData = $investors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $investor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $investor->accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $accountInvestment = $account->investments->sum('amount');
                                            $effectivePercentage = $useCustomPercentage 
                                                ? $customPercentage 
                                                : $account->profit_percentage;
                                            $profitShare = $totalInvestment > 0 
                                                ? ($profit->total_profit * ($accountInvestment / $totalInvestment)) * ($effectivePercentage / 100)
                                                : 0;
                                            $totalProfitShare += $profitShare;
                                            $roiPercentage = $accountInvestment > 0 
                                                ? ($profitShare / $accountInvestment) * 100 
                                                : 0;
                                        ?>
                                        
                                        <tr>
                                            <td><?php echo e($investor->name); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($investor->category == 'internal' ? 'primary' : 'success'); ?>">
                                                    <?php echo e(ucfirst($investor->category)); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php echo e($account->bank_name); ?><br>
                                                <small><?php echo e($account->account_number); ?></small>
                                            </td>
                                            <td class="text-right"><?php echo e(format_uang($accountInvestment)); ?></td>
                                            <td class="text-right"><?php echo e($account->profit_percentage); ?>%</td>
                                            <td class="text-right">
                                                <?php echo e(format_uang($profitShare)); ?>

                                                <small class="d-block text-muted">
                                                    (<?php echo e(number_format(($accountInvestment / $totalInvestment) * 100, 2)); ?>% alokasi)
                                                </small>
                                            </td>
                                            <td class="text-right <?php echo e($roiPercentage >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                <?php echo e(number_format($roiPercentage, 2)); ?>%
                                                <small class="d-block text-muted">
                                                    (<?php echo e(format_uang($profitShare)); ?> / <?php echo e(format_uang($accountInvestment)); ?>)
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right">Total:</td>
                                    <td class="text-right"><?php echo e(format_uang($totalInvestment)); ?></td>
                                    <td></td>
                                    <td class="text-right"><?php echo e(format_uang($totalProfitShare)); ?></td>
                                    <td class="text-right">
                                        <?php echo e(number_format(($totalProfitShare / $totalInvestmentDisplay) * 100, 2)); ?>%
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-right">Selisih (Keuntungan Disimpan):</td>
                                    <td class="text-right <?php echo e($profit->total_profit - $totalProfitShare != 0 ? 'text-danger' : 'text-success'); ?>">
                                        <?php echo e(format_uang($profit->total_profit - $totalProfitShare)); ?>

                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <?php if($profit->status == 'draft'): ?>
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Konfirmasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('irp.profit-management.confirm-payment', $profit->id)); ?>" 
                        method="POST" 
                        enctype="multipart/form-data"
                        onsubmit="return confirm('Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?')">
                        <?php echo csrf_field(); ?>
                        <!-- Simpan kategori yang dipilih -->
                        <input type="hidden" name="category" value="<?php echo e($selectedCategory ?? $profit->category); ?>">
                        
                        <div class="form-group">
                            <label>Upload Bukti Transfer (Optional)</label>
                            <input type="file" name="proof_file" class="form-control-file">
                            <small class="form-text text-muted">Format: PDF, JPG, PNG (Maks. 2MB)</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
            <?php elseif($profit->proof_file): ?>
            <div class="card mt-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Transfer</h6>
                </div>
                <div class="card-body text-center">
                    <a href="<?php echo e(asset('storage/'.$profit->proof_file)); ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-download"></i> Download Bukti Transfer
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    feather.replace()
    // Toggle custom percentage field
    $('#useCustomPercentage').change(function() {
        if($(this).is(':checked')) {
            $('#customPercentageField').show();
        } else {
            $('#customPercentageField').hide();
        }
    });

    $('#categoryFilter').change(function() {
        if ("<?php echo e($profit->status); ?>" === 'draft') {
            const category = $(this).val();
            
            $.ajax({
                url: "<?php echo e(route('irp.profit-management.update-category', $profit->id)); ?>",
                method: 'PUT',
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    category: category
                },
                success: function(response) {
                    if (!response.success) {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan saat menyimpan kategori');
                }
            });
        }
    });

    // Handle apply filter
    $('#applyFilter').click(function() {
        const category = $('#categoryFilter').val();
        const url = new URL(window.location.href);
        
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        
        window.location.href = url.toString();
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\profit_management\show.blade.php ENDPATH**/ ?>