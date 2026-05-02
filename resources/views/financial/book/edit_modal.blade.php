<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editBookModalLabel">Edit Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBookForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_book_id" value="{{ $book->id }}">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Buku</label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $book->name }}" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" 
                                   value="{{ $book->start_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_end_date" class="form-label">Tanggal Berakhir</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" 
                                   value="{{ $book->end_date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_currency" class="form-label">Mata Uang</label>
                        <select class="form-select" id="edit_currency" name="currency" required>
                            <option value="IDR" {{ $book->currency === 'IDR' ? 'selected' : '' }}>IDR - Rupiah</option>
                            <option value="USD" {{ $book->currency === 'USD' ? 'selected' : '' }}>USD - Dolar AS</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
