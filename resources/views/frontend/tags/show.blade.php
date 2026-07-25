@extends('frontend.layouts.app')

@section('title', $tag->name . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $tagDesc = 'Kumpulan berita dengan tag ' . $tag->name . ' terbaru dari ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' - Portal Berita Konawe Utara';
    @endphp
    <meta name="description" content="{{ $tagDesc }}">
    <meta name="keywords" content="{{ $tag->name }}, berita {{ $tag->name }}, Konut.Update, Konawe Utara">
    <link rel="canonical" href="{{ route('tags.show', $tag->slug) }}" />
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $tag->name }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $tagDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('tags.show', $tag->slug) }}" />
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $tag->name }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $tagDesc }}" />
    @if(!empty($site_settings['logo']))
        <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">{{ $tag->name }}</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-primary-light flex items-center justify-center">
                <i data-lucide="tag" class="w-5 h-5 text-primary"></i>
            </span>
            {{ $tag->name }}
        </h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Kumpulan berita dengan tag {{ $tag->name }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($posts as $post)
                        <article class="bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50 group">
                            <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                                <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low">
                                    <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/600x400/e9ecef/6b7280?text=KONUT' }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                </div>
                            </a>
                            <div class="p-4">
                                @if($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="text-[10px] font-bold text-primary uppercase tracking-wider no-underline hover:underline">{{ $post->category->name }}</a>
                                @endif
                                <h3 class="text-base font-bold text-on-surface mt-1.5 leading-snug">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="no-underline text-on-surface hover:text-primary transition-colors">{{ $post->title }}</a>
                                </h3>
                                <p class="text-sm text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ limitText(strip_tags($post->excerpt ?: $post->body), 120) }}</p>
                                <div class="flex items-center justify-between text-xs text-on-surface-variant mt-3">
                                    <span class="font-medium">{{ $post->author->name ?? 'Redaksi' }}</span>
                                    <span>{{ formatDate($post->published_at) }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if(method_exists($posts, 'links'))
                    <div class="mt-8">{{ $posts->links('vendor.pagination.tailwind') }}</div>
                @endif
            @else
                <div class="bg-surface rounded-2xl p-10 text-center text-on-surface-variant border border-outline">
                    <i data-lucide="info" class="w-8 h-8"></i>
                    <p class="mt-2">Belum ada artikel dengan tag ini.</p>
                </div>
            @endif
        </div>
        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
