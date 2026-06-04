<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Form Manifest - <?php echo e($booking->booking_code); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Nunito', sans-serif; }
        .bg-green-gradient { background: linear-gradient(135deg, #2E7D32, #4CAF50); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto py-8 px-4">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="bg-green-gradient text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3-WHITE.png')); ?>" 
                             alt="HM Tour" class="h-10 w-auto object-contain mb-2" 
                             onerror="this.style.display='none'">
                        <h1 class="text-2xl font-black">Form Manifest Jamaah</h1>
                        <p class="text-green-100 text-sm mt-1">Lengkapi data perjalanan Anda</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-black"><?php echo e($booking->booking_code); ?></div>
                        <div class="text-green-200 text-xs mt-1"><?php echo e($booking->travelPackage->package_name ?? 'Paket Travel'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <?php if(session('success')): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700 font-semibold"><?php echo e(session('success')); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if(session('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-700 font-semibold"><?php echo e(session('error')); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Box -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 m-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-bold mb-1">📸 Fitur OCR Passport Tersedia!</p>
                        <p>Upload foto passport Anda, sistem kami akan otomatis membaca dan mengisi data untuk Anda. Pastikan foto jelas dan tidak blur.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('public.booking.manifest.submit', $booking->id)); ?>" method="POST" enctype="multipart/form-data" id="manifestForm">
            <?php echo csrf_field(); ?>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-6 space-y-6">

                <!-- Section 1: Upload Passport (OCR) -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Upload Foto Passport <span class="text-red-500">*</span>
                    </h2>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 transition-all" id="uploadArea">
                        <input type="file" name="passport_foto" id="passport_foto" accept="image/*" required class="hidden" onchange="handlePassportUpload(this)">
                        <label for="passport_foto" class="cursor-pointer">
                            <div id="uploadPreview">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="font-bold text-gray-700">Klik untuk upload foto passport</div>
                                <div class="text-xs text-gray-500 mt-1">JPG, PNG (Max 5MB)</div>
                                <div class="text-xs text-blue-600 mt-2">✨ Data akan otomatis terisi dengan OCR</div>
                            </div>
                        </label>
                    </div>
                    <?php $__errorArgs = ['passport_foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Section 2: Data Passport -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Data Passport
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Passport <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="passport_nomor" id="passport_nomor" required
                                   value="<?php echo e(old('passport_nomor', $booking->jamaah->passport_nomor ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Contoh: A1234567">
                            <?php $__errorArgs = ['passport_nomor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama di Passport
                            </label>
                            <input type="text" name="passport_nama" id="passport_nama"
                                   value="<?php echo e(old('passport_nama', $booking->jamaah->passport_nama ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Sesuai passport">
                            <?php $__errorArgs = ['passport_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" name="passport_tanggal_lahir" id="passport_tanggal_lahir"
                                   value="<?php echo e(old('passport_tanggal_lahir', $booking->jamaah->passport_tanggal_lahir ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['passport_tanggal_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Kadaluarsa <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="passport_tanggal_kadaluarsa" id="passport_tanggal_kadaluarsa" required
                                   value="<?php echo e(old('passport_tanggal_kadaluarsa', $booking->jamaah->passport_tanggal_kadaluarsa ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['passport_tanggal_kadaluarsa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kewarganegaraan
                            </label>
                            <input type="text" name="passport_kewarganegaraan" id="passport_kewarganegaraan"
                                   value="<?php echo e(old('passport_kewarganegaraan', $booking->jamaah->passport_kewarganegaraan ?? 'Indonesia')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Indonesia">
                            <?php $__errorArgs = ['passport_kewarganegaraan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Upload KTP (Optional) -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                        Upload KTP (Opsional)
                    </h2>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 transition-all">
                        <input type="file" name="ktp_foto" id="ktp_foto" accept="image/*" class="hidden" onchange="handleKtpUpload(this)">
                        <label for="ktp_foto" class="cursor-pointer">
                            <div id="ktpUploadPreview">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="font-semibold text-gray-700 text-sm">Upload KTP (Opsional)</div>
                                <div class="text-xs text-gray-500 mt-1">JPG, PNG (Max 5MB)</div>
                            </div>
                        </label>
                    </div>
                    <?php $__errorArgs = ['ktp_foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- KTP Data Fields (Optional) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                NIK KTP (16 digit)
                            </label>
                            <input type="text" name="ktp_nik" id="ktp_nik" maxlength="16" pattern="\d{16}"
                                   value="<?php echo e(old('ktp_nik', $booking->jamaah->ktp_nik ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="16 digit angka">
                            <?php $__errorArgs = ['ktp_nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama di KTP
                            </label>
                            <input type="text" name="ktp_nama" id="ktp_nama"
                                   value="<?php echo e(old('ktp_nama', $booking->jamaah->ktp_nama ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Sesuai KTP">
                            <?php $__errorArgs = ['ktp_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tempat Lahir
                            </label>
                            <input type="text" name="ktp_tempat_lahir" id="ktp_tempat_lahir"
                                   value="<?php echo e(old('ktp_tempat_lahir', $booking->jamaah->ktp_tempat_lahir ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Kota kelahiran">
                            <?php $__errorArgs = ['ktp_tempat_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" name="ktp_tanggal_lahir" id="ktp_tanggal_lahir"
                                   value="<?php echo e(old('ktp_tanggal_lahir', $booking->jamaah->ktp_tanggal_lahir ?? '')); ?>"
                                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['ktp_tanggal_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat KTP
                            </label>
                            <textarea name="ktp_alamat" id="ktp_alamat" rows="2"
                                      class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Alamat lengkap sesuai KTP"><?php echo e(old('ktp_alamat', $booking->jamaah->ktp_alamat ?? '')); ?></textarea>
                            <?php $__errorArgs = ['ktp_alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="submitBtn"
                            class="w-full bg-green-gradient text-white font-black py-4 rounded-xl text-lg hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        SIMPAN DATA MANIFEST
                    </button>
                </div>

                <!-- Info Footer -->
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600">
                    <p class="font-semibold mb-1">📝 Catatan Penting:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Passport wajib diupload dan data nomor + tanggal kadaluarsa wajib diisi</li>
                        <li>KTP bersifat opsional, namun disarankan untuk dilengkapi</li>
                        <li>Data yang sudah disimpan dapat diubah kembali jika ada kesalahan</li>
                        <li>Hubungi kami jika ada pertanyaan atau kesulitan</li>
                    </ul>
                </div>

            </div>
        </form>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>&copy; <?php echo e(date('Y')); ?> HM Tour & Travel. Berizin Kemenag RI.</p>
        </div>

    </div>

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function handlePassportUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                // Show preview
                const preview = document.getElementById('uploadPreview');
                preview.innerHTML = `
                    <svg class="w-16 h-16 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="font-bold text-green-700">${fileName}</div>
                    <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
                    <div class="text-xs text-blue-600 mt-2">✨ Sedang memproses OCR...</div>
                `;

                // Call OCR API
                const formData = new FormData();
                formData.append('passport_image', file);

                fetch('<?php echo e(route("public.manifest.ocr-passport")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Fill form fields
                        if (data.data.passport_nomor) {
                            document.getElementById('passport_nomor').value = data.data.passport_nomor;
                        }
                        if (data.data.passport_nama) {
                            document.getElementById('passport_nama').value = data.data.passport_nama;
                        }
                        if (data.data.passport_tanggal_lahir) {
                            document.getElementById('passport_tanggal_lahir').value = data.data.passport_tanggal_lahir;
                        }
                        if (data.data.passport_tanggal_kadaluarsa) {
                            document.getElementById('passport_tanggal_kadaluarsa').value = data.data.passport_tanggal_kadaluarsa;
                        }
                        if (data.data.passport_kewarganegaraan) {
                            document.getElementById('passport_kewarganegaraan').value = data.data.passport_kewarganegaraan;
                        }

                        preview.innerHTML = `
                            <svg class="w-16 h-16 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="font-bold text-green-700">${fileName}</div>
                            <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
                            <div class="text-xs text-green-600 mt-2">✅ OCR berhasil! Data telah terisi otomatis</div>
                            <div class="text-xs text-gray-500 mt-1">Periksa dan lengkapi data jika perlu</div>
                        `;
                    } else {
                        throw new Error(data.message || 'OCR gagal');
                    }
                })
                .catch(error => {
                    console.error('OCR Error:', error);
                    preview.innerHTML = `
                        <svg class="w-16 h-16 mx-auto mb-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="font-bold text-yellow-700">${fileName}</div>
                        <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
                        <div class="text-xs text-yellow-600 mt-2">⚠️ OCR gagal, silakan isi manual</div>
                        <div class="text-xs text-gray-500 mt-1">File tetap akan diupload</div>
                    `;
                });
            }
        }

        function handleKtpUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                const preview = document.getElementById('ktpUploadPreview');
                preview.innerHTML = `
                    <svg class="w-12 h-12 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="font-semibold text-green-700 text-sm">${fileName}</div>
                    <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
                    <div class="text-xs text-green-600 mt-1">✅ File berhasil dipilih</div>
                `;
            }
        }

        // Form validation
        document.getElementById('manifestForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Menyimpan...
            `;
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\public\manifest-form.blade.php ENDPATH**/ ?>