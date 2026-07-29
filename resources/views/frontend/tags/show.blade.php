@extends('frontend.layouts.app')

@section('title', $tag->name . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Kumpulan berita dengan tag {{ $tag->name }} terbaru dari Konut.Update">
    <link rel="canonical" href="{{ route('tags.show', $tag->slug) }}" />
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>{{ $tag->name }}</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-primary/10 text-primary"><i data-lucide="tag" class="w-4 h-4"></i></span>
            {{ $tag->name }}
        </h1>
        <p class="text-on-surface-variant text-xs mt-1">Kumpulan berita dengan tag {{ $tag->name }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="news-list">
                    @foreach($posts as $post)
                    <article class="news-item group">
                        <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/110x80/e9ecef/6b7280?text=N' }}" alt="{{ $post->title }}" loading="lazy">
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
                    <i data-lucide="info" class="w-8 h-8 mb-2"></i>
                    <p>Belum ada artikel dengan tag ini.</p>
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
