@extends('frontend.layouts.app')

@section('title', 'Kebijakan Privasi - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $privacyDesc = 'Kebijakan Privasi ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' - Bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.';
    @endphp
    <meta name="description" content="{{ $privacyDesc }}" />
    <link rel="canonical" href="{{ route('pages.privacy') }}" />
    <meta property="og:title" content="Kebijakan Privasi - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $privacyDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('pages.privacy') }}" />
    <meta property="og:locale" content="id_ID" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Kebijakan Privasi - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $privacyDesc }}" />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Kebijakan Privasi</span>
        </nav>
    </div>

    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-8 py-12 md:px-12 md:py-16 text-center">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="shield" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Kebijakan Privasi</h1>
            <p class="text-white/80 text-lg max-w-xl mx-auto">Bagaimana {{ $site_settings['site_name'] ?? 'Konut.Update' }} melindungi data Anda</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-6">

            @php
                $sections = [
                    ['icon' => 'info', 'title' => 'Pendahuluan', 'desc' => 'Selamat datang di ' . ($site_settings['site_name'] ?? 'Konut.Update') . '. Kami menghargai privasi setiap pengunjung dan berkomitmen melindungi informasi yang Anda berikan saat mengakses platform kami.'],
                    ['icon' => 'database', 'title' => '1. Informasi yang Kami Kumpulkan', 'desc' => 'Kami dapat mengumpulkan informasi seperti nama, alamat email, atau data lain yang Anda berikan secara sukarela saat menghubungi kami, mengirimkan informasi, atau berinteraksi melalui platform ' . ($site_settings['site_name'] ?? 'Konut.Update') . '.'],
                    ['icon' => 'settings', 'title' => '2. Penggunaan Informasi', 'list' => ['Menyediakan dan meningkatkan layanan.', 'Menanggapi pertanyaan atau masukan dari pengguna.', 'Mengembangkan kualitas konten dan pengalaman pengguna.', 'Memenuhi ketentuan hukum yang berlaku.']],
                    ['icon' => 'shield-check', 'title' => '3. Perlindungan Data', 'desc' => 'Kami berupaya menjaga keamanan informasi pengguna dengan langkah-langkah yang wajar untuk mencegah akses, penggunaan, atau pengungkapan yang tidak sah.'],
                    ['icon' => 'cookie', 'title' => '4. Cookie', 'desc' => 'Platform kami dapat menggunakan cookie untuk meningkatkan pengalaman pengguna. Anda dapat mengatur browser untuk menolak penggunaan cookie, namun beberapa fitur mungkin tidak berfungsi secara optimal.'],
                    ['icon' => 'external-link', 'title' => '5. Tautan ke Situs Lain', 'desc' => ($site_settings['site_name'] ?? 'Konut.Update') . ' dapat memuat tautan ke situs pihak ketiga. Kami tidak bertanggung jawab atas isi maupun kebijakan privasi situs tersebut.'],
                    ['icon' => 'refresh-cw', 'title' => '6. Perubahan Kebijakan', 'desc' => 'Kebijakan Privasi ini dapat diperbarui sewaktu-waktu. Setiap perubahan akan dipublikasikan pada halaman ini.'],
                    ['icon' => 'mail', 'title' => '7. Hubungi Kami', 'desc' => 'Apabila Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan menghubungi tim ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' melalui alamat email atau media sosial resmi yang tersedia.', 'isContact' => true],
                ];
            @endphp

            @foreach($sections as $section)
                <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $section['icon'] }}" class="w-5 h-5 text-primary"></i>
                        </div>
                        <h2 class="text-xl font-bold text-on-surface">{{ $section['title'] }}</h2>
                    </div>
                    @if(!empty($section['desc']))
                        <p class="text-on-surface-variant leading-relaxed">{{ $section['desc'] }}</p>
                    @endif
                    @if(!empty($section['list']))
                        <ul class="space-y-3 mt-1">
                            @foreach($section['list'] as $item)
                                <li class="flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="check" class="w-3 h-3 text-primary"></i>
                                    </div>
                                    <span class="text-on-surface-variant leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($section['isContact']) && !empty($site_settings['email']))
                        <a href="mailto:{{ $site_settings['email'] }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold no-underline hover:opacity-90 transition-opacity mt-4">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            {{ $site_settings['email'] }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
