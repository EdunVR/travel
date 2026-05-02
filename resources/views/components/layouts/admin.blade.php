<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-slate-50 to-white overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Force 24-hour format for time inputs -->
    <meta name="time-format" content="24">
    <meta name="locale" content="id-ID">
    
    <title>{{ $title ?? 'MORRA ERP' }}</title>

    @if(app()->environment('production'))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            if (typeof tailwind !== 'undefined') {
                tailwind.config = {
                    theme: {
                        container: { center: true, padding: '1rem' },
                        extend: {
                            colors: {
                                primary: {50:'#eef7ff',100:'#daecff',200:'#b6d8ff',300:'#87beff',400:'#55a0ff',500:'#2f86ff',600:'#186ae6',700:'#1354b4',800:'#0f418c',900:'#0c356f'},
                                ink: { 900:'#0f172a', 700:'#334155', 500:'#64748b' }
                            },
                            boxShadow: {
                                card: '0 6px 20px rgba(15,23,42,.06)',
                                float: '0 14px 40px rgba(15,23,42,.10)',
                            },
                            borderRadius: { '2xl': '1rem' }
                        }
                    }
                }
            }
        </script>
    @endif
    
    {{-- Desktop Responsive Scaling CSS --}}
    <link rel="stylesheet" href="{{ asset('css/desktop-responsive-scaling.css') }}">
    
    {{-- Dropdown Text Fix CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dropdown-fix.css') }}?v={{ time() }}">
    
    {{-- Force Dropdown Full Text (Highest Priority) --}}
    <link rel="stylesheet" href="{{ asset('css/force-dropdown-full-text.css') }}?v={{ time() }}">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/datatable-helper.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/emergency-alpine-datatable-fix.js') }}"></script>
    <script src="{{ asset('js/inter-outlet.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/outlet-helper.js') }}"></script>
    <script src="{{ asset('js/finance-components.js') }}"></script>
    <script src="{{ asset('js/date-helper.js') }}"></script>
    <script src="{{ asset('js/lazy-loading.js') }}"></script>
    
    {{-- Desktop Responsive Scaling - Auto-scale for desktop viewports --}}
    <script src="{{ asset('js/desktop-responsive-scaling.js') }}"></script>
    
    <script src="{{ asset('js/alpine-helpers.js') }}"></script>
    <script src="{{ asset('js/production-form-fix.js') }}"></script>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Force 24-hour format CSS -->
    <link rel="stylesheet" href="{{ asset('css/force-24hour-format.css') }}">
    
    <!-- Force 24-hour format JavaScript -->
    <script src="{{ asset('js/force-24hour-format.js?v=1769175062') }}?v={{ time() }}"></script>

    <style>
        html, body { width: 100%; }
        body { overflow-x: hidden; }
        svg { display: block; max-width: 100%; height: auto; }
        img { max-width: 100%; height: auto; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* GLOBAL 24-HOUR FORMAT ENFORCEMENT - SUPER AGGRESSIVE */
        
        /* Hide ALL AM/PM elements in WebKit browsers */
        input[type="time"]::-webkit-datetime-edit-ampm-field,
        input[type="time"]::-webkit-datetime-edit-meridiem-field {
            display: none !important;
            visibility: hidden !important;
            width: 0 !important;
            height: 0 !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }
        
        /* Hide AM/PM text elements */
        input[type="time"]::-webkit-datetime-edit-text {
            display: none !important;
        }
        
        /* Force appearance */
        input[type="time"] {
            -webkit-appearance: none !important;
            -moz-appearance: textfield !important;
            appearance: none !important;
        }
        
        /* Firefox AM/PM hiding */
        input[type="time"]::-moz-time-picker-ampm {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* Additional selectors for different browsers */
        input[type="time"]::after,
        input[type="time"]::before {
            content: "" !important;
        }
        
        /* Force container styling */
        input[type="time"]::-webkit-datetime-edit {
            padding: 0 !important;
        }
        
        /* Style visible parts */
        input[type="time"]::-webkit-datetime-edit-hour-field,
        input[type="time"]::-webkit-datetime-edit-minute-field {
            color: inherit !important;
        }
        
        /* Microsoft Edge specific */
        input[type="time"]::-ms-clear {
            display: none !important;
        }
        
        /* Additional hiding for any AM/PM related elements */
        input[type="time"] *[class*="ampm"],
        input[type="time"] *[class*="meridiem"],
        input[type="time"] *[id*="ampm"],
        input[type="time"] *[id*="meridiem"] {
            display: none !important;
            visibility: hidden !important;
        }
    </style>

<script>
// Ensure Alpine.js is properly initialized before POS app
document.addEventListener('alpine:init', () => {
    console.log('🏔️ [ALPINE] Alpine.js initialized successfully');
});

// Add global error handler for Alpine.js
window.addEventListener('error', (e) => {
    if (e.message && e.message.includes('Alpine')) {
        console.error('❌ [ALPINE] Alpine.js error:', e.message);
        console.error('📄 [ALPINE] Error details:', e);
    }
});

// Debug Alpine.js loading
console.log('🔍 [DEBUG] Checking Alpine.js availability...');
if (typeof Alpine !== 'undefined') {
    console.log('✅ [DEBUG] Alpine.js is available');
} else {
    console.log('⏳ [DEBUG] Alpine.js not yet available, waiting...');
}
</script>
</head>
<body class="h-full text-ink-900 overflow-x-hidden" x-data="{ loading: true }" x-init="window.addEventListener('load', () => loading = false)">
    
    <div id="global-loading" class="fixed inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-md z-[9999] transition-opacity duration-700 opacity-100">
        <div class="relative">
            <img src="{{ url(asset('img/logo_xx.png')) }}" class="w-20 h-20 animate-bounce drop-shadow-lg" />
            <div class="absolute inset-0 rounded-full border-4 border-red-500 animate-ping"></div>
        </div>
        <div class="mt-6 text-lg font-semibold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent animate-pulse">
            Memuat data, mohon tunggu...
        </div>
    </div>

    <div x-data="{ modalLoading: false }" x-show="modalLoading" x-transition.opacity id="modal-loader" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/20 backdrop-blur-[1px]" style="display: none;">
        <div class="bg-white rounded-2xl shadow-card px-6 py-4 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary-500 border-t-transparent"></div>
            <p class="text-sm text-primary-700 font-medium">MORRA Sedang Memuat, sabar ya...</p>
        </div>
    </div>

    <x-notifications />

    <div class="min-h-screen flex flex-col">
        <main class="flex-1 px-4 lg:px-6 py-6 overflow-x-hidden flex flex-col">
            {{ $slot }}
        </main>
        
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
                const loader = document.getElementById('global-loading');
                if (loader) loader.style.display = 'flex';
            }
        });
    });
});

window.addEventListener('load', function() {
    setTimeout(function() {
        const loader = document.getElementById('global-loading');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(function() {
                loader.style.display = 'none';
            }, 700);
        }
    }, 500);
});

window.ModalLoader = {
    show() {
        const loader = document.getElementById('modal-loader');
        if (loader) {
            loader.style.display = 'flex';
            const alpineEl = loader.__x;
            if (alpineEl && alpineEl.$data) {
                alpineEl.$data.modalLoading = true;
            }
        }
    },
    hide() {
        const loader = document.getElementById('modal-loader');
        if (loader) {
            const alpineEl = loader.__x;
            if (alpineEl && alpineEl.$data) {
                alpineEl.$data.modalLoading = false;
            }
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    }
};

console.log('✅ ModalLoader initialized:', typeof window.ModalLoader);

// GLOBAL 24-HOUR FORMAT ENFORCEMENT - SUPER AGGRESSIVE
(function() {
    'use strict';
    
    console.log('🕐 Initializing GLOBAL 24-hour format enforcement...');
    
    // Function to aggressively enforce 24-hour format
    function enforce24HourFormatGlobal() {
        const timeInputs = document.querySelectorAll('input[type="time"]');
        console.log(`🔍 Found ${timeInputs.length} time inputs to process globally`);
        
        timeInputs.forEach((input, index) => {
            // Force attributes
            input.setAttribute('step', '1');
            input.setAttribute('pattern', '[0-9]{2}:[0-9]{2}');
            input.setAttribute('data-format', '24');
            input.setAttribute('data-24hour', 'true');
            
            // Remove AM/PM related attributes
            input.removeAttribute('data-12hour');
            input.removeAttribute('data-ampm');
            input.removeAttribute('data-meridiem');
            
            // Force CSS properties
            input.style.setProperty('-webkit-appearance', 'none', 'important');
            input.style.setProperty('-moz-appearance', 'textfield', 'important');
            input.style.setProperty('appearance', 'none', 'important');
            
            // Add validation
            if (!input.hasAttribute('data-24hour-validated')) {
                input.setAttribute('data-24hour-validated', 'true');
                
                input.addEventListener('input', function() {
                    const value = this.value;
                    if (value && !value.match(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
                        this.setCustomValidity('Format harus HH:MM (24 jam)');
                    } else {
                        this.setCustomValidity('');
                    }
                });
                
                input.addEventListener('focus', function() {
                    this.setAttribute('data-format', '24');
                    this.setAttribute('data-24hour', 'true');
                });
            }
            
            console.log(`✅ Processed time input ${index + 1}:`, input);
        });
    }
    
    // Run immediately when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enforce24HourFormatGlobal);
    } else {
        enforce24HourFormatGlobal();
    }
    
    // Run after page load
    window.addEventListener('load', function() {
        setTimeout(enforce24HourFormatGlobal, 100);
        setTimeout(enforce24HourFormatGlobal, 500);
        setTimeout(enforce24HourFormatGlobal, 1000);
    });
    
    // Watch for dynamically added time inputs
    const observer = new MutationObserver(function(mutations) {
        let hasNewTimeInputs = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'INPUT' && node.type === 'time') {
                            hasNewTimeInputs = true;
                        } else if (node.querySelectorAll) {
                            const timeInputs = node.querySelectorAll('input[type="time"]');
                            if (timeInputs.length > 0) {
                                hasNewTimeInputs = true;
                            }
                        }
                    }
                });
            }
        });
        
        if (hasNewTimeInputs) {
            console.log('🔄 New time inputs detected, re-enforcing 24-hour format');
            setTimeout(enforce24HourFormatGlobal, 50);
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['type']
    });
    
    // Additional enforcement for Alpine.js and other frameworks
    document.addEventListener('alpine:init', function() {
        console.log('🏔️ Alpine.js initialized, enforcing 24-hour format');
        setTimeout(enforce24HourFormatGlobal, 100);
    });
    
    // Intercept any attempts to set 12-hour format
    const originalSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function(name, value) {
        if (this.type === 'time' && (name === 'data-12hour' || name === 'data-ampm')) {
            console.log('🚫 Blocked attempt to set 12-hour format on time input');
            return;
        }
        return originalSetAttribute.call(this, name, value);
    };
    
    console.log('✅ GLOBAL 24-hour format enforcement initialized');
})();
</script>

    <x-ui.toast />

    @stack('scripts')

<!-- Button Loading Helper -->
<script src="{{ asset('js/button-loading.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Alpine.js is only started once
    if (typeof Alpine !== 'undefined' && !window.alpineStarted) {
        console.log('🏔️ Starting Alpine.js...');
        window.alpineStarted = true;
        console.log('✅ Alpine.js started successfully');
    } else if (window.alpineStarted) {
        console.log('ℹ️ Alpine.js already started, skipping initialization');
    }
});
</script>
</body>
</html>
