<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Jurnal Umum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16pt;
            margin: 5px 0;
        }
        .header h2 {
            font-size: 13pt;
            margin: 5px 0;
            color: #4F46E5;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #4F46E5;
        }
        .filter-info p {
            margin: 3px 0;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead tr {
            background-color: #4F46E5;
            color: white;
        }
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8pt;
            border: 1px solid #4F46E5;
        }
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .debit { color: #059669; }
        .credit { color: #DC2626; }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .status-draft { background-color: #FEF3C7; color: #92400E; }
        .status-posted { background-color: #D1FAE5; color: #065F46; }
        .status-void { background-color: #FEE2E2; color: #991B1B; }
        tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }
        tfoot td {
            padding: 10px 5px;
            border: 2px solid #4F46E5;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #4F46E5;
        }
        .summary-item {
            margin: 5px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo e($filters['company_name'] ?? config('app.name', 'Nama Perusahaan')); ?></h1>
        <h2>DAFTAR JURNAL UMUM</h2>
        <?php if(isset($filters['outlet_name'])): ?>
            <p style="font-size: 9pt;"><?php echo e($filters['outlet_name']); ?></p>
        <?php endif; ?>
    </div>

    <?php if(!empty($filters) && (isset($filters['date_from']) || isset($filters['status']) || isset($filters['book_name']))): ?>
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        <?php if(isset($filters['date_from']) && isset($filters['date_to'])): ?>
            <p>Periode: <?php echo e(\Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y')); ?></p>
        <?php endif; ?>
        <?php if(isset($filters['status']) && $filters['status'] !== 'all'): ?>
            <p>Status: <?php echo e(ucfirst($filters['status'])); ?></p>
        <?php endif; ?>
        <?php if(isset($filters['book_name'])): ?>
            <p>Buku: <?php echo e($filters['book_name']); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 11%;">No. Transaksi</th>
                <th style="width: 9%;">Kode Akun</th>
                <th style="width: 17%;">Nama Akun</th>
                <th style="width: 20%;">Deskripsi</th>
                <th style="width: 12%;" class="text-right">Debit</th>
                <th style="width: 12%;" class="text-right">Kredit</th>
                <th style="width: 6%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalDebit = 0;
                $totalCredit = 0;
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($journal->transaction_date)->format('d/m/Y')); ?></td>
                    <td><?php echo e($journal->transaction_number); ?></td>
                    <td><?php echo e($journal->account_code); ?></td>
                    <td><?php echo e($journal->account_name); ?></td>
                    <td><?php echo e($journal->description); ?></td>
                    <td class="text-right amount debit">
                        <?php echo e($journal->debit > 0 ? 'Rp ' . number_format($journal->debit, 0, ',', '.') : '-'); ?>

                    </td>
                    <td class="text-right amount credit">
                        <?php echo e($journal->credit > 0 ? 'Rp ' . number_format($journal->credit, 0, ',', '.') : '-'); ?>

                    </td>
                    <td class="text-center">
                        <span class="status-badge status-<?php echo e($journal->status); ?>">
                            <?php if($journal->status === 'draft'): ?>
                                Draft
                            <?php elseif($journal->status === 'posted'): ?>
                                Posted
                            <?php elseif($journal->status === 'void'): ?>
                                Void
                            <?php else: ?>
                                <?php echo e(ucfirst($journal->status)); ?>

                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
                <?php
                    $totalDebit += $journal->debit;
                    $totalCredit += $journal->credit;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Tidak ada data jurnal
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if(count($data) > 0): ?>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right amount debit">
                    <strong>Rp <?php echo e(number_format($totalDebit, 0, ',', '.')); ?></strong>
                </td>
                <td class="text-right amount credit">
                    <strong>Rp <?php echo e(number_format($totalCredit, 0, ',', '.')); ?></strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <?php if(count($data) > 0): ?>
    <div class="summary">
        <div class="summary-item">
            Total Debit: <span class="amount debit">Rp <?php echo e(number_format($totalDebit, 0, ',', '.')); ?></span>
        </div>
        <div class="summary-item">
            Total Kredit: <span class="amount credit">Rp <?php echo e(number_format($totalCredit, 0, ',', '.')); ?></span>
        </div>
        <div class="summary-item" style="border-top: 1px solid #999; padding-top: 5px; margin-top: 5px;">
            Selisih: 
            <span class="amount" style="color: <?php echo e(abs($totalDebit - $totalCredit) < 0.01 ? '#059669' : '#DC2626'); ?>;">
                Rp <?php echo e(number_format(abs($totalDebit - $totalCredit), 0, ',', '.')); ?>

                <?php if(abs($totalDebit - $totalCredit) < 0.01): ?>
                    (Seimbang)
                <?php else: ?>
                    (Tidak Seimbang)
                <?php endif; ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(now()->format('d F Y, H:i:s')); ?> WIB</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\jurnal\pdf.blade.php ENDPATH**/ ?>