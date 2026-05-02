<ul class="sub-menu">
    @if(in_array('User', Auth::user()->akses ?? []))
        <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}">
                <i data-feather="users"></i> <span>User</span>
            </a>
        </li>
    @endif
    
    {{-- User Management - Visible for all users (temporary) --}}
    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}">
            <i data-feather="user-check"></i> <span>User Management</span>
        </a>
    </li>
    
    {{-- Role & Permission - Visible for all users (temporary) --}}
    <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <a href="{{ route('admin.roles.index') }}">
            <i data-feather="shield"></i> <span>Role & Permission</span>
        </a>
    </li>
    
    @if(in_array('Pengaturan', Auth::user()->akses ?? []))
        <li class="{{ request()->routeIs('setting.index') ? 'active' : '' }}">
            <a href="{{ route('setting.index') }}">
                <i data-feather="settings"></i> <span>Pengaturan</span>
            </a>
        </li>
    @endif
    
    @if(in_array('Pengaturan COA', Auth::user()->akses ?? []))
        <li class="{{ request()->routeIs('settings.coa.index') ? 'active' : '' }}">
            <a href="{{ route('settings.coa.index') }}">
                <i data-feather="sliders"></i> <span>Pengaturan COA</span>
            </a>
        </li>
    @endif
    
    {{-- Audit Logs - Visible for all users (temporary) --}}
    <li class="{{ request()->routeIs('admin.inventaris.admin.audit.*') ? 'active' : '' }}">
        <a href="{{ route('admin.inventaris.admin.audit.index') }}">
            <i data-feather="file-text"></i> <span>Audit Logs</span>
        </a>
    </li>
</ul>
