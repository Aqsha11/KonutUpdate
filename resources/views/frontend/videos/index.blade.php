@extends('frontend.layouts.app')

@section('title', 'Video - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Kumpulan video berita dan momen terkini dari {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="Video - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Kumpulan video berita dan momen terkini dari {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:type" content="website" />
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Video</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-accent-light text-accent"><i data-lucide="play-circle" class="w-4 h-4"></i></span>
            Video
        </h1>
    </div>

    @if($videos->count() > 0)
        <div class="ku-video-grid">
            @foreach($videos as $post)
            <article class="ku-video-card" data-post-id="{{ $post->id }}">
                <a href="{{ route('posts.show', $post->slug) }}" class="ku-video-thumb" data-video-player="{{ videoPlayerData($post) }}">
                    @if($post->thumbnail)
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

        <div class="mt-6">
            {{ $videos->links() }}
        </div>
    @else
        <div class="rounded-xl border border-outline bg-surface p-10 text-center">
            <i data-lucide="play-circle" class="w-10 h-10 mx-auto text-gray-400"></i>
            <p class="mt-3 font-semibold">Belum ada video</p>
            <p class="text-sm text-gray-500">Video akan segera hadir.</p>
        </div>
    @endif
@endsection
