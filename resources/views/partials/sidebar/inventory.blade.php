{{-- Only show if user has access to at least one Inventory submenu --}}
@hasModuleAccess('inventory')
<ul class="sub-menu">
    @hasAnyPermission('sistem.outlets.view', 'inventory.barang.view')
    <li class="{{ request()->routeIs('admin.inventaris.outlet.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.outlet.index') }}">
            <i data-feather="crosshair"></i> <span>Outlet</span>
        </a>
    </li>
    @endhasAnyPermission
    
    @hasPermission('inventory.kategori.view')
    <li class="{{ request()->routeIs('admin.inventaris.kategori.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.kategori.index') }}">
            <i data-feather="grid"></i> <span>Kategori Umum</span>
        </a>
    </li>
    @endhasPermission
    
    @hasAnyPermission('inventory.barang.view', 'inventory.kategori.view')
    <li class="{{ request()->routeIs('admin.inventaris.satuan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.satuan.index') }}">
            <i data-feather="pocket"></i> <span>Satuan</span>
        </a>
    </li>
    @endhasAnyPermission
    
    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('admin.inventaris.produk.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.produk.index') }}">
            <i data-feather="package"></i> <span>Produk</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.barang.view')
    <li class="{{ request()->routeIs('admin.inventaris.bahan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.bahan.index') }}">
            <i data-feather="layers"></i> <span>Bahan</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.stok.view')
    <li class="{{ request()->routeIs('admin.inventaris.inventori.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.inventori.index') }}">
            <i data-feather="database"></i> <span>Inventori/Stok</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('inventory.transfer.view')
    <li class="{{ request()->routeIs('manajemen-gudang.*') ? 'active' : '' }}">
        <a href="{{ route('manajemen-gudang.index') }}">
            <i data-feather="send"></i> <span>Transfer Gudang</span>
        </a>
    </li>
    @endhasPermission
    
    {{-- Stock Opname route doesn't exist yet, commenting out
    @hasPermission('inventory.opname.view')
    <li class="{{ request()->routeIs('admin.inventaris.opname.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.opname.index') }}">
            <i data-feather="clipboard"></i> <span>Stock Opname</span>
        </a>
    </li>
    @endhasPermission
    --}}
    
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Analisis Inventaris</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
@endhasModuleAccess
