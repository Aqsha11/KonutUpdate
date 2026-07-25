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
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Kebijakan Privasi</h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Bagaimana {{ $site_settings['site_name'] ?? 'Konut.Update' }} melindungi data Anda</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8 lg:p-10 text-on-surface-variant leading-relaxed">
                <p class="mb-6">Kebijakan Privasi ini menjelaskan bagaimana {{ $site_settings['site_name'] ?? 'Konut.Update' }} mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.</p>

                @php
                    $sections = [
                        '1. Informasi yang Kami Kumpulkan' => [
                            '<strong>Informasi yang Anda berikan:</strong> Nama, alamat email, dan informasi lainnya saat Anda mengisi formulir kontak, berlangganan newsletter, atau memberikan komentar.',
                            '<strong>Informasi otomatis:</strong> Alamat IP, jenis browser, halaman yang dikunjungi, waktu akses, dan data demografis lainnya.',
                            '<strong>Cookie:</strong> Kami menggunakan cookie untuk meningkatkan pengalaman browsing Anda.',
                        ],
                        '2. Penggunaan Informasi' => [
                            'Menyediakan dan memelihara layanan portal berita.',
                            'Mengirimkan newsletter dan update berita (dengan persetujuan Anda).',
                            'Menjawab pertanyaan dan menanggapi permintaan Anda.',
                            'Menganalisis penggunaan situs untuk meningkatkan kualitas konten dan layanan.',
                            'Menyesuaikan konten dan iklan yang ditampilkan kepada Anda.',
                        ],
                        '3. Perlindungan Data' => [
                            'Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang tepat untuk melindungi informasi pribadi Anda dari akses tidak sah, perubahan, pengungkapan, atau penghancuran.',
                        ],
                        '4. Pengungkapan kepada Pihak Ketiga' => [
                            'Kami tidak menjual, memperdagangkan, atau mentransfer informasi pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.',
                        ],
                        '5. Cookie' => [
                            'Cookie esensial: Diperlukan untuk fungsi dasar situs.',
                            'Cookie analitik: Membantu kami memahami bagaimana pengunjung berinteraksi dengan situs.',
                            'Cookie fungsional: Mengingat preferensi Anda untuk pengalaman yang lebih personal.',
                        ],
                        '6. Hak Anda' => [
                            'Anda memiliki hak untuk mengakses, memperbaiki, atau menghapus data pribadi Anda. Anda juga dapat menolak pemrosesan data dan menarik persetujuan kapan saja.',
                        ],
                        '7. Perubahan Kebijakan' => [
                            'Kebijakan privasi ini dapat diperbarui dari waktu ke waktu. Perubahan akan diumumkan melalui situs kami.',
                        ],
                    ];
                @endphp

                @foreach($sections as $title => $items)
                    <h4 class="text-lg font-bold text-on-surface mt-6 mb-3">{{ $title }}</h4>
                    @if(count($items) === 1)
                        <p class="mb-4">{!! $items[0] !!}</p>
                    @else
                        <ul class="list-disc pl-5 mb-6 space-y-1.5">
                            @foreach($items as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach

                <hr class="border-outline my-6">

                <h4 class="text-lg font-bold text-on-surface mt-6 mb-3 flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-primary"></i>
                    Kontak
                </h4>
                <p>Email: <a href="mailto:{{ !empty($site_settings['email']) ? $site_settings['email'] : 'redaksi@konut.update' }}" class="text-primary no-underline hover:underline">{{ !empty($site_settings['email']) ? $site_settings['email'] : 'redaksi@konut.update' }}</a></p>
            </div>
        </div>
        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
