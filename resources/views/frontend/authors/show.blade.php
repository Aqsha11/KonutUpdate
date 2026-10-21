@extends('frontend.layouts.app')

@section('title', $author->name . ' - Penulis - ' . ($site_settings['site_name'] ?? 'KonutUpdate'))

@php
    $authorDesc = $author->bio
        ? $author->name . ' - ' . \Illuminate\Support\Str::limit(strip_tags($author->bio), 150)
        : 'Profil penulis ' . $author->name . ' di ' . ($site_settings['site_name'] ?? 'KonutUpdate') . '. Daftar berita dan artikel terbaru yang ditulisnya.';
@endphp
@section('seo_title', $author->name . ' - Penulis - ' . ($site_settings['site_name'] ?? 'KonutUpdate'))
@section('seo_description', $authorDesc)
@section('seo_type', 'profile')
@section('seo_robots', $posts->total() === 0 ? 'noindex, follow' : '')

@section('json_ld')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Person",
        "@@id": "{{ route('authors.show', $author->slug) }}#person",
        "name": @json($author->name),
        "url": @json(route('authors.show', $author->slug)),
        @if($author->bio)
            "description": @json(\Illuminate\Support\Str::limit(strip_tags($author->bio), 300)),
        @endif
        @if($author->avatar_url)
            "image": @json($author->avatar_url),
        @endif
        "worksFor": { "@@id": "{{ url('/') }}#organization" }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            { "@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ url('/') }}" },
            { "@@type": "ListItem", "position": 2, "name": @json($author->name), "item": "{{ route('authors.show', $author->slug) }}" }
        ]
    }
    </script>
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>{{ $author->name }}</span>
        </nav>
    </div>

    <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5 md:gap-8">
            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden bg-surface-container border-2 border-primary/20 shrink-0 flex items-center justify-center mx-auto sm:mx-0">
                @if ($author->avatar_url)
                    <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="w-full h-full object-cover" loading="lazy">
                @else
                    <i data-lucide="user-round" class="w-9 h-9 md:w-10 md:h-10 text-on-surface-variant"></i>
                @endif
            </div>

            <div class="flex-1 min-w-0 text-center sm:text-left">
                <h1 class="text-lg md:text-xl font-bold text-on-surface">{{ $author->name }}</h1>
                <p class="text-xs text-on-surface-variant uppercase tracking-wide mt-1">Penulis</p>
                @if ($author->bio)
                    <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ $author->bio }}</p>
                @endif
                <div class="flex items-center justify-center sm:justify-start gap-6 mt-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-on-surface">{{ $posts->total() }}</div>
                        <div class="text-xs text-on-surface-variant">Artikel</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="page-title mb-4">
        <span class="page-title-icon bg-primary/10 text-primary"><i data-lucide="newspaper" class="w-4 h-4"></i></span>
        Artikel oleh {{ $author->name }}
    </h2>

    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
        <div class="md:w-[68%]">
            @if($posts->count() > 0)
                <div class="news-list">
                    @foreach($posts as $post)
                        <article class="news-item group">
                            <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/110x80/e9ecef/6b7280?text=N' }}" alt="{{ $post->image_alt }}" loading="lazy">
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
                    <p>Belum ada artikel dari penulis ini.</p>
                </div>
            @endif
        </div>
        <div class="hidden md:block md:w-[32%]">
            <div class="md:sticky md:top-20 space-y-4">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>

    <div class="md:hidden mt-5 space-y-4">
        @include('frontend.partials.sidebar')
    </div>
@endsection
