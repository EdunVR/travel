<style>
    /* Warna Dasar */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }
    
    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.25rem;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-body {
        background-color: #ffffff;
        padding: 1.25rem;
    }
    
    /* Tabel */
    .table {
        font-size: 1.5rem;
    }
    
    .table thead th {
        background-color: #f8f9fc !important;
        color: #4e73df;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-bottom-width: 2px;
        text-transform: uppercase;
        font-size: 1.5rem;
        letter-spacing: 0.5px;
    }
    
    .table td {
        padding: 0.65rem 1rem;
        vertical-align: middle;
        border-color: #f6f6f6;
        font-size: 1.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }
    
    /* Legend */
    .legend-container {
        background-color: #ffffff;
        border-color: #e3e6f0 !important;
    }
    
    .legend-color {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 3px;
        margin-right: 6px;
        vertical-align: middle;
    }
    
    .legend-text {
        font-size: 0.8rem;
        color: #5a5c69;
    }
    
    /* Badge */
    .badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.3em 0.6em;
    }
    
    /* Padding Umum */
    .container-fluid {
        padding: 16px;
    }
    
    .mb-4 {
        margin-bottom: 1.25rem !important;
    }
    
    .mt-4 {
        margin-top: 1.25rem !important;
    }
    
    .p-4 {
        padding: 1.25rem !important;
    }
    
    /* Tombol */
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #3d5dd9;
        border-color: #3d5dd9;
    }
    
    .btn-success {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }
    
    .btn-success:hover {
        background-color: #17a673;
        border-color: #17a673;
    }
    
    .btn-light {
        background-color: #f8f9fc;
        border-color: #e3e6f0;
    }
    
    .btn-light:hover {
        background-color: #e3e6f0;
        border-color: #d1d5e0;
    }
    
    /* Form */
    .form-control {
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .color-badge {
        display: inline-block;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        vertical-align: middle;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
    }
    
    /* Informasi Utama - Layout Terstruktur */
    .text-small {
        font-size: 0.75rem;
    }
    
    .border-bottom {
        border-bottom: 1px solid #e3e6f0 !important;
    }
    
    /* Chart */
    .chart-pie {
        position: relative;
        height: 220px;
    }
    
    /* Utility Classes */
    .text-primary { color: #4e73df !important; }
    .text-success { color: #1cc88a !important; }
    .text-danger { color: #e74a3b !important; }
    .text-muted { color: #858796 !important; }
    
    /* Card Header */
    .card-header h6 {
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Feather Icons */
    .feather {
        width: 18px;
        height: 18px;
        stroke-width: 2.25px;
    }
    
    /* Custom Switch */
    .custom-control-label::before {
        border-radius: 12px;
        height: 1.25rem;
        top: 0.125rem;
    }
    
    .custom-control-label::after {
        border-radius: 50%;
        height: calc(1.25rem - 4px);
        width: calc(1.25rem - 4px);
        top: calc(0.125rem + 2px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 12px;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>

@extends('app')

@section('title', 'Bagi Hasil Kelompok - ' . $group->name)

@section('content')
<div class="container-fluid">
    <div class="card-all shadow mb-4">
        <a href="{{ route('irp.profit-management.index', ['tab' => 'group']) }}" class="btn btn-light btn-sm mb-3">
            <i data-feather="arrow-left"></i> Kembali
        </a>

        @if($history->status == 'draft')
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i data-feather="percent"></i> Pengaturan Pembagian
                </h6>
            </div>
            <div class="card-body bg-white">
            <form action="{{ route('irp.profit-management.update-distribution-group', $group->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Keterangan Bagi Hasil</label>
                                <input type="text" name="period" class="form-control" 
                                    value="{{ old('period', $history->period) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Distribusi</label>
                                <input type="date" name="distribution_date" class="form-control" 
                                    value="{{ old('distribution_date', $history->distribution_date ? $history->distribution_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Total Keuntungan (Rp)</label>
                                <input type="number" name="new_total_profit" class="form-control" 
                                    value="{{ old('new_total_profit', $history->total_profit) }}" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="useCustomPercentage" 
                                        name="use_custom_percentage" value="1" {{ $history->use_custom_percentage ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="useCustomPercentage">Gunakan Persentase Custom</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="customPercentageField" style="{{ !$history->use_custom_percentage ? 'display:none;' : '' }}">
                            <div class="form-group">
                                <label class="font-weight-bold">Persentase Custom (%)</label>
                                <input type="number" name="custom_percentage" class="form-control" 
                                    value="{{ old('custom_percentage', $history->custom_percentage) }}" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i data-feather="info"></i> Informasi Utama
                        </h6>
                    </div>
                    <div class="card-body bg-white">
                        <div class="d-flex flex-column">
                            <!-- Bagian Atas - Informasi Grup -->
                            <div class="mb-4 p-3 border-bottom">
                                <h5 class="font-weight-bold text-primary mb-3">{{ $group->name }}</h5>
                                <p class="text-muted mb-0">{{ $group->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                            
                            <!-- Bagian Tengah - Data Periode -->
                            <div class="mb-4 p-3 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <i data-feather="calendar" class="text-primary mr-2"></i>
                                    <div>
                                        <div class="text-small text-muted">Keterangan</div>
                                        <div class="font-weight-bold">{{ $history->period ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i data-feather="clock" class="text-primary mr-2"></i>
                                    <div>
                                        <div class="text-small text-muted">Tanggal Distribusi</div>
                                        <div class="font-weight-bold">
                                            @if($history->distribution_date)
                                                {{ \Carbon\Carbon::parse($history->distribution_date)->translatedFormat('d F Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bagian Bawah - Statistik -->
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Total Keuntungan</span>
                                    <span class="font-weight-bold text-primary">Rp {{ number_format($history->total_profit, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Total Investasi</span>
                                    <span class="font-weight-bold text-success">Rp {{ number_format($total_investment, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($history->use_custom_percentage)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Persentase Custom</span>
                                    <span class="font-weight-bold">@percentage($history->custom_percentage)</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Grafik Distribusi -->
                <div class="card mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i data-feather="pie-chart"></i> Distribusi Bagi Hasil
                        </h6>
                    </div>
                    <div class="card-body bg-white">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i data-feather="users"></i> Detail Pembagian Investor
                        </h6>
                        <div class="d-flex gap-2">
                            <span class="badge badge-primary">
                                <i data-feather="user" width="14" height="14" class="mr-1"></i>
                                Investor: {{ $uniqueInvestorsCount }}
                            </span>
                            <span class="badge badge-info">
                                <i data-feather="credit-card" width="14" height="14" class="mr-1"></i>
                                Rekening: {{ $validAccountsCount }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body bg-white p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="5%">Color</th>
                                        <th>Investor</th>
                                        <th>Rekening</th>
                                        <th class="text-right">Investasi</th>
                                        <th class="text-right">Persentase</th>
                                        <th class="text-right">Bagi Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $colorPalette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
                                        $totalProfitShare = 0;
                                    @endphp
                                    
                                    @foreach($group->investors as $index => $investor)
                                        @php
                                            $account = $investor->account;
                                            $accountInvestment = $account->total_investment;
                                            $effectivePercentage = $history->use_custom_percentage 
                                                ? $history->custom_percentage 
                                                : ($account ? $account->profit_percentage : 0);
                                            $profitShare = $total_investment > 0 
                                                ? ($history->total_profit * ($accountInvestment / $total_investment)) * ($effectivePercentage / 100)
                                                : 0;
                                            $totalProfitShare += $profitShare;
                                            $roiPercentage = $accountInvestment > 0 
                                                ? ($profitShare / $accountInvestment) * 100 
                                                : 0;
                                            $colorIndex = $index % count($colorPalette);
                                            $investorColor = $colorPalette[$colorIndex];
                                        @endphp
                                        
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>
                                                <span class="color-badge" style="background-color: {{ $investorColor }}"></span>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $investor->investor->name }}</div>
                                                <small class="text-muted">
                                                    <span class="badge badge-{{ $investor->investor->category == 'internal' ? 'primary' : 'success' }}">
                                                        {{ ucfirst($investor->investor->category) }}
                                                    </span>
                                                </small>
                                            </td>
                                            <td>
                                                @if($account)
                                                <div class="font-weight-bold">{{ $account->bank_name }}</div>
                                                <small class="text-muted">{{ $account->account_number }}</small>
                                                @else
                                                <span class="text-danger">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                Rp {{ number_format($accountInvestment, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right">
                                                <div class="font-weight-bold">{{ number_format($effectivePercentage) }}%</div>
                                                @if($account)
                                                <small class="d-block text-muted">Estimasi</small>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="font-weight-bold">Rp {{ number_format($profitShare, 0, ',', '.') }}</div>
                                                <small class="d-block text-muted">
                                                    {{ number_format(($accountInvestment / $total_investment) * 100, 2) }}%
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Konfirmasi -->
                @if($history->status == 'draft')
                <div class="card mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i data-feather="check-circle"></i> Konfirmasi Pembayaran
                        </h6>
                    </div>
                    <div class="card-body bg-white">
                        <form action="{{ route('irp.profit-management.confirm-payment-group', $group->id) }}" 
                            method="POST" 
                            enctype="multipart/form-data"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?')">
                            @csrf
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Upload Bukti Transfer (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" name="proof_file" class="custom-file-input" id="proofFile">
                                    <label class="custom-file-label" for="proofFile">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Format: PDF, JPG, PNG (Maks. 2MB)</small>
                            </div>
                            <button type="submit" class="btn btn-success float-right">
                                <i data-feather="check"></i> Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
                @elseif($history->proof_file)
                <!-- Bukti Transfer -->
                <div class="card mt-4 border-left-success">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i data-feather="file-text"></i> Bukti Transfer
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <a href="{{ asset('storage/'.$history->proof_file) }}" target="_blank" class="btn btn-primary">
                            <i data-feather="download"></i> Download Bukti Transfer
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Feather Icons
    feather.replace();
    
    // Toggle custom percentage field
    $('#useCustomPercentage').change(function() {
        $('#customPercentageField').toggle(this.checked);
    });

    // Grafik Distribusi dengan Warna yang Sesuai Tabel
    var ctx = document.getElementById('distributionChart');
    var colorPalette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($group->investors->pluck('investor.name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($group->investors->pluck('investment_amount')->toArray()) !!},
                backgroundColor: {!! json_encode($group->investors->map(function($item, $index) use ($colorPalette) {
                    return $colorPalette[$index % count($colorPalette)];
                })) !!},
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 70,
            legend: {
                display: false // Legend dihilangkan karena sudah ada di tabel
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var value = data.datasets[0].data[tooltipItem.index];
                        var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                        var percentage = Math.round((value / total) * 100);
                        return label + ': Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + 
                               ' (' + percentage + '%)';
                    }
                }
            }
        }
    });

    // Update custom file input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
@endpush
