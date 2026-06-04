<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pendaftaran - HM Tour</title>
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
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <?php
                $logoPath = public_path('images/hm-tour-logo.png');
                $logoExists = file_exists($logoPath);
            ?>
            <?php if($logoExists): ?>
                <img src="<?php echo e(asset('images/hm-tour-logo.png')); ?>" alt="HM Tour" class="h-16 mx-auto mb-4">
            <?php else: ?>
                <div class="h-16 w-16 mx-auto mb-4 bg-green-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">HM</span>
                </div>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-gray-800">Pembayaran Pendaftaran</h1>
            <p class="text-gray-600 mt-2"><?php echo e($program->name); ?></p>
        </div>

        <?php if($errors->any()): ?>
        <div class="max-w-2xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Payment Container -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-8">
            
            <!-- Program Info -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold mb-2"><?php echo e($program->name); ?></h2>
                        <p class="text-green-100 text-sm"><?php echo e($program->description); ?></p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold"><?php echo e($program->formatted_fee); ?></div>
                        <div class="text-xs text-green-100">Biaya Pendaftaran</div>
                    </div>
                </div>
            </div>

            <!-- Registrant Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Data Pendaftar</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Nama:</span>
                        <span class="font-medium text-gray-900 ml-2"><?php echo e($registrationData['full_name']); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Username:</span>
                        <span class="font-medium text-gray-900 ml-2"><?php echo e($registrationData['username']); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-500">HP:</span>
                        <span class="font-medium text-gray-900 ml-2"><?php echo e($registrationData['phone_number']); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <span class="font-medium text-gray-900 ml-2"><?php echo e($registrationData['email']); ?></span>
                    </div>
                </div>
            </div>

            <?php if($program->registration_fee > 0): ?>
            <!-- Payment Instructions -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i>
                    Pilih Metode Pembayaran
                </h3>

                <!-- Payment Method Tabs -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button type="button" onclick="selectAffPayMethod('transfer')" id="affMethodTransfer"
                        class="border-2 border-green-500 bg-green-50 rounded-xl p-4 text-center transition-all">
                        <i class="fas fa-university text-green-600 text-2xl mb-2"></i>
                        <div class="font-bold text-sm text-gray-900">Transfer Bank</div>
                        <div class="text-xs text-gray-500">Upload bukti transfer</div>
                    </button>
                    <button type="button" onclick="selectAffPayMethod('qris')" id="affMethodQris"
                        class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-purple-300">
                        <i class="fas fa-qrcode text-purple-600 text-2xl mb-2"></i>
                        <div class="font-bold text-sm text-gray-900">QRIS</div>
                        <div class="text-xs text-gray-500">Scan & bayar langsung</div>
                    </button>
                </div>

                <!-- Transfer Section -->
                <div id="affTransferSection">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Silakan transfer ke rekening berikut:</p>
                                <div class="mt-2 space-y-1">
                                    <p><strong>Bank:</strong> BCA</p>
                                    <p><strong>No. Rekening:</strong> 1234567890</p>
                                    <p><strong>Atas Nama:</strong> HM Tour</p>
                                    <p><strong>Jumlah:</strong> <span class="text-lg font-bold"><?php echo e($program->formatted_fee); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo e(route('affiliate.payment.process', $token)); ?>" method="POST" enctype="multipart/form-data" id="paymentForm">
                        <?php echo csrf_field(); ?>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Bukti Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="payment_proof" accept="image/*" required id="proofInput"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Max 2MB</p>
                            <div id="proofPreview" class="mt-3 hidden">
                                <img src="" alt="Preview" class="w-full max-w-md rounded-lg border-2 border-gray-200">
                            </div>
                        </div>
                        <button type="submit" id="submitBtn"
                                class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition text-lg">
                            <i class="fas fa-check-circle mr-2"></i> Konfirmasi Transfer
                        </button>
                    </form>
                </div>

                <!-- QRIS Section -->
                <div id="affQrisSection" style="display: none;">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                            <div>
                                <div class="font-bold text-purple-900">Bayar dengan QRIS</div>
                                <div class="text-xs text-purple-600">GoPay, OVO, DANA, ShopeePay, atau mobile banking</div>
                            </div>
                        </div>
                        <div class="text-sm text-purple-700">
                            <strong>Jumlah:</strong> <?php echo e($program->formatted_fee); ?>

                        </div>
                    </div>

                    <!-- QR Display -->
                    <div id="affQrisDisplay" style="display: none;">
                        <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center mb-4">
                            <div id="affQrisQrCode" class="flex justify-center mb-4"></div>
                            <div class="text-sm text-gray-600 mb-2">Scan QR di atas dengan aplikasi pembayaran Anda</div>
                            <div class="text-xs text-orange-600 font-semibold">⏱️ Berlaku: <span id="affQrisCountdown">30:00</span></div>
                            <div class="mt-3 flex items-center justify-center gap-2 text-sm text-blue-600">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menunggu pembayaran...
                            </div>
                        </div>
                    </div>

                    <!-- QRIS Success -->
                    <div id="affQrisSuccess" style="display: none;">
                        <div class="bg-green-50 border-2 border-green-500 rounded-xl p-6 text-center">
                            <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                            <div class="font-bold text-green-800 text-lg mb-1">Pembayaran Berhasil! 🎉</div>
                            <div class="text-sm text-green-700 mb-4">Pendaftaran Anda sedang diproses...</div>
                            <button onclick="location.reload()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl">
                                Lanjutkan
                            </button>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button type="button" id="affBtnGenerateQris" onclick="affGenerateQris()"
                        class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg font-medium transition text-lg">
                        <i class="fas fa-qrcode mr-2"></i> Tampilkan QR Code QRIS
                    </button>
                </div>
            </div>
            <?php else: ?>
            <!-- Free Program -->
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-gift text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Program Gratis!</h3>
                <p class="text-gray-600 mb-6">Tidak ada biaya pendaftaran untuk program ini</p>
                
                <form action="<?php echo e(route('affiliate.payment.process', $token)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition text-lg">
                        <i class="fas fa-check-circle mr-2"></i> Selesaikan Pendaftaran
                    </button>
                </form>
            </div>
            <?php endif; ?>

        </div>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="<?php echo e(route('affiliate.register')); ?>" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pendaftaran
            </a>
        </div>
    </div>

    <script>
        // Payment proof preview
        document.getElementById('proofInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                    e.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('#proofPreview img').src = e.target.result;
                    document.getElementById('proofPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Memproses Pembayaran...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            this.submit();
        });

        // ==========================================
        // QRIS PAYMENT FOR AFFILIATE
        // ==========================================
        let affQrisCheckInterval = null;
        let affQrisCountdownInterval = null;

        function selectAffPayMethod(method) {
            document.getElementById('affTransferSection').style.display = method === 'transfer' ? 'block' : 'none';
            document.getElementById('affQrisSection').style.display = method === 'qris' ? 'block' : 'none';
            
            document.getElementById('affMethodTransfer').className = method === 'transfer' 
                ? 'border-2 border-green-500 bg-green-50 rounded-xl p-4 text-center transition-all'
                : 'border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-green-300';
            document.getElementById('affMethodQris').className = method === 'qris'
                ? 'border-2 border-purple-500 bg-purple-50 rounded-xl p-4 text-center transition-all'
                : 'border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-purple-300';
        }

        function affGenerateQris() {
            const btn = document.getElementById('affBtnGenerateQris');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Membuat QRIS...';

            fetch('<?php echo e(route("affiliate.payment.qris.generate", $token)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAffQris(data.data);
                } else {
                    Swal.fire('Error', data.message || 'Gagal membuat QRIS', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-qrcode mr-2"></i> Tampilkan QR Code QRIS';
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-qrcode mr-2"></i> Tampilkan QR Code QRIS';
            });
        }

        function showAffQris(data) {
            document.getElementById('affBtnGenerateQris').style.display = 'none';
            document.getElementById('affQrisDisplay').style.display = 'block';

            // Render QR
            const container = document.getElementById('affQrisQrCode');
            container.innerHTML = '<div id="affQrDiv"></div>';
            
            if (typeof QRCode !== 'undefined' && QRCode.CorrectLevel) {
                new QRCode(document.getElementById('affQrDiv'), {
                    text: data.qris_content, width: 260, height: 260,
                    correctLevel: QRCode.CorrectLevel.M
                });
            } else {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
                script.onload = function() {
                    new QRCode(document.getElementById('affQrDiv'), {
                        text: data.qris_content, width: 260, height: 260,
                        correctLevel: QRCode.CorrectLevel.M
                    });
                };
                document.head.appendChild(script);
            }

            // Countdown
            const expiry = new Date(data.expired_at || Date.now() + 30*60*1000);
            affQrisCountdownInterval = setInterval(function() {
                const diff = expiry - new Date();
                if (diff <= 0) {
                    clearInterval(affQrisCountdownInterval);
                    clearInterval(affQrisCheckInterval);
                    document.getElementById('affQrisDisplay').style.display = 'none';
                    document.getElementById('affBtnGenerateQris').style.display = 'block';
                    document.getElementById('affBtnGenerateQris').disabled = false;
                    document.getElementById('affBtnGenerateQris').innerHTML = '<i class="fas fa-redo mr-2"></i> Buat Ulang QRIS';
                    return;
                }
                const m = Math.floor(diff/60000), s = Math.floor((diff%60000)/1000);
                document.getElementById('affQrisCountdown').textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            }, 1000);

            // Poll status
            affQrisCheckInterval = setInterval(function() {
                fetch('<?php echo e(url("/affiliate/qris")); ?>/' + data.trx_number + '/check-status', {
                    headers: {'Accept': 'application/json'}
                })
                .then(r => r.json())
                .then(res => {
                    if (res.paid) {
                        clearInterval(affQrisCheckInterval);
                        clearInterval(affQrisCountdownInterval);
                        document.getElementById('affQrisDisplay').style.display = 'none';
                        document.getElementById('affQrisSuccess').style.display = 'block';
                    }
                })
                .catch(e => console.error(e));
            }, 5000);
        }
    </script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\affiliate\payment.blade.php ENDPATH**/ ?>