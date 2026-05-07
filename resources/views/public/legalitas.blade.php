<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legalitas Perusahaan - HM Tour & Travel</title>
    <meta name="description" content="Dokumen legalitas resmi HM Tour & Travel yang berizin dari Kementerian Agama RI.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-brand':  '#2E7D32',
                        'green-mid':    '#4CAF50',
                        'green-light':  '#81C784',
                        'green-pale':   '#E8F5E9',
                    }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Nunito', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png') }}"
                     alt="HM Tour" class="h-12 w-auto object-contain"
                     onerror="this.style.display='none'">
            </a>
            <a href="{{ url('/') }}" class="text-green-brand hover:text-green-mid text-sm font-semibold">
                <i class="fas fa-home mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</nav>

<!-- Header -->
<section class="bg-gradient-to-br from-green-50 to-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-playfair text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
            Legalitas <span class="text-green-brand">Perusahaan</span>
        </h1>
        <p class="text-gray-600 text-lg">
            Dokumen resmi yang menjamin kepercayaan dan keamanan perjalanan ibadah Anda
        </p>
    </div>
</section>

<!-- Legalitas Documents -->
<section class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Akta Pendirian -->
            <div class="bg-white rounded-2xl p-8 shadow-md border border-green-100 hover:shadow-xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-alt text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Akta Pendirian</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-blue-100 text-blue-700 text-sm font-semibold px-4 py-2 rounded-full">
                        No. 100 / 25 Oktober 2023
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Akta pendirian perusahaan yang sah dan terdaftar secara resmi sebagai badan hukum PT Hikami Mandiri Indonesia.
                </p>
            </div>

            <!-- NPWP -->
            <div class="bg-white rounded-2xl p-8 shadow-md border border-green-100 hover:shadow-xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-receipt text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">NPWP</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-green-100 text-green-700 text-sm font-semibold px-4 py-2 rounded-full">
                        65.256.531.8-429.000
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Nomor Pokok Wajib Pajak yang menunjukkan perusahaan terdaftar dan taat pajak sesuai peraturan perpajakan Indonesia.
                </p>
            </div>

            <!-- Izin PPIU -->
            <div class="bg-gradient-to-br from-yellow-50 to-white rounded-2xl p-8 shadow-lg border-2 border-yellow-200 hover:shadow-2xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-certificate text-yellow-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Izin PPIU KEMENAG RI</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-yellow-100 text-yellow-700 text-sm font-semibold px-4 py-2 rounded-full">
                        27042200404460002
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Izin resmi sebagai Penyelenggara Perjalanan Ibadah Umroh dari Kementerian Agama Republik Indonesia.
                </p>
            </div>

            <!-- NIB -->
            <div class="bg-white rounded-2xl p-8 shadow-md border border-green-100 hover:shadow-xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-id-card text-purple-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">NIB (Nomor Induk Berusaha)</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-purple-100 text-purple-700 text-sm font-semibold px-4 py-2 rounded-full">
                        Terdaftar Resmi
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Nomor Induk Berusaha yang dikeluarkan oleh OSS (Online Single Submission) sebagai identitas pelaku usaha.
                </p>
            </div>

            <!-- Akreditasi A -->
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-lg border-2 border-green-200 hover:shadow-2xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-award text-green-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Akreditasi A</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-green-100 text-green-700 text-sm font-semibold px-4 py-2 rounded-full">
                        Kemenag RI - 2024
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Akreditasi tertinggi dari Kementerian Agama RI yang menunjukkan standar pelayanan dan manajemen terbaik.
                </p>
            </div>

            <!-- SAPUHI -->
            <div class="bg-white rounded-2xl p-8 shadow-md border border-green-100 hover:shadow-xl transition-shadow">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-handshake text-orange-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Anggota SAPUHI</h3>
                <div class="text-center mb-4">
                    <span class="inline-block bg-orange-100 text-orange-700 text-sm font-semibold px-4 py-2 rounded-full">
                        Terdaftar Aktif
                    </span>
                </div>
                <p class="text-gray-600 text-sm text-center leading-relaxed">
                    Anggota resmi Serikat Penyelenggara Umrah dan Haji Indonesia, organisasi profesi PPIU di Indonesia.
                </p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-12 bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-8 border border-green-200">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-brand rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jaminan Keamanan & Kepercayaan</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Semua dokumen legalitas kami dapat diverifikasi melalui instansi terkait. Kami berkomitmen untuk memberikan pelayanan yang transparan, profesional, dan sesuai dengan peraturan yang berlaku.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Dengan akreditasi A dari Kemenag RI, kami telah memenuhi standar tertinggi dalam hal manajemen, pelayanan, dan keamanan perjalanan ibadah. Kepercayaan Anda adalah prioritas kami.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-green-brand py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-white mb-4">
            Percayakan Perjalanan Ibadah Anda pada Kami
        </h2>
        <p class="text-green-100 mb-6">
            Dengan legalitas lengkap dan akreditasi A, kami siap melayani Anda
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white text-green-brand font-bold px-8 py-3 rounded-full hover:bg-green-50 transition-all">
            <i class="fas fa-kaaba"></i> Lihat Paket Umroh
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} HM Tour & Travel. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
