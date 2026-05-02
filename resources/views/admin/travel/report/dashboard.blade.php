<x-layouts.admin title="Dashboard Laporan Travel">
    <div class="container-fluid">
        <!-- Key Metrics Cards -->
        <div class="row">
            <!-- Total Jamaah -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($totalJamaah) }}</h3>
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
                        <h3>{{ $upcomingDepartures->count() }}</h3>
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
                        <h3>{{ $pendingPayments->count() }}</h3>
                        <p>Pembayaran Pending</p>
                        <small>Rp {{ number_format($totalPendingAmount, 0, ',', '.') }}</small>
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
                        <h3>{{ $incompleteDocuments->count() }}</h3>
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
                                @forelse($upcomingDepartures as $departure)
                                <tr>
                                    <td>{{ $departure->keberangkatan_code }}</td>
                                    <td>{{ $departure->keberangkatan_name }}</td>
                                    <td>{{ $departure->travelPackage->package_name }}</td>
                                    <td>{{ $departure->departure_date->format('d M Y') }}</td>
                                    <td>{{ $departure->jamaahBookings->count() }}</td>
                                    <td>
                                        @if($departure->status == 'planning')
                                            <span class="badge badge-secondary">Planning</span>
                                        @elseif($departure->status == 'confirmed')
                                            <span class="badge badge-success">Confirmed</span>
                                        @elseif($departure->status == 'departed')
                                            <span class="badge badge-info">Departed</span>
                                        @else
                                            <span class="badge badge-primary">{{ ucfirst($departure->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada keberangkatan dalam 30 hari ke depan</td>
                                </tr>
                                @endforelse
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
                                @forelse($pendingPayments->take(10) as $booking)
                                <tr>
                                    <td>{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->jamaah->nama_member ?? '-' }}</td>
                                    <td>{{ $booking->travelPackage->package_name }}</td>
                                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($booking->payment_status == 'unpaid')
                                            <span class="badge badge-danger">Belum Bayar</span>
                                        @elseif($booking->payment_status == 'partial')
                                            <span class="badge badge-warning">Sebagian</span>
                                        @else
                                            <span class="badge badge-success">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada pembayaran pending</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($pendingPayments->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('travel.booking.index') }}?payment_status=partial,unpaid" class="btn btn-sm btn-primary">
                                Lihat Semua ({{ $pendingPayments->count() }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Booking Volume Trend Chart
    const bookingVolumeCtx = document.getElementById('bookingVolumeChart').getContext('2d');
    const bookingVolumeData = @json($bookingVolumeTrend);
    
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
    const profitMarginData = @json($profitMargins->values());
    
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
    @endpush
</x-layouts.admin>
