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
    .revenue-account { background-color: #e8f5e9; }
    .expense-account { background-color: #ffebee; }
    .profit-row {
        font-weight: bold;
        background-color: #f5f5f5 !important;
    }
    .net-profit-row {
        font-weight: bold;
        background-color: #e8f5e9 !important;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .summary-card {
        border-left: 4px solid #2e7d32;
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
                <i data-feather="bar-chart-2"></i> Laporan Laba Rugi
            </h5>
            <div>
                @if(isset($profitLossData))
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
            
            @if(isset($profitLossData))
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
            
            @if(isset($profitLossData))
            <!-- Revenue Section -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">PENDAPATAN USAHA</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitLossData['revenues'] as $revenue)
                        <tr class="revenue-account">
                            <td>{{ $revenue['code'] }}</td>
                            <td style="text-align: left;">{{ $revenue['name'] }}</td>
                            <td style="text-align: right; {{ $revenue['amount'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($revenue['amount'] >= 0)
                                    {{ number_format($revenue['amount'], 2) }}
                                @else
                                    ({{ number_format(abs($revenue['amount']), 2) }})
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL PENDAPATAN USAHA</strong></td>
                            <td style="text-align: right; {{ $profitLossData['totals']['revenue'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['totals']['revenue'] >= 0)
                                    {{ number_format($profitLossData['totals']['revenue'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['totals']['revenue']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- COGS Section -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">HARGA POKOK PENJUALAN</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitLossData['cogs'] as $cogs)
                        <tr class="expense-account">
                            <td>{{ $cogs['code'] }}</td>
                            <td style="text-align: left;">{{ $cogs['name'] }}</td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($cogs['amount'], 2) }})</td>
                        </tr>
                        @endforeach
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL HPP</strong></td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($profitLossData['totals']['cogs'], 2) }})</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Gross Profit -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>LABA KOTOR</strong></td>
                            <td style="text-align: right; {{ $profitLossData['gross_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['gross_profit'] >= 0)
                                    {{ number_format($profitLossData['gross_profit'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['gross_profit']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Operating Expenses Section -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">BEBAN OPERASIONAL</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitLossData['operating_expenses'] as $expense)
                        <tr class="expense-account">
                            <td>{{ $expense['code'] }}</td>
                            <td style="text-align: left;">{{ $expense['name'] }}</td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($expense['amount'], 2) }})</td>
                        </tr>
                        @endforeach
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL BEBAN OPERASIONAL</strong></td>
                            <td class="negative-amount" style="text-align: right;">({{ number_format($profitLossData['totals']['operating_expenses'], 2) }})</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Operating Profit -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>LABA OPERASI</strong></td>
                            <td style="text-align: right; {{ $profitLossData['operating_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['operating_profit'] >= 0)
                                    {{ number_format($profitLossData['operating_profit'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['operating_profit']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Other Income/Expenses -->
            @if(count($profitLossData['other_income']) > 0 || count($profitLossData['other_expenses']) > 0)
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th colspan="3" class="text-center">PENDAPATAN/BEBAN LAIN-LAIN</th>
                        </tr>
                        <tr>
                            <th width="10%">Kode Akun</th>
                            <th style="text-align: left;">Nama Akun</th>
                            <th width="25%" class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitLossData['other_income'] as $income)
                        <tr class="revenue-account">
                            <td>{{ $income['code'] }}</td>
                            <td style="text-align: left;">{{ $income['name'] }}</td>
                            <td class="positive-amount" style="text-align: right;">
                                {{ number_format($income['amount'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                        
                        @foreach($profitLossData['other_expenses'] as $expense)
                        <tr class="expense-account">
                            <td>{{ $expense['code'] }}</td>
                            <td style="text-align: left;">{{ $expense['name'] }}</td>
                            <td class="negative-amount" style="text-align: right;">
                                ({{ number_format($expense['amount'], 2) }})
                            </td>
                        </tr>
                        @endforeach
                        
                        @if(count($profitLossData['other_income']) > 0)
                        <tr class="profit-row">
                            <td colspan="2" class="text-center"><strong>TOTAL PENDAPATAN LAIN</strong></td>
                            <td class="positive-amount" style="text-align: right;">
                                {{ number_format($profitLossData['totals']['other_income'], 2) }}
                            </td>
                        </tr>
                        @endif
                        
                        @if(count($profitLossData['other_expenses']) > 0)
                        <tr class="profit-row">
                            <td colspan="2" style="text-align: left;"><strong>TOTAL BEBAN LAIN</strong></td>
                            <td class="negative-amount" style="text-align: right;">
                                ({{ number_format($profitLossData['totals']['other_expenses'], 2) }})
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Profit Before Tax -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="profit-row">
                            <td colspan="2" class="text-center"><strong>LABA SEBELUM PAJAK</strong></td>
                            <td style="text-align: right; {{ $profitLossData['profit_before_tax'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['profit_before_tax'] >= 0)
                                    {{ number_format($profitLossData['profit_before_tax'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['profit_before_tax']), 2) }})
                                @endif
                            </td>
                        </tr>
                        
                        @if($profitLossData['tax_expense'] > 0)
                        <tr class="profit-row">
                            <td colspan="2" class="text-center"><strong>PAJAK PENGHASILAN (10%)</strong></td>
                            <td class="negative-amount" style="text-align: right;">
                                ({{ number_format($profitLossData['tax_expense'], 2) }})
                            </td>
                        </tr>
                        @endif
                        
                        <tr class="net-profit-row">
                            <td colspan="2" class="text-center"><strong>LABA BERSIH</strong></td>
                            <td style="text-align: right; {{ $profitLossData['net_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['net_profit'] >= 0)
                                    {{ number_format($profitLossData['net_profit'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['net_profit']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Profit Summary -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr class="profit-row">
                            <td colspan="2" class="text-center"><strong>LABA OPERASI</strong></td>
                            <td style="text-align: right; {{ $profitLossData['operating_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['operating_profit'] >= 0)
                                    {{ number_format($profitLossData['operating_profit'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['operating_profit']), 2) }})
                                @endif
                            </td>
                        </tr>
                        <tr class="net-profit-row">
                            <td colspan="2" class="text-center"><strong>LABA BERSIH</strong></td>
                            <td style="text-align: right; {{ $profitLossData['net_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                @if($profitLossData['net_profit'] >= 0)
                                    {{ number_format($profitLossData['net_profit'], 2) }}
                                @else
                                    ({{ number_format(abs($profitLossData['net_profit']), 2) }})
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i data-feather="bar-chart-2" class="feather-xl text-muted"></i>
                <h5 class="mt-3">Tampilkan Laporan Laba Rugi</h5>
                <p class="text-muted">Silakan pilih tahun buku dan periode untuk melihat laporan laba rugi</p>
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
                <h5 class="modal-title"><i data-feather="download"></i> Export Laporan Laba Rugi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="POST" action="{{ route('financial.profit-loss.export') }}">
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
                responseType: 'blob' // Important for file download
            },
            success: function(data, status, xhr) {
                // Get filename from content-disposition header or use default
                var disposition = xhr.getResponseHeader('content-disposition');
                var filename = 'export';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].split(';')[0];
                    // Remove quotes if present
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
