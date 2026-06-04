<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Produksi - <?php echo e($production->production_code); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 3px 0;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .hpp-summary {
            background-color: #f0f8ff;
            padding: 15px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }
        .hpp-summary h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .hpp-grid {
            display: table;
            width: 100%;
        }
        .hpp-row {
            display: table-row;
        }
        .hpp-label {
            display: table-cell;
            width: 200px;
            padding: 5px 0;
        }
        .hpp-value {
            display: table-cell;
            text-align: right;
            padding: 5px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL PRODUKSI</h1>
        <h2><?php echo e($production->production_code); ?></h2>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Kode Produksi:</div>
                <div class="info-value"><?php echo e($production->production_code); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Produk:</div>
                <div class="info-value">
                    <?php if($production->hppRecords && $production->hppRecords->count() > 1): ?>
                        Multi-Produk (<?php echo e($production->hppRecords->count()); ?> produk)
                    <?php elseif($production->hppRecords && $production->hppRecords->count() == 1): ?>
                        <?php echo e($production->hppRecords->first()->product->nama_produk ?? 'Produk tidak ditemukan'); ?>

                    <?php else: ?>
                        Produk tidak ditemukan
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Outlet:</div>
                <div class="info-value"><?php echo e($production->outlet->nama_outlet ?? '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lini Produksi:</div>
                <div class="info-value"><?php echo e($production->production_line); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Target Produksi:</div>
                <div class="info-value"><?php echo e(number_format($production->target_quantity, 0, ',', '.')); ?> unit</div>
            </div>
            <div class="info-row">
                <div class="info-label">Realisasi Produksi:</div>
                <div class="info-value"><?php echo e(number_format($production->realizations->sum('quantity_produced'), 0, ',', '.')); ?> unit</div>
            </div>
            <div class="info-row">
                <div class="info-label">Qty Reject:</div>
                <div class="info-value" style="color: #dc2626;"><?php echo e(number_format($production->realizations->sum('quantity_rejected'), 0, ',', '.')); ?> unit</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Diproduksi:</div>
                <div class="info-value"><?php echo e(number_format($production->realizations->sum('quantity_produced') + $production->realizations->sum('quantity_rejected'), 0, ',', '.')); ?> unit</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Mulai:</div>
                <div class="info-value"><?php echo e($production->start_date ? \Carbon\Carbon::parse($production->start_date)->format('d/m/Y') : '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Selesai:</div>
                <div class="info-value"><?php echo e($production->end_date ? \Carbon\Carbon::parse($production->end_date)->format('d/m/Y') : '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Kadaluarsa:</div>
                <div class="info-value"><?php echo e($production->expiry_date ? \Carbon\Carbon::parse($production->expiry_date)->format('d/m/Y') : '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value"><?php echo e(ucfirst($production->status)); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Prioritas:</div>
                <div class="info-value"><?php echo e(ucfirst($production->priority)); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lokasi Gudang:</div>
                <div class="info-value"><?php echo e($production->warehouse_location ?? '-'); ?></div>
            </div>
        </div>
    </div>

    <?php if($production->hppRecords && $production->hppRecords->count() > 1): ?>
    <div class="section-title">DETAIL PRODUK</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kode Produk</th>
                <th class="text-right">Target</th>
                <th class="text-right">Realisasi</th>
                <th class="text-right">Reject</th>
                <th class="text-right">Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $production->hppRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hpp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $progress = $hpp->target_quantity > 0 ? ($hpp->realized_quantity / $hpp->target_quantity) * 100 : 0;
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($hpp->product->nama_produk ?? 'Unknown Product'); ?></td>
                    <td class="text-center"><?php echo e($hpp->product->kode_produk ?? '-'); ?></td>
                    <td class="text-right"><?php echo e(number_format($hpp->target_quantity ?? 0, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($hpp->realized_quantity ?? 0, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($hpp->rejected_quantity ?? 0, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($progress, 1)); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL:</td>
                <td class="text-right"><?php echo e(number_format($production->hppRecords->sum('target_quantity'), 0, ',', '.')); ?></td>
                <td class="text-right"><?php echo e(number_format($production->hppRecords->sum('realized_quantity'), 0, ',', '.')); ?></td>
                <td class="text-right"><?php echo e(number_format($production->hppRecords->sum('rejected_quantity'), 0, ',', '.')); ?></td>
                <td class="text-right">
                    <?php
                        $totalTarget = $production->hppRecords->sum('target_quantity');
                        $totalRealized = $production->hppRecords->sum('realized_quantity');
                        $overallProgress = $totalTarget > 0 ? ($totalRealized / $totalTarget) * 100 : 0;
                    ?>
                    <?php echo e(number_format($overallProgress, 1)); ?>%
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="section-title">KEBUTUHAN MATERIAL</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Material</th>
                <th>Jenis</th>
                <th class="text-right">Jumlah</th>
                <th>Satuan</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php $materialTotal = 0; ?>
            <?php $__currentLoopData = $materialsWithFifoPrice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $materialData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $material = $materialData['material'];
                    $materialName = $materialData['name'];
                    $unitPrice = $materialData['fifo_price']; // Use FIFO price calculated in controller
                    
                    $totalPrice = $material->quantity_required * $unitPrice;
                    $materialTotal += $totalPrice;
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($materialName); ?></td>
                    <td class="text-center"><?php echo e(ucfirst($material->material_type)); ?></td>
                    <td class="text-right"><?php echo e(number_format($material->quantity_required, 2, ',', '.')); ?></td>
                    <td class="text-center"><?php echo e($material->unit); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($unitPrice, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($totalPrice, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL BIAYA MATERIAL:</td>
                <td class="text-right">Rp <?php echo e(number_format($materialTotal, 0, ',', '.')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">BIAYA TENAGA KERJA</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jumlah Pekerja</th>
                <th class="text-right">Biaya per Pekerja</th>
                <th class="text-right">Total Biaya</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $laborTotal = 0; ?>
            <?php $__currentLoopData = $production->laborCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $labor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $totalLaborCost = $labor->worker_count * $labor->cost_per_worker;
                    $laborTotal += $totalLaborCost;
                ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td class="text-center"><?php echo e($labor->worker_count); ?> orang</td>
                    <td class="text-right">Rp <?php echo e(number_format($labor->cost_per_worker, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($totalLaborCost, 0, ',', '.')); ?></td>
                    <td><?php echo e($labor->notes ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($production->laborCosts->isEmpty()): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data biaya tenaga kerja</td>
                </tr>
            <?php else: ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL BIAYA TENAGA KERJA:</td>
                    <td class="text-right">Rp <?php echo e(number_format($laborTotal, 0, ',', '.')); ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">BIAYA OPERASIONAL</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Biaya</th>
                <th class="text-right">Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $operationalTotal = 0; ?>
            <?php $__currentLoopData = $production->operationalCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $operationalTotal += $cost->amount; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e(ucfirst($cost->cost_type)); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($cost->amount, 0, ',', '.')); ?></td>
                    <td><?php echo e($cost->description ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($production->operationalCosts->isEmpty()): ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data biaya operasional</td>
                </tr>
            <?php else: ?>
                <tr class="total-row">
                    <td colspan="2" class="text-right">TOTAL BIAYA OPERASIONAL:</td>
                    <td class="text-right">Rp <?php echo e(number_format($operationalTotal, 0, ',', '.')); ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="hpp-summary">
        <h3>PERHITUNGAN HARGA POKOK PRODUKSI (HPP)</h3>
        <div class="hpp-grid">
            <div class="hpp-row">
                <div class="hpp-label">Biaya Material:</div>
                <div class="hpp-value">Rp <?php echo e(number_format($hppCalculation['material_cost'], 0, ',', '.')); ?></div>
            </div>
            <div class="hpp-row">
                <div class="hpp-label">Biaya Tenaga Kerja:</div>
                <div class="hpp-value">Rp <?php echo e(number_format($hppCalculation['labor_cost'], 0, ',', '.')); ?></div>
            </div>
            <div class="hpp-row">
                <div class="hpp-label">Biaya Operasional:</div>
                <div class="hpp-value">Rp <?php echo e(number_format($hppCalculation['operational_cost'], 0, ',', '.')); ?></div>
            </div>
            <div class="hpp-row" style="border-top: 1px solid #333; margin-top: 10px; padding-top: 10px;">
                <div class="hpp-label"><strong>TOTAL HPP:</strong></div>
                <div class="hpp-value" style="font-size: 14px;">Rp <?php echo e(number_format($hppCalculation['total_cost'], 0, ',', '.')); ?></div>
            </div>
            <div class="hpp-row">
                <div class="hpp-label"><strong>HPP per Unit:</strong></div>
                <div class="hpp-value" style="font-size: 14px;">Rp <?php echo e(number_format($hppCalculation['hpp_per_unit'], 0, ',', '.')); ?></div>
            </div>
            <div class="hpp-row">
                <div class="hpp-label">Target Produksi:</div>
                <div class="hpp-value"><?php echo e(number_format($production->target_quantity, 0, ',', '.')); ?> unit</div>
            </div>
        </div>
    </div>

    <?php if($production->notes): ?>
    <div class="section-title">CATATAN</div>
    <p><?php echo e($production->notes); ?></p>
    <?php endif; ?>

    <div class="footer">
        <p>Laporan dibuat pada: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?></p>
        <p><?php echo e(config('app.name')); ?> - Sistem Manajemen Produksi</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\pdf.blade.php ENDPATH**/ ?>