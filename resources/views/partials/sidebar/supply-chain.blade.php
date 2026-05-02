<ul class="sub-menu">
    @hasPermission('supply-chain.permintaan-barang.view')
        <li class="{{ request()->routeIs('admin.supply-chain.permintaan-barang.*') ? 'active' : '' }}">
            <a href="{{ route('admin.supply-chain.permintaan-barang.index') }}">
                <i data-feather="clipboard"></i> <span>Permintaan Barang</span>
            </a>
        </li>
    @endhasPermission
    
    @if(in_array('Gudang', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('manajemen-gudang.index') ? 'active' : '' }}">
            <a href="{{ route('manajemen-gudang.index') }}">
                <i data-feather="send"></i> <span>Transfer Gudang</span>
            </a>
        </li>
    @endif
    <!-- Sub-menu yang belum ada -->
    <li class="unavailable">
        <a href="#">
            <i data-feather="truck"></i> <span>Manajemen Logistik</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="map"></i> <span>Manajemen Transportasi</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="bar-chart-2"></i> <span>Manajemen Permintaan dan Pasokan</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="map-pin"></i> <span>Pelacakan Rantai Pasok</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
