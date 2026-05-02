@props([
    'href' => '#',
    'icon' => 'square',
    'title' => 'Judul',
    'desc' => null,
])

<a href="{{ $href }}"
   class="group relative rounded-2xl border border-slate-200 bg-white p-5 shadow-card hover:shadow-float transition transform hover:-translate-y-0.5 overflow-hidden">
    <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-100/50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition"></div>

    <div class="flex items-start gap-3 relative">
        <div class="rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
            <x-icon :name="$icon" class="w-6 h-6 text-primary-700" />
        </div>
        <div>
            <h3 class="font-semibold text-ink-900">{{ $title }}</h3>
            @if($desc)
                <p class="mt-1 text-sm text-ink-700/80">{{ $desc }}</p>
            @endif
        </div>
    </div>

    <div class="absolute right-3 top-3 text-slate-300 group-hover:text-primary-500 transition">
        <x-icon name="arrow-right" class="w-4 h-4" />
    </div>
</a>
