{{-- Only show if user has access to at least one CRM submenu --}}
@hasModuleAccess('crm')
<ul class="sub-menu">
    @hasPermission('crm.tipe.view')
    <li class="{{ request()->routeIs('tipe.index') ? 'active' : '' }}">
        <a href="{{ route('tipe.index') }}">
            <i data-feather="tag"></i> <span>Tipe & Diskon Customer</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('crm.pelanggan.view')
    <li class="{{ request()->routeIs('member.index') ? 'active' : '' }}">
        <a href="{{ route('member.index') }}">
            <i data-feather="users"></i> <span>Manajemen Pelanggan</span>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('crm.leads.view')
    <li class="{{ request()->routeIs('prospek.index') ? 'active' : '' }}">
        <a href="{{ route('prospek.index') }}">
            <i data-feather="user-plus"></i> <span>Manajemen Prospek & Lead</span>
        </a>
    </li>
    @endhasPermission
    
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Pembuatan & Pelacakan Sales Order</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
@endhasModuleAccess
