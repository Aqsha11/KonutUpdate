@extends('frontend.layouts.app')

@section('title', $page->title . ' - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $pageDesc = strip_tags(Str::limit($page->content, 160));
    @endphp
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ route('pages.show', $page->slug) }}" />
    <meta property="og:title" content="{{ $page->title }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $pageDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('pages.show', $page->slug) }}" />
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $page->title }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $pageDesc }}" />
    @if(!empty($site_settings['logo']))
        <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
@endsection

@push('styles')
<style>
    .page-content h1 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: var(--color-on-surface);
        line-height: 1.3;
    }
    .page-content h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: var(--color-on-surface);
        line-height: 1.4;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--color-primary);
        display: inline-block;
    }
    .page-content h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--color-on-surface);
        line-height: 1.5;
    }
    .page-content h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--color-on-surface);
    }
    .page-content p {
        font-size: 0.9375rem;
        line-height: 1.8;
        margin-bottom: 1rem;
        color: var(--color-on-surface-variant);
    }
    .page-content a {
        color: var(--color-primary);
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .page-content a:hover {
        color: var(--color-primary-hover);
    }
    .page-content ul,
    .page-content ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .page-content ul {
        list-style-type: disc;
    }
    .page-content ol {
        list-style-type: decimal;
    }
    .page-content li {
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
        line-height: 1.7;
        color: var(--color-on-surface-variant);
    }
    .page-content img {
        border-radius: 0.75rem;
        margin: 1.5rem 0;
        max-width: 100%;
        height: auto;
    }
    .page-content blockquote {
        border-left: 4px solid var(--color-primary);
        padding: 1rem 1.25rem;
        font-style: italic;
        margin: 1.5rem 0;
        background: var(--color-surface-container);
        border-radius: 0 0.75rem 0.75rem 0;
        color: var(--color-on-surface-variant);
    }
    .page-content hr {
        border: none;
        border-top: 1px solid var(--color-outline);
        margin: 2rem 0;
    }
    .page-content strong {
        color: var(--color-on-surface);
        font-weight: 600;
    }
    .page-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        font-size: 0.875rem;
    }
    .page-content th,
    .page-content td {
        padding: 0.75rem 1rem;
        border: 1px solid var(--color-outline);
        text-align: left;
    }
    .page-content th {
        background: var(--color-surface-container);
        font-weight: 600;
        color: var(--color-on-surface);
    }
    .page-content td {
        color: var(--color-on-surface-variant);
    }
</style>
@endpush

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">{{ $page->title }}</span>
        </nav>
    </div>

    {{-- Hero --}}
    @php
        $heroIcons = [
            'tentang-kami' => 'users',
            'privacy-policy' => 'shield',
            'pedoman-media-siber' => 'book-open',
            'kontak' => 'send',
        ];
        $heroIcon = $heroIcons[$page->slug] ?? 'file-text';
        $heroDesc = strip_tags(Str::limit($page->content, 120));
    @endphp
    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-8 py-12 md:px-12 md:py-16 text-center">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="{{ $heroIcon }}" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">{{ $page->title }}</h1>
            <p class="text-white/80 text-lg max-w-xl mx-auto">{{ $heroDesc }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8 lg:p-10 page-content">
                {!! $page->content !!}
            </div>
        </div>
        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
