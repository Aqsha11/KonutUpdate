@extends('frontend.layouts.app')

@section('title', 'Semua Berita - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Cari berita terbaru di {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Berita</span>
        </nav>
        <h1 class="page-title">
            @if($query)
                <span class="page-title-icon bg-primary/10 text-primary"><i data-lucide="search" class="w-4 h-4"></i></span>
                Hasil Pencarian
            @else
                <span class="page-title-icon bg-primary/10 text-primary"><i data-lucide="newspaper" class="w-4 h-4"></i></span>
                Semua Berita
            @endif
        </h1>
        @if($query)
            <p class="text-on-surface-variant text-xs mt-1">Hasil untuk: <strong>"{{ e($query) }}"</strong></p>
        @endif
    </div>

    @if(isset($categories) && $categories->count() > 0)
    <div class="flex flex-nowrap lg:flex-wrap gap-1.5 mb-4 overflow-x-auto lg:overflow-visible hide-scrollbar">
        <a href="{{ route('search') }}{{ $query ? '?q='.urlencode($query) : '' }}" class="filter-chip shrink-0 {{ empty($categorySlug) ? 'filter-chip-active' : '' }}">
            <i data-lucide="layers" class="w-3 h-3"></i> Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('search') }}?{{ http_build_query(array_merge(request()->query(), ['category' => $cat->slug])) }}" class="filter-chip shrink-0 {{ $categorySlug === $cat->slug ? 'filter-chip-active' : '' }}">
                {{ $cat->name }}
                @if($cat->posts_count > 0)
                    <span class="filter-chip-count">{{ $cat->posts_count }}</span>
                @endif
            </a>
        @endforeach
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="lg:w-[68%]">
            @if(isset($posts) && $posts->count() > 0)
                <div class="news-list">
                    @foreach($posts as $post)
                    <article class="news-item group">
                        <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/110x80/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                            @if($post->type === 'video')
                            <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                            @endif
                        </a>
                        <div class="news-item-body">
                            <div class="news-item-meta">
                                @if($post->categories->count() > 0)
                                    @foreach($post->categories as $cat)
                                        <a href="{{ route('categories.show', $cat->slug) }}" class="news-item-cat">{{ $cat->name }}</a>
                                        @if(! $loop->last) <span>/</span> @endif
                                    @endforeach
                                @elseif($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="news-item-cat">{{ $post->category->name }}</a>
                                @endif
                                <span class="news-item-time">{{ formatDate($post->published_at) }}</span>
                            </div>
                            <h3 class="news-item-title">
                                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <div class="news-item-stats">
                                <span class="stat-btn"><i data-lucide="user" class="w-3 h-3"></i> {{ $post->author->name ?? 'Redaksi' }}</span>
                                <span class="stat-btn"><i data-lucide="eye" class="w-3 h-3"></i> {{ number_format($post->views_count ?? 0) }}</span>
                                <span class="stat-btn"><i data-lucide="heart" class="w-3 h-3"></i> {{ $post->likes_count ?? 0 }}</span>
                                <span class="stat-btn"><i data-lucide="message-circle" class="w-3 h-3"></i> {{ $post->comments_count ?? 0 }}</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                @if(method_exists($posts, 'links'))
                    <div class="mt-4">{{ $posts->links('vendor.pagination.tailwind') }}</div>
                @endif
            @else
                <div class="empty-placeholder">
                    <i data-lucide="search-x" class="w-8 h-8 mb-2"></i>
                    @if($query)
                        <p>Tidak ditemukan berita dengan kata kunci <strong>"{{ e($query) }}"</strong>.</p>
                    @else
                        <p>Belum ada berita.</p>
                    @endif
                </div>
            @endif
        </div>
        <div class="hidden lg:block lg:w-[32%]">
            <div class="lg:sticky lg:top-20 space-y-4">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>

    <div class="lg:hidden mt-5 space-y-4">
        @include('frontend.partials.sidebar')
    </div>
@endsection
