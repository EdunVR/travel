<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-yellow-50 to-yellow-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-clock text-yellow-600 text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Menunggu Verifikasi</h1>
            
            <p class="text-gray-600 mb-6">
                Terima kasih telah mendaftar sebagai affiliator HM Tour. 
                Akun Anda sedang dalam proses verifikasi oleh tim kami.
            </p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-semibold text-yellow-900 mb-2">Informasi Akun:</h3>
                <div class="space-y-1 text-sm text-yellow-800">
                    <div><strong>Nama:</strong> {{ $affiliator->full_name }}</div>
                    <div><strong>No. HP:</strong> {{ $affiliator->phone_number }}</div>
                    <div><strong>Email:</strong> {{ $affiliator->email }}</div>
                    <div><strong>Status:</strong> <span class="font-semibold">Pending</span></div>
                </div>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Proses verifikasi biasanya memakan waktu 1x24 jam. 
                Kami akan menghubungi Anda melalui WhatsApp atau email setelah akun diverifikasi.
            </p>

            <div class="flex gap-3">
                <a href="/" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition">
                    <i class="fas fa-home mr-2"></i>Beranda
                </a>
                <a href="{{ route('affiliate.logout') }}" 
                   class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-3 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                </a>
            </div>
        </div>
    </div>
</body>
</html>
