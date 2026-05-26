<!-- Modal HPP Management for Travel Package -->
<!-- IMPORTANT: This modal MUST be inside the Alpine x-data="packageCrud()" scope -->
<div x-show="showHppModal" 
     x-cloak
     x-transition.opacity 
     class="fixed inset-0 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto" 
     style="z-index: 9999;"
     @click.self="!hppLocked && closeHppModal()"
     @keydown.escape.window="!hppLocked && closeHppModal()">
  <div class="w-full max-w-4xl bg-white rounded-2xl shadow-float max-h-[90vh] flex flex-col overflow-hidden my-4"
       @click.stop>
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
      <div>
        <h3 class="font-semibold text-lg">Kelola HPP Dasar Paket</h3>
        <p class="text-sm text-slate-600" x-text="'Paket: ' + (selectedPackage?.package_name || '')"></p>
      </div>
      <div class="flex items-center gap-2">
        <!-- TEST BUTTON - Remove after testing -->
        <button type="button" 
                class="px-3 py-1.5 rounded-lg bg-lime-500 text-white text-xs font-medium hover:bg-lime-600" 
                @click="testAlpineScope()">
          🧪 Test Alpine
        </button>
        <button type="button" class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="closeHppModal()">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto">
      <!-- Package Info -->
      <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
          <div><span class="text-slate-500">Kode:</span> <span class="font-mono ml-1" x-text="selectedPackage?.package_code"></span></div>
          <div><span class="text-slate-500">Tipe:</span> <span class="ml-1 uppercase" x-text="selectedPackage?.package_type"></span></div>
          <div><span class="text-slate-500">Kapasitas:</span> <span class="ml-1 font-semibold" x-text="selectedPackage?.capacity"></span></div>
          <div><span class="text-slate-500">HPP Dasar/Orang:</span> <span class="ml-1 font-semibold text-primary-700" x-text="formatCurrency((hppForm.flight_cost||0)+getTotalExtraComponents())"></span></div>
        </div>
        <div class="mt-2 p-2 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-700">
          <i class='bx bx-info-circle'></i>
          <strong>HPP Dasar Paket</strong> — Ini adalah estimasi biaya pokok untuk keperluan simulasi profit. HPP aktual per jamaah dihitung otomatis di halaman keberangkatan berdasarkan tipe kamar booking dan add-ons.
        </div>
      </div>

      <!-- Loading -->
      <div x-show="loadingHpp" class="text-center py-8">
        <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
        <span class="ml-2 text-slate-600">Memuat data HPP...</span>
      </div>

      <div x-show="!loadingHpp" class="px-5 py-4 space-y-5">
        <!-- Lock warning -->
        <div x-show="hppLocked" class="p-3 rounded-lg bg-yellow-50 border border-yellow-200 flex items-center gap-2 text-yellow-800">
          <i class='bx bx-lock-alt text-xl'></i>
          <div><div class="font-medium">HPP Terkunci</div><div class="text-sm">HPP sudah dikunci dan tidak dapat diubah.</div></div>
        </div>

        <!-- ===== FLIGHT & HOTEL (fixed, auto-fill dari paket) ===== -->
        <div class="space-y-3">
          <!-- PENERBANGAN -->
          <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
            <div class="text-sm font-semibold text-blue-800 flex items-center gap-1 mb-3">
              <i class='bx bx-plane'></i> Biaya Penerbangan (per orang)
            </div>
            <!-- Header -->
            <div class="grid grid-cols-12 gap-2 text-xs text-blue-600 font-medium mb-1 px-1">
              <div class="col-span-5">Penerbangan</div>
              <div class="col-span-4">Pilih / Dropdown</div>
              <div class="col-span-3 text-right">Harga/Orang (Rp)</div>
            </div>
            <!-- Baris Keberangkatan -->
            <div class="grid grid-cols-12 gap-2 items-start mb-2">
              <div class="col-span-5 pt-1.5">
                <span class="text-xs text-blue-700 font-medium">Keberangkatan</span>
              </div>
              <div class="col-span-4">
                <select x-model="flightDeparture.id" @change="onFlightDepartureSelected()" :disabled="hppLocked"
                        class="w-full rounded-lg border border-blue-200 px-2 py-1.5 text-xs disabled:bg-slate-100">
                  <option value="">-- Manual --</option>
                  <template x-for="f in availableFlights" :key="f.id">
                    <option :value="f.id" x-text="f.label"></option>
                  </template>
                </select>
              </div>
              <div class="col-span-3">
                <input type="number" min="0"
                       :value="flightDeparture.id ? flightDeparture.price : flightDeparture.manual"
                       @input="flightDeparture.manual = parseFloat($event.target.value)||0; if(!flightDeparture.id){ hppForm.flight_cost = calcFlightTotal(); }"
                       :readonly="!!flightDeparture.id" :disabled="hppLocked"
                       class="w-full rounded-lg border border-blue-200 px-2 py-1.5 text-xs text-right disabled:bg-slate-100 read-only:bg-blue-100">
                <small class="block text-right text-blue-500 mt-0.5 leading-tight"
                       x-text="formatCurrency(flightDeparture.id ? flightDeparture.price : (parseFloat(flightDeparture.manual)||0))"></small>
              </div>
            </div>
            <!-- Baris Kepulangan -->
            <div class="grid grid-cols-12 gap-2 items-start">
              <div class="col-span-5 pt-1.5">
                <span class="text-xs text-blue-700 font-medium">Kepulangan</span>
              </div>
              <div class="col-span-4">
                <select x-model="flightReturn.id" @change="onFlightReturnSelected()" :disabled="hppLocked"
                        class="w-full rounded-lg border border-blue-200 px-2 py-1.5 text-xs disabled:bg-slate-100">
                  <option value="">-- Manual --</option>
                  <template x-for="f in availableFlights" :key="f.id">
                    <option :value="f.id" x-text="f.label"></option>
                  </template>
                </select>
              </div>
              <div class="col-span-3">
                <input type="number" min="0"
                       :value="flightReturn.id ? flightReturn.price : flightReturn.manual"
                       @input="flightReturn.manual = parseFloat($event.target.value)||0; if(!flightReturn.id){ hppForm.flight_cost = calcFlightTotal(); }"
                       :readonly="!!flightReturn.id" :disabled="hppLocked"
                       class="w-full rounded-lg border border-blue-200 px-2 py-1.5 text-xs text-right disabled:bg-slate-100 read-only:bg-blue-100">
                <small class="block text-right text-blue-500 mt-0.5 leading-tight"
                       x-text="formatCurrency(flightReturn.id ? flightReturn.price : (parseFloat(flightReturn.manual)||0))"></small>
              </div>
            </div>
            <!-- Total -->
            <div class="mt-2 flex justify-between text-xs text-blue-800 font-semibold border-t border-blue-200 pt-2">
              <span>Total Biaya Penerbangan/Orang:</span>
              <span x-text="formatCurrency((flightDeparture.id ? flightDeparture.price : (parseFloat(flightDeparture.manual)||0)) + (flightReturn.id ? flightReturn.price : (parseFloat(flightReturn.manual)||0)))"
                    x-effect="hppForm.flight_cost = (flightDeparture.id ? flightDeparture.price : (parseFloat(flightDeparture.manual)||0)) + (flightReturn.id ? flightReturn.price : (parseFloat(flightReturn.manual)||0))"></span>
            </div>
          </div>

          <!-- HOTEL dihapus dari HPP Dasar — HPP hotel aktual dihitung per booking jamaah -->

          <!-- INFO TRANSPORTASI SAUDI -->
          <div x-show="selectedPackage?.id_saudi_transport"
               class="rounded-xl border border-orange-200 bg-orange-50 p-3 flex items-center gap-2 text-sm text-orange-800">
            <i class='bx bx-bus text-lg flex-shrink-0'></i>
            <div>
              <span class="font-medium">Transportasi Saudi terpasang di paket ini.</span>
              Biaya transportasi saudi otomatis diisi ke komponen <em>Biaya Transportasi</em> di bawah (jika belum ada nilai).
            </div>
          </div>

          <!-- TRANSPORTASI SAUDI -->
          <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
            <div class="text-sm font-semibold text-orange-800 flex items-center gap-1 mb-3">
              <i class='bx bx-bus'></i> Biaya Transportasi Saudi (per orang)
            </div>
            <div class="grid grid-cols-12 gap-2 text-xs text-orange-600 font-medium mb-1 px-1">
              <div class="col-span-5">Transportasi</div>
              <div class="col-span-4">Pilih / Dropdown</div>
              <div class="col-span-3 text-right">Harga/Orang (Rp)</div>
            </div>
            <div class="grid grid-cols-12 gap-2 items-start">
              <div class="col-span-5 pt-1.5">
                <span class="text-xs text-orange-700 font-medium">Transportasi Saudi</span>
              </div>
              <div class="col-span-4">
                <select x-model="saudiTransportSelected.id" @change="onSaudiTransportSelected()" :disabled="hppLocked"
                        class="w-full rounded-lg border border-orange-200 px-2 py-1.5 text-xs disabled:bg-slate-100">
                  <option value="">-- Manual --</option>
                  <template x-for="t in availableSaudiTransports" :key="t.id">
                    <option :value="t.id" x-text="`${t.transport_name} (${t.type_label})${t.route ? ' - ' + t.route : ''}`"></option>
                  </template>
                </select>
              </div>
              <div class="col-span-3">
                <input type="number" min="0"
                       :value="saudiTransportSelected.id ? saudiTransportSelected.price : saudiTransportSelected.manual"
                       @input="saudiTransportSelected.manual = parseFloat($event.target.value)||0; if(!saudiTransportSelected.id){ syncSaudiTransportToComp(); }"
                       :readonly="!!saudiTransportSelected.id" :disabled="hppLocked"
                       class="w-full rounded-lg border border-orange-200 px-2 py-1.5 text-xs text-right disabled:bg-slate-100 read-only:bg-orange-100">
                <small class="block text-right text-orange-500 mt-0.5 leading-tight"
                       x-text="formatCurrency(saudiTransportSelected.id ? saudiTransportSelected.price : (parseFloat(saudiTransportSelected.manual)||0))"
                       x-effect="syncSaudiTransportToComp()"></small>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== EXTRA COMPONENTS (dynamic, add/remove) ===== -->
        <div>
          <div class="mb-2">
            <label class="text-sm font-semibold text-slate-700">Komponen Biaya Lainnya</label>
            <p class="text-xs text-slate-500 mt-0.5">Semua komponen biaya otomatis berstatus HUTANG dan akan menjadi RAB di keberangkatan</p>
          </div>

          <div class="space-y-2">
            <!-- Hanya tampilkan komponen yang sudah diisi (value > 0) atau custom -->
            <template x-for="(comp, idx) in hppExtraComponents" :key="comp.id">
              <div x-show="!comp.isDefault || (comp.value > 0)" class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="flex items-start gap-2">
                  <!-- Label -->
                  <div class="flex-1 min-w-0">
                    <input type="text" x-model="comp.label" :disabled="hppLocked || comp.isDefault"
                           class="w-full text-sm font-medium bg-transparent border-0 border-b border-slate-300 focus:border-primary-400 focus:outline-none disabled:text-slate-700 disabled:border-transparent"
                           placeholder="Nama komponen">
                    <div class="text-xs text-slate-400 mt-0.5" x-text="comp.hint || 'Biaya per orang'"></div>
                  </div>
                  <!-- Value -->
                  <div class="w-36 flex-shrink-0">
                    <input type="number" min="0" step="0.01" x-model.number="comp.value" :disabled="hppLocked"
                           class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-right disabled:bg-slate-100"
                           placeholder="0">
                    <small class="block text-right text-slate-400 mt-0.5 leading-tight"
                           x-text="formatCurrency(comp.value||0)"></small>
                  </div>
                  <!-- Status Badge (read-only, always HUTANG) -->
                  <div class="w-24 flex-shrink-0 pt-1">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border border-red-300 bg-red-50 text-red-800 text-xs font-medium">
                      <i class='bx bx-error-circle'></i> HUTANG
                    </span>
                  </div>
                  <!-- Remove (hanya untuk custom components) -->
                  <button type="button" @click="removeExtraComponent(idx)" :disabled="hppLocked || comp.isDefault"
                          x-show="!comp.isDefault"
                          class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-40 flex-shrink-0 mt-0.5">
                    <i class='bx bx-trash text-sm'></i>
                  </button>
                </div>
                <!-- Info Hutang -->
                <div class="mt-2 p-2 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2">
                  <i class='bx bx-info-circle text-red-600 text-sm flex-shrink-0 mt-0.5'></i>
                  <div class="text-xs text-red-700">
                    <span class="font-medium">Nilai Terhutang Total:</span>
                    <span class="font-mono ml-1" x-text="formatCurrency((comp.value||0) * (selectedPackage?.capacity||1))"></span>
                    <span class="text-red-500 ml-1">— Akan otomatis menjadi item RAB per keberangkatan</span>
                  </div>
                </div>
              </div>
            </template>
            
            <!-- Tombol Tambah Komponen di bawah -->
            <div class="pt-2">
              <button type="button" @click="addExtraComponent()" :disabled="hppLocked"
                      class="w-full inline-flex items-center justify-center gap-1 text-sm px-4 py-2.5 rounded-lg border-2 border-dashed border-green-300 text-green-700 hover:bg-green-50 hover:border-green-400 disabled:opacity-40 disabled:cursor-not-allowed">
                <i class='bx bx-plus text-lg'></i> Tambah Komponen Biaya Lainnya
              </button>
            </div>
            
            <div x-show="hppExtraComponents.filter(c => !c.isDefault || c.value > 0).length === 0" class="text-center py-4 text-slate-400 text-sm">
              Belum ada komponen biaya. Klik tombol di bawah untuk menambahkan.
            </div>
          </div>
        </div>

        <!-- ===== SUMMARY ===== -->
        <div class="rounded-xl border-2 border-primary-200 bg-primary-50 p-4">
          <div class="text-sm font-semibold text-primary-900 mb-3">
            Ringkasan HPP Dasar — Kapasitas: <span x-text="selectedPackage?.capacity || 0"></span> jamaah
          </div>

          <!-- Fixed: Flight only (hotel dihitung per booking) -->
          <div class="space-y-1.5 text-sm mb-3">
            <div class="flex justify-between">
              <span class="text-slate-600">Biaya Penerbangan:</span>
              <div class="text-right">
                <span class="font-mono font-semibold" x-text="formatCurrency((hppForm.flight_cost||0)*(selectedPackage?.capacity||0))"></span>
                <span class="text-xs text-slate-400 ml-1" x-text="'(' + formatCurrency(hppForm.flight_cost||0) + ' × ' + (selectedPackage?.capacity||0) + ')'"></span>
              </div>
            </div>
            <div class="p-2 rounded-lg bg-purple-50 border border-purple-200 text-xs text-purple-700">
              <i class='bx bx-hotel'></i> Biaya hotel tidak termasuk HPP dasar — dihitung otomatis per jamaah berdasarkan tipe kamar booking (Quad/Triple/Double) dan add-ons.
            </div>
            <!-- Extra components -->
            <template x-for="comp in hppExtraComponents" :key="comp.id">
              <div class="flex justify-between" x-show="(comp.value||0) > 0">
                <span class="text-slate-600" x-text="comp.label || 'Komponen'"></span>
                <div class="text-right">
                  <span class="font-mono font-semibold" x-text="formatCurrency((comp.value||0)*(selectedPackage?.capacity||0))"></span>
                  <span class="text-xs text-slate-400 ml-1" x-text="'(' + formatCurrency(comp.value||0) + ' × ' + (selectedPackage?.capacity||0) + ')'"></span>
                </div>
              </div>
            </template>
          </div>

          <div class="flex justify-between pt-2 border-t-2 border-primary-300 font-semibold">
            <span class="text-primary-900">Total HPP Dasar:</span>
            <span class="font-mono text-primary-900" x-text="formatCurrency(calculateTotalHpp())"></span>
          </div>
          <div class="flex justify-between mt-1 text-sm">
            <span class="text-slate-600">HPP Dasar per Orang:</span>
            <span class="font-mono font-medium text-primary-700"
                  x-text="formatCurrency((hppForm.flight_cost||0)+getTotalExtraComponents())"></span>
          </div>

          <!-- ===== SIMULASI PER PRICE PACKAGE & VARIAN ===== -->
          <template x-if="selectedPackage?.price_packages && selectedPackage.price_packages.length > 0">
            <div class="mt-4 pt-3 border-t border-primary-200">
              <div class="text-sm font-semibold text-primary-900 mb-2">Simulasi Revenue & Profit per Paket Harga</div>
              <template x-for="pkg in (selectedPackage.price_packages || [])" :key="pkg.name">
                <div class="mb-3 rounded-lg border border-primary-200 bg-white overflow-hidden">
                  <div class="px-3 py-2 bg-primary-100 text-sm font-semibold text-primary-800" x-text="pkg.name"></div>
                  <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                      <tr>
                        <th class="text-left px-3 py-1.5 text-slate-600">Varian</th>
                        <th class="text-right px-3 py-1.5 text-slate-600">Harga/Orang</th>
                        <th class="text-right px-3 py-1.5 text-slate-600">Total Revenue</th>
                        <th class="text-right px-3 py-1.5 text-slate-600">Profit/Orang</th>
                        <th class="text-right px-3 py-1.5 text-slate-600">Margin</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template x-for="v in (pkg.variants || [])" :key="v.type">
                        <tr class="border-t border-slate-100">
                          <td class="px-3 py-1.5 capitalize font-medium" x-text="v.type"></td>
                          <td class="px-3 py-1.5 text-right font-mono" x-text="formatCurrency(parseFloat(v.price)||0)"></td>
                          <td class="px-3 py-1.5 text-right font-mono text-green-700"
                              x-text="formatCurrency((parseFloat(v.price)||0)*(selectedPackage?.capacity||0))"></td>
                          <td class="px-3 py-1.5 text-right font-mono"
                              :class="((parseFloat(v.price)||0) - ((hppForm.flight_cost||0)+getTotalExtraComponents())) >= 0 ? 'text-green-700' : 'text-red-600'"
                              x-text="formatCurrency((parseFloat(v.price)||0) - ((hppForm.flight_cost||0)+getTotalExtraComponents()))"></td>
                          <td class="px-3 py-1.5 text-right font-mono"
                              :class="((parseFloat(v.price)||0) > 0 && (((parseFloat(v.price)||0) - ((hppForm.flight_cost||0)+getTotalExtraComponents())) / (parseFloat(v.price)||1) * 100) >= 0) ? 'text-green-700' : 'text-red-600'"
                              x-text="(parseFloat(v.price)||0) > 0 ? (((parseFloat(v.price)||0) - ((hppForm.flight_cost||0)+getTotalExtraComponents())) / (parseFloat(v.price)||1) * 100).toFixed(1) + '%' : '-'"></td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </template>
            </div>
          </template>

          <!-- Fallback: single price -->
          <template x-if="!selectedPackage?.price_packages || selectedPackage.price_packages.length === 0">
            <div class="mt-3 pt-3 border-t border-primary-200 space-y-1 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-600">Harga Jual (per orang):</span>
                <span class="font-mono font-medium text-green-700" x-text="formatCurrency(selectedPackage?.price||0)"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Total Revenue:</span>
                <span class="font-mono font-medium text-green-700" x-text="formatCurrency((selectedPackage?.price||0)*(selectedPackage?.capacity||0))"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Profit per Orang:</span>
                <span class="font-mono font-medium"
                      :class="((selectedPackage?.price||0)-((hppForm.flight_cost||0)+getTotalExtraComponents()))>=0?'text-green-700':'text-red-700'"
                      x-text="formatCurrency((selectedPackage?.price||0)-((hppForm.flight_cost||0)+getTotalExtraComponents()))"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Profit Margin:</span>
                <span class="font-mono font-medium"
                      :class="calculateProfitMargin()>=0?'text-green-700':'text-red-700'"
                      x-text="calculateProfitMargin().toFixed(2)+'%'"></span>
              </div>
            </div>
          </template>
        </div>

        <!-- Warning -->
        <div x-show="(selectedPackage?.price||0) < ((hppForm.flight_cost||0)+getTotalExtraComponents())"
             class="p-3 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2 text-red-800 text-sm">
          <i class='bx bx-error text-xl flex-shrink-0'></i>
          <div>
            <div class="font-medium">Harga jual lebih rendah dari HPP per orang</div>
            <div class="text-xs mt-0.5">Margin akan negatif. Periksa kembali komponen biaya atau harga jual.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-5 py-4 border-t border-slate-100 flex justify-between flex-shrink-0">
      <button x-show="!hppLocked" @click="confirmLockHpp()"
              class="rounded-lg border border-yellow-500 text-yellow-700 px-4 py-2 hover:bg-yellow-50 text-sm">
        <i class='bx bx-lock-alt'></i> Kunci HPP
      </button>
      <div class="flex gap-2">
        <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50 text-sm" @click="closeHppModal()">Tutup</button>
        <button x-show="!hppLocked" 
                type="button"
                class="rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 text-sm disabled:opacity-50"
                @click.prevent="submitHppForm()" 
                :disabled="savingHpp">
          <span x-show="!savingHpp">Simpan HPP</span>
          <span x-show="savingHpp" class="flex items-center gap-2"><i class='bx bx-loader-alt bx-spin'></i> Menyimpan...</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirm Lock HPP -->
<div x-show="showLockConfirm" x-cloak x-transition.opacity
     class="fixed inset-0 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto"
     style="display: none; z-index: 10000;"
     @keydown.escape.window="showLockConfirm = false">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-float my-4">
    <div class="px-5 py-4">
      <div class="font-semibold text-lg">Kunci HPP Calculation?</div>
      <p class="text-slate-600 mt-2 text-sm">Setelah dikunci, HPP tidak dapat diubah lagi. Pastikan semua data sudah benar.</p>
      <div class="mt-4 p-4 rounded-lg bg-slate-50 border border-slate-200">
        <div class="text-sm font-medium mb-1">Total HPP yang akan dikunci:</div>
        <div class="text-2xl font-bold text-primary-700" x-text="formatCurrency(calculateTotalHpp())"></div>
      </div>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
      <button class="rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50 text-sm"
              @click="showLockConfirm = false; document.body.style.overflow = '';">Batal</button>
      <button @click="lockHppNow()" :disabled="lockingHpp"
              class="rounded-lg bg-yellow-600 text-white px-4 py-2 hover:bg-yellow-700 disabled:opacity-50 text-sm">
        <span x-show="!lockingHpp"><i class='bx bx-lock-alt'></i> Ya, Kunci HPP</span>
        <span x-show="lockingHpp" class="flex items-center gap-2"><i class='bx bx-loader-alt bx-spin'></i> Mengunci...</span>
      </button>
    </div>
  </div>
</div>

<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/hpp-modal.blade.php ENDPATH**/ ?>