<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - ERP System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen">
    
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            
            
            <div class="text-center mb-8">
                <img src="<?php echo e(url(asset('img/logo_xx.png'))); ?>" alt="Logo" class="h-24 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-slate-800">ERP System</h1>
                <p class="text-slate-600 mt-2">Masuk ke akun Anda</p>
            </div>

            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
                
                
                <?php if($errors->any()): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-start gap-3">
                        <i class='bx bx-error-circle text-2xl text-red-600'></i>
                        <div class="flex-1">
                            <h3 class="font-semibold text-red-800 mb-1">
                                <?php if($errors->has('csrf')): ?>
                                    Sesi Berakhir
                                <?php else: ?>
                                    Login Gagal
                                <?php endif; ?>
                            </h3>
                            <ul class="text-sm text-red-700 space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php if($errors->has('csrf') || str_contains(implode(' ', $errors->all()), '419')): ?>
                            <div class="mt-3 flex gap-2">
                                <button 
                                    onclick="refreshPage()" 
                                    class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors"
                                >
                                    Refresh Halaman
                                </button>
                                <button 
                                    onclick="clearCacheAndRefresh()" 
                                    class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors"
                                >
                                    Clear Cache & Refresh
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                
                <div id="sessionStatus" class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 hidden">
                    <div class="flex items-center gap-2">
                        <i class='bx bx-check-circle text-green-600'></i>
                        <span class="text-sm text-green-700">Sesi aktif dan siap untuk login</span>
                    </div>
                </div>

                
                <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="space-y-5" id="loginForm">
                    <?php echo csrf_field(); ?>

                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-envelope text-slate-400 text-xl'></i>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?php echo e(old('email')); ?>"
                                required 
                                autofocus
                                class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="nama@email.com"
                            >
                        </div>
                    </div>

                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-lock-alt text-slate-400 text-xl'></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                        </label>
                        <div class="text-xs text-slate-500" id="tokenStatus">
                            Token: <span class="font-mono" id="tokenIndicator">OK</span>
                        </div>
                    </div>

                    
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
                    >
                        <i class='bx bx-log-in text-xl'></i>
                        Masuk
                    </button>
                </form>

                
                <div class="mt-6 text-center text-sm text-slate-600">
                    <p>Default Login:</p>
                    <p class="font-mono text-xs mt-1">superadmin@morra.com / password</p>
                </div>
            </div>

            
            <div class="mt-8 text-center text-sm text-slate-500">
                <p>&copy; <?php echo e(date('Y')); ?> ERP System. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
    let tokenRefreshInterval;
    let sessionCheckInterval;

    // Enhanced CSRF token management
    function refreshCSRFToken() {
        return fetch('<?php echo e(route("login")); ?>', {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTokenMeta = doc.querySelector('meta[name="csrf-token"]');
            const newTokenInput = doc.querySelector('input[name="_token"]');
            
            if (newTokenMeta && newTokenInput) {
                document.querySelector('meta[name="csrf-token"]').content = newTokenMeta.content;
                document.querySelector('input[name="_token"]').value = newTokenInput.value;
                
                updateTokenStatus('OK', 'text-green-600');
                showSessionStatus(true);
                console.log('CSRF token refreshed successfully');
                return true;
            }
            return false;
        })
        .catch(error => {
            console.error('Failed to refresh CSRF token:', error);
            updateTokenStatus('ERROR', 'text-red-600');
            showSessionStatus(false);
            return false;
        });
    }

    function updateTokenStatus(status, className) {
        const indicator = document.getElementById('tokenIndicator');
        if (indicator) {
            indicator.textContent = status;
            indicator.className = `font-mono ${className}`;
        }
    }

    function showSessionStatus(isActive) {
        const statusDiv = document.getElementById('sessionStatus');
        if (statusDiv) {
            if (isActive) {
                statusDiv.className = 'mb-4 p-3 rounded-xl bg-green-50 border border-green-200';
                statusDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class='bx bx-check-circle text-green-600'></i>
                        <span class="text-sm text-green-700">Sesi aktif dan siap untuk login</span>
                    </div>
                `;
            } else {
                statusDiv.className = 'mb-4 p-3 rounded-xl bg-yellow-50 border border-yellow-200';
                statusDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class='bx bx-error-circle text-yellow-600'></i>
                        <span class="text-sm text-yellow-700">Sesi mungkin bermasalah, refresh halaman jika login gagal</span>
                    </div>
                `;
            }
            statusDiv.classList.remove('hidden');
        }
    }

    // Refresh page functions
    function refreshPage() {
        window.location.reload(true);
    }

    function clearCacheAndRefresh() {
        // Clear various caches
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    caches.delete(name);
                });
            });
        }
        
        // Clear session storage
        sessionStorage.clear();
        
        // Force reload with cache bypass
        window.location.reload(true);
    }

    // Enhanced form submission handling
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin text-xl"></i> Memproses...';
        
        // Re-enable button after 10 seconds as fallback
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-log-in text-xl"></i> Masuk';
            }
        }, 10000);
    });

    // Initialize token refresh
    function initializeTokenManagement() {
        // Initial token refresh
        refreshCSRFToken();
        
        // Set up periodic refresh (every 5 minutes)
        tokenRefreshInterval = setInterval(refreshCSRFToken, 5 * 60 * 1000);
        
        // Check session every minute
        sessionCheckInterval = setInterval(() => {
            const tokenAge = Date.now() - (window.tokenLastRefresh || Date.now());
            if (tokenAge > 10 * 60 * 1000) { // 10 minutes
                updateTokenStatus('OLD', 'text-yellow-600');
            }
        }, 60 * 1000);
    }

    // Activity tracking for idle detection
    let lastActivity = Date.now();
    let idleWarningShown = false;

    function resetActivity() {
        lastActivity = Date.now();
        idleWarningShown = false;
    }

    function checkIdleStatus() {
        const idleTime = Date.now() - lastActivity;
        const thirtyMinutes = 30 * 60 * 1000;
        
        if (idleTime > thirtyMinutes && !idleWarningShown) {
            showIdleWarning();
            idleWarningShown = true;
        }
    }

    function showIdleWarning() {
        const warning = document.createElement('div');
        warning.className = 'fixed top-4 right-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4 shadow-lg max-w-sm z-50';
        warning.innerHTML = `
            <div class="flex items-start gap-3">
                <i class='bx bx-time text-2xl text-yellow-600'></i>
                <div>
                    <h4 class="font-semibold text-yellow-800">Sesi Mungkin Expired</h4>
                    <p class="text-sm text-yellow-700 mt-1">Halaman sudah idle > 30 menit. Refresh jika login gagal.</p>
                    <button onclick="refreshCSRFToken(); this.parentElement.parentElement.parentElement.remove();" 
                            class="mt-2 text-xs bg-yellow-600 text-white px-2 py-1 rounded hover:bg-yellow-700">
                        Refresh Token
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(warning);
        
        // Auto remove after 15 seconds
        setTimeout(() => {
            if (warning.parentNode) {
                warning.remove();
            }
        }, 15000);
    }

    // Event listeners for activity tracking
    ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetActivity, { passive: true });
    });

    // Check idle status every minute
    setInterval(checkIdleStatus, 60 * 1000);

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTokenManagement();
        
        // Show initial session status
        setTimeout(() => showSessionStatus(true), 1000);
    });

    // Cleanup intervals when page unloads
    window.addEventListener('beforeunload', function() {
        if (tokenRefreshInterval) clearInterval(tokenRefreshInterval);
        if (sessionCheckInterval) clearInterval(sessionCheckInterval);
    });

    // Handle visibility change (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page became visible, refresh token
            refreshCSRFToken();
        }
    });

    // Expose functions globally for button onclick handlers
    window.refreshPage = refreshPage;
    window.clearCacheAndRefresh = clearCacheAndRefresh;
    </script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/auth/login.blade.php ENDPATH**/ ?>