@extends('frontend.layouts.app')

@section('title', 'Semua Berita - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Arsip seluruh berita {{ $site_settings['site_name'] ?? 'Konut.Update' }} — cari dan filter berdasarkan kategori serta kecamatan.">
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="Semua Berita - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Arsip seluruh berita {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:type" content="website" />
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Semua Berita</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-primary-light text-primary"><i data-lucide="newspaper" class="w-4 h-4"></i></span>
            Semua Berita
        </h1>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('news.index') }}" id="news-filter-form" class="news-filter">
        <div class="news-filter-field">
            <i data-lucide="search" class="w-3.5 h-3.5"></i>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul atau isi berita...">
        </div>
        <select name="kategori" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" @selected($categorySlug === $cat->slug)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="kecamatan" onchange="this.form.submit()">
            <option value="">Semua Kecamatan</option>
            @foreach($kecamatans as $kec)
                <option value="{{ $kec->slug }}" @selected($kecamatanSlug === $kec->slug)>{{ $kec->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="news-filter-btn"><i data-lucide="filter" class="w-3.5 h-3.5"></i> Terapkan</button>
        @if($q || $categorySlug || $kecamatanSlug)
            <a href="{{ route('news.index') }}" class="news-filter-reset"><i data-lucide="x" class="w-3.5 h-3.5"></i> Reset</a>
        @endif
    </form>

    @if($posts->count() > 0)
        <p class="text-xs text-gray-500 mb-2">{{ number_format($posts->total()) }} berita ditemukan</p>
        <div class="news-list">
            @foreach($posts as $post)
            <article class="news-item group">
                <a href="{{ route('posts.show', $post->slug) }}" class="news-item-thumb" @if($post->isVideo()) data-video-player="{{ videoPlayerData($post) }}" @endif>
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                    @if($post->isVideo())
                    <div class="news-item-play"><i data-lucide="play" class="w-3 h-3 text-primary ml-0.5"></i></div>
                    @endif
                </a>
                <div class="news-item-body">
                    <div class="news-item-meta">
                        @if($post->isVideo())
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-accent/10 text-accent text-[9px] md:text-[10px] font-bold uppercase tracking-wide">Video</span>
                        @elseif($post->type === 'opini')
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-accent/10 text-accent text-[9px] md:text-[10px] font-bold uppercase tracking-wide">Opini</span>
                        @endif
                        @if($post->categories->count() > 0)
                            @foreach($post->categories as $cat)
                                <a href="{{ route('categories.show', $cat->slug) }}" class="news-item-cat">{{ $cat->name }}</a>
                                @if(! $loop->last) <span>/</span> @endif
                            @endforeach
                        @elseif($post->category)
                            <a href="{{ route('categories.show', $post->category->slug) }}" class="news-item-cat">{{ $post->category->name }}</a>
                        @endif
                        @if($post->kecamatan)
                        <a href="{{ route('kecamatan.show', $post->kecamatan->slug) }}" class="inline-flex items-center gap-0.5 text-[9px] md:text-[10px] font-semibold text-secondary">
                            <i data-lucide="map-pin" class="w-2.5 h-2.5"></i>{{ $post->kecamatan->name }}
                        </a>
                        @endif
                        <span>{{ \Carbon\Carbon::parse($post->published_at)->diffForHumans() }}</span>
                    </div>
                    <h2 class="news-item-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h2>
                    <p class="news-item-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 110) }}</p>
                    <div class="news-item-stats">
                        <span><i data-lucide="eye" class="w-2.5 h-2.5"></i>{{ number_format($post->views_count) }} dibaca</span>
                        <span><i data-lucide="heart" class="w-2.5 h-2.5"></i>{{ number_format($post->likes_count) }}</span>
                        <span><i data-lucide="message-square" class="w-2.5 h-2.5"></i>{{ number_format($post->comments_count) }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @else
        <div class="rounded-xl border border-outline bg-surface p-10 text-center mt-2">
            <i data-lucide="search-x" class="w-10 h-10 mx-auto text-gray-400"></i>
            <p class="mt-3 font-semibold">Tidak ada berita ditemukan</p>
            <p class="text-sm text-gray-500">Coba ubah kata kunci atau reset filter.</p>
        </div>
    @endif
@endsection
