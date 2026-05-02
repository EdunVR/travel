<x-layouts.admin :title="'Master / Hotel'">
  <div x-data="hotelCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Data Hotel</h1>
        <p class="text-slate-600 text-sm">Kelola data hotel untuk paket travel.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasPermission('master.hotel.create')
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Hotel
        </button>
        @endhasPermission
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari nama hotel, lokasi, kota…"
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <div class="lg:col-span-3">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Outlet</option>
            <template x-for="o in userOutlets" :key="o.id_outlet"><option :value="o.id_outlet" x-text="o.nama_outlet"></option></template>
          </select>
        </div>
        <div class="lg:col-span-3">
          <select x-model="cityFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Kota</option>
            <template x-for="c in cities" :key="c"><option :value="c" x-text="c"></option></template>
          </select>
        </div>
        <div class="lg:col-span-2">
          <select x-model="starFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Semua Rating</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <div class="grid grid-cols-2 gap-2 lg:col-span-4">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="hotel_name">Nama Hotel</option>
            <option value="city">Kota</option>
            <option value="star_rating">Rating</option>
            <option value="total_rooms">Total Kamar</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="asc">Naik</option><option value="desc">Turun</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    <!-- TABLE -->
    <div x-show="!loading">
      <div class="hidden md:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3">Nama Hotel</th>
              <th class="text-left px-4 py-3">Lokasi</th>
              <th class="text-left px-4 py-3">Kota/Negara</th>
              <th class="text-left px-4 py-3">Rating</th>
              <th class="text-left px-4 py-3">Total Kamar</th>
              <th class="text-left px-4 py-3">Tipe Kamar</th>
              <th class="px-4 py-3 text-right w-56">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="h in hotels" :key="h.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-medium" x-text="h.hotel_name"></td>
                <td class="px-4 py-3 text-slate-600" x-text="h.location"></td>
                <td class="px-4 py-3" x-text="h.city_country"></td>
                <td class="px-4 py-3" x-html="h.star_rating"></td>
                <td class="px-4 py-3" x-text="h.total_rooms"></td>
                <td class="px-4 py-3">
                  <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200" 
                        x-text="h.room_types_count + ' tipe'"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    @hasPermission('master.hotel.view')
                    <button x-on:click="viewRoomTypes(h)" class="inline-flex items-center gap-1 rounded-lg border border-blue-200 text-blue-700 px-2 py-1.5 hover:bg-blue-50 text-xs">
                      <i class='bx bx-door-open'></i> Tipe Kamar
                    </button>
                    @endhasPermission
                    
                    @hasPermission('master.hotel.edit')
                    <button x-on:click="openEdit(h)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 hover:bg-slate-50 text-xs">
                      <i class='bx bx-edit-alt'></i> Edit
                    </button>
                    @endhasPermission
                    
                    @hasPermission('master.hotel.delete')
                    <button x-on:click="confirmDelete(h)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-2 py-1.5 hover:bg-red-50 text-xs">
                      <i class='bx bx-trash'></i> Hapus
                    </button>
                    @endhasPermission
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="hotels.length===0"><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile list -->
      <div class="md:hidden grid grid-cols-1 gap-3">
        <template x-for="h in hotels" :key="h.id">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100">
                <i class='bx bx-hotel'></i>
              </div>
              <div class="flex-1">
                <div class="font-semibold" x-text="h.hotel_name"></div>
                <div class="text-sm text-slate-600 mt-1" x-text="h.location"></div>
                <div class="text-xs text-slate-500 mt-1" x-text="h.city_country"></div>
                <div class="mt-1 text-xs" x-html="h.star_rating"></div>
                <div class="mt-1 text-[11px]">
                  <span class="px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-200">
                    <span x-text="h.total_rooms"></span> kamar
                  </span>
                  <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 ml-1">
                    <span x-text="h.room_types_count"></span> tipe
                  </span>
                </div>
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              @hasPermission('master.hotel.view')
              <button x-on:click="viewRoomTypes(h)" class="flex-1 rounded-lg border border-blue-200 text-blue-700 px-3 py-2 hover:bg-blue-50 text-xs">Tipe Kamar</button>
              @endhasPermission
              
              @hasPermission('master.hotel.edit')
              <button x-on:click="openEdit(h)" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50 text-xs">Edit</button>
              @endhasPermission
              
              @hasPermission('master.hotel.delete')
              <button x-on:click="confirmDelete(h)" class="flex-1 rounded-lg border border-red-200 text-red-700 px-3 py-2 hover:bg-red-50 text-xs">Hapus</button>
              @endhasPermission
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- MODAL: Tambah/Edit Hotel -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 flex items-start justify-center p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeForm()" class="w-full max-w-3xl bg-white rounded-2xl shadow-float my-8 flex flex-col" style="max-height: calc(100vh - 4rem);">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
          <div class="font-semibold truncate" x-text="form.id ? 'Edit Hotel' : 'Tambah Hotel'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Nama Hotel <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.hotel_name" placeholder="Hotel Makkah Grand" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.hotel_name" class="text-red-500 text-xs mt-1" x-text="errors.hotel_name"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Lokasi <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.location" placeholder="Dekat Masjidil Haram" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.location" class="text-red-500 text-xs mt-1" x-text="errors.location"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Kota <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.city" placeholder="Makkah" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.city" class="text-red-500 text-xs mt-1" x-text="errors.city"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Negara <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="form.country" placeholder="Saudi Arabia" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.country" class="text-red-500 text-xs mt-1" x-text="errors.country"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Rating Bintang</label>
              <select x-model="form.star_rating" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Rating</option>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
              </select>
              <div x-show="errors.star_rating" class="text-red-500 text-xs mt-1" x-text="errors.star_rating"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Total Kamar <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="form.total_rooms" placeholder="100" min="1" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.total_rooms" class="text-red-500 text-xs mt-1" x-text="errors.total_rooms"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Kontak</label>
              <input type="text" x-model.trim="form.contact_person" placeholder="Ahmad" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.contact_person" class="text-red-500 text-xs mt-1" x-text="errors.contact_person"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Telepon</label>
              <input type="text" x-model.trim="form.phone" placeholder="+966 12 345 6789" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.phone" class="text-red-500 text-xs mt-1" x-text="errors.phone"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Email</label>
              <input type="email" x-model.trim="form.email" placeholder="hotel@example.com" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Alamat</label>
              <textarea x-model.trim="form.address" placeholder="Alamat lengkap hotel" rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
              <div x-show="errors.address" class="text-red-500 text-xs mt-1" x-text="errors.address"></div>
            </div>
            <div class="sm:col-span-2">
              <label class="text-sm text-slate-600">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.id_outlet" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                <option value="">Pilih Outlet</option>
                <template x-for="outlet in userOutlets" :key="outlet.id_outlet">
                  <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Nama Host Seller Hotel</label>
              <input type="text" x-model.trim="form.seller_name" placeholder="Nama agen/seller hotel" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
            <div>
              <label class="text-sm text-slate-600">Telepon Host Seller</label>
              <input type="text" x-model.trim="form.seller_phone" placeholder="08xxxxxxxxxx" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2 flex-shrink-0">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeForm()">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
            <span x-show="saving" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Room Types -->
    <div x-show="showRoomTypes" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 flex items-start justify-center p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeRoomTypes()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float my-8 flex flex-col" style="max-height: calc(100vh - 4rem);">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
          <div>
            <div class="font-semibold">Tipe Kamar</div>
            <div class="text-sm text-slate-600" x-text="selectedHotel?.hotel_name"></div>
          </div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeRoomTypes()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4 overflow-y-auto flex-1">
          <div class="mb-3">
            <button x-on:click="openCreateRoomType()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 text-sm">
              <i class='bx bx-plus-circle'></i> Tambah Tipe Kamar
            </button>
          </div>

          <div class="space-y-2">
            <template x-for="rt in (roomTypes || [])" :key="rt.id">
              <div class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="font-medium" x-text="rt.room_type_name"></div>
                    <div class="text-sm text-slate-600 mt-1">
                      Kapasitas: <span x-text="rt.capacity"></span> orang • 
                      Total Kamar: <span x-text="rt.total_rooms"></span> • 
                      Harga: Rp <span x-text="formatNumber(rt.price_per_night)"></span>/malam
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <button x-on:click="editRoomType(rt)" class="p-2 rounded-lg border border-slate-200 hover:bg-slate-100">
                      <i class='bx bx-edit-alt'></i>
                    </button>
                    <button x-on:click="confirmDeleteRoomType(rt)" class="p-2 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                      <i class='bx bx-trash'></i>
                    </button>
                  </div>
                </div>
              </div>
            </template>
            <div x-show="!roomTypes || roomTypes.length===0" class="text-center py-8 text-slate-500">Belum ada tipe kamar</div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end flex-shrink-0">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeRoomTypes()">Tutup</button>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit Room Type -->
    <div x-show="showRoomTypeForm" x-transition.opacity class="fixed inset-0 z-50 bg-black/40 flex items-start justify-center p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeRoomTypeForm()" class="w-full max-w-lg bg-white rounded-2xl shadow-float my-8">
        <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="roomTypeForm.id ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar'"></div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeRoomTypeForm()"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-4 sm:px-5 py-4">
          <div class="space-y-3">
            <div>
              <label class="text-sm text-slate-600">Nama Tipe Kamar <span class="text-red-500">*</span></label>
              <input type="text" x-model.trim="roomTypeForm.room_type_name" placeholder="Deluxe Double" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="roomTypeErrors.room_type_name" class="text-red-500 text-xs mt-1" x-text="roomTypeErrors.room_type_name"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Kapasitas (orang) <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="roomTypeForm.capacity" placeholder="2" min="1" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="roomTypeErrors.capacity" class="text-red-500 text-xs mt-1" x-text="roomTypeErrors.capacity"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Total Kamar <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="roomTypeForm.total_rooms" placeholder="20" min="1" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="roomTypeErrors.total_rooms" class="text-red-500 text-xs mt-1" x-text="roomTypeErrors.total_rooms"></div>
            </div>
            <div>
              <label class="text-sm text-slate-600">Harga per Malam (Rp) <span class="text-red-500">*</span></label>
              <input type="number" x-model.number="roomTypeForm.price_per_night" placeholder="500000" min="0" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <div x-show="roomTypeErrors.price_per_night" class="text-red-500 text-xs mt-1" x-text="roomTypeErrors.price_per_night"></div>
            </div>
          </div>
        </div>

        <div class="px-4 sm:px-5 pb-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="closeRoomTypeForm()">Batal</button>
          <button x-on:click="submitRoomTypeForm()" :disabled="savingRoomType" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
            <span x-show="savingRoomType" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!savingRoomType">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus Hotel -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 flex items-start justify-center p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDelete=null" class="w-full max-w-md rounded-2xl bg-white shadow-float my-8">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Hotel?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="toDelete?.hotel_name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="toDelete?.city_country"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDelete=null">Batal</button>
          <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deleting" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
            </span>
            <span x-show="!deleting">Hapus</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus Room Type -->
    <div x-show="toDeleteRoomType" x-transition.opacity class="fixed inset-0 z-50 bg-black/40 flex items-start justify-center p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toDeleteRoomType=null" class="w-full max-w-md rounded-2xl bg-white shadow-float my-8">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Tipe Kamar?</div>
          <p class="text-slate-600 mt-1">Data akan dihapus secara permanen.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="toDeleteRoomType?.room_type_name"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" x-on:click="toDeleteRoomType=null">Batal</button>
          <button x-on:click="deleteRoomTypeNow()" :disabled="deletingRoomType" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="deletingRoomType" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
            </span>
            <span x-show="!deletingRoomType">Hapus</span>
          </button>
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
    // Version: 2024-02-22-FINAL - selectedHotel debug
    console.log('=== HOTEL SCRIPT LOADED - VERSION 2024-02-22-FINAL ===');
    function hotelCrud(){
      return {
        // State management
        hotels: [],
        cities: [],
        userOutlets: [],
        loading: false,
        saving: false,
        deleting: false,
        
        // Filters and search
        search: '',
        outletFilter: 'ALL',
        cityFilter: 'ALL',
        starFilter: 'ALL',
        sortKey: 'hotel_name',
        sortDir: 'asc',
        
        // Form state
        showForm: false,
        form: { 
          id: null, 
          hotel_name: '', 
          location: '', 
          city: '', 
          country: '', 
          star_rating: '',
          total_rooms: '',
          contact_person: '',
          phone: '',
          email: '',
          address: '',
          seller_name: '',
          seller_phone: '',
          id_outlet: ''
        },
        errors: {},
        
        // Room types management
        showRoomTypes: false,
        selectedHotel: null,
        roomTypes: [],
        showRoomTypeForm: false,
        roomTypeForm: {
          id: null,
          room_type_name: '',
          capacity: '',
          total_rooms: '',
          price_per_night: ''
        },
        roomTypeErrors: {},
        savingRoomType: false,
        deletingRoomType: false,
        toDeleteRoomType: null,
        
        // Delete confirmation
        toDelete: null,
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          try {
            await Promise.all([
              this.fetchUserOutlets(),
              this.fetchData(),
              this.fetchCities()
            ]);
          } catch (error) {
            console.error('Error during initialization:', error);
          }
        },

        async fetchData(){
          this.loading = true;
          try {
            const params = new URLSearchParams({
              search: this.search,
              outlet_filter: this.outletFilter,
              city_filter: this.cityFilter,
              star_filter: this.starFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir
            });

            const response = await fetch(`{{ route('admin.inventaris.hotel.data') }}?${params}`);
            const data = await response.json();
            
            this.hotels = data.data.map(item => ({
              id: item.id,
              hotel_name: item.hotel_name,
              location: item.location,
              city_country: item.city_country,
              star_rating: item.star_rating,
              total_rooms: item.total_rooms,
              room_types_count: item.room_types_count,
              id_outlet: item.id_outlet,
              outlet_name: item.outlet_name
            }));
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchUserOutlets(){
          try {
            const response = await fetch('{{ route("admin.inventaris.hotel.user-outlets") }}');
            const data = await response.json();
            this.userOutlets = data;
          } catch (error) {
            console.error('Error fetching user outlets:', error);
          }
        },

        async fetchCities(){
          try {
            const response = await fetch('{{ route("admin.inventaris.hotel.cities") }}');
            const data = await response.json();
            this.cities = data;
          } catch (error) {
            console.error('Error fetching cities:', error);
          }
        },

        openCreate(){ 
          this.form = { 
            id: null, 
            hotel_name: '', 
            location: '', 
            city: '', 
            country: '', 
            star_rating: '',
            total_rooms: '',
            contact_person: '',
            phone: '',
            email: '',
            address: '',
            seller_name: '',
            seller_phone: '',
            id_outlet: ''
          }; 
          this.errors = {};
          this.showForm = true; 
        },

        async openEdit(h){ 
          try {
            const response = await fetch(`{{ route('admin.inventaris.hotel.show', '') }}/${h.id}`);
            const data = await response.json();
            
            this.form = { 
              id: data.id,
              hotel_name: data.hotel_name, 
              location: data.location, 
              city: data.city, 
              country: data.country, 
              star_rating: data.star_rating,
              total_rooms: data.total_rooms,
              contact_person: data.contact_person,
              phone: data.phone,
              email: data.email,
              address: data.address,
              seller_name: data.seller_name || '',
              seller_phone: data.seller_phone || '',
              id_outlet: data.id_outlet
            }; 
            this.errors = {};
            this.showForm = true;
          } catch (error) {
            console.error('Error loading hotel data:', error);
            this.showToastMessage('Gagal memuat data hotel', 'error');
          }
        },

        closeForm(){ 
          this.showForm = false; 
          this.errors = {};
        },

        async submitForm(){
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('admin.inventaris.hotel.update', '') }}/${this.form.id}`
              : '{{ route("admin.inventaris.hotel.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmDelete(h){ 
          this.toDelete = h; 
        },

        async deleteNow(){
          if(!this.toDelete) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('admin.inventaris.hotel.destroy', '') }}/${this.toDelete.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.toDelete = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || result.error || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        async viewRoomTypes(h){
          console.log('=== viewRoomTypes START ===');
          console.log('Received hotel object h:', JSON.stringify(h));
          console.log('Setting this.selectedHotel to:', h);
          this.selectedHotel = h;
          console.log('After assignment, this.selectedHotel is:', JSON.stringify(this.selectedHotel));
          this.showRoomTypes = true;
          await this.fetchRoomTypes(h.id);
          console.log('=== viewRoomTypes END ===');
        },

        closeRoomTypes(){
          this.showRoomTypes = false;
          this.selectedHotel = null;
          this.roomTypes = [];
        },

        async fetchRoomTypes(hotelId){
          this.roomTypes = [];
          try {
            const baseUrl = '{{ url("admin/inventaris/hotel") }}';
            const response = await fetch(`${baseUrl}/${hotelId}/room-types`);
            const data = await response.json();
            this.roomTypes = data.data || data.room_types || [];
          } catch (error) {
            console.error('Error fetching room types:', error);
            this.showToastMessage('Gagal memuat tipe kamar', 'error');
          }
        },

        openCreateRoomType(){
          console.log('=== openCreateRoomType START ===');
          console.log('Current this.selectedHotel:', JSON.stringify(this.selectedHotel));
          
          // Store hotel ID in form as fallback
          const hotelId = this.selectedHotel ? this.selectedHotel.id : null;
          
          this.roomTypeForm = {
            id: null,
            hotel_id: hotelId,  // Add hotel_id to form
            room_type_name: '',
            capacity: '',
            total_rooms: '',
            price_per_night: ''
          };
          this.roomTypeErrors = {};
          this.showRoomTypeForm = true;
          console.log('=== openCreateRoomType END ===');
        },

        editRoomType(rt){
          // Store hotel ID in form as fallback
          const hotelId = this.selectedHotel ? this.selectedHotel.id : null;
          
          this.roomTypeForm = {
            id: rt.id,
            hotel_id: hotelId,  // Add hotel_id to form
            room_type_name: rt.room_type_name,
            capacity: rt.capacity,
            total_rooms: rt.total_rooms,
            price_per_night: rt.price_per_night
          };
          this.roomTypeErrors = {};
          this.showRoomTypeForm = true;
        },

        closeRoomTypeForm(){
          this.showRoomTypeForm = false;
          this.roomTypeErrors = {};
        },

        async submitRoomTypeForm(){
          console.log('=== submitRoomTypeForm START ===');
          console.log('Current this.selectedHotel:', JSON.stringify(this.selectedHotel));
          console.log('Current this.roomTypeForm:', JSON.stringify(this.roomTypeForm));
          
          // Try to get hotel ID from selectedHotel or fallback to form
          const hotelId = this.selectedHotel?.id || this.roomTypeForm.hotel_id;
          
          if (!hotelId) {
            console.error('ERROR: No hotel ID found in selectedHotel or roomTypeForm');
            console.error('this.selectedHotel value:', this.selectedHotel);
            console.error('this.roomTypeForm value:', this.roomTypeForm);
            this.showToastMessage('Error: Hotel tidak ditemukan. Silakan tutup dan buka kembali modal tipe kamar.', 'error');
            return;
          }

          console.log('Using hotel ID:', hotelId);
          this.savingRoomType = true;
          this.roomTypeErrors = {};

          try {
            const baseUrl = '{{ url("admin/inventaris/hotel") }}';
            const url = this.roomTypeForm.id
              ? `${baseUrl}/${hotelId}/room-types/${this.roomTypeForm.id}`
              : `${baseUrl}/${hotelId}/room-types`;

            const method = this.roomTypeForm.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(this.roomTypeForm)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Tipe kamar berhasil disimpan', 'success');
              this.closeRoomTypeForm();
              await this.fetchRoomTypes(hotelId);
            } else {
              if (result.errors) {
                this.roomTypeErrors = result.errors;
              } else {
                this.showToastMessage(result.error || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving room type:', error);
            this.showToastMessage('Gagal menyimpan tipe kamar', 'error');
          } finally {
            this.savingRoomType = false;
          }
        },

        confirmDeleteRoomType(rt){
          this.toDeleteRoomType = rt;
        },

        async deleteRoomTypeNow(){
          if(!this.toDeleteRoomType) return;
          
          this.deletingRoomType = true;
          try {
            const baseUrl = '{{ url("admin/inventaris/hotel") }}';
            const url = `${baseUrl}/${this.selectedHotel.id}/room-types/${this.toDeleteRoomType.id}`;

            const response = await fetch(url, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Tipe kamar berhasil dihapus', 'success');
              this.toDeleteRoomType = null;
              await this.fetchRoomTypes(this.selectedHotel.id);
            } else {
              this.showToastMessage(result.message || result.error || 'Gagal menghapus tipe kamar', 'error');
            }
          } catch (error) {
            console.error('Error deleting room type:', error);
            this.showToastMessage('Gagal menghapus tipe kamar', 'error');
          } finally {
            this.deletingRoomType = false;
          }
        },

        formatNumber(num){
          return new Intl.NumberFormat('id-ID').format(num);
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        }
      }
    }
  </script>
</x-layouts.admin>
