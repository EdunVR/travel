<x-layouts.admin :title="($isEdit ? 'Edit' : 'Tambah') . ' Hotel Booking - ' . $booking->booking_code">
  <div class="container-fluid" x-data="hotelBookingForm()">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">{{ $isEdit ? 'Edit' : 'Tambah' }} Hotel Booking</h1>
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
            <h5 class="mb-0">{{ $isEdit ? 'Edit' : 'Tambah' }} Hotel Booking</h5>
          </div>
          <div class="card-body">
            <form action="{{ $isEdit ? route('admin.inventaris.booking.hotel-bookings.update', [$booking->id, $hotelBooking->id]) : route('admin.inventaris.booking.hotel-bookings.store', $booking->id) }}" method="POST">
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
                <label>Kota <span class="text-danger">*</span></label>
                <select name="city_type" class="form-control @error('city_type') is-invalid @enderror" required>
                  <option value="makkah" {{ old('city_type', $hotelBooking->city_type ?? 'makkah') == 'makkah' ? 'selected' : '' }}>Mekkah</option>
                  <option value="madinah" {{ old('city_type', $hotelBooking->city_type ?? '') == 'madinah' ? 'selected' : '' }}>Madinah</option>
                </select>
                @error('city_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Hotel <span class="text-danger">*</span></label>
                <select name="id_hotel" x-model="selectedHotelId" @change="onHotelChange()" 
                        class="form-control @error('id_hotel') is-invalid @enderror" required>
                  <option value="">Pilih Hotel</option>
                  @foreach($hotels as $hotel)
                    <option value="{{ $hotel->id }}" 
                            data-room-types="{{ json_encode($hotel->roomTypes) }}"
                            {{ old('id_hotel', $hotelBooking->id_hotel ?? '') == $hotel->id ? 'selected' : '' }}>
                      {{ $hotel->hotel_name }} - {{ $hotel->location }}
                    </option>
                  @endforeach
                </select>
                @error('id_hotel')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label>Tipe Kamar</label>
                <select name="room_type" x-model="selectedRoomType" @change="onRoomTypeChange()"
                        class="form-control @error('room_type') is-invalid @enderror">
                  <option value="">Pilih Tipe Kamar</option>
                  <template x-for="rt in roomTypes" :key="rt.id">
                    <option :value="rt.room_type_name" 
                            :data-price="rt.price_per_night"
                            x-text="`${rt.room_type_name} - Rp ${formatNumber(rt.price_per_night)}/malam`"></option>
                  </template>
                  <!-- Fallback options -->
                  <template x-if="roomTypes.length === 0">
                    <option value="quad">Quad (4 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="triple">Triple (3 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="double">Double (2 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="single">Single (1 orang)</option>
                  </template>
                </select>
                <small class="text-muted" x-show="roomTypes.length > 0">Harga/malam otomatis terisi dari tipe kamar</small>
                <small class="text-muted" x-show="roomTypes.length === 0 && selectedHotelId">Hotel ini belum memiliki tipe kamar, isi harga manual</small>
                @error('room_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Check-in</label>
                    <input type="date" name="check_in_date" x-model="checkInDate" @change="calculateNights()"
                           class="form-control @error('check_in_date') is-invalid @enderror"
                           value="{{ old('check_in_date', $hotelBooking->check_in_date ?? '') }}">
                    @error('check_in_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Check-out</label>
                    <input type="date" name="check_out_date" x-model="checkOutDate" @change="calculateNights()"
                           class="form-control @error('check_out_date') is-invalid @enderror"
                           value="{{ old('check_out_date', $hotelBooking->check_out_date ?? '') }}">
                    @error('check_out_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jumlah Malam</label>
                    <input type="number" name="nights" x-model.number="nights" min="0"
                           class="form-control @error('nights') is-invalid @enderror"
                           value="{{ old('nights', $hotelBooking->nights ?? 0) }}">
                    @error('nights')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Harga/Malam (Rp)</label>
                    <input type="number" name="price_per_night" x-model.number="pricePerNight" min="0"
                           class="form-control @error('price_per_night') is-invalid @enderror"
                           value="{{ old('price_per_night', $hotelBooking->price_per_night ?? 0) }}">
                    <small class="text-muted" x-text="`Total: Rp ${formatNumber(pricePerNight * nights)}`"></small>
                    @error('price_per_night')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <input type="hidden" name="total_cost" :value="pricePerNight * nights">

              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="is_charged" value="1" class="custom-control-input" id="isCharged"
                         {{ old('is_charged', $hotelBooking->is_charged ?? false) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="isCharged">
                    <strong>Charge ke Invoice</strong>
                  </label>
                </div>
                <small class="text-muted">
                  Jika dicentang, biaya hotel akan ditambahkan ke invoice jamaah. Jika tidak, keterangan "Include Paket".
                </small>
              </div>

              <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                          rows="2">{{ old('notes', $hotelBooking->notes ?? '') }}</textarea>
                @error('notes')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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

  @push('scripts')
  <script>
    function hotelBookingForm() {
      return {
        selectedHotelId: '{{ old('id_hotel', $hotelBooking->id_hotel ?? '') }}',
        selectedRoomType: '{{ old('room_type', $hotelBooking->room_type ?? '') }}',
        checkInDate: '{{ old('check_in_date', $hotelBooking->check_in_date ?? '') }}',
        checkOutDate: '{{ old('check_out_date', $hotelBooking->check_out_date ?? '') }}',
        nights: {{ old('nights', $hotelBooking->nights ?? 0) }},
        pricePerNight: {{ old('price_per_night', $hotelBooking->price_per_night ?? 0) }},
        roomTypes: [],

        init() {
          if (this.selectedHotelId) {
            this.loadRoomTypes();
          }
        },

        onHotelChange() {
          this.loadRoomTypes();
          this.selectedRoomType = '';
          this.pricePerNight = 0;
        },

        loadRoomTypes() {
          const select = document.querySelector('select[name="id_hotel"]');
          const option = select.querySelector(`option[value="${this.selectedHotelId}"]`);
          if (option) {
            const roomTypesData = option.getAttribute('data-room-types');
            this.roomTypes = roomTypesData ? JSON.parse(roomTypesData) : [];
          } else {
            this.roomTypes = [];
          }
        },

        onRoomTypeChange() {
          const rt = this.roomTypes.find(r => r.room_type_name === this.selectedRoomType);
          if (rt) {
            this.pricePerNight = parseFloat(rt.price_per_night) || 0;
          }
        },

        calculateNights() {
          if (this.checkInDate && this.checkOutDate) {
            const checkIn = new Date(this.checkInDate);
            const checkOut = new Date(this.checkOutDate);
            const diffTime = checkOut - checkIn;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            this.nights = diffDays > 0 ? diffDays : 0;
          }
        },

        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num || 0);
        }
      };
    }
  </script>
  @endpush
</x-layouts.admin>
