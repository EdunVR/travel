<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Produksi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Produksi')]); ?>
  <style>
    /* Force Indonesian date format display */
    input[type="date"] {
      position: relative;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
      background: transparent;
      bottom: 0;
      color: transparent;
      cursor: pointer;
      height: auto;
      left: 0;
      position: absolute;
      right: 0;
      top: 0;
      width: auto;
    }
    
    /* Custom date format hint - Fixed double overlay */
    .date-input-wrapper {
      position: relative;
    }
    
    .date-format-overlay {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      pointer-events: none;
      color: #64748b;
      font-size: 14px;
      z-index: 1;
      transition: opacity 0.2s ease;
    }
    
    /* Hide overlay when input has value or is focused */
    input[type="date"]:focus + .date-format-overlay,
    input[type="date"]:not(:placeholder-shown) + .date-format-overlay,
    input[type="date"][value]:not([value=""]) + .date-format-overlay {
      opacity: 0;
      visibility: hidden;
    }
    
    /* Ensure overlay only shows when input is truly empty */
    input[type="date"]:placeholder-shown + .date-format-overlay {
      opacity: 1;
      visibility: visible;
    }
    
    /* Improved grid layout */
    .production-card {
      transition: all 0.2s ease;
    }
    
    .production-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .production-code-badge {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    
    .date-highlight {
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      border: 1px solid #0ea5e9;
      border-radius: 8px;
      padding: 8px 12px;
      margin: 4px 0;
    }
    
    .date-label {
      font-size: 10px;
      color: #0369a1;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .date-value {
      font-size: 13px;
      color: #0c4a6e;
      font-weight: 500;
      margin-top: 2px;
    }
  </style>

  <div x-data="productionCrud()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Data Produksi</h1>
        <p class="text-slate-600 text-sm">Kelola rencana & realisasi produksi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Buat Produksi Baru
        </button>
        
        <button x-on:click="openMonthlyCosts()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
          <i class='bx bx-money text-lg'></i> Biaya Bulanan
        </button>
        
        <!-- Export PDF Dropdown -->
        <div class="relative" x-data="{ open: false }">
          <button @click="open = !open" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
            <i class='bx bx-export text-lg'></i> Export PDF
            <i class='bx bx-chevron-down text-sm'></i>
          </button>
          
          <!-- Dropdown Menu -->
          <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-1 w-64 bg-white rounded-lg shadow-lg border border-slate-200 z-50">
            <div class="p-2">
              <!-- Bulk Production Report -->
              <button @click="exportBulkProductionPdf(); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                <i class='bx bx-file-blank text-lg text-blue-600'></i>
                <div>
                  <div class="font-medium text-sm">Laporan Produksi</div>
                  <div class="text-xs text-slate-500">Semua data produksi dalam tabel</div>
                </div>
              </button>
              
              <!-- Bulk QC Egg Tofu Mentah Report -->
              <button @click="exportQcTofuMentahPdf(); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                <i class='bx bx-clipboard-check text-lg text-purple-600'></i>
                <div>
                  <div class="font-medium text-sm">QC Egg Tofu Mentah</div>
                  <div class="text-xs text-slate-500">Formulir QC resmi perusahaan</div>
                </div>
              </button>
            </div>
          </div>
        </div>
        
        <button x-on:click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="grid grid-cols-1 gap-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        <!-- Search -->
        <div class="lg:col-span-4">
          <div class="relative">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model="search" x-on:input.debounce.500ms="fetchData()" placeholder="Cari kode produksi, produk, lini…" 
                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
          </div>
        </div>
        <!-- Filter Outlet -->
        <div class="lg:col-span-3">
          <select x-model="outletFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <template x-for="outlet in outlets" :key="outlet.id">
              <option :value="outlet.id" x-text="'Outlet: ' + outlet.name"></option>
            </template>
          </select>
        </div>
        <!-- Filter Status -->
        <div class="lg:col-span-2">
          <select x-model="statusFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Status: Semua</option>
            <option value="draft">Draft</option>
            <option value="approved">Disetujui</option>
            <option value="in_progress">Berjalan</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Dibatalkan</option>
          </select>
        </div>
        <!-- Filter Lini -->
        <div class="lg:col-span-3">
          <select x-model="lineFilter" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="ALL">Lini: Semua</option>
            <option value="Lini A">Lini A</option>
            <option value="Lini B">Lini B</option>
            <option value="Lini C">Lini C</option>
            <option value="Lini D">Lini D</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-2">
        <!-- Sort -->
        <div class="grid grid-cols-2 gap-2 lg:col-span-4">
          <select x-model="sortKey" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="created_at">Tanggal Dibuat</option>
            <option value="production_code">Kode Produksi</option>
            <option value="start_date">Tanggal Mulai</option>
            <option value="target_quantity">Target</option>
          </select>
          <select x-model="sortDir" x-on:change="fetchData()" class="rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="desc">Terbaru</option><option value="asc">Terlama</option>
          </select>
        </div>

        <!-- Stats -->
        <div class="lg:col-span-6 grid grid-cols-3 gap-2">
          <div class="bg-primary-50 rounded-xl p-3 border border-primary-100 text-center">
            <div x-text="stats.active" class="text-lg font-bold text-primary-700">0</div>
            <div class="text-xs text-primary-600">Produksi Aktif</div>
          </div>
          <div class="bg-green-50 rounded-xl p-3 border border-green-100 text-center">
            <div x-text="stats.completed" class="text-lg font-bold text-green-700">0</div>
            <div class="text-xs text-green-600">Selesai</div>
          </div>
          <div class="bg-yellow-50 rounded-xl p-3 border border-yellow-100 text-center">
            <div x-text="stats.progress + '%'" class="text-lg font-bold text-yellow-700">0%</div>
            <div class="text-xs text-yellow-600">Rata-rata Progress</div>
          </div>
        </div>

        <!-- Toggle View -->
        <div class="lg:col-span-2">
          <div class="flex rounded-xl border border-slate-200 overflow-hidden">
            <button x-on:click="view='grid'"  :class="view==='grid'  ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Grid</button>
            <button x-on:click="view='table'" :class="view==='table' ? 'bg-primary-600 text-white' : 'bg-white text-slate-700'" class="flex-1 px-3 py-2 text-sm">Tabel</button>
          </div>
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

    <!-- GRID -->
    <div x-show="view==='grid' && !loading">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <template x-for="p in productions" :key="p.id">
          <div class="production-card rounded-2xl border border-slate-200 bg-white shadow-card hover:shadow-[0_14px_40px_rgba(15,23,42,.10)] transition p-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-primary-50 text-primary-700 border border-primary-100 shrink-0">
                <i class='bx bx-factory text-2xl'></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-2">
                  <div class="production-code-badge bg-primary-600 text-white px-2 py-1 rounded-md text-xs font-medium" x-text="p.production_code"></div>
                  <span class="text-[11px] px-2 py-0.5 rounded-full"
                        :class="getStatusClass(p.status)"
                        x-text="getStatusText(p.status)"></span>
                </div>
                
                <div class="text-sm font-semibold text-slate-800 mb-1" x-text="p.product_name"></div>
                <div class="text-xs text-slate-500 mb-3">
                  <span x-text="p.production_line"></span>
                  <template x-if="p.hpp_records && p.hpp_records.length > 1">
                    <span class="text-primary-600" x-text="' • Multi-produk (' + p.hpp_records.length + ')'"></span>
                  </template>
                </div>
                
                <!-- Date Information - Highlighted -->
                <div class="date-highlight mb-3">
                  <div class="date-label">Periode Produksi</div>
                  <div class="date-value">
                    <span x-text="formatDate(p.start_date) || 'Belum ditentukan'"></span>
                    <template x-if="formatDate(p.end_date)">
                      <span> s/d <span x-text="formatDate(p.end_date)"></span></span>
                    </template>
                  </div>
                  <template x-if="formatDate(p.expiry_date)">
                    <div class="text-xs text-red-600 mt-1 font-medium">
                      Kadaluarsa: <span x-text="formatDate(p.expiry_date)"></span>
                    </div>
                  </template>
                </div>
                
                <div class="mt-2">
                  <!-- Progress Bar -->
                  <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-slate-600">Progress</span>
                    <span class="font-medium" x-text="p.progress + '%'"></span>
                  </div>
                  <div class="w-full bg-slate-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full transition-all" :style="'width: ' + Math.min(p.progress, 100) + '%'"></div>
                  </div>
                </div>
                
                <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                  <div>
                    <span class="text-slate-500">Target:</span>
                    <div class="font-medium" x-text="formatNumber(p.target_quantity)"></div>
                  </div>
                  <div>
                    <span class="text-slate-500">Realisasi:</span>
                    <div class="font-medium" x-text="formatNumber(p.realized_quantity)"></div>
                  </div>
                  <div>
                    <span class="text-slate-500">Reject:</span>
                    <div class="font-medium text-red-600" x-text="formatNumber(p.rejected_quantity || 0)"></div>
                  </div>
                  <div>
                    <span class="text-slate-500">Total Produksi:</span>
                    <div class="font-medium" x-text="formatNumber((p.realized_quantity || 0) + (p.rejected_quantity || 0))"></div>
                  </div>
                  <div>
                    <span class="text-slate-500">HPP/Unit:</span>
                    <div class="font-medium text-xs" x-text="p.hpp_per_unit_formatted"></div>
                  </div>
                  <div>
                    <span class="text-slate-500">Total Biaya:</span>
                    <div class="font-medium text-xs" x-text="p.total_cost_formatted"></div>
                  </div>
                </div>
                
                <!-- Multi-Product Details -->
                <template x-if="p.hpp_records && p.hpp_records.length > 1">
                  <div class="mt-3 pt-2 border-t border-slate-100">
                    <div class="text-xs font-medium text-slate-600 mb-2">Detail Produk:</div>
                    <div class="space-y-1">
                      <template x-for="hpp in p.hpp_records" :key="hpp.id">
                        <div class="flex justify-between items-center text-xs bg-slate-50 rounded px-2 py-1">
                          <div class="flex-1 min-w-0">
                            <div class="font-medium truncate" x-text="hpp.product_name"></div>
                          </div>
                          <div class="text-right ml-2">
                            <span class="text-slate-500" x-text="hpp.realized_quantity + '/' + hpp.target_quantity"></span>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </template>
              </div>
            </div>
            
            <!-- Action Buttons - Icon Only -->
            <div class="mt-3 flex gap-1 justify-center">
              <!-- View button - always available -->
              <button x-on:click="viewProduction(p)" class="w-8 h-8 rounded-lg bg-primary-600 text-white hover:bg-primary-700 flex items-center justify-center" title="Lihat Detail">
                <i class='bx bx-show text-sm'></i>
              </button>
              
              <!-- Download Document dropdown - always available -->
              <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-8 h-8 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50 flex items-center justify-center" title="Download Dokumen">
                  <i class='bx bx-download text-sm'></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-1 w-64 bg-white rounded-lg shadow-lg border border-slate-200 z-50">
                  <div class="p-2">
                    <!-- Regular Production PDF -->
                    <button @click="downloadProductionPdf(p.id); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                      <i class='bx bx-file-blank text-lg text-blue-600'></i>
                      <div>
                        <div class="font-medium text-sm">Laporan Produksi</div>
                        <div class="text-xs text-slate-500">Detail lengkap produksi dan HPP</div>
                      </div>
                    </button>
                    
                    <!-- QC Egg Tofu Mentah - only for tofu productions -->
                    <template x-if="p.business_type === 'tofu'">
                      <button @click="downloadQcTofuPdf(p.id); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                        <i class='bx bx-clipboard-check text-lg text-purple-600'></i>
                        <div>
                          <div class="font-medium text-sm">QC Egg Tofu Mentah</div>
                          <div class="text-xs text-slate-500" x-text="getQcDataSummary(p)"></div>
                        </div>
                      </button>
                    </template>
                  </div>
                </div>
              </div>
              
              <!-- Edit button for draft status -->
              <template x-if="p.status === 'draft'">
                <button x-on:click="editProduction(p)" class="w-8 h-8 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 flex items-center justify-center" title="Edit">
                  <i class='bx bx-edit-alt text-sm'></i>
                </button>
              </template>
              
              <!-- Approve button for draft status -->
              <template x-if="p.status === 'draft'">
                <button x-on:click="approveProduction(p)" class="w-8 h-8 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 flex items-center justify-center" title="Approve">
                  <i class='bx bx-check text-sm'></i>
                </button>
              </template>
              
              <!-- Start button for approved status -->
              <template x-if="p.status === 'approved'">
                <button x-on:click="startProduction(p)" class="w-8 h-8 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50 flex items-center justify-center" title="Mulai Produksi">
                  <i class='bx bx-play text-sm'></i>
                </button>
              </template>
              
              <!-- Realization button for in_progress status -->
              <template x-if="p.status === 'in_progress'">
                <button x-on:click="showRealizationModal(p)" class="w-8 h-8 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50 flex items-center justify-center" title="Tambah Realisasi">
                  <i class='bx bx-plus text-sm'></i>
                </button>
              </template>
              
              <!-- Complete button for in_progress status -->
              <template x-if="p.status === 'in_progress'">
                <button x-on:click="completeProduction(p)" 
                        :class="p.progress >= 100 ? 'w-8 h-8 rounded-lg bg-green-600 text-white hover:bg-green-700 flex items-center justify-center' : 'w-8 h-8 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 flex items-center justify-center'" 
                        :title="p.progress >= 100 ? 'Selesaikan (Target Tercapai)' : 'Selesaikan Produksi'">
                  <i class='bx bx-check-circle text-sm'></i>
                </button>
              </template>
              
              <!-- Delete button for draft status -->
              <template x-if="p.status === 'draft'">
                <button x-on:click="deleteProduction(p)" class="w-8 h-8 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 flex items-center justify-center" title="Hapus">
                  <i class='bx bx-trash text-sm'></i>
                </button>
              </template>
            </div>
          </div>
        </template>
      </div>
      <div x-show="productions.length===0" class="text-center text-slate-500 py-8">Belum ada data produksi.</div>
    </div>

    <!-- TABLE -->
    <div x-show="view==='table' && !loading">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm min-w-[1300px]">
          <thead class="bg-slate-50 text-slate-700">
            <tr>
              <th class="text-left px-4 py-3 w-12">No</th>
              <th class="text-left px-4 py-3">Kode Produksi</th>
              <th class="text-left px-4 py-3">Produk</th>
              <th class="text-left px-4 py-3">Detail Produk</th>
              <th class="text-left px-4 py-3">Lini</th>
              <th class="text-right px-4 py-3">Target</th>
              <th class="text-right px-4 py-3">Realisasi</th>
              <th class="text-right px-4 py-3">Reject</th>
              <th class="text-center px-4 py-3">Progress</th>
              <th class="text-center px-4 py-3">Status</th>
              <th class="text-right px-4 py-3">HPP/Unit</th>
              <th class="text-right px-4 py-3">Total Cost</th>
              <th class="text-center px-4 py-3">Tanggal Produksi</th>
              <th class="text-center px-4 py-3">Tanggal Kadaluarsa</th>
              <th class="text-center px-4 py-3">Prioritas</th>
              <th class="text-center px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(p,i) in productions" :key="p.id">
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3" x-text="i+1"></td>
                <td class="px-4 py-3">
                  <div class="production-code-badge bg-primary-600 text-white px-2 py-1 rounded text-xs font-medium inline-block" x-text="p.production_code"></div>
                </td>
                <td class="px-4 py-3" x-text="p.product_name"></td>
                <td class="px-4 py-3">
                  <template x-if="p.hpp_records && p.hpp_records.length > 1">
                    <div class="space-y-1">
                      <template x-for="hpp in p.hpp_records" :key="hpp.id">
                        <div class="text-xs bg-slate-50 rounded px-2 py-1">
                          <div class="font-medium" x-text="hpp.product_name"></div>
                          <div class="text-slate-500" x-text="'Realisasi: ' + hpp.realized_quantity + '/' + hpp.target_quantity"></div>
                        </div>
                      </template>
                    </div>
                  </template>
                  <template x-if="!p.hpp_records || p.hpp_records.length <= 1">
                    <div class="text-xs text-slate-500">Single Product</div>
                  </template>
                </td>
                <td class="px-4 py-3" x-text="p.production_line"></td>
                <td class="px-4 py-3 text-right" x-text="formatNumber(p.target_quantity)"></td>
                <td class="px-4 py-3 text-right" x-text="formatNumber(p.realized_quantity)"></td>
                <td class="px-4 py-3 text-right">
                  <span class="text-red-600 font-medium" x-text="formatNumber(p.rejected_quantity || 0)"></span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <div class="w-16 bg-slate-200 rounded-full h-2">
                      <div class="bg-primary-600 h-2 rounded-full" :style="'width: ' + Math.min(p.progress, 100) + '%'"></div>
                    </div>
                    <span class="text-xs font-medium" x-text="p.progress + '%'"></span>
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(p.status)" x-text="getStatusText(p.status)"></span>
                </td>
                <td class="px-4 py-3 text-right" x-text="p.hpp_per_unit_formatted"></td>
                <td class="px-4 py-3 text-right" x-text="p.total_cost_formatted"></td>
                <td class="px-4 py-3 text-center">
                  <div class="date-highlight inline-block">
                    <div class="date-label">Mulai</div>
                    <div class="date-value text-sm" x-text="formatDate(p.start_date) || '-'"></div>
                    <template x-if="formatDate(p.end_date)">
                      <div class="date-label mt-1">Selesai</div>
                    </template>
                    <template x-if="formatDate(p.end_date)">
                      <div class="date-value text-sm" x-text="formatDate(p.end_date)"></div>
                    </template>
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="text-sm" x-text="formatDate(p.expiry_date) || '-'"></div>
                  <template x-if="formatDate(p.expiry_date)">
                    <div class="text-xs text-red-500">Kadaluarsa</div>
                  </template>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="getPriorityClass(p.priority)" x-text="getPriorityText(p.priority)"></span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-1 justify-center">
                    <!-- View button - always available -->
                    <button x-on:click="viewProduction(p)" class="w-7 h-7 rounded-lg bg-primary-600 text-white hover:bg-primary-700 flex items-center justify-center" title="Lihat Detail">
                      <i class='bx bx-show text-sm'></i>
                    </button>
                    
                    <!-- Download Document dropdown - always available -->
                    <div class="relative" x-data="{ open: false }">
                      <button @click="open = !open" class="w-7 h-7 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50 flex items-center justify-center" title="Download Dokumen">
                        <i class='bx bx-download text-sm'></i>
                      </button>
                      
                      <!-- Dropdown Menu -->
                      <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-1 w-64 bg-white rounded-lg shadow-lg border border-slate-200 z-50">
                        <div class="p-2">
                          <!-- Regular Production PDF -->
                          <button @click="downloadProductionPdf(p.id); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                            <i class='bx bx-file-blank text-lg text-blue-600'></i>
                            <div>
                              <div class="font-medium text-sm">Laporan Produksi</div>
                              <div class="text-xs text-slate-500">Detail lengkap produksi dan HPP</div>
                            </div>
                          </button>
                          
                          <!-- QC Egg Tofu Mentah - only for tofu productions -->
                          <template x-if="p.business_type === 'tofu'">
                            <button @click="downloadQcTofuPdf(p.id); open = false" class="w-full flex items-center gap-3 p-2 text-left hover:bg-slate-50 rounded-lg">
                              <i class='bx bx-clipboard-check text-lg text-purple-600'></i>
                              <div>
                                <div class="font-medium text-sm">QC Egg Tofu Mentah</div>
                                <div class="text-xs text-slate-500" x-text="getQcDataSummary(p)"></div>
                              </div>
                            </button>
                          </template>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Edit button for draft -->
                    <template x-if="p.status === 'draft'">
                      <button x-on:click="editProduction(p)" class="w-7 h-7 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 flex items-center justify-center" title="Edit">
                        <i class='bx bx-edit-alt text-sm'></i>
                      </button>
                    </template>
                    
                    <!-- Approve button for draft -->
                    <template x-if="p.status === 'draft'">
                      <button x-on:click="approveProduction(p)" class="w-7 h-7 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 flex items-center justify-center" title="Approve">
                        <i class='bx bx-check text-sm'></i>
                      </button>
                    </template>
                    
                    <!-- Start button for approved -->
                    <template x-if="p.status === 'approved'">
                      <button x-on:click="startProduction(p)" class="w-7 h-7 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50 flex items-center justify-center" title="Mulai Produksi">
                        <i class='bx bx-play text-sm'></i>
                      </button>
                    </template>
                    
                    <!-- Realization button for in_progress -->
                    <template x-if="p.status === 'in_progress'">
                      <button x-on:click="showRealizationModal(p)" class="w-7 h-7 rounded-lg border border-yellow-200 text-yellow-700 hover:bg-yellow-50 flex items-center justify-center" title="Tambah Realisasi">
                        <i class='bx bx-plus text-sm'></i>
                      </button>
                    </template>
                    
                    <!-- Complete button for in_progress status -->
                    <template x-if="p.status === 'in_progress'">
                      <button x-on:click="completeProduction(p)" 
                              :class="p.progress >= 100 ? 'w-7 h-7 rounded-lg bg-green-600 text-white hover:bg-green-700 flex items-center justify-center' : 'w-7 h-7 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 flex items-center justify-center'" 
                              :title="p.progress >= 100 ? 'Selesaikan (Target Tercapai)' : 'Selesaikan Produksi'">
                        <i class='bx bx-check-circle text-sm'></i>
                      </button>
                    </template>
                    
                    <!-- Delete button for draft -->
                    <template x-if="p.status === 'draft'">
                      <button x-on:click="deleteProduction(p)" class="w-7 h-7 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 flex items-center justify-center" title="Hapus">
                        <i class='bx bx-trash text-sm'></i>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="productions.length===0"><td colspan="16" class="px-4 py-8 text-center text-slate-500">Belum ada data produksi.</td></tr>
          </tbody>
        </table>
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

    <!-- Completion Confirmation Modal -->
    <div id="completionModal" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-4 hidden overflow-y-auto pt-20" style="display: none;">
      <div class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
          <div class="font-semibold text-lg">Konfirmasi Selesaikan Produksi</div>
          <button x-on:click="closeCompletionModal()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg transition-colors">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
          <!-- Production Info -->
          <div class="bg-slate-50 rounded-xl p-4 mb-4">
            <h3 class="font-medium text-slate-700 mb-3">Informasi Produksi</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-slate-500">Kode Produksi:</span>
                <div id="completionProductionCode" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Lini Produksi:</span>
                <div id="completionProductionLine" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Target Quantity:</span>
                <div id="completionTargetQuantity" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Realisasi:</span>
                <div id="completionRealizedQuantity" class="font-medium">-</div>
              </div>
            </div>
          </div>

          <!-- Confirmation Message -->
          <div class="mb-4">
            <p class="text-slate-700 mb-3">Apakah Anda yakin ingin menyelesaikan produksi ini?</p>
          </div>

          <!-- Checkbox Option -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
            <label class="flex items-start gap-3 cursor-pointer">
              <input type="checkbox" id="consumeRemainingMaterials" class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-200">
              <div>
                <div class="font-medium text-yellow-800 mb-1">
                  Sisa bahan dan biaya produksi lainnya pada Data Produksi ini dianggap habis
                </div>
                <div class="text-sm text-yellow-700">
                  Jika dicentang, maka biaya dan qty bahan yang digunakan akan sama dengan yang dicantumkan di data produksi. 
                  Artinya jika ada sisa bahan, maka stok akan dikurangi sesuai sisa tersebut.
                </div>
              </div>
            </label>
          </div>

          <!-- Warning -->
          <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <i class='bx bx-info-circle text-blue-600 text-lg mt-0.5'></i>
            <div class="text-sm text-blue-800">
              <div class="font-medium mb-1">Perhatian:</div>
              <ul class="space-y-1 text-xs">
                <li>• Status produksi akan berubah menjadi "Selesai"</li>
                <li>• Produksi yang sudah selesai tidak dapat diubah lagi</li>
                <li>• Jika checkbox dicentang, stok bahan akan disesuaikan dengan rencana produksi</li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-200 flex-shrink-0">
          <div class="flex gap-3">
            <button type="button" x-on:click="closeCompletionModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
              Batal
            </button>
            <button type="button" x-on:click="confirmCompleteProduction()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
              Ya, Selesaikan Produksi
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Production Modal -->
  <div id="createModal" class="fixed inset-0 z-50 items-start justify-center bg-black/40 p-2 hidden overflow-y-auto" style="display: none;">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-float my-2 flex flex-col">
      <!-- Modal Header - Fixed -->
      <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-white rounded-t-2xl">
        <div class="font-semibold text-lg">Buat Produksi Baru</div>
        <button onclick="closeCreateModal()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
      
      <!-- Modal Content - Scrollable -->
      <div class="flex-1 overflow-y-auto p-4 sm:p-5">
        <form id="productionForm" class="space-y-4">
          <?php echo csrf_field(); ?>
          
          <!-- Outlet Selection -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
              <select id="outletSelect" name="outlet_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                <option value="">Pilih Outlet</option>
                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e($selectedOutlet == $outlet->id_outlet ? 'selected' : ''); ?>>
                    <?php echo e($outlet->nama_outlet); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Kode Produksi</label>
              <input type="text" name="production_code" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Otomatis" readonly>
            </div>
          </div>

          <!-- Multi-Product Selection -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-medium text-slate-700">Produk yang Diproduksi</label>
              <button type="button" onclick="addProductRow()" class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700">
                <i class='bx bx-plus'></i> Tambah Produk
              </button>
            </div>
            <div id="productRows" class="space-y-3">
              <!-- Product rows will be added here -->
            </div>
          </div>

          <!-- Production Details -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Lini Produksi</label>
              <select name="production_line" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                <option value="">Pilih Lini</option>
                <option value="Lini A">Lini A</option>
                <option value="Lini B">Lini B</option>
                <option value="Lini C">Lini C</option>
                <option value="Lini D">Lini D</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Total Target Quantity</label>
              <input type="number" id="totalTargetQuantity" name="target_quantity" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-100 focus:ring-2 focus:ring-primary-200" readonly>
              <div class="text-xs text-slate-500 mt-1">Otomatis terhitung dari semua produk</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Mulai</label>
              <div class="date-input-wrapper">
                <input type="date" name="start_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                <div class="date-format-overlay">DD/MM/YYYY</div>
              </div>
              <div class="text-xs text-slate-500 mt-1">Format: DD/MM/YYYY</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Selesai</label>
              <div class="date-input-wrapper">
                <input type="date" name="end_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                <div class="date-format-overlay">DD/MM/YYYY</div>
              </div>
              <div class="text-xs text-slate-500 mt-1">Format: DD/MM/YYYY</div>
            </div>
          </div>

          <!-- Additional Production Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Kadaluarsa</label>
              <div class="date-input-wrapper">
                <input type="date" name="expiry_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                <div class="date-format-overlay">DD/MM/YYYY</div>
              </div>
              <div class="text-xs text-slate-500 mt-1">Format: DD/MM/YYYY (Opsional)</div>
            </div>
          </div>

          <!-- Priority -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas</label>
            <select name="priority" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
              <option value="normal">Normal</option>
              <option value="high">Tinggi</option>
              <option value="urgent">Mendesak</option>
            </select>
          </div>

          <!-- Advanced Production Group -->
          <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-4 border border-purple-200">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-medium text-purple-700">Grup Produksi Tingkat Lanjut (Opsional)</h3>
              <i class='bx bx-info-circle text-purple-600' title="Data tambahan untuk laporan PDF"></i>
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Usaha</label>
              <select id="businessTypeSelect" name="business_type" onchange="toggleBusinessSpecificForms()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-purple-200">
                <option value="">Tidak Ada (Default)</option>
                <option value="tofu">Tofu</option>
              </select>
            </div>

            <!-- Tofu Specific Forms -->
            <div id="tofuSpecificForms" class="hidden">
              <div class="bg-white rounded-lg p-4 border border-purple-200">
                <h4 class="font-medium text-purple-700 mb-4">QC Egg Tofu Mentah</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Perendaman Kacang Kedelai -->
                  <div class="col-span-2">
                    <h5 class="text-sm font-medium text-slate-700 mb-2">1. Perendaman Kacang Kedelai</h5>
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Waktu (jam)</label>
                        <input type="number" name="tofu_data[perendaman_waktu]" step="0.1" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Quantity (kg)</label>
                        <input type="number" name="tofu_data[perendaman_qty]" step="0.1" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                    </div>
                  </div>

                  <!-- Jumlah Rijek Telur -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">2. Jumlah Rijek Telur</label>
                    <input type="number" name="tofu_data[rijek_telur]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                  </div>

                  <!-- Pasteurisasi -->
                  <div>
                    <h5 class="text-sm font-medium text-slate-700 mb-2">3. Pasteurisasi</h5>
                    <div class="grid grid-cols-2 gap-2">
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Waktu (menit)</label>
                        <input type="number" name="tofu_data[pasteurisasi_waktu]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Suhu (°C)</label>
                        <input type="number" name="tofu_data[pasteurisasi_suhu]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                    </div>
                  </div>

                  <!-- Berat Akhir Sari Kedelai -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">4. Berat Akhir Sari Kedelai (kg)</label>
                    <input type="number" name="tofu_data[berat_sari_kedelai]" step="0.1" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                  </div>

                  <!-- Waktu Pencampuran -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">5. Waktu Pencampuran (menit)</label>
                    <input type="number" name="tofu_data[waktu_pencampuran]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                  </div>

                  <!-- Filling dan Pengemasan -->
                  <div class="col-span-2">
                    <h5 class="text-sm font-medium text-slate-700 mb-2">6. Filling dan Pengemasan</h5>
                    <div class="grid grid-cols-4 gap-2">
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Waktu (jam)</label>
                        <input type="number" name="tofu_data[filling_waktu]" step="0.1" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Mesin 1</label>
                        <input type="number" name="tofu_data[filling_mesin1]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Mesin 2</label>
                        <input type="number" name="tofu_data[filling_mesin2]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                      </div>
                      <div>
                        <label class="block text-xs text-slate-600 mb-1">Total</label>
                        <input type="number" id="fillingTotal" name="tofu_data[filling_total]" readonly class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm bg-slate-100">
                      </div>
                    </div>
                  </div>

                  <!-- Jumlah Rijek Mentah -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">7. Jumlah Rijek Mentah</label>
                    <input type="number" name="tofu_data[rijek_mentah]" min="0" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-200">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Material Requirements -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-medium text-slate-700">Kebutuhan Bahan</label>
              <button type="button" onclick="addMaterial()" class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700">
                <i class='bx bx-plus'></i> Tambah Bahan
              </button>
            </div>
            <div id="materialRequirements" class="space-y-2">
              <!-- Material rows will be added here -->
            </div>
          </div>

          <!-- Labor Costs -->
          <div class="bg-slate-50 rounded-xl p-4">
            <h3 class="font-medium text-slate-700 mb-3">Biaya Tenaga Kerja</h3>
            
            <div class="mb-3">
              <label class="flex items-center gap-2">
                <input type="checkbox" id="fromAttendance" onchange="toggleAttendanceDate(this)" class="rounded border-slate-300">
                <span class="text-sm">Ambil dari data absensi</span>
              </label>
            </div>

            <div id="attendanceDateSection" class="hidden mb-3">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Absensi</label>
                  <input type="date" id="attendanceDate" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                </div>
                <div class="flex items-end">
                  <button type="button" onclick="getAttendanceCount()" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700">
                    Ambil Data
                  </button>
                </div>
              </div>
              <div id="attendanceResult" class="mt-2 text-sm text-slate-600"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Pekerja</label>
                <input type="number" name="labor_costs[worker_count]" onchange="calculateLaborCost()" oninput="calculateLaborCost()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="1">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Biaya per Pekerja</label>
                <input type="number" name="labor_costs[cost_per_worker]" onchange="calculateLaborCost(); updateLaborCostDisplay(this)" oninput="calculateLaborCost(); updateLaborCostDisplay(this)" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01">
                <div class="text-xs text-slate-500 mt-1" id="costPerWorkerDisplay">Format: Rp 0</div>
              </div>
            </div>
            
            <div class="mt-3">
              <label class="block text-sm font-medium text-slate-700 mb-1">Total Biaya Tenaga Kerja</label>
              <input type="text" id="totalLaborCost" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-100" readonly>
              <input type="hidden" name="labor_costs[total_cost]" id="laborCostHidden">
            </div>
          </div>

          <!-- Operational Costs -->
          <div class="bg-slate-50 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-medium text-slate-700">Biaya Operasional</h3>
              <div class="flex gap-2">
                <button type="button" onclick="loadMonthlyOperationalCosts()" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700">
                  <i class='bx bx-refresh'></i> Auto dari Biaya Bulanan
                </button>
                <button type="button" onclick="addOperationalCost()" class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700">
                  <i class='bx bx-plus'></i> Tambah Manual
                </button>
              </div>
            </div>

            <!-- Auto Calculation Section -->
            <div id="autoOperationalSection" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-blue-700">Perhitungan Otomatis dari Biaya Bulanan</h4>
                <button type="button" onclick="clearAutoOperational()" class="text-xs text-red-600 hover:text-red-700">
                  <i class='bx bx-x'></i> Hapus
                </button>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-medium text-slate-700 mb-1">Jumlah Hari Kerja</label>
                  <input type="number" id="workingDays" onchange="calculateDailyOperationalCosts()" 
                         class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-200" 
                         min="1" max="31" placeholder="Masukkan jumlah hari kerja">
                  <div class="text-xs text-slate-500 mt-1">Untuk membagi biaya bulanan</div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-slate-700 mb-1">% Gaji Office untuk Produksi</label>
                  <input type="number" id="officePercentage" onchange="calculateDailyOperationalCosts()" 
                         class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-200" 
                         min="1" max="100" value="30" placeholder="30">
                  <div class="text-xs text-slate-500 mt-1">Default 30% dari gaji office</div>
                </div>
              </div>
              
              <div class="mb-3">
                <div>
                  <label class="block text-xs font-medium text-slate-700 mb-1">Total Biaya Operasional</label>
                  <input type="text" id="totalAutoOperational" readonly
                         class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm bg-slate-100">
                  <div class="text-xs text-slate-500 mt-1">Otomatis terhitung</div>
                </div>
              </div>

              <!-- Monthly Cost Breakdown -->
              <div id="monthlyCostBreakdown" class="hidden">
                <div class="text-xs font-medium text-slate-700 mb-2">Rincian Biaya Bulanan:</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                  <div class="bg-white rounded p-2">
                    <div class="text-slate-500">Listrik</div>
                    <div id="monthlyElectricity" class="font-medium">Rp 0</div>
                    <div id="dailyElectricity" class="text-slate-400">Per hari: Rp 0</div>
                  </div>
                  <div class="bg-white rounded p-2">
                    <div class="text-slate-500">Air</div>
                    <div id="monthlyWater" class="font-medium">Rp 0</div>
                    <div id="dailyWater" class="text-slate-400">Per hari: Rp 0</div>
                  </div>
                  <div class="bg-white rounded p-2">
                    <div class="text-slate-500">Bahan Bakar</div>
                    <div id="monthlyFuel" class="font-medium">Rp 0</div>
                    <div id="dailyFuel" class="text-slate-400">Per hari: Rp 0</div>
                  </div>
                  <div class="bg-white rounded p-2">
                    <div class="text-slate-500">Gaji Office</div>
                    <div id="monthlyOffice" class="font-medium">Rp 0</div>
                    <div id="dailyOffice" class="text-slate-400">Per hari: Rp 0</div>
                    <div id="usedOffice" class="text-blue-600 text-xs">Dipakai: <span id="officeUsedAmount">30%</span></div>
                  </div>
                </div>
              </div>
            </div>

            <div id="operationalCosts" class="space-y-2">
              <!-- Operational cost rows will be added here -->
            </div>
          </div>

          <!-- HPP Preview -->
          <div class="bg-primary-50 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-medium text-primary-700">Preview HPP (Realtime)</h3>
              <div class="text-xs text-slate-500">Otomatis terhitung saat input berubah</div>
            </div>
            <div id="hppPreview" class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm mb-4">
              <div class="bg-white rounded-lg p-3 text-center">
                <div class="text-xs text-slate-500 mb-1">Biaya Material</div>
                <div id="previewMaterialCost" class="font-semibold text-slate-700">Rp 0</div>
              </div>
              <div class="bg-white rounded-lg p-3 text-center">
                <div class="text-xs text-slate-500 mb-1">Biaya Tenaga Kerja</div>
                <div id="previewLaborCost" class="font-semibold text-slate-700">Rp 0</div>
              </div>
              <div class="bg-white rounded-lg p-3 text-center">
                <div class="text-xs text-slate-500 mb-1">Biaya Operasional</div>
                <div id="previewOperationalCost" class="font-semibold text-slate-700">Rp 0</div>
              </div>
              <div class="bg-white rounded-lg p-3 text-center">
                <div class="text-xs text-slate-500 mb-1">Total Biaya</div>
                <div id="previewTotalCost" class="font-semibold text-primary-700">Rp 0</div>
              </div>
              <div class="bg-white rounded-lg p-3 text-center">
                <div class="text-xs text-slate-500 mb-1">HPP per Unit</div>
                <div id="previewHppPerUnit" class="font-semibold text-primary-700">Rp 0</div>
              </div>
            </div>
            
            <!-- Material Details Breakdown -->
            <div id="materialBreakdown" class="hidden">
              <div class="bg-white rounded-lg p-3 border border-slate-200">
                <h4 class="text-sm font-medium text-slate-700 mb-2">Detail Biaya Material (FIFO)</h4>
                <div id="materialBreakdownContent" class="space-y-2 text-xs">
                  <!-- Material breakdown will be populated here -->
                </div>
              </div>
            </div>
          </div>

          </div>
        </form>
        
        <!-- Submit Buttons - At bottom of content -->
        <div class="flex gap-3 pt-6 pb-4">
          <button type="button" onclick="closeCreateModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
            Batal
          </button>
          <button type="submit" form="productionForm" id="submitProductionBtn" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors">
            <span id="submitBtnText">Simpan Produksi</span>
            <i id="submitBtnLoader" class="bx bx-loader-alt bx-spin ml-2 hidden"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Realization Modal -->
  <div id="realizationModal" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-4 hidden overflow-y-auto" style="display: none;">
    <div class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="font-semibold text-lg">Tambah Realisasi Produksi</div>
        <button onclick="closeRealizationModal()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
      
      <!-- Modal Content - Scrollable -->
      <div class="p-6 overflow-y-auto flex-1">
        <form id="realizationForm" class="space-y-4">
          <?php echo csrf_field(); ?>
          
          <!-- Production Info -->
          <div class="bg-slate-50 rounded-xl p-4">
            <h3 class="font-medium text-slate-700 mb-3">Informasi Produksi</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-slate-500">Kode Produksi:</span>
                <div id="realizationProductionCode" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Lini Produksi:</span>
                <div id="realizationProductionLine" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Total Target:</span>
                <div id="realizationTotalTarget" class="font-medium">-</div>
              </div>
              <div>
                <span class="text-slate-500">Total Sudah Diproduksi:</span>
                <div id="realizationTotalCurrent" class="font-medium">-</div>
              </div>
            </div>
          </div>

          <!-- Multi-Product Realization -->
          <div>
            <h3 class="font-medium text-slate-700 mb-3">Realisasi per Produk</h3>
            <div id="productRealizationRows" class="space-y-4">
              <!-- Product realization rows will be populated here -->
            </div>
          </div>

          <!-- Global Notes -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Umum (Opsional)</label>
            <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Catatan realisasi produksi..."></textarea>
          </div>

          <!-- Warning -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
              <i class='bx bx-info-circle text-yellow-600 text-lg mt-0.5'></i>
              <div class="text-sm text-yellow-800">
                <div class="font-medium mb-1">Perhatian:</div>
                <ul class="space-y-1 text-xs">
                  <li>• Stok bahan akan berkurang sesuai dengan jumlah yang diproduksi</li>
                  <li>• Stok setiap produk akan bertambah sesuai jumlah yang berhasil diproduksi</li>
                  <li>• HPP akan dihitung berdasarkan FIFO method</li>
                  <li>• Pastikan stok bahan mencukupi sebelum menambah realisasi</li>
                  <li>• Realisasi dapat dilakukan per produk secara terpisah</li>
                </ul>
              </div>
            </div>
          </div>
        </form>
      </div>
      
      <!-- Modal Footer - Fixed at bottom -->
      <div class="px-6 py-4 border-t border-slate-200 flex-shrink-0">
        <div class="flex gap-3">
          <button type="button" onclick="closeRealizationModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
            Batal
          </button>
          <button type="submit" form="realizationForm" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors">
            Tambah Realisasi
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Monthly Costs Modal -->
  <div id="monthlyCostsModal" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-4 hidden overflow-y-auto" style="display: none;">
    <div class="w-full max-w-6xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="font-semibold text-lg">Biaya Produksi Bulanan</div>
        <button onclick="closeMonthlyCostsModal()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg transition-colors">
          <i class='bx bx-x text-xl'></i>
        </button>
      </div>
      
      <!-- Modal Content - Scrollable -->
      <div class="p-6 overflow-y-auto flex-1">
        <!-- Outlet Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Outlet</label>
          <select id="monthlyCostsOutletSelect" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">Pilih Outlet...</option>
            <?php if(isset($outlets)): ?>
              <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Input Form Biaya Bulanan -->
        <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium text-slate-700">Input Biaya Bulanan</h3>
            <button id="toggleInputForm" type="button" class="text-sm text-primary-600 hover:text-primary-700">
              <span id="toggleText">Tampilkan Form</span>
              <i id="toggleIcon" class='bx bx-chevron-down ml-1'></i>
            </button>
          </div>
          
          <div id="inputFormContent" class="hidden">
            <form id="monthlyCostsForm" class="space-y-4">
              <?php echo csrf_field(); ?>
              <input type="hidden" id="formOutletId" name="outlet_id">
              
              <!-- Period Selection -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Bulan</label>
                  <select name="month" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                    <option value="">Pilih Bulan...</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Tahun</label>
                  <select name="year" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" required>
                    <option value="">Pilih Tahun...</option>
                    <?php for($year = date('Y'); $year >= 2020; $year--): ?>
                      <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>

              <!-- Cost Inputs -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Listrik</label>
                  <input type="number" name="electricity_cost" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Air</label>
                  <input type="number" name="water_cost" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Bahan Bakar</label>
                  <input type="number" name="fuel_cost" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Gaji Office</label>
                  <input type="number" name="office_salary_cost" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01" required>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Biaya Lain-lain (Opsional)</label>
                  <input type="number" name="other_costs" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" min="0" step="0.01">
                </div>
              </div>

              <!-- Notes -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Catatan tambahan..."></textarea>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors">
                  Simpan Biaya Bulanan
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Current Month Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="text-blue-600 text-sm font-medium">Total Biaya Bulan Ini</div>
            <div id="currentMonthCost" class="text-2xl font-bold text-blue-700">Rp 0</div>
          </div>
          <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="text-green-600 text-sm font-medium">Rata-rata per Hari</div>
            <div id="averageDailyCost" class="text-2xl font-bold text-green-700">Rp 0</div>
          </div>
          <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <div class="text-purple-600 text-sm font-medium">Proyeksi Bulan Ini</div>
            <div id="projectedMonthlyCost" class="text-2xl font-bold text-purple-700">Rp 0</div>
          </div>
        </div>

        <!-- Historical Data -->
        <div class="bg-slate-50 rounded-xl p-4">
          <h3 class="font-medium text-slate-700 mb-4">Riwayat Biaya Operasional 12 Bulan Terakhir</h3>
          <div id="monthlyHistoryTable" class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-slate-200">
                  <th class="text-left py-2 px-3">Periode</th>
                  <th class="text-right py-2 px-3">Total</th>
                  <th class="text-right py-2 px-3">Listrik</th>
                  <th class="text-right py-2 px-3">Air</th>
                  <th class="text-right py-2 px-3">Bahan Bakar</th>
                  <th class="text-right py-2 px-3">Gaji Office</th>
                  <th class="text-right py-2 px-3">Lainnya</th>
                  <th class="text-left py-2 px-3">Catatan</th>
                </tr>
              </thead>
              <tbody id="monthlyHistoryBody">
                <tr>
                  <td colspan="8" class="text-center py-4 text-slate-500">Pilih outlet untuk melihat data</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-200 flex-shrink-0">
        <div class="flex justify-end">
          <button type="button" onclick="closeMonthlyCostsModal()" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function productionCrud(){
      return {
        // State management
        productions: [],
        outlets: [],
        loading: false,
        currentProduction: null, // For completion modal
        
        // Filters and search
        search: '',
        outletFilter: '<?php echo e($selectedOutlet && $selectedOutlet !== "ALL" ? $selectedOutlet : ""); ?>', // Will be set to first outlet in init
        statusFilter: 'ALL',
        lineFilter: 'ALL',
        sortKey: 'created_at',
        sortDir: 'desc',
        view: 'grid',
        
        // Stats
        stats: {
          active: 0,
          completed: 0,
          progress: 0
        },
        
        // Toast notification
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          console.log('🚀 [INIT] Alpine.js component initializing...');
          console.log('🚀 [INIT] Available methods:', Object.getOwnPropertyNames(this));
          console.log('🚀 [INIT] confirmCompleteProduction method exists:', typeof this.confirmCompleteProduction);
          console.log('🚀 [INIT] Initial state:', {
            outletFilter: this.outletFilter,
            statusFilter: this.statusFilter,
            lineFilter: this.lineFilter,
            view: this.view,
            loading: this.loading
          });
          
          try {
            console.log('🚀 [INIT] Starting parallel data fetch...');
            // First fetch outlets to set default
            await this.fetchOutlets();
            
            // Set default outlet to first accessible outlet if not set
            if (!this.outletFilter || this.outletFilter === '' || this.outletFilter === 'ALL') {
              if (this.outlets.length > 0) {
                this.outletFilter = this.outlets[0].id;
                console.log('🚀 [INIT] Set default outlet to first accessible:', this.outletFilter);
              }
            }
            
            // Then fetch data and stats with the correct outlet
            await Promise.all([
              this.fetchData(),
              this.fetchStats()
            ]);
            console.log('✅ [INIT] All data fetched successfully');
          } catch (error) {
            console.error('💥 [INIT] Error during initialization:', error);
          }
        },

        async fetchData(){
          console.log('🔄 [FETCH DATA] Starting fetchData...');
          console.log('🔄 [FETCH DATA] Current state:', {
            outletFilter: this.outletFilter,
            search: this.search,
            statusFilter: this.statusFilter,
            lineFilter: this.lineFilter,
            sortKey: this.sortKey,
            sortDir: this.sortDir,
            loading: this.loading
          });
          
          this.loading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.outletFilter || '', // Use empty string if no outlet selected
              search: this.search,
              status: this.statusFilter === 'ALL' ? '' : this.statusFilter,
              production_line: this.lineFilter === 'ALL' ? '' : this.lineFilter,
              sort_key: this.sortKey,
              sort_dir: this.sortDir,
              for_grid: 'true'
            });

            const url = `<?php echo e(route('admin.produksi.produksi.data')); ?>?${params}`;
            console.log('🌐 [FETCH DATA] Request URL:', url);
            console.log('🌐 [FETCH DATA] Request params:', Object.fromEntries(params));

            const response = await fetch(url);
            console.log('📡 [FETCH DATA] Response status:', response.status);
            console.log('📡 [FETCH DATA] Response headers:', Object.fromEntries(response.headers));
            
            if (!response.ok) {
              console.error('❌ [FETCH DATA] HTTP Error:', response.status, response.statusText);
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('📦 [FETCH DATA] Response data:', data);
            console.log('📦 [FETCH DATA] Data structure:', {
              success: data.success,
              dataType: typeof data.data,
              dataLength: Array.isArray(data.data) ? data.data.length : 'not array',
              message: data.message || 'no message',
              error: data.error || 'no error'
            });
            
            if (data.success) {
              console.log('✅ [FETCH DATA] Success! Data items:', data.data.length);
              if (data.data.length > 0) {
                console.log('📋 [FETCH DATA] First item sample:', data.data[0]);
              } else {
                console.log('⚠️ [FETCH DATA] No data items returned');
              }
              this.productions = data.data;
              console.log('✅ [FETCH DATA] Productions updated, current count:', this.productions.length);
            } else {
              console.error('❌ [FETCH DATA] Server returned error:', data.message || 'Unknown error');
              this.showToastMessage(data.message || 'Gagal memuat data', 'error');
            }
          } catch (error) {
            console.error('💥 [FETCH DATA] Exception caught:', error);
            console.error('💥 [FETCH DATA] Error details:', {
              name: error.name,
              message: error.message,
              stack: error.stack
            });
            this.showToastMessage('Gagal memuat data: ' + error.message, 'error');
          } finally {
            this.loading = false;
            console.log('🏁 [FETCH DATA] Finished, loading set to false');
            console.log('🏁 [FETCH DATA] Final productions count:', this.productions.length);
          }
        },

        async fetchOutlets(){
          console.log('🏢 [FETCH OUTLETS] Starting fetchOutlets...');
          try {
            const outletsData = <?php echo json_encode($outlets->map(function($outlet) {
              return ['id' => $outlet->id_outlet, 'name' => $outlet->nama_outlet];
            }), 512) ?>;
            console.log('🏢 [FETCH OUTLETS] Outlets data from server:', outletsData);
            this.outlets = outletsData;
            console.log('✅ [FETCH OUTLETS] Outlets loaded:', this.outlets.length, 'items');
          } catch (error) {
            console.error('💥 [FETCH OUTLETS] Error fetching outlets:', error);
          }
        },

        async fetchStats(){
          console.log('📊 [FETCH STATS] Starting fetchStats...');
          console.log('📊 [FETCH STATS] Using outlet:', this.outletFilter);
          
          try {
            const outletParam = this.outletFilter || '';
            const url = `<?php echo e(route('admin.produksi.produksi.statistics')); ?>?outlet_id=${outletParam}`;
            console.log('🌐 [FETCH STATS] Request URL:', url);
            
            const response = await fetch(url);
            console.log('📡 [FETCH STATS] Response status:', response.status);
            
            if (!response.ok) {
              console.error('❌ [FETCH STATS] HTTP Error:', response.status, response.statusText);
              return;
            }
            
            const data = await response.json();
            console.log('📦 [FETCH STATS] Response data:', data);
            
            if (data.success) {
              this.stats = {
                active: data.data.active || 0,
                completed: data.data.completed || 0,
                progress: data.data.average_progress || 0
              };
              console.log('✅ [FETCH STATS] Stats updated:', this.stats);
            } else {
              console.error('❌ [FETCH STATS] Server returned error:', data.message);
            }
          } catch (error) {
            console.error('💥 [FETCH STATS] Exception caught:', error);
          }
        },

        // Helper methods
        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num || 0);
        },

        formatDate(dateString) {
          console.log('formatDate called with:', dateString, typeof dateString);
          
          if (!dateString || dateString === 'null' || dateString === 'undefined' || dateString === null) {
            console.log('formatDate returning - for null/empty date');
            return '-';
          }
          
          try {
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
              console.warn('Invalid date:', dateString);
              return '-';
            }
            
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const formatted = `${day}/${month}/${year}`;
            console.log('formatDate returning:', formatted);
            return formatted;
          } catch (error) {
            console.error('Error formatting date:', dateString, error);
            return '-';
          }
        },

        getStatusClass(status) {
          const classes = {
            'draft': 'bg-slate-100 text-slate-700',
            'approved': 'bg-blue-100 text-blue-700',
            'in_progress': 'bg-yellow-100 text-yellow-700',
            'completed': 'bg-green-100 text-green-700',
            'cancelled': 'bg-red-100 text-red-700'
          };
          return classes[status] || 'bg-slate-100 text-slate-700';
        },

        getStatusText(status) {
          const texts = {
            'draft': 'Draft',
            'approved': 'Disetujui',
            'in_progress': 'Berjalan',
            'completed': 'Selesai',
            'cancelled': 'Dibatalkan'
          };
          return texts[status] || status;
        },

        getPriorityClass(priority) {
          const classes = {
            'normal': 'bg-slate-100 text-slate-600',
            'high': 'bg-orange-100 text-orange-600',
            'urgent': 'bg-red-100 text-red-600'
          };
          return classes[priority] || 'bg-slate-100 text-slate-600';
        },

        getPriorityText(priority) {
          const texts = {
            'normal': 'Normal',
            'high': 'Tinggi',
            'urgent': 'Mendesak'
          };
          return texts[priority] || 'Normal';
        },

        // Actions
        openCreate() {
          // Check if this is being called for edit mode
          const form = document.getElementById('productionForm');
          const isEditMode = form && form.dataset.editMode === 'true';
          
          if (!isEditMode) {
            // Reset form to create mode only if not in edit mode
            if (form) {
              form.dataset.editMode = 'false';
              form.dataset.productionId = '';
              form.reset();
            }
            
            // Reset modal title and button text for create mode
            setTimeout(() => {
              const modalTitle = document.querySelector('#createModal .font-semibold');
              if (modalTitle) {
                modalTitle.textContent = 'Buat Produksi Baru';
              }
              
              const submitButton = document.querySelector('#createModal button[type="submit"]');
              if (submitButton) {
                submitButton.textContent = 'Simpan Produksi';
              }
            }, 100);
          }
          
          // Use the existing modal system
          if (typeof openCreateModal === 'function') {
            openCreateModal();
          } else {
            // Fallback: trigger the modal directly
            const modal = document.getElementById('createModal');
            if (modal) {
              modal.classList.remove('hidden');
              modal.style.display = 'flex';
              document.body.style.overflow = 'hidden';
              
              // Initialize product search after modal is shown
              setTimeout(() => {
                if (typeof initializeProductSearch === 'function') {
                  initializeProductSearch();
                }
              }, 100);
            }
          }
        },

        openMonthlyCosts() {
          // Use the existing monthly costs modal system
          if (typeof openMonthlyCostsModal === 'function') {
            openMonthlyCostsModal();
          } else {
            // Fallback: trigger the modal directly
            const modal = document.getElementById('monthlyCostsModal');
            if (modal) {
              modal.classList.remove('hidden');
              modal.style.display = 'flex';
              document.body.style.overflow = 'hidden';
            } else {
              // If modal doesn't exist, show message
              this.showToastMessage('Modal biaya bulanan tidak ditemukan', 'error');
            }
          }
        },

        viewProduction(production) {
          // Open PDF view
          const url = `<?php echo e(route('admin.produksi.produksi.pdf', ':id')); ?>`.replace(':id', production.id);
          
          // Create iframe for PDF streaming
          const iframe = document.createElement('iframe');
          iframe.src = url;
          iframe.style.width = '100%';
          iframe.style.height = '80vh';
          iframe.style.border = 'none';
          
          // Create PDF modal
          const pdfModal = document.createElement('div');
          pdfModal.id = 'pdfModal';
          pdfModal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2';
          pdfModal.innerHTML = `
            <div class="w-full max-w-6xl bg-white rounded-2xl shadow-float max-h-[95vh] flex flex-col overflow-hidden">
              <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="font-semibold">Detail Produksi - ${production.production_code}</div>
                <button onclick="this.closest('#pdfModal').remove(); document.body.style.overflow = 'auto';" class="p-2 -m-2 hover:bg-slate-100 rounded-lg">
                  <i class='bx bx-x text-xl'></i>
                </button>
              </div>
              <div class="flex-1 overflow-hidden" style="height: 80vh;">
                ${iframe.outerHTML}
              </div>
            </div>
          `;
          
          document.body.appendChild(pdfModal);
          document.body.style.overflow = 'hidden';
        },

        editProduction(production) {
          // Load production data for editing
          this.loadProductionForEdit(production.id);
        },

        async loadProductionForEdit(productionId) {
          try {
            const url = `<?php echo e(route('admin.produksi.produksi.edit', ':id')); ?>`.replace(':id', productionId);
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
              // Populate the create modal with existing data
              this.populateEditModal(data.data);
              this.openCreate(); // Reuse the create modal for editing
            } else {
              this.showToastMessage(data.message || 'Gagal memuat data produksi', 'error');
            }
          } catch (error) {
            console.error('Error loading production for edit:', error);
            this.showToastMessage('Terjadi kesalahan saat memuat data produksi', 'error');
          }
        },

        populateEditModal(production) {
          // Set form to edit mode
          const form = document.getElementById('productionForm');
          if (form) {
            form.dataset.editMode = 'true';
            form.dataset.productionId = production.id;
            
            // Update modal title
            const modalTitle = document.querySelector('#createModal .font-semibold');
            if (modalTitle) {
              modalTitle.textContent = 'Edit Produksi';
            }
            
            // Update submit button text
            const submitButton = document.querySelector('#createModal button[type="submit"]');
            if (submitButton) {
              submitButton.textContent = 'Update Produksi';
            }
            
            console.log('Edit mode set:', {
              editMode: form.dataset.editMode,
              productionId: form.dataset.productionId,
              production: production
            });
          }
          
          // Pre-load materials data for the outlet
          this.preloadMaterialsForOutlet(production.outlet_id);
          
          // Populate basic fields
          setTimeout(() => {
            // Outlet
            const outletSelect = document.getElementById('outletSelect');
            if (outletSelect && production.outlet_id) {
              outletSelect.value = production.outlet_id;
              
              // Trigger change event to load materials for this outlet
              const changeEvent = new Event('change', { bubbles: true });
              outletSelect.dispatchEvent(changeEvent);
            }
            
            // Production line
            const productionLineSelect = document.querySelector('select[name="production_line"]');
            if (productionLineSelect) {
              productionLineSelect.value = production.production_line;
            }
            
            // Dates - Format from ISO to YYYY-MM-DD and handle timezone properly
            const startDateInput = document.querySelector('input[name="start_date"]');
            if (startDateInput && production.start_date) {
              // Parse date and format to local date string
              const startDate = new Date(production.start_date + 'T00:00:00');
              if (!isNaN(startDate.getTime())) {
                const year = startDate.getFullYear();
                const month = String(startDate.getMonth() + 1).padStart(2, '0');
                const day = String(startDate.getDate()).padStart(2, '0');
                startDateInput.value = `${year}-${month}-${day}`;
                console.log('Set start date:', startDateInput.value, 'from:', production.start_date);
              }
            }
            
            const endDateInput = document.querySelector('input[name="end_date"]');
            if (endDateInput && production.end_date) {
              // Parse date and format to local date string
              const endDate = new Date(production.end_date + 'T00:00:00');
              if (!isNaN(endDate.getTime())) {
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                endDateInput.value = `${year}-${month}-${day}`;
                console.log('Set end date:', endDateInput.value, 'from:', production.end_date);
              }
            }
            
            const expiryDateInput = document.querySelector('input[name="expiry_date"]');
            if (expiryDateInput && production.expiry_date) {
              // Parse date and format to local date string
              const expiryDate = new Date(production.expiry_date + 'T00:00:00');
              if (!isNaN(expiryDate.getTime())) {
                const year = expiryDate.getFullYear();
                const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
                const day = String(expiryDate.getDate()).padStart(2, '0');
                expiryDateInput.value = `${year}-${month}-${day}`;
                console.log('Set expiry date:', expiryDateInput.value, 'from:', production.expiry_date);
              }
            }
            
            // Priority
            const prioritySelect = document.querySelector('select[name="priority"]');
            if (prioritySelect) {
              prioritySelect.value = production.priority || 'normal';
            }
            
            // Description
            const descriptionInput = document.querySelector('textarea[name="description"]');
            if (descriptionInput && production.description) {
              descriptionInput.value = production.description;
            }
            
            // Business type
            const businessTypeSelect = document.querySelector('select[name="business_type"]');
            if (businessTypeSelect && production.business_type) {
              businessTypeSelect.value = production.business_type;
              
              // Trigger business type change to show/hide specific forms
              setTimeout(() => {
                if (typeof toggleBusinessSpecificForms === 'function') {
                  toggleBusinessSpecificForms();
                }
              }, 100);
            }
            
            // Load tofu data if business type is tofu
            if (production.business_type === 'tofu' && production.tofu_data) {
              this.loadTofuDataForEdit(production.tofu_data);
            }
            
            // Load products (multi-product support)
            this.loadProductsForEdit(production.products);
            
            // Load materials with proper async handling - DELAYED to ensure materials are loaded
            setTimeout(() => {
              this.loadMaterialsForEdit(production.materials);
            }, 1000);
            
            // Load labor costs
            this.loadLaborCostsForEdit(production.labor_costs);
            
            // Load operational costs with proper async handling
            this.loadOperationalCostsForEdit(production.operational_costs);
            
            // Trigger HPP calculation only once after all data is loaded
            setTimeout(() => {
              if (typeof calculateHppPreview === 'function') {
                calculateHppPreview();
              }
            }, 3000); // Increased delay to ensure all data is loaded
          }, 500); // Give time for modal to open
        },

        async preloadMaterialsForOutlet(outletId) {
          console.log('🔄 Pre-loading materials for outlet:', outletId);
          
          try {
            // Use the materials API to load data
            const response = await fetch(`${materialsUrl}?outlet_id=${outletId}`);
            
            if (!response.ok) {
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📦 Pre-loaded materials:', data);
            
            if (data.success && Array.isArray(data.data)) {
              // Store materials in global state
              if (!window.state) {
                window.state = {};
              }
              window.state.materials = data.data;
              console.log('✅ Materials pre-loaded:', window.state.materials.length, 'items');
            } else {
              console.error('❌ Invalid materials response:', data);
              window.state.materials = [];
            }
          } catch (error) {
            console.error('❌ Error pre-loading materials:', error);
            if (!window.state) {
              window.state = {};
            }
            window.state.materials = [];
          }
        },

        async loadProductsForEdit(products) {
          if (!products || products.length === 0) {
            console.warn('No products data found for edit');
            return;
          }
          
          console.log('Loading products for edit:', products);
          
          // Clear existing product rows
          const productContainer = document.getElementById('productRows');
          if (productContainer) {
            productContainer.innerHTML = '';
            productRowIndex = 0; // Reset index
          }
          
          // Ensure available products are loaded
          if (availableProducts.length === 0) {
            await loadAvailableProducts();
          }
          
          // Add each product
          for (let i = 0; i < products.length; i++) {
            const product = products[i];
            
            // Add product row
            if (typeof addProductRow === 'function') {
              addProductRow();
            }
            
            // Wait for row to be created
            await new Promise(resolve => setTimeout(resolve, 200));
            
            // Populate the product row
            const productRows = document.querySelectorAll('#productRows .product-row');
            const currentRow = productRows[i];
            
            if (currentRow) {
              // Find product by ID in available products
              const availableProduct = availableProducts.find(p => p.id == product.product_id);
              
              // Populate product search and hidden field
              const productSearchInput = currentRow.querySelector('input[type="text"]');
              const productIdInput = currentRow.querySelector('input[name*="product_id"]');
              
              if (productSearchInput && availableProduct) {
                productSearchInput.value = availableProduct.name || product.product_name || 'Unknown Product';
              }
              
              if (productIdInput) {
                productIdInput.value = product.product_id;
              }
              
              // Populate target quantity
              const targetQuantityInput = currentRow.querySelector('input[name*="target_quantity"]');
              if (targetQuantityInput && product.target_quantity) {
                targetQuantityInput.value = product.target_quantity;
              }
              
              // Populate sample quantity
              const sampleQuantityInput = currentRow.querySelector('input[name*="sample_quantity"]');
              if (sampleQuantityInput && product.sample_quantity) {
                sampleQuantityInput.value = product.sample_quantity;
              }
            }
          }
          
          // Calculate total target quantity
          setTimeout(() => {
            if (typeof calculateTotalTargetQuantity === 'function') {
              calculateTotalTargetQuantity();
            }
          }, 300);
        },

        loadLaborCostsForEdit(laborCosts) {
          if (!laborCosts || laborCosts.length === 0) return;
          
          const laborCost = laborCosts; // laborCosts is already an object from backend
          
          const workerCountInput = document.querySelector('input[name="labor_costs[worker_count]"]');
          if (workerCountInput && laborCost.worker_count) {
            workerCountInput.value = laborCost.worker_count;
          }
          
          const costPerWorkerInput = document.querySelector('input[name="labor_costs[cost_per_worker]"]');
          if (costPerWorkerInput && laborCost.cost_per_worker) {
            costPerWorkerInput.value = laborCost.cost_per_worker;
          }
          
          const totalCostInput = document.querySelector('input[name="labor_costs[total_cost]"]');
          if (totalCostInput && laborCost.total_cost) {
            totalCostInput.value = laborCost.total_cost;
          }
          
          // Trigger calculation
          setTimeout(() => {
            if (typeof calculateLaborCost === 'function') {
              calculateLaborCost();
            }
          }, 100);
        },

        async loadOperationalCostsForEdit(operationalCosts) {
          if (!operationalCosts || operationalCosts.length === 0) {
            // If no operational costs, ensure at least one empty row exists
            const operationalContainer = document.getElementById('operationalCosts');
            if (operationalContainer && operationalContainer.children.length === 0) {
              if (typeof addOperationalCost === 'function') {
                addOperationalCost();
              }
            }
            return;
          }
          
          console.log('🔧 Loading operational costs for edit:', operationalCosts);
          
          // Clear existing operational costs
          const operationalContainer = document.getElementById('operationalCosts');
          if (operationalContainer) {
            operationalContainer.innerHTML = '';
          }
          
          // Add each operational cost with proper population
          for (let i = 0; i < operationalCosts.length; i++) {
            const cost = operationalCosts[i];
            
            console.log(`🔧 Loading operational cost ${i + 1}:`, cost);
            
            // Add operational cost row
            if (typeof addOperationalCost === 'function') {
              addOperationalCost();
            }
            
            // Wait for row to be created
            await new Promise(resolve => setTimeout(resolve, 100));
            
            // Populate the operational cost row
            const costRows = document.querySelectorAll('#operationalCosts .operational-cost-row');
            const currentRow = costRows[i];
            
            if (currentRow) {
              // Find inputs with correct selectors
              const typeSelect = currentRow.querySelector('select[name*="cost_type"]');
              const amountInput = currentRow.querySelector('input[name*="amount"]');
              const descInput = currentRow.querySelector('input[name*="description"]');
              
              if (typeSelect && cost.cost_type) {
                // Check if the cost_type exists as an option
                const existingOption = typeSelect.querySelector(`option[value="${cost.cost_type}"]`);
                
                if (!existingOption) {
                  // Add the cost_type as a new option if it doesn't exist
                  const newOption = document.createElement('option');
                  newOption.value = cost.cost_type;
                  newOption.textContent = cost.cost_type;
                  typeSelect.appendChild(newOption);
                  console.log(`🔧 Added new option: ${cost.cost_type}`);
                }
                
                typeSelect.value = cost.cost_type;
                console.log(`🔧 Set cost_type: ${cost.cost_type}`);
              }
              
              if (amountInput && cost.amount) {
                amountInput.value = cost.amount;
                console.log(`🔧 Set amount: ${cost.amount}`);
                
                // Update display
                if (typeof updateOperationalCostDisplay === 'function') {
                  updateOperationalCostDisplay(amountInput);
                }
              }
              
              if (descInput && cost.description) {
                descInput.value = cost.description;
                console.log(`🔧 Set description: ${cost.description}`);
              }
            } else {
              console.error(`🔧 Could not find operational cost row ${i}`);
            }
          }
          
          console.log('🔧 Operational costs loaded, triggering HPP calculation...');
          
          // Trigger HPP calculation after loading operational costs
          setTimeout(() => {
            if (typeof calculateHppPreview === 'function') {
              calculateHppPreview();
            }
          }, 500); // Increased delay to ensure all data is loaded
        },

        async loadMaterialsForEdit(materials) {
          if (!materials || materials.length === 0) {
            console.log('=== No materials to load for edit ===');
            // Still add one empty material row for editing
            if (typeof addMaterial === 'function') {
              addMaterial();
            }
            return;
          }
          
          console.log('=== Loading materials for edit ===', materials);
          
          // Clear existing materials
          const materialContainer = document.getElementById('materialRequirements');
          if (materialContainer) {
            materialContainer.innerHTML = '';
          }
          
          // Force load materials data first
          await this.forceLoadMaterialsData();
          
          // Add each material with proper population
          for (let i = 0; i < materials.length; i++) {
            const material = materials[i];
            
            console.log(`=== Loading material ${i + 1} ===`, material);
            
            // Add material row with materials data
            await this.addMaterialRowWithData();
            
            // Wait for row to be created
            await new Promise(resolve => setTimeout(resolve, 300));
            
            // Populate the material row
            const materialRows = document.querySelectorAll('#materialRequirements .material-row');
            const currentRow = materialRows[i];
            
            if (currentRow) {
              const materialSelect = currentRow.querySelector('select[name*="material_id"]');
              const quantityInput = currentRow.querySelector('input[name*="quantity"]');
              const unitInput = currentRow.querySelector('input[name*="unit"]');
              
              console.log(`=== Populating material row ${i + 1} ===`, {
                materialSelect: !!materialSelect,
                quantityInput: !!quantityInput,
                unitInput: !!unitInput,
                material_id: material.material_id,
                quantity: material.quantity,
                unit: material.unit
              });
              
              if (materialSelect) {
                // Ensure options are populated before setting value
                if (materialSelect.options.length <= 1) {
                  console.log('🔄 Re-populating select options...');
                  await this.populateSelectWithMaterials(materialSelect);
                }
                
                // Set the material ID
                materialSelect.value = material.material_id;
                console.log(`✅ Set material select value: ${material.material_id}`);
                
                // Trigger change event to update unit
                const changeEvent = new Event('change', { bubbles: true });
                materialSelect.dispatchEvent(changeEvent);
              }
              
              if (quantityInput) {
                quantityInput.value = material.quantity;
                console.log(`✅ Set quantity: ${material.quantity}`);
              }
              
              // Set unit directly if available
              if (unitInput && material.unit) {
                unitInput.value = material.unit;
                console.log(`✅ Set unit: ${material.unit}`);
              }
            } else {
              console.error(`❌ Material row ${i + 1} not found`);
            }
          }
          
          // Final HPP calculation with longer delay
          setTimeout(() => {
            console.log('=== Triggering HPP calculation after materials loaded ===');
            if (typeof calculateHppPreview === 'function') {
              calculateHppPreview();
            }
          }, 1000);
        },

        async forceLoadMaterialsData() {
          console.log('🔄 Force loading materials data...');
          
          try {
            const outletSelect = document.getElementById('outletSelect');
            const outletId = outletSelect ? outletSelect.value : null;
            
            if (!outletId) {
              console.warn('⚠️ No outlet selected for materials loading');
              return;
            }

            // Use the materials API to load data
            const response = await fetch(`${materialsUrl}?outlet_id=${outletId}`);
            
            if (!response.ok) {
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📦 Materials API response:', data);
            
            if (data.success && Array.isArray(data.data)) {
              // Store materials in global state
              if (!window.state) {
                window.state = {};
              }
              window.state.materials = data.data;
              console.log('✅ Materials data loaded:', window.state.materials.length, 'items');
            } else {
              console.error('❌ Invalid materials response:', data);
              window.state.materials = [];
            }
          } catch (error) {
            console.error('❌ Error loading materials:', error);
            window.state.materials = [];
          }
        },

        async addMaterialRowWithData() {
          console.log('➕ Adding material row with data...');
          
          // Ensure materials are loaded
          if (!window.state || !window.state.materials || window.state.materials.length === 0) {
            await this.forceLoadMaterialsData();
          }
          
          // Call the addMaterial function
          if (typeof addMaterial === 'function') {
            addMaterial();
          } else {
            console.error('❌ addMaterial function not available');
          }
        },

        async populateSelectWithMaterials(selectElement) {
          console.log('🔄 Populating select with materials...');
          
          // Clear existing options except the first one
          while (selectElement.options.length > 1) {
            selectElement.removeChild(selectElement.lastChild);
          }
          
          // Ensure materials are loaded
          if (!window.state || !window.state.materials || window.state.materials.length === 0) {
            await this.forceLoadMaterialsData();
          }
          
          // Populate with materials
          if (window.state && window.state.materials && window.state.materials.length > 0) {
            window.state.materials.forEach(material => {
              const option = document.createElement('option');
              option.value = material.id;
              option.textContent = material.name + " (Stok: " + material.stock + " " + material.unit + ")";
              option.dataset.type = material.type;
              option.dataset.unit = material.unit;
              selectElement.appendChild(option);
            });
            console.log(`✅ Populated select with ${window.state.materials.length} materials`);
          } else {
            console.warn('⚠️ No materials available to populate');
          }
        },

        async ensureMaterialsLoaded() {
          // Check if materials are already loaded
          if (window.state && window.state.materials && window.state.materials.length > 0) {
            return Promise.resolve();
          }
          
          // Load materials if not loaded
          return new Promise((resolve) => {
            if (typeof loadMaterials === 'function') {
              loadMaterials();
              
              // Wait for materials to load
              const checkMaterials = () => {
                if (window.state && window.state.materials && window.state.materials.length > 0) {
                  resolve();
                } else {
                  setTimeout(checkMaterials, 100);
                }
              };
              
              setTimeout(checkMaterials, 100);
            } else {
              resolve();
            }
          });
        },

        loadTofuDataForEdit(tofuData) {
          if (!tofuData || typeof tofuData !== 'object') {
            console.log('No tofu data to load for edit');
            return;
          }
          
          console.log('Loading tofu data for edit:', tofuData);
          
          // Populate all tofu form fields
          const tofuFields = [
            'perendaman_waktu',
            'perendaman_qty', 
            'rijek_telur',
            'pasteurisasi_waktu',
            'pasteurisasi_suhu',
            'berat_sari_kedelai',
            'waktu_pencampuran',
            'filling_waktu',
            'filling_mesin1',
            'filling_mesin2',
            'filling_total',
            'rijek_mentah'
          ];
          
          tofuFields.forEach(field => {
            const input = document.querySelector(`input[name="tofu_data[${field}]"]`);
            if (input && tofuData[field] !== undefined && tofuData[field] !== null) {
              input.value = tofuData[field];
              console.log(`Set tofu field ${field}:`, tofuData[field]);
            }
          });
          
          // Recalculate filling total if mesin values are present
          if (tofuData.filling_mesin1 || tofuData.filling_mesin2) {
            setTimeout(() => {
              if (typeof calculateFillingTotal === 'function') {
                calculateFillingTotal();
              }
            }, 100);
          }
        },

        getQcDataSummary(production) {
          if (!production.tofu_data) {
            return 'Data QC tidak tersedia';
          }
          
          try {
            const tofuData = typeof production.tofu_data === 'string' 
              ? JSON.parse(production.tofu_data) 
              : production.tofu_data;
            
            const summaryParts = [];
            
            // Add key QC metrics
            if (tofuData.perendaman_waktu) {
              summaryParts.push(`Perendaman: ${tofuData.perendaman_waktu}h`);
            }
            if (tofuData.rijek_telur) {
              summaryParts.push(`Rijek Telur: ${tofuData.rijek_telur}`);
            }
            if (tofuData.filling_total) {
              summaryParts.push(`Total Filling: ${tofuData.filling_total}`);
            }
            if (tofuData.rijek_mentah) {
              summaryParts.push(`Rijek Mentah: ${tofuData.rijek_mentah}`);
            }
            
            return summaryParts.length > 0 
              ? summaryParts.slice(0, 2).join(' • ') 
              : 'Data QC tersedia';
              
          } catch (error) {
            console.error('Error parsing tofu_data:', error);
            return 'Error parsing QC data';
          }
        },

        downloadProductionPdf(productionId) {
          const url = `<?php echo e(route('admin.produksi.produksi.pdf', ':id')); ?>`.replace(':id', productionId);
          window.open(url, '_blank');
        },

        downloadQcTofuPdf(productionId) {
          const url = `<?php echo e(route('admin.produksi.produksi.qc-tofu-pdf', ':id')); ?>`.replace(':id', productionId);
          window.open(url, '_blank');
        },

        async approveProduction(production) {
          if (confirm(`Apakah Anda yakin ingin menyetujui produksi ${production.production_code}?`)) {
            try {
              const url = `<?php echo e(route('admin.produksi.produksi.approve', ':id')); ?>`.replace(':id', production.id);
              const response = await fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
              });
              
              const data = await response.json();
              if (data.success) {
                this.showToastMessage('Produksi berhasil disetujui', 'success');
                await this.fetchData(); // Auto reload data
                await this.fetchStats(); // Update statistics
              } else {
                this.showToastMessage(data.message || 'Gagal menyetujui produksi', 'error');
              }
            } catch (error) {
              console.error('Error:', error);
              this.showToastMessage('Terjadi kesalahan saat menyetujui produksi', 'error');
            }
          }
        },

        async startProduction(production) {
          if (confirm(`Apakah Anda yakin ingin memulai produksi ${production.production_code}?`)) {
            try {
              const url = `<?php echo e(route('admin.produksi.produksi.start', ':id')); ?>`.replace(':id', production.id);
              const response = await fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
              });
              
              const data = await response.json();
              if (data.success) {
                this.showToastMessage('Produksi berhasil dimulai', 'success');
                await this.fetchData(); // Auto reload data
                await this.fetchStats(); // Update statistics
              } else {
                this.showToastMessage(data.message || 'Gagal memulai produksi', 'error');
              }
            } catch (error) {
              console.error('Error:', error);
              this.showToastMessage('Terjadi kesalahan saat memulai produksi', 'error');
            }
          }
        },

        completeProduction(production) {
          console.log('completeProduction called with:', production);
          // Show completion modal instead of simple confirm
          this.showCompletionModal(production);
        },

        showCompletionModal(production) {
          console.log('showCompletionModal called with:', production);
          // Store production data for modal
          this.currentProduction = production;
          
          // Populate modal data
          document.getElementById('completionProductionCode').textContent = production.production_code;
          document.getElementById('completionProductionLine').textContent = production.production_line;
          document.getElementById('completionTargetQuantity').textContent = this.formatNumber(production.target_quantity);
          document.getElementById('completionRealizedQuantity').textContent = this.formatNumber(production.realized_quantity || 0);
          
          // Reset checkbox
          document.getElementById('consumeRemainingMaterials').checked = false;
          
          // Show modal
          const modal = document.getElementById('completionModal');
          modal.classList.remove('hidden');
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        },

        closeCompletionModal() {
          console.log('closeCompletionModal called');
          const modal = document.getElementById('completionModal');
          modal.classList.add('hidden');
          modal.style.display = 'none';
          document.body.style.overflow = 'auto';
          this.currentProduction = null;
        },

        async confirmCompleteProduction() {
          console.log('confirmCompleteProduction called');
          if (!this.currentProduction) {
            console.log('No current production found');
            return;
          }
          
          try {
            const consumeRemaining = document.getElementById('consumeRemainingMaterials').checked;
            console.log('Consume remaining materials:', consumeRemaining);
            
            const url = `<?php echo e(route('admin.produksi.produksi.complete', ':id')); ?>`.replace(':id', this.currentProduction.id);
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({
                consume_remaining_materials: consumeRemaining
              })
            });
            
            const data = await response.json();
            if (data.success) {
              this.showToastMessage('Produksi berhasil diselesaikan', 'success');
              this.closeCompletionModal();
              await this.fetchData(); // Auto reload data
              await this.fetchStats(); // Update statistics
            } else {
              this.showToastMessage(data.message || 'Gagal menyelesaikan produksi', 'error');
            }
          } catch (error) {
            console.error('Error:', error);
            this.showToastMessage('Terjadi kesalahan saat menyelesaikan produksi', 'error');
          }
        },

        async deleteProduction(production) {
          if (confirm(`Apakah Anda yakin ingin menghapus produksi ${production.production_code}? Tindakan ini tidak dapat dibatalkan.`)) {
            try {
              const url = `<?php echo e(route('admin.produksi.produksi.destroy', ':id')); ?>`.replace(':id', production.id);
              const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
              });
              
              const data = await response.json();
              if (data.success) {
                this.showToastMessage('Produksi berhasil dihapus', 'success');
                await this.fetchData(); // Auto reload data
                await this.fetchStats(); // Update statistics
              } else {
                this.showToastMessage(data.message || 'Gagal menghapus produksi', 'error');
              }
            } catch (error) {
              console.error('Error:', error);
              this.showToastMessage('Terjadi kesalahan saat menghapus produksi', 'error');
            }
          }
        },

        showRealizationModal(production) {
          console.log('Alpine showRealizationModal called with:', production);
          
          // Open realization modal directly
          const modal = document.getElementById('realizationModal');
          if (modal) {
            // Populate production info with null checks
            const codeElement = document.getElementById('realizationProductionCode');
            const lineElement = document.getElementById('realizationProductionLine');
            const targetElement = document.getElementById('realizationTotalTarget');
            const currentElement = document.getElementById('realizationTotalCurrent');
            
            if (codeElement) codeElement.textContent = production.production_code || '-';
            if (lineElement) lineElement.textContent = production.production_line || '-';
            if (targetElement) targetElement.textContent = this.formatNumber(production.target_quantity || 0) + ' unit';
            if (currentElement) currentElement.textContent = this.formatNumber(production.realized_quantity || 0) + ' unit';
            
            // Load and populate product realization rows
            this.loadProductRealizationRows(production.id);
            
            // Store production ID for form submission
            const form = document.getElementById('realizationForm');
            if (form) {
              form.dataset.productionId = production.id;
            }
            
            // Show modal
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
          } else {
            console.error('Realization modal not found');
            this.showToastMessage('Modal realisasi tidak ditemukan', 'error');
          }
        },

        async loadProductRealizationRows(productionId) {
          try {
            console.log('Loading product realization rows for production:', productionId);
            
            // Fetch production details with products
            const response = await fetch(`<?php echo e(route('admin.produksi.produksi.show', ':id')); ?>`.replace(':id', productionId));
            
            if (!response.ok) {
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('Production details loaded:', data);
            
            if (data.success && data.data) {
              const production = data.data;
              const container = document.getElementById('productRealizationRows');
              
              if (!container) {
                console.error('Product realization rows container not found');
                return;
              }
              
              // Get products from HPP records
              const products = production.hpp_records || [];
              console.log('HPP records found:', products);
              
              if (products.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-slate-500">Tidak ada produk ditemukan untuk produksi ini.</div>';
                return;
              }
              
              container.innerHTML = products.map((hppRecord, index) => {
                const product = hppRecord.product || {};
                const currentRealized = parseInt(hppRecord.realized_quantity) || 0;
                const targetQuantity = parseInt(hppRecord.target_quantity) || 0;
                const remainingQuantity = Math.max(0, targetQuantity - currentRealized);
                const progressPercent = targetQuantity > 0 ? Math.round((currentRealized / targetQuantity) * 100) : 0;
                
                console.log(`Product ${index}:`, {
                  product_name: product.nama_produk,
                  current_realized: currentRealized,
                  target_quantity: targetQuantity,
                  remaining: remainingQuantity
                });
                
                return `
                  <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                      <div class="flex-1">
                        <h4 class="font-medium text-slate-700">${product.nama_produk || 'Unknown Product'}</h4>
                        <p class="text-sm text-slate-500">${product.kode_produk || 'No Code'}</p>
                      </div>
                      <div class="text-right text-sm">
                        <div class="text-slate-500">Progress</div>
                        <div class="font-medium">${currentRealized}/${targetQuantity}</div>
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                      <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Diproduksi</label>
                        <input type="number" 
                               name="products[${index}][quantity_produced]" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200" 
                               min="0" 
                               max="${remainingQuantity}"
                               placeholder="0">
                        <div class="text-xs text-slate-500 mt-1">Maks: ${remainingQuantity}</div>
                        <input type="hidden" name="products[${index}][product_id]" value="${product.id_produk || ''}">
                        <input type="hidden" name="products[${index}][hpp_record_id]" value="${hppRecord.id || ''}">
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Reject</label>
                        <input type="number" 
                               name="products[${index}][quantity_rejected]" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200" 
                               min="0" 
                               placeholder="0">
                        <div class="text-xs text-slate-500 mt-1">Opsional</div>
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Produk</label>
                        <input type="text" 
                               name="products[${index}][notes]" 
                               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200" 
                               placeholder="Catatan khusus...">
                        <div class="text-xs text-slate-500 mt-1">Opsional</div>
                      </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-3">
                      <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-slate-600">Progress Produk</span>
                        <span class="font-medium">${progressPercent}%</span>
                      </div>
                      <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-primary-600 h-2 rounded-full transition-all" style="width: ${Math.min(progressPercent, 100)}%"></div>
                      </div>
                    </div>
                  </div>
                `;
              }).join('');
              
              console.log('Product realization rows populated successfully');
              
            } else {
              console.error('Failed to load production details:', data);
              const container = document.getElementById('productRealizationRows');
              if (container) {
                container.innerHTML = '<div class="text-center py-4 text-red-500">Gagal memuat detail produksi.</div>';
              }
            }
          } catch (error) {
            console.error('Error loading product realization rows:', error);
            const container = document.getElementById('productRealizationRows');
            if (container) {
              container.innerHTML = '<div class="text-center py-4 text-red-500">Terjadi kesalahan saat memuat data produk.</div>';
            }
            this.showToastMessage('Gagal memuat data produk untuk realisasi', 'error');
          }
        },

        exportBulkProductionPdf(){
          const params = new URLSearchParams({
            outlet_id: this.outletFilter,
            status: this.statusFilter !== 'ALL' ? this.statusFilter : '',
            production_line: this.lineFilter !== 'ALL' ? this.lineFilter : '',
            search: this.search || '',
            sort_key: this.sortKey || 'created_at',
            sort_dir: this.sortDir || 'desc'
          });
          window.open(`<?php echo e(route('admin.produksi.produksi.export.bulk-production-pdf')); ?>?${params}`, '_blank');
        },

        exportQcTofuMentahPdf(){
          const params = new URLSearchParams({
            outlet_id: this.outletFilter,
            status: this.statusFilter !== 'ALL' ? this.statusFilter : '',
            production_line: this.lineFilter !== 'ALL' ? this.lineFilter : '',
            search: this.search || '',
            sort_key: this.sortKey || 'created_at',
            sort_dir: this.sortDir || 'desc'
          });
          window.open(`<?php echo e(route('admin.produksi.produksi.export.qc-tofu-mentah-pdf')); ?>?${params}`, '_blank');
        },

        exportBulkQcTofuPdf(){
          // Legacy method - redirect to new QC export
          this.exportQcTofuMentahPdf();
        },

        exportPdf(){
          // Legacy method - redirect to bulk production PDF
          this.exportBulkProductionPdf();
        },

        exportExcel(){
          const params = new URLSearchParams({
            outlet_id: this.outletFilter,
            status: this.statusFilter !== 'ALL' ? this.statusFilter : '',
            production_line: this.lineFilter !== 'ALL' ? this.lineFilter : ''
          });
          window.open(`<?php echo e(route('admin.produksi.produksi.export.excel')); ?>?${params}`, '_blank');
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        }
      };
    }

    // Monthly Costs Modal Functions
    function closeMonthlyCostsModal() {
      const modal = document.getElementById('monthlyCostsModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
      }
    }

    function loadMonthlyCostsData(outletId) {
      if (!outletId) return;
      
      // Show loading state
      document.getElementById('currentMonthCost').textContent = 'Loading...';
      document.getElementById('averageDailyCost').textContent = 'Loading...';
      document.getElementById('projectedMonthlyCost').textContent = 'Loading...';
      
      // Load monthly costs data via AJAX
      fetch(`<?php echo e(route('admin.produksi.produksi.monthly-production-costs.data')); ?>?outlet_id=${outletId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update current month stats
            const current = data.current || {};
            document.getElementById('currentMonthCost').textContent = 
              new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(current.total_cost || 0);
            document.getElementById('averageDailyCost').textContent = 
              new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(current.average_daily || 0);
            document.getElementById('projectedMonthlyCost').textContent = 
              new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(current.projected || 0);
            
            // Update history table
            const tbody = document.getElementById('monthlyHistoryBody');
            if (data.history && data.history.length > 0) {
              tbody.innerHTML = data.history.map(item => `
                <tr class="border-b border-slate-100">
                  <td class="py-2 px-3 font-medium">${item.period}</td>
                  <td class="py-2 px-3 text-right font-semibold text-slate-800">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.total_cost)}</td>
                  <td class="py-2 px-3 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.electricity_cost)}</td>
                  <td class="py-2 px-3 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.water_cost)}</td>
                  <td class="py-2 px-3 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.fuel_cost)}</td>
                  <td class="py-2 px-3 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.office_salary_cost)}</td>
                  <td class="py-2 px-3 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.other_costs)}</td>
                  <td class="py-2 px-3 text-slate-600 text-xs">${item.notes || '-'}</td>
                </tr>
              `).join('');
            } else {
              tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-slate-500">Tidak ada data</td></tr>';
            }
          }
        })
        .catch(error => {
          console.error('Error loading monthly costs:', error);
          document.getElementById('currentMonthCost').textContent = 'Error';
          document.getElementById('averageDailyCost').textContent = 'Error';
          document.getElementById('projectedMonthlyCost').textContent = 'Error';
        });
    }

    // Initialize monthly costs modal when outlet changes
    document.addEventListener('DOMContentLoaded', function() {
      const outletSelect = document.getElementById('monthlyCostsOutletSelect');
      if (outletSelect) {
        outletSelect.addEventListener('change', function() {
          loadMonthlyCostsData(this.value);
          // Update form outlet ID
          const formOutletId = document.getElementById('formOutletId');
          if (formOutletId) {
            formOutletId.value = this.value;
          }
        });
      }

      // Toggle form visibility
      const toggleButton = document.getElementById('toggleInputForm');
      const formContent = document.getElementById('inputFormContent');
      const toggleText = document.getElementById('toggleText');
      const toggleIcon = document.getElementById('toggleIcon');
      
      if (toggleButton && formContent) {
        toggleButton.addEventListener('click', function() {
          const isHidden = formContent.classList.contains('hidden');
          if (isHidden) {
            formContent.classList.remove('hidden');
            toggleText.textContent = 'Sembunyikan Form';
            toggleIcon.classList.remove('bx-chevron-down');
            toggleIcon.classList.add('bx-chevron-up');
          } else {
            formContent.classList.add('hidden');
            toggleText.textContent = 'Tampilkan Form';
            toggleIcon.classList.remove('bx-chevron-up');
            toggleIcon.classList.add('bx-chevron-down');
          }
        });
      }

      // Handle form submission
      const monthlyCostsForm = document.getElementById('monthlyCostsForm');
      if (monthlyCostsForm) {
        monthlyCostsForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          const submitButton = this.querySelector('button[type="submit"]');
          const originalText = submitButton.textContent;
          
          // Show loading state
          submitButton.disabled = true;
          submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin mr-2"></i>Menyimpan...';
          
          fetch('<?php echo e(route("admin.produksi.produksi.monthly-production-costs.store")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show success message
              alert('Biaya bulanan berhasil disimpan!');
              
              // Reset form
              monthlyCostsForm.reset();
              
              // Reload data if outlet is selected
              const selectedOutlet = document.getElementById('monthlyCostsOutletSelect').value;
              if (selectedOutlet) {
                loadMonthlyCostsData(selectedOutlet);
              }
              
              // Hide form
              formContent.classList.add('hidden');
              toggleText.textContent = 'Tampilkan Form';
              toggleIcon.classList.remove('bx-chevron-up');
              toggleIcon.classList.add('bx-chevron-down');
            } else {
              alert('Error: ' + (data.message || 'Gagal menyimpan data'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
          })
          .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
          });
        });
      }
    });

    // ===== OPERATIONAL COSTS AUTO CALCULATION FUNCTIONS =====
    
    // Global variable to store monthly cost data
    let monthlyOperationalData = null;

    /**
     * Load monthly operational costs for auto calculation
     */
    function loadMonthlyOperationalCosts() {
      const outletSelect = document.getElementById('outletSelect');
      const outletId = outletSelect ? outletSelect.value : null;
      
      if (!outletId) {
        alert('Pilih outlet terlebih dahulu');
        return;
      }

      // Show loading state
      const autoSection = document.getElementById('autoOperationalSection');
      autoSection.classList.remove('hidden');
      
      // Fetch monthly cost data
      fetch(`<?php echo e(route('admin.produksi.produksi.monthly-production-costs.data')); ?>?outlet_id=${outletId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.current && data.current.total_cost > 0) {
            // Use the latest available data from MonthlyProductionCost
            fetch(`<?php echo e(route('admin.produksi.produksi.monthly-costs.get')); ?>?outlet_id=${outletId}&limit=1`)
              .then(response => response.json())
              .then(detailData => {
                if (detailData.success && detailData.data && detailData.data.length > 0) {
                  monthlyOperationalData = detailData.data[0];
                  displayMonthlyCostBreakdown(detailData.data[0]);
                  
                  // Set default working days and office percentage
                  const currentDate = new Date();
                  const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
                  document.getElementById('workingDays').value = daysInMonth;
                  document.getElementById('officePercentage').value = 30;
                  
                  // Calculate initial costs
                  calculateDailyOperationalCosts();
                } else {
                  alert('Data detail biaya bulanan tidak ditemukan untuk outlet ini.');
                  clearAutoOperational();
                }
              })
              .catch(error => {
                console.error('Error loading monthly cost details:', error);
                alert('Gagal memuat detail biaya bulanan');
                clearAutoOperational();
              });
          } else {
            alert('Data biaya bulanan tidak ditemukan untuk outlet ini. Silakan input biaya bulanan terlebih dahulu.');
            clearAutoOperational();
          }
        })
        .catch(error => {
          console.error('Error loading monthly costs:', error);
          alert('Gagal memuat data biaya bulanan');
          clearAutoOperational();
        });
    }

    /**
     * Display monthly cost breakdown
     */
    function displayMonthlyCostBreakdown(costData) {
      const breakdown = document.getElementById('monthlyCostBreakdown');
      breakdown.classList.remove('hidden');
      
      // Format currency
      const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', { 
          style: 'currency', 
          currency: 'IDR',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(amount || 0);
      };
      
      // Update monthly costs
      document.getElementById('monthlyElectricity').textContent = formatCurrency(costData.electricity_cost);
      document.getElementById('monthlyWater').textContent = formatCurrency(costData.water_cost);
      document.getElementById('monthlyFuel').textContent = formatCurrency(costData.fuel_cost);
      document.getElementById('monthlyOffice').textContent = formatCurrency(costData.office_salary_cost);
    }

    /**
     * Calculate daily operational costs based on working days
     */
    function calculateDailyOperationalCosts() {
      const workingDaysInput = document.getElementById('workingDays');
      const officePercentageInput = document.getElementById('officePercentage');
      const workingDays = parseInt(workingDaysInput.value) || 0;
      const officePercentage = parseInt(officePercentageInput.value) || 30;
      
      if (!monthlyOperationalData || workingDays <= 0) {
        return;
      }

      // Format currency
      const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', { 
          style: 'currency', 
          currency: 'IDR',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(amount || 0);
      };

      // Calculate daily costs
      const dailyElectricity = (monthlyOperationalData.electricity_cost || 0) / workingDays;
      const dailyWater = (monthlyOperationalData.water_cost || 0) / workingDays;
      const dailyFuel = (monthlyOperationalData.fuel_cost || 0) / workingDays;
      const dailyOfficeBase = (monthlyOperationalData.office_salary_cost || 0) / workingDays;
      const dailyOffice = dailyOfficeBase * (officePercentage / 100); // Apply percentage
      
      // Update daily cost displays
      document.getElementById('dailyElectricity').textContent = `Per hari: ${formatCurrency(dailyElectricity)}`;
      document.getElementById('dailyWater').textContent = `Per hari: ${formatCurrency(dailyWater)}`;
      document.getElementById('dailyFuel').textContent = `Per hari: ${formatCurrency(dailyFuel)}`;
      document.getElementById('dailyOffice').textContent = `Per hari: ${formatCurrency(dailyOfficeBase)}`;
      document.getElementById('officeUsedAmount').textContent = `${officePercentage}%`;
      
      // Calculate total daily operational cost
      const totalDailyOperational = dailyElectricity + dailyWater + dailyFuel + dailyOffice;
      document.getElementById('totalAutoOperational').value = formatCurrency(totalDailyOperational);
      
      console.log('🔧 Auto operational costs calculated:', {
        dailyElectricity: dailyElectricity,
        dailyWater: dailyWater,
        dailyFuel: dailyFuel,
        dailyOffice: dailyOffice,
        totalDailyOperational: totalDailyOperational,
        workingDays: workingDays,
        officePercentage: officePercentage
      });
      
      // Clear existing operational cost rows
      clearOperationalCostRows();
      
      // Add auto-generated operational cost rows
      addAutoOperationalCostRow('Biaya Listrik (Harian)', dailyElectricity);
      addAutoOperationalCostRow('Biaya Air (Harian)', dailyWater);
      addAutoOperationalCostRow('Biaya Bahan Bakar (Harian)', dailyFuel);
      addAutoOperationalCostRow(`Biaya Gaji Office (${officePercentage}%)`, dailyOffice);
      
      console.log('🔧 Auto operational cost rows added to form');
      
      // Auto update HPP preview
      setTimeout(() => {
        console.log('🔧 Triggering HPP preview update after auto calculation');
        updateHppPreviewAuto();
      }, 100);
    }

    /**
     * Add auto-generated operational cost row
     */
    function addAutoOperationalCostRow(description, amount) {
      if (amount <= 0) return;
      
      const container = document.getElementById('operationalCosts');
      const rowIndex = container.children.length;
      
      console.log(`🔧 Adding auto operational cost row: ${description} = ${amount}`);
      
      const row = document.createElement('div');
      row.className = 'flex gap-2 items-center auto-operational-row';
      row.innerHTML = `
        <div class="flex-1">
          <input type="text" name="operational_costs[${rowIndex}][description]" 
                 value="${description}" readonly
                 class="w-full rounded-lg border border-slate-200 px-3 py-2 bg-blue-50 text-sm">
        </div>
        <div class="w-32">
          <input type="number" name="operational_costs[${rowIndex}][amount]" 
                 value="${amount.toFixed(2)}" readonly
                 class="w-full rounded-lg border border-slate-200 px-3 py-2 bg-blue-50 text-sm text-right">
        </div>
        <div class="w-8 flex justify-center">
          <i class='bx bx-check-circle text-blue-600' title="Auto dari biaya bulanan"></i>
        </div>
      `;
      
      container.appendChild(row);
      
      console.log(`🔧 Auto operational cost row added with name: operational_costs[${rowIndex}][amount] = ${amount.toFixed(2)}`);
    }

    /**
     * Clear auto-generated operational cost rows
     */
    function clearOperationalCostRows() {
      const container = document.getElementById('operationalCosts');
      const autoRows = container.querySelectorAll('.auto-operational-row');
      autoRows.forEach(row => row.remove());
    }

    /**
     * Clear auto operational section
     */
    function clearAutoOperational() {
      const autoSection = document.getElementById('autoOperationalSection');
      autoSection.classList.add('hidden');
      
      // Clear data
      monthlyOperationalData = null;
      
      // Clear form
      document.getElementById('workingDays').value = '';
      document.getElementById('officePercentage').value = '30';
      document.getElementById('totalAutoOperational').value = '';
      
      // Hide breakdown
      const breakdown = document.getElementById('monthlyCostBreakdown');
      breakdown.classList.add('hidden');
      
      // Clear auto-generated rows
      clearOperationalCostRows();
      
      // Auto update HPP preview after clearing
      setTimeout(() => {
        updateHppPreviewAuto();
      }, 100);
    }

    // ===== END OPERATIONAL COSTS AUTO CALCULATION FUNCTIONS =====
    
    /**
     * Auto update HPP preview - compatible with existing system
     */
    function updateHppPreviewAuto() {
      console.log('🔄 Triggering HPP preview update after auto operational cost calculation');
      
      // Primary method: call the main HPP calculation function
      if (typeof calculateHppPreview === 'function') {
        calculateHppPreview();
        return;
      }
      
      // Fallback 1: Try alternative HPP update functions
      if (typeof updateHppPreview === 'function') {
        updateHppPreview();
        return;
      }
      
      // Fallback 2: Trigger change events on operational cost inputs to update preview
      const operationalInputs = document.querySelectorAll('#operationalCosts input[type="number"]');
      if (operationalInputs.length > 0) {
        // Trigger change on the first operational cost input to update calculations
        const event = new Event('change', { bubbles: true });
        operationalInputs[0].dispatchEvent(event);
        
        // Also trigger input event for real-time updates
        const inputEvent = new Event('input', { bubbles: true });
        operationalInputs[0].dispatchEvent(inputEvent);
      }
      
      // Fallback 3: Trigger change on target quantity to force recalculation
      const targetQuantity = document.querySelector('input[name="target_quantity"]');
      if (targetQuantity && targetQuantity.value) {
        const event = new Event('change', { bubbles: true });
        targetQuantity.dispatchEvent(event);
      }
      
      // Fallback 4: Try other global HPP calculation functions
      if (typeof updatePreviewHpp === 'function') {
        updatePreviewHpp();
      } else if (typeof recalculateHpp === 'function') {
        recalculateHpp();
      }
    }
    
    // ===== END AUTO HPP UPDATE FUNCTION =====
    
  </script>

  <!-- Include existing modal and JavaScript for compatibility -->
  <script src="<?php echo e(asset('js/production.js')); ?>?v=<?php echo e(time()); ?>&debug=1" onerror="console.warn('production.js not found')"></script>
  
  <!-- Remove problematic fix_addmaterial_function.js and use inline fallback -->
  <script>
    // Inline addMaterial function to avoid 404 errors
    console.log('🔧 Loading addMaterial function inline...');
    
    // Define the addMaterial function directly
    window.addMaterial = function () {
        console.log('🔧 addMaterial function called');
        
        const container = document.getElementById("materialRequirements");
        if (!container) {
            console.error('❌ materialRequirements container not found');
            return;
        }
        
        const index = container.children.length;
        console.log('📝 Adding material at index:', index);
        
        const newRow = document.createElement("div");
        newRow.className = "material-row bg-slate-50 rounded-lg p-3 space-y-3";
        newRow.innerHTML = 
            '<input type="hidden" name="materials[' + index + '][material_type]" value="bahan">' +
            '<div class="flex items-center gap-3">' +
                '<select name="materials[' + index + '][material_id]" ' +
                        'class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                        'onchange="updateMaterialUnit(this, ' + index + '); calculateHppPreview();" required>' +
                    '<option value="">Pilih Material</option>' +
                '</select>' +
                '<input type="number" name="materials[' + index + '][quantity]" min="0.01" step="0.01" ' +
                       'class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                       'placeholder="Qty" onchange="calculateHppPreview();" ' +
                       'oninput="calculateHppPreview();" required>' +
                '<input type="text" name="materials[' + index + '][unit]" readonly ' +
                       'class="w-24 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 focus:outline-none" ' +
                       'placeholder="Unit">' +
                '<button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">' +
                    '<i class="bx bx-trash"></i>' +
                '</button>' +
            '</div>';

        container.appendChild(newRow);
        console.log('✅ New material row added');
        
        // Get the new select element
        const newSelect = newRow.querySelector('select[name*="material_id"]');
        
        // Try to populate from existing state first
        if (newSelect && window.state && window.state.materials && window.state.materials.length > 0) {
            console.log('📋 Populating select with', window.state.materials.length, 'materials from state');
            populateSelectWithMaterialsSync(newSelect, window.state.materials);
        } else {
            // Load materials asynchronously
            console.log('🔄 Loading materials for new select...');
            loadMaterialsForSelect(newSelect);
        }
    };

    // Synchronous function to populate select with materials
    function populateSelectWithMaterialsSync(selectElement, materials) {
        // Clear existing options except the first one
        while (selectElement.options.length > 1) {
            selectElement.removeChild(selectElement.lastChild);
        }
        
        // Add materials as options
        materials.forEach(material => {
            const option = document.createElement('option');
            option.value = material.id;
            option.textContent = material.name + " (Stok: " + material.stock + " " + material.unit + ")";
            option.dataset.type = material.type;
            option.dataset.unit = material.unit;
            selectElement.appendChild(option);
        });
        
        console.log(`✅ Populated select with ${materials.length} materials`);
    }

    // Asynchronous function to load and populate materials
    async function loadMaterialsForSelect(selectElement) {
        try {
            const outletSelect = document.getElementById('outletSelect');
            const outletId = outletSelect ? outletSelect.value : null;
            
            if (!outletId) {
                console.warn('⚠️ No outlet selected, cannot load materials');
                return;
            }

            console.log('🔄 Fetching materials for outlet:', outletId);
            
            // Use the materials API
            const response = await fetch(`${materialsUrl}?outlet_id=${outletId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📦 Materials API response:', data);
            
            if (data.success && Array.isArray(data.data)) {
                // Store in global state for future use
                if (!window.state) {
                    window.state = {};
                }
                window.state.materials = data.data;
                
                // Populate the select
                populateSelectWithMaterialsSync(selectElement, data.data);
                
                console.log('✅ Materials loaded and populated:', data.data.length, 'items');
            } else {
                console.error('❌ Invalid materials response:', data);
            }
        } catch (error) {
            console.error('❌ Error loading materials for select:', error);
            
            // Show error in select
            const errorOption = document.createElement('option');
            errorOption.value = '';
            errorOption.textContent = 'Error loading materials';
            errorOption.disabled = true;
            selectElement.appendChild(errorOption);
        }
    }

    // Define removeMaterial function
    window.removeMaterial = function (button) {
        console.log('🗑️ removeMaterial function called');
        
        const row = button.closest('.material-row');
        const container = document.getElementById("materialRequirements");
        
        if (!container) {
            console.error('❌ materialRequirements container not found');
            return;
        }
        
        if (container.children.length > 1) {
            row.remove();
            console.log('✅ Material row removed');
        } else {
            console.warn('⚠️ Cannot remove last material row');
            alert('Minimal harus ada satu material');
        }
    };

    // Define updateMaterialUnit function
    window.updateMaterialUnit = function (select, index) {
        console.log('🔄 updateMaterialUnit function called for index:', index);
        
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.unit) {
            const unitInput = document.querySelector('input[name="materials[' + index + '][unit]"]');
            const typeInput = document.querySelector('input[name="materials[' + index + '][material_type]"]');
            
            if (unitInput) {
                unitInput.value = selectedOption.dataset.unit;
                console.log('✅ Unit updated to:', selectedOption.dataset.unit);
            }
            
            if (typeInput && selectedOption.dataset.type) {
                typeInput.value = selectedOption.dataset.type;
                console.log('✅ Type updated to:', selectedOption.dataset.type);
            }
        }
    };
    
    console.log('✅ addMaterial functions loaded inline successfully');
    
    // Define URLs for existing JavaScript compatibility
    const productionDataUrl = "<?php echo e(route('admin.produksi.produksi.data')); ?>";
    const storeUrl = "<?php echo e(route('admin.produksi.produksi.store')); ?>";
    const updateUrl = "<?php echo e(route('admin.produksi.produksi.update', ':id')); ?>";
    const showUrl = "<?php echo e(route('admin.produksi.produksi.show', ':id')); ?>";
    const deleteUrl = "<?php echo e(route('admin.produksi.produksi.destroy', ':id')); ?>";
    const approveUrl = "<?php echo e(route('admin.produksi.produksi.approve', ':id')); ?>";
    const startUrl = "<?php echo e(route('admin.produksi.produksi.start', ':id')); ?>";
    const productsUrl = "<?php echo e(route('admin.produksi.produksi.products')); ?>";
    const materialsUrl = "<?php echo e(route('admin.produksi.produksi.materials')); ?>";
    const materialsFifoUrl = "<?php echo e(route('admin.produksi.produksi.materials.fifo', ':id')); ?>";
    const statisticsUrl = "<?php echo e(route('admin.produksi.produksi.statistics')); ?>";
    const addRealizationUrl = "<?php echo e(route('admin.produksi.produksi.realization', ':id')); ?>";
    const attendanceCountUrl = "<?php echo e(route('admin.produksi.produksi.attendance.count')); ?>";
    const hppPreviewUrl = "<?php echo e(route('admin.produksi.produksi.hpp.preview')); ?>";
    const pdfUrl = "<?php echo e(route('admin.produksi.produksi.pdf', ':id')); ?>";

    // ===== MULTI-PRODUCT FUNCTIONALITY =====
    
    let productRowIndex = 0;
    let availableProducts = [];

    /**
     * Load available products for selection
     */
    async function loadAvailableProducts() {
      try {
        const outletSelect = document.getElementById('outletSelect');
        const outletId = outletSelect ? outletSelect.value : null;
        
        if (!outletId) {
          console.log('No outlet selected, cannot load products');
          return;
        }

        console.log('Loading products for outlet:', outletId);
        
        // Use the existing productsUrl variable
        const response = await fetch(`${productsUrl}?outlet_id=${outletId}`);
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Products API response:', data);
        
        if (data.success && Array.isArray(data.data)) {
          availableProducts = data.data;
          console.log('Loaded products:', availableProducts.length, 'items');
          
          // Log first product structure for debugging
          if (availableProducts.length > 0) {
            console.log('First product structure:', availableProducts[0]);
          }
        } else {
          console.error('Invalid products response:', data);
          availableProducts = [];
        }
      } catch (error) {
        console.error('Error loading products:', error);
        availableProducts = [];
        
        // Show user-friendly error
        const resultsDiv = document.querySelector('[id^="productResults_"]');
        if (resultsDiv) {
          resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Gagal memuat data produk</div>';
          resultsDiv.classList.remove('hidden');
        }
      }
    }

    /**
     * Add a new product row
     */
    function addProductRow() {
      const container = document.getElementById('productRows');
      const rowIndex = productRowIndex++;
      
      const row = document.createElement('div');
      row.className = 'product-row bg-white border border-slate-200 rounded-lg p-4';
      row.dataset.index = rowIndex;
      
      row.innerHTML = `
        <div class="flex items-start gap-3">
          <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3">
            <!-- Product Selection -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-1">Produk</label>
              <div class="relative">
                <input type="text" 
                       id="productSearch_${rowIndex}" 
                       placeholder="Cari produk..." 
                       onkeyup="searchProduct(${rowIndex})"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200">
                <input type="hidden" name="products[${rowIndex}][product_id]" id="productId_${rowIndex}" required>
                <div id="productResults_${rowIndex}" class="absolute top-full left-0 right-0 bg-white border border-slate-200 rounded-lg mt-1 max-h-32 overflow-y-auto z-20 hidden"></div>
              </div>
            </div>
            
            <!-- Target Quantity -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Target Qty</label>
              <input type="number" 
                     name="products[${rowIndex}][target_quantity]" 
                     onchange="calculateTotalTargetQuantity()"
                     class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200" 
                     min="1" required>
            </div>
            
            <!-- Sample Quantity -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Qty Sampel (Opsional)</label>
              <input type="number" 
                     name="products[${rowIndex}][sample_quantity]" 
                     class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-200" 
                     min="0" placeholder="0">
              <div class="text-xs text-slate-500 mt-1">Stok akan dikurangi</div>
            </div>
          </div>
          
          <!-- Remove Button -->
          <button type="button" 
                  onclick="removeProductRow(${rowIndex})" 
                  class="mt-6 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus Produk">
            <i class='bx bx-trash text-lg'></i>
          </button>
        </div>
      `;
      
      container.appendChild(row);
      
      // Load products if not loaded yet
      if (availableProducts.length === 0) {
        loadAvailableProducts();
      }
      
      // If this is the first row, make it required
      if (container.children.length === 1) {
        row.querySelector('button[onclick*="removeProductRow"]').style.display = 'none';
      }
    }

    /**
     * Remove a product row
     */
    function removeProductRow(index) {
      const container = document.getElementById('productRows');
      const row = container.querySelector(`[data-index="${index}"]`);
      
      if (row && container.children.length > 1) {
        row.remove();
        calculateTotalTargetQuantity();
        
        // Show remove button for remaining rows if more than 1
        const remainingRows = container.querySelectorAll('.product-row');
        remainingRows.forEach((row, idx) => {
          const removeBtn = row.querySelector('button[onclick*="removeProductRow"]');
          if (removeBtn) {
            removeBtn.style.display = remainingRows.length > 1 ? 'block' : 'none';
          }
        });
      }
    }

    /**
     * Search products for a specific row
     */
    function searchProduct(rowIndex) {
      const searchInput = document.getElementById(`productSearch_${rowIndex}`);
      const resultsDiv = document.getElementById(`productResults_${rowIndex}`);
      
      if (!searchInput || !resultsDiv) {
        console.error('Search elements not found for row:', rowIndex);
        return;
      }
      
      const query = searchInput.value.toLowerCase();
      
      if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
      }
      
      // Ensure availableProducts is an array and has data
      if (!Array.isArray(availableProducts) || availableProducts.length === 0) {
        console.log('No products available, loading...');
        loadAvailableProducts().then(() => {
          // Retry search after loading
          if (availableProducts.length > 0) {
            searchProduct(rowIndex);
          }
        });
        return;
      }
      
      const filteredProducts = availableProducts.filter(product => {
        if (!product) return false;
        
        // Use the correct property names from the API response
        const productName = product.name || '';
        const productCode = product.code || '';
        
        return productName.toLowerCase().includes(query) ||
               productCode.toLowerCase().includes(query);
      });
      
      if (filteredProducts.length > 0) {
        resultsDiv.innerHTML = filteredProducts.map(product => {
          // Use the correct property names from the API response
          const productName = product.name || 'Unknown Product';
          const productCode = product.code || 'No Code';
          const productId = product.id || 0;
          const productStock = product.stock || 0;
          
          // Escape quotes for onclick function
          const escapedName = productName.replace(/'/g, "\\'");
          const escapedCode = productCode.replace(/'/g, "\\'");
          
          return `
            <div class="px-3 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-b-0" 
                 onclick="selectProduct(${rowIndex}, ${productId}, '${escapedName}', '${escapedCode}')">
              <div class="font-medium text-sm">${productName}</div>
              <div class="text-xs text-slate-500">${productCode} • Stok: ${productStock}</div>
            </div>
          `;
        }).join('');
        resultsDiv.classList.remove('hidden');
      } else {
        resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-slate-500">Produk tidak ditemukan</div>';
        resultsDiv.classList.remove('hidden');
      }
    }

    /**
     * Select a product for a specific row
     */
    function selectProduct(rowIndex, productId, productName, productCode) {
      console.log('Selecting product:', { rowIndex, productId, productName, productCode });
      
      const searchInput = document.getElementById(`productSearch_${rowIndex}`);
      const productIdInput = document.getElementById(`productId_${rowIndex}`);
      const resultsDiv = document.getElementById(`productResults_${rowIndex}`);
      
      if (searchInput) {
        searchInput.value = `${productName} (${productCode})`;
        // Reset error styling when product is selected
        searchInput.style.borderColor = '';
        searchInput.style.backgroundColor = '';
      }
      
      if (productIdInput) {
        productIdInput.value = productId;
        console.log('✅ Product ID set:', productId);
      }
      
      if (resultsDiv) {
        resultsDiv.classList.add('hidden');
      }
      
      // Trigger validation and calculations
      calculateTotalTargetQuantity();
    }

    /**
     * Calculate total target quantity from all product rows
     */
    function calculateTotalTargetQuantity() {
      const container = document.getElementById('productRows');
      const quantityInputs = container.querySelectorAll('input[name*="target_quantity"]');
      let total = 0;
      
      quantityInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
      });
      
      const totalInput = document.getElementById('totalTargetQuantity');
      if (totalInput) {
        totalInput.value = total;
      }
      
      // Trigger HPP preview calculation
      if (typeof calculateHppPreview === 'function') {
        calculateHppPreview();
      }
    }

    // ===== ADVANCED BUSINESS TYPE FUNCTIONALITY =====

    /**
     * Toggle business-specific forms based on selected business type
     */
    function toggleBusinessSpecificForms() {
      const businessTypeSelect = document.getElementById('businessTypeSelect');
      const tofuForms = document.getElementById('tofuSpecificForms');
      
      if (businessTypeSelect.value === 'tofu') {
        tofuForms.classList.remove('hidden');
      } else {
        tofuForms.classList.add('hidden');
        // Clear tofu form data when hidden
        clearTofuFormData();
      }
    }

    /**
     * Clear tofu form data
     */
    function clearTofuFormData() {
      const tofuInputs = document.querySelectorAll('#tofuSpecificForms input');
      tofuInputs.forEach(input => {
        if (!input.readOnly) {
          input.value = '';
        }
      });
    }

    /**
     * Calculate filling total automatically
     */
    function calculateFillingTotal() {
      const mesin1 = parseFloat(document.querySelector('input[name="tofu_data[filling_mesin1]"]')?.value) || 0;
      const mesin2 = parseFloat(document.querySelector('input[name="tofu_data[filling_mesin2]"]')?.value) || 0;
      const total = mesin1 + mesin2;
      
      const totalInput = document.getElementById('fillingTotal');
      if (totalInput) {
        totalInput.value = total;
      }
    }

    // ===== GLOBAL FUNCTIONS =====
    
    /**
     * Global function to download production PDF
     */
    window.downloadProductionPdf = function(productionId) {
      const url = `<?php echo e(route('admin.produksi.produksi.pdf', ':id')); ?>`.replace(':id', productionId);
      window.open(url, '_blank');
      
      // Close modal if open
      const modal = document.getElementById('downloadOptionsModal');
      if (modal) {
        modal.remove();
        document.body.style.overflow = 'auto';
      }
    };

    /**
     * Global function to download QC Tofu PDF
     */
    window.downloadQcTofuPdf = function(productionId) {
      const url = `<?php echo e(route('admin.produksi.produksi.qc-tofu-pdf', ':id')); ?>`.replace(':id', productionId);
      window.open(url, '_blank');
      
      // Close modal if open
      const modal = document.getElementById('downloadOptionsModal');
      if (modal) {
        modal.remove();
        document.body.style.overflow = 'auto';
      }
    };

    // ===== INITIALIZATION AND EVENT LISTENERS =====

    /**
     * Initialize multi-product functionality
     */
    function initializeMultiProduct() {
      // Add first product row
      addProductRow();
      
      // Load available products when outlet changes
      const outletSelect = document.getElementById('outletSelect');
      if (outletSelect) {
        outletSelect.addEventListener('change', function() {
          availableProducts = [];
          loadAvailableProducts();
        });
      }
      
      // Add event listeners for tofu filling calculation
      document.addEventListener('input', function(e) {
        if (e.target.name === 'tofu_data[filling_mesin1]' || e.target.name === 'tofu_data[filling_mesin2]') {
          calculateFillingTotal();
        }
      });
      
      // Hide product results when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
          document.querySelectorAll('[id^="productResults_"]').forEach(div => {
            div.classList.add('hidden');
          });
        }
      });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize multi-product functionality
      initializeMultiProduct();
      
      // Override existing openCreate function to initialize multi-product
      const originalOpenCreate = window.openCreate;
      window.openCreate = function() {
        console.log('Opening create modal with multi-product support');
        
        if (originalOpenCreate) {
          originalOpenCreate();
        } else {
          // Fallback: open modal directly
          const modal = document.getElementById('createModal');
          if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
          }
        }
        
        // Reset and initialize multi-product
        const container = document.getElementById('productRows');
        if (container) {
          container.innerHTML = '';
          productRowIndex = 0;
          addProductRow();
        }
        
        // Load products for current outlet
        setTimeout(() => {
          loadAvailableProducts();
        }, 100);
      };
      
      // Also override the Alpine.js openCreate method
      setTimeout(() => {
        const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
        if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
          const component = alpineComponent._x_dataStack[0];
          const originalOpenCreate = component.openCreate;
          
          component.openCreate = function() {
            console.log('Alpine openCreate called');
            
            // Call original function first
            if (originalOpenCreate) {
              originalOpenCreate.call(this);
            }
            
            // Then initialize multi-product
            setTimeout(() => {
              const container = document.getElementById('productRows');
              if (container) {
                container.innerHTML = '';
                productRowIndex = 0;
                addProductRow();
              }
              
              loadAvailableProducts();
            }, 200);
          };
        }
      }, 1000);
    });

    // ===== END MULTI-PRODUCT FUNCTIONALITY =====

    // ===== REALIZATION MODAL FUNCTIONALITY =====

    /**
     * Close realization modal
     */
    function closeRealizationModal() {
      const modal = document.getElementById('realizationModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Clear form data
        const form = document.getElementById('realizationForm');
        if (form) {
          form.reset();
          form.removeAttribute('data-production-id');
        }
        
        // Clear product realization rows
        const container = document.getElementById('productRealizationRows');
        if (container) {
          container.innerHTML = '';
        }
      }
    }

    /**
     * Handle realization form submission
     */
    document.addEventListener('DOMContentLoaded', function() {
      const realizationForm = document.getElementById('realizationForm');
      if (realizationForm) {
        realizationForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          const productionId = this.dataset.productionId;
          if (!productionId) {
            alert('Production ID tidak ditemukan');
            return;
          }
          
          // Collect form data
          const formData = new FormData(this);
          const products = [];
          
          // Get all product realization data
          const productInputs = document.querySelectorAll('input[name*="products["]');
          const productData = {};
          
          productInputs.forEach(input => {
            const match = input.name.match(/products\[(\d+)\]\[(\w+)\]/);
            if (match) {
              const index = match[1];
              const field = match[2];
              
              if (!productData[index]) {
                productData[index] = {};
              }
              
              // Convert to appropriate type
              if (field === 'product_id' || field === 'hpp_record_id' || field === 'quantity_produced' || field === 'quantity_rejected') {
                productData[index][field] = parseInt(input.value) || 0;
              } else {
                productData[index][field] = input.value || '';
              }
            }
          });
          
          console.log('Collected product data:', productData);
          
          // Convert to array and filter valid products
          Object.values(productData).forEach(product => {
            // Ensure required fields are present and valid
            if (product.product_id && product.hpp_record_id) {
              const quantityProduced = parseInt(product.quantity_produced) || 0;
              const quantityRejected = parseInt(product.quantity_rejected) || 0;
              
              // At least one quantity must be greater than 0
              if (quantityProduced > 0 || quantityRejected > 0) {
                products.push({
                  product_id: parseInt(product.product_id),
                  hpp_record_id: parseInt(product.hpp_record_id),
                  quantity_produced: quantityProduced,
                  quantity_rejected: quantityRejected,
                  notes: (product.notes || '').trim()
                });
              }
            }
          });
          
          if (products.length === 0) {
            alert('Minimal harus ada satu produk dengan jumlah produksi > 0');
            return;
          }
          
          // Validate each product has minimum quantity
          const invalidProducts = products.filter(p => p.quantity_produced <= 0 && p.quantity_rejected <= 0);
          if (invalidProducts.length > 0) {
            alert('Setiap produk harus memiliki jumlah produksi atau reject > 0');
            return;
          }
          
          // Prepare request data
          const requestData = {
            products: products,
            notes: (formData.get('notes') || '').trim(),
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
          };
          
          console.log('Submitting realization data:', requestData);
          console.log('Products to submit:', products);
          
          try {
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : 'Tambah Realisasi';
            if (submitBtn) {
              submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin mr-2"></i>Menyimpan...';
              submitBtn.disabled = true;
            }
            
            // Submit to server
            const url = addRealizationUrl.replace(':id', productionId);
            const response = await fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': requestData._token,
                'Accept': 'application/json'
              },
              body: JSON.stringify(requestData)
            });
            
            const result = await response.json();
            
            console.log('Server response:', result);
            
            if (result.success) {
              // Show success message
              alert('Realisasi berhasil ditambahkan');
              
              // Close modal
              closeRealizationModal();
              
              // Refresh data
              const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
              if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                const component = alpineComponent._x_dataStack[0];
                if (component.fetchData) {
                  component.fetchData();
                }
              }
            } else {
              // Show error message
              let errorMessage = result.message || 'Gagal menambahkan realisasi';
              
              if (result.errors) {
                console.error('Validation errors:', result.errors);
                const errorList = Object.values(result.errors).flat();
                errorMessage += ':\n' + errorList.join('\n');
              }
              
              console.error('Realization submission failed:', result);
              alert(errorMessage);
            }
            
          } catch (error) {
            console.error('Error submitting realization:', error);
            
            // Check if it's a network error or parsing error
            if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
              alert('Koneksi ke server gagal. Periksa koneksi internet Anda.');
            } else if (error instanceof SyntaxError) {
              alert('Server mengembalikan response yang tidak valid. Silakan coba lagi.');
            } else {
              alert('Terjadi kesalahan saat menyimpan realisasi: ' + error.message);
            }
          } finally {
            // Reset submission flag
            this.dataset.submitting = 'false';
            
            // Restore button state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
            }
          }
        });
      }
    });

    // ===== END REALIZATION MODAL FUNCTIONALITY =====

    // ===== DATE FORMAT HANDLING =====
    
    /**
     * Initialize date inputs with Indonesian locale and fix double overlay
     */
    document.addEventListener('DOMContentLoaded', function() {
      // Set locale for all date inputs and handle overlay visibility
      const dateInputs = document.querySelectorAll('input[type="date"]');
      dateInputs.forEach(input => {
        // Handle overlay visibility based on input value
        const updateOverlay = () => {
          const overlay = input.nextElementSibling;
          if (overlay && overlay.classList.contains('date-format-overlay')) {
            if (input.value && input.value !== '') {
              overlay.style.opacity = '0';
              overlay.style.visibility = 'hidden';
            } else {
              overlay.style.opacity = '1';
              overlay.style.visibility = 'visible';
            }
          }
        };
        
        // Add event listeners
        input.addEventListener('change', function() {
          updateOverlay();
          if (this.value) {
            console.log('Date selected:', this.value);
            // The value will always be in YYYY-MM-DD format for submission
            // But we can add visual feedback
            const dateObj = new Date(this.value + 'T00:00:00');
            const formattedDate = dateObj.toLocaleDateString('id-ID', {
              day: '2-digit',
              month: '2-digit', 
              year: 'numeric'
            });
            console.log('Formatted for display:', formattedDate);
          }
        });
        
        input.addEventListener('focus', updateOverlay);
        input.addEventListener('blur', updateOverlay);
        input.addEventListener('input', updateOverlay);
        
        // Initial check
        updateOverlay();
        
        // Add placeholder behavior (legacy support)
        if (!input.value) {
          input.classList.add('placeholder-shown');
        }
        
        input.addEventListener('focus', function() {
          this.classList.remove('placeholder-shown');
        });
        
        input.addEventListener('blur', function() {
          if (!this.value) {
            this.classList.add('placeholder-shown');
          }
        });
      });
    });
    
    // ===== END DATE FORMAT HANDLING =====

    // ===== PRODUCTION FORM SUBMISSION HANDLER =====
    
    /**
     * Handle production form submission with loading indicator and date format fix
     */
    document.addEventListener('DOMContentLoaded', function() {
      const productionForm = document.getElementById('productionForm');
      if (productionForm) {
      // Prevent multiple event listener registration
      if (productionForm.dataset.listenerAdded === "true") {
        console.log("Production form listener already added, skipping...");
        return;
      }
      productionForm.dataset.listenerAdded = "true";
      
        productionForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          // Prevent double submission
          if (this.dataset.submitting === 'true') {
            console.log('Form already being submitted, ignoring...');
            return;
          }
          this.dataset.submitting = 'true';
          
          console.log('Production form submitted');
          
          // Show loading state
          const submitBtn = document.getElementById('submitProductionBtn');
          const submitBtnText = document.getElementById('submitBtnText');
          const submitBtnLoader = document.getElementById('submitBtnLoader');
          
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
          }
          
          if (submitBtnText) {
            submitBtnText.textContent = 'Menyimpan...';
          }
          
          if (submitBtnLoader) {
            submitBtnLoader.classList.remove('hidden');
          }
          
          try {
            // Get form data
            const formData = new FormData(this);
            
            // CRITICAL: Validate products before submission
            const productIdInputs = document.querySelectorAll('input[name*="[product_id]"]');
            const emptyProducts = [];
            
            productIdInputs.forEach((input, index) => {
              if (!input.value || input.value.trim() === '') {
                emptyProducts.push(index + 1);
                // Highlight the empty field
                const productRow = input.closest('.product-row');
                if (productRow) {
                  const searchInput = productRow.querySelector('input[type="text"]');
                  if (searchInput) {
                    searchInput.style.borderColor = '#ef4444';
                    searchInput.style.backgroundColor = '#fef2f2';
                  }
                }
              }
            });
            
            if (emptyProducts.length > 0) {
              throw new Error(`Produk belum dipilih pada baris: ${emptyProducts.join(', ')}. Silakan pilih produk terlebih dahulu.`);
            }
            
            // Fix date format - ensure dates are in correct format
            const startDate = formData.get('start_date');
            const endDate = formData.get('end_date');
            const expiryDate = formData.get('expiry_date');
            
            // Validate dates
            if (startDate && endDate) {
              const start = new Date(startDate);
              const end = new Date(endDate);
              
              if (start > end) {
                throw new Error('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
              }
            }
            
            // Convert FormData to JSON for better handling
            const jsonData = {};
            
            // Handle regular fields
            for (let [key, value] of formData.entries()) {
              if (key.includes('[') && key.includes(']')) {
                // Handle array fields (products, materials, etc.)
                const matches = key.match(/^(\w+)\[(\d+)\]\[(\w+)\]$/);
                if (matches) {
                  const [, arrayName, index, fieldName] = matches;
                  if (!jsonData[arrayName]) jsonData[arrayName] = {};
                  if (!jsonData[arrayName][index]) jsonData[arrayName][index] = {};
                  jsonData[arrayName][index][fieldName] = value;
                } else {
                  // Handle nested objects like labor_costs[worker_count]
                  const nestedMatches = key.match(/^(\w+)\[(\w+)\]$/);
                  if (nestedMatches) {
                    const [, objectName, fieldName] = nestedMatches;
                    if (!jsonData[objectName]) jsonData[objectName] = {};
                    jsonData[objectName][fieldName] = value;
                  } else {
                    jsonData[key] = value;
                  }
                }
              } else {
                jsonData[key] = value;
              }
            }
            
            // Convert array objects to arrays and clean empty values
            ['products', 'materials', 'operational_costs'].forEach(arrayName => {
              if (jsonData[arrayName] && typeof jsonData[arrayName] === 'object') {
                const arrayValues = Object.values(jsonData[arrayName]);
                
                // Filter out empty operational costs
                if (arrayName === 'operational_costs') {
                  jsonData[arrayName] = arrayValues.filter(cost => {
                    return cost.amount && parseFloat(cost.amount) > 0 && 
                           (cost.cost_type || cost.description);
                  });
                } else {
                  jsonData[arrayName] = arrayValues;
                }
              }
            });
            
            // Handle tofu_data if present
            const tofuData = {};
            let hasTofuData = false;
            for (let [key, value] of formData.entries()) {
              if (key.startsWith('tofu_data[') && key.endsWith(']')) {
                const fieldName = key.match(/tofu_data\[(\w+)\]/)[1];
                if (value && value.trim() !== '') {
                  tofuData[fieldName] = value;
                  hasTofuData = true;
                }
              }
            }
            if (hasTofuData) {
              jsonData.tofu_data = tofuData;
            }
            
            console.log('Prepared JSON data:', jsonData);
            
            // Determine if this is edit or create
            const isEdit = this.dataset.editMode === 'true';
            const productionId = this.dataset.productionId;
            
            let url, method;
            if (isEdit && productionId) {
              url = updateUrl.replace(':id', productionId);
              method = 'PUT';
              console.log('Updating production:', productionId);
            } else {
              url = storeUrl;
              method = 'POST';
              console.log('Creating new production');
            }
            
            // Submit to server
            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || jsonData._token,
                'Accept': 'application/json'
              },
              body: JSON.stringify(jsonData)
            });
            
            const result = await response.json();
            console.log('Server response:', result);
            
            if (result.success) {
              // Show success message
              const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
              if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                const component = alpineComponent._x_dataStack[0];
                if (component.showToastMessage) {
                  component.showToastMessage(result.message || 'Produksi berhasil disimpan', 'success');
                }
              } else {
                alert(result.message || 'Produksi berhasil disimpan');
              }
              
              // Close modal
              closeCreateModal();
              
              // Refresh data
              if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                const component = alpineComponent._x_dataStack[0];
                if (component.fetchData) {
                  await component.fetchData();
                }
                if (component.fetchStats) {
                  await component.fetchStats();
                }
              }
              
            } else {
              // Show error message
              let errorMessage = result.message || 'Gagal menyimpan produksi';
              
              if (result.user_friendly_errors && result.user_friendly_errors.length > 0) {
                // Use user-friendly error messages
                errorMessage = result.user_friendly_errors.join('\n');
              } else if (result.errors) {
                console.error('Validation errors:', result.errors);
                const errorList = Object.values(result.errors).flat();
                errorMessage += ':\n' + errorList.join('\n');
              }
              
              console.error('Production submission failed:', result);
              alert(errorMessage);
              
              // Highlight fields with errors if available
              if (result.errors) {
                Object.keys(result.errors).forEach(fieldName => {
                  if (fieldName.includes('products.') && fieldName.includes('product_id')) {
                    // Extract product index and highlight the field
                    const match = fieldName.match(/products\.(\d+)\.product_id/);
                    if (match) {
                      const productIndex = parseInt(match[1]);
                      const searchInput = document.getElementById(`productSearch_${productIndex}`);
                      if (searchInput) {
                        searchInput.style.borderColor = '#ef4444';
                        searchInput.style.backgroundColor = '#fef2f2';
                        searchInput.focus();
                      }
                    }
                  }
                });
              }
            }
            
          } catch (error) {
            console.error('Error submitting production:', error);
            
            // Show user-friendly error message
            let errorMessage = 'Terjadi kesalahan saat menyimpan produksi';
            if (error.message) {
              errorMessage += ': ' + error.message;
            }
            alert(errorMessage);
            
          } finally {
            // Reset submission flag
            this.dataset.submitting = 'false';
            
            // Restore button state
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
            
            if (submitBtnText) {
              const isEdit = this.dataset.editMode === 'true';
              submitBtnText.textContent = isEdit ? 'Update Produksi' : 'Simpan Produksi';
            }
            
            if (submitBtnLoader) {
              submitBtnLoader.classList.add('hidden');
            }
          }
        });
      }
    });
    
    /**
     * Close create modal and reset form
     */
    function closeCreateModal() {
      const modal = document.getElementById('createModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Reset form
        const form = document.getElementById('productionForm');
        if (form) {
          form.reset();
          form.removeAttribute('data-edit-mode');
          form.removeAttribute('data-production-id');
        }
        
        // Reset modal title and button text
        const modalTitle = document.querySelector('#createModal .font-semibold');
        if (modalTitle) {
          modalTitle.textContent = 'Buat Produksi Baru';
        }
        
        const submitBtnText = document.getElementById('submitBtnText');
        if (submitBtnText) {
          submitBtnText.textContent = 'Simpan Produksi';
        }
        
        // Clear dynamic content
        const productRows = document.getElementById('productRows');
        if (productRows) {
          productRows.innerHTML = '';
        }
        
        const materialRequirements = document.getElementById('materialRequirements');
        if (materialRequirements) {
          materialRequirements.innerHTML = '';
        }
        
        const operationalCosts = document.getElementById('operationalCosts');
        if (operationalCosts) {
          operationalCosts.innerHTML = '';
        }
      }
    }
    
    // ===== END PRODUCTION FORM SUBMISSION HANDLER =====
  
    // Add global click protection
    let isSubmitting = false;
    
    // Override form submission to add global protection
    const originalSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        if (this.id === "productionForm" && isSubmitting) {
            console.log("Global submission protection: Form already being submitted");
            return false;
        }
        
        if (this.id === "productionForm") {
            isSubmitting = true;
            setTimeout(() => { isSubmitting = false; }, 3000); // Reset after 3 seconds
        }
        
        return originalSubmit.call(this);
    };
    
    // Add visual feedback for button clicks
    document.addEventListener("click", function(e) {
        if (e.target.type === "submit" && e.target.form && e.target.form.id === "productionForm") {
            if (isSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Button click prevented: Form already being submitted");
                return false;
            }
        }
    }, true);

    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\index.blade.php ENDPATH**/ ?>