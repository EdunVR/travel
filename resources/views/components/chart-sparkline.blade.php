@props([
    'title' => 'Trend',
    'labels' => 'Jan,Feb,Mar,Apr,May,Jun,Jul',
    // path d svg (contoh)
    'path' => 'M0,30 L30,28 L60,26 L90,22 L120,24 L150,18 L180,14 L210,12',
])

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="flex items-center justify-between gap-3">
        <div class="font-medium text-sm sm:text-base">{{ $title }}</div>
        <div class="text-[10px] sm:text-xs text-slate-400 truncate">{{ $labels }}</div>
    </div>

    <div class="mt-3 overflow-hidden">
        {{-- SVG scale penuh, tinggi responsif --}}
        <svg viewBox="0 0 210 40" preserveAspectRatio="none" class="w-full h-20 sm:h-24">
            {{-- area halus di bawah garis --}}
            <path d="{{ $path }} L210,40 L0,40 Z" fill="rgba(47,134,255,0.10)"></path>
            <path d="{{ $path }}" fill="none" stroke="rgb(47,134,255)" stroke-width="2"></path>
        </svg>
    </div>
</div>
