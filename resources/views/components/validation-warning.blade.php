@props(['message', 'dismissible' => true])

<div {{ $attributes->merge(['class' => 'alert alert-warning' . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>Peringatan:</strong> {{ $message }}
    @if ($dismissible)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
