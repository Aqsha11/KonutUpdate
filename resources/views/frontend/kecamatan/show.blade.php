@extends('frontend.layouts.app')

@section('title', 'Kec. ' . $kecamatan->name . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $desc = $kecamatan->description ? 'Berita terkini Kecamatan ' . $kecamatan->name . ' - ' . $kecamatan->description : 'Kumpulan berita Kecamatan ' . $kecamatan->name . ' terbaru dari Konut.Update';
        $kw = 'berita ' . $kecamatan->name . ', Kecamatan ' . $kecamatan->name . ', Konawe Utara, Konut.Update';
    @endphp
    <meta name="description" content="{{ $desc }}">
    <meta name="keywords" content="{{ $kw }}">
    <link rel="canonical" href="{{ route('kecamatan.show', $kecamatan->slug) }}" />
    <meta property="og:title" content="Kec. {{ $kecamatan->name }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $desc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('kecamatan.show', $kecamatan->slug) }}" />
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
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
                "name": "Kec. {{ $kecamatan->name }}",
                "item": "{{ route('kecamatan.show', $kecamatan->slug) }}"
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
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">{{ $kecamatan->name }}</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">
            <i data-lucide="map-pin" class="w-6 h-6 inline text-primary mr-1 align-text-bottom"></i>
            Kec. {{ $kecamatan->name }}
        </h1>
        @if($kecamatan->description)
            <p class="text-on-surface-variant text-sm mt-1.5">{{ $kecamatan->description }}</p>
        @endif
    </div>

    @if($heroMain)
    <section class="grid grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-4 mb-6 lg:mb-8">
        <article class="col-span-2 lg:col-span-7 group">
            <a href="{{ route('posts.show', $heroMain->slug) }}" class="block no-underline">
                <div class="relative overflow-hidden rounded-2xl h-[200px] sm:h-[300px] md:h-[450px]">
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
                            Kec. {{ $kecamatan->name }}
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
        <div class="col-span-2 lg:col-span-5 grid grid-cols-2 gap-2 sm:gap-3 lg:gap-4">
            @foreach($heroSide as $post)
            <article class="group">
                <a href="{{ route('posts.show', $post->slug) }}" class="block no-underline">
                    <div class="relative overflow-hidden rounded-lg sm:rounded-xl h-28 sm:h-40 lg:h-44">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        <div class="hero-gradient-sm absolute inset-0"></div>
                        @if($post->isVideo())
                            <div class="absolute inset-0 flex items-center justify-center" style="z-index:1;">
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                    <i data-lucide="play" class="w-4 h-4 text-primary ml-0.5"></i>
                                </div>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 p-2 sm:p-3">
                            <h3 class="text-[11px] sm:text-sm font-bold text-white line-clamp-2 drop-shadow leading-snug">{{ $post->title }}</h3>
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="grid grid-cols-2 gap-2.5 sm:gap-3 lg:gap-4">
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
                            <div class="p-2.5 sm:p-3 lg:p-4">
                                @if($post->categories->count() > 0)
                                    @foreach($post->categories as $cat)
                                        <a href="{{ route('categories.show', $cat->slug) }}" class="text-[10px] font-bold text-primary uppercase tracking-wider no-underline hover:underline">{{ $cat->name }}</a>
                                        @if(! $loop->last)
                                            <span class="text-on-surface-variant"> / </span>
                                        @endif
                                    @endforeach
                                @endif
                                <h3 class="text-[13px] sm:text-base font-bold text-on-surface mt-1 sm:mt-1.5 leading-snug">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="no-underline text-on-surface hover:text-primary transition-colors">{{ $post->title }}</a>
                                </h3>
                                <p class="text-[11px] sm:text-sm text-on-surface-variant mt-1 sm:mt-1.5 line-clamp-2 leading-relaxed">{{ limitText(strip_tags($post->excerpt ?: $post->body), 120) }}</p>
                                <div class="flex items-center justify-between text-[10px] sm:text-xs text-on-surface-variant mt-2 sm:mt-3">
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
                    <p class="mt-2">Belum ada berita dari Kecamatan {{ $kecamatan->name }}.</p>
                </div>
            @endif
        </div>
        <div class="hidden lg:block lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
