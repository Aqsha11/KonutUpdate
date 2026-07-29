@extends('frontend.layouts.app')

@section('title', ($site_settings['site_name'] ?? 'Konut.Update') . ' - Berita Terpercaya Konawe Utara')

@section('meta')
    @php
        $homeDesc = $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya';
        $homeKw = $site_settings['meta_keywords'] ?? 'konut, konawe utara, berita, news, informasi, sulawesi tenggara';
    @endphp
    <meta name="description" content="{{ $homeDesc }}">
    <meta name="keywords" content="{{ $homeKw }}">
    <link rel="canonical" href="{{ url('/') }}" />
    <meta property="og:title" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $homeDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "NewsMediaOrganization",
        "name": @json($site_settings['site_name'] ?? 'Konut.Update'),
        "url": "{{ url('/') }}",
        "description": "Portal berita terkini Konawe Utara"
    }
    </script>
@endsection

@php
    $kriminalPosts = $categoryPosts['kriminal'] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
    $pemerintahanPosts = $categoryPosts['pemerintahan'] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
    $tambangPosts = $categoryPosts['tambang'] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
    $ekonomiPosts = $categoryPosts['ekonomi'] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
    $olahragaPosts = $categoryPosts['olahraga'] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];

    $categoryMeta = [
        'kriminal' => ['name' => 'Kriminal', 'icon' => 'shield-alert', 'color' => 'error'],
        'pemerintahan' => ['name' => 'Pemerintahan', 'icon' => 'landmark', 'color' => 'primary'],
        'tambang' => ['name' => 'Tambang', 'icon' => 'pickaxe', 'color' => 'secondary'],
        'ekonomi' => ['name' => 'Ekonomi', 'icon' => 'trending-up', 'color' => 'tertiary'],
        'olahraga' => ['name' => 'Olahraga', 'icon' => 'trophy', 'color' => 'accent'],
    ];

    $categoryData = [
        'kriminal' => $kriminalPosts,
        'pemerintahan' => $pemerintahanPosts,
        'tambang' => $tambangPosts,
        'ekonomi' => $ekonomiPosts,
        'olahraga' => $olahragaPosts,
    ];

    $latestChunks = $latestPosts->chunk(6);
    $heroSlides = $headlinePosts->chunk(3);
@endphp

@section('content')

    {{-- ════════════════════════════════════════════
         HERO CAROUSEL
         ════════════════════════════════════════════ --}}
    @if($headlinePosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="hero-carousel" x-data="{ active: 0, total: {{ $heroSlides->count() }} }" x-init="setInterval(() => { if(total > 1) active = (active + 1) % total }, 5000)">
            <div class="hero-carousel-track" :style="'transform: translateX(-' + (active * 100) + '%)'">
                @foreach($heroSlides as $slideIndex => $slide)
                <div class="hero-carousel-slide">
                    @php
                        $big = $slide->first();
                        $smalls = $slide->slice(1);
                    @endphp
                    <div class="hero-slide-grid">
                        {{-- Big Post --}}
                        <div class="hero-slide-big" data-post-id="{{ $big->id }}">
                            <a href="{{ route('posts.show', $big->slug) }}">
                                <img src="{{ $big->thumbnail ? Storage::url($big->thumbnail) : ($big->video_poster ?? 'https://placehold.co/800x500/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $big->title }}" class="hero-slide-big-img" loading="{{ $slideIndex === 0 ? 'eager' : 'lazy' }}">
                            </a>
                            <div class="hero-slide-big-overlay"></div>
                            @if($big->isVideo())
                            <div class="hero-play"><i data-lucide="play" class="w-6 h-6 text-primary ml-0.5"></i></div>
                            @endif
                            <div class="hero-slide-big-content">
                                @if($big->categories->count() > 0)
                                <a href="{{ route('categories.show', $big->categories->first()->slug) }}" class="hero-slide-badge">{{ $big->categories->first()->name }}</a>
                                @elseif($big->category)
                                <a href="{{ route('categories.show', $big->category->slug) }}" class="hero-slide-badge">{{ $big->category->name }}</a>
                                @endif
                                <h2 class="hero-slide-big-title"><a href="{{ route('posts.show', $big->slug) }}">{{ $big->title }}</a></h2>
                                <div class="hero-slide-author">
                                    <div class="hero-slide-avatar">
                                        @if($big->author && $big->author->avatar)
                                        <img src="{{ Storage::url($big->author->avatar) }}" alt="{{ $big->author->name }}">
                                        @else
                                        <span>{{ substr($big->author->name ?? 'R', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="hero-slide-author-name">{{ $big->author->name ?? 'Redaksi' }}</span>
                                </div>
                                <div class="hero-slide-actions">
                                    <button type="button" onclick="event.preventDefault();event.stopPropagation();toggleLike({{ $big->id }})" id="like-btn-hero-{{ $big->id }}" class="hero-slide-btn {{ $big->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                        <i data-lucide="heart" class="w-3 h-3"></i>
                                        <span id="like-count-hero-{{ $big->id }}">{{ $big->likesCount() }}</span>
                                    </button>
                                    <a href="{{ route('posts.show', $big->slug) }}#comments" class="hero-slide-btn" onclick="event.preventDefault();event.stopPropagation()">
                                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                                    </a>
                                    <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $big->slug) }}', '{{ addslashes($big->title) }}')" class="hero-slide-btn">
                                        <i data-lucide="share-2" class="w-3 h-3"></i>
                                    </button>
                                    <span class="hero-slide-time">{{ $big->published_at ? \Carbon\Carbon::parse($big->published_at)->diffForHumans() : '' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Small Posts --}}
                        <div class="hero-slide-smalls">
                            @foreach($smalls as $small)
                            <div class="hero-slide-small" data-post-id="{{ $small->id }}">
                                <a href="{{ route('posts.show', $small->slug) }}">
                                    <img src="{{ $small->thumbnail ? Storage::url($small->thumbnail) : ($small->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $small->title }}" class="hero-slide-small-img" loading="lazy">
                                </a>
                                <div class="hero-slide-small-overlay"></div>
                                <div class="hero-slide-small-content">
                                    @if($small->categories->count() > 0)
                                    <a href="{{ route('categories.show', $small->categories->first()->slug) }}" class="hero-slide-badge-sm">{{ $small->categories->first()->name }}</a>
                                    @elseif($small->category)
                                    <a href="{{ route('categories.show', $small->category->slug) }}" class="hero-slide-badge-sm">{{ $small->category->name }}</a>
                                    @endif
                                    <h3 class="hero-slide-small-title"><a href="{{ route('posts.show', $small->slug) }}">{{ $small->title }}</a></h3>
                                    <div class="hero-slide-author-sm">
                                        <div class="hero-slide-avatar-sm">
                                            @if($small->author && $small->author->avatar)
                                            <img src="{{ Storage::url($small->author->avatar) }}" alt="{{ $small->author->name }}">
                                            @else
                                            <span>{{ substr($small->author->name ?? 'R', 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <span class="hero-slide-author-name-sm">{{ $small->author->name ?? 'Redaksi' }}</span>
                                    </div>
                                    <div class="hero-slide-actions-sm">
                                        <button type="button" onclick="event.preventDefault();event.stopPropagation();toggleLike({{ $small->id }})" id="like-btn-hero-sm-{{ $small->id }}" class="hero-slide-btn-sm {{ $small->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                            <i data-lucide="heart" class="w-2.5 h-2.5"></i>
                                            <span id="like-count-hero-sm-{{ $small->id }}">{{ $small->likesCount() }}</span>
                                        </button>
                                        <a href="{{ route('posts.show', $small->slug) }}#comments" class="hero-slide-btn-sm" onclick="event.preventDefault();event.stopPropagation()">
                                            <i data-lucide="message-circle" class="w-2.5 h-2.5"></i>
                                        </a>
                                        <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $small->slug) }}', '{{ addslashes($small->title) }}')" class="hero-slide-btn-sm">
                                            <i data-lucide="share-2" class="w-2.5 h-2.5"></i>
                                        </button>
                                        <span class="hero-slide-time-sm">{{ $small->published_at ? \Carbon\Carbon::parse($small->published_at)->diffForHumans() : '' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($smalls->count() < 2)
                            @for($i = $smalls->count(); $i < 2; $i++)
                            <div class="hero-slide-small hero-slide-placeholder">
                                <div class="hero-slide-placeholder-inner">
                                    <i data-lucide="newspaper" class="w-5 h-5 opacity-20"></i>
                                </div>
                            </div>
                            @endfor
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($heroSlides->count() > 1)
            <div class="hero-carousel-indicators">
                @foreach($heroSlides as $i => $_)
                <button type="button" class="hero-carousel-dot" :class="active === {{ $i }} ? 'active' : ''" @click="active = {{ $i }}"></button>
                @endforeach
            </div>
            <button type="button" class="hero-carousel-prev" @click="active = active > 0 ? active - 1 : total - 1">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <button type="button" class="hero-carousel-next" @click="active = active < total - 1 ? active + 1 : 0">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
            @endif
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         BERITA TERBARU — Mobile: flat list, Desktop: 3-col grid
         ════════════════════════════════════════════ --}}
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><span class="section-bar-dot bg-primary"></span>Berita Terbaru</h2>
            <a href="{{ route('search') }}" class="section-bar-link">Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>

        {{-- Mobile: flat list --}}
        <div class="news-mobile-list">
            <div class="news-list">
                @foreach($latestPosts->take(10) as $post)
                <article class="news-item" data-post-id="{{ $post->id }}">
                    <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/110x80/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                        @if($post->isVideo())
                        <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                        @endif
                        <div class="viewed-badge"><i data-lucide="eye" class="w-2.5 h-2.5"></i></div>
                    </a>
                    <div class="news-item-body">
                        <div class="news-item-meta">
                            @if($post->categories->count() > 0)
                                <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="news-item-cat">{{ $post->categories->first()->name }}</a>
                            @elseif($post->category)
                                <a href="{{ route('categories.show', $post->category->slug) }}" class="news-item-cat">{{ $post->category->name }}</a>
                            @endif
                            <span class="news-item-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                        </div>
                        <h3 class="news-item-title">
                            <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <div class="news-item-stats">
                            <button type="button" onclick="toggleLike({{ $post->id }})" id="like-btn-m-{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                <i data-lucide="heart" class="w-3 h-3"></i>
                                <span id="like-count-m-{{ $post->id }}">{{ $post->likesCount() }}</span>
                            </button>
                            <span class="stat-btn">
                                <i data-lucide="eye" class="w-3 h-3"></i>
                                <span>{{ number_format($post->views_count) }}</span>
                            </span>
                            <a href="{{ route('posts.show', $post->slug) }}#comments" class="stat-btn">
                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                <span>{{ $post->commentsCount() }}</span>
                            </a>
                            <button type="button" onclick="sharePost('{{ route('posts.show', $post->slug) }}', '{{ addslashes($post->title) }}')" class="stat-btn">
                                <i data-lucide="share-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>

        {{-- Desktop: 3-column grid --}}
        <div class="news-3col-grid">
            @foreach($latestChunks as $chunkIndex => $chunk)
            <div class="news-3col-col">
                @foreach($chunk as $post)
                <article class="news-item" data-post-id="{{ $post->id }}">
                    <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/110x80/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                        @if($post->isVideo())
                        <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                        @endif
                        <div class="viewed-badge"><i data-lucide="eye" class="w-2.5 h-2.5"></i></div>
                    </a>
                    <div class="news-item-body">
                        <div class="news-item-meta">
                            @if($post->categories->count() > 0)
                                <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="news-item-cat">{{ $post->categories->first()->name }}</a>
                            @elseif($post->category)
                                <a href="{{ route('categories.show', $post->category->slug) }}" class="news-item-cat">{{ $post->category->name }}</a>
                            @endif
                            <span class="news-item-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                        </div>
                        <h3 class="news-item-title">
                            <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <div class="news-item-stats">
                            <button type="button" onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                <i data-lucide="heart" class="w-3 h-3"></i>
                                <span id="like-count-{{ $post->id }}">{{ $post->likesCount() }}</span>
                            </button>
                            <span class="stat-btn">
                                <i data-lucide="eye" class="w-3 h-3"></i>
                                <span>{{ number_format($post->views_count) }}</span>
                            </span>
                            <a href="{{ route('posts.show', $post->slug) }}#comments" class="stat-btn">
                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                <span>{{ $post->commentsCount() }}</span>
                            </a>
                            <button type="button" onclick="sharePost('{{ route('posts.show', $post->slug) }}', '{{ addslashes($post->title) }}')" class="stat-btn">
                                <i data-lucide="share-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @endforeach
        </div>
    </section>

    {{-- ════════════════════════════════════════════
         CATEGORY SECTIONS
         ════════════════════════════════════════════ --}}
    @foreach($categorySlugs as $slug)
        @php
            $catData = $categoryData[$slug] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
            $meta = $categoryMeta[$slug] ?? ['name' => ucfirst($slug), 'icon' => 'folder', 'color' => 'primary'];
        @endphp

        @if($catData['hero'] || $catData['trending']->count() > 0 || $catData['latest']->count() > 0)
        <section class="mb-3 lg:mb-4">
            <div class="cat-header">
                <div class="cat-header-title">
                    <span class="cat-header-dot bg-{{ $meta['color'] }}"></span>
                    <h2>{{ $meta['name'] }}</h2>
                </div>
                <a href="{{ route('categories.show', $slug) }}" class="cat-header-link">Lihat Semua <i data-lucide="chevron-right" class="w-3 h-3"></i></a>
            </div>

            <div class="cat-3col-layout">
                {{-- Kolom 1: Portrait Hero --}}
                <div class="cat-3col-hero">
                    @if($catData['hero'])
                    <div class="cat-portrait-card" data-post-id="{{ $catData['hero']->id }}">
                        <a href="{{ route('posts.show', $catData['hero']->slug) }}">
                            <img src="{{ $catData['hero']->thumbnail ? Storage::url($catData['hero']->thumbnail) : ($catData['hero']->video_poster ?? 'https://placehold.co/400x520/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $catData['hero']->title }}" class="cat-portrait-img" loading="lazy">
                        </a>
                        <div class="cat-portrait-overlay"></div>
                        <div class="cat-portrait-content">
                            <span class="cat-portrait-badge">{{ $meta['name'] }}</span>
                            <h3 class="cat-portrait-title"><a href="{{ route('posts.show', $catData['hero']->slug) }}">{{ $catData['hero']->title }}</a></h3>
                            <div class="cat-portrait-meta">
                                <span class="cat-portrait-author">{{ $catData['hero']->author->name ?? 'Redaksi' }}</span>
                                <span>·</span>
                                <span>{{ $catData['hero']->published_at ? \Carbon\Carbon::parse($catData['hero']->published_at)->diffForHumans() : '' }}</span>
                            </div>
                            <div class="cat-portrait-stats">
                                <button type="button" onclick="event.preventDefault();toggleLike({{ $catData['hero']->id }})" id="like-btn-{{ $catData['hero']->id }}" class="cat-stat-btn {{ $catData['hero']->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                    <i data-lucide="heart" class="w-2.5 h-2.5"></i>
                                    <span id="like-count-{{ $catData['hero']->id }}">{{ $catData['hero']->likesCount() }}</span>
                                </button>
                                <a href="{{ route('posts.show', $catData['hero']->slug) }}#comments" class="cat-stat-btn" onclick="event.stopPropagation()">
                                    <i data-lucide="message-circle" class="w-2.5 h-2.5"></i>
                                    <span>{{ $catData['hero']->commentsCount() }}</span>
                                </a>
                                <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $catData['hero']->slug) }}', '{{ addslashes($catData['hero']->title) }}')" class="cat-stat-btn">
                                    <i data-lucide="share-2" class="w-2.5 h-2.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="cat-portrait-empty">
                        <i data-lucide="{{ $meta['icon'] }}" class="w-6 h-6 text-on-surface-variant/30"></i>
                    </div>
                    @endif
                </div>

                {{-- Kolom 2: Trending --}}
                <div class="cat-3col-list">
                    <div class="cat-3col-label">
                        <i data-lucide="flame" class="w-2.5 h-2.5 text-accent"></i>
                        Trending
                    </div>
                    @forelse($catData['trending'] as $index => $post)
                    <a href="{{ route('posts.show', $post->slug) }}" class="cat-3col-item group" data-post-id="{{ $post->id }}">
                        <span class="cat-3col-num {{ $index < 3 ? 'hot' : '' }}">{{ $index + 1 }}</span>
                        <div class="cat-3col-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/80x60/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                        </div>
                        <div class="cat-3col-item-body">
                            <h4 class="cat-3col-title">{{ $post->title }}</h4>
                            <div class="cat-3col-meta">
                                <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                                <span>·</span>
                                <span>{{ number_format($post->views_count) }} views</span>
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
                    <p class="cat-3col-empty">Belum ada berita</p>
                    @endforelse
                </div>

                {{-- Kolom 3: Terbaru --}}
                <div class="cat-3col-list">
                    <div class="cat-3col-label">
                        <i data-lucide="clock" class="w-2.5 h-2.5 text-primary"></i>
                        Terbaru
                    </div>
                    @forelse($catData['latest'] as $post)
                    <a href="{{ route('posts.show', $post->slug) }}" class="cat-3col-item group" data-post-id="{{ $post->id }}">
                        <div class="cat-3col-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/80x60/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                        </div>
                        <div class="cat-3col-item-body">
                            <h4 class="cat-3col-title">{{ $post->title }}</h4>
                            <div class="cat-3col-meta">
                                <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                            </div>
                            <div class="cat-3col-stats">
                                <button type="button" onclick="event.preventDefault();toggleLike({{ $post->id }})" id="like-btn-l-{{ $post->id }}" class="cat-3col-stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                    <i data-lucide="heart" class="w-2 h-2"></i>
                                    <span id="like-count-l-{{ $post->id }}">{{ $post->likesCount() }}</span>
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
                    <p class="cat-3col-empty">Belum ada berita</p>
                    @endforelse
                </div>
            </div>
        </section>
        @endif
    @endforeach

@endsection
