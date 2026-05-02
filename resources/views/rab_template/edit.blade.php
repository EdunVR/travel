@extends('app')

@section('title') Edit RAB Template @endsection

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item"><a href="{{ route('rab_template.index') }}">RAB Template</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Edit RAB Template</h3>
            </div>
            <div class="box-body">
                @include('rab_template._form', [
                    'action' => route('rab_template.update', $template->id_rab),
                    'method' => 'PUT',
                    'template' => $template,
                    'products' => $products
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {

    // Fungsi untuk menghitung total
    $(document).on('keyup', '.rab-item-cost', function() {
        calculateTotal();
    });

    // Inisialisasi Feather Icons
    feather.replace();
});
</script>
@endpush
