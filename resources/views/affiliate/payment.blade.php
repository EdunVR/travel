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
    @php
        try {
            $settings = \App\Models\CompanySetting::first();
            $faviconUrl = $settings && $settings->logo ? asset('storage/' . $settings->logo) : url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        } catch (\Exception $e) {
            $faviconUrl = url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        }
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            @php
                $logoPath = public_path('images/hm-tour-logo.png');
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <img src="{{ asset('images/hm-tour-logo.png') }}" alt="HM Tour" class="h-16 mx-auto mb-4">
            @else
                <div class="h-16 w-16 mx-auto mb-4 bg-green-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">HM</span>
                </div>
            @endif
            <h1 class="text-3xl font-bold text-gray-800">Pembayaran Pendaftaran</h1>
            <p class="text-gray-600 mt-2">{{ $program->name }}</p>
        </div>

        @if($errors->any())
        <div class="max-w-2xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Payment Container -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-8">
            
            <!-- Program Info -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">{{ $program->name }}</h2>
                        <p class="text-green-100 text-sm">{{ $program->description }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $program->formatted_fee }}</div>
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
                        <span class="font-medium text-gray-900 ml-2">{{ $registrationData['full_name'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Username:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $registrationData['username'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">HP:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $registrationData['phone_number'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $registrationData['email'] }}</span>
                    </div>
                </div>
            </div>

            @if($program->registration_fee > 0)
            <!-- Payment Instructions -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-university text-green-600 mr-2"></i>
                    Informasi Pembayaran
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Silakan transfer ke rekening berikut:</p>
                            <div class="mt-2 space-y-1">
                                <p><strong>Bank:</strong> BCA</p>
                                <p><strong>No. Rekening:</strong> 1234567890</p>
                                <p><strong>Atas Nama:</strong> HM Tour</p>
                                <p><strong>Jumlah:</strong> <span class="text-lg font-bold">{{ $program->formatted_fee }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Payment Proof -->
                <form action="{{ route('affiliate.payment.process', $token) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                    @csrf
                    
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
                        <i class="fas fa-check-circle mr-2"></i> Konfirmasi Pembayaran
                    </button>
                </form>
            </div>
            @else
            <!-- Free Program -->
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-gift text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Program Gratis!</h3>
                <p class="text-gray-600 mb-6">Tidak ada biaya pendaftaran untuk program ini</p>
                
                <form action="{{ route('affiliate.payment.process', $token) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition text-lg">
                        <i class="fas fa-check-circle mr-2"></i> Selesaikan Pendaftaran
                    </button>
                </form>
            </div>
            @endif

        </div>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="{{ route('affiliate.register') }}" class="text-gray-600 hover:text-gray-800">
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
    </script>

</body>
</html>
