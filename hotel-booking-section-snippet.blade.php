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
      </div>
