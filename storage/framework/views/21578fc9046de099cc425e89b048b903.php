<?php
$affiliator = \App\Models\Affiliator::where('username', session('affiliate_username'))->first();
$currentRoute = request()->route()->getName();
?>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
    <!-- Profile Section -->
    <div class="p-4 bg-gradient-to-r from-green-600 to-green-500 text-white text-center">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xl mx-auto mb-2 overflow-hidden border-2 border-white/30">
            <?php if($affiliator && $affiliator->photo): ?>
                <img src="<?php echo e($affiliator->photo_url); ?>" alt="<?php echo e($affiliator->full_name); ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <?php echo e(strtoupper(substr($affiliator->full_name ?? 'M', 0, 2))); ?>

            <?php endif; ?>
        </div>
        <div class="font-semibold text-sm"><?php echo e($affiliator->full_name ?? 'Mitra'); ?></div>
        <div class="text-xs opacity-90"><?php echo e($affiliator->partnershipProgram->name ?? 'HM Seller'); ?></div>
        <div class="text-xs opacity-75 mt-1">Sejak <?php echo e($affiliator->created_at->format('M Y')); ?></div>
    </div>

    <!-- Menu Items -->
    <nav class="p-2">
        <a href="<?php echo e(route('affiliate.dashboard')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.dashboard' ? 'text-white bg-green-600' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-home w-5"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo e(route('affiliate.referrals')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.referrals' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-users w-5"></i>
            <span>Referrals</span>
        </a>
        <a href="<?php echo e(route('affiliate.payments')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.payments' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-money-bill-wave w-5"></i>
            <span>Payments</span>
        </a>
        <a href="<?php echo e(route('affiliate.wallet')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.wallet' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-wallet w-5"></i>
            <span>Wallet</span>
        </a>
        
        <div class="border-t border-gray-100 my-2"></div>
        
        <a href="<?php echo e(route('affiliate.profile')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.profile' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-user w-5"></i>
            <span>Profile</span>
        </a>
        <a href="<?php echo e(route('affiliate.marketing')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.marketing' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-bullhorn w-5"></i>
            <span>Marketing</span>
        </a>
        <a href="<?php echo e(route('affiliate.reports')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.reports' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-chart-bar w-5"></i>
            <span>Reports</span>
        </a>
        <a href="<?php echo e(route('affiliate.leaderboard')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.leaderboard' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
            <i class="fas fa-trophy w-5"></i>
            <span>Leaderboard</span>
        </a>
        <a href="<?php echo e(route('affiliate.hierarchy')); ?>" 
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg mb-1 transition <?php echo e($currentRoute == 'affiliate.hierarchy' ? 'text-white bg-green-600 font-medium' : 'text-gray-700 hover:bg-gray-50'); ?>">
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\affiliate\partials\sidebar.blade.php ENDPATH**/ ?>