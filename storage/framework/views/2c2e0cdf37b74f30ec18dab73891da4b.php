<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca - <?php echo e($outlet_name); ?></title>
    <style>
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
            margin: 20mm 15mm 20mm 15mm;
        }
        
        /* Professional Letterhead */
        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e40af;
            position: relative;
        }
        
        .logo-section {
            display: table-cell;
            width: 80px;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .logo-section img {
            width: 70px;
            height: auto;
            max-height: 70px;
            object-fit: contain;
        }
        
        .company-info {
            display: table-cell;
            vertical-align: top;
            text-align: center;
            width: auto;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-address {
            font-size: 10pt;
            color: #555;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .company-contact {
            font-size: 9pt;
            color: #666;
            margin-bottom: 15px;
        }

        /* Report Header */
        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }
        
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-outlet {
            font-size: 12pt;
            color: #475569;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .report-date {
            font-size: 11pt;
            color: #475569;
            font-weight: 500;
        }
        
        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 6px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: 600;
            width: 120px;
            padding: 4px 0;
            color: #374151;
        }

        .info-value {
            display: table-cell;
            padding: 4px 0;
            color: #1f2937;
        }
        
        .content {
            margin-top: 20px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 12px 15px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 6px 6px 0 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .section-title.liability {
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
        }
        
        .section-title.equity {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        
        .account-list {
            background: white;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .account-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .account-item:last-child {
            border-bottom: none;
        }
        
        .account-item.parent {
            font-weight: 600;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            color: #374151;
        }
        
        .account-item.child {
            margin-left: 25px;
            font-size: 9pt;
            color: #6b7280;
            background: #fefefe;
        }
        
        .account-info {
            display: flex;
            align-items: center;
        }
        
        .account-code {
            background: #eff6ff;
            color: #1e40af;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            font-weight: 600;
            margin-right: 10px;
            min-width: 60px;
            text-align: center;
        }
        
        .account-name {
            font-weight: inherit;
        }
        
        .account-balance {
            text-align: right;
            white-space: nowrap;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1f2937;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            margin-top: 10px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #94a3b8;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .grand-total {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
            color: white !important;
            font-size: 12pt !important;
            border-top: 3px solid #1e40af !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .grand-total.liability {
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%) !important;
            border-top: 3px solid #7c3aed !important;
        }
        
        .grand-total.equity {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            border-top: 3px solid #059669 !important;
        }
        
        .balance-check {
            margin-top: 30px;
            padding: 15px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 8px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .balance-check.balanced {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
            color: #065f46;
        }
        
        .balance-check.unbalanced {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
            color: #991b1b;
        }
        
        .two-column {
            display: table;
            width: 100%;
            margin-top: 20px;
            table-layout: fixed;
        }
        
        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 0 1%;
        }

        .column:first-child {
            padding-right: 2%;
        }

        .column:last-child {
            padding-left: 2%;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
        }

        .footer p {
            margin-bottom: 3px;
        }

        @media print {
            body {
                margin: 15mm 10mm 15mm 10mm;
            }
        }
    </style>
</head>
<body>
    <!-- Professional Letterhead -->
    <div class="letterhead">
        <?php if(isset($companySettings['logo_url']) && $companySettings['logo_url']): ?>
        <div class="logo-section">
            <img src="<?php echo e($companySettings['logo_url']); ?>" alt="Company Logo">
        </div>
        <?php endif; ?>
        <div class="company-info">
            <div class="company-name"><?php echo e($companySettings['company_name'] ?? 'Nama Perusahaan'); ?></div>
            <?php if(isset($companySettings['company_address']) && $companySettings['company_address']): ?>
            <div class="company-address"><?php echo e($companySettings['company_address']); ?></div>
            <?php endif; ?>
            <div class="company-contact">
                <?php if(isset($companySettings['company_phone']) && $companySettings['company_phone']): ?>
                    Telp: <?php echo e($companySettings['company_phone']); ?>

                <?php endif; ?>
                <?php if(isset($companySettings['company_email']) && $companySettings['company_email']): ?>
                    <?php if(isset($companySettings['company_phone']) && $companySettings['company_phone']): ?> | <?php endif; ?>
                    Email: <?php echo e($companySettings['company_email']); ?>

                <?php endif; ?>
                <?php if(isset($companySettings['company_website']) && $companySettings['company_website']): ?>
                    <?php if((isset($companySettings['company_phone']) && $companySettings['company_phone']) || (isset($companySettings['company_email']) && $companySettings['company_email'])): ?> | <?php endif; ?>
                    <?php echo e($companySettings['company_website']); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">Neraca (Balance Sheet)</div>
        <div class="report-outlet"><?php echo e($outlet_name); ?></div>
        <div class="report-date">Per Tanggal: <?php echo e(\Carbon\Carbon::parse($end_date)->format('d F Y')); ?></div>
    </div>
    
    <!-- Report Information -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value"><?php echo e($print_date); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Outlet:</div>
            <div class="info-value"><?php echo e($outlet_name); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Periode:</div>
            <div class="info-value">Per <?php echo e(\Carbon\Carbon::parse($end_date)->format('d F Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Mata Uang:</div>
            <div class="info-value">Rupiah (IDR)</div>
        </div>
    </div>
    
    <div class="two-column">
        
        <div class="column">
            <div class="section">
                <div class="section-title">Aset</div>
                <div class="account-list">
                    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="account-item parent">
                            <div class="account-info">
                                <span class="account-code"><?php echo e($asset['code']); ?></span>
                                <span class="account-name"><?php echo e($asset['name']); ?></span>
                            </div>
                            <div class="account-balance">
                                Rp <?php echo e(number_format($asset['balance'], 0, ',', '.')); ?>

                            </div>
                        </div>
                        
                        <?php if(!empty($asset['children'])): ?>
                            <?php $__currentLoopData = $asset['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="account-item child">
                                    <div class="account-info">
                                        <span class="account-code"><?php echo e($child['code']); ?></span>
                                        <span class="account-name"><?php echo e($child['name']); ?></span>
                                    </div>
                                    <div class="account-balance">
                                        Rp <?php echo e(number_format($child['balance'], 0, ',', '.')); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="total-row grand-total">
                    <span>Total Aset</span>
                    <span>Rp <?php echo e(number_format($totals['total_assets'], 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
        
        
        <div class="column">
            
            <div class="section">
                <div class="section-title liability">Kewajiban</div>
                <div class="account-list">
                    <?php $__currentLoopData = $liabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="account-item parent">
                            <div class="account-info">
                                <span class="account-code"><?php echo e($liability['code']); ?></span>
                                <span class="account-name"><?php echo e($liability['name']); ?></span>
                            </div>
                            <div class="account-balance">
                                Rp <?php echo e(number_format($liability['balance'], 0, ',', '.')); ?>

                            </div>
                        </div>
                        
                        <?php if(!empty($liability['children'])): ?>
                            <?php $__currentLoopData = $liability['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="account-item child">
                                    <div class="account-info">
                                        <span class="account-code"><?php echo e($child['code']); ?></span>
                                        <span class="account-name"><?php echo e($child['name']); ?></span>
                                    </div>
                                    <div class="account-balance">
                                        Rp <?php echo e(number_format($child['balance'], 0, ',', '.')); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="total-row">
                    <span>Total Kewajiban</span>
                    <span>Rp <?php echo e(number_format($totals['total_liabilities'], 0, ',', '.')); ?></span>
                </div>
            </div>
            
            
            <div class="section">
                <div class="section-title equity">Ekuitas</div>
                <div class="account-list">
                    <?php $__currentLoopData = $equity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="account-item parent">
                            <div class="account-info">
                                <span class="account-code"><?php echo e($eq['code']); ?></span>
                                <span class="account-name"><?php echo e($eq['name']); ?></span>
                            </div>
                            <div class="account-balance">
                                Rp <?php echo e(number_format($eq['balance'], 0, ',', '.')); ?>

                            </div>
                        </div>
                        
                        <?php if(!empty($eq['children'])): ?>
                            <?php $__currentLoopData = $eq['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="account-item child">
                                    <div class="account-info">
                                        <span class="account-code"><?php echo e($child['code']); ?></span>
                                        <span class="account-name"><?php echo e($child['name']); ?></span>
                                    </div>
                                    <div class="account-balance">
                                        Rp <?php echo e(number_format($child['balance'], 0, ',', '.')); ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    
                    <div class="account-item parent">
                        <div class="account-info">
                            <span class="account-code">-</span>
                            <span class="account-name">Laba Ditahan</span>
                        </div>
                        <div class="account-balance">
                            Rp <?php echo e(number_format($retained_earnings, 0, ',', '.')); ?>

                        </div>
                    </div>
                </div>
                
                <div class="total-row">
                    <span>Total Ekuitas</span>
                    <span>Rp <?php echo e(number_format($totals['total_equity'], 0, ',', '.')); ?></span>
                </div>
                
                <div class="total-row grand-total liability">
                    <span>Total Kewajiban & Ekuitas</span>
                    <span>Rp <?php echo e(number_format($totals['total_liabilities_and_equity'], 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($totals['is_balanced']): ?>
        <div class="balance-check balanced">
            <strong>✓ Neraca Seimbang (Balance)</strong><br>
            <span style="font-size: 10pt; font-weight: normal;">
                Total Aset = Total Kewajiban & Ekuitas<br>
                Rp <?php echo e(number_format($totals['total_assets'], 0, ',', '.')); ?>

            </span>
        </div>
    <?php else: ?>
        <div class="balance-check unbalanced">
            <strong>⚠ Neraca Tidak Seimbang</strong><br>
            <span style="font-size: 10pt; font-weight: normal;">
                Selisih: Rp <?php echo e(number_format(abs($totals['difference']), 0, ',', '.')); ?><br>
                Perlu dilakukan penyesuaian akuntansi
            </span>
        </div>
    <?php endif; ?>
    
    <div class="footer">
        <p><strong><?php echo e($companySettings['company_name'] ?? 'Nama Perusahaan'); ?></strong> - Neraca</p>
        <p>Dicetak pada: <?php echo e($print_date); ?></p>
        <p>Halaman ini merupakan dokumen resmi dan sah untuk keperluan akuntansi</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\neraca\pdf.blade.php ENDPATH**/ ?>