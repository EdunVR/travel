@php
    $slug = $node->partnershipProgram?->slug ?? '';
    $colorMap = [
        'hm-master'  => ['bg' => 'bg-purple-500', 'border' => 'border-purple-300', 'badge' => 'bg-purple-100 text-purple-700'],
        'hm-leader'  => ['bg' => 'bg-indigo-500', 'border' => 'border-indigo-300', 'badge' => 'bg-indigo-100 text-indigo-700'],
        'hm-partner' => ['bg' => 'bg-green-500',  'border' => 'border-green-300',  'badge' => 'bg-green-100 text-green-700'],
        'hm-seller'  => ['bg' => 'bg-blue-500',   'border' => 'border-blue-300',   'badge' => 'bg-blue-100 text-blue-700'],
        'hm-member'  => ['bg' => 'bg-gray-400',   'border' => 'border-gray-300',   'badge' => 'bg-gray-100 text-gray-600'],
    ];
    $colors = $colorMap[$slug] ?? $colorMap['hm-member'];

    // Tentukan downline berdasarkan level
    $children = collect();
    if ($slug === 'hm-master')  $children = $node->downlineLeaders ?? collect();
    elseif ($slug === 'hm-leader')  $children = $node->downlinePartners ?? collect();
    elseif ($slug === 'hm-partner') $children = $node->downlineSellers ?? collect();

    $viewUrl = route('admin.inventaris.affiliate.show', $node);
    $editUrl = route('admin.inventaris.affiliate.show', $node);
@endphp

<div class="tree-node">
    {{-- Connector dari atas (kecuali root) --}}
    @if($level > 0)
    <div class="tree-connector-top"></div>
    @endif

    {{-- Card Node --}}
    <div class="tree-card bg-white rounded-xl border-2 {{ $colors['border'] }} shadow-sm p-3 w-44 text-center"
         onclick="window.location='{{ $viewUrl }}'"
         oncontextmenu="showContextMenu(event, {{ $node->id }}, '{{ addslashes($node->full_name) }}', '{{ $node->partnershipProgram?->name }}', '{{ $viewUrl }}', '{{ $editUrl }}')">

        {{-- Avatar --}}
        <div class="w-12 h-12 rounded-full {{ $colors['bg'] }} flex items-center justify-center text-white font-bold text-lg mx-auto mb-2 overflow-hidden border-2 border-white shadow">
            @if($node->photo)
                <img src="{{ $node->photo_url }}" alt="{{ $node->full_name }}" class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr($node->full_name, 0, 1)) }}
            @endif
        </div>

        {{-- Info --}}
        <div class="font-semibold text-slate-800 text-xs leading-tight truncate" title="{{ $node->full_name }}">
            {{ $node->full_name }}
        </div>
        <div class="text-xs text-slate-400 truncate">@{{ $node->username }}</div>

        {{-- Badge Program --}}
        <span class="inline-block mt-1.5 text-xs px-2 py-0.5 rounded-full {{ $colors['badge'] }} font-medium">
            {{ $node->partnershipProgram?->name ?? 'N/A' }}
        </span>

        {{-- Stats --}}
        <div class="mt-2 pt-2 border-t border-slate-100 grid grid-cols-2 gap-1 text-xs">
            <div>
                <div class="text-slate-400">Penjualan</div>
                <div class="font-semibold text-slate-700">{{ $node->total_sales }}</div>
            </div>
            <div>
                <div class="text-slate-400">Status</div>
                <div class="font-semibold {{ $node->status === 'active' ? 'text-green-600' : 'text-amber-600' }}">
                    {{ $node->status === 'active' ? 'Aktif' : ucfirst($node->status) }}
                </div>
            </div>
        </div>

        {{-- Downline count --}}
        @if($children->isNotEmpty())
        <div class="mt-1 text-xs text-slate-400">
            <i class="fas fa-users mr-1"></i>{{ $children->count() }} downline
        </div>
        @endif
    </div>

    {{-- Children --}}
    @if($children->isNotEmpty())
    <div class="tree-children">
        <div class="tree-children-wrapper">
            @foreach($children as $child)
            <div class="relative pt-6">
                <div class="tree-connector-top absolute top-0 left-1/2 -translate-x-1/2"></div>
                @include('admin.affiliate.partials.tree-node', ['node' => $child, 'level' => $level + 1])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
