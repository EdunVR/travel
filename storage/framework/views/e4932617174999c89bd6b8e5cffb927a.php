<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Investor - <?php echo e($title ?? 'Dashboard'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-green-700 text-white shadow-lg">
            <div class="p-4 flex items-center space-x-3 border-b border-green-600">
                <img src="<?php echo e(asset('images/logo-investor.png')); ?>" alt="Logo" class="h-10">
                <span class="font-bold text-xl">Portal Investor</span>
            </div>
            <div class="p-4 border-b border-green-600 flex items-center space-x-3">
                <img src="<?php echo e(auth()->guard('investor')->user()->photoUrl); ?>" 
                     class="h-10 w-10 rounded-full object-cover border-2 border-white">
                <div>
                    <div class="font-medium"><?php echo e(auth()->guard('investor')->user()->name); ?></div>
                    <div class="text-xs text-green-100"><?php echo e(auth()->guard('investor')->user()->email); ?></div>
                </div>
            </div>
            <nav class="mt-4">
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.dashboard')).'','icon' => 'fas fa-chart-line','active' => request()->routeIs('investor.dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.dashboard')).'','icon' => 'fas fa-chart-line','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.dashboard'))]); ?>
                    Dashboard
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.accounts')).'','icon' => 'fas fa-wallet','active' => request()->routeIs('investor.accounts')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.accounts')).'','icon' => 'fas fa-wallet','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.accounts'))]); ?>
                    Investasi Saya
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.profits')).'','icon' => 'fas fa-hand-holding-usd','active' => request()->routeIs('investor.profits')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.profits')).'','icon' => 'fas fa-hand-holding-usd','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.profits'))]); ?>
                    Bagi Hasil
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.withdrawals')).'','icon' => 'fas fa-money-bill-wave','active' => request()->routeIs('investor.withdrawals')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.withdrawals')).'','icon' => 'fas fa-money-bill-wave','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.withdrawals'))]); ?>
                    Pencairan
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.documents')).'','icon' => 'fas fa-file-alt','active' => request()->routeIs('investor.documents')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.documents')).'','icon' => 'fas fa-file-alt','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.documents'))]); ?>
                    Dokumen
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal3b82244cb8f27515f189c97d7a36955b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b82244cb8f27515f189c97d7a36955b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.nav-link','data' => ['href' => ''.e(route('investor.profile')).'','icon' => 'fas fa-user','active' => request()->routeIs('investor.profile')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('investor.profile')).'','icon' => 'fas fa-user','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('investor.profile'))]); ?>
                    Profil Saya
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $attributes = $__attributesOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__attributesOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b82244cb8f27515f189c97d7a36955b)): ?>
<?php $component = $__componentOriginal3b82244cb8f27515f189c97d7a36955b; ?>
<?php unset($__componentOriginal3b82244cb8f27515f189c97d7a36955b); ?>
<?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-800"><?php echo e($title ?? 'Dashboard'); ?></h1>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 rounded-full hover:bg-gray-100">
                            <i class="fas fa-bell text-gray-600"></i>
                        </button>
                        <form method="POST" action="<?php echo e(route('investor.logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-sm text-gray-600 hover:text-green-700">
                                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <?php echo e($slot); ?>

            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\layouts\investor.blade.php ENDPATH**/ ?>