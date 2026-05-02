<div class="flex items-center gap-2">
    @if($production->status === 'draft')
        @hasPermission('production.produksi.approve')
        <button onclick="approveProduction({{ $production->id }})" 
                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition"
                title="Setujui">
            <i class='bx bx-check text-lg'></i>
        </button>
        @endhasPermission
        @hasPermission('production.produksi.update')
        <button onclick="editProduction({{ $production->id }})" 
                class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition"
                title="Edit">
            <i class='bx bx-edit text-lg'></i>
        </button>
        @endhasPermission
        @hasPermission('production.produksi.delete')
        <button onclick="deleteProduction({{ $production->id }})" 
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                title="Hapus">
            <i class='bx bx-trash text-lg'></i>
        </button>
        @endhasPermission
    @elseif($production->status === 'approved')
        <button onclick="startProduction({{ $production->id }})" 
                class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition"
                title="Mulai Produksi">
            <i class='bx bx-play text-lg'></i>
        </button>
    @elseif($production->status === 'in_progress')
        <button onclick="addRealization({{ $production->id }})" 
                class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition"
                title="Tambah Realisasi">
            <i class='bx bx-plus-circle text-lg'></i>
        </button>
    @endif
    
    <button onclick="viewProduction({{ $production->id }})" 
            class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition"
            title="Lihat Detail">
        <i class='bx bx-show text-lg'></i>
    </button>
</div>
