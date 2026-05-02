<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Success Message -->
        <div class="max-w-3xl mx-auto mb-6 no-print">
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg flex items-center">
                <i class="fas fa-check-circle text-3xl mr-4"></i>
                <div>
                    <h3 class="font-bold text-lg">Pembayaran Berhasil!</h3>
                    <p class="text-sm">Terima kasih telah mendaftar. Kami akan mengirimkan konfirmasi via WhatsApp.</p>
                </div>
            </div>
        </div>

        <!-- Receipt Container -->
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-8 text-center">
                @php
                    $logoPath = public_path('images/hm-tour-logo.png');
                    $logoExists = file_exists($logoPath);
                @endphp
                @if($logoExists)
                    <img src="{{ asset('images/hm-tour-logo.png') }}" alt="HM Tour" class="h-20 mx-auto mb-4 brightness-0 invert">
                @else
                    <div class="h-20 w-20 mx-auto mb-4 bg-white rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-3xl font-bold">HM</span>
                    </div>
                @endif
                <h1 class="text-3xl font-bold">KWITANSI PEMBAYARAN</h1>
                <p class="text-green-100 mt-2">Program Kemitraan HM Tour</p>
            </div>

            <!-- Receipt Content -->
            <div class="p-8">
                
                <!-- Receipt Number & Date -->
                <div class="flex justify-between items-start mb-6 pb-6 border-b-2 border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">No. Kwitansi</p>
                        <p class="text-lg font-bold text-gray-900">{{ $receiptNumber }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="text-lg font-bold text-gray-900">{{ now()->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Payer Info -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Telah Terima Dari</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <table class="w-full text-sm">
                            <tr>
                                <td class="py-2 text-gray-600 w-32">Nama</td>
                                <td class="py-2 font-medium text-gray-900">: {{ $affiliator->full_name }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Username</td>
                                <td class="py-2 font-medium text-gray-900">: {{ $affiliator->username }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">No. HP</td>
                                <td class="py-2 font-medium text-gray-900">: {{ $affiliator->phone_number }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Email</td>
                                <td class="py-2 font-medium text-gray-900">: {{ $affiliator->email }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Untuk Pembayaran</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <table class="w-full text-sm">
                            <tr>
                                <td class="py-2 text-gray-600 w-32">Program</td>
                                <td class="py-2 font-medium text-gray-900">: {{ $program->name }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Deskripsi</td>
                                <td class="py-2 text-gray-900">: {{ $program->description }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Target</td>
                                <td class="py-2 text-gray-900">: {{ $program->target_audience }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Amount -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                            <p class="text-4xl font-bold text-green-600">{{ $program->formatted_fee }}</p>
                            <p class="text-xs text-gray-500 mt-2 italic">{{ $amountInWords }}</p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full">
                                <i class="fas fa-check text-white text-2xl"></i>
                            </div>
                            <p class="text-xs text-green-600 font-semibold mt-2">LUNAS</p>
                        </div>
                    </div>
                </div>

                <!-- Commission Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-coins mr-2"></i>
                        Komisi Anda
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-blue-600">Komisi PPC (Per Klik)</p>
                            <p class="text-lg font-bold text-blue-900">Rp {{ number_format($affiliator->ppc_commission, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-blue-600">Komisi Minimal (Per Penjualan)</p>
                            <p class="text-lg font-bold text-blue-900">Rp {{ number_format($affiliator->min_sale_commission, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Referral Link -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-link mr-2 text-green-600"></i>
                        Link Referral Anda
                    </h4>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="{{ $affiliator->referral_link }}" id="referralLink"
                               class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm">
                        <button onclick="copyLink()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center text-sm text-gray-500 pt-6 border-t border-gray-200">
                    <p>Kwitansi ini sah dan diproses secara elektronik</p>
                    <p class="mt-1">Untuk informasi lebih lanjut, hubungi: <strong>{{ env('COMPANY_PHONE', '08976688800') }}</strong></p>
                </div>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="max-w-3xl mx-auto mt-6 flex gap-4 justify-center no-print">
            <button onclick="window.print()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-print mr-2"></i> Cetak Kwitansi
            </button>
            <a href="{{ route('affiliate.login') }}" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-sign-in-alt mr-2"></i> Kembali ke Login
            </a>
        </div>

    </div>

    <script>
        function copyLink() {
            const linkInput = document.getElementById('referralLink');
            linkInput.select();
            document.execCommand('copy');
            
            // Show feedback
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('bg-green-700');
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('bg-green-700');
            }, 2000);
        }
    </script>

</body>
</html>
