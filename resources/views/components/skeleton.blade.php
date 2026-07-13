@props(['type' => 'card', 'count' => 1])

@php
    $template = match($type) {
        'card' => function() {
            return '<div class="bg-surface rounded-2xl border border-outline/50 overflow-hidden"><div class="aspect-video bg-surface-container-low animate-pulse"></div><div class="p-3 space-y-2"><div class="h-3 w-16 bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-full bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-3/4 bg-surface-container-low rounded animate-pulse"></div><div class="h-3 w-24 bg-surface-container-low rounded animate-pulse"></div></div></div>';
        },
        'list' => function() {
            return '<div class="flex gap-4 bg-surface rounded-2xl p-3 border border-outline/50"><div class="w-[200px] shrink-0 aspect-video bg-surface-container-low rounded-lg animate-pulse"></div><div class="flex-1 space-y-2"><div class="h-3 w-32 bg-surface-container-low rounded animate-pulse"></div><div class="h-5 w-full bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-3/4 bg-surface-container-low rounded animate-pulse"></div><div class="h-3 w-40 bg-surface-container-low rounded animate-pulse"></div></div></div>';
        },
        'text' => function() {
            return '<div class="space-y-2"><div class="h-4 w-full bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-5/6 bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-2/3 bg-surface-container-low rounded animate-pulse"></div></div>';
        },
        default => function() {
            return '<div class="bg-surface rounded-2xl border border-outline/50 overflow-hidden"><div class="aspect-video bg-surface-container-low animate-pulse"></div><div class="p-3 space-y-2"><div class="h-3 w-16 bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-full bg-surface-container-low rounded animate-pulse"></div><div class="h-4 w-3/4 bg-surface-container-low rounded animate-pulse"></div><div class="h-3 w-24 bg-surface-container-low rounded animate-pulse"></div></div></div>';
        },
    };
@endphp

@for($i = 0; $i < $count; $i++)
    {!! $template() !!}
@endfor
