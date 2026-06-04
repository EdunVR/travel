<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Akuntansi - <?php echo e($book->name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .book-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .summary-section {
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .balance-positive {
            color: #059669;
            font-weight: bold;
        }
        
        .balance-negative {
            color: #dc2626;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    
    <div class="header">
        <div class="company-name"><?php echo e($companySettings['company_name'] ?? config('app.name')); ?></div>
        <div><?php echo e($companySettings['company_address'] ?? ''); ?></div>
        <?php if(isset($companySettings['company_phone'])): ?>
            <div>Telp: <?php echo e($companySettings['company_phone']); ?></div>
        <?php endif; ?>
        <div class="report-title">LAPORAN BUKU AKUNTANSI</div>
    </div>

    
    <div class="book-info">
        <div class="info-row">
            <span class="info-label">Kode Buku:</span>
            <span><?php echo e($book->code); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama Buku:</span>
            <span><?php echo e($book->name); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipe:</span>
            <span><?php echo e(ucfirst($book->type)); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Outlet:</span>
            <span><?php echo e($book->outlet->nama_outlet); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span><?php echo e(\Carbon\Carbon::parse($book->start_date)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($book->end_date)->format('d/m/Y')); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span><?php echo e(ucfirst($book->status)); ?><?php echo e($book->is_locked ? ' (Terkunci)' : ''); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Mata Uang:</span>
            <span><?php echo e($book->currency); ?></span>
        </div>
    </div>

    
    <div class="summary-section">
        <div class="summary-title">RINGKASAN</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value"><?php echo e(number_format($totalEntries)); ?></div>
                <div class="summary-label">Total Entri Jurnal</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo e(number_format($totalDebit, 0, ',', '.')); ?></div>
                <div class="summary-label">Total Debit</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo e(number_format($totalCredit, 0, ',', '.')); ?></div>
                <div class="summary-label">Total Kredit</div>
            </div>
        </div>
    </div>

    
    <div class="summary-section">
        <div class="summary-title">SALDO AKUN</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15%">Kode Akun</th>
                    <th style="width: 35%">Nama Akun</th>
                    <th style="width: 10%">Tipe</th>
                    <th style="width: 15%">Total Debit</th>
                    <th style="width: 15%">Total Kredit</th>
                    <th style="width: 15%">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $accountBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($balance['account']->code); ?></td>
                    <td><?php echo e($balance['account']->name); ?></td>
                    <td class="text-center"><?php echo e(ucfirst($balance['account']->type)); ?></td>
                    <td class="text-right"><?php echo e(number_format($balance['debit'], 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($balance['credit'], 0, ',', '.')); ?></td>
                    <td class="text-right <?php echo e($balance['balance'] >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                        <?php echo e(number_format($balance['balance'], 0, ',', '.')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data saldo akun</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($book->journalEntries->count() > 0): ?>
    <div class="page-break"></div>
    <div class="summary-section">
        <div class="summary-title">DAFTAR ENTRI JURNAL</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15%">No. Transaksi</th>
                    <th style="width: 12%">Tanggal</th>
                    <th style="width: 35%">Deskripsi</th>
                    <th style="width: 15%">Total Debit</th>
                    <th style="width: 15%">Total Kredit</th>
                    <th style="width: 8%">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $book->journalEntries->sortBy('transaction_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($entry->transaction_number); ?></td>
                    <td class="text-center"><?php echo e(\Carbon\Carbon::parse($entry->transaction_date)->format('d/m/Y')); ?></td>
                    <td><?php echo e($entry->description); ?></td>
                    <td class="text-right"><?php echo e(number_format($entry->total_debit, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($entry->total_credit, 0, ',', '.')); ?></td>
                    <td class="text-center"><?php echo e(ucfirst($entry->status)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL:</td>
                    <td class="text-right"><?php echo e(number_format($totalDebit, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($totalCredit, 0, ',', '.')); ?></td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>

    
    <div class="footer">
        <div>Laporan digenerate pada: <?php echo e($generatedAt->format('d/m/Y H:i:s')); ?></div>
        <div>Sistem Akuntansi - <?php echo e(config('app.name')); ?></div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\buku\report-pdf.blade.php ENDPATH**/ ?>