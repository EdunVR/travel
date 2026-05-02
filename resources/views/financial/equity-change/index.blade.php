@extends('app')

@section('content')
<style>
    .card-header {
        background-color: #2e7d32;
        color: white;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th {
        white-space: nowrap;
        vertical-align: middle;
        background-color: #f5f5f5;
    }
    .table td {
        vertical-align: middle;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    .equity-account { background-color: #e8f5e9; }
    .withdrawal-account { background-color: #ffebee; }
    .summary-row {
        font-weight: bold;
        background-color: #f5f5f5 !important;
    }
    .final-row {
        font-weight: bold;
        background-color: #e8f5e9 !important;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .positive-amount {
        color: #2e7d32;
    }
    .negative-amount {
        color: #c62828;
    }
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="bar-chart-2"></i> Laporan Perubahan Modal
            </h5>
            <div>
                @if(isset($equityData))
                <button id="exportBtn" class="btn btn-success btn-sm">
                    <i data-feather="download"></i> Export
                </button>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filter Section -->
            <div class="filter-section">
                <form id="filterForm" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="book_id" class="form-label">Tahun Buku *</label>
                            <select name="book_id" id="book_id" class="form-select" required>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ $selectedBookId == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }} ({{ $book->start_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                value="{{ $dateTo ? $dateTo->format('Y-m-d') : '' }}">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            @if(isset($equityData))
            <!-- Report Information -->
            <div class="mb-3">
                <h5>
                    <strong>Tahun Buku:</strong> {{ $accountingBook->name }} 
                    ({{ $accountingBook->start_date->format('d/m/Y') }} - {{ $accountingBook->end_date->format('d/m/Y') }})
                </h5>
                <p>
                    <strong>Periode:</strong> 
                    @if($dateFrom && $dateTo)
                        {{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }}
                    @else
                        Semua Periode
                    @endif
                </p>
            </div>
            
            <!-- Beginning Equity  style="max-width: 800px; margin: auto;" -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">MODAL AWAL</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equityData['equity_items'] as $item)
                            @if($item['is_beginning'])
                            <tr class="equity-account">
                                <td>{{ $item['code'] }}</td>
                                <td style="text-align: left;">{{ $item['name'] }}</td>
                                <td class="positive-amount" style="text-align: right;">{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr class="summary-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL MODAL AWAL</strong></td>
                            <td class="positive-amount" style="text-align: right;">{{ number_format($equityData['beginning_equity'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Additional Investment -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">TAMBAHAN MODAL</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equityData['equity_items'] as $item)
                            @if($item['is_additional'])
                            <tr class="equity-account">
                                <td>{{ $item['code'] }}</td>
                                <td style="text-align: left;">{{ $item['name'] }}</td>
                                <td class="positive-amount" style="text-align: right;">{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr class="summary-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL TAMBAHAN MODAL</strong></td>
                            <td class="positive-amount" style="text-align: right;">{{ number_format($equityData['additional_investment'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Laba/Rugi Bersih -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="summary-row">
                            <td colspan="2" style="text-align: left;">
                                <strong>LABA/RUGI BERSIH</strong>
                                <br>
                                <small>
                                    Pendapatan: {{ number_format($equityData['profit_loss_detail']['totals']['revenue'], 2) }} - 
                                    HPP: {{ number_format($equityData['profit_loss_detail']['totals']['cogs'], 2) }} = 
                                    Laba Kotor: {{ number_format($equityData['profit_loss_detail']['gross_profit'], 2) }}
                                    <br>
                                    Beban Operasional: {{ number_format($equityData['profit_loss_detail']['totals']['operating_expenses'], 2) }} | 
                                    Pendapatan Lain: {{ number_format($equityData['profit_loss_detail']['totals']['other_income'], 2) }} | 
                                    Beban Lain: {{ number_format($equityData['profit_loss_detail']['totals']['other_expenses'], 2) }}
                                    <br>
                                    Pajak: {{ number_format($equityData['profit_loss_detail']['tax_expense'], 2) }}
                                </small>
                            </td>
                            <td style="text-align: right; {{ $equityData['profit_loss'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($equityData['profit_loss'] >= 0)
                                    {{ number_format($equityData['profit_loss'], 2) }}
                                @else
                                    ({{ number_format(abs($equityData['profit_loss']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Owner Withdrawal -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">PENGAMBILAN PRIVE</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equityData['equity_items'] as $item)
                            @if($item['is_withdrawal'])
                            <tr class="withdrawal-account">
                                <td>{{ $item['code'] }}</td>
                                <td style="text-align: left;">{{ $item['name'] }}</td>
                                <td class="negative-amount" style="text-align: right;">({{ number_format(abs($item['amount']), 2) }})</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr class="summary-row">
                            <td colspan="2" class="text-center"><strong>TOTAL PENGAMBILAN PRIVE</strong></td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($equityData['owner_withdrawal'], 2) }})</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Ending Equity Calculation -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">PERHITUNGAN MODAL AKHIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">Modal Awal</td>
                            <td class="positive-amount" style="text-align: right;">{{ number_format($equityData['beginning_equity'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Tambahan Modal</td>
                            <td class="positive-amount" style="text-align: right;">{{ number_format($equityData['additional_investment'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Laba/Rugi Bersih</td>
                            <td style="text-align: right; {{ $equityData['profit_loss'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($equityData['profit_loss'] >= 0)
                                    {{ number_format($equityData['profit_loss'], 2) }}
                                @else
                                    ({{ number_format(abs($equityData['profit_loss']), 2) }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Pengambilan Prive</td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($equityData['owner_withdrawal'], 2) }})</td>
                        </tr>
                        <tr class="final-row">
                            <td colspan="2"><strong>MODAL AKHIR</strong></td>
                            <td class="positive-amount" style="text-align: right;"><strong>{{ number_format($equityData['ending_equity'], 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="bar-chart-2" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Tampilkan Laporan Perubahan Modal</h5>
                <p class="text-muted">Silakan pilih tahun buku dan periode untuk melihat laporan perubahan modal</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i data-feather="download"></i> Export Laporan Perubahan Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="POST" action="{{ route('financial.equity-change.export') }}">
                @csrf
                <input type="hidden" name="book_id" value="{{ $selectedBookId ?? '' }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                <input type="hidden" name="date_to" value="{{ $dateTo ? $dateTo->format('Y-m-d') : '' }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="export_type" id="exportPdf" value="pdf" checked>
                            <label class="form-check-label" for="exportPdf">
                                PDF (Portable Document Format)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="export_type" id="exportExcel" value="excel">
                            <label class="form-check-label" for="exportExcel">
                                Excel (Microsoft Excel)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    feather.replace();
    
    // Export button handler - show modal
    $('#exportBtn').click(function() {
        $('#exportModal').modal('show');
    });

    $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
        $('#exportModal').modal('hide');
    });
    
    // Export form submission
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Disable button and show loading
        $submitBtn.prop('disabled', true).html('<i data-feather="loader"></i> Exporting...');
        feather.replace();
        
        // Submit form via AJAX
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Get filename from content-disposition header or use default
                var disposition = xhr.getResponseHeader('content-disposition');
                var filename = 'Laporan_Perubahan_Modal';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].split(';')[0];
                    filename = filename.replace(/['"]/g, '');
                }
                
                // Create download link
                var blob = new Blob([data]);
                var downloadUrl = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                
                // Clean up
                setTimeout(function() {
                    URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                }, 100);
            },
            error: function(xhr) {
                alert('Export failed: ' + xhr.statusText);
            },
            complete: function() {
                // Re-enable button and reset text
                $submitBtn.prop('disabled', false).html('Export');
                feather.replace();
                
                // Close modal
                $('#exportModal').modal('hide');
            }
        });
    });
});
</script>
@endpush
