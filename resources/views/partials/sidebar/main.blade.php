{{-- Main Sidebar Navigation --}}
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- Travel Management --}}
        @hasModuleAccess('travel')
        <li class="nav-item {{ request()->is('admin/travel/*') || request()->is('admin/inventaris/flight/*') || request()->is('admin/inventaris/hotel/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/travel/*') || request()->is('admin/inventaris/flight/*') || request()->is('admin/inventaris/hotel/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-plane-departure"></i>
                <p>
                    Travel Management
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.travel')
        </li>
        @endhasModuleAccess

        {{-- CRM --}}
        @hasModuleAccess('crm')
        <li class="nav-item {{ request()->is('admin/crm/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/crm/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>
                    CRM
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.customer-service')
        </li>
        @endhasModuleAccess

        {{-- Sales --}}
        @hasModuleAccess('sales')
        <li class="nav-item {{ request()->is('admin/sales/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/sales/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>
                    Sales
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.sales')
        </li>
        @endhasModuleAccess

        {{-- Finance --}}
        @hasModuleAccess('finance')
        <li class="nav-item {{ request()->is('admin/finance/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/finance/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-coins"></i>
                <p>
                    Finance
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.finance')
        </li>
        @endhasModuleAccess

        {{-- Inventory --}}
        @hasModuleAccess('inventory')
        <li class="nav-item {{ request()->is('admin/inventory/*') || request()->is('admin/inventaris/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/inventory/*') || request()->is('admin/inventaris/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                    Inventory
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.inventory')
        </li>
        @endhasModuleAccess

        {{-- Procurement --}}
        @hasModuleAccess('procurement')
        <li class="nav-item {{ request()->is('admin/procurement/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/procurement/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                    Procurement
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.procurement')
        </li>
        @endhasModuleAccess

        {{-- Production --}}
        @hasModuleAccess('production')
        <li class="nav-item {{ request()->is('admin/production/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/production/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-industry"></i>
                <p>
                    Production
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.production')
        </li>
        @endhasModuleAccess

        {{-- HRM --}}
        @hasModuleAccess('hrm')
        <li class="nav-item {{ request()->is('admin/hrm/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/hrm/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>
                    HRM
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.hrm')
        </li>
        @endhasModuleAccess

        {{-- Project Management --}}
        @hasModuleAccess('project')
        <li class="nav-item {{ request()->is('admin/project/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/project/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-project-diagram"></i>
                <p>
                    Projects
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.project-management')
        </li>
        @endhasModuleAccess

        {{-- Services --}}
        @hasModuleAccess('services')
        <li class="nav-item {{ request()->is('admin/services/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/services/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wrench"></i>
                <p>
                    Services
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.services')
        </li>
        @endhasModuleAccess

        {{-- POS --}}
        @hasModuleAccess('pos')
        <li class="nav-item {{ request()->is('admin/pos/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/pos/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-cash-register"></i>
                <p>
                    POS
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.pos')
        </li>
        @endhasModuleAccess

        {{-- Analytics --}}
        @hasModuleAccess('analytics')
        <li class="nav-item {{ request()->is('admin/analytics/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/analytics/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>
                    Analytics
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.analytics')
        </li>
        @endhasModuleAccess

        {{-- System --}}
        @hasModuleAccess('system')
        <li class="nav-item {{ request()->is('admin/system/*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/system/*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                    System
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            @include('partials.sidebar.system')
        </li>
        @endhasModuleAccess

    </ul>
</nav>
