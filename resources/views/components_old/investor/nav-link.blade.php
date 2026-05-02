@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" @class([
    'flex items-center px-4 py-3 text-sm transition-colors',
    'bg-green-800 text-white' => $active,
    'text-green-100 hover:bg-green-600' => !$active,
])>
    <i class="{{ $icon }} w-6 text-center mr-3"></i>
    <span>{{ $slot }}</span>
    @if($active)
        <span class="ml-auto w-1 h-6 bg-white rounded-l"></span>
    @endif
</a>
