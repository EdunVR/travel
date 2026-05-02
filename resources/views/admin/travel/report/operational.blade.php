<x-layouts.admin title="Laporan Operasional">
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
                <form method="GET" action="{{ route('travel.report.operational') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tampilkan
                                    </button>
                                    <a href="{{ route('travel.report.operational') }}" class="btn btn-secondary">
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
                    Waktu Penyelesaian Workflow Stage
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
                                <th>Workflow Stage</th>
                                <th class="text-right">Rata-rata Durasi (Jam)</th>
                                <th class="text-right">Rata-rata Durasi (Hari)</th>
                                <th class="text-right">Jumlah Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $data)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $data['stage_name'])) }}</td>
                                    <td class="text-right">{{ number_format($data['average_duration_hours'], 2) }}</td>
                                    <td class="text-right">{{ number_format($data['average_duration_hours'] / 24, 2) }}</td>
                                    <td class="text-right">{{ $data['package_count'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-muted">
                    <small><i class="fas fa-clock"></i> Laporan dibuat pada: {{ now()->format('d M Y H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
function exportPdf() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '{{ route("travel.report.operational.pdf") }}?' + params.toString();
}

function exportExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '{{ route("travel.report.operational.excel") }}?' + params.toString();
}
</script>
    @endpush
</x-layouts.admin>
