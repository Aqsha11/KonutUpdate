@php
    $secData = $kecamatanData ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
    $secHero = $secData['trending']->first() ?? $secData['hero'];
    $secTrending = $secData['trending']->slice(1)->values()->merge($secData['latest']);

    if ($secData['hero'] && $secHero && $secHero->id !== $secData['hero']->id) {
        $secTrending = $secTrending->push($secData['hero']);
    }
@endphp

@if($secData['hero'] || $secData['trending']->count() > 0 || $secData['latest']->count() > 0)
<section class="mb-3 lg:mb-4">
    <div class="cat-header">
        <div class="cat-header-title">
            <span class="cat-header-dot bg-accent"></span>
            <h2>Kec. {{ $kecamatanName }}</h2>
        </div>
        <a href="{{ route('kecamatan.show', $kecamatanSlug) }}" class="cat-header-link">Lihat Semua <i data-lucide="chevron-right" class="w-3 h-3"></i></a>
    </div>

    <div class="cat-3col-layout">
        {{-- Kolom 1: Portrait Hero (trending #1) --}}
        <div class="cat-3col-hero">
            @if($secHero)
            <div class="cat-portrait-card" data-post-id="{{ $secHero->id }}">
                <a href="{{ route('posts.show', $secHero->slug) }}">
                    <img src="{{ $secHero->thumbnail ? Storage::url($secHero->thumbnail) : ($secHero->video_poster ?? 'https://placehold.co/400x520/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $secHero->title }}" class="cat-portrait-img" loading="lazy">
                </a>
                <div class="cat-portrait-overlay"></div>
                <div class="cat-portrait-content">
                    <span class="cat-portrait-badge">
                        <i data-lucide="map-pin" class="w-2.5 h-2.5"></i>
                        <span>{{ $kecamatanName }}</span>
                    </span>
                    <h3 class="cat-portrait-title"><a href="{{ route('posts.show', $secHero->slug) }}">{{ $secHero->title }}</a></h3>
                    <div class="cat-portrait-meta">
                        <span class="cat-portrait-author">{{ $secHero->author->name ?? 'Redaksi' }}</span>
                        <span>·</span>
                        <span>{{ $secHero->published_at ? \Carbon\Carbon::parse($secHero->published_at)->diffForHumans() : '' }}</span>
                    </div>
                    <div class="cat-portrait-stats">
                        <button type="button" onclick="event.preventDefault();toggleLike({{ $secHero->id }})" data-like-btn="{{ $secHero->id }}" id="like-btn-{{ $secHero->id }}" class="cat-stat-btn {{ $secHero->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-2.5 h-2.5"></i>
                            <span data-like-count="{{ $secHero->id }}" id="like-count-{{ $secHero->id }}">{{ $secHero->likesCount() }}</span>
                        </button>
                        <a href="{{ route('posts.show', $secHero->slug) }}#comments" class="cat-stat-btn" onclick="event.stopPropagation()">
                            <i data-lucide="message-circle" class="w-2.5 h-2.5"></i>
                            <span>{{ $secHero->commentsCount() }}</span>
                        </a>
                        <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $secHero->slug) }}', '{{ addslashes($secHero->title) }}')" class="cat-stat-btn">
                            <i data-lucide="share-2" class="w-2.5 h-2.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="cat-portrait-empty">
                <i data-lucide="map-pin" class="w-6 h-6 text-on-surface-variant/30"></i>
            </div>
            @endif
        </div>

        {{-- Kolom 2: Trending (mulai #2) --}}
        <div class="cat-3col-list">
            <div class="cat-3col-label">
                <i data-lucide="flame" class="w-2.5 h-2.5 text-accent"></i>
                Trending
            </div>

            <div class="cat-3col-scroll">
            @forelse($secTrending as $index => $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="cat-3col-item group" data-post-id="{{ $post->id }}">
                <span class="cat-3col-num {{ $index < 2 ? 'hot' : '' }}">{{ str_pad($index + 2, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="cat-3col-item-thumb">
                    <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/160x100/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" loading="lazy">
                </div>
                <div class="cat-3col-item-body">
                    <h4 class="cat-3col-title">{{ $post->title }}</h4>
                    <div class="cat-3col-meta">
                        @if($post->categories->count() > 0)
                        <span class="cat-3col-cat">{{ $post->categories->first()->name }}</span>
                        <span>·</span>
                        @elseif($post->category)
                        <span class="cat-3col-cat">{{ $post->category->name }}</span>
                        <span>·</span>
                        @endif
                        <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                        <span>·</span>
                        <span>{{ number_format($post->views_count) }} dibaca</span>
                    </div>
                    <div class="cat-3col-stats">
                        <button type="button" onclick="event.preventDefault();toggleLike({{ $post->id }})" id="like-btn-t-{{ $post->id }}" class="cat-3col-stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-2 h-2"></i>
                            <span id="like-count-t-{{ $post->id }}">{{ $post->likesCount() }}</span>
                        </button>
                        <a href="{{ route('posts.show', $post->slug) }}#comments" class="cat-3col-stat-btn" onclick="event.preventDefault();event.stopPropagation()">
                            <i data-lucide="message-circle" class="w-2 h-2"></i>
                            <span>{{ $post->commentsCount() }}</span>
                        </a>
                        <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $post->slug) }}', '{{ addslashes($post->title) }}')" class="cat-3col-stat-btn">
                            <i data-lucide="share-2" class="w-2 h-2"></i>
                        </button>
                    </div>
                </div>
            </a>
            @empty
            @if(!$secHero)
            <p class="cat-3col-empty">Belum ada berita</p>
            @endif
            @endforelse
            </div>
        </div>
    </div>
</section>
@endif
