<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editGroupForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalLabel">Edit Kelompok Bagi Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="editFormErrors" class="alert alert-danger d-none"></div>
                    
                    <div class="form-group">
                        <label>Nama Kelompok*</label>
                        <input type="text" name="name" class="form-control" id="groupName" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="2" id="groupDescription"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Pilih Produk (Opsional)</label>
                        <select name="product_id" class="form-control" id="groupProduct">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id_produk }}" data-price="{{ $product->harga_jual }}">
                                    {{ $product->nama_produk }} ({{ format_uang($product->harga_jual) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Kuota</label>
                        <input type="text" name="total_quota" class="form-control" id="groupTotalQuota" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Anggota Investor</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="editInvestorTable">
                                <thead>
                                    <tr>
                                        <th>Investor</th>
                                        <th>Rekening</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="editInvestorTableBody">
                                    <!-- Rows akan diisi via JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <button type="button" class="btn btn-sm btn-primary" id="addEditInvestorBtn">
                                                <i class="fas fa-plus"></i> Tambah Investor
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="alert alert-info mt-3">
                                <strong>Sisa Kuota:</strong> 
                                <span id="editRemainingQuota">Rp 0</span> / 
                                <span id="editTotalQuotaDisplay">Rp 0</span>
                                <span class="mx-2">|</span>
                                <strong>Total Investasi:</strong>
                                <span id="editTotalInvestment">Rp 0</span>
                            </div>
                        </div>
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
