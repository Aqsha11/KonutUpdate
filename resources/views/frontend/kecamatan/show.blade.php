@extends('frontend.layouts.app')

@section('title', 'Kec. ' . $kecamatan->name . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $desc = $kecamatan->description ? 'Berita terkini Kecamatan ' . $kecamatan->name . ' - ' . $kecamatan->description : 'Kumpulan berita Kecamatan ' . $kecamatan->name . ' terbaru dari Konut.Update';
    @endphp
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ route('kecamatan.show', $kecamatan->slug) }}" />
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            { "@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ url('/') }}" },
            { "@@type": "ListItem", "position": 2, "name": "Kec. {{ $kecamatan->name }}", "item": "{{ route('kecamatan.show', $kecamatan->slug) }}" }
        ]
    }
    </script>
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>{{ $kecamatan->name }}</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-accent/10 text-accent"><i data-lucide="map-pin" class="w-4 h-4"></i></span>
            Kec. {{ $kecamatan->name }}
        </h1>
        @if($kecamatan->description)
            <p class="text-on-surface-variant text-xs mt-1">{{ $kecamatan->description }}</p>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="news-list">
                    @foreach($posts as $post)
                    <article class="news-item group">
                        <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                            <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/110x80/1a1a2e/ffffff?text=N') }}" alt="{{ $post->title }}" loading="lazy">
                            @if($post->isVideo())
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
                                @endif
                                <span class="news-item-time">{{ formatDate($post->published_at) }}</span>
                            </div>
                            <h3 class="news-item-title">
                                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <div class="news-item-stats">
                                <span class="stat-btn"><i data-lucide="user" class="w-3 h-3"></i> {{ $post->author->name ?? 'Redaksi' }}</span>
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
                    <p>Belum ada berita dari Kecamatan {{ $kecamatan->name }}.</p>
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
