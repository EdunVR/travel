<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Package Detail']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Package Detail')]); ?>
    <style>
        [x-cloak] { display: none !important; }
        /* Ensure buttons are always clickable */
        button, a {
            position: relative;
            z-index: 1;
        }
        /* Ensure tabs and header buttons have higher z-index */
        nav[aria-label="Tabs"] button,
        .flex.gap-2 > a,
        .flex.gap-2 > button {
            position: relative;
            z-index: 10 !important;
        }
    </style>
    <div x-data="packageDetail(<?php echo e($package->id); ?>)" x-init="init()" class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="<?php echo e(route('admin.inventaris.travel.package.index')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-2">
                    <i class="bx bx-arrow-back"></i> Back to Packages
                </a>
                <h1 class="text-2xl font-bold" x-text="package.package_name"></h1>
                <p class="text-gray-600" x-text="package.package_code"></p>
            </div>
            <div class="flex gap-2">
                <button @click="exportPdf()" type="button" 
                        :disabled="exportingPdf"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bx" :class="exportingPdf ? 'bx-loader-alt bx-spin' : 'bx-download'"></i> 
                    <span x-text="exportingPdf ? 'Exporting...' : 'Export PDF'"></span>
                </button>
                <button @click="editPackage()" type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="bx bx-edit-alt"></i> Edit
                </button>
                <button @click="openHppModal()" type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="bx bx-calculator"></i> Manage HPP
                </button>
            </div>
        </div>

        <!-- Package Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="bx bx-calendar text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Departure Date</p>
                        <p class="font-semibold" x-text="formatDate(package.departure_date)"></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="bx bx-group text-2xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Capacity</p>
                        <p class="font-semibold">
                            <span x-text="package.capacity"></span> 
                            (<span x-text="package.available_seats"></span> tersedia)
                        </p>
                        <p class="text-xs text-slate-400">termasuk anggota keluarga</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="bx bx-money text-2xl text-purple-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600 mb-1">Price per Person</p>

                        <!-- Jika ada price_packages -->
                        <template x-if="package.price_packages && package.price_packages.length > 0">
                            <div class="space-y-2">
                                <template x-for="pkg in package.price_packages" :key="pkg.name">
                                    <div>
                                        <div class="text-xs font-semibold text-purple-700 mb-0.5" x-text="pkg.name"></div>
                                        <div class="flex flex-wrap gap-x-3 gap-y-0.5">
                                            <template x-for="v in (pkg.variants || [])" :key="v.type">
                                                <div class="flex items-center gap-1 text-xs">
                                                    <span class="capitalize text-slate-500" x-text="v.type + ':'"></span>
                                                    <span class="font-semibold text-purple-800" x-text="formatCurrency(parseFloat(v.price)||0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Fallback: single price -->
                        <template x-if="!package.price_packages || package.price_packages.length === 0">
                            <p class="font-semibold" x-text="formatCurrency(package.price)"></p>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="bx bx-calculator text-2xl text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">HPP Dasar</p>
                        <p class="font-semibold" x-text="formatCurrency(calculateHppPerPerson())"></p>
                        <p class="text-xs text-slate-400">per orang (tanpa hotel)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex gap-4 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'details'" type="button"
                            :class="activeTab === 'details' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        Details
                    </button>
                    <button @click="activeTab = 'keberangkatan'" type="button"
                            :class="activeTab === 'keberangkatan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        Keberangkatan
                    </button>
                    <button @click="activeTab = 'workflow'" type="button"
                            :class="activeTab === 'workflow' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        Workflow
                    </button>
                    <button @click="activeTab = 'hpp'" type="button"
                            :class="activeTab === 'hpp' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        HPP Dasar
                    </button>
                    <button @click="activeTab = 'materials'" type="button"
                            :class="activeTab === 'materials' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        Design Materials
                    </button>
                    <button @click="activeTab = 'tourplan'" type="button"
                            :class="activeTab === 'tourplan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm">
                        Tour Plan
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Details Tab -->
                <div x-show="activeTab === 'details'" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Package Type</label>
                            <p class="mt-1 text-gray-900" x-text="package.package_type?.toUpperCase()"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Duration</label>
                            <p class="mt-1 text-gray-900"><span x-text="package.duration_days"></span> days</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-gray-100 text-gray-800': package.status === 'draft',
                                          'bg-green-100 text-green-800': package.status === 'active',
                                          'bg-yellow-100 text-yellow-800': package.status === 'full',
                                          'bg-blue-100 text-blue-800': package.status === 'completed',
                                          'bg-red-100 text-red-800': package.status === 'cancelled'
                                      }"
                                      x-text="package.status?.toUpperCase()"></span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Outlet</label>
                            <p class="mt-1 text-gray-900" x-text="package.outlet_name"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-gray-900" x-text="package.description || '-'"></p>
                        </div>
                    </div>

                    <!-- Flight Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">✈️ Informasi Penerbangan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Departure Flight -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-900 mb-3">Keberangkatan</h4>
                                <template x-if="package.flight_departure">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="text-sm text-gray-600">Maskapai:</span>
                                            <p class="font-medium" x-text="package.flight_departure.airline_name"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Nomor Penerbangan:</span>
                                            <p class="font-medium" x-text="package.flight_departure.flight_number"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Rute:</span>
                                            <p class="font-medium" x-text="package.flight_departure.route"></p>
                                        </div>
                                        <div x-show="package.departure_datetime">
                                            <span class="text-sm text-gray-600">Waktu:</span>
                                            <p class="font-medium" x-text="formatDateTime(package.departure_datetime)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!package.flight_departure">
                                    <p class="text-gray-500 text-sm">Belum ditentukan</p>
                                </template>
                            </div>

                            <!-- Return Flight -->
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-900 mb-3">Kepulangan</h4>
                                <template x-if="package.flight_return">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="text-sm text-gray-600">Maskapai:</span>
                                            <p class="font-medium" x-text="package.flight_return.airline_name"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Nomor Penerbangan:</span>
                                            <p class="font-medium" x-text="package.flight_return.flight_number"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Rute:</span>
                                            <p class="font-medium" x-text="package.flight_return.route"></p>
                                        </div>
                                        <div x-show="package.return_datetime">
                                            <span class="text-sm text-gray-600">Waktu:</span>
                                            <p class="font-medium" x-text="formatDateTime(package.return_datetime)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!package.flight_return">
                                    <p class="text-gray-500 text-sm">Belum ditentukan</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Hotel Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">🏨 Informasi Akomodasi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hotel Makkah -->
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-900 mb-3">🕋 Hotel Mekkah</h4>
                                <template x-if="package.hotel_makkah">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="text-sm text-gray-600">Nama Hotel:</span>
                                            <p class="font-medium" x-text="package.hotel_makkah.hotel_name"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Bintang:</span>
                                            <p class="font-medium"><span x-text="package.hotel_makkah.star_rating"></span> ⭐</p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Lokasi:</span>
                                            <p class="font-medium" x-text="package.hotel_makkah.location"></p>
                                        </div>
                                        <div x-show="package.hotel_room_type_makkah">
                                            <span class="text-sm text-gray-600">Tipe Kamar:</span>
                                            <p class="font-medium" x-text="package.hotel_room_type_makkah?.room_type_name"></p>
                                        </div>
                                        <div x-show="package.makkah_check_in && package.makkah_check_out">
                                            <span class="text-sm text-gray-600">Check-in / Check-out:</span>
                                            <p class="font-medium">
                                                <span x-text="formatDate(package.makkah_check_in)"></span> - 
                                                <span x-text="formatDate(package.makkah_check_out)"></span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!package.hotel_makkah">
                                    <p class="text-gray-500 text-sm">Belum ditentukan</p>
                                </template>
                            </div>

                            <!-- Hotel Madinah -->
                            <div class="bg-teal-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-teal-900 mb-3">🕌 Hotel Madinah</h4>
                                <template x-if="package.hotel_madinah">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="text-sm text-gray-600">Nama Hotel:</span>
                                            <p class="font-medium" x-text="package.hotel_madinah.hotel_name"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Bintang:</span>
                                            <p class="font-medium"><span x-text="package.hotel_madinah.star_rating"></span> ⭐</p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Lokasi:</span>
                                            <p class="font-medium" x-text="package.hotel_madinah.location"></p>
                                        </div>
                                        <div x-show="package.hotel_room_type_madinah">
                                            <span class="text-sm text-gray-600">Tipe Kamar:</span>
                                            <p class="font-medium" x-text="package.hotel_room_type_madinah?.room_type_name"></p>
                                        </div>
                                        <div x-show="package.madinah_check_in && package.madinah_check_out">
                                            <span class="text-sm text-gray-600">Check-in / Check-out:</span>
                                            <p class="font-medium">
                                                <span x-text="formatDate(package.madinah_check_in)"></span> - 
                                                <span x-text="formatDate(package.madinah_check_out)"></span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!package.hotel_madinah">
                                    <p class="text-gray-500 text-sm">Belum ditentukan</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Saudi Transport Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">🚌 Transportasi Saudi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Makkah Transports -->
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-900 mb-3">🕋 Transportasi Mekkah</h4>
                                <template x-if="package.saudi_transports && package.saudi_transports.makkah && package.saudi_transports.makkah.length > 0">
                                    <div class="space-y-3">
                                        <template x-for="(transport, index) in package.saudi_transports.makkah" :key="index">
                                            <div class="bg-white p-3 rounded-lg border border-purple-200">
                                                <div class="font-medium text-purple-900" x-text="transport.name"></div>
                                                <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                                                    <div><span class="font-medium">Kapasitas:</span> <span x-text="transport.capacity"></span> orang</div>
                                                    <div><span class="font-medium">Harga:</span> Rp <span x-text="formatNumber(transport.price)"></span>/orang</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!package.saudi_transports || !package.saudi_transports.makkah || package.saudi_transports.makkah.length === 0">
                                    <p class="text-gray-500 text-sm">Belum ada transportasi untuk Mekkah</p>
                                </template>
                            </div>

                            <!-- Madinah Transports -->
                            <div class="bg-teal-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-teal-900 mb-3">🕌 Transportasi Madinah</h4>
                                <template x-if="package.saudi_transports && package.saudi_transports.madinah && package.saudi_transports.madinah.length > 0">
                                    <div class="space-y-3">
                                        <template x-for="(transport, index) in package.saudi_transports.madinah" :key="index">
                                            <div class="bg-white p-3 rounded-lg border border-teal-200">
                                                <div class="font-medium text-teal-900" x-text="transport.name"></div>
                                                <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                                                    <div><span class="font-medium">Kapasitas:</span> <span x-text="transport.capacity"></span> orang</div>
                                                    <div><span class="font-medium">Harga:</span> Rp <span x-text="formatNumber(transport.price)"></span>/orang</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!package.saudi_transports || !package.saudi_transports.madinah || package.saudi_transports.madinah.length === 0">
                                    <p class="text-gray-500 text-sm">Belum ada transportasi untuk Madinah</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Package Photos -->
                    <div x-show="package.package_photos && package.package_photos.length > 0" class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">📷 Galeri Foto</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <template x-for="(photo, index) in package.package_photos" :key="index">
                                <div class="relative group">
                                    <img :src="photo" :alt="`Package photo ${index + 1}`" 
                                         class="w-full h-32 object-cover rounded-lg shadow-sm group-hover:shadow-md transition-shadow cursor-pointer"
                                         @click="window.open(photo, '_blank')">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Keberangkatan Tab -->
                <div x-show="activeTab === 'keberangkatan'" x-data="keberangkatanTab(<?php echo e($package->id); ?>)" x-init="init()">
                    <?php echo $__env->make('admin.travel.package.partials.keberangkatan-tab', ['package' => $package], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <!-- Workflow Tab -->
                <div x-show="activeTab === 'workflow'">
                    <?php echo $__env->make('admin.travel.package.workflow', ['packageId' => $package->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <!-- HPP Tab -->
                <div x-show="activeTab === 'hpp'" class="space-y-4">
                    <div x-show="package.hpp_calculation" class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">HPP Dasar Paket</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Estimasi biaya pokok. HPP aktual per jamaah dihitung di keberangkatan berdasarkan tipe kamar booking & add-ons.</p>
                            <button @click="openHppModal()" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                <i class="bx bx-edit-alt"></i> Edit HPP
                            </button>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <div class="px-4 py-2 bg-slate-50 border-b border-slate-200 text-sm font-semibold text-slate-700">Komponen Biaya (per orang)</div>
                            <div class="divide-y divide-slate-100">
                                <div class="flex justify-between px-4 py-2.5 text-sm">
                                    <span class="text-slate-600">Biaya Penerbangan</span>
                                    <span class="font-semibold" x-text="formatCurrency(package.hpp_calculation?.flight_cost || 0)"></span>
                                </div>
                                <div class="px-4 py-2 text-xs text-slate-400 italic">Hotel tidak masuk HPP Dasar — dihitung per booking jamaah</div>
                                <template x-for="[key, label] in [['transportation_cost','Transportasi'],['meal_cost','Makan'],['visa_cost','Visa'],['guide_cost','Pembimbing'],['insurance_cost','Asuransi'],['operational_overhead','Operasional'],['contingency','Kontingensi']]" :key="key">
                                    <div class="flex justify-between px-4 py-2.5 text-sm" x-show="(package.hpp_calculation?.[key]||0) > 0">
                                        <span class="text-slate-600" x-text="label"></span>
                                        <span class="font-semibold" x-text="formatCurrency(package.hpp_calculation?.[key] || 0)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between px-4 py-3 bg-primary-50 font-semibold text-sm">
                                    <span class="text-primary-900">HPP per Orang</span>
                                    <span class="text-primary-900" x-text="formatCurrency(calculateHppPerPerson())"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Simulasi per Price Package & Varian -->
                        <div x-show="package.price_packages && package.price_packages.length > 0">
                            <div class="text-sm font-semibold text-slate-700 mb-2">Simulasi Revenue & Profit per Paket Harga</div>
                            <template x-for="pkg in (package.price_packages || [])" :key="pkg.name">
                                <div class="mb-3 rounded-xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-200 text-sm font-semibold text-slate-700" x-text="pkg.name"></div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-slate-50 text-xs text-slate-500">
                                                <tr>
                                                    <th class="text-left px-4 py-2">Varian</th>
                                                    <th class="text-right px-4 py-2">Harga/Orang</th>
                                                    <th class="text-right px-4 py-2">HPP/Orang</th>
                                                    <th class="text-right px-4 py-2">Total Revenue</th>
                                                    <th class="text-right px-4 py-2">Profit/Orang</th>
                                                    <th class="text-right px-4 py-2">Margin</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <template x-for="v in (pkg.variants || [])" :key="v.type">
                                                    <tr>
                                                        <td class="px-4 py-2.5 capitalize font-medium" x-text="v.type"></td>
                                                        <td class="px-4 py-2.5 text-right font-mono" x-text="formatCurrency(parseFloat(v.price)||0)"></td>
                                                        <td class="px-4 py-2.5 text-right font-mono text-slate-500" x-text="formatCurrency(calculateHppPerPerson())"></td>
                                                        <td class="px-4 py-2.5 text-right font-mono text-green-700"
                                                            x-text="formatCurrency((parseFloat(v.price)||0)*package.capacity)"></td>
                                                        <td class="px-4 py-2.5 text-right font-mono"
                                                            :class="((parseFloat(v.price)||0)-calculateHppPerPerson())>=0?'text-green-700':'text-red-600'"
                                                            x-text="formatCurrency((parseFloat(v.price)||0)-calculateHppPerPerson())"></td>
                                                        <td class="px-4 py-2.5 text-right font-mono"
                                                            :class="(parseFloat(v.price)||0)>0&&(((parseFloat(v.price)||0)-calculateHppPerPerson())/(parseFloat(v.price)||1)*100)>=0?'text-green-700':'text-red-600'"
                                                            x-text="(parseFloat(v.price)||0)>0?(((parseFloat(v.price)||0)-calculateHppPerPerson())/(parseFloat(v.price)||1)*100).toFixed(1)+'%':'-'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Fallback single price -->
                        <div x-show="!package.price_packages || package.price_packages.length === 0" class="space-y-2">
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex justify-between text-sm"><span class="font-semibold text-green-900">Harga Jual / Orang</span><span class="font-bold text-green-900" x-text="formatCurrency(package.price)"></span></div>
                                <div class="flex justify-between text-sm mt-1"><span class="text-green-800">Total Revenue (<span x-text="package.capacity"></span> jamaah)</span><span class="font-bold text-green-900" x-text="formatCurrency(package.price * package.capacity)"></span></div>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex justify-between text-sm"><span class="font-semibold text-blue-900">Profit / Orang</span><span class="font-bold" :class="(package.price-calculateHppPerPerson())>=0?'text-green-700':'text-red-700'" x-text="formatCurrency(package.price-calculateHppPerPerson())"></span></div>
                                <div class="flex justify-between text-sm mt-1"><span class="text-blue-800">Total Profit</span><span class="font-bold" :class="((package.price*package.capacity)-(calculateHppPerPerson()*package.capacity))>=0?'text-green-700':'text-red-700'" x-text="formatCurrency((package.price*package.capacity)-(calculateHppPerPerson()*package.capacity))"></span></div>
                                <div class="flex justify-between text-sm mt-1"><span class="text-blue-800">Profit Margin</span><span class="font-bold" :class="calculateProfitMarginFromHpp()>=0?'text-green-700':'text-red-700'" x-text="calculateProfitMarginFromHpp().toFixed(2)+'%'"></span></div>
                            </div>
                        </div>

                        <div x-show="package.hpp_calculation?.is_locked" class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl flex items-center gap-2 text-yellow-800 text-sm">
                            <i class='bx bx-lock-alt text-xl'></i>
                            <div><div class="font-medium">HPP Terkunci</div><div class="text-xs">HPP tidak dapat diubah lagi.</div></div>
                        </div>
                    </div>
                    <div x-show="!package.hpp_calculation" class="text-center py-12">
                        <i class='bx bx-calculator text-6xl text-gray-300'></i>
                        <p class="mt-4 text-gray-500">Belum ada HPP calculation</p>
                        <button @click="openHppModal()" type="button" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="bx bx-plus"></i> Buat HPP Calculation
                        </button>
                    </div>
                </div>

                <!-- Design Materials Tab -->
                <div x-show="activeTab === 'materials'">
                    <?php echo $__env->make('admin.travel.package.design-materials', ['packageId' => $package->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <!-- Tour Plan Tab -->
                <div x-show="activeTab === 'tourplan'">
                    <?php echo $__env->make('admin.travel.package.partials.tour-plan-tab', ['package' => $package], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>

        <!-- Include HPP Modal (inside Alpine component scope) -->
        <?php echo $__env->make('admin.travel.package.hpp-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script>
    function packageDetail(packageId) {
        return {
            packageId: packageId,
            package: {},
            activeTab: 'details',
            exportingPdf: false,
            
            // HPP Modal state
            showHppModal: false,
            selectedPackage: null,
            hppForm: {
                flight_cost: 0,
                hotel_cost: 0,
                transportation_cost: 0,
                meal_cost: 0,
                visa_cost: 0,
                guide_cost: 0,
                insurance_cost: 0,
                operational_overhead: 0,
                contingency: 0
            },
            hppErrors: {},
            hppLocked: false,
            loadingHpp: false,
            savingHpp: false,
            lockingHpp: false,
            showLockConfirm: false,
            availableFlights: [],
            availableHotels: [],
            availableSaudiTransports: [],
            selectedFlightId: '',
            selectedHotelId: '',
            selectedFlightPrice: 0,
            selectedHotelPrice: 0,
            // Dynamic extra components (beyond flight & hotel)
            hppExtraComponents: [],
            // Split flight/hotel/transport state
            flightDeparture: { id: '', price: 0, manual: 0 },
            flightReturn:    { id: '', price: 0, manual: 0 },
            hotelMakkah:     { id: '', price_per_night: 0, manual: 0, nights: 0 },
            hotelMadinah:    { id: '', price_per_night: 0, manual: 0, nights: 0 },
            saudiTransportSelected: { id: '', price: 0, manual: 0 },
            async init() {
                console.log('Package Detail Alpine.js initialized', this.packageId);
                await this.fetchPackage();
                console.log('Package data loaded:', this.package);
            },
            
            async fetchPackage() {
                try {
                    const response = await fetch(`<?php echo e(url('admin/inventaris/travel/package')); ?>/${this.packageId}`);
                    const data = await response.json();
                    
                    // Parse hpp_calculation properly
                    let hppCalculation = null;
                    if (data.hpp_calculation) {
                        hppCalculation = {
                            flight_cost: parseFloat(data.hpp_calculation.flight_cost) || 0,
                            hotel_cost: parseFloat(data.hpp_calculation.hotel_cost) || 0,
                            transportation_cost: parseFloat(data.hpp_calculation.transportation_cost) || 0,
                            meal_cost: parseFloat(data.hpp_calculation.meal_cost) || 0,
                            visa_cost: parseFloat(data.hpp_calculation.visa_cost) || 0,
                            guide_cost: parseFloat(data.hpp_calculation.guide_cost) || 0,
                            insurance_cost: parseFloat(data.hpp_calculation.insurance_cost) || 0,
                            operational_overhead: parseFloat(data.hpp_calculation.operational_overhead) || 0,
                            contingency: parseFloat(data.hpp_calculation.contingency) || 0,
                            total_hpp: parseFloat(data.hpp_calculation.total_hpp) || 0,
                            is_locked: data.hpp_calculation.is_locked || false
                        };
                    }
                    
                    this.package = {
                        ...data,
                        capacity: parseInt(data.capacity) || 0,
                        price: parseFloat(data.price) || 0,
                        hpp: parseFloat(data.hpp) || 0,
                        profit_margin: parseFloat(data.profit_margin) || 0,
                        hpp_calculation: hppCalculation
                    };
                    
                    console.log('Package loaded:', this.package);
                    console.log('HPP Calculation:', this.package.hpp_calculation);
                } catch (error) {
                    console.error('Error fetching package:', error);
                }
            },
            
            exportPdf() {
                this.exportingPdf = true;
                
                // Create a temporary link and trigger download
                const url = `<?php echo e(url('admin/inventaris/travel/package')); ?>/${this.packageId}/export-pdf`;
                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';
                link.download = `Package_${this.package.package_code}_${Date.now()}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Reset loading state after a short delay
                setTimeout(() => {
                    this.exportingPdf = false;
                }, 2000);
            },
            
            editPackage() {
                window.location.href = `<?php echo e(url('admin/inventaris/travel/package')); ?>/${this.packageId}/edit`;
            },
            
            async openHppModal() {
                this.selectedPackage = {
                    ...this.package,
                    capacity: parseInt(this.package.capacity) || 0,
                    price: parseFloat(this.package.price) || 0,
                    id_saudi_transport: this.package.id_saudi_transport || null,
                    saudi_transport_price: this.package.saudi_transport
                        ? parseFloat(this.package.saudi_transport.price_per_person) || 0
                        : 0,
                };

                // Reset split state
                this.flightDeparture = { id: '', price: 0, manual: 0 };
                this.flightReturn    = { id: '', price: 0, manual: 0 };
                this.hotelMakkah     = { id: '', price_per_night: 0, manual: 0, nights: 0 };
                this.hotelMadinah    = { id: '', price_per_night: 0, manual: 0, nights: 0 };
                
                this.showHppModal = true;
                document.body.style.overflow = 'hidden';
                
                await Promise.all([
                    this.fetchHppData(),
                    this.loadFlights(),
                    this.loadHotels(),
                    this.loadSaudiTransports()
                ]);

                this.$nextTick(() => {
                    this.autoFillFlightHotel();
                    this.autoFillSaudiTransport();
                });
            },

            autoFillSaudiTransport() {
                const pkg = this.selectedPackage;
                if (!pkg?.id_saudi_transport) return;
                const price = parseFloat(pkg.saudi_transport_price) || 0;
                const t = this.availableSaudiTransports.find(x => x.id == pkg.id_saudi_transport);
                if (t) {
                    this.saudiTransportSelected.id = t.id;
                    this.saudiTransportSelected.price = parseFloat(t.price_per_person) || price;
                } else if (price) {
                    this.saudiTransportSelected.id = '';
                    this.saudiTransportSelected.manual = price;
                }
                this.syncSaudiTransportToComp();
            },

            onSaudiTransportSelected() {
                const t = this.availableSaudiTransports.find(x => x.id == this.saudiTransportSelected.id);
                this.saudiTransportSelected.price = t ? (parseFloat(t.price_per_person) || 0) : 0;
                this.syncSaudiTransportToComp();
            },

            syncSaudiTransportToComp() {
                const val = this.saudiTransportSelected.id
                    ? this.saudiTransportSelected.price
                    : (parseFloat(this.saudiTransportSelected.manual) || 0);
                const comp = this.hppExtraComponents.find(c => c.id === 'transportation_cost');
                if (comp) comp.value = val;
            },

            async loadSaudiTransports() {
                try {
                    const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.saudi-transports')); ?>';
                    const response = await fetch(url);
                    if (response.ok) {
                        this.availableSaudiTransports = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading saudi transports:', error);
                }
            },

            autoFillFlightHotel() {
                const pkg = this.selectedPackage;
                const duration = parseInt(pkg.duration_days) || 1;

                if (pkg.id_flight_departure) {
                    const f = this.availableFlights.find(x => x.id == pkg.id_flight_departure);
                    if (f) { this.flightDeparture.id = f.id; this.flightDeparture.price = parseFloat(f.price_per_person)||0; }
                }
                if (pkg.id_flight_return) {
                    const f = this.availableFlights.find(x => x.id == pkg.id_flight_return);
                    if (f) { this.flightReturn.id = f.id; this.flightReturn.price = parseFloat(f.price_per_person)||0; }
                }
                if (pkg.id_hotel_room_type_makkah) {
                    const h = this.availableHotels.find(x => x.id == pkg.id_hotel_room_type_makkah);
                    if (h) { this.hotelMakkah.id = h.id; this.hotelMakkah.price_per_night = parseFloat(h.price_per_night)||0; }
                }
                this.hotelMakkah.nights = duration;
                if (pkg.id_hotel_room_type_madinah) {
                    const h = this.availableHotels.find(x => x.id == pkg.id_hotel_room_type_madinah);
                    if (h) { this.hotelMadinah.id = h.id; this.hotelMadinah.price_per_night = parseFloat(h.price_per_night)||0; }
                }
                this.hotelMadinah.nights = duration;

                if (this.hppForm.flight_cost === 0) this.hppForm.flight_cost = this.calcFlightTotal();
                if (this.hppForm.hotel_cost  === 0) this.hppForm.hotel_cost  = this.calcHotelTotal();
            },

            calcFlightTotal() {
                const fd = this.flightDeparture, fr = this.flightReturn;
                return (fd.id ? fd.price : (parseFloat(fd.manual)||0))
                     + (fr.id ? fr.price : (parseFloat(fr.manual)||0));
            },
            calcHotelTotal() {
                const hm = this.hotelMakkah, hd = this.hotelMadinah;
                return ((hm.id ? hm.price_per_night : (parseFloat(hm.manual)||0)) * (parseInt(hm.nights)||0))
                     + ((hd.id ? hd.price_per_night : (parseFloat(hd.manual)||0)) * (parseInt(hd.nights)||0));
            },

            onFlightDepartureSelected() {
                const f = this.availableFlights.find(x => x.id == this.flightDeparture.id);
                this.flightDeparture.price = f ? (parseFloat(f.price_per_person)||0) : 0;
                this.hppForm.flight_cost = this.calcFlightTotal();
            },
            onFlightReturnSelected() {
                const f = this.availableFlights.find(x => x.id == this.flightReturn.id);
                this.flightReturn.price = f ? (parseFloat(f.price_per_person)||0) : 0;
                this.hppForm.flight_cost = this.calcFlightTotal();
            },
            onHotelMakkahSelected() {
                const h = this.availableHotels.find(x => x.id == this.hotelMakkah.id);
                this.hotelMakkah.price_per_night = h ? (parseFloat(h.price_per_night)||0) : 0;
                this.hppForm.hotel_cost = this.calcHotelTotal();
            },
            onHotelMadinahSelected() {
                const h = this.availableHotels.find(x => x.id == this.hotelMadinah.id);
                this.hotelMadinah.price_per_night = h ? (parseFloat(h.price_per_night)||0) : 0;
                this.hppForm.hotel_cost = this.calcHotelTotal();
            },
            
            calculateHppPerPerson() {
                // HPP Dasar = tanpa hotel (hotel dihitung per booking jamaah)
                if (this.package.hpp_calculation) {
                    return (this.package.hpp_calculation.flight_cost || 0) +
                           (this.package.hpp_calculation.transportation_cost || 0) +
                           (this.package.hpp_calculation.meal_cost || 0) +
                           (this.package.hpp_calculation.visa_cost || 0) +
                           (this.package.hpp_calculation.guide_cost || 0) +
                           (this.package.hpp_calculation.insurance_cost || 0) +
                           (this.package.hpp_calculation.operational_overhead || 0) +
                           (this.package.hpp_calculation.contingency || 0);
                }
                return 0;
            },
            
            calculateProfitMarginFromHpp() {
                const totalHpp = this.calculateHppPerPerson() * this.package.capacity;
                const totalRevenue = this.package.price * this.package.capacity;
                
                if (totalRevenue === 0) return 0;
                return ((totalRevenue - totalHpp) / totalRevenue) * 100;
            },
            
            // Copy all HPP management functions from index.blade.php
            async fetchHppData() {
                if (!this.selectedPackage) return;
                
                this.loadingHpp = true;
                try {
                    const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
                    const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp`);
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.hppForm = {
                            flight_cost: parseFloat(data.flight_cost) || 0,
                            hotel_cost: parseFloat(data.hotel_cost) || 0,
                            transportation_cost: parseFloat(data.transportation_cost) || 0,
                            meal_cost: parseFloat(data.meal_cost) || 0,
                            visa_cost: parseFloat(data.visa_cost) || 0,
                            guide_cost: parseFloat(data.guide_cost) || 0,
                            insurance_cost: parseFloat(data.insurance_cost) || 0,
                            operational_overhead: parseFloat(data.operational_overhead) || 0,
                            contingency: parseFloat(data.contingency) || 0
                        };
                        this.hppLocked = data.is_locked || false;

                        // Build extra components from existing HPP data
                        this.hppExtraComponents = [];
                        const extras = [
                            { key: 'transportation_cost', label: 'Biaya Transportasi', hint: 'Transportasi lokal per orang' },
                            { key: 'meal_cost', label: 'Biaya Makan', hint: 'Biaya makan selama perjalanan' },
                            { key: 'visa_cost', label: 'Biaya Visa', hint: 'Pengurusan visa per orang' },
                            { key: 'guide_cost', label: 'Biaya Pembimbing', hint: 'Pembimbing/muthawif per orang' },
                            { key: 'insurance_cost', label: 'Biaya Asuransi', hint: 'Asuransi perjalanan per orang' },
                            { key: 'operational_overhead', label: 'Biaya Operasional', hint: 'Operasional & administrasi' },
                            { key: 'contingency', label: 'Biaya Kontingensi', hint: 'Cadangan darurat per orang' },
                        ];
                        extras.forEach(e => {
                            this.hppExtraComponents.push({
                                id: e.key,
                                label: e.label,
                                hint: e.hint,
                                value: parseFloat(data[e.key]) || 0,
                                isDefault: true,
                                payment_status: (data.component_payment_status || {})[e.key] || 'lunas',
                                hutang_amount: parseFloat((data.component_hutang_amount || {})[e.key]) || 0,
                            });
                        });
                    } else {
                        // No HPP yet — init default extra components
                        this.initDefaultExtraComponents();
                    }
                } catch (error) {
                    console.error('Error fetching HPP data:', error);
                    this.initDefaultExtraComponents();
                } finally {
                    this.loadingHpp = false;
                }
            },

            initDefaultExtraComponents() {
                this.hppExtraComponents = [
                    { id: 'transportation_cost', label: 'Biaya Transportasi', hint: 'Transportasi lokal per orang', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'meal_cost', label: 'Biaya Makan', hint: 'Biaya makan selama perjalanan', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'visa_cost', label: 'Biaya Visa', hint: 'Pengurusan visa per orang', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'guide_cost', label: 'Biaya Pembimbing', hint: 'Pembimbing/muthawif per orang', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'insurance_cost', label: 'Biaya Asuransi', hint: 'Asuransi perjalanan per orang', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'operational_overhead', label: 'Biaya Operasional', hint: 'Operasional & administrasi', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                    { id: 'contingency', label: 'Biaya Kontingensi', hint: 'Cadangan darurat per orang', value: 0, isDefault: true, payment_status: 'lunas', hutang_amount: 0 },
                ];
            },

            addExtraComponent() {
                this.hppExtraComponents.push({
                    id: 'custom_' + Date.now(),
                    label: '', hint: '', value: 0, isDefault: false,
                    payment_status: 'lunas', hutang_amount: 0,
                });
            },

            removeExtraComponent(index) {
                this.hppExtraComponents.splice(index, 1);
            },

            getTotalExtraComponents() {
                return this.hppExtraComponents.reduce((sum, c) => sum + (parseFloat(c.value) || 0), 0);
            },
            
            async loadFlights() {
                try {
                    const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.flights')); ?>';
                    const params = new URLSearchParams();
                    
                    if (this.selectedPackage?.id_outlet) {
                        params.append('outlet_id', this.selectedPackage.id_outlet);
                    }
                    
                    const response = await fetch(`${url}?${params}`);
                    if (response.ok) {
                        this.availableFlights = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading flights:', error);
                }
            },
            
            async loadHotels() {
                try {
                    const url = '<?php echo e(route('admin.inventaris.travel.package.hpp.hotels')); ?>';
                    const params = new URLSearchParams();
                    
                    if (this.selectedPackage?.id_outlet) {
                        params.append('outlet_id', this.selectedPackage.id_outlet);
                    }
                    
                    const response = await fetch(`${url}?${params}`);
                    if (response.ok) {
                        this.availableHotels = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading hotels:', error);
                }
            },
            closeHppModal() {
                this.showHppModal = false;
                this.showLockConfirm = false;
                this.selectedPackage = null;
                this.hppLocked = false;
                this.selectedFlightId = '';
                this.selectedHotelId = '';
                this.selectedFlightPrice = 0;
                this.selectedHotelPrice = 0;
                this.resetHppForm();
                
                // Force remove any lingering overlays
                setTimeout(() => {
                    document.body.style.overflow = '';
                }, 100);
            },
            
            resetHppForm() {
                this.hppForm = {
                    flight_cost: 0,
                    hotel_cost: 0,
                    transportation_cost: 0,
                    meal_cost: 0,
                    visa_cost: 0,
                    guide_cost: 0,
                    insurance_cost: 0,
                    operational_overhead: 0,
                    contingency: 0
                };
                this.hppErrors = {};
                this.hppExtraComponents = [];
                this.flightDeparture = { id: '', price: 0, manual: 0 };
                this.flightReturn    = { id: '', price: 0, manual: 0 };
                this.hotelMakkah     = { id: '', price_per_night: 0, manual: 0, nights: 0 };
                this.hotelMadinah    = { id: '', price_per_night: 0, manual: 0, nights: 0 };
                this.saudiTransportSelected = { id: '', price: 0, manual: 0 };
            },
            
            async submitHppForm() {
                if (this.hppLocked) {
                    alert('HPP sudah terkunci dan tidak dapat diubah');
                    return;
                }
                
                this.hppErrors = {};
                this.savingHpp = true;

                // Build payload: merge fixed fields + extra components
                const extraMap = {};
                const payStatusMap = {};
                const hutangMap = {};
                const knownKeys = ['transportation_cost','meal_cost','visa_cost','guide_cost','insurance_cost','operational_overhead','contingency'];
                knownKeys.forEach(k => extraMap[k] = 0);
                this.hppExtraComponents.forEach(c => {
                    if (knownKeys.includes(c.id)) {
                        extraMap[c.id] = (extraMap[c.id] || 0) + (parseFloat(c.value) || 0);
                        payStatusMap[c.id] = c.payment_status || 'lunas';
                        if (c.payment_status === 'hutang') hutangMap[c.id] = parseFloat(c.hutang_amount) || 0;
                    } else if (c.id.startsWith('custom_')) {
                        extraMap['operational_overhead'] = (extraMap['operational_overhead'] || 0) + (parseFloat(c.value) || 0);
                    }
                });

                const payload = {
                    flight_cost: this.hppForm.flight_cost || 0,
                    hotel_cost: this.hppForm.hotel_cost || 0,
                    ...extraMap,
                    component_payment_status: payStatusMap,
                    component_hutang_amount: hutangMap,
                };
                
                try {
                    const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
                    const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Berhasil!', 'HPP calculation berhasil disimpan', 'success');
                        } else {
                            alert('HPP calculation berhasil disimpan');
                        }
                        this.closeHppModal();
                        await this.fetchPackage(); // Refresh package data
                    } else {
                        if (data.errors) {
                            this.hppErrors = data.errors;
                        } else {
                            throw new Error(data.message || 'Failed to save HPP');
                        }
                    }
                } catch (error) {
                    console.error('Error saving HPP:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Gagal menyimpan HPP calculation', 'error');
                    } else {
                        alert('Gagal menyimpan HPP calculation');
                    }
                } finally {
                    this.savingHpp = false;
                }
            },
            
            confirmLockHpp() {
                this.showLockConfirm = true;
                document.body.style.overflow = 'hidden';
            },
            
            async lockHppNow() {
                this.lockingHpp = true;
                
                try {
                    const baseUrl = '<?php echo e(url('admin/inventaris/travel/package')); ?>';
                    const response = await fetch(`${baseUrl}/${this.selectedPackage.id}/hpp/lock`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Berhasil!', 'HPP calculation berhasil dikunci', 'success');
                        } else {
                            alert('HPP calculation berhasil dikunci');
                        }
                        this.showLockConfirm = false;
                        this.hppLocked = true;
                        document.body.style.overflow = '';
                        await this.fetchPackage();
                    } else {
                        throw new Error(data.message || 'Failed to lock HPP');
                    }
                } catch (error) {
                    console.error('Error locking HPP:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Gagal mengunci HPP calculation', 'error');
                    } else {
                        alert('Gagal mengunci HPP calculation');
                    }
                } finally {
                    this.lockingHpp = false;
                }
            },
            
            calculateTotalHpp() {
                const capacity = this.selectedPackage?.capacity || 1;
                const flightOnly = (this.hppForm.flight_cost || 0);
                const extras = this.getTotalExtraComponents();
                return (flightOnly + extras) * capacity;
            },
            
            calculateProfitMargin() {
                const totalHpp = this.calculateTotalHpp();
                const capacity = this.selectedPackage?.capacity || 1;
                const price = this.selectedPackage?.price || 0;
                const totalRevenue = price * capacity;
                
                if (totalRevenue === 0) return 0;
                return ((totalRevenue - totalHpp) / totalRevenue) * 100;
            },
            
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric'
                });
            },
            
            formatDateTime(dateTimeString) {
                if (!dateTimeString) return '-';
                const date = new Date(dateTimeString);
                return date.toLocaleString('id-ID', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
            
            formatCurrency(amount) {
                if (!amount) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            },
            
            formatLabel(key) {
                return key.split('_').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
            }
        };
    }

    function keberangkatanTab(packageId) {
        return {
            packageId: packageId,
            keberangkatanList: [],
            loadingKeberangkatan: false,
            showKbForm: false,
            savingKb: false,
            toDeleteKb: null,
            deletingKb: false,
            showFinancialModal: false,
            financialData: null,
            selectedKb: null,
            showRabModal: false,
            rabData: null,
            rabKb: null,
            updatingRab: false,
            kbForm: {
                id: null, keberangkatan_name: '', departure_date: '', return_date: '',
                total_jamaah: 0, status: 'planning'
            },

            async init() {
                await this.fetchKeberangkatan();
            },

            async fetchKeberangkatan() {
                this.loadingKeberangkatan = true;
                try {
                    const res = await fetch(`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/data?package_filter=${this.packageId}&per_page=100`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    // DataTables format
                    const rows = data.data || [];
                    // Fetch detail for each keberangkatan
                    this.keberangkatanList = await Promise.all(rows.map(async (kb) => {
                        const detail = await this.fetchKbDetail(kb.id || kb.DT_RowId);
                        return detail;
                    }));
                } catch(e) {
                    console.error('Error fetching keberangkatan:', e);
                } finally {
                    this.loadingKeberangkatan = false;
                }
            },

            async fetchKbDetail(id) {
                try {
                    const res = await fetch(`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${id}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const kb = await res.json();
                    // Fetch bookings with addons
                    const bookings = await this.fetchKbBookings(id, kb);
                    return {
                        ...kb,
                        departure_date_formatted: kb.departure_date ? new Date(kb.departure_date).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '-',
                        bookings: bookings
                    };
                } catch(e) {
                    return { id, keberangkatan_name: 'Error', bookings: [] };
                }
            },

            async fetchKbBookings(kbId, kb) {
                try {
                    const res = await fetch(`<?php echo e(url('')); ?>/admin/inventaris/travel/booking/data?keberangkatan_id=${kbId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    const rows = data.data || [];
                    // Enrich with hpp calculation — hpp_dasar sudah ada dari API
                    return rows.map(b => {
                        const hppDasar = b.hpp_dasar || 0;
                        const addons = b.addons || [];

                        // Add-ons: masuk_hpp → tambah ke HPP, semua add-ons → tambah ke harga jual
                        const hppAddons = addons.filter(a => a.masuk_hpp).reduce((s,a) => s + (parseFloat(a.harga)||0)*(parseInt(a.qty)||1), 0);
                        const allAddons = addons.reduce((s,a) => s + (parseFloat(a.harga)||0)*(parseInt(a.qty)||1), 0);

                        // Hotel booking: charge → masuk HPP + harga jual, include → masuk HPP saja
                        const hotelBookings = b.hotel_bookings || [];
                        const hppHotelCharge = hotelBookings.filter(h => h.is_charged).reduce((s,h) => s + (parseFloat(h.total_cost)||0), 0);
                        const hppHotelInclude = hotelBookings.filter(h => !h.is_charged).reduce((s,h) => s + (parseFloat(h.total_cost)||0), 0);
                        const hppHotelTotal = hppHotelCharge + hppHotelInclude;

                        const hppAktual = hppDasar + hppAddons + hppHotelTotal;
                        const hargaJual = (parseFloat(b.total_price)||0) + allAddons + hppHotelCharge;

                        return {
                            ...b,
                            hpp_dasar: hppDasar,
                            hpp_hotel: hppHotelTotal,
                            hpp_addons: hppAddons,
                            hpp_aktual: hppAktual,
                            harga_jual_aktual: hargaJual,
                            family_count: b.family_members_count || 0,
                            family_members_list: b.family_members_list || []
                        };
                    });
                } catch(e) {
                    return [];
                }
            },

            openCreateKeberangkatan() {
                this.kbForm = {
                    id: null,
                    keberangkatan_name: '',
                    departure_date: '<?php echo e($package->departure_date ? $package->departure_date->format("Y-m-d") : ""); ?>',
                    return_date: '<?php echo e($package->return_date ? $package->return_date->format("Y-m-d") : ""); ?>',
                    total_jamaah: <?php echo e($package->capacity); ?>,
                    status: 'planning'
                };
                this.showKbForm = true;
            },

            openEditKeberangkatan(kb) {
                this.kbForm = {
                    id: kb.id,
                    keberangkatan_name: kb.keberangkatan_name,
                    departure_date: kb.departure_date,
                    return_date: kb.return_date,
                    total_jamaah: kb.total_jamaah,
                    status: kb.status
                };
                this.showKbForm = true;
            },

            async submitKbForm() {
                this.savingKb = true;
                try {
                    const isEdit = !!this.kbForm.id;
                    const url = isEdit
                        ? `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${this.kbForm.id}`
                        : `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan`;
                    const method = isEdit ? 'PUT' : 'POST';
                    const payload = {
                        ...this.kbForm,
                        id_travel_package: this.packageId,
                        id_outlet: <?php echo e($package->id_outlet); ?>,
                        keberangkatan_code: this.kbForm.keberangkatan_name.replace(/\s+/g, '-').toUpperCase() + '-' + Date.now().toString().slice(-4)
                    };
                    const res = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const result = await res.json();
                    if (res.ok) {
                        this.showKbForm = false;
                        await this.fetchKeberangkatan();
                    } else {
                        alert(result.message || 'Gagal menyimpan');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan');
                } finally {
                    this.savingKb = false;
                }
            },

            confirmDeleteKeberangkatan(kb) {
                this.toDeleteKb = kb;
            },

            async deleteKbNow() {
                if (!this.toDeleteKb) return;
                this.deletingKb = true;
                try {
                    const res = await fetch(`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${this.toDeleteKb.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        this.toDeleteKb = null;
                        await this.fetchKeberangkatan();
                    }
                } catch(e) {} finally {
                    this.deletingKb = false;
                }
            },

            downloadManifest(kbId) {
                window.open(`<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${kbId}/manifest`, '_blank');
            },

            openRoomlistSetting(kb) {
                // Buka halaman manage room position (stream PDF dengan live preview)
                window.open(`<?php echo e(url('')); ?>/admin/inventaris/travel/document/${kb.id}/manage-room-position`, '_blank');
            },

            async openFinancialReport(kb) {
                this.selectedKb = kb;
                this.showFinancialModal = true;
                // Build financial data from bookings
                const bookings = kb.bookings || [];
                const totalRevenue = bookings.reduce((s,b) => s + (b.harga_jual_aktual || parseFloat(b.total_price)||0), 0);
                const totalHpp = bookings.reduce((s,b) => s + (b.hpp_aktual||0), 0);
                this.financialData = {
                    total_revenue: totalRevenue,
                    total_hpp: totalHpp,
                    profit: totalRevenue - totalHpp,
                    rows: bookings.map(b => ({
                        booking_id: b.id,
                        jamaah_name: b.jamaah_name || '-',
                        room_type: b.room_type,
                        hpp_dasar: b.hpp_dasar || 0,
                        hpp_hotel: b.hpp_hotel || 0,
                        hpp_addons: b.hpp_addons || 0,
                        total_hpp: b.hpp_aktual || 0,
                        harga_jual: b.harga_jual_aktual || parseFloat(b.total_price) || 0,
                        profit: (b.harga_jual_aktual || parseFloat(b.total_price)||0) - (b.hpp_aktual||0)
                    }))
                };
            },

            async openRabModal(kb) {
                this.rabKb = kb;
                this.rabData = null;
                this.showRabModal = true;
                try {
                    const url = `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${kb.id}/rab-modal`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        this.rabData = await res.json();
                    } else {
                        const err = await res.json();
                        this.rabData = { error: err.error || 'Gagal memuat data RAB.' };
                    }
                } catch(e) {
                    this.rabData = { error: 'Gagal memuat data RAB.' };
                }
            },

            async sesuaikanLaporan() {
                if (!this.rabKb) return;
                const surplusDefisit = (this.rabData?.total_budget||0) - (this.rabData?.total_realisasi||0);
                const type = surplusDefisit >= 0 ? 'Surplus' : 'Defisit';
                const nilai = Math.abs(surplusDefisit);

                if (typeof Swal !== 'undefined') {
                    const result = await Swal.fire({
                        title: `Sesuaikan Laporan Keuangan?`,
                        html: `${type} sebesar <strong>${this.formatCurrency(nilai)}</strong> akan disesuaikan ke laporan keuangan keberangkatan ini.<br><br>
                               ${surplusDefisit >= 0
                                   ? 'Biaya (costs) akan <strong>dikurangi</strong> sebesar nilai surplus (efisiensi anggaran).'
                                   : 'Biaya (costs) akan <strong>ditambah</strong> sebesar nilai defisit (kelebihan pengeluaran).'}`,
                        icon: surplusDefisit >= 0 ? 'success' : 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Sesuaikan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: surplusDefisit >= 0 ? '#059669' : '#d97706',
                    });
                    if (!result.isConfirmed) return;
                }

                this.updatingRab = true;
                try {
                    const url = `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${this.rabKb.id}/sesuaikan-laporan`;
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({}),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Berhasil', data.message, 'success');
                        }
                        // Refresh RAB data
                        await this.openRabModal(this.rabKb);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', data.error || 'Gagal menyesuaikan laporan', 'error');
                        }
                    }
                } catch(e) {
                    console.error('Error sesuaikan laporan:', e);
                } finally {
                    this.updatingRab = false;
                }
            },

            async resetPenyesuaianLaporan() {
                if (!this.rabKb) return;
                if (typeof Swal !== 'undefined') {
                    const result = await Swal.fire({
                        title: 'Reset Penyesuaian?',
                        text: 'Penyesuaian laporan keuangan akan dihapus dan laporan kembali ke nilai asli.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal',
                    });
                    if (!result.isConfirmed) return;
                }
                this.updatingRab = true;
                try {
                    const url = `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${this.rabKb.id}/reset-penyesuaian`;
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({}),
                    });
                    if (res.ok) {
                        await this.openRabModal(this.rabKb);
                    }
                } catch(e) {
                    console.error('Error reset penyesuaian:', e);
                } finally {
                    this.updatingRab = false;
                }
            },

            async updateRabItemStatus(hppKey, status, hutangAmount, realisasi) {
                if (!this.rabKb) return;
                this.updatingRab = true;
                try {
                    const url = `<?php echo e(url('')); ?>/admin/inventaris/travel/keberangkatan/${this.rabKb.id}/rab-modal-update`;
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            hpp_key: hppKey,
                            payment_status: status,
                            hutang_amount: hutangAmount || 0,
                            realisasi: realisasi || 0,
                        }),
                    });
                    if (res.ok) {
                        await this.openRabModal(this.rabKb);
                    }
                } catch(e) {
                    console.error('Error updating RAB item:', e);
                } finally {
                    this.updatingRab = false;
                }
            },

            async openAddJamaah(kb) {
                window.location.href = `<?php echo e(route('admin.inventaris.booking.index')); ?>?keberangkatan=${kb.id}&package=${this.packageId}`;
            },

            formatCurrency(amount) {
                if (!amount) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            },

            calcAge(dob) {
                if (!dob) return '-';
                const today = new Date();
                const birth = new Date(dob);
                let age = today.getFullYear() - birth.getFullYear();
                const m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                return age;
            }
        };
    }
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
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/show.blade.php ENDPATH**/ ?>