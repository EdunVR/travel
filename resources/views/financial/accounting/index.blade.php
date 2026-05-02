<style>
    /* Accounting Styles */
    .account-type-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    .journal-entry {
        transition: all 0.3s;
    }

    .journal-entry:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .account-select {
        width: 100%;
    }

    #accountsTable tr {
        cursor: pointer;
    }

    #accountsTable tr:hover {
        background-color: #f8f9fa;
    }

    .accounting-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .section-title {
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 20px;
        color: #4e73df;
    }
    
    .table-container {
        max-height: 500px;
        overflow-y: auto;
    }

    .feather {
        width: 16px;
        height: 16px;
        vertical-align: middle;
    }

    .btn .feather {
        margin-right: 3px;
    }
</style>

@extends('app')

@section('title', 'Manajemen Akuntansi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Chart of Accounts -->
        <div class="col-md-4">
            <div class="accounting-section">
                <h5 class="section-title">
                    <i class="fas fa-list-alt"></i> Chart of Accounts
                </h5>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="accountsTable">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Akun</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accounts as $account)
                                    <tr>
                                        <td>{{ $account['code'] }}</td>
                                        <td>{{ $account['name'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ [
                                                'asset' => 'primary',
                                                'liability' => 'warning',
                                                'equity' => 'success',
                                                'revenue' => 'info',
                                                'expense' => 'danger'
                                            ][$account['type']] }}">
                                                {{ ucfirst($account['type']) }}
                                            </span>
                                        </td>  
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journal Entry -->
        <div class="col-md-8">
            <div class="accounting-section">
                <h5 class="section-title">
                    <i class="fas fa-book"></i> Journal Entry
                </h5>
                <!-- Konten Journal -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Entri Jurnal</h6>
                    </div>
                    <div class="card-body">
                        <form id="journalForm" method="POST" action="{{ route('financial.accounting.storeJournal') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <input type="text" name="description" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="journal-entries">
                                <div class="entry row mb-3">
                                    <div class="col-md-4">
                                    <select name="entries[0][account_id]" class="form-control account-select" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account['code'] }}">{{ $account['code'] }} - {{ $account['name'] }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="entries[0][debit]" class="form-control debit" placeholder="Debit" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="entries[0][credit]" class="form-control credit" placeholder="Credit" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="entries[0][memo]" class="form-control" placeholder="Memo">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="addEntry" class="btn btn-sm btn-secondary">
                                        <i data-feather="plus"></i> Tambah Entri
                                    </button>
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i data-feather="save"></i> Posting Jurnal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="accounting-section">
                <h5 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i> Journal List
                </h5>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Filter Jurnal</h6>
                    </div>
                    <div class="card-body">
                        <form id="journalFilterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tipe Transaksi</label>
                                        <select name="type" class="form-control">
                                            <option value="">Semua</option>
                                            <option value="manual">Manual</option>
                                            <option value="penjualan">Penjualan</option>
                                            <option value="pembelian">Pembelian</option>
                                            <option value="payroll">Payroll</option>
                                            <option value="inventory">Inventory</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Dari Tanggal</label>
                                        <input type="date" name="date_from" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sampai Tanggal</label>
                                        <input type="date" name="date_to" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Akun</label>
                                        <select name="entries[0][account_id]" class="form-control account-select" required>
                                            <option value="">Semua Akun</option>
                                            @foreach($accounts as $account)
                                            <option value="{{ $account['code'] }}">{{ $account['code'] }} - {{ $account['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </form>
                    </div>
                </div>
                <!-- Konten Journal List -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Jurnal</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="journalsTable">
                                <thead>
                                    <tr>
                                        <th>No. Ref</th>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Keterangan</th>
                                        <th>Total Debit</th>
                                        <th>Total Kredit</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($journals as $journal)
                                    <tr>
                                        <td>{{ $journal->reference }}</td>
                                        <td>{{ $journal->date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $journal->transaction_type === 'manual' ? 'primary' : 
                                                ($journal->transaction_type === 'penjualan' ? 'success' :
                                                ($journal->transaction_type === 'pembelian' ? 'warning' : 'info'))
                                            }}">
                                                {{ ucfirst($journal->transaction_type ?? 'manual') }}
                                            </span>
                                        </td>
                                        <td>{{ $journal->description }}</td>
                                        <td>{{ number_format($journal->entries->sum('debit'), 0) }}</td>
                                        <td>{{ number_format($journal->entries->sum('credit'), 0) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="#" class="btn btn-info show-journal" 
                                                    data-url="{{ route('financial.journals.show', $journal->id) }}">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('financial.journals.edit_journal', $journal->id) }}" 
                                                    class="btn btn-warning">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('financial.journals.destroy_journal', $journal->id) }}" 
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-icon btn-sm btn-danger delete-journal-btn" 
                                                            title="Hapus" 
                                                            data-id="{{ $journal->id }}">
                                                        <i data-feather="trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $journals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Akun Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('financial.accounting.storeAccount') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Akun</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Akun</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Akun</label>
                        <select name="type" class="form-control" required>
                            <option value="asset">Aset</option>
                            <option value="liability">Kewajiban</option>
                            <option value="equity">Ekuitas</option>
                            <option value="revenue">Pendapatan</option>
                            <option value="expense">Beban</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Induk Akun</label>
                        <select name="entries[0][account_id]" class="form-control account-select" required>
                            <option value="">- Tidak Ada -</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account['code'] }}">{{ $account['code'] }} - {{ $account['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Jurnal -->
@include('financial.accounting.journals.show')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    $(document).ready(function() {
        // Add journal entry
        let entryCount = 1;
        $('#addEntry').click(function() {
            const newEntry = $(`<div class="entry row mb-3">
                <div class="col-md-4">
                    <select name="entries[${entryCount}][account_id]" class="form-control account-select" required>
                        <option value="">Pilih Akun</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account['code'] }}">{{ $account['code'] }} - {{ $account['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="entries[${entryCount}][debit]" class="form-control debit" placeholder="Debit" min="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" name="entries[${entryCount}][credit]" class="form-control credit" placeholder="Credit" min="0" step="0.01">
                </div>
                <div class="col-md-3">
                    <input type="text" name="entries[${entryCount}][memo]" class="form-control" placeholder="Memo">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-entry"><i class="fa fa-trash"></i></button>
                </div>
            </div>`);
            
            $('.journal-entries').append(newEntry);
            entryCount++;
        });

        // Remove journal entry
        $(document).on('click', '.remove-entry', function() {
            if($('.entry').length > 1) {
                $(this).closest('.entry').remove();
            }
        });

        // Validate debit/credit
        $(document).on('change', '.debit, .credit', function() {
            const entry = $(this).closest('.entry');
            const debit = entry.find('.debit').val();
            const credit = entry.find('.credit').val();
            
            if(debit && credit) {
                if(parseFloat(debit) > 0 && parseFloat(credit) > 0) {
                    entry.find('.credit').val('');
                }
            }
        });

        $(document).on('submit', '#journalForm', function(e) {
            // Validate at least 2 entries
            if ($('.entry').length < 2) {
                alert('Minimal harus ada 2 entri jurnal');
                e.preventDefault();
                return;
            }

            // Validate debit credit balance
            let totalDebit = 0;
            let totalCredit = 0;

            $('.entry').each(function() {
                const debit = parseFloat($(this).find('.debit').val()) || 0;
                const credit = parseFloat($(this).find('.credit').val()) || 0;
                
                if (debit > 0 && credit > 0) {
                    alert('Setiap entri hanya boleh memiliki debit ATAU credit');
                    e.preventDefault();
                    return false;
                }

                totalDebit += debit;
                totalCredit += credit;
            });

            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                alert('Total debit dan credit harus balance\nDebit: ' + totalDebit + '\nCredit: ' + totalCredit);
                e.preventDefault();
            }
        });

        const DEFAULT_ACCOUNTS = ['1101', '1102', '4101', '5101', '1201', '2101', '5201', '5301', '2102'];

        // SweetAlert untuk konfirmasi delete
        $(document).on('click', '.delete-account', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const accountId = $(this).data('id');
            const accountCode = $(this).closest('tr').find('td:first').text().trim();

            // Cek apakah akun termasuk default yang tidak boleh dihapus
            if (DEFAULT_ACCOUNTS.includes(accountCode)) {
                Swal.fire({
                    title: 'Tidak Dapat Dihapus',
                    text: `Akun dengan kode ${accountCode} adalah akun default sistem dan tidak dapat dihapus.`,
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }
            
            Swal.fire({
                title: 'Hapus Akun?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function() {
                            Swal.fire('Deleted!', 'Akun berhasil dihapus', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal menghapus', 'error');
                        }
                    });
                }
            });
        });

        // SweetAlert untuk konfirmasi delete journal
        $(document).on('click', '.delete-journal-btn', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const journalId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Jurnal?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

    });

    function editAccount(url) {
        $.get(url)
            .done((response) => {
                if (response.success) {
                    // Isi form
                    $('#account_id').val(response.data.id);
                    $('#code').val(response.data.code);
                    $('#name').val(response.data.name);
                    $('#type').val(response.data.type);
                    $('#parent_id').val(response.data.parent_id);
                    
                    // Update form action
                    const updateUrl = url.replace('/edit', '');
                    $('#accountForm').attr('action', updateUrl);
                    
                    $('#accountModal').modal('show');
                }
            })
            .fail((errors) => {
                console.error(errors);
                Swal.fire('Error', 'Gagal memuat data akun', 'error');
            });
    }

    // Panggil dengan:
    $(document).on('click', '.edit-account', function() {
        const editUrl = $(this).data('url');
        const updateUrl = editUrl.replace('/edit', ''); // Hasilkan URL update
        editAccount(editUrl, updateUrl);
    });

    $('#accountForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST', // Tetap POST karena menggunakan method spoofing
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success')
                        .then(() => location.reload());
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMessage = '';
                
                for (const field in errors) {
                    errorMessage += errors[field].join('<br>') + '<br>';
                }
                
                Swal.fire('Error', errorMessage || xhr.responseJSON?.message || 'Gagal memperbarui akun', 'error');
            }
        });
    });

    // Fungsi Show Journal Detail
    function showJournal(url) {
        $('#journalDetailModal').modal('show');
        
        $.get(url)
            .done((response) => {
                if (response.success) {
                    const journal = response.data.journal;
                    $('#journalReference').text(journal.reference);
                    $('#journalDate').text(journal.date_formatted);
                    $('#journalDescription').text(journal.description);
                    
                    let entriesHtml = '';
                    let totalDebit = 0;
                    let totalCredit = 0;
                    
                    journal.entries.forEach(entry => {
                        entriesHtml += `
                            <tr>
                                <td>${entry.account.code} - ${entry.account.name}</td>
                                <td class="text-right">${entry.debit_formatted}</td>
                                <td class="text-right">${entry.credit_formatted}</td>
                                <td class="text-center">${entry.memo}</td>
                            </tr>
                        `;
                        totalDebit += parseFloat(entry.debit);
                        totalCredit += parseFloat(entry.credit);
                    });
                    
                    $('#journalEntries').html(entriesHtml);
                    $('#totalDebit').text('Rp ' + totalDebit.toLocaleString('id-ID'));
                    $('#totalCredit').text('Rp ' + totalCredit.toLocaleString('id-ID'));
                }
            })
            .fail((errors) => {
                console.error(errors);
                Swal.fire('Error', 'Gagal memuat detail jurnal', 'error');
            });
    }

    $(document).on('click', '.show-journal', function(e) {
        e.preventDefault();
        showJournal($(this).data('url'));
    });

    $(document).on('submit', '#journalFilterForm', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("financial.accounting.filterJournals") }}',
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {
                $('#journalsTable').html($(response).find('#journalsTable').html());
                $('.pagination').html($(response).find('.pagination').html());
                feather.replace();
            }
        });
    });

</script>
@endpush
