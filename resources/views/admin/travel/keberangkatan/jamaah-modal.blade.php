<!-- Modal: Manage Jamaah -->
<div x-show="showJamaahModal" 
     x-transition.opacity 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 p-4 pt-8 overflow-y-auto"
     x-on:click.self="closeJamaahModal()">
  <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[85vh] flex flex-col my-4">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <h3 class="text-lg font-semibold">Kelola Jamaah</h3>
        <p class="text-sm text-slate-600">
          <span x-text="keberangkatan.confirmed_jamaah"></span> / 
          <span x-text="keberangkatan.total_jamaah"></span> jamaah terdaftar
          (<span x-text="keberangkatan.available_capacity"></span> tersedia)
        </p>
      </div>
      <button x-on:click="closeJamaahModal()" class="p-2 hover:bg-slate-100 rounded-lg">
        <i class='bx bx-x text-xl'></i>
      </button>
    </div>

    <!-- Tabs -->
    <div class="px-6 pt-4 border-b border-slate-200">
      <div class="flex gap-4">
        <button x-on:click="jamaahTab = 'registered'" 
                :class="jamaahTab === 'registered' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-slate-600'"
                class="pb-3 px-2 font-medium text-sm">
          Jamaah Terdaftar (<span x-text="keberangkatan.jamaah_list?.length || 0"></span>)
        </button>
        <button x-on:click="jamaahTab = 'cost'; loadCostCalculation()" 
                :class="jamaahTab === 'cost' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-slate-600'"
                class="pb-3 px-2 font-medium text-sm">
          Perhitungan Biaya
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto p-6">
      <!-- Tab: Registered Jamaah -->
      <div x-show="jamaahTab === 'registered'">
        <div x-show="keberangkatan.jamaah_list?.length === 0" class="text-center py-8 text-slate-500">
          <i class='bx bx-user-x text-4xl mb-2'></i>
          <p>Belum ada jamaah terdaftar</p>
          <p class="text-sm mt-2">Jamaah akan otomatis terdaftar saat melakukan booking</p>
        </div>

        <div class="space-y-2">
          <template x-for="(jamaah, index) in keberangkatan.jamaah_list" :key="jamaah.booking_id">
            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
              <div class="flex items-center gap-2">
                <span class="font-medium" x-text="(index + 1) + '. ' + jamaah.jamaah_name"></span>
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
              <div class="text-sm text-slate-600 mt-1">
                Booking ID: <span class="font-mono" x-text="jamaah.booking_id"></span>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Tab: Cost Calculation -->
      <div x-show="jamaahTab === 'cost'">
        <div x-show="loadingCost" class="text-center py-8">
          <i class='bx bx-loader-alt bx-spin text-2xl text-slate-400'></i>
        </div>

        <div x-show="!loadingCost && costData">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
              <div class="text-sm text-blue-600 mb-1">Total Biaya</div>
              <div class="text-2xl font-bold text-blue-700" x-text="formatCurrency(costData?.total_cost)"></div>
              <div class="text-xs text-blue-600 mt-1">
                <span x-text="formatCurrency(costData?.cost_per_jamaah)"></span> / jamaah
              </div>
            </div>
            <div class="p-4 rounded-xl bg-green-50 border border-green-200">
              <div class="text-sm text-green-600 mb-1">Total Pendapatan</div>
              <div class="text-2xl font-bold text-green-700" x-text="formatCurrency(costData?.total_revenue)"></div>
              <div class="text-xs text-green-600 mt-1">
                <span x-text="formatCurrency(costData?.revenue_per_jamaah)"></span> / jamaah
              </div>
            </div>
            <div class="p-4 rounded-xl border"
                 :class="(costData?.total_profit || 0) >= 0 ? 'bg-purple-50 border-purple-200' : 'bg-red-50 border-red-200'">
              <div class="text-sm mb-1" :class="(costData?.total_profit || 0) >= 0 ? 'text-purple-600' : 'text-red-600'">
                Total Profit
              </div>
              <div class="text-2xl font-bold" :class="(costData?.total_profit || 0) >= 0 ? 'text-purple-700' : 'text-red-700'"
                   x-text="formatCurrency(costData?.total_profit)"></div>
              <div class="text-xs mt-1" :class="(costData?.total_profit || 0) >= 0 ? 'text-purple-600' : 'text-red-600'">
                Margin: <span x-text="(costData?.profit_margin || 0).toFixed(2)"></span>%
              </div>
            </div>
          </div>

          <!-- Cost Breakdown -->
          <div class="rounded-xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
              <h4 class="font-semibold">Rincian Biaya</h4>
              <p class="text-sm text-slate-600">Berdasarkan <span x-text="costData?.jamaah_count"></span> jamaah</p>
            </div>
            <div class="p-4">
              <table class="w-full text-sm">
                <thead class="text-left text-slate-600 border-b border-slate-200">
                  <tr>
                    <th class="pb-2">Komponen</th>
                    <th class="pb-2 text-right">Harga Satuan</th>
                    <th class="pb-2 text-center">Jamaah</th>
                    <th class="pb-2 text-right">Total</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr>
                    <td class="py-2">Biaya Penerbangan</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.flight_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.flight_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Hotel</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.hotel_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.hotel_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Transportasi</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.transportation_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.transportation_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Makan</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.meal_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.meal_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Visa</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.visa_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.visa_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Pembimbing</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.guide_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.guide_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Asuransi</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.insurance_cost)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.insurance_cost)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Operasional</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.operational_overhead)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.operational_overhead)"></td>
                  </tr>
                  <tr>
                    <td class="py-2">Biaya Kontingensi</td>
                    <td class="py-2 text-right font-mono" x-text="formatCurrency(costData?.unit_prices?.contingency)"></td>
                    <td class="py-2 text-center" x-text="'× ' + costData?.jamaah_count"></td>
                    <td class="py-2 text-right font-semibold" x-text="formatCurrency(costData?.total_costs?.contingency)"></td>
                  </tr>
                  <tr class="font-bold border-t-2 border-slate-300">
                    <td class="py-3" colspan="3">TOTAL BIAYA</td>
                    <td class="py-3 text-right text-lg text-primary-700" x-text="formatCurrency(costData?.total_cost)"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-slate-200 flex justify-end">
      <button x-on:click="closeJamaahModal()" 
              class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50">
        Tutup
      </button>
    </div>
  </div>
</div>
