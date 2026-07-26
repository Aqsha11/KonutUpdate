@extends('frontend.layouts.app')

@section('title', 'Trending - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Berita trending terpopuler di {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
@endsection

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
                <div class="trending-numbered-list">
                    @foreach($posts as $index => $post)
                    <a href="{{ route('posts.show', $post->slug) }}" class="trending-numbered-item group">
                        <div class="trending-num-col {{ $index < 3 ? 'hot' : '' }}">
                            <span class="trending-big-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
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
