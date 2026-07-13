@extends('frontend.layouts.app')

@section('title', 'Pedoman Media Siber - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Pedoman Media Siber {{ $site_settings['site_name'] ?? 'Konut.Update' }} - Pedoman pemberitaan media siber yang berlaku untuk seluruh konten yang dipublikasikan." />
    <meta property="og:title" content="Pedoman Media Siber - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Pedoman Media Siber {{ $site_settings['site_name'] ?? 'Konut.Update' }} - Pedoman pemberitaan media siber yang berlaku untuk seluruh konten yang dipublikasikan." />
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Pedoman Media Siber</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Pedoman Media Siber</h1>
        <p class="text-on-surface-variant text-sm mt-1.5">Pedoman pemberitaan {{ $site_settings['site_name'] ?? 'Konut.Update' }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%]">
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8 lg:p-10 text-on-surface-variant leading-relaxed">
                <p class="mb-6">Pedoman Media Siber ini berlaku untuk seluruh konten yang dipublikasikan melalui platform {{ $site_settings['site_name'] ?? 'Konut.Update' }}, termasuk namun tidak terbatas pada artikel berita, opini, foto, video, dan konten multimedia lainnya.</p>

                @php
                    $sections = [
                        '1. Prinsip Dasar' => [
                            'Kami berkomitmen untuk menyajikan informasi yang akurat, berimbang, dan tidak memihak.',
                            'Setiap berita yang dipublikasikan telah melalui proses verifikasi dan editing yang ketat.',
                            'Kami menjunjung tinggi etika jurnalistik dan kode etik jurnalistik Indonesia.',
                            'Kami menghormati hak privasi dan asas praduga tak bersalah.',
                        ],
                        '2. Verifikasi dan Akurasi' => [
                            'Setiap informasi wajib diverifikasi minimal dari dua sumber yang terpercaya.',
                            'Informasi yang bersifat merugikan pihak lain harus melalui konfirmasi terlebih dahulu.',
                            'Dalam pemberitaan yang menyangkut sengketa, redaksi menerapkan asas praduga tak bersalah dan memberikan hak jawab kepada semua pihak yang terkait.',
                            'Kesalahan informasi akan segera diperbaiki dan disertai dengan koreksi yang jelas.',
                        ],
                        '3. Hak Jawab dan Hak Koreksi' => [
                            'Setiap pihak yang dirugikan oleh pemberitaan berhak mengajukan hak jawab atau hak koreksi.',
                            'Redaksi wajib memuat hak jawab atau koreksi secara proporsional dan tidak ditunda-tunda.',
                            'Hak jawab dan koreksi akan dimuat di platform yang sama dengan berita yang dipermasalahkan.',
                            'Pengajuan hak jawab dapat dilakukan melalui email, surat, atau datang langsung ke kantor redaksi.',
                        ],
                        '4. Pornografi dan Kekerasan' => [
                            'Kami tidak mempublikasikan konten pornografi dalam bentuk apapun.',
                            'Konten kekerasan akan disajikan secara proporsional dan tidak sensasional.',
                            'Korban kekerasan dan anak-anak dilindungi identitasnya dalam setiap pemberitaan.',
                        ],
                        '5. Iklan dan Konten Berbayar' => [
                            'Iklan dan konten bersponsor harus dibedakan secara jelas dari konten redaksional.',
                            'Kami tidak menerima konten berbayar yang bertentangan dengan nilai-nilai jurnalistik.',
                            'Tim redaksi tidak terlibat dalam produksi konten iklan atau bersponsor.',
                        ],
                        '6. Sumber dan Hak Cipta' => [
                            'Setiap penggunaan konten dari sumber lain wajib menyebutkan sumber asli.',
                            'Kami menghormati hak cipta dan kekayaan intelektual pihak lain.',
                            'Konten yang diproduksi sendiri oleh redaksi dilindungi hak cipta.',
                        ],
                    ];
                @endphp

                @foreach($sections as $title => $items)
                    <h4 class="text-lg font-bold text-on-surface mt-6 mb-3">{{ $title }}</h4>
                    <ul class="list-disc pl-5 mb-6 space-y-1.5">
                        @foreach($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endforeach

                <hr class="border-outline my-6">

                <h4 class="text-lg font-bold text-on-surface mt-6 mb-3 flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-primary"></i>
                    Kontak Redaksi
                </h4>
                <p>Email: <a href="mailto:{{ !empty($site_settings['email']) ? $site_settings['email'] : 'redaksi@konut.update' }}" class="text-primary no-underline hover:underline">{{ !empty($site_settings['email']) ? $site_settings['email'] : 'redaksi@konut.update' }}</a></p>
                @if(!empty($site_settings['address']))
                    <p class="mt-1">Alamat: {{ $site_settings['address'] }}</p>
                @endif
            </div>
        </div>
        <div class="lg:w-[32%]">
            <div class="lg:sticky lg:top-24 space-y-6">
                @include('frontend.partials.sidebar')
            </div>
        </div>
    </div>
@endsection
