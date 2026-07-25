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
    <meta property="og:locale" content="id_ID" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $page->title }} - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $pageDesc }}" />
@endsection

@push('styles')
<style>
    .page-content h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2.5rem; margin-bottom: 1rem; color: var(--color-on-surface); }
    .page-content h3 { font-size: 1.25rem; font-weight: 700; margin-top: 2rem; margin-bottom: 0.75rem; color: var(--color-on-surface); }
    .page-content p { line-height: 1.8; margin-bottom: 1rem; }
    .page-content a { color: var(--color-primary); text-decoration: underline; }
    .page-content a:hover { color: var(--color-primary-hover); }
    .page-content ul, .page-content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
    .page-content ul { list-style-type: disc; }
    .page-content ol { list-style-type: decimal; }
    .page-content li { margin-bottom: 0.25rem; }
    .page-content img { border-radius: 0.75rem; margin: 1rem 0; max-width: 100%; height: auto; }
    .page-content blockquote { border-left: 4px solid var(--color-primary); padding-left: 1rem; font-style: italic; margin-bottom: 1rem; }
    .page-content hr { border: none; border-top: 1px solid var(--color-outline); margin: 2rem 0; }
</style>
@endpush

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">{{ $page->title }}</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">{{ $page->title }}</h1>
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
