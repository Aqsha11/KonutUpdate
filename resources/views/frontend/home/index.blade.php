@extends('frontend.layouts.app')

@section('title', ($site_settings['site_name'] ?? 'KonutUpdate') . ' - Berita Terpercaya Konawe Utara')

@section('meta')
    @php
        $homeDesc = $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya';
        $homeKw = $site_settings['meta_keywords'] ?? 'konut, konawe utara, berita, news, informasi, sulawesi tenggara';
    @endphp
    <meta name="description" content="{{ $homeDesc }}">
    <meta name="keywords" content="{{ $homeKw }}">
    <meta property="og:title" content="{{ $site_settings['site_name'] ?? 'KonutUpdate' }}" />
    <meta property="og:description" content="{{ $homeDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
@endsection

@php
    // $heroMain & $heroSidePosts disiapkan di HomeController
    $latestForList = $latestPosts->take(10);
@endphp

@section('content')

    {{-- H1 utama homepage (visual hidden) — target keyword: konut, berita konawe utara, berita konut hari ini --}}
    <h1 class="sr-only">
        {{ ($site_settings['site_name'] ?? 'KonutUpdate') }} — Berita Terkini Konawe Utara (Konut) Hari Ini
    </h1>

    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
        <div class="min-w-0 md:w-[68%]">

    {{-- ════════════════════════════════════════════
         HERO — berita utama besar + kolom headline kanan (ala CNN)
         ════════════════════════════════════════════ --}}
    @if($heroMain)
    <section class="mb-3 lg:mb-4">
        <div class="cnn-hero">
            {{-- Berita utama --}}
            <article class="cnn-hero-main" data-post-id="{{ $heroMain->id }}">
                <a href="{{ route('posts.show', $heroMain->slug) }}" class="cnn-hero-main-img" aria-label="{{ $heroMain->title }}">
                    @if($heroMain->categories->count() > 0)
                        <span class="cnn-badge">{{ $heroMain->categories->first()->name }}</span>
                    @elseif($heroMain->category)
                        <span class="cnn-badge">{{ $heroMain->category->name }}</span>
                    @endif
                    @include('frontend.partials.breaking-badge', ['post' => $heroMain, 'breakingVariant' => 'on-image'])
                    <img src="{{ postThumbnail($heroMain) }}" alt="{{ $heroMain->title }}" loading="eager" fetchpriority="high">
                    @if($heroMain->isVideo())
                    <div class="ku-video-play"><i data-lucide="play" class="w-6 h-6 text-primary ml-0.5"></i></div>
                    @endif
                </a>
                <h2 class="cnn-hero-main-title"><a href="{{ route('posts.show', $heroMain->slug) }}">{{ $heroMain->title }}</a></h2>
                @if($heroMain->excerpt)
                <p class="cnn-hero-main-desc">{{ \Illuminate\Support\Str::limit(strip_tags($heroMain->excerpt), 140) }}</p>
                @endif
                <div class="cnn-hero-main-meta">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <div class="ku-cat-avatar">
                            @if($heroMain->author && $heroMain->author->avatar)
                            <img src="{{ Storage::url($heroMain->author->avatar) }}" alt="{{ $heroMain->author->name }}">
                            @else
                            <span>{{ strtoupper(substr($heroMain->author_name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <span class="font-semibold truncate">{{ $heroMain->author_name }}</span>
                    </div>
                    <span aria-hidden="true">·</span>
                    <time>{{ $heroMain->published_at ? \Carbon\Carbon::parse($heroMain->published_at)->diffForHumans() : '' }}</time>
                    <span aria-hidden="true">·</span>
                    <span class="inline-flex items-center gap-1"><i data-lucide="eye" class="w-3 h-3"></i>{{ number_format($heroMain->views_count) }}</span>
                </div>
                <div class="news-item-stats">
                    <button type="button" onclick="toggleLike({{ $heroMain->id }})" data-like-btn="{{ $heroMain->id }}" class="stat-btn {{ $heroMain->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                        <i data-lucide="heart" class="w-3 h-3"></i>
                        <span data-like-count="{{ $heroMain->id }}">{{ $heroMain->likesCount() }}</span>
                    </button>
                    <a href="{{ route('posts.show', $heroMain->slug) }}#comments" class="stat-btn">
                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                        <span>{{ $heroMain->commentsCount() }}</span>
                    </a>
                    <button type="button" onclick="sharePost('{{ route('posts.show', $heroMain->slug) }}', '{{ addslashes($heroMain->title) }}')" class="stat-btn">
                        <i data-lucide="share-2" class="w-3 h-3"></i>
                    </button>
                </div>
            </article>

            {{-- Headline kecil di samping --}}
            @if($heroSidePosts->count() > 0)
            <div class="cnn-hero-side">
                @foreach($heroSidePosts as $small)
                <a href="{{ route('posts.show', $small->slug) }}" class="cnn-hero-item" data-post-id="{{ $small->id }}">
                    <div class="cnn-hero-item-body">
                        @include('frontend.partials.breaking-badge', ['post' => $small])
                        @if($small->categories->count() > 0)
                            <span class="cnn-hero-item-cat">{{ $small->categories->first()->name }}</span>
                        @elseif($small->category)
                            <span class="cnn-hero-item-cat">{{ $small->category->name }}</span>
                        @endif
                        <h3 class="cnn-hero-item-title">{{ $small->title }}</h3>
                        <time class="cnn-hero-item-time">{{ $small->published_at ? \Carbon\Carbon::parse($small->published_at)->diffForHumans() : '' }}</time>
                    </div>
                    <div class="cnn-hero-item-thumb">
                        <img src="{{ postThumbnail($small) }}" alt="{{ $small->title }}" loading="lazy">
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         KABAR TERKINI — daftar baris 2 kolom (ala CNN)
         ════════════════════════════════════════════ --}}
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><span class="section-bar-dot bg-primary"></span>Kabar Terkini</h2>
            <a href="{{ route('terkini') }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>

        <div class="news-list cnn-news-cols">
            @foreach($latestForList as $post)
            <article class="news-item" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb" @if($post->isVideo()) data-video-player="{{ videoPlayerData($post) }}" @endif>
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @if($post->isVideo())
                    <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                    @endif
                    <div class="viewed-badge"><i data-lucide="eye" class="w-2.5 h-2.5"></i></div>
                </a>
                <div class="news-item-body">
                    <div class="news-item-meta">
                        @include('frontend.partials.breaking-badge', ['post' => $post])
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
                        <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
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
         VIDEO — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($videoPosts) && $videoPosts->count() > 0)
    <section class="mb-3 lg:mb-4 accent-strip">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="play-circle" class="w-4 h-4 text-accent"></i>Video</h2>
            <a href="{{ route('videos') }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>
        <div class="ku-video-row hide-scrollbar">
            @foreach($videoPosts as $post)
            <article class="ku-video-card" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="ku-video-thumb" data-video-player="{{ videoPlayerData($post) }}">
                    @if($post->thumbnail || $post->video_poster)
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @endif
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
         TERPOPULER — daftar bernomor ala CNN
         ════════════════════════════════════════════ --}}
    @if(isset($trendingPosts) && $trendingPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><i data-lucide="flame" class="w-4 h-4 text-accent"></i>Trending</h2>
            <a href="{{ route('trending') }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
        </div>
        <div class="cnn-pop-list">
            @foreach($trendingPosts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="cnn-pop-item" data-post-id="{{ $post->id }}">
                <span class="cnn-pop-num" aria-hidden="true"></span>
                <span class="cnn-pop-body">
                    <span class="cnn-pop-title">{{ $post->title }}</span>
                    <span class="cnn-pop-meta">
                        <span>{{ number_format($post->views_count) }} dibaca</span>
                        <span aria-hidden="true">·</span>
                        <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                    </span>
                </span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════
         KONTEN PILIHAN — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($featuredPosts) && $featuredPosts->count() > 0)
    <section class="mb-3 lg:mb-4 accent-strip">
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
         OPINI — satu baris scroll horizontal
         ════════════════════════════════════════════ --}}
    @if(isset($opiniPosts) && $opiniPosts->count() > 0)
    <section class="mb-3 lg:mb-4 accent-strip">
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
         REKOMENDASI — post acak semua kategori,
         tampil seperti section Kabar Terkini
         ════════════════════════════════════════════ --}}
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><span class="section-bar-dot bg-accent"></span>Rekomendasi Untuk Anda</h2>
        </div>

        <div class="news-list cnn-news-cols">
            @foreach($randomPosts as $post)
            <article class="news-item" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb" @if($post->isVideo()) data-video-player="{{ videoPlayerData($post) }}" @endif>
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @if($post->isVideo())
                    <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                    @endif
                    <div class="viewed-badge"><i data-lucide="eye" class="w-2.5 h-2.5"></i></div>
                </a>
                <div class="news-item-body">
                    <div class="news-item-meta">
                        @include('frontend.partials.breaking-badge', ['post' => $post])
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
                        <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
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


    {{-- ============================================
         BERITA KECAMATAN - post acak semua kecamatan,
         tampil seperti section Kabar Terkini
         ============================================ --}}
    @if(isset($kecamatanRandomPosts) && $kecamatanRandomPosts->count() > 0)
    <section class="mb-3 lg:mb-4">
        <div class="section-bar">
            <h2 class="section-bar-title"><span class="section-bar-dot bg-secondary"></span>Berita Kecamatan</h2>
        </div>

        <div class="news-list cnn-news-cols">
            @foreach($kecamatanRandomPosts as $post)
            <article class="news-item" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb" @if($post->isVideo()) data-video-player="{{ videoPlayerData($post) }}" @endif>
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @if($post->isVideo())
                    <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                    @endif
                    <div class="viewed-badge"><i data-lucide="eye" class="w-2.5 h-2.5"></i></div>
                </a>
                <div class="news-item-body">
                    <div class="news-item-meta">
                        @include('frontend.partials.breaking-badge', ['post' => $post])
                        @if($post->kecamatan)
                            <a href="{{ route('kecamatan.show', $post->kecamatan->slug) }}" class="news-item-cat">{{ $post->kecamatan->name }}</a>
                        @endif
                        <span class="news-item-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                    </div>
                    <h3 class="news-item-title">
                        <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    <div class="news-item-stats">
                        <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="stat-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                            <i data-lucide="heart" class="w-3 h-3"></i>
                            <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
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
    @endif

    {{-- Lihat semua berita --}}
    <div class="mt-6 mb-2 text-center">
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-outline px-7 py-2.5 text-sm font-bold text-primary transition-colors hover:bg-primary hover:border-primary hover:text-white">
            Lihat Semua Berita <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

        </div>

        {{-- Kolom Kanan: Sidebar + Iklan (tablet, desktop, laptop) --}}
        <div class="hidden md:block md:w-[32%]">
            <div class="md:sticky md:top-20 space-y-4">
                @include('frontend.partials.sidebar', ['adCompact' => true, 'hideHomeWidgets' => true])
            </div>
        </div>

        {{-- Iklan Mobile (paling bawah, ala detail berita) --}}
        @php
            $mobileAds = isset($sidebarAds) ? $sidebarAds : \App\Models\Ad::active()->sorted()->get();
        @endphp
        @if($mobileAds->count() > 0)
        <div class="md:hidden mt-4" x-data="adAutoScroll">
            <div class="ku-feat-row hide-scrollbar ku-ad-row" x-ref="scroller" data-axis="x">
                @foreach($mobileAds as $ad)
                @if($ad->link)
                <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="ku-feat-card">
                @else
                <div class="ku-feat-card ku-feat-card-static">
                @endif
                    <div class="ku-feat-media">
                        <span class="ku-feat-thumb">
                            <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" loading="lazy">
                        </span>
                    </div>
                @if($ad->link)
                </a>
                @else
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

@endsection
