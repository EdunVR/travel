{{-- Enhanced Loading Component --}}
<div x-data="{ show: false }" x-show="show" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 flex items-center gap-4 max-w-sm mx-4">
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary-500 border-t-transparent"></div>
        <div>
            <p class="font-medium text-slate-800">Memuat data...</p>
            <p class="text-sm text-slate-500">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>

{{-- Usage: Add x-on:click to trigger loading --}}