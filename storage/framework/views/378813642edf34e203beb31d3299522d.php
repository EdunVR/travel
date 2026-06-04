

<?php $__env->startSection('content'); ?>
<style>
    .card-header {
        background-color: #2e7d32;
        color: white;
    }
    .balance-sheet-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .balance-column {
        flex: 1;
        min-width: 300px;
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
    .text-center {
        text-align: center;
    }
    .asset-account { background-color: #e8f5e9; }
    .liability-account { background-color: #ffebee; }
    .equity-account { background-color: #e3f2fd; }
    .summary-row {
        font-weight: bold;
        background-color: #f5f5f5 !important;
    }
    .total-row {
        font-weight: bold;
        background-color: #e8f5e9 !important;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .positive-amount {
        color: #2e7d32;
    }
    .negative-amount {
        color: #c62828;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="bar-chart-2"></i> Neraca
            </h5>
            <div>
                <?php if(isset($balanceSheetData)): ?>
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
            
            <?php if(isset($balanceSheetData)): ?>
            <!-- Report Information -->
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
            
            <!-- Balance Sheet -->
            <div class="balance-sheet-container">
                <!-- Aktiva Column -->
                <div class="balance-column">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th colspan="2" class="text-center">AKTIVA</th>
                                </tr>
                            </thead>
                            
                            <!-- Current Assets -->
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">AKTIVA LANCAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['assets']['current']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="asset-account">
                                    <td style="text-align: left;"><?php echo e($asset['code']); ?> <?php echo e($asset['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($asset['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL AKTIVA LANCAR</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['assets']['total_current'], 2)); ?></td>
                                </tr>
                            </tbody>
                            
                            <!-- Fixed Assets -->
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">AKTIVA TETAP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['assets']['fixed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="asset-account">
                                    <td style="text-align: left;"><?php echo e($asset['code']); ?> <?php echo e($asset['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($asset['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL AKTIVA TETAP</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['assets']['total_fixed'], 2)); ?></td>
                                </tr>
                            </tbody>
                            
                            <!-- Other Assets -->
                            <?php if(!empty($balanceSheetData['assets']['other'])): ?>
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">AKTIVA LAINNYA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['assets']['other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="asset-account">
                                    <td style="text-align: left;"><?php echo e($asset['code']); ?> <?php echo e($asset['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($asset['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL AKTIVA LAINNYA</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['assets']['total_other'], 2)); ?></td>
                                </tr>
                            </tbody>
                            <?php endif; ?>
                            
                            <!-- Total Assets -->
                            <tfoot>
                                <tr class="total-row">
                                    <td class="text-center"><strong>TOTAL AKTIVA</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['assets']['total_assets'], 2)); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Pasiva Column -->
                <div class="balance-column">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th colspan="2" class="text-center">PASIVA</th>
                                </tr>
                            </thead>
                            
                            <!-- Current Liabilities -->
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">KEWAJIBAN LANCAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['liabilities']['current']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="liability-account">
                                    <td style="text-align: left;"><?php echo e($liability['code']); ?> <?php echo e($liability['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($liability['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL KEWAJIBAN LANCAR</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['liabilities']['total_current'], 2)); ?></td>
                                </tr>
                            </tbody>
                            
                            <!-- Long-term Liabilities -->
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">KEWAJIBAN JANGKA PANJANG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['liabilities']['long_term']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="liability-account">
                                    <td style="text-align: left;"><?php echo e($liability['code']); ?> <?php echo e($liability['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($liability['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL KEWAJIBAN JANGKA PANJANG</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['liabilities']['total_long_term'], 2)); ?></td>
                                </tr>
                            </tbody>
                            
                            <!-- Total Liabilities -->
                            <tr class="summary-row">
                                <td class="text-center"><strong>TOTAL KEWAJIBAN</strong></td>
                                <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['liabilities']['total_liabilities'], 2)); ?></td>
                            </tr>
                            
                            <!-- Equities -->
                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-center">MODAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $balanceSheetData['equities']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="equity-account">
                                    <td style="text-align: left;"><?php echo e($equity['code']); ?> <?php echo e($equity['name']); ?></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($equity['amount'], 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="summary-row">
                                    <td class="text-center"><strong>TOTAL MODAL</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['equities']['total_equities'], 2)); ?></td>
                                </tr>
                            </tbody>
                            
                            <!-- Total Liabilities & Equities -->
                            <tfoot>
                                <tr class="total-row">
                                    <td class="text-center"><strong>TOTAL KEWAJIBAN & MODAL</strong></td>
                                    <td class="positive-amount" style="text-align: right;"><?php echo e(number_format($balanceSheetData['total_liabilities_equities'], 2)); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="bar-chart-2" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Tampilkan Neraca</h5>
                <p class="text-muted">Silakan pilih tahun buku dan periode untuk melihat neraca</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i data-feather="download"></i> Export Neraca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="POST" action="<?php echo e(route('financial.balance-sheet.export')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="book_id" value="<?php echo e($selectedBookId ?? ''); ?>">
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
    
    // Export button handler - show modal
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
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Get filename from content-disposition header or use default
                var disposition = xhr.getResponseHeader('content-disposition');
                var filename = 'Neraca';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].split(';')[0];
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

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\balance-sheet\index.blade.php ENDPATH**/ ?>