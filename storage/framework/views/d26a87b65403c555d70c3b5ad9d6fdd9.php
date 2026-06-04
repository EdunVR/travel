

<?php $__env->startSection('content'); ?>
<style>
    .card-header {
        background-color: #2e7d32;
        color: white;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th {
        white-space: nowrap;
        vertical-align: middle;
        background-color: #f5f5f5;
    }
    .table td {
        vertical-align: middle;
    }
    .text-right {
        text-align: right;
    }
    .account-type-asset { background-color: #e3f2fd; }
    .account-type-liability { background-color: #e8f5e9; }
    .account-type-equity { background-color: #f1f8e9; }
    .account-type-revenue { background-color: #f3e5f5; }
    .account-type-expense { background-color: #ffebee; }
    .total-row {
        font-weight: bold;
        background-color: #f5f5f5 !important;
    }
    .net-income-row {
        font-weight: bold;
        background-color: #e8f5e9 !important;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="file-text"></i> Neraca Lajur
            </h5>
            <div>
                <?php if(isset($worksheetData)): ?>
                <button id="exportBtn" class="btn btn-success btn-sm">
                    <i data-feather="download"></i> Export
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filter Section -->
            <div class="filter-section">
                <form id="filterForm" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="book_id" class="form-label">Tahun Buku *</label>
                            <select name="book_id" id="book_id" class="form-select" required>
                                <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($book->id); ?>" <?php echo e($selectedBookId == $book->id ? 'selected' : ''); ?>>
                                        <?php echo e($book->name); ?> (<?php echo e($book->start_date->format('Y')); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                value="<?php echo e($dateFrom ? $dateFrom->format('Y-m-d') : ''); ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                value="<?php echo e($dateTo ? $dateTo->format('Y-m-d') : ''); ?>">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if(isset($worksheetData)): ?>
            <!-- Worksheet Information -->
            <div class="mb-3">
                <h5>
                    <strong>Tahun Buku:</strong> <?php echo e($accountingBook->name); ?> 
                    (<?php echo e($accountingBook->start_date->format('d/m/Y')); ?> - <?php echo e($accountingBook->end_date->format('d/m/Y')); ?>)
                </h5>
                <p>
                    <strong>Periode:</strong> 
                    <?php if($dateFrom && $dateTo): ?>
                        <?php echo e($dateFrom->format('d/m/Y')); ?> - <?php echo e($dateTo->format('d/m/Y')); ?>

                    <?php else: ?>
                        Semua Periode
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Worksheet Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th rowspan="2" width="5%">No</th>
                            <th rowspan="2" width="15%">Kode Akun</th>
                            <th rowspan="2" width="25%">Nama Akun</th>
                            <th colspan="2" class="text-center">Neraca Saldo</th>
                            <th colspan="2" class="text-center">Laba Rugi</th>
                            <th colspan="2" class="text-center">Neraca</th>
                        </tr>
                        <tr>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Kredit</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Kredit</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $worksheetData['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="account-type-<?php echo e($account['account_type']); ?>">
                            <td class="text-center"><?php echo e($index + 1); ?></td>
                            <td style="text-align: left;"><?php echo e($account['account_code']); ?></td>
                            <td style="text-align: left;"><?php echo e($account['account_name']); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['trial_balance']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['trial_balance']['credit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['income_statement']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['income_statement']['credit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['balance_sheet']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($account['balance_sheet']['credit'], 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <!-- Total Neraca Saldo -->
                        <tr class="total-row">
                            <td colspan="3" class="text-center"><strong>TOTAL NERACA SALDO</strong></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['trial_balance']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['trial_balance']['credit'], 2)); ?></td>
                            <td colspan="4"></td>
                        </tr>
                        
                        <!-- Total Laba Rugi -->
                        <tr class="total-row">
                            <td colspan="5" class="text-center"><strong>TOTAL LABA/RUGI</strong></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['income_statement']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['income_statement']['credit'], 2)); ?></td>
                            <td colspan="2"></td>
                        </tr>
                        
                        <!-- Laba/Rugi Bersih -->
                        <tr class="net-income-row">
                            <td colspan="6" class="text-center">
                                <strong>LABA/RUGI BERSIH</strong>
                            </td>
                            <td colspan="2" class="text-center">
                                <?php if($worksheetData['net_income'] >= 0): ?>
                                    <span class="text-success">Laba: <?php echo e(number_format($worksheetData['net_income'], 2)); ?></span>
                                <?php else: ?>
                                    <span class="text-danger">Rugi: <?php echo e(number_format(abs($worksheetData['net_income']), 2)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Total Neraca (Sebelum Laba/Rugi) -->
                        <tr class="total-row">
                            <td colspan="7" class="text-center"><strong>TOTAL NERACA SEBELUM LABA/RUGI</strong></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['balance_sheet']['debit'], 2)); ?></td>
                            <td style="text-align: right;"><?php echo e(number_format($worksheetData['totals']['balance_sheet']['credit'], 2)); ?></td>
                        </tr>
                        
                        <!-- Total Neraca (Setelah Laba/Rugi) -->
                        <tr class="total-row">
                            <td colspan="7" class="text-center"><strong>TOTAL NERACA SETELAH LABA/RUGI</strong></td>
                            <td style="text-align: right;">
                                <?php echo e(number_format($worksheetData['totals']['balance_sheet']['debit'] + ($worksheetData['net_income'] < 0 ? abs($worksheetData['net_income']) : 0), 2)); ?>

                            </td>
                            <td style="text-align: right;">
                                <?php echo e(number_format($worksheetData['totals']['balance_sheet']['credit'] + ($worksheetData['net_income'] >= 0 ? $worksheetData['net_income'] : 0), 2)); ?>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="file-text" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Tampilkan Neraca Lajur</h5>
                <p class="text-muted">Silakan pilih tahun buku dan periode untuk melihat neraca lajur</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- modal export di bagian bawah view -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i data-feather="download"></i> Export Neraca Lajur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="POST" action="<?php echo e(route('financial.worksheet.export')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="book_id" value="<?php echo e($selectedBookId); ?>">
                <input type="hidden" name="date_from" value="<?php echo e($dateFrom ? $dateFrom->format('Y-m-d') : ''); ?>">
                <input type="hidden" name="date_to" value="<?php echo e($dateTo ? $dateTo->format('Y-m-d') : ''); ?>">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="export_type" id="exportPdf" value="pdf" checked>
                            <label class="form-check-label" for="exportPdf">
                                PDF (Portable Document Format)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="export_type" id="exportExcel" value="excel">
                            <label class="form-check-label" for="exportExcel">
                                Excel (Microsoft Excel)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    feather.replace();
    
    // Export button handler
    $('#exportBtn').click(function() {
        $('#exportModal').modal('show');
    });

    $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
        $('#exportModal').modal('hide');
    });
    
    // Export form submission
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Disable button and show loading
        $submitBtn.prop('disabled', true).html('<i data-feather="loader"></i> Exporting...');
        feather.replace();
        
        // Submit form via AJAX
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            xhrFields: {
                responseType: 'blob' // Important for file download
            },
            success: function(data, status, xhr) {
                // Get filename from content-disposition header or use default
                var disposition = xhr.getResponseHeader('content-disposition');
                var filename = 'export';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].split(';')[0];
                    // Remove quotes if present
                    filename = filename.replace(/['"]/g, '');
                }
                
                // Create download link
                var blob = new Blob([data]);
                var downloadUrl = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                
                // Clean up
                setTimeout(function() {
                    URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                }, 100);
            },
            error: function(xhr) {
                alert('Export failed: ' + xhr.statusText);
            },
            complete: function() {
                // Re-enable button and reset text
                $submitBtn.prop('disabled', false).html('Export');
                feather.replace();
                
                // Close modal
                $('#exportModal').modal('hide');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\worksheet\index.blade.php ENDPATH**/ ?>