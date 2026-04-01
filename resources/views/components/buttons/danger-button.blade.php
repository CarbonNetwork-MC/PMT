@props(['size' => 'md', 'disabled' => false])
@php
    $sizeClasses = match($size) {
        'xs' => 'leading-5 text-xs px-3 py-1.5',
        'sm' => 'leading-5 text-sm px-3 py-2',
        'md' => 'leading-5 text-md px-4 py-2.5',
        'lg' => 'text-base px-5 py-3',
        'xl' => 'text-base px-6 py-3.5',
        default => 'md',
    }
@endphp

<button 
    type="button"
    {{ $attributes->merge([
        'class' => "text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base {$sizeClasses} focus:outline-none " . ($disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer')
    ]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</button>