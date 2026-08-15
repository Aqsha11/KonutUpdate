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
    $categoryMeta = [
        'kriminal' => ['name' => 'Kriminal', 'icon' => 'shield-alert', 'color' => 'error'],
        'pemerintahan' => ['name' => 'Pemerintahan', 'icon' => 'landmark', 'color' => 'primary'],
        'tambang' => ['name' => 'Tambang', 'icon' => 'pickaxe', 'color' => 'secondary'],
        'ekonomi' => ['name' => 'Ekonomi', 'icon' => 'trending-up', 'color' => 'tertiary'],
        'olahraga' => ['name' => 'Olahraga', 'icon' => 'trophy', 'color' => 'accent'],
    ];

    $categoryData = $categoryPosts;

    $heroSlides = $headlinePosts;
@endphp

@section('content')

    {{-- ════════════════════════════════════════════
         HERO CAROUSEL
         ════════════════════════════════════════════ --}}
    @if($headlinePosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="hero-carousel" x-data="{ active: 0, total: {{ $heroSlides->count() }} }" x-init="setInterval(() => { if(total > 1) active = (active + 1) % total }, 5000)">
            <div class="hero-carousel-track" :style="'transform: translateX(-' + (active * 100) + '%)'">
                @foreach($heroSlides as $slideIndex => $big)
                <div class="hero-carousel-slide">
                    @php
                        $smalls = $heroSlides->skip($slideIndex + 1)->take(2);
                    @endphp
                    <div class="hero-slide-grid">
                        {{-- Big Post --}}
                        <div class="hero-slide-big" data-post-id="{{ $big->id }}">
                            <a href="{{ route('posts.show', $big->slug) }}">
                                <img src="{{ postThumbnail($big) }}" alt="{{ $big->title }}" class="hero-slide-big-img" loading="{{ $slideIndex === 0 ? 'eager' : 'lazy' }}">
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
                                        <span>{{ substr($big->author_name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="hero-slide-author-name">{{ $big->author_name }}</span>
                                </div>
                                <div class="hero-slide-actions">
                                    <button type="button" onclick="event.preventDefault();event.stopPropagation();toggleLike({{ $big->id }})" id="like-btn-hero-{{ $big->id }}" class="hero-slide-btn {{ $big->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                        <i data-lucide="heart" class="w-3 h-3"></i>
                                        <span id="like-count-hero-{{ $big->id }}">{{ $big->likesCount() }}</span>
                                    </button>
                                    <a href="{{ route('posts.show', $big->slug) }}#comments" class="hero-slide-btn" onclick="event.preventDefault();event.stopPropagation()">
                                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                                        <span>{{ $big->commentsCount() }}</span>
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
                                    <img src="{{ postThumbnail($small) }}" alt="{{ $small->title }}" class="hero-slide-small-img" loading="lazy">
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
                                            <span>{{ substr($small->author_name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <span class="hero-slide-author-name-sm">{{ $small->author_name }}</span>
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
         VIDEO — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($videoPosts) && $videoPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="play-circle" class="w-4 h-4 text-accent"></i>Video</h2>
            <a href="{{ route('search', ['type' => 'video']) }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>
        <div class="ku-video-row hide-scrollbar">
            @foreach($videoPosts as $post)
            <article class="ku-video-card" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="ku-video-thumb">
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    <div class="ku-video-play"><i data-lucide="play" class="w-5 h-5 text-primary ml-0.5"></i></div>
                </a>
                <div class="ku-video-body">
                    <div class="ku-video-meta">
                        @if($post->categories->count() > 0)
                            <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="ku-video-cat">{{ $post->categories->first()->name }}</a>
                        @elseif($post->category)
                            <a href="{{ route('categories.show', $post->category->slug) }}" class="ku-video-cat">{{ $post->category->name }}</a>
                        @endif
                        <span class="ku-video-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                    </div>
                    <h3 class="ku-video-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         KONTEN PILIHAN — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($featuredPosts) && $featuredPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="layout-grid" class="w-4 h-4 text-primary"></i>Konten Pilihan</h2>
        </div>
        <div class="ku-feat-row hide-scrollbar">
            @foreach($featuredPosts as $post)
            <article class="ku-feat-card" data-post-id="{{ $post->id }}">
                <div class="ku-feat-media">
                    <a href="{{ route('posts.show', $post->slug) }}" class="ku-feat-thumb">
                        <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    </a>
                    <div class="ku-feat-overlay"></div>
                    <div class="ku-feat-content">
                        <div class="ku-coll-meta">
                            @if($post->categories->count() > 0)
                                <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="ku-coll-cat">{{ $post->categories->first()->name }}</a>
                            @elseif($post->category)
                                <a href="{{ route('categories.show', $post->category->slug) }}" class="ku-coll-cat">{{ $post->category->name }}</a>
                            @endif
                            <span class="ku-feat-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                        </div>
                        <h3 class="ku-feat-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                    </div>
                    <div class="ku-feat-stats">
                        <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
                        </button>
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
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         TRENDING
         ════════════════════════════════════════════ --}}
    @if(isset($trendingPosts) && $trendingPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="flame" class="w-4 h-4 text-accent"></i>Trending</h2>
            <a href="{{ route('trending') }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>
        <div class="ku-trend-grid">
            @foreach($trendingPosts as $index => $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="ku-trend-item group" data-post-id="{{ $post->id }}">
                <span class="ku-trend-num {{ $index < 3 ? 'hot' : '' }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="ku-trend-body">
                    <h3 class="ku-trend-title">{{ $post->title }}</h3>
                    <div class="ku-trend-meta">
                        <span>{{ number_format($post->views_count) }} dibaca</span>
                        <span>·</span>
                        <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                    </div>
                </div>
                <div class="ku-trend-thumb">
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         TERKINI — Mobile: flat list, Desktop: 3-col grid
         ════════════════════════════════════════════ --}}
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><span class="section-bar-dot bg-primary"></span>Terkini</h2>
            <a href="{{ route('terkini') }}" class="section-bar-link">Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>

        {{-- Mobile: flat list --}}
        <div class="news-mobile-list">
            <div class="news-list">
                @foreach($latestPosts->take(10) as $post)
                <article class="news-item" data-post-id="{{ $post->id }}">
                    <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                        <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
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
                            @if($post->type === 'opini')
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-accent/10 text-accent text-[9px] md:text-[10px] font-bold uppercase tracking-wide">Opini</span>
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

        {{-- Tablet & Desktop: editorial card grid --}}
        <div class="news-3col-grid">
            @foreach($latestPosts as $post)
            <article class="news-card" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="news-card-thumb">
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @if($post->isVideo())
                    <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                    @endif
                </a>
                <div class="news-card-body">
                    <div class="news-card-meta">
                        @if($post->categories->count() > 0)
                            <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="news-card-cat">{{ $post->categories->first()->name }}</a>
                        @elseif($post->category)
                            <a href="{{ route('categories.show', $post->category->slug) }}" class="news-card-cat">{{ $post->category->name }}</a>
                        @endif
                        <span class="news-item-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                    </div>
                    <h3 class="news-card-title">
                        <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    @if($post->excerpt)
                    <p class="news-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt), 110) }}</p>
                    @endif
                    <div class="news-card-stats">
                        <button type="button" onclick="toggleLike({{ $post->id }})" id="like-btn-g-{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span id="like-count-g-{{ $post->id }}">{{ $post->likesCount() }}</span>
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
    </section>

    {{-- ════════════════════════════════════════════
         OPINI — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($opiniPosts) && $opiniPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="pencil-line" class="w-4 h-4 text-accent"></i>Opini</h2>
            <a href="{{ route('opini') }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>
        <div class="ku-feat-row hide-scrollbar">
            @foreach($opiniPosts as $post)
            <article class="ku-feat-card" data-post-id="{{ $post->id }}">
                <div class="ku-feat-media">
                    <a href="{{ route('posts.show', $post->slug) }}" class="ku-feat-thumb">
                        <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    </a>
                    <div class="ku-feat-overlay"></div>
                    <div class="ku-feat-content">
                        <div class="ku-coll-meta">
                            <span class="ku-coll-cat text-accent">Opini</span>
                            @if($post->categories->count() > 0)
                                <span class="ku-feat-time">/</span>
                                <a href="{{ route('categories.show', $post->categories->first()->slug) }}" class="ku-coll-cat">{{ $post->categories->first()->name }}</a>
                            @endif
                            <span class="ku-feat-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                        </div>
                        <h3 class="ku-feat-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                    </div>
                    <div class="ku-feat-stats">
                        <span class="stat-btn"><i data-lucide="user" class="w-3 h-3"></i> {{ $post->author_name }}</span>
                        <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
                        </button>
                        <a href="{{ route('posts.show', $post->slug) }}#comments" class="stat-btn">
                            <i data-lucide="message-circle" class="w-3 h-3"></i>
                            <span>{{ $post->commentsCount() }}</span>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         CATEGORY SECTIONS — row kartu ala KendariInfo
         ════════════════════════════════════════════ --}}
    @foreach($categorySlugs as $slug)
        @php
            $catData = $categoryData[$slug] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];
            $meta = $categoryMeta[$slug] ?? ['name' => $categoryNames[$slug] ?? ucfirst($slug), 'icon' => 'folder', 'color' => 'primary'];

            $catPosts = collect()
                ->push($catData['hero'])
                ->merge($catData['trending'] ?? collect())
                ->merge($catData['latest'] ?? collect())
                ->filter()
                ->unique('id')
                ->values()
                ->take(8);
        @endphp

        @if($catPosts->count() > 0)
        <section class="mb-3 lg:mb-4">
            <div class="section-bar">
                <h2 class="section-bar-title"><span class="section-bar-dot bg-{{ $meta['color'] }}"></span>{{ $meta['name'] }}</h2>
                <a href="{{ route('categories.show', $slug) }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
            </div>
            <div class="ku-cat-row hide-scrollbar">
                @foreach($catPosts as $post)
                <article class="ku-cat-post" data-post-id="{{ $post->id }}">
                    <div class="ku-cat-left">
                        <h3 class="ku-cat-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                        <div class="ku-cat-author">
                            <div class="ku-cat-avatar">
                                @if($post->author && $post->author->avatar)
                                <img src="{{ Storage::url($post->author->avatar) }}" alt="{{ $post->author->name }}">
                                @else
                                <span>{{ strtoupper(substr($post->author_name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <span class="ku-cat-author-name">{{ $post->author_name }}</span>
                        </div>
                        <div class="ku-cat-actions">
                            <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="ku-cat-act {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                <i data-lucide="heart" class="w-3 h-3"></i>
                                <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
                            </button>
                            <a href="{{ route('posts.show', $post->slug) }}#comments" class="ku-cat-act">
                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                <span>{{ $post->commentsCount() }}</span>
                            </a>
                            <button type="button" onclick="sharePost('{{ route('posts.show', $post->slug) }}', '{{ addslashes($post->title) }}')" class="ku-cat-act">
                                <i data-lucide="share-2" class="w-3 h-3"></i>
                            </button>
                            <time class="ku-cat-date">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : '' }}</time>
                        </div>
                    </div>
                    <div class="ku-cat-right">
                        <a href="{{ route('posts.show', $post->slug) }}">
                            <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

    {{-- ════════════════════════════════════════════
         KECAMATAN SECTIONS
         ════════════════════════════════════════════ --}}
    @foreach($kecamatanSlugs as $kecamatanSlug)
        @include('frontend.partials.kecamatan-section', [
            'kecamatanData' => $kecamatanPosts[$kecamatanSlug] ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()],
            'kecamatanName' => $kecamatanNames[$kecamatanSlug] ?? ucfirst($kecamatanSlug),
        ])
    @endforeach

@endsection
