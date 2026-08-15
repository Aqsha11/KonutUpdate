@extends('frontend.layouts.app')

@section('title', 'Opini - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Opini dan tulisan pembaca dari {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="Opini - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Opini dan tulisan pembaca dari {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:type" content="website" />
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            { "@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ url('/') }}" },
            { "@@type": "ListItem", "position": 2, "name": "Opini", "item": "{{ url()->current() }}" }
        ]
    }
    </script>
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Opini</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-primary-light text-primary"><i data-lucide="pencil-line" class="w-4 h-4"></i></span>
            Opini
        </h1>
        <p class="text-on-surface-variant text-xs mt-1">Pandangan dan analisis dari pembaca serta kontributor</p>
        <a href="{{ route('opini.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold no-underline hover:opacity-90 transition-opacity">
            <i data-lucide="pencil-line" class="w-4 h-4"></i> Tulis Opini Anda
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg border border-primary/20 bg-primary/10 text-primary text-sm flex items-start gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="news-list">
                    @foreach($posts as $post)
                    <article class="news-item group">
                        <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                            <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                        </a>
                        <div class="news-item-body">
                            <div class="news-item-meta">
                                <span class="news-item-cat">Opini</span>
                                @if($post->categories->count() > 0)
                                    <span class="mx-1">/</span>
                                    @foreach($post->categories as $cat)
                                        <a href="{{ route('categories.show', $cat->slug) }}" class="news-item-cat">{{ $cat->name }}</a>
                                    @endforeach
                                @elseif($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="news-item-cat">{{ $post->category->name }}</a>
                                @endif
                                <span class="news-item-time">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->diffForHumans() : '' }}</span>
                            </div>
                            <h3 class="news-item-title">
                                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <div class="news-item-stats">
                                <span class="stat-btn"><i data-lucide="user" class="w-3 h-3"></i> {{ $post->author_name }}</span>
                                <span class="stat-btn"><i data-lucide="heart" class="w-3 h-3"></i> {{ $post->likes_count ?? 0 }}</span>
                                <span class="stat-btn"><i data-lucide="message-circle" class="w-3 h-3"></i> {{ $post->comments_count ?? 0 }}</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                <div class="mt-4">{{ $posts->links('vendor.pagination.tailwind') }}</div>
            @else
                <div class="empty-placeholder">
                    <i data-lucide="pencil-line" class="w-8 h-8 mb-2"></i>
                    <p>Belum ada opini.</p>
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
