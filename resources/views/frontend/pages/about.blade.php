@extends('frontend.layouts.app')

@section('title', 'Tentang Kami - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Tentang {{ $site_settings['site_name'] ?? 'Konut.Update' }} - Portal berita online terpercaya dari Konawe Utara, Sulawesi Tenggara." />
    <meta property="og:title" content="Tentang Kami - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Tentang {{ $site_settings['site_name'] ?? 'Konut.Update' }} - Portal berita online terpercaya dari Konawe Utara, Sulawesi Tenggara." />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Tentang Kami</span>
        </nav>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8 lg:p-10">
                @if(!empty($site_settings['logo']))
                    <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" class="h-12 w-auto object-contain mb-6">
                @endif

                <h2 class="text-2xl font-extrabold text-on-surface mb-4">Selamat Datang di {{ $site_settings['site_name'] ?? 'Konut.Update' }}</h2>
                <p class="text-on-surface-variant leading-relaxed mb-6">{{ $site_settings['description'] ?? 'Portal berita online yang menyajikan informasi terkini, akurat, dan terpercaya dari Konawe Utara (Konut), Sulawesi Tenggara.' }}</p>

                <hr class="border-outline my-8">

                <h4 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5 text-primary"></i>
                    Visi
                </h4>
                <p class="text-on-surface-variant mb-6 leading-relaxed">Menjadi portal berita terdepan dan terpercaya di Konawe Utara yang mampu memberikan informasi yang mendidik, mencerahkan, dan memberdayakan masyarakat.</p>

                <h4 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="target" class="w-5 h-5 text-primary"></i>
                    Misi
                </h4>
                <ul class="text-on-surface-variant space-y-2.5 list-disc pl-5 mb-6 leading-relaxed">
                    <li>Menyajikan berita yang akurat, berimbang, dan terpercaya kepada masyarakat Konawe Utara dan sekitarnya.</li>
                    <li>Menjadi jembatan informasi antara pemerintah daerah, pelaku bisnis, dan masyarakat.</li>
                    <li>Mengedepankan jurnalisme yang bertanggung jawab dan berintegritas.</li>
                    <li>Memanfaatkan teknologi digital untuk menyebarkan informasi secara cepat dan luas.</li>
                </ul>

                <hr class="border-outline my-8">

                <h4 class="text-lg font-bold text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="phone" class="w-5 h-5 text-primary"></i>
                    Kontak
                </h4>
                <div class="text-on-surface-variant space-y-3 leading-relaxed">
                    @if(!empty($site_settings['address']))
                        <p class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 shrink-0 text-primary"></i>
                            <span><strong>Alamat:</strong> {{ $site_settings['address'] }}</span>
                        </p>
                    @endif
                    @if(!empty($site_settings['email']))
                        <p class="flex items-start gap-2">
                            <i data-lucide="mail" class="w-4 h-4 mt-0.5 shrink-0 text-primary"></i>
                            <span><strong>Email:</strong> <a href="mailto:{{ $site_settings['email'] }}" class="text-primary no-underline hover:underline">{{ $site_settings['email'] }}</a></span>
                        </p>
                    @endif
                    @if(!empty($site_settings['phone']))
                        <p class="flex items-start gap-2">
                            <i data-lucide="phone" class="w-4 h-4 mt-0.5 shrink-0 text-primary"></i>
                            <span><strong>Telepon:</strong> {{ $site_settings['phone'] }}</span>
                        </p>
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
