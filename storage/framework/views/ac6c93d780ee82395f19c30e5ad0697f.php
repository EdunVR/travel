<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sejarah HM Tour - HM Tour & Travel</title>
    <meta name="description" content="Perjalanan HM Tour & Travel dari tahun 2012 hingga menjadi penyelenggara umroh dan haji berakreditasi A.">

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
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
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
                <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>"
                     alt="HM Tour" class="h-12 w-auto object-contain"
                     onerror="this.style.display='none'">
            </a>
            <a href="<?php echo e(url('/')); ?>" class="text-green-brand hover:text-green-mid text-sm font-semibold">
                <i class="fas fa-home mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</nav>

<!-- Header -->
<section class="bg-gradient-to-br from-green-50 to-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-playfair text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
            Sejarah <span class="text-green-brand">HM Tour</span>
        </h1>
        <p class="text-gray-600 text-lg">
            Perjalanan kami melayani jemaah selama lebih dari 13 tahun
        </p>
    </div>
</section>

<!-- Timeline -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <!-- Vertical Line -->
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-green-200"></div>

            <!-- Timeline Items -->
            <div class="space-y-12">
                <!-- 2012 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-brand rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2012
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-green-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Berdirinya HM Tour & Travel</h3>
                        <p class="text-gray-600 leading-relaxed">
                            HM Tour & Travel didirikan sebagai biro perjalanan wisata lokal, melayani perjalanan domestik di seluruh Indonesia. Dimulai dengan visi untuk memberikan pelayanan terbaik bagi para pelancong.
                        </p>
                    </div>
                </div>

                <!-- 2015 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-brand rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2015
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-green-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Ekspansi Layanan</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Memperluas layanan dengan membuka cabang di berbagai kota di Indonesia. Fokus pada peningkatan kualitas pelayanan dan kepuasan pelanggan.
                        </p>
                    </div>
                </div>

                <!-- 2021 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-brand rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2021
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-green-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Transformasi Menjadi PT</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Meningkat menjadi PT Hikami Mandiri Indonesia. Mulai fokus pada layanan ibadah umroh dan haji, dengan komitmen untuk memberikan pengalaman ibadah yang bermakna sesuai tuntunan sunnah.
                        </p>
                    </div>
                </div>

                <!-- 2022 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-brand rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2022
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-green-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Izin Resmi Penyelenggara Umroh</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Mendapatkan izin resmi sebagai Penyelenggara Perjalanan Ibadah Umroh (PPIU) dari Kementerian Agama RI. Bergabung dengan SAPUHI (Serikat Penyelenggara Umrah dan Haji Indonesia).
                        </p>
                    </div>
                </div>

                <!-- 2023 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-brand rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2023
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-green-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Pertumbuhan Signifikan</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Melayani ribuan jemaah dari berbagai kota di Indonesia. Memperluas jaringan mitra hotel dan maskapai untuk memberikan pilihan terbaik bagi jemaah.
                        </p>
                    </div>
                </div>

                <!-- 2024 -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                        2024
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-white rounded-2xl p-6 shadow-lg border-2 border-yellow-200">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-award text-yellow-500 text-2xl"></i>
                            <h3 class="text-xl font-bold text-gray-900">Akreditasi A dari Kemenag RI</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Meraih pencapaian tertinggi dengan mendapatkan Akreditasi A dari Kementerian Agama RI. Ini adalah pengakuan atas komitmen kami dalam memberikan pelayanan terbaik dan profesional kepada jemaah.
                        </p>
                        <div class="bg-white rounded-lg p-4 border border-yellow-200">
                            <p class="text-sm text-gray-700 italic">
                                "Akreditasi A adalah bukti dedikasi kami untuk terus meningkatkan kualitas layanan dan memastikan setiap perjalanan ibadah jemaah berjalan dengan lancar dan bermakna."
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Present -->
                <div class="relative pl-20">
                    <div class="absolute left-0 w-16 h-16 bg-green-accent rounded-full flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-6 shadow-lg border-2 border-green-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Hari Ini & Masa Depan</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Terus berinovasi dan berkembang untuk memberikan pengalaman ibadah terbaik. Dengan teknologi digital dan tim profesional, kami siap melayani lebih banyak jemaah dengan standar pelayanan tertinggi.
                        </p>
                        <div class="grid grid-cols-3 gap-4 mt-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-brand">10K+</div>
                                <div class="text-xs text-gray-500">Jamaah</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-brand">50+</div>
                                <div class="text-xs text-gray-500">Kota</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-brand">100%</div>
                                <div class="text-xs text-gray-500">Visa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-green-brand py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-white mb-4">
            Bergabunglah dengan Ribuan Jemaah Kami
        </h2>
        <p class="text-green-100 mb-6">
            Wujudkan impian ibadah Anda bersama HM Tour & Travel
        </p>
        <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 bg-white text-green-brand font-bold px-8 py-3 rounded-full hover:bg-green-50 transition-all">
            <i class="fas fa-kaaba"></i> Lihat Paket Umroh
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400 text-sm">&copy; <?php echo e(date('Y')); ?> HM Tour & Travel. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\public\sejarah.blade.php ENDPATH**/ ?>