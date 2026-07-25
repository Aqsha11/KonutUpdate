@extends('frontend.layouts.app')

@section('title', 'Kontak - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    @php
        $kontakDesc = 'Hubungi ' . ($site_settings['site_name'] ?? 'Konut.Update') . ' - Kirim saran, masukan, atau pertanyaan kepada redaksi kami.';
    @endphp
    <meta name="description" content="{{ $kontakDesc }}" />
    <link rel="canonical" href="{{ route('pages.kontak') }}" />
    <meta property="og:title" content="Kontak - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="{{ $kontakDesc }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('pages.kontak') }}" />
    <meta property="og:site_name" content="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:locale" content="id_ID" />
    @if(!empty($site_settings['logo']))
        <meta property="og:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
    @endif
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Kontak - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta name="twitter:description" content="{{ $kontakDesc }}" />
    @if(!empty($site_settings['logo']))
        <meta name="twitter:image" content="{{ url(Storage::url($site_settings['logo'])) }}" />
    @endif
@endsection

@section('content')
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-on-surface-variant mb-3">
            <a href="{{ url('/') }}" class="no-underline text-on-surface-variant hover:text-primary">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-on-surface font-semibold">Kontak</span>
        </nav>
    </div>

    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-primary/90 via-primary to-primary-dark rounded-2xl overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
        </div>
        <div class="relative px-8 py-12 md:px-12 md:py-16 text-center">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i data-lucide="send" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Hubungi Kami</h1>
            <p class="text-white/80 text-lg max-w-xl mx-auto">Kirim saran, masukan, atau pertanyaan kepada redaksi {{ $site_settings['site_name'] ?? 'Konut.Update' }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
        <div class="lg:w-[68%] space-y-6">

            {{-- Formulir --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="pencil-line" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Kirim Pesan</h2>
                </div>
                <form id="contactForm" class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-on-surface mb-1.5">Nama</label>
                        <input type="text" id="name" required
                               class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                               placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-on-surface mb-1.5">Subjek</label>
                        <input type="text" id="subject" required
                               class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                               placeholder="Perihal pesan Anda">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-on-surface mb-1.5">Pesan</label>
                        <textarea id="message" rows="5" required
                                  class="w-full px-4 py-2.5 rounded-xl border border-outline bg-surface-container text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-y"
                                  placeholder="Tuliskan pesan Anda di sini..."></textarea>
                    </div>

                    {{-- Pilih platform tujuan --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-2">Kirim melalui</label>
                        <div class="flex flex-wrap gap-3" id="platformButtons">
                            @if(!empty($site_settings['whatsapp']))
                                <button type="button" onclick="sendTo('whatsapp')"
                                    class="platform-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#25d366]/10 text-[#25d366] text-sm font-semibold no-underline hover:bg-[#25d366] hover:text-white transition-colors border-2 border-transparent data-[active]:border-[#25d366]">
                                    <i class="fab fa-whatsapp text-base"></i> WhatsApp
                                </button>
                            @endif
                            @if(!empty($site_settings['instagram']))
                                <a href="{{ $site_settings['instagram'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#e4405f]/10 text-[#e4405f] text-sm font-semibold no-underline hover:bg-[#e4405f] hover:text-white transition-colors">
                                    <i class="fab fa-instagram text-base"></i> Instagram DM
                                </a>
                            @endif
                            @if(!empty($site_settings['facebook']))
                                <a href="{{ $site_settings['facebook'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1877f2]/10 text-[#1877f2] text-sm font-semibold no-underline hover:bg-[#1877f2] hover:text-white transition-colors">
                                    <i class="fab fa-facebook text-base"></i> Facebook
                                </a>
                            @endif
                            @if(!empty($site_settings['email']))
                                <button type="button" onclick="sendTo('email')"
                                    class="platform-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary/10 text-primary text-sm font-semibold no-underline hover:bg-primary hover:text-white transition-colors border-2 border-transparent data-[active]:border-primary">
                                    <i data-lucide="mail" class="w-4 h-4"></i> Email
                                </button>
                            @endif
                            @if(!empty($site_settings['phone']))
                                <button type="button" onclick="sendTo('sms')"
                                    class="platform-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent/10 text-accent text-sm font-semibold no-underline hover:bg-accent hover:text-white transition-colors border-2 border-transparent data-[active]:border-accent">
                                    <i data-lucide="smartphone" class="w-4 h-4"></i> SMS
                                </button>
                            @endif
                        </div>
                        <p id="platformHint" class="text-xs text-on-surface-variant mt-3 hidden">
                            <i data-lucide="info" class="w-3 h-3 inline-block mr-0.5"></i>
                            <span id="hintText"></span>
                        </p>
                    </div>
                </form>
            </div>

            {{-- Informasi Kontak --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="info" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Informasi Kontak</h2>
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

            {{-- Media Sosial --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline p-6 md:p-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                        <i data-lucide="share-2" class="w-5 h-5 text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-on-surface">Ikuti Kami</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(!empty($site_settings['facebook']))
                        <a href="{{ $site_settings['facebook'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1877f2]/10 text-[#1877f2] text-sm font-medium no-underline hover:bg-[#1877f2] hover:text-white transition-colors">
                            <i class="fab fa-facebook text-sm"></i> Facebook
                        </a>
                    @endif
                    @if(!empty($site_settings['instagram']))
                        <a href="{{ $site_settings['instagram'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#e4405f]/10 text-[#e4405f] text-sm font-medium no-underline hover:bg-[#e4405f] hover:text-white transition-colors">
                            <i class="fab fa-instagram text-sm"></i> Instagram
                        </a>
                    @endif
                    @if(!empty($site_settings['youtube']))
                        <a href="{{ $site_settings['youtube'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#ff0000]/10 text-[#ff0000] text-sm font-medium no-underline hover:bg-[#ff0000] hover:text-white transition-colors">
                            <i class="fab fa-youtube text-sm"></i> YouTube
                        </a>
                    @endif
                    @if(!empty($site_settings['tiktok']))
                        <a href="{{ $site_settings['tiktok'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-black/10 text-black text-sm font-medium no-underline hover:bg-black hover:text-white transition-colors">
                            <i class="fab fa-tiktok text-sm"></i> TikTok
                        </a>
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

    @push('scripts')
    <script>
        function sendTo(platform) {
            const name = document.getElementById('name').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!name || !subject || !message) {
                alert('Harap isi semua kolom terlebih dahulu.');
                return;
            }

            const body = `Halo, saya *${name}*.\n\n*Subjek:* ${subject}\n\n*Pesan:*\n${message}`;

            switch (platform) {
                case 'whatsapp':
                    window.open('https://wa.me/{{ $site_settings["whatsapp"] ?? "" }}?text=' + encodeURIComponent(body), '_blank');
                    break;
                case 'email':
                    window.location.href = 'mailto:{{ $site_settings["email"] ?? "" }}?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
                    break;
                case 'sms':
                    window.location.href = 'sms:?body=' + encodeURIComponent(body);
                    break;
            }
        }
    </script>
    @endpush
@endsection
