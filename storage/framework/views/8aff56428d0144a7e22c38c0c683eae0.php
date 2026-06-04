<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Quality Control Proses Produksi Egg Tofu Mentah</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            margin: 20px; /* Added margin to prevent edge sticking */
            padding: 10px;
        }
        
        /* Header Styles */
        .header-container {
            width: 100%;
            border: 2px solid #000;
            margin-bottom: 15px;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f5f5f5;
            line-height: 60px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        
        .company-info {
            width: 55%;
            text-align: center;
            vertical-align: middle;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .form-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .document-info {
            width: 30%;
            vertical-align: top;
        }
        
        .doc-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .doc-info-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9px;
        }
        
        .doc-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 40%;
        }
        
        /* Period Section */
        .period-section {
            text-align: center;
            margin: 15px 0;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Main Table Styles */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }
        
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }
        
        .main-table td {
            font-size: 8px;
        }
        
        /* Column widths - Updated structure (removed filling_kuantitas column) */
        .col-no { width: 3%; }
        .col-date { width: 7%; }
        .col-code { width: 8%; }
        .col-perendaman-waktu { width: 5%; }
        .col-perendaman-kuantitas { width: 5%; }
        .col-reject-telur { width: 5%; }
        .col-pasteurisasi-waktu { width: 5%; }
        .col-pasteurisasi-suhu { width: 5%; }
        .col-berat-akhir { width: 6%; }
        .col-pencampuran { width: 5%; }
        .col-filling-waktu { width: 5%; }
        .col-mesin { width: 6%; }
        .col-total { width: 6%; }
        .col-reject-mentah { width: 6%; }
        
        /* Text alignment */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* No data message */
        .no-data {
            text-align: center;
            padding: 40px;
            font-style: italic;
            color: #666;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        /* Print optimizations */
        @media print {
            body { 
                font-size: 9px; 
                margin: 15px;
                padding: 5px;
            }
            .main-table { font-size: 8px; }
            .main-table th, .main-table td { padding: 2px 1px; }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <?php if($companyLogo && file_exists(storage_path('app/public/' . $companyLogo))): ?>
                        <img src="<?php echo e(storage_path('app/public/' . $companyLogo)); ?>" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                    <?php else: ?>
                        <div class="company-logo">LOGO</div>
                    <?php endif; ?>
                </td>
                <td class="company-info">
                    <div class="company-name"><?php echo e($companyName); ?></div>
                    <div class="form-title">FORMULIR QUALITY CONTROL PROSES PRODUKSI EGG TOFU MENTAH</div>
                </td>
                <td class="document-info">
                    <table class="doc-info-table">
                        <tr>
                            <td class="doc-label">Nomor Dokumen</td>
                            <td><?php echo e($documentNumber); ?></td>
                        </tr>
                        <tr>
                            <td class="doc-label">Revisi</td>
                            <td><?php echo e($revision); ?></td>
                        </tr>
                        <tr>
                            <td class="doc-label">Tanggal</td>
                            <td><?php echo e($currentDate); ?></td>
                        </tr>
                        <tr>
                            <td class="doc-label">Halaman</td>
                            <td>1 dari 1</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Period Section -->
    <div class="period-section">
        <?php echo e($period); ?>

    </div>

    <!-- Main QC Data Table -->
    <?php if($qcData->count() > 0): ?>
    <table class="main-table">
        <thead>
            <!-- Header Row 1 -->
            <tr>
                <th rowspan="3" class="col-no">No</th>
                <th rowspan="3" class="col-date">Tanggal Produksi</th>
                <th rowspan="3" class="col-code">Kode Produk</th>
                <th colspan="2">Perendaman Kacang Kedelai</th>
                <th rowspan="3" class="col-reject-telur">Jumlah Reject Telur</th>
                <th colspan="2">Pasteurisasi</th>
                <th rowspan="3" class="col-berat-akhir">Berat Akhir Sari Kedelai</th>
                <th rowspan="3" class="col-pencampuran">Pencampuran</th>
                <th colspan="4">Filling & Pengemasan</th>
                <th rowspan="3" class="col-reject-mentah">Jumlah Reject Mentah</th>
            </tr>
            <!-- Header Row 2 -->
            <tr>
                <th rowspan="2" class="col-perendaman-waktu">Waktu</th>
                <th rowspan="2" class="col-perendaman-kuantitas">Kuantitas</th>
                <th rowspan="2" class="col-pasteurisasi-waktu">Waktu</th>
                <th rowspan="2" class="col-pasteurisasi-suhu">Suhu (°C)</th>
                <th rowspan="2" class="col-filling-waktu">Waktu</th>
                <th colspan="2">Kuantitas Mesin</th>
                <th rowspan="2" class="col-total">Total Kuantitas</th>
            </tr>
            <!-- Header Row 3 -->
            <tr>
                <th class="col-mesin">Mesin 1</th>
                <th class="col-mesin">Mesin 2</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $qcData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($data['no']); ?></td>
                <td class="text-center"><?php echo e($data['tanggal_produksi']); ?></td>
                <td class="text-center"><?php echo e($data['kode_produk']); ?></td>
                
                <!-- Perendaman Kacang Kedelai -->
                <td class="text-center"><?php echo e($data['perendaman_waktu']); ?></td>
                <td class="text-center"><?php echo e($data['perendaman_kuantitas']); ?></td>
                
                <!-- Jumlah Reject Telur -->
                <td class="text-center"><?php echo e($data['reject_telur_kuantitas']); ?></td>
                
                <!-- Pasteurisasi -->
                <td class="text-center"><?php echo e($data['pasteurisasi_waktu']); ?></td>
                <td class="text-center"><?php echo e($data['pasteurisasi_suhu']); ?></td>
                
                <!-- Berat Akhir Sari Kedelai -->
                <td class="text-center"><?php echo e($data['berat_akhir_sari_kedelai']); ?></td>
                
                <!-- Pencampuran -->
                <td class="text-center"><?php echo e($data['pencampuran_waktu']); ?></td>
                
                <!-- Filling & Pengemasan -->
                <td class="text-center"><?php echo e($data['filling_waktu']); ?></td>
                <td class="text-center"><?php echo e($data['filling_mesin_1']); ?></td>
                <td class="text-center"><?php echo e($data['filling_mesin_2']); ?></td>
                <td class="text-center"><?php echo e($data['total_kuantitas']); ?></td>
                
                <!-- Jumlah Reject Mentah -->
                <td class="text-center"><?php echo e($data['jumlah_reject_mentah']); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">
        <p><strong>Data QC tidak tersedia</strong></p>
        <p>Tidak ada data produksi tofu dengan QC data yang sesuai dengan filter yang diterapkan.</p>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan dibuat pada: <?php echo e($exportDate); ?></p>
        <p>Total <?php echo e($totalRecords); ?> data QC Egg Tofu Mentah</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\qc-tofu-mentah-pdf.blade.php ENDPATH**/ ?>