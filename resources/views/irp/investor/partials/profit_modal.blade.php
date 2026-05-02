<div class="modal fade" id="addProfitModal" tabindex="-1" role="dialog" aria-labelledby="addProfitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProfitModalLabel">Tambah Pembagian Keuntungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('irp.investor.profit.store', $investor->id) }}" method="POST" id="profitDistributionForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Periode*</label>
                                <input type="text" class="form-control" name="period" placeholder="Contoh: 2023-Q1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Keuntungan*</label>
                                <input type="number" class="form-control" name="total_profit" id="totalProfitInput" required step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Pembayaran</label>
                        <input type="date" class="form-control" name="payment_date">
                    </div>

                    <hr>
                    <h5>Pilih Rekening:</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40">Pilih</th>
                                    <th>Rekening</th>
                                    <th>Investasi</th>
                                    <th>Bagi Hasil</th>
                                    <th>Perhitungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($investor->accounts as $account)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               name="accounts[]" 
                                               value="{{ $account->id }}"
                                               class="account-checkbox"
                                               data-investment="{{ $account->total_investment }}"
                                               data-percentage="{{ $account->profit_percentage }}"
                                               checked>
                                    </td>
                                    <td>
                                        {{ $account->bank_name }}<br>
                                        <small>{{ $account->account_number }}</small>
                                    </td>
                                    <td class="text-right">{{ format_uang($account->total_investment) }}</td>
                                    <td class="text-right">{{ $account->profit_percentage }}%</td>
                                    <td class="text-right calculation" id="calc-{{ $account->id }}">
                                        -
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pembagian</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Fungsi untuk menghitung pembagian keuntungan
    function calculateProfitDistribution() {
        const totalProfit = parseFloat($('#totalProfitInput').val()) || 0;
        const checkedAccounts = $('.account-checkbox:checked');
        
        if (checkedAccounts.length === 0) return;
        
        // Hitung total investment dari akun yang dipilih
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

    // Format mata uang Rupiah
    function formatRupiah(amount) {
        return 'Rp ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Event listeners
    $('#totalProfitInput').on('input', calculateProfitDistribution);
    $('.account-checkbox').change(calculateProfitDistribution);
    
    // Hitung awal saat modal dibuka
    $('#addProfitModal').on('shown.bs.modal', calculateProfitDistribution);
});
</script>
@endpush
