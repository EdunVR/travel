<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
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
            margin: 20mm 15mm 20mm 15mm;
        }

        .container {
            width: 100%;
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
        
        .report-period {
            font-size: 11pt;
            color: #475569;
            font-weight: 500;
        }

        .comparison-period {
            font-size: 10pt;
            color: #6b7280;
            font-style: italic;
            margin-top: 3px;
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
            width: 150px;
            padding: 4px 0;
            color: #374151;
        }

        .info-value {
            display: table-cell;
            padding: 4px 0;
            color: #1f2937;
        }

        /* Enhanced Table Styles */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .main-table th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .main-table th:last-child {
            border-right: none;
        }

        .main-table th.text-right {
            text-align: right;
        }

        .main-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 9pt;
            vertical-align: middle;
        }

        .main-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .main-table tbody tr:hover {
            background-color: #f3f4f6;
        }

        .main-table td.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }

        .main-table td.account-code {
            width: 100px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1e40af;
            background: #eff6ff;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 8pt;
        }

        .main-table td.account-name {
            width: auto;
            font-weight: 500;
            color: #374151;
        }

        /* Section Headers */
        .section-header {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%) !important;
            color: white !important;
            font-weight: bold !important;
            font-size: 11pt !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-header td {
            padding: 12px 10px !important;
            border: none !important;
        }

        /* Total Rows */
        .total-row {
            font-weight: bold !important;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            border-top: 2px solid #94a3b8 !important;
        }

        .total-row td {
            padding: 10px !important;
            font-size: 10pt !important;
        }

        .grand-total-row {
            font-weight: bold !important;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
            border-top: 3px double #1e40af !important;
            border-bottom: 3px double #1e40af !important;
            font-size: 11pt !important;
        }

        .grand-total-row td {
            padding: 12px 10px !important;
            color: #1e40af !important;
        }

        /* Profit/Loss Specific Styling */
        .profit-row {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
        }

        .profit-row td {
            color: #065f46 !important;
        }

        .loss-row {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
        }

        .loss-row td {
            color: #991b1b !important;
        }

        /* Child Account Indentation */
        .child-account {
            padding-left: 25px !important;
            font-size: 8pt;
            color: #6b7280;
        }

        .grandchild-account {
            padding-left: 45px !important;
            font-size: 8pt;
            color: #9ca3af;
        }

        /* Enhanced Ratios Section */
        .ratios-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .ratios-left {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }

        .ratios-right {
            display: table-cell;
            width: 50%;
            padding-left: 15px;
            vertical-align: top;
        }

        .ratio-box {
            padding: 15px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .ratio-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 12px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }

        .ratio-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .ratio-item:last-child {
            border-bottom: none;
        }

        .ratio-label {
            font-weight: 600;
            color: #475569;
        }

        .ratio-value {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1e40af;
        }

        /* Footer */
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

        /* Comparison Columns */
        .comparison-table th,
        .comparison-table td {
            width: 16.66%;
        }

        .comparison-table td.account-name {
            width: auto;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Positive/Negative Indicators */
        .positive {
            color: #059669;
        }

        .negative {
            color: #dc2626;
        }

        @media print {
            body {
                margin: 10mm 8mm 10mm 8mm;
            }
            
            .main-table tbody tr:hover {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Professional Letterhead -->
        <div class="letterhead">
            <?php if(isset($companySettings['logo_url']) && $companySettings['logo_url']): ?>
            <div class="logo-section">
                <img src="<?php echo e($companySettings['logo_url']); ?>" alt="Company Logo">
            </div>
            <?php endif; ?>
            <div class="company-info">
                <div class="company-name"><?php echo e($companySettings['company_name'] ?? $filters['company_name'] ?? 'Nama Perusahaan'); ?></div>
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
            <div class="report-title">Laporan Laba Rugi</div>
            <div class="report-outlet"><?php echo e($filters['outlet_name'] ?? '-'); ?></div>
            <div class="report-period">
                Periode: <?php echo e(\Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y')); ?> 
                s/d <?php echo e(\Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y')); ?>

            </div>
            <?php if($filters['comparison_enabled'] ?? false): ?>
            <div class="comparison-period">
                Pembanding: <?php echo e(\Carbon\Carbon::parse($filters['comparison_start_date'])->translatedFormat('d F Y')); ?> 
                s/d <?php echo e(\Carbon\Carbon::parse($filters['comparison_end_date'])->translatedFormat('d F Y')); ?>

            </div>
            <?php endif; ?>
        </div>

        <!-- Report Information -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Tanggal Cetak:</div>
                <div class="info-value"><?php echo e(now()->translatedFormat('d F Y H:i:s')); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Mata Uang:</div>
                <div class="info-value">Rupiah (IDR)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Metode:</div>
                <div class="info-value"><?php echo e(($filters['comparison_enabled'] ?? false) ? 'Dengan Perbandingan' : 'Standar'); ?></div>
            </div>
        </div>

        <!-- Profit & Loss Statement Table -->
        <table class="main-table <?php echo e(($filters['comparison_enabled'] ?? false) ? 'comparison-table' : ''); ?>">
            <thead>
                <tr>
                    <th class="account-code">Kode Akun</th>
                    <th class="account-name">Nama Akun</th>
                    <th class="text-right">Jumlah</th>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <th class="text-right">Pembanding</th>
                    <th class="text-right">Selisih</th>
                    <th class="text-right">%</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <!-- PENDAPATAN -->
                <tr class="section-header">
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>">PENDAPATAN</td>
                </tr>
                <?php $__currentLoopData = $data['revenue']['accounts'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="account-code"><?php echo e($account['code']); ?></td>
                        <td class="account-name"><?php echo e($account['name']); ?></td>
                        <td class="amount"><?php echo e(number_format($account['amount'], 2, ',', '.')); ?></td>
                        <?php if($filters['comparison_enabled'] ?? false): ?>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php if(!empty($account['children'])): ?>
                        <?php $__currentLoopData = $account['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="account-code"><?php echo e($child['code']); ?></td>
                            <td class="account-name child-account"><?php echo e($child['name']); ?></td>
                            <td class="amount"><?php echo e(number_format($child['amount'], 2, ',', '.')); ?></td>
                            <?php if($filters['comparison_enabled'] ?? false): ?>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Pendapatan</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['revenue']['total'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>

                <!-- PENDAPATAN LAIN-LAIN -->
                <?php if(!empty($data['other_revenue']['accounts'])): ?>
                <tr class="section-header">
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>">PENDAPATAN LAIN-LAIN</td>
                </tr>
                <?php $__currentLoopData = $data['other_revenue']['accounts'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="account-code"><?php echo e($account['code']); ?></td>
                        <td class="account-name"><?php echo e($account['name']); ?></td>
                        <td class="amount"><?php echo e(number_format($account['amount'], 2, ',', '.')); ?></td>
                        <?php if($filters['comparison_enabled'] ?? false): ?>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php if(!empty($account['children'])): ?>
                        <?php $__currentLoopData = $account['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="account-code"><?php echo e($child['code']); ?></td>
                            <td class="account-name child-account"><?php echo e($child['name']); ?></td>
                            <td class="amount"><?php echo e(number_format($child['amount'], 2, ',', '.')); ?></td>
                            <?php if($filters['comparison_enabled'] ?? false): ?>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Pendapatan Lain-Lain</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['other_revenue']['total'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>

                <!-- TOTAL PENDAPATAN -->
                <tr class="grand-total-row">
                    <td></td>
                    <td><strong>TOTAL PENDAPATAN</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['summary']['total_revenue'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>

                <!-- Empty Row -->
                <tr>
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>" style="border: none; height: 10px; background: transparent;"></td>
                </tr>

                <!-- BEBAN OPERASIONAL -->
                <tr class="section-header">
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>">BEBAN OPERASIONAL</td>
                </tr>
                <?php $__currentLoopData = $data['expense']['accounts'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="account-code"><?php echo e($account['code']); ?></td>
                        <td class="account-name"><?php echo e($account['name']); ?></td>
                        <td class="amount"><?php echo e(number_format($account['amount'], 2, ',', '.')); ?></td>
                        <?php if($filters['comparison_enabled'] ?? false): ?>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php if(!empty($account['children'])): ?>
                        <?php $__currentLoopData = $account['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="account-code"><?php echo e($child['code']); ?></td>
                            <td class="account-name child-account"><?php echo e($child['name']); ?></td>
                            <td class="amount"><?php echo e(number_format($child['amount'], 2, ',', '.')); ?></td>
                            <?php if($filters['comparison_enabled'] ?? false): ?>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Beban Operasional</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['expense']['total'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>

                <!-- BEBAN LAIN-LAIN -->
                <?php if(!empty($data['other_expense']['accounts'])): ?>
                <tr class="section-header">
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>">BEBAN LAIN-LAIN</td>
                </tr>
                <?php $__currentLoopData = $data['other_expense']['accounts'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="account-code"><?php echo e($account['code']); ?></td>
                        <td class="account-name"><?php echo e($account['name']); ?></td>
                        <td class="amount"><?php echo e(number_format($account['amount'], 2, ',', '.')); ?></td>
                        <?php if($filters['comparison_enabled'] ?? false): ?>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <td class="amount">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php if(!empty($account['children'])): ?>
                        <?php $__currentLoopData = $account['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="account-code"><?php echo e($child['code']); ?></td>
                            <td class="account-name child-account"><?php echo e($child['name']); ?></td>
                            <td class="amount"><?php echo e(number_format($child['amount'], 2, ',', '.')); ?></td>
                            <?php if($filters['comparison_enabled'] ?? false): ?>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <td class="amount">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td></td>
                    <td><strong>Total Beban Lain-Lain</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['other_expense']['total'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>

                <!-- TOTAL BEBAN -->
                <tr class="grand-total-row">
                    <td></td>
                    <td><strong>TOTAL BEBAN</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['summary']['total_expense'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>

                <!-- Empty Row -->
                <tr>
                    <td colspan="<?php echo e(($filters['comparison_enabled'] ?? false) ? 6 : 3); ?>" style="border: none; height: 10px; background: transparent;"></td>
                </tr>

                <!-- LABA/RUGI BERSIH -->
                <tr class="grand-total-row <?php echo e(($data['summary']['net_income'] ?? 0) >= 0 ? 'profit-row' : 'loss-row'); ?>">
                    <td></td>
                    <td><strong>LABA/RUGI BERSIH</strong></td>
                    <td class="amount"><strong><?php echo e(number_format($data['summary']['net_income'] ?? 0, 2, ',', '.')); ?></strong></td>
                    <?php if($filters['comparison_enabled'] ?? false): ?>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <td class="amount">-</td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>

        <!-- Enhanced Financial Ratios Section -->
        <div class="ratios-section">
            <div class="ratios-left">
                <div class="ratio-box">
                    <div class="ratio-title">Rasio Profitabilitas</div>
                    <div class="ratio-item">
                        <span class="ratio-label">Gross Profit Margin:</span>
                        <span class="ratio-value"><?php echo e($data['summary']['gross_profit_margin'] ?? 'N/A'); ?><?php echo e(is_numeric($data['summary']['gross_profit_margin'] ?? null) ? '%' : ''); ?></span>
                    </div>
                    <div class="ratio-item">
                        <span class="ratio-label">Net Profit Margin:</span>
                        <span class="ratio-value"><?php echo e($data['summary']['net_profit_margin'] ?? 'N/A'); ?><?php echo e(is_numeric($data['summary']['net_profit_margin'] ?? null) ? '%' : ''); ?></span>
                    </div>
                    <div class="ratio-item">
                        <span class="ratio-label">Operating Expense Ratio:</span>
                        <span class="ratio-value"><?php echo e($data['summary']['operating_expense_ratio'] ?? 'N/A'); ?><?php echo e(is_numeric($data['summary']['operating_expense_ratio'] ?? null) ? '%' : ''); ?></span>
                    </div>
                </div>
            </div>
            <div class="ratios-right">
                <div class="ratio-box">
                    <div class="ratio-title">Ringkasan Keuangan</div>
                    <div class="ratio-item">
                        <span class="ratio-label">Total Pendapatan:</span>
                        <span class="ratio-value">Rp <?php echo e(number_format($data['summary']['total_revenue'] ?? 0, 0, ',', '.')); ?></span>
                    </div>
                    <div class="ratio-item">
                        <span class="ratio-label">Total Beban:</span>
                        <span class="ratio-value">Rp <?php echo e(number_format($data['summary']['total_expense'] ?? 0, 0, ',', '.')); ?></span>
                    </div>
                    <div class="ratio-item">
                        <span class="ratio-label">Laba/Rugi Bersih:</span>
                        <span class="ratio-value <?php echo e(($data['summary']['net_income'] ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                            Rp <?php echo e(number_format($data['summary']['net_income'] ?? 0, 0, ',', '.')); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong><?php echo e($companySettings['company_name'] ?? $filters['company_name'] ?? 'Nama Perusahaan'); ?></strong> - Laporan Laba Rugi</p>
            <p>Dicetak pada: <?php echo e(now()->translatedFormat('d F Y H:i:s')); ?></p>
            <p>Halaman ini merupakan dokumen resmi dan sah untuk keperluan akuntansi</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\labarugi\pdf.blade.php ENDPATH**/ ?>