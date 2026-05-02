{{-- Enhanced Input Component --}}
@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => '',
    'help' => '',
    'placeholder' => '',
    'value' => ''
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input type="{{ $type }}" 
           id="{{ $name }}" 
           name="{{ $name }}"
           value="{{ $value }}"
           placeholder="{{ $placeholder }}"
           @if($required) required @endif
           {{ $attributes->merge([
               'class' => 'w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors' . ($error ? ' border-red-500' : '')
           ]) }}>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
    
    @if($help)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>