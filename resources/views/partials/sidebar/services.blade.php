<ul class="sub-menu">
    @if(in_array('Invoice Service', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('service.invoice.index') ? 'active' : '' }}">
            <a href="{{ route('service.invoice.index') }}">
                <i data-feather="file-text"></i> <span>Invoice Service</span>
            </a>
        </li>
    @endif
    @if(in_array('History Service', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('service.invoice.history') ? 'active' : '' }}">
            <a href="{{ route('service.invoice.history') }}">
                <i data-feather="layers"></i> <span>History Invoice</span>
            </a>
        </li>
    @endif
    @if(in_array('Ongkir Service', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('service.ongkos-kirim.index') ? 'active' : '' }}">
            <a href="{{ route('service.ongkos-kirim.index') }}">
                <i data-feather="truck"></i> <span>Ongkir Service</span>
            </a>
        </li>
    @endif
    @if(in_array('Mesin Customer', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('service.mesin-customer.index') ? 'active' : '' }}">
            <a href="{{ route('service.mesin-customer.index') }}">
                <i data-feather="award"></i> <span>Mesin Customer</span>
            </a>
        </li>
    @endif
</ul>
