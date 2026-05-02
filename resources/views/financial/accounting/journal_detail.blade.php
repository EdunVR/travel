<div class="modal-header">
    <h5 class="modal-title">Detail Jurnal: {{ $journal->reference }}</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6"><strong>Tanggal:</strong> {{ $journal->date->format('d/m/Y') }}</div>
        <div class="col-md-6"><strong>Keterangan:</strong> {{ $journal->description }}</div>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Akun</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->entries as $entry)
            <tr>
                <td>{{ $entry->account->code }} - {{ $entry->account->name }}</td>
                <td class="text-right">{{ number_format($entry->debit, 2) }}</td>
                <td class="text-right">{{ number_format($entry->credit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-weight-bold">
                <td class="text-right">Total:</td>
                <td class="text-right">{{ number_format($journal->entries->sum('debit'), 2) }}</td>
                <td class="text-right">{{ number_format($journal->entries->sum('credit'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
