<x-layouts.admin :title="'Travel / Edit Paket Perjalanan'">
  <div x-data="packageEdit()" x-init="init()" class="space-y-4 self-start w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <a href="{{ route('admin.inventaris.travel.package.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-2">
          <i class="bx bx-arrow-back"></i> Kembali ke Daftar Paket
        </a>
        <h1 class="text-2xl font-bold">Edit Paket Perjalanan</h1>
        <p class="text-slate-600 text-sm">Edit paket {{ $package->package_name }}</p>
      </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submitForm" class="space-y-6">
      <!-- Basic Information -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <h2 class="text-lg font-semibold mb-4">Informasi Dasar</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kode Paket <span class="text-red-500">*</span></label>
            <input type="text" x-model="form.package_code" required
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                   placeholder="Contoh: HJ2024-001">
            <p x-show="errors.package_code" x-text="errors.package_code" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Paket <span class="text-red-500">*</span></label>
            <input type="text" x-model="form.package_name" required
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                   placeholder="Contoh: Paket Umrah Ramadhan 2024">
            <p x-show="errors.package_name" x-text="errors.package_name" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kategori Paket <span class="text-red-500">*</span></label>
            <select x-model="form.package_subtype" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="">Pilih Kategori</option>
              <option value="umroh_regular">Umroh Regular</option>
              <option value="umroh_plus">Umroh Plus</option>
              <option value="umroh_ramadhan">Umroh Ramadhan</option>
              <option value="haji">Haji</option>
            </select>
            <p x-show="errors.package_subtype" x-text="errors.package_subtype" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
            <select x-model="form.id_outlet" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="">Pilih Outlet</option>
              @foreach($outlets as $outlet)
              <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
              @endforeach
            </select>
            <p x-show="errors.id_outlet" x-text="errors.id_outlet" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Durasi (Hari) <span class="text-red-500">*</span></label>
            <input type="number" x-model="form.duration_days" required min="1"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                   placeholder="Contoh: 9">
            <p x-show="errors.duration_days" x-text="errors.duration_days" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kapasitas <span class="text-red-500">*</span></label>
            <input type="number" x-model="form.capacity" required min="1"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                   placeholder="Contoh: 45">
            <p x-show="errors.capacity" x-text="errors.capacity" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Keberangkatan <span class="text-red-500">*</span></label>
            <input type="date" x-model="form.departure_date" required
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <p x-show="errors.departure_date" x-text="errors.departure_date" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Kepulangan <span class="text-red-500">*</span></label>
            <input type="date" x-model="form.return_date" required
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <p x-show="errors.return_date" x-text="errors.return_date" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
            <select x-model="form.status"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="draft">Draft</option>
              <option value="active">Active</option>
              <option value="full">Full</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <!-- Paket Harga -->
        <div class="mt-6">
          <div class="flex items-center justify-between mb-3">
            <label class="block text-sm font-medium text-slate-700">Paket Harga <span class="text-red-500">*</span></label>
            <button type="button" @click="addPricePackage()"
                    class="inline-flex items-center gap-1 text-xs rounded-lg bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1.5 hover:bg-primary-100">
              <i class="bx bx-plus"></i> Tambah Paket
            </button>
          </div>
          <div class="space-y-3">
            <template x-for="(pkg, pkgIdx) in form.price_packages" :key="pkgIdx">
              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between mb-3">
                  <select x-model="pkg.name" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium focus:ring-2 focus:ring-primary-200">
                    <option value="Reguler">Reguler</option>
                    <option value="Royal">Royal</option>
                    <option value="Platinum">Platinum</option>
                    <option value="VIP">VIP</option>
                  </select>
                  <button type="button" @click="removePricePackage(pkgIdx)" x-show="form.price_packages.length > 1"
                          class="p-1 rounded-lg text-red-500 hover:bg-red-50">
                    <i class="bx bx-trash text-sm"></i>
                  </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                  <template x-for="(variant, vIdx) in pkg.variants" :key="vIdx">
                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                      <div class="text-xs font-medium text-slate-600 mb-2 capitalize" x-text="variant.type"></div>
                      <input type="number" x-model.number="variant.price" min="0" placeholder="Harga"
                             class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-primary-200">
                      <div class="text-xs text-slate-400 mt-1" x-show="variant.price > 0" x-text="'Rp ' + formatNumber(variant.price)"></div>
                    </div>
                  </template>
                </div>
              </div>
            </template>
          </div>
          <p x-show="errors.price_packages" x-text="errors.price_packages" class="text-red-500 text-sm mt-1"></p>
        </div>

        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
          <textarea x-model="form.description" rows="3"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                    placeholder="Deskripsi paket perjalanan"></textarea>
        </div>

        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700 mb-2">Nama Ustadz Pendamping</label>
          <input type="text" x-model="form.ustadz_name"
                 class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                 placeholder="Contoh: Ustadz Ahmad Fauzi, Lc">
        </div>

        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700 mb-2">Fasilitas yang Termasuk</label>
          <textarea x-model="form.inclusions" rows="3"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                    placeholder="Contoh: Tiket pesawat PP, Hotel bintang 5, Makan 3x sehari, dll"></textarea>
        </div>

        <!-- Label Paket -->
        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700 mb-3">Label Paket</label>
          <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center cursor-pointer">
              <input type="checkbox" x-model="form.is_promo" value="1"
                     class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2">
              <span class="ml-2 text-sm font-medium text-gray-700 flex items-center gap-2">
                <i class='bx bxs-offer text-red-600 text-lg'></i>
                <span>Promo</span>
              </span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
              <input type="checkbox" x-model="form.is_best_seller" value="1"
                     class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2">
              <span class="ml-2 text-sm font-medium text-gray-700 flex items-center gap-2">
                <i class='bx bxs-star text-yellow-600 text-lg'></i>
                <span>Best Seller</span>
              </span>
            </label>
          </div>
          <p class="text-xs text-slate-400 mt-2">Label akan ditampilkan di katalog dan homepage untuk menarik perhatian</p>
        </div>
      </div>

      <!-- Flight Information -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <h2 class="text-lg font-semibold mb-4">Informasi Penerbangan</h2>
        
        <!-- Flight Group Selection (Quick Option) -->
        <div class="mb-4 p-3 rounded-lg bg-blue-50 border border-blue-200">
          <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-medium text-blue-900">🚀 Pilih Grup Penerbangan (Opsional)</label>
            <button type="button" @click="clearFlightGroup()" x-show="form.flight_group_code"
                    class="text-xs text-blue-600 hover:text-blue-700">
              <i class="bx bx-x"></i> Batal
            </button>
          </div>
          <select x-model="form.flight_group_code" @change="onFlightGroupChange"
                  class="w-full rounded-lg border border-blue-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 bg-white">
            <option value="">-- Pilih Grup (Auto-fill Departure & Return) --</option>
            <template x-for="group in flightGroups" :key="group.code">
              <option :value="group.code" x-text="`${group.label}`"></option>
            </template>
          </select>
          <p class="text-xs text-blue-600 mt-1">💡 Pilih grup untuk auto-fill penerbangan keberangkatan dan kepulangan sekaligus</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Penerbangan Keberangkatan</label>
            <select x-model="form.id_flight_departure" @change="onFlightDepartureChange"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                    :class="{'bg-slate-100': form.flight_group_code}">
              <option :value="null">-- Pilih Penerbangan --</option>
              <template x-for="flight in flights" :key="flight.id">
                <option :value="flight.id" :selected="flight.id == form.id_flight_departure" x-text="`${flight.airline_name} - ${flight.flight_number} (${flight.departure_airport} → ${flight.arrival_airport})${flight.seller_name ? ' | ' + flight.seller_name : ''}`"></option>
              </template>
            </select>
            <p x-show="errors.id_flight_departure" x-text="errors.id_flight_departure" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Keberangkatan</label>
            <input type="datetime-local" x-model="form.departure_datetime"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <p x-show="errors.departure_datetime" x-text="errors.departure_datetime" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Penerbangan Kepulangan</label>
            <select x-model="form.id_flight_return" @change="onFlightReturnChange"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200"
                    :class="{'bg-slate-100': form.flight_group_code}">
              <option :value="null">-- Pilih Penerbangan --</option>
              <template x-for="flight in flights" :key="flight.id">
                <option :value="flight.id" :selected="flight.id == form.id_flight_return" x-text="`${flight.airline_name} - ${flight.flight_number} (${flight.departure_airport} → ${flight.arrival_airport})${flight.seller_name ? ' | ' + flight.seller_name : ''}`"></option>
              </template>
            </select>
            <p x-show="errors.id_flight_return" x-text="errors.id_flight_return" class="text-red-500 text-sm mt-1"></p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Kepulangan</label>
            <input type="datetime-local" x-model="form.return_datetime"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <p x-show="errors.return_datetime" x-text="errors.return_datetime" class="text-red-500 text-sm mt-1"></p>
          </div>
        </div>
      </div>

      <!-- Hotel Makkah & Madinah (Specific) -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <h2 class="text-lg font-semibold mb-4">🏨 Hotel Makkah & Madinah</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
          <!-- Hotel Makkah -->
          <div class="space-y-4">
            <h3 class="font-medium text-slate-700">Hotel Makkah</h3>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Hotel</label>
              <select x-model="form.id_hotel_makkah" @change="onHotelMakkahChange"
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option :value="null">-- Pilih Hotel Makkah --</option>
                <template x-for="hotel in hotels.filter(h => h.city === 'Makkah' || h.city === 'Mekah')" :key="hotel.id">
                  <option :value="hotel.id" :selected="hotel.id == form.id_hotel_makkah" x-text="`${hotel.hotel_name} - ${hotel.location || ''}`"></option>
                </template>
              </select>
              <p x-show="errors.id_hotel_makkah" x-text="errors.id_hotel_makkah" class="text-red-500 text-sm mt-1"></p>
            </div>
            
            <div x-show="form.id_hotel_makkah && hotelRoomTypesMakkah.length > 0">
              <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Kamar</label>
              <select x-model="form.id_hotel_room_type_makkah"
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option :value="null">-- Pilih Tipe Kamar --</option>
                <template x-for="rt in hotelRoomTypesMakkah" :key="rt.id">
                  <option :value="rt.id" :selected="rt.id == form.id_hotel_room_type_makkah" x-text="`${rt.room_type_name} - Rp ${rt.price_per_night?.toLocaleString()}/malam`"></option>
                </template>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Check-in</label>
                <input type="date" x-model="form.makkah_check_in"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Check-out</label>
                <input type="date" x-model="form.makkah_check_out"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>
            </div>
          </div>
          
          <!-- Hotel Madinah -->
          <div class="space-y-4">
            <h3 class="font-medium text-slate-700">Hotel Madinah</h3>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Hotel</label>
              <select x-model="form.id_hotel_madinah" @change="onHotelMadinahChange"
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option :value="null">-- Pilih Hotel Madinah --</option>
                <template x-for="hotel in hotels.filter(h => h.city === 'Madinah')" :key="hotel.id">
                  <option :value="hotel.id" :selected="hotel.id == form.id_hotel_madinah" x-text="`${hotel.hotel_name} - ${hotel.location || ''}`"></option>
                </template>
              </select>
              <p x-show="errors.id_hotel_madinah" x-text="errors.id_hotel_madinah" class="text-red-500 text-sm mt-1"></p>
            </div>
            
            <div x-show="form.id_hotel_madinah && hotelRoomTypesMadinah.length > 0">
              <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Kamar</label>
              <select x-model="form.id_hotel_room_type_madinah"
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <option :value="null">-- Pilih Tipe Kamar --</option>
                <template x-for="rt in hotelRoomTypesMadinah" :key="rt.id">
                  <option :value="rt.id" :selected="rt.id == form.id_hotel_room_type_madinah" x-text="`${rt.room_type_name} - Rp ${rt.price_per_night?.toLocaleString()}/malam`"></option>
                </template>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Check-in</label>
                <input type="date" x-model="form.madinah_check_in"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Check-out</label>
                <input type="date" x-model="form.madinah_check_out"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hotels - Multiple Hotels (Flexible Cities) -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-semibold">Hotel Tambahan (Opsional)</h2>
            <p class="text-xs text-slate-500">Tambahkan hotel untuk kota lain (Istanbul, Dubai, dll)</p>
          </div>
          <button type="button" @click="addHotel()"
                  class="inline-flex items-center gap-1 text-sm rounded-lg bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1.5 hover:bg-primary-100">
            <i class="bx bx-plus"></i> Tambah Hotel
          </button>
        </div>

        <template x-if="form.hotels && form.hotels.length > 0">
          <div class="space-y-3">
            <template x-for="(hotel, index) in form.hotels" :key="index">
              <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                <div class="flex items-start justify-between mb-3">
                  <div class="text-sm font-medium text-slate-700">Hotel <span x-text="index + 1"></span></div>
                  <button type="button" @click="removeHotel(index)"
                          class="text-red-600 hover:text-red-700">
                    <i class="bx bx-trash text-sm"></i>
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <!-- City Name -->
                  <div>
                    <label class="text-xs text-slate-600">Nama Kota</label>
                    <input type="text" x-model="hotel.city" placeholder="Contoh: Mekkah, Madinah, Jeddah"
                           class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                  </div>

                  <!-- Hotel Selection -->
                  <div>
                    <label class="text-xs text-slate-600">Pilih Hotel</label>
                    <select x-model="hotel.id_hotel" @change="updateHotelDetails(index)"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                      <option value="">-- Pilih Hotel --</option>
                      <template x-for="h in hotels" :key="h.id">
                        <option :value="h.id" :selected="h.id == hotel.id_hotel" x-text="`${h.hotel_name} - ${h.location}${h.seller_name ? ' | ' + h.seller_name : ''}`"></option>
                      </template>
                    </select>
                  </div>

                  <!-- Room Type Selection -->
                  <div x-show="hotel.id_hotel && hotel.room_types && hotel.room_types.length > 0">
                    <label class="text-xs text-slate-600">Tipe Kamar</label>
                    <select x-model="hotel.id_room_type"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                      <option value="">-- Pilih Tipe Kamar --</option>
                      <template x-for="rt in hotel.room_types" :key="rt.id">
                        <option :value="rt.id" :selected="rt.id == hotel.id_room_type" x-text="`${rt.room_type_name} - Rp ${formatNumber(rt.price_per_night)}/malam`"></option>
                      </template>
                    </select>
                  </div>

                  <!-- Check-in Date -->
                  <div>
                    <label class="text-xs text-slate-600">Check-in</label>
                    <input type="date" x-model="hotel.check_in" @change="calculateNights(index)"
                           class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                  </div>

                  <!-- Check-out Date -->
                  <div>
                    <label class="text-xs text-slate-600">Check-out</label>
                    <input type="date" x-model="hotel.check_out" @change="calculateNights(index)"
                           class="mt-1 w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm">
                  </div>

                  <!-- Nights Display -->
                  <div x-show="hotel.nights > 0">
                    <label class="text-xs text-slate-600">Jumlah Malam</label>
                    <div class="mt-1 text-sm font-medium text-slate-700" x-text="hotel.nights + ' malam'"></div>
                  </div>
                </div>

                <!-- Hotel Details Display -->
                <template x-if="hotel.id_hotel && hotel.hotel_name">
                  <div class="mt-3 text-xs text-slate-600 space-y-0.5 pl-2 border-l-2 border-primary-200">
                    <div><span class="font-medium">Hotel:</span> <span x-text="hotel.hotel_name"></span></div>
                    <div x-show="hotel.location"><span class="font-medium">Lokasi:</span> <span x-text="hotel.location"></span></div>
                    <div x-show="hotel.star_rating"><span class="font-medium">Bintang:</span> <span x-text="hotel.star_rating"></span></div>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </template>

        <template x-if="!form.hotels || form.hotels.length === 0">
          <div class="text-xs text-slate-500 italic p-4 rounded-lg border border-dashed border-slate-200 text-center">
            Belum ada hotel ditambahkan. Klik "Tambah Hotel" untuk menambahkan.
          </div>
        </template>
      </div>

      <!-- Saudi Transport (Multiple) -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <h2 class="text-lg font-semibold mb-4">Transportasi Saudi</h2>
        <p class="text-xs text-slate-500 mb-4">Tambahkan transportasi untuk Mekkah dan Madinah secara terpisah</p>
        
        <!-- Mekkah Transports -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-3">
            <label class="block text-sm font-medium text-slate-700">🕋 Transportasi Mekkah</label>
            <button type="button" @click="addSaudiTransport('makkah')"
                    class="inline-flex items-center gap-1 text-xs rounded-lg bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1.5 hover:bg-primary-100">
              <i class="bx bx-plus"></i> Tambah Transport
            </button>
          </div>
          
          <template x-if="form.saudi_transports.makkah && form.saudi_transports.makkah.length > 0">
            <div class="space-y-2">
              <template x-for="(transport, index) in form.saudi_transports.makkah" :key="index">
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                  <div class="flex items-start justify-between mb-2">
                    <select x-model="transport.id" @change="updateTransportDetails('makkah', index)"
                            class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm focus:ring-2 focus:ring-primary-200">
                      <option value="">-- Pilih Transportasi --</option>
                      <template x-for="t in saudiTransports" :key="t.id">
                        <option :value="t.id" x-text="`${t.transport_name} (${t.type_label})${t.route ? ' - ' + t.route : ''}`"></option>
                      </template>
                    </select>
                    <button type="button" @click="removeSaudiTransport('makkah', index)"
                            class="ml-2 p-1.5 rounded-lg text-red-500 hover:bg-red-50">
                      <i class="bx bx-trash text-sm"></i>
                    </button>
                  </div>
                  <template x-if="transport.id && transport.name">
                    <div class="text-xs text-slate-600 space-y-0.5 pl-2 border-l-2 border-primary-200">
                      <div><span class="font-medium">Nama:</span> <span x-text="transport.name"></span></div>
                      <div><span class="font-medium">Kapasitas:</span> <span x-text="transport.capacity"></span> orang</div>
                      <div><span class="font-medium">Harga:</span> Rp <span x-text="formatNumber(transport.price)"></span>/orang</div>
                    </div>
                  </template>
                </div>
              </template>
            </div>
          </template>
          
          <template x-if="!form.saudi_transports.makkah || form.saudi_transports.makkah.length === 0">
            <div class="text-xs text-slate-500 italic p-3 rounded-lg border border-dashed border-slate-200 text-center">
              Belum ada transportasi untuk Mekkah
            </div>
          </template>
        </div>
        
        <!-- Madinah Transports -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <label class="block text-sm font-medium text-slate-700">🕌 Transportasi Madinah</label>
            <button type="button" @click="addSaudiTransport('madinah')"
                    class="inline-flex items-center gap-1 text-xs rounded-lg bg-primary-50 text-primary-700 border border-primary-200 px-3 py-1.5 hover:bg-primary-100">
              <i class="bx bx-plus"></i> Tambah Transport
            </button>
          </div>
          
          <template x-if="form.saudi_transports.madinah && form.saudi_transports.madinah.length > 0">
            <div class="space-y-2">
              <template x-for="(transport, index) in form.saudi_transports.madinah" :key="index">
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                  <div class="flex items-start justify-between mb-2">
                    <select x-model="transport.id" @change="updateTransportDetails('madinah', index)"
                            class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm focus:ring-2 focus:ring-primary-200">
                      <option value="">-- Pilih Transportasi --</option>
                      <template x-for="t in saudiTransports" :key="t.id">
                        <option :value="t.id" x-text="`${t.transport_name} (${t.type_label})${t.route ? ' - ' + t.route : ''}`"></option>
                      </template>
                    </select>
                    <button type="button" @click="removeSaudiTransport('madinah', index)"
                            class="ml-2 p-1.5 rounded-lg text-red-500 hover:bg-red-50">
                      <i class="bx bx-trash text-sm"></i>
                    </button>
                  </div>
                  <template x-if="transport.id && transport.name">
                    <div class="text-xs text-slate-600 space-y-0.5 pl-2 border-l-2 border-primary-200">
                      <div><span class="font-medium">Nama:</span> <span x-text="transport.name"></span></div>
                      <div><span class="font-medium">Kapasitas:</span> <span x-text="transport.capacity"></span> orang</div>
                      <div><span class="font-medium">Harga:</span> Rp <span x-text="formatNumber(transport.price)"></span>/orang</div>
                    </div>
                  </template>
                </div>
              </template>
            </div>
          </template>
          
          <template x-if="!form.saudi_transports.madinah || form.saudi_transports.madinah.length === 0">
            <div class="text-xs text-slate-500 italic p-3 rounded-lg border border-dashed border-slate-200 text-center">
              Belum ada transportasi untuk Madinah
            </div>
          </template>
        </div>
      </div>

      <!-- Package Photos -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <h2 class="text-lg font-semibold mb-4">Foto Paket</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Gambar Utama</label>
            
            <!-- Preview Gambar yang Sudah Ada dengan Crop Settings -->
            @if($package->image_path)
            <div class="mb-3 border border-slate-200 rounded-lg p-3 bg-slate-50">
              <p class="text-xs font-medium text-slate-700 mb-2">Preview Thumbnail Homepage (Hasil Crop):</p>
              <div class="relative bg-white rounded-lg overflow-hidden" style="width: 100%; max-width: 400px; aspect-ratio: 16/9;">
                @php
                  $cropData = $package->thumbnail_crop_settings ?? null;
                  if ($cropData && is_array($cropData)) {
                    // Cropper.js format: x, y, width, height, rotate, scaleX, scaleY
                    $imageUrl = asset('storage/' . $package->image_path);
                  }
                @endphp
                @if($cropData)
                <img src="{{ asset('storage/' . $package->image_path) }}" 
                     alt="Cropped preview"
                     class="crop-preview-image"
                     data-crop='@json($cropData)'
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onload="applyCropToPreview(this)">
                @else
                <img src="{{ asset('storage/' . $package->image_path) }}" alt="Current image" class="w-full h-full object-cover">
                @endif
              </div>
              <div class="mt-2 flex items-center justify-between">
                <p class="text-xs text-slate-500">Gambar saat ini {{ $cropData ? '(dengan crop settings)' : '' }}</p>
                <button type="button" @click="editExistingCrop()" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                  <i class="bx bx-edit"></i> Edit Crop
                </button>
              </div>
            </div>
            @endif
            
            <input type="file" @change="handleMainImageUpload" accept="image/jpeg,image/png,image/jpg" id="main-image-input-edit"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Upload gambar baru atau klik "Edit Crop" untuk mengubah posisi gambar yang ada.</p>
            <p x-show="errors.image" x-text="errors.image" class="text-red-500 text-sm mt-1"></p>
            
            <!-- Image Cropper Preview -->
            <div x-show="imagePreviewUrl" class="mt-4">
              <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-slate-700">Atur Posisi Thumbnail untuk Homepage</label>
                  <button type="button" @click="resetCrop()" class="text-xs text-slate-600 hover:text-slate-800">
                    <i class="bx bx-reset"></i> Reset
                  </button>
                </div>
                
                <!-- Cropper Container -->
                <div class="relative bg-slate-100 rounded-lg overflow-hidden" style="max-height: 400px;">
                  <img id="crop-image-edit" :src="imagePreviewUrl" style="max-width: 100%; display: block;">
                </div>
                
                <!-- Crop Controls -->
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                  <button type="button" @click="cropperZoomIn()" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm">
                    <i class="bx bx-zoom-in"></i> Zoom In
                  </button>
                  <button type="button" @click="cropperZoomOut()" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm">
                    <i class="bx bx-zoom-out"></i> Zoom Out
                  </button>
                  <button type="button" @click="cropperRotateLeft()" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm">
                    <i class="bx bx-rotate-left"></i> Putar Kiri
                  </button>
                  <button type="button" @click="cropperRotateRight()" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm">
                    <i class="bx bx-rotate-right"></i> Putar Kanan
                  </button>
                </div>
                
                <!-- Tombol Simpan Crop -->
                <div class="mt-4 flex items-center justify-between">
                  <p class="text-xs text-slate-400">
                    💡 Drag gambar untuk menggeser posisi, gunakan scroll mouse untuk zoom
                  </p>
                  <button type="button" @click="saveCropSettings()" 
                          class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                    <i class="bx bx-check"></i> Simpan Crop
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Preview Hasil Crop -->
            <div x-show="cropPreviewUrl" class="mt-4">
              <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-green-900">✓ Preview Thumbnail Homepage (Hasil Crop)</label>
                  <span class="text-xs text-green-600 font-medium">Crop tersimpan</span>
                </div>
                <div class="relative bg-white rounded-lg overflow-hidden" style="width: 100%; max-width: 400px; aspect-ratio: 16/9;">
                  <img :src="cropPreviewUrl" alt="Crop preview" class="w-full h-full object-cover">
                </div>
                <p class="text-xs text-green-600 mt-2">
                  <i class="bx bx-info-circle"></i> Ini adalah tampilan thumbnail yang akan muncul di homepage
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Handling & Lounge Fee Settings -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
            <i class="bx bx-plane-departure text-yellow-600 text-xl"></i>
          </div>
          <div>
            <h2 class="text-lg font-semibold">Handling & Lounge Fee</h2>
            <p class="text-sm text-slate-600">Biaya tambahan yang akan ditambahkan ke total harga paket</p>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Toggle Enable/Disable -->
          <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200">
            <input type="checkbox" 
                   x-model="form.include_handling_lounge_fee" 
                   id="include_handling_lounge_fee"
                   class="mt-1 w-5 h-5 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
            <div class="flex-1">
              <label for="include_handling_lounge_fee" class="font-medium text-slate-900 cursor-pointer">
                Aktifkan Handling & Lounge Fee
              </label>
              <p class="text-sm text-slate-600 mt-1">
                Jika diaktifkan, biaya ini akan otomatis ditambahkan ke total harga paket dan muncul di semua dokumen (invoice, kwitansi, dll)
              </p>
            </div>
          </div>

          <!-- Fee Settings (shown when enabled) -->
          <div x-show="form.include_handling_lounge_fee" 
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 transform scale-95"
               x-transition:enter-end="opacity-100 transform scale-100"
               class="space-y-4 pl-8">
            
            <!-- Amount -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Nominal Fee <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                <input type="number" 
                       x-model.number="form.handling_lounge_fee_amount"
                       class="w-full rounded-lg border border-slate-200 pl-10 pr-3 py-2 focus:ring-2 focus:ring-yellow-500"
                       placeholder="500000"
                       min="0"
                       step="1000"
                       :required="form.include_handling_lounge_fee">
              </div>
              <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-slate-500">
                  Default: Rp 500.000 per booking (bukan per orang)
                </p>
                <p class="text-sm font-semibold text-yellow-700" x-show="form.handling_lounge_fee_amount > 0" x-text="'Rp ' + formatNumber(form.handling_lounge_fee_amount)"></p>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                Deskripsi (Opsional)
              </label>
              <textarea x-model="form.handling_lounge_fee_description"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-yellow-500"
                        rows="2"
                        placeholder="Handling & Lounge Fee Wajib"></textarea>
              <p class="text-xs text-slate-500 mt-1">
                Kosongkan untuk menggunakan teks default. Deskripsi ini akan muncul di halaman detail paket dan invoice.
              </p>
            </div>

            <!-- Preview -->
            <div class="p-4 rounded-lg bg-white border border-yellow-200">
              <p class="text-xs font-semibold text-yellow-700 mb-2">📋 Preview Tampilan:</p>
              <div class="text-sm">
                <div class="font-medium text-slate-900" x-text="form.handling_lounge_fee_description || 'Handling & Lounge Fee Wajib'"></div>
                <div class="text-yellow-700 font-bold mt-1" x-text="'Rp ' + formatNumber(form.handling_lounge_fee_amount || 500000)"></div>
              </div>
            </div>

            <!-- Info Box -->
            <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 border border-blue-200">
              <i class="bx bx-info-circle text-blue-600 text-lg mt-0.5"></i>
              <div class="text-xs text-blue-700">
                <p class="font-semibold mb-1">Informasi Penting:</p>
                <ul class="list-disc list-inside space-y-0.5">
                  <li>Fee ini dikenakan <strong>per booking</strong>, bukan per orang</li>
                  <li>Otomatis muncul di halaman detail paket (homepage)</li>
                  <li>Otomatis terkalkulasi ke total harga</li>
                  <li>Otomatis muncul di invoice, kwitansi, dan semua dokumen</li>
                  <li>Hanya berlaku untuk booking baru setelah pengaturan ini disimpan</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3">
        <a href="{{ route('admin.inventaris.travel.package.index') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-slate-200 text-slate-700 px-6 py-2 hover:bg-slate-300">
          <i class="bx bx-x"></i> Batal
        </a>
        <button type="submit" :disabled="submitting"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-6 py-2 hover:bg-primary-700 disabled:opacity-50">
          <i class="bx bx-save" x-show="!submitting"></i>
          <i class="bx bx-loader-alt bx-spin" x-show="submitting"></i>
          <span x-text="submitting ? 'Menyimpan...' : 'Update Paket'"></span>
        </button>
      </div>
    </form>
  </div>

  @push('scripts')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script>
    function packageEdit() {
      return {
        submitting: false,
        flights: [],
        flightGroups: [],
        hotels: [],
        saudiTransports: [],
        hotelRoomTypesAll: [],
        hotelRoomTypesMakkah: [],
        hotelRoomTypesMadinah: [],
        imagePreviewUrl: null,
        cropPreviewUrl: null,
        cropper: null,
        form: {
          package_code: '{{ $package->package_code }}',
          package_name: '{{ $package->package_name }}',
          package_type: '{{ $package->package_type ?? 'umrah' }}',
          package_subtype: '{{ $package->package_subtype ?? '' }}',
          description: `{{ $package->description ?? '' }}`,
          ustadz_name: '{{ $package->ustadz_name ?? '' }}',
          inclusions: `{{ $package->inclusions ?? '' }}`,
          duration_days: {{ $package->duration_days }},
          departure_date: '{{ $package->departure_date ? $package->departure_date->format('Y-m-d') : '' }}',
          return_date: '{{ $package->return_date ? $package->return_date->format('Y-m-d') : '' }}',
          // Flight Information
          flight_group_code: '',
          id_flight_departure: {{ $package->id_flight_departure ?? 'null' }},
          departure_datetime: '{{ $package->departure_datetime ? $package->departure_datetime->format('Y-m-d\TH:i') : '' }}',
          id_flight_return: {{ $package->id_flight_return ?? 'null' }},
          return_datetime: '{{ $package->return_datetime ? $package->return_datetime->format('Y-m-d\TH:i') : '' }}',
          // Hotel Makkah
          id_hotel_makkah: {{ $package->id_hotel_makkah ?? 'null' }},
          id_hotel_room_type_makkah: {{ $package->id_hotel_room_type_makkah ?? 'null' }},
          makkah_check_in: '{{ $package->makkah_check_in ? $package->makkah_check_in->format('Y-m-d') : '' }}',
          makkah_check_out: '{{ $package->makkah_check_out ? $package->makkah_check_out->format('Y-m-d') : '' }}',
          // Hotel Madinah
          id_hotel_madinah: {{ $package->id_hotel_madinah ?? 'null' }},
          id_hotel_room_type_madinah: {{ $package->id_hotel_room_type_madinah ?? 'null' }},
          madinah_check_in: '{{ $package->madinah_check_in ? $package->madinah_check_in->format('Y-m-d') : '' }}',
          madinah_check_out: '{{ $package->madinah_check_out ? $package->madinah_check_out->format('Y-m-d') : '' }}',
          // Saudi Transport
          id_saudi_transport: {{ $package->id_saudi_transport ?? 'null' }},
          saudi_transports: (function() {
            try {
              const raw = @json($package->saudi_transports ?? null);
              if (raw && typeof raw === 'object' && (raw.makkah || raw.madinah)) {
                return {
                  makkah: Array.isArray(raw.makkah) ? raw.makkah : [],
                  madinah: Array.isArray(raw.madinah) ? raw.madinah : []
                };
              }
            } catch(e) {}
            return { makkah: [], madinah: [] };
          })(),
          // Hotels
          hotels: (function() {
            try {
              const raw = @json($package->hotels ?? null);
              if (raw && Array.isArray(raw)) {
                return raw.map(h => ({
                  city: h.city || '',
                  id_hotel: h.id_hotel || '',
                  id_room_type: h.id_room_type || '',
                  check_in: h.check_in || '',
                  check_out: h.check_out || '',
                  nights: h.nights || 0,
                  hotel_name: h.hotel_name || '',
                  location: h.location || '',
                  star_rating: h.star_rating || '',
                  room_types: h.room_types || []
                }));
              }
            } catch(e) {}
            return [];
          })(),
          // Other fields
          capacity: {{ $package->capacity }},
          price: {{ $package->price }},
          status: '{{ $package->status }}',
          is_promo: {{ $package->is_promo ? 'true' : 'false' }},
          is_best_seller: {{ $package->is_best_seller ? 'true' : 'false' }},
          id_outlet: {{ $package->id_outlet }},
          image: null,
          thumbnail_crop: {!! json_encode($package->thumbnail_crop_settings ?? ['x' => 0, 'y' => 0, 'width' => 0, 'height' => 0, 'rotate' => 0, 'scaleX' => 1, 'scaleY' => 1]) !!},
          price_packages: (function() {
            try {
              const raw = @json($package->price_packages ?? null);
              if (raw && Array.isArray(raw) && raw.length > 0) return raw;
            } catch(e) {}
            return [{ name: 'Reguler', variants: [{ type: 'quad', price: 0 }, { type: 'triple', price: 0 }, { type: 'double', price: 0 }] }];
          })(),
          // Handling & Lounge Fee
          include_handling_lounge_fee: {{ $package->include_handling_lounge_fee ? 'true' : 'false' }},
          handling_lounge_fee_amount: {{ $package->handling_lounge_fee_amount ?? 500000 }},
          handling_lounge_fee_description: `{{ $package->handling_lounge_fee_description ?? '' }}`
        },
        errors: {},

        async init() {
          console.log('Package edit form initialized');
          
          await this.loadFlights();
          await this.loadFlightGroups();
          await this.loadHotels();
          await this.loadSaudiTransports();
          
          // Load room types for Makkah hotel if selected
          if (this.form.id_hotel_makkah) {
            const hotel = this.hotels.find(h => h.id == this.form.id_hotel_makkah);
            this.hotelRoomTypesMakkah = hotel ? (hotel.room_types || []) : [];
          }
          
          // Load room types for Madinah hotel if selected
          if (this.form.id_hotel_madinah) {
            const hotel = this.hotels.find(h => h.id == this.form.id_hotel_madinah);
            this.hotelRoomTypesMadinah = hotel ? (hotel.room_types || []) : [];
          }
          
          await this.$nextTick();
        },

        async loadFlights() {
          try {
            const url = '{{ route('admin.inventaris.travel.package.hpp.flights') }}';
            const params = this.form.id_outlet ? `?outlet_id=${this.form.id_outlet}` : '';
            const response = await fetch(url + params);
            const data = await response.json();
            // HPP endpoint returns array directly with departure_time, arrival_time, route
            this.flights = Array.isArray(data) ? data : (data.data || []);
          } catch (error) {
            console.error('Error loading flights:', error);
          }
        },

        async loadFlightGroups() {
          try {
            const response = await fetch('{{ route("admin.inventaris.travel.package.flight-groups") }}');
            const data = await response.json();
            this.flightGroups = data || [];
          } catch (error) {
            console.error('Error loading flight groups:', error);
          }
        },

        async loadHotels() {
          try {
            const response = await fetch('{{ route('admin.inventaris.travel.package.hpp.hotels') }}');
            const data = await response.json();
            this.hotels = data || [];
          } catch (error) {
            console.error('Error loading hotels:', error);
          }
        },

        async loadSaudiTransports() {
          try {
            const response = await fetch('{{ route('admin.inventaris.travel.package.hpp.saudi-transports') }}');
            const data = await response.json();
            this.saudiTransports = data || [];
          } catch (error) {
            console.error('Error loading saudi transports:', error);
          }
        },

        async onFlightDepartureChange() {
          if (this.form.id_flight_departure) {
            const flight = this.flights.find(f => f.id == this.form.id_flight_departure);
            if (flight && flight.departure_time) {
              this.form.departure_datetime = flight.departure_time;
            }
          } else {
            this.form.departure_datetime = '';
          }
        },

        async onFlightReturnChange() {
          if (this.form.id_flight_return) {
            const flight = this.flights.find(f => f.id == this.form.id_flight_return);
            if (flight && flight.arrival_time) {
              this.form.return_datetime = flight.arrival_time;
            }
          } else {
            this.form.return_datetime = '';
          }
        },

        async onFlightGroupChange() {
          if (!this.form.flight_group_code) {
            return;
          }

          // Find departure and return flights in this group
          const departureFlight = this.flights.find(f => 
            f.flight_group_code === this.form.flight_group_code && f.flight_direction === 'departure'
          );
          const returnFlight = this.flights.find(f => 
            f.flight_group_code === this.form.flight_group_code && f.flight_direction === 'return'
          );

          // Auto-fill both flights
          if (departureFlight) {
            this.form.id_flight_departure = departureFlight.id;
            this.form.departure_datetime = departureFlight.departure_time || '';
          }
          if (returnFlight) {
            this.form.id_flight_return = returnFlight.id;
            this.form.return_datetime = returnFlight.arrival_time || '';
          }

          console.log('Flight group selected:', this.form.flight_group_code);
          console.log('Departure:', departureFlight);
          console.log('Return:', returnFlight);
        },

        clearFlightGroup() {
          this.form.flight_group_code = '';
          this.form.id_flight_departure = null;
          this.form.departure_datetime = '';
          this.form.id_flight_return = null;
          this.form.return_datetime = '';
        },

        async onHotelMakkahChange() {
          if (this.form.id_hotel_makkah) {
            const hotel = this.hotels.find(h => h.id == this.form.id_hotel_makkah);
            this.hotelRoomTypesMakkah = hotel ? (hotel.room_types || []) : [];
            this.form.id_hotel_room_type_makkah = null;
          } else {
            this.hotelRoomTypesMakkah = [];
            this.form.id_hotel_room_type_makkah = null;
          }
        },

        async onHotelMadinahChange() {
          if (this.form.id_hotel_madinah) {
            const hotel = this.hotels.find(h => h.id == this.form.id_hotel_madinah);
            this.hotelRoomTypesMadinah = hotel ? (hotel.room_types || []) : [];
            this.form.id_hotel_room_type_madinah = null;
          } else {
            this.hotelRoomTypesMadinah = [];
            this.form.id_hotel_room_type_madinah = null;
          }
        },

        async loadHotelRoomTypes(hotelId, location) {
          // kept for backward compat
        },

        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num);
        },

        addPricePackage() {
          this.form.price_packages.push({
            name: 'Reguler',
            variants: [{ type: 'quad', price: 0 }, { type: 'triple', price: 0 }, { type: 'double', price: 0 }]
          });
        },

        removePricePackage(idx) {
          this.form.price_packages.splice(idx, 1);
        },

        addSaudiTransport(city) {
          if (!this.form.saudi_transports[city]) {
            this.form.saudi_transports[city] = [];
          }
          this.form.saudi_transports[city].push({
            id: '',
            name: '',
            capacity: 0,
            price: 0
          });
        },

        removeSaudiTransport(city, index) {
          this.form.saudi_transports[city].splice(index, 1);
        },

        updateTransportDetails(city, index) {
          const transport = this.form.saudi_transports[city][index];
          if (transport.id) {
            const selected = this.saudiTransports.find(t => t.id == transport.id);
            if (selected) {
              transport.name = selected.transport_name;
              transport.capacity = selected.capacity;
              transport.price = selected.price_per_person;
            }
          }
        },

        addHotel() {
          this.form.hotels.push({
            city: '',
            id_hotel: '',
            id_room_type: '',
            check_in: '',
            check_out: '',
            nights: 0,
            hotel_name: '',
            location: '',
            star_rating: '',
            room_types: []
          });
        },

        removeHotel(index) {
          this.form.hotels.splice(index, 1);
        },

        updateHotelDetails(index) {
          const hotel = this.form.hotels[index];
          if (hotel.id_hotel) {
            const selected = this.hotels.find(h => h.id == hotel.id_hotel);
            if (selected) {
              hotel.hotel_name = selected.hotel_name;
              hotel.location = selected.location;
              hotel.star_rating = selected.star_rating;
              hotel.room_types = selected.room_types || [];
              hotel.id_room_type = ''; // Reset room type selection
            }
          } else {
            hotel.hotel_name = '';
            hotel.location = '';
            hotel.star_rating = '';
            hotel.room_types = [];
            hotel.id_room_type = '';
          }
          this.calculateNights(index);
        },

        calculateNights(index) {
          const hotel = this.form.hotels[index];
          if (hotel.check_in && hotel.check_out) {
            const checkIn = new Date(hotel.check_in);
            const checkOut = new Date(hotel.check_out);
            const diffTime = checkOut - checkIn;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            hotel.nights = diffDays > 0 ? diffDays : 0;
          } else {
            hotel.nights = 0;
          }
        },

        handleMainImageUpload(event) {
          const file = event.target.files[0];
          if (file) {
            this.form.image = file;
            
            // Create preview URL
            this.imagePreviewUrl = URL.createObjectURL(file);
            
            // Wait for next tick to ensure image is rendered
            this.$nextTick(() => {
              this.initCropper();
            });
          }
        },

        initCropper() {
          const image = document.getElementById('crop-image-edit');
          if (!image) return;
          
          // Destroy existing cropper if any
          if (this.cropper) {
            this.cropper.destroy();
          }
          
          // Initialize Cropper.js
          this.cropper = new Cropper(image, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
            ready: () => {
              // Load saved crop data if exists
              if (this.form.thumbnail_crop && this.form.thumbnail_crop.cropData) {
                this.cropper.setData(this.form.thumbnail_crop.cropData);
              }
            },
            crop: (event) => {
              // Save crop data
              this.form.thumbnail_crop = {
                x: Math.round(event.detail.x),
                y: Math.round(event.detail.y),
                width: Math.round(event.detail.width),
                height: Math.round(event.detail.height),
                rotate: Math.round(event.detail.rotate),
                scaleX: event.detail.scaleX,
                scaleY: event.detail.scaleY,
                cropData: this.cropper.getData()
              };
            }
          });
        },

        cropperZoomIn() {
          if (this.cropper) {
            this.cropper.zoom(0.1);
          }
        },

        cropperZoomOut() {
          if (this.cropper) {
            this.cropper.zoom(-0.1);
          }
        },

        cropperRotateLeft() {
          if (this.cropper) {
            this.cropper.rotate(-90);
          }
        },

        cropperRotateRight() {
          if (this.cropper) {
            this.cropper.rotate(90);
          }
        },

        resetCrop() {
          if (this.cropper) {
            this.cropper.reset();
            this.form.thumbnail_crop = {
              x: 0,
              y: 0,
              width: 0,
              height: 0,
              rotate: 0,
              scaleX: 1,
              scaleY: 1
            };
            this.cropPreviewUrl = null;
          }
        },

        saveCropSettings() {
          if (!this.cropper) {
            alert('Tidak ada cropper aktif');
            return;
          }
          
          // Get cropped canvas
          const canvas = this.cropper.getCroppedCanvas({
            width: 800,
            height: 450,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
          });
          
          if (canvas) {
            // Generate preview URL
            this.cropPreviewUrl = canvas.toDataURL('image/jpeg', 0.9);
            
            // Save crop data
            const cropData = this.cropper.getData();
            this.form.thumbnail_crop = {
              x: Math.round(cropData.x),
              y: Math.round(cropData.y),
              width: Math.round(cropData.width),
              height: Math.round(cropData.height),
              rotate: Math.round(cropData.rotate),
              scaleX: cropData.scaleX,
              scaleY: cropData.scaleY
            };
            
            console.log('Crop settings saved:', this.form.thumbnail_crop);
            
            // Show success message
            alert('✓ Crop berhasil disimpan! Preview thumbnail sudah muncul di bawah.');
          }
        },

        editExistingCrop() {
          // Load existing image for cropping
          const existingImagePath = '{{ $package->image_path ?? '' }}';
          if (!existingImagePath) {
            alert('Tidak ada gambar untuk di-crop');
            return;
          }
          
          // Set preview URL to existing image
          this.imagePreviewUrl = '{{ asset('storage/' . ($package->image_path ?? '')) }}';
          
          // Wait for next tick to ensure image is rendered
          this.$nextTick(() => {
            this.initCropper();
            
            // Load existing crop data if available
            if (this.form.thumbnail_crop && this.form.thumbnail_crop.x !== undefined) {
              setTimeout(() => {
                if (this.cropper) {
                  this.cropper.setData({
                    x: this.form.thumbnail_crop.x || 0,
                    y: this.form.thumbnail_crop.y || 0,
                    width: this.form.thumbnail_crop.width || 0,
                    height: this.form.thumbnail_crop.height || 0,
                    rotate: this.form.thumbnail_crop.rotate || 0,
                    scaleX: this.form.thumbnail_crop.scaleX || 1,
                    scaleY: this.form.thumbnail_crop.scaleY || 1
                  });
                }
              }, 100);
            }
          });
        },

        async submitForm() {
          this.submitting = true;
          this.errors = {};

          try {
            const formData = new FormData();
            
            // Append all form fields
            Object.keys(this.form).forEach(key => {
              if (key === 'thumbnail_crop') {
                formData.append('thumbnail_crop_settings', JSON.stringify(this.form.thumbnail_crop));
              } else if (key === 'price_packages') {
                formData.append('price_packages', JSON.stringify(this.form.price_packages));
                // Set price from max variant of first package
                const firstPkg = this.form.price_packages[0];
                if (firstPkg && firstPkg.variants) {
                  const maxPrice = Math.max(...firstPkg.variants.map(v => v.price || 0));
                  formData.append('price', maxPrice || 0);
                }
              } else if (key === 'id_saudi_transport') {
                if (this.form.id_saudi_transport) {
                  formData.append('id_saudi_transport', this.form.id_saudi_transport);
                }
              } else if (key === 'saudi_transports') {
                // Handle saudi_transports - filter out empty transports
                const filtered = {
                  makkah: (this.form.saudi_transports.makkah || []).filter(t => t.id),
                  madinah: (this.form.saudi_transports.madinah || []).filter(t => t.id)
                };
                formData.append('saudi_transports', JSON.stringify(filtered));
              } else if (key === 'hotels') {
                // Handle hotels - filter out empty hotels
                const filtered = (this.form.hotels || []).filter(h => h.id_hotel && h.city);
                formData.append('hotels', JSON.stringify(filtered));
              } else if (this.form[key] !== null && this.form[key] !== '' && this.form[key] !== undefined) {
                formData.append(key, this.form[key]);
              }
            });
            
            // Explicitly append hotel fields
            // Only send hotel ID if selected
            if (this.form.id_hotel_makkah) {
              formData.append('id_hotel_makkah', this.form.id_hotel_makkah);
            }
            // Only send room type if selected AND valid
            if (this.form.id_hotel_room_type_makkah && this.form.id_hotel_room_type_makkah > 0) {
              formData.append('id_hotel_room_type_makkah', this.form.id_hotel_room_type_makkah);
            }
            if (this.form.makkah_check_in) {
              formData.append('makkah_check_in', this.form.makkah_check_in);
            }
            if (this.form.makkah_check_out) {
              formData.append('makkah_check_out', this.form.makkah_check_out);
            }
            
            if (this.form.id_hotel_madinah) {
              formData.append('id_hotel_madinah', this.form.id_hotel_madinah);
            }
            // Only send room type if selected AND valid
            if (this.form.id_hotel_room_type_madinah && this.form.id_hotel_room_type_madinah > 0) {
              formData.append('id_hotel_room_type_madinah', this.form.id_hotel_room_type_madinah);
            }
            if (this.form.madinah_check_in) {
              formData.append('madinah_check_in', this.form.madinah_check_in);
            }
            if (this.form.madinah_check_out) {
              formData.append('madinah_check_out', this.form.madinah_check_out);
            }
            
            // Explicitly append flight fields
            if (this.form.id_flight_departure) {
              formData.append('id_flight_departure', this.form.id_flight_departure);
            }
            if (this.form.departure_datetime) {
              formData.append('departure_datetime', this.form.departure_datetime);
            }
            if (this.form.id_flight_return) {
              formData.append('id_flight_return', this.form.id_flight_return);
            }
            if (this.form.return_datetime) {
              formData.append('return_datetime', this.form.return_datetime);
            }

            // Add _method for PUT request
            formData.append('_method', 'PUT');
            
            // Debug: Log what we're sending
            console.log('Sending hotel data:');
            console.log('  id_hotel_makkah:', this.form.id_hotel_makkah);
            console.log('  id_hotel_room_type_makkah:', this.form.id_hotel_room_type_makkah);
            console.log('  id_hotel_madinah:', this.form.id_hotel_madinah);
            console.log('  id_hotel_room_type_madinah:', this.form.id_hotel_room_type_madinah);

            const response = await fetch('{{ route('admin.inventaris.travel.package.update', $package->id) }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              body: formData
            });

            const data = await response.json();

            if (response.ok) {
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: data.message || 'Paket berhasil diupdate',
                  showConfirmButton: false,
                  timer: 1500
                }).then(() => {
                  window.location.href = '{{ route('admin.inventaris.travel.package.index') }}';
                });
              } else {
                alert(data.message || 'Paket berhasil diupdate');
                window.location.href = '{{ route('admin.inventaris.travel.package.index') }}';
              }
            } else {
              if (data.errors) {
                this.errors = data.errors;
              }
              if (typeof Swal !== 'undefined') {
                Swal.fire('Error', data.message || 'Gagal mengupdate paket', 'error');
              } else {
                alert(data.message || 'Gagal mengupdate paket');
              }
            }
          } catch (error) {
            console.error('Error submitting form:', error);
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', 'Terjadi kesalahan saat mengupdate paket', 'error');
            } else {
              alert('Terjadi kesalahan saat mengupdate paket');
            }
          } finally {
            this.submitting = false;
          }
        }
      };
    }
    
    // Function to apply crop to preview image
    function applyCropToPreview(img) {
      try {
        const cropData = JSON.parse(img.getAttribute('data-crop'));
        if (!cropData || !cropData.width || cropData.width === 0) return;
        if (!img.naturalWidth || !img.naturalHeight) return;
        
        // Calculate center of crop area as percentage
        const cropCenterX = cropData.x + (cropData.width / 2);
        const cropCenterY = cropData.y + (cropData.height / 2);
        const centerXPercent = (cropCenterX / img.naturalWidth) * 100;
        const centerYPercent = (cropCenterY / img.naturalHeight) * 100;
        
        // Apply styles
        img.style.objectFit = 'cover';
        img.style.objectPosition = centerXPercent + '% ' + centerYPercent + '%';
        
        if (cropData.rotate || cropData.scaleX !== 1 || cropData.scaleY !== 1) {
          img.style.transform = 'rotate(' + (cropData.rotate || 0) + 'deg) scaleX(' + (cropData.scaleX || 1) + ') scaleY(' + (cropData.scaleY || 1) + ')';
        }
      } catch (e) {
        console.error('Error applying crop:', e);
      }
    }
  </script>
  @endpush
</x-layouts.admin>
