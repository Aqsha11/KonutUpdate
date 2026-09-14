<div class="baca-juga-card my-4 border-l-4 border-primary bg-primary-light/70 rounded-r-lg px-4 py-3">
    <p class="text-[11px] font-bold text-primary uppercase tracking-wider mb-2 flex items-center gap-1.5">
        <i data-lucide="link" class="w-3 h-3"></i> Baca juga
    </p>
    <div class="space-y-2">
        @foreach($bacaJugaPosts as $bacaJuga)
            <a href="{{ route('posts.show', $bacaJuga->slug) }}"
               title="{{ $bacaJuga->title }}"
               class="block text-sm font-semibold text-on-surface hover:text-primary no-underline leading-snug">
                {{ $bacaJuga->title }}
            </a>
        @endforeach
    </div>
</div>