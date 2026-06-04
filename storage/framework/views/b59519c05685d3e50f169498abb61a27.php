<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Dashboard Laporan Travel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard Laporan Travel']); ?>
    <div class="container-fluid">
        <!-- Key Metrics Cards -->
        <div class="row">
            <!-- Total Jamaah -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo e(number_format($totalJamaah)); ?></h3>
                        <p>Total Jamaah</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Upcoming Departures -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo e($upcomingDepartures->count()); ?></h3>
                        <p>Keberangkatan (30 Hari)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo e($pendingPayments->count()); ?></h3>
                        <p>Pembayaran Pending</p>
                        <small>Rp <?php echo e(number_format($totalPendingAmount, 0, ',', '.')); ?></small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <!-- Incomplete Documents -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo e($incompleteDocuments->count()); ?></h3>
                        <p>Dokumen Belum Lengkap</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Booking Volume Trend -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Tren Volume Booking (12 Bulan Terakhir)
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingVolumeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Profit Margin by Package Type -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Profit Margin per Tipe Paket
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="profitMarginChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Departures Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Keberangkatan Mendatang (30 Hari)
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Keberangkatan</th>
                                    <th>Paket</th>
                                    <th>Tanggal Berangkat</th>
                                    <th>Jumlah Jamaah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $upcomingDepartures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departure): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($departure->keberangkatan_code); ?></td>
                                    <td><?php echo e($departure->keberangkatan_name); ?></td>
                                    <td><?php echo e($departure->travelPackage->package_name); ?></td>
                                    <td><?php echo e($departure->departure_date->format('d M Y')); ?></td>
                                    <td><?php echo e($departure->jamaahBookings->count()); ?></td>
                                    <td>
                                        <?php if($departure->status == 'planning'): ?>
                                            <span class="badge badge-secondary">Planning</span>
                                        <?php elseif($departure->status == 'confirmed'): ?>
                                            <span class="badge badge-success">Confirmed</span>
                                        <?php elseif($departure->status == 'departed'): ?>
                                            <span class="badge badge-info">Departed</span>
                                        <?php else: ?>
                                            <span class="badge badge-primary"><?php echo e(ucfirst($departure->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada keberangkatan dalam 30 hari ke depan</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Pembayaran Pending
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Nama Jamaah</th>
                                    <th>Paket</th>
                                    <th>Total Harga</th>
                                    <th>Sudah Dibayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pendingPayments->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($booking->booking_code); ?></td>
                                    <td><?php echo e($booking->jamaah->nama_member ?? '-'); ?></td>
                                    <td><?php echo e($booking->travelPackage->package_name); ?></td>
                                    <td>Rp <?php echo e(number_format($booking->total_price, 0, ',', '.')); ?></td>
                                    <td>Rp <?php echo e(number_format($booking->paid_amount, 0, ',', '.')); ?></td>
                                    <td>Rp <?php echo e(number_format($booking->remaining_amount, 0, ',', '.')); ?></td>
                                    <td>
                                        <?php if($booking->payment_status == 'unpaid'): ?>
                                            <span class="badge badge-danger">Belum Bayar</span>
                                        <?php elseif($booking->payment_status == 'partial'): ?>
                                            <span class="badge badge-warning">Sebagian</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada pembayaran pending</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if($pendingPayments->count() > 10): ?>
                        <div class="text-center mt-3">
                            <a href="<?php echo e(route('travel.booking.index')); ?>?payment_status=partial,unpaid" class="btn btn-sm btn-primary">
                                Lihat Semua (<?php echo e($pendingPayments->count()); ?>)
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Booking Volume Trend Chart
    const bookingVolumeCtx = document.getElementById('bookingVolumeChart').getContext('2d');
    const bookingVolumeData = <?php echo json_encode($bookingVolumeTrend, 15, 512) ?>;
    
    new Chart(bookingVolumeCtx, {
        type: 'line',
        data: {
            labels: bookingVolumeData.map(d => d.month),
            datasets: [{
                label: 'Jumlah Booking',
                data: bookingVolumeData.map(d => d.count),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Revenue (Juta Rp)',
                data: bookingVolumeData.map(d => d.revenue / 1000000),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Booking'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (Juta Rp)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });

    // Profit Margin Chart
    const profitMarginCtx = document.getElementById('profitMarginChart').getContext('2d');
    const profitMarginData = <?php echo json_encode($profitMargins->values(), 15, 512) ?>;
    
    new Chart(profitMarginCtx, {
        type: 'doughnut',
        data: {
            labels: profitMarginData.map(d => d.type.toUpperCase()),
            datasets: [{
                label: 'Profit Margin (%)',
                data: profitMarginData.map(d => d.margin),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toFixed(2) + '%';
                        }
                    }
                }
            }
        }
    });
});
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\report\dashboard.blade.php ENDPATH**/ ?>