<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran Lunas - <?php echo e($booking->booking_code); ?> | HM Tour</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>*{font-family:'Nunito',sans-serif}</style>
</head>
<body class="bg-gray-50">
<div class="max-w-2xl mx-auto py-12 px-4">
<div class="bg-white rounded-2xl shadow-lg overflow-hidden text-center">

<!-- Success Animation -->
<div class="bg-gradient-to-r from-green-500 to-green-600 p-10">
    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <h1 class="text-3xl font-black text-white mb-2">Alhamdulillah! 🎉</h1>
    <p class="text-green-100 text-lg">Pembayaran Anda Telah Lunas</p>
</div>

<!-- Body -->
<div class="p-8 space-y-6">
    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
        <div class="text-green-800 font-bold text-lg mb-2">✅ Pembayaran Lengkap</div>
        <p class="text-green-700 text-sm">
            Seluruh pembayaran untuk paket <strong><?php echo e($package->package_name); ?></strong> 
            dengan kode booking <strong><?php echo e($booking->booking_code); ?></strong> telah diterima dengan baik.
        </p>
    </div>

    <div class="bg-gray-50 rounded-xl p-5 space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Paket</span>
            <span class="font-bold text-gray-900"><?php echo e($package->package_name); ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Booking Code</span>
            <span class="font-bold text-gray-900"><?php echo e($booking->booking_code); ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Total Dibayar</span>
            <span class="font-bold text-green-600">Rp <?php echo e(number_format($booking->paid_amount, 0, ',', '.')); ?></span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Status</span>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">LUNAS</span>
        </div>
        <?php if($package->departure_date): ?>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Keberangkatan</span>
            <span class="font-bold text-gray-900"><?php echo e(\Carbon\Carbon::parse($package->departure_date)->format('d M Y')); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h3 class="font-bold text-blue-900 mb-3">📋 Langkah Selanjutnya</h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
            <li>Lengkapi dokumen perjalanan (passport, foto, dll)</li>
            <li>Isi form manifest untuk data keberangkatan</li>
            <li>Tim kami akan menghubungi Anda untuk persiapan keberangkatan</li>
        </ol>
    </div>

    <a href="<?php echo e($manifestUrl); ?>" 
       class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-4 rounded-xl text-center hover:shadow-lg transition">
        📝 Lengkapi Form Manifest
    </a>

    <a href="<?php echo e(url('/')); ?>" 
       class="block w-full border-2 border-gray-200 text-gray-700 font-semibold py-3 rounded-xl text-center hover:bg-gray-50 transition">
        🏠 Kembali ke Homepage
    </a>
</div>

<!-- Footer -->
<div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
    <p class="text-gray-400 text-xs">&copy; <?php echo e(date('Y')); ?> HM Tour & Travel. Berizin Kemenag RI.</p>
</div>

</div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\public\booking-paid.blade.php ENDPATH**/ ?>