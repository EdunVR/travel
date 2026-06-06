{{-- Travel Management Submenu --}}
<ul class="nav nav-treeview">
    {{-- Master Data Section --}}
    @hasPermission('travel.airline.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.airline.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.airline.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Maskapai</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.airport.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.airport.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.airport.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Bandara</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.flight.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.flight.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.flight.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Penerbangan</p>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('travel.hotel.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.hotel.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.hotel.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Hotel</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.transport.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.transport.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.transport.*') ? 'active' : '' }}" style="padding-left: 2.5rem;">
            <i class="far fa-dot-circle nav-icon" style="font-size:0.7rem;"></i>
            <p style="font-size:0.85rem;">↳ Transportasi Saudi</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.package.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.package.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.package.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Paket Perjalanan</p>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('travel.catalog.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.catalog.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.catalog.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Katalog Paket</p>
        </a>
    </li>
    @endhasPermission
    
    {{-- Operations Section --}}
    @hasPermission('travel.booking.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.booking.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.booking.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Booking Jamaah</p>
        </a>
    </li>
    @endhasPermission
    
    {{-- Task Management Section --}}
    @hasPermission('travel.task.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.tasks.index') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Task Management</p>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('travel.task.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.tasks.my-tasks') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.tasks.my-tasks') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>My Tasks</p>
        </a>
    </li>
    @endhasPermission
    
    {{-- Communication & Reporting Section --}}
    @hasPermission('travel.communication.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.communication.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.communication.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Komunikasi</p>
        </a>
    </li>
    @endhasPermission
    
    @hasPermission('travel.report.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.report.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.report.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Laporan</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.payment-reminder.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.travel.payment-reminder.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.travel.payment-reminder.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Pengingat Pembayaran</p>
        </a>
    </li>
    @endhasPermission

    {{-- Affiliate --}}
    @hasPermission('travel.affiliate.view')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.affiliate.index') }}" class="nav-link {{ request()->routeIs('admin.inventaris.affiliate.index') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Mitra
                @php
                    $pendingAff = \App\Models\Affiliator::where('status','pending')->count();
                    $pendingPayout = \App\Models\AffiliatePayout::where('status','pending')->count();
                @endphp
                @if($pendingAff + $pendingPayout > 0)
                <span class="badge badge-warning badge-sm ml-1">{{ $pendingAff + $pendingPayout }}</span>
                @endif
            </p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.affiliate.payout')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.affiliate.payouts') }}" class="nav-link {{ request()->routeIs('admin.inventaris.affiliate.payouts') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Withdraw Mitra</p>
        </a>
    </li>
    @endhasPermission

    @hasPermission('travel.affiliate.settings')
    <li class="nav-item">
        <a href="{{ route('admin.inventaris.affiliate.settings') }}" class="nav-link {{ request()->routeIs('admin.inventaris.affiliate.settings') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Pengaturan Affiliate</p>
        </a>
    </li>
    @endhasPermission
</ul>
