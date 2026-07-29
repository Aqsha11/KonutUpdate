@extends('frontend.layouts.app')

@section('title', 'Pedoman Media Siber - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $pedomanDesc = 'Pedoman Media Siber ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' - Pedoman pemberitaan media siber yang berlaku untuk seluruh konten yang dipublikasikan.';
    @endphp
    <meta name="description" content="{{ $pedomanDesc }}" />
    <link rel="canonical" href="{{ route('pages.pedoman') }}" />
    <meta property="og:title" content="Pedoman Media Siber - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $pedomanDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('pages.pedoman') }}" />
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Pedoman Media Siber - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $pedomanDesc }}" />
    @if(!empty($site_settings['logo']))
        <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Pedoman Media Siber</span>
        </nav>
    </div>

    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-xl lg:rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-6 py-10 md:px-12 md:py-16 text-center">
            <div class="w-14 h-14 md:w-16 md:h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="book-open" class="w-7 h-7 md:w-8 md:h-8 text-white"></i>
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold text-white mb-3">Pedoman Media Siber</h1>
            <p class="text-white/80 text-base md:text-lg max-w-xl mx-auto">Pedoman pemberitaan dan etika jurnalistik {{ $site_settings['site_name'] ?? 'Konut.Update' }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-6">

            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                <p class="text-on-surface-variant leading-relaxed text-sm md:text-base">
                    Pedoman Media Siber ini berlaku untuk seluruh konten yang dipublikasikan melalui platform {{ $site_settings['site_name'] ?? 'Konut.Update' }}, termasuk namun tidak terbatas pada artikel berita, opini, foto, video, dan konten multimedia lainnya.
                </p>
            </div>

            @foreach([
                ['icon' => 'compass', 'title' => 'Prinsip Dasar', 'items' => [
                    'Kami berkomitmen untuk menyajikan informasi yang akurat, berimbang, dan tidak memihak.',
                    'Setiap berita yang dipublikasikan telah melalui proses verifikasi dan editing yang ketat.',
                    'Kami menjunjung tinggi etika jurnalistik dan kode etik jurnalistik Indonesia.',
                    'Kami menghormati hak privasi dan asas praduga tak bersalah.',
                ]],
                ['icon' => 'search-check', 'title' => 'Verifikasi dan Akurasi', 'items' => [
                    'Setiap informasi wajib diverifikasi minimal dari dua sumber yang terpercaya.',
                    'Informasi yang bersifat merugikan pihak lain harus melalui konfirmasi terlebih dahulu.',
                    'Dalam pemberitaan yang menyangkut sengketa, redaksi menerapkan asas praduga tak bersalah dan memberikan hak jawab kepada semua pihak yang terkait.',
                    'Kesalahan informasi akan segera diperbaiki dan disertai dengan koreksi yang jelas.',
                ]],
                ['icon' => 'message-square', 'title' => 'Hak Jawab dan Hak Koreksi', 'items' => [
                    'Setiap pihak yang dirugikan oleh pemberitaan berhak mengajukan hak jawab atau hak koreksi.',
                    'Redaksi wajib memuat hak jawab atau koreksi secara proporsional dan tidak ditunda-tunda.',
                    'Hak jawab dan koreksi akan dimuat di platform yang sama dengan berita yang dipermasalahkan.',
                    'Pengajuan hak jawab dapat dilakukan melalui email, surat, atau datang langsung ke kantor redaksi.',
                ]],
            ] as $section)
            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $section['icon'] }}" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-on-surface">{{ $section['title'] }}</h2>
                </div>
                <ul class="space-y-3">
                    @foreach($section['items'] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed text-sm md:text-base">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endforeach

            {{-- Pornografi & Kekerasan --}}
            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-accent/10 flex items-center justify-center shrink-0">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-accent"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-on-surface">Pornografi dan Kekerasan</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Kami tidak mempublikasikan konten pornografi dalam bentuk apapun.',
                        'Konten kekerasan akan disajikan secara proporsional dan tidak sensasional.',
                        'Korban kekerasan dan anak-anak dilindungi identitasnya dalam setiap pemberitaan.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-accent/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="x" class="w-3 h-3 text-accent"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed text-sm md:text-base">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @foreach([
                ['icon' => 'badge-percent', 'title' => 'Iklan dan Konten Berbayar', 'items' => [
                    'Iklan dan konten bersponsor harus dibedakan secara jelas dari konten redaksional.',
                    'Kami tidak menerima konten berbayar yang bertentangan dengan nilai-nilai jurnalistik.',
                    'Tim redaksi tidak terlibat dalam produksi konten iklan atau bersponsor.',
                ]],
                ['icon' => 'copyright', 'title' => 'Sumber dan Hak Cipta', 'items' => [
                    'Setiap penggunaan konten dari sumber lain wajib menyebutkan sumber asli.',
                    'Kami menghormati hak cipta dan kekayaan intelektual pihak lain.',
                    'Konten yang diproduksi sendiri oleh redaksi dilindungi hak cipta.',
                ]],
            ] as $section)
            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $section['icon'] }}" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-on-surface">{{ $section['title'] }}</h2>
                </div>
                <ul class="space-y-3">
                    @foreach($section['items'] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed text-sm md:text-base">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endforeach

            {{-- Kontak Redaksi --}}
            <div class="bg-surface rounded-xl lg:rounded-2xl shadow-sm border border-outline p-5 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="mail" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-on-surface">Kontak Redaksi</h2>
                </div>
                <div class="space-y-4">
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
