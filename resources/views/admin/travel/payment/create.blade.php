<x-layouts.admin :title="'Tambah Pembayaran - Booking ' . $booking->booking_code">
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">Tambah Pembayaran</h1>
            <p class="text-muted">Booking: {{ $booking->booking_code }} - {{ $booking->jamaah->nama }}</p>
          </div>
          <div>
            <a href="{{ route('admin.inventaris.booking.show', $booking->id) }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Form Pembayaran</h5>
          </div>
          <div class="card-body">
            <form action="{{ route('admin.inventaris.payment.store', $booking->id) }}" method="POST" enctype="multipart/form-data">
              @csrf

              @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
              @endif

              @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
              @endif

              <div class="form-group">
                <label>Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                       value="{{ old('amount') }}" min="0" max="{{ $booking->remaining_amount }}" step="0.01" required>
                <small class="form-text text-muted">
                  Sisa tagihan: Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                </small>
                @error('amount')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                  <option value="">Pilih Metode</option>
                  <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                  <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                  <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                  <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Kartu Debit</option>
                  <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('payment_method')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Tipe Pembayaran</label>
                <select name="payment_type" class="form-control @error('payment_type') is-invalid @enderror">
                  <option value="dp" {{ old('payment_type', 'dp') == 'dp' ? 'selected' : '' }}>DP (Down Payment)</option>
                  <option value="full" {{ old('payment_type') == 'full' ? 'selected' : '' }}>Lunas</option>
                </select>
                @error('payment_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Nomor Referensi</label>
                <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror" 
                       value="{{ old('reference_number') }}" placeholder="Nomor transaksi/referensi">
                @error('reference_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Bukti Transfer</label>
                <input type="file" name="bukti_transfer" class="form-control-file @error('bukti_transfer') is-invalid @enderror" 
                       accept="image/jpeg,image/jpg,image/png">
                <small class="form-text text-muted">Format: JPG, PNG. Maksimal 10MB</small>
                @error('bukti_transfer')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Keterangan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                          rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-0">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Simpan Pembayaran
                </button>
                <a href="{{ route('admin.inventaris.booking.show', $booking->id) }}" class="btn btn-secondary">
                  Batal
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Booking Info Sidebar -->
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-header">
            <h6 class="mb-0">Informasi Booking</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-borderless">
              <tr>
                <th>Jamaah:</th>
                <td>{{ $booking->jamaah->nama }}</td>
              </tr>
              <tr>
                <th>Paket:</th>
                <td>{{ $booking->travelPackage->package_name }}</td>
              </tr>
              <tr>
                <th>Total Harga:</th>
                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
              </tr>
              <tr>
                <th>Terbayar:</th>
                <td class="text-success">Rp {{ number_format($booking->paid_amount ?? 0, 0, ',', '.') }}</td>
              </tr>
              <tr>
                <th>Sisa Tagihan:</th>
                <td class="text-warning font-weight-bold">Rp {{ number_format($booking->remaining_amount ?? 0, 0, ',', '.') }}</td>
              </tr>
              <tr>
                <th>Status:</th>
                <td>
                  @php
                    $badges = [
                      'pending' => 'warning',
                      'confirmed' => 'info',
                      'paid' => 'success',
                      'departed' => 'primary',
                      'completed' => 'success',
                      'cancelled' => 'danger'
                    ];
                    $color = $badges[$booking->status] ?? 'secondary';
                  @endphp
                  <span class="badge badge-{{ $color }}">{{ ucfirst($booking->status) }}</span>
                </td>
              </tr>
            </table>
          </div>
        </div>

        @if($booking->payments->count() > 0)
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Riwayat Pembayaran</h6>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              @foreach($booking->payments->sortByDesc('payment_date')->take(5) as $payment)
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <div class="font-weight-bold text-success">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                    <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                  </div>
                  <span class="badge badge-{{ $payment->payment_type === 'full' ? 'success' : 'warning' }}">
                    {{ $payment->payment_type === 'full' ? 'LUNAS' : 'DP' }}
                  </span>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</x-layouts.admin>
