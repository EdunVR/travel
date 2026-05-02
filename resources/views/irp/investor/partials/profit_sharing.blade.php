<div class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <button class="btn btn-success" data-toggle="modal" data-target="#addProfitModal">
                <i class="fas fa-plus"></i> Tambah Pembagian Keuntungan
            </button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addTransactionModal">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </button>
        </div>
        <div class="col-md-8">
            <form method="GET" class="form-inline float-right">
                <div class="form-group mr-2">
                    <select name="account" class="form-control form-control-sm">
                        <option value="">Semua Rekening</option>
                        @foreach($investor->accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->bank_name }} - {{ $acc->account_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                    <select name="type" class="form-control form-control-sm">
                        <option value="">Semua Jenis</option>
                        <option value="investment" {{ request('type') == 'investment' ? 'selected' : '' }}>Investasi</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Bagi Hasil</option>
                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Pencairan</option>
                        <option value="penarikan" {{ request('type') == 'penarikan' ? 'selected' : '' }}>Penarikan Modal</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <input type="date" name="start_date" class="form-control form-control-sm" 
                           value="{{ request('start_date') }}" placeholder="Dari Tanggal">
                </div>
                <div class="form-group mr-2">
                    <input type="date" name="end_date" class="form-control form-control-sm" 
                           value="{{ request('end_date') }}" placeholder="Sampai Tanggal">
                </div>
                <button type="submit" class="btn btn-sm btn-info mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('irp.investor.show', $investor->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Rekening</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Debit</th>
                <th>Kredit</th>
                <th>Saldo Investasi</th>
                <th>Saldo Bagi Hasil</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $investmentBalance = 0;
                $profitBalance = 0;
                $filteredAccounts = request('account') 
                    ? $investor->accounts->where('id', request('account')) 
                    : $investor->accounts;
                
                $transactions = $filteredAccounts->flatMap(function($account) {
                    return $account->investments
                        ->when(request('type'), function($query, $type) {
                            return $query->where('type', $type);
                        })
                        ->when(request('start_date'), function($query, $date) {
                            return $query->where('date', '>=', $date);
                        })
                        ->when(request('end_date'), function($query, $date) {
                            return $query->where('date', '<=', $date);
                        })
                        ->map(function($investment) use ($account) {
                            $investment->account_number = $account->account_number;
                            $investment->bank_name = $account->bank_name;
                            return $investment;
                        });
                })->sortBy('date');
            @endphp

            @foreach($transactions as $transaction)
                @php
                    if ($transaction->type == 'investment') {
                        $investmentBalance += $transaction->amount;
                    } elseif ($transaction->type == 'deposit') {
                        $profitBalance += $transaction->amount;
                    } elseif ($transaction->type == 'withdrawal') {
                        $profitBalance -= $transaction->amount;
                    } elseif ($transaction->type == 'penarikan') {
                        $investmentBalance -= $transaction->amount;
                    }
                @endphp
                <tr>
                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                    <td>
                        {{ $transaction->bank_name }}<br>
                        <small>{{ $transaction->account_number }}</small>
                    </td>
                    <td>
                        @if($transaction->type == 'investment')
                            <span class="badge badge-info">Investasi</span>
                        @elseif($transaction->type == 'deposit')
                            <span class="badge badge-success">Bagi Hasil</span>
                        @elseif($transaction->type == 'withdrawal')
                            <span class="badge badge-warning">Pencairan</span>
                        @else
                            <span class="badge badge-danger">Penarikan Modal</span>
                        @endif
                    </td>
                    <td>{{ $transaction->description }}</td>
                    <td class="text-right">
                        @if($transaction->type == 'investment' || $transaction->type == 'deposit')
                            {{ format_uang($transaction->amount) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($transaction->type == 'withdrawal' || $transaction->type == 'penarikan')
                            {{ format_uang($transaction->amount) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($transaction->type == 'investment' || $transaction->type == 'penarikan')
                            {{ format_uang($investmentBalance) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($transaction->type == 'deposit' || $transaction->type == 'withdrawal')
                            {{ format_uang($profitBalance) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-transaction" 
                                data-id="{{ $transaction->id }}"
                                data-account-id="{{ $transaction->account_id }}"
                                data-toggle="modal" 
                                data-target="#editTransactionModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form class="d-inline delete-form" 
                            action="{{ route('irp.investor.account.investment.destroy', [
                                'investor' => $investor->id,
                                'account' => $transaction->account_id,
                                'investment' => $transaction->id
                            ]) }}" 
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                    data-id="{{ $transaction->id }}"
                                    title="Hapus Transaksi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pembagian Keuntungan -->
@include('irp.investor.partials.profit_modal', ['investor' => $investor, 'accounts' => $investor->accounts])

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('irp.investor.account.investment.store', ['investor' => $investor->id, 'account' => ':accountId']) }}" id="addTransactionForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rekening*</label>
                        <select name="account_id" class="form-control" required>
                            @foreach($investor->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal*</label>
                        <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Jenis Transaksi*</label>
                        <select name="type" class="form-control" required>
                            <option value="investment">Investasi</option>
                            <option value="deposit">Bagi Hasil</option>
                            <option value="withdrawal">Pencairan</option>
                            <option value="penarikan">Penarikan Modal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah*</label>
                        <input type="number" name="amount" class="form-control" required step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Dokumen (Optional)</label>
                        <input type="file" name="document" class="form-control-file">
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

<!-- Modal untuk Edit Transaksi -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTransactionForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Transaksi*</label>
                        <select name="type" class="form-control" required>
                            <option value="investment">Investasi</option>
                            <option value="deposit">Bagi Hasil</option>
                            <option value="withdrawal">Pencairan</option>
                            <option value="penarikan">Penarikan Modal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="amount" class="form-control" required step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Dokumen (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="document" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi form tambah transaksi
    $('#addTransactionForm').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var accountId = form.find('select[name="account_id"]').val();
        var actionUrl = form.attr('action').replace(':accountId', accountId);
        
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addTransactionModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
            }
        });
    });

    // Inisialisasi form tambah bagi hasil
    $('#profitDistributionForm').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Memproses Pembagian',
            html: 'Sedang menyimpan data pembagian keuntungan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    let message = '<div class="text-left"><h5>Detail Pembagian:</h5><ul class="list-group">';
                    
                    response.details.forEach(detail => {
                        message += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${detail.account}
                            <span class="badge badge-primary badge-pill">
                                ${formatRupiah(detail.profit_share)}
                            </span>
                        </li>`;
                    });
                    
                    message += '</ul></div>';
                    
                    Swal.fire({
                        title: 'Berhasil',
                        icon: 'success',
                        html: message,
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Perhitungan preview bagi hasil
    $('#totalProfitInput').on('input', calculateProfitDistribution);
    $('.account-checkbox').change(calculateProfitDistribution);

    function calculateProfitDistribution() {
        const totalProfit = parseFloat($('#totalProfitInput').val()) || 0;
        const checkedAccounts = $('.account-checkbox:checked');
        
        if (checkedAccounts.length === 0) return;
        
        // Hitung total investasi dari akun yang dipilih
        let totalInvestment = 0;
        checkedAccounts.each(function() {
            totalInvestment += parseFloat($(this).data('investment'));
        });
        
        // Hitung bagi hasil untuk masing-masing akun
        checkedAccounts.each(function() {
            const accountId = $(this).val();
            const accountInvestment = parseFloat($(this).data('investment'));
            const percentage = parseFloat($(this).data('percentage'));
            
            // Hitung proporsi
            const investmentRatio = accountInvestment / totalInvestment;
            
            // Hitung jumlah yang diterima
            const profitAmount = totalProfit * investmentRatio * (percentage / 100);
            
            // Tampilkan hasil perhitungan
            $(`#calc-${accountId}`).text(formatRupiah(profitAmount));
        });
    }

    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }

    // Tangani klik tombol edit
    $('.edit-transaction').click(function() {
        var transactionId = $(this).data('id');
        var accountId = $(this).data('account-id'); // Ambil account_id dari data attribute
        
        // Ambil data transaksi via AJAX
        $.get("{{ route('irp.investor.account.investment.edit', [
            'investor' => $investor->id,
            'account' => ':accountId',
            'investment' => ':transactionId'
        ]) }}".replace(':accountId', accountId).replace(':transactionId', transactionId), 
        function(data) {
            var dateObj = new Date(data.date);
            var formattedDate = dateObj.toISOString().split('T')[0];
            // Isi form edit dengan data transaksi
            $('#editTransactionForm').attr('action', 
                "{{ url('irp/investor') }}/{{ $investor->id }}/accounts/" + 
                accountId + "/investments/" + transactionId);
            
            $('input[name="date"]').val(formattedDate);
            $('select[name="type"]').val(data.type);
            $('input[name="amount"]').val(data.amount);
            $('textarea[name="description"]').val(data.description);
        });
    });

    // Handle delete transaction
    $(document).on('click', '.delete-btn', function() {
        const form = $(this).closest('.delete-form');
        const transactionId = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Anda yakin ingin menghapus transaksi ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Terhapus!',
                                'Transaksi berhasil dihapus.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Gagal menghapus transaksi',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush
