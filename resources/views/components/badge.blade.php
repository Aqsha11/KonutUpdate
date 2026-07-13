@props(['variant' => 'primary', 'href' => null])

@php
    $baseClasses = 'inline-flex items-center text-[10px] font-bold uppercase tracking-wider no-underline';
    $colorClasses = match($variant) {
        'primary' => 'text-primary',
        'accent' => 'text-accent',
        'error' => 'text-error',
        'secondary' => 'text-secondary',
        'warning' => 'text-[#f59e0b]',
        default => 'text-primary',
    };
    $classes = "$baseClasses $colorClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </span>
@endif
