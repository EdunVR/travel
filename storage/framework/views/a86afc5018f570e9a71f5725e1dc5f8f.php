<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Laporan Kinerja Tim']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Laporan Kinerja Tim']); ?>
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
                <form method="GET" action="<?php echo e(route('travel.report.team-performance')); ?>" id="filterForm">
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
                                <label>Tim</label>
                                <select name="team_code" class="form-control">
                                    <option value="">Semua Tim</option>
                                    <?php $__currentLoopData = \App\Models\Team::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($team->team_code); ?>" <?php echo e(($filters['team_code'] ?? '') == $team->team_code ? 'selected' : ''); ?>>
                                            <?php echo e($team->team_name); ?>

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
                                    <a href="<?php echo e(route('travel.report.team-performance')); ?>" class="btn btn-secondary">
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
                    Data Kinerja Tim
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
                                <th>Nama Tim</th>
                                <th class="text-right">Total Tugas</th>
                                <th class="text-right">Selesai</th>
                                <th class="text-right">Pending</th>
                                <th class="text-right">In Progress</th>
                                <th class="text-right">Terlambat</th>
                                <th class="text-right">Tingkat Penyelesaian</th>
                                <th class="text-right">Rata-rata Waktu (Jam)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($data['team_name']); ?></td>
                                    <td class="text-right"><?php echo e($data['total_tasks']); ?></td>
                                    <td class="text-right">
                                        <span class="badge badge-success"><?php echo e($data['completed_tasks']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-secondary"><?php echo e($data['pending_tasks']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-info"><?php echo e($data['in_progress_tasks']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-danger"><?php echo e($data['overdue_tasks']); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?php echo e($data['completion_rate']); ?>%"
                                                 aria-valuenow="<?php echo e($data['completion_rate']); ?>" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                <?php echo e(number_format($data['completion_rate'], 1)); ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right"><?php echo e(number_format($data['average_completion_hours'], 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
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
    window.location.href = '<?php echo e(route("travel.report.team-performance.pdf")); ?>?' + params.toString();
}

function exportExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '<?php echo e(route("travel.report.team-performance.excel")); ?>?' + params.toString();
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\team-performance.blade.php ENDPATH**/ ?>