<div class="modal fade" id="closeBookModal" tabindex="-1" aria-labelledby="closeBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="closeBookModalLabel">
                    <i class="fas fa-lock me-2"></i>Konfirmasi Tutup Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</h5>
                    <hr>
                    <p class="mb-0">Proses ini tidak dapat dibatalkan. Pastikan:</p>
                    <ul class="mb-0">
                        <li>Semua transaksi telah dicatat dengan benar</li>
                        <li>Semua transaksi telah divalidasi</li>
                        <li>Saldo buku sudah balance</li>
                        <li>Periode buku sudah berakhir</li>
                    </ul>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Detail Buku</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Buku:</strong><br>{{ $book->name }}</p>
                                <p><strong>Periode:</strong><br>
                                    {{ $book->start_date->format('d/m/Y') }} - {{ $book->end_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Mata Uang:</strong><br>{{ $book->currency }}</p>
                                <p><strong>Status:</strong><br>
                                    <span class="badge bg-success">{{ ucfirst($book->status) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Aksi yang akan dilakukan</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Generate laporan akhir tahun (Laba Rugi, Neraca, Arus Kas)</li>
                            <li>Mengubah status buku menjadi "Closed"</li>
                            <li>Membuat buku baru untuk periode berikutnya (jika belum ada)</li>
                            <li>Memindahkan saldo akhir sebagai saldo awal periode berikutnya</li>
                        </ol>
                    </div>
                </div>
                
                <form id="closeBookForm">
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-key me-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" name="password" id="password" 
                               class="form-control" required 
                               placeholder="Masukkan password Anda untuk konfirmasi">
                        <div class="form-text">Anda harus memasukkan password untuk mengonfirmasi tindakan ini</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batalkan
                </button>
                <button type="button" class="btn btn-danger" id="confirmCloseBtn">
                    <i class="fas fa-lock me-2"></i>Konfirmasi Tutup Buku
                </button>
            </div>
        </div>
    </div>
</div>
