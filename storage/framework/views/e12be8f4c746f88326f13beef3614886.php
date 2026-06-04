<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Egg Tofu Mentah Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            background-color: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .form-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .form-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 8px;
        }
        
        .form-info-item {
            border: 1px solid #000;
            padding: 2px 5px;
        }
        
        .month-year {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
        }
        
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .main-table .header-row-1 th {
            font-size: 7px;
        }
        
        .main-table .header-row-2 th {
            font-size: 6px;
        }
        
        .main-table .data-row td {
            font-size: 6px;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .rotate-text {
            writing-mode: vertical-lr;
            text-orientation: mixed;
            transform: rotate(180deg);
            white-space: nowrap;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        /* Column widths to match the image */
        .col-no { width: 3%; }
        .col-date { width: 8%; }
        .col-code { width: 10%; }
        .col-perendaman { width: 6%; }
        .col-jumlah-reject { width: 5%; }
        .col-pasteurisasi { width: 6%; }
        .col-berat { width: 5%; }
        .col-pencampuran { width: 6%; }
        .col-waktu { width: 5%; }
        .col-filling { width: 8%; }
        .col-mesin { width: 8%; }
        .col-total { width: 6%; }
        .col-jumlah-reject-mentah { width: 6%; }
        
        @media print {
            body { font-size: 7px; }
            .main-table { font-size: 6px; }
            .main-table .data-row td { font-size: 5px; }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="company-logo">
            PNI
        </div>
        <div class="company-name">PT.PELITA NUSANTARA INDONESIA</div>
        <div class="form-title">FORMULIR QUALITY CONTROL PROSES PRODUKSI EGG TOFU MENTAH</div>
        
        <div class="form-info">
            <div class="form-info-item">PNI/FSOP/QC/01-2</div>
            <div class="form-info-item">Revisi : 00</div>
            <div class="form-info-item">Tanggal : 4 Juni 2025</div>
            <div class="form-info-item">Halaman : 1 dari 1</div>
        </div>
    </div>

    <!-- Month/Year Display -->
    <div class="month-year">
        <?php echo e(date('F Y')); ?>

    </div>

    <!-- Main QC Data Table -->
    <?php if($qcData->count() > 0): ?>
    <table class="main-table">
        <thead>
            <!-- Header Row 1 -->
            <tr class="header-row-1">
                <th rowspan="2" class="col-no">No</th>
                <th rowspan="2" class="col-date">Tanggal Produksi</th>
                <th rowspan="2" class="col-code">Kode Produk</th>
                <th colspan="2">Perendaman Kacang Kedelai</th>
                <th colspan="2">Pasteurisasi</th>
                <th colspan="2">Berat Adonan Pencampuran</th>
                <th colspan="2">Filling & Pengemasan</th>
                <th rowspan="2" class="col-total">Total Kuantitas</th>
                <th rowspan="2" class="col-jumlah-reject-mentah">Jumlah Reject Mentah</th>
            </tr>
            <!-- Header Row 2 -->
            <tr class="header-row-2">
                <th class="col-perendaman">Waktu</th>
                <th class="col-jumlah-reject">Kuantitas</th>
                <th class="col-pasteurisasi">Waktu</th>
                <th class="col-berat">Suhu (°C)</th>
                <th class="col-pencampuran">Waktu</th>
                <th class="col-waktu">Kuantitas</th>
                <th class="col-filling">Waktu</th>
                <th class="col-mesin">Mesin 1</th>
                <th class="col-mesin">Mesin 2</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $qcData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $qc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="data-row">
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td class="text-center"><?php echo e($qc['start_date']); ?></td>
                <td class="text-center"><?php echo e($qc['production_code']); ?></td>
                
                <!-- Perendaman Kacang Kedelai -->
                <td class="text-center"><?php echo e($qc['qc_data']['perendaman_waktu'] ?? '-'); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['perendaman_kuantitas'] ?? ($qc['qc_data']['rijek_telur'] ?? '-')); ?></td>
                
                <!-- Pasteurisasi -->
                <td class="text-center"><?php echo e($qc['qc_data']['pasteurisasi_waktu'] ?? '-'); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['pasteurisasi_suhu'] ?? '-'); ?></td>
                
                <!-- Berat Adonan Pencampuran -->
                <td class="text-center"><?php echo e($qc['qc_data']['pencampuran_waktu'] ?? ($qc['qc_data']['homogenisasi_waktu'] ?? '-')); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['pencampuran_kuantitas'] ?? ($qc['qc_data']['target_quantity'] ?? '-')); ?></td>
                
                <!-- Filling & Pengemasan -->
                <td class="text-center"><?php echo e($qc['qc_data']['packaging_waktu'] ?? ($qc['qc_data']['filling_waktu'] ?? '-')); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['mesin_1'] ?? ($qc['qc_data']['realized_quantity_1'] ?? '-')); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['mesin_2'] ?? ($qc['qc_data']['realized_quantity_2'] ?? '-')); ?></td>
                
                <!-- Total & Reject -->
                <td class="text-center"><?php echo e($qc['qc_data']['total_kuantitas'] ?? ($qc['total_realized'] ?? '-')); ?></td>
                <td class="text-center"><?php echo e($qc['qc_data']['jumlah_reject_mentah'] ?? ($qc['rejected_quantity'] ?? '-')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">
        <p>Tidak ada data QC Egg Tofu Mentah yang tersedia.</p>
        <p style="margin-top: 10px;">
            Pastikan ada produksi dengan business_type = 'tofu' dan memiliki data QC.
        </p>
    </div>
    <?php endif; ?>

    <!-- Footer Information -->
    <div style="margin-top: 20px; font-size: 7px; text-align: center; color: #666;">
        <p>Laporan dibuat pada: <?php echo e($filters['export_date']); ?></p>
        <p>Total <?php echo e($filters['total_count']); ?> data QC Egg Tofu Mentah</p>
        <?php if($filters['outlet'] !== 'Semua Outlet'): ?>
        <p>Outlet: <?php echo e($filters['outlet']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\bulk-qc-tofu-pdf.blade.php ENDPATH**/ ?>