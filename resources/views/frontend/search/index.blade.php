@extends('frontend.layouts.app')

@section('title', 'Semua Berita - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface">Berita</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface flex items-center gap-2">
            @if($query)
                <span class="w-9 h-9 rounded-xl bg-primary-light flex items-center justify-center">
                    <i data-lucide="search" class="w-5 h-5 text-primary"></i>
                </span>
                Hasil Pencarian
            @else
                <span class="w-9 h-9 rounded-xl bg-primary-light flex items-center justify-center">
                    <i data-lucide="newspaper" class="w-5 h-5 text-primary"></i>
                </span>
                Semua Berita
            @endif
        </h1>
        @if($query)
            <p class="text-on-surface-variant text-sm mt-1.5">Menampilkan hasil untuk: <strong>"{{ e($query) }}"</strong></p>
        @else
            <p class="text-on-surface-variant text-sm mt-1.5">Berita terbaru dari Konut.Update</p>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            @if(isset($posts) && $posts->count() > 0)
                <div class="space-y-4">
                    @foreach($posts as $post)
                        <article class="flex flex-col sm:flex-row gap-5 bg-surface rounded-2xl p-4 card-hover border border-outline/50 group">
                            <a href="{{ route('posts.show', $post->slug) }}" class="sm:w-[200px] shrink-0">
                                <div class="aspect-video sm:aspect-[4/3] rounded-xl overflow-hidden img-zoom bg-surface-container-low relative">
                                    <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/400x250/1a1a2e/ffffff?text=VIDEO') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                    @if($post->type === 'video')
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                            <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                                                <i data-lucide="play" class="w-5 h-5 text-primary ml-0.5"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            <div class="flex-1 flex flex-col justify-center min-w-0">
                                @if($post->category)
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="text-[10px] font-bold text-primary uppercase tracking-wider no-underline hover:underline">{{ $post->category->name }}</a>
                                @endif
                                <h3 class="text-base md:text-lg font-bold text-on-surface mt-1 leading-snug">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="no-underline text-on-surface hover:text-primary transition-colors">
                                        @if($post->type === 'video')
                                            <span class="inline-flex items-center gap-1 text-accent mr-1"><i data-lucide="play-circle" class="w-4 h-4"></i></span>
                                        @endif
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">{{ $post->excerpt ? strip_tags($post->excerpt) : '' }}</p>
                                <div class="flex items-center justify-between text-xs text-on-surface-variant mt-2">
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
                    <i data-lucide="search-x" class="w-10 h-10 mb-3 mx-auto"></i>
                    @if($query)
                        <p class="mt-2 font-medium">Tidak ditemukan berita dengan kata kunci <strong>"{{ e($query) }}"</strong>.</p>
                        <p class="text-sm mt-1 text-on-surface-variant">Coba gunakan kata kunci lain yang lebih umum.</p>
                    @else
                        <p class="mt-2 font-medium">Belum ada berita.</p>
                    @endif
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
