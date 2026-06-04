<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?> - Dashboard Mitra - HM Tour</title>
    <!-- Favicon from CompanySettings -->
    <?php
        try {
            $settings = \App\Models\CompanySetting::first();
            $faviconUrl = $settings && $settings->favicon_url ? $settings->favicon_url : ($settings && $settings->logo_url ? $settings->logo_url : url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png'));
        } catch (\Exception $e) {
            $faviconUrl = url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        }
    ?>
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>">
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
                        <div class="text-sm font-semibold text-gray-900">Dashboard Mitra</div>
                        <?php
                            $affiliator = \App\Models\Affiliator::where('username', session('affiliate_username'))->first();
                        ?>
                        <div class="text-xs text-gray-500"><?php echo e($affiliator->full_name ?? 'Mitra'); ?></div>
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
                <?php echo $__env->make('affiliate.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\affiliate\layouts\app.blade.php ENDPATH**/ ?>