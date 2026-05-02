<tr class="level-{{ $level }}">
    <td class="account-code" style="text-align: left">
        <span class="d-block text-left pr-2">{{ $account['code'] }}</span>
    </td>
    <td class="account-name" style="text-align: left">
        <span class="d-block text-left pl-2">{{ $account['name'] }}</span>
    </td>
    <td class="text-center">
        @switch($account['type'])
            @case('asset')
                <span class="badge badge-primary">Asset</span>
                @break
            @case('liability')
                <span class="badge badge-success">Liability</span>
                @break
            @case('equity')
                <span class="badge badge-info">Equity</span>
                @break
            @case('revenue')
                <span class="badge badge-warning">Revenue</span>
                @break
            @case('expense')
                <span class="badge badge-danger">Expense</span>
                @break
        @endswitch
    </td>
    <td class="text-center">
        @if($account['is_active'])
            <span class="badge badge-success">Aktif</span>
        @else
            <span class="badge badge-secondary">Non-Aktif</span>
        @endif
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-sm">
            <button class="btn btn-primary add-child-btn" 
                    data-parent-code="{{ $account['code'] }}" 
                    data-parent-name="{{ $account['name'] }}"
                    title="Tambah Child">
                <i data-feather="plus" width="16"></i>
            </button>
            @if($level > 0 || empty($account['children']))
                <button class="btn btn-danger delete-account-btn" 
                        data-code="{{ $account['code'] }}" 
                        title="Hapus">
                    <i data-feather="trash-2" width="16"></i>
                </button>
            @endif
        </div>
    </td>
</tr>

@if(!empty($account['children']))
    @foreach($account['children'] as $child)
        @include('financial.book.account_row', [
            'account' => $child, 
            'level' => $level + 1,
            'parentAccounts' => $parentAccounts
        ])
    @endforeach
@endif
