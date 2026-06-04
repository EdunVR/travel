<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - HM Tour</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .card-header { 
            background: linear-gradient(135deg, #2E7D32, #4CAF50); 
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #2E7D32, #4CAF50); 
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.4);
        }
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .page-header h2 {
            color: #2E7D32;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .table td {
            padding: 12px 8px;
        }
        .upload-area {
            border: 2px dashed #4CAF50;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover {
            background: #e8f5e9;
            border-color: #2E7D32;
        }
        .upload-area i {
            font-size: 48px;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        .file-preview {
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            display: none;
        }
        .file-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
        }
        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <!-- Header -->
                <div class="page-header text-center">
                    <h2><i class="fas fa-check-circle text-success mr-2"></i>Konfirmasi Pembayaran</h2>
                    <p class="text-muted mb-0"><?php echo e($package->package_name); ?></p>
                    <span class="badge badge-success badge-custom mt-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <?php echo e(\Carbon\Carbon::parse($package->departure_date)->format('d M Y')); ?>

                    </span>
                </div>
                
                <!-- Card Info Booking -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user mr-2"></i>
                            Informasi Jamaah
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td width="150">Nama</td>
                                <td>: <?php echo e($bookingData['jamaah_name']); ?></td>
                            </tr>
                            <tr>
                                <td>No. HP</td>
                                <td>: <?php echo e($bookingData['jamaah_phone']); ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>: <?php echo e($bookingData['jamaah_email']); ?></td>
                            </tr>
                            <tr>
                                <td>Tipe Kamar</td>
                                <td>: <?php echo e(ucfirst($bookingData['room_type'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Card Anggota Keluarga (jika ada) -->
                <?php if(!empty($bookingData['family_members'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users mr-2"></i>
                            Anggota Keluarga
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                    <th>Tanggal Lahir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $bookingData['family_members']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($fm['nama'] ?? '-'); ?></td>
                                    <td><?php echo e($fm['hubungan'] ?? '-'); ?></td>
                                    <td>
                                        <?php if(!empty($fm['tanggal_lahir'])): ?>
                                            <?php echo e(\Carbon\Carbon::parse($fm['tanggal_lahir'])->format('d M Y')); ?>

                                            (<?php echo e(\Carbon\Carbon::parse($fm['tanggal_lahir'])->age); ?> tahun)
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Card Perlengkapan (jika ada) -->
                <?php if(!empty($bookingData['equipment'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Perlengkapan
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $bookingData['equipment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($eq['name']); ?></td>
                                    <td class="text-center"><?php echo e($eq['qty']); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($eq['price'], 0, ',', '.')); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($eq['subtotal'], 0, ',', '.')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Card Voucher Diskon -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Kode Voucher Diskon
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="voucher_code">Punya kode voucher? Masukkan di sini:</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="voucher_code" 
                                       placeholder="Masukkan kode voucher"
                                       style="text-transform: uppercase;">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" id="applyVoucherBtn" onclick="applyVoucher()">
                                        <i class="fas fa-check mr-1"></i>Gunakan
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Voucher diskon dari affiliator kami
                            </small>
                        </div>
                        
                        <!-- Voucher Applied Info -->
                        <div id="voucherApplied" style="display: none;">
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <strong>Voucher diterapkan!</strong>
                                        <p class="mb-0 mt-1" id="voucherInfo"></p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVoucher()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Total -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td><h5 class="mb-0">Subtotal:</h5></td>
                                <td class="text-right">
                                    <h5 class="mb-0" id="subtotalAmount">
                                        Rp <?php echo e(number_format($bookingData['total_price'], 0, ',', '.')); ?>

                                    </h5>
                                </td>
                            </tr>
                            <tr id="voucherDiscountRow" style="display: none;">
                                <td><h5 class="mb-0 text-success">Diskon Voucher:</h5></td>
                                <td class="text-right">
                                    <h5 class="mb-0 text-success" id="voucherDiscountAmount">- Rp 0</h5>
                                </td>
                            </tr>
                            <tr>
                                <td><h4 class="mb-0 font-weight-bold">Total Tagihan:</h4></td>
                                <td class="text-right">
                                    <h3 class="mb-0 text-primary font-weight-bold" id="finalAmount">
                                        Rp <?php echo e(number_format($bookingData['total_price'], 0, ',', '.')); ?>

                                    </h3>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Form Konfirmasi -->
                <form action="<?php echo e(route('public.booking.payment.process', ['token' => $token])); ?>" method="POST" enctype="multipart/form-data" id="paymentForm">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Hidden inputs for voucher -->
                    <input type="hidden" name="voucher_code" id="voucher_code_input" value="">
                    <input type="hidden" name="voucher_discount" id="voucher_discount_input" value="0">
                    
                    <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Upload Bukti Pembayaran -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Bukti Pembayaran
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="upload-area" onclick="document.getElementById('bukti_pembayaran').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h5>Klik untuk Upload Bukti Transfer</h5>
                                <p class="text-muted mb-0">Format: JPG, JPEG, PNG (Max 5MB)</p>
                                <p class="text-muted small">Gambar akan otomatis dikompres</p>
                            </div>
                            <input type="file" 
                                   id="bukti_pembayaran" 
                                   name="bukti_pembayaran" 
                                   accept="image/jpeg,image/jpg,image/png" 
                                   required
                                   style="display: none;"
                                   onchange="previewImage(this)">
                            
                            <div id="filePreview" class="file-preview">
                                <img id="previewImg" src="" alt="Preview">
                                <p class="mt-2 mb-0"><strong id="fileName"></strong></p>
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-times mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Dengan menekan tombol "Konfirmasi & Bayar", data booking Anda akan tersimpan dan Anda akan diarahkan ke WhatsApp untuk konfirmasi lebih lanjut.
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                        <i class="fas fa-check-circle mr-2"></i>
                        Konfirmasi & Bayar
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-white">
                        <i class="fas fa-lock mr-1"></i>
                        Data Anda aman & terlindungi
                    </small>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Voucher data
        let voucherData = null;
        const originalAmount = <?php echo e($bookingData['total_price']); ?>;
        
        // Apply voucher
        function applyVoucher() {
            const code = document.getElementById('voucher_code').value.trim().toUpperCase();
            
            if (!code) {
                alert('Silakan masukkan kode voucher!');
                return;
            }
            
            const btn = document.getElementById('applyVoucherBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Validasi...';
            
            // Validate voucher via AJAX
            fetch('<?php echo e(route("voucher.validate")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    code: code,
                    amount: originalAmount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    voucherData = data.data;
                    showVoucherApplied();
                    updateTotalAmount();
                } else {
                    alert(data.message || 'Voucher tidak valid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat validasi voucher');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>Gunakan';
            });
        }
        
        // Show voucher applied
        function showVoucherApplied() {
            if (!voucherData) return;
            
            document.getElementById('voucher_code').value = '';
            document.getElementById('voucherApplied').style.display = 'block';
            document.querySelector('.input-group').style.display = 'none';
            
            let discountText = '';
            if (voucherData.discount_type === 'percentage') {
                discountText = `Diskon ${voucherData.discount_value}%`;
            } else {
                discountText = `Diskon Rp ${formatNumber(voucherData.discount_value)}`;
            }
            
            document.getElementById('voucherInfo').innerHTML = `
                <strong>${voucherData.code}</strong> - ${discountText}<br>
                <small>${voucherData.description || ''}</small>
            `;
            
            // Set hidden inputs
            document.getElementById('voucher_code_input').value = voucherData.code;
            document.getElementById('voucher_discount_input').value = voucherData.discount_amount;
        }
        
        // Remove voucher
        function removeVoucher() {
            voucherData = null;
            document.getElementById('voucherApplied').style.display = 'none';
            document.querySelector('.input-group').style.display = 'flex';
            document.getElementById('voucher_code').value = '';
            document.getElementById('voucher_code_input').value = '';
            document.getElementById('voucher_discount_input').value = '0';
            updateTotalAmount();
        }
        
        // Update total amount
        function updateTotalAmount() {
            const discountAmount = voucherData ? voucherData.discount_amount : 0;
            const finalAmount = originalAmount - discountAmount;
            
            // Update display
            document.getElementById('subtotalAmount').textContent = 'Rp ' + formatNumber(originalAmount);
            document.getElementById('voucherDiscountAmount').textContent = '- Rp ' + formatNumber(discountAmount);
            document.getElementById('finalAmount').textContent = 'Rp ' + formatNumber(finalAmount);
            
            // Show/hide discount row
            if (discountAmount > 0) {
                document.getElementById('voucherDiscountRow').style.display = 'table-row';
            } else {
                document.getElementById('voucherDiscountRow').style.display = 'none';
            }
        }
        
        // Format number
        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Preview image function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                if (!file.type.match('image/jpeg') && !file.type.match('image/jpg') && !file.type.match('image/png')) {
                    alert('Format file tidak didukung! Gunakan JPG, JPEG, atau PNG');
                    input.value = '';
                    return;
                }
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('filePreview').style.display = 'block';
                    document.querySelector('.upload-area').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }
        
        function removeImage() {
            document.getElementById('bukti_pembayaran').value = '';
            document.getElementById('filePreview').style.display = 'none';
            document.querySelector('.upload-area').style.display = 'block';
        }
        
        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            var fileInput = document.getElementById('bukti_pembayaran');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                alert('Silakan upload bukti pembayaran terlebih dahulu!');
                return false;
            }
            
            // Disable submit button to prevent double submission
            var submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        });
    </script>
    
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\public\booking-payment.blade.php ENDPATH**/ ?>