<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Produksi - <?php echo e($outlet->nama_outlet); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #888;
        }
        
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-item {
            text-align: center;
            flex: 1;
        }
        
        .summary-item h3 {
            margin: 0;
            font-size: 20px;
            color: #2563eb;
        }
        
        .summary-item p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        
        .filters {
            background: #e5e7eb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 11px;
        }
        
        .filters strong {
            color: #374151;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-approved { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #d97706; }
        .status-completed { background: #dcfce7; color: #16a34a; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        
        .priority {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .priority-normal { background: #f1f5f9; color: #64748b; }
        .priority-high { background: #fed7aa; color: #ea580c; }
        .priority-urgent { background: #fee2e2; color: #dc2626; }
        
        .progress-bar {
            width: 50px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #2563eb;
            transition: width 0.3s ease;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .materials-list {
            font-size: 9px;
            line-height: 1.3;
        }
        
        .materials-list div {
            margin-bottom: 2px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA PRODUKSI</h1>
        <h2><?php echo e($outlet->nama_outlet); ?></h2>
        <p>Dicetak pada: <?php echo e(date('d F Y H:i:s')); ?></p>
        <?php if($request->filled('start_date') || $request->filled('end_date')): ?>
            <p>
                Periode: 
                <?php echo e($request->start_date ? date('d F Y', strtotime($request->start_date)) : 'Awal'); ?> - 
                <?php echo e($request->end_date ? date('d F Y', strtotime($request->end_date)) : 'Akhir'); ?>

            </p>
        <?php endif; ?>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <h3><?php echo e($totalProductions); ?></h3>
            <p>Total Produksi</p>
        </div>
        <div class="summary-item">
            <h3><?php echo e(number_format($totalTarget)); ?></h3>
            <p>Total Target</p>
        </div>
        <div class="summary-item">
            <h3><?php echo e(number_format($totalRealized)); ?></h3>
            <p>Total Realisasi</p>
        </div>
        <div class="summary-item">
            <h3><?php echo e($totalTarget > 0 ? number_format(($totalRealized / $totalTarget) * 100, 1) : 0); ?>%</h3>
            <p>Rata-rata Progress</p>
        </div>
    </div>

    <!-- Applied Filters -->
    <?php if($request->filled('status') || $request->filled('production_line') || $request->filled('start_date') || $request->filled('end_date')): ?>
    <div class="filters">
        <strong>Filter yang Diterapkan:</strong>
        <?php if($request->filled('status') && $request->status !== 'all'): ?>
            Status: <?php echo e(ucfirst($request->status)); ?> |
        <?php endif; ?>
        <?php if($request->filled('production_line') && $request->production_line !== 'all'): ?>
            Lini: <?php echo e($request->production_line); ?> |
        <?php endif; ?>
        <?php if($request->filled('start_date')): ?>
            Dari: <?php echo e(date('d/m/Y', strtotime($request->start_date))); ?> |
        <?php endif; ?>
        <?php if($request->filled('end_date')): ?>
            Sampai: <?php echo e(date('d/m/Y', strtotime($request->end_date))); ?>

        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="7%">Kode</th>
                <th width="10%">Produk</th>
                <th width="6%">Lini</th>
                <th width="6%">Target</th>
                <th width="6%">Realisasi</th>
                <th width="5%">Progress</th>
                <th width="6%">Status</th>
                <th width="5%">Prioritas</th>
                <th width="12%">Material</th>
                <th width="7%">Total Material</th>
                <th width="7%">HPP/Unit</th>
                <th width="7%">Biaya Tenaga</th>
                <th width="7%">Biaya Operasional</th>
                <th width="9%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $productions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $production): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $realizedQty = $production->realizations->sum('quantity_produced');
                    $progress = $production->target_quantity > 0 ? ($realizedQty / $production->target_quantity) * 100 : 0;
                    
                    // Calculate material cost
                    $materialCost = 0;
                    foreach ($production->materials as $material) {
                        if ($material->material_type === 'bahan') {
                            $bahan = $material->material;
                            if ($bahan && $bahan->hargaBahan && $bahan->hargaBahan->isNotEmpty()) {
                                $hargaBahan = $bahan->hargaBahan->first();
                                $materialCost += $material->quantity_required * ($hargaBahan->harga_beli ?? 0);
                            }
                        } else {
                            $produk = $material->material;
                            if ($produk && method_exists($produk, 'calculateHpp')) {
                                $materialCost += $material->quantity_required * ($produk->calculateHpp() ?? 0);
                            }
                        }
                    }
                    
                    $laborCost = $production->laborCosts->sum('total_cost');
                    $operationalCost = $production->operationalCosts->sum('amount');
                    $totalCost = $materialCost + $laborCost + $operationalCost;
                    $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                ?>
                <tr>
                    <td><?php echo e($production->production_code); ?></td>
                    <td><?php echo e($production->product->nama_produk ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($production->production_line); ?></td>
                    <td class="text-right"><?php echo e(number_format($production->target_quantity)); ?></td>
                    <td class="text-right"><?php echo e(number_format($realizedQty)); ?></td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo e(min($progress, 100)); ?>%"></div>
                        </div>
                        <?php echo e(number_format($progress, 1)); ?>%
                    </td>
                    <td class="text-center">
                        <span class="status status-<?php echo e($production->status); ?>">
                            <?php echo e(ucfirst($production->status)); ?>

                        </span>
                    </td>
                    <td class="text-center">
                        <span class="priority priority-<?php echo e($production->priority ?? 'normal'); ?>">
                            <?php echo e(ucfirst($production->priority ?? 'Normal')); ?>

                        </span>
                    </td>
                    <td>
                        <div class="materials-list">
                            <?php $__currentLoopData = $production->materials->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <?php if($material->material_type === 'bahan'): ?>
                                        <?php echo e($material->material->nama_bahan ?? 'N/A'); ?>

                                    <?php else: ?>
                                        <?php echo e($material->material->nama_produk ?? 'N/A'); ?>

                                    <?php endif; ?>
                                    (<?php echo e(number_format($material->quantity_required, 2)); ?> <?php echo e($material->unit); ?>)
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($production->materials->count() > 3): ?>
                                <div><em>+<?php echo e($production->materials->count() - 3); ?> lainnya</em></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-right">
                        <?php echo e($materialCost > 0 ? 'Rp ' . number_format($materialCost) : '-'); ?>

                    </td>
                    <td class="text-right">
                        <?php echo e($hppPerUnit > 0 ? 'Rp ' . number_format($hppPerUnit) : '-'); ?>

                    </td>
                    <td class="text-right">
                        <?php echo e($laborCost > 0 ? 'Rp ' . number_format($laborCost) : '-'); ?>

                    </td>
                    <td class="text-right">
                        <?php echo e($operationalCost > 0 ? 'Rp ' . number_format($operationalCost) : '-'); ?>

                    </td>
                    <td class="text-center">
                        <?php echo e(date('d/m/Y', strtotime($production->start_date))); ?><br>
                        <small>s/d <?php echo e(date('d/m/Y', strtotime($production->end_date))); ?></small>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data produksi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Status Summary -->
    <?php if($statusCounts->count() > 0): ?>
    <div style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px;">Ringkasan Status:</h3>
        <div style="display: flex; gap: 20px;">
            <?php $__currentLoopData = $statusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="text-align: center;">
                    <div style="font-size: 16px; font-weight: bold; color: #2563eb;"><?php echo e($count); ?></div>
                    <div style="font-size: 11px; color: #666;"><?php echo e(ucfirst($status)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada <?php echo e(date('d F Y H:i:s')); ?></p>
        <p><?php echo e(config('app.name')); ?> - Sistem Manajemen Produksi</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\export-pdf.blade.php ENDPATH**/ ?>