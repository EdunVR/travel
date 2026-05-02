<div class="mb-3">
    <h4>Total Investasi: {{ format_uang($investor->total_investment) }}</h4>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rekening</th>
                <th>Bank</th>
                <th>Investasi</th>
                <th>Persentase</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investor->accounts as $account)
            <tr>
                <td>{{ $account->account_number }}</td>
                <td>{{ $account->bank_name }}</td>
                <td class="text-right">{{ format_uang($account->total_investment) }}</td>
                <td class="text-right">{{ $account->profit_percentage }}%</td>
                <td>
                    <button class="btn btn-sm btn-primary" data-toggle="modal" 
                            data-target="#addInvestmentModal" 
                            data-account-id="{{ $account->id }}">
                        <i class="fas fa-plus"></i> Tambah Investasi
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('irp.investor.partials.investment_modal')
