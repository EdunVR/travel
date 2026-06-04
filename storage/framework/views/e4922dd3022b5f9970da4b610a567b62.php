<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Manifest Tidak Tersedia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            
            <!-- Icon -->
            <div class="bg-yellow-50 p-8 text-center">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Form Manifest Belum Tersedia</h1>
                <p class="text-gray-600"><?php echo e($message); ?></p>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-blue-900 mb-2">Langkah Selanjutnya:</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                        <li>Lakukan pembayaran pertama untuk booking Anda</li>
                        <li>Setelah pembayaran, Anda akan menerima WhatsApp dengan link form manifest</li>
                        <li>Klik link tersebut untuk mengisi data perjalanan Anda</li>
                    </ol>
                </div>
                
                <div class="text-center">
                    <a href="<?php echo e(url('/')); ?>" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all">
                        Kembali ke Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\public\manifest-not-available.blade.php ENDPATH**/ ?>