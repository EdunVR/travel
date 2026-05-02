@props([
    'label' => 'Metric',
    'value' => '0',
    'delta' => '+0.0%',
    'positive' => true,
])

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="text-xs sm:text-sm text-slate-500 leading-snug">{{ $label }}</div>
    <div class="mt-2 flex items-end gap-2">
        <div class="text-xl sm:text-2xl font-bold tracking-tight break-words">{{ $value }}</div>
        <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full {{ $positive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
            {{ $delta }}
        </span>
    </div>
</div>
