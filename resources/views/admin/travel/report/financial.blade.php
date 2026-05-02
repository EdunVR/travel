<x-layouts.admin title="Laporan Keuangan">
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
                <form method="GET" action="{{ route('travel.report.financial') }}" id="filterForm">
                    <div class="row">
                        <!-- Mode toggle -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tampilkan Per</label>
                                <select name="mode" class="form-control" id="modeSelect" onchange="toggleModeFields()">
                                    <option value="package" {{ ($mode ?? 'package') === 'package' ? 'selected' : '' }}>Paket Perjalanan</option>
                                    <option value="keberangkatan" {{ ($mode ?? '') === 'keberangkatan' ? 'selected' : '' }}>Keberangkatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                        </div>
                        <!-- Per Paket: tipe paket -->
                        <div class="col-md-2" id="fieldPackageType" style="{{ ($mode ?? 'package') === 'keberangkatan' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Tipe Paket</label>
                                <select name="package_type" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="hajj" {{ ($filters['package_type'] ?? '') == 'hajj' ? 'selected' : '' }}>Hajj</option>
                                    <option value="umrah" {{ ($filters['package_type'] ?? '') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                                </select>
                            </div>
                        </div>
                        <!-- Per Keberangkatan: pilih keberangkatan spesifik -->
                        <div class="col-md-2" id="fieldKeberangkatan" style="{{ ($mode ?? 'package') !== 'keberangkatan' ? 'display:none' : '' }}">
                            <div class="form-group">
                                <label>Keberangkatan</label>
                                <select name="id_keberangkatan" class="form-control">
                                    <option value="">Semua Keberangkatan</option>
                                    @foreach($allKeberangkatan ?? [] as $kb)
                                        <option value="{{ $kb->id }}" {{ ($filters['id_keberangkatan'] ?? '') == $kb->id ? 'selected' : '' }}>
                                            {{ $kb->keberangkatan_code }} - {{ $kb->keberangkatan_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
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
                        <h4>Rp {{ number_format($totals['total_revenue'], 0, ',', '.') }}</h4>
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
                        <h4>Rp {{ number_format($totals['total_costs'], 0, ',', '.') }}</h4>
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
                        <h4>Rp {{ number_format($totals['total_profit'], 0, ',', '.') }}</h4>
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
                        <h4>{{ number_format($totals['average_profit_margin'], 2) }}%</h4>
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
                    @if(($mode ?? 'package') === 'keberangkatan')
                    @php $anyAdjusted = $reportData->where('laporan_disesuaikan', true)->count(); @endphp
                    @if($anyAdjusted > 0)
                    <div class="alert alert-info alert-sm mb-3 py-2">
                        <i class="bx bx-info-circle"></i>
                        <strong>{{ $anyAdjusted }} keberangkatan</strong> sudah disesuaikan laporan keuangannya (surplus/defisit diterapkan ke biaya).
                    </div>
                    @endif
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
                            @forelse($reportData as $data)
                                <tr>
                                    <td><strong>{{ $data['keberangkatan_code'] }}</strong></td>
                                    <td>{{ $data['keberangkatan_name'] }}</td>
                                    <td>{{ $data['package_name'] }}</td>
                                    <td><span class="badge badge-{{ $data['package_type'] == 'hajj' ? 'primary' : 'info' }}">{{ strtoupper($data['package_type']) }}</span></td>
                                    <td>{{ $data['departure_date'] ? \Carbon\Carbon::parse($data['departure_date'])->format('d M Y') : '-' }}</td>
                                    <td>{{ $data['jamaah_count'] }}</td>
                                    <td class="text-right">{{ number_format($data['hpp_per_person'], 0, ',', '.') }}</td>
                                    <td class="text-right text-warning">{{ number_format($data['addon_hpp'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['costs'], 0, ',', '.') }}</td>
                                    <td class="text-right {{ $data['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        <strong>{{ number_format($data['profit'], 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-right">{{ number_format($data['profit_margin'], 2) }}%</td>
                                    <td class="text-right text-success">
                                        {{ number_format($data['rab_realisasi'] ?? 0, 0, ',', '.') }}
                                        @if(($data['rab_realisasi'] ?? 0) > 0 && ($data['costs'] ?? 0) > 0)
                                            <br><small class="text-muted">{{ number_format(min(100, ($data['rab_realisasi'] / $data['costs']) * 100), 1) }}%</small>
                                        @endif
                                    </td>
                                    <td class="text-right {{ ($data['rab_hutang'] ?? 0) > 0 ? 'text-danger' : '' }}">
                                        {{ ($data['rab_hutang'] ?? 0) > 0 ? number_format($data['rab_hutang'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-right {{ ($data['surplus_defisit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        <strong>
                                            {{ ($data['surplus_defisit'] ?? 0) >= 0 ? 'Surplus' : 'Defisit' }}:
                                            {{ number_format(abs($data['surplus_defisit'] ?? 0), 0, ',', '.') }}
                                        </strong>
                                        @if($data['laporan_disesuaikan'] ?? false)
                                            <br><span class="badge badge-info" style="font-size:10px">✓ Disesuaikan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="15" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="8">Total</td>
                                <td class="text-right">{{ number_format($totals['total_revenue'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($totals['total_costs'], 0, ',', '.') }}</td>
                                <td class="text-right {{ $totals['total_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($totals['total_profit'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-right">{{ number_format($totals['average_profit_margin'], 2) }}%</td>
                                <td class="text-right text-success">{{ number_format($reportData->sum('rab_realisasi'), 0, ',', '.') }}</td>
                                <td class="text-right text-danger">{{ number_format($reportData->sum('rab_hutang'), 0, ',', '.') }}</td>
                                <td class="text-right {{ $reportData->sum('surplus_defisit') >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $reportData->sum('surplus_defisit') >= 0 ? 'Surplus' : 'Defisit' }}: {{ number_format(abs($reportData->sum('surplus_defisit')), 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    @else
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
                            @forelse($reportData as $data)
                                <tr>
                                    <td>{{ $data['package_code'] }}</td>
                                    <td>{{ $data['package_name'] }}</td>
                                    <td><span class="badge badge-{{ $data['package_type'] == 'hajj' ? 'primary' : 'info' }}">{{ strtoupper($data['package_type']) }}</span></td>
                                    <td>{{ $data['departure_date'] ? \Carbon\Carbon::parse($data['departure_date'])->format('d M Y') : '-' }}</td>
                                    <td>{{ $data['jamaah_count'] }}</td>
                                    <td class="text-right">{{ number_format($data['hpp_per_person'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['price_per_person'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['costs'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['profit'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['profit_margin'], 2) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @endif
                </div>
                <div class="mt-3 text-muted">
                    <small><i class="fas fa-clock"></i> Laporan dibuat pada: {{ now()->format('d M Y H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function toggleModeFields() {
        const mode = document.getElementById('modeSelect').value;
        document.getElementById('fieldPackageType').style.display = mode === 'keberangkatan' ? 'none' : '';
        document.getElementById('fieldKeberangkatan').style.display = mode === 'keberangkatan' ? '' : 'none';
    }

    function exportPdf() {
        const params = new URLSearchParams($('#filterForm').serialize());
        window.location.href = '{{ route("travel.report.financial.pdf") }}?' + params.toString();
    }

    function exportExcel() {
        const params = new URLSearchParams($('#filterForm').serialize());
        window.location.href = '{{ route("travel.report.financial.excel") }}?' + params.toString();
    }
    </script>
    @endpush
</x-layouts.admin>
