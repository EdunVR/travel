<x-layouts.admin :title="'Detail Keberangkatan'">
  <div x-data="keberangkatanDetail()" x-init="init()" class="space-y-4 self-start w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2">
          <a href="{{ route('admin.inventaris.travel.keberangkatan.index') }}" class="p-2 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-arrow-back text-xl'></i>
          </a>
          <div>
            <h1 class="text-2xl font-bold" x-text="keberangkatan.keberangkatan_name"></h1>
            <p class="text-slate-600 text-sm font-mono" x-text="keberangkatan.keberangkatan_code"></p>
          </div>
        </div>
      </div>
      <div class="flex gap-2">
        <!-- Refresh Button -->
        <button x-on:click="refreshAllData()" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white text-slate-700 px-4 py-2 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                title="Refresh data keberangkatan">
          <i class='bx bx-refresh text-lg' :class="{'bx-spin': loading}"></i>
          <span class="hidden sm:inline">Refresh</span>
        </button>
        
        @hasPermission('travel.keberangkatan.create')
        <button x-show="!keberangkatan.id_rab && !creatingRab" x-on:click="createRab()" 
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-4 py-2 hover:bg-green-700">
          <i class='bx bx-calculator'></i> Buat RAB
        </button>
        <button x-show="creatingRab" disabled
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-4 py-2 opacity-50 cursor-not-allowed">
          <i class='bx bx-loader-alt bx-spin'></i> Membuat RAB...
        </button>
        @endhasPermission
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    <!-- Content -->
    <div x-show="!loading" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Left Column: Main Info -->
      <div class="lg:col-span-2 space-y-4">
        <!-- Basic Info Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <h2 class="font-semibold text-lg mb-4">Informasi Keberangkatan</h2>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="text-sm text-slate-600">Paket Travel</div>
              <div class="font-medium" x-text="keberangkatan.package_name"></div>
            </div>
            <div>
              <div class="text-sm text-slate-600">Status</div>
              <div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="{
                        'bg-gray-100 text-gray-800': keberangkatan.status === 'planning',
                        'bg-green-100 text-green-800': keberangkatan.status === 'confirmed',
                        'bg-blue-100 text-blue-800': keberangkatan.status === 'departed',
                        'bg-purple-100 text-purple-800': keberangkatan.status === 'completed'
                      }"
                      x-text="keberangkatan.status?.toUpperCase()"></span>
              </div>
            </div>
            <div>
              <div class="text-sm text-slate-600">Tanggal Keberangkatan</div>
              <div class="font-medium" x-text="keberangkatan.departure_date"></div>
            </div>
            <div>
              <div class="text-sm text-slate-600">Tanggal Kembali</div>
              <div class="font-medium" x-text="keberangkatan.return_date"></div>
            </div>
            <div>
              <div class="text-sm text-slate-600">Total Jamaah</div>
              <div class="font-medium">
                <span x-text="keberangkatan.confirmed_jamaah"></span> / 
                <span x-text="keberangkatan.total_jamaah"></span>
                <span class="text-sm text-slate-500">
                  (<span x-text="keberangkatan.available_capacity"></span> tersedia)
                </span>
              </div>
            </div>
            <div>
              <div class="text-sm text-slate-600">Outlet</div>
              <div class="font-medium" x-text="keberangkatan.outlet_name"></div>
            </div>
          </div>
        </div>

        <!-- RAB Card -->
        <div x-show="keberangkatan.id_rab" class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Rencana Anggaran Biaya (RAB)</h2>
            <button x-on:click="refreshRabData()" class="p-2 hover:bg-slate-100 rounded-lg">
              <i class='bx bx-refresh'></i>
            </button>
          </div>

          <div x-show="loadingRab" class="text-center py-4">
            <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
          </div>

          <div x-show="!loadingRab && rabData">
            <!-- RAB Summary -->
            <div class="grid grid-cols-3 gap-4 mb-4">
              <div class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                <div class="text-xs text-blue-600">Total Budget</div>
                <div class="text-lg font-bold text-blue-700" x-text="formatCurrency(rabData?.total_budget)"></div>
              </div>
              <div class="p-3 rounded-xl bg-green-50 border border-green-100">
                <div class="text-xs text-green-600">Disetujui</div>
                <div class="text-lg font-bold text-green-700" x-text="formatCurrency(rabData?.total_approved)"></div>
              </div>
              <div class="p-3 rounded-xl bg-purple-50 border border-purple-100">
                <div class="text-xs text-purple-600">Realisasi</div>
                <div class="text-lg font-bold text-purple-700" x-text="formatCurrency(rabData?.total_actual)"></div>
              </div>
            </div>

            <!-- RAB Details Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-700">
                  <tr>
                    <th class="text-left px-3 py-2">Item</th>
                    <th class="text-right px-3 py-2">Qty</th>
                    <th class="text-right px-3 py-2">Budget</th>
                    <th class="text-right px-3 py-2">Realisasi</th>
                    <th class="text-right px-3 py-2">Varians</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="detail in rabData?.details" :key="detail.id">
                    <tr class="border-t border-slate-100">
                      <td class="px-3 py-2">
                        <div class="font-medium" x-text="detail.item"></div>
                        <div class="text-xs text-slate-500" x-text="detail.description"></div>
                      </td>
                      <td class="px-3 py-2 text-right">
                        <span x-text="detail.qty"></span> <span class="text-xs text-slate-500" x-text="detail.unit"></span>
                      </td>
                      <td class="px-3 py-2 text-right font-medium" x-text="formatCurrency(detail.budget)"></td>
                      <td class="px-3 py-2 text-right" x-text="formatCurrency(detail.actual)"></td>
                      <td class="px-3 py-2 text-right">
                        <span :class="(detail.actual - detail.budget) > 0 ? 'text-red-600' : 'text-green-600'"
                              x-text="formatCurrency(detail.actual - detail.budget)"></span>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>

          <div x-show="!loadingRab && !rabData" class="text-center py-4 text-slate-500">
            RAB belum dibuat
          </div>
        </div>

        <!-- Budget Variance Card -->
        <div x-show="variance?.has_rab" class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <h2 class="font-semibold text-lg mb-4">Analisis Varians Budget</h2>

          <div x-show="loadingVariance" class="text-center py-4">
            <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
          </div>

          <div x-show="!loadingVariance && variance">
            <!-- Overall Variance -->
            <div class="p-4 rounded-xl mb-4"
                 :class="variance.has_warning ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm" :class="variance.has_warning ? 'text-red-600' : 'text-green-600'">
                    Total Varians
                  </div>
                  <div class="text-2xl font-bold" :class="variance.has_warning ? 'text-red-700' : 'text-green-700'"
                       x-text="formatCurrency(variance.variance)"></div>
                  <div class="text-xs mt-1" :class="variance.has_warning ? 'text-red-600' : 'text-green-600'">
                    <span x-text="variance.variance_percentage?.toFixed(2)"></span>% dari budget
                  </div>
                </div>
                <div x-show="variance.has_warning">
                  <i class='bx bx-error-circle text-4xl text-red-500'></i>
                </div>
                <div x-show="!variance.has_warning">
                  <i class='bx bx-check-circle text-4xl text-green-500'></i>
                </div>
              </div>
            </div>

            <!-- Item Variances -->
            <div class="space-y-2">
              <template x-for="item in variance.items" :key="item.item">
                <div class="p-3 rounded-lg border"
                     :class="item.has_warning ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-slate-50'">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="font-medium text-sm" x-text="item.item"></div>
                      <div class="text-xs text-slate-600 mt-1">
                        Budget: <span x-text="formatCurrency(item.budget)"></span> | 
                        Actual: <span x-text="formatCurrency(item.actual)"></span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="font-bold" :class="item.has_warning ? 'text-red-700' : 'text-slate-700'"
                           x-text="formatCurrency(item.variance)"></div>
                      <div class="text-xs" :class="item.has_warning ? 'text-red-600' : 'text-slate-600'">
                        <span x-text="item.variance_percentage?.toFixed(1)"></span>%
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Available Flights from Package -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Penerbangan Tersedia (dari Paket)</h2>
          </div>

          <div x-show="loading" class="text-center py-4">
            <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
          </div>

          <div x-show="!loading && (!keberangkatan.available_flights || keberangkatan.available_flights.length === 0)" class="text-center py-4 text-slate-500">
            Belum ada penerbangan tersedia
          </div>

          <div x-show="!loading && keberangkatan.available_flights && keberangkatan.available_flights.length > 0" class="space-y-3">
            <template x-for="(flight, index) in keberangkatan.available_flights" :key="'flight-' + index">
              <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <div class="font-medium" x-text="flight.airline + ' - ' + flight.flight_number"></div>
                      <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700" x-text="flight.type"></span>
                    </div>
                    <div class="text-sm text-slate-600 mt-1">
                      <span x-text="flight.route"></span>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                      <div>
                        <span class="text-slate-600">Keberangkatan:</span>
                        <span class="font-medium" x-text="flight.departure_time || '-'"></span>
                      </div>
                      <div>
                        <span class="text-slate-600">Kedatangan:</span>
                        <span class="font-medium" x-text="flight.arrival_time || '-'"></span>
                      </div>
                    </div>
                    <div class="mt-2 text-sm">
                      <span class="text-slate-600">Harga per orang:</span>
                      <span class="font-medium text-blue-600" x-text="formatCurrency(flight.price_per_person)"></span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Available Hotels from Package -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Hotel Tersedia (dari Paket)</h2>
          </div>

          <div x-show="loading" class="text-center py-4">
            <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
          </div>

          <div x-show="!loading && (!keberangkatan.available_hotels || keberangkatan.available_hotels.length === 0)" class="text-center py-4 text-slate-500">
            Belum ada hotel tersedia
          </div>

          <div x-show="!loading && keberangkatan.available_hotels && keberangkatan.available_hotels.length > 0" class="space-y-3">
            <template x-for="(hotel, index) in keberangkatan.available_hotels" :key="'hotel-' + index">
              <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <div class="font-medium" x-text="hotel.hotel_name"></div>
                      <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700" x-text="hotel.city_type"></span>
                    </div>
                    <div class="text-sm text-slate-600 mt-1">
                      <span x-text="hotel.location"></span>
                      <span class="ml-2" x-text="'★'.repeat(hotel.star_rating || 0)"></span>
                    </div>
                    <div class="mt-2 text-sm">
                      <span class="text-slate-600">Tipe Kamar:</span>
                      <span class="font-medium" x-text="hotel.room_type"></span>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                      <div>
                        <span class="text-slate-600">Check-in:</span>
                        <span class="font-medium" x-text="hotel.check_in || '-'"></span>
                      </div>
                      <div>
                        <span class="text-slate-600">Check-out:</span>
                        <span class="font-medium" x-text="hotel.check_out || '-'"></span>
                      </div>
                    </div>
                    <div class="mt-2 text-sm">
                      <span class="text-slate-600">Harga per malam:</span>
                      <span class="font-medium text-purple-600" x-text="formatCurrency(hotel.price_per_night)"></span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Hotel Bookings Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Hotel Bookings</h2>
            <button x-on:click="showHotelBookingModal = true; fetchAvailableHotels()"
                    class="inline-flex items-center gap-2 rounded-xl bg-purple-600 text-white px-4 py-2 hover:bg-purple-700">
              <i class='bx bx-plus'></i>
              <span>Tambah Hotel Booking</span>
            </button>
          </div>

          <!-- Loading State -->
          <div x-show="loadingHotels" class="text-center py-8">
            <div class="inline-flex items-center gap-2 text-slate-600">
              <i class='bx bx-loader-alt bx-spin text-xl'></i>
              <span>Memuat data...</span>
            </div>
          </div>

          <!-- Hotel Bookings List -->
          <div x-show="!loadingHotels" class="space-y-3">
            <template x-for="booking in hotelBookings" :key="booking.id">
              <div class="p-4 rounded-lg border border-slate-200 hover:border-purple-300 transition-colors">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1">
                    <div class="font-medium text-slate-900" x-text="booking.hotel_name"></div>
                    <div class="text-sm text-slate-600 mt-1">
                      <i class='bx bx-calendar'></i>
                      <span x-text="booking.check_in_formatted"></span> - <span x-text="booking.check_out_formatted"></span>
                    </div>
                    <div class="text-sm text-slate-600 mt-1">
                      <i class='bx bx-door-open'></i>
                      <span x-text="booking.room_count"></span> kamar
                      <span x-show="booking.room_type" class="ml-2 px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700 capitalize" x-text="booking.room_type"></span>
                    </div>
                    <div x-show="booking.seller_name && booking.seller_name !== '-'" class="text-sm text-slate-600 mt-1">
                      <i class='bx bx-user'></i>
                      Seller: <span x-text="booking.seller_name"></span>
                      <span x-show="booking.seller_phone && booking.seller_phone !== '-'"> · <span x-text="booking.seller_phone"></span></span>
                    </div>
                    <div x-show="booking.booking_reference" class="text-sm text-slate-600 mt-1">
                      <i class='bx bx-bookmark'></i>
                      Ref: <span x-text="booking.booking_reference"></span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-medium"
                          :class="{
                            'bg-green-100 text-green-700': booking.status === 'confirmed',
                            'bg-yellow-100 text-yellow-700': booking.status === 'pending',
                            'bg-red-100 text-red-700': booking.status === 'cancelled'
                          }"
                          x-text="booking.status"></span>
                  </div>
                </div>

                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                  <button x-on:click="showRoomAssignmentModal(booking)"
                          class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                    <i class='bx bx-user-plus'></i>
                    <span>Assign Jamaah</span>
                  </button>
                  <button x-on:click="editHotelBooking(booking)"
                          class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm bg-slate-50 text-slate-600 rounded-lg hover:bg-slate-100">
                    <i class='bx bx-edit'></i>
                    <span>Edit</span>
                  </button>
                  <button x-on:click="deleteHotelBooking(booking.id)"
                          class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                    <i class='bx bx-trash'></i>
                  </button>
                </div>
              </div>
            </template>

            <!-- Empty State -->
            <div x-show="hotelBookings.length === 0" class="text-center py-8 text-slate-500">
              <i class='bx bx-hotel text-4xl mb-2'></i>
              <p>Belum ada hotel booking</p>
              <p class="text-sm mt-1">Klik tombol "Tambah Hotel Booking" untuk memulai</p>
            </div>
          </div>
        </div>

        <!-- Visa Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Visa</h2>
            @hasPermission('travel.keberangkatan.edit')
            <button x-on:click="openVisaCreate()"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-3 py-2 text-sm hover:bg-indigo-700">
              <i class='bx bx-plus-circle'></i> Tambah Visa
            </button>
            @endhasPermission
          </div>

          <div x-show="loadingVisas" class="text-center py-4">
            <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
          </div>

          <div x-show="!loadingVisas">
            <div x-show="visas.length === 0" class="text-center py-6 text-slate-500">
              <i class='bx bx-id-card text-4xl mb-2'></i>
              <p>Belum ada data visa</p>
            </div>
            <div class="space-y-2">
              <template x-for="visa in visas" :key="visa.id">
                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="font-medium text-sm capitalize" x-text="visa.visa_type"></div>
                      <div class="text-xs text-slate-600 mt-1 space-y-0.5">
                        <div><span class="font-medium">Seller:</span> <span x-text="visa.seller_name"></span></div>
                        <div x-show="visa.seller_phone && visa.seller_phone !== '-'">
                          <span class="font-medium">Telp:</span> <span x-text="visa.seller_phone"></span>
                        </div>
                        <div><span class="font-medium">Harga/orang:</span> <span x-text="visa.price_formatted"></span></div>
                      </div>
                      <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs px-2 py-0.5 rounded-full"
                              :class="{
                                'bg-yellow-100 text-yellow-700': visa.status === 'pending',
                                'bg-blue-100 text-blue-700': visa.status === 'processing',
                                'bg-green-100 text-green-700': visa.status === 'ready',
                                'bg-purple-100 text-purple-700': visa.status === 'distributed'
                              }"
                              x-text="visa.status_label"></span>
                        <span x-show="visa.submission_date" class="text-xs text-slate-500">
                          Submit: <span x-text="visa.submission_date"></span>
                        </span>
                      </div>
                    </div>
                    <div class="flex gap-1 ml-2">
                      @hasPermission('travel.keberangkatan.edit')
                      <button x-on:click="openVisaEdit(visa)" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100">
                        <i class='bx bx-edit-alt text-sm'></i>
                      </button>
                      <button x-on:click="confirmDeleteVisa(visa)" class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                        <i class='bx bx-trash text-sm'></i>
                      </button>
                      @endhasPermission
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Jamaah List -->
      <div class="space-y-4">

        <!-- Reminder Card -->
        <div class="rounded-2xl border border-amber-200 bg-amber-50 shadow-card p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-lg text-amber-800">Reminder Keberangkatan</h2>
            @hasPermission('travel.keberangkatan.edit')
            <button x-on:click="sendReminderNow()" :disabled="sendingReminder"
                    class="inline-flex items-center gap-1 rounded-xl bg-amber-600 text-white px-3 py-1.5 text-sm hover:bg-amber-700 disabled:opacity-50">
              <i class='bx bx-bell' :class="{'bx-tada': sendingReminder}"></i>
              <span x-show="!sendingReminder">Kirim Sekarang</span>
              <span x-show="sendingReminder">Mengirim...</span>
            </button>
            @endhasPermission
          </div>
          <p class="text-xs text-amber-700 mb-3">Reminder otomatis dikirim setiap Senin, 2 bulan sebelum keberangkatan.</p>
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2 rounded-lg bg-white border border-amber-200 text-center">
              <i class='bx bx-hotel text-amber-600 text-lg'></i>
              <div class="text-xs font-medium mt-1">Hotel</div>
              <div class="text-xs text-slate-500">Owner</div>
            </div>
            <div class="p-2 rounded-lg bg-white border border-amber-200 text-center">
              <i class='bx bx-plane text-amber-600 text-lg'></i>
              <div class="text-xs font-medium mt-1">Tiket</div>
              <div class="text-xs text-slate-500">Owner</div>
            </div>
            <div class="p-2 rounded-lg bg-white border border-amber-200 text-center">
              <i class='bx bx-id-card text-amber-600 text-lg'></i>
              <div class="text-xs font-medium mt-1">Visa</div>
              <div class="text-xs text-slate-500">Owner</div>
            </div>
            <div class="p-2 rounded-lg bg-white border border-amber-200 text-center">
              <i class='bx bx-train text-amber-600 text-lg'></i>
              <div class="text-xs font-medium mt-1">Kereta Cepat</div>
              <div class="text-xs text-slate-500">Admin</div>
            </div>
          </div>
        </div>

        <!-- Reports Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <h2 class="font-semibold text-lg mb-4">Laporan & Dokumen</h2>
          <div class="space-y-2">
            <a :href="`{{ url('/admin/inventaris/travel/keberangkatan') }}/${keberangkatan.id}/logistics`"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
              <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                <i class='bx bx-package text-orange-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Logistics & Equipment</div>
                <div class="text-xs text-slate-500">Manage equipment checklist</div>
              </div>
              <i class='bx bx-chevron-right text-slate-400'></i>
            </a>

            <a href="javascript:void(0)"
               @click="window.open(`{{ url('/admin/inventaris/travel/keberangkatan') }}/${keberangkatan.id}/manifest`, '_blank')"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
              <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <i class='bx bx-file text-blue-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Manifest Jamaah</div>
                <div class="text-xs text-slate-500">Daftar lengkap jamaah</div>
              </div>
              <i class='bx bx-download text-slate-400'></i>
            </a>

            <a href="javascript:void(0)"
               @click="window.open(`{{ url('/admin/inventaris/travel/keberangkatan') }}/${keberangkatan.id}/siskopatuh`, '_blank')"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
              <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <i class='bx bx-file-blank text-green-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Laporan Siskopatuh</div>
                <div class="text-xs text-slate-500">Format pemerintah</div>
              </div>
              <i class='bx bx-download text-slate-400'></i>
            </a>

            <a href="javascript:void(0)"
               @click="window.open(`{{ url('/admin/inventaris/travel/keberangkatan') }}/${keberangkatan.id}/roomlist`, '_blank')"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
              <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                <i class='bx bx-bed text-purple-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Roomlist Hotel</div>
                <div class="text-xs text-slate-500">Daftar kamar jamaah</div>
              </div>
              <i class='bx bx-download text-slate-400'></i>
            </a>

            <a href="javascript:void(0)"
               @click="window.open(`{{ url('/admin/inventaris/travel/keberangkatan') }}/${keberangkatan.id}/roomlist-stream`, '_blank')"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
              <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                <i class='bx bx-table text-teal-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Roomlist Stream</div>
                <div class="text-xs text-slate-500">Format tabel dengan room position</div>
              </div>
              <i class='bx bx-download text-slate-400'></i>
            </a>

            <a href="{{ route('admin.inventaris.document.manage-room-position', $keberangkatan->id) }}"
               class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
              <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class='bx bx-map-pin text-indigo-600 text-xl'></i>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">Kelola Room Position</div>
                <div class="text-xs text-slate-500">Atur posisi kamar jamaah</div>
              </div>
              <i class='bx bx-chevron-right text-slate-400'></i>
            </a>
          </div>
        </div>

        <!-- Jamaah List Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Daftar Jamaah</h2>
            @hasPermission('travel.keberangkatan.edit')
            <button x-on:click="openJamaahModal()" 
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-3 py-2 text-sm hover:bg-primary-700">
              <i class='bx bx-user-plus'></i> Kelola Jamaah
            </button>
            @endhasPermission
          </div>
          
          <div x-show="keberangkatan.jamaah_list?.length === 0" class="text-center py-4 text-slate-500">
            Belum ada jamaah terdaftar
          </div>

          <div class="space-y-2">
            <template x-for="jamaah in keberangkatan.jamaah_list" :key="jamaah.booking_id">
              <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                <div class="font-medium text-sm" x-text="jamaah.jamaah_name"></div>
                <div class="text-xs text-slate-600 mt-1 space-y-0.5">
                  <div x-show="jamaah.no_ktp && jamaah.no_ktp !== '-'">
                    <span class="font-medium">KTP:</span> <span x-text="jamaah.no_ktp"></span>
                  </div>
                  <div x-show="jamaah.no_passport && jamaah.no_passport !== '-'">
                    <span class="font-medium">Passport:</span> <span x-text="jamaah.no_passport"></span>
                  </div>
                  <div x-show="jamaah.no_telp && jamaah.no_telp !== '-'">
                    <span class="font-medium">Telp:</span> <span x-text="jamaah.no_telp"></span>
                  </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                  <span class="text-xs px-2 py-0.5 rounded-full"
                        :class="{
                          'bg-yellow-100 text-yellow-700': jamaah.payment_status === 'unpaid',
                          'bg-blue-100 text-blue-700': jamaah.payment_status === 'partial',
                          'bg-green-100 text-green-700': jamaah.payment_status === 'paid'
                        }"
                        x-text="jamaah.payment_status"></span>
                  <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 text-slate-700"
                        x-text="jamaah.booking_status"></span>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Include Jamaah Management Modal -->
    @include('admin.travel.keberangkatan.jamaah-modal')

    <!-- Visa Modal -->
    <div x-show="showVisaForm" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeVisaForm()" class="w-full max-w-lg bg-white rounded-2xl shadow-float my-4">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="visaForm.id ? 'Edit Visa' : 'Tambah Visa'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeVisaForm()"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="px-5 py-4 space-y-3">
          <div>
            <label class="text-sm text-slate-600">Tipe Visa <span class="text-red-500">*</span></label>
            <select x-model="visaForm.visa_type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <option value="umrah">Umrah</option>
              <option value="hajj">Haji</option>
              <option value="ziarah">Ziarah</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Nama Host Seller</label>
              <input type="text" x-model.trim="visaForm.seller_name" placeholder="Nama agen visa" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Telepon Seller</label>
              <input type="text" x-model.trim="visaForm.seller_phone" placeholder="08xxxxxxxxxx" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
          </div>
          <div>
            <label class="text-sm text-slate-600">Harga per Orang (Rp) <span class="text-red-500">*</span></label>
            <input type="number" x-model.number="visaForm.price_per_person" min="0" placeholder="0" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
          </div>
          <div>
            <label class="text-sm text-slate-600">Status</label>
            <select x-model="visaForm.status" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <option value="pending">Pending</option>
              <option value="processing">Diproses</option>
              <option value="ready">Siap</option>
              <option value="distributed">Sudah Dibagikan</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-slate-600">Tanggal Submit</label>
              <input type="date" x-model="visaForm.submission_date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Tanggal Siap</label>
              <input type="date" x-model="visaForm.ready_date" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
          </div>
          <div>
            <label class="text-sm text-slate-600">Catatan</label>
            <textarea x-model.trim="visaForm.notes" rows="2" placeholder="Catatan tambahan..." class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
          </div>
        </div>
        <div class="px-5 pb-4 pt-2 border-t border-slate-100 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeVisaForm()">Batal</button>
          <button x-on:click="submitVisaForm()" :disabled="savingVisa"
                  class="rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 disabled:opacity-50">
            <span x-show="savingVisa" class="inline-flex items-center gap-2"><i class='bx bx-loader-alt bx-spin'></i> Menyimpan...</span>
            <span x-show="!savingVisa">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Visa Delete Confirm -->
    <div x-show="toDeleteVisa" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDeleteVisa=null" class="w-full max-w-md rounded-2xl bg-white shadow-float my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Visa?</div>
          <p class="text-slate-600 mt-1 text-sm">Data visa akan dihapus permanen.</p>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDeleteVisa=null">Batal</button>
          <button x-on:click="deleteVisaNow()" :disabled="deletingVisa"
                  class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deletingVisa" class="inline-flex items-center gap-2"><i class='bx bx-loader-alt bx-spin'></i></span>
            <span x-show="!deletingVisa">Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Flight Booking Modal -->
    <div x-show="showFlightBookingModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showFlightBookingModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Tambah Pemesanan Penerbangan</h3>
          <button x-on:click="showFlightBookingModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <form x-on:submit.prevent="submitFlightBooking">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Penerbangan</label>
              <select x-model="flightBookingForm.id_flight" required
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Penerbangan</option>
                <template x-for="flight in availableFlights" :key="flight.id">
                  <option :value="flight.id" x-text="flight.text"></option>
                </template>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Kursi</label>
              <input type="number" x-model="flightBookingForm.seat_count" required min="1"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Referensi Booking</label>
              <input type="text" x-model="flightBookingForm.booking_reference"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Kode Konfirmasi</label>
              <input type="text" x-model="flightBookingForm.confirmation_code"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <div class="flex gap-2 mt-6">
            <button type="button" x-on:click="showFlightBookingModal = false"
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" :disabled="submittingFlightBooking"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50">
              <span x-show="!submittingFlightBooking">Simpan</span>
              <span x-show="submittingFlightBooking">Menyimpan...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Flight Booking Modal -->
    <div x-show="showEditFlightModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showEditFlightModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Edit Status Penerbangan</h3>
          <button x-on:click="showEditFlightModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <form x-on:submit.prevent="submitEditFlightBooking">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
              <select x-model="editFlightForm.status" required
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="ticketed">Ticketed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Kode Konfirmasi</label>
              <input type="text" x-model="editFlightForm.confirmation_code"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
          </div>

          <div class="flex gap-2 mt-6">
            <button type="button" x-on:click="showEditFlightModal = false"
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" :disabled="submittingEditFlight"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50">
              <span x-show="!submittingEditFlight">Update</span>
              <span x-show="submittingEditFlight">Updating...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Ticket Upload Modal -->
    <div x-show="showTicketModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showTicketModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Upload Tiket Penerbangan</h3>
          <button x-on:click="showTicketModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <form x-on:submit.prevent="submitTicketUpload">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">File Tiket (PDF, JPG, PNG)</label>
              <input type="file" x-ref="ticketFile" accept=".pdf,.jpg,.jpeg,.png" required
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <p class="text-xs text-slate-500 mt-1">Maksimal 5MB</p>
            </div>
          </div>

          <div class="flex gap-2 mt-6">
            <button type="button" x-on:click="showTicketModal = false"
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" :disabled="uploadingTicket"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50">
              <span x-show="!uploadingTicket">Upload</span>
              <span x-show="uploadingTicket">Uploading...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Hotel Booking Modal -->
    <div x-show="showHotelBookingModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showHotelBookingModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Tambah Pemesanan Hotel</h3>
          <button x-on:click="showHotelBookingModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <form x-on:submit.prevent="submitHotelBooking">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Hotel</label>
              <select x-model="hotelBookingForm.id_hotel" required
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Pilih Hotel</option>
                <template x-for="hotel in availableHotels" :key="hotel.id">
                  <option :value="hotel.id" x-text="hotel.text"></option>
                </template>
              </select>
            </div>

            <!-- Tipe Kamar -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Kamar</label>
              <select x-model="hotelBookingForm.room_type"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Pilih Tipe Kamar</option>
                <option value="quad">Quad (4 orang)</option>
                <option value="triple">Triple (3 orang)</option>
                <option value="double">Double (2 orang)</option>
              </select>
            </div>

            <!-- Host Seller -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Host Seller</label>
                <input type="text" x-model="hotelBookingForm.seller_name" placeholder="Nama agen hotel"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Telepon Seller</label>
                <input type="text" x-model="hotelBookingForm.seller_phone" placeholder="08xxxxxxxxxx"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Check-in</label>
                <input type="date" x-model="hotelBookingForm.check_in_date" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Check-out</label>
                <input type="date" x-model="hotelBookingForm.check_out_date" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Kamar</label>
              <input type="number" x-model="hotelBookingForm.room_count" required min="1"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Referensi Booking</label>
              <input type="text" x-model="hotelBookingForm.booking_reference"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
              <textarea x-model="hotelBookingForm.notes" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
            </div>
          </div>

          <div class="flex gap-2 mt-6">
            <button type="button" x-on:click="showHotelBookingModal = false"
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" :disabled="submittingHotelBooking"
                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 disabled:opacity-50">
              <span x-show="!submittingHotelBooking">Simpan</span>
              <span x-show="submittingHotelBooking">Menyimpan...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Hotel Booking Modal -->
    <div x-show="showEditHotelModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showEditHotelModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Edit Status Hotel</h3>
          <button x-on:click="showEditHotelModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <form x-on:submit.prevent="submitEditHotelBooking">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
              <select x-model="editHotelForm.status" required
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked_in">Checked In</option>
                <option value="checked_out">Checked Out</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Referensi Booking</label>
              <input type="text" x-model="editHotelForm.booking_reference"
                     class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
          </div>

          <div class="flex gap-2 mt-6">
            <button type="button" x-on:click="showEditHotelModal = false"
                    class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
              Batal
            </button>
            <button type="submit" :disabled="submittingEditHotel"
                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 disabled:opacity-50">
              <span x-show="!submittingEditHotel">Update</span>
              <span x-show="submittingEditHotel">Updating...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Room Assignment Modal -->
    <div x-show="showRoomAssignModal" 
         x-transition.opacity 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 pt-8 overflow-y-auto"
         x-on:click.self="showRoomAssignModal = false">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Assign Rooms</h3>
          <button x-on:click="showRoomAssignModal = false" class="p-1 hover:bg-slate-100 rounded-lg">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div x-show="loadingAssignments" class="text-center py-8">
          <i class='bx bx-loader-alt bx-spin text-2xl text-slate-400'></i>
        </div>

        <div x-show="!loadingAssignments">
          <!-- Debug Info -->
          <div x-show="true" class="mb-4 p-3 bg-gray-100 rounded text-xs">
            <div>Current Assignments: <span x-text="currentAssignments.length"></span></div>
            <div>Unassigned Jamaah: <span x-text="unassignedJamaah.length"></span></div>
          </div>

          <!-- Existing Assignments -->
          <div x-show="currentAssignments && currentAssignments.length > 0" class="mb-6">
            <h4 class="font-medium mb-3">Assigned Jamaah</h4>
            <div class="space-y-2">
              <template x-for="assignment in currentAssignments" :key="assignment.id">
                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                  <div class="flex-1">
                    <div class="font-medium text-sm" x-text="assignment.jamaah_booking?.jamaah?.nama || 'Unknown'"></div>
                    <div class="text-xs text-slate-600 mt-1">
                      Room: <span x-text="assignment.room_number"></span>
                      <span x-show="assignment.bed_number"> | Bed: <span x-text="assignment.bed_number"></span></span>
                    </div>
                  </div>
                  <button x-on:click="removeRoomAssignment(assignment.id)" 
                          class="p-2 hover:bg-slate-200 rounded-lg text-red-600">
                    <i class='bx bx-trash'></i>
                  </button>
                </div>
              </template>
            </div>
          </div>

          <!-- Unassigned Jamaah -->
          <div x-show="unassignedJamaah && unassignedJamaah.length > 0">
            <h4 class="font-medium mb-3">Unassigned Jamaah (<span x-text="unassignedJamaah.length"></span>)</h4>
            <form x-on:submit.prevent="submitRoomAssignments">
              <div class="space-y-3 mb-4">
                <template x-for="(jamaah, index) in unassignedJamaah" :key="jamaah.id">
                  <div class="p-3 rounded-lg border border-slate-200 bg-white">
                    <div class="flex items-center justify-between mb-2">
                      <div class="font-medium text-sm" x-text="jamaah.jamaah_name || 'No Name'"></div>
                      <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700" x-text="jamaah.room_type || 'Standard'"></span>
                    </div>
                    <div class="text-xs text-slate-500 mb-2">
                      <span x-show="jamaah.booking_code">Booking: <span x-text="jamaah.booking_code"></span></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <input type="text" 
                             x-model="roomAssignments[jamaah.id].room_number" 
                             placeholder="Room #"
                             class="px-2 py-1 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-purple-500">
                      <input type="number" 
                             x-model="roomAssignments[jamaah.id].bed_number" 
                             placeholder="Bed #"
                             class="px-2 py-1 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-purple-500">
                      <input type="text" 
                             x-model="roomAssignments[jamaah.id].room_type" 
                             placeholder="Type"
                             class="px-2 py-1 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-purple-500"
                             readonly
                             title="Room type dari booking jamaah">
                    </div>
                  </div>
                </template>
              </div>

              <div class="flex gap-2">
                <button type="button" x-on:click="showRoomAssignModal = false"
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-xl hover:bg-slate-50">
                  Tutup
                </button>
                <button type="submit" :disabled="submittingAssignments"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 disabled:opacity-50">
                  <span x-show="!submittingAssignments">Assign Rooms</span>
                  <span x-show="submittingAssignments">Assigning...</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Empty State -->
          <div x-show="(!unassignedJamaah || unassignedJamaah.length === 0) && (!currentAssignments || currentAssignments.length === 0)" class="text-center py-8 text-slate-500">
            <i class='bx bx-user-x text-4xl mb-2'></i>
            <p>Tidak ada jamaah yang perlu di-assign</p>
            <p class="text-sm mt-1">Semua jamaah sudah memiliki room assignment atau belum ada jamaah terdaftar</p>
          </div>

          <div x-show="(!unassignedJamaah || unassignedJamaah.length === 0) && currentAssignments && currentAssignments.length > 0" class="text-center py-4 text-green-600">
            <i class='bx bx-check-circle text-2xl'></i>
            <div class="mt-2">All jamaah have been assigned to rooms</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
      <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
           class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
        <div class="flex items-center gap-2">
          <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
          <span x-text="toastMessage"></span>
        </div>
      </div>
    </div>
  </div>

  <script>
    function keberangkatanDetail() {
      return {
        keberangkatan: {},
        keberangkatanId: null,
        rabData: null,
        variance: {
          has_warning: false,
          variance: 0,
          variance_percentage: 0,
          items: []
        },
        loading: true,
        loadingRab: false,
        loadingVariance: false,
        creatingRab: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        
        // Jamaah Management
        showJamaahModal: false,
        jamaahTab: 'registered',
        availableJamaah: [],
        selectedJamaahIds: [],
        selectAllJamaah: false,
        jamaahSearch: '',
        loadingAvailableJamaah: false,
        addingJamaah: false,
        removingJamaah: false,
        showRemoveConfirm: false,
        jamaahToRemove: null,
        costData: null,
        loadingCost: false,

        // Visa Management
        visas: [],
        loadingVisas: false,
        showVisaForm: false,
        savingVisa: false,
        toDeleteVisa: null,
        deletingVisa: false,
        visaForm: {
          id: null,
          visa_type: 'umrah',
          seller_name: '',
          seller_phone: '',
          price_per_person: 0,
          status: 'pending',
          submission_date: '',
          ready_date: '',
          notes: '',
        },

        // Reminder
        sendingReminder: false,

        async init() {
          const id = window.location.pathname.split('/').pop();
          this.keberangkatanId = id;
          await this.fetchKeberangkatanData();
          if (this.keberangkatan.id_rab) {
            await Promise.all([
              this.fetchRabData(id),
              this.fetchBudgetVariance(id)
            ]);
          }
          // Fetch hotel bookings
          await this.fetchHotelBookings();
          // Fetch visas
          await this.fetchVisas();
        },

        async fetchKeberangkatanData() {
          this.loading = true;
          try {
            const baseUrl = '{{ url('') }}';
            const response = await fetch(`${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const data = await response.json();
            this.keberangkatan = data;
          } catch (error) {
            console.error('Error fetching keberangkatan:', error);
            this.showToastMessage('Gagal memuat data keberangkatan', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchKeberangkatan(id) {
          // Alias for backward compatibility
          this.keberangkatanId = id;
          await this.fetchKeberangkatanData();
        },

        async createRab() {
          this.creatingRab = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatan.id}/create-rab`;
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'RAB berhasil dibuat', 'success');
              // Reload page to show RAB data
              setTimeout(() => {
                window.location.reload();
              }, 1000);
            } else {
              this.showToastMessage(result.message || 'Gagal membuat RAB', 'error');
            }
          } catch (error) {
            console.error('Error creating RAB:', error);
            this.showToastMessage('Gagal membuat RAB', 'error');
          } finally {
            this.creatingRab = false;
          }
        },

        async fetchRabData(id) {
          this.loadingRab = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${id}/rab-data`;
            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const result = await response.json();
            
            if (result.success && result.has_rab) {
              this.rabData = result.data;
            }
          } catch (error) {
            console.error('Error fetching RAB data:', error);
          } finally {
            this.loadingRab = false;
          }
        },

        async fetchBudgetVariance(id) {
          this.loadingVariance = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${id}/budget-variance`;
            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const result = await response.json();
            
            if (result.success) {
              this.variance = result.data;
            }
          } catch (error) {
            console.error('Error fetching budget variance:', error);
          } finally {
            this.loadingVariance = false;
          }
        },

        async refreshRabData() {
          const id = this.keberangkatan.id;
          await Promise.all([
            this.fetchRabData(id),
            this.fetchBudgetVariance(id)
          ]);
          this.showToastMessage('Data RAB diperbarui', 'success');
        },

        async refreshAllData() {
          console.log('Refreshing all keberangkatan data...');
          await this.fetchKeberangkatanData();
          
          // If RAB exists, refresh RAB data too
          if (this.keberangkatan.id_rab) {
            await Promise.all([
              this.fetchRabData(this.keberangkatanId),
              this.fetchBudgetVariance(this.keberangkatanId)
            ]);
          }
          
          this.showToastMessage('Data berhasil diperbarui', 'success');
          console.log('Keberangkatan data refreshed. id_rab:', this.keberangkatan.id_rab);
        },

        formatCurrency(value) {
          if (!value && value !== 0) return 'Rp 0';
          return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        },

        // Jamaah Management Functions
        openJamaahModal() {
          this.showJamaahModal = true;
          this.jamaahTab = 'registered';
          this.selectedJamaahIds = [];
          this.selectAllJamaah = false;
        },

        closeJamaahModal() {
          this.showJamaahModal = false;
          this.jamaahTab = 'registered';
          this.availableJamaah = [];
          this.selectedJamaahIds = [];
          this.jamaahSearch = '';
        },

        async loadAvailableJamaah() {
          this.loadingAvailableJamaah = true;
          try {
            const params = new URLSearchParams({
              search: this.jamaahSearch
            });
            
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/available-jamaah?${params}`;
            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            
            if (response.ok) {
              const data = await response.json();
              console.log('Available jamaah data:', data);
              this.availableJamaah = Array.isArray(data) ? data : [];
            } else {
              const errorText = await response.text();
              console.error('Failed to load available jamaah:', response.status, errorText);
              this.showToastMessage('Gagal memuat data jamaah', 'error');
            }
          } catch (error) {
            console.error('Error loading available jamaah:', error);
            this.showToastMessage('Gagal memuat data jamaah', 'error');
          } finally {
            this.loadingAvailableJamaah = false;
          }
        },

        toggleSelectAll() {
          if (this.selectAllJamaah) {
            this.selectedJamaahIds = this.availableJamaah.map(j => j.booking_id);
          } else {
            this.selectedJamaahIds = [];
          }
        },

        async addSelectedJamaah() {
          if (this.selectedJamaahIds.length === 0) {
            this.showToastMessage('Pilih minimal 1 jamaah', 'error');
            return;
          }

          // Check capacity
          if (this.selectedJamaahIds.length > this.keberangkatan.available_capacity) {
            this.showToastMessage(`Kapasitas tidak cukup. Tersedia: ${this.keberangkatan.available_capacity}`, 'error');
            return;
          }

          this.addingJamaah = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/add-jamaah`;
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                booking_ids: this.selectedJamaahIds
              })
            });

            const data = await response.json();

            if (response.ok && data.success) {
              this.showToastMessage(data.message, 'success');
              
              // Refresh data
              await this.fetchKeberangkatanData();
              
              // Reset selection
              this.selectedJamaahIds = [];
              this.selectAllJamaah = false;
              
              // Switch to registered tab
              this.jamaahTab = 'registered';
            } else {
              this.showToastMessage(data.message || 'Gagal menambahkan jamaah', 'error');
            }
          } catch (error) {
            console.error('Error adding jamaah:', error);
            this.showToastMessage('Gagal menambahkan jamaah', 'error');
          } finally {
            this.addingJamaah = false;
          }
        },

        confirmRemoveJamaah(jamaah) {
          this.jamaahToRemove = jamaah;
          this.showRemoveConfirm = true;
        },

        async removeJamaahNow() {
          if (!this.jamaahToRemove) return;

          this.removingJamaah = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/remove-jamaah`;
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                booking_id: this.jamaahToRemove.booking_id
              })
            });

            const data = await response.json();

            if (response.ok && data.success) {
              this.showToastMessage(data.message, 'success');
              
              // Refresh data
              await this.fetchKeberangkatanData();
              
              // Close confirm modal
              this.showRemoveConfirm = false;
              this.jamaahToRemove = null;
            } else {
              this.showToastMessage(data.message || 'Gagal menghapus jamaah', 'error');
            }
          } catch (error) {
            console.error('Error removing jamaah:', error);
            this.showToastMessage('Gagal menghapus jamaah', 'error');
          } finally {
            this.removingJamaah = false;
          }
        },

        async loadCostCalculation() {
          this.loadingCost = true;
          try {
            const baseUrl = '{{ url('') }}';
            const url = `${baseUrl}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/total-cost`;
            const response = await fetch(url, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            
            if (response.ok) {
              const result = await response.json();
              console.log('Cost calculation data:', result);
              if (result.success) {
                this.costData = result.data;
              } else {
                this.showToastMessage(result.message || 'Gagal memuat perhitungan biaya', 'error');
              }
            } else {
              const errorText = await response.text();
              console.error('Failed to load cost calculation:', response.status, errorText);
              this.showToastMessage('Gagal memuat perhitungan biaya', 'error');
            }
          } catch (error) {
            console.error('Error loading cost calculation:', error);
            this.showToastMessage('Gagal memuat perhitungan biaya', 'error');
          } finally {
            this.loadingCost = false;
          }
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        },

        // Flight Booking Functions
        flightBookings: [],
        availableFlights: [],
        loadingFlights: false,
        showFlightBookingModal: false,
        showEditFlightModal: false,
        showTicketModal: false,
        submittingFlightBooking: false,
        submittingEditFlight: false,
        uploadingTicket: false,
        currentFlightBooking: null,
        flightBookingForm: {
          id_flight: '',
          seat_count: '',
          booking_reference: '',
          confirmation_code: ''
        },
        editFlightForm: {
          id: null,
          status: '',
          confirmation_code: ''
        },

        async fetchFlightBookings() {
          this.loadingFlights = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.flight-booking.getByKeberangkatan', '') }}/${this.keberangkatan.id}`);
            const result = await response.json();
            if (result.success) {
              this.flightBookings = result.data;
            }
          } catch (error) {
            console.error('Error fetching flight bookings:', error);
          } finally {
            this.loadingFlights = false;
          }
        },

        async fetchAvailableFlights() {
          try {
            const response = await fetch('{{ route('admin.inventaris.flight-booking.getAvailableFlights') }}');
            const result = await response.json();
            if (result.success) {
              this.availableFlights = result.data;
            }
          } catch (error) {
            console.error('Error fetching available flights:', error);
          }
        },

        async submitFlightBooking() {
          this.submittingFlightBooking = true;
          try {
            const response = await fetch('{{ route('admin.inventaris.flight-booking.store') }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                ...this.flightBookingForm,
                id_keberangkatan: this.keberangkatan.id
              })
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Pemesanan penerbangan berhasil dibuat', 'success');
              this.showFlightBookingModal = false;
              this.flightBookingForm = {
                id_flight: '',
                seat_count: '',
                booking_reference: '',
                confirmation_code: ''
              };
              await this.fetchFlightBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal membuat pemesanan penerbangan', 'error');
            }
          } catch (error) {
            console.error('Error creating flight booking:', error);
            this.showToastMessage('Gagal membuat pemesanan penerbangan', 'error');
          } finally {
            this.submittingFlightBooking = false;
          }
        },

        editFlightBooking(booking) {
          this.editFlightForm = {
            id: booking.id,
            status: booking.status,
            confirmation_code: booking.confirmation_code || ''
          };
          this.showEditFlightModal = true;
        },

        async submitEditFlightBooking() {
          this.submittingEditFlight = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.flight-booking.updateStatus', '') }}/${this.editFlightForm.id}`, {
              method: 'PUT',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                status: this.editFlightForm.status,
                confirmation_code: this.editFlightForm.confirmation_code
              })
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Status penerbangan berhasil diupdate', 'success');
              this.showEditFlightModal = false;
              await this.fetchFlightBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal mengupdate status penerbangan', 'error');
            }
          } catch (error) {
            console.error('Error updating flight booking:', error);
            this.showToastMessage('Gagal mengupdate status penerbangan', 'error');
          } finally {
            this.submittingEditFlight = false;
          }
        },

        showTicketUploadModal(booking) {
          this.currentFlightBooking = booking;
          this.showTicketModal = true;
        },

        async submitTicketUpload() {
          this.uploadingTicket = true;
          try {
            const formData = new FormData();
            formData.append('ticket_document', this.$refs.ticketFile.files[0]);

            const response = await fetch(`{{ route('admin.inventaris.flight-booking.uploadTicket', '') }}/${this.currentFlightBooking.id}`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Tiket berhasil diupload', 'success');
              this.showTicketModal = false;
              this.$refs.ticketFile.value = '';
              await this.fetchFlightBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal mengupload tiket', 'error');
            }
          } catch (error) {
            console.error('Error uploading ticket:', error);
            this.showToastMessage('Gagal mengupload tiket', 'error');
          } finally {
            this.uploadingTicket = false;
          }
        },

        async deleteFlightBooking(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus pemesanan penerbangan ini?')) {
            return;
          }

          try {
            const response = await fetch(`{{ route('admin.inventaris.flight-booking.destroy', '') }}/${id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Pemesanan penerbangan berhasil dihapus', 'success');
              await this.fetchFlightBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus pemesanan penerbangan', 'error');
            }
          } catch (error) {
            console.error('Error deleting flight booking:', error);
            this.showToastMessage('Gagal menghapus pemesanan penerbangan', 'error');
          }
        },

        // Hotel Booking Functions
        hotelBookings: [],
        availableHotels: [],
        loadingHotels: false,
        showHotelBookingModal: false,
        showEditHotelModal: false,
        showRoomAssignModal: false,
        submittingHotelBooking: false,
        submittingEditHotel: false,
        submittingAssignments: false,
        loadingAssignments: false,
        currentHotelBooking: null,
        currentAssignments: [],
        unassignedJamaah: [],
        roomAssignments: {},
        hotelBookingForm: {
          id_hotel: '',
          check_in_date: '',
          check_out_date: '',
          room_count: '',
          room_type: '',
          seller_name: '',
          seller_phone: '',
          booking_reference: '',
          notes: ''
        },
        editHotelForm: {
          id: null,
          status: '',
          booking_reference: ''
        },

        async fetchHotelBookings() {
          this.loadingHotels = true;
          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/hotel-booking/keberangkatan') }}/${this.keberangkatan.id}`);
            const result = await response.json();
            if (result.success) {
              this.hotelBookings = result.data;
            }
          } catch (error) {
            console.error('Error fetching hotel bookings:', error);
          } finally {
            this.loadingHotels = false;
          }
        },

        async fetchAvailableHotels() {
          try {
            const response = await fetch('{{ url('/admin/inventaris/travel/hotel-booking/hotels/available') }}');
            const result = await response.json();
            if (result.success) {
              this.availableHotels = result.data;
            }
          } catch (error) {
            console.error('Error fetching available hotels:', error);
          }
        },

        async submitHotelBooking() {
          this.submittingHotelBooking = true;
          try {
            const response = await fetch('{{ url('/admin/inventaris/travel/hotel-booking') }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                ...this.hotelBookingForm,
                id_keberangkatan: this.keberangkatan.id
              })
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Pemesanan hotel berhasil dibuat', 'success');
              this.showHotelBookingModal = false;
              this.hotelBookingForm = {
                id_hotel: '',
                check_in_date: '',
                check_out_date: '',
                room_count: '',
                booking_reference: '',
                notes: ''
              };
              await this.fetchHotelBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal membuat pemesanan hotel', 'error');
            }
          } catch (error) {
            console.error('Error creating hotel booking:', error);
            this.showToastMessage('Gagal membuat pemesanan hotel', 'error');
          } finally {
            this.submittingHotelBooking = false;
          }
        },

        editHotelBooking(booking) {
          this.editHotelForm = {
            id: booking.id,
            status: booking.status,
            booking_reference: booking.booking_reference || ''
          };
          this.showEditHotelModal = true;
        },

        async submitEditHotelBooking() {
          this.submittingEditHotel = true;
          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/hotel-booking') }}/${this.editHotelForm.id}/status`, {
              method: 'PUT',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                status: this.editHotelForm.status,
                booking_reference: this.editHotelForm.booking_reference
              })
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Status hotel berhasil diupdate', 'success');
              this.showEditHotelModal = false;
              await this.fetchHotelBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal mengupdate status hotel', 'error');
            }
          } catch (error) {
            console.error('Error updating hotel booking:', error);
            this.showToastMessage('Gagal mengupdate status hotel', 'error');
          } finally {
            this.submittingEditHotel = false;
          }
        },

        async showRoomAssignmentModal(booking) {
          console.log("=== SHOW ROOM ASSIGNMENT MODAL START ===");
          console.log("Hotel Booking:", booking);
          
          this.currentHotelBooking = booking;
          this.showRoomAssignModal = true;
          this.loadingAssignments = true;

          try {
            const url = `{{ url('/admin/inventaris/travel/hotel-booking') }}/${booking.id}/assignments`;
            console.log("Fetching from URL:", url);
            
            const response = await fetch(url);
            console.log("Response Status:", response.status);
            console.log("Response OK:", response.ok);
            
            const result = await response.json();
            console.log("=== API RESPONSE ===");
            console.log("Full Response:", result);
            console.log("Success:", result.success);

            if (result.success) {
              console.log("=== RESPONSE DATA ===");
              console.log("Booking:", result.data.booking);
              console.log("Assignments:", result.data.assignments);
              console.log("Unassigned Jamaah:", result.data.unassigned_jamaah);
              console.log("Unassigned Count:", result.data.unassigned_jamaah?.length || 0);
              
              this.currentAssignments = result.data.assignments;
              this.unassignedJamaah = result.data.unassigned_jamaah;
              
              console.log("=== AFTER ASSIGNMENT ===");
              console.log("this.currentAssignments:", this.currentAssignments);
              console.log("this.unassignedJamaah:", this.unassignedJamaah);
              
              // Initialize room assignments object with room_type from jamaah booking
              this.roomAssignments = {};
              
              if (this.unassignedJamaah && this.unassignedJamaah.length > 0) {
                console.log("=== INITIALIZING ROOM ASSIGNMENTS ===");
                this.unassignedJamaah.forEach(jamaah => {
                  console.log("Processing Jamaah:", jamaah);
                  this.roomAssignments[jamaah.id] = {
                    room_number: '',
                    bed_number: '',
                    room_type: jamaah.room_type || 'Standard'
                  };
                  console.log(`Initialized assignment for ${jamaah.jamaah_name}:`, this.roomAssignments[jamaah.id]);
                });
              } else {
                console.warn("No unassigned jamaah to initialize!");
              }
              
              console.log("=== FINAL ROOM ASSIGNMENTS ===");
              console.log("Room Assignments Object:", this.roomAssignments);
              console.log("Room Assignments Keys:", Object.keys(this.roomAssignments));
              console.log("Room Assignments Count:", Object.keys(this.roomAssignments).length);
            } else {
              console.error("API returned success=false");
              console.error("Error Message:", result.message);
              this.showToastMessage('Gagal memuat data room assignment', 'error');
            }
          } catch (error) {
            console.error("=== ERROR IN FETCH ===");
            console.error("Error Type:", error.name);
            console.error("Error Message:", error.message);
            console.error("Error Stack:", error.stack);
            this.showToastMessage('Gagal memuat data room assignment', 'error');
          } finally {
            this.loadingAssignments = false;
            console.log("=== SHOW ROOM ASSIGNMENT MODAL END ===");
          }
        },

        async submitRoomAssignments() {
          this.submittingAssignments = true;
          try {
            // Build assignments array from roomAssignments object
            const assignments = [];
            for (const [jamaahId, assignment] of Object.entries(this.roomAssignments)) {
              if (assignment.room_number) {
                assignments.push({
                  id_jamaah_booking: parseInt(jamaahId),
                  room_number: assignment.room_number,
                  room_type: assignment.room_type || null,
                  bed_number: assignment.bed_number ? parseInt(assignment.bed_number) : null
                });
              }
            }

            if (assignments.length === 0) {
              this.showToastMessage('Silakan isi minimal satu room assignment', 'error');
              this.submittingAssignments = false;
              return;
            }

            const response = await fetch(`{{ url('/admin/inventaris/travel/hotel-booking') }}/${this.currentHotelBooking.id}/assign-rooms`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ assignments })
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Room assignments berhasil disimpan', 'success');
              await this.showRoomAssignmentModal(this.currentHotelBooking);
              await this.fetchHotelBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal menyimpan room assignments', 'error');
            }
          } catch (error) {
            console.error('Error submitting room assignments:', error);
            this.showToastMessage('Gagal menyimpan room assignments', 'error');
          } finally {
            this.submittingAssignments = false;
          }
        },

        async removeRoomAssignment(assignmentId) {
          if (!confirm('Apakah Anda yakin ingin menghapus room assignment ini?')) {
            return;
          }

          try {
            const url = `/admin/inventaris/travel/hotel-booking/${this.currentHotelBooking.id}/assignment/${assignmentId}`;
            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Room assignment berhasil dihapus', 'success');
              await this.showRoomAssignmentModal(this.currentHotelBooking);
              await this.fetchHotelBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus room assignment', 'error');
            }
          } catch (error) {
            console.error('Error removing room assignment:', error);
            this.showToastMessage('Gagal menghapus room assignment', 'error');
          }
        },

        downloadRoomlist(bookingId) {
          window.location.href = `{{ url('/admin/inventaris/travel/hotel-booking') }}/${bookingId}/roomlist`;
        },

        async deleteHotelBooking(id) {
          if (!confirm('Apakah Anda yakin ingin menghapus pemesanan hotel ini?')) {
            return;
          }

          try {
            const response = await fetch(`{{ url('/admin/inventaris/travel/hotel-booking') }}/${id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok && result.success) {
              this.showToastMessage(result.message || 'Pemesanan hotel berhasil dihapus', 'success');
              await this.fetchHotelBookings();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus pemesanan hotel', 'error');
            }
          } catch (error) {
            console.error('Error deleting hotel booking:', error);
            this.showToastMessage('Gagal menghapus pemesanan hotel', 'error');
          }
        },

        // ===== VISA METHODS =====
        async fetchVisas() {
          this.loadingVisas = true;
          try {
            const res = await fetch(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/visas`, {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            this.visas = data.data || [];
          } catch (e) {
            console.error('Error fetching visas:', e);
          } finally {
            this.loadingVisas = false;
          }
        },

        openVisaCreate() {
          this.visaForm = { id: null, visa_type: 'umrah', seller_name: '', seller_phone: '', price_per_person: 0, status: 'pending', submission_date: '', ready_date: '', notes: '' };
          this.showVisaForm = true;
        },

        openVisaEdit(visa) {
          this.visaForm = { ...visa };
          this.showVisaForm = true;
        },

        closeVisaForm() {
          this.showVisaForm = false;
        },

        async submitVisaForm() {
          this.savingVisa = true;
          try {
            const isEdit = !!this.visaForm.id;
            const url = isEdit
              ? `{{ url('') }}/admin/travel/keberangkatan/${this.keberangkatanId}/visas/${this.visaForm.id}`
              : `{{ url('') }}/admin/travel/keberangkatan/${this.keberangkatanId}/visas`;
            const method = isEdit ? 'PUT' : 'POST';
            const res = await fetch(url, {
              method,
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
              body: JSON.stringify(this.visaForm)
            });
            const result = await res.json();
            if (res.ok) {
              this.showToastMessage(result.message || 'Visa berhasil disimpan', 'success');
              this.closeVisaForm();
              await this.fetchVisas();
            } else {
              this.showToastMessage(result.message || 'Gagal menyimpan visa', 'error');
            }
          } catch (e) {
            this.showToastMessage('Gagal menyimpan visa', 'error');
          } finally {
            this.savingVisa = false;
          }
        },

        confirmDeleteVisa(visa) {
          this.toDeleteVisa = visa;
        },

        async deleteVisaNow() {
          if (!this.toDeleteVisa) return;
          this.deletingVisa = true;
          try {
            const res = await fetch(`{{ url('') }}/admin/travel/keberangkatan/${this.keberangkatanId}/visas/${this.toDeleteVisa.id}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (res.ok) {
              this.showToastMessage(result.message || 'Visa berhasil dihapus', 'success');
              this.toDeleteVisa = null;
              await this.fetchVisas();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus visa', 'error');
            }
          } catch (e) {
            this.showToastMessage('Gagal menghapus visa', 'error');
          } finally {
            this.deletingVisa = false;
          }
        },

        // ===== REMINDER METHODS =====
        async sendReminderNow() {
          this.sendingReminder = true;
          try {
            const res = await fetch(`{{ url('') }}/admin/inventaris/travel/keberangkatan/${this.keberangkatanId}/send-reminders`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (res.ok) {
              this.showToastMessage(result.message || 'Reminder berhasil dikirim', 'success');
            } else {
              this.showToastMessage(result.message || 'Gagal mengirim reminder', 'error');
            }
          } catch (e) {
            this.showToastMessage('Gagal mengirim reminder', 'error');
          } finally {
            this.sendingReminder = false;
          }
        },

        // Watch jamaahTab to load cost when switching to cost tab
        $watch: {
          jamaahTab(value) {
            if (value === 'cost') {
              this.loadCostCalculation();
            }
          }
        }
      };
    }
  </script>
</x-layouts.admin>
