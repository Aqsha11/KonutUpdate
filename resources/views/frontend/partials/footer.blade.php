<footer class="bg-inverse-surface text-inverse-on-surface dark:bg-gray-900 dark:text-gray-300 mt-10 lg:mt-12">
    {{-- Decorative Wave --}}
    <div class="footer-wave">
        <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,20 C240,0 480,40 720,20 C960,0 1200,40 1440,20 L1440,40 L0,40 Z" fill="var(--color-primary, #189B39)" opacity="0.15"/>
            <path d="M0,25 C240,10 480,35 720,25 C960,10 1200,35 1440,25 L1440,40 L0,40 Z" fill="var(--color-accent, #F58220)" opacity="0.1"/>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 lg:py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            {{-- About --}}
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="inline-block mb-4 no-underline">
                    @if(!empty($site_settings['logo']))
                        <img src="{{ Storage::url($site_settings['logo']) }}" alt="{{ $site_settings['site_name'] ?? 'Konut.Update' }}" class="h-14 w-auto object-contain">
                    @else
                        <span class="text-xl font-extrabold">
                            <span class="text-primary-fixed-dim">KONUT</span><span class="text-accent-fixed-dim">UPDATE</span>
                        </span>
                    @endif
                </a>
                <p class="text-sm leading-relaxed opacity-70">{{ $site_settings['description'] ?? 'Portal berita online terpercaya dari Konawe Utara, Sulawesi Tenggara.' }}</p>
                <div class="flex gap-2.5 mt-5">
                    @if(!empty($site_settings['facebook']))
                        <a href="{{ $site_settings['facebook'] }}" target="_blank" class="social-icon bg-white/10 hover:bg-[#1877f2]"><i class="fab fa-facebook text-sm"></i></a>
                    @endif
                    @if(!empty($site_settings['instagram']))
                        <a href="{{ $site_settings['instagram'] }}" target="_blank" class="social-icon bg-white/10 hover:bg-[#e4405f]"><i class="fab fa-instagram text-sm"></i></a>
                    @endif
                    @if(!empty($site_settings['youtube']))
                        <a href="{{ $site_settings['youtube'] }}" target="_blank" class="social-icon bg-white/10 hover:bg-[#ff0000]"><i class="fab fa-youtube text-sm"></i></a>
                    @endif
                    @if(!empty($site_settings['tiktok']))
                        <a href="{{ $site_settings['tiktok'] }}" target="_blank" class="social-icon bg-white/10 hover:bg-[#000000]"><i class="fab fa-tiktok text-sm"></i></a>
                    @endif
                    @if(!empty($site_settings['email']))
                        <a href="mailto:{{ $site_settings['email'] }}" class="social-icon bg-white/10 hover:bg-accent"><i data-lucide="mail" class="w-4 h-4"></i></a>
                    @endif
                    <a href="{{ url('/feed') }}" target="_blank" class="social-icon bg-white/10 hover:bg-[#ee802f]" title="RSS Feed"><i data-lucide="rss" class="w-4 h-4"></i></a>
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-5 text-inverse-on-surface dark:text-gray-100">Kategori</h4>
                <ul class="space-y-3 text-sm list-none p-0">
                    @foreach($categories->take(6) as $cat)
                        <li><a href="{{ route('categories.show', $cat->slug) }}" class="opacity-70 hover:opacity-100 hover:translate-x-1 inline-block transition-all no-underline text-inverse-on-surface dark:text-gray-300">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Halaman --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-5 text-inverse-on-surface dark:text-gray-100">Halaman</h4>
                <ul class="space-y-3 text-sm list-none p-0">
                    @foreach($footerPages as $page)
                        <li><a href="{{ route('pages.show', $page->slug) }}" class="opacity-70 hover:opacity-100 hover:translate-x-1 inline-block transition-all no-underline text-inverse-on-surface dark:text-gray-300">{{ $page->title }}</a></li>
                    @endforeach
                    @if($footerPages->isEmpty())
                        <li class="opacity-50">Belum ada halaman.</li>
                    @endif
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-5 text-inverse-on-surface dark:text-gray-100">Kontak</h4>
                <ul class="space-y-3.5 text-sm list-none p-0">
                    <li class="flex gap-2.5 opacity-70">
                        <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 shrink-0"></i>
                        <span>{{ $site_settings['address'] ?? 'Konawe Utara, Sulawesi Tenggara' }}</span>
                    </li>
                    @if(!empty($site_settings['email']))
                        <li>
                            <a href="mailto:{{ $site_settings['email'] }}" class="flex gap-2.5 opacity-70 hover:opacity-100 transition-all no-underline text-inverse-on-surface dark:text-gray-300">
                                <i data-lucide="mail" class="w-4 h-4 mt-0.5 shrink-0"></i>
                                <span>{{ $site_settings['email'] }}</span>
                            </a>
                        </li>
                    @endif
                    @if(!empty($site_settings['phone']))
                        <li class="flex gap-2.5 opacity-70">
                            <i data-lucide="phone" class="w-4 h-4 mt-0.5 shrink-0"></i>
                            <span>{{ $site_settings['phone'] }}</span>
                        </li>
                    @endif
                    <li class="mt-4">
                        <a href="https://maps.app.goo.gl/Us9tSdkMkJWC56m38" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden border border-white/10 group">
                            <iframe src="https://maps.google.com/maps?q=Konawe+Utara&output=embed&z=11" width="100%" height="160" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <div class="flex items-center justify-center gap-1.5 py-2 text-xs text-inverse-on-surface/60 bg-white/5 group-hover:bg-white/10 group-hover:text-inverse-on-surface transition-all">
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                <span>Buka di Google Maps</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col md:flex-row items-center justify-between gap-2 text-sm opacity-60">
            <p>&copy; {{ date('Y') }} {{ $site_settings['site_name'] ?? 'Konut.Update' }}. All rights reserved.</p>
            <p>Portal Berita Terpercaya Konawe Utara</p>
            <a href="https://viteks.id" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 opacity-80 hover:opacity-100 transition-opacity no-underline">
                <img src="https://viteks.id/storage/site/J5MNxOhayYQO9ENI3oFOxy0fQd50ll84bFpyFshl.png" alt="Viteks Logo" class="h-5 w-auto brightness-0 invert opacity-90">
                <span class="text-xs text-white/70">Powered by <span style="color:#0ea5a0">Viteks</span></span>
            </a>
        </div>
    </div>
</footer>
