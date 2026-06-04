<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Laporan Keuangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Laporan Keuangan']); ?>
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-1"></i>
                    Filter Laporan
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('travel.report.financial')); ?>" id="filterForm">
                    <div class="row">
                        <!-- Mode toggle -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tampilkan Per</label>
                                <select name="mode" class="form-control" id="modeSelect" onchange="toggleModeFields()">
                                    <option value="package" <?php echo e(($mode ?? 'package') === 'package' ? 'selected' : ''); ?>>Paket Perjalanan</option>
                                    <option value="keberangkatan" <?php echo e(($mode ?? '') === 'keberangkatan' ? 'selected' : ''); ?>>Keberangkatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo e($filters['start_date'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo e($filters['end_date'] ?? ''); ?>">
                            </div>
                        </div>
                        <!-- Per Paket: tipe paket -->
                        <div class="col-md-2" id="fieldPackageType" style="<?php echo e(($mode ?? 'package') === 'keberangkatan' ? 'display:none' : ''); ?>">
                            <div class="form-group">
                                <label>Tipe Paket</label>
                                <select name="package_type" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="hajj" <?php echo e(($filters['package_type'] ?? '') == 'hajj' ? 'selected' : ''); ?>>Hajj</option>
                                    <option value="umrah" <?php echo e(($filters['package_type'] ?? '') == 'umrah' ? 'selected' : ''); ?>>Umrah</option>
                                </select>
                            </div>
                        </div>
                        <!-- Per Keberangkatan: pilih keberangkatan spesifik -->
                        <div class="col-md-2" id="fieldKeberangkatan" style="<?php echo e(($mode ?? 'package') !== 'keberangkatan' ? 'display:none' : ''); ?>">
                            <div class="form-group">
                                <label>Keberangkatan</label>
                                <select name="id_keberangkatan" class="form-control">
                                    <option value="">Semua Keberangkatan</option>
                                    <?php $__currentLoopData = $allKeberangkatan ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($kb->id); ?>" <?php echo e(($filters['id_keberangkatan'] ?? '') == $kb->id ? 'selected' : ''); ?>>
                                            <?php echo e($kb->keberangkatan_code); ?> - <?php echo e($kb->keberangkatan_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Outlet</label>
                                <select name="id_outlet" class="form-control">
                                    <option value="">Semua Outlet</option>
                                    <?php $__currentLoopData = \App\Models\Outlet::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e(($filters['id_outlet'] ?? '') == $outlet->id_outlet ? 'selected' : ''); ?>>
                                            <?php echo e($outlet->nama_outlet); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Tampilkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>Rp <?php echo e(number_format($totals['total_revenue'], 0, ',', '.')); ?></h4>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Rp <?php echo e(number_format($totals['total_costs'], 0, ',', '.')); ?></h4>
                        <p>Total Costs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Rp <?php echo e(number_format($totals['total_profit'], 0, ',', '.')); ?></h4>
                        <p>Total Profit</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h4><?php echo e(number_format($totals['average_profit_margin'], 2)); ?>%</h4>
                        <p>Avg Profit Margin</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Data Laporan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-danger" onclick="exportPdf()">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="exportExcel()">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php if(($mode ?? 'package') === 'keberangkatan'): ?>
                    <?php $anyAdjusted = $reportData->where('laporan_disesuaikan', true)->count(); ?>
                    <?php if($anyAdjusted > 0): ?>
                    <div class="alert alert-info alert-sm mb-3 py-2">
                        <i class="bx bx-info-circle"></i>
                        <strong><?php echo e($anyAdjusted); ?> keberangkatan</strong> sudah disesuaikan laporan keuangannya (surplus/defisit diterapkan ke biaya).
                    </div>
                    <?php endif; ?>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Kode Keberangkatan</th>
                                <th>Nama Keberangkatan</th>
                                <th>Paket</th>
                                <th>Tipe</th>
                                <th>Tgl Berangkat</th>
                                <th>Jamaah</th>
                                <th class="text-right">HPP/Orang</th>
                                <th class="text-right">Add-on HPP</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">Costs</th>
                                <th class="text-right">Profit</th>
                                <th class="text-right">Margin</th>
                                <th class="text-right">RAB Realisasi</th>
                                <th class="text-right">RAB Hutang</th>
                                <th class="text-right">Surplus/Defisit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($data['keberangkatan_code']); ?></strong></td>
                                    <td><?php echo e($data['keberangkatan_name']); ?></td>
                                    <td><?php echo e($data['package_name']); ?></td>
                                    <td><span class="badge badge-<?php echo e($data['package_type'] == 'hajj' ? 'primary' : 'info'); ?>"><?php echo e(strtoupper($data['package_type'])); ?></span></td>
                                    <td><?php echo e($data['departure_date'] ? \Carbon\Carbon::parse($data['departure_date'])->format('d M Y') : '-'); ?></td>
                                    <td><?php echo e($data['jamaah_count']); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['hpp_per_person'], 0, ',', '.')); ?></td>
                                    <td class="text-right text-warning"><?php echo e(number_format($data['addon_hpp'] ?? 0, 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['revenue'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['costs'], 0, ',', '.')); ?></td>
                                    <td class="text-right <?php echo e($data['profit'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <strong><?php echo e(number_format($data['profit'], 0, ',', '.')); ?></strong>
                                    </td>
                                    <td class="text-right"><?php echo e(number_format($data['profit_margin'], 2)); ?>%</td>
                                    <td class="text-right text-success">
                                        <?php echo e(number_format($data['rab_realisasi'] ?? 0, 0, ',', '.')); ?>

                                        <?php if(($data['rab_realisasi'] ?? 0) > 0 && ($data['costs'] ?? 0) > 0): ?>
                                            <br><small class="text-muted"><?php echo e(number_format(min(100, ($data['rab_realisasi'] / $data['costs']) * 100), 1)); ?>%</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right <?php echo e(($data['rab_hutang'] ?? 0) > 0 ? 'text-danger' : ''); ?>">
                                        <?php echo e(($data['rab_hutang'] ?? 0) > 0 ? number_format($data['rab_hutang'], 0, ',', '.') : '-'); ?>

                                    </td>
                                    <td class="text-right <?php echo e(($data['surplus_defisit'] ?? 0) >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <strong>
                                            <?php echo e(($data['surplus_defisit'] ?? 0) >= 0 ? 'Surplus' : 'Defisit'); ?>:
                                            <?php echo e(number_format(abs($data['surplus_defisit'] ?? 0), 0, ',', '.')); ?>

                                        </strong>
                                        <?php if($data['laporan_disesuaikan'] ?? false): ?>
                                            <br><span class="badge badge-info" style="font-size:10px">✓ Disesuaikan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="15" class="text-center">Tidak ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="8">Total</td>
                                <td class="text-right"><?php echo e(number_format($totals['total_revenue'], 0, ',', '.')); ?></td>
                                <td class="text-right"><?php echo e(number_format($totals['total_costs'], 0, ',', '.')); ?></td>
                                <td class="text-right <?php echo e($totals['total_profit'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <strong><?php echo e(number_format($totals['total_profit'], 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-right"><?php echo e(number_format($totals['average_profit_margin'], 2)); ?>%</td>
                                <td class="text-right text-success"><?php echo e(number_format($reportData->sum('rab_realisasi'), 0, ',', '.')); ?></td>
                                <td class="text-right text-danger"><?php echo e(number_format($reportData->sum('rab_hutang'), 0, ',', '.')); ?></td>
                                <td class="text-right <?php echo e($reportData->sum('surplus_defisit') >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <strong><?php echo e($reportData->sum('surplus_defisit') >= 0 ? 'Surplus' : 'Defisit'); ?>: <?php echo e(number_format(abs($reportData->sum('surplus_defisit')), 0, ',', '.')); ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php else: ?>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Kode Paket</th>
                                <th>Nama Paket</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Jamaah</th>
                                <th class="text-right">HPP/Orang</th>
                                <th class="text-right">Harga/Orang</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">Costs</th>
                                <th class="text-right">Profit</th>
                                <th class="text-right">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($data['package_code']); ?></td>
                                    <td><?php echo e($data['package_name']); ?></td>
                                    <td><span class="badge badge-<?php echo e($data['package_type'] == 'hajj' ? 'primary' : 'info'); ?>"><?php echo e(strtoupper($data['package_type'])); ?></span></td>
                                    <td><?php echo e($data['departure_date'] ? \Carbon\Carbon::parse($data['departure_date'])->format('d M Y') : '-'); ?></td>
                                    <td><?php echo e($data['jamaah_count']); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['hpp_per_person'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['price_per_person'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['revenue'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['costs'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['profit'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['profit_margin'], 2)); ?>%</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="11" class="text-center">Tidak ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <div class="mt-3 text-muted">
                    <small><i class="fas fa-clock"></i> Laporan dibuat pada: <?php echo e(now()->format('d M Y H:i:s')); ?></small>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function toggleModeFields() {
        const mode = document.getElementById('modeSelect').value;
        document.getElementById('fieldPackageType').style.display = mode === 'keberangkatan' ? 'none' : '';
        document.getElementById('fieldKeberangkatan').style.display = mode === 'keberangkatan' ? '' : 'none';
    }

    function exportPdf() {
        const params = new URLSearchParams($('#filterForm').serialize());
        window.location.href = '<?php echo e(route("travel.report.financial.pdf")); ?>?' + params.toString();
    }

    function exportExcel() {
        const params = new URLSearchParams($('#filterForm').serialize());
        window.location.href = '<?php echo e(route("travel.report.financial.excel")); ?>?' + params.toString();
    }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\financial.blade.php ENDPATH**/ ?>