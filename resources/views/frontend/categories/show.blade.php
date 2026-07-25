@extends('frontend.layouts.app')

@section('title', $category->name . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $catDesc = $category->description ?: 'Kumpulan berita ' . $category->name . ' - Konut.Update';
    @endphp
    <meta name="description" content="{{ $catDesc }}">
    <link rel="canonical" href="{{ route('categories.show', $category->slug) }}" />
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $category->name }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $catDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('categories.show', $category->slug) }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $category->name }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $catDesc }}" />
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
                "name": "{{ $category->name }}",
                "item": "{{ route('categories.show', $category->slug) }}"
            }
        ]
    }
    </script>
@endsection

@php
    $heroMain = $heroPosts->shift();
    $heroSide = $heroPosts->take(4);
@endphp

@section('content')
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">{{ $category->name }}</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-on-surface-variant text-sm mt-1.5">{{ $category->description }}</p>
        @endif
    </div>

    {{-- Hero --}}
    @if($heroMain)
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-8">
        <article class="lg:col-span-7 group">
            <a href="{{ route('posts.show', $heroMain->slug) }}" class="block no-underline">
                <div class="relative overflow-hidden rounded-2xl h-[300px] md:h-[450px]">
                    <img src="{{ $heroMain->thumbnail ? Storage::url($heroMain->thumbnail) : ($heroMain->video_poster ?? 'https://placehold.co/800x500/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $heroMain->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="eager" fetchpriority="high">
                    <div class="hero-gradient absolute inset-0"></div>
                    @if($heroMain->isVideo())
                        <div class="absolute inset-0 flex items-center justify-center" style="z-index:1;">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-white/90 flex items-center justify-center shadow-2xl">
                                <i data-lucide="play" class="w-8 h-8 md:w-10 md:h-10 text-primary ml-1"></i>
                            </div>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-5 md:p-7">
                        <span class="category-badge mb-3 inline-block">
                            @if($heroMain->isVideo())
                                <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                            @endif
                            {{ $category->name }}
                        </span>
                        <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-white leading-tight drop-shadow-lg">{{ $heroMain->title }}</h2>
                        <p class="text-white/80 text-sm line-clamp-2 hidden md:block mt-2 max-w-2xl">{{ $heroMain->excerpt ? strip_tags($heroMain->excerpt) : '' }}</p>
                        <div class="flex items-center gap-3 mt-3 text-white/60 text-xs">
                            <span>{{ $heroMain->published_at ? \Carbon\Carbon::parse($heroMain->published_at)->diffForHumans() : '' }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </article>
        <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($heroSide as $post)
            <article class="group">
                <a href="{{ route('posts.show', $post->slug) }}" class="block no-underline">
                    <div class="relative overflow-hidden rounded-xl h-40 sm:h-44">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        <div class="hero-gradient-sm absolute inset-0"></div>
                        @if($post->isVideo())
                            <div class="absolute inset-0 flex items-center justify-center" style="z-index:1;">
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                    <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                </div>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            <h3 class="text-sm font-bold text-white line-clamp-2 drop-shadow leading-snug">{{ $post->title }}</h3>
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Content + Sidebar --}}
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($posts as $post)
                        <article class="bg-surface rounded-2xl overflow-hidden card-hover border border-outline/50 group">
                            <a href="{{ route('posts.show', $post->slug) }}" class="no-underline">
                                <div class="aspect-video overflow-hidden img-zoom bg-surface-container-low relative">
                                    <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/600x400/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                    @if($post->isVideo())
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                            <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                                <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                @if($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="text-[10px] font-bold text-primary uppercase tracking-wider no-underline hover:underline">
                                        @if($post->isVideo())
                                            <i data-lucide="play-circle" class="w-3 h-3 inline mr-0.5 align-text-bottom"></i>
                                        @endif
                                        {{ $post->category->name }}
                                    </a>
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
                    <p class="mt-2">Belum ada artikel dalam kategori ini.</p>
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
