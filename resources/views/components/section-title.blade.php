@props(['title', 'link' => null, 'linkText' => 'Lihat Semua', 'accent' => 'primary'])

@php
    $dotClass = match($accent) {
        'primary' => 'bg-primary',
        'accent' => 'bg-accent',
        'error' => 'bg-error',
        'secondary' => 'bg-secondary',
        'warning' => 'bg-[#f59e0b]',
        default => 'bg-primary',
    };
@endphp

<div class="flex items-center gap-3 mb-5">
    <span class="w-1 h-7 {{ $dotClass }} rounded-full"></span>
    <h2 class="section-title pb-1 flex-1">{{ $title }}</h2>
    @if($link)
        <a href="{{ $link }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">{{ $linkText }}</a>
    @endif
</div>
