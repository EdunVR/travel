@extends('app')

@section('content')
<style>
    .account-section {
        margin-bottom: 40px;
        page-break-inside: avoid;
    }
    .account-header {
        background-color: #e8f5e9;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .account-title {
        font-weight: bold;
        font-size: 1.1em;
    }
    .summary-card {
        border-left: 4px solid #2e7d32;
        padding: 5px 10px;
        margin-bottom: 10px;
    }
    /* Tambahkan style dari index.blade.php */
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
    }
    .table td {
        vertical-align: middle;
    }
    .balance-positive {
        color: #2e7d32;
        font-weight: bold;
    }
    .balance-negative {
        color: #c62828;
        font-weight: bold;
    }
    .filter-section {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .account-info {
        background-color: #e8f5e9;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .feather {
        width: 16px;
        height: 16px;
        vertical-align: text-bottom;
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
                <i data-feather="book"></i> Buku Besar - Semua Akun
            </h5>
            <div>
                <button id="exportAllBtn" class="btn btn-success btn-sm">
                    <i data-feather="download"></i> Export Semua
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filter Section (sama seperti index.blade.php) -->
            <div class="filter-section">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
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
                            <label for="account_code" class="form-label">Akun</label>
                            <select name="account_code" id="account_code" class="form-select">
                                <option value="">Pilih Akun</option>
                                <option value="all" selected>-- LIHAT SEMUA --</option>
                                @foreach($accountOptions as $code => $name)
                                    <option value="{{ $code }}" {{ request('account_code') == $code ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                value="{{ $dateFrom ? $dateFrom->format('Y-m-d') : '' }}">
                        </div>
                        
                        <div class="col-md-2">
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
                    
                    <div class="row mt-3">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" id="resetFilter" class="btn btn-secondary">
                                <i data-feather="refresh-ccw"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Info Periode -->
            <div class="alert alert-info">
                <strong>Tahun Buku:</strong> {{ $accountingBook->name }} 
                ({{ $accountingBook->start_date->format('d/m/Y') }} - {{ $accountingBook->end_date->format('d/m/Y') }})
                <br>
                <strong>Periode:</strong> 
                @if($dateFrom && $dateTo)
                    {{ $dateFrom->format('d/m/Y') }} - {{ $dateTo->format('d/m/Y') }}
                @else
                    Semua Periode
                @endif
            </div>
            
            <!-- Ledger untuk setiap akun -->
            @foreach($allLedgers as $ledger)
            <div class="account-section">
                <div class="account-header">
                    <div class="account-title">
                        {{ $ledger['account']['code'] }} - {{ $ledger['account']['name'] }}
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Saldo Awal:</strong>
                            <span class="{{ $ledger['initialBalance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                {{ number_format($ledger['initialBalance'], 2) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Saldo Akhir:</strong>
                            <span class="{{ $ledger['endingBalance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                {{ number_format($ledger['endingBalance'], 2) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Total:</strong> 
                            Debit: {{ number_format($ledger['totalDebit'], 2) }} | 
                            Kredit: {{ number_format($ledger['totalCredit'], 2) }}
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="12%">No. Jurnal</th>
                                <th width="12%">No. Bukti</th>
                                <th width="25%">Keterangan</th>
                                <th width="10%">Sub Kelas</th>
                                <th width="8%">Debit</th>
                                <th width="8%">Kredit</th>
                                <th width="10%">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Initial Balance -->
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>SALDO AWAL</strong></td>
                                <td></td>
                                <td style="text-align: right;">{{ $ledger['initialBalance'] > 0 && !$ledger['isCreditAccount'] ? number_format($ledger['initialBalance'], 2) : '' }}</td>
                                <td style="text-align: right;">{{ $ledger['initialBalance'] > 0 && $ledger['isCreditAccount'] ? number_format($ledger['initialBalance'], 2) : '' }}</td>
                                <td style="text-align: right; {{ $ledger['initialBalance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                    {{ number_format($ledger['initialBalance'], 2) }}
                                </td>
                            </tr>
                            
                            <!-- Transactions -->
                            @foreach($ledger['entries'] as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $entry['date']->format('d/m/Y') }}</td>
                                <td>{{ $entry['journal_number'] }}</td>
                                <td>{{ $entry['reference_number'] }}</td>
                                <td>{{ $entry['description'] }}</td>
                                <td>{{ $entry['sub_class'] }}</td>
                                <td style="text-align: right;">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '' }}</td>
                                <td style="text-align: right;">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '' }}</td>
                                <td style="text-align: right; {{ $entry['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                    {{ number_format($entry['balance'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                            
                            <!-- Totals -->
                            <tr>
                                <th colspan="6" class="text-end">TOTAL</th>
                                <th style="text-align: right;">{{ number_format($ledger['totalDebit'], 2) }}</th>
                                <th style="text-align: right;">{{ number_format($ledger['totalCredit'], 2) }}</th>
                                <th style="text-align: right; {{ $ledger['endingBalance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                    {{ number_format($ledger['endingBalance'], 2) }}
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    feather.replace();
    
    // Reset filter
    $('#resetFilter').click(function() {
        $('#filterForm')[0].reset();
        $('#book_id').val('{{ $books->first()->id ?? '' }}');
        window.location.href = "{{ route('financial.ledger.index') }}?book_id=" + $('#book_id').val() + "&account_code=all";
    });
    
    // Export All button handler - PERBAIKAN
    $('#exportAllBtn').click(function(e) {
        e.preventDefault();
        
        console.log('Export All button clicked'); // Log untuk debugging
        
        // Create a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('financial.ledger.export.all') }}";
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = "{{ csrf_token() }}";
        form.appendChild(csrfToken);
        
        // Add current filter values
        const bookId = document.createElement('input');
        bookId.type = 'hidden';
        bookId.name = 'book_id';
        bookId.value = $('#book_id').val();
        form.appendChild(bookId);
        
        const dateFrom = document.createElement('input');
        dateFrom.type = 'hidden';
        dateFrom.name = 'date_from';
        dateFrom.value = $('#date_from').val();
        form.appendChild(dateFrom);
        
        const dateTo = document.createElement('input');
        dateTo.type = 'hidden';
        dateTo.name = 'date_to';
        dateTo.value = $('#date_to').val();
        form.appendChild(dateTo);
        
        // Log nilai yang akan dikirim
        console.log('Exporting with parameters:', {
            book_id: bookId.value,
            date_from: dateFrom.value,
            date_to: dateTo.value
        });
        
        // Add form to body and submit
        document.body.appendChild(form);
        form.submit();
        
        // Clean up
        setTimeout(() => document.body.removeChild(form), 100);
    });
    
    // Handle form submission
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        const params = new URLSearchParams(window.location.search);
        params.set('book_id', $('#book_id').val());
        params.set('account_code', $('#account_code').val());
        params.set('date_from', $('#date_from').val());
        params.set('date_to', $('#date_to').val());
        window.location.href = "{{ route('financial.ledger.index') }}?" + params.toString();
    });
});
</script>
@endpush

@endsection
