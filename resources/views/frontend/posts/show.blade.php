@extends('frontend.layouts.app')

@section('title', $post->title . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $excerpt = strip_tags($post->excerpt ?: Str::limit(strip_tags($post->body), 160));
        $thumb = $post->thumbnail ? url(Storage::url($post->thumbnail)) : '';
        $fallbackImage = !empty($site_settings['logo']) ? url(Storage::url($site_settings['logo'])) : '';
        $shareImage = $thumb ?: $fallbackImage;
    @endphp
    <meta name="description" content="{{ $excerpt }}" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $post->title }}" />
    <meta property="og:description" content="{{ $excerpt }}" />
    <meta property="og:type" content="{{ $post->isVideo() ? 'video.other' : 'article' }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @if($shareImage)<meta property="og:image" content="{{ $shareImage }}" />@endif
    <meta property="article:published_time" content="{{ $post->published_at }}" />
    <meta property="article:author" content="{{ $post->author->name ?? 'Redaksi' }}" />
    <meta name="twitter:card" content="{{ $shareImage ? 'summary_large_image' : 'summary' }}" />
    <meta name="twitter:title" content="{{ $post->title }}" />
    <meta name="twitter:description" content="{{ $excerpt }}" />
    @if($shareImage)<meta name="twitter:image" content="{{ $shareImage }}" />@endif
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "{{ $post->isVideo() ? 'VideoObject' : 'NewsArticle' }}",
        "headline": @json($post->title),
        "description": @json($excerpt),
        @if($thumb)"image": "{{ $thumb }}",@endif
        "datePublished": "{{ $post->published_at }}",
        "author": { "@@type": "Person", "name": @json($post->author->name ?? 'Redaksi') },
        "publisher": { "@@type": "Organization", "name": @json($site_settings['site_name'] ?? 'Konut.Update') }
    }
    </script>
@endsection

@section('content')
    {{-- Sticky Share (Desktop) --}}
    <div class="sticky-share" id="stickyShare">
        <a href="{{ shareFacebook(url()->current()) }}" target="_blank" rel="nofollow noopener noreferrer" class="sticky-share-btn facebook" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
        <a href="{{ shareWhatsApp(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="sticky-share-btn whatsapp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        <a href="{{ shareTelegram(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="sticky-share-btn telegram" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
        <a href="{{ shareTwitter(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="sticky-share-btn twitter" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
        <button type="button" onclick="toggleLike({{ $post->id }})" id="like-btn-sticky" class="sticky-share-btn like {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
            <i data-lucide="heart" class="w-4 h-4"></i>
        </button>
    </div>

    <article>
        {{-- Breadcrumb --}}
        <nav class="breadcrumb mb-2">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            @if($post->categories->count() > 0)
                <a href="{{ route('categories.show', $post->categories->first()->slug) }}">{{ $post->categories->first()->name }}</a>
                <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            @elseif($post->category)
                <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            @endif
            <span class="truncate max-w-[140px] md:max-w-[300px]">{{ $post->title }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
            {{-- Main Content --}}
            <div class="lg:w-[68%] min-w-0">
                {{-- Category Badges --}}
                @if($post->categories->count() > 0)
                    @foreach($post->categories as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}" class="category-badge mb-1.5 mr-1 inline-block no-underline hover:opacity-90">{{ $cat->name }}</a>
                    @endforeach
                @elseif($post->category)
                    <a href="{{ route('categories.show', $post->category->slug) }}" class="category-badge mb-1.5 inline-block no-underline hover:opacity-90">{{ $post->category->name }}</a>
                @endif

                {{-- Title --}}
                <h1 class="article-title">
                    @if($post->isVideo())
                        <span class="inline-flex items-center gap-1 text-accent mr-1.5"><i data-lucide="play-circle" class="w-5 h-5 md:w-6 md:h-6"></i></span>
                    @endif
                    {{ $post->title }}
                </h1>

                {{-- Author + Date --}}
                <div class="article-meta-bar">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                            <span class="text-primary font-bold text-sm">{{ strtoupper(substr($post->author->name ?? 'R', 0, 1)) }}</span>
                        </div>
                        <div>
                            <span class="article-author">{{ $post->author->name ?? 'Redaksi' }}</span>
                            <span class="article-date">{{ formatDate($post->published_at) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$post->isVideo())
                        <span class="text-[10px] md:text-[11px] text-on-surface-variant flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ readTime($post->body) }}</span>
                        @endif
                        <span class="text-[10px] md:text-[11px] text-on-surface-variant flex items-center gap-1"><i data-lucide="eye" class="w-3 h-3"></i> {{ number_format($post->views_count) }}</span>
                    </div>
                </div>

                {{-- Share Bar --}}
                <div class="share-bar">
                    <a href="{{ shareFacebook(url()->current()) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn facebook" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="{{ shareWhatsApp(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn whatsapp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="{{ shareTelegram(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn telegram" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="{{ shareTwitter(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn twitter" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <button type="button" onclick="toggleLike({{ $post->id }})" id="like-btn-{{ $post->id }}" class="like-btn {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                        <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                        <span id="like-count-{{ $post->id }}">{{ $post->likesCount() }}</span>
                    </button>
                </div>

                {{-- Video Player --}}
                @if($post->isVideo() && $post->video_url)
                    @if($post->video_poster)
                        <div class="article-hero-media" x-data="{ playing: false }">
                            <div x-show="!playing" @click="playing = true" class="relative cursor-pointer group" style="padding-top: 56.25%;">
                                <img src="{{ $post->video_poster }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-primary/90 group-hover:bg-primary flex items-center justify-center shadow-2xl transition-all group-hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-6 h-6 md:w-7 md:h-7 ml-1"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div x-show="playing" x-cloak>
                                @if($post->video_embed_url && $post->video_embed_url !== $post->video_url)
                                    <div class="relative w-full" style="padding-top: 56.25%;">
                                        <iframe src="{{ $post->video_embed_url }}?autoplay=1" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen allow="autoplay"></iframe>
                                    </div>
                                @else
                                    <video controls autoplay class="w-full" style="max-height:500px;">
                                        <source src="{{ $post->video_url }}">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="article-hero-media">
                            <video controls preload="metadata" class="w-full" style="max-height:500px;"><source src="{{ $post->video_url }}"></video>
                        </div>
                    @endif
                @elseif($post->thumbnail)
                    <div class="article-hero-media">
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="w-full h-auto object-cover" loading="eager">
                    </div>
                @endif

                {{-- Table of Contents --}}
                <div class="toc" id="articleToc" x-data="{ open: true }">
                    <div class="toc-title cursor-pointer" x-on:click="open = !open">
                        <i data-lucide="list" class="w-3.5 h-3.5 text-primary"></i>
                        <span>Daftar Isi</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 ml-auto transition-transform" :class="open && 'rotate-180'"></i>
                    </div>
                    <ol class="toc-list" x-show="open" x-transition.opacity.duration.200ms></ol>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var body = document.getElementById('articleBody');
                    var tocList = document.querySelector('#articleToc .toc-list');
                    var tocContainer = document.getElementById('articleToc');
                    if (!body || !tocList) return;
                    var headings = body.querySelectorAll('h2, h3');
                    if (headings.length < 2) { tocContainer.style.display = 'none'; return; }
                    headings.forEach(function(h, i) {
                        var id = 'heading-' + i;
                        h.id = id;
                        var li = document.createElement('li');
                        li.style.paddingLeft = h.tagName === 'H3' ? '16px' : '0';
                        var a = document.createElement('a');
                        a.href = '#' + id;
                        a.textContent = h.textContent;
                        a.className = 'toc-link';
                        li.appendChild(a);
                        tocList.appendChild(li);
                    });
                });
                </script>

                {{-- Article Body --}}
                <div class="article-body" id="articleBody">
                    {!! $post->body !!}
                </div>

                {{-- Iklan Mobile --}}
                @if(isset($sidebarAdsTop) && $sidebarAdsTop->count() > 0)
                <div class="lg:hidden mt-3 mb-3 space-y-2">
                    @foreach($sidebarAdsTop as $ad)
                    <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-lg overflow-hidden border border-outline no-underline group">
                        <div class="aspect-[2/1] overflow-hidden bg-surface-container-low">
                            <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
                        </div>
                        <div class="p-1.5">
                            <p class="text-[10px] font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                            <p class="text-[8px] text-on-surface-variant mt-0.5">Iklan</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- In-Article Ads --}}
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
                    <div class="flex flex-wrap items-center gap-1.5 mt-4 pt-3 border-t border-outline">
                        <span class="text-xs font-semibold text-on-surface-variant mr-1">Tag:</span>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" class="tag-pill">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif

                {{-- Share Bottom --}}
                <div class="share-bar mt-4 pt-3 border-t border-outline">
                    <span class="text-xs font-semibold text-on-surface-variant mr-1">Bagikan:</span>
                    <a href="{{ shareFacebook(url()->current()) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn facebook"><i class="fab fa-facebook"></i></a>
                    <a href="{{ shareWhatsApp(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="{{ shareTelegram(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn telegram"><i class="fab fa-telegram"></i></a>
                    <a href="{{ shareTwitter(url()->current(), $post->title) }}" target="_blank" rel="nofollow noopener noreferrer" class="share-btn twitter"><i class="fab fa-x-twitter"></i></a>
                </div>

                {{-- Iklan Mobile Bawah --}}
                @if(isset($sidebarAdsBottom) && $sidebarAdsBottom->count() > 0)
                <div class="lg:hidden mt-3 mb-3 space-y-2">
                    @foreach($sidebarAdsBottom as $ad)
                    <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow sponsored" class="block bg-surface rounded-lg overflow-hidden border border-outline no-underline group">
                        <div class="aspect-[2/1] overflow-hidden bg-surface-container-low">
                            <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" loading="lazy">
                        </div>
                        <div class="p-1.5">
                            <p class="text-[10px] font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $ad->title }}</p>
                            <p class="text-[8px] text-on-surface-variant mt-0.5">Iklan</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Comments --}}
                @include('frontend.posts._comments')

                {{-- Related Posts --}}
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                    <section class="mt-6">
                        <div class="section-bar">
                            <h2 class="section-bar-title"><span class="section-bar-dot bg-primary"></span>Berita Terkait</h2>
                        </div>
                        <div class="news-list">
                            @foreach($relatedPosts as $related)
                            <a href="{{ route('posts.show', $related->slug) }}" class="news-item group">
                                <div class="news-item-thumb">
                                    <img src="{{ $related->thumbnail ? Storage::url($related->thumbnail) : 'https://placehold.co/110x80/e9ecef/6b7280?text=N' }}" alt="{{ $related->title }}" loading="lazy">
                                </div>
                                <div class="news-item-body">
                                    <div class="news-item-meta">
                                        @if($related->categories->count() > 0)
                                            <span class="news-item-cat">{{ $related->categories->first()->name }}</span>
                                        @elseif($related->category)
                                            <span class="news-item-cat">{{ $related->category->name }}</span>
                                        @endif
                                        <span class="news-item-time">{{ formatDate($related->published_at) }}</span>
                                    </div>
                                    <h3 class="news-item-title">{{ $related->title }}</h3>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Next / Prev --}}
                @if(isset($nextPost) || isset($prevPost))
                <div class="flex flex-col sm:flex-row gap-2 mt-6 pt-4 border-t border-outline">
                    @if(isset($prevPost))
                    <a href="{{ route('posts.show', $prevPost->slug) }}" class="flex-1 flex items-center gap-2 p-3 rounded-lg bg-surface-container-low no-underline group hover:bg-primary-light transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-on-surface-variant group-hover:text-primary shrink-0"></i>
                        <div class="min-w-0">
                            <span class="text-[9px] font-semibold text-on-surface-variant uppercase tracking-wider">Sebelumnya</span>
                            <h4 class="text-xs font-bold text-on-surface line-clamp-1 group-hover:text-primary transition-colors">{{ $prevPost->title }}</h4>
                        </div>
                    </a>
                    @endif
                    @if(isset($nextPost))
                    <a href="{{ route('posts.show', $nextPost->slug) }}" class="flex-1 flex items-center gap-2 p-3 rounded-lg bg-surface-container-low no-underline group hover:bg-primary-light transition-colors sm:text-right sm:flex-row-reverse">
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-on-surface-variant group-hover:text-primary shrink-0"></i>
                        <div class="min-w-0">
                            <span class="text-[9px] font-semibold text-on-surface-variant uppercase tracking-wider">Selanjutnya</span>
                            <h4 class="text-xs font-bold text-on-surface line-clamp-1 group-hover:text-primary transition-colors">{{ $nextPost->title }}</h4>
                        </div>
                    </a>
                    @endif
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="hidden lg:block lg:w-[32%]">
                <div class="lg:sticky lg:top-20 space-y-4">
                    @include('frontend.partials.sidebar')
                </div>
            </div>
        </div>
    </article>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var stickyShare = document.getElementById('stickyShare');
        if (!stickyShare) return;
        var shareBar = document.querySelector('.share-bar');
        if (!shareBar) return;
        window.addEventListener('scroll', function() {
            var rect = shareBar.getBoundingClientRect();
            if (rect.bottom < 0) { stickyShare.classList.add('visible'); } else { stickyShare.classList.remove('visible'); }
        }, { passive: true });
    });
    </script>
@endsection
