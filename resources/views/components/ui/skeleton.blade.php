{{-- Skeleton Loading Component --}}
@props([
    'rows' => 3,
    'columns' => 4,
    'height' => 'h-4',
    'type' => 'table' // table, card, list
])

@if($type === 'table')
    <div class="animate-pulse">
        <div class="bg-gray-200 h-8 rounded mb-4"></div>
        @for($i = 0; $i < $rows; $i++)
            <div class="grid grid-cols-{{ $columns }} gap-4 mb-3">
                @for($j = 0; $j < $columns; $j++)
                    <div class="bg-gray-200 {{ $height }} rounded"></div>
                @endfor
            </div>
        @endfor
    </div>
@elseif($type === 'card')
    <div class="animate-pulse">
        @for($i = 0; $i < $rows; $i++)
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="bg-gray-200 h-6 rounded mb-3"></div>
                <div class="bg-gray-200 h-4 rounded mb-2"></div>
                <div class="bg-gray-200 h-4 rounded w-3/4"></div>
            </div>
        @endfor
    </div>
@elseif($type === 'list')
    <div class="animate-pulse">
        @for($i = 0; $i < $rows; $i++)
            <div class="flex items-center space-x-4 mb-4">
                <div class="bg-gray-200 h-10 w-10 rounded-full"></div>
                <div class="flex-1">
                    <div class="bg-gray-200 h-4 rounded mb-2"></div>
                    <div class="bg-gray-200 h-3 rounded w-2/3"></div>
                </div>
            </div>
        @endfor
    </div>
@endif