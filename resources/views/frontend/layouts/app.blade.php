<!DOCTYPE html>
<html class="light" lang="id" x-data="{ mobileOpen: false, searchOpen: false, theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => { document.documentElement.className = val; localStorage.setItem('theme', val); })" :class="theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#189B39" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0F172A" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Konut.Update">
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ url('/icons/icon.svg') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $site_settings['site_name'] ?? 'Konut.Update' }} RSS Feed" href="{{ url('/feed') }}">
    <title>@yield('title', ($site_settings['site_name'] ?? 'Konut.Update'))</title>
    {{-- SEO Fallback: hanya render jika child TIDAK define @section('meta') --}}
    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}">
        <meta name="keywords" content="{{ $site_settings['meta_keywords'] ?? 'konut, konawe utara, berita, news, informasi, sulawesi tenggara' }}">
        <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
        <meta property="og:locale" content="id_ID" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:title" content="@yield('title', ($site_settings['site_name'] ?? 'Konut.Update'))" />
        <meta property="og:description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}" />
        @if(!empty($site_settings['logo']))
            <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta property="og:image:alt" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
        @endif
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('title', ($site_settings['site_name'] ?? 'Konut.Update'))" />
        <meta name="twitter:description" content="{{ $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}" />
        @if(!empty($site_settings['logo']))
            <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
            <meta name="twitter:image:alt" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
        @endif
    @endif
    <link rel="canonical" href="{{ url()->current() }}" />
    @if(!empty($site_settings['favicon']))
        <link rel="icon" type="image/png" href="{{ Storage::url($site_settings['favicon']) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%23189B39'/%3E%3Ctext x='16' y='23' text-anchor='middle' font-family='Arial' font-weight='bold' font-size='20' fill='white'%3EK%3C/text%3E%3C/svg%3E">
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
<body class="antialiased">

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
            <input type="text" id="liveSearchInput" placeholder="Cari berita di Konut Update..." autocomplete="off" x-ref="searchInput">
            <button class="p-1.5 text-on-surface-variant hover:text-on-surface cursor-pointer bg-transparent border-none shrink-0" x-on:click="searchOpen = false">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div id="liveSearchResults" class="live-search-results" :class="{ 'active': $refs.searchInput?.value?.length >= 2 }"></div>
        </div>
    </div>

    {{-- Header --}}
    <header id="mainHeader" class="sticky-header bg-surface border-b border-outline">
        {{-- Top Bar --}}
        <div class="hidden lg:flex bg-primary text-white text-[11px] py-1">
            <div class="max-w-7xl mx-auto px-4 w-full flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 opacity-90" id="currentDate">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        <span></span>
                    </span>
                    <span class="opacity-30">|</span>
                    <span class="opacity-70">Portal Berita Konawe Utara</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="weather-widget" id="weatherWidget">
                        <i data-lucide="sun" class="w-3 h-3"></i>
                        <span id="weatherTemp">--°C</span>
                        <span class="opacity-60" id="weatherCity">Konawe Utara</span>
                    </div>
                    <div class="flex items-center gap-2.5 pl-3 border-l border-white/20">
                        @if(!empty($site_settings['facebook']))
                            <a href="{{ $site_settings['facebook'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors"><i class="fab fa-facebook text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['instagram']))
                            <a href="{{ $site_settings['instagram'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors"><i class="fab fa-instagram text-sm"></i></a>
                        @endif
                        @if(!empty($site_settings['youtube']))
                            <a href="{{ $site_settings['youtube'] }}" target="_blank" class="text-white/70 hover:text-white no-underline transition-colors"><i class="fab fa-youtube text-sm"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Logo + Search --}}
        <div class="border-b border-outline bg-surface">
            <div class="max-w-7xl mx-auto px-4 h-16 lg:h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer bg-transparent border-none" x-on:click="mobileOpen = true">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 no-underline">
                        @if(!empty($site_settings['logo']))
                            <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" class="h-12 lg:h-16 w-auto object-contain">
                        @else
                            <div class="flex items-center gap-2.5">
                                <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-primary flex items-center justify-center shadow-sm">
                                    <span class="text-white font-extrabold text-xl lg:text-2xl">K</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-2xl lg:text-3xl font-extrabold leading-none tracking-tight">
                                        <span class="text-primary">KONUT</span><span class="text-accent">UPDATE</span>
                                    </span>
                                    <span class="text-[9px] lg:text-[10px] text-on-surface-variant tracking-[0.25em] uppercase font-semibold leading-tight">Berita Terpercaya</span>
                                </div>
                            </div>
                        @endif
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <button class="flex items-center gap-2.5 bg-surface-container-low rounded-xl px-5 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer border-none min-w-[260px] group" x-on:click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())">
                        <i data-lucide="search" class="w-4 h-4 text-on-surface-variant group-hover:text-primary transition-colors"></i>
                        <span class="flex-1 text-left">Cari berita...</span>
                        <kbd class="hidden md:inline-flex text-[10px] bg-surface-container px-1.5 py-0.5 rounded border border-outline font-mono text-on-surface-variant">/</kbd>
                    </button>
                    <button class="theme-toggle" x-on:click="theme = theme === 'dark' ? 'light' : 'dark'">
                        <span x-show="theme === 'dark'">
                            <i data-lucide="sun" class="w-4 h-4"></i>
                        </span>
                        <span x-show="theme !== 'dark'">
                            <i data-lucide="moon" class="w-4 h-4"></i>
                        </span>
                    </button>
                </div>
                <div class="flex md:hidden items-center gap-2">
                    <button class="flex items-center justify-center w-10 h-10 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer bg-transparent border-none" x-on:click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <button class="theme-toggle" x-on:click="theme = theme === 'dark' ? 'light' : 'dark'">
                        <span x-show="theme === 'dark'">
                            <i data-lucide="sun" class="w-4 h-4"></i>
                        </span>
                        <span x-show="theme !== 'dark'">
                            <i data-lucide="moon" class="w-4 h-4"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Navigation --}}
        <nav class="hidden md:flex bg-surface border-b border-outline shadow-sm">
            <div class="max-w-7xl mx-auto px-4 w-full flex items-center justify-center overflow-x-auto">
                <a href="{{ url('/') }}" class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                       class="nav-link-custom whitespace-nowrap {{ request()->routeIs('categories.show') && request()->slug == $cat->slug ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach

            </div>
        </nav>
    </header>

    {{-- Breaking News --}}
    @include('frontend.partials.breaking-news')

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 py-6 lg:py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    {{-- Scroll to Top --}}
    <button id="scrollTopBtn" x-data="scrollToTop" x-show="showScroll" x-cloak x-on:click="scrollTop">
        <i data-lucide="arrow-up" class="w-5 h-5"></i>
    </button>

    {{-- Mobile Bottom Navigation --}}
    <nav class="mobile-bottom-nav md:hidden">
        <div class="flex items-center justify-around">
            <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 no-underline {{ request()->routeIs('home') ? 'text-primary' : 'text-on-surface-variant' }}">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="{{ route('search') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 no-underline text-on-surface-variant">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span class="text-[10px] font-medium">Cari</span>
            </a>
            <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 py-1 px-3 no-underline text-on-surface-variant">
                <i data-lucide="flame" class="w-5 h-5"></i>
                <span class="text-[10px] font-medium">Populer</span>
            </a>
            <button class="flex flex-col items-center gap-0.5 py-1 px-3 no-underline text-on-surface-variant cursor-pointer bg-transparent border-none" x-on:click="mobileOpen = true">
                <i data-lucide="align-left" class="w-5 h-5"></i>
                <span class="text-[10px] font-medium">Menu</span>
            </button>
        </div>
    </nav>

    {{-- Mobile Offcanvas --}}
    <template x-teleport="body">
        <div>
            <div class="fixed inset-0 bg-black/50 z-50 transition-opacity" x-show="mobileOpen" x-on:click="mobileOpen = false" x-cloak style="z-index: 105;"></div>
            <div class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-surface transform transition-transform duration-300 shadow-2xl overflow-y-auto" x-show="mobileOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" x-cloak @click.away="mobileOpen = false" style="z-index: 106;">
                <div class="relative flex items-center justify-center p-5 border-b border-outline">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="Konut.Update" class="h-14 w-auto object-contain">
                    @else
                        <span class="text-2xl font-extrabold"><span class="text-primary">KONUT</span><span class="text-accent">UPDATE</span></span>
                    @endif
                    <button class="absolute right-5 flex items-center justify-center w-10 h-10 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer bg-transparent border-none" x-on:click="mobileOpen = false">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-5 space-y-1">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('home') ? 'text-primary bg-primary-light' : 'text-on-surface hover:bg-surface-container' }} no-underline transition-colors">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        Home
                    </a>
                    <div class="pt-5 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant px-3 mb-3 uppercase tracking-wider">Kategori</p>
                        @foreach($categories as $cat)
                            <a href="{{ route('categories.show', $cat->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('categories.show') && request()->slug == $cat->slug ? 'text-primary bg-primary-light font-bold' : 'text-on-surface hover:bg-surface-container' }} no-underline transition-colors">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-on-surface-variant"></i>
                                <span>{{ $cat->name }}</span>
                                @if($cat->posts_count > 0)
                                    <span class="ml-auto text-xs text-on-surface-variant bg-surface-container-low px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <div class="pt-5 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant px-3 mb-3 uppercase tracking-wider">Halaman</p>
                        <a href="{{ route('pages.about') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-on-surface hover:bg-surface-container no-underline transition-colors">
                            <i data-lucide="info" class="w-4 h-4 text-on-surface-variant"></i>
                            Tentang Kami
                        </a>
                        <a href="{{ route('pages.pedoman') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-on-surface hover:bg-surface-container no-underline transition-colors">
                            <i data-lucide="scroll-text" class="w-4 h-4 text-on-surface-variant"></i>
                            Pedoman Siber
                        </a>
                        <a href="{{ route('pages.privacy') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-on-surface hover:bg-surface-container no-underline transition-colors">
                            <i data-lucide="shield" class="w-4 h-4 text-on-surface-variant"></i>
                            Kebijakan Privasi
                        </a>
                    </div>
                    <div class="pt-5 mt-4 border-t border-outline">
                        <p class="text-xs font-semibold text-on-surface-variant px-3 mb-3 uppercase tracking-wider">Ikuti Kami</p>
                        <div class="flex gap-2 px-3">
                            @if(!empty($site_settings['facebook']))
                                <a href="{{ $site_settings['facebook'] }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#1877f2] text-white no-underline"><i class="fab fa-facebook text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['instagram']))
                                <a href="{{ $site_settings['instagram'] }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gradient-to-tr from-[#833ab4] via-[#fd1d1d] to-[#f77737] text-white no-underline"><i class="fab fa-instagram text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['youtube']))
                                <a href="{{ $site_settings['youtube'] }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#ff0000] text-white no-underline"><i class="fab fa-youtube text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['tiktok']))
                                <a href="{{ $site_settings['tiktok'] }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-black text-white no-underline"><i class="fab fa-tiktok text-sm"></i></a>
                            @endif
                            @if(!empty($site_settings['email']))
                                <a href="mailto:{{ $site_settings['email'] }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-on-surface text-white no-underline"><i data-lucide="mail" class="w-4 h-4"></i></a>
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
                try {
                    return document.documentElement._x_dataStack?.[0];
                } catch (e) { return null; }
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

            // Sticky header hide on scroll (debounced with rAF)
            let lastScroll = 0;
            let headerTick = null;
            const header = document.getElementById('mainHeader');
            window.addEventListener('scroll', function() {
                if (headerTick) cancelAnimationFrame(headerTick);
                headerTick = requestAnimationFrame(function() {
                    const st = window.pageYOffset;
                    if (st > lastScroll && st > 200) header.classList.add('hidden-header');
                    else header.classList.remove('hidden-header');
                    lastScroll = st;
                });
            }, { passive: true });
        })();

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW registration failed:', err));
            });

            // Detect offline/online status
            window.addEventListener('online', () => document.body.classList.remove('is-offline'));
            window.addEventListener('offline', () => document.body.classList.add('is-offline'));
        }
    </script>
    @stack('scripts')
    @if(!empty($site_settings['footer_script']))
        {!! $site_settings['footer_script'] !!}
    @endif
</body>
</html>
