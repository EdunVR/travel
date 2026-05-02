@props(['errors' => null, 'title' => 'Validasi Gagal'])

@php
    $errorBag = $errors ?? $errors ?? session('errors');
@endphp

@if ($errorBag && $errorBag->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger alert-dismissible fade show']) }} role="alert">
        <h5 class="alert-heading">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $title }}
        </h5>
        <p class="mb-2">Silakan perbaiki kesalahan berikut:</p>
        <ul class="mb-0">
            @foreach ($errorBag->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
