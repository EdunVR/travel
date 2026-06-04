<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Packing List - <?php echo e($keberangkatan->keberangkatan_code); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .category-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .category-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-not_ordered {
            background-color: #e0e0e0;
            color: #666;
        }
        .status-ordered {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-received {
            background-color: #e8eaf6;
            color: #3f51b5;
        }
        .status-packed {
            background-color: #fff9c4;
            color: #f57f17;
        }
        .status-shipped {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .deadline-warning {
            color: #d32f2f;
            font-weight: bold;
        }
        .deadline-approaching {
            color: #f57c00;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PACKING LIST</h1>
        <p><?php echo e($keberangkatan->keberangkatan_name); ?></p>
        <p><?php echo e($keberangkatan->keberangkatan_code); ?></p>
    </div>

    <!-- Keberangkatan Info -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Paket Perjalanan:</div>
            <div class="info-value"><?php echo e($keberangkatan->travelPackage->package_name ?? '-'); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Keberangkatan:</div>
            <div class="info-value"><?php echo e($keberangkatan->departure_date->format('d F Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Kepulangan:</div>
            <div class="info-value"><?php echo e($keberangkatan->return_date->format('d F Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Jamaah:</div>
            <div class="info-value"><?php echo e($keberangkatan->getConfirmedJamaahCount()); ?> orang</div>
        </div>
        <div class="info-row">
            <div class="info-label">Dibuat:</div>
            <div class="info-value"><?php echo e($generatedAt->format('d F Y H:i')); ?></div>
        </div>
    </div>

    <!-- Equipment by Category -->
    <?php $__currentLoopData = $groupedEquipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="category-section">
        <div class="category-title"><?php echo e($category ?: 'Tanpa Kategori'); ?></div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Nama Perlengkapan</th>
                    <th style="width: 10%;" class="text-center">Jml Dibutuhkan</th>
                    <th style="width: 10%;" class="text-center">Jml Diterima</th>
                    <th style="width: 12%;" class="text-center">Status</th>
                    <th style="width: 18%;">Supplier</th>
                    <th style="width: 15%;" class="text-center">Tenggat</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->equipment_name); ?></td>
                    <td class="text-center"><?php echo e($item->quantity_needed); ?></td>
                    <td class="text-center"><?php echo e($item->quantity_received); ?></td>
                    <td class="text-center">
                        <span class="status-badge status-<?php echo e($item->status); ?>">
                            <?php echo e($item->getStatusLabel()); ?>

                        </span>
                    </td>
                    <td><?php echo e($item->supplier_name ?? '-'); ?></td>
                    <td class="text-center">
                        <?php if($item->shipping_deadline): ?>
                            <span class="<?php echo e($item->isDeadlineOverdue() ? 'deadline-warning' : ($item->isDeadlineApproaching() ? 'deadline-approaching' : '')); ?>">
                                <?php echo e($item->shipping_deadline->format('d M Y')); ?>

                                <?php if($item->isDeadlineOverdue()): ?>
                                    (TERLAMBAT!)
                                <?php elseif($item->isDeadlineApproaching()): ?>
                                    (SEGERA!)
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if($item->notes): ?>
                <tr>
                    <td></td>
                    <td colspan="6" style="font-size: 10px; color: #666; font-style: italic;">
                        Catatan: <?php echo e($item->notes); ?>

                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Summary -->
    <div class="summary-section">
        <strong>Ringkasan:</strong>
        <div class="summary-row">
            <span>Total Item Perlengkapan:</span>
            <span><?php echo e($groupedEquipment->flatten()->count()); ?></span>
        </div>
        <div class="summary-row">
            <span>Belum Dipesan:</span>
            <span><?php echo e($groupedEquipment->flatten()->where('status', 'not_ordered')->count()); ?></span>
        </div>
        <div class="summary-row">
            <span>Dipesan:</span>
            <span><?php echo e($groupedEquipment->flatten()->where('status', 'ordered')->count()); ?></span>
        </div>
        <div class="summary-row">
            <span>Diterima:</span>
            <span><?php echo e($groupedEquipment->flatten()->where('status', 'received')->count()); ?></span>
        </div>
        <div class="summary-row">
            <span>Dikemas:</span>
            <span><?php echo e($groupedEquipment->flatten()->where('status', 'packed')->count()); ?></span>
        </div>
        <div class="summary-row">
            <span>Dikirim:</span>
            <span><?php echo e($groupedEquipment->flatten()->where('status', 'shipped')->count()); ?></span>
        </div>
    </div>

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <p>Disiapkan Oleh</p>
                <div class="signature-line">
                    <p>Tim Logistik</p>
                </div>
            </div>
            <div class="signature-box">
                <p>Disetujui Oleh</p>
                <div class="signature-line">
                    <p>Manajer Operasional</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\logistics\packing-list-pdf.blade.php ENDPATH**/ ?>