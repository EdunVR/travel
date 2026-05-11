<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Affiliator - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-brand': '#2E7D32',
                        'green-mid': '#4CAF50',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>" 
                         alt="HM Tour" class="h-10">
                    <div class="border-l pl-3">
                        <div class="text-sm font-semibold text-gray-900">Dashboard Affiliator</div>
                        <div class="text-xs text-gray-500"><?php echo e($affiliator->full_name); ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm text-gray-600 hover:text-green-brand">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                    <a href="<?php echo e(route('affiliate.logout')); ?>" class="text-sm text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex gap-6 py-8">
            <!-- Sidebar -->
            <div class="w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                    <!-- Profile Section -->
                    <div class="p-4 bg-gradient-to-r from-green-600 to-green-500 text-white text-center">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xl mx-auto mb-2 overflow-hidden border-2 border-white/30">
                            <?php if($affiliator->photo): ?>
                                <img src="<?php echo e($affiliator->photo_url); ?>" alt="<?php echo e($affiliator->full_name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php echo e(strtoupper(substr($affiliator->full_name, 0, 2))); ?>

                            <?php endif; ?>
                        </div>
                        <div class="font-semibold text-sm"><?php echo e($affiliator->full_name); ?></div>
                        <div class="text-xs opacity-90"><?php echo e($affiliator->partnershipProgram->name ?? 'HM Seller'); ?></div>
                        <div class="text-xs opacity-75 mt-1">Sejak <?php echo e($affiliator->created_at->format('M Y')); ?></div>
                    </div>

                    <!-- Menu Items -->
                    <nav class="p-2">
                        <a href="<?php echo e(route('affiliate.dashboard')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white bg-green-600 rounded-lg mb-1">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.referrals')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-users w-5"></i>
                            <span>Referrals</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.payments')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-money-bill-wave w-5"></i>
                            <span>Payments</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.wallet')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-wallet w-5"></i>
                            <span>Wallet</span>
                        </a>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <a href="<?php echo e(route('affiliate.profile')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-user w-5"></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.marketing')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-bullhorn w-5"></i>
                            <span>Marketing</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.reports')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Reports</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.leaderboard')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-trophy w-5"></i>
                            <span>Leaderboard</span>
                        </a>
                        <a href="<?php echo e(route('affiliate.hierarchy')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg mb-1 transition">
                            <i class="fas fa-sitemap w-5"></i>
                            <span>Jenjang Saya</span>
                        </a>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <a href="<?php echo e(route('affiliate.logout')); ?>" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>LogOut</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 min-w-0">

        <!-- Alert Success -->
        <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Klik -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mouse-pointer text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Total Klik</span>
                </div>
                <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_clicks'])); ?></div>
                <div class="text-xs text-gray-500 mt-1">Klik referral</div>
            </div>

            <!-- Total Penjualan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Total Penjualan</span>
                </div>
                <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_sales'])); ?></div>
                <div class="text-xs text-gray-500 mt-1">Konversi <?php echo e($stats['conversion_rate']); ?>%</div>
            </div>

            <!-- Saldo Tersedia -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Saldo Tersedia</span>
                </div>
                <div class="text-2xl font-bold text-green-600">Rp <?php echo e(number_format($stats['available_balance'], 0, ',', '.')); ?></div>
                <div class="text-xs text-gray-500 mt-1">Bisa ditarik</div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Total Pendapatan</span>
                </div>
                <div class="text-2xl font-bold text-gray-900">Rp <?php echo e(number_format($stats['total_earnings'], 0, ',', '.')); ?></div>
                <div class="text-xs text-gray-500 mt-1">Pending: Rp <?php echo e(number_format($stats['pending_balance'], 0, ',', '.')); ?></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Link Referral -->
                <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fas fa-link mr-2"></i>Link Referral Anda
                    </h3>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <input type="text" 
                                   id="referralLink" 
                                   value="<?php echo e($affiliator->referral_link); ?>" 
                                   readonly
                                   class="flex-1 bg-transparent border-none text-white text-sm focus:outline-none">
                            <button onclick="copyReferralLink()" 
                                    class="bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                                <i class="fas fa-copy mr-1"></i> Salin
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="https://wa.me/?text=<?php echo e(urlencode('Yuk umroh bareng! Daftar di: ' . $affiliator->referral_link)); ?>" 
                           target="_blank"
                           class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold text-center transition">
                            <i class="fab fa-whatsapp mr-1"></i> Share WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode($affiliator->referral_link)); ?>" 
                           target="_blank"
                           class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold text-center transition">
                            <i class="fab fa-facebook mr-1"></i> Share Facebook
                        </a>
                    </div>
                </div>

                <!-- Referral Berhasil -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>Referral Berhasil
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if($recentReferrals->where('status', '!=', 'rejected')->count() > 0): ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $recentReferrals->where('status', '!=', 'rejected'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 text-sm">
                                        <?php echo e($referral->package->package_name ?? 'Paket Tidak Tersedia'); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php echo e($referral->order_date->format('d M Y H:i')); ?>

                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600 text-sm">
                                        Rp <?php echo e(number_format($referral->commission_amount, 0, ',', '.')); ?>

                                    </div>
                                    <span class="inline-block px-2 py-1 text-xs rounded-full mt-1
                                        <?php echo e($referral->status === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                                        <?php echo e($referral->status === 'verified' ? 'Verified' : 'Pending'); ?>

                                    </span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-sm">Belum ada referral berhasil</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Referral Gagal -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>Referral Gagal
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if($recentReferrals->where('status', 'rejected')->count() > 0): ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $recentReferrals->where('status', 'rejected'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 text-sm">
                                        <?php echo e($referral->package->package_name ?? 'Paket Tidak Tersedia'); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php echo e($referral->order_date->format('d M Y H:i')); ?>

                                    </div>
                                    <?php if($referral->notes): ?>
                                    <div class="text-xs text-red-600 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i><?php echo e($referral->notes); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-red-600 text-sm line-through">
                                        Rp <?php echo e(number_format($referral->commission_amount, 0, ',', '.')); ?>

                                    </div>
                                    <span class="inline-block px-2 py-1 text-xs rounded-full mt-1 bg-red-100 text-red-700">
                                        Rejected
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-smile text-4xl mb-3"></i>
                            <p class="text-sm">Tidak ada referral yang gagal</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Voucher Diskon Saya -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" x-data="affiliateVoucherManager(<?php echo e($affiliator->id); ?>)">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-ticket-alt text-amber-600 mr-2"></i>Voucher Diskon Saya
                        </h3>
                        <button @click="openCreateModal()" 
                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            <i class="fas fa-plus mr-1"></i> Buat Voucher
                        </button>
                    </div>
                    <div class="p-6">
                        <template x-if="loading">
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p class="text-sm">Memuat voucher...</p>
                            </div>
                        </template>
                        
                        <template x-if="!loading && vouchers.length === 0">
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                                <p class="text-sm">Belum ada voucher</p>
                                <p class="text-xs mt-1">Klik "Buat Voucher" untuk membuat voucher pertama</p>
                            </div>
                        </template>
                        
                        <template x-if="!loading && vouchers.length > 0">
                            <div class="space-y-3">
                                <template x-for="voucher in vouchers" :key="voucher.id">
                                    <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg border border-amber-200">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="font-bold text-amber-700 text-lg" x-text="voucher.code"></div>
                                                <div class="text-xs text-gray-600 mt-1" x-text="voucher.description || '-'"></div>
                                            </div>
                                            <div class="flex gap-1">
                                                <button @click="openEditModal(voucher)" 
                                                        class="p-2 bg-white hover:bg-gray-50 text-blue-600 rounded-lg transition">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <button @click="deleteVoucher(voucher.id)" 
                                                        :disabled="voucher.usage_count > 0"
                                                        class="p-2 bg-white hover:bg-gray-50 text-red-600 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3 mt-3 text-xs">
                                            <div>
                                                <span class="text-gray-500">Diskon:</span>
                                                <span class="font-semibold text-gray-900 ml-1">
                                                    <template x-if="voucher.discount_type === 'percentage'">
                                                        <span x-text="voucher.discount_value + '%'"></span>
                                                    </template>
                                                    <template x-if="voucher.discount_type === 'fixed'">
                                                        <span x-text="'Rp ' + formatNumber(voucher.discount_value)"></span>
                                                    </template>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Penggunaan:</span>
                                                <span class="font-semibold text-gray-900 ml-1">
                                                    <span x-text="voucher.usage_count"></span> / 
                                                    <span x-text="voucher.usage_limit || '∞'"></span>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Berlaku:</span>
                                                <span class="font-semibold text-gray-900 ml-1" x-text="formatDate(voucher.valid_from) + ' - ' + formatDate(voucher.valid_until)"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Status:</span>
                                                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                                      :class="{
                                                          'bg-green-100 text-green-700': voucher.is_active && isValidDate(voucher),
                                                          'bg-red-100 text-red-700': !voucher.is_active,
                                                          'bg-amber-100 text-amber-700': voucher.is_active && !isValidDate(voucher)
                                                      }"
                                                      x-text="getStatusText(voucher)">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Request Payout -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Tarik Saldo
                    </h3>
                    <form action="<?php echo e(route('affiliate.payout.request')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penarikan</label>
                            <input type="number" 
                                   name="amount" 
                                   min="100000" 
                                   max="<?php echo e($stats['available_balance']); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Minimal Rp 100.000">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="paypal">PayPal</option>
                                <option value="stripe">Stripe</option>
                            </select>
                        </div>
                        <?php if($affiliator->bank_name): ?>
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs">
                            <div class="font-semibold text-gray-700 mb-1">Rekening Terdaftar:</div>
                            <div class="text-gray-600"><?php echo e($affiliator->bank_name); ?></div>
                            <div class="text-gray-600"><?php echo e($affiliator->bank_account_number); ?></div>
                            <div class="text-gray-600"><?php echo e($affiliator->bank_account_name); ?></div>
                        </div>
                        <?php endif; ?>
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                            <i class="fas fa-paper-plane mr-2"></i>Ajukan Penarikan
                        </button>
                    </form>
                </div>

                <!-- Riwayat Penarikan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-history text-gray-600 mr-2"></i>Riwayat Penarikan
                    </h3>
                    <?php if($payouts->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-semibold text-gray-900">
                                    Rp <?php echo e(number_format($payout->amount, 0, ',', '.')); ?>

                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    <?php echo e($payout->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                       ($payout->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')); ?>">
                                    <?php echo e(ucfirst($payout->status)); ?>

                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($payout->requested_at->format('d M Y')); ?>

                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-6 text-gray-400 text-sm">
                        Belum ada riwayat penarikan
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Klik Terbaru -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-mouse-pointer text-blue-600 mr-2"></i>Klik Terbaru
                    </h3>
                    <?php if($recentClicks->count() > 0): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $recentClicks->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $click): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between text-xs">
                            <div class="text-gray-600">
                                <?php echo e($click->clicked_at->diffForHumans()); ?>

                            </div>
                            <div class="text-green-600 font-semibold">
                                +Rp <?php echo e(number_format($click->commission_amount, 0, ',', '.')); ?>

                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-6 text-gray-400 text-sm">
                        Belum ada klik
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </div>

    
    <div id="voucherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" x-data="voucherModalData()">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold text-gray-900" x-text="editMode ? 'Edit Voucher' : 'Buat Voucher Baru'"></h3>
                <button onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form @submit.prevent="saveVoucher()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Voucher <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" x-model="form.code" required maxlength="50" 
                                   :readonly="editMode"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 uppercase"
                                   placeholder="DISKON10">
                            <button type="button" @click="generateCode()" x-show="!editMode"
                                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition">
                                <i class="fas fa-random"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe Diskon <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.discount_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nilai Diskon <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="form.discount_value" required min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                               placeholder="10">
                        <p class="text-xs text-gray-500 mt-1" x-show="form.discount_type === 'percentage'">
                            Contoh: 10 untuk diskon 10%
                        </p>
                        <p class="text-xs text-gray-500 mt-1" x-show="form.discount_type === 'fixed'">
                            Masukkan nominal dalam Rupiah
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Maksimal Diskon (Opsional)
                        </label>
                        <input type="number" x-model="form.max_discount" min="0" step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                               placeholder="500000">
                        <p class="text-xs text-gray-500 mt-1">Untuk tipe persentase</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Minimal Transaksi
                        </label>
                        <input type="number" x-model="form.min_transaction" min="0" step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                               placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Batas Penggunaan
                        </label>
                        <input type="number" x-model="form.usage_limit" min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                               placeholder="Unlimited">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk unlimited</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Berlaku Dari
                        </label>
                        <input type="date" x-model="form.valid_from"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Berlaku Sampai
                        </label>
                        <input type="date" x-model="form.valid_until"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea x-model="form.description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                              placeholder="Deskripsi voucher..."></textarea>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_active" id="voucherActive" class="rounded">
                    <label for="voucherActive" class="text-sm text-gray-700">Aktif</label>
                </div>
                
                <div class="flex gap-2 justify-end pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeVoucherModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="saving"
                            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition disabled:opacity-50">
                        <span x-show="!saving">
                            <i class="fas fa-save mr-1"></i>Simpan
                        </span>
                        <span x-show="saving">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function copyReferralLink() {
            const input = document.getElementById('referralLink');
            input.select();
            document.execCommand('copy');
            
            alert('Link referral berhasil disalin!');
        }

        // Base URL for API calls
        const baseUrl = '<?php echo e(url("/")); ?>';

        // Voucher Manager Alpine Component for Affiliator
        function affiliateVoucherManager(affiliatorId) {
            return {
                vouchers: [],
                loading: false,
                affiliatorId: affiliatorId,
                
                init() {
                    this.loadVouchers();
                    
                    // Listen for reload event
                    window.addEventListener('reload-vouchers', () => {
                        this.loadVouchers();
                    });
                },
                
                async loadVouchers() {
                    this.loading = true;
                    try {
                        const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/list?affiliator_id=${this.affiliatorId}`);
                        const data = await response.json();
                        this.vouchers = data.data || [];
                    } catch (error) {
                        console.error('Error loading vouchers:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                openCreateModal() {
                    window.dispatchEvent(new CustomEvent('open-voucher-modal', { 
                        detail: { mode: 'create', affiliatorId: this.affiliatorId } 
                    }));
                },
                
                openEditModal(voucher) {
                    window.dispatchEvent(new CustomEvent('open-voucher-modal', { 
                        detail: { mode: 'edit', voucher: voucher } 
                    }));
                },
                
                async deleteVoucher(id) {
                    if (!confirm('Hapus voucher ini?')) return;
                    
                    try {
                        const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            alert('Voucher berhasil dihapus');
                            this.loadVouchers();
                        } else {
                            alert(data.message || 'Gagal menghapus voucher');
                        }
                    } catch (error) {
                        console.error('Error deleting voucher:', error);
                        alert('Terjadi kesalahan saat menghapus voucher');
                    }
                },
                
                formatNumber(num) {
                    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },
                
                formatDate(date) {
                    if (!date) return '-';
                    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                },
                
                isValidDate(voucher) {
                    const now = new Date();
                    const validFrom = voucher.valid_from ? new Date(voucher.valid_from) : null;
                    const validUntil = voucher.valid_until ? new Date(voucher.valid_until) : null;
                    
                    if (validFrom && now < validFrom) return false;
                    if (validUntil && now > validUntil) return false;
                    
                    return true;
                },
                
                getStatusText(voucher) {
                    if (!voucher.is_active) return 'Tidak Aktif';
                    if (!this.isValidDate(voucher)) return 'Kadaluarsa';
                    if (voucher.usage_limit && voucher.usage_count >= voucher.usage_limit) return 'Habis';
                    return 'Aktif';
                }
            }
        }

        // Voucher Modal Data
        function voucherModalData() {
            return {
                editMode: false,
                saving: false,
                affiliatorId: null,
                form: {
                    id: null,
                    id_affiliator: null,
                    code: '',
                    discount_type: 'percentage',
                    discount_value: '',
                    max_discount: '',
                    min_transaction: 0,
                    usage_limit: '',
                    valid_from: '',
                    valid_until: '',
                    description: '',
                    is_active: true
                },
                
                init() {
                    window.addEventListener('open-voucher-modal', (e) => {
                        const { mode, voucher, affiliatorId } = e.detail;
                        this.editMode = mode === 'edit';
                        
                        if (mode === 'create') {
                            this.resetForm();
                            this.affiliatorId = affiliatorId;
                            this.form.id_affiliator = affiliatorId;
                        } else {
                            this.form = { ...voucher };
                            this.affiliatorId = voucher.id_affiliator;
                        }
                        
                        document.getElementById('voucherModal').classList.remove('hidden');
                    });
                },
                
                async generateCode() {
                    try {
                        const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/generate-code`);
                        const data = await response.json();
                        if (data.success) {
                            this.form.code = data.code;
                        }
                    } catch (error) {
                        console.error('Error generating code:', error);
                    }
                },
                
                async saveVoucher() {
                    this.saving = true;
                    try {
                        const url = this.editMode 
                            ? `${baseUrl}/admin/affiliate/vouchers/${this.form.id}`
                            : `${baseUrl}/admin/affiliate/vouchers`;
                        
                        const method = this.editMode ? 'PUT' : 'POST';
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            alert(data.message);
                            closeVoucherModal();
                            // Reload vouchers
                            window.dispatchEvent(new CustomEvent('reload-vouchers'));
                        } else {
                            alert(data.message || 'Gagal menyimpan voucher');
                        }
                    } catch (error) {
                        console.error('Error saving voucher:', error);
                        alert('Terjadi kesalahan saat menyimpan voucher');
                    } finally {
                        this.saving = false;
                    }
                },
                
                resetForm() {
                    this.form = {
                        id: null,
                        id_affiliator: this.affiliatorId,
                        code: '',
                        discount_type: 'percentage',
                        discount_value: '',
                        max_discount: '',
                        min_transaction: 0,
                        usage_limit: '',
                        valid_from: '',
                        valid_until: '',
                        description: '',
                        is_active: true
                    };
                }
            }
        }

        function closeVoucherModal() {
            document.getElementById('voucherModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('voucherModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVoucherModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVoucherModal();
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/affiliate/dashboard.blade.php ENDPATH**/ ?>