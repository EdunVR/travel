<x-layouts.admin :title="($isEdit ? 'Edit' : 'Tambah') . ' Add-on - Booking ' . $booking->booking_code">
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">{{ $isEdit ? 'Edit' : 'Tambah' }} Add-on</h1>
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
            <h5 class="mb-0">{{ $isEdit ? 'Edit' : 'Tambah' }} Add-on</h5>
          </div>
          <div class="card-body">
            <form action="{{ $isEdit ? route('admin.inventaris.booking.addons.update', [$booking->id, $addon->id]) : route('admin.inventaris.booking.addons.store', $booking->id) }}" method="POST">
              @csrf
              @if($isEdit)
                @method('PUT')
              @endif

              @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
              @endif

              @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
              @endif

              <div class="form-group">
                <label>Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                       value="{{ old('nama', $addon->nama ?? '') }}" 
                       placeholder="Contoh: Upgrade Kamar, Makan Tambahan" required>
                @error('nama')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                          rows="3" placeholder="Deskripsi tambahan">{{ old('keterangan', $addon->keterangan ?? '') }}</textarea>
                @error('keterangan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" 
                           value="{{ old('harga', $addon->harga ?? 0) }}" min="0" step="0.01" required>
                    @error('harga')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Qty <span class="text-danger">*</span></label>
                    <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" 
                           value="{{ old('qty', $addon->qty ?? 1) }}" min="1" required>
                    @error('qty')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="masuk_hpp" value="1" class="custom-control-input" id="masukHpp"
                         {{ old('masuk_hpp', $addon->masuk_hpp ?? true) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="masukHpp">Masuk HPP</label>
                </div>
                <small class="text-muted">Centang jika add-on ini harus dihitung dalam HPP booking</small>
              </div>

              <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Simpan
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
        <div class="card">
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
      </div>
    </div>
  </div>
</x-layouts.admin>
