@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" 
   class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200 {{ $active ? 'bg-green-100 text-green-600 border-r-4 border-green-600' : '' }}">
    <i class="{{ $icon }} w-5 h-5 mr-3"></i>
    <span class="font-medium">{{ $slot }}</span>
</a>