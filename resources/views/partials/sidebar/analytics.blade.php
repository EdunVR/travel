<ul class="sub-menu">
    @if(in_array('Laporan', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('laporan.index') ? 'active' : '' }}">
            <a href="{{ route('laporan.index') }}">
                <i data-feather="file-text"></i> <span>Laporan Umum</span>
            </a>
        </li>
    @endif
    @if(in_array('Laporan Penjualan', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('laporan_penjualan.index') ? 'active' : '' }}">
            <a href="{{ route('laporan_penjualan.index') }}">
                <i data-feather="file-text"></i> <span>Laporan Penjualan</span>
            </a>
        </li>
    @endif

    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Pelaporan Customizable</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Analisis Kinerja Bisnis</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="trending-up"></i> <span>Predictive Analytics</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
