@extends('app')

@section('title')
    Setting Chart of Accounts
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Setting COA</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Menu Setting</h3>
            </div>
            <div class="box-body">
                <div class="list-group">
                    <a href="{{ route('settings.coa.po-penjualan') }}" class="list-group-item {{ request()->is('settings/coa/po-penjualan') ? 'active' : '' }}">
                        <i class="fa fa-shopping-cart"></i> PO Penjualan
                    </a>
                    <a href="{{ route('settings.coa.pembelian') }}" class="list-group-item {{ request()->is('settings/coa/pembelian') ? 'active' : '' }}">
                        <i class="fa fa-truck"></i> Pembelian
                    </a>
                    <a href="{{ route('settings.coa.produksi') }}" class="list-group-item {{ request()->is('settings/coa/produksi') ? 'active' : '' }}">
                        <i class="fa fa-industry"></i> Produksi
                    </a>
                    <a href="{{ route('settings.coa.retur') }}" class="list-group-item {{ request()->is('settings/coa/retur') ? 'active' : '' }}">
                        <i class="fa fa-exchange"></i> Retur
                    </a>
                </div>
            </div>
        </div>
        
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Info COA</h3>
            </div>
            <div class="box-body">
                <div class="small">
                    @foreach($accountTypesLabels as $type => $label)
                        <div class="mb-2">
                            <strong>{{ $label }}:</strong> 
                            <span class="badge bg-{{ $type == 'asset' ? 'primary' : ($type == 'liability' ? 'warning' : ($type == 'equity' ? 'success' : ($type == 'revenue' ? 'info' : 'danger'))) }}">
                                {{ $accountCounts[$type] ?? 0 }} akun
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9">
        @yield('coa-content')
    </div>
</div>
@endsection
