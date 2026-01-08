@props([
    'variant' => 'primary', // primary, secondary, danger, success, warning
    'type' => 'submit',
    'size' => 'medium', // small, medium, large
    'disabled' => false,
])

@php
    // Base classes
    $baseClass = 'inline-flex items-center justify-center rounded-lg font-bold capitalize tracking-widest transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm';
    
    // Size variants
    $sizes = [
        'small' => 'px-3 py-1 text-xs',
        'medium' => 'px-4 py-2 text-sm',
        'large' => 'px-6 py-3 text-base',
    ];
    
    // Color variants
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500 border border-transparent',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:ring-gray-500 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus:ring-red-500 border border-transparent',
        'success' => 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800 focus:ring-green-500 border border-transparent',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600 active:bg-amber-600 focus:ring-amber-500 border border-transparent',
    ];

    $classes = $baseClass . ' ' . ($sizes[$size] ?? $sizes['medium']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    
    <span wire:loading.delay {{ $attributes->has('wire:click') || $attributes->has('wire:submit') ? '' : 'style=display:none' }} class="ml-2">
        <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </span>
</button>
