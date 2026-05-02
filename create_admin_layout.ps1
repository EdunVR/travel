$content = @'
<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-slate-50 to-white overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MORRA ERP' }}</title>

    {{-- Tailwind CSS - Production Ready --}}
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <!-- Bootstrap (for modals) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Outlet Helper --}}
    <script src="{{ asset('js/outlet-helper.js') }}"></script>
    
    {{-- Finance Components --}}
    <script src="{{ asset('js/finance-components.js') }}"></script>

    {{-- Boxicons --}}
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        html, body { width: 100%; }
        body { overflow-x: hidden; }
        svg { display: block; max-width: 100%; height: auto; }
        img { max-width: 100%; height: auto; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full text-ink-900 overflow-x-hidden" 
      x-data="{ loading: true }"
      x-init="window.addEventListener('load', () => loading = false)">
    
    <!-- GLOBAL LOADING OVERLAY -->
    <div id="global-loading"
        class="fixed inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-md z-[9999] transition-opacity duration-700 opacity-100">
        <div class="relative">
            <img src="{{ url(asset('img/logo_xx.png')) }}"
                class="w-20 h-20 animate-bounce drop-shadow-lg" />
            <div class="absolute inset-0 rounded-full border-4 border-red-500 animate-ping"></div>
        </div>
        <div class="mt-6 text-lg font-semibold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent animate-pulse">
            Memuat data, mohon tunggu...
        </div>
    </div>

    {{-- Modal Loading Overlay --}}
    <div
        x-data="{ modalLoading: false }"
        x-show="modalLoading"
        x-transition.opacity
        id="modal-loader"
        class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/20 backdrop-blur-[1px]"
        style="display: none;"
    >
        <div class="bg-white rounded-2xl shadow-card px-6 py-4 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary-500 border-t-transparent"></div>
            <p class="text-sm text-primary-700 font-medium">MORRA Sedang Memuat, sabar ya...</p>
        </div>
    </div>

    {{-- Notification Toast Component --}}
    <x-notifications />

    {{-- Main content area (NO SIDEBAR, NO TAB BAR) --}}
    <div class="min-h-screen flex flex-col">
        {{-- Main Content Area --}}
        <main class="flex-1 px-4 lg:px-6 py-6 overflow-x-hidden">
            {{ $slot }}
        </main>

        <footer class="px-4 lg:px-6 pb-6 text-xs text-slate-500">
            © {{ date('Y') }} Admin ERP. Semua data dummy.
        </footer>
    </div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('loader', {
        show() { 
            const el = document.querySelector('[x-data*="loading"]');
            if (el && el.__x) el.__x.$data.loading = true;
        },
        hide() { 
            const el = document.querySelector('[x-data*="loading"]');
            if (el && el.__x) el.__x.$data.loading = false;
        }
    });
});

document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
            const loader = document.getElementById('global-loading');
            if (loader) loader.style.display = 'flex';
        }
    });
});
</script>

{{-- Hide global loading after page load --}}
<script>
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
</script>

</body>
</html>
'@

$content | Out-File -FilePath "resources/views/components/layouts/admin.blade.php" -Encoding UTF8 -NoNewline
Write-Host "File created successfully!"
Get-Item "resources/views/components/layouts/admin.blade.php" | Select-Object Name, Length
