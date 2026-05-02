<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Affiliator - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Favicon -->
    <?php
        try {
            $settings = \App\Models\CompanySetting::first();
            $faviconUrl = $settings && $settings->logo ? asset('storage/' . $settings->logo) : url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        } catch (\Exception $e) {
            $faviconUrl = url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        }
    ?>
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>">
    <style>
        .program-card {
            position: relative;
            transition: all 0.3s ease;
        }
        .program-card:hover {
            transform: translateY(-2px);
            border-color: #10b981 !important;
        }
        .program-card.selected {
            border-color: #10b981 !important;
            border-width: 3px !important;
            background-color: #f0fdf4 !important;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1) !important;
        }
        .selected-indicator {
            animation: scaleIn 0.3s ease;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .program-wrapper label {
            display: block;
            width: 100%;
        }
        .program-wrapper input[type="radio"]:checked + label .program-card {
            border-color: #10b981 !important;
            border-width: 3px !important;
            background-color: #f0fdf4 !important;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1) !important;
        }
        .program-wrapper input[type="radio"]:checked + label .selected-indicator {
            display: flex !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>" 
                 alt="HM Tour" 
                 class="h-20 mx-auto mb-4"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div style="display:none" class="h-20 w-20 mx-auto mb-4 bg-green-600 rounded-full items-center justify-center">
                <span class="text-white text-3xl font-bold">HM</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Program Kemitraan</h1>
            <p class="text-gray-600 mt-2">Bergabunglah dengan HM Tour dan raih penghasilan tambahan</p>
        </div>

        <?php if(session('success')): ?>
        <div class="max-w-2xl mx-auto mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="max-w-2xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Form Container -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-8" id="registrationForm">
            
            <form action="<?php echo e(route('affiliate.register.store')); ?>" method="POST" enctype="multipart/form-data" id="affiliateForm">
                <?php echo csrf_field(); ?>
                
                <!-- Step 1: Data Diri -->
                <div class="step-content" id="step1">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3">1</span>
                        Data Diri
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="<?php echo e(old('full_name')); ?>" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" value="<?php echo e(old('username')); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="username_anda">
                                <p class="text-xs text-gray-500 mt-1">Untuk login dan link referral</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required
                                           class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           placeholder="Min. 8 karakter">
                                    <button type="button" onclick="togglePassword('password', 'togglePassword1')" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-eye" id="togglePassword1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('password_confirmation', 'togglePassword2')" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-eye" id="togglePassword2"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Password harus sama</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                                <input type="text" name="phone_number" value="<?php echo e(old('phone_number')); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="08xxxxxxxxxx">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="email@example.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil (Opsional)</label>
                            <input type="file" name="photo" accept="image/*" id="photoInput"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Foto akan otomatis dikompres. Max 2MB</p>
                            <div id="photoPreview" class="mt-3 hidden">
                                <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                                <input type="text" name="bank_name" value="<?php echo e(old('bank_name')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="BCA">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No. Rekening</label>
                                <input type="text" name="bank_account_number" value="<?php echo e(old('bank_account_number')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="1234567890">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Atas Nama</label>
                                <input type="text" name="bank_account_name" value="<?php echo e(old('bank_account_name')); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Nama pemilik">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" onclick="nextStep(2)"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Pilih Program -->
                <div class="step-content hidden" id="step2">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3">2</span>
                        Pilih Program Kemitraan
                    </h2>

                    <div class="space-y-4">
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="program-wrapper">
                            <input type="radio" name="partnership_program_id" value="<?php echo e($program->id); ?>" 
                                   id="program-<?php echo e($program->id); ?>"
                                   data-fee="<?php echo e($program->registration_fee); ?>"
                                   data-commission="<?php echo e($program->min_sale_commission); ?>"
                                   data-requires-booking="<?php echo e($program->requires_previous_booking ? 'true' : 'false'); ?>"
                                   data-slug="<?php echo e($program->slug); ?>"
                                   class="hidden program-radio" required>
                            <label for="program-<?php echo e($program->id); ?>" class="block cursor-pointer">
                            <div class="program-card border-2 border-gray-200 rounded-xl p-6 hover:border-green-500 transition-all duration-200">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800"><?php echo e($program->name); ?></h3>
                                        <?php if($program->target_audience): ?>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo e($program->target_audience); ?></p>
                                        <?php endif; ?>
                                        <?php if($program->requires_previous_booking): ?>
                                        <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                            <i class="fas fa-star mr-1"></i>Khusus Alumni Jamaah
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600"><?php echo e($program->formatted_fee); ?></div>
                                        <div class="text-xs text-gray-500">Biaya Pendaftaran</div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3"><?php echo e($program->description); ?></p>
                                
                                <!-- Expand/Collapse Button -->
                                <button type="button" 
                                        onclick="event.stopPropagation(); toggleDetails(<?php echo e($program->id); ?>)"
                                        class="w-full text-sm text-green-600 hover:text-green-700 font-medium py-2 px-4 border border-green-200 rounded-lg hover:bg-green-50 transition flex items-center justify-center mb-3">
                                    <i class="fas fa-chevron-down mr-2" id="icon-<?php echo e($program->id); ?>"></i>
                                    <span id="text-<?php echo e($program->id); ?>">Lihat Detail Benefit</span>
                                </button>

                                <!-- Detail Benefits (Hidden by default) -->
                                <div id="details-<?php echo e($program->id); ?>" class="hidden mt-3 pt-3 border-t border-gray-200">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3">
                                        <i class="fas fa-gift text-green-500 mr-2"></i>Benefit yang Anda Dapatkan:
                                    </h4>
                                    <?php if($program->benefits && is_array($program->benefits)): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <?php $__currentLoopData = $program->benefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-start text-xs text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                            <span><?php echo e($benefit); ?></span>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-xs text-gray-500 italic">Detail benefit akan segera diupdate.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-200 mt-3">
                                    <div class="text-sm">
                                        <span class="text-gray-600">Komisi Minimal:</span>
                                        <span class="font-bold text-green-600 ml-2"><?php echo e($program->formatted_commission); ?></span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-600">PPC:</span>
                                        <span class="font-bold text-blue-600 ml-2">Rp <?php echo e(number_format($program->default_ppc_commission, 0, ',', '.')); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Selected Indicator -->
                                <div class="selected-indicator hidden absolute top-4 right-4 bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                            </div>
                            </label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Dropdown Jenjang (muncul sesuai program yang dipilih) -->
                    <div id="hierarchy-section" class="hidden mt-6 p-5 bg-blue-50 border border-blue-200 rounded-xl">
                        <h3 class="text-sm font-bold text-blue-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-sitemap"></i> Jenjang Kemitraan (Opsional)
                        </h3>
                        <p class="text-xs text-blue-600 mb-4">Jika Anda bergabung melalui referensi mitra tertentu, pilih di bawah ini. Kosongkan jika tidak ada.</p>

                        <!-- Dropdown Cabang (HM Master) - tampil untuk Seller, Partner, Leader -->
                        <div id="dropdown-master" class="hidden mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building text-purple-500 mr-1"></i> Cabang (HM Master)
                            </label>
                            <select name="upline_master_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Pusat (Default) --</option>
                                <?php $__currentLoopData = $hmMasters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $master): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($master->id); ?>" <?php echo e(old('upline_master_id') == $master->id ? 'selected' : ''); ?>>
                                    <?php echo e($master->full_name); ?> ({{ $master->username }})
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Dropdown Leader (HM Leader) - tampil untuk Seller, Partner -->
                        <div id="dropdown-leader" class="hidden mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-tie text-indigo-500 mr-1"></i> Leader (HM Leader)
                            </label>
                            <select name="upline_leader_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Tidak Ada --</option>
                                <?php $__currentLoopData = $hmLeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leader): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($leader->id); ?>" <?php echo e(old('upline_leader_id') == $leader->id ? 'selected' : ''); ?>>
                                    <?php echo e($leader->full_name); ?> ({{ $leader->username }})
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Dropdown Partner (HM Partner) - tampil untuk Seller saja -->
                        <div id="dropdown-partner" class="hidden mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-handshake text-green-500 mr-1"></i> Partner (HM Partner)
                            </label>
                            <select name="upline_partner_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Tidak Ada --</option>
                                <?php $__currentLoopData = $hmPartners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partner->id); ?>" <?php echo e(old('upline_partner_id') == $partner->id ? 'selected' : ''); ?>>
                                    <?php echo e($partner->full_name); ?> ({{ $partner->username }})
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(1)"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-arrow-right mr-2"></i> Lanjutkan Pendaftaran
                        </button>
                    </div>
                </div>

            </form>

        </div>

        <!-- Login Link -->
        <div class="text-center mt-6">
            <p class="text-gray-600">
                Sudah punya akun? 
                <a href="<?php echo e(route('affiliate.login')); ?>" class="text-green-600 hover:text-green-700 font-medium">Login di sini</a>
            </p>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let selectedProgram = null;

        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-select program from URL parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const programId = urlParams.get('program');
            
            if (programId) {
                const programRadio = document.getElementById(`program-${programId}`);
                if (programRadio) {
                    // Auto-select the program
                    programRadio.checked = true;
                    programRadio.dispatchEvent(new Event('change'));
                    
                    // Auto-navigate to step 2
                    setTimeout(() => {
                        nextStep(2);
                    }, 500);
                }
            }
        });

        // Toggle details function
        function toggleDetails(programId) {
            const detailsDiv = document.getElementById(`details-${programId}`);
            const icon = document.getElementById(`icon-${programId}`);
            const text = document.getElementById(`text-${programId}`);
            
            if (detailsDiv.classList.contains('hidden')) {
                detailsDiv.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                text.textContent = 'Sembunyikan Detail';
            } else {
                detailsDiv.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                text.textContent = 'Lihat Detail Benefit';
            }
        }

        // Photo preview with compression
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                    e.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('#photoPreview img').src = e.target.result;
                    document.getElementById('photoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Program selection
        document.querySelectorAll('.program-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove highlight from all cards
                document.querySelectorAll('.program-card').forEach(card => card.classList.remove('selected'));
                document.querySelectorAll('.selected-indicator').forEach(ind => ind.classList.add('hidden'));
                
                const selectedCard = this.closest('.program-wrapper').querySelector('.program-card');
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                    const ind = selectedCard.querySelector('.selected-indicator');
                    if (ind) ind.classList.remove('hidden');
                }
                
                selectedProgram = {
                    fee: parseFloat(this.dataset.fee),
                    commission: parseFloat(this.dataset.commission),
                    requiresBooking: this.dataset.requiresBooking === 'true',
                    slug: this.dataset.slug,
                };

                updateHierarchyDropdowns(this.dataset.slug);
            });
        });

        function updateHierarchyDropdowns(slug) {
            const section  = document.getElementById('hierarchy-section');
            const dMaster  = document.getElementById('dropdown-master');
            const dLeader  = document.getElementById('dropdown-leader');
            const dPartner = document.getElementById('dropdown-partner');

            section.classList.add('hidden');
            dMaster.classList.add('hidden');
            dLeader.classList.add('hidden');
            dPartner.classList.add('hidden');

            if (slug === 'hm-seller') {
                section.classList.remove('hidden');
                dMaster.classList.remove('hidden');
                dLeader.classList.remove('hidden');
                dPartner.classList.remove('hidden');
            } else if (slug === 'hm-partner') {
                section.classList.remove('hidden');
                dMaster.classList.remove('hidden');
                dLeader.classList.remove('hidden');
            } else if (slug === 'hm-leader') {
                section.classList.remove('hidden');
                dMaster.classList.remove('hidden');
            }
        }

        function nextStep(step) {
            // If no step parameter provided, go to next step
            if (!step) {
                step = currentStep + 1;
            }
            
            // Validasi step sebelumnya
            if (step === 2) {
                const requiredFields = ['full_name', 'username', 'password', 'password_confirmation', 'phone_number', 'email'];
                let isValid = true;
                let errorMessage = '';
                
                requiredFields.forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('border-red-500');
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });
                
                // Check if passwords match
                const password = document.querySelector('[name="password"]').value;
                const passwordConfirmation = document.querySelector('[name="password_confirmation"]').value;
                
                if (password !== passwordConfirmation) {
                    isValid = false;
                    errorMessage = 'Password dan konfirmasi password tidak cocok!';
                    document.querySelector('[name="password"]').classList.add('border-red-500');
                    document.querySelector('[name="password_confirmation"]').classList.add('border-red-500');
                } else if (password.length < 8) {
                    isValid = false;
                    errorMessage = 'Password minimal 8 karakter!';
                    document.querySelector('[name="password"]').classList.add('border-red-500');
                }
                
                if (!isValid) {
                    Swal.fire('Error', errorMessage || 'Mohon lengkapi semua field yang wajib diisi', 'error');
                    return;
                }
            }

            const currentStepEl = document.getElementById(`step${currentStep}`);
            const nextStepEl = document.getElementById(`step${step}`);
            
            // Add null checks before accessing classList
            if (currentStepEl) {
                currentStepEl.classList.add('hidden');
            }
            if (nextStepEl) {
                nextStepEl.classList.remove('hidden');
            }
            
            currentStep = step;
            window.scrollTo(0, 0);
        }

        function prevStep(step) {
            document.getElementById(`step${currentStep}`).classList.add('hidden');
            document.getElementById(`step${step}`).classList.remove('hidden');
            currentStep = step;
            window.scrollTo(0, 0);
        }

        // Form submission
        document.getElementById('affiliateForm').addEventListener('submit', function(e) {
            // Check if program is selected
            const selectedRadio = document.querySelector('.program-radio:checked');
            if (!selectedRadio) {
                e.preventDefault();
                Swal.fire('Error', 'Pilih program kemitraan terlebih dahulu', 'error');
                return;
            }
            
            // Don't prevent default - let form submit normally
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/affiliate/register.blade.php ENDPATH**/ ?>