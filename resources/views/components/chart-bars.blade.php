@props([
    'title' => 'Performa',
    // contoh: "6,8,7,10,12,9,14"
    'data' => '6,8,7,10,12,9,14',
])

@php
    $vals = collect(explode(',', $data))->map(fn($v)=> (int)trim($v));
    $max = max($vals->all()) ?: 1;
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <div class="font-medium text-sm sm:text-base">{{ $title }}</div>

    {{-- Container fleksibel: bar menyebar rapi di mobile --}}
    <div class="mt-3 h-20 sm:h-24 flex items-end gap-1.5 min-w-0">
        @foreach($vals as $v)
            @php $h = max(6, round(($v / $max) * 96)); @endphp
            <div
                class="flex-1 basis-0 min-w-[10px] max-w-[22px] rounded-md bg-primary-200/80 hover:bg-primary-300 transition"
                style="height: {{ $h }}px">
            </div>
        @endforeach
    </div>
</div>
