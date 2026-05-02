<div class="mb-4">
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addAccountModal">
        <i class="fas fa-plus"></i> Tambah Rekening Baru
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Rekening</th>
                <th>Atas Nama</th>
                <th>Tanggal</th>
                <th>Jatuh Tempo</th>
                <th>Total Investasi</th>
                <th>Saldo Tertahan</th>
                <th>Saldo Bagi Hasil</th>
                <th>Persentase</th>
                <th>Status</th>
                <th style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($investor->accounts as $account)
            <tr>
                <td>
                    {{ $account->bank_name }}
                    <div class="text-center"><small>{{ $account->account_number }}</small></div>
                </td>
                <td>{{ $account->account_name }}</td>
                <td>{{ $account->date ? tanggal_indonesia($account->date) : '-' }}</td>
                <td>{{ $account->tempo ? tanggal_indonesia($account->tempo) : '-' }}</td>
                <td class="text-right">{{ format_uang($account->total_investment) }}</td>
                <td class="text-right">{{ format_uang($account->saldo_tertahan) }}</td>
                <td class="text-right">{{ format_uang($account->profit_balance - $account->saldo_tertahan) }}</td>
                <td class="text-right">{{ $account->profit_percentage }}%</td>
                <td>
                    <span class="badge badge-{{ $account->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($account->status) }}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('irp.investor.account.show', ['investor' => $investor->id, 'account' => $account->id]) }}" 
                        class="btn btn-info" title="Detail">
                            <i data-feather="eye" class="icon-xs"></i>
                        </a>
                        <a href="{{ route('irp.investor.account.edit', ['investor' => $investor->id, 'account' => $account->id]) }}" 
                        class="btn btn-warning" title="Edit">
                            <i data-feather="edit" class="icon-xs"></i>
                        </a>
                        <form action="{{ route('irp.investor.account.destroy', ['investor' => $investor->id, 'account' => $account->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus rekening ini?')">
                                <i data-feather="trash-2" class="icon-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada rekening</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('irp.investor.partials.account_modal', ['investor' => $investor])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tangani tombol edit
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const accountId = this.getAttribute('data-account-id');
            editAccount(accountId);
        });
    });
    
    // Definisikan fungsi editAccount
    function editAccount(accountId) {
        // Ganti dengan URL edit yang benar
        const url = `{{ route('irp.investor.account.edit', ['investor' => $investor->id, 'account' => '__ACCOUNT_ID__']) }`
            .replace('__ACCOUNT_ID__', accountId);
        
        // Redirect ke halaman edit
        window.location.href = url;
    }
});
</script>
@endpush
