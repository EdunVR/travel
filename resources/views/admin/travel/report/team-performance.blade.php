<x-layouts.admin title="Laporan Kinerja Tim">
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
                <form method="GET" action="{{ route('travel.report.team-performance') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tim</label>
                                <select name="team_code" class="form-control">
                                    <option value="">Semua Tim</option>
                                    @foreach(\App\Models\Team::all() as $team)
                                        <option value="{{ $team->team_code }}" {{ ($filters['team_code'] ?? '') == $team->team_code ? 'selected' : '' }}>
                                            {{ $team->team_name }}
                                        </option>
                                    @endforeach
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
                                    <a href="{{ route('travel.report.team-performance') }}" class="btn btn-secondary">
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
                            @forelse($reportData as $data)
                                <tr>
                                    <td>{{ $data['team_name'] }}</td>
                                    <td class="text-right">{{ $data['total_tasks'] }}</td>
                                    <td class="text-right">
                                        <span class="badge badge-success">{{ $data['completed_tasks'] }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-secondary">{{ $data['pending_tasks'] }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-info">{{ $data['in_progress_tasks'] }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-danger">{{ $data['overdue_tasks'] }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $data['completion_rate'] }}%"
                                                 aria-valuenow="{{ $data['completion_rate'] }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($data['completion_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">{{ number_format($data['average_completion_hours'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
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
    window.location.href = '{{ route("travel.report.team-performance.pdf") }}?' + params.toString();
}

function exportExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '{{ route("travel.report.team-performance.excel") }}?' + params.toString();
}
</script>
    @endpush
</x-layouts.admin>
