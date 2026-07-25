@extends('frontend.layouts.app')

@section('title', ($site_settings['site_name'] ?? 'Konut.Update') . ' - Berita Terpercaya Konawe Utara')

@section('meta')
    <meta name="description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}">
    <meta name="keywords" content="{{ $site_settings['meta_keywords'] ?? 'berita, konawe utara, konut, news, informasi' }}">
    <link rel="canonical" href="{{ url('/') }}" />
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya" />
    @if(!empty($site_settings['logo']))
        <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
    {{-- Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "NewsMediaOrganization",
        "name": @json($site_settings['site_name'] ?? 'Konut.Update'),
        "url": "{{ url('/') }}",
        "description": "Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya",
        "foundingDate": "2024",
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Konawe Utara",
            "addressRegion": "Sulawesi Tenggara",
            "addressCountry": "ID"
        },
        "potentialAction": {
            "@@type": "SearchAction",
            "target": {
                "@@type": "EntryPoint",
                "urlTemplate": "{{ url('/search') }}?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
@endsection

@php
    $heroMain = $featuredPosts->shift();
    $heroSide = $featuredPosts->take(4);
    $kriminalPosts = $categoryPosts['Kriminal'] ?? collect();
    $pemerintahanPosts = $categoryPosts['Pemerintahan'] ?? collect();
    $tambangPosts = $categoryPosts['Tambang'] ?? collect();
    $wisataPosts = $categoryPosts['Ekonomi'] ?? collect();
    $olahragaPosts = $categoryPosts['Olahraga'] ?? collect();
@endphp

@section('content')
    {{-- Hero Section --}}
    @if($heroMain)
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-8 lg:mb-10">
        <article class="lg:col-span-7 group">
            <a href="{{ route('posts.show', $heroMain->slug) }}" class="block no-underline">
                <div class="relative overflow-hidden rounded-2xl h-[320px] md:h-[500px]">
                    <img src="{{ $heroMain->thumbnail ? Storage::url($heroMain->thumbnail) : ($heroMain->video_poster ?? 'https://placehold.co/800x500/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $heroMain->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="eager" fetchpriority="high">
                    <div class="hero-gradient absolute inset-0"></div>
                    @if($heroMain->isVideo())
                        <div class="absolute inset-0 flex items-center justify-center" style="z-index:1;">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-white/90 flex items-center justify-center shadow-2xl">
                                <i data-lucide="play" class="w-8 h-8 md:w-10 md:h-10 text-primary ml-1"></i>
                            </div>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                        @if($heroMain->category)
                            <span class="category-badge mb-3 inline-block">
                                @if($heroMain->isVideo())
                                    <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                @endif
                                {{ $heroMain->category->name }}
                            </span>
                        @endif
                        <h1 class="text-xl md:text-3xl lg:text-4xl font-extrabold text-white mb-2 leading-tight drop-shadow-lg">{{ $heroMain->title }}</h1>
                        <p class="text-white/80 text-sm md:text-base line-clamp-2 hidden md:block mb-3 max-w-2xl">{{ $heroMain->excerpt ? strip_tags($heroMain->excerpt) : '' }}</p>
                        <div class="flex items-center gap-4 text-white/60 text-xs">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                {{ $heroMain->published_at ? \Carbon\Carbon::parse($heroMain->published_at)->diffForHumans() : '' }}
                            </span>
                            @if($heroMain->author)
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    {{ $heroMain->author->name ?? 'Redaksi' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </article>
        <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($heroSide as $post)
            <article class="group">
                <a href="{{ route('posts.show', $post->slug) }}" class="block no-underline">
                    <div class="relative overflow-hidden rounded-xl h-44 sm:h-[236px]">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        <div class="hero-gradient-sm absolute inset-0"></div>
                        @if($post->isVideo())
                            <div class="absolute inset-0 flex items-center justify-center" style="z-index:1;">
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                    <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                </div>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            @if($post->category)
                                <span class="text-[10px] font-bold uppercase text-primary-fixed-dim tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    {{ $post->category->name }}
                                </span>
                            @endif
                            <h3 class="text-sm md:text-base font-bold text-white mt-0.5 line-clamp-2 drop-shadow leading-snug">{{ $post->title }}</h3>
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Main Content + Sidebar --}}
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-8 lg:space-y-10">

            {{-- Berita Terbaru --}}
            <section class="reveal">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="section-title pb-1">Berita Terbaru</h2>
                    <a href="{{ route('search') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div id="posts-container" class="space-y-4">
                    @forelse($latestPosts as $post)
                    <article data-post-item class="flex flex-col sm:flex-row gap-5 bg-surface rounded-2xl p-4 card-hover border border-outline/50" data-post-id="{{ $post->id }}">
                        <a href="{{ route('posts.show', $post->slug) }}" class="sm:w-[220px] shrink-0">
                            <div class="aspect-video sm:aspect-[4/3] rounded-xl overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="flex-1 flex flex-col justify-center min-w-0">
                            <div class="flex items-center gap-2.5 text-xs text-on-surface-variant mb-2">
                                @if($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="font-bold text-primary uppercase tracking-wider no-underline hover:underline">{{ $post->category->name }}</a>
                                    <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                @endif
                                <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d F Y') : '' }}</span>
                                <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                <span>{{ $post->author->name ?? 'Redaksi' }}</span>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-on-surface leading-snug">
                                <a href="{{ route('posts.show', $post->slug) }}" class="no-underline text-on-surface hover:text-primary transition-colors">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-4 h-4 text-accent inline align-text-top mr-0.5"></i>
                                    @endif
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-on-surface-variant mt-2 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                            <div class="flex items-center gap-4 mt-3 text-xs text-on-surface-variant">
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    {{ number_format($post->views_count) }}
                                </span>
                                @if(!$post->isVideo())
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        {{ readTime($post->body) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="bg-surface rounded-2xl p-10 text-center text-on-surface-variant border border-outline">
                        <i data-lucide="newspaper" class="w-10 h-10 mb-3 mx-auto"></i>
                        <p>Belum ada berita.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination / Infinite Scroll Sentinel --}}
                @if(method_exists($latestPosts, 'links') && $latestPosts->hasPages())
                    <div id="infinite-scroll-sentinel" class="mt-8"></div>
                    <div class="mt-8 hidden">
                        {{ $latestPosts->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </section>

            {{-- Kriminal --}}
            @if($kriminalPosts->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-1 h-7 bg-error rounded-full"></span>
                    <h2 class="section-title pb-1 flex-1">Kriminal</h2>
                    <a href="{{ route('categories.show', 'kriminal') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($kriminalPosts as $post)
                    <article class="group bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50">
                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                            <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-[10px] font-bold text-error uppercase tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    Kriminal
                                </span>
                                <h4 class="text-sm font-bold text-on-surface mt-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h4>
                                <p class="text-xs text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <span class="text-xs text-on-surface-variant mt-3 block">{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Pemerintahan --}}
            @if($pemerintahanPosts->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-1 h-7 bg-primary rounded-full"></span>
                    <h2 class="section-title pb-1 flex-1">Pemerintahan</h2>
                    <a href="{{ route('categories.show', 'pemerintahan') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($pemerintahanPosts as $post)
                    <article class="group bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50">
                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                            <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    Pemerintahan
                                </span>
                                <h4 class="text-sm font-bold text-on-surface mt-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h4>
                                <p class="text-xs text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <span class="text-xs text-on-surface-variant mt-3 block">{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Tambang --}}
            @if($tambangPosts->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-1 h-7 bg-secondary rounded-full"></span>
                    <h2 class="section-title pb-1 flex-1">Tambang</h2>
                    <a href="{{ route('categories.show', 'tambang') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($tambangPosts as $post)
                    <article class="group bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50">
                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                            <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-[10px] font-bold text-secondary uppercase tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    Tambang
                                </span>
                                <h4 class="text-sm font-bold text-on-surface mt-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h4>
                                <p class="text-xs text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <span class="text-xs text-on-surface-variant mt-3 block">{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Ekonomi --}}
            @if($wisataPosts->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-1 h-7 bg-tertiary rounded-full"></span>
                    <h2 class="section-title pb-1 flex-1">Ekonomi</h2>
                    <a href="{{ route('categories.show', 'ekonomi') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($wisataPosts as $post)
                    <article class="group bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50">
                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                            <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-[10px] font-bold text-tertiary uppercase tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    Ekonomi
                                </span>
                                <h4 class="text-sm font-bold text-on-surface mt-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h4>
                                <p class="text-xs text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <span class="text-xs text-on-surface-variant mt-3 block">{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Olahraga --}}
            @if($olahragaPosts->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-1 h-7 bg-[#f59e0b] rounded-full"></span>
                    <h2 class="section-title pb-1 flex-1">Olahraga</h2>
                    <a href="{{ route('categories.show', 'olahraga') }}" class="text-xs font-semibold text-primary hover:underline no-underline uppercase tracking-wider">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($olahragaPosts as $post)
                    <article class="group bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50">
                        <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                            <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                            <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-[10px] font-bold text-[#f59e0b] uppercase tracking-wider">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                    @endif
                                    Olahraga
                                </span>
                                <h4 class="text-sm font-bold text-on-surface mt-1.5 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h4>
                                <p class="text-xs text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <span class="text-xs text-on-surface-variant mt-3 block">{{ \Carbon\Carbon::parse($post->published_at)->format('d F Y') }}</span>
                            </div>
                        </a>
                    </article>
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
@endsection
