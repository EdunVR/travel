<x-layouts.admin title="Laporan Ringkasan Keberangkatan">
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
                <form method="GET" action="{{ route('travel.report.departure-summary') }}" id="filterForm">
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
                                <label>Outlet</label>
                                <select name="id_outlet" class="form-control">
                                    <option value="">Semua Outlet</option>
                                    @foreach(\App\Models\Outlet::all() as $outlet)
                                        <option value="{{ $outlet->id_outlet }}" {{ ($filters['id_outlet'] ?? '') == $outlet->id_outlet ? 'selected' : '' }}>
                                            {{ $outlet->nama_outlet }}
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
                                    <a href="{{ route('travel.report.departure-summary') }}" class="btn btn-secondary">
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
                            @php
                                $totalJamaah = 0;
                                $totalRevenue = 0;
                                $totalExpenses = 0;
                                $totalProfit = 0;
                            @endphp
                            @forelse($reportData as $data)
                                @php
                                    $totalJamaah += $data['jamaah_count'];
                                    $totalRevenue += $data['revenue'];
                                    $totalExpenses += $data['expenses'];
                                    $totalProfit += $data['profit'];
                                @endphp
                                <tr>
                                    <td>{{ $data['keberangkatan_code'] }}</td>
                                    <td>{{ $data['keberangkatan_name'] }}</td>
                                    <td>{{ $data['departure_date']->format('d M Y') }}</td>
                                    <td>{{ $data['jamaah_count'] }}</td>
                                    <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($data['expenses'], 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($data['profit'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['profit_margin'], 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($reportData->count() > 0)
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="3">TOTAL</td>
                                <td>{{ $totalJamaah }}</td>
                                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 2) : 0 }}%</td>
                            </tr>
                        </tfoot>
                        @endif
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
    window.location.href = '{{ route("travel.report.departure-summary.pdf") }}?' + params.toString();
}

function exportExcel() {
    const params = new URLSearchParams($('#filterForm').serialize());
    window.location.href = '{{ route("travel.report.departure-summary.excel") }}?' + params.toString();
}
</script>
    @endpush
</x-layouts.admin>
