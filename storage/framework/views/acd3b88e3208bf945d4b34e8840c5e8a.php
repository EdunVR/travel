<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Akuntansi</title>
    <style>
        @page {
            margin: 10mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 5mm;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1a1a1a;
        }

        .header h2 {
            font-size: 14pt;
            font-weight: normal;
            margin-bottom: 3px;
            color: #555;
        }

        .header .company-info {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #4472C4;
        }

        .filter-info p {
            margin: 3px 0;
            font-size: 9pt;
        }

        .filter-info strong {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #4472C4;
            color: white;
        }

        table thead th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #4A7C59;
        }

        table tbody td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .status-closed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .summary h3 {
            font-size: 11pt;
            margin-bottom: 10px;
            color: #333;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-label {
            display: table-cell;
            padding: 5px;
            font-weight: bold;
            width: 40%;
        }

        .summary-value {
            display: table-cell;
            padding: 5px;
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        @media print {
            body {
                margin: 0;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>LAPORAN BUKU AKUNTANSI</h1>
        <h2><?php echo e($companyName ?? 'PT. NAMA PERUSAHAAN'); ?></h2>
        <div class="company-info">
            <?php echo e($companyAddress ?? 'Alamat Perusahaan'); ?><br>
            Telp: <?php echo e($companyPhone ?? '-'); ?> | Email: <?php echo e($companyEmail ?? '-'); ?>

        </div>
    </div>

    
    <?php if(isset($filters) && count($filters) > 0): ?>
    <div class="filter-info">
        <p><strong>Filter yang Diterapkan:</strong></p>
        <?php if(isset($filters['outlet'])): ?>
            <p>Outlet: <?php echo e($filters['outlet']); ?></p>
        <?php endif; ?>
        <?php if(isset($filters['type']) && $filters['type'] !== 'all'): ?>
            <p>Tipe: <?php echo e($filters['type']); ?></p>
        <?php endif; ?>
        <?php if(isset($filters['status']) && $filters['status'] !== 'all'): ?>
            <p>Status: <?php echo e($filters['status']); ?></p>
        <?php endif; ?>
        <?php if(isset($filters['period'])): ?>
            <p>Periode: <?php echo e($filters['period']); ?></p>
        <?php endif; ?>
        <p>Tanggal Cetak: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    </div>
    <?php endif; ?>

    
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Kode</th>
                <th style="width: 18%;">Nama Buku</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 8%;">Mata Uang</th>
                <th style="width: 12%;">Periode</th>
                <th style="width: 12%;" class="text-right">Saldo Awal</th>
                <th style="width: 12%;" class="text-right">Saldo Akhir</th>
                <th style="width: 8%;" class="text-center">Entri</th>
                <th style="width: 12%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($book->code); ?></td>
                <td><?php echo e($book->name); ?></td>
                <td><?php echo e($book->type_name ?? $book->type); ?></td>
                <td><?php echo e($book->currency); ?></td>
                <td>
                    <?php echo e($book->start_date ? \Carbon\Carbon::parse($book->start_date)->format('d/m/Y') : '-'); ?>

                    s/d
                    <?php echo e($book->end_date ? \Carbon\Carbon::parse($book->end_date)->format('d/m/Y') : '-'); ?>

                </td>
                <td class="text-right"><?php echo e(number_format($book->opening_balance, 0, ',', '.')); ?></td>
                <td class="text-right"><?php echo e(number_format($book->closing_balance, 0, ',', '.')); ?></td>
                <td class="text-center"><?php echo e($book->total_entries); ?></td>
                <td class="text-center">
                    <span class="status-badge status-<?php echo e($book->status); ?>">
                        <?php echo e($book->status_name ?? $book->status); ?>

                    </span>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data buku akuntansi</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <?php if(isset($summary) && count($data) > 0): ?>
    <div class="summary">
        <h3>Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-label">Total Buku:</div>
                <div class="summary-value"><?php echo e($summary['total_books'] ?? count($data)); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Buku Aktif:</div>
                <div class="summary-value"><?php echo e($summary['active_books'] ?? 0); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Entri:</div>
                <div class="summary-value"><?php echo e(number_format($summary['total_entries'] ?? 0, 0, ',', '.')); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Saldo Awal:</div>
                <div class="summary-value"><?php echo e(number_format($summary['total_opening_balance'] ?? 0, 0, ',', '.')); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Saldo Akhir:</div>
                <div class="summary-value"><?php echo e(number_format($summary['total_closing_balance'] ?? 0, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis pada <?php echo e(now()->format('d F Y, H:i')); ?> WIB</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\buku\pdf.blade.php ENDPATH**/ ?>