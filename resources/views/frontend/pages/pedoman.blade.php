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
    <meta property="og:locale" content="id_ID" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Pedoman Media Siber - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $pedomanDesc }}" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Pedoman Media Siber</span>
        </nav>
    </div>

    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-8 py-12 md:px-12 md:py-16 text-center">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="book-open" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Pedoman Media Siber</h1>
            <p class="text-white/80 text-lg max-w-xl mx-auto">Pedoman pemberitaan dan etika jurnalistik {{ $site_settings['site_name'] ?? 'Konut.Update' }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-6">

            {{-- Intro --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <p class="text-on-surface-variant leading-relaxed">
                    Pedoman Media Siber ini berlaku untuk seluruh konten yang dipublikasikan melalui platform {{ $site_settings['site_name'] ?? 'Konut.Update' }}, termasuk namun tidak terbatas pada artikel berita, opini, foto, video, dan konten multimedia lainnya.
                </p>
            </div>

            {{-- 1. Prinsip Dasar --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="compass" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Prinsip Dasar</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Kami berkomitmen untuk menyajikan informasi yang akurat, berimbang, dan tidak memihak.',
                        'Setiap berita yang dipublikasikan telah melalui proses verifikasi dan editing yang ketat.',
                        'Kami menjunjung tinggi etika jurnalistik dan kode etik jurnalistik Indonesia.',
                        'Kami menghormati hak privasi dan asas praduga tak bersalah.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 2. Verifikasi dan Akurasi --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="search-check" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Verifikasi dan Akurasi</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Setiap informasi wajib diverifikasi minimal dari dua sumber yang terpercaya.',
                        'Informasi yang bersifat merugikan pihak lain harus melalui konfirmasi terlebih dahulu.',
                        'Dalam pemberitaan yang menyangkut sengketa, redaksi menerapkan asas praduga tak bersalah dan memberikan hak jawab kepada semua pihak yang terkait.',
                        'Kesalahan informasi akan segera diperbaiki dan disertai dengan koreksi yang jelas.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3. Hak Jawab dan Hak Koreksi --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="message-square" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Hak Jawab dan Hak Koreksi</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Setiap pihak yang dirugikan oleh pemberitaan berhak mengajukan hak jawab atau hak koreksi.',
                        'Redaksi wajib memuat hak jawab atau koreksi secara proporsional dan tidak ditunda-tunda.',
                        'Hak jawab dan koreksi akan dimuat di platform yang sama dengan berita yang dipermasalahkan.',
                        'Pengajuan hak jawab dapat dilakukan melalui email, surat, atau datang langsung ke kantor redaksi.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 4. Pornografi dan Kekerasan --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-accent/10 flex items-center justify-center shrink-0">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-accent"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Pornografi dan Kekerasan</h2>
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
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 5. Iklan dan Konten Berbayar --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="badge-percent" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Iklan dan Konten Berbayar</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Iklan dan konten bersponsor harus dibedakan secara jelas dari konten redaksional.',
                        'Kami tidak menerima konten berbayar yang bertentangan dengan nilai-nilai jurnalistik.',
                        'Tim redaksi tidak terlibat dalam produksi konten iklan atau bersponsor.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 6. Sumber dan Hak Cipta --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="copyright" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Sumber dan Hak Cipta</h2>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Setiap penggunaan konten dari sumber lain wajib menyebutkan sumber asli.',
                        'Kami menghormati hak cipta dan kekayaan intelektual pihak lain.',
                        'Konten yang diproduksi sendiri oleh redaksi dilindungi hak cipta.',
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                            </div>
                            <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Kontak Redaksi --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="mail" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Kontak Redaksi</h2>
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
