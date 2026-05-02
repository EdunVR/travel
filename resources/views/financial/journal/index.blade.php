<style>
    /* Adjust modal positioning */
    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Adjust table columns */
    #entriesTable {
        width: 100% !important;
    }
    #entriesTable th:nth-child(1), 
    #entriesTable td:nth-child(1) { width: 5%; }  /* No */
    #entriesTable th:nth-child(2), 
    #entriesTable td:nth-child(2) { width: 15%; } /* No. Akun */
    #entriesTable th:nth-child(3), 
    #entriesTable td:nth-child(3) { width: 15%; } /* Nama Akun */
    #entriesTable th:nth-child(4), 
    #entriesTable td:nth-child(4) { width: 10%; } /* Sub Kelas */
    #entriesTable th:nth-child(5), 
    #entriesTable td:nth-child(5) { width: 10%; } /* Posting */
    #entriesTable th:nth-child(6), 
    #entriesTable td:nth-child(6) { width: 15%; min-width: 120px; } /* Debit */
    #entriesTable th:nth-child(7), 
    #entriesTable td:nth-child(7) { width: 15%; min-width: 120px; } /* Kredit */
    #entriesTable th:nth-child(8), 
    #entriesTable td:nth-child(8) { width: 10%; min-width: 100px; } /* Saldo */
    #entriesTable th:nth-child(9), 
    #entriesTable td:nth-child(9) { width: 5%; min-width: 80px; }  /* Aksi */
    .table-success {
        --bs-table-bg: #e8f5e9;
        --bs-table-striped-bg: #dcedc8;
    }
    .btn-success {
        background-color: #2e7d32;
        border-color: #2e7d32;
    }
    .bg-success {
        background-color: #2e7d32 !important;
    }
    .entry-row:hover {
        background-color: #f1f8e9 !important;
    }
    .feather {
        width: 16px;
        height: 16px;
        vertical-align: text-bottom;
    }

    /* Style untuk checkbox */
    .journal-checkbox {
        margin-left: 5px;
    }

    /* Style untuk tombol aksi */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
    small.text-muted {
        font-size: 0.75rem;
        display: block;
        line-height: 1.2;
    }

    .journal-checkbox {
        margin: 0 auto;
        display: block;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    #selectAllCheckbox {
        cursor: pointer;
        width: 16px;
        height: 16px;
    }

    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .ui-autocomplete {
        position: absolute;
        z-index: 1051 !important;
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .ui-menu-item {
        padding: 8px 12px;
        font-size: 0.9rem;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    
    .ui-menu-item:hover {
        background-color: #f8f9fa;
    }
    
    .ui-state-active {
        background-color: #e9ecef !important;
        color: #495057;
    }
    .debit, .credit {
        text-align: right;
        padding-right: 8px;
        font-family: monospace; /* Membuat angka lebih konsisten */
    }

    /* Style saat input aktif */
    .debit:focus, .credit:focus {
        background-color: #fff8e1;
        border-color: #ffc107;
    }

</style>


@extends('app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i data-feather="file-text"></i> Daftar Jurnal Transaksi
            </h5>
            <div>
                <button id="openCreateModal" class="btn btn-success btn-sm">
                    <i data-feather="plus"></i> Tambah Jurnal
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <div class="bg-light p-3 rounded mb-3">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="book_id" class="form-label">Tahun Buku</label>
                            <select name="book_id" id="book_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }} ({{ $book->start_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="account_code" class="form-label">Kode Akun</label>
                            <select name="account_code" id="account_code" class="form-select">
                                <option value="">Semua Akun</option>
                                @foreach($accountOptions as $code => $name)
                                    <option value="{{ $code }}" {{ request('account_code') == $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Pencarian</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="No. Jurnal/No. Bukti/Deskripsi">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Total Debit Kredit -->
            <div class="alert alert-info d-flex justify-content-between">
                <strong>Total Debit: {{ number_format($totals->total_debit ?? 0, 2) }}</strong>
                <strong>Total Kredit: {{ number_format($totals->total_credit ?? 0, 2) }}</strong>
                <strong>Selisih: {{ number_format(($totals->total_debit ?? 0) - ($totals->total_credit ?? 0), 2) }}</strong>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th width="50">No</th>
                            <th><i data-feather="hash"></i> No. Jurnal</th>
                            <th><i data-feather="calendar"></i> Tanggal</th>
                            <th>No. Bukti</th>
                            <th>Keterangan</th>
                            <th>Detail Akun</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Input By</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($journals as $journal)
                        <tr>
                            <td>
                                <input type="checkbox" class="journal-checkbox" value="{{ $journal->id }}">
                            </td>
                            <td>{{ $loop->iteration + ($journals->currentPage() - 1) * $journals->perPage() }}</td>
                            <td>{{ $journal->journal_number }}</td>
                            <td>{{ $journal->transaction_date->format('d/m/Y') }}</td>
                            <td>{{ $journal->reference_number }}</td>
                            <td>{{ $journal->description }}</td>
                            <td style="text-align: left;">
                                @foreach($journal->entries as $entry)
                                <div>
                                    {{ $entry->account_code }} - {{ $accountNames[$entry->account_code] ?? '' }}
                                    @if($entry->subClass)
                                    <small>({{ $entry->subClass->name }})</small>
                                    @endif
                                </div>
                                @endforeach
                            </td>
                            <td style="text-align: right;">
                                @foreach($journal->entries as $entry)
                                <div>{{ number_format($entry->debit, 2) }}</div>
                                @endforeach
                            </td>
                            <td style="text-align: right;">
                                @foreach($journal->entries as $entry)
                                <div>{{ number_format($entry->credit, 2) }}</div>
                                @endforeach
                            </td>
                            <td>{{ $journal->creator->name }}</td>
                            <td>
                                @if($journal->is_validated)
                                    <span class="badge bg-success">Valid</span>
                                    <small class="d-block text-muted">
                                        {{ $journal->validated_at->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                            <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @if(!$journal->is_validated)
                                    <button class="btn btn-success validate-btn" data-id="{{ $journal->id }}" title="Validasi">
                                        <i data-feather="check"></i>
                                    </button>
                                    <button class="btn btn-primary edit-btn" data-id="{{ $journal->id }}" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                @endif
                                <button class="btn btn-danger delete-btn" data-id="{{ $journal->id }}" title="Hapus">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button id="validateSelectedBtn" class="btn btn-success btn-sm me-2" disabled>
                                <i data-feather="check"></i> Validasi yang Dipilih
                            </button>
                            <button id="deleteSelectedBtn" class="btn btn-danger btn-sm me-2" disabled>
                                <i data-feather="trash-2"></i> Hapus yang Dipilih
                            </button>
                            <button id="resetFilter" class="btn btn-secondary btn-sm">
                                <i data-feather="refresh-ccw"></i> Reset Filter
                            </button>
                        </div>
                        <div>
                            {{ $journals->links() }}
                        </div>
                    </div>
        </div>
    </div>
</div>

<!-- Create/Edit Journal Modal -->
<div class="modal fade" id="journalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i data-feather="plus-circle"></i> <span id="modalAction">Tambah</span> Jurnal Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-x: auto;">
                <form id="journalForm">
                    @csrf
                    <input type="hidden" id="journalId" name="id">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="accounting_book_id" class="form-label">
                                <i data-feather="book"></i> Tahun Buku *
                            </label>
                            <select name="accounting_book_id" id="accounting_book_id" class="form-select" required>
                                <option value="">Pilih Tahun Buku</option>
                                @foreach($booksActive as $book)
                                    <option value="{{ $book->id }}">{{ $book->name }} ({{ $book->start_date->format('Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="transaction_date" class="form-label">
                                <i data-feather="calendar"></i> Tanggal *
                            </label>
                            <input type="date" name="transaction_date" id="transaction_date" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="reference_number" class="form-label">
                                <i data-feather="file"></i> No. Bukti
                            </label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Arus Kas</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_cash_flow" id="is_cash_flow" value="1" checked>
                                <label class="form-check-label" for="is_cash_flow">
                                    Termasuk Arus Kas
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Keterangan *</label>
                        <textarea name="description" id="description" class="form-control" rows="2" required></textarea>
                    </div>
                    
                    <!-- Entries Table -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-end mb-2">
                            <div class="me-3">
                                <strong>Total Debit: </strong>
                                <span id="totalDebit">0.00</span>
                            </div>
                            <div>
                                <strong>Total Kredit: </strong>
                                <span id="totalCredit">0.00</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span id="balanceStatus" class="badge"></span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="entriesTable" style="width:100%">
                                <thead class="table-success">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">No. Akun *</th>
                                        <th width="15%">Nama Akun</th>
                                        <th width="15%">Sub Kelas</th>
                                        <th width="10%">Posting *</th>
                                        <th width="15%">Debit</th>
                                        <th width="15%">Kredit</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be added dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i data-feather="x"></i> Batal
                </button>
                <button type="button" id="saveJournalBtn" class="btn btn-success">
                    <i data-feather="save"></i> Simpan Jurnal
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

$(document).ready(function() {
    feather.replace();

    let isEditMode = false;
    let currentJournalId = null;
    baseUrl = window.baseUrl;

    function openCreateModal() {
        isEditMode = false;
        currentJournalId = null;
        
        $('#modalTitle span').text('Tambah');
        $('#journalForm')[0].reset();
        $('#journalId').val('');
        $('#entriesTable tbody').html('');
        
        // Generate reference number
        $.get("{{ route('financial.journal.generate-reference') }}", function(response) {
            $('#reference_number').val(response.reference_number);
        });
        
        // Tambahkan 2 baris kosong
        addEntryRow(0);
        addEntryRow(1);
        
        $('#journalModal').modal('show');
    }

    // Fungsi untuk membuka modal dalam mode edit
    function openEditModal(journalId) {
        console.log('[DEBUG] Opening edit modal for journal ID:', journalId);
        isEditMode = true;
        currentJournalId = journalId;
        
        $('#modalTitle span').text('Edit');
        $('#journalForm')[0].reset();
        $('#entriesTable tbody').empty(); // Gunakan empty() bukan html('')
        
        $.get(`${baseUrl}/financial/journal/${journalId}/edit`, function(response) {
            console.log('[DEBUG] Received edit data:', response);
            
            // Isi form header
            $('#journalId').val(response.id);
            $('#accounting_book_id').val(response.accounting_book_id);
            $('#transaction_date').val(response.transaction_date);
            $('#reference_number').val(response.reference_number);
            $('#description').val(response.description);
            $('#is_cash_flow').prop('checked', response.is_cash_flow);
            
            // Kosongkan tabel sebelum menambahkan row
            $('#entriesTable tbody').empty();
            
            // Tambahkan row untuk setiap entry
            response.entries.forEach((entry, index) => {
                console.log(`[DEBUG] Processing entry ${index}:`, entry);
                
                const formattedDebit = entry.debit > 0 ? formatNumber(entry.debit) : '0';
                const formattedCredit = entry.credit > 0 ? formatNumber(entry.credit) : '0';
                
                console.log(`[DEBUG] Formatted values - Debit: ${formattedDebit}, Credit: ${formattedCredit}`);
                
                const rowData = {
                    account_code: entry.account_code,
                    account: entry.account,
                    sub_class_id: entry.sub_class_id,
                    posting_type: entry.posting_type,
                    debit: formattedDebit,
                    credit: formattedCredit,
                    amount: entry.amount
                };
                
                console.log(`[DEBUG] Prepared row data for ${index}:`, rowData);
                addEntryRow(index, rowData);
            });
            
            // Perbaikan untuk warning ARIA
            $('#journalModal').removeAttr('aria-hidden');
            $('#journalModal').modal('show');
            
        }).fail(function(xhr) {
            console.error('[ERROR] Failed to load journal data:', xhr.responseText);
            Swal.fire('Error!', 'Gagal memuat data jurnal', 'error');
        });
    }

    // Ganti fungsi initAutocomplete dengan yang lebih responsif
    function initAutocomplete() {
        $('.account-code-autocomplete').autocomplete({
            source: function(request, response) {
                $.get("{{ route('financial.journal.search-accounts') }}", {
                    term: request.term
                }, function(data) {
                    response(data);
                });
            },
            minLength: 1,
            select: function(event, ui) {
                const row = $(this).closest('tr');
                $(this).val(ui.item.label);
                row.find('.account-code').val(ui.item.code);
                row.find('.account-name').text(ui.item.name);
                row.data('account-type', ui.item.type);
                
                // Update balance immediately
                updateBalanceDisplay(row, ui.item.code);
                
                return false;
            },
            change: function(event, ui) {
                if (!ui.item) {
                    $(this).val('');
                    $(this).closest('tr').find('.account-code').val('');
                    $(this).closest('tr').find('.account-name').text('-');
                    $(this).closest('tr').find('.debit-balance, .credit-balance').text('Saldo: 0.00');
                }
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>")
                .append(`<div>${item.label}</div>`)
                .appendTo(ul);
        };
    }

    // Fungsi baru untuk langsung menampilkan balance
    function updateBalanceDisplay(row, accountCode) {
        const bookId = $('#accounting_book_id').val();
        const date = $('#transaction_date').val() || new Date().toISOString().split('T')[0];
        
        if (!bookId) {
            row.find('.debit-balance').text('Pilih tahun buku terlebih dahulu');
            row.find('.credit-balance').text('Pilih tahun buku terlebih dahulu');
            return;
        }
        
        $.ajax({
            url: "{{ route('financial.journal.account.balance') }}",
            type: 'GET',
            data: {
                account_code: accountCode,
                book_id: bookId,
                date: date
            },
            success: function(response) {
                if (response.success) {
                    row.find('.debit-balance').text(`Saldo Debit: ${response.formatted_debit}`);
                    row.find('.credit-balance').text(`Saldo Kredit: ${response.formatted_credit}`);
                    
                    // Enable/disable debit/credit based on account type
                    const postingType = row.find('.posting-type').val();
                    if (postingType) {
                        row.find('.posting-type').trigger('change');
                    }
                }
            },
            error: function() {
                row.find('.debit-balance').text('Gagal memuat saldo');
                row.find('.credit-balance').text('Gagal memuat saldo');
            }
        });
    }

    function addEntryRow(index, entryData = null) {
        console.log(`[DEBUG] Adding row ${index} with data:`, entryData);
        
        const accountDisplay = entryData?.account_code ? 
            `${entryData.account_code} - ${entryData.account?.name || ''}` : '';
        
        const debitValue = entryData?.debit ? entryData.debit : '0';
        const creditValue = entryData?.credit ? entryData.credit : '0';
        
        console.log(`[DEBUG] Final values for row ${index}: Debit=${debitValue}, Credit=${creditValue}`);
        
        const row = `
            <tr class="entry-row" data-index="${index}">
                <td>${index + 1}</td>
                <td>
                    <input type="text" class="form-control account-code-autocomplete" 
                        value="${accountDisplay}" required>
                    <input type="hidden" name="entries[${index}][account_code]" 
                        class="account-code" value="${entryData?.account_code || ''}">
                </td>
                <td class="account-name">${entryData?.account?.name || '-'}</td>
                <td>
                    <select name="entries[${index}][sub_class_id]" class="form-select">
                        <option value="">Pilih Sub Kelas</option>
                        @foreach($subClasses as $sc)
                            <option value="{{ $sc->id }}" ${entryData?.sub_class_id == {{ $sc->id }} ? 'selected' : ''}>
                                {{ $sc->code }} - {{ $sc->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="entries[${index}][posting_type]" class="form-select posting-type" required>
                        <option value="">Pilih</option>
                        <option value="increase" ${entryData?.posting_type === 'increase' ? 'selected' : ''}>Menambah</option>
                        <option value="decrease" ${entryData?.posting_type === 'decrease' ? 'selected' : ''}>Mengurangi</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="entries[${index}][debit]" class="form-control debit" 
                        value="${debitValue}" readonly>
                    <div class="text-muted debit-balance" style="font-size: 0.85rem; margin-top: 5px;">
                        ${entryData?.account_code ? 'Saldo Debit: 0.00' : ''}
                    </div>
                </td>
                <td>
                    <input type="text" name="entries[${index}][credit]" class="form-control credit" 
                        value="${creditValue}" readonly>
                    <div class="text-muted credit-balance" style="font-size: 0.85rem; margin-top: 5px;">
                        ${entryData?.account_code ? 'Saldo Kredit: 0.00' : ''}
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary add-entry" title="Tambah Baris">
                            <i data-feather="plus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-entry" title="Hapus">
                            <i data-feather="trash-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary move-up" title="Geser ke atas">
                            <i data-feather="arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary move-down" title="Geser ke bawah">
                            <i data-feather="arrow-down"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        $('#entriesTable tbody').append(row);
        feather.replace();
        initAutocomplete();
        
        if (entryData?.account_code) {
            const rowElement = $('#entriesTable tbody tr').last();
            
            // Update balance display TANPA mengubah nilai debit/credit
            $.ajax({
                url: "{{ route('financial.journal.account.balance') }}",
                type: 'GET',
                data: {
                    account_code: entryData.account_code,
                    book_id: $('#accounting_book_id').val(),
                    date: $('#transaction_date').val() || new Date().toISOString().split('T')[0]
                },
                success: function(response) {
                    if (response.success) {
                        rowElement.find('.debit-balance').text(`Saldo Debit: ${response.formatted_debit}`);
                        rowElement.find('.credit-balance').text(`Saldo Kredit: ${response.formatted_credit}`);
                    }
                },
                error: function() {
                    rowElement.find('.debit-balance').text('Gagal memuat saldo');
                    rowElement.find('.credit-balance').text('Gagal memuat saldo');
                }
            });
        }
    }

    //add-entry button click event
    $(document).on('click', '.add-entry', function() {
        const index = $(this).closest('tr').data('index');
        addEntryRow(index + 1);
    });

    // Handle tombol edit
    $(document).on('click', '.edit-btn', function() {
        const journalId = $(this).data('id');
        openEditModal(journalId);
    });

    // Handle tombol create
    $('#openCreateModal').click(openCreateModal);

    // Handle submit form
    $('#saveJournalBtn').click(function() {
        const formData = {
            accounting_book_id: $('#accounting_book_id').val(),
            transaction_date: $('#transaction_date').val(),
            reference_number: $('#reference_number').val(),
            description: $('#description').val(),
            is_cash_flow: $('#is_cash_flow').is(':checked') ? 1 : 0,
            entries: collectEntriesData(),
            _method: isEditMode ? 'PUT' : 'POST' // Tambahkan ini untuk update
        };

        // Lakukan AJAX request
        const url = isEditMode 
            ? `${baseUrl}/financial/journal/${currentJournalId}`
            : `${baseUrl}/financial/journal`;
        
        $.ajax({
            url: url,
            type: 'POST', // Selalu gunakan POST karena kita menggunakan _method untuk PUT
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.redirect;
                });
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menyimpan jurnal';
                if (xhr.status === 422) {
                    message = Object.values(xhr.responseJSON.errors).join('\n');
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });


    // Update row actions when adding new rows
    function updateRowActions() {
        $('.entry-row').each(function(index) {
            const rowCount = $('.entry-row').length;
            const $row = $(this);
            
            // Enable/disable move up button
            if (index === 0) {
                $row.find('.move-up').prop('disabled', true);
            } else {
                $row.find('.move-up').prop('disabled', false);
            }
            
            // Enable/disable move down button
            if (index === rowCount - 1) {
                $row.find('.move-down').prop('disabled', true);
            } else {
                $row.find('.move-down').prop('disabled', false);
            }
            
            // Enable/disable remove button
            $row.find('.remove-entry').prop('disabled', rowCount <= 2);
        });
    }

    

    updateRowActions();

    // Move row up
    $(document).on('click', '.move-up', function() {
        const row = $(this).closest('tr');
        const prevRow = row.prev();
        
        if (prevRow.length) {
            row.insertBefore(prevRow);
            renumberRows();
            calculateTotals();
        }
    });

    // Move row down
    $(document).on('click', '.move-down', function() {
        const row = $(this).closest('tr');
        const nextRow = row.next();
        
        if (nextRow.length) {
            row.insertAfter(nextRow);
            renumberRows();
            calculateTotals();
        }
    });


    const accounts = @json($accounts);
    let entryCount = 2; // Start from 2 because we have 2 initial rows
    
    
    // Remove entry row
    $(document).on('click', '.remove-entry', function() {
        if ($('.entry-row').length > 2) {
            $(this).closest('tr').remove();
            renumberRows();
            
            // Disable remove buttons if only 2 rows left
            if ($('.entry-row').length <= 2) {
                $('.remove-entry').prop('disabled', true);
            }
            
            calculateTotals();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Jurnal harus memiliki minimal 2 entri'
            });
        }
    });
    
    // Renumber rows after add/remove
    function renumberRows() {
        $('.entry-row').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('td:first').text(index + 1);
            
            // Update input names
            $(this).find('select, input').each(function() {
                const name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
                $(this).attr('name', name);
            });
        });
        entryCount = $('.entry-row').length;
    }
    
    // Get account name when account code selected
    $(document).on('change', '.account-code', function() {
        const row = $(this).closest('tr');
        const accountCode = $(this).val();
        const accountType = $(this).find('option:selected').data('type');
        
        // Find account in accounts data
        const account = findAccountByCode(accountCode);
        
        if (account) {
            row.find('.account-name').text(account.name);
            
            // Update account type for this row
            row.data('account-type', account.type);
            
            // Get current balance
            getAccountBalance(row, accountCode);
        } else {
            row.find('.account-name').text('-');
        }
    });
    
    // Find account by code
    function findAccountByCode(code) {
        for (const group of accounts) {
            if (group.code === code) {
                return group;
            }
            if (group.children) {
                const found = findInChildren(group.children, code);
                if (found) return found;
            }
        }
        return null;
    }
    
    function findInChildren(children, code) {
        for (const child of children) {
            if (child.code === code) {
                return child;
            }
            if (child.children) {
                const found = findInChildren(child.children, code);
                if (found) return found;
            }
        }
        return null;
    }
    
    // Get account balance via AJAX
    function getAccountBalance(row, accountCode) {
        const bookId = $('#accounting_book_id').val();
        const date = $('#transaction_date').val();
        
        if (!bookId || !date) {
            return;
        }
        
        $.ajax({
            url: "{{ route('financial.journal.account.balance') }}",
            type: 'GET',
            data: {
                account_code: accountCode,
                book_id: bookId,
                date: date
            },
            success: function(response) {
                if (response.success) {
                    const isCreditAccount = response.is_credit_account;
                    const totalDebit = response.formatted_debit;
                    const totalCredit = response.formatted_credit;
                    
                    row.find('.debit-balance').text(`Saldo: ${totalDebit}`);
                    row.find('.credit-balance').text(`Saldo: ${totalCredit}`);
                    
                    // Update debit/credit fields based on account type
                    if (isCreditAccount) {
                        row.find('.debit').attr('placeholder', 'Aktif jika mengurangi');
                        row.find('.credit').attr('placeholder', 'Aktif jika menambah');
                    } else {
                        row.find('.debit').attr('placeholder', 'Aktif jika menambah');
                        row.find('.credit').attr('placeholder', 'Aktif jika mengurangi');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error getting account balance:', xhr.responseText);
            }
        });
    }
    
    // When posting type changes, update debit/credit fields
    $(document).on('change', '.posting-type', function() {
        const row = $(this).closest('tr');
        const postingType = $(this).val();
        const accountType = row.data('account-type');
        const isCreditAccount = ['liability', 'equity', 'revenue'].includes(accountType);
        
        // Reset nilai dan readonly state
        row.find('.debit').val('0').prop('readonly', true);
        row.find('.credit').val('0').prop('readonly', true);
        
        if (postingType === 'increase') {
            if (isCreditAccount) {
                row.find('.credit').prop('readonly', false);
            } else {
                row.find('.debit').prop('readonly', false);
            }
        } else if (postingType === 'decrease') {
            if (isCreditAccount) {
                row.find('.debit').prop('readonly', false);
            } else {
                row.find('.credit').prop('readonly', false);
            }
        }
        
        // Jika dalam mode edit, pertahankan nilai asli
        if (isEditMode) {
            const debitValue = row.find('.debit').val();
            const creditValue = row.find('.credit').val();
            
            if (debitValue !== '0') {
                row.find('.debit').val(debitValue);
            }
            if (creditValue !== '0') {
                row.find('.credit').val(creditValue);
            }
        }
    });
    
    
    
    // Calculate totals
    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        
        $('.entry-row').each(function() {
            const debitValue = $(this).find('.debit').val() || '0';
            const creditValue = $(this).find('.credit').val() || '0';
            
            const debit = parseInt(debitValue.replace(/\./g, '')) || 0;
            const credit = parseInt(creditValue.replace(/\./g, '')) || 0;
            
            totalDebit += debit;
            totalCredit += credit;
        });
        
        // Format tampilan total
        $('#totalDebit').text(new Intl.NumberFormat('id-ID').format(totalDebit));
        $('#totalCredit').text(new Intl.NumberFormat('id-ID').format(totalCredit));
        
        // Update balance status
        const diff = Math.abs(totalDebit - totalCredit);
        const balanceStatus = $('#balanceStatus');
        
        if (diff < 0.01) {
            balanceStatus.removeClass('bg-danger').addClass('bg-success').text('Balance');
        } else {
            balanceStatus.removeClass('bg-success').addClass('bg-danger')
                .text(`Tidak Balance (Selisih: ${new Intl.NumberFormat('id-ID').format(diff)})`);
        }
    }
    
    // When book or date changes, update all account balances
    $('#accounting_book_id, #transaction_date').change(function() {
        $('.account-code').each(function() {
            if ($(this).val()) {
                getAccountBalance($(this).closest('tr'), $(this).val());
            }
        });
    });

    // Validasi Jurnal
    $(document).on('click', '.validate-btn', function() {
        const journalId = $(this).data('id');
        
        Swal.fire({
            title: 'Validasi Jurnal?',
            text: "Anda yakin ingin memvalidasi jurnal ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Validasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`${baseUrl}/financial/journal/${journalId}/validate`, {
                    _token: '{{ csrf_token() }}',
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    }
                }).fail(function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                });
            }
        });
    });

    // Hapus Jurnal
    $(document).on('click', '.delete-btn', function() {
        const journalId = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Jurnal?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/financial/journal/${journalId}`,
                    type: 'DELETE',
                    data: { 
                        _token: '{{ csrf_token() }}' 
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });

    // Fungsi untuk validasi jurnal terpilih
    $('#validateSelectedBtn').click(function() {
        const selectedIds = $('.journal-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu jurnal', 'info');
            return;
        }

        Swal.fire({
            title: 'Validasi Jurnal Terpilih?',
            text: `Anda akan memvalidasi ${selectedIds.length} jurnal`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Validasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("financial.journal.validate-selected") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });

    // Hapus yang Dipilih
    $('#deleteSelectedBtn').click(function() {
        const selectedIds = $('.journal-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu jurnal', 'info');
            return;
        }
        
        Swal.fire({
            title: 'Hapus Jurnal Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} jurnal`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '${baseUrl}/financial/journal/delete-selected',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });
    });

    // Fungsi untuk mengumpulkan data entries
    function collectEntriesData() {
        const entries = [];
        
        $('.entry-row').each(function(index) {
            const row = $(this);
            const accountCode = row.find('.account-code').val();
            const postingType = row.find('.posting-type').val();
            
            // Ambil nilai dan unformat
            const debitValue = row.find('.debit').val() || '0';
            const creditValue = row.find('.credit').val() || '0';
            const debit = parseInt(debitValue.replace(/\./g, '')) || 0;
            const credit = parseInt(creditValue.replace(/\./g, '')) || 0;
            const amount = debit > 0 ? debit : credit;
            
            if (accountCode && postingType && amount > 0) {
                entries.push({
                    account_code: accountCode,
                    sub_class_id: row.find('[name*="[sub_class_id]"]').val() || null,
                    posting_type: postingType,
                    amount: amount,
                    debit: debit,
                    credit: credit
                });
            }
        });
        
        return entries;
    }

    $(document).on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
        $('#journalModal').modal('hide');
    });

    // Select All Functionality
    $('#selectAllCheckbox').change(function() {
        $('.journal-checkbox').prop('checked', $(this).prop('checked'));
        
    });


    // Fungsi toggle tombol
    function toggleActionButtons() {
        const checkedCount = $('.journal-checkbox:checked').length;
        $('#validateSelectedBtn').prop('disabled', checkedCount === 0);
        $('#deleteSelectedBtn').prop('disabled', checkedCount === 0);
        
        // Update teks tombol
        if (checkedCount > 0) {
            $('#validateSelectedBtn').html(`<i data-feather="check"></i> Validasi (${checkedCount})`);
            $('#deleteSelectedBtn').html(`<i data-feather="trash-2"></i> Hapus (${checkedCount})`);
        } else {
            $('#validateSelectedBtn').html(`<i data-feather="check"></i> Validasi yang Dipilih`);
            $('#deleteSelectedBtn').html(`<i data-feather="trash-2"></i> Hapus yang Dipilih`);
        }
        feather.replace();
    }

    // Panggil fungsi saat checkbox berubah
    $(document).on('change', '.journal-checkbox, #selectAllCheckbox', function() {
        toggleActionButtons();
    });

    // Handle delete selected
    $('#deleteSelectedBtn').click(function() {
        const selectedIds = $('.journal-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Pilih minimal satu jurnal', 'info');
            return;
        }

        Swal.fire({
            title: 'Hapus Jurnal Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} jurnal`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("financial.journal.delete-selected") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
    // Fungsi untuk memformat angka dengan separator ribuan
    function formatNumber(number) {
        // Jika number adalah string, bersihkan dulu
        if (typeof number === 'string') {
            number = parseFloat(number.replace(/[^\d]/g, '')) || 0;
        }
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi untuk mengembalikan ke format number asli (tanpa separator)
    function unformatNumber(formattedNumber) {
        return parseFloat(formattedNumber.replace(/\./g, ''));
    }

    // Fungsi untuk memformat input saat kehilangan fokus
    function formatInputOnBlur(inputElement) {
        const value = inputElement.val();
        if (value && value !== '') {
            const number = unformatNumber(value);
            inputElement.val(formatNumber(number));
        }
    }

    // Fungsi untuk meng-unformat input saat mendapat fokus
    function unformatInputOnFocus(inputElement) {
        const value = inputElement.val();
        if (value && value !== '') {
            inputElement.val(unformatNumber(value));
        }
    }

    // Fungsi untuk memformat angka secara realtime
    function formatNumberRealtime(input) {
        // Simpan posisi cursor
        const cursorPosition = input.selectionStart;
        const originalValue = input.value;
        
        // Hapus semua karakter non-digit
        let numericValue = originalValue.replace(/[^\d]/g, '');
        
        // Jika kosong, set ke 0
        if (numericValue === '') numericValue = '0';
        
        // Konversi ke number
        const numberValue = parseInt(numericValue);
        
        // Format dengan separator ribuan
        const formattedValue = new Intl.NumberFormat('id-ID').format(numberValue);
        
        // Update nilai input
        input.value = formattedValue;
        
        // Hitung posisi cursor baru
        const addedChars = formattedValue.length - originalValue.length;
        const newCursorPosition = cursorPosition + addedChars;
        
        // Set posisi cursor (jika input adalah text)
        if (input.type === 'text') {
            input.setSelectionRange(newCursorPosition, newCursorPosition);
        }
        
        return numberValue;
    }

    // Event handler untuk input realtime
    $(document).on('input', '.debit, .credit', function(e) {
        // Format angka secara realtime
        const rawValue = formatNumberRealtime(this);
        
        // Hitung total
        calculateTotals();
    });

    // Fungsi reset filter
    $('#resetFilter').click(function(e) {
        e.preventDefault();
        
        // Reset nilai form ke default
        $('#filterForm')[0].reset();
        
        // Hapus semua parameter query dari URL
        const cleanUrl = window.location.pathname;
        
        // Redirect ke URL bersih tanpa parameter query
        window.location.href = cleanUrl;
    });

    // Pastikan form tidak melakukan submit biasa
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        
        // Dapatkan semua nilai form
        const formData = $(this).serialize();
        
        // Bangun URL baru dengan parameter query
        const newUrl = `${window.location.pathname}?${formData}`;
        
        // Redirect ke URL baru
        window.location.href = newUrl;
    });

});
</script>
@endpush
