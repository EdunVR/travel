<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> Pengaturan Perusahaan <?php $__env->endSlot(); ?>
     <?php $__env->slot('header', null, []); ?> Pengaturan Perusahaan <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <!-- Header Card with Outlet Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Pengaturan Perusahaan</h1>
                    <p class="text-slate-600 mt-1">Kelola informasi dan pengaturan perusahaan untuk outlet yang dipilih</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="openCompanySettingsModal()" 
                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Pengaturan
                    </button>
                </div>
            </div>
            
            <!-- Outlet Filter -->
            <div class="border-t border-slate-200 pt-4">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-slate-700">Filter Outlet:</label>
                    <select id="outlet_filter" onchange="changeOutlet()" 
                            class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($outlet->id_outlet); ?>" <?php echo e($outlet->id_outlet == $currentOutletId ? 'selected' : ''); ?>>
                                <?php echo e($outlet->nama_outlet); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="text-sm text-slate-500">
                        <span class="inline-flex items-center px-2 py-1 bg-slate-100 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Outlet Aktif: <?php echo e($setting->outlet->nama_outlet ?? 'Tidak ada'); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Informasi Perusahaan
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Perusahaan</label>
                        <p class="text-slate-900 font-medium"><?php echo e($setting->company_name); ?></p>
                    </div>

                    <?php if($setting->company_code): ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kode Perusahaan</label>
                        <p class="text-slate-900"><?php echo e($setting->company_code); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->company_address): ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <p class="text-slate-900"><?php echo $setting->formatted_address; ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php if($setting->company_phone): ?>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                            <p class="text-slate-900"><?php echo e($setting->company_phone); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($setting->company_email): ?>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <p class="text-slate-900"><?php echo e($setting->company_email); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if($setting->company_website): ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Website</label>
                        <a href="<?php echo e($setting->company_website); ?>" target="_blank" class="text-primary-600 hover:text-primary-700">
                            <?php echo e($setting->company_website); ?>

                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Logo & Branding -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Logo & Branding
                </h2>

                <div class="space-y-4">
                    <!-- Company Logo -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Logo Perusahaan</label>
                        <?php if($setting->company_logo): ?>
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo e($setting->logo_url); ?>" alt="Company Logo" class="h-16 w-auto object-contain border border-slate-200 rounded-lg p-2">
                                <div>
                                    <p class="text-sm text-slate-600">Logo saat ini</p>
                                    <p class="text-xs text-slate-500"><?php echo e(basename($setting->company_logo)); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-16 w-32 border-2 border-dashed border-slate-300 rounded-lg">
                                <span class="text-sm text-slate-500">Tidak ada logo</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Company Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                        <?php if($setting->company_favicon): ?>
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo e($setting->favicon_url); ?>" alt="Favicon" class="h-8 w-8 object-contain border border-slate-200 rounded">
                                <div>
                                    <p class="text-sm text-slate-600">Favicon saat ini</p>
                                    <p class="text-xs text-slate-500"><?php echo e(basename($setting->company_favicon)); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center justify-center h-8 w-8 border-2 border-dashed border-slate-300 rounded">
                                <span class="text-xs text-slate-500">-</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legal & Banking Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Legal Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Dokumen Legal
                </h2>

                <div class="space-y-3">
                    <?php if($setting->npwp): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">NPWP</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->npwp); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->nib): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">NIB</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->nib); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->siup): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">SIUP</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->siup); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->tdp): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">TDP</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->tdp); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if(!$setting->npwp && !$setting->nib && !$setting->siup && !$setting->tdp): ?>
                    <div class="text-center py-4">
                        <p class="text-sm text-slate-500">Belum ada dokumen legal yang diatur</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Banking Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Informasi Bank
                </h2>

                <div class="space-y-3">
                    <?php if($setting->bank_name): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">Nama Bank</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->bank_name); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->bank_account_number): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">No. Rekening</span>
                        <span class="text-sm text-slate-900 font-mono"><?php echo e($setting->bank_account_number); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($setting->bank_account_name): ?>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-sm font-medium text-slate-700">Atas Nama</span>
                        <span class="text-sm text-slate-900"><?php echo e($setting->bank_account_name); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if(!$setting->bank_name && !$setting->bank_account_number && !$setting->bank_account_name): ?>
                    <div class="text-center py-4">
                        <p class="text-sm text-slate-500">Belum ada informasi bank yang diatur</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan Sistem
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mata Uang</label>
                    <p class="text-slate-900 font-medium"><?php echo e($setting->currency); ?></p>
                </div>

                <div class="bg-slate-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Zona Waktu</label>
                    <p class="text-slate-900 font-medium"><?php echo e($setting->timezone); ?></p>
                </div>

                <div class="bg-slate-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Format Tanggal</label>
                    <p class="text-slate-900 font-medium"><?php echo e($setting->date_format); ?></p>
                </div>

                <div class="bg-slate-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pajak (%)</label>
                    <p class="text-slate-900 font-medium"><?php echo e(number_format($setting->tax_rate, 2)); ?>%</p>
                </div>
            </div>

            <div class="mt-4 flex items-center">
                <div class="flex items-center">
                    <?php if($setting->is_active): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Aktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Tidak Aktif
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Settings Modal -->
    <div id="companySettingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 p-6 z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Edit Pengaturan Perusahaan</h3>
                        <button onclick="closeCompanySettingsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <form id="companySettingsForm" class="p-6" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Company Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Perusahaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan *</label>
                                <input type="text" name="company_name" id="company_name" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Perusahaan</label>
                                <input type="text" name="company_code" id="company_code"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="company_address" id="company_address" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                <input type="text" name="company_phone" id="company_phone"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="company_email" id="company_email"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" name="company_website" id="company_website"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Favicon -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Logo & Favicon</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Perusahaan</label>
                                
                                <!-- Current Logo Display -->
                                <div id="current_logo_display" class="mb-3 hidden">
                                    <p class="text-xs text-gray-600 mb-2">Logo saat ini:</p>
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                        <img id="current_logo_img" src="" alt="Current Logo" class="h-16 w-auto object-contain border border-gray-200 rounded">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-700 font-medium">Logo aktif</p>
                                            <button type="button" onclick="removeCurrentLogo()" class="text-xs text-red-600 hover:text-red-800 mt-1">
                                                Hapus logo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, PNG, GIF, SVG)</p>
                                
                                <!-- New Logo Preview -->
                                <div id="logo_preview" class="mt-2 hidden">
                                    <p class="text-xs text-gray-600 mb-2">Preview logo baru:</p>
                                    <img src="" alt="Logo Preview" class="h-20 object-contain border border-gray-200 rounded p-2 bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                                
                                <!-- Current Favicon Display -->
                                <div id="current_favicon_display" class="mb-3 hidden">
                                    <p class="text-xs text-gray-600 mb-2">Favicon saat ini:</p>
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                        <img id="current_favicon_img" src="" alt="Current Favicon" class="h-8 w-8 object-contain border border-gray-200 rounded">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-700 font-medium">Favicon aktif</p>
                                            <button type="button" onclick="removeCurrentFavicon()" class="text-xs text-red-600 hover:text-red-800 mt-1">
                                                Hapus favicon
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="file" name="company_favicon" id="company_favicon" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-xs text-gray-500 mt-1">Max 1MB (ICO, PNG, JPG, GIF, SVG)</p>
                                
                                <!-- New Favicon Preview -->
                                <div id="favicon_preview" class="mt-2 hidden">
                                    <p class="text-xs text-gray-600 mb-2">Preview favicon baru:</p>
                                    <img src="" alt="Favicon Preview" class="h-12 object-contain border border-gray-200 rounded p-1 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Legal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                                <input type="text" name="npwp" id="npwp"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIB</label>
                                <input type="text" name="nib" id="nib"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SIUP</label>
                                <input type="text" name="siup" id="siup"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TDP</label>
                                <input type="text" name="tdp" id="tdp"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Bank</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                                <input type="text" name="bank_name" id="bank_name"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                                <input type="text" name="bank_account_name" id="bank_account_name"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Pengaturan Sistem</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Uang *</label>
                                <select name="currency" id="currency" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="IDR">IDR - Indonesian Rupiah</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="SGD">SGD - Singapore Dollar</option>
                                    <option value="MYR">MYR - Malaysian Ringgit</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Timezone *</label>
                                <select name="timezone" id="timezone" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                    <option value="Asia/Singapore">Asia/Singapore</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Format Tanggal *</label>
                                <select name="date_format" id="date_format" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="d/m/Y">DD/MM/YYYY</option>
                                    <option value="m/d/Y">MM/DD/YYYY</option>
                                    <option value="Y-m-d">YYYY-MM-DD</option>
                                    <option value="d-m-Y">DD-MM-YYYY</option>
                                    <option value="d.m.Y">DD.MM.YYYY</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Format Waktu *</label>
                                <select name="time_format" id="time_format" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="H:i">24 Jam (HH:MM)</option>
                                    <option value="h:i A">12 Jam (hh:mm AM/PM)</option>
                                    <option value="H:i:s">24 Jam dengan detik (HH:MM:SS)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif Pajak (%) *</label>
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="11" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="is_active" id="is_active"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Keagenan Settings -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">
                            <i class="fas fa-users-cog mr-2 text-primary-600"></i>
                            Pengaturan Fee Keagenan
                        </h4>
                        
                        <!-- Enable/Disable -->
                        <div class="mb-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="agency_fee_enabled" id="agency_fee_enabled" value="1"
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Aktifkan Fee Keagenan</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                Mitra yang merekrut mitra baru akan mendapat komisi dari aktivitas mitra yang direkrut
                            </p>
                        </div>

                        <div id="agency_fee_fields" class="space-y-4">
                            <!-- Fee Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Fee Keagenan</label>
                                <select name="agency_fee_type" id="agency_fee_type"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="percentage">Persentase dari Komisi</option>
                                    <option value="fixed">Nominal Tetap</option>
                                    <option value="both">Keduanya (Persentase + Tetap)</option>
                                </select>
                            </div>

                            <!-- Percentage -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Persentase Fee (%)</label>
                                <input type="number" name="agency_fee_percentage" id="agency_fee_percentage" 
                                       step="0.01" min="0" max="100" value="10"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    Contoh: 10% berarti mitra upline dapat 10% dari komisi mitra downline
                                </p>
                            </div>

                            <!-- Fixed Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Tetap (Rp)</label>
                                <input type="number" name="agency_fee_fixed" id="agency_fee_fixed" 
                                       step="1000" min="0" value="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    Fee tetap per transaksi yang dilakukan mitra downline
                                </p>
                            </div>

                            <!-- Max Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Maksimal Level Downline</label>
                                <select name="agency_fee_max_level" id="agency_fee_max_level"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="1">1 Level (Direct Recruit Only)</option>
                                    <option value="2">2 Level (Multi-Level)</option>
                                    <option value="3">3 Level (Multi-Level)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Level 1: Hanya dari mitra yang langsung direkrut<br>
                                    Level 2+: Dari mitra yang direkrut dan mitra yang direkrut oleh mitra tersebut
                                </p>
                            </div>

                            <!-- Example Calculation -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Contoh Perhitungan:
                                </p>
                                <ul class="text-xs text-blue-800 space-y-1">
                                    <li>• Mitra A merekrut Mitra B</li>
                                    <li>• Mitra B closing jamaah, dapat komisi Rp 1.000.000</li>
                                    <li>• Fee keagenan 10%</li>
                                    <li>• Mitra A dapat: Rp 100.000 (10% x Rp 1.000.000)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Progress Bar -->
                    <div id="upload_progress_container" class="mb-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900">Mengupload file...</span>
                                <span id="upload_percentage" class="text-sm font-semibold text-blue-900">0%</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2.5">
                                <div id="upload_progress_bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="upload_status" class="text-xs text-blue-700 mt-2">Mempersiapkan upload...</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCompanySettingsModal()" 
                                id="btn_cancel"
                                class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                id="btn_save"
                                class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center">
                            <span id="btn_save_text">Simpan Pengaturan</span>
                            <svg id="btn_save_spinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Outlet Filter Function
        function changeOutlet() {
            const outletId = document.getElementById('outlet_filter').value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('outlet_id', outletId);
            window.location.href = currentUrl.toString();
        }

        // Company Settings Modal Functions
        function openCompanySettingsModal() {
            // Load current settings
            fetch('<?php echo e(route("admin.sistem.pengaturan.settings")); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateCompanySettingsForm(data.data);
                    }
                    document.getElementById('companySettingsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading settings:', error);
                    document.getElementById('companySettingsModal').classList.remove('hidden');
                });
        }

        function closeCompanySettingsModal() {
            const modal = document.getElementById('companySettingsModal');
            const form = document.getElementById('companySettingsForm');
            const progressContainer = document.getElementById('upload_progress_container');
            const progressBar = document.getElementById('upload_progress_bar');
            const progressPercentage = document.getElementById('upload_percentage');
            const btnSave = document.getElementById('btn_save');
            const btnCancel = document.getElementById('btn_cancel');
            const btnSaveText = document.getElementById('btn_save_text');
            const btnSaveSpinner = document.getElementById('btn_save_spinner');
            
            // Reset form
            form.reset();
            
            // Reset buttons
            btnSave.disabled = false;
            btnCancel.disabled = false;
            btnSaveText.textContent = 'Simpan Pengaturan';
            btnSaveSpinner.classList.add('hidden');
            
            // Hide progress bar
            progressContainer.classList.add('hidden');
            progressBar.style.width = '0%';
            progressPercentage.textContent = '0%';
            progressBar.classList.remove('bg-green-600');
            progressBar.classList.add('bg-blue-600');
            
            // Hide previews
            document.getElementById('logo_preview').classList.add('hidden');
            document.getElementById('favicon_preview').classList.add('hidden');
            
            // Hide modal
            modal.classList.add('hidden');
        }

        function populateCompanySettingsForm(settings) {
            if (!settings) return;

            // Populate form fields
            const fields = [
                'company_name', 'company_code', 'company_address', 'company_phone', 
                'company_email', 'company_website', 'npwp', 'nib', 'siup', 'tdp',
                'bank_name', 'bank_account_number', 'bank_account_name',
                'currency', 'timezone', 'date_format', 'time_format', 'tax_rate'
            ];

            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element && settings[field]) {
                    element.value = settings[field];
                }
            });

            // Set is_active
            const isActiveElement = document.getElementById('is_active');
            if (isActiveElement) {
                isActiveElement.value = settings.is_active ? '1' : '0';
            }

            // Set agency fee settings
            const agencyFeeEnabled = document.getElementById('agency_fee_enabled');
            if (agencyFeeEnabled) {
                agencyFeeEnabled.checked = settings.agency_fee_enabled || false;
                toggleAgencyFeeFields();
            }

            const agencyFeeType = document.getElementById('agency_fee_type');
            if (agencyFeeType && settings.agency_fee_type) {
                agencyFeeType.value = settings.agency_fee_type;
            }

            const agencyFeePercentage = document.getElementById('agency_fee_percentage');
            if (agencyFeePercentage && settings.agency_fee_percentage) {
                agencyFeePercentage.value = settings.agency_fee_percentage;
            }

            const agencyFeeFixed = document.getElementById('agency_fee_fixed');
            if (agencyFeeFixed && settings.agency_fee_fixed) {
                agencyFeeFixed.value = settings.agency_fee_fixed;
            }

            const agencyFeeMaxLevel = document.getElementById('agency_fee_max_level');
            if (agencyFeeMaxLevel && settings.agency_fee_max_level) {
                agencyFeeMaxLevel.value = settings.agency_fee_max_level;
            }

            // Show current logo if exists
            if (settings.logo_url) {
                const currentLogoDisplay = document.getElementById('current_logo_display');
                const currentLogoImg = document.getElementById('current_logo_img');
                currentLogoImg.src = settings.logo_url;
                currentLogoDisplay.classList.remove('hidden');
            } else {
                document.getElementById('current_logo_display').classList.add('hidden');
            }

            // Show current favicon if exists
            if (settings.favicon_url) {
                const currentFaviconDisplay = document.getElementById('current_favicon_display');
                const currentFaviconImg = document.getElementById('current_favicon_img');
                currentFaviconImg.src = settings.favicon_url;
                currentFaviconDisplay.classList.remove('hidden');
            } else {
                document.getElementById('current_favicon_display').classList.add('hidden');
            }

            // Hide new preview sections initially
            document.getElementById('logo_preview').classList.add('hidden');
            document.getElementById('favicon_preview').classList.add('hidden');
        }

        // Toggle agency fee fields visibility
        function toggleAgencyFeeFields() {
            const checkbox = document.getElementById('agency_fee_enabled');
            const fields = document.getElementById('agency_fee_fields');
            if (checkbox && fields) {
                if (checkbox.checked) {
                    fields.classList.remove('hidden');
                } else {
                    fields.classList.add('hidden');
                }
            }
        }

        // Add event listener for agency fee checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const agencyFeeCheckbox = document.getElementById('agency_fee_enabled');
            if (agencyFeeCheckbox) {
                agencyFeeCheckbox.addEventListener('change', toggleAgencyFeeFields);
                toggleAgencyFeeFields(); // Initial state
            }
        });

        // Remove current logo
        function removeCurrentLogo() {
            if (confirm('Apakah Anda yakin ingin menghapus logo perusahaan?')) {
                fetch('<?php echo e(route("admin.sistem.pengaturan.remove-file")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ type: 'logo' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Logo berhasil dihapus');
                        document.getElementById('current_logo_display').classList.add('hidden');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menghapus logo'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus logo');
                });
            }
        }

        // Remove current favicon
        function removeCurrentFavicon() {
            if (confirm('Apakah Anda yakin ingin menghapus favicon?')) {
                fetch('<?php echo e(route("admin.sistem.pengaturan.remove-file")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ type: 'favicon' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Favicon berhasil dihapus');
                        document.getElementById('current_favicon_display').classList.add('hidden');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menghapus favicon'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus favicon');
                });
            }
        }

        // Handle form submission with upload progress
        document.getElementById('companySettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnSave = document.getElementById('btn_save');
            const btnCancel = document.getElementById('btn_cancel');
            const btnSaveText = document.getElementById('btn_save_text');
            const btnSaveSpinner = document.getElementById('btn_save_spinner');
            const progressContainer = document.getElementById('upload_progress_container');
            const progressBar = document.getElementById('upload_progress_bar');
            const progressPercentage = document.getElementById('upload_percentage');
            const uploadStatus = document.getElementById('upload_status');
            
            // Check if there are files to upload
            const hasLogo = document.getElementById('company_logo').files.length > 0;
            const hasFavicon = document.getElementById('company_favicon').files.length > 0;
            const hasFiles = hasLogo || hasFavicon;
            
            // Disable buttons and show loading
            btnSave.disabled = true;
            btnCancel.disabled = true;
            btnSaveText.textContent = 'Menyimpan...';
            btnSaveSpinner.classList.remove('hidden');
            
            // Show progress bar if uploading files
            if (hasFiles) {
                progressContainer.classList.remove('hidden');
                uploadStatus.textContent = 'Mempersiapkan upload...';
            }
            
            // Use XMLHttpRequest for upload progress tracking
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable && hasFiles) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressPercentage.textContent = percentComplete + '%';
                    
                    if (percentComplete < 100) {
                        uploadStatus.textContent = 'Mengupload file... (' + formatBytes(e.loaded) + ' / ' + formatBytes(e.total) + ')';
                    } else {
                        uploadStatus.textContent = 'Memproses data...';
                    }
                }
            });
            
            // Handle completion
            xhr.addEventListener('load', function() {
                try {
                    const data = JSON.parse(xhr.responseText);
                    
                    if (xhr.status === 200 && data.success) {
                        if (hasFiles) {
                            uploadStatus.textContent = 'Upload berhasil!';
                            progressBar.classList.remove('bg-blue-600');
                            progressBar.classList.add('bg-green-600');
                        }
                        
                        setTimeout(() => {
                            alert('Pengaturan berhasil disimpan!');
                            closeCompanySettingsModal();
                            location.reload();
                        }, 500);
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    resetFormButtons();
                }
            });
            
            // Handle errors
            xhr.addEventListener('error', function() {
                console.error('Network error');
                alert('Terjadi kesalahan jaringan saat menyimpan pengaturan');
                resetFormButtons();
            });
            
            // Handle timeout
            xhr.addEventListener('timeout', function() {
                alert('Request timeout. Silakan coba lagi.');
                resetFormButtons();
            });
            
            // Send request
            xhr.open('POST', '<?php echo e(route("admin.sistem.pengaturan.update")); ?>');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.setRequestHeader('X-HTTP-Method-Override', 'PUT');
            xhr.timeout = 60000; // 60 seconds timeout
            xhr.send(formData);
            
            // Helper function to reset buttons
            function resetFormButtons() {
                btnSave.disabled = false;
                btnCancel.disabled = false;
                btnSaveText.textContent = 'Simpan Pengaturan';
                btnSaveSpinner.classList.add('hidden');
                progressContainer.classList.add('hidden');
                progressBar.style.width = '0%';
                progressPercentage.textContent = '0%';
                progressBar.classList.remove('bg-green-600');
                progressBar.classList.add('bg-blue-600');
            }
        });
        
        // Helper function to format bytes
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Handle file preview
        document.getElementById('company_logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logo_preview');
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('company_favicon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('favicon_preview');
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sistem\pengaturan\index.blade.php ENDPATH**/ ?>