<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas</title>
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
        
        .report-period {
            font-size: 11pt;
            color: #475569;
            font-weight: 500;
        }

        .report-method {
            font-size: 10pt;
            color: #6b7280;
            font-style: italic;
            margin-top: 3px;
        }
        
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
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .cashflow-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .cashflow-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .cashflow-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .cashflow-table tr:hover {
            background-color: #f3f4f6;
        }
        
        .cashflow-table .item-name {
            width: 70%;
            font-weight: 500;
            color: #374151;
        }
        
        .cashflow-table .amount {
            width: 30%;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .cashflow-table .subtotal {
            font-weight: bold !important;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            border-top: 2px solid #94a3b8 !important;
            font-size: 11pt;
        }
        
        .cashflow-table .total {
            font-weight: bold !important;
            font-size: 12pt !important;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
            border-top: 3px solid #1e40af !important;
            border-bottom: 3px solid #1e40af !important;
            color: #1e40af !important;
        }
        
        .positive {
            color: #059669;
        }
        
        .negative {
            color: #dc2626;
        }
        
        .summary-box {
            margin-top: 25px;
            padding: 15px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 8px 12px;
            border-bottom: 1px solid #e0f2fe;
        }

        .summary-box tr:last-child td {
            border-bottom: none;
            border-top: 2px solid #0ea5e9;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .summary-box .label {
            font-weight: 600;
            width: 70%;
            color: #0c4a6e;
        }
        
        .summary-box .value {
            text-align: right;
            font-family: 'Courier New', monospace;
            width: 30%;
            font-weight: 600;
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
            
            .page-break {
                page-break-after: always;
            }

            .cashflow-table tr:hover {
                background-color: transparent !important;
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
            <div class="company-name"><?php echo e($companySettings['company_name'] ?? $companyName ?? 'Nama Perusahaan'); ?></div>
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
        <div class="report-title">Laporan Arus Kas</div>
        <div class="report-period">
            Periode: <?php echo e($startDate); ?> s/d <?php echo e($endDate); ?>

        </div>
        <div class="report-method">
            Metode: <?php echo e($method === 'direct' ? 'Langsung (Direct)' : 'Tidak Langsung (Indirect)'); ?>

        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Outlet:</div>
            <div class="info-value"><?php echo e($outletName ?? 'Semua Outlet'); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Buku Akuntansi:</div>
            <div class="info-value"><?php echo e($bookName ?? 'Semua Buku'); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value"><?php echo e(now()->format('d/m/Y H:i')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Mata Uang:</div>
            <div class="info-value">Rupiah (IDR)</div>
        </div>
    </div>

    
    <div class="section">
        <div class="section-title">AKTIVITAS OPERASI</div>
        <table class="cashflow-table">
            <?php $__currentLoopData = $operatingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="item-name"><?php echo e($item['name']); ?></td>
                <td class="amount <?php echo e($item['amount'] >= 0 ? 'positive' : 'negative'); ?>">
                    <?php echo e(number_format(abs($item['amount']), 0, ',', '.')); ?>

                    <?php if($item['amount'] < 0): ?> (<?php echo e(number_format(abs($item['amount']), 0, ',', '.')); ?>) <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Operasi</td>
                <td class="amount <?php echo e($netOperating >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($netOperating < 0): ?> ( <?php endif; ?>
                    <?php echo e(number_format(abs($netOperating), 0, ',', '.')); ?>

                    <?php if($netOperating < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="section">
        <div class="section-title">AKTIVITAS INVESTASI</div>
        <table class="cashflow-table">
            <?php $__currentLoopData = $investingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="item-name"><?php echo e($item['name']); ?></td>
                <td class="amount <?php echo e($item['amount'] >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($item['amount'] < 0): ?> ( <?php endif; ?>
                    <?php echo e(number_format(abs($item['amount']), 0, ',', '.')); ?>

                    <?php if($item['amount'] < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Investasi</td>
                <td class="amount <?php echo e($netInvesting >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($netInvesting < 0): ?> ( <?php endif; ?>
                    <?php echo e(number_format(abs($netInvesting), 0, ',', '.')); ?>

                    <?php if($netInvesting < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="section">
        <div class="section-title">AKTIVITAS PENDANAAN</div>
        <table class="cashflow-table">
            <?php $__currentLoopData = $financingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="item-name"><?php echo e($item['name']); ?></td>
                <td class="amount <?php echo e($item['amount'] >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($item['amount'] < 0): ?> ( <?php endif; ?>
                    <?php echo e(number_format(abs($item['amount']), 0, ',', '.')); ?>

                    <?php if($item['amount'] < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="subtotal">
                <td class="item-name">Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="amount <?php echo e($netFinancing >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($netFinancing < 0): ?> ( <?php endif; ?>
                    <?php echo e(number_format(abs($netFinancing), 0, ',', '.')); ?>

                    <?php if($netFinancing < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Kenaikan (Penurunan) Kas Bersih</td>
                <td class="value <?php echo e($netCashFlow >= 0 ? 'positive' : 'negative'); ?>">
                    <?php if($netCashFlow < 0): ?> ( <?php endif; ?>
                    Rp <?php echo e(number_format(abs($netCashFlow), 0, ',', '.')); ?>

                    <?php if($netCashFlow < 0): ?> ) <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Kas Awal Periode</td>
                <td class="value">Rp <?php echo e(number_format($beginningCash, 0, ',', '.')); ?></td>
            </tr>
            <tr style="border-top: 2px solid #2563eb;">
                <td class="label" style="font-size: 11pt;">Kas Akhir Periode</td>
                <td class="value" style="font-size: 11pt; font-weight: bold;">
                    Rp <?php echo e(number_format($endingCash, 0, ',', '.')); ?>

                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak oleh: <?php echo e(auth()->user()->name ?? 'System'); ?> | 
        <?php echo e(config('app.name')); ?> - Laporan Arus Kas
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\finance\cashflow\pdf.blade.php ENDPATH**/ ?>