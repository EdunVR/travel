<ul class="sub-menu">
    @if(in_array('POS', Auth::user()->akses ?? []))
        <li class="{{ request()->routeIs('penjualan.pos.*') ? 'active' : '' }}">
            <a href="{{ route('admin.penjualan.pos.index') }}">
                <i data-feather="monitor"></i> <span>Point of Sales</span>
            </a>
        </li>
    @endif
    @if(in_array('Invoice Penjualan', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('sales.invoice.history') ? 'active' : '' }}">
            <a href="{{ route('sales.invoice.history') }}">
                <i data-feather="shopping-cart"></i> <span>Invoice Penjualan</span>
            </a>
        </li>
    @endif
    @if(in_array('Penjualan', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('admin.penjualan.dashboard.index') ? 'active' : '' }}">
            <a href="{{ route('admin.penjualan.dashboard.index') }}">
                <i data-feather="shopping-cart"></i> <span>Dashboard Penjualan</span>
            </a>
        </li>
    @endif
    @if(in_array('Laporan Penjualan', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('laporan_penjualan.index') ? 'active' : '' }}">
            <a href="{{ route('laporan_penjualan.index') }}">
                <i data-feather="file-text"></i> <span>Laporan Margin</span>
            </a>
        </li>
    @endif
    @if(in_array('Agen', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('agen_gerobak.index') ? 'active' : '' }}">
            <a href="{{ route('agen_gerobak.index') }}">
                <i data-feather="file-text"></i> <span>Agen dan Gerobak</span>
            </a>
        </li>
    @endif
    @if(in_array('Halaman Agen', Auth::user()->akses ?? []))
        <li class="{{ request()->routeIs('agen.laporan.index') ? 'active' : '' }}">
            <a href="{{ route('agen.laporan.index') }}">
                <i data-feather="file-text"></i> <span>Halaman Agen</span>
            </a>
        </li>
    @endif
    <li class="unavailable">
        <a href="#">
            <i data-feather="target"></i> <span>Manajemen Pemasaran</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Analisis Penjualan dan Pemasaran</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
