<x-layouts.admin title="Laporan Travel">
    <div class="container-fluid">
        <div class="row">
            <!-- Dashboard -->
            @hasPermission('travel.report.dashboard')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h4>Dashboard</h4>
                        <p>Ringkasan Metrik Utama</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <a href="{{ route('admin.inventaris.travel.report.dashboard') }}" class="small-box-footer">
                        Lihat Dashboard <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            @endhasPermission

            <!-- Departure Summary Report -->
            @hasPermission('travel.report.view')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>Ringkasan Keberangkatan</h4>
                        <p>Jamaah, Revenue, Expenses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <a href="{{ route('admin.inventaris.travel.report.departure-summary') }}" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            @endhasPermission

            <!-- Financial Report -->
            @hasPermission('travel.report.view')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Laporan Keuangan</h4>
                        <p>Revenue, Costs, Profit Margin</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="{{ route('admin.inventaris.travel.report.financial') }}" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            @endhasPermission

            <!-- Operational Report -->
            @hasPermission('travel.report.view')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Laporan Operasional</h4>
                        <p>Waktu Penyelesaian Stage</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <a href="{{ route('admin.inventaris.travel.report.operational') }}" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            @endhasPermission

            <!-- Team Performance Report -->
            @hasPermission('travel.report.view')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h4>Kinerja Tim</h4>
                        <p>Tingkat Penyelesaian Tugas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.inventaris.travel.report.team-performance') }}" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            @endhasPermission
        </div>
    </div>
</x-layouts.admin>
