<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenjang Saya - HM Tour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center gap-3">
            <img src="{{ url('WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png') }}" alt="HM Tour" class="h-10" onerror="this.style.display='none'">
            <div class="border-l pl-3">
                <div class="text-sm font-semibold text-gray-900">Jenjang Saya</div>
                <div class="text-xs text-gray-500">{{ $affiliator->full_name }}</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('affiliate.dashboard') }}" class="text-sm text-gray-600 hover:text-green-600">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>
            <a href="{{ route('affiliate.logout') }}" class="text-sm text-red-600 hover:text-red-700">
                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 py-4 space-y-4">

    {{-- Legend --}}
    <div class="bg-white rounded-xl border border-gray-100 p-3 flex flex-wrap gap-4 text-xs text-gray-600">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span> HM Master</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span> HM Leader</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> HM Partner</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> HM Seller</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span> HM Member</span>
        <span class="flex items-center gap-1.5 ml-4"><span class="w-6 h-0.5 bg-green-500 inline-block"></span> Garis hijau = ada fee</span>
        <span class="flex items-center gap-1.5"><span class="w-6 border-t-2 border-dashed border-gray-300 inline-block"></span> Belum ada fee</span>
    </div>

    {{-- SVG Tree — tinggi diatur otomatis oleh JS via aspect-ratio --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm" style="overflow:hidden; width:100%">
        <svg id="tree-svg" style="width:100%;display:block"></svg>
    </div>

    {{-- Fee dari Downline --}}
    @if($feeReceived->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-coins text-amber-500"></i> Fee dari Downline (20 Terakhir)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold text-gray-600">Dari</th>
                        <th class="text-center px-4 py-2 font-semibold text-gray-600">Termin</th>
                        <th class="text-right px-4 py-2 font-semibold text-gray-600">Jumlah</th>
                        <th class="text-center px-4 py-2 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($feeReceived as $fd)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $fd->fromAffiliator?->full_name }}</td>
                        <td class="px-4 py-2 text-center">
                            @if($fd->termin)
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $fd->termin === 'termin_1' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $fd->termin === 'termin_1' ? 'Termin 1' : 'Termin 2' }}
                            </span>
                            @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                Fee Jenjang
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-semibold text-green-700">Rp {{ number_format($fd->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $fd->status === 'released' ? 'bg-green-100 text-green-700' : ($fd->status === 'paid' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $fd->status === 'released' ? 'Cair' : ($fd->status === 'paid' ? 'Dibayar' : 'Pending') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script src="{{ asset('js/affiliate-tree.js') }}?v={{ time() }}"></script>
<script>
@php
    $slug  = $affiliator->partnershipProgram?->slug ?? '';
    $myId  = $affiliator->id;
    $treeNodes = [];
    $treeEdges = [];

    // Self
    $treeNodes[] = [
        'id'      => $affiliator->id,
        'name'    => $affiliator->full_name,
        'program' => $affiliator->partnershipProgram?->name ?? '-',
        'slug'    => $slug,
        'isSelf'  => true,
    ];

    // 1 upline langsung
    if ($directUpline) {
        $uplineSlug = $directUpline->partnershipProgram?->slug ?? '';
        $treeNodes[] = [
            'id'      => $directUpline->id,
            'name'    => $directUpline->full_name,
            'program' => $directUpline->partnershipProgram?->name ?? '-',
            'slug'    => $uplineSlug,
            'isSelf'  => false,
        ];
        
        // Ambil fee setting spesifik atau global untuk upline
        $feeSetting = \App\Models\AffiliateHierarchySetting::getFeeForPair(
            $slug,
            $uplineSlug,
            $affiliator->id,
            $directUpline->id
        );
        
        $treeEdges[] = [
            'from'       => $affiliator->id,
            'to'         => $directUpline->id,
            'has_fee'    => false,
            'fee_total'  => 0,
            'percentage' => $feeSetting['percentage'] ?? 0,
            'fee_type'   => $feeSetting['fee_type'] ?? 'percentage',
            'fee_value'  => $feeSetting['fee_value'] ?? 0,
            'is_specific' => $feeSetting['is_specific'] ?? false,
            'from_level' => $slug,
            'to_level'   => $uplineSlug,
        ];
    }

    // Semua downline
    foreach ($downlines as $dl) {
        $dlSlug = $dl->partnershipProgram?->slug ?? '';
        $treeNodes[] = [
            'id'      => $dl->id,
            'name'    => $dl->full_name,
            'program' => $dl->partnershipProgram?->name ?? '-',
            'slug'    => $dlSlug,
            'isSelf'  => false,
        ];
        $dlFee = $feeReceived->where('from_affiliator_id', $dl->id)->sum('amount');
        
        // Ambil fee setting spesifik atau global untuk downline
        $feeSetting = \App\Models\AffiliateHierarchySetting::getFeeForPair(
            $dlSlug,
            $slug,
            $dl->id,
            $affiliator->id
        );
        
        $treeEdges[] = [
            'from'       => $dl->id,
            'to'         => $affiliator->id,
            'has_fee'    => $dlFee > 0,
            'fee_total'  => (float) $dlFee,
            'percentage' => $feeSetting['percentage'] ?? 0,
            'fee_type'   => $feeSetting['fee_type'] ?? 'percentage',
            'fee_value'  => $feeSetting['fee_value'] ?? 0,
            'is_specific' => $feeSetting['is_specific'] ?? false,
            'from_level' => $dlSlug,
            'to_level'   => $slug,
        ];
    }
@endphp

const NODES = @json($treeNodes);
const EDGES = @json($treeEdges);

function initTree() {
    const svg = document.getElementById('tree-svg');
    if (!svg) return;
    requestAnimationFrame(() => {
        const tree = new AffiliateTree(svg, NODES, EDGES, {
            isAdmin:    false,
            csrfToken:  '',
            feeUrl:     '',
            viewBase:   '',
            updateBase: '',
        });
        tree.render();
    });
}

window.addEventListener('load', initTree);
</script>

</body>
</html>
