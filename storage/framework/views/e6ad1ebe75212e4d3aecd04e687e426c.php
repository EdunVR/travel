<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?php echo e($booking->booking_code); ?> | HM Tour</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{font-family:'Nunito',sans-serif}
.bg-green-gradient{background:linear-gradient(135deg,#2E7D32,#4CAF50)}
.payment-type-btn{transition:all 0.2s}
.payment-type-btn.active{background:#2E7D32;color:white;border-color:#2E7D32}
</style>
</head>
<body class="bg-gray-50">
<div class="max-w-3xl mx-auto py-8 px-4">
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

<!-- Header -->
<div class="bg-green-gradient text-white p-6">
<div class="flex items-center justify-between">
<div>
<img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3-WHITE.png')); ?>" alt="HM Tour" class="h-10 w-auto object-contain mb-2" onerror="this.style.display='none'">
<p class="text-green-100 text-sm">Invoice Pemesanan Paket</p>
</div>
<div class="text-right">
<div class="text-2xl font-black"><?php echo e($booking->booking_code); ?></div>
<div class="text-green-200 text-xs mt-1"><?php echo e(now()->format('d M Y')); ?></div>
</div>
</div>
</div>

<!-- Status -->
<div class="px-6 py-3 bg-yellow-50 border-b border-yellow-100 flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
<span class="text-yellow-700 text-sm font-semibold">Status: PENDING - Menunggu Pembayaran</span>
</div>

<!-- Body -->
<div class="p-6 space-y-5">

<!-- Paket -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Paket Perjalanan</h3>
<div class="bg-gray-50 rounded-xl p-4">
<div class="font-bold text-gray-900 text-lg"><?php echo e($package->package_name); ?></div>
<div class="text-sm text-gray-500 mt-1">
<?php echo e(ucwords(str_replace('_',' ',$package->package_type))); ?>

<?php if($package->duration_days): ?> &bull; <?php echo e($package->duration_days); ?> Hari <?php endif; ?>
<?php if($booking->price_package_name): ?> &bull; <?php echo e($booking->price_package_name); ?> <?php endif; ?>
<?php if($booking->price_variant): ?> (<?php echo e($booking->price_variant); ?>) <?php endif; ?>
</div>
<?php if($package->departure_date): ?>
<div class="text-sm text-green-700 font-semibold mt-2">
✈️ Keberangkatan: <?php echo e(\Carbon\Carbon::parse($package->departure_date)->format('d M Y')); ?>

</div>
<?php endif; ?>
</div>
</div>

<!-- Pemesan -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Data Pemesan</h3>
<div class="grid grid-cols-2 gap-3">
<div class="bg-gray-50 rounded-xl p-3">
<div class="text-xs text-gray-400">Nama</div>
<div class="font-semibold text-gray-900"><?php echo e($booking->jamaah->nama); ?></div>
</div>
<div class="bg-gray-50 rounded-xl p-3">
<div class="text-xs text-gray-400">Telepon</div>
<div class="font-semibold text-gray-900"><?php echo e($booking->jamaah->telepon); ?></div>
</div>
</div>
</div>

<!-- Rincian Harga -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rincian Harga</h3>
<div class="border border-gray-100 rounded-xl overflow-hidden">
<?php $__currentLoopData = $priceBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 last:border-0">
<span class="text-sm text-gray-700"><?php echo e($item['label']); ?></span>
<span class="font-semibold text-gray-900 text-sm">Rp <?php echo e(number_format($item['amount'],0,',','.')); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($booking->discount_amount > 0): ?>
<div class="flex items-center justify-between px-4 py-3 bg-red-50 border-b border-red-100">
<span class="text-sm text-red-700 font-semibold">
<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
</svg>
Diskon
</span>
<span class="font-bold text-red-600 text-sm">- Rp <?php echo e(number_format($booking->discount_amount,0,',','.')); ?></span>
</div>
<?php endif; ?>

<?php if(isset($adminDiscount) && $adminDiscount > 0): ?>
<div class="flex items-center justify-between px-4 py-3 bg-blue-50 border-b border-blue-100">
<span class="text-sm text-blue-700 font-semibold">
<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
Diskon Admin
</span>
<span class="font-bold text-blue-600 text-sm">- Rp <?php echo e(number_format($adminDiscount,0,',','.')); ?></span>
</div>
<?php endif; ?>

<div class="flex items-center justify-between px-4 py-4 bg-green-50">
<span class="font-bold text-gray-900">Total</span>
<span class="font-black text-green-700 text-xl">Rp <?php echo e(number_format($grandTotal,0,',','.')); ?></span>
</div>
</div>

<?php if($booking->discount_amount > 0): ?>
<div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-2">
<svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<div class="text-xs text-yellow-700">
<span class="font-semibold">Selamat!</span> Anda mendapatkan diskon sebesar Rp <?php echo e(number_format($booking->discount_amount,0,',','.')); ?> untuk pemesanan ini.
</div>
</div>
<?php endif; ?>

<?php if(isset($adminDiscount) && $adminDiscount > 0): ?>
<div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
<svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<div class="text-xs text-blue-700">
<span class="font-semibold">Diskon Khusus!</span> Admin telah memberikan diskon sebesar Rp <?php echo e(number_format($adminDiscount,0,',','.')); ?> untuk pemesanan ini.
</div>
</div>
<?php endif; ?>
</div>

<!-- Anggota Keluarga -->
<?php if(!empty($familyMembers)): ?>
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Anggota Keluarga</h3>
<div class="space-y-2">
<?php $__currentLoopData = $familyMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between">
<div>
<span class="font-semibold text-gray-900 text-sm"><?php echo e($fm['nama']); ?></span>
<?php if(!empty($fm['hubungan'])): ?> <span class="text-gray-400 text-xs ml-1">(<?php echo e($fm['hubungan']); ?>)</span> <?php endif; ?>
</div>
<?php if(!empty($fm['tanggal_lahir'])): ?>
<span class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($fm['tanggal_lahir'])->age); ?> tahun</span>
<?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<?php endif; ?>

<!-- Voucher Diskon -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kode Voucher Diskon</h3>
<div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-xl p-4">
<div id="voucherInputSection">
<label for="voucher_code" class="text-sm font-semibold text-gray-700 mb-2 block">Punya kode voucher? Masukkan di sini:</label>
<div class="flex gap-2">
<input type="text" 
       id="voucher_code" 
       placeholder="Masukkan kode voucher"
       class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-transparent uppercase"
       style="text-transform: uppercase;">
<button type="button" 
        id="applyVoucherBtn" 
        onclick="applyVoucher()"
        class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition-all flex items-center gap-2">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
</svg>
Gunakan
</button>
</div>
<p class="text-xs text-gray-500 mt-2">
<svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
Voucher diskon dari affiliator kami
</p>
</div>

<!-- Voucher Applied Info -->
<div id="voucherApplied" style="display: none;">
<div class="bg-green-50 border-2 border-green-500 rounded-lg p-4">
<div class="flex justify-between items-start">
<div class="flex-1">
<div class="flex items-center gap-2 mb-2">
<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<span class="font-bold text-green-700">Voucher diterapkan!</span>
</div>
<div id="voucherInfo" class="text-sm text-green-700"></div>
</div>
<button type="button" 
        onclick="removeVoucher()"
        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
Hapus
</button>
</div>
</div>
</div>
</div>
</div>

<!-- Status Pembayaran -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status Pembayaran</h3>
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4 space-y-3" id="paymentStatusCard">
<div class="flex items-center justify-between pb-2 border-b border-blue-200">
<span class="text-sm text-gray-600">Total Tagihan</span>
<span class="font-bold text-gray-900" id="displayGrandTotal">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></span>
</div>
<div id="voucherDiscountRow" class="flex items-center justify-between pb-2 border-b border-blue-200" style="display: none;">
<span class="text-sm text-green-600 font-semibold">Diskon Voucher</span>
<span class="font-bold text-green-600" id="displayVoucherDiscount">- Rp 0</span>
</div>
<div id="finalTotalRow" class="flex items-center justify-between pb-2 border-b border-blue-200" style="display: none;">
<span class="text-sm text-gray-700 font-semibold">Total Setelah Diskon</span>
<span class="font-bold text-blue-600" id="displayFinalTotal">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></span>
</div>
<div class="flex items-center justify-between pb-2 border-b border-blue-200">
<span class="text-sm text-gray-600">Sudah Dibayar</span>
<span class="font-semibold text-green-600">Rp <?php echo e(number_format($booking->paid_amount ?? 0, 0, ',', '.')); ?></span>
</div>
<div class="flex items-center justify-between">
<span class="text-sm font-semibold text-gray-700">Sisa Tagihan</span>
<span class="font-bold text-red-600 text-lg" id="displayRemainingBalance">Rp <?php echo e(number_format($grandTotal - ($booking->paid_amount ?? 0), 0, ',', '.')); ?></span>
</div>
</div>

<?php if($booking->id_invoice): ?>
<?php
    // Generate secure token for public invoice access
    $invoiceToken = hash('sha256', $booking->id . $booking->id_invoice . config('app.key'));
    $publicInvoiceUrl = route('public.invoice', ['bookingId' => $booking->id, 'token' => $invoiceToken]);
?>
<div class="mt-3 bg-white border-2 border-green-200 rounded-xl p-4">
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-2">
<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
</svg>
<span class="font-bold text-gray-900 text-sm">Invoice Tersedia</span>
</div>
<span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
<?php echo e($booking->invoice->no_invoice ?? 'INV-' . $booking->booking_code); ?>

</span>
</div>
<div class="flex gap-2">
<a href="<?php echo e($publicInvoiceUrl); ?>" 
   target="_blank"
   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg text-sm text-center transition-all flex items-center justify-center gap-2">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
</svg>
Lihat Invoice
</a>
<a href="<?php echo e($publicInvoiceUrl); ?>" 
   download
   class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-4 rounded-lg text-sm text-center transition-all flex items-center justify-center gap-2">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
</svg>
Download Invoice
</a>
</div>
<p class="text-xs text-gray-500 mt-2 text-center">
Invoice resmi untuk pembayaran Anda
</p>
</div>
<?php endif; ?>
</div>

<!-- FORM PEMBAYARAN -->
<form action="<?php echo e(route('public.paket.pay', ['packageId' => $package->id, 'bookingId' => $booking->id])); ?>" method="POST" enctype="multipart/form-data" id="paymentForm">
<?php echo csrf_field(); ?>

<!-- Hidden inputs for voucher -->
<input type="hidden" name="voucher_code" id="voucher_code_input" value="">
<input type="hidden" name="voucher_discount" id="voucher_discount_input" value="0">

<!-- Info Pembayaran yang Dipilih -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jumlah yang Harus Dibayar Sekarang</h3>
<div class="bg-green-50 border-2 border-green-500 rounded-xl p-4">
<div class="flex items-center justify-between">
<div>
<div class="font-bold text-gray-900">
<?php
// Prioritas: custom_payment_amount > payment_type calculation
$paymentAmount = $grandTotal - ($booking->paid_amount ?? 0); // Default = sisa tagihan

if (!empty($booking->custom_payment_amount)) {
    // Jika admin set custom amount
    $paymentAmount = min($booking->custom_payment_amount, $paymentAmount);
    $paymentLabel = 'Jumlah yang Ditentukan Admin';
} elseif ($booking->payment_type === 'full') {
    $paymentLabel = 'Bayar Lunas';
} else {
    // DP
    if ($booking->dp_option === '25_percent') {
        $paymentAmount = min(round($grandTotal * 0.25), $paymentAmount);
        $paymentLabel = 'Bayar DP (25%)';
    } else {
        $paymentAmount = min(10000000, $paymentAmount);
        $paymentLabel = 'Bayar DP (Rp 10 Juta)';
    }
}
?>
<?php echo e($paymentLabel); ?>

</div>
<div class="text-xs text-gray-500 mt-1">
<?php if(!empty($booking->custom_payment_amount)): ?>
Jumlah pembayaran telah diatur oleh admin
<?php else: ?>
Pilihan pembayaran Anda
<?php endif; ?>
</div>
</div>
<div class="text-green-700 font-black text-2xl">
Rp <?php echo e(number_format($paymentAmount, 0, ',', '.')); ?>

</div>
</div>
<?php if(!empty($booking->custom_payment_amount)): ?>
<div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
<svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<div class="text-xs text-blue-700">
<span class="font-semibold">Catatan:</span> Jumlah pembayaran ini telah disesuaikan oleh admin. Silakan transfer sesuai nominal yang tertera di atas.
</div>
</div>
<?php endif; ?>
</div>
<input type="hidden" name="payment_type" value="<?php echo e($booking->payment_type); ?>">
<input type="hidden" name="dp_option" value="<?php echo e($booking->dp_option); ?>">
</div>

<!-- Rekening Bank -->
<?php if($bankAccounts->isNotEmpty()): ?>
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer ke Rekening</h3>
<div class="space-y-2">
<?php $__currentLoopData = $bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
<div class="flex items-center justify-between">
<div class="flex-1">
<div class="font-bold text-blue-900"><?php echo e($bank->bank_name); ?></div>
<div class="text-blue-700 font-mono text-lg font-bold mt-1" id="acc_<?php echo e($bank->id); ?>"><?php echo e($bank->account_number); ?></div>
<div class="text-blue-600 text-sm">a/n <?php echo e($bank->account_holder_name); ?></div>
</div>
<button type="button" onclick="copyToClipboard('<?php echo e($bank->account_number); ?>', <?php echo e($bank->id); ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
<span id="copyBtn_<?php echo e($bank->id); ?>">📋 Copy</span>
</button>
</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<?php endif; ?>

<!-- Upload Bukti Transfer -->
<div>
<h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></h3>
<div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 transition-all">
<input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*,application/pdf" required class="hidden" onchange="previewFile(this)">
<label for="bukti_transfer" class="cursor-pointer">
<div id="filePreview" class="text-gray-500">
<svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
</svg>
<div class="font-semibold">Klik untuk upload</div>
<div class="text-xs mt-1">JPG, PNG, atau PDF (Max 5MB)</div>
</div>
</label>
</div>
</div>

<!-- Catatan -->
<div>
<label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Catatan (Opsional)</label>
<textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
</div>

<!-- Tombol Bayar -->
<button type="submit" class="w-full bg-green-gradient text-white font-black py-4 rounded-xl text-lg hover:shadow-lg transition-all flex items-center justify-center gap-2">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
BAYAR SEKARANG
</button>

</form>

<!-- Info -->
<div class="bg-blue-50 rounded-xl p-4 text-sm text-blue-800">
<div class="font-bold mb-1">Setelah Pembayaran:</div>
<ol class="list-decimal list-inside space-y-1 text-blue-700">
<li>Anda akan diarahkan ke WhatsApp dengan link invoice & kwitansi</li>
<li>Tim kami akan verifikasi pembayaran dalam 1x24 jam</li>
<li>Dokumen perjalanan akan diproses setelah verifikasi</li>
</ol>
</div>

</div>

<!-- Footer -->
<div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
<p class="text-gray-400 text-xs">&copy; <?php echo e(date('Y')); ?> HM Tour & Travel. Berizin Kemenag RI.</p>
</div>

</div>
</div>

<script>
// Voucher data
let voucherData = null;
const originalGrandTotal = <?php echo e($grandTotal); ?>;

function copyToClipboard(text, id) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn_' + id);
        btn.textContent = '✅ Tersalin!';
        setTimeout(() => { btn.textContent = '📋 Copy'; }, 2000);
    });
}

function previewFile(input) {
    const preview = document.getElementById('filePreview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        preview.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="font-semibold text-green-700">${fileName}</div>
            <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
        `;
    }
}

// Apply voucher
function applyVoucher() {
    const code = document.getElementById('voucher_code').value.trim().toUpperCase();
    
    if (!code) {
        alert('Silakan masukkan kode voucher!');
        return;
    }
    
    const btn = document.getElementById('applyVoucherBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Validasi...';
    
    // Validate voucher via AJAX
    fetch('<?php echo e(route("voucher.validate")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            code: code,
            amount: originalGrandTotal
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
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Gunakan';
    });
}

// Show voucher applied
function showVoucherApplied() {
    if (!voucherData) return;
    
    document.getElementById('voucher_code').value = '';
    document.getElementById('voucherInputSection').style.display = 'none';
    document.getElementById('voucherApplied').style.display = 'block';
    
    let discountText = '';
    if (voucherData.discount_type === 'percentage') {
        discountText = `Diskon ${voucherData.discount_value}%`;
    } else {
        discountText = `Diskon Rp ${formatNumber(voucherData.discount_value)}`;
    }
    
    document.getElementById('voucherInfo').innerHTML = `
        <p class="font-semibold">${voucherData.code}</p>
        <p class="text-xs mt-1">${discountText} - Hemat Rp ${formatNumber(voucherData.discount_amount)}</p>
        ${voucherData.description ? `<p class="text-xs mt-1">${voucherData.description}</p>` : ''}
    `;
    
    // Set hidden inputs
    document.getElementById('voucher_code_input').value = voucherData.code;
    document.getElementById('voucher_discount_input').value = voucherData.discount_amount;
}

// Remove voucher
function removeVoucher() {
    voucherData = null;
    document.getElementById('voucherInputSection').style.display = 'block';
    document.getElementById('voucherApplied').style.display = 'none';
    document.getElementById('voucher_code').value = '';
    document.getElementById('voucher_code_input').value = '';
    document.getElementById('voucher_discount_input').value = '0';
    updateTotalAmount();
}

// Update total amount
function updateTotalAmount() {
    const discountAmount = voucherData ? voucherData.discount_amount : 0;
    const finalAmount = originalGrandTotal - discountAmount;
    const paidAmount = <?php echo e($booking->paid_amount ?? 0); ?>;
    const remainingBalance = finalAmount - paidAmount;
    
    // Update display
    document.getElementById('displayGrandTotal').textContent = 'Rp ' + formatNumber(originalGrandTotal);
    document.getElementById('displayVoucherDiscount').textContent = '- Rp ' + formatNumber(discountAmount);
    document.getElementById('displayFinalTotal').textContent = 'Rp ' + formatNumber(finalAmount);
    document.getElementById('displayRemainingBalance').textContent = 'Rp ' + formatNumber(remainingBalance);
    
    // Show/hide discount rows
    if (discountAmount > 0) {
        document.getElementById('voucherDiscountRow').style.display = 'flex';
        document.getElementById('finalTotalRow').style.display = 'flex';
    } else {
        document.getElementById('voucherDiscountRow').style.display = 'none';
        document.getElementById('finalTotalRow').style.display = 'none';
    }
}

// Format number
function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Allow Enter key to apply voucher
document.addEventListener('DOMContentLoaded', function() {
    const voucherInput = document.getElementById('voucher_code');
    if (voucherInput) {
        voucherInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyVoucher();
            }
        });
    }
});
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/public/invoice-booking.blade.php ENDPATH**/ ?>