

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
    }
    .table td {
        vertical-align: middle;
    }
    .balance-positive {
        color: #2e7d32;
        font-weight: bold;
    }
    .balance-negative {
        color: #c62828;
        font-weight: bold;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .account-info {
        background-color: #e8f5e9;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .summary-card {
        border-left: 4px solid #2e7d32;
        margin-bottom: 20px;
    }
    .feather {
        width: 16px;
        height: 16px;
        vertical-align: text-bottom;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
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
                <i data-feather="book"></i> Buku Besar
            </h5>
            <div>
                <?php if(isset($account)): ?>
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
                        
                        <div class="col-md-4">
                            <label for="account_code" class="form-label">Akun</label>
                            <select name="account_code" id="account_code" class="form-select">
                                <option value="">Pilih Akun</option>
                                <option value="all" <?php echo e(request('account_code') == 'all' ? 'selected' : ''); ?>>-- LIHAT SEMUA --</option>
                                <?php $__currentLoopData = $accountOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($code); ?>" <?php echo e(request('account_code') == $code ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                value="<?php echo e($dateFrom ? $dateFrom->format('Y-m-d') : ''); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                value="<?php echo e($dateTo ? $dateTo->format('Y-m-d') : ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="search"></i> Tampilkan
                            </button>
                            <button type="button" id="resetFilter" class="btn btn-secondary">
                                <i data-feather="refresh-ccw"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if(isset($account)): ?>
            <!-- Account Information -->
            <div class="account-info">
                <div class="row">
                    <div class="col-md-6">
                        <h5>
                            <strong><?php echo e($account['code']); ?> - <?php echo e($account['name']); ?></strong>
                        </h5>
                        <p class="mb-1">
                            <strong>Tahun Buku:</strong> <?php echo e($accountingBook->name); ?> 
                            (<?php echo e($accountingBook->start_date->format('d/m/Y')); ?> - <?php echo e($accountingBook->end_date->format('d/m/Y')); ?>)
                        </p>
                        <p class="mb-1">
                            <strong>Periode:</strong> 
                            <?php if($dateFrom && $dateTo): ?>
                                <?php echo e($dateFrom->format('d/m/Y')); ?> - <?php echo e($dateTo->format('d/m/Y')); ?>

                            <?php else: ?>
                                Semua Periode
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="summary-card p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Saldo Awal:</strong>
                                    <span class="<?php echo e($initialBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                        <?php echo e(number_format($initialBalance, 2)); ?>

                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Saldo Akhir:</strong>
                                    <span class="<?php echo e($endingBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                        <?php echo e(number_format($endingBalance, 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ledger Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>No. Jurnal</th>
                            <th>No. Bukti</th>
                            <th>Keterangan</th>
                            <th>Sub Kelas</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Initial Balance Row -->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>SALDO AWAL</strong></td>
                            <td></td>
                            <td style="text-align: right;"><?php echo e($initialBalance > 0 && !$isCreditAccount ? number_format($initialBalance, 2) : ''); ?></td>
                            <td style="text-align: right;"><?php echo e($initialBalance > 0 && $isCreditAccount ? number_format($initialBalance, 2) : ''); ?></td>
                            <td style="text-align: right; <?php echo e($initialBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                <?php echo e(number_format($initialBalance, 2)); ?>

                            </td>
                        </tr>
                        
                        <!-- Transaction Rows -->
                        <?php $__currentLoopData = $ledgerEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($entry['date']->format('d/m/Y')); ?></td>
                            <td><?php echo e($entry['journal_number']); ?></td>
                            <td><?php echo e($entry['reference_number']); ?></td>
                            <td><?php echo e($entry['description']); ?></td>
                            <td><?php echo e($entry['sub_class']); ?></td>
                            <td style="text-align: right;"><?php echo e($entry['debit'] > 0 ? number_format($entry['debit'], 2) : ''); ?></td>
                            <td style="text-align: right;"><?php echo e($entry['credit'] > 0 ? number_format($entry['credit'], 2) : ''); ?></td>
                            <td style="text-align: right; <?php echo e($entry['balance'] >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                <?php echo e(number_format($entry['balance'], 2)); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <th colspan="6" class="text-end">TOTAL</th>
                            <th style="text-align: right;"><?php echo e(number_format($totalDebit, 2)); ?></th>
                            <th style="text-align: right;"><?php echo e(number_format($totalCredit, 2)); ?></th>
                            <th style="text-align: right; <?php echo e($endingBalance >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                <?php echo e(number_format($endingBalance, 2)); ?>

                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="book-open" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Pilih Akun untuk Menampilkan Buku Besar</h5>
                <p class="text-muted">Silakan pilih akun dari dropdown di atas untuk melihat detail transaksi</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    feather.replace();
    
    // Reset filter
    $('#resetFilter').click(function() {
        $('#filterForm')[0].reset();
        $('#book_id').val('<?php echo e($books->first()->id ?? ''); ?>');
        window.location.href = "<?php echo e(route('financial.ledger.index')); ?>?book_id=" + $('#book_id').val();
    });
    
    // Auto submit when account is selected
    $('#account_code').change(function() {
        if ($(this).val()) {
            $('#filterForm').submit();
        }
    });

    // Export button handler
    $(document).on('click', '#exportBtn', function(e) {
        e.preventDefault();
        
        // Create a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "<?php echo e(route('financial.ledger.export')); ?>";
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = "<?php echo e(csrf_token()); ?>";
        form.appendChild(csrfToken);
        
        // Add current filter values
        const bookId = document.createElement('input');
        bookId.type = 'hidden';
        bookId.name = 'book_id';
        bookId.value = $('#book_id').val();
        form.appendChild(bookId);
        
        const accountCode = document.createElement('input');
        accountCode.type = 'hidden';
        accountCode.name = 'account_code';
        accountCode.value = $('#account_code').val();
        form.appendChild(accountCode);
        
        const dateFrom = document.createElement('input');
        dateFrom.type = 'hidden';
        dateFrom.name = 'date_from';
        dateFrom.value = $('#date_from').val();
        form.appendChild(dateFrom);
        
        const dateTo = document.createElement('input');
        dateTo.type = 'hidden';
        dateTo.name = 'date_to';
        dateTo.value = $('#date_to').val();
        form.appendChild(dateTo);
        
        // Add form to body and submit
        document.body.appendChild(form);
        form.submit();
        
        // Clean up
        setTimeout(() => document.body.removeChild(form), 100);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\ledger\index.blade.php ENDPATH**/ ?>