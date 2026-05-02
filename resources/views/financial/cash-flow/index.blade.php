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
    .section-header {
        background-color: #e0e0e0 !important;
        font-weight: bold;
    }
    .total-row {
        font-weight: bold;
        border-top: 2px solid #333 !important;
        border-bottom: 2px solid #333 !important;
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
    .indent-1 {
        padding-left: 20px;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="dollar-sign"></i> Laporan Arus Kas
            </h5>
            <div>
                @if(isset($cashFlowData))
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
            
            @if(isset($cashFlowData))
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
            
            <!-- Cash Flow Report -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">LAPORAN ARUS KAS</th>
                        </tr>
                    </thead>
                    
                    <!-- Operating Activities Section -->
                    <tr class="section-header">
                        <td colspan="3">ARUS KAS AKTIVITAS OPERASIONAL</td>
                    </tr>

                    @foreach($cashFlowData['operating_activities']['revenues'] as $revenue)
                    <tr>
                        <td>{{ $revenue['code'] }} - {{ $revenue['name'] }}</td>
                        <td></td>
                        <td class="text-right positive-amount">
                            {{ number_format($revenue['amount'], 0) }}
                        </td>
                    </tr>
                    @endforeach

                    @foreach($cashFlowData['operating_activities']['cogs'] as $cogs)
                    <tr>
                        <td>{{ $cogs['code'] }} - {{ $cogs['name'] }}</td>
                        <td></td>
                        <td class="text-right negative-amount">
                            ({{ number_format(abs($cogs['amount']), 0) }})
                        </td>
                    </tr>
                    @endforeach

                    @foreach($cashFlowData['operating_activities']['expenses'] as $expense)
                    <tr>
                        <td>{{ $expense['code'] }} - {{ $expense['name'] }}</td>
                        <td></td>
                        <td class="text-right negative-amount">
                            ({{ number_format(abs($expense['amount']), 0) }})
                        </td>
                    </tr>
                    @endforeach

                    @foreach($cashFlowData['operating_activities']['other_expenses'] as $expense)
                    <tr>
                        <td>{{ $expense['code'] }} - {{ $expense['name'] }}</td>
                        <td></td>
                        <td class="text-right negative-amount">
                            ({{ number_format(abs($expense['amount']), 0) }})
                        </td>
                    </tr>
                    @endforeach

                    <tr class="total-row">
                        <td><strong>SUBTOTAL</strong></td>
                        <td></td>
                        <td class="text-right {{ $cashFlowData['operating_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            <strong>{{ number_format($cashFlowData['operating_activities']['subtotal'], 0) }}</strong>
                        </td>
                    </tr>

                    <!-- Investing Activities Section -->
                    <tr class="section-header">
                        <td colspan="3">ARUS KAS AKTIVITAS INVESTASI</td>
                    </tr>

                    @foreach($cashFlowData['investing_activities']['items'] as $item)
                    <tr>
                        <td>{{ $item['code'] }} - {{ $item['name'] }}</td>
                        <td></td>
                        <td class="text-right {{ $item['amount'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            @if($item['amount'] >= 0)
                                {{ number_format($item['amount'], 0) }}
                            @else
                                ({{ number_format(abs($item['amount']), 0) }})
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    <tr class="total-row">
                        <td><strong>SUBTOTAL</strong></td>
                        <td></td>
                        <td class="text-right {{ $cashFlowData['investing_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            <strong>{{ number_format($cashFlowData['investing_activities']['subtotal'], 0) }}</strong>
                        </td>
                    </tr>

                    <!-- Financing Activities Section -->
                    <tr class="section-header">
                        <td colspan="3">ARUS KAS AKTIVITAS PENDANAAN</td>
                    </tr>

                    @foreach($cashFlowData['financing_activities']['items'] as $item)
                    <tr>
                        <td>{{ $item['code'] }} - {{ $item['name'] }}</td>
                        <td></td>
                        <td class="text-right {{ $item['amount'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            @if($item['amount'] >= 0)
                                {{ number_format($item['amount'], 0) }}
                            @else
                                ({{ number_format(abs($item['amount']), 0) }})
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    <tr class="total-row">
                        <td><strong>SUBTOTAL</strong></td>
                        <td></td>
                        <td class="text-right {{ $cashFlowData['financing_activities']['subtotal'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            <strong>{{ number_format($cashFlowData['financing_activities']['subtotal'], 0) }}</strong>
                        </td>
                    </tr>

                    <!-- Total Kenaikan/Penurunan Kas -->
                    <tr class="final-row">
                        <td colspan="2"><strong>TOTAL KENAIKAN (karena +)</strong></td>
                        <td class="text-right positive-amount">
                            <strong>{{ number_format($cashFlowData['total_increase'], 0) }}</strong>
                        </td>
                    </tr>
                    <tr class="final-row">
                        <td colspan="2"><strong>TOTAL PENURUNAN (karena -)</strong></td>
                        <td class="text-right negative-amount">
                            <strong>({{ number_format($cashFlowData['total_decrease'], 0) }})</strong>
                        </td>
                    </tr>

                    <!-- Saldo Kas -->
                    <tr>
                        <td colspan="2">Saldo Kas Awal Periode</td>
                        <td class="text-right positive-amount">
                            {{ number_format($cashFlowData['beginning_cash'], 0) }}
                        </td>
                    </tr>
                    <tr class="final-row">
                        <td colspan="2"><strong>Saldo Kas Akhir Periode</strong></td>
                        <td class="text-right positive-amount">
                            <strong>{{ number_format($cashFlowData['ending_cash'], 0) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Cash Accounts Detail -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Detail Saldo Kas dan Bank</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th class="text-right">Saldo Awal</th>
                                    <th class="text-right">Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cashFlowData['cash_details'] as $account)
                                <tr>
                                    <td>{{ $account['code'] }}</td>
                                    <td>{{ $account['name'] }}</td>
                                    <td class="text-right {{ $account['beginning_balance'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                        {{ number_format($account['beginning_balance'], 0) }}
                                    </td>
                                    <td class="text-right {{ $account['ending_balance'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                        {{ number_format($account['ending_balance'], 0) }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="2"><strong>TOTAL</strong></td>
                                    <td class="text-right {{ $cashFlowData['beginning_cash'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                        <strong>{{ number_format($cashFlowData['beginning_cash'], 0) }}</strong>
                                    </td>
                                    <td class="text-right {{ $cashFlowData['ending_cash'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                        <strong>{{ number_format($cashFlowData['ending_cash'], 0) }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="dollar-sign" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Tampilkan Laporan Arus Kas</h5>
                <p class="text-muted">Silakan pilih tahun buku dan periode untuk melihat laporan arus kas</p>
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
                <h5 class="modal-title"><i data-feather="download"></i> Export Laporan Arus Kas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="POST" action="{{ route('financial.cash-flow.export') }}">
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
                var filename = 'Laporan_Arus_Kas';
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
