<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Laporan Ringkasan Keberangkatan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Laporan Ringkasan Keberangkatan']); ?>
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
                <form method="GET" action="<?php echo e(route('travel.report.departure-summary')); ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo e($filters['start_date'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo e($filters['end_date'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tampilkan
                                    </button>
                                    <a href="<?php echo e(route('travel.report.departure-summary')); ?>" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Keberangkatan</th>
                                <th>Tanggal Berangkat</th>
                                <th>Jumlah Jamaah</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">Expenses</th>
                                <th class="text-right">Profit</th>
                                <th class="text-right">Profit Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalJamaah = 0;
                                $totalRevenue = 0;
                                $totalExpenses = 0;
                                $totalProfit = 0;
                            ?>
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $totalJamaah += $data['jamaah_count'];
                                    $totalRevenue += $data['revenue'];
                                    $totalExpenses += $data['expenses'];
                                    $totalProfit += $data['profit'];
                                ?>
                                <tr>
                                    <td><?php echo e($data['keberangkatan_code']); ?></td>
                                    <td><?php echo e($data['keberangkatan_name']); ?></td>
                                    <td><?php echo e($data['departure_date']->format('d M Y')); ?></td>
                                    <td><?php echo e($data['jamaah_count']); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($data['revenue'], 0, ',', '.')); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($data['expenses'], 0, ',', '.')); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($data['profit'], 0, ',', '.')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($data['profit_margin'], 2)); ?>%</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($reportData->count() > 0): ?>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="3">TOTAL</td>
                                <td><?php echo e($totalJamaah); ?></td>
                                <td class="text-right">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></td>
                                <td class="text-right">Rp <?php echo e(number_format($totalExpenses, 0, ',', '.')); ?></td>
                                <td class="text-right">Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?></td>
                                <td class="text-right"><?php echo e($totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 2) : 0); ?>%</td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="mt-3 text-muted">
                    <small><i class="fas fa-clock"></i> Laporan dibuat pada: <?php echo e(now()->format('d M Y H:i:s')); ?></small>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
<script>
function exportPdf() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '<?php echo e(route("travel.report.departure-summary.pdf")); ?>?' + params.toString();
}

function exportExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '<?php echo e(route("travel.report.departure-summary.excel")); ?>?' + params.toString();
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\departure-summary.blade.php ENDPATH**/ ?>