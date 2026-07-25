@extends('frontend.layouts.app')

@section('title', $post->title . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $excerpt = strip_tags($post->excerpt ?: Str::limit(strip_tags($post->body), 160));
        $thumb = $post->thumbnail ? url(Storage::url($post->thumbnail)) : '';
    @endphp
    <meta name="description" content="{{ $excerpt }}" />
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $post->title }}" />
    <meta property="og:description" content="{{ $excerpt }}" />
    <meta property="og:type" content="{{ $post->isVideo() ? 'video.other' : 'article' }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @if($thumb)
        <meta property="og:image" content="{{ $thumb }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:alt" content="{{ $post->title }}" />
    @endif
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if($post->isVideo() && $post->video_url)
        <meta property="og:video" content="{{ $post->video_embed_url }}" />
        <meta property="og:video:type" content="text/html" />
        <meta property="og:video:width" content="1280" />
        <meta property="og:video:height" content="720" />
    @endif
    <meta property="article:published_time" content="{{ $post->published_at }}" />
    <meta property="article:modified_time" content="{{ $post->updated_at ?? $post->published_at }}" />
    <meta property="article:author" content="{{ $post->author->name ?? 'Redaksi' }}" />
    <meta property="article:section" content="{{ $post->category->name ?? '' }}" />
    @foreach($post->tags as $tag)
        <meta property="article:tag" content="{{ $tag->name }}" />
    @endforeach
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $post->isVideo() ? 'player' : 'summary_large_image' }}" />
    <meta name="twitter:title" content="{{ $post->title }}" />
    <meta name="twitter:description" content="{{ $excerpt }}" />
    @if($thumb)
        <meta name="twitter:image" content="{{ $thumb }}" />
        <meta name="twitter:image:alt" content="{{ $post->title }}" />
    @endif
    @if($post->isVideo() && $post->video_embed_url)
        <meta name="twitter:player" content="{{ $post->video_embed_url }}" />
        <meta name="twitter:player:width" content="1280" />
        <meta name="twitter:player:height" content="720" />
    @endif
    {{-- Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "{{ $post->isVideo() ? 'VideoObject' : 'NewsArticle' }}",
        "headline": @json($post->title),
        "description": @json($excerpt),
        @if($thumb)
        "image": "{{ $thumb }}",
        @endif
        "datePublished": "{{ $post->published_at }}",
        "dateModified": "{{ $post->updated_at ?? $post->published_at }}",
        "author": {
            "@@type": "Person",
            "name": @json($post->author->name ?? 'Redaksi')
        },
        "publisher": {
            "@@type": "Organization",
            "name": @json($site_settings['site_name'] ?? 'Konut.Update'),
            "logo": {
                "@@type": "ImageObject",
                "url": "{{ !empty($site_settings['logo']) ? url(Storage::url($site_settings['logo'])) : '' }}"
            }
        },
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@@id": "{{ url()->current() }}"
        },
        "wordCount": {{ str_word_count(strip_tags($post->body)) }}@if($post->isVideo()),
        "contentUrl": "{{ $post->video_url }}",
        "embedUrl": "{{ $post->video_embed_url }}",
        "uploadDate": "{{ $post->published_at }}",
        "duration": "PT0M"@endif
    }
    </script>
    @if($post->category)
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "{{ url('/') }}"
            },
            {
                "@@type": "ListItem",
                "position": 2,
                "name": "{{ $post->category->name }}",
                "item": "{{ route('categories.show', $post->category->slug) }}"
            },
            {
                "@@type": "ListItem",
                "position": 3,
                "name": "{{ $post->title }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>
    @endif
@endsection

@section('content')
    <article>
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-5 flex-wrap">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            @if($post->category)
                <a href="{{ route('categories.show', $post->category->slug) }}" class="no-underline text-on-surface-variant hover:text-primary transition-colors">{{ $post->category->name }}</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
            @endif
            <span class="text-on-surface truncate max-w-[200px] md:max-w-[400px] font-medium">{{ $post->title }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
            {{-- Main Content --}}
            <div class="lg:w-[68%]">
                {{-- Category Badge --}}
                @if($post->category)
                    <a href="{{ route('categories.show', $post->category->slug) }}" class="category-badge mb-4 inline-block no-underline hover:opacity-90">{{ $post->category->name }}</a>
                @endif

                {{-- Title --}}
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-on-surface leading-tight mb-4">
                    @if($post->isVideo())
                        <span class="inline-flex items-center gap-1.5 text-accent mr-2 align-middle">
                            <i data-lucide="play-circle" class="w-7 h-7 md:w-8 md:h-8"></i>
                        </span>
                    @endif
                    {{ $post->title }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-on-surface-variant mb-6 pb-6 border-b border-outline">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span class="font-medium">{{ $post->author->name ?? 'Redaksi' }}</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>{{ formatDate($post->published_at) }}</span>
                    </span>
                    @if(!$post->isVideo())
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <span>{{ readTime($post->body) }}</span>
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>{{ number_format($post->views_count) }} x dilihat</span>
                    </span>
                </div>

                {{-- Video Player --}}
                @if($post->isVideo() && $post->video_url)
                    @if($post->video_poster)
                        <div class="rounded-2xl overflow-hidden mb-6 bg-black shadow-sm" x-data="{ playing: false }">
                            <div x-show="!playing" @click="playing = true" class="relative cursor-pointer group" style="padding-top: 56.25%;">
                                <img src="{{ $post->video_poster }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'">
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-primary/90 group-hover:bg-primary flex items-center justify-center shadow-2xl transition-all group-hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-7 h-7 md:w-9 md:h-9 ml-1"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div x-show="playing" x-cloak>
                                @if($post->video_embed_url && $post->video_embed_url !== $post->video_url)
                                    <div class="relative w-full" style="padding-top: 56.25%;">
                                        <iframe src="{{ $post->video_embed_url }}?autoplay=1" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                    </div>
                                @else
                                    <video controls autoplay class="w-full" style="max-height:500px;">
                                        <source src="{{ $post->video_url }}">
                                        Browser Anda tidak mendukung pemutar video.
                                    </video>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl overflow-hidden mb-6 bg-black shadow-sm">
                            <video controls preload="metadata" class="w-full" style="max-height:500px;">
                                <source src="{{ $post->video_url }}">
                                Browser Anda tidak mendukung pemutar video.
                            </video>
                        </div>
                    @endif
                @elseif($post->thumbnail)
                    {{-- Featured Image (article only) --}}
                    <div class="rounded-2xl overflow-hidden mb-6 bg-surface-container-low shadow-sm">
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-auto object-cover" loading="eager">
                    </div>
                @endif

                {{-- Share Top --}}
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-sm font-semibold text-on-surface-variant mr-1">Bagikan:</span>
                    <a href="{{ shareFacebook(url()->current()) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn facebook" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="{{ shareWhatsApp(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn whatsapp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="{{ shareTelegram(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn telegram" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="{{ shareTwitter(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn twitter" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>

                {{-- Article Body --}}
                <div class="article-body">
                    {!! $post->body !!}
                </div>

                {{-- Iklan Dalam Artikel --}}
                @if(isset($articleAds) && $articleAds->count() > 0)
                    <div class="article-ad-wrap">
                        @foreach($articleAds as $ad)
                        <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="article-ad-banner group">
                            <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" loading="lazy">
                            <div class="article-ad-overlay">
                                <span class="article-ad-title">{{ $ad->title }}</span>
                                <span class="article-ad-label">Iklan</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @endif

                {{-- Tags --}}
                @if($post->tags->count() > 0)
                    <div class="flex flex-wrap items-center gap-2 mt-8 pt-6 border-t border-outline">
                        <span class="text-sm font-semibold text-on-surface-variant mr-1">Tag:</span>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" class="px-3 py-1.5 bg-surface-container-low text-sm text-on-surface-variant rounded-full hover:bg-primary-light hover:text-primary dark:hover:bg-primary-container no-underline transition-colors">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif

                {{-- Share Bottom --}}
                <div class="flex items-center gap-2 mt-8 pt-6 border-t border-outline">
                    <span class="text-sm font-semibold text-on-surface-variant mr-1">Bagikan artikel ini:</span>
                    <a href="{{ shareFacebook(url()->current()) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn facebook" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="{{ shareWhatsApp(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn whatsapp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="{{ shareTelegram(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn telegram" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="{{ shareTwitter(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn twitter" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>

                {{-- Comments --}}
                @include('frontend.posts._comments')

                {{-- Related Posts --}}
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                    <section class="related-section mt-10">
                        <h3 class="section-title pb-1 mb-5">Berita Terkait</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($relatedPosts as $related)
                                <a href="{{ route('posts.show', $related->slug) }}" class="flex gap-4 bg-surface rounded-2xl p-4 card-hover border border-outline/50 no-underline group">
                                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-surface-container-low relative">
                                        <img src="{{ $related->thumbnail ? Storage::url($related->thumbnail) : 'https://placehold.co/80x80/e9ecef/6b7280?text=N' }}" alt="{{ $related->title }}" class="w-full h-full object-cover" loading="lazy">
                                        @if($related->isVideo())
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                                <div class="w-7 h-7 rounded-full bg-white/90 flex items-center justify-center">
                                                    <i data-lucide="play" class="w-3.5 h-3.5 text-primary ml-0.5"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                                            @if($related->isVideo())
                                                <i data-lucide="play-circle" class="w-3.5 h-3.5 text-accent inline align-text-top mr-0.5"></i>
                                            @endif
                                            {{ $related->title }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-2 text-xs text-on-surface-variant">
                                            <span>{{ $related->category->name ?? '' }}</span>
                                            <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                            <span>{{ formatDate($related->published_at) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-[32%]">
                <div class="lg:sticky lg:top-24 space-y-6">
                    @include('frontend.partials.sidebar')
                </div>
            </div>
        </div>
    </article>
@endsection
