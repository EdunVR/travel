<div class="modal fade" id="journalDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Jurnal: <span id="journalReference"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tanggal:</strong> <span id="journalDate"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Keterangan:</strong> <span id="journalDescription"></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Akun</th>
                                <th class="text-right">Debit (Rp)</th>
                                <th class="text-right">Credit (Rp)</th>
                                <th class="text-center">Memo</th>
                            </tr>
                        </thead>
                        <tbody id="journalEntries">
                            <!-- Data akan diisi via JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td class="text-right">Total:</td>
                                <td class="text-right" id="totalDebit"></td>
                                <td class="text-right" id="totalCredit"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
