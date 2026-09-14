<!DOCTYPE html>
<html class="light" lang="id" x-data="{ mobileOpen: false, searchOpen: false, theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => { document.documentElement.className = val; localStorage.setItem('theme', val); })" :class="theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#189B39" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0F172A" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KonutUpdate">
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    @if(!empty($site_settings['favicon']))
        <link rel="icon" type="image/png" href="{{ Storage::url($site_settings['favicon']) }}">
    @else
        <link rel="icon" type="image/png" href="{{ url('/icons/favicon.png') }}">
    @endif
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/icons/icon-180.png') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $site_settings['site_name'] ?? 'KonutUpdate' }} RSS Feed" href="{{ url('/feed') }}">
    <title>@yield('title', ($site_settings['site_name'] ?? 'KonutUpdate'))</title>
    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}">
        <meta name="keywords" content="{{ $site_settings['meta_keywords'] ?? 'konut, konawe utara, berita, news, informasi, sulawesi tenggara' }}">
        <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'KonutUpdate' }}" />
        <meta property="og:locale" content="id_ID" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:title" content="@yield('title', ($site_settings['site_name'] ?? 'KonutUpdate'))" />
        <meta property="og:description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}" />
        @if(!empty($site_settings['logo']))
            <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta property="og:image:alt" content="{{ $site_settings['site_name'] ?? 'KonutUpdate' }}" />
        @endif
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('title', ($site_settings['site_name'] ?? 'KonutUpdate'))" />
        <meta name="twitter:description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}" />
        @if(!empty($site_settings['logo']))
            <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        @endif
    @endif
    @php
        // Canonical self-referencing; arsip terpaginasi tetap mengarah ke URL halamannya sendiri (?page=N)
        $__canonical = url()->current();
        if (($page = (int) request()->query('page', 1)) > 1) {
            $__canonical .= '?page=' . $page;
        }
    @endphp
    <link rel="canonical" href="@yield('canonical', $__canonical)" />
    <meta name="robots" content="max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @include('frontend.partials.schema-org')
    @php
        // Normalisasi nilai Google Site Verification bila disimpan bersama prefix (mis. "google-site-verification=xxx")
        $__gsv = trim((string) ($site_settings['google_site_verification'] ?? ''));
        $__gsv = (string) preg_replace('/^(?:google-site-verification|content)\s*=\s*/i', '', $__gsv);
    @endphp
    @if($__gsv !== '')
        <meta name="google-site-verification" content="{{ $__gsv }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(!empty($site_settings['primary_color']) || !empty($site_settings['accent_color']))
    <style>
        :root {
            @if(!empty($site_settings['primary_color']))
            --color-primary: {{ $site_settings['primary_color'] }};
            --color-primary-hover: {{ $site_settings['primary_color'] }}dd;
            --color-primary-light: {{ $site_settings['primary_color'] }}1a;
            --color-primary-container: {{ $site_settings['primary_color'] }}33;
            --color-inverse-primary: {{ $site_settings['primary_color'] }}99;
            --color-primary-fixed: {{ $site_settings['primary_color'] }}33;
            --color-primary-fixed-dim: {{ $site_settings['primary_color'] }}66;
            @endif
            @if(!empty($site_settings['accent_color']))
            --color-accent: {{ $site_settings['accent_color'] }};
            --color-accent-hover: {{ $site_settings['accent_color'] }}dd;
            --color-accent-light: {{ $site_settings['accent_color'] }}1a;
            --color-accent-container: {{ $site_settings['accent_color'] }}33;
            --color-tertiary: {{ $site_settings['accent_color'] }};
            --color-accent-fixed: {{ $site_settings['accent_color'] }}33;
            --color-accent-fixed-dim: {{ $site_settings['accent_color'] }}66;
            --color-tertiary-container: {{ $site_settings['accent_color'] }}33;
            @endif
        }
    </style>
    @endif
    @stack('styles')
    @if(!empty($site_settings['header_script']))
        {!! $site_settings['header_script'] !!}
    @endif
</head>
<body class="antialiased min-h-screen flex flex-col">

    {{-- Reading Progress Bar --}}
    <div class="reading-progress">
        <div class="reading-progress-bar" id="readingProgressBar"></div>
    </div>

    {{-- Offline Banner --}}
    <div class="offline-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.56 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
        Koneksi terputus. Beberapa fitur mungkin tidak tersedia.
    </div>

    {{-- Toast Container --}}
    <div id="toastContainer" class="toast-container"></div>

    {{-- Search Overlay --}}
    <div class="search-overlay" :class="{ 'active': searchOpen }" x-on:click.self="searchOpen = false">
        <div class="search-box relative">
            <i data-lucide="search" class="w-5 h-5 text-on-surface-variant shrink-0"></i>
            <input type="text" id="liveSearchInput" placeholder="Cari berita..." autocomplete="off" x-ref="searchInput">
            <button class="flex items-center justify-center p-2 text-on-surface-variant hover:text-on-surface cursor-pointer bg-transparent border-none shrink-0" x-on:click="searchOpen = false">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div id="liveSearchResults" class="live-search-results"></div>
        </div>
    </div>

    {{-- HEADER --}}
    <header id="mainHeader" class="sticky-header bg-surface border-b border-outline">

        {{-- Top Bar — visible on all screens --}}
        <div class="flex bg-primary text-white text-[10px] lg:text-[11px] py-1 lg:py-1.5 overflow-x-auto topbar-scroll">
            <div class="max-w-7xl mx-auto px-3 lg:px-4 w-full flex flex-nowrap items-center justify-between gap-3 lg:gap-6">
                <div class="flex items-center gap-3 lg:gap-4 shrink-0">
                    <span class="flex items-center gap-1.5 opacity-90 whitespace-nowrap" id="currentDate">
                        <i data-lucide="calendar" class="w-2.5 h-2.5 lg:w-3 lg:h-3"></i>
                        <span></span>
                    </span>
                    <span class="opacity-30 hidden sm:inline">|</span>
                    <span class="opacity-70 hidden sm:inline whitespace-nowrap">Portal Berita Konawe Utara</span>
                </div>
                <div class="flex items-center gap-3 lg:gap-4 shrink-0">
                    <div class="weather-widget" id="weatherWidget">
                        <i data-lucide="sun" class="w-2.5 h-2.5 lg:w-3 lg:h-3"></i>
                        <span id="weatherTemp">--°C</span>
                        <span class="opacity-60 hidden sm:inline" id="weatherCity">Konawe Utara</span>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-2.5 pl-3 lg:pl-4 border-l border-white/20">
                        @if(!empty($site_settings['facebook']))
                            <a href="{{ $site_settings['facebook'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors" title="Facebook"><i class="fab fa-facebook text-[11px] lg:text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['instagram']))
                            <a href="{{ $site_settings['instagram'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors" title="Instagram"><i class="fab fa-instagram text-[11px] lg:text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['youtube']))
                            <a href="{{ $site_settings['youtube'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors" title="YouTube"><i class="fab fa-youtube text-[11px] lg:text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['tiktok']))
                            <a href="{{ $site_settings['tiktok'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors" title="TikTok"><i class="fab fa-tiktok text-[11px] lg:text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['whatsapp']))
                            <a href="{{ $site_settings['whatsapp'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors" title="WhatsApp"><i class="fab fa-whatsapp text-[11px] lg:text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['email']))
                            <a href="mailto:{{ $site_settings['email'] }}" class="text-white/70 hover:text-white no-underline transition-colors" title="Email"><i data-lucide="mail" class="w-2.5 h-2.5 lg:w-3.5 lg:h-3.5 inline-block"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Header ala KendariInfo: hamburger kiri, logo tengah, search/theme kanan --}}
        <div class="ku-header-wrapper bg-surface border-b border-outline">
            <div class="max-w-7xl mx-auto px-3 lg:px-4 h-14 lg:h-20 flex items-center justify-between relative">
                <div class="ku-header-side ku-header-left">
                    <button class="ku-hamburger" x-on:click="mobileOpen = true" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                </div>
                <div class="ku-header-logo">
                    <a href="{{ url('/') }}" class="ku-header-logo-link">
                        @if(!empty($site_settings['logo']))
                            <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'KonutUpdate' }}" class="ku-header-logo-img">
                        @else
                            <span class="ku-header-logo-text">
                                <span class="text-primary">KONUT</span><span class="text-accent">UPDATE</span>
                            </span>
                        @endif
                    </a>
                </div>
                <div class="ku-header-side ku-header-right">
                    <button class="ku-header-icon ku-header-icon-search" x-on:click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())" aria-label="Cari">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <button class="ku-header-icon theme-toggle" x-on:click="theme = theme === 'dark' ? 'light' : 'dark'" aria-label="Toggle theme">
                        <span x-show="theme === 'dark'">
                            <i data-lucide="sun" class="w-5 h-5"></i>
                        </span>
                        <span x-show="theme !== 'dark'">
                            <i data-lucide="moon" class="w-5 h-5"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Nav mobile: scroll horizontal ala KendariInfo --}}
        <nav class="ku-nav bg-surface border-b border-outline" x-data="navMenuArrows()">
            <div class="max-w-7xl mx-auto px-2 lg:px-4 relative">
                <button type="button" class="ku-nav-arrow ku-nav-arrow-left" x-show="canLeft" x-cloak x-on:click="scrollMenu(-180)" aria-label="Geser ke kiri">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                </button>
                <div class="ku-nav-menu hide-scrollbar" x-ref="navMenu" x-on:scroll="updateArrows()">
                    <a href="{{ route('opini') }}" class="ku-nav-item {{ request()->routeIs('opini') ? 'active' : '' }}">
                        Opini
                    </a>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $cat)
                            <a href="{{ route('categories.show', $cat->slug) }}" class="ku-nav-item {{ request()->routeIs('categories.show') && request()->slug == $cat->slug ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="ku-nav-arrow ku-nav-arrow-right" x-show="canRight" x-cloak x-on:click="scrollMenu(180)" aria-label="Geser ke kanan">
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </nav>


    </header>

    {{-- Breaking News Ticker --}}
    @include('frontend.partials.breaking-news')

    {{-- Main Content --}}
    <main class="max-w-7xl lg:mx-auto px-3 lg:px-4 py-4 lg:py-8 flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    {{-- Scroll to Top --}}
    <button id="scrollTopBtn" x-data="scrollToTop" x-show="showScroll" x-cloak x-on:click="scrollTop" aria-label="Scroll to top">
        <i data-lucide="arrow-up" class="w-5 h-5"></i>
    </button>

    {{-- Bubble Widget Akun --}}
    @auth
        @php
            $accountRole = auth()->user()->role?->slug;
            $accountUrl = match ($accountRole) {
                'kontributor' => route('kontributor.dashboard'),
                'super_admin', 'editor', 'reporter' => route('admin.dashboard'),
                default => route('home'),
            };
        @endphp
        <a href="{{ $accountUrl }}" class="ku-bubble-widget" aria-label="Akun Saya">
            <i data-lucide="user-round" class="w-5 h-5"></i>
            <span class="ku-bubble-label">{{ auth()->user()->name }}</span>
        </a>
        <form id="frontend-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        <button type="button" class="ku-bubble-widget ku-bubble-logout" aria-label="Keluar" onclick="event.preventDefault(); document.getElementById('frontend-logout-form').submit();">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span class="ku-bubble-label">Keluar</span>
        </button>
    @else
        <a href="{{ route('login') }}" class="ku-bubble-widget" aria-label="Masuk atau Daftar">
            <i data-lucide="user-round" class="w-5 h-5"></i>
            <span class="ku-bubble-label">Masuk / Daftar</span>
        </a>
    @endauth

    {{-- Mobile Bottom Navigation --}}
    <nav class="mobile-bottom-nav md:hidden safe-bottom">
        <div class="flex items-center justify-around max-w-lg mx-auto">
            <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline min-w-0 {{ request()->routeIs('home') ? 'text-primary' : 'text-on-surface-variant' }}" aria-label="Home">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Home</span>
            </a>
            <button class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline text-on-surface-variant cursor-pointer bg-transparent border-none min-w-0" x-on:click="searchOpen = true" aria-label="Cari">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Cari</span>
            </button>
            <a href="{{ route('terkini') }}" class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline min-w-0 {{ request()->routeIs('terkini') ? 'text-primary' : 'text-on-surface-variant' }}" aria-label="Terkini">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Terkini</span>
            </a>
            <a href="{{ route('trending') }}" class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline min-w-0 {{ request()->routeIs('trending') ? 'text-primary' : 'text-on-surface-variant' }}" aria-label="Trending">
                <i data-lucide="flame" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Trending</span>
            </a>
            <button class="flex flex-col items-center gap-0.5 py-1.5 px-3 no-underline text-on-surface-variant cursor-pointer bg-transparent border-none min-w-0" x-on:click="mobileOpen = true" aria-label="Menu">
                <i data-lucide="align-left" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Menu</span>
            </button>
        </div>
    </nav>

    {{-- Share Popup ala KendariInfo --}}
    <div id="sharePopup" class="ku-share-popup" role="dialog" aria-modal="true" aria-label="Bagikan">
        <div class="ku-share-backdrop" x-on:click="document.getElementById('sharePopup').classList.remove('open')"></div>
        <div class="ku-share-sheet">
            <div class="ku-share-handle"></div>
            <div class="ku-share-header">
                <span class="ku-share-title">Bagikan Berita</span>
                <button class="ku-share-close" x-on:click="document.getElementById('sharePopup').classList.remove('open')" aria-label="Tutup">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="ku-share-body">
                <a class="ku-share-opt" id="shareWa" href="#" target="_blank" rel="noopener">
                    <span class="ku-share-icon ku-share-wa"><svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></span>
                    WhatsApp
                </a>
                <a class="ku-share-opt" id="shareFb" href="#" target="_blank" rel="noopener">
                    <span class="ku-share-icon ku-share-fb"><svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
                    Facebook
                </a>
                <a class="ku-share-opt" id="shareTg" href="#" target="_blank" rel="noopener">
                    <span class="ku-share-icon ku-share-tg"><svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></span>
                    Telegram
                </a>
                <a class="ku-share-opt" id="shareTw" href="#" target="_blank" rel="noopener">
                    <span class="ku-share-icon ku-share-tw"><svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></span>
                    X
                </a>
                <button class="ku-share-opt" id="shareCopy" x-on:click="document.getElementById('sharePopup').classList.remove('open')">
                    <span class="ku-share-icon ku-share-copy"><i data-lucide="link" class="w-5 h-5"></i></span>
                    Salin Link
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Offcanvas Menu --}}
    <template x-teleport="body">
        <div>
            <div class="fixed inset-0 bg-black/50 z-50 transition-opacity" x-show="mobileOpen" x-on:click="mobileOpen = false" x-cloak style="z-index: 105;"></div>
            <div class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-surface transform transition-transform duration-300 shadow-2xl overflow-y-auto" x-ref="mobileDrawer" x-effect="if (mobileOpen) { const el = $refs.mobileDrawer.querySelector('.drawer-active'); if (el) { $nextTick(() => { const drawer = $refs.mobileDrawer; drawer.scrollTop = Math.max(0, el.offsetTop - drawer.clientHeight / 2 + el.offsetHeight / 2); }); } }" x-show="mobileOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" x-cloak @click.away="mobileOpen = false" style="z-index: 106;">
                <div class="relative flex items-center justify-center px-4 py-4 border-b border-outline bg-surface-container-low">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="KonutUpdate" class="h-12 w-auto object-contain">
                    @else
                        <span class="text-xl font-extrabold"><span class="text-primary">KONUT</span><span class="text-accent">UPDATE</span></span>
                    @endif
                    <button class="absolute right-3 flex items-center justify-center w-10 h-10 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer bg-transparent border-none" x-on:click="mobileOpen = false" aria-label="Tutup">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-3">
                    <button type="button" x-on:click="mobileOpen = false; searchOpen = true; $nextTick(() => $refs.searchInput?.focus())" class="w-full flex items-center gap-2.5 bg-surface-container-low rounded-xl px-4 py-3 text-sm text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer border-none">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span class="flex-1 text-left">Cari berita...</span>
                    </button>
                </div>

                <div class="px-3 pb-4 space-y-1">
                    <div>
                        <p class="text-xs font-semibold text-on-surface-variant px-3 mb-2 uppercase tracking-wider">Kategori</p>
                        @foreach($categories as $cat)
                            <a href="{{ route('categories.show', $cat->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('categories.show') && request()->slug == $cat->slug ? 'drawer-active text-primary bg-primary-light font-bold' : 'text-on-surface hover:bg-surface-container' }} no-underline transition-colors">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-on-surface-variant"></i>
                                <span>{{ $cat->name }}</span>
                                @if(isset($cat->all_posts_count) ? $cat->all_posts_count > 0 : $cat->posts_count > 0)
                                    <span class="ml-auto text-xs text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded-full">{{ $cat->all_posts_count ?? $cat->posts_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-4 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant px-3 mb-2 uppercase tracking-wider">Kecamatan</p>
                        @if(isset($kecamatans) && $kecamatans->count() > 0)
                            @foreach($kecamatans as $kec)
                                <a href="{{ route('kecamatan.show', $kec->slug) }}"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('kecamatan.show') && request()->slug == $kec->slug ? 'drawer-active text-primary bg-primary-light font-bold' : 'text-on-surface hover:bg-surface-container' }} no-underline transition-colors">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-on-surface-variant"></i>
                                    <span>{{ $kec->name }}</span>
                                    @if(isset($kec->posts_count) && $kec->posts_count > 0)
                                        <span class="ml-auto text-xs text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded-full">{{ $kec->posts_count }}</span>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    </div>

                    <div class="pt-4 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant mb-2 uppercase tracking-wider">Halaman</p>
                        @forelse($footerPages as $page)
                            @php
                                $pageIcons = [
                                    'tentang-kami' => 'info',
                                    'kontak' => 'mail',
                                    'pedoman-media-siber' => 'scroll-text',
                                    'privacy-policy' => 'shield',
                                    'pasang-iklan' => 'megaphone',
                                    'info-iklan' => 'megaphone',
                                ];
                                $pageIcon = $pageIcons[$page->slug] ?? 'file-text';
                            @endphp
                            <a href="{{ route('pages.show', $page->slug) }}"
                               class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm text-on-surface hover:bg-surface-container no-underline transition-colors">
                                <i data-lucide="{{ $pageIcon }}" class="w-4 h-4 text-on-surface-variant"></i>
                                {{ $page->title }}
                            </a>
                        @empty
                            <p class="text-sm text-on-surface-variant px-3 py-2">Belum ada halaman.</p>
                        @endforelse
                    </div>

                    <div class="pt-4 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant mb-2 uppercase tracking-wider">Ikuti Kami</p>
                        <div class="flex gap-2">
                            @if(!empty($site_settings['facebook']))
                                <a href="{{ $site_settings['facebook'] }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#1877f2] text-white no-underline"><i class="fab fa-facebook text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['instagram']))
                                <a href="{{ $site_settings['instagram'] }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-tr from-[#833ab4] via-[#fd1d1d] to-[#f77737] text-white no-underline"><i class="fab fa-instagram text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['youtube']))
                                <a href="{{ $site_settings['youtube'] }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#ff0000] text-white no-underline"><i class="fab fa-youtube text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['tiktok']))
                                <a href="{{ $site_settings['tiktok'] }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-black text-white no-underline"><i class="fab fa-tiktok text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['whatsapp']))
                                <a href="{{ $site_settings['whatsapp'] }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#25d366] text-white no-underline"><i class="fab fa-whatsapp text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['email']))
                                <a href="mailto:{{ $site_settings['email'] }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-on-surface text-white no-underline"><i data-lucide="mail" class="w-4 h-4"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        (function() {
            function getAlpineData() {
                try { return document.documentElement._x_dataStack?.[0]; } catch (e) { return null; }
            }

            // Keyboard shortcut
            document.addEventListener('keydown', function(e) {
                const data = getAlpineData();
                if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
                    e.preventDefault();
                    if (data) data.searchOpen = true;
                    setTimeout(() => document.getElementById('liveSearchInput')?.focus(), 100);
                }
                if (e.key === 'Escape') {
                    if (data) data.searchOpen = false;
                }
            });

            // Live Search
            const input = document.getElementById('liveSearchInput');
            const results = document.getElementById('liveSearchResults');
            if (input) {
                let timer;
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    const q = this.value.trim();
                    if (q.length < 2) { results.classList.remove('active'); return; }
                    timer = setTimeout(() => {
                        fetch('{{ route("search") }}?q=' + encodeURIComponent(q) + '&ajax=1')
                            .then(r => r.json())
                            .then(data => {
                                results.innerHTML = '';
                                if (data.length > 0) {
                                    data.forEach(p => {
                                        const a = document.createElement('a'); a.href = p.url;
                                        a.innerHTML = '<img src="' + (p.thumb || 'https://placehold.co/48x48/e9ecef/6b7280?text=N') + '" alt="" loading="lazy">' +
                                            '<div><div class="result-title">' + p.title + '</div><div class="result-meta">' + (p.category || '') + ' &bull; ' + p.date + '</div></div>';
                                        results.appendChild(a);
                                    });
                                } else {
                                    results.innerHTML = '<div class="p-4 text-center text-sm text-on-surface-variant">Tidak ditemukan</div>';
                                }
                                results.classList.add('active');
                            });
                    }, 350);
                });
                input.addEventListener('blur', () => setTimeout(() => results.classList.remove('active'), 200), { passive: true });
            }

            // Date
            const dateEl = document.getElementById('currentDate');
            if (dateEl) {
                const now = new Date();
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                dateEl.querySelector('span').textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            }

            // Weather
            const tempEl = document.getElementById('weatherTemp');
            const cityEl = document.getElementById('weatherCity');
            const widget = document.getElementById('weatherWidget');
            if (tempEl) {
                fetch('https://api.open-meteo.com/v1/forecast?latitude=-3.4&longitude=122.0&current=temperature_2m,weather_code&daily=temperature_2m_max,temperature_2m_min,weather_code&timezone=auto&forecast_days=3')
                    .then(r => r.json())
                    .then(d => {
                        var temp = Math.round(d.current.temperature_2m);
                        var code = d.current.weather_code;
                        var descs = { 0:'Cerah', 1:'Cerah', 2:'Berawan', 3:'Berawan', 45:'Kabut', 48:'Kabut', 51:'Gerimis', 53:'Gerimis', 55:'Gerimis', 61:'Hujan', 63:'Hujan', 65:'Hujan', 71:'Salju', 73:'Salju', 75:'Salju', 80:'Hujan', 81:'Hujan', 82:'Hujan', 95:'Badai', 96:'Badai', 99:'Badai' };
                        var desc = descs[code] || 'Cerah';
                        tempEl.textContent = temp + '°C';
                        if (cityEl) cityEl.textContent = 'Konawe Utara - ' + desc;
                        var sunIcon = widget ? widget.querySelector('[data-lucide]') : null;
                        if (sunIcon) {
                            var icons = { 'Cerah':'sun', 'Berawan':'cloud', 'Kabut':'cloud-fog', 'Gerimis':'cloud-drizzle', 'Hujan':'cloud-rain', 'Salju':'cloud-snow', 'Badai':'cloud-lightning' };
                            var icon = icons[desc] || 'sun';
                            sunIcon.setAttribute('data-lucide', icon);
                            if (window.__lucideCreateIcons) window.__lucideCreateIcons({ icons: window.__lucideIcons });
                        }
                    })
                    .catch(function() { tempEl.textContent = '--°C'; if (cityEl) cityEl.textContent = 'Konut'; });
            }

            // Sticky header: topbar & navigasi selalu terlihat saat scroll
            const header = document.getElementById('mainHeader');
            if (header) header.classList.remove('hidden-header');
        })();

        // Like Toggle
        function toggleLike(postId) {
            fetch('/berita/' + postId + '/like', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                var btn = document.getElementById('like-btn-' + postId);
                var countEl = document.getElementById('like-count-' + postId);
                if (btn) {
                    if (data.liked) { btn.classList.add('liked'); } else { btn.classList.remove('liked'); }
                }
                if (countEl) countEl.textContent = data.count;
                document.querySelectorAll('[data-like-btn="' + postId + '"]').forEach(function(el) {
                    if (data.liked) { el.classList.add('liked'); } else { el.classList.remove('liked'); }
                });
                document.querySelectorAll('[data-like-count="' + postId + '"]').forEach(function(el) {
                    el.textContent = data.count;
                });
            })
            .catch(() => {});
        }

        // Share — popup ala KendariInfo
        function sharePost(url, title) {
            if (!url) return;
            var encTitle = encodeURIComponent(title || 'KonutUpdate');
            var encUrl = encodeURIComponent(url);
            document.getElementById('shareWa').href = 'https://wa.me/?text=' + encTitle + '%20' + encUrl;
            document.getElementById('shareFb').href = 'https://www.facebook.com/sharer/sharer.php?u=' + encUrl;
            document.getElementById('shareTg').href = 'https://t.me/share/url?url=' + encUrl + '&text=' + encTitle;
            document.getElementById('shareTw').href = 'https://twitter.com/intent/tweet?text=' + encTitle + '&url=' + encUrl;
            document.getElementById('sharePopup').classList.add('open');
        }
        document.getElementById('shareCopy').addEventListener('click', function() {
            var url = new URL(location.href);
            var href = document.getElementById('shareWa').getAttribute('href');
            var m = href && href.match(/url=([^&]+)/);
            var link = m ? decodeURIComponent(m[1]) : url.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(function() {
                    document.getElementById('shareCopy').textContent = 'Tersalin!';
                    setTimeout(function() { document.getElementById('shareCopy').textContent = 'Salin Link'; }, 1500);
                });
            }
        });

        // Viewed Posts
        (function() {
            const STORAGE_KEY = 'konut_viewed_posts';
            let viewed = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            document.querySelectorAll('[data-post-id]').forEach(function(el) {
                if (viewed.includes(el.dataset.postId)) {
                    el.classList.add('post-viewed');
                }
            });
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a[href*="/berita/"]');
                if (!link) return;
                var article = link.closest('[data-post-id]');
                if (!article) return;
                var id = article.dataset.postId;
                if (!viewed.includes(id)) {
                    viewed.push(id);
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(viewed));
                    article.classList.add('post-viewed');
                }
            }, true);
        })();

        // PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
            window.addEventListener('online', () => document.body.classList.remove('is-offline'));
            window.addEventListener('offline', () => document.body.classList.add('is-offline'));
        }
    </script>
    @stack('scripts')
    @include('frontend.partials.video-player')
    @if(!empty($site_settings['footer_script']))
        {!! $site_settings['footer_script'] !!}
    @endif
</body>
</html>