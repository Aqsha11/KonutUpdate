@props(['post', 'imageSize' => 'sm', 'showExcerpt' => true, 'showMeta' => true])

@php
    $imageClasses = match($imageSize) {
        'sm' => 'w-20 h-20',
        'md' => 'aspect-video',
        'lg' => 'aspect-video',
        default => 'aspect-video',
    };
@endphp

<article {{ $attributes->merge(['class' => 'group bg-surface rounded-xl sm:rounded-2xl overflow-hidden card-hover border border-outline/50']) }}>
    <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
        <div class="{{ $imageClasses }} overflow-hidden img-zoom bg-surface-container-low">
            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/400x250/e9ecef/6b7280?text=KONUT' }}"
                 alt="{{ $post->title }}"
                 class="w-full h-full object-cover"
                 loading="lazy">
        </div>
        <div class="p-2.5 sm:p-3">
            @if($post->category)
                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">{{ $post->category->name }}</span>
            @endif
            <h4 class="text-sm font-bold text-on-surface mt-1 line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                {{ $post->title }}
            </h4>
            @if($showExcerpt && $post->excerpt)
                <p class="text-xs text-on-surface-variant mt-1 line-clamp-2 leading-relaxed">{{ strip_tags($post->excerpt) }}</p>
            @endif
            @if($showMeta)
                <div class="flex items-center gap-2 mt-2 text-xs text-on-surface-variant">
                    <span>{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                </div>
            @endif
        </div>
    </a>
</article>
