<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mitra - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <?php
        try {
            $settings = \App\Models\CompanySetting::first();
            $faviconUrl = $settings && $settings->logo ? asset('storage/' . $settings->logo) : url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        } catch (\Exception $e) {
            $faviconUrl = url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png');
        }
    ?>
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>">
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center">
    
    <div class="w-full max-w-md px-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="<?php echo e(url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png')); ?>" 
                 alt="HM Tour" 
                 class="h-20 mx-auto mb-4"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div style="display:none" class="h-20 w-20 mx-auto mb-4 bg-green-600 rounded-full items-center justify-center">
                <span class="text-white text-3xl font-bold">HM</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Login Mitra</h1>
            <p class="text-gray-600 mt-2">Masuk ke dashboard mitra Anda</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            
            <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

            </div>
            <?php endif; ?>

            <form action="<?php echo e(route('affiliate.login.process')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-gray-400"></i>Username
                        </label>
                        <input type="text" name="username" value="<?php echo e(old('username')); ?>" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Masukkan username Anda">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                        </label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Masukkan password Anda">
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="<?php echo e(route('affiliate.forgot-password')); ?>" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit"
                        class="w-full mt-6 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="<?php echo e(route('affiliate.register')); ?>" class="text-green-600 hover:text-green-700 font-medium">
                        Daftar di sini
                    </a>
                </p>
            </div>

        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="<?php echo e(url('/')); ?>" class="text-gray-600 hover:text-gray-800 text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/affiliate/login.blade.php ENDPATH**/ ?>