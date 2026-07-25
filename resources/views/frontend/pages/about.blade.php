@extends('frontend.layouts.app')

@section('title', 'Tentang Kami - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $aboutDesc = 'Tentang ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' - Portal berita online terpercaya dari Konawe Utara, Sulawesi Tenggara.';
    @endphp
    <meta name="description" content="{{ $aboutDesc }}" />
    <link rel="canonical" href="{{ route('pages.about') }}" />
    <meta property="og:title" content="Tentang Kami - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $aboutDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('pages.about') }}" />
    <meta property="og:locale" content="id_ID" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Tentang Kami - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $aboutDesc }}" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Tentang Kami</span>
        </nav>
    </div>

    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-8 py-12 md:px-12 md:py-16 text-center">
            @if(!empty($site_settings['logo']))
                <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" class="h-24 md:h-28 w-auto object-contain mx-auto mb-6 drop-shadow-lg">
            @endif
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Tentang Kami</h1>
            <p class="text-white/80 text-lg max-w-xl mx-auto">{{ $site_settings['description'] ?? 'Portal berita online terpercaya dari Konawe Utara, Sulawesi Tenggara.' }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-8">

            {{-- Tentang --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <p class="text-on-surface-variant leading-relaxed">
                    {{ $site_settings['about_text'] ?? 'Konut Update adalah media informasi digital yang menyajikan berita, peristiwa, pemerintahan, pembangunan, pendidikan, ekonomi, olahraga, dan berbagai informasi terkini seputar Kabupaten Konawe Utara, Sulawesi Tenggara. Kami berkomitmen menghadirkan informasi yang cepat, akurat, berimbang, dan mudah dipahami masyarakat. Selain menyampaikan berita, Konut Update juga menjadi wadah untuk mempromosikan potensi daerah, mengangkat kisah inspiratif, serta menyebarkan informasi yang bermanfaat bagi warga Konawe Utara. Dengan mengedepankan prinsip jurnalisme yang bertanggung jawab, Konut Update berharap dapat menjadi sumber informasi terpercaya sekaligus jembatan komunikasi antara masyarakat, pemerintah, dan berbagai pihak dalam mendukung kemajuan Konawe Utara.' }}
                </p>
            </div>

            {{-- Visi --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="eye" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Visi</h2>
                </div>
                <p class="text-on-surface-variant leading-relaxed">
                    Menjadi portal berita terdepan dan terpercaya di Konawe Utara yang mampu memberikan informasi yang mendidik, mencerahkan, dan memberdayakan masyarakat.
                </p>
            </div>

            {{-- Misi --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="target" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Misi</h2>
                </div>
                <ul class="space-y-4">
                    @foreach([
                        'Menyajikan berita yang akurat, berimbang, dan terpercaya kepada masyarakat Konawe Utara dan sekitarnya.',
                        'Menjadi jembatan informasi antara pemerintah daerah, pelaku bisnis, dan masyarakat.',
                        'Mengedepankan jurnalisme yang bertanggung jawab dan berintegritas.',
                        'Memanfaatkan teknologi digital untuk menyebarkan informasi secara cepat dan luas.',
                    ] as $i => $misi)
                        <li class="flex items-start gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">{{ $i + 1 }}</span>
                            <span class="text-on-surface-variant leading-relaxed">{{ $misi }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Mengapa Memilih Kami --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-accent/10 flex items-center justify-center shrink-0">
                        <i data-lucide="badge-check" class="w-5 h-5 text-accent"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Mengapa Memilih Kami</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['icon' => 'zap', 'title' => 'Cepat & Akurat', 'desc' => 'Berita disampaikan secara real-time tanpa mengorbankan akurasi.'],
                        ['icon' => 'shield-check', 'title' => 'Terpercaya', 'desc' => 'Setiap artikel melalui proses verifikasi ketat sebelum dipublikasikan.'],
                        ['icon' => 'users', 'title' => 'Berpihak pada Rakyat', 'desc' => 'Independen dan tidak terpengaruh oleh kepentingan politik manapun.'],
                        ['icon' => 'smartphone', 'title' => 'Digital First', 'desc' => 'Diakses kapan saja dan di mana saja melalui berbagai perangkat.'],
                    ] as $item)
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-surface-container border border-outline/50">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-bold text-on-surface mb-1">{{ $item['title'] }}</h4>
                                <p class="text-xs text-on-surface-variant leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kontak --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="mail" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Hubungi Kami</h2>
                </div>
                <div class="space-y-4">
                    @if(!empty($site_settings['address']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-0.5">Alamat</p>
                                <p class="text-on-surface text-sm">{{ $site_settings['address'] }}</p>
                            </div>
                        </div>
                    @endif
                    @if(!empty($site_settings['email']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-4 h-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-0.5">Email</p>
                                <a href="mailto:{{ $site_settings['email'] }}" class="text-primary text-sm no-underline hover:underline">{{ $site_settings['email'] }}</a>
                            </div>
                        </div>
                    @endif
                    @if(!empty($site_settings['phone']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-4 h-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wide mb-0.5">Telepon</p>
                                <p class="text-on-surface text-sm">{{ $site_settings['phone'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
