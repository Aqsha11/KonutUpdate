@extends('frontend.layouts.app')

@section('title', 'Trending - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Berita trending terpopuler di {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
@endsection

@php
    $trendingItems = collect($posts->items());
    $isFirstPage = $posts->currentPage() === 1;
    $headlineItems = $isFirstPage ? $trendingItems->take(3) : collect();
    $listItems = $isFirstPage ? $trendingItems->slice(3) : $trendingItems;
@endphp

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Trending</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-accent/10 text-accent"><i data-lucide="flame" class="w-4 h-4"></i></span>
            Trending
        </h1>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)

                {{-- Hero Section — only on page 1 (top 3) --}}
                @if($headlineItems->count() > 0)
                <section class="mb-3 lg:mb-4">
                    @php
                        $big = $headlineItems->first();
                        $smalls = $headlineItems->slice(1);
                    @endphp
                    <div class="hero-slide-grid">
                        <div class="hero-slide-big">
                            <a href="{{ route('posts.show', $big->slug) }}">
                                <img src="{{ $big->thumbnail ? Storage::url($big->thumbnail) : ($big->video_poster ?? 'https://placehold.co/800x500/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $big->title }}" class="hero-slide-big-img" loading="eager">
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
                                    <button type="button" onclick="event.preventDefault();event.stopPropagation();toggleLike({{ $big->id }})" id="like-btn-tr-hero-{{ $big->id }}" class="hero-slide-btn {{ $big->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                        <i data-lucide="heart" class="w-3 h-3"></i>
                                        <span id="like-count-tr-hero-{{ $big->id }}">{{ $big->likesCount() }}</span>
                                    </button>
                                    <a href="{{ route('posts.show', $big->slug) }}#comments" class="hero-slide-btn" onclick="event.preventDefault();event.stopPropagation()">
                                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                                    </a>
                                    <button type="button" onclick="event.preventDefault();event.stopPropagation();sharePost('{{ route('posts.show', $big->slug) }}', '{{ addslashes($big->title) }}')" class="hero-slide-btn">
                                        <i data-lucide="share-2" class="w-3 h-3"></i>
                                    </button>
                                    <span class="hero-slide-btn"><i data-lucide="eye" class="w-3 h-3"></i> <span>{{ number_format($big->views_count) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="hero-slide-smalls">
                            @foreach($smalls as $small)
                            <div class="hero-slide-small">
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
                                        <button type="button" onclick="event.preventDefault();event.stopPropagation();toggleLike({{ $small->id }})" id="like-btn-tr-sm-{{ $small->id }}" class="hero-slide-btn-sm {{ $small->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                                            <i data-lucide="heart" class="w-2.5 h-2.5"></i>
                                            <span id="like-count-tr-sm-{{ $small->id }}">{{ $small->likesCount() }}</span>
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
                </section>
                @endif

                {{-- Numbered List (4+ on page 1, or all on page 2+) --}}
                @if($listItems->count() > 0)
                <div class="trending-numbered-list">
                    @foreach($listItems as $index => $post)
                    @php
                        $globalNum = $isFirstPage
                            ? $loop->iteration + 3
                            : ($posts->currentPage() - 1) * $posts->perPage() + $loop->iteration;
                    @endphp
                    <a href="{{ route('posts.show', $post->slug) }}" class="trending-numbered-item group">
                        <div class="trending-num-col {{ $loop->index < 3 ? 'hot' : '' }}">
                            <span class="trending-big-num">{{ str_pad($globalNum, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="trending-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/110x80/e9ecef/6b7280?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                            @if($post->isVideo())
                            <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                            @endif
                        </div>
                        <div class="trending-item-body">
                            <h3 class="news-item-title">{{ $post->title }}</h3>
                            <div class="news-item-meta">
                                @if($post->categories->count() > 0)
                                    <span class="news-item-cat">{{ $post->categories->first()->name }}</span>
                                @elseif($post->category)
                                    <span class="news-item-cat">{{ $post->category->name }}</span>
                                @endif
                            </div>
                            <div class="news-item-stats">
                                <span class="stat-btn"><i data-lucide="eye" class="w-3 h-3"></i> <span>{{ number_format($post->views_count) }}</span></span>
                                <span class="stat-btn"><i data-lucide="heart" class="w-3 h-3"></i> <span>{{ $post->likes_count ?? 0 }}</span></span>
                                <span class="stat-btn"><i data-lucide="message-circle" class="w-3 h-3"></i> <span>{{ $post->comments_count ?? 0 }}</span></span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                <div class="mt-4">{{ $posts->links('vendor.pagination.tailwind') }}</div>
            @else
                <div class="empty-placeholder">
                    <i data-lucide="flame" class="w-8 h-8 mb-2"></i>
                    <p>Belum ada berita trending.</p>
                </div>
            @endif
        </div>
        <div class="hidden lg:block lg:w-[32%]">
            <div class="lg:sticky lg:top-20 space-y-4">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
