<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi | HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>*{font-family:'Nunito',sans-serif}</style>
    
    <!-- Auto-redirect when payment is verified -->
    <script>
        // Check payment status every 5 seconds
        let checkInterval = setInterval(function() {
            fetch('<?php echo e(route("public.payment.check-status", ["paymentId" => $payment->id])); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        clearInterval(checkInterval);
                        // Show success message briefly
                        document.getElementById('statusMessage').innerHTML = `
                            <div class="bg-green-100 border-2 border-green-500 rounded-xl p-4 text-center">
                                <svg class="w-12 h-12 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="font-bold text-green-900 text-lg">Pembayaran Terverifikasi!</p>
                                <p class="text-green-700 text-sm mt-1">Mengalihkan ke invoice...</p>
                            </div>
                        `;
                        // Redirect after 2 seconds
                        setTimeout(function() {
                            window.location.href = '<?php echo e(route("public.paket.invoice", ["packageId" => $package->id, "bookingId" => $booking->id])); ?>';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                });
        }, 5000); // Check every 5 seconds
    </script>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            
            <!-- Icon -->
            <div class="bg-yellow-50 p-8 text-center" id="statusMessage">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h1>
                <p class="text-gray-600">Pembayaran Anda sedang diverifikasi</p>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-6">
                
                <!-- Info Box -->
                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                    <h3 class="font-bold text-blue-900 mb-3">Informasi Pembayaran:</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">Kode Booking:</span>
                            <span class="font-semibold text-blue-900"><?php echo e($booking->booking_code); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Jumlah Dibayar:</span>
                            <span class="font-semibold text-blue-900">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Tanggal Upload:</span>
                            <span class="font-semibold text-blue-900"><?php echo e($payment->created_at->format('d M Y H:i')); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Status:</span>
                            <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">
                                Menunggu Verifikasi
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Timeline -->
                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
                    <h3 class="font-bold text-green-900 mb-3">Langkah Selanjutnya:</h3>
                    <ol class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">1</div>
                            <div class="text-sm text-green-800">
                                <strong>Verifikasi Admin</strong><br>
                                Tim kami akan memverifikasi pembayaran Anda dalam <strong>1x24 jam</strong>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">2</div>
                            <div class="text-sm text-gray-600">
                                <strong>Notifikasi WhatsApp</strong><br>
                                Anda akan menerima notifikasi setelah verifikasi selesai
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-gray-300 text-white rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">3</div>
                            <div class="text-sm text-gray-600">
                                <strong>Invoice & Kwitansi</strong><br>
                                Dokumen akan dikirimkan via WhatsApp
                            </div>
                        </li>
                    </ol>
                </div>
                
                <!-- Contact -->
                <div class="text-center text-sm text-gray-500 border-t pt-4">
                    <p class="mb-1">Jika ada pertanyaan, hubungi kami:</p>
                    <p class="font-semibold text-gray-700">WhatsApp: 0812-3456-7890</p>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3">
                    <a href="<?php echo e(url('/')); ?>" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition-all">
                        Kembali ke Homepage
                    </a>
                    <a href="<?php echo e(route('public.paket.invoice', ['packageId' => $package->id, 'bookingId' => $booking->id])); ?>" 
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition-all">
                        Lihat Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/public/payment-pending-verification.blade.php ENDPATH**/ ?>