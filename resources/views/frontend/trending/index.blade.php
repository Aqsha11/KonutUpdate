@extends('frontend.layouts.app')

@section('title', 'Trending - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Berita trending terpopuler di {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface">Trending</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-accent-light flex items-center justify-center">
                <i data-lucide="flame" class="w-5 h-5 text-accent"></i>
            </span>
            Trending
        </h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Berita paling banyak dilihat</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
        <div class="lg:w-[68%]">
            @if($posts->count() > 0)
                <div class="space-y-3">
                    @foreach($posts as $index => $post)
                        <a href="{{ route('posts.show', $post->slug) }}" class="relative flex gap-3 bg-surface rounded-xl p-3 card-hover border border-outline/50 no-underline group overflow-hidden">
                            {{-- Nomor urut besar transparan --}}
                            <div class="absolute -left-2 -top-3 text-[80px] font-black leading-none select-none pointer-events-none
                                {{ $index < 3 ? 'text-primary/10' : 'text-on-surface/5' }}">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden shrink-0 bg-surface-container-low relative z-10">
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : ($post->video_poster ?? 'https://placehold.co/80x80/e9ecef/6b7280?text=N') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="lazy">
                                @if($post->isVideo())
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                        <div class="w-7 h-7 rounded-full bg-white/90 flex items-center justify-center">
                                            <i data-lucide="play" class="w-3.5 h-3.5 text-primary ml-0.5"></i>
                                        </div>
                                    </div>
                                @endif
                                {{-- Badge nomor kecil --}}
                                <div class="absolute -top-0.5 -left-0.5 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow
                                    {{ $index == 0 ? 'bg-accent' : ($index == 1 ? 'bg-primary' : ($index == 2 ? 'bg-secondary' : 'bg-on-surface-variant')) }}">
                                    {{ $index + 1 }}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0 z-10">
                                <h4 class="text-sm font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                                    @if($post->isVideo())
                                        <i data-lucide="play-circle" class="w-3.5 h-3.5 text-accent inline align-text-top mr-0.5"></i>
                                    @endif
                                    {{ $post->title }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1.5 text-xs text-on-surface-variant">
                                    @if($post->categories->count() > 0)
                                        <span>{{ $post->categories->first()->name }}</span>
                                    @elseif($post->category)
                                        <span>{{ $post->category->name }}</span>
                                    @endif
                                    <span class="w-1 h-1 rounded-full bg-on-surface-variant"></span>
                                    <span>{{ formatDate($post->published_at) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">{{ $posts->links('vendor.pagination.tailwind') }}</div>
            @else
                <div class="bg-surface rounded-2xl p-10 text-center text-on-surface-variant border border-outline">
                    <i data-lucide="flame" class="w-10 h-10 mb-3 mx-auto"></i>
                    <p class="mt-2 font-medium">Belum ada berita trending.</p>
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
