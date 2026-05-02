<ul class="sub-menu">
    @if(in_array('Transaksi', Auth::user()->akses ?? []))
            <li class="{{ request()->routeIs('transaksi.baru') ? 'active' : '' }}">
                <a href="{{ route('transaksi.baru') }}">
                    <i data-feather="credit-card"></i> <span>Transaksi Baru</span>
                </a>
            </li>
    @endif
    @if(in_array('Kontra Bon', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('kontra_bon.index') ? 'active' : '' }}">
            <a href="{{ route('kontra_bon.index') }}">
                <i data-feather="dollar-sign"></i> <span>Kontra Bon</span>
            </a>
        </li>
    @endif
    
</ul>
