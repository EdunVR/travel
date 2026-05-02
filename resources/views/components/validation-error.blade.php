@props(['field', 'bag' => 'default'])

@if ($errors->getBag($bag)->has($field))
    <div class="invalid-feedback d-block">
        {{ $errors->getBag($bag)->first($field) }}
    </div>
@endif
