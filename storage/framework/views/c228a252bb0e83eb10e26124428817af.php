

<div x-show="showHppModal"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 flex items-start justify-center bg-black/40 p-2 pt-6 overflow-y-auto"
     style="z-index: 9999;"
     @click.self="!hppLocked && closeHppModal()"
     @keydown.escape.window="!hppLocked && closeHppModal()">
  <div class="w-full max-w-5xl bg-white rounded-2xl shadow-float max-h-[92vh] flex flex-col overflow-hidden my-2"
       @click.stop>

    
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
      <div>
        <h3 class="font-semibold text-base">Kelola HPP Dasar Paket</h3>
        <p class="text-xs text-slate-500" x-text="'Paket: ' + (selectedPackage?.package_name || '')"></p>
      </div>
      <div class="flex items-center gap-2">
        
        <div class="flex items-center gap-1 text-xs bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
          <i class='bx bx-money text-slate-500'></i>
          <span class="text-slate-500">Kurs:</span>
          <template x-for="(rate, cur) in hppKurs" :key="cur">
            <div x-show="cur !== 'IDR'" class="flex items-center gap-1 ml-1">
              <span class="font-mono text-slate-700 text-xs" x-text="cur + '='"></span>
              <input type="number" min="0" step="100" x-model.number="hppKurs[cur]"
                     class="w-20 border border-slate-200 rounded px-1 py-0.5 text-xs text-right">
            </div>
          </template>
        </div>
        <button type="button" class="p-1.5 hover:bg-slate-100 rounded-lg" @click="closeHppModal()">
          <i class='bx bx-x text-lg'></i>
        </button>
      </div>
    </div>

    
    <div class="flex-1 overflow-y-auto">
      
      <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
        <div class="flex flex-wrap gap-4 text-xs">
          <div><span class="text-slate-500">Kode:</span> <span class="font-mono ml-1" x-text="selectedPackage?.package_code"></span></div>
          <div><span class="text-slate-500">Tipe:</span> <span class="ml-1 uppercase" x-text="selectedPackage?.package_type"></span></div>
          <div><span class="text-slate-500">Kapasitas:</span> <span class="ml-1 font-semibold" x-text="selectedPackage?.capacity"></span></div>
          <div><span class="text-slate-500">Jamaah Booking:</span> <span class="ml-1 font-semibold text-green-700" x-text="hppBookedCount || selectedPackage?.capacity || 0"></span></div>
          <div><span class="text-slate-500">HPP/Orang:</span> <span class="ml-1 font-semibold text-primary-700" x-text="formatCurrency(getTotalHppPerUnit())"></span></div>
        </div>
      </div>

      <div x-show="loadingHpp" class="text-center py-6">
        <i class='bx bx-loader-alt bx-spin text-xl text-slate-400'></i>
        <span class="ml-2 text-slate-500 text-sm">Memuat...</span>
      </div>

      <div x-show="!loadingHpp" class="px-4 py-3 space-y-3">
        
        <div x-show="hppLocked" class="p-2 rounded-lg bg-yellow-50 border border-yellow-200 flex items-center gap-2 text-yellow-800 text-xs">
          <i class='bx bx-lock-alt'></i>
          <span class="font-medium">HPP Terkunci</span> — tidak dapat diubah.
        </div>

        
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
          <div class="text-xs font-semibold text-blue-800 flex items-center gap-1 mb-2">
            <i class='bx bx-plane'></i> Biaya Penerbangan
          </div>
          
          <div class="grid grid-cols-12 gap-1 text-xs text-blue-600 font-medium mb-1">
            <div class="col-span-3">Rute</div>
            <div class="col-span-3">Maskapai</div>
            <div class="col-span-2 text-center">Mata Uang</div>
            <div class="col-span-2 text-right">Nilai</div>
            <div class="col-span-2 text-right">IDR</div>
          </div>
          
          <div class="grid grid-cols-12 gap-1 items-center mb-1.5">
            <div class="col-span-3 text-xs text-blue-700 font-medium">Keberangkatan</div>
            <div class="col-span-3">
              <select x-model="flightDeparture.id" @change="onFlightDepartureSelected()" :disabled="hppLocked"
                      class="w-full rounded border border-blue-200 px-1.5 py-1 text-xs disabled:bg-slate-100">
                <option value="">-- Manual --</option>
                <template x-for="f in availableFlights" :key="f.id">
                  <option :value="f.id" x-text="f.label"></option>
                </template>
              </select>
            </div>
            <div class="col-span-2">
              <select x-model="flightDeparture.currency" :disabled="hppLocked || !!flightDeparture.id"
                      class="w-full rounded border border-blue-200 px-1 py-1 text-xs disabled:bg-slate-100">
                <option value="IDR">IDR</option>
                <option value="SAR">SAR</option>
                <option value="USD">USD</option>
                <option value="AED">AED</option>
                <option value="TL">TL</option>
              </select>
            </div>
            <div class="col-span-2">
              <input type="number" min="0"
                     :value="flightDeparture.id ? flightDeparture.price : flightDeparture.manual"
                     @input="flightDeparture.manual = parseFloat($event.target.value)||0; if(!flightDeparture.id){ hppForm.flight_cost = calcFlightTotal(); }"
                     :readonly="!!flightDeparture.id" :disabled="hppLocked"
                     class="w-full rounded border border-blue-200 px-1.5 py-1 text-xs text-right disabled:bg-slate-100 read-only:bg-blue-100">
            </div>
            <div class="col-span-2 text-right text-xs font-semibold text-blue-800"
                 x-text="formatCurrency(convertToIDR(flightDeparture.id ? flightDeparture.price : (parseFloat(flightDeparture.manual)||0), flightDeparture.currency||'IDR'))"></div>
          </div>
          
          <div class="grid grid-cols-12 gap-1 items-center mb-1.5">
            <div class="col-span-3 text-xs text-blue-700 font-medium">Kepulangan</div>
            <div class="col-span-3">
              <select x-model="flightReturn.id" @change="onFlightReturnSelected()" :disabled="hppLocked"
                      class="w-full rounded border border-blue-200 px-1.5 py-1 text-xs disabled:bg-slate-100">
                <option value="">-- Manual --</option>
                <template x-for="f in availableFlights" :key="f.id">
                  <option :value="f.id" x-text="f.label"></option>
                </template>
              </select>
            </div>
            <div class="col-span-2">
              <select x-model="flightReturn.currency" :disabled="hppLocked || !!flightReturn.id"
                      class="w-full rounded border border-blue-200 px-1 py-1 text-xs disabled:bg-slate-100">
                <option value="IDR">IDR</option>
                <option value="SAR">SAR</option>
                <option value="USD">USD</option>
                <option value="AED">AED</option>
                <option value="TL">TL</option>
              </select>
            </div>
            <div class="col-span-2">
              <input type="number" min="0"
                     :value="flightReturn.id ? flightReturn.price : flightReturn.manual"
                     @input="flightReturn.manual = parseFloat($event.target.value)||0; if(!flightReturn.id){ hppForm.flight_cost = calcFlightTotal(); }"
                     :readonly="!!flightReturn.id" :disabled="hppLocked"
                     class="w-full rounded border border-blue-200 px-1.5 py-1 text-xs text-right disabled:bg-slate-100 read-only:bg-blue-100">
            </div>
            <div class="col-span-2 text-right text-xs font-semibold text-blue-800"
                 x-text="formatCurrency(convertToIDR(flightReturn.id ? flightReturn.price : (parseFloat(flightReturn.manual)||0), flightReturn.currency||'IDR'))"></div>
          </div>
          
          <div class="flex justify-between text-xs text-blue-800 font-semibold border-t border-blue-200 pt-1.5">
            <span>Total/Orang:</span>
            <span x-text="formatCurrency(calcFlightTotal())"
                  x-effect="hppForm.flight_cost = calcFlightTotal()"></span>
          </div>
        </div>

        
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <div>
              <span class="text-xs font-semibold text-slate-700">Komponen Biaya Lainnya</span>
              <span class="text-xs text-slate-400 ml-1">— nilai akan dikonversi ke IDR</span>
            </div>
          </div>

          
          <div class="rounded-t-lg border border-slate-200 bg-slate-50 px-2 py-1.5">
            <div class="grid gap-1 text-xs text-slate-500 font-medium" style="grid-template-columns: 2fr 1.5fr 0.7fr 0.7fr 1fr 1.2fr 1.2fr 0.5fr">
              <div>Nama Komponen</div>
              <div>Mata Uang / Kurs</div>
              <div class="text-right">Nilai</div>
              <div class="text-center">Qty</div>
              <div>Satuan</div>
              <div class="text-right">Total (mata uang)</div>
              <div class="text-right">Total IDR</div>
              <div></div>
            </div>
          </div>

          
          <div class="border-l border-r border-slate-200 divide-y divide-slate-100">
            <template x-for="(comp, idx) in hppExtraComponents" :key="comp.id">
              <div x-show="!comp.isDefault || (comp.value > 0)"
                   class="px-2 py-1.5 bg-white">
                <div class="grid gap-1 items-center" style="grid-template-columns: 2fr 1.5fr 0.7fr 0.7fr 1fr 1.2fr 1.2fr 0.5fr">
                  
                  <input type="text" x-model="comp.label" :disabled="hppLocked"
                         class="text-xs font-medium bg-transparent border-0 border-b border-slate-200 focus:border-primary-400 focus:outline-none disabled:border-transparent w-full"
                         placeholder="Nama komponen">
                  
                  <div class="flex gap-1 items-center">
                    <select x-model="comp.currency" :disabled="hppLocked"
                            class="rounded border border-slate-200 px-1 py-0.5 text-xs w-16 disabled:bg-slate-100">
                      <option value="IDR">IDR</option>
                      <option value="SAR">SAR</option>
                      <option value="USD">USD</option>
                      <option value="AED">AED</option>
                      <option value="TL">TL</option>
                    </select>
                    <span x-show="comp.currency && comp.currency !== 'IDR'" class="text-xs text-slate-400 font-mono"
                          x-text="'×' + (hppKurs[comp.currency]||1).toLocaleString('id-ID')"></span>
                  </div>
                  
                  <input type="number" min="0" step="0.01" x-model.number="comp.value" :disabled="hppLocked"
                         class="rounded border border-slate-200 px-1 py-0.5 text-xs text-right disabled:bg-slate-100 w-full">
                  
                  <input type="number" min="1" x-model.number="comp.qty" :disabled="hppLocked"
                         class="rounded border border-slate-200 px-1 py-0.5 text-xs text-center disabled:bg-slate-100 w-full">
                  
                  <select x-model="comp.satuan" :disabled="hppLocked"
                          class="rounded border border-slate-200 px-1 py-0.5 text-xs disabled:bg-slate-100 w-full">
                    <option value="pax">per orang/pax</option>
                    <option value="pcs">pcs</option>
                    <option value="unit">unit</option>
                    <option value="paket">paket</option>
                    <option value="trip">trip</option>
                    <option value="malam">malam</option>
                    <option value="hari">hari</option>
                  </select>
                  
                  <div class="text-right text-xs font-mono text-slate-600"
                       x-text="(comp.currency||'IDR') + ' ' + ((comp.value||0)*(comp.qty||1)).toLocaleString('id-ID')"></div>
                  
                  <div class="text-right text-xs font-semibold text-slate-800"
                       x-text="formatCurrency(convertToIDR((comp.value||0)*(comp.qty||1), comp.currency||'IDR'))"></div>
                  
                  <button type="button" @click="removeExtraComponent(idx)" :disabled="hppLocked"
                          class="p-1 rounded border border-red-200 text-red-500 hover:bg-red-50 disabled:opacity-30 text-xs">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
                
                <div x-show="comp.currency && comp.currency !== 'IDR'" class="mt-0.5 text-xs text-amber-600 pl-0">
                  <span x-text="comp.currency + ' ' + (comp.value||0) + ' × kurs ' + (hppKurs[comp.currency]||1).toLocaleString('id-ID') + ' = IDR ' + convertToIDR(comp.value||0, comp.currency||'IDR').toLocaleString('id-ID') + ' per satuan'"></span>
                </div>
              </div>
            </template>

            <div x-show="hppExtraComponents.filter(c => !c.isDefault || c.value > 0).length === 0"
                 class="py-4 text-center text-xs text-slate-400 bg-white">
              Belum ada komponen. Klik Tambah.
            </div>
          </div>

          
          <div class="border border-t-0 border-slate-200 rounded-b-lg bg-slate-50 px-2 py-1.5 flex items-center justify-between">
            <button type="button" @click="addExtraComponent()" :disabled="hppLocked"
                    class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded border-2 border-dashed border-green-300 text-green-700 hover:bg-green-50 disabled:opacity-40">
              <i class='bx bx-plus'></i> Tambah Komponen
            </button>
            <div class="text-xs font-semibold text-slate-700">
              Total Komponen: <span class="font-mono text-primary-700" x-text="formatCurrency(getTotalExtraIDR())"></span>
            </div>
          </div>
        </div>

        
        <div class="rounded-lg border-2 border-primary-200 bg-primary-50 p-3">
          <div class="text-xs font-semibold text-primary-900 mb-2">
            Ringkasan HPP Dasar
          </div>

          
          <div class="text-xs space-y-1 mb-2">
            <div class="flex justify-between">
              <span class="text-slate-600">Biaya Penerbangan
                <span class="text-slate-400 ml-1">(× <span x-text="hppFlightQty || selectedPackage?.capacity || 0"></span> pax)</span>
              </span>
              <div class="text-right">
                <span class="font-mono font-semibold" x-text="formatCurrency((hppForm.flight_cost||0) * (hppFlightQty || selectedPackage?.capacity || 0))"></span>
                <span class="text-slate-400 ml-1" x-text="'(' + formatCurrency(hppForm.flight_cost||0) + '/orang)'"></span>
              </div>
            </div>

            
            <template x-for="comp in hppExtraComponents" :key="comp.id">
              <div class="flex justify-between" x-show="(comp.value||0) > 0">
                <span class="text-slate-600">
                  <span x-text="comp.label || 'Komponen'"></span>
                  <span class="text-slate-400 ml-1">
                    (× <span x-text="comp.qty||1"></span> <span x-text="comp.satuan||'pax'"></span>)
                  </span>
                </span>
                <div class="text-right">
                  <span class="font-mono font-semibold" x-text="formatCurrency(convertToIDR((comp.value||0)*(comp.qty||1), comp.currency||'IDR'))"></span>
                  <span x-show="comp.currency && comp.currency !== 'IDR'" class="text-amber-600 ml-1 text-xs"
                        x-text="'(' + comp.currency + ' ' + ((comp.value||0)*(comp.qty||1)).toLocaleString('id-ID') + ')'"></span>
                </div>
              </div>
            </template>
          </div>

          <div class="flex justify-between pt-1.5 border-t-2 border-primary-300 font-semibold text-sm">
            <span class="text-primary-900">Total HPP Dasar (semua komponen):</span>
            <span class="font-mono text-primary-900" x-text="formatCurrency(calculateTotalHpp())"></span>
          </div>
          <div class="flex justify-between mt-0.5 text-xs">
            <span class="text-slate-600">HPP per orang (flight ÷ pax + komponen ÷ qty):</span>
            <span class="font-mono font-medium text-primary-700" x-text="formatCurrency(getTotalHppPerUnit())"></span>
          </div>

          
          <template x-if="selectedPackage?.price_packages && selectedPackage.price_packages.length > 0">
            <div class="mt-2 pt-2 border-t border-primary-200">
              <div class="text-xs font-semibold text-primary-900 mb-1.5">Simulasi Profit per Paket Harga</div>
              <template x-for="pkg in (selectedPackage.price_packages || [])" :key="pkg.name">
                <div class="mb-2 rounded-lg border border-primary-200 bg-white overflow-hidden">
                  <div class="px-2 py-1 bg-primary-100 text-xs font-semibold text-primary-800" x-text="pkg.name"></div>
                  <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                      <tr>
                        <th class="text-left px-2 py-1 text-slate-500">Varian</th>
                        <th class="text-right px-2 py-1 text-slate-500">Harga/Org</th>
                        <th class="text-right px-2 py-1 text-slate-500">HPP/Org</th>
                        <th class="text-right px-2 py-1 text-slate-500">Profit/Org</th>
                        <th class="text-right px-2 py-1 text-slate-500">Margin</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template x-for="v in (pkg.variants || [])" :key="v.type">
                        <tr class="border-t border-slate-100">
                          <td class="px-2 py-1 capitalize font-medium" x-text="v.type"></td>
                          <td class="px-2 py-1 text-right font-mono" x-text="formatCurrency(parseFloat(v.price)||0)"></td>
                          <td class="px-2 py-1 text-right font-mono text-slate-500" x-text="formatCurrency(getTotalHppPerUnit())"></td>
                          <td class="px-2 py-1 text-right font-mono"
                              :class="((parseFloat(v.price)||0)-getTotalHppPerUnit())>=0?'text-green-700':'text-red-600'"
                              x-text="formatCurrency((parseFloat(v.price)||0)-getTotalHppPerUnit())"></td>
                          <td class="px-2 py-1 text-right font-mono"
                              :class="(parseFloat(v.price)||0)>0&&(((parseFloat(v.price)||0)-getTotalHppPerUnit())/(parseFloat(v.price)||1)*100)>=0?'text-green-700':'text-red-600'"
                              x-text="(parseFloat(v.price)||0)>0?(((parseFloat(v.price)||0)-getTotalHppPerUnit())/(parseFloat(v.price)||1)*100).toFixed(1)+'%':'-'"></td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </template>
            </div>
          </template>

          <template x-if="!selectedPackage?.price_packages || selectedPackage.price_packages.length === 0">
            <div class="mt-2 pt-1.5 border-t border-primary-200 space-y-0.5 text-xs">
              <div class="flex justify-between">
                <span class="text-slate-600">Harga Jual/Org:</span>
                <span class="font-mono text-green-700" x-text="formatCurrency(selectedPackage?.price||0)"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Profit/Org:</span>
                <span class="font-mono"
                      :class="((selectedPackage?.price||0)-getTotalHppPerUnit())>=0?'text-green-700':'text-red-700'"
                      x-text="formatCurrency((selectedPackage?.price||0)-getTotalHppPerUnit())"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Margin:</span>
                <span class="font-mono"
                      :class="calculateProfitMargin()>=0?'text-green-700':'text-red-700'"
                      x-text="calculateProfitMargin().toFixed(2)+'%'"></span>
              </div>
            </div>
          </template>
        </div>

        
        <div x-show="(selectedPackage?.price||0) < getTotalHppPerUnit()"
             class="p-2 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2 text-red-800 text-xs">
          <i class='bx bx-error text-sm flex-shrink-0 mt-0.5'></i>
          <div><span class="font-medium">Harga jual lebih rendah dari HPP per orang.</span> Margin negatif.</div>
        </div>
      </div>
    </div>

    
    <div class="px-4 py-2.5 border-t border-slate-100 flex justify-between items-center flex-shrink-0">
      <button x-show="!hppLocked" @click="confirmLockHpp()"
              class="rounded-lg border border-yellow-500 text-yellow-700 px-3 py-1.5 hover:bg-yellow-50 text-xs">
        <i class='bx bx-lock-alt'></i> Kunci HPP
      </button>
      <div class="flex gap-2">
        <button class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-xs" @click="closeHppModal()">Tutup</button>
        <button x-show="!hppLocked"
                type="button"
                class="rounded-lg bg-primary-600 text-white px-4 py-1.5 hover:bg-primary-700 text-xs disabled:opacity-50"
                @click.prevent="submitHppForm()"
                :disabled="savingHpp">
          <span x-show="!savingHpp">Simpan HPP</span>
          <span x-show="savingHpp" class="flex items-center gap-1"><i class='bx bx-loader-alt bx-spin'></i> Menyimpan...</span>
        </button>
      </div>
    </div>
  </div>
</div>


<div x-show="showLockConfirm" x-cloak x-transition.opacity
     class="fixed inset-0 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto"
     style="display: none; z-index: 10000;"
     @keydown.escape.window="showLockConfirm = false">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-float my-4">
    <div class="px-4 py-3">
      <div class="font-semibold">Kunci HPP Calculation?</div>
      <p class="text-slate-600 mt-1 text-sm">Setelah dikunci, HPP tidak dapat diubah lagi.</p>
      <div class="mt-3 p-3 rounded-lg bg-slate-50 border border-slate-200 text-sm">
        Total HPP: <span class="font-bold text-primary-700 ml-2" x-text="formatCurrency(calculateTotalHpp())"></span>
      </div>
    </div>
    <div class="px-4 py-2.5 border-t border-slate-100 flex items-center justify-end gap-2">
      <button class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-sm"
              @click="showLockConfirm = false;">Batal</button>
      <button @click="lockHppNow()" :disabled="lockingHpp"
              class="rounded-lg bg-yellow-600 text-white px-3 py-1.5 hover:bg-yellow-700 disabled:opacity-50 text-sm">
        <span x-show="!lockingHpp"><i class='bx bx-lock-alt'></i> Kunci</span>
        <span x-show="lockingHpp" class="flex items-center gap-1"><i class='bx bx-loader-alt bx-spin'></i></span>
      </button>
    </div>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/hpp-modal.blade.php ENDPATH**/ ?>